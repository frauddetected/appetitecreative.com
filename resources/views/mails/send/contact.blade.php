@component('mail::message')
# Welcome to Link

Hello Admin!

I hope this message finds you well. I am writing to request a subscription to <b>{{ $contact->plan_type }}</b>.

I kindly request information on the subscription packages available, including pricing, subscription duration.

Please feel free to contact me at <b>{{ $user->email }}</b> or <b>{{ $contact->phone }}</b> for any additional information you may require.

Thank you for considering my request. I am looking forward to the possibility of becoming a subscriber to <b>{{ $contact->plan_type }}</b> and benefiting from your outstanding content and services.

Thank you
{{ $contact->name }}

@endcomponent
