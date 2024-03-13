<?php

declare(strict_types=1);

namespace AstrotechLabs\BancoBrasilPix\CreatePixChargeGateway;

use JsonSerializable;

final class CreatePixChargeOutput implements JsonSerializable
{
    public function __construct(
        public readonly string $txId,
        public readonly string $copyPasteKey,
        public readonly array $payloadResponse
    ) {
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
