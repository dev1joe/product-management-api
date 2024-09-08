<?php
declare(strict_types=1);

namespace App\RequestValidators;

use App\Contracts\RequestValidatorInterface;
use App\Exceptions\ValidationException;
use App\Services\FileService;
use Valitron\Validator;

class CreateCategoryRequestValidator implements RequestValidatorInterface
{
    public function __construct(
        private readonly FileService $fileService,
    ){
    }

    public function validate(array $data):array {
        $v = new Validator($data);

        $v->rule('required', ['name']);
        $v->rule('lengthMin', 3);
        $v->rule('regex', 'name', '/^[A-Za-z ]*$/');

        if(isset($data['image'])) {
            $file = $data['image'];
            $this->fileService->validateFile(
                $file,
                'image',
                5,
                '/^[a-zA-Z0-9\s._-]+$/',
                ['image/png', 'image/jpeg']
            );
        }

        if(! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}