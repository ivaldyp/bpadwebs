<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Display Webcam Stream</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <style>
    /* #container {
        margin: 0px auto;
        width: 500px;
        height: 375px;
        border: 10px #333 solid;
    } */
    #video-webcam {
        /* width: 500px; */
        /* height: 375px; */
        background-color: #666;
    }
    </style>
</head>
 
<body>
    <div class="container" id="contain">
        <div class="row">
            <div class="navbar-brand">
                <a  href="{{ url('/') }}"><img src="/portal/public/img/photo/bpad-logo-04b.png32" alt="logo" height="85"></a>
            </div>
        </div>
        <div class="row" style="align-items: center; justify-content: center; display: flex;">
            <video autoplay="true" id="video-webcam" width="100%">
                Browsermu tidak mendukung, silahkan update!
            </video>
        </div>
        <div class="row" style="align-items: center; justify-content: center; display: flex; padding: 30px;">
            <button onclick="takeSnapshot()" class="btn btn-lg btn-info">Ambil Gambar</button>
        </div>
        <div class="row" id="capture-result">
            <div class="col-md-12">
                <div class="row">
                    <img src="" id="captured">
                </div>
                <div class="row">
                    <div class="card" style="width: 100%; margin-top: 50px; margin-bottom: 50px;">
                        <div class="card-header">
                            <h3>Data</h3>
                        </div>
                        <div class="card-body">
                            <!-- {{ date_default_timezone_set('Asia/Jakarta') }} -->
                            <h3 class="card-title">ID: <span class="text-muted">{{ $data['id_emp'] }}</span></h3>
                            <h3 class="card-title">Nama: <span class="text-muted">{{ $data['nm_emp'] }}</span></h3>
                            @if(isset($data['nip_emp']) || isset($data['nrk_emp']))
                            <h3 class="card-title">NIP / NRK: <span class="text-muted">{{ $data['nip_emp'] }} / {{ $data['nrk_emp'] }}</span></h3>
                            @endif
                            <h3 class="card-title">Unit: <span class="text-muted">{{ $data['nm_unit'] }}</span></h3>
                            <h3 class="card-title">Tanggal: <span class="text-muted" id="nowdate"></span></h3>
                            <h3 class="card-title">Waktu: <span class="text-muted" id="nowtime"></span></h3>
                            <a href="#!" class="btn btn-primary" style="display: block; font-size: 30px;"> SIMPAN</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $( document ).ready(function() {
            $("#capture-result").hide();
        });
        // seleksi elemen video
        var video = document.querySelector("#video-webcam");

        // minta izin user
        navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia || navigator.oGetUserMedia;

        // jika user memberikan izin
        if (navigator.getUserMedia) {
            // jalankan fungsi handleVideo, dan videoError jika izin ditolak
            navigator.getUserMedia({ video: true }, handleVideo, videoError);
        }

        // fungsi ini akan dieksekusi jika  izin telah diberikan
        function handleVideo(stream) {
            video.srcObject = stream;
        }

        // fungsi ini akan dieksekusi kalau user menolak izin
        function videoError(e) {
            // do something
            alert("Izinkan menggunakan webcam")
        }

        function takeSnapshot() {
            // buat elemen img
            var img = document.createElement('img');
            var context;

            // ambil ukuran video
            var width = video.offsetWidth
                    , height = video.offsetHeight;

            // buat elemen canvas
            canvas = document.createElement('canvas');
            canvas.width = width;
            canvas.height = height;

            // ambil gambar dari video dan masukan 
            // ke dalam canvas
            context = canvas.getContext('2d');
            context.drawImage(video, 0, 0, width, height);

            // render hasil dari canvas ke elemen img
            document.getElementById("captured").src = canvas.toDataURL('image/png');
            var newdate = new Date();
            $("#nowdate").text(("0" + newdate.getDate()).slice(-2) + "-" + ("0" + (newdate.getMonth()+1)).slice(-2) + "-" + ("0" + newdate.getFullYear()).slice(-2));
            $("#nowtime").text(("0" + newdate.getHours()).slice(-2) + ":" + ("0" + newdate.getMinutes()).slice(-2) + ":" + ("0" + newdate.getSeconds()).slice(-2)); 
            $("#capture-result").show();
            // document.getElementById("captured").style.display = "block";
        }
    </script>

    
</body>
</html>