<?php
declare(strict_types=1);

namespace App\RequestValidators;

use App\Contracts\RequestValidatorInterface;
use App\Exceptions\ValidationException;
use League\MimeTypeDetection\FinfoMimeTypeDetector;
use Psr\Http\Message\UploadedFileInterface;

class UploadProductPhotoRequestValidator implements RequestValidatorInterface
{

    public function validate(array $data): array
    {
        /** @var UploadedFileInterface $uploadedFile */
        $uploadedFile = $data['photo'] ?? null;

        // 1. validate uploaded file
        if(! $uploadedFile) {
            throw new ValidationException(['photo' => 'Please upload a photo']);
        }

        // validate that there are no upload errors
        if($uploadedFile->getError() !== UPLOAD_ERR_OK) {
            throw new ValidationException(['photo' => 'Failed to upload photo']);
        }

        // 2. validate file's size
        $maxFileSize = 5 * 1024 * 1024;

        if($uploadedFile->getSize() > $maxFileSize) {
            throw new ValidationException(['photo' => 'Maximum allowed size is 5 MBs']);
        }

        // 3. validate file's name
        $filename = $uploadedFile->getClientFilename();

        if (! preg_match('/^[a-zA-Z0-9\s._-]+$/', $filename)) {
            throw new ValidationException(['receipt' => ['Invalid filename']]);
        }

        // 4. validate MIME type
        // validation using data sent by the client which can be spoofed
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'application/pdf'];

        if(! in_array($uploadedFile->getClientMediaType(), $allowedMimeTypes)) {
            throw new ValidationException(['photo' => 'Invalid file type (client side validation)']);
        }

        // validation using a Flysystem MIME type detector
        // you can also use the built-in fInfo function BTW
        // it can figure out the file extension or the file mime type
        $tmpFilePath = $uploadedFile->getStream()->getMetadata('uri');
        $detector = new FinfoMimeTypeDetector();
        $mimeType = $detector->detectMimeType($tmpFilePath, $uploadedFile->getStream()->getContents());
        $uploadedFile->getStream()->rewind();

        if(! in_array($mimeType, $allowedMimeTypes)) {
            throw new ValidationException(['photo' => 'Invalid file type (server side validation)']);
        }

        return $data;
    }
}