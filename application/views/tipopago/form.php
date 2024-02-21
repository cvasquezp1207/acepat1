<form id="form_<?php echo $controller; ?>" class="form-horizontal app-form form-uppercase">
	<input type="hidden" name="idtipopago" id="<?php echo $prefix; ?>idtipopago" value="<?php echo (!empty($idtipopago)) ? $idtipopago : ""; ?>">
	<!--
	<div class="form-group">
		<label class="col-lg-3 control-label required">Descripci&oacute;n</label>
		<div class="col-lg-9">
			<input type="text" name="descripcion" id="<?php echo $prefix; ?>descripcion" value="<?php echo (!empty($descripcion)) ? $descripcion : ""; ?>" class="form-control" required="">
		</div>
	</div>

	<div class="form-group">
		<div class="col-lg-offset-3 col-lg-9">
			<div class="row">
				<div class="col-sm-3" style="border:0px solid red;">
					<div class="form-group">
						<label class="required">Ver en compra</label>
						<div class="onoffswitch">
							<input type="checkbox" name="mostrar_en_compra" id="mostrar_en_compra" class="onoffswitch-checkbox" value="N" <?php echo (isset($mostrar_en_compra) && $mostrar_en_compra == 'S') ? "checked" : ""; ?>>
							<label class="onoffswitch-label" for="mostrar_en_compra">
								<span class="onoffswitch-inner"></span>
								<span class="onoffswitch-switch"></span>
							</label>
						</div>
					</div>
				</div>

				<div class="col-sm-3" style="border:0px solid red;">
					<div class="form-group">
						<label class="required">Ver en venta</label>
						<div class="onoffswitch">
							<input type="checkbox" name="mostrar_en_venta" id="mostrar_en_venta" class="onoffswitch-checkbox" value="N" <?php echo (isset($mostrar_en_venta) && $mostrar_en_venta == 'S') ? "checked" : ""; ?>>
							<label class="onoffswitch-label" for="mostrar_en_venta">
								<span class="onoffswitch-inner"></span>
								<span class="onoffswitch-switch"></span>
							</label>
						</div>
					</div>
				</div>

				<div class="col-sm-3" style="border:0px solid red;">
					<div class="form-group">
						<label class="required">Ver en r. ingreso</label>
						<div class="onoffswitch">
							<input type="checkbox" name="mostrar_en_reciboingreso" id="mostrar_en_reciboingreso" class="onoffswitch-checkbox" value="N" <?php echo (isset($mostrar_en_reciboingreso) && $mostrar_en_reciboingreso == 'S') ? "checked" : ""; ?>>
							<label class="onoffswitch-label" for="mostrar_en_reciboingreso">
								<span class="onoffswitch-inner"></span>
								<span class="onoffswitch-switch"></span>
							</label>
						</div>
					</div>
				</div>

				<div class="col-sm-3" style="border:0px solid red;">
					<div class="form-group">
						<label class="required">Ver en r. egreso</label>
						<div class="onoffswitch">
							<input type="checkbox" name="mostrar_en_reciboegreso" id="mostrar_en_reciboegreso" class="onoffswitch-checkbox" value="N" <?php echo (isset($mostrar_en_reciboegreso) && $mostrar_en_reciboegreso == 'S') ? "checked" : ""; ?>>
							<label class="onoffswitch-label" for="mostrar_en_reciboegreso">
								<span class="onoffswitch-inner"></span>
								<span class="onoffswitch-switch"></span>
							</label>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	-->
	
	<div class="form-group">
		<div class="col-sm-12">
		<label class="required">Descripci&oacute;n</label>
		<input type="text" name="descripcion" id="<?php echo $prefix; ?>descripcion" value="<?php echo (!empty($descripcion)) ? $descripcion : ""; ?>" class="form-control" required="">
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-12">
			<div class="row">
				<div class="col-sm-3">
					<div class="">
						<label>Ver en Compra?</label>
						<div class="onoffswitch">
							<input type="checkbox" name="mostrar_en_compra" id="mostrar_en_compra" class="onoffswitch-checkbox" value="N" <?php echo (isset($mostrar_en_compra) && $mostrar_en_compra == 'S') ? "checked" : ""; ?>>
							<label class="onoffswitch-label" for="mostrar_en_compra">
								<span class="onoffswitch-inner"></span>
								<span class="onoffswitch-switch"></span>
							</label>
						</div>
					</div>
				</div>

				<div class="col-sm-3">
					<div class="">
						<label>Ver en venta?</label>
						<div class="onoffswitch">
							<input type="checkbox" name="mostrar_en_venta" id="mostrar_en_venta" class="onoffswitch-checkbox" value="N" <?php echo (isset($mostrar_en_venta) && $mostrar_en_venta == 'S') ? "checked" : ""; ?>>
							<label class="onoffswitch-label" for="mostrar_en_venta">
								<span class="onoffswitch-inner"></span>
								<span class="onoffswitch-switch"></span>
							</label>
						</div>
					</div>
				</div>

				<div class="col-sm-3">
					<div class="">
						<label>Ver en recibo Ingreso?</label>
						<div class="onoffswitch">
							<input type="checkbox" name="mostrar_en_reciboingreso" id="mostrar_en_reciboingreso" class="onoffswitch-checkbox" value="N" <?php echo (isset($mostrar_en_reciboingreso) && $mostrar_en_reciboingreso == 'S') ? "checked" : ""; ?>>
							<label class="onoffswitch-label" for="mostrar_en_reciboingreso">
								<span class="onoffswitch-inner"></span>
								<span class="onoffswitch-switch"></span>
							</label>
						</div>
					</div>
				</div>

				<div class="col-sm-3">
					<div class="">
						<label>Ver en recibo Egreso?</label>
						<div class="onoffswitch">
							<input type="checkbox" name="mostrar_en_reciboegreso" id="mostrar_en_reciboegreso" class="onoffswitch-checkbox" value="N" <?php echo (isset($mostrar_en_reciboegreso) && $mostrar_en_reciboegreso == 'S') ? "checked" : ""; ?>>
							<label class="onoffswitch-label" for="mostrar_en_reciboegreso">
								<span class="onoffswitch-inner"></span>
								<span class="onoffswitch-switch"></span>
							</label>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-12">
			<div class="row">
				<div class="col-sm-3">
					<div class="">
						<label>Ver en pago proveedor?</label>
						<div class="onoffswitch">
							<input type="checkbox" name="mostrar_en_pagoproveedor" id="mostrar_en_pagoproveedor" class="onoffswitch-checkbox" <?php echo (isset($mostrar_en_pagoproveedor) && $mostrar_en_pagoproveedor == 'S') ? "checked" : ""; ?> />
							<label class="onoffswitch-label" for="mostrar_en_pagoproveedor">
								<span class="onoffswitch-inner"></span>
								<span class="onoffswitch-switch"></span>
							</label>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="form-group">
		<div class="col-lg-offset-3 col-lg-9">
			<button id="<?php echo $prefix; ?>btn_cancel" class="btn btn-sm btn-white btn_cancel<?php echo $modal?" modal-form":""; ?>" data-controller="<?php echo $controller; ?>">Cancelar</button>
			<button type="submit" id="<?php echo $prefix; ?>btn_save" class="btn btn-sm btn-primary" data-controller="<?php echo $controller; ?>">Guardar</button>
		</div>
	</div>
</form>