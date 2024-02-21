<div class="row">	
	<div class="col-sm-12">
		<!-- datos del credito -->
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5><div>Cobranza por vendedor <span class="label label-default pull-right" id="nro_credito_ref"></span></div></h5>
				<div class="pull-right">
					<div class="ibox-tools">
						<span style="font-size:10px;font-weight:bold;" id="cant_vendedor">0 VENDEDORES</span>
						
						<a class="dropdown-toggle tooltip-demo" id="exportar_head" href="#" title="">
							<i class="fa fa-file-excel-o" data-toggle="tooltip" style="color:#055d05;font-weight: bold;font-size:18px;" title="Exportar" data-placement="bottom"></i>
						</a>
						
						<a class="collapse-link">
							<i class="fa fa-chevron-up"></i>
						</a>
					</div>
				</div>
				<div class="pull-right" id=""><h2 id="central_riesgo" class="no-margins text-danger text-center"></h2></div>
			</div>
			<div class="ibox-content">
				<div class="row">
					<div class="col-sm-4">
						<form id='form-filtro'>
							<div class="row">
								<div class="col-md-7">
									<div class="form-group">
										<label>Vendedor</label>
										<?php echo $vendedor;?>
									</div>
								</div>
								
								<div class="col-sm-5">
									<div class="form-group">
										<label>Moneda</label>
										<?php echo $moneda;?>
									</div>
								</div>
							</div>
							
							<div class="row">
								<div class="col-sm-7">
									<div class="form-group">
										<div class="row">
											<div class="col-sm-6">
												<label style="font-size:12px;">Cobro De</label>
												<input type="text" name="fecha_inicio" id="venta_fecha_inicio" class="form-control input-xs" value="<?php echo date('d/m/Y');?>">
											</div>
											<div class="col-sm-6">
												<label style="font-size:12px;">Cobro hasta</label>
												<input type="text" name="fecha_fin" id="venta_fecha_fin" class="form-control input-xs">
											</div>
										</div>
										
										<!--
										<div class="input-group date input-group-xs">
											<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										</div>
										-->
									</div>
								</div>
								<div class="col-sm-5">
									<div class="form-group">
										<button id="filtrar" style="width:100%;" class="btn btn-primary btn-sm" type="button"> <i class="fa fa-search"></i>&nbsp;&nbsp;Buscar</button>
									</div>
								</div>
							</div>
						</form>
					</div>
					<div class="col-sm-8">
						<table class="table table-striped"  style="width:calc(100% - 0px);margin-bottom: 0px;">
							<thead>
								<tr>
									<?php
										foreach($head_resumen as $k=>$v){
											echo "<th width='{$v[2]}%' class='text-center'>{$v[0]}</th>";
										}
									?>
								</tr>
							</thead>
						</table>
						<div style="width:100%;height:98px;overflow-x:hidden;overflow-y:scroll;border:1px solid #c7c7c7;">
							<table id="table-vendedor" class="table table-bordered detail-table no-header-background">
								<thead>
									<tr>
										<?php
											foreach($head_resumen as $k=>$v){
												if($v[3]){
													echo "<th width='{$v[2]}%'><input class='form-control filter input-xs search_{$k}'></th>";
												}else{
													echo "<th width='{$v[2]}%'></th>";
												}
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
									<th width="8%"    class="text-center"></th>
									<th width="40%" class="text-center"></th>
									<th width="15%"   class="text-number total_cobranza">0.00</th>
									<th width="15%"   class="text-number total_cobrado">0.00</th>
									<th width="15%"   class="text-center"></th>
									<th width="4%">&nbsp;</th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-sm-12">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5>Lista de Ventas al credito <span id="status"></span></h5>
				<div class="ibox-tools">
					<a class="dropdown-toggle tooltip-demo" id="exportar_ventas" href="#" title="">
						<i class="fa fa-file-excel-o" data-toggle="tooltip" style="color:#055d05;font-weight: bold;font-size:18px;" title="Exportar" data-placement="bottom"></i>
					</a>
					
					<!--
					<a class="dropdown-toggle tooltip-demo" id="ver" href="#" style="margin-right:15px;">						
						<i class="fa fa-eye" data-toggle="tooltip" title="Ver detalle de credito" style="color: black; font-weight: bold;" data-placement="bottom"></i>
					</a>					
					
					<a class="dropdown-toggle tooltip-demo" data-toggle="dropdown" href="#" >						
						<i class="fa fa-search" data-toggle="tooltip" title="Filtro" style="color: #028db7; font-weight: bold;" data-placement="bottom"></i>
					</a>
					<ul class="dropdown-menu dropdown-user">
						<li><a class="filter" valor="T" text="" href="#" id=""><i class="fa fa-download"></i> Ver todos los creditos</a></li>
						<li><a class="filter" valor="S" text="Pagados" href="#" id=""><i class="fa fa-rotate-right"></i> Ver Solo Pagados</a></li>
						<li><a class="filter" valor="N" text="Pendientes" href="#" id=""><i class="fa fa-rotate-right"></i> Ver Solo Pendientes</a></li>
					</ul>
					-->
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div class="ibox-content">
				<!-- tabla credito -->
				<div class="row">
					<div class="col-md-12">
						<table class="table table-striped"  style="width:calc(100% - 0px);margin-bottom: 0px;">
							<thead>
								<tr>
									<?php
									foreach($array_head as $k=>$v){
										echo "<th width='{$v[2]}%' class='text-center'>{$v[0]}</th>";
									}
									?>
								</tr>
							</thead>
						</table>
					
						<div style="width:100%;height:155px;overflow-x:hidden;overflow-y:scroll;">
							<table id="table-creditos" class="table table-bordered detail-table no-header-background">
								<thead>
									<tr>
										<?php
										foreach($array_head as $k=>$v){
											if($v[3]){
												echo "<th width='{$v[2]}%'><input class='form-control filter input-xs search_{$k}'></th>";
											}else{
												echo "<th width='{$v[2]}%'></th>";
											}
										}
										?>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
						<table class="table table-striped" id="foot-creditos" style="width:calc(100% - 0px);margin-bottom: 0px;">
							<tfoot>
								<tr>
									<th width="59%" class="total_creditos">0 CREDITOS</th>
									<th width="7%" class="text-number tot_impt_doc">0.00</th>
									<th width="7%" class="text-number tot_m_cobrado">0.00</th>
									<th width="6%" class="text-number tot_saldo"></th><!-- saldo = pagos + nota credito -->
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
				<!-- fin tabla credito -->
			</div>
		</div>
	</div>
</div>

<input type="hidden" name='ver_credito' id='ver_credito' value='T'/>
<style>
.badge{font-size:12px;}
#table-vendedor tbody tr td {font-size:10.4px;}
#table-vendedor thead tr th {white-space:nowrap;color:black;}
#foot-vendedor tfoot tr th {font-size:10.4px;}

#table-creditos tbody tr td {font-size:10.4px;}
#foot-creditos tfoot tr th {font-size:10.4px;}
#table-creditos thead tr th {white-space:nowrap;color:black;}
#table-creditos tfoot input[id^=total_] {font-weight:bold;}
#table-creditos .descuento {width:78%;display:inline-block;}
.widget {padding: 4px 10px;}
.label#nro_credito_ref{font-size:14px;}
.list-group{margin-bottom:0px;}
.list-group.clear-list .list-group-item{padding: 9px 0;}
.block_pago {position: absolute;background: #000;opacity: 0.2;left: 0;width: 100%;bottom: 0;height: 100%;z-index:100;}
.wrapper-content {
    padding: 5px 10px 40px;
}
.input-cronograma{width:50px;}
</style>