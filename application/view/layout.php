<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= isset($titulo) ? $titulo : 'Ofertas' ?></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- JS -->
    <!-- please note: The JavaScript files are loaded in the footer to speed up page construction -->
    <!-- See more here: http://stackoverflow.com/q/2105327/1114320 -->

    <!-- CSS -->
    <link href="<?php echo URL; ?>css/style.css" rel="stylesheet">
</head>
<body>
    <header class="cabecera">
        <!-- logo -->
        <div class="logo">
            <?= isset($titulo) ? $titulo : 'Ofertas' ?>
        </div>
    </header>

    <!-- navigation -->
<?php $this->insert('partials/menu') ?>
<p><a href="#" class="simple-back-to-top"></a></p>
<?= $this->section('content') ?>
<footer class="pie">
    <address><b>Autor:</b> Emmanuel Valverde ramos</address>
</footer>
    <!-- jQuery, loaded in the recommended protocol-less way -->
    <!-- more http://www.paulirish.com/2010/the-protocol-relative-url/ -->
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>

    <!-- define the project's URL (to make AJAX calls possible, even when using this in sub-folders etc) -->
    <script>
        var url = "<?php echo URL; ?>";
    </script>

    <!-- our JavaScript -->
    <script src="<?php echo URL; ?>js/application.js"></script>
</body>
</html>

