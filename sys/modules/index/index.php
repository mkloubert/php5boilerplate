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
        ?>

        <style type="text/css">
            .col {
                min-width: 100px;
            }
        </style>

        <div class="row" id="wurst">
            <div class="col col-1">1</div>
            <div class="col col-1">2</div>
            <div class="col col-1">3</div>
            <div class="col col-1">4</div>
            <div class="col col-1">5</div>
            <div class="col col-1">6</div>
            <div class="col col-1">7</div>
            <div class="col col-1">8</div>
            <div class="col col-1">9</div>
            <div class="col col-1">10</div>
            <div class="col col-1">11</div>
            <div class="col col-1">12</div>
        </div>

        <script type="text/javascript">

            $php5bp.page.addElements('wurstColumns', '#wurst .col');

            $php5bp.page.addOnLoaded(function() {
                alert('abc def e'.ucwords());
            });

        </script>

        <?php
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
