var form = {
	
	guardar: function() {
		var data = $("#form_"+_controller).serialize();
		data+= "&"+$("#form_more").serialize();
		data+= "&tipodocumento="+$( "#idtipodocumento option:selected" ).text();
		model.save(data, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				//if ($("#idcompra").val()!='') {//EDITAR
					redirect(_controller);
				//}else{
					/*if( $("#idtipoventa").val() ==  2 || $("#idtipoventa").val() ==  4){//CREDITO
						$("#cliente_credito").html(res.cliente);
						$("#comprobante_credito").html(res.tipodocumento+' / '+pad(3, res.serie, '0')+'-'+pad(6, res.correlativo,'0')) ;
						$("#idventaCredito").val(res.idventa);
						$("#modal-form-credito").modal('show');
					}else{//CONTADO
						redirect(_controller);
					}*/					
				//}
			});
		});
	},
	cancelar: function() {
		
	}
};