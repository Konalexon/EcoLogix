<!DOCTYPE html>
<html lang="pl" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'EcoLogix')); ?></title>
    <meta name="description" content="System zarządzania logistyką i przesyłkami">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <?php echo app('Illuminate\Foundation\Vite')(['resources/js/app.js', 'resources/css/app.css']); ?>
</head>

<body class="bg-secondary-900 antialiased">
    <div id="app"></div>
</body>

</html><?php /**PATH C:\xampp\htdocs\EcoLogix\resources\views/app.blade.php ENDPATH**/ ?>