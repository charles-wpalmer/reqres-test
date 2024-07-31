<?php

declare(strict_types=1);

namespace Cwp\Users\DTO;

use JsonSerializable;

class User implements JsonSerializable
{
    public function __construct(
        private readonly ?int $id,
        private readonly string $name,
        private readonly ?string $job
    )
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getJob(): string
    {
        return $this->job;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'job' => $this->job,
        ];
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'job' => $this->job,
        ];
    }
}
