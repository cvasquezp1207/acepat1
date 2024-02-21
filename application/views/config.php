<div id="right-sidebar">
	<div class="sidebar-container">
		<ul class="nav nav-tabs navs-3">
			<li class="active">
				<a data-toggle="tab" href="#tab-1">Datos</a>
			</li>
			<li>
				<a data-toggle="tab" href="#tab-2"><i class="fa fa-gear"></i></a>
			</li>
		</ul>

		<div class="tab-content">
			<div id="tab-1" class="tab-pane active">
				<div class="sidebar-title">
					<h3><i class="fa fa-cube"></i> Datos predeterminados</h3>
				</div>
				<form class="form-config">
					<div class="setings-item">
						<span>Tipo venta</span>
						<div class="switch"><?php echo $tipo_venta;?></div>
					</div>
					<div class="setings-item">
						<span>Tipo documento</span>
						<div class="switch"><?php echo $tipo_documento;?></div>
					</div>
					<div class="setings-item">
						<span>Serie documento</span>
						<div class="switch"><input type="text" class="form-control input-xs input-config" name="serie" value="<?php echo $serie;?>"></div>
					</div>
					<div class="setings-item">
						<span>Almacen</span>
						<div class="switch"><?php echo $almacen;?></div>
					</div>
					<div class="setings-item">
						<span>Tipo pago</span>
						<div class="switch"><?php echo $tipo_pago;?></div>
					</div>
					<div class="setings-item">
						<span>Moneda</span>
						<div class="switch"><?php echo $moneda;?></div>
					</div>
					<div class="setings-item">
						<span>Vendedor</span>
						<div class="switch"><?php echo $vendedor;?></div>
					</div>
					<div class="setings-item">
						<div class="text-right"><button class="btn btn-primary btn-xs btn-config-save"><i class="fa fa-check"></i> Guardar</button></div>
					</div>
				</form>
			</div>
			<div id="tab-2" class="tab-pane">
				<div class="sidebar-title">
					<h3><i class="fa fa-gears"></i> Configuraciones</h3>
				</div>
				
				<div class="setings-item">
					<span>
						Ver notificaciones
					</span>
					<div class="switch">
						<div class="onoffswitch">
							<input type="checkbox" class="onoffswitch-checkbox" id="shownotification" <?php echo (isset($shownotification) && $shownotification == 'S') ? "checked" : ""; ?>>
							<label class="onoffswitch-label" for="shownotification">
								<span class="onoffswitch-inner"></span>
								<span class="onoffswitch-switch"></span>
							</label>
						</div>
					</div>
				</div>
				<div class="setings-item">
					<span>
						Deshabilitar Chat
					</span>
					<div class="switch">
						<div class="onoffswitch">
							<input type="checkbox" class="onoffswitch-checkbox" id="disablechat" <?php echo (isset($disablechat) && $disablechat == 'S') ? "checked" : ""; ?>>
							<label class="onoffswitch-label" for="disablechat">
								<span class="onoffswitch-inner"></span>
								<span class="onoffswitch-switch"></span>
							</label>
						</div>
					</div>
				</div>
				<div class="setings-item">
					<span>
						Usuarios fuera de línea
					</span>
					<div class="switch">
						<div class="onoffswitch">
							<input type="checkbox" class="onoffswitch-checkbox" id="offline_users" <?php echo (isset($offline_users) && $offline_users == 'S') ? "checked" : ""; ?>>
							<label class="onoffswitch-label" for="offline_users">
								<span class="onoffswitch-inner"></span>
								<span class="onoffswitch-switch"></span>
							</label>
						</div>
					</div>
				</div>
				<!--
				-->
				
				<!-- configuar plqantilla del sistema por usuario -->
				<!--
				<div class="setings-item">
                    <span>
                        Collapse menu
                    </span>

					<div class="switch">
						<div class="onoffswitch">
							<input type="checkbox" class="onoffswitch-checkbox" id="collapsemenu" <?php echo (isset($collapsemenu) && $collapsemenu == 'S') ? "checked" : ""; ?>>
							<label class="onoffswitch-label" for="collapsemenu">
								<span class="onoffswitch-inner"></span>
								<span class="onoffswitch-switch"></span>
							</label>
						</div>
					</div>
				</div>
				
				<div class="setings-item">
                    <span>
                        Top navbar
                    </span>

					<div class="switch">
						<div class="onoffswitch">
							<input type="checkbox" class="onoffswitch-checkbox" id="fixednavbar" <?php echo (isset($fixednavbar) && $fixednavbar == 'S') ? "checked" : ""; ?>>
							<label class="onoffswitch-label" for="fixednavbar">
								<span class="onoffswitch-inner"></span>
								<span class="onoffswitch-switch"></span>
							</label>
						</div>
					</div>
				</div>
				
				<div class="setings-item">
                    <span>
                        Boxed layout
                    </span>

					<div class="switch">
						<div class="onoffswitch">
							<input type="checkbox" class="onoffswitch-checkbox" id="boxedlayout" <?php echo (isset($boxedlayout) && $boxedlayout == 'S') ? "checked" : ""; ?>>
							<label class="onoffswitch-label" for="boxedlayout">
								<span class="onoffswitch-inner"></span>
								<span class="onoffswitch-switch"></span>
							</label>
						</div>
					</div>
				</div>
				
				<?php if(count($theme)){
					echo '<div class="title">Temas</div>';
				}
				
				foreach($theme as $k=>$v){
					echo '<div class="setings-item '.$v['clase_settings'].'" style="padding: 5px;">';
					echo '	<span class="skin-name ">';
					echo '		<a href="#" class="'.$v['clase_skin'].'">'.$v['nombre'].'</a>';
					echo '	</span>';
					echo '</div>';
				}
				?>
				-->
			</div>
		</div>
	</div>
</div>