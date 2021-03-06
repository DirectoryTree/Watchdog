@component('watchdog::layout', [
    'title' => $watchdog->getNotifiableSubject(),
    'subtitle' => $watchdog->object()->dn,
])

<p class="text-center">
    The above users password has been changed on:
</p>

<p class="text-center">
    <strong>{{ $watchdog->after()->attribute('pwdlastset')->first()->format(config('watchdog.date.format')) }}</strong>
</p>

@endcomponent
