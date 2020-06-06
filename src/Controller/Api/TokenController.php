<?php
declare(strict_types=1);

namespace App\Controller\Api;

use Gesdinet\JWTRefreshTokenBundle\Service\RefreshToken;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;

/**
 * @Route("/api/token", name="api.token.")
 */
class TokenController extends AbstractController
{
    /**
     * @Route("/refresh", name="refresh", methods={"POST"})
     * @SWG\Post(
     *     tags={"Auth"},
     *     @SWG\Response(
     *      response="200",
     *      description="Refresh token action"
     *     )
     * )
     */
    public function refresh(RefreshToken $refreshToken, Request $request): Response
    {
        return $refreshToken->refresh($request);
    }
}
