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
        return array(
            'PZ' => '19861222',
            'MK' => \php5bp::format('{0:Y-m-d H:i:s}', \DateTime::createFromFormat('Y-m-d H:i', '1979-09-23 21:50')),
            'wurst' => "Käse"
        );
    }

    public function testAction(ModuleExecutionContext $ctx) {
        if ($ctx != null) {

        }
    }

    public function test2Action(ModuleExecutionContext $ctx, $module, $a, $b, array &$result) {
        if ($ctx != null) {

        }
    }

    public function test3Action(ModuleExecutionContext $ctx, array &$result) {
        $ctx->setupForHtml(true);

        if ($ctx != null) {

        }

        $result = null;

        echo '<strong>abcdef</strong>';
    }
}
