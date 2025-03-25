<div class= "text-start text-primary emailValidity d-none">
    Click
    <a style="font-weight: bold; text-decoration: underline !important; color: #43A4D7 !important;" href="#"
        class="validityLink">
        here</a>
    &nbsp;to resend verification email.
</div>
@if (session('successEmail'))
    <div class= "text-start text-success  ">
        Click
        <a style="font-weight: bold; text-decoration: underline !important; color: rgb(0, 128, 0) !important;"
            href="{{ route('user.verify.resend', ['email' => session('successEmail')]) }}">
            here</a>
        &nbsp;to resend verification email.
    </div>
@endif
