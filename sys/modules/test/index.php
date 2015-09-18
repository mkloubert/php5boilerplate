<?php

namespace php5bp\Modules\Impl\Test;

use \php5bp\Modules\ModuleBase;
use \php5bp\Modules\Execution\ContextInterface as ModuleExecutionContext;


class TestModule extends ModuleBase {
    protected function execute(ModuleExecutionContext $ctx) {
        echo \php5bp::hash("test", 'main');
    }

    public function __call($method, $args) {
        if ($args != null) {

        }
    }

    public function test2Action(ModuleExecutionContext $ctx, array $args, array &$result) {
        $result['code'] = 666;

        echo "test";
    }
}
