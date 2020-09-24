<?php

namespace yii\graphql\types;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

/**
 * Class PaginationType
 * @package yii\graphql\types
 */
class PaginationType extends InputObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Pagination',
            'description' => '',
            'fields' => [
                'first' => [
                    'type' => Type::int(),
                    'description' => 'Returns the first n elements from the list.'
                ],
                'after' => [
                    'type' => Type::string(),
                    'description' => 'Returns the elements in the list that come after the specified global ID.'
                ],
                'last' => [
                    'type' => Type::int(),
                    'description' => 'Returns the last n elements from the list..'
                ],
                'before' => [
                    'type' => Type::string(),
                    'description' => 'Returns the elements in the list that come before the specified global ID.'
                ],
            ],
        ];
        parent::__construct($config);
    }
}
