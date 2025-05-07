@extends('layouts.detail')

@section('stylesheet')
@endsection

@section('title', 'Kebijakan Privasi')
@section('back', route('home'))

@section('content')
<h3>Kebijakan Privasi</h3>

<p>Di {{ config('app.name') }}, dapat diakses dari {{ config('app.url') }} atau mobile app, salah satu prioritas utama kami adalah privasi pengunjung kami. Dokumen Kebijakan Privasi ini berisi jenis informasi yang dikumpulkan dan dicatat oleh {{ config('app.name') }} dan bagaimana kami menggunakannya.</p>
<p>Jika Kamu memiliki pertanyaan tambahan atau memerlukan informasi lebih lanjut tentang Kebijakan Privasi kami, jangan ragu untuk menghubungi kami.</p>

<h4>File Log</h4>
<p>{{ config('app.name') }} mengikuti prosedur standar menggunakan file log. File-file ini mencatat pengunjung ketika mereka mengunjungi aplikasi. Semua perusahaan hosting melakukan ini dan merupakan bagian dari analitik layanan hosting. Informasi yang dikumpulkan oleh file log termasuk alamat protokol internet (IP), jenis browser, Penyedia Layanan Internet (ISP), cap tanggal dan waktu, halaman rujukan / keluar, dan mungkin jumlah klik. Ini tidak terkait dengan informasi apa pun yang dapat diidentifikasi secara pribadi. Tujuan dari informasi tersebut adalah untuk menganalisis tren, mengelola aplikasi, melacak pergerakan pengguna di aplikasi, dan mengumpulkan informasi demografis.</p>

<h4>Cookies dan Web Beacon</h4>
<p>Seperti aplikasi lainnya, {{ config('app.name') }} menggunakan \'cookie\'. Cookies ini digunakan untuk menyimpan informasi termasuk preferensi pengunjung, dan halaman-halaman di aplikasi yang diakses atau dikunjungi pengunjung. Informasi tersebut digunakan untuk mengoptimalkan pengalaman pengguna dengan menyesuaikan konten halaman web kami berdasarkan jenis browser pengunjung dan / atau informasi lainnya.</p>

<h4>Request Penghapusan Data</h4>
<p>Hubungi kontak melalui (i) mobile phone di (0821) 25251123 atau (ii) telepon di (0251) 8563279 atau (ii) e-mail di info@{{ config('app.name') }}.com atau (iv) surat biasa di Jl Mayjen HR Edi Sukma No 59 Cigombong Bogor apabila sebagai pengguna merasa data yang tersimpan di aplikasi kami ingin dihapus karena beberapa alasan yang logis.</p>

<h4>Kebijakan Privasi Pihak Ketiga</h4>
<p>Kebijakan Privasi {{ config('app.name') }} tidak berlaku untuk pengiklan atau aplikasi lain. Karenanya, kami menyarankan Kamu untuk berkonsultasi dengan masing-masing Kebijakan Privasi dari server iklan pihak ketiga ini untuk informasi yang lebih rinci. Ini mungkin termasuk praktik dan instruksi mereka tentang cara menyisih dari opsi tertentu.</p>

<h4>Informasi Anak</h4>
<p>Bagian lain dari prioritas kami adalah menambahkan perlindungan untuk anak-anak saat menggunakan internet. Kami mendorong orang tua dan wali untuk mengamati, berpartisipasi, dan / atau memantau dan membimbing aktivitas online mereka.</p>
<p>{{ config('app.name') }} tidak dengan sengaja mengumpulkan Informasi Identifikasi Pribadi apa pun dari anak-anak di bawah usia 13 tahun. Jika menurut Kamu anak Kamu memberikan informasi semacam ini di aplikasi kami, kami sangat menganjurkan Kamu untuk segera menghubungi kami dan kami akan melakukan upaya terbaik kami untuk segera menghapusnya.</p>

<h4>Hanya Kebijakan Privasi Online</h4>
<p>Kebijakan Privasi ini hanya berlaku untuk aktivitas online kami dan berlaku untuk pengunjung aplikasi kami sehubungan dengan informasi yang mereka bagikan dan / atau kumpulkan di {{ config('app.name') }}. Kebijakan ini tidak berlaku untuk informasi apa pun yang dikumpulkan secara offline atau melalui saluran selain aplikasi ini.</p>

<h4>Persetujuan</h4>
<p>Dengan menggunakan aplikasi kami, Kamu dengan ini menyetujui Kebijakan Privasi kami dan menyetujui Syarat dan Ketentuannya.</p>
@endsection

@section('script')
@endsection
