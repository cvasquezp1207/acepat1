<?php $_SERVER['REMOTE_ADDR']=$_SERVER['HTTP_HOST'];?>
<iframe width='100%' height="650px" border='0' src="http://<?php echo (!empty($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR']:'127.0.0.1'; ?>:8080/pentaho/content/saiku-ui/index.html?userid=admin&password=password&biplugin5=true&dimension_prefetch=false#query/open//public/sorsa.saiku">
</iframe>