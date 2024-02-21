function init() {
	cargarReciboIngreso();
	$("#search").focus();
}

function cargarReciboIngreso() {
	reload_combo("#serie", 
	{
		controller: "tipo_documento",
		method: "get_series", 
		data: "idtipodocumento="+_idrecibo_ingreso_
	}, function() {
		$("#serie").trigger("change");
	});
}

function cargarListaCreditos(idcliente, idcredito) {
	ajax.post({url: _base_url+"cuentas_cobrar/get_lista_credito/"+idcliente, dataType: 'json'}, function(arr) {
		var options = '';
		if(arr.length) {
			for(var i in arr) {
				options += '<option value="'+arr[i].idcredito+'">'+arr[i].nro_credito+'</option>';
			}
		}
		$("#nro_credito").html(options);
		if(idcredito) {
			$("#nro_credito").val(idcredito);
		}
		$("#nro_credito").trigger("change");
	});
}

function generarLetras(arr) {
	if(arr.length) {
		var table = new Table(), data;
		
		for(var i in arr) {
			data = arr[i];
			
			k = parseInt(i) + 1;
			letra = parseFloat(data.monto_letra);
			pagado = parseFloat(data.monto_pagado);
			saldo = letra - pagado;
			mora = parseFloat(data.mora);
			descuento = parseFloat(data.descuento);
			total = saldo + mora - descuento;
			title = (data.fecha_pago) ? "title='Fecha amortizaci&oacute;n: "+fecha_es(data.fecha_pago, "d/m/Y")+"'" : "";
			
			table.tr({index: k});
			table.td("<input type='checkbox' class='idletra' name='idletra[]' value='"+data.idletra+"' style='display:none;'>"+
				"<input type='text' class='form-control input-xs text-center letra' name='letra[]' value='"+data.nro_letra+"' readonly>");
			table.td("<input type='text' class='form-control input-xs fecha_vencimiento' name='fecha_vencimiento[]' value='"+fecha_es(data.fecha_vencimiento)+"' readonly>");
			table.td("<input type='text' class='form-control input-xs text-right importe' name='importe[]' value='"+letra.toFixed(2)+"' readonly>");
			table.td("<input type='text' class='form-control input-xs text-right amrtz' name='amrtz[]' value='"+pagado.toFixed(2)+"' "+title+" readonly>");
			table.td("<input type='text' class='form-control input-xs text-right saldo' name='saldo[]' value='"+saldo.toFixed(2)+"' readonly>");
			table.td("<input type='text' class='form-control input-xs text-right text-danger moras' name='moras[]' value='"+mora.toFixed(2)+"' readonly>");
			table.td("<input type='checkbox' class='aplicar_descuento' name='aplicar_descuento[]' value='1' style='display:none;'>"+
				"<input type='text' class='form-control input-xs text-right pull-right descuento' name='descuento[]' value='"+descuento.toFixed(2)+"' readonly>");
			table.td("<input type='text' class='form-control input-xs text-right text-success total' name='total[]' value='"+total.toFixed(2)+"' readonly>");
			table.td("<input type='text' class='form-control input-xs text-right text-navy pagar' name='pagar[]' value='0.00' readonly>");
			
			table.td("<input type='hidden' class='last_fecha_vencimiento' name='last_fecha_vencimiento[]' value='"+data.last_fecha_venc+"'>", {style:"display:none"});
		}
		
		$("#table-letras tbody").html(table.to_string());
	}
}

function calcularTotales() {
	var inputs = ["importe", "amrtz", "saldo", "moras", "descuento", "total", "pagar"];
	var total, v;
	
	if( $("#table-letras tbody tr").length ) {
		$.each(inputs, function() {
			total = 0;
			if( $("input." + this, "#table-letras tbody").length ) {
				$("input." + this, "#table-letras tbody").each(function() {
					v = parseFloat( $(this).val() );
					if( isNaN(v) )
						v = 0;
					total += v;
				});
			}
			
			$("#table-letras tfoot input#total_"+this).val(total.toFixed(2));
		});
	}
	else {
		total = 0;
		$.each(inputs, function() {
			$("#table-letras tfoot input#total_"+this).val(total.toFixed(2));
		});
	}
}

function cargarDatosVenta(idventa) {
	ajax.post({url: _base_url+"venta/get_all/"+idventa, dataType: 'json'}, function(data) {
		$("#venta_comprobante").val(data.venta.comprobante);
		$("#venta_sucursal").val(data.sucursal.descripcion);
		$("#venta_cliente").val(data.venta.full_nombres);
		$("#venta_empleado").val(data.vendedor.nombres+' '+data.vendedor.appat);
		if(data.detalle_venta.length) {
			var html = '';
			for(var i in data.detalle_venta) {
				html += '<tr><td><small>'+data.detalle_venta[i].descripcion+'</small></td>'+
					'<td class="text-navy"><small style="white-space:nowrap;">'+
					data.detalle_venta[i].cantidad+' '+data.detalle_venta[i].abreviatura+
					'</small></td></tr>';
			}
			$("#table-productos tbody").html(html);
		}
	});
}

function cargarDatosCredito(idcredito) {
	ajax.post({url: _base_url+"credito/get_all/"+idcredito+"/1", dataType: 'json'}, function(data) {
		if(data.credito.pagado == "S") {
			ventana.alert({titulo:"", mensaje:"El credito "+data.credito.nro_credito+" ya se ha cancelado. Consulte en el modulo de creditos cancelados."});
			$("#table-letras tbody tr").remove();
			calcularTotales();
			return;
		}
		cargarDatosVenta(data.credito.idventa);
		$("#nro_credito_ref").val(data.credito.nro_credito);
		$("#estado_credito").val(data.credito_view.estado_credito);
		if(data.credito.central_riesgo == "S") {
			$("#central_riesgo").text("En central de riesgo");
		}
		else {
			$("#central_riesgo").text("");
		}
		$("#tasa").val(data.credito.tasa);
		$("#idmoneda").val(data.credito.idmoneda);
		$(".credito_moneda").text(data.credito_view.moneda);
		$("#cliente_credito").val(data.credito_view.cliente);
		$("#descripcion").val(data.credito.descripcion);
		$("#nro_letras").text(data.credito.nro_letras);
		$("#letras_canceladas").text(data.credito.letras_canceladas);
		$("#letras_pendientes").text(data.credito.letras_pendientes);
		$("#dias_gracia").text(data.credito.dias_gracia);
		if(data.credito.genera_mora == "S") {
			$("#genera_mora").html('<i class="fa fa-check"></i>');
		}
		else {
			$("#genera_mora").html('');
		}
		if(data.credito_view.garante) {
			$("#garante_credito").val(data.credito_view.garante);
		}
		if(data.ciclo) {
			$("#dias_mes").val(data.ciclo.dias);
		}
		$("#valor_mora").val($("#current_mora").val());
		// falta verificar el cobrador
		generarLetras(data.arr_letras_pendientes);
		calcularTotales();
		$(".block_pago").css("display", "none");
	});
}

$(document).on("change", "#serie", function() {
	if($.isNumeric($(this).val())) {
		ajax.post({
			url: _base_url+"tipo_documento/get_correlativo", 
			data: "idtipodocumento="+_idrecibo_ingreso_+"&serie="+$("#serie").val()
		}, function(res) {
			$("#correlativo").val(res.correlativo);
		});
	}
	else {
		$("#correlativo").val("");
	}
});

$(document).on("keypress", ".monto_entregado", function(e) {
	var t = e.keyCode ? e.keyCode : e.which;
	e.preventDefault();
	if(t == 13) { // cuando se usa el lector
		if($("div.modal.fade.in#modal-pay").length>0){
			$("button.btn-accept-pay").trigger("click");
		}
	}
});

$("#correlativo").on("dblclick", function() {
	$("#serie").trigger("change");
});

$("#search").autocomplete({
	source: function( request, response ) {
		ajax.post({url: _base_url+"cuentas_cobrar/autocomplete", 
		data: "m=50&q="+request.term+"&f="+$("#filter").val(), dataType: 'json'}, function(data) {
			response(data);
		});
	}
	,appendTo: $("#search").closest("div")
	,select: function( event, ui ) {
		if(ui.item) {
			$("#credito_idcliente").val(ui.item.idcliente);
			cargarListaCreditos(ui.item.idcliente, ui.item.idcredito);
		}
	}
});

$("#filter").change(function() {
	$("#search").focus().select();
});

$("#nro_credito").on("change", function() {
	cargarDatosCredito($(this).val());
});

$("#idtipopago_temp").change(function() {
	if($(this).val() == "3") {
		$("#div_fecha_pago").css("display", "block");
		$("#fecha_pago").val($("#current_date").val());
	}
	else {
		$("#div_fecha_pago").css("display", "none");
		$("#fecha_pago").val("");
	}
	calcular_forma_pago();
	recalcular_mora();
});

$("#letras_pagar").numero_entero();
$("#monto_pagar").numero_real();

var _forma_pago_ = null;

function reset_pagos() {
	if($("#table-letras tbody tr[class!='success']").length) {
		$("#table-letras tbody tr[class!='success']").each(function() {
			total = parseFloat($("input.saldo", this).val()) + parseFloat($("input.moras", this).val());
			
			$("input.idletra", this).prop("checked", false);
			$("input.aplicar_descuento", this).prop("checked", false).css("display", "none");
			$("input.descuento", this).val("0.00");
			$("input.total", this).val(total.toFixed(2));
			$("input.pagar", this).val("0.00");
		});
	}
}

function calcular_forma_pago() {
	_forma_pago_ = ( parseInt($("#letras_pagar").val()) >= 1 ) ? "L" : "M";
}

function letras_pagar() {
	$("#monto_pagar").val("");
	$("#table-letras tbody tr").removeClass("success");
	
	var letras = parseInt($("#letras_pagar").val());
	if(letras >= 1) {
		var i = 1, tr;
		while(i <= letras) {
			tr = $("#table-letras tbody tr[index="+i+"]");
			tr.addClass("success");
			
			$("input.idletra", tr).prop("checked", true);
			$("input.aplicar_descuento", tr).css("display", "inline-block");
			$("input.pagar", tr).val($("input.total", tr).val());
			
			i ++;
		}
	}
	reset_pagos();
}

function monto_pagar() {
	$("#letras_pagar").val("");
	$("#table-letras tbody tr").removeClass("success");
	
	var monto = parseFloat($("#monto_pagar").val());
	if(monto >= 1) {
		$("#table-letras tbody tr").each(function() {
			v = parseFloat($("input.total", this).val());
			
			$(this).addClass("success");
			
			$("input.idletra", this).prop("checked", true);
			$("input.aplicar_descuento", this).css("display", "inline-block");
			
			if(monto > v) {
				$("input.pagar", this).val(v.toFixed(2));
			}
			else {
				$("input.pagar", this).val(monto.toFixed(2));
			}
			
			monto -= v;
			return (monto > 0);
		});
	}
	reset_pagos();
}

function canje_pagar() {
	$("#letras_pagar").val("");
	$("#monto_pagar").val("");
	$("#table-letras tbody tr").removeClass("success");
	
	var monto = parseFloat($("#monto_recibo").val());
	if(monto >= 1) {
		$("#table-letras tbody tr").each(function() {
			v = parseFloat($("input.total", this).val());
			
			$(this).addClass("success");
			
			$("input.idletra", this).prop("checked", true);
			$("input.aplicar_descuento", this).css("display", "inline-block");
			
			if(monto > v) {
				$("input.pagar", this).val(v.toFixed(2));
			}
			else {
				$("input.pagar", this).val(monto.toFixed(2));
			}
			
			monto -= v;
			return (monto > 0);
		});
	}
	reset_pagos();
}

function calular_monto_pago() {
	if(_forma_pago_ == "L") {
		letras_pagar();
	}
	else if(_forma_pago_ == "C") {
		canje_pagar();
	}
	else {
		monto_pagar();
	}
	calcularTotales();
}

function recalcular_mora() {
	if($("#genera_mora").html() == "") {
		ventana.alert({titulo:":(", mensaje:"Este cr&eacute;dito no genera mora, lo siento mucho."});
		$("#valor_mora").val($("#current_mora").val());
		return;
	}
	if($.trim($("#valor_mora").val()) != "" && $.isNumeric($("#valor_mora").val())) {
		if($("#table-letras tbody tr").length) {
			var current_fecha = "";
			if(_forma_pago_ == "C") {
				current_fecha = $("#fecha_recibo").val();
			}
			else if($("#idtipopago_temp").val() == '3') {
				current_fecha = $("#fecha_pago").val();
			}
			if($.trim(current_fecha) == "") {
				current_fecha = $("#current_date").val()
			}
			
			var valor_mora = parseFloat($("#valor_mora").val()), nro_dias, saldo, descuento, mora, total;
			var dias_mes = parseFloat($("#dias_mes").val());
			
			$("#table-letras tbody tr").each(function() {
				nro_dias = getDays( $('input.last_fecha_vencimiento', this).val(), fecha_en(current_fecha) );
				
				saldo = parseFloat($("input.saldo", this).val());
				mora = parseFloat($("input.moras", this).val());
				descuento = parseFloat($("input.descuento", this).val());
				
				if(nro_dias > 0) {
					mora = (saldo - descuento)*valor_mora/100*nro_dias/dias_mes;
					mora = Math.round(mora);
				}
				total = saldo + mora - descuento;
				
				$("input.moras", this).val(mora.toFixed(2));
				$("input.total", this).val(total.toFixed(2));
			});
			calular_monto_pago();
		}
	}
}

$("#letras_pagar,#monto_pagar,#valor_mora").keypress(function(e) {
	var k = e.keyCode ? e.keyCode : e.which;
	if(k == 13) {
		e.preventDefault();
		if(this.id == "valor_mora") {
			recalcular_mora();
			return false;
		}
		if(this.id == "letras_pagar") {
			_forma_pago_ = "L";
		}
		else {
			_forma_pago_ = "M";
		}
		calular_monto_pago();
	}
});

$("#table-letras").on("change", "input.aplicar_descuento", function() {
	var tr = $(this).closest("tr");
	
	var monto = parseFloat( $('input.saldo', tr).val() );
	var mora = parseFloat( $('input.moras', tr).val() );
	var descuento = 0;
		
	if($(this).is(":checked")) {
		var pdesc = parseFloat( $("#tasa").val() ) / 100;
		var dias = parseInt( $("#dias_mes").val() );
		var current_fecha = "";
		if(_forma_pago_ == "C") {
			current_fecha = $("#fecha_recibo").val();
		}
		else if($("#idtipopago_temp").val() == '3') {
			current_fecha = $("#fecha_pago").val();
		}
		if($.trim(current_fecha) == "") {
			current_fecha = $("#current_date").val()
		}
		
		var nro_dias = getDays( fecha_en(current_fecha), fecha_en($('input.fecha_vencimiento', tr).val()) );
		if(nro_dias > 0) {
			descuento = monto * pdesc * (nro_dias / dias);
			descuento = Math.round(descuento);				
		}
	}
	
	monto = monto + mora - descuento;
	monto = Math.round(monto);
	
	$('input.descuento', tr).val(descuento.toFixed(2));
	$('input.total', tr).val(monto.toFixed(2));
	
	calular_monto_pago();
});

$('#fecha_pago').datepicker({
	todayBtn: "linked",
	keyboardNavigation: false,
	forceParse: false,
	autoclose: true,
	language: 'es',
	endDate: parseDate(_current_date)
}).on("changeDate", function() {
	calcular_forma_pago();
	recalcular_mora();
});

$("#btn_save_observacion").click(function(e) {
	e.preventDefault();
	if($("#nro_credito").required()) {
		ajax.post({url: _base_url+"credito/guardar_observacion", 
		data: "idcredito="+$("#nro_credito").val()+"&descripcion="+$("#descripcion").val()}, function() {
			ventana.alert({titulo:"", mensaje:"Observaci&oacute;n guardada"});
		});
	}
});

// configuramos las opciones de pago
// pay.disabledTipopago(true);
// pay.save();

$("#btn_save_pago").click(function(e) {
	e.preventDefault();
	var v = true && $("#nro_credito").required();
	v = v && $("#serie").required();
	v = v && $("#correlativo").required();
	v = v && $("#idtipopago_temp").required();
	if($("#idtipopago_temp").val() == "3") {
		v = v && $("#fecha_pago").required();
	}
	if(v) {
		if($("#table-letras tbody tr").length) {
			if(_forma_pago_ == "C") {
				ventana.alert({titulo: "Finalizar canje", mensaje: "Para efectuar el canje del recibo, Dir&iacute;jase"+
					" a la pesta&ntilde;a \"Canjear recibos\" y haga clic en el boton [Aplicar] y [Guardar]"});
				return;
			}
			
			if($("#total_pagar").required({numero:true, tipo:"float"})) {
				var e1 = e2 = 0;
				if(_forma_pago_ == "L") {
					e1 = parseInt($("#letras_pagar").val());
					e2 = $("#table-letras tbody tr.success").length;
				}
				else {
					e1 = parseFloat($("#monto_pagar").val());
					e2 = parseFloat($("#total_pagar").val());
				}
				if(e1 != e2) {
					ventana.alert({titulo: "Advertencia", mensaje: "Existe algo irregular en el proceso"
						+" de pago, por favor vuelva a realizar todo el proceso desde el inicio"});
					return;
				}
				
				var deuda = parseFloat($("#total_total").val());
				var pagar = parseFloat($("#total_pagar").val());
				if(deuda >= pagar) {
					/* jPay.create({
						idtipopago: $("#idtipopago_temp").val()
						,monto_pagar: pagar
						,disabled: true
						,onSave: function(datos) {
							var str = $("#form-pago").serialize();
							str += "&"+$("#form-letras").serialize();
							str += "&idcredito="+$("#nro_credito").val();
							alert(str);
							return;
							ajax.post({url: _base_url+"cuentas_cobrar/amortizar", data: str}, function(data) {
								ventana.alert({titulo:"", mensaje:"Pago realizado correctamente"}, function() {
									$("#nro_credito").trigger("change");
								});
							});
						}
					});
					jPay.show(); */
					pay.disabledTipopago(true);
					pay.setIdtipopago($("#idtipopago_temp").val());
					pay.setFecha($("#fecha_pago").val());
					pay.setMonto(pagar);
					
					$(".monto_entregado").val($("#total_pagar").val());
					$(".monto_entregado").trigger("keyup");
					setTimeout(function(){
						$(".monto_entregado").focus();
					},800);
					
					pay.ok(function(datos) {
						var str = $("#form-pago").serialize();
						str += "&"+$("#form-letras").serialize();
						str += "&idcredito="+$("#nro_credito").val();
						str += "&"+datos;
						
						ajax.post({url: _base_url+"cuentas_cobrar/amortizar", data: str}, function(data) {
							ventana.alert({titulo:"", mensaje:"Pago realizado correctamente"}, function() {
								$("#nro_credito").trigger("change");
								$("#serie").trigger("change");
								$("#letras_pagar,#monto_pagar").val("");
							});
						});
					});
					pay.show();
				}
				else {
					ventana.alert({titulo:"", mensaje:"El monto a pagar es mayor a la deuda"});
				}
			}
			else {
				ventana.alert({titulo:"", mensaje:"Ingrese la cantidad a amortizar"});
			}
		}
		else {
			ventana.alert({titulo:"", mensaje:"No existen letras para el credito o ya han sido canceladas"});
		}
	}
});

function callback_fecha_popup(nRow, aData, iDisplayIndex) {
	$("td:eq(0)", nRow).html(fecha_es(aData.fecha));
}

// recibo de ingreso
$("#btn_search_recibo").click(function(e) {
	e.preventDefault();
	if($("#nro_credito").required()) {
		// modal para los recibos de ingreso
		jFrame.create({
			title: "Buscar recibos de ingreso"
			,controller: "reciboingreso"
			,method: "grilla_popup"
			,msg: ""
			,widthclass: "modal-lg"
			,data: "idcliente="+$("#credito_idcliente").val()+"&idmoneda="+$("#idmoneda").val()
			,onSelect: function(datos) {
				$("#tipo_recibo").val("I");
				$("#numero_recibo").val(datos.nrodoc);
				$("#fecha_recibo").val(fecha_es(datos.fecha));
				$("#monto_recibo").val(datos.monto);
				$("#idrecibo").val(datos.idreciboingreso);
			}
		});
		
		jFrame.show();
	}
});

// recibo de cobranza
$("#btn_search_cobranza").click(function(e) {
	e.preventDefault();
});

$("#btn_aplica_canje").click(function(e) {
	e.preventDefault();
	var s = true && $("#nro_credito").required();
	s = s && $("#numero_recibo").required();
	s = s && $("#fecha_recibo").required();
	s = s && $("#monto_recibo").required({numero:true,tipo:"float"});
	if(s) {
		_forma_pago_ = "C"; // canje
		recalcular_mora();
		calular_monto_pago();
	}
});

$("#btn_save_canje").click(function(e) {
	e.preventDefault();
	var v = true && $("#nro_credito").required();
	v = v && $("#numero_recibo").required();
	v = v && $("#fecha_recibo").required();
	v = v && $("#monto_recibo").required({numero:true,tipo:"float"});
	if(v) {
		if($.trim($("#idrecibo").val()) == "") {
			ventana.alert({titulo: "Recibo no encontrado", 
			mensaje: "Ha ocurrido algo extra&ntilde;o. Por favor busque el recibo nuevamente."});
			return;
		}
		if($("#table-letras tbody tr").length) {
			if($("#total_pagar").required({numero:true, tipo:"float"})) {
				var e1 = parseFloat($("#monto_recibo").val());
				var e2 = parseFloat($("#total_pagar").val());
				if(e1 != e2) {
					ventana.alert({titulo: "Advertencia", mensaje: "Existe algo irregular en el proceso"
						+" de canje, por favor vuelva a realizar todo el proceso desde el inicio"});
					return;
				}
				
				var deuda = parseFloat($("#total_total").val());
				var pagar = parseFloat($("#total_pagar").val());
				if(deuda >= pagar) {
					var str = $("#form-canje").serialize();
					str += "&"+$("#form-letras").serialize();
					str += "&idcredito="+$("#nro_credito").val();
					
					ajax.post({url: _base_url+"cuentas_cobrar/canjear", data: str}, function(data) {
						ventana.alert({titulo:"", mensaje:"Pago realizado correctamente"}, function() {
							$("#nro_credito").trigger("change");
							$("#serie").trigger("change");
							$("#letras_pagar,#monto_pagar").val("");
						});
					});
				}
				else {
					ventana.alert({titulo:"", mensaje:"El monto del recibo es mayor a la deuda. &iquest;Que ha pasado aqui?"});
				}
			}
			else {
				ventana.alert({titulo:"", mensaje:"Antes haga clic en el boton [Aplicar]."});
			}
		}
		else {
			ventana.alert({titulo:"", mensaje:"No existen letras para el credito o ya han sido canceladas"});
		}
	}
});

// inicializando datos
init();

$(".btn-change").click(function(e) {
	e.preventDefault();
	if($.trim($("#nro_credito").val()) == "")
		return;
	var t = $(this).data("target");
	
	$("#"+t+"_prev").val($("#"+t+"_credito").val());
	$("#modal-"+t).modal("show");
});

$(".btn-search-change").click(function(e) {
	e.preventDefault();
	var t = $(this).data("target");
	
	jFrame.create({
		title: "Buscar "+t
		,controller: "cliente"
		,method: "grilla_popup"
		,msg: ""
		,onSelect: function(datos) {
			$("#"+t+"_new").val(datos.cliente);
			$("#id"+t).val(datos.idcliente);
		}
	});
	
	jFrame.show();
});

$(".btn-save-modal").click(function(e) {
	e.preventDefault();
	var t = $(this).data("target");
	
	if( $(":input:not([id$='_prev'])", "#form-"+t).required() ) {
		var s = "idcredito="+$("#nro_credito").val()+"&"+$("#form-"+t).serialize();
		
		ajax.post({url: _base_url+"cuentas_cobrar/guardar_datos", data:s, dataType: 'json'}, function(res) {
			ventana.alert({titulo: "", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				$("#"+t+"_credito").val($("#"+t+"_prev").val());
				if(t == "cliente") {
					$("#credito_idcliente").val($("#idcliente").val());
					cargarListaCreditos($("#credito_idcliente").val(), $("#nro_credito").val());
				}
				$("#modal-"+t).modal("hide");
			});
		});
	}
});

$("#btn-amortizaciones").click(function(e) {
	e.preventDefault();
	if($.trim($("#nro_credito").val()) == "")
		return;
	
	get_amortizaciones($("#nro_credito").val());
	
	$("#modal-amortizaciones").modal("show");
});

function get_amortizaciones(idcredito) {
	ajax.post({url: _base_url+"cuentas_cobrar/get_amortizaciones/"+idcredito, dataType: 'json'}, function(res) {
		$("#table-amortizaciones tbody tr .btn-del-amortizacion").tooltip('destroy');
		$("#table-amortizaciones tbody tr").remove();
		if(res.array.length) {
			var last = res.last_recibo, table = new Table(), data;
			
			for(var i in res.array) {
				data = res.array[i];
				
				rec = '<span class="text-navy">'+data.recibo+'</span>';
				if(data.idrecibo == last) {
					rec += '<span class="pull-right">'+
						'<button class="btn btn-white btn-xs btn-del-amortizacion" data-toggle="tooltip" data-recibo="'+
						data.recibo+'"'+'data-idrecibo="'+data.idrecibo+'" data-idsucursal="'+data.idsucursal+
						'" title="Eliminar recibo"><i class="fa fa-trash"></i></button></span>';
				}
				
				table.tr({index: data.idrecibo});
				table.td(data.fecha);
				table.td(data.hora);
				table.td(data.idletra);
				table.td(data.monto, {class:'text-navy'});
				table.td(data.mora, {class:'text-navy'});
				table.td("<small>"+data.moneda+"</small>");
				table.td("<small>"+data.tipo_pago+"</small>");
				table.td(rec);
				table.td("<small>"+data.usuario+"</small>");
				table.td("<small>"+data.sucursal+"</small>");
			}
			
			$("#table-amortizaciones tbody").html(table.to_string());
		}
	});
}

$("#table-amortizaciones").on("click", ".btn-del-amortizacion", function(e) {
	e.preventDefault();
	var self = $(this);
	
	ventana.confirm({
		titulo:"Confirmar"
		,mensaje:"Esta seguro que desea eliminar el Recibo de Ingreso "+self.data("recibo")
		,textoBotonAceptar: "Eliminar"
	}, function(ok) {
		if(ok) {
			var s = "idcredito="+$("#nro_credito").val()+"&idreciboingreso="+self.data("idrecibo");
			
			ajax.post({url: _base_url+"cuentas_cobrar/anular_recibo", data: s, dataType: 'json'}, function() {
				$("#nro_credito").trigger("change");
				get_amortizaciones($("#nro_credito").val());
				ventana.alert({titulo: "", mensaje: "El Recibo de Ingreso "+self.data("idrecibo")+" se ha anulado correctamente", tipo:"success"});
			});
		}
	});
});

$("#print-cronograma").click(function(e){
	e.preventDefault();
	str = "&idcredito="+$("#nro_credito").val();
	str+= "&nro_credito="+$("#nro_credito option:selected").text();
	str+= "&idcliente="+$("#credito_idcliente").val();
	str+= "&resumen=true";
	open_url_windows(_controller+"/imprimir?"+str);
});