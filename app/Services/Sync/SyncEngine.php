<?php

namespace App\Services\Sync;

use App\Models\SyncBatch;
use App\Models\SyncChange;
use App\Models\SyncConflict;
use App\Models\SyncCursor;
use App\Models\SyncQueue;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SyncEngine
{
    protected string $centralUrl;
    protected string $apiKey;
    protected string $supabaseKey;

    public function __construct()
    {
        $this->centralUrl = rtrim(config('siap.central_api_url', 'https://api.siapdesa.id/api/v1'), '/');
        $this->apiKey = config('siap.central_api_key', 'sb_publishable_UeCe6pqUTtRhBgLjpT1djA_anoXjXve');
        $this->supabaseKey = config('siap.supabase_key', 'sb_publishable_UeCe6pqUTtRhBgLjpT1djA_anoXjXve');
    }

    public function pushOutbox(): array
    {
        $pending = SyncQueue::where('status', 'PENDING')->orderBy('created_at', 'asc')->take(100)->get();
        if ($pending->isEmpty()) {
            return ['status' => 'IDLE', 'message' => 'Tidak ada antrean data lokal yang perlu dikirim.'];
        }

        $batchId = (string)Str::uuid();
        $mutations = [];

        foreach ($pending as $item) {
            $mutations[] = [
                'queue_id' => $item->id,
                'uuid' => $item->entity_uuid,
                'table' => $item->entity_table,
                'action' => $item->action,
                'payload' => $item->payload,
                'created_at' => $item->created_at->toIso8601String(),
            ];
        }

        try {
            $villageCode = SystemSetting::get('village_code', '3203162002');
            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'apikey' => $this->supabaseKey,
                    'X-Client-Info' => 'SIAP-Desa-LocalFirst/1.0.0',
                    'Content-Type' => 'application/json',
                ])
                ->post($this->centralUrl . '/sync/push', [
                    'batch_id' => $batchId,
                    'village_code' => $villageCode,
                    'device_code' => session('device_name', 'PC-ADMIN-01'),
                    'mutations' => $mutations,
                ]);

            if ($response->successful()) {
                $itemIds = $pending->pluck('id');
                SyncQueue::whereIn('id', $itemIds)->update(['status' => 'SYNCED']);

                SyncBatch::create([
                    'direction' => 'PUSH',
                    'items_count' => count($mutations),
                    'status' => 'SUCCESS',
                    'cursor_before' => null,
                    'cursor_after' => now()->toIso8601String(),
                    'synced_at' => now(),
                ]);

                return ['status' => 'SUCCESS', 'count' => count($mutations), 'message' => 'Data berhasil disinkronkan ke Cloud Supabase.'];
            } else {
                return ['status' => 'FAILED', 'message' => 'Server merespons error: ' . $response->status()];
            }
        } catch (\Throwable $e) {
            return ['status' => 'ERROR', 'message' => 'Gagal koneksi ke Central Supabase: ' . $e->getMessage()];
        }
    }

    public function pullDelta(): array
    {
        try {
            $cursor = SyncCursor::where('entity_table', 'ALL')->first();
            $lastCursor = $cursor ? $cursor->last_cursor : null;
            $villageCode = SystemSetting::get('village_code', '3203162002');

            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'apikey' => $this->supabaseKey,
                    'X-Client-Info' => 'SIAP-Desa-LocalFirst/1.0.0',
                ])
                ->get($this->centralUrl . '/sync/pull', [
                    'village_code' => $villageCode,
                    'cursor' => $lastCursor,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $changes = $data['changes'] ?? [];

                if (isset($data['next_cursor'])) {
                    SyncCursor::updateOrCreate(
                        ['entity_table' => 'ALL'],
                        ['last_cursor' => $data['next_cursor'], 'last_synced_at' => now()]
                    );
                }

                return ['status' => 'SUCCESS', 'changes_count' => count($changes), 'message' => 'Pembaruan data dari cloud berhasil ditarik.'];
            }
            return ['status' => 'FAILED', 'message' => 'Gagal menarik data dari server.'];
        } catch (\Throwable $e) {
            return ['status' => 'ERROR', 'message' => 'Gagal menghubungi Central Server: ' . $e->getMessage()];
        }
    }
}
