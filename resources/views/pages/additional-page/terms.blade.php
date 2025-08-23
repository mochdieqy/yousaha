@extends('layouts.detail')

@section('stylesheet')
@endsection

@section('title', 'Terms of Service')
@section('back', route('home'))

@section('content')
<h3>Terms of Service</h3>

<p>Welcome! We hope that you will enjoy your online experience.</p>
<p>{{ config('app.name') }} is committed to maintaining trust with our users. The terms below govern your use of this application.</p>

<h4>Acceptable Use</h4>
<p>Please feel free to explore our application.</p>
<p>However, the use of the application and materials posted to this application should not be illegal or offensive in any way. You should be careful not to:</p>
<ul>
    <li>Violate others' rights to privacy;</li>
    <li>Violate intellectual property rights;</li>
    <li>Make defamatory statements (including against {{ config('app.name') }}), related to pornography, racist or xenophobic in nature, promote hatred or incite violence or harassment;</li>
    <li>Upload files containing viruses or that may cause security issues; or</li>
    <li>Harm the integrity of the application.</li>
</ul>
<p>Please note that {{ config('app.name') }} may remove any content from the application that is believed to be illegal or offensive.</p>

<h4>Data Protection</h4>
<p>Our Privacy Statement applies to personal data or materials shared on this application. Find out more <a href="{{ route('additional-page.privacy') }}">here.</a></p>

<h4>Intellectual Property</h4>
<h6>1. Content provided by {{ config('app.name') }}</h6>
<p>All intellectual property rights, including copyright and trademarks, in materials published by or on behalf of {{ config('app.name') }} on the application (e.g., text and images) are owned by {{ config('app.name') }} or its licensors.</p>
<p>You may reproduce extracts from this application for your own personal use (e.g., non-commercial use) provided you preserve all intellectual property rights intact and with respect, including copyright notices that may appear in such content (e.g., @2020 {{ config('app.name') }}).</p>
<h6>2. Content you provide</h6>
<p>You represent to {{ config('app.name') }} that you are either the author of the content you contribute to this application, or that you have the rights (i.e., have been given permission by the rights holder) and are able to contribute such content (e.g., images, videos, music) to the application.</p>
<p>You agree that such content will be treated as non-confidential and you grant {{ config('app.name') }} a royalty-free, perpetual, and broad license to use (including to disclose, reproduce, transmit, publish, or broadcast) the content you provide for purposes related to its business.</p>
<p>Please note that {{ config('app.name') }} is free to decide whether to use or not use this content and that {{ config('app.name') }} may have developed similar editions or may have obtained such content from other sources, in which case all intellectual property rights in this content remain with {{ config('app.name') }} and its licensors.</p>
<h6>3. Liability</h6>
<p>While {{ config('app.name') }} uses all reasonable efforts to ensure the accuracy of materials on our application and to avoid disruption, we are not responsible for inaccurate information, disruption, termination, or other events that may cause you to suffer losses, either directly (e.g., computer failure) or indirectly (e.g., loss of profits). Any reliance on materials in this application will be at your own risk.</p>
<p>This application may contain links to applications outside {{ config('app.name') }}. {{ config('app.name') }} has no control over such third-party applications, does not always support them, and is not responsible for them, including for their content, accuracy, or functionality. As a result, we expect you to be careful in reviewing the legal statements of such third-party applications, including keeping yourself informed of information regarding changes to them.</p>

<h4>Contact Us</h4>
<p>If you have questions or comments about the application, please feel free to contact us through (i) mobile phone at (0821) 25251123 or (ii) phone at (0251) 8563279 or (iii) e-mail at info@jagonyamvp.com or (iv) regular mail at Jl Mayjen HR Edi Sukma No 59 Cigombong Bogor</p>

<h4>Changes</h4>
<p>{{ config('app.name') }} has the right to make changes to these terms of use. Please check this page at any time to review the terms of use and new information.</p>

<h4>Governing Law and Jurisdiction</h4>
<p>This application is intended for users from Indonesia only. {{ config('app.name') }} makes no representation that the products and content of this application are appropriate or available in locations other than Indonesia.</p>
<p>You and {{ config('app.name') }} agree that any claim or dispute related to this application will be governed by the laws of the Republic and brought to the courts of Bogor in Indonesia.</p>

@endsection

@section('script')
@endsection
