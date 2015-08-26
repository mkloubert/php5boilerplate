# php5boilerplate

PHP 5 based and modular framework for starting websites and/or Web-APIs from scratch.

The documentation can be found at the [wiki](https://github.com/mkloubert/php5boilerplate/wiki).

## Requirements

* PHP 5.5+

## Included

* [AngularJS](https://angularjs.org/)
* [jQuery](https://jquery.com/)
* [normalize.css](https://github.com/necolas/normalize.css/)
* [phpDeeBuk](https://github.com/mkloubert/phpDeeBuk/)
* [phpLINQ](https://github.com/mkloubert/phpLINQ/)
* [Slim](http://www.slimframework.com/)
* [Zend Framework](http://framework.zend.com/)
* [Zend PDF](https://github.com/zendframework/ZendPdf)

## Getting started

If you want to do a quick setup, have a look at the [Quick Start Guide](https://github.com/mkloubert/php5boilerplate/wiki/quick-start-guide) at the wiki.

### Global application settings

Create or edit the file **app.json** in the **sys/conf** folder.

```json
{
  "debug": false,
  "modules": {
    "actions": {
        "source": ["get", "post"]
    },
    "var": "CE4EBCB3"
  }
}
```

**modules.var** stores the HTTP variable for the **action name**. This is handled by the **.htaccess** file

```apache
RewriteRule ^(.*)$ index.php?CE4EBCB3=$1 [QSA,L]
```

and should only be changed if you want to use another name. The **default value** is **module**.

**modules.actions** defines where the system **looks for the action name**. In that example it tries to find the name in the [$_GET](http://php.net/manual/en/reserved.variables.get.php) and than in the [$_POST](http://php.net/manual/en/reserved.variables.post.php) array.

If you want to see more **debug information** (via [FirePHP](https://github.com/firephp/firephp-core) or [ChromePHP](https://github.com/ccampbell/chromephp) e.g.) you have to **set the debug flag** to **(true)**.

### Create a module

Create a directory inside the **sys/modules** folder.

For example, if you want to create a module called **mymodule**, create the folder **sys/modules/mymodule**.

Now you have to create an **index.php** file there. You can use the following content as skeleton:

```php
namespace MyPage\Modules;

use \php5bp\Modules\ModuleBase;
use \php5bp\Modules\Execution\ContextInterface as ModuleExecutionContext;


class MyModule extends ModuleBase {
    protected function execute(ModuleExecutionContext $ctx) {
        ?>
            <h1>Hello, World!</h1>
        <?php
    }
}
```

The system needs to know what class should be used for a module instance.

Add a **meta.json** file inside your module's folder and **define the full class name**:

```json
{
  "class": "\\MyPage\\Modules\\MyModule"
}
```

Now you only need to open the URL `/mymodule` to invoke the module.

The **default module** is called **index**.

#### Define an action for a module

Edit the **meta.json** file and define the action(s) there:

```json
{
  "class": "\\MyPage\\Modules\\MyModule",
  "actions": {
     "myFormAction": "processForm",
     "myJsonAction": "processJson"
  }
}
```

The **key** of each entry **defines the name of the action**, the **value the name of the method** inside the module class. 

These methods are invoked instead of the `execute()` method.

Each method HAS TO BE public and non-static.

```php
namespace MyPage\Modules;

use \php5bp\Modules\ModuleBase;
use \php5bp\Modules\Execution\ContextInterface as ModuleExecutionContext;


class MyModule extends ModuleBase {
    protected function execute(ModuleExecutionContext $ctx) {
        ?>
            <!-- call 'myFormAction' action -->
            <form action="/mymodule" method="POST">
                <input type="hidden" name="action" value="myFormAction">
                
                <input type="submit">
            </form>
            
            <input type="button" onclick="callJsonAction()">
            
            <script type="text/javascript">
            
                function callJsonAction() {
                    $.ajax({
                        'url': '/mymodule',
                        'type': 'POST',
                        'data': {
                            'action': 'myJsonAction'
                        },
                        
                        'success': function(data) {
                            // process result of 'myJsonAction'
                        }
                    });
                }
            
            </script>
        <?php
    }
    
    // myFormAction
    public function processForm(ModuleExecutionContext $ctx) {
        // process the form of 'execute' method
    }
    
    // myJsonAction
    public function processJson(ModuleExecutionContext $ctx) {
        $ctx->setupForJson();
        
        $jsonResult = array();
        
        // do something that returns JSON conform data
        
        return $jsonResult;
    }
}
```

### Website design

Edit or create the **index.phtml** file in the **sys/views/main** folder.

Here is an example:

```php
<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>My page :: <?= \htmlentities($this->title) ?></title>

        <link rel="stylesheet" href="css/normalize-3.0.3.css">
        <link rel="stylesheet" href="css/php5bp.min.css">

        <script type="text/javascript" src="js/jquery.min.js"></script>
        <script type="text/javascript" src="js/angular.min.js"></script>

        <!-- this should be included as last script -->
        <script type="text/javascript" src="js/php5bp.min.js"></script>
    </head>

    <body>
        <!-- your module's content -->
        <?= $this->content ?>

        <!-- this should be executed as last script -->
        <script type="text/javascript">
            jQuery(function() {
                $php5bp.processOnLoadedActions();
            });
        </script>
    </body>
</html>
```

The content you output or return in your modules will be stored in the `$this->content` variable.

You do not need to use the JavaScript libraries and the CSS styles from the example. You can completely start with your own stuff.

### Database connections

Create or edit the file **main.json** in the **sys/conf/db** folder.

This is an example for a MySQL database connection:

```json
{
  "driver": "Pdo",
  "dsn": "mysql:dbname=myDatabase;host=localhost",
  "username": "dbUser",
  "password": "dbPassword"
}
```

Now you can use it by calling the `\php5bp::db()` method:

```php
$db = \php5bp::db();

$sql = "SELECT * FROM users;";

$dbResult = $db->query($sql);
               ->execute();
               
foreach ($dbResult as $row) {
    // do things with the current row
}
```

Have a look at [Zend Framework documentation page](http://framework.zend.com/manual/current/en/modules/zend.db.adapter.html) to get more information about adapters and their configurations and how to use them.

#### Create classes for cached rows

The frameworks provides the base class **\php5bp\Db\CachableRowBase** which manages the data of a table's row by storing it in a/the cache.

This is a small example for a row of a table called `users`:

```php
<?php

namespace MyPage\Db\Entities;

class UserRow extends \php5bp\Db\CachableRowBase {
    protected $_id;
    
    public function __construct($id) {
        $this->_id = $id;
        
        parent::__construct();
    }

    // list of columns that represent
    // the primary key of the rows
    public static function idColumns() {
        return array('id');
    }
    
    // the list of values that represent
    // the primary key value
    protected abstract function ids() {
        return array($this->_id);
    }
    
    // the name of the table
    public static function tableName() {
        return 'users';
    }
}
```

That class contains methods like `clearCache()`, `deleteRow()`, `registerDeletedEvent()` or `updateColumn()` which gives you control over the underlying data.

### Caching

By default the system is configured to use the application's memory. The problem is that all cached data will be lost when the script execution has finished.

Create or edit the file **main.json** in the **sys/conf/cache** folder.

This is an example for a [memcached](http://php.net/manual/en/book.memcached.php) server:

```json
{
  "adapter": {
    "name": "memcached",
    "options": {
      "ttl": 7200,
      "namespace": "myPage",
      "servers": [
        ["127.0.0.1", 11211]
      ],
      "liboptions": {
        "COMPRESSION": true,
        "binary_protocol": true,
        "no_block": true
      }
    }
  },
  "plugins": {
    "exception_handler": {
      "throw_exceptions": false
    }
  }
}
```

The following code shows the basic usage of a cache storage by calling `\php5bp::cache()` method:

```php
$cache = \php5bp::cache();

$pz = $cache->getItem('PZ', $hasPZ);

if (!$hasPZ) {
    // keep sure to have an item
    $cache->setItem('PZ', '19861222');
}

// get current value
$pz = $cache->getItem('PZ');

if ($cache->hasItem('PZ')) {
    // remove existing item
    $cache->removeItem('PZ');
}
```

Have a look at [Zend Framework documentation page](http://framework.zend.com/manual/current/en/modules/zend.cache.storage.adapter.html) to get more information about storage adapters and their configurations and how to use them.

### Logging

Use `\php5bp::log()` method to access the global logger:

```php
\php5bp::log()
       ->debug('Something for the developer or tester')
       ->info('An info')
       ->notice('That is something you should know')
       ->warn("That's a warning")
       ->err('Something went wrong!')
       ->crit('This becomes critical!')
       ->alert('ALERT!!!')
       ->emerg('WORST CASE!');
```

Have a look at [Zend Framework documentation page](http://framework.zend.com/manual/current/en/modules/zend.log.overview.html) to get more information about logging.

### Bootstrapping

In the **sys/bootstrap** folder you can define own scripts for initializing your application environment. All files are included in alphabetic order (case-insensitive).

One suggestion is to choose a filename that has a format like `{script_nr_with_leading_zeros}_{script_name}.php`.

For example you can create a file called `0001_logger.php` to add additional log writers:

```php
<?php

defined('PHP5BP_BOOTSTRAP') or die();

use \php5bp\Diagnostics\Log\Logger;
use \php5bp\Diagnostics\Log\Writers\CallableLogWriter;
use \Zend\Log\Filter\Priority as ZendLogFilterPriority;

// write to database
\php5bp::log()
       ->addWriter(new CallableLogWriter(
                       function($event) {
                           // get table gateway for a table called 'logs'
                           $table = \php5bp::table('logs');
                           
                           // define the columns (left) with their values (right)
                           $newEntry = array(
                               'extra'    => json_encode($event['extra']),
                               'message'  => (string)$event['message'],
                               'priority' => $event['priority'],
                               'time'     => $event['timestamp']->format('Y-m-d H:i:s'),
                           );

                           // insert data
                           $table->insert($newEntry);
                       }));
                       
if (!php5bp::isDebug()) {
    // write debug messages in debug mode only

    \php5bp::log()
           ->addFilter(new ZendLogFilterPriority(Logger::DEBUG, "<"));
}
```

### Shutdown

In the **sys/shutdown** folder you can define own scripts for disposing the application environment.

It works the same way like bootstrapping.

### Classes

Classes are stored inside the **sys/classes** folder structure.

If you want to create the class

```php
<?php

// folder 'sys/classes/MyPage/MyNamespace1/MyNamespace2'
namespace MyPage\MyNamespace1\MyNamespace2;

// file 'MyClass.php'
class MyClass {
}
```

for example, you have to store the code into the **sys/classes/MyPage/MyNamespace1/MyNamespace2/MyClass.php** file.
