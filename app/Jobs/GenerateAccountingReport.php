<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateAccountingReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $reportType;
    protected $parameters;
    protected $userId;
    public $timeout = 900; 

    public function __construct(string $reportType, array $parameters, int $userId)
    {
        $this->reportType = $reportType;
        $this->parameters = $parameters;
        $this->userId = $userId;
    }

    public function handle(\App\Services\ReportService $reportService)
    {
        try {
            $report = $reportService->generateReport($this->reportType, $this->parameters);
            
            
            $fileName = "reports/{$this->reportType}_" . now()->format('Y_m_d_H_i_s') . '.xlsx';
            Storage::disk('s3')->put($fileName, $report);
            
           
            $user = \App\Models\User::findOrFail($this->userId);
            \Mail::to($user->email)->send(
                new \App\Mail\ReportGenerated($fileName, $this->reportType)
            );
            
        } catch (\Exception $e) {
            \Log::error("Report generation failed: " . $e->getMessage());
            
            
            $user = \App\Models\User::findOrFail($this->userId);
            \Mail::to($user->email)->send(
                new \App\Mail\ReportGenerationFailed($this->reportType, $e->getMessage())
            );
            
            throw $e;
        }
    }
}
