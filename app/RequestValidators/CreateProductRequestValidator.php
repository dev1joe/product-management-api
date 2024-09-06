<?php
declare(strict_types=1);

namespace App\RequestValidators;

use App\Contracts\RequestValidatorInterface;
use App\Entities\Category;
use App\Entities\Manufacturer;
use App\Exceptions\ValidationException;
use Doctrine\ORM\EntityManager;
use League\MimeTypeDetection\FinfoMimeTypeDetector;
use Psr\Http\Message\UploadedFileInterface;
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

        //TODO: require photo
        $v->rule('required', ['name', 'category', 'price', 'description', 'manufacturer']);
        $v->rule('regex', ['name', 'description'], '/^[A-Za-z0-9\-._,*:\r\n\s\t()]*$/');
        //TODO: I think I need to allow more characters
        $v->rule('lengthMax', 'description', 1000);

        // price handling
        $v->rule('numeric', 'price');
        $v->rule(function($field, $value, $params, $fields) {
            $price = (float) $value;

            if($price == 0 || $price < 0) {
                return false;
            } else {
                return true;
            }
        }, "price")->message("price must be positive");

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

        // photo handling
        if(array_key_exists('photo', $data)) {
            /** @var UploadedFileInterface $uploadedFile */
            $uploadedFile = $data['photo'];

            // validate that there are no upload errors
            if($uploadedFile->getError() !== UPLOAD_ERR_OK) {
                throw new ValidationException(['photo' => ['Failed to upload photo']]);
            }

            // 2. validate file's size
            $maxFileSize = 5 * 1024 * 1024;

            if($uploadedFile->getSize() > $maxFileSize) {
                throw new ValidationException(['photo' => ['Maximum allowed size is 5 MBs']]);
            }

            // 3. validate file's name
            $filename = $uploadedFile->getClientFilename();

            if (! preg_match('/^[a-zA-Z0-9\s._-]+$/', $filename)) {
                throw new ValidationException(['photo' => ['Invalid filename']]);
            }

            // 4. validate MIME type
            // validation using data sent by the client which can be spoofed
            $allowedMimeTypes = ['image/jpeg', 'image/png'];

            if(! in_array($uploadedFile->getClientMediaType(), $allowedMimeTypes)) {
                throw new ValidationException(['photo' => ['Invalid file type (client side validation)']]);
            }

            // validation using a Flysystem MIME type detector
            // you can also use the built-in fInfo function BTW
            // it can figure out the file extension or the file mime type
            $tmpFilePath = $uploadedFile->getStream()->getMetadata('uri');
            $detector = new FinfoMimeTypeDetector();
            $mimeType = $detector->detectMimeType($tmpFilePath, $uploadedFile->getStream()->getContents());
            $uploadedFile->getStream()->rewind();

            if(! in_array($mimeType, $allowedMimeTypes)) {
                throw new ValidationException(['photo' => ['Invalid file type (server side validation)']]);
            }

        }

        if(! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}