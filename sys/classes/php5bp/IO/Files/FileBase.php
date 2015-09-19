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

use \System\Linq\Enumerable;


/**
 * A basic file.
 *
 * @package php5bp\IO\Files
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
abstract class FileBase extends \php5bp\Object implements FileInterface {
    /**
     * The default MIME type.
     */
    const MIME_TYPE_DEFAULT = "application/octet-stream";


    /**
     * {@inheritDoc}
     */
    public abstract function content();

    /**
     * {@inheritDoc}
     */
    public abstract function extension();

    /**
     * {@inheritDoc}
     */
    public abstract function mime();

    /**
     * {@inheritDoc}
     */
    public final function moveTo($folder, $name = null, $extension = null) {
        if (!$this->prepareForMoveTo($folder, $name, $extension)) {
            // invalid input
            return false;
        }

        $dest = \php5bp::format('{0}{1}{2}.{3}',
                                $folder, \DIRECTORY_SEPARATOR,
                                $name, $extension);

        return $this->moveToInner($dest);
    }

    /**
     * @see FileBase::moveTo()
     */
    protected abstract function moveToInner($dest);

    /**
     * {@inheritDoc}
     */
    public abstract function name();

    /**
     * {@inheritDoc}
     */
    public abstract function path();

    /**
     * Tries to prepare input data of FileBase::moveTo() method.
     *
     * @param string $folder The path of the folder.
     * @param string $name The custom name WITHOUT the extension.
     * @param string $extension The custom extension to use.
     *
     * @return bool Operation was successful or not.
     */
    protected function prepareForMoveTo(&$folder, &$name, &$extension) {
        $folder = \realpath($folder);
        if (false === $folder) {
            return false;
        }

        $name = \trim($name);
        if ('' === $name) {
            $pi = \pathinfo($this->name());

            $name = $pi['filename'];
        }

        $extension = \trim($extension);
        if ('' === $extension) {
            $extension = $this->suggestedExtension();
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public abstract function size();

    /**
     * {@inheritDoc}
     */
    public function suggestedExtension() {
        $result = null;

        $files = \php5bp::conf('known.files');
        if (\is_array($files)) {
            if (isset($files['mime'])) {
                $mime = \trim(\strtolower($this->mime()));

                // find extension by MIME type
                $result = Enumerable::create($files['mime'])
                                    ->where(function($x, $ctx) use ($mime) {
                                                return \trim(\strtolower($ctx->key)) === $mime;
                                            })
                                    ->selectMany(function($extensions) {
                                                     if ($extensions instanceof \Traversable) {
                                                         $extensions = \iterator_to_array($extensions);
                                                     }

                                                     if (!\is_array($extensions)) {
                                                         // keep sure to have an array
                                                         $extensions = array($extensions);
                                                     }

                                                     return $extensions;
                                                 })
                                    ->firstOrDefault();
            }
        }

        if (null === $result) {
            // use "real" file extension
            $result = $this->extension();
        }

        return $result;
    }
}
