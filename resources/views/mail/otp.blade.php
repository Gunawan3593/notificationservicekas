<x-mail::message>
# Introduction

Hallo, {{$name}}

otp verifikasi anda {{$otp}}

The body of your message.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
