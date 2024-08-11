<?php
declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Views\Twig;

class FileController
{
    public function __construct(
        private readonly Twig $twig,
    ){
    }

    public function form(Request $request, Response $response): Response {
        return $this->twig->render($response, '/forms/uploadFile.twig');
    }

    public function store(Request $request, Response $response) {
        var_dump($_FILES);

        // $fileName = $file->getClientFilename();
        // $message = ['message' => 'file will be stored in "' . STORAGE_PATH . '" directory'];

        // $files = $request->getUploadedFiles();

        // $output = [];
        /** @var UploadedFileInterface $file */
        // foreach($files as $file) {$output[] = $file->getClientFilename();}

        // $request->getBody()->write(json_encode($_FILES));
        return $response;
    }
}