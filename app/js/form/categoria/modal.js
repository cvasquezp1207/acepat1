if(typeof form.guardar_categoria != 'function') {
	form.guardar_categoria = function() {
		var data = $("#form_categoria").serialize();
		model.save(data, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				$("#modal-categoria").modal("hide");
			});
		}, "categoria");
	}
}

$("#modal-categoria").on('shown.bs.modal', function () {
	$('#mar_descripcion').focus();
});

$("#modal-categoria").on('hidden.bs.modal', function () {
	clear_form("#form_categoria");
});

validate("#form_categoria", form.guardar_categoria);