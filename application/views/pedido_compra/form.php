<div class="app-form tooltip-demo">
	<div class="panel panel-info">
		<div class="panel-heading">
			<h3 class="panel-title">Generar <small> | generacion de Planillas</small></h3>
		</div>
		<div class="panel-body">
			<div class="row">
				<div class="col-sm-3">
					<form id="form">
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group">
									<label>Rango de fechas</label>
									<div class="input-daterange input-group input-group-xs">
										<span class="input-group-addon">
											<input type="checkbox" name="all_fecha" id="all_fecha" value="1" data-toggle="tooltip" title="Todas las fechas">
										</span>
										<input type="text" class="input-sm form-control" name="fecha_i" id="fecha_i" placeholder="dd/mm/aaaa" autocomplete="off">
										<span class="input-group-addon">hasta</span>
										<input type="text" class="input-sm form-control" name="fecha_f" id="fecha_f" placeholder="dd/mm/aaaa" autocomplete="off">
									</div>
								</div>
							</div>
						</div>
						
						<!--
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group">
									<label>Tiempo reposici&oacute;n</label>
									<div class="input-group input-group-xs">
										<input id="reposicion" name="reposicion" class="form-control input-xs">
										<span class="input-group-addon">D&iacute;as</span>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group">
									<label>Tiempo seguridad</label>
									<div class="input-group input-group-xs">
										<input id="seguridad" name="seguridad" class="form-control input-xs">
										<span class="input-group-addon">D&iacute;as</span>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group">
									<label>Pedido para</label>
									<div class="input-group input-group-xs">
										<input id="para" name="para" class="form-control input-xs">
										<span class="input-group-addon">D&iacute;as</span>
									</div>
								</div>
							</div>
						</div>-->
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group">
									<label>Sucursal</label>
									<select id="idsucursal" name="idsucursal" class="form-control input-xs"><?php echo $combosucursal;?></select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group">
									<label>Tipo</label>
									<select id="descripcion" name="descripcion" class="form-control input-xs"><?php echo $combotipotercero;?></select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group">
									<label>Bancos</label>
									<select id="entidadbancaria" name="entidadbancaria" class="form-control input-xs"><?php echo $combotipopedido;?></select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group">
									<button id="btn-consultar" class="btn btn-xs btn-primary btn-block btn-rounded">
										<i class="fa fa-search"></i> Consultar</button>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group">
									<button id="btn-generar" class="btn btn-xs btn-primary btn-block btn-rounded">
										<i class="fa fa-check"></i> Cancelar Planillas</button>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group">
									<button id="btn-print" class="btn btn-xs btn-primary btn-block btn-rounded">
										<i class="fa fa-print"></i> Imprimir</button>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group">
									<button id="btn-excel" class="btn btn-xs btn-primary btn-block btn-rounded">
										<i class="fa fa-file-excel-o"></i> Exportar a Excel</button>
								</div>
							</div>
						</div>
					</form>
				</div>
				<div class="col-sm-9">
				<!--
					<div class="row">
						<div class="col-sm-9">
							<h4 class="example-title">Proveedores</h4>
						</div>
						<div class="col-sm-3 text-right">
							<div class="form-group">
								<div class="input-group">
									<input type="text" class="form-control input-xs" id="txtSearchProveedor" placeholder="Buscar en la tabla">
									<span class="input-group-btn">
										<button type="button" class="btn btn-primary btn-xs btn-search-txt-proveedor" tabindex="-1">
											<i class="fa fa-search" aria-hidden="true"></i>
										</button>
									</span>
								</div>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-sm-12">
							<div class="table-responsive" style="overflow-x:hidden;overflow-y:auto;max-height:160px;">
								<table class="table table-striped table-hover table-dark table-proveedor" style="margin:0;">
									<thead class="min-table">
										<tr>
											<th>Razon social</th>
											<th style="width:100px;">Unid. Cr&iacute;ticas</th>
										</tr>
									</thead>
									<tbody></tbody>
								</table>
							</div>
						</div>
					</div>
					-->
					<div class="row" style="margin-top:40px;">
						<div class="col-sm-9">
							<h4 class="example-title">Socios</h4>
						</div>
						<div class="col-sm-3 text-right">
							<div class="form-group">
								<div class="input-group">
									<input type="text" class="form-control input-xs" id="txtSearchProducto" placeholder="Buscar en la tabla">
									<span class="input-group-btn">
										<button type="button" class="btn btn-primary btn-xs btn-search-txt-producto" tabindex="-1">
											<i class="fa fa-search" aria-hidden="true"></i>
										</button>
									</span>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div class="table-responsive" style="overflow-x:hidden;overflow-y:auto;max-height:260px;">
								<table class="table table-striped table-hover table-dark table-producto" style="margin:0;">
									<thead class="min-table">
										<tr>
											<th style="width:20px;">Id Deuda</th>
											<th style="width:20px;">Ticket</th>
											<th style="width:20px;">Fecha</th>
											<th style="width:90px;">Proveedor / Socio</th>
											<th style="width:20px;">Monto</th>
											<th style="width:25px;">Amortizacion</th>
											<th style="width:25px;">Saldo</th>
											<th style="width:30px;">&nbsp;</th>
										</tr>
									</thead>
									<tbody></tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<input type="hidden" id="idsucursal_consultar">
<div id="modal-generar-pedido" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Generaci&oacute;n de Pago</h4>
			</div>
			<div class="modal-body">
				<form id="form-pedido">
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							<label>Entidad Bancaria</label>
							<select id="idtipo_pedido" name="idtipo_pedido" class="form-control input-xs"><?php echo $combotipopedido;?></select>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							<label>Nro de Movimientos</label>
							<textarea type="text" id="descripcion" name="descripcion" class="form-control input-xs" style="resize:none;"></textarea>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							<label>Estado aprobado</label>
							<select id="aprobado" name="aprobado" class="form-control input-xs">
								
								<option value="S">APROBADO</option>
							</select>
						</div>
					</div>
				</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-white btn-sm" data-dismiss="modal">Cancelar</button>
				<button type="button" id="btn-save-pedido" class="btn btn-primary btn-sm">Pagar Planilla</button>
			</div>
		</div>
	</div>
</div>