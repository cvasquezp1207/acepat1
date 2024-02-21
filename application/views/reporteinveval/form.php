<div class="">
	<div class="col-sm-4">
		<div class="">
			<div class="">
				<div class="clients-list" style="margin-top:0px;">
					<div class="">
						<div class="table-responsive">
							<form id="parametros">
								<table class="table table-striped">
									<tr>
										<td width="25%">Fecha</td>
										<td width="">
											<input type="text" name="fechainicio" id="fechainicio" class="form-control input-xs" value="<?php echo date('d/m/Y'); ?>"   placeholder="d/m/Y" >
										</td>

										<td width="">
											<input type="text" name="fechafin" id="fechafin" class="form-control input-xs"  placeholder="d/m/Y"  value="<?php echo date('d/m/Y'); ?>" readonly >
										</td>
									</tr>
																		
									
									<tr>
										<td><div id="">Categoria</div></td>
										<td colspan=2>
											<?php echo $categoria;?>
										</td>
									</tr>
									
									<tr>
										<td><div id="">Marca</div></td>
										<td colspan=2>
											<?php echo $marca;?>
										</td>
									</tr>
									
									<tr>
										<td><div id="">Modelo</div></td>
										<td colspan=2>
											<?php echo $modelo;?>
										</td>
									</tr>
									
									<!--<tr>
										<td><div id="">Producto</div></td>
										<td colspan=2>
											<?php echo $producto;?>
										</td>
									</tr>
									-->
									
									<tr>
										<td><div id="">Sucursal:</div></td>
										<td colspan=2>
											<select name="idsucursal" class="form-control input-xs" id="idsucursal" >
												<?php
													if ($control_reporte=='S') {
													
													}

													foreach ($sucursal as $key => $value) {
														echo "<option value='{$value['idsucursal']}'>{$value['descripcion']}</option>";
													}
												?>
											</select>
										</td>
									</tr>
									
									<tr>
										<td><div id="">Mostrar</div></td>
										<td colspan=2>
											<select name="ver" id="ver" class="form-control input-xs">
												<option value="R">RESUMIDO</option>
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
										<td colspan=3>
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
                height="980px" 
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
			str = $("#parametros").serialize();
			str+= "&sucursal="+$("#idsucursal").val();
			str+= "&cliente="+$("#idcliente option:selected").text();
			// str+= "&fechainicio="+$("#fechainicio").val();
			// str+= "&fechafin="+$("#fechafin option:selected").text();
			// str+= "&sucursal="+$("#idsucursal option:selected").text();
			// // open_url_windows(_controller+"/imprimir?"+str);
			if($("#externo").is(":checked"))
				open_url_windows(_controller+"/imprimir?"+str);
			else
				$("#cuadroReporte").attr("src", _base_url +_controller+ "/imprimir?" + str);
		});
		
		$("#ver-excel").click(function(e){
			e.preventDefault();
			str = $("#parametros").serialize();
			str+= "&sucursal="+$("#idsucursal").val();
			str+= "&cliente="+$("#idcliente option:selected").text();
			open_url_windows(_controller+"/exportar?"+str);
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

<style>
	.chosen-container{font-size:10.5px !important;}
	.chosen-container-single .chosen-single{min-height: 24px !important;}
	.clients-list table tr td{height:auto;}
	table > tbody > tr >td{padding:0px;}
</style>