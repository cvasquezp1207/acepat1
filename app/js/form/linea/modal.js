if(typeof form.guardar_linea != 'function') {
	form.guardar_linea = function() {
		model.save(data, function(res) {
		var data = $("#form_linea").serialize();
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				$("#modal-linea").modal("hide");
			});
		}, "linea");
	}
}

$("#modal-linea").on('shown.bs.modal', function () {
	$('#lin_descripcion').focus();
});

$("#modal-linea").on('hidden.bs.modal', function () {
	clear_form("#form_linea");
});

validate("#form_linea", form.guardar_linea);