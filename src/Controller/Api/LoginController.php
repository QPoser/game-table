<?php
declare(strict_types=1);

namespace App\Controller\Api;

use FOS\RestBundle\Controller\Annotations\RequestParam;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;

/**
 * @Route("/api")
 */
class LoginController extends AbstractController
{
    /**
     * @Route("/login_check", name="login_check", methods={"POST"})
     * @SWG\Post(
     *     tags={"Auth"},
     *     @SWG\Response(
     *      response="200",
     *      description="Login check action"
     *     )
     * )
     * @RequestParam(name="username", requirements="\w+", nullable=false, strict=true, description="User email")
     * @RequestParam(name="password", requirements="\w+", nullable=false, strict=true, description="User password")
     */
    public function checkAction()
    {
    }
}
