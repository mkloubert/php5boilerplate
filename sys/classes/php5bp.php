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

use \System\Linq\Enumerable;


/**
 * Provides global stuff.
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
final class php5bp {
    /**
     * @var \php5bp\Application
     */
    private static $_app;
    /**
     * @var array
     */
    private static $_configExtensions = array(
        'json' => '\Zend\Config\Reader\Json',
        'php'  => '\php5bp\Config\Reader\PhpArray',
        'xml'  => '\Zend\Config\Reader\Xml',
        'ini'  => '\Zend\Config\Reader\Ini',
    );
    /**
     * @var DateTime
     */
    private static $_now;


    private function __construct() {
    }


    /**
     * Gets the singleton app instance.
     *
     * @return \php5bp\Application The app instance.
     */
    public static function app() {
        if (is_null(self::$_app)) {
            // initialize
            self::$_app = new \php5bp\Application();
        }

        return self::$_app;
    }

    /**
     * Gets the global application config.
     *
     * @return array The configuration or (null) if not found.
     */
    public static function appConf() {
        return self::conf('app');
    }

    /**
     * Reads configuration data from a storage (file).
     *
     * @param string $name The name of the config storage.
     *                     . characters in the name are replaced by directory separator.
     * @param string $confDir The custom config root directory.
     *
     * @return array The loaded data or (null) if config storage does not exist.
     *               (false) indicates that $name is invalid.
     *
     * @throws \Exception Error while loading.
     */
    public static function conf($name, $confDir = null) {
        $name = trim($name);
        if ('' == $name) {
            return false;
        }

        $confDir = \trim($confDir);
        if ('' == $confDir) {
            $confDir = PHP5BP_DIR_CONFIG;
        }
        else {
            $confDir = realpath($confDir);
            if (false !== $confDir) {
                $confDir .= DIRECTORY_SEPARATOR;
            }
        }

        if (false !== $confDir) {
            // . characters in the name are replaced
            // by directory separator
            $filePrefix = $confDir . str_replace('.', DIRECTORY_SEPARATOR, $name) . '.';

            $file = Enumerable::create(self::$_configExtensions)
                              ->select(function($x, $ctx) use ($filePrefix) {
                                           $result        = new stdClass();
                                           $result->class = $x;
                                           $result->path  = realpath($filePrefix . $ctx->key);

                                           return $result;
                                       })
                              ->singleOrDefault(function(stdClass $x) {
                                                    return false !== $x->path;
                                                }, false);

            if (false !== $file) {
                $cls = new \ReflectionClass($file->class);

                $reader = $cls->newInstance();
                return $reader->fromFile($file->path);
            }
        }

        // file not found
        return null;
    }

    /**
     * Checks if a string ends with a specific expression.
     *
     * @param string $str The string to search in.
     * @param string $expr The expression to search for.
     * @param bool $ignoreCase Ignore case or not.
     *
     * @return bool Ends with expression or not.
     */
    public static function endsWith($str, $expr, $ignoreCase = false) {
        $func = !$ignoreCase ? 'strpos' : 'stripos';

        return self::isNullOrEmpty($expr) ||
               (($temp = strlen($str) - strlen($expr)) >= 0 &&
                call_user_func($func,
                               $str, $expr, $temp) !== false);
    }

    /**
     * Checks if a string is (null) or empty.
     *
     * @param string $str The string to check.
     *
     * @return bool Is (null) or empty; otherwise (false).
     */
    public static function isNullOrEmpty($str) {
        return is_null($str) ||
               ('' == strval($str));
    }

    /**
     * Gets the current time.
     *
     * @return DateTime The current time.
     */
    public static function now() {
        if (is_null(self::$_now)) {
            self::$_now = new \DateTime();

            if (array_key_exists('REQUEST_TIME', $_SERVER)) {
                self::$_now->setTimestamp($_SERVER['REQUEST_TIME']);
            }
        }

        // create copy
        $result = new \DateTime();
        $result->setTimestamp(self::$_now->getTimestamp());

        return $result;
    }

    /**
     * Checks if a string starts with a specific expression.
     *
     * @param string $str The string to search in.
     * @param string $expr The expression to search for.
     * @param bool $ignoreCase Ignore case or not.
     *
     * @return bool Starts with expression or not.
     */
    public static function startsWith($str, $expr, $ignoreCase = false) {
        $func = !$ignoreCase ? 'strpos' : 'stripos';

        return 0 === call_user_func($func,
                                    $str, $expr);
    }
}
