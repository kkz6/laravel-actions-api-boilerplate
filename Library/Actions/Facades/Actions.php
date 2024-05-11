<?php

namespace Library\Actions\Facades;

use Illuminate\Support\Facades\Facade;
use Library\Actions\ActionManager;

class Actions extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return ActionManager::class;
    }
}
