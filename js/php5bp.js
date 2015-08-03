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

var $MJK_PHP5_BOILERPLATE = {};

$MJK_PHP5_BOILERPLATE.page = {};
{
    $MJK_PHP5_BOILERPLATE.page.__onLoadedActions = [];

    $MJK_PHP5_BOILERPLATE.page.addOnLoaded = function(action, opts) {
        opts = jQuery.extend({
            'continueOnError': true,
            'state': null
        }, opts);

        if (action) {
            var newEntry = {
                'action': action,
                'options': opts
            };

            this.__onLoadedActions.push(newEntry);
        }

        return this;
    };

    $MJK_PHP5_BOILERPLATE.page.onHandleLoadedErrors = function(errors) {
        // replace with own code
    };

    $MJK_PHP5_BOILERPLATE.page.processOnLoadedActions = function() {
        if (!$MJK_PHP5_BOILERPLATE.page.__onLoadedActions) {
            return;
        }

        var errors = [];
        var lastError = null;
        var prevError = null;
        var prevValue = null;
        var value = null;
        for (var i = 0; i < this.__onLoadedActions.length; i++) {
            var entry = this.__onLoadedActions[i];
            if (!entry.action) {
                continue;
            }

            var ctx = {
                'errors': errors,
                'index': i,
                'lastError': lastError,
                'nextValue': null,
                'prevError': prevError,
                'prevValue' : prevValue,
                'state': entry.options.state,
                'value': value
            };

            try {
                entry.action(ctx);

                prevError = null;
            }
            catch (err) {
                var errCtx = {
                    'error': err,
                    'index': i
                };

                lastError = errCtx;
                prevError = errCtx;

                errors.push(errCtx);

                if (!entry.options.continueOnError) {
                    throw err;
                }
            }
            finally {
                prevValue = ctx.nextValue;
                value = ctx.value;
            }
        }

        if (errors.length > 0) {
            if ($MJK_PHP5_BOILERPLATE.page.onHandleLoadedErrors) {
                $MJK_PHP5_BOILERPLATE.page.onHandleLoadedErrors(errors);
            }
        }

        return this;
    };
}

// aliases
{
    if ('undefined' === typeof $php5boilerplate) {
        $php5boilerplate = $MJK_PHP5_BOILERPLATE;
    }

    if ('undefined' === typeof $php5BP) {
        $php5BP = $MJK_PHP5_BOILERPLATE;
    }

    if ('undefined' === typeof $php5bp) {
        $php5bp = $MJK_PHP5_BOILERPLATE;
    }
}
