<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->title; ?></title>

    <?= $this->metaScripts; ?>
    <?= $this->metaStylesheets; ?>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>

<header class="navbar navbar-static-top bs-docs-nav navbar-inverse" id="top" role="banner">
    <div class="container-fluid">
        <div class="navbar-header">
            <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".bs-navbar-collapse">
                <span class="sr-only">Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="#" class="navbar-brand">IS SKOLA</a>
        </div>
        <nav class="collapse navbar-collapse bs-navbar-collapse" role="navigation">
            <ul class="nav navbar-nav">
                <li class="active">
                    <a href="<?= \Alien\Router::getRouteUrl('dashboard'); ?>"><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
                </li>
                <li>
                    <a href="<?= \Alien\Router::getRouteUrl('course'); ?>"><i class="fa fa-fw fa-file"></i> Kurzy</a>
                </li>
                <li>
                    <a href="<?= \Alien\Router::getRouteUrl('schedule'); ?>"><i class="fa fa-fw fa-calendar"></i> Rozvrh</a>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-fw fa-building"></i> Budovy
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="<?= \Alien\Router::getRouteUrl('building'); ?>"><i class="fa fa-fw fa-building"></i> Budovy</a></li>
                        <li><a href="<?= \Alien\Router::getRouteUrl('room'); ?>"><i class="fa fa-fw fa-building"></i> Miestnosti</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-fw fa-group"></i> Používatelia
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="<?= \Alien\Router::getRouteUrl('user/filter/4'); ?>"><i class="fa fa-fw fa-graduation-cap"></i> Učitelia</a>
                        </li>
                        <li>
                            <a href="<?= \Alien\Router::getRouteUrl('user/filter/3'); ?>"><i class="fa fa-fw fa-group"></i> Študenti</a>
                        </li>
                        <li>
                            <a href="<?= \Alien\Router::getRouteUrl('user'); ?>"><i class="fa fa-fw fa-user"></i> Všetci</a>
                        </li>
                    </ul>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a href="<?= \Alien\Router::getRouteUrl('logout'); ?>">Odhlásiť sa <i class="fa fa-fw fa-sign-out"></i></a>
                </li>
            </ul>
        </nav>
    </div>
</header>

<div class="container-fluid">

    <div class="row">

        <div class="col-md-2" id="sidebar">
            <div class="list-group">
                <a href="#" class="list-group-item active">
                    Možnosti
                </a>
                <a href="/" class="list-group-item">
                    <i class="fa fa-fw fa-dashboard"></i>
                    Dashboard <span class="label label-primary pull-right">2</span>
                </a>
                <a href="/?page=table" class="list-group-item">
                    <i class="fa fa-fw fa-table"></i>
                    Tabuľka
                </a>
                <a href="/?page=form" class="list-group-item">
                    <i class="fa fa-fw fa-edit"></i>
                    Formulár
                </a>
                <a href="/?page=alerts" class="list-group-item">
                    <i class="fa fa-fw fa-warning"></i>
                    Upozornenia
                </a>
                <a href="#" class="list-group-item">
                    <i class="fa fa-fw fa-sign-out"></i>
                    Odhlásiť sa
                </a>
            </div>
        </div>

        <div class="col-md-10">
            <div class="container-fluid" style="padding: 0;">

                <div class="row">
                    <div class="col-xs-12">
                        <ol class="breadcrumb">
                            <li><a href="#">Home</a></li>
                            <li><a href="#">Library</a></li>
                            <li class="active">Data</li>
                        </ol>
                    </div>
                </div>
                <!-- main holder -->
                <?= $this->mainContent; ?>
                <!-- /main holder -->
            </div>
        </div>

    </div>
</div>

<section id="notifications">
    <?= $this->notifications; ?>
</section>

</body>
</html>