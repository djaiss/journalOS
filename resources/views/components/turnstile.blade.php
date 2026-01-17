@if (config('turnstile.enabled'))
  <div class="cf-turnstile" data-sitekey="{{ config('turnstile.sitekey') }}" {{ $attributes }}></div>
@endif
