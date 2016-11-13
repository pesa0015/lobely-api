<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>read with who</title>
    
    <!-- vendor -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="/vendor/bootstrap-social/bootstrap-social.css" rel="stylesheet">

    <!-- Styles -->
    <link href="/css/app.css" rel="stylesheet">
    <link href="/css/start.css" rel="stylesheet">

    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
    <div id="app">
        <img src="/img/blooovy.png" alt="">
        <div>Hitta din partner baserat på böckerna du gillar.</div>
        <div>Det är enkelt. Logga in med Facebook bara.</div>
        <a href="/auth/facebook" class="btn btn-social-icon btn-facebook">
            <span class="fa fa-facebook"></span>
        </a>
    </div>
</body>
</html>
