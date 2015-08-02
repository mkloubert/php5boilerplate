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
        $var = $ctx->getVar('wurst', null, $has);
        $has2 = $ctx->hasVar('wurst');
        $ctx->setVar('Wurst', 666);
        $var = $ctx->getVar('WuRsT', null, $has);
        $has2 = $ctx->hasVar('wUrSt');
        $has2 = $ctx->clearVars()
                    ->hasVar('wUrSt');

        if ($ctx != null) {

        }
    }

    public function testAction(ModuleExecutionContext $ctx) {
        if ($ctx != null) {

        }
    }

    public function test2(ModuleExecutionContext $ctx) {
        if ($ctx != null) {

        }
    }

    public function test3Action(ModuleExecutionContext $ctx) {
        if ($ctx != null) {

        }
    }
}
