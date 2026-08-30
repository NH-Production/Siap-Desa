<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SyncChange;
use App\Models\SyncCursor;
use App\Models\SyncQueue;
use Illuminate\Http\Request;

class SyncApiController extends Controller
{
    public function handlePush(Request $request)
    {
        $batch = $request->input('changes', []);
        $deviceId = $request->input('device_id');
        $villageId = $request->input('village_id');

        $accepted = 0;
        foreach ($batch as $change) {
            SyncChange::firstOrCreate(
                ['change_uuid' => $change['uuid']],
                [
                    'village_id' => $villageId,
                    'source_device_id' => $deviceId,
                    'table_name' => $change['table_name'],
                    'record_uuid' => $change['record_uuid'],
                    'operation' => $change['operation'],
                    'version' => $change['local_version'],
                    'payload' => $change['payload'],
                ]
            );
            $accepted++;
        }

        return response()->json([
            'status' => 'SUCCESS',
            'accepted_count' => $accepted,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function handlePull(Request $request)
    {
        $since = $request->input('since', 0);
        $villageId = $request->input('village_id');

        $query = SyncChange::where('id', '>', $since);
        if ($villageId) {
            $query->where('village_id', $villageId);
        }

        $changes = $query->limit(100)->get();

        return response()->json([
            'status' => 'SUCCESS',
            'changes' => $changes,
            'last_id' => $changes->last()->id ?? $since,
        ]);
    }

    public function handleAck(Request $request)
    {
        $deviceId = $request->input('device_id');
        $lastId = $request->input('last_id');

        SyncCursor::updateOrCreate(
            ['device_id' => $deviceId],
            [
                'last_pull_change_id' => $lastId,
                'last_sync_at' => now(),
            ]
        );

        return response()->json(['status' => 'ACK_OK']);
    }
}
