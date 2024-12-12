<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Pesanan;
use App\Models\PesananDetail;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PesanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($id)
    {
        $barang = Barang::where('id', $id)->first();
        return view('pesan.index', compact('barang'));
    }

    public function pesan(Request $request, $id)
    {
        $barang = Barang::where('id', $id)->first();
        $tanggal = Carbon::now();

        // Validasi apakah jumlah pesan melebihi stok
        if ($request->jumlah_pesan > $barang->stok) {
            return redirect('pesan/' . $id)->with('error', 'Jumlah pesanan melebihi stok yang tersedia.');
        }

        // Cek apakah pesanan dengan status 0 sudah ada
        $cek_pesanan = Pesanan::where('user_id', Auth::user()->id)->where('status', 0)->first();

        // Jika tidak ada, buat pesanan baru
        if (empty($cek_pesanan)) {
            $pesanan = new Pesanan();
            $pesanan->user_id = Auth::user()->id;
            $pesanan->tanggal = $tanggal;
            $pesanan->status = 0;
            $pesanan->jumlah_harga = 0;
            $pesanan->save();
        }

        // Ambil pesanan baru atau yang sudah ada
        $pesanan_baru = Pesanan::where('user_id', Auth::user()->id)->where('status', 0)->first();

        // Cek apakah barang sudah ada di pesanan detail
        $cek_pesanan_detail = PesananDetail::where('barang_id', $barang->id)
            ->where('pesanan_id', $pesanan_baru->id)
            ->first();

        if (empty($cek_pesanan_detail)) {
            // Jika belum ada, tambahkan ke pesanan detail
            $pesanan_detail = new PesananDetail();
            $pesanan_detail->barang_id = $barang->id;
            $pesanan_detail->pesanan_id = $pesanan_baru->id;
            $pesanan_detail->jumlah = $request->jumlah_pesan;
            $pesanan_detail->jumlah_harga = $barang->harga * $request->jumlah_pesan;
            $pesanan_detail->save();
        } else {
            // Jika sudah ada, update pesanan detail
            $cek_pesanan_detail->jumlah += $request->jumlah_pesan;
            $harga_pesanan_detail_baru = $barang->harga * $request->jumlah_pesan;
            $cek_pesanan_detail->jumlah_harga += $harga_pesanan_detail_baru;
            $cek_pesanan_detail->update();
        }

        // Update total harga pada pesanan
        $pesanan_baru->jumlah_harga += $barang->harga * $request->jumlah_pesan;
        $pesanan_baru->update();

        return redirect('home')->with('success', 'Pesanan Sudah Masuk Keranjang!');
    }
}
