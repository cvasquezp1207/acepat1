<div id="right-chat">
	<div class="sidebar-container">
		<div class="sidebar-title" style="padding:5px 0px 5px 15px;">
			<h3><i class="fa fa-group"></i> Usuarios</h3>
		</div>
		
		<div class="setings-item">
			<div class="form-chat">
				<input type="text"id="fu" name="fu" placeholder="Buscar usuario..." class="form-control input-xs" />
            </div>
		</div>

		<div class="setings-item dlu">
			<ul class="dropdown-messages" id="dropdown-chat" style="">
			<?php
			$html_ul ="";
			foreach($usuarios as $k=>$v){
				$photo	= $v["avatar"];
				$ruta	= "app/img/usuario/";
				if(!empty($photo)){
					if(file_exists($ruta.$photo)){
						$photo = base_url("{$ruta}{$photo}");
					}else{
						$photo = base_url($ruta."anonimo.png");
					}
				}else{
					$photo = base_url($ruta."anonimo.png");
				}
				
				$class_active = "avatar-offline";
				if($v["online"]=='S')
					$class_active = "avatar-online";
				$html_ul.= "<li index='{$v['idusuario']}' data-user-name='{$v['nombres']}' data-avatar='{$photo}' data-sucursal-online='{$v['sucursal']}' data-status='{$class_active}'>";
				$html_ul.= "	<div class='dropdown-messages-box' style='margin-bottom:5px;'>";
				$html_ul.= "		<a href='#' class='pull-left '>";
				$html_ul.= "			<img alt='image' class='img-circle-chat' src='{$photo}' />";
				$html_ul.= "			<span class='{$class_active}'><i class='fa fa-circle'></i></span>";
				$html_ul.= "		</a>";
				$html_ul.= "		<div class='media-body' style='margin-left:25px;'>";
				$html_ul.= "			<strong>{$v['user_nombres']}</strong>";
				$html_ul.= "			<div><small class='text-muted'>{$v['sucursal']}</small></div>";
				$html_ul.= "		</div>";
				$html_ul.= "	</div>";
				$html_ul.="</li>";
			}
			echo $html_ul;
			?>
			</ul>
		</div>
	</div>
</div>

<div id="chat_compose_wrapper">
	<div class="">
		<div class="sidebar-title dropdown-messages-box" style="padding:15px 12px 11px 10px;">
			<a href='#' class='pull-left '>
				<img alt='image' class='img-circle-chat' id="avatar_user_chat" src='' />
				<span class='' id="status_chat_user"><i class='fa fa-circle'></i></span>
			</a>
			<div class='media-body' style='margin-left:25px;'>
				<a href='#' id="close_chat" class='pull-right'>
					<i class="fa fa-close"></i>
				</a>
				
				<strong id="name_user_chat" style="font-size:11px;"> {}</strong>
				<div><small class='text-muted' id="sucursal_online">{$v['sucursal']}</small></div>
			</div>
		</div>
		<div class="full-height-scroll" style="">
			<div class="lg-chat-box">
				<div class="content">
				</div>
			</div>		
			<div class="form-chat">
				<div class="">
					<input type="text" class="form-control" id="input-send" placeholder="Escriba un mensaje...">
					<!--
					<span class="input-group-btn">
						<button class="btn btn-primary" type="button">Send
						</button>
					</span>
					-->
				</div>
			</div>
		</div>
	</div>
</div>