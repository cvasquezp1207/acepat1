<div class="">
	<div class="col-sm-4">
		<div class="">
			<div class="">
				<div class="clients-list">
					<div class="">
						<div class="table-responsive">
							<form id="parametros">
								<table class="table table-striped">
									<tr>
										<td colspan=2><div id="">Cliente:</div></td>
										<td colspan=3>
											<?php echo $cliente;?>
										</td>
									</tr>
									
									<tr>
										<td colspan=2><div id="">Ruta:</div></td>
										<td colspan=3>
											<?php echo $ruta;?>
										</td>
									</tr>
									
									<tr>
										<td colspan=2><div id="">Localidad:</div></td>
										<td colspan=3>
											<select name="idzona" id="idzona" class="form-control input-xs">
												<option value="">TODOS</option>
											</select>
										</td>
									</tr>
									
									<tr>
										<td colspan=2><div id="">Tipo</div></td>
										<td colspan=3>
											<select name="tipo" id="tipo" class="form-control input-xs">
												<option value="">TODOS</option>
												<option value="N">NATURAL</option>
												<option value="J">JURIDICO</option>
											</select>
										</td>
									</tr>
									
									<tr>
										<td width='230px' colspan=2>Sin Dirección</td>
										<td colspan=3>
											<div class="onoffswitch">
												<input type="checkbox" id="sin_direccion" class="onoffswitch-checkbox" />
												<label class="onoffswitch-label" for="sin_direccion">
													<span class="onoffswitch-inner"></span>
													<span class="onoffswitch-switch"></span>
												</label>
											</div>
										</td>
									</tr>
									
									<tr>
										<td colspan=2>Sin Teléfono</td>
										<td colspan=3>
											<div class="onoffswitch">
												<input type="checkbox" id="sin_telefono" class="onoffswitch-checkbox" />
												<label class="onoffswitch-label" for="sin_telefono">
													<span class="onoffswitch-inner"></span>
													<span class="onoffswitch-switch"></span>
												</label>
											</div>
										</td>
									</tr>
									
									<tr>
										<td colspan=2>Sin RUC</td>
										<td colspan=3>
											<div class="onoffswitch">
												<input type="checkbox" id="sin_ruc" class="onoffswitch-checkbox" />
												<label class="onoffswitch-label" for="sin_ruc">
													<span class="onoffswitch-inner"></span>
													<span class="onoffswitch-switch"></span>
												</label>
											</div>
										</td>
									</tr>
									
									<tr>
										<td colspan=2>Sin DNI</td>
										<td colspan=3>
											<div class="onoffswitch">
												<input type="checkbox" id="sin_dni" class="onoffswitch-checkbox" />
												<label class="onoffswitch-label" for="sin_dni">
													<span class="onoffswitch-inner"></span>
													<span class="onoffswitch-switch"></span>
												</label>
											</div>
										</td>
									</tr>
									
									<tr>
										<td colspan=2>Sin E-mail</td>
										<td colspan=3>
											<div class="onoffswitch">
												<input type="checkbox" id="sin_email" class="onoffswitch-checkbox" />
												<label class="onoffswitch-label" for="sin_email">
													<span class="onoffswitch-inner"></span>
													<span class="onoffswitch-switch"></span>
												</label>
											</div>
										</td>
									</tr>
									
									<tr>
										<td colspan=2><div id="">Mostrar</div></td>
										<td colspan=3>
											<select name="ver" id="ver" class="form-control input-xs">
												<option value="R">RESUMIDO</option>
												<option value="D" selected>DETALLADO</option>
											</select>
										</td>
									</tr>
									
									<tr>
										<td colspan=2><div id="">En Pestaña</div></td>
										<td colspan=3>
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
										<td colspan=4>
											<center>
												<button id="ver-pdf" class="btn btn-primary btn-sm" type="button"> <i class="fa fa-file-pdf-o"></i>&nbsp;&nbsp;Ver &nbsp;&nbsp;&nbsp;&nbsp;</button>
												<button id="ver-excel" class="btn btn-primary btn-sm" type="button"> <i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;Exportar &nbsp;&nbsp;&nbsp;&nbsp;</button>
												<!--
												-->
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
	});
</script>

<style>
	.chosen-container{font-size:10.5px !important;}
	.chosen-container-single .chosen-single{min-height: 24px !important;}
	.clients-list table tr td{height:auto;}
	table > tbody > tr >td{padding:0px;}
</style>