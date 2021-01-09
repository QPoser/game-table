<?php

declare(strict_types=1);

namespace App\Dto\RequestDto;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final class RegisterUserRequest implements RequestDTOInterface
{
    /**
     * @Assert\Length(min=6, max=32)
     * @Assert\NotBlank
     */
    private string $password;

    /**
     * @Assert\Length(min=3, max=18)
     * @Assert\NotBlank
     */
    private string $username;

    /**
     * @Assert\Email
     * @Assert\NotBlank
     */
    private string $email;

    public function __construct(Request $request)
    {
        $this->email = (string) $request->request->get('email');
        $this->username = (string) $request->request->get('username');
        $this->password = (string) $request->request->get('password');
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
