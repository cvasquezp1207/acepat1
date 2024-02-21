$("#cambio_moneda,#total_pen,#total_pagar,#total_importe_pagar,#total_acumulado").numero_real();
$("#search").autocomplete({
	source: function( request, response ) {
		ajax.post({url: _base_url+"cuentas_pagar/proveedor_deuda", 
		data: "m=50&q="+request.term+"&f="+$("#filter").val()+"&pagado="+$("#pagado").val(), dataType: 'json'}, function(data) {
			response(data);
		});
	}
	,appendTo: $("#search").closest("div")
	,select: function( event, ui ) {
		if(ui.item) {
			$("#credito_idproveedor").val(ui.item.idproveedor)
			cargar_datos($("#credito_idproveedor").val());
		}
	}
});

$("#pagado").change(function(e){
	cargar_datos($("#credito_idproveedor").val());
});

/* Para la primera version de creditos individuales */
// $(document).on("click","#table-creditos tbody tr",function(){
	// $("#table-creditos tbody tr").removeClass('active');
	// $(this).addClass('active');
	
	// cargar_comprobante();
// });

$("#id_creditos").change(function(e){
	cargar_comprobante();
})

$(document).on("click",".idletra",function(e){
	if($(this).is(":checked")){
		$(this).parent("div").parent("td").parent("tr").next('tr').find(".checkbox-primary").show();
		$(this).parent("div").parent("td").parent("tr").find(".fecha_vencimiento").datepicker({
			todayBtn: "linked",
			keyboardNavigation: false,
			forceParse: false,
			autoclose: true,
			language: 'es'
		}).on("changeDate",function(e){
			e.preventDefault();
			calculo_dia_forma($(this));
		});
		$(this).parent("div").parent("td").parent("tr").find(".fecha_vencimiento").prop('readonly',false);
	}else{
		posicion = $(this).parent("div").parent("td").parent("tr").next('tr').index();
		$(this).parent("div").parent("td").parent("tr").find(".fecha_vencimiento").prop('readonly',true);
		$(this).parent("div").parent("td").parent("tr").find(".fecha_vencimiento").datepicker('remove');
		if(posicion>0){
			x_pos   = posicion + 1;
			tr_cant = $("#table-letras tbody tr").length;

			$("#table-letras tbody tr").each(function(i,j){
				y_pos = $(j).index() + 1;
				if(y_pos>=x_pos){
					$(j).find(".idletra").prop("checked",false);
					$(j).find(".fecha_vencimiento").datepicker('remove');
					$(j).find(".fecha_vencimiento").prop('readonly',true);
					$(j).find(".checkbox-primary").hide();			
				}
			});
		}
	}
	calcular_monto();
});

$(document).on("click",".btn-del-amortizacion",function(e){
	e.preventDefault();
	
			tr_ = $(this).parent("span").parent("td").parent("tr");
			id_letra = tr_.attr("key-letra");
			serie_d  = $(this).attr("key-serie");
			numro_d  = $(this).attr("key-numero");
			var str = "serie="+serie_d;
				str+= "&numero="+numro_d;
				str+= "&iddeuda="+$("#table-creditos tbody tr.active").attr("index");
				
	ventana.confirm({
		titulo:"Confirmar"
		,mensaje:"Esta seguro que desea eliminar el Pago de la letra seleccinado "
		,textoBotonAceptar: "Si quiero ;)"
	}, function(ok) {
		if(ok) {
			ajax.post({url: _base_url+"cuentas_pagar/delete_pago", data:str}, function(data) {
				if(data){
					ventana.alert({titulo: "En horabuena!", mensaje: "Se ah eliminado el pago correctamente.", tipo:"success"}, function() {
						// $("#table-creditos tbody tr.active").trigger("click");
						cargar_datos($("#credito_idproveedor").val());
					});
				}
			});
		}
	});
	
});

$("#idmoneda").change(function(e){
	if($.isNumeric($(this).val())) {
		ajax.post({url: _base_url+"moneda/get/"+$(this).val()}, function(data) {
			$("#cambio_moneda").val(parseFloat(data.valor_cambio).toFixed(2));
			$(".pagar_con").html(data.abreviatura);
		});
		$("#total_pagar").val("");
		return;
	}
	$("#cambio_moneda").val("");
});

$("#idmoneda_deuda").change(function(e){
	$("#id_creditos").empty();
	cargar_datos($("#credito_idproveedor").val(),'N');
});

$("#guardar_pago").click(function(e){
	e.preventDefault();
	
	if($("#table-creditos tbody tr.active").attr("index")!=''){
		if($("#table-letras tbody tr").length){
			if($(".idletra").is(":checked")){
				if($("#total_pagar").required()){
					pay.setMonto($("#total_pagar").val());
					$(".monto_entregado").val($("#total_pagar").val());
					$(".monto_entregado").trigger("keyup");
					
					// $(".idcuentas_bancarias").trigger("change");
					
					$(".idcuentas_bancarias option[data-idmoneda!='"+$("#idmoneda").val()+"']").hide();
					$(".idcuentas_bancarias option[data-idmoneda='"+$("#idmoneda").val()+"']").show();
					$(".idcuentas_bancarias option:visible").first().prop("selected", true);
					
					setTimeout(function(){
						$(".monto_entregado").focus();
					},800);
					pay.ok(function(datos) {
						var str = $("#form-letras").serialize();
						str+= "&iddeuda="+$("#table-creditos tbody tr.active").attr("index");
						str+="&"+datos;
						str+="&referencia="+$("#proveedor").val();
						ajax.post({url: _base_url+"cuentas_pagar/guardar_pago", data:str}, function(data) {
							if(data){
								ventana.alert({titulo: "En horabuena!", mensaje: "Pago realizado correctamente.", tipo:"success"}, function() {
									cargar_datos($("#credito_idproveedor").val());
								});
							}
						});
					});
					pay.show();
					
				}
			}else{
				ventana.alert({titulo: "", mensaje: "Debe seleccionar una o mas letras para pagar", tipo: "warning"});
			}
		}else{
		}
	}else{
		ventana.alert({titulo: "", mensaje: "Debe seleccionar un credito", tipo: "warning"});
	}
});

$("#idmoneda").trigger("change");

function cargar_datos(id, reload_all){
	reload_all = reload_all || 'S';
	
	if(reload_all=='S')
		clear_input();
	if( $.trim(id)!='' ){
		var str = "idproveedor="+id;
			str+= "&pagado="+$("#pagado").val();
			str+= "&idmoneda="+$("#idmoneda_deuda").val();
		ajax.post({url: _base_url+"cuentas_pagar/get_credito", data:str}, function(data) {
			if(data){
				if(data.proveedor && reload_all=='S'){
					$("#proveedor").val(data.proveedor.nombre);
					$("#ruc").val(data.proveedor.ruc);
				}
				if(data.deuda){
					html = "";
					combo = "";
					$(data.deuda).each(function(x,y){
						cls			= '';
						selected	= '';
						if(x==0){
							cls = 'active';
							selected = 'selected';
						}
						// html+="<tr index='"+y['iddeuda']+"' key-pagado='"+y['pagado']+"' class='"+cls+"'><td>"+y['nro_credito']+"</td></tr>";
						combo+="<option value='"+y['iddeuda']+"' "+selected+" >"+y['nro_credito']+"</option>";
					});
					// $("#table-creditos tbody").html(html);
					$("#id_creditos").html(combo);
					
					cargar_comprobante();
				}
			}
		});
	}
}

function cargar_comprobante(){
	// if($("#table-creditos tbody tr").hasClass("active")){
		// var str = "iddeuda="+$("#table-creditos tbody tr.active").attr("index");
			// str+= "&pagado="+$("#pagado").val();
		// ajax.post({url: _base_url+"cuentas_pagar/get_comprobante", data:str}, function(data) {
			// html = "";
			// $(data.comprobante).each(function(x,y){
				// html+="<option value='"+y['idcompra']+"'>"+y['comprobante'];
			// });
			// $("#id_compras").html(html);
			// if(data.deuda){
				// $("#monto_deuda").val(parseFloat(data.deuda.monto_pendiente).toFixed(2));
				// $("#idmoneda").val(data.deuda.idmoneda);
				// $("#cambio_moneda").val(data.deuda.valor_cambio);
			// }else{
				// $("#monto_deuda").val('0.00');
				// $("#idmoneda,#cambio_moneda").val('');
			// }
			
			// $("#idmoneda").trigger("change");
		// });
		// cargar_letras();
	// }
	
	// var str = "iddeuda[]="+$.trim($("#id_creditos").val());
		// str+= "&pagado="+$("#pagado").val();
		// str+= "&idmoneda="+$("#idmoneda_deuda").val();
	var str = $("#form-letras").serialize();
		
		ajax.post({url: _base_url+"cuentas_pagar/get_comprobante_multiple", data:str}, function(data) {
			html = "";
			$(data.comprobante).each(function(x,y){
				html+="<option value='"+y['idcompra']+"'>"+y['comprobante'];
			});
			$("#id_compras").html(html);
			
			if(data.deuda){
				$("#monto_deuda").val(parseFloat(data.deuda.monto_pendiente).toFixed(2));
				$("#idmoneda").val(data.deuda.idmoneda);
				$("#cambio_moneda").val(data.deuda.valor_cambio);
			}else{
				$("#monto_deuda").val('0.00');
				$("#idmoneda,#cambio_moneda").val('');
			}
			
			$("#idmoneda").trigger("change");
		});
		
		cargar_letras();
}

function cargar_letras(){
	// var str = "iddeuda="+$("#table-creditos tbody tr.active").attr("index");
	// var str = "iddeuda[]="+$.trim($("#id_creditos").val());
		// str+= "&pagado="+$("#pagado").val();
		// str+= "&idmoneda="+$("#idmoneda_deuda").val();
	var str = $("#form-letras").serialize();
		ajax.post({url: _base_url+"cuentas_pagar/get_letras", data:str}, function(data) {
			var table="";
			var next_point	= true;
			var is_pagado	= $("#table-creditos tbody tr.active").attr("key-pagado");
			var paga_en		= '';
			$(data).each(function(x,y){
				paga_en = y["moneda"];
				content_check = "<div class='checkbox checkbox-primary' title='Seleccione la letra sólo si lo pagará' ";
				if(y['pagado']=='N' && next_point){
					next_point = false;
				}else{
					content_check+=" style='display:none;' ";
				}
				content_check+="><input type='checkbox' name='idletra[]' class='idletra' value='"+y['idletra']+"'><label></label></div>";
				
				content_td = "";
				if(y['pagado']=='S' && y['last_pago']){//Falta verificar el ultimo pago realizado
					content_td+= "<span class='pull-right'>"+"<button class='btn btn-white btn-xs btn-del-amortizacion' key-serie='"+y['serie']+"' key-numero='"+y['numero']+"' data-toggle='tooltip' title='Eliminar Pago'><i class='fa fa-trash'></i></button>"+"</span>";
				}
				
				// table+="<tr index='"+y['idletra']+"'>";
				table+="<tr key-letra='"+y['idletra']+"'>";
				table+="	<td>"+content_check+"</td>";
				table+="	<td>"+y['nro_credito']+"</td>";
				table+="	<td class='text-center'>"+y['nro_letra']+"</td>";
				table+="	<td class='text-center'>"+y['fecha_credito']+"</td>";
				table+="	<td class='text-center'><input type='text' readonly name='fecha_vencimiento[]' class='form-control input-xs fecha_vencimiento' value='"+y['fecha_venc']+"' >";"</td>";
				table+="	<td class='text-center'>"+y['fecha_pago']+"</td>";
				table+="	<td class='text-center'>"+y['moneda']+"</td>";
				table+="	<td class='text-center'>"+y['tipopago']+"</td>";
				table+="	<td class='text-number'>"+y['monto_capital']+"</td>";
				table+="	<td class='' style='display:none;'>";
				table+="		<input value='"+$.trim(y['idforma_pago_compra'])+"' class='idforma_pago_compra' name='idforma_pago_compra[]' >";
				table+="		<input value='"+$.trim(y['nro_dias_formapago'])+"' class='nro_dias_formapago' name='nro_dias_formapago[]' >";
				table+="		<input value='"+$.trim(y['id_referencia'])+"' class='id_referencia' name='id_referencia[]' >";
				table+="	</td>";
				table+="	<td>"+content_td+"</td>";
				table+="</tr>";
			});
			$(".deuda_en").html(paga_en);
			$("#table-letras tbody").html(table);
			$('.btn-del-amortizacion').tooltip(); 
			
			calcular_monto();
		});
}

function calcular_monto(){
	var monto_p = 0;
	$(".idletra").each(function(i,j){
		if($(this).is(":checked")){
			monto_p+=parseFloat($(this).parent("div").parent("td").parent("tr").find("td:eq(8)").text());
		}
	});
	// $("#total_pagar,#total_pen,#total_importe_pagar,#total_acumulado").val(parseFloat(monto_p).toFixed(2));
	$("#total_importe_pagar,#total_acumulado").val(parseFloat(monto_p).toFixed(2));
	$("#total_pagar").val("");
}

function calculo_dia_forma(input){
	var f_input = input.val();
	var f_credt = input.parent("td").parent("tr").find("td:eq(3)").text();
	
	var dias = resta_Fechas(f_credt, f_input);
	
	input.parent("td").parent("tr").find(".nro_dias_formapago").attr('value',dias);
}

function clear_input(){
	$("#table-creditos tbody").empty();
	$("#table-letras tbody").empty();
	$("#id_creditos").empty();
	$("#id_compras").empty();
	$("#proveedor,#ruc,#monto_deuda,#total_pagar,#total_importe_pagar,#total_acumulado").val('');
	$(".deuda_en").html("??");
}