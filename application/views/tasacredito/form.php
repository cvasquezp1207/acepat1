<form id="form_<?php echo $controller; ?>" class="form-horizontal app-form form-uppercase">
	<input type="hidden" name="idtasacredito" id="idtasacredito" value="<?php echo (!empty($idtasacredito)) ? $idtasacredito : ""; ?>">
	<div class="form-group">
		<label class="col-lg-2 control-label required">Mes</label>
		<div class="col-lg-10">
			<input type="text" name="mes" id="mes" value="<?php echo (!empty($mes)) ? $mes : ""; ?>" class="form-control" required="">
		</div>
	</div>
	
	<div class="form-group">
		<label class="col-lg-2 control-label required">Porcentaje</label>
		<div class="col-lg-10">
			<div class="input-group">
				<input type="text" name="porcentaje" id="porcentaje" value="<?php echo (!empty($porcentaje)) ? $porcentaje : ""; ?>" class="form-control" placeholder=0 required="">
				<span class="input-group-addon" id="sizing-addon2">%</span>
			</div>
		</div>
	</div>
	
	<div class="form-group">
		<div class="col-lg-offset-2 col-lg-9">
			<button id="btn_cancel" class="btn btn-sm btn-white btn_cancel" >Cancelar</button>
			<button type="submit" id="btn_save" class="btn btn-sm btn-primary" >Guardar</button>
		</div>
	</div>
</form>