<div class="">
	<div class="col-sm-4">
		<div class="">
			<div class="">
				<div class="clients-list">
					<div class="full-height-scroll">
						<div class="table-responsive">
							<form id="parametros">
								<table class="table table-striped">
									<tr>
										<td width="20%">Fecha</td>
										<td width="65%">
											<div class="col-sm-6" style="padding-right: 0px;padding-left: 0px;">
												<input type="text" name="fechainicio" id="fechainicio" class="form-control input-xs" value="<?php echo date('d/m/Y'); ?>"   placeholder="yy/mm/YY" >
											</div>
											
											<div class="col-sm-6" style="padding-right: 0px;padding-left: 0px;">
												<input type="text" name="fechafin"  id="fechafin" class="form-control input-xs"  placeholder="yy/mm/YY" >
											</div>
										</td>
									</tr>

									<tr>
										<td><div id=""><select id="entidad" name="entidad" class="form-control input-xs"><option value="CLIENTE">Cliente</option><option value="USUARIO">Empleado</option></select></div></td>
										<td>
											<input type="hidden" name="idcliente" id="idcliente" class="form-control input-xs"  placeholder="" >
											<input type="text" name="cliente" id="cliente_recibo" class="form-control input-xs"  placeholder="Apellidos y Nombres" >
										</td>
									</tr>
									
									<tr>
										<td width="20%">R. Desde</td>
										<td width="65%">
											<div class="col-sm-4" style="padding-right: 0px;padding-left: 0px;">
												<input type="text" name="serie_d" id="serie_d" class="form-control input-xs" value=""   placeholder="001" >
											</div>
											
											<div class="col-sm-8" style="padding-right: 0px;padding-left: 0px;">											
												<input type="text" name="numero_d"  id="numero_d" class="form-control input-xs"  placeholder="000001" >
											</div>
										</td>
									</tr>
									
									<tr>
										<td width="20%">R. Hasta</td>
										<td width="65%">
											<div class="col-sm-4" style="padding-right: 0px;padding-left: 0px;">
												<input type="text" name="serie_h" id="serie_h" class="form-control input-xs" value=""   placeholder="002" >
											</div>
											
											<div class="col-sm-8" style="padding-right: 0px;padding-left: 0px;">											
												<input type="text" name="numero_h"  id="numero_h" class="form-control input-xs"  placeholder="000001" >
											</div>
										</td>
									</tr>
									
									<tr>
										<td><div id="">Moneda</div></td>
										<td>
											<?php echo $moneda;?>
										</td>
									</tr>
									
									<tr>
										<td><div id="">Tipo Pago</div></td>
										<td>
											<?php echo $tipopago;?>
										</td>
									</tr>
									
									<tr>
										<td><div id="">Sucursal:</div></td>
										<td>
											<select name="idsucursal" class="form-control input-xs" id="idsucursal" >
												<?php
													if ($idperfil=='1') {// ADMINISREADOR
												?>
												<option value="">[TODOS]</option>
												<?php		
													}

													foreach ($sucursal as $key => $value) {
														echo "<option value='{$value['idsucursal']}'>{$value['descripcion']}</option>";
													}
												?>
											</select>
										</td>
									</tr>
									
									<tr>
										<td><div id="">ORDENAR</div></td>
										<td>
											<select name="orden" class="form-control input-xs" id="orden" >
												<option value="fecha">FECHA</option>
												<option value="nro_recibo">RECIBO</option>
												<option value="entidad">REFERENCIA</option>
											</select>
										</td>
									</tr>
									
									<tr>
										<td colspan=2>
											<center>
												<button id="ver-pdf" class="btn btn-primary btn-sm" type="button"> <i class="fa fa-file-pdf-o"></i>&nbsp;&nbsp;Ver &nbsp;&nbsp;&nbsp;&nbsp;</button>
											</center>
										</td>
									</tr>
								</table>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-sm-8" style="">
		<iframe src="" 
                width="100%" 
                height="480px" 
                border='0' 
                frameborder='0' 
                scrolling="yes" 
                marginwidth="0" 
                marginheight="0"
                vspace="0" 
                hspace="0"
                id="cuadroReporte">            
        </iframe>
	</div>
</div>

<input type="hidden" id="" value="<?php echo date('d/m/Y') ?>"></input>

<script src="app/js/jquery-2.1.1.js"></script>
<script src="app/js/plugins/datapicker/bootstrap-datepicker.js"></script>
<script src="app/js/plugins/datapicker/bootstrap-datepicker.es.js"></script>
<link rel="stylesheet" type="text/css" href="app/css/plugins/datapicker/datepicker3.css">
<script type="text/javascript">
	$(function(){		
		$("#ver-pdf").click(function(e){
			e.preventDefault();
			bval = true;
			if( $("#serie_h").val()!='' ){
				bval = bval && $("#numero_h").required();
			}
			if(bval){
				str = $("#parametros").serialize();
				str+= "&moneda="+$( "#idmoneda option:selected" ).text();;
				str+= "&tipopago="+$( "#idtipopago option:selected" ).text();;
				str+= "&sucursal="+$( "#idsucursal option:selected" ).text();;
				$("#cuadroReporte").attr("src", _base_url +_controller+ "/imprimir?" + str);				
			}
		});
		$('#cliente_recibo').letras({'permitir':' '})
		$('#serie_d,#serie_h,#numero_d,#numero_h').numero_entero();
		
		$("#cliente_recibo").autocomplete({
			source: function( request, response ) {
				ajax.post({url: _base_url+$("#entidad").val()+"/autocomplete", data: "maxRows=50&startsWith="+request.term, dataType: 'json'}, function(data) {
					response( $.map( data, function( item ) {
						return {
							label: ((item.apellidos)? item.apellidos :'')+ " " + ((item.nombres)? item.nombres :'')
						   ,value:  ((item.apellidos)? item.apellidos :'')+ " " + ((item.nombres)? item.nombres :'')
						   ,nombres: (item.nombres)? item.nombres :''
						   ,apellidos: (item.apellidos)? item.apellidos :''
						   ,dni: (item.dni)? item.dni :''
						   ,ruc: (item.ruc)? item.ruc :''
						   ,id: item.idcliente
						}
					}));
				});
			},
			select: function( event, ui ) {
				if(ui.item) {
					$("#idcliente").val(ui.item.id);
				}
			}
		}).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
			var html = "";
			if($.trim(item.ruc)) {
				html += "<strong>RUC: "+item.ruc+"</strong>| ";
			}
			else if($.trim(item.dni)) {
				html += "<strong>DNI: "+item.dni+"</strong>| ";
			}
			// console.log(item.apellidos);
			// console.log(item.nombres);
			if($.trim(item.apellidos)){
				html += item.apellidos;
			}
			
			if($.trim(item.nombres)){
				html += " "+item.nombres;
			}
			
			return $( "<li>" )
			.data( "ui-autocomplete-item", item )
			.append( html )
			.appendTo( ul );
		};
	})

	$('#fechainicio,#fechafin').datepicker({
		todayBtn: "linked",
		keyboardNavigation: false,
		forceParse: false,
		autoclose: true,
		language: 'es',
	});
</script>

<style>
	#entidad{font-size:11px;}
</style>