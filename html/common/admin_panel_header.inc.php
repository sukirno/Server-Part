<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title><?php echo $page_title; ?></title>
    <meta name="google-site-verification" content="" />
    <meta name='yandex-verification' content='' />
    <meta name="msvalidate.01" content="" />
    <meta property="og:site_name" content="<?php echo APP_TITLE; ?>">
    <meta property="og:title" content="<?php echo $page_title; ?>">
    <meta charset="utf-8">
    <meta name="description" content="">
    <link href="/img/favicon.png" rel="shortcut icon" type="image/x-icon">

    <script type="text/javascript" src="/js/jquery-2.1.1.js"></script>
    <script type="text/javascript" src="/js/materialize.min.js"></script>

    <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="/css/materialize.css"  media="screen,projection"/>

    <?php
        foreach($css_files as $css): ?>
        <link rel="stylesheet" href="/css/<?php echo $css."?x=70"; ?>" type="text/css" media="screen">
    <?php
        endforeach;
    ?>

    <link rel="stylesheet" href="/css/colorbox.css" type="text/css" media="screen">
</head>