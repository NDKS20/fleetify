<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeHelperCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:helper {name : The helper name} {--f|force : Overwrite existing file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new helper class';

    protected function getStub()
    {
        return __DIR__ . '/../Stubs/Helper.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . "\\Helpers";
    }
}
