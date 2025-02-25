<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\InventoryService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class InventoryController
{
    public function __construct(
        private readonly InventoryService $inventoryService,
    ){
    }

    public function fetchAll(Request $request, Response $response): Response {

    }
}