<div class="row border-bottom">
	<nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
        </div>
		<ul class="nav navbar-top-links navbar-right">
			<?php if(!empty($sucursal)) { ?>
			<li class="tooltip-demo">
				<a href="<?php echo base_url("home/seleccion_sucursal"); ?>" data-toggle="tooltip" data-placement="bottom" title="Cambiar sucursal">
				<i class="fa fa-map-marker"></i> <label class="label"><?php echo $sucursal; ?></label></a>
			</li>
			<?php } ?>
			<?php if(!empty($sistema)) { ?>
			<li class="tooltip-demo">
				<a href="<?php echo base_url("home/seleccion_sistema"); ?>" data-toggle="tooltip" data-placement="bottom" title="Cambiar sistema">
				<i class="fa fa-desktop"></i> <label class="label label-info"><?php echo $sistema; ?></label></a>
			</li>
			<?php } ?>
			
			<li class="dropdown <?php echo ($alerta["cant"]>0) ? "show_alert" : ""; ?>" id="shownotification_icon" style="display:<?php echo ($shownotification=='N') ? "none" : ""; ?>">
				<!--
				-->
				<?php
				if(!empty($alerta['list'])){
				?>
				<a class="dropdown-toggle count-info " data-toggle="dropdown" href="#">
					<i class="fa fa-bell"></i>  <span class="label label-primary"><?php echo ($alerta['cant']);?></span>
				</a>
				<ul id="dropdown-menu-alert" class="dropdown-menu dropdown-alerts">
					<?php
						foreach($alerta['list'] as $k=>$v){
							if($k<6){
								echo "<li>";
								echo "	<a href='#' style='cursor:default;'>";
								echo "		<div>";
								echo "			<i class='fa fa-hand-o-right fa-fw'></i> <span style='font-size:11px;'>".($v['sms_alerta']."</span> ");
								echo "			<span class='pull-right text-muted small'>{$v['hora_alerta']}</span>";
								echo "		</div>";
								echo "	</a>";
								echo "</li>";
								if($alerta['cant']>1)
									echo "<li class='divider'></li>";								
							}else{
								break;
							}
						}
						if($alerta['cant']>5){
					?>
								<li>
									<div class="text-center link-block">
										<a href="#" id="ver_alertas" style=''>
											<strong>Ver Todo</strong>
											<i class="fa fa-angle-right"></i>
										</a>
									</div>
								</li>
					<?php
						}
					?>
				</ul>
				<?php
				}
				?>
			</li>
			
			<?php if(isset($idsucursal) && $modo_prueba_chat=='S'){
			?>
			<li class="tooltip-demo" style="display:<?php echo ($disablechat=='S') ? "none" : ""; ?>" id="disablechat_icon">
				<a class="dropdown-toggle  right-chat-toggle" data-toggle="tooltip" id="" href="#" data-placement="bottom" title="Inicie una conversacion">
					<i class="fa fa-wechat " style="color:#0084ff;font-size:20px;" ></i>
				</a>
			</li>
			<?php
			}?>

			<li>
				<a class="logout_open" href="#logout">
					<i class="fa fa-sign-out"></i> Salir
				</a>
			</li>
			
			<li class="tooltip-demo">
				<a class="right-sidebar-toggle" data-toggle="tooltip" data-placement="left" title="Configurar sistema">
					<i class="fa fa-tasks"></i>
				</a>
			</li>
		</ul>
	</nav>
</div>