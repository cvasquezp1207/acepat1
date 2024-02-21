function init() {
	$("#search").focus();
}

function cargarListaCreditos(idcliente, idcredito) {
	ajax.post({url: _base_url+"creditoscancelados/get_lista_credito/"+idcliente+"/S", dataType: 'json'}, function(arr) {
		var options = '';
		if(arr.length) {
			for(var i in arr) {
				options += '<option value="'+arr[i].idcredito+'">'+arr[i].nro_credito+" "+arr[i].comprobante+'</option>';
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
	$("#table-letras tbody").empty();
	if(arr.length) {
		var table = new Table(), data;
		var c, f, e, j = 0, cls = '';
		
		for(var i in arr) {
			f = true;
			data = arr[i];
			
			k = parseInt(i) + 1;
			letra = parseFloat(data.monto_letra);
			pagado = parseFloat(data.monto_pagado);
			saldo = letra - pagado;
			gasto = (data.gastos) ? data.gastos : 0;
			mora = parseFloat(data.mora);
			cuota = letra - gasto;
			monto = parseFloat(cuota) + parseFloat(gasto)
			descuento = parseFloat(data.descuento);
			total = saldo + mora - descuento;
			subtotal = monto + parseFloat(mora) - parseFloat(descuento);
			title = '';
			
			content_recibo = data.recibo+"&nbsp;&nbsp;";
			if(f) {
				// content_recibo += '<span class="pull-right">';
				// content_recibo += '<button class="btn btn-white btn-xs btn-del-amortizacion-c" data-toggle="tooltip" data-recibo="'+data.recibo+'" data-idri="'+data.id_ri+'" data-idnc="'+data.id_nc+'" title="Eliminar recibo">'+'<i class="fa fa-trash"></i></button>';
				// content_recibo += '</span>';
				f = false;
			}
			
			table.tr({index: k});
			table.td("<input type='checkbox' class='idletra' name='idletra[]' value='"+data.idletra+"' style='display:none;'>"+data.nro_letra);
			table.td(fecha_es(data.fecha_vencimiento));
			table.td(fecha_es(data.fecha_pago, "d/m/Y"));
			table.td(cuota.toFixed(2),{'class':'text-number'});
			table.td(gasto, {'class':'text-number'});
			table.td(monto.toFixed(2), {'class':'text-number'});
			table.td(mora.toFixed(2), {'class':'text-number'});
			table.td(descuento.toFixed(2), {'class':'text-number'});
			table.td(subtotal.toFixed(2), {'class':'text-number'});
			table.td(content_recibo,{'class':'text-navy'});
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
		$("#venta_fecha_venta").val(fecha_es(data.venta.fecha_venta));
		if(data.detalle_venta.length) {
			var html = '';
			for(var i in data.detalle_venta) {
				html += '<tr><td><small>'+data.detalle_venta[i].descripcion+'</small></td>'+
					'<td class="text-navy"><small style="white-space:nowrap;">'+
					data.detalle_venta[i].cantidad+' '+data.detalle_venta[i].abreviatura+'   ||  '+data.venta.simbolo_moneda+' '+number_format(data.detalle_venta[i].precio,fixed_venta,'.',',')+
					'</small></td></tr>';
			}
			$("#table-productos tbody").html(html);
		}
	});
}

function cargarDatosCredito(idcredito) {
	ajax.post({
		url: _base_url+"credito/get_all/"+idcredito+"/1"
		,data:"pagado=S"
		, dataType: 'json'
	}, function(data) {
		cargarDatosVenta(data.credito.idventa);
		$("#credito_idventa").val(data.credito.idventa);
		$("#credito_fecha_emision").val(fecha_es(data.credito.fecha_credito, false));
		$("#credito_dias_atrazo").val(data.dias_atrazo);
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
		$("#cliente_direccion").val(data.credito_view.direccion);
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
		
		generarLetras(data.arr_letras_pendientes);
		calcularTotales();
	});
}

$("#search").autocomplete({
	source: function( request, response ) {
		ajax.post({url: _base_url+"cuentas_cobrar/autocomplete", 
		data: "m=10&q="+request.term+"&f="+$("#filter").val()+"&pagado=S", dataType: 'json'}, function(data) {
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

var _forma_pago_ = null;

function callback_fecha_popup(nRow, aData, iDisplayIndex) {
	$("td:eq(0)", nRow).html(fecha_es(aData.fecha));
}

// inicializando datos
init();

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
				if(data.tipo_recibo == last.tipo_recibo && $.trim(data.id_ri) == $.trim(last.idrecibo_ingreso) && $.trim(data.id_nc) == $.trim(last.idnotacredito)) {
					rec += '<span class="pull-right">'+
						'<button class="btn btn-white btn-xs btn-del-amortizacion" data-toggle="tooltip" data-recibo="'+
						data.recibo+'" data-idri="'+$.trim(data.id_ri)+'" data-idnc="'+$.trim(data.id_nc)+'" data-tipo="'+
						$.trim(data.tipo_recibo)+'" data-idsucursal="'+data.idsucursal+'" title="Eliminar recibo">'+
						'<i class="fa fa-trash"></i></button></span>';
				}
				
				table.tr();
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
		,mensaje:"Desea eliminar todas las amortizaciones realizadas por el documento "+self.data("recibo")
		,textoBotonAceptar: "Eliminar"
	}, function(ok) {
		if(ok) {
			var msg = "Eliminando el documento "+self.data("recibo")+"... ind&iacute;quenos por qu&eacute; desea eliminar el documento";
			if(self.data("tipo") == "NC")
				msg = "Ind&iacute;quenos por qu&eacute; desea eliminar las amortizaciones relacionadas al documento "+self.data("recibo");
			
			setTimeout(function() {
				ventana.prompt({titulo:"",
					mensaje:"<div style='text-align:left;margin-bottom:6px;'>"+msg+"</div>",
					tipo: false,
					textoBotonAceptar: "Eliminar",
					placeholder: 'Ingrese motivo'
				}, function(inputValue) {
					if(inputValue !== false) {
						var l = self.data("recibo");
						var t = self.data("tipo");
						var s = "idcredito=" + $("#nro_credito").val() + "&tipo=" + self.data("tipo") +
							"&idri=" + self.data("idri") + "&idnc=" + self.data("idnc") + "&motivo=" + inputValue;
						
						ajax.post({url: _base_url+"cuentas_cobrar/anular_recibo", data: s, dataType: 'json'}, function() {
							reload_lista();
							var msg = "El Documento "+l+" se ha anulado correctamente.";
							if(t == "NC") {
								msg = "Se han eliminado todas las amortizaciones relacionadas con la Nota de Credito "+l+
									". Si desea eliminar ademas la nota de credito vaya al modulo [Nota de Credito]";
							}
							ventana.alert({titulo: "", mensaje: msg, tipo:"success"});
						});
					}
				});
			}, 300);
		}
	});
});

function reload_lista(){
	cargarListaCreditos($("#credito_idcliente").val(),'');
	$("#modal-amortizaciones").modal("hide");
}

$("#print-cronograma").click(function(e){
	e.preventDefault();
	str = "&idcredito="+$("#nro_credito").val();
	str+= "&nro_credito="+$("#nro_credito option:selected").text();
	str+= "&idcliente="+$("#credito_idcliente").val();
	str+= "&resumen=true";
	open_url_windows(_controller+"/imprimir?"+str);
});