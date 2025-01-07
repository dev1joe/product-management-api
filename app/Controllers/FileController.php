<?php
declare(strict_types=1);

namespace App\Controllers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Views\Twig;

class FileController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly Filesystem $filesystem,
    ){
    }

    public function form(Request $request, Response $response): Response {
        return $this->twig->render($response, '/uploadFile.twig');
    }

    /**
     * @throws FilesystemException
     */
    public function tmp(Request $request, Response $response, array $args): Response {
        $queryParams = $request->getQueryParams();

        if(sizeof($queryParams) > 0) {
            if(isset($queryParams['file'])) {
                $filePath = $queryParams['file'];
                $filePath = str_replace('/storage', '', $filePath);

                if($this->filesystem->fileExists($filePath)) {
                    $fileContent = $this->filesystem->read($filePath);
                    $fileSize = $this->filesystem->fileSize($filePath);
                    $fileMimeType = $this->filesystem->mimeType($filePath);

                    $response = $response->withHeader('Content-Length', $fileSize)->withHeader('Content-Type', $fileMimeType);
                    $response->getBody()->write($fileContent);
                    return $response;
                } else {
                    $response->getBody()->write(json_encode(['error' => "file $filePath not found!"]));
                    return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
                }
            }
        } else {
            return $response;
            // query params not defined
            // return all available files
        }

        $response->getBody()->write(json_encode($queryParams));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * @throws FilesystemException
     */
    public function fetchFile(Request $request, Response $response, array $args): Response {
        $uri = $args[0];

        // path will be like `/storage/products/myProduct.png`
        // I should remove `/storage` because that the root of the file system
        $pathArray = explode('/', $uri);
        foreach($pathArray as $index => $value) {
            if($value === 'storage'){
                unset($pathArray[$index]);
            }
        }

        $filePath = '';
        foreach($pathArray as $str) {
            $filePath .= '/' . $str;
        }

        if($this->filesystem->fileExists($filePath)) {
            $fileContent = $this->filesystem->read($filePath);

            $response = $response->withHeader('Content-Length', strlen($fileContent));
            $response->getBody()->write($fileContent);
            return $response;
        } else {
            $response->getBody()->write(json_encode(['error' => "file $filePath not found! | initial uri: $uri"]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
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