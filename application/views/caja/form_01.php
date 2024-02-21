<script src="app/js/jquery-2.1.1.js"></script>
<style type="text/css">
	.numero{text-align: right;}
	.modal.large {	}
</style>

<?php echo $grilla; ?>
	
	<div class="modal fade" id="abrir-caja" aria-labelledby="myLargeModalLabel" data-backdrop="static" >
		<div class="modal-dialog modal-md" >
			<div class="modal-content">
				<form id="open-caja">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title">Abrir Caja</h4>
					</div>
					<div class="modal-body" style="padding:20px 30px 0px 30px;">
						<div class="row">	
							<div class="col-md-6">
								<label>Monto Apertura para <?php echo date("d/m/Y"); ?></label>
								<input type="hidden" name="idcaja" id="idcaja" value="<?php echo $codcaja;?>">
								<table class="table table-striped" border=0>
									<tbody>
										<?php
											foreach ($monedas as $k => $v) {
												echo "<tr>";
												echo "	<td width='160px'>";
												echo "		<label>Monto apertura ".ucwords(strtolower($v['descripcion']))."</label>";
												echo "		<input name='monto[]' class='form-control numero input_apertura' id='monto_abrir_caja{$v['idmoneda']}' placeholder='0.00'>";
												echo "		<input name='idmoneda[]' type='hidden' value='".$v['idmoneda']."'>";
												echo "		<input name='tipocambio[]' type='hidden' value='".$v['valor_cambio']."'>";
												echo "		<input name='denominacion[]' type='hidden' value='".$v['descripcion']."'>";
												echo "	</td>";
												echo "</tr>";
											}
										?>
									</tbody>
								</table>
							</div>
							<div class="col-md-6">
								<?php
								$f_caja_anterior = '';
									if(!empty($caja_pasada[0]['fecha_caja']))
										$f_caja_anterior = $caja_pasada[0]['fecha_caja'];
								?>
								<label>Monto anterior cierre de <?php echo $f_caja_anterior;?></label>
								<table class="table table-striped" border=0>
									<tbody>
										<?php
											foreach ($monedas as $k => $v) {
												echo "<tr>";
												echo "	<td width='160px'>";
												echo "		<label>Monto apertura ".ucwords(strtolower($v['descripcion']))."</label>";
												echo "		<input class='form-control numero monto_cierre' ajax-money='{$v['idmoneda']}' id='monto_cierre{$v['idmoneda']}' readonly='readonly' placeholder='0.00'>";
												echo "	</td>";
												echo "</tr>";
											}
										?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
						<button type="button" class="btn btn-primary btn-save">Guardar</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<input id="caja_hoy" type="hidden" value="<?php echo $codcaja;?>" />
	<div class="modal fade" id="cerrar-caja" aria-labelledby="myLargeModalLabel" data-backdrop="static"  >
		<div class="modal-dialog" >
			<div class="modal-content">
				<form id="form-cerrar">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title">Cerrar Caja</h4>
					</div>
					<div class="modal-body">
							<div class="">
								<input type="hidden" name="idcaja" id="idcaja" value="<?php echo $codcaja;?>">
								<table class="table table-striped" border=0>
									<tbody>
										<?php
											foreach ($cierre_temp as $k => $v) {
												echo "<tr>";
												echo "	<td width='160px'>";
												echo "		<label style='color:#f8ac59;font-weight:bold;' >".(($v['moneda'])).' '.$v['simbolo']."</label>";
												echo "		<table width='100%'>";
												echo "			<tr>";
												echo "				<td>";
												echo "					<label>Monto apertura {$v['simbolo']}</label>";
												echo "					<input name='monto_aperutra[]' class='form-control numero' style='width:80%;' readonly='readonly' value='".$v['monto_apertura']."' placeholder='0.00' />";
												echo "				</td>";

												echo "				<td>";
												echo "					<label>Saldo Actual {$v['simbolo']}</label>";
												echo "					<input name='monto_cierre[]' class='form-control numero' style='width:80%;' readonly='readonly' value='".$v['monto_cierre']."' placeholder='0.00' />";
												echo "				</td>";
												echo "			</tr>";
												echo "		</table>";
												echo "	</td>";
												echo "</tr>";
											}
										?>
									</tbody>
								</table>
							</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
						<button type="button" class="btn btn-primary btn-save">Guardar</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	
	<div class="modal fade" id="arqueo-caja" aria-labelledby="myLargeModalLabel" data-backdrop="static"  >
		<div class="modal-dialog" >
			<div class="modal-content">
				<form id="form-arqueo">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title">Arqueo Caja</h4>
					</div>
					
					<div class="modal-body">
						<div class="">
							<input type="hidden" name="idcaja"       id="idcaja"       value="<?php echo $codcaja;?>" />
							<input type="hidden" name="idcierrecaja" id="idcierrecaja" value="<?php echo (!empty($cierrecaja[0]['idcierrecaja'])) ?  : "0"; ?>" />
							<input type="hidden" name="resto" id="resto" />
							<input type="hidden" name="tiporesto" id="tiporesto" />
							
							<div class="nav-tabs-horizontal">
								<ul class="nav nav-tabs" data-plugin="nav-tabs" role="tablist">
									<?php
									foreach ($monedas as $k => $v) {
										$cls='';
										if($k==0)
											$cls='active';
										echo "<li class='".$cls."' role='presentation' index_li='{$k}' mon_li='{$v['idmoneda']}'>";
										echo "	<a data-toggle='tab' href='#tab{$prefix}-{$k}' aria-controls='exampleTabs{$k}' role='tab' style='font-size:10px;'>{$v['descripcion']}</a>";
										echo "</li>";
									}
									?>
								</ul>
							</div>
							<!--
							<table class="table table-striped" border=0>
									<tbody>
										<?php
											echo "<tr>";
											foreach ($monedas as $k => $v) {
												echo "	<td width='160px'>";
												echo "		<label style='color:#f8ac59;font-weight:bold;' >".(($v['descripcion'])).' '.$v['simbolo']."</label>";
												echo "	</td>";
											}
											echo "</tr>";
											
											echo "<tr>";
											foreach ($monedas as $k => $v) {
												echo "	<td>";
												echo "		<table>";
												foreach($denominacion as $kk=>$vv){
													echo "<tr>";
													if($vv['idmoneda'] == $v['idmoneda']){
														echo "<td width='500px' style='padding:0px 5px 5px 0px'>";
														echo "	<label>Billete {$vv['simbolo']} {$vv['billete']}<label>";
														echo "</td>";
														
														echo "<td>";
														echo "	<input type='hidden' name='idmoneda[]' 		value='{$v['idmoneda']}'>";
														echo "	<input type='hidden' name='iddenominacion[]' 	value='{$vv['iddenominacion']}'>";
														echo "	<input name='billete[]' class='form-control billete numero idinero' const='{$vv['billete']}' data-moneda='{$v['idmoneda']}' style='width:55px;height:25px;padding:0px 6px;'  value='' placeholder='0'>";
														echo "</td>";
													echo "</tr>";
													}
												}												
												echo "		</table>";
												echo "	</td>";
											}
											echo "</tr>";
											
											echo "<tr>";
											foreach ($monedas as $k => $v) {
												$saldo_cierre = 0;
												foreach($cierrecaja as $key=>$val){
													if( $val['idmoneda']== $v['idmoneda'] ){
														$saldo_cierre = $val['monto'];
													}													
												}
												
												
												echo "	<td width='160px' align=''>";
												echo "		<table>";
												echo "			<tr>";
												echo "				<td>".'<label>Total Fisico&nbsp;'."</label></td>";
												echo "				<td><label>".'/&nbsp;Saldo Caja'."</label></td>";
												echo "			</tr>";
												
												echo "			<tr>";
												echo "				<td><input name class='form-control numero total' data-simbolo = '{$v['simbolo']}' data-money='{$v['idmoneda']}' id='total{$v['idmoneda']}' style='width:65px;height:25px;padding:0px 6px;font-size:13px;' placeholder='0.00' readonly value='0'></td>";
												echo "				<td><input name class='form-control numero saldo' id='saldo{$v['idmoneda']}' style='width:65px;height:25px;padding:0px 6px;font-size:13px;' placeholder='0.00' readonly value='{$saldo_cierre}'></td>";
												echo "			</tr>";
												echo "		</table>";
												echo "	</td>";
											}<script src="app/js/jquery-2.1.1.js"></script>
<script type="text/javascript">
	//$("#button-cc").attr('disabled','disabled');
	
</script>

<style type="text/css">
	.numero{text-align: right;}
	.modal.large {	}
</style>

<?php echo $grilla; ?>
	
	<div class="modal fade" id="abrir-caja" aria-labelledby="myLargeModalLabel" data-backdrop="static" >
		<div class="modal-dialog modal-md" >
			<div class="modal-content">
				<form id="open-caja">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title">Abrir Caja</h4>
					</div>
					<div class="modal-body" style="padding:20px 30px 0px 30px;">
						<div class="row">	
							<div class="col-md-6">
								<label>Monto Apertura para <?php echo date("d/m/Y"); ?></label>
								<input type="hidden" name="idcaja" id="idcaja" value="<?php echo $codcaja;?>">
								<table class="table table-striped" border=0>
									<tbody>
										<?php
											foreach ($monedas as $k => $v) {
												echo "<tr>";
												echo "	<td width='160px'>";
												echo "		<label>Monto apertura ".ucwords(strtolower($v['descripcion']))."</label>";
												echo "		<input name='monto[]' class='form-control numero' placeholder='0.00'>";
												echo "		<input name='idmoneda[]' type='hidden' value='".$v['idmoneda']."'>";
												echo "		<input name='tipocambio[]' type='hidden' value='".$v['valor_cambio']."'>";
												echo "		<input name='denominacion[]' type='hidden' value='".$v['descripcion']."'>";
												echo "	</td>";
												echo "</tr>";
											}
										?>
									</tbody>
								</table>
							</div>
							<div class="col-md-6">
								<?php
								$f_caja_anterior = '';
									if(!empty($caja_pasada[0]['fecha_caja']))
										$f_caja_anterior = $caja_pasada[0]['fecha_caja'];
								?>
								<label>Monto anterior cierre de <?php echo $f_caja_anterior;?></label>
								<table class="table table-striped" border=0>
									<tbody>
										<?php
											foreach ($monedas as $k => $v) {
												echo "<tr>";
												echo "	<td width='160px'>";
												echo "		<label>Monto apertura ".ucwords(strtolower($v['descripcion']))."</label>";
												echo "		<input class='form-control numero monto_cierre' ajax-money='{$v['idmoneda']}' id='monto_cierre{$v['idmoneda']}' readonly='readonly' placeholder='0.00'>";
												echo "	</td>";
												echo "</tr>";
											}
										?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
						<button type="button" class="btn btn-primary btn-save">Guardar</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<input id="caja_hoy" type="hidden" value="<?php echo $codcaja;?>" />
	<div class="modal fade" id="cerrar-caja" aria-labelledby="myLargeModalLabel" data-backdrop="static"  >
		<div class="modal-dialog" >
			<div class="modal-content">
				<form id="form-cerrar">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title">Cerrar Caja</h4>
					</div>
					<div class="modal-body">
							<div class="">
								<input type="hidden" name="idcaja" id="idcaja" value="<?php echo $codcaja;?>">
								<table class="table table-striped" border=0>
									<tbody>
										<?php
											foreach ($cierre_temp as $k => $v) {
												echo "<tr>";
												echo "	<td width='160px'>";
												echo "		<label style='color:#f8ac59;font-weight:bold;' >".(($v['moneda'])).' '.$v['simbolo']."</label>";
												echo "		<table width='100%'>";
												echo "			<tr>";
												echo "				<td>";
												echo "					<label>Monto apertura {$v['simbolo']}</label>";
												echo "					<input name='monto_aperutra[]' class='form-control numero' style='width:80%;' readonly='readonly' value='".$v['monto_apertura']."' placeholder='0.00' />";
												echo "				</td>";

												echo "				<td>";
												echo "					<label>Saldo Actual {$v['simbolo']}</label>";
												echo "					<input name='monto_cierre[]' class='form-control numero' style='width:80%;' readonly='readonly' value='".$v['monto_cierre']."' placeholder='0.00' />";
												echo "				</td>";
												echo "			</tr>";
												echo "		</table>";
												echo "	</td>";
												echo "</tr>";
											}
										?>
									</tbody>
								</table>
							</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
						<button type="button" class="btn btn-primary btn-save">Guardar</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	
	<div class="modal fade" id="arqueo-caja" aria-labelledby="myLargeModalLabel" data-backdrop="static"  >
		<div class="modal-dialog" >
			<div class="modal-content">
				<form id="form-arqueo">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title">Arqueo Caja</h4>
					</div>
					
					<div class="modal-body">
						<div class="">
							<input type="hidden" name="idcaja"       id="idcaja"       value="<?php echo $codcaja;?>" />
							<input type="hidden" name="idcierrecaja" id="idcierrecaja" value="<?php echo (!empty($cierrecaja[0]['idcierrecaja'])) ?  : "0"; ?>" />
							<input type="hidden" name="resto" id="resto" />
							<input type="hidden" name="tiporesto" id="tiporesto" />
							
							<table class="table table-striped" border=0>
									<tbody>
										<?php
											echo "<tr>";
											foreach ($monedas as $k => $v) {
												echo "	<td width='160px'>";
												echo "		<label style='color:#f8ac59;font-weight:bold;' >".(($v['descripcion'])).' '.$v['simbolo']."</label>";
												echo "	</td>";
											}
											echo "</tr>";
											
											echo "<tr>";
											foreach ($monedas as $k => $v) {
												echo "	<td>";
												echo "		<table>";
												foreach($denominacion as $kk=>$vv){
													echo "<tr>";
													if($vv['idmoneda'] == $v['idmoneda']){
														echo "<td width='500px' style='padding:0px 5px 5px 0px'>";
														echo "	<label>Billete {$vv['simbolo']} {$vv['billete']}<label>";
														echo "</td>";
														
														echo "<td>";
														echo "	<input type='hidden' name='idmoneda[]' 		value='{$v['idmoneda']}'>";
														echo "	<input type='hidden' name='iddenominacion[]' 	value='{$vv['iddenominacion']}'>";
														echo "	<input name='billete[]' class='form-control billete numero idinero' const='{$vv['billete']}' data-moneda='{$v['idmoneda']}' style='width:55px;height:25px;padding:0px 6px;'  value='' placeholder='0'>";
														echo "</td>";
													echo "</tr>";
													}
												}												
												echo "		</table>";
												echo "	</td>";
											}
											echo "</tr>";
											
											echo "<tr>";
											foreach ($monedas as $k => $v) {
												$saldo_cierre = 0;
												foreach($cierrecaja as $key=>$val){
													if( $val['idmoneda']== $v['idmoneda'] ){
														$saldo_cierre = $val['monto'];
													}													
												}
												
												
												echo "	<td width='160px' align=''>";
												echo "		<table>";
												echo "			<tr>";
												echo "				<td>".'<label>Total Fisico&nbsp;'."</label></td>";
												echo "				<td><label>".'/&nbsp;Saldo Caja'."</label></td>";
												echo "			</tr>";
												
												echo "			<tr>";
												echo "				<td><input name class='form-control numero total' data-simbolo = '{$v['simbolo']}' data-money='{$v['idmoneda']}' id='total{$v['idmoneda']}' style='width:65px;height:25px;padding:0px 6px;font-size:13px;' placeholder='0.00' readonly value='0'></td>";
												echo "				<td><input name class='form-control numero saldo' id='saldo{$v['idmoneda']}' style='width:65px;height:25px;padding:0px 6px;font-size:13px;' placeholder='0.00' readonly value='{$saldo_cierre}'></td>";
												echo "			</tr>";
												echo "		</table>";
												echo "	</td>";
											}
											echo "</tr>";
										?>
									</tbody>
								</table>
						</div>
					</div>
					
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
						<button type="button" class="btn btn-primary btn-save">Guardar</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	
	<div class="modal fade" id="arqueo_confirmar" aria-labelledby="myLargeModalLabel" data-backdrop="static"  >
		<div class="modal-dialog modal-sm" >
			<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title">Ingrese Observacion</h4>
					</div>
					<div class="modal-body">
						<label for="observaciones" class="labels" >Observaciones</label>
						<textarea name="observaciones" id="observaciones" style="width: 240px; height: 80px"></textarea>
						<p style="margin-top: 10px;">No se olvide informar al administrador.</p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
						<button type="button" class="btn btn-primary btn-save">Guardar</button>
					</div>
			</div>
		</div>
	</div>
											echo "</tr>";
										?>
									</tbody>
								</table>
							-->
						</div>
					</div>
					
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
						<button type="button" class="btn btn-primary btn-save">Guardar</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	
	<div class="modal fade" id="arqueo_confirmar" aria-labelledby="myLargeModalLabel" data-backdrop="static"  >
		<div class="modal-dialog modal-sm" >
			<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title">Ingrese Observacion</h4>
					</div>
					<div class="modal-body">
						<label for="observaciones" class="labels" >Observaciones</label>
						<textarea name="observaciones" id="observaciones" style="width: 240px; height: 80px"></textarea>
						<p style="margin-top: 10px;">No se olvide informar al administrador.</p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
						<button type="button" class="btn btn-primary btn-save">Guardar</button>
					</div>
			</div>
		</div>
	</div>