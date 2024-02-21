if(typeof form == 'undefined') {
	form = {};
}

if(typeof form.guardar_zona != 'function') {
	form.guardar_zona = function() {
		var data = $("#form_zona").serialize();
		model.save(data, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				$("#modal-zona").modal("hide");
				reload_combo("#idzona", {controller: "zona"}, function() {
					$("#idzona").val(res.idzona);
		  		});
			});
		}, "zona");
	}
}

$("#modal-zona").on('shown.bs.modal', function () {
	$('#mar_descripcion').focus();
});

$("#modal-zona").on('hidden.bs.modal', function () {
	clear_form("#form_zona");
});

validate("#form_zona", form.guardar_zona);

$("#btn_ubigeo").click(function() {
	ubigeo.ok(function(data) {
		$("#zon_ubigeo_descr").val(data.departamento+' - '+data.provincia+' - '+data.distrito);
		$("#zon_idubigeo").val(data.idubigeo);
	});
	ubigeo.show();
	return false;
});