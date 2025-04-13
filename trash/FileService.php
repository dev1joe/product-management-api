<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\ValidationException;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\MimeTypeDetection\FinfoMimeTypeDetector;
use Psr\Http\Message\UploadedFileInterface;

class FileService
{
    public function __construct(
        private readonly Filesystem $filesystem,
    ){
    }

    public function validateFile(
        UploadedFileInterface $file,
        string $fieldName,
        int $sizeInMBs,
        string $regex,
        array $allowedMimeTypes,
    ): bool
    {
        // validate that there are no upload errors
        if ($file->getError() !== UPLOAD_ERR_OK) {
            // throw new ValidationException([$fieldName => ['Failed to upload photo']]);
            return false;
        }

        // 2. validate file's size
        $maxFileSize = $sizeInMBs * 1024 * 1024;

        if ($file->getSize() > $maxFileSize) {
            // throw new ValidationException([$fieldName => ['Maximum allowed size is 5 MBs']]);
            return false;
        }

        // 3. validate file's name
        $filename = $file->getClientFilename();

        if (!preg_match($regex, $filename)) {
            // throw new ValidationException([$fieldName => ['Invalid filename']]);
            return false;
        }

        // 4. validate MIME type
        // validation using data sent by the client which can be spoofed
        if (!in_array($file->getClientMediaType(), $allowedMimeTypes)) {
            // throw new ValidationException([$fieldName => ['Invalid file type (client side validation)']]);
            return false;
        }

        // validation using a Flysystem MIME type detector
        // you can also use the built-in fInfo function BTW
        // it can figure out the file extension or the file mime type
        $tmpFilePath = $file->getStream()->getMetadata('uri');
        $detector = new FinfoMimeTypeDetector();
        $mimeType = $detector->detectMimeType($tmpFilePath, $file->getStream()->getContents());
        $file->getStream()->rewind();

        if (!in_array($mimeType, $allowedMimeTypes)) {
            // throw new ValidationException([$fieldName => ['Invalid file type (server side validation)']]);
            return false;
        }

        return true;
    }

    /**
     * @return string server relative path of the product image
     * @throws FilesystemException
     */
    public function saveProductImage(UploadedFileInterface $file): string
    {
        // sanitize file name
        $fileName = $file->getClientFilename();
        $fileName = str_replace([' '], ['-'], $fileName);

        $this->filesystem->write('/products/' . $fileName, $file->getStream()->getContents());

        return ('/storage/products/' . $fileName);
    }

    /**
     * @return string server relative path of the product image
     * @throws FilesystemException
     */
    public function saveCategoryImage(UploadedFileInterface $file): string
    {
        // sanitize file name
        $fileName = $file->getClientFilename();
        $fileName = str_replace([' '], ['-'], $fileName);

        $this->filesystem->write('/categories/' . $fileName, $file->getStream()->getContents());

        return ('/storage/categories/' . $fileName);
    }

    /**
     * @return string server relative path of the product image
     * @throws FilesystemException
     */
    public function saveManufacturerLogo(UploadedFileInterface $file): string
    {
        // sanitize file name
        $fileName = $file->getClientFilename();
        $fileName = str_replace([' '], ['-'], $fileName);

        $this->filesystem->write('/manufacturers/' . $fileName, $file->getStream()->getContents());

        return ('/storage/manufacturers/' . $fileName);
    }

}