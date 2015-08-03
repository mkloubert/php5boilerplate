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

namespace php5bp\Views;


/**
 * Extension of \Zend\View\Renderer\PhpRenderer class.
 *
 * @package php5bp\Views
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class PhpRenderer extends \Zend\View\Renderer\PhpRenderer {
    /**
     * @var string
     */
    protected $_dir;


    /**
     * Initializes a new instance of that class.
     *
     * @param string $dir
     * @param array $config
     */
    public function __construct($dir, $config = array()) {
        $this->_dir = \realpath($dir) . DIRECTORY_SEPARATOR;

        parent::__construct($config);
    }


    /**
     * Gets the root directory.
     *
     * @return string The root directory.
     */
    public function dir() {
        return $this->_dir;
    }
}
