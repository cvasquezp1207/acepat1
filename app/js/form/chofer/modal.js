if(typeof form.guardar_chofer != 'function') {
	form.guardar_chofer = function() {
		var data = $("#form_chofer").serialize();
		model.save(data, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				$("#modal-chofer").modal("hide");
			});
		}, "chofer");
	}
}

$("#modal-chofer").on('shown.bs.modal', function () {
	$('#chof_nombre').focus();
});

$("#modal-chofer").on('hidden.bs.modal', function () {
	clear_form("#form_chofer");
});

validate("#form_chofer", form.guardar_chofer);