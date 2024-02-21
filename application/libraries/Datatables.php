<?php
 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Grilla con server side utilizado en el plugin DataTables de jQuery
 */

class Datatables {
    /**
     * Array que contiene el nombre de los campos de la tabla de la base de datos, 
     * del cual se recogeran los registros para mostrarlo en la tabla
     * e.g.
     *		...
     *		$aColumns = array( 'alu_nombre', 'alu_descripcion', 'alu_codigo', 'alu_telefono' );
     *		...
     */
    private $aColumns;

    /**
     * variable que contiene el Id de la tabla
     * e.g.
     *		$sIndexColumn = "alu_id";
     */
    public $sIndexColumn;

    /**
     * contiene el nombre de la tabla de la base de datos
     * e.g
     *		$sTable = "alumno";
     */
    private $sTable;

    private $sSchema;

    private $sFullTable;

    /**
     * se arma la consulta sql por partes
     */
    private $sLimit;
    private $sOrder;
    private $sWhere;
    private $rResult; // resultado de la consulta

    /**
     * numero total de registros
     */
    private $iTotal;

     /**
      * numero total de registros encontrados
      */
    private $iFilteredTotal;

    private $REQUEST; // variable que contiene el request solo para el caso de la paginacion

    private $modelAdapter; // adaptador de modelo, para realizar conexion y consulta a la BD

    public $queryRecords; // contiene la consulta para obtener los registros
    public $queryNumRows; // contiene la consulta para obtener el numero de registros
    public $queryCounter; // contiene la consulta para obtener el total de registros

    // nuevas variables para la clase grilla
    private $data = array();
    private $urlFile = "./index.php";
    private $isStyle = false;
    private $callback = "";
    private $popup = false;
    private $aColumnsSelected = array();
    private $aOrder = array('column'=>0, 'order'=>'asc');

    private $oQueryBuilder = array();
    public $sWhereAdicional;
	
	private $fnSubgrid = null;
	private $performClick = false;
	
	private $bInfo = true;
	private $bFilter = true;
	
    /**
     * constructor de la clase.
     */
	public function __construct() {
		$this->urlFile = base_url("paginate/get_data");
    }

    /**
     * Asignamos el modelo de la tabla sobre el cual se generara la grilla
     * @param AbstractModel $modelAdapter
     */
    public function setModel(CI_Model $modelAdapter) {
        $this->modelAdapter = $modelAdapter;
        $this->init();
    }
	
    /**
     * Asigna el nombre de la tabla a extraer los datos para la grilla
     * @param string $table
     */
    public function setTable($table) {
        $this->sTable = $table;
    }

    /**
     * Asigna el esquema en el que se encuentra la tabla
     * @param string $schema
     */
    public function setSchema($schema) {
        $this->sSchema = $schema;
    }
	
    /**
     * Establece el array de columnas a mostrar en la grilla
     * @param array $columns
     */
    public function setColumns(array $columns) {
        $this->aColumnsSelected = $columns;
    }

    /**
     * Metodo para indicar el campo PK de la tabla actual.
     * @param string $indexColumn
     */
    public function setIndexColumn($indexColumn) {
        $this->sIndexColumn = $indexColumn;
    }

    /**
     * Asignamos todo el request que viene del cliente
     * @param array $request
     */
    public function setRequest($request) {
        $this->REQUEST = $request;
    }
	
    /**
     * Metodo para validar los datos y armar la consulta SQL
     * que obtendra los datos para la grilla
     */
    public function prepareQuery() {
        $this->checkRequiredData();
        $this->limit();
        $this->order();
        $this->where_filter();
        $this->generateQuery();
    }
	
    /**
     * Metodo que verifica si los datos necesarios han sido asignados, 
     * antes de armar las consultas SQL
     */
    private function checkRequiredData() {
        if(empty($this->sTable)) {
            $this->sTable = $this->modelAdapter->get_table_name();
        }
        if(empty($this->sSchema)) {
            $this->sSchema = $this->modelAdapter->get_schema();
        }
        if(empty($this->sFullTable)) {
            $this->sFullTable = $this->modelAdapter->get_full_table_name();
        }
        if(empty($this->aColumns)) {
            $this->aColumns = $this->modelAdapter->get_columns();
        }
        if(empty($this->sIndexColumn)) {
			$pk = $this->modelAdapter->get_pk(false);
			// if(is_array($pk)) {
				// $pk = array_shift($pk);
			// }
            $this->sIndexColumn = $pk;
        }
        if(empty($this->aColumnsSelected)) {
            $this->aColumnsSelected = $this->aColumns;
        }
    }
	
    /**
     * Se arma el limit de la consulta sql, para la paginacion de la grilla
     */
    private function limit() {
        $this->sLimit = "";
        if ( isset( $this->REQUEST['iDisplayStart'] ) && $this->REQUEST['iDisplayLength'] != '-1' ) {
            $this->sLimit = "LIMIT ".pg_escape_string( $this->REQUEST['iDisplayLength'] )." OFFSET ".pg_escape_string( $this->REQUEST['iDisplayStart'] );
        }
    }
	
    /**
     * Se arma la consulta order, para ordenar las columnas de la grilla
     */
    private function order() {
        $this->sOrder = "";
        if ( isset( $this->REQUEST['iSortCol_0'] ) ) {
//            if( $this->REQUEST['iSortCol_0'] >= count($this->aColumns) ) {$this->REQUEST['iSortCol_0'] = 0;}
            // if( $this->REQUEST['iSortCol_0'] >= count($this->aColumnsSelected) ) {$this->REQUEST['iSortCol_0'] = 0;}

            $this->sOrder = "ORDER BY  "; // estos dos espacio al final dejarlo asi
			// with_subgrid
			$cols = explode(",", $this->REQUEST['sColumns']);

            for ( $i=0 ; $i < intval( $this->REQUEST['iSortingCols'] ) ; $i++ ) {
                if ( $this->REQUEST[ 'bSortable_'.intval($this->REQUEST['iSortCol_'.$i]) ] == "true" ) {
                    // $this->sOrder .= $this->aColumnsSelected[ intval( $this->REQUEST['iSortCol_'.$i] ) ]."
                    $this->sOrder .= $cols[ intval( $this->REQUEST['iSortCol_'.$i] ) ]."
                        ".pg_escape_string( $this->REQUEST['sSortDir_'.$i] ) .", ";
                }
            }

            $this->sOrder = substr_replace( $this->sOrder, "", -2 ); // si sustituyen los dos caracteres del final

            if ( $this->sOrder == "ORDER BY" ) {
                $this->sOrder = "";
            }
        }
    }
	
	
    /**
     * Se arma la consulta SQL, para la busqueda de registros en la grilla
     */
    private function where_filter() {
        $this->sWhere = "";
        // busqueda simple
        if ( isset($this->REQUEST['sSearch']) && $this->REQUEST['sSearch'] != "" ) {
            $this->sWhere = "WHERE (";

            for ( $i=0 ; $i < count($this->aColumnsSelected) ; $i++ ) {
				$column = $this->aColumnsSelected[$i];
				
				if(strripos($column, ' as ') !== false) {
					$column = str_ireplace(' as ', ';', $column);
					list($column, ) = explode(';', $column);
				}
				
                // $this->sWhere .= "UPPER(CAST(".$this->aColumnsSelected[$i]." as text)) LIKE UPPER('%".pg_escape_string( $this->REQUEST['sSearch'] )."%') OR ";
                $this->sWhere .= "UPPER(CAST(".$column." as text)) LIKE UPPER('%".pg_escape_string( $this->REQUEST['sSearch'] )."%') OR ";
            }

            $this->sWhere = substr_replace( $this->sWhere, "", -3 );
            $this->sWhere .= ')';
        }

        // busqueda avanzada
        for ( $i=0 ; $i<count($this->aColumnsSelected) ; $i++ ) {
            if ( isset($this->REQUEST['bSearchable_'.$i]) && $this->REQUEST['bSearchable_'.$i] == "true" && $this->REQUEST['sSearch_'.$i] != '' ) {

                if ( $this->sWhere == "" ) {
                    $this->sWhere = "WHERE ";
                }
                else {
                    $this->sWhere .= " AND ";
                }
				
				$column = $this->aColumnsSelected[$i];
				
				if(strripos($column, ' as ') !== false) {
					$column = str_ireplace(' as ', ';', $column);
					list($column, ) = explode(';', $column);
				}

                // $this->sWhere .= "UPPER(CAST(".$this->aColumnsSelected[$i]." as text)) LIKE UPPER('%".pg_escape_string($this->REQUEST['sSearch_'.$i])."%') ";
                $this->sWhere .= "UPPER(CAST(".$column." as text)) LIKE UPPER('%".pg_escape_string($this->REQUEST['sSearch_'.$i])."%') ";
            }
        }

        // adicional codigo para enviar cualquier otro dato
        if( isset($this->REQUEST['extWhereColumn']) ) {
            $aColumns = $this->REQUEST['extWhereColumn'];
            $aValues = $this->REQUEST['extWhereValue'];

            if ( $this->sWhere == "" ) {
                $this->sWhere = "WHERE ";
            }
            else {
                $this->sWhere .= " AND ";
            }

            $this->sWhere .= "UPPER(CAST(".$aColumns." as text)) LIKE UPPER('%".pg_escape_string($aValues)."%') ";
        }
    }
		
    /**
     * Armamos las consultas para obtener los datos, el numero de registros 
     * y demas para llenar la grilla.
     */
    private function generateQuery() {
//         //obsoleto, ya no se usa
//        if( isset($this->REQUEST['extWhereColumn']) ) {
//                $clave = array_search($this->REQUEST['extWhereColumn'], $this->aColumns); 
//                unset($this->aColumns[ $clave ]);
//        }

        $where = '';
        $whereCount = '';
        if(!empty($this->sWhereAdicional)) {
            if(empty($this->sWhere)) {
                $where = 'WHERE ' . $this->sWhereAdicional;
            }
            else {
                $where .= ' AND ' . $this->sWhereAdicional;
            }
            $whereCount = 'WHERE ' . $this->sWhereAdicional;
        }

        $this->queryRecords = "
            SELECT ".str_replace(" , ", " ", implode(", ", $this->aColumns))."
            FROM   {$this->sFullTable}
            {$this->sWhere} $where
            {$this->sOrder}
            {$this->sLimit}
        ";

        $this->queryNumRows = "
            SELECT count(*) as cantidad
            FROM   {$this->sFullTable}
            {$this->sWhere} $where
        ";

        if(isset($this->REQUEST['isView'])) {
            if($this->REQUEST['isView'] == true) {
                $this->queryCounter = "
                    SELECT COUNT(*) as cantidad
                    FROM   {$this->sFullTable} $whereCount
                ";
            }
            else {
                $this->queryCounter = "
                    SELECT COUNT(".$this->sIndexColumn.") as cantidad
                    FROM   {$this->sFullTable} $whereCount
                ";
            }
        }
        else {
            $this->queryCounter = "
                SELECT COUNT(".$this->sIndexColumn.") as cantidad
                FROM   {$this->sFullTable} $whereCount
            ";
        }
    }
	
    /**
     * Ejecuta las consultas previamente armadas, para obtener los registros
     * a llenar en la grilla
     */
    public function executeQuery() {

        $rs = $this->modelAdapter->query($this->queryRecords);
		$this->rResult = $rs->result_array();

        $rs = $this->modelAdapter->query($this->queryNumRows);
        $this->iFilteredTotal = $rs->row()->cantidad;

        $rs = $this->modelAdapter->query($this->queryCounter);
        $this->iTotal = $rs->row()->cantidad;
    }
	
	/**
	 * Antiguo metodo para obtener los datos 
	 */
	public function getRecordsOld($popup=false, $withRowsId=false) {
        $output = array(
            "sEcho" => intval($this->REQUEST['sEcho']),
            "iTotalRecords" => intval($this->iTotal),
            "iTotalDisplayRecords" => intval($this->iFilteredTotal),
            "aaData" => array()
        );
		$popup = false;
        if( !empty($this->rResult) ) {
            $cantidad = (!empty($this->REQUEST['iColumns'])) ? ($this->REQUEST['iColumns'] - 1) : count($this->aColumnsSelected); 

            foreach ( $this->rResult as $aRow ) {
                $row = array();

                $i = 0;
                do {
                    $text = '';
					
                    if($i == 0) {
						if($withRowsId) {
							$row["DT_RowId"] = "row_".$aRow[ $this->sIndexColumn ];
						}
						else {
							$text = '<input type="hidden" name="pkindex[]" class="pkindex" value="'.$aRow[ $this->sIndexColumn ].'" />';
						}
                    }
					
					$column = $this->aColumnsSelected[$i];
					
					if(strpos($column, '||') !== false) {
						$columns = explode('||', $column);
						
						if( !empty($columns) ) {
							foreach($columns as $column) {
								if( array_key_exists($column, $aRow) ) {
									// $text .= htmlentities($aRow[ $column ]);
									$text .= $aRow[ $column ];
								}
								else {
									if(strpos($column, "'") !== false) {
										$column = str_replace("'", "", $column);
									}
									$text .= $column;
								}
							}
						}
					}
					else if(strripos($column, ' as ') !== false) {
						$column = str_ireplace(' as ', ';', $column);
						list(, $column) = explode(';', $column);
						
						// $text .= htmlentities($aRow[ $column ]);
						$text .= $aRow[ $column ];
					}	
					else {
						// $text .= htmlentities($aRow[ $column ]);
						$text .= $aRow[ $column ];
					}
					
                    $row[$i] = $text;
                    // $row[] = $text . htmlentities($aRow[ $this->aColumnsSelected[$i] ]);
                    // $row[] = $text . $aRow[ $this->aColumnsSelected[$i] ];
                    $i ++;
                }
                while($i < $cantidad);

                if(!$popup) {
//                    $row[] = '<span title="Editar" class="edit_row">&nbsp;</span>';
//                    $row[] = '<span title="Eliminar" class="delete_row">&nbsp;</span>';
                }
                else {
                    $row[$i] = '<span title="Seleccionar" class="selected_row">&nbsp;</span>';
                }

                if($i < count($this->aColumnsSelected)) {
					
                    for($j=$i; $j < count($this->aColumnsSelected); $j++) {
						$column = $this->aColumnsSelected[$j];
						
						$text = '';
						
						if(strpos($column, '||') !== false) {
							$columns = explode('||', $column);
							
							if( !empty($columns) ) {
								foreach($columns as $column) {
									if( array_key_exists($column, $aRow) ) {
										// $text .= htmlentities($aRow[ $column ]);
										$text .= $aRow[ $column ];
									}
									else {
										if(strpos($column, "'") !== false) {
											$column = str_replace("'", "", $column);
										}
										$text .= $column;
									}
								}
							}
						}
						else if(strripos($column, ' as ') !== false) {
							$column = str_ireplace(' as ', ';', $column);
							list(, $column) = explode(';', $column);
							
							// $text .= htmlentities($aRow[ $column ]);
							$text .= $aRow[ $column ];
						}
						else {
							// $text .= htmlentities($aRow[ $column ]);
							$text .= $aRow[ $column ];
						}
					
                        // $row[] = $aRow[ $this->aColumnsSelected[$j] ];
						if($popup) {
							$row[($j+1)] = $text;
						}
						else {
							$row[$j] = $text;
						}
                    }
                }

                $output['aaData'][] = $row;
            }
        }

        return json_encode( $output );
    }
	
    /**
     * Metodo para devolver los registros al cliente para procesar la grilla
     * @param boolean $popup por defecto false, true si la ventana es un popup
     * @return string los registros codificados en formato json
     */
    public function getRecords($popup=false, $withRowsId=false) {
        $output = array(
            "sEcho" => intval($this->REQUEST['sEcho']),
            "iTotalRecords" => intval($this->iTotal),
            "iTotalDisplayRecords" => intval($this->iFilteredTotal),
            "aaData" => array()
        );
		$popup = false;
		
        if( !empty($this->rResult) ) {
			$aaData = array();
			
            foreach ( $this->rResult as $aRow ) {
				$this->_fix_date_format($aRow);
				
				$aRow["DT_RowId"] = "row_".$aRow[ $this->sIndexColumn ];
				$aRow["pkey"] = $aRow[ $this->sIndexColumn ];
				if(!empty($this->REQUEST["with_subgrid"])) {
					$aRow["DT_RowClass"] = "dt_subgrid";
				}
                $aaData[] = $aRow;
            }
			
			$output["aaData"] = $aaData;
        }

        return json_encode( $output );
    }
	
	private function _fix_date_format(&$aRow) {
		if(is_array($aRow)) {
			foreach($aRow as $k=>$v) {
				if(is_string($k)) {
					if( preg_match("/fecha/i", $k) ) {
						if(strpos($v, "-") !== FALSE) {
							$aRow[$k] = fecha_es($v, TRUE);
						}
					}
				}
			}
		}
	}
	
    /**
     * Metodo para crear la tabla de la grilla que contendra los registros;
     * el parametro $columnsName es un array 2D que contiene el 
     * nombre de la columna y su tamanio 
     * e.g.
     *		$columnasName = array(
     *			array('columna1', '20%'),
     *			array('columna2', '25%'),
     *			array('columna3', '15%'),
     *			array('columna4', '10%')
     *		);
     * 
     * @param array $columnsName nombre de las columnas que apareceran en la grilla
     * @param string $idTable id del tag TABLE en el DOM
     * @param boolean $popup indica si la grilla es una ventana popup
     * @return string html para la tabla de la grilla
     */
    public function createTable($columnsName = array(), $idTable = "", $class = "table-striped table-bordered table-hover table-green") {
		if(empty($idTable)) {
			$idTable = $this->modelAdapter->get_table_name();
		}
		$tableRealId = $idTable;
		$cls = "";
		
		if($this->popup) {
			$cls = "grilla_popup";
			$idTable .= "_popup";
		}
		
        $thead = "";
        $tfoot = "";
		
		if(!empty($this->fnSubgrid)) {
			// $thead .= "<th></th>";
			// $tfoot .= "<th></th>";
		}
		
        if(empty($columnsName)) {
            if(empty($this->aColumnsSelected)) {$this->checkRequiredData();}

            $columnsName = $this->aColumnsSelected;

            foreach($columnsName as $ft) {
                // $thead .= '<th>'.ucfirst(strtolower($ft)).'</th>';
                $thead .= '<th>'.$ft.'</th>';
                // $tfoot .= '<th>'.ucfirst(strtolower($ft)).'</th>';
                $tfoot .= '<th>'.$ft.'</th>';
            }
        }
        else {
            foreach($columnsName as $ft) {
				if( is_array($ft) ) {
					// $thead .= '<th width="'.$ft[1].'">'.ucfirst(strtolower($ft[0])).'</th>';
					$thead .= '<th width="'.$ft[1].'">'.$ft[0].'</th>';
					// $tfoot .= '<th>'.ucfirst(strtolower($ft[0])).'</th>';
					$tfoot .= '<th>'.$ft[0].'</th>';
				}
				else {
					// $thead .= '<th>'.ucfirst(strtolower($ft)).'</th>';
					$thead .= '<th>'.$ft.'</th>';
					// $tfoot .= '<th>'.ucfirst(strtolower($ft)).'</th>';
					$tfoot .= '<th>'.$ft.'</th>';
				}
            }
        }
        $i = 0;
        if(!$this->popup) {
//            $thead .= "<th class='aditional' width='5%'>&nbsp;</th><th class='aditional' width='5%'>&nbsp;</th>";
//            $tfoot .= "<th></th><th></th>";
        }
        else {
            // $i = 1;
            // $thead .= "<th width='5%'>&nbsp;</th>";
            // $tfoot .= "<th></th>";
        }

        $html = '<table id="dt'.$idTable.'" realid="'.$tableRealId.'" class="table '.$class.' '.$cls.'">';
        $html .= '<thead><tr>'.$thead.'</tr></thead>';
        $html .= '<tbody><tr><td colspan="'.(count($columnsName) + $i).'" class="dataTables_empty">Loading data from server</td></tr></tbody>';
        //$html .= '<tfoot><tr>'.$tfoot.'</tr></tfoot></table>';
        $html .= '</table>';
        
		if(!$this->popup) {
			$html .= '<script>_default_grilla="'.$tableRealId.'";</script>';
		}

        return $html;
    }
        
    /**
     * Metodo para establecer los valores por defecto a la grilla
     */
    private function init() {
        $this->setData("controller", "paginate");
        if(!empty($this->modelAdapter)) {
            $this->setData("table_name", $this->modelAdapter->get_table_name());
            $this->setData("schema", $this->modelAdapter->get_schema());
            $this->setData("popup_enable", false);
            $this->setData("with_subgrid", false);
        }
    }
        
    /**
     * Meetodo para enviar datos adicionales a la grilla
     * @param string $name nombre de las columnas que apareceran en la grilla
     * @param string $value nombre de las columnas que apareceran en la grilla
     */
    public function setData($name, $value) {
        $this->data[$name] = $value;
    }

    /**
     * Metodo para indicar si la tabla tendra estilos
     * @param boolean $style
     */
    public function setStyle($style) {
        $this->isStyle = $style;
    }

    /**
     * Metodo para indicar la url de donde cogera los datos la grilla
     * @param string $url
     */
    public function setURL($url) {
        $this->urlFile = $url;
    }

    /**
     * Metodo para indicar el nombre de la funcion callback en javascript 
     * que sera ejecutada al momento de llenar los datos en la grilla
     * @param string $name nombre de la funcion
     */
    public function setCallback($name) {
        $this->callback = $name;
    }

    /**
     * Metodo para indicar si la grilla sera para una ventana popup
     * @param boolean $popup
     */
    public function setPopup($popup) {
        $this->popup = $popup;
        if($popup) {
            $this->setData("popup_enable", true);
        }
    }
	
	/**
	 * Metodo para indicar el campo por el cual ordenar los filas de la grilla
	 */
	public function order_by($column, $type = 'desc') {
		$index = 0;
		if($type != 'desc' && $type != 'asc')
			$type = 'desc';
		
		if(!empty($column)) {
			if(is_numeric($column)) {
				$index = intval($column);
				if($index > (count($this->aColumnsSelected) - 1))
					$index = 0;
			}
			else {
				if(in_array($column, $this->aColumnsSelected)) {
					$index = array_search($column, $this->aColumnsSelected);
				}
			}
		}
		
		$this->aOrder['column'] = $index;
		$this->aOrder['order'] = $type;
	} 
	 
	
    /**
     * Metodo para devolver el scrip generado para la grilla popup
     * @param string $idTable id del tag TABLE en el DOM
     * @return string script generado para la grilla
     */
    public function createScriptPopup($idTable = "") {
		if(empty($idTable)) {
			$idTable = $this->modelAdapter->get_table_name();
		}
		if($this->popup) {
			$idTable .= "_popup";
		}
		
		$this->setData('columns', json_encode($this->aColumnsSelected));
        if(!empty($this->oQueryBuilder)) {
            // $this->setData('where', implode(" AND ", $this->oQueryBuilder));
            $this->setData('where', json_encode($this->oQueryBuilder));
		}
	
        $html = "$(document).ready(function() { $('#dt" . $idTable . "').dataTable( {";

        $html .= "'sPaginationType': 'full_numbers',";
        $html .= "'oLanguage': {'sLengthMenu': 'Mostrar _MENU_ registros por p&aacute;gina','sZeroRecords': 'No existen ninguna coincidencia','sInfo': 'Mostrando _START_ a _END_ de _TOTAL_ registros','sInfoEmpty': 'Mostrando 0 de 0 registros','sInfoFiltered': '(Filtrando de _MAX_ registros totales)',";
        $html .= "'sSearch': 'Buscar', 'oPaginate': {'sPrevious': '&lsaquo;', 'sNext': '&rsaquo;', 'sFirst': '&laquo;', 'sLast': '&raquo;'}}, ";
        if($this->isStyle) {
            $html .= "'bJQueryUI': true,";
        }

//        $html .= "'aoColumnDefs': [ {'sClass': 'center','aTargets': [ 0 , -1";
//        if(!$this->popup) {
//            $html .= ", -2 ";
//        }
//        $html .= "]} ],";

        // $html .= "'aoColumnDefs': [ {'sClass': 'center','aTargets': [ 0 ]} ],";
        $html .= "'bLengthChange': false,";
//        $html .= "'aLengthMenu': [10, 20],";
		$html .= "'responsive': true,";
        // $html .= "'dom': 'T<\"clear\">lfrtip',";
		$html .= "'dom': 'T<\"row\"<\"col-sm-5\"l><\"col-sm-3\"r><\"col-sm-4\"f>><\"row\"<\"col-sm-12\"t>><\"row\"<\"col-sm-6\"i><\"col-sm-6\"p>>',";
        $html .= "'bProcessing': true,";
        $html .= "'bServerSide': true,";
        $html .= "'sAjaxSource': '" . $this->urlFile . "',";
        $html .= '"aaSorting": [[ 0, "asc" ]],';

        if(!empty($this->data)) {
            $html .= "'fnServerParams': function(aoData) {aoData.push(";

            foreach($this->data as $key=>$value) {
                $html .= "{'name': '" . $key . "', 'value': '" . addslashes($value) . "'}, ";
            }

            $html = substr_replace($html, "", -2);

            $html .= ");}";
        }
        else {
            $html = substr_replace($html, "", -1);
        }

        $html .= ", 'oTableTools': {'sRowSelect': 'single', 'aButtons':false}";

        if(!empty($this->callback)) {
            $html .= ", 'fnRowCallback': function( nRow, aData, iDisplayIndex ) {" . $this->callback . "(nRow, aData, iDisplayIndex);return nRow;}";
        }

        $html .= "} );} );";

        return $html;
    }
	
	public function showInfo($bool = true) {
		$this->bInfo = $bool;
	}
	
	public function showFilter($bool = true) {
		$this->bFilter = $bool;
	}
	
	public function createScript($idTable = "", $lengthChange = true) {
		if(empty($idTable)) {
			$idTable = $this->modelAdapter->get_table_name();
		}
		if($this->popup) {
			$idTable .= "_popup";
		}
		
        $this->setData('columns', json_encode($this->aColumnsSelected));
		$this->setData('index_column', $this->sIndexColumn);
		
        if(!empty($this->oQueryBuilder)) {
            // $this->setData('where', implode(" AND ", $this->oQueryBuilder));
			$this->setData('where', json_encode($this->oQueryBuilder));
		}

        $html = "var oTable_dt".$idTable."; $(document).ready(function() { oTable_dt".$idTable." = $('#dt" . $idTable . "').dataTable( {";

        // $html .= "'sPaginationType': 'full_numbers',";
		
		$html .= "'oLanguage': {'sLengthMenu': '_MENU_ registros','sZeroRecords': 'No existen registros','sInfo': '_START_ a _END_ de _TOTAL_ registros','sInfoEmpty': '0 de 0 registros','sInfoFiltered': '(_MAX_ registros totales)',";
		// $html .= "'sSearch': 'Buscar', 'oPaginate': {'sPrevious': '&lsaquo;', 'sNext': '&rsaquo;', 'sFirst': '&laquo;', 'sLast': '&raquo;'}}, ";
		$html .= "'sSearch': '<i class=\"fa fa-search\"></i> ', 'oPaginate': {'sPrevious': '<i class=\"fa fa-chevron-left\"></i>', 'sNext': '<i class=\"fa fa-chevron-right\"></i>'}}, ";
		// $html .= "'sSearch': 'Buscar'}, ";
		
		if($this->bInfo == false) {
			$html .= "'bInfo':false,";
		}
		
		if($this->bFilter == false) {
			$html .= "'bFilter':false,";
		}
		
        if($this->isStyle) {
           $html .= "'bJQueryUI': true,";
        }

//        $html .= "'aoColumnDefs': [ {'sClass': 'center','aTargets': [ 0 , -1";
//        if(!$this->popup) {
//            $html .= ", -2 ";
//        }
//        $html .= "]} ],";
//
//        $html .= "'aoColumnDefs': [ {'sClass': 'center','aTargets': [ 0 ]} ],";
		if( $lengthChange ) {
			$html .= "'aLengthMenu': [10, 25, 50, 100],";
		}
		else {
			$html .= "'bLengthChange': false,";
		}
		
		$cols = array();
		if(!empty($this->fnSubgrid)) {
			// $cols[] = array("mData"=>null, "sDefaultContent"=>"", "bSearchable"=>false, "bSortable"=>false);
		}
		foreach($this->aColumnsSelected as $c) {
			$cols[] = array("mDataProp"=>$c, "sName"=>$c, "mData"=>$c);
		}
		
        $html .= "'responsive': true,";
		// $html .= "'dom': 'T<\"clear\">lfrtip',";
		if($lengthChange) {
			$html .= "'dom': 'T<\"row\"<\"col-sm-5\"l><\"col-sm-3\"r><\"col-sm-4\"f>>".
				"<\"row\"<\"col-sm-12\"t>>".
				"<\"row\"<\"col-sm-6\"i><\"col-sm-6\"p>>',";
		}
		else {
			// $html .= "'dom': 'T<\"row\"<\"col-sm-5\"f><\"col-sm-3\"r><\"col-sm-4\"l>><\"row\"<\"col-sm-12\"t>><\"row\"<\"col-sm-6\"i><\"col-sm-6\"p>>',";
			$html .= "'dom': 'T<\"row\"<\"col-sm-5\"><\"col-sm-3\"r><\"col-sm-4\"f>>".
				"<\"row\"<\"col-sm-12\"t>>".
				"<\"row\"<\"col-sm-6\"i><\"col-sm-6\"p>>',";
		}
        $html .= "'bProcessing': true,";
        $html .= "'bServerSide': true,";
        $html .= "'sAjaxSource': '" . $this->urlFile . "',";
		$html .= "'aoColumns': " . json_encode($cols) . ",";
        $html .= '"aaSorting": [[ '.$this->aOrder['column'].', "'.$this->aOrder['order'].'" ]],';

        if(!empty($this->data)) {
            $html .= "'fnServerParams': function(aoData) {aoData.push(";

            foreach($this->data as $key=>$value) {
                $html .= "{'name': '" . $key . "', 'value': '" . addslashes($value) . "'}, ";
            }

            $html = substr_replace($html, "", -2);

            $html .= ");}";
        }
        else {
            $html = substr_replace($html, "", -1);
        }

        $html .= ", 'oTableTools': {'sRowSelect': 'single', 'aButtons':false}";

        if(!empty($this->callback)) {
            $html .= ", 'fnRowCallback': function( nRow, aData, iDisplayIndex ) {" . $this->callback . "(nRow, aData, iDisplayIndex);return nRow;}";
        }
		
		if(!empty($this->fnSubgrid)) {
			$cols = $this->aColumnsSelected;
			$field = array_shift($cols);
			$html .= ", 'fnCreatedRow': function( nRow, aData, iDisplayIndex ) {
				$('td:eq(0)', nRow).html( '<span class=\"dt_icon_subgrid fa fa-angle-right\">&nbsp;&nbsp;</span> ' + aData['$field'] );
				$('td:eq(0) span.dt_icon_subgrid', nRow).on('click', function(ev) {
					ev.stopPropagation();
					if ($(this).hasClass('fa-angle-right')) {
						" . $this->fnSubgrid . "(oTable_dt".$idTable.", nRow, aData, iDisplayIndex);
					} else {
						oTable_dt".$idTable.".fnClose(nRow);
					}
					$(this).toggleClass('fa-angle-right fa-angle-down');
				});";
			// if($this->performClick) {
				// $html .= "$(nRow).on('click', function() {
					// $('td:eq(0) span.dt_icon_subgrid', this).trigger('click');
				// });";
			// }
			$html .= "}";
		}

        $html .= "} );} );";

        return $html;
    }
    
    /**
     * Metodo que devuelve la consulta SQL para obtener los datos de los 
     * registros
     * @return string
     */
    public function getQuery() {
        return $this->queryRecords;
    }
	
	/**
	 * Asignar filtros where al sql para obtener los datos
	 * @param $columna
	 * @param $simbolo
	 * @param $valor
	 */
	public function where($columna, $simbolo, $valor) {
		// $this->oQueryBuilder[] = "$columna $simbolo ".$this->modelAdapter->escape($valor);
		$this->oQueryBuilder[] = array("column"=>$columna, "simbol"=>trim($simbolo), "value"=>$valor);
	}
    
    /**
     * Metodo para indicar un where adicional a la tabla, para filtrar 
     * los registros, empleado cuando se obtienen desde el servidor.
     * @param string $where
     */
    public function setWhere($where) {
		if(is_array($where)) {
			$arrWhere = array();
			if(!empty($where)) {
				foreach($where as $val) {
					if(!empty($val["column"]) && !empty($val["simbol"]) && isset($val["value"])) {
						$arrWhere[] = $val["column"].' '.$val["simbol"].' '.$this->modelAdapter->escape($val["value"]);
					}
				}
			}
			$where = implode(" AND ", $arrWhere);
		}
        $this->sWhereAdicional = trim($where);
    }
	
	public function setSubgrid($fnsubgrid, $clickOnTR = false) {
		$this->fnSubgrid = $fnsubgrid;
		$this->performClick = $clickOnTR;
		if(!empty($fnsubgrid)) {
			$this->setData("with_subgrid", true);
		}
	}
}

/* End of file Datatables.php */