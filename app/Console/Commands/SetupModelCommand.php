<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class SetupModelCommand extends Command
{
    protected $signature = 'setup:model {name} {--namespace=}';

    protected $description = 'Creates the scaffolding for a new model type';

    public function handle(): void
    {
        $name      = $this->argument('name');
        $namespace = $this->option('namespace') ? "{$this->option('namespace')}/" : null;

        $namespacedName = "$namespace$name";

        //Create the model and migration
        $this->call('make:model', ['name' => $name, '--migration' => true]);

        //Create the request
        $this->call('make:model-request', ['name' => "{$namespacedName}Request", '--model' => $name]);

        //Create the action
        $this->call('make:action', ['name' => "$namespace{$name}Action", '--request' => "{$namespacedName}Request", '--model' => $name]);

        //Create the policy
        $this->call('make:policy', ['name' => "{$name}Policy", '--model' => $name]);

        //Create a model factory
        $this->call('make:factory', ['name' => "{$name}Factory"]);

        //Create a model seeder
        $this->call('make:seeder', ['name' => "{$name}Seeder"]);

        //Create the controller
        $this->call('make:api-controller', [
            'name'               => "{$namespacedName}Controller",
            '--model'            => $name,
            '--resource'         => "{$name}Resource",
            '--request'          => "{$namespacedName}Request",
            '--action'           => "{$namespace}Persist{$name}Action",
        ]);

        //Create a unit test
        $this->call('make:test', ['name' => "{$name}Test", '--unit' => true]);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['namespace', 'n', InputOption::VALUE_OPTIONAL, 'Gets the namespace to use for requests, actions and controllers'],
        ];
    }
}
