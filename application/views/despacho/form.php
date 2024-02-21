<div style="display:none;"><?php echo $almacen; ?></div>
<form id="form_<?php echo $controller; ?>" class="app-form">
	<input type="hidden" name="iddespacho" id="iddespacho">
	<input type="hidden" name="idventa" id="idventa">

	<div class="row">
		<div class="col-sm-6">
			<div class="row">
				<div class="col-sm-12">
					<div class="form-group">
						<label class="required">Buscar Venta</label>
						<div class="input-group">
							<input type="text" name="venta" id="buscar_venta" class="form-control" required="">
							<span class="input-group-btn tooltip-demo">
								<button type="button" id="btn-buscar-venta" class="btn btn-outline btn-primary" data-toggle="tooltip" title="Buscar Venta">
									<i class="fa fa-search"></i>
								</button>								
							</span>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<label class="required">Tipo documento</label>
						<?php echo $tipodocumento; ?>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<label class="required">Serie</label>
						<input type="text" name="serie" id="serie" class="form-control" required="">
					</div>
				</div>
				
			</div>
			
		</div>
		
		<div class="col-sm-6">
			<div class="row">
				<div class="col-sm-12">
					<div class="form-group">
						<label class="required">Observaciones</label>
						<input type="text" name="observacion" id="observacion" class="form-control">
					</div>
				</div>
				
			</div>
			<div class="col-sm-6">
				<div class="form-group">
					<label class="required">Nro. documento</label>
					<input type="text" name="numero" id="numero" class="form-control">
				</div>
			</div>
			
		</div>
	</div>
	
	
	
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">Detalle despacho</div>
				<div class="panel-body">
					
					<div class="table-responsive">
						<table id="tbl-detalle" class="table table-striped tooltip-demo detail-table">
							<thead>
								<tr>
									<th style="width: 3%;"><input type="checkbox" id="check_all"></th>
									<th style="width: 37%;">Producto</th>
									<th style="width: 15%;">Almacen</th>
									<th style="width: 10%;">U.Medida</th>
									<th style="width: 8%;">Cantidad</th>
									<th style="width: 8%;">Cant. despachado</th>
									<th style="width: 8%;">Cant. Pendiente</th>
									<th style="width: 8%;">Despacho</th>									
									<th style="width: 3%;"></th>
									<th style="display:none;"></th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="form-group">
			<div class="col-sm-6 text-left">
				<button class="btn btn-sm btn-warning btn_cancel" data-controller="<?php echo $controller; ?>">Cancelar</button>
			</div>
			<div class="col-sm-6 text-right">
				<input type="submit" id="btn_save_despacho" class="btn btn-sm btn-primary" data-controller="<?php echo $controller; ?>" value="Guardar">
			</div>
		</div>
	</div>
</form>

<div id="modal-series" class="modal fade" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"></h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<div class="input-group"><input id="input-text-serie" placeholder="Ingrese la serie" class="input-sm form-control text-uppercase" type="text" />
							<span class="input-group-btn"><button id="btn-search-serie" type="button" class="btn btn-sm btn-white"><i class="fa fa-search"></i> Buscar</button></span></div>
					</div>
				</div>
				<div class="table-responsive div_scroll" style="max-height:300px;">
					<table id="table-serie" class="table table-striped">
						<tbody></tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="btn-close-serie" class="btn btn-primary">Cerrar</button>
			</div>
		</div>
	</div>
</div>