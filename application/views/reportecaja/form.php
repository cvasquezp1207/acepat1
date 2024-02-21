<?php $distant = '  '; ?>
<div class="">
	<div class="col-sm-4">
		<div class="">
			<div class="">
				<div class="">
					<div class="">
						<div class="table-responsive">
							<form id="parametros">
								<table class="table table-striped">
									<tr>
										<td>Fecha:</td>
										<td>
											<div class="input-group date">
												<input name="fecha"  id="fecha" type="text" class="form-control input-xs" value="<?php  echo (!empty($nacimiento)) ? date('d/m/Y',strtotime($nacimiento)) : date('d/m/Y'); ?>" placeholder="<?php echo date('d/m/Y'); ?>"/>
												<!--<span class="input-group-addon"><i class="fa fa-calendar"></i></span>-->
											</div>
										</td>
									</tr>

									<tr>
										<td>Pago:</td>
										<td>
											<?php echo $tipopago;?>
										</td>
									</tr>
									
									<tr>
										<td>Sucursal:</td>
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
										<td><div id="eti">Empleado:</div></td>
										<td>
											<!--<div class="input-group">
												<input name="idusuario" id="idusuario" type="text" class="form-control"/>
												<span class="input-group-addon"><i class="fa fa-user"></i></span>
											</div>-->
											<select name="idusuario" class="form-control input-xs" id="idusuario" >
												<option value="">[TODOS]</option>
											</select>
										</td>
									</tr>
									<!--
									<tr>
										<td><div>Ordenar por</div></td>
										<td>
											<select name="orden" class="form-control input-xs" id="orden" >
												<option value="doc">Comprobante</option>
												<option value="hora">Hora</option>
												<option value="referencia">Referencia</option>
												<option value="tipopago">Tipo Pago</option>
												<option value="monto">Monto</option>
											</select>
										</td>
									</tr>-->
									
									<tr>
										<td colspan=2>
											<center>
												<button id="button-detall-pdf"    class="btn btn-primary btn-sm" type="button">Detalle Ingreso Caja &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
											</center>
											<!--<center>
												<button id="button-notdetall-pdf" class="btn btn-primary btn-sm" type="button">Detalle NO Ingreso Caja</button>
											</center>-->
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
		<div class="">
			
						<div class="table-responsive">
							<table id="" class="tabla_modulos table table-striped" border='0'>
								<tbody>
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<?php 
											foreach ($monedaactiva as $key => $value) {
												echo '<td style="font-size:12px;text-align:right;width:12%;"><b class="money">'.$value['abreviatura'].'</b></td>';
											}
										?>
										<td>&nbsp;</td>
									</tr>

									<?php
										foreach ($tipomov as $key => $value) {
											echo "<tr class='tr'>";
											echo '	<td style="" class="td_padre"><b>'.$value['alias'].'</b></td>';
											echo '	<td colspan="'.(count($monedaactiva)+2).'">&nbsp;</td>';
											echo "</tr>";

											foreach ($conceptos as $k => $v) {
												if ($value['idtipomovimiento'] == $v['idtipomovimiento']) {
													echo "<tr class='tr' index='".$v['descripcion']."'>";
													echo '	<td style="padding:0px 0px 0px 15px;height:35px !important;" >'.$v['descripcion'].'</td>';
													echo "	<td style='padding:3px;'>". $distant."</td>";
													foreach ($monedaactiva as $key => $vv) {
														echo "<td style='padding:3px;height:35px !important;' ><div class='numero colum' id='saldo_".$v['idconceptomovimiento']."_".$vv['idmoneda']."' ajax-data=".$v['idconceptomovimiento']."-".$v['idtipomovimiento']."-".$vv['idmoneda']." money=".$vv['idmoneda']." ajax-simbolo=".$vv['simbolo'].">0.00</div></td>";
													}
													echo '<td><a class="modal-detalle" ajax-data='.$v['idconceptomovimiento'].' style="cursor:pointer;font-size:20px;"><i class="fa fa-search-plus"></i></a></td>';
													echo "</tr>";
												}
											}

											echo "<tr>";
											echo '	<td class="td_padre"><b>TOTAL '.$value['alias'].'</b></td>';
											echo '	<td >&nbsp;</td>';
											foreach ($monedaactiva as $key => $vvv) {
												echo "<td style='padding:3px;height:35px !important;' ><div class='bold numero subt' id='total_".$value['idtipomovimiento']."_".$vvv['idmoneda']."' ajax-data=".$value['idtipomovimiento']."-".$vvv['idmoneda'].">0.00</div></td>";
											}
											echo '	<td >&nbsp;</td>';
											echo "</tr>";
											
											echo "<tr>";
											echo "	<td colspan='".(count($monedaactiva)+3)."'><hr></hr></td>";
											echo "</tr>";
										}
									?>

									<tr class='tr'>
										<td style='padding:10px;'><b>SALDO TOTAL</b></td>
										<td style='padding:10px;'><?php echo  $distant;?></td>
										<?php
											foreach ($monedaactiva as $key => $vvv) {
												echo "<td style='padding:3px;height:35px !important;' ><div class='bold numero total' ajax-data=".$vvv['idmoneda']." id='total_".$vvv['idmoneda']."'>0.00</div></td>";
											}
										?>
										<td style='padding:10px;'>&nbsp;</td>
									</tr>

									<tr>
										<td colspan="<?php echo (count($monedaactiva)+3); ?>">
											<center>
												<button id="view" class="btn btn-primary btn-sm" type="button">Vista Previa</button>
											</center>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					
		</div>
	</div>
</div>

<div class="modal fade" id="modal" aria-labelledby="myLargeModalLabel" data-backdrop="static" >
	<div class="modal-dialog modal-lg" >
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">{Titulo}</h4>
			</div>
			<div class="modal-body">
							<?php $width_cols_table = array("5%", "14%","10%", "30%", "23%", "10%"); ?>
							<p>CONSULTAS DE {CONCEPTO} DEL <?php echo date('d/m/Y'); ?></p>
							<!-- tabla header -->
							<table class="table table-striped table-green m-b-n" style="width:calc(100% - 17px);">
								<thead>
									<th width="<?php echo $width_cols_table[0];?>">N&deg;</th>
									<th width="<?php echo $width_cols_table[1];?>">Doc</th>
									<th width="<?php echo $width_cols_table[2];?>">Hora</th>
									<th width="<?php echo $width_cols_table[3];?>">Referencia</th>
									<th width="<?php echo $width_cols_table[4];?>">Concepto</th>
									<?php
										foreach ($monedaactiva as $key => $vvv) {
											echo "<th width='".$width_cols_table[5]."'>Monto {$vvv['simbolo']}</th>";
										}
									?>
								</thead>
							</table>
							<!-- tabla body -->
							<div style="width:100%;height:150px;overflow-x:hidden;overflow-y:scroll;">
								<table class="table table-striped m-b-n">
									<thead>
										<th width="<?php echo $width_cols_table[0];?>"></th>
										<th width="<?php echo $width_cols_table[1];?>"></th>
										<th width="<?php echo $width_cols_table[2];?>"></th>
										<th width="<?php echo $width_cols_table[3];?>"></th>
										<th width="<?php echo $width_cols_table[4];?>"></th>
										<?php
											foreach ($monedaactiva as $key => $vvv) {
												echo "<th width='".$width_cols_table[5]."'></th>";
											}
										?>
									</thead>
									<tbody id='bodyI'></tbody>
								</table>
							</div>
							<!-- tabla footer -->
							<table class="table table-striped m-b-n" style="width:calc(100% - 17px);margin-bottom:10px">
								<tbody>
									<th width="<?php echo $width_cols_table[0];?>"></th>
									<th width="<?php echo $width_cols_table[1];?>"></th>
									<th width="<?php echo $width_cols_table[2];?>"></th>
									<th width="<?php echo $width_cols_table[3];?>"></th>
									<th width="<?php echo $width_cols_table[4];?>" class="text-right"><b>TOTAL</b></th>
									<?php
										foreach ($monedaactiva as $key => $vvv) {
											echo "<th width='".$width_cols_table[5]."'><div class='numero total_m' id='total_modal_".$vvv['idmoneda']."'>{$vvv['simbolo']} 0.00</div></th>";
										}
									?>
								</tbody>
							</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<input type="hidden" id="" value="<?php echo date('d/m/Y') ?>"></input>

<style type="text/css">
	.numero{
		text-align: right;
	}

	.bold{
		font-weight: bold;
	}

	.clients-list table tr td{
		padding:3px;height:38px !important;
	}
	.table thead th{color:white;}
	select[multiple], select[size] {
		height: auto;
	}
</style>

<script src="app/js/jquery-2.1.1.js"></script>

<script src="app/js/plugins/datapicker/bootstrap-datepicker.js"></script>
<script src="app/js/plugins/datapicker/bootstrap-datepicker.es.js"></script>
<link rel="stylesheet" type="text/css" href="app/css/plugins/datapicker/datepicker3.css">
<script type="text/javascript">
	var is_superuser = "<?php echo $es_superusuario;?>";
	$(function(){
		// dateChanged();
		// $("#idtipopago").attr("name","idtipopago[]");
		
		$('.modal-detalle').click(function(){
			tr_ = $(this).parent('td').parent('tr').find('td .numero.colum');
			$(".modal-title").html("LISTA DE "+($(this).parent('td').parent('tr').attr('index')).toUpperCase() );
			$(".modal-body p:first").html("CONSULTAS DE "+ ($(this).parent('td').parent('tr').attr('index')).toUpperCase()+" DEL "+$("#fecha").val()||$("#fecha_hoy").val());

			str = "idconceptomovimiento="+$(this).attr("ajax-data");
			str+= "&fecha="+$("#fecha").val();
			str+= "&idsucursal="+$("#idsucursal").val();
			str+= "&idusuario="+$("#idusuario").val();
			str+= "&idtipopago="+$.trim($("#idtipopago").val());
			str+= "&orden="+$("#orden").val();
			
			ajax.post({url: _base_url+_controller+"/return_filas", data: str, type:'html'}, function(res) {
				if (res) {
					$("#bodyI").html(res);
				}else{
					$("#bodyI").empty();
				}

				$(tr_).each(function(x,y){
					montillo = $(y).text();
					idmoney  = $(y).attr('money');
					simb  = $(y).attr('ajax-simbolo');
					$("#total_modal_"+idmoney).html(simb+' '+montillo);
				})
	
				$("#modal").modal('show');
			});
		});
		
		$("#idsucursal").change(function(){
			ajax.post({
				url: _base_url+_controller+"/return_cajero", 
				data: "idsucursal="+$(this).val()
			}, function(res) {
				options='';
				if(is_superuser=='S'){
					options+='<option value="">[TODOS]</option>';
				}
				if(res.length) {
					// var cant = res.length, options = '', fc = 'selected';
					fc = '';
					for(var i in res) {						
						options += '<option value="'+res[i].idusuario+'" '+fc+'>'+res[i].appat+' '+res[i].apmat+' '+res[i].nombres+'</option>';
					}

					$("#idusuario").html(options);
				}else{
					if($.trim(options)=='')
						options+='<option value="-1">[NO HAY COBRADOR]</option>';
					$("#idusuario").empty().html(options);
				}
				dateChanged();
			});
		});
		$("#idsucursal").trigger("change");
		$("#idusuario").change(function(){
			dateChanged();
		})

		$("#idtipopago").change(function(){
			dateChanged();
		});
		
		$("#button-detall-pdf").click(function(e){
			e.preventDefault();
			str = $("#parametros").serialize();
			str+= "&empleado="+$("#idusuario option:selected").text();
			str+= "&tipopago="+$("#idtipopago option:selected").text();
			str+= "&sucursal="+$("#idsucursal option:selected").text();
			str+= "&idusuario="+$("#idusuario").val();
			str+= "&id_tipopago="+$.trim($("#idtipopago").val());
			str+= "&idtipopago=";
			open_url_windows(_controller+"/imprimir?"+str+"&tipo=detallado");
		});
		
		$("#view").click(function(e){
			e.preventDefault();
			
			str = $("#parametros").serialize();
			str+= "&empleado="+$("#idusuario option:selected").text();
			str+= "&tipopago="+$("#idtipopago option:selected").text();
			str+= "&sucursal="+$("#idsucursal option:selected").text();
			str+= "&idusuario="+$("#idusuario").val();
			str+= "&id_tipopago="+$.trim($("#idtipopago").val());
			str+= "&idtipopago=";
			open_url_windows(_controller+"/imprimir?"+str+"&tipo=resumido");
		});
	})

	$('#fecha').datepicker({
		todayBtn: "linked",
		keyboardNavigation: false,
		forceParse: false,
		autoclose: true,
		language: 'es',
	}).change(dateChanged);
	
	function dateChanged(){
		$('.numero.colum').each(function(x,y){
			$arraysito = $(y).attr("ajax-data");
			res = $arraysito.split("-");
			str = "idmoneda="+res[2]+"&idconceptomovimiento="+res[0]+"&idtipomovimiento="+res[1];
			str+= "&fecha="+$("#fecha").val();
			str+= "&idsucursal="+$("#idsucursal").val();
			str+= "&idusuario="+$("#idusuario").val();
			str+= "&idtipopago="+$.trim($("#idtipopago").val());
			ajax.post({url: _base_url+_controller+"/recoger_data", data: str}, function(res) {
				$id = $(y).attr("id");
				montito = parseFloat(res.monto).toFixed(2);
				$("#"+$id).html(montito);
			});
		});
		
		total_monto = 0;
		
		$(".numero.subt").each(function(m,n){
			var vari_array = $(n).attr("ajax-data");
			
			res = vari_array.split("-");
			str = "idmoneda="+res[1]+"&idtipomovimiento="+res[0];
			str+= "&fecha="+$("#fecha").val();
			str+= "&idsucursal="+$("#idsucursal").val();
			str+= "&idusuario="+$("#idusuario").val();
			str+= "&idtipopago="+$.trim($("#idtipopago").val());
			ajax.post({url: _base_url+_controller+"/recoger_subtotal", data: str}, function(res) {
				$id = $(n).attr("id");
				montito = parseFloat(res.monto).toFixed(2);
				$("#"+$id).html(montito);
			});
		});

		$('.numero.total').each(function(i,j){
			var idmoneda = $(j).attr("ajax-data");
			str = "idmoneda="+idmoneda
			str+= "&fecha="+$("#fecha").val();
			str+= "&idsucursal="+$("#idsucursal").val();
			str+= "&idusuario="+$("#idusuario").val();
			str+= "&idtipopago="+$.trim($("#idtipopago").val());

			ajax.post({url: _base_url+_controller+"/recoger_total", data: str}, function(res) {
				$id = $(j).attr("id");
				montito = parseFloat(res.monto).toFixed(2);
				$("#"+$id).html(montito);
			});
		})
	}
</script>