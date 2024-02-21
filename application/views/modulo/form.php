<div class="wrapper wrapper-content animated fadeInRight">
	<div class="row">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<div class="ibox-title">
					<h5>Registrar Modulo<small></small></h5>
				</div>
				<div class="ibox-content">
					<form id="form-data<?php echo $prefix; ?>" class="app-form" >
						<input type="hidden" name="idmodulo" id="idmodulo<?php echo $prefix; ?>" value="<?php echo (!empty($idmodulo)) ? $idmodulo : ""; ?>" />
						<input type="hidden" id="id_padre<?php echo $prefix; ?>" value="<?php echo (!empty($idpadre)) ? $idpadre : ""; ?>" />
						<div class="">
							<div class="row">
								<div class="col-sm-4">
									<div class="form-group row">
										<div class="col-md-12">
											<label class="required">Sistema</label>
											<div class="input-group input-group-sm">
												<?php echo $sistema;?>
												<span class="input-group-btn tooltip-demo">
													<button type="button" id="btn_sistema<?php echo $prefix;?>" class="btn btn-outline btn-primary" data-toggle="tooltip" title="Nuevo Sistema">
														<i class="fa fa-file"></i>
													</button>
												</span>
											</div>
										</div>
									</div>

									<div class="form-group row">
										<div class="col-md-12">
											<label class="required">Modulo Hijo</label>
											<input type="text" name="descripcion" id="descripcion<?php echo $prefix; ?>" class="form-control input-sm" value="<?php echo (!empty($descripcion)) ? $descripcion : ""; ?>" />
										</div>
									</div>

									<div class="form-group row">
										<div class="col-md-7">
											<label class="">Abreviatura</label>
											<input type="text" name="abreviatura" id="abreviatura<?php echo $prefix; ?>" class="form-control" value="<?php echo (!empty($abreviatura)) ? $abreviatura : ""; ?>">
										</div>
										<div class="col-md-5">
											<label class="required">Orden</label>
											<input type="text" name="orden" id="orden<?php echo $prefix; ?>" class="form-control numero" value="<?php echo (!empty($orden)) ? $orden : ""; ?>">
										</div>
									</div>
									
									<div class="form-group row">
										<div class="col-md-7">
											<label class="">Inactivo?</label>
											<div class="onoffswitch">
												<input type="checkbox" id="estado<?php echo $prefix; ?>" class="onoffswitch-checkbox" <?php echo (isset($estado) && $estado == 'I') ? "checked" : ""; ?> />
												<label class="onoffswitch-label" for="estado<?php echo $prefix; ?>">
													<span class="onoffswitch-inner"></span>
													<span class="onoffswitch-switch"></span>
												</label>
											</div>
										</div>
									</div>
								</div>

								<div class="col-sm-4">
									<div class="form-group row">
										<div class="col-md-12">
											<label class="">Modulo Padre</label>						
											<div class="input-group input-group-sm">
												<select name="idpadre" id="idpadre" class="form-control input-sm"></select>
												<span class="input-group-btn tooltip-demo">
													<button type="button" id="btn_padre<?php echo $prefix;?>" class="btn btn-outline btn-primary" data-toggle="tooltip" title="Refrescar Modulos Padre">
														<i class="fa fa-refresh"></i>
													</button>
												</span>
											</div>
										</div>
									</div>

									<div class="form-group row">
										<div class="col-md-12">
											<label class="">URL</label>
											<input type="text" name="url" id="url<?php echo $prefix; ?>" class="form-control" value="<?php echo (!empty($url)) ? $url : ""; ?>" />
										</div>
									</div>

									<div class="form-group row">
										<div class="col-md-12">
											<label class="">Icono</label>			
											<div class="input-group input-group-sm">
												<span id="icono_preview<?php echo $prefix; ?>" class="input-group-addon">
												<i class="fa <?php echo (!empty($icono)) ? $icono : ""; ?>"></i>
												
												</span>
												<input type="text" name="icono" id="icono<?php echo $prefix; ?>" class="form-control " required="" value="<?php echo (!empty($icono)) ? $icono : ""; ?>" />
												<div class="input-group-btn">
													<button data-toggle="dropdown" class="btn btn-white dropdown-toggle" style="margin-bottom:0 !important;" type="button">Buscar <span class="caret"></span></button>
													<ul class="dropdown-menu pull-right" style="max-height: 200px; overflow-x:auto;">
														<li><a href="#"></a></li>
														<?php
															if(!empty($icons)) {
																foreach($icons as $icon) {
																	echo '<li><a href="#" class="select_icon" id="select_icon'.$prefix.'" data-icon="'.$icon.'"><i class="fa fa-'.$icon.'"></i> '.$icon.'</a></li>';
																}
															}
														?>
													</ul>
												</div>
											</div>
										</div>
									</div>
								</div>

								<div class="col-sm-4">
									<div class="form-group row">
										<div class="col-md-12">
											<label class="required">Boton</label>
											<div class="input-group input-group-sm">
												<button data-toggle="dropdown" class="btn btn-white dropdown-toggle btn-sm">Seleccione Boton...<span class="caret"></span></button>
												<ul class="dropdown-menu update-dropdown-menu" id="idboton_sel"></ul>
												<span class="input-group-btn tooltip-demo" style="display:inline-block;">
													<button type="button" id="btn-registrar-boton" class="btn btn-outline btn-primary btn-sm" data-toggle="tooltip" title="Registrar nuevo boton">
														<i class="fa fa-file"></i>
													</button>
												</span>
											</div>
										</div>
									</div>
									
									<div class="form-group row">
										<div class="col-md-12">
											<table width="100%">
												<tbody id="detalle_boton"></tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
							
							<div class="row">
								<div class="form-group">
									<div class="col-lg-offset-4 col-lg-8">
										<button id="btn_cancel" class="btn btn-sm btn-white btn_cancel" >Cancelar</button>
										<button type="submit" id="btn_save" class="btn btn-sm btn-primary" >Guardar</button>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="modal-boton" class="modal fade" data-keyboard="false" aria-hidden="true" aria-labelledby="myLargeModalCliente" data-backdrop="static">
	<div class="modal-dialog" style="margin-top: 10px;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Registrar Boton</h4>
			</div>
			<div class="modal-body" style="padding: 0px 30px 0px 29px;">
				<div class="row">
					<?php echo $form_boton; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="modal-sistema" class="modal fade" data-keyboard="false" aria-hidden="true" aria-labelledby="myLargeModalCliente" data-backdrop="static">
	<div class="modal-dialog" style="margin-top: 10px;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Registrar Sistema</h4>
			</div>
			<div class="modal-body" style="padding: 0px 30px 0px 29px;">
				<div class="row">
					<?php echo $form_sistema; ?>
				</div>
			</div>
		</div>
	</div>
</div>


<style>
	.sortable{		
		list-style-type: none;
		padding: 5px 0 0 0;
		margin-bottom:20px;
		height:auto;
		border: 1px solid #ccc;
		background:white;
	}
	
	.sortable li{
		margin: 0 5px 5px 5px;
		padding: 5px;
		font-size: 12px;
		font-weight:bold;
	}
	
	.ui-state-default{
		border: 1px solid #c5c5c5;
		background: #f6f6f6;
		font-weight: normal;
		color: #454545;
	}
	
	.ui-state-highlight{
		border: 1px solid #dad55e;
		background: #fffa90;
		color: #777620;
		height: 2.2em;
	}
</style>