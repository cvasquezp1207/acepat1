<?php echo $grilla;?>

<div id="modal-form" class="modal fade" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header"> <h4 class="modal-title">ASIGNAR USUARIO</h4>	</div>
			<form id="form-user" >
				<input type="hidden" name="idusuario_firts" id="idusuario" >
				<input type="file" 	 id="file" 		 name="file"style="display: none;" onchange='leerarchivobin(this)' />
				<input type="hidden" id="avatar" 	 name="avatar" 		value="anonimo.png"/>
				<input type="hidden" id="avatar_new" name="avatar_new" 	value="anonimo.png"/>
				<input type="hidden" id="clave" 	 name="clave"  >
				<input type="hidden" name="clave_past" id="clave_past" >
				
				<div class="modal-body">
					<div class="row">
						<div class="col-md-5">
							<div class="form-group">
								<label for="" class="requerido">Empleado</label>
								<input type="text" class="form-control" readonly id="nombres_data">
							</div>

							<div class="form-group">
								<label for="" class="requerido">DNI</label>
								<input type="text" class="form-control" id="dni" readonly>
							</div>
							
							<!--<div class="form-group">
								<label for="" class="">Avatar</label><br>
								<div id="load_photo" class="app-img-temp img-thumbnail">
									<?php
										$n_logo = 'app/img/usuario/anonimo.png';
										if(!empty($idusuario))
											$n_logo = '../app/img/usuario/'.$avatar;
									?>
									<img id="photo" src="<?php echo $n_logo;?>" class="img-responsive img-thumbnail" style="background:#f3f3f4;height:170px;"/>
								</div>
							</div>-->

							<div class="form-group">
								<label for="" class="required">Usuario</label>
								<div class="input-group">
									<input type="text" class="form-control obligatorio" id="usuario" name="usuario" required=""  >
									<span class="input-group-addon"><i class="fa fa-user"></i></span>
								</div><br>
							</div>
									
							<div class="form-group">
								<label for="" class="required">Clave</label>
								<div class="input-group">
									<input type="text" class="form-control obligatorio" aria-describedby="sizing-addon2" name="p2" id="p2" required="">
									<input type="password" class="form-control obligatorio" aria-describedby="sizing-addon2" name="p1" id="p1" required="" >
									<span class="input-group-addon"><i class="fa fa-key"></i></span>								  	
								</div>
							</div>
									
							<div class="checkbox checkbox-primary">
								<input id="view" type="checkbox" checked="" name="view">
								<label for="view">Mostrar contrase&ntilde;a</label>
							</div>
							

							<div class="checkbox checkbox-success">
                    			<input id="change_pass" name="change_pass" value='0' type="checkbox" >
                    			<label class="change_pass" for="change_pass">Nueva Clave</label>
                			</div>
						</div>
							
						<div class="col-md-7">
							<!--
								<div class="form-group">
									<label for="" class="">Avatar</label><br>
									<div id="load_photo" class="app-img-temp img-thumbnail">
										<?php
											$n_logo = 'app/img/usuario/anonimo.png';
											if(!empty($idusuario))
												$n_logo = '../app/img/usuario/'.$avatar;
										?>
										<img id="photo" src="<?php echo $n_logo;?>" class="img-responsive img-thumbnail" style="background:#f3f3f4;height:170px;"/>
									</div>
								</div>
							-->
							<div class="form-group">
								<label for="" class="">SUCURSAL ASIGNADO</label>
								<ul class="sortable">
									
									<div id="lista_sucursal"></div>
									<!--<li style='border:1px solid transparent;'>TARACITY <div class="pull-right" style="margin-top: 0px;"> <select> <option></option></select> </div></li>
									<li style='border:1px solid transparent;'>LAMAS    <div class="pull-right" style="margin-top: 0px;"> <select> <option></option></select> </div></li>
									<li style='border:1px solid transparent;'>JUANJUI  <div class="pull-right" style="margin-top: 0px;"> <select> <option></option></select> </div></li>-->
								</ul>							
							</div>
						</div>
					</div>
				</div>			
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
					<button type="button" class="btn btn-primary btn-save btn_user">Guardar</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- ASIGNAR SUCURSAL -->
<div id="modal-form-sucursal" class="modal fade" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header"> <h4 class="modal-title">ASIGNAR SUCURSAL Y ROL</h4></div>
			
			<div class="modal-body">
				<div class="row">
					<div class="ibox">
						<div class="ibox-content " id="lista_asignacion" style=""></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- ASIGNAR SUCURSAL -->

<style>
	.sortable li{
		margin: 0 5px 1px 5px;
		padding: 5px;
		font-size: 12px;
		font-weight:bold;
	}
	
	.sortable{		
		list-style-type: none;
		padding: 5px 0 0 0;
		margin-bottom:20px;
		height:auto;
		border: 1px solid #ccc;
		background:white;
	}
	
	select.form-control{
		padding:0px;
		display: inline-block;
		font-size:11px;
		height: 22px;
		width: 100px;
	}
	.form-control.ui-state-error{
		border: 1px solid #f1a899;
		background: #fddfdf;
		color: #5f3f3f;
	}
	#modal-form{top:-3%;}

	#lista_asignacion ul {
    	padding-left: 24px;
	}

	
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
			/*border: 1px solid #e7eaec;
			background: #1ab394;
			-webkit-border-radius: 3px;
			border-radius: 3px;
			box-sizing: border-box;
			font-size:13px;
			padding: 10px;*/
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

<!--<link href="app/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css" rel="stylesheet">-->