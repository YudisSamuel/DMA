<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateProfilePhotosDirectory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-profile-photos-directory';

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
        $path = storage_path('app/public/profile_photos');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
            $this->info('Profile photos directory created.');
        }
    }
}
