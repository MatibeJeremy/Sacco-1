@component('mail::message')
# Change Password Request

Click on the buttton below to change password

@component('mail::button', ['url' => 'http://localhost:8080/password-reset?token='.$token])
Reset Password
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
