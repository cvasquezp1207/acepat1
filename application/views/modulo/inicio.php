<div class="row wrapper border-bottom white-bg page-heading fixed-button-top form-inline">
	<div class="col-sm-6 text-left">
		<div class="form-group no-margins">
			<h2 class="title-heading">Mantenimiento Modulo</h2>
			<small>Lista de Modulos</small>
		</div>
	</div>
	
	<div class="col-sm-6 text-right">
		<div class="">
			<?php
                if(count($botones)){
                    foreach($botones as $k){
                        echo "\n".$k;
                    }
                }
            ?>
		</div>
	</div>
</div>

<div class="wrapper wrapper-content  animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-content">
                    <div class="">
                        <div class="full-height-scroll">
							<div class="">
								<?php echo $grilla1;?>
							</div>
						</div>
                    </div>	
                </div>
            </div>
        </div>
    </div>    
</div>                        

<div id="modal-order" class="modal fade" data-keyboard="false" aria-hidden="true" aria-labelledby="myLargeModalCliente" data-backdrop="static">
	<div class="modal-dialog" style="margin-top: 10px;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Ordenar Modulos </h4>
			</div>
			<div class="modal-body" style="padding: 0px 30px 0px 29px;">
				<div class="row">
					<form id="form-order<?php echo $prefix; ?>" class="app-form" >
						<div class="row">
							<div class="col-sm-6">
								<div class="row">
									<div class="col-sm-12">
										<div class="form-group">
											<label>Sistema</label>
											<div class="input-group input-group-sm">
												<select name="idsistema" id="codsistema_order" class="form-control input-sm"></select>
												<span class="input-group-btn tooltip-demo">
													<button type="button" id="btn_sistema<?php echo $prefix;?>_order" class="btn btn-outline btn-primary" data-toggle="tooltip" title="Refrescar Sistemas">
														<i class="fa fa-refresh"></i>
													</button>
												</span>
											</div>
										</div>
									</div>
								</div>
								
								<div class="row">
									<div class="col-sm-12">
										<div class="form-group">
											<label>Modulo Padre</label>
											<select name="idpadre" id="codpadre_order" class="form-control input-sm"></select>
										</div>
									</div>
								</div>
							</div>
							
							<div class="col-sm-6">
								<label>Modulo(s) Hijo(s)</label>
								<div id="">
									<ul id="list_modulos" class="sortable"></ul>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group">
									<button class="btn btn-sm btn-white cancel_save" data-dismiss="modal" aria-label="Close"><strong>Cancelar</strong></button>
									<button type="submit" id="btn_save_order" class="btn btn-sm btn-primary" >Guardar</button>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
	.sortable{		
		list-style-type: none;
		padding: 5px 0 0 0;
		margin-bottom:20px;
		height:auto;
		border: 1px solid #ccc;
		background:white;
	}
	
	.sortable li{
		margin: 0 5px 5px 5px;
		padding: 5px;
		font-size: 12px;
		font-weight:bold;
	}
	
	.ui-state-default{
		border: 1px solid #c5c5c5;
		background: #f6f6f6;
		font-weight: normal;
		color: #454545;
	}
	
	.ui-state-highlight{
		border: 1px solid #dad55e;
		background: #fffa90;
		color: #777620;
		height: 2.2em;
	}
</style>