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

namespace php5bp;


/**
 * The application.
 *
 * @package php5bp
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class Application extends Object {
    /**
     * The name of the default render method of a module object.
     */
    const DEFAULT_MODULE_METHOD_RENDER = 'render';
    /**
     * The name of the default update context method of a module object.
     */
    const DEFAULT_MODULE_METHOD_UPDATECONTEXT = 'updateContext';
    /**
     * The name of the default module.
     */
    const DEFAULT_MODULE_NAME = 'index';


    /**
     * @var bool
     */
    protected $_processShutdown = true;


    /**
     * Disposes the application and all its resources.
     */
    public function dispose() {
    }

    /**
     * Handles an exception.
     *
     * @param \Exception $ex The exception to handle.
     *
     * @return bool Was handled or not.
     */
    public function handleException(\Exception $ex) {
        return false;
    }

    /**
     * Initializes the application.
     *
     * @throws \Exception Initialization failed.
     */
    public function initialize() {
    }

    /**
     * Parses the name of a module.
     *
     * @param string $moduleName The input value.
     *
     * @return string The parsed value.
     */
    protected static function normalizeModuleName($moduleName) {
        $moduleName = \trim($moduleName);
        $moduleName = \str_replace(' ' , '_', $moduleName);
        $moduleName = \str_replace('\\', '/', $moduleName);
        $moduleName = \str_replace('.' , '' , $moduleName);

        // normalize duplicate / chars
        while (false !== \strpos($moduleName, '//')) {
            $moduleName = \str_replace('//', '/', $moduleName);
        }

        // remove leading / chars
        while (\php5bp::startsWith($moduleName, '/')) {
            $moduleName = \trim(\substr($moduleName, 1));
        }

        // remove ending / chars
        while (\php5bp::endsWith($moduleName, '/')) {
            $moduleName = \trim(\substr($moduleName, 0, \strlen($moduleName) - 1));
        }

        return $moduleName;
    }

    /**
     * Gets or sets if shutdown should be processed or not.
     *
     * @return bool Process shutdown or not.
     */
    public function processShutdown() {
        if (\func_num_args() > 0) {
            $this->_processShutdown = \boolval(\func_get_arg(0));
        }

        return $this->_processShutdown;
    }

    /**
     * Runs the application.
     */
    public function run() {
        $appConf = \php5bp::appConf();

        $moduleVar = null;
        if (\array_key_exists('modules', $appConf)) {
            if (\array_key_exists('var', $appConf['modules'])) {
                $moduleVar = $appConf['modules']['var'];
            }
        }

        $moduleVar = \trim($moduleVar);
        if ('' == $moduleVar) {
            $moduleVar = 'module';
        }

        $moduleName = null;
        if (\array_key_exists($moduleVar, $_REQUEST)) {
            $moduleName = $_REQUEST[$moduleVar];
        }

        $moduleName = \trim(static::normalizeModuleName($moduleName));
        if ('' == $moduleName) {
            $defaultModuleName = null;
            if (\array_key_exists('modules', $appConf)) {
                if (\array_key_exists('default', $appConf['modules'])) {
                    $defaultModuleName = $appConf['modules']['default'];
                }
            }

            $defaultModuleName = \trim(static::normalizeModuleName($defaultModuleName));  // custom default?
            if ('' == $defaultModuleName) {
                // no, you system default
                $defaultModuleName = static::DEFAULT_MODULE_NAME;
            }

            $moduleName = $defaultModuleName;
        }

        $found = false;

        $modulePath = \realpath(\PHP5BP_DIR_MODULES .
                                \str_replace('/', \DIRECTORY_SEPARATOR, $moduleName));
        if (false !== $modulePath) {
            $moduleScriptPath = \realpath($modulePath . \DIRECTORY_SEPARATOR . 'index.php');
            if (false !== $moduleScriptPath) {
                $moduleMeta = \php5bp::conf('meta', $modulePath);

                if (!\is_array($moduleMeta)) {
                    $moduleMeta = array();
                }

                $moduleClass = null;
                if (\array_key_exists('class', $moduleMeta)) {
                    $moduleClass = \trim($moduleMeta['class']);
                }

                $moduleClass = \trim($moduleClass);

                require_once $moduleScriptPath;

                if ('' != $moduleClass) {
                    if (\class_exists($moduleClass)) {
                        $mc = new \ReflectionClass($moduleClass);

                        //TODO: read from meta file
                        $moduleConstructorArgs = array();

                        $module = $mc->newInstanceArgs($moduleConstructorArgs);

                        $moduleCtx         = new \php5bp\Modules\Context();
                        $moduleCtx->Meta   = $moduleMeta;
                        $moduleCtx->Module = $module;
                        $moduleCtx->Name   = $moduleName;

                        $renderMethod = '';
                        $updateContextMethod = '';

                        // try get custom methods from app config
                        if (\array_key_exists('modules', $appConf)) {
                            if (\array_key_exists('methods', $appConf['modules'])) {
                                if (\array_key_exists('render', $appConf['modules']['methods'])) {
                                    $renderMethod = $appConf['modules']['methods']['render'];
                                }

                                if (\array_key_exists('updateContext', $appConf['modules']['methods'])) {
                                    $updateContextMethod = $appConf['modules']['methods']['updateContext'];
                                }
                            }
                        }

                        // try get custom methods from meta data
                        if (\array_key_exists('methods', $moduleMeta)) {
                            if (\array_key_exists('render', $moduleMeta['methods'])) {
                                $renderMethod = $moduleMeta['methods']['render'];
                            }

                            if (\array_key_exists('updateContext', $moduleMeta['methods'])) {
                                $updateContextMethod = $moduleMeta['methods']['updateContext'];
                            }
                        }

                        $renderMethod = \trim($renderMethod);
                        if ('' == $renderMethod) {
                            $renderMethod = 'render';
                        }

                        $updateContextMethod = \trim($updateContextMethod);
                        if ('' == $updateContextMethod) {
                            $updateContextMethod = 'updateContext';
                        }

                        // update module context
                        if (\method_exists($module, $updateContextMethod)) {
                            \call_user_func(array($module, $updateContextMethod),
                                            $moduleCtx);
                        }

                        // execute and render
                        if (\method_exists($module, $renderMethod)) {
                            $found = true;

                            $result = \call_user_func(array($module, $renderMethod));

                            if (!$result instanceof \Exception) {
                                echo $result;
                            }
                            else {
                                throw $result;
                            }
                        }
                    }
                }
                else {
                    // simple script execution
                    $found = true;
                }
            }
        }

        if (!$found) {
            \header(':', true, 404);
            return false;
        }

        return true;
    }
}
