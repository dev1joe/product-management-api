<?php
declare(strict_types=1);

namespace App\RequestValidators;

use App\Contracts\RequestValidatorInterface;
use http\Exception\RuntimeException;
use Psr\Container\ContainerInterface;

class RequestValidatorFactory
{
    public function __construct(
        private readonly ContainerInterface $container,
    ){
    }

    public function make(string $class): RequestValidatorInterface
    {
        // get validator
        $validator = $this->container->get($class);

        // validate
        if(! $validator instanceof RequestValidatorInterface) {
            throw new RuntimeException("Request validator not found");
        }

        // return
        return $validator;
    }
}