# php5boilerplate

PHP 5 based and modular framework for starting websites from scratch.

## Requirements

* PHP 5.5+

## Included

* [phpLINQ](https://github.com/mkloubert/phpLINQ)
* [Zend Framework 2](http://framework.zend.com/)

## Getting started

### Global application settings

Create or edit the file **app.json** in the folder **sys/conf**.

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

**modules.var stores** the HTTP variable name for the **action name**. This is handled by the **.htaccess** file

```apache
RewriteRule ^(.*)$ index.php?CE4EBCB3=$1 [QSA,L]
```

and should only be changed if you want to use another name. The **default value** is **module**.

**modules.actions** defines where the system **looks for the action name**. In that example it tries to find the name in the [$_GET](http://php.net/manual/en/reserved.variables.get.php) and than in the [$_POST](http://php.net/manual/en/reserved.variables.post.php) array.

If you want to see more **debug information** you have to **set the debug flag** to **(true)**.

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




