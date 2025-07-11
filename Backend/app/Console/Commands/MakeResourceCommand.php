<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Str;

class MakeResourceCommand extends GeneratorCommand implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:resource {name : The resource name, must match with a model} {--f|force : Overwrite existing file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a resource';

    /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array<string, string>
     */
    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'name' => ['What should the resource be called?', 'E.g. Role'],
        ];
    }

    public function handle()
    {
        parent::handle();

        $this->call('make:service', [
            'name' => $this->getServiceName(),
            'model' => $this->argument('name'),
            '--resource' => true,
        ]);
    }

    protected function getServiceName()
    {
        return trim($this->argument('name')) . 'Service';
    }

    protected function getNameInput()
    {
        return trim($this->argument('name')) . 'Controller';
    }

    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);
        $stub = str_replace('{{ service }}', $this->getServiceName(), $stub);
        $stub = str_replace('{{ lower:resource }}', Str::snake($this->argument('name')), $stub);
        $stub = str_replace('{{ model }}', $this->argument('name'), $stub);

        return $stub;
    }

    protected function getStub()
    {
        return __DIR__ . '/../Stubs/ResourceController.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . "\\Http\\Controllers";
    }
}
