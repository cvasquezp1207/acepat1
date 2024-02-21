var form = {
	nuevo: function() {
		
	},
	editar: function(id) {
		// alert(id);
	},
	eliminar: function(id) {
		// model.del(id, function(res) {
			// ventana.alert({titulo: "En horabuena!", mensaje: "Registro eliminado correctamente", tipo:"success"}, function() {
				// grilla.reload(_default_grilla);
			// });
		// });
	},
	imprimir: function() {
		// var id = grilla.get_id(_default_grilla);
		// if(id != null) {
			// alert(id);
		// }
	},
	guardar: function(ls_producto) {
		var data = $("#form_"+_controller).serialize();
		
		// var data =  "idrecepcion=&idcompra="+$("#idcompra").val()+"&observacion="+$("#observacion").val()+"&idtipodocumento="+$("#idtipodocumento").val();
			// data += "&serie="+$("#serie").val()+"&numero="+$("#numero").val()+"&ls_producto="+ls_producto;

			model.save(data, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				redirect(_controller);
			});
		});
	},
	cancelar: function() {
		
	}
	// ,eliminar_recepcion: function(str) {
		// model.del_param(str, function(res) {
			// ventana.alert({titulo: "En horabuena!", mensaje: "Registro eliminado correctamente", tipo:"success"}, function() {
				// grilla.reload(_default_grilla);
			// });
		// });
	// }
};


// $("#btn_ver").on("click", function() {
	// var id = grilla.get_id(_default_grilla);
	// if(id != null) {
		// if(_type_form=="reload") {
			// redirect(_controller+"/view_all_recepcion/"+id);
			// return false;
		// }
		// form.view_all_recepcion(id);
	// }
	// else {
		// ventana.alert({titulo: "Aviso", mensaje: "Seleccione un registro de la tabla"});
	// }
	// return false;
// });

function format_fecha(nRow, aData, iDisplayIndex) {
	$("td:eq(0)",nRow).html(aData.fecha_compra_es);
	$("td:eq(4)",nRow).html(fecha_es(aData.fecha_recepcion));
}

$("select[filter]").on("change", function() {
	grilla.set_filter(_default_grilla, $(this).attr("filter"), "=", $(this).val());
	grilla.reload(_default_grilla);
});


/*Eventos acceso directo*/
$(document).ready(function(){
	// $("#btn_nuevo").button("option", "label", "<sub class='hotkey white'>(F1)</sub> Nuevo");
	$("#btn_nuevo").html("<i class='fa fa-file-o'></i> Nuevo <sub class='hotkey white'>(F1)</sub>");
	$("sub").css({"bottom":"0"});

	$(document).keydown(function(e) {
		var i = $("div.modal.fade.in").length;
		console.log(e.keyCode);
		switch(e.keyCode) {
			case 27:// "Esc"
				e.preventDefault();
				e.stopPropagation();
				if(i==0){
					redirect(_controller);
				}
				break;
			
			case 13:// "Enter"
				e.preventDefault();
				e.stopPropagation();

					if($("div.modal.fade.in#modal-pay").length>0){
						$("button.btn-accept-pay").trigger("click");
					}else if( $("div.showSweetAlert.visible").length>0 ){
						$("div.showSweetAlert.visible .confirm").trigger("click");
					}else if( $("div#modal-popup.modal.fade.in").length>0  ){
						$("div#modal-popup.modal.fade.in button.select-modal-popup").trigger("click");
					}else if( $("div#modal-precio-tempp.modal.fade.in").length>0 ){
						addPrecio($("#ptemp"));
					}else if($("div#modal-proveedor.modal.fade.in").length>0){
						$("div#modal-proveedor.modal.fade.in button#prov_btn_save").trigger("click");
					}else{
						console.log("enter en otra part confirm");
					}

				break;
				
			// case 120:// "F9"
				// e.preventDefault();
				// e.stopPropagation();
				
				// $("#btn-search-preventa").trigger("click");
				
				// break;
			case 112:// "F1"
				e.preventDefault();
				e.stopPropagation();
				
				$("#btn_nuevo").trigger("click");				
				break;
				
			case 113://F2
				e.preventDefault();
				e.stopPropagation();
				
				// $("#producto_descripcion").focus();
				break;
				
			case 114://
				e.preventDefault();
				e.stopPropagation();
				break;
				
			case 117://
				e.preventDefault();
				e.stopPropagation();
				break;
			case 118://
				e.preventDefault();
				e.stopPropagation();
				break;
				
			case 119://
				e.preventDefault();
				e.stopPropagation();
				break;
				
			case 116://
				e.preventDefault();
				e.stopPropagation();				
				console.log("no hacer nada F5");
				break;
			case 115: // "F4"
				e.preventDefault();
				e.stopPropagation();
				$("#btn_save_recepcion").trigger('click');
				break;
			default: 
				// e.preventDefault();
				// e.stopPropagation();
				break;
		}
	});
});
/*Eventos acceso directo*/