<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('index');
// });

// Route::get('/home', function () {
//     return view('index');
// });
Route::get('/mobilein', 'Auth\LoginController@attemptMobile');
Route::get('/loginaset', 'ApiController@loginaset');
Route::get('/loginemp', 'ApiController@loginemp');

Route::get('/', 'LandingController@index');
Route::get('/home', 'HomeController@index');
Route::POST('/home/password', 'HomeController@password');
Route::get('/home/ulangtahun', 'HomeController@ulangtahun')->name('home.ulangtahun');
Route::get('/home/pensiun', 'HomeController@pensiun')->name('home.pensiun');

Route::get('/ceksurat', 'LandingController@ceksurat');
Route::post('/mail', 'LandingController@feedback');
Route::get('/logout', 'LandingController@logout');
Route::get('/kkrekon', 'LandingController@kkrekon');
Route::get('/kkrekon/{pusat}', 'LandingController@kertaskerja');
Route::get('/kkrekon/{utara}', 'LandingController@kertaskerja');
Route::get('/kkrekon/{barat}', 'LandingController@kertaskerja');
Route::get('/kkrekon/{timur}', 'LandingController@kertaskerja');
Route::get('/kkrekon/{selatan}', 'LandingController@kertaskerja');
Route::get('/kkrekon/{seribu}', 'LandingController@kertaskerja');
Route::get('/kkrekon/{provinsi}', 'LandingController@kertaskerja');

Route::get('/kebijakan-dan-privasi', function () {
    return view('pages.pnp');
});
Route::get('/syarat-dan-ketentuan', function () {
    return view('pages.tnc');
});
Route::get('/brandgang-permohonan', function () {
    return view('pages.brandgang-permohonan');
});

// --------- BPAD LINK FORM PUBLIK-----------

Route::group(['prefix' => 'form'], function () {
	Route::get('/{id}/thanks', 'FormController@openthanksform');
	Route::get('/{id}/excel', 'InternalController@openexcelform');
	Route::get('/{id}/lihat', 'InternalController@openresponseform');
	Route::get('/{id}/lihattt', 'InternalController@openresponseformlihattt');
	Route::get('/{id}/{judul}', 'FormController@openform');
	Route::post('/simpanform', 'FormController@simpanform');
});

// --------- BPAD FOTO ABSEN-----------

Route::group(['prefix' => 'esiappe'], function () {
	Route::get('/masuk', 'AbsenController@masuk');
	// Route::post('/cekuser', 'AbsenController@cekuser');
	Route::get('/cekabsen', 'AbsenController@cekabsen');
	Route::post('/foto', 'AbsenController@foto');
	Route::post('/simpan', 'AbsenController@simpan');
	Route::get('/berhasil', 'AbsenController@berhasil');
});

// ------------- BPAD API -------------

Route::group(['prefix' => 'ws'], function () {
	Route::get('/disposisi', 'ApiDisposisiController@disposisi');
	Route::get('/kepegawaian', 'ApiController@kepegawaian');
	Route::get('/getuserdata', 'ApiController@getuserdata');
	Route::POST('/receive', 'ApiController@tldisposisi');
});

// ------------- BPAD CMS -------------

Route::group(['prefix' => 'profil'], function () {
	Route::get('/', 'LandingController@profil')->name('profil.profil');
	Route::get('/visimisi', 'LandingController@visimisi')->name('profil.visimisi');
	Route::get('/tupoksi', 'LandingController@tupoksi')->name('profil.tupoksi');
	Route::get('/struktur', 'LandingController@struktur')->name('profil.struktur');
	Route::get('/profilpejabat', 'LandingController@profilpejabat')->name('profil.profilpejabat');
	Route::get('/gethistorypejabat', 'LandingController@gethistorypejabat')->name('profil.gethistorypejabat');
});

Route::group(['prefix' => 'content'], function () {
	Route::get('/berita', 'ContentController@berita_all');
	Route::get('/berita/{id}', 'ContentController@berita_read');
	Route::get('/berita/{id}/{isi}', 'ContentController@berita_read');
	Route::get('/lelang', 'ContentController@lelang');
	Route::get('/foto', 'ContentController@foto_all');
	Route::get('/foto/{id}', 'ContentController@foto_open');
	Route::get('/foto/{id}/{isi}', 'ContentController@foto_open');
	Route::get('/video', 'ContentController@video_all');
	Route::get('/video/{id}', 'ContentController@video_open');
});

// ------------- BPAD PPID -------------

Route::group(['prefix' => 'ppid'], function () {
	Route::get('/profil', 'PpidController@profil')->name('ppid.profil');
	Route::get('/struktur', 'PpidController@struktur')->name('ppid.struktur');
	Route::get('/informasi-publik', 'PpidController@informasipublik')->name('ppid.informasipublik');
	Route::get('/form', 'PpidController@form')->name('ppid.form');
	Route::post('/form', 'PpidController@saveform')->name('ppid.saveform');
	Route::get('/infografis', 'PpidController@infografis')->name('ppid.infografis');
});

// ------------- BPAD DT --------------
Route::group(['prefix' => 'notifikasi'], function () {
	Route::get('/cek/{jenis}/{ids}', 'NotifikasiController@cek');
	Route::get('/', 'NotifikasiController@notifall');
});

Route::group(['prefix' => 'booking'], function () {
    // --setup--
	Route::get('/manageruang', 'BookingController@manageruang');
	Route::post('form/tambahruang', 'BookingController@forminsertruang');
	Route::post('form/ubahruang', 'BookingController@formupdateruang');
	Route::post('form/hapusruang', 'BookingController@formdeleteruang');
	Route::get('/managepagu', 'BookingController@managepagu');
	Route::post('form/ubahpagu', 'BookingController@formupdatepagu');
    // --/setup--

	Route::get('pinjam', 'BookingController@formpinjam');
	Route::post('lihat', 'BookingController@lihatpinjam');
	Route::post('ubah pinjam', 'BookingController@ubahpinjam');
	Route::post('form/tambahpinjam', 'BookingController@forminsertpinjam');
	Route::post('form/ubahpinjam', 'BookingController@formupdatepinjam');
	Route::post('form/hapuspinjam', 'BookingController@formdeletepinjam');
	Route::post('form/approvepinjam', 'BookingController@formapprovepinjam');

	Route::get('kalender', 'BookingController@kalenderpinjam');
	Route::get('request/getallkalender', 'BookingController@requestkalenderall');


	Route::get('list', 'BookingController@listpinjam');
	Route::get('request', 'BookingController@requestpinjam');
});

Route::group(['prefix' => 'notulen'], function () {
	Route::get('tambah notulen', 'NotulenController@tambahnotulen');
	Route::post('ubah notulen', 'NotulenController@ubahnotulen');
	Route::get('notulen', 'NotulenController@notulenall');
	Route::get('mynotulen', 'NotulenController@mynotulen');
	Route::post('form/tambahnotulen', 'NotulenController@forminsertnotulen');
	Route::post('form/ubahnotulen', 'NotulenController@formupdatenotulen');
	Route::post('form/hapusnotulen', 'NotulenController@formdeletenotulen');
});

Route::group(['prefix' => 'disposisi'], function () {
	Route::get('/formdisposisi', 'DisposisiController@formdisposisi');
	Route::get('/hapusfiledisposisi', 'DisposisiController@disposisihapusfile');
	Route::get('/tambah disposisi', 'DisposisiController@disposisitambah');
	Route::get('/ubah disposisi', 'DisposisiController@disposisiubah');
	Route::post('form/tambahdisposisi', 'DisposisiController@forminsertdisposisi');
	Route::post('form/ubahdisposisi', 'DisposisiController@formupdatedisposisi');
	Route::get('form/hapusdisposisi', 'DisposisiController@formdeletedisposisi');
	Route::get('form/resetdisposisi', 'DisposisiController@formresetdisposisi');

	Route::get('/disposisi', 'DisposisiController@disposisi');
	Route::get('/lihat disposisi', 'DisposisiController@disposisilihat');
	Route::post('form/lihatdisposisi', 'DisposisiController@formlihatdisposisi');
	Route::get('form/hapusdisposisiemp', 'DisposisiController@formdeletedisposisiemp');

	Route::get('/excel', 'DisposisiController@printexcel');
    Route::get('/exceleselon3', 'DisposisiController@printexceleselon3');
	Route::get('/log', 'DisposisiController@log');
});

Route::group(['prefix' => 'profil'], function () {
	Route::get('/disposisi', 'ProfilController@disposisi');
	Route::get('/tambah disposisi', 'ProfilController@disposisitambah');
	Route::post('/lihat disposisi', 'ProfilController@disposisilihat');
	Route::post('form/lihatdisposisi', 'ProfilController@formviewdisposisi');
	Route::post('form/tambahdisposisi', 'ProfilController@forminsertdisposisi');
	Route::post('form/hapusdisposisi', 'ProfilController@formdeletedisposisi');
	Route::get('/ceknoform', 'ProfilController@ceknoform');

	Route::get('/pegawai', 'ProfilController@pegawai');
	Route::get('/printdrh', 'ProfilController@printdrh');
	Route::post('/form/ubahidpegawai', 'ProfilController@formupdateidpegawai');

	Route::post('/form/tambahkelpegawai', 'ProfilController@forminsertkelpegawai');
	Route::post('/form/ubahkelpegawai', 'ProfilController@formupdatekelpegawai');
	Route::post('/form/hapuskelpegawai', 'ProfilController@formdeletekelpegawai');

	Route::post('/form/tambahdikpegawai', 'ProfilController@forminsertdikpegawai');
	Route::post('/form/ubahdikpegawai', 'ProfilController@formupdatedikpegawai');
	Route::post('/form/hapusdikpegawai', 'ProfilController@formdeletedikpegawai');

	// Route::post('/form/ubahidpegawai', 'ProfilController@formupdateidpegawai');
	Route::post('/form/tambahnonpegawai', 'ProfilController@forminsertnonpegawai');
	Route::post('/form/ubahnonpegawai', 'ProfilController@formupdatenonpegawai');
	Route::post('/form/hapusnonpegawai', 'ProfilController@formdeletenonpegawai');

	// Route::post('/form/ubahidpegawai', 'ProfilController@formupdateidpegawai');
	Route::post('/form/tambahgolpegawai', 'ProfilController@forminsertgolpegawai');
	Route::post('/form/ubahgolpegawai', 'ProfilController@formupdategolpegawai');
	Route::post('/form/hapusgolpegawai', 'ProfilController@formdeletegolpegawai');

	// Route::post('/form/ubahidpegawai', 'ProfilController@formupdateidpegawai');
	Route::post('/form/tambahjabpegawai', 'ProfilController@forminsertjabpegawai');
	Route::post('/form/ubahjabpegawai', 'ProfilController@formupdatejabpegawai');
	Route::post('/form/hapusjabpegawai', 'ProfilController@formdeletejabpegawai');

	Route::post('/form/tambahskppegawai', 'ProfilController@forminsertskppegawai');

	Route::post('/form/tambahhukpegawai', 'ProfilController@forminserthukpegawai');
	Route::post('/form/ubahhukpegawai', 'ProfilController@formupdatehukpegawai');
	Route::post('/form/hapushukpegawai', 'ProfilController@formdeletehukpegawai');
	
    Route::post('/form/tambahfilespegawai', 'ProfilController@forminsertfilespegawai');
	Route::post('/form/ubahfilespegawai', 'ProfilController@formupdatefilespegawai');
	Route::post('/form/hapusfilespegawai', 'ProfilController@formdeletefilespegawai');
});

Route::group(['prefix' => 'cms'], function () {
	Route::get('/menu', 'CmsController@menuall');
	Route::post('/form/tambahmenu', 'CmsController@forminsertmenu');
	Route::post('/form/ubahmenu', 'CmsController@formupdatemenu');
	Route::post('/form/hapusmenu', 'CmsController@formdeletemenu');
	Route::get('/menuakses', 'CmsController@menuakses');
	Route::post('/form/ubahaccess', 'CmsController@formupdateaccess');

	Route::get('/kategori', 'CmsController@kategoriall');
	Route::post('/form/tambahkategori', 'CmsController@forminsertkategori');
	Route::post('/form/ubahkategori', 'CmsController@formupdatekategori');
	Route::post('/form/hapuskategori', 'CmsController@formdeletekategori');

	Route::get('/subkategori', 'CmsController@subkategoriall');
	Route::post('/form/tambahsubkategori', 'CmsController@forminsertsubkategori');
	Route::post('/form/ubahsubkategori', 'CmsController@formupdatesubkategori');
	Route::post('/form/hapussubkategori', 'CmsController@formdeletesubkategori');

	Route::get('/content', 'CmsController@contentall');
	Route::get('/rekap content', 'CmsController@contentrekap');
	Route::get('/rekap excel', 'CmsController@contentexcel');
	Route::get('/tambah content', 'CmsController@contenttambah');
	Route::post('/ubah content', 'CmsController@contentubah');
	Route::get('/form/apprcontent', 'CmsController@formapprcontent');
	Route::post('/form/tambahcontent', 'CmsController@forminsertcontent');
	Route::post('/form/ubahcontent', 'CmsController@formupdatecontent');
	Route::post('/form/hapuscontent', 'CmsController@formdeletecontent');

	Route::get('/approve', 'CmsController@approve');
	Route::post('/form/approve', 'CmsController@formsaveapprove');

	Route::get('/produk', 'CmsController@produkall');
	Route::post('/form/tambahproduk', 'CmsController@forminsertproduk');
	Route::post('/form/ubahproduk', 'CmsController@formupdateproduk');
	Route::post('/form/hapusproduk', 'CmsController@formdeleteproduk');
});

Route::group(['prefix' => 'internal'], function () {
	Route::get('/agenda', 'InternalController@agenda');
	Route::get('/agenda tambah', 'InternalController@agendatambah');
	Route::post('/agenda ubah', 'InternalController@agendaubah');
	Route::get('/form/appragenda', 'InternalController@formappragenda');
	Route::post('/form/tambahagenda', 'InternalController@forminsertagenda');
	Route::post('/form/ubahagenda', 'InternalController@formupdateagenda');
	Route::post('/form/hapusagenda', 'InternalController@formdeleteagenda');

	Route::get('/berita', 'InternalController@berita');
	Route::get('/berita tambah', 'InternalController@beritatambah');
	Route::post('/berita ubah', 'InternalController@beritaubah');
	Route::get('/form/apprberita', 'InternalController@formapprberita');
	Route::post('/form/tambahberita', 'InternalController@forminsertberita');
	Route::post('/form/ubahberita', 'InternalController@formupdateberita');
	Route::post('/form/hapusberita', 'InternalController@formdeleteberita');

	Route::get('/info', 'InternalController@infoall');
	Route::get('/info tambah', 'InternalController@infotambah');
	Route::post('/info ubah', 'InternalController@infoubah');
	Route::get('/form/apprinfo', 'InternalController@formapprinfo');
	Route::post('/form/tambahinfo', 'InternalController@forminsertinfo');
	Route::post('/form/ubahinfo', 'InternalController@formupdateinfo');
	Route::post('/form/hapusinfo', 'InternalController@formdeleteinfo');

	Route::get('/arsip', 'InternalController@arsipall');
	Route::get('/arsip tambah', 'InternalController@arsiptambah');
	Route::post('/arsip ubah', 'InternalController@arsipubah');
	Route::get('/form/apprarsip', 'InternalController@formapprarsip');
	Route::post('/form/tambaharsip', 'InternalController@forminsertarsip');
	Route::post('/form/ubaharsip', 'InternalController@formupdatearsip');
	Route::post('/form/hapusarsip', 'InternalController@formdeletearsip');

	Route::get('/saran', 'InternalController@saran');
	// Route::post('/form/reply', 'InternalController@formmailsaran');
	Route::post('/form/apprsaran', 'InternalController@formapprsaran');

	Route::get('/kehadiran', 'InternalController@kehadiranall');
	Route::get('/kehadiran tambah', 'InternalController@kehadirantambah');
	Route::post('/form/tambahkehadiran', 'InternalController@forminsertkehadiran');
	Route::get('/kehadiran ubah', 'InternalController@kehadiranubah');
	Route::post('/form/hapuskehadiran', 'InternalController@formdeletekehadiran');

    Route::get('/agenda-kaban', 'InternalController@agendakabanall');
    Route::get('/form/get-agenda-kaban', 'InternalController@getagendakaban');
    Route::post('/form/tambah-agenda-kaban', 'InternalController@forminsertagendakaban');
    Route::post('/form/ubah-agenda-kaban', 'InternalController@formupdateagendakaban');
    Route::post('/form/hapus-agenda-kaban', 'InternalController@formdeleteagendakaban');
    Route::post('/form/generate-agenda-kaban', 'InternalController@formgenerateagendakaban');
    Route::get('/form/export-excel-agenda-bpad', 'InternalController@exportexcelagendabpad');
});

Route::group(['prefix' => 'kepegawaian'], function () {
	Route::get('/setup/unit', 'KepegawaianSetupController@unitall');
	Route::post('/form/tambahunit', 'KepegawaianSetupController@forminsertunit');
	Route::post('/form/ubahunit', 'KepegawaianSetupController@formupdateunit');
	Route::post('/form/hapusunit', 'KepegawaianSetupController@formdeleteunit');

	Route::get('/excel', 'KepegawaianController@printexcel');
	Route::get('/excelpegawai', 'KepegawaianController@printexcelpegawai');
	Route::get('/excelpegawaiadmin', 'KepegawaianController@printexcelpegawaiadmin');

	Route::get('/data pegawai', 'KepegawaianController@pegawaiall');
	Route::get('/tambah pegawai', 'KepegawaianController@pegawaitambah');
	Route::get('/ubah pegawai', 'KepegawaianController@pegawaiubah');
	Route::get('/lihat pegawai', 'KepegawaianController@pegawailihat');
	Route::post('/form/approvepegawai', 'KepegawaianController@formapprovepegawai');
	Route::post('/form/tambahpegawai', 'KepegawaianController@forminsertpegawai');
	Route::post('/form/ubahpegawai', 'KepegawaianController@formupdatepegawai');
	Route::post('/form/hapuspegawai', 'KepegawaianController@formdeletepegawai');
	Route::post('/form/ubahpassuser', 'KepegawaianController@formupdatepassuser');
	Route::post('/form/ubahstatuspegawai', 'KepegawaianController@formupdatestatuspegawai');
	Route::post('/form/tambahdikpegawai', 'KepegawaianController@forminsertdikpegawai');
	Route::post('/form/ubahdikpegawai', 'KepegawaianController@formupdatedikpegawai');
	Route::post('/form/hapusdikpegawai', 'KepegawaianController@formdeletedikpegawai');
	Route::post('/form/tambahgolpegawai', 'KepegawaianController@forminsertgolpegawai');
	Route::post('/form/ubahgolpegawai', 'KepegawaianController@formupdategolpegawai');
	Route::post('/form/hapusgolpegawai', 'KepegawaianController@formdeletegolpegawai');
	Route::post('/form/tambahjabpegawai', 'KepegawaianController@forminsertjabpegawai');
	Route::post('/form/ubahjabpegawai', 'KepegawaianController@formupdatejabpegawai');
	Route::post('/form/hapusjabpegawai', 'KepegawaianController@formdeletejabpegawai');

	Route::get('/struktur', 'KepegawaianController@strukturorganisasi');


    // -----KINERJA-----

	Route::get('/kinerja tambah', 'KepegawaianController@kinerjatambah');
	Route::get('/getaktivitas', 'KepegawaianController@getaktivitas');
	Route::get('/getdetailaktivitas', 'KepegawaianController@getdetailaktivitas');
	Route::post('/form/tambahkinerja', 'KepegawaianController@forminsertkinerja');
	Route::get('/form/cekjadwalaktivitas', 'KepegawaianController@formcekjadwalaktivitas');
    
    Route::get('/entri kinerja', 'KinerjaController@entrikinerja');
	Route::post('/form/tambahaktivitas', 'KinerjaController@forminsertaktivitas');
    Route::post('/form/hapuskinerja', 'KinerjaController@formdeletekinerja');
    Route::post('/form/hapusaktivitas', 'KinerjaController@formdeleteaktivitas');

	Route::get('/approve kinerja', 'KepegawaianController@approvekinerja');
	Route::post('/form/approvekinerja', 'KepegawaianController@formapprovekinerja');
	Route::post('/form/approvekinerjasingle', 'KepegawaianController@formapprovekinerjasingle');

	Route::get('/laporan kinerja', 'KepegawaianController@laporankinerja');
	Route::post('/form/formresetkinerja', 'KepegawaianController@formresetkinerja');

    // -----KINERJA-----
	
	
	Route::get('/status disposisi', 'KepegawaianController@statusdisposisi');
	
	Route::get('/surat keluar', 'KepegawaianController@suratkeluar');
	Route::get('/surat keluar tambah', 'KepegawaianController@suratkeluartambah');
	Route::post('/surat keluar ubah', 'KepegawaianController@suratkeluarubah');
	Route::post('/form/tambahsuratkeluar', 'KepegawaianController@forminsertsuratkeluar');
	Route::post('/form/ubahsuratkeluar', 'KepegawaianController@formupdatesuratkeluar');
	Route::post('/form/hapussuratkeluar', 'KepegawaianController@formdeletesuratkeluar');
	
	//////////////////////////////////////////////////////////////////////
	Route::get('/laporan foto', 'Kepegawaian2Controller@laporanfoto');

	Route::get('/peta jabatan', 'Kepegawaian2Controller@petajabatan');
	Route::post('/form/insertjabchild', 'Kepegawaian2Controller@forminsertjabchild');

    //////////////////////////////////////////////////////////////////////
    Route::get('/report', 'KepegawaianReportController@index')->name('kepegawaian.report');
    
    Route::get('/excelpensiun', 'KepegawaianReportController@printexcelpensiun')->name('kepegawaian.report.excelpensiun');
    Route::get('/excelnaikgol', 'KepegawaianReportController@printexcelnaikgol')->name('kepegawaian.report.excelnaikgol');

});

Route::group(['prefix' => 'security'], function () {
	Route::get('/group user', 'SecurityController@grupall');
	Route::get('/group user/ubah', 'SecurityController@grupubah');
	Route::post('/form/tambahgrup', 'SecurityController@forminsertgrup');
	Route::post('/form/ubahgrup', 'SecurityController@formupdategrup');
	Route::post('/form/hapusgrup', 'SecurityController@formdeletegrup');

	Route::get('/tambah user', 'SecurityController@tambahuser');
	Route::post('/form/tambahuser', 'SecurityController@forminsertuser');

	Route::get('/manage user', 'SecurityController@manageuser');
	Route::post('/form/tambahuser', 'SecurityController@forminsertuser');
	Route::post('/form/ubahuser', 'SecurityController@formupdateuser');
	Route::post('/form/ubahpassuser', 'SecurityController@formupdatepassuser');
	Route::post('/form/hapususer', 'SecurityController@formdeleteuser');
});

// Route::get('/home', 'HomeController@index')->name('home');

// --------- BPAD HIDDEN URL-----------

Route::group(['prefix' => 'showmeyoursecrets'], function () {
	Route::get('/', 'HiddenController@index');
});

Route::group(['prefix' => 'qrabsen'], function () {
	Route::get('/setup', 'HiddenController@qrabsensetup');
	Route::get('/rekap', 'PublicController@qrabsenrekap');
	Route::get('/excelraw', 'HiddenController@qrabsenexcelraw');
	Route::get('/getpegawaitidakabsen', 'PublicController@getpegawaitidakabsen');
	Route::get('/detail', 'PublicController@qrabsendetail');
	Route::get('/setpegawai', 'HiddenController@qrabsensetpegawai');

	Route::get('/form/ubahactive', 'HiddenController@formupdateactive');
	Route::post('/form/ubahstshadir', 'HiddenController@formuptdatestsabsen');
	Route::post('/form/tambahabsen', 'HiddenController@forminsertqrabsen');
	Route::post('/form/ubahabsen', 'HiddenController@formupdateqrabsen');
	Route::post('/form/hapusabsen', 'HiddenController@formdeleteqrabsen');
	Route::get('/form/newqrabsen', 'HiddenController@formnewqrabsen');
});

Route::group(['prefix' => 'faq'], function () {
	Route::get('/app/{app_name}', 'FaqController@index');
    Route::get('/setup', 'FaqController@setup');
    Route::post('/insert', 'FaqController@insert');
    Route::post('/update', 'FaqController@update');
    Route::get('/delete', 'FaqController@delete');
});

Route::group(['prefix' => 'mobile'], function () {
	Route::get('/notif', 'MobileController@notifall');
	Route::get('/tambah notif', 'MobileController@tambahnotif');
	Route::post('/form/tambahnotif', 'MobileController@forminsertnotif');
	Route::post('/form/approvenotif', 'MobileController@formapprovenotif');
	Route::post('/form/hapusnotif', 'MobileController@formdeletenotif');
});

Route::group(['prefix' => 'pemanfaatan'], function () {
	Route::get('/images', 'PemanfaatanController@carouselall');
	Route::get('/tambah carousel', 'PemanfaatanController@tambahcarousel');
	Route::post('/form/tambahcarousel', 'PemanfaatanController@forminsertcarousel');
	Route::get('/ubah carousel', 'PemanfaatanController@ubahcarousel');
	Route::post('/form/ubahcarousel', 'PemanfaatanController@formupdatecarousel');
	Route::post('/form/approvecarousel', 'PemanfaatanController@formapprovecarousel');
	Route::post('/form/hapuscarousel', 'PemanfaatanController@formdeletecarousel');
});

Route::group(['prefix' => 'bpadstorage'], function () {
	Route::get('/', 'FileStorageController@index');
	Route::get('/test', 'FileStorageController@indextest');
});




















































































































Auth::routes();