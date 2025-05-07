@extends('layouts.detail')

@section('stylesheet')
@endsection

@section('title', 'Syarat Layanan')
@section('back', route('home'))

@section('content')
<h3>Syarat Layanan</h3>

<p>Selamat Datang! Kami berharap bahwa Kamu akan menikmati pengalaman online Kamu.</p>
<p>{{ config('app.name') }} berkomitmen untuk menjaga kepercayaan dengan pengguna kami. Persyaratan di bawah ini mengatur penggunaan Kamu atas aplikasi ini.</p>

<h4>Penggunaan yang dapat diterima</h4>
<p>Silakan menjelajahi aplikasi kami dengan leluasa.</p>
<p>Namun, penggunaan aplikasi dan bahan yang diposting ke aplikasi ini seharusnya bukan yang ilegal atau yang menyinggung dengan cara apapun. Kamu harus berhati-hati untuk tidak:</p>
<ul>
    <li>Melanggar hak orang lain untuk privasi;</li>
    <li>Melanggar hak kekayaan intelektual;</li>
    <li>Membuat pernyataan yang memfitnah (termasuk terhadap {{ config('app.name') }}), berhubungan dengan pornografi, bersifat rasis atau xenofobia, mempromosikan kebencian atau menghasut kekerasan atau gangguan;</li>
    <li>Mengunggah file yang berisi virus atau dapat menyebabkan masalah keamanan; atau</li>
    <li>Tidak membahayakan integritas aplikasi.</li>
</ul>
<p>Harap dicatat bahwa {{ config('app.name') }} dapat menghapus konten apapun dari aplikasi yang dipercaya mungkin ilegal atau menyinggung.</p>

<h4>Perlindungan Data</h4>
<p>Pernyataan Privasi kami berlaku untuk data pribadi atau bahan yang dibagi bersama pada aplikasi ini. Cari tahu lebih lanjut di <a href="{{ route('additional-page.privacy') }}">sini.</a></p>

<h4>Kekayaan Intelektual</h4>
<h6>1. Konten yang disediakan oleh {{ config('app.name') }}</h6>
<p>Semua hak kekayaan intelektual, termasuk hak cipta dan merek dagang, bahan yang diterbitkan oleh atau atas nama {{ config('app.name') }} di aplikasi (misalnya teks dan gambar) yang dimiliki oleh {{ config('app.name') }} atau pemberi lisensinya.</p>
<p>Kamu mungkin mereproduksi ekstrak dari aplikasi ini untuk penggunaan pribadi Kamu sendiri (misalnya penggunaan non-komersial) asalkan Kamu menyimpan semua hak kekayaan intelektual secara utuh dan dengan rasa hormat, termasuk pemberitahuan hak cipta yang mungkin muncul di konten tersebut (misalnya @2020 {{ config('app.name') }}).</p>
<h6>2. Konten Kamu sediakan</h6>
<p>Kamu mewakili untuk {{ config('app.name') }} bahwa Kamu baik sebagai penulis konten yang Kamu kontribusikan ke aplikasi ini, ataupun bahwa Kamu memiliki hak (yaitu: telah diberi izin oleh pemegang hak) dan mampu memberikan kontribusi atas konten tersebut (misalnya gambar, video, musik) ke aplikasi.</p>
<p>Kamu setuju bahwa konten tersebut akan diperlakukan sebagai bukan rahasia dan Kamu memberikan {{ config('app.name') }} royalti, berkelanjutan, dan lisensi luas secara gratis untuk menggunakan (termasuk untuk mengungkapkan, mereproduksi, mentransmisikan, mempublikasikan, atau menyiarkan) konten yang Kamu berikan untuk tujuan yang berkaitan dengan bisnisnya.</p>
<p>Harap dicatat bahwa {{ config('app.name') }} bebas untuk memutuskan apakah menggunakan atau tidak menggunakan konten ini dan bahwa {{ config('app.name') }} mungkin telah mengembangkan edisi serupa atau telah memperoleh konten tersebut dari sumber lain, dalam hal ini semua hak kekayaan intelektual di konten ini tetap ada pada {{ config('app.name') }} dan pemberi lisensinya.</p>
<h6>3. Kewajiban</h6>
<p>Sementara {{ config('app.name') }} menggunakan semua upaya yang wajar untuk memastikan keakuratan dari bahan pada aplikasi kami dan untuk menghindari gangguan, kami tidak bertanggung jawab atas informasi yang tidak akurat, gangguan, penghentian atau peristiwa lain yang dapat menyebabkan Kamu mengalami kerugian, baik secara langsung (misalnya kegagalan komputer) atau tidak langsung (misalnya kehilangan keuntungan). Setiap ketergantungan pada bahan-bahan dalam aplikasi ini akan menjadi risiko Kamu sendiri.</p>
<p>Aplikasi ini mungkin berisi hubungan ke aplikasi-aplikasi di luar {{ config('app.name') }}. {{ config('app.name') }} tidak memiliki kontrol atas aplikasi pihak ketiga tersebut, tidak selalu mendukung mereka dan tidak bertanggung jawab untuk mereka, termasuk untuk konten, akurasi atau fungsi mereka. Sebagai akibatnya, kami mengharapkan agar Kamu berhati-hati dalam meninjau pernyataan hukum aplikasi-aplikasi pihak ketiga tersebut, termasuk menjaga diri tetap mengetahui informasi mengenai perubahan atas mereka.</p>

<h4>Kontak Kami</h4>
<p>Jika Kamu memiliki pertanyaan atau komentar tentang aplikasi, jangan ragu untuk menghubungi kami melalui (i) mobile phone di (0821) 25251123 atau (ii) telepon di (0251) 8563279 atau (ii) e-mail di info@jagonyamvp.com atau (iv) surat biasa di Jl Mayjen HR Edi Sukma No 59 Cigombong Bogor</p>

<h4>Perubahan</h4>
<p>{{ config('app.name') }} memiliki hak untuk membuat perubahan atas persyaratan penggunaan ini. Silakan lihat halaman ini pada setiap saat untuk meninjau persyaratan penggunaan dan informasi baru.</p>

<h4>Hukum dan yurisdiksi yang mengatur</h4>
<p>Aplikasi ini ditujukan untuk pengguna dari Indonesia saja. {{ config('app.name') }} tidak membuat pernyataan bahwa produk dan konten aplikasi ini sesuai atau tersedia di lokasi selain Indonesia.</p>
<p>Kamu dan {{ config('app.name') }} setuju bahwa setiap klaim atau sengketa yang berkaitan dengan aplikasi ini akan diatur oleh hukum Republik dan dibawa ke pengadilan dari Bogor di Indonesia.</p>

@endsection

@section('script')
@endsection
