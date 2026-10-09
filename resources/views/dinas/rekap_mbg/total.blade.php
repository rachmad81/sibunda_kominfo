<tr>
    <td colspan="3" class="text-left"><b>Total</b></td>
    @foreach ($total as $k => $t)
        <td class="text-right">
            <a href="javascript:void(0)" onclick="modal('All','All','{{ $k }}')">
                {!! $total[$k] !!}
            </a>
        </td>
    @endforeach
</tr>
