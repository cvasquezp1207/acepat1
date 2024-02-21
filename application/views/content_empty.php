<?php 
if(!isset($with_tabs)) 
	$with_tabs = false;
$cls = "";
if(!$with_tabs) {
?>
<div class="row wrapper border-bottom white-bg page-heading">
	<div class="col-lg-10">
		<h2><?php echo $menu_title; ?></h2>
		<?php 
		if(!empty($path)) {
			echo $path;
		}
		?>
	</div>
</div>
<?php $cls="animated fadeInRight"; } ?>
<div class="wrapper wrapper-content <?php echo $cls; ?>">
	<?php echo $content; ?>
</div>