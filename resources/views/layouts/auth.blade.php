<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>

    <meta name="description" content="Mbaise Diaspora in the Americas (MDIA) is a vibrant Igbo community organization uniting Mbaise people across the Americas. We promote culture, networking, development, and community support. Join us today.">

<meta name="keywords" content="Mbaise Diaspora, MDIA, Igbo community USA, Mbaise USA, Nigerian diaspora Americas, Igbo association abroad, Mbaise organization, Nigerian community USA, cultural association, diaspora networking">

<meta name="author" content="Mbaise Diaspora in the Americas">

<meta property="og:title" content="Mbaise Diaspora in the Americas (MDIA)">
<meta property="og:description" content="Connecting Mbaise people across the Americas for culture, unity, and development.">
<meta property="og:type" content="website">
<meta property="og:url" content="https://mbaisedia.org/">
<meta property="og:image" content="https://mbaisedia.org/logo.png">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Mbaise Diaspora in the Americas (MDIA)">
<meta name="twitter:description" content="Join the Mbaise community in the Americas. Connect, grow, and support one another.">


    <link rel="icon" type="image/x-icon" href="{{ asset('assets/favicon.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
    :root {
        --tblr-primary: #13400C;
        --tblr-primary-rgb: 19, 64, 12;
        --tblr-primary-fg: #fff;
    }
    * { font-family: 'Space Grotesk', sans-serif !important; }
    </style>
</head>
<body class="d-flex flex-column">
<div class="page page-center">
    @yield('content')
</div>
<script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
</body>
</html>
