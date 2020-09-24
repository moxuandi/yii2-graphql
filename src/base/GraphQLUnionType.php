<?php

namespace yii\graphql\base;

use Closure;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\UnionType;
use yii\base\InvalidConfigException;
use yii\graphql\GraphQL;

/**
 * Class GraphQLUnionType for UnionType
 * @package yii\graphql\base
 */
class GraphQLUnionType extends GraphQLType
{
    /**
     * @return Type[]
     */
    public function types()
    {
        return [];
    }

    /**
     * @return Closure
     * @throws InvalidConfigException
     */
    protected function getTypeResolver()
    {
        if (!method_exists($this, 'resolveType')) {
            throw new InvalidConfigException(get_called_class() . ' must implement resolveType method');
        }
        $resolver = array($this, 'resolveType');
        return function () use ($resolver) {
            $args = func_get_args();
            return $resolver(...$args);
        };
    }

    /**
     * Get the attributes from the container.
     * @param null $name
     * @param null $except
     * @return array
     * @throws InvalidConfigException
     */
    public function getAttributes($name = null, $except = null)
    {
        $attributes = $this->attributes;

        $resolver = $this->getTypeResolver();
        if (isset($resolver)) {
            $attributes['resolveType'] = $resolver;
        }
        $types = array_map(function ($item) {
            if (is_string($item)) {
                return GraphQL::type($item);
            } else {
                return $item;
            }
        }, static::types());

        $attributes['types'] = $types;
        //TODO support $name and $except??
        return $attributes;
    }

    public function toType()
    {
        return new UnionType($this->toArray());
    }
}
