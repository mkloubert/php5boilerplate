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
 * A HTTP response context.
 *
 * @package php5bp\Http\Responses
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class Context extends \php5bp\Object implements ContextInterface {
    /**
     * The name of the header for the MIME type of the output.
     */
    const HEADER_CONTENT_TYPE = 'Content-type';


    /**
     * @var int
     */
    protected $_code;
    /**
     * @var array
     */
    protected $_headers;


    /**
     * Initializes a new instance of that class.
     */
    public function __construct() {
        $this->clearHeaders();
    }


    /**
     * {@inheritDoc}
     */
    public function clearHeaders() {
        $this->_headers = array();
    }

    /**
     * {@inheritDoc}
     */
    public function getCode() {
        return $this->_code;
    }

    /**
     * {@inheritDoc}
     */
    public function getContentType() {
        return $this->getHeader(static::HEADER_CONTENT_TYPE);
    }

    /**
     * {@inheritDoc}
     */
    public function getHeader($name, $defaultValue = null, &$found = null) {
        $name = static::normalizeHeaderName($name);

        $found = false;
        if (\array_key_exists($name, $this->_headers)) {
            $found = true;
            return $this->_headers[$name];
        }

        return $defaultValue;
    }

    /**
     * {@inheritDoc}
     */
    public function hasHeader($name) {
        $this->getHeader($name, null, $result);
        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function headers() {
        return $this->_headers;
    }

    /**
     * Normalizes the name of a HTTP header.
     *
     * @param string $name The input value.
     *
     * @return string The output value.
     */
    public static function normalizeHeaderName($name) {
        return \ucwords(\trim($name));
    }

    /**
     * {@inheritDoc}
     */
    public function setCode($code) {
        $code = \trim($code);

        $this->_code = \is_numeric($code) ? \intval($code) : null;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setContentType($mime) {
        $mime = \trim(\strtolower($mime));
        if ('' !== $mime) {
            $this->setHeader(static::HEADER_CONTENT_TYPE, $mime);
        }
        else {
            $this->unsetHeader(static::HEADER_CONTENT_TYPE);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setHeader($name, $value) {
        $name = static::normalizeHeaderName($name);
        if ('' === $name) {
            return $this->setCode($value);
        }

        $this->_headers[$name] = $value;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function unsetHeader($name) {
        $name = static::normalizeHeaderName($name);
        unset($this->_headers[$name]);

        return $this;
    }
}
