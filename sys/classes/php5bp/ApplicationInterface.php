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

namespace php5bp;


/**
 * Describes an application handler.
 *
 * @package php5bp
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
interface ApplicationInterface extends ObjectInterface {
    /**
     * Disposes the application and all its resources.
     */
    function dispose();

    /**
     * Handles an exception.
     *
     * @param \Exception $ex The exception to handle.
     *
     * @return bool Was handled or not.
     */
    function handleException(\Exception $ex);

    /**
     * Initializes the application.
     *
     * @throws \Exception Initialization failed.
     */
    function initialize();

    /**
     * Gets or sets if shutdown should be processed or not.
     *
     * @return bool Process shutdown or not.
     */
    function processShutdown();

    /**
     * Runs the application.
     *
     * @return bool Operation was successful or not.
     *
     * @throws \Exception An error occurred.
     */
    function run();
}
