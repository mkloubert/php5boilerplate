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


define('PHP5BP_INDEX', true, false);

chdir(__DIR__);

iconv_set_encoding('internal_encoding', 'UTF-8');
iconv_set_encoding('input_encoding', 'UTF-8');
iconv_set_encoding('output_encoding', 'UTF-8');
ob_start('ob_iconv_handler');

require_once __DIR__ . DIRECTORY_SEPARATOR . 'bootstrap.php';


// run application
$app = php5bp::app();
try {
    try {
        $app->initialize();
    }
    catch (\Exception $ex) {
        throw new \php5bp\Application\NotInitializedException($app, $ex);
    }

    $app->run();
}
catch (\Exception $ex) {
    if (!$app->handleException($ex)) {
        // not handled => rethrow
        throw $ex;
    }
}
finally {
    $app->dispose();

    if ($app->processShutdown()) {
        // shutdown process
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'shutdown.php';
    }
}
