if(typeof form == 'undefined') {
	form = {};
}

if( !$.isFunction(form.guardar_producto) ) {
	form.guardar_producto = function() {
		var arry = ["genera_alerta_stock"];
		var data = $("#form_producto").serialize();
		$.each(arry, function(i, val) {
			if($("#"+val).is(':checked'))
				data += "&" + val + "=S";
			else
				data += "&" + val + "=N";
		});
		
		// if($.trim($("#idlinea").val())==''){
			// ventana.alert({titulo: "Hey..!", mensaje: "Debe seleccionar una Linea de la lista", tipo:"warning"}, function() {
				// $("#linea").focus();
			// });
			// return;
		// }
		
		// if($.trim($("#idcategoria").val())==''){
			// ventana.alert({titulo: "Hey..!", mensaje: "Debe seleccionar una Categoria de la lista", tipo:"warning"}, function() {
				// $("#categoria").focus();
			// });
			// return;
		// }
		// if($.trim($("#idmarca").val())==''){
			// ventana.alert({titulo: "Hey..!", mensaje: "Debe seleccionar una Marca de la lista", tipo:"warning"}, function() {
				// $("#marca").focus();
			// });
			// return;
		// }
		// if($.trim($("#idmodelo").val())==''){
			// ventana.alert({titulo: "Hey..!", mensaje: "Debe seleccionar una Modelo de la lista", tipo:"warning"}, function() {
				// $("#modelo").focus();
			// });
			// return;
		// }
		model.save(data, function(res) {
			$("#modal-producto").modal("hide");
		}, "producto");
	}
}

form.guardar_marca = function() {
	var data = $("#form_marca").serialize();
	model.save(data, function(res) {
		$("#modal-marca").modal("hide");
		reload_combo("#idmarca", {controller: "marca"}, function() {
			$("#idmarca").val(res.idmarca);
		});
	}, "marca");
}

form.guardar_modelo = function() {
	var data = $("#form_modelo").serialize();
	model.save(data, function(res) {
		$("#modal-modelo").modal("hide");
		reload_combo("#idmodelo", {controller: "modelo", data: "idmarca="+$("#idmarca").val()}, function() {
			$("#idmodelo").val(res.idmodelo);
		});
	}, "modelo");
}

form.guardar_unidad = function() {
	var data = $("#form_unidad").serialize();
	model.save(data, function(res) {
		$("#modal-unidad").modal("hide");
		reload_combo("#idunidad", {controller: "unidad"}, function() {
			$("#idunidad").val(res.idunidad);
		});
	}, "unidad");
}

validate("#form_producto", form.guardar_producto);

$("#modal-producto").on('shown.bs.modal', function () {
	$("#prod_descripcion").focus();
});

$("#modal-producto").on('hidden.bs.modal', function () {
	clear_form("#form_producto");
});

$(".float-number").numero_real();
$(".int-number").numero_entero();

$("#btn-registrar-marca").on("click", function() {
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
	$("#idmodelo").html('<option value=""></option>');
	if($(this).val() != "") {
		reload_combo("#idmodelo", {controller: "modelo", data: "idmarca="+$(this).val()});
	}
});

$("#btn-registrar-unidad").on("click", function() {
	$("#modal-unidad").modal("show");
	return false;
});

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