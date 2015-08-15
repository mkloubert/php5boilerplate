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

namespace php5bp\IO\Files;


/**
 * A basic local file.
 *
 * @package php5bp\IO\Files
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
abstract class LocalFileBase extends FileBase {
    /**
     * @var string
     */
    protected $_path;


    /**
     * Initializes a new instance of that class.
     *
     * @param string $path The path to the local file.
     */
    public function __construct($path) {
        $this->_path = $path;
    }


    /**
     * {@inheritDoc}
     */
    public function content() {
        return \file_get_contents($this->path());
    }

    /**
     * {@inheritDoc}
     */
    public function extension() {
        return \pathinfo($this->name(), \PATHINFO_EXTENSION);
    }

    /**
     * {@inheritDoc}
     */
    public function mime() {
        return \php5bp::getMimeByFilename($this->name());
    }

    /**
     * {@inheritDoc}
     */
    protected function moveToInner($dest) {
        if (@\rename($this->_path, $dest)) {
            $this->_path = \realpath($dest);
            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function name() {
        return \basename($this->path());
    }

    /**
     * {@inheritDoc}
     */
    public function path() {
        return \realpath($this->_path);
    }

    /**
     * {@inheritDoc}
     */
    public function size() {
        return \filesize($this->path());
    }
}
