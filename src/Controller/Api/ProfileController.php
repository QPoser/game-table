<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\User;
use App\Services\Response\Responser;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;

/**
 * @Route("/api/profile", name="api")
 */
class ProfileController extends AbstractFOSRestController
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
    public function index(): array
    {
        return Responser::wrapSuccess($this->getUser());
    }
}
