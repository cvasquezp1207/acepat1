<input type="hidden" id="row_smart" value="<?php echo($carterita['cantidad']);?>"></input>
<input type="hidden" id="es_cobrador" value="<?php echo($es_cobrador);?>"></input>
<input type="hidden" id="user_session" value="<?php echo($user_session);?>"></input>
<input type="hidden" class="combito" ></input>

<div class="row">
    <div class="col-lg-12">
        <div class="row" style="">
            <div class="col-md-3" style="padding-right: 5px;padding-left: 5px;">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5>Filtro Hoja Ruta</h5>
						<div class="ibox-tools">
							<a class="dropdown-toggle tooltip-demo" data-toggle="dropdown" href="#" title="">						
								<i class="fa fa-wrench" data-toggle="tooltip" data-placement="bottom" title="Las opciones cargan con los respectivos permisos en el sistema"></i>
							</a>
							<!--
							<a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                <i class="fa fa-wrench"></i>
                            </a>
							-->
                            <ul class="dropdown-menu dropdown-user">
                                <?php echo $botones;?>
                            </ul>
							<a class="collapse-link">
								<i class="fa fa-chevron-up"></i>
							</a>
						</div>
					</div>
					
					<div class="ibox-content">
						<div class="form-group no-margins">
							<form id="parametros">
								<div class="row" style="">
									<div class="col-md-12">
										<div class="form-group">
											<label class="">Cobrador</label>
											<?php echo $cobradores_empleados;?>
										</div>
									</div>	
								</div>

								<div class="row" style="">
									<div class="col-md-6">
										<div class="form-group">
											<label class="">Credito</label>
											<?php echo $estadocredito;?>
										</div>
									</div>

									<div class="col-md-6">
										<div class="form-group">
											<label class="">Nro Credito</label>
											<input name="nro_credito" id="nro_credito" class="form-control input-xs"></input>
										</div>
									</div>
								</div>
								
								<div class="row" style="">
									<div class="col-md-12">
										<div class="form-group">
											<label class="">Tipo Venta</label>
											<?php echo $tipo_venta;?>
										</div>
									</div>
								</div>

								<div class="row" style="">
									<div class="col-md-12">
										<div class="form-group">
											<label class="">Ruta</label>
											<?php //echo $ruta;?>
											<select name="idubigeo"  id="idubigeo" title="Ubigeo asignadas al cobrador con clientes " class="form-control input-xs"></select>
										</div>
									</div>
									</tr>
								</div>
								
								<div class="row" style="">
									<div class="col-md-12">
										<div class="form-group">
											<label class="">Localidad</label>
											<!--<?php echo $zona;?>-->
											<select name="idzona_cartera" title="Zonas asignadas al cobrador con clientes " id="idzona_cartera" class="form-control input-xs"></select>
										</div>
									</div>
								</div>

								<div class="row" style="">
									<div class="col-md-12">
										<div class="form-group">
											<label class="">Cliente</label>
											<input name="cliente" id="cliente" class="form-control input-xs"></input>
										</div>
									</div>	
								</div>

								<div class="row" style="">
									<div class="col-md-12">
										<div class="form-group">
											<label class="">Central Riesgo</label>
											<select class="form-control input-xs" id="central_riesgo" name="central_riesgo">
												<option value="">[TODOS]</option>
												<option value="S">SI</option>
												<option value="N">NO</option>
											</select>
										</div>
									</div>
								</div>

								<div class="row" style="">
									<div class="col-md-12">
										<div class="form-group">
											<button class="btn btn-success  btn_search fa fa-search"> Buscar</button>
											<button class="btn btn-success  btn_print fa fa-print"> Imprimir</button>
											<button class="btn btn-success  fa fa-file-excel-o" id="exportar"> Exportar</button>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>   
			</div>
            
			<div class="col-md-9" style="padding-right: 5px;padding-left: 5px;">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5 style="margin-right:20px;"><span class="widget nav_centralriesgo_tr"></span> Central Riesgo</h5>
						<h5 style="margin-right:20px;"><span class="widget nav_visitado"></span> Visitado en el mes</h5>
						<h5 style="margin-right:20px;"><span class="widget nav_combinado"></span> Central Riesgo/ Visitado</h5>
						<div class="ibox-tools">
							<a class="collapse-link">
								<i class="fa fa-chevron-up"></i>
							</a>
						</div>
					</div>
					
					<div class="ibox-content">
						<div class="form-group no-margins">
							<div class="">	
								<div class="">
									<div class="table-responsive">
										<div class="tabla-creditos-header row-header" style="margin-right: 0px;">
											<table class="table table-striped pintar" id="tabla-creditos" border="1" style="width: 100%;border:1px solid #ddd;margin-bottom:0px;">
												<thead>
													<tr>
														<th style="width: 22%">Cliente</th>
														<th style="width: 24%">Direccion</th>
														<th style="width: 7%">Letras</th>
														<th style="width: 10%">Vencimiento</th>
														<th style="width: 4%">Credito</th>
														<th style="width: 8%">Importe Let</th>
														<th style="width: 8%">Moras</th>
														<th style="width: 8%">Total</th>
														<!--<th style="width: 1%">&nbsp;</th>-->
													</tr>
												</thead>
												<tbody></tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
							
							<div class="tabla-creditos-header row-header" style="margin-right: 0px;">
								<table class="table table-striped pintar" id="tabla-footer" border="1" style="width: 100%;border:1px solid #1ab394;margin-bottom:0px;">
									<thead>
										<tr>
											<th style="width: 100%;text-align:left;"><div class="cant_rows"></div></th>
											<!--<th style="width: 1%">&nbsp;</th>-->
										</tr>
									</thead>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
        </div>
    </div>
</div>

<!-- Configurar la hoja de ruta -->
<div id="modal-config-cobrador" class="modal fade" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Cambiar cartera</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<form id="form-intercambio" >
						<div class="col-sm-12">
							<div class="row">
								<div class="col-sm-12">
									<div class="alert alert-success">
										Todos los clientes que pertenece al vendedor. <a class="alert-link" href="#" id="p_cob">{Antiguo vendedor}</a> pasara a la cartera de cobranzas de <a class="alert-link" href="#" id="n_cob">{Nuevo cobrador}</a>
									</div>
								</div>
							</div>
						</div>
						
						<div class="col-sm-12">
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label class="required">Antiguo Cobrador</label>
										<!--<div class="input-group">-->
											<?php echo $cobradores;?>
											<!--
											<span class="input-group-btn tooltip-demo">
												<button type="button" class="btn btn-outline btn-success btn-xs btn-actualizar-combo" data-toggle="tooltip" title="Recargar combo">
													<i class="fa fa-refresh"></i>
												</button>
											</span>
											-->
										<!--</div>-->
									</div>
								</div>

								<div class="col-sm-6">
									<div class="form-group">
										<label class="required">Nuevo cobrador</label>
										<div class="input-group">
											<select class="form-control input-xs combo_cobrador" id="idcobrador_new" name="idcobrador_new"></select>
											<span class="input-group-btn tooltip-demo">
												<button type="button" class="btn btn-outline btn-success btn-xs btn-actualizar-combo" data-toggle="tooltip" title="Recargar combo">
													<i class="fa fa-refresh"></i>
												</button>
												<!--
												<button type="button" class="btn btn-outline btn-success btn-xs" id="nuevo_cobrador" data-toggle="tooltip" title="Nuevo cobrador">
													<i class="fa fa-file"></i>
												</button>
												-->
											</span>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<div class="col-sm-12">
							<div class="" style="float:right">
								<button class="btn btn-sm btn-default cancel_save" data-dismiss="modal" aria-label="Close">
									<strong>Cerrar</strong>
								</button>
								
								<button class="btn btn-sm btn-primary" id="guardar_intercambio">
									<strong>Guardar</strong>
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="modal-config-orden" class="modal fade" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Ordenar Hoja de Ruta de <span id="ref_cobrador">{Cobrador}</span></h4>
			</div>
			<div class="modal-body">
				<div class="row">			
					<div class="col-sm-12">
						<div class="row">
							<div class="col-sm-12">
								<div class="tabs-container" id="tabs_container">
									<ul class="nav nav-tabs">
										<li class="active"><a data-toggle="tab" href="#tab-1"> Ordenar Localidad</a></li>
										<li class=""><a data-toggle="tab" href="#tab-2"> Ordenar Cliente</a></li>
									</ul>
									<div class="tab-content" id="current">
										<div id="tab-1" class="tab-pane active">
											<form id="form_orden_zona">
												<div class="panel-body">
													<div class="row">
														<div class="col-sm-12">
															<label>Rutas</label>
															<select class="input-xs form-control" name="id_ubigeo" id="id_ubigeo"></select>
															<table class="table table-striped" id="tabla_zonas">
																<thead>
																	<tr>
																		<th width="5%"><label>Item</label></th>
																		<th width="95%"><label>Localidad</label></th>
																	</tr>
																</thead>
																
																<tbody></tbody>
															</table>
														</div>
													</div>
													
													<div class="row">
														<div class="col-sm-12">														
															<button class="btn btn-sm btn-primary" id="guardar_orden_zona">
																<strong>Guardar Orden Localidad</strong>
															</button>
														</div>
													</div>
												</div>
											</form>
										</div>
										<div id="tab-2" class="tab-pane">
											<form id="form_orden_cliente">
												<div class="panel-body">
													<div class="row">
														<div class="col-sm-8">
															<label for="" class="required">Zona</label>
															<select name="idzona_ref" id="idzona_ref" class="form-control input-xs"></select>
														</div>
														
														<div class="col-sm-4">
															<label for="" class="">Por Letra</label>
															<select id='letra' name='letra' style="" class="form-control input-xs">
																<option value="">TODOS</option>
																<?php
																	for($i=65; $i<=90; $i++) {
																		if($i==65)
																			echo "<option value='".chr($i)."' >".chr($i)."</option>";
																		else
																			echo "<option value='".chr($i)."'>".chr($i)."</option>";
																	}
																?>
															</select>
														</div>
													</div>
													
													<div class="row">
														<div class="col-sm-12">
															<div id="">
																<table class="table table-striped" id="tabla_clientes">
																	<thead>
																		<tr>
																			<th width="5%"><label>Item</label></th>
																			<th width="40%"><label>Cliente</label></th>
																			<th width="50%"><label>Direccion</label></th>
																		</tr>
																	</thead>
																	
																	<tbody></tbody>
																</table>
															</div>
														</div>
													</div>
													
													<div class="row">
														<div class="" style="">															
															<button class="btn btn-sm btn-primary" id="guardar_orden_cliente">
																<strong>Guardar Orden cliente</strong>
															</button>
														</div>
													</div>
												</div>
											</form>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
						
					<div class="col-sm-12">
						<div class="" style="float:right">
							<button class="btn btn-sm btn-default cancel_save" data-dismiss="modal" aria-label="Close">
								<strong>Cerrar</strong>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Este modal no lo usan -->
<!--
<div id="modal-form" class="modal fade" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Formulario Incidencia</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<form id="form-data" >
						<input type="hidden" id="idvisita" name="idvisita">
						<input type="hidden"  id='idcliente' name="idcliente">
						<input type="hidden"  id='idcredito' name='idcredito'>
						<input type="hidden"  id='idventa' name='idventa'>
						<input type="hidden"  id='in_central_riesgo' name='in_central_riesgo'>
						
						<div class="col-sm-12">
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label class="required">Cliente</label>
										<input type="text" id="cliente_name" class="form-control input-xs" readonly="readonly">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label class="required">Direcci&oacute;n</label>
										<input type="text" id="direccion_cliente" class="form-control input-xs" readonly="readonly">
									</div>
								</div>
							</div>
						</div>
						
						<div class="col-sm-12">
							<div class="row">
								<div class="col-sm-2">
									<div class="form-group">
										<label class="required">Credito</label>
										<input type="text" id="nrocredito" class="form-control input-xs" readonly="readonly">
									</div>
								</div>

								<div class="col-sm-2">
									<div class="form-group">
										<label class="required">Fecha v.</label>
										<input type="text" id="fecha_venc" class="form-control input-xs" readonly="readonly">
									</div>
								</div>

								<div class="col-sm-2">
									<div class="form-group">
										<label class="required">Letras</label>
										<input type="text" id="letras_v" name="letra_vencidas" class="form-control centro input-xs" readonly="readonly">
									</div>
								</div>

								<div class="col-sm-2">
									<div class="form-group">
										<label class="required">Monto</label>
										<input type="text" id="monto_d" class="form-control numerillo input-xs" readonly="readonly">
									</div>
								</div>

								<div class="col-sm-2">
									<div class="form-group">
										<label class="required">Mora</label>
										<input type="text" id="mora_d" class="form-control numerillo input-xs" readonly="readonly">
									</div>
								</div>

								<div class="col-sm-2">
									<div class="form-group">
										<label class="required">Deuda</label>
										<input type="text" id="total_d" class="form-control numerillo input-xs" readonly="readonly">
									</div>
								</div>
							</div>
						</div>

						<div class="col-sm-12">
							<div class="row">
								<div class="col-sm-3">
									<div class="form-group">
										<label class="">Documento</label>
										<?php //echo $tipodocumento;?>
									</div>
								</div>

								<div class="col-sm-2">
									<div class="form-group">
										<label class="">Serie</label>
										<input type="text" id="serie_doc" name="serie" class="form-control input-xs" >
									</div>
								</div>

								<div class="col-sm-2">
									<div class="form-group">
										<label class="">Numero</label>
										<input type="text" id="numero" name="numero" class="form-control input-xs" >
									</div>
								</div>

								<div class="col-sm-2">
									<div class="form-group">
										<label class="">Monto</label>
										<input type="text" id="monto_cobrado" name="monto_cobrado" class="form-control input-xs numerillo" placeholder="0.00">
									</div>
								</div>
								
								<div class="col-sm-3">
									<div class="form-group">
										<label class="required">Compromiso</label>
										<select id="compromiso" name="compromiso" class="form-control input-xs">
											<option value="N">NO</option>
											<option value="S">SI</option>
										</select>
									</div>
								</div>
							</div>
						</div>

						<div class="col-sm-12 ">
							<div class="row">
								<div class="col-sm-3">
									<div class="form-group">
										<label class="">Pagara el dia</label>
										<input type="text" id="posible_pago" name="posible_pago" class="form-control input-xs" placeholder="d/m/Y">
									</div>
								</div>

								<div class="col-sm-3">
									<div class="form-group">
										<label class="">Proxima Visita</label>
										<input type="text" id="fecha_prox_visita" name="fecha_prox_visita" class="form-control input-xs" placeholder="d/m/Y">
									</div>
								</div>
							</div>
						</div>	

						<div class="col-sm-12">
							<div class="form-group">
								<label class="required">Incidencia</label>
								<textarea id="observacion" name="observacion" class="form-control" ></textarea>
							</div>
						</div>

						<div class="col-sm-12">
							<div class="" style="float:right">
								<button class="btn btn-sm btn-white cancel_save" data-dismiss="modal" aria-label="Close">
									<strong>Cancelar</strong>
								</button>
								<button class="btn btn-sm btn-primary save_data" type="button"><strong>Guardar</strong></button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

-->
<!--
<div id="modal-incidencia" class="modal fade" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Historial Incidencias</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<form id="form-incidencia" >						
						<div class="col-sm-12">
							<div class="row">
								<input type="hidden" id="cli_id"  name="idcliente"/>
								<input type="hidden" id="cred_id" name="idcredito"/>
								<div class="col-sm-5">
									<div class="form-group">
										<label class="required">Fecha Inicio</label>
										<div class="input-group">
											<span id="icono_father" class="input-group-addon">
												<i class="fa fa-calendar-o"></i>
											</span>
											<input type="text" id="fecha_inicio" name="fecha_inicio" value="<?php echo date('d/m/Y');?>" class="form-control" >
										</div>
									</div>
								</div>
								
								<div class="col-sm-5">
									<div class="form-group">
										<label class="required">Fecha Fin</label>
										<div class="input-group">
											<span id="icono_father" class="input-group-addon">
												<i class="fa fa-calendar-o"></i>
											</span>
											<input type="text" id="fecha_fin" name="fecha_fin" class="form-control" >
										</div>
									</div>
								</div>
								
								<div class="col-sm-2">
									<div class="form-group">
										<label class="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
										<button class="btn btn-sm btn-success buscar_incidencia" type="button"><strong>Ver</strong></button>
									</div>
								</div>
							</div>
						</div>
						
						<div class="col-sm-12">
							<div class="row">
								<div class="full-height-scroll">
									<div class="table-responsive">
										<div class="tabla-creditos-header row-header" style="margin-right: 0px;">
											<table class="table table-striped pintar" id="tabla-visitas" border="1" style="width: 100%;border:1px solid #ddd;">
												<thead>
													<tr>
														<th colspan=4 style="width:100%;"><h5 class="here_incidencia">INCIDENCIA DE {CLIENTE} , CREDITO : {NROCREDITO}</h5></th>
													</tr>
													
													<tr>
														<th style="width: 4%">Item</th>
														<th style="width: 85%">Incidencia</th>
														<th style="width: 10%">Fecha</th>
													</tr>
												</thead>
												<tbody></tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="col-sm-12">
							<div class="" style="float:right">
								<button class="btn btn-sm btn-default cancel_save" data-dismiss="modal" aria-label="Close">
									<strong>Cerrar</strong>
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
 -->
<style>
	.widget {padding: 4px 10px;}
	#idtipodocumento{font-size:10px;}
	.seleccionado{background: #2f4050 !important;}
	.visitado, .nav_visitado{background: #f19800 !important;}
	.combinado, .nav_combinado{
		/* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#1e5799+0,2989d8+50,207cca+51,7db9e8+100;Blue+Gloss+Default */
		background: #1e5799; /* Old browsers */
		background: -moz-linear-gradient(top,  #1e5799 0%, #2989d8 50%, #207cca 51%, #7db9e8 100%); /* FF3.6-15 */
		background: -webkit-linear-gradient(top,  #1e5799 0%,#2989d8 50%,#207cca 51%,#7db9e8 100%); /* Chrome10-25,Safari5.1-6 */
		background: linear-gradient(to bottom,  #1e5799 0%,#2989d8 50%,#207cca 51%,#7db9e8 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#1e5799', endColorstr='#7db9e8',GradientType=0 ); /* IE6-9 */
	}
	.seleccionado td{color: white !important;font-weight: bold;}
	.centro{text-align: center;}
	#form-data .form-control{font-size: 11px !important;padding: 4px 4px !important;}

	.numerillo{text-align: right;}
	.nro{font-size: 10.5px;color: black;}
	table.pintar thead tr th{
		background: #1ab394;
		color:white;
		font-size: 10.4px;
	}
  
	.botoncito{
		width: 100%
	}
	
	.cursor{cursor:pointer;font-size:18px}
	
	.tr-bold{font-weight: bold;}
	.tr-title a{color: #b00 !important;font-size:10px;}
	.centralriesgo_tr, .nav_centralriesgo_tr {background-color: #EDCCCC;}
	
	
	select.form-control{
		padding:0px;
		display: inline-block;
		font-size:13px;
	}
	.form-control-req.ui-state-error{
		border: 1px solid #f1a899;
		background: #fddfdf;
		color: #5f3f3f;
	}
	.seleccionado input{color:black !important}
	.table tbody tr td.font_upper{font-size:10.8px;}
	
	.grabado{font-size:10px !important;}	
	.dropdown-menu>li>a:hover {		background-color: #1e90ff !important;	}	
	.btn .caret {margin-right: 3px;float: right;margin-top: 5px;}
	
	.resaltar{
		background: #cfe7fa;
		background: -moz-linear-gradient(top, #cfe7fa 0%, #6393c1 100%);
		background: -webkit-linear-gradient(top, #cfe7fa 0%,#6393c1 100%);
		background: linear-gradient(to bottom, #cfe7fa 0%,#6393c1 100%);
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#cfe7fa', endColorstr='#6393c1',GradientType=0 );
		color: black;
	}

	.table-striped>tbody>tr.tr_ {background-color: white;}
	#tabla-creditos tbody tr.fila-credito:hover{background: #c0c0c0;}
	
	.td_zona{padding:2px !important;}
	.table>tbody>tr>td{padding:2px 0px 4px 8px !important;}
  </style>
  
	<script src="./app/js/jquery-2.1.1.js"></script>
	<script src="./app/js/jquery-ui.js"></script>
	
	<script>
		/*Reordenar HOJA RUTA*/
		$("#tabs_container tbody").sortable({
			// items: "> tr:not(:first)",
			appendTo: "parent",
			helper: "clone"
		}).disableSelection();

		$("#tabs_container ul li a").droppable({
			hoverClass: "drophover",
			tolerance: "pointer",
			drop: function(e, ui) {
				var tabdiv = $(this).attr("href");
				$(tabdiv + " table tr:last").after("<tr>" + ui.draggable.html() + "</tr>");
				ui.draggable.remove();
				// reordenar_item();
			}
		});
		/*Reordenar HOJA RUTA*/
	</script>