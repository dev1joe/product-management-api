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

    // any request that is handled by the `formatResponse` function will use these stylesheets
    /** any request handled by {@see formatResponse()} will have these stylesheets*/
    private array $globalStylesheets = [
        ['name' => 'pagination.css', 'url' => '/resources/css/pagination.css'],
        ['name' => 'windows.css', 'url' => '/resources/css/windows.css'],
    ];

    private function formatResponse(
        Response $response,
        string $window,
        string $popupForm,
        array $stylesheets,
        array $scripts = []
    ): Response {
        /** @var Administrator $admin */
        $admin = $this->authService->getAuthenticatedUser();

        $stylesheets = array_merge($this->globalStylesheets, $stylesheets);

        $options = [
            'username' => $admin->getUsername(),
            'email' => $admin->getEmail(),
            'window' => $window,
            'popupForm' => $popupForm,
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

        return $this->formatResponse(
            response: $response,
            window: '/windows/analyticsWindow.twig',
            popupForm: '',
            stylesheets: $stylesheets,
            scripts: $scripts
        );
    }

    public function productsView(Request $request, Response $response): Response {
        $stylesheets = [
            ['name' => 'productCard.css', 'url' => '/resources/css/productCard.css'],
            ['name' => 'productForm.css', 'url' => '/resources/css/productForm.css'],
        ];

        $scripts = [
            'products-script' => ['src' => '/resources/js/pagination.js'],
            'products-extra-script' => ['src' => '/resources/js/paginationExtras.js'],
        ];

        $popupForm = '/elements/createProductForm.html';

        $admin = $this->authService->getAuthenticatedUser();

        $options = [
            'username' => $admin->getUsername(),
            'email' => $admin->getEmail(),
            'filters' => [
                '/components/filtersCategorySelector.twig',
                '/components/filtersSortSelector.twig',
                '/components/createButton.twig'
            ],
            'containerId' => 'products-container',
            'popupForm' => $popupForm,
            'stylesheets' => array_merge($stylesheets, $this->globalStylesheets),
            'scripts' => $scripts
        ];

        return $this->twig->render(
            response: $response,
            template: '/admin/dashboard.twig',
            data: $options
        );
    }

    public function categoryView(Request $request, Response $response): Response {
        $stylesheets = [
            ['name' => 'categoryCard.css', 'url' => '/resources/css/categoryCard.css'],
            ['name' => 'categoryForm.css', 'url' => '/resources/css/categoryForm.css'],
        ];

        $scripts = [
            'categories-script' => ['src' => '/resources/js/categories.js'],
        ];

        return $this->formatResponse(
            response: $response,
            window: '/windows/categoriesWindow.twig',
            popupForm: '/elements/createCategoryForm.html',
            stylesheets: $stylesheets,
            scripts: $scripts
        );
    }

    public function viewRequest(Request $request, Response $response): Response {
        $data = $request->getParsedBody();
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }
}