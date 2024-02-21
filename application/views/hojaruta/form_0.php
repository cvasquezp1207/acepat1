<input type="hidden" id="row_smart" value="<?php echo($carterita['cantidad']);?>"></input>
<input type="hidden" class="combito" ></input>

<div class="row">
    <div class="col-lg-12">
        <div class="tabs-container">
            <ul class="nav nav-tabs">
                <li class="active">
                	<a data-toggle="tab" href="#tab-1"><i class="fa fa-sitemap"></i>Cartera Credito</a>
                </li>
                <li class="">
                	<a data-toggle="tab" href="#tab-2"><i class="fa fa-database"></i>Hoja de Ruta</a>
                </li>
            </ul>
            
            <div class="tab-content">
                <div id="tab-1" class="tab-pane active">
                    <div class="panel-body">
                        <div class="row" style="">
							<div class="col-sm-12" style="border:0px solid red;margin-bottom:0px;">
								<div class="" style="border:0px solid red;margin-bottom:-10px;">
									<div class="row">
										<div class="col-sm-3" style="border:0px solid red;">
											<button class="btn btn-success botoncito btn_save fa fa-file-o">&nbsp;&nbsp;Grabar Hoja Ruta</button>
										</div>
										
										<div class="col-sm-9" style="border:0px solid red;">
											<input class="form-control" id="buscador" placeholder="Buscar Zona..." style="display:inline-block;"/>
										</div>
										
									</div>
								</div>
							</div>
							<br></br>
							<div id="contenido_form">
									<div class="col-sm-3 content_all" style="border:0px solid red;">
										<?php echo $zonas;?>
									</div>
									
									<form id="form">
									<?php
										echo $zona_x_cobrador;
									?>
									</form>
							</div>
						</div>
                    </div>
                </div>
                
                <div id="tab-2" class="tab-pane">
                    <div class="panel-body">
                        <div class="row" style="">
                        	<div class="col-md-3">
	                        	<form id="parametros">
	                        		<div class="row" style="">
	                        			<div class="col-md-12">
											<div class="form-group">
												<label class="">Cobrador</label>
												<?php echo $cobradores_empleados;?>
											</div>
										</div>	
									</div>

									<div class="row" style="">
										<div class="col-md-6">
											<div class="form-group">
												<label class="">Estado Credito</label>
												<?php echo $estadocredito;?>
											</div>
										</div>

										<div class="col-md-6">
											<div class="form-group">
												<label class="">Nro Credito</label>
												<input name="nro_credito" id="nro_credito" class="form-control"></input>
											</div>
										</div>
									</div>

									<div class="row" style="">
	                        			<div class="col-md-12">
											<div class="form-group">
												<label class="">Zona</label>
												<?php echo $zona;?>
											</div>
										</div>	
									</div>

									<div class="row" style="">
	                        			<div class="col-md-12">
											<div class="form-group">
												<label class="">Cliente</label>
												<input name="cliente" id="cliente" class="form-control"></input>
											</div>
										</div>	
									</div>

									<div class="row" style="">
										<div class="col-md-12">
											<div class="form-group">
												<label class="">Central Riesgo</label>
												<select class="form-control" id="central_riesgo" name="central_riesgo">
													<option value="">[TODOS]</option>
													<option value="S">SI</option>
													<option value="N">NO</option>
												</select>
											</div>
										</div>
									</div>

									
									<div class="row" style="">
	                        			<div class="col-md-6">
											<div class="form-group">
												<button class="btn btn-success botoncito btn_search fa fa-search">&nbsp;&nbsp;Buscar</button>
											</div>
										</div>

										<div class="col-md-6">
											<div class="form-group">
												<button class="btn btn-success botoncito btn_search fa fa-print">&nbsp;&nbsp;Imprimir</button>
											</div>
										</div>	
									</div>
								</form>
							</div>
                        	<div class="col-md-9">
                        		<div class="full-height-scroll">
									<div class="table-responsive">
										<div class="tabla-creditos-header row-header" style="margin-right: 0px;">
											<table class="table table-striped pintar" id="tabla-creditos" border="1" style="width: 100%;border:1px solid #ddd;">
												<thead>
													<tr>
														<th style="width: 22%">Cliente</th>
														<th style="width: 24%">Direccion</th>
														<th style="width: 7%">Letras</th>
														<th style="width: 10%">Vencimiento</th>
														<th style="width: 4%">Credito</th>
														<th style="width: 8%">Importe</th>
														<th style="width: 8%">Moras</th>
														<th style="width: 8%">Total</th>
														<th style="width: 1%">&nbsp;</th>
													</tr>
												</thead>
												<tbody></tbody>
											</table>
											 
											 <table class="table table-striped" border="1" style="width: 100%;border:1px solid #ddd;min-width: 462px;">
											 	<tr>
											 		<td style="width: 100%;padding: 0px !important;">
											 			<div id="green" style="margin: auto;"></div>
											 		</td>
											 	</tr>
											 </table>
										</div>
									</div>
								</div>
							</div>
                    	</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal-form" class="modal fade" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Formulario Incidencia</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<form id="form-data" >
						<input type="hidden" id="idvisita" name="idvisita">
						<input type="hidden"  id='idcliente' name="idcliente">
						<input type="hidden"  id='idcredito' name='idcredito'>
						<input type="hidden"  id='in_central_riesgo' name='in_central_riesgo'>
						
						<div class="col-sm-12">
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label class="required">Cliente</label>
										<div class="input-group">
											<span id="icono_father" class="input-group-addon">
												<i class="fa fa-user"></i>
											</span>
											<input type="text" id="cliente_name" class="form-control" readonly="readonly">
										</div>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label class="required">Direcci&oacute;n</label>
										<div class="input-group">
											<span id="icono_father" class="input-group-addon">
												<i class="fa fa-map-marker"></i>
											</span>
											<input type="text" id="direccion_cliente" class="form-control" readonly="readonly">
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<div class="col-sm-12">
							<div class="row">
								<div class="col-sm-2">
									<div class="form-group">
										<label class="required">Credito</label>
										<input type="text" id="nrocredito" class="form-control" readonly="readonly">
									</div>
								</div>

								<div class="col-sm-2">
									<div class="form-group">
										<label class="required">Fecha v.</label>
										<input type="text" id="fecha_venc" class="form-control" readonly="readonly">
									</div>
								</div>

								<div class="col-sm-2">
									<div class="form-group">
										<label class="required">Letras</label>
										<input type="text" id="letras_v" name="letra_vencidas" class="form-control centro" readonly="readonly">
									</div>
								</div>

								<div class="col-sm-2">
									<div class="form-group">
										<label class="required">Monto</label>
										<input type="text" id="monto_d" class="form-control numerillo" readonly="readonly">
									</div>
								</div>

								<div class="col-sm-2">
									<div class="form-group">
										<label class="required">Mora</label>
										<input type="text" id="mora_d" class="form-control numerillo" readonly="readonly">
									</div>
								</div>

								<div class="col-sm-2">
									<div class="form-group">
										<label class="required">Deuda</label>
										<input type="text" id="total_d" class="form-control numerillo" readonly="readonly">
									</div>
								</div>
							</div>
						</div>

						<div class="col-sm-12">
							<div class="row">
								<div class="col-sm-3">
									<div class="form-group">
										<label class="">Documento</label>
										<?php echo $tipodocumento;?>
									</div>
								</div>

								<div class="col-sm-2">
									<div class="form-group">
										<label class="">Serie</label>
										<input type="text" id="serie_doc" name="serie" class="form-control" >
									</div>
								</div>

								<div class="col-sm-2">
									<div class="form-group">
										<label class="">Numero</label>
										<input type="text" id="numero" name="numero" class="form-control" >
									</div>
								</div>

								<div class="col-sm-2">
									<div class="form-group">
										<label class="">Monto</label>
										<input type="text" id="monto_cobrado" name="monto_cobrado" class="form-control numerillo" placeholder="0.00">
									</div>
								</div>
								
								<div class="col-sm-3">
									<div class="form-group">
										<label class="required">Compromiso</label>
										<select id="compromiso" name="compromiso" class="form-control">
											<option value="N">NO</option>
											<option value="S">SI</option>
										</select>
									</div>
								</div>
							</div>
						</div>

						<div class="col-sm-12 ">
							<div class="row">
								<div class="col-sm-3">
									<div class="form-group">
										<label class="">Pagara el dia</label>
										<div class="input-group">
											<span id="icono_father" class="input-group-addon">
												<i class="fa fa-calendar-o"></i>
											</span>
											<input type="text" id="posible_pago" name="posible_pago" class="form-control">
										</div>
									</div>
								</div>

								<div class="col-sm-3">
									<div class="form-group">
										<label class="">Proxima Visita</label>
										<div class="input-group">
											<span id="icono_father" class="input-group-addon">
												<i class="fa fa-calendar-o"></i>
											</span>
											<input type="text" id="fecha_prox_visita" name="fecha_prox_visita" class="form-control">
										</div>
									</div>
								</div>
							</div>
						</div>	

						<div class="col-sm-12">
							<div class="form-group">
								<label class="required">Incidencia</label>
								<textarea id="observacion" name="observacion" class="form-control" ></textarea>
							</div>
						</div>

						<div class="col-sm-12">
							<div class="" style="float:right">
								<button class="btn btn-sm btn-white cancel_save" data-dismiss="modal" aria-label="Close">
									<strong>Cancelar</strong>
								</button>
								<button class="btn btn-sm btn-primary ver_incidencias" type="button"><strong>Ver Incidencias</strong></button>
								<button class="btn btn-sm btn-primary save_data" type="button"><strong>Guardar</strong></button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
 
<link href="./app/css/plugins/sweetalert/sweetalert.css" rel="stylesheet">
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
	#idtipodocumento{font-size:10px;}
	.seleccionado{background: #1c84c6 !important;}
	.visitado{background: #f19800 !important;}
	.seleccionado td{color: black !important;font-weight: bold;}
	.centro{text-align: center;}
	#form-data .form-control{font-size: 11px !important;padding: 4px 4px !important;}
	.sortable li{
		margin: 2px 0px 0px 2px;
		padding: 5px;
		font-size: 11px;
		font-weight:bold;
		width:97%;
	}

	.numerillo{text-align: right;}
	.nro{font-size: 10.5px;color: black;}
	table.pintar thead tr th{
		background: #1ab394;
		color:white;
		font-size: 10.4px;
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
	
	.btn.idtipoempleado_select{		padding: 2px 5px!important;	}
	
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
	
	.grabado{font-size:10px !important;}
	
	.dropdown-menu>li>a:hover {		background-color: #1e90ff !important;	}
	
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

	.table-striped>tbody>tr:nth-of-type(odd) {
    	background-color: white;
	}

	#tabla-creditos tbody tr.fila-credito:hover{background: #c0c0c0;}

	#tab-2 .form-control{		height:27px !important;	}
	.table{margin-bottom: 0px !important; }
	.green {	    background: #f9f9f9 !important;	}

	.pager_smart ul{    list-style: none !important;    padding: 0;    margin: 0;    float: left;    margin-right: 4px;}
	.pager_smart ul li{    display: inline;    margin-left: 2px;}
	.pager_smart ul li a{
	    text-decoration: none;
	    display: inline-table;
	    width: 20px;
	    height: 20px;
	    text-align: center;
	    border-radius: 4px;
	    -moz-border-radius: 4px;
	}

	.pager_smart .disabled{    color: #A0A0A0 !important;    text-shadow: 1px 1px 1px #FFFFFF;}

	.green.normal{background-color: white !important;border: solid 1px #ddd;}
	.td_zona{padding:2px !important;}
	.table>tbody>tr>td{padding:2px 0px 4px 8px !important;}

	.pager_smart div.short {float: right;  margin: 0;    padding: 0;    margin-right: 10px;    width: 74px;}
	.pager_smart div.short input{
	    width: 28px;
	    height: 18px;
	     border: 1px solid #ddd;
	    margin-left: 8px;
	    float: left;
	}

	.pager_smart{
	    height: 38px;
	    padding: 0;
	    margin: 0;
	    padding-top: 10px;
	    padding-left: 10px;
	    border: 1px solid #ddd;
	}

	.pager_smart span{    margin-left: 4px;    color: black;    font-weight: bold;    float: left;}

	.green.active{    background-color: #1ab394 !important;    color: white;    border: 1px solid #ddd;}
	.pager_smart.green .btn_smart{    background-color: #1ab394;    color: white;    border: 1px solid #ddd;}
}
  </style>
  
<script src="./app/js/jquery-2.1.1.js"></script>
<script src="./app/js/jquery-ui.js"></script>


<script>
	var drag_all = true;
	var sms = true;
	var xitem = 0;
	
	$( ".draggable" ).draggable({
		connectToSortable: ".sortable_connect"
		//,helper: "clone"
		,revert: 'invalid'
		,stop: function( event, ui ) {
			verifi = control(ui.helper[0]);
		}
    });

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
					redirect(_controller);
				});
			});
		}
	});

	calcularTamanioTabla();
  
	function control(aqui){
		$new_padre   = $(aqui).parent('ul').attr('data-padre');
		$idempleado = $(aqui).parent('ul').attr('data-cob');
		valor_mov = $.trim($(aqui).text());

		$($(aqui).parent('ul').find('li')).each(function(e,u){
			if(!$($(this)[0]).hasClass('ui-state-disabled')){
				if($new_padre != 0)
					if( $.trim( $($(this)).text()) == valor_mov ){
						xitem++;
						$($(this)).find('.inlista').hide();
						$($(this)).find('.in_list').show();
						$($(this)).find('input.idempleado').attr({'name':'idempleado[]','value':$idempleado});
					}
			}
		});
		//return xitem;
	}

	function calcularTamanioTabla(){
		var th = $(".tabla-creditos-header").outerHeight();
		var tf = $(".tabla-creditos-footer").outerHeight();
		var h = $(window).height() - tf - th - 380;
		//$(".tabla-creditos-body").css('height', h+'px');
	}

	
</script>