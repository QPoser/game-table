<?php

declare(strict_types=1);

namespace App\Dto;

interface RegisterDTOInterface
{
    public function getEmail(): string;

    public function getUsername(): string;

    public function getPassword(): string;
}
