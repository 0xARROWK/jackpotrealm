@component('mail::message')
<h1>Hello {{ $username }},</h1>
<p>Good news, you have been unbanned from my site ! You now have access to all the features again.</p>

@component('mail::button', ['url' => config('app.url')])
    Go to Jackpotrealm
@endcomponent

@endcomponent
