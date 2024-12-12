@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Row untuk Logo -->
    <div class="row justify-content-center mb-5">
        <div class="col-md-12">
            <img src="{{ url('images/logo.png') }}" class="rounded mx-auto d-block" width="700" alt="Logo">
        </div>
    </div>

    <!-- Row untuk Produk -->
    <div class="row justify-content-center">
        @foreach($barangs as $barang)
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="{{ url('uploads') }}/{{ $barang->gambar }}" class="card-img-top" alt="Gambar Barang">
                    <div class="card-body">
                        <h5 class="card-title">{{ $barang->nama_barang }}</h5>
                        <p class="card-text">
                            <strong>Harga :</strong> Rp. {{ number_format($barang->harga) }} <br>
                            <strong>Stok :</strong> {{ $barang->stok }} <br>
                            <hr>
                            <strong>Keterangan :</strong> <br>
                            {{ $barang->keterangan }}
                        </p>
                        <a href="{{ url('pesan') }}/{{ $barang->id }}" class="btn btn-primary">
                            <i class="fa fa-shopping-cart"></i> Pesan
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
