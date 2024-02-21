<div class="row wrapper border-bottom white-bg page-heading fixed-button-top form-inline tooltip-demo">
	<div class="col-md-12">
		<form id="form-data">
			<input type="hidden" name="idproducto" id="idproducto">
			<label>Producto</label>
			<div class="input-group input-group-xs">
				<input type="text" class="input-xs form-control" id="producto" autocomplete="off" style="width:300px;">
				<span class="input-group-addon">
					<input type="checkbox" id="all_producto" data-toggle="tooltip" data-placement="bottom" title="Todos" style="vertical-align:bottom;">
				</span>
			</div>
			<label style="margin-left:10px;">Sucursal</label>
			<?php echo $sucursal;?>
			<div style="display:inline-block;margin-left:10px;">
				<button type="button" id="btnsearch" class="btn btn-white btn-sm"><i class="fa fa-search"></i> Consultar</button>
				<button type="button" id="btnpdf" class="btn btn-success btn-sm"><i class="fa fa-print"></i> Generar PDF</button>
				<button type="button" id="btnexcel" class="btn btn-primary btn-sm"><i class="fa fa-download"></i> Generar Excel</button>
			</div>
		</form>
	</div>
</div>
<div class="row">
	<div class="col-md-12" style="padding:0;">
		<!-- datos del credito -->
		<div class="ibox float-e-margins" style="margin-bottom: 0px;">
			<div class="ibox-content">
				<div class="table-responsive">
					<table id="tabla-result" class="table table-bordered" style="width: 100%;margin-bottom:0;">
						<thead>
							<tr>
								<th>Producto</th>
								<th>Marca</th>
								<th>Modelo</th>
								<th>Stock</th>
								<th>Precio Unit.</th>
								<th>Precio 1</th>
								<th>Precio 2</th>
								<th>Precio 3</th>
								<th>Precio 4</th>
								<th>Precio 5</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<style>
.wrapper-content {padding:0;}
</style>