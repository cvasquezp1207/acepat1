<div class="row" style="border:0px solid red;">
	<form id="form-all">
		<?php echo $sucursal; ?>
		<?php echo $perfil; ?>
		<?php echo $sistemas; ?>
		
		<div class="col-sm-4">
			<div class="ibox">
				<div class="ibox-title">
						<h5>
							<div class="title">
								<i class="fa fa-wrench fa-2x"></i>&nbsp;&nbsp;MODULOS
							</div>
						</h5>
						
						<div class="pull-right">
							<!--<div class="ibox-tools">
								<a class="collapse-link">
									<i class="fa fa-chevron-up"></i>
								</a>
							</div>-->
							<a id="save_acces" class="cursor" title="Guardar Accesos"><i style="color:white;" class="fa fa-floppy-o fa-2x"></i></a>
						</div>
				</div>
				
				<!--<div class="ibox-content " id="lista_modulo" style="min-height:250px;"></div>-->
				<div id="tree" class="ibox-content "></div>
			</div>
		</div>		
	</form>
</div>

<div class="row">
</div>

<script>
	_base_url="<?php echo base_url();?>";_controller="<?php echo $controller;?>";
</script>
<link  href="app/css/uikit.docs.min.css" rel="stylesheet">
	
	<style>
		.checkbox_nodo{display:none;}
		li.uk-nestable-item{margin-top:1px !important;}
		.sistema{
			padding: 10px 10px;
			color: white;
			border: 1px solid #e7eaec;
			background: #1ab394;
			-webkit-border-radius: 3px;
			border-radius: 3px;
			box-sizing: border-box;
			font-size:13px;
		}
		
		.ibox-title{
			border: 1px solid #e7eaec;
			background: #1ab394;
			-webkit-border-radius: 3px;
			border-radius: 3px;
			box-sizing: border-box;
			font-size:13px;
			padding: 10px;
		}
		
		.ibox-tools {
			margin-top: 5px;
		}
		
		.title,.collapse-link{
			color: white !important;
			font-weight:bold;
		}
		
		.ibox-content {
			padding: 0px 5px 0px 5px;
		}
		
		.seleccionado{
			background:#B0BED9;
			color:black;
			text-shadow: 0px 0px 0px transparent;
		}
		
	</style>

	<link href="app/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css" rel="stylesheet">