<?php
include 'cnx.php';

$fp = fopen("creditos_1.txt", "r");
$ii=100;
while(!feof($fp)) {
	$linea = fgets($fp);
	$array_dato=explode(",",$linea);
	
	if(count($array_dato)>1){
		// $sql = "INSERT INTO venta.cliente VALUES($linea);";
		$sql = "INSERT INTO credito.credito VALUES(";
		foreach($array_dato as $k=>$v){
			if($k==0)
				if(!intval($v)){
					$v=($ii);
				}
			if($k==14)
				$v='N';
			
			if($v=='NULL'||$v=='null')
				$sql.="null,";
			else
				$sql.="'".trim($v)."',";
		}
		
		$sql=substr($sql,0,-1);
		
		$sql.= ");";
		pg_query($c1,$sql);
		echo $sql."<hr/>";
		$ii++;
	}else{
		// print_r($array_dato);
	}
}
fclose($fp);

?>