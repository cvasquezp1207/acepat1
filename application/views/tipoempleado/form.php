<form id="form_<?php echo $controller; ?>" class="form-horizontal app-form form-uppercase">
	<input type="hidden" name="idtipoempleado" id="idtipoempleado" value="<?php echo (!empty($idtipoempleado)) ? $idtipoempleado : ""; ?>">
	<div class="form-group">
		<label class="col-lg-2 control-label required">Descripci&oacute;n</label>
		<div class="col-lg-10">
			<input type="text" name="descripcion" id="descripcion" value="<?php echo (!empty($descripcion)) ? $descripcion : ""; ?>" class="form-control" required="">
		</div>
	</div>
	
	<div class="form-group">
		<div class="col-lg-offset-2 col-lg-9">
			<button id="btn_cancel" class="btn btn-sm btn-white btn_cancel" >Cancelar</button>
			<button type="submit" id="btn_save" class="btn btn-sm btn-primary" >Guardar</button>
		</div>
	</div>
</form>