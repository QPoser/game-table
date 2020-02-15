<?php

namespace App\Controller\Api;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class ProfileController extends AbstractFOSRestController
{
    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @Route("/profile", name="api.profile")
     * @Rest\View(serializerGroups={"Default", "UserRoles", "Api"})
     */
    public function index()
    {
        return $this->getUser();
    }
}
