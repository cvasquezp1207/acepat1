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

$("#btn_cancel").click(function() {
	redirect(_controller);
	return false;
});

$("#idbanco").focus();

if (typeof prefix_cuentas_bancarias === 'undefined')
    prefix_cuentas_bancarias='';

keyboardSequence([	"#idbanco"+prefix_cuentas_bancarias
						,"#idsucursal"+prefix_cuentas_bancarias
						,"#idmoneda"+prefix_cuentas_bancarias
						,"#nro_cuenta"+prefix_cuentas_bancarias
						,retornar_boton("cuentas_bancarias",prefix_cuentas_bancarias)
				], "#form_cuentas_bancarias");