@extends('landing.layout')
@section('css')
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .calendar-container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .calendar-header {
            background-color: #0d6efd;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            padding: 10px;
        }

        .weekday {
            text-align: center;
            font-weight: 600;
            color: #6c757d;
            padding: 10px 0;
            text-transform: uppercase;
            font-size: 0.8rem;
        }

        .day {
            aspect-ratio: 1 / 1;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border-radius: 10px;
            transition: all 0.2s ease;
            position: relative;
            font-weight: 500;
        }

        .day:hover:not(.empty) {
            background-color: #e9ecef;
            color: #0d6efd;
        }

        .day.today {
            background-color: #0d6efd !important;
            color: white !important;
            font-weight: bold;
        }

        .day.selected {
            border: 2px solid #0d6efd;
            background-color: #e7f1ff;
        }

        .empty {
            cursor: default;
        }

        .btn-nav {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 5px 15px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .btn-nav:hover {
            background: rgba(255, 255, 255, 0.4);
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="row bg-light" style="padding-top: 10px;padding-bottom: 10px;">
            <div class="col-lg-12 text-center">
                <h1>Jadwal Peminjaman Graha Bunda PAUD</h1>
            </div>
        </div>
        <div class="row bg-light">
            <div class="col-md-4">
                <div class="calendar-container">
                    <!-- Header Kalender -->
                    <div class="calendar-header">
                        <button class="btn-nav" id="prevMonth">&lt;</button>
                        <h2 id="monthDisplay" class="mb-0">Januari 2024</h2>
                        <button class="btn-nav" id="nextMonth">&gt;</button>
                    </div>

                    <!-- Baris Nama Hari -->
                    <div class="calendar-grid">
                        <div class="weekday">Min</div>
                        <div class="weekday">Sen</div>
                        <div class="weekday">Sel</div>
                        <div class="weekday">Rab</div>
                        <div class="weekday">Kam</div>
                        <div class="weekday">Jum</div>
                        <div class="weekday">Sab</div>
                    </div>

                    <!-- Grid Angka Tanggal -->
                    <div id="daysContainer" class="calendar-grid"></div>
                </div>
            </div>
            <div class="col-md-8">
                <div>
                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal_tambah"
                        class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Tambah
                        Kegiatan</a>
                </div>
                <!-- Info Box -->
                <div class="text-center mt-4 text-muted">
                    <p id="selectedDateText">Pilih tanggal untuk melihat detail.</p>
                    <div id="wadah_jadwal" class="table-responsive"></div>
                </div>
            </div>
        </div>
        <div class="row bg-light" style="padding-top: 10px;padding-bottom: 10px;">
        </div>
        <div class="modal-page"></div>
    </div>

    @include('landing.sewa_gedung.modal')
@endsection

@section('js')
    <script>
        const monthDisplay = document.getElementById('monthDisplay');
        const daysContainer = document.getElementById('daysContainer');
        const prevMonthBtn = document.getElementById('prevMonth');
        const nextMonthBtn = document.getElementById('nextMonth');
        const selectedDateText = document.getElementById('selectedDateText');
        var warna = [];

        let currentDate = new Date();

        const monthNames = [
            "Januari", "Februari", "Maret", "April", "Mei", "Juni",
            "Juli", "Agustus", "September", "Oktober", "November", "Desember"
        ];

        function renderCalendar() {
            daysContainer.innerHTML = '';

            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();

            monthDisplay.innerText = `${monthNames[month]} ${year}`;

            const firstDayIndex = new Date(year, month, 1).getDay();
            const lastDay = new Date(year, month + 1, 0).getDate();

            const today = new Date();
            const isCurrentMonth = today.getFullYear() === year && today.getMonth() === month;

            for (let i = 0; i < firstDayIndex; i++) {
                const emptyDiv = document.createElement('div');
                emptyDiv.classList.add('day', 'empty');
                daysContainer.appendChild(emptyDiv);
            }

            const tanggal_sekarang = new Date(year, month, today.getDate());
            selectedDateText.innerHTML =
                `Anda memilih tanggal: <strong>${today.getDate()} ${monthNames[month]} ${year}</strong>`;
            get_jadwal(tanggal_sekarang);

            for (let i = 1; i <= lastDay; i++) {
                const dayDiv = document.createElement('div');
                dayDiv.classList.add('day');
                dayDiv.innerText = i;

                if (isCurrentMonth && i === today.getDate()) {
                    dayDiv.classList.add('today');
                }

                dayDiv.onclick = () => {
                    const allDays = document.querySelectorAll('.day');
                    allDays.forEach(d => d.classList.remove('selected'));

                    dayDiv.classList.add('selected');
                    selectedDateText.innerHTML =
                        `Anda memilih tanggal: <strong>${i} ${monthNames[month]} ${year}</strong>`;

                    const tanggal_sekarang = new Date(year, month, i);
                    get_jadwal(tanggal_sekarang);
                };

                daysContainer.appendChild(dayDiv);
            }
        }

        prevMonthBtn.onclick = () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
        };

        nextMonthBtn.onclick = () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
        };

        window.onload = renderCalendar;

        function get_jadwal(n_tanggal) {
            const tanggal = new Date(n_tanggal);
            const m1 = tanggal.getMonth();
            const day1 = tanggal.getDate();
            const y = tanggal.getFullYear();
            const m = String(tanggal.getMonth() + 1).padStart(2, '0');
            const day = String(tanggal.getDate()).padStart(2, '0');
            const formatted = `${y}-${m}-${day}`;

            const allDays = document.querySelectorAll('.day');
            allDays.forEach(d => d.classList.remove('selected'));

            if (allDays[day1 - 1]) {
                allDays[day1 - 1].classList.add('selected');
            }

            selectedDateText.innerHTML =
                `Anda memilih tanggal: <strong>${day} ${monthNames[m1]} ${y}</strong>`;

            $('#wadah_jadwal').html('<div class="text-center"><i class="fa fa-refresh fa-spin"></i></div>');

            $.post('{{ route('landing', ['get_jadwal']) }}', {
                tanggal: formatted,
            }, function(data) {
                $('#wadah_jadwal').html(data.content);

                const allDays = document.querySelectorAll('.day');
                const kalender = data.kalender;

                if (kalender.length > 0) {
                    $.each(kalender, function(k, v) {
                        if (allDays[parseInt(v.tanggal) - 1]) {
                            if (warna[parseInt(v.tanggal) - 1] !== undefined) {} else {
                                warna[parseInt(v.tanggal) - 1] = v.warna;
                            }
                            allDays[parseInt(v.tanggal) - 1].style.background = warna[parseInt(v.tanggal) -
                                1];
                        }
                    });
                }
            }).fail(function() {
                alert('500 - Internal Server Error');
            });
        }

        function simpan_sewa() {
            event.preventDefault();
            $('.tempat_error').hide();

            var data = $('.form_sewa').serialize(0);
            $.post('{{ route('landing', ['simpan_sewa']) }}', data, function(data) {
                if (data.code == 200) {
                    $('.tempat_error').hide();
                    $('#modal_tambah').modal('hide');
                    get_jadwal(data.tanggal);
                } else {
                    $('.tempat_error').show();
                    $('.tempat_error').html(data.pesan);
                }
            }).fail(function() {
                alert('500 - Internal Server Error');
            });
        }
    </script>
@endsection
