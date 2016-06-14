<?php
namespace App\Console\Commands\Generator;


use Illuminate\Console\Command;


class EspGeneration extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:espPackage {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add new Esp Account';

   public function handle(){
       $name = $this->argument('name');
       $this->call('generate:espSeed', ['name' => $name]);
       $this->call('generate:espReportService', ['name' => $name]);
       $this->call('generate:espApiClass', ['name' => $name]);
       $this->call('generate:espModel', ['name' => $name]);
       $table = str_plural(snake_case(class_basename($this->argument('name')."Report")));
       $this->call('make:migration', ['name' => "create_{$table}_table", '--create' => $table]);
       $name = trim($this->argument('name'))."Seeder";

       $this->info("please run db:seed --class={$name}");
   }
  }