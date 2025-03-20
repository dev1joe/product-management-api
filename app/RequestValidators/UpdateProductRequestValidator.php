<?php
declare(strict_types=1);

namespace App\RequestValidators;

use App\Contracts\RequestValidatorInterface;
use App\Entities\Category;
use App\Entities\Manufacturer;
use App\Exceptions\ValidationException;
use App\Services\FileService;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\UploadedFileInterface;
use Valitron\Validator;

class UpdateProductRequestValidator implements RequestValidatorInterface
{
    public function __construct(
        private readonly EntityManager $entityManager,
        private readonly FileService $fileService,
    ){
    }

    /**
     * @inheritDoc
     */
    public function validate(array $data): array
    {
        $v = new Validator($data);

        if(array_key_exists('name', $data)) {
            $v->rule('regex', 'name', '/^[A-Za-z0-9\-._,`*:\r\n\s\t()]*$/');
            $v->rule('lengthBetween', 'name', 3, 200);
            // for suitable error message use valitron to validate length instead of regex
        }

        if(array_key_exists('description', $data)) {
            $v->rule('regex', 'description', '/^[A-Za-z0-9\-._,`*:\r\n\s\t()]*$/');
            $v->rule('lengthBetween', 'description', 3, 1000);
        }

        if(array_key_exists('price', $data)) {
            $v->rule('numeric', 'price');
            $v->rule('min', 'price', 1);
        }

        // category handling
        if(array_key_exists('category', $data)) {
            $v->rule('integer', 'category');

            $v->rule(function ($field, $value, $params, $fields) use (&$data) {
                /** @var Category $category */
                $category = $this->entityManager->find(Category::class, (int)$value);

                if (!$category) {
                    return false;
                }

                $data['category'] = $category;
                return true;

            }, 'category')->message('category not found');
        }

        // manufacturer handling
        if(array_key_exists('manufacturer', $data)) {
            $v->rule('integer', 'manufacturer');

            $v->rule(function ($field, $value, $params, $fields) use (&$data) {
                /** @var Manufacturer $manufacturer */
                $manufacturer = $this->entityManager->find(Manufacturer::class, (int)$value);

                if (!$manufacturer) {
                    return false;
                }

                $data['manufacturer'] = $manufacturer;
                return true;

            }, 'manufacturer')->message('manufacturer not found');
        }

        // photo handling
        if(array_key_exists('photo', $data)) {
            $v->rule(function($field, $value, $params, $fields) {

                if(! ($value instanceof UploadedFileInterface)) {
                    return false;
                }

                return $this->fileService->validateFile(
                    $value,
                    'photo',
                    5,
                    '/^[a-zA-Z0-9\s._-]+$/',
                    ['image/png', 'image/jpeg']
                );
            }, 'photo')->message('invalid photo');
        }

        if(! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}