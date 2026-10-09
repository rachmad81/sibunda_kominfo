        <!-- Icons -->
        <link rel="shortcut icon" href="{{ asset('logo/logo-sibunda.png') }}">
        <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('logo/logo-sibunda.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('logo/logo-sibunda.png') }}">

        <!-- Main Style -->
        <!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Muli:300,400,400i,600,700"> -->
        
        
        <!-- Plugins -->
        <link href="{{ url('assets/js/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
        <!-- Datepicker -->
        <link rel="stylesheet" href="{{ url('assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
        
        <!-- Datatables -->
        <link rel="stylesheet" href="{{ url('assets/js/plugins/datatables/dataTables.bootstrap4.css') }}">
        <link rel="stylesheet" href="{{  url('assets/js/plugins/datatables/buttons-bs4/buttons.bootstrap4.min.css') }}">
        
        <!-- Vue Select -->
        <link href="{{ url('assets/js/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
        <link href="{{ url('assets/css/main.css') }}" rel="stylesheet"> 
        <link href="{{ url('assets/css/util.css') }}" rel="stylesheet">
        <link rel="stylesheet" id="css-main" href="{{ url('assets/css/codebase.css') }}">
        <link rel="stylesheet" id="css-main" href="{{ url('chosen/chosen.css') }}">
        <!-- Additional Styles That May Applied to All Pages -->
        <style>
                @media (min-width: 992px){
                  .nav-main-header li {
                    margin : 0px
                  }
                }
                @font-face {
                  font-family: arial-narrow;
                  /*src: url('assets/fonts/arial-narrow/Arial-Narrow.woff');*/
                  src: url('{{ url("assets/fonts/arial-narrow/ARIALN.woff") }}');
                }

                body, main, html, .btn {
                  font-family: arial-narrow !important;
                }

                .content-side-user{
                  min-height: 170px;
                }

                #page-container.page-header-glass.page-header-fixed.page-header-scroll.page-header-inverse #page-header{
                  background-color: #007bff !important;
                  padding-bottom: 5px;
                }

                .list-panel{
                    padding-inline-start:0px; 
                    padding-top: 10px; 
                    list-style-type: none; 
                    display: flex;
                }

                .nav-item .nav-link:hover, .nav-item .small:hover{
                    color: #f27474;
                }

                .nav-item .nav-link, .small{
                    color: #f0f0f0;
                }
                .btn-sm{
                        margin-top:2px;
                }

                .badge{
                  margin: 0px;
                }

                .no-padding{
                        padding:0;
                }

                .no-margin{
                        margin:0;
                }

                .m-t-xs{
                        margin-top: 1%;
                }

                .m-b-xs{
                        margin-bottom: 1%;
                }
                
                .m-l-xs{
                        margin-left: 1%;
                }

                .no-left-padding{
                        padding-left: 0;
                }

                .no-right-padding{
                        padding-right: 0;
                }

                .no-right-margin{
                        margin-right: 0;
                }

                .btn-outline-secondary{
                        color:#b9b9b9;
                        border-color:#b9b9b9;
                }

                .btn-outline-secondary:hover{
                        /* background-color:#d4dae3; */
                }

                .btn-outline-secondary:focus{
                        /* background-color:#d4dae3; */
                }
                .switch {
                  position: relative;
                  display: inline-block;
                  width: 50px;
                  height: 25px;
                }

                .switch input { 
                  opacity: 0;
                  width: 0;
                  height: 0;
                }

                .slider {
                  position: absolute;
                  cursor: pointer;
                  top: 0;
                  left: 0;
                  right: 0;
                  bottom: 0;
                  background-color: #ccc;
                  -webkit-transition: .4s;
                  transition: .4s;
                }

                .slider:before {
                  position: absolute;
                  content: "";
                  height: 17px;
                  width: 17px;
                  left: 4px;
                  bottom: 4px;
                  background-color: white;
                  -webkit-transition: .4s;
                  transition: .4s;
                }

                input:checked + .slider {
                  background-color: #2196F3;
                }

                input:focus + .slider {
                  box-shadow: 0 0 1px #2196F3;
                }

                input:checked + .slider:before {
                  -webkit-transform: translateX(26px);
                  -ms-transform: translateX(26px);
                  transform: translateX(26px);
                }

                /* Rounded sliders */
                .slider.round {
                  border-radius: 34px;
                }

                .slider.round:before {
                  border-radius: 50%;
                }

               
                .required:after{
                    content:"*";
                    color:red;
                }

                .card {
                  position: relative;
                  display: -webkit-box;
                  display: -webkit-flex;
                  display: -ms-flexbox;
                  display: flex;
                  -webkit-box-orient: vertical;
                  -webkit-box-direction: normal;
                  -webkit-flex-direction: column;
                  -ms-flex-direction: column;
                  flex-direction: column;
                  background-color: #fff;
                  border: 1px solid rgba(0, 0, 0, 0.125);
                  border-radius: 0.25rem;
              }

              .card-block {
                  -webkit-box-flex: 1;
                  -webkit-flex: 1 1 auto;
                  -ms-flex: 1 1 auto;
                  flex: 1 1 auto;
                  padding: 1.25rem;
              }

              .card-title {
                  margin-bottom: 0.75rem;
              }

              .card-subtitle {
                  margin-top: -0.375rem;
                  margin-bottom: 0;
              }

              .card-text:last-child {
                  margin-bottom: 0;
              }

              .card-link:hover {
                  text-decoration: none;
              }

              .card-link + .card-link {
                  margin-left: 1.25rem;
              }

              .card > .list-group:first-child .list-group-item:first-child {
                  border-top-right-radius: 0.25rem;
                  border-top-left-radius: 0.25rem;
              }

              .card > .list-group:last-child .list-group-item:last-child {
                  border-bottom-right-radius: 0.25rem;
                  border-bottom-left-radius: 0.25rem;
              }

              .card-header {
                  padding: 0.75rem 1.25rem;
                  margin-bottom: 0;
                  background-color: #f7f7f9;
                  border-bottom: 1px solid rgba(0, 0, 0, 0.125);
              }

              .card-header:first-child {
                  border-radius: calc(0.25rem - 1px) calc(0.25rem - 1px) 0 0;
              }

              .card-footer {
                  padding: 0.75rem 1.25rem;
                  background-color: #f7f7f9;
                  border-top: 1px solid rgba(0, 0, 0, 0.125);
              }

              .card-footer:last-child {
                  border-radius: 0 0 calc(0.25rem - 1px) calc(0.25rem - 1px);
              }

              .card-header-tabs {
                  margin-right: -0.625rem;
                  margin-bottom: -0.75rem;
                  margin-left: -0.625rem;
                  border-bottom: 0;
              }

              .card-header-pills {
                  margin-right: -0.625rem;
                  margin-left: -0.625rem;
              }

              .card-primary {
                  background-color: #0275d8;
                  border-color: #0275d8;
              }

              .card-primary .card-header,
              .card-primary .card-footer {
                  background-color: transparent;
              }

              .card-success {
                  background-color: #5cb85c;
                  border-color: #5cb85c;
              }

              .card-success .card-header,
              .card-success .card-footer {
                  background-color: transparent;
              }

              .card-info {
                  background-color: #5bc0de;
                  border-color: #5bc0de;
              }

              .card-info .card-header,
              .card-info .card-footer {
                  background-color: transparent;
              }

              .card-warning {
                  background-color: #f0ad4e;
                  border-color: #f0ad4e;
              }

              .card-warning .card-header,
              .card-warning .card-footer {
                  background-color: transparent;
              }

              .card-danger {
                  background-color: #d9534f;
                  border-color: #d9534f;
              }

              .card-danger .card-header,
              .card-danger .card-footer {
                  background-color: transparent;
              }

              .card-outline-primary {
                  background-color: transparent;
                  border-color: #0275d8;
              }

              .card-outline-secondary {
                  background-color: transparent;
                  border-color: #ccc;
              }

              .card-outline-info {
                  background-color: transparent;
                  border-color: #5bc0de;
              }

              .card-outline-success {
                  background-color: transparent;
                  border-color: #5cb85c;
              }

              .card-outline-warning {
                  background-color: transparent;
                  border-color: #f0ad4e;
              }

              .card-outline-danger {
                  background-color: transparent;
                  border-color: #d9534f;
              }

              .card-inverse {
                  color: rgba(255, 255, 255, 0.65);
              }

              .card-inverse .card-header,
              .card-inverse .card-footer {
                  background-color: transparent;
                  border-color: rgba(255, 255, 255, 0.2);
              }

              .card-inverse .card-header,
              .card-inverse .card-footer,
              .card-inverse .card-title,
              .card-inverse .card-blockquote {
                  color: #fff;
              }

              .card-inverse .card-link,
              .card-inverse .card-text,
              .card-inverse .card-subtitle,
              .card-inverse .card-blockquote .blockquote-footer {
                  color: rgba(255, 255, 255, 0.65);
              }

              .card-inverse .card-link:focus, .card-inverse .card-link:hover {
                  color: #fff;
              }

              .card-blockquote {
                  padding: 0;
                  margin-bottom: 0;
                  border-left: 0;
              }

              .card-img {
                  border-radius: calc(0.25rem - 1px);
              }

              .card-img-overlay {
                  position: absolute;
                  top: 0;
                  right: 0;
                  bottom: 0;
                  left: 0;
                  padding: 1.25rem;
              }

              .card-img-top {
                  border-top-right-radius: calc(0.25rem - 1px);
                  border-top-left-radius: calc(0.25rem - 1px);
              }

              .card-img-bottom {
                  border-bottom-right-radius: calc(0.25rem - 1px);
                  border-bottom-left-radius: calc(0.25rem - 1px);
              }

              @media (min-width: 576px) {
                  .card-deck {
                      display: -webkit-box;
                      display: -webkit-flex;
                      display: -ms-flexbox;
                      display: flex;
                      -webkit-flex-flow: row wrap;
                      -ms-flex-flow: row wrap;
                      flex-flow: row wrap;
                  }
                  .card-deck .card {
                      display: -webkit-box;
                      display: -webkit-flex;
                      display: -ms-flexbox;
                      display: flex;
                      -webkit-box-flex: 1;
                      -webkit-flex: 1 0 0%;
                      -ms-flex: 1 0 0%;
                      flex: 1 0 0%;
                      -webkit-box-orient: vertical;
                      -webkit-box-direction: normal;
                      -webkit-flex-direction: column;
                      -ms-flex-direction: column;
                      flex-direction: column;
                  }
                  .card-deck .card:not(:first-child) {
                      margin-left: 15px;
                  }
                  .card-deck .card:not(:last-child) {
                      margin-right: 15px;
                  }
              }

              @media (min-width: 576px) {
                  .card-group {
                      display: -webkit-box;
                      display: -webkit-flex;
                      display: -ms-flexbox;
                      display: flex;
                      -webkit-flex-flow: row wrap;
                      -ms-flex-flow: row wrap;
                      flex-flow: row wrap;
                  }
                  .card-group .card {
                      -webkit-box-flex: 1;
                      -webkit-flex: 1 0 0%;
                      -ms-flex: 1 0 0%;
                      flex: 1 0 0%;
                  }
                  .card-group .card + .card {
                      margin-left: 0;
                      border-left: 0;
                  }
                  .card-group .card:first-child {
                      border-bottom-right-radius: 0;
                      border-top-right-radius: 0;
                  }
                  .card-group .card:first-child .card-img-top {
                      border-top-right-radius: 0;
                  }
                  .card-group .card:first-child .card-img-bottom {
                      border-bottom-right-radius: 0;
                  }
                  .card-group .card:last-child {
                      border-bottom-left-radius: 0;
                      border-top-left-radius: 0;
                  }
                  .card-group .card:last-child .card-img-top {
                      border-top-left-radius: 0;
                  }
                  .card-group .card:last-child .card-img-bottom {
                      border-bottom-left-radius: 0;
                  }
                  .card-group .card:not(:first-child):not(:last-child) {
                      border-radius: 0;
                  }
                  .card-group .card:not(:first-child):not(:last-child) .card-img-top,
                  .card-group .card:not(:first-child):not(:last-child) .card-img-bottom {
                      border-radius: 0;
                  }
              }

              @media (min-width: 576px) {
                  .card-columns {
                      -webkit-column-count: 3;
                      -moz-column-count: 3;
                      column-count: 3;
                      -webkit-column-gap: 1.25rem;
                      -moz-column-gap: 1.25rem;
                      column-gap: 1.25rem;
                  }
                  .card-columns .card {
                      display: inline-block;
                      width: 100%;
                      margin-bottom: 0.75rem;
                  }
              }

              </style>

        
        {{-- Datatable Button Styling  --}}
        <style> 
                @media only screen and (min-width: 600px) {
                div.dt-buttons {
                        float: right;
                        margin-left:10px;
                        margin-bottom:10px
                }
                }
                @media only screen and (max-width: 600px) {
                div.dt-buttons {
                        margin-left:10px;
                        margin-bottom:10px;
                        justify-content: center;
                }
                }
        </style>

        <style>

                .kode-form-overflow{
                        max-width: 120px;
                        white-space: nowrap;
                        text-overflow: ellipsis;
                        word-break: break-all;
                        overflow: hidden;
                        direction: rtl;
                }
                
        </style>