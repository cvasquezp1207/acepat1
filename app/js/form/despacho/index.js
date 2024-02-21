var form = {
	nuevo: function() {
		
	},
	editar: function(id) {
		
	},
	eliminar: function(id) {
		
	},
	imprimir: function() {
		
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

function format_fecha(nRow, aData, iDisplayIndex) {
	$("td:eq(0)",nRow).html(aData.fecha_venta_es);
	$("td:eq(4)",nRow).html(fecha_es(aData.fecha_despacho));
}

$("select[filter]").on("change", function() {
	grilla.set_filter(_default_grilla, $(this).attr("filter"), "=", $(this).val());
	grilla.reload(_default_grilla);
});