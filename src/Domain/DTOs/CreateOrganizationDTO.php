<?php

namespace RomegaSoftware\WorkOSTeams\Domain\DTOs;

class CreateOrganizationDTO
{
    public function __construct(
        public readonly string $name,
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
        return [
            'name' => $this->name,
            'domains' => $this->domains,
            'allow_profiles_outside_organization' => $this->allowProfilesOutsideOrganization,
            'domain_data' => $this->domainData,
            'metadata' => $this->metadata,
        ];
    }
}
