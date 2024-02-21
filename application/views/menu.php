<?php 

$file = base_url("application/uploads/usuario/".$avatar);
if(!file_exists($file)) {
	$file = base_url("app/img/usuario/M1.jpg");
	//$file = base_url("app/img/usuario/".$sexo."1.png");
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
				<div class="dropdown profile-element"> <span>
					<img alt="image" class="img-circle" src="<?php echo $file; ?>">
					 </span>
					<a data-toggle="dropdown" class="dropdown-toggle" href="#">
					<span class="clear"> <span class="block m-t-xs"> <strong class="font-bold"><?php echo $name; ?></strong>
					 </span> <span class="text-muted text-xs block"><?php echo $perfil; ?> <b class="caret"></b></span> </span> </a>
					<ul class="dropdown-menu animated fadeInRight m-t-xs">
						<li><a href="#">Cambiar imagen</a></li>
						<li><a href="#">Cambiar contrase&ntilde;a</a></li>
						<li class="divider"></li>
						<li><a href="<?php echo base_url("login/salir");?>">Salir</a></li>
					</ul>
				</div>
				<div class="logo-element">
					IN+
				</div>
			</li>
			<?php 
			if(!empty($menus)) {
				foreach($menus as $val) {
					$cls = '';
					if($val["idmodulo"]==$menu_p) {
						$cls='active';
					}
					echo '<li class="'.$cls.'">';
					
					echo '<a href="#">';
					if(!empty($val["icono"])) {
						echo '<i class="fa '.$val["icono"].'"></i> ';
					}
					// echo '<span class="nav-label">'.strtolower($val["descripcion"]).'</span>';
					echo '<span class="nav-label">'.$val["descripcion"].'</span>';
					if(!empty($val["submenus"])) {
						echo '<span class="fa arrow"></span>';
					}
					echo '</a>';
					
					// submenus
					if(!empty($val["submenus"])) {
						echo '<ul class="nav nav-second-level">';
						foreach($val["submenus"] as $s) {
							$cls = '';
							if($s["idmodulo"]==$menu_c) {
								$cls='active';
							}
							echo '<li class="'.$cls.'"><a href="'.base_url($s["url"].'?mop='.$val["idmodulo"].'&moc='.$s["idmodulo"]).'">';
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
			?>
		</ul>
	</div>
</nav>