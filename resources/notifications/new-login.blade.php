@component('watchdog::layout', [
    'title' => $watchdog->getNotifiableSubject(),
    'subtitle' => $watchdog->object()->dn,
])

<p class="text-center">
    A new login has occurred on the above user on:
</p>

<p class="text-center">
    <strong>{{ $watchdog->after()->attribute('lastlogon')->first()->format(config('watchdog.notifications.date_format')) }}</strong>
</p>

@endcomponent
