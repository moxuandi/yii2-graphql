<?php

namespace yii\graphql\exceptions;

use Throwable;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\base\Model;

/**
 * Class ValidatorException
 * @package yii\graphql\exceptions
 */
class ValidatorException extends Exception
{
    public $formatErrors;

    /**
     * ValidatorException constructor.
     * @param Model $model
     * @param int $code
     * @param Throwable|null $previous
     * @throws InvalidConfigException
     */
    public function __construct($model, $code = 0, Throwable $previous = null)
    {
        parent::__construct("model {$model->formName()} validate false", $code, $previous);
        $this->formatModelErrors($model);
    }

    /**
     * @param Model $model
     */
    private function formatModelErrors($model)
    {
        foreach ($model->getErrors() as $field => $fieldErrors) {
            foreach ($fieldErrors as $error) {
                $this->formatErrors[] = ['code' => $field, 'message' => $error];
            }
        }
    }
}
