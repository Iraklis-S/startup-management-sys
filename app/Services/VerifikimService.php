<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class VerifikimService
{
    public function logAction(
        string $verifiableType,
        int $verifiableId,
        string $action,
        ?string $note = null,
        ?int $verifiedBy = null
    ) {
        return DB::table('verifikime')->insertGetId([
            'verifiable_type' => $verifiableType,
            'verifiable_id' => $verifiableId,
            'action' => $action,
            'note' => $note,
            'verified_by' => $verifiedBy,
            'created_at' => now(),
        ]);
    }

    public function getPendingQueue(): array
    {
        return [
            'kompanite' => DB::table('kompanite')
                ->where('verification_status', 'pending')
                ->orderBy('created_at', 'asc')
                ->get(),
            'marredheniet' => DB::table('marredheniet')
                ->where('verification_status', 'pending')
                ->orderBy('created_at', 'asc')
                ->get(),
            'raundet_financimit' => DB::table('raundet_financimit')
                ->where('verification_status', 'pending')
                ->orderBy('created_at', 'asc')
                ->get(),
            'investimet' => DB::table('investimet')
                ->where('verification_status', 'pending')
                ->orderBy('created_at', 'asc')
                ->get(),
            'fondet' => DB::table('fondet')
                ->where('verification_status', 'pending')
                ->orderBy('created_at', 'asc')
                ->get(),
            'blerjet' => DB::table('blerjet')
                ->where('verification_status', 'pending')
                ->orderBy('created_at', 'asc')
                ->get(),
            'arritjet' => DB::table('arritjet')
                ->where('verification_status', 'pending')
                ->orderBy('created_at', 'asc')
                ->get(),
            'ipos' => DB::table('ipos')
                ->where('verification_status', 'pending')
                ->orderBy('created_at', 'asc')
                ->get(),
        ];
    }

    public function getHistory(string $verifiableType, int $verifiableId)
    {
        return DB::table('verifikime as v')
            ->join('users as u', 'u.id', '=', 'v.verified_by')
            ->select('v.*', 'u.name as verified_by_name', 'u.email as verified_by_email')
            ->where('v.verifiable_type', $verifiableType)
            ->where('v.verifiable_id', $verifiableId)
            ->orderBy('v.created_at', 'desc')
            ->get();
    }
}

