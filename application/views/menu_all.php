<?php 

$file = base_url("app/img/usuario/".$avatar);
// echo base_url("app/img/usuario/".$avatar);
// if(!is_file($file)) {//No esta funcionando
	// $file = base_url("app/img/usuario/".$sexo."1.png");
// }

if(empty($avatar)){
	$file = base_url("app/img/usuario/".$sexo."1.png");
}

$name = strtolower($nombres)." ".strtolower($appat);
$perfil = (!empty($perfil)) ? strtolower($perfil) : "";
$menu_p = (!empty($menu_p)) ? $menu_p : "";
$menu_c = (!empty($menu_c)) ? $menu_c : "";

?>
<nav class="navbar-default navbar-static-side" role="navigation">
	<div class="sidebar-collapse">
		<ul class="nav metismenu" id="side-menu">
			<li class="nav-header">
				<div class="dropdown profile-element"> 
					<span>
						<img data-toggle="tooltip" title="Cambiar Imagen" alt="image" id="avatar_session" class="img-circle" src="<?php echo $file; ?>">
					</span>
					<a data-toggle="dropdown" class="dropdown-toggle" href="#">
					<span class="clear"> <span class="block m-t-xs"> <strong class="font-bold"><?php echo $name; ?></strong>
					 </span> <span class="text-muted text-xs block"><?php echo $perfil; ?> <b class="caret"></b></span> </span> </a>
					<ul class="dropdown-menu animated fadeInRight m-t-xs">
						<li><a id="change_avatar" href="#">Cambiar imagen</a></li>
						<li><a id="change_clave" href="#">Cambiar contrase&ntilde;a</a></li>
						<li class="divider"></li>
						<li><a class="logout_open" href="#">Salir</a></li>
						<!--<li><a href="<?php echo base_url("login/salir");?>">Salir</a></li>-->
					</ul>
				</div>
				<div class="logo-element">
					IN+
				</div>
			</li>
			<?php 
			if(!empty($menus)) {
				$arr_color = array("primary", "warning", "danger");
				
				foreach($menus as $val) {
					$color = "primary";
					// $color = $arr_color[ rand(0, 2) ];
					
					echo '<li class="item-nav-sistema item-hide" style="display: block;">';
					// echo '<button class="btn btn-'.$color.' btn-block btn-outline btn-sel-sistema" pkey="'.$val["idsistema"].'">';
					echo '<a href="#" class="btn-sel-sistema" pkey="'.$val["idsistema"].'">';
					if(!empty($val["image"])) {
						echo '<i class="fa '.$val["image"].' fa-2x"></i> ';
						// echo '<i class="fa '.$val["image"].'"></i> ';
					}
					// echo strtolower($val["descripcion"]).'</button></li>';
					echo '<span class="nav-label">'.strtolower($val["descripcion"]).'</span></a></li>';
					
					// retornar al sistema
					echo '<li class="item-nav-menu item-nav-back item-hide" pkey="'.$val["idsistema"].'">';
					echo '<a href="#" class="item-back"><i class="fa fa-arrow-left"></i> '.strtolower($val["descripcion"]).'</a></li>';
					
					// menu
					if(!empty($val["menus"])) {
						foreach($val["menus"] as $m) {
							$cls = '';
							if($m["idmodulo"]==$menu_p) {
								$cls='active';
							}
							echo '<li class="item-nav-menu item-hide '.$cls.'" pkey="'.$val["idsistema"].'">';
							
							echo '<a href="#">';
							if(!empty($m["icono"])) {
								echo '<i class="fa '.$m["icono"].'"></i> ';
							}
							// echo '<span class="nav-label">'.strtolower($m["descripcion"]).'</span>';
							echo '<span class="nav-label">'.$m["descripcion"].'</span>';
							if(!empty($m["submenus"])) {
								echo '<span class="fa arrow"></span>';
							}
							echo '</a>';
							
							// submenus
							if(!empty($m["submenus"])) {
								echo '<ul class="nav nav-second-level">';
								foreach($m["submenus"] as $s) {
									$cls = '';
									if($s["idmodulo"]==$menu_c) {
										$cls='active';
									}
									$http = strpos($s['url'], "http");
									if ($http !== false) {
										echo '<li class="item-menu ' . $cls . '"><a href="' . $s["url"] . '/index?idusuario=' . $_SESSION['idusuario'] . '&idempresa=' . $_SESSION['idempresa'] . '&idsucursal=' . $_SESSION['idsucursal'] . '" ikey="' . $s["idmodulo"] . '">';
									} else {
										echo '<li class="item-menu ' . $cls . '"><a href="' . base_url($s["url"] . '?mop=' . $m["idmodulo"] . '&moc=' . $s["idmodulo"]) . '" ikey="' . $s["idmodulo"] . '">';
									}
									if(!empty($s["icono"])) {
										echo '<i class="fa '.$s["icono"].'"></i> ';
									}
									// echo strtolower($s["descripcion"]).'</a></li>';
									echo $s["descripcion"].'</a></li>';
								}
								echo '</ul>';
							}
							
							echo '</li>';
						}
					}
				}
			}
			?>
		</ul>
	</div>
</nav>

<!-- FORM CHANGE AVATAR -->
<div id="modal-change-avatar" class="modal fade" aria-hidden="false" data-backdrop="static" >
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Cambiar avatar</h4>
            </div>   
            <div class="modal-body">
                <div class="row">
                    <form id="form-change-avatar">
						<input type="file" name="file_avatar" id="file_avatar" style="display: none;" onchange='leer_archivo(this)' />
						<div class="form-group">
							<div class="row">
								<div class="col-sm-12">
									<center>
										<div id="load_avatar" class="app-img-temp img-thumbnail load_photo">
											<img id="photoAvatar" src="<?php echo $file;?>" class="img-responsive img-thumbnail photo" style="background:#f3f3f4;"/>
										</div>
									</center>
								</div>
							</div>
                        </div>
						
                        <div class="form-group">
							<div class="sms_avatar alert alert-danger" style="display:none;"></div>
						</div>
                    </form>
                </div>
            </div>
			
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				<button type="button" id="save_avatar" class="btn btn-primary">Cambiar avatar</button>
			</div>
        </div>    
    </div>    
</div>
<!-- FORM CHANGE AVATAR -->



<!-- FORM CHANGE PASS -->
<div id="modal-change-pass" class="modal fade" aria-hidden="false" data-backdrop="static" >
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Cambiar Clave</h4>
            </div>   
            <div class="modal-body">
                <div class="row">
                    <form id="form-change-clave">
						<div class="form-group">
							<div class="row">
								<div class="col-sm-12">
									<label>Clave Anterior</label>
									<div class="input-group">
										<input type="password" class="form-control" name="clave_anterior" id="clave_anterior" ></input>
										<span class="input-group-btn tooltip-demo">
											<button type="button" id="btn-verificar-pass" class="btn btn-outline btn-success " data-toggle="tooltip" title="Verificar la clave">
												<i class="fa fa-unlock"></i>
											</button>
										</span>
									</div>
								</div>
							</div>
                        </div>
						
						<div class="form-group">
							<div class="row">
								<div class="col-sm-12">
									<label>Clave Nueva</label>
									<input type="text" class="form-control" name="clave_nueva" id="clave_nueva" readonly="readonly"></input>
								</div>
							</div>
                        </div>
						
                        <div class="form-group">
							<div class="sms_pass alert alert-danger" style="display:none;"></div>
						</div>
                    </form>
                </div>
            </div>
			
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				<button type="button" id="save_clave" class="btn btn-primary">Cambiar clave</button>
			</div>
        </div>    
    </div>    
</div>
<!-- FORM CHANGE PASS -->
<style>
	#photoAvatar ,#avatar_session{width:48px;height:48px;}
</style>