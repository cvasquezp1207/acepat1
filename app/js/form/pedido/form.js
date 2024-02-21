if(typeof form == 'undefined') {
	form = {};
}
/*
form.guardar_producto = function() {
	var data = $("#form_producto").serialize();
	model.save(data, function(res) {
		$("#modal-producto").modal("hide");
		$("#producto_descripcion").focus().select();
	}, "producto");
}
*/

form.guardar_producto = function() {
	var data = $("#form_producto").serialize();
	
	if($.trim($("#idlinea").val())==''){
		ventana.alert({titulo: "Hey..!", mensaje: "Debe seleccionar una Linea de la lista", tipo:"warning"}, function() {
			$("#linea").focus();
		});
		return;
	}
		
	if($.trim($("#idcategoria").val())==''){
		ventana.alert({titulo: "Hey..!", mensaje: "Debe seleccionar una Categoria de la lista", tipo:"warning"}, function() {
			$("#categoria").focus();
		});
		return;
	}
	if($.trim($("#idmarca").val())==''){
		ventana.alert({titulo: "Hey..!", mensaje: "Debe seleccionar una Marca de la lista", tipo:"warning"}, function() {
			$("#marca").focus();
		});
		return;
	}
	if($.trim($("#idmodelo").val())==''){
		ventana.alert({titulo: "Hey..!", mensaje: "Debe seleccionar una Modelo de la lista", tipo:"warning"}, function() {
			$("#modelo").focus();
		});
		return;
	}
	model.save(data, function(res) {
		$("#modal-producto").modal("hide");
		$("#producto_descripcion").focus().select();
	}, "producto");
}


form.guardar_proveedor = function() {
		var data = $("#form_proveedor").serialize();
		model.save(data, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				$("#proveedor").val(res.nombre);
				$("#idproveedor").val(res.idproveedor);
				$("#modal-proveedor").modal("hide");
			});
		}, "proveedor");
}

form.guardar_unidad_medida = function() {
	var data = $("#form_unidad_medida").serialize();
	model.save(data, function(res) {
		updateUnidades($("#uni_idproducto").val());
		$("#modal-unidad_medida").modal("hide");
	}, "producto", "guardar_unidad");
}

function agregarProducto(idproducto, callback) {
	ajax.post({url: _base_url+"producto/get/"+idproducto}, function(data) {
		if($("#tbl-detalle tbody tr[index="+data.idproducto+"]").length) {
			ventana.confirm({
				titulo:"Confirmar"
				,mensaje:"El producto "+data.descripcion_detallada+" ya se encuentra en la tabla. ¿Desea volver a agregar otra vez?"
				,textoBotonAceptar: "Agregar"
			}, function(ok) {
				if(ok) {
					addDetalle(data);
					updateUnidades(data.idproducto, data.idunidad);
				}
				if($.isFunction(callback)) {
					callback();
				}
			});
			}
		else {
			addDetalle(data);
			updateUnidades(data.idproducto, data.idunidad);
			if($.isFunction(callback)) {
				callback();
			}
		}
	});
}

function ordenarItem() {
	if($("#tbl-detalle tbody tr").length) {
		var i = 0;
		$("#tbl-detalle tbody tr").each(function() {
			$("td.item", this).html('<span class="badge">'+(++i)+'</span>');
		});
	}
}

function updateUnidades(idproducto, idunidad, tr) {
	ajax.post({url: _base_url+"producto/get_unidades/"+idproducto}, function(res) {
		var data, options='';
		
		if($.isArray(res)) {
			for(var i in res) {
				data = res[i];
				options += '<option value="'+data.idunidad+'" title="'+data.descripcion+'">'+data.abreviatura+'</option>';
			}
		}
		
		options += '<option value="N">Asignar otra unidad de medida?</option>';
		
		if(tr) {
			v = (idunidad) ? parseInt(idunidad) : parseInt($(".deta_idunidad", tr).val());
			$(".deta_idunidad", tr).html(options);
			if(!isNaN(v)) {
				$(".deta_idunidad", tr).val(v).trigger("change");
			}
			return;
		}
		
		$("#tbl-detalle tbody tr[index="+idproducto+"] select.deta_idunidad").each(function() {
			v = (idunidad) ? parseInt(idunidad) : parseInt($(this).val());
			
			$(this).html(options);
			if(!isNaN(v)) {
				$(this).val(v).trigger("change");
			}
		});
	});
}

function addDetalle(data) {
	if(typeof data.cantidad == "undefined") {
		data.cantidad = 1.00;
	}
	
	c = $("#tbl-detalle tbody tr").length + 1;
	
	var table = new Table();
	table.tr({index: data.idproducto});
	table.td('<span class="badge">'+c+'</span>', {class: "text-center item"});
	table.td('<input type="hidden" name="deta_idproducto[]" class="deta_idproducto" value="'+data.idproducto+'">'+data.descripcion_detallada);
	table.td('<select name="deta_idunidad[]" class="form-control input-xs deta_idunidad" data-toggle="tooltip" title=""></select>');
	//table.td('<input type="text" name="deta_cantidad[]" class="form-control input-xs deta_cantidad" value="'+data.cantidad+'">');
	table.td('<input type="text" placeholder="0.00" name="deta_cantidad[]" class="form-control input-xs deta_cantidad numerillo" value="'+data.cantidad+'" autocomplete="off">');
	table.td('<button class="btn btn-danger btn-xs btn_deta_delete" data-toggle="tooltip" title="Eliminar fila"><i class="fa fa-trash"></i></button>', {class:"text-center"});
	
	$("#tbl-detalle tbody").append(table.to_string());
	//$("#tbl-detalle tbody tr:last input.deta_cantidad").numero_entero();
	$("#tbl-detalle tbody tr:last input.deta_cantidad").numero_real();
	
	return $("#tbl-detalle tbody tr:last"); // ultima fila creada
}

function show_dialog_unidad_medida(tr) {
	ajax.post({url: _base_url+"producto/get_all/"+tr.attr("index")}, function(data) {
		$("#uni_producto_descripcion").text(data.producto.descripcion);
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

$("#form_pedido").submit(function() {
	return false;
});

input.autocomplete({
	selector: "#proveedor"
	,controller: "proveedor"
	,label: "[nombre]"
	,value: "[nombre]"
	,highlight: true
	,onSelect: function(item) {
		$("#idproveedor").val(item.idproveedor);
	}
});

$("#btn-buscar-proveedor").click(function(e) {
	e.preventDefault();
	
	jFrame.create({
		title: "Buscar Proveedor"
		,msg: ""
		,controller: "proveedor"
		,method: "grilla_popup"
		,onSelect: function(datos) {
			$("#proveedor").val(datos.nombre);
			$("#idproveedor").val(datos.idproveedor);
		}
	});
	
	jFrame.show();
	return false;
});

$("#btn-registrar-proveedor").on("click", function() {
	$("#modal-proveedor").modal("show");
	return false;
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

$('#fecha').datepicker({
	todayBtn: "linked",
	keyboardNavigation: false,
	forceParse: false,
	autoclose: true,
	language: 'es',
	// endDate: parseDate(_current_date)
});

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
});

$(document).on("click", "button.btn_deta_delete", function() {
	$(this).tooltip('destroy');
	$(this).closest("tr").remove();
	ordenarItem();
});

$("#btn_save_pedido").click(function(e) {
	e.preventDefault();
	var v = true;
	v = v && $("#descripcion").required();
	v = v && $("#fecha").required();
	v = v && $("#idalmacen").required();
	if(v) {
		// if(!$("#proveedor_idproveedor").required()) {
			// ventana.alert({titulo: "Error", mensaje: "Seleccione un proveedor de la lista o registre el proveedor si no existe"});
			// return;
		// }
		if($("#tbl-detalle tbody tr").length < 1) {
			ventana.alert({titulo: "Error", mensaje: "Agregue los productos de la compra a la tabla"});
			return;
		}
		v = v && $("input.deta_cantidad").required({numero:true, tipo:"float"});
		// v = v && $("input.deta_precio").required({numero:true, tipo:"int", aceptaCero:true});
		if(v) {
			form.guardar();
		}
	}
});
/*
$("#btn_cerrar_tab").on("click", function(e) {
	e.preventDefault();
	close_tab($(this).data("tabkey"));
});
*/
function llenarDetallePedido() {
	if($.isArray(data_detalle)) {
		for(var i in data_detalle) {
			tr = addDetalle(data_detalle[i]);
			updateUnidades(data_detalle[i].idproducto, data_detalle[i].idunidad, tr);
		}
	}
}

if(!_es_nuevo_pedido_) {
	llenarDetallePedido();
}