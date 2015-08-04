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
     * @property isEmpty
     * @type Boolean
     * @readOnly
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
     * @property isNotEmpty
     * @type Boolean
     * @readOnly
     */
    Object.defineProperty(Array.prototype, 'isNotEmpty', {
        get: function() {
            return this.length > 0;
        }
    });
}

if ('undefined' === typeof String.prototype.endsWith) {
    /**
     * Checks if that string ends with an expression.
     *
     * @param {String} s The expression.
     *
     * @return {Boolean} Ends with expression or not.
     */
    String.prototype.endsWith = function(s) {
        if (null == s) {
            s = '';
        }

        return ('' == s) ||
               (this.indexOf(s,
                             this.length - s.length) > -1);
    };
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

if ('undefined' === typeof String.prototype.isEmpty) {
    /**
     * Checks if that string is empty or not.
     *
     * @property isEmpty
     * @type Boolean
     * @readOnly
     */
    Object.defineProperty(String.prototype, 'isEmpty', {
        get: function() {
            return this.length < 1;
        }
    });
}

if ('undefined' === typeof String.prototype.isNotEmpty) {
    /**
     * Checks if that string is NOT empty.
     *
     * @property isNotEmpty
     * @type Boolean
     * @readOnly
     */
    Object.defineProperty(String.prototype, 'isNotEmpty', {
        get: function() {
            return this.length > 0;
        }
    });
}

if ('undefined' === typeof String.isNullOrEmpty) {
    /**
     * Check if a string is (null) or empty.
     *
     * @param {String} s The string to check.
     *
     * @return {Boolean} Is (null) or empty.
     */
    String.isNullOrEmpty = function(s) {
        return (null == s) || ('' == s);
    };
}

if ('undefined' === typeof String.isNullOrWhitespace) {
    /**
     * Check if a string is (null) or contains whitespaces only.
     *
     * @param {String} s The string to check.
     *
     * @return {Boolean} Is (null) or contains whitespaces only.
     */
    String.isNullOrWhitespace = function(s) {
        return (null == s) ||
               (s.replace(/^\s+|\s+$/g, '').length < 1);
    };
}

if ('undefined' === typeof String.prototype.startsWith) {
    /**
     * Checks if that string starts with an expression.
     *
     * @param {String} s The expression.
     *
     * @return {Boolean} Starts with expression or not.
     */
    String.prototype.startsWith = function(s) {
        if (null == s) {
            s = '';
        }

        return ('' == s) ||
               (0 == this.indexOf(s));
    };
}

if ('undefined' === typeof String.prototype.trim) {
    /**
     * Trims the string.
     *
     * @return {String} The new string.
     */
    String.prototype.trim = function() {
        return this.replace(/^\s+|\s+$/g, '');
    };
}

if ('undefined' === typeof String.prototype.trimLeft) {
    /**
     * Removes whitespaces at the beginning.
     *
     * @return {String} The new string.
     */
    String.prototype.trimLeft = function() {
        return this.replace(/^\s+/, '');
    };
}

if ('undefined' === typeof String.prototype.trimRight) {
    /**
     * Removes ending whitespaces.
     *
     * @return {String} The new string.
     */
    String.prototype.trimRight = function() {
        return this.replace(/\s+$/, '');
    };
}


var $MJK_PHP5_BOILERPLATE = {};

$MJK_PHP5_BOILERPLATE.events = {};
{
    // DON'T CHANGE!
    // THIS IS FOR INTERNAL USE ONLY!
    $MJK_PHP5_BOILERPLATE.events.__defaultPageLoaded = function(e) {
        if (!$MJK_PHP5_BOILERPLATE.page.__onLoadedActions) {
            return this;
        }

        var errors = [];
        var lastError = null;
        var prevError = null;
        var prevValue = null;
        var value = null;
        for (var i = 0; i < $MJK_PHP5_BOILERPLATE.page.__onLoadedActions.length; i++) {
            var entry = $MJK_PHP5_BOILERPLATE.page.__onLoadedActions[i];
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
                'time': e.time,
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
    };

    $MJK_PHP5_BOILERPLATE.events.pageLoaded = $MJK_PHP5_BOILERPLATE.events.__defaultPageLoaded;
}

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
     * Executes JavaScript code globally.
     *
     * @param {String} code The JavaScript code to execute.
     *
     * @return {mixed} The result of the execution.
     */
    $MJK_PHP5_BOILERPLATE.funcs.eval = function(code) {
        return jQuery.globalEval(code);
    };

    /**
     * Invokes a function inside a try-catch block and returns the result as object.
     *
     * @param {function} fn The function to invoke.
     * @param {mixed} [...args] The arguments for the function.
     *
     * @return {Object} The result object of the invocation.
     */
    $MJK_PHP5_BOILERPLATE.funcs.invoke = function(fn) {
        var args = [];
        for (var i = 1; i < arguments.length; i++) {
            args.push(arguments[i]);
        }

        return this.invokeArray(fn, args);
    };

    /**
     * Invokes a function inside a try-catch block and returns the result as object.
     *
     * @param {function} fn The function to invoke.
     * @param {Array} [args] The arguments for the function.
     *
     * @return {Object} The result object of the invocation.
     */
    $MJK_PHP5_BOILERPLATE.funcs.invokeArray = function(fn, args) {
        if (!args) {
            args = [];
        }

        var result = {
            'args': args,
            'hasBeenInvoked': false
        };

        Object.defineProperty(result, 'duration', {
            get: function() {
                return this.endTime - this.startTime;
            }
        });

        Object.defineProperty(result, 'hasFailed', {
            get: function() {
                return this.error ? true : false;
            }
        });

        try {
            if (fn) {
                result.hasBeenInvoked = true;

                var code = 'fn(';
                for (var i = 0; i < result.args.length; i++) {
                    if (i > 0) {
                        code += ',';
                    }

                    code += 'result.args[' + i + ']';
                }
                code += ');';

                result.startTime = $MJK_PHP5_BOILERPLATE.now;
                result.result = eval(code);
                result.endTime = $MJK_PHP5_BOILERPLATE.now;
            }
        }
        catch (e) {
            result.endTime = $MJK_PHP5_BOILERPLATE.now;

            result.error = e;
        }

        return result;
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

/**
 * Gets the current time.
 *
 * @property now
 * @type Date
 * @readOnly
 */
Object.defineProperty($MJK_PHP5_BOILERPLATE,
                      'now',
                      {
                          get: function() {
                              return new Date();
                          }
                      });

/**
 * Gets the current UTC time.
 *
 * @property nowUTC
 * @type Date
 * @readOnly
 */
Object.defineProperty($MJK_PHP5_BOILERPLATE,
                      'nowUTC',
                      {
                          get: function() {
                              var n = this.now;

                              return new Date(n.getUTCFullYear(), n.getUTCMonth(), n.getUTCDate(),
                                              n.getUTCHours(), n.getUTCMinutes(), n.getUTCSeconds(), n.getUTCMilliseconds());
                          }
                      });

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
            'continueOnError': false,
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
        if ($MJK_PHP5_BOILERPLATE.events.pageLoaded) {
            var e = {
                'time': $MJK_PHP5_BOILERPLATE.now
            };

            e.invokeDefault = function() {
                $MJK_PHP5_BOILERPLATE.events.__defaultPageLoaded(e);
            };

            $MJK_PHP5_BOILERPLATE.events.pageLoaded(e);

            return true;
        }

        return false;
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

// alias
if ('undefined' === typeof $php5bp) {
    $php5bp = $MJK_PHP5_BOILERPLATE;
}
