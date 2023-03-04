@component('mail::message')
<h1>Hello {{ $username }},</h1>
<p>This email serves as a banning notification : due to inappropriate behavior, your access to the website and your account have been restricted.</p>

Thanks,<br>
{{ config('app.name') }}<br>
@endcomponent
