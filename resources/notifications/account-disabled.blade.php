@component('watchdog::layout', [
    'title' => "{$watchdog->object()->name} has been disabled.",
    'subtitle' => $watchdog->object()->dn,
])
@endcomponent
