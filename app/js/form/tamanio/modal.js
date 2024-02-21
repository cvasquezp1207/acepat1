if(typeof form.guardar_tamanio != 'function') {
	form.guardar_tamanio = function() {
		var data = $("#form_tamanio").serialize();
		model.save(data, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				$("#modal-tamanio").modal("hide");
			});
		}, "tamanio");
	}
}

$("#modal-tamanio").on('shown.bs.modal', function () {
	$('#tam_descripcion').focus();
});

$("#modal-tamanio").on('hidden.bs.modal', function () {
	clear_form("#form_tamanio");
});

validate("#form_tamanio", form.guardar_tamanio);