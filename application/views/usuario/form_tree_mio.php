<form id="form_<?php echo $controller; ?>" class="app-form form-uppercase">
	<input type="hidden" 	name="idusuario" 	id="<?php echo $prefix; ?>idusuario" 	value="<?php echo (!empty($idusuario)) ? $idusuario : ""; ?>">
	<!--
	<input type="file" 		name="file" 	 	id="<?php echo $prefix; ?>file" 		style="display: none;" onchange='leerarchivobin(this)' />
	<input type="hidden" 	name="avatar"	 	id="<?php echo $prefix; ?>avatar"  	value="<?php echo (!empty($avatar)) ? $avatar : "anonimo.png"; ?>"/>
	<input type="hidden" 	name="avatar_new"	id="<?php echo $prefix; ?>avatar_new" value="<?php echo (!empty($avatar)) ? $avatar : "anonimo.png"; ?>"/>
	<input type="hidden" 	name="clave" 		id="<?php echo $prefix; ?>clave"  	value="<?php echo (!empty($clave)) ? $clave : ""; ?>">
	<input type="hidden" 	name="clave_past" 	id="<?php echo $prefix; ?>clave_past" value="<?php echo (!empty($clave)) ? $clave : ""; ?>">
	-->
	
	<div class="row">
		<div class="col-lg-12">
			<div class="tabs-container">
				<ul class="nav nav-tabs">
					<li class="active"><a data-toggle="tab" href="#tab-1">Datos Personales</a></li>
					<li class=""><a data-toggle="tab" href="#tab-2"> Rol y Sucursal</a></li>
					<!--<li class=""><a data-toggle="tab" href="#tab-3"> Usuario</a></li>-->
				</ul>
				<div class="tab-content">
					<div id="tab-1" class="tab-pane active">
						<div class="panel-body">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<div class="row">
											<div class="col-md-12">
												<div class="form-group">
													<label for="" class="required">Nombres</label>
													<input type="text" class="form-control"  id="<?php echo $prefix; ?>nombres" name="nombres" required=""  value="<?php echo (!empty($nombres)) ? $nombres : ""; ?>" />
												</div>
											</div>
										</div>
									</div>

									<div class="form-group">
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label for="" class="required">Apellido Paterno</label>
													<input type="text" class="form-control"  id="<?php echo $prefix; ?>appat" name="appat" required="" value="<?php echo (!empty($appat)) ? $appat : ""; ?>" />
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="" class="required">Apellido Materno</label>
													<input type="text" class="form-control"  id="<?php echo $prefix; ?>apmat" name="apmat" required="" value="<?php echo (!empty($apmat)) ? $apmat : ""; ?>" />
												</div>
											</div>
										</div>
									</div>

									<div class="form-group">
										<div class="row">
											<div class="col-md-4">
												<div class="form-group">
													<label for="" class="required">DNI</label>
													<input type="text" class="form-control" maxlength=8 id="<?php echo $prefix; ?>dni" name="dni" required="" value="<?php echo (!empty($dni)) ? $dni : ""; ?>" >
												</div>
											</div>

											<div class="col-md-8">
												<label for="" class="">E-mail</label>
												<div class="input-group">
													<input type="text" class="form-control" placeholder="E-mail" id="<?php echo $prefix; ?>email" name="email" aria-describedby="sizing-addon2" value="<?php echo (!empty($email)) ? $email : ""; ?>" >
													<span class="input-group-addon" id="sizing-addon2">@</span>
												</div>
											</div>
										</div>
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-group">
										<div class="row">
											<div class="col-md-12">
												<div class="form-group">
												<label class="required">Direcci&oacute;n</label>
												<div class="input-group">
													<input type="text" name="direccion" id="<?php echo $prefix; ?>direccion" value="<?php echo (!empty($direccion)) ? $direccion : ""; ?>" class="form-control" required="">
													<span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
												</div>
												</div>
											</div>
										</div>
									</div>

									<div class="form-group">
										<div class="row">
											<div class="col-md-7">
												<label for="" class="">Telefono</label>
												<div class="input-group">
													<input type="text" class="form-control" id="<?php echo $prefix; ?>telefono" name="telefono" aria-describedby="sizing-addon2"  value="<?php echo (!empty($telefono)) ? $telefono : ""; ?>" >
													<span class="input-group-addon"><i class="fa fa-phone"></i></span>
												</div>
											</div>

											<div class="col-md-5">
												<label class="required">Fecha Nacimiento</label>
												<div class="input-group date">
													<input type="text" placeholder="dd/mm/Y" name="fecha_nac" id="<?php echo $prefix; ?>fecha_nac" value="<?php echo (!empty($fecha_nac)) ? dateFormat($fecha_nac, "d/m/Y") : ""; ?>" class="form-control" required="">
													<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
												</div>
											</div>
										</div>
									</div>
									
									<div class="form-group">
										<div class="row">
											<div class="col-md-12">
												<div class="form-group">
													<br>
													<label for="" class="required">Sexo</label>
													<?php
														echo radio_sexo((!empty($sexo)) ? $sexo : "A");
													?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<div id="tab-2" class="tab-pane">
						<div class="panel-body">
							<div class="row">
								<div class="col-sm-12">
									<div id="sms_checkbox"></div>
									<h4 class="modal-title">ASIGNAR SUCURSAL Y ROL( EN LA EMPRESA)</h4>
									<div class="" id="lista_asignacion" style=""></div>
								</div>
							</div>
						</div>
					</div>
					
					<!--
					<div id="tab-3" class="tab-pane">
						<div class="panel-body">
							<div class="row">
								<div class="col-sm-12">
									
								</div>
							</div>
						</div>
					</div>
					-->
				</div>
			</div>
		</div>
	</div>
	
	<div class="form-group">
		<div class="col-lg-offset-3 col-lg-9">
			<button id="<?php echo $prefix; ?>btn_cancel" class="btn btn-sm btn-white btn_cancel<?php echo $modal?" modal-form":""; ?>" data-controller="<?php echo $controller; ?>">Cancelar</button>
			<button type="submit" id="<?php echo $prefix; ?>btn_save" class="btn btn-sm btn-primary" >Guardar</button>
		</div>
	</div>
</form>
<input type="hidden" id="controlador" value="<?php echo $controller;?>" />

<style type="text/css">
	#lista_asignacion ul {
    	padding-left: 24px;
	}
	#sms_checkbox{color:red;font-weight:bold;padding:10px 10px 10px 10px;}
</style>