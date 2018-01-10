<?php
declare(strict_types=1);

namespace Flight\Service\Amadeus\Application\Provider;

use Flight\Service\Amadeus\Application\Response\ErrorResponse;
use Flight\Service\Amadeus\Search\Exception\SystemRequirementException;
use Flight\Service\Amadeus\Search\Response\AmadeusErrorResponse;
use Flight\Service\Amadeus\Search\Response\Error;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Application;
use Silex\Provider\MonologServiceProvider;
use Symfony\Component\HttpFoundation\Request;
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
     *
     * @throws \Exception
     */
    public function register(Container $app) : void
    {
        // general service provider

         /* @var $app Application  */
        $app->register(
            new MonologServiceProvider(),
            [
                'monolog.logfile' => 'php://stderr',
                'monolog.level' => Logger::NOTICE,
                'monolog.formatter' => function () {
                    return new JsonFormatter();
                }
            ]
        );

        $handler = new StreamHandler(
            __DIR__ . '/../../../var/logs/app.log',
            Logger::NOTICE, $app['monolog.bubble'],
            $app['monolog.permission']
        );
        $handler->setFormatter($app['monolog.formatter']);
        $app['monolog']->pushHandler($handler);

        $app->error(function (NotFoundHttpException $e, Request $request, $code) {
            return AmadeusErrorResponse::notFound($this->renderErrors([Error::resourceNotFound($e)]));
        });

        $app->error(function (SystemRequirementException $e) {
            $error = new Error(
                '_',
                $e->getInternalErrorCode(),
                ErrorResponse::HTTP_INTERNAL_SERVER_ERROR,
                $e->getInternalErrorMessage()
            );
            return new ErrorResponse($this->renderErrors([$error]));
        });

        $app->error(function (\Throwable $e, Request $request, $code) {
            return AmadeusErrorResponse::serverError($this->renderErrors([Error::serverError()]));
        });

        $this->registerHandlers($app);
    }

    /**
     * Make sure that uncaught exceptions are creating the expected type of response (hal).
     * Unfortunately necessary in Silex to handle \Throwable
     *
     * @param Container $app
     */
    public function registerHandlers(Container $app)
    {
        set_exception_handler(function (\Throwable $throwable) use ($app) {
            AmadeusErrorResponse::serverError($this->renderErrors([Error::serverError()]))->send();
            $app['logger']->error($throwable);
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
