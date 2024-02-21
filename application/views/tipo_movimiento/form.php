<form id="form_<?php echo $controller; ?>" class="form-horizontal app-form form-uppercase">
	<input type="hidden" name="reg" value="<?php echo $reg; ?>">
	<div class="form-group">
		<label class="col-lg-2 control-label required">Id</label>
		<div class="col-lg-10">
			<input type="text" name="tipo_movimiento" id="tipo_movimiento" value="<?php echo (!empty($tipo_movimiento)) ? $tipo_movimiento : ""; ?>" class="form-control" required="" <?php echo $readonly; ?>>
		</div>
	</div>
	
	<div class="form-group">
		<label class="col-lg-2 control-label required">Descripci&oacute;n</label>
		<div class="col-lg-10">
			<input type="text" name="descripcion" id="descripcion" value="<?php echo (!empty($descripcion)) ? $descripcion : ""; ?>" class="form-control" required="">
		</div>
	</div>
	
	<div class="form-group">
		<label class="col-lg-2 control-label required">Tipo</label>
		<div class="col-lg-10">
			<?php echo $combo_tipo; ?>
		</div>
	</div>
	
	<div class="form-group">
		<label class="col-lg-2 control-label">Correlativo</label>
		<div class="col-lg-10">
			<div class="input-group">
				<input type="text" name="correlativo" id="correlativo" value="<?php echo (!empty($correlativo)) ? $correlativo : "1"; ?>" class="form-control" readonly>
				<span class="input-group-addon"> <input type="checkbox" name="edit_correlativo" id="edit_correlativo" value="1" title="Modificar campo correlativo"> </span>
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