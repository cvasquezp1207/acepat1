<div class="row wrapper border-bottom white-bg page-heading fixed-button-top form-inline">
	<div class="col-sm-6 text-left">
		<div class="form-group no-margins">
			<h2 class="title-heading">Mantenimiento de Cliente</h2>
			<small>Lista de Clientes</small>
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
        <div class="col-sm-8">
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

        <div class="col-sm-4" style="border:0px solid red;">					
			<div class="row">
				<div class="cols-sm-12">
					<div class="ibox float-e-margins">
						<div class="ibox-title">
							<h5>DATOS DEL CLIENTE</h5>
							<div class="ibox-tools">
								<a class="collapse-link">
									<i class="fa fa-chevron-up"></i>
								</a>
							</div>
						</div>
						<div class="ibox-content">
							<div class="tab-content">
								<div id="contact-1" class="tab-pane active">
									<div class="row ">
										<div class="col-lg-4 text-center">
											<div class="m-b-sm">
												<img alt="image" class="img-circle thumb_image" src="./app/img/cliente/anonimo.jpg" style="width: 62px">
											</div>
										</div>
										<div class="col-lg-8">
											<strong class="title_cliente">{NOMBRE CLIENTE}</strong>
											<p class="referencia" style="text-align : justify;">
												Seleccione un cliente para ver mas detalles sobre el.
											</p>
										</div>
									</div>

									<div class="">
										<div class="">
											<div class="row">	
												<div class="col-sm-12">
													<strong class="title_mail">{Correo@hotmail.com}</strong>
													<hr style="margin-top: 5px;margin-bottom: 5px;"></hr>
												</div>
											</div>
													
											<div class="more_info" style="display:block;"></div>
										</div>
									</div>
								</div>
							</div> 
						</div> 
					</div> 
				</div>
			</div>
 		</div> 	                           	
    </div>    
</div>                        

<div id="modal-form" class="modal fade" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Ampliación de Linea de Credito</h4>
            </div>   
            <div class="modal-body">
                <div class="row">
                    <form id="form-linea">
                        <input type="hidden" id="idcliente_linea" name="idcliente">
                        <div class="form-group">
							<div class="sms_linea alert alert-danger"></div>
						</div>
						
                        <div class="form-group">
							<div class="row">
								<div class="col-sm-12">
									<label class="required">Cliente</label>
									<input type="text" name="" id="cliente_ampliar" class="form-control" readonly="readonly">
								</div>
							</div>
                        </div>
						
						 <div class="form-group">
							<div class="row">
								<div class="col-sm-4">
									<label class="required">Valido Solo el</label>
									<div class="input-group">
										<input type="text" name="f_desde" id="f_desde"  class="form-control" value="<?php echo date('d/m/Y');?>" readonly=""/>
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
									</div>
								</div>
									<!--
								<div class="col-sm-4">
									<label class="required">Fecha Fin</label>
									<div class="input-group">
										<input type="text" name="f_hasta" id="f_hasta"  class="form-control" value="<?php echo date('d/m/Y');?>" readonly=""/>
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
									</div>
								</div>-->
									
								<div class="col-sm-4">
									<label class="required">Monto</label>
									<div class="input-group">
										<input type="text" name="monto" id="monto"  class="form-control numero" placeholder="0.00"/>
										<span class="input-group-addon"><i class="fa fa-cc"></i></span>
									</div>
								</div>
							</div>
                        </div>
                    </form>
                </div>
            </div>
			
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				<button type="button" id="save_linea_c" class="btn btn-primary">Guardar</button>
			</div>
        </div>    
    </div>    
</div>

<div id="form-configurar" class="modal fade" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Bloquear/Desbloquear Cliente</h4>
            </div>   
            <div class="modal-body">
                <div class="row">
                    <form id="form-config">
                        <input type="hidden" id="idcliente_block" name="idcliente">
						
                        <div class="form-group">
							<div class="row">
								<div class="col-sm-12">
									<label class="required">Cliente</label>
									<input type="text" name="" id="cliente_block" class="form-control" readonly="readonly">
								</div>
							</div>
                        </div>
						
						<div class="form-group" style="margin-right:0px;margin-left:0px;">							
							<div class="row" style="">
								<div class="col-md-2">
									<div class="">
										<label>L. Credito</label>
										<div class="onoffswitch">
											<input type="checkbox" id="linea_credito" class="onoffswitch-checkbox" >
											<label class="onoffswitch-label" for="linea_credito">
												<span class="onoffswitch-inner"></span>
												<span class="onoffswitch-switch"></span>
											</label>
										</div>
									</div>
								</div>
								
								<div class="col-md-2">
									<div class="">
										<label>Bloquear</label>
										<div class="onoffswitch">
											<input type="checkbox" id="bloqueado" class="onoffswitch-checkbox" >
											<label class="onoffswitch-label" for="bloqueado">
												<span class="onoffswitch-inner"></span>
												<span class="onoffswitch-switch"></span>
											</label>
										</div>
									</div>
								</div>
								
								<div class="col-md-3">
									<div class="">
										<label class="">Linea Credito</label>
										<input type="text" name="limite_credito" id="limite_credito" readonly="readonly" value="" class="form-control numerillo limite_credito input-sm">
									</div>
								</div>
								
								<div class="col-md-3">
									<div class="">
										<label class="">Linea consumida</label>
										<input type="text" name="linea_consumida" id="linea_consumida" readonly="readonly" value="" class="form-control numerillo linea_consumida input-sm" placeholder='0.00'>
									</div>
								</div>
								
								<div class="col-md-2">
									<div class="">
										<label class="">Disponible</label>
										<input type="text" name="linea_disponible" id="linea_disponible" readonly="readonly" value="" class="form-control numerillo linea_disponible input-sm" placeholder='0.00'>
									</div>
								</div>
							</div>
						</div>
                    </form>
                </div>
            </div>
			
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				<button type="button" id="save_config_c" class="btn btn-primary">Guardar</button>
			</div>
        </div>    
    </div>    
</div>
<!--
-->

<style>
	#dtcliente_view tbody tr td{font-size:10px;}
	.client-avatar img {
		width: 18px;
		height: 15px;
	}
	.numero{text-align:right;}
</style>