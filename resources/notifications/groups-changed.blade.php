@component('watchdog::layout', [
    'title' => "{$watchdog->object()->name} has had their groups changed",
    'subtitle' => $watchdog->object()->dn,
])

@component('mail::table')
| Added Groups |
|:---:|
@forelse($watchdog->added() as $group)
| {{ $group }} |
@empty
| *None* |
@endforelse
@endcomponent

@component('mail::table')
| Removed Groups |
|:---:|
@forelse($watchdog->removed() as $group)
| {{ $group }} |
@empty
| *None* |
@endforelse
@endcomponent

@endcomponent
