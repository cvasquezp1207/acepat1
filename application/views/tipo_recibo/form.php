<form id="form_<?php echo $controller; ?>" class="app-form form-uppercase">
	<input type="hidden" name="idtipo_recibo" id="idtipo_recibo" value="<?php echo (!empty($idtipo_recibo)) ? $idtipo_recibo : "";?>"/>
	
	<div class="row">
		<div class="col-lg-12">
			<div class="tabs-container">
				<ul class="nav nav-tabs">
					<li class="active"><a data-toggle="tab" href="#tab-1"> Tipo Recibo</a></li>
					<!-- <li class=""><a data-toggle="tab" href="#tab-2"> Control Correlativo</a></li> -->
				</ul>
				<div class="tab-content">
					<div id="tab-1" class="tab-pane active">
						<div class="panel-body">
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<label class="required">Descripci&oacute;n</label>
										<input type="text" name="descripcion" id="descripcion" value="<?php echo (!empty($descripcion)) ? $descripcion : ""; ?>" class="form-control" required="" />
									</div>
								</div>

								
							</div>

							<div class="row">
								<div class="col-sm-12">
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
													<input type="checkbox" id="mostrar_en_recibos" class="onoffswitch-checkbox" <?php echo (isset($mostrar_en_recibos) && $mostrar_en_recibos == 'S') ? "checked" : ""; ?> />
													<label class="onoffswitch-label" for="mostrar_en_recibos">
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
													<input type="checkbox" id="mostrar_en_recibo" class="onoffswitch-checkbox" <?php echo (isset($mostrar_en_recibo) && $mostrar_en_recibo == 'S') ? "checked" : ""; ?>>
													<label class="onoffswitch-label" for="mostrar_en_recibo">
														<span class="onoffswitch-inner"></span>
														<span class="onoffswitch-switch"></span>
													</label>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-sm-12">
									<div class="row">
										<div class="col-sm-3">
											<div class="form-group">
												<label>Genera Correlativo?</label>
												<div class="onoffswitch">
													<input type="checkbox" id="genera_correlativo" class="onoffswitch-checkbox" <?php echo (isset($genera_correlativo) && $genera_correlativo == 'S') ? "checked" : ""; ?> />
													<label class="onoffswitch-label" for="genera_correlativo">
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

<input type="hidden" id="controlador" value="<?php echo $controller;?>" />
<input type="hidden" id="idsuc" value="<?php echo $sucursal_session;?>" />