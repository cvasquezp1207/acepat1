<?php
if( ! isset($editable)) {
	$editable = true;
}
if($editable == false) {
?>
<div class="alert alert-danger">
	<strong class="alert-link">¡Compra recepcionado!</strong> para modificar una compra recepcionado, primero elimine las recepciones.
</div>
<?php 
}
?>

<div class="row">
	<div class="col-sm-6">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5>Datos de la compra</h5>
				<div class="ibox-tools">
					<a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
					<a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false"><i class="fa fa-wrench"></i></a>
					<ul class="dropdown-menu dropdown-user">
						<li><a href="#" id="buscar-pedido"><i class="fa fa-search"></i> Buscar pedido</a></li>
					</ul>
				</div>
			</div>
			
			<div class="ibox-content">
				<form aria-control="<?php echo $controller; ?>" class="app-form form-uppercase">
					<input type="hidden" name="idcompra" id="idcompra" value="<?php echo (!empty($compra["idcompra"])) ? $compra["idcompra"] : ""; ?>">
					<input type="hidden" name="idproveedor" id="proveedor_idproveedor" value="<?php echo (!empty($compra["idproveedor"])) ? $compra["idproveedor"] : ""; ?>">
					<input type="hidden" name="flete_convertido" id="flete_convertido" value="<?php echo (!empty($compra["flete_convertido"])) ? $compra["flete_convertido"] : ""; ?>">
		
					<div class="row">
						<div class="col-sm-4">
							<div class="form-group">
								<label class="required">Tipo documento</label>
								<?php  echo $tipodocumento; ?>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label class="required" style="font-size: 12px;">Serie</label>
								<input type="text" name="serie" id="serie" value="<?php echo (!empty($compra["serie"])) ? $compra["serie"] : ""; ?>" class="form-control input-sm" maxlength="4" required="">
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label class="required">Numero</label>
								<input type="text" name="numero" id="numero" value="<?php echo (!empty($compra["numero"])) ? $compra["numero"] : ""; ?>" class="form-control input-sm" maxlength="8" required="">
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label class="required">Proveedor</label>
								<div class="input-group">
									<input type="text" name="proveedor" id="proveedor_descripcion" value="<?php echo (!empty($proveedor["nombre"])) ? $proveedor["nombre"] : ""; ?>" class="form-control input-sm" placeholder="Razon social o RUC" required="">
									<span class="input-group-btn tooltip-demo">
										<button type="button" id="btn-buscar-proveedor" class="btn btn-outline btn-primary btn-sm" data-toggle="tooltip" title="Buscar proveedores">
											<i class="fa fa-search"></i>
										</button>
										<button type="button" id="btn-registrar-proveedor" class="btn btn-outline btn-primary btn-sm" data-toggle="tooltip" title="&iquest;No existe el proveedor? Registrar aqui">
											<i class="fa fa-edit"></i>
										</button>
									</span>
								</div>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-sm-4">
							<div class="form-group">
								<label class="required">Fecha compra</label>
								<input type="text" name="fecha_compra" id="fecha_compra" value="<?php echo (!empty($compra["fecha_compra"])) ? dateFormat($compra["fecha_compra"], "d/m/Y") : date("d/m/Y"); ?>" class="form-control input-sm" placeholder="<?php echo date("d/m/Y"); ?>" required="">
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label class="required">Moneda</label>
								<?php echo $moneda; ?>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label>Cambio moneda</label>
								<input type="text" name="cambio_moneda" id="cambio_moneda" value="<?php echo (!empty($compra["cambio_moneda"])) ? $compra["cambio_moneda"] : ""; ?>" class="form-control input-sm numero">
							</div>
						</div>
					</div>
				</form>
			</div><!-- ibox-content -->
		</div><!-- ibox -->
	</div>
	<div class="col-sm-3">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5>Otros datos</h5>
				<div class="ibox-tools">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div class="ibox-content">
				<form aria-control="<?php echo $controller; ?>" class="app-form form-uppercase">
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
								<label>
									Ingresar productos al almacen
								</label>
								<div class="onoffswitch">
									<input type="checkbox" name="recepcionado" id="recepcionado" class="onoffswitch-checkbox" value="1" <?php echo (isset($compra["recepcionado"]) && $compra["recepcionado"] == 'S') ? "checked" : ""; ?>>
									<label class="onoffswitch-label" for="recepcionado">
										<span class="onoffswitch-inner"></span>
										<span class="onoffswitch-switch"></span>
									</label>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label>Guias ref. / Doc. flete</label>
								<input type="text" name="nroguias" id="nroguias" value="<?php echo (!empty($compra["nroguias"])) ? $compra["nroguias"] : ""; ?>" class="form-control input-sm">
							</div>
						</div>
					</div>
				</form>
			</div><!-- ibox-content -->
		</div><!-- ibox -->
	</div>
	<div class="col-sm-3">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5>Opciones de pago</h5>
				<div class="ibox-tools">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div class="ibox-content">
				<form aria-control="<?php echo $controller; ?>" class="app-form form-uppercase">
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label class="required">Tipo compra</label>
								<?php echo $tipocompra; ?>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label class="">Forma pago</label>
								<?php echo $forma_pago_compra; ?>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label class="">Nro. letras</label>
								<input type="text" name="nro_letras" id="nro_letras" value="<?php echo (!empty($compra["nro_letras"])) ? $compra["nro_letras"] : ""; ?>" class="form-control input-sm">
							</div>
						</div>
					</div>
				</form>
			</div><!-- ibox-content -->
		</div><!-- ibox -->
	</div>
</div>

<div class="row">
	<div class="col-sm-12">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5>Detalle de la compra</h5>
				<div class="ibox-tools">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div class="ibox-content">
				<form aria-control="<?php echo $controller; ?>" class="app-form form-uppercase">
					<div class="row m-b-sm m-t-sm">
						<!--
						<div class="col-md-2">
							<button type="button" id="btn-buscar-producto" class="btn btn-white btn-sm"><i class="fa fa-search"></i> Buscar Producto</button>
						</div>
						-->
						<div class="col-md-12">
							<div class="input-group">
								<input type="hidden" name="producto_idproducto" id="producto_idproducto">
								<span class="input-group-btn" style="height:10px !important;">
									<button type="button" id="f2" class="btn btn-white btn-sm"><sub class='hotkey white'>(F2)</sub> </button>
									<!--
										<sub class='hotkey white'>(F2)</sub> 
									-->
									<!--
									<button type="button" disabled id="" class="btn btn-sm btn-outline btn-default" data-toggle="tooltip" title="Agregar producto a la tabla">
										<i class="fa fa-share"></i> <i class="fa fa-shopping-cart"></i>
									</button>
									-->
								</span>
								<input type="text" name="producto" id="producto_descripcion" placeholder="Nombre o Codigo del producto" class="input-sm form-control">
								<span class="input-group-btn tooltip-demo">
									<button type="button" id="btn-agregar-producto" class="btn btn-sm btn-outline btn-primary" data-toggle="tooltip" title="Agregar producto a la tabla">
										<i class="fa fa-share"></i> <i class="fa fa-shopping-cart"></i>
									</button>
									<button type="button" id="btn-registrar-producto" class="btn btn-sm btn-outline btn-primary" data-toggle="tooltip" title="&iquest;No existe el producto? Registrar aqui">
										<i class="fa fa-edit"></i>
									</button>
								</span>
							</div>
						</div>
					</div>
					
					<div class="table-responsive">
						<table id="tbl-detalle" class="table table-striped tooltip-demo detail-table">
							<thead>
								<tr>
									<th></th>
									<th style="width: 30%;">Producto</th>
									<th style="width: 8%;">U.Med.</th>
									<th>Cant.</th>
									<th>Precio</th>
									<th>P.Inc.IGV</th>
									<th>Total</th>
									<th>Total.Inc.IGV</th>
									<th>IGV</th>
									<th>Flete</th>
									<th>Dscto.</th>
									<th>P.Costo</th>
									<th></th>
									<th></th>
									<th style="display:none;"></th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
					
					<div class="row">
						<div class="col-sm-2 col-sm-offset-2">
							<div class="form-group">
								<label class="required">Subtotal</label>
								<input type="text" name="subtotal" id="subtotal" value="<?php echo (!empty($compra["subtotal"])) ? $compra["subtotal"] : ""; ?>" class="form-control numero" required="" readonly="">
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<label>IGV</label>
								<div class="input-group">
									<span class="input-group-addon"><input type="checkbox" name="valor_igv" id="valor_igv" value="<?php echo $valor_igv;?>" <?php echo (!empty($compra["igv"]) and $compra["igv"]>0) ? "checked" : ""; ?>></span>
									<!--<span class="input-group-addon">
										<label class="checkbox-inline i-checks"><input type="checkbox" value="option1"></label>
									</span>-->
									<input type="text" name="igv" id="igv" value="<?php echo (!empty($compra["igv"])) ? $compra["igv"] : ""; ?>" class="numero form-control" readonly="">
								</div>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<label>Descuento</label>
								<input type="text" name="descuento" id="descuento" value="<?php echo (!empty($compra["descuento"])) ? $compra["descuento"] : ""; ?>" class="form-control numero">
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<label>Flete</label>
								<div class="input-group">
									<span class="input-group-addon" style="padding: 0px;">
										<?php echo $moneda_flete; ?>
										<input type="text" class="input_min numero" id="cambio_moneda_flete" name="cambio_moneda_flete" value="<?php echo (!empty($compra["cambio_moneda_flete"])) ? $compra["cambio_moneda_flete"] : ""; ?>" title="Tipo de cambio">
									</span>
									<input type="text" name="flete" id="flete" value="<?php echo (!empty($compra["flete"])) ? $compra["flete"] : ""; ?>" class="form-control numero">
								</div>
							</div>
						</div>
						<!--<div class="col-sm-2">
							<div class="form-group">
								<label>% Gastos</label>
								<div class="input-group">
									<span class="input-group-addon"  style="padding: 0px 0px;">
										<?php echo $moneda_gastos;?>
									</span>
									<input type="text" name="gastos" id="gastos" value="<?php echo (!empty($compra["gastos"])) ? $compra["gastos"] : ""; ?>" class="form-control numero">
								</div>
							</div>
						</div>-->
						<div class="col-sm-2">
							<div class="form-group">
								<label class="required">Total</label>
								<input type="text" name="total" id="total" value="<?php echo (!empty($total)) ? $total : ""; ?>" class="form-control numero" required="" readonly="">
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="form-group">
							<div class="col-sm-6 text-left">
								<button class="btn btn-sm btn-warning btn_cancel" data-controller="<?php echo $controller; ?>"><sub class='hotkey white'>(Esc)</sub> Cancelar</button>
							</div>
							<?php if($editable) { ?>
							<div class="col-sm-6 text-right">
								<button id="btn_save_compra" class="btn btn-sm btn-primary" data-controller="<?php echo $controller; ?>"><sub class='hotkey white'>(F4)</sub> Guardar</button>
							</div>
							<?php } ?>
						</div>
					</div>
				</form>
			</div><!-- ibox-content -->
		</div><!-- ibox -->
	</div>
</div>

<div id="modal-producto" class="modal fade" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Registrar producto</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<?php echo $form_producto; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="modal-unidad_medida" class="modal fade" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Unidad de medida <small id="uni_producto_descripcion"></small></h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<?php echo $form_producto_unidad; ?>
				</div>
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
						<div class="input-group"><input id="input-text-serie" placeholder="Ingrese la serie" class="input-sm form-control text-uppercase" type="text" />
							<span class="input-group-btn"><button id="btn-add-serie" type="button" class="btn btn-sm btn-primary">Agregar</button></span></div>
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

<div id="modal-proveedor" class="modal fade" aria-hidden="true" aria-labelledby="myLargeModalLabel" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Registrar Proveedor</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<?php echo $form_proveedor; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php echo $modal_pago; ?>

<style>
	.here_transparent{background: #808080;   opacity: 0.9;}
	.numero{text-align:right;}
	table#dtpreventa_view_popup tbody>tr>td{padding: 4px !important;}
	table#dtcliente_view_popup tbody>tr>td{padding: 4px !important;}
	.hotkey.white {
		color: #ccc;
	}
	sub.hotkey{bottom: 0;}
	.combo_min{
		padding: 0;
		margin: 0;
		font-size: .8em;
		border: 0;
		outline: none;
		display: block;
		width: 100%;
	}
	.input_min {
		width: 60px;
		display: block;
		font-size: .9em;
		padding: 2px;
		border: 0;
	}
</style>