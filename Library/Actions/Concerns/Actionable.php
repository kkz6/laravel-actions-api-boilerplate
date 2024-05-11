<?php

namespace Library\Actions\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Library\Actions\Generics\PersistModel;

trait Actionable
{
    /**
     * Runs the persist model action against a model
     */
    public function persist(FormRequest $request): Model
    {
        return $this->getModelAction()::run($this, $request);
    }

    /**
     * Gets the model action we should be using for the current model
     *
     * @return class-string
     */
    public function getModelAction(): string
    {
        return self::$modelAction ?? PersistModel::class;
    }
}
