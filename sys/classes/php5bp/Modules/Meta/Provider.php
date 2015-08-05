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

namespace php5bp\Modules\Meta;


/**
 * A common meta data provider for a module.
 *
 * @package php5bp\Modules\Meta
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class Provider extends \php5bp\Object implements ProviderInterface {
    /**
     * The name of a meta file.
     */
    const META_NAME = 'meta';
    /**
     * Expression for module name separator.
     */
    const MODULE_NAME_SEPARATOR = '/';


    public function getModuleMetaByName($moduleName) {
        $modulePath = \realpath(\PHP5BP_DIR_MODULES .
                                \str_replace(static::MODULE_NAME_SEPARATOR, \DIRECTORY_SEPARATOR, $moduleName));
        if (false !== $modulePath) {
            return \php5bp::conf(static::META_NAME, $modulePath);
        }

        return false;
    }
}