<?php $distant = '  '; ?>
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
										<td width="35%">Fecha visita Inicio:</td>
										<td width="65%">
											<div class="input-group date">
												<input name="fechainicio"  id="fechainicio" type="text" class="form-control input-xs" value="<?php echo date('01/m/Y'); ?>" placeholder=""/>
												<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
											</div>
										</td>
									</tr>
									
									<tr>
										<td>Fecha Fin:</td>
										<td>											
											<div class="input-group date">
												<input name="fechafin"  id="fechafin" type="text" class="form-control input-xs" value="<?php echo date('d/m/Y'); ?>" />
												<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
											</div>
										</td>
									</tr>

									<tr>
										<td><div id="eti">Cobrador:</div></td>
										<td>
											<?php echo $usuario; ?>
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
		$("#idcobrador").change(function(){
			dateChanged();
		})
		
		$("#ver-pdf").click(function(e){
			e.preventDefault();
			str = $("#parametros").serialize();
			str+= "&empleado="+$("#idcobrador option:selected").text();
			// str+= "&fechainicio="+$("#fechainicio").val();
			// str+= "&fechafin="+$("#fechafin option:selected").text();
			// str+= "&sucursal="+$("#idsucursal option:selected").text();
			// // open_url_windows(_controller+"/imprimir?"+str);
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
</script>