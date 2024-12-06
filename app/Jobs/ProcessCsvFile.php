<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use App\Models\YourModel; // Ganti dengan nama model Anda

class ProcessCsvFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;

    /**
     * Create a new job instance.
     *
     * @param string $filePath
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            // Baca file dari storage
            $file = Storage::get($this->filePath);

            $csv = Reader::createFromString($file);
            $csv->setHeaderOffset(0);

            foreach ($csv as $row) {
                YourModel::create([
                    'column1' => $row['column1'],
                    'column2' => $row['column2'],
                ]);
            }

            Storage::delete($this->filePath); // Hapus file setelah diproses
        } catch (\Exception $e) {
            // Tangkap error dan kirim log ke server
            \Log::error('Error processing CSV file: ' . $e->getMessage());
        }
    }

}
