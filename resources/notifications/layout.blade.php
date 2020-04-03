@component('mail::message')

{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
<img alt="Watchdog Logo" width="175" src="https://ldapwatchdog.com/assets/img/logo.svg"/>
@endcomponent
@endslot

<style>
    .text-center {
        text-align: center;
    }
</style>

<h1 class="text-center" style="margin-bottom: 0;">{{ $title }}</h1>

@isset($subtitle)
<h5 class="text-center" style="margin-top:2px;color:grey;">{{ $subtitle }}</h5>
@endif

{{ $slot }}

@endcomponent
