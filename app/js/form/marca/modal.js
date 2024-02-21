if(typeof form.guardar_marca != 'function') {
	form.guardar_marca = function() {
		var data = $("#form_marca").serialize();
		model.save(data, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				$("#modal-marca").modal("hide");
			});
		}, "marca");
	}
}

$("#modal-marca").on('shown.bs.modal', function () {
	$('#mar_descripcion').focus();
});

$("#modal-marca").on('hidden.bs.modal', function () {
	clear_form("#form_marca");
});

validate("#form_marca", form.guardar_marca);