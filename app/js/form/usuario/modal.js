if(typeof form.guardar_usuario != 'function') {
	form.guardar_usuario = function() {
		var data = $("#form_usuario").serialize();
		var arr = ["baja"];
		$.each(arr, function(i, val) {
			if($("#"+val).is(':checked'))
				data += "&" + val + "=S";
			else
				data += "&" + val + "=N";
		});
		model.save(data, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				$("#recibo_idpersona").val(res.idusuario);
				$("#cliente_razonsocial").val(res.nombres+' '+res.appat+' '+res.apmat);
				$("#modal-empleado").modal("hide");
			});
		}, "usuario");
	}
}

$("#usu_nombres,#usu_appat,#usu_apmat").letras({'permitir':' '});
$("#usu_telefono").numero_entero({'permitir':' #*-'});
$("#usu_dni").numero_entero();

$("#modal-empleado").on('shown.bs.modal', function () {
	$('#mar_descripcion').focus();
});

$("#modal-empleado").on('hidden.bs.modal', function () {
	clear_form("#form_usuario");
});


$("#usu_btn_cancel").click(function() {
    $("#modal-empleado").modal("hide");
	// $('.list_direcciones,.list_telefonos,.list_representantes').empty();
});

validate("#form_usuario", form.guardar_usuario);