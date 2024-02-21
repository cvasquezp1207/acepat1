	<?php 
		if(!empty($titulo_form)){
			echo "<div class='ibox-title'>";
			echo "<h5>".$titulo_form."</h5>"; 
			echo "</div>";
		}
	?>
<div class="ibox-content">
	<form id="form_<?php echo $controller; ?>" class="form-horizontal app-form form-uppercase">
		<input type="hidden" name="idparametrocartera" id="idparametrocartera" value="<?php echo (!empty($idparametrocartera)) ? $idparametrocartera : ""; ?>">
		<div class="form-group">
			<label class="col-lg-2 control-label required">Descripci&oacute;n</label>
			<div class="col-lg-10">
				<input type="text" name="descripcion" id="descripcion" value="<?php echo (!empty($descripcion)) ? $descripcion : ""; ?>" class="form-control" required="">
			</div>
		</div>
		
		<div class="form-group">
			<label class="col-lg-2 control-label required">Tipo</label>
			<div class="col-lg-10">
				<!--<input type="text" name="tipo" id="tipo" value="<?php echo (!empty($tipo)) ? $tipo : ""; ?>" class="form-control" required="">-->
				<?php 
					$valores = array("NO"=>"NO","SI"=>"SI");
					$combo = "<select id='tipo' class='form-control' name='tipo'>";
					foreach($valores as $k=>$val){
						$combo.="<option value='{$val}' ";
						if(!empty($tipo) && $tipo==$val)
							$combo.="selected";
						$combo.=">";
						$combo.=$k;
						$combo.="</option>";
					}
					$combo.= "</select>";
					
					echo $combo;
				?>
			</div>
		</div>
		
		<div class="form-group">
			<div class="col-lg-offset-2 col-lg-9">
				<button id="btn_cancel" class="btn btn-sm btn-white btn_cancel" >Cancelar</button>
				<button type="submit" id="btn_save" class="btn btn-sm btn-primary" >Guardar</button>
			</div>
		</div>
	</form>
</div>