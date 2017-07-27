<?php
namespace AmadeusService\Application\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class HalResponse
 * @package AmadeusService\Search\Response
 */
class HalResponse extends JsonResponse
{
    /**
     * HalResponse constructor.
     * @param null $data
     * @param int $status
     * @param array $headers
     * @param bool $json
     */
    public function __construct($data = null, $status = 200, array $headers = array(), $json = false)
    {
        parent::__construct($data, $status, $headers, $json);
        $this->headers->set('content-type', 'application/hal+json');
    }
}