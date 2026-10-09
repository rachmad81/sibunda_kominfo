<!-- Modal -->
<div id="modal_profil" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Monitor MPLS
                    @php
                        $pertanyaan = $pertanyaans->first();
                        $data = $pertanyaan ? $pertanyaan->data : null;
                        $sekolahDetail = $data ? "{$data->npsn} - {$data->nama}" : '';
                    @endphp
                    {{ $sekolahDetail }}
                </h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead style="background-color:#989898;">
                                <tr>
                                    <th style="max-width:1px;">
                                        No
                                    </th>
                                    <th>
                                        Pertanyaan
                                    </th>
                                </tr>
                            </thead>
                            @php
                                $no = 0;
                            @endphp
                            <tbody style="color:black;text-align:justify;text-justify:inter-word;">
                                @foreach ($pertanyaans as $key => $pertanyaan)
                                    <tr id="pertanyaan-{{ $pertanyaan->id }}"
                                        {{ $pertanyaan->parent_id ? "parent_id={$pertanyaan->parent_id}" : '' }}
                                        {{ $pertanyaan->is_shown_when_parent_is !== null ? "is_shown_when_parent_is={$pertanyaan->is_shown_when_parent_is}" : '' }}>
                                        <td>
                                            {{ $pertanyaan->parent_id === null ? ++$no : null }}
                                        </td>
                                        <td>
                                            {{ $pertanyaan->pertanyaan }}<br>
                                            @switch($pertanyaan->tipe_jawaban)
                                                @case('select')
                                                    <select id="jawaban-{{ $pertanyaan->id }}"
                                                        name="jawaban[{{ $pertanyaan->id }}]" class="form-control" required>
                                                        <option value="" disabled selected>Pilih Jawaban...</option>
                                                        @foreach (explode(';;;', $pertanyaan->list_jawaban) as $jawaban)
                                                            <option value="{{ $jawaban }}"
                                                                @if ($pertanyaan->jawabanMonitor) {{ $pertanyaan->jawabanMonitor->jawaban == $jawaban ? 'selected' : '' }} @endif>
                                                                {{ $jawaban }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @break

                                                @case('checkbox')
                                                    @php
                                                        $listJawaban = explode(';;;', $pertanyaan->list_jawaban);
                                                        $jawabanMonitor = $pertanyaan->jawabanMonitor
                                                            ? json_decode($pertanyaan->jawabanMonitor->jawaban)
                                                            : [];
                                                        $jawabanMonitor = (array) $jawabanMonitor;
                                                        $function = function ($jawaban) use ($listJawaban) {
                                                            return in_array($jawaban, $listJawaban) ? false : true;
                                                        };
                                                        $jawabanLainnya = array_filter($jawabanMonitor, $function);
                                                        $jawabanLainnya = array_shift($jawabanLainnya);
                                                    @endphp
                                                    @foreach ($listJawaban as $key => $jawaban)
                                                        <div class="form-check">
                                                            <input id="jawaban-{{ $pertanyaan->id }}_{{ $key }}"
                                                                name="jawaban[{{ $pertanyaan->id }}][{{ $key }}]"
                                                                type="checkbox" value="{{ $jawaban }}"
                                                                @if ($pertanyaan->jawabanMonitor) {{ in_array($jawaban, $jawabanMonitor) ? 'checked' : '' }} @endif>
                                                            <label for="jawaban-{{ $pertanyaan->id }}_{{ $key }}">
                                                                {{ $jawaban }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                    @if ($jawabanLainnya)
                                                        <div class="form-check">
                                                            <input id="jawaban-{{ $pertanyaan->id }}_{{ $key + 1 }}"
                                                                name="jawaban[{{ $pertanyaan->id }}][{{ $key + 1 }}]"
                                                                type="checkbox" value="{{ $jawabanLainnya }}" checked>
                                                            <label for="jawaban-{{ $pertanyaan->id }}_{{ $key + 1 }}">
                                                                {{ $jawabanLainnya }}
                                                            </label>
                                                        </div>
                                                    @endif
                                                @break

                                                @case('text')
                                                    <textarea id="jawaban-{{ $pertanyaan->id }}" name="jawaban[{{ $pertanyaan->id }}]" class="form-control"
                                                        placeholder="Ketik jawaban disini..." required>{{ $pertanyaan->jawabanMonitor ? $pertanyaan->jawabanMonitor->jawaban : '' }}</textarea>
                                                @break
                                            @endswitch
                                        </td>
                                        {{-- <td>
                                            <select id="jawaban[{{ $pertanyaan['id'] }}]"
                                                name="jawaban[{{ $pertanyaan['id'] }}]" class="form-control">
                                                <option value="" selected>Pilih...</option>
                                                <option value="true"
                                                    {{ $pertanyaan->jawabanSurvey !== null ? ($pertanyaan->jawabanSurvey->jawaban === true ? 'selected' : '') : '' }}>
                                                    Ada/Sudah</option>
                                                <option value="false"
                                                    {{ $pertanyaan->jawabanSurvey !== null ? ($pertanyaan->jawabanSurvey->jawaban === false ? 'selected' : '') : '' }}>
                                                    Tidak Ada/Belum</option>
                                            </select>
                                        </td>
                                        <td>
                                            <textarea id="keterangan[{{ $pertanyaan['id'] }}]" name="keterangan[{{ $pertanyaan['id'] }}]" class="form-control">{{ $pertanyaan->jawabanSurvey ? $pertanyaan->jawabanSurvey->keterangan : '' }}</textarea>
                                        </td>
                                        <td>
                                            <input id="bukti_dokumentasi[{{ $pertanyaan['id'] }}]"
                                                name="bukti_dokumentasi[{{ $pertanyaan['id'] }}]" type="text"
                                                class="form-control"
                                                {{ $pertanyaan->jawabanSurvey ? "value={$pertanyaan->jawabanSurvey->bukti_dokumentasi}" : '' }}>
                                        </td> --}}
                                    </tr>
                                    {{-- @endif --}}
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {{-- <div class="modal-footer">
                <a href="javascript:void(0)" class="btn btn-primary" onclick="simpan()">Simpan</a>
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div> --}}
        </div>

    </div>
</div>
<style type="text/css">
    label {
        font-weight: 10;
    }

    table.kop_jadwal tr td {
        text-align: left;
        vertical-align: top;
        padding: 5px;
    }

    .modal-lg {
        max-width: 1200px !important;
    }
</style>
<script type="text/javascript">
    $(document).ready(function() {
        $('#modal_profil').modal({
            backdrop: 'static'
        });
    });
</script>
<script type="text/javascript">
    var pertanyaanMonitorings = @json($pertanyaans);
    $(document).ready(function() {
        startPage();
    });

    $('form').submit(function(event) {
        event.preventDefault();

        //Validasi checkbox salah satu harus terisi
        var checkboxes = document.querySelectorAll(`input[type="checkbox"]`);
        var inputs = [];
        checkboxes.forEach(function(checkbox, index) {
            inputs.push(checkbox.id.split('_')[0]);
        });
        // ambil checkbox dengan prefix id yang unik saja
        inputs = [...new Set(inputs)];
        var stop = false;
        inputs.forEach(function(input, index) {
            if (stop === false) {
                var validateCheckboxes = document.querySelectorAll(
                    `input[type="checkbox"][id^="${input}"]:checked`);
                if (validateCheckboxes.length <= 0) {
                    window.scroll(0, findPosition(document.querySelector(
                        `input[type="checkbox"][id^="${input}"]`)));
                    alert('Isi minimal satu dari jawaban ini!');
                    stop = true;
                    return;
                }
            }
        });
        if (stop === true) {
            return;
        }
        //Jika validasi sudah sukses dan semua checkbox telah terisi
        this.submit();
    });

    // Logic untuk dirun ketika page pertama dibuka
    function startPage() {
        //Logic untuk show/hide semua pertanyaan
        var pertanyaans = document.querySelectorAll('[id^="pertanyaan-"]');
        pertanyaans.forEach(function(pertanyaan, index) {
            var id = pertanyaan.id.split('-')[1];
            var jawabans = document.querySelectorAll(`[id^=jawaban-${id}]`);
            var pertanyaanMonitoring = pertanyaanMonitorings.filter(function(pertanyaanMonitoring, index) {
                return pertanyaanMonitoring.id == id;
            })[0];
            // Logic untuk hide semua pertanyaan yang memiliki parent, dan show semua pertanyaan yang adalah parent utama
            if (pertanyaan.hasAttribute('parent_id') === true) {
                if (pertanyaanMonitoring.jawaban_monitor === null || (pertanyaanMonitoring.jawaban_monitor
                        .jawaban === null)) {
                    pertanyaan.style.display = 'none';
                    jawabans.forEach(function(jawaban, index) {
                        jawaban.disabled = true;
                        if (jawaban.type != 'checkbox') {
                            jawaban.required = false;
                        }
                    });
                }
            } else {
                pertanyaan.style.display = '';
                jawabans.forEach(function(jawaban, index) {
                    jawaban.disabled = false;
                    if (jawaban.type != 'checkbox') {
                        jawaban.required = true;
                    }
                });
            }
            // Logic untuk memberikan semua pertanyaan yang parent dari pertanyaan lain behaviour onchange untuk menampilkan/menyembunyikan anaknya
            var childrens = document.querySelectorAll(`[parent_id="${id}"]`);
            if (childrens.length > 0) {
                jawabans.forEach(function(jawaban, index) {
                    var previousOnchange = jawaban.getAttribute('onchange');
                    previousOnchange = previousOnchange ? previousOnchange : '';
                    jawaban.setAttribute('onchange', `${previousOnchange}childrenLogic();`);
                });
            }
        });

        //Logic untuk semua jawaban bertipe "select" untuk dapat diinput manual oleh user jika jawaban yang dipilih berisi kata "Lainnya".
        var jawabans = document.querySelectorAll('select[id^="jawaban-"], input[type="checkbox"][id^="jawaban-"]');
        jawabans.forEach(function(jawaban, index) {
            var previousOnchange = jawaban.getAttribute('onchange');
            previousOnchange = previousOnchange ? previousOnchange : '';
            jawaban.setAttribute('onchange', `${previousOnchange}lainnyaLogic()`);
        });
    }

    // Logic ketika pertanyaan parent memiliki value yang sama atau berbeda dengan children nya
    function childrenLogic() {
        var parentId = event.target.id.split('-')[1];
        var value = event.target.value;
        var pertanyaans = document.querySelectorAll(`[id^="pertanyaan-"][parent_id="${parentId}"]`);
        pertanyaans.forEach(function(pertanyaan, index) {
            var id = pertanyaan.id.split('-')[1];
            var jawabans = document.querySelectorAll(`[id^=jawaban-${id}]`);
            if (pertanyaan.getAttribute('is_shown_when_parent_is') == value) {
                pertanyaan.style.display = '';
                jawabans.forEach(function(jawaban, index) {
                    jawaban.disabled = false;
                    if (jawaban.type != 'checkbox') {
                        jawaban.required = true;
                    }
                });
            } else {
                pertanyaan.style.display = 'none';
                jawabans.forEach(function(jawaban, index) {
                    jawaban.disabled = true;
                    if (jawaban.type != 'checkbox') {
                        jawaban.required = false;
                    }
                });
            }
        });
    }

    function lainnyaLogic() {
        var element = event.target;
        var value = element.value;
        if (value.includes("Lainnya.") && (element.type != 'checkbox' || element.checked === true)) {
            var newElement = document.createElement("input");
            newElement.type = "text";
            newElement.name = element.name;
            newElement.required = true;
            newElement.placeholder = "Ketik jawaban disini...";
            newElement.classList.add("form-control");
            if (element.type == 'checkbox') {
                var label = document.querySelector(`label[for="${element.id}"]`);
                label.after(newElement);
            } else {
                element.after(newElement);
            }
        } else {
            if (element.type != 'checkbox' || (element.type == 'checkbox' && value.includes("Lainnya."))) {
                var existedElements = document.querySelectorAll(`input[type="text"][name="${element.name}"]`);
                existedElements.forEach(function(existedElement, index) {
                    existedElement.remove();
                });
            }
        }
    }

    function findPosition(obj) {
        var currenttop = 0;
        if (obj.offsetParent) {
            do {
                currenttop += obj.offsetTop;
            } while ((obj = obj.offsetParent));
            return [currenttop] - 300;
        }
    }
</script>
