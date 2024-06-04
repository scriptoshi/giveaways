@component('mail::message')
{{-- Greeting --}}
@if (! empty($greeting))

# {{ $greeting }}

@else
@if ($level === 'error')

# @lang('Whoops!')

@else

# @lang('Hello!')

@endif
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
{{ $line }}

@endforeach
{{-- code --}}
@isset($codeText)
@component('mail::code',['align'=>'left'])
{{ $codeText }}
@endcomponent
@lang("The activation code will be valid for 30 minutes. Please do not share this code with anyone. Don't recognize this activity?") <span class="break-all"><a href="@route('password.request')">@lang("Please reset your password")</a href="https://t.me/sleepfinance"> @lang('and') <a>@lang('contact customer support immediately')</a></span>
@endisset

{{-- Action Button --}}
@isset($actionText)

<?php
    $color = match ($level) {
        'success', 'error' => $level,
        default => 'primary',
    };
?>

@component('mail::button', ['url' => $actionUrl, 'color' => $color])
{{ $actionText }}
@endcomponent
@endisset

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{{ $line }}

@endforeach

{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
@lang('Regards'),<br>
{{ config('app.name') }}
@endif

{{-- Subcopy --}}
@isset($actionText)
@slot('subcopy')
@lang(
"If you're having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
'into your web browser:',
[
'actionText' => $actionText,
]
) <span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
@endslot
@endisset
@endcomponent
