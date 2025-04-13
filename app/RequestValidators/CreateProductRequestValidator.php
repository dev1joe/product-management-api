<?php
declare(strict_types=1);

namespace App\RequestValidators;

use App\Contracts\RequestValidatorInterface;
use App\Entities\Category;
use App\Entities\Manufacturer;
use App\Exceptions\ValidationException;
use Doctrine\ORM\EntityManager;
use Valitron\Validator;

class CreateProductRequestValidator implements RequestValidatorInterface
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

        $v->rule('required', ['name', 'category', 'price', 'description', 'manufacturer']);
        $v->rule('regex', ['name', 'description'], '/^[A-Za-z0-9\-._,*:\r\n\s\t()]*$/');
        $v->rule('lengthMax', 'name', 200);
        $v->rule('lengthMax', 'description', 1000);

        // price handling
        $v->rule('numeric', 'price');
        $v->rule('min', 'price', 1);

//        $price = (float) $data['price'];
//        $price *= 100;
//        $data['price'] = (int) $price;

        // category handling
        $v->rule('integer', 'category');

        $v->rule(function($field, $value, $params, $fields) use(&$data) {
            /** @var Category $category */
            $category = $this->entityManager->find(Category::class, (int) $value);

            if(! $category) {
                return false;
            }

            $data['category'] = $category;
            return true;

        }, 'category')->message('category not found');

         // manufacturer handling
         $v->rule('integer', 'manufacturer');

        $v->rule(function($field, $value, $params, $fields) use(&$data) {
            /** @var Manufacturer $manufacturer */
            $manufacturer = $this->entityManager->find(Manufacturer::class, (int) $value);

            if(! $manufacturer) {
                return false;
            }

            $data['manufacturer'] = $manufacturer;
            return true;

        }, 'manufacturer')->message('manufacturer not found');

        if(! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}