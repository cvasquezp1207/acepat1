<div class="row wrapper border-bottom white-bg page-heading fixed-button-top form-inline">
	<div class="col-sm-11 text-right">
		<button type="button" id="exportar" class="btn btn-white btn-sm"><i class="fa fa-exchange"></i> Exportar EXCEL</button>
		<button type="button" id="ver-pdf" class="btn btn-white btn-sm"><i class="fa fa-truck"></i> Exportar PDF</button>
	</div>
</div>
<input type="hidden" id="producto_idproducto">
<div class="row">
	<div class="col-sm-3">
		<div class="ibox float-e-margins" style="margin-bottom: 0px;">
			<div class="ibox-title" >
				<h5>Filtro de Producto</h5>
				<div class="ibox-tools">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div class="ibox-content">
				<div class="row">
					<div class="col-sm-12">
						<form id="form_filtro">
							<div class="form-group">
								<label>Fecha</label>
								<div class="input-group date">
									<input type="text" name="fechainicio" id="fechainicio" class="form-control input-xs" value="<?php echo date('d/m/Y'); ?>"   placeholder="yy/mm/YY" >
									<span class="input-group-addon" style="padding: 3px 8px;"><i class="fa fa-calendar"></i></span>
								</div>
								<div class="input-group date">
									<input type="text" name="fechafin" id="fechafin" class="form-control input-xs"  placeholder="yy/mm/YY" >
									<span class="input-group-addon" style="padding: 3px 8px;"><i class="fa fa-calendar"></i></span>
								</div>
							</div>
						
							
							<div class="form-group">
								<label>Categoria</label>
								<?php echo $categoria;?>
							</div>
							
							<div class="form-group">
								<label>Marca</label>
								<?php echo $marca;?>
							</div>
							<div class="form-group">
								<label>Modelo</label>
								<?php echo $modelo;?>
							</div>
							
							<div class="form-group">
								<label>Almacen</label>
								<?php echo $almacen;?>
							</div>
					
						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- bloque de botones de consultas -->
	</div>
	
	<div class="col-sm-9">
		<!-- datos del credito -->
		<div class="ibox float-e-margins" style="margin-bottom: 0px;">
			<div class="ibox-title" >

				<div class="ibox-tools">
					<a class="dropdown-toggle tooltip-demo change_panel" data-toggle="dropdown" href="#">						
						<i class="fa fa-bars" data-toggle="tooltip" data-placement="left" title="Cambiar Presentacion"></i>
                    </a>
					
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div class="ibox-content">
				<div class="row">

					<div class="col-sm-5 m-b-xs">
						<div data-toggle="buttons" class="btn-group">
							<label class="btn btn-sm btn-white active"> <input type="radio" class="filtro_stock" name="con_stock" value="T"> Todos </label>
							<label class="btn btn-sm btn-white"> <input type="radio" class="filtro_stock" name="con_stock" value="S"> Con stock </label>
							<label class="btn btn-sm btn-white"> <input type="radio" class="filtro_stock" name="con_stock" value="N"> Sin stock </label>
						</div>
					</div>
					<div class="col-sm-7 m-b-xs">
						<div class="input-group">
							<input type="text" placeholder="Buscar" name="query" id="txtQuery" class="input-sm form-control">
							<span class="input-group-btn"><button type="button" class="btn btn-sm btn-primary" id="btnQuery"><i class="fa fa-search"></i></button> </span>
						</div>
					</div>
				</div>
				
				<div class="table-responsive">
					<?php echo $grid;?>
				</div>
			</div>
		</div>
	</div>
</div>


<style>
#dtview_stock_filter {display:none;}
table#dtview_stock tbody>tr>td{padding: 4px !important;font-size: 12px;}
.panel-form {margin: 0;}
.panel-form>.panel-heading {padding: 5px 10px;}
.panel-form>.panel-body {padding: 10px;}
.form-group {margin-bottom:10px;}
.form-inline .temp_producto {width:30%}
.form-inline .temp_unidad {width:10%}
.form-inline .temp_stock {width:10%}
.form-inline .temp_cantidad {width:10%}
.detail-table {font-size:12px;}
</style>