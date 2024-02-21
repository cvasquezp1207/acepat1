<form id="form-param-comision">
	<div class="row">
		<div class="col-sm-6">
			<div class="form-group">
				<label class="control-label">Nombre</label>
				<input name="nombre" id="nombre" class="form-control" value="<?php echo (!empty($comision)) ? $comision['nombre'] : ""; ?>" type="text">
			</div>
		</div>
		<div class="col-sm-3">
			<label class="control-label">Fecha Inicio</label>
				<input type="text" name="fecha_inicio" id="fecha_inicio" class="form-control" value="<?php echo (!empty($comision)) ? fecha_es($comision['fecha_inicio']) : date('d/m/Y'); ?>"   placeholder="d/m/Y" />	
		</div>
		<div class="col-sm-3">
			<label class="control-label">Fecha Fin</label>
				<input type="text" name="fecha_fin" id="fecha_fin" class="form-control" value="<?php echo (!empty($comision)) ? fecha_es($comision['fecha_fin']) : date('d/m/Y'); ?>" placeholder="d/m/Y" />
		</div>
	</div>
	
	<div class="row">
		<div class="col-sm-3">
			<div class="form-group">
				<label class="control-label">Empresa</label>
				<?php echo $empresa;?>
			</div>
		</div>
		<div class="col-sm-3">
			<label class="control-label">Sucursal</label>
			<?php echo $sucursal;?>
		</div>
		<div class="col-sm-3">
			<div class="form-group">
				<label class="control-label">Marca</label>
				<?php echo $marca;?>
			</div>
		</div>
		<div class="col-sm-3">
			<div class="form-group">
				<label class="control-label">Rango dias</label>
				<div class="input-group">
					<?php echo $rangodias;?>
					<span class="input-group-btn tooltip-demo">
						<button id="btn-new-rango" class="btn btn-info btn-sm" data-toggle="tooltip" title="Registrar"><i class="fa fa-file"></i></button>
						<button id="btn-del-rango" class="btn btn-default btn-sm" data-toggle="tooltip" title="Eliminar"><i class="fa fa-times"></i></button>
					</span>
				</div>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-sm-12">
			<div class="form-group">
				<button id="btn-add-comision" class="btn btn-info btn-block btn-sm"><i class="fa fa-arrow-down"></i> Agregar a la tabla</button>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-sm-12">
			<table id="table-comision" class="table table-striped">
				<thead>
					<tr>
						<th class="marca">Marca</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>
	
	<div class="row">
		<div class="col-sm-6">
			<div class="form-group">
				<a class="btn btn-sm btn-white" href="<?php echo base_url("comisiones");?>"><i class="fa fa-arrow-left"></i> Regresar</a>
				<button id="btn_save" class="btn btn-sm btn-primary" >Guardar</button>
			</div>
		</div>
	</div>
</form>

<form id="modal-rango" class="modal fade" data-keyboard="false" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-sm" >
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Registrar rango de d&iacute;as</h4>
			</div>
			
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							<label class="control-label">Desde</label>
							<input type="text" id="dias_min" name="dias_min" class="form-control input-sm">
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							<label class="control-label">Hasta</label>
							<input type="text" id="dias_max" name="dias_max" class="form-control input-sm">
						</div>
					</div>
				</div>
			</div>
			
			<div class="modal-footer">
				<button type="button" class="btn btn-sm btn-white" data-dismiss="modal">Cancelar</button>
				<button id="btn-save-rango" class="btn btn-sm btn-primary">Guardar</button>
			</div>
		</div>
	</div>
</form>
<!--<script src="app/js/jquery-2.1.1.js"></script>
<script src="app/js/plugins/datapicker/bootstrap-datepicker.js"></script>
<script src="app/js/plugins/datapicker/bootstrap-datepicker.es.js"></script>
<link rel="stylesheet" type="text/css" href="app/css/plugins/datapicker/datepicker3.css">-->
