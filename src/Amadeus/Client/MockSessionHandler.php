<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Amadeus\Client;

use Amadeus\Client\Params\SessionHandlerParams;
use Amadeus\Client\Session\Handler\HandlerInterface;
use Amadeus\Client\Session\Handler\SendResult;
use Amadeus\Client\Session\Handler\UnsupportedOperationException;
use Amadeus\Client\Session\Handler\WsdlAnalyser;
use Amadeus\Client\Struct\BaseWsMessage;

/**
 * MockSessionHandler.php
 *
 * Return a response from a fixture file for requests to Amadeus
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class MockSessionHandler implements HandlerInterface
{
    private const MASTERPRICER_RESPONSE_FIXTURE = 'tests/_data/fixtures/03-Fare_MasterPricerTravelBoardSearch_FBA-rt.xml';

    private const CREATE_SESSION_RESPONSE_FIXTURE = 'tests/_data/fixtures/Security_Authenticate-Response.xml';

    private const IGNORE_SESSION_RESPONSE_FIXTURE = 'tests/_data/fixtures/PNR_Ignore-Response.xml';

    private const TERMINATE_SESSION_RESPONSE_FIXTURE = 'tests/_data/fixtures/Security_SignOut-Response.xml';

    /**
     * @var SessionHandlerParams
     */
    private $params;

    /**
     * @var array
     */
    private $messagesAndVersions;


    public function __construct(SessionHandlerParams $params)
    {
        $this->params = $params;
    }

    /**
     * @param               $messageName
     * @param BaseWsMessage $messageBody
     * @param               $messageOptions
     *
     * @return SendResult
     * @throws \Exception
     */
    public function sendMessage($messageName, BaseWsMessage $messageBody, $messageOptions)
    {
        switch ($messageName) {
            case 'Fare_MasterPricerTravelBoardSearch':
                return $this->loadMasterPricerTravelBoardSearchResponse();
                break;
            case 'Security_Authenticate':
                return $this->loadCreateSessionResponse();
                break;
            case 'Security_SignOut':
                return $this->loadIgnoreSessionResponse();
                break;
            case 'PNR_Ignore':
                return $this->loadTerminateSessionResponse();
                break;
        }

        throw new \Exception("no mock response configured for message `{$messageName}`");
    }

    private function loadMasterPricerTravelBoardSearchResponse(): SendResult
    {
        $sendResult = new SendResult();
        $sendResult->responseXml = file_get_contents(self::MASTERPRICER_RESPONSE_FIXTURE);
        $sendResult->responseObject = json_decode(json_encode(new \SimpleXMLElement($sendResult->responseXml)));

        return $sendResult;
    }

    private function loadCreateSessionResponse()
    {
        $sendResult = new SendResult();
        $sendResult->responseXml = file_get_contents(self::CREATE_SESSION_RESPONSE_FIXTURE);
        $sendResult->responseObject = json_decode(json_encode(new \SimpleXMLElement($sendResult->responseXml)));

        return $sendResult;
    }

    private function loadIgnoreSessionResponse()
    {
        $sendResult = new SendResult();
        $sendResult->responseXml = file_get_contents(self::IGNORE_SESSION_RESPONSE_FIXTURE);
        $sendResult->responseObject = json_decode(json_encode(new \SimpleXMLElement($sendResult->responseXml)));

        return $sendResult;
    }

    private function loadTerminateSessionResponse()
    {
        $sendResult = new SendResult();
        $sendResult->responseXml = file_get_contents(self::TERMINATE_SESSION_RESPONSE_FIXTURE);
        $sendResult->responseObject = json_decode(json_encode(new \SimpleXMLElement($sendResult->responseXml)));

        return $sendResult;
    }

    public function getOriginatorOffice()
    {
        return 'mock';
    }

    public function getMessagesAndVersions()
    {
        if (empty($this->messagesAndVersions)) {
            $this->messagesAndVersions = WsdlAnalyser::loadMessagesAndVersions($this->params->wsdl);
        }

        return $this->messagesAndVersions;
    }

    public function setStateful($stateful)
    {
    }

    public function getSessionData()
    {
        return null;
    }

    public function setSessionData(array $sessionData)
    {
        return true;
    }

    public function isStateful()
    {
        return false;
    }

    /**
     * @param string $msgName
     *
     * @return null|string|void
     * @throws \Exception
     */
    public function getLastRequest($msgName)
    {
        throw new \Exception('not implemented for mock session handler');
    }

    /**
     * @param string $msgName
     *
     * @return null|string
     * @throws \Exception
     */
    public function getLastResponse($msgName)
    {
        switch ($msgName) {
            case 'Security_Authenticate':
                return file_get_contents(self::CREATE_SESSION_RESPONSE_FIXTURE);
                break;
            default:
                throw new \Exception('not implemented for mock session handler for msg ' . $msgName);

        }
    }

    /**
     * @param string $msgName
     *
     * @return null|string|void
     * @throws \Exception
     */
    public function getLastRequestHeaders($msgName)
    {
        throw new \Exception('not implemented for mock session handler');
    }

    /**
     * @param string $msgName
     *
     * @return null|string|void
     * @throws \Exception
     */
    public function getLastResponseHeaders($msgName)
    {
        throw new \Exception('not implemented for mock session handler');
    }

    /**
     * Is the TransactionFlowLink header enabled?
     *
     * @return bool
     */
    public function isTransactionFlowLinkEnabled()
    {
        throw new \Exception('not implemented for mock session handler');
    }

    /**
     * Enable or disable TransactionFlowLink header
     *
     * @throws UnsupportedOperationException when used on unsupported WSAP versions
     * @param bool $enabled
     */
    public function setTransactionFlowLink($enabled)
    {
        throw new \Exception('not implemented for mock session handler');
    }

    /**
     * Get the TransactionFlowLink Consumer ID
     *
     * @return string|null
     */
    public function getConsumerId()
    {
        throw new \Exception('not implemented for mock session handler');
    }

    /**
     * Set the TransactionFlowLink Consumer ID
     *
     * @throws UnsupportedOperationException when used on unsupported WSAP versions
     * @param string $id
     * @return void
     */
    public function setConsumerId($id)
    {
        throw new \Exception('not implemented for mock session handler');
    }
}
