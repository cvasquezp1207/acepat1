if(typeof form == 'undefined') {
	form = {};
}

if(typeof form.guardar_boton != 'function') {
	form.guardar_boton = function() {
		var data = $("#form_boton").serialize();
		model.save(data, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				$("#modal-boton").modal("hide");
				LoadBoton();
			});
		}, "boton");
	}
}

$("#modal-boton").on('shown.bs.modal', function () {
	$('#bot_descripcion').focus();
});

$("#modal-boton").on('hidden.bs.modal', function () {
	clear_form("#form_boton");
});

validate("#form_boton", form.guardar_boton);