<form id="form_<?php echo $controller; ?>" class="" enctype="multipart/form-data">
	<input type="hidden" name="idusuario" id="idusuario" value="<?php echo (!empty($idusuario)) ? $idusuario : ""; ?>">
	<input type="file" name="file" id="file" style="display: none;" onchange='leerarchivobin(this)' />
	<input type="hidden" id="avatar" name="avatar" value="<?php echo (!empty($avatar)) ? $avatar : "anonimo.png"; ?>"/>
	<input type="hidden" id="avatar_new" name="avatar_new" value="<?php echo (!empty($avatar)) ? $avatar : "anonimo.png"; ?>"/>
	<input type="hidden" name="clave" id="clave"  value="<?php echo (!empty($clave)) ? $clave : ""; ?>">
	<input type="hidden" name="clave_past" id="clave_past"  value="<?php echo (!empty($clave)) ? $clave : ""; ?>">
	
	<div class="modal-body">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="" class="required">Nombres</label>
					<input type="text" class="form-control"  id="nombres" name="nombres" required=""  value="<?php echo (!empty($nombres)) ? $nombres : ""; ?>" >
				</div>

				<div class="form-group">
					<label for="" class="required">Apellido Paterno</label>
					<input type="text" class="form-control"  id="appat" name="appat" required="" value="<?php echo (!empty($appat)) ? $appat : ""; ?>" >
				</div>
								
				<div class="form-group">
					<label for="" class="required">Apellido Materno</label>
					<input type="text" class="form-control"  id="apmat" name="apmat" required="" value="<?php echo (!empty($apmat)) ? $apmat : ""; ?>" >
				</div>

				<div class="form-group">
					<label for="" class="required">Direccion</label>
					<input type="text" class="form-control"  id="direccion" name="direccion" required="" value="<?php echo (!empty($direccion)) ? $direccion : ""; ?>" >
				</div>

				<div class="form-group">
					<label for="" class="required">DNI</label>
					<input type="text" class="form-control"  id="dni" name="dni" required="" value="<?php echo (!empty($dni)) ? $dni : ""; ?>" >
				</div>

				<div class="form-group">
					<label for="" class="required">Nick</label>
					<div class="input-group">
						<input type="text" class="form-control" id="usuario" name="usuario" required="" value="<?php echo (!empty($usuario)) ? $usuario : ""; ?>" >
						<span class="input-group-addon"><i class="fa fa-user"></i></span>
					</div><br>
				</div>
				
				<div class="form-group">
					<label for="" class="required">Clave</label>
					
					<div class="input-group">
						<input type="text" class="form-control clavesita" 		<?php if(!empty($idusuario)){echo "readonly=''";} ?> aria-describedby="sizing-addon2" name="p2" id="p2" required="" value="<?php echo (!empty($clave)) ? $clave : ""; ?>">
						<input type="password" class="form-control clavesita" 	<?php if(!empty($idusuario)){echo "readonly=''";} ?>aria-describedby="sizing-addon2" name="p1" id="p1" required="" value="<?php echo (!empty($clave)) ? $clave : ""; ?>">
						<span class="input-group-addon"><i class="fa fa-key"></i></span>								  	
					</div>
				</div>
								
				<div class="checkbox checkbox-primary">
                    <input id="view" type="checkbox" checked="" name="view">
                    <label for="view">Mostrar contrase&ntilde;a</label>
                </div>

                <?php
                if(!empty($idusuario)) 
                	echo '	<div class="checkbox checkbox-success">
                    			<input id="change_pass" name="change_pass" type="checkbox" >
                    			<label for="change_pass">Nueva Clave</label>
                			</div>';
                ?>
			</div>

			<div class="col-md-6">
				<div class="form-group">
					<label for="" class="">Avatar</label><br>
					<div id="load_photo" class="app-img-temp img-thumbnail">
						<?php
							$n_logo = '../app/img/usuario/anonimo.png';
							if(!empty($idusuario))
								$n_logo = '../../app/img/usuario/'.$avatar;
						?>
						<img id="photo" src="<?php echo $n_logo;?>" class="img-responsive img-thumbnail" style="background:#f3f3f4;height:170px;"/>
					</div>
				</div>

				<div class="form-group">
					<label for="" class="">Telefono</label>
					<div class="input-group">
						<input type="text" class="form-control" id="telefono" name="telefono" aria-describedby="sizing-addon2"  value="<?php echo (!empty($telefono)) ? $telefono : ""; ?>" >
						<span class="input-group-addon"><i class="fa fa-phone"></i></span>
					</div>
				</div>

				<div class="form-group">
					<label for="" class="">E-mail</label>
					<div class="input-group">
						<input type="text" class="form-control" placeholder="E-mail" id="email" name="email" aria-describedby="sizing-addon2" value="<?php echo (!empty($email)) ? $email : ""; ?>" >
						<span class="input-group-addon" id="sizing-addon2">@</span>
					</div>
				</div>

				<div class="form-group">
					<label class="required">Fecha Nacimiento</label>
					<div class="input-group date">
						<input type="text" placeholder="dd/mm/Y" name="fecha_nac" id="fecha_nac" value="<?php echo (!empty($fecha_nac)) ? dateFormat($fecha_nac, "d/m/Y") : ""; ?>" class="form-control" required="">
						<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					</div>
				</div><br>
					
				<div class="form-group">
					<label for="" class="required">Sexo</label>
					<div>
						<?php
							echo radio_sexo((!empty($sexo)) ? $sexo : "A");
						?>
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