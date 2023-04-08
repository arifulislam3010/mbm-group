<?php

namespace App\Console\Commands;

use Illuminate\Support\Composer;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class RepositoryMakeCommand extends GeneratorCommand
{
    protected $name = 'make:repository';

    
    protected $description = 'Create a new repository class';

    
    protected $type = 'Repository';

   
    protected $composer;

    
    public function __construct(Filesystem $files, Composer $composer)
    {
        parent::__construct($files);

        $this->composer = $composer;
    }

   
    public function handle()
    {
        parent::handle();

        $this->composer->dumpAutoloads();
    }

    
    protected function getStub()
    {
        return base_path('app\Console\Commands\stubs\repository.stub');
    }

  
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Repositories';
    }

    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
    }

    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the repository already exists']
        ];
    }
}