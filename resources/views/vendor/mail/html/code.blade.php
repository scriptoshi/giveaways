@props([
'align' => 'left',
])

<table class="action" align="{{ $align }}" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="{{ $align }}">
<div class="code"><b>{{ $slot }}</b></div>
</td>
</tr>
</table>
