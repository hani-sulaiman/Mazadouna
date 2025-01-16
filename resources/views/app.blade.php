<!-- resources/views/app.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mazadouna</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="{{ asset('assets/css/admin/style.css') }}" id="admin-css" disabled>
    <link rel="stylesheet" href="{{ asset('assets/css/user/created-auction.css') }}" id="user-created-auction-css" disabled>
    <link rel="stylesheet" href="{{ asset('assets/css/user/favorite-auction.css') }}" id="user-favorite-auction-css" disabled>
    <link rel="stylesheet" href="{{ asset('assets/css/user/manage.css') }}" id="user-manage-css" disabled>
    <link rel="stylesheet" href="{{ asset('assets/css/user/style.css') }}" id="user-style-css" disabled>
    <link rel="stylesheet" href="{{ asset('assets/css/public/style.css') }}" id="public-style-css" disabled>
    <link rel="stylesheet" href="{{ asset('assets/css/public/responsive.css') }}" id="public-responsive-css" disabled>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite('resources/js/app.js')
</head>
<body>
    <div id="app"></div>
</body>
</html>
