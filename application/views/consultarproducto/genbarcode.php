<html lang="es">
<head>
    <title><?php echo $codigo_barras;?> - Barcode</title>
    <meta charset="utf-8" />
    <link href="<?php echo base_url("app/css/reset.css");?>" rel="stylesheet" type="text/css">
    <link href="<?php echo base_url("app/css/print.css");?>" rel="stylesheet" type="text/css">
    <script type="text/javascript">
        window.onload = function() {
//            window.print(); // A4: 21 x 29.7
        };
    </script>
</head>

<body>
<div class="content-bloque2">
<?php
$cantidad = (empty($cantidad)) ? 1 : $cantidad;
while($cantidad) {
?>
<div class="bloque2">
	<p class="text-right">Precio S/.</p>
	<div class="left-right clearfix">
		<div class="left">
			<img class="bar" src="<?php echo base_url($path);?>">
			<span class="bar"><?php echo $codigo_barras;?></span>
		</div>
		<div class="right">
			<p class="precio"><?php echo number_format($precio_sugerido,2);?></p>
			<p><?php echo date("d/m/Y");?></p>
		</div>
	</div>
	<p class="item"><?php echo substr($producto, 0, 41);?></p>
</div>
<?php
	$cantidad --;
}
?>
</div>
</body>
</html>