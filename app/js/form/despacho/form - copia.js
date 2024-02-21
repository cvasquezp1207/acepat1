if(typeof form == 'undefined') {
	form = {};
}

function lista_almacen(ind){
	ajax.post({url: _base_url+"recepcion/lista_almacen", data:""}, function(data) {
		li_almacen='';
		li_almacen += "<select id='almacen"+ind+"' name='almacen' class='form-control' required='' style=''>";
					// alert(data.length);
		if(data.length) {
			for(var i in data) {
				li_almacen += "<option value='"+data[i].idalmacen+"'>"+data[i].descripcion+"</option>";
			}
		}
		li_almacen += "</select>";
		$( "#tbl-detalle  tbody td.comboalmacen"+ind ).html(li_almacen);
	});
}


function consultar_recepcion(ind,idcompra,idproducto){
	can_pendiente = 0;
	ajax.post({
		url: _base_url+"recepcion/can_recepcionada/", data: "idcompra="+idcompra+"&idproducto="+idproducto}, function(data) {
			html1 = "<input type='text' style='text-align: right;' readonly id='cant_recep"+ind+"' name='cant_recep"+ind+"' class='form-control input-sm cant_recep"+ind+"' value='"+data[0]['recep']+"'>";
			
			cantidad = $("#cantidad"+ind).val();
			can_pendiente = cantidad - data[0]['recep'];
			
			estado='enabled';
			if(can_pendiente<=0){
				estado=' readonly disabled';	
			}
						
			html2 = "<input type='text' style='text-align: right;' readonly id='can_pendi"+ind+"' name='can_pendi"+ind+"' class='form-control input-sm can_pendi"+ind+"' value='"+can_pendiente+"'>";
			
			html3 = "<input type='text' style='text-align: right; border-color: #1ab394;' "+estado+" id='new_recepcion"+ind+"' name='new_recepcion' class='form-control input-sm new_recepcion"+ind+"' value='"+can_pendiente+"'>";
			
			$( "#tbl-detalle  tbody td.can_recep"+ind ).html(html1);
			$( "#tbl-detalle  tbody td.can_pendi"+ind ).html(html2);
			$( "#tbl-detalle  tbody td.new_recep"+ind ).html(html3);
		}
	);
}


$("#buscar_compra").focus();

$("#form_recepcion").submit(function() {
	return false;
});



$("#buscar_compra").autocomplete({
	source: function( request, response ) {
		ajax.post({url: _base_url+"compra/autocomplete", data: "maxRows=50&startsWith="+request.term, dataType: 'json'}, function(data) {
			response( $.map( data, function( item ) {
				return {
					label: item.buscar_compra
				   ,value: "O.C Nro:"+item.idcompra+" - "+item.nombre+" "+item.ruc
				   ,descripcion: item.nombre
				   ,ruc: item.ruc
				   ,id: item.idcompra
				}
			}));
		});
	},
	select: function( event, ui ) {
		if(ui.item) {
			idcompra = ui.item.id;
			// alert(idcompra);
			$("#idcompra").val(idcompra);
			$("#tbl-detalle  tbody").children().remove()
			$.ajax({
				type: "post",
				url: _base_url+"/compra/select_detalle",
				data: "idcompra="+idcompra,
				dataType: "json",
				success: function(response) {
					html0 = "";
					$(response.lsProdctos_compras).each(function(ind, item){
						
						html0 = "<tr><td><input type='checkbox' name='pto_nume"+ind+"' checked value='"+item.idproducto+"'/></td>"
						html0 += "<td>"+item.descripcion+"</td>";						
						html0 += "<td class='comboalmacen"+ind+"'></td>";
						html0 += "<td align='center'>"+item.abreviatura+"</td>";
						html0 += "<td><input type='text' style='text-align: right;' readonly id='cantidad"+ind+"' name='cantidad"+ind+"' class='form-control input-sm cantidad"+ind+"' value='"+item.cantidad+"'></td>";
						html0 += "<td class='can_recep"+ind+"'></td>";
						html0 += "<td class='can_pendi"+ind+"'></td>";
						html0 += "<td class='new_recep"+ind+"'></td>";
						html0 += "<td><button class='btn btn-success btn-xs btn_deta_serie' data-toggle='tooltip' title='Ingresar las series del producto'><i class='fa fa-cubes'></i></button></td>";
						html0 += "</tr>";
						
						$( "#tbl-detalle  tbody" ).append(html0);
						lista_almacen(ind);
						consultar_recepcion(ind,idcompra,item.idproducto);
					});

				}
			});
			
			
		}
	}
}).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
	return $( "<li>" )
	.data( "ui-autocomplete-item", item )
	.append( "<strong> O.C Nro:"+item.id+"</strong>| "+item.descripcion+" "+item.ruc )
	.appendTo( ul );

};

// $("#buscar_compra").keypress(function(e) {
	// var t = e.keyCode ? e.keyCode : e.which;
	// if(t == 13) {
		// $("#btn-agregar-producto").trigger("click");
		// return false;
	// }
// });





// $('#fecha_compra').datepicker({
	// todayBtn: "linked",
	// keyboardNavigation: false,
	// forceParse: false,
	// autoclose: true,
	// language: 'es',
// });



// $(document).on("change", "#tbl-detalle tbody tr select.deta_idunidad", function() {
	// var opt, openmodal = false;
	// if($(this).val() == "N") {
		// opt = $("option:first", this);
		// $(this).val(opt.attr("value"));
		// openmodal = true;
	// }
	// else {
		// opt = $("option:selected", this);
	// }
	// $(this).attr("title", opt.attr("title"));
	// $(this).attr("data-original-title", opt.attr("title"));
	
	// if(openmodal) {
		// show_dialog_unidad_medida($(this).closest("tr"));
	// }
// });


// $(document).on("keyup", "#tbl-detalle tbody tr input.cant_recep", function() {
	// calcularDatos($(this).closest("tr"));
	// alert("dd");
// });

// $(document).on("keyup", "#tbl-detalle tbody tr input.deta_precio", function() {
	// calcularDatos($(this).closest("tr"));
// });

// $(document).on("blur", "#tbl-detalle tbody tr input.deta_precio", function() {
	// if($.isNumeric($(this).val())) {
		// var v = parseFloat($(this).val());
		// $(this).val(v.toFixed(_fixed_compra));
	// }
// });


$(document).on("click", "button.btn_deta_delete", function() {
	alert("ddd");
	
	// $(this).tooltip('destroy');
	// $(this).closest("tr").remove();
	// calcularSubtotal();
	// calcularIgv();
	// calcularTotal();
});


$("#btn_save_recepcion").click(function() {
	var v = true;
	v = v && $("#buscar_compra").required();
	v = v && $("#observacion").required();
	v = v && $("#idtipodocumento").required();
	v = v && $("#serie").required();
	v = v && $("#numero").required();
	if(v) {
		if(!$("#buscar_compra").required()) {
			ventana.alert({titulo: "Error", mensaje: "Seleccione una Orden de Compra para poder hacer la recepcion de Mercaderia"});
			return;
		}
		// if($("#tbl-detalle tbody tr").length < 1) {
			// ventana.alert({titulo: "Error", mensaje: "Agregue los productos de la compra a la tabla"});
			// return;
		// }
		
		if(v) {			
			producto='';
			i = 0;
			$('#tbl-detalle tbody input[type=checkbox]').each( function() {
				if($(this).val()!='ChkFull'){
					if($(this).is(':checked')){
						if($(this).val()!=''){
							producto += $(this).val()+'-'+$("#almacen"+i).val()+'-'+$("#new_recepcion"+i).val()+'*';
						}
					}
				i++;
				}
			});
			
			ls_producto=producto.substring(0,(producto.length-1));
			if(ls_producto!=''){
				form.guardar(ls_producto);			
			}
			else{
				ventana.alert({titulo: "Error", mensaje: "Seleccione al menos un producto para realizar una recepcion"});
				return;
			}
			
				
		}
	}
});
