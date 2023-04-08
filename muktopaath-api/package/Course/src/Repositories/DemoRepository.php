<?php

namespace Muktopaath\Course\Repositories;

use Muktopaath\Course\Interfaces\DemoInterface;

class DemoRepository implements DemoInterface
{

    public function test(){
        return 21;
    }
    
}