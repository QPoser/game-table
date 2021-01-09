<?php

declare(strict_types=1);

namespace App\Dto\RequestDto\GameChat;

use App\Dto\RequestDto\RequestDTOInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final class GameMessageRequest implements RequestDTOInterface
{
    /**
     * @Assert\Length(min=1)
     * @Assert\NotBlank
     */
    private string $content;

    /**
     * @Assert\Choice(choices={"team", "game"})
     * @Assert\NotBlank
     */
    private string $type;

    public function __construct(Request $request)
    {
        $this->content = (string) $request->request->get('content');
        $this->type = (string) $request->request->get('type');
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
