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

    private function format(Response $response, string $window, array $stylesheets, array $scripts = []): Response {
        /** @var Administrator $admin */
        $admin = $this->authService->getAuthenticatedUser();

        $options = [
            'username' => $admin->getUsername(),
            'email' => $admin->getEmail(),
            'window' => $window,
            'stylesheets' => $stylesheets,
            'scripts' => $scripts
        ];

        return $this->twig->render(
            $response,
            '/admin/dashboard.twig',
            $options
        );
    }

    //TODO: useless function
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
        $stylesheets = [];
        $scripts = [
            'jsDeliver-charts' => ['src' => 'https://cdn.jsdelivr.net/npm/chart.js'],
            'my-charts' => ['src' => '/resources/js/chart.js'],
        ];

        return $this->format($response, '/windows/analyticsWindow.twig', $stylesheets, $scripts);
    }

    public function productsView(Request $request, Response $response): Response {
        $stylesheets = [
            ['name' => 'productCard.css', 'url' => '/resources/css/productCard.css'],
            ['name' => 'productForm.css', 'url' => '/resources/css/productForm.css'],
        ];

        $scripts = [
            'products-script' => ['src' => '/resources/js/products.js'],
        ];

        return $this->format($response, '/windows/productsWindow.twig', $stylesheets, $scripts);
    }

    public function categoryView(Request $request, Response $response): Response {
        $stylesheets = [
            ['name' => 'categoryCard.css', 'url' => '/resources/css/categoryCard.css'],
            ['name' => 'categoryForm.css', 'url' => '/resources/css/categoryForm.css'],
        ];

        $scripts = [
            'categories-script' => ['src' => '/resources/js/categories.js'],
        ];

        return $this->format($response, '/windows/categoriesWindow.twig', $stylesheets, $scripts);
    }

    public function viewRequest(Request $request, Response $response): Response {
        $data = $request->getParsedBody();
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }
}