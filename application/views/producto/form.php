<!-- datos temporales -->
<div style="display:none;"><?php echo $tipo_precio_temp; ?></div>

<form id="form_<?php echo $controller; ?>" class="app-form form-uppercase">
	<input type="hidden" name="idproducto" id="<?php echo $prefix; ?>idproducto" value="<?php echo (isset($producto["idproducto"])) ? $producto["idproducto"] : ""; ?>">
	<input type="hidden" name="corr_temp" id="<?php echo $prefix; ?>corr_temp" value="<?php echo (isset($corr_temp)) ? $corr_temp : ""; ?>">
	<div class="row">
		<div class="col-lg-12">
			<div class="tabs-container">
				<ul class="nav nav-tabs">
					<li class="active"><a data-toggle="tab" href="#tab-1"> Datos b&aacute;sicos</a></li>
					<li class=""><a data-toggle="tab" href="#tab-2"> Otros datos</a></li>
					<li class=""><a data-toggle="tab" href="#tab-3"> Unidades de medida</a></li>
					<li class=""><a data-toggle="tab" href="#tab-4"> Lista de precios</a></li>
					<!--<li class=""><a data-toggle="tab" href="#tab-5"> Combos | Regalos</a></li>-->
					<li class=""><a data-toggle="tab" href="#tab-6"> Imagenes</a></li>
				</ul>
				<div class="tab-content">
					<div id="tab-1" class="tab-pane active">
						<div class="panel-body">
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label class="required">Linea</label>
										<input type="hidden" name="idlinea" id="<?php echo $prefix; ?>idlinea" value="<?php echo (isset($producto["idlinea"])) ? $producto["idlinea"] : ""; ?>">
										<input type="text" name="linea" id="<?php echo $prefix; ?>linea" value="<?php echo (isset($linea["descripcion"])) ? $linea["descripcion"] : ""; ?>" class="form-control" required="">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label class="required">Categoria</label>
										<input type="hidden" name="idcategoria" id="<?php echo $prefix; ?>idcategoria" value="<?php echo (isset($producto["idcategoria"])) ? $producto["idcategoria"] : ""; ?>">
										<input type="text" name="categoria" id="<?php echo $prefix; ?>categoria" value="<?php echo (isset($categoria["descripcion"])) ? $categoria["descripcion"] : ""; ?>" class="form-control" required="">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label class="required">Tipo</label>
										<?php echo $tipo; ?>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label class="required">Descripci&oacute;n / Gen&eacute;rico</label>
										<input type="text" name="descripcion" id="<?php echo $prefix; ?>descripcion" value="<?php echo (isset($producto["descripcion"])) ? $producto["descripcion"] : ""; ?>" class="form-control" required="">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label class="required">Marca</label>
										<!--<div class="input-group">
											<?php // echo $marca; ?>
											<span class="input-group-btn tooltip-demo">
												<button type="button" id="btn-registrar-marca" class="btn btn-outline btn-primary" data-toggle="tooltip" title="Registrar nueva marca">
													<i class="fa fa-edit"></i>
												</button>
											</span>
										</div>-->
										<input type="hidden" name="idmarca" id="<?php echo $prefix; ?>idmarca" value="<?php echo (isset($producto["idmarca"])) ? $producto["idmarca"] : ""; ?>">
										<input type="text" name="marca" id="<?php echo $prefix; ?>marca" value="<?php echo (isset($marca["descripcion"])) ? $marca["descripcion"] : ""; ?>" class="form-control" required="">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label class="required">Modelo</label>
										<!--<div class="input-group">
											<?php // echo $modelo; ?>
											<span class="input-group-btn tooltip-demo">
												<button type="button" id="btn-registrar-modelo" class="btn btn-outline btn-primary" data-toggle="tooltip" title="Registrar nuevo modelo">
													<i class="fa fa-edit"></i>
												</button>
											</span>
										</div>-->
										<input type="hidden" name="idmodelo" id="<?php echo $prefix; ?>idmodelo" value="<?php echo (isset($producto["idmodelo"])) ? $producto["idmodelo"] : ""; ?>">
										<input type="text" name="modelo" id="<?php echo $prefix; ?>modelo" value="<?php echo (isset($modelo["descripcion"])) ? $modelo["descripcion"] : ""; ?>" class="form-control" required="">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<label class="required">Descripci&oacute;n detallada</label>
										<input type="text" name="descripcion_detallada" id="<?php echo $prefix; ?>descripcion_detallada" value="<?php echo (isset($producto["descripcion_detallada"])) ? $producto["descripcion_detallada"] : ""; ?>" class="form-control" required="">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label class="required">Unidad medida</label>
										<div class="input-group">
											<?php echo $unidad; ?>
											<span class="input-group-btn tooltip-demo">
												<button type="button" class="btn btn-outline btn-primary btn-registrar-unidad" data-toggle="tooltip" title="Registrar unidad de medida">
													<i class="fa fa-edit"></i>
												</button>
											</span>
										</div>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label>Codigo de barras</label>
										<input type="text" name="codigo_barras" id="<?php echo $prefix; ?>codigo_barras" value="<?php echo (isset($producto["codigo_barras"])) ? $producto["codigo_barras"] : ""; ?>" class="form-control">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label>ID producto</label>
										<div class="row">
											<div class="col-sm-5">
												<!--<span class="input-group-addon" id="pref_code_prod" style="background: #dedede;"></span> -->
												<input type="text" name="pref_codigo_producto" id="<?php echo $prefix; ?>pref_codigo_producto" value="<?php echo (isset($producto["pref_codigo_producto"])) ? $producto["pref_codigo_producto"] : ""; ?>" class="form-control">
											</div>
											<div class="col-sm-7">
												<input type="text" name="nro_codigo_producto" id="<?php echo $prefix; ?>nro_codigo_producto" value="<?php echo (isset($producto["nro_codigo_producto"])) ? $producto["nro_codigo_producto"] : ""; ?>" class="form-control">
											</div>
										</div>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label>Producto alterno</label>
										<input type="hidden" name="codigo_alterno" id="<?php echo $prefix; ?>codigo_alterno" value="<?php echo (isset($producto["codigo_alterno"])) ? $producto["codigo_alterno"] : ""; ?>">
										<input type="text" name="producto_alterno" id="<?php echo $prefix; ?>producto_alterno" value="<?php echo (isset($producto_alterno["descripcion_detallada"])) ? $producto_alterno["descripcion_detallada"] : ""; ?>" class="form-control">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-6">
									<div class="row">
										<div class="col-sm-4">
											<div class="form-group">
												<label>Controla stock</label>
												<div class="onoffswitch">
													<input type="checkbox" name="controla_stock" id="<?php echo $prefix; ?>controla_stock" class="onoffswitch-checkbox" value="1" <?php echo isset($producto["controla_stock"]) ? ($producto["controla_stock"] == 'S') ? "checked" : "" : "checked"; ?>>
													<label class="onoffswitch-label" for="<?php echo $prefix; ?>controla_stock">
														<span class="onoffswitch-inner"></span>
														<span class="onoffswitch-switch"></span>
													</label>
												</div>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label>Controla serie</label>
												<div class="onoffswitch">
													<input type="checkbox" name="controla_serie" id="<?php echo $prefix; ?>controla_serie" class="onoffswitch-checkbox" value="1" <?php echo (isset($producto["controla_serie"]) && $producto["controla_serie"] == 'S') ? "checked" : ""; ?>>
													<label class="onoffswitch-label" for="<?php echo $prefix; ?>controla_serie">
														<span class="onoffswitch-inner"></span>
														<span class="onoffswitch-switch"></span>
													</label>
												</div>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label>Aplica IGV</label>
												<div class="onoffswitch">
													<input type="checkbox" name="aplica_igv" id="<?php echo $prefix; ?>aplica_igv" class="onoffswitch-checkbox" value="1" <?php echo (isset($producto["aplica_igv"]) && $producto["aplica_igv"] == 'S') ? "checked" : ""; ?>>
													<label class="onoffswitch-label" for="<?php echo $prefix; ?>aplica_igv">
														<span class="onoffswitch-inner"></span>
														<span class="onoffswitch-switch"></span>
													</label>
												</div>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label>Aplica ICBPER</label>
												<div class="onoffswitch">
													<input type="checkbox" name="aplica_icbper" id="<?php echo $prefix; ?>aplica_icbper" class="onoffswitch-checkbox" value="1" <?php echo (isset($producto["aplica_icbper"]) && $producto["aplica_icbper"] == 'S') ? "checked" : ""; ?>>
													<label class="onoffswitch-label" for="<?php echo $prefix; ?>aplica_icbper">
														<span class="onoffswitch-inner"></span>
														<span class="onoffswitch-switch"></span>
													</label>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Codigo Anterior</label>
										<input type="text" name="codigo_anterior" id="<?php echo $prefix; ?>codigo_anterior" value="<?php echo (isset($producto["codigo_anterior"])) ? $producto["codigo_anterior"] : ""; ?>" class="form-control">
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Factor</label>
										<input type="text" name="factor" id="<?php echo $prefix; ?>factor" value="<?php echo (isset($producto["factor"])) ? $producto["factor"] : ""; ?>" class="form-control numero">
									</div>
								</div>
							</div>
						</div>
					</div>
					<div id="tab-2" class="tab-pane">
						<div class="panel-body">
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label>Color</label>
										<input type="hidden" name="idcolor" id="<?php echo $prefix; ?>idcolor" value="<?php echo (isset($producto["idcolor"])) ? $producto["idcolor"] : ""; ?>">
										<input type="text" name="color" id="<?php echo $prefix; ?>color" value="<?php echo (isset($color["descripcion"])) ? $color["descripcion"] : ""; ?>" class="form-control">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label>Material</label>
										<input type="hidden" name="idmaterial" id="<?php echo $prefix; ?>idmaterial" value="<?php echo (isset($producto["idmaterial"])) ? $producto["idmaterial"] : ""; ?>">
										<input type="text" name="material" id="<?php echo $prefix; ?>material" value="<?php echo (isset($material["descripcion"])) ? $material["descripcion"] : ""; ?>" class="form-control">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label>Tama&ntilde;o</label>
										<input type="hidden" name="idtamanio" id="<?php echo $prefix; ?>idtamanio" value="<?php echo (isset($producto["idtamanio"])) ? $producto["idtamanio"] : ""; ?>">
										<input type="text" name="tamanio" id="<?php echo $prefix; ?>tamanio" value="<?php echo (isset($tamanio["descripcion"])) ? $tamanio["descripcion"] : ""; ?>" class="form-control">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label>Peso</label>
										<input type="text" name="peso" id="<?php echo $prefix; ?>peso" value="<?php echo (isset($producto["peso"])) ? $producto["peso"] : ""; ?>" class="form-control">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-6">
									<div class="row">
										<div class="col-sm-6">
											<div class="form-group">
												<label>Stock m&iacute;nimo</label>
												<input type="text" name="stock_minimo" id="<?php echo $prefix; ?>stock_minimo" value="<?php echo (isset($producto["stock_minimo"])) ? $producto["stock_minimo"] : ""; ?>" class="form-control int-number">
											</div>
										</div>
										<div class="col-sm-6">
											<div class="form-group">
												<label>Stock m&aacute;ximo</label>
												<input type="text" name="stock_maximo" id="<?php echo $prefix; ?>stock_maximo" value="<?php echo (isset($producto["stock_maximo"])) ? $producto["stock_maximo"] : ""; ?>" class="form-control int-number">
											</div>
										</div>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label>Generar alerta stock</label>
										<div class="onoffswitch">
											<input type="checkbox" id="<?php echo $prefix; ?>genera_alerta_stock" class="onoffswitch-checkbox" value="1" <?php echo (isset($producto["genera_alerta_stock"]) && $producto["genera_alerta_stock"] == 'S') ? "checked" : ""; ?>>
											<label class="onoffswitch-label" for="<?php echo $prefix; ?>genera_alerta_stock">
												<span class="onoffswitch-inner"></span>
												<span class="onoffswitch-switch"></span>
											</label>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div id="tab-3" class="tab-pane">
						<!-- unidades de medida -->
						<div class="panel-body">
							<div class="row">
								<div class="col-sm-8">
									<div class="input-group">
										<select id="combo_asignar_unidad_medidad" class="input-sm form-control input-s-sm inline"><?php echo $unidades; ?></select>
										<span class="input-group-btn tooltip-demo">
											<button type="button" class="btn btn-outline btn-primary btn-sm btn-registrar-unidad" data-toggle="tooltip" title="Registrar unidad de medida">
												<i class="fa fa-edit"></i>
											</button>
										</span>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="btn-group">
										<button id="btn-asignar-unidad" class="btn btn-sm btn-white parent" type="button">Agregar</button>
									</div>
								</div>
							</div>
							<div class="clients-list">
								<div class="">
									<div class="table-responsive" style="">
										<table border="0" class="tabla_modulos table table-striped tabla_unidad_medida"><?php echo (!empty($tr_unidad))?$tr_unidad:''; ?></table>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div id="tab-4" class="tab-pane">
						<!-- precios -->
						<div class="panel-body">
							<div class="row">
								<div class="col-md-6">
									<label>Precio compra unitario</label>
									<input type="text" name="precio_compra" id="<?php echo $prefix; ?>precio_compra" value="<?php echo (isset($precio["precio_compra"])) ? $precio["precio_compra"] : ""; ?>" class="form-control float-number">
								</div>
								<div class="col-md-6">
									<label>Precio venta unitario</label>
									<input type="text" name="precio_venta" id="<?php echo $prefix; ?>precio_venta" value="<?php echo (isset($precio["precio_venta"])) ? $precio["precio_venta"] : ""; ?>" class="form-control float-number">
								</div>
							</div>
							<div class="row" style="margin-top:10px;">
								<div class="col-md-12">
									<div class="panel panel-default">
										<div class="panel-heading">Precios de venta</div>
										<div class="panel-body">
											<div class="row">
												<div class="col-md-4">
													<button class="btn btn-white btn-sm btn-block" id="add_precio_venta"><i class="fa fa-plus"></i> Agregar nuevo precio de venta</button>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<table class="table table-stripped table-bordered table_precio_producto_venta">
														<thead>
															<tr>
																<th>Unidad Med.</th>
																<!--<th>Moneda</th>-->
																<th>Cantidad</th>
																<th>Precio</th>
																<th class="tooltip-demo">Porcentaje 
																	<i class="fa fa-info-circle" data-toggle="tooltip" title="Porcentaje para el calculo del precio de venta a partir del precio de compra"></i>
																</th>
																<th>&nbsp;</th>
															</tr>
														</thead>
														<tbody><?php echo (!empty($tr_venta))?$tr_venta:''; ?></tbody>
													</table>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!--<div id="tab-5" class="tab-pane">
						<!-- combos 
						<div class="panel-body"></div>
					</div>-->
					<div id="tab-6" class="tab-pane">
						<!-- imagenes -->
						<div class="panel-body"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="form-group">
			<!--<div class="col-lg-offset-10 col-lg-2">-->
			<div class="col-lg-12">
				<button id="<?php echo $prefix; ?>btn_cancel" class="btn btn-sm btn-white btn_cancel<?php echo $modal?" modal-form":""; ?>" data-controller="<?php echo $controller; ?>">Cancelar</button>
				<button type="submit" id="<?php echo $prefix; ?>btn_save" class="btn btn-sm btn-primary" data-controller="<?php echo $controller; ?>">Guardar</button>
			</div>
		</div>
	</div>
</form>

<div id="modal-linea" class="modal fade" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Registrar linea</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<?php echo $form_linea; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="modal-categoria" class="modal fade" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Registrar categoria</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<?php echo $form_categoria; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="modal-marca" class="modal fade" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Registrar marca</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<?php echo $form_marca; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="modal-unidad" class="modal fade" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Registrar unidad de medida</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<?php echo $form_unidad; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="modal-modelo" class="modal fade" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Registrar modelo</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<?php echo $form_modelo; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="modal-color" class="modal fade" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Registrar color</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<?php echo $form_color; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="modal-material" class="modal fade" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Registrar material</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<?php echo $form_material; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="modal-tamanio" class="modal fade" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Registrar tama&ntilde;os o tallas</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<?php echo $form_tamanio; ?>
				</div>
			</div>
		</div>
	</div>
</div>