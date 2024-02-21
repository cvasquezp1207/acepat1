<form id="form_<?php echo $controller; ?>" class="form-horizontal app-form">
	<div class="row">
		<div class="col-sm-6">
			<div class="form-group">
				<label class="col-lg-3 control-label required">Parametro</label>
				<div class="col-lg-9">
					<input  name="idparam" id="<?php echo $prefix; ?>idparam" value="<?php echo (!empty($idparam)) ? $idparam : ""; ?>" class="form-control" required="">
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-3 control-label required">Descripcion</label>
				<div class="col-lg-9">
					<input type="text" name="descripcion" id="<?php echo $prefix; ?>descripcion" value="<?php echo (!empty($descripcion)) ? $descripcion : ""; ?>" class="form-control" required="">
				</div>
			</div>
		</div>
		<div class="col-sm-6">
			<div class="form-group">
				<label class="col-lg-2 control-label required">Valor</label>
				<div class="col-lg-10">
					<input type="text" name="valor" id="<?php echo $prefix; ?>valor" value="<?php echo (!empty($valor)) ? $valor : ""; ?>" class="form-control" required="">
				</div>
			</div>
		
			<div class="form-group">
				<label class="col-lg-2 control-label required">Tipo</label>
				<div class="col-lg-10">
					<input type="text" name="tipo" id="<?php echo $prefix; ?>tipo" value="<?php echo (!empty($tipo)) ? $tipo : ""; ?>" class="form-control" required="">
				</div>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="form-group">
			<div class="col-lg-offset-2 col-lg-9">
				<button id="<?php echo $prefix; ?>btn_cancel" class="btn btn-sm btn-white btn_cancel<?php echo $modal?" modal-form":""; ?>" data-controller="<?php echo $controller; ?>">Cancelar</button>
				<button type="submit" id="<?php echo $prefix; ?>btn_save" class="btn btn-sm btn-primary" data-controller="<?php echo $controller; ?>">Guardar</button>
			</div>
		</div>
	</div>
</form>