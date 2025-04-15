<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\JwtService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Handlers\Strategies\RequestResponseNamedArgs;

class AuthController
{
    public function __construct(
        private readonly JwtService $jwtService,
    ){
    }

    public function getToken(Request $request, Response $response): Response
    {
        $body = json_decode($request->getBody()->getContents(), true);

        $isAdmin = strtolower($body['admin'] ?? 'false');
        $username = $body['name'] ?? $body['username'] ?? 'username';

        // validate isAdmin
        if(! in_array($isAdmin, ['true', 'false'])) {
            $response->getBody()->write(json_encode(['error' => 'admin should be true or false']));
            return $response->withHeader('Content-Type','application/json')->withStatus(400);
        }

        // validate username
        if(! preg_match("/^[A-Za-z0-9_\-\s]{3,50}$/", $username)) {
            $response->getBody()->write(json_encode([
                'error' => 'invalid username.',
                'allowed' => ['capital and small letters', 'number 0 to 9', 'hyphens (-)', 'underscores (_)', 'length minimum 3 maximum 50']
            ]));
            return $response->withHeader('Content-Type','application/json')->withStatus(400);
        }

        // structure payload
        $payload = [
            'name' => $username,
            'admin' => $isAdmin
        ];

        // generate token
        $token = $this->jwtService->generateToken($payload);

        $response->getBody()->write(json_encode(['token' => $token]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}