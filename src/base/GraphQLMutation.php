<?php

namespace yii\graphql\base;

use yii\graphql\traits\ShouldValidate;

/**
 * Class GraphQLMutation
 * @package yii\graphql\base
 */
class GraphQLMutation extends GraphQLField
{
    use ShouldValidate;
}
