if(typeof form.guardar_unidad != 'function') {
	form.guardar_unidad = function() {
		var data = $("#form_unidad").serialize();
		model.save(data, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				$("#modal-unidad").modal("hide");
			});
		}, "unidad");
	}
}

$("#modal-unidad").on('shown.bs.modal', function () {
	$('#uni_descripcion').focus();
});

$("#modal-unidad").on('hidden.bs.modal', function () {
	clear_form("#form_unidad");
});

validate("#form_unidad", form.guardar_unidad);