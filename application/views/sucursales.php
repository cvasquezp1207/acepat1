<div class="modal" id="modal_inicio" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content animated bounceInRight">
			<div class="modal-header">
				<!--<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<i class="fa fa-laptop modal-icon"></i>-->
				<h4 class="modal-title">Seleccione la sucursal de trabajo</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<div class="panel-group" id="accordion_sucursal" role="tablist">
							<?php 
							if(!empty($sucursales)) {
								$i = 0;
								foreach ($sucursales as $idempresa=>$valores) { $i++; $cls=''; if($i == 1) {$cls="in";}
							?>
							<div class="panel panel-default">
								<div class="panel-heading" role="tab">
									<h5 class="panel-title">
										<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion_sucursal" href="#collapse-<?php echo $idempresa;?>" aria-expanded="false"><?php echo strtoupper($valores['nombre']);?></a>
									</h5>
								</div>
								<?php if(!empty($valores["sucursal"])) { ?>
								<div id="collapse-<?php echo $idempresa;?>" class="panel-collapse collapse <?php echo $cls;?>">
									<div class="panel-body">
										<div class="list-group">
											<?php foreach ($valores["sucursal"] as $val) { ?>
											<a href="<?php echo base_url("home/cambiar_sucursal")."/".$val["idsucursal"];?>" class="list-group-item"><?php echo $val['nombre']; ?></a>
											<?php } ?>
										</div>
									</div>
								</div>
								<?php } ?>
							</div>
							<?php 
								}
							}
							?>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<!--<button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary">Save changes</button>-->
				<a href="<?php echo base_url()?>/login/salir" class="btn btn-primary btn-sm"><i class="fa fa-sign-out"></i> Salir</a>
			</div>
		</div>
	</div>
</div>