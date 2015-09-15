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

use \Zend\Db\Adapter\AdapterInterface;
use \Zend\Db\ResultSet\ResultSetInterface;
use \Zend\Db\Sql\Sql;
use \Zend\Db\Sql\TableIdentifier;
use \Zend\Db\TableGateway\Exception\InvalidArgumentException;
use \Zend\Db\TableGateway\Feature\AbstractFeature;
use \Zend\Db\TableGateway\Feature\FeatureSet;


/**
 * Extension of \Zend\Db\TableGateway\TableGateway class.
 *
 * @package php5bp\Db
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class TableGateway extends \Zend\Db\TableGateway\TableGateway {
    /**
     * Initializes a new instance of that class.
     *
     * @param string|TableIdentifier|array $table
     * @param AdapterInterface|string $adapter
     * @param AbstractFeature|FeatureSet|AbstractFeature[]|null $features
     * @param ResultSetInterface|null $resultSetPrototype
     * @param Sql|null $sql
     *
     * @throws InvalidArgumentException
     */
    public function __construct($table, $adapter = null, $features = null, ResultSetInterface $resultSetPrototype = null, Sql $sql = null) {
        if (null === $adapter) {
            // create default instance
            $adapter = \php5bp::db();
        }

        if (!$adapter instanceof AdapterInterface) {
            // use $adapter as config storage name
            $adapter = \php5bp::db($adapter);
        }

        parent::__construct($table, $adapter, $features, $resultSetPrototype, $sql);
    }
}
