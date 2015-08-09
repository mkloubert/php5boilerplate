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
 * Describes a file.
 *
 * @package php5bp\IO\Files
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
interface FileInterface extends \php5bp\ObjectInterface {
    /**
     * Gets the content of the uploaded file.
     *
     * @return string The content of the file or (false) if content could not be loaded.
     */
    function content();

    /**
     * Gets the current extension of the file.
     *
     * @return string The current extension.
     */
    function extension();

    /**
     * Gets the MIME type.
     *
     * @return string The MIME type.
     */
    function mime();

    /**
     * Moves that file to a specific (local) folder.
     *
     * @param string $folder The path of the folder.
     * @param string $name The custom name WITHOUT the extension.
     * @param string $extension The custom extension to use.
     *
     * @return string The full (new) path or (false) if an error occured.
     */
    function moveTo($folder, $name = null, $extension = null);

    /**
     * Gets the name of the file.
     *
     * @return string The name of the file.
     */
    function name();

    /**
     * Gets the full path of that file.
     *
     * @return string The path.
     */
    function path();

    /**
     * Gets the size in bytes.
     *
     * @return int The size in bytes or (null) if size is not available.
     *             (false) indicates that an error occurred.
     */
    function size();

    /**
     * Gets the suggested file extension based on the MIME type.
     *
     * @return string The suggested file extension.
     */
    function suggestedExtension();
}
