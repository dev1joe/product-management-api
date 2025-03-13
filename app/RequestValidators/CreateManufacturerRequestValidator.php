<?php
declare(strict_types=1);

namespace App\RequestValidators;

use App\Contracts\RequestValidatorInterface;
use App\Exceptions\ValidationException;
use App\Services\FileService;
use Valitron\Validator;

class CreateManufacturerRequestValidator implements RequestValidatorInterface
{
    public function __construct(
        private readonly FileService $fileService,
    ){}

    public function validate(array $data):array {
        $v = new Validator($data);

        $v->rule('required', ['name', 'email']);
        $v->rule('lengthMin', 'name', 3);
        $v->rule('regex', 'name', '/^[A-Za-z ]*$/');
        $v->rule('email', 'email');

        if(isset($data['logo'])) {
            $file = $data['logo'];
            $this->fileService->validateFile(
                $file,
                'logo',
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