<?php

namespace yii\graphql;

use GraphQL\Error\Error;
use GraphQL\Error\FormattedError;
use Throwable;
use Yii;
use yii\graphql\exceptions\ValidatorException;
use yii\web\HttpException;

/**
 * Class ErrorFormatter
 * @package yii\graphql
 */
class ErrorFormatter
{
    /**
     * @param Error $e
     * @return array|mixed[]
     * @throws Throwable
     */
    public static function formatError(Error $e)
    {
        $previous = $e->getPrevious();
        if ($previous) {
            Yii::$app->getErrorHandler()->logException($previous);
            if ($previous instanceof ValidatorException) {
                return $previous->formatErrors;
            }
            if ($previous instanceof HttpException) {
                return ['code' => $previous->statusCode, 'message' => $previous->getMessage()];
            } else {
                return ['code' => $previous->getCode(), 'message' => $previous->getMessage()];
            }
        } else {
            Yii::error($e->getMessage(), get_class($e));
        }
        return FormattedError::createFromException($e, YII_DEBUG);
    }
}
