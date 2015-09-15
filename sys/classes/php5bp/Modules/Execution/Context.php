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

namespace php5bp\Modules\Execution;

use \php5bp\Modules\ModuleInterface;
use \System\Linq\Enumerable;


/**
 * A module execution context.
 *
 * @package php5bp\Modules\Execution
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class Context extends \php5bp\Object implements ContextInterface {
    /**
     * Name of the default view.
     */
    const DEFAULT_VIEW_DEFAULT = 'main';
    /**
     * Default name of the view that outputs JSON data.
     */
    const DEFAULT_VIEW_JSON = 'json';
    /**
     * Default name of the view that outputs nothing.
     */
    const DEFAULT_VIEW_NULL = 'null';
    /**
     * The name of the variable for storing the action name.
     */
    const VAR_NAME_ACTION = 'action';
    /**
     * The name of the variable for storing the view name.
     */
    const VAR_NAME_VIEW = 'view';


    /**
     * @var string
     */
    protected $_action;
    /**
     * @var string
     */
    protected $_title;
    /**
     * @var array
     */
    protected $_vars;
    /**
     * @var string
     */
    protected $_view;
    /**
     * @var array
     */
    public $Config;
    /**
     * @var \php5bp\Http\Requests\ContextInterface
     */
    public $Request;
    /**
     * @var \php5bp\Http\Responses\ContextInterface
     */
    public $Response;
    /**
     * @var string
     */
    public $View;


    /**
     * Initializes a new instance of that class.
     */
    public function __construct() {
        $this->clearVars();
    }


    /**
     * {@inheritDoc}
     */
    public function clearVars() {
        $this->_vars = array();

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function config() {
        return $this->Config;
    }

    /**
     * {@inheritDoc}
     */
    public function getAction() {
        return $this->_action;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig($name, $defaultValue = null, &$found = null) {
        $name = static::normalizeConfigName($name);

        $found = false;

        $key = Enumerable::create($this->Config)
                         ->select(function($x, $ctx) {
                                      return Context::normalizeConfigName($ctx->key);
                                  })
                         ->singleOrDefault(function($x) use ($name) {
                                               return $x === $name;
                                           }, false);

        if (false !== $key) {
            $found = true;
            return $this->Config[$key];
        }

        return $defaultValue;
    }

    /**
     * {@inheritDoc}
     */
    public function getTitle() {
        return $this->_title;
    }

    /**
     * {@inheritDoc}
     */
    public function getVar($name, $defaultValue = null, &$found = null) {
        $name = static::normalizeVarName($name);

        $found = \array_key_exists($name, $this->_vars);
        if ($found) {
            return $this->_vars[$name];
        }

        return $defaultValue;
    }

    /**
     * {@inheritDoc}
     */
    public function getView() {
        return $this->_view;
    }

    /**
     * {@inheritDoc}
     */
    public function hasConfig($name) {
        $this->getConfig($name, null, $result);
        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function hasVar($name) {
        $this->getVar($name, null, $result);
        return $result;
    }

    /**
     * Parses the name of a config value.
     *
     * @param string $name The input value.
     *
     * @return string The parsed value.
     */
    public static function normalizeConfigName($name) {
        return \trim($name);
    }

    /**
     * Parses the name for a variable.
     *
     * @param string $name The input value.
     *
     * @return string The parsed value.
     */
    public static function normalizeVarName($name) {
        return \trim($name);
    }

    /**
     * {@inheritDoc}
     */
    public function notFound() {
        $this->suppressOutput();
        $this->response()->clearHeaders();

        $this->response()->setCode(404);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function noView() {
        return $this->setView(null);
    }

    /**
     * {@inheritDoc}
     */
    public function redirectTo($url) {
        $this->suppressOutput();
        $this->response()->clearHeaders();

        $this->response()->setCode(307)
                         ->setHeader('Location', \trim($url));

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function request() {
        return $this->Request;
    }

    /**
     * {@inheritDoc}
     */
    public function response() {
        return $this->Response;
    }

    /**
     * {@inheritDoc}
     */
    public function sendAsDownload($filename, $mime = null) {
        $filename = \trim($filename);
        if ('' !== $filename) {
            $mime = \trim($mime);
            if ('' === $mime) {
                $mime = \php5bp::getMimeByFilename($filename);
            }

            $this->noView();

            $this->response()->setContentType($mime)
                             ->setHeader('Content-Disposition',
                                         \php5bp::format('attachment; filename="{0}"',
                                                         $filename));
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setAction($actionName) {
        $actionName = \trim($actionName);
        if ('' === $actionName) {
            $actionName = null;
        }

        $this->_action = $actionName;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultView() {
        $viewName = static::DEFAULT_VIEW_DEFAULT;

        $appConf = \php5bp::appConf();
        if (\array_key_exists('views', $appConf)) {
            if (\array_key_exists('default', $appConf['views'])) {
                $viewName = $appConf['views']['default'];
            }
        }

        return $this->setView($viewName);
    }

    /**
     * {@inheritDoc}
     */
    public function setTitle($title) {
        $title = \trim($title);
        if ('' === $title) {
            $title = null;
        }

        $this->_title = $title;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setupForHtml($viewName = false) {
        if (false !== $viewName) {
            if (true !== $viewName) {
                $this->setView($viewName);
            }
            else {
                $this->setDefaultView();
            }
        }

        $this->response()
             ->setContentType('text/html; charset=' . \php5bp::outputEncoding());

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setupForJson() {
        $viewName = static::DEFAULT_VIEW_JSON;

        $appConf = \php5bp::appConf();
        if (\array_key_exists('views', $appConf)) {
            if (\array_key_exists('json', $appConf['views'])) {
                $viewName = $appConf['views']['json'];
            }
        }

        $this->response()
             ->setContentType('application/json; charset=' . \php5bp::outputEncoding());

        $this->setView($viewName);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setVar($name, $value) {
        $name = static::normalizeVarName($name);

        $this->_vars[$name] = $value;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setView($viewName) {
        $viewName = \trim($viewName);
        if ('' === $viewName) {
            $viewName = null;
        }

        $this->_view = $viewName;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function suppressOutput() {
        $viewName = static::DEFAULT_VIEW_NULL;

        $appConf = \php5bp::appConf();
        if (\array_key_exists('views', $appConf)) {
            if (\array_key_exists('null', $appConf['views'])) {
                $viewName = $appConf['views']['null'];
            }
        }

        return $this->setView($viewName);
    }

    /**
     * {@inheritDoc}
     */
    public function unsetVar($name) {
        $name = static::normalizeVarName($name);

        unset($this->_vars[$name]);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function vars() {
        return $this->_vars;
    }
}
