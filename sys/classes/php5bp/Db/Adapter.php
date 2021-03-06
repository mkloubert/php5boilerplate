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

namespace php5bp\Db;

use \Zend\Db\ResultSet\ResultSetInterface;
use \Zend\Db\Sql\Sql;


/**
 * Extension of \Zend\Db\Adapter\Adapter class.
 *
 * @package php5bp\Db
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class Adapter extends \Zend\Db\Adapter\Adapter {
    /**
     * @see \Zend\Db\Adapter\Driver\DriverInterface::formatParameterName
     */
    public function fp($name) {
        return $this->driver->formatParameterName($name);
    }

    /**
     * @see \Zend\Db\Adapter\Platform\PlatformInterface::quoteIdentifier
     */
    public function qi($identifier) {
        return $this->platform->quoteIdentifier($identifier);
    }

    /**
     * @see TableGateway
     */
    public function table($table, $features = null, ResultSetInterface $resultSetPrototype = null, Sql $sql = null) {
        return new TableGateway($table, $this, $features, $resultSetPrototype, $sql);
    }
}
