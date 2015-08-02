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

namespace php5bp\Modules;


/**
 * Describes a module.
 *
 * @package php5bp\Modules
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
interface ModuleInterface extends \php5bp\ObjectInterface {
    /**
     * Gets the current context.
     *
     * @return ContextInterface The current context.
     */
    function context();

    /**
     * Executes the module.
     *
     * @return mixed The rendered content.
     */
    function render();

    /**
     * Updates the current context.
     *
     * @param ContextInterface $ctx The new context.
     *
     * @return $this
     */
    function updateContext(ContextInterface $ctx = null);
}
