
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
										<td width="35%">Fecha Liq. Inicio:</td>
										<td width="65%">
											<input name="fechainicio"  id="fechainicio" type="text" class="form-control input-xs" value="<?php echo date('01/m/Y'); ?>" placeholder=""/>
										</td>
									</tr>
									
									<tr>
										<td>Fecha Fin:</td>
										<td>											
											<input name="fechafin"  id="fechafin" type="text" class="form-control input-xs" value="<?php echo date('d/m/Y'); ?>" />
										</td>
									</tr>

									<tr>
										<td><div id="">Cobrador:</div></td>
										<td>
											<?php echo $usuario; ?>
										</td>
									</tr>
									
									<tr>
										<td><div id="">Zona:</div></td>
										<td>
											<select class="form-control input-xs" name="idzona_cartera" id="idzona_cartera"></select>
										</td>
									</tr>
									
									<tr>
										<td><div id="">Tipo Documento:</div></td>
										<td>
											<?php echo $tipodocumento;?>
										</td>
									</tr>
									
									<tr>
										<td><div id="">Tipo Liquidacion:</div></td>
										<td>
											<select id="tipo_liquidacion" name="tipo_liquidacion" class="form-control input-xs">
												<option value=0>[TODOS]</option>
												<option value=1>A cuenta</option>
												<option value=2>Completo</option>
											</select>
										</td>
									</tr>
									
									<tr>
										<td><div id="">En Pestaña</div></td>
										<td colspan=2>
											<div class="onoffswitch">
												<input type="checkbox" id="externo" class="onoffswitch-checkbox" value="1">
												<label class="onoffswitch-label" for="externo">
													<span class="onoffswitch-inner"></span>
													<span class="onoffswitch-switch"></span>
												</label>
											</div>
										</td>
									</tr>
									
									<tr>
										<td colspan=2>
											<center>
												<button id="ver-pdf" class="btn btn-primary btn-sm" type="button"><i class="fa fa-file-pdf-o"></i>&nbsp;&nbsp;Ver reporte &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
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
                height="400px" 
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
		cargar_zona();
		$("#idcobrador").change(function(){
			cargar_zona();
		})
		
		$("#ver-pdf").click(function(e){
			e.preventDefault();
			str = $("#parametros").serialize();
			str+= "&empleado="+$("#idcobrador option:selected").text();

			if($("#externo").is(":checked"))
				open_url_windows(_controller+"/imprimir?"+str);
			else
				$("#cuadroReporte").attr("src", _base_url +_controller+ "/imprimir?" + str);
		});
	})

	$('#fechainicio,#fechafin').datepicker({
		todayBtn: "linked",
		keyboardNavigation: false,
		forceParse: false,
		autoclose: true,
		language: 'es',
	});
	
	function cargar_zona(){
		str = "idcobrador="+$("#idcobrador").val();
		ajax.post({url: _base_url+"hojaruta/cargar_zonas/", data: $("#parametros").serialize()+"&order=false"}, function(res) {
			$("#idzona_cartera").empty();
			arr = res;
			combo = "<option value='0'>[TODOS]</option>";
			if(arr.length) {
				var html = '';
				item=1;
				// combo='';
				for(var i in arr) {
					idzona   = (arr[i].idzona);
					zona	 = (arr[i].zona_h);

					item++;
					
					combo+= "<option value='"+idzona+"'>"+zona+"</option>";
					
					$("#tabla_zonas tbody").append(html);
				}
			}
			$("#idzona_ref,#idzona_cartera").append(combo);
			// $("#idzona_ref").trigger("change");
		});
	}
</script>