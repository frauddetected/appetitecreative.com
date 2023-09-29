@component('mail::message')

You have new inquiry.

<p>Name : <b>{{ $contact->name }}</b></p>
<p>Email : <b>{{ $user->email }}</b></p>
<p>Phone : <b>{{ $contact->phone }}</b></p>
<p>Plan Type : <b>{{ $contact->plan_type }}</b></p>
<p>Message : {{ $contact->message }}</p>

Thank you
<p>{{ $contact->name }}</p>

@endcomponent
