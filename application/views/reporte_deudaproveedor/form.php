<div class="">
	<div class="col-sm-4">
		<div class="">
			<div class="">
				<div class="clients-list" style="margin-top:0px;">
					<div class="">
						<div class="table-responsive">
							<form id="parametros">
								<table class="table table-striped" border=0>
									<tr>
										<td width="31%">Fecha</td>
										<td width="">
											<input type="text" name="fechainicio" id="fechainicio" class="form-control input-xs" value="<?php echo date('d/m/Y'); ?>"   placeholder="d/m/Y" >
										</td>

										<td width="">
											<input type="text" name="fechafin" id="fechafin" class="form-control input-xs"  placeholder="d/m/Y" >
										</td>
									</tr>
									
									<tr>
										<td><div class>Proveedor</div></td>
										<td colspan=2>
											<?php echo $proveedor;?>
										</td>
									</tr>
									
									<tr>
										<td><div id="">Comprobante</div></td>
										<td colspan=2>
											<?php echo $comprobante;?>
										</td>
									</tr>
									
									<tr>
										<td><div id="">&nbsp;</div></td>
										<td>
											<label>Serie</label>
											<input class="form-control input-xs" id="serie" name="serie" placeholder='001'>
										</td>
										<td>
											<label>Numero</label>
											<input class="form-control input-xs" id="correlativo" name="correlativo" placeholder='000001' >
										</td>
									</tr>
									<!--
									-->
									
									<tr>
										<td><div class="">Moneda</div></td>
										<td colspan=2>
											<?php echo $moneda;?>
										</td>
									</tr>
						
									<tr>
										<td><div id="">Sucursal</div></td>
										<td colspan=2>
											<select name="idsucursal" class="form-control input-xs" id="idsucursal" >
												<?php
												if($all_sucursal=='S'){
												?>
												<option value="">[TODAS LAS SUCURSALES]</option>
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
										<td><div id="">Mostrar</div></td>
										<td colspan=2>
											<select name="ver" id="ver" class="form-control input-xs">
												<option value="R">RESUMIDO</option>
												<option value="D">DETALLADO</option>
											</select>
										</td>
									</tr>
									
									<tr>
										<td><div id="">Estado</div></td>
										<td colspan=2>
											<select name="pagado" id="pagado" class="form-control input-xs">
												<option value="N">PENDIENTE</option>
												<option value="S">PAGADOS</option>
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
												<!--
												<button id="ver-excel" class="btn btn-primary btn-sm" type="button"> <i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;Exportar &nbsp;&nbsp;&nbsp;&nbsp;</button>
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

<style>
	.chosen-container{font-size:10.5px !important;}
	.chosen-container-single .chosen-single{min-height: 24px !important;}
	.clients-list table tr td{height:auto;}
	table > tbody > tr >td{padding:0px;}
</style>