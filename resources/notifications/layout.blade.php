@component('mail::message')
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
