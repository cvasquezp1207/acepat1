var form = {
	nuevo: function() {
		
	},
	editar: function(id) {
		
	},
	guardar: function() {
		var data = $("#form-data").serialize();
		$.each(arr, function(i, val) {
			if($("#"+val).is(':checked'))
				data += "&" + val + "=I";
			else
				data += "&" + val + "=A";
		});
		
		model.save(data, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				redirect(_controller);
			});
		});
	},
}
var arr = ["estado"];
$("#orden").numero_entero();
$(document).ready(function(){
	LoadBoton($("#idmodulo").val());
	$("#idsistema").trigger("change");
});

$("#idsistema").change(function(e){
	reload_padre($("#id_padre").val());
});

$("a.select_icon").click(function(e) {
	var icon	= $(this).data("icon");
	$name_modal = 'form-data';
	
	setIcon(icon,$name_modal);
	$("#icono").val("fa-"+icon);
});

$(document).on('click','.delete_boton',function(){
	$tr_ = $(this).parent('td').parent('tr');
	$tr_.remove();
	LoadBoton('');
});

/* Nuevo sistema */
$("#btn_sistema").click(function(e){
	$("#modal-sistema").modal('show');
});

/* Nuevo boton */
$("#btn-registrar-boton").click(function(e){
	$("#modal-boton").modal('show');
});

$('#form-data #btn_save').bind('click',function(e){
	e.preventDefault();
	s = true;
	s = s && $('#descripcion').required();
	s = s && $('#orden').required();
	s = s && $('#icono').required();
		
	if(s){
		form.guardar();
	}
});

// $("#btn_orden").click(function() {
	// redirect(_controller+"/ordenar_item");
	// return false;
// });

$("#btn_cancel").click(function() {
	redirect(_controller);
	return false;
});

//Refrescar combo padre
$("#btn_padre").click(function(e){
	reload_padre();
});

$(document).on('click','a.here_boton',function(){
	$id_boton = $(this).parent('li').attr('data-value');
	if($id_boton)
		AddDetalleBoton($id_boton,$(this).text(),$(this).attr('data-icon'),'');
});

/* Ordenar Modulos */
$("#btn_orden").click(function(e){
	e.preventDefault();
	cargar_sistema();
	$("#modal-order").modal("show");
});

/* Cargar los modulos por sistema */
$("#codsistema_order").change(function(e){
	if($.trim($(this).val())!='0')
		cargar_padre();
	else
		$("#codpadre_order").html("<option value='0'>Seleccione...</option>");
});

/* cargar modulo para ordenar */
$("#codpadre_order").change(function(e){
	if($.trim($(this).val())!='0')
		cargar_modulos_orden();
	else
		$("#list_modulos").empty();
});

$("#btn_sistema_order").click(function(e){
	cargar_sistema();
});

$("#btn_save_order").click(function(e){
	e.preventDefault();
	s = true && $("#codsistema_order").required();
	s = s && $("#codpadre_order").required();
	if(s){
		var str = $("#form-order").serialize();

		$("#list_modulos li").each(function(x,y){
			str+= "&idmodulo[]="+$(this).attr("index-key");
		});

		ajax.post({url: _base_url+"modulo/save_order",data:str}, function(rpt) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Los modulos se ordenaron correctamente", tipo:"success"}, function() {//Error en ventana
				$("#modal-order").modal("hide");
				grilla.reload(_default_grilla);
			});
		});
	}
});

function reload_padre(cod_padre){
	cod_padre = cod_padre|| '';

	ajax.post({url: _base_url+_controller+"/get_all_padre", data: "idsistema="+$("#idsistema").val()}, function(res) {
		html = "<option value=''>Seleccione...</option>";
		$(res).each(function(i,j){
			selected = '';
			if(cod_padre==j.idpadre)
				selected = 'selected';
			html+="<option value='"+j.idpadre+"' "+selected+">"+j.padre+"</option>";
		});

		$("#idpadre").html(html);		
	});
}

function LoadBoton($idmodulo){
	ajax.post({url: _base_url+"modulo/ListBotones",data:$("#form-data").serialize()}, function(res) {
		if(res){
			$("#idboton_sel").html(res);
			
			if($idmodulo!=''){
				LoadDetalleBoton();
			}
		}else
			$("#idboton_sel").empty();
	});
}

function LoadDetalleBoton(){
	ajax.post({url: _base_url+"modulo/ListDetalleBoton",data:$("#form-data").serialize()}, function(rpt) {
		if(rpt){
			$("#detalle_boton").html(rpt);
			LoadBoton('');
		}else
			$("#detalle_boton").empty();
	});
}

function setIcon(icon,modal){
	$("#"+modal+" #icono_preview").html('<i class="fa fa-'+icon+'"></i>');
}

function AddDetalleBoton($id,$boton,$icon,$data){
	$html = '<tr>';
	$html+= '	<td style="padding:3px;">'+'<button style="width:100%;text-align:left;" type="button" class="btn fa '+$icon+'" >&nbsp;&nbsp;'+$boton+'</button>'+'</td>';
	$html+= '	<td style="padding:3px;">';
	$html+= '		<button type="button" class="btn delete_boton btn-danger fa fa-times"></button>';
	$html+= '		<input type="hidden" name="idboton[]" value="'+$id+'">';
	$html+= '	</td>';
	$html+= '</tr>';
	$("#detalle_boton").append($html);
	LoadBoton( '' );
}

function cargar_sistema(){
	ajax.post({url: _base_url+"sistema/get_all",data:''}, function(res) {
		html = "<option value=''>Seleccione...</option>";
		$(res).each(function(i,j){
			selected = '';

			html+="<option value='"+j.idsistema+"' "+selected+">"+j.sistema+"</option>";
		});

		$("#codsistema_order").html(html);
		$("#codsistema_order").trigger("change");
	});
}

function cargar_padre(){
	ajax.post({url: _base_url+"modulo/get_all_padre",data:'idsistema='+$("#codsistema_order").val()}, function(res) {
		html = "<option value=''>Seleccione...</option>";
		$(res).each(function(i,j){
			selected = '';

			html+="<option value='"+j.idpadre+"' "+selected+">"+j.padre+"</option>";
		});

		$("#codpadre_order").html(html);
		$("#codpadre_order").trigger("change");
	});
}

function cargar_modulos_orden(){
	ajax.post({url: _base_url+"modulo/modulos_order",data:'idpadre='+$("#codpadre_order").val()}, function(res) {
		html = "";
		$(res).each(function(y,x){
			html+='<li class="ui-state-default" index-key="'+x.idmodulo+'"><span class="badge badge-primary">'+x.orden+'</span> '+x.modulos+'</li>';
		});
		$("#list_modulos").html(html);
		$("#list_modulos" ).sortable({
			placeholder: "ui-state-highlight"
		});
	
		$( "#list_modulos" ).disableSelection();
	});
}