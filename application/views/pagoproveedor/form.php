<div class="row">
	<div class="col-sm-4">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5>Filtro</h5>
				<div class="ibox-tools">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div class="ibox-content" style="padding: 15px 15px 5px 20px;">
				<form id="form_filtro">
					<div class="form-group">
						<label>Fecha Vencimiento</label>
						<div class="row">
							<div class="col-md-6">
								<div class="input-group date">
									<input type="text" name="fecha_inicio" id="fecha_inicio" class="form-control input-xs"  placeholder="yy/mm/YY" >
									<span class="input-group-addon" style="padding: 3px 8px;"><i class="fa fa-calendar"></i></span>
								</div>
									<!--<input type="text" name="venta_comprobante" id="venta_comprobante" class="form-control input-xs">-->
								
							</div>
							<div class="col-md-6">
								<div class="input-group date">
									<input type="text" name="fecha_fin" id="fecha_fin" class="form-control input-xs"  placeholder="yy/mm/YY" >
									<span class="input-group-addon" style="padding: 3px 8px;"><i class="fa fa-calendar"></i></span>
								</div>
									<!--<input type="text" name="venta_sucursal" id="venta_sucursal" class="form-control input-xs" >-->
								
							</div>
						</div>
					</div>
					
					<div class="form-group">
						<label>Letras</label>
						 <select class="form-control input-xs" id="estado_letra" name="estado_letra">
							<!--<option value="0" class="" >TODOS LAS LETRAS </option>-->
							<option value="N">PENDIENTE</option>
							<option value="S">AMORTIZADO</option>
						</select>
					</div>
					
					<div class="form-group">
						<label>Proveedor</label>
						<div class="input-group">
							<?php echo $proveedor;?>
							<span class="input-group-btn tooltip-demo">
								<button type="button" id="btn-buscar-proveedor" class="btn btn-outline btn-primary" data-toggle="tooltip" title="Buscar Proveedor">
									<i class="fa fa-search"></i>
								</button>
							</span>
						</div>
					</div>
					
					<div class="form-group">
						<center><button class="btn btn-success botoncito btn_print fa fa-search" id="filtrar"> Buscar</button></center>
					</div>
				<!--
				-->
				</form>
			</div>
		</div>
		<!-- bloque de botones de consultas -->
		
		
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5>Forma de Pago</h5>
				<div class="ibox-tools">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div class="ibox-content">
				<form id="form_pago">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label>Tipo Pago</label>
								<?php echo $tipopago;?>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Moneda</label>
								<input type="text" id="descr_moneda" placeholder="Soles" value="" class="form-control input-xs is_numero" readonly>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Tipo Cambio</label>
								<input type="text" name="" id="tipo_cambio" placeholder="0.00" class="form-control input-xs is_numero">
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="label_monto">Monto Bruto</label>
								<input type="text" id="monto_bruto" placeholder="0.00" class="form-control input-xs is_numero" readonly>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Monto Convertido</label>
								<input type="text" name="monto" id="monto_pagar" placeholder="0.00" class="form-control input-xs is_numero" readonly>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<center>
									<button class="btn btn-primary botoncito btn_print fa fa-money boton_pagar" 	id="pagar_letra"> Pagar Letra(s)</button>
									<!--<button class="btn btn-warning botoncito btn_print fa fa-trash   boton_pagar" 		id="eliminar_letra"> Eliminar Letra</button>-->
									<button class="btn btn-success botoncito btn_print fa fa-trash-o boton_anular" 	id="anular_amort" style="display_none"> Anular Pago</button>
								</center>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	
	<div class="col-sm-8">
		<!-- datos del credito -->
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5> Resultado de Busqueda</h5>
				<div class="ibox-tools">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div class="ibox-content" style="padding: 15px 15px 2px 20px;">
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<?php echo $grid;?>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5>Letras seleccionadas</h5>
				<div class="ibox-tools">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div class="ibox-content">
				<form id="form_detalle">				
					<table class="table table-striped" id="table_letas_pago" border=0>
						<thead class="btn-primary">
							<tr>
								<td rowspan=2>&nbsp;</td>
								<td rowspan=2>Comprobante</td>
								<td rowspan=2>Fecha Venc</td>
								<td rowspan=2>Letra</td>
								<td colspan=2 align="center">NCR</td>
								<td rowspan=2>Deuda</td>
								<td rowspan=2>Amortizac</td>
								<td rowspan=2>&nbsp;</td>
							</tr>
							
							<tr>
								<td>Monto Nota</td>
								<td>Doc NCR</td>
							</tr>
						</thead>
						
						<tbody></tbody>
					</table>
				</form>
			</div>
		</div>
	</div>
</div>
<?php echo $modal_pago; ?>
<style>
.badge{font-size:12px;}
#table-letras thead tr th {white-space:nowrap;}
#table-letras tfoot input[id^=total_] {font-weight:bold;}
#table-letras .descuento {width:78%;display:inline-block;}
.widget {padding: 4px 10px;}
.block_pago {position: absolute;background: #000;opacity: 0.2;left: 0;width: 100%;bottom: 0;height: 100%;z-index:100;}
.chosen-container{font-size:12px;}
/*table#table_letas_pago thead{background:;}*/
#dtcronograma_pago_view tbody tr td{font-size:12px}
#dtcronograma_pago_view tbody tr td{padding:2px;font-size:12px}

#table_letas_pago tr td{padding:2px;font-size:12px}
tr.seleccionado{background-color:#08C !important;color:white;}
.input_detalle{width:85px;}
.is_numero{text-align:right;}
</style>