<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Administrator;
use App\Services\AuthService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class AdminController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly AuthService $authService,
    ){
    }

    public function index(Request $request, Response $response): Response {
        /** @var Administrator $admin */
        $admin = $this->authService->getAuthenticatedUser();
        $userName = $admin->getFirstName() . ' ' . $admin->getLastName();

        return $this->twig->render(
            $response,
            '/admin/dashboard.twig',
            ['username' => $userName, 'email' => $admin->getEmail()]
        );
    }

    public function viewRequest(Request $request, Response $response): Response {
        $data = $request->getParsedBody();
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }
}