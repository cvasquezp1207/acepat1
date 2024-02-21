
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

$('#btn_add_sucursal').click(function(){
	redirect(_controller+"/asign_suc");
})

$('.eliminar').bind('click',function(e){
	e.preventDefault();
	$su_li=$(this).parent('div').parent('li');
	Eventodelete($(this));
});

function setIcon(icon) {
	$("#icono_preview").html('<i class="fa '+icon+'"></i>');
}

$("a.select_icon").click(function() {
	var icon = $(this).data("icon");
	setIcon(icon);
	$("#image").val(icon);
});

$("#image").blur(function() {
	setIcon($(this).val());
});

function callbackSistema(nRow, aData, iDisplayIndex){
	$('td', nRow).eq(3).html('<center><i class="fa '+aData["image"]+'"></i></center>');
}

function Eventodelete(here){
	$input			= $(here).parent('div.pull-right');
	$input_sistema	= $input.find('input.idsistema').val();
	$input_sucursal	= $input.find('input.idsucursal').val();
	
	$sistema = $input.find('input.idsistema').attr('data-name');
	$sucursal = $(here).parent('div.pull-right').parent('li').parent('ul').find('li.ui-state-disabled');
	$li = $(here).parent('div.pull-right').parent('li');
	
	ventana.confirm({titulo:"Confirmar", 
			mensaje:"¿Desea desvincular a "+$sistema+" de "+$sucursal.text()+"?", 
			textoBotonAceptar: "Eliminar"}, function(ok){
			if(ok) {
				// if($input_sistema)
					// ajax.post({url: _base_url+_controller+"/eliminar_detalle/", data: {idsistema:$input_sistema,idsucursal:$input_sucursal}}, function(res) {
						// redirect(_controller);
					// });
				// else
					$($li).fadeOut(800,function(){
						$($li).remove();
					});			
			}
	});	
}