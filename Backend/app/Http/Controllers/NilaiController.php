<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Nilai;
use App\Helpers\HasPermissionRestrictions;

class NilaiController extends Controller
{
    use HasPermissionRestrictions;

    const PERMISSIONS = [
        // 'nilaiRT' => ['read-nilai'],
        // 'nilaiST' => ['read-nilai'],
    ];

    /**
     * Get nilaiRT data in image format
     */
    public function nilaiRT(Request $request)
    {
        // Use SQL aggregation to calculate Holland test scores
        // Using materi_uji_id = 7 and excluding pelajaran_khusus
        $results = Nilai::selectRaw('
                nama,
                nisn,
                MAX(CASE WHEN nama_pelajaran = "REALISTIC" THEN skor ELSE 0 END) as realistic,
                MAX(CASE WHEN nama_pelajaran = "INVESTIGATIVE" THEN skor ELSE 0 END) as investigative,
                MAX(CASE WHEN nama_pelajaran = "ARTISTIC" THEN skor ELSE 0 END) as artistic,
                MAX(CASE WHEN nama_pelajaran = "SOCIAL" THEN skor ELSE 0 END) as social,
                MAX(CASE WHEN nama_pelajaran = "ENTERPRISING" THEN skor ELSE 0 END) as enterprising,
                MAX(CASE WHEN nama_pelajaran = "CONVENTIONAL" THEN skor ELSE 0 END) as conventional
            ')
            ->where('materi_uji_id', 7)
            ->whereIn('nama_pelajaran', ['REALISTIC', 'INVESTIGATIVE', 'ARTISTIC', 'SOCIAL', 'ENTERPRISING', 'CONVENTIONAL'])
            ->whereNotIn('nama_pelajaran', ['Pelajaran Khusus'])
            ->groupBy('nama', 'nisn')
            ->get();

        // Final data formatting using collection (only for grouping)
        $data = $results->mapWithKeys(function ($item, $index) {
            return [
                $index => [
                    'nama' => $item->nama,
                    'nisn' => $item->nisn,
                    'nilaiRT' => [
                        'artistic' => (int) $item->artistic,
                        'conventional' => (int) $item->conventional,
                        'enterprising' => (int) $item->enterprising,
                        'investigative' => (int) $item->investigative,
                        'realistic' => (int) $item->realistic,
                        'social' => (int) $item->social,
                    ]
                ]
            ];
        })->toArray();

        return $this->respond($data);
    }

    /**
     * Get nilaiST data in image format
     */
    public function nilaiST(Request $request)
    {
        // Use SQL aggregation to calculate Scholastic test scores
        // Using materi_uji_id = 4 with specific multipliers
        $results = Nilai::selectRaw('
                nama,
                nisn,
                MAX(CASE WHEN pelajaran_id = 44 THEN skor * 41.67 ELSE 0 END) as verbal,
                MAX(CASE WHEN pelajaran_id = 45 THEN skor * 29.67 ELSE 0 END) as kuantitatif,
                MAX(CASE WHEN pelajaran_id = 46 THEN skor * 100 ELSE 0 END) as penalaran,
                MAX(CASE WHEN pelajaran_id = 47 THEN skor * 23.81 ELSE 0 END) as figural,
                (
                    MAX(CASE WHEN pelajaran_id = 44 THEN skor * 41.67 ELSE 0 END) +
                    MAX(CASE WHEN pelajaran_id = 45 THEN skor * 29.67 ELSE 0 END) +
                    MAX(CASE WHEN pelajaran_id = 46 THEN skor * 100 ELSE 0 END) +
                    MAX(CASE WHEN pelajaran_id = 47 THEN skor * 23.81 ELSE 0 END)
                ) as total
            ')
            ->where('materi_uji_id', 4)
            ->whereIn('pelajaran_id', [44, 45, 46, 47])
            ->groupBy('nama', 'nisn')
            ->orderBy(DB::raw('(
                MAX(CASE WHEN pelajaran_id = 44 THEN skor * 41.67 ELSE 0 END) +
                MAX(CASE WHEN pelajaran_id = 45 THEN skor * 29.67 ELSE 0 END) +
                MAX(CASE WHEN pelajaran_id = 46 THEN skor * 100 ELSE 0 END) +
                MAX(CASE WHEN pelajaran_id = 47 THEN skor * 23.81 ELSE 0 END)
            )'), 'desc')
            ->get();

        // Final data formatting using collection (only for grouping)
        $data = $results->mapWithKeys(function ($item, $index) {
            return [
                $index => [
                    'nama' => $item->nama,
                    'nisn' => $item->nisn,
                    'listNilai' => [
                        'figural' => (float) $item->figural,
                        'kuantitatif' => (float) $item->kuantitatif,
                        'penalaran' => (float) $item->penalaran,
                        'verbal' => (float) $item->verbal,
                    ],
                    'total' => (float) $item->total
                ]
            ];
        })->toArray();

        return $this->respond($data);
    }
}
