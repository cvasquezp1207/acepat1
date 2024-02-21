// FUNCIONES PARA LA GRILLA
var grilla = function() {
	/**
	 * Obtener el ID real de la grilla
	 */
	function _get_real_id(idobject) {
		if( $("table[realid='"+idobject+"']").length == 1 ) {
			var table = "dt"+idobject;

			if($("table[realid='"+idobject+"']").hasClass("grilla_popup")) {
				table += "_popup";
			}

			var realId = "#"+table;

			// if( $(realId).hasClass('dataTable') && $(realId).attr('role') == 'grid' && $(realId).prop('tagName') == 'TABLE' ) {
			if ( $.fn.DataTable.isDataTable( realId ) ) {
				return table;
			}
			else {
				console.log(idobject + " podria no ser una instancia de DataTables");
			}
		}
		else {
			console.log("El objeto DOM no existe o existe mas de una instancia para " + idobject);
		}

		return false;
	}

	/**
	 * Metodo para recargar la grilla
	 * @param String idobject ID del objeto DOM de la tabla grilla.
	 * @access public
	 */
    function reload(idobject) {
		var table = _get_real_id(idobject);

		if( table !== false ) {
			var script = "oTable_" + table + ".fnDraw();";
			eval(script);
		}
    }

	/**
	 * Metodo para indicar los filtros sobre la grilla
	 */
	function set_filter(idobject, columna, simbolo, valor) {
		var table = _get_real_id(idobject);
		
		var aoServerParams, aoDataTemp = [], i, j, k, where = [], 
			existCol = false, existWhere = false, existParam = false;
		
		eval("aoServerParams = oTable_" + table + ".fnSettings().aoServerParams;");
		
		// verificamos si existe algun ServerParams
		if(aoServerParams.length) {
			// recorremos la lista de serverParams
			for(i in aoServerParams) {
				aoDataTemp = [];
				aoServerParams[i].fn(aoDataTemp);
				
				if(aoDataTemp.length) {
					for(j in aoDataTemp) {
						if(aoDataTemp[j].name == "where") {
							// obtenemos el where
							where = JSON.parse(aoDataTemp[j].value);
							if(where.length) {
								for(k in where) {
									// existe el filtro que deseamos?, solo modificamos datos
									if(where[k].column == columna) {
										where[k].simbol = simbolo;
										where[k].value = valor;
										
										existCol = true;
										break;
									}
								}
							}
							
							// si no existe el filtro, agregamos el filtro
							if( ! existCol) {
								where.push({column: columna, simbol: simbolo, value: valor});
							}
							
							// reemplazamos el nuevo where
							aoDataTemp[j].value = JSON.stringify(where);
							
							existWhere = true; // indicamos que hemos encontrado el where
							break;
						}
					}
				}
				
				if(existWhere) {
					aoServerParams[i].fn = function(arr) {
						for(var i in aoDataTemp) {
							arr.push(aoDataTemp[i]);
						}
					};
					
					existParam = true;
					break;
				}
			}
		}
		
		if( ! existParam) {
			aoServerParams.push({
				"fn": function(arr) {
					arr.push( {name: "where", value: JSON.stringify( [{column: columna, simbol: simbolo, value: valor}] )} );
				},
				"sName": "user"
			});
		}
		
		eval("oTable_" + table + ".fnSettings().aoServerParams = aoServerParams;");
	}
	
	/**
	 * Metodo para eliminar los filtros de la grilla
	 */
	function del_filter(idobject, columna) {
		var table = _get_real_id(idobject);
		
		var aoServerParams, aoDataTemp, i, j, k, where, exists = false;
		
		eval("aoServerParams = oTable_" + table + ".fnSettings().aoServerParams;");
		
		if(aoServerParams.length) {
			for(i in aoServerParams) {
				aoDataTemp = [];
				aoServerParams[i].fn(aoDataTemp);
				
				if(aoDataTemp.length) {
					for(j in aoDataTemp) {
						if(aoDataTemp[j].name == "where") {
							// obtenemos el where
							where = JSON.parse(aoDataTemp[j].value);
							if(where.length) {
								for(k in where) {
									if(where[k].column == columna) {
										where.splice(k, 1); // eliminamos el filtro
										
										// reemplazamos el nuevo where
										aoDataTemp[j].value = JSON.stringify(where);
										
										// reescribimos funcion
										aoServerParams[i].fn = function(arr) {
											for(var i in aoDataTemp) {
												arr.push(aoDataTemp[i]);
											}
										};
										
										exists = true;
										break;
									}
								}
							}
						}
						
						if(exists) {break;}
					}
				}
				
				if(exists) {break;}
			}
		}
		
		if(exists) {
			eval("oTable_" + table + ".fnSettings().aoServerParams = aoServerParams;");
		}
	}

	function _escape_val(val) {
		if($.isNumeric(val)) {
			if(val.indexOf(".") != -1) {
				return parseFloat(val);
			}
			else {
				return parseInt(val);
			}
		}

		return val;
	}
	
	function isNumeric(columns, idobject) {
		if( isArray(columns) ) {
			if(columns.length > 0) {
				var table = idobject || '#grilla';
				var col = 0, style = "<style>";
				for(var i in columns) {
					// col = parseInt(columns[i]) - 1;
					col = parseInt(columns[i]);
					if(!isNaN(col)) {
						style += table + " tbody tr td:nth-child("+col+"),";
					}
				}
				if(style.length > 1) {
					style = style.substring(0, (style.length - 1));
					style += "{text-align: right;}</style>";
					$("body").append(style);
				}
			}
		}
	}
	
	function get_data(idobject, iRow) {
		var table = _get_real_id(idobject);
		
		if( table !== false ) {
			var script = "";
			if(iRow) {
				script = "data = oTable_"+table+".fnGetData(iRow);";
			}
			else if($("tr.active[role='row']", "#"+table).length) {
				script = "data = oTable_"+table+".fnGetData(oTable_"+table+".$(\"tr.active[role='row']\"));";
			}
			else if($("tr.DTTT_selected[role='row']", "#"+table).length) {
				script = "data = oTable_"+table+".fnGetData(oTable_"+table+".$(\"tr.DTTT_selected[role='row']\"));";
			}
			
			if(script != "") {
				var data = null;
				eval(script);
				return data;
			}
		}
		return null;
	}
	
	function get_id(idobject, iRow) {
		var row = get_data(idobject, iRow);
		if(row != null) {
			return row["pkey"];
		}
		return null;
	}
	
    function search(idobject, input, regex, smart) {
		var table = _get_real_id(idobject);

		if( table !== false ) {
			// var script = "oTable_" + table + ".search(input, regex, smart).fnDraw();";
			// var script = "oTable_" + table + ".fnSearch(input, regex, smart).fnDraw();";
			var script = "oTable_" + table + ".fnFilter(input, regex, smart);";
			eval(script);
		}
    }

    return {
		reload: reload, 
		get_id: get_id, 
		get_data: get_data, 
		set_filter: set_filter, 
		del_filter: del_filter, 
		search: search
	};
}();

// PARA LAS PETICIONES AJAX
var ajax = function() {
    var _typeReturn = ['json', 'jsonp'];
    var _dataTypeDefault = 'json';
    var _responseTypeDefault = 'ajax';

    var propertiesDefault = {
        selector: ''
        ,url: './index.php'
        ,data: ''
        ,dataType: 'json' // xml, json, script, text, html
		,responseType: 'ajax' // ajax, html
        ,showErrors: true
        // ,security: true
    };

	function is_object(mixed_var) {
		if (Object.prototype.toString.call(mixed_var) === '[object Array]') {
			return false;
		}
		return mixed_var !== null && typeof mixed_var === 'object';
	}

    function _prepareData(data, properties) {
        var str = 'response=' + properties.responseType + '&type=' + properties.dataType;

		// if(!properties.security) {
			// str += '&security=1';
		// }

		// if(typeof _getKeyCode == "function") {
			// str += '&im=' + _getKeyCode();
		// }

		if(is_object(data)) {
			str += '&' + $.param(data);
        }
        else {
			str += '&' + String(data);
        }
        return str;
    }

	function in_array(_string, _array) {
		return (_array.indexOf(_string) != -1);
	}

    function _response(valores, response, callback) {
		if($.isFunction(callback)) {
            if(in_array(valores.dataType, _typeReturn)) {
                if(response.code == 'OK') {
                    callback(response.data);
                }
                else {
					// if(valores.showErrors)
						ventana.alert({titulo: 'Error', mensaje: response.message, tipo: 'error'},function(){
							
						});
						setTimeout(function(){
							$(":button.confirm").get(0).focus();
							console.log($(".confirm"));
						},1000)
                }
				// if(response.operation) {
					// if(typeof _setKeyCode == "function") {
						// _setKeyCode(response.operation);
					// }
				// }
            }
            else {
                callback(response);
            }
        }
		else {
			if(in_array(valores.dataType, _typeReturn)) {
				if(response.code != 'OK') {
					ventana.alert({titulo: 'Error', mensaje: response.message, tipo: 'error'});
				}
			}
		}
    }

    function enableError(isEnabled) {
        if(isEnabled) {
            $.ajaxSetup({
                error: function(xhr, error, thrown){
                    ventana.alert({titulo: 'Error', mensaje: '<b>'+xhr.statusText+'</b><br />'+xhr.responseText, tipo: 'error'});
                }
            });
        }
    }
	
	function defaults() {
		propertiesDefault.dataType = _dataTypeDefault;
		propertiesDefault.responseType = _responseTypeDefault;
	}

    function post(propiedades, callback) {
		defaults();
        var valores = $.extend({}, propertiesDefault, propiedades);
        valores.dataType = String(valores.dataType).toLowerCase();

        var data = _prepareData(valores.data, valores);

        enableError(valores.showErrors);

        $.post(valores.url, data, function(response) {
            _response(valores, response, callback);
        }, _dataTypeDefault);
    }

    function get(propiedades, callback) {
		defaults();
        var valores = $.extend({}, propertiesDefault, propiedades);
        valores.dataType = String(valores.dataType).toLowerCase();

        var data = _prepareData(valores.data, valores);

        enableError(valores.showErrors);

        $.get(valores.url, data, function(response) {
            _response(valores, response, callback);
        }, _dataTypeDefault);
    }

    function load(propiedades, callback) {
		defaults();
        var valores = $.extend({}, propertiesDefault, propiedades);

        if( $.trim(valores.selector) == '' ) {
            ventana.alert({titulo: 'Error', mensaje: 'No se ha indicado el selector del objeto DOM'});
            return;
        }

        if( $(valores.selector).length < 1 ) {
            ventana.alert({titulo: 'Error', mensaje: 'No existe el objeto DOM: ' + valores.selector});
            return;
        }

        var data = _prepareData(valores.data, valores);

        enableError(valores.showErrors);

        $(valores.selector).load(valores.url, data, function(response) {
			if($.isFunction(callback)) {
				callback(response);
			}
        });
    }

    return {post: post, get: get, load: load};
}();

/**
 * Abrir el formulario de cambio de sucursal
 */
function abrir_seleccion_sucursal() {
	if($("#accordion_sucursal a.list-group-item").length >= 1)//yo lo estoy poniendo mayor igual a uno
		$('#modal_inicio').modal('show');
	else 
		$("#accordion_sucursal a.list-group-item:first").trigger("click");
}

/**
 * Funcion para redireccionar a una url especifica
 */
var redirect = function(url) {
	document.location = _base_url+url;
}

/**
 * Funcion para redireccionar a una url especifica
 */
var open_url = function(url) {
	window.open(_base_url+url);
}

/**
 * Funcion para redireccionar a una url especifica en otra ventana
 */
var open_url_windows = function(url) {
	window.open(_base_url+url, "_blank");
}

/**
 * Funcion para redireccionar a una url especifica en un tab
 */
var open_url_tab = function(url, key, label, force) {
	if(typeof key == "undefined" || $.trim(key) == "")
		key = Math.round(Math.random()*10000);
	if(typeof label == "undefined")
		label = "untitle";
	if(typeof force == "undefined")
		force = false;
	
	// verificamos si estamos dentro del iframe
	if(window.frameElement != null && window.name != "") {
		window.parent.open_url_tab(url, key, label, force);
		return;
	}
	
	if(jIframe.init("#jiframe-ymenu")) {
		var ifr = "module_iframe_"+key;
		var h = jIframe.getHeight("#jiframe-ymenu");
		
		jIframe.add({
			label: label
			,content: "<iframe id='"+ifr+"' name='"+ifr+"' src='"+url+"' style='border:0;width:100%;min-height:"+h+"'></iframe>"
			,href: "ymtab-"+key
			,close: true
			,forceContent: force
		});
	}
	else {
		window.open(url);
	}
}

var close_tab = function(key) {
	if(typeof key == "undefined")
		return;
	
	if($.trim(key) == "")
		return;
	
	if(window.frameElement != null && window.name != "") {
		window.parent.close_tab(key);
		return;
	}
	
	if(jIframe.init("#jiframe-ymenu")) {
		var href = jIframe.clearfix("ymtab-"+key);
		$("ul.nav-tabs>li>a[href='#"+href+"']>span.close-tab", "#jiframe-ymenu_header").trigger("click");
	}
	else {
		window.close();
	}
}

/**
 * Funcion para verificar si una variable esta vacia
 */
function empty(mixed_var) {
	//  discuss at: http://phpjs.org/functions/empty/
	// original by: Philippe Baumann
	//    input by: Onno Marsman
	//    input by: LH
	//    input by: Stoyan Kyosev (http://www.svest.org/)
	// bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// improved by: Onno Marsman
	// improved by: Francesco
	// improved by: Marc Jansen
	// improved by: Rafal Kukawski
	//   example 1: empty(null);
	//   returns 1: true
	//   example 2: empty(undefined);
	//   returns 2: true
	//   example 3: empty([]);
	//   returns 3: true
	//   example 4: empty({});
	//   returns 4: true
	//   example 5: empty({'aFunc' : function () { alert('humpty'); } });
	//   returns 5: false

	if (typeof mixed_var === 'string') {
		mixed_var = mixed_var.trim();
	}

	var undef, key, i, len;
	var emptyValues = [undef, null, false, 0, '', '0'];

	for (i = 0, len = emptyValues.length; i < len; i++) {
		if (mixed_var === emptyValues[i]) {
			return true;
		}
	}

	if (typeof mixed_var === 'object') {
		for (key in mixed_var) {
			// TODO: should we check for own properties only?
			//if (mixed_var.hasOwnProperty(key)) {
			return false;
			//}
		}
		return true;
	}

	return false;
}

function isset() {
	//  discuss at: http://phpjs.org/functions/isset/
	// original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// improved by: FremyCompany
	// improved by: Onno Marsman
	// improved by: Rafał Kukawski
	//   example 1: isset( undefined, true);
	//   returns 1: false
	//   example 2: isset( 'Kevin van Zonneveld' );
	//   returns 2: true

	var a = arguments,
		l = a.length,
		i = 0,
		undef;

	if (l === 0) {
		throw new Error('Empty isset');
	}

	while (i !== l) {
		if (a[i] === undef || a[i] === null) {
			return false;
		}
		i++;
	}
	return true;
}

/**
 * Funciones para hacer algunas llamadas a  metodos de
 * algun controlador, las peticiones se hacen mediante ajax
 */
var model = function() {
	function get(id, callback, controller, method) {

	}

	function save(data, callback, controller, method) {
		controller = controller || _controller;
		method = method || "guardar";

		ajax.post({url: _base_url+controller+"/"+method, data: data, dataType: 'json'}, function(res) {
			if($.isFunction(callback)) {
				callback(res);
			}
		});
	}

	function del(id, callback, controller, method, datos) {
		controller = controller || _controller;
		method = method || "eliminar";
		if(typeof datos == "undefined")
			datos = {};

		ajax.post({url: _base_url+controller+"/"+method+"/"+id, data: datos, dataType: 'json'}, function(res) {
			if($.isFunction(callback)) {
				callback(res);
			}
		});
	}
	
	function aprobar_pedido(id, callback, controller, method) {
		controller = controller || _controller;
		method = "aprobar_pedido";

		ajax.post({url: _base_url+controller+"/"+method+"/"+id, data: {}, dataType: 'json'}, function(res) {
			if($.isFunction(callback)) {
				callback(res);
			}
		});
	}
	
	function del_param(data, callback, controller, method) {
		controller = controller || _controller;
		method = method || "eliminar_param";
		ajax.post({url: _base_url+controller+"/"+method, data: data, dataType: 'json'}, function(res) {
			if($.isFunction(callback)) {
				callback(res);
			}
		});
	}

	return {get:get, save:save, del:del, del_param:del_param, aprobar_pedido:aprobar_pedido}
}();

/**
 * Funcion para las ventanas de alertas y confirm
 */
var ventana = function() {
	var propertiesDefault = {
        titulo: 'Mensaje'
        ,mensaje: 'Mensaje'
        ,tipo: 'info' // success, warning, error
        ,textoBoton: 'Aceptar'
        ,textoBotonCancelar: 'Cancelar'
        ,textoBotonAceptar: 'Aceptar'
        ,cerrarConTeclaEscape: true
        ,cerrarAlConfirmar: true
		,botonCancelar: true
        ,placeholder: ""
    };

	function alert(propiedades, callback) {
		var props = $.extend({}, propertiesDefault, propiedades);

		var opciones = {
			title: props.titulo
			,text: props.mensaje
			,type: props.tipo
			,confirmButtonText: props.textoBoton
			,html: true
			,allowEscapeKey: props.cerrarConTeclaEscape
			,closeOnConfirm: props.cerrarAlConfirmar
		};

		if($.isFunction(callback)) {
			swal(opciones, function() {
				callback(true);
			});
		}
		else {
			swal(opciones);
		}
    }

    function confirm(propiedades, callback) {
		if(typeof propiedades.tipo == "undefined")
			propiedades.tipo = "warning";
		
        var props = $.extend({}, propertiesDefault, propiedades);

		var opciones = {
			title: props.titulo
			,text: props.mensaje
			,type: props.tipo
			,showCancelButton: true
			,confirmButtonColor: "#DD6B55"
			,confirmButtonText: props.textoBotonAceptar
			,cancelButtonText: props.textoBotonCancelar
			,html: true
			,allowEscapeKey: props.cerrarConTeclaEscape
			,closeOnConfirm: props.cerrarAlConfirmar
		};

		if($.isFunction(callback)) {
			swal(opciones, function(isConfirm) {
				callback(isConfirm);
			});
		}
		else {
			swal(opciones);
		}
    }
	
	function prompt(propiedades, callback) {
		propiedades.tipo = "input";
        var props = $.extend({}, propertiesDefault, propiedades);

		var opciones = {
			title: props.titulo
			,text: props.mensaje
			,type: props.tipo
			,showCancelButton: props.botonCancelar
			,confirmButtonColor: "#DD6B55"
			,confirmButtonText: props.textoBotonAceptar
			,cancelButtonText: props.textoBotonCancelar
			,html: true
			,allowEscapeKey: props.cerrarConTeclaEscape
			,closeOnConfirm: false
			// ,closeOnConfirm: props.cerrarAlConfirmar
			,inputPlaceholder: props.placeholder
			,animation: "slide-from-top"
		};

		if($.isFunction(callback)) {
			swal(opciones, function(isConfirm) {
				var bool = callback(isConfirm);
				if(bool === false) {
					return false;
				}
				swal.close();
			});
			// swal(opciones, callback);
		}
		else {
			swal(opciones);
		}
    }

	return {alert: alert, confirm: confirm, prompt: prompt};
}();

/**
 * Funcion para validar algun formulario, si no se envia parametros valida
 * el formulario principal del controlador y se invoca la funcion form.guardar()
 * se puede validar cualquier form enviando el selector del form
 * y la funcion callback
 */
var validate = function(idform, callback) {
	idform = idform || "#form_"+_controller;
	callback = callback || form.guardar;

	if($(idform).length) {
		if($(":input[required]", idform).length) {
			var arrmsg = {};
			$(":input[required]", idform).each(function() {
				arrmsg[$(this).attr("name")] = {required: ""};
			});

			var div_error = $("<p class='text-danger text-center' style='display:none;'></p>");
			$(idform).prepend(div_error);

			$(idform).validate({
				invalidHandler:function(e,validator){
					if(validator.numberOfInvalids()){
						div_error.html("Llene los campos requeridos por el formulario.").show();
					}
					else{
						div_error.html("").hide();
					}
				},
				showErrors: function(errorMap, errorList) {
					$("div.form-group.has-error", idform).removeClass("has-error");

					if(errorList.length) {
						$.each(errorList, function(index, value) {
							$(value.element).closest("div.form-group").addClass("has-error");
						});
						div_error.html("Llene los campos requeridos por el formulario.").show();
					}
					else {
						div_error.html("").hide();
					}
				},
				submitHandler: function() {
					div_error.hide();
					if($.isFunction(callback)) {
						callback();
					}
				},
				messages: arrmsg
			});
		}
	}
}

var clear_form = function(oform) {
	if( $(":input", oform).length ) {
		$(":input", oform).each(function() {
			switch( $(this).attr('type') ) {
				case 'select': 
					$('option:first', this).prop('selected', true);
					break;
				
				case 'radio':
				case 'checkbox':
					$(this).prop('checked', false);
					break;
					
				case 'button':
				case 'reset':
				case 'submit':
				case 'image':
					break;
				
				case 'text':
				case 'textarea':
				case 'hidden':
				case 'password':
				case 'file':
				default: 
					$(this).val('');
					break;
			}
		});
	}
}

function reload_combo(layout, data, callback) {
	var _defaults = {
		controller: "index"
		,method: "options"
		,data: ""
		,empty: false
		,labelEmpty: ""
	}
	var params = $.extend({}, _defaults, data);
	
	ajax.post({url: _base_url+params.controller+"/"+params.method, data: params.data}, function(res) {
		if(params.empty === true)
			res ="<option value=''>"+params.labelEmpty+"</option>"+res;
		$(layout).html(res);
		if($.isFunction(callback)) {
			callback();
		}
	});
}

Table = function() {
	this.props = {
        index: false
        ,class: false
        ,style: false
		,colspan: false
		,rowspan: false
		,data: false
    };
	
	this.arr_tr = [];
	this.arr_td = [];
	
	function _attr(data) {
		var str = '';
		if(data.index !== false) {
			str += ' index="'+data.index+'"';
		}
		if(data.class !== false) {
			str += ' class="'+data.class+'"';
		}
		if(data.style !== false) {
			str += ' style="'+data.style+'"';
		}
		if(data.colspan !== false) {
			str += ' colspan="'+data.colspan+'"';
		}
		if(data.rowspan !== false) {
			str += ' rowspan="'+data.rowspan+'"';
		}
		if(data.data !== false) {
			if($.isPlainObject(data.data)) {
				if( ! $.isEmptyObject(data.data)) {
					$.each(data.data, function(key, value) {
						str += ' data-'+key+'="'+value+'"';
					});
				}
			}
		}
		return str;
	}
	
	this.init = function() {
		this.arr_tr = [];
		this.arr_td = [];
	}
	
	this.to_string = function(clear) {
		if(typeof clear == "undefined")
			clear = true;
		
		var str = "";
		var trs = this.arr_tr, tds = [];
		
		for(var i in trs) {
			tds = this.arr_td[i];
			if(empty(tds)) {
				continue;
			}
			
			str += '<tr' + _attr(trs[i]) + '>';
			for(var j in tds) {
				str += '<td' + _attr(tds[j]) + '>' + tds[j].html + '</td>';
			}
			str += '</tr>';
		}
		
		if(clear)
			this.init();
		
		return str;
	}
}
Table.prototype.tr = function(param) {
	param = param || {};
	var data = $.extend({}, this.props, param);
	this.arr_tr.push(data);
	this.arr_td.push([]);
}
Table.prototype.td = function(text, param) {
	param = param || {};
	var data = $.extend({}, this.props, param);
	data.html = text;
	// obtenemos el ultimo tr data
	var key = this.arr_td.length - 1;
	this.arr_td[key].push(data); // almacenamos los TD
}

/**
 * Funcion para administrar tabs dinamicamente
 */
var jTab = function() {
	var _selector = null;
	
    var defaults = {
		label: ""
		,content: ""
		,href: ""
		,close: false
		,icon: "fa fa-times"
    };
	
	function init(selector) {
		if($(selector).length <= 0) {
			console.log("El selector para el tab no existe");
			return;
		}
		// verificar si es una instancia del tab
		_selector = selector;
    }
	
	function clearfix(val) {
		val = $.trim(val);
		val = val.replace(/\W/g, "");
		return val;
	}
	
	function exists(href) {
		return ($("ul.nav-tabs>li>a[href='#"+href+"']", _selector).length >= 1);
	}
	
	function create(opt) {
		var li = '<li class=""><a data-toggle="tab" href="#'+opt.href+'">'+$.trim(opt.label);
		if(opt.close) {
			li += '<span class="close-tab"><i class="'+opt.icon+'"></i></span>';
		}
		li += '</a></li>';
		
		$("ul.nav-tabs", _selector).append(li);
		$("div.tab-content", _selector).append('<div id="'+opt.href+'" class="tab-pane"><div class="panel-body">'+opt.content+'</div></div>');
		
		if(opt.close) {
			// evento close
			$("ul.nav-tabs>li>a[href='#"+opt.href+"']>span.close-tab", _selector).on("click", function(e){
				e.stopPropagation();
				var href = $(this).parent("a").attr("href");
				
				if($(this).closest("li").hasClass("active")) {
					var ul = $(this).closest("ul");
					var index = $(this).closest("li").index();
					
					index ++;
					if( $("li:eq("+index+")", ul).length < 1) {
						index -= 2;
					}
					
					$("li:eq("+index+")>a:first", ul).trigger("click");
				}
				
				$(this).off("click");
				$("div.tab-content>div"+href, _selector).remove();
				$(this).closest("li").remove();
				return false;
			});
		}
	}
	
	function select(href) {
		if(exists(href)) {
			$("ul.nav-tabs>li>a[href='#"+href+"']", _selector).trigger("click");
		}
	}

    function add(options) {
		if(_selector == null)
			return;
		
        var val = $.extend({}, defaults, options);
		val.href = clearfix(val.href);
		
		if(val.href == "") {
			console.log("No se ha indicado el ancla del tab (href)");
			return;
		}
		
		if(!exists(val.href)) {
			create(val);
		}
		
		select(val.href);
    }

    return {init:init, add:add, select:select};
}();

/**
 * Funcion para administrar tabs dinamicamente
 */
var jIframe = function() {
	var _selector = null;
	
    var defaults = {
		label: ""
		,content: ""
		,href: ""
		,close: false
		,icon: "fa fa-times"
		,forceContent: false
    };
	
	function init(selector) {
		if($(selector+"_header").length <= 0 || $(selector+"_body").length <= 0) {
			console.log("El selector para el tab iframe no existe");
			return false;
		}
		// verificar si es una instancia del tab
		_selector = selector;
		return true;
    }
	
	function clearfix(val) {
		val = $.trim(val);
		val = val.replace(/\W/g, "");
		return val;
	}
	
	function exists(href) {
		return ($("ul.nav-tabs>li>a[href='#"+href+"']", _selector+"_header").length >= 1);
	}
	
	function _calculate_width(selector) {
		var width = 0;
		
		if( $("ul.nav-tabs>li", selector+"_header").length ) {
			var widthTotal = $(selector+"_header").outerWidth();
			
			$("ul.nav-tabs>li", selector+"_header").each(function() {
				width += $(this).outerWidth() + 1;
			});
			
			if(width > widthTotal)
				width += "px";
			else
				width = "100%";
		}
		else {
			width = "100%";
		}
		
		$("ul.nav-tabs", selector+"_header").css("width", width);
	}
	
	function getHeight(selector) {
		var heightTotal = $(selector+"_body").closest("div.full-height-scroll").outerHeight();
		var padding = $(selector+"_body").outerHeight() - $(selector+"_body").height();
		return (heightTotal - padding - 5) + "px";
	}
	
	function create(opt) {
		var li = '<li class=""><a data-toggle="tab" href="#'+opt.href+'"><span class="title-tab">'+$.trim(opt.label)+'</span>';
		if(opt.close) {
			li += '<span class="close-tab"><i class="'+opt.icon+'"></i></span>';
		}
		li += '</a></li>';
		
		$("ul.nav-tabs", _selector+"_header").append(li);
		$("div.tab-content", _selector+"_body").append('<div id="'+opt.href+'" class="tab-pane">'+opt.content+'</div>');
		
		_calculate_width(_selector);
		
		if(opt.close) {
			// evento close
			$("ul.nav-tabs>li>a[href='#"+opt.href+"']>span.close-tab", _selector+"_header").on("click", function(e){
				e.stopPropagation();
				var href = $(this).parent("a").attr("href");
				
				if($(this).closest("li").hasClass("active")) {
					var ul = $(this).closest("ul");
					var index = $(this).closest("li").index();
					
					index ++;
					if( $("li:eq("+index+")", ul).length < 1) {
						index -= 2;
					}
					
					$("li:eq("+index+")>a:first", ul).trigger("click");
				}
				
				$(this).off("click");
				$("div.tab-content>div"+href, _selector+"_body").remove();
				$(this).closest("li").remove();
				_calculate_width(_selector);
				return false;
			});
		}
	}
	
	function replace(opt) {
		$("a[data-toggle='tab'][href='#"+opt.href+"']>.title-tab", _selector+"_header").text(opt.label);
		$(".tab-pane#"+opt.href, _selector+"_body").html(opt.content);
	}
	
	function select(href) {
		if(exists(href)) {
			$("ul.nav-tabs>li>a[href='#"+href+"']", _selector+"_header").trigger("click");
			var off = $("ul.nav-tabs>li>a[href='#"+href+"']", _selector+"_header").offset();
			// $(_selector+"_header").slimscroll({ scrollToX: off.left });
			$(_selector+"_header").scrollLeft(off.left);
		}
	}

    function add(options) {
		if(_selector == null)
			return;
		
        var val = $.extend({}, defaults, options);
		val.href = clearfix(val.href);
		
		if(val.href == "") {
			console.log("No se ha indicado el ancla del tab (href)");
			return;
		}
		
		if( ! exists(val.href)) {
			create(val);
		}
		else if(val.forceContent === true) {
			replace(val);
		}
		
		select(val.href);
    }

    return {init:init, clearfix:clearfix, add:add, select:select, getHeight:getHeight};
}();

$(function() {
	$("#btn_nuevo").on("click", function() {
		if(_type_form=="reload") {
			redirect(_controller+"/nuevo");
			return false;
		}
		form.nuevo();
		return false;
	});

	$("#btn_editar, #btn_eliminar").on("click", function() {
		var id = grilla.get_id(_default_grilla);
		if(id != null) {
			if(this.id == "btn_editar") {
				if(_type_form=="reload") {
					redirect(_controller+"/editar/"+id);
					return false;
				}
				form.editar(id);
			}
			else {
				ventana.confirm({titulo:"Confirmar",
				mensaje:"¿Desea eliminar el registro seleccionado?",
				textoBotonAceptar: "Eliminar"}, function(ok){
					if(ok) {
						// if(_type_form=="reload") {
							// redirect(_controller+"/eliminar/"+id);
							// return false;
						// }
						form.eliminar(id);
						// form.del(id);
					}
				});
			}
		}
		else {
			ventana.alert({titulo: "Aviso", mensaje: "Seleccione un registro de la tabla"});
		}
		return false;
	});

	$("#btn_print").on("click", function() {
		var id = grilla.get_id(_default_grilla);
		form.imprimir(id);
		return false;
	});

	// $(".btn_save").on("click", function() {
		// var ctrl = $(this).data("controller");
		// console.log(ctrl);
		// if(ctrl != undefined) {
			// var idform = "#form_"+ctrl;
			// if($(idform).length) {
				// console.log($(idform).attr("novalidate"));
				// if( $(idform).attr("novalidate") != undefined ) {
					// alert("utilizando plugins");
				// }
				// else {
					// form.guardar();
				// }
			// }
		// }
		// return false;
	// });

	$(".btn_cancel").on("click", function() {
		var ctrl = $(this).data("controller");
		if(ctrl == _controller) {
			if($.isFunction(form.cancelar)) {
				form.cancelar();
			}
			if(_type_form=="reload") {
				redirect(_controller);
			}
		}
		else {
			if($(this).hasClass("modal-form")) {
				if (typeof jQuery.fn.modal == 'function') {
					$(this).closest("div.modal").modal("hide");
				}
			}
		}
		return false;
	});
	
	// evento clic en los botones de seleccion del sistema
	$(".nav>li.item-nav-sistema>.btn-sel-sistema").on("click", function() {
		var i = $(this).attr("pkey");
		$(".nav>li.item-nav-sistema").removeAttr("style");
		setTimeout(function() {
			$(".nav>li.item-nav-menu[pkey="+i+"]").fadeIn(500);
		}, 100);
		return false;
	});
	// evento clic en el boton de retroceso a la seleccion del sistema
	$(".nav>li.item-nav-menu>a.item-back").on("click", function() {
		$(".nav>li.item-nav-menu").removeAttr("style");
		setTimeout(function() {
			$(".nav>li.item-nav-sistema").fadeIn(500);
		}, 100);
		return false;
	});
	// $("#jiframe-ymenu_header").slimscroll({
        // axis: 'x',
		// width: '100%',
		// height: '40px'
    // });
	// evento clic en los enlaces a los modulos
	$(".nav>li.item-nav-menu>ul.nav-second-level>li.item-menu>a").on("click", function() {
		var ifr = "module_iframe_"+$(this).attr("ikey");
		var h = jIframe.getHeight("#jiframe-ymenu");
		
		if(jIframe.init("#jiframe-ymenu")) {
			jIframe.add({
				label: $(this).text()
				,content: "<iframe id='"+ifr+"' name='"+ifr+"' src='"+ $(this).attr("href")+"' style='border:0;width:100%;min-height:"+h+"'></iframe>"
				,href: "ymtab-"+$(this).attr("ikey")
				,close: true
			});
		}
		return false;
	});
	
	$(document).ajaxStart(function() {
		$(".loader").show();
	}).ajaxStop(function() {
		$(".loader").hide();
	});
});

function saveStorage(key, datos) {
	if(localStorageSupport) {
		localStorage.setItem(key, datos);
	}
}

function getStorage(key) {
	if(localStorageSupport) {
		return localStorage.getItem(key);
	}
	
	return null;
}

function setDefaultValue(clave, valor) {
	var s = getStorage("default_values") || '{}';
	var data = $.parseJSON(s);
	
	if($.isPlainObject(clave)) {
		$.each(clave, function(k, v) {
			data[k] = v;
		});
	}
	else {
		data[clave] = valor;
	}
	
	saveStorage("default_values", JSON.stringify(data));
}

function getDefaultValue(clave) {
	var s = getStorage("default_values") || '{}';
	var data = $.parseJSON(s);
	
	if(typeof data[clave] != undefined) {
		return data[clave];
	}
	
	return false;
}

// sumar dias a una fecha
function addDate(fecha, dias) {
	if(typeof dias == 'string') {
		var arr = String(dias).split(' ');
		if(arr.length == 2) {
			var intervalo = String(arr[1]).trim();
			var cantidad = parseInt(arr[0]);
			
			if(intervalo == 'months') {
				dias = cantidad * daysInMonth((fecha.getMonth()+1), fecha.getFullYear());
			}
			else if(intervalo == 'weeks') {
				dias = cantidad * 7;
			}
			else {
				dias = cantidad;
			}
		}
		else {
			dias = 0;
		}
	}
	
	return new Date(fecha.getTime() + (dias * 24 * 3600 * 1000));
}
// restar dias
function subDate(fecha, dias) {
	if(typeof dias == 'string') {
		var arr = String(dias).split(' ');
		if(arr.length == 2) {
			var intervalo = String(arr[1]).trim();
			var cantidad = parseInt(arr[0]);
			
			if(intervalo == 'months') {
				dias = cantidad * daysInMonth((fecha.getMonth()+1), fecha.getFullYear());
			}
			else if(intervalo == 'weeks') {
				dias = cantidad * 7;
			}
			else {
				dias = cantidad;
			}
		}
		else {
			dias = 0;
		}
	}
	
	return new Date(fecha.getTime() - (dias * 24 * 3600 * 1000));
}

/* Restar fechas y devolver dias */
function resta_Fechas(f1,f2){
	var aFecha1 = f1.split('/'); 
	var aFecha2 = f2.split('/'); 
	var fFecha1 = Date.UTC(aFecha1[2],aFecha1[1]-1,aFecha1[0]); 
	var fFecha2 = Date.UTC(aFecha2[2],aFecha2[1]-1,aFecha2[0]); 
	var dif = fFecha2 - fFecha1;
	var dias = Math.floor(dif / (1000 * 60 * 60 * 24)); 
	return dias;
}

function daysInMonth(humanMonth, year) {
	return new Date(year || new Date().getFullYear(), humanMonth, 0).getDate();
}

function parseDate(txtDate) {
	if(txtDate) {
		var parts = String(txtDate).split('-');
		return new Date(parseInt(parts[0]), (parseInt(parts[1]) - 1), parseInt(parts[2]));
	}
	
	return null;
}

function dateFormat(_datetime, _format) {
	var outputString = '';
	
	if(_datetime) {
		var ms = Date.parse(_datetime); // una T entre fecha y hora
		var date = new Date(ms);
		
		var formats = ['Y', 'm', 'd', 'H', 'i', 's'];
		var dates = [
			date.getFullYear()
			,str_pad( (date.getMonth() + 1), 2, '0', 'LEFT' )
			,str_pad( date.getDate(), 2, '0', 'LEFT' )
			,date.getHours()
			,date.getMinutes()
			,date.getSeconds()
		];
		
		var index, s;
		
		for(var i=0; i < _format.length; i++) {
			s = _format.charAt(i);
			index = formats.indexOf(s);
			if(index != -1) {
				s = dates[index];
			}
			outputString += s;
		}
	}
    
    return outputString;
}

function str_pad(_string, _pad_length, _pad_string, _pad_type) {
    
    var str_pad_repeater = function (s, len) {
        var collect = '';

        while (collect.length < len) {
            collect += s;
        }
        collect = collect.substr(0, len);
        
        return collect;
    };
  
    _string = String(_string);
    var pad_length = _pad_length || 0;
    var pad_string = _pad_string || ' ';
    var pad_type = _pad_type ? String(_pad_type).toUpperCase() : 'RIGHT';
    
    if (pad_type !== 'LEFT' && pad_type !== 'RIGHT' && pad_type !== 'BOTH') {
        pad_type = 'RIGHT';
    }
    
    var pad_total = pad_length - _string.length;
    
    if(pad_total > 0) {
        if (pad_type === 'LEFT') {
            _string = str_pad_repeater(pad_string, pad_total) + _string;
        }
        else if (pad_type === 'RIGHT') {
            _string = _string + str_pad_repeater(pad_string, pad_total);
        }
        else if (pad_type === 'BOTH') {
            var half = str_pad_repeater(pad_string, Math.ceil(pad_total / 2));
            _string = half + _string + half;
            _string = _string.substr(0, pad_length);
        }
    }
    
    return _string;
}

/**
 * Funciones para la ventana modal popup, con subgrid y toda la vaina.
 * Para hacer una busqueda "avanzada".
 */
var jFrame = function() {
	var selector = "";
	
    var defaults = {
        selector: '#modal-popup'
        ,title: 'Seleccione'
        ,msg: 'Doble clic para seleccionar un registro.'
		,controller: ''
		,method: ''
		,data: ''
		,widthclass: ''
		,autoclose: true
		,onSelect: null
		,onCancel: null
    };

    function create(options) {
        var val = $.extend({}, defaults, options);
		selector = val.selector;
		load(val);
        $(".modal-title", selector).html(val.title);
        $(".modal-desc", selector).html(val.msg);
		if(val.widthclass != "") {
			$(".modal-dialog", selector).addClass(val.widthclass);
		}
		
		// eventos de los botones del modal dialog
        $("button.close-modal-popup", selector).on("click", function() {
			if($.isFunction(val.onCancel)) {
				val.onCancel();
			}
			jFrame.close(selector, val);
			return false;
		});
		
		$("button.select-modal-popup", selector).on("click", function() {
			var table = $("table.grilla_popup", selector);
			if($.isFunction(val.onSelect)) {
				var data = grilla.get_data(table.attr("realid"));
				if(data == null) {
					ventana.alert({titulo: "", mensaje: "Seleccione un registro de la tabla"});
					return false;
				}
				val.onSelect(data);
			}
			if(val.autoclose) {
				jFrame.close(selector, val);
			}
			return false;
		});
    }
	
	function load(opt) {
		$('.table-responsive', opt.selector).html("");
		ajax.load({
			selector: opt.selector+' .table-responsive', 
			url: _base_url+opt.controller+"/"+opt.method,
			data: opt.data, 
			dataType:'html', 
			responseType:"html"
		}, function() {
			// $("table.grilla_popup tbody", opt.selector).on("dblclick", "tr.dt_subgrid", function() {
			$("table.grilla_popup tbody", opt.selector).on("dblclick", "tr", function() {
				var tr = $(this);
				var table = tr.closest("table.grilla_popup");
				if($.isFunction(opt.onSelect)) {
					var data = grilla.get_data(table.attr("realid"), tr);
					opt.onSelect(data);
				}
				if(opt.autoclose) {
					jFrame.close(opt.selector, opt);
				}
			});
		});
	}
	
	function show(obj) {
		selector = obj || selector;
		$(selector).modal("show");
	}
	
	function close(obj, opt) {
		selector = obj || selector;
		$("button.close-modal-popup", selector).off("click");
		$("button.select-modal-popup", selector).off("click");
		$(selector).modal("hide");
		
		if(opt.widthclass != "") {
			setTimeout(function() {
				$(".modal-dialog", selector).removeClass(opt.widthclass);
			}, 200);
		}
	}

    return {create: create, show: show, close: close};
}();

var input = function() {
	// origin callback render item, menu
	// var _defaultRenderItem = $.ui.autocomplete.prototype._renderItem;
	var _defaultRenderMenu = $.ui.autocomplete.prototype._renderMenu;
	
	// default props
	var _props = {
		selector: "" // selector del input text
		// configuracion del ajax post
		,controller: ""
		,method: "autocomplete"
		,maxRows: 50
		,data: ""
		// configuracion autocomplete plugin
		,minLength: 2
		,label: "[descripcion]" // template para label, use "[" y "]", para indicar datos a obtener
		,value: "[descripcion]" // template para value, use "[" y "]", para indicar datos a obtener
		,appendTo: null
		// configuracion usuario
		,highlight: false
		,show_empty_msg: false
		,empty_msg: "<i>No se han encontrado resultados.</i>"
		,show_new_item: false
		,new_item_msg: "Agregar nuevo item"
		,onSelect: null
		,onNewItem: null
	};
	
	function defaults(obj) {
		// obj._renderItem = _defaultRenderItem;
		obj._renderItem = function( ul, item) {
			return $( "<li></li>" )
				.data( "ui-autocomplete-item", item )
				.append( $( "<a></a>" ).html( item.label ) )
				.appendTo( ul );
		};
		obj._renderMenu = _defaultRenderMenu;
	}
	
	function renderItemHighlight(obj) {
		obj._renderItem = function( ul, item) {
			if (item.disabled) {
				item.value = "";
				return $( "<li class='ui-state-disabled'>" ).data( "ui-autocomplete-item", item ).append( item.label ).appendTo( ul );
			} else {
				// re = new RegExp("^" + this.term, "i") ;
				// re = new RegExp("("+this.term+")(?![^<]*>|[^<>]*<\/)", "gi");
				re = new RegExp("("+this.term+")(?![^<]*>)", "gi");
				
				// re = new RegExp(this.term, "gi");
				t = item.label.replace(re,"<span class='highlight'>" + "$&" + "</span>");
				
				// re = new RegExp(String(this.term).replace(/\s+/g, "|"), "i");
				// t = item.label.replace(re,"<span class='highlight'>" + "$&" + "</span>");
				
				return $( "<li>" ).data( "ui-autocomplete-item", item ).append( "<a>" + t + "</a>" ).appendTo( ul );
			}
		};
	}
	
	function renderMenu(obj, props) {
		obj._renderMenu = function( ul, items ) {
			$.ui.autocomplete.prototype._renderMenu.apply( this, [ul, items] );
			item = {
				label: props.new_item_msg+": <strong>"+this.term+
					"</strong><label class='pull-right label label-primary'><i class='fa fa-plus'></i> Agregar</label>"
				,value: this.term
			};
			$( "<li class='success-element new-menu-item'>" ).data( "ui-autocomplete-item", item )
			  .append( "<a>" + item.label + "</a>" )
			  .appendTo( ul );
		}
	}
	
	function _renderData(obj) {
		if(obj)
			return $.isPlainObject(obj) ? $.param(obj) : obj;
		return "";
	}
	
	function autocomplete(props) {
		var def = $.extend({}, _props, props);
		if(def.show_new_item) {
			def.show_empty_msg = true;
		}
		
		// armamos el label
		var pattern = /(\[\w+\])/g
		var colLabel = def.label.match(pattern);
		var colValue = def.value.match(pattern);
		
		var f = $.isFunction(def.data);
		
		// var autoSearch = $(def.selector).autocomplete({
		$(def.selector).autocomplete({
			source: function( request, response ) {
				ajax.post({url: _base_url+def.controller+"/"+def.method, dataType: 'json',
				data: "maxRows="+def.maxRows+"&startsWith="+request.term+"&"+(f ? _renderData(def.data()) : _renderData(def.data))},
				function(res) {
					if(res.length <= 0) {
						if(def.show_empty_msg) {
							res.push({label:def.empty_msg,value:"",disabled:true});
						}
						response(res);
					}
					else {
						response( $.map( res, function( item ) {
							// armammos el label
							slabel = def.label;
							$.each(colLabel, function(index, value) {
								col = value.replace(/\W/g, "");
								slabel = slabel.replace(value, item[col]);
							});
							
							// armamos el value
							svalue = def.value;
							$.each(colValue, function(index, value) {
								col = value.replace(/\W/g, "");
								svalue = svalue.replace(value, item[col]);
							});
							
							item.label = slabel;
							item.value = svalue;
							
							return item;
						}));
					}
				});
			},
			html: true,
			minLength: def.minLength,
			appendTo: def.appendTo,
			select: function( event, ui ) {
				var li = $(this).data("ui-autocomplete").menu.active;
				if(li.hasClass("ui-state-disabled")) {
					return false;
				}
				if(li.hasClass("new-menu-item")) {
					if($.isFunction(def.onNewItem)) {
						def.onNewItem(ui.item.value);
					}
					return false;
				}
				if($.isFunction(def.onSelect)) {
					def.onSelect(ui.item);
				}
			}
		});
		// }).data( "ui-autocomplete" );
		
		// defaults(autoSearch);
		defaults($(def.selector).data( "ui-autocomplete" ));
		
		if(def.highlight) {
			// renderItemHighlight(autoSearch);
			renderItemHighlight($(def.selector).data( "ui-autocomplete" ));
		}
		
		if(def.show_new_item) {
			// renderMenu(autoSearch, def);
			renderMenu($(def.selector).data( "ui-autocomplete" ), def);
		}
	}

	return {autocomplete:autocomplete}
}();

function pad(width, string, padding) { 
  return (width <= string.length) ? string : pad(width, padding + string, padding)
}

/**
 * convierte formato de fechas, soporta fecha datetime (fecha y hora)
 * input format: yyyy-mm-dd
 * output format: dd/mm/yyyy
 */
function fecha_es(str, full, split, join) {
	var sp = split || "-";
	var jo = join || "/";
	if(typeof full != "boolean")
		full = true;
	
	var ext = "";
	
	if(str) {
		str = String(str);
		if(str.length > 10) {
			ext = str.substr(10);
			str = str.substr(0, 10);
		}
		
		if(full)
			return str.split(sp).reverse().join(jo) + ext;
		
		return str.split(sp).reverse().join(jo);
	}
	
	return "";
}

/**
 * convierte formato de fechas
 * input format: dd/mm/yyyy
 * output format: yyyy-mm-dd
 */
function fecha_en(str, full) {
	return fecha_es(str, full, "/", "-")
}

function resize_iframe(options) {
	var defaults = {
		name: null
		,height: null
		,width: null
	}
	var data = $.extend({}, defaults, options);
	if($.trim(data.name) == "") {
		return;
	}
	
	if( $("iframe[name='"+data.name+"']").length >= 1 ) {
		if(data.height)
			$("iframe[name='"+data.name+"']").css("height", data.height);
		if(data.width)
			$("iframe[name='"+data.name+"']").css("width", data.width);
	}
}

/**
 * Obtener el numero de dias entre dos fechas
 */
function getDays(inDate1, inDate2) {
	var oDate1 = (inDate1 instanceof Date) ? inDate1 : parseDate(inDate1);
	var oDate2 = (inDate2 instanceof Date) ? inDate2 : parseDate(inDate2);
	
	var dif = oDate2.getTime() - oDate1.getTime();
	
	return Math.floor(dif / (1000*60*60*24));
}

function MaysPrimera(string){
  return string.charAt(0).toUpperCase() + string.slice(1);
}

function redondeo_sunat(n) {
	var str_n = String(arr[i].file_url).split('.').pop();
}

function redondeosunat(n) {
	var str = parseFloat(n).toFixed(2);
	// n = n.toFixed(2);
	var x_n = String(str).split(".");
	// return x_n[1]/5;
	u_dig 	  = x_n[1]%10;
	n_decimal = 0;
	
	if(u_dig>=5){
		n_decimal = parseInt(x_n[1]) + parseInt(10-u_dig);
		if(n_decimal==100){
			n_decimal=0;
			x_n[0] = parseInt(x_n[0])+1;
		}

	}else if(u_dig>1){
		n_decimal = parseInt(x_n[1]) - parseInt(u_dig);
	}else{
		n_decimal = x_n[1];
	}
	// return u_dig;
	return parseFloat((x_n[0]+'.'+n_decimal));
}

function forEachIn(object, action) {
    for (var property in object) {
        if (object.hasOwnProperty(property))
            action(property, object[property]);
    }
}

function open_modal_cliente(abrir){
	$(".list_direcciones").empty();
	$(".list_telefonos").empty();
	$(".list_representantes").empty();
	var array = [];
	var data = {
			descripcion: ''
			,dir_principal: 'S'
			,direccion: ''
			,estado: 'A'
			,idcliente: ''
			,idclientedireccion: ''
		};

	array.push(data);
	direcciones_grid(array,true,prefix_cliente);
	
	var array = [];
	var data = {
			idclientetelefono: ''
			,idcliente: ''
			,descripcion: ''
			,estado: 'A'
			,telefono: ''
		};
	array.push(data);
	telefonos_grid(array,true,prefix_cliente);
	
	var array = [];
	var data = {
			idcliente_representante: ''
			,idcliente: ''
			,nombre_representante: ''
			,apellidos_representante: ''
			,dni_representante: ''
			,email_representante: ''
			,estado: 'A'
		};
	array.push(data);
	representante_grid(array,true,prefix_cliente);
	
	if(abrir){
		$("#"+prefix_cliente+"tipo option:first").prop("selected", "selected");
		$("#modal-cliente").modal("show");
	}
}

	function obtenerDatosCliente(id, prefix, modal_form, response){
		$(".list_direcciones").empty();
		$(".list_telefonos").empty();
		$(".list_representantes").empty();
		prefix 					= prefix || '';			 // example = _form
		modal_form 				= modal_form || ''; // example = #form-data	-> Sirve para abrir el moda si es necesario, y el valor esl el nombre del modal
		response				= response || ''; //aqui se cargan los datos que en alguna ofra funcion se reconge con post
		//jFrame.clear("#form-data"+prefix);
		if(id!=''){
			ajax.post({url: _base_url+"cliente/get_all/", data:{id:id}}, function(response) {
				forEachIn(response.cliente, function(name, value) {
					if( $("#"+prefix_cliente+name).length ) {
						if(name == 'fecha_nac')
							$("#"+prefix_cliente+name).val(dateFormat(parseDate(value),'d/m/Y'));
						else if($("#"+prefix_cliente+name).prop("type")=='checkbox')
							if(value=='S'){
								$("#"+prefix_cliente+name).prop("checked",true);
							}else
								$("#"+prefix_cliente+name).prop("checked",false);
						else
							$("#"+prefix_cliente+name).val(value);
						
					}
				});
				// $('#'+prefix_cliente+'linea_credito').trigger("click");
				$('#'+prefix_cliente+'tipo').trigger("click");
				
				if(response.direccion.length){
					datos = response.direccion;
				}else{
					var array = [];
					var data = {
							idclientedireccion: ''
							,descripcion: ''
							,idcliente: ''
							,direccion: ''
							,dir_principal: 'S'
						};
				
					array.push(data);
					datos = array;
				}
				direcciones_grid(datos,false,prefix_cliente);
				
				if(response.telefonos.length){
					datos = response.telefonos;
				}else{
					var array = [];
					var data = {
							idclientetelefono: ''
							,idcliente: ''
							,descripcion: ''
							,telefono: ''
						};
				
					array.push(data);
					datos = array;
				}
				telefonos_grid(datos,false,prefix_cliente);
				
				if(response.representantes.length){
					datos = response.representantes;
				}else{
					var array = [];
					var data = {
							idcliente_representante: ''
							,idcliente: ''
							,nombre_representante: ''
							,apellidos_representante: ''
							,dni_representante: ''
							,email_representante: ''
						};
				
					array.push(data);
					datos = array;
				}
				representante_grid(datos,false,prefix_cliente);
			});
		}
		if($.trim(modal_form) && $.trim(id)){
			$("#modal-cliente").modal("show");
			
			setTimeout(function(){
				$("#"+prefix_cliente+"tipo").trigger('change');
				$("#"+prefix_cliente+"tipo").focus();
			},1000);
			return false;
		}
	}

function direcciones_grid(rows,es_nuevo,prefix){
	es_nuevo = es_nuevo || false;
	prefix   = prefix || '';
	if(rows.length) {		
		var data = null, tr = null, html = '',$is_checked = "",$id_direccion = "",$id_principal='N';
		ver_dir = 0;
		
		$(".direccion").each(function(){
			ver_dir ++;
		});
		
		for(var i in rows) {
			data = rows[i];
			
			direccion_cliente = (data.direccion) ? data.direccion : '';
			dir_principal = (data.dir_principal) ? data.dir_principal : 'N';
			botoncito ='	<span class="input-group-addon cursor tooltip-demo delete_direccion" style="border:1px solid #1c84c6;color: #1c84c6;border-radius: 0px 3px 3px 0px;">';
			botoncito+='    	<div data-toggle="tooltip" class="" title="Borrar direccion">';
			botoncito+='			<i class="fa fa-trash"></i>';
			botoncito+='    	</div>';
			botoncito+='  	</span>';
			
			if(es_nuevo && i==0){
				$is_checked = "checked";
				$id_principal = "S";

				botoncito ='	<span class="input-group-addon cursor tooltip-demo new_direccion" id="addDireccion" style="border:1px solid #1c84c6;color: #1c84c6;border-radius: 0px 3px 3px 0px;">';
				botoncito+='		<div data-toggle="tooltip" id="" title="" class="">';
				botoncito+='			<i class="fa fa-plus-square"></i>';
				botoncito+='		</div>';
				botoncito+='	</span>';
			}else{
				if(dir_principal=='S')
					$is_checked = "checked";
				else
					$is_checked = '';
				
				if(i==0 && ver_dir == 0){
					botoncito ='	<span class="input-group-addon cursor tooltip-demo" id="addDireccion" style="border:1px solid #1c84c6;color: #1c84c6;border-radius: 0px 3px 3px 0px;">';
					botoncito+='		<div data-toggle="tooltip" id="" title="Añadir direccion" class="">';
					botoncito+='			<i class="fa fa-plus-square"></i>';
					botoncito+='		</div>';
					botoncito+='	</span>';
				}
			}
			
			$html_dir ='<div class="input-group" style="margin-top:5px;">';
			$html_dir+='  	<span class="input-group-addon tooltip-demo" style="padding: 0px 2px 1px 0px;margin-left: 5px;">';
			$html_dir+='    	<div class="radio" style="padding-left: 30px;" data-toggle="tooltip" title="Seleccione Direccion Principal">';
			$html_dir+='        	<input type="radio" '+$is_checked+' name="radio_dir" class="dir_principal" value="'+dir_principal+'" >';
			$html_dir+='        	<label></label>';
			$html_dir+='    	</div>';
			$html_dir+='    	<input type="hidden" class="dir_principal_val" name="dir_principal[]" value="'+dir_principal+'" >';
			$html_dir+='  	</span>';
			
			$html_dir+='	<input type="text" name="direccion[]" value="'+direccion_cliente+'" placeholder="Direccion..." value="" class="form-control direccion here_req req" style="font-size:12px;padding:4px 4px;">';
			$html_dir+=botoncito;
			
			$html_dir+='</div>';
			$(".list_direcciones").append($html_dir);
			$(".direccion").required();
			// $(".tooltip-demo").tooltip('destroy');
			// $(".new_direccion").attr({ title: 'Añadir direccion'}).tooltip('fixTitle').tooltip('show');
		}
	}
}

function telefonos_grid(rows,es_nuevo,prefix){
	es_nuevo = es_nuevo || false;
	prefix   = prefix || '';
	
	if(rows.length) {
		var data = null, tr = null, html = '';
		ver_dir = 0;
		
		$(".telefono").each(function(){
			ver_dir ++;
		});
		req = '';
		if(ver_dir>0){
			req = 'req';
		}
		for(var i in rows) {
			data = rows[i];
			
			telefono_cliente = (data.telefono) ? data.telefono : '';
			
			$html_telf ='<div class="input-group" style="margin-top: 5px;">';
			$html_telf+='	<input type="text" name="telefono[]" value="'+telefono_cliente+'" class="form-control telefono '+req+'" style="font-size:12px;padding:4px 4px;">';
			$html_telf+='	<span class="input-group-btn tooltip-demo">';
			if(i==0 && ver_dir == 0){
				$html_telf+='		<button type="button" id="addTelefono" style="" class="btn btn-outline btn-success" data-toggle="tooltip" title="Añadir Telefono">';
				$html_telf+='			<i class="fa fa-plus-square"></i>';
				$html_telf+='		</button>';																
			}else{
				$html_telf+='		<button type="button" style="" class="btn btn-outline btn-success delete_telefono" title="Borrar Telefono">';
				$html_telf+='			<i class="fa fa-trash"></i>';
				$html_telf+='		</button>';	
			}
			$html_telf+='	</span>';
			$html_telf+='</div>';
			$(".list_telefonos").append($html_telf);
			// $(".telefono").required();
		}
	}
}

function representante_grid(rows,es_nuevo,prefix){
	es_nuevo = es_nuevo || false;
	prefix   = prefix || '';
	
	if(rows.length) {
		var data = null, tr = null, html = '';
		ver_dir = 0;
		
		$(".nombre_representante").each(function(){
			ver_dir ++;
		});
		req = '';
		if(ver_dir>0){
			req = 'req';
		}
		for(var i in rows) {
			data = rows[i];
			
			$html_rep ='<div class="col-md-12">';
			$html_rep+='	<div class="row" style="">';
			$html_rep+='		<div class="col-md-3">';
			$html_rep+='      <div class="">';
			$html_rep+='			<label class="required">Nombres</label>';
			$html_rep+='				<input type="text" name="nombre_representante[]" value="" placeholder="Nombre Representante" class="form-control nombre_representante here_req input-xs">';
			$html_rep+='			</div>';
			$html_rep+='		</div>';
			
			$html_rep+='		<div class="col-md-6">';
			$html_rep+='      <div class="">';    
			$html_rep+='			<label class="required">Apellidos</label>';		
			$html_rep+='				<input type="text" name="apellidos_representante[]" value="" placeholder="Apellidos Representante" class="form-control apellidos_representante here_req input-xs">';
			$html_rep+='			</div>';
			$html_rep+='		</div>';
			
			$html_rep+='    <div class="col-md-3">';
			$html_rep+='		<label class="required">Dni</label>';
			$html_rep+='			<div class="input-group">';
			$html_rep+='				<input type="text" name="dni_representante[]" value="" maxlength="8" placeholder="DNI" class="form-control dni_representante here_req input-xs">';
			$html_rep+='				<span class="input-group-btn tooltip-demo">';
			
			if(i==0 && ver_dir == 0){
				$html_rep+='					<button type="button" id="addRepresentante" style="" class="btn btn-outline btn-success btn-xs" data-toggle="tooltip" title="Añadir Representante">';
				$html_rep+='						<i class="fa fa-plus-square"></i>';
				$html_rep+='					</button>';
			}else{
				$html_rep+='					<button type="button" class="btn btn-outline delete_repres btn-success btn-xs" title="Borrar Representante">';
				$html_rep+='						<i class="fa fa-trash"></i>';
				$html_rep+='					</button>';
			}

			$html_rep+='				</span>';
			$html_rep+='			</div>';
			$html_rep+='		</div>';

			$html_rep+='	</div>';
			$html_rep+='</div>';
		}

		$('.list_representantes').append($html_rep);
		$('.nombre_representante').required();
		$('.dni_representante').numero_entero().css({'font-size':'10px',"padding":"2px 2px"});
		$('.nombre_representante').letras({permitir:' '});
	}
}

function keyboardSequence(inputs, selform, setfocusfirst, setseq, setjumpfirst, settabindex) {
	var tabseq = $.isNumeric(setseq) ? parseInt(setseq) : 0;
	
	if(inputs.length) {
		var target = selform || "body";
		var setfocus = (typeof setfocusfirst == "boolean") ? setfocusfirst : true;
		var tabindex = (typeof settabindex == "boolean") ? settabindex : false;
		var jumpfirst = (typeof setjumpfirst == "boolean") ? setjumpfirst : false;
		
		if(tabindex === true)
			$(":input", target).attr("tabindex", "-1"); // modify attr tabindex for all items
		
		var fn = function(obj, t) {
			var next = obj;
			
			function callback_keydown(e) {
				if(e.which == 9) { // tab key
					e.stopPropagation();
					e.preventDefault();
					if(next != null)
						next.focus();
				}
				else if(e.which == 13) { // enter key
					if( ! $(this).is(":button")) {
						if(next != null) {
							// if(next.is(":button") || next.prop("tagName") != "SELECT") {
							if( ! next.is(":button") || next.prop("tagName") != "SELECT") {
								e.stopPropagation();
								e.preventDefault();
							}
							next.focus();
						}
					}
				}
			}
			
			function callback_keypress(e) {
				if(e.which == 13) {
					e.stopPropagation();
					if($(this).is(":button")) {
						var c = Number($(this).data("count")) + 1;
						/* if(c >= 2) {
							e.preventDefault();
							if(next != null)
								next.focus();
							c = 0;
						} */
						$(this).data("count", c);
					}
				}
			}
			
			if(t == "p")
				return callback_keypress;
			else
				return callback_keydown;
		};
		
		var len = inputs.length, next, j;
			
		$.each(inputs, function(i, sel) {
			j = Number(i) + 1;
			that = $(sel, target);
			if(tabindex === true) {
				that.attr("tabindex", ++tabseq);
			}
			next = null;
			
			if(j != len) {
				next = $(inputs[j], target);
			}
			else if(jumpfirst === true) {
				next = $(inputs[0], target); // tab jump first input
			}
			
			if(that.is(":button")) {
				that.data("count", 0);
				that.on("keypress", fn(next, "p"));
			}
			if(next != null) {
				that.on("keydown", fn(next));
			}
		});
		
		if(setfocus)
			$(inputs[0], target).focus(); // focus first input
	}
	
	return tabseq;
}
	
function retornar_boton(controlador,pref,idbot,is_controller){
	is_controller = is_controller|| 'S';
	if($.trim(controlador)!='' && is_controller=='S'){
		controlador = "_"+controlador;
	}
	
	idform		=	'#form'+controlador;
	pref		=	pref||'';
	idbot		=	idbot||'btn_save';
	
	return $(idform).find("#"+pref+idbot)
	// if(boton != "")
		// return $(idform).closest(".modal-content").find(".modal-footer>button.btn-primary:contains('"+boton+"')");
	// else
		// return $(idform).closest(".modal-content").find(".modal-footer>button.btn-primary:first");
}

function number_format(number, decimals, dec_point, thousands_sep) {
  number = (number + '')
    .replace(/[^0-9+\-Ee.]/g, '');
  var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function(n, prec) {
      var k = Math.pow(10, prec);
      return '' + (Math.round(n * k) / k)
        .toFixed(prec);
    };
  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
    .split('.');
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }
  if ((s[1] || '')
    .length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1)
      .join('0');
  }
  return s.join(dec);
}