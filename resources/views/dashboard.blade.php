@extends('layout')

@section('title','Dashboard')
@section('judul','Dashboard')

@section('isi')

@php
$user = session('user');

use App\Models\PendaftaranModel;
use App\Models\PelatihanModel;
use App\Models\SertifikatModel;
use App\Models\SesiPelatihanModel;

$cards = [];

// ADMIN
if ($user && $user->role === 'admin') {
   $cards = [
    ['title'=>'Total Peserta','value'=>PendaftaranModel::count(),'color'=>'bg-primary'],
    ['title'=>'Pelatihan Berlangsung','value'=>PelatihanModel::where('status','berlangsung')->count(),'color'=>'bg-success'],
    ['title'=>'Pelatihan Selesai','value'=>PelatihanModel::where('status','selesai')->count(),'color'=>'bg-danger'],
    ['title'=>'Sertifikat','value'=>SertifikatModel::count(),'color'=>'bg-info'],
    ['title'=>'Instruktur','value'=>SesiPelatihanModel::distinct('id_instruktur')->count(),'color'=>'bg-warning'],
   ];
}

// INSTRUKTUR
elseif ($user && $user->role === 'instruktur') {
    $cards = [
        ['title'=>'Pelatihan Berlangsung','value'=>SesiPelatihanModel::where('id_instruktur',$user->id)->count()],
        ['title'=>'Pelatihan Selesai','value'=>SesiPelatihanModel::where('id_instruktur',$user->id)->count()],
        ['title'=>'Total Peserta','value'=>PendaftaranModel::count()],
        ['title'=>'Sertifikat','value'=>SertifikatModel::count()],
    ];
}

// USER
else {
    $cards = [
        ['title'=>'Pelatihan Terdaftar','value'=>PendaftaranModel::where('user_id',$user->id)->count()],
        ['title'=>'Pelatihan Berlangsung','value'=>0],
        ['title'=>'Pelatihan Selesai','value'=>0],
        ['title'=>'Sertifikat','value'=>SertifikatModel::where('id_user',$user->id)->count()],
    ];
}
@endphp


<div id="overlay" class="overlay"></div>

<div class="container-fluid">

    <!-- HEADER -->
    <div class="row">
        <div class="col-12">
            <div class="mb-6">
                <h1 class="fs-3 mb-1">Dashboard</h1>
                <p>Your main content goes here…</p>
            </div>
        </div>
    </div>

    <!-- CARD -->
    <div class="row g-3 mb-3">
        @foreach($cards as $card)
        <div class="col-lg-3 col-12">
            <div class="card p-4 {{ $card['color'] ?? 'bg-light' }} bg-opacity-10 border rounded-2">
                <div class="d-flex gap-3">
                    <div class="icon-shape icon-md {{ $card['color'] ?? 'bg-secondary' }} text-white rounded-2">
                        <i class="ti ti-report-analytics fs-4"></i>
                    </div>
                    <div>
                        <h2 class="mb-3 fs-6">{{ $card['title'] }}</h2>
                        <h3 class="fw-bold mb-0">{{ $card['value'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- GRAFIK + TOP 5 -->
    <div class="row g-3 mb-3">

        <!-- GRAFIK -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5>Grafik Pendaftaran</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartPendaftaran"></canvas>
                </div>
            </div>
        </div>

        <!-- TOP 5 -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5>Top 5 Pelatihan</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">

                        @foreach($topPelatihan as $item)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $item->nama_pelatihan }}</span>
                            <span class="badge bg-primary">{{ $item->total }}</span>
                        </li>
                        @endforeach

                    </ul>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection



@yield('script')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('chartPendaftaran');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
        datasets: [{
            label: 'Jumlah Pendaftaran',
            data: @json($grafik ?? []),
            borderWidth: 2
        }]
    }
});
</script>

@endsection