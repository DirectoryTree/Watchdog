@component('watchdog::layout', [
    'title' => "{$watchdog->object()->name} has had their password changed",
    'subtitle' => $watchdog->object()->dn,
])

<p class="text-center">
    The above users password has been changed on:
</p>

<p class="text-center">
    <strong>{{ $watchdog->before()->attribute('pwdlastset')->first()->format(config('watchdog.notifications.date_format')) }}</strong>
</p>

@endcomponent
