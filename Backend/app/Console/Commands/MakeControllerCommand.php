<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Console\GeneratorCommand;

class MakeControllerCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:controller
                            {name : The name of the controller class}
                            {--f|force : Overwrite existing file}
                            {--r|resource : Create a resource controller}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new controller class with optional resource methods';

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        // Use anticipate to prompt for model and service with suggestions
        $model = $this->anticipate('What is the model for the resource controller?', $this->getClassNamesFromDirectory(app_path('Models')));
        $service = $this->anticipate('What is the service for the resource controller?', $this->getClassNamesFromDirectory(app_path('Services')));

        $stub = str_replace('{{ model }}', $model ?? 'ModelNotDefined', $stub);
        $stub = str_replace('{{ service }}', $service ?? 'ServiceNotDefined', $stub);

        return $stub;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->option('resource')
            ? __DIR__ . '/../Stubs/ResourceController.stub'
            : __DIR__ . '/../Stubs/Controller.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\\Http\\Controllers';
    }

    /**
     * Get class names from the specified directory.
     *
     * @param  string  $path
     * @return array
     */
    protected function getClassNamesFromDirectory($path)
    {
        if (!File::exists($path)) {
            return [];
        }

        $files = File::allFiles($path);
        $classes = [];

        foreach ($files as $file) {
            $class = Str::replaceLast('.php', '', $file->getFilename());
            $classes[] = $class;
        }

        return $classes;
    }
}
