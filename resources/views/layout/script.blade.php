<script src="{{ url('assets/js/codebase.core.min.js') }}"></script>
<script src="{{ url('assets/js/codebase.app.min.js') }}"></script>
<script src="{{ url('assets/js/vue-devel.js') }}"></script>
<script src="{{ url('assets/js/plugins/vue-swal/vue-swal.js') }}"></script>
<script src="{{ url('assets/js/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ url('assets/js/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ url('assets/js/plugins/bootstrap-wizard/jquery.bootstrap.wizard.js') }}"></script>
<script src="{{ url('assets/js/plugins/jquery-validation/additional-methods.js') }}"></script>
<!-- <script src="{{ url('assets/js/pages/be_forms_wizard.min.js') }}"></script> -->

<script src="{{ url('assets/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ url('assets/js/plugins/select2/js/select2.min.js') }}"></script>

<script src="{{ url('assets/js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
{{-- <script src="{{ url('assets/js/plugins/datatables/buttons/dataTables.buttons.js') }}"></script> --}}
<script src="{{ url('assets/js/plugins/datatables/buttons-bs4/buttons.bootstrap4.min.js') }}"></script>
{{-- <script src="{{ url('assets/js/plugins/datatables/buttons/buttons.html5.js') }}"></script> --}}
{{-- <script src="{{ url('assets/js/pages/be_tables_datatables.min.js') }}"></script> --}}

<script src="{{ url('assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ url('chosen/chosen.jquery.js') }}"></script>
{{-- <script src="{{ url('chosen/chosenmobile.js') }}"></script> --}}


<script>
    // // Chosen touch support.
    // if ($('.chosen-container').length > 0) {
    //   $('.chosen-container').on('touchstart', function(e){
    //     e.stopPropagation(); e.preventDefault();
    //     // Trigger the mousedown event.
    //     $(this).trigger('mousedown');
    //   });
    // }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let v = new Vue({
        el: "#vue-side-overlay",
        data: {
            password: "",
            repassword: "",
            oldpassword: "",
            alert_success: false,
            alert_failed: false,
            message: ""
        },
        created: function() {

            jQuery(function() {
                Codebase.layout('content_layout_full_width');
            });
        },
        methods: {
            updatePassword: function() {
                $('.btn-update-password').children().addClass('fa-spin')
                $.ajax({
                    url: "{{ route('common-profil-update-akun') }}",
                    type: 'POST',
                    data: {
                        oldpassword: v.oldpassword,
                        password: v.password,
                        repassword: v.repassword
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        if (data.status == 'success') {
                            v.alert_success = true
                            v.alert_failed = false
                        } else {
                            v.alert_failed = true
                            v.alert_success = false
                        }
                        v.message = data.message
                        $('.btn-update-password').children().removeClass('fa-spin')
                    }
                });
            }
        },
        watch: {},
        computed: {
            isInvalid: function() {
                if (this.password.length < 6 && this.repassword.length > 0) {
                    this.alert_failed = true
                    this.message = "Password tidak sama!"
                    return true
                }

                if (this.password.length >= 6 && this.repassword.length >= 6) {
                    if (this.password != this.repassword) {
                        this.alert_failed = true
                        this.message = "Password tidak sama!"
                        return true
                    }
                }

                this.alert_failed = false
                this.message = ""
                return false
            }
        }
    })
</script>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-SR981CR0E3"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'G-SR981CR0E3');
</script>
<script type="text/javascript">
    function maintenis() {
        swal('Maaf.', 'Fitur sedang dalam perbaikan', 'warning');
    }

    function onprogres_bos() {
        swal({
            html: '<h1>Waduuuh</h1><img style="width:50%" src="https://i.pinimg.com/originals/f3/aa/c6/f3aac641686d15ed98d0038f3219fb04.gif"><h5>Maaf, masih dipersiapkan</h5>',
        })
    }
</script>
