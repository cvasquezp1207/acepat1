<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>..:: BIENVENIDO A <?php echo $titulo ?>::..</title>

    <link href="<?php echo base_url();?>app/componentes/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url();?>app/componentes/font-awesome/css/font-awesome.css" rel="stylesheet">

    <link href="<?php echo base_url();?>app/css/animate.css" rel="stylesheet">
    <link href="<?php echo base_url();?>app/css/style.css" rel="stylesheet">

    <!-- Estilos propios -->
    <link href="<?php echo base_url();?>app/css/estilos_menu.css" rel="stylesheet">

</head>

<body>
    <div id="wrapper">
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav metismenu" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element">
                        <?php if($this->session->idsucursal) :?>
                            <span>
                                <img alt="image" class="img-circle" src="<?php echo base_url();?>app/images/img/profile_small.jpg">
                            </span>
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                <span class="clear">
                                    <span class="block m-t-xs">
                                        <strong class="font-bold"><?php echo $this->session->usuario ?></strong>
                                    </span>
                                    <span class="text-muted text-xs block"><?php echo $this->session->rol ?> <b class="caret"></b></span>
                                </span>
                            </a>
                            <ul class="dropdown-menu animated fadeInRight m-t-xs">
                                <li><a href="profile.html">Configurar Perfil</a></li>
                                <li class="divider"></li>
                                <li><a href="<?php echo base_url()?>login/salir">Logout</a></li>
                            </ul>
                        <?php endif; ?>
                        </div>
                        <div class="logo-element">
                            <span><i class="fa fa-desktop fa-2x"></i></span>
                        </div>
                    </li>
                    <?php echo $menu_lateral; ?>
                </ul>

            </div>
        </nav>

        <div id="page-wrapper" class="gray-bg dashbard-1">
        <div class="row border-bottom">
