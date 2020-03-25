@component('watchdog::layout', [
    'title' => "{$watchdog->object()->name} has been logged into",
    'subtitle' => $watchdog->object()->dn,
])

<p class="text-center">
    This is a notification that a new login has occurred on <strong></strong>
</p>

@endcomponent
