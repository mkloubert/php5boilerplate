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


/**
 * Describes a module execution context.
 *
 * @package php5bp\Modules\Execution
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
interface ContextInterface extends \php5bp\ObjectInterface {
    /**
     * Removes all vars.
     *
     * @return $this
     */
    function clearVars();

    /**
     * Gets the module configuration.
     *
     * @return array The module configuration.
     */
    function config();

    /**
     * Gets the name of the action to invoke.
     *
     * @return string The name of the action.
     */
    function getAction();

    /**
     * Gets the config value.
     *
     * @param string $name The name of the value.
     * @param mixed $defaultValue The default value if $name does not exist.
     * @param bool &$found The variable where to write if variable was found or not.
     *
     * @return mixed The value.
     */
    function getConfig($name, $defaultValue = null, &$found = null);

    /**
     * Gets the value of a variable.
     *
     * @param string $name The name of the variable.
     * @param mixed $defaultValue The default value if $name does not exist.
     * @param bool &$found The variable where to write if variable was found or not.
     *
     * @return mixed The value.
     */
    function getVar($name, $defaultValue = null, &$found = null);

    /**
     * Gets the name of the view to use.
     *
     * @return string The view.
     */
    function getView();

    /**
     * Checks if a config value exists.
     *
     * @param string $name The name of the value.
     *
     * @return bool Exists or not.
     */
    function hasConfig($name);

    /**
     * Checks if a variable exists.
     *
     * @param string $name The name of the variable.
     *
     * @return bool Exists or not.
     */
    function hasVar($name);

    /**
     * Gets the HTTP request context.
     *
     * @return \php5bp\Http\Requests\ContextInterface The request context.
     */
    function request();

    /**
     * Gets the HTTP response context.
     *
     * @return \php5bp\Http\Responses\ContextInterface The request context.
     */
    function response();

    /**
     * Sets the name of the action to invoke.
     *
     * @param string $actionName The new value.
     *
     * @return $this
     */
    function setAction($actionName);

    /**
     * Sets the value for a variable.
     *
     * @param string $name The name of the variable.
     * @param mixed $value The value for the variable.
     *
     * @return $this
     */
    function setVar($name, $value);

    /**
     * Sets the name of the view to use.
     *
     * @param string $viewName The new value.
     *
     * @return $this.
     */
    function setView($viewName);

    /**
     * Removes a variable.
     *
     * @param string $name The name of the variable.
     *
     * @return $this
     */
    function unsetVar($name);

    /**
     * Returns all vars.
     *
     * @return array The list of vars.
     */
    function vars();
}
