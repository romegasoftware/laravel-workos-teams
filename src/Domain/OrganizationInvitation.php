<?php

namespace RomegaSoftware\WorkOSTeams\Domain;

class OrganizationInvitation
{
    public function __construct(
        public readonly string $id,
        public readonly string $email,
        public readonly string $state,
        public readonly ?string $acceptedAt = null,
        public readonly ?string $revokedAt = null,
        public readonly ?string $expiresAt = null,
        public readonly ?string $token = null,
        public readonly ?string $acceptInvitationUrl = null,
        public readonly ?string $organizationId = null,
        public readonly ?string $inviterUserId = null,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            email: $data['email'],
            state: $data['state'],
            acceptedAt: $data['accepted_at'] ?? null,
            revokedAt: $data['revoked_at'] ?? null,
            expiresAt: $data['expires_at'] ?? null,
            token: $data['token'] ?? null,
            acceptInvitationUrl: $data['accept_invitation_url'] ?? null,
            organizationId: $data['organization_id'] ?? null,
            inviterUserId: $data['inviter_user_id'] ?? null,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
        );
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'state' => $this->state,
            'accepted_at' => $this->acceptedAt,
            'revoked_at' => $this->revokedAt,
            'expires_at' => $this->expiresAt,
            'token' => $this->token,
            'accept_invitation_url' => $this->acceptInvitationUrl,
            'organization_id' => $this->organizationId,
            'inviter_user_id' => $this->inviterUserId,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
