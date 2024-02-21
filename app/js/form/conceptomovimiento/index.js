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
				redirect(_controller);
			});
		});
	},
	imprimir: function() {
		grilla.set_where(_default_grilla, "idsucursal", "=", "1");
		grilla.reload(_default_grilla);
	},
	guardar: function() {
		var data = $("#form-data").serialize();
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

$("#descripcion").focus();

$(".btn_estado").on("click", function() {
	
});

	$("#descripcion").letras({'permitir':' '});
	$("#orden").numero_entero();

	$('.btn_nuevo').click(function(){
		Limpiar();
		if(!$(this).hasClass('disabled')){
			$idtipomovimiento					=	$('.seleccinado').attr('data-modulo');

			if($idtipomovimiento){
				$("#idtipomovimiento").val( $idtipomovimiento );
				$("#modal-form").modal('show');
			}else
				ventana.alert({titulo: "", mensaje: "Seleccione el registro."});
		}			
	});
	
	$('.btn_editar').click(function(){
		Limpiar();
		if(!$(this).hasClass('disabled')){
			$idconptmovimiento					=	$('.seleccinado').attr('data-value');
			$idtipomovimiento					=	$('.seleccinado').attr('data-value');

			if($idconptmovimiento){
				modificar($idconptmovimiento);
			}else
				ventana.alert({titulo: "", mensaje: "Seleccione el registro."});
		}
	});
	
	$('.btn_delete').click(function(){
		if(!$(this).hasClass('disabled')){
			$idconptmovimiento					=	$('.seleccinado').attr('data-value');

			if($idconptmovimiento){
				form.eliminar($idconptmovimiento);
			}else
				ventana.alert({titulo: "", mensaje: "Seleccione el registro."});
		}
	});
	
	$('.save_data').click(function(){
		form.guardar();
	});
	
	function guardar_todo() {
		var data = $("#form-all").serialize();
		console.log(data);
		ajax.post({url: _base_url+"conceptomovimiento/guardar_orden/", data: data}, function(res) {
			redirect(_controller);
			// return ;
		})
		// model.save(data, function(res) {
			// ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
			// });
		// });
	}

	function modificar(id){
		ajax.post({url: _base_url+"conceptomovimiento/get/"+id, data: {}}, function(res) {
			$("#idconceptomovimiento").val(id)
			$("#idtipomovimiento").val(res.idtipomovimiento)
			$("#descripcion").val(res.descripcion)
			$("#orden").val(res.orden)
			$("#ver_compra").val(res.ver_compra)
			$("#ver_venta").val(res.ver_venta)
			$("#ver_reciboingreso").val(res.ver_reciboingreso)
			$("#ver_reciboegreso").val(res.ver_reciboegreso)

			//$("#form-data .onoffswitch-checkbox").each(function(x,y){
				//nombre_resp = $(y).attr('id');
			//});
			$("#ver_compra").prop("checked",false);
			if (res.ver_compra == 'S') {
				$("#ver_compra").prop("checked",true);
			}

			$("#ver_venta").prop("checked",false);
			if (res.ver_venta == 'S') {
				$("#ver_venta").prop("checked",true);
			}

			$("#ver_reciboingreso").prop("checked",false);
			if (res.ver_reciboingreso == 'S') {
				$("#ver_reciboingreso").prop("checked",true);
			}

			$("#ver_reciboegreso").prop("checked",false);
			if (res.ver_reciboegreso == 'S') {
				$("#ver_reciboegreso").prop("checked",true);
			}

			$("#modal-form").modal('show');
		})
	}
	function Limpiar(){
		
	}