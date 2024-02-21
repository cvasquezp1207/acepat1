// funciones principales, estas funciones se invocaran cuando
// se haga click en cualquier boton de accion no incluye
// los eventos de botones dentro del formulario
var form = {
	nuevo: function() {

	},
	editar: function(id) {
		// alert(id);
	},
	eliminar: function(id) {
		model.del(0, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Registro eliminado correctamente", tipo:"success"}, function() {
				grilla.reload(_default_grilla); // _default_grilla=perfil, si no se indico otro nombre
			});
		}, _controller, 'eliminar', {idsucursal:id.idsucursal, fecha_inicio:id.fecha_inicio, fecha_fin:id.fecha_fin});
	},
	imprimir: function() {
		// grilla.set_filter(_default_grilla, "idperfil", "=", "2"); // indicar filtro
		// grilla.del_filter(_default_grilla, "idperfil"); // eliminar filtro
		// grilla.reload(_default_grilla);
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

function deleteEvent() {
	$("#btn_editar, #btn_eliminar").off("click");
}

function addEvent() {
	$("#btn_editar, #btn_eliminar").off("click");
	$("#btn_editar, #btn_eliminar").on("click", function() {
		var id = grilla.get_data(_default_grilla);
		if(id != null) {
			if(this.id == "btn_editar") {
				if(_type_form=="reload") {
					var datos = {idsucursal:id.idsucursal, fecha_inicio:id.fecha_inicio, fecha_fin:id.fecha_fin};
					redirect(_controller+"/editar?"+$.param(datos));
					return false;
				}
				form.editar(id);
			}
			else {
				ventana.confirm({titulo:"Confirmar",
				mensaje:"¿Desea eliminar el registro seleccionado?",
				textoBotonAceptar: "Eliminar"}, function(ok){
					if(ok) {
						// if(_type_form=="reload") {
							// redirect(_controller+"/eliminar/"+id);
							// return false;
						// }
						form.eliminar(id);
						// form.del(id);
					}
				});
			}
		}
		else {
			ventana.alert({titulo: "Aviso", mensaje: "Seleccione un registro de la tabla"});
		}
		return false;
	});
}