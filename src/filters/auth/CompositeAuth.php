<?php

namespace yii\graphql\filters\auth;

use Yii;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\filters\auth\AuthInterface;
use yii\filters\auth\AuthMethod;
use yii\graphql\GraphQLAction;
use yii\web\IdentityInterface;
use yii\web\Request;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;
use yii\web\User;

/**
 * CompositeAuth 用于解决Graphql单入口请求的授权验证。
 * 在graphql查询中，可以多个请求同时查询，相比较MVC可以认为在一次请求中，发生了多个action的执行行为。
 * @package yii\graphql\filters\auth
 */
class CompositeAuth extends AuthMethod
{
    /**
     * @var array the supported authentication methods. This property should take a list of supported
     * authentication methods, each represented by an authentication class or configuration.
     *
     * If this property is empty, no authentication will be performed.
     *
     * Note that an auth method class must implement the [[\yii\filters\auth\AuthInterface]] interface.
     */
    public $authMethods = [];


    /**
     * @param Action $action
     * @return bool
     * @throws UnauthorizedHttpException
     */
    public function beforeAction($action)
    {
        return empty($this->authMethods) ? true : parent::beforeAction($action);
    }

    /**
     * @param User $user
     * @param Request $request
     * @param Response $response
     * @return IdentityInterface|null
     * @throws InvalidConfigException
     * @throws UnauthorizedHttpException
     */
    public function authenticate($user, $request, $response)
    {
        foreach ($this->authMethods as $i => $auth) {
            if (!$auth instanceof AuthInterface) {
                $this->authMethods[$i] = $auth = Yii::createObject($auth);
                if (!$auth instanceof AuthInterface) {
                    throw new InvalidConfigException(get_class($auth) . ' must implement yii\filters\auth\AuthInterface');
                }
            }
            $identity = $auth->authenticate($user, $request, $response);
            if ($identity !== null) {
                return $identity;
            }
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function challenge($response)
    {
        foreach ($this->authMethods as $method) {
            /** @var $method AuthInterface */
            $method->challenge($response);
        }
    }

    protected function isActive($action)
    {
        if ($action instanceof GraphQLAction) {
            $maps = $action->getGraphQLActions();

            if (empty($this->only)) {
                $onlyMatch = true;
            } else {
                $onlyMatch = true;
                foreach ($maps as $key => $value) {
                    foreach ($this->only as $pattern) {
                        if (fnmatch($pattern, $key)) {
                            continue 2;
                        }
                    }
                    $onlyMatch = false;
                    break;
                }
            }

            $exceptMatch = true;
            foreach ($maps as $key => $value) {
                foreach ($this->except as $pattern) {
                    if (fnmatch($pattern, $key)) {
                        $action->removeGraphQlAction($key);
                        continue 2;
                    }
                }
                $exceptMatch = false;
                break;
            }
            return !$exceptMatch && $onlyMatch;
        } else {
            return parent::isActive($action);
        }
    }
}
