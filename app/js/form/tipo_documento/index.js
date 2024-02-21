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
		grilla.set_where(_default_grilla, "idperfil", "=", "1");
		grilla.reload(_default_grilla);
		// var id = grilla.get_id(_default_grilla);
		// if(id != null) {
			// alert(id);
		// }
	},
	guardar: function() {
		// algunas validaciones aqui
		// var data = $("#form_perfil").serialize();
		var data = $("#form_"+_controller).serialize();
		
		var arr = ["mostrar_en_compra","mostrar_en_venta","mostrar_en_recibos",
			"mostrar_en_recibo","genera_correlativo","facturacion_electronica","mostrar_en_cobranzas","dni_obligatorio","ruc_obligatorio"];
		
		$.each(arr, function(i, val) {
			if($("#"+val).is(':checked'))
				data += "&" + val + "=S";
			else
				data += "&" + val + "=N";
		});
		
        band = false;
        if ($("#idtipodocumento").val != '') {//Estoy editando
        	$('#tabla_correlativo tbody tr').each(function(){
				band = true;
			});
        	//band = true;
        }else{
        	band = true;
        }

        if(band) {
			model.save(data, function(res) {
				ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
					redirect(_controller);
				});
			});        	
        }
		else{
        	ventana.alert({titulo: "Hey..!", mensaje: "Debe Ingresar un correlativo al detalle", tipo:"warning"}, function() {
				$("#serie").focus();
			});
        }
	},
	cancelar: function() {
		
	}
};

validate();

$("#descripcion").focus();
$("#codsunat").numero_entero();

if ($.trim($("#idtipodocumento").val())!='') {
	Editar_correlativo();
}

///////////////////////////// EVENTOS PARA LOS CORRELATIVOS //////////////////	
	$("#descripcion").keyup(function(){
		$("#tipodoc").val($(this).val());
	});

	$("#idsucursal").change(function(){
		if( $(this).val() != '' ){
			Editar_correlativo();
		}else{
			ventana.alert({titulo: 'Error', mensaje: 'Seleccione una Sucursal'});
		}
	});
	
	$("#addCorrelativo").click(function(){
		if($("#serie").required()){
			var s = String($("#serie").val()).replace(/\W+/g, "");
			s = s.toUpperCase();
			
			var array = [];
			var data = {
				serie: s
				,correlativo: '1'
				,coddetalle_tipodocumento: ''
			};
			
			array.push(data);
			
			band = true;
			$('.serie').each(function(){
				if( $(this).val() == s ){
					band =false;
				}
			});
			
			if(band){
				ArmarDetalle(array);
			}else{
				ventana.alert({titulo: 'Error', mensaje: 'La serie ya fue agregada al detalle'},function(){
					$("#serie").focus();
				});
			}
		}
	});
	
	$(document).on('click',".delete_correlativo",function(e){
		$(this).closest('tr').remove();
	});
function Editar_correlativo(){
    $('#tabla_correlativo tbody').empty();
	//$("#form-data-correlativo input").val('');

	str= 'id='+$("#idtipodocumento").val() + "&idsucursal="+$("#idsucursal").val(); // string

	ajax.post({url: _base_url+$("#controlador").val()+"/getCorrelativo", data: str}, function(res) {
		ArmarDetalle(res.detalle);
	});
}

function ArmarDetalle(rows){
	if(rows.length) {
        var data = null, tr = null, html = '';

        for(var i in rows) {
            row = i + 1;
            data = rows[i];
                        
            tr = $("<tr></tr>");
            
            html   = "<td>"+'<input name="serie[]" readonly="readonly" value="'+data.serie+'" class="form-control serie">'+"</td>";
            html += "<td>";
			html += '	<div class style="">';
			html += '		<div class="input-group">';
			html += '			<input name="correlativo[]"  value="'+data.correlativo+'" class="form-control numero" maxlength="10">';
			html += '			<span class="input-group-btn tooltip-demo">';
			html += '				<button type="button" class="btn btn-outline btn-primary delete_correlativo" data-toggle="tooltip" title="Borrar Correlativo">';
			html += '					<i class="fa fa-trash-o"></i>';
			html += '				</button>';
			html += '			</span>';
			html += '		</div>';
			html += '	</div>';
			html +="</td>";
            //html += "<td style='display:none'>" + "<input type='text' name='coddetalle_tipodocumento[]' value='"+data.coddetalle_tipodocumento+"'  /> </td>";
            
            tr.html(html);
            
            $('#tabla_correlativo tbody').append(tr);
        }
    }else{
		$('#tabla_correlativo tbody').empty();
	}
}
///////////////////////////// EVENTOS PARA LOS CORRELATIVOS //////////////////