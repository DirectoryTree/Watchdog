@component('mail::message')

<style>
    .text-center {
        text-align: center;
    }
</style>

<h1 class="text-center" style="margin-bottom: 0;">{{ $watchdog->getObject()->name }} has been changed</h1>
<h5 class="text-center" style="margin-top:2px;color:grey;">{{ $watchdog->getObject()->dn }}</h5>
<h5 class="text-center">Detected on {{ $watchdog->getObject()->updated_at->format(config('watchdog.notifications.date_format')) }}</h5>

@foreach($watchdog->getModifiedAttributes() as $attribute)
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
                    @foreach($watchdog->getBeforeAttribute($attribute) as $values)
                        <span>{{ $values }}</span> <br/>
                    @endforeach
                </td>
            </tr>
            <tr>
                <td class="text-center">After</td>
                <td class="text-center">
                    @foreach($watchdog->getAfterAttribute($attribute) as $values)
                        <span>{{ $values }}</span> <br/>
                    @endforeach
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endforeach

@endcomponent
