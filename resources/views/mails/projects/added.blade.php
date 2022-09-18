@component('mail::message')
# Added to Project

Hello {{ $user->name }}!

You have been granted access to the following project: <br>
<strong>{{ $project->name }}</strong>

You can access it by clicking on the button below and login with your regular credentials.

@component('mail::button', ['url' => 'https://app.appetite.link'])
Go To Platform
@endcomponent

If you have any problems or questions, please reach us out at <a href="mailto:support@appetite.link">support@appetite.link</a>.

Thank you<br>
Appetite.link
@endcomponent
