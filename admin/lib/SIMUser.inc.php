<?php
class SIMUser extends SIMReg
{
	//datos a encapsular
	static $_params = array();

	function insert($frm, $table, $key, $passwdfield)
	{
		$dbo = &SIMDB::get();

		$values = array();
		$fields = $dbo->fieldsOf($table);
		$str = "INSERT INTO " . $table . " ( ";

		foreach ($fields as $row) {
			if (!empty($frm[$row["Field"]]))
				if ($row["Field"] <> $key)
					$field[] = $row["Field"];
		}

		$fields = implode(",", $field);

		$str .= $fields . " ) ";

		$str .= " VALUES ( ";

		for ($i = 0; $i < (count($field)); $i++) {
			if ($field[$i] == $passwdfield)
				$values[] = "sha1( '" . $frm[$field[$i]] . "' )";
			else
				$values[] = "'" . $frm[$field[$i]] . "'";
		}

		$str .= implode(",", $values) . " ) ";

		if ($dbo->query($str))
			return $dbo->lastID();
		else
			return false;
	}

	function update($frm, $table, $key, $id, $passwdfield, $exceptions = array())
	{
		$array_field = array();
		$value_array = array();

		$dbo = &SIMDB::get();

		$fields = $dbo->fieldsOf($table);

		foreach ($fields as $row) {
			if (!in_array($row["Field"], $exceptions) && isset($frm[$row["Field"]]))
				$array_field[] = $row["Field"];
			else {
				if (!empty($frm[$row["Field"]]))
					$array_field[] = $row["Field"];
			}
		}

		$str = "UPDATE " . $table . " SET ";

		foreach ($array_field as $field) {
			if ($field <> $key) {
				if ($field == $passwdfield)
					array_push($value_array, $field . " = sha1( '" . $frm[$field] . "' ) ");
				else
					array_push($value_array, $field . " = '" . $frm[$field] . "' ");
			}
		}

		$str .= implode(" , ", $value_array) . " WHERE " . $key . " = '" . $id . "' ";
		if ($results = $dbo->query($str)) {
			$dbo->free($results);
			return $id;
		} else
			return false;
	}
}
