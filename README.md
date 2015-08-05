# php5boilerplate

PHP 5 based and modular framework for starting websites from scratch.

## Requirements

* PHP 5.5+

## Included

* [AngularJS](https://angularjs.org/)
* [jQuery](https://jquery.com/)
* [normalize.css](https://github.com/necolas/normalize.css/)
* [phpLINQ](https://github.com/mkloubert/phpLINQ/)
* [Slim](http://www.slimframework.com/)
* [Zend Framework](http://framework.zend.com/)

## Getting started

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

**modules.var stores** the HTTP variable for the **action name**. This is handled by the **.htaccess** file

```apache
RewriteRule ^(.*)$ index.php?CE4EBCB3=$1 [QSA,L]
```

and should only be changed if you want to use another name. The **default value** is **module**.

**modules.actions** defines where the system **looks for the action name**. In that example it tries to find the name in the [$_GET](http://php.net/manual/en/reserved.variables.get.php) and than in the [$_POST](http://php.net/manual/en/reserved.variables.post.php) array.

If you want to see more **debug information** (via [FirePHP](https://github.com/firephp/firephp-core) or [ChromePHP](https://github.com/ccampbell/chromephp) e.g.) you have to **set the debug flag** to **(true)**.

### Create module

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

Add a **meta.json** file inside your module's folder and define the full class name:

```json
{
  "class": "\\MyPage\\Modules\\MyModule"
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

        <title><?= \htmlentities($this->title) ?></title>

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

