<?php if($readonly) { ?>
<div class="alert alert-danger">La nota de credito solo se puede modificar en el transcurso del d&iacute;a.</div>
<?php } ?>
<form id="form_<?php echo $controller; ?>" class="app-form form-uppercase">
	<input type="hidden" name="idnotacredito" id="idnotacredito" value="<?php echo (!empty($notacredito["idnotacredito"])) ? $notacredito["idnotacredito"] : ""; ?>">
	<input type="hidden" name="serie_ref" id="serie_ref" value="<?php echo (!empty($notacredito["serie_ref"])) ? $notacredito["serie_ref"] : ""; ?>">
	<input type="hidden" name="iddocumento_ref" id="iddocumento_ref" value="<?php echo (!empty($notacredito["iddocumento_ref"])) ? $notacredito["iddocumento_ref"] : ""; ?>">
	<input type="hidden" name="numero_ref" id="numero_ref" value="<?php echo (!empty($notacredito["numero_ref"])) ? $notacredito["numero_ref"] : ""; ?>">
	<input type="hidden" name="idcliente" id="idcliente" value="<?php echo (!empty($notacredito["idcliente"])) ? $notacredito["idcliente"] : ""; ?>">
	<input type="hidden" name="idventa" id="idventa" value="<?php echo (!empty($notacredito["idventa"])) ? $notacredito["idventa"] : ""; ?>">
	
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
		<div class="col-md-8">
			<div class="row">
				<div class="col-md-4">
					<div class="form-group">
							<label class="required">Tipo documento</label>
							<?php echo $tipodocumento; ?>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label class="control-label">N&uacute;mero</label>
						<div class="input-group">
							<span class="input-group-btn">
								<?php echo $serie; ?>
							</span>
							<input type="text" name="numero" id="numero" class="form-control input-xs" value="<?php echo (!empty($notacredito["numero"])) ? $notacredito["numero"] : ""; ?>" readonly>
						</div>
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">Motivo</label>
						<?php echo $motivo;?>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label class="control-label">Tipo</label>
						<?php echo $tiponota; ?>
					</div>
				</div>
				<div class="col-md-2 div-descuento hide">
					<label class="control-label">Descuento</label>
					<input id="descuento" name="descuento" class="form-control input-xs" value="<?php echo (!empty($notacredito["descuento"])) ? $notacredito["descuento"] : ""; ?>">
				</div>
			</div>

			
			<div class="row">
				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">Moneda</label>
						<?php echo $moneda;?>
					</div>
				</div>
				
				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">T. Cambio</label>
						<input type="text" name="cambio_moneda" id="cambio_moneda" class="numerillo form-control input-xs" value="<?php echo (!empty($notacredito["cambio_moneda"])) ? $notacredito["cambio_moneda"] : ""; ?>">
					</div>
				</div>
				
				<div class="col-md-6">
					<div class="form-group">
						<label class="control-label">Cliente</label>
						<div class="input-group">
							<input type="text" name="cliente" id="cliente" class="form-control input-xs" value="<?php echo (!empty($notacredito_view["cliente"])) ? $notacredito_view["cliente"] : ""; ?>" readonly>
							<span class="input-group-btn">
								<button type="button" id="btn-buscar-cliente" class="btn btn-white btn-xs"><i class="fa fa-search"></i> Buscar</button>
							</span>
						</div>
					</div>
				</div>
				
				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">RUC / DNI</label>
						<input type="text" name="rucdni" id="rucdni" class="form-control input-xs" maxlength="11" value="<?php echo (!empty($notacredito["rucdni"])) ? $notacredito["rucdni"] : ""; ?>">
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<label class="control-label">Concepto</label>
						<input type="text" name="descripcion" id="descripcion" class="form-control input-xs" value="<?php echo (!empty($notacredito["descripcion"])) ? $notacredito["descripcion"] : ""; ?>">
					</div>
				</div>
			</div>
		</div>
		
		<div class="col-md-4">
			<div class="panel panel-default">
				<div class="panel-heading">Documento que modifica 
					<?php if($nuevo){ ?><button id="btn-buscar-venta" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> Buscar venta</button><?php } ?>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label">Comprobante</label>
								<input type="text" name="tipo_comprobante" id="tipo_comprobante" class="form-control input-xs" value="<?php echo (!empty($notacredito_view["tipo_documento_ref"])) ? $notacredito_view["tipo_documento_ref"] : ""; ?>" readonly>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label">Serie y n&uacute;mero</label>
								<input type="text" name="nrodoc_ref" id="nrodoc_ref" class="form-control input-xs" value="<?php echo (!empty($notacredito["serie_ref"])) ? $notacredito["serie_ref"]."-".$notacredito["numero_ref"] : ""; ?>" readonly>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label">Fecha de emisi&oacute;n</label>
								<input type="text" name="fecha_ref" id="fecha_ref" class="form-control input-xs" value="<?php echo (!empty($notacredito["fecha_ref"])) ? $notacredito["fecha_ref"] : ""; ?>" readonly>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label">Monto</label>
								<input type="text" name="monto_ref" id="monto_ref" class="form-control input-xs" value="<?php echo (!empty($notacredito["monto_ref"])) ? $notacredito["monto_ref"] : ""; ?>" readonly>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">Detalle venta</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table id="tbl-detalle" class="table table-striped tooltip-demo detail-table">
							<thead>
								<tr>
									<th>Producto</th>
									<th style="width:10%">U.Med.</th>
									<th style="width:5%">Cant.</th>
									<th style="width:10%">Serie</th>
									<th style="width:8%">P.U.</th>
									<th style="width:10%">Total</th>
									<th style="width:10%">Grupo Op</th>
									<th style="width:10%">Tipo IGV</th>
									<th style="width:2%"><input type="checkbox" id="check_all" title="Seleccionar todos los item"></th>
									<th style="display:none;"></th>
								</tr>
							</thead>
							<tbody>
							<?php 
							if( ! empty($detalle)) {
								$combobox_grupo_igv->removeAttr("id");
								$combobox_grupo_igv->setAttr("name", "deta_grupo_igv[]");
								$combobox_grupo_igv->setAttr("class", "form-control input-xs deta_grupo_igv");
								
								$combobox_tipo_igv->removeAttr("id");
								$combobox_tipo_igv->setAttr("name", "deta_tipo_igv[]");
								$combobox_tipo_igv->setAttr("class", "form-control input-xs deta_tipo_igv");
								
								foreach($detalle as $row) {
									$combobox_grupo_igv->setSelectedOption($row["codgrupo_igv"]);
									$combobox_tipo_igv->setSelectedOption($row["codtipo_igv"]);
									
									echo '<tr class="item-select">';
									echo '<td><input type="text" name="deta_producto[]" class="form-control input-xs deta_producto" value="'.$row["producto"].'" readonly></td>';
									echo '<td style="vertical-align:middle;">'.$row['unidad'].'</td>';
									echo '<td><input type="text" name="deta_cantidad[]" class="form-control input-xs deta_cantidad" value="'.$row["cantidad"].'" readonly></td>';
									echo '<td><input type="text" name="deta_serie[]" class="form-control input-xs deta_serie" value="'.$row["serie"].'" readonly></td>';
									echo '<td><input type="text" name="deta_precio[]" class="form-control input-xs deta_precio" value="'.$row["precio"].'" readonly></td>';
									echo '<td><input type="text" name="deta_importe[]" class="form-control input-xs deta_importe" value="'.$row["importe"].'" readonly></td>';
									echo '<td>'.$combobox_grupo_igv->getObject().'</td>';
									echo '<td>'.$combobox_tipo_igv->getObject().'</td>';
									echo '<td></td>';
									echo '<td style="display:none;">';
									echo '<input type="hidden" name="deta_idunidad[]" class="deta_idunidad" value="'.$row["idunidad"].'">';
									echo '<input type="hidden" name="deta_idproducto[]" class="deta_idproducto" value="'.$row["idproducto"].'">';
									echo '<input type="hidden" name="deta_idalmacen[]" class="deta_idalmacen" value="'.$row["idalmacen"].'">';
									echo '<input type="hidden" name="deta_cantidad_real[]" class="deta_cantidad_real" value="'.$row["cantidad"].'">';
									echo '<input type="hidden" name="deta_controla_stock[]" class="deta_controla_stock" value="'.$row["controla_stock"].'">';
									echo '<input type="hidden" name="deta_controla_serie[]" class="deta_controla_serie" value="'.$row["controla_serie"].'">';
									echo '</td>';
									echo '</tr>';
								}
							}
							?>
							</tbody>
						</table>
					</div>
					<div class="row">
						<div class="col-md-2 col-md-offset-6">
                            <div class="form-group">
								<label class="control-label">Subtotal</label>
								<input type="text" name="subtotal" id="subtotal" class="form-control input-xs numerillo" value="<?php echo (isset($notacredito["subtotal"])) ? $notacredito["subtotal"] : ""; ?>" readonly>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label>IGV</label>
								<!--<div class="input-group">
									<span class="input-group-addon"><input type="checkbox" name="valor_igv" id="valor_igv" value="<?php //echo $valor_igv;?>"></span>
									<input type="text" name="igv" id="igv" class="form-control" readonly value="<?php //echo (isset($notacredito["igv"])) ? $notacredito["igv"] : ""; ?>">
								</div>-->
								<input type="text" name="igv" id="igv" class="form-control input-xs numerillo" readonly value="<?php echo (isset($notacredito["igv"])) ? $notacredito["igv"] : ""; ?>">
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">Total</label>
								<input type="text" name="total" id="total" class="form-control input-xs numerillo" value="<?php echo (isset($notacredito_view["total"])) ? $notacredito_view["total"] : ""; ?>" readonly>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="form-group">
			<div class="col-sm-6 text-left">
				<button class="btn btn-sm btn-warning btn_cancel" data-controller="<?php echo $controller; ?>">Cancelar</button>
			</div>
			<div class="col-sm-6 text-right">
				<?php if($readonly == false && $anulado == false) { ?>
				<button id="btn_save_notacredito" class="btn btn-sm btn-primary" data-controller="<?php echo $controller; ?>">Guardar</button>
				<?php } ?>
			</div>
		</div>
	</div>
</form>

<div style="display:none;"><?php echo $combo_grupo_igv.'</br>'.$combo_tipo_igv;?></div>
<script>
var default_grupo_igv = <?php echo (!empty($default_igv)) ? "'$default_igv'" : "false"; ?>;
var $idtipodocumento = '<?php echo $idtipodocumento; ?>';
</script>