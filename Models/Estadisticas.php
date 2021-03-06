<?php namespace Models;

/**
* Metodos de las consultas principales de PAINRA
*/
class Estadisticas extends Conexion
{

	public function barrasEstadisticas($rol)
	{
		$where = '';
		if ($rol == 2) {
			$where = 'WHERE t.coordinacion = '.$_SESSION['id_coordinacion'];
		} elseif ($rol == 3) {
			$where = 'WHERE t.id_soporte = ' . $_SESSION['id'];
		}
		$this->sql = 'SELECT e.id, e.descripcion, COUNT(t.id) AS tickets '.$this->join.' '.$where.'
		GROUP BY 1 ORDER BY 1;';
		return $this;
	}

	public function mTickets($estatus, $rol)
	{
		if ($rol == 2) {
			$where = 'AND t.coordinacion = '.$_SESSION['id_coordinacion'];
		} elseif ($rol == 3) {
			$where = 'AND t.id_soporte = '.$_SESSION['id'];
		} else {
			$where = '';
		}
		$this->sql = 'SELECT t.id, t.fecha_apertura, t.hora, u2.nombre AS soportista, t.colaborador ';
		$this->sql .= $this->join;
		$this->sql .= ' WHERE e.descripcion = "'.$estatus.'" '.$where.' ORDER BY id DESC';
		return $this;
	}

	public function ticketsPorSoportistas($soportista = 0)
	{
		if ($soportista) {
		} else {
			$where = ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 4) ? '' : 'WHERE t.coordinacion ='.$_SESSION['id_coordinacion'];
		}
		$this->sql = 'SELECT u.nombre, count(*) AS tickets
		FROM tickets AS t INNER JOIN users AS u	ON t.id_soporte = u.id '.$where.'
		GROUP BY 1;';
		return $this;
	}

	public function ticketsPorDepartamentos()
	{
		$this->sql = "SELECT d.opcion AS departamento, count(*) AS tickets
		FROM tickets AS t INNER JOIN direccion AS d ON t.id_departamento = d.id
		WHERE t.id_estatus = 3
		GROUP BY 1 ORDER BY 2 DESC";
		return $this;
	}

	public function estatusPersonal($cedula, $fechaI, $fechaF)
	{
		$fecha = ($fechaI == 0 || $fechaF == 0) ? '' : 'AND fecha_apertura BETWEEN "'.$fechaI.'" AND "'.$fechaF.'"';
		$this->sql = 'SELECT t.id_estatus, e.descripcion, count(*) AS tickets, u.nombre
		FROM tickets AS t 
		INNER JOIN users AS u ON t.id_soporte = u.id 
		INNER JOIN estatus AS e ON t.id_estatus = e.id
		WHERE t.id_soporte = '.$cedula.' '.$fecha.'
		GROUP BY t.id_estatus';
		return $this;
	}

	public function ticketsMensuales($año = -1)
	{
		$this->sql = 'SELECT YEAR(fecha_apertura) AS a, MONTH(fecha_apertura) AS mes, count(*) AS tickets
		FROM tickets AS t INNER JOIN users AS u ON t.id_soporte = u.id
		INNER JOIN problema_i AS p1 ON t.solicitud = p1.id
		INNER JOIN problema_ii AS p2 ON t.problema = p2.id
		INNER JOIN division AS d ON t.id_secciones = d.id
		WHERE YEAR(fecha_apertura) = '.$año.' OR '.$año.' = -1
		GROUP BY 1, mes';
		return $this;
	}

	public function estatusSimple($idDepartamento, $fechaI, $fechaF)
	{
		$this->sql = 'SELECT t.id_estatus, e.descripcion, COUNT(t.id_departamento) AS tickets, d.opcion AS departamento
		FROM tickets AS t LEFT JOIN direccion AS d ON t.id_departamento = d.id
		INNER JOIN estatus AS e ON t.id_estatus = e.id
		WHERE t.id_departamento = '.$idDepartamento.' AND  t.fecha_cierre
		BETWEEN "'.$fechaI.'" AND "'.$fechaF.'" GROUP BY 1';
		return $this;
	}

	public function estatusCoordinacion($departamento, $fechaI, $fechaF)
	{
		$this->sql = 'SELECT co.coordinacion, e.descripcion, count(*) AS tickets
		'.$this->join.'
		WHERE t.coordinacion = '.$departamento.' AND t.fecha_cierre BETWEEN "'.$fechaI.'" AND "'.$fechaF.'"
		GROUP BY 2';
		return $this;
	}
} /* Fin de la clase Estadisticas */