<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Dto\RequestDto\RegisterUserRequest;
use App\Dto\ResponseDto\ResponseDTO;
use App\Services\Response\Responser;
use App\Services\User\RegisterService;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="api")
 */
final class RegisterController extends AbstractController
{
    private RegisterService $registerService;

    public function __construct(RegisterService $registerService)
    {
        $this->registerService = $registerService;
    }

    /**
     * @Route("/register", name=".register", methods={"POST"})
     * @Rest\View(serializerGroups={"Api"})
     * @SWG\Post(
     *     tags={"Auth"},
     *     @SWG\Response(
     *      response="200",
     *      description="Register action"
     *     ),
     *     @SWG\Parameter(name="body", in="body", @Model(type=RegisterUserRequest::class)))
     * )
     */
    public function actionApiRegister(RegisterUserRequest $registerUserRequest): ResponseDTO
    {
        $result = $this->registerService->registerUser($registerUserRequest->getEmail(), $registerUserRequest->getUsername(), $registerUserRequest->getPassword());

        return Responser::wrapSuccess((bool) $result);
    }
}
