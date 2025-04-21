<?php
declare(strict_types=1);

namespace App\Services;

use App\Config;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    private string $secret;
    private string $alg;

    public function __construct(
        private readonly Config $config,
    ){
        $this->secret = $this->config->get('security.jwt_secret');
        $this->alg = $this->config->get('security.jwt_alg');
    }

    public function generateToken(array $payload, int $expiry = 3600): string
    {
        // 3600 seconds is a 1 hour.
        $issuedAt = time();
        $payload['iat'] = $issuedAt;
        $payload['exp'] = $issuedAt + $expiry;

        return JWT::encode($payload, $this->secret, $this->alg);
    }

    public function validateToken(string $token): ?object {
        try {
            return JWT::decode($token, new Key($this->secret, $this->alg));
        } catch (Exception $e) {
            return null;
        }
    }
}