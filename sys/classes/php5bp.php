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

use \System\ClrString;
use \System\Linq\Enumerable;


/**
 * Provides global stuff.
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
final class php5bp {
    /**
     * Default name of the application class.
     */
    const DEFAULT_APPLICATION_CLASS = '\php5bp\Application';
    /**
     * Name of a default config (sub)storage.
     */
    const DEFAULT_CONFIG_NAME = 'main';
    /**
     * List separator expression.
     */
    const LIST_SEPARATOR = ';';


    /**
     * @var \php5bp\ApplicationInterface
     */
    private static $_app;
    /**
     * @var array
     */
    private static $_cache = array();
    /**
     * @var array
     */
    private static $_configCache = array();
    /**
     * @var array
     */
    private static $_configExtensions = array(
        'json' => \Zend\Config\Reader\Json::class,
        'php'  => \php5bp\Config\Reader\PhpArray::class,
        'xml'  => \Zend\Config\Reader\Xml::class,
        'ini'  => \Zend\Config\Reader\Ini::class,
        'yml'  => \Zend\Config\Reader\Yaml::class,
        'yaml'  => \Zend\Config\Reader\Yaml::class,
        'properties'  => \Zend\Config\Reader\JavaProperties::class,
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
     * @return \php5bp\ApplicationInterface The app instance.
     */
    public static function app() {
        if (null === static::$_app) {
            $className = '';

            $appConf = static::appConf();

            if (isset($appConf['class'])) {
                $className = $appConf['class'];
            }

            $className = trim($className);
            if ('' == $className) {
                // use default class
                $className = static::DEFAULT_APPLICATION_CLASS;
            }

            // create instance
            $rc = new ReflectionClass($className);
            static::$_app = $rc->newInstance();
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
    public static function cache($name = self::DEFAULT_CONFIG_NAME) {
        $name = trim($name);
        if ('' === $name) {
            return false;
        }

        if (array_key_exists($name, static::$_cache)) {
            $cacheConf = static::conf('cache.' . $name);
            if (!is_array($cacheConf)) {
                $cacheConf = array();
            }

            static::$_cache[$name] = \Zend\Cache\StorageFactory::factory($cacheConf);
        }

        return static::$_cache[$name];
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
     * @throws Exception Error while loading.
     */
    public static function conf($name, $confDir = null) {
        $name = trim($name);
        if ('' == $name) {
            return false;
        }

        $confDir = trim($confDir);
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
                $cacheKey = $file->path;

                if (!array_key_exists($cacheKey, static::$_configCache)) {
                    $cls = new ReflectionClass($file->class);

                    $reader = $cls->newInstance();
                    static::$_configCache[$cacheKey] = $reader->fromFile($file->path);
                }

                return static::$_configCache[$cacheKey];
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
     * Returns the global crypter.
     *
     * @param bool $setKey The key from configuration or not.
     *
     * @return \Zend\Crypt\BlockCipher The created instance or (false) if an error occurred.
     */
    public static function crypter($nameOrSetKey = self::DEFAULT_CONFIG_NAME, $setKey = true) {
        if (1 == func_num_args()) {
            if (is_bool($nameOrSetKey)) {
                // swap values

                $setKey       = $nameOrSetKey;
                $nameOrSetKey = self::DEFAULT_CONFIG_NAME;
            }
        }

        $adapter = null;
        $options = null;

        if (false === static::getEncryptionData($nameOrSetKey, $key)) {
            return false;
        }

        $conf = static::config('crypt.' . $nameOrSetKey);

        if (is_array($conf)) {
            if (isset($conf['crypter'])) {
                if (isset($conf['crypter']['adapter'])) {
                    $adapter = $conf['crypter']['adapter'];
                }

                if (isset($conf['crypter']['options'])) {
                    $options = $conf['crypter']['options'];
                }
            }
        }

        $adapter = trim(strtolower($adapter));
        if ('' === $adapter) {
            $adapter = 'mcrypt';

            if (null === $options) {
                $options = ['algo' => 'aes'];
            }
        }

        $result = \Zend\Crypt\BlockCipher::factory($adapter, $options);

        if ($setKey) {
            if (null !== $key) {
                $result->setKey($key);
            }
        }

        return $result;
    }

    /**
     * Returns a new database adapter.
     *
     * @param string $name The name of the configuration storage.
     *
     * @return \php5bp\Db\Adapter The new connection.
     *                            (false) indicates that name is invalid.
     */
    public static function db($name = self::DEFAULT_CONFIG_NAME) {
        $name = trim($name);
        if ('' === $name) {
            return false;
        }

        $dbConf = static::conf('db.' . $name);
        if (!is_array($dbConf)) {
            $dbConf = array();
        }

        return new \php5bp\Db\Adapter($dbConf);
    }

    /**
     * Decrypts a string.
     *
     * @param string $encStr The string to decrypt.
     * @param string|bool $nameOrIsBase64 The name of the configuration storage.
     *                                    If only 2 arguments are submitted and that value is a boolean
     *                                    it is handled as value for $isBase64 and is set to default.
     * @param bool $isBase64 $str is Base64 encoded or not.
     *
     * @return string The decrypted string or (false) if an error occurred.
     */
    public static function decrypt($encStr, $nameOrIsBase64 = self::DEFAULT_CONFIG_NAME, $isBase64 = true) {
        if (2 == func_num_args()) {
            if (is_bool($nameOrIsBase64)) {
                // swap values

                $isBase64       = $nameOrIsBase64;
                $nameOrIsBase64 = self::DEFAULT_CONFIG_NAME;
            }
        }

        $crypter = static::crypter($nameOrIsBase64, false);
        if (false === $crypter) {
            return false;
        }

        static::getEncryptionData($nameOrIsBase64, $key, $prefixSize, $suffixSize);

        $crypter->setKey($key);

        if ($isBase64) {
            $encStr = \trim($encStr);
            if ('' !== $encStr) {
                $encStr = base64_decode($encStr);
            }
            else {
                $encStr = null;
            }
        }

        $str = $crypter->decrypt($encStr);
        return substr($str,
                      $prefixSize,
                      strlen($str) - $prefixSize - $suffixSize);
    }

    /**
     * Encrypts a string.
     *
     * @param string $str The string to encrypt.
     * @param string|bool $nameOrReturnBase64 The name of the configuration storage.
     *                                        If only 2 arguments are submitted and that value is a boolean
     *                                        it is handled as value for $returnBase64 and is set to default.
     * @param bool $returnBase64 Return encrypted data Base64 encoded or not.
     *
     * @return string The encrypted string or (false) if an error occurred.
     */
    public static function encrypt($str, $nameOrReturnBase64 = self::DEFAULT_CONFIG_NAME, $returnBase64 = true) {
        if (2 == func_num_args()) {
            if (is_bool($nameOrReturnBase64)) {
                // swap values

                $returnBase64       = $nameOrReturnBase64;
                $nameOrReturnBase64 = self::DEFAULT_CONFIG_NAME;
            }
        }

        $crypter = static::crypter($nameOrReturnBase64, false);
        if (false === $crypter) {
            return false;
        }

        static::getEncryptionData($nameOrReturnBase64, $key, $prefixSize, $suffixSize, $saltChars);

        $crypter->setKey($key);

        if (static::isNullOrEmpty($saltChars)) {
            $saltChars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        }

        $result = $crypter->encrypt(static::randChars($prefixSize, $saltChars) .
                                    strval($str) .
                                    static::randChars($suffixSize, $saltChars));

        return $returnBase64 ? base64_encode($result)
                             : $result;
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
        $func = !$ignoreCase ? '\\strpos' : '\\stripos';

        return static::isNullOrEmpty($expr) ||
               (($temp = strlen($str) - strlen($expr)) >= 0 &&
                $func($str, $expr, $temp) !== false);
    }

    /**
     * @see ClrString::format()
     */
    public static function format($format) {
        return call_user_func_array(array(ClrString::class, "format"),
                                    func_get_args());
    }

    /**
     * @see ClrString::formatArray()
     */
    public static function formatArray($format, $args = null) {
        return ClrString::formatArray($format, $args);
    }

    /**
     * Returns all data for global encryption / decryption.
     *
     * @param string &$key The variable where to write the crypter key to.
     * @param int &$prefixSize The variable where to write the prefix salt size to.
     * @param int &$suffixSize The variable where to write the suffix salt size to.
     * @param string &$saltChars The variable where to write the allowed chars for salting a string.
     *
     * @return bool Is (false) if $name is invalid; otherwise (true).
     */
    protected static function getEncryptionData(
        $name,
        &$key = null,
        &$prefixSize = null,
        &$suffixSize = null,
        &$saltChars = null
    ) {
        $name = \trim($name);
        if ('' === $name) {
            return false;
        }

        $conf = static::conf('crypt.' . $name);

        if (is_array($conf)) {
            if (isset($conf['key'])) {
                $key = base64_decode(trim($conf['key']));
            }

            if (isset($conf['salt'])) {
                if (isset($conf['salt']['chars'])) {
                    $saltChars = $conf['salt']['chars'];
                }

                if (isset($conf['salt']['prefix_size'])) {
                    $prefixSize = $conf['salt']['prefix_size'];
                }

                if (isset($conf['salt']['suffix_size'])) {
                    $suffixSize = $conf['salt']['suffix_size'];
                }
            }
        }

        return true;
    }

    /**
     * Returns a MIME type by filename.
     * The list of supported MIME types is handled in the known.files config storage.
     *
     * @param string $filename The filename.
     *
     * @return string The MIME type or (false) if $filename is invalid.
     */
    public static function getMimeByFilename($filename) {
        $filename = trim(strtolower($filename));
        if ('' == $filename) {
            return false;
        }

        $result = null;

        $lastDot = strrpos($filename, '.');
        if (false !== $lastDot) {
            $ext = trim(substr($filename, $lastDot + 1));

            $files = static::conf('known.files');
            if (is_array($files)) {
                if (isset($files['mime'])) {
                    // find MIME by extension
                    $result = Enumerable::create($files['mime'])
                                        ->select(function($extensions, $ctx) {
                                                     $result             = new stdClass();
                                                     $result->extensions = $extensions;
                                                     $result->mime       = trim(strtolower($ctx->key));

                                                     if ($result->extensions instanceof Traversable) {
                                                         $result->extensions = iterator_to_array($result->extensions);
                                                     }

                                                     if (!is_array($result->extensions)) {
                                                         // keep sure to have an array
                                                         $result->extensions = array($result->extensions);
                                                     }

                                                     // normalize list
                                                     $result->extensions = Enumerable::create($result->extensions)
                                                                                     ->select(function($x) {
                                                                                                  return trim(strtolower($x));
                                                                                              })
                                                                                     ->where(function($x) {
                                                                                                 return '' != $x;
                                                                                             })
                                                                                     ->toArray();

                                                     return $result;
                                                 })
                                        ->where(function($x) use ($ext) {
                                                    return in_array($ext, $x->extensions);
                                                })
                                        ->select(function($x) {
                                                     return $x->mime;
                                                 })
                                        ->firstOrDefault();
                }
            }
        }

        if (null === $result) {
            // use default
            $result = 'application/octet-stream';
        }

        return trim(strtolower($result));
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
     * Hashes a string.
     *
     * @param string $str The string to hash.
     * @param string|bool $nameOrRawOutput The name of the configuration to use.
     *                                     If only 2 arguments are submitted and this value is a boolean,
     *                                     it will be used as value for $rawOutput by setting the configuration
     *                                     storage name to default.
     * @param bool $rawOutput Return as raw binary data or as hex string.
     *
     * @return string The hash or (false) if an error occurred.
     */
    public static function hash($str, $nameOrRawOutput = self::DEFAULT_CONFIG_NAME, $rawOutput = false) {
        if (2 == func_num_args()) {
            if (is_bool($nameOrRawOutput)) {
                // use $nameOrRawOutput for $rawOutput

                $rawOutput       = $nameOrRawOutput;
                $nameOrRawOutput = self::DEFAULT_CONFIG_NAME;
            }
        }

        $nameOrRawOutput = trim($nameOrRawOutput);
        if ('' === $nameOrRawOutput) {
            return false;
        }

        $algo       = null;
        $prefixSalt = null;
        $suffixSalt = null;

        $conf = static::conf('hash.' . $nameOrRawOutput);
        if (!is_array($conf)) {
            $conf = array();
        }

        if (isset($conf['algo'])) {
            $algo = $conf['algo'];
        }

        $prefix = null;
        $suffix = null;

        if (isset($conf['salt'])) {
            if (isset($conf['salt']['prefix'])) {
                $prefix = $conf['salt']['prefix'];
                if (null !== $prefix) {
                    if (!is_array($prefix)) {
                        $prefixValue = $prefix;

                        $prefix = array(
                            'provider' => function() use ($prefixValue) {
                                return $prefixValue;
                            },
                        );
                    }
                }
            }

            if (isset($conf['salt']['suffix'])) {
                $suffix = $conf['salt']['suffix'];
                if (null !== $suffix) {
                    if (!is_array($suffix)) {
                        $suffixValue = $suffix;

                        $suffix = array(
                            'provider' => function() use ($suffixValue) {
                                return $suffixValue;
                            },
                        );
                    }
                }
            }
        }

        if (null !== $prefix) {
            if (isset($prefix['provider'])) {
                $prefixSalt = $prefix['provider']($nameOrRawOutput);
            }
        }

        if (null !== $suffix) {
            if (isset($suffix['provider'])) {
                $suffixSalt = $suffix['provider']($nameOrRawOutput);
            }
        }

        $algo = trim(strtolower($algo));
        if ('' === $algo) {
            $algo = 'md5';
        }

        $transformers = null;
        if (isset($conf['transformer'])) {
            $transformers = $conf['transformer'];
        }

        if (null !== $transformers) {
            if (!\is_array($transformers)) {
                $transformers = \explode(static::LIST_SEPARATOR, $transformers);
            }

            foreach ($transformers as $t) {
                $str = $t($str);
            }
        }

        return hash($algo,
                    $prefixSalt . $str . $suffixSalt,
                    $rawOutput);
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
     * Creates and opens a new temp file and invokes a function for it.
     * The temp file will be deleted after $func has been invoked.
     *
     * @param callable $func The function to invoke.
     * @param string $prefix The prefix to use.
     * @param bool &$success The variable where to write down if file could be created/opened or not.
     *
     * @return mixed The result of $func.
     */
    public static function invokeForTempFile(callable $func, $prefix = '', &$success = null) {
        $res = static::openTempFile($prefix, $tempFile);

        $success = false;
        if (is_resource($res)) {
            try {
                $success = true;

                return call_user_func($func,
                                      $res, $tempFile);
            }
            finally {
                if (fclose($res)) {
                    unlink($tempFile);
                }
            }
        }
    }

    /**
     * Gets if the application runs in debug mode or not.
     *
     * @return bool Runs in debug mode or not.
     */
    public static function isDebug() {
        $appConf = static::appConf();
        if (isset($appConf['debug'])) {
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
        return ClrString::isNullOrEmpty($str);
    }

    /**
     * Gets the logger.
     *
     * @return \php5bp\Diagnostics\Log\Logger The logger.
     */
    public static function log() {
        if (null === static::$_logger) {
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
        return trim($name);
    }

    /**
     * Gets the current time.
     *
     * @return DateTimeInterface The current time.
     */
    public static function now() {
        if (null === static::$_now) {
            static::$_now = new DateTime();

            if (isset($_SERVER['REQUEST_TIME'])) {
                static::$_now->setTimestamp($_SERVER['REQUEST_TIME']);
            }
        }

        // create copy
        $result = new DateTime();
        $result->setTimestamp(static::$_now->getTimestamp());

        return $result;
    }

    /**
     * Creates and opens a new temp file.
     *
     * @param string $prefix The prefix to use.
     * @param string &$tempFile The variable where to write the path of the new temp file.
     *                          Is (false) on error.
     *
     * @return resource The resource of the temp file or (false) if an error occurred.
     */
    public static function openTempFile($prefix = '', &$tempFile = null) {
        $tempFile = static::tempFile($prefix);
        if (false === $tempFile) {
            return false;
        }

        return fopen($tempFile, 'a+');
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
     * Returns a random string.
     *
     * @param int $count The number of characters.
     * @param string $chars The chars to use.
     *
     * @return string The random string.
     */
    public static function randChars($count, $chars = 'abcdefghijklmnopqrstuvxyzABCDEFGHIJKLMNOPQRSTUVXYZ0123456789') {
        return Enumerable::buildRandom($count)
                         ->select(function($x) use ($chars) {
                                      return $chars[$x % strlen($chars)];
                                  })
                         ->concatToString();
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
        $func = !$ignoreCase ? '\\strpos' : '\\stripos';

        return 0 === $func($str, $expr);
    }

    /**
     * @see \php5bp\Db\TableGateway
     */
    public static function table($table, $adapter = null, $features = null, \Zend\Db\ResultSet\ResultSetInterface $resultSetPrototype = null, \Zend\Db\Sql\Sql $sql = null) {
        return new \php5bp\Db\TableGateway($table, $adapter, $features, $resultSetPrototype, $sql);
    }

    /**
     * Gets the full path of the directory for the temporary files.
     *
     * @return string The path or (false) on error.
     */
    public static function tempDir() {
        $result = null;

        $appConf = static::appConf();

        // custom directory?
        if (isset($appConf['dirs'])) {
            if (isset($appConf['dirs']['temp'])) {
                $result = $appConf['dirs']['temp'];
            }
        }

        $result = trim($result);
        if ('' === $result) {
            $result = sys_get_temp_dir();
        }

        $result = realpath($result);

        if (false !== $result) {
            $result .= DIRECTORY_SEPARATOR;
        }

        return $result;
    }

    /**
     * Creates a new temp file.
     *
     * @param string $prefix The prefix to use.
     *
     * @return string The full path of the file or (false) on error.
     */
    public static function tempFile($prefix = '') {
        $tempDir = static::tempDir();
        if (false === $tempDir) {
            return false;
        }

        return tempnam($tempDir, trim($prefix));
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
