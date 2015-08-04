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

use \php5bp\Modules\Meta\ProviderInterface as ModuleMetaProviderInterface;
use \System\Linq\Enumerable;


/**
 * The application.
 *
 * @package php5bp
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class Application extends Object {
    /**
     * Default class of a meta provider.
     */
    const DEFAULT_MODULE_META_PROVIDER = '\php5bp\Modules\Meta\Provider';
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
     * Expression for module name separator.
     */
    const MODULE_NAME_SEPARATOR = '/';
    /**
     * The name of a module script file.
     */
    const MODULE_SCRIPT_FILENAME = 'index.php';


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
     * @return ModuleMetaProviderInterface
     */
    protected function getModuleMetaProvider() {
        $providerClass = null;

        $appConf = \php5bp::appConf();

        // custom provider?
        if (\array_key_exists('modules', $appConf)) {
            // $appConf['modules']['meta']
            if (\array_key_exists('meta', $appConf['modules'])) {
                // $appConf['modules']['meta']['provider']
                if (\array_key_exists('provider', $appConf['modules']['meta'])) {
                    $providerClass = $appConf['modules']['meta']['provider'];
                }
            }
        }

        $providerClass = \trim($providerClass);
        if ('' == $providerClass) {
            $providerClass = static::DEFAULT_MODULE_META_PROVIDER;
        }

        $prc = new \ReflectionClass($providerClass);
        return $prc->newInstance();
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
        $chars = Enumerable::create(\trim($moduleName));

        $moduleName = '';
        foreach ($chars as $c) {
            // a-z
            // A-Z
            // 0-9
            // _
            // /
            $appendChar = ((\ord($c) >= \ord('a')) && (\ord($c) <= \ord('z'))) ||
                          ((\ord($c) >= \ord('A')) && (\ord($c) <= \ord('Z'))) ||
                          ((\ord($c) >= \ord('0')) && (\ord($c) <= \ord('9'))) ||
                          ($c == '_') ||
                          ($c == static::MODULE_NAME_SEPARATOR);

            if ($appendChar) {
                $moduleName .= $c;
            }
        }

        // normalize duplicate separator chars
        while (false !== \strpos($moduleName, static::MODULE_NAME_SEPARATOR . static::MODULE_NAME_SEPARATOR)) {
            $moduleName = \str_replace(static::MODULE_NAME_SEPARATOR . static::MODULE_NAME_SEPARATOR,
                                       static::MODULE_NAME_SEPARATOR,
                                       $moduleName);
        }

        // remove leading separator chars
        while (\php5bp::startsWith($moduleName, static::MODULE_NAME_SEPARATOR)) {
            $moduleName = \trim(\substr($moduleName, 1));
        }

        // remove ending separator chars
        while (\php5bp::endsWith($moduleName, static::MODULE_NAME_SEPARATOR)) {
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
     *
     * @return bool Operation was successful or not.
     *
     * @throws \Exception An error occurred.
     */
    public function run() {
        $appConf = \php5bp::appConf();

        // custom module variable
        $moduleVar = null;
        if (\array_key_exists('modules', $appConf)) {
            // $appConf['modules']['var']
            if (\array_key_exists('var', $appConf['modules'])) {
                $moduleVar = $appConf['modules']['var'];
            }
        }

        $moduleVar = \trim($moduleVar);
        if ('' == $moduleVar) {
            // set default
            $moduleVar = 'module';
        }

        $moduleName = null;
        if (\array_key_exists($moduleVar, $_GET)) {
            $moduleName = $_GET[$moduleVar];
        }

        $moduleName = static::normalizeModuleName($moduleName);
        if ('' == $moduleName) {
            $defaultModuleName = null;

            // try get custom default module
            if (\array_key_exists('modules', $appConf)) {
                // $appConf['modules']['default']
                if (\array_key_exists('default', $appConf['modules'])) {
                    $defaultModuleName = $appConf['modules']['default'];
                }
            }

            $defaultModuleName = static::normalizeModuleName($defaultModuleName);  // custom default?
            if ('' == $defaultModuleName) {
                // use system default
                $defaultModuleName = static::DEFAULT_MODULE_NAME;
            }

            $moduleName = $defaultModuleName;
        }

        $found = false;

        $modulePath = \realpath(\PHP5BP_DIR_MODULES .
                                \str_replace(static::MODULE_NAME_SEPARATOR, \DIRECTORY_SEPARATOR, $moduleName));
        if (false !== $modulePath) {
            $moduleScriptPath = \realpath($modulePath . \DIRECTORY_SEPARATOR . static::MODULE_SCRIPT_FILENAME);
            if (false !== $moduleScriptPath) {
                $moduleMeta = $this->getModuleMetaProvider()
                                   ->getModuleMetaByName($moduleName);

                if (!\is_array($moduleMeta)) {
                    // set default
                    $moduleMeta = array();
                }

                // module class defined?
                $moduleClass = null;
                if (\array_key_exists('class', $moduleMeta)) {
                    $moduleClass = $moduleMeta['class'];
                }

                //TODO: read constructor arguments from meta file
                $moduleClass = \trim($moduleClass);
                $moduleConstructorArgs = array();

                require_once $moduleScriptPath;

                if ('' != $moduleClass) {
                    if (\class_exists($moduleClass)) {
                        $mc = new \ReflectionClass($moduleClass);

                        $module = $mc->newInstanceArgs($moduleConstructorArgs);

                        $moduleCtx         = new \php5bp\Modules\Context();
                        $moduleCtx->Dir    = $modulePath;
                        $moduleCtx->Meta   = $moduleMeta;
                        $moduleCtx->Module = $module;
                        $moduleCtx->Name   = $moduleName;

                        $renderMethod = '';
                        $updateContextMethod = '';

                        // try get custom methods from app config
                        if (\array_key_exists('modules', $appConf)) {
                            // $appConf['modules']['methods']
                            if (\array_key_exists('methods', $appConf['modules'])) {
                                // $appConf['modules']['methods']['render']
                                if (\array_key_exists('render', $appConf['modules']['methods'])) {
                                    $renderMethod = $appConf['modules']['methods']['render'];
                                }

                                // $appConf['modules']['methods']['updateContext']
                                if (\array_key_exists('updateContext', $appConf['modules']['methods'])) {
                                    $updateContextMethod = $appConf['modules']['methods']['updateContext'];
                                }
                            }
                        }

                        // try get custom methods from meta data
                        if (\array_key_exists('methods', $moduleMeta)) {
                            // $moduleMeta['methods']['render']
                            if (\array_key_exists('render', $moduleMeta['methods'])) {
                                $renderMethod = $moduleMeta['methods']['render'];
                            }

                            // $moduleMeta['methods']['updateContext']
                            if (\array_key_exists('updateContext', $moduleMeta['methods'])) {
                                $updateContextMethod = $moduleMeta['methods']['updateContext'];
                            }
                        }

                        $renderMethod = \trim($renderMethod);
                        if ('' == $renderMethod) {
                            // set default
                            $renderMethod = static::DEFAULT_MODULE_METHOD_RENDER;
                        }

                        $updateContextMethod = \trim($updateContextMethod);
                        if ('' != $updateContextMethod) {
                            // default
                            $updateContextMethod = static::DEFAULT_MODULE_METHOD_UPDATECONTEXT;
                        }

                        // update module context
                        if (\method_exists($module, $updateContextMethod)) {
                            \call_user_func(array($module, $updateContextMethod),
                                            $moduleCtx);
                        }

                        // execute and render
                        if (\method_exists($module, $renderMethod)) {
                            $found = true;

                            $result = \call_user_func(array($module, $renderMethod),
                                                      $moduleCtx);

                            if (!$result instanceof \Exception) {
                                echo $result;
                            }
                            else {
                                // rethrow
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
