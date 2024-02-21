<form id="form_<?php echo $controller; ?>" class="form-horizontal app-form form-uppercase">
	<input type="hidden" name="idreciboegreso" id="idreciboegreso" value="<?php echo (!empty($idreciboegreso)) ? $idreciboegreso : ""; ?>">
	<input type="hidden" name="idpersona" id="recibo_idpersona" value="<?php echo (!empty($idpersona)) ? $idpersona : ""; ?>">
	<input type="hidden" name="en_cierrecaja" id="en_cierrecaja" value="<?php echo (!empty($en_cierrecaja)) ? $en_cierrecaja : "N"; ?>">
	
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
				<div class="col-sm-12">
					<div class="row">
						<div class="col-sm-6">
								<select class="form-control" name="serie" id="serie" ></select>
						</div>
							
						<div class="col-sm-6">
							<input type="text" name="numero" id="numero" value="<?php echo (!empty($numero)) ? $numero : ""; ?>" class="form-control" required="" style="text-align:right;margin-left:0px;" readonly="">
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
				<div class="col-sm-7">
					<label class="control-label required">Moneda</label>
					<?php echo $moneda;?>
				</div>
				
				<div class="col-sm-5">
					<div class="">
						<label class="control-label required">T Cambio</label>
						<input type="text" name="tipocambio" id="cambio_moneda" class="form-control numero" value="<?php echo (!empty($tipocambio)) ? $tipocambio : ""; ?>" placeholder="0.00" readonly="" required="">
					</div>
				</div>
			</div>
		</div>
		
		<div class="col-sm-2" style="border:0px solid red;">
			<label class="control-label required">Monto</label>
			<div class="row">
				<div class="col-sm-12">
					<div class="input-group">
						<input type="text" name="monto" id="monto" class="form-control numero" value="<?php echo (!empty($monto)) ? $monto : ""; ?>"  placeholder="0.00" required="">
						<span class="input-group-btn tooltip-demo">
							<button type="button" id="btn-buscar-monto" class="btn btn-outline btn-primary" data-toggle="tooltip" con_cierre="false" title="Buscar Monto">
								<i class="fa fa-search"></i>
							</button>
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="row">
		<!--<div class="col-sm-3" style="border:0px solid red;">
			<label class="control-label required">Monto</label>
			<div class="row">
				<div class="col-sm-12">
					<input type="text" name="monto" id="monto" class="form-control numero" value="<?php echo (!empty($monto)) ? $monto : ""; ?>"  placeholder="0.00" required="">
				</div>
			</div>
		</div>-->
		
		<div class="col-sm-3" style="border:0px solid red;">
			<label class="control-label" data-toggle='tooltip' title="Seleccione si tiene un comprobante de referencia ">Tipo Documento</label>
			<div class="row">
				<div class="col-sm-12">
					<?php echo $tidocumento; ?>
				</div>
			</div>
		</div>

		<div class="col-sm-9" style="border:0px solid red;">
			<div class="row">
				<div class="col-sm-3" style="border:0px solid red;">
					<label class="control-label required ">Referencia</label>
					<div class="row">
						<div class="col-sm-12">
							<select class="form-control" name="tabla" id="tabla">
							<?php
							$ref = array('CLIENTE'=>'CLIENTE','USUARIO'=>'EMPLEADO'); 
							foreach($ref as $k=>$v){
								echo '<option value="'.$k.'" ';
								if(!empty($tipo_ingreso) && $tipo_ingreso==$k){
									echo 'selected="selected"';
								}
								echo '" >'.$v.'</option>';
							}
							?>
							</select>
						</div>
					</div>
				</div>
				
				<div class="col-sm-9" style="border:0px solid red;">
					<label class="control-label required refern">Cliente</label>
					<div class="row">
						<div class="col-sm-12">
							<div class="input-group">
								<input type="text" name="cliente" id="cliente_razonsocial" value="<?php echo (!empty($referencia)) ? $referencia : ""; ?>" class="form-control" required="" placeholder="Nombre, DNI, razon social o RUC">
								<span class="input-group-btn tooltip-demo">
									<button type="button" id="btn-buscar-cliente" class="btn btn-outline btn-primary" data-toggle="tooltip" title="Buscar clientes">
										<i class="fa fa-search"></i>
									</button>
									<button type="button" id="btn-registrar-cliente" class="btn btn-outline btn-primary" data-toggle="tooltip" title="&iquest;No existe el cliente? Registrar aqui">
										<i class="fa fa-edit"></i>
									</button>
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!--<label class="control-label required">Cliente</label>
			<div class="input-group">
				<input type="text" name="cliente" id="cliente_razonsocial" value="<?php echo (!empty($cliente)) ? $cliente : ""; ?>" class="form-control" required="" placeholder="Nombre, DNI, razon social o RUC">
				<span class="input-group-btn tooltip-demo">
					<button type="button" id="btn-buscar-cliente" class="btn btn-outline btn-primary" data-toggle="tooltip" title="Buscar clientes">
						<i class="fa fa-search"></i>
					</button>
					<button type="button" id="btn-registrar-cliente" class="btn btn-outline btn-primary" data-toggle="tooltip" title="&iquest;No existe el cliente? Registrar aqui">
						<i class="fa fa-edit"></i>
					</button>
				</span>
			</div>-->
		</div>
	</div>
	
	<div class="row">		
		<div class="col-sm-3 tooltip-demo" style="border:0px solid red;">
			<div class="row">			
				<div class="col-sm-12" style="border:0px solid red;">
					<div class="row">
						<div class="col-sm-6">
							<label class="control-label" >Serie</label>
							<input type="text" name="serie_doc" id="serie_doc" class="form-control" value="<?php echo (!empty($serie_doc)) ? $serie_doc : ""; ?>"   placeholder="000" readonly="">
						</div>
						
						<div class="col-sm-6">
							<label class="control-label" >Numero</label>
							<input type="text" name="numero_doc" id="numero_doc" class="form-control" value="<?php echo (!empty($numero_doc)) ? $numero_doc : ""; ?>"  placeholder="000000" readonly="">
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="col-sm-9" style="border:0px solid red;">
			<label class="control-label required">Concepto</label>
			<textarea class="form-control" required="" name="concepto" id="concepto" value="<?php echo (!empty($concepto)) ? $concepto : ""; ?>"><?php echo (!empty($concepto)) ? $concepto : ""; ?></textarea>
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

<div id="modal-empleado" class="modal fade" aria-hidden="true" aria-labelledby="myLargeModalLabel" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Registrar Empleado</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<?php echo $form_usuario; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="modal-montos" class="modal fade" aria-hidden="true" aria-labelledby="myLargeModalLabel" data-backdrop="static">
	<div class="modal-dialog ">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Seleccion de Montos</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="form-group">
						<label>Moneda</label>
						<select id="idmoneda_cierre" name="idmoneda_cierre" class="form-control input-xs"></select>
					</div>
				</div>
				
				<div class="row">					
					<div class="table-responsive">
						<table id="" class="tabla_modulos table table-striped" border='0'>
							<tbody>
								<tr>
									<td width="8%"></td>
									<td width="60%"></td>
									<td width="15%"></td>
									<td width="5%"></td>
								</tr>
									<?php
									foreach ($tipomov as $key => $value) {
										echo "<tr class='tr'>";
										echo '	<td class="td_padre"><b>'.$value['alias'].'</b></td>';
										echo '	<td colspan="3">&nbsp;</td>';
										echo "</tr>";
											foreach ($conceptos as $k => $v) {
											$readonly = '';
											$display = 'block';
											if($value['simbolo']=='S'){
												$readonly = 'readonly';
												$display = 'none';
											}
											if ($value['idtipomovimiento'] == $v['idtipomovimiento']) {
												echo "<tr class='tr' index='".$v['descripcion']."'>";
												echo "	<td style='padding:3px;'><div class=''>".date('d/m/Y')."</div></td>";
												echo '	<td style="padding:0px 0px 0px 15px;height:35px !important;" >'.$v['descripcion'].'</td>';
												echo "	<td style='padding:3px;'>";
												echo "		<input type='text' name='monto_c[]' id='monto_{$v['idconceptomovimiento']}' class='form-control input-xs numero colum' ajax-data=".$v['idconceptomovimiento']."-".$v['idtipomovimiento']." ajax-type='{$value['simbolo']}' $readonly value='0.00'/>";
												echo "	</td>";
												echo '	<td>';
												// echo "		<div class='input-group tooltip-demo'>";
												// echo "			<span class='input-group-addon' data-toggle='tooltip' title=''>";
												if($value['simbolo']=='S'){
												echo "		<input type='checkbox' class='ckb_m' style='display:none;'  id='ckb{$v['idconceptomovimiento']}' value='{$v['idconceptomovimiento']}' ajax-cm='{$v['idconceptomovimiento']}'	>";
													echo "	<i class='fa fa-check-square-o'></i>";
												}else{
													echo "		<input type='checkbox' class='ckb_m' style='display:block;' id='ckb{$v['idconceptomovimiento']}' value='{$v['idconceptomovimiento']}' ajax-cm='{$v['idconceptomovimiento']}'>";
												}
												// echo "			</span>";
												// echo "		</div>";
												echo '</td>';
												echo "</tr>";
											}
										}
									}
								?>
								<tr>
									<td colspan=2 align="right">Monto Acumulado(Efectivo)</td>
									<td>
										<input type="text" name="monto_acumulado" id="monto_acumulado" class="form-control input-xs numero" placeholder="0.00" readonly="">
									</td>
									<td>&nbsp;</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="btn_asign">Asignar</button>
				<button type="button" class="btn btn-warning" id="btn_no_asign">No asignar</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<?php echo $modal_pago; ?>
<script>var $idtipodocumento = '<?php echo $idtipodocumento; ?>';</script>
<style>
	.numero{text-align:right;}
	.form-control#serie{padding: 5px 8px;}
	.form-control#idmoneda{font-size: 12px;padding: 5px 2px;}
</style>