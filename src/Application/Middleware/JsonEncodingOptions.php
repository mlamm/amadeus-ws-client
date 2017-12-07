<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Application\Middleware;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * JsonEncodingOptions.php
 *
 * Set custom encoding options on the response from config
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class JsonEncodingOptions
{
    /**
     * @var \stdClass
     */
    private $config;

    /**
     * @param \stdClass $config
     */
    public function __construct(\stdClass $config)
    {
        $this->config = $config;
    }

    public function __invoke(Request $request, Response $response)
    {
        if ($response instanceof JsonResponse && isset($this->config->response->json_encoding_options)) {
            $value = 0;
            set_error_handler(function () { return false; });

            try {
                foreach ($this->config->response->json_encoding_options as $option) {
                    $optionValue = constant($option);

                    if ($optionValue === null) {
                        throw new InvalidArgumentException('undefined constant ' . $option);
                    }

                    $value |= $optionValue;
                }
            } finally {
                restore_error_handler();
            }

            $response->setEncodingOptions($value);
        }
    }
}
