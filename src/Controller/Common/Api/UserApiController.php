<?php

namespace App\Controller\Common\Api;

use App\Service\User\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/user")
 */
class UserApiController extends AbstractApiController
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * UserApiContro
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @Route("/", name="api_user_get", methods={"GET"})
     *
     * @return JsonResponse
     *
     * @throws \RuntimeException
     */
    public function getAction(): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->userService->getPublicData($this->getUser());
        return $this->sendApiMessage(['user' => $user]);
    }
}
