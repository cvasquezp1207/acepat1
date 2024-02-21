<div class="row">
	<div class="col-md-3">
		<div class="ibox float-e-margins" style="margin-bottom: 0px;">
			<div class="ibox-content">
				<form id="form-data">
					<div class="form-group">
						<label>Modulo</label>
						<?php echo $modulos;?>
					</div>
					<div class="form-group">
						<label>Tipo documento</label>
						<select class="form-control input-xs" id="idtipodocumento" name="idtipodocumento"></select>
					</div>
					<div class="form-group">
						<label>Fechas</label>
						<div class="input-daterange input-group input-group-xs">
							<input type="text" class="input-xs form-control" name="fecha_i" id="fecha_i" placeholder="dd/mm/aaaa" autocomplete="off">
							<span class="input-group-addon">hasta</span>
							<input type="text" class="input-xs form-control" name="fecha_f" id="fecha_f" placeholder="dd/mm/aaaa" autocomplete="off">
						</div>
					</div>
					<div class="form-group">
						<label>Sucursal</label>
						<?php echo $sucursal;?>
					</div>
					<div class="form-group">
						<button type="button" id="btnsearch" class="btn btn-white btn-sm"><i class="fa fa-search"></i> Consultar</button>
					</div>
					<div class="form-group">
						<button type="button" id="btnpdf" class="btn btn-success btn-xs"><i class="fa fa-print"></i> Generar PDF</button>
						<button type="button" id="btnexcel" class="btn btn-primary btn-xs"><i class="fa fa-download"></i> Generar Excel</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="col-md-9" style="padding:0;">
		<!-- datos del credito -->
		<div class="ibox float-e-margins" style="margin-bottom: 0px;">
			<div class="ibox-content">
				<div class="table-responsive">
					<table id="tabla-result" class="table table-bordered" style="width: 100%;margin-bottom:0;">
						<thead>
							<?php
							if( ! empty($columns)) {
								echo '<tr>';
								foreach($columns as $col) {
									echo '<th>'.$col["label"].'</th>';
								}
								echo '</tr>';
							}
							?>
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