<div class="row m-b-sm m-t-sm">
	<div class="col-sm-4">
		<form class="form-horizontal">
			<div class="form-group">
				<label class="col-lg-2 control-label">Venta</label>
				<div class="col-lg-10">
					<input type="text" value="<?php echo $venta["tipo_documento"]." ".$venta["serie"]."-".$venta["correlativo"]; ?>" class="input-sm form-control" readonly>
				</div>
			</div>
		</form>
	</div>
	
	<div class="col-sm-8">
		<form class="form-horizontal">
			<div class="form-group">
				<label class="col-lg-2 control-label">Cliente</label>
				<div class="col-lg-10">
					<input type="text" value="<?php echo $venta["full_nombres"]; ?>" class="input-sm form-control" readonly>
				</div>
			</div>
		</form>
	</div>
</div>

<div class="project-list">
	<table class="table table-hover table-despacho">
		<tbody>
		<?php
		if( ! empty($detalle)) {
			foreach($detalle as $k=>$val) {
				echo '<tr>';
				
				echo '<td class="project-status"><span class="badge item-count">'.($k+1).'</span></td>';
				
				echo '<td class="project-status">'.$val["fecha"].'<br>'.$val["hora"].'</td>';
				echo '<td class="project-title"><a href="#" class="producto_desc">'.$val["producto"].'</a><br><small>'.$val["observacion"].'</small></td>';
				echo '<td class="project-status"><small>'.$val["almacen"].'</small></td>';
				echo '<td class="project-status"><span class="label label-primary label-desc">'.$val["tipodocumento"].' '.$val["serie"].'-'.$val["numero"].'</span></td>';
				
				echo '<td class="project-title"><a href="#">'.$val["cantidad"].'</a><br><small>'.$val["unidad"].'</small></td>';
				echo '<td class="project-status"><small>'.$val["usuario"].'</small></td>';
				
				echo '<td class="project-actions"><div class="btn-group tooltip-demo">';
				if( ! empty($val["series"])) {
					echo '<button class="btn-white btn btn-sm btn_view" data-toggle="tooltip" title="Ver series" data-series="'.$val["series"].'"><i class="fa fa-folder-open"></i></button>';
				}
				echo '<button class="btn-white btn btn-sm btn_del" data-toggle="tooltip" title="Eliminar recepcion" data-iddespacho="'.$val["iddespacho"].'"><i class="fa fa-trash"></i></button>';
				echo '</div></td>';
				echo '</tr>';
			}
		}
		?>
		</tbody>
	</table>
</div>

<div class="row">
	<div class="form-group">
		<div class="col-sm-6 text-left">
			<button class="btn btn-sm btn-warning btn_cancel" data-controller="<?php echo $controller; ?>">Atras</button>
		</div>
	</div>
</div>

<div id="modal-series" class="modal fade" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"></h4>
			</div>
			<div class="modal-body">
				<div class="table-responsive div_scroll" style="max-height:300px;">
					<table id="table-serie" class="table table-striped">
						<tbody></tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<style>
.label-desc {font-size: 12px;}
</style>