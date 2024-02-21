<div class="">
	<div class="col-sm-12" style="">
		<form id="parametros">
			<div class="row">
				<div class="col-sm-2" style="">
					<label>Comprobante</label>
					<select class="form-control input-xs" id="idtipodocumento" name="idtipodocumento">
						<option value="">[TODOS]</option>
						<?php
							foreach ($comprobante as $key => $value) {
								echo "<option value='{$value['idtipodocumento']}'>{$value['tipo_documento']}</option>";
							}
						?>
					</select>
				</div>
				
				<div class="col-sm-4" style="">
					<label>Serie</label>
					<input class="form-control input-xs" name="serie" id="serie"></input>
				</div>
				
				<div class="col-sm-3" style="">
					<label>Sucursal</label>
					<select name="idsucursal" class="form-control input-xs" id="idsucursal" >
						<option value="">[TODOS]</option>
						<?php
							foreach ($sucursal as $key => $value) {
								echo "<option value='{$value['idsucursal']}'>{$value['descripcion']}</option>";
							}
						?>
					</select>
				</div>
				
				<div class="col-sm-3" style="">
					<label>&nbsp;&nbsp;</label>
					<div>
						<button id="btn_pdf" class="btn btn-sm btn-white" ><i class="fa fa-file-pdf-o"></i> PDF</button>
						<!--<button id="btn_excel" class="btn btn-sm btn-white" ><i class="fa fa-file-excel-o"></i> EXCEL</button>-->
					</div>
				</div>
			</div>
		</form>
	</div>
	<div class="col-sm-12" style="">
		<iframe src="" 
                width="100%" 
                height="480px" 
                border='0' 
                frameborder='0' 
                scrolling="yes" 
                marginwidth="0" 
                marginheight="0"
                vspace="0" 
                hspace="0"
                id="cuadroReporte">            
        </iframe>
	</div>
</div>

<script src="app/js/jquery-2.1.1.js"></script>
<script type="text/javascript">
	$(function(){		
		$("#btn_pdf").click(function(e){
			e.preventDefault();
			str = $("#parametros").serialize();
			$("#cuadroReporte").attr("src", _base_url +_controller+ "/imprimir?" + str);
		});
	});
</script>