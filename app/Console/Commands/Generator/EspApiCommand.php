<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 6/14/16
 * Time: 2:37 PM
 */

namespace App\Console\Commands\Generator;


use Illuminate\Console\GeneratorCommand;

class EspApiCommand extends GeneratorCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:espApiClass {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add new Esp Api Class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'API';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return resource_path('/stubs').'/api.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Services\API';
    }

    /**
     * Build the model class with the given name.
     *
     * @param  string  $name
     *
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        $name = $this->argument('name');

        return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
    }


}