<form id="form_<?php echo $controller; ?>" class="form-horizontal app-form form-uppercase">
	<input type="hidden" name="idchofer" id="<?php echo $prefix; ?>idchofer" value="<?php echo (!empty($idchofer)) ? $idchofer : ""; ?>">
	<div class="form-group">
		<label class="col-lg-3 control-label required">Nombres</label>
		<div class="col-lg-9">
			<input type="text" name="nombre" id="<?php echo $prefix; ?>nombre" value="<?php echo (!empty($nombre)) ? $nombre : ""; ?>" class="form-control" required="required">
		</div>
	</div>
	<div class="form-group">
		<label class="col-lg-3 control-label">Licencia de conducir</label>
		<div class="col-lg-9">
			<input type="text" name="licencia" id="<?php echo $prefix; ?>licencia" value="<?php echo (!empty($licencia)) ? $licencia : ""; ?>" class="form-control">
		</div>
	</div>
	<div class="form-group">
		<label class="col-lg-3 control-label">Marca y nro. placa</label>
		<div class="col-lg-9">
			<input type="text" name="placa" id="<?php echo $prefix; ?>placa" value="<?php echo (!empty($placa)) ? $placa : ""; ?>" class="form-control">
		</div>
	</div>
	<div class="form-group">
		<label class="col-lg-3 control-label">Constancia de inscripcion</label>
		<div class="col-lg-9">
			<input type="text" name="inscripcion" id="<?php echo $prefix; ?>inscripcion" value="<?php echo (!empty($inscripcion)) ? $inscripcion : ""; ?>" class="form-control">
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-offset-3 col-lg-9">
			<button id="<?php echo $prefix; ?>btn_cancel" class="btn btn-sm btn-white btn_cancel<?php echo $modal?" modal-form":""; ?>" data-controller="<?php echo $controller; ?>">Cancelar</button>
			<button type="submit" id="<?php echo $prefix; ?>btn_save" class="btn btn-sm btn-primary" data-controller="<?php echo $controller; ?>">Guardar</button>
		</div>
	</div>
</form>