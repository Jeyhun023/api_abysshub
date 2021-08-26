@component('mail::message')
# Introduction

WELCOME DUDE

@component('mail::button', ['url' => ''])
Button Text
@endcomponent
{{$user}}
Thanks,<br>
{{ config('app.name') }}
@endcomponent
