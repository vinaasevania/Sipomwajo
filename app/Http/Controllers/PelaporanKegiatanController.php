<?php

namespace App\Http\Controllers;

use App\Models\PelaporanKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use JD\Cloudder\Facades\Cloudder;
use RealRashid\SweetAlert\Facades\Alert;

class PelaporanKegiatanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $pelaporanKegiatan = PelaporanKegiatan::where('ormas_id', $user->id)->latest()->get();
        return view('ormas.pelaporan-kegiatan.index', compact('pelaporanKegiatan'));
    }

    public function create()
    {
        return view('ormas.pelaporan-kegiatan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ormas_id' => 'required',
            'jenis_kegiatan' => 'required',
            'waktu_pelaksanaan' => 'required',
            'lokasi_pelaksanaan' => 'required',
            'kelurahan' => 'required',
            'kecamatan' => 'required',
            'ketupat' => 'required',
            'jumlah_anggaran' => 'required',
            'tujuan_permohonan' => 'nullable',
            'laporan_kegiatan'   => 'required|mimes:pdf|max:500000',
            'foto_kegiatan1' => 'required|image|mimes:jpeg,png,jpg|max:5000',
            'foto_kegiatan2' => 'required|image|mimes:jpeg,png,jpg|max:5000',
            'foto_kegiatan3' => 'nullable|image|mimes:jpeg,png,jpg|max:5000',
        ]);
        // surat permohonan
        $file1 = $request->file('laporan_kegiatan');

        Cloudder::upload($file1->getRealPath(), null, [
            'folder' => 'laporan-kegiatan',
        ]);

        $laporan_kegiatan = Cloudder::resource(Cloudder::getPublicId());
        $laporan_kegiatan = $laporan_kegiatan['url'];

        // foto_kegiatan1
        $file2 = $request->file('foto_kegiatan1');
        Cloudder::upload($file2->getRealPath(), null, [
            'folder' => 'laporan-kegiatan',
        ]);
        $foto_kegiatan1 = Cloudder::show(Cloudder::getPublicId());

        // foto_kegiatan2
        $file3 = $request->file('foto_kegiatan2');
        Cloudder::upload($file3->getRealPath(), null, [
            'folder' => 'laporan-kegiatan',
        ]);
        $foto_kegiatan2 = Cloudder::show(Cloudder::getPublicId());


        if ($request->file('foto_kegiatan3')) {
            // foto_kegiatan3
            $file4 = $request->file('foto_kegiatan3');
            Cloudder::upload($file4->getRealPath(), null, [
                'folder' => 'laporan-kegiatan',
            ]);
            $foto_kegiatan3 = Cloudder::show(Cloudder::getPublicId());

            $pelaporanKegiatan = new PelaporanKegiatan([
                "status" => "Menunggu Verifikasi",
                "ormas_id" => $request->ormas_id,
                "jenis_kegiatan" => $request->jenis_kegiatan,
                "waktu_pelaksanaan" => $request->waktu_pelaksanaan,
                "lokasi_pelaksanaan" => $request->lokasi_pelaksanaan,
                "kelurahan" => $request->kelurahan,
                "kecamatan" => $request->kecamatan,
                "ketupat" => $request->ketupat,
                "jumlah_anggaran" => $request->jumlah_anggaran,
                "tujuan_permohonan" => $request->tujuan_permohonan,
                "laporan_kegiatan" => $laporan_kegiatan,
                "foto_kegiatan1" => $foto_kegiatan1,
                "foto_kegiatan2" => $foto_kegiatan2,
                "foto_kegiatan3" => $foto_kegiatan3,
            ]);
            $pelaporanKegiatan->save();
        } else {
            $pelaporanKegiatan = new PelaporanKegiatan([
                "status" => "Menunggu Verifikasi",
                "ormas_id" => $request->ormas_id,
                "jenis_kegiatan" => $request->jenis_kegiatan,
                "waktu_pelaksanaan" => $request->waktu_pelaksanaan,
                "lokasi_pelaksanaan" => $request->lokasi_pelaksanaan,
                "kelurahan" => $request->kelurahan,
                "kecamatan" => $request->kecamatan,
                "ketupat" => $request->ketupat,
                "jumlah_anggaran" => $request->jumlah_anggaran,
                "tujuan_permohonan" => $request->tujuan_permohonan,
                "laporan_kegiatan" => $laporan_kegiatan,
                "foto_kegiatan1" => $foto_kegiatan1,
                "foto_kegiatan2" => $foto_kegiatan2,
            ]);
            $pelaporanKegiatan->save();
        }


        Alert::success('Sukses', 'Berhasil Mengajukan Pelaporan Kegiatan');
        return redirect('/pelaporan-kegiatan/index');
    }

    public function show($id)
    {
        $pelaporanKegiatan = PelaporanKegiatan::find($id);

        // Pastikan Pelaporan Kegiatan ditemukan
        if (!$pelaporanKegiatan) {
            return redirect('/pelaporan-kegiatan/index');
        }

        // Periksa apakah pengguna yang masuk adalah pemilik Pelaporan Kegiatan
        if (Auth::user()->id != $pelaporanKegiatan->ormas_id) {
            return redirect('/pelaporan-kegiatan/index');
        }

        return view('ormas.pelaporan-kegiatan.detail', compact('pelaporanKegiatan'));
    }
}
