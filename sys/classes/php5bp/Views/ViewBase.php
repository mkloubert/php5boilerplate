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

namespace php5bp\Views;


/**
 * A basic view.
 *
 * @package php5bp\Views
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
abstract class ViewBase extends \php5bp\Object {
    /**
     * @var array
     */
    protected $_vars = array();


    /**
     * Gets a property value.
     *
     * @param string $name The name of the property.
     *
     * @return mixed The value of the property.
     */
    public function __get($name) {
        return $this->_vars[static::normalizeVarName($name)];
    }

    /**
     * Checks if a property is set.
     *
     * @param string $name The name of the property.
     *
     * @return boolean Is set or not.
     */
    public function __isset($name) {
        return isset($this->_vars[static::normalizeVarName($name)]);
    }

    /**
     * Sets a property value.
     *
     * @param string $name The name of the property.
     */
    public function __set($name, $value) {
        $this->_vars[static::normalizeVarName($name)] = $value;
    }

    /**
     * Unsets a property.
     *
     * @param string $name The name of the property.
     */
    public function __unset($name) {
        unset($this->_vars[static::normalizeVarName($name)]);
    }


    /**
     * Normalizes a variable name.
     *
     * @param string $name The input name.
     *
     * @return string The normalized / parsed name.
     */
    public static function normalizeVarName($name) {
        return \trim($name);
    }

    /**
     * Gets the list of all vars.
     *
     * @return array The vars.
     */
    public function vars() {
        return $this->_vars;
    }
}
