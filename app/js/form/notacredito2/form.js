function LoadSerieDoc(idtipodocumento, tpl) {
	tpl = tpl || "#serie";
	if($(tpl).length <= 0 || $(tpl).prop("tagName") != "SELECT")
		return;
	
	if($.isNumeric(idtipodocumento)) {
		reload_combo(tpl, 
		{
			controller: "tipo_documento",
			method: "get_series", 
			data: "idtipodocumento="+idtipodocumento
		}, function() {
			$(tpl).trigger("change");
		});
	}
	else {
		$(tpl).html("").trigger("change");
	}
}

function LoadNumeroDoc(idtipodocumento, serie, tpl) {
	tpl = tpl || "#numero";
	if($(tpl).length <= 0 || $(tpl).prop("tagName") != "INPUT")
		return;
	
	if($.isNumeric(idtipodocumento) && $.trim(serie) != "") {
		ajax.post({
			url: _base_url+"tipo_documento/get_correlativo", 
			data: "idtipodocumento="+idtipodocumento+"&serie="+serie
		}, function(res) {
			$(tpl).val(res.correlativo);
		});
	}
	else {
		$(tpl).val("");
	}
}

function init() {
	if(_es_nueva_nota_) {
		// LoadSerieDoc($idtipodocumento);
		$("#serie").trigger("change");
		$("#idmoneda").trigger("change");
	}
}

$(document).on("change", "#serie", function() {
	LoadNumeroDoc($idtipodocumento, $(this).val());
});

$("#btn-buscar-cliente").click(function(e) {
	e.preventDefault();
	
	jFrame.create({
		title: "Buscar Cliente"
		,controller: "cliente"
		,method: "grilla_popup"
		,msg: ""
		,onSelect: function(datos) {
			$("#cliente").val(datos.cliente);
			$("#idcliente").val(datos.idcliente);
			if(datos.ruc)
				$("#rucdni").val(datos.ruc);
			else if(datos.dni)
				$("#rucdni").val(datos.dni);
		}
	});
	
	jFrame.show();
	return false;
});

function getUnidades(tr, idproducto, idunidad) {
	ajax.post({url: _base_url+"producto/get_unidades/"+idproducto}, function(res) {
		var data, options='';
		
		if($.isArray(res)) {
			for(var i in res) {
				data = res[i];
				options += '<option value="'+data.idunidad+'" title="'+data.descripcion+'" count="'+
					data.cantidad_unidad_min+'">'+data.abreviatura+'</option>';
			}
		}
		
		var d = $(".deta_idunidad", tr).prop("disabled");
		
		$(".deta_idunidad", tr).prop("disabled", false);
		
		$(".deta_idunidad", tr).html(options);
		if(idunidad)
			$(".deta_idunidad", tr).val(idunidad);
		
		$(".deta_idunidad", tr).prop("disabled", d);
	});
}

function cargarDetalleVenta(idventa) {
	ajax.post({url: _base_url+"venta/get_detalle/"+idventa, dataType: 'json'}, function(res) {
		$("#tbl-detalle tbody tr").remove();
		// alert();
		if(res.length) {
			var table = new Table(), cls, data, sty, ser, imp;
			
			for(var i in res) {
				data = res[i];
				data.codgrupo_igv = data.codgrupo_igv || default_grupo_igv;
				data.codtipo_igv = data.codtipo_igv || false;
				cls = "";
				sty = "";
				ser = [""];
				if(data.controla_serie == 'S')  {
					cls = "has_serie";
					sty = "readonly";
					data.cantidad = 1;
					ser = String(data.serie).split("|");
				}
				
				for(var j=0; j < ser.length; j++) {
					imp = data.cantidad * data.precio;
					
					table.tr({class: cls});
					
					table.td('<input type="text" name="deta_producto[]" class="form-control input-xs deta_producto" value="'+data.producto+'" readonly>');
					table.td('<select name="deta_idunidad[]" class="form-control input-xs deta_idunidad"></select>');
					table.td('<input type="text" name="deta_cantidad[]" class="form-control input-xs deta_cantidad" value="'+data.cantidad+'" '+sty+'>');
					table.td('<input type="text" name="deta_serie[]" class="form-control input-xs deta_serie" value="'+ser[j]+'" readonly>');
					table.td('<input type="text" name="deta_precio[]" class="form-control input-xs deta_precio" value="'+data.precio+'">');
					table.td('<input type="text" name="deta_importe[]" class="form-control input-xs deta_importe" value="'+imp+'" readonly>');
					
					table.td('<select name="deta_grupo_igv[]" class="form-control input-xs deta_grupo_igv">'+$("#grupo_igv_temp").html()+'</select>');
					table.td('<select name="deta_tipo_igv[]" class="form-control input-xs deta_tipo_igv">'+$("#tipo_igv_temp").html()+'</select>');
					
					table.td('<input type="checkbox" name="deta_iddetalle[]" class="deta_iddetalle" value="'+data.iddetalle_venta+'" title="Seleccionar item">', {class:"text-center"});
					
					table.td('<input type="hidden" name="deta_idproducto[]" class="deta_idproducto" value="'+data.idproducto+'">'+
						'<input type="hidden" name="deta_idalmacen[]" class="deta_idalmacen" value="'+data.idalmacen+'">'+
						'<input type="hidden" name="deta_cantidad_real[]" class="deta_cantidad_real" value="'+data.cantidad+'">'+
						'<input type="hidden" name="deta_controla_stock[]" class="deta_controla_stock" value="'+data.controla_stock+'">'+
						'<input type="hidden" name="deta_controla_serie[]" class="deta_controla_serie" value="'+data.controla_serie+'">', {style:"display:none"});
						
					$("#tbl-detalle tbody").append(table.to_string());
					
					tr = $("#tbl-detalle tbody tr:last");
					
					if(data.codgrupo_igv)
						$(".deta_grupo_igv", tr).val(data.codgrupo_igv);
					setTipoIgv(tr, data.codtipo_igv);
					
					getUnidades(tr, data.idproducto, data.idunidad);
				}
			}
			
			$("#tbl-detalle tbody tr .deta_cantidad").numero_real();
			$("#tbl-detalle tbody tr .deta_precio").numero_real();
			$("#tbl-detalle tbody tr :input:not(.deta_iddetalle)").prop("disabled", true);
		}
	});
}

$("#btn-buscar-venta").click(function(e) {
	e.preventDefault();
	jFrame.create({
		title: "Buscar venta"
		,controller: "venta"
		,method: "grilla_popup"
		,msg: ""
		,widthclass: "modal-lg"
		,data: "nc=S"
		,onSelect: function(datos) {
			$("#idventa").val(datos.idventa);
			$("#idmoneda").val(datos.idmoneda);
			$("#idcliente").val(datos.idcliente);
			$("#cliente").val(datos.full_nombres);
			if(datos.ruc)
				$("#rucdni").val(datos.ruc);
			else if(datos.dni)
				$("#rucdni").val(datos.dni);
			$("#tipo_comprobante").val(datos.tipo_documento);
			$("#cambio_moneda").val(datos.cambio_moneda);
			$("#iddocumento_ref").val(datos.idtipodocumento);
			$("#serie_ref").val(datos.serie);
			$("#numero_ref").val(datos.correlativo);
			$("#nrodoc_ref").val(datos.serie+'-'+datos.correlativo);
			$("#fecha_ref").val(datos.fecha_venta_format);
			$("#monto_ref").val(datos.total);
			
			cargarDetalleVenta(datos.idventa);
		}
	});
	
	jFrame.show();
});


$("#tbl-detalle").on("change", ".deta_iddetalle", function() {
	var tr = $(this).closest("tr");
	var bool = ( ! $(this).is(":checked"));
	$(":input:not(.deta_iddetalle)", tr).prop("disabled", bool);
	
	tr.removeClass("item-select");
	if( ! bool)
		tr.addClass("item-select");
	
	calcularTotales();
});

$("#tbl-detalle").on("keyup", ".deta_cantidad", function() {
	var tr = $(this).closest("tr");
	calcularImporte(tr);
	calcularTotales();
});

$("#tbl-detalle").on("blur", ".deta_cantidad", function() {
	var tr = $(this).closest("tr");
	var c = parseFloat($(this).val());
	if( ! isNaN(c)) {
		var r = parseFloat($(".deta_cantidad_real", tr).val());
		if(c > r) {
			ventana.alert({titulo: "", mensaje: "La cantidad ingresada debe ser menor a "+
				r+" "+$(".deta_idunidad option:selected", tr).text()}, function() {
				$(".deta_cantidad", tr).focus();
			});
		}
	}
});

$("#tbl-detalle").on("keyup", ".deta_precio", function() {
	var tr = $(this).closest("tr");
	calcularImporte(tr);
	calcularTotales();
});

$("#tbl-detalle").on("change", ".deta_grupo_igv", function() {
	setTipoIgv($(this).closest("tr"));
	calcularTotales();
});

$("#check_all").on("change", function() {
	var bool = $(this).is(":checked");
	$("#tbl-detalle tbody tr .deta_iddetalle").prop("checked", bool).trigger("change");
});

function calcularTotal() {
	if( $.isNumeric($("#subtotal").val()) ) {
		var total = parseFloat($("#subtotal").val());
		if($("#valor_igv").is(":checked") && $.isNumeric($("#igv").val())) {
			total += parseFloat($("#igv").val());
		}
		$("#total").val(total.toFixed(2));
		return;
	}
	$("#total").val("");
}

function calcularIgv() {
	if( $("#tbl-detalle tbody tr.item-select").length ) {
		var impuesto = 0, importe, igv;
        $("#tbl-detalle tbody tr.item-select").each(function() {
			importe = parseFloat($("input.deta_importe", this).val());
			if(isNaN(importe))
				importe = 0;
			
			igv = parseFloat( $(".deta_grupo_igv>option:selected", this).data("igv") );
            if(isNaN(igv))
                igv = 0;
			
            impuesto += importe * igv;
        });
		$("#igv").val(impuesto.toFixed(2));
		return;
	}
	$("#igv").val("");
}

function calcularSubtotal() {
	if( $("#tbl-detalle tbody tr.item-select").length ) {
		var t = 0;
		$("#tbl-detalle tbody tr.item-select").each(function() {
			if($.isNumeric($("input.deta_importe", this).val())) {
				t += parseFloat($("input.deta_importe", this).val());
			}
		});
		$("#subtotal").val(t.toFixed(2));
		return;
	}
	$("#subtotal").val("");
}

// $("#valor_igv").on("change", function() {
	// calcularIgv();
	// calcularTotal();
// });

function calcularTotales() {
	calcularSubtotal();
	calcularIgv();
	calcularTotal();
}

$("#idmoneda").on("change", function() {
	if($.isNumeric($(this).val())) {
		ajax.post({url: _base_url+"moneda/get/"+$(this).val()}, function(data) {
			$("#cambio_moneda").val(parseFloat(data.valor_cambio).toFixed(2));
		});
		return;
	}
	$("#cambio_moneda").val("");
});

$("#btn_save_notacredito").click(function(e) {
	e.preventDefault();
	var v = true;
	v = v && $("#serie").required();
	v = v && $("#numero").required();
	v = v && $("#motivo").required();
	v = v && $("#cliente").required();
	v = v && $("#tipo_comprobante").required();
	v = v && $("#nrodoc_ref").required();
	v = v && $("#fecha_ref").required();
	v = v && $("#monto_ref").required();
	v = v && $("#descripcion").required();
	if(v) {
		if(!$("#idventa").required()) {
			ventana.alert({titulo: "Error", mensaje: "Seleccione la venta (documento) que modifica"});
			return;
		}
		if(!$("#idcliente").required()) {
			ventana.alert({titulo: "Error", mensaje: "Seleccione un cliente de la lista"});
			return;
		}
		
		var table = $("#tbl-detalle");
		
		if($("tbody tr.item-select", table).length < 1) {
			ventana.alert({titulo: "Error", mensaje: "Seleccione algun item de la tabla"});
			return;
		}
		v = v && $("tr.item-select .deta_cantidad", table).required({numero:true, tipo:"float"});
		v = v && $("tr.item-select.has_serie .deta_serie", table).required();
		v = v && $("tr.item-select .deta_precio", table).required({numero:true, tipo:"float", aceptaCero:true});
		v = v && $("tr.item-select .deta_importe", table).required({numero:true, tipo:"float", aceptaCero:true});
		if(v) {
			var a, c, r, msg = '';
			
			$( "tr.item-select", table ).each(function() {
				c = parseFloat($(".deta_cantidad", this).val());
				r = parseFloat($(".deta_cantidad_real", this).val());
				if(c > r) {
					msg = "La cantidad ingresada del item "+$(".deta_producto", this).val()+
						" debe ser menor a "+r+" "+$(".deta_idunidad option:selected", this).text();
					v = false;
					return false;
				}
				
				if($(this).hasClass("has_serie")) {
					if( $(".deta_serie", this).val() != '' ) {
						a = String($(".deta_serie", this).val()).split('|');
						c = c * parseFloat($(".deta_idunidad option:selected", this).attr("count"));
						if(a.length != c) {
							v = false;
							msg = "Ingrese las series del producto "+$(".deta_producto", this).val();
						}
					}
					else {
						v = false;
						msg = "Ingrese las series del producto "+$(".deta_producto", this).val();
					}
				}
				return v;
			});
			
			if(v == false && msg != '') {
				ventana.alert({titulo: '', mensaje: msg});
				return;
			}
			
			v = v && $("#subtotal").required({numero:true, tipo:'float'});
			v = v && $("#total").required({numero:true, tipo:'float'});
			if(v)
				form.guardar();
		}
	}
});

function setTipoIgv(tr, sel) {
	if(sel) {}
	else {
		sel = $(".deta_grupo_igv>option:selected", tr).data("tipo_igv_default");
	}
	
	$(".deta_tipo_igv", tr).val(sel);
}

function calcularImporte(tr) {
	var c = parseFloat($(".deta_cantidad", tr).val());
	if(isNaN(c))
		c = 0;
	var p = parseFloat($(".deta_precio", tr).val());
	if(isNaN(p))
		p = 0;
	var i = c * p;
	
	$(".deta_importe", tr).val(i.toFixed(2));
}

init();