<?php

namespace yii\graphql;

use Yii;

/**
 * graphQl trait，help the yii Module initial
 * @author QingShan Li
 */
trait GraphQLModuleTrait
{
    /**
     * The schemas for query and/or mutation. It expects an array to provide
     * both the 'query' fields and the 'mutation' fields. You can also
     * provide directly an object GraphQL\Schema
     *
     * Example:
     *
     * 'schema' => [
     *      'query' => [
     *          'user' => 'App\GraphQL\Query\UsersQuery'
     *      ],
     *      'mutation' => [
     *
     *      ],
     *      'types'=>[
     *          'user'=>'app\modules\graph\type\UserType'
     *      ],
     * ]
     *
     * @var array
     */
    public $schema = [];

    /**
     * @var GraphQL the Graph handle
     */
    private $graphQL;

    /**
     * @var callable if don't set error formatter,it will use php-graphql default
     * @see \GraphQL\Executor\ExecutionResult
     */
    public $errorFormatter;

    /**
     * get graphql handler
     * @return GraphQL
     */
    public function getGraphQL()
    {
        if ($this->graphQL == null) {
            $this->graphQL = new GraphQL();
            $this->graphQL->schema($this->schema);
            if ($this->errorFormatter) {
                $this->graphQL->setErrorFormatter($this->errorFormatter);
            } else {
                $this->graphQL->setErrorFormatter(['yii\graphql\ErrorFormatter', 'formatError']);
            }
        }
        return $this->graphQL;
    }
}
