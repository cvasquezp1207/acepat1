<?php 

class NumeroLetra {
    protected $UNIDADES = array("", "un ", "dos ", "tres ", "cuatro ", "cinco ", "seis ", "siete ", "ocho ", "nueve ");
    protected $DECENAS = array("diez ", "once ", "doce ", "trece ", "catorce ", "quince ", "dieciseis ", "diecisiete ", "dieciocho ", "diecinueve ", "veinte ", "treinta ", "cuarenta ", "cincuenta ", "sesenta ", "setenta ", "ochenta ", "noventa ");
    protected $CENTENAS = array("", "ciento ", "doscientos ", "trescientos ", "cuatrocientos ", "quinientos ", "seiscientos ", "setecientos ", "ochocientos ", "novecientos ");
    
    public function __construct() {
        //nada por aqui
    }

    public function convertir($numero, $uppercase = false) {
        $literal = ""; 
        $parteDecimal = '';
        
        $numero = str_replace('.', ',', $numero);
   
        if(strpos($numero, ',') === false) {
            $numero .= ',00';
        }
        
        if(preg_match("/^\d{1,9},\d{1,2}$/", $numero)) {
            $num = explode(',', $numero);
            $parteDecimal = $num[1] . '/100';
//            $parteDecimal = $num[1] . '/100 Nuevos Soles';
            
            $nro = (int) $num[0];
            if($nro == 0) {
                $literal = 'cero ';
            } else if ($nro > 999999) {//si es millon
                $literal = $this->getMillones($num[0]);
            } else if ($nro > 999) {//si es miles
                $literal = $this->getMiles($num[0]);
            } else if ($nro > 99) {//si es centena
                $literal = $this->getCentenas($num[0]);
            } else if ($nro > 9) {//si es decena
                $literal = $this->getDecenas($num[0]);
            } else {//sino unidades -> 9
                $literal = $this->getUnidades($num[0]);
            }
            //devuelve el resultado en mayusculas o minusculas
            if ($uppercase) {
                return strtoupper($literal . " CON " . $parteDecimal);
            } else {
                return ($literal . " con " . $parteDecimal);
            }
        }
        else {
            return null;
        }
    }
    
    public function getUnidades($numero) {
        $num = substr($numero, strlen($numero) - 1);
        return $this->UNIDADES[intval($num)];
    }
    
    public function getDecenas($num) {
        $n = intval($num);
        if ($n < 10) {
            //para casos como -> 01 - 09
            return $this->getUnidades($num);
        } else if ($n > 19) {
            //para 20...99
            $u = $this->getUnidades($num);
            if ($u == "") {
                //para 20,30,40,50,60,70,80,90
                return $this->DECENAS[intval(substr($num, 0, 1)) + 8];
            } else {
                if($n > 20 && $n < 30)
                    return "veinti" . $u;
                else
                    return $this->DECENAS[intval(substr($num, 0, 1)) + 8] . "y " . $u;
            }
        } else {
            //numeros entre 11 y 19
            return $this->DECENAS[$n - 10];
        }
    }
    
    public function getCentenas($num) {// 999 o 099
        if( (int) $num > 99 ){//es centena
            if ((int) $num == 100) {//caso especial
                return "cien ";
            } else {
                 return $this->CENTENAS[(int) substr($num, 0, 1)] . $this->getDecenas(substr($num, 1));
            }
        }else{//por Ej. 099
            //se quita el 0 antes de convertir a decenas
            return $this->getDecenas((int) $num . "");
        }
    }
    
    public function getMiles($numero) {// 999 999
        //obtiene las centenas
        $c = substr($numero, strlen($numero) - 3);
        //obtiene los miles
        $m = substr($numero, 0, strlen($numero) - 3);
        $n="";
        //se comprueba que miles tenga valor entero
        if ((int) $m > 0) {
			$n = ((int) $m > 1) ? $this->getCentenas($m) : "";
            return $n . "mil " . $this->getCentenas($c);
        } else {
            return "" . $this->getCentenas($c);
        }
    }
    
    public function getMillones($numero) {//000 000 000
        //se obtiene los miles
        $miles = substr($numero, strlen($numero) - 6);
        //se obtiene los millones
        $millon = substr($numero, 0, strlen($numero) - 6);
        $n = "";
        if(strlen($millon) > 1){
            $n = $this->getCentenas($millon) . "millones ";
        } else {
            $n = $this->getUnidades($millon) . "millon ";
        }
        return $n . $this->getMiles($miles);
    }
}
