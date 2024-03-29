<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Services\User\RegisterService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class RegistrationController extends AbstractController
{
    private RegisterService $registerService;

    public function __construct(RegisterService $registerService)
    {
        $this->registerService = $registerService;
    }

    /**
     * @Route("/register", name="app.register")
     * @Security("is_anonymous()")
     */
    public function register(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $username = $form->get('username')->getData();
            $password = $form->get('plainPassword')->getData();
            $this->registerService->registerUser($email, $username, $password);

            return $this->redirectToRoute('app.login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/register/verify/{token}", name="app.register.verify")
     * @Security("is_anonymous()")
     */
    public function registerVerify(string $token): Response
    {
        $this->registerService->verifyRegister($token);

        return $this->redirectToRoute('app.login');
    }
}
