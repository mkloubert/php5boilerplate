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
     * @var \Zend\Cache\Storage\StorageInterface
     */
    private static $_cache;
    /**
     * @var array
     */
    private static $_configExtensions = array(
        'json' => '\Zend\Config\Reader\Json',
        'php'  => '\php5bp\Config\Reader\PhpArray',
        'xml'  => '\Zend\Config\Reader\Xml',
        'ini'  => '\Zend\Config\Reader\Ini',
        'yml'  => '\Zend\Config\Reader\Yaml',
        'yaml'  => '\Zend\Config\Reader\Yaml',
        'properties'  => '\Zend\Config\Reader\JavaProperties',
    );
    private static $_logger;
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
     * Returns a (new) cache adapter.
     *
     * @param string $name The name of the configuration storage.
     *
     * @return \Zend\Cache\Storage\StorageInterface The adapter or (false) if input data is invalid.
     */
    public static function cache($name = 'main') {
        $name = trim($name);
        if ('' == $name) {
            return false;
        }

        if (is_null(self::$_cache)) {
            $cacheConf = self::conf('cache.' . $name);
            if (!is_array($cacheConf)) {
                $cacheConf = array();
            }

            self::$_cache = \Zend\Cache\StorageFactory::factory($cacheConf);
        }

        return self::$_cache;
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
     * Returns a new database adapter.
     *
     * @param string $name The name of the configuration storage.
     *
     * @return \php5bp\Db\Adapter The new connection.
     *                            (false) indicates that name is invalid.
     */
    public static function db($name = 'main') {
        $name = trim($name);
        if ('' == $name) {
            return false;
        }

        $dbConf = self::conf('db.' . $name);
        if (!is_array($dbConf)) {
            $dbConf = array();
        }

        return new \php5bp\Db\Adapter($dbConf);
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
     * Formats a string.
     *
     * @param string $format The format string.
     * @param mixed ...$arg One or more argument for $format.
     *
     * @return string The formatted string.
     */
    public static function format($format) {
        return self::formatArray($format,
                                 array_slice(func_get_args(), 1));
    }

    /**
     * Formats a string.
     *
     * @param string $format The format string.
     * @param \Traversable|array $args The arguments for $format.
     *
     * @return string The formatted string.
     */
    public static function formatArray($format, $args = null) {
        if (is_null($args)) {
            $args = array();
        }

        if (!is_array($args)) {
            $args = iterator_to_array($args);
        }

        return preg_replace_callback('/{(\d+)(\:[^}]*)?}/i',
                                     function($match) use ($args) {
                                         $i = intval($match[1]);

                                         $format = null;
                                         if (array_key_exists(2, $match)) {
                                             $format = substr($match[2], 1);
                                         }

                                         return array_key_exists($i, $args) ? strval(php5bp::parseFormatStringValue($format, $args[$i]))
                                                                            : $match[0];
                                     }, $format);
    }

    /**
     * Gets if the application runs in debug mode or not.
     *
     * @return bool Runs in debug mode or not.
     */
    public static function isDebug() {
        $appConf = self::appConf();
        if (array_key_exists('debug', $appConf)) {
            return boolval($appConf['debug']);
        }

        return false;
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
     * Gets the logger.
     *
     * @return \php5bp\Diagnostics\Log\Logger The logger.
     */
    public static function log() {
        if (is_null(self::$_logger)) {
            $newLogger = new \php5bp\Diagnostics\Log\Logger();
            $newLogger->addWriter(new \Zend\Log\Writer\Noop());

            if (self::isDebug()) {
                if (isset($_SERVER['HTTP_USER_AGENT'])) {
                    if (false !== stripos($_SERVER['HTTP_USER_AGENT'], 'firefox')) {
                        // FirePHP
                        $newLogger->addWriter(new \Zend\Log\Writer\FirePhp());
                    }

                    if (false !== stripos($_SERVER['HTTP_USER_AGENT'], 'chrome')) {
                        // ChromePHP
                        $newLogger->addWriter(new \Zend\Log\Writer\ChromePhp());
                    }
                }
            }

            self::$_logger = $newLogger;
        }

        return self::$_logger;
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
     * Formats a value for a formatted string.
     *
     * @param string $format The format string for $value.
     * @param mixed $value The value to parse.
     *
     * @return mixed The parsed value.
     */
    public static function parseFormatStringValue($format, $value) {
        if (!is_null($format)) {
            $handled = true;

            if ($value instanceof DateTime) {
                $value = $value->format($format);
            }
            else {
                $handled = false;
            }

            if (!$handled) {
                // default
                $value = sprintf($format, $value);
            }
        }

        return $value;
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

    /**
     * @see \php5bp\Db\TableGateway
     */
    public static function table($table, $adapter = null, $features = null, \Zend\Db\ResultSet\ResultSetInterface $resultSetPrototype = null, \Zend\Db\Sql\Sql $sql = null) {
        return new \php5bp\Db\TableGateway($table, $adapter, $features, $resultSetPrototype, $sql);
    }
}
