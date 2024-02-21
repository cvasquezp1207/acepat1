<form id="form_<?php echo $controller; ?>" class="form-horizontal app-form form-uppercase">
	<input type="hidden" name="idtransporte" id="<?php echo $prefix; ?>idtransporte" value="<?php echo (!empty($idtransporte)) ? $idtransporte : ""; ?>">
	<div class="form-group">
		<label class="col-lg-3 control-label required">Descripci&oacute;n</label>
		<div class="col-lg-9">
			<input type="text" name="nombre" id="<?php echo $prefix; ?>nombre" value="<?php echo (!empty($nombre)) ? $nombre : ""; ?>" class="form-control" required="">
		</div>
	</div>
	<div class="form-group">
		<label class="col-lg-3 control-label">RUC</label>
		<div class="col-lg-9">
			<input type="text" name="ruc" id="<?php echo $prefix; ?>ruc" value="<?php echo (!empty($ruc)) ? $ruc : ""; ?>" class="form-control" maxlength="11">
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-offset-3 col-lg-9">
			<button id="<?php echo $prefix; ?>btn_cancel" class="btn btn-sm btn-white btn_cancel<?php echo $modal?" modal-form":""; ?>" data-controller="<?php echo $controller; ?>">Cancelar</button>
			<button type="submit" id="<?php echo $prefix; ?>btn_save" class="btn btn-sm btn-primary" data-controller="<?php echo $controller; ?>">Guardar</button>
		</div>
	</div>
</form>