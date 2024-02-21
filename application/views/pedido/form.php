<?php 
if( ! empty($pedido["aprobado"]) && $pedido["aprobado"] == "S") {
?>
<div class="alert alert-info">
	<strong class="alert-link">¡Pedido Aprobado!</strong> usted esta viendo un pedido de compra aprobado.
</div>
<?php 
}
?>
<form id="form_<?php echo $controller; ?>" class="app-form form-uppercase">
	<input type="hidden" name="idpedido" id="idpedido" value="<?php echo (!empty($pedido["idpedido"])) ? $pedido["idpedido"] : ""; ?>">
	<div class="row">
		<div class="col-sm-6">
			<div class="form-group">
				<label class="required">Tipo Pedido</label>
				<?php echo $tipo_pedido; ?>
			</div>
		</div>
		<div class="col-sm-6">
			<div class="form-group">
				<label>Proveedor</label>
				<div class="input-group">
					<input type="hidden" name="idproveedor" id="idproveedor" value="<?php echo (!empty($proveedor["idproveedor"])) ? $proveedor["idproveedor"] : ""; ?>">
					<input type="text" name="proveedor" id="proveedor" value="<?php echo (!empty($proveedor["nombre"])) ? $proveedor["nombre"] : ""; ?>" class="form-control input-sm" placeholder="Razon social o RUC">
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
		<div class="col-sm-6">
			<div class="form-group">
				<label class="required">Descripcion</label>
				<input type="text" name="descripcion" id="descripcion" value="<?php echo (!empty($pedido["descripcion"])) ? $pedido["descripcion"] : ""; ?>" class="form-control" required="">
			</div>
		</div>
		<div class="col-sm-3">
			<div class="form-group">
				<label class="required">Fecha emision</label>
				<div class="input-group date">
					<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					<input type="text" name="fecha" id="fecha" value="<?php echo (!empty($pedido["fecha"])) ? dateFormat($pedido["fecha"], "d/m/Y") : date("d/m/Y"); ?>" class="form-control" required="">
				</div>
			</div>
		</div>
		<div class="col-sm-3">
			<div class="form-group">
				<label class="required">Almacen</label>
				<?php echo $almacen; ?>
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
								<input type="text" name="producto" id="producto_descripcion" placeholder="Nombre o codigo del producto" class="input-sm form-control">
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
									<th style="width: 5%"></th>
									<th style="width: 65%;">Producto</th>
									<th style="width: 10%;">U.Med.</th>
									<th style="width: 10%;">Cant.</th>
									<th style="width: 10%"></th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	

	<div class="row">
		<div class="form-group">
			<div class="col-lg-12">
				<?php if( ! empty($tabkey)) { ?>
				<button id="btn_cerrar_tab" class="btn btn-sm btn-white" data-tabkey="<?php echo $tabkey; ?>">Cerrar pesta&ntilde;a</button>
				<?php } else {?>
				<button class="btn btn-sm btn-white btn_cancel" data-controller="<?php echo $controller; ?>">Cancelar</button>
				<?php } ?>
				<?php //if(empty($pedido["aprobado"]) || $pedido["aprobado"] == "N") { ?>
				<button id="btn_save_pedido" class="btn btn-sm btn-primary" data-controller="<?php echo $controller; ?>">Guardar</button>
				<?php //} ?>
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