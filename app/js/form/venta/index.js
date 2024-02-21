var form = {
	nuevo: function() {
		
	},
	editar: function(id) {
		// alert(id);
	},
	eliminar: function(id) {
		setTimeout(function() {
			ventana.prompt({titulo:"",
				mensaje:"<div style='text-align:left;margin-bottom:6px;'>&iquest;Por qu&eacute; esta eliminando el registro?.</div>",
				tipo: false,
				textoBotonAceptar: "Eliminar",
				placeholder: 'Ingrese motivo'
			}, function(inputValue) {
				if(inputValue !== false) {
					model.del(id, function(res) {
						ventana.alert({titulo: "En horabuena!", mensaje: "Registro eliminado correctamente", tipo:"success"}, function() {
							grilla.reload(_default_grilla);
							
							if(res.recibo_egreso == 1) {
								setTimeout(function() {
									ventana.alert({titulo:"Aviso", mensaje: "Si va retirar dinero de caja. Registre "+
										"un Recibo de Egreso por el monto total de la venta de "+res.total});
								}, 250);
							}
						});
					}, _controller, "eliminar", "motivo="+inputValue);
				}
			});
		}, 300);
	},
	imprimir: function(id) {
		var id = id||grilla.get_id(_default_grilla);
		if(id != null) {
			window.open(_base_url+'venta/imprimir/'+id,'_blank');
			 location.reload();// No lo veo necesario el reload
		}else{
			
		}
	},
	guardar: function(datos_aux) {
		var data = $("#form_"+_controller).serialize();
		// data += "&tipodocumento="+$( "#idtipodocumento option:selected" ).text();
		if(datos_aux) {
			data += "&"+datos_aux;
		}
		
		/*
		model.save(data, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function(resp) {
				if( $("#idtipoventa").val() ==  "1"){//contado
					if( _es_nueva_venta_ ){//es_nuevaventa
						setTimeout(function() {
							ventana.confirm({
								titulo: "Imprimir"
								,mensaje: "&iquest;Desea Imprimir El Comprobante...?"
								,textoBotonAceptar: "IMPRIMIR"
								,textoBotonCancelar: "Mas tarde"
								,cerrarConTeclaEscape: false
							}, function(isOk) {
								if(isOk) {
									form.imprimir(res.idventa);
								}
								// creo que en lugar de hacer el redirec deberia limpiar el form 
								// para que se quede en blanco listo para una nueva venta
								redirect(_controller);
							});
						}, 250);
					}else{
						// aqui deberia quedarse el form tal y como ha modificado el cliente y 
						// evitar hacer el redirec
					    redirect(_controller);
					}
				}
				else {// se supone venta al credito
					if(res.idcredito) { // editando una venta al credito
						redirect("credito/editar/"+res.idcredito);
					}
					else { // nueva venta al credito
						setTimeout(function() {
							ventana.confirm({
								titulo: "Crear cronograma"
								,mensaje: "¿Desea crear el cronograma de pagos ahora?"
								,textoBotonAceptar: "Crear cronograma"
								,textoBotonCancelar: "Mas tarde"
								,cerrarConTeclaEscape: false
							}, function(isOk) {
								if(isOk) {
									redirect("credito/nuevo/"+res.idventa);
								}
								else {
									redirect(_controller);
								}
							});
						}, 250);
					}
				}
			});
		});*/
		
		model.save(data, function(res) {
			// ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function(resp) {
				if( $("#idtipoventa").val() ==  "1"){//contado
					if( _es_nueva_venta_ ){//es_nuevaventa
						// setTimeout(function() {
							// ventana.confirm({
								// titulo: "Imprimir"
								// ,mensaje: "&iquest;Desea Imprimir El Comprobante...?"
								// ,textoBotonAceptar: "IMPRIMIR"
								// ,textoBotonCancelar: "Mas tarde"
								// ,cerrarConTeclaEscape: false
							// }, function(isOk) {
								// if(isOk) {
									form.imprimir(res.idventa);
								// }
								// creo que en lugar de hacer el redirec deberia limpiar el form 
								// para que se quede en blanco listo para una nueva venta
								// redirect(_controller);
							// });
						// }, 250);
					}else{
						// aqui deberia quedarse el form tal y como ha modificado el cliente y 
						// evitar hacer el redirec
					    redirect(_controller);
					}
				}
				else {// se supone venta al credito
					if(res.idcredito) { // editando una venta al credito
						redirect("credito/editar/"+res.idcredito);
					}
					else { // nueva venta al credito
						setTimeout(function() {
							ventana.confirm({
								titulo: "Crear cronograma"
								,mensaje: "¿Desea crear el cronograma de pagos ahora?"
								,textoBotonAceptar: "Crear cronograma"
								,textoBotonCancelar: "Mas tarde"
								,cerrarConTeclaEscape: false
							}, function(isOk) {
								if(isOk) {
									redirect("credito/nuevo/"+res.idventa);
								}
								else {
									redirect(_controller);
								}
							});
						}, 250);
					}
				}
			// });
		});
	},
	cancelar: function() {
		
	},
	cobrar: function(datos) {
		model.save(datos, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "La venta se ha cobrado, consulte su saldo en caja.", tipo:"success"}, function() {
				grilla.reload(_default_grilla);
			});
		}, _controller, "cobrar");
	}
};

$("select[filter]").on("change", function() {
	if($(this).val() == "")
		grilla.del_filter(_default_grilla, $(this).attr("filter"));
	else
		grilla.set_filter(_default_grilla, $(this).attr("filter"), "=", $(this).val());
	grilla.reload(_default_grilla);
});

// $("input[filter]").on("change", function() {
	// console.log($(this));
	// if($("#fecha_i").val() == ""){
		// grilla.del_filter(_default_grilla, $(this).attr("filter").attr("filter"));
		// grilla.del_filter(_default_grilla, $(this).attr("filter"));
	// }else{
		// if($("#fecha_f").val()!=''){
			// console.log( $("#fecha_i").val());
			// console.log( $("#fecha_f").val());
			// grilla.set_filter(_default_grilla, $(this).attr("filter"), ">=", $("#fecha_i").val());
			// grilla.set_filter(_default_grilla, $(this).attr("filter"), "<=", $("#fecha_f").val());
		// }else{
			// grilla.set_filter(_default_grilla, $(this).attr("filter"), "=", $("#fecha_i").val());
		// }
	// }
	// grilla.reload(_default_grilla);
// });

function formatoFechaGrilla(nRow, aData, iDisplayIndex) {
	// $("td:eq(0)",nRow).html(fecha_es(aData.fecha_venta, false));
	$("td:eq(0)",nRow).html(aData.fecha_venta_format);
	if(aData.estado == "I")
		$(nRow).addClass("danger");
}

$("#btn-cobrar-venta").click(function(e) {
	e.preventDefault();
	
	var id = grilla.get_id(_default_grilla);
	if(id != null) {
		ajax.post({url: _base_url+"venta/is_valid_cobro/"+id}, function(res) {
			pay.setMonto(res.total);
			pay.ok(function(datos) {
				var str = "idventa="+res.idventa+"&"+datos;
				form.cobrar(str);
			});
			pay.show();
		});
	}
	else {
		ventana.alert({titulo:"", mensaje: "Seleccione un registro de la tabla."});
	}
	
	return false;
});

$(document).on("click", "a.del-blank-cdp", function(e) {
	e.preventDefault();
	$(this).closest("div.sweet-alert").find("button.cancel").trigger("click");
	setTimeout(function() {
		open_form_del_blank_cdp();
	}, 200);
	return false;
});

$("#btn-anular").click(function(e) {
	e.preventDefault();
	
	var data = grilla.get_data(_default_grilla);
	if(data != null) {
		if(data.estado == "I") {
			ventana.confirm({
				titulo:"",
				mensaje:"<div style='text-align:left;margin-bottom:6px;'>El comprobante <b>"+data.comprobante+"</b> "+
					"se encuentre actualmente anulado &iquest;Desea Restaurar el Comprobante?</div>",
				tipo: false,
				textoBotonAceptar: "Restaurar"
			}, function(ok){
				if(ok) {
					ajax.post({url: _base_url+"venta/restaurar/"+data.idventa}, function(res) {
						ventana.alert({titulo: "En horabuena!", mensaje: "Comprobante Restaurado correctamente", tipo:"success"}, function() {
							grilla.reload(_default_grilla);
						});
					});
				}
			});
		}
		else {
			ventana.prompt({titulo:"",
				mensaje:"<div style='text-align:left;margin-bottom:6px;'><b>&iquest;Desea Anular el Comprobante "+
					data.comprobante+"...?</b></div><div class='text-left'><i>Si prefiere anular un comprobante "+
					"en blanco haga <a href='#' class='del-blank-cdp'><u>clic aqui</u></a>.</i></div>",
				tipo: false,
				textoBotonAceptar: "Anular",
				placeholder: 'Ingrese algun motivo'
			}, function(inputValue){
				if(inputValue === false)
					return false;
				
				if (inputValue === "") {
					swal.showInputError("Ingrese el motivo para anular el comprobante");
					return false
				}
				
				ajax.post({url: _base_url+"venta/anular", data:"idventa="+data.idventa+"&motivo="+inputValue}, function(res) {
					ventana.alert({titulo: "En horabuena!", mensaje: "Comprobante Anulado correctamente", tipo:"success"}, function() {
						grilla.reload(_default_grilla);
						
						if(res.recibo_egreso == 1) {
							setTimeout(function() {
								ventana.alert({titulo:"Aviso", mensaje: "Si va retirar dinero de caja. Registre "+
									"un Recibo de Egreso por el monto total de la venta de "+res.total});
							}, 250);
						}
					});
				});
			});
		}
	}
	else {
		ventana.confirm({
			titulo:"",
			mensaje:"<div style='text-align:left;margin-bottom:12px;'><b>Si desea eliminar un comprobante en blanco "+
				"haga clic en Continuar.</b></div><div style='text-align:left;font-size:81%;'><i><b>Nota</b>: Para anular un comprobante "+
				"emitido, seleccione un registro de la tabla y haga clic en Anular.</i></div>",
			tipo: false,
			textoBotonAceptar: "Continuar"
		}, function(ok){
			if(ok) {
				open_form_del_blank_cdp();
			}
		});
	}
	
	return false;
});

// $("#btn_eliminar").removeAttr('id').attr("id","eliminar_venta");

function open_form_del_blank_cdp() {
	ajax.post({url: _base_url+"venta/tipo_doc_anular", data:""}, function(res) {
		if(res){
			html = '';
			$(res).each(function(x,y){
				html+="<option value='"+y.idtipodocumento+"'>"+y.descripcion+"</option>"
			});
			$(".t_doc").html(html);
			
			reload_combo(".serie_doc",{
				controller: "tipo_documento",
				method: "get_series", 
				data: "idtipodocumento="+$('.t_doc').val()
			});
		}else{
			$(".t_doc").empty();
		}
		$("#form_anul").modal("show");
	});
}

// $("#eliminar_venta").click(function(e){
	// var id = id||grilla.get_id(_default_grilla);
	// if(id != null) {
		// form.eliminar(id);
	// }else{
		// setTimeout(function() {
			// open_form_del_blank_cdp();
		// }, 250);
		// ventana.confirm({titulo:"",
		// mensaje:"<div class='row'>"+
			// "	<div class='col-sm-12'><div style='text-align:left;'><strong>No seleccion&oacute; un comprobante</strong>... Ingrese los datos del comprobante vac&iacute;o que desea anular.</div></div>"+
			// "</div><br>"+
			// "<div class='row'>"+
			// "	<div class='col-sm-5'>"+
			// "		<div style='text-align:left;'>Tipo Documento</div><select class='form-control input-xs t_doc'></select>"+
			// "	</div>"+
			// "	<div class='col-sm-3'>"+
			// "		<div style='text-align:left;'>Serie</div><select class='form-control input-xs serie_doc'></select>"+
			// "	</div>"+
			// "	<div class='col-sm-4'>"+
			// "		<div style='text-align:left;'>Numero</div><input type='text' placeholder='000001' class='form-control input-xs nro_doc' style='display:block;height: 25px; padding: 4px 8px;font-size: 12px;line-height: 1.5;   border-radius: 3px;margin-top:0px;'>"+
			// "	</div>"+
			// "</div>"+
			// "<div class='row'>"+
			// "	<div class='col-sm-12'>"+
			// "		<div style='text-align:left;'>Motivo de anulacion</div>"+
			// "		<textarea id='txtMotivoAnulacion' class='form-control' style='resize:none;'></textarea>"+
			// "	</div>"+
			// "</div>",
		// textoBotonAceptar: "Anular"}, function(ok){
			// if(ok) {
				// ajax.post({url: _base_url+"venta/anular_vacio", data:"idtipodocumento="+$('.t_doc').val()+"&serie="+$('.serie_doc').val()+"&numero="+$('.nro_doc').val()+"&t_documento="+$(".t_doc option:selected").text()+"&motivo="+$("#txtMotivoAnulacion").val()}, function(res) {
					// ventana.alert({titulo: "Atencion..!", mensaje: res, tipo:"warning"}, function() {
						// redirect(_controller);
					// });
				// });
			// }else{
				
			// }
		// });
		// ajax.post({url: _base_url+"venta/tipo_doc_anular", data:""}, function(res) {
			// if(res){
				// html = '';
				// $(res).each(function(x,y){
					// html+="<option value='"+y.idtipodocumento+"'>"+y.descripcion+"</option>"
				// });
				// $(".t_doc").html(html);
				
				// reload_combo(".serie_doc",{
					// controller: "tipo_documento",
					// method: "get_series", 
					// data: "idtipodocumento="+$('.t_doc').val()
				// });
			// }else{
				// $(".t_doc").empty();
			// }
		// });
	// }
// });

$(".t_doc").change(function() {
	if($.isNumeric($(this).val())) {
		reload_combo(".serie_doc",{
			controller: "tipo_documento",
			method: "get_series", 
			data: "idtipodocumento="+$('.t_doc').val()
		}, function() {
			
		});
	}
});

$("#anular_vacio").click(function(e){
	bval = true && $(".t_doc").required();
	bval = bval && $(".serie_doc").required();
	bval = bval && $(".nro_doc").required();
	bval = bval && $("#txtMotivoAnulacion").required();
	
	if(bval){
		ajax.post({url: _base_url+"venta/anular_vacio", data:"idtipodocumento="+$('.t_doc').val()+"&serie="+$('.serie_doc').val()+"&numero="+$('.nro_doc').val()+"&t_documento="+$(".t_doc option:selected").text()+"&motivo="+$("#txtMotivoAnulacion").val()}, function(res) {
			if(res.status=='1'){
				ventana.confirm({titulo:"",
					mensaje:"Esta seguro que desea anular la <b>"+$(".t_doc option:selected").text()+" "+$(".serie_doc").val()+"-"+$(".nro_doc").val()+"</b> ?",
					textoBotonAceptar: "SI"}, function(ok){
						if(ok) {
							ajax.post({url: _base_url+"venta/anular_vacio", data:"idtipodocumento="+$('.t_doc').val()+"&serie="+$('.serie_doc').val()+"&numero="+$('.nro_doc').val()+"&t_documento="+$(".t_doc option:selected").text()+"&motivo="+$("#txtMotivoAnulacion").val()}, function(res) {
								ventana.alert({titulo: "Atencion..!", mensaje: res.sms, tipo:"warning"}, function() {
									redirect(_controller);
								});
							});
						}else{
							
						}
					});
			}else{
				ventana.alert({titulo: "Atencion..!", mensaje: res.sms, tipo:"warning"}, function() {

				});				
			}
		});
	}
});

$('#fecha_i,#fecha_f').datepicker({
	todayBtn: "linked",
	keyboardNavigation: false,
	forceParse: false,
	autoclose: true,
	language: 'es',
	format: "dd/mm/yyyy",
	endDate: parseDate(_current_date)
}).on("changeDate",function(e){
	e.preventDefault();
	
	if($("#fecha_i").val() == ""){
		grilla.del_filter(_default_grilla, "fecha_venta");
		grilla.del_filter(_default_grilla, "fechaventa");
	}else{
		if($("#fecha_f").val()!=''){			
			grilla.set_filter(_default_grilla, "fecha_venta", ">=", $("#fecha_i").val());
			grilla.set_filter(_default_grilla, "fechaventa", "<=", $("#fecha_f").val());
		}else{
			grilla.set_filter(_default_grilla, $(this).attr("filter"), "=", $("#fecha_i").val());
		}
	}
	grilla.reload(_default_grilla);
});

$(".nro_doc").numero_real();

/*Eventos acceso directo*/
$(document).ready(function(){
	$("#btn_nuevo").html("<i class='fa fa-file-o'></i> Nuevo <sub class='hotkey white'>(F1)</sub>");
	$("sub").css({"bottom":"0"});

	$(document).keydown(function(e) {
		var i = $("div.modal.fade.in").length;

		switch(e.keyCode) {
			// case 8:// "Esc"
				// e.preventDefault();
				// e.stopPropagation();

				// redirect(_controller);
				// break;
			
			case 27:// "Esc"
				e.preventDefault();
				e.stopPropagation();

				if(i<=0){
					redirect(_controller);
				}
				break;
			
			// case 13:// "Enter"
				// e.preventDefault();
				// e.stopPropagation();
					// if($("div.modal.fade.in#modal-pay").length>0){
						// $("button.btn-accept-pay").trigger("click");
					// }else if( $("div.showSweetAlert.visible").length>0 ){
						// $("div.showSweetAlert.visible .confirm").trigger("click");
					// }else if( $("div#modal-popup.modal.fade.in").length>0  ){
						// $("div#modal-popup.modal.fade.in button.select-modal-popup").trigger("click");
					// }else if( $("div#modal-precio-tempp.modal.fade.in").length>0 ){
						// addPrecio($("#ptemp"));
					// }else{
						// console.log("enter en otra part confirm");
					// }
				// break;
				
			case 120:// "F9"
				e.preventDefault();
				e.stopPropagation();
				
				// $("#btn-search-preventa").trigger("click");
				
				break;
			case 112:// "F1"
				e.preventDefault();
				e.stopPropagation();
				
				$("#btn_nuevo").trigger("click");				
				break;
				
			case 113://F2
				e.preventDefault();
				e.stopPropagation();
				
				$("#producto_descripcion").focus();
				break;
				
			case 114://F4
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
				$("#btn_save_venta").trigger('click');
				break;
			default: 
				// e.preventDefault();
				// e.stopPropagation();
				break;
		}
	});
});
