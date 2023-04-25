<?php

class SIMDB
{
	static $instance = null;
	static $conexion = null;


	public static function &get()
	{
		if (!self::$instance) {
			self::$instance = new SIMDB();
			return self::$instance;
		} else
			return self::$instance;
	}

	function getConection()
	{
		return self::$conexion;
	}

	function connect($dbhost, $dbname, $dbuser, $dbpass)
	{

		if ($this == self::$instance) {
			$conexion = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
			if ($conexion->connect_errno) {
				echo "Lo sentimos, este sitio web está experimentando problemas.";
				echo "Error: Fallo al conectarse a MySQL debido a: \n";
				//echo "Errno: " . $conexion->connect_errno . "\n";
				//echo "Error: " . $conexion->connect_error . "\n";
				exit;
			}
			mysqli_set_charset($conexion, "utf8");
			$conexion->set_charset("utf8");
			self::$conexion = $conexion;
			return self::$conexion;
		} else
			return false;
	}

	function close()
	{
		$conexion = self::$conexion;
		mysqli_close($conexion);
		return true;
	}

	function query($query, $debug = false, $die = false)
	{
		/*$file = fopen("bd.log", "a");
		fwrite($file, $query . PHP_EOL);
		fclose($file);*/
		$conexion = self::$conexion;

		if ($this == self::$instance) {
			if ($debug) {
				//echo "<pre>" . htmlspecialchars($query) . "</pre>";
				if ($die) die;
			}

			$qid = $conexion->query($query);

			/*if ( $debug && !$qid )
				echo "<h2>Ha ocurrido un error</h2><p><b>MySQL Error</b>: " . mysql_error();*/

			return $qid;
		} else
			return false;
	}

	function fetchArray($qid, $param = "")
	{
		if ($qid) {
			$conexion = self::$conexion;
			return $qid->fetch_array();
		}
	}

	function fetchRow($qid)
	{
		return mysql_fetch_row($qid);
	}

	function rewind($qid, $pos)
	{
		return mysql_data_seek($qid, $pos);
	}

	function assoc($qid)
	{
		$conexion = self::$conexion;
		return mysqli_fetch_assoc($qid);
	}

	function row($qid)
	{
		return mysql_fetch_row($qid);
	}

	function object($qid)
	{
		if ($qid) {
			$conexion = self::$conexion;
			return $qid->fetch_object();
		}
	}

	function rows($qid)
	{
		$conexion = self::$conexion;
		return mysqli_num_rows($qid);
	}

	function affected()
	{
		return mysql_affected_rows();
	}

	function lastID()
	{
		$conexion = self::$conexion;
		$id = $conexion->insert_id;
		return $id;
	}

	function free($qid)
	{
		$conexion = self::$conexion;
		mysqli_free_result($qid);
		return true;
	}

	function fields($qid)
	{
		return mysql_num_fields($qid);
	}

	function fieldName($qid, $fieldno)
	{
		//return mysql_field_name( $qid , $fieldno );
		return $qid->fetch_field_direct($fieldno);
	}

	function &all($table, $condition = 0)
	{
		$conexion = self::$conexion;
		$resultado = "";
		$sql = "SELECT * FROM " . $table;

		if ($condition)
			$sql .= " WHERE " . $condition;


		return $conexion->query($sql);
	}

	function fetchById($table, $key, $id, $type)
	{
		return $this->fetchAll($table,  $key . "=" . $id . $leng_cond, $type);
	}

	function fetchAll($table, $condition = 0, $type = "array")
	{
		$res = &$this->all($table, $condition);
		return $this->fetch($res, $type);
	}

	function fetch($resource, $type = "array")
	{

		$resultado = array();

		if (gettype($resource) == "string")
			$resource = $this->query($resource);

		if (!$this->rows($resource))	return false;

		if ($type == "array") {
			if ($this->rows($resource) > 1) {
				while ($r = $this->assoc($resource))
					$resultado[] = $r;
			} else {
				$resultado = $this->assoc($resource);

				//$resultado[] = $this->assoc( $resource );
			}
		} else {
			if ($this->rows($resource) > 1) {
				while ($r = $this->object($resource))
					$resultado[] = $r;
			} else {
				$resultado = new stdClass;
				$resultado = $this->object($resource);
			}
		}

		$this->free($resource);

		return $resultado;
	}

	function insert($frm, $table, $key)
	{
		$values = "";
		$leng_cond = "";
		$fields = $this->fieldsOf($table);
		$field = array();

		$str = "INSERT INTO " . $table . " ( ";
		foreach ($fields as $row) {
			if (!empty($frm[$row["Field"]]))
				if ($row["Field"] == "IDLenguaje")
					$field[] = $key;
			if ($row["Field"] <> $key)
				$field[] = $row["Field"];
		}

		$fields = implode(",", $field);
		$str .= $fields . " ) ";

		$str .= " VALUES ( ";
		$sep = "";

		for ($i = 0; $i < (count($field)); $i++) {
			$values .= $sep . "'" . $frm[$field[$i]] . "'";
			$sep = ",";
		}

		$str .= $values . " ) ";
		SIMLog::insert($frm["UsuarioTrCr"], $table, "", "insert",  $str);
		if ($this->query($str))
			return $this->lastID();
		else
			return false;
	}

	function update($frm, $table, $key, $id, $exceptions = array())
	{
		$cond_leng = "";
		$array_field = array();
		$value_array = array();

		$fields = $this->fieldsOf($table);

		foreach ($fields as $row) {
			if (!in_array($row["Field"], $exceptions) && isset($frm[$row["Field"]]))
				$array_field[] = $row["Field"];
			else {
				if (!empty($frm["IDLenguaje"]))
					$cond_leng = " AND IDLenguaje = '" . $frm["IDLenguaje"] . "' ";

				if (!empty($frm[$row["Field"]]))
					$array_field[] = $row["Field"];
			}
		}

		$str = "UPDATE " . $table . " SET ";

		foreach ($array_field as $field) {
			if ($field <> $key)
				array_push($value_array, $field . " = '" . $frm[$field] . "' ");
		}

		$str .= implode(" , ", $value_array) . " WHERE " . $key . " = '" . $id . "' " . $cond_leng;
		SIMLog::insert($frm["UsuarioTrEd"], $table, $mod, "update",  $str);

		if ($results = $this->query($str)) {
			$this->free($results);
			return $id;
		} else
			return false;
	}

	function fieldsOf($table)
	{
		$conexion = self::$conexion;
		$fields = array();
		$resultfields = $conexion->query("SHOW FIELDS FROM " . $table);
		while ($row = $this->assoc($resultfields))
			$fields[] = $row;

		$this->free($resultfields);

		return $fields;
	}

	function deleteById($table, $key, $id)
	{
		$qry = $this->query("SELECT " . $key . " FROM " . $table . " WHERE " . $key . " = '" . $id . "'");

		if ($this->rows($qry)) {

			$qry_delete = $this->query("DELETE FROM " . $table . " WHERE " . $key . " = '" . $id . "' ");

			return true;
		} else
			return false;
	}

	function delete($table, $condicion = 1)
	{
		if ($this->query("DELETE FROM " . $table . " WHERE " . $condicion))
			return true;
		else
			return false;
	}


	function db_table_query($table)
	{

		$def = "";

		$def .= "CREATE TABLE TMP$table (\n";

		$result = $this->query("SHOW FIELDS FROM $table");

		while ($row = $this->fetchArray($result)) {
			$def .= "    $row[Field] $row[Type]";
			if ($row["Default"] != "") $def .= " DEFAULT '$row[Default]'";
			if ($row["Null"] != "YES") $def .= " NOT NULL";
			if ($row[Extra] != "") $def .= " $row[Extra]";
			$def .= ",\n";
		}
		$def = ereg_replace(",\n$", "", $def);
		$result = $this->query("SHOW KEYS FROM $table");

		while ($row = $this->fetchArray($result)) {

			$kname = $row[Key_name];
			if (($kname != "PRIMARY") && ($row[Non_unique] == 0)) $kname = "UNIQUE|$kname";
			if (!isset($index[$kname])) $index[$kname] = array();
			$index[$kname][] = $row[Column_name];
		}
		while (list($x, $columns) = @each($index)) {
			$def .= ",\n";
			if ($x == "PRIMARY") $def .= "   PRIMARY KEY (" . implode($columns, ", ") . ")";
			else if (substr($x, 0, 6) == "UNIQUE") $def .= "   UNIQUE " . substr($x, 7) . " (" . implode($columns, ", ") . ")";
			else $def .= "   KEY $x (" . implode($columns, ", ") . ")";
		}
		$def .= "\n)";

		return (stripslashes($def));
	}




	function getFields($table, $fields, $condicion = 1)
	{
		$conexion = self::$conexion;
		if (is_array($fields))
			$fieldstr = implode(",", $fields);
		else
			$fieldstr = $fields;

		$qry = $conexion->query(" SELECT " . $fieldstr . " FROM " . $table . " WHERE " . $condicion);

		if ($this->rows($qry)) {
			$r = $this->assoc($qry);
			if (is_array($fields))
				return $r;
			else
				return $r[$fields];
		} else
			return false;
	}
}
