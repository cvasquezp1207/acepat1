<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo $title; ?></title>

    <link href="<?php echo base_url();?>app/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url();?>app/font-awesome/css/font-awesome.css" rel="stylesheet">

    <link href="<?php echo base_url();?>app/css/animate.css" rel="stylesheet">
    <link href="<?php echo base_url();?>app/css/style.css" rel="stylesheet">

</head>

<body class="gray-bg">

    <div class="loginColumns animated fadeInDown">
        <div class="row">

            <div class="col-md-6" style="text-align: center;">
                <div class="photos">
					<?php if(!empty($logo)) { ?>
                    <a target="_blank" href="javascript:void(0);"> 
                        <img alt="image" class="feed-photo" src="<?php echo base_url();?>app/img/logo/<?php echo $logo; ?>">
                    </a>
					<?php } ?>
                </div>
                <h3 style="font-weight: bold;">SISTEMA DE COOPERATIVA </h3>
            </div>
            <div class="col-md-6" style="text-align: center;">
                <div class="ibox-content">
                    <?php $atributos = array('class' => 'm-t', 'id' => 'frmlogin', 'role' => 'form'); ?>
                    <?php echo form_open('login/ingresar' , $atributos); ?>
                        <div class="form-group">
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-user"></i></span>
								<input type="text" id="usuario" name="usuario" class="form-control" value="<?php echo $usuario; ?>" placeholder="Usuario" autocomplete="off">
							</div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
								<span class="input-group-addon"><i class="fa fa-key"></i></span>
								<input type="password" id="password" name="password" class="form-control" placeholder="Contraseña" autocomplete="off">
							</div>
                        </div>
						<?php if($error) { ?>
						<div class="form-group">
							<div class="alert alert-danger"><i class="fa fa-frown-o fa-2x"></i> <?php echo $error; ?></div>
						</div>
						<?php } ?>
                        <button type="submit" class="btn btn-primary block full-width m-b">Ingresar</button>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-6">
               ACEPTAT - TOCACHE
            </div>
            <div class="col-md-6 text-right">
               <small>© 2023</small>
            </div>
        </div>
    </div>

</body>
<script type="text/javascript" src="<?php echo base_url()?>app/js/jquery-2.1.1.js" type="text/javascript"></script>
<script type="text/javascript">
$(function(){
    $("#usuario").focus();
    // $("#frmlogin input").val('');
});
</script>

</html>