<div class="row wrapper border-bottom white-bg page-heading fixed-button-top form-inline">
	<form id="form_format">
	<div class="col-sm-8 text-left">
		<div class="form-group no-margins">
			<label>Documento</label>			
			<?php echo $tipo_documento;?>
		</div>
		
		<div class="form-group" style="margin-left:15px;">
			<label>Serie</label>
			<select name="serie" id="serie" class="form-control input-xs">
			</select>
		</div>
	</div>
	</form>
	
	<div class="col-sm-4 text-right">
		<div class="btn-group">
			<!-- Definir el tamaño del lienzo, entre otras cosas -->
			<button type="button" id="btn-save" class="btn btn-white btn-xs" style="margin-right:10px;"><i class="fa fa-save"></i> Guardar</button>
			<button type="button" class="btn btn-white btn-xs" id="option"><i class="fa fa-cogs"></i> Opciones</button>
			<button type="button" class="btn btn-white btn-xs" id="add-fila"><i class="fa fa-pulss"></i> Filas (+)</button>
			<button type="button" class="btn btn-white btn-xs" id="add-detalle"><i class="fa fa-pulss"></i> Detalle (+)</button>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-sm-12">
		<div class="ibox float-e-margins">
			<div class="ibox-content" style="">
				<form id="form_content">
				<div class="row" style="min-height:100px;border:1px solid #ccc;margin: 0 auto;" id="lienzo">
					<div class="container_f">
						<div class="row" style="margin-left:0px;margin-right:0px;"></div> <!-- /row -->
					</div>
				</div>
				</form>
			</div>
		</div>
	</div>
</div>

		<div class="modal fade" id="more_option" aria-labelledby="myLargeModalLabel" data-backdrop="static"  >
			<div class="modal-dialog" >
				<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title">Opciones del formato</h4>
						</div>
						<div class="modal-body">
							<form id="form_opt">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-2">
											<label for="" class="labels" >Ancho</label>
											<input id="width_lienzo" name="width_lienzo" class="form-control input-xs" placeholder='0cm'/>
										</div>
										
										<div class="col-sm-2">
											<label for="" class="labels" >Largo</label>
											<input id="height_lienzo" name="height_lienzo" class="form-control input-xs" placeholder='0cm'/>
										</div>
										
										<div class="col-sm-3">
											<label for="" class="labels" >Tamaño Letra</label>
											<input id="font_size_lienzo" name="font_size_lienzo" class="form-control input-xs" placeholder='10px'/>
										</div>
										
										<div class="col-sm-3">
											<label for="" class="labels" >Fuente Letra</label>
											<input id="fuente_letra_lienzo" name="fuente_letra_lienzo" class="form-control input-xs" placeholder='Arial'/>
										</div>
										
										<div class="col-sm-2">
											<label for="" class="labels" >Ver Borde</label>
											<div class="onoffswitch">
												<input type="checkbox" id="ver_borde" class="onoffswitch-checkbox" >
												<label class="onoffswitch-label" for="ver_borde">
													<span class="onoffswitch-inner"></span>
													<span class="onoffswitch-switch"></span>
												</label>
											</div>
										</div>
									</div>
								</div>
							</form>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">No aplicar</button>
							<button type="button" id="aplicar" class="btn btn-primary btn-save">Aplicar</button>
						</div>
				</div>
			</div>
		</div>
		
	<div class="modal fade" id="option_param_etiqueta" aria-labelledby="myLargeModalLabel" data-backdrop="static"  >
		<div class="modal-dialog" >
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="editando_fila_etiqueta"></h4>
				</div>
				
				<div class="modal-body">
					<form id="">
						<div class="form-group">
							<div class="row">
								<div class="col-sm-3">
									<label for="" class="labels" >Ancho Etiqueta</label>
									<input id="ancho_etiqueta" class="form-control input-xs" placeholder='0cm'/>
								</div>

								<div class="col-sm-3">
									<label for="" class="labels" >Sangria Etiqueta</label>
									<input id="sangria_etiqueta" class="form-control input-xs" placeholder='0cm'/>
								</div>
								
								<div class="col-sm-3">
									<label for="" class="labels" >Alinear texto</label>
									<input id="text_align_etiqueta" class="form-control input-xs" placeholder='left | center | right'/>
								</div>
							</div>
						</div>
					</form>
				</div>
				
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">No aplicar</button>
					<button type="button" id="aplicar" class="btn btn-primary btn-save">Aplicar</button>
				</div>
			</div>
		</div>
	</div>
	
	<div class="modal fade" id="option_param_detalle" aria-labelledby="myLargeModalLabel" data-backdrop="static"  >
		<div class="modal-dialog" >
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="editando_detalle_etiqueta"></h4>
				</div>
				
				<div class="modal-body">
					<form id="">
						<div class="form-group">
							<div class="row">
								<div class="col-sm-3">
									<label for="" class="labels" >Alineacion Texto</label>
									<input id="text_align_detalle" class="form-control input-xs" placeholder='left | center | right'/>
								</div>
								
								<!--
								<div class="col-sm-4">
									<label for="" class="labels" >Largo celda Detalle</label>
									<input id="height_colum_detalle" class="form-control input-xs" placeholder='0cm'/>
								</div>
								-->
								
								<div class="col-sm-6">
									<label for="" class="labels" >Ancho celda <span id="descripcion_celda_detalle"></span></label>
									<input id="width_colum_detalle" class="form-control input-xs" placeholder='0cm'/>
								</div>
							</div>
						</div>
					</form>
				</div>
				
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">No aplicar</button>
					<button type="button" id="aplicar" class="btn btn-primary btn-save">Aplicar</button>
				</div>
			</div>
		</div>
	</div>
	
	<div class="modal fade" id="panel_add_fila" aria-labelledby="myLargeModalLabel" data-backdrop="static"  >
		<div class="modal-dialog" >
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="">Panel para añadir filas al formato</h4>
				</div>
				
				<div class="modal-body">
					<form class="form_temp">
						<div class="form-group">
							<div class="row">
								<div class="col-sm-12">
									<label>Etiquetas</label>
									<div class="input-group">
										<select class="form-control input-xs idetiqueta" id="idetiquetaf"></select>
										<span class="input-group-btn ">
											<button type="button" id="btn-add-etiqueta" class="btn btn-outline btn-success btn-xs">
												<i class="fa fa-plus"></i>
											</button>
										</span>
									</div>
								</div>
							</div>
							
							<div class="row">
								<br>
								<div class="col-sm-12 fila_generada">
									<table border='1' style="border:1px solid #ccc;"  cellspacing=0 cellpadding=0 class="content_temp">
										<tbody><tr></tr></tbody>
									</table>
								</div>
							</div>
						</div>
					</form>
				</div>
				
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">No aplicar</button>
					<button type="button" id="aplicar" class="btn btn-primary btn-save">Aplicar</button>
				</div>
			</div>
		</div>
	</div>
	
	<div class="modal fade" id="panel_add_detalle" aria-labelledby="myLargeModalLabel" data-backdrop="static"  >
		<div class="modal-dialog" >
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="">Panel para añadir detalle al fomato</h4>
				</div>
				
				<div class="modal-body">
					<form class="form_temp">
						<div class="form-group">
							<div class="row">
								<div class="col-sm-3">
									<label>No Filas</label>
									<input id="cant_elementos_detalle_lienzo" class="form-control input-xs" placeholder="1"/>
								</div>
								
								<div class="col-sm-5">
									<label>Ancho de celdas</label>
									<input id="ancho_celda_detalle" name="ancho_celda_detalle" class="form-control input-xs" placeholder="1cm"/>
								</div>
								
								
								<div class="col-sm-4">
									<label>Etiquetas</label>
									<div class="input-group">
										<select class="form-control input-xs idetiqueta" id="idetiquetad"></select>
										<span class="input-group-btn ">
											<button type="button" id="btn-add-etiqueta" class="btn btn-outline btn-success btn-xs">
												<i class="fa fa-plus"></i>
											</button>
										</span>
									</div>
								</div>
							</div>
							
							<div class="row">
								<br>
								<div class="col-sm-12 fila_generada">
									<table border='1' style="border:1px solid #ccc;"  cellspacing=0 cellpadding=0 class="content_temp table_detalle">
										<thead><tr></tr></thead>
										<tbody><tr></tr></tbody>
									</table>
								</div>
							</div>
						</div>
					</form>
				</div>
				
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">No aplicar</button>
					<button type="button" id="aplicar" class="btn btn-primary btn-save">Aplicar</button>
				</div>
			</div>
		</div>
	</div>
	
	<div class="modal fade" id="option_detalle" aria-labelledby="myLargeModalLabel" data-backdrop="static"  >
		<div class="modal-dialog modal-sm" >
			<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title">Parametro Etiqueta</h4>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<div class="row">
								<div class="col-sm-12">
									<h1><div id="label_seleccionado"></div></h1>
								</div>
							</div>
							
							<div class="row">
										<div class="col-sm-6">
											<label for="observaciones" class="labels" >Ancho</label>
											<input id="width_etiqueta" name="" class="form-control input-xs" placeholder='0cm'/>
										</div>
										
										<div class="col-sm-6">
											<label for="observaciones" class="labels" >Largo</label>
											<input id="height_etiqueta" name="" class="form-control input-xs" placeholder='0cm'/>
										</div>
							</div>
							
							<div class="row">
								<div class="col-sm-12">
									<label for="observaciones" class="labels" >Ver Etiqueta</label>
									<div class="onoffswitch">
										<input type="checkbox" id="v_label" class="onoffswitch-checkbox" value="1" >
										<label class="onoffswitch-label" for="v_label">
											<span class="onoffswitch-inner"></span>
											<span class="onoffswitch-switch"></span>
										</label>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">No aplicar</button>
						<button type="button" id="aplicar" class="btn btn-primary btn-save">Aplicar</button>
					</div>
			</div>
		</div>
	</div>
<style>
.container_f .panel-body{padding:1px 2px 1px 3px;}
.container_f .panel{margin-bottom: 4px;}
.wrapper-content {
    padding: 5px 5px 5px;
}
.label_span{background:yellow;}
.container_f td,th{border:0px;border-right: 1px solid #ccc;}
.container_f table{border:none !important;}
</style>

<script type='text/javascript' src="app/js/jquery.min.js"></script>

<script>
	$contedor 		= $('.container_f .row');

	id_content      = "#panel_add_fila table.content_temp tbody";
	id_content_d    = "#panel_add_detalle table.content_temp tbody";
	id_content_d_h  = "#panel_add_detalle table.content_temp thead";
	$div = detallito_thead = '';
	x_item=0;
	tipo_contenido = 'f';

	$(document).ready(function() {
		$('.idetiqueta').on("change",function(e){
			str = "idetiqueta="+$(this).val();
			str+= "&tipo_contenido="+tipo_contenido;

			if($(this).val()!=''){
				ajax.post({url: _base_url+_controller+"/get_etiqueta", data: str}, function(res) {
					detallito = temp_tr = detallito_thead = "";
					if(!res){
						res=$('#idetiqueta'+tipo_contenido+' option:selected').text();
					}
					xres = res;
					detallito+= "				<td style='' class='td_"+xres+"' ajax-text='"+res+"' ><div>";
					if(tipo_contenido=='f'){
						detallito+= "					<span class='label_span' id='label_"+$('#idetiqueta'+tipo_contenido).val()+"'></span>{"+xres+"}";
						detallito+= "					<div class='pull-right'>";
						detallito+= "						<a class='edit_option_etiqueta' title='Editar Etiqueta'><i class='fa fa-gear'></i></a>  ";
						detallito+= "					</div>";
					}else{
						detallito+= "{"+xres+"}";
						detallito+= "					<div class='pull-right'>";
						detallito+= "						<a class='trash_colum_prev' title='Eliminar Etiqueta'><i class='fa fa-trash'></i></a>  ";
						detallito+= "					</div>";
					}
					detallito+= "					<input type='hidden' class='sangria_etiqueta'>";
					detallito+= "					<input type='hidden' class='width_etiqueta_fila'>";
					detallito+= "					<input class='width_etiqueta' type='hidden' value='"+$('#width_etiqueta').val()+"'/>";
					detallito+= "					<input class='height_etiqueta' type='hidden' value='"+$('#height_etiqueta').val()+"'/>";
					detallito+= "					<input class='align_etiqueta' type='hidden' value='left'/>";
					detallito+= "					<input type='hidden' name='idetiqueta[]' value='"+$('#idetiqueta'+tipo_contenido).val()+"' />";
					detallito+= "				</div></td>";
					
					if(tipo_contenido=='d'){
						detallito_thead+= "		<td style='' class='td_"+xres+"'>";
						detallito_thead+= "			<div class='pull-right'>";
						detallito_thead+= "				<a class='edit_detalle' title='Editar Detalle'><i class='fa fa-gear'></i></a>  ";
						detallito_thead+= "			</div>";
						detallito_thead+= "		</td>";
						
						$(id_content_d_h+' tr').append(detallito_thead);
						id_content = id_content_d;
					}
					$(id_content+" tr").append(detallito);
					filter_etiqueta();
				});			
			}else{
				console.log("sin etiquera");
			}
		});
		
		$("#idtipodocumento").change(function(){
			if($.isNumeric($(this).val())) {
				reload_combo("#serie", 
				{
					controller: "tipo_documento",
					method: "get_series", 
					data: "idtipodocumento="+$(this).val()
				}, function() {
					$("#serie").trigger("change");
				});
			}
			else {
				$("#serie").html("").trigger("change");
			}
		});
		
		$("#serie").change(function(){
			load_formato();
		});
		
		$('#btn-save').click(function(e){
			str = $("#form_format").serialize();
			str+= '&'+$("#form_opt").serialize();
			str+= '&contenido='+$contedor.html();
			str+= '&cantidad_filas_detalle='+$("#cant_elementos_detalle_lienzo").val();
			str+= '&ancho_celda_detalle='+$("#ancho_celda_detalle").val();
			
			if( $("#ver_borde").is(":checked") ){
				str+='&ver_borde=S';
			}else{
				str+='&ver_borde=N';
			}

			ajax.post({url: _base_url+_controller+"/save", data: str}, function(data) {
				if(data){
					ventana.alert({titulo: "En horabuena!", mensaje: "La plantilla se guardo correctamente", tipo:"success"}, function() {
						load_formato();
					});
				}
			});
		});
		
		$("#add-fila").click(function(e){
			tipo_contenido = 'f';
			$(id_content).html("<tr></tr>");
			filter_etiqueta();
			
			$("#panel_add_fila").modal('show');
		});
		
		$("#add-detalle").click(function(e){
			tipo_contenido = 'd';
			
			existe_detalle = true;
			// $('. table_detalle tbody tr').each(m,y){
				
			// }
			// console.log($('.table_detalle tbody tr').length);
			if($('.table_detalle tbody tr').length>1){
				existe_detalle = false;
			}
			
			if(existe_detalle){
				$(id_content).html("<tr></tr>");
				$(id_content_d_h).html("<tr></tr>");
				
				filter_etiqueta();
				$("#panel_add_detalle").modal('show');				
			}else{
				ventana.alert({titulo: "Hey..!", mensaje: "Ya existe el detalle en la plantilla, borra y crea uno nuevo", tipo:"warning"}, function() {
					
				});
			}
		});
		
		$('#btn-add-etiqueta').click(function(e){
			if( $('.idetiqueta').required() ){
				if($("#tipo_contenido").val()=='f'){
					$('.idetiqueta').trigger('change');
				}else{
					if($("#cant_elementos_detalle_lienzo").required())
						$('.idetiqueta').trigger('change');
				}
			}
		});
		
		////////////////////////////////////////////// OPCIONES DEL LIENZO /////////////////////////////////////
		$('#option').click(function(e){
			load_formato();
			$("#more_option").modal('show');
		});
		
		$('#more_option #aplicar').click(function(e){
			$("#lienzo").css({'width':$("#width_lienzo").val(),'height':$("#height_lienzo").val(),'font-size':$("#font_size_lienzo").val()});
			$("#more_option").modal('hide');
		});
		////////////////////////////////////////////// OPCIONES DEL LIENZO /////////////////////////////////////

		
		/*------------------------------- PARA LAS FILAS QUE CONTIENE LAS ETIQUETAS --------------------------------------*/
		$(document).on('click','.trash_fila',function(){
			$div = $(this).parent('.pull-right').parent('.panel-body').parent('.panel');
			$div.remove();
		});
		/*------------------------------- PARA LAS FILAS QUE CONTIENE LAS ETIQUETAS --------------------------------------*/
		
		////////////////// AGREGANDO FILAS //////////////////// 
		$("#panel_add_fila #aplicar").click(function(){
			detallito = "<div class='panel panel-default'>";
			detallito+= "	<div class='panel-body'>";
			detallito+= "		<div class='pull-right'>";
			detallito+= "			<a class='trash_fila' title='Eliminar Fila'><i class='fa fa-trash'></i></a>";
			detallito+= "		</div>";
			detallito+= 		$("#panel_add_fila .fila_generada").html();
			detallito+= "	<div>";
			detallito+= "<div>";

			/*Quitamos la clase content_temp para k no haya cruze con las tablas(FILAS) que seguimos generando*/
			detallito = detallito.replace('content_temp','');			
			/*Quitamos la clase content_temp para k no haya cruze con las tablas(FILAS) que seguimos generando*/
			
			$contedor.append(detallito);

			eventos();
			filter_etiqueta();
			$(".trash_colum_prev").hide();
			$("#panel_add_fila").modal('hide');
		});
		
		$("#panel_add_detalle #aplicar").click(function(){
			if( tipo_contenido == 'd' ){
				if( !$("#cant_elementos_detalle_lienzo").required() )
					return false;
				if( !$("#ancho_celda_detalle").required() )
					return false;
			}

			detallito = "<div class='panel panel-default'>";
			detallito+= "	<div class='panel-body'>";
			
			detallito+= "		<div class='pull-right'>";
			detallito+= "			<a class='trash_detalle' title='Eliminar Detalle'><i class='fa fa-trash'></i></a>";
			detallito+= "		</div>";

			x_tr ='';
			for(xx = 1; xx<=$('#cant_elementos_detalle_lienzo').val();xx++){
				x_tr+= '<tr>';
				$("#panel_add_detalle .fila_generada table tbody tr td").each(function(i,j){
					cad = $(j).html();
					x_tr+= '<td style="'+$(j).attr("style")+'" class="'+$(j).attr("class")+'" ajax-text="'+$(j).attr("ajax-text")+'" >';
					x_tr+= 	cad.replace($.trim($(j).text()), $.trim($(j).text()).slice(0,-1)+parseInt(xx)+'}' );
					x_tr+= '</td>';
				});
				x_tr+= '</tr>';
			}
			
			$("#panel_add_detalle .fila_generada table tbody").html(x_tr);
			$("#panel_add_detalle .fila_generada table thead").html($(".table_detalle thead").html());

			detallito+= 		$("#panel_add_detalle .fila_generada").html();

			detallito+= "	<div>";
			detallito+= "<div>";

			/*Quitamos la clase content_temp para k no haya cruze con las tablas(FILAS) que seguimos generando*/
			detallito = detallito.replace('content_temp','');
			/*Quitamos la clase content_temp para k no haya cruze con las tablas(FILAS) que seguimos generando*/
			
			$contedor.append(detallito);

			eventos();
			filter_etiqueta();
			
			$(".trash_colum_prev").hide();
			$("#panel_add_detalle").modal('hide');
		});
		////////////////// AGREGANDO FILAS //////////////////// 
		
		
		////////////////// EDITANDO ETIQUETAS DENTRO DE UNA FILA //////////////////// 
		$(document).on('click','.edit_option_etiqueta',function(){
			$td_etiqueta = $(this).parent('.pull-right').parent('div').parent('td');
			$("#editando_fila_etiqueta").html("Parametro de la etiqueta <b>"+$td_etiqueta.text()+"</b>");
			$("#sangria_etiqueta").val($td_etiqueta.find('.sangria_etiqueta').val());
			$("#ancho_etiqueta").val($td_etiqueta.find('.width_etiqueta_fila').val());
			$("#text_align_etiqueta").val($td_etiqueta.find('.align_etiqueta').val());
			$("#option_param_etiqueta").modal('show');
		});
		
		$('#option_param_etiqueta #aplicar').click(function(e){
			$td_etiqueta.css({'width':$("#ancho_etiqueta").val()});
			$td_etiqueta.children().css({'margin-left':$("#sangria_etiqueta").val(),"text-align":$("#text_align_etiqueta").val()});
			$td_etiqueta.find('.sangria_etiqueta').val($("#sangria_etiqueta").val());
			$td_etiqueta.find('.width_etiqueta_fila').val($("#ancho_etiqueta").val())
			$td_etiqueta.find('.align_etiqueta').val($("#text_align_etiqueta").val())
			$("#option_param_etiqueta").modal('hide');
		});
		
		$(document).on('click','.trash_colum_prev',function(){
			$clase = $(this).parent('.pull-right').parent('div').parent('td').attr('class');
			$tabla_colum = $(this).parent('.pull-right').parent('div').parent('td').parent('tr').parent('tbody').parent('table');
			$tabla_colum.find('td.'+$clase).remove();
			filter_etiqueta();
		});
		
		$(document).on('click','.edit_detalle',function(e){
			$("#option_param_detalle").find('input').val('');

			$clase = $(this).parent('.pull-right').parent('td').attr('class');
			$table_td  = $(this).parent('.pull-right').parent('td').parent('thead').find('table tbody');
			$table_ref = $(this).parent('.pull-right').parent('td').parent('tr').parent('thead').parent('table');
			texto_etiqueta = '';
			$table_ref.find('tbody tr td.'+$clase).each(function(i,j){
				texto_etiqueta = $(j).attr('ajax-text');
				return false;
			});

			// $("#editando_detalle_etiqueta").html("Parametro de la celda <b>"+$table_ref.find('tbody tr td.'+$clase).children().text()+"</b>");
			$("#editando_detalle_etiqueta").html("Parametro de la celda <b>"+texto_etiqueta+"</b>");
			$("#descripcion_celda_detalle").html(texto_etiqueta);
			if($("#cant_elementos_detalle_lienzo").required() && $("#ancho_celda_detalle").required()){
				$("#width_colum_detalle").val($table_ref.find('tbody tr td.'+$clase+' .width_etiqueta').val())
				$("#text_align_detalle").val($table_ref.find(' tbody tr td.'+$clase+' .align_etiqueta').val())

				$("#option_param_detalle").modal('show');
				$("#option_param_detalle input#text_align_detalle").focus();
			}
		});
		
		$("#option_param_detalle #aplicar").click(function(){
			bval = true && $("#text_align_detalle").required();
			bval = bval && $("#width_colum_detalle").required();
			
			if(bval){
				$table_ref.find('tr td.'+$clase).css({"text-align":$("#text_align_detalle").val(),"width":$("#width_colum_detalle").val(),"height":$("#ancho_celda_detalle").val()});
				$table_ref.find('tr td.'+$clase+' input.width_etiqueta').val($("#width_colum_detalle").val());
				$table_ref.find('tr td.'+$clase+' input.height_etiqueta').val($("#ancho_celda_detalle").val());
				$table_ref.find('tr td.'+$clase+' input.align_etiqueta').val($("#text_align_detalle").val());
				
				$("#option_param_detalle").modal('hide');
			}
		});
		////////////////// EDITANDO ETIQUETAS DENTRO DE UNA FILA //////////////////// 
		
		// $(document).on('click','.edit_etiqueta',function(){
			// $div = $(this).parent('.pull-right').parent('.panel-body').parent('.panel');
			// $("#width_etiqueta").val('');
			// $("#height_etiqueta").val('');
			
			// x_ver=$div.find('.ver_label').val();
			// if(x_ver=='S'){
				// $("#v_label").prop("checked",true);
			// }else{
				// $("#v_label").prop("checked",false);
			// }

			// $("#width_etiqueta").attr({'value':$div.find('.width_etiqueta').val()});
			// $("#height_etiqueta").attr({'value':$div.find('.height_etiqueta').val()});

			// $div.find('.ver_label').attr({'value':x_ver});
			// $("#label_seleccionado").html($div.find('.panel-body').text());
			// $("#option_detalle").modal('show');
		// });
		
		
		/*------------------------------------ EVENTOS PARA el detalle del formato --------------------------------------------*/
		$(document).on('click','.trash_detalle',function(){
			$div = $(this).parent('.pull-right').parent('.panel-body').parent('.panel');
			$div.remove();
			filter_etiqueta();
		});
		/*------------------------------------ EVENTOS PARA el detalle del formato --------------------------------------------*/
		
		$("#idtipodocumento").trigger('change');

		load_formato();
	});
	
	function load_formato(){
		if($.trim($("#serie").val())==''){
			$("#serie").html("<option value='0'></option>");
		}
		xstr = "idtipodocumento="+$("#idtipodocumento").val();
		xstr+= "&serie="+$("#serie").val();
		if($.trim($("#serie").val())!=''){
			ajax.post({url: _base_url+_controller+"/get_formato", data: xstr}, function(data) {
				$contedor.empty();
				if(data.formato){
					$contedor.html(data.formato.contenido);
					// $("#width_lienzo").attr("value",data.formato.width);
					// $("#height_lienzo").attr("value",data.formato.height);
					// $("#font_size_lienzo").attr("value",data.formato.font_size);
					// $("#cant_elementos_detalle_lienzo").attr("value",data.formato.cantidad_filas_detalle);
					// $("#ancho_celda_detalle").attr("value",data.formato.ancho_celda_detalle);
					
					$("#width_lienzo").val(data.formato.width);
					$("#height_lienzo").val(data.formato.height);
					$("#font_size_lienzo").val(data.formato.font_size);
					$("#fuente_letra_lienzo").val(data.formato.fuente_letra);
					$("#cant_elementos_detalle_lienzo").val(data.formato.cantidad_filas_detalle);
					$("#ancho_celda_detalle").val(data.formato.ancho_celda_detalle);
					
					if(data.formato.ver_borde=='S'){
						$("#ver_borde").prop("checked",true);
					}else{
						$("#ver_borde").prop("checked",false);
					}
					
				}else{
					$("#width_lienzo").val('');
					$("#height_lienzo").val('');
					$("#font_size_lienzo").val('');
					$("#cant_elementos_detalle_lienzo").val('');
					$("#ancho_celda_detalle").val('');
					$("#ver_borde").prop("checked",false);
				}
				eventos(true);
				filter_etiqueta();
			});
		}
	}
	
	function filter_etiqueta(){
		xstr = "idtipodocumento="+$("#idtipodocumento").val();
		xstr+= "&serie="+$("#serie").val();
		xstr+= "&tipo_contenido="+tipo_contenido;
		xstr+= "&"+$("#form_content").serialize();
		xstr+= "&"+$(".form_temp").serialize();
		
		if($.trim($("#serie").val())!=''){
			ajax.post({url: _base_url+_controller+"/data_etiqueta", data: xstr}, function(data) {
				option = "<option value=''>Seleccione..</option>";
				if(data){
					$(data).each(function(i,j){
						option+="<option value='"+j.idetiqueta+"'>"+j.label_impresion+"</option>";
					});
					$("#idetiqueta"+tipo_contenido).html(option);
				}else{
					$("#idetiqueta"+tipo_contenido).html(option);
				}
			});
		}
	}
	
	function eventos(destroy_resizable){
		destroy_resizable = destroy_resizable || false;
		$("#lienzo").css({'width':$("#width_lienzo").val(),'height':$("#height_lienzo").val(),'font-size':$("#font_size_lienzo").val()});
		$('.panel').draggable({
			cursor: "move",
			containment: "#lienzo",
			scroll: false
		});
	}
</script>