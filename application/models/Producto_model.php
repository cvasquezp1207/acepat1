<?php

include_once "Model.php";

class Producto_model extends Model {

	public function init() {
		$this->set_schema("compra");
	}
	
	/**
	 * Obtener la cantidad disponible en unidades minimas. Para obtener el stock
	 * en otras unidades se debe hacer la conversion dividiendo esta cantidad por la
	 * cantidad de la unidad que se desea convertir.
	 * @param integer $idproducto
	 * @return integer numero que representa la cantidad disponible en unidades minimas
	 */
	public function stock($idproducto, $idalmacen = NULL) {
		$sql = "SELECT SUM(d.cantidad*u.cantidad_unidad_min*d.tipo_number) as stock
			FROM almacen.detalle_almacen d
			INNER JOIN compra.producto_unidad u ON u.idproducto=d.idproducto AND u.idunidad=d.idunidad
			WHERE d.estado = 'A' AND d.idproducto = ?";
		$param = array($idproducto);
		
		if($idalmacen != NULL) {
			$sql .= " AND d.idalmacen = ?";
			$param[] = $idalmacen;
		}
		
		$query = $this->db->query($sql, $param);
		
		if ($query->num_rows() > 0) {
			$row = $query->row();
			if(is_numeric($row->stock))
				return $row->stock;
		}
		
		return 0;
	}
	
	/**
	 * Obtener las unidades de medida asignados al producto
	 * @param integer $idproducto
	 * @return array resultset
	 */
	public function unidades($idproducto, $idunidad = FALSE) {
		$this->db
			->select("p.idunidad, u.descripcion, u.abreviatura, p.cantidad_unidad, p.cantidad_unidad_min")
			->where("p.idproducto", $idproducto);
		
		if($idunidad !== FALSE)
			$this->db->where("p.idunidad", $idunidad);
		
		$query = $this->db->where("u.estado", "A")
			->join("compra.unidad u", "u.idunidad=p.idunidad")
			->order_by("u.descripcion", "asc")
			->get("compra.producto_unidad p");
		return $query->result_array();
	}
	
	/**
	 * Obtener los precios de compra asignados al producto, segun unidad de medida, 
	 * moneda y sucursal
	 * @param integer $idproducto
	 * @param integer $idsucursal
	 * @param integer $idmoneda opcional
	 * @return array resultset
	 */
	public function precio_compra($idproducto, $idsucursal, $idmoneda=null) {
		$this->db
			->select("p.idunidad, u.descripcion, p.idmoneda, m.descripcion as moneda, p.precio")
			->where("p.idproducto", $idproducto)
			->where("p.idsucursal", $idsucursal);
		
		if($idmoneda != null) {
			$this->db->where("p.idmoneda", $idmoneda);
		}
		
		$query = $this->db->join("compra.unidad u", "u.idunidad=p.idunidad")
			->join("general.moneda m", "m.idmoneda=p.idmoneda")
			->get("compra.producto_precio_compra p");
		
		return $query->result_array();
	}
	
	/**
	 * Obtener los precios de venta asignados al producto
	 * @param integer $idproducto
	 * @param integer $idsucursal
	 * @return array resultset
	 */
	public function precio_venta($idproducto, $idsucursal) {
		$query = $this->db
			->select("p.idunidad, p.idmoneda, p.idtipo_precio, p.cantidad, p.precio, p.porcentaje")
			->where("p.idproducto", $idproducto)
			->where("p.idsucursal", $idsucursal)
			->order_by("idprecio", "asc")
			->get("compra.producto_precio_venta p");
		return $query->result_array();
	}
	
	/**
	 * Obtener el precio de compra en unidades segun el producto
	 
	 */
	public function get_precio_compra_unitario($idproducto, $idsucursal, $idunidad = NULL, $idmoneda = NULL) {
		if($idmoneda == NULL) {
			$idmoneda = 1; // soles
		}
		
		$sql = "select pc.precio/pu.cantidad_unidad_min as precio
			from compra.producto_precio_compra pc
			join compra.producto_unidad pu on pu.idproducto=pc.idproducto and pu.idunidad = pc.idunidad
			where pc.idproducto = '$idproducto' and pc.idsucursal = '$idsucursal'";
		if($idunidad != NULL) {
			$sql .= " and pc.idunidad = '$idunidad'";
		}
		if($idmoneda != NULL) {
			$sql .= " and pc.idmoneda = '$idmoneda'";
		}
		$sql .= " order by pu.cantidad_unidad_min, pu.idunidad limit 1";
		
		$query = $this->db->query($sql);
		if($query->num_rows() > 0) {
			$row = $query->row_array();
			return $row["precio"];
		}
		else {
			$sql = "select pc.precio/pu.cantidad_unidad_min as precio
				from compra.producto_precio_compra pc
				join compra.producto_unidad pu on pu.idproducto=pc.idproducto and pu.idunidad = pc.idunidad
				where pc.idproducto = '$idproducto' and pc.idsucursal = '$idsucursal'";
			if($idmoneda != NULL) {
				$sql .= " and pc.idmoneda = '$idmoneda'";
			}
			$sql .= " order by pu.cantidad_unidad_min, pu.idunidad limit 1";
			$query = $this->db->query($sql);
			if($query->num_rows() > 0) {
				$row = $query->row_array();
				return $row["precio"];
			}
		}
		
		return 0;
	}

	/**
	 * Obtener el precio de costo unitario de un producto, esta funcion es alternativo a
	 * la funcion "get_precio_compra_unitario", utilizamos nueva tabla para hacer el calculo.
	 */
	public function get_precio_costo_unitario($idproducto, $idsucursal, $idunidad = NULL, $idmoneda = NULL) {
		$precio = 0;
		$tipocambio = 1;
		$equivalencia = 1;
		
		// obtenemos el precio de costo unitario
		$sql = "select precio_compra
			from compra.producto_precio_unitario
			where idproducto = ? and idsucursal = ? and coalesce(precio_compra,0) > 0";
		$query = $this->db->query($sql, array($idproducto, $idsucursal));
		if($query->num_rows() > 0)
			$precio = $query->row()->precio_compra;
		else {
			// obtenemos precio de costo unitario desde el detalle de las compras
			$sql = "select dc.costo, c.idmoneda, c.cambio_moneda, coalesce(u.cantidad_unidad_min,dc.cantidad_um,1) as cant
				from compra.detalle_compra dc
				join compra.compra c on c.idcompra = dc.idcompra
				left join compra.producto_unidad u on u.idproducto=dc.idproducto and u.idunidad=dc.idunidad
				where dc.estado='A' and dc.idproducto = ? and c.idsucursal = ?
				order by dc.iddetalle_compra desc 
				limit 1";
			$query = $this->db->query($sql, array($idproducto, $idsucursal));
			if($query->num_rows() > 0) {
				$row = $query->row_array();
				
				$precio = ($row["idmoneda"] == 1) ? $row["costo"] : $row["costo"]*$row["cambio_moneda"];
				if($row["cant"] > 0)
					$precio /= $row["cant"];
			}
		}
		
		// obtenemos la equivalencia segun la unidad de medida
		if($idunidad != NULL) {
			$sql = "select cantidad_unidad_min
				from compra.producto_unidad
				where idproducto = ? and idunidad = ?";
			$query = $this->db->query($sql, array($idproducto, $idunidad));
			if($query->num_rows() > 0)
				$equivalencia = $query->row()->cantidad_unidad_min;
		}
		
		// obtenemos el tipo de cambio de la moneda
		if($idmoneda != NULL) {
			$sql = "select valor_cambio from general.moneda where idmoneda = ?";
			$query = $this->db->query($sql, array($idmoneda));
			if($query->num_rows() > 0)
				$tipocambio = $query->row()->valor_cambio;
		}
		
		return $precio*$tipocambio*$equivalencia;
	}
	
	/**
	 * Obtener el precio de venta unitario de un producto, utilizamos nueva tabla para hacer el calculo.
	 */
	public function get_precio_venta_unitario($idproducto, $idsucursal, $idunidad = NULL, $idmoneda = NULL) {
		$precio = 0;
		$tipocambio = 1;
		$equivalencia = 1;
		
		// obtenemos el precio de venta unitario desde nueva tabla
		$sql = "select precio_venta
			from compra.producto_precio_unitario
			where idproducto = ? and idsucursal = ? and coalesce(precio_venta,0) > 0";
		$query = $this->db->query($sql, array($idproducto, $idsucursal));
		if($query->num_rows() > 0)
			$precio = $query->row()->precio_venta;
		else {
			// obtenemos precio de venta unitario desde el detalle de ventas
			$sql = "select dc.precio, c.idmoneda, c.cambio_moneda, coalesce(dc.cantidad_um,u.cantidad_unidad_min,1) as cant
				from venta.detalle_venta dc
				join venta.venta c on c.idventa = dc.idventa
				left join compra.producto_unidad u on u.idproducto=dc.idproducto and u.idunidad=dc.idunidad
				where dc.estado='A' and dc.idproducto = ? and c.idsucursal = ?
				order by dc.iddetalle_venta desc 
				limit 1";
			$query = $this->db->query($sql, array($idproducto, $idsucursal));
			if($query->num_rows() > 0) {
				$row = $query->row_array();
				$precio = ($row["idmoneda"] == 1) ? $row["precio"] : $row["precio"]*$row["cambio_moneda"];
				if($row["cant"] > 0)
					$precio /= $row["cant"];
			}
			else {
				// obtenemos el precio de venta unitario desde tabla precios de venta
				$sql = "select pc.precio, pu.cantidad_unidad_min as cant_um, pc.cantidad, pc.idmoneda, m.valor_cambio
					from compra.producto_precio_venta pc
					join compra.producto_unidad pu on pu.idproducto = pc.idproducto and pu.idunidad = pc.idunidad
					join general.moneda m on m.idmoneda = pc.idmoneda
					where pc.idproducto = ? and pc.idsucursal = ? and coalesce(pc.cantidad,0)>0
					and coalesce(pu.cantidad_unidad_min,0) > 0
					order by pu.cantidad_unidad_min, pc.cantidad, pu.idunidad limit 1";
				$query = $this->db->query($sql, array($idproducto, $idsucursal));
				if($query->num_rows() > 0) {
					$row = $query->row_array();
					$precio = $row["precio"] / $row["cant_um"] / $row["cantidad"];
					if($row["idmoneda"] != 1)
						$precio = $precio * $row["valor_cambio"];
				}
			}
		}
		
		// obtenemos la equivalencia segun la unidad de medida
		if($idunidad != NULL) {
			$sql = "select cantidad_unidad_min
				from compra.producto_unidad
				where idproducto = ? and idunidad = ?";
			$query = $this->db->query($sql, array($idproducto, $idunidad));
			if($query->num_rows() > 0)
				$equivalencia = $query->row()->cantidad_unidad_min;
		}
		
		// obtenemos el tipo de cambio de la moneda
		if($idmoneda != NULL) {
			$sql = "select valor_cambio from general.moneda where idmoneda = ?";
			$query = $this->db->query($sql, array($idmoneda));
			if($query->num_rows() > 0)
				$tipocambio = $query->row()->valor_cambio;
		}
		
		return $precio*$tipocambio*$equivalencia;
	}
}

?>