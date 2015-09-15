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

use \System\Linq\Enumerable;
use \Zend\Cache\Storage\StorageInterface as ZendCacheStorageInterface;
use \Zend\Db\Adapter\AdapterInterface as ZendDbAdapterInterface;
use \Zend\Db\Sql\Select as ZendSqlSelect;
use \Zend\Db\Sql\Where as ZendSqlWhere;
use \Zend\Db\TableGateway\TableGatewayInterface as ZendTableGatewayInterface;


/**
 * A basic cachable object that is based on a table row.
 *
 * @package php5bp\Db
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
abstract class CachableRowBase extends \php5bp\Object {
    /**
     * @var ZendCacheStorageInterface
     */
    protected $_cache;
    /**
     * @var ZendDbAdapterInterface
     */
    protected $_db;
    /**
     * @var array
     */
    protected $_deletedEvents = array();
    /**
     * @var array
     */
    protected $_row;


    /**
     * Initializes a new instance of that class.
     *
     * @param ZendDbAdapterInterface $db The custom database connection to use.
     * @param ZendCacheStorageInterface $cache The custom cache storage to use.
     */
    public function __construct(ZendDbAdapterInterface $db = null, ZendCacheStorageInterface $cache = null) {
        $this->_cache = $cache;
        $this->_db = $db;
    }


    /**
     * Gets the cache storage to use.
     *
     * @return ZendCacheStorageInterface The cache storage.
     */
    public function cache() {
        $result = $this->_cache;
        if (null === $result) {
            $result = \php5bp::cache();
        }

        return $result;
    }

    /**
     * Gets the key for the cache storage.
     *
     * @return mixed The cache storage key.
     */
    public function cacheKey() {
        return \php5bp::format("TableRow::{0}::{1}",
                               static::tableName(),
                               Enumerable::create(static::toArraySafe($this->ids()))
                                         ->joinToString('::'));
    }

    /**
     * Clears the cache.
     *
     * @return bool Operation was successful or not.
     */
    public function clearCache() {
        try {
            $result = $this->cache()
                           ->removeItem($this->cacheKey());
        }
        catch (\Exception $ex) {
            $result = false;
        }

        if ($result) {
            $this->_row = null;
        }

        return $this;
    }

    /**
     * Removes all deleted events.
     *
     * @return $this
     */
    public function clearDeletedEvents() {
        $this->_deletedEvents = array();
        return $this;
    }

    /**
     * Collects IDs with their column names.
     *
     * @return array The IDs with their columns.
     */
    public function collectIds() {
        $result = array();

        $columns = static::idColumns();
        $ids     = $this->ids();

        $colCount = \count($columns);
        for ($i = 0; $i < $colCount; $i++) {
            $result[\trim($columns[$i])] = $ids[$i];
        }

        return $result;
    }

    /**
     * Creates a new WHERE statement for the underlying row.
     *
     * @return ZendSqlWhere The new statement.
     */
    public function createWhere() {
        return static::createWhereByIds($this->ids());
    }

    /**
     * Creates a new WHERE statement from a list of IDs.
     *
     * @param \Traversable|array|mixed $ids The list of IDs or a single ID.
     *
     * @return ZendSqlWhere The new statement.
     */
    protected static function createWhereByIds($ids) {
        $columns = static::toArraySafe(static::idColumns());
        $ids     = static::toArraySafe($ids);

        $result = new ZendSqlWhere();

        $i = 0;
        foreach ($columns as $col) {
            if ($i > 0) {
                $result = $result->AND;
            }

            $result->equalTo(\trim($col), $ids[$i]);
            ++$i;
        }

        return $result;
    }

    /**
     * Gets the database connection to use.
     *
     * @return ZendDbAdapterInterface The database connection.
     */
    public function db() {
        $result = $this->_db;
        if (null === $result) {
            $result = \php5bp::db();
        }

        return $result;
    }

    /**
     * Deletes the underlying row in the database.
     *
     * @return bool Operation was successful or not.
     */
    protected function deleteRow() {
        $result = $this->table()
                       ->delete($this->createWhere()) > 0;

        if ($result) {
            $this->clearCache();

            $this->raiseDeletedEvents();
        }

        return $result;
    }

    /**
     * Returns the list of ID columns.
     *
     * @return array|\Traversable|string The list of ID columns or a single column.
     *
     * @throws \System\NotImplementedException Please implement!
     */
    public static function idColumns() {
        throw new \System\NotImplementedException();
    }

    /**
     * Gets the list of IDs.
     *
     * @return array|\Traversable|mixed The list of IDs or a single ID.
     */
    protected abstract function ids();

    /**
     * Raises deleted events.
     */
    protected function raiseDeletedEvents() {
        $ctx         = new \stdClass();
        $ctx->ids    = $this->collectIds();
        $ctx->object = $this;

        // invoke events
        foreach ($this->_deletedEvents as $event) {
            \call_user_func($event,
                            $this, $ctx);
        }
    }

    /**
     * Reads the (uncached) row data from table even it is cached or not.
     *
     * @return array The data or (false) if the row does not exist anymore.
     */
    public function readRow() {
        $where = $this->createWhere();
        $dbRes = $this->table()
                      ->select(function(ZendSqlSelect $select) use ($where) {
                                   $select->where($where);

                                   $select->offset(0)
                                          ->limit(1);
                               });

        if (\count($dbRes) > 0) {
            $result = static::toArraySafe($dbRes->current());
        }
        else {
            $result = false;
        }

        return $result;
    }

    /**
     * Registers a function/method that is called if the row of that object has been deleted.
     *
     * @param callable $event The function to invoke.
     *
     * @return $this
     */
    public function registerDeletedEvent(callable $event) {
        $this->_deletedEvents[] = $event;

        return $this;
    }

    /**
     * Gets the current (cached) row data.
     *
     * @return array The data or (false) if the row does not exist anymore.
     */
    public function row() {
        /* @var ZendCacheStorageInterface $cache */

        if (null === $this->_row) {
            $cache    = $this->cache();
            $cacheKey = $this->cacheKey();

            $cachedRow = $cache->getItem($cacheKey, $isInCache);
            if ($isInCache) {
                $this->_row = $cachedRow;
            }
        }

        if (null === $this->_row) {
            $this->_row = $this->readRow();

            $cache->setItem($cacheKey, $this->_row);
        }

        return $this->_row;
    }

    /**
     * Gets a new table gateway.
     *
     * @param string $alias The custom alias to use.
     *
     * @return ZendTableGatewayInterface The new gateway.
     */
    public function table($alias = null) {
        $tableName = static::tableName();

        $alias = \trim($alias);
        if ('' != $alias) {
            $tableName = array(
                $alias => $tableName,
            );
        }

        return new \php5bp\Db\TableGateway($tableName,
                                           $this->db());
    }

    /**
     * Gets the name of the underlying table.
     *
     * @return string The table name.
     *
     * @throws \System\NotImplementedException Please implement!
     */
    public static function tableName() {
        throw new \System\NotImplementedException();
    }

    /**
     * Keeps sure that a value is an array.
     *
     * @param mixed $value The value.
     *
     * @return array The value as array.
     */
    protected static function toArraySafe($value) {
        if (\is_array($value)) {
            return $value;
        }

        if (null === $value) {
            return array();
        }

        if ($value instanceof \Traversable) {
            return \iterator_to_array($value);
        }

        return array($value);
    }

    /**
     * Updates a column of row.
     *
     * @param string $name The name of the column.
     * @param mixed $value The new value.
     *
     * @return bool Update was successful or not.
     */
    protected function updateColumn($name, $value) {
        $newData = array(
            \trim($name) => $value,
        );

        $result = $this->table()
                       ->update($newData,
                                $this->createWhere()) > 0;

        if ($result) {
            $this->clearCache();
        }

        return $result;
    }
}
