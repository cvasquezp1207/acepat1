var item = 0;
var form = {
	nuevo: function() {
		
	},
	editar: function() {
		var str = $("#form_pago").serialize();
		str+= "&"+$("#form_detalle").serialize();
		ajax.post({url: _base_url+"pagoproveedor/anular_pago",data:str}, function(datos) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos Anulados correctamente", tipo:"success"}, function() {
				$("#filtrar").trigger("click");
			});
		});
	},
	eliminar: function(id) {
		var str = $("#form_pago").serialize();
		str+= "&"+$("#form_detalle").serialize();
		ajax.post({url: _base_url+"pagoproveedor/eliminar_letra",data:str}, function(datos) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos Eliminados correctamente", tipo:"success"}, function() {
				$("#filtrar").trigger("click");
			});
		});
	},
	imprimir: function() {
		grilla.set_where(_default_grilla, "idperfil", "=", "1");
		grilla.reload(_default_grilla);
	},
	guardar: function(datos_aux) {
		var data = $("#form_pago").serialize();
		data+= "&"+$("#form_detalle").serialize();
		// data += "&tipodocumento="+$( "#idtipodocumento option:selected" ).text();
		data += "&action=guardar";
		if(datos_aux) {
			data += "&"+datos_aux;
		}
		
		model.save(data, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				$("#filtrar").trigger("click");
			});
		});
	},
	cancelar: function() {
		
	}
};

validate();

$("#btn-buscar-proveedor").click(function() {
	jFrame.create({
		title: "Buscar Proveedor"
		,controller: "proveedor"
		,method: "grilla_popup"
		// ,autoclose: false
		,onSelect: function(datos) {
			$("#idproveedor").val(datos.idproveedor).trigger("chosen:updated")
		}
	});
	
	jFrame.show();
	return false;
});

$(".label_monto").html("Monto Bruto");
$("#tipo_cambio").numero_real();
$("#tipo_cambio").val("1.00");

$("#tipo_cambio").keyup(function(e){
	e.preventDefault();
	
	if( $(this).val() ){
		monto_convertido = parseFloat($(this).val())*parseFloat($("#monto_bruto").val());
		$("#monto_pagar").val(parseFloat(monto_convertido).toFixed(2));
	}else $(this).val(0);
});

$("#estado_letra").change(function(e){
	if( $(this).val()==0 ){
		$(".label_monto").html("Monto Bruto");
	}else if( $(this).val()=='S' ){
		$(".boton_pagar").hide();
		$(".boton_anular").show();
		$(".label_monto").html("Monto Pagado");
		Filtrar_proveedor('S');
	}else if( $(this).val()=='N' ){
		$(".label_monto").html("Monto Deuda");
		$(".boton_anular").hide();
		$(".boton_pagar").show();
		Filtrar_proveedor('N');
	}
});

$("#filtrar").click(function(e){
	e.preventDefault();

	str = $("#form_filtro").serialize();
	idprov = $("#idproveedor").val();
	if($("#idproveedor").val()==''){
		idprov = null;
	}
	grilla.set_filter(_default_grilla, "idproveedor", "=", idprov);
	
	if( $("#fecha_inicio").val()!='' ){
		if( $("#fecha_fin").val()!='' ){
			grilla.set_filter(_default_grilla, "fecha_vencimiento", ">=", $("#fecha_inicio").val());
			grilla.set_filter(_default_grilla, "fecha_vencimiento", "<=", $("#fecha_fin").val());
		}else{
			grilla.set_filter(_default_grilla, "fecha_vencimiento", "=", $("#fecha_inicio").val());
		}
	}else{}
	
	if( $('#estado_letra').val() != "" ){//pagados / amortizados
		grilla.set_filter(_default_grilla, "cancelado", "=", $("#estado_letra").val());
	}
	setTotal();
	grilla.reload(_default_grilla);
	$('#table_letas_pago tbody').empty();
});

$('#fecha_inicio,#fecha_fin').datepicker({
	todayBtn: "linked",
	keyboardNavigation: false,
	forceParse: false,
	autoclose: true,
	language: 'es',
	endDate: parseDate(_current_date)
});

$(document).on('click','.btn_deta_delete',function(e){
	$tr_ 			= $(this).parent("td").parent("tr");
	$letra 		= $tr_.attr("index_letra");
	$compra 	= $tr_.attr("index_compra");
	
	$tabla = $("#dtcronograma_pago_view tbody tr td").find("input.pk_index[value='"+$compra+$letra+"']")
	$tr_grid = $tabla.parent("div").parent("td").parent("tr");

	$tr_grid.removeClass('seleccionado active');
	$tabla.prop('checked', false);
	reordenarItem($('#table_letas_pago tbody'));
	$(this).closest("tr").remove();
	setTotal();
});

$(document).on('click','.odd,.even',function(){
	_tr = $(this);
	selector_tr = _tr.find('td').find('input.pk_index') ;
	selector_co = _tr.find('td').find('input.codcompra') ;
	selector_p = _tr.find('td').find('input.codproveed') ;
	selector_l = _tr.find('td').find('input.codletra') ;
	
	if($('#estado_letra').val()!=0){
		if(selector_tr.val()){
			if( selector_tr.is(':checked') ){
				selector_tr.prop('checked', false);
				_tr.removeClass('seleccionado active');
				Remover(selector_tr.val());
			}else{
				Cargardatos(selector_co.val(),selector_l.val(),$('#table_letas_pago'),$('#table_letas_pago tbody'));
					selector_tr.prop('checked', true);
				_tr.addClass('seleccionado');	
			}
		}
	}
});

$(document).on('click','input.pk_index',function(){
	selector = $(this);
			
		if( selector.is(':checked') ){
			selector.prop('checked', false);
		}else{
			selector.prop('checked', true);
		}
});

$(document).on('keyup','.monto_notacredito',function(e){
	x_value = 0;
	$tr_ = $(this).parent("td").parent("tr.tr");
	x_deuda = $tr_.find("input.monto_letra_deuda").val();

	if( $(this).val() !=''){
		x_value = parseFloat(x_deuda) - parseFloat( $(this).val() );
	}else{
		x_value = x_deuda;
	}
	
	if( $(this).val()>x_deuda){
		// e.preventDefault();
		// x_value = parseFloat(x_deuda) - parseFloat( $(this).val() );
		// console.log($(this).val());
		// console.log(x_deuda);
		// return false;
	}
	
	$tr_.find("input.monto_letra").attr("value",parseFloat(x_value).toFixed(2));
});
	
$("#pagar_letra").click(function(e){
	e.preventDefault();
	bval = true && $("#idtipopago").required();
	bval = bval && $("#descr_moneda").required();
	bval = bval && $("#tipo_cambio").required();
	bval = bval && $("#monto_bruto").required();
	bval = bval && $("#monto_pagar").required();
	
	if(bval){
		x_item = 0;
		$("#table_letas_pago tbody tr.tr").each(function(i,j){
			x_item++;
			
			if( $(this).find('input.monto_notacredito').val()>0 ){
				$(this).find('input.doc_notacredito').addClass('req');
			}
		});
		if(x_item>0)
			if($('.req').required()){
				l_item = 0;
				band = false;
				$("#table_letas_pago tbody tr.tr").each(function(i,j){					
					input_monto_cr = $(this).find('input.monto_notacredito');
					if( $(this).find('input.monto_letra').val()<0 ){
						l_item++;
						input_monto_cr.addClass('ui-state-error ui-icon-alert');
						$(input_monto_cr).focus()

						return band = false;

					}else{
						band = true;
						input_monto_cr.removeClass('ui-state-error ui-icon-alert');
					}
				});

				if(band){
					pay.setMonto($("#monto_pagar").val());
					pay.ok(function(datos) {
						form.guardar(datos);
					});
					pay.show();				
				}else{
					ventana.alert({titulo: "Hey..!!", mensaje: "El monto de la nota de credito, no debe ser mayor al monto de la letra"});
				}
			}
		else{
			ventana.alert({titulo: "Hey..!!", mensaje: "Si estas ingresando un monto mayor que cero en una nota de credito, sirvase a ingresar el numero del comprobante para posible referencia en el futuro"});
		}
	}
});

$("#eliminar_letra").click(function(e){
	e.preventDefault();
	
	x_item = 0;
		$("#table_letas_pago tbody tr.tr").each(function(i,j){
			x_item++;
		});
		if(x_item>0)
			form.eliminar();
		else{
			ventana.alert({titulo: "Hey..!!", mensaje: "Debe seleccionar al menos una letra para poder eliminar la letra de la lista de pagos pendientes"});
		}
});

$("#anular_amort").click(function(e){
	e.preventDefault();
	
	x_item = 0;
		$("#table_letas_pago tbody tr.tr").each(function(i,j){
			x_item++;
		});
		if(x_item>0)
			form.editar();
		else{
			ventana.alert({titulo: "Hey..!!", mensaje: "Debe seleccionar al menos una letra para poder eliminar la letra de la lista de pagos pendientes"});
		}
});

var config = {
                '.chosen-select'           : {},
                '.chosen-select-deselect'  : {allow_single_deselect:true},
                '.chosen-select-no-single' : {disable_search_threshold:10},
                '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
                '.chosen-select-width'     : {width:"98%"}
                }
            for (var selector in config) {
                $(selector).chosen(config[selector]);
            }
			
function callbackDeudas(nRow, aData, iDisplayIndex){
	ckb ='';
	if($('#estado_letra').val() == 'S'  && $.trim( $("#idproveedor").val())!='' ){//AMORTIZADO
		ckb = '<div class="checkbox checkbox-primary"><input type="checkbox" name="pkindex[]" class="pk_index" value="'+aData['pkey']+aData['letra']+'" /><label></label></div>';	
		ckb+= '<input type="hidden" name="letra_deuda[]" class="codletra" value="'+aData['letra']+'" />';
		ckb+= '<input type="hidden" name="codproveedor[]" class="codproveedor" value="'+aData['idproveedor']+'" />';
		ckb+= '<input type="hidden" name="codcompra[]" class="codcompra" value="'+aData['pkey']+'" />';
	}else{
		if($('#estado_letra').val() == 0){//TODOS
			ckb = '';
		}else if( $.trim( $("#idproveedor").val() )!='' ){//PENDIENTES
			ckb = '<div class="checkbox checkbox-primary"><input type="checkbox" name="pkindex[]" class="pk_index" value="'+aData['pkey']+aData['letra']+'" /><label></label></div>';
			ckb+= '<input type="hidden" name="letra_deuda[]" class="codletra" value="'+aData['letra']+'" />';
			ckb+= '<input type="hidden" name="codproveedor[]" class="codproveedor" value="'+aData['idproveedor']+'" />';
			ckb+= '<input type="hidden" name="codcompra[]" class="codcompra" value="'+aData['pkey']+'" />';
		}
	}
	// console.log('sss');
	$('td', nRow).eq(0).html(ckb);
	$('td', nRow).eq(6).html("<div style='text-align:right;'>"+aData['monto_letra']+"</div>");
 }
 
 function Remover(cod){
	$("#list-"+cod).remove();
	reordenarItem($('#table_letas_pago tbody'));
	setTotal();	
 }
 
 function setTotal(){
	x_total = 0;
	$("#table_letas_pago tbody tr.tr").each(function(i,j){
		if($("#estado_letra").val()=='S')
			x_total = x_total + parseFloat($(this).find('input.monto_pagado').val());
		else if($("#estado_letra").val()=='N')
			x_total = x_total + parseFloat($(this).find('input.monto_letra').val());
	});
	
	$("#monto_bruto").val(parseFloat(x_total).toFixed(2));
	$("#tipo_cambio").trigger("keyup");
 }
 
  function Cargardatos(id,letra,form,tbody){
	str = 'id=' + id + '&letra='+letra;
	accion = "capture_temp";
	if( $('#estado_letra').val() == "S" ){//pagados / amortizados
		accion = "return_temp";
	}
	
	//Comprobamos las monedas
	cant_money = 0;
	money = '';
	$("#table_letas_pago tbody tr.tr").each(function(i,j){
		cant_money++
		money = $(this).attr("index_moneda");
	});

	if(cant_money>0){// Verifico si la moneda a ingresar, es la misma que la existente

		xmoney = '';
		$("#table_letas_pago tbody tr.tr").each(function(i,j){
			xmoney = $(this).attr("index_moneda");
			if(money!=xmoney){
				ventana.alert({titulo: "Hey..!!", mensaje: "No puede realizar una misma operacion con diferente tipo de moneda"});
				return;
			}
			$("#descr_moneda").attr("value",money);
		});
	}else{
		$("#descr_moneda").attr("value",money);
	}
	//Comprobamos las monedas
	
	ajax.post({url: _base_url+"pagoproveedor/"+accion,data:str}, function(datos) {
		cargarDatosList(datos,tbody);
	});
 }
 
 function cargarDatosList(rows,tbody){	
	if(rows.length) {
		var data = null, tr = null, html = '';
		item=item+1;
		for(var i in rows) {
			data = rows[i];		
			
			monto = (data.monto) ? parseFloat(data.monto) : 0.00;
			monto_notacredito = (data.monto_notacredito) ? parseFloat(data.monto_notacredito) : 0.00;
			doc_notacredito = (data.doc_notacredito) ? data.doc_notacredito : '';
			monto_letra = (data.monto_letra) ? data.monto_letra : '0';
			monto_pagado = (data.monto) ? data.monto : '0';
			cambio_moneda = (data.cambio_moneda) ? data.cambio_moneda : $("#tipo_cambio").val();
			idpagocompra = (data.idpagocompra) ? data.idpagocompra : '';
			
			if( $('#estado_letra').val() == "S" ){
				monto_pagado = monto_letra;
				monto_letra = monto_pagado - monto_letra;
			}
			
			tr = $("<tr class='tr' id='list-"+data.idcompra+data.letra+"' index_letra='"+data.letra+"' index_moneda='"+data.moneda+"' index_compra='"+data.idcompra+"'></tr>");
            
			html   = '<td>'+'<span class="badge">'+item+'</span>'+'</td>';
			html += '<td>'+data.comprobante+'</td>';
            html += '<td>'+data.fecha_vencimiento+'</td>';
            html += '<td>'+'<input name="letra[]" value="'+data.letra+'" class="form-control input-xs is_numero " style="width:30px;" readonly />'+'</td>';
            html += '<td>'+'<input type="text" class="form-control input-xs is_numero monto_notacredito" name="monto_notacredito[]" placeholder="0.00" value="'+monto_notacredito+'" style="width:75px;" />'+'</td>';
            html += '<td>'+'<input type="text" class="form-control input-xs doc_notacredito" placeholder="001-00000001" name="doc_notacredito[]" value="'+doc_notacredito+'" style="width:100px;" />'+'</td>';
            html += '<td class="is_numero">'+'<input type="text" class="form-control input-xs is_numero monto_letra" name="monto[]" placeholder="0.00" value="'+parseFloat(monto_letra).toFixed(2)+'" readonly style="width:75px;" />'+'</td>';
            html += '<td class="is_numero">'+'<input type="text" class="form-control input-xs is_numero monto_pagado" name="monto_pagado[]" placeholder="0.00" value="'+parseFloat(monto_pagado).toFixed(2)+'" readonly style="width:75px;" />'+'</td>';
            html += '<td>'+'<button class="btn btn-danger btn-xs btn_deta_delete" data-toggle="tooltip" title="Eliminar registro"><i class="fa fa-trash"></i></button>'+'</td>';
			
            html += '<td style="display:none">';
			html += 	'<input class="codletra" 				name="idcompra[]" value="'+data.idcompra+'" />';
			html += 	'<input class="idmoneda" 			name="idmoneda[]" value="'+data.idmoneda+'" />';
			html += 	'<input class="cambio_moneda" name="cambio_moneda[]" value="'+cambio_moneda+'" />';
			html += 	'<input class="idpagocompra" 	name="idpagocompra[]" value="'+idpagocompra+'" />';
			html += 	'<input class="monto_letra_deuda" 	value="'+monto_letra+'" />';
			html += '</td>';
			tr.html(html);
			
			if( $("#descr_moneda").val()=='' )
				$("#descr_moneda").val(data.moneda);
            tbody.append(tr);
			setTotal();
			$("input.is_numero").numero_entero();
			$(".doc_notacredito").numero_entero({permitir:'-'});
		}
		reordenarItem($('#table_letas_pago tbody'));
		// $('#montosoles').val(number_format(monto_soles,2,'.',''));
		// $('#montodolares').val(number_format(monto_dolares,2,'.',''));
	}
 }
 
function reordenarItem(layout){
	layout = layout || ".detalle_body";
	
	$("td span.badge", layout).each(function(x,y){
		$(this).html(x+1);
	})
}

function Filtrar_proveedor(estado_letra){
	estado_letra = estado_letra || 'N';
	
	str = 'cancelado=' + estado_letra;
	ajax.post({url: _base_url+"pagoproveedor/buscar_proveedor_deuda",data:str}, function(res) {
		$("#idproveedor").empty();
		html='<option value="">[TODOS]</option>';
		$(res).each(function(i,j){
			html+="<option value='"+j.idproveedor+"'>"+j.proveedor+"</option>";
		});
		$("#idproveedor").html(html);
		$('#idproveedor').trigger("chosen:updated");
	});
}

$(document).ready(function(){
	$("#estado_letra").trigger("change");
});