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
		model.del(id, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Registro eliminado correctamente", tipo:"success"}, function() {
				grilla.reload(_default_grilla); // _default_grilla=perfil, si no se indico otro nombre
			});
		});
	},
	guardar: function() {
		// algunas validaciones aqui
		// var data = $("#form_perfil").serialize();
		// if($('#tipo').required()){
			var data = $("#form_"+_controller).serialize();
			model.save(data, function(res) {
				ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
					redirect(_controller);
				});
			});			
		// }
	},
	cancelar: function() {

	}
};

validate();

function setIcon(icon) {
	$("#icono_preview").html('<i class="fa '+icon+'"></i>');
}

$("a.select_icon").click(function() {
	var icon = $(this).data("icon");
	setIcon(icon);
	$("#icono").val(icon);
});

$("#icono").blur(function() {
	setIcon($(this).val());
});

$("#btn_cancel").click(function() {
	redirect(_controller);
	return false;
});

$("#descripcion").focus();

$(".btn_estado").on("click", function() {
	
});

$("#descripcion,#clase_name,#id_name,alias").alfanumerico({'permitir':' -_'})
