<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;


class MakeEnum extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:enum {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a enum class';

    protected $type = 'Enum';


    /**
     * Finds the full path to the correct stub based on defined options
     *
     * @return string
     */
    protected function getStub(): string
    {
        //If we've given a model we're making a new model action
        return $this->resolveStubPath('/stubs/enum.stub');
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
            : App::path("Console/{$stub}");
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
        return $rootNamespace . '\Enums';
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
        return parent::qualifyClass(Str::finish($name, 'Enum'));
    }

    /**
     * Generates a list of values to replace in the stub.
     */
    protected function buildReplacements(string $class, string $type): array
    {
        $class       = Str::replace('/', '\\', $class);
        $ucFirstType = Str::ucfirst($type);

        return [
            "{{ {$type} }}"                                   => Str::of($class)->classBasename(),
            "{{ {$type}Plural }}"                             => Str::of($class)->classBasename()->plural(),
            "{{ namespaced{$ucFirstType} }}"                  => $class,
            "{{ {$type}VariableName }}"                       => Str::of($class)->classBasename()->camel()->singular(),
            "{{ {$type}PluralVariableName }}"                 => Str::of($class)->classBasename()->camel()->plural(),
            "{{ {$type}SnakedVariableName }}"                 => Str::of($class)->classBasename()->snake()->singular(),
            "{{ {$type}HyphenatedSnakedPluralVariableName }}" => Str::of($class)->classBasename()->snake('-')->plural(),
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [];
    }
}
