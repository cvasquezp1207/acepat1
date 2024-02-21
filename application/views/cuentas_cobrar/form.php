<div class="row wrapper border-bottom white-bg page-heading fixed-button-top form-inline">
	<div class="col-sm-4 text-left">
		<div class="form-group no-margins">
			<label>Buscar</label>
			<div class="input-group">
				<span class="input-group-btn">
					<select name="filter" id="filter" class="form-control input-xs" style="width:120px;">
						<option value="D">DNI</option>
						<option value="R">RUC</option>
						<option value="N">NOMBRES | RAZON SOCIAL</option>
						<option value="A">APELLIDOS Y NOMBRES</option>
						<option value="C">CREDITO</option>
					</select>
				</span>
				<input type="text" name="search" id="search" placeholder="Texto a buscar" class="form-control input-xs">
				<input type="hidden" name="credito_idcliente" id="credito_idcliente">
				<input type="hidden" name="credito_idventa" id="credito_idventa">
			</div>
		</div>
	</div>
	<div class="col-sm-3 text-left">
		<div class="form-group no-margins">
			<label>Nro. Cr&eacute;dito</label>
			<select name="nro_credito" id="nro_credito" class="form-control input-xs" style="width:99px;"></select>
		</div>
	</div>
	<div class="col-sm-4 text-right">
		<div class="btn-group">
			<button type="button" id="btn-amortizaciones" class="btn btn-white btn-xs"><i class="fa fa-folder-open"></i> Amortizaciones</button>
			<div class="btn-group">
				<button type="button" class="btn btn-white btn-xs dropdown-toggle" data-toggle="dropdown">
					<i class="fa fa-print"></i> Imprimir <span class="caret"></span></button>
				<ul class="dropdown-menu">
					<li><a href="#" class="btn-print" id="print-cronograma" data-uri="cronograma">Cronograma</a></li>
				</ul>
			</div>
			<div class="btn-group">
				<button type="button" class="btn btn-white btn-xs dropdown-toggle" data-toggle="dropdown">
					<i class="fa fa-edit"></i> Modificar <span class="caret"></span></button>
				<ul class="dropdown-menu">
					<li><a href="#" class="btn-change" data-target="estado">Cambiar estado</a></li>
					<!--<li><a href="#" class="btn-change" data-target="cliente">Cambiar cliente</a></li>-->
					<li><a href="#" class="btn-change" data-target="garante">Cambiar garante</a></li>
				</ul>
			</div>
			<!--<button type="button" class="btn btn-white btn-xs"><i class="fa fa-cogs"></i> Refinanciar</button>-->
		</div>
	</div>
</div>

<div class="row">
	<div class="col-sm-4">
		<!--<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5><span class="widget navy-bg">1</span> Buscar cliente | cr&eacute;dito</h5>
				<div class="ibox-tools">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div class="ibox-content">
				<div class="form-group no-margins">
					<label>Buscar segun</label>
					<div class="input-group">
						<span class="input-group-btn">
							<select name="filter" id="filter" class="form-control input-xs" style="width:120px;">
								<option value="D">DNI</option>
								<option value="R">RUC</option>
								<option value="N">NOMBRES | RAZON SOCIAL</option>
								<option value="A">APELLIDOS Y NOMBRES</option>
								<option value="C">CREDITO</option>
							</select>
						</span>
						<input type="text" name="search" id="search" placeholder="Texto a buscar" class="form-control input-xs">
						<input type="hidden" name="credito_idcliente" id="credito_idcliente">
					</div>
				</div>
			</div>
		</div>-->
		<!-- datos de la venta -->
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5>Datos de la venta</h5>
				<div class="ibox-tools">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div class="ibox-content">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label>Comprobante</label>
							<input type="text" name="venta_comprobante" id="venta_comprobante" class="form-control input-xs" readonly>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label>Sucursal</label>
							<input type="text" name="venta_sucursal" id="venta_sucursal" class="form-control input-xs" readonly>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label>Cliente</label>
							<input type="text" name="venta_cliente" id="venta_cliente" class="form-control input-xs" readonly>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label>Vendedor</label>
							<input type="text" name="venta_empleado" id="venta_empleado" class="form-control input-xs" readonly>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label>Garante</label>
							<input type="text" name="garante_credito" id="garante_credito" class="form-control input-xs" readonly>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label>Cobrador</label>
							<!-- <input type="text" name="venta_cobrador" id="venta_cobrador" class="form-control input-xs" readonly> -->
							<select id="idcobrador" name="idcobrador" class="form-control input-xs" ></select>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label>Fecha de venta</label>
							<input type="text" name="venta_fecha_venta" id="venta_fecha_venta" class="form-control input-xs" readonly>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- detalle de la venta -->
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5>Productos | Servicios</h5>
				<div class="ibox-tools">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div class="ibox-content">
				<table id="table-productos" class="table table-striped no-margins"><tbody></tbody></table>
			</div>
		</div>
		<!-- bloque de botones de consultas -->
		<!--<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5>Consultas | Opciones</h5>
				<div class="ibox-tools">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div class="ibox-content">
				<div class="row">
					<div class="col-md-12">
						<button class="btn btn-white btn-rounded btn-block"><i class="fa fa-money"></i> Ver amortizaciones</button>
						<button class="btn btn-white btn-sm btn-rounded btn-block"><i class="fa fa-print"></i> Imprimir cronograma</button>
						<button class="btn btn-white btn-sm btn-rounded btn-block"><i class="fa fa-toggle-on"></i> Cambiar estado del credito</button>
						<button class="btn btn-white btn-sm btn-rounded btn-block"><i class="fa fa-edit"></i> Cambiar cliente</button>
						<button class="btn btn-white btn-sm btn-rounded btn-block"><i class="fa fa-edit"></i> Cambiar garante</button>
						<button class="btn btn-white btn-sm btn-rounded btn-block"><i class="fa fa-files-o"></i> Refinanciar</button>
					</div>
				</div>
			</div>
		</div>-->
	</div>
	
	<div class="col-sm-8">
		<!-- datos del credito -->
		<div class="ibox float-e-margins">
			<!--
			<div class="ibox-title">
				<h5>Datos del cr&eacute;dito</h5>
				<div class="ibox-tools">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div class="ibox-content">
				<div class="row">
					<div class="col-md-9">
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label>Nro. Cr&eacute;dito</label>
									<input name="nro_credito_ref" id="nro_credito_ref" class="form-control input-xs" readonly>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Estado</label>
									<input type="text" name="estado_credito" id="estado_credito" class="form-control input-xs text-success" readonly>
								</div>
							</div>
							<div class="col-md-4"><h2 id="central_riesgo" class="no-margins text-danger text-center"></h2></div>
						</div>
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<label>Tasa interes</label>
									<input type="text" name="tasa" id="tasa" class="form-control input-xs" readonly>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>Cliente</label>
									<input type="text" name="cliente_credito" id="cliente_credito" class="form-control input-xs" readonly>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label class="tooltip-demo">Valor mora <i class="fa fa-question-circle text-muted" data-toggle="tooltip" title="Ingrese un valor para la mora luego presione Enter"></i></label>
									<input type="text" name="valor_mora" id="valor_mora" class="form-control input-xs" value="<?php echo $valor_mora; ?>">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-9">
								<div class="form-group">
									<label>Observaciones</label>
									<textarea name="descripcion" id="descripcion" class="form-control input-xs"></textarea>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label style="display:block;">&nbsp;</label>
									<button id="btn_save_observacion" class="btn btn-white btn-xs" style="white-space:normal;vertical-align:bottom;">Grabar observaci&oacute;n</button>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<ul class="list-group clear-list m-t">
                            <li class="list-group-item fist-item"><span id="nro_letras" class="badge badge-success"></span>Total letras</li>
                            <li class="list-group-item "><span id="letras_canceladas" class="badge badge-primary"></span>Letras canceladas</li>
                            <li class="list-group-item"><span id="letras_pendientes" class="badge badge-danger"></span>Letras pendientes</li>
                            <li class="list-group-item"><span id="dias_gracia" class="badge badge-info"></span>Dias de gracia</li>
                            <li class="list-group-item"><span id="genera_mora" class="badge badge-warning"></span>Genera mora</li>
                        </ul>
					</div>
				</div>
			</div>
			-->
			
			<div class="ibox-title">
				<h5><div>Datos del Cr&eacute;dito <span class="label label-default pull-right" id="nro_credito_ref"></span></div></h5>
				<div class="pull-right">
					<div class="ibox-tools">
						<a class="collapse-link">
							<i class="fa fa-chevron-up"></i>
						</a>
					</div>
				</div>
				<div class="pull-right" id=""><h2 id="central_riesgo" class="no-margins text-danger text-center"></h2></div>
			</div>
			<div class="ibox-content">
				<div class="row">
					<div class="col-md-9">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<label>Estado</label>
									<input type="text" name="estado_credito" id="estado_credito" class="form-control input-xs text-success" readonly>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label>Tasa interes</label>
									<input type="text" name="tasa" id="tasa" class="form-control input-xs" readonly>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>Cliente</label>
									<input type="text" name="cliente_credito" id="cliente_credito" class="form-control input-xs" readonly>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<label>Fecha emisi&oacute;n</label>
									<input type="text" name="credito_fecha_emision" id="credito_fecha_emision" class="form-control input-xs" readonly>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label>Dias de atrazo</label>
									<input type="text" name="credito_dias_atrazo" id="credito_dias_atrazo" class="form-control input-xs" readonly>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>Direcci&oacute;n</label>
									<input type="text" name="cliente_direccion" id="cliente_direccion" class="form-control input-xs" readonly>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<label class="tooltip-demo">Valor mora <i class="fa fa-question-circle text-muted" data-toggle="tooltip" title="Ingrese un valor para la mora luego presione Enter"></i></label>
									<input type="text" name="valor_mora" id="valor_mora" class="form-control input-xs" value="<?php echo $valor_mora; ?>">
									<!-- <button id="btn_save_observacion" class="btn btn-white btn-xs" style="white-space:normal;vertical-align:bottom;">Grabar observaci&oacute;n</button> -->
								</div>
							</div>
							
							<div class="col-md-9">
								<div class="form-group">
									<label>Observaciones</label>
									<div class="input-group">
										<textarea name="descripcion" id="descripcion" class="form-control input-xs"></textarea>
										<span class="input-group-btn">
											<button id="btn_save_observacion" class="btn btn-white btn-xs" style="white-space:normal;vertical-align:bottom;font-size:13.4px;">Grabar observaci&oacute;n</button> 
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<ul class="list-group clear-list">
                            <li class="list-group-item fist-item"><span id="nro_letras" class="badge badge-success"></span>Total letras</li>
                            <li class="list-group-item "><span id="letras_canceladas" class="badge badge-primary"></span>Letras canceladas</li>
                            <li class="list-group-item"><span id="letras_pendientes" class="badge badge-danger"></span>Letras pendientes</li>
                            <li class="list-group-item"><span id="dias_gracia" class="badge badge-info"></span>Dias de gracia</li>
                            <li class="list-group-item"><span id="genera_mora" class="badge badge-warning"></span>Genera mora</li>
                        </ul>
					</div>
				</div>
			</div>
		</div>
		<!-- metodos de pago -->
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<!--<h5><span class="widget navy-bg">3</span> Letras y amortizaciones</h5>-->
				<h5>Letras y amortizaciones</h5>
				<div class="ibox-tools">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div class="ibox-content">
				<div class="tabs-container">
					<ul class="nav nav-tabs">
						<li class="active"><a data-toggle="tab" href="#tab-1" aria-expanded="true">Realizar pagos</a></li>
						<li class=""><a data-toggle="tab" href="#tab-2" aria-expanded="false">Canjear recibos</a></li>
					</ul>
					<div class="tab-content">
						<div id="tab-1" class="tab-pane active">
							<div class="panel-body">
								<form id="form-pago">
									<input type="hidden" name="idmoneda" id="idmoneda">
									<div class="row" style="position:relative">
										<div class="col-md-4">
											<div class="form-group no-margins">Moneda <span class="label label-default credito_moneda"></span></div>
										</div>
										<div class="col-md-5">
											<div class="form-group text-right no-margins">Recibo de Ingreso N&deg;</div>
										</div>
										<div class="col-md-3">
											<div class="form-group no-margins">
												<div class="input-group">
													<span class="input-group-btn">
														<select name="serie" id="serie" class="form-control input-xs" style="width:70px;"></select>
													</span>
													<input type="text" name="correlativo" id="correlativo" class="form-control input-xs" title="Doble clic para actualizar el correlativo" >
												</div>
											</div>
										</div>
										<div class="block_pago"></div>
									</div>
									<div class="row" style="position:relative;">
										<div class="col-md-3">
											<div class="form-group no-margins">
												<label>Forma de pago</label>
												<?php echo $tipopago; ?>
											</div>
										</div>
										<div id="div_fecha_pago" class="col-md-3" style="display:none;">
											<div class="form-group no-margins">
												<label>Fecha pago</label>
												<input type="text" name="fecha_pago" id="fecha_pago" class="form-control input-xs" placeholder="<?php echo date("d/m/Y"); ?>">
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group no-margins">
												<label class="tooltip-demo">Letras <i class="fa fa-question-circle text-muted" data-toggle="tooltip" title="Ingrese la cantidad de letras a pagar y presione Enter"></i></label>
												<input type="text" name="letras_pagar" id="letras_pagar" class="form-control input-xs">
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group no-margins">
												<label class="tooltip-demo">Monto <i class="fa fa-question-circle text-muted" data-toggle="tooltip" title="Ingrese el monto total a amortizar y presione Enter"></i></label>
												<input type="text" name="monto_pagar" id="monto_pagar" class="form-control input-xs">
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group no-margins">
												<label style="display:block;">&nbsp;</label>
												<button class="btn btn-primary btn-xs" id="btn_save_pago">Guardar</button>
											</div>
										</div>
										<div class="block_pago"></div>
									</div>
								</form>
							</div>
						</div>
						<div id="tab-2" class="tab-pane">
							<div class="panel-body">
								<form id="form-canje">
									<input type="hidden" name="tipo_recibo" id="tipo_recibo">
									<input type="hidden" name="idrecibo" id="idrecibo">
									<div class="row" style="position:relative;">
										<div class="col-md-4">
											<div class="form-group no-margins">Moneda <span class="label label-default credito_moneda"></span></div>
										</div>
										<div class="col-md-8">
											<div class="form-group text-right no-margins">
												<button class="btn btn-white btn-xs" id="btn_search_recibo"><i class="fa fa-search"></i> Recibos de Ingreso</button>
												<button class="btn btn-white btn-xs" id="btn_search_notacredito"><i class="fa fa-search"></i> Notas de credito</button>
											</div>
										</div>
										<!--<div class="col-md-4">
											<div class="form-group no-margins">
												<button class="btn btn-white btn-xs" id="btn_search_cobranza"><i class="fa fa-search"></i> Recibos de Cobranza</button>
											</div>
										</div>-->
										<div class="block_pago"></div>
									</div>
									<div class="row" style="position:relative;">
										<div class="col-md-3">
											<div class="form-group no-margins">
												<label>Documento</label>
												<input type="text" name="numero_recibo" id="numero_recibo" class="form-control input-xs" readonly>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group no-margins">
												<label>Fecha recibo</label>
												<input type="text" name="fecha_recibo" id="fecha_recibo" class="form-control input-xs" readonly>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group no-margins">
												<label>Monto</label>
												<input type="text" name="monto_recibo" id="monto_recibo" class="form-control input-xs" readonly>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group no-margins">
												<label style="display:block;">&nbsp;</label>
												<button class="btn btn-warning btn-xs" id="btn_aplica_canje">Aplicar</button>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group no-margins">
												<label style="display:block;">&nbsp;</label>
												<button class="btn btn-primary btn-xs" id="btn_save_canje">Guardar</button>
											</div>
										</div>
										<div class="block_pago"></div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<!-- tabla credito -->
				<div class="row">
					<div class="col-md-12">
						<form id="form-letras">
							<table id="table-letras" class="table table-bordered detail-table no-header-background">
								<thead>
									<tr>
										<th>Letra</th>
										<th>Fecha Venc.</th>
										<th>Importe</th>
										<th>Amrtz</th>
										<th>Saldo</th>
										<th>Moras</th>
										<th>Dscto.</th>
										<th>Total</th>
										<th>Pagar</th>
										<th style="display:none;"></th>
									</tr>
								</thead>
								<tbody></tbody>
								<tfoot>
									<tr>
										<td colspan="2" class="text-right"><strong>TOTALES</strong></td>
										<td><input type="text" name="total_importe" id="total_importe" class="form-control input-xs text-right" readonly></td>
										<td><input type="text" name="total_amrtz" id="total_amrtz" class="form-control input-xs text-right" readonly></td>
										<td><input type="text" name="total_saldo" id="total_saldo" class="form-control input-xs text-right" readonly></td>
										<td><input type="text" name="total_moras" id="total_moras" class="form-control input-xs text-right text-danger" readonly></td>
										<td><input type="text" name="total_descuento" id="total_descuento" class="form-control input-xs text-right" readonly></td>
										<td><input type="text" name="total_total" id="total_total" class="form-control input-xs text-right text-success" readonly></td>
										<td><input type="text" name="total_pagar" id="total_pagar" class="form-control input-xs text-right text-navy" readonly></td>
										<td style="display:none;"></td>
									</tr>
								</tfoot>
							</table>
						</form>
					</div>
				</div>
				<!-- fin tabla credito -->
			</div>
		</div>
	</div>
</div>
<?php echo $modal_pago; ?>
<input type="hidden" name="current_date" id="current_date" value="<?php echo date("d/m/Y"); ?>">
<input type="hidden" name="dias_mes" id="dias_mes" value="<?php echo $dias_mes; ?>">
<input type="hidden" name="current_mora" id="current_mora" value="<?php echo $valor_mora; ?>">

<div id="modal-estado" class="modal fade" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Cambiar estado del credito</h4>
			</div>
			<div class="modal-body">
				<form id="form-estado">
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label>Estado actual</label>
								<input type="text" name="estado_prev" id="estado_prev" class="form-control input-xs" readonly>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label>Nuevo estado</label>
								<?php echo $estado_credito;?>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-white btn-sm" data-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-primary btn-sm btn-save-modal" data-target="estado" data-action="guardar_estado">Guardar</button>
			</div>
		</div>
	</div>
</div>

<div id="modal-cliente" class="modal fade" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Cambiar cliente</h4>
			</div>
			<div class="modal-body">
				<form id="form-cliente">
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label>Cliente actual</label>
								<input type="text" name="cliente_prev" id="cliente_prev" class="form-control input-xs" readonly>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label>Cliente nuevo</label>
								<div class="input-group">
									<input type="hidden" name="idcliente" id="idcliente">
									<input type="text" name="cliente_new" id="cliente_new" class="form-control input-sm" readonly>
									<span class="input-group-btn tooltip-demo">
										<button type="button" class="btn btn-white btn-sm btn-search-change" data-target="cliente" data-toggle="tooltip" title="Buscar cliente"><i class="fa fa-search"></i></button>
									</span>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-white btn-sm" data-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-primary btn-sm btn-save-modal" data-target="cliente" data-action="guardar_cliente">Guardar</button>
			</div>
		</div>
	</div>
</div>

<div id="modal-garante" class="modal fade" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Cambiar garante</h4>
			</div>
			<div class="modal-body">
				<form id="form-garante">
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label>Garante actual</label>
								<input type="text" name="garante_prev" id="garante_prev" class="form-control input-xs" readonly>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label>Garante nuevo</label>
								<div class="input-group">
									<input type="hidden" name="idgarante" id="idgarante">
									<input type="text" name="garante_new" id="garante_new" class="form-control input-sm" readonly>
									<span class="input-group-btn tooltip-demo">
										<button type="button" class="btn btn-white btn-sm btn-search-change" data-target="garante" data-toggle="tooltip" title="Buscar garante"><i class="fa fa-search"></i></button>
									</span>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-white btn-sm" data-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-primary btn-sm btn-save-modal" data-target="garante" data-action="guardar_garante">Guardar</button>
			</div>
		</div>
	</div>
</div>

<div id="modal-amortizaciones" class="modal fade" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Amortizaciones</h4>
			</div>
			<div class="modal-body">
				<table id="table-amortizaciones" class="table table-hover no-margins tooltip-demo">
					<thead>
						<tr>
							<th>Fecha</th>
							<th>Hora</th>
							<th>Letra</th>
							<th>Monto</th>
							<th>Mora</th>
							<th>Moneda</th>
							<th>Tipo pago</th>
							<th>Nro. documento</th>
							<th>Usuario</th>
							<th>Sucursal</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-white btn-sm" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<style>
.badge{font-size:12px;}
#table-letras thead tr th {white-space:nowrap;color:black;}
#table-letras tfoot input[id^=total_] {font-weight:bold;}
#table-letras .descuento {width:78%;display:inline-block;}
.widget {padding: 4px 10px;}
.label#nro_credito_ref{font-size:14px;}
.list-group{margin-bottom:0px;}
.list-group.clear-list .list-group-item{padding: 9px 0;}
.block_pago {position: absolute;background: #000;opacity: 0.2;left: 0;width: 100%;bottom: 0;height: 100%;z-index:100;}
.wrapper-content {
    padding: 5px 10px 40px;
}
</style>