<div class="row" style="border:0px solid red;">
	<form id="form-all">
		<div class="col-sm-4">
			<div class="ibox">
				<div class="ibox-title">
					<h5>
						<div class="title">
							<i class="fa fa-wrench fa-2x"></i>&nbsp;&nbsp;FILTRO
						</div>
					</h5>
				</div>
			
				<div class="ibox-content ">
					<div class="">
					<div class="form-group row">
						<div class="col-md-12">
							<label class="">Sucursal</label>
							<?php echo $sucursal;?>
						</div>
					</div>

					<div class="form-group row">
						<div class="col-md-12">
							<label class="">SISTEMA</label>
							<select name="idsistema" id="idsistema<?php echo $prefix;?>" class="form-control">
								<option value=0>Seleccione...</option>
							</select>
						</div>
					</div>

					<div class="form-group row">
						<div class="col-md-12">
							<label class="">PERFIL</label>
							<?php echo $perfil;?>
						</div>
					</div>
					
					<div class="form-group row">
						<div class="col-md-12">
							<label class="tooltip-demo">Empleados Asignados <i class="fa fa-info-circle text-muted" data-toggle="tooltip" title="Lista de empleados de la sucursal y el perfil seleccionado"></i></label>
							<select class="form-control empleados" multiple="" >
							</select>
						</div>
					</div>
					
					<div class="form-group row">
						<div class="col-md-12">
							<button type="button" id="save_acces" class="btn btn-primary btn-save btn-sm">Guardar</button>
						</div>
					</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="col-sm-8">
			<div class="ibox">
				<div class="ibox-title">
					<h5>
						<div class="title">
							<i class="fa fa-wrench fa-2x"></i>&nbsp;&nbsp;MODULOS
						</div>
					</h5>
				</div>
			
				<div class="ibox-content ">
					<div class="" id="tree"></div>
				</div>
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