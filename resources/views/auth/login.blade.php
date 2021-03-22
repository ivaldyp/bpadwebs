@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div>
                            <img src="public/img/photo/bpad-logo-04b.png32" style="height: auto; max-height: 100%; max-width: 100%">
                        </div>
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right"> Username </label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="off" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="off">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- <div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div> -->

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>

                                <!-- @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif -->
                            </div>
                        </div>

                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php  
    use App\Internal_info;
    use Illuminate\Support\Facades\DB;
    date_default_timezone_set('Asia/Jakarta');
    $datenow = date('Y-m-d');

    $query = DB::select( DB::raw("  
                SELECT TOP 3 * 
                FROM bpaddtfake.dbo.internal_info
                WHERE '$datenow' <= tgl_akhir AND '$datenow' >= tgl_mulai AND sts = 1 AND info_tampil = 1
                ORDER BY tgl desc") );
    $query = json_decode(json_encode($query), true);
?>

@if(count($query) > 0)
<div style="margin-top: 70px; background-color: #ffffff; font: bold  large calibri , serif; font-size: 19px; font-weight: bold; width: 100%" class="col-md-12">
    <marquee scrollamount="12" style="width: 100%">
    @foreach($query as $key => $data)
        @if($key % 2 == 0)
            @php
                $bgcolor = '#1b4f72';
                $bgcolor = '#006cb9';
            @endphp
        @else
            @php
                $bgcolor = '#5dade2';
            @endphp
        @endif

    
        <p align="center" style="color:#ffffff;background-color:{{ $bgcolor }}; margin-bottom: 0px; height: 30px; vertical-align: middle; padding: 3px; text-shadow: 1px 0 0 #000, 0 -1px 0 #000, 0 1px 0 #000, -1px 0 0 #000;">
            @if( !(is_null($data['info_file'])) && $data['info_file'] != '' )
                <a style="color: #ffffff; text-shadow: 1px 0 0 #000, 0 -1px 0 #000, 0 1px 0 #000, -1px 0 0 #000;" target="_blank" href="{{ config('app.openfileinfo') }}/{{ $data['ids'] }}/{{ $data['info_file'] }}">{{ $data['info_judul'] }}</a>
            @else
                {{ $data['info_judul'] }}
            @endif
        </p>
    @endforeach
    </marquee>
</div>
@endif



@endsection
