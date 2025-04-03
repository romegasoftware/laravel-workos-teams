<?php

namespace RomegaSoftware\WorkOSTeams\Domain\DTOs;

use WorkOS\Resource\Order;

class ListUsersDTO
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        public readonly ?string $organizationId = null,
        public readonly ?string $email = null,
        public readonly int $limit = 10,
        public readonly ?string $before = null,
        public readonly ?string $after = null,
        public readonly ?Order $order = null,
    ) {}

    public function toArray(): array
    {
        return [
            'organizationId' => $this->organizationId,
            'email' => $this->email ?? '',
            'limit' => $this->limit,
            'before' => $this->before,
            'after' => $this->after,
            'order' => $this->order,
        ];
    }
}
