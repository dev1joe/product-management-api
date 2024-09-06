<?php
declare(strict_types=1);

namespace App\Services;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Psr\Http\Message\UploadedFileInterface;

class FileService
{
    public function __construct(
        private readonly Filesystem $filesystem,
    ){
    }

    /**
     * @return string server relative path of the product image
     * @throws FilesystemException
     */
    public function saveProductImage(UploadedFileInterface $file): string {
        $fileName = $file->getClientFilename();
        $fileName = str_replace([' '], ['-'], $fileName);


        $this->filesystem->write('/products/' . $fileName, $file->getStream()->getContents());

        return('/storage/products/'.$fileName);
    }

}