@extends('paudmutu.master.master')

@section('css_tambahan')
@endsection

@section('content')
    <section id="tab-dashboard" class="space-y-6">
        <div class="grid">
            <div class="bg-white p-6 border border-grey-600 rounded-2xl">
                <div class="text-2xl font-bold">
                    Dashboard
                </div>
            </div>
        </div>

        {{-- Berkas --}}
        <div class="grid grid-cols-4 md:grid-cols-4 gap-4">
            @php
                $jumlah_berkas = collect($berkas)->where('kategori', 'berkas')->first();
                $jumlah_berkas_ada = collect($berkas)->where('kategori', 'ada')->first();
            @endphp
            <div class="bg-white border border-gray-200 p-4 rounded-2xl shadow-sm">
                <h4 class="text-l sm:text-l font-bold text-gray-900">Pendaftaran</h4>
                <p>{{ $jumlah_berkas_ada->jumlah_daftar ?? '0' }} / {{ $jumlah_berkas->jumlah_daftar ?? '0' }}</p>
            </div>
            <div class="bg-white border border-gray-200 p-4 rounded-2xl shadow-sm">
                <h4 class="text-l sm:text-l font-bold text-gray-900">Administrasi Tahap 1</h4>
                <p>{{ $jumlah_berkas_ada->jumlah_tahap1 ?? '0' }} / {{ $jumlah_berkas->jumlah_tahap1 ?? '0' }}</p>
            </div>
            <div class="bg-white border border-gray-200 p-4 rounded-2xl shadow-sm">
                <h4 class="text-l sm:text-l font-bold text-gray-900">Mutu Layanan Tahap 2</h4>
                <p>{{ $jumlah_berkas_ada->jumlah_tahap2 ?? '0' }} / {{ $jumlah_berkas->jumlah_tahap2 ?? '0' }}</p>
            </div>
            <div class="bg-white border border-gray-200 p-4 rounded-2xl shadow-sm">
                <h4 class="text-l sm:text-l font-bold text-gray-900">Tahap III</h4>
                <p>{{ $sekolah->nama ?? '' }}</p>
            </div>
        </div>

        <!-- Identitas -->
        <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-sm">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Identitas Lembaga</h2>
            <p>
                Data lembaga sesuai isian profil sekolah.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="bg-white border border-gray-200 p-2 rounded-2xl shadow-sm">
                    <h4 class="text-l sm:text-l font-bold text-gray-900">Nama Lembaga</h4>
                    <p>{{ $sekolah->nama ?? '' }}</p>
                </div>
                <div class="bg-white border border-gray-200 p-2 rounded-2xl shadow-sm">
                    <h4 class="text-l sm:text-l font-bold text-gray-900">NPSN Lembaga</h4>
                    <p>{{ $sekolah->npsn ?? '' }}</p>
                </div>
                <div class="bg-white border border-gray-200 p-2 rounded-2xl shadow-sm">
                    <h4 class="text-l sm:text-l font-bold text-gray-900">Bentuk Satuan</h4>
                    <p>{{ $sekolah->jenjang ?? '' }}</p>
                </div>
                <div class="bg-white border border-gray-200 p-2 rounded-2xl shadow-sm">
                    <h4 class="text-l sm:text-l font-bold text-gray-900">Kepala Satuan</h4>
                    <p>{{ $sekolah->nama_ks ?? '' }}</p>
                </div>
                <div class="bg-white border border-gray-200 p-2 rounded-2xl shadow-sm">
                    <h4 class="text-l sm:text-l font-bold text-gray-900">Alamat</h4>
                    <p>{{ $sekolah->alamat ?? '' }}, RT {{ $sekolah->rt ?? '' }}, RW {{ $sekolah->rw ?? '' }},
                        {{ $sekolah->kelurahan_dispenduk ?? '' }}, {{ $sekolah->kecamatan_dispenduk ?? '' }},</p>
                </div>
                <div class="bg-white border border-gray-200 p-2 rounded-2xl shadow-sm">
                    <h4 class="text-l sm:text-l font-bold text-gray-900">Kontak</h4>
                    <p>{{ $sekolah->telp ?? '' }}</p>
                    <p>{{ $sekolah->email ?? '' }}</p>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js_tambahan')
@endsection
