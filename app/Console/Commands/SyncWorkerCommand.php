<?php
namespace App\Console\Commands;

use App\Services\Sync\SyncEngine;
use Illuminate\Console\Command;

class SyncWorkerCommand extends Command
{
    protected $signature = 'siap:sync {--once : Jalankan satu siklus saja}';
    protected $description = 'Menjalankan sinkronisasi Local-First SIAP-DESA';

    public function handle(SyncEngine $sync): int
    {
        do {
            $online = $sync->checkCentral();
            if (!$online) {
                $this->line('OFFLINE: Central API tidak dapat diakses.');
            } else {
                $push = $sync->pushChanges();
                $pull = $sync->pullChanges();
                $this->line(json_encode(['online'=>true,'push'=>$push,'pull'=>$pull], JSON_UNESCAPED_SLASHES));
            }
            if ($this->option('once')) break;
            sleep((int) config('siapdesa.sync.interval_seconds', 60));
        } while (true);
        return self::SUCCESS;
    }
}