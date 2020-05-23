<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\FriendRequest;
use App\Services\Friends\FriendService;
use App\Services\Response\Responser;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class FriendsController extends AbstractController
{
    private FriendService $friendService;

    public function __construct(FriendService $friendService)
    {
        $this->friendService = $friendService;
    }


    /**
     * @Route("/api/friends/requests", name="api_friends_requests", methods={"GET"})
     * @Rest\View(serializerGroups={"Api"})
     * @Rest\QueryParam(name="directions",  requirements="from|to", map=true, nullable=false, strict=true)
     */
    public function index(ParamFetcher $paramFetcher)
    {
        $directions = $paramFetcher->get('directions');
        $requests = $this->getDoctrine()->getRepository(FriendRequest::class)->getUserRequests($this->getUser(), $directions);

        return new JsonResponse(Responser::wrapSuccess($requests), Response::HTTP_OK);
    }
    /**
     * @Route("/api/friends", name="api_friends", methods={"GET"})
     * @Rest\View(serializerGroups={"Api"})
     */
    public function friends(): JsonResponse
    {
        return new JsonResponse(Responser::wrapSuccess($this->friendService->getUserFriends($this->getUser())), Response::HTTP_OK);
    }
}
