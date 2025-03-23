<?php
declare(strict_types=1);

namespace App\DataObjects;

class QueryParams
{
    // although this class doesn't have abstract functions,
    // the important thing is that it cannot be instantiated
    public ?string $name = null;
    public ?string $orderBy = null;
    public ?string $orderDir = null;
    public mixed $page = null;
    public mixed $limit = null; // allow page and limit to be mixed values, allowing validators to catch errors

    //TODO: refactor default values to environment variables / constants
    public function __construct(array $params)
    {
        $params = array_change_key_case($params, CASE_LOWER);

        $this->name = (isset($params['name']))? $params['name'] : null;
        $this->page = (isset($params['page']))? $params['page'] : 1;
        $this->limit = (isset($params['limit']))? $params['limit'] : 10;
        $this->orderBy = (isset($params['orderby']))? $params['orderby'] : 'id';
        $this->orderDir = (isset($params['orderdir']))? $params['orderdir'] : 'asc';
    }
}