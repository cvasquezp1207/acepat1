<div class="row">
	<div class="col-sm-12">
		<div class="ibox">
			<div class="ibox-content">
				<button type="button" class="btn btn-primary botoncito btn_nuevo fa fa-file-o disabled" >&nbsp;&nbsp;Nuevo</button>
				<button type="button" class="btn btn-white botoncito btn_editar fa fa-pencil" >&nbsp;&nbsp;Modificar</button>
				<button type="button" class="btn btn-dafault botoncito btn_delete fa fa-trash" >&nbsp;&nbsp;Eliminar</button>

			</div>
		</div>
	</div>
	<form id="form-all"><?php echo $columnas; ?></form>
</div>

<div id="modal-form" class="modal fade" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Concepto Movimiento</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<form id="form-data" >
						<input type="hidden" name="idconceptomovimiento" id="idconceptomovimiento" value="">

						<div class="col-sm-12">
							<div class="form-group label_padre">
								<label class="required">Tipo Movimiento</label>
								<?php echo $tipomovimiento; ?>
							</div>
						
							<div class="form-group">
								<label class="required">Descripci&oacute;n</label>
								<input type="text" name="descripcion" id="descripcion" class="form-control" required="">
							</div>
							
							<div class="form-group">
								<label class="required">Orden</label>
								<input type="text" name="orden" id="orden" class="form-control">
							</div>
						</div>

						<!--<div id="checkbox_list"></div>-->

						
						<div class="col-sm-12">
							<div class="row">
								<div class="col-sm-3" style="border:0px solid red;">
									<div class="form-group">
										<label class="required">Ver en compra</label>
										<div class="onoffswitch">
											<input type="checkbox" name="ver_compra" id="ver_compra" class="onoffswitch-checkbox" value="N">
											<label class="onoffswitch-label" for="ver_compra">
												<span class="onoffswitch-inner"></span>
												<span class="onoffswitch-switch"></span>
											</label>
										</div>
									</div>
								</div>

								<div class="col-sm-3" style="border:0px solid red;">
									<div class="form-group">
										<label class="required">Ver en venta</label>
										<div class="onoffswitch">
											<input type="checkbox" name="ver_venta" id="ver_venta" class="onoffswitch-checkbox" value="N">
											<label class="onoffswitch-label" for="ver_venta">
												<span class="onoffswitch-inner"></span>
												<span class="onoffswitch-switch"></span>
											</label>
										</div>
									</div>
								</div>

								<div class="col-sm-3" style="border:0px solid red;">
									<div class="form-group">
										<label class="required">Ver en r. ingreso</label>
										<div class="onoffswitch">
											<input type="checkbox" name="ver_reciboingreso" id="ver_reciboingreso" class="onoffswitch-checkbox" value="N">
											<label class="onoffswitch-label" for="ver_reciboingreso">
												<span class="onoffswitch-inner"></span>
												<span class="onoffswitch-switch"></span>
											</label>
										</div>
									</div>
								</div>

								<div class="col-sm-3" style="border:0px solid red;">
									<div class="form-group">
										<label class="required">Ver en r. egreso</label>
										<div class="onoffswitch">
											<input type="checkbox" name="ver_reciboegreso" id="ver_reciboegreso" class="onoffswitch-checkbox" value="N">
											<label class="onoffswitch-label" for="ver_reciboegreso">
												<span class="onoffswitch-inner"></span>
												<span class="onoffswitch-switch"></span>
											</label>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						
						<div class="col-sm-12">
							<div class="" style="float:right">
								<button class="btn btn-sm btn-white cancel_save" data-dismiss="modal" aria-label="Close"><strong>Cancelar</strong></button>
								<button class="btn btn-sm btn-primary save_data" type="button"><strong>Guardar</strong></button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="app/js/jquery-2.1.1.js"></script>
<script src="app/js/uikit.min.js"></script>
<script src="app/js/nestable.js"></script>
<script>
	sumita = 0;
	vueltitas = 0;
	var nestable = UIkit.nestable('.uk-nestable',{
			maxDepth:1
			,group:'widgets'
			
		});
		
	$('.uk-nestable').on("stop.uk.nestable", function(e,ui){
		reordenar(true);
		// console.log("Here...");
	});
	
	$(document).on('click','.manejable',function(){
		if(  $(this).hasClass('seleccinado') ){
			$(this).removeClass('seleccinado');
		}else{
			$('.manejable').removeClass('seleccinado');
			$(this).addClass('seleccinado');
			$tipo	=	$('.seleccinado').attr('data-type');
			$nivel	=	$('.seleccinado').attr('data-level');

			if($tipo=='system'){
				$('.btn_editar,.btn_delete').addClass('disabled');
				$('.btn_nuevo').removeClass('disabled');
			}else{
				if($nivel<=2){
					$('.btn_editar,.btn_delete').removeClass('disabled');
					$('.btn_nuevo').addClass('disabled');
				}else{
					$('.btn_nuevo').removeClass('disabled');
				}
			}
		}
	});
	
	function reordenar(band){
		$('.uk-nestable').each(function(i,j){
			$super_idpadre = $(j).attr('data-father-super');
			$(j.children).each(function(ii,jj){				
				$(jj).find('input.orden').attr('value',(ii+1));
				$(jj).find('input.idtipomovimiento').attr('value',$super_idpadre);
			})
		});
	
		sumita++;
		// console.log(sumita);
		
		if(band)
			guardar_todo();
	}
	
	reordenar(false);
</script>
<link  href="app/css/uikit.docs.min.css" rel="stylesheet">

	<style>
		li{
			text-decoration: none;
			display: block;
		}
		
		.modal_icono{font-size:35px;}
		.parent_icono{font-size:20px;}
		
		.sistema{
			padding: 10px 10px;
			/*margin-left: -30px !important;
			margin: 5px 0;*/
			color: white;
			border: 1px solid #e7eaec;
			/*background: #2f4050;*/
			background: #293846;
			-webkit-border-radius: 3px;
			border-radius: 3px;
			box-sizing: border-box;
			font-size:13px;
		}
		
		.btn_nuevo_m{
			background:transparent;
			border:0;
			font-size:20px;
		}
		
		.btn_nuevo_m_h{
			background:transparent;
			border:0;
			font-size:13px;
		}
		
		.btn_nuevo_m:hover{
			border:0;
			
		}
		
		.ul_master li{
			list-style: none;
			  margin: 0;
			  padding: 0;
		}
		
		.option_menu{
			font-size:16px;
		}
		
		.uk-nestable{
			
		}
		
		.seleccinado{
			background:#B0BED9;
			color:black;
			text-shadow: 0px 0px 0px transparent;
		}
		
		.uk-nestable-list:not(.uk-nestable-dragged)>.uk-nestable-item:first-child {
			margin-top: 5px;
		}
		
		.uk-nestable-item+.uk-nestable-item {
			margin-top: 5px;
		}
		
		.disabled{
			pointer-events: auto !important;
			cursor: not-allowed !important;
		}
		
		.uk-nestable-panel{
			padding: 4px !important;
		}
	</style>