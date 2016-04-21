<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 20/03/2016
 * Time: 21:57
 */

include "../../vendor/autoload.php";
?>
<html>
<head>
    <meta charset="UTF-8"/>
    <base href="http://localhost:8080/php-frame/" target="_blank">
    <meta id="viewport" name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Cache-Control" content="private">
    <meta name="author" content="Florian Moser">
    <meta name="robots" content="noindex, nofollow">
    <meta name="description" content="Layout Beispiel">
    <link href="dist/css/styles.min.css" rel="stylesheet" type="text/css">
    <title>Layout</title>
</head>
<body>
<div id="view">
    <div id="menu-left">
        <div id="menu-left" class="ui vertical menu">
            <a class="item">
                <i class="spinner loading icon"></i>
                Dashboard
            </a>
            <a class="item">
                Item 2
            </a>
            <a class="item">
                Item 3
            </a>
        </div>
    </div>
    <div id="content-right">
        <div id="header">
            <h1>Dashboard</h1>
        </div>
        <div id="center-content">
            <div class="ui container">
                <p>Hallo Welt!</p>
            </div>
        </div>
        -->
    </div>
</div>
<script type="text/javascript" src="dist/js/scripts.min.js"></script>
</body>
</html>

