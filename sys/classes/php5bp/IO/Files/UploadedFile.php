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

use \System\Collections\IEnumerable;
use \System\Linq\Enumerable;


/**
 * An uploaded file.
 *
 * @package php5bp\IO\Files
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class UploadedFile extends LocalFileBase implements UploadedFileInterface {
    /**
     * @var string
     */
    protected $_fileEntry;
    /**
     * @var string
     */
    protected $_field;


    /**
     * Creates a new instance of that class.
     *
     * @param string $fieldName The name of the underlying HTML form field.
     * @param array $fileEntry The file entry.
     */
    public function __construct($fieldName, array $fileEntry) {
        $this->_field     = $fieldName;
        $this->_fileEntry = $fileEntry;

        $path = null;
        if (\array_key_exists('tmp_name', $this->_fileEntry)) {
            $path = $this->_fileEntry['tmp_name'];
        }

        parent::__construct($path);
    }


    /**
     * Creates a list of instances from $_FILES super global.
     *
     * @return IEnumerable The created instances.
     */
    public static function create() {
        return Enumerable::create(static::createInner())
                         ->toArray(function($key, UploadedFile $x) {
                                       return $x->field();
                                   });
    }

    /**
     * @see UploadedFile::create()
     */
    protected static function createInner() {
        foreach ($_FILES as $fieldName => $fileEntry) {
            yield new static($fieldName, $fileEntry);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function field() {
        return $this->_field;
    }

    /**
     * {@inheritDoc}
     */
    public function mime() {
        $result = '';

        if (isset($this->_fileEntry['type'])) {
            $result = \trim(\strtolower($this->_fileEntry['type']));
        }

        if ('' === $result) {
            $result = static::MIME_TYPE_DEFAULT;
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    protected function moveToInner($dest) {
        if (@\move_uploaded_file($this->_path, $dest)) {
            $this->_path = \realpath($dest);
            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function name() {
        if (\array_key_exists('name', $this->_fileEntry)) {
            return $this->_fileEntry['name'];
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function size() {
        if (\array_key_exists('size', $this->_fileEntry)) {
            return $this->_fileEntry['size'];
        }

        return null;
    }
}
