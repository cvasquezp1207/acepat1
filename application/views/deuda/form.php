<?php if($has_amortizacion) { ?>
<div class="alert alert-danger">
	No se puede modificar el credito, debido a que se han realizado amortizaciones. Para modificar el credito primero elimine las amortizaciones.
</div>
<?php } ?>

<div class="row tooltip-demo">
	<input type="hidden" name="iddeuda" id="iddeuda" value="<?php echo (!empty($deuda["iddeuda"])) ? $deuda["iddeuda"] : ""; ?>">
	<div class="col-sm-4">
		<div class="ibox">
			<div class="ibox-title">
				<h5>Datos de la compra</h5>
			</div>

			<div class="ibox-content">
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							<label class="control-label required">ProveedoR</label>
							<input type="text" name="deuda_proveedor" id="deuda_proveedor" value="<?php echo (!empty($deuda["proveedor"])) ? $deuda["proveedor"] : ""; ?>" class="form-control input-xs">
							<input type="hidden" name="idproveedor" id="idproveedor" value="<?php echo (!empty($deuda["idproveedor"])) ? $deuda["idproveedor"] : ""; ?>">
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-sm-7">
						<div class="form-group">
							<label class="control-label">RUC</label>
							<div class="input-group">
								<input type="text" name="proveedor_ruc" id="proveedor_ruc" value="<?php echo (!empty($deuda["ruc"])) ? $deuda["ruc"] : ""; ?>" class="form-control input-xs" readonly="">
								<span class="input-group-btn tooltip-demo">
									<button type="button" id="btn-buscar-proveedor" class="btn btn-outline btn-primary btn-xs" data-toggle="tooltip" title="Buscar Proveedor">
										<i class="fa fa-search"></i>
									</button>
								</span>
							</div>
						</div>
					</div>
					
					<div class="col-md-5">
						<div class="form-group">
							<label class="control-label">Moneda Compra</label>
							<?php echo $moneda;?>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-7">
						<div class="form-group">
							<label class="control-label">Monto Facturado</label>
							<input type="text" name="monto" id="monto" class="form-control numerillo input-xs" readonly="" value="<?php echo (!empty($deuda["total_facturado"])) ? $deuda["total_facturado"] : "0.00"; ?>" placeholder="0.00"/>
						</div>
					</div>
					
					<div class="col-md-5">
						<div class="form-group">
							<label class="control-label">T.C Compra</label>
							<input type="text" name="" id="" class="form-control numerillo input-xs" readonly="" value="<?php echo (!empty($deuda[""])) ? $deuda[""] : "3.40"; ?>" placeholder="0.00"/>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-sm-12" >
						<div class="form-group">
							<label class="control-label required">Compras credito</label>
							<select class="form-control id_compras input-xs" multiple="" id="id_compras" name="id_compras[]"></select>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="col-sm-8">
		<div class="row">
			<div class="ibox">
				<div class="ibox-title">
					<h5>Datos de la Deuda</h5>
				</div>

				<div class="ibox-content">
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">Nro Credito</label>
								<input name="nro_credito" id="nro_credito" type="text" class="form-control input-xs" value="<?php echo (!empty($deuda["nro_credito"])) ? $deuda["nro_credito"] : ""; ?>" placeholder="01501000" readonly=""/>
							</div>
						</div>
						
						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label required">Gastos</label>
								<input type="text" name="gastos" id="gastos" class="form-control numerillo input-xs" value="<?php echo (!empty($deuda["gastos"])) ? $deuda["gastos"] : "0.00"; ?>" placeholder="0.00"/>
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label required">Descuento</label>
								<input type="text" name="descuento" id="descuento" class="form-control numerillo input-xs" value="<?php echo (!empty($deuda["descuento"])) ? $deuda["descuento"] : "0.00"; ?>" placeholder="0.00"/>
							</div>
						</div>

						<div class="col-sm-2 ">
							<div class="form-group">
								<label class="control-label tooltip-demo required">Letras <i class="fa fa-info-circle text-muted" data-toggle="tooltip" title="Cantidad de letras que tendrá el credito"></i></label>
								<input type="text" name="cant_letras" id="cant_letras" class="form-control numerillo input-xs" readonly="" value="<?php echo (!empty($deuda["cant_letras"])) ? $deuda["cant_letras"] : ""; ?>" placeholder=""/>
							</div>
						</div>
						
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label required">Fecha Credito</label>
								<div class="input-group date input-group-xs">
									<input name="fecha_deuda" id="fecha_deuda" type="text" class="form-control input-xs" value="<?php echo (!empty($deuda["fecha_deuda"])) ? dateFormat($deuda["fecha_deuda"], "d/m/Y") : date("d/m/Y"); ?>" />
									<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
								</div>
							</div>
						</div>
						
					</div>
					
					<div class="row">
						<div class="col-sm-3 ">
							<div class="form-group">
								<label class="control-label">Moneda</label>
								<?php echo $moneda_deuda;?>
							</div>
						</div>
						
						<div class="col-sm-2 ">
							<div class="form-group">
								<label class="control-label tooltip-demo">T. Cambio <i class="fa fa-info-circle text-muted" data-toggle="tooltip" title="Tipo cambio de la deuda"></i></label>
								<input type="text" name="" id="" class="form-control numerillo input-xs" value="<?php echo (!empty($deuda["cambio_moneda"])) ? $deuda["cambio_moneda"] : ""; ?>" placeholder=""/>
							</div>
						</div>
						
						<div class="col-sm-2 ">
							<div class="form-group">
								<label class="control-label tooltip-demo">Convertido <i class="fa fa-info-circle text-muted" data-toggle="tooltip" title="Monto convertido en Nuevos soles"></i></label>
								<input type="text" name="" id="" class="form-control numerillo input-xs" value="<?php echo (!empty($deuda["total_convertido"])) ? $deuda["total_convertido"] : ""; ?>" placeholder="0.00" readonly="" />
							</div>
						</div>
						
						<div class="col-sm-2 ">
							<div class="form-group">
								<label class="control-label tooltip-demo required">M deuda <i class="fa fa-info-circle text-muted" data-toggle="tooltip" title="Deuda=MontoFacturado+Gastos-Descuento"></i></label>
								<input type="text" name="" id="" class="form-control numerillo input-xs" value="<?php echo (!empty($deuda["monto"])) ? $deuda["monto"] : ""; ?>" placeholder=""/>
							</div>
						</div>
						
						<div class="col-sm-3 ">
							<div class="form-group">
								<label class="control-label">Forma de pago</label>
								<div class="input-group">								
									<input type="text" name="nro_dias" id="nro_dias" class="form-control numerillo input-xs" value="" placeholder="0"/>
									<span class="input-group-btn tooltip-demo">
										<button class="btn btn-primary btn-xs btn-block " id="btn_generarletras" data-toggle="tooltip" title="Agregar forma de pago al detalle"><i class="fa fa-cogs"></i></button>
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="ibox">
			<div class="ibox-title">
				<h5>Letras a pagar</h5>
			</div>
			
			<div class="ibox-content">
				<div class="row">
					<table id="table-cronograma" class="table table-bordered">
						<thead>
							<tr>
								<?php
									foreach($cabecera as $k=>$v){
										echo "<th width='{$v[1]}' class='text-center'>{$v[0]}</th>";
									}
								?>
								<th width="3%" class="text-center"></th>
								<th style="display:none;"></th>
							</tr>
						</thead>
						
						<tbody>
							<?php 
								$a = $m = 0;
								if( ! empty($letras)) {
									$a = $m = 0;
									foreach($letras as $row) {
										$cuota = $row["monto_letra"];
										$m += $cuota;
										
										echo "<tr index='".$row["idletra"]."' forma_pago_compra='{$row['idforma_pago_compra']}'>";
										echo "<td><input type='text' class='form-control input-xs nro_letra' name='nro_letra[]' value='{$row["nro_letra"]}' readonly></td>";
										echo "<td><input type='text' class='form-control input-xs forma_pago_compra' data-dias='{$row['nrodias']}' readonly='' value='F/".$row["nrodias"]." DIAS'></td>";
										echo "<td><input type='text' class='form-control input-xs fecha_vencimiento' name='fecha_vencimiento[]' readonly='' value='".$row["fecha_vencimiento"]."'></td>";
										echo "<td><input type='text' class='form-control input-xs monto_letra numerillo' name='monto_letra[]' value='".$cuota."' readonly></td>";
										echo "<td><input type='text' class='form-control input-xs gastos numerillo' name='gasto[]' value='{$row["gastos"]}' readonly></td>";
										echo "<td><input type='text' class='form-control input-xs descuento numerillo' name='descuento[]' value='{$row["descuento"]}' readonly></td>";
										echo "<td><input type='text' class='form-control input-xs monto_capital numerillo' name='monto_capital[]' value='".$row["monto_capital"]."'></td>";
										echo "<td><button class='btn btn-danger btn-xs btn_deta_delete' data-toggle='tooltip' title='Eliminar registro'><i class='fa fa-trash'></i></button></td>";
										echo "<td style='display:none'>";
										echo "	<input type='text' class='idforma_pago_compra' name='idforma_pago_compra[]' value='".$row["idforma_pago_compra"]."'>";
										echo "	<input type='text' class='nro_dias_formapago' name='nro_dias_formapago[]'  value='".$row["nrodias"]."'>";
										echo "	<input type='text' class='id_referencia' name='id_referencia[]'  value='".$row["id_referencia"]."'>";
										echo "</td>";
										echo "</tr>";
									}
								}
							?>
						</tbody>

						<tfoot>
							<tr>
								<td colspan="3" class="text-right"><strong>TOTALES</strong></td>
								<td><input type='text' class='form-control input-xs total_cuota numerillo' name='' value="<?php echo number_format($m,2,'.',',');?>" readonly placeholder="0.00"></td>
								<td><input type='text' class='form-control input-xs total_gastos numerillo' name='' value="<?php echo (isset($deuda["gastos"])) ? number_format($deuda["gastos"],2,'.',',') : "0.00"; ?>" placeholder="0.00" readonly></td>
								<td><input type='text' class='form-control input-xs total_descuento numerillo' name='' value="<?php echo (isset($deuda["descuento"])) ? number_format($deuda["descuento"],2,'.',',') : "0.00"; ?>" placeholder="0.00" readonly></td>
								<td><input type='text' class='form-control input-xs total_total numerillo' name='' value="<?php echo (isset($deuda["monto"])) ? number_format($deuda["monto"],2,'.',',') : ""; ?>" placeholder="0.00" readonly></td>
								<td style="display:none;"></td>
							</tr>
						</tfoot>
					</table>
				</div>
				
				<div class="row">
					<div class="col-md-12">
						<div class="form-group text-right">
							<button class="btn btn-white btn_cancel_credito">Cancelar</button>
							<?php if($has_amortizacion == false) { ?>
								<button id="btn_save_credito" class="btn btn-primary" data-controller="<?php echo $controller; ?>">Guardar</button>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		</div>
	</div>
</div>
<!--
<form id="form_<?php echo $controller; ?>" class="app-form">	
</form>
-->