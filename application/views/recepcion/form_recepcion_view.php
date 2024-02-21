<form id="form_<?php echo $controller; ?>" class="app-form">
	<input type="hidden" name="idcompra" id="idcompra" value="<?php echo $idcompra; ?>">
	<input type="hidden" name="idproveedor" id="proveedor_idproveedor" value="<?php echo (!empty($pedido["idproveedor"])) ? $pedido["idproveedor"] : ""; ?>">

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">Detalle pedido</div>
				<div class="panel-body">
					
					<div class="table-responsive">
						<table id="tbl-detalle" class="table table-striped tooltip-demo detail-table">
							<thead>
								<tr>
									<!--<th><input type="checkbox" onClick="SeleccionarTodo"></th>-->
									<th align="center" style="width: 5%;">kardex</th>
									<th align="center" style="width: 10%;">Almacen</th>
									<th align="center" style="width: 54%;">Producto</th>
									<th align="center" style="width: 14%;">Tipo Documento</th>
									<th align="center" style="width: 5%;">Serie</th>
									<th align="center" style="width: 6%;">Numero</th>									
									<th align="center" style="width: 11%;">Fecha Registro</th>
									<th align="center" style="width: 5%;">Cant. Recep.</th>
									<th></th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<?php
								$ind = 0;
								$item ='';
								$can_sub_total=0;
								$total=0;
								if(!empty($kardex_det)) {
									foreach($kardex_det as $val){
										
										$fila='<tr>
													<!--<td><input type="checkbox" name ="checkall1 id="items_'.$ind.'" checked value="'.$val["correlativo"].'" onClick="SeleccionarTodo1('.$ind.');" /></td>-->											
													<td style="text-align: center;">'.$val["correlativo"].'
														<input type="hidden" name="kardex'.$ind.'" id="kardex'.$ind.'" value="'.$val["correlativo"].'">
													</td>
													<td>'.$val["almacen"].'
														<input type="hidden" name="alma'.$ind.'" id="alma'.$ind.'" value="'.$val["idalmacen"].'">
													</td>
													<td>'.$val["producto"].'
														<input type="hidden" name="produc'.$ind.'" id="produc'.$ind.'" value="'.$val["idproducto"].'">
													</td>
													<td>'.$val["tipo"].'
														<input type="hidden" name="tipo_docu'.$ind.'" id="tipo_docu'.$ind.'" value="'.$val["tipo_docu"].'">
													</td>
													<td>'.$val["serie"].'
														<input type="hidden" name="serie'.$ind.'" id="serie'.$ind.'" value="'.$val["serie"].'">
													</td>
													<td>'.$val["numero"].'
														<input type="hidden" name="numero'.$ind.'" id="numero'.$ind.'" value="'.$val["numero"].'">													
													</td>
													<td>'.$val["fecha_registro"].'</td>
													<td align="center">'.$val["cantidad"].'</td>';
													
										if($val['estado']=='A'){
											$fila.='<td><button class="btn btn-close btn-xs btn_close" data-toggle="tooltip" onClick="procesar_recepcion('.$ind.')" title="Procesar la Recepcion para que ingrese al Kardex"><i class="fa fa-trash"></i></button></td>';
										}
										$fila.='<td><button class="btn btn-danger btn-xs btn_deta_delete" data-toggle="tooltip" onClick="detele_recepcion('.$ind.')" title="Eliminar registro de Recepcion"><i class="fa fa-trash"></i></button></td>
												</tr>';
												
										$fila_vacia = '<tr style="background: mediumaquamarine; color: black;"><td colspan="8" style="font-size: 9pt;text-align: right;"><b>SUB TOTAL RECEPCION :</b></td><td align="center"><b>'.$can_sub_total.'</b></td><td></td><tr>';
										
										if($item!=''){
											if($item == $val["idproducto"]){
												echo $fila;
												$can_sub_total = $can_sub_total + $val["cantidad"];
											}else{
												echo $fila_vacia;
												$can_sub_total = 0;
												echo $fila;
												$item = $val["idproducto"];
												$can_sub_total = $can_sub_total + $val["cantidad"];
											}
										}else{
											
											echo $fila;
											$item = $val["idproducto"];
											$can_sub_total = $can_sub_total + $val["cantidad"];
											// $fila_vacia = '<tr><td colspan="7" style="font-size: 9pt;text-align: right; color: teal;"><b>SUB TOTAL RECEPCION </b></td><td colspan="2"> '.$can_sub_total.'</td><tr>';
										}
										
										$total = $total + $val["cantidad"];
										$ind++;
									}
									$fila_vacia = '<tr style="background: mediumaquamarine; color: black;"><td colspan="8" style="font-size: 9pt;text-align: right;"><b>SUB TOTAL RECEPCION :</b></td><td align="center"><b>'.$can_sub_total.'</b></td><td></td><tr>';
									echo $fila_vacia;
									
									echo '<tr style="color: black;"><td colspan="8" style="font-size: 9pt;text-align: right;"><b>TOTAL : </b></td><td align="center"><b>'.$total.'</b></td><td></td><tr>';
								}
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="form-group">
			<div class="col-lg-12">
				<button class="btn btn-sm btn-white btn_cancel" data-controller="<?php echo $controller; ?>">Cancelar</button>
				<button id="btn_save_compra" class="btn btn-sm btn-primary" data-controller="<?php echo $controller; ?>">Guardar</button>
			</div>
		</div>
	</div>
</form>