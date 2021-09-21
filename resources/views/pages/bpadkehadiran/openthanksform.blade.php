<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Form Kehadiran</title>
        <link
            rel="shortcut icon"
            type="image/x-icon"
            href="{{ '/portal/public/img/photo/bpad-logo-00.png' }}"
        />

        <link
            rel="stylesheet"
            href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
        />

        
    </head>

    <body style="background-color: #f7f7f7">
        <div class="container">
            <div
                class="row"
                style="
                    align-items: center;
                    display: flex;
                    justify-content: center;
                "
            >
                <div class="navbar-brand">
                    <a href="{{ url('/') }}"
                        ><img
                            src="/portal/public/img/photo/bpad-logo-04b.png32"
                            alt="logo"
                            height="100"
                    /></a>
                </div>
            </div>
            <div class="row" id="">
                <div class="col-lg-2"></div>
                <div class="col-lg-8">
                    <div class="row">
                        <div
                            class="card"
                            style="
                                width: 100%;
                                margin-top: 20px;
                                margin-bottom: 10px;
                            "
                        >
                            <div class="card-header"></div>
                            <div class="card-body" style="padding: 0px;">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                        <h4 class="card-title" style="font-weight: bold;">Terima kasih telah mengisi <span><h2>{{ $form['judul'] }}</h2></span></h4>
                                    </li> 
                                    <li class="list-group-item"><a href="/portal/form/{{ $form['no_form'] }}/{{ $form['judul'] }}">Isi kembali form sebelumnya</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
</html>
