<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class NilaiController extends Controller
{
    public function nilaiRT(): JsonResponse
    {
        $rtScores = DB::select("SELECT nama, nisn, nama_pelajaran, skor FROM nilai WHERE materi_uji_id = 7 AND nama_pelajaran != 'Pelajaran Khusus' ORDER BY id_siswa");
        $result = [];
        $student = [
            'nama' => '',
            'nilaiRt' => [],
            'nisn' => '',
        ];
        $lastNisn = $rtScores[0]->nisn;
        foreach ($rtScores as $rtScore) {
            if ($lastNisn != $rtScore->nisn) {
                array_push($result, $student);
            }

            $student['nama'] = $rtScore->nama;
            $student['nisn'] = $rtScore->nisn;
            if (! isset($student['nilaiRt'][strtolower($rtScore->nama_pelajaran)])) {
                $student['nilaiRt'][strtolower($rtScore->nama_pelajaran)] = 0;
            }
            $student['nilaiRt'][strtolower($rtScore->nama_pelajaran)] = (int)$rtScore->skor;
            $lastNisn = $rtScore->nisn;
        }

        return response()->json($result);
    }

    public function nilaiST(): JsonResponse
    {
        $stScores = DB::select('SELECT
            `nama`,
            `nisn`,
            `nama_pelajaran`,
            (
                CASE
                    WHEN `pelajaran_id` = 44 THEN (`skor` * 41.67)
                    WHEN `pelajaran_id` = 45 THEN (`skor` * 29.67)
                    WHEN `pelajaran_id` = 46 THEN (`skor` * 100)
                    WHEN `pelajaran_id` = 47 THEN (`skor` * 23.81)
                    ELSE `nilai`.`skor`
                END
            ) AS `skor`,
            (
                SELECT SUM(
                    CASE
                        WHEN sub.`pelajaran_id` = 44 THEN (sub.`skor` * 41.67)
                        WHEN sub.`pelajaran_id` = 45 THEN (sub.`skor` * 29.67)
                        WHEN sub.`pelajaran_id` = 46 THEN (sub.`skor` * 100)
                        WHEN sub.`pelajaran_id` = 47 THEN (sub.`skor` * 23.81)
                        ELSE sub.`skor`
                    END
                )
                FROM `nilai` AS sub
                WHERE sub.`nisn` = `nilai`.`nisn` AND sub.`materi_uji_id` = 4
            ) AS `total`
            FROM `nilai`
            WHERE `materi_uji_id` = 4
            ORDER BY `total` DESC
        ');

        $result = [];
        $student = [
            'nama' => '',
            'listNilai' => [],
            'nisn' => '',
            'total' => '',
        ];
        $lastNisn = $stScores[0]->nisn;
        foreach ($stScores as $stScore) {
            if ($lastNisn != $stScore->nisn) {
                array_push($result, $student);
            }

            $student['nama'] = $stScore->nama;
            $student['nisn'] = $stScore->nisn;
            $student['total'] = (int)$stScore->total;
            if (! isset($student['listNilai'][strtolower($stScore->nama_pelajaran)])) {
                $student['listNilai'][strtolower($stScore->nama_pelajaran)] = 0;
            }
            $student['listNilai'][strtolower($stScore->nama_pelajaran)] = (int)$stScore->skor;
            $lastNisn = $stScore->nisn;
        }

        return response()->json($result);
    }
}
