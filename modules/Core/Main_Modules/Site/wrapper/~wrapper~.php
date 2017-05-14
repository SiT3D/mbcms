<!DOCTYPE html>
<html>
<head>
    <meta charset="<?php echo $charset; ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{page_title}}</title>
    <meta name="Description" content="{{page_description}}">
    <meta name="Keywords" content="{{page_metakeywords}}">

    <?php foreach ($_css as $file) : ?>
        <link href="<?php echo $file ?>" type="text/css" rel="stylesheet"/>
    <?php endforeach; ?>


</head>
<body>

<?php echo_modules($modules); ?>

<?php foreach ($top_js as $file) : ?>
    <script type="application/javascript" src="<?php echo $file; ?>"></script>
<?php endforeach; ?>
<?php foreach ($bottom_js as $file) : ?>
    <script src="<?php echo $file; ?>"></script>
<?php endforeach; ?>

</body>
</html>

