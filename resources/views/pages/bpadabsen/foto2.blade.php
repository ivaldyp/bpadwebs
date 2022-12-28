<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>e-SIAPPE</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ ('/portal/img/photo/bpad-logo-00.png') }}" />

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
                <a  href="{{ url('/') }}"><img src="/portal/img/photo/bpad-logo-04b.png32" alt="logo" height="100"></a>
            </div>
        </div>
        <div class="row" style="padding-top:50px">
            <div class="col-md-12 text-center">
                <!-- <h1 class="title"><span style="background: linear-gradient(to right, #8C0606 0%, #FF0000 50%, #8C0606 100%); -webkit-background-clip: text;-webkit-text-fill-color: transparent; font-size: 64px">PROFIL BPAD</span></h1> -->
                <h1 class="title" style="font-family: 'Century Gothic'; font-size: 64px;"><span style="color: #006cb8; font-weight: bold">ABSENSI e-SIAPPE</h1>
            </div>
        </div>
        <div class="row" style="margin-top: 60px;">
            <div class="col-sm-12" style="align-items: center; justify-content: center; display: flex;">
                <video autoplay="true" id="video-webcam" width="auto">
                    Browsermu tidak mendukung, silahkan update!
                </video>
            </div>
        </div>
        <div class="row" style="align-items: center; justify-content: center; display: flex; padding: 30px;">
            <button onclick="takeSnapshot()" class="btn btn-lg btn-info" style="font-size: 30px;">Ambil Gambar</button>
        </div>
        <div class="row" id="capture-result">
            <div class="col-md-12">
                <div class="row" style="align-items: center; justify-content: center; display: flex;">
                    <img src="" id="captured">
                </div>
                <div class="row">
                    <div class="card" style="width: 100%; margin-top: 50px; margin-bottom: 50px;">
                        <div class="card-header">
                            <h3 id="nowjenis" class="text-uppercase text-center"></h3>
                        </div>
                        <div class="card-body">
                            <!-- {{ date_default_timezone_set('Asia/Jakarta') }} -->
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><h3 class="card-title">ID: <span class="text-muted">{{ $data['id_emp'] }}</span></h3></li>
                                <li class="list-group-item"><h3 class="card-title">Nama: <span class="text-muted">{{ $data['nm_emp'] }}</span></h3></li>
                                <li class="list-group-item">
                                    @if(isset($data['nip_emp']) || isset($data['nrk_emp']))
                                    <h3 class="card-title">NIP / NRK: <span class="text-muted">{{ $data['nip_emp'] }} / {{ $data['nrk_emp'] }}</span></h3>
                                    @endif
                                </li>
                                <li class="list-group-item"><h3 class="card-title">Unit: <span class="text-muted">{{ $data['nm_unit'] }}</span></h3></li>
                                <li class="list-group-item"><h3 class="card-title">Tanggal: <span class="text-muted" id="nowdate"></span></h3></li>
                                <li class="list-group-item"><h3 class="card-title">Waktu: <span class="text-muted" id="nowtime"></span></h3></li>
                            </ul>
                           
                            <form id="fotosimpan" class="form-horizontal" method="POST" action="/portal/esiappe/simpan" data-toggle="validator" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="absenid" id="absenid" value="{{ old('absenid', $data['id_emp']) }}">
                                <input type="hidden" name="absenjenis" id="absenjenis">
                                <input type="hidden" name="absentgl" id="absentgl">
                                <input type="hidden" name="absenwaktu" id="absenwaktu">
                                <input type="hidden" name="absenimg" id="absenimg">
                                <input type="hidden" name="absenjam" id="absenjam">
                                
                            </form>
                            <button type="button" id="btnsimpan" href="#!" class="btn btn-primary col-md-12" style="display: block; font-size: 30px;"> SIMPAN</button>
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
            $( "#btnsimpan" ).click(function() {
                var varid = $("#absenid").val();
                var vartgl = $("#absentgl").val();
                var varjenis = $("#absenjenis").val();

                $.ajax({ 
				type: "GET", 
				url: "/portal/esiappe/cekabsen",
				data: { id : varid, tgl : vartgl, jenis : varjenis },
				dataType: "JSON",
				}).done(function( data ) { 
                    if(data == 1) {
                        alert("Tidak boleh menyimpan absen lebih dari sekali");
                    } else if (data == 0) {
                        var jam = $("#absenjam").val();
                
                        if(jam >= 0 && jam < 5) {
                            alert("Tidak dapat menyimpan foto (Absen pagi pukul 05 - 07)");
                        } else if (jam >= 5 && jam < 20) {
                            $( "#fotosimpan" ).submit();
                        } else if (jam >= 20 && jam < 24) {
                            alert("Tidak dapat menyimpan foto (Absen sore pukul 16 - 20)");
                        }
                    }
				}); 

                
                
            });
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
            var newdate = new Date();
            var jam = newdate.getHours();
            if (jam >= 0 && jam < 12) {
                $("#nowjenis").text("Absen Pagi");
            } else if (jam >= 12 && jam < 24) {
                $("#nowjenis").text("Absen Sore");
            }
            
            $("#nowdate").text(("0" + newdate.getDate()).slice(-2) + "-" + ("0" + (newdate.getMonth()+1)).slice(-2) + "-" + ("0" + newdate.getFullYear()).slice(-2));
            $("#nowtime").text(("0" + newdate.getHours()).slice(-2) + ":" + ("0" + newdate.getMinutes()).slice(-2) + ":" + ("0" + newdate.getSeconds()).slice(-2)); 
            
            document.getElementById("absentgl").setAttribute("value", newdate.getFullYear() + "-" + ("0" + (newdate.getMonth()+1)).slice(-2) + "-" + ("0" + newdate.getDate()).slice(-2));
            document.getElementById("absenwaktu").setAttribute("value", ("0" + newdate.getHours()).slice(-2) + ":" + ("0" + newdate.getMinutes()).slice(-2) + ":" + ("0" + newdate.getSeconds()).slice(-2));
            
            if(jam >= 0 && jam <= 11) {
                document.getElementById("absenjenis").setAttribute("value", "pagi");
            } else if (jam >= 12 && jam <= 24) {
                document.getElementById("absenjenis").setAttribute("value", "sore");
            }
            document.getElementById("absenjam").setAttribute("value", jam);
            
            document.getElementById("captured").src = canvas.toDataURL('image/png');
            document.getElementById("absenimg").setAttribute("value", canvas.toDataURL('image/png'));
            $("#capture-result").show();
            // document.getElementById("captured").style.display = "block";
        }
    </script>

    
</body>
</html>