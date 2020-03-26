@component('watchdog::layout', [
    'title' => "{$watchdog->object()->name} has had members change",
    'subtitle' => $watchdog->object()->dn,
])

@component('mail::table')
| Added |
|:---:|
@forelse($watchdog->added() as $member)
| {{ $member }} |
@empty
| *None* |
@endforelse

| Removed |
|:---:|
@forelse($watchdog->removed() as $member)
| {{ $member }} |
@empty
| *None* |
@endforelse
@endcomponent

@endcomponent
