@component('watchdog::layout', [
    'title' => "{$watchdog->object()->name} has had members change",
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

| Removed Members |
|:---:|
@forelse($watchdog->removed() as $member)
| {{ $member }} |
@empty
| *None* |
@endforelse
@endcomponent

@endcomponent
