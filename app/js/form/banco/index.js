var form = {
	nuevo: function() {
		
	},
	editar: function(id) {
		// alert(id);
	},
	eliminar: function(id) {
		model.del(id, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Registro eliminado correctamente", tipo:"success"}, function() {
				grilla.reload(_default_grilla); // _default_grilla=perfil, si no se indico otro nombre
			});
		});
	},
	imprimir: function() {
		grilla.set_where(_default_grilla, "idperfil", "=", "1");
		grilla.reload(_default_grilla);
		// var id = grilla.get_id(_default_grilla);
		// if(id != null) {
			// alert(id);
		// }
	},
	guardar: function() {
		// algunas validaciones aqui
		// var data = $("#form_perfil").serialize();
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

$("#descripcion").focus();