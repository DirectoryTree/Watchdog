@component('watchdog::layout', [
    'title' => $watchdog->getNotifiableSubject(),
    'subtitle' => $watchdog->object()->dn,
])

<p class="text-center">
    The above user has expired on:
</p>

<p class="text-center">
    <strong>{{ $watchdog->after()->attribute('accountexpires')->first()->format(config('watchdog.notifications.date_format')) }}</strong>
</p>

@endcomponent
