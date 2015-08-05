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

namespace php5bp\Diagnostics\Log\Writers;


/**
 * A log writer that uses a callable.
 *
 * @package php5bp\Diagnostics\Log\Writers
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class CallableLogWriter extends \Zend\Log\Writer\AbstractWriter {
    /**
     * @var callable
     */
    protected $_action;


    /**
     * Initializes a new instance of that class.
     *
     * @param callable $action The action to invoke.
     * @param array|\Traversable $options The options for the logger.
     */
    public function __construct(callable $action, $options = null) {
        parent::__construct($options);

        $this->_action = $action;
    }


    protected function doWrite(array $event) {
        \call_user_func($this->_action,
                        $event);
    }
}
