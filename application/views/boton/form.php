<form id="form_<?php echo $controller; ?>" >
	<input type="hidden" name="idboton" id="<?php echo $prefix; ?>idboton" value="<?php echo (!empty($idboton)) ? $idboton : ""; ?>">
	<div class="row">
		<div class="col-sm-6">
			<div class="col-md-12">
				<div class="form-group">
					<label class="required">Boton</label>
					<input type="text" name="descripcion" id="<?php echo $prefix; ?>descripcion" value="<?php echo (!empty($descripcion)) ? $descripcion : ""; ?>" class="form-control" required="">
				</div>
			</div>

			<div class="col-md-12">
				<div class="form-group">
					<label class="required">Alias</label>
					<input type="text" name="alias" id="<?php echo $prefix; ?>alias" value="<?php echo (!empty($alias)) ? $alias : ""; ?>" class="form-control" required="">
				</div>
			</div>
			
			<div class="col-md-12">
				<div class="form-group">
					<label class="required">Id</label>
					<input type="text" name="id_name" id="<?php echo $prefix; ?>id_name" value="<?php echo (!empty($id_name)) ? $id_name : ""; ?>" class="form-control" required="">
				</div>
			</div>
		</div>
		
		<div class="col-sm-6">
			<div class="col-md-12">
				<div class="form-group">
					<label class="required">Icono</label>
					<div class="input-group" >
						<span id="icono_preview<?php echo $prefix; ?>" class="input-group-addon icono_preview">
						<?php 
							$icono_preview = "";
							if(!empty($icono))
								$icono_preview = $icono;
							echo '<i class="fa '.$icono_preview.'"></i>';
						?>
						</span>
						<input type="text" name="icono" id="<?php echo $prefix; ?>icono" value="<?php echo (!empty($icono)) ? $icono : ""; ?>" class="form-control icono" required="">
						<div class="input-group-btn">
							<button data-toggle="dropdown" class="btn btn-white dropdown-toggle" style="margin-bottom:0 !important;" type="button">Buscar <span class="caret"></span></button>
							<ul class="dropdown-menu pull-right" style="max-height: 200px; overflow-x:auto;">
								<li><a href="#"></a></li>
								<?php
									if(!empty($icons)) {
										foreach($icons as $icon) {
											echo '<li><a href="#" class="select_icon" id="select_icon'.$prefix.'" data-modal="modal-boton" data-icon="fa-'.$icon.'"><i class="fa fa-'.$icon.'"></i> fa-'.$icon.'</a></li>';
										}
									}
								?>
							</ul>
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-12">
				<div class="form-group">
					<label class="required">Tipo</label>
					<select name="tipo"  id="<?php echo $prefix; ?>tipo" class="form-control" id="tipo" >

					<?php
						$tipos = array('default'=>'Por defecto','personalizado'=>'Personalizado');
						foreach($tipos as $k=>$v){
							echo '<option value="'.$k.'" ';
							if(!empty($tipo) && $tipo==$k){
								echo 'selected="selected"';
							}
							echo '" >'.$v.'</option>';
						}
					?>
					</select>
				</div>
			</div>
			
			<div class="col-md-12">
				<div class="form-group">
					<label class="required">Class</label>
					<input type="text" name="clase_name" id="<?php echo $prefix; ?>clase_name" value="<?php echo (!empty($clase_name)) ? $clase_name : ""; ?>" class="form-control" required="">
				</div>
			</div>
		</div>	
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<div class="col-md-12">
				<div class="form-group">
					<button id="<?php echo $prefix; ?>btn_cancel" class="btn btn-sm btn-white btn_cancel<?php echo $modal?" modal-form":""; ?>" data-controller="<?php echo $controller; ?>">Cancelar</button>
					<button type="submit" id="<?php echo $prefix; ?>btn_save" class="btn btn-sm btn-primary" data-controller="<?php echo $controller; ?>">Guardar</button>
				</div>
			</div>
		</div>
	</div>
</form>