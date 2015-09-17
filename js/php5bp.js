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


if ('undefined' === typeof Array.prototype.contains) {
    /**
     * Checks if that array contains an item or not.
     *
     * @param {mixed} item The item to search for.
     * @param {Function} [equalFunc] The function that compares two items.
     *
     * @return {Boolean} Exists or not.
     */
    Array.prototype.contains = function(item, equalFunc) {
        if (!equalFunc) {
            equalFunc = function(x, y) {
                return x == y;
            };
        }

        for (var i = 0; i < this.length; i++) {
            var arrItem = this[i];

            if (equalFunc(arrItem, item)) {
                return true;
            }
        }

        return false;
    };
}

if ('undefined' === typeof Array.prototype.count) {
    /**
     * Gets the number of elements.
     *
     * @property count
     * @type Number
     * @readOnly
     */
    Object.defineProperty(Array.prototype, 'count', {
        get: function() {
            return this.length;
        }
    });
}

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

if ('undefined' === typeof Array.prototype.remove) {
    /**
     * Removes an item.
     *
     * @param {mixed} item The item to remove.
     * @param {Function} [equalFunc] The function that compares two items.
     *
     * @return {Number} If removed this is the index where the item was remove.
     *                  Otherwise (null) is returned.
     */
    Array.prototype.remove = function(item, equalFunc) {
        if (!equalFunc) {
            equalFunc = function(x, y) {
                return x == y;
            };
        }

        for (var i = 0; i < this.length; i++) {
            var arrItem = this[i];

            if (equalFunc(arrItem, item)) {
                this.splice(i, 1);
                return i;
            }
        }

        return null;
    };
}

if ('undefined' === typeof Array.prototype.removeAll) {
    /**
     * Removes all items.
     *
     * @param {mixed} item The items to remove.
     * @param {Function} [equalFunc] The function that compares two items.
     *
     * @return {Array} The indexes of removed items.
     */
    Array.prototype.remove = function(item, equalFunc) {
        if (!equalFunc) {
            equalFunc = function(x, y) {
                return x == y;
            };
        }

        var result = [];

        for (var i = 0; i < this.length;) {
            var arrItem = this[i];

            if (equalFunc(arrItem, item)) {
                this.splice(i, 1);
                result.push(i);

                continue;
            }

            ++i;
        }

        return result;
    };
}

if ('undefined' === typeof Array.prototype.removeAt) {
    /**
     * Removes an item at a specific position.
     *
     * @param {Number} index The zero based index.
     *
     * @return {Boolean} Item was removed or not.
     */
    Array.prototype.removeAt = function(index) {
        if ((index >= 0) && (index < this.length)) {
            this.splice(index, 1);
            return true;
        }

        return false;
    };
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
        if (!$MJK_PHP5_BOILERPLATE.__onLoadedActions) {
            return this;
        }

        var errors = [];
        var lastError = null;
        var prevError = null;
        var prevValue = null;
        var value = null;
        for (var i = 0; i < $MJK_PHP5_BOILERPLATE.__onLoadedActions.length; i++) {
            var entry = $MJK_PHP5_BOILERPLATE.__onLoadedActions[i];
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
            if ($MJK_PHP5_BOILERPLATE.events.handleLoadedErrors) {
                $MJK_PHP5_BOILERPLATE.events.handleLoadedErrors({
                    'errors': errors
                });
            }
        }
    };

    $MJK_PHP5_BOILERPLATE.events.handleLoadedErrors = function(e) {
        // replace with own code
    };

    $MJK_PHP5_BOILERPLATE.events.pageLoaded = $MJK_PHP5_BOILERPLATE.events.__defaultPageLoaded;
}

// functions
{
    /**
     * Keeps sure to return an object as (getter) function.
     *
     * @param {Object} obj The input object.
     *
     * @return {function} The object as function.
     */
    $MJK_PHP5_BOILERPLATE.asFunc = function(obj) {
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
    $MJK_PHP5_BOILERPLATE.asJQuery = function(obj) {
        if (!this.isJQuery(obj)) {
            obj = jQuery(obj);
        }

        return obj;
    };

    /**
     * Creates an object for batch operations.
     *
     * @param {Object} [opts] Additional options.
     *
     * @return {Object} The created object.
     */
    $MJK_PHP5_BOILERPLATE.createBatch = function(opts) {
        opts = jQuery.extend({
            'initialResult': null,
            'initialPrevValue': null,
            'initialValue': null,
            'state': null,
            'stopOnFirstError': true
        }, opts);

        var result = {};

        var batchItems = [];

        /**
         * Adds a new batch item.
         *
         * @param {Function} fn The function to invoke.
         * @param {mixed} [...arg] The arguments for the function.
         *
         * @chainable
         */
        result.add = function(fn) {
            var args = [];
            for (var i = 1; i < arguments.length; i++) {
                args.push(arguments[i]);
            }

            return this.addArray(fn, args);
        };

        /**
         * Adds a new batch item.
         *
         * @param {Function} fn The function to invoke.
         * @param {Array} args The arguments for the function.
         *
         * @chainable
         */
        result.addArray = function(fn, args) {
            if (!args) {
                args = [];
            }

            var newItem = {
                'args': args,
                'func': fn
            };

            batchItems.push(newItem);
            return this;
        };

        /**
         * Removes all items.
         *
         * @chainable
         */
        result.clear = function() {
            batchItems = [];
            return this;
        };

        /**
         * Gets the number of batch items.
         *
         * @property count
         * @type Number
         * @readonly
         */
        Object.defineProperty(result, 'count', {
            get: function () {
                return this.length;
            }
        });

        /**
         * Gets the number of batch items.
         *
         * @property length
         * @type Number
         * @readonly
         */
        Object.defineProperty(result, 'length', {
            get: function () {
                return batchItems.length;
            }
        });

        /**
         * Invokes a specific item.
         *
         * @param {Number} index The zero based index.
         *
         * @returns {Object} The result.
         */
        result.invoke = function(index) {
            var item = batchItems[index];

            return $MJK_PHP5_BOILERPLATE.invokeArray(item.func, item.args);
        };

        /**
         * Removes a specific batch item.
         *
         * @param {Number} index The zero based index.
         *
         * @chainable
         */
        result.removeAt = function(index) {
            batchItems.splice(index, 1);

            return this;
        };

        /**
         * Runs all batch items.
         *
         * @param {Object} [opts2] Additional options.
         *
         * @returns {Object} The result.
         */
        result.run = function(opts2) {
            opts2 = jQuery.extend({
                'stopOnFirstError': !!opts.stopOnFirstError
            }, opts2);

            var batchResult = {
                'batch': result,
                'hasBeenCanceled': false,
                'hasFailed': false,
                'results': [],
                'state': opts.state
            };

            var prevValue = opts.initialPrevValue;
            var allResult = opts.initialResult;
            var value = opts.initialValue;
            for (var i = 0; i < batchItems.length; i++) {
                var item = batchItems[i];

                var ctx = {
                    'args': item.args,
                    'batch': result,
                    'batchResult': allResult,
                    'cancel': false,
                    'func': item.func,
                    'prevValue': prevValue,
                    'nextValue': null,
                    'skip': false,
                    'state': opts.state,
                    'value': value
                };

                var updateAllResult = function() {
                    allResult = ctx.batchResult;
                };

                /**
                 * Gets the zero based index of the item.
                 *
                 * @property index
                 * @type Number
                 * @readonly
                 */
                Object.defineProperty(ctx, 'index', {
                    get: function() {
                        return i;
                    }
                });

                /**
                 * Gets if this is the first item or not.
                 *
                 * @property index
                 * @type Boolean
                 * @readonly
                 */
                Object.defineProperty(ctx, 'isFirst', {
                    get: function() {
                        return 0 == this.index;
                    }
                });

                /**
                 * Gets if this is the last item or not.
                 *
                 * @property index
                 * @type Boolean
                 * @readonly
                 */
                Object.defineProperty(ctx, 'isLast', {
                    get: function() {
                        return (batchItems.length - 1) == this.index;
                    }
                });

                if (opts.beforeExecute) {
                    opts.beforeExecute(ctx);
                }

                if (ctx.cancel) {
                    batchResult.hasBeenCanceled = true;

                    updateAllResult();
                    break;
                }

                if (ctx.skip) {
                    updateAllResult();
                    continue;
                }

                var func = this.wrap(i);

                var r = func();
                batchResult.results.push(r);

                ctx.result = r;

                if (!r.hasFailed) {
                    if (opts.onSuccess) {
                        opts.onSuccess(ctx);
                    }
                }
                else {
                    ctx.error = r.error;
                    ctx.errorHandled = false;

                    if (opts.onError) {
                        opts.onError(ctx);
                    }

                    if (!ctx.errorHandled && opts2.stopOnFirstError) {
                        batchResult.hasFailed = true;

                        updateAllResult();
                        break;
                    }
                }

                if (opts.onComplete) {
                    opts.onComplete(ctx);
                }

                if (ctx.cancel) {
                    batchResult.hasBeenCanceled = true;

                    updateAllResult();
                    break;
                }

                prevValue = ctx.nextValue;
                value = ctx.value;

                updateAllResult();
            }

            batchResult.allResult = allResult;

            return batchResult;
        };

        /**
         * Wraps one or more batch items to simple functions.
         *
         * @param {Number} [...index] One or more zero based indexes.
         *
         * @returns {mixed} If there is only one index defined, the function is returned.
         *                  Otherwise an array with functions.
         */
        result.wrap = function() {
            var wrappedFuncs = this.wrapArray(arguments);

            if (1 == wrappedFuncs.length) {
                return wrappedFuncs[0];
            }

            return wrappedFuncs;
        };

        /**
         * Wraps all batch items to simple functions.
         *
         * @returns {Array} The functions.
         */
        result.wrapAll = function() {
            var wrappedFuncs = [];
            for (var i = 0; i < this.length; i++) {
                wrappedFuncs.push(this.wrap(i));
            }

            return wrappedFuncs;
        };

        /**
         * Wraps one or more batch items to simple functions.
         *
         * @param {Array} [indexes] One or more zero based indexes.
         *
         * @returns {mixed} If there is only one index defined, the function is returned.
         *                  Otherwise an array with functions.
         */
        result.wrapArray = function(indexes) {
            if (!indexes) {
                indexes = [];
            }

            var wrappedFuncs = [];

            var createFuncWrapper = function(item) {
                return function() {
                    return $MJK_PHP5_BOILERPLATE.invokeArray(item.func, item.args);
                };
            };

            for (var i = 0; i < indexes.length; i++) {
                wrappedFuncs.push(createFuncWrapper(batchItems[indexes[i]]));
            }

            return wrappedFuncs;
        };

        return result;
    };

    /**
     * Creates a function iterator.
     *
     * @returns {Object} The created iterator.
     */
    $MJK_PHP5_BOILERPLATE.createFunctionIterator = function() {
        var result = {};

        var iteratorItems = [];
        var currentIndex = 0;

        var repairCurrentIndex = function() {
            if (currentIndex >= iteratorItems.length) {
                currentIndex = iteratorItems.length - 1;
            }

            if (currentIndex < 0) {
                currentIndex = 0;
            }
        };

        /**
         * Adds a new iterator item.
         *
         * @param {Function} fn The function to invoke.
         * @param {mixed} [...arg] The arguments for the function.
         *
         * @chainable
         */
        result.add = function(fn) {
            var args = [];
            for (var i = 1; i < arguments.length; i++) {
                args.push(arguments[i]);
            }

            return this.addArray(fn, args);
        };

        /**
         * Adds a new iterator item.
         *
         * @param {Function} fn The function to invoke.
         * @param {Array} args The arguments for the function.
         *
         * @chainable
         */
        result.addArray = function(fn, args) {
            if (!args) {
                args = [];
            }

            var newItem = {
                'args': args,
                'func': fn
            };

            iteratorItems.push(newItem);
            return this;
        };

        /**
         * Removes all iterator items and resets the iterator.
         *
         * @chainable
         */
        result.clear = function() {
            iteratorItems = [];
            this.reset();

            return this;
        };

        /**
         * Goes to the first item.
         *
         * @chainable
         */
        result.gotoFirst = function() {
            return this.reset();
        };

        /**
         * Goes to the last item.
         *
         * @chainable
         */
        result.gotoLast = function() {
            currentIndex = this.length - 1;

            repairCurrentIndex();
            return this;
        };

        /**
         * Invokes all functions from the beginning to the end.
         *
         * @param {Object} [opts2] Additional options.
         *
         * @returns {Array} The list of results.
         */
        result.invokeAll = function(opts2) {
            opts2 = jQuery.extend({
                'restoreIndex': true
            }, opts2);

            this.reset();

            return this.invokeRest({
                'restoreIndex': opts2.restoreIndex
            });
        };

        /**
         * Invokes the next item.
         *
         * @param {Object} [opts2] Additional options.
         *
         * @returns {Object} The result or (false) if there are no more item.
         */
        result.invokeNext = function(opts2) {
            opts2 = jQuery.extend({
                'moveNext': true
            }, opts2);

            if (!this.hasNext) {
                return false;
            }

            var item = iteratorItems[currentIndex];
            var funcResult = $MJK_PHP5_BOILERPLATE.invokeArray(item.func, item.args);

            if (opts2.moveNext) {
                ++currentIndex;
            }

            return funcResult;
        };

        /**
         * Invokes all functions from the current position to the end.
         *
         * @param {Object} [opts2] Additional options.
         *
         * @returns {Array} The list of results.
         */
        result.invokeRest = function(opts2) {
            opts2 = jQuery.extend({
                'restoreIndex': false
            }, opts2);

            var indexToRestore = currentIndex;

            var results = [];

            while (!this.eof) {
                results.push(this.invokeNext());
            }

            if (opts2.restoreIndex) {
                currentIndex = indexToRestore;
            }

            return results;
        };

        /**
         * Invokes the first function and removes it by restoring the
         * last index.
         *
         * @param {Object} [opts2] Additional options.
         *
         * @returns {Object} The result or (false) if there are no more item.
         */
        result.popFirst = function(opts2) {
            opts2 = jQuery.extend({
                'removeOnError': true,
                'restoreIndex': true
            }, opts2);

            var indexToRestore = currentIndex;

            this.reset();

            var funcResult = this.invokeNext({
                'moveNext': false
            });

            if (funcResult) {
                var removeItem = true;

                if (funcResult.hasFailed) {
                    removeItem = !!opts2.removeOnError;
                }

                if (removeItem) {
                    this.removeFirst();
                }
            }

            if (opts2.restoreIndex) {
                currentIndex = indexToRestore - 1;
            }

            repairCurrentIndex();

            return funcResult;
        };

        /**
         * Invokes the current function and removes it.
         *
         * @param {Object} [opts2] Additional options.
         *
         * @returns {Object} The result or (false) if there are no more item.
         */
        result.popNext = function(opts2) {
            opts2 = jQuery.extend({
                'removeOnError': true
            }, opts2);

            var funcResult = this.invokeNext({
                'moveNext': false
            });

            if (funcResult) {
                var removeItem = true;

                if (funcResult.hasFailed) {
                    removeItem = !!opts2.removeOnError;
                }

                if (removeItem) {
                    this.removeCurrent();
                }
            }

            return funcResult;
        };

        /**
         * Removes a specific iterator item.
         *
         * @param {Number} index The zero based index.
         *
         * @chainable
         */
        result.removeAt = function(index) {
            if ((index >= 0) && (index < this.length)) {
                iteratorItems.splice(index, 1);
            }

            repairCurrentIndex();

            return this;
        };

        /**
         * Removes a the current item.
         *
         * @chainable
         */
        result.removeCurrent = function() {
            return this.removeAt(currentIndex);
        };

        /**
         * Removes a specific iterator item.
         *
         * @param {Number} index The zero based index.
         *
         * @chainable
         */
        result.removeFirst = function() {
            return this.removeAt(0);
        };

        /**
         * Resets the iterator.
         *
         * @chainable
         */
        result.reset = function() {
            currentIndex = 0;
            return this;
        };

        /**
         * Wraps one or more iterator items to simple functions.
         *
         * @param {Number} [...index] One or more zero based indexes.
         *                            If no index is defined, the current index is used.
         *
         * @returns {mixed} If there is only one index defined, the function is returned.
         *                  Otherwise an array with functions.
         */
        result.wrap = function() {
            var indexes = arguments;
            if (indexes.length < 1) {
                indexes = [currentIndex];
            }

            var wrappedFuncs = this.wrapArray(indexes);

            if (1 == wrappedFuncs.length) {
                return wrappedFuncs[0];
            }

            return wrappedFuncs;
        };

        /**
         * Wraps all iterator items to simple functions.
         *
         * @returns {Array} The functions.
         */
        result.wrapAll = function() {
            var wrappedFuncs = [];
            for (var i = 0; i < this.length; i++) {
                wrappedFuncs.push(this.wrap(i));
            }

            return wrappedFuncs;
        };

        /**
         * Wraps one or more iterator items to simple functions.
         *
         * @param {Array} [indexes] One or more zero based indexes.
         *
         * @returns {mixed} If there is only one index defined, the function is returned.
         *                  Otherwise an array with functions.
         */
        result.wrapArray = function(indexes) {
            if (!indexes) {
                indexes = [];
            }

            var wrappedFuncs = [];

            var createFuncWrapper = function(item) {
                return function() {
                    return $MJK_PHP5_BOILERPLATE.invokeArray(item.func, item.args);
                };
            };

            for (var i = 0; i < indexes.length; i++) {
                wrappedFuncs.push(createFuncWrapper(batchItems[indexes[i]]));
            }

            return wrappedFuncs;
        };

        /**
         * Gets the number of iterator items.
         *
         * @property count
         * @type Number
         * @readonly
         */
        Object.defineProperty(result, 'count', {
            get: function () {
                return this.length;
            }
        });

        /**
         * Gets the current index.
         *
         * @property currentIndex
         * @type Number
         * @readonly
         */
        Object.defineProperty(result, 'currentIndex', {
            get: function () {
                return currentIndex;
            }
        });

        /**
         * Gets if the iterator has reached the end or not.
         *
         * @property eof
         * @type Boolean
         * @readonly
         */
        Object.defineProperty(result, 'eof', {
            get: function () {
                return this.currentIndex >= this.length;
            }
        });

        /**
         * Gets if the iterator has currently an item to invoke or not.
         *
         * @property hasNext
         * @type Boolean
         * @readonly
         */
        Object.defineProperty(result, 'hasNext', {
            get: function () {
                return !this.eof;
            }
        });

        /**
         * Gets if the iterator is at the beginning or not.
         *
         * @property isFirst
         * @type Boolean
         * @readonly
         */
        Object.defineProperty(result, 'isFirst', {
            get: function () {
                return 0 == this.currentIndex;
            }
        });

        /**
         * Gets if the iterator is at the end or not.
         *
         * @property isLast
         * @type Boolean
         * @readonly
         */
        Object.defineProperty(result, 'isLast', {
            get: function () {
                return (this.length - 1) == this.currentIndex;
            }
        });

        /**
         * Gets the number of iterator items.
         *
         * @property length
         * @type Number
         * @readonly
         */
        Object.defineProperty(result, 'length', {
            get: function () {
                return iteratorItems.length;
            }
        });

        return result;
    };

    /**
     * Executes JavaScript code globally.
     *
     * @param {String} code The JavaScript code to execute.
     *
     * @return {mixed} The result of the execution.
     */
    $MJK_PHP5_BOILERPLATE.eval = function(code) {
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
    $MJK_PHP5_BOILERPLATE.invoke = function(fn) {
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
     * @param {Object} [opts] Additional options.
     *
     * @return {Object} The result object of the invocation.
     */
    $MJK_PHP5_BOILERPLATE.invokeArray = function(fn, args, opts) {
        if (fn) {
            fn = this.asFunc(fn);
        }

        if (!args) {
            args = [];
        }

        opts = jQuery.extend({
            'state': null,
            'throwOnError': false
        }, opts);

        var result = {
            'args': args,
            'hasBeenInvoked': false,
            'state': opts.state,
            'throwOnError': function() {
                if (this.hasFailed) {
                    throw this.error;
                }
            }
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

        if (opts.throwOnError) {
            result.throwOnError();
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
    $MJK_PHP5_BOILERPLATE.isFunc = function(obj) {
        return jQuery.isFunction(obj);
    };

    /**
     * Checks if an object is a jQuery object or not.
     *
     * @param {Object} obj The object to check.
     *
     * @return {Boolean} Is a jQuery object or not.
     */
    $MJK_PHP5_BOILERPLATE.isJQuery = function(obj) {
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
Object.defineProperty($MJK_PHP5_BOILERPLATE, 'now', {
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
Object.defineProperty($MJK_PHP5_BOILERPLATE, 'nowUTC', {
    get: function() {
        var n = this.now;

        return new Date(n.getUTCFullYear(), n.getUTCMonth(), n.getUTCDate(),
                        n.getUTCHours(), n.getUTCMinutes(), n.getUTCSeconds(), n.getUTCMilliseconds());
    }
});

// vars, elements and functions
{
    // this is for internal use only!
    $MJK_PHP5_BOILERPLATE.__onLoadedActions = [];

    $MJK_PHP5_BOILERPLATE.elements = {};  // elements and selectors
    $MJK_PHP5_BOILERPLATE.funcs = {};  // functions
    $MJK_PHP5_BOILERPLATE.vars = {};  // variables / values

    /**
     * Adds a function that is called when the page has been loaded.
     *
     * @param {function} action The action to invoke.
     * @param {Object} opts Additional options.
     *
     * @chainable
     */
    $MJK_PHP5_BOILERPLATE.addOnLoaded = function(action, opts) {
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

    /**
     * Process all actions that were added by $MJK_PHP5_BOILERPLATE.addOnLoaded() method.
     *
     * @return {Boolean} Loaded actions were processed or not.
     */
    $MJK_PHP5_BOILERPLATE.processOnLoadedActions = function() {
        if (!$MJK_PHP5_BOILERPLATE.events.pageLoaded) {
            return false;
        }

        var e = {
            'time': $MJK_PHP5_BOILERPLATE.now
        };

        e.invokeDefault = function() {
            $MJK_PHP5_BOILERPLATE.events.__defaultPageLoaded(e);
        };

        $MJK_PHP5_BOILERPLATE.events.pageLoaded(e);

        return true;
    };

    /**
     * Adds an (element) selector.
     * The selectors can be accessed by using $MJK_PHP5_BOILERPLATE.elements property.
     *
     * @param {String} name The name of the selector.
     * @param {jQuery} selector The selector.
     * @param {Object} [opts] Additional options.
     *
     * @chainable
     */
    $MJK_PHP5_BOILERPLATE.addElements = function(name, selector, opts) {
        opts = jQuery.extend({
        }, opts);

        var getSelector;
        if (!$MJK_PHP5_BOILERPLATE.isJQuery(selector)) {
            getSelector = function() {
                return $MJK_PHP5_BOILERPLATE.asJQuery(selector);
            };
        }
        else {
            getSelector = function() {
                return selector;
            };
        }

        if (opts.onLoaded) {
            this.addOnLoaded(function() {
                var ctx = {};

                Object.defineProperty(ctx, 'selector', {
                    get: getSelector
                });

                opts.onLoaded(ctx);
            });
        }

        Object.defineProperty(this.elements, jQuery.trim(name), {
            get: getSelector
        });

        return this;
    };

    /**
     * Adds a function.
     * The functions can be accessed by using $MJK_PHP5_BOILERPLATE.funcs property.
     *
     * @param {String} name The name of the function.
     * @param {function} func The function.
     *
     * @chainable
     */
    $MJK_PHP5_BOILERPLATE.addFunction = function(name, func) {
        Object.defineProperty(this.funcs, jQuery.trim(name), {
            get: function() {
                return $MJK_PHP5_BOILERPLATE.asFunc(func);
            }
        });

        return this;
    };

    /**
     * Adds a variable.
     * The variables can be accessed by using $MJK_PHP5_BOILERPLATE.vars property.
     *
     * @param {String} name The name of the function.
     * @param {mixed} value The value or the function that provides it.
     *
     * @chainable
     */
    $MJK_PHP5_BOILERPLATE.addVar = function(name, value) {
        Object.defineProperty(this.vars, jQuery.trim(name), {
            get: $MJK_PHP5_BOILERPLATE.asFunc(value)
        });

        return this;
    };
}

// alias
if ('undefined' === typeof $php5bp) {
    $php5bp = $MJK_PHP5_BOILERPLATE;
}
