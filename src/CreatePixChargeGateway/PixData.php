<?php

namespace AstrotechLabs\BancoBrasilPix\CreatePixChargeGateway;

final class PixData
{
    public function __construct(
        public readonly string $senderName,
        public readonly string $senderCpf,
        public readonly float $amount,
        public readonly string $destinationKey,
        public readonly PixKeyType $pixKeyType,
        public readonly string $certFile,
        public readonly string $description = '',
        public readonly int $expiration = 36000,
    ) {
    }
}
