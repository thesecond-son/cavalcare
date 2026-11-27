<?php

namespace App\Console\Commands;

use App\Services\Care\WebsiteIntakeImporter;
use Illuminate\Console\Command;

/**
 * One-off (and safe to repeat): creates a client record for every website
 * intake form that doesn't have one yet. New intakes are imported
 * automatically when the family submits the form.
 *
 *   php artisan os:import-website-clients
 */
class ImportWebsiteClients extends Command
{
    protected $signature = 'os:import-website-clients';

    protected $description = 'Create Caval OS clients from website intake forms that have none yet';

    public function handle(WebsiteIntakeImporter $importer): int
    {
        $count = $importer->importAll();
        $this->info($count === 0 ? 'Nothing to import: every intake form already has a client.' : "Created {$count} client(s) from website intake forms.");

        return self::SUCCESS;
    }
}
