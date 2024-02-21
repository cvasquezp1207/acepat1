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
    <link href="<?php echo base_url("app/css/style.css?".VERSION);?>" rel="stylesheet">
	
	<?php
	if(!empty($css)) {
		if(is_array($css)) {
			$css = implode("\n", $css);
		}
		echo $css;
	}
	?>
</head>

<body class="no-skin-config">
    <div>
        <div id="page-wrapper" class="gray-bg dashbard-1 no-margins">
			<?php echo $content;?>
		</div>
		<div class="loader"><i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i></div>
		<!-- modal popup -->
		<div id="modal-popup" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
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

    <!-- Mainly scripts -->
    <script src="<?php echo base_url("app/js/jquery-2.1.1.js");?>"></script>
    <script src="<?php echo base_url("app/js/jquery-ui.js");?>"></script>
    <script src="<?php echo base_url("app/js/bootstrap.min.js");?>"></script>
    <script src="<?php echo base_url("app/js/plugins/metisMenu/jquery.metisMenu.js");?>"></script>
    <script src="<?php echo base_url("app/js/plugins/slimscroll/jquery.slimscroll.min.js");?>"></script>
    
	<!-- Custom and plugin javascript -->
    <script src="<?php echo base_url("app/js/inspinia.js");?>"></script>
    <script src="<?php echo base_url("app/js/plugins/pace/pace.min.js");?>"></script>
    <script src="<?php echo base_url("app/js/plugins/wow/wow.min.js");?>"></script>
	
	<!-- Sweet alert -->
    <script src="<?php echo base_url("app/js/plugins/sweetalert/sweetalert.min.js");?>"></script>
	
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
	
	<!-- Multimdal -->
	<script src="<?php echo base_url("app/js/multimodal.js");?>"></script>
	
	<!-- Jquery Validate -->
    <script src="<?php echo base_url("app/js/plugins/validate/jquery.validate.min.js");?>"></script>
	
    <script>
		_base_url="<?php echo base_url();?>";
		_controller="<?php echo $controller;?>";
		_type_form="<?php echo $type_form; ?>";
		_current_date="<?php echo date("Y-m-d"); ?>";
		_current_user_id="<?php echo $session["idusuario"]; ?>";
		_current_user="<?php echo $session["nombres"].' '.$session["appat"]; ?>";
		_iframe=true;
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
	<script>
		/* function actualizar_iframe() {
			if(window.parent.resize_iframe && typeof window.parent.resize_iframe == "function") {
				window.parent.resize_iframe({name:window.name,height:$(document).outerHeight()+"px"});
			}
		}
		
		$(window).bind("load resize scroll", function () {
			actualizar_iframe();
		}); */
		
		if($('.fixed-button-top').length) {
			$('#page-wrapper').css('padding-top', $('.fixed-button-top').outerHeight());
			
			var cbpAnimatedHeader = (function() {
				var docElem = document.documentElement,
					header = document.querySelector( '.fixed-button-top' ),
					didScroll = false,
					changeHeaderOn = header.offsetHeight;
					// changeHeaderOn = 200;
				
				function init() {
					window.addEventListener('scroll', function( event ) {
						if( ! didScroll) {
							didScroll = true;
							setTimeout(scrollPage, 250);
						}
					}, false);
				}
				
				function scrollPage() {
					var sy = scrollY();
					if ( sy >= changeHeaderOn ) {
						$(header).addClass('fixed-scroll');
					}
					else {
						$(header).removeClass('fixed-scroll');
					}
					didScroll = false;
				}
				
				function scrollY() {
					return window.pageYOffset || docElem.scrollTop;
				}
				
				init();
			})();
		}
	</script>
</body>
</html>