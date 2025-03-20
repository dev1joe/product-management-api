<?php
declare(strict_types=1);

namespace App\Services;

use Psr\Http\Message\ResponseInterface as Response;

class ResponseService
{
    public function json(Response $response, string $status, string $message, int $code = 200): Response {
        $response->getBody()->write(json_encode([
            'status' => $status,
            'message' => $message
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($code);
    }
}