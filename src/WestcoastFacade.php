<?php

namespace Ridown\Westcoast;

use Illuminate\Support\Facades\Facade;

class WestcoastFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'westcoast';
    }
}
