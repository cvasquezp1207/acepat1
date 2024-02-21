<form id="form_<?php echo $controller; ?>" class="form-horizontal app-form">
	<input type="hidden" name="idsistema" id="idsistema<?php echo $prefix;?>" value="<?php echo (!empty($idsistema)) ? $idsistema : ""; ?>">
	
	<div class="form-group">
		<div class="col-md-12">
		<label class="required">Descripci&oacute;n</label>
			<input type="text" name="descripcion" id="descripcion<?php echo $prefix;?>" value="<?php echo (!empty($descripcion)) ? $descripcion : ""; ?>" class="form-control" required="">
		</div>
	</div>
	
	<div class="form-group">
		<div class="row">
			<div class="col-sm-6">
				<div class="col-md-12">
				<label class="required">Abreviatura</label>
					<input type="text" name="abreviatura" id="abreviatura<?php echo $prefix;?>" value="<?php echo (!empty($abreviatura)) ? $abreviatura : ""; ?>" class="form-control" required="">
				</div>	
			</div>

			<div class="col-sm-6">
				<div class="col-md-12">
					<label class="required">Icono</label>
					<div class="input-group">
						<span id="icono_preview<?php echo $prefix;?>" class="input-group-addon">
						<?php 
							$icono_preview = "";
							if(!empty($image))
								$icono_preview = $image;
							echo '<i class="fa '.$icono_preview.'"></i>';
						?>
						</span>
						<input type="text" name="image" id="image<?php echo $prefix;?>" value="<?php echo (!empty($image)) ? $image : ""; ?>" class="form-control" required="">
						<div class="input-group-btn">
							<button data-toggle="dropdown" class="btn btn-white dropdown-toggle" style="margin-bottom:0 !important;" type="button">Buscar <span class="caret"></span></button>
							<ul class="dropdown-menu pull-right" style="max-height: 200px; overflow-x:auto;">
								<li><a href="#"></a></li>
								<?php
									if(!empty($icons)) {
										foreach($icons as $icon) {
											echo '<li><a href="#" class="select_icon" id="select_icon'.$prefix.'" data-icon="fa-'.$icon.'"><i class="fa fa-'.$icon.'"></i> fa-'.$icon.'</a></li>';
										}
									}
								?>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="form-group">
		<div class="col-md-10">
			<label class="">URL</label>
			<input type="text" name="url" id="url<?php echo $prefix;?>" value="<?php echo (!empty($url)) ? $url : ""; ?>" class="form-control" >
		</div>
		
		<div class="col-md-2">
			<label class="required">Orden</label>
			<input type="text" name="orden" id="orden<?php echo $prefix;?>" value="<?php echo (!empty($orden)) ? $orden : ""; ?>" class="form-control" required="" />
		</div>
	</div>
	
	<div class="form-group">

		<div class="col-lg-offset-2 col-lg-9">
			<button id="btn_cancel" class="btn btn-sm btn-white btn_cancel" >Cancelar</button>
			<button type="submit" id="btn_save" class="btn btn-sm btn-primary" >Guardar</button>
		</div>
	</div>
</form>