<?php

namespace App\Console\Commands;

use Cloudinary;
use Illuminate\Console\Command;
use JD\Cloudder\Facades\Cloudder;

class TestCloud extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test-cloud';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        // $publicId = Cloudder::getPublicId();

        // $this->info($publicId);

        // $c = Cloudder::admin()
        
        $a = Cloudder::show('laporan-kegiatan/mywsollz4rzfswehb7ql');

        // cloudinary()->admin();

        $this->info($a);

    }
}
