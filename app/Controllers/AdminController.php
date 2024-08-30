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

    private function format(Response $response, string $window, array $scripts = []): Response {
        /** @var Administrator $admin */
        $admin = $this->authService->getAuthenticatedUser();

        $options = [
            'username' => $admin->getUsername(),
            'email' => $admin->getEmail(),
            'window' => $window,
            'scripts' => $scripts
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
        $scripts = [
            'jsDeliver-charts' => ['src' => 'https://cdn.jsdelivr.net/npm/chart.js'],
            'my-charts' => ['src' => '/resources/js/chart.js'],
        ];

        return $this->format($response, '/windows/analyticsWindow.twig', $scripts);
    }

    public function productsView(Request $request, Response $response): Response {
        $scripts = [
            'products-script' => ['src' => '/resources/js/products.js'],
        ];

        return $this->format($response, '/windows/productsWindow.twig', $scripts);
    }

    public function categoryView(Request $request, Response $response): Response {
        //TODO: category page scripts here

        return $this->format($response, '/windows/categoriesWindow.twig');
    }

    public function viewRequest(Request $request, Response $response): Response {
        $data = $request->getParsedBody();
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }
}