<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Customer;
use App\Enums\UserType;
use App\Services\AuthService;
use App\Services\ProductService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class HomeController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly ProductService $productService,
        private readonly AuthService $authService,
    ){
    }

    public function index(Request $request, Response $response): Response {
        $userType = $this->authService->getUserType() ?? null;
        $profileRoute = ($userType === UserType::Customer || $userType === null)? '/profile' : '/admin';

        return $this->twig->render(
            $response,
            'home.twig',
            ['products' => $this->productService->fetchAll()]
        );
    }
}