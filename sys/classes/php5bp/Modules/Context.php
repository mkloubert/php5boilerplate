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
 * A module context.
 *
 * @package php5bp\Modules
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class Context extends \php5bp\Object implements ContextInterface {
    /**
     * @var string
     */
    public $Dir;
    /**
     * @var array
     */
    public $Meta;
    /**
     * @var ModuleInterface
     */
    public $Module;
    /**
     * @var string
     */
    public $Name;


    /**
     * {@inheritDoc}
     */
    public function dir() {
        return $this->Dir;
    }

    /**
     * {@inheritDoc}
     */
    public function meta() {
        return $this->Meta;
    }

    /**
     * {@inheritDoc}
     */
    public function module() {
        return $this->Module;
    }

    /**
     * {@inheritDoc}
     */
    public function name() {
        return $this->Name;
    }
}
