function reloadTable() {
	var filters = ["idcategoria", "idmarca", "idmodelo", "idalmacen"];
	
	$.each(filters, function(i, sel) {
		if( $("#"+sel).val() == 'T' )
			grilla.del_filter(_default_grilla, sel);
		else
			grilla.set_filter(_default_grilla, sel, "=", $("#"+sel).val());
	});
	
	grilla.del_filter(_default_grilla, "stock");
	
	if($(".filtro_stock:checked").val() == "S") {
		grilla.set_filter(_default_grilla, "stock", ">", 0);
	}
	else if($(".filtro_stock:checked").val() == "N") {
		grilla.set_filter(_default_grilla, "stock", "<=", 0);
	}
	
	grilla.reload(_default_grilla);
}

$("#idcategoria").chosen();
$("#idmarca").chosen();
$("#idmodelo").chosen();
$("#idalmacen").chosen();

// $(".tipo_operacion").chosen();

$("#btnQuery").click(function(e) {
	e.preventDefault();
	var q = String($("#txtQuery").val()).replace(/\s+/g, '%');
	grilla.search( _default_grilla, q );
	return false;
});

$("#txtQuery").keyup(function(e) {
	if(e.keyCode == 13) {
		e.preventDefault();
		$("#btnQuery").trigger("click");
		return false;
	}
});

$("select.combo-filtro").change(function() {
	reloadTable();
});

$("input.filtro_stock").change(function() {
	reloadTable();
});

/******************************************************* eventos movimiento stock ***************************************/
$("#btn-movimiento-stock").click(function(e) {
	e.preventDefault();
	var id = grilla.get_id(_default_grilla);
	if(id != null) {
		agregarProducto(id);
	}
	$("#modal-movimiento-stock").modal("show");
	return false;
});

input.autocomplete({
	selector: "#producto_descripcion"
	,controller: "producto"
	,method: "autocomplete"
	,label: "<strong>[codigo_producto]</strong>| [descripcion_detallada]"
	,value: "[descripcion_detallada]"
	,highlight: true
	,appendTo: $("#producto_descripcion").closest("div")
	,data: function() {
		return {
			idalmacen: $("#idalmacen").val()
		};
	}
	,onSelect: function(item) {
		$("#producto_idproducto").val(item.idproducto);
		verificarProducto();
	}
});

$("#producto_descripcion").keypress(function(e) {
	var t = e.keyCode ? e.keyCode : e.which;
	if(t == 13) {
		verificarProducto();
		return false;
	}
});

function verificarProducto() {
	if( ! $("#producto_idproducto").required()) {
		return false;
	}
	
	if( $("#producto_descripcion").required() ) {
		agregarProducto($("#producto_idproducto").val(), function() {
			$("#producto_descripcion").val("").focus();
		});
	}
}

function agregarProducto(idproducto, callback) {
	ajax.post({url: _base_url+"producto/get/"+idproducto}, function(data) {
		
		if($("#tbl-detalle tbody tr[index="+data.idproducto+"]").length) {
			// $("#tbl-detalle tbody tr[index="+data.idproducto+"] .deta_cantidad").focus();
			// return;
		}
		else {
			var tr = addDetalle(data);
			updateUnidades(tr, data, function(tr, data) {
				calcularStock(data.idproducto);
			});
		}
		
		if($.isFunction(callback)) {
			callback();
		}
	});
}

function calcularStock(idproducto) {
	ajax.post({url: _base_url+"producto/get_stock/"+idproducto+"/"+$("#idalmacen").val()}, function(stock) {
		$("#tbl-detalle tbody tr[index="+idproducto+"]").each(function() {
			cantidad = parseFloat($(".deta_idunidad option:selected", this).attr("count"));
			stock_real = stock;
			if(cantidad > 0)
				stock_real = stock_real / cantidad;
			if(isNaN(stock_real))
				stock_real = 0;
			$(".deta_stock", this).val(stock_real.toFixed(2));
		});
	});
}

function getUnidades(idproducto, callback) {
	ajax.post({url: _base_url+"producto/get_unidades/"+idproducto}, function(res) {
		var data, options='';
		
		if($.isArray(res)) {
			for(var i in res) {
				data = res[i];
				options += '<option value="'+data.idunidad+'" title="'+data.descripcion+'" count="'+data.cantidad_unidad_min+'">'+data.abreviatura+'</option>';
			}
		}
		
		if($.isFunction(callback)) {
			callback(options);
		}
	});
}

function updateUnidades(tr, params, callback) {
	if(params && typeof params.idproducto == "undefined")
		return;
	
	getUnidades(params.idproducto, function(options) {
		if(tr) {
			v = (params.idunidad) ? parseInt(params.idunidad) : parseInt($(".deta_idunidad", tr).val());
			$(".deta_idunidad", tr).html(options);
			if(!isNaN(v)) {
				$(".deta_idunidad", tr).val(v);
			}
		}
		
		if($.isFunction(callback)) {
			callback(tr, params);
		}
	});
}

$("#tbl-detalle").on("change", "select.deta_idunidad", function() {
	var self = $(this);
	var tr = $(this).closest("tr");
	tr.data("idunidad", self.val());
	
	var opt = $("option:selected", self);
	self.attr("title", opt.attr("title"));
	self.attr("data-original-title", opt.attr("title"));
	calcularStock($("input.deta_idproducto", tr).val());
});

$("#tbl-detalle").on("click", ".btn_deta_delete", function(e) {
	e.preventDefault();
	$(this).closest("tr").remove();
	return false;
});

$("#tbl-detalle").on("keypress", ".deta_cantidad", function(e) {
	if(e.keyCode == 13) {
		e.preventDefault();
		var i = $(this).closest("tr").index() + 1;
		if($("#tbl-detalle tbody tr:eq("+i+")").length <= 0)
			i = 0;
		$("#tbl-detalle tbody tr:eq("+i+") .deta_cantidad").focus();
	}
});

$("#modal-movimiento-stock").on('hidden.bs.modal', function () {
	$(".detail-table tbody tr").remove();
});

function addDetalle(data) {
	cls = (data.controla_serie == 'S') ? "has_serie" : "";
	
	var table = new Table();
	table.tr({index: data.idproducto, class: cls, data:{idunidad: data.idunidad}});
	
	table.td('<input type="text" name="deta_producto[]" class="form-control input-xs" value="'+data.descripcion_detallada+'" readonly>');
	table.td('<select name="deta_idunidad[]" class="form-control input-xs deta_idunidad" data-toggle="tooltip" title=""></select>');
	table.td('<input type="text" name="deta_stock[]" class="form-control input-xs text-success deta_stock" readonly>');
	table.td('<input type="text" name="deta_cantidad[]" class="form-control input-xs deta_cantidad">');
	table.td('<input type="text" name="deta_costo[]" class="form-control input-xs deta_costo">');
	
	if(cls == 'has_serie') {
		table.td('<input type="hidden" name="deta_series[]" class="deta_series" value="'+$.trim(data.serie)+'">'+
			'<button class="btn btn-success btn-xs btn_deta_serie" data-toggle="tooltip" title="Ingresar las series del producto"><i class="fa fa-cubes"></i></button>');
	}
	else {
		table.td('<input type="hidden" name="deta_series[]" class="deta_series" value="'+$.trim(data.serie)+'">');
	}
	table.td('<button class="btn btn-danger btn-xs btn_deta_delete" data-toggle="tooltip" title="Eliminar registro"><i class="fa fa-trash"></i></button>');
	
	table.td('<input type="hidden" name="deta_idproducto[]" class="deta_idproducto" value="'+data.idproducto+'">'+
		'<input type="hidden" name="deta_controla_stock[]" class="deta_controla_stock" value="'+data.controla_stock+'">'+
		'<input type="hidden" name="deta_controla_serie[]" class="deta_controla_serie" value="'+data.controla_serie+'">', {style:"display:none"});
	
	$("#tbl-detalle tbody").append(table.to_string());
	$("#tbl-detalle tbody tr:last input.deta_cantidad").numero_real();
	$("#tbl-detalle tbody tr:last input.deta_costo").numero_real();
	
	return $("#tbl-detalle tbody tr:last");
}

$("#btn-save-movimiento-stock").click(function(e) {
	e.preventDefault();
	var form = "#modal-movimiento-stock";
	var v = true;
	v = v && $(".tipo_movimiento", form).required();
	v = v && $(".tipo_operacion", form).required();
	if(v) {
		var table = $("#tbl-detalle", form);
		
		if($("tbody tr", table).length < 1) {
			ventana.alert({titulo: "Error", mensaje: "Agregue los item a la tabla"});
			return;
		}
		v = v && $(".deta_idunidad", table).required({numero:true, tipo:"int"});
		v = v && $(".deta_cantidad", table).required({numero:true, tipo:"float"});
		
		if(v) {
			var str = "idalmacen="+$("#idalmacen").val()+"&"+$(form).serialize();
			ajax.post({url: _base_url+"consultarproducto/add_stock", data:str}, function() {
				grilla.reload(_default_grilla);
				$("#modal-movimiento-stock").modal("hide");
			});
		}
	}
	return false;
});

/************************************************ evento de traslado almacen ****************************************/
$("#btn-traslado").click(function(e) {
	e.preventDefault();
	$("#modal-traslado").modal("show");
	return false;
});

input.autocomplete({
	selector: ".temp_producto"
	,controller: "producto"
	,method: "autocomplete"
	,label: "<strong>[codigo_producto]</strong>| [descripcion_detallada]"
	,value: "[descripcion_detallada]"
	,highlight: true
	,appendTo: $(".temp_producto").closest("div")
	,onSelect: function(item) {
		$("#producto_idproducto").data("datos", item);
		$("#producto_idproducto").val(item.idproducto);
		
		setTimeout(function() {
			var e = jQuery.Event("keypress");
			e.which = 13; // enter event
			$(".temp_producto").trigger(e);
		}, 50);
	}
});

$(".temp_producto").keypress(function(e) {
	var t = e.keyCode ? e.keyCode : e.which;
	if(t == 9) {
		e.preventDefault();
		return;
	}
	if(t == 13) {
		e.preventDefault();
		getUnidades($("#producto_idproducto").val(), function(options) {
			var datos = $("#producto_idproducto").data("datos");
			$(".temp_unidad").html(options).val(datos.idunidad).trigger("change").focus();
		});
	}
});

$(".temp_unidad").change(function() {
	getStock($("#producto_idproducto").val(), $(".panel-salida .idalmacen").val(), $(this), ".temp_stock");
});

$(".temp_cantidad").numero_real();

$(".temp_unidad").keypress(function(e) {
	var t = e.keyCode ? e.keyCode : e.which;
	if(t == 9) {
		e.preventDefault();
		return;
	}
	if(t == 13) {
		e.preventDefault();
		$(".temp_cantidad").focus();
	}
});

$(".temp_cantidad").keypress(function(e) {
	var t = e.keyCode ? e.keyCode : e.which;
	if(t == 9) {
		e.preventDefault();
		return;
	}
	if(t == 13) {
		e.preventDefault();
		if( $.isNumeric($(this).val()) != '' && $.trim($("#producto_idproducto").val()) != '' && 
		$.trim($(".temp_producto").val()) != '' && $.trim($(".temp_unidad").val()) != '' ) {
			addTransferencia({
				idproducto: $("#producto_idproducto").val()
				,producto: $(".temp_producto").val()
				,idunidad: $(".temp_unidad").val()
				,unidad: $(".temp_unidad>option:selected").text()
				,stock: $(".temp_stock").val()
				,cantidad: $(".temp_cantidad").val()
			}, function() {
				$("#producto_idproducto,.temp_producto,.temp_stock,.temp_cantidad").val('');
				$(".temp_unidad>option").remove();
				$(".temp_producto").focus();
			});
		}
		else {
			$(".temp_producto").focus();
		}
	}
});

function getStock(idproducto, idalmacen, selUnidad, selStock) {
	ajax.post({url: _base_url+"producto/get_stock/"+idproducto+"/"+idalmacen}, function(stock) {
		cantidad = parseFloat($("option:selected", selUnidad).attr("count"));
		stock_real = stock;
		if(cantidad > 0)
			stock_real = stock_real / cantidad;
		if(isNaN(stock_real))
			stock_real = 0;
		$(selStock).val(stock_real.toFixed(2));
	});
}

function addTransferencia(data, callback) {
	var table = new Table();
	table.tr();
	
	table.td(data.producto);
	table.td(data.unidad, {class:'text-center'});
	// table.td(data.stock, {class:'text-center'});
	table.td(data.cantidad, {class:'text-center'});
	table.td('<button class="btn btn-danger btn-xs btn_delete_transferencia"><i class="fa fa-trash"></i></button>');
	$("#tbl-detalle-entrada tbody").append(table.to_string(false));
	
	table.td('<input type="hidden" name="deta_idproducto[]" class="deta_idproducto" value="'+data.idproducto+'">'+
		'<input type="hidden" name="deta_producto[]" value="'+data.producto+'">'+
		'<input type="hidden" name="deta_idunidad[]" class="deta_idunidad" value="'+data.idunidad+'">'+
		'<input type="hidden" name="deta_cantidad[]" class="deta_cantidad" value="'+data.cantidad+'">', {style:"display:none"});
	$("#tbl-detalle-salida tbody").append(table.to_string());
	
	if($.isFunction(callback))
		callback();
}

$("#modal-traslado").on('hidden.bs.modal', function () {
	$(".detail-table tbody tr").remove();
});

$(".detail-table").on("click", ".btn_delete_transferencia", function(e) {
	e.preventDefault();
	var i = $(this).closest("tr").index();
	$("#tbl-detalle-salida tbody tr:eq("+i+")").remove();
	$("#tbl-detalle-entrada tbody tr:eq("+i+")").remove();
});

$("#btn-save-traslado").click(function(e) {
	e.preventDefault();
	var form = "#modal-traslado";
	var v = true;
	v = v && $(".panel-salida .idalmacen", form).required();
	v = v && $(".panel-entrada .idalmacen", form).required();
	if(v) {
		if( $(".panel-salida .idalmacen", form).val() == $(".panel-entrada .idalmacen", form).val() ) {
			ventana.alert({titulo: "Error", mensaje: "El almacen de Entrada debe ser distinto al almacen de Salida"});
			return;
		}
		
		var table = $("#tbl-detalle-salida", form);
		
		if($("tbody tr", table).length < 1) {
			ventana.alert({titulo: "Error", mensaje: "Agregue los item a la tabla"});
			return;
		}
		v = v && $(".deta_idunidad", table).required({numero:true, tipo:"int"});
		v = v && $(".deta_cantidad", table).required({numero:true, tipo:"float"});
		
		if(v) {
			var str = $(form).serialize();
			str += "&idalmacen_salida=" + $(".panel-salida .idalmacen", form).val();
			str += "&idalmacen_entrada=" + $(".panel-entrada .idalmacen", form).val();
			
			ajax.post({url: _base_url+"consultarproducto/traslado", data:str}, function() {
				grilla.reload(_default_grilla);
				$("#modal-traslado").modal("hide");
			});
		}
	}
	return false;
});

/******************************************** eventos para la conversion *************************************/
$("#btn-conversion").click(function(e) {
	e.preventDefault();
	$("#modal-conversion").modal("show");
});

$(".conversion_cantidad").numero_real();
$(".conversion_equivalencia").numero_real();

input.autocomplete({
	selector: ".conversion_producto"
	,controller: "producto"
	,method: "autocomplete"
	,label: "<strong>[codigo_producto]</strong>| [descripcion_detallada]"
	,value: "[descripcion_detallada]"
	,highlight: true
	,appendTo: $(".conversion_producto").closest("div")
	,onSelect: function(item) {
		$(".resultado_tipo").val("A");
		$(".conversion_idproducto").val(item.idproducto);
		
		reload_combo(".producto_idunidad", 
		{
			controller: "consultarproducto",
			method: "get_unidades", 
			data: "idproducto="+item.idproducto
		}, function() {
			$(".producto_idunidad").val(item.idunidad).trigger("change");
		});
		
		$(".conversion_idunidad").trigger("change"); //buscamos equivalencia
		$(".conversion_idunidad").focus();
	}
});

$(".conversion_idunidad").change(function() {
	cargarDatosEq($(".conversion_idproducto").val(), $(this).val(), $(".producto_idunidad").val());
});

$(".producto_idunidad").change(function() {
	$(".producto_abreviatura").text("("+$("option:selected", this).data("abreviatura")+")");
	cargarDatosEq($(".conversion_idproducto").val(), $(".conversion_idunidad").val(), $(this).val());
});

function cargarDatosEq(idproducto, idunidad, idunidadprod) {
	if($.isNumeric(idproducto) && $.isNumeric(idunidad) && $.isNumeric(idunidadprod)) {
		var str = "idproducto="+idproducto+"&idunidad="+idunidad+"&idunidadprod="+idunidadprod;
		ajax.post({url: _base_url+"producto/get_equivalencia", data:str}, function(res) {
			if(res.unidad) {
				$(".conversion_equivalencia").val(res.unidad.cantidad_unidad_min);
			}
			if(res.producto) {
				$(".resultado_idproducto").val(res.producto.idproducto);
				$(".resultado_idunidad").val(res.producto.idunidad);
				$(".resultado_idproducto").data("desc", res.producto.descripcion_detallada);
				$(".resultado_producto").val(res.producto.descripcion_detallada);
			}
			else {
				// if($(".resultado_tipo").val() == "A")
					$(".resultado_idproducto,.resultado_idunidad,.resultado_producto").val("");
			}
			cargarResultado();
		});
	}
	else {
		// if($(".resultado_tipo").val() == "A")
		$(".resultado_idproducto,.resultado_idunidad,.resultado_producto").val("");
	}
}

$(".conversion_producto,.conversion_cantidad,.conversion_idunidad,.conversion_equivalencia").keypress(function(e) {
	var t = e.keyCode ? e.keyCode : e.which;
	if(t == 9) {
		e.preventDefault();
		return;
	}
	if(t == 13) {
		e.preventDefault();
		var f = {
			conversion_producto: ".conversion_idunidad"
			,conversion_idunidad: ".conversion_cantidad"
			,conversion_cantidad: ".conversion_equivalencia"
			,conversion_equivalencia: ".conversion_producto"
		}
		$( f[$(this).attr("name")] ).focus();
	}
});

$(".conversion_cantidad,.conversion_equivalencia").keyup(function() {
	cargarResultado();
});

function cargarResultado() {
	$(".resultado_unidad,.resultado_cantidad").val("");
	
	if( $.isNumeric($(".conversion_idunidad").val()) ) {
		$(".resultado_unidad").val( $(".conversion_idunidad>option:selected").data("abreviatura") );
	}
	
	if( $.isNumeric($(".conversion_cantidad").val()) && $.isNumeric($(".conversion_equivalencia").val()) ) {
		var t = parseFloat($(".conversion_cantidad").val()) * parseFloat($(".conversion_equivalencia").val());
		$(".resultado_cantidad").val(t);
	}
	
	var s = '';
	
	if( $.isNumeric($(".resultado_idproducto").val()) == false ) {
		// no existe un producto dependiente
		s = $(".conversion_producto").val() + " X " + $(".resultado_unidad").val() + " (SUELTO)";
	}
	else {
		var s = $(".resultado_idproducto").data("desc");
		// if(typeof s != "undefined") {
		if(typeof s == "undefined") {
			// s = String(s).replace(/\(SUELTO\)/g, "");
			// s = $.trim(s) + " X " + $(".resultado_unidad").val() + " (SUELTO)";
			s = $(".conversion_producto").val() + " X " + $(".resultado_unidad").val() + " (SUELTO)";
		}
	}
	$(".resultado_producto").val(s);
}

$(".btn-edit-resultado").click(function(e) {
	e.preventDefault();
	var bool = ! $(".resultado_producto").prop("readonly");
	
	$(".resultado_producto").prop("readonly", bool);
	
	if(bool)
		$(this).html('<i class="fa fa-pencil"></i>');
	else
		$(this).html('<i class="fa fa-times"></i>');
});

$(".btn-search-producto").click(function(e) {
	e.preventDefault();
	jFrame.create({
		title: "Buscar producto"
		,controller: "consultarproducto"
		,method: "grilla_producto_popup"
		,msg: ""
		,widthclass: "modal-lg"
		,onSelect: function(datos) {
			$(".resultado_tipo").val("B");
			$(".resultado_idproducto").data("desc", datos.descripcion_detallada);
			$(".resultado_idproducto").val(datos.idproducto);
			$(".resultado_idunidad").val(datos.idunidad);
			$(".resultado_producto").val(datos.descripcion_detallada);
		}
	});
	
	jFrame.show();
	return false;
});

$(".btn-del-producto").click(function(e) {
	e.preventDefault();
	var bool = ($.trim($(".resultado_idproducto").val()) != "");
	$(".resultado_idproducto").val("");
	$(".resultado_idunidad").val("");
	if(bool) {
		ventana.alert({titulo:"", mensaje:"La relacion se ha eliminado"});
	}
});

$("#btn-save-conversion").click(function(e) {
	e.preventDefault();
	var form = "#modal-conversion";
	var v = true;
	v = v && $(".tipo_operacion", form).required();
	v = v && $(".idalmacen", form).required();
	v = v && $(".conversion_producto", form).required();
	v = v && $(".conversion_idunidad", form).required();
	v = v && $(".conversion_cantidad", form).required({numero:true,tipo:'float',aceptaCero:false});
	v = v && $(".conversion_equivalencia", form).required({numero:true,tipo:'float',aceptaCero:false});
	v = v && $(".resultado_cantidad", form).required({numero:true,tipo:'float',aceptaCero:false});
	if(v) {
		if($.trim($(".resultado_idunidad", form).val()) != "") {
			if($(".resultado_idunidad", form).val() != $(".conversion_idunidad", form).val()) {
				ventana.confirm({
					titulo: "Confirmar"
					,mensaje: "El producto que ha seleccionado tiene unidad de medida diferente de la "+
						"unidad de medida de conversion. ¿Desea continuar y guardar con los datos ingresados?"
					,textoBotonAceptar: "Guardar"
					,textoBotonCancelar: "Cancelar"
					,cerrarConTeclaEscape: false
				}, function(isOk) {
					if(isOk) {
						guardarFormConversion(form);
					}
				});
				return;
			}
		}
		
		guardarFormConversion(form);
	}
	return false;
});

function guardarFormConversion(form) {
	var str = $(form).serialize();
	str += "&idalmacen_salida=" + $(".div-salida .idalmacen", form).val();
	str += "&idalmacen_entrada=" + $(".div-entrada .idalmacen", form).val();
	
	ajax.post({url: _base_url+"consultarproducto/conversion", data:str}, function() {
		grilla.reload(_default_grilla);
		$(form).modal("hide");
	});
}

$("#modal-conversion").on('hidden.bs.modal', function () {
	$("input:text", this).val("");
	$("input:hidden", this).val("");
	$("textarea", this).val("");
});

/********************** boton codigo barras ***********************/
$("#modal-codigo-barras .precio_sugerido").numero_real();

$("#btn-codigo-barras").click(function(e) {
	e.preventDefault();
	var id = grilla.get_id(_default_grilla);
	if(id != null) {
		ajax.post({url: _base_url+"producto/get/"+id}, function(data) {
			var form = "#modal-codigo-barras";
			$(".idproducto", form).val(data.idproducto);
			$(".producto", form).val(data.descripcion_detallada);
			$(".codigo_barras", form).val(data.codigo_barras);
			$(".precio_sugerido", form).val(data.precio_venta);
			$(form).modal("show");
		});
	}
	else {
		ventana.alert({titulo: "Error", mensaje: "Seleccione un producto de la lista."});
	}
});

$("#btn-generar-codigo-barras").click(function(e) {
	e.preventDefault();
	var form = "#modal-codigo-barras";
	var v = true;
	v = v && $(".idproducto", form).required();
	v = v && $(".producto", form).required();
	v = v && $(".codigo_barras", form).required();
	v = v && $(".precio_sugerido", form).required({numero:true, tipo:'float'});
	v = v && $(".cantidad", form).required({numero:true, tipo:'integer'});
	if(v) {
		var str = $(form).serialize();
		open_url("consultarproducto/gen_codigobarras?"+str);
	}
});

$("#ver-pdf").click(function(e){
	e.preventDefault();
	str = $("#form_filtro").serialize();
	str+= "&con_stock="+$(".filtro_stock:checked").val();

	open_url_windows(_controller+"/imprimir?"+str);
});

$("#exportar").click(function(e){
	e.preventDefault();
	str = $("#form_filtro").serialize();
	str+= "&con_stock="+$(".filtro_stock:checked").val();

	open_url_windows(_controller+"/exportar?"+str);
});