<form id="form_<?php echo $controller; ?>" class="form-horizontal app-form">
	<input type="hidden" name="idsucursal" id="idsucursal" value="<?php echo (!empty($idsucursal)) ? $idsucursal : ""; ?>">
	<div class="form-group">
		<label class="col-lg-2 control-label required">Empresa</label>
		<div class="col-lg-10">
			<?php echo $empresa; ?>
		</div>
	</div>
	
	<div class="form-group">
		<label class="col-lg-2 control-label required">Descripci&oacute;n</label>
		<div class="col-lg-10">
			<input type="text" name="descripcion" id="descripcion" value="<?php echo (!empty($descripcion)) ? $descripcion : ""; ?>" class="form-control" required="">
		</div>
	</div>
	
	
	<div class="form-group">
		<label class="col-lg-2 control-label required">Direcci&oacute;n</label>
		<div class="col-lg-10">
			<input type="text" name="direccion" id="direccion" value="<?php echo (!empty($direccion)) ? $direccion : ""; ?>" class="form-control" required="">
		</div>
	</div>
	
	<div class="form-group">
		<label class="col-lg-2 control-label">Telefono</label>
		<div class="col-lg-10">
			<input type="text" name="telefono" id="telefono" value="<?php echo (!empty($telefono)) ? $telefono : ""; ?>" class="form-control" >
		</div>
	</div>
	
	<!--<div class="form-group">
		<label class="col-lg-2 control-label">Logo</label>
		<div class="col-lg-10">
			<input type="text" name="logo" id="logo" value="<?php echo (!empty($logo)) ? $logo : ""; ?>" class="form-control" >
		</div>
	</div>-->
	
	<div class="form-group">
		<div class="col-lg-offset-2 col-lg-9">
			<button id="btn_cancel" class="btn btn-sm btn-white btn_cancel" >Cancelar</button>
			<button type="submit" id="btn_save" class="btn btn-sm btn-primary" >Guardar</button>
		</div>
	</div>
</form>