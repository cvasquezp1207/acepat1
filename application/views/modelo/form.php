<form id="form_<?php echo $controller; ?>" class="form-horizontal app-form form-uppercase">
	<input type="hidden" name="idmodelo" id="<?php echo $prefix; ?>idmodelo" value="<?php echo (!empty($idmodelo)) ? $idmodelo : ""; ?>">
	<div class="form-group">
		<label class="col-lg-3 control-label required">Descripci&oacute;n</label>
		<div class="col-lg-9">
			<input type="text" name="descripcion" id="<?php echo $prefix; ?>descripcion" value="<?php echo (!empty($descripcion)) ? $descripcion : ""; ?>" class="form-control" required="">
		</div>
	</div>
	<div class="form-group">
		<label class="col-lg-3 control-label">Prefijo</label>
		<div class="col-lg-9">
			<input type="text" name="prefijo" id="<?php echo $prefix; ?>prefijo" value="<?php echo (!empty($prefijo)) ? $prefijo : ""; ?>" class="form-control">
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-offset-2 col-lg-10">
			<button id="<?php echo $prefix; ?>btn_cancel" class="btn btn-sm btn-white" data-controller="<?php echo $controller; ?>">Cancelar</button>
			<button type="submit" id="<?php echo $prefix; ?>btn_save" class="btn btn-sm btn-primary" data-controller="<?php echo $controller; ?>">Guardar</button>
		</div>
	</div>
</form>