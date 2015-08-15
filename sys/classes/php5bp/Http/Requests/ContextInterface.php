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

use \php5bp\IO\Files\UploadedFileInterface;
use \System\Collections\IEnumerable;


/**
 * Describes a HTTP request context.
 *
 * @package php5bp\Http\Requests
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
interface ContextInterface extends \php5bp\ObjectInterface {
    /**
     * Returns a cookie value.
     *
     * @param string $name The name of the value.
     * @param mixed $defaultValue The default value if $name was not found.
     * @param bool &$found The variable where to write if $name was found or not.
     *
     * @return mixed The value.
     */
    function cookie($name, $defaultValue = null, &$found = null);

    /**
     * Gets if the client has send DNT header or not.
     *
     * @return bool Client send 1 (true) or 0 (false).
     *              Otherwise (null) is returned.
     */
    function doNotTrack();

    /**
     * Returns an environment variable.
     *
     * @param string $name The name of the variable.
     * @param mixed $defaultValue The default value if $name was not found.
     * @param bool &$found The variable where to write if $name was found or not.
     *
     * @return mixed The value.
     */
    function env($name, $defaultValue = null, &$found = null);

    /**
     * Returns an uploaded file.
     *
     * @param string $name The (field) name of the file.
     *
     * @return UploadedFileInterface The file or (null) if not found.
     */
    function file($name);

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
     * Checks if a cookie is defined or not.
     *
     * @param string $name The name of the cookie.
     *
     * @return bool Cookie was found or not.
     */
    function hasCookie($name);

    /**
     * Checks if a file is defined or not.
     *
     * @param string $name The (field) name of the file.
     *
     * @return bool File was found or not.
     */
    function hasFile($name);

    /**
     * Checks if a GET variable is defined or not.
     *
     * @param string $name The name of the variable.
     *
     * @return bool Variable was found or not.
     */
    function hasGet($name);

    /**
     * Checks if a request header is defined or not.
     *
     * @param string $name The name of the header.
     *
     * @return bool Header was found or not.
     */
    function hasHeader($name);

    /**
     * Checks if a POST variable is defined or not.
     *
     * @param string $name The name of the variable.
     *
     * @return bool Variable was found or not.
     */
    function hasPost($name);

    /**
     * Checks if a REQUEST variable is defined or not.
     *
     * @param string $name The name of the variable.
     *
     * @return bool Variable was found or not.
     */
    function hasRequest($name);

    /**
     * Returns a HTTP request header value.
     *
     * @param string $name The name of the header value.
     * @param mixed $defaultValue The default value if $name was not found.
     * @param bool &$found The variable where to write if $name was found or not.
     *
     * @return mixed The value.
     */
    function header($name, $defaultValue = null, &$found = null);

    /**
     * Checks if the requesting client is Chrome compatible or not.
     *
     * @return bool Is Chrome or not.
     */
    function isChrome();

    /**
     * Checks if the requesting client is Microsoft Edge compatible or not.
     *
     * @return bool Is Edge or not.
     */
    function isEdge();

    /**
     * Checks if the requesting client is a Facebook crawler or not.
     *
     * @return bool Is a Facebook crawler or not.
     */
    function isFacebook();

    /**
     * Checks if the requesting client is Firefox compatible or not.
     *
     * @return bool Is Firefox or not.
     */
    function isFirefox();

    /**
     * Checks if the requesting client is a mobile device or not.
     *
     * @return bool Is mobile device or not.
     */
    function isMobile();

    /**
     * Checks if the requesting client is Apple Safari compatible or not.
     *
     * @return bool Is Safari or not.
     */
    function isSafari();

    /**
     * Checks if the requesting client is a search engine bot/crawler or not.
     *
     * @return bool Is search engine or not.
     */
    function isSearchEngine();

    /**
     * Checks if the requesting client is Trident compatible (Internet Explorer) or not.
     *
     * @return bool Is Trident or not.
     */
    function isTrident();

    /**
     * Checks if the requesting client is a Twitter crawler or not.
     *
     * @return bool Is a Twitter crawler or not.
     */
    function isTwitter();

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

    /**
     * Returns a server variable.
     *
     * @param string $name The name of the variable.
     * @param mixed $defaultValue The default value if $name was not found.
     * @param bool &$found The variable where to write if $name was found or not.
     *
     * @return mixed The value.
     */
    function server($name, $defaultValue = null, &$found = null);

    /**
     * Returns a session variable.
     *
     * @param string $name The name of the variable.
     * @param mixed $defaultValue The default value if $name was not found.
     * @param bool &$found The variable where to write if $name was found or not.
     *
     * @return mixed The value.
     */
    function session($name, $defaultValue = null, &$found = null);

    /**
     * Returns the list of uploaded files.
     *
     * @return IEnumerable The list of UploadedFileInterface instances.
     */
    function uploadedFiles();
}
