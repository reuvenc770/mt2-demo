<?php

namespace App\Console\Commands;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\CompareExportsJob;
use Illuminate\Console\Command;
use Storage;

class CompareMt1AndCmpExports extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'util:compare';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compare MT1 and CMP deliverable list profiles';
    const MT1_PREFIX = 'mt1';
    const CMP_PREFIX = 'cmp';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $mt1FileName = $this->findFile(self::MT1_PREFIX);
        $cmpFileName = $this->findFile(self::CMP_PREFIX);
	
        // if the two files exist ... 
        if ($mt1FileName && $cmpFileName) {
            echo "Found $mt1FileName and $cmpFileName" . PHP_EOL;
            $job = new CompareExportsJob($mt1FileName, $cmpFileName, str_random(16));
            $this->dispatch($job);
        }
    }

    private function findFile($prefix) {
        $files = Storage::disk('SystemFtp')->files('mt1_cmp_lp_comparisons');
        foreach ($files as $fileName) {
            $fArr = explode('/', $fileName);
            $f = $fArr[1];

            if (preg_match("/^$prefix/", $f)) {
                return $f;
            }
        }
        return false;
    }
}
