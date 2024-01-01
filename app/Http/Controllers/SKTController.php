<?php

namespace App\Http\Controllers;

use App\Models\SKT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use JD\Cloudder\Facades\Cloudder;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Str;

class SKTController extends Controller
{
    public function index()
    {
        return view('ormas.dashboard.index');
    }

    public function statusSkt()
    {
        $user = Auth::user();
        $latestStatusSkt = SKT::where('ormas_id', $user->id)->latest()->first();
        return view('ormas.dashboard.status', compact('latestStatusSkt'));
    }

    public function keorganisasian()
    {
        $user = Auth::user();
        $keorganisasian = SKT::where('ormas_id', $user->id)->latest()->first();
        return view('ormas.dashboard.keorganisasian', compact('keorganisasian'));
    }

    public function kepengurusan()
    {
        $user = Auth::user();
        $kepengurusan = SKT::where('ormas_id', $user->id)->latest()->first();
        return view('ormas.dashboard.kepengurusan', compact('kepengurusan'));
    }

    public function dokumen()
    {
        $user = Auth::user();
        $dokumen = SKT::where('ormas_id', $user->id)->latest()->first();
        return view('ormas.dashboard.dokumen', compact('dokumen'));
    }

    public function create()
    {
        return view('ormas.dashboard.permohonan-skt');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'ormas_id' => 'required',

                // data keorganisasian
                'bidang_kegiatan' => 'nullable',
                'no_telp' => 'required',
                'alamat_sekretariat' => 'required',
                'kelurahan' => 'required',
                'kecamatan' => 'required',
                'tempat_pendirian' => 'required',
                'tahun_pendirian' => 'required',
                'asas_organisasi' => 'nullable',
                'tujuan_organisasi' => 'nullable',

                // data kepergurusan
                'nama_pendiri' => 'required',
                'nama_pembina' => 'required',
                'nama_penasehat' => 'required',
                'nama_ketua' => 'required',
                'nama_sekertaris' => 'required',
                'nama_bendahara' => 'nullable',
                'masa_bhakti' => 'required',
                'keputusan_tertinggi' => 'required',
                'bentuk_organisasi' => 'required',
                'usaha_organisasi' => 'nullable',
                'sumber_keuangan' => 'nullable',

                // dokumen ormas
                'logo_organisasi' => 'required|image|mimes:jpeg,png,jpg|max:5000',
                'surat_pengantar'   => 'required|mimes:pdf|max:500000',
                'sk_kemenkum_ham'   => 'required|mimes:pdf|max:500000',
                'sk_pengurus'   => 'required|mimes:pdf|max:500000',
                'sk_domisili'   => 'required|mimes:pdf|max:500000',
                'surat_pernyataan'   => 'required|mimes:pdf|max:500000',
                'biodata_pengurus'   => 'required|mimes:pdf|max:500000',
                'akta_notaris'   => 'required|mimes:pdf|max:500000',
            ]);

            $ormas_name = Str::snake(auth()->user()->nama_organisasi, '-');

            // logo
            $file1 = $request->file('logo_organisasi');
            Cloudder::upload($file1->getRealPath(), null, [
                'folder' => "$ormas_name/logo_organisasi",
            ]);
            $logo_organisasi = Cloudder::show(Cloudder::getPublicId());

            // surat_pengantar
            $file2 = $request->file('surat_pengantar');
            Cloudder::upload($file2->getRealPath(), null, [
                'folder' => $ormas_name.'/surat-pengantar',
            ]);
            $surat_pengantar = Cloudder::resource(Cloudder::getPublicId());
            $surat_pengantar = $surat_pengantar['url'];

            // sk_kemenkum_ham
            $file3 = $request->file('sk_kemenkum_ham');
            Cloudder::upload($file3->getRealPath(), null, [
                'folder' => $ormas_name.'/sk-kemenkumham',
            ]);
            $sk_kemenkum_ham = Cloudder::resource(Cloudder::getPublicId());
            $sk_kemenkum_ham = $sk_kemenkum_ham['url'];

            // sk_pengurus
            $file4 = $request->file('sk_pengurus');
            Cloudder::upload($file4->getRealPath(), null, [
                'folder' => $ormas_name.'/sk-pengurus',
            ]);
            $sk_pengurus = Cloudder::resource(Cloudder::getPublicId());
            $sk_pengurus = $sk_pengurus['url'];

            // sk_domisili
            $file5 = $request->file('sk_domisili');
            Cloudder::upload($file5->getRealPath(), null, [
                'folder' => $ormas_name.'/sk-domisili',
            ]);
            $sk_domisili = Cloudder::resource(Cloudder::getPublicId());
            $sk_domisili = $sk_domisili['url'];

            // surat_pernyataan
            $file6 = $request->file('surat_pernyataan');
            Cloudder::upload($file6->getRealPath(), null, [
                'folder' => $ormas_name.'/surat-pernyataan',
            ]);
            $surat_pernyataan = Cloudder::resource(Cloudder::getPublicId());
            $surat_pernyataan = $surat_pernyataan['url'];

            // biodata_pengurus
            $file7 = $request->file('biodata_pengurus');
            Cloudder::upload($file7->getRealPath(), null, [
                'folder' => $ormas_name.'/biodata-pengurus',
            ]);
            $biodata_pengurus = Cloudder::resource(Cloudder::getPublicId());
            $biodata_pengurus = $biodata_pengurus['url'];

            // akta_notaris
            $file8 = $request->file('akta_notaris');
            Cloudder::upload($file8->getRealPath(), null, [
                'folder' => $ormas_name.'/biodata-pengurus',
            ]);
            $akta_notaris = Cloudder::resource(Cloudder::getPublicId());
            $akta_notaris = $akta_notaris['url'];

            $permohonanSkt = new SKT([
                "status" => "Menunggu Verifikasi",
                "ormas_id" => $request->ormas_id,

                // data keorganisasian
                "bidang_kegiatan" => $request->bidang_kegiatan,
                "no_telp" => $request->no_telp,
                'alamat_sekretariat' => $request->alamat_sekretariat,
                'kelurahan' => $request->kelurahan,
                'kecamatan' => $request->kecamatan,
                'tempat_pendirian' => $request->tempat_pendirian,
                'tahun_pendirian' => $request->tahun_pendirian,
                'asas_organisasi' => $request->asas_organisasi,
                'tujuan_organisasi' => $request->tujuan_organisasi,

                // data keorganisasian
                "nama_pendiri" => $request->nama_pendiri,
                "nama_pembina" => $request->nama_pembina,
                "nama_penasehat" => $request->nama_penasehat,
                "nama_ketua" => $request->nama_ketua,
                "nama_sekertaris" => $request->nama_sekertaris,
                "nama_bendahara" => $request->nama_bendahara,
                "masa_bhakti" => $request->masa_bhakti,
                "keputusan_tertinggi" => $request->keputusan_tertinggi,
                "bentuk_organisasi" => $request->bentuk_organisasi,
                "usaha_organisasi" => $request->usaha_organisasi,
                "sumber_keuangan" => $request->sumber_keuangan,

                'logo_organisasi' => $logo_organisasi,
                'surat_pengantar'   => $surat_pengantar,
                'sk_kemenkum_ham'   => $sk_kemenkum_ham,
                'sk_pengurus'   => $sk_pengurus,
                'sk_domisili'   => $sk_domisili,
                'surat_pernyataan'   => $surat_pengantar,
                'biodata_pengurus' => $biodata_pengurus,
                'akta_notaris'   => $akta_notaris,
            ]);

            $permohonanSkt->save();
            Alert::success('Sukses', 'Berhasil Mengajukan Permohonan Skt');
            return redirect('/dashboard-ormas/status-skt');
        } catch (\Exception $e) {
            // Tampilkan pesan kesalahan jika ada
            Log::error($e->getMessage());
            Alert::error('Error', 'Gagal menyimpan data. ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function show($id)
    {
        $permohonanSkt = SKT::find($id);

        // Pastikan permohonan dana ditemukan
        if (!$permohonanSkt) {
            return redirect('/permohonan-skt/index');
        }

        // Periksa apakah pengguna yang masuk adalah pemilik permohonan dana
        if (Auth::user()->id != $permohonanSkt->ormas_id) {
            return redirect('/permohonan-skt/index');
        }

        return view('ormas.permohonan-skt.detail', compact('permohonanSkt'));
    }
}
