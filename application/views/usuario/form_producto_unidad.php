<form id="form_unidad_medida" class="app-form">
	<input type="hidden" name="idproducto" id="uni_idproducto" value="<?php echo (isset($producto["idproducto"])) ? $producto["idproducto"]:""; ?>">
	<div class="row">
		<div class="col-sm-8 m-b-xs">
			<select id="unidad_medidad_filtro" class="input-sm form-control input-s-sm inline"></select>
		</div>
		<div class="col-sm-4">
			<div class="btn-group">
				<button id="btn-add-unidad" class="btn btn-sm btn-white parent" type="button">Agregar item</button>
			</div>
		</div>
	</div>
	<div class="clients-list">
		<div class="full-height-scroll">
			<div class="table-responsive">
				<table id="tabla_unidad_medida" class="tabla_modulos table table-striped">
					<thead>
						<tr>
							<th>Unidad medida</th>
							<th>Cantidad</th>
							<th class="tooltip-demo">Equivalencia (<a data-toggle="tooltip" title="<?php echo (isset($unidad["descripcion"])) ? $unidad["descripcion"]:""; ?>"><?php echo (isset($unidad["descripcion"])) ? $unidad["abreviatura"]:""; ?></a>)</th>
							<th>&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						<?php
						if(!empty($producto_unidad)) {
							foreach($producto_unidad as $val){
								echo '<tr data-idunidad="'.$val["idunidad"].'">';
								echo '<td><input type="hidden" name="idunidad[]" class="idunidad" value="'.$val["idunidad"].'">'.$val["descripcion"].' ('.$val["abreviatura"].')</td>';
								echo '<td><input type="text" name="cantidad_unidad[]" class="cantidad_unidad form-control input-sm" value="'.$val["cantidad_unidad"].'" readonly></td>';
								echo '<td><input type="text" name="cantidad_unidad_min[]" class="cantidad_unidad_min form-control input-sm" value="'.$val["cantidad_unidad_min"].'"></td>';
								echo '<td><button type="button" class="btn btn-default btn-xs btn_delete_unidad_medida">Eliminar</button></td>';
								echo '</tr>';
							}
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="form-group">
			<div class="col-lg-4">
				<button id="uni_btn_cancel" class="btn btn-sm btn-white btn_cancel<?php echo $modal?" modal-form":""; ?>" data-controller="<?php echo $controller; ?>">Cancelar</button>
				<?php if($permiso->nuevo == 1 && $permiso->editar == 1) { ?>
				<button type="submit" id="uni_btn_save" class="btn btn-sm btn-primary" data-controller="<?php echo $controller; ?>">Guardar</button>
				<?php } ?>
			</div>
		</div>
	</div>
</form>
<script>
var UNIDAD_MEDIDA = <?php echo json_encode($unidades); ?>;
</script>