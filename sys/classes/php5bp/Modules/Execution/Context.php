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

namespace php5bp\Modules\Execution;
use \System\Linq\Enumerable;


/**
 * A module execution context.
 *
 * @package php5bp\Modules\Execution
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class Context extends \php5bp\Object implements ContextInterface {
    /**
     * The name of the variable for storing the action name.
     */
    const VAR_NAME_ACTION = 'action';
    /**
     * The name of the variable for storing the view name.
     */
    const VAR_NAME_VIEW = 'view';


    /**
     * @var array
     */
    public $Config;
    /**
     * @var \php5bp\Http\Requests\ContextInterface
     */
    public $Request;
    /**
     * @var \php5bp\Http\Responses\ContextInterface
     */
    public $Response;
    /**
     * @var array
     */
    protected $_vars;


    /**
     * Initializes a new instance of that class.
     */
    public function __construct() {
        $this->clearVars();
    }


    public function clearVars() {
        $this->_vars = array();

        return $this;
    }

    public function config() {
        return $this->Config;
    }

    public function getAction() {
        return $this->getVar(static::VAR_NAME_ACTION);
    }

    public function getConfig($name, $defaultValue = null, &$found = null) {
        $name = static::normalizeConfigName($name);

        $found = false;

        $key = Enumerable::create($this->Config)
                         ->select(function($x, $ctx) {
                                      return Context::normalizeConfigName($ctx->key);
                                  })
                         ->singleOrDefault(function($x) use ($name) {
                                               return $x == $name;
                                           }, false);

        if (false !== $key) {
            $found = true;
            return $this->Config[$key];
        }

        return $defaultValue;
    }

    public function getVar($name, $defaultValue = null, &$found = null) {
        $name = static::normalizeVarName($name);

        $found = \array_key_exists($name, $this->_vars);
        if ($found) {
            return $this->_vars[$name];
        }

        return $defaultValue;
    }

    public function getView() {
        return $this->getVar(static::VAR_NAME_VIEW);
    }

    public function hasConfig($name) {
        $this->getConfig($name, null, $result);
        return $result;
    }

    public function hasVar($name) {
        $this->getVar($name, null, $result);
        return $result;
    }

    /**
     * Parses the name of a config value.
     *
     * @param string $name The input value.
     *
     * @return string The parsed value.
     */
    public static function normalizeConfigName($name) {
        return \trim(strtolower($name));
    }

    /**
     * Parses the name for a variable.
     *
     * @param string $name The input value.
     *
     * @return string The parsed value.
     */
    protected static function normalizeVarName($name) {
        return \trim(strtolower($name));
    }

    public function request() {
        return $this->Request;
    }

    public function response() {
        return $this->Response;
    }

    public function setAction($actionName) {
        $actionName = \trim($actionName);
        if ('' == $actionName) {
            $actionName = null;
        }

        return $this->setVar(static::VAR_NAME_ACTION, $actionName);
    }

    public function setVar($name, $value) {
        $name = static::normalizeVarName($name);

        $this->_vars[$name] = $value;
        return $this;
    }

    public function setView($viewName) {
        return $this->setVar(static::VAR_NAME_VIEW,
                             \trim($viewName));
    }

    public function unsetVar($name) {
        $name = static::normalizeVarName($name);

        unset($this->_vars[$name]);
        return $this;
    }

    public function vars() {
        return $this->_vars;
    }
}
