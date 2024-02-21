<div class="row">	
	<div class="col-sm-12">
		<!-- datos del credito -->
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5><div>Lista de clientes con credito <span class="label label-default pull-right" id="nro_credito_ref"></span></div></h5>
				<div class="pull-right">
					<div class="ibox-tools">
						<span style="font-size:10px;font-weight:bold;" id="cant_clientes">0 CLIENTES</span>
						<a class="collapse-link">
							<i class="fa fa-chevron-up"></i>
						</a>
					</div>
				</div>
				<div class="pull-right" id=""><h2 id="central_riesgo" class="no-margins text-danger text-center"></h2></div>
			</div>
			<div class="ibox-content">
				<div class="row">
					<table class="table table-striped"  style="width:calc(100% - 0px);margin-bottom: 0px;">
						<thead>
							<tr>
								<th width="2%"    class="text-center">Cod</th>
								<th width="12.5%" class="text-center">Cliente</th>
								<th width="4%"    class="text-center">L. Cred</th>
								<th width="4%"    class="text-center">Deuda</th>
								<th width="4%"    class="text-center">Disponible</th>
								<th width="9%"    class="text-center">Telefono</th>
								<th width="8%"    class="text-center">Zona</th>
								<th width="16%"   class="text-center">Direccion</th>
								<th width="1%"></th>
							</tr>
						</thead>
						
						<tbody>
							<tr>
								<td></td>
								<td><input type="text" id="search_cliente" class="input-xs form-control"/></td>
								<td></td>
								<td></td>
								<td></td>
								<td><input type="text" id="search_telefono" class="input-xs form-control"/></td>
								<td><input type="text" id="search_zona" class="input-xs form-control"/></td>
								<td><input type="text" id="search_direccion" class="input-xs form-control"/></td>
								<td></td>
							</tr>
						</tbody>
					</table>
					<div style="width:100%;height:140px;overflow-x:hidden;overflow-y:scroll;">
						<table id="table-clientes" class="table table-bordered detail-table no-header-background">
							<thead>
								<tr>
									<th width="2%"></th>
									<th width="12%"></th>
									<th width="4%"></th>
									<th width="4%"></th>
									<th width="4%"></th>
									<th width="9%"></th>
									<th width="8%"></th>
									<th width="16%"></th>
									<th style="display:none;"></th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</div>
		</div>

		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5>Lista de Creditos <span id="status"></span></h5>
				<div class="ibox-tools">
					<a class="dropdown-toggle tooltip-demo" id="ver" href="#" style="margin-right:15px;">						
						<i class="fa fa-eye" data-toggle="tooltip" title="Ver detalle de credito" style="color: black; font-weight: bold;" data-placement="bottom"></i>
					</a>					
					
					<a class="dropdown-toggle tooltip-demo" id="exportar" href="#" title="">						
						<i class="fa fa-file-excel-o" data-toggle="tooltip" style="color:#055d05;font-weight: bold;" title="Exportar" data-placement="bottom"></i>
					</a>
					
					<a class="dropdown-toggle tooltip-demo" data-toggle="dropdown" href="#" >						
						<i class="fa fa-search" data-toggle="tooltip" title="Filtro" style="color: #028db7; font-weight: bold;" data-placement="bottom"></i>
					</a>
					<ul class="dropdown-menu dropdown-user">
						<li><a class="filter" valor="T" text="" href="#" id=""><i class="fa fa-download"></i> Ver todos los creditos</a></li>
						<li><a class="filter" valor="S" text="Pagados" href="#" id=""><i class="fa fa-rotate-right"></i> Ver Solo Pagados</a></li>
						<li><a class="filter" valor="N" text="Pendientes" href="#" id=""><i class="fa fa-rotate-right"></i> Ver Solo Pendientes</a></li>
					</ul>
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div class="ibox-content">
				<!-- tabla credito -->
				<div class="row">
					<div class="col-md-12">
						<table class="table table-striped"  style="width:calc(100% - 0px);margin-bottom: 0px;">
							<thead>
								<tr>
									<!-- saldo = pagos + nota credito -->
									<?php
									foreach($array_head as $k=>$v){
										echo "<th width='{$v[2]}%'>{$v[0]}</th>";
									}
									?>
									<th width="1%"></th>
								</tr>
							</thead>
						</table>
					
						<div style="width:100%;height:105px;overflow-x:hidden;overflow-y:scroll;">
							<table id="table-creditos" class="table table-bordered detail-table no-header-background">
								<thead>
									<tr>
										<?php
										foreach($array_head as $k=>$v){
											echo "<th width='{$v[2]}%'></th>";
										}
										?>
										<th style="display:none;"></th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
						<table class="table table-striped" id="foot-creditos" style="width:calc(100% - 0px);margin-bottom: 0px;">
							<tfoot>
								<tr>
									<th width="8%" class="total_creditos">0 CREDITOS</th>
									<th width="14%"></th>
									<th width="8%"></th>
									<th width="8%"></th>
									<th width="4%"></th>
									<th width="4%"></th>
									<th width="6%" class="text-number tot_importe">0.00</th>
									<th width="6%" class="text-number tot_notacre">0.00</th>
									<th width="6%"></th>
									<th width="6%" class="text-number tot_pagos">0.00</th>
									<th width="8%" class="text-number tot_saldo">0.00</th><!-- saldo = pagos + nota credito -->
									<th width="1%"></th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
				<!-- fin tabla credito -->
			</div>
		</div>
	</div>
</div>

<div id="modal-detalles" class="modal fade" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Informacion detallada</h4>
			</div>
			<div class="modal-body">
				<div class="ibox float-e-margins">
			<!--
			<div class="ibox-title">
				<h5>Datos de la venta</h5>
				<div class="ibox-tools">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			-->
			<div class="ibox-content" style="padding:5px 5px 0px 5px;">
				<div class="row">
				<div class="tabs-container">
					<ul class="nav nav-tabs">
						<li class="active"><a data-toggle="tab" href="#tab-1" aria-expanded="true">Datos ventas</a></li>
						<li class=""><a data-toggle="tab" href="#tab-2" aria-expanded="false">Pagos</a></li>
					</ul>
					<div class="tab-content">
						<div id="tab-1" class="tab-pane active">
							<div class="panel-body">
								<div class="row">
									<div class="col-md-4">
										<div class="form-group">
											<label>Comprobante</label>
											<input type="text" name="venta_comprobante" id="venta_comprobante" class="form-control input-xs" readonly>
										</div>
									</div>
									<div class="col-md-8">
										<div class="form-group">
											<label>Cliente</label>
											<input type="text" name="venta_cliente" id="venta_cliente" class="form-control input-xs" readonly>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-4">
										<div class="form-group">
											<label>Fecha venta</label>
											<input type="text" name="venta_fecha_venta" id="venta_fecha_venta" class="form-control input-xs" readonly>
										</div>
									</div>
									<div class="col-md-8">
										<div class="form-group">
											<label>Direccion</label>
											<input type="text" name="venta_direccion" id="venta_direccion" class="form-control input-xs" readonly>
										</div>
									</div>
								</div>
								
								<div class="row">
									<div class="col-md-12">
										<legend style="margin-bottom: 5px;">Productos | Servicios</legend>
										<table id="table-productos" class="table table-striped no-margins"><tbody></tbody></table>
									</div>
								</div>
							</div>
						</div>
						<div id="tab-2" class="tab-pane">
							<div class="panel-body">
								<div class="row">
									<div class="col-md-12">
										<table id="table-amortizaciones" class="table table-hover no-margins tooltip-demo">
											<thead>
												<tr>
													<th>Fecha</th>
													<!--
													<th>Hora</th>
													-->
													<th>Letra</th>
													<th>Monto</th>
													<th>Mora</th>
													<th>Moneda</th>
													<th>Tipo pago</th>
													<th>Recibo</th>
													<th>Usuario</th>
													<!--
													<th>Sucursal</th>
													-->
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
		</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-white btn-sm" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<input type="hidden" name='ver_credito' id='ver_credito' value='T'/>
<style>
.badge{font-size:12px;}
#table-clientes tbody tr td {font-size:10.4px;}
#table-clientes thead tr th {white-space:nowrap;color:black;}

#table-creditos tbody tr td {font-size:10.4px;}
#foot-creditos tfoot tr th {font-size:10.4px;}
#table-creditos thead tr th {white-space:nowrap;color:black;}
#table-creditos tfoot input[id^=total_] {font-weight:bold;}
#table-creditos .descuento {width:78%;display:inline-block;}
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