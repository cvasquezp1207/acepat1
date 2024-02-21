<form id="form_<?php echo $controller; ?>" class="form-horizontal app-form">
	<input type="hidden" name="idperfil" id="idperfil" value="<?php echo (!empty($idperfil)) ? $idperfil : ""; ?>">
	<div class="form-group">
		<label class="col-lg-2 control-label required">Descripci&oacute;n</label>
		<div class="col-lg-10">
			<input type="text" name="descripcion" id="descripcion" value="<?php echo (!empty($descripcion)) ? $descripcion : ""; ?>" class="form-control" required="">
		</div>
	</div>
	
	<div class="form-group">
		<label class="col-lg-2 control-label required">Estado</label>
		<div class="col-lg-10">
			<div class="btn-group" role="group" aria-label="Estados">
	  			<?php
					$n_estado = 1;
					if(isset($estado))
						if($estado==0)
							$n_estado = 0;
					
					echo mi_cambio_estado($n_estado);
				?>
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-offset-2 col-lg-10">
			<!--<button id="btn_cancel" class="btn btn-sm btn-white" data-controller="<?php echo $controller; ?>">Cancelar</button>
			<button type="submit" id="btn_save" class="btn btn-sm btn-primary" data-controller="<?php echo $controller; ?>">Guardar</button>-->
			
			<button id="<?php echo $prefix; ?>btn_cancel" class="btn btn-sm btn-white btn_cancel<?php echo $modal?" modal-form":""; ?>" data-controller="<?php echo $controller; ?>">Cancelar</button>
			<button type="submit" id="<?php echo $prefix; ?>btn_save" class="btn btn-sm btn-primary" data-controller="<?php echo $controller; ?>">Guardar</button>
		</div>
	</div>
</form>

<style>
	/*.onoffswitch-inner:before {content: "ACTIVO";}
	.onoffswitch-inner:after {content: "INACTIVO";}*/

<script>
	// $('input[type="radio"].large').checkbox({
		// buttonStyle: 'btn-link btn-large',
		// checkedClass: 'icon-check',
		// uncheckedClass: 'icon-check-empty'
	// });
</script>