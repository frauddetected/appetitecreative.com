<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;

class DownloadGeoLite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'geolite:download';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download the GeoLite database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $client = new Client();
        $licenseKey = "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";
        $response = $client->get('https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City&suffix=tar.gz&license_key='.$licenseKey);

        Storage::put('geolite.tar.gz', $response->getBody());

        // Extract the archive
        $archive = new \PharData(storage_path('app/geolite.tar.gz'));
        $archive->extractTo(storage_path('app/'));

        // Find the mmdb file in the extracted directory
        $mmdbFile = glob(storage_path('app/GeoLite2-City_*/GeoLite2-City.mmdb'))[0];

        // Move the mmdb file to the geolite directory
        rename($mmdbFile, storage_path('app/geolite/GeoLite2-City.mmdb'));

        // Delete the extracted directory
        $extractedDir = dirname($mmdbFile);
        exec("rm -rf $extractedDir");

        // Delete the archive
        Storage::delete('geolite.tar.gz');

        $this->info('GeoLite database downloaded and extracted successfully.');
    }
}
