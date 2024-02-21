<div class="row">
	<div class="col-sm-4">
		<!-- metodos de pago -->
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5>Par&aacute;metros de comisi&oacute;n</h5>
				<div class="ibox-tools">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div id="msg-param-comision" class="ibox-content"></div>
		</div>
	</div>
	<div class="col-sm-8">
		<!-- tabla de comisiones -->
		<div class="ibox">
			<div class="ibox-title">
				<h5>Comisiones</h5>
				<div class="ibox-tools">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div class="ibox-content">
				<form id="frm-filtros">
					<div class="row">
						<div class="col-sm-3">
							<div class="form-group">
								<label class="control-label">Empresa</label>
								<?php echo $empresa;?>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<label class="control-label">Sucursal</label>
								<?php echo $sucursal;?>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<label class="control-label">A&ntilde;o</label>
								<?php echo $anio;?>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<label class="control-label">Mes</label>
								<?php echo $mes;?>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-sm-4">
							<div class="form-group">
								<label class="control-label">Empleado</label>
								<?php echo $empleado;?>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<label class="control-label" style="display:block;">&nbsp;</label>
								<button id="btn-listar" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> Consultar</button>
							</div>
						</div>
						<div id="msg-info-mes" class="col-sm-5"></div>
					</div>
				</form>
				<!-- tabla credito -->
				<div class="row">
					<div class="col-md-12">
						<form id="form-letras">
							<table id="table-letras" class="table table-bordered detail-table no-header-background">
								<thead>
									<tr>
										<th>F. Venta</th>
										<th>Comprobante</th>
										<th>Total. Vta</th>
										<th>Vendedor</th>
										<th>Amrtz.</th>
										<th>F. Amrtz.</th>
										<th>Nro. d&iacute;as</th>
										<th>% Comisi&oacute;n</th>
										<th>Comisi&oacute;n</th>
									</tr>
								</thead>
								<tbody>
									
								</tbody>
								<tfoot>
									<tr>
										<td colspan="8" class="text-right"><strong>TOTAL</strong></td>
										<td><input type="text" name="total_comision" id="total_comision" class="form-control input-xs text-right text-navy" readonly></td>
									</tr>
								</tfoot>
							</table>
						</form>
					</div>
					<div>
						<button type="button" class="btn btn-sm btn-white" data-dismiss="modal">Cancelar</button>
						<button id="btn-save-comision" class="btn btn-sm btn-primary">Guardar</button>
					</div>
				</div>
				<!-- fin tabla credito -->
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
	.chosen-container{font-size:10.5px !important;}
	.chosen-container-single .chosen-single{min-height: 24px !important;}
</style>