{{-- Spam trap. Paired with \App\Http\Middleware\Honeypot on the route.
     Hidden from humans (off-screen + aria-hidden) so it stays empty;
     bots tend to fill it. The encrypted timestamp lets the middleware
     reject forms submitted faster than a human could. --}}
@php($__hpTime = \Illuminate\Support\Facades\Crypt::encryptString((string) time()))
<div style="position:absolute!important;left:-9999px!important;top:-9999px!important;height:0;width:0;overflow:hidden;" aria-hidden="true">
    <label for="website">Leave this field empty</label>
    <input type="text" name="website" id="website" value="" tabindex="-1" autocomplete="off" />
    <input type="hidden" name="_hpt" value="{{ $__hpTime }}" />
</div>
