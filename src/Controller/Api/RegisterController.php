<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Services\Response\Responser;
use App\Services\User\RegisterService;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Rest\Route("/api")
 */
class RegisterController extends AbstractController
{
    private RegisterService $registerService;

    public function __construct(RegisterService $registerService)
    {
        $this->registerService = $registerService;
    }

    /**
     * @Rest\Route("/register", name="api.register", methods={"POST"})
     * @RequestParam(name="email", requirements=@Assert\Email(), nullable=false, strict=true, description="Email")
     * @RequestParam(name="username", requirements="\w+", nullable=false, strict=true, description="Username")
     * @RequestParam(name="password", requirements="\w+", nullable=false, strict=true, description="Password")
     */
    public function actionApiRegister(ParamFetcher $paramFetcher): array
    {
        $email = $paramFetcher->get('email');
        $password = $paramFetcher->get('password');
        $username = $paramFetcher->get('username');

        $result = $this->registerService->registerUser($email, $username, $password);

        return Responser::wrapSuccess((bool)$result);
    }
}
