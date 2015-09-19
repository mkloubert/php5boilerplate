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

use \System\Linq\Enumerable;


defined('PHP5BP_BOOTSTRAP') or die();

define('PHP5BP_SHUTDOWN', true, false);

// shutdown files
$shutdownFiles = Enumerable::scanDir(PHP5BP_DIR_SHUTDOWN);
if (false !== $shutdownFiles) {
    $shutdownFiles = $shutdownFiles->where(function($x) {
                                               return Enumerable::TYPE_FILE === $x->type &&
                                                      '.php' === substr($x->fullPath, -4);
                                           });

    foreach ($shutdownFiles as $sdf) {
        require_once $sdf->fullPath;
    }
}
