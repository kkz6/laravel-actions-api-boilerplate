<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputOption;

class MakeActionCommand extends GeneratorCommand
{
    protected $name = 'make:action';
    protected $description = 'Generates a new action class';

    protected $type = 'Action';

    /**
     * Finds the full path to the correct stub based on defined options
     *
     * @return string
     */
    protected function getStub(): string
    {
        //If we've given a model we're making a new model action
        return $this->resolveStubPath(
            $this->option('model')
                ? '/stubs/action.model.stub'
                : '/stubs/action.stub'
        );
    }

    /**
     * Gets the final path to the stub
     *
     * @param string $stub
     *
     * @return string
     */
    protected function resolveStubPath(string $stub): string
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : $this->laravel->basePath("app/Console/{$stub}");
    }

    /**
     * Gets the namespace for the class to use
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\Actions';
    }

    /**
     * Gets the name for the class to create
     *
     * @param string $name
     *
     * @return string
     */
    protected function qualifyClass($name): string
    {
        return parent::qualifyClass(Str::finish($name, 'Action'));
    }


    /**
     * Build the class with the given name.
     *
     * Remove the base controller import if we are already in the base namespace.
     *
     * @param string $name
     *
     * @return string
     */
    protected function buildClass($name): string
    {
        $replace = [
            '{{ maybeImplementsWithTransaction }}' => $this->option('transaction')
                ? 'implements WithTransaction'
                : '',
        ];

        if ($this->option('model')) {
            $replace = $this->buildModelReplacements($replace);
        }

        return str_replace(
            array_keys($replace),
            array_values($replace),
            parent::buildClass($name)
        );
    }

    /**
     * Build the model replacement values.
     *
     *
     * @param array $replace
     * @return array
     */
    protected function buildModelReplacements(array $replace): array
    {
        $modelClass = $this->parseModel($this->option('model'));

        if (!class_exists($modelClass) && $this->components->confirm("A {$modelClass} model does not exist. Do you want to generate it?", true)) {
            $this->call('make:model', ['name' => $modelClass]);
        }

        $replace = $this->buildFormRequestReplacements($replace, $modelClass);

        return array_merge($replace, [
            '{{ namespacedModel }}' => $modelClass,
            '{{ model }}'           => class_basename($modelClass),
            '{{ modelVariable }}'   => lcfirst(class_basename($modelClass)),
        ]);
    }

    /**
     * Build the model replacement values.
     *
     * @param array $replace
     * @param string $modelClass
     *
     * @return array
     */
    protected function buildFormRequestReplacements(array $replace, string $modelClass): array
    {
        $namespace = 'App\\Http\\Requests';

        $requestClass = $this->generateFormRequest($modelClass, $namespace);

        return array_merge($replace, [
            '{{ request }}'           => class_basename($requestClass),
            '{{ namespacedRequest }}' => $namespace.'\\'.str_replace('/', '\\', $requestClass),
        ]);
    }

    /**
     * Generate the form request for the given model.
     *
     * @param string $modelClass
     * @param string $namespace
     *
     * @return string
     */
    protected function generateFormRequest(string $modelClass, string $namespace): string
    {
        $storeRequestClass = $this->option('request') ?? (class_basename($modelClass).'Request');

        if (!class_exists("{$namespace}\\{$storeRequestClass}")) {
            $this->call('make:request', [
                'name' => $storeRequestClass,
            ]);
        }

        return $storeRequestClass;
    }

    /**
     * Get the fully-qualified model class name.
     *
     * @param string $model
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    protected function parseModel($model): string
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }

        return $this->qualifyModel($model);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'Generate a resource controller for the given model'],
            ['request', 'r', InputOption::VALUE_OPTIONAL, 'Generate a FormRequest class for the action (Only applies when a model is also given)'],
            ['transaction', 't', InputOption::VALUE_NONE, 'If the action should be a transaction (Only applied when no model is provided)'],
        ];
    }
}
