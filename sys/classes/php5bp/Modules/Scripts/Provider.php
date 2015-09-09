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


namespace php5bp\Modules\Scripts;

use \php5bp\Modules\ContextInterface as ModuleContextInterface;


/**
 * An common object that provides a script for a module.
 *
 * @package php5bp\Modules
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class Provider implements ProviderInterface {
    /**
     * The (default) name of the script.
     */
    const SCRIPT_NAME = 'index.php';


    /**
     * {@inheritDoc}
     */
    public function getScriptName(ModuleContextInterface $ctx) {
        return static::SCRIPT_NAME;
    }
}
