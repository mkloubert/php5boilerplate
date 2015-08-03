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
 * A simple view.
 *
 * @package php5bp\Views
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class SimpleView extends ViewBase {
    /**
     * The name of a template file.
     */
    const TEMPLATE_FILENAME = 'index.phtml';


    /**
     * @var string
     */
    protected $_dir;


    /**
     * Initializes a new instance of that class.
     *
     * @param string $dir The custom root directory of the views.
     */
    public function __construct($dir = null) {
        $dir = \trim($dir);
        if ('' == $dir) {
            $dir = \PHP5BP_DIR_VIEWS;
        }

        $this->_dir = \realpath($dir);
    }


    /**
     * Gets the directory.
     *
     * @return string The directory.
     */
    public function dir() {
        return $this->_dir;
    }


    /**
     * Renders a view.
     *
     * @param string $name The name of the view (script).
     *                     An empty value indicates that no script should
     *                     be executed.
     *
     * @return mixed The rendered result of the view script.
     *               (false) indicates that there is no data to output.
     */
    public function render($name) {
        $result = false;

        $dir = \str_replace('.', \DIRECTORY_SEPARATOR, \trim($name));
        if ('' != $dir) {
            $dir = \realpath($this->dir() . \DIRECTORY_SEPARATOR . $dir);
            if (false !== $dir) {
                // renderer
                $renderer = new PhpRenderer($dir);
                $renderer->resolver()->addPaths(array(
                    $dir,
                ));

                // ViewModel
                $vm = new ViewModel();
                $vm->setTemplate(static::TEMPLATE_FILENAME);

                // set variables
                foreach ($this->_vars as $n => $v) {
                    $vm->setVariable($n, $v);
                }

                $result = $renderer->render($vm);
            }
        }

        return $result;
    }
}
