@component('watchdog::layout', [
    'title' => $watchdog->getNotifiableSubject(),
    'subtitle' => $watchdog->object()->dn,
])

<h5 class="text-center">{{ \Illuminate\Support\Str::finish($watchdog->getNotifiableSubject(), '.') }}</h5>
@endcomponent
