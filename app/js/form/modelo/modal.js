if(typeof form.guardar_modelo != 'function') {
	form.guardar_modelo = function() {
		var data = $("#form_modelo").serialize();
		model.save(data, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				$("#modal-modelo").modal("hide");
			});
		}, "modelo");
	}
}

$("#modal-modelo").on('shown.bs.modal', function () {
	$('#mod_descripcion').focus();
});

$("#modal-modelo").on('hidden.bs.modal', function () {
	clear_form("#form_modelo");
});

validate("#form_modelo", form.guardar_modelo);