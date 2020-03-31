@component('watchdog::layout', [
    'title' => $watchdog->getNotifiableSubject(),
    'subtitle' => $watchdog->object()->dn,
])

@component('mail::table')
| Added Members |
|:---:|
@forelse($watchdog->added() as $member)
| {{ $member }} |
@empty
| *None* |
@endforelse
@endcomponent

@component('mail::table')
| Removed Members |
|:---:|
@forelse($watchdog->removed() as $member)
| {{ $member }} |
@empty
| *None* |
@endforelse
@endcomponent

@endcomponent
