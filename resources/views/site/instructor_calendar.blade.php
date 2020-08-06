<table class="table table-bordered table-sm">
    <thead>
        <th></th>
        <th>Mo</th>
        <th>Tu</th>
        <th>We</th>
        <th>Th</th>
        <th>Fr</th>
        <th>Sa</th>
        <th>Su</th>
    </thead>
    <tbody>
        <tr>
            <td>Morning<br>06:00-12:00</td>
            @if(isset($timespans))
            @foreach($timespans['morning'] as $timespan)
            @if($timespan >= 4)
            <td class="cell-high"></td>
            @elseif($timespan >= 2)
            <td class="cell-medium"></td>
            @elseif($timespan > 0)
            <td class="cell-low"></td>
            @else
            <td></td>
            @endif
            @endforeach
            @else
            @for($i=0;$i<7;$i++)
            <td></td>
            @endfor
            @endif
        </tr>
        <tr>
            <td>Afternoon<br>12:00-18:00</td>
            @if(isset($timespans))
            @foreach($timespans['afternoon'] as $timespan)
            @if($timespan >= 4)
            <td class="cell-high"></td>
            @elseif($timespan >= 2)
            <td class="cell-medium"></td>
            @elseif($timespan > 0)
            <td class="cell-low"></td>
            @else
            <td></td>
            @endif
            @endforeach
            @else
            @for($i=0;$i<7;$i++)
            <td></td>
            @endfor
            @endif
        </tr>
        <tr>
            <td>Evening<br>18:00-24:00</td>
            @if(isset($timespans))
            @foreach($timespans['evening'] as $timespan)
            @if($timespan >= 4)
            <td class="cell-high"></td>
            @elseif($timespan >= 2)
            <td class="cell-medium"></td>
            @elseif($timespan > 0)
            <td class="cell-low"></td>
            @else
            <td></td>
            @endif
            @endforeach
            @else
            @for($i=0;$i<7;$i++)
            <td></td>
            @endfor
            @endif
        </tr>
        <tr>
            <td>Night<br>00:00-06:00</td>
            @if(isset($timespans))
            @foreach($timespans['night'] as $timespan)
            @if($timespan >= 4)
            <td class="cell-high"></td>
            @elseif($timespan >= 2)
            <td class="cell-medium"></td>
            @elseif($timespan > 0)
            <td class="cell-low"></td>
            @else
            <td></td>
            @endif
            @endforeach
            @else
            @for($i=0;$i<7;$i++)
            <td></td>
            @endfor
            @endif
        </tr>
    </tbody>
</table>