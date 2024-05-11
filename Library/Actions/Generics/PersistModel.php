<?php

namespace Library\Actions\Generics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Library\Actions\ModelAction;

class PersistModel extends ModelAction
{
    /**
     * Handles storing and updating a model
     */
    public function handle(Model $model, FormRequest $request): Model
    {
        return tap($model)
            ->fill($request->validated())
            ->save();
    }
}
