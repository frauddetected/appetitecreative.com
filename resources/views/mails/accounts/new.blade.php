@component('mail::message')
# Welcome to Link

Hello {{ $user->name }}!

A new account for accessing Appetite.Link, our proprietary PaaS platform, has been created for you.

You can access your new account by clicking on the button below and use the following details:

{{ $user->email }} <br />
{{ $password }}

@component('mail::button', ['url' => 'https://app.appetite.link'])
Go To Platform
@endcomponent

You can update your password and enable 2FA any time under your account <a href="https://app.appetite.link/user/profile">settings</a>.

If you have any problems or questions, please reach us out at <a href="mailto:support@appetite.link">support@appetite.link</a>.

Thank you<br>
Appetite.link
@endcomponent
