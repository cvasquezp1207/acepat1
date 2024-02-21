<div class="row wrapper border-bottom white-bg page-heading fixed-button-top form-inline">
	<div class="col-sm-11 text-right">
		<button type="button" id="btn-movimiento-stock" class="btn btn-white btn-sm"><i class="fa fa-exchange"></i> Movimiento stock</button>
		<button type="button" id="btn-salida-stock" class="btn btn-white btn-sm"><i class="fa fa-truck"></i> Traslado / Conversi&oacute;n</button>
		<button type="button" id="btn-codigo-barras" class="btn btn-white btn-sm"><i class="fa fa-qrcode"></i> Generar c&oacute;digo barras</button>
	</div>
</div>


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
			<div class="ibox-content" style=";">
				<div class="row">
					<div class="col-sm-12">
						<form id="form_filtro">
							<div class="form-group">
								<label>Buscar por</label>
								<div class="input-group">
									<span class="input-group-btn">
										<select name="filter" id="filter" class="form-control input-xs" style="width:85px;">
											<option value="modelo">Modelo</option>
											<option value="marca">Marca</option>
											<option value="producto">Producto</option>
										</select>
									</span>
									<input type="text" name="search" id="search" placeholder="" class="form-control input-xs">
								</div>
							</div>
							
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
			<div class="ibox-content" style="padding: 0px 20px 20px 20px;">
				<div class="row">
					<div class="col-md-12" style="">
						<div class="clients-list" style="margin-top:0px;">
							<div class="tab-pane panel_active" id="panel_lista_producto" style="max-height:530px;display:block;">
								<div style="overflow-y:auto;overflow-x:hidden;height:332px;border-bottom:1px solid #ebebeb;">
									<?php echo $grid;?>
								</div>
								
								<div style="font-size: 23px; margin-top: -15px;"><center><a class="ver_detalle_producto tooltip-demo" data-toggle="dropdown"><i class="fa fa-angle-double-down" data-toggle="tooltip" data-placement="top" title="Mostrar detalles de producto"></i></a></center></div>
								
								<div style="overflow-y:auto;overflow-x:hidden;height:230px;border:0px solid red;padding:5px 0px 0px;margin-top:-20px;">
									<div class="row panel_detalles" style="display:none;background:#ebebeb;">
									
										<div class="col-md-3" style="padding-right:5px;padding-left:5px;">
											<div class="ibox float-e-margins" style="margin-bottom: 0px;">
												<div class="ibox-title" style="min-height: 20px;padding: 0px 14px 0px;">
													<h5>Producto</h5>
												</div>
												<div class="ibox-content" style="padding: 0px 0px 0px 20px;">
													<div class="row">
														<div class="col-md-12">
															<div style="height:190px;overflow-x:hidden;overflow-y:auto">
																<ul class="list-group clear-list m-t" style="margin-top: 0px;">
																	<li class="list-group-item fist-item">
																		<label>PRODUCTO</label>
																		<div class="prod_descr"></div>
																	</li>
																	
																	<li class="list-group-item">
																		<label>MATERIAL</label>
																		<div class="prod_tam"></div>
																	</li>
																	
																	<li class="list-group-item">
																		<label>COLOR</label>
																		<div class="prod_tam"></div>
																	</li>
																	
																	<li class="list-group-item">
																		<div class="row">
																			<div class="col-md-6">
																				<label>PESO</label>
																				<div class="prod_peso"></div>
																			</div>
																			<div class="col-md-6">
																				<label>TAMAÑO</label>
																				<div class="prod_tam"></div>
																			</div>
																		</div>
																	</li>
																	
																</ul>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										
										<div class="col-md-3" style="padding-right:5px;padding-left:5px;">
											<div class="ibox float-e-margins" style="margin-bottom: 0px;">
												<div class="ibox-title" style="min-height: 20px;padding: 0px 14px 0px;">
													<h5>Lista Precio</h5>
												</div>
												<div class="ibox-content" style="padding: 0px 0px 0px 20px;">
													<div class="row">
														<div class="col-md-12">
															<div style="height:190px;overflow-x:hidden;overflow-y:auto">
																<ul class="list-group clear-list m-t list_precios" style="margin-top: 0px;">
																</ul>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										
										<div class="col-md-3" style="padding-right:5px;padding-left:5px;">
											<div class="ibox float-e-margins" style="margin-bottom: 0px;">
												<div class="ibox-title" style="min-height: 20px;padding: 0px 14px 0px;">
													<h5>Stock / Series</h5>
												</div>
												<div class="ibox-content" style="padding: 0px 0px 0px 20px;">
													<div class="row">
														<div class="col-md-12">
															<div style="height:190px;overflow-x:hidden;overflow-y:auto">
																<ul class="list-group clear-list m-t list_stock" style="margin-top: 0px;margin-bottom: 0px;">
																</ul>
																
																<ul class="list-group clear-list m-t" style="margin-top: 0px;">
																	<li class="list-group-item">
																		<label>Serie(S)</label>
																		<select multiple class="form-control input-xs list_serie"></select>
																	</li>
																</ul>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										
										<div class="col-md-3">
											<div class="ibox float-e-margins" style="margin-bottom: 0px;">
												<div class="ibox-title" style="min-height: 20px;padding: 0px 14px 0px;">
													<h5>Carrusel Imagen</h5>
												</div>
												<div class="ibox-content" style="padding: 0px 0px 0px 20px;">
													<div class="row">
														<div class="col-md-12">
															<div style="height:190px;overflow-x:hidden;overflow-y:auto">
																<ul class="list-group clear-list m-t" style="margin-top: 0px;">			
																	<li class="" >
																		<div class="list_carrusel"></div>
																		<!--
																		<div class="carousel slide carousel1" id="carousel1">
																		</div>
																		-->
																	</li>
																</ul>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										
									</div>
								</div>
							</div>
							
							
							<div class="tab-pane" id="panel_imagen_producto" style="max-height:500px;display:none;">
								<div class="">
									<div class="row" id="content_imagen"></div>
								</div>
							</div>
							
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- MODAL Producto -->
<div class="modal fade" id="modal_producto" aria-labelledby="myLargeModalLabel" data-backdrop="static"  >
	<div class="modal-dialog modal-lg" >
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Detalles de producto</h4>
			</div>
			
			<div class="modal-body" style="">
				<div style="overflow-y:auto;overflow-x:hidden;height:230px;border:0px solid red;padding:5px 0px 0px;background:#ebebeb;">
					<div class="col-md-3" style="padding-right:5px;padding-left:5px;">
						<div class="ibox float-e-margins" style="margin-bottom: 0px;">
							<div class="ibox-title" style="min-height: 20px;padding: 0px 14px 0px;">
								<h5>Producto</h5>
							</div>
							<div class="ibox-content" style="padding: 0px 0px 0px 20px;">
								<div class="row">
									<div class="col-md-12">
										<div style="height:190px;overflow-x:hidden;overflow-y:auto">
											<ul class="list-group clear-list m-t" style="margin-top: 0px;">
												<li class="list-group-item fist-item">
													<label>PRODUCTO</label>
													<div class="prod_descr"></div>
												</li>
																		
												<li class="list-group-item">
													<label>MATERIAL</label>
													<div class="prod_tam"></div>
												</li>
																
												<li class="list-group-item">
													<label>COLOR</label>
													<div class="prod_tam"></div>
												</li>
																		
												<li class="list-group-item">
													<div class="row">
														<div class="col-md-6">
															<label>PESO</label>
															<div class="prod_peso"></div>
														</div>
														<div class="col-md-6">
															<label>TAMAÑO</label>
															<div class="prod_tam"></div>
														</div>
													</div>
												</li>
												
											</ul>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-3" style="padding-right:5px;padding-left:5px;">
						<div class="ibox float-e-margins" style="margin-bottom: 0px;">
							<div class="ibox-title" style="min-height: 20px;padding: 0px 14px 0px;">
								<h5>Lista Precio</h5>
							</div>
							
							<div class="ibox-content" style="padding: 0px 0px 0px 20px;">
								<div class="row">
									<div class="col-md-12">
										<div style="height:190px;overflow-x:hidden;overflow-y:auto">
											<ul class="list-group clear-list m-t list_precios" style="margin-top: 0px;">
											</ul>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-3" style="padding-right:5px;padding-left:5px;">
						<div class="ibox float-e-margins" style="margin-bottom: 0px;">
							<div class="ibox-title" style="min-height: 20px;padding: 0px 14px 0px;">
								<h5>Stock / Series</h5>
							</div>
							
							<div class="ibox-content" style="padding: 0px 0px 0px 20px;">
								<div class="row">
									<div class="col-md-12">
										<div style="height:190px;overflow-x:hidden;overflow-y:auto">
											<ul class="list-group clear-list m-t list_stock" style="margin-top: 0px;margin-bottom: 0px;">
											</ul>
																	
											<ul class="list-group clear-list m-t" style="margin-top: 0px;">
												<li class="list-group-item">
													<label>Serie(S)</label>
													<select multiple class="form-control input-xs list_serie"></select>
												</li>
											</ul>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-3">
						<div class="ibox float-e-margins" style="margin-bottom: 0px;">
							<div class="ibox-title" style="min-height: 20px;padding: 0px 14px 0px;">
								<h5>Carrusel Imagen</h5>
							</div>
							<div class="ibox-content" style="padding: 0px 0px 0px 20px;">
								<div class="row">
									<div class="col-md-12">
										<div style="height:190px;overflow-x:hidden;overflow-y:auto">
											<ul class="list-group clear-list m-t" style="margin-top: 0px;">			
												<li class="" >
													<div class="list_carrusel"></div>
													<!--
													<div class="carousel slide carousel1" id="carousel1">
													</div>
													-->
												</li>
											</ul>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>
<!-- MODAL Producto -->

<form id="modal-cambio-stock" class="modal fade" aria-hidden="true" aria-labelledby="myLargeModalLabel">
	<div class="modal-dialog modal-sm" >
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Ingresar stock</h4>
			</div>
			<div class="modal-body">
				<input type="hidden" name="tipo" id="tipotemp">
				<input type="hidden" name="idproducto" id="productotemp">
				<div class="row">
					<div class="col-md-12">
						<label>Unidad de medida</label>
						<select name="idunidad" id="unidadtemp" class="form-control"></select>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<label>Cantidad</label>
						<input type="text" name="cantidad" id="cantidadtemp" class="form-control">
					</div>
				</div>
			</div>
		</div>
	</div>
</form>

<style>
.badge{font-size:12px;}
#table-letras thead tr th {white-space:nowrap;}
#table-letras tfoot input[id^=total_] {font-weight:bold;}
#table-letras .descuento {width:78%;display:inline-block;}
.widget {padding: 4px 10px;}
.block_pago {position: absolute;background: #000;opacity: 0.2;left: 0;width: 100%;bottom: 0;height: 100%;z-index:100;}
#dtcronograma_pago_view tr td{padding:2px;font-size:12px}
.class_mulpiple{height:50px !important;}
.clients-list table tr td {
    height: auto;
    vertical-align: middle;
    border: none;
	padding:2px;
	font-size:10px
}
.product-name{font-size:12px;}
.product-box{height: 240px;}
.panel_detalles{font-size:12px;}
.prod_serie{font-size:10px;}
#panel_lista_producto .dataTables_wrapper{padding-bottom: 0px;}
.list-group.clear-list .list-group-item {padding: 2px 0;}
#dtview_stock_filter label input.form-control{height: 25px;
    font-size: 12px;
    line-height: 1.5;
    padding: 4px 8px;
    border-radius: 3px;}
#dtview_stock thead tr th{padding:6px;}
</style>