<title>{{ $title ?? config('app.name') }}</title>

<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="csrf-token" content="{{ csrf_token() }}" />

<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml" />

<meta name="description" content="{{ config('collectos.description') }}" />
