<div class="row">
    <div class="col-lg-12">
		<div class="row" style="">
			<div class="col-sm-12" style="border:0px solid red;margin-bottom:0px;">
				<div class="" style="border:0px solid red;margin-bottom:-10px;">
					<div class="row">
						<div class="col-sm-4" style="border:0px solid red;">
							<button class="btn btn-success botoncito btn_save fa fa-file-o">&nbsp;&nbsp;Grabar Hoja Ruta</button>
							<div style="height:470px;border:0px solid red;overflow-y:scroll;">
								<?php echo $zonas;?>
							</div>
						</div>

						<div class="col-sm-6" style="border:0px solid red;background:white;">
							<div class="form-group">
								<input class="form-control" id="buscador" placeholder="Buscar Zona..." style="display:inline-block;"/>
							</div>
							
							<div class="form-group">
							<form id="form">
								<label>Cobradores</label>
								<?php
									echo $cobrador;
								?>
								<div style="height:375px;border:0px solid red;overflow-y:scroll;">
									<ul class="connectedSortable sortable sortable_connect ui-sortable" id="list_zona" data-padre="1" data-cob="id_cobrador"></ul>
								</div>
							</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
    </div>
</div>
 

<style>
	.sortable{		
		list-style-type: none;
		padding: 5px 0 0 0;
		margin-bottom:0px;
		height:auto;
		min-height:356px;
		border: 1px solid #ccc;
		background:white;
	}
	
	.sortable li{
		margin: 2px 0px 0px 2px;
		padding: 5px;
		font-size: 11px;
		font-weight:bold;
		width:97%;
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
  
	.botoncito{
		width: 100%
	}
	
	.cursor{cursor:pointer;font-size:18px}
	
	.ibox-content{background:transparent !important;}

	.tr-bold{font-weight: bold;}
	.tr-title{color: #b00;font-size:10px;}
	.centralriesgo_tr {background-color: #EDCCCC !important;}
	
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
}
  </style>
  
<script src="./app/js/jquery-2.1.1.js"></script>
<script src="./app/js/jquery-ui.js"></script>

<script>
	var drag_all = true;
	var sms = true;
	var xitem = 0;
	var multi_zona = "<?php echo $multi_zona;?>"
	if(multi_zona=='N'){		
		$( ".draggable" ).draggable({
			connectToSortable: ".sortable_connect"
			// ,helper: "clone"
			,revert: 'invalid'
			,stop: function( event, ui ) {
				verifi = controlar(ui.helper[0]);
			}
		});
	}else{
		$( ".draggable" ).draggable({
			connectToSortable: ".sortable_connect"
			,helper: "clone"
			,revert: 'invalid'
			,stop: function( event, ui ) {
				verifi = controlar(ui.helper[0]);
			}
		});
	}

	if(drag_all){
		$( ".sortable_connect" ).sortable({
			connectWith: ".sortable_connect"
			,placeholder: "ui-state-highlight"
			,items: "li:not(.ui-state-disabled)"
			,stop:function( event, ui ) {
				verifi = control(ui.item[0]);
			}
		}).disableSelection();
	}
  
    $("#buscador").keyup(function(){
		buscar = $(this).val();
		$('li.lista').removeClass('resaltar');
		if (jQuery.trim(buscar) != '') {
			$("li.lista:contains('" + buscar.toUpperCase() + "')").addClass('resaltar');
		}
	})

	$('.btn_save').click(function(){
		$band = true;
		
		if( $band ){
			ajax.post({url: _base_url+_controller+"/save/", data: $('#form').serialize()}, function(res) {
				ventana.alert({titulo: "Asignacion concluida", mensaje: "Asignacion realizada correctamente.", tipo:"success"}, function() {
					// redirect(_controller);
					$("#idempleado").trigger("change");
				});
			});
		}
	});

	$(document).ready(function(){
		setTimeout(function(){
			$("#idempleado").trigger("change");
		},100);
		
		$("#idempleado").on("change",function(){
			id = $("#idempleado").val();
			
			$("ul#list_zona").attr({"data-cob":id});
			ajax.post({url: _base_url+_controller+"/zona_cobrador/", data: "idempleado="+$("#idempleado").val()}, function(res) {
				$("#list_zona").html(res);
			});
		});
		
		$(document).on("click","a.delete_zona",function(e){
			li = $(this).parent("div").parent("li.lista");
			li.remove();
		});
	})
	
  
	function control(aqui){
		$new_padre   = $(aqui).parent('ul').attr('data-padre');
		$idempleado = $(aqui).parent('ul').attr('data-cob');
		valor_mov = $.trim($(aqui).text());

		$($(aqui).parent('ul').find('li')).each(function(e,u){
			if(!$($(this)[0]).hasClass('ui-state-disabled')){
				if($new_padre != 0)
					if( $.trim( $($(this)).text()) == valor_mov ){
						$($(this)).find('.inlista').hide();
						$($(this)).find('.in_list').show();
						$($(this)).find('input.idempleado').attr({'name':'idempleado[]','value':$idempleado});
					}
			}
		});
	}
	
	function controlar(aqui){
		$new_padre   = $(aqui).parent('ul').attr('data-padre');
		$idempleado = $(aqui).parent('ul').attr('data-cob');
		id_zona = $(aqui).attr("data-zona");
		$ul = $(aqui).parent('ul');

		exist = $ul.find("li.ui-state-none.draggable[data-zona="+id_zona+"]").length;
		if(exist>1){
			$(aqui).remove();
		}
	}
</script>