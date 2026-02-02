<?php

/**
 * @var string|null $title
 */
?>

<title>{{ $title ?? config('app.name') }}</title>

<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="csrf-token" content="{{ csrf_token() }}" />

<link rel="icon" href="{{ asset('favicon/favicon.svg') }}" type="image/svg+xml" />

<meta name="description" content="{{ config('app.description') }}" />
<link rel="icon" type="image/png" href="{{ asset('favicon/favicon-96x96.png') }}" sizes="96x96" />
<link rel="icon" type="image/svg+xml" href="{{ asset('favicon/favicon.svg') }}" />
<link rel="shortcut icon" href="{{ asset('favicon/favicon.ico') }}" />
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-touch-icon.png') }}" />
<link rel="manifest" href="{{ asset('favicon/site.webmanifest') }}" />

<!-- Basic -->
<meta property="og:type" content="website">
<meta property="og:site_name" content="JournalOS">
<meta property="og:title" content="JournalOS – a self-hosted system to log your daily life">
<meta property="og:description" content="A privacy-first, open-source system to log your daily life without writing essays. Fully self-hosted. You own your data.">
<meta property="og:url" content="https://journalos.cloud/">

<!-- Image -->
<meta property="og:image" content="{{ public_path('images/marketing/social-sharing.png') }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="JournalOS dashboard showing daily life modules">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="JournalOS – a self-hosted system to log your daily life">
<meta name="twitter:description" content="Log your days in a structured way. No essays. No ads. Fully open source and self-hosted.">
<meta name="twitter:image" content="{{ public_path('images/marketing/social-sharing.png') }}">
