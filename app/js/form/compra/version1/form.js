if(typeof form == 'undefined') {
	form = {};
}

form.guardar_producto = function() {
	var data = $("#form_producto").serialize();
	model.save(data, function(res) {
		$("#modal-producto").modal("hide");
		$("#producto_descripcion").focus().select();
	}, "producto");
}

form.guardar_proveedor = function() {
		var data = $("#form_proveedor").serialize();
		model.save(data, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				$("#proveedor_descripcion").attr("value",res.nombre);
				$("#proveedor_idproveedor").attr("value",res.idproveedor);
				$("#modal-proveedor").modal("hide");
			});
		}, "proveedor");
}

form.guardar_unidad_medida = function() {
	var data = $("#form_unidad_medida").serialize();
	model.save(data, function(res) {
		updateUnidades({idproducto: $("#uni_idproducto").val()});
		$("#modal-unidad_medida").modal("hide");
	}, "producto", "guardar_unidad");
}

function agregarProducto(idproducto, callback) {
	ajax.post({url: _base_url+"producto/get/"+idproducto}, function(data) {
		if($("#tbl-detalle tbody tr[index="+data.idproducto+"]").length) {
			ventana.confirm({
				titulo:"Confirmar"
				,mensaje:"El producto "+data.descripcion+" ya se encuentra en la tabla. ¿Desea volver a agregar otra vez?"
				,textoBotonAceptar: "Agregar"
			}, function(ok) {
				if(ok) {
					addDetalle(data);
					updateUnidades(data);
					calcularDatos();
				}
				if($.isFunction(callback)) {
					callback();
				}
			});
		}
		else {
			addDetalle(data);
			updateUnidades(data);
			calcularDatos();
			if($.isFunction(callback)) {
				callback();
			}
		}
	});
}

function updateUnidades(params, tr) {
	if(params && typeof params.idproducto == "undefined")
		return;
	
	ajax.post({url: _base_url+"producto/get_unidades/"+params.idproducto}, function(res) {
		var data, options='';
		
		if($.isArray(res)) {
			for(var i in res) {
				data = res[i];
				options += '<option value="'+data.idunidad+'" title="'+data.descripcion+'" count="'+data.cantidad_unidad_min+'">'+data.abreviatura+'</option>';
			}
		}
		
		options += '<option value="N">Asignar otra unidad de medida?</option>';
		
		if(tr) {
			v = (params.idunidad) ? parseInt(params.idunidad) : parseInt($(".deta_idunidad", tr).val());
			$(".deta_idunidad", tr).html(options);
			if(!isNaN(v)) {
				$(".deta_idunidad", tr).val(v).trigger("change");
			}
			return;
		}
		
		$("#tbl-detalle tbody tr[index="+params.idproducto+"] select.deta_idunidad").each(function() {
			v = (params.idunidad) ? parseInt(params.idunidad) : parseInt($(this).val());
			
			$(this).html(options);
			if(!isNaN(v)) {
				$(this).val(v).trigger("change");
			}
		});
	});
}

function addDetalle(data) {
	if(typeof data.cantidad == "undefined") {
		data.cantidad = 1;
	}
	if(typeof data.serie == "undefined" || data.serie == null) {
		data.serie = "";
	}
	if(typeof data.precio_compra == "undefined" || data.precio_compra == null) {
		data.precio_compra = parseFloat(0).toFixed(_fixed_compra);
	}
	
	desc = '<input type="hidden" name="deta_idproducto[]" class="deta_idproducto" value="'+data.idproducto+'">';
	if(data.idpedido) {
		desc += '<input type="hidden" name="deta_idpedido[]" class="deta_idpedido" value="'+data.idpedido+'">';
	}
	desc += '<input type="hidden" name="deta_producto[]" class="deta_producto" value="'+data.descripcion_detallada+'">';
	desc += data.descripcion_detallada;
	
	cls = (data.controla_serie == 'S') ? "has_serie" : "";
	
	c = $("#tbl-detalle tbody tr").length + 1;
	
	table = new Table();
	table.tr({index: data.idproducto, class: cls});
	table.td('<span class="badge">'+c+'</span>', {class: "item"});
	table.td(desc, {class: "text-sm"});
	table.td('<select name="deta_idunidad[]" class="form-control input-xs deta_idunidad" data-toggle="tooltip" title=""></select>');
	table.td('<input type="text" name="deta_cantidad[]" class="form-control input-xs deta_cantidad" value="'+data.cantidad+'">');
	table.td('<input type="text" name="deta_precio[]" class="form-control input-xs deta_precio" value="'+data.precio_compra+'">');
	table.td('<input type="text" name="deta_precio_igv[]" class="form-control input-xs deta_precio_igv" value="'+data.precio_compra+'">');
	table.td('<input type="text" name="deta_importe[]" class="form-control input-xs font-bold deta_importe">');
	table.td('<input type="text" name="deta_importe_igv[]" class="form-control input-xs font-bold deta_importe_igv">');
	table.td('<input type="text" name="deta_igv[]" class="form-control input-xs text-success deta_igv" readonly>');
	table.td('<input type="text" name="deta_flete[]" class="form-control input-xs text-success deta_flete" readonly>');
	table.td('<input type="text" name="deta_descuento[]" class="form-control input-xs text-success deta_descuento" readonly>');
	table.td('<input type="text" name="deta_costo[]" class="form-control input-xs text-success font-bold deta_costo" value="'+data.precio_compra+'" readonly>');
	// if(data.tipo == 'P' && data.controla_serie == 'S') {
	if(cls == 'has_serie') {
		table.td('<input type="hidden" name="deta_series[]" class="deta_series" value="'+data.serie+'">'+
			'<button class="btn btn-success btn-xs btn_deta_serie" data-toggle="tooltip" title="Ingresar las series del producto">'+
			'<i class="fa fa-cubes"></i></button>');
	}
	else {
		table.td('<input type="hidden" name="deta_series[]" class="deta_series" value="'+data.serie+'">');
	}
	table.td('<button class="btn btn-danger btn-xs btn_deta_delete" data-toggle="tooltip" title="Eliminar registro"><i class="fa fa-trash"></i></button>');
	
	table.td('<input type="hidden" name="deta_controla_stock[]" class="deta_controla_stock" value="'+data.controla_stock+'">'+
		'<input type="hidden" name="deta_controla_serie[]" class="deta_controla_serie" value="'+data.controla_serie+'">', {style:"display:none;"});
	
	$("#tbl-detalle tbody").append(table.to_string());
	$("#tbl-detalle tbody tr:last input.deta_cantidad").numero_real();
	$("#tbl-detalle tbody tr:last input.deta_precio").numero_real();
	
	return $("#tbl-detalle tbody tr:last"); // ultima fila creada
}

/* function appendDetalle(data) {
	if(typeof data.cantidad != "undefined") {
		if($("#tbl-detalle tbody tr[index="+data.idproducto+"]").length) {
			var tr = $("#tbl-detalle tbody tr[index="+data.idproducto+"]:first");
			if(String($("input.deta_cantidad", tr).val()).indexOf(".") != -1) {
				cantidad = parseFloat($("input.deta_cantidad", tr).val());
			}
			else {
				cantidad = parseInt($("input.deta_cantidad", tr).val());
			}
			cantidad += data.cantidad;
			$("input.deta_cantidad", tr).val(cantidad);
		}
	}
} */

function show_dialog_unidad_medida(tr) {
	ajax.post({url: _base_url+"producto/get_all/"+tr.attr("index")}, function(data) {
		$("#uni_producto_descripcion").text(data.producto.descripcion_detallada);
		$("#uni_idproducto").val(data.producto.idproducto);
		$("#ref_unidad_medida").text(data.producto_unidad.abreviatura);
		$("#ref_unidad_medida").attr("data-original-title", data.producto_unidad.descripcion);
		$("#tabla_unidad_medida tbody").html("");
		appendUnidadMedida(data.unidades);
		ajax.post({url: _base_url+"unidad/get_all/"}, function(data) {
			UNIDAD_MEDIDA = data;
			actualizarComboUnidades();
		});
		$("#modal-unidad_medida").modal("show");
	});
}

function calcularDatos(tr, setPrecioIgv, setImporte, setImporteIgv) {
	if(typeof setPrecioIgv != "boolean")
		setPrecioIgv = true;
	if(typeof setImporte != "boolean")
		setImporte = true;
	if(typeof setImporteIgv != "boolean")
		setImporteIgv = true;
	
	if(setImporte) {
		calcularImporte(tr);
	}
	calcularIgv(tr);
	if(setPrecioIgv) {
		calcularPrecioIgv(tr);
	}
	if(setImporteIgv) {
		calcularImporteIgv(tr);
	}
	calcularSubtotalCompra();
	calcularIgvCompra();
	calcularDescuento();
	calcularTotalCompra();
	calcularFlete();
	calcularGasto();
	calcularPrecioCosto();
}

function calcularImporte(tr) {
	if(tr) {
		if($.isNumeric($("input.deta_cantidad", tr).val()) && $.isNumeric($("input.deta_precio", tr).val())) {
			var importe = parseFloat($("input.deta_cantidad", tr).val()) * parseFloat($("input.deta_precio", tr).val());
			$("input.deta_importe", tr).val(importe.toFixed(_fixed_compra));
			return;
		}
		else {
			$("input.deta_importe", tr).val("");
		}
	}
	else {
		$("#tbl-detalle tbody tr").each(function() {
			if($.isNumeric($("input.deta_cantidad", this).val()) && $.isNumeric($("input.deta_precio", this).val())) {
				importe = parseFloat($("input.deta_cantidad", this).val()) * parseFloat($("input.deta_precio", this).val());;
				$("input.deta_importe", this).val(importe.toFixed(_fixed_compra));
			}
			else {
				$("input.deta_importe", this).val("");
			}
		});
		return;
	}
}

function calcularImporteIgv(tr) {
	if(tr) {
		if($.isNumeric($("input.deta_cantidad", tr).val()) && $.isNumeric($("input.deta_precio_igv", tr).val())) {
			var importe = parseFloat($("input.deta_cantidad", tr).val()) * parseFloat($("input.deta_precio_igv", tr).val());
			$("input.deta_importe_igv", tr).val(importe.toFixed(_fixed_compra));
			return;
		}
		else {
			$("input.deta_importe_igv", tr).val("");
		}
	}
	else {
		$("#tbl-detalle tbody tr").each(function() {
			if($.isNumeric($("input.deta_cantidad", this).val()) && $.isNumeric($("input.deta_precio_igv", this).val())) {
				importe = parseFloat($("input.deta_cantidad", this).val()) * parseFloat($("input.deta_precio_igv", this).val());;
				$("input.deta_importe_igv", this).val(importe.toFixed(_fixed_compra));
			}
			else {
				$("input.deta_importe_igv", this).val("");
			}
		});
		return;
	}
}

function calcularSubtotalCompra() {
	if( $("#tbl-detalle tbody tr").length ) {
		var t = 0;
		$("#tbl-detalle tbody tr").each(function() {
			if($.isNumeric($("input.deta_importe", this).val())) {
				t += parseFloat($("input.deta_importe", this).val());
			}
		});
		$("#subtotal").val(t.toFixed(2));
		return;
	}
	$("#subtotal").val("");
}

function calcularIgv(tr) {
	var igv = 0;
	if(tr) {
		if($.isNumeric($("input.deta_precio", tr).val())) {
			igv = 0;
			if($("#valor_igv").is(":checked")) {
				igv = parseFloat($("input.deta_precio", tr).val()) * parseFloat($("#valor_igv").val())/100;
			}
			$("input.deta_igv", tr).val(igv.toFixed(_fixed_compra));
		}
		else {
			$("input.deta_igv", tr).val("");
		}
	}
	
	$("#tbl-detalle tbody tr").each(function() {
		if($.isNumeric($("input.deta_precio", this).val())) {
			igv = 0;
			if($("#valor_igv").is(":checked")) {
				igv = parseFloat($("input.deta_precio", this).val()) * parseFloat($("#valor_igv").val())/100;
			}
			$("input.deta_igv", this).val(igv.toFixed(_fixed_compra));
		}
		else {
			$("input.deta_igv", this).val("");
		}
	});
}

function calcularPrecioIgv(tr) {
	var igv, precio;
	if(tr) {
		if($.isNumeric($("input.deta_precio", tr).val())) {
			igv = parseFloat($("input.deta_igv", tr).val());
			if(isNaN(igv)) {
				igv = 0;
			}
			precio = parseFloat($("input.deta_precio", tr).val()) + igv;
			$("input.deta_precio_igv", tr).val(precio.toFixed(_fixed_compra));
		}
		else {
			$("input.deta_precio_igv", tr).val("");
		}
	}
	
	$("#tbl-detalle tbody tr").each(function() {
		if($.isNumeric($("input.deta_precio", this).val())) {
			igv = parseFloat($("input.deta_igv", this).val());
			if(isNaN(igv)) {
				igv = 0;
			}
			precio = parseFloat($("input.deta_precio", this).val()) + igv;
			$("input.deta_precio_igv", this).val(precio.toFixed(_fixed_compra));
		}
		else {
			$("input.deta_precio_igv", this).val("");
		}
	});
}

function calcularIgvCompra() {
	if( $.isNumeric($("#subtotal").val()) ) {
		if($("#valor_igv").is(":checked")) {
			var igv = parseFloat($("#subtotal").val()) * parseFloat($("#valor_igv").val())/100;
			$("#igv").val(igv.toFixed(2));
			// $("#igv").val(igv);
			return;
		}
	}
	$("#igv").val("");
}

function calcularTotalCompra() {
	if( $.isNumeric($("#subtotal").val()) ) {
		var total = parseFloat($("#subtotal").val());
		if($("#valor_igv").is(":checked") && $.isNumeric($("#igv").val())) {
			total += parseFloat($("#igv").val());
		}
		if($.isNumeric($("#descuento").val())) {
			total -= parseFloat($("#descuento").val());
		}
		$("#total").val(total.toFixed(2));
		return;
	}
	$("#total").val("");
}

function calcularFlete() {
	if($.isNumeric($("#total").val()) && $.isNumeric($("#flete").val())) {
		var totalFlete = getMontoConvertido($("#idmoneda>option:selected").data("abreviatura"), 
			$("#idmoneda_flete>option:selected").data("abreviatura"), $("#cambio_moneda_flete").val(), $("#flete").val());
		$("#flete_convertido").val(totalFlete);
		
		if(totalFlete > 0) {
			var cantidad = 0;
			
			$("#tbl-detalle tbody tr").each(function() {
				if($.isNumeric($("input.deta_cantidad", this).val())) {
					cantidad += parseFloat($("input.deta_cantidad", this).val());
				}
			});
			
			if(cantidad > 0) {
				var fleteitem = totalFlete / cantidad;
				$("input.deta_flete").val(fleteitem.toFixed(_fixed_compra));
			}
			return;
		}
		// var total = parseFloat($("#total").val());
		// if(total > 0) {
			// var flete = parseFloat($("#flete").val()), cantidad, importe, v;
			// if($("#idmoneda_flete").val() != $("#idmoneda").val()) {
				// var tipocambio = parseFloat($("#cambio_moneda").val());
				// if(isNaN(tipocambio))
					// tipocambio = 1;
				// flete = flete * tipocambio;
			// }
			
			// $("#tbl-detalle tbody tr").each(function() {
				// if($.isNumeric($("input.deta_importe", this).val()) && $.isNumeric($("input.deta_cantidad", this).val())) {
					// cantidad = parseFloat($("input.deta_cantidad", this).val());
					// if(cantidad > 0) {
						// importe = parseFloat($("input.deta_importe", this).val());
						// v = (importe * flete / total) / cantidad;
						// $("input.deta_flete", this).val(v.toFixed(_fixed_compra));
					// }
					// else {
						// $("input.deta_flete", this).val("");
					// }
				// }
			// });
			// return;
		// }
	}
	$("input.deta_flete").val("");
}

function calcularDescuento() {
	if($.isNumeric($("#descuento").val())) {
		var descuento = parseFloat($("#descuento").val()), cantidad = 0;
		
		$("#tbl-detalle tbody tr").each(function() {
			if($.isNumeric($("input.deta_cantidad", this).val())) {
				cantidad += parseFloat($("input.deta_cantidad", this).val());
			}
		});
		
		if(cantidad > 0) {
			var descitem = descuento / cantidad;
			$("input.deta_descuento").val(descitem.toFixed(_fixed_compra));
		}
		return;
	}
	$("input.deta_descuento").val("");
}

function calcularGasto() {
	return;
	if($.isNumeric($("#gastos").val())) {
		var gastos = parseFloat($("#gastos").val()), v, pu;
		$("#tbl-detalle tbody tr").each(function() {
			if($.isNumeric($("input.deta_precio", this).val())) {
				pu = parseFloat($("input.deta_precio", this).val());
				v = pu * gastos / 100;
				$("input.deta_gastos", this).val(v.toFixed(_fixed_compra));
			}
			else {
				$("input.deta_gastos", this).val("");
			}
		});
		return;
	}
	$("input.deta_gastos").val("");
}

function calcularPrecioCosto() {
	if( $("#tbl-detalle tbody tr").length ) {
		var t = 0;
		$("#tbl-detalle tbody tr").each(function() {
			if($.isNumeric($("input.deta_precio", this).val())) {
				t = parseFloat($("input.deta_precio", this).val());
				if($.isNumeric($("input.deta_igv", this).val()))
					t += parseFloat($("input.deta_igv", this).val());
				if($.isNumeric($("input.deta_flete", this).val()))
					t += parseFloat($("input.deta_flete", this).val());
				if($.isNumeric($("input.deta_gastos", this).val()))
					t += parseFloat($("input.deta_gastos", this).val());
				if($.isNumeric($("input.deta_descuento", this).val()))
					t -= parseFloat($("input.deta_descuento", this).val());
				$("input.deta_costo", this).val(t.toFixed(_fixed_compra));
			}
			else {
				$("input.deta_costo", this).val("");
			}
		});
	}
}

// $("#btn_cancel").click(function() {
	// redirect(_controller);
	// return false;
// });

$("#idtipodocumento").focus();

$("#form_compra").submit(function() {
	return false;
});

input.autocomplete({
	selector: "#proveedor_descripcion"
	,controller: "proveedor"
	,label: "[nombre]"
	,value: "[nombre]"
	,highlight: true
	,onSelect: function(item) {
		$("#proveedor_idproveedor").val(item.idproveedor);
	}
});

input.autocomplete({
	selector: "#producto_descripcion"
	,controller: "producto"
	,method: "autocomplete"
	,label: "<strong>[codigo_producto]</strong>| [descripcion_detallada]"
	,value: "[descripcion_detallada]"
	,highlight: true
	,onSelect: function(item) {
		$("#producto_idproducto").val(item.idproducto);
		$("#btn-agregar-producto").trigger("click");
	}
});

$("#producto_descripcion").keypress(function(e) {
	var t = e.keyCode ? e.keyCode : e.which;
	if(t == 13) {
		e.preventDefault();
		$("#btn-agregar-producto").trigger("click");
		return false;
	}
});

$("#btn-agregar-producto").click(function() {
	if(!$("#producto_idproducto").required()) {
		ventana.alert({titulo: "Aviso", mensaje: "Seleccione un producto de la lista"}, function() {
			$("#producto_descripcion").focus().select();
		});
		return false;
	}
	
	if( $("#producto_descripcion").required() ) {
		agregarProducto($("#producto_idproducto").val(), function() {
			$("#producto_idproducto,#producto_descripcion").val("");
			$("#producto_descripcion").focus();
		});
	}
	return false;
});

$("#btn-registrar-producto").on("click", function() {
	$("#modal-producto").modal("show");
	return false;
});

$("#f2").click(function(){
	$("#producto_descripcion").focus();
});

$("#cambio_moneda").numero_real();
$("#cambio_moneda_flete").numero_real();

$("#idmoneda").on("change", function() {
	if($.isNumeric($(this).val())) {
		ajax.post({url: _base_url+"moneda/get/"+$(this).val()}, function(data) {
			$("#cambio_moneda").val(parseFloat(data.valor_cambio).toFixed(2));
			calcularFlete();
			calcularGasto();
			calcularPrecioCosto();
		});
		return;
	}
	$("#cambio_moneda").val("");
});

$('#fecha_compra').datepicker({
	todayBtn: "linked",
	keyboardNavigation: false,
	forceParse: false,
	autoclose: true,
	language: 'es',
	endDate: parseDate(_current_date)
});

/* PARA EL MODAL DE PROVEEDOR*/
$("#btn-buscar-proveedor").click(function(e) {
	e.preventDefault();
	
	jFrame.create({
		title: "Buscar Proveedor"
		,msg: ""
		,controller: "proveedor"
		,method: "grilla_popup"
		// ,autoclose: false
		,onSelect: function(datos) {
			// console.log(datos);
			$("#proveedor_descripcion").val(datos.nombre);
			$("#proveedor_idproveedor").val(datos.idproveedor);
			// jFrame.close();
		}
	});
	
	jFrame.show();
	return false;
});

$("#btn-registrar-proveedor").on("click", function() {
	$("#modal-proveedor").modal("show");
	return false;
});
/* PARA EL MODAL DE PROVEEDOR*/

// $('.i-checks').iCheck({
	// checkboxClass: 'icheckbox_square-green',
	// radioClass: 'iradio_square-green',
// });

$(document).on("change", "#tbl-detalle tbody tr select.deta_idunidad", function() {
	var opt, openmodal = false;
	if($(this).val() == "N") {
		opt = $("option:first", this);
		$(this).val(opt.attr("value"));
		openmodal = true;
	}
	else {
		opt = $("option:selected", this);
	}
	$(this).attr("title", opt.attr("title"));
	$(this).attr("data-original-title", opt.attr("title"));
	
	if(openmodal) {
		show_dialog_unidad_medida($(this).closest("tr"));
	}
	/* else {
		get_precio_compra($(this).closest("tr"));
	} */
});

$("#tbl-detalle").on("click", "button.btn_deta_delete", function() {
	$(this).tooltip('destroy');
	$(this).closest("tr").remove();
	calcularIgv();
	calcularPrecioIgv();
	calcularImporteIgv();
	calcularSubtotalCompra();
	calcularIgvCompra();
	calcularDescuento();
	calcularTotalCompra();
	calcularFlete();
	calcularGasto();
	calcularPrecioCosto();
	ordenarItem();
});

$(document).on("keyup", "#tbl-detalle tbody tr input.deta_cantidad", function() {
	calcularDatos($(this).closest("tr"));
});

$(document).on("keyup", "#tbl-detalle tbody tr input.deta_precio", function() {
	calcularDatos($(this).closest("tr"));
});

$(document).on("blur", "#tbl-detalle tbody tr input.deta_precio", function() {
	if($.isNumeric($(this).val())) {
		var v = parseFloat($(this).val());
		$(this).val(v.toFixed(_fixed_compra));
	}
});

$(document).on("keyup", "#tbl-detalle tbody tr input.deta_precio_igv", function() {
	var tr = $(this).closest("tr");
	if($.isNumeric($("input.deta_cantidad", tr).val()) && $.isNumeric($("input.deta_precio_igv", tr).val())) {
		var igv = 1;
		if($("#valor_igv").is(":checked")) {
			igv += parseFloat($("#valor_igv").val()) / 100;
		}
		
		var precio = parseFloat($("input.deta_precio_igv", tr).val()) / igv;
		$("input.deta_precio", tr).val(precio.toFixed(_fixed_compra));
	}
	else {
		$("input.deta_precio", tr).val("");
	}
	
	calcularDatos(tr, false);
});

$(document).on("blur", "#tbl-detalle tbody tr input.deta_precio_igv", function() {
	if($.isNumeric($(this).val())) {
		var v = parseFloat($(this).val());
		$(this).val(v.toFixed(_fixed_compra));
	}
});

$(document).on("keyup", "#tbl-detalle tbody tr input.deta_importe", function() {
	var tr = $(this).closest("tr");
	if($.isNumeric($("input.deta_importe", tr).val())) {
		var cantidad = 1;
		if($.isNumeric($("input.deta_cantidad", tr).val())) {
			cantidad = parseFloat($("input.deta_cantidad", tr).val());
		}
		else {
			$("input.deta_cantidad", tr).val(1);
		}
		
		var precio = parseFloat($("input.deta_importe", tr).val());
		if(cantidad > 0)
			precio /= cantidad;
		
		$("input.deta_precio", tr).val(precio.toFixed(_fixed_compra));
	}
	else {
		$("input.deta_precio", tr).val("");
	}
	
	calcularDatos(tr, true, false);
});

$(document).on("blur", "#tbl-detalle tbody tr input.deta_importe", function() {
	if($.isNumeric($(this).val())) {
		var v = parseFloat($(this).val());
		$(this).val(v.toFixed(_fixed_compra));
	}
});

$(document).on("keyup", "#tbl-detalle tbody tr input.deta_importe_igv", function() {
	var tr = $(this).closest("tr");
	if($.isNumeric($("input.deta_importe_igv", tr).val())) {
		var cantidad = 1, igv = 1;
		if($.isNumeric($("input.deta_cantidad", tr).val())) {
			cantidad = parseFloat($("input.deta_cantidad", tr).val());
		}
		else {
			$("input.deta_cantidad", tr).val(1);
		}
		
		if($("#valor_igv").is(":checked")) {
			igv += parseFloat($("#valor_igv").val()) / 100;
		}
		
		var precio = parseFloat($("input.deta_importe_igv", tr).val()) / igv;
		if(cantidad > 0)
			precio /= cantidad;
		
		$("input.deta_precio", tr).val(precio.toFixed(_fixed_compra));
	}
	else {
		$("input.deta_precio", tr).val("");
	}
	
	calcularDatos(tr, true, true, false);
});

$(document).on("blur", "#tbl-detalle tbody tr input.deta_importe_igv", function() {
	if($.isNumeric($(this).val())) {
		var v = parseFloat($(this).val());
		$(this).val(v.toFixed(_fixed_compra));
	}
});

$("#valor_igv").on("change", function() {
	calcularIgv();
	calcularPrecioIgv();
	calcularImporteIgv();
	calcularIgvCompra();
	calcularDescuento();
	calcularTotalCompra();
	calcularFlete();
	calcularGasto();
	calcularPrecioCosto();
});

$("#descuento").on("keyup", function() {
	calcularDescuento();
	calcularTotalCompra();
	calcularFlete();
	calcularGasto();
	calcularPrecioCosto();
});

$("#flete").on("keyup", function() {
	calcularFlete();
	calcularGasto();
	calcularPrecioCosto();
});

$("#idmoneda_flete").on("change", function() {
	calcularFlete();
	calcularGasto();
	calcularPrecioCosto();
});

$("#gastos").on("keyup", function() {
	calcularGasto();
	calcularPrecioCosto();
});

$("#buscar-pedido").click(function() {
	jFrame.create({
		title: "Buscar pedidos Aprobados"
		,controller: "pedido"
		,method: "grilla_popup"
		// ,autoclose: false
		,onSelect: function(datos) {
			// console.log(datos);
			ajax.post({url: _base_url+"pedido/get_detalle_pedido", data:"idpedido="+datos.idpedido}, function(data) {
				if(data.length) {
					for(var i in data) {
						// if($("#tbl-detalle tbody tr[index="+data[i].idproducto+"]").length) {
							// appendDetalle(data[i]);
						// }
						// else {
							tr = addDetalle(data[i]);
							updateUnidades(data[i], tr);
						// }
					}
					calcularDatos();
				}
			});
			// jFrame.close();
		}
	});
	
	jFrame.show();
	return false;
});

function cargarDetalle(oTable, nRow, aData, iDisplayIndex) {
	ajax.post({url: _base_url+"pedido/get_detalle_pedido", data:"idpedido="+aData.idpedido}, function(data) {
		if(data.length) {
			var setting = oTable.fnSettings();
			var htmltable = '<table ref="'+setting.sTableId+'" class="table table-bordered no-margins small grilla_subgrid">';
			// htmltable += '<thead><tr><th>Producto</th><th>Cantidad</th><th>Un.Med.</th><th></th></tr></thead>';
			// htmltable += '<thead><tr><th colspan="4"><input type="text" class="pull-right" placeholder="Buscar productos"></th></tr></thead>';
			htmltable += '<tbody>';
			for(var i in data) {
				htmltable += '<tr>';
				htmltable += '<td class="">'+data[i].descripcion_detallada+'</td>';
				htmltable += '<td class="">'+data[i].cantidad+'</td>';
				htmltable += '<td class="">'+data[i].abreviatura+'</td>';
				htmltable += '<td class="text-navy"><button class="btn btn-white btn-xs btn_agregar_pedido_producto" data-idpedido="'+
					aData.idpedido+'" data-idproducto="'+data[i].idproducto+'" data-idunidad="'+data[i].idunidad+'"'+
					' data-descripcion="'+data[i].descripcion+'" data-cantidad="'+data[i].cantidad+'">Agregar</button></td>';
				htmltable += '</tr>';
			}
			htmltable += '</tbody></table>';
			oTable.fnOpen(nRow, htmltable, "details");
		}
	});
}

$(document).on("click", "button.btn_agregar_pedido_producto", function() {
	// console.log($(this).data());
	var datos = $(this).data();
	ajax.post({url: _base_url+"pedido/get_detalle_pedido", data:"idpedido="+datos.idpedido+"&idproducto="+datos.idproducto}, function(data) {
		if(data.length) {
			for(var i in data) {
				// if($("#tbl-detalle tbody tr[index="+data[i].idproducto+"]").length) {
					// appendDetalle(data[i]);
				// }
				// else {
					tr = addDetalle(data[i]);
					updateUnidades(data[i], tr);
				// }
			}
			calcularDatos();
		}
	});
	return false;
});

$("#btn_save_compra").click(function(e) {
	e.preventDefault();
	var v = true;
	v = v && $("#proveedor_descripcion").required();
	v = v && $("#idtipodocumento").required();
	v = v && $("#serie").required();
	v = v && $("#numero").required();
	v = v && $("#idtipoventa").required();
	v = v && $("#fecha_compra").required();
	v = v && $("#idalmacen").required();
	v = v && $("#idmoneda").required();
	v = v && $("#subtotal").required({numero:true, tipo:"float"});
	v = v && $("#total").required({numero:true, tipo:"float"});
	if(v) {
		if(!$("#proveedor_idproveedor").required()) {
			ventana.alert({titulo: "Error", mensaje: "Seleccione un proveedor de la lista o registre el proveedor si no existe"});
			return;
		}
		if($("#tbl-detalle tbody tr").length < 1) {
			ventana.alert({titulo: "Error", mensaje: "Agregue los productos de la compra a la tabla"});
			return;
		}
		v = v && $("input.deta_cantidad").required({numero:true, tipo:"float"});
		v = v && $("input.deta_idunidad").required({numero:true, tipo:"int"});
		v = v && $("input.deta_precio").required({numero:true, tipo:"float", aceptaCero:true});
		if(v) {
			// se recepciona directamente
			if($("#recepcionado").is(":checked")) {
				// validamos las series
				var arraySeries = null, cantidad = 0, prod = '';
				
				if($( "#tbl-detalle tbody tr.has_serie" ).length) {
					$( "#tbl-detalle tbody tr.has_serie" ).each(function() {
						if( $(".deta_series", this).val() != '' ) {
							arraySeries = String($(".deta_series", this).val()).split('|');
							cantidad = parseFloat($(".deta_cantidad", this).val()) * parseFloat($(".deta_idunidad option:selected", this).attr("count"));
							if(arraySeries.length != cantidad) {
								v = false;
								prod = $(".deta_producto", this).val();
							}
						}
						else {
							v = false;
							prod = $(".deta_producto", this).val();
						}
						return v;
					});
				}
				
				if(v == false && prod != '') {
					ventana.alert({mensaje: "Ingrese las series del producto "+prod});
					return;
				}
			}
			
			if(v) {
				if($("#idtipoventa").val() == "1") {
					if($.trim($("#idcompra").val()) == "") {
						pay.setMonto($("#total").val());
						$(".monto_entregado").val($("#total").val());
						$(".monto_entregado").trigger("keyup");
						setTimeout(function(){
							$(".monto_entregado").focus();
						},800);
						pay.ok(function(datos) {
							form.guardar(datos);
						});
						pay.show();
						return;
					}
				}
				
				form.guardar();
			}
		}
	}
	
	return false;
});

function llenarDetalleCompra() {
	if($.isArray(data_detalle)) {
		for(var i in data_detalle) {
			tr = addDetalle(data_detalle[i]);
			updateUnidades(data_detalle[i], tr);
		}
		$("#tbl-detalle tbody tr input.deta_precio").trigger("keyup");
	}
}

if(_es_nuevo_compra_) {
	$("#idmoneda").trigger("change");
}
else {
	llenarDetalleCompra();
}

/* function get_precio_compra(tr) {
	ajax.post({url: _base_url+"producto/get_real_precio_compra/"+tr.attr("index")}, function(data) {
		$(".deta_precio", tr).val(data);
	});
} */

var arrListaSeries = [];

$(document).on("click", "button.btn_deta_serie", function(e) {
	e.preventDefault();
	var tr = $(this).closest("tr");
	
	if($.trim($(".deta_series", tr).val()) != "") {
		// obtenemos las series ingresadas
		var arrSeries = String($(".deta_series", tr).val()).split("|");
		add_series(arrSeries);
	}
	
	// obtenemos la lista completa de todas las series
	arrListaSeries = [];
	$("#tbl-detalle tbody tr[index="+tr.attr("index")+"]").each(function() {
		if($.trim($(".deta_series", this).val()) != "") {
			// obtenemos las series ingresadas
			temp = String($(".deta_series", this).val()).split("|");
			arrListaSeries = arrListaSeries.concat(temp);
		}
	});
	
	tr.addClass("current");
	$("#modal-series .modal-title").text($("td:eq(1)", tr).text());
	$("#modal-series").modal("show");
	$("#input-text-serie").val("").focus();
	return false;
});

$("#input-text-serie").keypress(function(e) {
	var t = e.keyCode ? e.keyCode : e.which;
	if(t == 13) {
		e.preventDefault();
		$("#btn-add-serie").trigger("click");
		return false;
	}
});

$("#btn-add-serie").click(function(e) {
	e.preventDefault();
	if($.trim($("#input-text-serie").val()) != "") {
		var temp = String($("#input-text-serie").val()).replace(/\W/g, '').toUpperCase();
		if(arrListaSeries.indexOf(temp) != -1) {
			ventana.alert({titulo: '', mensaje: 'La serie <b>'+temp+'</b> ya se ha agregado'}, function() {
				$("#input-text-serie").focus().select();
			});
			return false;
		}
		add_series(temp);
		arrListaSeries.push(temp);
		$("#input-text-serie").val("").focus();
	}
	return false;
});

$("#btn-close-serie").click(function(e) {
	e.preventDefault();
	var tr = $("#tbl-detalle tbody tr.current");
	var txt = "", cant = 0;
	
	if($("#table-serie tbody tr").length) {
		var arr = [];
		$("#table-serie tbody tr").each(function() {
			arr.push($(this).attr("index"));
		});
		cant = arr.length;
		txt = arr.join("|");
	}
	
	$(".deta_series", tr).val(txt);
	if(cant > 0) {
		cant = cant / parseFloat($(".deta_idunidad option:selected", tr).attr("count"));
		$(".deta_cantidad", tr).val(Math.round(cant));
		calcularDatos(tr);
	}
	
	$("#input-text-serie").val("");
	$("#table-serie tbody tr").remove();
	tr.removeClass("current");
	
	$("#modal-series").modal("hide");
	return false;
});

function add_series(arr) {
	if($.isArray(arr) == false) {
		var temp = [];
		temp.push(arr);
		arr = temp;
	}
	if(arr.length) {
		var c = $("#table-serie tbody tr").length, html = '';
		for(var i in arr) {
			c++;
			html += '<tr index="'+arr[i]+'">';
			html += '<td>'+c+'</td>';
			html += '<td>'+arr[i]+'</td>';
			html += '<td><button class="btn btn-xs btn-danger btn_remove_serie" title="Eliminar fila"><i class="fa fa-trash"></i></button></td>';
			html += '</tr>';
		}
		$("#table-serie tbody").append(html);
		$('div.div_scroll').scrollTop($('div.div_scroll')[0].scrollHeight);
	}
}

$(document).on("click", "button.btn_remove_serie", function(e) {
	e.preventDefault();
	var serie = $(this).closest("tr").attr("index");
	var index = arrListaSeries.indexOf(serie);
	
	// eliminamos la serie
	arrListaSeries.splice(index, 1);
	$(this).closest("tr").remove();
	
	// reordenamos las series
	if($("#table-serie tbody tr").length) {
		var c = 0;
		$("#table-serie tbody tr").each(function() {
			$("td:eq(0)", this).text(++c);
		});
	}
	
	return false;
});

function ordenarItem() {
	if($("#tbl-detalle tbody tr").length) {
		var i = 0;
		$("#tbl-detalle tbody tr").each(function() {
			$("td.item", this).html('<span class="badge">'+(++i)+'</span>');
		});
	}
}

$("#idmoneda_flete").on("change", function() {
	if($.isNumeric($(this).val()) && ! $.isNumeric($("#cambio_moneda_flete").val())) {
		ajax.post({url: _base_url+"moneda/get/"+$(this).val()}, function(data) {
			$("#cambio_moneda_flete").val(parseFloat(data.valor_cambio).toFixed(2));
			calcularFlete();
			calcularGasto();
			calcularPrecioCosto();
		});
		return;
	}
	$("#cambio_moneda_flete").focus();
	calcularFlete();
	calcularGasto();
	calcularPrecioCosto();
});

function getMontoConvertido(monedaFactura, monedaFlete, tipoCambio, flete) {
	if(monedaFactura == monedaFlete)
		return Number(flete);
	
	if(monedaFactura == "PEN" && monedaFlete != "PEN")
		return Number(flete) * Number(tipoCambio);
	
	if(monedaFactura != "PEN" && monedaFlete == "PEN") {
		tipoCambio = Number(tipoCambio);
		if(tipoCambio <= 0)
			tipoCambio = 1;
		return Number(flete) / tipoCambio;
	}
	
	return 0;
}