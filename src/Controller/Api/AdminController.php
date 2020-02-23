<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\User;
use App\Services\Response\Responser;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/admin", name="api.admin.")
 */
class AdminController extends AbstractFOSRestController
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @Route("/users", name="users")
     * @Rest\View(serializerGroups={"UserRoles", "Api"})
     */
    public function users(): array
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return Responser::wrapSuccess($users);
    }
}
