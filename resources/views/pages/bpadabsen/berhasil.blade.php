<!DOCTYPE html>
<html>
    <head>
        <title>e-SIAPPE</title>
        <link rel="shortcut icon" type="image/x-icon" href="{{ ('/portal/public/img/photo/bpad-logo-00.png') }}" />
    </head>
    <body>
        <h1>Foto berhasil tersimpan</h1><br><br>
        <h1>Akan kembali ke halaman awal dalam <span id="seconds">6</span> detik......<br><br>
        Atau tekan <a href="/portal/esiappe/masuk">link ini untuk kembali ke halaman awal.</a></h1>
        
        <script>
            // Countdown timer for redirecting to another URL after several seconds
            var seconds = 6; // seconds for HTML
            var foo; // variable for clearInterval() function

            function redirect() {
                document.location.href = '/portal/esiappe/masuk ';
            }

            function updateSecs() {
                document.getElementById("seconds").innerHTML = seconds;
                seconds--;
                if (seconds == -1) {
                    clearInterval(foo);
                    redirect();
                }
            }

            function countdownTimer() {
                foo = setInterval(function () {
                    updateSecs()
                }, 1000);
            }

            countdownTimer();
        </script>
    </body>
</html>
