<form id="form_<?php echo $controller; ?>" class="app-form form-uppercase">
	<input type="hidden" name="idtipodocumento" id="idtipodocumento" value="<?php echo (!empty($idtipodocumento)) ? $idtipodocumento : "";?>"/>
	
	<div class="row">
		<div class="col-lg-12">
			<div class="tabs-container">
				<ul class="nav nav-tabs">
					<li class="active"><a data-toggle="tab" href="#tab-1"> Tipo Documento</a></li>
					<li class=""><a data-toggle="tab" href="#tab-2"> Control Correlativo</a></li>
				</ul>
				<div class="tab-content">
					<div id="tab-1" class="tab-pane active">
						<div class="panel-body">
							<div class="row">
								<div class="col-sm-8">
									<div class="form-group">
										<label class="required">Descripci&oacute;n</label>
										<input type="text" name="descripcion" id="descripcion" value="<?php echo (!empty($descripcion)) ? $descripcion : ""; ?>" class="form-control" required="" />
									</div>
								</div>

								<div class="col-sm-2">
									<div class="form-group">
										<label class="required">Abreviatura</label>
										<input type="text" name="abreviatura" id="abreviatura" value="<?php echo (!empty($abreviatura)) ? $abreviatura : ""; ?>" class="form-control" required="" />
									</div>
								</div>

								<div class="col-sm-2">
									<div class="form-group">
										<label class="">Codigo Sunat</label>
										<input type="text" name="codsunat" id="codsunat" value="<?php echo (!empty($codsunat)) ? $codsunat : ""; ?>" class="form-control" maxlength="2" />
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-sm-3">
									<div class="form-group">
										<label>Ver en Compra?</label>
										<div class="onoffswitch">
											<input type="checkbox" id="mostrar_en_compra" class="onoffswitch-checkbox" <?php echo (isset($mostrar_en_compra) && $mostrar_en_compra == 'S') ? "checked" : ""; ?> />
											<label class="onoffswitch-label" for="mostrar_en_compra">
												<span class="onoffswitch-inner"></span>
												<span class="onoffswitch-switch"></span>
											</label>
										</div>
									</div>
								</div>

								<div class="col-sm-3">
									<div class="form-group">
										<label>Ver en venta?</label>
										<div class="onoffswitch">
											<input type="checkbox" id="mostrar_en_venta" class="onoffswitch-checkbox" <?php echo (isset($mostrar_en_venta) && $mostrar_en_venta == 'S') ? "checked" : ""; ?> />
											<label class="onoffswitch-label" for="mostrar_en_venta">
												<span class="onoffswitch-inner"></span>
												<span class="onoffswitch-switch"></span>
											</label>
										</div>
									</div>
								</div>

								<div class="col-sm-3">
									<div class="form-group">
										<label>Ver en recibo Ingreso?</label>
										<div class="onoffswitch">
											<input type="checkbox" id="mostrar_en_recibos" class="onoffswitch-checkbox" <?php echo (isset($mostrar_en_recibos) && $mostrar_en_recibos == 'S') ? "checked" : ""; ?> />
											<label class="onoffswitch-label" for="mostrar_en_recibos">
												<span class="onoffswitch-inner"></span>
												<span class="onoffswitch-switch"></span>
											</label>
										</div>
									</div>
								</div>

								<div class="col-sm-3">
									<div class="form-group">
										<label>Ver en recibo Egreso?</label>
										<div class="onoffswitch">
											<input type="checkbox" id="mostrar_en_recibo" class="onoffswitch-checkbox" <?php echo (isset($mostrar_en_recibo) && $mostrar_en_recibo == 'S') ? "checked" : ""; ?>>
											<label class="onoffswitch-label" for="mostrar_en_recibo">
												<span class="onoffswitch-inner"></span>
												<span class="onoffswitch-switch"></span>
											</label>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-sm-3">
									<div class="form-group">
										<label>Ver en cobranzas?</label>
										<div class="onoffswitch">
											<input type="checkbox" id="mostrar_en_cobranzas" class="onoffswitch-checkbox" <?php echo (isset($mostrar_en_cobranzas) && $mostrar_en_cobranzas == 'S') ? "checked" : ""; ?>>
											<label class="onoffswitch-label" for="mostrar_en_cobranzas">
												<span class="onoffswitch-inner"></span>
												<span class="onoffswitch-switch"></span>
											</label>
										</div>
									</div>
								</div>
								
								<div class="col-sm-3">
									<div class="form-group">
										<label>Genera Correlativo?</label>
										<div class="onoffswitch">
											<input type="checkbox" id="genera_correlativo" class="onoffswitch-checkbox" <?php echo (isset($genera_correlativo) && $genera_correlativo == 'S') ? "checked" : ""; ?> />
											<label class="onoffswitch-label" for="genera_correlativo">
												<span class="onoffswitch-inner"></span>
												<span class="onoffswitch-switch"></span>
											</label>
										</div>
									</div>
								</div>
								
								<div class="col-sm-3">
									<div class="form-group">
										<label>Facturaci&oacute;n electr&oacute;nica?</label>
										<div class="onoffswitch">
											<input type="checkbox" id="facturacion_electronica" class="onoffswitch-checkbox" <?php echo (isset($facturacion_electronica) && $facturacion_electronica == 'S') ? "checked" : ""; ?> />
											<label class="onoffswitch-label" for="facturacion_electronica">
												<span class="onoffswitch-inner"></span>
												<span class="onoffswitch-switch"></span>
											</label>
										</div>
									</div>
								</div>
								
								<div class="col-sm-3">
									<div class="form-group">
										<label>DNI Obligatorio?</label>
										<div class="onoffswitch">
											<input type="checkbox" id="dni_obligatorio" class="onoffswitch-checkbox" <?php echo (isset($dni_obligatorio) && $dni_obligatorio == 'S') ? "checked" : ""; ?> />
											<label class="onoffswitch-label" for="dni_obligatorio">
												<span class="onoffswitch-inner"></span>
												<span class="onoffswitch-switch"></span>
											</label>
										</div>
									</div>
								</div>
							</div>
							
							<div class="row">
								<div class="col-sm-3">
									<div class="form-group">
										<label>RUC Obligatorio?</label>
										<div class="onoffswitch">
											<input type="checkbox" id="ruc_obligatorio" class="onoffswitch-checkbox" <?php echo (isset($ruc_obligatorio) && $ruc_obligatorio == 'S') ? "checked" : ""; ?> />
											<label class="onoffswitch-label" for="ruc_obligatorio">
												<span class="onoffswitch-inner"></span>
												<span class="onoffswitch-switch"></span>
											</label>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div id="tab-2" class="tab-pane">
						<div class="panel-body">
							<div class="row">
								<div class="col-sm-12">
									<label for="" class="required">Sucursal</label>
									<select name="idsucursal" id="idsucursal" style="" class="form-control">
										<?php //if(Session::extract('codperfil', 'usuario')=='001' || Session::extract('codperfil', 'usuario')=='000'){ ?>
										<!--<option value="">Seleccione...</option>-->
										<?php //} ?>
										<?php 
										if(!empty($sucursal)) {
											foreach($sucursal as $k => $v) {
												echo '<optgroup label="'.$k.'">';
												
												if(!empty($v)) {
													foreach($v as $t) {
														$selected = '';
														if ($sucursal_session == $t['idsucursal']) {
															$selected = "selected";
														}
														echo '<option '.$selected.' value="'.$t['idsucursal'].'" >'.$k." - ".$t['sede'].'</option>';
													}
													
												}
												
												echo '</optgroup>';
											}
										}
										?>
									</select>
								</div>
							</div>
							
							<div class="row">
								<div class="col-sm-8">
									<div class="form-group" style="">
										<label for="tipodoc" class="required">Documento</label>
										<input id="tipodoc" readonly="readonly" class="form-control" value="<?php echo (!empty($descripcion)) ? $descripcion : ""; ?>" required="">
									</div>
								</div>
								
								<div class="col-sm-4">
									<div class="form-group" style="">
										<label for="serie_c" class="required">Nueva Serie</label>
										<div class="input-group">
											<input id="serie" class="form-control text-uppercase" maxlength='4'>
											<span class="input-group-btn tooltip-demo">
												<button type="button" id="addCorrelativo" class="btn btn-outline btn-primary" data-toggle="tooltip" title="Añadir Correlativo">
													<i class="fa fa-plus-circle"></i>
												</button>
											</span>
										</div>
									</div>							
								</div>
							</div>
							
							<div class="row">
								<div class="col-sm-12">
									<table class="table table-striped" id="tabla_correlativo">
										<thead>
										  <tr>
											<th width="20%"><label>Serie</label></th>
											<th width="80%"><label>Numero</label></th>
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
	
	<div class="form-group">
		<div class="col-lg-offset-3 col-lg-9">
			<button id="<?php echo $prefix; ?>btn_cancel" class="btn btn-sm btn-white btn_cancel<?php echo $modal?" modal-form":""; ?>" data-controller="<?php echo $controller; ?>">Cancelar</button>
			<button type="submit" id="<?php echo $prefix; ?>btn_save" class="btn btn-sm btn-primary" data-controller="<?php echo $controller; ?>">Guardar</button>
		</div>
	</div>
</form>

<!--
<form id="form-data-correlativo">
	<div class="modal fade" id="form-data-dialog-second" >
		<div class="modal-dialog modal-md" >
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Control Correlativos</h4>
				</div>

				<div class="modal-body">
					<input type="hidden" id="codtipodocumento_" name="codtipodocumento" />
					
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group" style="">
								<label for="sucursal" class="required">Sucursal</label>
								<select name="codsede" id="codsede" style="" class="form-control">
									<?php //if(Session::extract('codperfil', 'usuario')=='001' || Session::extract('codperfil', 'usuario')=='000'){ ?>
									<option value="">Seleccione...</option>
									<?php //} ?>
									<?php 
									/*if(!empty($sede)) {
										foreach($sede as $k => $v) {
											echo '<optgroup label="'.$k.'">';
											
											if(!empty($v)) {
												$codsedes = array();
												foreach($v as $t) {
													echo '<option value="'.$t['codsede'].'">'.$t['sede'].'</option>';
													$codsedes[] = $t['codsede'];
												}
												
												if(count($codsedes) > 1) {
													echo '<option value="'.implode(',', $codsedes).'">TODOS LOCALES '.$k.'</option>';
												}
											}
											
											echo '</optgroup>';
										}
									}*/
									?>
								</select>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-sm-8">
							<div class="form-group" style="">
								<label for="tipodoc" class="required">Documento</label>
								<input id="tipodoc" readonly="readonly" class="form-control">
							</div>
						</div>
						
						<div class="col-sm-4">
							<div class="form-group" style="">
								<label for="tipodoc" class="required">Nueva Serie</label>
								<div class="input-group">
									<input id="serie"  class="form-control" maxlength='10'>
									<span class="input-group-btn tooltip-demo">
										<button type="button" id="addCorrelativo" class="btn btn-outline btn-primary" data-toggle="tooltip" title="Añadir Correlativo">
											<i class="fa fa-plus-circle"></i>
										</button>
									</span>
								</div>
							</div>							
						</div>
					</div>
					
					<div class="row">
						<div class="col-sm-12">
							<table class="table table-striped" id="tabla_correlativo">
								<thead>
								  <tr>
									<th width="20%"><label>Serie</label></th>
									<th width="80%"><label>Numero</label></th>
								  </tr>
								</thead>
								
								<tbody></tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default btn-save">Guardar</button>
					<button type="button" class="btn btn-white btn-cancel" data-dismiss="modal">Cerrar</button>
				</div>
			</div>
		</div>
	</div>
</form>
-->
<input type="hidden" id="controlador" value="<?php echo $controller;?>" />
<input type="hidden" id="idsuc" value="<?php echo $sucursal_session;?>" />