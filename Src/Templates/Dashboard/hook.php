<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 03/04/2016
 * Time: 17:01
 */

use famoser\phpFrame\Views\ViewBase;

if ($this instanceof ViewBase) {
    ?>
    <!DOCTYPE html>
    <!--suppress HtmlUnknownTarget -->
    <html>
    <head>
        <meta charset="UTF-8">
        <base href="<?= $this->getApplicationUrl(); ?>">

        <meta id="viewport" name="viewport" content="width=device-width, initial-scale=1">

        <meta name="author" content="<?= $this->getApplicationAuthor(); ?>">

        <meta name="robots" content="noindex, nofollow">
        <meta name="description" content="<?= $this->getPageDescription(); ?>">

        <link href="/css/styles.min.css" rel="stylesheet" type="text/css">

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
    <!--
    <body class='ui_charcoal' data-page='root:index'>

    <header class='header-expanded navbar navbar-fixed-top navbar-gitlab'>
        <div class='container-fluid'>
            <div class='header-content'>
                <button class='navbar-toggle' type='button'>
                    <span class='sr-only'>Toggle navigation</span>
                    <i class="fa fa-bars"></i>
                </button>
                <div class='navbar-collapse collapse'>
                    <ul class='nav navbar-nav'>
                        <li class='hidden-sm hidden-xs'>
                            <div class='search'>
                                <form class="navbar-form pull-left" action="/search" accept-charset="UTF-8"
                                      method="get"><input name="utf8" type="hidden" value="&#x2713;"/>
                                    <input type="search" name="search" id="search" placeholder="Search"
                                           class="search-input form-control" spellcheck="false" tabindex="1"/>
                                    <input type="hidden" name="group_id" id="group_id"/>
                                    <input type="hidden" name="repository_ref" id="repository_ref"/>

                                    <div class='search-autocomplete-opts hide'
                                         data-autocomplete-path='/search/autocomplete'></div>
                                </form>

                            </div>
                        </li>
                        <li class='visible-sm visible-xs'>
                            <a title="Search" data-toggle="tooltip" data-placement="bottom" data-container="body" href="/search">
                                <i class="fa fa-search"></i>
                            </a>
                        </li>
                        <li>
                            <a title="Todos" data-toggle="tooltip" data-placement="bottom" data-container="body" href="/dashboard/todos">
                                <span class='badge todos-pending-count'>
                                0
                                </span>
                            </a></li>
                        <li>
                            <a title="New project" data-toggle="tooltip" data-placement="bottom" data-container="body" href="/projects/new">
                                <i class="fa fa-plus fa-fw"></i>
                            </a>
                        </li>
                        <li>
                            <a class="logout" title="Sign out" data-toggle="tooltip" data-placement="bottom" data-container="body" rel="nofollow" data-method="delete" href="/users/sign_out">
                                <i class="fa fa-sign-out"></i>
                            </a>
                        </li>
                    </ul>
                </div>
                <h1 class='title'><a href="/dashboard/projects">Projects</a></h1>
            </div>
        </div>
    </header>


    <div class='page-sidebar-expanded page-with-sidebar'>
        <div class='nicescroll sidebar-expanded sidebar-wrapper'>
            <div class='header-logo'>
                <a id='logo'>

                </a>
                <a class="gitlab-text-container-link" title="Dashboard" id="js-shortcuts-home" href="/">
                    <div class='gitlab-text-container'>
                        <h3>GitLab</h3>
                    </div>
                </a>
            </div>
            <ul class='nav nav-sidebar'>
                <li class="home active">
                    <a title="Projects" href="/dashboard/projects">
                        <i class="fa fa-bookmark fa-fw"></i>
                        <span>
                        Projects
                        </span>
                    </a>
                </li>
                <li class="">
                    <a title="Snippets" href="/dashboard/snippets">
                        <i class="fa fa-clipboard fa-fw"></i>
                        <span>
                        Snippets
                        </span>
                    </a>
                </li>
                <li class="">
                    <a title="Help" href="/help">
                        <i class="fa fa-question-circle fa-fw"></i>
                        <span>
                        Help
                        </span>
                    </a>
                </li>
                <li class='separate-item'></li>
                <li class="">
                    <a title="Profile Settings" data-placement="bottom" href="/profile">
                        <i class="fa fa-user fa-fw"></i>
                        <span>
                        Profile Settings
                        </span>
                    </a>
                </li>
            </ul>

            <div class='collapse-nav'>
                <a class="toggle-nav-collapse" title="Open/Close" href="#"><i class="fa fa-angle-left"></i></a>

            </div>
            <a class="sidebar-user" title="Profile" href="/u/famoser"><img alt="Profile" class="avatar avatar s36"
                                                                           src="https://gitlab.com/uploads/user/avatar/291134/portr%C3%A4it_farbig_quad_191k.jpg"/>
                <div class='username'>
                    famoser
                </div>
            </a>
        </div>
        <div class='content-wrapper'>
            <div class='flash-container'>
            </div>


            <div class='container-fluid container-limited'>
                <div class='content'>
                    <div class='clearfix'>
                        <div class='top-area'>
                            <ul class='nav-links'>
                                <li class="active"><a title="Home" class="shortcuts-activity" data-placement="right"
                                                      href="/dashboard/projects">Your Projects
                                    </a></li>
                                <li class=""><a title="Starred Projects" data-placement="right"
                                                href="/dashboard/projects/starred">Starred Projects
                                    </a></li>
                                <li class=""><a title="Explore" data-placement="right" href="/explore">Explore Projects
                                    </a></li>
                            </ul>
                            <div class='nav-controls'>
                                <form class="project-filter-form" id="project-filter-form" action="https://gitlab.com/"
                                      accept-charset="UTF-8" method="get"><input name="utf8" type="hidden"
                                                                                 value="&#x2713;"/>
                                    <input type="search" name="filter_projects" id="project-filter-form-field"
                                           placeholder="Filter by name..."
                                           class="project-filter-form-field form-control input-short projects-list-filter"
                                           spellcheck="false" tabindex="2"/>
                                </form>

                                <div class='dropdown inline'>
                                    <button class='dropdown-toggle btn' data-toggle='dropdown' type='button'>
<span class='light'>
Last updated
</span>
                                        <b class='caret'></b>
                                    </button>
                                    <ul class='dropdown-menu dropdown-menu-align-right dropdown-menu-selectable'>
                                        <li class='dropdown-header'>
                                            Sort by
                                        </li>
                                        <li>
                                            <a href="/?archived=&amp;group=&amp;scope=&amp;sort=name_asc&amp;tag=&amp;visibility_level=">Name
                                            </a></li>
                                        <li>
                                            <a class="is-active"
                                               href="/?archived=&amp;group=&amp;scope=&amp;sort=updated_desc&amp;tag=&amp;visibility_level=">Last
                                                updated
                                            </a></li>
                                        <li>
                                            <a href="/?archived=&amp;group=&amp;scope=&amp;sort=updated_asc&amp;tag=&amp;visibility_level=">Oldest
                                                updated
                                            </a></li>
                                        <li>
                                            <a href="/?archived=&amp;group=&amp;scope=&amp;sort=id_desc&amp;tag=&amp;visibility_level=">Last
                                                created
                                            </a></li>
                                        <li>
                                            <a href="/?archived=&amp;group=&amp;scope=&amp;sort=id_asc&amp;tag=&amp;visibility_level=">Oldest
                                                created
                                            </a></li>
                                        <li class='divider'></li>
                                        <li>
                                            <a class="is-active"
                                               href="/?archived=&amp;group=&amp;scope=&amp;sort=updated_desc&amp;tag=&amp;visibility_level=">Hide
                                                archived projects
                                            </a></li>
                                        <li>
                                            <a href="/?archived=true&amp;group=&amp;scope=&amp;sort=updated_desc&amp;tag=&amp;visibility_level=">Show
                                                archived projects
                                            </a></li>
                                    </ul>
                                </div>

                                <a class="btn btn-new" href="/projects/new"><i class="fa fa-plus"></i>
                                    New Project
                                </a></div>
                        </div>

                        <div class='projects-list-holder'>
                            <ul class='projects-list content-list'>
                                <li class='project-row'>
                                    <a class="project" href="/JKwebGmbH/rap.ch">
                                        <div class='dash-project-avatar'>
                                            <div class="avatar project-avatar s40 identicon"
                                                 style="background-color: #FFEBEE; color: #555">R
                                            </div>
                                        </div>
<span class='project-full-name title'>
<span class='namespace-name'>
JKwebGmbH
/
</span>
<span class='project-name filter-title'>
rap.ch
</span>
</span>
                                    </a>
                                    <div class='controls'>
<span>
<i class="fa fa-star"></i>
0
</span>
<span class='visibility-icon has_tooltip' data-container='body' data-placement='left'
      title='Private - Project access must be granted explicitly to each user.'>
<i class="fa fa-lock"></i>
</span>
                                    </div>
                                    <div class='description'>
                                        <p>The rap.ch site</p>
                                    </div>
                                </li>

                                <li class='project-row'>
                                    <a class="project" href="/JKwebGmbH/JKwebCMS">
                                        <div class='dash-project-avatar'>
                                            <div class="avatar project-avatar s40 identicon"
                                                 style="background-color: #E0F2F1; color: #555">J
                                            </div>
                                        </div>
<span class='project-full-name title'>
<span class='namespace-name'>
JKwebGmbH
/
</span>
<span class='project-name filter-title'>
JKwebCMS
</span>
</span>
                                    </a>
                                    <div class='controls'>
<span>
PHP
</span>
<span>
<a class="ci-status-link ci-status-icon-skipped" title="Build skipped" data-toggle="tooltip" data-placement="auto left"
   href="/JKwebGmbH/JKwebCMS/commit/06b60b3eaa82c2e10c93506fc79a18ba4572d83a/builds"><i class="fa fa-circle fa-fw"></i></a>
</span>
<span>
<i class="fa fa-star"></i>
0
</span>
<span class='visibility-icon has_tooltip' data-container='body' data-placement='left'
      title='Private - Project access must be granted explicitly to each user.'>
<i class="fa fa-lock"></i>
</span>
                                    </div>
                                    <div class='description'>
                                        <p>The best CMS ever made!</p>
                                    </div>
                                </li>

                                <li class='project-row'>
                                    <a class="project" href="/JKwebGmbH/JKwebAdminCMS">
                                        <div class='dash-project-avatar'>
                                            <div class="avatar project-avatar s40 identicon"
                                                 style="background-color: #E0F2F1; color: #555">J
                                            </div>
                                        </div>
<span class='project-full-name title'>
<span class='namespace-name'>
JKwebGmbH
/
</span>
<span class='project-name filter-title'>
JKwebAdminCMS
</span>
</span>
                                    </a>
                                    <div class='controls'>
<span>
<i class="fa fa-star"></i>
0
</span>
<span class='visibility-icon has_tooltip' data-container='body' data-placement='left'
      title='Private - Project access must be granted explicitly to each user.'>
<i class="fa fa-lock"></i>
</span>
                                    </div>
                                    <div class='description'>
                                        <p>The internal project management tool of JKweb.</p>
                                    </div>
                                </li>

                                <li class='no-description project-row'>
                                    <a class="project" href="/famoser/eth-2016-1">
                                        <div class='dash-project-avatar'>
                                            <div class="avatar project-avatar s40 identicon"
                                                 style="background-color: #FFEBEE; color: #555">E
                                            </div>
                                        </div>
<span class='project-full-name title'>
<span class='namespace-name'>
Florian Moser
/
</span>
<span class='project-name filter-title'>
eth-2016-1
</span>
</span>
                                    </a>
                                    <div class='controls'>
<span>
<a class="ci-status-link ci-status-icon-skipped" title="Build skipped" data-toggle="tooltip" data-placement="auto left"
   href="/famoser/eth-2016-1/commit/3fca2d28b9946db009bcf69e4e3ba93a7ada37f4/builds"><i class="fa fa-circle fa-fw"></i></a>
</span>
<span>
<i class="fa fa-star"></i>
0
</span>
<span class='visibility-icon has_tooltip' data-container='body' data-placement='left'
      title='Private - Project access must be granted explicitly to each user.'>
<i class="fa fa-lock"></i>
</span>
                                    </div>
                                </li>

                                <li class='no-description project-row'>
                                    <a class="project" href="/JKwebGmbH/kochmahl.ch">
                                        <div class='dash-project-avatar'>
                                            <div class="avatar project-avatar s40 identicon"
                                                 style="background-color: #E0F2F1; color: #555">K
                                            </div>
                                        </div>
<span class='project-full-name title'>
<span class='namespace-name'>
JKwebGmbH
/
</span>
<span class='project-name filter-title'>
kochmahl.ch
</span>
</span>
                                    </a>
                                    <div class='controls'>
<span>
JavaScript
</span>
<span>
<a class="ci-status-link ci-status-icon-skipped" title="Build skipped" data-toggle="tooltip" data-placement="auto left"
   href="/JKwebGmbH/kochmahl.ch/commit/ce4ee6b2213593101a96a6ec75b87f5649ebf601/builds"><i
        class="fa fa-circle fa-fw"></i></a>
</span>
<span>
<i class="fa fa-star"></i>
0
</span>
<span class='visibility-icon has_tooltip' data-container='body' data-placement='left'
      title='Private - Project access must be granted explicitly to each user.'>
<i class="fa fa-lock"></i>
</span>
                                    </div>
                                </li>

                                <li class='no-description project-row'>
                                    <a class="project" href="/JKwebGmbH/krippenstellen.ch">
                                        <div class='dash-project-avatar'>
                                            <div class="avatar project-avatar s40 identicon"
                                                 style="background-color: #E3F2FD; color: #555">K
                                            </div>
                                        </div>
<span class='project-full-name title'>
<span class='namespace-name'>
JKwebGmbH
/
</span>
<span class='project-name filter-title'>
krippenstellen.ch
</span>
</span>
                                    </a>
                                    <div class='controls'>
<span>
PHP
</span>
<span>
<a class="ci-status-link ci-status-icon-skipped" title="Build skipped" data-toggle="tooltip" data-placement="auto left"
   href="/JKwebGmbH/krippenstellen.ch/commit/f9f2c1d8ae0f0135dc0171844085168ae271183d/builds"><i
        class="fa fa-circle fa-fw"></i></a>
</span>
<span>
<i class="fa fa-star"></i>
0
</span>
<span class='visibility-icon has_tooltip' data-container='body' data-placement='left'
      title='Private - Project access must be granted explicitly to each user.'>
<i class="fa fa-lock"></i>
</span>
                                    </div>
                                </li>

                                <li class='no-description project-row'>
                                    <a class="project" href="/famoser/florianalexandermoser-ch">
                                        <div class='dash-project-avatar'>
                                            <div class="avatar project-avatar s40 identicon"
                                                 style="background-color: #FFEBEE; color: #555">F
                                            </div>
                                        </div>
<span class='project-full-name title'>
<span class='namespace-name'>
Florian Moser
/
</span>
<span class='project-name filter-title'>
florianalexandermoser.ch
</span>
</span>
                                    </a>
                                    <div class='controls'>
<span>
HTML
</span>
<span>
<a class="ci-status-link ci-status-icon-skipped" title="Build skipped" data-toggle="tooltip" data-placement="auto left"
   href="/famoser/florianalexandermoser-ch/commit/0125280a80b09a2959533e35e3b83ffd7ae23097/builds"><i
        class="fa fa-circle fa-fw"></i></a>
</span>
<span>
<i class="fa fa-star"></i>
0
</span>
<span class='visibility-icon has_tooltip' data-container='body' data-placement='left'
      title='Private - Project access must be granted explicitly to each user.'>
<i class="fa fa-lock"></i>
</span>
                                    </div>
                                </li>

                                <li class='no-description project-row'>
                                    <a class="project" href="/JKwebGmbH/gitworkflow">
                                        <div class='dash-project-avatar'>
                                            <div class="avatar project-avatar s40 identicon"
                                                 style="background-color: #E0F2F1; color: #555">G
                                            </div>
                                        </div>
<span class='project-full-name title'>
<span class='namespace-name'>
JKwebGmbH
/
</span>
<span class='project-name filter-title'>
gitworkflow
</span>
</span>
                                    </a>
                                    <div class='controls'>
<span>
PLpgSQL
</span>
<span>
<a class="ci-status-link ci-status-icon-skipped" title="Build skipped" data-toggle="tooltip" data-placement="auto left"
   href="/JKwebGmbH/gitworkflow/commit/3ab4a668817836f4053a8b8285a70442a21f414e/builds"><i
        class="fa fa-circle fa-fw"></i></a>
</span>
<span>
<i class="fa fa-star"></i>
0
</span>
<span class='visibility-icon has_tooltip' data-container='body' data-placement='left'
      title='Private - Project access must be granted explicitly to each user.'>
<i class="fa fa-lock"></i>
</span>
                                    </div>
                                </li>

                                <li class='project-row'>
                                    <a class="project" href="/JKwebGmbH/mycamper.ch">
                                        <div class='dash-project-avatar'>
                                            <div class="avatar project-avatar s40 identicon"
                                                 style="background-color: #FBE9E7; color: #555">M
                                            </div>
                                        </div>
<span class='project-full-name title'>
<span class='namespace-name'>
JKwebGmbH
/
</span>
<span class='project-name filter-title'>
mycamper.ch
</span>
</span>
                                    </a>
                                    <div class='controls'>
<span>
PHP
</span>
<span>
<a class="ci-status-link ci-status-icon-skipped" title="Build skipped" data-toggle="tooltip" data-placement="auto left"
   href="/JKwebGmbH/mycamper.ch/commit/a808412ba962660a3ad511e1595967d311aaf0fc/builds"><i
        class="fa fa-circle fa-fw"></i></a>
</span>
<span>
<i class="fa fa-star"></i>
0
</span>
<span class='visibility-icon has_tooltip' data-container='body' data-placement='left'
      title='Private - Project access must be granted explicitly to each user.'>
<i class="fa fa-lock"></i>
</span>
                                    </div>
                                    <div class='description'>
                                        <p>The mycamper.ch Site</p>
                                    </div>
                                </li>

                                <li class='no-description project-row'>
                                    <a class="project" href="/famoser/programs">
                                        <div class='dash-project-avatar'>
                                            <div class="avatar project-avatar s40 identicon"
                                                 style="background-color: #E8EAF6; color: #555">P
                                            </div>
                                        </div>
<span class='project-full-name title'>
<span class='namespace-name'>
Florian Moser
/
</span>
<span class='project-name filter-title'>
programs
</span>
</span>
                                    </a>
                                    <div class='controls'>
<span>
Eiffel
</span>
<span>
<a class="ci-status-link ci-status-icon-skipped" title="Build skipped" data-toggle="tooltip" data-placement="auto left"
   href="/famoser/programs/commit/daabe4396bb68718a175b127ccd5bc82e2c13c49/builds"><i
        class="fa fa-circle fa-fw"></i></a>
</span>
<span>
<i class="fa fa-star"></i>
0
</span>
<span class='visibility-icon has_tooltip' data-container='body' data-placement='left'
      title='Private - Project access must be granted explicitly to each user.'>
<i class="fa fa-lock"></i>
</span>
                                    </div>
                                </li>

                                <li class='no-description project-row'>
                                    <a class="project" href="/famoser/publish-famoser-ch">
                                        <div class='dash-project-avatar'>
                                            <div class="avatar project-avatar s40 identicon"
                                                 style="background-color: #E0F2F1; color: #555">P
                                            </div>
                                        </div>
<span class='project-full-name title'>
<span class='namespace-name'>
Florian Moser
/
</span>
<span class='project-name filter-title'>
publish.famoser.ch
</span>
</span>
                                    </a>
                                    <div class='controls'>
<span>
HTML
</span>
<span>
<a class="ci-status-link ci-status-icon-skipped" title="Build skipped" data-toggle="tooltip" data-placement="auto left"
   href="/famoser/publish-famoser-ch/commit/71451cffe1ade78a5df15bc8db6603793f3f844e/builds"><i
        class="fa fa-circle fa-fw"></i></a>
</span>
<span>
<i class="fa fa-star"></i>
0
</span>
<span class='visibility-icon has_tooltip' data-container='body' data-placement='left'
      title='Private - Project access must be granted explicitly to each user.'>
<i class="fa fa-lock"></i>
</span>
                                    </div>
                                </li>

                                <li class='project-row'>
                                    <a class="project" href="/JKwebGmbH/metu.ch">
                                        <div class='dash-project-avatar'>
                                            <div class="avatar project-avatar s40 identicon"
                                                 style="background-color: #FBE9E7; color: #555">M
                                            </div>
                                        </div>
<span class='project-full-name title'>
<span class='namespace-name'>
JKwebGmbH
/
</span>
<span class='project-name filter-title'>
metu.ch
</span>
</span>
                                    </a>
                                    <div class='controls'>
<span>
PHP
</span>
<span>
<a class="ci-status-link ci-status-icon-skipped" title="Build skipped" data-toggle="tooltip" data-placement="auto left"
   href="/JKwebGmbH/metu.ch/commit/296e4504652f9efe463b8651ede0094df8b4dccb/builds"><i
        class="fa fa-circle fa-fw"></i></a>
</span>
<span>
<i class="fa fa-star"></i>
0
</span>
<span class='visibility-icon has_tooltip' data-container='body' data-placement='left'
      title='Private - Project access must be granted explicitly to each user.'>
<i class="fa fa-lock"></i>
</span>
                                    </div>
                                    <div class='description'>
                                        <p>metu.ch Web Project</p>
                                    </div>
                                </li>

                                <li class='no-description project-row'>
                                    <a class="project" href="/famoser/jkweb">
                                        <div class='dash-project-avatar'>
                                            <div class="avatar project-avatar s40 identicon"
                                                 style="background-color: #E8EAF6; color: #555">J
                                            </div>
                                        </div>
<span class='project-full-name title'>
<span class='namespace-name'>
Florian Moser
/
</span>
<span class='project-name filter-title'>
jkweb
</span>
</span>
                                    </a>
                                    <div class='controls'>
<span>
PHP
</span>
<span>
<i class="fa fa-star"></i>
0
</span>
<span class='visibility-icon has_tooltip' data-container='body' data-placement='left'
      title='Private - Project access must be granted explicitly to each user.'>
<i class="fa fa-lock"></i>
</span>
                                    </div>
                                </li>

                                <li class='no-description project-row'>
                                    <a class="project" href="/famoser/cranio">
                                        <div class='dash-project-avatar'>
                                            <div class="avatar project-avatar s40 identicon"
                                                 style="background-color: #E0F2F1; color: #555">C
                                            </div>
                                        </div>
<span class='project-full-name title'>
<span class='namespace-name'>
Florian Moser
/
</span>
<span class='project-name filter-title'>
cranio
</span>
</span>
                                    </a>
                                    <div class='controls'>
<span>
<i class="fa fa-star"></i>
0
</span>
<span class='visibility-icon has_tooltip' data-container='body' data-placement='left'
      title='Private - Project access must be granted explicitly to each user.'>
<i class="fa fa-lock"></i>
</span>
                                    </div>
                                </li>

                                <li class='no-description project-row'>
                                    <a class="project" href="/famoser/personal">
                                        <div class='dash-project-avatar'>
                                            <div class="avatar project-avatar s40 identicon"
                                                 style="background-color: #E3F2FD; color: #555">P
                                            </div>
                                        </div>
<span class='project-full-name title'>
<span class='namespace-name'>
Florian Moser
/
</span>
<span class='project-name filter-title'>
personal
</span>
</span>
                                    </a>
                                    <div class='controls'>
<span>
<i class="fa fa-star"></i>
0
</span>
<span class='visibility-icon has_tooltip' data-container='body' data-placement='left'
      title='Private - Project access must be granted explicitly to each user.'>
<i class="fa fa-lock"></i>
</span>
                                    </div>
                                </li>

                                <li class='no-description project-row'>
                                    <a class="project" href="/famoser/eth-2015-2">
                                        <div class='dash-project-avatar'>
                                            <div class="avatar project-avatar s40 identicon"
                                                 style="background-color: #FBE9E7; color: #555">E
                                            </div>
                                        </div>
<span class='project-full-name title'>
<span class='namespace-name'>
Florian Moser
/
</span>
<span class='project-name filter-title'>
eth-2015-2
</span>
</span>
                                    </a>
                                    <div class='controls'>
<span>
<i class="fa fa-star"></i>
0
</span>
<span class='visibility-icon has_tooltip' data-container='body' data-placement='left'
      title='Private - Project access must be granted explicitly to each user.'>
<i class="fa fa-lock"></i>
</span>
                                    </div>
                                </li>

                                <li class='no-description project-row'>
                                    <a class="project" href="/famoser/kleintierpraxis-baselwest-ch">
                                        <div class='dash-project-avatar'>
                                            <div class="avatar project-avatar s40 identicon"
                                                 style="background-color: #FFEBEE; color: #555">K
                                            </div>
                                        </div>
<span class='project-full-name title'>
<span class='namespace-name'>
Florian Moser
/
</span>
<span class='project-name filter-title'>
kleintierpraxis-baselwest.ch
</span>
</span>
                                    </a>
                                    <div class='controls'>
<span>
<i class="fa fa-star"></i>
0
</span>
<span class='visibility-icon has_tooltip' data-container='body' data-placement='left'
      title='Private - Project access must be granted explicitly to each user.'>
<i class="fa fa-lock"></i>
</span>
                                    </div>
                                </li>

                                <li class='no-description project-row'>
                                    <a class="project" href="/famoser/famoserrechnungexample">
                                        <div class='dash-project-avatar'>
                                            <div class="avatar project-avatar s40 identicon"
                                                 style="background-color: #FFEBEE; color: #555">F
                                            </div>
                                        </div>
<span class='project-full-name title'>
<span class='namespace-name'>
Florian Moser
/
</span>
<span class='project-name filter-title'>
FamoserRechnungExample
</span>
</span>
                                    </a>
                                    <div class='controls'>
<span>
<i class="fa fa-star"></i>
0
</span>
<span class='visibility-icon has_tooltip' data-container='body' data-placement='left'
      title='Private - Project access must be granted explicitly to each user.'>
<i class="fa fa-lock"></i>
</span>
                                    </div>
                                </li>

                                <li class='no-description project-row'>
                                    <a class="project" href="/famoser/famoserreadv11">
                                        <div class='dash-project-avatar'>
                                            <div class="avatar project-avatar s40 identicon"
                                                 style="background-color: #E3F2FD; color: #555">F
                                            </div>
                                        </div>
<span class='project-full-name title'>
<span class='namespace-name'>
Florian Moser
/
</span>
<span class='project-name filter-title'>
FamoserReadV11
</span>
</span>
                                    </a>
                                    <div class='controls'>
<span>
<i class="fa fa-star"></i>
0
</span>
<span class='visibility-icon has_tooltip' data-container='body' data-placement='left'
      title='Private - Project access must be granted explicitly to each user.'>
<i class="fa fa-lock"></i>
</span>
                                    </div>
                                </li>

                                <li class='no-description project-row'>
                                    <a class="project" href="/famoser/knbu-ch">
                                        <div class='dash-project-avatar'>
                                            <div class="avatar project-avatar s40 identicon"
                                                 style="background-color: #FBE9E7; color: #555">K
                                            </div>
                                        </div>
<span class='project-full-name title'>
<span class='namespace-name'>
Florian Moser
/
</span>
<span class='project-name filter-title'>
knbu.ch
</span>
</span>
                                    </a>
                                    <div class='controls'>
<span>
<i class="fa fa-star"></i>
0
</span>
<span class='visibility-icon has_tooltip' data-container='body' data-placement='left'
      title='Private - Project access must be granted explicitly to each user.'>
<i class="fa fa-lock"></i>
</span>
                                    </div>
                                </li>

                            </ul>
                            <div class='gl-pagination'>
                                <ul class='pagination clearfix'>
                                    <li class='prev disabled'>
                                        <span>Prev</span>
                                    </li>

                                    <li class='page active'>
                                        <a href="/">1</a>
                                    </li>

                                    <li class='page'>
                                        <a rel="next" href="/?page=2">2</a>
                                    </li>

                                    <li class='page'>
                                        <a href="/?page=3">3</a>
                                    </li>

                                    <li class='next'>
                                        <a rel="next" href="/?page=2">Next</a>
                                    </li>


                                </ul>
                            </div>

                        </div>
                        <script>
                            ProjectsList.init();
                        </script>


                    </div>
                </div>
            </div>
        </div>
    </div>
    </body>

-->
    </html>
<?php } ?>