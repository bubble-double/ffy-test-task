<?php

namespace App\Event\Listener;

use App\Controller\Common\Api\AbstractApiController;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Validator\Exception\ValidatorException;

class ExceptionListener
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param ExceptionEvent $event
     *
     * @return void
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $controllerAndAction = $event->getRequest()->attributes->get('_controller');
        $data = explode('::', $controllerAndAction);
        $controllerClassName = $data[0] ?? null;
        if ($controllerClassName && \is_subclass_of($controllerClassName, AbstractApiController::class)) {
            $exception = $event->getException();

            $messageType = $event->getRequest()->getRequestUri();
            $data = [
                'version' => AbstractApiController::DEFAULT_VERSION, // @todo for example
                'messageType' => $messageType ?: AbstractApiController::DEFAULT_MESSAGE_TYPE, // @todo for example
                'error' => $exception->getMessage(),
            ];
            $status = $this->getStatusCode($exception);

            $event->setResponse(new JsonResponse($data, $status));
            $this->logger->error($exception);
        }
    }

    /**
     * @param \Throwable $exception
     *
     * @return int
     */
    protected function getStatusCode(\Throwable $exception): int
    {
        if (!$status = $exception->getCode()) {
            switch(\get_class($exception)) {
                default:
                    $status = Response::HTTP_NOT_FOUND;
                    break;
                case ValidatorException::class:
                    $status = Response::HTTP_BAD_REQUEST;
                    break;
                // @todo handle other exceptions without code
            }
        }
        return $status;
    }
}
