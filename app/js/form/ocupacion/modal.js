// if(typeof form == 'undefined') {
	// form = {};
// }
if(typeof form.guardar_ocupacion != 'function') {
	form.guardar_ocupacion = function() {
		var data = $("#form_ocupacion").serialize();
		model.save(data, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				$("#modal-ocupacion").modal("hide");
				reload_combo("#idocupacion", {controller: "ocupacion"}, function() {
					$("#idocupacion").val(res.idocupacion);
		  		});
			});
		}, "ocupacion");
	}
}

$("#modal-ocupacion").on('shown.bs.modal', function () {
	$('#ocup_ocupacion').focus();
});

$("#modal-ocupacion").on('hidden.bs.modal', function () {
	clear_form("#form_ocupacion");
});

validate("#form_ocupacion", form.guardar_ocupacion);