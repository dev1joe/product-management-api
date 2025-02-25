<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\BaseService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class BaseController
{
    public function __construct(
        private readonly BaseService $baseService,
    ){
    }
}