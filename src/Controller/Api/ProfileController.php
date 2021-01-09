<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Dto\ResponseDto\ResponseDTO;
use App\Entity\User;
use App\Services\Response\Responser;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/profile", name="api")
 */
final class ProfileController extends AbstractFOSRestController
{
    /**
     * @Route("", name=".profile", methods={"GET"})
     * @Rest\View(serializerGroups={"UserRoles", "Api"})
     * @SWG\Get(
     *     tags={"User profile"},
     *     @SWG\Response(
     *      response="200",
     *      description="Get current user info (by token)",
     *      @Model(type=User::class, groups={"UserRoles", "Api"})
     *     ),
     * )
     */
    public function index(): ResponseDTO
    {
        return Responser::wrapSuccess($this->getUser());
    }
}
