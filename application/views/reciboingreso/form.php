<form id="form_<?php echo $controller; ?>" class="form-horizontal app-form form-uppercase">
	<input type="hidden" name="idreciboingreso" id="idreciboingreso" value="<?php echo (!empty($idreciboingreso)) ? $idreciboingreso : ""; ?>">
	<input type="hidden" name="idcliente" id="recibo_idcliente" value="<?php echo (!empty($idcliente)) ? $idcliente: ""; ?>">
	
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
		<div class="col-sm-3" style="border:0px solid red;">
			<label class="control-label required">Nro Recibo</label>
			<div class="row">
				<div class="col-sm-12" style="border:0px solid red;">
					<div class="row">
						<div class="col-sm-6">
							<select class="form-control input-xs" name="serie" id="serie" ></select>
						</div>
						
						<div class="col-sm-6">
							<input type="text" name="numero" id="numero" value="<?php echo (!empty($numero)) ? $numero : ""; ?>" class="form-control input-xs" required="" style="text-align:right;margin-left:0px;" readonly="">
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="col-sm-2" style="border:0px solid red;">
			<label class="control-label required">Tipo Recibo</label>
			<?php echo $tipo_recibo ?>
		</div>
		
		<div class="col-sm-2" style="border:0px solid red;">
			<label class="control-label required">Tipo Pago</label>
			<div>
				<?php echo $tipopago;?>
			</div>
		</div>
		
		
		<div class="col-sm-3" style="border:0px solid red;">
			
			<div class="row">
				<div class="col-sm-6">
					<label class="control-label required">Moneda</label>
					<?php echo $moneda;?>
				</div>
				
				<div class="col-sm-6">
					<div class="">
						<label class="control-label required">T Camb</label>
						<input type="text" name="tipocambio" id="cambio_moneda" class="form-control numero input-xs" value="<?php echo (!empty($tipocambio)) ? $tipocambio : ""; ?>" placeholder="0.00" readonly="" required="">
					</div>
				</div>
			</div>
		</div>
		
		<div class="col-sm-2" style="border:0px solid red;">
			<label class="control-label required">Monto</label>
			<div class="row">
				<div class="col-sm-12">
					<input type="text" name="monto" id="monto" class="form-control numero input-xs" value="<?php echo (!empty($monto)) ? $monto : ""; ?>"  placeholder="0.00" required="">
				</div>
			</div>
		</div>
		
	</div>
	
	<div class="row">
		<div class="col-sm-3" style="border:0px solid red;">
			<label class="control-label" data-toggle='tooltip' title="Seleccione si tiene un comprobante de referencia ">Tipo Documento</label>
			<div class="row">
				<div class="col-sm-12">
					<?php echo $tidocumento; ?>
				</div>
			</div>
		</div>
		<div class="col-sm-9" style="border:0px solid red;">
			<label class="control-label required">Cliente</label>
			<div class="input-group">
				<input type="text" name="cliente" id="cliente_razonsocial" value="<?php echo (!empty($cliente)) ? $cliente : ""; ?>" class="form-control input-xs" required="" placeholder="Nombre, DNI, razon social o RUC">
				<span class="input-group-btn tooltip-demo">
					<button type="button" id="btn-buscar-cliente" class="btn btn-outline btn-primary btn-xs" data-toggle="tooltip" title="Buscar clientes">
						<i class="fa fa-search"></i>
					</button>
					<button type="button" id="btn-registrar-cliente" class="btn btn-outline btn-primary btn-xs" data-toggle="tooltip" title="&iquest;No existe el cliente? Registrar aqui">
						<i class="fa fa-edit"></i>
					</button>
				</span>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-sm-3 tooltip-demo" style="border:0px solid red;">
			<div class="row">
				<!--<div class="col-sm-12">
					<?php echo $tidocumento; ?>
				</div>
				-->
				<div class="col-sm-12" style="border:0px solid red;">
					<div class="row">
						<div class="col-sm-6">
							<label class="control-label" >Serie</label>
							<input type="text" name="serie_doc" id="serie_doc" class="form-control input-xs" value="<?php echo (!empty($serie_doc)) ? $serie_doc : ""; ?>"   placeholder="000" readonly="">
						</div>
						
						<div class="col-sm-6">
							<label class="control-label" >Numero</label>
							<input type="text" name="numero_doc" id="numero_doc" class="form-control input-xs" value="<?php echo (!empty($numero_doc)) ? $numero_doc : ""; ?>"  placeholder="000000" readonly="">
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="col-sm-9" style="border:0px solid red;">
			<label class="control-label required">Concepto</label>
			<textarea class="form-control input-xs" required="" name="concepto" id="concepto" value="<?php echo (!empty($concepto)) ? $concepto : ""; ?>"><?php echo (!empty($concepto)) ? $concepto : ""; ?></textarea>
		</div>
	</div>
	
	<div class="row">
		<div class="form-group">
			<div class="col-lg-offset-4 col-lg-8" style="border:0px solid red;">
				<br>
				<button class="btn btn-sm btn-white btn_cancel" data-controller="<?php echo $controller; ?>">Cancelar</button>
				<?php if($anulado == false) { ?>
				<button id="btn_save_recibo" class="btn btn-sm btn-primary" data-controller="<?php echo $controller; ?>">Guardar</button>
				<?php } ?>
			</div>
		</div>
	</div>
</form>

<div id="modal-cliente" class="modal fade" aria-hidden="true" aria-labelledby="myLargeModalLabel" data-backdrop="static">
	<div class="modal-dialog modal-lg" style="margin-top: 10px;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Registrar Cliente</h4>
			</div>
			<div class="modal-body" style="padding: 0px 30px 0px 29px;">
				<div class="row">
					<?php echo $form_cliente; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php echo $modal_pago; ?>
	<!--
	<div id="modal-form" class="modal fade" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Seleccione Movimiento</h4>
				</div>

				<form id="form_more" class="form-horizontal app-form form-uppercase">
					<div class="modal-body">
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group">
									<label class="required">Movimiento</label>
									<?php //echo $movimiento; ?>
								</div>
							</div>
							
							<div class="col-sm-12">
								<div class="form-group">
									<label class="required">Tipo Pago</label>
									<?php //echo $tipopago_modal;?>
								</div>
							</div>

							<div class="col-sm-12">
								<div class="form-group">
									<div class="table-responsive">
										<table class="table table-striped tabla_tarjeta">
											<tr>
												<td>
													<label class="required">Tarjeta</label>
													<input type="hidden" name="idoperacion" id="idoperacion_tarjeta" type-name="idoperacion">
													<input type="hidden" name="tabla" id="tabla" type-name="tabla">
													<?php //echo $tarjeta;?>
												</td>

												<td>
													<label class="required">Nro Operacion</label>
													<input type="text" name="nro_operacion" id="nro_operaciont" class="form-control" type-name="nro_operacion">
												</td>
											</tr>

											<tr>
												<td>
													<label class="required">Nro Tarjeta</label>
													<input type="text" name="nro_tarjeta" id="nro_tarjeta" class="form-control" type-name="nro_tarjeta" placeholder="****_****_****_0000" >

												</td>

												<td>
													<label class="required">Importe</label>
													<input type="text" name="importe" id="importe" class="form-control numero" readonly="readonly" type-name="importe">
												</td>
											</tr>
										</table>

										<table class="table table-striped tabla_deposito" style="width:98.5% !important;">
											<tr>
												<td width="50%">
													<label class="required">Banco</label>
													<input type="hidden" name="idoperacion" id="idoperacion_deposito" type-name="idoperacion">
													<input type="hidden" name="tabla" id="tabla_deposito" type-name="tabla">
													<?php //echo $cuentas_bancarias;?>
												</td>
												
												<td width="50%">
													<div class="row">
														<div class="col-md-12">
															<label class="required">Fecha Deposito</label>
																<div class="input-group date">
																	<input type="text" name="fecha_deposito" id="fecha_deposito" type-name="fecha_deposito" class="form-control"  placeholder="yy/mm/YY" >
																	<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
																</div>

														</div>
													</div>
												</td>
											</tr>

											<tr>
												<td>
													<label class="required">Nro Operacion</label>
													<input type="text" name="nro_operacion" id="nro_operaciond" type-name="nro_operacion" class="form-control" >
												</td>
												
												<td>
													<label class="required">Importe</label>
													<input type="text" name="importe" id="importe_deposito" class="form-control numero" readonly="readonly" type-name="importe">

												</td>
											</tr>
										</table>
									</div>
								</div>
							</div>

							<div class="col-sm-12">
								<div class="" style="float:right">
									<button class="btn btn-sm btn-white cancel_save" data-dismiss="modal" aria-label="Close"><strong>Cerrar</strong></button>
									<button class="btn btn-sm btn-primary save_data" type="button"><strong>Aceptar</strong></button>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	-->
<script>var $idtipodocumento = '<?php echo $idtipodocumento; ?>';</script>
<style>
	.numero{text-align:right;}
	.form-control#serie{/*padding: 5px 8px;*/}
	.form-control#idmoneda{font-size: 12px;padding: 0px;}
</style>