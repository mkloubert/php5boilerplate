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


namespace php5bp\Modules\Impl;

use \php5bp\Modules\ModuleBase;
use \php5bp\Modules\Execution\ContextInterface as ModuleExecutionContext;


/**
 * The index / default module.
 *
 * @package php5bp\Modules\Impl
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class IndexModule extends ModuleBase {
    protected function execute(ModuleExecutionContext $ctx) {
        $conf1 = \php5bp::conf('test.test1');
        // $conf2 = \php5bp::conf('test.test2');
        $conf3 = \php5bp::conf('test.test3');

        $h = \php5bp::cache()->hasItem('PZ');
        if (!$h) {
            \php5bp::cache()->setItem('PZ', '19861222');
            \php5bp::cache()->setItem('MK', '19790923');
            \php5bp::cache()->setItem('TM', '19790905');
        }

        $h = \php5bp::cache()->hasItem('PZ');
        if ($h) {
            \php5bp::cache()->removeItem('PZ');
        }

        $h = \php5bp::cache()->hasItem('MK');
        if ($h) {
            \php5bp::cache()->removeItem('MK');
        }

        $h1 = \php5bp::cache()->hasItem('PZ');
        $h2 = \php5bp::cache()->hasItem('MK');
        $h3 = \php5bp::cache()->hasItem('TM');
        if (!$h1 && !$h2 && $h3) {
            if ($ctx != null) {

            }
        }
    }

    public function testAction(ModuleExecutionContext $ctx) {
        if ($ctx != null) {

        }
    }

    public function test2Action(ModuleExecutionContext $ctx) {
        if ($ctx != null) {

        }
    }

    public function test3Action(ModuleExecutionContext $ctx) {
        if ($ctx != null) {

        }
    }
}
