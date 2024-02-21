<div class="row">
	<div class="col-sm-12" style="border:0px solid red;margin-bottom:0px;">
		<div class="ibox" style="border:0px solid red;margin-bottom:-10px;">
			<div class="ibox-content">
                <button class="btn btn-success botoncito btn_save fa fa-file-o">&nbsp;&nbsp;Grabar Asignacion</button>
			</div>
		</div>
	</div>
	
		<div id="contenido_form">
			<div class="col-sm-4 content_all" style="border:0px solid red;">
				<?php echo $sistemas;?>
			</div>
			
			<form id="form">
			<?php
				echo $sistema_sucursal;
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
		font-size: 12px;
		font-weight:bold;
		width:97%;
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
	
	.cursor{cursor:pointer;font-size:20px}
	
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
	.form-control.ui-state-error{
		border: 1px solid #f1a899;
		background: #fddfdf;
		color: #5f3f3f;
	}
  </style>
  
  <script src="../app/js/jquery-2.1.1.js"></script>
  <script src="../app/js/jquery-ui.js"></script>
  <script>
	var drag_all = true;
	var sms = true;
	var xitem = 0;

	$('.idtmp').hide();
  // $(function() {
	$( ".draggable" ).draggable({
		connectToSortable: ".sortable_connect",
		helper: "clone"
		,revert: false
		,stop: function( event, ui ) {
			verifi = control(ui.helper[0]);
			if(parseInt(verifi)>1){
				LimpiarClon(ui.helper[0]);
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

	$('.btn_save').click(function(){
		ajax.post({url: _base_url+"sistema/save_detalle_sucu/", data: $('#form').serialize()}, function(res) {
			ventana.alert({titulo: "Asignacion concluida", mensaje: "Asignacion realizada correctamente.", tipo:"success"}, function() {
				redirect(_controller);
			});
		});
	})
  // });
  
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
					// $($(this)).find('.inlista').hide();
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
  
  function UpdateName(id,aqui){
	$new_padre = aqui.parent('ul').attr('data-padre');	
	$('#'+id).val($new_padre);
  }	
</script>