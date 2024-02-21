var form = {
	nuevo: function() {
		
	},
	editar: function(id) {
		
	},
	guardar_boton:function(){
		alert('Here.....')
	}
}
//$(".btn_search").trigger("click");

$('#fecha_prox_visita,#posible_pago,#fecha_inicio,#fecha_fin').datepicker({
	todayBtn: "linked",
	keyboardNavigation: false,
	forceParse: false,
	autoclose: true,
	language: 'es',
	endDate: parseDate(_current_date)
});

$(".btn_print").click(function(e){
	e.preventDefault();
	open_url_windows(_controller+"/imprimir?"+$("#parametros").serialize()+"&cobrador="+$("#idcobrador option:selected").text());
});

$(".save_data").click(function(){
	s = true && $("#cliente_name").required();
	s = s && $("#direccion_cliente").required();
	s = s && $("#nrocredito").required();
	s = s && $("#fecha_venc").required();
	s = s && $("#letras_v").required();
	s = s && $("#monto_d").required();
	s = s && $("#mora_d").required();
	s = s && $("#total_d").required();
	s = s && $("#observacion").required();

	if (s) {
		var data = $("#form-data").serialize();
		ajax.post({url: _base_url+_controller+"/save_incidencia", data: data}, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Incidencia grabada Correctamente", tipo:"success"}, function(rp) {
				idvisita = res.idvisita;
				
				cls = (res.in_central_riesgo === 'S') ? 'centralriesgo_tr' : '';
				con_visita = '';
				
				if ($.trim(cls)!='') {
					if ($.trim(idvisita)&&idvisita>0) {
						// con_visita = 'combinado';
						cls = 'combinado';
					}else{

					}
				}else{
					if ($.trim(idvisita)&&idvisita>0) {
						cls = 'visitado';
					}
				}
				// console.log(cls);
				if (cls!='') {
					$("tr.seleccionado").addClass(cls);
				}
				
				// if (con_visita!='') {
					// $("tr.seleccionado td:eq(2),tr.seleccionado td:eq(3),tr.seleccionado td:eq(4),tr.seleccionado td:eq(5),tr.seleccionado td:eq(6),tr.seleccionado td:eq(7),tr.seleccionado td:eq(8)").addClass(con_visita);
				// }else{
					
				// }
				
				$("tr.seleccionado").removeClass("seleccionado")
				$("#modal-form").modal('hide');
			});
		});
	}
});

// cargarTablaCreditos();
$("#nro_credito").numero_entero()
$("#cliente").letras({'permitir':' '})
$(".btn_search").click(function(e){
	e.preventDefault();
    cargarTablaCreditos();
})

var  continuar = true;

	$(".buscar_incidencia").click(function(){
		var data = $("#form-incidencia").serialize();
		ajax.post({url: _base_url+_controller+"/ver_incidencias", data: data}, function(res) {
			$(".here_incidencia").html("INCIDENCIA DE "+$("#cliente_name").val()+" , CREDITO : "+$("#nrocredito").val());
			fila_incidencia(res);
			$("#modal-incidencia").modal('show');
		});
	});
	
	$(".ver_incidencias").click(function(){
		$("#cli_id").val( $("#idcliente").val() );
		$("#cred_id").val($("#idcredito").val());
		$(".buscar_incidencia").trigger("click");
	});

	$(document).on('dblclick','#tabla-creditos tbody tr.fila-credito',function(e){
		if($('#es_cobrador').val()=='A' && ($("#user_session").val()==$("#idcobrador").val())){
			$(this).addClass("seleccionado");
			str = "idcredito="+$(this).attr('aria-credito');
			str+= "&idcliente="+$(this).attr('aria-cliente');
			str+= "&idventa="+$(this).attr('aria-venta');
			
			$("#form-data input,#form-data textarea").val('');
			$("#in_central_riesgo").val('N');
			
			if( $(this).hasClass('centralriesgo_tr') ){
				$("#in_central_riesgo").val('S');
			}
			//$("#form-data input,#form-data textarea,#form-data select").val('');
			ajax.post({url: _base_url+_controller+"/get_credito/", data: str}, function(res) {
				if (res.credito.length>0) {
					$("#idcliente").val(res.credito[0].idcliente)
					$("#idcredito").val(res.credito[0].idcredito)
					$("#idventa").val(res.credito[0].idventa)
					$("#cliente_name").val(res.credito[0].cliente)
					$("#direccion_cliente").val(res.credito[0].direccion)
					$("#nrocredito").val(res.credito[0].nro_credito)
					$("#fecha_venc").val(res.credito[0].fecha_vencimiento)
					$("#letras_v").val(res.credito[0].letra_vencida)
					$("#monto_d").val(res.credito[0].monto_letra)
					$("#mora_d").val(res.credito[0].monto_mora)
					total_deuda = parseFloat(res.credito[0].monto_letra) + parseFloat(res.credito[0].monto_mora);
					$("#total_d").val(total_deuda.toFixed(2))
				}else{
					// ventana.alert({titulo: "Que paso!", mensaje: "Error al cargar los datos", tipo:"errorr"})
					swal({
						title: "Hey!",
						text: "Este registro no cuenta con credito!",
						type: "warning"
					});
					return false;
				}

				if (res.visitas.length>0) {
					$("#idvisita").val(res.visitas[0].idvisita)
					if($.trim(res.visitas[0].fecha_prox_visita)){
						$("#fecha_prox_visita").val(dateFormat(parseDate(res.visitas[0].fecha_prox_visita), "d/m/Y"))
					}
					
					if($.trim(res.visitas[0].posible_pago)){
						$("#posible_pago").val(dateFormat(parseDate(res.visitas[0].posible_pago), "d/m/Y"))
					}
					// $("#posible_pago").val(res.visitas[0].posible_pago)
					$("#serie_doc").val(res.visitas[0].serie)
					$("#numero").val(res.visitas[0].numero)
					$("#monto_cobrado").val(res.visitas[0].monto_cobrado)
					$("#compromiso").val(res.visitas[0].compromiso)
					$("#observacion").val(res.visitas[0].observacion)				
				}

				$("#modal-form").modal('show');
			});			
		}else{
			swal({
				title: "Hey!",
				text: "No puedes ingresar incidencia de la hoja de ruta que no te pertenece!",
				type: "warning"
			});
		}
	})

	$(document).on('click','#tabla-creditos tbody tr.fila-credito',function(e){
		if ($(this).hasClass("seleccionado")) {
			$(this).removeClass("seleccionado")
		}else{
			$('#tabla-creditos tbody tr.fila-credito').removeClass("seleccionado")
			$(this).addClass("seleccionado");
		}
	})
	
	$(document).on('click','.more',function(e){
		tr_ = $(this).parent('td').parent('tr');
		id_zonita = tr_.attr('aria-zona');

		if( tr_.hasClass('ver') ){
			$(this).find('i').removeClass("fa-angle-double-down").addClass("fa-angle-double-right");
			$(".hijo-zona"+id_zonita).fadeOut();
			tr_.removeClass('ver').addClass('no_ver');
		}else{
			$(this).find('i').addClass("fa-angle-double-down").removeClass("fa-angle-double-right");
			$(".hijo-zona"+id_zonita).fadeIn();
			tr_.removeClass('no_ver').addClass('ver');
		}
	});
	$(document).on('click','#update_cartera',function(e){
		ajax.post({url: _base_url+_controller+"/generar_hoja/", data: $('#parametros').serialize()+"&ajax=true"}, function(res) {
			if(res){
				cargarTablaCreditos();
			}
		});
	});

	$(document).on('click','#change_cobrador',function(e){
		$("#idcobrador_past").trigger('change');
		$("#modal-config-cobrador").modal('show');
	});
	
	$(document).on('click','#order_cartera',function(e){
		$("#ref_cobrador").html($("#idcobrador option:selected").text());
		cargar_localidad();
		$("#modal-config-orden").modal('show');
	});
	
	$(document).on('click','.fila-cliente',function(e){
		$("tr.fila-cliente").removeClass("seleccionado")
		$(this).addClass("seleccionado");
	});
	
	$(document).on('click','.fila-zona',function(e){
		$("tr.fila-zona").removeClass("seleccionado")
		$(this).addClass("seleccionado");
	});
	
	$("#idzona_ref").change(function(e){
		if($(this).val()!=''){
			cargar_cliente();
			// cargarTablaCreditos();
		}
	});
	
	$("#letra").change(function(e){
		cargar_cliente();
	});
	
	$("#idzona_cartera").change(function(e){
		$(".btn_search").trigger("click");
	});
	
	$("#id_ubigeo").change(function(e){
		$("#tabla-creditos tbody").empty();
		$("#idubigeo").val($(this).val());
		cargar_localidad('cliente');
	});
	
	$("#idcobrador").change(function(e){
		$("#tabla-creditos tbody").empty();
		cargar_ruta();
	});
	
	$("#idubigeo").change(function(e){
		cargar_localidad('cartera');
	});
	
	$("#guardar_orden_zona").click(function(e){
		e.preventDefault();
		str =$("#form_orden_zona").serialize();
		str+="&idcobrador="+$("#idcobrador").val();
		ajax.post({url: _base_url+_controller+"/guardar_orden_zona/", data: str}, function(data) {
			if(data){
				cargarTablaCreditos();
				$("#modal-config-orden").modal('hide');
			}
		});
	});
	
	$("#guardar_orden_cliente").click(function(e){
		e.preventDefault();
		str =$("#form_orden_cliente").serialize();
		str+="&idcobrador="+$("#idcobrador").val();
		ajax.post({url: _base_url+_controller+"/guardar_orden_cliente/", data: str}, function(data) {
			if(data){
				cargarTablaCreditos();
				$("#modal-config-orden").modal('hide');
			}
		});
	});
	
	$("#idcobrador_past").change(function(e){
		id = $(this).val();
		label_p = "{Antiguo vendedor}";
		label_n = "{Nuevo vendedor}";
		if(id!=''){
			label_p = $("#idcobrador_past option:selected").text();
			ajax.post({url: _base_url+_controller+"/cobradores_lista/", data: "idcobrador="+id}, function(data) {
				s = '';
				$(data).each(function(y,x){
					s+="<option value='"+x.idcobrador+"'>"+x.cobrador+"</option>";
				});
				$("#idcobrador_new").html(s);
				$("#idcobrador_new").trigger("change");
			});
		}else{
			$("#idcobrador_new").empty();
		}
		
		$("#p_cob").html(label_p);
		$("#n_cob").html(label_n);
	});
	
	$("#idcobrador_new").change(function(e){
		label_n = $("#idcobrador_new option:selected").text();
		$("#n_cob").html(label_n);
	});
	
	$(".btn-actualizar-combo").click(function(e){
		// $div    = $(this).parent("span.input-group-btn").parent("div.input-group");
		// $select = $div.find('select.combo_cobrador');
		
		$("#idcobrador_past").trigger('change');
	});
	
	$("#guardar_intercambio").click(function(e){
		e.preventDefault();
		s = true && $("#idcobrador_past").required();
		s = s && $("#idcobrador_new").required();
		
		if(s){
			ajax.post({url: _base_url+_controller+"/guardar_intercambio/", data: $('#form-intercambio').serialize()}, function(res) {
				if(res){
					$("#modal-config-cobrador").modal('hide');
					cargarTablaCreditos();
				}
			});
		}
	});

	$("#exportar").click(function(e){
		e.preventDefault();
		data = $("#parametros").serialize();
		data+= "&cobrador="+$("#idcobrador option:selected").text();
		open_url_windows(_controller+"/exportar?"+data);
	});
	
	function cargarTablaCreditos(){
		ajax.post({url: _base_url+_controller+"/consulta_cartera/", data: $('#parametros').serialize()+"&ajax=true&id_ubigeo="+$("#id_ubigeo").val()}, function(res) {
			$("#row_smart").val(res.cantidad);
			
			var sms_row = "Mostrando "+$("#row_smart").val()+" Registros";
			
			$(".cant_rows").html(sms_row);
			crearFilas(res.filas);
		});
	}

	function crearFilas(arr){
		$("#tabla-creditos tbody").empty();
		if(arr.length) {
			var codzona_old = '';
			var codzona_new = '';
			var html = '';
			var has_cobrador = false, codcartera, codsector, cls, con_visita='';

			for(var i in arr) {
					codzona_new = arr[i].idzona;
					if(codzona_new != codzona_old) {
						if( $("#tabla-creditos tbody tr[aria-zona="+codzona_new+"]").length < 1 ) {
							html = '<tr aria-zona="'+codzona_new+'" class="ver tr_"><td colspan="9" class="tr-bold tr-title">'+'<a href="#" class="more"><i class="fa fa-angle-double-down fa-2x">'+'&nbsp;&nbsp;'+'</i>'+arr[i].zona+'</a></td></tr>';
							$("#tabla-creditos tbody").append(html);
						}
						
						codzona_old = codzona_new;
					}

					codcartera = '0';
					codsector  = '';
					idvisita   = (arr[i].idvisita);

					fecha_vencimiento = (arr[i].fecha_vencimiento) ? arr[i].fecha_vencimiento : '';
					letras_pendientes = (arr[i].letras_vencidas) ? arr[i].letras_vencidas : '-';
					importe = (arr[i].monto_letra) ? arr[i].monto_letra : '0';
					mora = (arr[i].mora) ? arr[i].mora : '0';
					total = (arr[i].total) ? arr[i].total : '0';
					
					cls = (arr[i].central_riesgo === 'S') ? 'centralriesgo_tr' : '';
					con_visita = '';
					if ($.trim(cls)!='') {
						if ($.trim(idvisita)&&idvisita>0) {
							// con_visita = 'visitado';
							cls = 'combinado';
						}
					}else{
						if ($.trim(idvisita)&&idvisita>0) {
							cls = 'visitado';
						}
					}
					
					html = '<tr class="fila-credito hijo-zona'+codzona_new+' '+cls+'" aria-credito='+arr[i].idcredito+' aria-estado='+arr[i].id_estado_credito+' aria-visita='+idvisita+' aria-cliente='+arr[i].idcliente+' aria-venta='+arr[i].idventa+'>';
					html += '<td class="nro font_upper">'+arr[i].cliente+'</td>';
					html += '<td class="nro font_upper">'+arr[i].direccion+'</td>';
					html += '<td class="nro '+' ">'+letras_pendientes+'</td>';
					html += '<td class="nro '+' ">'+fecha_vencimiento+'</td>';
					html += '<td class="nro '+' ">'+arr[i].nro_credito+'</td>';
					html += '<td class="numerillo nro '+' ">'+parseFloat(importe).toFixed(2)+'</td>';
					html += '<td class="numerillo nro '+' ">'+parseFloat(mora).toFixed(2)+'</td>';
					html += '<td class="numerillo nro '+' ">'+parseFloat(total).toFixed(2)+'</td>';
					// html += '<td class="'+'"><center><a href="#" class=""><i class="fa fa-bell-o fa-1x"></i></a></center></td>';
					
					$("#tabla-creditos tbody").append(html);					
			}
		}
	}
	
	function fila_incidencia(arr){
		$("#tabla-visitas tbody").empty();
		if(arr.length) {
			for(var i in arr) {
				html = '<tr >';
				html += '<td class="nro ">'+(i+1)+'</td>';
				html += '<td class="nro ">'+arr[i].observacion+'</td>';
				html += '<td class="nro '+' ">'+fecha_es(arr[i].fecha_visita)+'</td>';
				$("#tabla-visitas tbody").append(html);
			}
		}
	}
	
	function cargar_ruta(){
		ajax.post({url: _base_url+_controller+"/cargar_ruta/", data: $('#parametros').serialize()+"&ajax=true"}, function(res) {
			$("#tabla_zonas tbody").empty();
			$("#idubigeo").empty();
			$("#idzona_cartera").empty();
			arr = res;
			combo='<option value="">[TODOS]</option>';
			if(arr.length) {
				for(var i in arr) {
					idubigeo   = (arr[i].idubigeo);
					ruta	 = (arr[i].ruta);
					
					combo+= "<option value='"+idubigeo+"'>"+ruta+"</option>";
				}
			}
			$("#idubigeo,#id_ubigeo").html(combo);
			$("#idubigeo").trigger("change");
		});
	}
	
	function cargar_localidad(procesar){
		ajax.post({url: _base_url+_controller+"/cargar_zonas/", data: $('#parametros').serialize()+"&ajax=true&id_ubigeo="+$("#id_ubigeo").val()}, function(res) {
			$("#tabla_zonas tbody").empty();
			$("#idzona_ref").empty();
			$("#idzona_cartera").empty();
			arr = res;
			combo='<option value="">[TODOS]</option>';
			var html = '';
			if(arr.length) {
				item=1;
				for(var i in arr) {
					idzona   = (arr[i].idzona);
					zona	 = (arr[i].zona_h);
					orden	 = (arr[i].orden);
					cls		 ='';
					
					if(orden==0){
						orden = item;
					}
					item++;
					
					html += '<tr class="fila-zona">';
					html += '<td class="">'+"<input type='text' readonly='readonly' class='form-control input-xs' name='orden[]' value='"+orden+"'>"+'</td>';
					html += '<td class="">'+zona+'</td>';
					html += '<td style="display:none;">';
					html += '	<input name="idzona[]" value="'+idzona+'">';
					html += '</td>';
					html += '</tr>';
					
					combo+= "<option value='"+idzona+"'>"+zona+"</option>";
				}
			}
			$("#tabla_zonas tbody").append(html);
			$("#idzona_ref,#idzona_cartera").append(combo);
			if(procesar=='cartera'){
				$("#idzona_cartera").trigger("change");
			}else{
				$("#idzona_ref").trigger("change");
			}
		});
	}
	
	function cargar_cliente(){
		str = "idzona="+$.trim($("#idzona_ref").val())+"&letra="+$("#letra").val()+"&idcobrador="+$("#idcobrador").val();
		str+= "&idubigeo="+$.trim($("#idubigeo").val());
		ajax.post({url: _base_url+_controller+"/cargar_clientes/", data:str}, function(res) {
			$("#tabla_clientes tbody").empty();
			arr = res;
			if(arr.length) {
				var html = '';
				item=1;
				for(var i in arr) {
					idcliente   = (arr[i].idcliente);
					cliente	 	= (arr[i].cliente);
					direccion	= (arr[i].direccion);
					orden	 	= (arr[i].orden_cliente);
					
					if(orden==0){
						orden = item;
					}
					item++;
					
					html  = '<tr class="fila-cliente">';
					html += '<td class="">'+"<input type='text' readonly='readonly' class='form-control input-xs' value='"+orden+"'>"+'</td>';
					html += '<td class="">'+cliente+'</td>';
					html += '<td class="">'+direccion+'</td>';
					html += '<td style="display:none;">';
					html += '	<input name="idcliente[]" value="'+idcliente+'">';
					html += '</td>';
					html += '</tr>';
					
					$("#tabla_clientes tbody").append(html);
				}
			}
		});
	}

	function vincularEventos(){
		// $('#green').smartpaginator({ 
	    	// totalrecords: $("#row_smart").val()
	    	// , recordsperpage: 20
	    	// , datacontainer: 'tabla-creditos'
	    	// , dataelement: 'tr'
	    	// , initval: 0
	    	// , next: 'Next'
	    	// , prev: 'Prev'
	    	// , first: 'First'
	    	// , last: 'Last'
	    	// , theme: 'green' 
	    // });
	}
	
	$(document).ready(function(){
		// cargar_ruta();
		$("#idcobrador").trigger("change");
	});