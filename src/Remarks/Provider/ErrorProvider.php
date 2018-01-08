<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Remarks\Provider;

use Flight\Service\Amadeus\Remarks\Response\AmadeusErrorResponse;
use Flight\Service\Amadeus\Remarks\Response\Error;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Log\LogLevel;
use Silex\Application;
use Silex\Provider\MonologServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * ErrorProvider.php
 *
 * Register errors handlers and logging
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class ErrorProvider implements ServiceProviderInterface
{
    /**
     * @param Container $app
     */
    public function register(Container $app) : void
    {
        // general service provider

         /* @var $app Application  */
        $app->register(
            new MonologServiceProvider(),
            [
                'monolog.logfile' => __DIR__ . '/../../../var/logs/app.log',
                'monolog.exception.logger.filter'  => $app->protect(function (\Throwable $e) {
                    if ($e instanceof NotFoundHttpException) {
                        //log 404 exception at lowest priority
                        return LogLevel::DEBUG;
                    } elseif ($e instanceof HttpExceptionInterface && $e->getStatusCode() < 500) {
                        return LogLevel::ERROR;
                    }

                    return LogLevel::CRITICAL;
                })
            ]
        );

        $app->error(function (NotFoundHttpException $e, Request $request, $code) {
            return AmadeusErrorResponse::notFound($this->renderErrors([Error::resourceNotFound($e)]));
        });

        $app->error(function (\Throwable $e, Request $request, $code) {
            return AmadeusErrorResponse::serverError($this->renderErrors([Error::serverError()]));
        });
    }

    /**
     * Make sure that uncaught exceptions are creating the expected type of response (hal).
     * Unfortunately necessary in Silex to handle \Throwable
     */
    public function registerHandlers()
    {
        set_exception_handler(function (\Throwable $throwable) {
            AmadeusErrorResponse::serverError($this->renderErrors([Error::serverError()]))->send();
        });

        \Symfony\Component\Debug\ErrorHandler::register();
    }

    /**
     * Turn a list of Error instances into a HAL compatible structure
     *
     * @param array $errors
     * @return array
     */
    private function renderErrors(array $errors) : array
    {
        $errorStorage = [];

        /** @var Error $error */
        foreach ($errors as $error) {
            $errorStorage[$error->getProperty()][] = [
                'code'    => $error->getCode(),
                'message' => $error->getMessage(),
                'status'  => $error->getStatus()
            ];
        }

        return ['errors' => $errorStorage];
    }
}