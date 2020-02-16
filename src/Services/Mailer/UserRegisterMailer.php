<?php
declare(strict_types=1);

namespace App\Services\Mailer;

use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserRegisterMailer
{
    private MailerInterface $mailer;

    private string $senderEmail;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(MailerInterface $mailer, string $senderEmail, UrlGeneratorInterface $urlGenerator)
    {
        $this->mailer = $mailer;
        $this->senderEmail = $senderEmail;
        $this->urlGenerator = $urlGenerator;
    }

    public function sendUserRegisterMail(User $user): void
    {
        $email = (new Email())
            ->from($this->senderEmail)
            ->to($user->getEmail())
            ->subject('Thank you for registration!')
            ->text(
                'This is your verification link - ' . $this->urlGenerator->generate(
                    'app.register.verify',
                    ['token' => $user->getVerifyToken()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                )
            );

        $this->mailer->send($email);
    }
}