	<!--<form id="form_<?php echo $controller; ?>" action="<?php echo $controller; ?>/guardar" class="form-horizontal app-form" enctype="multipart/form-data"> -->
	<form id="form_<?php echo $controller; ?>" class="form-horizontal app-form" enctype="multipart/form-data">
		<input type="hidden" name="idempresa" id="idempresa" value="<?php echo (!empty($idempresa)) ? $idempresa : ""; ?>">
		<input type="file" name="file" id="file" style="display: none;" onchange='leerarchivobin(this)' />
		<input type="hidden" id="logo" name="logo" value="<?php echo (!empty($logo)) ? $logo : "default_logo.png"; ?>"/>
		<input type="hidden" id="logo_new" name="logo_new" value="<?php echo (!empty($logo)) ? $logo : "default_logo.png"; ?>"/>
		<div id='input_extras'></div>
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label class="col-lg-3 control-label required">Razon Social</label>
					<div class="col-lg-9">
						<input type="text" name="descripcion" id="descripcion" value="<?php echo (!empty($descripcion)) ? $descripcion : ""; ?>" class="form-control" required="">
					</div>
				</div>
								
				<div class="form-group">
					<label class="col-lg-3 control-label required">Direccion</label>
					<div class="col-lg-9">
						<input type="text" name="direccion" id="direccion" value="<?php echo (!empty($direccion)) ? $direccion : ""; ?>" class="form-control" required="">
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-lg-3 control-label required">RUC</label>
					<div class="col-lg-9">
						<input type="text" maxlength=11 name="ruc" id="ruc" value="<?php echo (!empty($ruc)) ? $ruc : ""; ?>" class="form-control" required="">
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-lg-3 control-label">Telefono</label>
					<div class="col-lg-9">
						<input type="text" name="telefono" id="telefono" value="<?php echo (!empty($telefono)) ? $telefono : ""; ?>" class="form-control" >
					</div>
				</div>
			</div>

			<div class="col-md-6">			
				<div class="form-group">
					<label class="col-lg-2 control-label">Logo</label>
					<div class="col-lg-9">
						<div id="load_photo" class="app-img-temp img-thumbnail">
							<?php
								$n_logo = '../app/img/empresa/default_logo.png';
								if(!empty($idempresa))
									$n_logo = '../../app/img/empresa/'.$logo;
							?>
							<img id="photo" src="<?php echo $n_logo;?>" class="img-responsive img-thumbnail" style="background:#f3f3f4;"/>
						</div>
					</div>
				</div>	
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

	<style type="text/css">
		.msg{
			color:red;
		}
	</style>