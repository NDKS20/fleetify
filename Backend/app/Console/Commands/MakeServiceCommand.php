<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeServiceCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name : The service name} {--f|force : Overwrite existing file} {--r|resource : Create a resource service} {model : Model for resource service}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service class';

    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);
        $stub = str_replace('{{ model }}', $this->argument('model') ?? 'NOT_DEFINED', $stub);

        return $stub;
    }

    protected function getStub()
    {
        if ($this->option('resource')) {
            return __DIR__ . '/../Stubs/ResourceService.stub';
        }

        return __DIR__ . '/../Stubs/Service.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . "\\Services";
    }
}
