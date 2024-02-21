<?php echo $grilla;?>

<div id="modal-form" class="modal fade" aria-hidden="true" data-backdrop="static" >
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header"> 
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">ASIGNAR USUARIO</h4>
			</div>
			<form id="form-user" >
				<input type="hidden" name="idusuario_firts" id="idusuario" >
				<input type="file" 	 id="file" 		 name="file"style="display: none;" onchange='leerarchivobin(this)' />
				<input type="hidden" id="avatar" 	 name="avatar" 		value="anonimo.png"/>
				<input type="hidden" id="avatar_new" name="avatar_new" 	value="anonimo.png"/>
				<input type="hidden" id="clave" 	 name="clave"  >
				<input type="hidden" name="clave_past" id="clave_past" >
				
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6">
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
								<div class="row">
									<div class="col-md-6" style="border:0px solid red;">
										<label for="" class="required">Usuario</label>
										<div class="input-group">
											<input type="text" class="form-control obligatorio" id="usuario" name="usuario" required=""  >
											<span class="input-group-addon"><i class="fa fa-user"></i></span>
										</div>
									</div>
									
									<div class="col-md-6" style="border:0px solid red;">
										<label for="" class="required">Clave</label>
										<div class="input-group">
											<input type="text" class="form-control obligatorio" aria-describedby="sizing-addon2" name="p2" id="p2" required="">
											<input type="password" class="form-control obligatorio" aria-describedby="sizing-addon2" name="p1" id="p1" required="" >
											<span class="input-group-addon"><i class="fa fa-key"></i></span>								  	
										</div>
										<div style="margin-top:10px;"></div>
										<div class="checkbox checkbox-primary">
											<input id="view" type="checkbox" checked="" name="view">
											<label for="view">Mostrar contrase&ntilde;a</label>
										</div>
										
										<div class="checkbox checkbox-success">
											<input id="change_pass" name="change_pass" value='0' type="checkbox" >
											<label class="change_pass" for="change_pass">Nueva Clave</label>
										</div>
									</div>
								</div>
							</div>					

						</div>
							
						<div class="col-md-6">
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
								<label for="" class="">ASIGNAR PERFIL POR SUCURSAL</label>
								<table class="table table-striped list-suc" border=0 width='100%'>
									<thead>
										<tr>
											<th width='30%'>Empresa / Sucursal</th>
											<th width='20%'>Es Super Usuario?</th>
											<th width='20%'>Control sucursal?</th>
											<th width='18%'>Perfil</th>
										</tr>
									</thead>
									
									<tbody id="lista_sucursal"></tbody>
								</table>
								<!--
								<ul class="sortable">
									<div id="lista_sucursal"></div>
								</ul>
								-->
									<!--<li style='border:1px solid transparent;'>TARACITY <div class="pull-right" style="margin-top: 0px;"> <select> <option></option></select> </div></li>
									<li style='border:1px solid transparent;'>LAMAS    <div class="pull-right" style="margin-top: 0px;"> <select> <option></option></select> </div></li>
									<li style='border:1px solid transparent;'>JUANJUI  <div class="pull-right" style="margin-top: 0px;"> <select> <option></option></select> </div></li>-->
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

	#lista_asignacion ul,#lista_sucursal ul {
    	padding-left: 24px;
	}
</style>

<!--<link href="app/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css" rel="stylesheet">-->