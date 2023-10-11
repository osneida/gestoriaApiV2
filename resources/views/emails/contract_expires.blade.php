@component('mail::message')
# {{ __($subject) }}

{{ __($message) }}

<a href="{{ env('CLIENT_URL') }}">Accedir a l'aplicatiu</a><br>

{{ __("Gràcies") }},<br>
{{ config('app.name') }}
@endcomponent
