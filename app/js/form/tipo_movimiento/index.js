var form = {
	nuevo: function() {
		
	},
	editar: function(id) {
		// alert(id);
	},
	eliminar: function(id) {
		// model.del(id, function(res) {
			// ventana.alert({titulo: "En horabuena!", mensaje: "Registro eliminado correctamente", tipo:"success"}, function() {
				// grilla.reload(_default_grilla);
			// });
		// });
	},
	guardar: function() {
		var data = $("#form_"+_controller).serialize();
		model.save(data, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				redirect(_controller);
			});
		});
	},
	cancelar: function() {
		
	}
};

validate();

$("#tipo_movimiento").numero_entero();
$("#correlativo").numero_entero();

$("#edit_correlativo").on("change", function() {
	var p = ! $(this).is(":checked");
	$("#correlativo").prop("readonly", p);
	if( ! p)
		$("#correlativo").focus();
});

$("#btn_cancel").click(function() {
	redirect(_controller);
	return false;
});

$("#tipo_movimiento").focus();