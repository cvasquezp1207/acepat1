<form id="form_<?php echo $controller; ?>" class="form-horizontal app-form form-uppercase">
	<input type="hidden" name="idbanco" id="<?php echo $prefix; ?>idbanco" value="<?php echo (!empty($idbanco)) ? $idbanco : ""; ?>">
	<div class="form-group">
		<label class="col-lg-3 control-label required">Descripci&oacute;n</label>
		<div class="col-lg-9">
			<input type="text" name="banco" id="<?php echo $prefix; ?>banco" value="<?php echo (!empty($banco)) ? $banco : ""; ?>" class="form-control" required="">
		</div>
	</div>
	<div class="form-group">
		<label class="col-lg-3 control-label required">Abreviatura</label>
		<div class="col-lg-9">
			<input type="text" name="abreviatura" id="<?php echo $prefix; ?>abreviatura" value="<?php echo (!empty($abreviatura)) ? $abreviatura : ""; ?>" class="form-control" required="">
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-offset-3 col-lg-9">
			<button id="<?php echo $prefix; ?>btn_cancel" class="btn btn-sm btn-white btn_cancel<?php echo $modal?" modal-form":""; ?>" data-controller="<?php echo $controller; ?>">Cancelar</button>
			<button type="submit" id="<?php echo $prefix; ?>btn_save" class="btn btn-sm btn-primary" data-controller="<?php echo $controller; ?>">Guardar</button>
		</div>
	</div>
</form>