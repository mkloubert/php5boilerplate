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

namespace php5bp\Http\Requests;


/**
 * Describes a HTTP request context.
 *
 * @package php5bp\Http\Requests
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
interface ContextInterface extends \php5bp\ObjectInterface {
    /**
     * Returns a GET/query variable.
     *
     * @param string $name The name of the variable.
     * @param mixed $defaultValue The default value if $name was not found.
     * @param bool &$found The variable where to write if $name was found or not.
     *
     * @return mixed The value.
     */
    function get($name, $defaultValue = null, &$found = null);

    /**
     * Returns a POST variable.
     *
     * @param string $name The name of the variable.
     * @param mixed $defaultValue The default value if $name was not found.
     * @param bool &$found The variable where to write if $name was found or not.
     *
     * @return mixed The value.
     */
    function post($name, $defaultValue = null, &$found = null);

    /**
     * Returns a POST or GET/query variable.
     *
     * @param string $name The name of the variable.
     * @param mixed $defaultValue The default value if $name was not found.
     * @param bool &$found The variable where to write if $name was found or not.
     *
     * @return mixed The value.
     */
    function request($name, $defaultValue = null, &$found = null);
}
