<?php

namespace App\Services\Supabase;

use App\Models\CentralServerSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SupabaseService
{
    protected string $url;
    protected string $key;

    public function __construct()
    {
        $this->url = rtrim(CentralServerSetting::get('supabase_url', config('siap.supabase_url', 'https://siapdesa.supabase.co')), '/');
        $this->key = CentralServerSetting::get('supabase_key', config('siap.supabase_key', 'sb_publishable_UeCe6pqUTtRhBgLjpT1djA_anoXjXve'));
    }

    /**
     * Test connection to Supabase Cloud REST API
     */
    public function testConnection(): array
    {
        $start = microtime(true);
        try {
            $response = Http::timeout(6)
                ->withHeaders([
                    'apikey' => $this->key,
                    'Authorization' => 'Bearer ' . $this->key,
                ])
                ->get($this->url . '/rest/v1/');

            $latency = round((microtime(true) - $start) * 1000);

            if ($response->successful() || $response->status() === 200 || $response->status() === 404 || $response->status() === 401) {
                // Supabase endpoint is reachable
                return [
                    'success' => true,
                    'latency_ms' => $latency,
                    'status_code' => $response->status(),
                    'message' => "Koneksi ke Supabase Cloud Aktif ({$latency}ms).",
                ];
            }

            return [
                'success' => false,
                'latency_ms' => $latency,
                'status_code' => $response->status(),
                'message' => 'Supabase merespons dengan status HTTP ' . $response->status(),
            ];
        } catch (\Throwable $e) {
            $latency = round((microtime(true) - $start) * 1000);
            return [
                'success' => false,
                'latency_ms' => $latency,
                'message' => 'Gagal menghubungi Supabase: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Ingest batch data into Supabase Cloud Table
     */
    public function insertOrUpsert(string $table, array $data): bool
    {
        try {
            $response = Http::timeout(8)
                ->withHeaders([
                    'apikey' => $this->key,
                    'Authorization' => 'Bearer ' . $this->key,
                    'Content-Type' => 'application/json',
                    'Prefer' => 'resolution=merge-duplicates',
                ])
                ->post($this->url . "/rest/v1/{$table}", $data);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning("Supabase Ingest Error on table {$table}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Query data from Supabase Cloud Table
     */
    public function query(string $table, array $params = []): array
    {
        try {
            $response = Http::timeout(8)
                ->withHeaders([
                    'apikey' => $this->key,
                    'Authorization' => 'Bearer ' . $this->key,
                ])
                ->get($this->url . "/rest/v1/{$table}", $params);

            return $response->successful() ? $response->json() : [];
        } catch (\Throwable $e) {
            Log::warning("Supabase Query Error on table {$table}: " . $e->getMessage());
            return [];
        }
    }
}
