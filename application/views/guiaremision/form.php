<?php if($readonly) { ?>
<div class="alert alert-danger">La guia de remision solo se puede modificar en el transcurso del d&iacute;a.</div>
<?php } ?>
<form id="form_<?php echo $controller; ?>" class="app-form form-uppercase">
	<input type="hidden" name="idguia_remision" id="idguia_remision" value="<?php echo (!empty($guia_remision["idguia_remision"])) ? $guia_remision["idguia_remision"] : ""; ?>">
	<input type="hidden" name="tipo_guia" id="tipo_guia" value="<?php echo (!empty($guia_remision["tipo_guia"])) ? $guia_remision["tipo_guia"] : ""; ?>">
	<input type="hidden" name="idtransporte" id="idtransporte" value="<?php echo (!empty($guia_remision["idtransporte"])) ? $guia_remision["idtransporte"] : ""; ?>">
	<input type="hidden" name="referencia" id="referencia" value="<?php echo (!empty($guia_remision["referencia"])) ? $guia_remision["referencia"] : ""; ?>">
	<input type="hidden" name="idreferencia" id="idreferencia" value="<?php echo (!empty($guia_remision["idreferencia"])) ? $guia_remision["idreferencia"] : ""; ?>">
	<input type="hidden" name="idubigeo_partida" id="idubigeo_partida" value="<?php echo (!empty($guia_remision["idubigeo_partida"])) ? $guia_remision["idubigeo_partida"] : ""; ?>">
	<input type="hidden" name="idubigeo_llegada" id="idubigeo_llegada" value="<?php echo (!empty($guia_remision["idubigeo_llegada"])) ? $guia_remision["idubigeo_llegada"] : ""; ?>">
	
	<?php if($anulado == true) { ?>
	<div class="row">
		<div class="form-group">
			<div class="col-sm-6">
				<div class="block_content mensajillo" style="">ANULADO</div>
			</div>
		</div>
	</div>
	<?php }?>
	
	<div class="row">
		<div class="col-sm-6">
			<!--<div class="panel panel-default">
				<div class="panel-body">-->
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label class="control-label">N&uacute;mero de guia</label>
								<div class="input-group">
									<span class="input-group-btn">
										<?php 
										if($guia_remision["tipo_guia"] == "S") {
											echo $serie;
										} else {
										?>
										<input type="text" name="serie" id="serie" class="form-control" value="<?php echo (!empty($guia_remision["serie"])) ? $guia_remision["serie"] : ""; ?>" style="width: 100px;" placeholder="serie">
										<?php } ?>
									</span>
									<input type="text" name="numero" id="numero" class="form-control" value="<?php echo (!empty($guia_remision["numero"])) ? $guia_remision["numero"] : ""; ?>" <?php echo ($guia_remision["tipo_guia"] == "S") ? 'readonly':''; ?>>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label">Motivo</label>
								<?php echo $motivo;?>
							</div>
						</div>
						<div class="col-sm-6 row_buscar_detalle">
							<label class="control-label" style="display:block;">Buscar</label>
							<div class="btn-group">
								<button id="btn-buscar-guia" class="btn btn-info btn-sm"><i class="fa fa-search"></i> Guia</button>
								<button id="btn-buscar-venta" class="btn btn-info btn-sm"><i class="fa fa-search"></i> Venta</button>
								<button id="btn-buscar-compra" class="btn btn-info btn-sm"><i class="fa fa-search"></i> Compra</button>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label">Fecha traslado</label>
								<input type="text" name="fecha_traslado" id="fecha_traslado" class="form-control" value="<?php echo (!empty($guia_remision["fecha_traslado"])) ? $guia_remision["fecha_traslado"] : date("d/m/Y"); ?>" placeholder="dd/mm/yyyy">
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label">Costo m&iacute;nimo</label>
								<input type="text" name="costo_minimo" id="costo_minimo" class="form-control" value="<?php echo (!empty($guia_remision["costo_minimo"])) ? $guia_remision["costo_minimo"] : ""; ?>">
							</div>
						</div>
					</div>
				<!--</div>
			</div>-->
		</div>
		
		<div class="col-sm-6">
			<div class="panel panel-default">
				<div class="panel-heading">Destinatario 
					<div class="btn-group pull-right">
						<button id="btn-cliente" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> Cliente</button>
						<button id="btn-proveedor" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> Proveedor</button>
					</div>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label class="control-label">Nombres / Razon social</label>
								<input type="text" name="destinatario" id="destinatario" class="form-control" value="<?php echo (!empty($guia_remision["destinatario"])) ? $guia_remision["destinatario"] : ""; ?>">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label">RUC</label>
								<input type="text" name="ruc_destinatario" id="ruc_destinatario" class="form-control" value="<?php echo (!empty($guia_remision["ruc_destinatario"])) ? $guia_remision["ruc_destinatario"] : ""; ?>" maxlength="11">
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label">DNI</label>
								<input type="text" name="dni_destinatario" id="dni_destinatario" class="form-control" value="<?php echo (!empty($guia_remision["dni_destinatario"])) ? $guia_remision["dni_destinatario"] : ""; ?>" maxlength="8">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<!-- row partidad, llegada -->
	<div class="row">
		<div class="col-sm-6">
			<div class="panel panel-default">
				<div class="panel-heading">Partida <button class="btn btn-primary btn-xs pull-right btn_ubigeo" data-dir="partida"><i class="fa fa-search"></i> Ubigeo</button></div>
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label class="control-label">Punto partida</label>
								<input type="text" name="punto_partida" id="punto_partida" class="form-control" value="<?php echo (!empty($guia_remision["punto_partida"])) ? $guia_remision["punto_partida"] : ""; ?>">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-4">
							<div class="form-group">
								<label class="control-label">Distrito</label>
								<input type="text" name="distrito_partida" id="distrito_partida" class="form-control" value="<?php echo (!empty($partida["distrito"])) ? $partida["distrito"] : ""; ?>" readonly>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label class="control-label">Provincia</label>
								<input type="text" name="provincia_partida" id="provincia_partida" class="form-control" value="<?php echo (!empty($partida["provincia"])) ? $partida["provincia"] : ""; ?>" readonly>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label class="control-label">Departamento</label>
								<input type="text" name="departamento_partidad" id="departamento_partida" class="form-control" value="<?php echo (!empty($partida["departamento"])) ? $partida["departamento"] : ""; ?>" readonly>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="col-sm-6">
			<div class="panel panel-default">
				<div class="panel-heading">Llegada <button class="btn btn-primary btn-xs pull-right btn_ubigeo" data-dir="llegada"><i class="fa fa-search"></i> Ubigeo</button></div>
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label class="control-label">Punto llegada</label>
								<input type="text" name="punto_llegada" id="punto_llegada" class="form-control" value="<?php echo (!empty($guia_remision["punto_llegada"])) ? $guia_remision["punto_llegada"] : ""; ?>">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-4">
							<div class="form-group">
								<label class="control-label">Distrito</label>
								<input type="text" name="distrito_llegada" id="distrito_llegada" class="form-control" value="<?php echo (!empty($llegada["distrito"])) ? $llegada["distrito"] : ""; ?>" readonly>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label class="control-label">Provincia</label>
								<input type="text" name="provincia_llegada" id="provincia_llegada" class="form-control" value="<?php echo (!empty($llegada["provincia"])) ? $llegada["provincia"] : ""; ?>" readonly>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label class="control-label">Departamento</label>
								<input type="text" name="departamento_llegada" id="departamento_llegada" class="form-control" value="<?php echo (!empty($llegada["departamento"])) ? $llegada["departamento"] : ""; ?>" readonly>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- fin row partida, llegada -->
	
	<!-- row destinatario, unidad transporte, empresa transporte -->
	<div class="row">
		<div class="col-sm-6">
			<div class="panel panel-default">
				<div class="panel-heading">Unidad de transporte y conductor
					<div class="btn-group pull-right">
						<button id="btn-b-chofer" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> Buscar</button>
						<button id="btn-n-chofer" class="btn btn-white btn-xs"><i class="fa fa-file-o"></i> Nuevo</button>
					</div>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label class="control-label">Chofer</label>
								<input type="text" name="chofer" id="chofer" class="form-control" value="<?php echo (!empty($guia_remision["chofer"])) ? $guia_remision["chofer"] : ""; ?>">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label">Licencia de conducir</label>
								<input type="text" name="lic_conducir" id="lic_conducir" class="form-control" value="<?php echo (!empty($guia_remision["lic_conducir"])) ? $guia_remision["lic_conducir"] : ""; ?>">
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label">Marca y nro. de placa</label>
								<input type="text" name="marca_nroplaca" id="marca_nroplaca" class="form-control" value="<?php echo (!empty($guia_remision["marca_nroplaca"])) ? $guia_remision["marca_nroplaca"] : ""; ?>">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label class="control-label">Constancia de inscripci&oacute;n</label>
								<input type="text" name="const_inscripcion" id="const_inscripcion" class="form-control" value="<?php echo (!empty($guia_remision["const_inscripcion"])) ? $guia_remision["const_inscripcion"] : ""; ?>">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="col-sm-6">
			<div class="panel panel-default">
				<div class="panel-heading">Empresa de transporte
					<div class="btn-group pull-right">
						<button id="btn-b-transporte" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> Buscar</button>
						<button id="btn-n-transporte" class="btn btn-white btn-xs"><i class="fa fa-file-o"></i> Nuevo</button>
					</div>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label class="control-label">Nombre / Razon social</label>
								<input type="text" name="transporte" id="transporte" class="form-control" value="<?php echo (!empty($guia_remision["transporte"])) ? $guia_remision["transporte"] : ""; ?>">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label class="control-label">RUC</label>
								<input type="text" name="ruc_transporte" id="ruc_transporte" class="form-control" value="<?php echo (!empty($guia_remision["ruc_transporte"])) ? $guia_remision["ruc_transporte"] : ""; ?>">
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="form-group">
						<label class="control-label">Tipo y Nro. del comprobante de pago</label>
						<input type="text" name="comprobante_pago" id="comprobante_pago" class="form-control" value="<?php echo (!empty($guia_remision["comprobante_pago"])) ? $guia_remision["comprobante_pago"] : ""; ?>">
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- fin row destinatario -->
	
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">Detalle guia</div>
				<div class="panel-body">
					<div class="row m-b-sm m-t-sm row_buscar_detalle">
						<div class="col-md-2 libre_item libre_item_almacen"><?php echo $almacen; ?></div>
						<div class="col-md-6 libre_item">
							<input type="hidden" id="producto_idproducto">
							<input type="hidden" id="producto_has_serie">
							<input type="hidden" id="producto_idunidad">
							<input type="hidden" id="producto_idalmacen">
							<input type="hidden" id="producto_serie">
							<div class="input-group tooltip-demo">
								<span class="input-group-addon" data-toggle="tooltip" title="Buscar por serie o c&oacute;digo de barras">
									<input type="checkbox" value="1" id="buscar_serie" name="buscar_serie">
								</span>
								<input type="text" name="producto" id="producto_descripcion" class="form-control" placeholder="Ingrese el nombre o codigo del producto">
							</div>
						</div>
					</div>
					
					<div class="table-responsive">
						<table id="tbl-detalle" class="table table-striped tooltip-demo detail-table">
							<thead>
								<tr>
									<th>Producto</th>
									<th style="width:10%">U.Med.</th>
									<th style="width:10%">Cant.</th>
									<th style="width:10%">Peso</th>
									<th style="width:5%">Serie</th>
									<th style="width:5%">&nbsp;</th>
									<th style="display:none;"></th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="form-group">
			<div class="col-sm-6 text-left">
				<button class="btn btn-sm btn-warning btn_cancel" data-controller="<?php echo $controller; ?>">Cancelar</button>
			</div>
			<div class="col-sm-6 text-right">
				<?php if($readonly == false && $anulado == false) { ?>
				<button id="btn_save_guiaremision" class="btn btn-sm btn-primary" data-controller="<?php echo $controller; ?>">Guardar</button>
				<?php } ?>
			</div>
		</div>
	</div>
</form>

<div id="modal-transporte" class="modal fade" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Registrar transporte</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<?php echo $form_transporte; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="modal-chofer" class="modal fade" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Registrar conductor</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<?php echo $form_chofer; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="modal-product-list" class="modal fade" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Resultado de la b&uacute;squeda</h4>
			</div>
			<div class="modal-body">
				<p>Se han encontrado <span class="count-result-list"></span> resultados. Seleccione el item que corresponde.</p>
				<div class="list-group result-list"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<div id="modal-series" class="modal fade" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"></h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<div class="input-group">
							<input id="input-text-serie" placeholder="Ingrese la serie" class="input-sm form-control text-uppercase" type="text" />
							<span class="input-group-btn">
								<button id="btn-search-serie" type="button" class="btn btn-sm btn-warning"><i class="fa fa-search"></i> Buscar</button>
							</span>
						</div>
					</div>
				</div>
				<div class="table-responsive div_scroll" style="max-height:300px;">
					<table id="table-serie" class="table table-striped">
						<tbody></tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="btn-close-serie" class="btn btn-primary">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<?php echo $ubigeo; ?>

<div id="modal-cliente-direccion" class="modal fade" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Escoger direcci&oacute;n destinatario</h4>
			</div>
			<div class="modal-body">
				<div class="list-group result-list"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<script>var $idtipodocumento = '<?php echo $idtipodocumento; ?>';</script>
<script>var $tipo_guia = '<?php echo $guia_remision["tipo_guia"]; ?>';</script>
<script>var $nuevo = <?php echo $nuevo ? 'true':'false'; ?>;</script>
<?php if(isset($detalle)) { ?>
<script>var $data_detalle = <?php echo json_encode($detalle); ?>;</script>
<?php } ?>