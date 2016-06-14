<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 6/14/16
 * Time: 2:37 PM
 */

namespace App\Console\Commands\Generator;


use Illuminate\Console\GeneratorCommand;

class EspSeedCommand extends GeneratorCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:espSeed {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add new Esp Model Seeder';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Model';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return resource_path('/stubs').'/seed.stub';
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

    protected function getPath($name)
    {
        return $this->laravel->databasePath().'/seeds/'.$name.'.php';
    }

    /**
     * Parse the name and format according to the root namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function parseName($name)
    {
        return $name."Seeder";
    }


}