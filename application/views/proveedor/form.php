<form id="form_<?php echo $controller; ?>" class="form-horizontal app-form">
	<input type="hidden" name="idproveedor" id="<?php echo $prefix; ?>idproveedor" value="<?php echo (!empty($idproveedor)) ? $idproveedor : ""; ?>">

	<div class="modal-body">
		<div class="row">
			<div class="col-md-5">
				<div class="">
					<label for="" class="required">Razon Social</label>
					<input type="text" name="nombre" id="<?php echo $prefix; ?>nombre" placeholder="Nombre" value="<?php echo (!empty($nombre)) ? $nombre : ""; ?>" class="form-control" required=""/>
				</div>
				<br>
			</div>

			<div class="col-md-7">
				<label class="required">Direcci&oacute;n</label>
				<div class="input-group">
					<input type="text" name="direccion" id="<?php echo $prefix; ?>direccion" value="<?php echo (!empty($direccion)) ? $direccion : ""; ?>" class="form-control" required="">
					<span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
				</div>
				<br>
			</div>
		</div>

		<div class="row">
			<div class="col-md-5">
				<label class="">E-MAIL</label>
				<div class="input-group email">
					<input type="text" type="email" placeholder="Email" name="email" id="<?php echo $prefix; ?>email" value="<?php echo (!empty($email)) ? $email : ""; ?>" class="form-control" >
					<span class="input-group-addon">@</span>
				</div>
				<br>
			</div>

			<div class="col-md-4">
				<div class="">
					<label class="required">RUC</label>
					<input type="text" name="ruc" maxlength=11 id="<?php echo $prefix; ?>ruc" value="<?php echo (!empty($ruc)) ? $ruc : ""; ?>" class="form-control" required="">
				</div>
				<br>
			</div>

			<div class="col-md-3">
				<label class="">Tel&eacute;fono</label>
				<div class="input-group email">
					<input type="text" name="telefono" id="<?php echo $prefix; ?>telefono" value="<?php echo (!empty($telefono)) ? $telefono : ""; ?>" class="form-control">
					<span class="input-group-addon"><i class="fa fa-phone"></i></span>
				</div>
				<br>
			</div>
		</div>
	</div>	

	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<!--
			<button type="submit" id="btn_save" class="btn btn-sm btn-primary" data-controller="<?php echo $controller; ?>">Guardar</button>
			<button id="btn_cancel" class="btn btn-sm btn-white" data-controller="<?php echo $controller; ?>">Cancelar</button>-->
			<button type="submit" id="<?php echo $prefix; ?>btn_save" class="btn btn-sm btn-primary" data-controller="<?php echo $controller; ?>">Guardar</button>
			<button id="<?php echo $prefix; ?>btn_cancel" class="btn btn-sm btn-white btn_cancel" 	 data-controller="<?php echo $controller; ?>">Cancelar</button>
		</div>
	</div>
</form>