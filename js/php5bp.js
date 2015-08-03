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


if ('undefined' === typeof Array.prototype.isEmpty) {
    /**
     * Checks if that array is empty or not.
     *
     * @method IsEmpty
     *
     * @return {Boolean} Is empty or not.
     */
    Object.defineProperty(Array.prototype, 'isEmpty', {
       get: function() {
           return this.length < 1;
       }
    });
}

if ('undefined' === typeof Array.prototype.isNotEmpty) {
    /**
     * Checks if that array is NOT empty.
     *
     * @method isNotEmpty
     *
     * @return {Boolean} Is empty (false) or not (true).
     */
    Object.defineProperty(Array.prototype, 'isNotEmpty', {
        get: function() {
            return this.length > 0;
        }
    });
}

if ('undefined' === typeof String.prototype.format) {
    /**
     * Handles that string as formatted string.
     *
     * @param {mixed} [...args] The values for the placeholders in that string.
     *
     * @return {String} The formatted string.
     */
    String.prototype.format = function() {
        var args = arguments;

        return this.replace(/{(\d+)}/g, function(match, number) {
                                            return (typeof 'undefined' !== args[number]) ? args[number]
                                                                                         : match;
                                        });
    };
}

if ('undefined' === typeof String.prototype.formatArray) {
    /**
     * Handles that string as formatted string.
     *
     * @param {Array} [args] The values for the placeholders in that string.
     *
     * @return {String} The formatted string.
     */
    String.prototype.formatArray = function(args) {
        if (!args) {
            args = [];
        }

        return this.replace(/{(\d+)}/g, function(match, number) {
                                            return (typeof 'undefined' !== args[number]) ? args[number]
                                                                                         : match;
                                        });
    };
}

if ('undefined' === typeof String.isNullOrWhitespace) {
    /**
     * Check if a string is (null) or contains whitespaces only.
     *
     * @param {String} The string to check.
     *
     * @return {Boolean} Is (null) or contains whitespaces only.
     */
    String.isNullOrWhitespace = function(s) {
        return (s == null) ||
               (jQuery.trim(s).length < 1);
    };
}

if ('undefined' === typeof String.prototype.trim) {
    /**
     * Trims the string.
     *
     * @return {String} The new string.
     */
    String.prototype.trim = function() {
        return jQuery.trim(this);
    };
}


var $MJK_PHP5_BOILERPLATE = {};

$MJK_PHP5_BOILERPLATE.funcs = {};
{
    /**
     * Keeps sure to return an object as (getter) function.
     *
     * @param {Object} obj The input object.
     *
     * @return {function} The object as function.
     */
    $MJK_PHP5_BOILERPLATE.funcs.asFunc = function(obj) {
        var result = obj;
        if (!this.isFunc(result)) {
            result = function() {
                return obj;
            };
        }

        return result;
    };

    /**
     * Keeps sure to return an object as jQuery (wrapped) object.
     *
     * @param {Object} obj The input object.
     *
     * @return {jQuery} The jQuery object.
     */
    $MJK_PHP5_BOILERPLATE.funcs.asJQuery = function(obj) {
        if (!this.isJQuery(obj)) {
            obj = jQuery(obj);
        }

        return obj;
    };

    /**
     * Checks if an object is a function or not.
     *
     * @param {Object} obj The object to check.
     *
     * @return {Boolean} Is a function or not.
     */
    $MJK_PHP5_BOILERPLATE.funcs.isFunc = function(obj) {
        return jQuery.isFunction(obj);
    };

    /**
     * Checks if an object is a jQuery object or not.
     *
     * @param {Object} obj The object to check.
     *
     * @return {Boolean} Is a jQuery object or not.
     */
    $MJK_PHP5_BOILERPLATE.funcs.isJQuery = function(obj) {
        return obj instanceof jQuery;
    };
}

$MJK_PHP5_BOILERPLATE.page = {};
{
    // this is for internal use only!
    $MJK_PHP5_BOILERPLATE.page.__onLoadedActions = [];

    $MJK_PHP5_BOILERPLATE.page.elements = {};  // elements and selectors
    $MJK_PHP5_BOILERPLATE.page.funcs = {};  // functions
    $MJK_PHP5_BOILERPLATE.page.vars = {};  // variables / values

    /**
     * Adds a function that is called when the page has been loaded.
     *
     * @param {function} action The action to invoke.
     * @param {Object} opts Additional options.
     *
     * @chainable
     */
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

    /**
     * Process all actions that were added by $MJK_PHP5_BOILERPLATE.page.addOnLoaded() method.
     *
     * @chainable
     */
    $MJK_PHP5_BOILERPLATE.page.processOnLoadedActions = function() {
        if (!$MJK_PHP5_BOILERPLATE.page.__onLoadedActions) {
            return this;
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
                'page': $MJK_PHP5_BOILERPLATE.page,
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

    /**
     * Adds an (element) selector.
     * The selectors can be accessed by using $MJK_PHP5_BOILERPLATE.page.elements property.
     *
     * @param {String} name The name of the selector.
     * @param {jQuery} selector The selector.
     *
     * @chainable
     */
    $MJK_PHP5_BOILERPLATE.page.addElements = function(name, selector) {
        Object.defineProperty(this.elements,
                              jQuery.trim(name),
                              {
                                  get: function() {
                                      return $MJK_PHP5_BOILERPLATE.funcs.asJQuery(selector);
                                  }
                              });

        return this;
    };

    /**
     * Adds a function.
     * The functions can be accessed by using $MJK_PHP5_BOILERPLATE.page.funcs property.
     *
     * @param {String} name The name of the function.
     * @param {function} func The function.
     *
     * @chainable
     */
    $MJK_PHP5_BOILERPLATE.page.addFunction = function(name, func) {
        Object.defineProperty(this.funcs,
                              jQuery.trim(name),
                              {
                                  get: function() {
                                      return $MJK_PHP5_BOILERPLATE.funcs.asFunc(func);
                                  }
                              });

        return this;
    };

    /**
     * Adds a variable.
     * The variables can be accessed by using $MJK_PHP5_BOILERPLATE.page.vars property.
     *
     * @param {String} name The name of the function.
     * @param {mixed} value The value or the function that provides it.
     *
     * @chainable
     */
    $MJK_PHP5_BOILERPLATE.page.addVar = function(name, value) {
        Object.defineProperty(this.vars,
                              jQuery.trim(name),
                              {
                                  get: $MJK_PHP5_BOILERPLATE.funcs.asFunc(value)
                              });

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
