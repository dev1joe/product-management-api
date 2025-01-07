<?php
declare(strict_types=1);

namespace App\Controllers;

use App\DataObjects\CategoryQueryParams;
use App\DataObjects\ProductQueryParams;
use App\Entities\Customer;
use App\Enums\UserType;
use App\Services\AuthService;
use App\Services\CategoryService;
use App\Services\ProductService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class HomeController
{
    public function __construct(
        private readonly Twig            $twig,
        private readonly AuthService     $authService,
        private readonly ProductService  $productService,
        private readonly CategoryService $categoryService,
    )
    {
    }

    private function formatResponse(
        Response $response,
        array $stylesheets,
        array $scripts = []
    ): Response {

        $options = [
            'stylesheets' => $stylesheets,
            'scripts' => $scripts
        ];

        return $this->twig->render(
            $response,
            '/admin/dashboard.twig',
            $options
        );
    }

    public function index(Request $request, Response $response): Response {
        // managing user profile access
        $userType = $this->authService->getUserType() ?? null;
        $profileRoute = ($userType === UserType::Customer || $userType === null) ? '/profile' : '/admin';


        // fetching some categories
        $categories = $this->categoryService->fetchPaginatedCategories(
            new CategoryQueryParams([
                'orderBy' => 'id',
                'orderDir' => 'asc',
                'limit' => 7
            ])
        );

        return $this->twig->render(
            response: $response,
            template: 'home.twig',
            data: [
                'products' => $this->productService->fetchAll(),
                'categories' => $categories,
                'defaultCategory' => '/storage/categories/1920x1080.svg',
                'stylesheets' => [
                    ['name' => 'navbar.css', 'url' => '/resources/css/navbar.css'],
                ]
            ]
        );
    }

    public function products(Request $request, Response $response): Response {
        $options = [
            'navbar' => '/elements/navbar.twig',
            'filters' => [
                ['name' => 'Category', 'component' => '/components/filtersCategorySelector.twig'],
                ['name' => 'Sort By', 'component' => '/components/filtersSortSelector.twig'],
                ['name' => 'Price Range', 'component' => '/components/filtersPriceRange.twig'],
            ],
            'stylesheets' => [
                ['name' => 'navbar.css', 'url' => '/resources/css/navbar.css'],
                ['name' => 'pagination.css', 'url' => '/resources/css/pagination.css'],
                ['name' => 'windows.css', 'url' => '/resources/css/windows.css'],
                ['name' => 'productCard.css', 'url' => '/resources/css/productCard.css'],
            ],
            'scripts' => [
                'products-script' => ['src' => '/resources/js/pagination.js'],
            ]
        ];

        return $this->twig->render(
            response: $response,
            template: 'pagination.twig',
            data: $options
        );
    }
}