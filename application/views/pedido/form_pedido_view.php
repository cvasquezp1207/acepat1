<form id="form_<?php echo $controller; ?>" class="app-form">
	<input type="hidden" name="idpedido" id="idpedido" value="<?php echo (!empty($pedido["idpedido"])) ? $pedido["idpedido"] : ""; ?>">
	<input type="hidden" name="idproveedor" id="proveedor_idproveedor" value="<?php echo (!empty($pedido["idproveedor"])) ? $pedido["idproveedor"] : ""; ?>">
	<div class="row">
		<div class="col-sm-6">
			<div class="row">
				<div class="col-sm-12">
					<div class="form-group">
						<label class="required">Proveedor</label>
						<div class="input-group">
							<input type="text" name="proveedor" id="proveedor_descripcion" value="<?php echo (!empty($proveedor["descripcion"])) ? $proveedor["descripcion"] : ""; ?>" class="form-control" required="">
							<span class="input-group-btn tooltip-demo">
								<button type="button" id="btn-buscar-proveedor" class="btn btn-outline btn-primary" data-toggle="tooltip" title="Buscar proveedores">
									<i class="fa fa-search"></i>
								</button>
								<button type="button" id="btn-registrar-proveedor" class="btn btn-outline btn-primary" data-toggle="tooltip" title="&iquest;No existe el proveedor? Registrar aqui">
									<i class="fa fa-edit"></i>
								</button>
							</span>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<label class="required">Tipo compra</label>
						<?php echo $tipocompra; ?>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<label class="required">Fecha compra</label>
						<div class="input-group date">
							<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
							<input type="text" name="fecha" id="fecha" value="<?php echo (!empty($pedido["fecha"])) ? dateFormat($pedido["fecha"], "d/m/Y") : ""; ?>" class="form-control" required="">
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<label class="required">Moneda</label>
						<?php echo $moneda; ?>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<label>Cambio moneda</label>
						<input type="text" name="cambio_moneda" id="cambio_moneda" value="<?php echo (!empty($pedido["cambio_moneda"])) ? $pedido["cambio_moneda"] : ""; ?>" class="form-control">
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-6">
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<label class="required">Tipo documento</label>
						<?php echo $tipodocumento; ?>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<label class="required">Nro. documento</label>
						<input type="text" name="nrodocumento" id="nrodocumento" value="<?php echo (!empty($pedido["nrodocumento"])) ? $pedido["nrodocumento"] : ""; ?>" class="form-control" required="">
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<label>Nro. guias</label>
						<input type="text" name="nroguias" id="nroguias" value="<?php echo (!empty($pedido["nroguias"])) ? $pedido["nroguias"] : ""; ?>" class="form-control">
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<label class="required">Almacen</label>
						<?php echo $almacen; ?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<label>
							Compra anticipada 
							<abbr title="Si la compra es anticipada, los productos no ingresan al almacen (no se consideran en stock). Para ingresar los productos al almacen de una compra anticipada se debe ir al modulo de Recepcionar Compra.">?</abbr>
						</label>
						<div class="onoffswitch">
							<input type="checkbox" name="recepcionado" id="recepcionado" class="onoffswitch-checkbox" value="1" <?php echo (isset($pedido["recepcionado"]) && $pedido["recepcionado"] == 'N') ? "checked" : ""; ?>>
							<label class="onoffswitch-label" for="recepcionado">
								<span class="onoffswitch-inner"></span>
								<span class="onoffswitch-switch"></span>
							</label>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">Detalle pedido</div>
				<div class="panel-body">
					<div class="row m-b-sm m-t-sm">
						<div class="col-md-2">
							<button type="button" id="btn-buscar-producto" class="btn btn-white btn-sm"><i class="fa fa-search"></i> Buscar Producto</button>
						</div>
						<div class="col-md-10">
							<div class="input-group">
								<input type="hidden" name="producto_idproducto" id="producto_idproducto">
								<input type="text" name="producto" id="producto_descripcion" placeholder="o escribir aqui el nombre del producto" class="input-sm form-control">
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
									<th><input type="checkbox" onClick="SeleccionarTodo"></th>
									<th>Cant.</th>
									<th style="width: 8%;">U.Med.</th>
									<th style="width: 30%;">Producto</th>
									<th>P.U.</th>
									<th>Total</th>
									<th>IGV</th>
									<th>Flete</th>
									<th>Gastos</th>
									<th>P.Costo</th>
									<th></th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<?php
								$ind = 0;
								if(!empty($pedido_det)) {
									foreach($pedido_det as $val){
										echo '<tr>
												<td><input type="checkbox" name ="checkall1 id="items_'.$ind.'" checked value="'.$val["idproducto"].'" onClick="SeleccionarTodo1('.$ind.');" /></td>							
												<td><input type="text" name="deta_cantidad[]" class="deta_cantidad" value="'.$val["cantidad"].'"></td>
												<td><input type="text" name="deta_idunidad[]" class="deta_idunidad" value="'.$val["idunidad"].'"></td>
												<td><input type="text" name="deta_idproducto[]" class="deta_idproducto" value="'.$val["idproducto"].'"></td>
												<td><input type="text" name="deta_precio[]" class="form-control input-sm deta_precio" value=""></td>
												<td><input type="text" name="deta_importe[]" class="form-control input-sm font-bold deta_importe" readonly></td>
												<td><input type="text" name="deta_igv[]" class="form-control input-sm text-success deta_igv" readonly></td>
												<td><input type="text" name="deta_flete[]" class="form-control input-sm text-success deta_flete" readonly></td>
												<td><input type="text" name="deta_gastos[]" class="form-control input-sm text-success deta_gastos" readonly></td>
												<td><input type="text" name="deta_costo[]" class="form-control input-sm text-success font-bold deta_costo" value="" readonly></td>
												<td><button class="btn btn-danger btn-xs btn_deta_delete" data-toggle="tooltip" title="Eliminar registro"><i class="fa fa-trash"></i></button></td>
											</tr>';
									}
								}
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-2">
			<div class="form-group">
				<label class="required">Subtotal</label>
				<input type="text" name="subtotal" id="subtotal" value="<?php echo (!empty($pedido["subtotal"])) ? $pedido["subtotal"] : ""; ?>" class="form-control" required="" readonly="">
			</div>
		</div>
		<div class="col-sm-2">
			<div class="form-group">
				<label>IGV</label>
				<div class="input-group">
					<span class="input-group-addon"><input type="checkbox" name="valor_igv" id="valor_igv" value="<?php echo $valor_igv;?>"></span>
					<!--<span class="input-group-addon">
						<label class="checkbox-inline i-checks"><input type="checkbox" value="option1"></label>
					</span>-->
					<input type="text" name="igv" id="igv" value="<?php echo (!empty($pedido["igv"])) ? $pedido["igv"] : ""; ?>" class="form-control" readonly="">
				</div>
			</div>
		</div>
		<div class="col-sm-2">
			<div class="form-group">
				<label>Descuento</label>
				<input type="text" name="descuento" id="descuento" value="<?php echo (!empty($pedido["descuento"])) ? $pedido["descuento"] : ""; ?>" class="form-control">
			</div>
		</div>
		<div class="col-sm-2">
			<div class="form-group">
				<label>Flete</label>
				<input type="text" name="flete" id="flete" value="<?php echo (!empty($pedido["flete"])) ? $pedido["flete"] : ""; ?>" class="form-control">
			</div>
		</div>
		<div class="col-sm-2">
			<div class="form-group">
				<label>% Gastos</label>
				<input type="text" name="gastos" id="gastos" value="<?php echo (!empty($pedido["gastos"])) ? $pedido["gastos"] : ""; ?>" class="form-control">
			</div>
		</div>
		<div class="col-sm-2">
			<div class="form-group">
				<label class="required">Total</label>
				<input type="text" name="total" id="total" value="<?php echo (!empty($total)) ? $total : ""; ?>" class="form-control" required="" readonly="">
			</div>
		</div>
	</div>
	<div class="row">
		<div class="form-group">
			<div class="col-lg-12">
				<button class="btn btn-sm btn-white btn_cancel" data-controller="<?php echo $controller; ?>">Cancelar</button>
				<button id="btn_save_compra" class="btn btn-sm btn-primary" data-controller="<?php echo $controller; ?>">Guardar</button>
			</div>
		</div>
	</div>
</form>

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