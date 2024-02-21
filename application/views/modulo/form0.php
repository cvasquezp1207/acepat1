<div class="row wrapper border-bottom white-bg page-heading fixed-button-top form-inline">
	<div class="col-sm-6 text-left">
		<div class="form-group no-margins">
			<h2 class="title-heading">Mantenimiento de Modulo</h2>
			<small>Lista de Modulo</small>
		</div>
	</div>
	
	<div class="col-sm-6 text-right">
		<div class="">
			<?php
					$acceso_total = false;
					if(count($botones)){
						foreach($botones as $k){
							echo "\n".$k;
						}
					}
					if($boton_all==count($botones)){
				?>
			<div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-warning save dropdown-toggle">Nuevas Asignaciones <span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a class="save_asign" style="cursor:pointer">Guardar Asignaciones</a></li>
                        <li><a class="cancelar"   style="cursor:pointer">Cancelar Asignaciones</a></li>
                    </ul>
                </div>
				
				<?php } ?>
		</div>
	</div>
</div>

<div class="row">
	<form id="form-all"><?php echo $lista; ?></form>
</div>

<div id="modal-form" class="modal fade" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">[TITULO MODAL]</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<form id="form-data" >
						<input type="hidden" id="idmodulo" name="idmodulo">
						<input type="hidden"  id='idpadrecito' name="idpadre">
						<input type="hidden"  id='idsystem' name='idsistema'>
						
						<div class="col-sm-6 content_form">
							<div class="form-group label_padre">
								<label class="required">Padre</label>
								<div class="input-group">
									<span id="icono_father" class="input-group-addon"></span>
									<input type="text" id="padrecito" class="form-control" readonly="readonly">
								</div>
							</div>
						
							<div class="form-group">
								<label class="required">Descripci&oacute;n</label>
								<input type="text" name="descripcion" id="descripcion" class="form-control" required="">
							</div>
							
							<div class="form-group">
								<label>URL</label>
								<input type="text" name="url" id="url" class="form-control">
							</div>
							
							<div class="form-group">
								<label class="required">Orden</label>
								<input type="text" name="orden" id="orden" class="form-control">
							</div>

							<div class="form-group">
								<label class="required">Icono</label>
								<div class="input-group">
									<span id="icono_preview" class="input-group-addon icono_preview"></span>
									<input type="text" name="icono" id="icono" class="form-control icono" required="">
									<div class="input-group-btn">
										<button data-toggle="dropdown" class="btn btn-white dropdown-toggle" type="button">Buscar <span class="caret"></span></button>
										<ul class="dropdown-menu pull-right" style="max-height: 200px; overflow-x:auto;">
											<li><a href="#"></a></li>
											<?php
											if(!empty($icons)) {
												foreach($icons as $icon) {
													echo '<li><a href="#" class="select_icon" data-modal="modal-form" data-icon="fa-'.$icon.'"><i class="fa fa-'.$icon.'"></i> fa-'.$icon.'</a></li>';
												}
											}
											?>
										</ul>
									</div>
								</div>
							</div>
						</div>
						
						<div class="col-sm-6 content_boton">
							<div class="form-group" style="">
								<label class="required">Boton</label>
								<div class="input-group" style="border:0px solid red;">
									<!--<select name="idboton_sel" id="idboton_sel" class="form-control"></select>-->
									<div class="btn-group">
										<button data-toggle="dropdown" class="btn btn-white dropdown-toggle">Seleccione Boton......<span class="caret"></span></button>
										<ul class="dropdown-menu" id="idboton_sel"></ul>										
										<span class="input-group-btn tooltip-demo" style="display:inline-block;">
											<button type="button" id="btn-registrar-boton" class="btn btn-outline btn-primary" data-toggle="tooltip" title="Registrar nuevo boton">
												<i class="fa fa-edit"></i>
											</button>
										</span>
									</div>
								</div>
							</div>
							
							<div class="form-group" style="">
								<table width="100%">
									<tbody id="detalle_boton"></tbody>
								</table>
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

<div id="modal-boton" class="modal fade"  >
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">[]</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<?php echo $form_boton; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="app/js/jquery-2.1.1.js"></script>
<script src="app/js/uikit.min.js"></script>
<script src="app/js/nestable.js"></script>

<script>
var sumita = 0;
	$('.uk-nestable').on("stop.uk.nestable", function(e,ui){
		continuar = false;
		botones(continuar);
		reordenar();
	});
	function reordenar(){
		$('.uk-nestable').each(function(i,j){
			$super_idpadre = $(j).attr('data-father-super');

			$(j.children).each(function(ii,jj){
				$xyz = jj.childNodes[0].nextSibling;
				
				$($xyz).attr('data-idsystem',$super_idpadre);
				
				$idmodulo  			= $($xyz).attr('data-modulo');	//-->Id padre
				$idsistema 			= $($xyz).attr('data-idsystem');	//-->Id sistema
				$idsystem_parent 	= $($xyz).parent('div').attr('data-idsystem');
				$li_padre			= $($xyz).find('input.idpadre');
				
				$($($xyz)).find('input.idsistema').val($idsistema)// CAMBIANDO DE SISTEMA

				$li_padre.val(0);
				
				$($(jj).find('ul li')).each(function(x,y){
					
					$input_modulo 		= $(y).find('input.idmodulo');
					$input_idsistema 	= $(y).find('input.idsistema');
					$input_idpadre 		= $(y).find('input.idpadre');
					$input_orden	 	= $(y).find('input.orden');
						
					$input_idsistema.val($idsistema);				
					
					$input_idpadre.val($idmodulo);
					$input_orden.val((x+1));
					return;
				})
				return;
			})
			return;
		});
		sumita++;
	}
	
	reordenar();
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
			background: #1ab394;
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