<form id="form_<?php echo $controller; ?>" class="form-horizontal app-form form-uppercase">
	<input type="hidden" name="idalmacen" id="<?php echo $prefix; ?>idalmacen" value="<?php echo (!empty($idalmacen)) ? $idalmacen : ""; ?>">
	<div class="form-group">
		<label class="col-lg-2 control-label required">Sucursal</label>
		<div class="col-lg-10">
			<?php echo $sucursal; ?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-lg-2 control-label required">Almacen</label>
		<div class="col-lg-10">
			<input type="text" name="descripcion" id="<?php echo $prefix; ?>descripcion" value="<?php echo (!empty($descripcion)) ? $descripcion : ""; ?>" class="form-control" required="">
		</div>
	</div>
	<div class="form-group">
		<label class="col-lg-2 control-label required">Direccion</label>
		<div class="col-lg-10">
			<input type="text" name="direccion" id="<?php echo $prefix; ?>direccion" value="<?php echo (!empty($direccion)) ? $direccion : ""; ?>" class="form-control" >
		</div>
	</div>
	<div class="form-group">
			<!--<div class="col-md-8">
		<div class="rows">
				<div class="form-group">
		-->
					<label class="col-lg-2 control-label required">Telefono</label>
					<div class="col-lg-2">
						<input type="text" name="telefono" id="<?php echo $prefix; ?>telefono" value="<?php echo (!empty($telefono)) ? $telefono : ""; ?>" class="form-control" >
					</div>
			<!--
				</div>
			</div>
			
			<div class="col-md-4">
				<div class="form-group">
			-->
					<label class="col-lg-2 control-label required">Ver en Venta</label>
					<div class="col-lg-2">
						<div class="onoffswitch">
							<input type="checkbox" id="mostrar_en_venta" class="onoffswitch-checkbox" <?php echo (isset($mostrar_en_venta) && $mostrar_en_venta == 'S') ? "checked" : ""; ?> />
							<label class="onoffswitch-label" for="mostrar_en_venta">
								<span class="onoffswitch-inner"></span>
								<span class="onoffswitch-switch"></span>
							</label>
						</div>
					</div>
					
					<label class="col-lg-2 control-label required">Ver en Compra</label>
					<div class="col-lg-2">
						<div class="onoffswitch">
							<input type="checkbox" id="mostrar_en_compra" class="onoffswitch-checkbox" <?php echo (isset($mostrar_en_compra) && $mostrar_en_compra == 'S') ? "checked" : ""; ?> />
							<label class="onoffswitch-label" for="mostrar_en_compra">
								<span class="onoffswitch-inner"></span>
								<span class="onoffswitch-switch"></span>
							</label>
						</div>
					</div>
			<!--
				</div>
			</div>
			-->
		</div>
	<div class="form-group">
		<div class="col-lg-offset-2 col-lg-10">
			<button id="<?php echo $prefix; ?>btn_cancel" class="btn btn-sm btn-white" data-controller="<?php echo $controller; ?>">Cancelar</button>
			<button type="submit" id="<?php echo $prefix; ?>btn_save" class="btn btn-sm btn-primary" data-controller="<?php echo $controller; ?>">Guardar</button>
		</div>
	</div>
</form>