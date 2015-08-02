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


defined('PHP5BP_INDEX') or die();

define('PHP5BP_BOOTSTRAP', true , false);
define('PHP5BP_VERSION'  , '0.1', false);

// directories
define('PHP5BP_DIR_ROOT'     , realpath(__DIR__) . DIRECTORY_SEPARATOR              , false);
define('PHP5BP_DIR_SYSTEM'   , PHP5BP_DIR_ROOT . 'sys' . DIRECTORY_SEPARATOR        , false);
define('PHP5BP_DIR_BOOTSTRAP', PHP5BP_DIR_SYSTEM . 'bootstrap' . DIRECTORY_SEPARATOR, false);
define('PHP5BP_DIR_CLASSES'  , PHP5BP_DIR_SYSTEM . 'classes' . DIRECTORY_SEPARATOR  , false);
define('PHP5BP_DIR_CONFIG'   , PHP5BP_DIR_SYSTEM . 'conf' . DIRECTORY_SEPARATOR     , false);
define('PHP5BP_DIR_MODULES' , PHP5BP_DIR_SYSTEM . 'modules' . DIRECTORY_SEPARATOR   , false);
define('PHP5BP_DIR_SHUTDOWN' , PHP5BP_DIR_SYSTEM . 'shutdown' . DIRECTORY_SEPARATOR , false);

// update include paths
set_include_path(get_include_path() .
                 PATH_SEPARATOR . PHP5BP_DIR_CLASSES);

/**
 * Autoloader.
 *
 * @param string $clsName Name of the class to load.
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
spl_autoload_register(function($clsName) {
                          $file = realpath(PHP5BP_DIR_CLASSES .
                                           str_replace('\\', DIRECTORY_SEPARATOR, $clsName) .
                                           '.php');

                            if (false !== $file) {
                                require_once $file;
                            }
                        });

// bootstrap files
$bootstrapFiles = Enumerable::scanDir(PHP5BP_DIR_BOOTSTRAP);
if (false !== $bootstrapFiles) {
    $bootstrapFiles = $bootstrapFiles->where(function($x) {
                                                 return Enumerable::TYPE_FILE == $x->type &&
                                                        '.php' == substr($x->fullPath, -4);
                                             });

    foreach ($bootstrapFiles as $bsf) {
        require_once $bsf->fullPath;
    }
}
