<?php
declare(strict_types=1);

namespace RequestValidators;

use App\Contracts\RequestValidatorInterface;
use App\Exceptions\ValidationException;
use Psr\Http\Message\UploadedFileInterface;

class UploadProductPhotoRequestValidator implements RequestValidatorInterface
{

    public function validate(array $data): array
    {
        /** @var UploadedFileInterface $file */
        $file = $data['photo'] ?? null;

        // 1. validate uploaded file
        if(! $file) {
            throw new ValidationException(['photo' => 'Please upload a photo']);
        }

        // validate that there are no upload errors
        if($file->getError() !== UPLOAD_ERR_OK) {
            throw new ValidationException(['photo' => 'Failed to upload photo']);
        }

        // 2. validate file's size
        $maxFileSize = 5 * 1024 * 1024;

        if($file->getSize() > $maxFileSize) {
            throw new ValidationException(['photo' => 'Maximum allowed size is 5 MBs']);
        }

        // 3. validate file's name

        // 4. validate MIME type
        $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];

        if(in_array($file->getClientMediaType(), $allowedTypes)) {
            throw new ValidationException(['photo' => 'Invalid file type']);
        }

        return $data;
    }
}