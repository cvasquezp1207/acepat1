if(typeof form.guardar_material != 'function') {
	form.guardar_material = function() {
		var data = $("#form_material").serialize();
		model.save(data, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				$("#modal-material").modal("hide");
			});
		}, "material");
	}
}

$("#modal-material").on('shown.bs.modal', function () {
	$('#mat_descripcion').focus();
});

$("#modal-material").on('hidden.bs.modal', function () {
	clear_form("#form_material");
});

validate("#form_material", form.guardar_material);