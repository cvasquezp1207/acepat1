var ubigeo = {
	selector: "#modal-ubigeo" // selector, ID div modal
	,idubigeo: false // id del ubigeo a seleccionar
	,onCancel: $.noop // callback when click button cancel
	,onSave: $.noop // callback button save
	
	,_clearfix: function(idubigeo) {
		idubigeo = String(idubigeo).replace(/\s+/g, "");
		if(/\d{6}/.test(idubigeo)) {
			return idubigeo;
		}
		return false;
	}
	
	,_get_id_if_set: function(rel) {
		if(this.idubigeo === false)
			return false;
		
		if(rel == "departamento") {
			return String(this.idubigeo).substr(0, 2) + "0000";
		}
		else if (rel == "provincia") {
			return String(this.idubigeo).substr(0, 4) + "00";
		}
		else if (rel == "distrito") {
			return this.idubigeo;
		}
		
		return false;
	}
	
	,select: function(rel) {
		var sel = this._get_id_if_set(rel);
		if(sel !== false)
			$("select.idubigeo_temp_modal[data-name='"+rel+"']", this.selector).val( sel );
	}
	
	,reload: function(self, callback) {
		ajax.post({url: _base_url+"ubigeo/get_"+self.data("reload")+"/"+self.val()}, function(arr) {
			var options = '';
			if(arr.length) {
				for(var i in arr) {
					options += '<option value="'+arr[i].idubigeo+'">'+arr[i].descripcion+'</option>';
				}
			}
			$("select.idubigeo_temp_modal[data-name='"+self.data("reload")+"']", ubigeo.selector).html( options );
			if(ubigeo.idubigeo !== false) {
				ubigeo.select(self.data("reload"));
			}
			if($.isFunction(callback)) {
				callback();
			}
		});
	}
	
	,set: function(idubigeo) {
		this.idubigeo = this._clearfix(idubigeo);
		if(this.idubigeo !== false) {
			var self = $("select.idubigeo_temp_modal:first", this.selector);
			self.val( this._get_id_if_set(self.data("name")) );
			self.trigger("change");
		}
	}
	
	,show: function() {
		$(this.selector).modal("show");
	}
	,close: function() {
		$(this.selector).modal("hide");
	}
	
	,cancel: function(callback) {
		this.onCancel = callback;
	}
	,ok: function(callback) {
		this.onSave = callback;
	}
};

// evento show del modal
// $(ubigeo.selector).on('shown.bs.modal', function() {
	// if( $("select.idubigeo_temp_modal", ubigeo.selector).length ) {
		// $("select.idubigeo_temp_modal:first", ubigeo.selector).trigger("change");
	// }
// });

// trigger change idtipopago
$("select.idubigeo_temp_modal", ubigeo.selector).on("change", function() {
	var rel = $.trim($(this).data("reload"));
	if(rel != "") {
		ubigeo.reload($(this), function() {
			$("select.idubigeo_temp_modal[data-name='"+rel+"']", ubigeo.selector).trigger("change");
		});
	}
});

// event button cancel
$("button.btn-cancel-ubigeo", ubigeo.selector).on("click", function(e) {
	e.preventDefault();
	if($.isFunction(ubigeo.onCancel)) {
		ubigeo.onCancel();
	}
	ubigeo.close();
	return false;
});

// event button save
$("button.btn-accept-ubigeo", ubigeo.selector).on("click", function(e) {
	e.preventDefault();
	
	if($("select.idubigeo_temp_modal", ubigeo.selector).required()) {
		if($.isFunction(ubigeo.onSave)) {
			// preparamos data
			var object = {};
			var idubigeo = null;
			if( $("select.idubigeo_temp_modal", ubigeo.selector).length ) {
				$("select.idubigeo_temp_modal", ubigeo.selector).each(function() {
					object[$(this).data("name")] = $("option:selected", this).text();
					if($(this).val() != "") {
						idubigeo = $(this).val();
					}
				});
			}
			object["idubigeo"] = idubigeo;
			
			ubigeo.onSave(object);
		}
		
		ubigeo.close();
	}
	
	return false;
});