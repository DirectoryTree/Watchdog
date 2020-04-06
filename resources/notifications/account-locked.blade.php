@component('watchdog::layout', [
    'title' => $watchdog->getNotifiableSubject(),
    'subtitle' => $watchdog->object()->dn,
])

<p class="text-center">
    The above user has been locked out on:
</p>

<p class="text-center">
    <strong>{{ $watchdog->after()->attribute('lockouttime')->first()->format(config('watchdog.date.format')) }}</strong>
</p>

@endcomponent
