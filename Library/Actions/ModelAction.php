<?php

namespace Library\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Library\Actions\Concerns\AsAction;
use Library\Actions\Contracts\WithTransaction;
use Library\Actions\Facades\Actions;

abstract class ModelAction implements WithTransaction
{
    use AsAction {
        run as baseRun;
    }

    /**
     * Whether we should be sending flash messages for the given model
     */
    protected static bool $emitFlashMessages = true;

    /**
     * Run the action inside a transaction when dealing with models
     *
     */
    public static function run(Model $model, FormRequest $request = null, ...$args): Model
    {
        $model = static::baseRun($model, $request, ...$args);

        if (static::$emitFlashMessages && ! Actions::flashMessagesDisabled()) {
            Session::flash(
                key: 'success',
                value: Str::of(string: $model::class)
                    ->classBasename()
                    ->headline().' Saved Successfully'
            );
        }

        return $model;
    }
}
