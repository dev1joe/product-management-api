<?php
declare(strict_types=1);

namespace App\DataObjects;

abstract class QueryParams
{
    // although this class doesn't have abstract functions,
    // the important thing is that it cannot be instantiated
    public ?string $orderBy = null;
    public ?string $orderDir = null;
    public ?int $page = null;
    public ?int $limit = null;
    //TODO: do I convert limit variable from integer to mixed to prevent errors in that DTO
    // and validate the data type of it in the categoriesQueryValidator class ?? or stay like that ??

    public function __construct(array $queryParams)
    {
        $queryParams = array_change_key_case($queryParams, CASE_LOWER);

        if(isset($queryParams['orderby'])) {
            $this->orderBy = $queryParams['orderby'];
        }

        if(isset($queryParams['orderdir'])) {
            $this->orderDir = $queryParams['orderdir'];
        }

        if(isset($queryParams['page'])) {
            $this->page = $queryParams['page'];
        }

        if(isset($queryParams['limit'])) {
            $this->limit = (int) $queryParams['limit'];
        }
    }
}