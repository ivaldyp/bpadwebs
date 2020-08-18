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

Route::get('/', 'LandingController@index');
Route::get('/home', 'HomeController@index');
Route::POST('/home/password', 'HomeController@password');
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


// ------------- BPAD CMS -------------

Route::get('/profil', function () {
    return view('pages.profil');
});

Route::group(['prefix' => 'content'], function () {
	Route::get('/berita', 'ContentController@berita_all');
	Route::get('/berita/{id}', 'ContentController@berita_read');
	Route::get('/lelang', 'ContentController@lelang');
	Route::get('/foto', 'ContentController@foto_all');
	Route::get('/foto/{id}', 'ContentController@foto_open');
	Route::get('/video', 'ContentController@video_all');
	Route::get('/video/{id}', 'ContentController@video_open');
});

// ------------- BPAD DT --------------

Route::group(['prefix' => 'booking'], function () {
	Route::get('/manageruang', 'BookingController@manageruang');
	Route::post('form/tambahruang', 'BookingController@forminsertruang');
	Route::post('form/ubahruang', 'BookingController@formupdateruang');
	Route::post('form/hapusruang', 'BookingController@formdeleteruang');

	Route::get('pinjam', 'BookingController@formpinjam');
	Route::get('ubah pinjam', 'BookingController@ubahpinjam');
	Route::post('form/tambahpinjam', 'BookingController@forminsertpinjam');

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

	Route::get('/disposisi', 'DisposisiController@disposisi');
	Route::get('/lihat disposisi', 'DisposisiController@disposisilihat');
	Route::post('form/lihatdisposisi', 'DisposisiController@formlihatdisposisi');
	Route::get('form/hapusdisposisiemp', 'DisposisiController@formdeletedisposisiemp');

	Route::get('/excel', 'DisposisiController@printexcel');
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
	Route::post('/form/ubahidpegawai', 'ProfilController@formupdateidpegawai');
	Route::post('/form/tambahdikpegawai', 'ProfilController@forminsertdikpegawai');
	Route::post('/form/ubahdikpegawai', 'ProfilController@formupdatedikpegawai');
	Route::post('/form/hapusdikpegawai', 'ProfilController@formdeletedikpegawai');
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
	Route::get('/tambah content', 'CmsController@contenttambah');
	Route::post('/ubah content', 'CmsController@contentubah');
	Route::get('/form/apprcontent', 'CmsController@formapprcontent');
	Route::post('/form/tambahcontent', 'CmsController@forminsertcontent');
	Route::post('/form/ubahcontent', 'CmsController@formupdatecontent');
	Route::post('/form/hapuscontent', 'CmsController@formdeletecontent');

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

	Route::get('/saran', 'InternalController@saran');
	Route::post('/form/apprsaran', 'InternalController@formapprsaran');
});

Route::group(['prefix' => 'kepegawaian'], function () {
	Route::get('/excel', 'KepegawaianController@printexcel');
	Route::get('/excelpegawai', 'KepegawaianController@printexcelpegawai');

	Route::get('/data pegawai', 'KepegawaianController@pegawaiall');
	Route::get('/tambah pegawai', 'KepegawaianController@pegawaitambah');
	Route::get('/ubah pegawai', 'KepegawaianController@pegawaiubah');
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

	Route::get('/entri kinerja', 'KepegawaianController@entrikinerja');
	Route::post('/kinerja tambah', 'KepegawaianController@kinerjatambah');
	Route::get('/getaktivitas', 'KepegawaianController@getaktivitas');
	Route::get('/getdetailaktivitas', 'KepegawaianController@getdetailaktivitas');
	Route::post('/form/tambahkinerja', 'KepegawaianController@forminsertkinerja');
	Route::post('/form/hapuskinerja', 'KepegawaianController@formdeletekinerja');
	Route::post('/form/tambahaktivitas', 'KepegawaianController@forminsertaktivitas');
	Route::get('/form/hapusaktivitas', 'KepegawaianController@formdeleteaktivitas');

	Route::get('/approve kinerja', 'KepegawaianController@approvekinerja');
	Route::post('/form/approvekinerja', 'KepegawaianController@formapprovekinerja');
	Route::post('/form/approvekinerjasingle', 'KepegawaianController@formapprovekinerjasingle');

	Route::get('/laporan kinerja', 'KepegawaianController@laporankinerja');

	Route::get('/status disposisi', 'KepegawaianController@statusdisposisi');

	Route::get('/surat keluar', 'KepegawaianController@suratkeluar');
	Route::get('/surat keluar tambah', 'KepegawaianController@suratkeluartambah');
	Route::post('/surat keluar ubah', 'KepegawaianController@suratkeluarubah');
	Route::post('/form/tambahsuratkeluar', 'KepegawaianController@forminsertsuratkeluar');
	Route::post('/form/ubahsuratkeluar', 'KepegawaianController@formupdatesuratkeluar');
	Route::post('/form/hapussuratkeluar', 'KepegawaianController@formdeletesuratkeluar');
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
Auth::routes();

// Route::get('/home', 'HomeController@index')->name('home');
