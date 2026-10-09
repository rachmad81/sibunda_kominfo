<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sibunda - {{ $judul }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }

        .card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: transform 0.2s;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .btn {
            display: inline-block;
            font-weight: 400;
            text-align: center;
            cursor: pointer;
            border: 1px solid transparent;
            padding: 6px 12px;
            font-size: 14px;
            line-height: 1.5;
            border-radius: 4px;
            transition: all 0.2s ease-in-out;
        }

        .btn-sm {
            padding: 4px 8px;
            font-size: 12px;
            border-radius: 3px;
        }

        .btn-success {
            color: #fff;
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .btn-success:active {
            background-color: #1e7e34;
            border-color: #1c7430;
        }

        .btn:disabled,
        .btn.disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }

        .from-crimson-50 {
            --tw-gradient-from: #ffeeee var(--tw-gradient-from-position);
            --tw-gradient-to: rgb(238 242 255 / 0) var(--tw-gradient-to-position);
            --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
        }
    </style>
</head>

<body class="p-4 md:p-6">

    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <header class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">Dashboard Personal</h1>
                <p class="text-slate-500">Informasi terpadu data diri, kesehatan, dan bantuan sosial.</p>
            </div>
            <div class="flex items-center gap-3 bg-white p-3 rounded-xl shadow-sm border border-slate-100">
                <div
                    class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold text-xl">
                    @php
                        echo $inisial = App\Tools\Bantuan::getInisial($cekin_warga->nama_lengkap);
                    @endphp
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-800">{{ $cekin_warga->nama_lengkap ?? 'Nama' }}</p>
                    <p class="text-xs text-slate-500">NIK: {{ $cekin_warga->nik }}</p>
                </div>
                <div
                    class="w-12 h-12 bg-yellow-100 text-blue-600 rounded-full flex items-center justify-center font-bold text-xl">
                    <a href="{{ route('bunda-dashboard_asw-halaman_sinkron', ['nik' => $cekin_warga->nik]) }}"
                        title="Sinkron data">
                        <i class="fa fa-refresh fa-spin"></i>
                    </a>
                </div>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">

            <!-- Sidebar: Data Diri -->
            <div class="md:col-span-3 space-y-6">
                <section class="card p-6">
                    <div class="flex items-center gap-2 mb-4 text-blue-600 font-bold border-b pb-2">
                        <i class="fas fa-user-circle"></i>
                        <h2>Data Diri</h2>
                    </div>
                    <div class="space-y-4">
                        <div>
                            @php
                                $foto =
                                    'https://static.vecteezy.com/system/resources/previews/022/058/960/non_2x/no-image-available-icon-vector.jpg';
                                $default_foto =
                                    'https://static.vecteezy.com/system/resources/previews/022/058/960/non_2x/no-image-available-icon-vector.jpg';
                                if ($data_sekarang->foto ?? '' != '') {
                                    $foto =
                                        'https://dispendik.surabaya.go.id/profilsekolah_file/' . $data_sekarang->foto;
                                }
                            @endphp
                            <img src="{{ $foto }}" style="max-width: 3cm" alt=""
                                onerror="this.onerror=null; this.src='{{ $default_foto }}'">
                        </div>
                        <div>
                            <label class="text-xs text-slate-400 uppercase tracking-wider">NIK</label>
                            <p class="font-medium text-slate-700">{{ $cekin_warga->nik }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-slate-400 uppercase tracking-wider">Nama</label>
                            <p class="font-medium text-slate-700">{{ $cekin_warga->nama_lengkap }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-slate-400 uppercase tracking-wider">Tempat, Tanggal Lahir</label>
                            <p class="font-medium text-slate-700">{{ $cekin_warga->tempat_lahir }},
                                {!! App\Tools\Waktu::parseFullDate($cekin_warga->tgl_lahir) !!}</p>
                        </div>
                        <div>
                            <label class="text-xs text-slate-400 uppercase tracking-wider">Jenis Kelamin</label>
                            <p class="font-medium text-slate-700">{{ $cekin_warga->jenis_kelamin }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-slate-400 uppercase tracking-wider">Alamat KK</label>
                            <p class="font-medium text-slate-700">{{ $cekin_warga->alamat }}, RT/RW
                                {{ $cekin_warga->rt }}/{{ $cekin_warga->rw }}, {{ $cekin_warga->kelurahan }},
                                {{ $cekin_warga->kecamatan }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-slate-400 uppercase tracking-wider">Alamat Domisili</label>
                            <p class="font-medium text-slate-700">
                                {{ $cekin_warga->alamat_dom ?? $cekin_warga->alamat }},
                                RT/RW
                                {{ $cekin_warga->rt_dom ?? $cekin_warga->rt }}/{{ $cekin_warga->rw_dom ?? $cekin_warga->rw }},
                                {{ $cekin_warga->kelurahan_dom ?? $cekin_warga->kelurahan }},
                                {{ $cekin_warga->kecamatan_dom ?? $cekin_warga->kecamatan }}
                            </p>
                        </div>
                        <div>
                            <label class="text-xs text-slate-400 uppercase tracking-wider">Nama Orang Tua</label>
                            <p class="font-medium text-slate-700">Ibu: {{ $cekin_warga->nama_ibu }}</p>
                            <p class="font-medium text-slate-700">Ayah: {{ $cekin_warga->nama_ayah }}</p>
                        </div>
                        {{-- <div>
                            <label class="text-xs text-slate-400 uppercase tracking-wider">Pekerjaan</label>
                            <p class="font-medium text-slate-700">Ibu: -</p>
                            <p class="font-medium text-slate-700">Ayah: -</p>
                        </div> --}}
                        <div>
                            <label class="text-xs text-slate-400 uppercase tracking-wider">Anak ke</label>
                            <p class="font-medium text-slate-700">{{ $data_sekarang->anakke ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-slate-400 uppercase tracking-wider">Desil</label>
                            <p class="font-medium text-slate-700">{{ $cekin_warga->desil_nasional ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-slate-400 uppercase tracking-wider">Memiliki Akte</label>
                            <p class="font-medium text-slate-700">{{ $cekin_warga->no_akta_lahir ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-slate-400 uppercase tracking-wider">Memiliki KIA</label>
                            <p class="font-medium text-slate-700">
                                {{ ($cekin_warga->status_kepemilikan_kia == true ? 'Sudah' : 'Belum') ?? '-' }}</p>
                        </div>
                    </div>
                </section>

                <section class="card p-6">
                    <div class="flex items-center gap-2 text-blue-600 font-bold">
                        <i class="fas fa-school"></i>
                        <h2>Data Sekolah</h2>

                        @if (!empty($cekin_warga) && ($cekin_warga->jenjang_sekolah != '' && $cekin_warga->is_aktif_pendidikan == true))
                            <span class="status-badge bg-green-100 text-green-700">Bersekolah</span>
                        @endif
                    </div>
                    <div class="border-b" style="font-size:8pt">
                        Data diambil dari dapodik, penarikan TA 2025/2026
                    </div>
                    @if (!empty($cekin_warga) && ($cekin_warga->jenjang_sekolah != '' && $cekin_warga->is_aktif_pendidikan == true))
                        <div class="space-y-4">
                            <div>
                                <label class="text-xs text-slate-400 uppercase tracking-wider">NPSN Sekolah</label>
                                <p class="font-medium text-slate-700">{{ $cekin_warga->npsn_sekolah }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-slate-400 uppercase tracking-wider">Nama Sekolah</label>
                                <p class="font-medium text-slate-700">{{ $cekin_warga->nama_sekolah }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-slate-400 uppercase tracking-wider">Jenjang</label>
                                <p class="font-medium text-slate-700">{{ $cekin_warga->jenjang_sekolah }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-slate-400 uppercase tracking-wider">Kelas (Rombel)</label>
                                <p class="font-medium text-slate-700">{{ $cekin_warga->kelas }}
                                    ({{ $cekin_warga->rombel }})</p>
                            </div>
                        </div>
                    @else
                        <div class="space-y-4">
                            <div>
                                <label class="text-xs text-slate-400 uppercase tracking-wider">
                                    Siswa belum bersekolah
                                </label>
                            </div>
                        </div>
                    @endif
                </section>
                <section class="card p-6">
                    <div class="flex items-center gap-2 text-blue-600 font-bold">
                        <i class="fas fa-school"></i>
                        <h2>Data Profil Sekolah</h2>

                        @if (!empty($data_sekarang))
                            <span class="status-badge bg-green-100 text-green-700">Bersekolah</span>
                        @endif
                    </div>
                    <div class="border-b" style="font-size:8pt">
                        Data diambil dari aplikasi profil sekolah
                    </div>
                    @if (!empty($data_sekarang))
                        <div class="space-y-4">
                            <div>
                                <label class="text-xs text-slate-400 uppercase tracking-wider">NPSN Sekolah</label>
                                <p class="font-medium text-slate-700">{{ $data_sekarang->npsn }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-slate-400 uppercase tracking-wider">Nama Sekolah</label>
                                <p class="font-medium text-slate-700">{{ $data_sekarang->nama_sekolah }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-slate-400 uppercase tracking-wider">Jenjang</label>
                                <p class="font-medium text-slate-700">{{ $data_sekarang->jenjang }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-slate-400 uppercase tracking-wider">Kelas (Rombel)</label>
                                <p class="font-medium text-slate-700">{{ $data_sekarang->kelas }}
                                    ({{ $data_sekarang->rombel }})</p>
                            </div>
                            <div>
                                <label class="text-xs text-slate-400 uppercase tracking-wider">Status Menerima
                                    MBG</label>
                                <p class="font-medium text-slate-700">{{ !empty($mbg) ? 'Menerima' : '-' }}</p>
                            </div>
                        </div>
                    @else
                        <div class="space-y-4">
                            <div>
                                <label class="text-xs text-slate-400 uppercase tracking-wider">
                                    Siswa tidak terdeteksi di profil sekolah sebagai siswa aktif
                                </label>
                            </div>
                        </div>
                    @endif
                </section>

                <!-- Bantuan Sosial -->
                <section class="card p-6 bg-gradient-to-br from-indigo-50 to-white border border-indigo-100">
                    <div class="flex items-center gap-2 mb-4 text-indigo-600 font-bold border-b border-indigo-200 pb-2">
                        <i class="fas fa-hand-holding-heart"></i>
                        <h2>Riwayat Bantuan</h2>
                    </div>
                    @if ($bantuan->count() > 0)
                        <ul class="space-y-3">
                            @foreach ($bantuan as $b)
                                <li class="flex justify-between items-start">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-800">{{ $b->jenis_bantuan }}
                                            ({{ $b->ket_bantuan }})
                                        </p>
                                        <p class="text-xs text-slate-500">Anggaran: {{ $b->tahun_anggaran }}</p>
                                    </div>
                                    {{-- <span class="status-badge bg-green-100 text-green-700">Aktif</span> --}}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        Tidak ada riwayat menerima bantuan
                    @endif
                </section>

                <!-- Beasiswa PAUD -->
                <section class="card p-6" style="background: linear-gradient(135deg, #fffedc , #ffffff);">
                    <div class="flex items-center gap-2 mb-4 text-indigo-600 font-bold border-b border-indigo-200 pb-2">
                        <i class="fas fa-coins"></i>
                        <h2>Riwayat Beasiswa PAUD</h2>
                    </div>
                    @if ($beasiswa->count() > 0)
                        <ul class="space-y-3">
                            @foreach ($beasiswa as $b)
                                <li class="flex justify-between items-start">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-800">
                                            {{ $b->nama_sekolah }}
                                        </p>
                                        <p class="text-xs text-slate-500">Anggaran: {{ $b->tahun }}
                                            (Periode {{ $b->periode }})
                                        </p>
                                    </div>
                                    {{-- <span class="status-badge bg-green-100 text-green-700">Aktif</span> --}}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        Tidak ada riwayat menerima beasiswa
                    @endif
                </section>

                {{-- <!-- PENGHAFAL KITAB -->
                <section class="card p-6" style="background: linear-gradient(135deg, #fffedc , #ffffff);">
                    <div class="flex items-center gap-2 mb-4 text-indigo-600 font-bold border-b border-indigo-200 pb-2">
                        <i class="fas fa-coins"></i>
                        <h2>Riwayat Penerima Penghafal Kitab</h2>
                    </div>
                    @if ($genmas->count() > 0)
                        <ul class="space-y-3">
                            @foreach ($genmas as $b)
                                <li class="flex justify-between items-start">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-800">
                                            {{ $b->nama_sekolah }}
                                        </p>
                                        <p class="text-xs text-slate-500">Anggaran: {{ $b->tahun }}</p>
                                    </div>
                                    {{-- <span class="status-badge bg-green-100 text-green-700">Aktif</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        Tidak ada riwayat menerima beasiswa
                    @endif
                </section> --}}

                <!-- INKLUSI DINSOS -->
                <section class="card p-6" style="background: linear-gradient(135deg, #fffedc , #ffffff);">
                    <div
                        class="flex items-center gap-2 mb-4 text-indigo-600 font-bold border-b border-indigo-200 pb-2">
                        <i class="fas fa-wheelchair"></i>
                        <h2>Inklusi (Dinas Sosial)</h2>
                    </div>
                    @if ($inklusi_dinsos->count() > 0)
                        <ul class="space-y-3">
                            @foreach ($inklusi_dinsos as $b)
                                <li class="flex justify-between items-start">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-800">
                                            {{ $b->jenis_disabilitas }}
                                        </p>
                                    </div>
                                    {{-- <span class="status-badge bg-green-100 text-green-700">Aktif</span> --}}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        Tidak ada riwayat inklusi
                    @endif
                </section>

                <!-- RUTILAHU -->
                <section class="card p-6" style="background: linear-gradient(135deg, #dcfff6 , #ffffff);">
                    <div
                        class="flex items-center gap-2 mb-4 text-indigo-600 font-bold border-b border-indigo-200 pb-2">
                        <i class="fas fa-home"></i>
                        <h2>Riwayat Rutilahu</h2>
                    </div>
                    @if ($rutilahu->count() > 0)
                        <ul class="space-y-3">
                            @foreach ($rutilahu as $b)
                                <li class="flex justify-between items-start">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-800">
                                            {{ $b->tahun_pengerjaan }} - {{ $b->sumber_dana }} ({{ $b->instansi }})
                                        </p>
                                    </div>
                                    <span
                                        class="status-badge bg-green-100 text-green-700">{{ $b->status_pekerjaan }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        Tidak ada riwayat menerima rutilahu
                    @endif
                </section>

                <!-- SOTH -->
                <section class="card p-6" style="background: linear-gradient(135deg, #dcfff6 , #ffffff);">
                    <div
                        class="flex items-center gap-2 mb-4 text-indigo-600 font-bold border-b border-indigo-200 pb-2">
                        <i class="fas fa-school"></i>
                        <h2>Riwayat Sekolah Orang Tua Hebat(SOTH)</h2>
                    </div>
                    @if ($soth->count() > 0)
                        <ul class="space-y-3">
                            @foreach ($soth as $b)
                                <li class="flex justify-between items-start">
                                    <div>
                                        <p><b>Nama Ibu:</b> {{ $b->nama_ibu }}</p>
                                        <p><b>Lembaga SOTH:</b> {{ $b->nama_soth }}</p>
                                        <p><b>Alamat SOTH:</b> {{ $b->alamat_soth }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        Tidak ada riwayat SOTH
                    @endif
                </section>

                <!-- PHBS -->
                <section class="card p-6" style="background: linear-gradient(135deg, #dcfff6 , #ffffff);">
                    <div
                        class="flex items-center gap-2 mb-4 text-indigo-600 font-bold border-b border-indigo-200 pb-2">
                        <i class="fas fa-hand"></i>
                        <h2>Perilaku Hidup Bersih dan Sehat (PHBS)</h2>
                    </div>
                    {{ $cekin_warga->status_phbs ?? '-' }}
                </section>
            </div>

            <!-- Main Content: Kesehatan -->
            <div class="md:col-span-9 space-y-6">

                {{-- ANGGOTA KELUARGA --}}
                <section class="card p-6 bg-gradient-to-br from-indigo-50 to-white border border-indigo-100">
                    <div class="flex items-center gap-2 mb-4 font-bold border-b pb-2" style="color: #5d92c7">
                        <i class="fas fa-users"></i>
                        <h2>Anggota Keluarga</h2>
                    </div>
                    <div class="grid grid-cols-1" style="max-height: 500px;overflow:auto">
                        <div>
                            baris dengan warna <span style="background: #9affb3;padding:0px 10px">&nbsp;</span>
                            adalah baris
                            data anak
                            yang bersangkutan.
                        </div>
                        <table class="w-full text-left">
                            <thead
                                class="sticky top-0 z-10 bg-slate-100 text-xs font-semibold text-slate-600 uppercase shadow-sm">
                                <tr class="text-xs font-semibold text-slate-400 uppercase bg-slate-50">
                                    <th class="px-4 py-2">NIK</th>
                                    <th class="px-4 py-2">Nama</th>
                                    <th class="px-4 py-2">Status</th>
                                    <th class="px-4 py-2">Pekerjaan</th>
                                    <th class="px-4 py-2">Pendidikan</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y">
                                @foreach ($anggota_keluarga as $a)
                                    <tr
                                        style="background: @if ($a->nik == $cekin_warga->nik) #9affb3 @else transparent @endif;">
                                        <td class="px-4 py-3 text-slate-500">{{ $a->nik ?? '' }}</td>
                                        <td class="px-4 py-3 text-slate-500">{{ $a->nama_lengkap ?? '' }}</td>
                                        <td class="px-4 py-3 text-slate-500">{{ $a->hub_keluarga ?? '' }}</td>
                                        <td class="px-4 py-3 text-slate-500">{{ $a->pekerjaan ?? '' }}</td>
                                        <td class="px-4 py-3 text-slate-500">{{ $a->pendidikan ?? '' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Riwayat Imunisasi -->
                <section class="card p-6">
                    <div class="flex items-center justify-between mb-4 border-b pb-2">
                        <div class="flex items-center gap-2 text-emerald-600 font-bold">
                            <i class="fas fa-syringe"></i>
                            <h2>Riwayat Imunisasi</h2>
                        </div>
                        {{-- <span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-1 rounded">Lengkap</span> --}}
                    </div>
                    <div class="overflow-x-auto" style="max-height:500px">
                        <table class="w-full text-left">
                            <thead
                                class="sticky top-0 z-10 bg-slate-100 text-xs font-semibold text-slate-600 uppercase shadow-sm">
                                <tr class="text-xs font-semibold text-slate-400 uppercase bg-slate-50">
                                    <th class="px-4 py-2">Imunisasi</th>
                                    <th class="px-4 py-2">Tanggal</th>
                                    <th class="px-4 py-2">Imunisasi ke</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y">
                                @if ($imunisasi->count() > 0)
                                    @foreach ($imunisasi as $im)
                                        <tr>
                                            <td class="px-4 py-3 font-medium">{{ $im->imunisasi }}</td>
                                            <td class="px-4 py-3">{!! App\Tools\Waktu::parseFullDate($im->tanggal_imunisasi) !!}</td>
                                            <td class="px-4 py-3 text-slate-500">{{ $im->imunisasike }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" style="text-align: center">Tidak ditemukan riwayat
                                            Imunisasi</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Riwayat Posyandu -->
                <section class="card p-6">
                    <div class="flex items-center gap-2 mb-4 text-orange-600 font-bold border-b pb-2">
                        <i class="fas fa-chart-line"></i>
                        <h2>Riwayat Tumbuh Kembang</h2>
                    </div>
                    <div>
                        <span style="background: #93a6ff;padding: 0px 10px">&nbsp;</span> Lokasi pemeriksaan di
                        Sekolah<br>
                        <span style="background: #fffd8f;padding: 0px 10px">&nbsp;</span> Lokasi pemeriksaan di
                        Posyandu
                    </div>
                    <div class="grid grid-cols-1" style="max-height: 500px;overflow:auto">
                        <table class="w-full text-left">
                            <thead
                                class="sticky top-0 z-10 bg-slate-100 text-xs font-semibold text-slate-600 uppercase shadow-sm">
                                <tr class="text-xs font-semibold text-slate-400 uppercase bg-slate-50">
                                    <th class="px-4 py-2">Tanggal Posyandu</th>
                                    <th class="px-4 py-2">Berat (kg)</th>
                                    <th class="px-4 py-2">Tinggi (cm)</th>
                                    <th class="px-4 py-2">Lila (cm)</th>
                                    <th class="px-4 py-2">Status BB TB</th>
                                    <th class="px-4 py-2">Lokasi Posyandu</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y">
                                @if ($gizi->count() > 0)
                                    @foreach ($gizi as $g)
                                        @php
                                            $warna = $g->lokasi == 'sekolah' ? '#93a6ff' : '#fffd8f';
                                        @endphp
                                        <tr style="background: {{ $warna }}">
                                            <td class="px-4 py-3 font-medium">{!! App\Tools\Waktu::parseFullDate($g->tanggal_posyandu) !!}</td>
                                            <td class="px-4 py-3">{!! $g->beratbadan !!}</td>
                                            <td class="px-4 py-3">{{ $g->tinggibadan }}</td>
                                            <td class="px-4 py-3">{{ $g->lila }}</td>
                                            <td class="px-4 py-3">{{ $g->status_bb_tb }}</td>
                                            <td class="px-4 py-3">{{ $g->posyandu }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" style="text-align: center">Tidak ditemukan riwayat
                                            Timbang</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Riwayat Survey -->
                @if (!empty($survey))
                    <section class="card p-6">
                        <div class="flex items-center gap-2 mb-4 text-yellow-600 font-bold border-b pb-2">
                            <i class="fas fa-clipboard"></i>
                            <h2>Riwayat Survey Bunda PAUD</h2>
                        </div>
                        <div class="grid grid-cols-1">
                            <div class="space-y-4">
                                <div>
                                    <label class="text-xs text-slate-400 uppercase tracking-wider">Status
                                        SUrvey</label>
                                    <p class="font-medium text-slate-700">
                                        @if ($survey->status_verifikasi == 'Selesai')
                                            <span
                                                class="text-xs bg-emerald-100 text-emerald-700 px-2 py-1 rounded">{!! $survey->status_verifikasi ?? '' !!}</span>
                                        @else
                                            <span
                                                class="text-xs bg-maroon-100 text-maroon-700 px-2 py-1 rounded">{!! $survey->status_verifikasi ?? '' !!}</span>
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <label class="text-xs text-slate-400 uppercase tracking-wider">Survey
                                        Alamat</label>
                                    <p class="font-medium text-slate-700">
                                        @php
                                            $domisili = \App\Tools\ParameterATS::data_domisili();
                                            $dt = collect($domisili)->where('value', $survey->status_alamat)->first();
                                            echo $dt['text'] ?? $survey->status_alamat;
                                        @endphp
                                    </p>
                                    <p class="font-small text-slate-500" style="font-size: 10pt">
                                        ({!! $survey->detail_alamat ?? '' !!})
                                    </p>
                                </div>
                                <div>
                                    <label class="text-xs text-slate-400 uppercase tracking-wider">Survey
                                        Sekolah</label>
                                    <p class="font-medium text-slate-700">
                                        @php
                                            $sekolah = \App\Tools\ParameterATS::data_sekolah();
                                            $dt = collect($sekolah)->where('value', $survey->status_sekolah)->first();
                                            echo $dt['text'] ?? $survey->status_sekolah;
                                        @endphp
                                    </p>
                                    <p class="font-medium text-slate-500" style="font-size: 10pt">
                                        {!! $survey->detail_sekolah != '' ? '(' . $survey->detail_sekolah . ')' : '' !!}
                                    </p>
                                </div>
                                <div>
                                    <label class="text-xs text-slate-400 uppercase tracking-wider">Faktor yang
                                        mempengaruhi</label>
                                    <p class="font-medium text-slate-700">
                                        @php
                                            $faktor = \App\Tools\ParameterATS::data_faktor();
                                            $arr_tidak_Sekolah = explode('|', $survey->detail_tidak_sekolah);
                                            $dt = collect($faktor)->whereIn('value', $arr_tidak_Sekolah);

                                            $hasil_faktor = '<ol>';
                                            foreach ($dt as $d) {
                                                $hasil_faktor .= '<li>- ' . $d['text'] . '</li>';
                                            }
                                            $hasil_faktor .= '</ol>';
                                            echo $hasil_faktor;
                                        @endphp
                                    </p>
                                    <p class="font-medium text-slate-500" style="font-size: 10pt">
                                        {!! $survey->pilihan_detail_tidak_sekolah != '' ? '(' . $survey->pilihan_detail_tidak_sekolah . ')' : '' !!}
                                    </p>
                                </div>
                                <div>
                                    <label class="text-xs text-slate-400 uppercase tracking-wider">Tanggapa Orang
                                        Tua</label>
                                    <p class="font-medium text-slate-700">
                                        @php
                                            $sekolah = \App\Tools\ParameterATS::data_tanggapan();
                                            $dt = collect($sekolah)->where('value', $survey->tanggapan_ortu)->first();
                                            echo $dt['text'] ?? $survey->tanggapan_ortu;
                                        @endphp
                                    </p>
                                </div>
                                <div>
                                    <label class="text-xs text-slate-400 uppercase tracking-wider">Rencana Tindak
                                        Lanjut</label>
                                    <p class="font-medium text-slate-700">
                                        @php
                                            $faktor = \App\Tools\ParameterATS::data_rtl();
                                            $arr_tidak_Sekolah = explode('|', $survey->tindak_lanjut);
                                            $dt = collect($faktor)->whereIn('value', $arr_tidak_Sekolah);

                                            $hasil_faktor = '';
                                            if ($survey->tindak_lanjut != '') {
                                                $hasil_faktor .= '<b>Rencana Tindak Lanjut</b>';
                                                $hasil_faktor .= '<ol>';
                                                foreach ($dt as $d) {
                                                    $hasil_faktor .= '<li>- ' . $d['text'] . '</li>';
                                                }
                                                $hasil_faktor .= '</ol>';
                                            }

                                            if ($survey->penanganan_abk != '') {
                                                $hasil_faktor .= $survey->penanganan_abk;
                                            }
                                            echo $hasil_faktor;
                                        @endphp
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>
                @endif

                <!-- Riwayat KKA -->
                {{-- <section class="card p-6">
                    <div class="flex items-center gap-2 mb-4 font-bold border-b pb-2" style="color: #5d92c7">
                        <i class="fas fa-clipboard"></i>
                        <h2>Riwayat Kartu Kembang Anak</h2>
                    </div>
                    <div class="grid grid-cols-1" style="max-height: 500px;overflow:auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-xs font-semibold text-slate-400 uppercase bg-slate-50">
                                    <th class="px-4 py-2">Tanggal Pengecekan</th>
                                    <th class="px-4 py-2">Tahun</th>
                                    <th class="px-4 py-2">Bulan</th>
                                    <th class="px-4 py-2">Berat (kg)</th>
                                    <th class="px-4 py-2">Tinggi (cm)</th>
                                    <th class="px-4 py-2">Status Gizi</th>
                                    <th class="px-4 py-2">Sekolah</th>
                                    <th class="px-4 py-2">Kesimpulan KKA</th>
                                    <th class="px-4 py-2">Rekomendasi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y">
                                @if ($kka->count() > 0)
                                    @foreach ($kka as $g)
                                        <tr>
                                            <td class="px-4 py-3 font-medium">{!! App\Tools\Waktu::parseFullDate(date('Y-m-d', strtotime($g->tgl_pengecekan))) !!}</td>
                                            <td class="px-4 py-3">{!! $g->tahun !!}</td>
                                            <td class="px-4 py-3 text-slate-500">{{ $g->bulan }}</td>
                                            <td class="px-4 py-3 text-slate-500">{{ $g->bb }}</td>
                                            <td class="px-4 py-3 text-slate-500">{{ $g->tb }}</td>
                                            <td class="px-4 py-3 text-slate-500">{{ $g->status_gizi }}</td>
                                            <td class="px-4 py-3 text-slate-500">{{ $g->nama_sekolah }}
                                                ({{ $g->npsn }})
                                            </td>
                                            <td class="px-4 py-3 text-slate-500">{{ $g->kesimpulan_kka }}</td>
                                            <td class="px-4 py-3 text-slate-500">{{ $g->rekomendasi }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="9" style="text-align: center">Tidak ditemukan riwayat
                                            KKA</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </section> --}}

                <!-- Riwayat Profil Sekolah -->
                <section class="card p-6 bg-gradient-to-br from-indigo-50 to-white border border-indigo-100">
                    <div class="flex items-center gap-2 mb-4 font-bold border-b pb-2" style="color: #5d92c7">
                        <i class="fas fa-school"></i>
                        <h2>Riwayat Profil Sekolah</h2>
                    </div>
                    <div class="grid grid-cols-1" style="max-height: 500px;overflow:auto">
                        <table class="w-full text-left">
                            <thead
                                class="sticky top-0 z-10 bg-slate-100 text-xs font-semibold text-slate-600 uppercase shadow-sm">
                                <tr class="text-xs font-semibold text-slate-400 uppercase bg-slate-50">
                                    <th class="px-4 py-2">NIK</th>
                                    <th class="px-4 py-2">Nama</th>
                                    <th class="px-4 py-2">NPSN</th>
                                    <th class="px-4 py-2">Sekolah</th>
                                    <th class="px-4 py-2">Jenjang</th>
                                    <th class="px-4 py-2">Kelas</th>
                                    <th class="px-4 py-2">Rombel</th>
                                    <th class="px-4 py-2">Status Sekolah</th>
                                    <th class="px-4 py-2">Status Siswa</th>
                                    <th class="px-4 py-2">Status Inklusi</th>
                                    <th class="px-4 py-2">Alumni</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y">
                                @if ($data_profil->count() > 0)
                                    @foreach ($data_profil as $g)
                                        <tr>
                                            <td class="px-4 py-3">{!! $g->nik !!}</td>
                                            <td class="px-4 py-3 text-slate-500">{{ $g->nama }}</td>
                                            <td class="px-4 py-3 text-slate-500">{{ $g->npsn }}</td>
                                            <td class="px-4 py-3 text-slate-500">{{ $g->nama_sekolah }}</td>
                                            <td class="px-4 py-3 text-slate-500">{{ $g->jenjang }}</td>
                                            <td class="px-4 py-3 text-slate-500">{{ $g->kelas }}</td>
                                            <td class="px-4 py-3 text-slate-500">{{ $g->rombel }}</td>
                                            <td class="px-4 py-3 text-slate-500">{{ $g->status }}</td>
                                            <td class="px-4 py-3 text-slate-500">{{ $g->status_siswa }}</td>
                                            <td class="px-4 py-3 text-slate-500">
                                                {{ $g->kebutuhan_khusus }}
                                                @if ($g->kategori_inklusi != '')
                                                    <br>({{ $g->kategori_inklusi }})
                                                @endif
                                                @if ($g->berkas_inklusi_psikolog != '')
                                                    <br>
                                                    <a href="http://103.76.17.11/profilsekolah_file/{{ $g->berkas_inklusi_psikolog }}"
                                                        class="btn btn-sm btn-success" target="_blank">Liat file
                                                        psikolog</a>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-slate-500">{{ $g->status_alumni }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="9" style="text-align: center">Tidak ditemukan riwayat sekolah
                                            di Profil Sekolah</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- RIWAYAT PMT --}}
                <section class="card p-6 bg-gradient-to-br from-indigo-50 to-white border border-indigo-100">
                    <div class="flex items-center gap-2 mb-4 font-bold border-b pb-2" style="color: #5d92c7">
                        <i class="fas fa-utensils"></i>
                        <h2>Riwayat PMT</h2>
                    </div>
                    <div class="grid grid-cols-1" style="max-height: 500px;overflow:auto">
                        <table class="w-full text-left">
                            <thead
                                class="sticky top-0 z-10 bg-slate-100 text-xs font-semibold text-slate-600 uppercase shadow-sm">
                                <tr class="text-xs font-semibold text-slate-400 uppercase bg-slate-50">
                                    <th class="px-4 py-2">NIK</th>
                                    <th class="px-4 py-2">Nama</th>
                                    <th class="px-4 py-2">NPSN</th>
                                    <th class="px-4 py-2">Sekolah</th>
                                    <th class="px-4 py-2">Periode</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y">
                                @if ($pmt->count() > 0)
                                    @foreach ($pmt as $g)
                                        <tr>
                                            <td class="px-4 py-3">{!! $g->nik !!}</td>
                                            <td class="px-4 py-3 text-slate-500">{{ $g->nama }}</td>
                                            <td class="px-4 py-3 text-slate-500">{{ $g->npsn }}</td>
                                            <td class="px-4 py-3 text-slate-500">{{ $g->nama_sekolah }}</td>
                                            <td class="px-4 py-3 text-slate-500">{{ $g->tahun }}
                                                {{ $g->bulan }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="9" style="text-align: center">Tidak ditemukan riwayat sekolah
                                            di Profil Sekolah</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </section>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- MANASIK -->
                    <section class="card p-6" style="background: linear-gradient(135deg, #dcfff6 , #ffffff);">
                        <div
                            class="flex items-center gap-2 mb-4 text-indigo-600 font-bold border-b border-indigo-200 pb-2">
                            <i class="fas fa-cube"></i>
                            <h2>Riwayat Manasik Haji</h2>
                        </div>
                        @if ($manasik->count() > 0)
                            <ul class="space-y-3">
                                @foreach ($manasik as $b)
                                    <li class="flex justify-between items-start">
                                        <div>
                                            <p><b>Tanggal Pengajuan:</b> {!! App\Tools\Waktu::parseFullDate(date('Y-m-d', strtotime($b->tanggal_pengajuan))) !!}</p>
                                            <p><b>Sekolah:</b> {{ $b->jenjang }} : {{ $b->nama_sekolah }}
                                                ({{ $b->npsn }})
                                            </p>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            Tidak ada riwayat Manasik Haji
                        @endif
                    </section>

                    <!-- TALENTA -->
                    <section class="card p-6" style="background: linear-gradient(135deg, #dcfff6 , #ffffff);">
                        <div
                            class="flex items-center gap-2 mb-4 text-indigo-600 font-bold border-b border-indigo-200 pb-2">
                            <i class="fas fa-paint-brush"></i>
                            <h2>Riwayat Talenta Seni</h2>
                        </div>
                        @if ($talenta->count() > 0)
                            <ul class="space-y-3">
                                @foreach ($talenta as $b)
                                    <li class="flex justify-between items-start">
                                        <div>
                                            <p><b>Tanggal:</b> {!! App\Tools\Waktu::parseFullDate(date('Y-m-d', strtotime($b->tanggal))) !!}</p>
                                            <p><b>Lokasi:</b> {!! $b->nama_tempat !!}</p>
                                            <p><b>Judul Seni:</b> {!! $b->judul !!}</p>
                                            <p><b>Sekolah:</b> {{ $b->jenjang }} : {{ $b->nama_sekolah }}
                                                ({{ $b->npsn }})
                                            </p>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            Tidak ada riwayat Talenta Seni
                        @endif
                    </section>

                    <!-- EDU CITY -->
                    <section class="card p-6" style="background: linear-gradient(135deg, #dcfff6 , #ffffff);">
                        <div
                            class="flex items-center gap-2 mb-4 text-indigo-600 font-bold border-b border-indigo-200 pb-2">
                            <i class="fas fa-bus"></i>
                            <h2>Riwayat Edu City Tour</h2>
                        </div>
                        @if ($edu->count() > 0)
                            <ul class="space-y-3">
                                @foreach ($edu as $b)
                                    <li class="flex justify-between items-start">
                                        <div>
                                            <p><b>Tanggal Jadwal:</b> {!! App\Tools\Waktu::parseFullDate(date('Y-m-d', strtotime($b->tanggal))) !!}</p>
                                            <p><b>Sekolah:</b> {{ $b->jenjang ?? '' }} : {{ $b->nama_sekolah ?? '' }}
                                                ({{ $b->npsn ?? '' }})
                                            </p>
                                            <p><b>Wisata:</b> {{ $b->nama_wisata ?? '' }}
                                            </p>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            Tidak ada riwayat Edu City Tour
                        @endif
                    </section>
                </div>

                @if ($data_sekarang->kategori_inklusi ?? '' != '')
                    <!-- Riwayat Profil Perkembangan Inklusi -->
                    <section class="card p-6 bg-gradient-to-br from-crimson-50 to-white border border-crimson-100">
                        <div class="flex items-center gap-2 mb-4 font-bold border-b pb-2" style="color: #5d92c7">
                            <i class="fas fa-wheelchair"></i>
                            <h2>Riwayat Perkembangan Inklusi <b>({{ $data_sekarang->kategori_inklusi }})</b></h2>
                        </div>
                        <div class="grid grid-cols-1" style="max-height: 500px;overflow:auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="text-xs font-semibold text-slate-400 uppercase bg-slate-50">
                                        <th class="px-4 py-2">NIK</th>
                                        <th class="px-4 py-2">Nama</th>
                                        <th class="px-4 py-2">NPSN</th>
                                        <th class="px-4 py-2">Sekolah</th>
                                        <th class="px-4 py-2">Tanggal</th>
                                        <th class="px-4 py-2">Kategori Iklusi</th>
                                        <th class="px-4 py-2">Indikator</th>
                                        <th class="px-4 py-2">Skor</th>
                                        <th class="px-4 py-2">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm divide-y">
                                    @if ($data_kembang_inklusi->count() > 0)
                                        @foreach ($data_kembang_inklusi as $g)
                                            <tr>
                                                <td class="px-4 py-3">{!! $g->nik !!}</td>
                                                <td class="px-4 py-3 text-slate-500">{{ $g->nama }}</td>
                                                <td class="px-4 py-3 text-slate-500">{{ $g->npsn }}</td>
                                                <td class="px-4 py-3 text-slate-500">{{ $g->nama_sekolah }}</td>
                                                <td class="px-4 py-3 text-slate-500">{{ $g->tanggal }}</td>
                                                <td class="px-4 py-3 text-slate-500">{{ $g->isi }}</td>
                                                <td class="px-4 py-3 text-slate-500">{{ $g->nama_indikator }}</td>
                                                <td class="px-4 py-3 text-slate-500">{{ $g->skor }}</td>
                                                <td class="px-4 py-3 text-slate-500">{{ $g->catatan_khusus }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="9" style="text-align: center">Tidak ditemukan riwayat
                                                perkembangan inklusi</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </section>
                @endif
            </div>

            <div class="md:col-span-12 spcae-y-6">
                <!-- Riwayat Kesehatan (Dinkes) -->
                <section class="card p-6" style="background: linear-gradient(135deg,#ffcdfc,#ffffff)">
                    <div class="flex items-center justify-between mb-4 border-b pb-2">
                        <div class="flex items-center gap-2 font-bold" style="color: #d10dc4">
                            <i class="fas fa-syringe"></i>
                            <h2>Riwayat Kesehatan (Dinkes)</h2>
                        </div>
                        {{-- <span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-1 rounded">Lengkap</span> --}}
                    </div>
                    <div class="overflow-x-auto" style="max-height:500px">
                        @if (!empty($riwayat_kesehatan))
                            <p>Berat Badan : <b>{{ $riwayat_kesehatan->beratbadan ?? '' }} Kg</b></p>
                            <p>Status Berat Badan : <b>{{ $riwayat_kesehatan->bb_u ?? '' }}</b></p>
                            <p>Tinggi Badan : <b>{{ $riwayat_kesehatan->tinggibadan ?? '' }} Cm</b></p>
                            <p>Status Tinggi Badan : <b>{{ $riwayat_kesehatan->tb_u ?? '' }}</b></p>
                            <br>
                            <p>Riwayat Vaksin</p>
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="text-xs font-semibold text-slate-400 uppercase bg-slate-50">
                                        <th class="px-4 py-2">Tanggal</th>
                                        <th class="px-4 py-2">Jenis Imunisasi</th>
                                        <th class="px-4 py-2">Imunisasi</th>
                                        <th class="px-4 py-2">Dosis</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm divide-y">
                                    @if (!empty($riwayat_kesehatan->imunisasi))
                                        @php
                                            $imunisasi = json_decode($riwayat_kesehatan->imunisasi);
                                            $imunisasi = collect($imunisasi)->sortByDesc('tanggal');
                                        @endphp
                                        @foreach ($imunisasi as $im)
                                            <tr>
                                                <td class="px-4 py-3">{!! App\Tools\Waktu::parseFullDate($im->tanggal) !!}</td>
                                                <td class="px-4 py-3 font-medium">{{ $im->jenisimunisasi }}</td>
                                                <td class="px-4 py-3 text-slate-500">{{ $im->imunisasi }}</td>
                                                <td class="px-4 py-3 text-slate-500">{{ $im->dosis }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4" style="text-align: center">Tidak ditemukan riwayat
                                                Imunisasi</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            <br>
                            <p>Riwayat Vitamin</p>
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="text-xs font-semibold text-slate-400 uppercase bg-slate-50">
                                        <th class="px-4 py-2">Tanggal</th>
                                        <th class="px-4 py-2">Intervensi</th>
                                        <th class="px-4 py-2">Unit Kerja</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm divide-y">
                                    @if (!empty($riwayat_kesehatan->vitamin))
                                        @php
                                            $vitamin = json_decode($riwayat_kesehatan->vitamin);
                                            $vitamin = collect($vitamin)->sortByDesc('tanggal');
                                        @endphp
                                        @foreach ($vitamin as $im)
                                            <tr>
                                                <td class="px-4 py-3">{!! App\Tools\Waktu::parseFullDate($im->tanggal) !!}</td>
                                                <td class="px-4 py-3 font-medium">{{ $im->intervensi }}</td>
                                                <td class="px-4 py-3 text-slate-500">{{ $im->unitkerja }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="3" style="text-align: center">Tidak ditemukan riwayat
                                                Vitamin</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            <br>
                            <p>Riwayat Konginetal</p>
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="text-xs font-semibold text-slate-400 uppercase bg-slate-50">
                                        <th class="px-4 py-2">Tanggal</th>
                                        <th class="px-4 py-2">Nama</th>
                                        <th class="px-4 py-2">Unit Kerja</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm divide-y">
                                    @if (!empty($riwayat_kesehatan->konginetal))
                                        @php
                                            $konginetal = json_decode($riwayat_kesehatan->konginetal);
                                            $konginetal = collect($konginetal)->sortByDesc('tanggal');
                                        @endphp
                                        @foreach ($konginetal as $im)
                                            <tr>
                                                <td class="px-4 py-3">{!! App\Tools\Waktu::parseFullDate($im->tanggal) !!}</td>
                                                <td class="px-4 py-3 font-medium">{{ $im->nama }}</td>
                                                <td class="px-4 py-3 text-slate-500">{{ $im->unitkerja }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="3" style="text-align: center">Tidak ditemukan riwayat
                                                Konginetal</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        @else
                            <p>Tidak ditemukan riwayat kesehatan di API Dinas Kesehatan</p>
                        @endif
                    </div>
                </section>
            </div>
        </div>

        <!-- Footer -->
        <footer class="mt-12 text-center text-slate-400 text-sm">
            <p>&copy; 2026 SIBUNDA (Dinas Pendidikan Kota Surabaya).</p>
        </footer>
    </div>

</body>

</html>
