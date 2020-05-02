<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Services\Response\Responser;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class ProfileController extends AbstractFOSRestController
{
    /**
     * @Route("/profile", name="api.profile")
     * @Rest\View(serializerGroups={"UserRoles", "Api"})
     */
    public function index(): array
    {
        return Responser::wrapSuccess($this->getUser());
    }
}
