<?php

public function formlihatdisposisi(Request $request)
{

    //kalo kekirim ada disposisi ke & staff barengan, dia return message gaboleh
    if (isset($request->jabatans) && isset($request->stafs)) {
        return redirect('/disposisi/lihat disposisi?ids='.$request->ids)
                ->with('message', 'Tidak boleh memilih jabatan & staf bersamaan')
                ->with('signdate', $request->signdate)
                ->with('msg_num', 2);
    }

    //if draft
    if (isset($request->btnDraft)) {
        $rd = 'D';
    } 
    // else submit kirim
    else {
        $rd = 'S';

        //if gapilih disposisi ke & staf, berarti disposisi stop di dia (bisa eselon & staf)
        if (is_null($request->jabatans) && is_null($request->stafs)) {
            $selesai = 'Y';
            $child = 0;
        } 
        // else disposisi lanjut ke orang yang di TL
        else {
            $selesai = '';
            $child = 1;
        }
    }

    //ngesplit nomer form berdasarkan titik, buat ngambil angka paling belakang buat jadi nama file disposisi baru
    $splitmaxform = explode(".", $request->no_form);


    //ngambil nama file dari "nm_file" dari query detail disposisi
    $filedispo = $request->nm_file_master;

    // dapetin tahun dari "tgl_masuk" dari query disposisi
    $diryear = date('Y',strtotime($request->tgl_masuk));

    //ini buat nyimpen kalo ada file baru
    // variabel file nya dalem bentuk array, jadi kalo cuma 1 ya array file count nya 1
    if (isset($request->nm_file)) {
        $file = $request->nm_file;

        //ini kalo file baru cuma ada 1
        if (count($file) == 1) {
            
            //kalo size kegedean, return
            if ($file[0]->getSize() > 52222222) {
                return redirect('/disposisi/lihat disposisi?ids='.$request->ids)
                        ->with('message', 'Ukuran file terlalu besar')
                        ->with('signdate', $request->signdate)
                        ->with('msg_num', 2);    
            } 

            //nambah pemisah "::" buat di nama file "nm_file" 
            if ($filedispo != '') {
                $filedispo .= '::';
            }

            //ngubah nama file baru jadi [disp][tgl "HIs"][8 digit terakhir "no_form" disposisi][extension]
            $filenow = 'disp';
            $filenow .= (int) date('HIs');
            $filenow .= ($splitmaxform[3]);
            $filenow .= ".". $file[0]->getClientOriginalExtension();

            //nentuin tujuan folder upload file di [PATH]/[tahun berdasarkan tahun di "tgl_masuk"]/[no_form] 
            $tujuan_upload = config('app.savefiledisposisi');
            $tujuan_upload .= "\\" . $diryear;
            $tujuan_upload .= "\\" . $request->no_form;

            //append "nm_file" sama nama file baru
            $filedispo .= $filenow;

            //upload file
            $file[0]->move($tujuan_upload, $filenow);
        } 
        // ini ternyata sama aja, cuma bedanya ini kalo file yang di upload lebih dari 1
        else {

            //nambah pemisah "::" buat di nama file "nm_file" 
            if ($filedispo != '') {
                $filedispo .= '::';
            }

            //loop di masing2 file
            foreach ($file as $key => $data) {

                //kalo size kegedean, return
                if ($data->getSize() > 52222222) {
                    return redirect('/disposisi/lihat disposisi?ids='.$request->ids)
                            ->with('message', 'Ukuran file terlalu besar')
                            ->with('signdate', $request->signdate)
                            ->with('msg_num', 2);      
                } 

                //ngubah nama file baru jadi [disp][tgl "HIs"][8 digit terakhir "no_form" disposisi][extension]
                $filenow = 'disp';
                $filenow .= (int) date('HIs') + $key;
                $filenow .= ($splitmaxform[3]);
                $filenow .= ".". $data->getClientOriginalExtension();

                //nentuin tujuan folder upload file di [PATH]/[tahun berdasarkan tahun di "tgl_masuk"]/[no_form] 
                $tujuan_upload = config('app.savefiledisposisi');
                $tujuan_upload .= "\\" . $diryear;
                $tujuan_upload .= "\\" . $request->no_form;
                $data->move($tujuan_upload, $filenow);
            
                //append "nm_file" sama nama file baru
                if ($key != 0) {
                    $filedispo .= "::";
                } 
                $filedispo .= $filenow;

            }
        }

        //query update table "fr_disposisi" ubah kolom "nm_file" jadi nama yg baru
        Fr_disposisi::where('ids', $request->idmaster)
        ->update([
            'nm_file' => $filedispo,
        ]);	
    }

    // kolom "kepada" diisi "idunit" dari form disposisi ke, teks dipisah pake "::"
    $kepada = '';
    if (isset($request->jabatans)) {
        for ($i=0; $i < count($request->jabatans); $i++) { 
            $kepada .= $request->jabatans[$i];
            if ($i != (count($request->jabatans) - 1)) {
                $kepada .= "::";
            }
        }
    }

    // kolom "noid" diisi "id_emp" dari form staf, teks dipisah pake "::"
    $noid = '';
    if (isset($request->stafs)) {
        if (count($request->stafs) == 1) {
            $noid = $request->stafs[0];
        }
    }

    // if user mencet draft
    if (isset($request->btnDraft)) {
        // update row disposisi dari "ids" dari detail disposisi
        Fr_disposisi::where('ids', $request->ids)
            ->update([
            // kolom "usr_input" diisi "id_emp" dari orang yang login ato ngambil "to_pm" dari data detail disposisi
            'usr_input' => (Auth::user()->id_emp ? Auth::user()->id_emp : $request->from_pm_new),
            'tgl_input' => date('Y-m-d'),
            'kepada' => $kepada,
            'penanganan' => (isset($request->penanganan) ? $request->penanganan : '' ),
            'catatan' => (isset($request->catatan) ? $request->catatan : '' ),
            'rd' => 'D',
            'prioritas' => $prioritas,
        ]);

        // return ke halaman list disposisi kalo berhasil draft
        $splitsigndate = explode("::", $request->signdate);
        $yearnow = $splitsigndate[0];
        $signnow = $splitsigndate[1];
        $monthnow = $splitsigndate[2];
        return redirect('/disposisi/disposisi?yearnow='.$yearnow.'&signnow='.$signnow.'&monthnow='.$monthnow)
                ->with('message', 'Disposisi berhasil diubah')
                ->with('msg_num', 1);
    }

    // if user milih tombol submit / kirim / simpan
    if (isset($request->btnKirim)) {

        // update row disposisi dari "ids" dari detail disposisi
        Fr_disposisi::where('ids', $request->ids)
            ->update([
            // kolom "usr_input" diisi "id_emp" dari orang yang login ato ngambil "to_pm" dari data detail disposisi
            'usr_input' => (Auth::user()->id_emp ? Auth::user()->id_emp : $request->from_pm_new),
            'tgl_input' => date('Y-m-d'),
            'kepada' => $kepada,
            'noid' => $noid,
            'penanganan' => (isset($request->penanganan) ? $request->penanganan : '' ),
            'catatan' => (isset($request->catatan) ? $request->catatan : '' ),
            'rd' => 'S',
            'selesai' => $selesai,
            'child' => $child,
            'prioritas' => $prioritas,
        ]);

        // if user ngisi disposisi ke
        if (isset($request->jabatans)) {
            //ngecek doang biar gada double jataban yg kepilih, diapus jg gpp
            $uniqjabatans = array_unique($request->jabatans);

            //loop berdasarkan total disposisi ke yang dipilih
            for ($i=0; $i < count($uniqjabatans); $i++) { 

                // query asli buat dapetin "id_emp" berdasarkan "idunit" dari jabatan yang dipilih di form disposisi ke
                // $findidjabatan = DB::select( DB::raw("
                //         SELECT id_emp,a.uname+'::'+convert(varchar,a.tgl)+'::'+a.ip,createdate,nip_emp,nrk_emp,nm_emp,nrk_emp+'-'+nm_emp as c2,gelar_dpn,gelar_blk,jnkel_emp,tempat_lahir,tgl_lahir,CONVERT(VARCHAR(10), tgl_lahir, 103) AS [DD/MM/YYYY],idagama,alamat_emp,tlp_emp,email_emp,status_emp,ked_emp,status_nikah,gol_darah,nm_bank,cb_bank,an_bank,nr_bank,no_taspen,npwp,no_askes,no_jamsos,tgl_join,CONVERT(VARCHAR(10), tgl_join, 103) AS [DD/MM/YYYY],tgl_end,reason,a.idgroup,pass_emp,foto,ttd,a.telegram_id,a.lastlogin,tbgol.tmt_gol,CONVERT(VARCHAR(10), tbgol.tmt_gol, 103) AS [DD/MM/YYYY],tbgol.tmt_sk_gol,CONVERT(VARCHAR(10), tbgol.tmt_sk_gol, 103) AS [DD/MM/YYYY],tbgol.no_sk_gol,tbgol.idgol,tbgol.jns_kp,tbgol.mk_thn,tbgol.mk_bln,tbgol.gambar,tbgol.nm_pangkat,tbjab.tmt_jab,CONVERT(VARCHAR(10), tbjab.tmt_jab, 103) AS [DD/MM/YYYY],tbjab.idskpd,tbjab.idunit,tbjab.idjab, tbunit.child, tbjab.idlok,tbjab.tmt_sk_jab,CONVERT(VARCHAR(10), tbjab.tmt_sk_jab, 103) AS [DD/MM/YYYY],tbjab.no_sk_jab,tbjab.jns_jab,tbjab.idjab,tbjab.eselon,tbjab.gambar,tbdik.iddik,tbdik.prog_sek,tbdik.no_sek,tbdik.th_sek,tbdik.nm_sek,tbdik.gelar_dpn_sek,tbdik.gelar_blk_sek,tbdik.ijz_cpns,tbdik.gambar,tbdik.nm_dik,b.nm_skpd,c.nm_unit,c.notes,d.nm_lok FROM bpaddtfake.dbo.emp_data as a
                //             CROSS APPLY (SELECT TOP 1 tmt_gol,tmt_sk_gol,no_sk_gol,idgol,jns_kp,mk_thn,mk_bln,gambar,nm_pangkat FROM  bpaddtfake.dbo.emp_gol,bpaddtfake.dbo.glo_org_golongan WHERE a.id_emp = emp_gol.noid AND emp_gol.idgol=glo_org_golongan.gol AND emp_gol.sts='1' AND glo_org_golongan.sts='1' ORDER BY tmt_gol DESC) tbgol
                //             CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
                //             CROSS APPLY (SELECT TOP 1 iddik,prog_sek,no_sek,th_sek,nm_sek,gelar_dpn_sek,gelar_blk_sek,ijz_cpns,gambar,nm_dik FROM  bpaddtfake.dbo.emp_dik,bpaddtfake.dbo.glo_dik WHERE a.id_emp = emp_dik.noid AND emp_dik.iddik=glo_dik.dik AND emp_dik.sts='1' AND glo_dik.sts='1' ORDER BY th_sek DESC) tbdik
                //             CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
                //             ,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
                //             and tbunit.kd_unit like '".$uniqjabatans[$i]."' and ked_emp = 'aktif'") );
                // $findidjabatan = json_decode(json_encode($findidjabatan), true);

                // query yg gapake cross apply buat dapetin id_emp 
                $findidjabatan = DB::select( DB::raw("
                select id_emp, nm_emp
                from emp_data a
                join emp_jab tbjab on tbjab.ids = (SELECT TOP 1 ids FROM bpaddtfake.dbo.emp_jab WHERE emp_jab.noid = a.id_emp and emp_jab.sts='1' ORDER BY tmt_jab DESC)
                join glo_org_unitkerja tbunit on tbunit.kd_unit = (SELECT TOP 1 idunit FROM bpaddtfake.dbo.glo_org_unitkerja where tbunit.kd_unit = tbjab.idunit)
                where a.ked_emp = 'AKTIF'
                and a.sts = 1
                and a.id_emp = tbjab.noid
                and tbjab.sts = 1
                and tbunit.kd_unit like '".$uniqjabatans[$i]."'
                order by nm_emp") );
                $findidjabatan = json_decode(json_encode($findidjabatan), true);

                // nginput row baru di "fr_disposisi" berdasarkan jabatan disposisi ke yang dipilih & kalo query diatas ngereturn sesuatu
                if (isset($findidjabatan[0])) {
                    $insertjabatan = [
                        'sts' => 1,
                        'uname'     => (Auth::user()->id_emp ? Auth::user()->id_emp : Auth::user()->usname),
                        'tgl'       => date('Y-m-d H:i:s'),
                        'ip'        => '',
                        'logbuat'   => '',
                        'kd_skpd'	=> '1.20.512',
                        //dapet dari "kd_unit" query detail disposisi
                        'kd_unit'	=> $request->kd_unit,
                        //dapet dari "no_form" query detail disposisi
                        'no_form' => $request->no_form,
                        'kd_surat' => null,
                        'status_surat' => null,
                        //dapet dari "ids" query detail disposisi
                        'idtop' => $request->ids,
                        //dapet dari "tgl_masuk" query detail disposisi
                        'tgl_masuk' => (isset($request->tgl_masuk) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_masuk))) : date('Y-m-d')),
                        'usr_input' => '',
                        'tgl_input' => null,
                        'no_index' => '',
                        'kode_disposisi' => '',
                        'perihal' => '',
                        'tgl_surat' => null,
                        'no_surat' => '',
                        'asal_surat' => '',
                        'kepada_surat' => '',
                        'sifat1_surat' => '',
                        'sifat2_surat' => '',
                        'ket_lain' => '',
                        'nm_file' => '',
                        'kepada' => '',
                        'noid' => '',
                        'penanganan' => '',
                        'catatan' => '',
                        'from_user' => 'E',
                        // kolom "from_pm" diisi "id_emp" dari orang yang login ato ngambil "to_pm" dari data detail disposisi
                        'from_pm' => (Auth::user()->id_emp ? Auth::user()->id_emp : $request->from_pm_new),
                        'to_user' => 'E',
                        // dapet "id_emp" dari query diatas
                        'to_pm' => $findidjabatan[0]['id_emp'],
                        'rd' => 'N',
                        'usr_rd' => null,
                        'tgl_rd' => null,
                        'selesai' => '',
                        'child' => 0,
                    ];
                    Fr_disposisi::insert($insertjabatan);
                }
            }
        }

        // if user ngisi milih staf
        if (isset($request->stafs)) {
            //ngecek doang biar gada double staf yg kepilih, diapus jg gpp
            $uniqstafs = array_unique($request->stafs);

            //loop berdasarkan total staf ke yang dipilih
            for ($i=0; $i < count($uniqstafs); $i++) { 

                //query versi panjang buat dapetin data emp berdasarkan staf yang dipilih
                // $findidstaf = DB::select( DB::raw("
                //         SELECT id_emp,a.uname+'::'+convert(varchar,a.tgl)+'::'+a.ip,createdate,nip_emp,nrk_emp,nm_emp,nrk_emp+'-'+nm_emp as c2,gelar_dpn,gelar_blk,jnkel_emp,tempat_lahir,tgl_lahir,CONVERT(VARCHAR(10), tgl_lahir, 103) AS [DD/MM/YYYY],idagama,alamat_emp,tlp_emp,email_emp,status_emp,ked_emp,status_nikah,gol_darah,nm_bank,cb_bank,an_bank,nr_bank,no_taspen,npwp,no_askes,no_jamsos,tgl_join,CONVERT(VARCHAR(10), tgl_join, 103) AS [DD/MM/YYYY],tgl_end,reason,a.idgroup,pass_emp,foto,ttd,a.telegram_id,a.lastlogin,tbgol.tmt_gol,CONVERT(VARCHAR(10), tbgol.tmt_gol, 103) AS [DD/MM/YYYY],tbgol.tmt_sk_gol,CONVERT(VARCHAR(10), tbgol.tmt_sk_gol, 103) AS [DD/MM/YYYY],tbgol.no_sk_gol,tbgol.idgol,tbgol.jns_kp,tbgol.mk_thn,tbgol.mk_bln,tbgol.gambar,tbgol.nm_pangkat,tbjab.tmt_jab,CONVERT(VARCHAR(10), tbjab.tmt_jab, 103) AS [DD/MM/YYYY],tbjab.idskpd,tbjab.idunit,tbjab.idjab, tbunit.child, tbjab.idlok,tbjab.tmt_sk_jab,CONVERT(VARCHAR(10), tbjab.tmt_sk_jab, 103) AS [DD/MM/YYYY],tbjab.no_sk_jab,tbjab.jns_jab,tbjab.idjab,tbjab.eselon,tbjab.gambar,tbdik.iddik,tbdik.prog_sek,tbdik.no_sek,tbdik.th_sek,tbdik.nm_sek,tbdik.gelar_dpn_sek,tbdik.gelar_blk_sek,tbdik.ijz_cpns,tbdik.gambar,tbdik.nm_dik,b.nm_skpd,c.nm_unit,c.notes,d.nm_lok FROM bpaddtfake.dbo.emp_data as a
                //             CROSS APPLY (SELECT TOP 1 tmt_gol,tmt_sk_gol,no_sk_gol,idgol,jns_kp,mk_thn,mk_bln,gambar,nm_pangkat FROM  bpaddtfake.dbo.emp_gol,bpaddtfake.dbo.glo_org_golongan WHERE a.id_emp = emp_gol.noid AND emp_gol.idgol=glo_org_golongan.gol AND emp_gol.sts='1' AND glo_org_golongan.sts='1' ORDER BY tmt_gol DESC) tbgol
                //             CROSS APPLY (SELECT TOP 1 tmt_jab,idskpd,idunit,idlok,tmt_sk_jab,no_sk_jab,jns_jab,replace(idjab,'NA::','') as idjab,eselon,gambar FROM  bpaddtfake.dbo.emp_jab WHERE a.id_emp=emp_jab.noid AND emp_jab.sts='1' ORDER BY tmt_jab DESC) tbjab
                //             CROSS APPLY (SELECT TOP 1 iddik,prog_sek,no_sek,th_sek,nm_sek,gelar_dpn_sek,gelar_blk_sek,ijz_cpns,gambar,nm_dik FROM  bpaddtfake.dbo.emp_dik,bpaddtfake.dbo.glo_dik WHERE a.id_emp = emp_dik.noid AND emp_dik.iddik=glo_dik.dik AND emp_dik.sts='1' AND glo_dik.sts='1' ORDER BY th_sek DESC) tbdik
                //             CROSS APPLY (SELECT TOP 1 * FROM bpaddtfake.dbo.glo_org_unitkerja WHERE glo_org_unitkerja.kd_unit = tbjab.idunit) tbunit
                //             ,bpaddtfake.dbo.glo_skpd as b,bpaddtfake.dbo.glo_org_unitkerja as c,bpaddtfake.dbo.glo_org_lokasi as d WHERE tbjab.idskpd=b.skpd AND tbjab.idskpd+'::'+tbjab.idunit=c.kd_skpd+'::'+c.kd_unit AND tbjab.idskpd+'::'+tbjab.idlok=d.kd_skpd+'::'+d.kd_lok AND a.sts='1' AND b.sts='1' AND c.sts='1' AND d.sts='1' 
                //             and id_emp like '".$uniqstafs[$i]."' and ked_emp = 'aktif'") );
                // $findidstaf = json_decode(json_encode($findidstaf), true);

                // query versi pendek, cek aja staf yg dipilih masih aktif ga akunnya
                $findidstaf = DB::select( DB::raw("
                select id_emp, nm_emp
                from emp_data
                where id_emp = '".$uniqstafs[$i]."'
                and sts = 1
                and ked_emp = 'AKTIF'") );
                $findidstaf = json_decode(json_encode($findidstaf), true);

                // nginput row baru di "fr_disposisi" berdasarkan staf yang dipilih & kalo query diatas ngereturn sesuatu
                if (isset($findidstaf[0])) {
                    $insertstaf = [
                        'sts' => 1,
                        'uname'     => (Auth::user()->id_emp ? Auth::user()->id_emp : Auth::user()->usname),
                        'tgl'       => date('Y-m-d H:i:s'),
                        'ip'        => '',
                        'logbuat'   => '',
                        'kd_skpd'	=> '1.20.512',
                        //dapet dari "kd_unit" query detail disposisi
                        'kd_unit'	=> $request->kd_unit,
                        //dapet dari "no_form" query detail disposisi
                        'no_form' => $request->no_form,
                        'kd_surat' => null,
                        'status_surat' => null,
                        //dapet dari "ids" query detail disposisi
                        'idtop' => $request->ids,
                        //dapet dari "tgl_masuk" query detail disposisi
                        'tgl_masuk' => (isset($request->tgl_masuk) ? date('Y-m-d',strtotime(str_replace('/', '-', $request->tgl_masuk))) : date('Y-m-d')),
                        'usr_input' => '',
                        'tgl_input' => null,
                        'no_index' => '',
                        'kode_disposisi' => '',
                        'perihal' => '',
                        'tgl_surat' => null,
                        'no_surat' => '',
                        'asal_surat' => '',
                        'kepada_surat' => '',
                        'sifat1_surat' => '',
                        'sifat2_surat' => '',
                        'ket_lain' => '',
                        'nm_file' => '',
                        'kepada' => '',
                        'noid' => '',
                        'penanganan' => '',
                        'catatan' => '',
                        'from_user' => 'E',
                        // kolom "from_pm" diisi "id_emp" dari orang yang login ato ngambil "to_pm" dari data detail disposisi
                        'from_pm' => (Auth::user()->id_emp ? Auth::user()->id_emp : $request->from_pm_new),
                        'to_user' => 'E',
                        // dapet "id_emp" dari query diatas
                        'to_pm' => $findidstaf[0]['id_emp'],
                        'rd' => 'N',
                        'usr_rd' => null,
                        'tgl_rd' => null,
                        'selesai' => '',
                        'child' => 0,
                    ];
                    Fr_disposisi::insert($insertstaf);
                }
            }
        }

        $splitsigndate = explode("::", $request->signdate);
        $yearnow = $splitsigndate[0];
        $signnow = $splitsigndate[1];
        $monthnow = $splitsigndate[2];

        // return
        return redirect('/disposisi/disposisi?yearnow='.$yearnow.'&signnow='.$signnow.'&monthnow='.$monthnow)
                ->with('message', 'Disposisi berhasil')
                ->with('msg_num', 1);
    }
}