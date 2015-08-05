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
        \php5bp::log()
               ->debug('Something for the developer or tester')
               ->info('An info')
               ->notice('That is something you should know')
               ->warn("That's a warning")
               ->err('Something went wrong!')
               ->crit('This becomes critical!')
               ->alert('ALERT!!!')
               ->emerg('WORST CASE!');

        $ctx->setupForHtml()
            ->setDefaultView();

        ?>
            <script type="text/javascript">

                $php5bp.addOnLoaded(function() {
                    var b = $php5bp.createBatch({
                        'initialResult': 1
                    });

                    b = $php5bp.createFunctionIterator();

                    var i = 3;

                    b.add(function() {
                        i *= 100;
                        // alert('a');
                    });

                    b.add(function() {
                        i -= 13;
                        // alert('b');
                    });

                    // b.run();
                    while(b.invokeNext()) {
                        // alert(b.isLast);
                    }

                    $php5bp.addVar('wurst', function() {
                        return new Date();
                    });

                    alert($php5bp.vars.wurst);
                    alert(i);
                    alert(b.hasNext);
                    alert($php5bp.vars.wurst);
                });

            </script>
        <?php
    }
}
