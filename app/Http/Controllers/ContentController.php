<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Content_tb;
use App\Glo_kategori;
use App\Glo_subkategori;

session_start();

class ContentController extends Controller
{
	public function berita_all(Request $request)
	{
		if (isset($request->cari)) {
			$cari = $request->cari;
		} else {
			$cari = '';
		}
		$berita_list = Content_tb::
					where('idkat', 1)
					->where('appr', 'Y')
					->where('sts', 1)
					->whereRaw("judul like '%".$cari."%'")
					->orderBy('tanggal', 'desc')
					->paginate(10);
		$berita_list->appends($request->only('cari'));

		$aside_top_view = Content_tb::take(3)
							->where('appr', 'Y')
							->where('sts', 1)
							->where('idkat', 1)
							->orderBy('thits', 'desc')
							->get();

		$aside_recent = Content_tb::take(3)
							->where('appr', 'Y')
							->where('sts', 1)
							->where('idkat', 1)
							->orderBy('tanggal', 'desc')
							->get();

		return view('pages.berita.berita')
				->with('berita_list', $berita_list)
				->with('cari', $cari)
				->with('aside_top_view', $aside_top_view)
				->with('aside_recent', $aside_recent);
	}

	public function berita_read($id)
	{
		$thits = Content_tb::
					where('ids', $id)
					->first(['thits']);

		Content_tb::where('ids', $id)
			->update([
				'thits' => $thits['thits'] + 1,
			]);

		$berita = Content_tb::
					where('ids', $id)
					->first();

		$aside_top_view = Content_tb::take(3)
							->where('appr', 'Y')
							->where('idkat', 1)
							->where('sts', 1)
							->orderBy('thits', 'desc')
							->get();

		$aside_recent = Content_tb::take(3)
							->where('appr', 'Y')
							->where('idkat', 1)
							->where('sts', 1)
							->orderBy('tanggal', 'desc')
							->get();

		return view('pages.berita.beritasingle')
				->with('berita', $berita)
				->with('aside_top_view', $aside_top_view)
				->with('aside_recent', $aside_recent);
	}

	public function lelang()
	{
		$lelang_id = Glo_kategori::
						where('nmkat', 'lelang')
						->where('sts', 1)
						->first();

		$lelang = Content_tb::
					where('idkat', $lelang_id['ids'])
					// ->where('appr', 'Y')
					->where('sts', 1)
					->orderBy('tanggal', 'desc')
					->first();

		if ($lelang != null) {
			$thits = Content_tb::
						where('ids', $lelang['ids'])
						->first(['thits']);

			Content_tb::where('ids', $lelang['ids'])
				->update([
					'thits' => $thits['thits'] + 1,
				]);
		}

		return view('pages.berita.lelang')
				->with('berita', $lelang);
	}

	public function foto_all(Request $request)
	{
		if (isset($request->cari)) {
			$cari = $request->cari;
		} else {
			$cari = '';
		}

		$subkat = $request->subkategori;
		
		if (is_null($subkat)) {
			$foto_list = Content_tb::
					where('idkat', 5)
					->where('appr', 'Y')
					->where('sts', 1)
					->whereRaw("judul like '%".$cari."%'")
					->orderBy('tgl', 'desc')
					->paginate(10);
		} else {
			$foto_list = Content_tb::
					where('idkat', 5)
					->where('sts', 1)
					->where('subkat', $subkat)
					->where('appr', 'Y')
					->whereRaw("judul like '%".$cari."%'")
					->orderBy('tgl', 'desc')
					->paginate(10);
		}
		$foto_list->appends($request->only('cari'));
		
		$foto_kategori = Glo_subkategori::
						where('idkat', 5)
						->orderBy('urut_subkat', 'asc')
						->get();

		$aside_recent = Content_tb::take(3)
							->where('appr', 'Y')
							->where('sts', 1)
							->where('idkat', 5)
							->orderBy('tgl', 'desc')
							->get();

		return view('pages.foto.foto')
				->with('foto_list', $foto_list)
				->with('foto_kategori', $foto_kategori)
				->with('aside_recent', $aside_recent)
				->with('subkat', $subkat)
				->with('cari', $cari);
	}

	public function foto_open($id)
	{   
		$foto = Content_tb::
					where('ids', $id)
					->first();

		$aside_recent = Content_tb::take(3)
							->where('appr', 'Y')
							->where('idkat', 5)
							->orderBy('tanggal', 'desc')
							->get();

		return view('pages.foto.fotosingle')
				->with('foto', $foto)
				->with('aside_recent', $aside_recent);
	}

	public function video_all(Request $request)
	{
		if (isset($request->cari)) {
			$cari = $request->cari;
		} else {
			$cari = '';
		}

		$video_list = Content_tb::
					where('idkat', 12)
					->where('appr', 'Y')
					->where('sts', 1)
					->whereRaw("judul like '%".$cari."%'")
					->orderBy('tanggal', 'desc')
					->paginate(15);
		$video_list->appends($request->only('cari'));

		$aside_top_view = Content_tb::take(3)
							->where('appr', 'Y')
							->where('idkat', 12)
							->orderBy('thits', 'desc')
							->get();

		return view('pages.video.video')
				->with('video_list', $video_list)
				->with('aside_top_view', $aside_top_view)
				->with('cari', $cari);
	}

	public function video_open($id)
	{
		$video = Content_tb::
					where('ids', $id)
					->first();

		$aside_top_view = Content_tb::take(3)
							->where('appr', 'Y')
							->where('idkat', 12)
							->orderBy('thits', 'desc')
							->get();

		return view('pages.video.videosingle')
				->with('video', $video)
				->with('aside_top_view', $aside_top_view);
	}
}
