<?php

namespace Library\Actions\Concerns;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Library\Actions\Contracts\WithTransaction;
use Library\Actions\Exceptions\MethodNotFoundException;

trait AsAction
{
    /**
     * Creates a new instance of the action
     *
     * @param array $args
     */
    public static function make(...$args): static
    {
        return App::make(static::class, $args);
    }

    /**
     * Runs the action
     *
     * @param array $args
     */
    public static function run(...$args): mixed
    {
        $instance = static::make();

        //If the defined method doesn't exist we can't run the action
        if (! method_exists($instance, static::getActionMethod())) {
            throw new MethodNotFoundException('The Handle Method Must Be Implemented On Actions', $instance, 'handle', $args);
        }

        //If we've implemented the WithTransaction contract we should be using transactions for the action
        return $instance instanceof WithTransaction
            ? DB::transaction(fn () => $instance->handle(...$args))
            : $instance->handle(...$args);
    }

    /**
     * Gets the name of the method the action should call
     */
    public static function getActionMethod(): string
    {
        return static::$actionMethod ?? 'handle';
    }
}
