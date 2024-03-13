<?php

namespace AstrotechLabs\BancoBrasilPix\CreatePixChargeGateway;

enum PixKeyType: string
{
    case CPF = 'CPF';
    case CNPJ = 'CNPJ';
    case PHONE = 'PHONE';
    case EMAIL = 'EMAIL';
    case RANDOM_KEY = 'RANDOM_KEY';
}
