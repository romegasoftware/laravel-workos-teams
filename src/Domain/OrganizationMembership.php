<?php

namespace RomegaSoftware\WorkOSTeams\Domain;

final class OrganizationMembership
{
    public function __construct(
        public readonly string $id,
        public readonly string $organizationId,
        public readonly string $userId,
        public readonly string $role,
        public readonly ?array $metadata = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            organizationId: $data['organization_id'],
            userId: $data['user_id'],
            role: $data['role'],
            metadata: $data['metadata'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'organization_id' => $this->organizationId,
            'user_id' => $this->userId,
            'role' => $this->role,
            'metadata' => $this->metadata,
        ];
    }
}
