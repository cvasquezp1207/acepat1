<div id="modal-pay" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Opciones de pago</h4>
			</div>
			<div class="modal-body">
				<form id="form-pay-mov" class="form-uppercase">
					<input type="hidden" name="id_moneda_cambio" id="id_moneda_cambio"/>
					<?php if($show_afecta_caja) { ?>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label>&iquest;Afecta caja?</label>
								<div class="onoffswitch">
									<input type="checkbox" name="afecta_caja" class="afecta_caja onoffswitch-checkbox" value="S" checked>
									<label class="onoffswitch-label" for="afecta_caja">
										<span class="onoffswitch-inner"></span>
										<span class="onoffswitch-switch"></span>
									</label>
								</div>
							</div>
						</div>
						<!--<div class="col-sm-6 no_afecta hide">
							<div class="form-group">
								<label>&iquest;Volver a preguntar m&aacute;s tarde?</label>
								<div class="onoffswitch">
									<input type="checkbox" name="preguntar" class="preguntar onoffswitch-checkbox" value="1">
									<label class="onoffswitch-label" for="cobrar">
										<span class="onoffswitch-inner"></span>
										<span class="onoffswitch-switch"></span>
									</label>
								</div>
							</div>
						</div>-->
					</div>
					<?php } else { ?>
						<input type="checkbox" name="afecta_caja" class="afecta_caja" value="S" style="display:none;" checked>
					<?php } ?>
					<div class="row afecta hide">
						<div class="col-sm-12">
							<div class="form-group">
								<label>Movimiento en caja</label>
								<?php echo $movimiento; ?>
							</div>
						</div>
					</div>
					<div class="row afecta hide">
						<div class="col-sm-6">
							<div class="form-group">
								<label>Tipo Pago</label>
								<?php echo $tipopago;?>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label>Total pagar</label>
								<input type="text" name="monto_pagar" class="monto_pagar numerillo form-control" readonly>
								<!--
								<div class="input-group">
									<span class="input-group-addon">USSD</span>
								</div>
								-->
							</div>
						</div>
					</div>
					<div class="row afecta efectivo hide">
						<div class="col-sm-6">
							<div class="form-group">
								<label>Entregado</label>
								<input type="text" name="monto_entregado" class="monto_entregado numerillo form-control">
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label>Vuelto</label>
								<input type="text" name="monto_vuelto" class="monto_vuelto numerillo form-control" readonly>
							</div>
						</div>
					</div>
					<div class="row afecta tarjeta hide">
						<div class="col-sm-6">
							<div class="form-group">
								<label>Tarjeta</label>
								<?php echo $tarjeta; ?>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label>Nro. Tarjeta</label>
								<input type="text" name="nro_tarjeta" class="nro_tarjeta form-control" placeholder="****_****_****_XXXX">
							</div>
						</div>
					</div>
					<div class="row afecta tarjeta hide">
						<div class="col-sm-6">
							<div class="form-group">
								<label>Nro. Operaci&oacute;n</label>
								<input type="text" name="operacion_tarjeta" class="operacion_tarjeta form-control">
							</div>
						</div>
					</div>
					<div class="row afecta deposito hide">
						<div class="col-sm-6">
							<div class="form-group">
								<label>Banco</label>
								<?php echo $cuentas_bancarias; ?>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label>Fecha deposito</label>
								<div class="input-group">
									<input type="text" name="fecha_deposito" class="fecha_deposito form-control" placeholder="dd/mm/yyyy" value="<?php echo date("d/m/Y"); ?>">
									<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
								</div>
							</div>
						</div>
					</div>
					<div class="row afecta deposito hide">
								<!--
						<div class="col-sm-4">
							<div class="form-group">
								<label>Monto Convertido</label>
									<input type="text" name="monto_convertido_pay" class="monto_convertido_pay form-control numerillo">
								<div class="input-group">
									<span class="input-group-addon">USSD</span>
								</div>
							</div>
						</div>
						
						<div class="col-sm-2">
							<div class="form-group">
								<label>T.C </label>
								<input type="text" name="tipo_cambio_vigente" title="cambio vigente del numero de cuenta" class="tipo_cambio_vigente numerillo form-control">
							</div>
						</div>
								-->
						
						<div class="col-sm-6">
							<div class="form-group">
								<label>Nro. Operaci&oacute;n</label>
								<input type="text" name="operacion_deposito" class="operacion_deposito form-control">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12 text-right">
							<button class="btn btn-sm btn-default btn-cancel-pay">Cancelar</button>
							<button class="btn btn-sm btn-primary btn-accept-pay" id="btn-accept-pay">Aceptar</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>