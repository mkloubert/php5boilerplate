<?php

/**********************************************************************************************************************
 * php5boilerplate (https://github.com/mkloubert/php5boilerplate)                                                     *
 * Copyright (c) Marcel Joachim Kloubert <marcel.kloubert@gmx.net>, All rights reserved.                              *
 *                                                                                                                    *
 *                                                                                                                    *
 * This software is free software; you can redistribute it and/or                                                     *
 * modify it under the terms of the GNU Lesser General Public                                                         *
 * License as published by the Free Software Foundation; either                                                       *
 * version 3.0 of the License, or (at your option) any later version.                                                 *
 *                                                                                                                    *
 * This software is distributed in the hope that it will be useful,                                                   *
 * but WITHOUT ANY WARRANTY; without even the implied warranty of                                                     *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU                                                  *
 * Lesser General Public License for more details.                                                                    *
 *                                                                                                                    *
 * You should have received a copy of the GNU Lesser General Public                                                   *
 * License along with this software.                                                                                  *
 **********************************************************************************************************************/

namespace php5bp\Modules;

use \php5bp\IO\Files\UploadedFileInterface;
use \php5bp\Modules\Execution\ContextInterface as ModuleExecutionContext;
use \System\Linq\Enumerable;


/**
 * A basic module.
 *
 * @package php5bp\Modules
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
abstract class ModuleBase extends \php5bp\Object implements ModuleInterface {
    /**
     * Default name of a variable that contains the action name.
     */
    const DEFAULT_VAR_NAME_ACTION = 'action';
    /**
     * List separator expression.
     */
    const LIST_SEPARATOR = ';';


    /**
     * @var ContextInterface
     */
    private $_context;


    /**
     * Is executed AFTER ModuleBase::beforeExecute() and/or ModuleBase::execute() were invoked.
     *
     * @param ModuleExecutionContext $ctx The underlying execution context.
     * @param bool $wasExecuted ModuleBase::execute() was invoked or not.
     *                          (null) indicates that execution of ModuleBase::beforeExecute() method failed.
     * @param \Exception $ex The thrown exception.
     */
    protected function afterExecution(ModuleExecutionContext $ctx, $wasExecuted, \Exception $ex = null) {
    }

    /**
     * Is executed BEFORE ModuleBase::execute() is invoked.
     *
     * @param ModuleExecutionContext $ctx The underlying execution context.
     *
     * @return mixed If (false) is returned: ModuleBase::execute() will not be invoked.
     */
    protected function beforeExecute(ModuleExecutionContext $ctx) {
    }

    /**
     * Calls a method from that object / class.
     *
     * @param string $methodName The name of the method to call.
     * @param array $args The arguments for the method.
     * @param bool $found The variable where to write if method was found or not.
     *
     * @return mixed The result of the method.
     */
    protected function callMyMethod($methodName, array $args = array(), &$found = null) {
        $found = false;
        $cls   = \get_class($this);

        $methodName = \trim($methodName);

        $possibleMethods = array(
            array($this, $methodName   , false),
            array($cls , $methodName   , false),
            array($this, '__call'      , true),
            array($cls , '__callStatic', true),
        );

        foreach ($possibleMethods as $m) {
            $objOrClass = $m[0];
            $method     = $m[1];

            if (!\method_exists($objOrClass, $method)) {
                continue;
            }

            $found = true;

            $methodArgs = $args;
            if ($m[2]) {
                // magic method
                $methodArgs = array($methodName, $methodArgs);
            }

            return \call_user_func_array(array($objOrClass, $method),
                                         $methodArgs);
        }
    }

    /**
     * {@inheritDoc}
     */
    public final function context() {
        return $this->_context;
    }

    /**
     * Executes the module.
     *
     * @param ModuleExecutionContext $ctx The underlying execution context.
     *
     * @return mixed The result to output.
     */
    protected abstract function execute(ModuleExecutionContext $ctx);

    /**
     * Returns the default list of sources where to look for the value of an action argument.
     *
     * @return array The list of sources.
     */
    protected function getDefaultActionArgumentSources() {
        return array('var', 'request');
    }

    /**
     * Returns the default list of sources where to look for the action name.
     *
     * @return array The list of sources.
     */
    protected function getDefaultActionNameSources() {
        return array('request');
    }

    /**
     * Returns the default HTTP response code.
     *
     * @param ModuleExecutionContext $ctx The underlying execution context.
     *
     * @return int The response code or (null) to set none.
     */
    protected function getDefaultHttpResponseCode(ModuleExecutionContext $ctx) {
        return null;
    }

    /**
     * Returns the value of a variable from a source.
     *
     * @param ModuleExecutionContext $ctx The underlying execution context.
     * @param array|\Traversable $sources The allowed sources.
     * @param mixed $var The variable (name / callable / etc.).
     * @param string $defaultSrc The name of the default source.
     *
     * @return mixed The value of $var.
     */
    protected function getVarFromSource(ModuleExecutionContext $ctx, $sources, $var, $defaultSrc) {
        $defaultSrc = \trim(\strtolower($defaultSrc));

        $result = null;

        foreach ($sources as $src) {
            $src = \trim(\strtolower($src));
            if ('' === $src) {
                $src = $defaultSrc;
            }

            $found = false;

            switch ($src) {
                case 'var':
                case 'vars':
                    $result = $ctx->getVar($var, $result, $found);
                    break;

                case 'request':
                    $result = $ctx->request()->request($var, $result, $found);
                    break;

                case 'post':
                    $result = $ctx->request()->post($var, $result, $found);
                    break;

                case 'get':
                    $result = $ctx->request()->get($var, $result, $found);
                    break;

                case 'file':
                case 'files':
                    $result = $ctx->request()->file($var);
                    if ($result instanceof UploadedFileInterface) {
                        $found = true;
                    }
                    break;

                case 'func':
                case 'function':
                case 'provider':
                case 'callable':
                    if (\is_callable($var)) {
                        $found = true;

                        $funcRes = \call_user_func_array($var,
                                                         array($ctx, $result, &$found, $this));

                        if ($found) {
                            $result = $funcRes;
                        }
                    }
                    break;

                case 'module':
                    $methodResult = $this->callMyMethod($var,
                                                        array($ctx, $result, &$found, $this),
                                                        $found);

                    if ($found) {
                        $result = $methodResult;
                    }
                    break;

                case 'session':
                    $result = $ctx->request()->session($var, $result, $found);
                    break;

                case 'cookie':
                case 'cookies':
                    $result = $ctx->request()->cookie($var, $result, $found);
                    break;

                case 'header':
                case 'headers':
                    $result = $ctx->request()->header($var, $result, $found);
                    break;

                case 'global':
                case 'globals':
                    $result = \php5bp::getVar($var, $result, $found);
                    break;

                case 'server':
                    $result = $ctx->request()->server($var, $result, $found);
                    break;

                case 'env':
                case 'environment':
                    $result = $ctx->request()->env($var, $result, $found);
                    break;

                default:
                    //TODO throw exception
                    break;
            }

            if ($found) {
                break;
            }
        }

        return $result;
    }

    /**
     * Prepares the arguments for an execution method.
     *
     * @param ModuleExecutionContext $ctx The underlying execution context.
     * @param array $args The variable where to write the arguments to
     */
    protected function prepareInitialExecutionMethodArgs(ModuleExecutionContext $ctx, array &$args) {
    }

    /**
     * {@inheritDoc}
     */
    public final function render() {
        $result = null;

        \ob_start();
        try {
            $appConf = \php5bp::appConf();
            $meta    = $this->context()->meta();

            $actionVar = null;

            $execCtx           = new \php5bp\Modules\Execution\Context();
            $execCtx->Request  = new \php5bp\Http\Requests\Context();
            $execCtx->Response = new \php5bp\Http\Responses\Context();

            if (\array_key_exists('config', $meta)) {
                $execCtx->Config = $meta['config'];
            }

            if (null === $execCtx->Config) {
                $execCtx->Config = array();
            }

            if (!\is_array($execCtx->Config)) {
                $execCtx->Config = array($execCtx->Config);
            }

            $allowedActionVarSources = null;

            // get module default settings from app config
            if (isset($appConf['modules'])) {
                if (isset($appConf['modules']['actions'])) {
                    if (\array_key_exists('source', $appConf['modules']['actions'])) {
                        $allowedActionVarSources = $appConf['modules']['actions']['source'];
                    }

                    if (\array_key_exists('var', $appConf['modules']['actions'])) {
                        $actionVar = $appConf['modules']['actions']['var'];
                    }
                }
            }

            // get module default settings from module meta
            if (isset($meta['module'])) {
                if (isset($meta['module']['actions'])) {
                    if (\array_key_exists('source', $meta['module']['actions'])) {
                        $allowedActionVarSources = $meta['module']['actions']['source'];
                    }

                    if (\array_key_exists('var', $meta['module']['actions'])) {
                        $actionVar = $meta['module']['actions']['var'];
                    }
                }
            }

            if ((null === $actionVar) || \is_string($actionVar)) {
                $actionVar = \trim($actionVar);
                if ('' === $actionVar) {
                    $actionVar = static::DEFAULT_VAR_NAME_ACTION;
                }
            }

            if (null === $allowedActionVarSources) {
                // set default
                $allowedActionVarSources = $this->getDefaultActionNameSources();
            }

            if (!\is_array($allowedActionVarSources)) {
                $allowedActionVarSources = \explode(static::LIST_SEPARATOR, $allowedActionVarSources);
            }

            // action name
            $actionName = $this->getVarFromSource($execCtx,
                                                  $allowedActionVarSources, $actionVar, 'request');

            // do not change default view
            $initialView = false;

            $execCtx->setAction($actionName);
            $execCtx->setDefaultView();
            $execCtx->setVar('module', $this);

            $wasExecuted = null;
            $exception = null;

            /* @var callable $exceptionHandler */
            $exceptionHandler = null;
            $executionMode = null;
            $packAdditionalActionArgs = false;

            if (\array_key_exists('mode', $meta)) {
                $executionMode = $meta['mode'];
            }

            if (\array_key_exists('view', $meta)) {
                $initialView = $meta['view'];
            }

            $setupExecutionContext = function(array &$methodArgs = null) use (&$initialView, &$exceptionHandler, $execCtx, &$executionMode, &$methodResult) {
                switch (\trim(\strtolower($executionMode))) {
                    case 'json':
                        $methodResult         = array();
                        $methodResult['code'] = 0;
                        $methodResult['msg']  = 'OK';

                        $exceptionHandler = function(\Exception $ex) use (&$methodResult) {
                            $methodResult = \php5bp::createExceptionResult($ex);
                        };

                        $methodArgs[] = &$methodResult;

                        $execCtx->setupForJson();
                        break;

                    case 'html':
                        $execCtx->setupForHtml();
                        break;
                }

                if (false !== $initialView) {
                    if (true !== $initialView) {
                        $execCtx->setView($initialView);
                    }
                    else {
                        $execCtx->setDefaultView();
                    }
                }
            };

            try {
                if (false !== $this->beforeExecute($execCtx)) {
                    $wasExecuted = true;
                    $invokeDefault = false;
                    $methodResult = null;

                    $actionName = \trim(\strtolower($execCtx->getAction()));
                    if ('' === $actionName) {
                        $invokeDefault = true;
                    }
                    else {
                        $actionEntry = false;

                        // find matching action entry
                        if (\array_key_exists('actions', $meta)) {
                            $actionList = $meta['actions'];
                            if (!\is_array($actionList)) {
                                // get action list from ; separated string

                                $actionList = Enumerable::create(\explode(static::LIST_SEPARATOR, $actionList))
                                                        ->select(function($x) {
                                                                     return \trim($x);
                                                                 })
                                                        ->distinct("\\strcasecmp")
                                                        ->toArray(function($key, $value) {
                                                                      return $value;
                                                                  });
                            }

                            $actionEntry = Enumerable::create($actionList)
                                                     ->singleOrDefault(function($x, $ctx) use ($actionName) {
                                                                           $key = \is_array($x) ? $ctx->key : $x;

                                                                           return \trim(\strtolower($key)) === $actionName;
                                                                       }, false);
                        }

                        if (false !== $actionEntry) {
                            $actionMethod = null;

                            if (!\is_array($actionEntry)) {
                                $actionEntry = array(
                                    'method' => $actionEntry,
                                );
                            }

                            if (\array_key_exists('method', $actionEntry)) {
                                $actionMethod = $actionEntry['method'];
                            }

                            // action specific MODE
                            if (\array_key_exists('mode', $actionEntry)) {
                                $executionMode = $actionEntry['mode'];
                            }

                            // pack args
                            if (\array_key_exists('packArgs', $actionEntry)) {
                                $packAdditionalActionArgs = $actionEntry['packArgs'];
                            }

                            // action specific VIEW
                            if (\array_key_exists('view', $actionEntry)) {
                                $initialView = $actionEntry['view'];
                            }

                            $actionMethod = \trim($actionMethod);
                            if ('' === $actionMethod) {
                                $actionMethod = $actionName;
                            }

                            $actionMethodArgs = array($execCtx);
                            $this->prepareInitialExecutionMethodArgs($execCtx, $actionMethodArgs);

                            $actionArgs = null;
                            if (\array_key_exists('args', $actionEntry)) {
                                if (\is_array($actionEntry['args'])) {
                                    $actionArgs = $actionEntry['args'];
                                }
                                else {
                                    $actionArgs = Enumerable::create(\explode(static::LIST_SEPARATOR, $actionEntry['args']))
                                                            ->select(function ($x) {
                                                                         $x = \trim($x);
                                                                         if ('' === $x) {
                                                                             return null;
                                                                         }

                                                                         $result         = array();
                                                                         $result['name'] = $x;

                                                                         return $result;
                                                                     })
                                                            ->ofType('array')
                                                            ->toArray();
                                }
                            }

                            if (null === $actionArgs) {
                                // set default
                                $actionArgs = array();
                            }

                            $additionalActionArgs = array();
                            foreach ($actionArgs as $aa) {
                                $argName         = null;
                                $argSources      = null;
                                $argTransformers = null;

                                if (!\is_array($aa)) {
                                    $aa = array(
                                        'name' => $aa,
                                    );
                                }

                                if (\array_key_exists('name', $aa)) {
                                    $argName = $aa['name'];
                                }

                                if (\array_key_exists('source', $aa)) {
                                    $argSources = $aa['source'];
                                }

                                if (\array_key_exists('transformer', $aa)) {
                                    $argTransformers = $aa['transformer'];
                                }

                                if ((null === $argName) || \is_string($argName)) {
                                    $argName = \trim($argName);
                                    if ('' === $argName) {
                                        //TODO: throw exception
                                        continue;
                                    }
                                }

                                if (null !== $argSources) {
                                    if (!\is_array($argSources)) {
                                        $argSources = \explode(static::LIST_SEPARATOR, $argSources);
                                    }
                                }

                                if (null !== $argTransformers) {
                                    if (!\is_array($argTransformers)) {
                                        $argTransformers = \explode(static::LIST_SEPARATOR, $argTransformers);
                                    }
                                }

                                if (null === $argSources) {
                                    // set default
                                    $argSources = $this->getDefaultActionArgumentSources();
                                }

                                if (null === $argTransformers) {
                                    // set default
                                    $argTransformers = array();
                                }

                                // argument value
                                $argValue = $this->getVarFromSource($execCtx,
                                                                    $argSources, $argName, 'var');

                                // transform value
                                foreach ($argTransformers as $at) {
                                    $argValue = $at($argValue);
                                }

                                $additionalActionArgs[] = $argValue;
                            }

                            if ($packAdditionalActionArgs) {
                                $additionalActionArgs = array($additionalActionArgs);
                            }

                            $actionMethodArgs = \array_merge($actionMethodArgs,
                                                             $additionalActionArgs);

                            \call_user_func_array($setupExecutionContext,
                                                  array(&$actionMethodArgs));

                            if (null === $actionMethodArgs) {
                                $actionMethodArgs = array();
                            }

                            $result = $this->callMyMethod($actionMethod, $actionMethodArgs, $actionMethodFound);
                            if (!$actionMethodFound) {
                                //TODO throw exception
                            }
                        }
                        else {
                            //TODO throw exception
                        }
                    }

                    if ($invokeDefault) {
                        $setupExecutionContext();

                        $result = $this->execute($execCtx);
                    }

                    if (null !== $methodResult) {
                        // custom result defined

                        if (null === $result) {
                            // overwrite
                            $result = $methodResult;
                        }
                        else {
                            // append
                            $result = $result . $methodResult;
                        }
                    }
                }
                else {
                    $wasExecuted = false;
                }
            }
            catch (\Exception $ex) {
                $exception = $ex;

                if (null === $exceptionHandler) {
                    throw $ex;
                }
                else {
                    $exceptionHandler($ex, $execCtx);
                }
            }
            finally {
                $this->afterExecution($execCtx, $wasExecuted, $exception);

                // response code
                {
                    $respCode = $execCtx->response()->getCode();
                    if (null === $respCode) {
                        $respCode = $this->getDefaultHttpResponseCode($execCtx);
                    }

                    if (null !== $respCode) {
                        \header(':', true, $respCode);
                    }
                }

                // headers
                foreach ($execCtx->response()->headers() as $hn => $hv) {
                    \header($hn . ': ' . \strval($hv));
                }
            }

            $buffer = \ob_get_contents();
            if (!\php5bp::isNullOrEmpty($buffer)) {
                $result = $buffer . $result;
            }

            $viewName = \trim($execCtx->getView());
            if ('' !== $viewName) {
                $view = new \php5bp\Views\SimpleView();

                // initialize with defaults
                $view->__set('action'  , $execCtx->getAction());
                $view->__set('content' , $result);
                $view->__set('debug'   , \php5bp::isDebug());
                $view->__set('request' , $execCtx->request());
                $view->__set('response', $execCtx->response());
                $view->__set('title'   , $execCtx->getTitle());

                // overwrite/fill with values from execution context
                foreach ($execCtx->vars() as $vn => $vv) {
                    $view->__set($vn, $vv);
                }

                $result = $view->render($viewName);
            }

            return $result;
        }
        catch (\Exception $ex) {
            return $ex;
        }
        finally {
            \ob_end_clean();
        }
    }

    /**
     * {@inheritDoc}
     */
    public final function updateContext(ContextInterface $ctx = null) {
        $this->_context = $ctx;

        return $this;
    }
}
