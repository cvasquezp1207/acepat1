<?php 
if(!empty($sistemas)) {
	$start = 0;
	$end = count($sistemas)-1;
	$total_col = 6;
	$col = 2;
	$arr_color = array("default", "primary", "success", "info", "warning", "danger");
	foreach($sistemas as $key=>$val) {
		if($key%$total_col==0) {
			if($key != $start) {
				echo '</div>';
			}
			echo '<div class="row">';
		}
		$color = $arr_color[ rand(0, 5) ];
		
		echo '<div class="col-lg-'.$col.'">';
		echo '<a href="'.base_url().'home/cambiar_sistema/'.$val["idsistema"].'" class="btn btn-block btn-outline btn-'.$color.'" style="white-space:normal;">
			<i class="fa '.$val["image"].' fa-2x"></i> &nbsp;'.strtolower($val["descripcion"]).'
			</a>';
		echo '</div>';
		if($key == $end) {
			echo '</div>';
		}
	}
}
?>