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

namespace php5bp\Application;


/**
 * Is thrown if the application has NOT been initialized.
 *
 * @package php5bp\Application
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class NotInitializedException extends \php5bp\Exception {
    /**
     * @var \php5bp\Application
     */
    protected $_app;


    /**
     * Initializes a new instance of that class.
     *
     * @param \php5bp\Application $app The underlying application.
     * @param \Exception $innerException The inner exception.
     */
    public function __construct(\php5bp\Application $app, \Exception $innerException) {
        $this->_app = $app;

        parent::__construct($innerException->message,
                            $innerException,
                            $innerException->code);
    }


    /**
     * Gets the underlying application object.
     *
     * @return \php5bp\Application The app object.
     */
    public function app() {
        return $this->_app;
    }
}
