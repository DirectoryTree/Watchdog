@component('watchdog::layout', [
    'title' => "{$watchdog->object()->name} has been enabled.",
    'subtitle' => $watchdog->object()->dn,
])
@endcomponent
