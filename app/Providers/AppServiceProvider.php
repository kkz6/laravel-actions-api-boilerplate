<?php

namespace App\Providers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /**
         * Checks if the user has permissions to create or update a given model
         *
         * @param class-string $model
         *
         * @return bool
         */
        FormRequest::macro(
            name: 'authorizeModel',
            macro: fn (string $model) => Gate::allows(
                ability: $this->route()->hasParameter(
                    name: $modelName = (string) Str::of($model)->classBasename()->snake()
                )
                    ? "update $modelName"
                    : "create $modelName"
            )
        );
    }
}
