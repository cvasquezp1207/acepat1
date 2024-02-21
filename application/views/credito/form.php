<?php if($has_amortizacion) { ?>
<div class="alert alert-danger">
	No se puede modificar el credito, debido a que se han realizado amortizaciones. Para modificar el credito primero elimine las amortizaciones.
</div>
<?php } ?>

<form id="form_<?php echo $controller; ?>" class="app-form">
	<input type="hidden" name="idcredito" id="idcredito" value="<?php echo (!empty($credito["idcredito"])) ? $credito["idcredito"] : ""; ?>">
	<input type="hidden" name="idventa" id="idventa" value="<?php echo (!empty($credito["idventa"])) ? $credito["idventa"] : ""; ?>">
	<input type="hidden" name="idcliente" id="idcliente" value="<?php echo (!empty($credito["idcliente"])) ? $credito["idcliente"] : ""; ?>">
	<input type="hidden" name="idgarante" id="idgarante" value="<?php echo (!empty($credito["idgarante"])) ? $credito["idgarante"] : ""; ?>">
	<input type="hidden" name="nro_credito" id="nro_credito" value="<?php echo (!empty($credito["nro_credito"])) ? $credito["nro_credito"] : ""; ?>">
	<input type="hidden" name="idmoneda" id="idmoneda" value="<?php echo (!empty($credito["idmoneda"])) ? $credito["idmoneda"] : ""; ?>">
	
	<div id="tab-credito" class="tabs-container">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#tab-1" aria-expanded="true">Datos b&aacute;sicos</a></li>
			<li class=""><a href="#tab-2" aria-expanded="false">Requisitos del cr&eacute;dito</a></li>
			<li class=""><a href="#tab-3" aria-expanded="false">Generar cronograma</a></li>
		</ul>
		<div class="tab-content">
			<div id="tab-1" class="tab-pane active">
				<div class="panel-body">
					<div class="row">
						<div class="col-md-12">
							<div class="panel panel-default">
								<div class="panel-heading">Datos de la venta</div>
								<div class="panel-body">
									<div class="row">
										<div class="col-md-3">
											<div class="form-group">
												<label class="control-label">Tipo documento</label>
												<input type="text" id="venta_tipo_documento" name="venta_tipo_documento" class="form-control" value="<?php echo (!empty($venta["tipo_documento"])) ? $venta["tipo_documento"] : ""; ?>" readonly>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="control-label">Numero documento</label>
												<input type="text" id="venta_numero_documento" name="venta_numero_documento" class="form-control" value="<?php echo (!empty($venta["serie"])) ? $venta["serie"]."-".$venta["correlativo"] : ""; ?>" readonly>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="control-label">Moneda</label>
												<input type="text" id="venta_moneda" name="venta_moneda" class="form-control" value="<?php echo (!empty($venta["moneda"])) ? $venta["moneda"] : ""; ?>" readonly>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="control-label">Total venta</label>
												<input type="text" id="monto_facturado" name="monto_facturado" class="form-control" value="<?php echo (!empty($credito["monto_facturado"])) ? $credito["monto_facturado"] : ""; ?>" readonly>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Cliente</label>
								<div class="input-group">
									<input type="text" name="credito_cliente" id="credito_cliente" value="<?php echo (!empty($credito_view["cliente"])) ? $credito_view["cliente"] : ""; ?>" class="form-control" readonly>
									<span class="input-group-btn tooltip-demo">
										<!--
										<button type="button" id="btn-buscar-cliente" class="btn btn-outline btn-primary" data-toggle="tooltip" title="Buscar clientes">
											<i class="fa fa-search"></i>
										</button>
										<button type="button" id="btn-registrar-cliente" class="btn btn-outline btn-primary" data-toggle="tooltip" title="&iquest;No existe el cliente? Registrar aqui">
											<i class="fa fa-edit"></i>
										</button>
										-->
										<button type="button" id="btn-buscar-cliente" class="btn btn-outline btn-primary" data-toggle="tooltip" title="Buscar clientes">
											<i class="fa fa-search"></i>
										</button>
										<button type="button" id="btn-registrar-cliente" class="btn btn-outline btn-primary" data-toggle="tooltip" title="&iquest;No existe el cliente? Registrar aqui">
											<i class="fa fa-file-o"></i>
										</button>
										
										<button type="button" id="btn-edit-cliente" class="btn btn-outline btn-primary" data-toggle="tooltip" title="Editar Cliente">
											<i class="fa fa-edit"></i>
										</button>
									</span>
								</div>
							</div>
						</div>
						
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Garante</label>
								<div class="input-group">
									<input type="text" name="credito_garante" id="credito_garante" value="<?php echo (!empty($credito_view["garante"])) ? $credito_view["garante"] : ""; ?>" class="form-control" readonly>
									<span class="input-group-btn tooltip-demo">
										<button type="button" id="btn-buscar-garante" class="btn btn-outline btn-primary" data-toggle="tooltip" title="Buscar garantes">
											<i class="fa fa-search"></i>
										</button>
										<!--
										<button type="button" id="btn-registrar-garante" class="btn btn-outline btn-primary" data-toggle="tooltip" title="&iquest;No existe el garante? Registrar aqui">
											<i class="fa fa-edit"></i>
										</button>
										-->
										<button type="button" id="btn-registrar-garante" class="btn btn-outline btn-primary" data-toggle="tooltip" title="&iquest;No existe el Garante? Registrar aqui">
											<i class="fa fa-file-o"></i>
										</button>
										
										<button type="button" id="btn-edit-garante" class="btn btn-outline btn-primary" data-toggle="tooltip" title="Editar Garante">
											<i class="fa fa-edit"></i>
										</button>
									</span>
								</div>
							</div>
						</div>
					</div>
					
					<!--
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">Garante</label>
								<div class="input-group">
									<input type="text" name="credito_garante" id="credito_garante" value="<?php echo (!empty($credito_view["garante"])) ? $credito_view["garante"] : ""; ?>" class="form-control" readonly>
									<span class="input-group-btn tooltip-demo">
										<button type="button" id="btn-buscar-garante" class="btn btn-outline btn-primary" data-toggle="tooltip" title="Buscar garantes">
											<i class="fa fa-search"></i>
										</button>
										<button type="button" id="btn-registrar-garante" class="btn btn-outline btn-primary" data-toggle="tooltip" title="&iquest;No existe el garante? Registrar aqui">
											<i class="fa fa-edit"></i>
										</button>
									</span>
								</div>
							</div>
						</div>
					</div>
					-->
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">Inicial</label>
								<input type="text" name="inicial" id="inicial" value="<?php echo (!empty($credito["inicial"])) ? $credito["inicial"] : ""; ?>" class="form-control numerillo" placeholder="0.00"/>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12" id="info-saldo-cliente"></div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group text-right">
								<button class="btn btn-white btn_cancel" data-controller="<?php echo $controller; ?>">Cancelar</button>
								<button class="btn btn-primary btn_next_tab" data-tab="1">Siguiente</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="tab-2" class="tab-pane">
				<div class="panel-body">
					<div class="row">
						<div class="col-md-12">
							<!--<ul class="todo-list m-t small-list">
								<?php
								// if(!empty($requisitos)) {
									// foreach($requisitos as $val) {
										// echo '<li data-obligatorio="'.$val["obligatorio"].'" data-solicita_ficheros="'.$val["solicita_ficheros"].'"';
										// echo 'data-cantidad="'.$val["cantidad"].'" data-idrequisito_credito="'.$val["idrequisito_credito"].'">';
										// echo '<a href="#" class="check-link"><i class="fa fa-square-o"></i> </a>';
										// echo '<span class="m-l-xs">'.$val["descripcion"].'</span></li>';
									// }
								// }
								?>
							</ul>-->
							<div class="mail-box">
								<table id="table-req" class="table table-hover table-mail">
									<tbody>
									<?php
									if(!empty($requisitos)) {
										foreach($requisitos as $val) {
											$cls = "";
											if($val["obligatorio"] == "S") {
												$cls = " warning";
											}
											echo '<tr class="read'.$cls.'" data-obligatorio="'.$val["obligatorio"].'" data-solicita_ficheros="'.$val["solicita_ficheros"].'"';
											echo 'data-cantidad="'.$val["cantidad"].'" data-idrequisito_credito="'.$val["idrequisito_credito"].'" data-tipo="'.$val["tipo"].'">';
											
											echo '<td class="check-mail"><i class="fa fa-square-o" style="font-size:18px;"></i></td>';
											echo '<td class="mail-subject"><span class="label label-default">'.$val["cantidad"].'</span> <span class="desc-req m-l-xs">'.$val["descripcion"].'</span></td>';
											echo '<td class="project-people"></td>';
											if($val["solicita_ficheros"] == 'S') {
												echo '<td><a href="#" class="btn btn-white btn-xs btn-add-req"><i class="fa fa-paperclip"></i> Adjuntar</a></td>';
											}
											else {
												echo '<td><a href="#" class="btn btn-white btn-xs btn-confirm-req"><i class="fa fa-check"></i> Confirmar</a></td>';
											}
											
											echo '<td style="display:none;">';
											echo '<input type="checkbox" name="idrequisito_credito[]" class="idrequisito_credito" value="'.$val["idrequisito_credito"].'" />';
											echo '</td>';
											
											echo '</tr>';
										}
									}
									?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group text-right">
								<button class="btn btn-white btn_cancel_credito">Cancelar</button>
								<button class="btn btn-default btn_prev_tab" data-tab="2">Atras</button>
								<button class="btn btn-primary btn_next_tab" data-tab="2">Siguiente</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="tab-3" class="tab-pane">
				<div class="panel-body">
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">Capital</label>
								<input type="text" name="capital" id="capital" class="form-control" value="<?php echo (!empty($credito["capital"])) ? $credito["capital"] : ""; ?>" readonly />
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">Tipo de credito</label>
								<?php echo $tipo_credito; ?>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">Forma de pago</label>
								<?php echo $ciclo; ?>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">Numero de letras</label>
								<input type="text" name="nro_letras" id="nro_letras" class="form-control" value="<?php echo (!empty($credito["nro_letras"])) ? $credito["nro_letras"] : "1"; ?>" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">Tasa</label>
								<input type="text" name="tasa" id="tasa" class="form-control" value="<?php echo (!empty($credito["tasa"])) ? $credito["tasa"] : "0.00"; ?>" />
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">Fecha inicio</label>
								<input type="text" name="fecha_inicio" id="fecha_inicio" class="form-control"  value="<?php echo date('d/m/Y');?>" />
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">Gastos</label>
								<input type="text" name="gasto" id="gasto" class="form-control" />
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label style="display:block;">&nbsp;</label>
								<button class="btn btn-primary btn-sm btn-block" id="btn_generarletras"><i class="fa fa-cogs"></i> Generar Cronograma</button>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label tooltip-demo">D&iacute;as de gracia
								<i class="fa fa-info-circle text-muted" data-toggle="tooltip" title="D&iacute;as de atrazo en el pago para el c&aacute;lculo de la mora."></i></label>
								<input type="text" name="dias_gracia" id="dias_gracia" class="form-control" value="<?php echo (isset($credito["dias_gracia"])) ? $credito["dias_gracia"] : ""; ?>" />
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">Estado credito</label>
								<?php echo $estado_credito; ?>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label tooltip-demo">Genera mora
								<i class="fa fa-info-circle text-muted" data-toggle="tooltip" title="Cumplido los d&iacute;as de gracia, el sistema empezar&aacute; a generar mora."></i></label>
								<?php 
								$checked = "";
								if(isset($credito["genera_mora"])) {
									$checked = ($credito["genera_mora"] == 'S') ? "checked" : "";
								}
								?>
								<div class="onoffswitch">
									<input type="checkbox" name="genera_mora" id="genera_mora" class="onoffswitch-checkbox" value="1" <?php echo $checked; ?>>
									<label class="onoffswitch-label" for="genera_mora">
										<span class="onoffswitch-inner"></span>
										<span class="onoffswitch-switch"></span>
									</label>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<table id="table-cronograma" class="table table-bordered">
								<thead>
									<tr>
										<th>Letra</th>
										<th>Fec.Vencimiento</th>
										<th>Amortizaci&oacute;n</th>
										<th>Interes</th>
										<th>Cuota</th>
										<th>Gastos</th>
										<th>Total</th>
										<th style="display:none;"></th>
									</tr>
								</thead>
								<tbody>
								<?php 
								$a = $m = "";
								if( ! empty($letras)) {
									$a = $m = 0;
									foreach($letras as $row) {
										$cuota = $row["amortizacion"] + $row["interes"];
										$a += $row["amortizacion"];
										$m += $cuota;
										
										echo "<tr index='".$row["idletra"]."'>";
										echo "<td><input type='text' class='form-control input-sm letra' name='letra[]' value='".$row["idletra"]."' readonly></td>";
										echo "<td><input type='text' class='form-control input-sm fecha_vencimiento' name='fecha_vencimiento[]' value='".$row["fecha"]."'></td>";
										echo "<td><input type='text' class='form-control input-sm amortizacion' name='amortizacion[]' value='".$row["amortizacion"]."' readonly></td>";
										echo "<td><input type='text' class='form-control input-sm interes' name='interes[]' value='".$row["interes"]."' readonly></td>";
										echo "<td><input type='text' class='form-control input-sm monto' name='monto[]' value='".$cuota."' readonly></td>";
										echo "<td><input type='text' class='form-control input-sm gastos' name='gastos[]' value='".$row["gastos"]."' readonly></td>";
										echo "<td><input type='text' class='form-control input-sm total' name='total[]' value='".$row["total"]."'></td>";
										echo "<td style='display:none;'><input type='hidden' class='interes_temp' name='interes_temp[]' value='".$row["interes"]."'></td>";
										echo "</tr>";
									}
								}
								?>
								</tbody>
								<tfoot>
									<tr>
										<td colspan="2" class="text-right"><strong>TOTALES</strong></td>
										<td><input type='text' class='form-control input-sm total_amortizacion' name='total_amortizacion' value="<?php echo $a;?>" readonly></td>
										<td><input type='text' class='form-control input-sm total_interes' name='total_interes' value="<?php echo (isset($credito["interes"])) ? $credito["interes"] : ""; ?>" readonly></td>
										<td><input type='text' class='form-control input-sm total_monto' name='total_monto' value="<?php echo $m;?>" readonly></td>
										<td><input type='text' class='form-control input-sm total_gastos' name='total_gastos' value="<?php echo (isset($credito["gastos"])) ? $credito["gastos"] : ""; ?>" readonly></td>
										<td><input type='text' class='form-control input-sm total_total' name='total_total' value="<?php echo (isset($credito["monto_credito"])) ? $credito["monto_credito"] : ""; ?>" readonly></td>
										<td style="display:none;"></td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group text-right">
								<button class="btn btn-white btn_cancel_credito">Cancelar</button>
								<button class="btn btn-default btn_prev_tab" data-tab="3">Atras</button>
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
</form>
<div id="modal-upload_req" class="modal fade" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"></h4>
			</div>
			<div class="modal-body">
				<form id="form-upload" enctype="multipart/form-data">
					<div class="row">
						<div class="col-md-5">
							<div class="form-group">
								<input type="file" name="file" id="file_input" style="width:100%;" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx">
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<input type="text" class="form-control input-xs" name="file_nombre" id="file_nombre" placeholder="Nombre del archivo">
							</div>
						</div>
						<div class="col-md-3">
							<button id="btn-upload" class="btn btn-primary btn-xs">Subir archivo</button>
						</div>
					</div>
				</form>
				<div class="row">
					<div class="col-md-12">
						<div id="file-req" class="attachment clearfix"></div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<div id="modal-cliente" class="modal fade" data-keyboard="false" aria-hidden="true" aria-labelledby="myLargeModalCliente" data-backdrop="static">
	<div class="modal-dialog modal-lg" style="margin-top: 10px;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="title_modal_ref">Registrar Cliente</h4>
			</div>
			<div class="modal-body" style="padding: 0px 30px 0px 29px;">
				<div class="row">
					<?php echo $form_cliente; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<input type="hidden" name="linea_credito" id="linea_credito" />
<input type="hidden" name="limite_credito" id="limite_credito" />
<input type="hidden" name="saldo_cliente" id="saldo_cliente" />
<input type="hidden" name="redirect" id="redirect" value="<?php echo $redirect; ?>" />