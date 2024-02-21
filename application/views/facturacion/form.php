<div class="row wrapper border-bottom white-bg page-heading fixed-button-top form-inline tooltip-demo">
	<div class="col-md-12">
		<form id="form-data">
			<label>Fechas</label>
			<div class="input-daterange input-group input-group-xs">
				<span class="input-group-addon">
					<input type="checkbox" name="all_fecha" id="all_fecha" value="1" data-toggle="tooltip" data-placement="bottom" title="Todas las fechas">
				</span>
				<input type="text" class="input-xs form-control" name="fecha_i" id="fecha_i" placeholder="dd/mm/aaaa" autocomplete="off" value="<?php echo date("d/m/Y");?>" style="width:80px;">
				<span class="input-group-addon">hasta</span>
				<input type="text" class="input-xs form-control" name="fecha_f" id="fecha_f" placeholder="dd/mm/aaaa" autocomplete="off" value="<?php echo date("d/m/Y");?>" style="width:80px;">
			</div>
			<label>Comprobante</label>
			<?php echo $tipodocumento;?>
			<label>Situaci&oacute;n</label>
			<?php echo $situacion;?>
			<label>Sucursal</label>
			<?php echo $sucursal;?>
			<!--<label>Temporizador <i class="fa fa-question-circle" title="" data-toggle="tooltip" data-placement="bottom"></i></label>
			<div class="input-group input-group-xs">
				<span class="input-group-addon">
					<input type="checkbox" id="enableTemporizador" value="1" data-toggle="tooltip" data-placement="bottom" title="Habilitar temporizador">
				</span>
				<input type="text" class="input-xs form-control" id="temporizador" placeholder="minutos" autocomplete="off" style="width:60px;" readonly>
			</div>-->
			<div style="display:inline-block;margin-left:5px;">
				<button type="button" id="btnsearch" class="btn btn-white btn-sm" data-toggle="tooltip" data-placement="bottom" title="Buscar Registros"><i class="fa fa-search"></i></button>
				<button type="button" id="btnupdate" class="btn btn-white btn-sm" data-toggle="tooltip" data-placement="bottom" title="Actualizar Estado"><i class="fa fa-refresh"></i></button>
				<button type="button" id="btngenerar" class="btn btn-success btn-sm" data-toggle="tooltip" data-placement="bottom" title="Generar Comprobante"><i class="fa fa-gears"></i></button>
				<button type="button" id="btnsend" class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="bottom" title="Enviar Comprobante a SUNAT"><i class="fa fa-upload"></i></button>
				<button type="button" id="btnprint" class="btn btn-white btn-sm" data-toggle="tooltip" data-placement="bottom" title="Imprimir Comprobante"><i class="fa fa-print"></i></button>
				<button type="button" id="btnbaja" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="bottom" title="Comunicacion de Baja"><i class="fa fa-times"></i></button>
				<button type="button" id="btnconfig" class="btn btn-default btn-sm" data-toggle="tooltip" data-placement="bottom" title="Configurar temporizador" ><i class="fa fa-gear" ></i></button>
			</div>
		</form>
	</div>
</div>
<div class="row">
	<div class="col-md-12" style="padding:0;">
		<!-- datos del credito -->
		<div class="ibox float-e-margins" style="margin-bottom: 0px;">
			<div class="ibox-content">
				<!--<div class="row">
					<div class="col-md-4 col-md-offset-8 m-b-xs">
						<div class="input-group">
							<input type="text" placeholder="Buscar" name="query" id="txtQuery" class="input-sm form-control">
							<span class="input-group-btn"><button type="button" class="btn btn-sm btn-primary" id="btnQuery"><i class="fa fa-search"></i></button> </span>
						</div>
					</div>
				</div>-->
				<div class="table-responsive">
					<table id="tabla-result" class="table table-bordered" style="width: 100%;margin-bottom:0;">
						<thead>
							<tr>
								<th style="width:10%;">Nro. RUC</th>
								<th style="width:11%;">Tipo Doc.</th>
								<th style="width:11%;">Numero Doc.</th>
								<th style="width:10%;">Doc. Ref.</th>
								<th style="width:8%;">F. Carga</th>
								<th style="width:10%;">F. Generaci&oacute;n</th>
								<th style="width:10%;">F. Envio</th>
								<th style="width:12%;">Situaci&oacute;n</th>
								<th style="width:18%;">Observaciones</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="modal-config" class="modal fade" data-keyboard="false"  aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-sm" >
		<div class="modal-content">
			<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Configurar temporizador</h4>
			</div>
			
			<div class="modal-body">
				<p><strong>Nota:</strong> Esta funci&oacute;n se habilita mientras el m&oacute;dulo este abierto.</p>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label>Usar temporizador</label>
							<select id="usar_temporizador" class="form-control input-xs">
																<option value="S" selected>SI</option>
							</select>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label>Segundos</label>
							<input type="text" id="minutos" class="form-control input-xs" value="60">
						</div>
					</div>
				</div>
			</div>
			
			<div class="modal-footer">
				<button type="button" id="btn-guardar-temporizador" class="btn btn-sm btn-primary">Guardar</button>
				<button type="button" class="btn btn-sm btn-white" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>
<style>
.wrapper-content {padding:0;}
#tabla-result tbody tr {cursor:pointer;}
.input-daterange .input-group-addon {padding:0 3px;}
</style>