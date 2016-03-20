<?php
/**
 * Created by PhpStorm.
 * User: Florian Moser
 * Date: 01.07.2015
 * Time: 19:25
 */

use famoser\phpFrame\Views\ViewBase;

if ($this instanceof ViewBase) {
    ?>
    <!DOCTYPE html>
    <html>
<head>
    <meta charset="UTF-8">
    <base href="<?= $this->getApplicationUrl(); ?>">

    <meta id="viewport" name="viewport" content="width=device-width, initial-scale=1">

    <meta name="author" content="<?= $this->getPageAuthor(); ?>">

    <meta name="robots" content="noindex, nofollow">
    <meta name="description" content="<?= $this->getPageDescription(); ?>">

    <link href="/css/styles.css" rel="stylesheet" type="text/css">

    <!-- generate at http://www.favicon-generator.org/ -->
    <link rel="apple-touch-icon" sizes="57x57" href="/img/favicons/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/img/favicons/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/img/favicons/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/img/favicons/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/img/favicons/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/img/favicons/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/img/favicons/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/img/favicons/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/img/favicons/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/img/favicons/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/img/favicons/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/img/favicons/favicon-16x16.png">
    <link rel="manifest" href="/img/favicons/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/img/favicons/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">

    <script type="text/javascript">
        /* Fix IE Mobile Responsive Design */
        !function () {
            if ("-ms-user-select" in document.documentElement.style && navigator.userAgent.match(/IEMobile\/10\.0/)) {
                var e = document.createElement("style");
                e.appendChild(document.createTextNode("@-ms-viewport{width:auto!important}"));
                document.getElementsByTagName("head")[0].appendChild(e)
            }
        }();
        window.onload = function () {
            if (screen.width <= 400) {
                var t = document.getElementById("viewport");
                t.setAttribute("content", "width=400")
            }
        };
    </script>


    <title><?= $this->getPageTitle(); ?></title>
</head>

<?php } ?>