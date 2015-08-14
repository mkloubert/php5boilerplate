<?php

namespace php5bp\Modules\Impl\Test;

use \php5bp\Modules\ModuleBase;
use \php5bp\Modules\Execution\ContextInterface as ModuleExecutionContext;


class TestModule extends ModuleBase {
    protected function execute(ModuleExecutionContext $ctx) {
        echo "test";
    }

    public function test2Action(ModuleExecutionContext $ctx, array $args, array &$result) {
        $result['code'] = 666;

        echo "test";
    }
}
