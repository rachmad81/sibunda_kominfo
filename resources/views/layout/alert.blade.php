@if ($errors->any())
    <div class="alert alert-danger" style="margin-bottom:10px;">
        <h4 style="margin-bottom: 5px"><strong>Pesan Error</strong></h4>
        <hr style="margin-top: 0px; margin-bottom:0px;">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@if(session()->has('alert-success'))
    <div class="alert alert-success">
        {!! session()->get('alert-success') !!}
    </div>
@elseif(session()->has('alert-fail'))
    <div class="alert alert-danger">
        {!! session()->get('alert-fail') !!}
    </div>
@endif
