<?php

namespace App\Controller\Common\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class AbstractApiController extends AbstractController
{
    /**
     * @var string
     */
    public const DEFAULT_VERSION = '0.0.1';

    /**
     * @var string
     */
    public const DEFAULT_MESSAGE_TYPE = 'undefined';

    /**
     * @param array $data
     *
     * @return JsonResponse
     */
    public function sendApiMessage(array $data): JsonResponse
    {
        foreach ($data as $k => $item) {
            if (\is_object($item)) {
                // REMARK: fix circular references for transform object to json
                $encoders = [new JsonEncoder()];
                $normalizers = [new ObjectNormalizer()];
                $serializer = new Serializer($normalizers, $encoders);

                $jsonObject = $serializer->serialize(
                    $item,
                    'json',
                    [
                        'circular_reference_handler' => function($object) {
                            return \method_exists($object, 'getId') ? $object->getId() : null;
                        },
                    ]
                );

                $data[$k] = \json_decode($jsonObject, true);
            }
        }

        if ($request = $this->container->get('request_stack')->getCurrentRequest()) {
            $messageType = $request->getRequestUri();
        }

        $message = [
            'version' => self::DEFAULT_VERSION, // @todo for example
            'messageType' => $messageType ?? self::DEFAULT_MESSAGE_TYPE, // @todo for example
            'payload' => $data,
        ];

        return $this->json($message);
    }
}
