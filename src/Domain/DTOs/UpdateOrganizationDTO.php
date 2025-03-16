<?php

namespace RomegaSoftware\WorkOSTeams\Domain\DTOs;

final class UpdateOrganizationDTO
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?array $domains = null,
        public readonly ?bool $allowProfilesOutsideOrganization = null,
        public readonly ?array $domainData = null,
        public readonly ?array $metadata = null,
    ) {}

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'domains' => $this->domains,
            'allow_profiles_outside_organization' => $this->allowProfilesOutsideOrganization,
            'domain_data' => $this->domainData,
            'metadata' => $this->metadata,
        ], fn($value) => $value !== null);
    }
}
