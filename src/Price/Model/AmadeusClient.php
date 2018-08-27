<?php

namespace Flight\Service\Amadeus\Price\Model;

use Amadeus\Client\RequestOptions\TicketDisplayTstOptions;
use Amadeus\Client\RequestOptions\TicketCreateTstFromPricingOptions;
use Flight\Service\Amadeus\Price\Request\Entity\Authenticate;
use Amadeus\Client\RequestOptions\TicketDeleteTstOptions;
use \Flight\Service\Amadeus\Price\Exception\AmadeusRequestException;
use Flight\Service\Amadeus\Price\Response\PriceGetResponse;
use Psr\Log\LoggerInterface;
use Amadeus\Client;

/**
 * AmadeusClient
 *
 * amadeus client for Price request type
 *
 * @author      Michael Mueller <michael.mueller@invia.de>
 * @copyright   Copyright (c) 2018 Invia Flights Germany GmbH
 */
class AmadeusClient
{
    /**
     * result ok
     */
    public const CHECK_RESULT_OK = 1;

    /**
     * result already authenticate
     */
    public const CHECK_RESULT_ALREADY_AUTH = 2;

    /**
     * amadeus intern code for already authenticate
     */
    public const AMADEUS_RESULT_CODE_ALREADY_AUTH = '16001';

    /**
     * @var \stdClass
     */
    protected $config;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var AmadeusRequestTransformer
     */
    protected $requestTransformer;


    /**
     * @var PriceResponseTransformer
     */
    protected $responseTransformer;

    /**
     * The client must be created from request data. This makes it impossible to inject.
     * Therefore inject a factory method which builds the client.
     *
     * @var \Closure
     */
    protected $clientBuilder;

    /**
     * @param LoggerInterface           $logger
     * @param AmadeusRequestTransformer $requestTransformer
     * @param PriceResponseTransformer  $responseTransformer
     * @param \Closure                  $clientBuilder
     */
    public function __construct(
        LoggerInterface $logger,
        AmadeusRequestTransformer $requestTransformer,
        PriceResponseTransformer $responseTransformer,
        \Closure $clientBuilder
    ) {
        $this->logger              = $logger;
        $this->requestTransformer  = $requestTransformer;
        $this->responseTransformer = $responseTransformer;
        $this->clientBuilder       = $clientBuilder;
    }

    /**
     * send delete Price request to amadeus
     *
     * @param Authenticate $authenticate
     * @param Session      $session
     *
     * @return bool
     * @throws \Exception
     * @throws AmadeusRequestException
     */
    public function deletePrice(Authenticate $authenticate, Session $session) : bool
    {
        /** @var Client $client */
        $client  = ($this->clientBuilder)(
            $this->requestTransformer
                ->buildClientParams($authenticate, $this->logger)
        );
        $options = new TicketDeleteTstOptions(
            [
                'deleteMode' => TicketDeleteTstOptions::DELETE_MODE_ALL,
            ]
        );
        $client->setSessionData($session->toArray());

        $clientResult = $client->ticketDeleteTST($options);

        return self::CHECK_RESULT_OK == $this->checkResult($clientResult);
    }

    /**
     * Create pricing quote in CRS and safe it into TST.
     *
     * @param Authenticate $authenticate
     * @param Session      $session
     *
     * @param Authenticate $authenticate
     * @param Session      $session
     * @param              $tariff
     *
     * @return bool
     * @throws AmadeusRequestException
     * @throws Client\Exception
     * @throws Client\InvalidMessageException
     * @throws Client\RequestCreator\MessageVersionUnsupportedException
     */
    public function createAndSafePrice(Authenticate $authenticate, Session $session, $tariff) : bool
    {
        /** @var Client $client */
        $client = ($this->clientBuilder)(
            $this->requestTransformer
                ->buildClientParams($authenticate, $this->logger)
        );

        $tarifOptionsBuilder = new TarifOptionsBuilder($tariff);
        $tarifOptions        = $tarifOptionsBuilder->getTarifOptions();
        $clientResult        = null;

        foreach ($tarifOptions as $tarifOption) {
            try {
                // create fare pricing in record locator
                $client->setSessionData($session->toArray());
                $clientResult = $client->farePricePnrWithBookingClass($tarifOption);
                $this->checkResult($clientResult);

                if (self::CHECK_RESULT_OK == $this->checkResult($clientResult)) {
                    break;
                }
            } catch (AmadeusRequestException $requestException) {
                $this->logger->error($requestException);
            }
        }

        if ($clientResult !== null && self::CHECK_RESULT_OK != $this->checkResult($clientResult)) {
            return false;
        }

        // create tst entry from prior pricing
        $options      = new TicketCreateTstFromPricingOptions(
            [
                'pricings' => [
                    new Client\RequestOptions\Ticket\Pricing(
                        [
                            'tstNumber' => 1,
                        ]
                    ),
                ],
            ]
        );
        $clientResult = $client->ticketCreateTSTFromPricing($options);
        $this->checkResult($clientResult);

        return self::CHECK_RESULT_OK == $this->checkResult($clientResult);
    }

    /**
     * send delete Price request to amadeus
     *
     * @param Authenticate $authenticate
     * @param Session      $session
     *
     * @return PriceGetResponse
     * @throws \Exception
     * @throws AmadeusRequestException
     */
    public function getPrice(Authenticate $authenticate, Session $session)
    {
        /** @var Client $client */
        $client  = ($this->clientBuilder)(
            $this->requestTransformer
                ->buildClientParams($authenticate, $this->logger)
        );
        $options = new Client\RequestOptions\TicketDisplayTstOptions(
            [
                'displayMode' => TicketDisplayTstOptions::MODE_ALL,
            ]
        );
        $client->setSessionData($session->toArray());

//        $client->getLastRequest()

        $clientResult = $client->ticketDisplayTST($options);
        if (self::CHECK_RESULT_OK == $this->checkResult($clientResult)) {
            return $this->responseTransformer->mapResult($clientResult->response);
        }
    }

    /**
     * check result from AMA
     *
     * @param Client\Result $result
     *
     * @return string check result (s. self::CHECK_RESULT_*)
     * @throws AmadeusRequestException
     */
    protected function checkResult(Client\Result $result) : string
    {
        // result ok nothing to do
        if (Client\Result::STATUS_OK === $result->status) {
            return self::CHECK_RESULT_OK;
        }

        // already authenticate
        if (self::AMADEUS_RESULT_CODE_ALREADY_AUTH === $result->messages[0]->code) {
            return self::CHECK_RESULT_ALREADY_AUTH;
        }

        throw new AmadeusRequestException($result->messages);
    }
}
