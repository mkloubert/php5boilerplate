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
    /**
     * @var array
     */
    private static $_vars = array();


    private function __construct() {
    }


    /**
     * Gets the singleton app instance.
     *
     * @return \php5bp\Application The app instance.
     */
    public static function app() {
        if (is_null(static::$_app)) {
            // initialize
            static::$_app = new \php5bp\Application();
        }

        return static::$_app;
    }

    /**
     * Gets the global application config.
     *
     * @return array The configuration or (null) if not found.
     */
    public static function appConf() {
        return static::conf('app');
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

        if (is_null(static::$_cache)) {
            $cacheConf = static::conf('cache.' . $name);
            if (!is_array($cacheConf)) {
                $cacheConf = array();
            }

            static::$_cache = \Zend\Cache\StorageFactory::factory($cacheConf);
        }

        return static::$_cache;
    }

    /**
     * Removes all variables.
     */
    public static function clearVars() {
        static::$_vars = array();
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

            $file = Enumerable::create(static::$_configExtensions)
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
     * Creates an array that stores the data for an exception result.
     *
     * @param Exception $ex The occurred exception.
     *
     * @return array The data array.
     */
    public static function createExceptionData(Exception $ex) {
        $result        = array();
        $result['msg'] = $ex->getMessage();

        if (static::isDebug()) {
            $result['code'] = $ex->getCode();

            $inner = $ex->getPrevious();
            if ($inner instanceof Exception) {
                $result['inner'] = static::createExceptionData($inner);
            }
        }

        return $result;
    }

    /**
     * Creates an array with all basic data for an exception result.
     *
     * @param Exception $ex The occurred exception.
     *
     * @return array The data array.
     */
    public static function createExceptionResult(Exception $ex) {
        return array(
            'code' => -1,
            'data' => static::createExceptionData($ex),
            'msg'  => 'Exception',
        );
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

        $dbConf = static::conf('db.' . $name);
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

        return static::isNullOrEmpty($expr) ||
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
        return static::formatArray($format,
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
     * Gets the value of a variable.
     *
     * @param string $name The name of the variable.
     * @param mixed $defaultValue The value to return if $name was not found.
     * @param bool $found The variable where to write if value was found or not.
     *
     * @return mixed The value.
     */
    public static function getVar($name, $defaultValue = null, &$found = null) {
        $name = static::normalizeVarName($name);

        $found = false;
        if (array_key_exists($name, static::$_vars)) {
            $found = true;
            return static::$_vars[$name];
        }

        return $defaultValue;
    }

    /**
     * Checks if a variable exists.
     *
     * @param string $name The name of the variable.
     *
     * @return bool Variable exists or not.
     */
    public static function hasVar($name) {
        static::getVar($name, null, $result);
        return $result;
    }

    /**
     * Gets if the application runs in debug mode or not.
     *
     * @return bool Runs in debug mode or not.
     */
    public static function isDebug() {
        $appConf = static::appConf();
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
        if (is_null(static::$_logger)) {
            $newLogger = new \php5bp\Diagnostics\Log\Logger();
            $newLogger->addWriter(new \Zend\Log\Writer\Noop());

            if (static::isDebug()) {
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

            static::$_logger = $newLogger;
        }

        return static::$_logger;
    }

    /**
     * Normalizes a variable name.
     *
     * @param string $name The input value.
     *
     * @return string The output value.
     */
    protected function normalizeVarName($name) {
        return \trim($name);
    }

    /**
     * Gets the current time.
     *
     * @return DateTime The current time.
     */
    public static function now() {
        if (is_null(static::$_now)) {
            static::$_now = new \DateTime();

            if (array_key_exists('REQUEST_TIME', $_SERVER)) {
                static::$_now->setTimestamp($_SERVER['REQUEST_TIME']);
            }
        }

        // create copy
        $result = new \DateTime();
        $result->setTimestamp(static::$_now->getTimestamp());

        return $result;
    }

    /**
     * Gets the name of the output encoding.
     *
     * @return string The output encoding.
     */
    public static function outputEncoding() {
        return iconv_get_encoding('output_encoding');
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
     * Sets a variable.
     *
     * @param string $name The name of the variable.
     * @param mixed $value The value for the variable.
     */
    public static function setVar($name, $value) {
        $name = static::normalizeVarName($name);

        static::$_vars[$name] = $value;
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

    /**
     * Returns the list of all vars.
     *
     * @return array The list of vars.
     */
    public static function vars() {
        return static::$_vars;
    }
}
