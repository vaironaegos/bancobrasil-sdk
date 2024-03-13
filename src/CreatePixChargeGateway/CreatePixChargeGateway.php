<?php

declare(strict_types=1);

namespace AstrotechLabs\BancoBrasilPix\CreatePixChargeGateway;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;

final class CreatePixChargeGateway
{
    private GuzzleClient $httpClient;

    public function __construct(
        private readonly string $accessToken,
        private readonly string $devAppId,
        private readonly bool $isSandBox = false,
    ) {
        $baseUrl = $this->isSandBox
            ? 'https://api.hm.bb.com.br'
            : 'https://api-pix.bb.com.br';

        $this->httpClient = new GuzzleClient([
            'base_uri' => $baseUrl,
            'timeout' => 10
        ]);
    }

    public function createCharge(PixData $pixData): CreatePixChargeOutput
    {
        $headers = [
            "Content-Type" => "application/json",
            "Authorization" => "Bearer {$this->accessToken}",
            "X-Application-Key" => $this->devAppId
        ];

        $pixKey = $pixData->destinationKey;

        if (in_array($pixData->pixKeyType, [PixKeyType::PHONE, PixKeyType::CNPJ, PixKeyType::CPF])) {
            $pixKey = preg_replace("/[^0-9]/", "", $pixData->destinationKey);
        }

        $body = [
            'calendario' => [
                'expiracao' => (string)$pixData->expiration
            ],
            'devedor' => [
                'cpf' => preg_replace("/[^0-9]/", "", $pixData->senderCpf),
                'nome' => strtoupper(trim($pixData->senderName))
            ],
            'valor' => [
                'original' => (string)$pixData->amount
            ],
            'chave' => $pixKey,
            'solcnpjitacaoPagador' => trim($pixData->description),
        ];

        $txId = md5(substr(md5((string)mt_rand()), 0, 7));

        try {
            $response = $this->httpClient->put("/pix/v2/cob/{$txId}", [
                'cert' => $pixData->certFile,
                'ssl_key' => $pixData->certFile,
                'headers' => $headers,
                'json' => $body
            ]);
        } catch (ClientException $e) {
            $responsePayload = json_decode($e->getResponse()->getBody()->getContents(), true);
            throw new CreatePixChargeException(
                1001,
                $responsePayload['detail'],
                $responsePayload['type'],
                $body,
                $responsePayload
            );
        } catch (ConnectException $e) {
            throw new CreatePixChargeException(
                1002,
                $e->getMessage(),
                get_class($e),
                $body,
                []
            );
        }

        $responsePayload = json_decode($response->getBody()->getContents(), true);

        return new CreatePixChargeOutput($txId, $responsePayload['pixCopiaECola'], $responsePayload);
    }
}
