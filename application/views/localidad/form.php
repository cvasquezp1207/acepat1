<form id="form_<?php echo $controller; ?>" class="form-horizontal app-form form-uppercase">
	<input type="hidden" name="idzona" id="<?php echo $prefix; ?>idzona" value="<?php echo (!empty($idzona)) ? $idzona : ""; ?>">
	<div class="form-group">
		<label class="col-lg-2 control-label required">Ruta</label>
		<div class="col-lg-10">
			<?php echo $ubigeosorsa; ?>
		</div>
	</div>
	
	<div class="form-group">
		<label class="col-lg-2 control-label required">Localidad</label>
		<div class="col-lg-10">
			<input type="text" name="zona" id="<?php echo $prefix; ?>zona" value="<?php echo (!empty($zona)) ? $zona : ""; ?>" class="form-control" required="">
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-offset-2 col-lg-10">
			<button id="<?php echo $prefix; ?>btn_cancel" class="btn btn-sm btn-white" data-controller="<?php echo $controller; ?>">Cancelar</button>
			<button type="submit" id="<?php echo $prefix; ?>btn_save" class="btn btn-sm btn-primary" data-controller="<?php echo $controller; ?>">Guardar</button>
		</div>
	</div>
</form>