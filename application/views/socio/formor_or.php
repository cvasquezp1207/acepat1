<input id="credito_juridico" type="hidden" value="<?php echo $credito_juridico; ?>"></input>
<div class="row" style="border: 0px solid #c1c1c1;">
	<form id="form_<?php echo $controller; ?>" class="form-horizontal app-form form-uppercase">
	    <input id="<?php echo $prefix; ?>idcliente" type="hidden" name="idcliente" value="<?php  echo (!empty($idcliente)) ? $idcliente : ''; ?>"/>
	    <input type="file" name="file" id="<?php echo $prefix; ?>file" style="display: none;" onchange='leerarchivobin(this)' />
		<input type="hidden" id="<?php echo $prefix; ?>foto" name="foto" value="<?php echo (!empty($foto)) ? $foto : "anonimo.jpg"; ?>"/>
		<input type="hidden" id="<?php echo $prefix; ?>logo_new" name="logo_new" value="<?php echo (!empty($foto)) ? $foto : "anonimo.jpg"; ?>"/>

		<div class="col-lg-12" >
	        <div class="row" style="">
	            <div class="col-md-5">
					<div class="ibox float-e-margins" style="background: white;">
						<div class="ibox-title">						
							<h5><span class="widget "><i class="fa fa-folder-open-o" aria-hidden="true"></i></span>Datos Principales</h5>
							<div class="ibox-tools">
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
											<div class="">
												<label class="required">Tipo Cliente</label>
												<select class="form-control animation_select" name="tipo" id="<?php echo $prefix; ?>tipo" data-animation="rollIn">
													<?php
														$array_tcliente = array("N"=>'PERSONA NATURAL',"J"=>'PERSONA JURIDICA');
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
											<hr></hr>
										</div>
									</div>
									
									<div class="info_natural content_animed" id="">
										<div class="row" style="">
											<div class="col-md-12">
												<div class="row" style="">
													<div class="col-md-7">
														<div class="">
															<label class="required">Nombres</label>
															<input type="text" class="form-control nombres here_req req" placeholder="Nombres" value="<?php  echo (!empty($nombres)) ? $nombres : "" ?>" ></input>
															<!--<br>-->
															<div class="separation"></div>
														</div>

														<div class="">
															<label class="required">Apellidos</label>
															<input type="text" name="apellidos" class="form-control apellidos here_req req" placeholder="Apellidos" value="<?php  echo (!empty($apellidos)) ? $apellidos : "" ?>" ></input>
															<!--<br>-->
															<div class="separation"></div>
														</div>

														<div class="">
															<label class="">E-Mail</label>
															<div class="input-group email">
																<span class="input-group-addon">@</span>
																<input type="text" class="form-control cliente_email" value="<?php  echo (!empty($cliente_email)) ? $cliente_email : "" ?>">
															</div>
															<!--<br>-->
															<div class="separation"></div>
														</div>
													</div>

													<div class="col-md-5">
														<div class="form-group">
															<div class="col-md-12 ">
																<label class="control-label">Foto</label>
																<div class="">
																	<div id="<?php echo $prefix; ?>load_photo" class="app-img-temp img-thumbnail load_photo">
																		<?php
																			$n_logo = './../app/img/cliente/anonimo.jpg';
																			if(!empty($idcliente))
																				if(!empty($foto))
																					$n_logo = '../../app/img/cliente/'.$foto;
																				else
																					$n_logo = '../../app/img/cliente/anonimo.jpg';
																		?>
																		<img id="<?php echo $prefix; ?>photoN" src="<?php echo $n_logo;?>" class="img-responsive img-thumbnail photo" style="background:#f3f3f4;"/>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>

										<div class="row" style="">
											<div class="col-md-7">
												<div class="">
													<label class="required">Estado Civil</label>
														<?php echo $estado_civil;?>
														<!--<br>-->
													<div class="separation"></div>
												</div>
											</div>

											<div class="col-md-5">
												<div class="">
													<label class="required">Sexo</label>
													<select class="form-control here_req req" name="sexo" id="<?php echo $prefix; ?>sexo">
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
												<br>
											</div>
										</div>

										<div class="row" style="">
											<div class="col-md-5">
												<div class="">
													<label class="required">Fecha Nac.</label>
													<div class="input-group date">
												       	<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
												       	<input name="fecha_nac" id="<?php echo $prefix; ?>fecha_nac" type="text" class="form-control fecha_nac here_req req" value="<?php  echo (!empty($fecha_nac)) ? dateFormat($fecha_nac,"d/m/Y") : "" ?>" />
													</div>
													<br>
												</div>
											</div>

											<div class="col-md-3">
												<div class="">
													<label class="required">DNI</label>
													<input name="dni" class="form-control dni here_req req" maxlength=8 value="<?php  echo (!empty($dni)) ? $dni : "" ?>"></input>
												</div>
												<br>
											</div>
											
											<div class="col-md-4">
												<div class="">
													<label class="">RUC</label>
													<input type="text" class="form-control ruc" maxlength=11 value="<?php  echo (!empty($ruc)) ? $ruc : "" ?>"></input>
												</div>
												<br>
											</div>
										</div>

										<div class="row" style="">
											<div class="col-md-12">
												<div class="row" style="">
													<div class="col-md-12">
														<!--
														<div class="" >
															<label class="required">Zona</label>
															<?php echo $zona;?>
														</div>
														-->

														<label class="">Zona</label>
														<div class="input-group">
															<?php echo $zona_combo;?>
															
															<span class="input-group-btn tooltip-demo">
																<button type="button" id="btn-registrar-zona" class="btn btn-outline btn-success " data-toggle="tooltip" title="&iquest;No existe la zona? Registrar aqui">
																	<i class="fa fa-edit"></i>
																</button>
															</span>
														</div>
														<br>
													</div>

													<!--<div class="col-sm-4">
														<div class="">
															<label class="required">Estado Civil</label>
															<?php echo $estado_civil;?>
														</div>
													</div>
													-->
												</div>
											</div>
										</div>								
									</div>

									<div class="info_juridico content_animed" id="animation_box_0">
										<div class="row" style="">
											<div class="col-md-12">
												<div class="row" style="">
													<div class="col-md-7">
														<div class="">
															<label class="required">Razon Social</label>
															<?php
																$razon_social = '';
																if (!empty($nombres))
																	$razon_social.=$nombres;
																
																if (!empty($apellidos))
																	$razon_social.=' '.$apellidos;
															?>
															<input type="text" class="form-control nombres here_req" placeholder="Nombres" value="<?php  echo $razon_social; ?>"></input>
															<!--<br>-->
															<div class="separation"></div>
														</div>

														<div class="">
															<div class="" style="">
																<label class="required">RUC</label>
																<input type="text" class="form-control ruc here_req" maxlength=11 value="<?php  echo (!empty($ruc)) ? $ruc : "" ?>"></input>
																<!--<br>-->
																<div class="separation"></div>
															</div>
														</div>

														<div class="">
															<label class="">E-Mail</label>
															<div class="input-group email">
																<span class="input-group-addon">@</span>
																<input type="text" class="form-control cliente_email" value="<?php  echo (!empty($cliente_email)) ? $cliente_email : "" ?>" >
																<div class="separation"></div>
															</div>
														</div>
													</div>
													<div class="col-md-5">
														<div class="form-group">
															<div class="col-md-12">
																<label class="control-label">Foto</label>
																<div id="load_photoJ" class="app-img-temp img-thumbnail load_photo">
																	<?php
																		$n_logo = './../app/img/cliente/anonimo.jpg';
																		if(!empty($idcliente))
																			if(!empty($foto))
																				$n_logo = '../../app/img/cliente/'.$foto;
																			else
																				$n_logo = '../../app/img/cliente/anonimo.jpg';
																	?>
																	<img id="<?php echo $prefix; ?>photoJ" src="<?php echo $n_logo;?>" class="img-responsive img-thumbnail photo" style="background:#f3f3f4;"/>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>	
										</div>
									</div>

									<div class="row con_credito" style="">
										<div class="col-md-12">
											<div class="row" style="">
												<div class="col-md-4">
													<div class="">
														<label>Es Especial?</label>
														<div class="onoffswitch">
															<input type="checkbox" name="especial" id="<?php echo $prefix; ?>especial" class="onoffswitch-checkbox" value="1" <?php echo (isset($especial) && $especial == 'S') ? "checked" : ""; ?> >
															<label class="onoffswitch-label" for="especial">
																<span class="onoffswitch-inner"></span>
																<span class="onoffswitch-switch"></span>
															</label>
														</div>
													</div>
													<br>
												</div>

												<div class="col-md-4">
													<div class="">
														<label>Linea Credito</label>
														<div class="onoffswitch">
															<input type="checkbox" name="linea_credito" id="<?php echo $prefix; ?>linea_credito" class="onoffswitch-checkbox" value="1" <?php echo (isset($linea_credito) && $linea_credito == 'S') ? "checked" : ""; ?> >
															<label class="onoffswitch-label" for="linea_credito">
																<span class="onoffswitch-inner"></span>
																<span class="onoffswitch-switch"></span>
															</label>
														</div>
													</div>
													<br>
												</div>
												<div class="col-md-4">
													<div class="">
														<label class="">Limite Credito</label>
														<input type="text" name="limite_credito" id="<?php echo $prefix; ?>limite_credito" readonly="readonly" value="<?php  echo (!empty($limite_credito)) ? $limite_credito : "0.00" ?>" class="form-control numerillo limite_credito">
													</div>
													<br>
												</div>
											</div>
										</div>
									</div>
									
									<div class="row" style="">
										<div class="col-md-12">
											<center>
												<button id="<?php echo $prefix; ?>btn_cancel" class="btn btn-sm btn-white btn_cancel" data-controller="<?php echo $controller; ?>">Cancelar</button>
												<button id="<?php echo $prefix; ?>btn_save_cliente" class="btn btn-sm btn-primary" data-controller="<?php echo $controller; ?>">Guardar</button>
											</center>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>   
				</div>
	            
				<div class="col-md-7">
					<div class="row">
						<div class="col-md-12 info_all">
							<div class="ibox float-e-margins" style="background: white;">
								<div class="ibox-title">
									<h5 style="margin-right:20px;"><span class="widget "><i class="fa fa-android" aria-hidden="true"></i></span> Otros </h5>
									<div class="ibox-tools">
										<a class="collapse-link">
											<i class="fa fa-chevron-up"></i>
										</a>
									</div>
								</div>
								
								<div class="ibox-content">
									<div class="form-group no-margins">
										<div class="row" style="">
											<div class="col-md-12">
												<div class="form-group" style="margin-right:0px;margin-left:0px;">
													<label class="">Referencia</label>
													<textarea name="observacion" id="<?php echo $prefix; ?>observacion" value="<?php  echo (!empty($observacion)) ? $observacion : "" ?>" class="form-control"></textarea>
												</div>
											</div>
										</div>
										
										<div class="row" style="">
											<div class="col-md-8">
												<div class="row">
													<div class="col-md-12">
														<label class="required">Direccion</label>
														<div class="list_direcciones">

															<?php
																$html_dir ='';
																$checked = 'checked';
																if(!empty($direcciones)){
																	foreach($direcciones as $key=>$val){
																		$checked = '';
																		if ($val['dir_principal']=='S') {
																			$checked = 'checked';
																		}
																		$html_dir.='<div class="input-group" style="margin-top:4px;">';
																		$html_dir.='	<span class="input-group-addon tooltip-demo cursor" style="padding: 0px 2px 1px 0px;margin-left: 5px;">';
																		$html_dir.='		<div class="radio" style="padding-left: 30px;" data-toggle="tooltip" title="Seleccione Direccion Principal">';
                                                   	$html_dir.='				<input type="radio" name="radio_dir"  class="dir_principal" '.$checked.' >';
                                                   	$html_dir.='				<label></label>';
                                                		$html_dir.='		</div>';
                                                   	$html_dir.='		<input type="hidden" class="dir_principal_val" name="dir_principal[]" value="'.$val['dir_principal'].'" >';
																		$html_dir.='	</span>';
																		$html_dir.='	<input type="text" name="direccion[]" placeholder="Direccion..." value="'.$val['direccion'].'" class="form-control direccion here_req req">';
																		$html_dir.='	<span class="input-group-addon tooltip-demo" style="border:1px solid #1c84c6;color: #1c84c6;border-radius: 0px 3px 3px 0px;">';
																		if($key==0){
																			//$html_dir.='		<button type="button" id="addDireccion" style="height: 30px;" class="btn btn-outline btn-success" data-toggle="tooltip" title="Añadir direccion">';
																			//$html_dir.='			<i class="fa fa-plus-square"></i>';
																			//$html_dir.='		</button>';
																			$html_dir.='<div data-toggle="tooltip" id="addDireccion" title="Añadir direccion" class="">';
																			$html_dir.='	<i class="fa fa-plus-square"></i>';
																			$html_dir.='</div>';
																		}else{
																			$html_dir.='<div data-toggle="tooltip" class="delete_direccion" title="Borrar direccion">';
																			//$html_dir.='		<button type="button" style="height: 30px;" class="btn btn-outline btn-success delete_direccion" title="Borrar direccion">';
																			$html_dir.='			<i class="fa fa-trash"></i>';
																			//$html_dir.='		</button>';	
																			$html_dir.='</div>';
																		}
																		$html_dir.='	</span>';
																		$html_dir.='</div>';
																	}
																}else{
																	$html_dir.='<div class="input-group" >';
																	$html_dir.='	<span class="input-group-addon tooltip-demo cursor" style="padding: 0px 2px 1px 0px;margin-left: 5px;">';
																	$html_dir.='		<div class="radio" style="padding-left: 30px;" data-toggle="tooltip" title="Seleccione Direccion Principal">';
                                                  	$html_dir.='				<input type="radio" name="radio_dir" class="dir_principal" '.$checked.' >';
                                                  	$html_dir.='				<label></label>';
                                                	$html_dir.='		</div>';
                                                  	$html_dir.='		<input type="hidden" class="dir_principal_val" name="[]" value="S" >';
																	$html_dir.='	</span>';
																	$html_dir.='	</span>';
																	$html_dir.='	<input type="text" name="direccion[]" placeholder="Direccion..." class="form-control direccion here_req req">';
																	$html_dir.='	<span class="input-group-addon tooltip-demo cursor" id="addDireccion" style="border:1px solid #1c84c6;color: #1c84c6;border-radius: 0px 3px 3px 0px;">';
																	$html_dir.='		<div data-toggle="tooltip" title="Añadir direccion" class="">';
																	//$html_dir.='		<button type="button" id="addDireccion" style="height: 30px;" class="btn btn-outline btn-success" data-toggle="tooltip" title="Añadir direccion">';
																	$html_dir.='			<i class="fa fa-plus-square"></i>';
																	//$html_dir.='		</button>';
																	$html_dir.='		</div>';
																	$html_dir.='	</span>';
																	$html_dir.='</div>';
																}
																echo $html_dir;
															?>
														</div>
														<br>
													</div>
													<!--
													<div class="col-md-2">
														<label class="">&nbsp;</label>
														<a href="#" id="addDireccion">
															<i class="fa fa-plus-square fa-2x"></i>
														</a>
													</div>
													-->
												</div>
											</div>

											<div class="col-md-4">
												<div class="row">
													<div class="col-md-12">
														<label class="">Telefono</label>
														<div class="list_telefonos">
															<?php
																$html_telf ='';
																if(!empty($telefonos)){
																	foreach($telefonos as $key=>$val){
																		$html_telf.='<div class="input-group">';
																		$html_telf.='	<input type="text" name="telefono[]" value="'.$val['telefono'].'" class="form-control telefono">';
																		$html_telf.='	<span class="input-group-btn tooltip-demo">';
																		if($key==0){
																			$html_telf.='		<button type="button" id="addTelefono" style="height: 30px;" class="btn btn-outline btn-success" data-toggle="tooltip" title="Añadir Telefono">';
																			$html_telf.='			<i class="fa fa-plus-square"></i>';
																			$html_telf.='		</button>';																
																		}else{
																			$html_telf.='		<button type="button" style="height: 30px;" class="btn btn-outline btn-success delete_telefono" title="Borrar Telefono">';
																			$html_telf.='			<i class="fa fa-trash"></i>';
																			$html_telf.='		</button>';	
																		}
																		$html_telf.='	</span>';
																		$html_telf.='</div>';
																	}
																}else{
																	$html_telf.='<div class="input-group">';
																	$html_telf.='	<input type="text" name="telefono[]" class="form-control telefono">';
																	$html_telf.='	<span class="input-group-btn tooltip-demo">';
																	$html_telf.='		<button type="button" id="addTelefono" style="height: 30px;" class="btn btn-outline btn-success" data-toggle="tooltip" title="Añadir Telefono">';
																	$html_telf.='			<i class="fa fa-plus-square"></i>';
																	$html_telf.='		</button>';
																	$html_telf.='	</span>';
																	$html_telf.='</div>';
																}
																echo $html_telf;
															?>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-12 info_natural content_animed" >
							<div class="ibox float-e-margins" style="background: white;">
								<div class="ibox-title">
									<h5 style="margin-right:20px;"><span class="widget "><i class="fa fa-suitcase" aria-hidden="true"></i></span> Trabajo </h5>
									<div class="ibox-tools">
										<a class="collapse-link">
											<i class="fa fa-chevron-up"></i>
										</a>
									</div>
								</div>
								
								<div class="ibox-content">
									<div class="form-group no-margins">
										<div class="">
											<fieldset>
												<div class="row" style="">
													<div class="col-md-6">
														<div class="">
															<label class="">Situacion Laboral</label>
															<?php echo $situacion; ?>
														</div>
															<br>
													</div>

													<div class="col-md-6">
														<div class="">
															<label class="">Centro de Trabajo</label>
															<input type="text" name="centro_laboral" id="<?php echo $prefix; ?>centro_laboral" value="<?php  echo (!empty($centro_laboral)) ? $centro_laboral : "" ?>" class="form-control centro_laboral">
															<br>
														</div>
													</div>
												</div>

												<div class="row" style="">
													<div class="col-md-6">
														<label class="">Ocupacion / Cargo</label>
														<div class="input-group">
															<?php echo $ocupacion_cli;?>
															
															<span class="input-group-btn tooltip-demo">
																<button type="button" id="btn-registrar-ocupacion" class="btn btn-outline btn-success" data-toggle="tooltip" title="&iquest;No existe el Cargo/Ocupacion? Registrar aqui">
																	<i class="fa fa-edit"></i>
																</button>
															</span>
														</div>
														<br>
													</div>

													<div class="col-md-6">
														<div class="">
															<label class="">Direccion Trabajo</label>
															<input type="text" name="direccion_trabajo" id="<?php echo $prefix; ?>direccion_trabajo" value="<?php  echo (!empty($direccion_trabajo)) ? $direccion_trabajo : "" ?>"  class="form-control direccion_trabajo">
															<br>
														</div>
													</div>
												</div>

												<div class="row" style="">
													<div class="col-md-4">
														<div class="">
															<label class="">Ingreso Mensual</label>
															<input type="text" name="ingreso_mensual" id="<?php echo $prefix; ?>ingreso_mensual" value="<?php  echo (!empty($ingreso_mensual)) ? $ingreso_mensual : "0.00" ?>" class="form-control ingreso_mensual numerillo">
														</div>
													</div>
												</div>
											</fieldset>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-12 info_juridico content_animed" >
							<div class="ibox float-e-margins" style="background: white;">
								<div class="ibox-title">
									<h5 style="margin-right:20px;"><span class="widget "><i class="fa fa-suitcase" aria-hidden="true"></i></span> Representante </h5>
									<div class="ibox-tools">
										<a class="collapse-link">
											<i class="fa fa-chevron-up"></i>
										</a>
									</div>
								</div>
								
								<div class="ibox-content">
									<div class="form-group no-margins">
										<div class="row" style="">
											<div class="list_representantes" style="">
												<?php
													$html_rep ='';
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
																$html_rep.='					<button type="button" id="addRepresentante" style="height: 30px;" class="btn btn-outline btn-success" data-toggle="tooltip" title="Añadir Representante">';
																$html_rep.='						<i class="fa fa-plus-square"></i>';
																$html_rep.='					</button>';
															}else{
																// $html_rep.='					<button type="button" style="height: 30px;" class="btn btn-outline delete_repres btn-success" data-toggle="tooltip" title="Borrar Representante">';
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
														$html_rep.='					<button type="button" id="addRepresentante" style="height: 30px;" class="btn btn-outline btn-success" data-toggle="tooltip" title="Añadir Representante">';
														$html_rep.='						<i class="fa fa-plus-square"></i>';
														$html_rep.='					</button>';
														$html_rep.='				</span>';
														$html_rep.='			</div>';
														$html_rep.='		</div>';
														
														$html_rep.='	</div>';
														$html_rep.='</div>';
													}
													echo $html_rep;
												?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
	        </div>
	    </div>
	</form>
</div>

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
	.widget {padding: 4px 10px;border:0px solid #1ab394}
	.info_natural,.info_juridico{display: none;}/*HERE ....*/
	.centro{text-align: center;}
	#form-data .form-control{font-size: 11px !important;padding: 4px 4px !important;}

	.numerillo{text-align: right;}
	.nro{font-size: 10.5px;color: black;}	
	input.form-control{padding: 2px 4px;font-size: 12px;height: 30px}
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
	.ibox-content{background: transparent;}
	#animation_box_0{background-color: white;}
	.cursor{cursor: pointer;}
	/*span.cursor:hover{background-color: #1a7bb9; border-color: #1a7bb9;}*/
	.separation{height: 8px;}
  </style>