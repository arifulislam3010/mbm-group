<?php

namespace Muktopaath\Course\Lib;;

trait CacheHelper
{
    public function getExpiryDuration()
    {
        return 14*24*60*60;
    }
}