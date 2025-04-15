<?php
declare(strict_types=1);

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    private string $secret;
    private string $alg;

    public function __construct(){
        // TODO: handle environment vars
        $this->secret = $_ENV['JWT_SECRET'];
        $this->alg = 'HS256';
    }

    public function generateToken(array $payload, int $expiry = 3600): string
    {
        // TODO: 3600 default expiry ??
        $issuedAt = time();
        $payload['iat'] = $issuedAt;
        $payload['exp'] = $issuedAt + $expiry;

        return JWT::encode($payload, $this->secret, $this->alg);
    }

    public function validateToken(string $token): ?object {
        try {
            return JWT::decode($token, new Key($this->secret, $this->alg));
        } catch (\Exception $e) {
            return null;
        }
    }
}