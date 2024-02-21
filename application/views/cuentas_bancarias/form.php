<form id="form_<?php echo $controller; ?>" class="form-horizontal app-form form-uppercase">
	<input type="hidden" name="idcuentas_bancarias" id="<?php echo $prefix; ?>idcuentas_bancarias" value="<?php echo (!empty($idcuentas_bancarias)) ? $idcuentas_bancarias : ""; ?>">
	<div class="form-group">
		<label class="col-lg-2 control-label required">Banco</label>
		<div class="col-lg-10">
			<?php echo $banco; ?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-lg-2 control-label required">Sucursal</label>
		<div class="col-lg-10">
			<?php echo $sucursal; ?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-lg-2 control-label required">Moneda</label>
		<div class="col-lg-10">
			<?php echo $moneda; ?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-lg-2 control-label required">Nro de Cuenta</label>
		<div class="col-lg-10">
			<input type="text" name="nro_cuenta" id="<?php echo $prefix; ?>nro_cuenta" value="<?php echo (!empty($nro_cuenta)) ? $nro_cuenta : ""; ?>" class="form-control" required="">
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-offset-2 col-lg-10">
			<button id="<?php echo $prefix; ?>btn_cancel" class="btn btn-sm btn-white" data-controller="<?php echo $controller; ?>">Cancelar</button>
			<button type="submit" id="<?php echo $prefix; ?>btn_save" class="btn btn-sm btn-primary" data-controller="<?php echo $controller; ?>">Guardar</button>
		</div>
	</div>
</form>