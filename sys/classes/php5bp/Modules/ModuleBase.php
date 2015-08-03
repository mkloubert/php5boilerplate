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
     * Name of the default view.
     */
    const DEFAULT_VIEW = 'main';
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

            $actionName = null;
            foreach ($allowedActionVarSources as $avs) {
                $found = false;

                switch (\trim(\strtolower($avs))) {
                    case '':
                    case 'request':
                        $actionName = $execCtx->request()->request($actionVar, $actionName, $found);
                        break;

                    case 'post':
                        $actionName = $execCtx->request()->post($actionVar, $actionName, $found);
                        break;

                    case 'get':
                        $actionName = $execCtx->request()->get($actionVar, $actionName, $found);
                        break;

                    case 'vars':
                        $actionName = $execCtx->getVar($actionVar, $actionName, $found);
                        break;

                    default:
                        //TODO throw exception
                        break;
                }

                if ($found) {
                    break;
                }
            }

            $initialView = static::DEFAULT_VIEW;

            if (\array_key_exists('views', $appConf)) {
                if (\array_key_exists('default', $appConf['views'])) {
                    $initialView = $appConf['views']['default'];
                }
            }

            $execCtx->setView($initialView);

            $execCtx->setAction($actionName);
            $execCtx->setVar('module', $this);

            $wasExecuted = null;
            $exception = null;

            try {
                if (false !== $this->beforeExecute($execCtx)) {
                    $wasExecuted = true;
                    $invokeDefault = false;

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

                            if (!empty($actionMethod)) {
                                $actionMethodArgs = array($execCtx);

                                $actionArgs = null;
                                if (\array_key_exists('args', $actionEntry)) {
                                    if (\is_array($actionEntry['args'])) {
                                        $actionArgs = $actionEntry['args'];
                                    }
                                    else {
                                        $actionArgs = array();

                                        Enumerable::create(\explode(static::LIST_SEPARATOR, $actionEntry['args']))
                                                  ->each(function ($x) use (&$actionArgs) {
                                                             $x = \trim($x);
                                                             if ('' == $x) {
                                                                 return;
                                                             }

                                                             $newEntry = array();
                                                             $newEntry['name'] = $x;

                                                             $actionArgs[] = $newEntry;
                                                         });
                                    }
                                }

                                if (\is_null($actionArgs)) {
                                    // set default
                                    $actionArgs = array();
                                }

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

                                    $argValue = null;
                                    foreach ($argSources as $as) {
                                        $foundArgValue = false;

                                        switch (\trim(strtolower($as))) {
                                            case '':
                                            case 'vars':
                                                $argValue = $execCtx->getVar($argName, $argValue, $foundArgValue);
                                                break;

                                            case 'post':
                                                $argValue = $execCtx->request()->post($argName, $argValue, $foundArgValue);
                                                break;

                                            case 'request':
                                                $argValue = $execCtx->request()->request($argName, $argValue, $foundArgValue);
                                                break;

                                            case 'get':
                                                $argValue = $execCtx->request()->get($argName, $argValue, $foundArgValue);
                                                break;

                                            default:
                                                //TODO: throw exception
                                                break;
                                        }

                                        if ($foundArgValue) {
                                            // nothing more to do
                                            break;
                                        }
                                    }

                                    // transform value
                                    foreach ($argTransformers as $at) {
                                        $argValue = \call_user_func($at,
                                                                    $argValue);
                                    }

                                    $actionMethodArgs[] = $argValue;
                                }

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
                        $result = $this->execute($execCtx);
                    }
                }
                else {
                    $wasExecuted = false;
                }
            }
            catch (\Exception $ex) {
                $exception = $ex;

                throw $ex;
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

                // overwrite/fill with values from execution content
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

    public final function updateContext(ContextInterface $ctx = null) {
        $this->_context = $ctx;

        return $ctx;
    }
}
