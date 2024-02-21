<?php

/**
 * Clase que provee el driver necesario a usar en el sistema.
 * Driver, hace referencia al objeto que se crea segun el gestor 
 * de base de datos a emplear.
 */
class DriverController {
	
    /**
     * Crea y retorna un objeto segun el nombre del gestor de base de datos
     * pasado por parametro.
     * Para la creacion del objeto se emplea el patron de disenio AbstractFactory.
     * @param string $type nombre del gestor
     * @return Connectable o null
     * @throws Exception si el driver no se encuentra.
     */
    public static function &getDriver($type) {
        $file = MODEL_DIR."/drivers/{$type}.class.php";
        
        if(include_once($file)) {
            $instancia = call_user_func(array( $type, "getInstance" ));
            $instancia->setConectable();
            return $instancia;
        }
        else {
            throw new Exception ("Driver $type not found");
        }
        
        return null;
    }
}

?>