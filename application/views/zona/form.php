<form id="form_<?php echo $controller; ?>" class="form-horizontal app-form form-uppercase">
	<input type="hidden" name="idzona" id="<?php echo $prefix; ?>idzona" value="<?php echo (!empty($idzona)) ? $idzona : ""; ?>">
	<input type="hidden" name="idubigeo" id="<?php echo $prefix; ?>idubigeo" value="<?php echo (!empty($idubigeo)) ? $idubigeo : ""; ?>">
	<div class="form-group">
		<label class="col-lg-2 control-label required">Ubigeo</label>
		<div class="col-lg-10">
			<div class="input-group">
				<input type="text" name="" id="<?php echo $prefix; ?>ubigeo_descr" value="<?php echo (!empty($ubigeo_descr)) ? $ubigeo_descr : ""; ?>" class="form-control" required="" readonly="">
				<span class="input-group-btn tooltip-demo">
					<button type="button" id="btn_ubigeo" class="btn btn-outline btn-success" data-toggle="tooltip" title="&iquest;Buscar Ubigeo">
						<i class="fa fa-search"></i>
					</button>
				</span>
			</div>
		</div>
	</div>

	<div class="form-group">
		<label class="col-lg-2 control-label required">Zona</label>
		<div class="col-lg-10">
			<input type="text" name="zona" id="<?php echo $prefix; ?>zona" value="<?php echo (!empty($zona)) ? $zona : ""; ?>" class="form-control" required="">
		</div>
	</div>
	
	<div class="form-group">
		<div class="col-lg-offset-2 col-lg-9">
			<button id="<?php echo $prefix; ?>btn_cancel" class="btn btn-sm btn-white btn_cancel<?php echo $modal?" modal-form":""; ?>" data-controller="<?php echo $controller; ?>" >Cancelar</button>
			<button type="submit" id="<?php echo $prefix; ?>btn_save" class="btn btn-sm btn-primary" data-controller="<?php echo $controller; ?>">Guardar</button>
		</div>
	</div>
</form>

<?php echo $ubigeo; ?>