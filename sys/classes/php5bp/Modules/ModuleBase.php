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

            $getVarFromSource = function($sources, $var, $defaultSrc) use ($execCtx) {
                $defaultSrc = \trim(\strtolower($defaultSrc));

                $result = null;

                foreach ($sources as $src) {
                    $src = \trim(\strtolower($src));
                    if ('' == $src) {
                        $src = $defaultSrc;
                    }

                    $found = false;

                    switch ($src) {
                        case 'vars':
                            $result = $execCtx->getVar($var, $result, $found);
                            break;

                        case 'request':
                            $result = $execCtx->request()->request($var, $result, $found);
                            break;

                        case 'post':
                            $result = $execCtx->request()->post($var, $result, $found);
                            break;

                        case 'get':
                            $result = $execCtx->request()->get($var, $result, $found);
                            break;

                        case 'files':
                            $result = $execCtx->request()->file($var);
                            if ($result instanceof UploadedFileInterface) {
                                $found = true;
                            }
                            break;

                        case 'session':
                            $result = $execCtx->request()->session($var, $result, $found);
                            break;

                        case 'cookies':
                            $result = $execCtx->request()->cookie($var, $result, $found);
                            break;

                        case 'headers':
                            $result = $execCtx->request()->header($var, $result, $found);
                            break;

                        case 'environment':
                            $result = $execCtx->request()->env($var, $result, $found);
                            break;

                        case 'server':
                            $result = $execCtx->request()->server($var, $result, $found);
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
            };

            if (\array_key_exists('config', $meta)) {
                $execCtx->Config = $meta['config'];
            }

            if (\is_null($execCtx->Config)) {
                $execCtx->Config = array();
            }

            if (!\is_array($execCtx->Config)) {
                $execCtx->Config = array($execCtx->Config);
            }

            $allowedActionVarSources = null;

            // get module default settings from app config
            if (\array_key_exists('modules', $appConf)) {
                // $appConf['modules']['actions']
                if (\array_key_exists('actions', $appConf['modules'])) {
                    // $appConf['modules']['actions']['source']
                    if (\array_key_exists('source', $appConf['modules']['actions'])) {
                        $allowedActionVarSource = $appConf['modules']['actions']['source'];
                    }

                    // $appConf['modules']['actions']['var']
                    if (\array_key_exists('var', $appConf['modules']['actions'])) {
                        $actionVar = $appConf['modules']['actions']['var'];
                    }
                }
            }

            // get module default settings from module meta
            if (\array_key_exists('module', $meta)) {
                // $meta['module']['actions']
                if (\array_key_exists('actions', $meta['module'])) {
                    // $meta['module']['actions']['source']
                    if (\array_key_exists('source', $meta['module']['actions'])) {
                        $allowedActionVarSource = $meta['module']['actions']['source'];
                    }

                    // $meta['module']['actions']['var']
                    if (\array_key_exists('var', $meta['module']['actions'])) {
                        $actionVar = $meta['module']['actions']['var'];
                    }
                }
            }

            $actionVar = \trim($actionVar);
            if ('' == $actionVar) {
                $actionVar = static::DEFAULT_VAR_NAME_ACTION;
            }

            if (\is_null($allowedActionVarSources)) {
                // set default
                $allowedActionVarSources = array('request');
            }

            if (!\is_array($allowedActionVarSource)) {
                $allowedActionVarSources = \explode(static::LIST_SEPARATOR, $allowedActionVarSources);
            }

            // action name
            $actionName = \call_user_func($getVarFromSource,
                                          $allowedActionVarSources, $actionVar, 'request');

            // do not change default view
            $initialView = false;

            $execCtx->setAction($actionName);
            $execCtx->setDefaultView();
            $execCtx->setVar('module', $this);

            $wasExecuted = null;
            $exception = null;

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
                    if ('' == $actionName) {
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
                                                        ->distinct(function($x, $y) {
                                                                       return \strtolower($x) == \strtolower($y);
                                                                   })
                                                        ->toArray(function($key, $value) {
                                                                      return $value;
                                                                  });
                            }

                            $actionEntry = Enumerable::create($actionList)
                                                     ->singleOrDefault(function($x, $ctx) use ($actionName) {
                                                                           $key = \is_array($x) ? $ctx->key : $x;

                                                                           return \trim(\strtolower($key)) == $actionName;
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
                            if ('' != $actionMethod) {
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
                                                                             if ('' == $x) {
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

                                if (\is_null($actionArgs)) {
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

                                    $argName = \trim($argName);
                                    if ('' == $argName) {
                                        //TODO: throw exception
                                        continue;
                                    }

                                    if (!\is_null($argSources)) {
                                        if (!\is_array($argSources)) {
                                            $argSources = \explode(static::LIST_SEPARATOR, $argSources);
                                        }
                                    }

                                    if (!\is_null($argTransformers)) {
                                        if (!\is_array($argTransformers)) {
                                            $argTransformers = \explode(static::LIST_SEPARATOR, $argTransformers);
                                        }
                                    }

                                    if (\is_null($argSources)) {
                                        // set default
                                        $argSources = array('vars', 'request');
                                    }

                                    if (\is_null($argTransformers)) {
                                        // set default
                                        $argTransformers = array();
                                    }

                                    // argument value
                                    $argValue = \call_user_func($getVarFromSource,
                                                                $argSources, $argName, 'vars');

                                    // transform value
                                    foreach ($argTransformers as $at) {
                                        $argValue = \call_user_func($at,
                                                                    $argValue);
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

                                if (\is_null($actionMethodArgs)) {
                                    $actionMethodArgs = array();
                                }

                                // execute
                                $result = \call_user_func_array(array($this, $actionMethod),
                                                                $actionMethodArgs);
                            }
                            else {
                                // use default
                                $invokeDefault = true;
                            }
                        }
                        else {
                            //TODO throw exception
                        }
                    }

                    if ($invokeDefault) {
                        \call_user_func($setupExecutionContext);

                        $result = $this->execute($execCtx);
                    }

                    if (!\is_null($methodResult)) {
                        // custom result defined

                        if (\is_null($result)) {
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

                if (\is_null($exceptionHandler)) {
                    throw $ex;
                }
                else {
                    \call_user_func($exceptionHandler,
                                    $ex, $execCtx);
                }
            }
            finally {
                $this->afterExecution($execCtx, $wasExecuted, $exception);

                // response code
                {
                    $respCode = $execCtx->response()->getCode();
                    if (\is_null($respCode)) {
                        $respCode = $this->getDefaultHttpResponseCode($execCtx);
                    }

                    if (!\is_null($respCode)) {
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
