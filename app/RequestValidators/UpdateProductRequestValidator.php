<?php
declare(strict_types=1);

namespace App\RequestValidators;

use App\Contracts\RequestValidatorInterface;
use App\Entities\Category;
use App\Entities\Manufacturer;
use App\Exceptions\ValidationException;
use Doctrine\ORM\EntityManager;
use Valitron\Validator;

class UpdateProductRequestValidator implements RequestValidatorInterface
{
    public function __construct(
        private readonly EntityManager $entityManager,
    ){
    }

    /**
     * @inheritDoc
     */
    public function validate(array $data): array
    {
        $v = new Validator($data);

        if(sizeof($data) == 0) throw new ValidationException(['Request Body is Empty']);

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

        if(! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}