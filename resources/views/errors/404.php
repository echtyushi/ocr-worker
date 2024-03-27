<!doctype html>
<html lang="en">

<head>
    <?php echo view('partials.header')->render(); ?>

    <title>Page not found</title>
</head>

<body>

<div>Page not found.</div>

<?php if (config('app.development_mode') === true) { ?>
    <div>Tried to access: <?php echo url()->current(); ?></div>
    <div>Base URL: <?php echo url()::base_url(); ?></div>
<?php } ?>

<?php echo view('partials.footer')->render(); ?>

</body>

</html>
