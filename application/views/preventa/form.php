<form id="form_<?php echo $controller; ?>" class="app-form">
	<input type="hidden" name="idpreventa" id="idpreventa" value="<?php echo (!empty($preventa["idpreventa"])) ? $preventa["idpreventa"] : ""; ?>">
	<input type="hidden" name="idcliente" id="compra_idcliente" value="<?php echo (!empty($preventa["idcliente"])) ? $preventa["idcliente"] : ""; ?>">
	<input type="hidden" name="estado_cliente" id="estado_cliente">
	<div class="row">
		<div class="col-md-2">
			<div class="row">
				<div class="col-sm-12">
					<div class="form-group">
						<label class="required">Tipo documento</label>
						<?php echo $tipodocumento; ?>
					</div>
				</div>
			</div>
			<!--<div class="row">
				<div class="col-sm-12">
					<div class="form-group">
						<label>Serie</label>
						<?php // echo $comboserie; ?>
					</div>
				</div>
			</div>-->
			<div class="row">
				<div class="col-sm-12">
					<div class="form-group">
						<label class="required">Tipo venta</label>
						<?php echo $tipoventa; ?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="form-group">
						<label class="required">Moneda</label>
						<?php echo $moneda; ?>
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
						<label>Tipo de operaci&oacute;n</label>
						<?php echo $tipo_operacion; ?>
					</div>
				</div>
			</div>
			<div class = "row">
				<div class="col-sm-12">
				<div class="form-group">
						<label>Modalidad Venta:</label>
						<?php echo $modalidad; ?>
					</div>
				
				</div>
			</div>
			<!--<div class="col-sm-2" style="border:0px solid red;">
			<label class="control-label required">Tipo Recibo</label>
			
			<div class="col-sm-12" style="border:0px solid red;">
				<!--<div class="col-md-12 idrampa"> 
					<div class="form-group"> 
						<label for="idrampa" class="control-label" >Rampa:</label>			
						
						<?php echo $rampa; ?>
					</div>
				</div>-->
			<div class = "row">
				<div class="col-md-12 idrampa" style="display:none"> 
					<div class="form-group"> 
						<label for="idrampa" class="required idrampa" >Rampa:</label>
						<?php echo $rampa; ?>
					</div>
				</div>
			</div>
			<div class = "row">
				<div class="col-md-12 idrampa" style="display:none"> 
					<div class="form-group"> 
						<label for="idmecanico" class="required">Mecanico:</label>
						<?php echo $mecanico_vista; ?>
					</div>
				</div>
			</div>
			


 <!-- ESTO ES UN COMENARIO 
			<div class="row">
				<div class="col-sm-12">
					<div class="form-group">
						<label for="idmodalidad">Modalidad de Venta</label>
						<?php echo $modalidad_ingreso; ?>
					</div>	


				</div>
			</div>


             <div class="row">
				<div class="col-sm-12">
					<div class="form-group" style="display:none">
						<label for="idrampa" style="display:none">Rampa</label>
						<?php echo $rampa; ?>
					</div>
				</div>
			</div>


             <div class="row">
				<div class="col-sm-12">
					<div class="form-group" style="display:none">
						<label for="idmecanico" style="display:none">Mecanico</label>
						<?php echo $mecanico_view; ?>
					</div>
				</div>
			</div>

ESTO ES UN COMENTARIO-->  

		</div>
		<div class="col-md-10">
			<div class="row">
				<div class="col-sm-8">
					<div class="form-group">
						<label>Cliente</label>
						<div class="input-group">
							<input type="text" name="cliente" id="cliente_razonsocial" value="<?php echo (!empty($preventa["cliente"])) ? $preventa["cliente"] : ""; ?>" class="form-control" placeholder="Nombre, DNI, razon social o RUC">
							<span class="input-group-btn tooltip-demo">
								<button type="button" id="btn-buscar-cliente" class="btn btn-outline btn-primary" data-toggle="tooltip" title="Buscar clientes">
									<i class="fa fa-search"></i>
								</button>
								<button type="button" id="btn-registrar-cliente" class="btn btn-outline btn-primary" data-toggle="tooltip" title="&iquest;No existe el cliente? Registrar aqui">
									<i class="fa fa-edit"></i>
								</button>
								<button type="button" id="btn-consultar-ruc" class="btn btn-outline btn-default" data-toggle="tooltip" title="Consultar RUC">
									<img src="<?php echo base_url("app/img/sunat.png");?>" style="width:15px;">
								</button>
							</span>
						</div>
					</div>
				</div>
				<div class="col-sm-4 msg-about-cliente"></div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<i class="fa fa-shopping-cart"></i> Detalle del pedido
				</div>
				<div class="panel-body">
					<!--<p>Busque los productos en el recuadro de abajo.
					Presione la tecla 
					<span class="fa-stack">
						<i class="fa fa-square-o fa-stack-2x"></i>
						<i class="fa fa-level-down fa-rotate-90 fa-stack-1x"></i>
					</span> (<i>Enter</i>) para agregar los productos a la tabla.</p>-->
					<div class="row m-b-sm m-t-sm">
						<div class="col-md-9">
							<input type="hidden" id="producto_idproducto">
							<input type="hidden" id="producto_has_serie">
							<input type="hidden" id="producto_idunidad">
							<input type="hidden" id="producto_idalmacen">
							<input type="hidden" id="producto_serie">
							<!--<input type="hidden" id="producto_precio_compra">
							<input type="hidden" id="producto_precio_venta">-->
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
						<!--<div class="col-md-3">
							<button type="button" id="btn-buscar-producto" class="btn btn-white btn-sm"><i class="fa fa-search"></i> Buscar Producto</button>
						</div>-->
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
						<div class="col-sm-2"></div>
						<div class="col-sm-3">
							<div class="form-group">
								<label class="required">Subtotal</label>
								<input type="text" name="subtotal" id="subtotal" value="<?php echo (!empty($preventa["subtotal"])) ? number_format($preventa["subtotal"],$fixed,'.','') : ""; ?>" class="form-control numero input-xs" required="" readonly="" placeholder="0.00">
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<label>IGV</label>
								<input type="text" name="igv" id="igv" value="<?php echo (!empty($preventa["igv"])) ? $preventa["igv"] : ""; ?>" class="form-control numero input-xs" readonly="" placeholder="0.00">
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<label>Descuento</label>
								<input type="text" name="descuento" id="descuento" value="<?php echo (!empty($preventa["descuento"])) ? $preventa["descuento"] : ""; ?>" class="form-control numero input-xs" placeholder="0.00">
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<label class="required">Total</label>
								<input type="text" name="total" id="total" value="<?php echo (!empty($preventa["total"])) ? number_format($preventa["total"],$fixed,'.','') : ""; ?>" class="form-control numero input-xs" required="" readonly="" placeholder="0.00">
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-sm-12" id="info-saldo-cliente"></div>
					</div>
				</div>
			</div>
			
							<div class="row">
					<div class="form-group">
						<div class="col-sm-6 text-left">
							<?php if( ! empty($tabkey)) { ?>
							<button id="btn_cerrar_tab" class="btn btn-sm btn-warning" data-tabkey="<?php echo $tabkey; ?>">Cerrar pesta&ntilde;a</button>
							<?php } else {?>
							<button class="btn btn-sm btn-warning btn_cancel" data-controller="<?php echo $controller; ?>"><sub class='hotkey white'>(Esc)</sub> Cancelar</button>
							<?php } ?>
						</div>
						<div class="col-sm-6 text-right">
							<button id="btn_save_preventa" class="btn btn-sm btn-primary" data-controller="<?php echo $controller; ?>"><sub class='hotkey white'>(F4)</sub> Guardar</button>
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

<div id="modal-precio-tempp" class="modal fade" data-keyboard="false" aria-hidden="true" aria-labelledby="myLargeModalLabel" data-backdrop="static">
	<div class="modal-dialog modal-sm" >
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Agregar precio</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12"><input type="text" id="ptemp" class="form-control"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="modal-cliente" class="modal fade" data-keyboard="false" aria-hidden="true" aria-labelledby="myLargeModalCliente" data-backdrop="static">
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

<div style="display:none;"><?php echo $combo_grupo_igv.'</br>'.$combo_tipo_igv;?></div>
<script>
var default_grupo_igv = <?php echo (!empty($default_igv)) ? "'$default_igv'" : "false"; ?>;
var validar_ruc = <?php echo ($validar_ruc == "S") ? "true" : "false"; ?>;
var mostrar_precio_costo = <?php echo ($mostrar_precio_costo == "S") ? "true" : "false"; ?>;
</script>

<style>
	.numero{text-align:right;}
	.hotkey.white {
		color: #ccc;
	}
	table#dtcliente_view_popup tbody>tr>td{padding: 4px !important;}
	sub.hotkey{bottom: 0;}
	
	.block_content {
		position: absolute;
		top: 80px;
		bottom: 0;
		right: 0;
		left: 0;
	}
</style>