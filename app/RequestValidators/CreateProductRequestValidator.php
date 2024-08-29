<?php
declare(strict_types=1);

namespace App\RequestValidators;

use App\Contracts\RequestValidatorInterface;
use App\Entities\Category;
use App\Exceptions\ValidationException;
use Doctrine\ORM\EntityManager;
use Valitron\Validator;

class CreateProductRequestValidator implements RequestValidatorInterface
{
    public function __construct(
        private readonly EntityManager $entityManager,
    ){
    }

    public function validate(array $data): array
    {
        $v = new Validator($data);

        //TODO: require photo and manufacturer
        $v->rule('required', ['name', 'category', 'price', 'description']);
        $v->rule('regex', ['name', 'description'], '/^[A-Za-z0-9\-._,*:\r\n\s\t()]*$/');
        //TODO: I think I need to allow more characters
        $v->rule('lengthMax', 'description', 1000);

        // price handling
        $v->rule('numeric', 'price');
        $price = (int) $data['price'];
        $price *= 100;
        $data['price'] =$price;

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

        if(! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}