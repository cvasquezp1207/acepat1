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
				
				<div class="ibox-content " id="lista_modulo" style="min-height:250px;"></div>
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
		/*********** STYLE FOR CHECK TEST ***************/
		.checkbox_ {
			margin: 0 0 1em 2em;
		}
		.checkbox_ .tag {
		  color: #595959;
		  display: block;
		  float: left;
		  font-weight: bold;
		  position: relative;
		  width: 120px;
		}
		.checkbox_ label {
		  display: inline;
		}
		.checkbox_
			.input-assumpte {
		  display: none;
		}
		.input-assumpte + label {
		  -webkit-appearance: none;
		  background-color: #fafafa;
		  border: 1px solid #cacece;
		  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05), inset 0px -15px 10px -12px rgba(0, 0, 0, 0.05);
		  padding: 9px;
		  display: inline-block;
		  position: relative;
		  overflow: hidden;
		}
		.input-assumpte:checked + label:after {
		  width: 100%;
		  height: 100%;
		  content: "";
		  background-color: #008FD5;
		  left: 0px;
		  position: absolute;
		  top: 0px;
		}
		/*********** STYLE FOR CHECK TEST ***************/
		.checkbox, .radio {
			margin-top: 3px;
			margin-bottom: 2px;
		}
		
		.fa-check-square-o{font-size:18px;}
		
		.dimension_tabla{width:100%;}
		.td_bottom{font-size:12px;}
		
		.menu_padre{
			display:inline-block;
			border:0px solid black;
			width:85%;
		}
		
		.grupo{background-position: -292px -14px;background-repeat: repeat-y;}
		
		.presentacion:hover{
			background:#b2e7ff
		}
		
		li{
			text-decoration: none;
			display: block;
		}
		
		.li_old{
			background:#f3f3f4;
		}
		
		.nada{
			width: 24px;
			height: 24px;
			line-height: 24px;
			border:0px solid red;
		}
		
		.nada-icon{
			background-image: url(app/css/plugins/jsTree/32px.png);
		}
		
		.hijo-close{
			background-position: -100px -4px;
			cursor:pointer;
		}
		
		.hijo-open{
			background-position: -132px -4px;
			cursor:pointer;
		}
		
		.hijito{
			background-position: -68px -4px;
		}
		
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