@extends('layouts.detail')

@section('stylesheet')
@endsection

@section('title', 'Privacy Policy')
@section('back', route('home'))

@section('content')
<h3>Privacy Policy</h3>

<p>At {{ config('app.name') }}, accessible from {{ config('app.url') }} or mobile app, one of our main priorities is the privacy of our visitors. This Privacy Policy document contains types of information that is collected and recorded by {{ config('app.name') }} and how we use it.</p>
<p>If you have additional questions or require more information about our Privacy Policy, do not hesitate to contact us.</p>

<h4>Log Files</h4>
<p>{{ config('app.name') }} follows a standard procedure of using log files. These files log visitors when they visit the application. All hosting companies do this and it's a part of hosting services analytics. The information collected by log files include internet protocol (IP) addresses, browser type, Internet Service Provider (ISP), date and time stamp, referring/exit pages, and possibly the number of clicks. These are not linked to any information that is personally identifiable. The purpose of the information is for analyzing trends, administering the application, tracking users' movement on the application, and gathering demographic information.</p>

<h4>Cookies and Web Beacons</h4>
<p>Like any other application, {{ config('app.name') }} uses 'cookies'. These cookies are used to store information including visitors' preferences, and the pages on the application that the visitor accessed or visited. The information is used to optimize the users' experience by customizing our web page content based on visitors' browser type and/or other information.</p>

<h4>Data Deletion Request</h4>
<p>Contact us through (i) mobile phone at (0821) 25251123 or (ii) phone at (0251) 8563279 or (iii) e-mail at info@{{ config('app.name') }}.com or (iv) regular mail at Jl Mayjen HR Edi Sukma No 59 Cigombong Bogor if as a user you feel that data stored in our application should be deleted for some logical reasons.</p>

<h4>Third Party Privacy Policies</h4>
<p>{{ config('app.name') }}'s Privacy Policy does not apply to other advertisers or applications. Therefore, we advise you to consult the respective Privacy Policies of these third-party ad servers for more detailed information. This may include their practices and instructions about how to opt-out of certain options.</p>

<h4>Children's Information</h4>
<p>Another part of our priority is adding protection for children while using the internet. We encourage parents and guardians to observe, participate, and/or monitor and guide their online activity.</p>
<p>{{ config('app.name') }} does not knowingly collect any Personal Identifiable Information from children under the age of 13. If you think that your child provided this kind of information on our application, we strongly encourage you to contact us immediately and we will do our best efforts to promptly remove such information.</p>

<h4>Online Privacy Policy Only</h4>
<p>This Privacy Policy applies only to our online activities and is valid for visitors to our application with regards to the information that they shared and/or collect in {{ config('app.name') }}. This policy is not applicable to any information collected offline or via channels other than this application.</p>

<h4>Consent</h4>
<p>By using our application, you hereby consent to our Privacy Policy and agree to its Terms and Conditions.</p>
@endsection

@section('script')
@endsection
