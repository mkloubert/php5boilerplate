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

namespace php5bp\Diagnostics\Log;


/**
 * Extension of \Zend\Log\Logger class.
 *
 * @package php5bp\Diagnostics\Log
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class Logger extends \Zend\Log\Logger {
    /**
     * Add a filter specific to all writers.
     *
     * @param  int|string|\Zend\Log\Filter\FilterInterface $filter
     * @param  array|null $options
     *
     * @return $this
     *
     * @throws \Zend\Log\Exception\InvalidArgumentException
     */
    public function addFilter($filter, array $options = null) {
        foreach ($this->writers->toArray() as $writer) {
            $writer->addFilter($filter, $options);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function log($priority, $message, $extra = array()) {
        $timestamp = new \DateTime();

        if (0 === $this->writers->count()) {
            throw new \Zend\Log\Exception\RuntimeException('No log writer specified');
        }

        $event = array(
            'timestamp'    => $timestamp,
            'priority'     => (int) $priority,
            'priorityName' => $this->priorities[$priority],
            'message'      => $message,
            'extra'        => $extra,
        );

        foreach ($this->processors->toArray() as $processor) {
            $event = $processor->process($event);
        }

        foreach ($this->writers->toArray() as $writer) {
            $writer->write($event);
        }

        return $this;
    }
}
