<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo $page_title; ?></title>

    <link href="<?php echo base_url("app/css/bootstrap.min.css");?>" rel="stylesheet">
    <link href="<?php echo base_url("app/font-awesome/css/font-awesome.css");?>" rel="stylesheet">
	
	<!-- Sweet Alert -->
    <link href="<?php echo base_url("app/css/plugins/sweetalert/sweetalert.css");?>" rel="stylesheet">
	
	<!-- Toastr style -->
    <link href="<?php echo base_url("app/css/plugins/toastr/toastr.min.css");?>" rel="stylesheet">
	
	<!-- Datatables -->
    <link href="<?php echo base_url("app/css/plugins/dataTables/dataTables.bootstrap.css");?>" rel="stylesheet">
    <link href="<?php echo base_url("app/css/plugins/dataTables/dataTables.responsive.css");?>" rel="stylesheet">
    <link href="<?php echo base_url("app/css/plugins/dataTables/dataTables.tableTools.min.css");?>" rel="stylesheet">
	
	<!-- jQueryUI Autocomplete -->
    <link href="<?php echo base_url("app/css/plugins/jQueryUI/jquery-ui-autocomplete.min.css");?>" rel="stylesheet">

    <link href="<?php echo base_url("app/css/animate.css");?>" rel="stylesheet">
    <!--<link href="<?php // echo base_url("app/css/plugins/codemirror/codemirror.css");?>" rel="stylesheet">
    <link href="<?php // echo base_url("app/css/plugins/codemirror/ambiance.css");?>" rel="stylesheet">-->
    <link href="<?php echo base_url("app/css/style.css?".VERSION);?>" rel="stylesheet">
    <link href="<?php echo base_url("app/css/skins.css");?>" rel="stylesheet">
	
	<?php
	if(!empty($css)) {
		if(is_array($css)) {
			$css = implode("\n", $css);
		}
		echo $css;
	}
	
	?>
</head>

<body class="fixed-sidebar no-skin-config full-height-layout toolbar-min-padding">
    <div id="wrapper">
		<?php echo $menu; ?>
        <div id="page-wrapper" class="gray-bg">
			<?php echo $toolbar; ?>
			<?php echo $content;?>
			<div class="footer">
				<!--<div class="pull-right">
					<strong>Free</strong>
				</div>-->
				<div>
					<strong>Tarapoto</strong> &copy; 2023
				</div>
			</div>
		</div>
		<!-- ventana chat -->
    <!--    <div class="small-chat-box fadeInRight animated">

            <div class="heading" draggable="true">
                <small class="chat-date pull-right">
                    02.19.2015
                </small>
                Small chat
            </div>

            <div class="content">

                <div class="left">
                    <div class="author-name">
                        Monica Jackson <small class="chat-date">
                        10:02 am
                    </small>
                    </div>
                    <div class="chat-message active">
                        Lorem Ipsum is simply dummy text input.
                    </div>

                </div>
                <div class="right">
                    <div class="author-name">
                        Mick Smith
                        <small class="chat-date">
                            11:24 am
                        </small>
                    </div>
                    <div class="chat-message">
                        Lorem Ipsum is simpl.
                    </div>
                </div>
                <div class="left">
                    <div class="author-name">
                        Alice Novak
                        <small class="chat-date">
                            08:45 pm
                        </small>
                    </div>
                    <div class="chat-message active">
                        Check this stock char.
                    </div>
                </div>
                <div class="right">
                    <div class="author-name">
                        Anna Lamson
                        <small class="chat-date">
                            11:24 am
                        </small>
                    </div>
                    <div class="chat-message">
                        The standard chunk of Lorem Ipsum
                    </div>
                </div>
                <div class="left">
                    <div class="author-name">
                        Mick Lane
                        <small class="chat-date">
                            08:45 pm
                        </small>
                    </div>
                    <div class="chat-message active">
                        I belive that. Lorem Ipsum is simply dummy text.
                    </div>
                </div>


            </div>
            <div class="form-chat">
                <div class="input-group input-group-sm"><input type="text" class="form-control"> <span class="input-group-btn"> <button class="btn btn-primary" type="button">Send
                </button> </span></div>
            </div>

        </div>-->
		<!-- fin ventana chat -->
		<!-- notificacion chat -->
        <!--
		<div id="small-chat">
            <span class="badge badge-warning pull-right">5</span>
            <a class="open-small-chat">
                <i class="fa fa-comments"></i>
            </a>
        </div>
		-->
        <?php echo $panel_chat; ?>
		<!-- fin notificacion chat -->
		<!-- inicio setting -->
        <?php echo $panel_config; ?>
		<!-- fin setting -->
		<!-- form modal escoger sucursal -->
		<?php echo $modal_sucursal; ?>
		<!-- fin form escoger sucursal -->
		<!-- modal popup -->
		<div id="modal-popup" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<!--<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>-->
						<h4 class="modal-title"></h4>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<p class="modal-desc"></p>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="table-responsive"></div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default close-modal-popup">Cerrar</button>
						<button type="button" class="btn btn-primary select-modal-popup">Seleccionar</button>
					</div>
				</div>
			</div>
		</div>
		<!-- fin modal popup -->
    </div>
	
	<!-- Logout Notification Box -->
    <div id="logout">
        <div class="logout-message">
            <!--<img class="img-circle img-logout" src="img/profile-pic.jpg" alt="">-->
            <h3>
                <i class="fa fa-sign-out text-green"></i> &iquest;Desea salir?
            </h3>
            <p>Seleccione "Salir" si esta preparado para<br> finalizar su actual sesi&oacute;n.</p>
            <ul class="list-inline">
                <li>
                    <a href="<?php echo base_url("login/salir");?>" class="btn btn-primary">Salir</a>
                </li>
                <li>
                    <button class="logout_close btn btn-danger">Cancel</button>
                </li>
            </ul>
        </div>
    </div>
    <!-- /#logout -->

    <!-- Mainly scripts -->
    <script src="<?php echo base_url("app/js/jquery-2.1.1.js");?>"></script>
    <script src="<?php echo base_url("app/js/bootstrap.min.js");?>"></script>
    <script src="<?php echo base_url("app/js/plugins/metisMenu/jquery.metisMenu.js");?>"></script>
    <script src="<?php echo base_url("app/js/plugins/slimscroll/jquery.slimscroll_orig.min.js");?>"></script>
    
	<!-- Custom and plugin javascript -->
    <script src="<?php echo base_url("app/js/inspinia.js");?>"></script>
    <script src="<?php echo base_url("app/js/plugins/pace/pace.min.js");?>"></script>
	
	<!-- Sweet alert -->
    <!--<script src="<?php echo base_url("app/js/plugins/sweetalert/sweetalert.min.js");?>"></script>-->
	
	<!-- Toastr -->
    <script src="<?php echo base_url("app/js/plugins/toastr/toastr.min.js");?>"></script>
	
	<!-- Popupoverlay -->
    <script src="<?php echo base_url("app/js/plugins/popupoverlay/jquery.popupoverlay.js");?>"></script>
	
	<!-- Datatables -->
	<script src="<?php echo base_url("app/js/plugins/dataTables/jquery.dataTables.js");?>"></script>
	<script src="<?php echo base_url("app/js/plugins/dataTables/dataTables.bootstrap.js");?>"></script>
	<script src="<?php echo base_url("app/js/plugins/dataTables/dataTables.responsive.js");?>"></script>
	<script src="<?php echo base_url("app/js/plugins/dataTables/dataTables.tableTools.min.js");?>"></script>
	
	<!-- jQueryUI Autocomplete -->
	<script src="<?php echo base_url("app/js/plugins/jquery-ui/jquery-ui-autocomplete.min.js");?>"></script>
	
	<!-- Jquery Validate -->
    <script src="<?php echo base_url("app/js/plugins/validate/jquery.validate.min.js");?>"></script>
	
    <script>
		_base_url="<?php echo base_url();?>";
		_controller="<?php echo $controller;?>";
		_type_form="<?php echo $type_form; ?>";
		_current_date="<?php echo date("Y-m-d"); ?>";
		_current_user_id="<?php echo $session["idusuario"]; ?>";
		_current_user="<?php echo $session["nombres"].' '.$session["appat"]; ?>";
	</script>
    <script src="<?php echo base_url("app/js/required.js");?>"></script>
    <script src="<?php echo base_url("app/js/default.js?".VERSION);?>"></script>
	<?php
	if(!empty($js)) {
		if(is_array($js)) {
			$js = implode("\n", $js);
		}
		echo $js;
	}
	?>
</body>
</html>