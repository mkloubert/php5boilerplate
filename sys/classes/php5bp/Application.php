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
use \php5bp\Modules\Scripts\ProviderInterface as ModuleScriptProviderInterface;
use \System\Linq\Enumerable;


/**
 * The application.
 *
 * @package php5bp
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class Application extends Object implements ApplicationInterface {
    /**
     * Allowed chars for a module name.
     */
    const ALLOWED_MODULE_NAME_CHARS = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_/';
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
     * @var bool
     */
    protected $_processShutdown = true;


    /**
     * {@inheritDoc}
     */
    public function dispose() {
    }

    /**
     * {@inheritDoc}
     */
    public function handleException(\Exception $ex) {
        return false;
    }

    /**
     * Returns the meta data provider for modules.
     *
     * @return ModuleMetaProviderInterface The provider.
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
        if ('' === $providerClass) {
            $providerClass = static::DEFAULT_MODULE_META_PROVIDER;
        }

        $prc = new \ReflectionClass($providerClass);
        return $prc->newInstance();
    }

    /**
     * Returns the entry script for a module.
     *
     * @param array $meta The meta data of the module.
     *
     * @return ModuleScriptProviderInterface The script provider.
     */
    protected function getModuleScriptProvider(array $meta) {
        $providerClass = null;

        if (\array_key_exists('module', $meta)) {
            if (\array_key_exists('scriptProvider', $meta['module'])) {
                $providerClass = $meta['module']['scriptProvider'];
            }
        }

        $providerClass = \trim($providerClass);
        if ('' === $providerClass) {
            $providerClass = \php5bp\Modules\Scripts\Provider::class;
        }

        $pc = new \ReflectionClass($providerClass);
        return $pc->newInstance();
    }

    /**
     * {@inheritDoc}
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
        $chars     = \trim($moduleName);
        $charCount = \strlen($chars);

        $moduleName = '';
        for ($i = 0; $i < $charCount; $i++) {
            $c = $chars[$i];

            if (false !== \strpos(static::ALLOWED_MODULE_NAME_CHARS, $c)) {
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
     * {@inheritDoc}
     */
    public function processShutdown() {
        if (\func_num_args() > 0) {
            $this->_processShutdown = \boolval(\func_get_arg(0));
        }

        return $this->_processShutdown;
    }

    /**
     * {@inheritDoc}
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
        if ('' === $moduleVar) {
            // set default
            $moduleVar = 'module';
        }

        $moduleName = null;
        if (\array_key_exists($moduleVar, $_GET)) {
            $moduleName = $_GET[$moduleVar];
        }

        $moduleName = static::normalizeModuleName($moduleName);
        if ('' === $moduleName) {
            $defaultModuleName = null;

            // try get custom default module
            if (\array_key_exists('modules', $appConf)) {
                // $appConf['modules']['default']
                if (\array_key_exists('default', $appConf['modules'])) {
                    $defaultModuleName = $appConf['modules']['default'];
                }
            }

            $defaultModuleName = static::normalizeModuleName($defaultModuleName);  // custom default?
            if ('' === $defaultModuleName) {
                // use system default
                $defaultModuleName = static::DEFAULT_MODULE_NAME;
            }

            $moduleName = $defaultModuleName;
        }

        $found = false;

        $modulePath = \realpath(\PHP5BP_DIR_MODULES .
                                \str_replace(static::MODULE_NAME_SEPARATOR, \DIRECTORY_SEPARATOR, $moduleName));
        if (false !== $modulePath) {
            $moduleMeta = $this->getModuleMetaProvider()
                               ->getModuleMetaByName($moduleName);

            if (!\is_array($moduleMeta)) {
                // set default
                $moduleMeta = array();
            }

            $moduleCtx         = new \php5bp\Modules\Context();
            $moduleCtx->Dir    = $modulePath;
            $moduleCtx->Meta   = $moduleMeta;
            $moduleCtx->Name   = $moduleName;

            $moduleScriptPath = \realpath($modulePath . \DIRECTORY_SEPARATOR .
                                          $this->getModuleScriptProvider($moduleMeta)
                                               ->getScriptName($moduleCtx));
            if (false !== $moduleScriptPath) {
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
                        $mrc = new \ReflectionClass($moduleClass);
                        $module = $mrc->newInstanceArgs($moduleConstructorArgs);

                        $moduleCtx->Module = $module;

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
                        if ('' === $renderMethod) {
                            // set default
                            $renderMethod = static::DEFAULT_MODULE_METHOD_RENDER;
                        }

                        $updateContextMethod = \trim($updateContextMethod);
                        if ('' === $updateContextMethod) {
                            // default
                            $updateContextMethod = static::DEFAULT_MODULE_METHOD_UPDATECONTEXT;
                        }

                        // update module context
                        if (\method_exists($module, $updateContextMethod)) {
                            $module->$updateContextMethod($moduleCtx);
                        }

                        // execute and render
                        if (\method_exists($module, $renderMethod)) {
                            $found = true;

                            $result = $module->$renderMethod($moduleCtx);
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
