var form = {
	nuevo: function() {
		
	},
	editar: function(id) {
		
	},
	eliminar: function(id) {
		model.del(id, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Registro eliminado correctamente", tipo:"success"}, function() {
				grilla.reload(_default_grilla);
			});
		});
	},
	imprimir: function() {
		var id = grilla.get_id(_default_grilla);
		if(id != null) {
			alert(id);
		}
	},
	guardar: function() {
		var arry = ["genera_alerta_stock"];
		var data = $("#form_"+_controller).serialize();
		$.each(arry, function(i, val) {
			if($("#"+val).is(':checked'))
				data += "&" + val + "=S";
			else
				data += "&" + val + "=N";
		});
		
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
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				redirect(_controller);
			});
		});
	},
	cancelar: function() {
		
	}
	,unidad_medida: function(id) {
		
	}
	,guardar_linea: function() {
		var data = $("#form_linea").serialize();
		model.save(data, function(res) {
			$("#modal-linea").modal("hide");
			$("#linea").val(res.descripcion);
			$("#idlinea").val(res.idlinea).data("data", res);
			get_prefijo();
		}, "linea");
	}
	,guardar_categoria: function() {
		var data = $("#form_categoria").serialize();
		model.save(data, function(res) {
			$("#modal-categoria").modal("hide");
			$("#categoria").val(res.descripcion);
			$("#idcategoria").val(res.idcategoria).data("data", res);
			get_prefijo();
		}, "categoria");
	}
	,guardar_marca: function() {
		var data = $("#form_marca").serialize();
		model.save(data, function(res) {
			$("#modal-marca").modal("hide");
			$("#marca").val(res.descripcion);
			$("#idmarca").val(res.idmarca).data("data", res);
			// reload_combo("#idmarca", {controller: "marca"}, function() {
				// $("#idmarca").val(res.idmarca);
			// });
			get_prefijo();
		}, "marca");
	}
	,guardar_modelo: function() {
		var data = $("#form_modelo").serialize();
		model.save(data, function(res) {
			$("#modal-modelo").modal("hide");
			$("#modelo").val(res.descripcion);
			$("#idmodelo").val(res.idmodelo).data("data", res);
			// reload_combo("#idmodelo", {controller: "modelo", data: "idmarca="+$("#idmarca").val()}, function() {
				// $("#idmodelo").val(res.idmodelo);
			// });
			get_prefijo();
		}, "modelo");
	}
	,guardar_color: function() {
		var data = $("#form_color").serialize();
		model.save(data, function(res) {
			$("#modal-color").modal("hide");
			$("#color").val(res.descripcion);
			$("#idcolor").val(res.idcolor);
		}, "color");
	}
	,guardar_material: function() {
		var data = $("#form_material").serialize();
		model.save(data, function(res) {
			$("#modal-material").modal("hide");
			$("#material").val(res.descripcion);
			$("#idmaterial").val(res.idmaterial);
		}, "material");
	}
	,guardar_tamanio: function() {
		var data = $("#form_tamanio").serialize();
		model.save(data, function(res) {
			$("#modal-tamanio").modal("hide");
			$("#tamanio").val(res.descripcion);
			$("#idtamanio").val(res.idtamanio);
		}, "tamanio");
	}
	,guardar_unidad: function() {
		var data = $("#form_unidad").serialize();
		model.save(data, function(res) {
			$("#modal-unidad").modal("hide");
			reload_combo("#idunidad", {controller: "unidad"}, function() {
				$("#idunidad").val(res.idunidad);
			});
		}, "unidad");
	}
};

validate();

$("#btn_cancel").click(function() {
	redirect(_controller);
	return false;
});

$("#linea").focus();

$(".float-number").numero_real();
$(".int-number").numero_entero();

/* $("#btn-registrar-marca").on("click", function() {
	$("#modal-marca").modal("show");
	return false;
});

$("#btn-registrar-modelo").on("click", function() {
	if( $("#idmarca").required() ) {
		$("#mod_idmarca").val($("#idmarca").val());
		$("#modal-modelo").modal("show");
	}
	return false;
});

$(document).on("change", "#idmarca", function() {
	if($(this).val() != "") {
		reload_combo("#idmodelo", {controller: "modelo", data: "idmarca="+$(this).val()});
	}
	else {
		$("#idmodelo").html('<option value=""></option>');
	}
}); */

$(".btn-registrar-unidad").on("click", function() {
	$("#modal-unidad").modal("show");
	return false;
});

// creo que esto es el boton de asignar nuevo, ya no deberia haber
// $("#btn_unidad_medida").on("click", function() {
	// var id = grilla.get_id(_default_grilla);
	// if(id != null) {
		// if(_type_form=="reload") {
			// redirect(_controller+"/unidad_medida/"+id);
			// return false;
		// }
		// form.unidad_medida(id);
	// }
	// else {
		// ventana.alert({titulo: "Aviso", mensaje: "Seleccione un registro de la tabla"});
	// }
	// return false;
// });

input.autocomplete({
	selector: "#linea"
	,controller: "linea"
	,label: "[descripcion]"
	,value: "[descripcion]"
	,highlight: true
	,show_new_item: true
	,onSelect: function(item) {
		$("#idlinea").val(item.idlinea).data("data", item);
		get_prefijo();
	}
	,onNewItem: function(term) {
		$("#lin_descripcion").val(term);
		$("#modal-linea").modal("show");
	}
});

input.autocomplete({
	selector: "#categoria"
	,controller: "categoria"
	,label: "[descripcion]"
	,value: "[descripcion]"
	,highlight: true
	,show_new_item: true
	,onSelect: function(item) {
		$("#idcategoria").val(item.idcategoria).data("data", item);
		get_prefijo();
	}
	,onNewItem: function(term) {
		$("#cat_descripcion").val(term);
		$("#modal-categoria").modal("show");
	}
});

input.autocomplete({
	selector: "#marca"
	,controller: "marca"
	,label: "[descripcion]"
	,value: "[descripcion]"
	,highlight: true
	,show_new_item: true
	,onSelect: function(item) {
		$("#idmarca").val(item.idmarca).data("data", item);
		get_prefijo();
	}
	,onNewItem: function(term) {
		$("#mar_descripcion").val(term);
		$("#modal-marca").modal("show");
	}
});

input.autocomplete({
	selector: "#modelo"
	,controller: "modelo"
	,label: "[descripcion]"
	,value: "[descripcion]"
	,highlight: true
	,show_new_item: true
	,onSelect: function(item) {
		$("#idmodelo").val(item.idmodelo).data("data", item);
		get_prefijo();
	}
	,onNewItem: function(term) {
		$("#mod_descripcion").val(term);
		$("#modal-modelo").modal("show");
	}
});

input.autocomplete({
	selector: "#producto_alterno"
	,controller: "producto"
	,label: "<strong>[codigo_producto]</strong>| [descripcion_detallada]"
	,value: "[descripcion_detallada]"
	,highlight: true
	,onSelect: function(item) {
		$("#codigo_alterno").val(item.idproducto);
	}
});

input.autocomplete({
	selector: "#color"
	,controller: "color"
	,label: "[descripcion]"
	,value: "[descripcion]"
	,highlight: true
	,show_new_item: true
	,onSelect: function(item) {
		$("#idcolor").val(item.idcolor);
	}
	,onNewItem: function(term) {
		$("#col_descripcion").val(term);
		$("#modal-color").modal("show");
	}
});

input.autocomplete({
	selector: "#material"
	,controller: "material"
	,label: "[descripcion]"
	,value: "[descripcion]"
	,highlight: true
	,show_new_item: true
	,onSelect: function(item) {
		$("#idmaterial").val(item.idmaterial);
	}
	,onNewItem: function(term) {
		$("#mat_descripcion").val(term);
		$("#modal-material").modal("show");
	}
});

input.autocomplete({
	selector: "#tamanio"
	,controller: "tamanio"
	,label: "[descripcion]"
	,value: "[descripcion]"
	,highlight: true
	,show_new_item: true
	,onSelect: function(item) {
		$("#idtamanio").val(item.idtamanio);
	}
	,onNewItem: function(term) {
		$("#tam_descripcion").val(term);
		$("#modal-tamanio").modal("show");
	}
});

input.autocomplete({
	selector: "#descripcion"
	,controller: "producto"
	,method: "autocomplete_descripcion"
	,label: "[descripcion]"
	,value: "[descripcion]"
	,highlight: true
});

function get_prefijo() {
	var str = "";
	if(typeof $("#idlinea").data("data") != "undefined")
		str += $.trim($("#idlinea").data("data").prefijo);
	if(typeof $("#idcategoria").data("data") != "undefined")
		str += $.trim($("#idcategoria").data("data").prefijo);
	if(typeof $("#idmarca").data("data") != "undefined")
		str += $.trim($("#idmarca").data("data").prefijo);
	if(typeof $("#idmodelo").data("data") != "undefined")
		str += $.trim($("#idmodelo").data("data").prefijo);
	
	if($.trim(str) != "") {
		// str += "_";
		// $("#pref_code_prod").text(str);
		$("#pref_codigo_producto").val(str);
	}
}

$("#idunidad").on("change", function() {
	var t = $("option:selected", this).text();
	if(t == "") {
		t = "&iquest;?";
	}
	
	$("table.tabla_unidad_medida abbr").html(t);
	update_combo_unidad_temp();
});

function add_unidad_medida() {
	var id = $("#combo_asignar_unidad_medidad").val();
	var desc = $("#combo_asignar_unidad_medidad option:selected").text();
	
	if($("table.tabla_unidad_medida tr[index="+id+"]").length) {
		// ventana.alert({titulo: "Unidad de medida asignado", 
		// mensaje: "La unidad de medida "+desc+" ya se encuentra en la tabla"});
		$("table.tabla_unidad_medida tr[index="+id+"] input.prod_cantidad_unidad_min").focus();
		return;
	}
	
	var uni = $("#idunidad option:selected").text();
	if(uni == "") {
		uni = "&iquest;?";
	}
	
	var html = '<tr index="'+id+'">';
	html += '<td><input type="text" name="prod_cantidad_unidad[]" class="prod_cantidad_unidad form-control input-xs" value="1" readonly></td>';
	html += '<td><input type="hidden" name="prod_unidad[]" class="prod_unidad" value="'+id+'">'+desc+'</td>';
	html += '<td>es <strong>equivalente</strong> a</td>';
	html += '<td><input type="text" name="prod_cantidad_unidad_min[]" class="prod_cantidad_unidad_min form-control input-xs"></td>';
	html += '<td><abbr title="Unidad de Medida para el control del stock del producto escogido por el usuario en la pesta&ntilde;a [Datos b&aacute;sicos]">'+uni+'</abbr></td>';
	html += '<td><button class="btn btn-danger btn-xs btn-del-unidad"><i class="fa fa-trash"></i></button></td>';
	html += '</tr>';
	
	$("table.tabla_unidad_medida").append(html);
	update_combo_unidad_temp();
}

$("#btn-asignar-unidad").on("click", function() {
	if($("#combo_asignar_unidad_medidad").required()) {
		add_unidad_medida();
	}
	return false;
});

$(document).on("click", "button.btn-del-unidad", function() {
	$(this).closest("tr").remove();
	return false;
});

function get_unidad_temp() {
	var html = '', id = 0;
	if($("#idunidad").val() != '') {
		id = $("#idunidad").val();
		html += '<option value="'+id+'">'+$("#idunidad option:selected").text()+'</option>';
	}
	if($(".tabla_unidad_medida tbody tr").length) {
		$(".tabla_unidad_medida tbody tr").each(function() {
			if(id != $("input.prod_unidad", this).val()) {
				html += '<option value="'+$("input.prod_unidad", this).val()+'">'+$("td:eq(1)", this).text()+'</option>';
			}
		});
	}
	return html;
}



  $("table.table_precio_producto_venta").on('keyup','.precio_venta_precio',function() {
  		var tr = $(this).closest("tr");
  		var pc = parseFloat($("#precio_compra").val());
  		var pv = parseFloat($(this).val());
  		var po = (pv-pc)*(100/pc);
  		$(".precio_venta_porcentaje",tr).val(po.toFixed(2));
   });

$("table.table_precio_producto_venta").on('keyup','.precio_venta_porcentaje',function() {
  		var tr = $(this).closest("tr");
  		var pc = parseFloat($("#precio_compra").val());
  		var po = parseFloat($(this).val());
  		var pv = (po/100+1)*pc;
  		$(".precio_venta_precio",tr).val(pv.toFixed(2));
   });



$("#add_precio_venta").on("click", function() {
	var html = '<tr>';
	
	// html += '<td><select name="precio_venta_idtipo_precio[]" class="precio_venta_idtipo_precio form-control input-xs">'+$("#tipo_precio_temp").html()+'</select></td>';
	html += '<td><select name="precio_venta_idunidad[]" class="precio_venta_idunidad form-control input-xs">'+get_unidad_temp()+'</select></td>';
	// html += '<td><select name="precio_venta_idmoneda[]" class="precio_venta_idmoneda form-control input-xs">'+$("#moneda_temp").html()+'</select></td>';
	html += '<td><input type="text" name="precio_venta_cantidad[]" class="precio_venta_cantidad form-control input-xs" value="1"></td>';
	html += '<td><input type="text" name="precio_venta_precio[]" class="precio_venta_precio form-control input-xs"></td>';
	html += '<td><input type="text" name="precio_venta_porcentaje[]" class="precio_venta_porcentaje form-control input-xs"></td>';
	html += '<td><button class="btn btn-danger btn-xs btn-del-precio-venta"><i class="fa fa-trash"></i></button></td>';
	html += '</tr>';
	
	$("table.table_precio_producto_venta").append(html);
	return false;
});
$(document).on("click", "button.btn-del-precio-venta", function() {
	$(this).closest("tr").remove();
	return false;
});

$("#add_precio_compra").on("click", function() {
	if($("#moneda_temp").required() && $("#unidad_temp").required()) {
		var id = $("#moneda_temp").val() +''+ $("#unidad_temp").val();
		if($("table.table_precio_producto_compra tr[index="+id+"]").length) {
			$("table.table_precio_producto_compra tr[index="+id+"] .precio_compra_precio").focus();
			return false;
		}
		
		var html = '<tr index="'+id+'">';
		html += '<td><input type="hidden" name="precio_compra_idunidad[]" class="precio_compra_idunidad" value="'+$("#unidad_temp").val()+'">'+$("#unidad_temp option:selected").text()+'</td>';
		html += '<td><input type="hidden" name="precio_compra_idmoneda[]" class="precio_compra_idmoneda" value="'+$("#moneda_temp").val()+'">'+$("#moneda_temp option:selected").text()+'</td>';
		html += '<td><input type="text" name="precio_compra_precio[]" class="precio_compra_precio form-control input-xs"></td>';
		html += '<td><button class="btn btn-danger btn-xs btn-del-precio-compra"><i class="fa fa-trash"></i></button></td>';
		html += '</tr>';
		
		$("table.table_precio_producto_compra").append(html);
		
	}
	return false;
});
$(document).on("click", "button.btn-del-precio-compra", function() {
	$(this).closest("tr").remove();
	return false;
});

function update_combo_unidad_temp() {
	$("#unidad_temp").html(get_unidad_temp());
}

update_combo_unidad_temp();