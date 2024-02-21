<form id="form_<?php echo $controller; ?>" class="app-form form-uppercase">
	<input type="hidden" name="idmotivo_guia" id="idmotivo_guia" value="<?php echo (!empty($idmotivo_guia)) ? $idmotivo_guia : ""; ?>">
	
	<div class="row">
		<div class="col-md-12">
			<div class="form-group">
				<label class="required">Descripci&oacute;n</label>
				<input type="text" name="descripcion" id="descripcion" value="<?php echo isset($descripcion) ? $descripcion : ""; ?>" class="form-control" required="">
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-sm-6">
			<div class="form-group">
				<label class="required">Tipo</label>
				<?php echo $combo_operacion;?>
			</div>
		</div>
		<div class="col-sm-3">
			<div class="form-group">
				<label>Mostrar en guia</label>
				<div class="onoffswitch">
					<input type="checkbox" name="mostrar_en_guia" id="mostrar_en_guia" class="onoffswitch-checkbox" value="S" <?php echo (isset($mostrar_en_guia) && $mostrar_en_guia == 'S') ? "checked" : ""; ?>>
					<label class="onoffswitch-label" for="mostrar_en_guia">
						<span class="onoffswitch-inner"></span>
						<span class="onoffswitch-switch"></span>
					</label>
				</div>
			</div>
		</div>
		<div class="col-sm-3">
			<div class="form-group">
				<label>Afecta stock</label>
				<div class="onoffswitch">
					<input type="checkbox" name="afecta_stock" id="afecta_stock" class="onoffswitch-checkbox" value="S" <?php echo (isset($afecta_stock) && $afecta_stock == 'S') ? "checked" : ""; ?>>
					<label class="onoffswitch-label" for="afecta_stock">
						<span class="onoffswitch-inner"></span>
						<span class="onoffswitch-switch"></span>
					</label>
				</div>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-sm-6">
			<div class="panel panel-default">
				<div class="panel-heading"><i class="fa fa-wrench"></i> Configurar Salida</div>
				<div class="panel-body panel-body-salida">
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label>Tipo movimiento</label>
								<?php echo $combo_salida;?>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-sm-4">
							<div class="form-group">
								<label>Buscar venta</label>
								<div class="onoffswitch">
									<input type="checkbox" name="salida_buscar_venta" id="salida_buscar_venta" class="onoffswitch-checkbox" value="S" <?php echo (isset($salida_buscar_venta) && $salida_buscar_venta == 'S') ? "checked" : ""; ?>>
									<label class="onoffswitch-label" for="salida_buscar_venta">
										<span class="onoffswitch-inner"></span>
										<span class="onoffswitch-switch"></span>
									</label>
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label>Buscar compra</label>
								<div class="onoffswitch">
									<input type="checkbox" name="salida_buscar_compra" id="salida_buscar_compra" class="onoffswitch-checkbox" value="S" <?php echo (isset($salida_buscar_compra) && $salida_buscar_compra == 'S') ? "checked" : ""; ?>>
									<label class="onoffswitch-label" for="salida_buscar_compra">
										<span class="onoffswitch-inner"></span>
										<span class="onoffswitch-switch"></span>
									</label>
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label>Buscar item <i class="fa fa-question-circle" title="Buscar productos sin ninguna restricci&oacute;n"></i></label>
								<div class="onoffswitch">
									<input type="checkbox" name="salida_libre_item" id="salida_libre_item" class="onoffswitch-checkbox" value="S" <?php echo (isset($salida_libre_item) && $salida_libre_item == 'S') ? "checked" : ""; ?>>
									<label class="onoffswitch-label" for="salida_libre_item">
										<span class="onoffswitch-inner"></span>
										<span class="onoffswitch-switch"></span>
									</label>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-6">
			<div class="panel panel-default">
				<div class="panel-heading"><i class="fa fa-wrench"></i> Configurar Entrada</div>
				<div class="panel-body panel-body-ingreso">
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label>Tipo movimiento</label>
								<?php echo $combo_ingreso;?>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-8">
							<div class="form-group">
								<label>Buscar guia</label>
								<ul class="list-inline">
									<li>
										<div class="onoffswitch">
											<input type="checkbox" name="ingreso_buscar_guia" id="ingreso_buscar_guia" class="onoffswitch-checkbox" value="S" <?php echo (isset($ingreso_buscar_guia) && $ingreso_buscar_guia == 'S') ? "checked" : ""; ?>>
											<label class="onoffswitch-label" for="ingreso_buscar_guia">
												<span class="onoffswitch-inner"></span>
												<span class="onoffswitch-switch"></span>
											</label>
										</div>
									</li>
									<li>
										<label style="font-weight:normal;margin:0;">
											<input type="checkbox" name="ingreso_b_esta_sede" id="ingreso_b_esta_sede" value="S" <?php echo (isset($ingreso_b_esta_sede) && $ingreso_b_esta_sede == 'S') ? "checked" : ""; ?>>
											En esta sede
										</label>
									</li>
									<li>
										<label style="font-weight:normal;margin:0;">
											<input type="checkbox" name="ingreso_b_otra_sede" id="ingreso_b_otra_sede" value="S" <?php echo (isset($ingreso_b_otra_sede) && $ingreso_b_otra_sede == 'S') ? "checked" : ""; ?>>
											En otra sede
										</label>
									</li>
								</ul>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label>Buscar item <i class="fa fa-question-circle" title="Buscar productos sin ninguna restricci&oacute;n"></i></label>
								<div class="onoffswitch">
									<input type="checkbox" name="ingreso_libre_item" id="ingreso_libre_item" class="onoffswitch-checkbox" value="S" <?php echo (isset($ingreso_libre_item) && $ingreso_libre_item == 'S') ? "checked" : ""; ?>>
									<label class="onoffswitch-label" for="ingreso_libre_item">
										<span class="onoffswitch-inner"></span>
										<span class="onoffswitch-switch"></span>
									</label>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="form-group">
			<div class="col-lg-12">
				<button id="btn_cancel" class="btn btn-sm btn-white btn_cancel" data-controller="<?php echo $controller; ?>">Cancelar</button>
				<button id="btn_save" class="btn btn-sm btn-primary" data-controller="<?php echo $controller; ?>">Guardar</button>
			</div>
		</div>
	</div>
</form>