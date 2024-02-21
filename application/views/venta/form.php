<?php if($readonly && $anulado == false) { ?>
<div class="alert alert-danger">Ya se ha hecho algun despacho de producto para esta venta. Primero elimine el despacho para poder modificar.</div>
<?php } ?>
<div class="row"><div class="col-md-12 msg-about-cliente"></div></div>
<form id="form_<?php echo $controller; ?>" class="app-form">
	<input type="hidden" name="idventa" id="idventa" value="<?php echo (!empty($venta["idventa"])) ? $venta["idventa"] : ""; ?>">
	<input type="hidden" name="idpreventa" id="idpreventa" value="<?php echo (!empty($venta["idpreventa"])) ? $venta["idpreventa"] : ""; ?>">
	<input type="hidden" name="idcliente" id="compra_idcliente" value="<?php echo (!empty($venta["idcliente"])) ? $venta["idcliente"] : ""; ?>">
	<input type="hidden" name="ruc_obligatorio" id="ruc_obligatorio" value="<?php echo (!empty($venta["ruc_obligatorio"])) ? $venta["ruc_obligatorio"] : ""; ?>">
	<input type="hidden" name="dni_obligatorio" id="dni_obligatorio" value="<?php echo (!empty($venta["dni_obligatorio"])) ? $venta["dni_obligatorio"] : ""; ?>">
	
	<input type="hidden" name="dni_cliente" id="dni_cliente" value="<?php echo (!empty($venta["dni"])) ? $venta["ruc"] : ""; ?>">
	<input type="hidden" name="ruc_cliente" id="ruc_cliente" value="<?php echo (!empty($venta["dni"])) ? $venta["ruc"] : ""; ?>">
	<input type="hidden" name="estado_cliente" id="estado_cliente">
	
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
		<div class="col-md-2">
			<div class="form-group">
				<label class="required">Tipo documento</label>
				<?php echo $tipodocumento; ?>
			</div>
		</div>

		<div class="col-md-2">
			<div class="form-group">
				<label class="required">Nro. documento <?php echo ($editar_correlativo=='S')? '<input type="checkbox" name="edit_correlativo" value="1" id="edit_correlativo" title="Cambiar correlativo">':'';?></label>
				<div class="input-group">
					<span class="input-group-btn"><?php echo $serie; ?></span>
					<input type="text" name="correlativo" id="correlativo" class="form-control input-xs" readonly="" value="<?php echo (!empty($venta["correlativo"])) ? $venta["correlativo"] : ""; ?>">
				</div>
			</div>
		</div>

		<div class="col-md-4">
			<label>Cliente</label>
			<input type="text" name="cliente" id="cliente_razonsocial" value="<?php echo (!empty($venta["full_nombres"])) ? $venta["full_nombres"] : ""; ?>" class="form-control input-xs" placeholder="Nombre, DNI, razon social o RUC">
			<div class="input-group">
				<!--
				<span class="input-group-btn tooltip-demo">
					<button type="button" id="btn-buscar-cliente" class="btn btn-outline btn-primary" data-toggle="tooltip" title="Buscar clientes">
						<i class="fa fa-search"></i>
					</button>
					<button type="button" id="btn-registrar-cliente" class="btn btn-outline btn-primary" data-toggle="tooltip" title="&iquest;No existe el cliente? Registrar aqui">
						<i class="fa fa-edit"></i>
					</button>
				</span>
				-->
			</div>
		</div>
		
		<div class="col-md-4">
			<label class="label_doc_cli">
				<?php if( isset($venta['ruc_obligatorio'])  && $venta['ruc_obligatorio']=='S') echo "RUC"; else echo "DNI"; ?>
			</label>
			<div class="input-group">
				<input type="text" name="cliente_doc" id="cliente_doc" readonly="readonly" value="<?php echo (!empty($venta["doc_cliente"])) ? $venta["doc_cliente"] : ""; ?>" class="form-control input-xs" placeholder="DNI/RUC">
				<span class="input-group-btn tooltip-demo">
					<button type="button" id="btn-buscar-cliente" class="btn btn-outline btn-primary btn-xs" data-toggle="tooltip" title="Buscar clientes">
						<i class="fa fa-search"></i>
					</button>
					<button type="button" id="btn-registrar-cliente" class="btn btn-outline btn-primary btn-xs" data-toggle="tooltip" title="&iquest;No existe el cliente? Registrar aqui">
						<i class="fa fa-file-o"></i>
					</button>
					
					<button type="button" id="btn-edit-cliente" class="btn btn-outline btn-primary btn-xs" data-toggle="tooltip" title="Editar Cliente">
						<i class="fa fa-edit"></i>
					</button>
					<?php if($validar_ruc == "S") {?>
					<button type="button" id="btn-consultar-ruc" class="btn btn-outline btn-default btn-xs" data-toggle="tooltip" title="Consultar RUC">
						<img src="<?php echo base_url("app/img/sunat.png");?>" style="width:12px;">
					</button>
					<?php }?>
				</span>
			</div>
		</div>
		<!--
		<div class="col-md-10">
			<div class="form-group">
				<div class="row">
					
					
					<div class="col-md-3 msg-about-cliente"></div>
				</div>
			</div>
		</div>-->
		
		<!--
		<div class="col-sm-2" style="border:1px solid red;">
			<div class="form-group">
				<label style="display:block;">&nbsp;</label>
				<button id="btn-search-preventa" class="btn btn-white"><i class="fa fa-search"></i> Preventa <sub class='hotkey white'>(F9)</sub></button>
			</div>
		</div>
		-->
	</div>
	
	<div class="row">
		<div class="col-md-3">
			<!--<div class="row">
				<div class="col-sm-12">
					<div class="form-group">
						<label class="required">Nro. documento <?php echo ($editar_correlativo=='S')? '<input type="checkbox" name="edit_correlativo" value="1" id="edit_correlativo" title="Cambiar correlativo">':'';?></label>
						<div class="row">
							<div class="col-sm-6">
								<?php echo $serie; ?>
							</div>
							<div class="col-sm-6">
								<input type="text" name="correlativo" id="correlativo" class="form-control input-xs" readonly="" value="<?php echo (!empty($venta["correlativo"])) ? $venta["correlativo"] : ""; ?>">
							</div>
						</div>
					</div>
				</div>
			</div>-->
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<label>Fecha emision</label>
						<input type="text" name="fecha_venta" id="fecha_venta" class="form-control input-xs" value="<?php echo (!empty($venta["fecha_venta"])) ? fecha_es($venta["fecha_venta"]) : date("d/m/Y"); ?>" readonly="">
					</div>
				</div>
				
				<!--
				<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<label>Fecha vencim</label>
						<input type="text" name="fecha_venta" id="fecha_venta" class="form-control input-xs" value="<?php echo (!empty($venta["fecha_venta"])) ? fecha_es($venta["fecha_venta+"]+30) : date("d/m/Y"); ?>">
					</div>
				</div>-->
				
				<div class="col-sm-6">
					<div class="form-group">
						<label class="required">Tipo venta</label>
						<?php echo $tipoventa; ?>
					</div>
				</div>
			</div>
			<!--
			<div class="row">
				<div class="col-sm-12">
					<div class="form-group">
						<label class="required">Tipo venta</label>
						<?php echo $tipoventa; ?>
					</div>
				</div>
			</div>
			-->
			<div class="row">
				<div class="col-sm-12">
					<div class="form-group">
						<label class="required">Moneda</label>
						<div class="row">
							<div class="col-sm-7">
								<?php echo $moneda; ?>
							</div>
							<div class="col-sm-5">
								<input type="text" name="cambio_moneda" id="cambio_moneda" class="form-control numero input-xs" readonly="" value="<?php echo (!empty($venta["cambio_moneda"])) ? $venta["cambio_moneda"] : ""; ?>">
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="form-group">
						<label>Vendedor</label>
						<div class="input-group">
							<?php echo $vendedor; ?>
							<span class="input-group-btn">
								<button type="button" class="btn btn-primary btn-xs btn-search-vendedor" tabindex="-1" data-toggle="tooltip" title="Buscar otros empleados">
									<i class="fa fa-search" aria-hidden="true"></i>
								</button>
							</span>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="form-group">
						<label class="required">Almacen</label>
						<?php echo $almacen; ?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="form-group">
						<label>Nro. Guias</label>
						<input type="text" name="guias_remision" id="guias_remision" class="form-control input-xs" value="<?php echo (!empty($venta["guias_remision"])) ? $venta["guias_remision"] : ""; ?>">
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="form-group">
						<label>Tipo de operaci&oacute;n</label>
						<?php echo $tipo_operacion; ?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="form-group">
						<label>Modalidad venta</label>
						<?php echo $modalidad; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-9">
			<div class="panel panel-default">
				<div class="panel-heading">
					<i class="fa fa-shopping-cart"></i> Detalle venta
					<div class="pull-right"><button id="btn-search-preventa" class="btn btn-success btn-xs" tabindex='-1'><i class="fa fa-search"></i> Preventa <sub class='hotkey white'>(F9)</sub></button></div>
				</div>
				<div class="panel-body">
					<!--<p>Busque los productos en el recuadro de abajo.
					Presione la tecla 
					<span class="fa-stack">
						<i class="fa fa-square-o fa-stack-2x"></i>
						<i class="fa fa-level-down fa-rotate-90 fa-stack-1x"></i>
					</span> (<i>Enter</i>) para agregar los productos a la tabla.</p>-->
					<div class="row m-b-sm m-t-sm">
						<div class="col-md-12">
							<input type="hidden" id="producto_idproducto">
							<input type="hidden" id="producto_has_serie">
							<input type="hidden" id="producto_idunidad">
							<input type="hidden" id="producto_idalmacen">
							<input type="hidden" id="producto_serie">
							<div class="input-group tooltip-demo">
								<span class="input-group-addon" data-toggle="tooltip" title="Buscar por serie o c&oacute;digo de barras">
									<input type="checkbox" value="1" id="buscar_serie" name="buscar_serie">
								</span>
								<input type="text" name="producto" id="producto_descripcion" class="form-control" placeholder="Ingrese el nombre o codigo del producto">
								<span class="input-group-addon" >
									<sub class='hotkey white'>(F2)</sub> 
								</span>
							</div>
						</div>
						<!--
						<div class="col-md-3">
							<button type="button" id="btn-buscar-producto" class="btn btn-white btn-sm"><i class="fa fa-search"></i> Buscar Producto</button>
						</div>
						-->
					</div>
					<div class="table-responsive">
						<table id="tbl-detalle" class="table table-hover tooltip-demo detail-table">
							<thead>
								<tr>
									<th style="width: 2%;"></th>
									<th>Producto</th>
									<th style="width:8%">U.Med.</th>
									<?php if($mostrar_precio_costo == "S") {?>
									<th style="width:5%">Costo</th>
									<?php }?>
									<th style="width:5%">Stock</th>
									<th style="width:5%">Cant.</th>
									<th style="width:8%">P.U.</th>
									<th style="width:8%">Total</th>
									<th style="width:2%"></th>
									<th style="width:2%"></th>
									<th style="width:8%">Grupo Op</th>
									<th style="width:8%">Tipo IGV</th>
									<th style="width:2%"></th>
									<th style="display:none;"></th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
					<div class="row">
						<div class="col-sm-2">
							<div class="form-group">
								<div class="form-group">
									<label>&iquest;Pasa a despacho?</label>
									<div class="onoffswitch">
										<input type="checkbox" name="pasa_despacho" id="pasa_despacho" class="onoffswitch-checkbox" value="1" <?php echo (isset($venta["pasa_despacho"]) && $venta["pasa_despacho"] == 'S') ? "checked" : ""; ?>>
										<label class="onoffswitch-label" for="pasa_despacho">
											<span class="onoffswitch-inner"></span>
											<span class="onoffswitch-switch"></span>
										</label>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<label class="required">Subtotal</label>
								<input type="text" name="subtotal" id="subtotal" value="<?php echo (!empty($venta["subtotal"])) ? number_format($venta["subtotal"],$fixed,'.','') : ""; ?>" class="form-control numero input-xs" required="" readonly="">
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<label>IGV</label>
								<input type="text" name="igv" id="igv" value="<?php echo (!empty($venta["igv"])) ? $venta["igv"] : ""; ?>" class="form-control numero input-xs" readonly="">
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<label>Descuento</label>
								<input type="text" name="descuento" id="descuento" value="<?php echo (!empty($venta["descuento"])) ? $venta["descuento"] : ""; ?>" class="form-control numero input-xs">
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<label class="required">Total</label>
								<input type="text" name="total" id="total" value="<?php echo (!empty($venta["total"])) ? number_format($venta["total"],$fixed,'.','') : ""; ?>" class="form-control numero input-xs" required="" readonly="">
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group">
								<label >Observaci&oacute;n</label>
								<input type="text" name="observacion" id="observacion" value="<?php echo (!empty($venta["observacion"])) ? number_format($venta["observacion"],$fixed,'.','') : ""; ?>" class="form-control input-xs">
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12" id="info-saldo-cliente"></div>
			</div>
			
			<div class="row">
						<div class="form-group">
							<div class="col-sm-6 text-left">
								<button class="btn btn-sm btn-warning btn_cancel" data-controller="<?php echo $controller; ?>"><sub class='hotkey white'>( Esc ) </sub> Cancelar</button>
							</div>
							<div class="col-sm-6 text-right">
								<?php if($readonly == false && $anulado == false) { ?>
								<button id="btn_save_venta" class="btn btn-sm btn-primary" data-controller="<?php echo $controller; ?>"><sub class='hotkey white'>(F4)</sub> Guardar</button>
								<?php } ?>
							</div>
						</div>
					</div>
		</div>
	</div>
</form>

<div id="modal-product-list" data-keyboard="false" class="modal fade" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Resultado de la b&uacute;squeda</h4>
			</div>
			<div class="modal-body">
				<p>Se han encontrado <span class="count-result-list"></span> resultados. Seleccione el item que corresponde.</p>
				<div class="list-group result-list"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<div id="modal-series" class="modal fade" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"></h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<div class="input-group">
							<input id="input-text-serie" placeholder="Ingrese la serie" class="input-sm form-control text-uppercase" type="text" />
							<span class="input-group-btn">
								<button id="btn-search-serie" type="button" class="btn btn-sm btn-white"><i class="fa fa-search"></i> Buscar</button>
							</span>
						</div>
					</div>
				</div>
				<div class="table-responsive div_scroll" style="max-height:300px;">
					<table id="table-serie" class="table table-striped">
						<tbody></tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="btn-close-serie" class="btn btn-primary">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<div id="modal-cliente" class="modal fade" data-keyboard="false" aria-hidden="true" aria-labelledby="myLargeModalCliente" data-backdrop="static">
	<div class="modal-dialog modal-lg" style="margin-top: 10px;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
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

<div id="modal-consultar-producto" class="modal fade" data-keyboard="false" aria-hidden="true" aria-labelledby="myLargeModalLabel" data-backdrop="static">
	<div class="modal-dialog modal-lg" >
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Consultar Producto</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<?php //echo $form_consultarpr; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="modal-precio-tempp" class="modal fade" data-keyboard="false"  aria-hidden="true" aria-labelledby="myLargeModalPrecio" data-backdrop="static">
	<div class="modal-dialog modal-sm" >
		<div class="modal-content">
			<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Agregar precio</h4>
			</div>
			
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12"><input type="text" id="ptemp" class="form-control"></div>
				</div>
			</div>
			
			<div class="modal-footer">
				<button type="button" id="btn-close" class="btn btn-sm btn-white" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>
<?php echo $modal_pago; ?>

<div style="display:none;"><?php echo $combo_grupo_igv.'</br>'.$combo_tipo_igv;?></div>
<script>
var default_grupo_igv = <?php echo (!empty($default_igv)) ? "'$default_igv'" : "false"; ?>;
var validar_ruc = <?php echo ($validar_ruc == "S") ? "true" : "false"; ?>;
var mostrar_precio_costo = <?php echo ($mostrar_precio_costo == "S") ? "true" : "false"; ?>;
var precio_venta_cero = <?php echo ($precio_venta_cero == "S") ? "true" : "false"; ?>;
</script>

<style>
	.here_transparent{background: #808080;   opacity: 0.9;}
	.numero{text-align:right;}
	table#dtpreventa_view_popup tbody>tr>td{padding: 4px !important;}
	table#dtcliente_view_popup tbody>tr>td{padding: 4px !important;}
	.hotkey.white {
		color: #ddd;
	}
	sub.hotkey{bottom: 0;}
	
	.block_content {
		position: absolute;
		top: 80px;
		bottom: 0;
		right: 0;
		left: 0;
	}
	
	.mensajillo {
		-webkit-transform: rotate(343deg);
		-moz-transform: rotate(343deg);
		-o-transform: rotate(343deg);
		writing-mode: lr-tb;
		color: red;
		font-weight: bold;
		font-size: 95px;
		border: 0px solid red;
		text-align: center;
		margin-left: 150px;
		opacity: 0.4;
		filter: alpha(opacity=40);
		z-index:100;
		width:100%;
	}
</style>