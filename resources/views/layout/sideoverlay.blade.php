<!-- Side Overlay-->
<aside id="side-overlay">
    <!-- Side Header -->
    <div class="content-header content-header-fullrow">
        <div class="content-header-section align-parent">
            <!-- Close Side Overlay -->
            <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
            <button type="button" class="btn btn-circle btn-dual-secondary align-v-r" data-toggle="layout" data-action="side_overlay_close">
                <i class="fa fa-times text-danger"></i>
            </button>
            <!-- END Close Side Overlay -->
        </div>
    </div>
    <!-- END Side Header -->

    <!-- Side Content -->
    <div class="content-side">
        <!-- Profile -->
        <div class="block pull-r-l">
            <div class="block-header bg-body-light">
                <h3 class="block-title">
                    <i class="fa fa-fw fa-pencil font-size-default mr-5"></i>Profil
                </h3>
                <div class="block-options">
                    <button type="button" class="btn-block-option" data-toggle="block-option" data-action="content_toggle"></button>
                </div>
            </div>
            <div class="block-content" id="vue-side-overlay">
                <div class="alert alert-danger" v-if="alert_failed">@{{ message }}</div>
                <div class="alert alert-success" v-if="alert_success">@{{ message }}</div>
                <form action="" method="post" >
                    @csrf
                    <div class="form-group mb-15">
                        <label for="side-overlay-profile-name">Nama Pengguna</label>
                        <div class="input-group">
                            <input type="text" minlength="3" required class="form-control" id="side-overlay-profile-name" name="username" value="{{ \Auth::User()->username }}" disabled autocomplete="username">
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="fa fa-user"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-15">
                        <label for="side-overlay-profile-password-confirm"> Password Lama
                            <span class="text-pulse"> *</span>
                        </label>
                        <div class="input-group">
                            <input type="password" v-model="oldpassword" required minlength="6" class="form-control" id="side-overlay-profile-old-password" name="old_password" placeholder="Password Lama.." autocomplete="old-password">
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="fa fa-asterisk"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-15" v-bind:class="{'is-invalid':isInvalid}">
                        <label for="side-overlay-profile-password"> Password Baru
                            <span class="text-pulse"> *</span>
                        </label>
                        <div class="input-group">
                            <input type="password" v-model="password" required minlength="6" class="form-control" id="side-overlay-profile-password" name="password" placeholder="Password Baru.." autocomplete="new-password">
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="fa fa-asterisk"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-15" v-bind:class="{'is-invalid':isInvalid}">
                        <label for="side-overlay-profile-password-confirm"> Ketik Ulang Password
                            <span class="text-pulse"> *</span>
                        </label>
                        <div class="input-group">
                            <input type="password" v-model="repassword" required minlength="6" class="form-control" id="side-overlay-profile-password-confirm" name="re-password" placeholder="Ketik Ulang Password.." autocomplete="new-password">
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="fa fa-asterisk"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <button type="button" class="btn btn-block btn-alt-primary btn-update-password" v-on:click="updatePassword">
                                <i class="fa fa-refresh mr-5"></i> Update
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- END Profile -->

    </div>
    <!-- END Side Content -->
</aside>
<!-- END Side Overlay -->
