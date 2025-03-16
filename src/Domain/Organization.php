<?php

namespace RomegaSoftware\WorkOSTeams\Domain;

final class Organization
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?array $domains = null,
        public readonly ?bool $allowProfilesOutsideOrganization = null,
        public readonly ?array $domainData = null,
        public readonly ?array $metadata = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            domains: $data['domains'] ?? null,
            allowProfilesOutsideOrganization: $data['allow_profiles_outside_organization'] ?? null,
            domainData: $data['domain_data'] ?? null,
            metadata: $data['metadata'] ?? null,
        );
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'domains' => $this->domains,
            'allow_profiles_outside_organization' => $this->allowProfilesOutsideOrganization,
            'domain_data' => $this->domainData,
            'metadata' => $this->metadata,
        ];
    }
}
