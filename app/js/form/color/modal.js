if(typeof form.guardar_color != 'function') {
	form.guardar_color = function() {
		var data = $("#form_color").serialize();
		model.save(data, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				$("#modal-color").modal("hide");
			});
		}, "color");
	}
}

$("#modal-color").on('shown.bs.modal', function () {
	$('#col_descripcion').focus();
});

$("#modal-color").on('hidden.bs.modal', function () {
	clear_form("#form_color");
});

validate("#form_color", form.guardar_color);