function get_saldo(idcliente) {
	// campos de cliente (estructura tabla) no definido? no hay avance?
	ajax.post({url: _base_url+"cliente/get_saldo/"+idcliente}, function(res) {
		$("#linea_credito").val(res.linea_credito);
		$("#limite_credito").val(res.limite_credito);
		$("#saldo_cliente").val(res.saldo);
		
		html = '';
		if(res.linea_credito == "S") {
			html = '<div class="alert alert-success">'+
				'<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
				'El cliente tiene una linea de credito de '+res.limite_credito+
				'. <strong>Saldo disponible: '+res.saldo+'</strong>.</div>';
		}
		$("#info-saldo-cliente").html(html);
	});
}

function set_events_table() {
	$("input.fecha_vencimiento", "#table-cronograma").datepicker({
		todayBtn: "linked",
		keyboardNavigation: false,
		forceParse: false,
		autoclose: true,
		language: 'es'
	});
	
	$("input.total", "#table-cronograma").numero_real();
}

function get_requerimientos(idcliente) {
	ajax.post({url: _base_url+"credito/get_req/"+$("#idcliente").val()}, function(arr) {
		if(arr.length) {
			var imgs = ['jpg','jpeg','png'], tr, thumbs, ext, url;
			for(var i in arr) {
				if($("#table-req tbody tr[data-idrequisito_credito="+arr[i].idrequisito_credito+"]").length) {
					tr = $("#table-req tbody tr[data-idrequisito_credito="+arr[i].idrequisito_credito+"]");
					
					if(arr[i].con_archivo == "S") {
						ext = String(arr[i].file_url).split('.').pop().toLowerCase();
						url = _base_url+'app/img/'+_current_folder+'/'+arr[i].file_url;
						
						thumbs = ' <a href="'+url+'" target="_blank" title="'+arr[i].file_url+'" index="'+arr[i].idrequerimiento_cliente+'">';
						if(imgs.indexOf(ext) != -1) {
							thumbs += '<img alt="image" class="img-circle" src="'+url+'">';
						}
						else {
							thumbs += '<i class="fa fa-file fa-2x" style="vertical-align: bottom;"></i>';
						}
						thumbs += '</a>';
						
						$("td.project-people", tr).append(thumbs);
					}
					else {
						tr.removeClass("success");
						complete_req(tr, true);
					}
					
				}
			}
			
			// verificamos los req completados
			if($("#table-req tbody tr[data-solicita_ficheros='S']").length) {
				$("#table-req tbody tr[data-solicita_ficheros='S']").each(function() {
					if($("td.project-people a", this).length >= $(this).data("cantidad")) {
						$(this).removeClass("success");
						complete_req($(this), true);
					}
				});
			}
		}
	});
}

function check_complete_req() {
	var tr = $("#table-req tbody tr.current");
	if(tr.data("solicita_ficheros") == "S") {
		if($("td.project-people a", tr).length >= tr.data("cantidad")) {
			tr.removeClass("success");
			complete_req(tr, true);
		}
		else {
			complete_req(tr, false);
		}
	}
}

function complete_req(tr, completar) {
	if(completar) {
		// confirmamos el requerimiento
		$("td.check-mail i.fa", tr).removeClass("fa-square-o").addClass("fa-check-square");
		tr.addClass("success");
		$("input.idrequisito_credito", tr).prop("checked", true);
		
		if(tr.data("solicita_ficheros") == "N") {
			$("a.btn-confirm-req", tr).html('<i class="fa fa-times"></i> Desactivar</a>');
		}
	}
	else {
		// desactivamos el requerimiento
		$("td.check-mail i.fa", tr).removeClass("fa-check-square").addClass("fa-square-o");
		tr.removeClass("success");
		$("input.idrequisito_credito", tr).prop("checked", false);
		
		if(tr.data("solicita_ficheros") == "N") {
			$("a.btn-confirm-req", tr).html('<i class="fa fa-check"></i> Confirmar</a>');
		}
	}
}

function is_acepted_files(filename) {
	var ext = ['jpg','jpeg','png','pdf','doc','docx','xls','xlsx'];
    var v = filename.split('.').pop().toLowerCase();
	
	return (ext.indexOf(v) != -1);
}

var _current_folder = "requerimiento_cliente";

function set_req(arr, nuevo) {
	if(arr.length) {
		var imgs = ['jpg','jpeg','png'], thumbs = '', ext, url, thumbs_2 = '';
		for(var i in arr) {
			if(arr[i].con_archivo == "S") {
				ext = String(arr[i].file_url).split('.').pop().toLowerCase();
				url = _base_url+'app/img/'+_current_folder+'/'+arr[i].file_url;
				
				thumbs_2 += ' <a href="'+url+'" target="_blank" title="'+arr[i].file_url+'" index="'+arr[i].idrequerimiento_cliente+'">';
				thumbs += '<div class="file-box" index="'+arr[i].idrequerimiento_cliente+'"><div class="file">';
				thumbs += '<a href="'+url+'" target="_blank"><span class="corner"></span>';
				if(imgs.indexOf(ext) != -1) {
					thumbs += '<div class="image">';
					thumbs += '<img alt="image" class="img-responsive" src="'+url+'">';
					thumbs += '</div>';
					thumbs_2 += '<img alt="image" class="img-circle" src="'+url+'">';
				}
				else {
					thumbs += '<div class="icon"><i class="fa fa-file"></i></div>';
					thumbs_2 += '<i class="fa fa-file fa-2x" style="vertical-align: bottom;"></i>';
				}
				thumbs += '<div class="file-name">'+arr[i].file_url+'<br><small>Fecha subido: '+fecha_es(arr[i].fecha)+'</small></div>';
				thumbs += '</a>';
				thumbs += '<a href="#" class="trash del-req" title="Eliminar" data-id="'+arr[i].idrequerimiento_cliente+'"><i class="fa fa-trash-o "></i></a>';
				thumbs += '</div></div>';
				thumbs_2 += '</a>';
			}
		}
		$("#file-req").append(thumbs);
		if(nuevo) {
			$("#table-req tbody tr.current td.project-people").append(thumbs_2);
		}
	}
}

function seleccionar_venta() {
	if( ! $("#idventa").required()) {
		abrir_modal_venta();
	}
	else {
		get_saldo($("#idcliente").val());
		get_requerimientos($("#idcliente").val());
		
		if(_es_nuevo_credito_ == false) {
			set_events_table();
		}
	}
}

function abrir_modal_venta() {
	jFrame.create({
		title: "Buscar venta"
		,controller: "venta"
		,method: "grilla_popup"
		,msg: ""
		,widthclass: "modal-lg"
		,data: "c=S"
		,onSelect: function(datos) {
			$("#idventa").val(datos.idventa);
			$("#idcliente").val(datos.idcliente);
			$("#idmoneda").val(datos.idmoneda);
			$("#venta_moneda").val(datos.moneda);
			$("#venta_tipo_documento").val(datos.tipo_documento);
			$("#venta_numero_documento").val(datos.serie+'-'+datos.correlativo);
			// $("#venta_total").val(datos.total);
			$("#credito_cliente").val(datos.full_nombres);
			$("#monto_facturado").val(datos.total);
			$("#inicial").val("0.00");
			$("#capital").val(datos.total);
			get_saldo(datos.idcliente);
			get_requerimientos(datos.idcliente);
		}
		,onCancel: function() {
			$("#form_credito button.btn_cancel").trigger("click");
		}
	});
	
	jFrame.show();
}

function get_tasa() {
	if( $.trim($("#nro_letras").val()) != "" && $.isNumeric($("#nro_letras").val()) ) {
		if($("#id_tipo_credito").val() == "1") {
			$("#tasa").val("0.00").prop("readonly", true);
		}
		else {
			ajax.post({url: _base_url+"credito/get_tasa/"+$("#id_ciclo").val()+"/"+$("#nro_letras").val()}, function(res) {
				if(res.tasa <= 0) {
					ventana.alert({titulo: "", mensaje: "No se ha encontrado una tasa de interes para "+res.mes+" meses."}, function() {
						$("#tasa").focus();
					});
				}
				$("#tasa").val(parseFloat(res.tasa).toFixed(6));
			});
		}
	}
	else {
		$("#tasa").val("");
	}
}

function calcularTotalCredito() {
	if( $("#table-cronograma tbody tr").length ) {
		var inputs = ["amortizacion", "interes", "monto", "gastos", "total"];
		var total, v;
		
		$.each(inputs, function() {
			total = 0;
			if( $("input." + this, "#table-cronograma tbody").length ) {
				$("input." + this, "#table-cronograma tbody").each(function() {
					v = parseFloat( $(this).val() );
					if( isNaN(v) )
						v = 0;
					total += v;
				});
			}
			
			$("#table-cronograma tfoot input.total_"+this).val(total.toFixed(2));
		});
	}
	
}

function calcularValoresLetra(tr) {
	var amortizacion = parseFloat( tr.find('input.amortizacion').val() );
	
	var interes = parseFloat( tr.find('input.interes_temp').val() );
	if(isNaN(interes))
		interes = 0;
	
	var cuota = amortizacion + interes; // 208.57
	
	var total = parseFloat( tr.find('input.total').val() ); // 209
	if(isNaN(total))
		total = 0;
	
	// var total_gen = parseFloat( tr.find('input.deta_total_temp').val() ); // 209
	// if(isNaN(total_gen))
		// total_gen = 0;
	
	var gasto = 0;
	
	if(total >= cuota) {
		gasto = total - cuota;
	}
	else {
		gasto = parseFloat( $('#gasto').val() );
		if(isNaN(gasto))
			gasto = 0;
		
		var resto = cuota - total; // calculo la diferencia a restar
		resto += gasto; // primero sumo el gasto para mantener el gasto
		
		if(interes >= resto) {
			interes -= resto;
			cuota = amortizacion + interes;
		}
		else {
			// como la diferencia es mayor al interes
			resto -= gasto; // quitamos el gasto y ponemos a cero
			gasto = 0;
			// verificamos si con eso alcanza
			if(interes >= resto) {
				interes -= resto;
				cuota = amortizacion + interes;
			}
			else {
				resto -= interes;
				interes = 0;
				gasto -= resto;
				cuota = amortizacion + interes;
			}
		}
	}
	
	tr.find('input.interes').val( interes.toFixed(2) );
	tr.find('input.monto').val( cuota.toFixed(2) );
	tr.find('input.gastos').val( gasto.toFixed(2) );
}

function activar_tab(tab) {
	// desactivamos todos los tabs
	$("#tab-credito ul.nav-tabs li.active").removeClass("active");
	$("#tab-credito div.tab-content div.tab-pane.active").removeClass("active");
	
	// activamos el tab especifico
	$("#tab-credito ul.nav-tabs li a[href='#tab-"+tab+"']").parent("li").addClass("active");
	$("#tab-credito div.tab-content div#tab-"+tab).addClass("active");
}

seleccionar_venta();

$("#tab-credito ul.nav-tabs li a").click(function() {
	return false;
});

$("#inicial, #tasa, #gasto").numero_real();
$("#nro_letras, #dias_gracia").numero_entero();

$("#inicial").blur(function() {
	var total = parseFloat($("#monto_facturado").val());
	var inicial = parseFloat($(this).val());
	if(!$.isNumeric(inicial))
		inicial=0;
	var capital = total - inicial;
	$("#capital").val(capital.toFixed(2));
	$(this).val(inicial.toFixed(2));
});

$("button.btn_cancel_credito").click(function(e) {
	e.preventDefault();
	ventana.confirm({
		titulo: "Advertencia", 
		mensaje: "Si cancela ahora, se borrarán los datos que no hayan sido guardados. "+
			"¿Desea cancelar el registro de todas maneras?", 
		textoBotonCancelar: "No creo hijo", 
		textoBotonAceptar: "Cancelar"
	}, function(ok) {
		if(ok) {
			$("button.btn_cancel").trigger("click");
		}
	});
	return false;
});

$("button.btn_prev_tab").click(function(e) {
	e.preventDefault();
	var tab = $(this).data("tab") - 1;
	activar_tab(tab);
	return false;
});

$("button.btn_next_tab").click(function(e) {
	e.preventDefault();
	
	var tab = $(this).data("tab");
	if(tab == 1) {
		// validamos el saldo del cliente para el credito
		// Enviamos una peticion para recargar los datos
		get_saldo($("#idcliente").val());
		if($("#linea_credito").val() != "S") {
			ventana.alert({titulo: "", mensaje: "El cliente "+$("#credito_cliente").val()+" no tiene linea de crédito.", tipo: "warning"});
			return false;
		}
		
		if(_es_nuevo_credito_) {
			var capital = parseFloat($("#capital").val());
			var saldo = parseFloat($("#saldo_cliente").val());
			
			if(capital > saldo) {
				// var limite = parseFloat($("#limite_credito").val());
				
				ventana.alert({titulo: "", mensaje: "No existe saldo suficiente para el credito del cliente."+
					"<br />Saldo disponible: <strong>"+saldo.toFixed(2)+"</strong>", tipo: "warning"});
				return false;
			}
		}
	}
	else if(tab == 2) {
		// validamos los requisitos para el credito
		var error = false, msg = '';
		
		if($("#table-req tbody tr[data-obligatorio='S']").length) {
			// existen requerimientos obligatorios
			$("#table-req tbody tr[data-obligatorio='S']").each(function() {
				if(!$(this).hasClass("success")) {
					error = true;
				}
				if($(this).data("solicita_ficheros") == "S") {
					if($("td.project-people a", this).length < $(this).data("cantidad")) {
						error = true;
					}
				}
				if(error) {
					msg = "Complete el siguiente requerimiento antes de continuar: "+
						$(this).data("cantidad")+" "+$("span.desc-req", this).text();
					return  false;
				}
			});
		}
		
		if(error) {
			ventana.alert({titulo:"",mensaje:msg});
			return false;
		}
	}
	
	tab += 1;
	activar_tab(tab);
	
	return false;
});

$("#id_tipo_credito").change(function() {
	if($(this).val() == "1") {
		$("#tasa").val(0).prop("readonly", true);
	}
	else {
		$("#tasa").prop("readonly", false);
	}
	$("#id_ciclo").trigger("change");
});

$("#id_ciclo").change(function() {
	get_tasa();
});

$("#nro_letras").blur(function() {
	get_tasa();
});

$('#fecha_inicio').datepicker({
	todayBtn: "linked",
	keyboardNavigation: false,
	forceParse: false,
	autoclose: true,
	language: 'es',
});

$("#btn_generarletras").click(function(e) {
	e.preventDefault();
	var b = true;
	b = b && $("#capital").required({numero:true, tipo:"float", aceptaCero:false});
	b = b && $("#id_tipo_credito").required();
	b = b && $("#id_ciclo").required();
	b = b && $("#nro_letras").required({numero:true, tipo:"int", aceptaCero:false});
	if($("#id_tipo_credito").val() != "1") {
		b = b && $("#tasa").required({numero:true, tipo:"float", aceptaCero:false});
	}
	b = b && $("#fecha_inicio").required();
	if(b) {
		var fechaini = $("#fecha_inicio").val();
		
		var dias = parseFloat($("#id_ciclo option:selected").data("dias"));
		dias = Math.round(dias);
		
		var letras = parseInt($("#nro_letras").val());
		
		var capital = parseFloat($("#capital").val());
		
		var tasa = parseFloat($("#tasa").val()) / 100;
		if(isNaN(tasa))
			tasa = 0;
		
		var amortizacion = capital / letras;
		var interes = capital * tasa;
		var cuota = amortizacion + interes;
		var gasto = ($.isNumeric($("#gasto").val())) ? parseFloat($("#gasto").val()) : 0;
		var total = cuota + gasto;
		
		var arrFecha = String(fechaini).split('/');
		var fecha = new Date(parseInt(arrFecha[2]), (parseInt(arrFecha[1]) - 1), parseInt(arrFecha[0]));
		
		// if($("#id_ciclo").val() == '1') {
			// dias = '1 months';
		// }
		
		fecha = addDate(fecha, dias);
		
		var i = 0, table = new Table();
		
		for(var j=0; j < letras; j++) {
			i ++;
			
			table.tr({index: i});
			table.td("<input type='text' class='form-control input-sm letra' name='letra[]' value='"+i+"' readonly>");
			table.td("<input type='text' class='form-control input-sm fecha_vencimiento' name='fecha_vencimiento[]' value='"+dateFormat(fecha, 'd/m/Y')+"'>");
			table.td("<input type='text' class='form-control input-sm amortizacion' name='amortizacion[]' value='"+amortizacion.toFixed(2)+"' readonly>");
			table.td("<input type='text' class='form-control input-sm interes' name='interes[]' value='"+interes.toFixed(2)+"' readonly>");
			table.td("<input type='text' class='form-control input-sm monto' name='monto[]' value='"+cuota.toFixed(2)+"' readonly>");
			table.td("<input type='text' class='form-control input-sm gastos' name='gastos[]' value='"+gasto.toFixed(2)+"' readonly>");
			table.td("<input type='text' class='form-control input-sm total' name='total[]' value='"+total.toFixed(2)+"'>");
			table.td("<input type='hidden' class='interes_temp' name='interes_temp[]' value='"+interes.toFixed(2)+"'>", {style:"display:none;"});
			
			fecha = addDate(fecha, dias);
		}
		
		$("#table-cronograma tbody").html(table.to_string());
		
		calcularTotalCredito();
		
		set_events_table();
	}
	return false;
});

$('#table-cronograma').on('blur', 'input.total', function() {
	calcularValoresLetra( $(this).closest('tr') );
	calcularTotalCredito();
	
	var v = parseFloat( $(this).val() );
	$(this).val(v.toFixed(2));
});

$(".btn-confirm-req").click(function(e) {
	e.preventDefault();
	var tr = $(this).closest("tr");
	var str = "idrequisito_credito="+tr.data("idrequisito_credito")+"&idcliente="+$("#idcliente").val();
	if(tr.hasClass("success")) {
		ajax.post({url: _base_url+"credito/uncomplete_req", data: str},function(res) {
			complete_req(tr, false);
		});
	}
	else {
		ajax.post({url: _base_url+"credito/complete_req", data: str},function(res) {
			complete_req(tr, true);
		});
	}
	return false;
});

$("#modal-upload_req").on('shown.bs.modal', function () {
	// cargamos los archivos del cliente
	ajax.post({url: _base_url+"credito/get_req/"+$("#idcliente").val()+"/"+$("#table-req tbody tr.current").data("idrequisito_credito")}
	,function(res) {
		set_req(res);
	});
});

$("#modal-upload_req").on('hidden.bs.modal', function () {
	// limpiamos los archivos del cliente
	$("#file-req div.file-box").remove();
	$("#file_input").val("");
	$("#file_nombre").val("");
	$("#table-req tbody tr").removeClass("current");
});

$(".btn-add-req").click(function(e) {
	e.preventDefault();
	var tr = $(this).closest("tr");
	var title = tr.data("cantidad")+" "+$(".desc-req", tr).text();
	tr.addClass("current");
	$("#modal-upload_req .modal-title").text(title);
	$("#modal-upload_req").modal("show");
	return false;
});

$("#btn-upload").click(function(e) {
	e.preventDefault();
	if($("#file_input").required()) {
		var cantidad = $("#table-req tbody tr.current").data("cantidad");
		
		if($("#file-req div.file-box").length < cantidad) {
			if( is_acepted_files($("#file_input").val()) ) {
				var fd = new FormData(document.getElementById("form-upload"));
				fd.append("idrequisito_credito", $("#table-req tbody tr.current").data("idrequisito_credito"));
				fd.append("idcliente", $("#idcliente").val());
				fd.append("folder", _current_folder);
				fd.append("response", "ajax");
				fd.append("type", "json");
				
				$.ajax({
					url: _base_url+"credito/upload_req",
					type: "POST",
					data: fd,
					dataType: "json",
					enctype: 'multipart/form-data',
					processData: false,  // tell jQuery not to process the data
					contentType: false   // tell jQuery not to set contentType
				}).done(function( data ) {
					if(data.code == "ERROR") {
						ventana.alert({titulo:"", mensaje:data.message, type:"error"});
						return;
					}
					
					var arr = [];
					arr.push(data.data);
					set_req(arr, true);
					check_complete_req();
					
					$("#file_input").val("");
					$("#file_nombre").val("");
				});
			}
			else {
				var arr = String($("#file_input").attr("accept")).replace(/\./g, "").split(",");
				ventana.alert({titulo:"Archivo inv&aacute;lido", mensaje:"Solo se permite subir archivos: "+arr.join(", ")});
			}
		}
		else {
			ventana.alert({titulo:"", mensaje:"El requerimiento ya se ha completado."});
		}
	}
	return false;
});

$("#file-req").on("click", "a.del-req", function(e) {
	e.preventDefault();
	ajax.post({url: _base_url+"credito/del_req/"+$(this).data("id")},function(res) {
		$("#file-req div.file-box[index="+res.idrequerimiento_cliente+"]").remove();
		$("#table-req tr.current td.project-people a[index="+res.idrequerimiento_cliente+"]").remove();
		check_complete_req();
	});
	return false;
});

$("#btn_save_credito").click(function(e) {
	e.preventDefault();
	// hacemos las validaciones
	if(!$("#idventa").required()) {
		ventana.alert({titulo:"",mensaje:"Actualice la p&aacute;gina (Presione F5 para actualizar) para seleccionar la venta."});
		activar_tab(1);
		return false;
	}
	if(!$("#idcliente").required()) {
		ventana.alert({titulo:"",mensaje:"Indique el cliente para el credito."});
		activar_tab(1);
		return false;
	}
	
	var v = true;
	// validamos primer tab
	v = v && $("#venta_tipo_documento").required();
	v = v && $("#venta_numero_documento").required();
	v = v && $("#monto_facturado").required({numero:true,tipo:"float"});
	v = v && $("#credito_cliente").required();
	if(v == false) {
		activar_tab(1);
		return false;
	}
	
	// validamos segundo tab
	if($("#table-req tbody tr[data-obligatorio='S']").length) {
		$("#table-req tbody tr[data-obligatorio='S']").each(function() {
			if(!$(this).hasClass("success")) {
				v = false;
			}
			if($(this).data("solicita_ficheros") == "S") {
				if($("td.project-people a", this).length < $(this).data("cantidad")) {
					v = false;
				}
			}
			if(v == false) {
				ventana.alert({titulo:"",mensaje:"Complete el siguiente requerimiento antes de continuar: "+
					$(this).data("cantidad")+" "+$("span.desc-req", this).text()});
			}
			return v;
		});
	}
	if(v == false) {
		activar_tab(2);
		return false;
	}
	
	// validamos el tercer tab
	v = v && $("#capital").required({numero:true, tipo:"float"});
	v = v && $("#id_tipo_credito").required();
	v = v && $("#id_ciclo").required();
	v = v && $("#nro_letras").required({numero:true, tipo:"int"});
	v = v && $("#tasa").required({numero:true, tipo:"float", aceptaCero:true});
	v = v && $("#fecha_inicio").required();
	v = v && $("#dias_gracia").required({numero:true, tipo:"int", aceptaCero:true});
	v = v && $("#id_estado_credito").required();
	if(v == false) {
		activar_tab(3);
		return false;
	}
	if($("#table-cronograma tbody tr").length < 1) {
		ventana.alert({titulo:"", mensaje:"Se ha olvidado de Generar el Cronograma."});
		activar_tab(3);
		return false;
	}
	// validamos el interes del crediton
	var interes = parseFloat($("#table-cronograma tfoot input.total_interes").val());
	if($("#id_tipo_credito").val() == "1") {
		// credito sin interes
		if(interes > 0) {
			ventana.alert({titulo:"", mensaje:"El credito no debe considerar interes. Si esto no es asi, "+
				"por favor indique el tipo de credito y vuelva a Generar el Cronograma"});
			activar_tab(3);
			return false;
		}
	}
	else {
		// credito con interes
		if(interes <= 0) {
			ventana.alert({titulo:"", mensaje:"El credito debe tener interes. Si esto no es asi, "+
				"por favor indique el tipo de credito y vuelva a Generar el Cronograma."});
			activar_tab(3);
			return false;
		}
	}
	
	// validamos saldo disponible del cliente
	var total = parseFloat($("#table-cronograma tfoot input.total_total").val());
	var saldo = parseFloat($("#saldo_cliente").val());
	if(_es_nuevo_credito_ == false) {
		saldo = parseFloat($("#limite_credito").val());
	}
	
	if(total > saldo) {
		ventana.alert({titulo: "", mensaje: "No existe saldo suficiente para el credito del cliente."+
			"<br />Total cr&eacute;dito: <strong>"+total.toFixed(2)+"</strong>"+
			"<br />Saldo disponible: <strong>"+saldo.toFixed(2)+"</strong>", tipo: "warning"});
		activar_tab(3);
		return false;
	}
	
	if(v) {
		var data = $("#form_"+_controller).serialize();
		model.save(data, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				redirect($("#redirect").val());
			});
		});
	}
});

$("#btn-buscar-cliente").click(function(e) {
	e.preventDefault();
	jFrame.create({
		title: "Buscar clientes"
		,controller: "cliente"
		,method: "grilla_popup"
		,msg: ""
		,onSelect: function(datos) {
			$("#credito_cliente").val(datos.cliente);
			$("#idcliente").val(datos.idcliente);
		}
	});
	
	jFrame.show();
});

$("#btn-buscar-garante").click(function(e) {
	e.preventDefault();
	jFrame.create({
		title: "Buscar garantes"
		,controller: "cliente"
		,method: "grilla_popup"
		,msg: ""
		,onSelect: function(datos) {
			$("#credito_garante").val(datos.cliente);
			$("#idgarante").val(datos.idcliente);
		}
	});
	
	jFrame.show();
});

//Extend Cliente
$("#btn-registrar-cliente").on("click", function() {
	id_cliente_retornar = $("#idcliente");
	cliente_retornar	= $("#credito_cliente");
	$("#title_modal_ref").html("Registrar Cliente");
	open_modal_cliente(true);
  
	return false;
});

$("#btn-edit-cliente").on("click", function() {
	id = $("#idcliente").val();
	form_cli = "#form_"+prefix_cliente;
	id_cliente_retornar = $("#idcliente");
	cliente_retornar	= $("#credito_cliente");

	if(id!=''){
		obtenerDatosCliente(id, prefix_cliente, form_cli);
	}else{
		ventana.alert({titulo: "", mensaje: "Debe seleccionar un cliente"});
	}
});


//Extend Garante
$("#btn-registrar-garante").on("click", function() {
	id_cliente_retornar = $("#idgarante");
	cliente_retornar	= $("#credito_garante");
	$("#title_modal_ref").html("Registrar Garante");
	open_modal_cliente(true);
	return false;
});

$("#btn-edit-garante").on("click", function() {
	id = $("#idgarante").val();
	form_cli = "#form_"+prefix_cliente;
	id_cliente_retornar = $("#idgarante");
	cliente_retornar	= $("#credito_garante");

	if(id!=''){
		obtenerDatosCliente(id, prefix_cliente, form_cli);
	}else{
		ventana.alert({titulo: "", mensaje: "Debe seleccionar un cliente"});
	}
});