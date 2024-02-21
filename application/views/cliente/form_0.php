<form id="form_<?php echo $controller; ?>" class="form-horizontal app-form">
	<input type="hidden" name="idcliente" id="idcliente" value="<?php echo (!empty($idcliente)) ? $idcliente : ""; ?>">
		
	<div class="form-group">
		<!--incorporar tabs-->
	<?php 
		$checkedJ="";
		$checkedN="";
		$style = "";
		$razon = '';
		$nombre = '';

		if(!empty($tipo)):

			if($tipo=="J"):
				$checkedJ = 'checked=""';
				$checkedN = 'disabled=""';
				$styleJ='style="display: block"';
				$styleN='style="display: none"';
				
			else:
				$checkedN = 'checked=""';
				$checkedJ = 'disabled=""';
				$styleJ='style="display: none"';
				$styleN='style="display: block"';
				

			endif;

		else:
			$tipo="N";
			$checkedN = 'checked=""';
			$styleJ='style="display: none"';
			$styleN='style="display: block"';						
			endif;
	?>
		<div class="tabs-container">
			<ul class="nav nav-tabs">
				<li class="active"><a data-toggle="tab" href="#tab-1"> Datos Principales</a></li>
                <li  class=""><a data-toggle="tab" href="#tab-2">Trabajo</a></li>
                <li  class=""><a data-toggle="tab" href="#tab-3">Familia</a></li>
			</ul>
			<div class="tab-content">
				<div id="tab-1" class="tab-pane active">
					<div class="panel-body">
						<label class="col-sm-3 control-label">Tipo de Persona</label>
						<div class="form-group col-sm-9" >
							<div class="i-checks col-sm-3">
								<label><input type="radio" <?php echo $checkedN; ?> value="N" name="tipo" > <i></i> Persona Natural </label>
							</div>
							<div class="i-checks col-sm-3">
								<label><input type="radio" <?php echo $checkedJ; ?> value="J" name="tipo" id="check" > <i></i> Persona Jur&iacute;dica </label>
							</div>
						</div>	

						<div id="content2" <?php echo $styleN; ?>>
							<div class="form-group"  >
								<label class="col-sm-3 control-label required">DNI</label>
								<div class="col-sm-3">
									<input type="text" name="dni" id="dni" maxlength="8" value="<?php echo (!empty($dni)) ? $dni : ""; ?>" class="form-control" required="">
								</div>
								<div class="col-sm-3">
									<input type="text" name="ruc" id="rucN" maxlength="8" value="<?php echo (!empty($ruc)) ? $ruc : ""; ?>" class="form-control" required="">
								</div>

							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label required">Nombre</label>
								<div class="col-sm-3">
									<input type="text"  name="nombres" id="nombres" placeholder="Nombres" value="<?php echo (!empty($nombres)) ? $nombres : ""; ?>" class="form-control" required="">
								</div>
								<div class="col-sm-6">
									<input type="text" name="apellidos" id="apellidos" placeholder="Apellidos" value="<?php echo (!empty($apellidos)) ? $apellidos : ""; ?>" class="form-control" required="">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label ">Cumplea&ntilde;os</label>
								<div class="col-sm-3">
									<div class="input-group date">
									       <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
									       <input name="fecha_nac" id="fecha_nac" type="text" class="form-control" value="<?php  echo (!empty($fecha_nac)) ? dateFormat($fecha_nac,"d/m/Y") : "" ?>"/>
									</div>
								</div>
							</div>
						</div><!--fin content2-->
						<div id="content" <?php echo $styleJ; ?>>
							<div class="form-group">
								<label class="col-sm-3 control-label required">R.U.C.</label>
								<div class="col-sm-4">
									<input type="text" name="ruc" id="ruc" maxlength="11" value="<?php echo (!empty($ruc)) ? $ruc : ""; ?>" class="form-control" required="">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label required">Razon Social</label>
								<div class="col-sm-9">
									<input type="text" name="<?php echo $razon ?>" id="<?php echo $razon ?>" placeholder="Razon Social" value="<?php echo (!empty($razon)) ? $razon : ""; ?>" class="form-control" required="">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label ">Representante Legal1</label>
								<div class="col-sm-3">
									<input type="text" name="represnombre" id="represnombre" placeholder="Nombre" value="<?php echo (!empty($represnombre)) ? $represnombre : ""; ?>" class="form-control" >
								</div>
								<div class="col-sm-6">
									<input type="text" name="represapellido" id="represapellido" placeholder="Apellidos" value="<?php echo (!empty($represapellido)) ? $represapellido : ""; ?>" class="form-control" >
								</div>
							</div>
							<div class="form-group"  >
								<label class="col-sm-3 control-label ">Representante DNI</label>
								<div class="col-sm-3">
									<input type="text" name="represdni" id="represdni" maxlength="8" value="<?php echo (!empty($represdni)) ? $represdni : ""; ?>" class="form-control" >
								</div>
								<div class="col-sm-6">
									<div class="input-group email">
										<span class="input-group-addon">@</span>
										<input type="text" placeholder="email" name="represemail" id="represemail" value="<?php echo (!empty($represemail)) ? $represemail : ""; ?>" class="form-control">
									</div>
								</div>
							</div>
						</div><!--fin content-->
						<div class="form-group">
							<label class="col-sm-3 control-label required">Direcci&oacute;n</label>
							<div class="col-sm-9">
								<input type="text" name="direccion" id="direccion" value="<?php echo (!empty($direccion)) ? $direccion : ""; ?>" class="form-control" required="">
							</div>
						</div>

					</div>	
				</div>
				<!-- <div class="target"> -->
				<div id="tab-2" class="tab-pane " >
					<div class="pane-body">
						<div class="form-group">
						</br>
							<label class="col-sm-3 control-label">Situaci&oacute;n Laboral</label>
							<div class="col-sm-3" >
								<?php echo $situacion; ?>
							</div>
							<label class="col-sm-3 control-label">Ingreso Mensual</label>
							<div class="col-sm-3">
									<input type="text" name="ingreso" id="ingreso" maxlength="8" value="<?php echo (!empty($ingreso)) ? $ingreso : ""; ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label ">Centro de Trabajo</label>
							<div class="col-sm-9">
								<input type="text" name="centro_trabajo" id="centro_trabajo" value="<?php echo (!empty($centro_trabajo)) ? $centro_trabajo : ""; ?>" class="form-control" >
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label ">Ocupaci&oacute;n / Cargo </label>
							<div class="col-sm-9">
								<input type="text" name="ocupacion" id="ocupacion" value="<?php echo (!empty($ocupacion)) ? $ocupacion : ""; ?>" class="form-control" >
							</div>
						</div>	
						<div class="form-group">
							<label class="col-sm-3 control-label ">Direcci&oacute;n Trabajo</label>
							<div class="col-sm-9">
								<input type="text" name="direccion_trabajo" id="direccion_trabajo" value="<?php echo (!empty($direccion_trabajo)) ? $direccion_trabajo : ""; ?>" class="form-control" >
							</div>
						</div>
					</div>
				</div>
				<div id="tab-3" class="tab-pane ">
					<div class="pane-body">
						<div class="form-group">
						</br>
							<label class="col-sm-3 control-label ">Direcci&oacute;n Trabajo</label>
							<div class="col-sm-9">
								<input type="text" name="direccion_trabajo" id="direccion_trabajo" value="<?php echo (!empty($direccion_trabajo)) ? $direccion_trabajo : ""; ?>" class="form-control" >
							</div>
						</div>
					</div>
				</div>
				
			</div>	
		</div>	
	</div>	
	<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<button type="submit" id="btn_save" class="btn btn-sm btn-primary" data-controller="<?php echo $controller; ?>">Guardar</button>
				<button id="btn_cancel" class="btn btn-sm btn-white" data-controller="<?php echo $controller; ?>">Cancelar</button>
			</div>
	</div>			
</form>

		
