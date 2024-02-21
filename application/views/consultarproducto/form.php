<div class="row wrapper border-bottom white-bg page-heading fixed-button-top form-inline">
	<div class="col-sm-11 text-right">
		<button type="button" id="btn-movimiento-stock" class="btn btn-white btn-sm"><i class="fa fa-exchange"></i> Movimiento almacen</button>
		<button type="button" id="btn-traslado" class="btn btn-white btn-sm"><i class="fa fa-truck"></i> Traslados</button>
		<button type="button" id="btn-conversion" class="btn btn-white btn-sm"><i class="fa fa-wrench"></i> Conversi&oacute;n</button>
		<button type="button" id="btn-codigo-barras" class="btn btn-white btn-sm"><i class="fa fa-qrcode"></i> Generar c&oacute;digo barras</button>
		<button type="button" id="ver-pdf" class="btn btn-white btn-sm"><i class="fa fa-file-pdf-o"></i> Exportar PDF</button>
		<button type="button" id="exportar" class="btn btn-white btn-sm"><i class="fa fa-exchange"></i> Exportar EXCEL</button>
	</div>
</div>
<input type="hidden" id="producto_idproducto">
<div class="row">
	<div class="col-sm-3">
		<div class="ibox float-e-margins" style="margin-bottom: 0px;">
			<div class="ibox-title" >
				<h5>Buscar Producto</h5>
				<div class="ibox-tools">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div class="ibox-content">
				<div class="row">
					<div class="col-sm-12">
						<form id="form_filtro">
							<div class="form-group">
								<label>Categoria</label>
								<?php echo $categoria;?>
							</div>
							
							<div class="form-group">
								<label>Marca</label>
								<?php echo $marca;?>
							</div>
							<div class="form-group">
								<label>Modelo</label>
								<?php echo $modelo;?>
							</div>
							
							<div class="form-group">
								<label>Almacen</label>
								<?php echo $almacen;?>
							</div>
							
							<div class="form-group">
								<label>Ver serie | Detallado</label>
								<div class="onoffswitch">
									<input type="checkbox" name="detallado" id="detallado" class="onoffswitch-checkbox" value="1">
									<label class="onoffswitch-label" for="detallado">
										<span class="onoffswitch-inner"></span>
										<span class="onoffswitch-switch"></span>
									</label>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- bloque de botones de consultas -->
	</div>
	
	<div class="col-sm-9">
		<!-- datos del credito -->
		<div class="ibox float-e-margins" style="margin-bottom: 0px;">
			<div class="ibox-title" >
				<h5> Lista de Productos</h5>
				<div class="ibox-tools">
					<a class="dropdown-toggle tooltip-demo change_panel" data-toggle="dropdown" href="#">						
						<i class="fa fa-bars" data-toggle="tooltip" data-placement="left" title="Cambiar Presentacion"></i>
                    </a>
					
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div class="ibox-content">
				<div class="row">
					<div class="col-sm-5 m-b-xs">
						<div data-toggle="buttons" class="btn-group">
							<label class="btn btn-sm btn-white active"> <input type="radio" class="filtro_stock" name="con_stock" value="T"> Todos </label>
							<label class="btn btn-sm btn-white"> <input type="radio" class="filtro_stock" name="con_stock" value="S"> Con stock </label>
							<label class="btn btn-sm btn-white"> <input type="radio" class="filtro_stock" name="con_stock" value="N"> Sin stock </label>
						</div>
					</div>
					<div class="col-sm-7 m-b-xs">
						<div class="input-group">
							<input type="text" placeholder="Buscar" name="query" id="txtQuery" class="input-sm form-control">
							<span class="input-group-btn"><button type="button" class="btn btn-sm btn-primary" id="btnQuery"><i class="fa fa-search"></i></button> </span>
						</div>
					</div>
				</div>
				
				<div class="table-responsive">
					<?php echo $grid;?>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- form modal de movimiento stock (entrada, salida) -->
<form id="modal-movimiento-stock" class="modal fade" aria-hidden="true" aria-labelledby="myLargeModalLabel">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Movimiento almacen</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-6">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label>Tipo movimiento</label>
									<select name="tipo" class="form-control input-xs tipo_movimiento">
										<option value="E">ENTRADA</option>
										<option value="S">SALIDA</option>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label>Tipo operaci&oacute;n</label>
									<?php echo $tipo_movimiento;?>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label>Observaci&oacute;n</label>
							<textarea name="observacion" class="form-control input-xs observacion" style="resize:none;"></textarea>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label>Tipo documento</label>
							<?php echo $tipo_documento;?>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label>Serie doc.</label>
							<input type="text" name="serie" class="form-control input-xs input-sm serie">
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label>Numero doc.</label>
							<input type="text" name="numero" class="form-control input-xs numero">
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-8 col-md-offset-4">
						<div class="form-group">
							<input type="text" id="producto_descripcion" class="form-control input-xs" placeholder="Buscar / agregar productos">
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<table id="tbl-detalle" class="table table-striped detail-table no-header-background">
						<thead>
							<tr>
								<th>Producto</th>
								<th style="width:10%">U.Medida</th>
								<th style="width:10%">Stock</th>
								<th style="width:10%">Cantidad</th>
								<th style="width:10%">Costo</th>
								<th style="width:5%"></th>
								<th style="width:5%"></th>
								<th style="width:0%;display:none;"></th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancelar</button>
				<button type="button" id="btn-save-movimiento-stock" class="btn btn-primary btn-sm">Guardar</button>
			</div>
		</div>
	</div>
</form>

<!-- form modal traslado productos almacen -->
<form id="modal-traslado" class="modal fade" aria-hidden="true" aria-labelledby="myLargeModalLabel">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Traslados de item</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-2">
						<div class="form-group">
							<label>Documento</label>
							<?php echo $tipo_documento;?>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label>Serie doc.</label>
							<input type="text" name="serie" class="form-control input-xs input-sm serie">
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label>Numero doc.</label>
							<input type="text" name="numero" class="form-control input-xs numero">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label>Observaci&oacute;n</label>
							<textarea name="observacion" class="form-control input-xs observacion" style="resize:none;"></textarea>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-6">
						<div class="panel panel-primary panel-form panel-salida">
							<div class="panel-heading">Salida</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-md-12">
										<div class="form-group form-inline">
											<label>Almacen</label>
											<?php echo $almacen_temp;?>
										</div>
									</div>
								</div>
								<div class="table-responsive">
									<table id="tbl-detalle-salida" class="table table-striped detail-table no-header-background">
										<thead>
											<tr>
												<th>Producto</th>
												<th style="width:10%">U.M.</th>
												<!--<th style="width:10%">Stock</th>-->
												<th style="width:10%">Cant.</th>
												<th style="width:5%"></th>
												<th style="width:0%;display:none;"></th>
											</tr>
										</thead>
										<tbody></tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="panel panel-primary panel-form panel-entrada">
							<div class="panel-heading">Entrada</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-md-12">
										<div class="form-group form-inline">
											<label>Almacen</label>
											<?php echo $almacen_temp;?>
										</div>
									</div>
								</div>
								<div class="table-responsive">
									<table id="tbl-detalle-entrada" class="table table-striped detail-table no-header-background">
										<thead>
											<tr>
												<th>Producto</th>
												<th style="width:10%">U.M.</th>
												<!--<th style="width:10%">Stock</th>-->
												<th style="width:10%">Cant.</th>
												<th style="width:5%"></th>
											</tr>
										</thead>
										<tbody></tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<div class="row m-t-sm">
					<div class="col-md-12">
						<div class="form-group form-inline">
							<input type="text" class="form-control input-xs temp_producto" placeholder="Buscar / agregar productos">
							<select class="form-control input-xs temp_unidad"></select>
							<input type="text" class="form-control input-xs temp_stock" readonly>
							<input type="text" class="form-control input-xs temp_cantidad">
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancelar</button>
				<button type="button" id="btn-save-traslado" class="btn btn-primary btn-sm">Guardar</button>
			</div>
		</div>
	</div>
</form>

<!-- form modal conversion productos almacen -->
<form id="modal-conversion" class="modal fade" aria-hidden="true" aria-labelledby="myLargeModalLabel">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Conversi&oacute;n de item</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-4">
						<div class="panel panel-info panel-form">
							<div class="panel-heading">Opciones</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label>Tipo operaci&oacute;n</label>
											<?php echo $tipo_movimiento;?>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<div class="form-group div-salida">
											<label>Almacen salida</label>
											<?php echo $almacen_temp;?>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<div class="form-group div-entrada">
											<label>Almacen entrada</label>
											<?php echo $almacen_temp;?>
										</div>
									</div>
								</div>
								<!--<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label>Guardar equivalencia</label>
											<div class="onoffswitch">
												<input type="checkbox" name="guardar_equivalencia" id="guardar_equivalencia" class="onoffswitch-checkbox" value="1">
												<label class="onoffswitch-label" for="guardar_equivalencia">
													<span class="onoffswitch-inner"></span>
													<span class="onoffswitch-switch"></span>
												</label>
											</div>
										</div>
									</div>
								</div>-->
							</div>
						</div>
					</div>
					
					<div class="col-md-4">
						<div class="panel panel-success panel-form panel-conversion">
							<div class="panel-heading">Conversi&oacute;n</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label>Producto</label>
											<input type="hidden" name="conversion_idproducto" class="conversion_idproducto">
											<textarea name="conversion_producto" class="form-control input-xs conversion_producto" style="resize:none"></textarea>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label>De</label>
											<select name="producto_idunidad" class="form-control input-xs producto_idunidad"></select>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Convertir a</label>
											<?php echo $unidadmedida;?>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label>Cantidad <span class="producto_abreviatura"></span></label>
											<input type="text" name="conversion_cantidad" class="form-control input-xs conversion_cantidad">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Equivalencia</label>
											<input type="text" name="conversion_equivalencia" class="form-control input-xs conversion_equivalencia">
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="col-md-4">
						<div class="panel panel-primary panel-form">
							<div class="panel-heading">Resultados</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<div class="pull-right">
												<span class="btn-group tooltip-demo">
													<button class="btn btn-white btn-xs btn-edit-resultado" data-toggle="tooltip" title="Modificar texto"><i class="fa fa-pencil"></i></button>
													<button class="btn btn-white btn-xs btn-search-producto" data-toggle="tooltip" title="Relacionar con un producto existente"><i class="fa fa-search"></i></button>
													<button class="btn btn-white btn-xs btn-del-producto" data-toggle="tooltip" title="Eliminar relacion"><i class="fa fa-recycle"></i></button>
												</span>
											</div>
											<label>Producto</label>
											<input type="hidden" name="resultado_tipo" class="resultado_tipo">
											<input type="hidden" name="resultado_idproducto" class="resultado_idproducto">
											<input type="hidden" name="resultado_idunidad" class="resultado_idunidad">
											<textarea name="resultado_producto" class="form-control input-xs resultado_producto" style="resize:none" readonly></textarea>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label>Unidad medida</label>
											<input type="text" name="resultado_unidad" class="form-control input-xs resultado_unidad" readonly>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label>Cantidad</label>
											<input type="text" name="resultado_cantidad" class="form-control input-xs resultado_cantidad" readonly>
										</div>
									</div>
								</div>
								<!--<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label>P.Costo</label>
											<input type="text" name="resultado_costo" class="form-control input-xs resultado_costo">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>P.Venta</label>
											<input type="text" name="resultado_venta" class="form-control input-xs resultado_venta">
										</div>
									</div>
								</div>-->
							</div>
						</div>
					</div>
				</div>
				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cerrar</button>
				<button id="btn-save-conversion" class="btn btn-primary btn-sm">Guardar</button>
			</div>
		</div>
	</div>
</form>

<!-- form modal de codigo barras -->
<form id="modal-codigo-barras" class="modal fade" aria-hidden="true" aria-labelledby="myLargeModalLabel">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Generar c&oacute;digos de barra</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label>Producto</label>
							<input type="hidden" name="idproducto" class="idproducto">
							<input type="text" name="producto" class="form-control input-xs producto">
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label>C&oacute;digo de barra</label>
							<input type="text" name="codigo_barras" class="form-control input-xs codigo_barras">
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label>Precio sugerido</label>
							<input type="text" name="precio_sugerido" class="form-control input-xs precio_sugerido">
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label>Cantidad de item</label>
							<input type="text" name="cantidad" class="form-control input-xs cantidad">
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancelar</button>
				<button type="button" id="btn-generar-codigo-barras" class="btn btn-primary btn-sm">Generar</button>
			</div>
		</div>
	</div>
</form>

<style>
#dtview_stock_filter {display:none;}
table#dtview_stock tbody>tr>td{padding: 4px !important;font-size: 12px;}
.panel-form {margin: 0;}
.panel-form>.panel-heading {padding: 5px 10px;}
.panel-form>.panel-body {padding: 10px;}
.form-group {margin-bottom:10px;}
.form-inline .temp_producto {width:30%}
.form-inline .temp_unidad {width:10%}
.form-inline .temp_stock {width:10%}
.form-inline .temp_cantidad {width:10%}
.detail-table {font-size:12px;}
</style>