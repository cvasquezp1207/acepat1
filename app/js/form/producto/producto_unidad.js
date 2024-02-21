function actualizarComboUnidades() {
	var exists = [];
	if($("#tabla_unidad_medida input.idunidad").length) {
		$("#tabla_unidad_medida input.idunidad").each(function() {
			exists.push(parseInt($(this).val()));
		});
	}
	
	var options = '';
	
	$.each(UNIDAD_MEDIDA, function(i, value) {
		id = parseInt(value.idunidad);
		if(exists.indexOf(id) != -1) {
			return true;
		}
		options += '<option value="'+value.idunidad+'" desc="'+value.descripcion+'" abbr="'+value.abreviatura+'">'+value.descripcion+' ('+value.abreviatura+')</option>';
	});
	
	$("#unidad_medidad_filtro").html(options);
}

if(typeof form == 'undefined') {
	form = {};
}

if(typeof form.guardar_unidad_medida != 'function') {
	form.guardar_unidad_medida = function() {
		var data = $("#form_unidad_medida").serialize();
		model.save(data, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"});
		}, "producto", "guardar_unidad");
	}
}

function appendUnidadMedida(arr) {
	if(arr.length) {
		var html = '', data;
		for(var i in arr) {
			data = arr[i];
			html += '<tr data-idunidad="'+data.idunidad+'">';
			html += '<td><input type="hidden" name="idunidad[]" class="idunidad" value="'+data.idunidad+'">'+data.descripcion+' ('+data.abreviatura+')</td>';
			html += '<td><input type="text" name="cantidad_unidad[]" class="cantidad_unidad form-control input-sm" value="'+data.cantidad_unidad+'" readonly></td>';
			html += '<td><input type="text" name="cantidad_unidad_min[]" class="cantidad_unidad_min form-control input-sm" value="'+data.cantidad_unidad_min+'"></td>';
			html += '<td><button type="button" class="btn btn-default btn-xs btn_delete_unidad_medida">Eliminar</button></td>';
			html += '</tr>';
			
		}
		$("#tabla_unidad_medida tbody").append(html);
	}
}

actualizarComboUnidades();

$("#btn-add-unidad").click(function() {
	if($("#unidad_medidad_filtro").required()) {
		if($("#tabla_unidad_medida tbody tr[data-idunidad='"+$("#unidad_medidad_filtro").val()+"']").length) {
			return false;
		}
		
		var data = {
			idunidad: $("#unidad_medidad_filtro").val()
			,descripcion: $("#unidad_medidad_filtro option:selected").attr("desc")
			,abreviatura: $("#unidad_medidad_filtro option:selected").attr("abbr")
			,cantidad_unidad: 1
			,cantidad_unidad_min: ""
		};
		var arr = [];
		arr.push(data);
		
		appendUnidadMedida(arr);
		actualizarComboUnidades();
	}
	return false;
});

$(document).on("click", "button.btn_delete_unidad_medida", function() {
	$(this).closest("tr").remove();
	actualizarComboUnidades();
	return false;
});

$("#form_unidad_medida").submit(function() {
	return false;
});

$("#uni_prod_btn_save").on("click", function() {
	if( $("#tabla_unidad_medida .cantidad_unidad").required() && 
	$("#tabla_unidad_medida .cantidad_unidad_min").required() ) {
		form.guardar_unidad_medida();
	}
	return false;
});