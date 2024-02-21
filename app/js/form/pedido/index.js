var form = {
	nuevo: function() {
		
	},
	editar: function(id) {
		// alert(id);
	},
	eliminar: function(id) {
		model.del(id, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Registro eliminado correctamente", tipo:"success"}, function() {
				grilla.reload(_default_grilla);
			});
		});
	},
	imprimir: function() {
		// alert("ddd");
		var id = grilla.get_id(_default_grilla);
		if(id != null) {
			open_url_windows(_controller+"/imprimir?id="+id);
		}
	},
	guardar: function() {
		var data = $("#form_"+_controller).serialize();
		model.save(data, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				if($("#btn_cerrar_tab").length <= 0) {
					redirect(_controller);
				}
			});
		});
	},
	
	cancelar: function() {
		
	},
	aprobar: function(id) {
		ajax.post({url: _base_url+_controller+"/aprobar_pedido/"+id}, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Pedido aprobado correctamente", tipo:"success"}, function() {
				grilla.reload(_default_grilla);
			});
		});
	}
};

// $("#btn_ok_pedido").on("click", function() {
	// var id = grilla.get_id(_default_grilla);
	// if(id != null) {
		// if(_type_form=="reload") {
			// redirect(_controller+"/pedido_detalle/"+id);
			// return false;
		// }
		// form.pedido_detalle(id);
	// }
	// else {
		// ventana.alert({titulo: "Aviso", mensaje: "Seleccione un registro de la tabla"});
	// }
	// return false;
// });

$("#btn_aprobar").on("click", function() {
	var id = grilla.get_id(_default_grilla);
	form.aprobar(id);
	return false;
});

$("select[filter]").on("change", function() {
	grilla.set_filter(_default_grilla, $(this).attr("filter"), "=", $(this).val());
	grilla.reload(_default_grilla);
});