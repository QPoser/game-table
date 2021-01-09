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
 * @Route("/api/admin", name="api.admin")
 */
final class AdminController extends AbstractFOSRestController
{
    /**
     * @Route("/users", name=".users", methods={"GET"})
     * @Rest\View(serializerGroups={"UserRoles", "Api"})
     * @SWG\Get(
     *     tags={"Admin"},
     *     @SWG\Response(
     *      response="200",
     *      description="Get users list",
     *      @SWG\Schema(
     *          type="array",
     *          @Model(type=User::class, groups={"UserRoles", "Api"})
     *      )
     *     )
     * )
     */
    public function users(): ResponseDTO
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return Responser::wrapSuccess($users);
    }
}
