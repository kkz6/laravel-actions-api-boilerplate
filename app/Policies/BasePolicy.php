<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

abstract class BasePolicy
{
    use HandlesAuthorization;

    /**
     * Allows us to create a more basic policy where we just give the method name to validate against the gate for a model.
     */
    protected function check(string $calledMethod): Response
    {
        $calledMethod  = Str::snake($calledMethod, ' ');
        $modelBasename = Str::snake(class_basename($this->model));

        return Gate::allows("{$calledMethod} {$modelBasename}")
            ? $this->allow()
            : $this->deny();
    }
}
