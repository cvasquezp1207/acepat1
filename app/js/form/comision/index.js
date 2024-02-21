$("#idempresa").on("change", function() {
	reload_combo("#idsucursal", {controller:"sucursal", data:"idempresa="+$(this).val()}, function() {
		$("#idsucursal").trigger("change");
	});
});

$("#idsucursal").on("change", function() {
	reload_combo("#anio", {controller:"comision", method:"get_anios", data:"idsucursal="+$(this).val()});
	reload_combo("#idempleado", {controller:"comision", method:"get_empleados", data:"idsucursal="+$(this).val()}, function() {
		$("#idempleado").trigger("chosen:updated");
	});
});

$("#idempleado").chosen();

$("#btn-listar").on("click", function(e) {
	e.preventDefault();
	if($("#idempresa").required()) {
		verificarEstado();
		reloadTabla();
	}
});

function verificarEstado() {
	if($("#idsucursal").required() && $("#anio").required() && $("#mes").required()) {
		$("#msg-info-mes").html("");
		$("#msg-param-comision").html("");
		
		var str = $("#frm-filtros").serialize();
		
		ajax.post({url: _base_url+"comision/get_estado", data: str}, function(res) {
			$("#msg-info-mes").html(res.info);
			$("#msg-param-comision").html(res.tabla);
		});
	}
}
function reloadTabla() {
if($("#idsucursal").required() && $("#anio").required() && $("#mes").required()) {
		$("#msg-info-mes").html("");
		$("#msg-param-comision").html("");
		$("#table-letras tbody").empty();
		
		var str = $("#frm-filtros").serialize()+"&idsucursal="+$("#idsucursal").val();
		
		ajax.post({url: _base_url+"comision/get_tabladet", data: str}, function(res) {
			if(res){
				var html ='';
				$(res.detalle_pagos).each(function(i,j){
					html+="<tr>";
					html+="	<td><input name='fecha_venta[]' class='form-control input-xs' value='"+j.fecha_venta+"' readonly=true /></td>";
					html+="	<td><input name='comprobante[]' class='form-control input-xs' value='"+j.comprobante+"' readonly=true /></td>";
					html+="	<td align = 'right'><input name='totventa[]' class='form-control input-xs' value=' "+j.totventa+"' readonly=true /></td>";
					html+="	<td><input name='vendedor[]' class='form-control input-xs' value='"+j.vendedor+"' readonly=true /></td>";
					html+="	<td align = 'right'><input name='monto[]' class='form-control input-xs' value='"+j.monto+"' readonly=true /></td>";
					html+="	<td><input name='fecha_venta[]' class='form-control input-xs' value='"+j.fecha_venta+"'readonly=true /></td>";
					html+="	<td align = 'right'><input name='nrodias[]' class='form-control input-xs' value='"+j.nrodias+"' readonly=true /></td>";
					html+="	<td align = 'right'><input name='porcentaje[]' class='form-control input-xs' value='"+j.porcentaje+"' readonly= true/></td>";
					html+="	<td align = 'right'><input name='comision[]' class='form-control input-xs' value='"+j.comision+"' readonly=true /></td>";
					html+="	<td style='display:none;'>";
					html+=" 	<input type='text' name='idvendedor[]' value='"+j.idvendedor+"'>";
					html+=" </td>";
					// /* continua las delas columnas, copia y pega no mas */
					
					html+="</tr>";
				});
				$("#table-letras tbody").html(html);
			}
		});

		// aqui jalar los registros, mostrar boton [Guardar y Cerrar]
	}	
}
$("#btn-save-comision").on("click", function(e) {
	e.preventDefault();
	if($("#table-letras tbody tr").length>0){
		var str = $("#form-letras").serialize()
		str+="&idempresa="+$("#idempresa").val();
		str+="&idsucursal="+$("#idsucursal").val();
		str+="&anio="+$("#anio").val();
		str+="&mes="+$("#anio").val();
		
		ajax.post({url: _base_url+"comision/save_comision", data: str}, function(res) {
		});
	}else{
		// Aqui alerta, debe generar las comisiones, o algo asi
	}
	
});

(function() {
	verificarEstado();
})();