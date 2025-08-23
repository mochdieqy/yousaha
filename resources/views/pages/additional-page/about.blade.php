@extends('layouts.detail')

@section('stylesheet')
@endsection

@section('title', 'About Us')
@section('back', route('home'))

@section('content')
<h3>About Us</h3>
<p>
    {{ config('app.name') }} is a simple application designed to help you record and track debts easily. With a user-friendly interface, this application allows you to record who owes you, the amount of debt, and the repayment status.
</p>
<p>
    This application is suitable for personal use or daily use, helping you keep your finances under control easily without additional complexity.
</p>
@endsection

@section('script')
@endsection
