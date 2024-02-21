if(typeof form.guardar_transporte != 'function') {
	form.guardar_transporte = function() {
		var data = $("#form_transporte").serialize();
		model.save(data, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				$("#modal-transporte").modal("hide");
			});
		}, "transporte");
	}
}

$("#modal-transporte").on('shown.bs.modal', function () {
	$('#trans_nombre').focus();
});

$("#modal-transporte").on('hidden.bs.modal', function () {
	clear_form("#form_transporte");
});

validate("#form_transporte", form.guardar_transporte);