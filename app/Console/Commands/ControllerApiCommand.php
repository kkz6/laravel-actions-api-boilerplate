<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class ControllerApiCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:api-controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a new resource controller for a given model api style';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';

    /**
     * Finds the full path to the correct stub based on defined options
     *
     * @return string
     */
    protected function getStub(): string
    {
        //If we've given a model we're making a new model action
        return $this->resolveStubPath('/stubs/controller-api.model.stub');
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
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Http\Controllers';
    }

    /**
     * Build the class with the given name.
     *
     * Remove the base default import if we are already in the base namespace.
     *
     * @param string $name
     *
     * @return string
     */
    protected function buildClass($name)
    {
        $baseName = str_replace('Controller', '', class_basename($name));

        $modelClass                = App::getNamespace().'Models\\'.($this->option('model') ?? $baseName);
        $resourceClass             = App::getNamespace().'Http\\Resources\\'.($this->option('resource') ?? $baseName.'Resource');
        $resourceCollectionClass   = App::getNamespace().'Http\\Resources\\'.($this->option('collection') ?? $baseName.'CollectionResource');
        $requestClass              = App::getNamespace().'Http\\Requests\\'.($this->option('request') ?? $baseName.'Request');
        $actionClass               = App::getNamespace().'Actions\\'.($this->option('action') ?? 'Persist'.$baseName.'Action');

        $replace = array_merge(
            $this->buildReplacements($modelClass, 'model'),
            $this->buildReplacements($resourceClass, 'resource'),
            $this->buildReplacements($requestClass, 'request'),
            $this->buildReplacements($actionClass, 'action'),
            $this->buildReplacements($resourceCollectionClass, 'collection')
        );

        $replace['{{ routePrefix }}']     = $this->option('route-prefix');
        $replace['{{ componentPrefix }}'] = $this->option('component-prefix');

        return str_replace(
            array_keys($replace),
            array_values($replace),
            parent::buildClass($name) //Calls the buildClass method against the parents parent with the name parameter as an argument
        );
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
    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'Use or create the given model when creating the controller.'],
            ['resource', 'res', InputOption::VALUE_OPTIONAL, 'Use or create the given resource when creating the controller.'],
            ['collection', 'resc', InputOption::VALUE_OPTIONAL, 'Use or create the given resource collection when creating the controller.'],
            ['request', 'req', InputOption::VALUE_OPTIONAL, 'Use or create the given request when creating the controller.'],
            ['action', 'a', InputOption::VALUE_OPTIONAL, 'Use or create the given action when creating the controller.'],
            ['route-prefix', 'rp', InputOption::VALUE_OPTIONAL, 'Use the given route prefix for the routes.'],
            ['component-prefix', 'cp', InputOption::VALUE_OPTIONAL, 'Use the given route prefix for the components.'],
        ];
    }
}
