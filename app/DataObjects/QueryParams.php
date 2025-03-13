<?php
declare(strict_types=1);

namespace App\DataObjects;

class QueryParams
{
    // although this class doesn't have abstract functions,
    // the important thing is that it cannot be instantiated
    public ?string $orderBy = null;
    public ?string $orderDir = null;
    public mixed $page = null;
    public mixed $limit = null; // allow page and limit to be mixed values, allowing validators to catch errors

    //TODO: refactor default values to environment variables / constants
    public function __construct(array $queryParams)
    {
        $queryParams = array_change_key_case($queryParams, CASE_LOWER);

        $this->page = (isset($queryParams['page']))? $queryParams['page'] : 1;
        $this->limit = (isset($queryParams['limit']))? $queryParams['limit'] : 10;
        $this->orderBy = (isset($queryParams['orderby']))? $queryParams['orderby'] : 'id';
        $this->orderDir = (isset($queryParams['orderdir']))? $queryParams['orderdir'] : 'asc';
    }
}