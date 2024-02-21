<form id="form_<?php echo $controller; ?>" class="" enctype="multipart/form-data">
	<input type="hidden" name="idtipomovimiento" id="idtipomovimiento" value="<?php echo (!empty($idtipomovimiento)) ? $idtipomovimiento : ""; ?>">
	
	<div class="modal-body">
		<div class="row">
			<div class="col-md-12" >
				<div class="form-group">
					<label for="" class="required">Descripci&oacute;n</label>
					<input type="text" class="form-control"  id="descripcion" name="descripcion" required=""  value="<?php echo (!empty($descripcion)) ? $descripcion : ""; ?>" >
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group">
					<label for="" class="required">Simbolo</label>
					<select id="simbolo" name="simbolo" class="form-control">
						<option value=''></option>
						<option value='E'>E</option>
						<option value='S'>S</option>
					</select>
				</div>
			</div>

			<div class="col-md-6" >
				<div class="form-group">
					<label for="" class="required">Alias</label>
					<input type="text" class="form-control" id="alias" name="alias" aria-describedby="sizing-addon2"  value="<?php echo (!empty($alias)) ? $alias : ""; ?>" >
				</div>
			</div>

			<div class="col-md-6" >
				<div class="form-group">
					<label for="" class="required">Orden</label>
					<input type="text" class="form-control" id="orden" name="orden" aria-describedby="sizing-addon2"  value="<?php echo (!empty($orden)) ? $orden : ""; ?>" >
				</div>
			</div>
			
			<!--
			<div class="col-md-6" >
				<div class="form-group">
					<label for="" class="required">Lineal</label>
					<?php						
						$n_lineal 	= 'N';
						$attr 		= '';
						if (!empty($lineal)) {
							$n_lineal = $lineal;
							if ($lineal=='N') 
								$attr 		= 'checked=""';							
						}
					?>
					<div class="switch">
                        <div class="onoffswitch">
                            <input type="checkbox" <?php echo $attr;?> class="onoffswitch-checkbox" id="lineal" name="lineal" value="<?php echo $n_lineal;?>">
                            <label class="onoffswitch-label" for="lineal">
                            	<span class="onoffswitch-inner"></span>
                            	<span class="onoffswitch-switch"></span>
                            </label>
                        </div>
                    </div>
				</div>
			</div>
			-->
		</div>
	</div>

	<div class="row">
		<div class="form-group">
			<div class="col-lg-offset-4 col-lg-8">
				<button id="btn_cancel" class="btn btn-sm btn-white btn_cancel" >Cancelar</button>
				<button type="submit" id="btn_save" class="btn btn-sm btn-primary" >Guardar</button>
			</div>
		</div>
	</div>
</form>