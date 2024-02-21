<form id="form_<?php echo $controller; ?>" class="app-form tooltip-demo">
	<input type="hidden" name="idproveedor" id="idproveedor" value="">
	<input type="hidden" name="idproducto" id="idproducto" value="">
	
	<div class="row">
		<div class="col-sm-6">
			<div class="form-group">
				<label>Proveedor</label>
				<div class="input-group">
					<input type="text" name="proveedor" id="proveedor_descripcion" class="form-control">
					<span class="input-group-addon">
						<input type="checkbox" id="all_proveedor" data-toggle="tooltip" title="Todos" style="vertical-align:bottom;">
					</span>
				</div>
			</div>
		</div>
		<div class="col-sm-3">
			<div class="form-group">
				<label>Almacen:</label>
				<?php  echo $almacen_i; ?>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-sm-3">
			<div class="form-group">
				<label>Fecha inicio</label>
				<div class="input-group date">
					<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					<input type="text" name="fecha_i" id="fecha_i" class="form-control" placeholder="dd/mm/aaaa">
				</div>
			</div>
		</div>
		<div class="col-sm-3">
			<div class="form-group">
				<label>Fecha fin</label>
				<div class="input-group date">
					<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					<input type="text" name="fecha_f" id="fecha_f" class="form-control" placeholder="dd/mm/aaaa">
				</div>
			</div>
		</div>
		<div class="col-sm-3">
			<div class="form-group">
				<fieldset>
					<legend style="font-size: 13px; max-width: 100%; font-weight: 700;">Formato</legend>
					<label><input type="radio" name="opc_tipo" value="1" checked /> PDF</label>
					<label style="margin-left: 20px;"><input type="radio" name="opc_tipo" value="2" /> Excel</label>
				</fieldset>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-sm-6">
			<div class="form-group">
				<label>Producto</label>
				<div class="input-group">
					<input type="text" name="producto" id="producto_descripcion" class="form-control">
					<span class="input-group-addon">
						<input type="checkbox" id="all_producto" data-toggle="tooltip" title="Todos" style="vertical-align:bottom;">
					</span>
				</div>
			</div>
		</div>
		<div class="col-sm-3">
			<div class="form-group">
				<button id="btn_generar_report" class="btn btn-sm btn-primary" data-controller="<?php echo $controller; ?>">Generar Archivo</button>
			</div>
		</div>
	</div>
</form>

