<div class="row wrapper border-bottom white-bg page-heading fixed-button-top form-inline tooltip-demo">
	<div class="col-md-12">
		<form id="form-data">
			<input type="text" name="fechainicio" id="fechainicio" style="width:80px;" class="form-control input-xs" value="<?php echo date('d/m/Y'); ?>"   placeholder="d/m/Y" >
			<input type="text" name="fechafin" id="fechafin" style="width:80px;" class="form-control input-xs"  placeholder="d/m/Y" >
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
					<table class="table table-bordered"  style="width:calc(100% - 0px);margin-bottom: 0px;">
						<thead>
							<tr>
								<?php
								foreach($head as $k=>$v){
									$sort = "";
									if($k=='linea')
										$sort="<i class='fa fa-sort-asc'></i>";
									else
										$sort="<i class='fa fa-sort'></i>";
									echo "<th width='{$v[4]}%' class='text-center'><a href='#' class='sorting' data-sort='{$k}'>{$v[1]}</a><div class='pull-right'>{$sort}</div></th>";
								}
								?>
							</tr>
						</thead>
					</table>
					
					<div style="width:100%;height:400px;overflow-x:hidden;overflow-y:scroll;border:1px solid #c7c7c7;">
						<table id="tabla-result" class="table table-bordered" style="width: 100%;margin-bottom:0;">
							<thead>
								<tr>
									<?php
									foreach($head as $k=>$v){
										echo "<th width='{$v[4]}%'></th>";
									}
									?>
								</tr>
							</thead>
						
							<tbody></tbody>
						</table>
					</div>
					<table class="table table-striped"  id="foot-vendedor" style="width:calc(100% - 0px);margin-bottom: 0px;">
						<tfoot>
							<tr>
								<?php
								foreach($head as $k=>$v){
									if($v[5])
										echo "<th width='{$v[4]}%' class='numerillo total_foot total_{$k}' data-key='{$k}'>0.00</th>";
									else
										echo "<th width='{$v[4]}%'></th>";
								}
								?>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<style>
.wrapper-content {padding:0;}
.table tbody>tr>td{font-size:10.6px;}
.text-center{text-align:center;cursor:pointer;}
</style>