<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\ProductService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class HomeController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly ProductService $productService,
    ){
    }

    public function index(Request $request, Response $response): Response {
        return $this->twig->render(
            $response,
            'home.html.twig',
            ['products' => $this->productService->fetchAll()]
        );
    }
}