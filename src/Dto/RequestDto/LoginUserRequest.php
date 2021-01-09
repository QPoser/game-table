<?php

declare(strict_types=1);

namespace App\Dto\RequestDto;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final class LoginUserRequest implements RequestDTOInterface
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

    public function __construct(Request $request)
    {
        $this->username = (string) $request->request->get('username');
        $this->password = (string) $request->request->get('password');
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
