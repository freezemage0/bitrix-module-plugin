<?php


namespace Freezemage\BitrixPlugin;

use JsonSerializable;


final class ModuleMeta implements JsonSerializable
{
    public function __construct(
            public readonly string $id,
            public readonly string $name,
            public readonly string $description,
            public readonly string $partnerName,
            public readonly string $partnerUri
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
                'id' => $this->id,
                'name' => $this->name,
                'description' => $this->description,
                'partnerName' => $this->partnerName,
                'partnerUri' => $this->partnerUri
        ];
    }
}
