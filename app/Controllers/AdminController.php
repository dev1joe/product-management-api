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

    private function format(Response $response, string $window): Response {
        /** @var Administrator $admin */
        $admin = $this->authService->getAuthenticatedUser();

        $options = [
            'username' => $admin->getUsername(),
            'email' => $admin->getEmail(),
            'window' => $window,
        ];

        return $this->twig->render(
            $response,
            '/admin/dashboard.twig',
            $options
        );
    }

    public function index(Request $request, Response $response): Response {
        /** @var Administrator $admin */
        $admin = $this->authService->getAuthenticatedUser();

        return $this->twig->render(
            $response,
            '/admin/dashboard.twig',
            ['username' => $admin->getUsername(), 'email' => $admin->getEmail()]
        );
    }

    public function dashboardView(Request $request, Response $response): Response {
        return $this->format($response, '/windows/analyticsWindow.twig');
    }

    public function productsView(Request $request, Response $response): Response {
        return $this->format($response, '/windows/productsWindow.twig');
    }

    public function viewRequest(Request $request, Response $response): Response {
        $data = $request->getParsedBody();
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }
}