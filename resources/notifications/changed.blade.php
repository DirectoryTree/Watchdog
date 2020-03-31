@component('watchdog::layout', [
    'title' => $watchdog->getNotifiableSubject(),
    'subtitle' => $watchdog->object()->dn,
])

<h5 class="text-center">Detected on {{ $watchdog->object()->updated_at->format(config('watchdog.notifications.date_format')) }}</h5>

@foreach($watchdog->modified() as $attribute)
<div class="table">
    <table>
        <thead>
            <tr>
                <th colspan="2" class="text-center">{{ $attribute }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">Before</td>
                <td class="text-center">
                    @foreach($watchdog->before()->attribute($attribute) as $values)
                        <span>{{ $values }}</span> <br/>
                    @endforeach
                </td>
            </tr>
            <tr>
                <td class="text-center">After</td>
                <td class="text-center">
                    @foreach($watchdog->after()->attribute($attribute) as $values)
                        <span>{{ $values }}</span> <br/>
                    @endforeach
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endforeach

@endcomponent
