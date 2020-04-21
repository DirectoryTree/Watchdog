@component('watchdog::layout', [
    'title' => $watchdog->getNotifiableSubject(),
    'subtitle' => $watchdog->object()->dn,
])
<p class="text-center">
    The above users password has expired.
</p>
@endcomponent
