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


    public abstract function content();

    public abstract function extension();

    public abstract function mime();

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

    public abstract function name();

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
        if ('' == $name) {
            $pi = \pathinfo($this->name());

            $name = $pi['filename'];
        }

        $extension = \trim($extension);
        if ('' == $extension) {
            $extension = $this->suggestedExtension();
        }

        return true;
    }

    public abstract function size();

    public function suggestedExtension() {
        switch ($this->mime()) {
            case 'application/json':
                return 'json';

            case 'application/pdf':
                return 'pdf';

            case 'image/gif':
                return 'gif';

            case 'image/jpeg':
            case 'image/jpg':
                return 'jpg';

            case 'image/png':
                return 'png';

            case 'text/plain':
                return 'txt';

            case 'text/xml':
                return 'xml';
        }

        return $this->extension();
    }
}
