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
	imprimir: function() {
		grilla.set_where(_default_grilla, "idsistema", "=", "1");
		grilla.reload(_default_grilla);
	},
	guardar: function() {
		var data = $("#form_"+_controller).serialize();
		// $('#input_extras').html(
				// '<input type="hidden" name="controller" value="empresa">'
				// +'<input type="hidden" name="action" value="save">'
		// );
		if ($("#ruc").required_CarcEs()) {
			Save_action("form_"+_controller);
		};
		// model.save(data, function(res) {
			// ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				// redirect(_controller);
			// });
		// });
		
		// ajax.post({url: _base_url+_controller+"/guardar", data: $("#form_"+_controller).serialize(),type:'POST'}, function(res) {
			// console.log(res)
		// });
	},
	cancelar: function() {

	}
};

validate();

$("#btn_cancel").click(function() {
	redirect(_controller);
	return false;
});

$("#descripcion").focus();

$(".btn_estado").on("click", function() {
	
});

$("#load_photo").click(function() {
    $("#file").click();
});

function Save_action(id){//id= id del formulario
	var fd = new FormData(document.getElementById(id));
		$.ajax({
			url: _base_url+"empresa/guardar",
			type: "POST",
			data: fd,
			enctype: 'multipart/form-data',
			processData: false,  // tell jQuery not to process the data
			contentType: false   // tell jQuery not to set contentType
		}).done(function( data ) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				redirect(_controller);
			});
		});
		return false;
}

function leerarchivobin(f) {
    var imagenAR = document.getElementById("file");
    if (imagenAR.files.length != 0 && imagenAR.files[0].type.match(/image.*/)) {
		var lecimg = new FileReader();
		lecimg.onload = function(e) { 
			var img = document.getElementById("photo");
			img.src = e.target.result;
		} 
		lecimg.onerror = function(e) { 
			alert("Error leyendo la imagen!!");
		}
		lecimg.readAsDataURL(imagenAR.files[0]);
    } else {
		alert("Seleccione una imagen!!")
    }
}

/*$("#ruc").keyup(function(){
	$("#ruc").required_CarcEs();
});*/

$("#ruc").numero_entero();
$("#telefono").numero_entero({'permitir':' #*-'});