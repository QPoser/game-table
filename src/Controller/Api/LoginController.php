<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Dto\RequestDto\LoginUserRequest;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
final class LoginController extends AbstractController
{
    /**
     * @Route("/login_check", name="login_check", methods={"POST"})
     * @SWG\Post(
     *     tags={"Auth"},
     *     @SWG\Response(
     *      response="200",
     *      description="Login check action"
     *     ),
     *     @SWG\Parameter(name="body", in="body", @Model(type=LoginUserRequest::class)))
     * )
     */
    public function checkAction(LoginUserRequest $loginUserRequest): void
    {
    }
}
