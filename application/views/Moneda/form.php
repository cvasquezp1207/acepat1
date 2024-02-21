<!--
<form id="form_<?php echo $controller; ?>" class="form-horizontal app-form form-uppercase">
	<input type="hidden" name="idmoneda" id="idmoneda" value="<?php echo (!empty($idmoneda)) ? $idmoneda : ""; ?>">
	<div class="form-group">
		<label class="col-lg-3 control-label required">Moneda</label>
		<div class="col-lg-9">
			<input type="text" name="descripcion" id="descripcion" value="<?php echo (!empty($descripcion)) ? $descripcion : ""; ?>" class="form-control" required="">
		</div>
	</div>
	<div class="form-group">
		<label class="col-lg-3 control-label required">Abreviatura</label>
		<div class="col-lg-9">
			<input type="text" name="abreviatura" id="abreviatura" value="<?php echo (!empty($abreviatura)) ? $abreviatura : ""; ?>" class="form-control" required="">
		</div>
	</div>
	<div class="form-group">
		<label class="col-lg-3 control-label required">Simbolo</label>
		<div class="col-lg-9">
			<input type="text" name="simbolo" id="simbolo" value="<?php echo (!empty($simbolo)) ? $simbolo : ""; ?>" class="form-control" required="">
		</div>
	</div>
	<div class="form-group">
		<label class="col-lg-3 control-label required">Valor Cambio</label>
		<div class="col-lg-9">
			<input type="text" name="valor_cambio" id="valor_cambio" value="<?php echo (!empty($valor_cambio)) ? $valor_cambio : ""; ?>" class="form-control" required="">
		</div>
	</div>
	<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<button type="submit" id="btn_save" class="btn btn-sm btn-primary" data-controller="<?php echo $controller; ?>">Guardar</button>
				<button id="btn_cancel" class="btn btn-sm btn-white" data-controller="<?php echo $controller; ?>">Cancelar</button>
			</div>
	</div>
</form>
-->

<form id="form_<?php echo $controller; ?>" class=" app-form form-uppercase">
	<input type="hidden" name="idmoneda" id="idmoneda" value="<?php echo (!empty($idmoneda)) ? $idmoneda : ""; ?>">
	<div class="row">
		<div class="col-sm-6">
			<div class="form-group">
				<label class="required">Moneda</label>
				<input type="text" name="descripcion" id="descripcion" value="<?php echo (!empty($descripcion)) ? $descripcion : ""; ?>" class="form-control" required="" />
			</div>
		</div>

		<div class="col-sm-2">
			<div class="form-group">
				<label class="required">Valor Cambio</label>
				<input type="text" name="valor_cambio" id="valor_cambio" value="<?php echo (!empty($valor_cambio)) ? $valor_cambio : ""; ?>" class="form-control" required="" />
			</div>
		</div>
		
		<div class="col-sm-2">
			<div class="form-group">
				<label class="required">Abreviatura</label>
				<input type="text" name="abreviatura" id="abreviatura" value="<?php echo (!empty($abreviatura)) ? $abreviatura : ""; ?>" class="form-control" required="" />
			</div>
		</div>

		<div class="col-sm-2">
			<div class="form-group">
				<label class="required">Simbolo</label>
				<input type="text" name="simbolo" id="simbolo" value="<?php echo (!empty($simbolo)) ? $simbolo : ""; ?>" class="form-control" maxlength="3" />
			</div>
		</div>
	</div>
	<br>
	
	<div class="row">
		<div class="col-sm-3">
			<div class="form-group">
				<label>Ver en Compra?</label>
				<div class="onoffswitch">
					<input type="checkbox" id="mostrar_en_compra" class="onoffswitch-checkbox" <?php echo (isset($mostrar_en_compra) && $mostrar_en_compra == 'S') ? "checked" : ""; ?> />
					<label class="onoffswitch-label" for="mostrar_en_compra">
						<span class="onoffswitch-inner"></span>
						<span class="onoffswitch-switch"></span>
					</label>
				</div>
			</div>
		</div>

		<div class="col-sm-3">
			<div class="form-group">
				<label>Ver en venta?</label>
				<div class="onoffswitch">
					<input type="checkbox" id="mostrar_en_venta" class="onoffswitch-checkbox" <?php echo (isset($mostrar_en_venta) && $mostrar_en_venta == 'S') ? "checked" : ""; ?> />
					<label class="onoffswitch-label" for="mostrar_en_venta">
						<span class="onoffswitch-inner"></span>
						<span class="onoffswitch-switch"></span>
					</label>
				</div>
			</div>
		</div>

		<div class="col-sm-3">
			<div class="form-group">
				<label>Ver en recibo Ingreso?</label>
				<div class="onoffswitch">
					<input type="checkbox" id="mostrar_en_recibo_i" class="onoffswitch-checkbox" <?php echo (isset($mostrar_en_recibo_i) && $mostrar_en_recibo_i == 'S') ? "checked" : ""; ?> />
					<label class="onoffswitch-label" for="mostrar_en_recibo_i">
						<span class="onoffswitch-inner"></span>
						<span class="onoffswitch-switch"></span>
					</label>
				</div>
			</div>
		</div>

		<div class="col-sm-3">
			<div class="form-group">
				<label>Ver en recibo Egreso?</label>
				<div class="onoffswitch">
					<input type="checkbox" id="mostrar_en_recibo_e" class="onoffswitch-checkbox" <?php echo (isset($mostrar_en_recibo_e) && $mostrar_en_recibo_e == 'S') ? "checked" : ""; ?>>
					<label class="onoffswitch-label" for="mostrar_en_recibo_e">
						<span class="onoffswitch-inner"></span>
						<span class="onoffswitch-switch"></span>
					</label>
				</div>
			</div>
		</div>
	</div>
	<br>

	<div class="row">
		<div class="col-sm-3">
			<div class="form-group">
				<label>Ver Nota Credito?</label>
				<div class="onoffswitch">
					<input type="checkbox" id="mostrar_en_notacredito" class="onoffswitch-checkbox" <?php echo (isset($mostrar_en_notacredito) && $mostrar_en_notacredito == 'S') ? "checked" : ""; ?> />
					<label class="onoffswitch-label" for="mostrar_en_notacredito">
						<span class="onoffswitch-inner"></span>
						<span class="onoffswitch-switch"></span>
					</label>
				</div>
			</div>
		</div>
	</div>
	<br>
	
	<div class="row">
		<div class="col-sm-12">
			<div class>
				<button type="submit" id="btn_save" class="btn btn-sm btn-primary" data-controller="<?php echo $controller; ?>">Guardar</button>
				<button id="btn_cancel" class="btn btn-sm btn-white" data-controller="<?php echo $controller; ?>">Cancelar</button>
			</div>
		</div>
	</div>
</form>