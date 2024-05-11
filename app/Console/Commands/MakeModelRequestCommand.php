<?php

namespace App\Console\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Foundation\Console\RequestMakeCommand;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:model-request')]
class MakeModelRequestCommand extends RequestMakeCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:model-request';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        //If we've given a model we're making a new model action
        return $this->resolveStubPath('/stubs/model-request.stub');
    }

    /**
     * Gets the final path to the stub
     *
     * @param string $stub
     *
     * @return string
     */
    protected function resolveStubPath($stub): string
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : $this->laravel->basePath("app/Console/{$stub}");
    }

    /**
     * Build the class with the given name.
     *
     * @param string $name
     *
     * @return string
     * @throws FileNotFoundException
     */
    protected function buildClass($name): string
    {
        $baseName   = Str::of($name)->classBasename()->replace('Request', '');
        $modelClass = App::getNamespace().'Models\\'.($this->option('model') ?? $baseName);

        $content = parent::buildClass($name);

        $content = str_replace(
            '{{ namespacedModel }}',
            $modelClass,
            $content
        );

        return str_replace(
            '{{ model }}',
            Str::of($modelClass)->classBasename(),
            $content
        );
    }

    protected function getOptions(): array
    {
        return [
            ['model', null, InputOption::VALUE_OPTIONAL, "The model we're generating the request for"],
            ...parent::getOptions(),
        ];
    }
}
