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
		</div>
	</div>
</div>

<div class="row">
	<div class="col-sm-4">
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
							<input type="text" name="venta_cobrador" id="venta_cobrador" class="form-control input-xs" readonly>
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
	</div>
	
	<div class="col-sm-8">
		<!-- datos del credito -->
		<div class="ibox float-e-margins">			
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
				<h5>Letras y amortizaciones</h5>
				<div class="ibox-tools">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div class="ibox-content">
				<!-- tabla credito -->
				<div class="row">
					<div class="col-md-12">
						<form id="form-letras">
							<table id="table-letras" class="table table-bordered detail-table no-header-background">
								<thead>
									<tr>
										<th width="8%">Letra</th>
										<th width="10%">Fecha Venc.</th>
										<th width="10%">Fecha Pago.</th>
										<th width="9%">Cuota</th>
										<th width="9%">Gastos</th>
										<th width="9%">Monto</th>
										<th width="9%">Moras</th>
										<th width="9%">Dscto.</th>
										<th width="9%">Total</th>
										<th width="16%">Recibo</th>
										<th style="display:none;"></th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</form>
					</div>
				</div>
				<!-- fin tabla credito -->
			</div>
		</div>
	</div>
</div>

<input type="hidden" name="current_date" id="current_date" value="<?php echo date("d/m/Y"); ?>">

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
.input-cronograma{width:50px;}
</style>