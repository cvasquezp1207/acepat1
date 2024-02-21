<div class="row">
	<div class="col-sm-12" style="border:0px solid red;margin-bottom:0px;">
		<div class="" style="border:0px solid red;margin-bottom:-10px;">
			<div class="row">
				<div class="col-sm-3" style="border:0px solid red;">
					<button class="btn btn-primary botoncito fa fa-mail-reply" id="regresar">&nbsp;&nbsp;Regresar </button>
				</div>

				<div class="col-sm-3" style="border:0px solid red;">
					<button class="btn btn-success botoncito btn_save fa fa-file-o">&nbsp;&nbsp;Grabar Asignacion</button>
				</div>
				
				<div class="col-sm-6" style="border:0px solid red;">
					<input class="form-control" id="buscador" placeholder="Buscar Empleado..." style="display:inline-block;"/>
				</div>
			</div>
		</div>
	</div>
	<br></br>
	<div id="contenido_form">
			<div class="col-sm-6 content_all" style="border:0px solid red;">
				<?php echo $empleados;?>
			</div>
			
			<form id="form">
			<?php
				echo $sucursal;
			?>
			</form>
	</div>
</div>
 
 <style>
	.sortable{		
		list-style-type: none;
		padding: 5px 0 0 0;
		margin-bottom:20px;
		height:auto;
		min-height:300px;
		border: 1px solid #ccc;
		background:white;
	}
		
	.sortable li{
		margin: 0 5px 1px 5px;
		padding: 5px;
		font-size: 11px;
		font-weight:bold;
		width:97%;
	}
	
	.botoncito{
		width: 100%;	
	}
	
	.idtipoempleado{
		width:85px;
		height:18px;
		border: 1px solid #ccc;
		border-radius: 3px;
	}
	
	.sortable_none{
		background:#1ab394;
		color:white !important;
		font-weight:bold;
		text-align:center;
		height:40px;
	}
  
	.lista{
		background: #f7f7f7;
		border-radius: 4px;
		border: 1px solid rgba(0,0,0,.2);
		border-bottom-color: rgba(0,0,0,.3);
		background-origin: border-box;
		background-image: -webkit-linear-gradient(top,#fff,#eee);
		background-image: linear-gradient(to bottom,#fff,#eee);
		text-shadow: 0 1px 0 #fff;
		font-weight:bold;
		width:97% !important;
	}
  
	.ui-state-none{
		
	}
	
	.cursor{cursor:pointer;font-size:18px}
	
	.ibox-content{background:transparent !important;}
	
	.ui-state-default-head{
		background:#293846;
		color:white !important;
		font-weight:bold !important;
		text-align:center;
	}
  
	.ui-state-highlight { height: 1.5em; line-height: 1.2em;border: 1px dashed #CCC !important; background: #f0f0f0 !important;}
  
	li.ui-state-disabled{
		opacity: 1 !important;
	}
	
	select.form-control{
		padding:0px;
		display: inline-block;
		font-size:13px;
	}
	.form-control-req.ui-state-error{
		border: 1px solid #f1a899;
		background: #fddfdf;
		color: #5f3f3f;
	}
	
	.btn.idtipoempleado_select{
		padding: 2px 5px!important;
	}
	
	.dropdown-menu li{
		margin: 0px !important;
		padding: 0px !important;
		width:100% !important;
	}
	
	.dropdown-menu > li > a{
		border-radius: 1px !important;
		line-height: 15px !important;
		padding: 2px 5px;
		margin:0px !important;
		/*border-bottom:1px solid #c1c1c1;*/
	}
	
	.grabado,.lista{font-size:10px !important;}
	
	.dropdown-menu>li>a:hover {
		background-color: #1e90ff !important;
	}
	
	.btn .caret {
		margin-right: 3px;
		float: right;
		margin-top: 5px;
	}
	
	.resaltar{
		background: #cfe7fa;
		background: -moz-linear-gradient(top, #cfe7fa 0%, #6393c1 100%);
		background: -webkit-linear-gradient(top, #cfe7fa 0%,#6393c1 100%);
		background: linear-gradient(to bottom, #cfe7fa 0%,#6393c1 100%);
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#cfe7fa', endColorstr='#6393c1',GradientType=0 );
		color: black;
	}
  </style>
  
  <script src="../app/js/jquery-2.1.1.js"></script>
  <script src="../app/js/jquery-ui.js"></script>
  <script>
	var drag_all = true;
	var sms = true;
	var xitem = 0;

	$('.idtmp').hide();
	
	// $(document).on('change','.idtipoempleado',function(){//pendiente para revisar
		// cont = recorrer_ul( $(this) );
		// if(parseInt(cont)>1){
			// mensaje($(this));
		// }
	// })
	
	$(document).on('click','.li_tipoempleado',function(){//pendiente para revisar
		$(this).parent('li').parent('ul').parent('.btn-group').find('button').html(""+$(this).text());
		$(this).parent('li').parent('ul').parent('.btn-group').find('input.idtipoempleado').val( $(this).parent('li').attr("data-value") );

		cont = recorrer_ul( $(this).parent('li').parent('ul').parent('.btn-group') );
		if(parseInt(cont)>1){
			mensaje($(this).parent('li').parent('ul').parent('.btn-group'));
		}
	})
	
	$( ".draggable" ).draggable({
		connectToSortable: ".sortable_connect",
		helper: "clone"
		,revert: false
		,stop: function( event, ui ) {
			verifi = control(ui.helper[0]);
			if(parseInt(verifi)>1){
				// LimpiarClon(ui.helper[0]);
			}
			xitem = 0;
		}
    });
	
	if(drag_all){
		$( ".sortable_connect" ).sortable({
			 connectWith: ".sortable_connect"
			,placeholder: "ui-state-highlight"
			,items: "li:not(.ui-state-disabled)"
			,stop:function( event, ui ) {
				$(ui.item[0]).each(function(i,j){
					// UpdateName($(this).find('input.codparametrocartera').attr('id'),$(this));
				});
			}
		}).disableSelection();
	}
  // });
  
    $("#buscador").keyup(function(){
		buscar = $(this).val();
		$('li.lista').removeClass('resaltar');
		if (jQuery.trim(buscar) != '') {
			$("li.lista:contains('" + buscar.toUpperCase() + "')").addClass('resaltar');
			// console.log("Here...");
		}else
			console.log(buscar);
	})
  
  function control(aqui){
	$new_padre   = $(aqui).parent('ul').attr('data-padre');
	$id_sucursal = $(aqui).parent('ul').attr('data-sucu');
	valor_mov = $.trim($(aqui).text());
	$($(aqui).parent('ul').find('li')).each(function(e,u){
		if(!$($(this)[0]).hasClass('ui-state-disabled')){
			if($new_padre != 0)
				if( $.trim( $($(this)).text()) == valor_mov ){
					xitem++;
					$($(this)).find('.eliminar').show();
					$($(this)).find('.inlista').hide();
					$($(this)).find('button.idtipoempleado_select').removeClass('idtmp').addClass('form-control-req').show();//pendiente
					// $($(this)).find('select.idtipoempleado').removeClass('idtmp').show().addClass('form-control');//pendiente
					$($(this)).find('input.idsucursal').val($id_sucursal);
					$('.deletito').bind('click',function(e){
						e.preventDefault();
						Eventodelete( $(this) );
					})
				}
		}
	});
	return xitem;
  }
  
	function LimpiarClon(aqui){
		$input			= $(aqui).parent('div.pull-right');
		$empleado = $input.find('input.idusuario').attr('data-name');
		$sucursal = $(aqui).parent('div.pull-right').parent('li').parent('ul').find('li.ui-state-disabled');
		
		if(sms){
			ventana.alert({titulo: "Dato Repetido", mensaje: "El empleado sera eliminado de la lista, por que ya existe en la sucursal seleccionada."}, function() {
				$(aqui).fadeOut(800,function(){
					$(aqui).remove();
				});
			});
		}else{
			$(aqui).fadeOut(1000,function(){
				$(aqui).remove();
			});		
		}
	}
  
	function mensaje(select){
		ventana.alert({titulo: "Dato Repetido", mensaje: "El empleado no puede tener el mismo rol.",tipo: 'error'}, function() {
			// select.val('');
			select.find('button').html('');
			select.find('input.idtipoempleado').val('');
		});
	}
  
	function UpdateName(id,aqui){
		$new_padre = aqui.parent('ul').attr('data-padre');	
		$('#'+id).val($new_padre);
	}
  
	function recorrer_ul(select){
		$x_item = 0;
		$_li 		= select.parent('div').parent('li').parent('ul').find('li.lista');
		// $_sujeto 	= select.parent('div').parent('li')[0]['innerText'];
		$_sujeto 	= select.parent('div').parent('li').attr("data-text");

		$_li.each(function(i,j){
			// $texto =  $(j)[0]['innerText'];
			$texto =  $(j).attr("data-text");
			// console.log( $(j).find('.idtipoempleado').val() );
			if( $texto == $_sujeto)
				if( select.find('input.idtipoempleado').val() ==  $(j).find('.idtipoempleado').val())
					$x_item++;
		})
		return $x_item;
	}
</script>