if(typeof form == 'undefined') {
	form = {};
}
$("#prov_ruc").numero_real();
if( !$.isFunction(form.guardar_proveedor) ) {
	// form.guardar_proveedor = function() {
		// var data = $("#form_proveedor").serialize();
		// model.save(data, function(res) {
			// ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				// $("#modal-proveedor").modal("hide");
			// });
		// }, "proveedor");
	// }
}

$("#modal-proveedor").on('hidden.bs.modal', function () {
	clear_form("#form_proveedor");
});

$("#prov_btn_cancel").click(function() {
    $("#modal-proveedor").modal("hide");
});

validate("#form_proveedor", form.guardar_proveedor);