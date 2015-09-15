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

use \Zend\Db\Adapter\Adapter as ZendDbAdapter;
use \Zend\Db\RowGateway\Exception\InvalidArgumentException as ZendInvalidArgumentException;
use \Zend\Db\Sql\Sql as ZendSql;
use \Zend\Db\Sql\TableIdentifier as ZendSqlTableIdentifier;

/**
 * Extension of \Zend\Db\RowGateway\RowGateway class.
 *
 * @package php5bp\Db
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class RowGateway extends \Zend\Db\RowGateway\RowGateway {
    /**
     * Initializes a new instance of that class.
     *
     * @param string $primaryKeyColumn
     * @param string|ZendSqlTableIdentifier $table
     * @param ZendDbAdapter|ZendSql $adapterOrSql
     *
     * @throws ZendInvalidArgumentException
     */
    public function __construct($primaryKeyColumn, $table, $adapterOrSql = null) {
        if (null === $adapterOrSql) {
            $adapterOrSql = \php5bp::db();
        }

        parent::__construct($primaryKeyColumn, $table, $adapterOrSql);
    }
}
