<?php
declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class AdminController
{
    public function __construct(
        private readonly Twig $twig,
    ){
    }

    public function profile(Request $request, Response $response): Response {
        return $this->twig->render($response, '/admin/dashboard.html');
    }
}