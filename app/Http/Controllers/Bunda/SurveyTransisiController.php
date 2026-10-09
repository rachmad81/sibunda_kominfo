<?php

namespace App\Http\Controllers\Bunda;

use App\Http\Controllers\Controller;
use App\Models\ProfilSekolah\Kecamatan;
use App\Models\ProfilSekolah\Kelurahan;
use App\Models\ProfilSekolah\Sekolah;
use App\Models\SurveyTransisi\SurveyTransisi;
use App\Models\SurveyTransisi\SurveyTransisiJawaban;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class SurveyTransisiController extends Controller
{
    public function index(Request $request)
    {
        $pertanyaans = SurveyTransisi::orderBy('urutan')
            ->where('tipe', 'bunda2024')
            ->with(['jawabanSurvey' => function ($q) use ($request) {
                $q->where('bunda_id', $request->user()->id);
            }])
            ->get();

        return view('bunda.survey-transisi.index', compact('pertanyaans'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->jawaban as $pertanyaanId => $jawaban) {
                SurveyTransisiJawaban::updateOrCreate(
                    [
                        'bunda_id' => $request->user()->id,
                        'survey_transisi_id' => $pertanyaanId
                    ],
                    [
                        'jawaban' => $request->jawaban[$pertanyaanId],
                        'keterangan' => $request->keterangan[$pertanyaanId],
                        'bukti_dokumentasi' => $request->bukti_dokumentasi[$pertanyaanId],
                        'nama' => $request->user()->getNamaProfil(),
                        'kec_id' => $request->user()->kecamatan ? $request->user()->kecamatan->kecamatan_kode : null,
                        'kel_id' => $request->user()->kelurahan ? $request->user()->kelurahan->kelurahan_kode : null,
                    ]
                );
            }
            DB::commit();
            return redirect()->route('bunda-survey_transisi-index')->with(['type' => 'success', 'message' => 'Berhasil menyimpan.']);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('bunda-survey_transisi-index')->with(['type' => 'danger', 'message' => 'Gagal menyimpan.']);
        }
    }

    public function monitor(Request $request)
    {
        $lembagas = DB::table('survey_transisi as st')
            ->join('survey_transisi_jawaban as stj', function ($q) {
                $q->on('stj.survey_transisi_id', 'st.id');
            })
            ->where('st.tipe', 'satdik2024')
            ->when($request->user()->role == 'bunda', function ($q) use ($request) {
                if ($request->user()->tingkat == 'kecamatan') {
                    $q->where('stj.kec_id', $request->user()->Kecamatan->kecamatan_kode);
                } elseif ($request->user()->tingkat == 'kelurahan') {
                    $q->where('stj.kel_id', $request->user()->Kelurahan->kelurahan_kode);
                }
            })
            ->select(
                'stj.jenjang',
                'stj.npsn',
                'stj.nama',
                'stj.kec_id',
                'stj.kel_id',
                DB::raw('MAX(stj.updated_at) as updated_at'),
                DB::raw("COUNT(DISTINCT(CASE WHEN st.jawaban is true THEN st.id ELSE NULL END)) as jawaban_count"),
                DB::raw("COUNT(DISTINCT(CASE WHEN st.jawaban is true AND stj.jawaban IS NOT NULL THEN st.id ELSE NULL END)) as jawaban_done_count"),
                DB::raw("COUNT(DISTINCT(CASE WHEN st.keterangan is true THEN st.id ELSE NULL END)) as keterangan_count"),
                DB::raw("COUNT(DISTINCT(CASE WHEN st.keterangan is true AND stj.keterangan IS NOT NULL THEN st.id ELSE NULL END)) as keterangan_done_count"),
                DB::raw("COUNT(DISTINCT(CASE WHEN st.bukti_dokumentasi is true THEN st.id ELSE NULL END)) as bukti_dokumentasi_count"),
                DB::raw("COUNT(DISTINCT(CASE WHEN st.bukti_dokumentasi is true AND stj.bukti_dokumentasi IS NOT NULL THEN st.id ELSE NULL END)) as bukti_dokumentasi_done_count"),
            )
            ->groupBy('stj.jenjang', 'stj.npsn', 'stj.nama', 'stj.kec_id', 'stj.kel_id')
            ->get();
        $lembagas = $lembagas->map(function ($lembaga) {
            $lembaga->persentase_jawaban = round($lembaga->jawaban_done_count / $lembaga->jawaban_count, 2) * 100 . "%";
            $lembaga->persentase_keterangan = round($lembaga->keterangan_done_count / $lembaga->keterangan_count, 2) * 100 . "%";
            $lembaga->persentase_bukti_dokumentasi = round($lembaga->bukti_dokumentasi_done_count / $lembaga->bukti_dokumentasi_count, 2) * 100 . "%";
            $lembaga->persentase_total = round(($lembaga->jawaban_done_count + $lembaga->keterangan_done_count + $lembaga->bukti_dokumentasi_done_count) / ($lembaga->jawaban_count + $lembaga->keterangan_count + $lembaga->bukti_dokumentasi_count), 2) * 100 . "%";
            return $lembaga;
        });
        $kecamatans = Kecamatan::get();
        $kelurahans = Kelurahan::get();
        foreach ($lembagas as $lembaga) {
            $kecamatan = $kecamatans->where('kecamatan_kode', $lembaga->kec_id)->first();
            $lembaga->kecamatan = $kecamatan ? $kecamatan->kecamatan_dispenduk : null;
            $kelurahan = $kelurahans->where('kelurahan_kode', $lembaga->kel_id)->first();
            $lembaga->kelurahan = $kelurahan ? $kelurahan->kelurahan_dispenduk : null;
        }

        return view('bunda.survey-transisi.monitor', compact('lembagas'));
    }

    public function monitorDetail(Request $request)
    {
        $pertanyaans = SurveyTransisi::orderBy('urutan')
            ->where('tipe', $request->tipe)
            ->with(['jawabanSurvey' => function ($q) use ($request) {
                $q->where('npsn', $request->npsn);
            }])
            ->whereHas('jawabanSurvey', function ($q) use ($request) {
                $q->where('npsn', $request->npsn);
            })
            ->get();
        $content = view('bunda.survey-transisi.monitor_detail', compact('pertanyaans'))->render();
        return ['content' => $content];
    }
}
