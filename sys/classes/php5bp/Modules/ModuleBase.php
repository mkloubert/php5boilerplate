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
     * Name of the generic action method.
     */
    const GENERIC_ACTION_METHOD = '__call';
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
     * Returns the default HTTP response code.
     *
     * @param ModuleExecutionContext $ctx The underlying execution context.
     *
     * @return int The response code.
     */
    protected function getDefaultHttpResponseCode(ModuleExecutionContext $ctx) {
        return 200;
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

                case 'provider':
                case 'func':
                case 'function':
                case 'method':
                case 'callable':
                    $funcRes = \call_user_func_array($var,
                                                     array(&$found, $ctx, $result));
                    if ($found) {
                        $result = $funcRes;
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

            if (isset($meta['config'])) {
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
                    if (isset($appConf['modules']['actions']['source'])) {
                        $allowedActionVarSources = $appConf['modules']['actions']['source'];
                    }

                    if (isset($appConf['modules']['actions']['var'])) {
                        $actionVar = $appConf['modules']['actions']['var'];
                    }
                }
            }

            // get module default settings from module meta
            if (isset($meta['module'])) {
                if (isset($meta['module']['actions'])) {
                    if (isset($meta['module']['actions']['source'])) {
                        $allowedActionVarSources = $meta['module']['actions']['source'];
                    }

                    if (isset($meta['module']['actions']['var'])) {
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
                $allowedActionVarSources = array('request');
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

            if (isset($meta['mode'])) {
                $executionMode = $meta['mode'];
            }

            if (isset($meta['view'])) {
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
                        if (isset($meta['actions'])) {
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

                            if (isset($actionEntry['method'])) {
                                $actionMethod = $actionEntry['method'];
                            }

                            // action specific MODE
                            if (isset($actionEntry['mode'])) {
                                $executionMode = $actionEntry['mode'];
                            }

                            // pack args
                            if (isset($actionEntry['packArgs'])) {
                                $packAdditionalActionArgs = $actionEntry['packArgs'];
                            }

                            // action specific VIEW
                            if (isset($actionEntry['view'])) {
                                $initialView = $actionEntry['view'];
                            }

                            $actionMethod = \trim($actionMethod);
                            if ('' === $actionMethod) {
                                $actionMethod = $actionName;
                            }

                            $actionMethodArgs = array($execCtx);
                            $this->prepareInitialExecutionMethodArgs($execCtx, $actionMethodArgs);

                            $actionArgs = null;
                            if (isset($actionEntry['args'])) {
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

                                if (isset($aa['name'])) {
                                    $argName = $aa['name'];
                                }

                                if (isset($aa['source'])) {
                                    $argSources = $aa['source'];
                                }

                                if (isset($aa['transformer'])) {
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
                                    $argSources = array('vars', 'request');
                                }

                                if (null === $argTransformers) {
                                    // set default
                                    $argTransformers = array();
                                }

                                // argument value
                                $argValue = $this->getVarFromSource($execCtx,
                                                                    $argSources, $argName, 'vars');

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

                            if (!\method_exists($this, $actionMethod)) {
                                if (\method_exists($this, static::GENERIC_ACTION_METHOD)) {
                                    // use magic method

                                    $actionMethodArgs = array($actionMethod, $actionMethodArgs);
                                    $actionMethod     = static::GENERIC_ACTION_METHOD;
                                }
                            }

                            // execute
                            $result = \call_user_func_array(array($this, $actionMethod),
                                                            $actionMethodArgs);
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
                    \header($hn . ': ' . \strval($hv), true);
                }
            }

            $buffer = \ob_get_contents();
            if (!\php5bp::isNullOrEmpty($buffer)) {
                $result = $buffer . $result;
            }

            $viewName = \trim($execCtx->getView());
            if ('' != $viewName) {
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
