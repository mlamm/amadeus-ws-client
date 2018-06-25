<?php

namespace Flight\Service\Amadeus\Price\Model;

use Flight\Service\Amadeus\Price\Request\Entity\Authenticate;
use Amadeus\Client\RequestOptions\TicketDeleteTstOptions;
use \Flight\Service\Amadeus\Price\Exception\AmadeusRequestException;
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
     * The client must be created from request data. This makes it impossible to inject.
     * Therefore inject a factory method which builds the client.
     *
     * @var \Closure
     */
    protected $clientBuilder;

    /**
     * @param LoggerInterface            $logger
     * @param AmadeusRequestTransformer  $requestTransformer
     * @param \Closure                   $clientBuilder
     */
    public function __construct(
        LoggerInterface $logger,
        AmadeusRequestTransformer $requestTransformer,
        \Closure $clientBuilder
    ) {
        $this->logger              = $logger;
        $this->requestTransformer  = $requestTransformer;
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

        $clientResult        = $client->ticketDeleteTST($options);

        return self::CHECK_RESULT_OK == $this->checkResult($clientResult);
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
