<script>
	var prefix_cliente = <?php echo (!empty($prefix))? "'$prefix'" : "''"; ?>;
	var long_dni = <?php echo (!empty($long_dni))? "'$long_dni'" : "'0'"; ?>;
	var long_ruc = <?php echo (!empty($long_ruc))? "'$long_ruc'" : "'0'"; ?>;
</script>
<form id="form_<?php echo $controller; ?>" class="form-horizontal app-form form-uppercase form_clientito">
	<input type="hidden" name="idcliente" 		id="<?php echo $prefix; ?>idcliente"   value="<?php  echo (!empty($idcliente)) ? $idcliente : ''; ?>"/>
	<input type="file" 	 name="file" 			id="<?php echo $prefix; ?>file" 		 style="display: none;" onchange='leerarchivobin(this)' />
	<input type="hidden" name="foto"			id="<?php echo $prefix; ?>foto"  		 value="<?php echo (!empty($foto)) ? $foto : "anonimo.jpg"; ?>"/>
	<input type="hidden" name="logo_new"		id="<?php echo $prefix; ?>logo_new"  	 value="<?php echo (!empty($foto)) ? $foto : "anonimo.jpg"; ?>"/>
	
	<input type="hidden" name="linea_credito" 	id="<?php echo $prefix; ?>linea_credito"   value="<?php  echo (!empty($linea_credito)) ? $linea_credito : 'N'; ?>"/>
	<input type="hidden" name="limite_credito" 	id="<?php echo $prefix; ?>limite_credito"   value="<?php  echo (!empty($limite_credito)) ? $limite_credito : '0'; ?>"/>
	
	<div class="tabs-container">
		<ul class="nav nav-tabs">
			<li class="active"><a data-toggle="tab" href="#tab<?php echo $prefix; ?>-1"> <i class="fa fa-folder-open-o" aria-hidden="true"></i>Datos Generales</a></li>
			<li class=""><a data-toggle="tab" href="#tab<?php echo $prefix; ?>-3"><i class="fa fa-building-o" aria-hidden="true"></i>Datos Particulares</a></li>
		</ul>
		
		<div class="tab-content">
			<div id="tab<?php echo $prefix; ?>-1" class="tab-pane active">
				<div class="panel-body" style="padding:20px 20px 0px 20px;">
					<div class="row" style="">
						<div class="col-md-6">
							<div class="form-group" style="margin-right:0px;margin-left:0px;margin-bottom:0px">
								<div class="row" style="">
									<div class="col-md-8">										
										<div class="form-group" style="margin-right:0px;margin-left:0px;">
											<div class="row">
												<div class="col-md-6">
													<label class="label_persona">Cod Anterior</label>
													<input type="text" name="nombres" class="form-control input-xs" id="<?php echo $prefix; ?>codigo_anterior" placeholder="" value="<?php  echo (!empty($codigo_anterior)) ? $codigo_anterior : "" ?>" readonly="readonly"></input>
												</div>
												
												<div class="col-md-6">
													<label class="required">Tipo Cliente</label>
													<select class="form-control animation_select req input-xs" name="tipo" id="<?php echo $prefix; ?>tipo" data-animation="rollIn">
															<?php
																$array_tcliente = array("N"=>'P. NATURAL',"J"=>'P. JURIDICA');
																foreach($array_tcliente as $k=>$v){
																	if( !empty($tipo) && $k==$tipo ){
																		echo "<option value='{$k}' selected>".$v."</option>";
																	}else{
																		echo "<option value='{$k}' >".$v."</option>";
																	}
																}
															?>
													</select>
												</div>
											</div>
										</div>
										
										<div class="form-group" style="margin-right:0px;margin-left:0px;">
											<label class="required label_persona">Nombres</label>
											<input type="text" name="nombres" class="form-control nombres here_req req input-xs" id="<?php echo $prefix; ?>nombres" placeholder="Nombres/ Razon social" value="<?php  echo (!empty($nombres)) ? $nombres : "" ?>" ></input>
											<input type="text" name="apellidos" class="form-control apellidos here_req input-xs" id="<?php echo $prefix; ?>apellidos" placeholder="Apellidos" value="<?php  echo (!empty($apellidos)) ? $apellidos : "" ?>" ></input>
										</div>
										
										<div class="form-group" style="margin-right:0px;margin-left:0px;">
											<div class="row">
												<div class="col-md-4 ">
													<div class="">
														<label class="label_dni">DNI</label>
														<input name="dni" class="form-control dni input-xs" title="" data-toggle="tooltip" data-placement="top" id="<?php echo $prefix; ?>dni" maxlength=<?php echo $long_dni;?> value="<?php  echo (!empty($dni)) ? $dni : "" ?>" style="font-size:12px;padding:4px 4px;"></input>
													</div>
												</div>
												
												<div class="col-md-8">
													<label class="">E-Mail</label>
													<div class="input-group email">
														<span class="input-group-addon" style="font-size:8px;">@</span>
														<input type="text" class="form-control cliente_email input-xs" name="cliente_email" id="<?php echo $prefix; ?>cliente_email" value="<?php  echo (!empty($cliente_email)) ? $cliente_email : "" ?>">
													</div>
												</div>
											</div>
										</div>
									</div>
									
									<div class="col-md-4">
										<div class="form-group" style="margin-right:0px;margin-left:0px;">
											<label class="label_ruc">RUC</label>
											<input type="text" class="form-control ruc input-xs" title="" data-toggle="tooltip" data-placement="top" name="ruc" id="<?php echo $prefix; ?>ruc" maxlength=<?php echo $long_ruc;?> value="<?php  echo (!empty($ruc)) ? $ruc : "" ?>"></input>
										</div>
										
										<div class="form-group" style="margin-right:0px;margin-left:0px;">
											<div class="">
												<div id="load_photo" class="app-img-temp img-thumbnail load_photo">
													<?php
														if(empty($foto))
															$foto = 'anonimo.jpg';
														
														// $n_logo = 'http://localhost/sistema/app/img/cliente/'.$foto;
														$n_logo = base_url('app/img/cliente/'.$foto);
														$file_headers = @get_headers($n_logo);
													?>
													<img id="<?php echo $prefix; ?>photoN" src="<?php echo $n_logo;?>" class="img-responsive img-thumbnail photo" style="background:#f3f3f4;"/>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<div class="col-md-6">
							<div class="form-group" style="margin-right:0px;margin-left:0px;margin-bottom:0px">
								<div class="row" style="">
									<div class="col-md-8">
										<div class="form-group" style="margin-right:0px;margin-left:0px;">
											<label class="">Referencia</label>
											<textarea name="observacion" id="<?php echo $prefix; ?>observacion" value="<?php  echo (!empty($observacion)) ? $observacion : "" ?>" class="form-control input-xs"><?php  echo (!empty($observacion)) ? $observacion : "" ?></textarea>
										</div>
									</div>

									<div class="col-md-4">
										<div class="form-group" style="margin-right:0px;margin-left:0px;">
											<label>Es Especial?</label>
											<div class="onoffswitch">
												<input type="checkbox" name="especial" id="<?php echo $prefix; ?>especial" class="onoffswitch-checkbox" value="1" <?php echo (isset($especial) && $especial == 'S') ? "checked" : ""; ?> >
												<label class="onoffswitch-label" for="<?php echo $prefix; ?>especial">
													<span class="onoffswitch-inner"></span>
													<span class="onoffswitch-switch"></span>
												</label>
											</div>
										</div>
									</div>
								</div>
							</div>
							
									<!--
							<div class="form-group" style="margin-right:0px;margin-left:0px;">							
								<div class="row" style="">

									<div class="col-md-4">
										<div class="">
											<label>Linea Credito</label>
											<div class="onoffswitch">
												<input type="checkbox" name="linea_credito" id="<?php echo $prefix; ?>linea_credito" class="onoffswitch-checkbox" value="1" <?php echo (isset($linea_credito) && $linea_credito == 'S') ? "checked" : ""; ?> >
												<label class="onoffswitch-label" for="<?php echo $prefix; ?>linea_credito">
													<span class="onoffswitch-inner"></span>
													<span class="onoffswitch-switch"></span>
												</label>
											</div>
										</div>
									</div>
									
									<div class="col-md-4">
										<div class="">
											<label class="">Limite Credito</label>
											<input type="text" name="limite_credito" id="<?php echo $prefix; ?>limite_credito" readonly="readonly" value="<?php  echo (!empty($limite_credito)) ? $limite_credito : "0.00" ?>" class="form-control numerillo limite_credito input-xs">
										</div>
									</div>
								</div>
							</div>
									-->
							
							<div class="form-group" style="margin-right:0px;margin-left:0px;">
								<div class="row">
									<div class="col-md-8">
										<label class="required">Direccion</label>
										<div class="list_direcciones">
										<?php
											// $html_dir ='';
											// $checked = 'checked';
											// if(!empty($direcciones)){
												// foreach($direcciones as $key=>$val){
													// $checked = '';
													// if ($val['dir_principal']=='S') {
														// $checked = 'checked';
													// }
													// $html_dir.='<div class="input-group" style="margin-top:4px;">';
													// $html_dir.='	<span class="input-group-addon tooltip-demo cursor" style="padding: 0px 2px 1px 0px;margin-left: 5px;">';
													// $html_dir.='		<div class="radio" style="padding-left: 30px;" data-toggle="tooltip" title="Seleccione Direccion Principal">';
													// $html_dir.='				<input type="radio" name="radio_dir"  class="dir_principal " '.$checked.' >';
													// $html_dir.='				<label></label>';
													// $html_dir.='		</div>';
													// $html_dir.='		<input type="hidden" class="dir_principal_val " name="dir_principal[]" value="'.$val['dir_principal'].'" >';
													// $html_dir.='	</span>';
													// $html_dir.='	<input type="text" name="direccion[]" placeholder="Direccion..." value="'.$val['direccion'].'" class="form-control direccion here_req req" style="font-size:12px;padding:4px 4px;" required="">';
													
													// if($key==0){
														// $html_dir.='<span class="input-group-addon cursor tooltip-demo" id="addDireccion" style="border:1px solid #1c84c6;color: #1c84c6;border-radius: 0px 3px 3px 0px;">';
														// $html_dir.='	<div data-toggle="tooltip" id="" title="Añadir direccion" class="">';
														// $html_dir.='		<i class="fa fa-plus-square"></i>';
														// $html_dir.='	</div>';
													// }else{
														// $html_dir.='	<span class="input-group-addon cursor tooltip-demo delete_direccion" style="border:1px solid #1c84c6;color: #1c84c6;border-radius: 0px 3px 3px 0px;">';
														// $html_dir.='	<div data-toggle="tooltip" class="" title="Borrar direccion">';
														// $html_dir.='		<i class="fa fa-trash"></i>';
														// $html_dir.='	</div>';
													// }
													// $html_dir.='	</span>';
													// $html_dir.='</div>';
												// }
											// }else{
												// $html_dir.='<div class="input-group" >';
												// $html_dir.='	<span class="input-group-addon tooltip-demo cursor" style="padding: 0px 2px 1px 0px;margin-left: 5px;">';
												// $html_dir.='		<div class="radio" style="padding-left: 30px;" data-toggle="tooltip" title="Seleccione Direccion Principal">';
												// $html_dir.='				<input type="radio" name="radio_dir"  class="dir_principal" '.$checked.' >';
												// $html_dir.='				<label></label>';
												// $html_dir.='		</div>';
												// $html_dir.='		<input type="hidden" class="dir_principal_val" name="dir_principal[]" value="S" >';
												// $html_dir.='	</span>';

												// $html_dir.='	<input type="text" name="direccion[]" placeholder="Direccion..." class="form-control direccion here_req req " style="font-size:12px;padding:4px 4px;" required="">';
												// $html_dir.='	<span class="input-group-addon tooltip-demo cursor" id="addDireccion" style="border:1px solid #1c84c6;color: #1c84c6;border-radius: 0px 3px 3px 0px;">';
												// $html_dir.='		<div data-toggle="tooltip" title="Añadir direccion" class="">';
												// $html_dir.='			<i class="fa fa-plus-square" style=""></i>';
												// $html_dir.='		</div>';
												// $html_dir.='	</span>';
												// $html_dir.='</div>';
											// }
											// echo $html_dir;
											?>
										</div>
									</div>
									
									<div class="col-md-4">
										<label class="">Telefono</label>
										<div class="list_telefonos">
										<?php
											// $html_telf ='';
											// if(!empty($telefonos)){
												// foreach($telefonos as $key=>$val){
													// $html_telf.='<div class="input-group">';
													// $html_telf.='	<input type="text" name="telefono[]" value="'.$val['telefono'].'" class="form-control telefono" style="font-size:12px;padding:4px 4px;">';
													// $html_telf.='	<span class="input-group-btn tooltip-demo">';
													// if($key==0){
														// $html_telf.='		<button type="button" id="addTelefono" style="" class="btn btn-outline btn-success" data-toggle="tooltip" title="Añadir Telefono">';
														// $html_telf.='			<i class="fa fa-plus-square"></i>';
														// $html_telf.='		</button>';																
													// }else{
														// $html_telf.='		<button type="button" style="" class="btn btn-outline btn-success delete_telefono" title="Borrar Telefono">';
														// $html_telf.='			<i class="fa fa-trash"></i>';
														// $html_telf.='		</button>';	
													// }
													// $html_telf.='	</span>';
													// $html_telf.='</div>';
												// }
											// }else{
												// $html_telf.='<div class="input-group">';
												// $html_telf.='	<input type="text" name="telefono[]" class="form-control telefono" style="font-size:12px;padding:4px 4px;">';
												// $html_telf.='	<span class="input-group-btn tooltip-demo">';
												// $html_telf.='		<button type="button" id="addTelefono" style="" class="btn btn-outline btn-success" data-toggle="tooltip" title="Añadir Telefono">';
												// $html_telf.='			<i class="fa fa-plus-square"></i>';
												// $html_telf.='		</button>';
												// $html_telf.='	</span>';
												// $html_telf.='</div>';
											// }
											// echo $html_telf;
										?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div id="tab<?php echo $prefix; ?>-3" class="tab-pane ">
				<div class="panel-body" style="padding:20px 20px 0px 20px">
					<div class="row">
						<div class="col-md-6">
							<fieldset class="">
								<legend style="font-size:15px;"><label>Datos Cliente</label></legend>
								<div class="row" style="">
									<div class="col-md-12">
										<label class="">Zona</label>
										<div class="input-group">
											<?php echo $zona_combo;?>
											<span class="input-group-btn tooltip-demo">
												<button type="button" id="btn-registrar-zona" class="btn btn-outline btn-success btn-xs" data-toggle="tooltip" title="&iquest;No existe la zona? Registrar aqui">
													<i class="fa fa-edit"></i>
												</button>
											</span>
										</div>
										<br>
									</div>
								</div>
								
								<div class="row info_natural" style="">
									<div class="col-md-4 ">
										<label class="">Estado Civil</label>
										<?php echo $estado_civil;?>
									</div>
									
									<div class="col-md-3">
										<label class="required">Sexo</label>
										<select class="form-control here_req req input-xs" name="sexo" id="<?php echo $prefix; ?>sexo">
											<option value="">Seleccione...</option> 
											<?php
												$array_sexo = array("M"=>'MASCULINO',"F"=>'FEMENINO');
												foreach($array_sexo as $k=>$v){
													if( !empty($sexo) && $k==$sexo ){
														echo "<option value='{$k}' selected>".$v."</option>";
													}else{
														echo "<option value='{$k}' >".$v."</option>";
													}
												}
											?>
										</select>
									</div>

									<div class="col-md-5 ">
										<label class="required">Fecha Nac.</label>
										<div class="input-group date input-group-xs">
											<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
											<input name="fecha_nac" id="<?php echo $prefix; ?>fecha_nac" type="text" class="form-control fecha_nac here_req req input-xs" value="<?php  echo (!empty($fecha_nac)) ? dateFormat($fecha_nac,"d/m/Y") : "" ?>" />
										</div>
									</div>
								</div>
							</fieldset>
						</div>
						
						<div class="col-md-6">
							<fieldset class="">
								<legend style="font-size:15px;"><label class="label_secundario">Datos Adicionales</label></legend>
								<div class="form-group" style="margin-right:0px;margin-left:0px;">
									<div class="info_natural" style="display:block;">
										<div class="row" style="">
											<div class="col-md-6 ">
												<label class="">Situacion Laboral</label>
												<?php echo $situacion; ?>
												<br>
											</div>
											
											<div class="col-md-6 ">
												<label class="">Centro de Trabajo</label>
												<input type="text" name="centro_laboral" id="<?php echo $prefix; ?>centro_laboral" value="<?php  echo (!empty($centro_laboral)) ? $centro_laboral : "" ?>" class="form-control centro_laboral input-xs">
												<br>
											</div>
										</div>
										
										<div class="row" style="">
											<div class="col-md-6">
												<label class="">Ocupacion / Cargo</label>
												<div class="input-group">
													<?php echo $ocupacion_cli;?>
													<span class="input-group-btn tooltip-demo">
														<button type="button" id="btn-registrar-ocupacion" class="btn btn-outline btn-success btn-xs" data-toggle="tooltip" title="&iquest;No existe el Cargo/Ocupacion? Registrar aqui">
															<i class="fa fa-edit"></i>
														</button>
													</span>
												</div>
												<br>
											</div>

											<div class="col-md-6">
												<div class="">
													<label class="">Direccion Trabajo</label>
													<input type="text" name="direccion_trabajo" id="<?php echo $prefix; ?>direccion_trabajo" value="<?php  echo (!empty($direccion_trabajo)) ? $direccion_trabajo : "" ?>"  class="form-control direccion_trabajo input-xs">
													<br>
												</div>
											</div>
										</div>

										<div class="row" style="">
											<div class="col-md-4">
												<div class="">
													<label class="">Ingreso Mensual</label>
													<input type="text" name="ingreso_mensual" id="<?php echo $prefix; ?>ingreso_mensual" value="<?php  echo (!empty($ingreso_mensual)) ? $ingreso_mensual : "0.00" ?>" class="form-control ingreso_mensual numerillo input-xs">
												</div>
											</div>
										</div>
									</div>
									
									<div class="info_juridico" style="display:none;">
										<div class="row" style="">
											<div class="list_representantes" style="">
											<?php
												/*$html_rep ='';
												if(!empty($representantes)){
													foreach($representantes as $key=>$val){
														$html_rep.='<div class="col-md-12">';
														$html_rep.='	<div class="row" style="">';
														$html_rep.='		<div class="col-md-3">';
														$html_rep.='			<div class="">';
																	//if($key==0){
														$html_rep.='				<label class="required">Nombres</label>';
																	//}
														$html_rep.='				<input type="text" name="nombre_representante[]" value="'.$val['nombre_representante'].'" class="form-control nombre_representante here_req">';
														$html_rep.='			</div>';
														$html_rep.='		</div>';
																	
														$html_rep.='		<div class="col-md-6">';
														$html_rep.='			<div class="">';
																	//if($key==0){
														$html_rep.='				<label class="required">Apellidos</label>';
																	//}
														$html_rep.='				<input type="text" name="apellidos_representante[]" value="'.$val['apellidos_representante'].'" class="form-control apellidos_representante here_req">';
														$html_rep.='			</div>';
														$html_rep.='		</div>';
																	
														$html_rep.='		<div class="col-md-3">';
																	//if($key==0){
														$html_rep.='			<label class="required">Dni</label>';
																	//}
														$html_rep.='			<div class="input-group">';
														$html_rep.='				<input type="text" name="dni_representante[]" maxlength="8" value="'.$val['dni_representante'].'" class="form-control dni_representante here_req">';
														$html_rep.='				<span class="input-group-btn tooltip-demo">';
														if($key==0){
															$html_rep.='					<button type="button" id="addRepresentante" style="" class="btn btn-outline btn-success" data-toggle="tooltip" title="Añadir Representante">';
															$html_rep.='						<i class="fa fa-plus-square"></i>';
															$html_rep.='					</button>';
														}else{
															$html_rep.='					<button type="button" style="height: 30px;" class="btn btn-outline delete_repres btn-success" title="Borrar Representante">';
															$html_rep.='						<i class="fa fa-trash"></i>';
															$html_rep.='					</button>';
														}
														$html_rep.='				</span>';
														$html_rep.='			</div>';
														$html_rep.='		</div>';
																	
														$html_rep.='	</div>';
														$html_rep.='</div>';
													}
												}else{
													$html_rep.='<div class="col-md-12">';
													$html_rep.='	<div class="row" style="">';
													$html_rep.='		<div class="col-md-3">';
													$html_rep.='			<div class="">';
													$html_rep.='				<label class="required">Nombres</label>';
													$html_rep.='				<input type="text" name="nombre_representante[]" id="" class="form-control nombre_representante here_req">';
													$html_rep.='			</div>';
													$html_rep.='		</div>';

													$html_rep.='		<div class="col-md-6">';
													$html_rep.='			<div class="">';
													$html_rep.='				<label class="required">Apellidos</label>';
													$html_rep.='				<input type="text" name="apellidos_representante[]" id="" class="form-control apellidos_representante here_req">';
													$html_rep.='			</div>';
													$html_rep.='		</div>';
																
													$html_rep.='		<div class="col-md-3">';
													$html_rep.='			<label class="required">Dni</label>';
													$html_rep.='			<div class="input-group">';
													$html_rep.='				<input type="text" name="dni_representante[]" maxlength="8" class="form-control dni_representante here_req">';
													$html_rep.='				<span class="input-group-btn tooltip-demo">';
													$html_rep.='					<button type="button" id="addRepresentante" style="" class="btn btn-outline btn-success" data-toggle="tooltip" title="Añadir Representante">';
													$html_rep.='						<i class="fa fa-plus-square"></i>';
													$html_rep.='					</button>';
													$html_rep.='				</span>';
													$html_rep.='			</div>';
													$html_rep.='		</div>';

													$html_rep.='	</div>';
													$html_rep.='</div>';
												}
												echo $html_rep;
												*/
											?>
											</div>
										</div>
									</div>
								</div>
							</fieldset>
						</div>
					</div>
				</div>
			</div>
			
			<div class="row" style="padding:10px;">
				<div class="col-md-12">
					<center>
						<button id="<?php echo $prefix; ?>btn_cancel" class="btn btn-sm btn-white btn_cancel" data-controller="<?php echo $controller; ?>">Cancelar</button>
						<button id="<?php echo $prefix; ?>btn_save_cliente" class="btn btn-sm btn-primary" data-controller="<?php echo $controller; ?>">Guardar</button>
					</center>
				</div>
			</div>
		</div>
	</div>
</form>

<div id="modal-zona" class="modal fade" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Registrar zona</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<?php echo $form_zona; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="modal-ocupacion" class="modal fade" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Registrar ocupacion</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<?php echo $form_ocupacion; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
	.photo{height:130px;width: 100%;}
	.numerillo{text-align: right;}
	.cursor{cursor:pointer;}
</style>