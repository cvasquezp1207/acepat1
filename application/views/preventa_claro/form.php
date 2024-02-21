<div id="form-preventas_claro" class="app-form">
	<div class="panel panel-info">
		<div class="panel-heading">
			<h3 class="panel-title">Preventas claro <small> | Consultas</small></h3>
		</div>
		<div class="panel-body">
			<div class="row">
				<div class="col-sm-9">
					<div class="row">
						<div class="col-sm-3">
							<div class="form-group">
								<label>Vendedor</label>
								<div class="input-group">
									<?php echo $comboempleado;?>
									<span class="input-group-btn">
										<button type="button" class="btn btn-primary btn-xs btn-search-vendedor" tabindex="-1" data-toggle="tooltip" title="Buscar otros empleados">
											<i class="fa fa-search" aria-hidden="true"></i>
										</button>
									</span>
								</div>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<label>Estado</label>
								<select id="pendiente" name="pendiente" class="form-control input-xs">
									<option value="S">PENDIENTE</option>
									<option value="N">ATENDIDO</option>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row margin-top-15">
				<div class="col-sm-12">
					<h4 class="example-title">Importar archivo</h4>
					<div class="row">
						<div class="col-sm-3">
							<input type="file" name="file" id="file" accept=".xls,.xlsx" style="display:none;">
							<div class="form-group tooltip-demo">
								<div class="input-group input-group-xs input-group-file" title="hla">
									<span class="input-group-addon"><i class="fa fa-paperclip"></i></span>
									<input type="text" class="form-control input-xs" id="file_nombre" placeholder="Ningun archivo seleccionado" readonly>
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<button type="button" id="btn-upload" class="btn btn-primary btn-xs"><i class="fa fa-upload"></i> Subir archivo y procesar</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row margin-top-10">
				<div class="col-sm-12">
					<h4 class="example-title">Lista de preventas</h4>
					<div class="row">
						<div class="col-sm-6">
							<div class="btn-group">
								<button type="button" id="btn-ver" class="btn btn-xs btn-white"><i class="fa fa-eye"></i> Ver</button>
								<button type="button" id="btn-send" class="btn btn-xs btn-white"><i class="fa fa-send-o"></i> Enviar a preventa</button>
								<button type="button" id="btn-delete" class="btn btn-xs btn-white"><i class="fa fa-trash-o"></i> Eliminar</button>
							</div>
						</div>
						<div class="col-sm-3 col-sm-offset-3 text-right">
							<div class="form-group">
								<div class="input-group">
									<input type="text" class="form-control input-xs" id="txtSearch" placeholder="Buscar en la tabla">
									<span class="input-group-btn">
										<button type="button" class="btn btn-primary btn-xs btn-search-txt" tabindex="-1">
											<i class="fa fa-search" aria-hidden="true"></i>
										</button>
									</span>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12 tooltip-demo">
							<table class="table table-striped table-hover table-dark table-preventa">
								<thead class="min-table">
									<tr>
										<th style="width:50px;">Item</th>
										<th style="width:150px;">Fecha</th>
										<th>Cliente</th>
										<th style="width:70px;">Moneda</th>
										<th style="width:70px;">Total</th>
										<th style="width:100px;">Documento</th>
										<th>Productos</th>
										<th>Vendedor</th>
										<th class="text-center" style="width:35px;">&nbsp;</th>
										<th class="text-center" style="width:35px;"><input type="checkbox" id="checkAll"></th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="modal-ver-preventa" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Consultar datos preventa</h4>
			</div>
			<div class="modal-body">
				<input type="hidden" id="idpreventa_claro">
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label class="control-label">Fecha y hora</label>
							<p class="input-control-static fecha"></p>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label class="control-label">Documento</label>
							<p class="input-control-static documento"></p>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label class="control-label">Cliente</label>
							<p class="input-control-static cliente"></p>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label class="control-label">Vendedor</label>
							<p class="input-control-static vendedor"></p>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<table class="table table-striped table-detalle-preventa">
							<thead class="min-table">
								<tr>
									<th style="width:35px;">Item</th>
									<th>Producto</th>
									<th style="width:56px;">U.Med.</th>
									<th style="width:52px;">Cant.</th>
									<th style="width:60px;">Precio</th>
									<th style="width:60px;">Total</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-offset-3 col-sm-3">
						<div class="form-group">
							<label class="control-label">Subtotal</label>
							<p class="input-control-static subtotal"></p>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group">
							<label class="control-label">IGV</label>
							<p class="input-control-static igv"></p>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group">
							<label class="control-label">Total</label>
							<p class="input-control-static total"></p>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-white btn-sm" data-dismiss="modal">Cerrar</button>
				<button type="button" id="btn-make-preventa" class="btn btn-primary btn-sm">Enviar a preventa</button>
			</div>
		</div>
	</div>
</div>

<style>
.input-control-static {
	border: solid 1px #e5e6e7;
	padding: 2px 0 2px 5px;
    margin: 0;
	font-size: .9em;
}
</style>