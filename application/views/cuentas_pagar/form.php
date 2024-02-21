<form id="form-letras">
<div class="row">
	<div class="col-sm-4">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5>Datos del credito</h5>
				<div class="ibox-tools">
					<a class="collapse-link pull-right">
						<i class="fa fa-chevron-up"></i>
					</a>

					<div class='pull-right'>
						<select class='form-control input-xs' id='pagado'>
							<option value='N'>PENDIENTES</option>
							<option value='S'>PAGADOS</option>
						</select>
					</div>
				</div>
			</div>
			<div class="ibox-content">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label>Buscar</label>
							<div class="input-group">
								<span class="input-group-btn">
									<select name="filter" id="filter" class="form-control input-xs" style="width:120px;">
										<option value="R">RUC</option>
										<option value="N" selected>RAZON SOCIAL</option>
									</select>
								</span>
								<input type="text" name="search" id="search" placeholder="Texto a buscar" class="form-control input-xs">
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label>Proveedor</label>
							<input type="text" name="proveedor" id="proveedor" class="form-control input-xs" readonly>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label>Ruc</label>
							<input type="text" name="ruc" id="ruc" class="form-control input-xs" readonly>
						</div>
					</div>
					
					<div class="col-md-4">
						<div class="form-group">
							<label>Deuda En</label>
							<?php echo $moneda_deuda;?>
						</div>
					</div>
					
					<div class="col-md-4">
						<div class="form-group">
							<label class="tooltip-demo">Monto <i class="fa fa-info-circle text-muted" data-toggle="tooltip" title="Monto Pendiente de pago del credito seleccionado"></i></label>
							<input type="text" name="monto_deuda" id="monto_deuda" class="form-control numerillo input-xs" placeholder='0.00' readonly>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label>Creditos</label>
							<!--
							-->
							<select class="form-control id_creditos" id="id_creditos" name="id_creditos[]" multiple>
							</select>
							<!--
							<div style="width:100%;height:82px;overflow-x:hidden;overflow-y:scroll;border:1px solid #e5e6e7">
								<table id="table-creditos" class="table no-header-background" style="margin-bottom:0px;">
									<tbody>
										<tr><td>C 0039328</td></tr>
										<tr><td>C 0039328</td></tr>
										<tr><td>C 0039328</td></tr>
										<tr><td>C 0039328</td></tr>
										<tr><td>C 0039328</td></tr>
										<tr><td>C 0039328</td></tr>
									</tbody>
								</table>
							</div>
							-->
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label>Comprobantes</label>
							<select class="form-control id_compras" multiple="" id="id_compras" name="id_compras[]">
								<!--
								<option value="1">C 0017328</option>
								<option value="1">C 0017328</option>
								<option value="1">C 0017328</option>
								<option value="1">C 0017328</option>
								<option value="1">C 0017328</option>
								<option value="1">C 0017328</option>
								-->
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5>Forma de Pago</h5>
				<div class="ibox-tools">
					<a class="collapse-link pull-right">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div class="ibox-content">
				<div class="row">
					<div class="col-md-8">
						<div class="form-group">
							<label>Moneda a pagar</label>
							<?php echo $moneda;?>
						</div>
					</div>
					
					<div class="col-md-4">
						<div class="form-group">
							<label>Tipo cambio</label>
							<input type="text" name="cambio_moneda" id="cambio_moneda" class="form-control numerillo input-xs" placeholder='0.00' >
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="col-sm-8">
		<!-- metodos de pago -->
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5>Letras</h5>
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
						
							<input type="hidden" id="credito_idproveedor" name="idproveedor">
							<input type="hidden" id="total_importe_pagar" >
							<table id="table-letras" class="table table-bordered detail-table no-header-background">
								<thead>
									<tr>
										<th width="2%">&nbsp;</th>
										<th width="8%">Credito</th>
										<th width="2%">Letra</th>
										<th width="12%">Fecha Emi.</th>
										<th width="12%">Fecha Venc.</th>
										<th width="12%">Fecha Pago</th>
										<th width="10%">Moneda</th>
										<th width="10%">Pagó con</th>
										<th width="12%">Importe</th>
										<th width="3%">&nbsp;</th>
									</tr>
								</thead>
								<tbody></tbody>
								<tfoot>
									<tr>
										<td colspan="8" class="text-right"><strong>TOTALES <span class="deuda_en"></span></strong></td>
										<td><input type="text" name="total_acumulado" id="total_acumulado" class="form-control input-xs text-right" readonly="" placeholder="0.00"></td>
									</tr>
									
									<tr>
										<td colspan="8" class="text-right"><strong>TOTALES(<span class="text-navy pagar_con">??</span>)</strong></td>
										<td><input type="text" name="total_pagar" id="total_pagar" key-moneda='' class="form-control input-xs text-right" placeholder="0.00"></td>
											<!--
										<td>
											<div class="radio">
                                                <input type="radio" name="radio1" id="radio1" value="option1" checked="">
                                                <label for="radio1"></label>
                                            </div>
										</td>
											-->
									</tr>
									
									<!--
									<tr>
										<td colspan="8" class="text-right"><strong>TOTALES(<span class="text-navy">PEN</span>)</strong></td>
										<td><input type="text" name="total_pen" id="total_pen" key-moneda=1 class="form-control input-xs text-right"></td>
										<td>
											<div class="radio">
                                                <input type="radio" name="radio1" id="radio1" value="option1">
                                                <label for="radio1"></label>
                                            </div>
										</td>
									</tr>
									-->
								</tfoot>
							</table>
						
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-12">
						<button type="button" id="guardar_pago" class="btn btn-success btn-sm">Pagar Letras</button>
					</div>
				</div>
				<!-- fin tabla credito -->
			</div>
		</div>
	</div>
</div>
</form>
<?php echo $modal_pago; ?>
<input type="hidden" name="current_date" id="current_date" value="<?php echo date("d/m/Y"); ?>">

<style>
	#table-creditos tbody > tr > td{
		border-top: 1px solid transparent;
		line-height: 1.42857;
		padding: 0px 12px;
		vertical-align: top;
		font-size: 14.0px;
	}
</style>