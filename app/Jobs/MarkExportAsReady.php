<?php
namespace App\Jobs;

use App\Models\ExportLog;
use App\Traits\FileHandler;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MarkExportAsReady implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, FileHandler;

    protected $logId;

    public function __construct($logId)
    {
        $this->logId = $logId;
    }

    public function handle()
    {
        $ExportLog = ExportLog::find($this->logId);
        if (! $ExportLog) {
            return; // Log not found, exit the job
        }
        $this->BunnyStorageUpload($ExportLog->file_path);
      return  $ExportLog->update(['is_ready' => true]);
    }
}
