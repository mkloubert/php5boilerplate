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

namespace php5bp\Http\Responses;


/**
 * Describes a HTTP response context.
 *
 * @package php5bp\Http\Responses
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
interface ContextInterface extends \php5bp\ObjectInterface {
    /**
     * Removes all headers.
     *
     * @return $this
     */
    function clearHeaders();

    /**
     * Gets the HTTP reponse code.
     *
     * @return int The code or (null) to define that the system should select the response code.
     */
    function getCode();

    /**
     * Returns the value of a HTTP header.
     *
     * @param string $name The name of the header.
     * @param mixed $defaultValue The default value if $name was not found.
     * @param bool $found The variable that stores if value was found or not.
     *
     * @return mixed The value.
     */
    function getHeader($name, $defaultValue = null, &$found = null);

    /**
     * Checks if a HTTP header is defined.
     *
     * @param string $name The name of the header.
     *
     * @return mixed Is defined or not.
     */
    function hasHeader($name);

    /**
     * Returns all headers.
     *
     * @return array The headers.
     */
    function headers();

    /**
     * Sets the HTTP response code.
     *
     * @param int $code The new code or (null) to define that the system should
     *                  select the response code automatically.
     *
     * @return $this
     */
    function setCode($code);

    /**
     * Sets a HTTP header.
     *
     * @param string $name The name of the header.
     * @param string $value The (new) value.
     *
     * @return $this
     */
    function setHeader($name, $value);

    /**
     * Removes a HTTP header.
     *
     * @param string $name The name of the header.
     *
     * @return $this
     */
    function unsetHeader($name);
}
