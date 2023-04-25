<?php
class SIMUtil
{

    public static function cache($type = "text/html")
    {
        header("Content-type: " . $type);
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        return true;
    }
    public function generarPassword($caracteres)
    {
        $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        $cad = "";
        for ($i = 0; $i < $caracteres; $i++) {
            $cad .= substr($str, rand(0, 62), 1);
        }
        return $cad;
    }
    public function sendMail($To, $Subject, $Msg, $vars, $exclude, $From, $cabs)
    {
        $mess = "";

        foreach ($vars as $key => $val) {
            if (!in_array($key, $exclude)) {
                $mess .= " - " . $key . " : " . $val . "\n";
            }

        }

        $Msg .= "\n" . $mess;

        if (mail($To, $Subject, $Msg, "From: " . $From . implode("\n"))) {
            return true;
        } else {
            return false;
        }

    }

    public function repetition($reps = 2)
    {
        static $skip = 1;

        if ($skip++ == $reps) {
            $skip = 1;
            return true;
        } else {
            return false;
        }

    }

    public function display_msg($msg)
    {
        echo "<center><p>";
        echo "<font face=Arial, Helvetica, sans-serif size=3 color=#000066>";
        echo $msg;
        echo "</font></p></center>";
    }

    public static function str_crypt($string, $action = 'e', $key)
    {
        // you may change these values to your own

        $secret_key = $key . '_key';
        $secret_iv = $key . '_iv';
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        if ($action == 'e') {
            $output = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
        } else if ($action == 'd') {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);

        }

        return $output;
    }

    public function encrypt($plain_text, $password, $iv_len = 16)
    {
        $plain_text .= "\x13";
        $n = strlen($plain_text);
        if ($n % 16) {
            $plain_text .= str_repeat("\0", 16 - ($n % 16));
        }

        $i = 0;
        $enc_text = get_rnd_iv($iv_len);
        $iv = substr($password ^ $enc_text, 0, 512);
        while ($i < $n) {
            $block = substr($plain_text, $i, 16) ^ pack('H*', md5($iv));
            $enc_text .= $block;
            $iv = substr($block . $iv, 0, 512) ^ $password;
            $i += 16;
        }
        return base64_encode($enc_text);
    }

    public function decrypt($enc_text, $password, $iv_len = 16)
    {
        $enc_text = base64_decode($enc_text);
        $n = strlen($enc_text);
        $i = $iv_len;
        $plain_text = '';
        $iv = substr($password ^ substr($enc_text, 0, $iv_len), 0, 512);
        while ($i < $n) {
            $block = substr($enc_text, $i, 16);
            $plain_text .= $block ^ pack('H*', md5($iv));
            $iv = substr($block . $iv, 0, 512) ^ $password;
            $i += 16;
        }
        return preg_replace('/\\x13\\x00*$/', '', $plain_text);
    }

    public function decryptSodium($param)
    {
        $key = sodium_hex2bin($param['key']);
        $nonce = sodium_hex2bin($param['nonce']);
        $chiper = sodium_hex2bin($param['chiper']);
        $plaintext = sodium_crypto_secretbox_open($chiper, $nonce, $key);
        if ($plaintext === false) {
            $plaintext = "nodecrypt";
        }
        return $json['success'] = array("decryptedText" => $plaintext);
    }

    public function cryptSodium($param)
    {
        $key = sodium_hex2bin($param['key']);
        $nonce = sodium_hex2bin($param['nonce']);
        $msg = $param['msg'];
        $ciphertext = sodium_crypto_secretbox($msg, $nonce, $key);
        return $json['success'] = array("cryptedText" => $ciphertext);
    }

    public function makeUrlString()
    {
        $REQUEST_METHOD = $_SERVER["REQUEST_METHOD"];

        $cgi = $REQUEST_METHOD == 'GET' ? $_GET : $_POST;
        reset($cgi);

        foreach ($cgi as $key => $value) {
            if ($key != "row" && !empty($value) && $key != "Submit") {
                $query_string .= "&" . $key . "=" . $value;
            }

        }
        return $query_string;
    }

    public function getMicroTime()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float) $sec);
    }

    public function varsLOG($frm, $usuario = "", $table = "", $key = "", $id = "", $do = "")
    {
        $usuario = SIMUser::get("Nombre");
        $table = SIMReg::get("table");
        $key = SIMReg::get("key");
        $id = SIMNet::reqInt("id");
        $do = SIMNet::reqInt("action");

        $do = SIMNet::get("action");

        $dbo = &SIMDB::get();
        if (!empty($table) && !empty($key)) {
            $qry = $dbo->query("SELECT UsuarioTrCr , FechaTrCr , UsuarioTrEd , FechaTrEd FROM " . $table . " WHERE " . $key . " = '" . $id . "'");
            $r = $dbo->object($qry);
        }

        $now = date("Y-m-j h:i:s");

        if ($do == "insert" || $do == "add") {
            $frm['UsuarioTrCr'] = $usuario;
            $frm['FechaTrCr'] = $now;
            $frm['UsuarioTrEd'] = $r->UsuarioTrEd;
            $frm['FechaTrEd'] = $r->FechaTrEd;
        } else {
            $frm['UsuarioTrEd'] = $usuario;
            $frm['FechaTrEd'] = $now;
            $frm['UsuarioTrCr'] = $r->UsuarioTrCr;
            $frm['FechaTrCr'] = $r->FechaTrCr;
        }
        return $frm;
    }

    public static function makeSafe($data)
    {
        //    if( !get_magic_quotes_gpc()  )
        //    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $data[$key] = self::makeSafe($value);
                } else {
                    $data[$key] = addslashes($value);
                }

            }
        } else {
            return addslashes($data);
        }

        //    }

        return $data;
    }

    public function tiempo($fecha)
    {
        $horafinal = "";

        $fechahora = explode(" ", $fecha);

        $fecha = explode("-", $fechahora[0]);
        $hora = explode(":", $fechahora[1]);

        if (!empty($fecha)) {
            $horafinal .= SIMResources::$meses[$fecha[1] - 1] . " " . $fecha[2] . " de " . $fecha[0];
        }

        if (!empty($hora[0])) {
            $hora[0] = (int) $hora[0];

            if ($hora[0] > 12) {
                $hora[0] = $hora[0] - 12;
                $merid = "pm";

                if ($hora[0] < 10) {
                    $hora[0] = "0" . $hora[0];
                }

            } else {
                if ($hora[0] == 12) {
                    $merid = "pm";
                } else {
                    $merid = "am";
                }

            }

            $horafinal .= " " . $hora[0] . ":" . $hora[1] . ":" . $hora[2] . " " . $merid;
        }

        return trim($horafinal);
    }

    public function lastURI()
    {
        return $_SERVER["REQUEST_URI"];
    }

    public function &createPag($sql, $limit = 50, $front = "")
    {

        $nav = new buildNav;
        $nav->offset = 'offset';
        $nav->limit = $limit;
        $nav->execute($sql);
        $result = $nav->sql_result;
        $rows = $nav->rows;

        $pages = $nav->show_num_pages();

        $info = $nav->show_info();

        return array("info" => $info, "pages" => $pages, "rows" => $rows, "result" => &$result);

    }

    public function &createPag_front($sql, $limit = 50, $front = "")
    {

        $nav = new buildNav;
        $nav->offset = 'offset';
        $nav->limit = $limit;
        $nav->execute($sql);
        $result = $nav->sql_result;
        $rows = $nav->rows;

        $pages = $nav->show_num_pages_front();

        $info = $nav->show_info();

        return array("info" => $info, "pages" => $pages, "rows" => $rows, "result" => &$result);

    }
    public function filter($fieldInt, $fieldStr, $fromjoin, $fieldsjoin, $where_array, $wherejoin)
    {
        extract(SIMUtil::makeSafe($_GET));
        extract(SIMUtil::makeSafe($_POST));

        $fieldlist = array();
        $fromput = array();

        // Adicionando los INT campos para el query
        foreach ($fieldInt as $v) {
            if (${$v} != "") {
                array_push($where_array, " V.$v = '" . ${$v} . "'");
            }
        }

        // Adicionando los campos para el query
        foreach ($fieldStr as $v) {
            if (${$v} != "") {
                array_push($where_array, " V.$v LIKE '%" . ${$v} . "%' ");
            }
        }

        foreach ($fieldsjoin as $v => $field) {

            //echo "entro";
            $v . " =>" . $field;
            $fieldlist[] = $field;
            $fromput[] = $v;
            $where_array[$v] = $fromjoin[$v];
        }

        foreach ($wherejoin as $v => $from):
            if (!empty(${$v})):
                $whereput .= $wherejoin[$v] . " AND ";
            endif;
        endforeach;

        $fieldlist[] = " V.* ";
        $fieldiststr = implode(",", $fieldlist);

        if (sizeof($where_array)) {
            $condiciones = implode(" AND ", $where_array);
            $condicion .= " WHERE $condiciones ";

            //JOINS
            if (sizeof($where_array)) {
                $condicion .= " AND " . $whereput . " 1 ";
            }

        } elseif (!empty($whereput)) {
            $condicion = " WHERE " . $whereput . " 1 ";
        }

        return array("from" => implode(",", $fromput), "where" => $condicion, "fields" => $fieldiststr);
    }

    public function valida($frm, $arr_valida)
    {
        $errorMsg = "";
        $errorList = array();
        $arrayFields = $arr_valida;

        foreach ($arrayFields as $field => $text) {
            $value = $frm[$field];

            if (trim($frm[$field]) == "") {
                $errorList[] = array("field" => $field, "value" => $value, "msg" => $text);
            }

        }

        if (count($errorList) > 0) {
            $mess = "<strong>ATENCION!</strong> Debe Completar los siguientes campos:\n<ul>";

            foreach ($errorList as $item) {
                $mess .= "<li>" . $item['msg'] . "</li>\n";
            }

            $mess .= "</ul>\nPor favor corrijalos e intente de nuevo\n";

            return $mess;
        } else {
            return false;
        }

    }

    public function convertirmoneda($moneda1, $valor1, $moneda2, $fecha)
    {
        $dbo = &SIMDB::get();
        $qry_moneda1 = $dbo->query("SELECT * FROM CambioMoneda WHERE '$fecha' BETWEEN FechaDesde AND FechaHasta AND IDMoneda = '$moneda1' LIMIT 1");
        $r_moneda1 = $dbo->object($qry_moneda1);

        $qry_moneda2 = $dbo->query("SELECT * FROM CambioMoneda WHERE '$fecha' BETWEEN FechaDesde AND FechaHasta AND IDMoneda = '$moneda2' LIMIT 1");
        $r_moneda2 = $dbo->object($qry_moneda2);

        if ($moneda1 == 1) {
            //Multiplica
            $ValorCambio = $valor1 / $r_moneda2->Valor;
        } //end if
        elseif ($moneda2 == 1) {
            //Divide
            $ValorCambio = $valor1 * $r_moneda1->Valor;
        } //end if
        else {
                //Primero parar a pesos (dividir)
                $ValorCambio = $valor1 * $r_moneda1->Valor;
                //Luego pasar a la moneda (multiplicar)
                $ValorCambio = $ValorCambio / $r_moneda2->Valor;

            } //end else

            return $ValorCambio;

        }

        public function clearm($value)
    {
            return preg_replace("/[\,]+/", "", $value);
        }

        public function verify($modulo, $IDUsuario)
    {
            $dbo = &SIMDB::get();
            // Consulto los perfiles del usuario
            $sql_perfil = $dbo->query("select * from UsuarioPerfil where IDUsuario = '" . $IDUsuario . "'");
            while ($r_perfil = $dbo->object($sql_perfil)) {
                $nombre_modulo = $dbo->getFields("Perfil", "NombreModulo", "IDPerfil = '" . $r_perfil->IDPerfil . "'");
                $perfil[] = $nombre_modulo;
            }

            if (in_array($modulo, $perfil)) {
                return $permiso = 2;
            } else {
                return $permiso = 0;
            }
        }

        public function verificar_permiso($modulo, $IDPerfil)
    {
            $dbo = &SIMDB::get();

            $id_modulo = $dbo->getFields("Modulo", "IDModulo", "IdentificadorModulo = '" . $modulo . "'");
            // Consulto los perfiles del usuario
            $sql_perfil = $dbo->query("select * from ModuloPerfil where IDPerfil = '" . $IDPerfil . "' and IDModulo = '" . $id_modulo . "'");
            if ($dbo->rows($sql_perfil) > 0 || $IDPerfil == 0):
                $permiso = 0;
            else:
                $permiso = 1;
                // No tiene permiso sobre este modulo lo direcciono al index
                header("Location: sinpermiso.php");
            endif;

            return $permiso;
        }

        public function verificar_permiso_modulo($modulo, $IDPerfil)
    {
            $dbo = &SIMDB::get();

            $id_modulo = $dbo->getFields("Modulo", "IDModulo", "IdentificadorModulo = '" . $modulo . "'");
            // Consulto los perfiles del usuario
            $sql_perfil = $dbo->query("select * from ModuloPerfil where IDPerfil = '" . $IDPerfil . "' and IDModulo = '" . $id_modulo . "'");
            if ($dbo->rows($sql_perfil) > 0 || $IDPerfil == 0):
                $permiso = 0;
            else:
                $permiso = 1;
            endif;

            return $permiso;
        }

        public function get_permiso($mod, $perfil)
    {
            $dbo = &SIMDB::get();
            $qry_modulo = $dbo->query("SELECT IDModulo FROM Modulo WHERE NombreModulo = '$mod' LIMIT 1");
            $r_modulo = $dbo->object($qry_modulo);

            $qry_permiso = $dbo->query("SELECT Permiso FROM Permisos WHERE IDModulo = '$r_modulo->IDModulo' AND IDPerfil = '$perfil' LIMIT 1");
            $r_permiso = $dbo->object($qry_permiso);

            return $r_permiso->Permiso;

        }

        public function antiinjection($str)
    {

            $banchars = array("'", "/", "_", "*", ";", "--", ")", "(", "\n", "\r");
            $banwords = array("key_column_usage", "UNION", " or ", " OR ", " Or ", " oR ", " and ", " AND ", " aNd ", " aND ", " AnD ", "group_concat", "table_name");
            if (preg_match('/[a-zA-Z0-9 ]/', $str)) {
                $str = str_ireplace($banchars, '', ($str));
                $str = str_ireplace($banwords, '', ($str));
            } else {
                $str = null;
            }

            $str = trim($str);
            $str = strip_tags($str);
            $str = stripslashes($str);
            $str = addslashes($str);
            $str = htmlspecialchars($str);
            return $str;
        }

        public function floatvalue($value)
    {
            return floatval(preg_replace('#^([-]*[0-9\.,\' ]+?)((\.|,){1}([0-9-]{1,2}))*$#e', "str_replace(array('.', ',', \"'\", ' '), '', '\\1') . '.\\4'", $value));
        }

        public function get_comerciales($table, $usuario)
    {
            $dbo = &SIMDB::get();
            $comerciales = $dbo->fetchAll("ComercialEjecutivo", " IDComercial = '" . $usuario . "' ", "array");

            foreach ($comerciales as $key => $value) {
                $array_comerciales[] = $value["IDUsuario"];
            }

            if (count($array_comerciales) > 0) {
                $in = " AND " . $table . ".IDUsuario IN ( " . $usuario . "," . implode(",", $array_comerciales) . " ) ";
            }

            return $in;

        }

        public function get_arbol($table, $key, $ubicacion = "Menu")
    {
            $dbo = &SIMDB::get();
            $array_menu = array();

            if (!empty($ubicacion)) {
                $sqlUbicacion = " AND FIND_IN_SET( '" . $ubicacion . "', Ubicacion ) > 0 ";
            }

            $qry = $dbo->all($table, "Publicar = 'S' " . $sqlUbicacion . " ORDER BY Orden ASC ");
            while ($r = $dbo->fetchArray($qry)) {
                $array_menu[$r["IDPadre"]][$r[$key]] = $r;
            } //end while

            return $array_menu;

        } //end function

        public static function makeboolean($sqlfieldname, $keywordstr)
    {

            $keyword = $keywordstr;

            // Convert String To Lower Case
            $keyword = strtolower($keyword);

            // Replace Word Operators With Single Character Operators
            //trim($keyword);

            //$keyword = str_replace("   "," ",$keyword);
            //    $keyword = str_replace("  "," ",$keyword);
            $keyword = preg_replace("/\s+/", " ", trim($keyword));

            $keyword = str_replace(" ", "+", $keyword);
            $keyword = str_replace(",", "|", $keyword);
            // $keyword = ereg_replace(" -","-",$keyword);
            //  $keyword = ereg_replace("-","-",$keyword);

            // Build The Keywords String Based On Operators Assigned Above
            $operatorcount = 0;
            $len = strlen($keyword);
            for ($z = 0; $z < $len; $z++) {
                if (($keyword[$z] == "+") || ($keyword[$z] == "|")) { //|| ($keyword[$z] == "-")
                    $operatorpos[$operatorcount] = $z;
                    $operatorcount++;
                }
            }

            if ($operatorcount != 0) {
                for ($z = 0; $z < $operatorcount; $z++) {
                    if ($z == 0) {
                        $startpos = 0;
                        $endpos = $operatorpos[$z];
                    } else {
                        $startpos = $operatorpos[$z - 1] + 1;
                        $endpos = $operatorpos[$z];
                    }

                    $word = $endpos - $startpos;
                    $keystring = substr($keyword, $startpos, $word);
                    $keystring = str_replace("(", "", $keystring);
                    $keystring = str_replace(")", "", $keystring);
                    $keywords[$z] = $keystring;
                    $operator_pos = $operatorpos[$z];
                    $operators[$z] = $keyword[$operator_pos];
                } // end the for loop

                $wordcount = $operatorcount + 1;
                $startpos = $operatorpos[$z - 1] + 1;
                $len2 = strlen($keyword) - $startpos;
                $linestr = substr($keyword, $startpos, $len2);

                //store the line into the keywords array
                $keywords[$wordcount - 1] = $linestr;

                //loop through all of the words in the words array replacing them in the original string with a LIKE clause
                for ($z = 0; $z < $wordcount; $z++) {
                    $replacekeyword = $keywords[$z];
                    $y = $z - 1;
                    //    if ($operators[$y] != "-")   //odd case is in a NOT...must do something different!
                    $keyword = str_replace($replacekeyword, "$sqlfieldname LIKE '%$replacekeyword%'", $keyword);
                    //   else
                    //     $keyword = ereg_replace($replacekeyword,"$sqlfieldname NOT LIKE '%$replacekeyword%'",$keyword);
                }

                //
                $keyword = str_replace("+", " OR ", $keyword);
                $keyword = str_replace("|", " OR ", $keyword);
                // $keyword = ereg_replace("\-"," AND ", $keyword);  //I fudged in the above statement so this possible :-

            } // end if operatorcount != 0
        else { //there were no operators in the string
                $replacekeyword = $keyword;
                if ($keyword != "") {
                    $keyword = str_replace($replacekeyword, "$sqlfieldname LIKE '%$replacekeyword%'", $keyword);
                }
            }

            $keyword = " (" . $keyword . ") ";

            return ($keyword);
        }

        public function xml2array($contents, $get_attributes = 1, $priority = 'tag')
    {
            if (!$contents) {
                return array();
            }

            if (!function_exists('xml_parser_create')) {
                //print "'xml_parser_create()' function not found!";
                return array();
            }

            //Get the XML parser of PHP - PHP must have this module for the parser to work
            $parser = xml_parser_create('');
            xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
            xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
            xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
            xml_parse_into_struct($parser, trim($contents), $xml_values);
            xml_parser_free($parser);

            if (!$xml_values) {
                return;
            }
//Hmm...

            //Initializations
            $xml_array = array();
            $parents = array();
            $opened_tags = array();
            $arr = array();

            $current = &$xml_array; //Refference

            //Go through the tags.
            $repeated_tag_index = array(); //Multiple tags with same name will be turned into an array
            foreach ($xml_values as $data) {
                unset($attributes, $value); //Remove existing values, or there will be trouble

                //This command will extract these variables into the foreach scope
                // tag(string), type(string), level(int), attributes(array).
                extract($data); //We could use the array by itself, but this cooler.

                $result = array();
                $attributes_data = array();

                if (isset($value)) {
                    if ($priority == 'tag') {
                        $result = $value;
                    } else {
                        $result['value'] = $value;
                    }
                    //Put the value in a assoc array if we are in the 'Attribute' mode
                }

                //Set the attributes too.
                if (isset($attributes) and $get_attributes) {
                    foreach ($attributes as $attr => $val) {
                        if ($priority == 'tag') {
                            $attributes_data[$attr] = $val;
                        } else {
                            $result['attr'][$attr] = $val;
                        }
                        //Set all the attributes in a array called 'attr'
                        print_r($var);
                    }
                }

                //See tag status and do the needed.
                if ($type == "open") { //The starting of the tag '<tag>'
                    $parent[$level - 1] = &$current;
                    if (!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
                        $current[$tag] = $result;
                        if ($attributes_data) {
                            $current[$tag . '_attr'] = $attributes_data;
                        }

                        $repeated_tag_index[$tag . '_' . $level] = 1;

                        $current = &$current[$tag];

                    } else { //There was another element with the same tag name

                        if (isset($current[$tag][0])) { //If there is a 0th element it is already an array
                            $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                            $repeated_tag_index[$tag . '_' . $level]++;
                        } else { //This section will make the value an array if multiple tags with the same name appear together
                            $current[$tag] = array($current[$tag], $result); //This will combine the existing item and the new item together to make an array
                            $repeated_tag_index[$tag . '_' . $level] = 2;

                            if (isset($current[$tag . '_attr'])) { //The attribute of the last(0th) tag must be moved as well
                                $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                                unset($current[$tag . '_attr']);
                            }

                        }
                        $last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
                        $current = &$current[$tag][$last_item_index];
                    }

                } elseif ($type == "complete") { //Tags that ends in 1 line '<tag />'
                //See if the key is already taken.
                if (!isset($current[$tag])) { //New Key
                    $current[$tag] = $result;
                    $repeated_tag_index[$tag . '_' . $level] = 1;
                    if ($priority == 'tag' and $attributes_data) {
                        $current[$tag . '_attr'] = $attributes_data;
                    }

                } else { //If taken, put all things inside a list(array)
                    if (isset($current[$tag][0]) and is_array($current[$tag])) { //If it is already an array...

                        // ...push the new element into that array.
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;

                        if ($priority == 'tag' and $get_attributes and $attributes_data) {
                            $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                        }
                        $repeated_tag_index[$tag . '_' . $level]++;

                    } else { //If it is not an array...
                        $current[$tag] = array($current[$tag], $result); //...Make it an array using using the existing value and the new value
                        $repeated_tag_index[$tag . '_' . $level] = 1;
                        if ($priority == 'tag' and $get_attributes) {
                            if (isset($current[$tag . '_attr'])) { //The attribute of the last(0th) tag must be moved as well

                                $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                                unset($current[$tag . '_attr']);
                            }

                            if ($attributes_data) {
                                $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                            }
                        }
                        $repeated_tag_index[$tag . '_' . $level]++; //0 and 1 index is already taken
                    }
                }

            } elseif ($type == 'close') { //End of tag '</tag>'
                $current = &$parent[$level - 1];
            }
        }

        return ($xml_array);
    }

    public function remplaza_acentos($cadena)
    {
        $originales = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
        $modificadas = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
        $cadena = utf8_decode($cadena);
        $cadena = strtr($cadena, utf8_decode($originales), $modificadas);
        $cadena = strtolower($cadena);
        return $cadena;
    }

    public function URL($text)
    {
        $tildes = array('á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú', ' ', 'Ñ');
        $sin_tildes = array('a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', '-', 'n');
        //reemplazar tildes y espacios
        $text = str_replace($tildes, $sin_tildes, strtolower(trim($text)));
        //otros caracteres
        $text = preg_replace("/([^a-z0-9-_])/i", "", $text);
        return $text;
    }

    public function get_type_categoria($key)
    {
        $dbo = &SIMDB::get();
        $tipo_categoria = "N/A";
        $categoria_actual = $dbo->fetchById("CategoriaProducto", "IDCategoria", $key, "array");
        if ($categoria_actual["IDPadre"] != "0") {
            $tipo_categoria = "Hijo";
            $categoria_abuelo = $dbo->fetchById("CategoriaProducto", "IDCategoria", $categoria_actual["IDPadre"], "array");
            if ($categoria_abuelo["IDPadre"] != "0") {
                $tipo_categoria = "Nieto";
            }
        } else {
            $tipo_categoria = "Abuelo";
        }
        return $tipo_categoria;
    } //end function

    public function generar_codigo_barras($parametros_codigo_barras, $IDCliente, $alto_barras = '')
    {
        // Including all required classes
        require_once LIBDIR . 'barcodegen/class/BCGFontFile.php';
        require_once LIBDIR . 'barcodegen/class/BCGColor.php';
        require_once LIBDIR . 'barcodegen/class/BCGDrawing.php';

        // Including the barcode technology
        require_once LIBDIR . 'barcodegen/class/BCGcode128.barcode.php';

        // Loading Font
        $font = new BCGFontFile(LIBDIR . 'barcodegen/font/Arial.ttf', 18);

        // Don't forget to sanitize user inputs
        //$text = isset($_GET['text']) ? $_GET['text'] : 'SOCIO';

        // The arguments are R, G, B for color.
        $color_black = new BCGColor(0, 0, 0);
        $color_white = new BCGColor(255, 255, 255);

        if ($alto_barras == "") {
            $alto_barras = 30;
        }

        $drawException = null;
        try {
            $code = new BCGcode128();
            $code->setScale(8); // Resolution
            //$code->setThickness(30); // Thickness
            $code->setThickness($alto_barras); // Thickness
            $code->setForegroundColor($color_black); // Color of bars
            $code->setBackgroundColor($color_white); // Color of spaces
            $code->setFont($font); // Font (or 0)

            $code->parse($parametros_codigo_barras); // Text
        } catch (Exception $exception) {
            $drawException = $exception;
        }

        /* Here is the list of the arguments
        1 - Filename (empty : display on screen)
        2 - Background color */
        $nombre_archivo = 'Barras_socio_' . $IDCliente . '_' . rand(1, 1000000) . '.png';
        $ruta_archivo = SOCIO_DIR . $nombre_archivo;
        $drawing = new BCGDrawing($ruta_archivo, $color_white);
        if ($drawException) {
            $drawing->drawException($drawException);
        } else {
            $drawing->setBarcode($code);
            $drawing->draw();
        }

        // Header that says it is an image (remove it if you save the barcode to a file)
        //header('Content-Type: image/png');
        //header('Content-Disposition: inline; filename="barcode.png"');
        // Draw (or save) the image into PNG format.
        $drawing->finish(BCGDrawing::IMG_FORMAT_PNG);

        return $nombre_archivo;

    }

    public function generar_codigo_barras_talega($parametros_codigo_barras, $IDCliente)
    {
        // Including all required classes
        require_once LIBDIR . 'barcodegen/class/BCGFontFile.php';
        require_once LIBDIR . 'barcodegen/class/BCGColor.php';
        require_once LIBDIR . 'barcodegen/class/BCGDrawing.php';

        // Including the barcode technology
        require_once LIBDIR . 'barcodegen/class/BCGcode39.barcode.php';

        // Loading Font
        $font = new BCGFontFile(LIBDIR . 'barcodegen/font/Arial.ttf', 18);

        // Don't forget to sanitize user inputs
        //$text = isset($_GET['text']) ? $_GET['text'] : 'SOCIO';
        // The arguments are R, G, B for color.
        $color_black = new BCGColor(0, 0, 0);
        $color_white = new BCGColor(255, 255, 255);

        $drawException = null;
        try {
            $code = new BCGcode39();
            $code->setScale(2); // Resolution
            $code->setThickness(30); // Thickness
            $code->setForegroundColor($color_black); // Color of bars
            $code->setBackgroundColor($color_white); // Color of spaces
            $code->setFont($font); // Font (or 0)
            $code->parse($parametros_codigo_barras); // Text
        } catch (Exception $exception) {
            $drawException = $exception;
        }

        /* Here is the list of the arguments
        1 - Filename (empty : display on screen)
        2 - Background color */
        $nombre_archivo = 'Barras_talega_' . $IDCliente . '_' . rand(1, 10000) . '.png';
        $ruta_archivo = TALEGA_DIR . $nombre_archivo;
        $drawing = new BCGDrawing($ruta_archivo, $color_white);
        if ($drawException) {
            $drawing->drawException($drawException);
        } else {
            $drawing->setBarcode($code);
            $drawing->draw();
        }

        // Header that says it is an image (remove it if you save the barcode to a file)
        //header('Content-Type: image/png');
        //header('Content-Disposition: inline; filename="barcode.png"');
        // Draw (or save) the image into PNG format.
        $drawing->finish(BCGDrawing::IMG_FORMAT_PNG);

        return $nombre_archivo;
    }

    public function generar_codigo_barras_empleado($parametros_codigo_barras, $IDUsuario)
    {
        // Including all required classes
        require_once LIBDIR . 'barcodegen/class/BCGFontFile.php';
        require_once LIBDIR . 'barcodegen/class/BCGColor.php';
        require_once LIBDIR . 'barcodegen/class/BCGDrawing.php';

        // Including the barcode technology
        require_once LIBDIR . 'barcodegen/class/BCGcode128.barcode.php';

        // Loading Font
        $font = new BCGFontFile(LIBDIR . 'barcodegen/font/Arial.ttf', 18);

        // Don't forget to sanitize user inputs
        //$text = isset($_GET['text']) ? $_GET['text'] : 'SOCIO';

        // The arguments are R, G, B for color.
        $color_black = new BCGColor(0, 0, 0);
        $color_white = new BCGColor(255, 255, 255);

        $drawException = null;
        try {
            $code = new BCGcode128();
            $code->setScale(8); // Resolution
            $code->setThickness(30); // Thickness
            $code->setForegroundColor($color_black); // Color of bars
            $code->setBackgroundColor($color_white); // Color of spaces
            $code->setFont($font); // Font (or 0)
            $code->parse($parametros_codigo_barras); // Text
        } catch (Exception $exception) {
            $drawException = $exception;
        }

        /* Here is the list of the arguments
        1 - Filename (empty : display on screen)
        2 - Background color */
        $nombre_archivo = 'Barras_empleado_' . $IDUsuario . '_' . rand(1, 10000) . '.png';
        $ruta_archivo = USUARIO_DIR . $nombre_archivo;
        $drawing = new BCGDrawing($ruta_archivo, $color_white);
        if ($drawException) {
            $drawing->drawException($drawException);
        } else {
            $drawing->setBarcode($code);
            $drawing->draw();
        }

        // Header that says it is an image (remove it if you save the barcode to a file)
        //header('Content-Type: image/png');
        //header('Content-Disposition: inline; filename="barcode.png"');
        // Draw (or save) the image into PNG format.
        $drawing->finish(BCGDrawing::IMG_FORMAT_PNG);

        return $nombre_archivo;

    }

    public function consulta_regla_invitacion($IDSocio, $IDClub)
    {
        $dbo = &SIMDB::get();
        //Datos Socio
        $datos_socio = $dbo->fetchAll("Socio", " IDClub = '" . $IDClub . "' and IDSocio = '" . $IDSocio . "' ", "array");
        //Consulto la edad si tiene fecha de nacimiento
        if ($datos_socio["FechaNacimiento"] != "0000-00-00"):
            $year_nacimiento = substr($datos_socio["FechaNacimiento"], 0, 4);
            $year_actual = date("Y");
            $edad_socio = (int) $year_actual - (int) $year_nacimiento;
            if ($edad_socio < 18):
                return $datos_regla;
            endif;
        endif;

        //Consulto regla invitacion por categoria de socio
        $datos_regla = $dbo->fetchAll("Regla", " IDClub = '" . $IDClub . "' and IDCategoria like '%|" . $datos_socio["IDCategoria"] . "|%' ", "array");
        $sql_regla = "Select * From Regla Where IDClub = '" . $IDClub . "' and IDCategoria like '%|" . $datos_socio["IDCategoria"] . "|%' ";
        $qry_regla = $dbo->query($sql_regla);
        $datos_regla = $dbo->fetchArray($qry_regla);

        if (empty($datos_regla["IDRegla"])):
            //Consulto regla invitacion por parentesco de socio
            $datos_regla = $dbo->fetchAll("Regla", " IDClub = '" . $IDClub . "' and IDParentesco like '%" . $datos_socio["IDParentesco"] . "|%' ", "array");
        endif;

        return $datos_regla;
    }

    public function notificar_encuesta($id_reserva_general, $IDEncuesta)
    {

        $dbo = &SIMDB::get();

        $r_reservas = $dbo->fetchAll("ReservaGeneral", " IDReservaGeneral = '" . $id_reserva_general . "' ", "array");
        $datos_encuesta = $dbo->fetchAll("Encuesta", " IDEncuesta = '" . $IDEncuesta . "' ", "array");

        //actualizar la reserva
        $sql_update = "UPDATE ReservaGeneral SET NotificadoEncuesta = 'S' WHERE IDReservaGeneral = '" . $id_reserva_general . "' ";
        $dbo->query($sql_update);

        if ((int) $r_reservas["IDSocioBeneficiario"] > 0):
            $IDSocio = $r_reservas["IDSocioBeneficiario"];
        else:
            $IDSocio = $r_reservas["IDSocio"];
        endif;

        //traer socio
        $sql_socios = "SELECT Socio.IDSocio, Socio.IDClub, Socio.Token, Socio.Dispositivo FROM Socio WHERE Socio.IDSocio = '" . $IDSocio . "' ";
        $qry_socios = $dbo->query($sql_socios);

        //socios encontrados
        $socios_encontrados = $dbo->rows($qry_socios);

        while ($r_socios = $dbo->fetchArray($qry_socios)) {

            $users = array(array("id" => $r_socios["IDSocio"],
                "idclub" => $r_reservas["IDClub"],
                "registration_key" => $r_socios["Token"],
                "deviceType" => $r_socios["Dispositivo"]),

            );
            $serviciomaestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $r_reservas["IDServicio"] . "' ");
            $nombreservicio = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $serviciomaestro . "' ");

            $nombre_servicio_personalizado = $dbo->getFields("ServicioClub", "TituloServicio", "IDClub = '" . $r_reservas["IDClub"] . "' and IDServicioMaestro = '" . $serviciomaestro . "'");
            if (empty($nombre_servicio_personalizado)) {
                $nombre_servicio_personalizado = $nombreservicio;
            }

            if ((int) $r_reservas["IDServicioTipoReserva"] > 0):
                $nombre_servicio_personalizado .= " (" . $dbo->getFields("ServicioTipoReserva", "Nombre", "IDServicioTipoReserva = '" . $r_reservas["IDServicioTipoReserva"] . "'") . ")";
            endif;

            $message = "Lo invitamos a responder la encuesta: " . $datos_encuesta["Nombre"];

            $custom["tipo"] = "app";
            $custom["idmodulo"] = (string) "58";
            $custom["iddetalle"] = (string) $datos_encuesta["IDEncuesta"];
            $custom["titulo"] = "Notificacion Club";

            if ($r_socios["Dispositivo"] == "iOS") {
                $array_ios[] = $r_socios["Token"];
            } elseif ($r_socios["Dispositivo"] == "Android") {
                $array_android[] = $r_socios["Token"];
            }

            $result_send = SIMUtil::sendAlerts_V2($users, $message, $custom, $TipoApp, $array_android, $array_ios, $r_reservas["IDClub"]);

            //$result_send = SIMUtil::sendAlerts($users, $message, $custom);

            //invitados encontrados
            $sql_invitado = "Select * From ReservaGeneralInvitado Where IDReservaGeneral = '" . $r_reservas["IDReservaGeneral"] . "' and IDSocio >0";
            $result_invitado = $dbo->query($sql_invitado);
            while ($row_invitado = $dbo->fetchArray($result_invitado)):
                $sql_socio_invitado = "SELECT Socio.IDSocio, Socio.IDClub, Socio.Token, Socio.Dispositivo FROM Socio WHERE Socio.IDSocio = '" . $row_invitado["IDSocio"] . "' AND Socio.IDClub = '" . $r_reservas["IDClub"] . "' and Token<>'' and Token <> '2byte'";
                $qry_socio_invitado = $dbo->query($sql_socio_invitado);
                while ($r_socios_invitado = $dbo->fetchArray($qry_socio_invitado)) {
                    $users = array(array("id" => $r_socios_invitado["IDSocio"],
                        "idclub" => $r_reservas["IDClub"],
                        "registration_key" => $r_socios_invitado["Token"],
                        "deviceType" => $r_socios_invitado["Dispositivo"]),
                    );

                    if ($r_socios_invitado["Dispositivo"] == "iOS") {
                        $array_ios[] = $r_socios_invitado["Token"];
                    } elseif ($r_socios_invitado["Dispositivo"] == "Android") {
                    $array_android[] = $r_socios_invitado["Token"];
                }

                //SIMUtil::sendAlerts_V2($users, $message, $custom,$TipoApp,$array_android,$array_ios,$r_reservas["IDClub"]);
                //SIMUtil::sendAlerts($users, $message, $custom);
            }

            if (count($array_android) > 0 || count($array_ios) > 0) {
                $resp = SIMUtil::sendAlerts_V2($users, $message, $custom, $TipoApp, $array_android, $array_ios, $r_reservas["IDClub"]);
            }

            endwhile;

        }

    }

    public function notifica_actualiza_datos($IDClub, $correo, $Mensaje)
    {
        $dbo = &SIMDB::get();

        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");

        $msg = "<br>Cordial Saludo,<br><br>
		Se ha recibio correctamente los datos.<br><br>" . $Mensaje . "<br><br>

		Por favor no responda este correo, si desea dar una respuesta ingrese a nuestra app<br>
		Cordialmente<br><br>
		<b>Notificaciones " . $datos_club["Nombre"] . "</b>";

        $mensaje = "
				<body>
					<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
						<tr>
							<td>
								<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
							</td>
						</tr>
						<tr>
							<td>" .
            $msg
            . "</td>
						</tr>
					</table>
				</body>
		";

        $url_baja = URLROOT . "contactenos.php";
        $mail = new phpmailer();
        $array_correo = explode(",", $correo);
        if (count($array_correo) > 0) {
            foreach ($array_correo as $correo_value) {
                $mail->AddAddress($correo_value);
            }
        }

        $mail->Subject = "Actualizacion de datos";
        $mail->Body = $mensaje;
        $mail->IsHTML(true);
        $mail->Sender = $datos_club["CorreoRemitente"];
        $mail->Timeout = 120;
        //$mail->IsSMTP();
        $mail->Port = PUERTO_SMTP;
        $mail->SMTPAuth = true;
        $mail->Host = HOST_SMTP;
        //$mail->Mailer = 'smtp';
        $mail->Password = PASSWORD_SMPT;
        $mail->Username = USER_SMTP;
        $mail->From = $datos_club["CorreoRemitente"];
        $mail->FromName = "Club";
        $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
        $confirm = $mail->Send();

    }

    public function notifica_pago_extracto($IDClub, $IDSocio, $Valor)
    {
        $dbo = &SIMDB::get();

        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        $correo = $datos_club["EmailCartera"];

        if (!empty($correo)):

            $nombre_socio = $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
            $accion_socio = $datos_socio["Accion"];
            $celular_socio = $datos_socio["Celular"];

            $msg = "<br>Cordial Saludo,<br><br>
		  Se realizó el pago de saldo de cartera del siguiente socio
			Recuerde ingresar al sistema para conocer mas detalles .<br><br>
			Nombre Socio: " . $nombre_socio . "<br>
			Numero Accion: " . $accion_socio . "<br>
	    Valor: " . $Valor . "<br>
			Celular: " . $celular_socio . "<br>
			Cordialmente<br><br>
			<b>Notificaciones " . $datos_club["Nombre"] . "</b>";

            $mensaje = "
					<body>
						<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
							<tr>
								<td>
									<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
								</td>
							</tr>
							<tr>
								<td>" .
                $msg
                . "</td>
							</tr>
						</table>
					</body>
			";

            $url_baja = URLROOT . "contactenos.php";
            $mail = new phpmailer();
            $array_correo = explode(",", $correo);
            if (count($array_correo) > 0) {
                foreach ($array_correo as $correo_value) {
                    $mail->AddAddress($correo_value);
                }
            }

            $mail->Subject = "Pago Cartera";
            $mail->Body = $mensaje;
            $mail->IsHTML(true);
            $mail->Sender = $datos_club["CorreoRemitente"];
            $mail->Timeout = 120;
            //$mail->IsSMTP();
            $mail->Port = PUERTO_SMTP;
            $mail->SMTPAuth = true;
            $mail->Host = HOST_SMTP;
            //$mail->Mailer = 'smtp';
            $mail->Password = PASSWORD_SMPT;
            $mail->Username = USER_SMTP;
            $mail->From = $datos_club["CorreoRemitente"];
            $mail->FromName = "Club";
            $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
            $confirm = $mail->Send();
        endif;
    }

    public function notifica_alerta_acceso($IDClub, $IDInvitacion, $TipoInvitacion, $datos_mensaje)
    {
        $dbo = &SIMDB::get();

        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        $correo = $datos_club["CorreoAlertaCampoAcceso"];

        switch ($TipoInvitacion) {
            case "InvitadoAcceso":
                $datos_invitacion = $dbo->fetchAll("SocioInvitadoEspecial", " IDInvitado = '" . $IDInvitacion . "' ", "array");
                $datos_invitado = $dbo->fetchAll("Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array");
                $datos_persona = $datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"] . " Documento: " . $datos_invitado["NumeroDocumento"];
                break;
            case "SocioAutorizacion":
            case "Contratista":
                $datos_invitacion = $dbo->fetchAll("SocioAutorizacion", " IDInvitado = '" . $IDInvitacion . "' ", "array");
                $datos_invitado = $dbo->fetchAll("Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array");
                $datos_persona = $datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"] . " Documento: " . $datos_invitado["NumeroDocumento"];
                break;
            case "Socio":
                ///Se deja la PreSalida en blanco
                $datos_invitado = $dbo->fetchAll("Socio", " IDSocio = '" . $IDInvitacion . "' ", "array");
                $datos_persona = $datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"] . " Documento: " . $datos_invitado["NumeroDocumento"];
                break;
            case "Usuario":
                $datos_invitado = $dbo->fetchAll("Usuario", " IDUsuario = '" . $IDInvitacion . "' ", "array");
                $datos_persona = $datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"] . " Documento: " . $datos_invitado["NumeroDocumento"];
                break;
            case "SocioInvitado":
            case "Invitado":
                $datos_invitado = $dbo->fetchAll("Invitado", " IDInvitado = '" . $IDInvitacion . "' ", "array");
                $datos_persona = $datos_invitado["Nombre"] . " Documento: " . $datos_invitado["NumeroDocumento"];
                break;

        }

        $Mensaje = "Atencion! La siguiente persona " . $datos_persona . " ingresó superando el limite: " . $datos_mensaje;

        $msg = "<br>Cordial Saludo,<br><br>
		<br><br>" . $Mensaje . "<br><br>

		Por favor no responda este correo, si desea dar una respuesta ingrese a nuestra app<br>
		Cordialmente<br><br>
		<b>Notificaciones " . $datos_club["Nombre"] . "</b>";

        $mensaje = "
				<body>
					<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
						<tr>
							<td>
								<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
							</td>
						</tr>
						<tr>
							<td>" .
            $msg
            . "</td>
						</tr>
					</table>
				</body>
		";

        $url_baja = URLROOT . "contactenos.php";
        $mail = new phpmailer();
        $array_correo = explode(",", $correo);
        if (count($array_correo) > 0) {
            foreach ($array_correo as $correo_value) {
                if (filter_var(trim($correo_value), FILTER_VALIDATE_EMAIL)) {
                    $mail->AddAddress($correo_value);
                }
            }
        }

        $mail->Subject = "Alerta superacion de limite Acceso";
        $mail->Body = $mensaje;
        $mail->IsHTML(true);
        $mail->Sender = $datos_club["CorreoRemitente"];
        $mail->Timeout = 120;
        //$mail->IsSMTP();
        $mail->Port = PUERTO_SMTP;
        $mail->SMTPAuth = true;
        $mail->Host = HOST_SMTP;
        //$mail->Mailer = 'smtp';
        $mail->Password = PASSWORD_SMPT;
        $mail->Username = USER_SMTP;
        $mail->From = $datos_club["CorreoRemitente"];
        $mail->FromName = "Club";
        $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
        $confirm = $mail->Send();

    }

    public function notifica_alerta_diagnostico($IDClub, $IDSocio, $IDDiagnostico, $respuestas_diagnostico, $IDUsuario, $correo, $suma_peso, $TipoUsuario)
    {

        $dbo = &SIMDB::get();

        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");

        if (!empty($IDSocio) && $TipoUsuario == "Socio") {
            $datos_persona = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");

            if ($IDClub == 11) {
                $Mensaje = "Atencion! La siguiente persona " . $datos_persona["Nombre"] . " " . $datos_persona["Apellido"] . "con numero de acción " . $datos_persona["Accion"] . " superó el límite en el diagnostico: (" . $suma_peso . ") <br>" . $respuestas_diagnostico;

            } else {
                $Mensaje = "Atencion! La siguiente persona " . $datos_persona["Nombre"] . " " . $datos_persona["Apellido"] . " superó el límite en el diagnostico: (" . $suma_peso . ") <br>" . $respuestas_diagnostico;

            }

        } elseif (!empty($IDUsuario) && $TipoUsuario == "Funcionario") {
            $datos_persona = $dbo->fetchAll("Usuario", " IDUsuario = '" . $IDUsuario . "' ", "array");
            $Mensaje = "Atencion! La siguiente persona " . $datos_persona["Nombre"] . " " . $datos_persona["Apellido"] . " superó el límite en el diagnostico: (" . $suma_peso . ") <br>" . $respuestas_diagnostico;

        }

        $msg = "<br>Cordial Saludo,<br><br>
		<br><br>" . $Mensaje . "<br><br>

		Por favor no responda este correo, si desea dar una respuesta ingrese a nuestra app<br>
		Cordialmente<br><br>
		<b>Notificaciones " . $datos_club["Nombre"] . "</b>";

        $mensaje = "
				<body>
					<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
						<tr>
							<td>
								<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
							</td>
						</tr>
						<tr>
							<td>" .
            $msg
            . "</td>
						</tr>
					</table>
				</body>
		";

        $url_baja = URLROOT . "contactenos.php";
        $mail = new phpmailer();
        $array_correo = explode(",", $correo);
        if (count($array_correo) > 0) {
            foreach ($array_correo as $correo_value) {
                if (filter_var(trim($correo_value), FILTER_VALIDATE_EMAIL)) {
                    $mail->AddAddress($correo_value);
                }
            }
        }

        $mail->Subject = "Alerta superacion de limite auto-diagnostico";
        $mail->Body = $mensaje;
        $mail->IsHTML(true);
        $mail->Sender = $datos_club["CorreoRemitente"];
        $mail->Timeout = 120;
        //$mail->IsSMTP();
        $mail->Port = PUERTO_SMTP;
        $mail->SMTPAuth = true;
        $mail->Host = HOST_SMTP;
        //$mail->Mailer = 'smtp';
        $mail->Password = PASSWORD_SMPT;
        $mail->Username = USER_SMTP;
        $mail->From = $datos_club["CorreoRemitente"];
        $mail->FromName = "Club";
        //$mail->CharSet = 'UTF-8';
        $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
        $confirm = $mail->Send();

    }

    public function enviar_notificacion_dotacion($IDClub, $TipoUsuario, $IDPersona, $IDDotacion, $respuestas_dotacion, $correo)
    {

        $dbo = &SIMDB::get();

        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        $datos_dotacion = $dbo->fetchAll("Dotacion", "IDDotacion = '" . $IDDotacion . "' ", "array");

        if ($TipoUsuario == "Socio") {
            $datos_persona = $dbo->fetchAll("Socio", " IDSocio = '" . $IDPersona . "' ", "array");
        } elseif ($TipoUsuario == "Funcionario") {
            $datos_persona = $dbo->fetchAll("Usuario", " IDUsuario = '" . $IDPersona . "' ", "array");
        }

        $Mensaje = "El " . $TipoUsuario . " llamado " . $datos_persona["Nombre"] . " " . $datos_persona["Apellido"] . " ha llenado el formulario de dotación: " . $datos_dotacion["Nombre"] . ", por favor revisar.
		<br>Respuestas: <br>" . $respuestas_dotacion;

        $msg = "<br>Cordial Saludo,<br><br>
		<br><br>" . $Mensaje . "<br><br>

		Por favor no responda este correo, si desea dar una respuesta ingrese a nuestra app<br>
		Cordialmente<br><br>
		<b>Notificaciones " . $datos_club["Nombre"] . "</b>";

        $mensaje = "
				<body>
					<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
						<tr>
							<td>
								<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
							</td>
						</tr>
						<tr>
							<td>" .
            $msg
            . "</td>
						</tr>
					</table>
				</body>
		";
        /* //correos a donde se notifica
        $correo = $datos_dotacion['EmailAlerta']; */

        $url_baja = URLROOT . "contactenos.php";
        $mail = new phpmailer();
        $array_correo = explode(",", $correo);
        if (count($array_correo) > 0) {
            foreach ($array_correo as $correo_value) {
                if (filter_var(trim($correo_value), FILTER_VALIDATE_EMAIL)) {
                    $mail->AddAddress($correo_value);
                }
            }
        }
        $AsuntoMensaje = "Respuesta Dotación.";
        $subject = "=?UTF-8?B?" . base64_encode($AsuntoMensaje) . "=?=";

        $mail->Subject = $subject;
        $mail->Body = $mensaje;
        $mail->IsHTML(true);
        $mail->Sender = $datos_club["CorreoRemitente"];
        $mail->Timeout = 120;
        //$mail->IsSMTP();
        $mail->Port = PUERTO_SMTP;
        $mail->SMTPAuth = true;
        $mail->Host = HOST_SMTP;
        //$mail->Mailer = 'smtp';
        $mail->Password = PASSWORD_SMPT;
        $mail->Username = USER_SMTP;
        $mail->From = $datos_club["CorreoRemitente"];
        $mail->FromName = "Club";
        $mail->CharSet = 'UTF-8';
        $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
        $confirm = $mail->Send();

    }

    public function envia_carta_financiera($IDClub, $correo, $Mensaje)
    {
        $dbo = &SIMDB::get();

        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");

        if (!empty($correo)) {

            $mensaje = $Mensaje;

            $url_baja = URLROOT . "contactenos.php";
            $mail = new phpmailer();
            $array_correo = explode(",", $correo);
            if (count($array_correo) > 0) {
                foreach ($array_correo as $correo_value) {
                    $mail->AddAddress($correo_value);
                }
            }

            $mail->Subject = "Aviso financiero";
            $mail->Body = $mensaje;
            $mail->IsHTML(true);
            $mail->Sender = $datos_club["CorreoRemitente"];

            $mail->Timeout = 120;
            //$mail->IsSMTP();
            $mail->Port = PUERTO_SMTP;
            $mail->SMTPAuth = true;
            $mail->Host = HOST_SMTP;
            //$mail->Mailer = 'smtp';
            $mail->Password = PASSWORD_SMPT;
            $mail->Username = USER_SMTP;
            $mail->From = $datos_club["CorreoRemitente"];
            $mail->FromName = "Club";
            $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
            $confirm = $mail->Send();
            //$err=$mail->ErrorInfo();
            //echo "enviado2".$err;
        }

    }

    public function notifica_nuevo_contenido($IDClub, $Modulo, $Titular)
    {
        $dbo = &SIMDB::get();
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        $correo = $datos_club["EmailNotificacionContenido"];

        if (!empty($correo)) {

            $msg = "<br>Cordial Saludo,<br><br>
		Se ha creado un nuevo contenido en: <b>" . $Modulo . "</b> <b>" . $Titular . "</b> para su aprobacion<br><br>

		Por favor no responda este correo, si desea dar una respuesta ingrese a nuestra app<br>
		Cordialmente<br><br>
		<b>Notificaciones " . $datos_club["Nombre"] . "</b>";

            $mensaje = "
				<body>
					<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
						<tr>
							<td>
								<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
							</td>
						</tr>
						<tr>
							<td>" .
                $msg
                . "</td>
						</tr>
					</table>
				</body>
		";

            $url_baja = URLROOT . "contactenos.php";
            $mail = new phpmailer();
            $array_correo = explode(",", $correo);
            if (count($array_correo) > 0) {
                foreach ($array_correo as $correo_value) {
                    $mail->AddAddress($correo_value);
                }
            }

            $url_baja = URLROOT . "contactenos.php";
            $mail = new phpmailer();
            $array_correo = explode(",", $correo);
            if (count($array_correo) > 0) {
                foreach ($array_correo as $correo_value) {
                    if (filter_var(trim($correo_value), FILTER_VALIDATE_EMAIL)) {
                        $mail->AddAddress($correo_value);
                    }
                }
            }

            $mail->Subject = "Nuevo contenido para aprobacion";
            $mail->Body = $mensaje;
            $mail->IsHTML(true);
            $mail->Sender = $datos_club["CorreoRemitente"];
            $mail->Timeout = 120;
            //$mail->IsSMTP();
            $mail->Port = PUERTO_SMTP;
            $mail->SMTPAuth = true;
            $mail->Host = HOST_SMTP;
            //$mail->Mailer = 'smtp';
            $mail->Password = PASSWORD_SMPT;
            $mail->Username = USER_SMTP;
            $mail->From = $datos_club["CorreoRemitente"];
            $mail->FromName = "Club";
            $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
            $confirm = $mail->Send();
        }

    }

    public function envia_correo_pago_reserva($frm)
    {
        $dbo = &SIMDB::get();

        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $frm["IDClub"] . "' ", "array");

        $correo = $frm["EmailDuenoReserva"];
        if (!empty($correo)) {

            $msg = "<br>Cordial Saludo " . $frm["NombreDuenoReserva"] . ",<br><br>
		Se ha realizado una reserva en hotel a su nombre.

		Por favor ingrese al siguiente link para realizar el pago:
		<br><br><a href='" . $datos_club["UrlPagoNoPresencial"] . "'>" . $datos_club["UrlPagoNoPresencial"] . "</a>
		<br><br>Valor Total: $" . number_format($frm["Valor"], 0, '', '.') . "
		<br><br>Por favor no responda este correo<br>
		Cordialmente<br><br>
		<b>Notificaciones " . $datos_club["Nombre"] . "</b>";

            $mensaje = "
				<body>
					<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
						<tr>
							<td>
								<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
							</td>
						</tr>
						<tr>
							<td>" .
                $msg
                . "</td>
						</tr>
					</table>
				</body>
		";

            $url_baja = URLROOT . "contactenos.php";
            $mail = new phpmailer();
            $array_correo = explode(",", $correo);
            if (count($array_correo) > 0) {
                foreach ($array_correo as $correo_value) {
                    $mail->AddAddress($correo_value);
                }
            }

            $mail->Subject = "Reserva hotel";
            $mail->Body = $mensaje;
            $mail->IsHTML(true);
            $mail->Sender = $datos_club["CorreoRemitente"];
            $mail->Timeout = 120;
            //$mail->IsSMTP();
            $mail->Port = PUERTO_SMTP;
            $mail->SMTPAuth = true;
            $mail->Host = HOST_SMTP;
            //$mail->Mailer = 'smtp';
            $mail->Password = PASSWORD_SMPT;
            $mail->Username = USER_SMTP;
            $mail->From = $datos_club["CorreoRemitente"];
            $mail->FromName = "Club";
            $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
            $confirm = $mail->Send();
        }

    }

    public function envia_respuesta_cliente($frm, $id, $respuesta, $IDClub, $IDAreainteres = "")
    {
        $dbo = &SIMDB::get();

        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        $datos_pqr = $dbo->fetchAll("Pqr", " IDPqr = '" . $id . "' ", "array");
        $correo_func = utf8_encode($dbo->getFields("Area", "CorreoResponsable", "IDArea = '" . $datos_pqr[IDArea] . "'"));
        $correo_func_interes = utf8_encode($dbo->getFields("Area", "CorreoResponsable", "IDArea = '" . $IDAreainteres . "'"));

        $correo = $dbo->getFields("Socio", "CorreoElectronico", "IDSocio = '" . $frm[IDSocio] . "'");

        if (!empty($correo)) {

            switch ($frm[Tipo]) {
                case "P":
                    $tipo = "Peticion";
                    break;
                case "Q":
                    $tipo = "Queja";
                    break;
                case "R":
                    $tipo = "Reclamo";
                    break;
            }

            $historial_respuesta = "<table width='80%' style='border:1px solid'>";
            //Consulto historila del respuestas
            $sql_detalle = "SELECT * FROM Detalle_Pqr WHERE IDPQR = '" . $id . "' Order By 	IDDetallePqr Desc";
            $qry_detalle = $dbo->query($sql_detalle);
            while ($detalle = $dbo->object($qry_detalle)) {
                $historial_respuesta .= "
  				<tr>
  					<td>";
                if ($detalle->IDUsuario > 0) {
                    $nombre_responsable = $dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $detalle->IDUsuario . "'");
                    $persona_responde = (isset($nombre_responsable) ? $nombre_responsable : '<em>N/A</em>');
                } elseif ($detalle->IDSocio > 0) {
                    $nombre_cliente = $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $detalle->IDSocio . "'");
                    $apellido_cliente = $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $detalle->IDSocio . "'");
                    $persona_responde = "(socio) " . (isset($nombre_cliente) ? $nombre_cliente . " " . $apellido_cliente : '<em>N/A</em>');
                }

                $persona_responde = "";
                $historial_respuesta .= $persona_responde . "
  					</td>
                      <td>" .
                $detalle->Fecha . "
                      </td>
  					<td>
  						" . utf8_encode($detalle->Respuesta) . "
  					</td>
  				<tr>";
            }

            $historial_respuesta .= "</table>";

            $msg = "<br>Cordial Saludo,<br><br>
  		Se ha dado la siguiente respuesta a su solicitud.
  		Recuerde ingresar al App para conocer mas detalles .<br><br>
  		Numero: " . $datos_pqr["Numero"] . "<br>
  		Tipo: " . $tipo . "<br>
  		Descripcion: " . $frm[Descripcion] . "<br>
  		Fecha: " . $frm[Fecha] . "<br><br>
  		" . $historial_respuesta . "<br><br>

  		Por favor no responda este correo, si desea dar una respuesta ingrese a nuestra app<br>
  		Cordialmente<br><br>
  		<b>Notificaciones " . $datos_club["Nombre"] . "</b>";

            $mensaje = "
  				<body>
  					<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
  						<tr>
  							<td>
  								<img src='" . CLUB_ROOT . $datos_club["FotoDiseno1"] . "'>
  							</td>
  						</tr>
  						<tr>
  							<td>" .
                $msg
                . "</td>
  						</tr>
  					</table>
  				</body>
  		";

            $url_baja = URLROOT . "contactenos.php";
            $mail = new phpmailer();
            $array_correo = explode(",", $correo);
            if (count($array_correo) > 0) {
                foreach ($array_correo as $correo_value) {
                    if (filter_var(trim($correo_value), FILTER_VALIDATE_EMAIL)) {
                        $mail->AddAddress($correo_value);
                    }
                }
            }

            if (!empty($correo_func_interes)) {
                $correo_func = "," . $correo_func_interes;
            }
            $array_correo_func = explode(",", $correo_func);
            if (count($array_correo_func) > 0) {
                foreach ($array_correo_func as $correo_func_value) {
                    if (filter_var(trim($correo_value), FILTER_VALIDATE_EMAIL)) {
                        $mail->AddBCC($correo_func_value, "Pqr");
                    }
                }
            }

            $mail->Subject = "Respuesta PQR " . $datos_club["Nombre"] . " " . $datos_pqr["Numero"];
            $mail->Body = $mensaje;
            $mail->IsHTML(true);
            $mail->Sender = $datos_club["CorreoRemitente"];
            $mail->Timeout = 120;
            //$mail->IsSMTP();
            $mail->Port = PUERTO_SMTP;
            $mail->SMTPAuth = true;
            $mail->Host = HOST_SMTP;
            //$mail->Mailer = 'smtp';
            $mail->Password = PASSWORD_SMPT;
            $mail->Username = USER_SMTP;
            $mail->From = $datos_club["CorreoRemitente"];
            $mail->FromName = "Club";
            $mail->CharSet = 'UTF-8';
            $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
            $confirm = $mail->Send();

        }

    }

    public function envia_respuesta_funcionario($frm, $id, $respuesta, $IDClub)
    {
        $dbo = &SIMDB::get();

        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        $datos_pqr = $dbo->fetchAll("PqrFuncionario", " IDPqr = '" . $id . "' ", "array");

        $correo_resp = utf8_encode($dbo->getFields("AreaFuncionario", "CorreoResponsable", "IDArea = '" . $datos_pqr[IDArea] . "'"));
        $correo = $dbo->getFields("Usuario", "Email", "IDUsuario = '" . $frm[IDUsuarioCreacion] . "'");

        if (!empty($correo)):
            switch ($frm[Tipo]) {
                case "P":
                    $tipo = "Peticion";
                    break;
                case "Q":
                    $tipo = "Queja";
                    break;
                case "R":
                    $tipo = "Reclamo";
                    break;
            }

            $historial_respuesta = "<table width='80%' style='border:1px solid'>";
            //Consulto historila del respuestas
            $sql_detalle = "SELECT * FROM Detalle_PqrFuncionario WHERE IDPqr = '" . $id . "' Order By IDDetallePqr Desc";
            $qry_detalle = $dbo->query($sql_detalle);
            while ($detalle = $dbo->object($qry_detalle)) {
                $historial_respuesta .= "
							<tr>
								<td>";
                if ($detalle->IDUsuario > 0) {
                    $nombre_responsable = $dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $detalle->IDUsuario . "'");
                    $persona_responde = (isset($nombre_responsable) ? $nombre_responsable : '<em>N/A</em>');
                } elseif ($detalle->IDUsuarioCreacion > 0) {
                $nombre_cliente = $dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $detalle->IDUsuarioCreacion . "'");
                $persona_responde = "(funcionario) " . (isset($nombre_cliente) ? $nombre_cliente : '<em>N/A</em>');
            }

            $historial_respuesta .= $persona_responde . "
							</td>
							<td>" .
            $detalle->Fecha . "
							</td>
							<td>
								" . utf8_encode($detalle->Respuesta) . "
							</td>
						<tr>";
        }

        $historial_respuesta .= "</table>";

        $msg = "<br>Cordial Saludo,<br><br>
				Se ha dado la siguiente respuesta a su solicitud:
				Recuerde ingresar al App para conocer mas detalles .<br><br>
				Numero: " . $datos_pqr["Numero"] . "<br>
				Tipo: " . $tipo . "<br>
				Descripcion: " . $frm[Descripcion] . "<br>
				Fecha: " . $frm[Fecha] . "<br><br>
				" . $historial_respuesta . "<br><br>

				Por favor no responda este correo, si desea dar una respuesta ingrese a nuestra app<br>
				Cordialmente<br><br>
				<b>Notificaciones " . $datos_club["Nombre"] . "</b>";

        $mensaje = "
						<body>
							<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
								<tr>
									<td>
										<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
									</td>
								</tr>
								<tr>
									<td>" .
            $msg
            . "</td>
								</tr>
							</table>
						</body>
				";

        $url_baja = URLROOT . "contactenos.php";
        $mail = new phpmailer();
        $array_correo = explode(",", $correo);
        if (count($array_correo) > 0) {
            foreach ($array_correo as $correo_value) {
                $mail->AddAddress($correo_value);
            }
        }

        $array_correo_resp = explode(",", $correo_resp);
        if (count($array_correo_resp) > 0) {
            foreach ($array_correo_resp as $correo_resp_value) {
                $mail->AddBCC($correo_resp_value);
            }
        }

        $mail->Subject = "Respuesta PQR Funcionario " . $datos_club["Nombre"] . " " . $datos_pqr["Numero"];
        $mail->Body = $mensaje;
        $mail->IsHTML(true);
        $mail->Sender = $datos_club["CorreoRemitente"];
        $mail->Timeout = 120;
        //$mail->IsSMTP();
        $mail->Port = PUERTO_SMTP;
        $mail->SMTPAuth = true;
        $mail->Host = HOST_SMTP;
        //$mail->Mailer = 'smtp';
        $mail->Password = PASSWORD_SMPT;
        $mail->Username = USER_SMTP;
        $mail->From = $datos_club["CorreoRemitente"];
        $mail->FromName = "Club";
        $mail->CharSet = 'UTF-8';
        $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
        $confirm = $mail->Send();
        endif;

    }

    public function enviar_notificacion_push_general($IDClub, $IDSocio, $Mensaje, $IDModulo = "", $IDDetalle = "")
    {

        $dbo = &SIMDB::get();

        $sql_socios = "SELECT Socio.* FROM  Socio WHERE Socio.IDClub = '" . $IDClub . "' and IDSocio in (" . $IDSocio . ") AND Token <> '' and Token <> '2byte'";
        $qry_socios = $dbo->query($sql_socios);
        $notificaciones = $dbo->rows($qry_socios);
        while ($r_socios = $dbo->fetchArray($qry_socios)) {
            $users = array(array("id" => $r_socios["IDSocio"],
                "idclub" => $r_socios["IDClub"],
                "registration_key" => $r_socios["Token"],
                "deviceType" => $r_socios["Dispositivo"]),

            );

            $message = $Mensaje;
            $custom["tipo"] = "General";
            $custom["idseccion"] = (string) $IDModulo;
            $custom["iddetalle"] = (string) $IDDetalle;
            $custom["idmodulo"] = (string) $IDModulo;
            $custom["titulo"] = "Notificacion";

            ///enviar notificación
            //SIMUtil::sendAlerts($users, $message, $custom);

            if ($r_socios["Dispositivo"] == "iOS") {
                $array_ios[] = $r_socios["Token"];
            } elseif ($r_socios["Dispositivo"] == "Android") {
                $array_android[] = $r_socios["Token"];
            }

            SIMUtil::sendAlerts_V2($users, $message, $custom, $TipoApp, $array_android, $array_ios, $IDClub);

            //Guardo el log
            $sql_log = $dbo->query("Insert Into LogNotificacion (IDNotificacionesGenerales	, IDSocio, IDClub, Token, Dispositivo, Fecha, App, Tipo, Titulo, Mensaje, Modulo, IDDetalle, IDSeccion) Values ('" . $id . "', '" . $r_socios["IDSocio"] . "','" . $r_socios["IDClub"] . "','" . $r_socios["Token"] . "','" . $r_socios["Dispositivo"] . "',NOW(),'Socio','" . $custom["tipo"] . "', '" . $custom["titulo"] . "', '" . $message . "', '" . $custom["idseccion"] . "', '" . $custom["iddetalle"] . "','28')");
        }
        return true;
    }

    public function enviar_notificacion_push_clasificado($IDClub, $IDSocio, $Mensaje, $id_clasificado)
    {

        $dbo = &SIMDB::get();
        $datos_clasificado = $dbo->fetchAll("Clasificado", " IDClasificado = '" . $id_clasificado . "' ", "array");
        $sql_socios = "SELECT Socio.* FROM  Socio WHERE Socio.IDClub = '" . $IDClub . "' and IDSocio in (" . $IDSocio . ") AND Token <> '' and Token <> '2byte'";
        $qry_socios = $dbo->query($sql_socios);
        $notificaciones = $dbo->rows($qry_socios);
        while ($r_socios = $dbo->fetchArray($qry_socios)) {
            $users = array(array("id" => $r_socios["IDSocio"],
                "idclub" => $r_socios["IDClub"],
                "registration_key" => $r_socios["Token"],
                "deviceType" => $r_socios["Dispositivo"]),

            );

            $message = $Mensaje;
            $custom["tipo"] = "Clasificado";
            $custom["idseccion"] = (string) "46";
            $custom["iddetalle"] = (string) $id_clasificado;
            $custom["titulo"] = "Notificacion Club";
            $IDCategoria = $datos_clasificado["IDSeccionClasificados"];

            ///enviar notificación
            //SIMUtil::sendAlerts($users, $message, $custom);

            if ($r_socios["Dispositivo"] == "iOS") {
                $array_ios[] = $r_socios["Token"];
            } elseif ($r_socios["Dispositivo"] == "Android") {
                $array_android[] = $r_socios["Token"];
            }

            SIMUtil::sendAlerts_V2($users, $message, $custom, $TipoApp, $array_android, $array_ios, $IDClub);

            //Guardo el log

            $sql_log = $dbo->query("Insert Into LogNotificacion (IDNotificacionesGenerales	, IDSocio, IDClub, Token, Dispositivo, Fecha, App, Tipo, Titulo, Mensaje, Modulo, IDDetalle, IDSeccion) Values ('" . $id . "', '" . $r_socios["IDSocio"] . "','" . $r_socios["IDClub"] . "','" . $r_socios["Token"] . "','" . $r_socios["Dispositivo"] . "',NOW(),'Socio','" . $custom["tipo"] . "',
				'" . $custom["titulo"] . "', '" . $message . "', '" . $custom["idseccion"] . "', '" . $custom["iddetalle"] . "','" . $IDCategoria . "')");

        }
        return true;
    }

    public function enviar_notificacion_push_general_funcionario($IDClub, $IDUsuario, $Mensaje)
    {

        $dbo = &SIMDB::get();

        $sql_socios = "SELECT Usuario.* FROM  Usuario WHERE Usuario.IDClub = '" . $IDClub . "' and IDUsuario in (" . $IDUsuario . ") AND Token <> '' and Token <> '2byte'";
        $qry_socios = $dbo->query($sql_socios);
        $notificaciones = $dbo->rows($qry_socios);
        while ($r_socios = $dbo->fetchArray($qry_socios)) {
            $users = array(array("id" => $r_socios["IDUsuario"],
                "idclub" => $r_socios["IDClub"],
                "registration_key" => $r_socios["Token"],
                "deviceType" => $r_socios["Dispositivo"]),

            );

            $message = $Mensaje;
            $custom["tipo"] = "General";
            $custom["idseccion"] = (string) "0";
            $custom["iddetalle"] = (string) "0";
            $custom["titulo"] = "Notificacion Club";

            if ($r_socios["Dispositivo"] == "iOS") {
                $array_ios[] = $r_socios["Token"];
            } elseif ($r_socios["Dispositivo"] == "Android") {
                $array_android[] = $r_socios["Token"];
            }

            SIMUtil::sendAlerts_V2($users, $message, $custom, $TipoApp, $array_android, $array_ios, $IDClub);

            ///enviar notificación
            //SIMUtil::sendAlerts($users, $message, $custom,"Empleado");
            //Guardo el log
            $sql_log = $dbo->query("Insert Into LogNotificacion (IDNotificacionesGenerales	, IDSocio, IDClub, Token, Dispositivo, Fecha, App, Tipo, Titulo, Mensaje, Modulo, IDDetalle) Values ('" . $id . "', '" . $r_socios["IDSocio"] . "','" . $r_socios["IDClub"] . "','" . $r_socios["Token"] . "','" . $r_socios["Dispositivo"] . "',NOW(),'Socio','" . $custom["tipo"] . "', '" . $custom["titulo"] . "', '" . $message . "', '" . $custom["idseccion"] . "', '" . $custom["iddetalle"] . "')");
        }
        return true;
    }

    public function enviar_notificacion_push_entrega_vehiculo($IDClub, $Mensaje)
    {

        $dbo = &SIMDB::get();

        $sql_socios = "SELECT Usuario.* FROM  Usuario WHERE Usuario.IDClub = '" . $IDClub . "' and IDPerfil = '34' AND Token <> '' and Token <> '2byte' and Activo='S'";
        $qry_socios = $dbo->query($sql_socios);
        $notificaciones = $dbo->rows($qry_socios);
        while ($r_socios = $dbo->fetchArray($qry_socios)) {
            $users = array(array("id" => $r_socios["IDUsuario"],
                "idclub" => $r_socios["IDClub"],
                "registration_key" => $r_socios["Token"],
                "deviceType" => $r_socios["Dispositivo"]),

            );

            $message = $Mensaje;
            $custom["tipo"] = "General";
            $custom["idseccion"] = (string) "0";
            $custom["iddetalle"] = (string) "0";
            $custom["titulo"] = "Notificacion Club";
            ///enviar notificación

            if ($r_socios["Dispositivo"] == "iOS") {
                $array_ios[] = $r_socios["Token"];
            } elseif ($r_socios["Dispositivo"] == "Android") {
                $array_android[] = $r_socios["Token"];
            }

            SIMUtil::sendAlerts_V2($users, $message, $custom, $TipoApp, $array_android, $array_ios, $IDClub);

            //SIMUtil::sendAlerts($users, $message, $custom,"Empleado");
            //Guardo el log
            $sql_log = $dbo->query("Insert Into LogNotificacion (IDNotificacionesGenerales	, IDUsuario, IDClub, Token, Dispositivo, Fecha, App, Tipo, Titulo, Mensaje, Modulo, IDDetalle) Values ('" . $id . "', '" . $r_socios["IDUsuario"] . "','" . $r_socios["IDClub"] . "','" . $r_socios["Token"] . "','" . $r_socios["Dispositivo"] . "',NOW(),'Empleado','" . $custom["tipo"] . "', '" . $custom["titulo"] . "', '" . $message . "', '" . $custom["idseccion"] . "', '" . $custom["iddetalle"] . "')");
        }
        return true;
    }

    public function notifica_recibo_domicilio($IDDomicilio, $Version = "")
    {
        $dbo = &SIMDB::get();

        $datos_domicilio = $dbo->fetchAll("Domicilio" . $Version, " IDDomicilio = '" . $IDDomicilio . "' ", "array");
        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_domicilio["IDSocio"] . "' ", "array");
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $datos_domicilio["IDClub"] . "' ", "array");
        $datos_config_domicilio = $dbo->fetchAll("ConfiguracionDomicilios" . $Version, " IDClub = '" . $datos_domicilio["IDClub"] . "' Limit 1", "array");

        $correo = $datos_config_domicilio["EmailNotificacion"];

        if (!empty($datos_socio["CorreoElectronico"])) {
            $correo .= "," . $datos_socio["CorreoElectronico"];
        }

        //$correo="parallevar@clublagartos.com";
        //$correo="jorgechirivi@gmail.com";
        if (!empty($correo)):
            $numero_pedido = $datos_domicilio["Numero"];
            $nombre_socio = $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
            $accion_socio = $datos_socio["Accion"];
            $celular_socio = $datos_socio["Celular"];
            $direccion_socio = $datos_domicilio["Direccion"] . " otra direccion registrada : " . $datos_socio["Direccion"];

            if ($datos_club["IDClub"] != 7) {
                $hora_entrega = $datos_domicilio["HoraEntrega"];
            }

            $hora_solicitud = $datos_domicilio["FechaTrCr"];
            $pedido = "";
            $sql = "SELECT * FROM DomicilioDetalle" . $Version . " WHERE IDDomicilio = '" . $IDDomicilio . "'";
            $qry = $dbo->query($sql);
            while ($r = $dbo->fetchArray($qry)) {
                $pedido .= $r["Producto"] = $dbo->getFields("Producto" . $Version, "Nombre", "IDProducto = '" . $r["IDProducto"] . "'");
                $pedido .= ":" . $r["Cantidad"] . " " . "Comentario: " . $r["Comentario"] . "<br>";
            } //ednw hile

            $comentarios = $datos_domicilio["ComentariosSocio"];

            $msg = "<br>Cordial Saludo,<br><br>
				Se ha recibido la  siguiente solicitud de 'Pedido'.
				Recuerde ingresar al sistema para conocer mas detalles .<br><br>
				Nombre Socio: " . $nombre_socio . "<br>
				Numero Accion: " . $accion_socio . "<br>
				Celular: " . $celular_socio . "<br>
				Direccion: " . $direccion_socio . "<br>
				Hora Solicitud: " . $hora_solicitud . "<br>
				Hora Entrega:" . $hora_entrega . "<br>
				Descripcion Pedido:<br>" . $pedido . "<br>
				Comentarios:" . $comentarios . "<br><br>

				Cordialmente<br><br>
				<b>Notificaciones " . $datos_club["Nombre"] . "</b>";
            //especial para country que no quiere la imagen.
            if ($datos_domicilio["IDClub"] != 44) {
                $mensaje = "
					<body>
						<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
							<tr>
								<td>

									<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
								</td>
							</tr>
							<tr>
								<td>" .
                    $msg
                    . "</td>
							</tr>
						</table>
					</body>
					";
            } else {
                $mensaje = "
					<body>
						<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
							<tr>
								<td>
								</td>
							</tr>
							<tr>
								<td>" .
                    $msg
                    . "</td>
							</tr>
						</table>
					</body>
					";
            }

            $url_baja = URLROOT . "contactenos.php";
            $mail = new phpmailer();
            $array_correo = explode(",", $correo);
            if (count($array_correo) > 0) {
                foreach ($array_correo as $correo_value) {
                    if (filter_var(trim($correo_value), FILTER_VALIDATE_EMAIL)) {
                        $mail->AddAddress($correo_value);
                    }
                }
            }

            if($datos_config_domicilio["IDConfiguracionDomicilios"] == 14)
                $asunto = "Pedido Gun Express Numero: " . $numero_pedido;
            else
                $asunto = "Pedido Numero: " . $numero_pedido;

            $mail->Subject = $asunto;
            $mail->Body = $mensaje;
            $mail->IsHTML(true);
            $mail->Sender = $datos_club["CorreoRemitente"];
            $mail->Timeout = 120;
            //$mail->IsSMTP();
            $mail->Port = PUERTO_SMTP;
            $mail->SMTPAuth = true;
            $mail->Host = HOST_SMTP;
            //$mail->Mailer = 'smtp';
            $mail->Password = PASSWORD_SMPT;
            $mail->Username = USER_SMTP;
            $mail->From = $datos_club["CorreoRemitente"];
            $mail->FromName = "Club";
            $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
            $confirm = $mail->Send();
        endif;
    }

    public function notifica_elimina_domicilio($IDDomicilio)
    {
        $dbo = &SIMDB::get();

        $datos_domicilio = $dbo->fetchAll("Domicilio", " IDDomicilio = '" . $IDDomicilio . "' ", "array");
        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_domicilio["IDSocio"] . "' ", "array");
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $datos_domicilio["IDClub"] . "' ", "array");
        $datos_config_domicilio = $dbo->fetchAll("ConfiguracionDomicilios", " IDClub = '" . $datos_domicilio["IDClub"] . "' LIMIT 1", "array");

        //$correo="parallevar@clublagartos.com";
        //$correo="jorgechirivi@gmail.com";
        $correo = $datos_config_domicilio["EmailNotificacion"];

        if (!empty($correo)):

            $numero_pedido = $datos_domicilio["Numero"];
            $nombre_socio = $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
            $accion_socio = $datos_socio["Accion"];
            $celular_socio = $datos_socio["Celular"];
            $hora_entrega = $datos_domicilio["HoraEntrega"];
            $hora_solicitud = $datos_domicilio["FechaTrCr"];
            $pedido = "";
            $sql = "SELECT * FROM DomicilioDetalle WHERE IDDomicilio = '" . $IDDomicilio . "'";
            $qry = $dbo->query($sql);
            while ($r = $dbo->fetchArray($qry)) {
                $pedido .= $r["Producto"] = utf8_encode($dbo->getFields("Producto", "Nombre", "IDProducto = '" . $r["IDProducto"] . "'"));
                $pedido .= ":" . $r["Cantidad"] . "\n";
            } //ednw hile

            $comentarios = $datos_domicilio["ComentariosSocio"];

            $msg = "<br>Cordial Saludo,<br><br>
			Se ha CANCELADO la  siguiente solicitud de 'Pedido'.
			Recuerde ingresar al sistema para conocer mas detalles .<br><br>
			Nombre Socio: " . $nombre_socio . "<br>
			Numero Accion: " . $accion_socio . "<br>
			Celular: " . $celular_socio . "<br>
			Hora Solicitud: " . $hora_solicitud . "<br>
			Hora Entrega:" . $hora_entrega . "<br>
			Descripcion Pedido:" . $pedido . "<br>
			Comentarios:" . $comentarios . "<br><br>

			Cordialmente<br><br>
			<b>Notificaciones " . $datos_club["Nombre"] . "</b>";

            $mensaje = "
					<body>
						<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
							<tr>
								<td>
									<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
								</td>
							</tr>
							<tr>
								<td>" .
                $msg
                . "</td>
							</tr>
						</table>
					</body>
			";

            $url_baja = URLROOT . "contactenos.php";
            $mail = new phpmailer();
            $array_correo = explode(",", $correo);
            if (count($array_correo) > 0) {
                foreach ($array_correo as $correo_value) {
                    $mail->AddAddress($correo_value);
                }
            }

            $mail->Subject = "Cancelacion Pedido Numero: " . $numero_pedido;
            $mail->Body = $mensaje;
            $mail->IsHTML(true);
            $mail->Sender = $datos_club["CorreoRemitente"];
            $mail->Timeout = 120;
            //$mail->IsSMTP();
            $mail->Port = PUERTO_SMTP;
            $mail->SMTPAuth = true;
            $mail->Host = HOST_SMTP;
            //$mail->Mailer = 'smtp';
            $mail->Password = PASSWORD_SMPT;
            $mail->Username = USER_SMTP;
            $mail->From = $datos_club["CorreoRemitente"];
            $mail->FromName = "Club";
            $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
            $confirm = $mail->Send();
        endif;
    }

    public static function notifica_nuevo_cometario_noticia($IDNoticia, $Version = "", $Comentario, $IDSocio, $IDUsuario)
    {
        $dbo = &SIMDB::get();

        if ($Version == 1) {
            $VersionNoticia = "";
        } else {
            $VersionNoticia = $Version;
        }

        $datos_noticia = $dbo->fetchAll("Noticia" . $VersionNoticia, " IDNoticia= '" . $IDNoticia . "' ", "array");
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $datos_noticia["IDClub"] . "' ", "array");

        //$correo="parallevar@clublagartos.com";
        //$correo="jorgechirivi@gmail.com";
        $correo = $datos_club["CorreoNotificacionComentarioNoticia"];

        if (!empty($correo)):

            if (!empty($IDSocio)) {
                $datos_Socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "'", "array");
            } else {
                $datos_Socio = $dbo->fetchAll("Usuario", " IDUsuario = '" . $IDUsuario . "'", "array");
            }

            $msg = "<br>Saludos,<br><br>
			El Socio <strong>" . $datos_Socio["Nombre"] . " " . $datos_Socio["Apellido"] . "</strong> ha comentado la noticia <strong>" . $datos_noticia["Titular"] . "</strong>.<br><br>
			No olvides pasar por el administrador para autorizar la publicación del comentario.	<br><br>
			Comentario : <strong> " . $Comentario . "</strong><br><br>
			Este es un correo informativo, por favor, no respondas al mismo.<br><br>
			Cordialmente<br><br>
			<b>Notificaciones " . $datos_club["Nombre"] . "</b>";

            $mensaje = "
					<body>
						<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
							<tr>
								<td>
									<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
								</td>
							</tr>
							<tr>
								<td>" .
                $msg
                . "</td>
							</tr>
						</table>
					</body>
			";

            $url_baja = URLROOT . "contactenos.php";
            $mail = new phpmailer();
            $array_correo = explode(",", $correo);
            if (count($array_correo) > 0) {
                foreach ($array_correo as $correo_value) {
                    $mail->AddAddress($correo_value);
                }
            }

            $mail->Subject = "Nuevo Comentario Noticias";
            $mail->Body = $mensaje;
            $mail->IsHTML(true);
            $mail->Sender = $datos_club["CorreoRemitente"];
            $mail->Timeout = 120;
            //$mail->IsSMTP();
            $mail->Port = PUERTO_SMTP;
            $mail->SMTPAuth = true;
            $mail->Host = HOST_SMTP;
            //$mail->Mailer = 'smtp';
            $mail->Password = PASSWORD_SMPT;
            $mail->Username = USER_SMTP;
            $mail->From = $datos_club["CorreoRemitente"];
            $mail->FromName = $datos_club["RemitenteCorreo"];
            $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miempresapp.com>,  <$url_baja>");
            $confirm = $mail->Send();
        endif;
    }

    public function noticar_nuevo_pqr($id_pqr, $TipoNotif = "")
    {
        $dbo = &SIMDB::get();

        $datos_pqr = $dbo->fetchAll("Pqr", " IDPqr = '" . $id_pqr . "' ", "array");
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $datos_pqr["IDClub"] . "' ", "array");
        $correo = utf8_encode($dbo->getFields("Area", "CorreoResponsable", "IDArea = '" . $datos_pqr[IDArea] . "'"));
        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_pqr[IDSocio] . "' ", "array");

        if (!empty($correo)):
            switch ($datos_pqr[Tipo]) {
                case "P":
                    $tipo = "Peticion";
                    break;
                case "Q":
                    $tipo = "Queja";
                    break;
                case "R":
                    $tipo = "Reclamo";
                    break;
            }

            $msg = "<br>Cordial Saludo,<br><br>
				Se ha generado un nuevo pqr por parte de un socio.
				Recuerde ingresar al sistema para conocer mas detalles .<br><br>
				Numero: " . $datos_pqr["Numero"] . "<br>
				Socio: " . utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"]) . " Accion: " . $datos_socio["Accion"] . "" . "<br>
				Correo: " . utf8_encode($datos_socio["CorreoElectronico"]) . "<br>
				Area: " . utf8_decode($dbo->getFields("Area", "Nombre", "IDArea = '" . $datos_pqr[IDArea] . "'")) . "<br>
				Tipo: " . utf8_decode($dbo->getFields("TipoPqr", "Nombre", "IDTipoPqr = '" . $datos_pqr[IDTipoPqr] . "'")) . "<br>
				Asunto: " . $datos_pqr[Asunto] . "<br>
				Descripcion: " . $datos_pqr[Descripcion] . "<br>
				Estado: " . utf8_encode($dbo->getFields("PqrEstado", "Nombre", "IDPqrEstado = '" . $datos_pqr["IDPqrEstado"] . "'")) . "<br>
				Fecha: " . $datos_pqr[Fecha] . "<br><br>

				Por favor no responda este correo, si desea dar una respuesta ingrese al administrador<br>
				Cordialmente<br><br>
				<b>Notificaciones " . utf8_encode($datos_club["Nombre"]) . "</b>";

            $mensaje = "
						<body>
							<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
								<tr>
									<td>
										<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
									</td>
								</tr>
								<tr>
									<td>" .
                $msg
                . "</td>
								</tr>
							</table>
						</body>
				";

            $url_baja = URLROOT . "contactenos.php";
            $mail = new phpmailer();
            $array_correo = explode(",", $correo);
            if (count($array_correo) > 0) {
                foreach ($array_correo as $correo_value) {
                    if (filter_var(trim($correo_value), FILTER_VALIDATE_EMAIL)) {
                        $mail->AddAddress($correo_value);
                        $enviar_a = $correo_value;
                    }
                }

                if ($TipoNotif == "") {
                    $TipoNotif = "Nuevo PQR";
                }

                $mail->Subject = $TipoNotif . " " . $datos_club["Nombre"] . " " . $datos_pqr["Numero"];
                $mail->Body = $mensaje;
                $mail->IsHTML(true);
                $mail->Sender = $datos_club["CorreoRemitente"];
                $mail->Timeout = 120;
                //$mail->IsSMTP();
                $mail->Port = PUERTO_SMTP;
                $mail->SMTPAuth = true;
                $mail->Host = HOST_SMTP;
                //$mail->Mailer = 'smtp';
                $mail->Password = PASSWORD_SMPT;
                $mail->Username = USER_SMTP;
                $mail->From = $datos_club["CorreoRemitente"];
                $mail->FromName = "Club";
                $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
                /* $mail->addCustomHeader('Content-type',"charset=UTF-8"); */
                $confirm = $mail->Send();

                //Envio tambien el push con la notificacion del nuevo pqr
                $sql_usu_area = "SELECT * FROM Usuario U, UsuarioArea UA WHERE U.IDUsuario=UA.IDUsuario and U.IDClub='" . $datos_club["IDClub"] . "' and UA.IDArea = '" . $datos_pqr[IDArea] . "' Limit 3";
                $r_usu_area = $dbo->query($sql_usu_area);
                while ($row_usua_area = $dbo->fetchArray($r_usu_area)) {
                    $Mensaje = "Se ha recibido un nuevo pqr por parte de un socio:" . $datos_pqr["Numero"];
                    SIMUtil::enviar_notificacion_push_general_funcionario($datos_club["IDClub"], $row_usua_area["IDUsuario"], $Mensaje);
                }

                //Envio tambien el push
                if (!empty($datos_club["MensajePushCrearPqr"])) {
                    SIMUtil::enviar_notificacion_push_general($datos_club["IDClub"], $datos_socio["IDSocio"], $datos_club["MensajePushCrearPqr"], 15, $id_pqr);
                }

                //self::sendMail( $enviar_a , "Nuevo PQR " .$datos_club["Nombre"]." " . $datos_pqr["Numero"] , $mensaje , $vars , $exclude , "noreplay@miclubapp.com" , $cabs );

            }

        endif;
    }

    public function noticar_respuesta_aut_pqr($id_pqr)
    {
        $dbo = &SIMDB::get();

        $datos_pqr = $dbo->fetchAll("Pqr", " IDPqr = '" . $id_pqr . "' ", "array");
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $datos_pqr["IDClub"] . "' ", "array");
        $correo = utf8_encode($dbo->getFields("Socio", "CorreoElectronico", "IDSocio = '" . $datos_pqr["IDSocio"] . "'"));

        if (!empty($correo) && $datos_club["RespuestaAutomaticaPqr"] == "S" && !empty($datos_club["TextoRespuestaAutomaticaPqr"])):

            $msg = $datos_club["TextoRespuestaAutomaticaPqr"];

            $mensaje = "
						<body>
							<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
								<tr>
									<td>
										<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
									</td>
								</tr>
								<tr>
									<td>" .
                $msg
                . "</td>
								</tr>
							</table>
						</body>
				";

            $url_baja = URLROOT . "contactenos.php";
            $mail = new phpmailer();
            $array_correo = explode(",", $correo);
            if (count($array_correo) > 0) {
                foreach ($array_correo as $correo_value) {
                    if (filter_var(trim($correo_value), FILTER_VALIDATE_EMAIL)) {
                        $mail->AddAddress($correo_value);
                        $enviar_a = $correo_value;
                    }
                }

                $mail->Subject = "PQR " . $datos_club["Nombre"] . " " . $datos_pqr["Numero"];
                $mail->Body = $mensaje;
                $mail->IsHTML(true);
                $mail->Sender = $datos_club["CorreoRemitente"];
                $mail->Timeout = 120;
                //$mail->IsSMTP();
                $mail->Port = PUERTO_SMTP;
                $mail->SMTPAuth = true;
                $mail->Host = HOST_SMTP;
                //$mail->Mailer = 'smtp';
                $mail->Password = PASSWORD_SMPT;
                $mail->Username = USER_SMTP;
                $mail->From = $datos_club["CorreoRemitente"];
                $mail->FromName = "Club";
                $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
                $confirm = $mail->Send();

            }

        endif;
    }

    public function notificar_vacuna($IDClub, $Comentario)
    {
        $dbo = &SIMDB::get();

        $datos_vac = $dbo->fetchAll("ConfiguracionVacunacion", " IDClub = '" . $IDClub . "' ", "array");
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        $correo = $datos_vac["EmailNotificacion"];

        if (!empty($correo) && !empty($Comentario)):
            $msg = $Comentario;
            $mensaje = "
						<body>
							<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
								<tr>
									<td>
										<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
									</td>
								</tr>
								<tr>
									<td>" .
                $msg
                . "</td>
								</tr>
							</table>
						</body>
				";

            $url_baja = URLROOT . "contactenos.php";
            $mail = new phpmailer();
            $array_correo = explode(",", $correo);
            if (count($array_correo) > 0) {
                foreach ($array_correo as $correo_value) {
                    if (filter_var(trim($correo_value), FILTER_VALIDATE_EMAIL)) {
                        $mail->AddAddress($correo_value);
                        $enviar_a = $correo_value;
                    }
                }

                $mail->Subject = "Registro vacunacion ";
                $mail->Body = $mensaje;
                $mail->IsHTML(true);
                $mail->Sender = $datos_club["CorreoRemitente"];
                $mail->Timeout = 120;
                //$mail->IsSMTP();
                $mail->Port = PUERTO_SMTP;
                $mail->SMTPAuth = true;
                $mail->Host = HOST_SMTP;
                //$mail->Mailer = 'smtp';
                $mail->Password = PASSWORD_SMPT;
                $mail->Username = USER_SMTP;
                $mail->From = $datos_club["CorreoRemitente"];
                $mail->FromName = $datos_club["Nombre"];
                $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
                $confirm = $mail->Send();
            }
        endif;
    }

    public function noticar_seguimiento_pqr($id_pqr)
    {
        $dbo = &SIMDB::get();

        $datos_pqr = $dbo->fetchAll("Pqr", " IDPqr = '" . $id_pqr . "' ", "array");
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $datos_pqr["IDClub"] . "' ", "array");
        $correo = utf8_encode($dbo->getFields("Area", "CorreoResponsable", "IDArea = '" . $datos_pqr[IDArea] . "'"));

        if (!empty($correo)):
            $msg = "<br>Cordial Saludo,<br><br>
				Se ha generado una nueva fecha para realizar seguimiento al pqr.
				Recuerde ingresar al sistema para conocer mas detalles .<br><br>
				Numero: " . $datos_pqr["Numero"] . "<br>
				Socio: " . utf8_encode($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $datos_pqr[IDSocio] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $datos_pqr[IDSocio] . "'")) . "<br>
				Area: " . utf8_encode($dbo->getFields("Area", "Nombre", "IDArea = '" . $datos_pqr[IDArea] . "'")) . "<br>
				Tipo: " . utf8_encode($dbo->getFields("TipoPqr", "Nombre", "IDTipoPqr = '" . $datos_pqr[IDTipoPqr] . "'")) . "<br>
				Asunto: " . utf8_encode($datos_pqr[Asunto]) . "<br>
				Descripcion: " . utf8_encode($datos_pqr[Descripcion]) . "<br>
				Fecha: " . $datos_pqr[Fecha] . "<br>
				Fecha Seguimiento: " . $datos_pqr[FechaSeguimiento] . "<br><br>

				Por favor no responda este correo, si desea dar una respuesta ingrese al administrador<br>
				Cordialmente<br><br>
				<b>Notificaciones " . utf8_encode($datos_club["Nombre"]) . "</b>";

            $mensaje = "
						<body>
							<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
								<tr>
									<td>
										<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
									</td>
								</tr>
								<tr>
									<td>" .
                $msg
                . "</td>
								</tr>
							</table>
						</body>
				";

            $url_baja = URLROOT . "contactenos.php";
            $mail = new phpmailer();
            $array_correo = explode(",", $correo);
            if (count($array_correo) > 0) {
                foreach ($array_correo as $correo_value) {
                    if (filter_var(trim($correo_value), FILTER_VALIDATE_EMAIL)) {
                        $mail->AddAddress($correo_value);
                        $enviar_a = $correo_value;
                    }
                }

                $mail->Subject = "Nuevo PQR " . $datos_club["Nombre"] . " " . $datos_pqr["Numero"];
                $mail->Body = $mensaje;
                $mail->IsHTML(true);
                $mail->Sender = $datos_club["CorreoRemitente"];
                $mail->Timeout = 120;
                //$mail->IsSMTP();
                $mail->Port = PUERTO_SMTP;
                $mail->SMTPAuth = true;
                $mail->Host = HOST_SMTP;
                //$mail->Mailer = 'smtp';
                $mail->Password = PASSWORD_SMPT;
                $mail->Username = USER_SMTP;
                $mail->From = $datos_club["CorreoRemitente"];
                $mail->FromName = "Club";
                $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
                $confirm = $mail->Send();

                //Envio tambien el push con la notificacion del nuevo pqr
                $sql_usu_area = "SELECT * FROM Usuario U, UsuarioArea UA WHERE U.IDUsuario=UA.IDUsuario and U.IDClub='" . $datos_club["IDClub"] . "' and UA.IDArea = '" . $datos_pqr[IDArea] . "' Limit 3";
                $r_usu_area = $dbo->query($sql_usu_area);
                while ($row_usua_area = $dbo->fetchArray($r_usu_area)) {
                    $Mensaje = "Se programó una fecha de seguimiento del pqr :" . $datos_pqr["Numero"];
                    SIMUtil::enviar_notificacion_push_general_funcionario($datos_club["IDClub"], $row_usua_area["IDUsuario"], $Mensaje);
                }
                //self::sendMail( $enviar_a , "Nuevo PQR " .$datos_club["Nombre"]." " . $datos_pqr["Numero"] , $mensaje , $vars , $exclude , "noreplay@miclubapp.com" , $cabs );

            }

        endif;
    }

    public function noticar_cierre_pqr($id_pqr)
    {
        $dbo = &SIMDB::get();

        $datos_pqr = $dbo->fetchAll("Pqr", " IDPqr = '" . $id_pqr . "' ", "array");
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $datos_pqr["IDClub"] . "' ", "array");
        $correo = utf8_encode($dbo->getFields("Area", "CorreoResponsable", "IDArea = '" . $datos_pqr[IDArea] . "'"));

        if (!empty($correo)):

            $msg = "<br>Cordial Saludo,<br><br>
				Se ha cerrado el pqr.
				Recuerde ingresar al sistema para conocer mas detalles .<br><br>
				Numero: " . $datos_pqr["Numero"] . "<br>
				Socio: " . $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $datos_pqr[IDSocio] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $datos_pqr[IDSocio] . "'") . "<br>
				Area: " . $dbo->getFields("Area", "Nombre", "IDArea = '" . $datos_pqr[IDArea] . "'") . "<br>
				Tipo: " . $dbo->getFields("TipoPqr", "Nombre", "IDTipoPqr = '" . $datos_pqr[IDTipoPqr] . "'") . "<br>
				Asunto: " . $datos_pqr[Asunto] . "<br>
				Descripcion: " . $datos_pqr[Descripcion] . "<br>
				Fecha: " . $datos_pqr[Fecha] . "<br><br>

				Por favor no responda este correo, si desea dar una respuesta ingrese al administrador<br>
				Cordialmente<br><br>
				<b>Notificaciones " . $datos_club["Nombre"] . "</b>";

            $mensaje = "
						<body>
							<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
								<tr>
									<td>
										<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
									</td>
								</tr>
								<tr>
									<td>" .
                $msg
                . "</td>
								</tr>
							</table>
						</body>
				";

            $url_baja = URLROOT . "contactenos.php";
            $mail = new phpmailer();
            $array_correo = explode(",", $correo);
            if (count($array_correo) > 0) {
                foreach ($array_correo as $correo_value) {
                    if (filter_var(trim($correo_value), FILTER_VALIDATE_EMAIL)) {
                        $mail->AddAddress($correo_value);
                        $enviar_a = $correo_value;
                    }
                }

                $mail->Subject = "Cierre PQR " . $datos_club["Nombre"] . " " . $datos_pqr["Numero"];
                $mail->Body = $mensaje;
                $mail->IsHTML(true);
                $mail->Sender = $datos_club["CorreoRemitente"];
                $mail->Timeout = 120;
                //$mail->IsSMTP();
                $mail->Port = PUERTO_SMTP;
                $mail->SMTPAuth = true;
                $mail->Host = HOST_SMTP;
                //$mail->Mailer = 'smtp';
                $mail->Password = PASSWORD_SMPT;
                $mail->Username = USER_SMTP;
                $mail->From = $datos_club["CorreoRemitente"];
                $mail->FromName = "Club";
                $mail->CharSet = 'UTF-8';
                $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
                $confirm = $mail->Send();

            }

        endif;
    }

    public function noticar_nuevo_pqr_funcionario($id_pqr)
    {
        $dbo = &SIMDB::get();

        $datos_pqr = $dbo->fetchAll("PqrFuncionario", " IDPqr = '" . $id_pqr . "' ", "array");
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $datos_pqr["IDClub"] . "' ", "array");
        $correo = utf8_encode($dbo->getFields("AreaFuncionario", "CorreoResponsable", "IDArea = '" . $datos_pqr[IDArea] . "'"));

        if (!empty($correo)):
            switch ($datos_pqr[Tipo]) {
                case "P":
                    $tipo = "Peticion";
                    break;
                case "Q":
                    $tipo = "Queja";
                    break;
                case "R":
                    $tipo = "Reclamo";
                    break;
            }

            $msg = "<br>Cordial Saludo,<br><br>
				Se ha generado un nuevo pqr por parte de un funcionario.
				Recuerde ingresar al sistema para conocer mas detalles .<br><br>
				Numero: " . $datos_pqr["Numero"] . "<br>
				Funcionario: " . utf8_encode($dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $datos_pqr[IDUsuarioCreacion] . "'")) . "<br>
				Tipo: " . utf8_encode($dbo->getFields("TipoPqrFuncionario", "Nombre", "IDTipoPqr = '" . $datos_pqr[IDTipoPqr] . "'")) . "<br>
				Asunto: " . $datos_pqr[Asunto] . "<br>
				Descripcion: " . $datos_pqr[Descripcion] . "<br>
				Fecha: " . $datos_pqr[Fecha] . "<br><br>

				Por favor no responda este correo, si desea dar una respuesta ingrese al administrador<br>
				Cordialmente<br><br>
				<b>Notificaciones " . utf8_encode($datos_club["Nombre"]) . "</b>";

            $mensaje = "
						<body>
							<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
								<tr>
									<td>
										<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
									</td>
								</tr>
								<tr>
									<td>" .
                $msg
                . "</td>
								</tr>
							</table>
						</body>
				";

            $url_baja = URLROOT . "contactenos.php";
            $mail = new phpmailer();
            $array_correo = explode(",", $correo);
            if (count($array_correo) > 0) {
                foreach ($array_correo as $correo_value) {
                    if (filter_var(trim($correo_value), FILTER_VALIDATE_EMAIL)) {
                        $mail->AddAddress($correo_value);
                        $enviar_a = $correo_value;
                    }
                }

                $mail->Subject = "Nuevo PQR Funcionarios " . $datos_club["Nombre"] . " " . $datos_pqr["Numero"];
                $mail->Body = $mensaje;
                $mail->IsHTML(true);
                $mail->Sender = $datos_club["CorreoRemitente"];
                $mail->Timeout = 120;
                //$mail->IsSMTP();
                $mail->Port = PUERTO_SMTP;
                $mail->SMTPAuth = true;
                $mail->Host = HOST_SMTP;
                //$mail->Mailer = 'smtp';
                $mail->Password = PASSWORD_SMPT;
                $mail->Username = USER_SMTP;
                $mail->From = $datos_club["CorreoRemitente"];
                $mail->FromName = "Club";
                $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
                $confirm = $mail->Send();

                //self::sendMail( $enviar_a , "Nuevo PQR " .$datos_club["Nombre"]." " . $datos_pqr["Numero"] , $mensaje , $vars , $exclude , "noreplay@miclubapp.com" , $cabs );

            }
        endif;
    }

    public function noticar_cierre_pqr_func($id_pqr)
    {
        $dbo = &SIMDB::get();

        $datos_pqr = $dbo->fetchAll("PqrFuncionario", " IDPqr = '" . $id_pqr . "' ", "array");
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $datos_pqr["IDClub"] . "' ", "array");
        $correo = utf8_encode($dbo->getFields("AreaFuncionario", "CorreoResponsable", "IDArea = '" . $datos_pqr[IDArea] . "'"));

        if (!empty($correo)):
            switch ($datos_pqr[Tipo]) {
                case "P":
                    $tipo = "Peticion";
                    break;
                case "Q":
                    $tipo = "Queja";
                    break;
                case "R":
                    $tipo = "Reclamo";
                    break;
            }

            $msg = "<br>Cordial Saludo,<br><br>
				Se ha cerrado el pqr.
				Recuerde ingresar al sistema para conocer mas detalles .<br><br>
				Numero: " . $datos_pqr["Numero"] . "<br>
				Funcionario: " . utf8_encode($dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $datos_pqr[IDUsuarioCreacion] . "'")) . "<br>
				Tipo: " . utf8_encode($dbo->getFields("TipoPqrFuncionario", "Nombre", "IDTipoPqr = '" . $datos_pqr[IDTipoPqr] . "'")) . "<br>
				Asunto: " . $datos_pqr[Asunto] . "<br>
				Descripcion: " . $datos_pqr[Descripcion] . "<br>
				Fecha: " . $datos_pqr[Fecha] . "<br><br>

				Por favor no responda este correo, si desea dar una respuesta ingrese al administrador<br>
				Cordialmente<br><br>
				<b>Notificaciones " . utf8_encode($datos_club["Nombre"]) . "</b>";

            $mensaje = "
						<body>
							<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
								<tr>
									<td>
										<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
									</td>
								</tr>
								<tr>
									<td>" .
                $msg
                . "</td>
								</tr>
							</table>
						</body>
				";

            $url_baja = URLROOT . "contactenos.php";
            $mail = new phpmailer();
            $array_correo = explode(",", $correo);
            if (count($array_correo) > 0) {
                foreach ($array_correo as $correo_value) {
                    if (filter_var(trim($correo_value), FILTER_VALIDATE_EMAIL)) {
                        $mail->AddAddress($correo_value);
                        $enviar_a = $correo_value;
                    }
                }

                $mail->Subject = "Cierre PQR Funcionarios " . $datos_club["Nombre"] . " " . $datos_pqr["Numero"];
                $mail->Body = $mensaje;
                $mail->IsHTML(true);
                $mail->Sender = $datos_club["CorreoRemitente"];
                $mail->Timeout = 120;
                //$mail->IsSMTP();
                $mail->Port = PUERTO_SMTP;
                $mail->SMTPAuth = true;
                $mail->Host = HOST_SMTP;
                //$mail->Mailer = 'smtp';
                $mail->Password = PASSWORD_SMPT;
                $mail->Username = USER_SMTP;
                $mail->From = $datos_club["CorreoRemitente"];
                $mail->FromName = "Club";
                $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
                $confirm = $mail->Send();

                //self::sendMail( $enviar_a , "Nuevo PQR " .$datos_club["Nombre"]." " . $datos_pqr["Numero"] , $mensaje , $vars , $exclude , "noreplay@miclubapp.com" , $cabs );

            }
        endif;
    }

    public function noticar_respuesta_pqr($id_pqr, $Comentario)
    {
        $dbo = &SIMDB::get();

        $datos_pqr = $dbo->fetchAll("Pqr", " IDPqr = '" . $id_pqr . "' ", "array");
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $datos_pqr["IDClub"] . "' ", "array");
        $correo = utf8_encode($dbo->getFields("Area", "CorreoResponsable", "IDArea = '" . $datos_pqr[IDArea] . "'"));
        if (!empty($correo)) {

            switch ($datos_pqr[Tipo]) {
                case "P":
                    $tipo = "Peticion";
                    break;
                case "Q":
                    $tipo = "Queja";
                    break;
                case "R":
                    $tipo = "Reclamo";
                    break;
            }

            $msg = "<br>Cordial Saludo,<br><br>
		Se ha generado un nueva respuesta a un pqr por parte de un socio.
		Recuerde ingresar al sistema para conocer mas detalles .<br><br>
		Numero: " . $datos_pqr["Numero"] . "<br>
		Socio: " . utf8_encode($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $datos_pqr[IDSocio] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $datos_pqr[IDSocio] . "'")) . "<br>
		Tipo: " . utf8_encode($dbo->getFields("TipoPqr", "Nombre", "IDTipoPqr = '" . $datos_pqr[IDTipoPqr] . "'")) . "<br>
		Asunto: " . utf8_encode($datos_pqr[Asunto]) . "<br>
		Descripcion: " . utf8_encode($datos_pqr[Descripcion]) . "<br>
		Fecha: " . $datos_pqr[Fecha] . "<br>
		Comentario: " . $Comentario . "<br><br>

		Por favor no responda este correo, si desea dar una respuesta ingrese al administrador<br>
		Cordialmente<br><br>
		<b>Notificaciones " . utf8_encode($datos_club["Nombre"]) . "</b>";

            $mensaje = "
				<body>
					<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
						<tr>
							<td>
								<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
							</td>
						</tr>
						<tr>
							<td>" .
                $msg
                . "</td>
						</tr>
					</table>
				</body>
		";

            $url_baja = URLROOT . "contactenos.php";
            $mail = new phpmailer();
            $array_correo = explode(",", $correo);
            if (count($array_correo) > 0) {
                foreach ($array_correo as $correo_value) {
                    if (filter_var(trim($correo_value), FILTER_VALIDATE_EMAIL)) {
                        $mail->AddAddress($correo_value);
                        $enviar_a = $correo_value;
                    }
                }

                $mail->Subject = "Respuesta PQR " . $datos_club["Nombre"] . " " . $datos_pqr["Numero"];
                $mail->Body = $mensaje;
                $mail->IsHTML(true);
                $mail->Sender = $datos_club["CorreoRemitente"];
                $mail->Timeout = 120;
                //$mail->IsSMTP();
                $mail->Port = PUERTO_SMTP;
                $mail->SMTPAuth = true;
                $mail->Host = HOST_SMTP;
                //$mail->Mailer = 'smtp';
                $mail->Password = PASSWORD_SMPT;
                $mail->Username = USER_SMTP;
                $mail->From = $datos_club["CorreoRemitente"];
                $mail->FromName = "Club";
                $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
                $confirm = $mail->Send();
            }

            //self::sendMail( $enviar_a , "Nuevo PQR " .$datos_club["Nombre"]." " . $datos_pqr["Numero"] , $mensaje , $vars , $exclude , "noreplay@miclubapp.com" , $cabs );

        }
    }

    public function noticar_calificacion_pqr($id_pqr, $Calificacion)
    {
        $dbo = &SIMDB::get();

        $datos_pqr = $dbo->fetchAll("Pqr", " IDPqr = '" . $id_pqr . "' ", "array");
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $datos_pqr["IDClub"] . "' ", "array");
        $correo = utf8_encode($dbo->getFields("Area", "CorreoResponsable", "IDArea = '" . $datos_pqr[IDArea] . "'"));
        if (!empty($correo)) {

            switch ($datos_pqr[Tipo]) {
                case "P":
                    $tipo = "Peticion";
                    break;
                case "Q":
                    $tipo = "Queja";
                    break;
                case "R":
                    $tipo = "Reclamo";
                    break;
            }

            $msg = "<br>Cordial Saludo,<br><br>
		Se ha calificado un pqr por parte de un socio.
		Recuerde ingresar al sistema para conocer mas detalles .<br><br>
		Numero: " . $datos_pqr["Numero"] . "<br>
		Socio: " . utf8_encode($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $datos_pqr[IDSocio] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $datos_pqr[IDSocio] . "'")) . "<br>
		Tipo: " . utf8_encode($dbo->getFields("TipoPqr", "Nombre", "IDTipoPqr = '" . $datos_pqr[IDTipoPqr] . "'")) . "<br>
		Asunto: " . utf8_encode($datos_pqr[Asunto]) . "<br>
		Descripcion: " . utf8_encode($datos_pqr[Descripcion]) . "<br>
		Fecha: " . $datos_pqr[Fecha] . "<br>
		Calidficacion: " . $Calificacion . "<br><br>

		Por favor no responda este correo, si desea dar una respuesta ingrese al administrador<br>
		Cordialmente<br><br>
		<b>Notificaciones " . utf8_encode($datos_club["Nombre"]) . "</b>";

            $mensaje = "
				<body>
					<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
						<tr>
							<td>
								<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
							</td>
						</tr>
						<tr>
							<td>" .
                $msg
                . "</td>
						</tr>
					</table>
				</body>
		";

            $url_baja = URLROOT . "contactenos.php";
            $mail = new phpmailer();
            $array_correo = explode(",", $correo);
            if (count($array_correo) > 0) {
                foreach ($array_correo as $correo_value) {
                    if (filter_var(trim($correo_value), FILTER_VALIDATE_EMAIL)) {
                        $mail->AddAddress($correo_value);
                        $enviar_a = $correo_value;
                    }
                }

                $mail->Subject = "Calificacion PQR " . $datos_club["Nombre"] . " " . $datos_pqr["Numero"];
                $mail->Body = $mensaje;
                $mail->IsHTML(true);
                $mail->Sender = $datos_club["CorreoRemitente"];
                $mail->Timeout = 120;
                //$mail->IsSMTP();
                $mail->Port = PUERTO_SMTP;
                $mail->SMTPAuth = true;
                $mail->Host = HOST_SMTP;
                //$mail->Mailer = 'smtp';
                $mail->Password = PASSWORD_SMPT;
                $mail->Username = USER_SMTP;
                $mail->From = $datos_club["CorreoRemitente"];
                $mail->FromName = "Club";
                $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
                $confirm = $mail->Send();
            }

            //self::sendMail( $enviar_a , "Nuevo PQR " .$datos_club["Nombre"]." " . $datos_pqr["Numero"] , $mensaje , $vars , $exclude , "noreplay@miclubapp.com" , $cabs );

        }
    }

    public function noticar_respuesta_pqr_funcionario($id_pqr, $Comentario)
    {
        $dbo = &SIMDB::get();

        $datos_pqr = $dbo->fetchAll("PqrFuncionario", " IDPqr = '" . $id_pqr . "' ", "array");
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $datos_pqr["IDClub"] . "' ", "array");
        $correo = utf8_encode($dbo->getFields("AreaFuncionario", "CorreoResponsable", "IDArea = '" . $datos_pqr[IDArea] . "'"));

        switch ($datos_pqr[Tipo]) {
            case "P":
                $tipo = "Peticion";
                break;
            case "Q":
                $tipo = "Queja";
                break;
            case "R":
                $tipo = "Reclamo";
                break;
        }

        $msg = "<br>Cordial Saludo,<br><br>
		Se ha generado un nueva respuesta a un pqr por parte de un funcionario.
		Recuerde ingresar al sistema para conocer mas detalles .<br><br>
		Numero: " . $datos_pqr["Numero"] . "<br>
		Funcionario: " . utf8_encode($dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $datos_pqr[IDUsuarioCreacion] . "'")) . "<br>
		Tipo: " . utf8_encode($dbo->getFields("TipoPqrFuncionario", "Nombre", "IDTipoPqr = '" . $datos_pqr[IDTipoPqr] . "'")) . "<br>
		Asunto: " . utf8_encode($datos_pqr[Asunto]) . "<br>
		Descripcion: " . utf8_encode($datos_pqr[Descripcion]) . "<br>
		Fecha: " . $datos_pqr[Fecha] . "<br>
		Comentario: " . $Comentario . "<br><br>

		Por favor no responda este correo, si desea dar una respuesta ingrese al administrador<br>
		Cordialmente<br><br>
		<b>Notificaciones " . utf8_encode($datos_club["Nombre"]) . "</b>";

        $mensaje = "
				<body>
					<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
						<tr>
							<td>
								<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
							</td>
						</tr>
						<tr>
							<td>" .
            $msg
            . "</td>
						</tr>
					</table>
				</body>
		";

        $url_baja = URLROOT . "contactenos.php";
        $mail = new phpmailer();
        $array_correo = explode(",", $correo);
        if (count($array_correo) > 0) {
            foreach ($array_correo as $correo_value) {
                if (filter_var(trim($correo_value), FILTER_VALIDATE_EMAIL)) {
                    $mail->AddAddress($correo_value);
                    $enviar_a = $correo_value;
                }
            }

            $mail->Subject = "Respuesta PQR Funcionario" . $datos_club["Nombre"] . " " . $datos_pqr["Numero"];
            $mail->Body = $mensaje;
            $mail->IsHTML(true);
            $mail->Sender = $datos_club["CorreoRemitente"];
            $mail->Timeout = 120;
            //$mail->IsSMTP();
            $mail->Port = PUERTO_SMTP;
            $mail->SMTPAuth = true;
            $mail->Host = HOST_SMTP;
            //$mail->Mailer = 'smtp';
            $mail->Password = PASSWORD_SMPT;
            $mail->Username = USER_SMTP;
            $mail->From = $datos_club["CorreoRemitente"];
            $mail->FromName = "Club";
            $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
            $confirm = $mail->Send();

            //self::sendMail( $enviar_a , "Nuevo PQR " .$datos_club["Nombre"]." " . $datos_pqr["Numero"] , $mensaje , $vars , $exclude , "noreplay@miclubapp.com" , $cabs );

        }
    }

    public function envio_respuesta_registro($IDSocio, $IDEstado, $Usuario, $Clave)
    {
        $dbo = &SIMDB::get();

        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $datos_socio["IDClub"] . "' ", "array");
        $correo = $datos_socio["CorreoElectronico"];

        switch ($IDEstado) {
            case "1":
                $estado = "Aprobado. <br><br>Puede ingresar al app Bijao con los siguientes datos <br>Usuario: " . $Usuario . " <br>Clave: " . $Clave . "
				 <br><br>El APLICATIVO MOVIL BIJAO BEACH CLUB & RESIDENCES, puede descargarse a través de las siguientes tiendas de aplicaciones: <br><br>
				 PLAY STORE:<br>
https://play.google.com/store/apps/details?id=clubs.zerotwo.com.bijao

<br><br>APP STORE:<br>
https://itunes.apple.com/app/bijao-beach-club/id1325705162?ls=1&mt=8 <br><br>
				 ";
                break;
            case "2":
                $estado = "Inactivo";
                break;
            case "3":
                $estado = "No Aprobado, por favor comuniquese con el club para mas detalles";
                break;
        }

        $msg = "<br>Cordial Saludo,<br><br>
		Le informamos que la solicitud de registro del beneficiario " . $datos_socio["Nombre"] . " " . $datos_socio["Apellido"] . " realizada por usted fue: " . $estado . "<br><br>
		Por favor no responda este correo, si desea dar una respuesta ingrese al administrador<br>
		Cordialmente<br><br>
		<b>Notificaciones " . utf8_encode($datos_club["Nombre"]) . "</b>";

        $mensaje = "
				<body>
					<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
						<tr>
							<td>
								<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
							</td>
						</tr>
						<tr>
							<td>" .
            $msg
            . "</td>
						</tr>
					</table>
				</body>
		";

        $url_baja = URLROOT . "contactenos.php";
        $mail = new phpmailer();
        $array_correo = explode(",", $correo);
        if (count($array_correo) > 0) {
            foreach ($array_correo as $correo_value) {
                if (filter_var(trim($correo_value), FILTER_VALIDATE_EMAIL)) {
                    $mail->AddAddress($correo_value);
                    $enviar_a = $correo_value;
                }
            }

            $mail->Subject = "Solicitud agregar beneficiario";
            $mail->Body = $mensaje;
            $mail->IsHTML(true);
            $mail->Sender = $datos_club["CorreoRemitente"];
            $mail->Timeout = 120;
            //$mail->IsSMTP();
            $mail->Port = PUERTO_SMTP;
            $mail->SMTPAuth = true;
            $mail->Host = HOST_SMTP;
            //$mail->Mailer = 'smtp';
            $mail->Password = PASSWORD_SMPT;
            $mail->Username = USER_SMTP;
            $mail->From = $datos_club["CorreoRemitente"];
            $mail->FromName = "Club";
            $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
            $confirm = $mail->Send();

            //self::sendMail( $enviar_a , "Nuevo PQR " .$datos_club["Nombre"]." " . $datos_pqr["Numero"] , $mensaje , $vars , $exclude , "noreplay@miclubapp.com" , $cabs );

        }
    }

    public function notificar_nuevo_clasificado($id_clasificado)
    {
        $dbo = &SIMDB::get();

        $datos_clasificado = $dbo->fetchAll("Clasificado", " IDClasificado = '" . $id_clasificado . "' ", "array");
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $datos_clasificado["IDClub"] . "' ", "array");

        $correo = $datos_club["EmailNotificaciones"];
        //$correo="jorgechirivi@gmail.com";
        if (!empty($correo)):

            $msg = "<br>Cordial Saludo,<br><br>
					Se ha creado un nuevo clasificado por parte de un socio.
					Recuerde ingresar al sistema para conocer mas detalles para activarlo o recharlo .<br><br>
					Numero: " . $datos_clasificado["IDClasificado"] . "<br>
					Socio: " . $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $datos_clasificado[IDSocio] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $datos_clasificado[IDSocio] . "'") . "<br>
					Producto: " . $datos_clasificado["Nombre"] . "<br>
					Descripcion: " . $datos_clasificado["Descripcion"] . "<br>
					Categoria: " . $dbo->getFields("SeccionClasificados", "Nombre", "IDSeccionClasificados = '" . $datos_clasificado["IDSeccionClasificados"] . "'") . "<br>
					Valor: " . $datos_clasificado["Valor"] . "<br>
					Fecha: " . $datos_clasificado["FechaTrCr"] . "<br><br>

					Por favor no responda este correo<br>
					Cordialmente<br><br>
					<b>Notificaciones " . utf8_encode($datos_club["Nombre"]) . "</b>";

            $mensaje = "
							<body>
								<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
									<tr>
										<td>
											<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
										</td>
									</tr>
									<tr>
										<td>" .
                $msg
                . "</td>
									</tr>
								</table>
							</body>
					";

            $url_baja = URLROOT . "contactenos.php";
            $mail = new phpmailer();
            $array_correo = explode(",", $correo);
            if (count($array_correo) > 0) {
                foreach ($array_correo as $correo_value) {
                    if (filter_var(trim($correo_value), FILTER_VALIDATE_EMAIL)) {
                        $mail->AddAddress($correo_value);
                        $enviar_a = $correo_value;
                    }
                }

                $mail->Subject = "Nuevo Clasificado " . $datos_club["Nombre"] . " " . $datos_clasificado["IDClasificado"];
                $mail->Body = $mensaje;
                $mail->IsHTML(true);
                $mail->Sender = $datos_club["CorreoRemitente"];
                $mail->Timeout = 120;
                //$mail->IsSMTP();
                $mail->Port = PUERTO_SMTP;
                $mail->SMTPAuth = true;
                $mail->Host = HOST_SMTP;
                //$mail->Mailer = 'smtp';
                $mail->Password = PASSWORD_SMPT;
                $mail->Username = USER_SMTP;
                $mail->From = $datos_club["CorreoRemitente"];
                $mail->FromName = "Club";
                $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
                $confirm = $mail->Send();

            }
        endif;
    }

    public function notificar_solicitud_objeto_perdido($IDObjetoPerdido, $IDSocio)
    {
        $dbo = &SIMDB::get();

        $datos_objeto = $dbo->fetchAll("ObjetoPerdido", " IDObjetoPerdido = '" . $IDObjetoPerdido . "' ", "array");
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $datos_objeto["IDClub"] . "' ", "array");
        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");

        $correo = $datos_club["EmailObjetosPerdidos"];
        if (!empty($correo)):

            $msg = "<br>Cordial Saludo,<br><br>
					El siguiente socio ha enviado una solictud para reclamar el siguiente objeto perdido:
					Recuerde ingresar al sistema para conocer mas detalles.<br><br>
					Socio: " . utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"]) . "<br>
					Objeto Perdido: " . utf8_encode($datos_objeto["Nombre"]) . "<br>
					Descripcion: " . utf8_encode($datos_objeto["Descripcion"]) . "<br>
					Categoria: " . utf8_encode($dbo->getFields("SeccionObjetosPerdidos", "Nombre", "IDSeccionObjetosPerdidos = '" . $datos_objeto["IDSeccionObjetosPerdidos"] . "'")) . "<br>

					Por favor no responda este correo<br>
					Cordialmente<br><br>
					<b>Notificaciones " . utf8_encode($datos_club["Nombre"]) . "</b>";

            $mensaje = "
							<body>
								<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
									<tr>
										<td>
											<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
										</td>
									</tr>
									<tr>
										<td>" .
                $msg
                . "</td>
									</tr>
								</table>
							</body>
					";

            $url_baja = URLROOT . "contactenos.php";
            $mail = new phpmailer();
            $array_correo = explode(",", $correo);
            if (count($array_correo) > 0) {
                foreach ($array_correo as $correo_value) {
                    if (filter_var(trim($correo_value), FILTER_VALIDATE_EMAIL)) {
                        $mail->AddAddress($correo_value);
                        $enviar_a = $correo_value;
                    }
                }

                $mail->Subject = "Reclamar objeto perdido ";
                $mail->Body = $mensaje;
                $mail->IsHTML(true);
                $mail->Sender = $datos_club["CorreoRemitente"];
                $mail->Timeout = 120;
                //$mail->IsSMTP();
                $mail->Port = PUERTO_SMTP;
                $mail->SMTPAuth = true;
                $mail->Host = HOST_SMTP;
                //$mail->Mailer = 'smtp';
                $mail->Password = PASSWORD_SMPT;
                $mail->Username = USER_SMTP;
                $mail->From = $datos_club["CorreoRemitente"];
                $mail->FromName = "Club";
                $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
                $confirm = $mail->Send();

            }
        endif;
    }

    public function enviar_oferta($IDOferta, $IDCandidato)
    {
        $dbo = &SIMDB::get();

        $datos_oferta = $dbo->fetchAll("Oferta", " IDOferta = '" . $IDOferta . "' ", "array");
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $datos_oferta["IDClub"] . "' ", "array");
        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_oferta["IDSocio"] . "' ", "array");
        $datos_candidato = $dbo->fetchAll("OfertaCandidato", " IDOfertaCandidato = '" . $IDCandidato . "' ", "array");
        $datos_socio_candidato = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_candidato["IDSocio"] . "' ", "array");

        $correo = $datos_socio["CorreoElectronico"];
        if (!empty($correo)):

            $msg = "<br>Cordial Saludo,<br><br>
					Se ha enviado los siguientes datos a su oferta laboral publicada:
					<br><br>
					Oferta: " . $datos_oferta["DescripcionCargo"] . "<br>
					Socio: " . $datos_socio_candidato["Nombre"] . " " . $datos_socio_candidato["Apellido"] . "<br>
					Nombre Recomendado: " . $datos_candidato["NombreRecomendado"] . "<br>
					Telefono: " . $datos_candidato["Telefono"] . "<br>
					Correo Electronico: " . $datos_candidato["CorreoElectronico"] . "<br>
					Archivo: " . OFERTA_ROOT . $datos_candidato["Archivo"] . "<br>

					Por favor no responda este correo<br>
					Cordialmente<br><br>
					<b>Notificaciones " . utf8_encode($datos_club["Nombre"]) . "</b>";

            $mensaje = "
							<body>
								<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
									<tr>
										<td>
											<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
										</td>
									</tr>
									<tr>
										<td>" .
                $msg
                . "</td>
									</tr>
								</table>
							</body>
					";

            $url_baja = URLROOT . "contactenos.php";
            $mail = new phpmailer();
            $array_correo = explode(",", $correo);
            if (count($array_correo) > 0) {
                foreach ($array_correo as $correo_value) {
                    if (filter_var(trim($correo_value), FILTER_VALIDATE_EMAIL)) {
                        $mail->AddAddress($correo_value);
                        $enviar_a = $correo_value;
                    }
                }

                $mail->Subject = "Oferta Laboral - Candidato ";
                $mail->Body = $mensaje;
                $mail->IsHTML(true);
                $mail->Sender = $datos_club["CorreoRemitente"];
                $mail->Timeout = 120;
                //$mail->IsSMTP();
                $mail->Port = PUERTO_SMTP;
                $mail->SMTPAuth = true;
                $mail->Host = HOST_SMTP;
                //$mail->Mailer = 'smtp';
                $mail->Password = PASSWORD_SMPT;
                $mail->Username = USER_SMTP;
                $mail->From = $datos_club["CorreoRemitente"];
                $mail->FromName = "Club";
                $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
                $confirm = $mail->Send();

            }
        endif;
    }

    public function notificar_solicitud_canje($id_solicitud)
    {
        $dbo = &SIMDB::get();

        $datos_solicitud = $dbo->fetchAll("CanjeSolicitud", " IDCanjeSolicitud = '" . $id_solicitud . "' ", "array");
        $config_canje = $dbo->fetchAll("ClubCanje", " IDClub = '" . $datos_solicitud["IDClub"] . "' ", "array");
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $datos_solicitud["IDClub"] . "' ", "array");
        $sql_detalle_club = "Select * From DetalleClubCanje Where IDClub = '" . $datos_solicitud["IDClub"] . "' and IDListaClubes = '" . $datos_solicitud["IDListaClubes"] . "'";
        $result_detalle_club = $dbo->query($sql_detalle_club);
        $row_detalle_club = $dbo->fetchArray($result_detalle_club);
        $correo = utf8_encode($dbo->getFields("ClubCanje", "CorreoNotificacion", "IDClubCanje = '" . $row_detalle_club["IDClubCanje"] . "'"));

        if (!empty($correo)) {
            $msg = $dbo->getFields("ClubCanje", "MensajeClubDestino", "IDClubCanje = '" . $row_detalle_club["IDClubCanje"] . "'");
            /* $msg = $config_canje["MensajeClubDestino"]; */
            $msg = str_replace("[NombreClub]", utf8_encode($dbo->getFields("ListaClubes", "Nombre", "IDListaClubes = '" . $datos_solicitud["IDListaClubes"] . "'")), $msg);
            $msg = str_replace("[NombreSocio]", utf8_encode($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $datos_solicitud[IDSocio] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $datos_solicitud[IDSocio] . "'")), $msg);
            if ($datos_solicitud["IDClub"] == 11) {
                $msg = str_replace("[AccionSocio]", utf8_encode($dbo->getFields("Socio", "Accion", "IDSocio = '" . $datos_solicitud[IDSocio] . "'")), $msg);
            }
            $msg = str_replace("[DiasCanje]", $datos_solicitud["CantidadDias"], $msg);
            $msg = str_replace("[FechaCanje]", $datos_solicitud["FechaInicio"], $msg);
            $msg = str_replace("[Accion]", utf8_encode($dbo->getFields("Socio", "Accion", "IDSocio = '" . $datos_solicitud[IDSocio] . "'")), $msg);
            $msg = str_replace("[NumeroCanje]", $datos_solicitud["Numero"], $msg);

            $datos_socio_benef = "";
            $array_benef = explode("|", $datos_solicitud["IDSocioBeneficiario"]);
            if (count($array_benef) > 0) {
                foreach ($array_benef as $IDSocioBenef) {
                    $datos_benef = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocioBenef . "' ", "array");
                    $datos_socio_benef .= utf8_encode($datos_benef["Nombre"] . " " . $datos_benef["Apellido"]) . "<br>";
                }
            }

            $msg = str_replace("[GrupoSocio]", $datos_socio_benef, $msg);

            if ($datos_club["CanjesAutomaticos"] == "S") {
                $msg .= "<br> Se tienen activos los canjes automaticos.<br>El socio fue creado en la base de datos y queda pendiente de revisi&oacute;n para ser activado.";
            }

            $mensaje = "
						<body>
							<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
								<tr>
									<td>
										<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
									</td>
								</tr>
								<tr>
									<td>" .
                $msg
                . "</td>
								</tr>
							</table>
						</body>
				";

            $url_baja = URLROOT . "contactenos.php";
            $mail = new phpmailer();
            $array_correo = explode(",", $correo);
            if (count($array_correo) > 0) {
                foreach ($array_correo as $correo_value) {
                    if (filter_var(trim($correo_value), FILTER_VALIDATE_EMAIL)) {
                        $mail->AddAddress($correo_value);
                    }
                }

                $mail->Subject = "Solicitud Canje " . $datos_solicitud["Numero"];
                $mail->Body = $mensaje;
                $mail->IsHTML(true);
                $mail->Sender = $datos_club["CorreoRemitente"];
                $mail->Timeout = 120;
                //$mail->IsSMTP();
                $mail->Port = PUERTO_SMTP;
                $mail->SMTPAuth = true;
                $mail->Host = HOST_SMTP;
                //$mail->Mailer = 'smtp';
                $mail->Password = PASSWORD_SMPT;
                $mail->Username = USER_SMTP;
                $mail->From = $datos_club["CorreoRemitente"];
                $mail->FromName = "Club";
                $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
                $confirm = $mail->Send();

                self::notificar_solicitud_canje_destino($id_solicitud, $mensaje);

                //self::sendMail( $enviar_a , "Nuevo PQR " .$datos_club["Nombre"]." " . $datos_pqr["Numero"] , $mensaje , $vars , $exclude , "noreplay@miclubapp.com" , $cabs );

            }
        }
    }

    public function notificar_solicitud_canje_destino($id_solicitud, $mensaje)
    {
        $dbo = &SIMDB::get();

        $datos_solicitud = $dbo->fetchAll("CanjeSolicitud", " IDCanjeSolicitud = '" . $id_solicitud . "' ", "array");
        $config_canje = $dbo->fetchAll("ClubCanje", " IDClub = '" . $datos_solicitud["IDClub"] . "' ", "array");
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $datos_solicitud["IDClub"] . "' ", "array");
        $sql_detalle_club = "Select * From DetalleClubCanje Where IDClub = '" . $datos_solicitud["IDClub"] . "' and IDListaClubes = '" . $datos_solicitud["IDListaClubes"] . "'";
        $result_detalle_club = $dbo->query($sql_detalle_club);
        $row_detalle_club = $dbo->fetchArray($result_detalle_club);
        $correo = $row_detalle_club["CorreoNotificacion"];

        if (!empty($correo)) {

            /*
            $msg = $config_canje["MensajeClubDestino"];
            $msg = str_replace("[NombreClub]",utf8_encode($dbo->getFields( "ListaClubes" , "Nombre" , "IDListaClubes = '" .$datos_solicitud["IDListaClubes"] . "'" )),$msg);
            $msg = str_replace("[NombreSocio]",utf8_encode($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '" .$datos_solicitud[IDSocio] . "'" ). " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '" .$datos_solicitud[IDSocio] . "'" )),$msg);
            $msg = str_replace("[DiasCanje]",$datos_solicitud["CantidadDias"],$msg);
            $msg = str_replace("[FechaCanje]",$datos_solicitud["FechaInicio"],$msg);
            $msg = str_replace("[Accion]",utf8_encode($dbo->getFields( "Socio" , "Accion" , "IDSocio = '" .$datos_solicitud[IDSocio]."'")),$msg);

            $mensaje="
            <body>
            <table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
            <tr>
            <td>
            <img src='".CLUB_ROOT.$datos_club[FotoLogoApp]."'>
            </td>
            </tr>
            <tr>
            <td>".
            $msg
            ."</td>
            </tr>
            </table>
            </body>
            ";

             */

            $url_baja = URLROOT . "contactenos.php";
            $mail = new phpmailer();
            $array_correo = explode(",", $correo);
            if (count($array_correo) > 0) {
                foreach ($array_correo as $correo_value) {
                    if (filter_var(trim($correo_value), FILTER_VALIDATE_EMAIL)) {
                        $mail->AddAddress($correo_value);
                        $enviar_a = $correo_value;
                    }
                }

                $mail->Subject = "Solicitud Canje " . $datos_solicitud["Numero"];
                $mail->Body = $mensaje;
                $mail->IsHTML(true);
                $mail->Sender = $datos_club["CorreoRemitente"];
                $mail->Timeout = 120;
                //$mail->IsSMTP();
                $mail->Port = PUERTO_SMTP;
                $mail->SMTPAuth = true;
                $mail->Host = HOST_SMTP;
                //$mail->Mailer = 'smtp';
                $mail->Password = PASSWORD_SMPT;
                $mail->Username = USER_SMTP;
                $mail->From = $datos_club["CorreoRemitente"];
                $mail->FromName = "Club";
                $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
                $confirm = $mail->Send();

                //self::sendMail( $enviar_a , "Nuevo PQR " .$datos_club["Nombre"]." " . $datos_pqr["Numero"] , $mensaje , $vars , $exclude , "noreplay@miclubapp.com" , $cabs );

            }
        }
    }

    public function notificar_contactenos($IDClub, $IDSocio, $Telefono, $Ciudad, $Direccion, $Email, $Comentario, $Nombre)
    {
        $dbo = &SIMDB::get();

        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        $correo = $datos_club["EmailNotificacionesContacto"];

        $msg = "<br>Cordial Saludo,<br><br>
		Se ha generado un nuevo contacto.
		Nombre: " . $Nombre . "<br>
		Telefono: " . $Telefono . "<br>
		Ciudad: " . $Ciudad . "<br>
		Direccion: " . $Direccion . "<br>
		Email: " . $Email . "<br>
		Comentario: " . $Comentario . "<br>

		Por favor no responda este correo, si desea dar una respuesta ingrese al administrador<br>
		Cordialmente<br><br>
		<b>Notificaciones " . utf8_encode($datos_club["Nombre"]) . "</b>";

        $mensaje = "
				<body>
					<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
						<tr>
							<td>
								<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
							</td>
						</tr>
						<tr>
							<td>" .
            $msg
            . "</td>
						</tr>
					</table>
				</body>
		";

        $url_baja = URLROOT . "contactenos.php";
        $mail = new phpmailer();
        $array_correo = explode(",", $correo);
        if (count($array_correo) > 0) {
            foreach ($array_correo as $correo_value) {
                if (filter_var(trim($correo_value), FILTER_VALIDATE_EMAIL)) {
                    $mail->AddAddress($correo_value);
                    $enviar_a = $correo_value;
                }
            }

            $mail->Subject = "Contactenos ";
            $mail->Body = $mensaje;
            $mail->IsHTML(true);
            $mail->Sender = $datos_club["CorreoRemitente"];
            $mail->Timeout = 120;
            //$mail->IsSMTP();
            $mail->Port = PUERTO_SMTP;
            $mail->SMTPAuth = true;
            $mail->Host = HOST_SMTP;
            //$mail->Mailer = 'smtp';
            $mail->Password = PASSWORD_SMPT;
            $mail->Username = USER_SMTP;
            $mail->From = $datos_club["CorreoRemitente"];
            $mail->FromName = "Club";
            $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
            $confirm = $mail->Send();

            //self::sendMail( $enviar_a , "Nuevo PQR " .$datos_club["Nombre"]." " . $datos_pqr["Numero"] , $mensaje , $vars , $exclude , "noreplay@miclubapp.com" , $cabs );

        }
    }

    public function notificar_nueva_reserva($id_reserva, $IDTipoReserva = "", $solo_socio = "", $recordacion = "")
    {
        $dbo = &SIMDB::get();

        if (!empty($id_reserva)):

            $datos_reserva = $dbo->fetchAll("ReservaGeneral", " IDReservaGeneral = '" . $id_reserva . "' ", "array");
            $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $datos_reserva["IDClub"] . "' ", "array");
            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_reserva["IDSocio"] . "' ", "array");
            $datos_servicio = $dbo->fetchAll("Servicio", " IDServicio = '" . $datos_reserva["IDServicio"] . "' ", "array");
            $datos_maestro = $dbo->fetchAll("ServicioMaestro", " IDServicioMaestro = '" . $datos_servicio["IDServicioMaestro"] . "' ", "array");
            $datos_elemento = $dbo->fetchAll("ServicioElemento", " IDServicioElemento = '" . $datos_reserva["IDServicioElemento"] . "' ", "array");
            $datos_tipo_reserva = $dbo->fetchAll("ServicioTipoReserva", " IDServicioTipoReserva = '" . $IDTipoReserva . "' ", "array");

            $correo = trim($datos_servicio["EmailNotificacion"]);

            $nombre_servicio_personalizado = $dbo->getFields("ServicioClub", "TituloServicio", "IDClub = '" . $datos_reserva["IDClub"] . "' and IDServicioMaestro = '" . $datos_maestro["IDServicioMaestro"] . "'");
            if (empty($nombre_servicio_personalizado)) {
                $nombre_servicio_personalizado = $datos_maestro["Nombre"];
            }

            $otros_datos_reserva = "SELECT IDServicioCampo, Nombre FROM ServicioCampo WHERE IDServicio = '" . $datos_reserva["IDServicio"] . "' ";
            $r_otros_datos_reserva = $dbo->query($otros_datos_reserva);
            if ($dbo->rows($r_otros_datos_reserva) > 0) {
                // Consulto los otros datos
                $sql_valor_otros = "SELECT IDServicioCampo, Valor FROM ReservaGeneralCampo Where IDReservaGeneral = '" . $id_reserva . "' ";
                $r_valor_otros = $dbo->query($sql_valor_otros);
                while ($row_valor_otros = $dbo->fetchArray($r_valor_otros)) {
                    $array_valor_otro[$row_valor_otros["IDServicioCampo"]] = $row_valor_otros["Valor"];
                }

                while ($row_otros_datos_reserva = $dbo->fetchArray($r_otros_datos_reserva)) {
                    $otros_datos .= "<br><b>" . $row_otros_datos_reserva["Nombre"] . "</b>:" . $array_valor_otro[$row_otros_datos_reserva["IDServicioCampo"]];
                }

            }

            if (!empty($correo) && $solo_socio == ""):
                $msg = "<br>Cordial Saludo,<br><br>
									Se ha realizado una nueva reserva.<br><br>
									<b>Servicio:</b>" . utf8_decode($nombre_servicio_personalizado) . "<br>
									<b>Socio:</b>" . utf8_decode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"]) . "<br>
									<b>Fecha:</b>" . $datos_reserva["Fecha"] . "<br>
									<b>Hora:</b>" . $datos_reserva["Hora"] . "<br>
									<b>Elemento:</b>" . $datos_elemento["Nombre"] . "<br>
									<b>Cantidad de turnos:</b>" . $datos_tipo_reserva["Nombre"] . "(" . $datos_tipo_reserva["NumeroTurnos"] . " turnos)   <br>
									<b># Personas (salones):</b>" . $datos_reserva["CantidadInvitadoSalon"] . "<br>
									<b># Observaciones:</b>" . $datos_reserva["Observaciones"] . "<br>" .
                    $otros_datos . "
									<br><br>Por favor no responda este correo<br><br>
									<b>Mi Club App</b>";

                $mensaje = "
											<body>
												<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
													<tr>
														<td>
															<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
														</td>
													</tr>
													<tr>
														<td>" .
                    $msg
                    . "</td>
													</tr>
												</table>
											</body>
									";

                $url_baja = URLROOT . "contactenos.php";

                try {
                    $mail = new phpmailer();
                    $array_correo = explode(",", $correo);
                    if (count($array_correo) > 0) {
                        foreach ($array_correo as $correo_value) {
                            if (filter_var(trim($correo_value), FILTER_VALIDATE_EMAIL)) {
                                $mail->AddAddress($correo_value);
                            }
                        }
                    }

                    $asunto = "Reserva " . $nombre_servicio_personalizado . " " . $datos_socio["Nombre"] . " " . $datos_socio["Apellido"] . " " . $datos_reserva["Fecha"] . " " . $datos_reserva["Hora"];
                    $subject = "=?UTF-8?B?" . base64_encode($asunto) . "=?=";

                    $datos_club["RemitenteCorreo"] = "=?UTF-8?B?" . base64_encode($datos_club["RemitenteCorreo"]) . "=?=";

                    $mail->Subject = $subject;
                    $mail->Body = $mensaje;
                    $mail->IsHTML(true);
                    $mail->Sender = $datos_club["CorreoRemitente"];
                    $mail->Timeout = 120;
                    //$mail->IsSMTP();
                    $mail->Port = PUERTO_SMTP;
                    $mail->SMTPAuth = true;
                    $mail->Host = HOST_SMTP;
                    //$mail->Mailer = 'smtp';
                    $mail->Password = PASSWORD_SMPT;
                    $mail->Username = USER_SMTP;
                    $mail->From = $datos_club["CorreoRemitente"];
                    $mail->FromName = $datos_club["RemitenteCorreo"];
                    $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
                    //$mail->SMTPDebug = true;
                    //$mail->MailerDebug = true;
                    $confirm = $mail->Send();
                } catch (phpmailerException $e) {
                    //echo $e->errorMessage(); //Pretty error messages from PHPMailer
                } catch (Exception $e) {
                    //echo $e->getMessage(); //Boring error messages from anything else!
                }
                //$mail->ErrorInfo();
                //echo "enviado";
                //exit;

            endif;

            //Enviar correo al socio si asi está configurado
            if ($datos_servicio["NotificarSocioMailReserva"] == "S" && !empty($datos_servicio["TextoCorreoSocio"]) && !empty($datos_socio["CorreoElectronico"])):

                $correo = $datos_socio["CorreoElectronico"];
                if (!empty($correo) && filter_var(trim($correo), FILTER_VALIDATE_EMAIL)):
                    if ($recordacion == "S") {
                        $msg = $datos_servicio["TextoRecordacionSocio"];
                    } else {
                        $msg = $datos_servicio["TextoCorreoSocio"];
                    }

                    if ($datos_reserva["IDClub"] != 55) {
                        $msg = str_replace("/file/noticia/editor/", IMGNOTICIA_ROOT . 'editor/', $msg);
                    }

                    $msg = str_replace("[Servicio]", $nombre_servicio_personalizado, $msg);
                    $msg = str_replace("[FechaServicio]", $datos_reserva["Fecha"], $msg);
                    $msg = str_replace("[HoraServicio]", $datos_reserva["Hora"], $msg);
                    $msg = str_replace("[NombreSocio]", utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"]), $msg);
                    $msg = str_replace("[DocumentoSocio]", $datos_socio["NumeroDocumento"], $msg);

                    $mensaje = "
												<body>
													<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
														<tr>
															<td>
																<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
															</td>
														</tr>
														<tr>
															<td>" .
                        $msg
                        . "</td>
														</tr>
													</table>
												</body>
										";

                    $url_baja = URLROOT . "contactenos.php";

                    try {
                        $mail = new phpmailer();
                        $array_correo = explode(",", $correo);
                        if (count($array_correo) > 0) {
                            foreach ($array_correo as $correo_value) {
                                if (filter_var(trim($correo_value), FILTER_VALIDATE_EMAIL)) {
                                    $mail->AddAddress($correo_value);
                                }
                            }
                        }

                        $asunto = "Reserva " . $nombre_servicio_personalizado . " " . $datos_socio["Nombre"] . " " . $datos_socio["Apellido"] . " " . $datos_reserva["Fecha"] . " " . $datos_reserva["Hora"];
                        $subject = "=?UTF-8?B?" . base64_encode($asunto) . "=?=";

                        $datos_club["RemitenteCorreo"] = "=?UTF-8?B?" . base64_encode($datos_club["RemitenteCorreo"]) . "=?=";

                        $mail->Subject = $subject;
                        $mail->Body = $mensaje;
                        $mail->IsHTML(true);
                        $mail->Sender = $datos_club["CorreoRemitente"];
                        $mail->Timeout = 120;
                        //$mail->IsSMTP();
                        $mail->Port = PUERTO_SMTP;
                        $mail->SMTPAuth = true;
                        $mail->Host = HOST_SMTP;
                        //$mail->Mailer = 'smtp';
                        $mail->Password = PASSWORD_SMPT;
                        $mail->Username = USER_SMTP;
                        $mail->From = $datos_club["CorreoRemitente"];
                        $mail->FromName = $datos_club["RemitenteCorreo"];
                        $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
                        //$mail->SMTPDebug = true;
                        //$mail->MailerDebug = true;
                        $confirm = $mail->Send();
                    } catch (phpmailerException $e) {
                        //echo $e->errorMessage(); //Pretty error messages from PHPMailer
                    } catch (Exception $e) {
                        //echo $e->getMessage(); //Boring error messages from anything else!
                    }
                    //$mail->ErrorInfo();
                    //echo "enviado";
                    //exit;
                endif;

            endif;

        endif;

    }

    public function notifica_reserva_incumplida($id_reserva)
    {
        $dbo = &SIMDB::get();

        if (!empty($id_reserva)):

            $datos_reserva = $dbo->fetchAll("ReservaGeneral", " IDReservaGeneral = '" . $id_reserva . "' ", "array");
            $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $datos_reserva["IDClub"] . "' ", "array");
            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_reserva["IDSocio"] . "' ", "array");
            $datos_servicio = $dbo->fetchAll("Servicio", " IDServicio = '" . $datos_reserva["IDServicio"] . "' ", "array");
            $datos_maestro = $dbo->fetchAll("ServicioMaestro", " IDServicioMaestro = '" . $datos_servicio["IDServicioMaestro"] . "' ", "array");
            $datos_elemento = $dbo->fetchAll("ServicioElemento", " IDServicioElemento = '" . $datos_reserva["IDServicioElemento"] . "' ", "array");
            $datos_tipo_reserva = $dbo->fetchAll("ServicioTipoReserva", " IDServicioTipoReserva = '" . $IDTipoReserva . "' ", "array");

            $correo = $datos_servicio["EmailNotificacion"];

            $nombre_servicio_personalizado = $dbo->getFields("ServicioClub", "TituloServicio", "IDClub = '" . $datos_reserva["IDClub"] . "' and IDServicioMaestro = '" . $datos_maestro["IDServicioMaestro"] . "'");
            if (empty($nombre_servicio_personalizado)) {
                $nombre_servicio_personalizado = $datos_maestro["Nombre"];
            }

            //Enviar correo al socio si asi está configurado
            if ($datos_servicio["NotificarSocioReservaIncumplida"] == "S" && !empty($datos_servicio["TextoCorreoReservaIncumplida"]) && !empty($datos_socio["CorreoElectronico"])):

                $correo = $datos_socio["CorreoElectronico"];
                if (!empty($correo)):

                    $msg = $datos_servicio["TextoCorreoReservaIncumplida"];

                    $msg = str_replace("[Servicio]", $nombre_servicio_personalizado, $msg);
                    $msg = str_replace("[FechaServicio]", $datos_reserva["Fecha"] . " " . $datos_reserva["Hora"], $msg);
                    $msg = str_replace("[FechaCreacionReserva]", $datos_reserva["FechaTrCr"], $msg);

                    $mensaje = "
												<body>
													<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
														<tr>
															<td>
																<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
															</td>
														</tr>
														<tr>
															<td>" .
                        $msg
                        . "</td>
														</tr>
													</table>
												</body>
										";

                    $url_baja = URLROOT . "contactenos.php";

                    try {
                        $mail = new phpmailer();
                        $array_correo = explode(",", $correo);
                        if (count($array_correo) > 0) {
                            foreach ($array_correo as $correo_value) {
                                if (filter_var(trim($correo_value), FILTER_VALIDATE_EMAIL)) {
                                    $mail->AddAddress($correo_value);
                                }
                            }
                        }

                        $mail->Subject = "Reserva Incumplida: " . $nombre_servicio_personalizado . $datos_reserva["Fecha"] . " " . $datos_reserva["Hora"];
                        $mail->Body = $mensaje;
                        $mail->IsHTML(true);
                        $mail->Sender = $datos_club["CorreoRemitente"];
                        $mail->Timeout = 120;
                        //$mail->IsSMTP();
                        $mail->Port = PUERTO_SMTP;
                        $mail->SMTPAuth = true;
                        $mail->Host = HOST_SMTP;
                        //$mail->Mailer = 'smtp';
                        $mail->Password = PASSWORD_SMPT;
                        $mail->Username = USER_SMTP;
                        $mail->From = $datos_club["CorreoRemitente"];
                        $mail->FromName = "Club";
                        $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
                        //$mail->SMTPDebug = true;
                        //$mail->MailerDebug = true;
                        $confirm = $mail->Send();
                    } catch (phpmailerException $e) {
                        //echo $e->errorMessage(); //Pretty error messages from PHPMailer
                    } catch (Exception $e) {
                        //echo $e->getMessage(); //Boring error messages from anything else!
                    }
                    //$mail->ErrorInfo();
                    //echo "enviado";
                    //exit;
                endif;

            endif;

        endif;

    }

    public function notificar_nueva_inscripcion_evento($id_evento, $IDSocio, $OtrosDatosFormulario, $Version = "")
    {
        $dbo = &SIMDB::get();

        if (!empty($id_evento)):
            $datos_evento = $dbo->fetchAll("Evento" . $Version, " IDEvento" . $Version . " = '" . $id_evento . "' ", "array");
            $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $datos_evento["IDClub"] . "' ", "array");
            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
            $correo = $datos_evento["EmailNotificacionInscripcion"];

            if (!empty($correo)):
                $msg = "<br>Cordial Saludo,<br><br>
									Se ha realizado una nueva inscripcion al evento: " . $datos_evento["Titular"] . "<br><br>
									<b>Socio:</b>" . utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"]) . "<br>
									<b>Datos:</b>" . utf8_encode($OtrosDatosFormulario) . "<br>
									Por favor no responda este correo<br><br>
									<b>Mi Club App</b>";

                $mensaje = "
											<body>
												<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
													<tr>
														<td>
															<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
														</td>
													</tr>
													<tr>
														<td>" .
                    $msg
                    . "</td>
													</tr>
												</table>
											</body>
									";

                $url_baja = URLROOT . "contactenos.php";

                try {
                    $mail = new phpmailer();
                    $array_correo = explode(",", $correo);
                    if (count($array_correo) > 0) {
                        foreach ($array_correo as $correo_value) {
                            if (filter_var(trim($correo_value), FILTER_VALIDATE_EMAIL)) {
                                $mail->AddAddress($correo_value);
                            }
                        }
                    }

                    $mail->Subject = "Inscripcion evento: " . $datos_evento["Titular"];
                    $mail->Body = $mensaje;
                    $mail->IsHTML(true);
                    $mail->Sender = $datos_club["CorreoRemitente"];
                    $mail->Timeout = 120;
                    //$mail->IsSMTP();
                    $mail->Port = PUERTO_SMTP;
                    $mail->SMTPAuth = true;
                    $mail->Host = HOST_SMTP;
                    //$mail->Mailer = 'smtp';
                    $mail->Password = PASSWORD_SMPT;
                    $mail->Username = USER_SMTP;
                    $mail->From = $datos_club["CorreoRemitente"];
                    $mail->FromName = "Club";
                    $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
                    //$mail->SMTPDebug = true;
                    //$mail->MailerDebug = true;
                    $confirm = $mail->Send();
                } catch (phpmailerException $e) {
                    //echo $e->errorMessage(); //Pretty error messages from PHPMailer
                } catch (Exception $e) {
                    //echo $e->getMessage(); //Boring error messages from anything else!
                }
                //$mail->ErrorInfo();
                //echo "enviado";
                //exit;
            endif;

        endif;

    }

    public function notificar_elimina_inscripcion_evento($IDEventoRegistro, $IDSocio, $Version = "")
    {
        $dbo = &SIMDB::get();

        if (!empty($IDEventoRegistro)):
            $datos_evento_reg = $dbo->fetchAll("EventoRegistro" . $Version, " IDEventoRegistro" . $Version . " = '" . $IDEventoRegistro . "' ", "array");
            $datos_evento = $dbo->fetchAll("Evento" . $Version, " IDEvento" . $Version . " = '" . $datos_evento_reg["IDEvento"] . "' ", "array");
            $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $datos_evento["IDClub"] . "' ", "array");
            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
            $correo = $datos_evento["EmailNotificacionInscripcion"];

            if (!empty($correo)):
                $msg = "<br>Cordial Saludo,<br><br>
									Se ha realizado una nueva Eliminacion de inscripcion al evento: " . $datos_evento["Titular"] . "<br><br>
									<b>Socio:</b>" . utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"]) . "<br>
									<b>Datos:</b>" . utf8_encode($OtrosDatosFormulario) . "<br>
									Por favor no responda este correo<br><br>
									<b>Mi Club App</b>";

                $mensaje = "
											<body>
												<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
													<tr>
														<td>
															<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
														</td>
													</tr>
													<tr>
														<td>" .
                    $msg
                    . "</td>
													</tr>
												</table>
											</body>
									";

                $url_baja = URLROOT . "contactenos.php";

                try {
                    $mail = new phpmailer();
                    $array_correo = explode(",", $correo);
                    if (count($array_correo) > 0) {
                        foreach ($array_correo as $correo_value) {
                            if (filter_var(trim($correo_value), FILTER_VALIDATE_EMAIL)) {
                                $mail->AddAddress($correo_value);
                            }
                        }
                    }

                    $mail->Subject = "Eliminacion Inscripcion evento: " . $datos_evento["Titular"];
                    $mail->Body = $mensaje;
                    $mail->IsHTML(true);
                    $mail->Sender = $datos_club["CorreoRemitente"];
                    $mail->Timeout = 120;
                    //$mail->IsSMTP();
                    $mail->Port = PUERTO_SMTP;
                    $mail->SMTPAuth = true;
                    $mail->Host = HOST_SMTP;
                    //$mail->Mailer = 'smtp';
                    $mail->Password = PASSWORD_SMPT;
                    $mail->Username = USER_SMTP;
                    $mail->From = $datos_club["CorreoRemitente"];
                    $mail->FromName = "Club";
                    $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
                    //$mail->SMTPDebug = true;
                    //$mail->MailerDebug = true;
                    $confirm = $mail->Send();
                } catch (phpmailerException $e) {
                    //echo $e->errorMessage(); //Pretty error messages from PHPMailer
                } catch (Exception $e) {
                    //echo $e->getMessage(); //Boring error messages from anything else!
                }
                //$mail->ErrorInfo();
                //echo "enviado";
                //exit;
            endif;

        endif;

    }

    public function notificar_elimina_reserva($id_reserva, $IDTipoReserva = "")
    {
        $dbo = &SIMDB::get();

        if (!empty($id_reserva)):

            $datos_reserva = $dbo->fetchAll("ReservaGeneralEliminada", " IDReservaGeneral = '" . $id_reserva . "' ", "array");
            $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $datos_reserva["IDClub"] . "' ", "array");
            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_reserva["IDSocio"] . "' ", "array");
            $datos_servicio = $dbo->fetchAll("Servicio", " IDServicio = '" . $datos_reserva["IDServicio"] . "' ", "array");
            $datos_maestro = $dbo->fetchAll("ServicioMaestro", " IDServicioMaestro = '" . $datos_servicio["IDServicioMaestro"] . "' ", "array");
            $datos_elemento = $dbo->fetchAll("ServicioElemento", " IDServicioElemento = '" . $datos_reserva["IDServicioElemento"] . "' ", "array");
            $datos_tipo_reserva = $dbo->fetchAll("ServicioTipoReserva", " IDServicioTipoReserva = '" . $IDTipoReserva . "' ", "array");

            $correo = $datos_servicio["EmailNotificacion"];

            $nombre_servicio_personalizado = $dbo->getFields("ServicioClub", "TituloServicio", "IDClub = '" . $datos_reserva["IDClub"] . "' and IDServicioMaestro = '" . $datos_maestro["IDServicioMaestro"] . "'");
            if (empty($nombre_servicio_personalizado)) {
                $nombre_servicio_personalizado = $datos_maestro["Nombre"];
            }

            if (!empty($correo)):
                $msg = "<br>Cordial Saludo,<br><br>
						Se ha eliminado una nueva reserva.<br><br>
						<b>Servicio:</b>" . $nombre_servicio_personalizado . "<br>
						<b>Socio:</b>" . utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"]) . "<br>
						<b>Fecha:</b>" . $datos_reserva["Fecha"] . "<br>
						<b>Hora:</b>" . $datos_reserva["Hora"] . "<br>
						<b>Elemento:</b>" . $datos_elemento["Nombre"] . "<br>
						<b>Cantidad de turnos:</b>" . $datos_tipo_reserva["Nombre"] . "(" . $datos_tipo_reserva["NumeroTurnos"] . " turnos)   <br><br><br>
						<b># Personas (salones):</b>" . $datos_reserva["CantidadInvitadoSalon"] . "<br>
						Por favor no responda este correo<br><br>
						<b>Mi Club App</b>";

                $mensaje = "
								<body>
									<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
										<tr>
											<td>
												<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
											</td>
										</tr>
										<tr>
											<td>" .
                    $msg
                    . "</td>
										</tr>
									</table>
								</body>
						";

                $url_baja = URLROOT . "contactenos.php";
                $mail = new phpmailer();
                $array_correo = explode(",", $correo);
                if (count($array_correo) > 0) {
                    foreach ($array_correo as $correo_value) {
                        if (filter_var(trim($correo_value), FILTER_VALIDATE_EMAIL)) {
                            $mail->AddAddress($correo_value);
                        }
                    }
                }

                $mail->Subject = "Eliminacion Reserva " . $nombre_servicio_personalizado . " " . $datos_socio["Nombre"] . " " . $datos_socio["Apellido"] . " " . $datos_reserva["Fecha"] . " " . $datos_reserva["Hora"];
                $mail->Body = $mensaje;
                $mail->IsHTML(true);
                $mail->Sender = $datos_club["CorreoRemitente"];
                $mail->Timeout = 120;
                //$mail->IsSMTP();
                $mail->Port = PUERTO_SMTP;
                $mail->SMTPAuth = true;
                $mail->Host = HOST_SMTP;
                //$mail->Mailer = 'smtp';
                $mail->Password = PASSWORD_SMPT;
                $mail->Username = USER_SMTP;
                $mail->From = $datos_club["CorreoRemitente"];
                $mail->FromName = "Club";
                $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
                $confirm = $mail->Send();
            endif;
        endif;

    }

    public function notificar_nuevo_invitado($IDClub, $IDSocio, $NumeroDocumento, $Nombre, $FechaIngreso, $id_solicitud)
    {
        $dbo = &SIMDB::get();

        if ($IDClub == 25 || $IDClub == 23 || 38): //Gun, Arrayanes Ec, Colombia
            $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");

            if ($IDClub == 25):
                $correo = "recepcion@gunclub.com.co";
            elseif ($IDClub == 23):
                $correo = "seguridad.interna@arrayanes.com, atencionalsocio@arrayanes.com";
            elseif ($IDClub == 38):
                $correo = "recepcion@clubcolombia.org,porteriasocios@clubcolombia.org";
            elseif ($IDClub == 36):
                $correo = "aeroclub@aeroclubdecolombia.com,recepcion@aeroclubdecolombia.com";
            elseif ($IDClub == 8):
                $correo = "jorgechirivi@gmail.com";
            endif;

            //$correo="jorgechirivi@gmail.com";

            if (!empty($correo)):

                //otros datos
                $sql_otros = "SELECT * FROM InvitadosOtrosDatos WHERE IDInvitacion = '" . $id_solicitud . "'";
                $r_otros = $dbo->query($sql_otros);
                while ($row_otros = $dbo->fetchArray($r_otros)) {
                    $otros_datos .= "<br>" . $dbo->getFields("CampoFormularioInvitado", "EtiquetaCampo", "IDCampoFormularioInvitado = '" . $row_otros["IDCampoFormularioInvitado"] . "'") . ":" . $row_otros["Valor"] . "<br>";
                }

                $msg = "<br>Cordial Saludo,<br><br>
						Se ha realizado una nueva invitacion por parte de un socio.<br><br>
						<b>Socio:</b>" . $datos_socio["Nombre"] . " " . $datos_socio["Apellido"] . " (" . $datos_socio["Accion"] . ") " . "<br>
						<b>Nombre Invitado:</b>" . $Nombre . "<br>
						<b>Documento Invitado:</b>" . $NumeroDocumento . "<br>
						<b>Fecha Invitacion:</b>" . $FechaIngreso . "<br><br>
						" . $otros_datos . "
						Por favor no responda este correo<br><br>
						<b>Mi Club App</b>";

                $mensaje = "
								<body>
									<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
										<tr>
											<td>
												<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
											</td>
										</tr>
										<tr>
											<td>" .
                    $msg
                    . "</td>
										</tr>
									</table>
								</body>
						";

                $url_baja = URLROOT . "contactenos.php";
                $mail = new phpmailer();
                $array_correo = explode(",", $correo);
                if (count($array_correo) > 0) {
                    foreach ($array_correo as $correo_value) {
                        if (filter_var(trim($correo_value), FILTER_VALIDATE_EMAIL)) {
                            $mail->AddAddress($correo_value);
                        }
                    }
                }

                $mail->Subject = "Nueva invitacion " . $datos_maestro["Nombre"] . " " . $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
                $mail->Body = $mensaje;
                $mail->IsHTML(true);
                $mail->Sender = $datos_club["CorreoRemitente"];
                $mail->Timeout = 120;
                //$mail->IsSMTP();
                $mail->Port = PUERTO_SMTP;
                $mail->SMTPAuth = true;
                $mail->Host = HOST_SMTP;
                //$mail->Mailer = 'smtp';
                $mail->Password = PASSWORD_SMPT;
                $mail->Username = USER_SMTP;
                $mail->From = $datos_club["CorreoRemitente"];
                $mail->FromName = "Club";
                $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
                $confirm = $mail->Send();
            endif;
        endif;

    }

    public function generar_qr($IDInvitacionGenerada, $parametros_codigo_qr)
    {

        //set it to writable location, a place for temp generated PNG files
        $PNG_TEMP_DIR = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR;

        //html PNG location prefix
        $PNG_WEB_DIR = 'temp/';

        include LIBDIR . "phpqrcode/qrlib.php";

        //ofcourse we need rights to create temp dir
        if (!file_exists($PNG_TEMP_DIR)) {
            mkdir($PNG_TEMP_DIR);
        }

        $filename = $PNG_TEMP_DIR . 'test.png';

        $matrixPointSize = 5;
        $errorCorrectionLevel = 'L';

        $filename = $PNG_TEMP_DIR . 'test' . md5($IDInvitacionGenerada . '|' . $errorCorrectionLevel . '|' . $matrixPointSize) . '.png';
        QRcode::png($parametros_codigo_qr, $filename, $errorCorrectionLevel, $matrixPointSize, 2);

        return '<img src="' . URLROOT . 'admin/lib/' . $PNG_WEB_DIR . basename($filename) . '" /><hr/>';

    }

    public function generar_carne_qr($IDSocio, $parametros_codigo_qr)
    {

        //set it to writable location, a place for temp generated PNG files
        //$PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;

        $PNG_TEMP_DIR = SOCIO_DIR . "qr/";

        //html PNG location prefix
        $PNG_WEB_DIR = 'temp/';

        require_once LIBDIR . "phpqrcode/qrlib.php";

        //ofcourse we need rights to create temp dir
        if (!file_exists($PNG_TEMP_DIR)) {
            mkdir($PNG_TEMP_DIR);
        }

        $filename = $PNG_TEMP_DIR . 'test.png';

        $matrixPointSize = 5;
        $errorCorrectionLevel = 'L';

        $filename = $PNG_TEMP_DIR . 'test' . md5($parametros_codigo_qr . $IDSocio . '|' . $errorCorrectionLevel . '|' . $matrixPointSize) . '.png';
        QRcode::png($parametros_codigo_qr, $filename, $errorCorrectionLevel, $matrixPointSize, 2);

        return basename($filename);

    }

    public function generar_qr_evento($IDSocio, $parametros_codigo_qr)
    {

        //set it to writable location, a place for temp generated PNG files
        //$PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;

        $PNG_TEMP_DIR = SOCIO_DIR . "qr/";

        //html PNG location prefix
        $PNG_WEB_DIR = 'temp/';

        include LIBDIR . "phpqrcode/qrlib.php";

        //ofcourse we need rights to create temp dir
        if (!file_exists($PNG_TEMP_DIR)) {
            mkdir($PNG_TEMP_DIR);
        }

        $filename = $PNG_TEMP_DIR . 'test.png';

        $matrixPointSize = 5;
        $errorCorrectionLevel = 'L';

        $filename = $PNG_TEMP_DIR . 'test' . md5($parametros_codigo_qr . $IDSocio . '|' . $errorCorrectionLevel . '|' . $matrixPointSize) . '.png';
        QRcode::png($parametros_codigo_qr, $filename, $errorCorrectionLevel, $matrixPointSize, 2);

        return basename($filename);

    }

    public function generar_codigo_qr_talega($codigo, $IDCliente, $idTalega)
    {

        //set it to writable location, a place for temp generated PNG files
        //$PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;

        $PNG_TEMP_DIR = TALEGA_DIR . "/";

        //html PNG location prefix
        include LIBDIR . "phpqrcode/qrlib.php";

        //ofcourse we need rights to create temp dir
        if (!file_exists($PNG_TEMP_DIR)) {
            mkdir($PNG_TEMP_DIR);
        }

        $matrixPointSize = 5;
        $errorCorrectionLevel = 'L';

        $filename = $PNG_TEMP_DIR . 'QR_talega_' . $IDCliente . '_' . rand(1, 10000) . '.png';
        QRcode::png($codigo, $filename, $errorCorrectionLevel, $matrixPointSize, 2);

        return basename($filename);
    }

    public function generar_carne_qr_empleado($IDUsuario, $parametros_codigo_qr)
    {

        //set it to writable location, a place for temp generated PNG files
        //$PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;

        $PNG_TEMP_DIR = USUARIO_DIR . "qr/";

        //html PNG location prefix
        $PNG_WEB_DIR = 'temp/';

        include LIBDIR . "phpqrcode/qrlib.php";

        //ofcourse we need rights to create temp dir
        if (!file_exists($PNG_TEMP_DIR)) {
            mkdir($PNG_TEMP_DIR);
        }

        $filename = $PNG_TEMP_DIR . 'test.png';

        $matrixPointSize = 5;
        $errorCorrectionLevel = 'L';

        $filename = $PNG_TEMP_DIR . 'test' . md5($parametros_codigo_qr . $IDSocio . '|' . $errorCorrectionLevel . '|' . $matrixPointSize) . '.png';
        QRcode::png($parametros_codigo_qr, $filename, $errorCorrectionLevel, $matrixPointSize, 2);

        return basename($filename);

    }

    public function enviar_codigo_qr($IDInvitacionGenerada, $parametros_codigo_qr, $tipo = "")
    {

        $dbo = &SIMDB::get();

        $imagen_codigo = SIMUtil::generar_qr($IDInvitacionGenerada, $parametros_codigo_qr);

        if ($tipo == "Contratista"):
            $datos_invitacion = $dbo->fetchAll("SocioAutorizacion", " IDSocioAutorizacion = '" . $IDInvitacionGenerada . "' ", "array");
        else:
            $datos_invitacion = $dbo->fetchAll("SocioInvitadoEspecial", " IDSocioInvitadoEspecial = '" . $IDInvitacionGenerada . "' ", "array");
        endif;

        $datos_invitado = $dbo->fetchAll("Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array");
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $datos_invitacion["IDClub"] . "' ", "array");
        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");

        //$correo=$datos_invitado["Email"].",".$datos_socio["CorreoElectronico"];
        $correo = $datos_invitado["Email"];

        if (filter_var($correo, FILTER_VALIDATE_EMAIL)) {

            $msg = "<br>Hola " . $datos_invitacion["Nombre"] . ",<br><br>
            " . $datos_socio["Nombre"] . " " . $datos_socio["Apellido"] . " tiene una invitación para el pr&oacute;ximo " . $datos_invitacion["FechaIngreso"] . $mensaje_adicional . "<br><br>
            Para confirmar el ingreso,  <a href='" . $URLDiagnosticoWeb . "'>ingresa a aqui</a>  y diligencia el Protocolo de Salud
            <br><br>Adicionalmente, te invitamos a tener presente:<br><br>
            Si presentas alg&uacute;n s&iacute;ntoma asociado a COVID-19, abstente de presentarte.<br>
            Llega a la hora acordada.<br>
            Usa el tapabocas en todo momento.<br>
            Conserva una distancia de 2 metros con otros.<br>
            Cordialmente,<br><br>
            Recuerde presentar el siguiente codigo para su acceso: <br><br>
            " . $imagen_codigo . "
            Por favor no responda este correo<br>
            Cordialmente<br><br>
            <b>Notificaciones " . $datos_club["Nombre"] . "</b>";

            //Para el rancho envio datos adicionales
            if ($datos_invitacion["IDClub"] == 34):
                $mensaje_adicional = "<br>Invitado: " . $datos_invitado["NumeroDocumento"] . " " . $datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"] . " / Invitacion realizada por el socio: " . $datos_socio["Nombre"] . " " . $datos_socio["Apellido"] . " Accion:" . $datos_socio["Accion"];
            endif;

            $msg = "<br>Cordial Saludo, " . $datos_invitacion["Nombre"] . "<br><br>
				Se ha generado una nueva invitacion desde el dia: " . $datos_invitacion["FechaInicio"] . " Hasta: " . $datos_invitacion["FechaFin"] . $mensaje_adicional . "<br><br>
				Recuerde presentar el siguiente codigo para su acceso: <br><br>

				" . $imagen_codigo . "

				Por favor no responda este correo<br>
				Cordialmente<br><br>
				<b>Notificaciones " . $datos_club["Nombre"] . "</b>";

            $mensaje = "
						<body>
							<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
								<tr>
									<td>
										<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
									</td>
								</tr>
								<tr>
									<td>" .
                $msg
                . "</td>
								</tr>
							</table>
						</body>
				";

            $url_baja = URLROOT . "contactenos.php";

            $mail = new phpmailer();
            $array_correo = explode(",", $correo);
            if (count($array_correo) > 0) {
                foreach ($array_correo as $correo_value) {
                    $mail->AddAddress($correo_value);
                }
            }

            //Para el rancho envio copia
            if ($datos_invitacion["IDClub"] == 34):
                $mail->AddAddress("jservicios@crsf.com.ec");
                $mail->AddAddress("control@crsf.com.ec");
                $mail->AddAddress("operaciones@crsf.com.ec");
                //$mail->AddAddress("jorgechirivi@gmail.com");
            endif;

            $mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
            $mail->Subject = "Nueva Invitacion  " . $datos_invitacion["FechaInicio"];
            $mail->Body = $mensaje;
            $mail->IsHTML(true);
            $mail->Sender = $datos_club["CorreoRemitente"];
            $mail->Timeout = 120;
            //$mail->IsSMTP();
            $mail->Port = PUERTO_SMTP;
            $mail->SMTPAuth = true;
            $mail->Host = HOST_SMTP;
            //$mail->Mailer = 'smtp';
            $mail->Password = PASSWORD_SMPT;
            $mail->Username = USER_SMTP;
            $mail->From = $datos_club["CorreoRemitente"];
            $mail->FromName = "Club";
            $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
            //$confirm=$mail->Send();
            $mail->Send();
        } else {
            //echo "error en correo";
            $error_correo = 1;
        }

    }

    public function enviar_codigo_qr_invitado($IDInvitacionGenerada, $parametros_codigo_qr, $tipo = "", $correo)
    {

        $dbo = &SIMDB::get();

        $imagen_codigo = SIMUtil::generar_qr($IDInvitacionGenerada, $parametros_codigo_qr);

        $datos_invitacion = $dbo->fetchAll("SocioInvitado", " IDSocioInvitado = '" . $IDInvitacionGenerada . "' ", "array");

        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $datos_invitacion["IDClub"] . "' ", "array");
        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");

        $parametros = "IDSocio=" . $datos_invitacion["IDSocio"] . "&NumeroDocumento=" . $datos_invitacion["NumeroDocumento"] . "&IDInvitacion=" . $IDInvitacionGenerada . "&IDClub=" . $datos_club["IDClub"];
        $parametro_enviar = self::str_crypt($parametros, 'e', KEY_SERVICES); //encrypt($parametros,KEY_SERVICES,16);

        $URLDiagnosticoWeb = URLWEB . "click.php?p=" . $parametro_enviar;
        $msg_diagnostico = "<br>Recuerde diligenciar el autodiagnostico antes de llegar a las instalaciones, es un requisito obligatorio para el ingreso, debe llenarlo el dia que se presente en las intalaciones: " . "<a href='" . $URLDiagnosticoWeb . "'>ingrese aca para diligenciarlo</a>";

        if (filter_var($correo, FILTER_VALIDATE_EMAIL)) {

            $msg = "<br>Hola " . $datos_invitacion["Nombre"] . ",<br><br>
              " . $datos_socio["Nombre"] . " " . $datos_socio["Apellido"] . " tiene una invitación para el pr&oacute;ximo " . $datos_invitacion["FechaIngreso"] . $mensaje_adicional . "<br><br>
              Para confirmar el ingreso,  <a href='" . $URLDiagnosticoWeb . "'>ingresa a aqui</a>  y diligencia el Protocolo de Salud
              <br><br>Adicionalmente, te invitamos a tener presente:<br><br>
              Si presentas alg&uacute;n s&iacute;ntoma asociado a COVID-19, abstente de presentarte.<br>
              Llega a la hora acordada.<br>
              Usa el tapabocas en todo momento.<br>
              Conserva una distancia de 2 metros con otros.<br>
              Cordialmente,<br><br>
              Recuerde presentar el siguiente codigo para su acceso: <br><br>
              " . $imagen_codigo . "
              Por favor no responda este correo<br>
              Cordialmente<br><br>
              <b>Notificaciones " . $datos_club["Nombre"] . "</b>";

            //Para el rancho envio datos adicionales
            if ($datos_invitacion["IDClub"] == 34):
                $mensaje_adicional = "<br>Invitado: " . $datos_invitado["NumeroDocumento"] . " " . $datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"] . " / Invitacion realizada por el socio: " . $datos_socio["Nombre"] . " " . $datos_socio["Apellido"] . " Accion:" . $datos_socio["Accion"];
            endif;

            /*
            $msg = "<br>Cordial Saludo,<br><br>
            Se ha generado una nueva invitacion ara el dia: " .$datos_invitacion["FechaIngreso"] . $mensaje_adicional . "<br><br>
            Recuerde presentar el siguiente codigo para su acceso: <br><br>
            ".$imagen_codigo."
            Por favor no responda este correo<br>
            Cordialmente<br><br>
            <b>Notificaciones ".$datos_club["Nombre"]."</b>";
             */

            $mensaje = "
						<body>
							<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
								<tr>
									<td>
										<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
									</td>
								</tr>
								<tr>
									<td>" .
                $msg
                . "</td>
								</tr>
							</table>
						</body>
				";

            $url_baja = URLROOT . "contactenos.php";

            $mail = new phpmailer();
            $array_correo = explode(",", $correo);
            if (count($array_correo) > 0) {
                foreach ($array_correo as $correo_value) {
                    $mail->AddAddress($correo_value);
                }
            }

            //Para el rancho envio copia
            if ($datos_invitacion["IDClub"] == 34):
                $mail->AddAddress("jservicios@crsf.com.ec");
                $mail->AddAddress("control@crsf.com.ec");
                $mail->AddAddress("operaciones@crsf.com.ec");
                //$mail->AddAddress("jorgechirivi@gmail.com");
            endif;

            $mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
            $mail->Subject = "Nueva Invitacion  " . $datos_invitacion["FechaInicio"];
            $mail->Body = $mensaje;
            $mail->IsHTML(true);
            $mail->Sender = $datos_club["CorreoRemitente"];
            $mail->Timeout = 120;
            //$mail->IsSMTP();
            $mail->Port = PUERTO_SMTP;
            $mail->SMTPAuth = true;
            $mail->Host = HOST_SMTP;
            //$mail->Mailer = 'smtp';
            $mail->Password = PASSWORD_SMPT;
            $mail->Username = USER_SMTP;
            $mail->From = $datos_club["CorreoRemitente"];
            $mail->FromName = "Club";
            $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
            //$confirm=$mail->Send();
            $mail->Send();
        } else {
            //echo "error en correo";
            $error_correo = 1;
        }

    }

    public function view_reserva_app($horas, $elementos = "")
    {
        $dbo = &SIMDB::get();
        $IDClub = $horas["response"]["0"]["IDClub"];

        foreach ($elementos as $key_elemento => $datos_elemento) {
            $array_elementos_ver[] = $datos_elemento["IDElemento"];
        }

        if (!empty($elementos) && count($array_elementos_ver) <= 0) {
            $array_elementos_ver = explode(",", $elementos);
        }

        $respuesta = string;
        $todashoras = array();

        $respuesta = '<table id="simple-table" class="table table-striped table-bordered table-hover">';

        foreach ($horas["response"]["0"]["Disponibilidad"][0] as $key_horas => $todashoras) {
            $array_hora_disponible[$todashoras["Hora"]] = $todashoras["Hora"];

        } //end for

        ksort($array_hora_disponible);

        foreach ($array_hora_disponible as $key_hora => $todashoras) {
            $respuesta .= '
            <tr>
                <td colspan="2" style="background-color:#EAEAEA">' . $key_hora . '</td>
            </tr>
            <tr>
            <td>
            <table width="100%">';

            foreach ($horas["response"]["0"]["Disponibilidad"][0] as $key_detalle => $detalle) {
                $TipoR = "";
                if (in_array($detalle["IDElemento"], $array_elementos_ver)) {
                    $respuesta .= '
					            <tr>';
                    if ($detalle["Hora"] == $key_hora):
                        if ($detalle["Disponible"] == "S") {

                            $disponible = "Disponibles: ".$detalle["Socio"];
                            if(!empty($detalle["Inscritos"]))
                            {
                                $disponible .= "<br><span style='color:#E63336'> Cupos Tomados: ";
                                foreach($detalle["Inscritos"] as $campos => $dato)
                                {
                                    $disponible .= "<br>Socio: " . $dato["Socio"] . " ";
                                }
                                $disponible .= "</span>";
                            }

                        } else {
                            $disponible = "<span style='color:#E63336'> " . $detalle["Socio"] . "</span>";
                            if (empty($disponible)) {
                                $disponible = "<span style='color:#E63336'> " . utf8_encode($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $detalle["IDSocio"] . "'") . "  " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $detalle["IDSocio"] . "'")) . "</span>";
                            }
                            $TipoR = $detalle["TipoReserva"];
                            if((int)$detalle["IDReserva"]>0){
                              $sql_inv = "SELECT Nombre FROM ReservaGeneralInvitado WHERE IDReservaGeneral = '" . $detalle["IDReserva"] . "' ";
                              $r_inv = $dbo->query($sql_inv);
                              while ($row_inv = $dbo->fetchArray($r_inv)) {
                                  $disponible .= "<span style='color:#E63336'> / " . $row_inv["Nombre"] . "</span>";
                              }
                            }

                        }

                        $respuesta .= ' <td width="15%">' . $detalle["NombreElemento"] . $a . '</td>
                                        <td width="85%">' . $disponible . ' ' . $TipoR . '</td>';
                    endif;
                    $respuesta .= '
					                </tr>';
                }
            } //end for
            $respuesta .= '
					            </table>
					            </td>
					            </tr>';

        }

        $respuesta .= ' </table>';

        return $respuesta;
    }

    public function view_reserva_web($horas)
    {
        $dbo = &SIMDB::get();

        $respuesta = string;
        $todashoras = array();

        $respuesta = '<h4>Selecciona hora</h4><ul>';
        foreach ($horas["response"]["0"]["Disponibilidad"][0] as $key_horas => $todashoras) {
            $array_hora_disponible[$todashoras["Hora"]] = $todashoras["Hora"];

        } //end for

        foreach ($array_hora_disponible as $key_hora => $todashoras) {
            $respuesta .= '
            <li class="titulo">' . $key_hora . '</li>';

            foreach ($horas["response"]["0"]["Disponibilidad"][0] as $key_detalle => $detalle) {
                if ($detalle["Hora"] == $key_hora):
                    if ($detalle["Disponible"] == "S"):
                        $disponible = "Reservar";
                        $clase_confirmar = "ver_confirmar";
                    else:
                        $disponible = utf8_decode($detalle["Socio"]);
                        $clase_confirmar = "";
                    endif;
                    $datos = $horas["response"]["0"]["Fecha"] . "|" . $detalle["Hora"] . "|" . $detalle["IDElemento"] . "|" . $detalle["IDDisponibilidad"] . "|" . $detalle["Tee"];
                    $respuesta .= '<li>' . $detalle["NombreElemento"] . '
	                    <a href="#" rel="' . $datos . '" class="' . $clase_confirmar . '">' . $disponible . '</a></li>';
                endif;
            } //end for
        }

        $respuesta .= ' </ul>';
        return $respuesta;
    }

    public function view_busca_invitado_web($socio, $club)
    {
        $dbo = &SIMDB::get();
        $respuesta = string;

        $respuesta = '<h4>Socios Encontrados</h4><ul>';
        $respuesta .= '
		<a href="#" id="volver">
			Volver
		</a>';

        $consulta = "SELECT * FROM Socio WHERE IDClub = '" . $club . "' AND (Nombre LIKE '%" . $socio . "%' OR Apellido LIKE '%" . $socio . "%')";
        $result = $dbo->query($consulta);

        while ($row = $dbo->fetchArray($result)) {
            $datos = $row["Nombre"] . "|" . $row["IDSocio"];
            $respuesta .= '<li>' . $row["Nombre"] . ' ' . $row['Apellido'] .
                '<a href="#" rel="' . $datos . '" id = "agregar_invitado_socio">
									Invitar
								</a>
							</li>';
        }

        $respuesta .= '</ul>';
        return $respuesta;
    }

    public function genera_codigo_autorizacion($Tipo)
    {
        $dbo = &SIMDB::get();
        $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $letra = substr($str, rand(0, 26), 1);
        $numero = rand(0, 9999);
        if (strlen($numero) == 1) {
            $numero_inicial = "000";
        } elseif (strlen($numero) == 2) {
            $numero_inicial = "00";
        } elseif (strlen($numero) == 3) {
            $numero_inicial = "0";
        }

        $codigo_final = $Tipo . $letra . $numero_inicial . $numero;
        return $codigo_final;
    }

    public function view_reserva_app_grupos($horas)
    {
        $dbo = &SIMDB::get();

        $respuesta = '<table id="simple-table" class="table table-striped table-bordered table-hover">';
        foreach ($horas["response"]["0"]["Disponibilidad"][0] as $key_horas => $todashoras) {
            $array_hora_disponible[$todashoras["Hora"]] = $todashoras["Hora"];

        } //end for

        foreach ($array_hora_disponible as $key_hora => $todashoras) {
            $respuesta .= '
            <tr>
                <td colspan="2" style="background-color:#EAEAEA"><br>' . $key_hora . '</td>
            </tr>
            <tr>
            <td>
            <table width="80%">';

            foreach ($horas["response"]["0"]["Disponibilidad"][0] as $key_detalle => $detalle) {

                $respuesta .= '
            <tr>';
                if ($detalle["Hora"] == $key_hora):
                    if ($detalle["Disponible"] == "S") {
                        $disponible = "<a href='reservas_grupos.php?ids=" . $horas["response"][0]["IDServicio"] . "&hora=" . $key_hora . "&fecha=" . $horas["response"]["0"]["Fecha"] . "&idservicio=" . $horas["response"][0]["IDServicio"] . "&idelemento=" . $detalle["IDElemento"] . "&tee=" . $detalle["Tee"] . "' class='fancybox' data-fancybox-type='iframe'>Disponible</a>";
                    } else {
                        $disponible = $detalle["Socio"];
                    }

                    $respuesta .= '<td width="60%">' . $detalle["NombreElemento"] . '</td>
	                    <td width="40%">' . $disponible . '</td>';
                endif;
                $respuesta .= '
                </tr>';
            } //end for
            $respuesta .= '
            </table>
            </td>
            </tr>';
        }

        $respuesta .= ' </table>';

        return $respuesta;
    }

    public function insert_cedula($campo_id, $id, $tabla, $files)
    {
        $dbo = &SIMDB::get();
        $filedir = CEDULA_DIR;
        $mimes = array('application/vnd.ms-excel', 'text/plain', 'text/csv', 'text/tsv');
        foreach ($files as $key => $file) {
            if (!empty($file['name'])) {
                $ext = $file['type'];
                if (in_array($file['type'], $mimes)) {
                    $nombre_archivo = date("Y-m-d_H:s:i") . $file['name'];
                    if (copy($file['tmp_name'], $filedir . $nombre_archivo)) {
                        $ruta_archivo = $filedir . $nombre_archivo;

                        $sql_elimina = "Delete from " . $tabla . " where " . $campo_id . " = '" . $id . "'";
                        $dbo->query($sql_elimina);

                        if ($fp = fopen($ruta_archivo, "r")) {
                            $cont = 0;
                            $contfallas = 0;
                            while (!feof($fp)) {
                                $id_cliente = "";
                                ini_set('auto_detect_line_endings', true);
                                $linea = fgets($fp, 4096);

                                $fields = array_map('addslashes', array_map('trim', explode(",", $linea)));
                                $cedula = (int) $fields[0];
                                $sql_cedula = " SELECT IDRegistro FROM Registro WHERE Cedula = '" . $cedula . "'";
                                $qry_cedula = $dbo->query($sql_cedula);
                                $r_cedula = $dbo->fetchArray($qry_cedula);
                                $id_cliente = $r_cedula["IDRegistro"];

                                if (empty($id_cliente)) {
                                    $reporte["fallas"] .= "<br>Cedula no existe = " . $cedula;
                                    $contfallas++;
                                } else {
                                    //insertar cedula regla
                                    if ($cedula != 0) {
                                        $sql_cedula_regla = " INSERT INTO " . $tabla . " (" . $campo_id . ", IDRegistro,Cedula, UsuarioTrCr, FechaTrCr) VALUES ( '" . $id . "','" . $id_cliente . "','" . $cedula . "','Admin',NOW())";
                                        $qry_cedula_regla = $dbo->query($sql_cedula_regla);
                                        $cont++;
                                    }
                                }
                            }
                            fclose($fp);
                            unlink($ruta_archivo);
                            $reporte["insertados"] = $cont;
                            return $reporte;
                        } else {
                            echo "error open $filename";
                        }

                    } else {
                        echo "Error al cargar archivo verifique!!! " . $filedir . $file['name'];
                        exit;
                    }

                } else {
                    echo "El archivo no tiene una extension valida por favor verifique que sea un archivo de texto o csv";
                    exit;
                }
            }
        }

    }

    public function push_socio_entrada($IDClub, $IDInvitacion, $TipoInvitacion, $IDUsuario)
    {
        $dbo = &SIMDB::get();

        if (!empty($IDClub) && !empty($IDInvitacion) && !empty($TipoInvitacion)) {

            switch ($TipoInvitacion) {
                case "InvitadoAcceso":
                    $datos_invitacion = $dbo->fetchAll("SocioInvitadoEspecial", " IDSocioInvitadoEspecial = '" . $IDInvitacion . "' ", "array");
                    $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
                    $datos_invitado = $dbo->fetchAll("Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array");

                    //$datos_invitacion_especial = $dbo->fetchAll( "SocioInvitadoEspecial", " IDSocioInvitadoEspecial = '" . $IDInvitacion . "' ", "array" );
                    //if($datos_invitacion_especial["CabezaInvitacion"]=="S"):
                    //$sql_ingreso_grupo = $dbo->query("Update SocioInvitadoEspecial Set Ingreso = 'S', FechaIngreso = NOW() Where  IDPadre = '".$datos_invitacion_especial["IDInvitado"]."' and FechaInicio = '".$datos_invitacion_especial["FechaInicio"]."' and FechaFin = '".$datos_invitacion_especial["FechaFin"]."'");
                    //endif;
                    break;
                case "Contratista":
                    $datos_invitacion = $dbo->fetchAll("SocioAutorizacion", " IDSocioAutorizacion = '" . $IDInvitacion . "' ", "array");
                    $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
                    $datos_invitado = $dbo->fetchAll("Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array");

                    break;
                case "SocioInvitado":
                case "Invitado":
                    $datos_invitacion = $dbo->fetchAll("SocioInvitado", " IDSocioInvitado = '" . $IDInvitacion . "' ", "array");
                    $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
                    $datos_invitado["IDInvitado"] = $datos_invitacion["NumeroDocumento"];
                    $datos_invitado["Nombre"] = $datos_invitacion["Nombre"];

                    break;

            }

            //Para fontanar agrego por cual porteria
            if ($IDClub == 18 || $IDClub == 37):
                $datos_usuario = $dbo->fetchAll("Usuario", " IDUsuario = '" . $IDUsuario . "' ", "array");
                $Observaciones = "por la porteria: " . $datos_usuario["Nombre"];
            endif;

            if (!empty($datos_socio["IDSocio"]) && !empty($datos_invitado["IDInvitado"]) && $datos_socio["Token"] != "2byte" && $datos_socio["Token"] != "" && $datos_socio["IDSocio"] != 103672):
                $users = array(array("id" => $datos_socio["IDSocio"],
                    "idclub" => $IDClub,
                    "registration_key" => $datos_socio["Token"],
                    "deviceType" => $datos_socio["Dispositivo"]),
                );

                $message = "Su invitado " . $datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"] . " ha ingresado a las instalaciones " . date("Y-m-d H:i:s") . " " . $Observaciones;
                $custom["tipo"] = "app";
                $custom["idmodulo"] = (string) "44";
                $custom["titulo"] = "Notificacion";

                if ($datos_socio["Dispositivo"] == "iOS") {
                    $array_ios[] = $datos_socio["Token"];
                } elseif ($datos_socio["Dispositivo"] == "Android") {
                $array_android[] = $datos_socio["Token"];
            }

            SIMUtil::sendAlerts_V2($users, $message, $custom, $TipoApp, $array_android, $array_ios, $IDClub);

            //SIMUtil::sendAlerts($users, $message, $custom);
            //Guardo el log
            $sql_log = $dbo->query("INSERT INTO LogNotificacion (IDNotificacionesGenerales	, IDSocio, IDClub, Token, Dispositivo, Fecha,App,Titulo,Mensaje)
																		Values ('', '" . $datos_socio["IDSocio"] . "','" . $IDClub . "','" . $datos_socio["Token"] . "','$datos_socio[Dispositivo]',NOW(),'Socio','Entrada invitado','" . utf8_decode($message) . "')");

            endif;

        }
    }

    public function push_socio_salida($IDClub, $IDInvitacion, $TipoInvitacion, $IDUsuario)
    {
        $dbo = &SIMDB::get();

        if (!empty($IDClub) && !empty($IDInvitacion) && !empty($TipoInvitacion)) {

            switch ($TipoInvitacion) {
                case "InvitadoAcceso":
                    $datos_invitacion = $dbo->fetchAll("SocioInvitadoEspecial", " IDSocioInvitadoEspecial = '" . $IDInvitacion . "' ", "array");
                    $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
                    $datos_invitado = $dbo->fetchAll("Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array");

                    //$datos_invitacion_especial = $dbo->fetchAll( "SocioInvitadoEspecial", " IDSocioInvitadoEspecial = '" . $IDInvitacion . "' ", "array" );
                    //if($datos_invitacion_especial["CabezaInvitacion"]=="S"):
                    //$sql_ingreso_grupo = $dbo->query("Update SocioInvitadoEspecial Set Ingreso = 'S', FechaIngreso = NOW() Where  IDPadre = '".$datos_invitacion_especial["IDInvitado"]."' and FechaInicio = '".$datos_invitacion_especial["FechaInicio"]."' and FechaFin = '".$datos_invitacion_especial["FechaFin"]."'");
                    //endif;
                    break;
                case "SocioInvitado":
                case "Invitado":
                    $datos_invitacion = $dbo->fetchAll("SocioInvitado", " IDSocioInvitado = '" . $IDInvitacion . "' ", "array");
                    $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
                    $datos_invitado["IDInvitado"] = $datos_invitacion["NumeroDocumento"];
                    $datos_invitado["Nombre"] = $datos_invitacion["Nombre"];
                    break;
            }

            //Para fontanar agrego por cual porteria
            if ($IDClub == 18 || $IDClub == 37):
                $datos_usuario = $dbo->fetchAll("Usuario", " IDUsuario = '" . $IDUsuario . "' ", "array");
                $Observaciones = "por la porteria: " . $datos_usuario["Nombre"];
            endif;

            if (!empty($datos_socio["IDSocio"]) && !empty($datos_invitado["IDInvitado"]) && $datos_socio["Token"] != "2byte" && $datos_socio["Token"] != "" && $datos_socio["IDSocio"] != 103672):
                $users = array(array("id" => $datos_socio["IDSocio"],
                    "idclub" => $IDClub,
                    "registration_key" => $datos_socio["Token"],
                    "deviceType" => $datos_socio["Dispositivo"]),
                );
                $message = "Su invitado " . $datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"] . " ha salido de las instalaciones " . date("Y-m-d H:i:s") . " " . $Observaciones;
                $custom["tipo"] = "app";
                $custom["idmodulo"] = (string) "44";
                $custom["titulo"] = "Notificacion";

                if ($datos_socio["Dispositivo"] == "iOS") {
                    $array_ios[] = $datos_socio["Token"];
                } elseif ($datos_socio["Dispositivo"] == "Android") {
                $array_android[] = $datos_socio["Token"];
            }

            SIMUtil::sendAlerts_V2($users, $message, $custom, $TipoApp, $array_android, $array_ios, $IDClub);

            //SIMUtil::sendAlerts($users, $message, $custom);

            //Guardo el log
            $sql_log = $dbo->query("INSERT INTO LogNotificacion (IDNotificacionesGenerales	, IDSocio, IDClub, Token, Dispositivo, Fecha,App,Titulo,Mensaje)
																		Values ('', '" . $datos_socio["IDSocio"] . "','" . $IDClub . "','" . $datos_socio["Token"] . "','$datos_socio[Dispositivo]',NOW(),'Socio','Salida invitado','" . utf8_decode($message) . "')");
            endif;

        }
    }

    public function push_socio_invitado($IDClub, $IDReservaGeneral, $IDSocio)
    {
        $dbo = &SIMDB::get();

        if (!empty($IDClub) && !empty($IDReservaGeneral) && !empty($IDSocio)) {

            $datos_reserva = $dbo->fetchAll("ReservaGeneral", " IDReservaGeneral = '" . $IDReservaGeneral . "' ", "array");
            $datos_socio_reserva = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_reserva["IDSocio"] . "' ", "array");
            $datos_socio_invitado = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");

            $datos_servicio = $dbo->fetchAll("Servicio", " IDServicio = '" . $datos_reserva["IDServicio"] . "' ", "array");
            $datos_maestro = $dbo->fetchAll("ServicioMaestro", " IDServicioMaestro = '" . $datos_servicio["IDServicioMaestro"] . "' ", "array");
            $NombrePersonalizado = $dbo->getFields("ServicioClub", "TituLoServicio", "IDClub = '" . $IDClub . "' and Activo = 'S' and IDServicioMaestro = '" . $datos_servicio["IDServicioMaestro"] . "'");

            if (empty($NombrePersonalizado)) {
                $NombrePersonalizado = $datos_maestro["Nombre"];
            }

            if (!empty($datos_socio_invitado["IDSocio"]) && !empty($datos_socio_reserva["IDSocio"]) && $datos_socio_invitado["Token"] != "2byte" && $datos_socio_invitado["Token"] != ""):
                $users = array(array("id" => $datos_socio_invitado["IDSocio"],
                    "idclub" => $IDClub,
                    "registration_key" => $datos_socio_invitado["Token"],
                    "deviceType" => $datos_socio_invitado["Dispositivo"]),
                );

                $message = "Ha sido invitado a " . $NombrePersonalizado . " por: " . $datos_socio_reserva["Nombre"] . " " . $datos_socio_reserva["Apellido"] . " Fecha: " . $datos_reserva["Fecha"] . " Hora: " . $datos_reserva["Hora"];
                $custom["tipo"] = "app";
                $custom["idmodulo"] = (string) "2";
                $custom["titulo"] = "Notificacion Club";

                if ($datos_socio_invitado["Dispositivo"] == "iOS") {
                    $array_ios[] = $datos_socio_invitado["Token"];
                } elseif ($datos_socio_invitado["Dispositivo"] == "Android") {
                $array_android[] = $datos_socio_invitado["Token"];
            }

            SIMUtil::sendAlerts_V2($users, $message, $custom, $TipoApp, $array_android, $array_ios, $IDClub);

            //SIMUtil::sendAlerts($users, $message, $custom);
            endif;
        }
    }

    public function push_notifica_reserva($IDClub, $IDReservaGeneral, $TipoApp)
    {
        $dbo = &SIMDB::get();

        if (!empty($IDClub) && !empty($IDReservaGeneral)) {

            $datos_reserva = $dbo->fetchAll("ReservaGeneral", " IDReservaGeneral = '" . $IDReservaGeneral . "' ", "array");
            if (!empty($datos_reserva["IDSocioBeneficiario"])) {
                $IDSocioReserva = $datos_reserva["IDSocioBeneficiario"];
            } else {
                $IDSocioReserva = $datos_reserva["IDSocio"];
            }

            $datos_socio_reserva = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocioReserva . "' ", "array");

            //Consulto si el elemento tiene un usuario y clave dentro del app de empleados
            $sql_elemento_usuario = "Select * From UsuarioServicioElemento Where IDServicioElemento = '" . $datos_reserva["IDServicioElemento"] . "'  ";
            $result_elemento_usuario = $dbo->query($sql_elemento_usuario);
            while ($row_elemento_usuario = $dbo->fetchArray($result_elemento_usuario)):
                //Consulto los datos del empleado
                $datos_empleado = $dbo->fetchAll("Usuario", " IDUsuario = '" . $row_elemento_usuario["IDUsuario"] . "' ", "array");

                if (!empty($datos_empleado["IDUsuario"]) && $datos_empleado["Token"] != "2byte" && $datos_empleado["Token"] != "" && $datos_empleado["PushReserva"] != "N"):

                    $users = array(array("id" => $datos_empleado["IDUsuario"],
                        "idclub" => $IDClub,
                        "registration_key" => $datos_empleado["Token"],
                        "deviceType" => $datos_empleado["Dispositivo"]),
                    );

                    if ($IDClub != 10) {
                        $otro_dato = $datos_socio_reserva["Predio"];
                    }

                    $message = "Ha sido reservado para el día " . $datos_reserva["Fecha"] . " " . $datos_reserva["Hora"] . " por: " . utf8_encode($datos_socio_reserva["Nombre"] . " " . $datos_socio_reserva["Apellido"] . " " . $otro_dato);

                    $custom["tipo"] = "app";
                    $custom["idmodulo"] = (string) "44";
                    $custom["titulo"] = "Nueva Reserva Club. ";
                    $custom["idseccion"] = "0";
                    $custom["iddetalle"] = "0";

                    if ($datos_empleado["Dispositivo"] == "iOS") {
                        $array_ios[] = $datos_empleado["Token"];
                    } elseif ($datos_empleado["Dispositivo"] == "Android") {
                    $array_android[] = $datos_empleado["Token"];
                }

                //SIMUtil::sendAlerts($users, $message, $custom, $TipoApp);

                //Guardo el log
                $sql_log = $dbo->query("Insert Into LogNotificacion (IDNotificacionesGenerales	, IDUsuario, IDClub, Token, Dispositivo, Fecha,App,Titulo,Mensaje) Values ('', '$datos_empleado[IDUsuario]','" . $IDClub . "','USUARIO: $datos_empleado[Token]','$datos_empleado[Dispositivo]',NOW(),'Empleado','Nueva Reserva','" . utf8_decode($message) . "')");
            endif;
            endwhile;

            if (count($array_android) > 0 || count($array_ios) > 0) {
                $resp = SIMUtil::sendAlerts_V2($users, $message, $custom, $TipoApp, $array_android, $array_ios, $IDClub);
            }

            //Consulto si el auxiliar tiene un usuario y clave dentro del app de empleados
            if (!empty($datos_reserva["IDAuxiliar"])) {
                $array_aux = explode(",", $datos_reserva["IDAuxiliar"]);
                foreach ($array_aux as $id_auxiliar) {
                    if ((int) $id_auxiliar > 0) {
                        $array_ios = array();
                        $array_android = array();
                        $sql_elemento_aux = "Select * From UsuarioAuxiliar Where IDAuxiliar = '" . $id_auxiliar . "'  ";
                        $result_aux = $dbo->query($sql_elemento_aux);
                        while ($row_aux = $dbo->fetchArray($result_aux)):
                            //Consulto los datos del empleado
                            $datos_empleado = $dbo->fetchAll("Usuario", " IDUsuario = '" . $row_aux["IDUsuario"] . "' ", "array");

                            if (!empty($datos_empleado["IDUsuario"]) && $datos_empleado["Token"] != "2byte" && $datos_empleado["Token"] != "" && $datos_empleado["PushReserva"] != "N"):

                                $users = array(array("id" => $datos_empleado["IDUsuario"],
                                    "idclub" => $IDClub,
                                    "registration_key" => $datos_empleado["Token"],
                                    "deviceType" => $datos_empleado["Dispositivo"]),
                                );

                                $Elemento = $dbo->getFields("ServicioElemento", "Nombre", "IDServicioElemento = '" . $datos_reserva["IDServicioElemento"] . "'");

                                if ($IDClub != 10) {
                                    $otro_dato = $datos_socio_reserva["Predio"];
                                }

                                $message = "Ha sido reservado para el día " . $datos_reserva["Fecha"] . " " . $datos_reserva["Hora"] . "(" . $Elemento . ") por: " . utf8_encode($datos_socio_reserva["Nombre"] . " " . $datos_socio_reserva["Apellido"] . " " . $otro_dato);

                                $custom["tipo"] = "app";
                                $custom["idmodulo"] = (string) "44";
                                $custom["titulo"] = "Nueva Reserva Club.";
                                $custom["idseccion"] = "0";
                                $custom["iddetalle"] = "0";

                                if ($datos_empleado["Dispositivo"] == "iOS") {
                                    $array_ios[] = $datos_empleado["Token"];
                                } elseif ($datos_empleado["Dispositivo"] == "Android") {
                                $array_android[] = $datos_empleado["Token"];
                            }

                            //SIMUtil::sendAlerts($users, $message, $custom, $TipoApp);

                            //Guardo el log
                            $sql_log = $dbo->query("Insert Into LogNotificacion (IDNotificacionesGenerales	, IDUsuario, IDClub, Token, Dispositivo, Fecha, FechaReserva, App,Titulo,Mensaje) Values ('', '$datos_empleado[IDUsuario]','" . $IDClub . "','USUARIO: $datos_empleado[Token]','$datos_empleado[Dispositivo]',NOW(),'" . $datos_reserva["Fecha"] . " " . $datos_reserva["Hora"] . "','Empleado','Nueva Reserva','" . utf8_decode($message) . "')");
                        endif;
                        endwhile;
                        if (count($array_android) > 0 || count($array_ios) > 0) {
                            $resp = SIMUtil::sendAlerts_V2($users, $message, $custom, $TipoApp, $array_android, $array_ios, $IDClub);
                        }

                    }
                }
            }

        }
    }

    public function push_notifica_reserva_elimina($IDClub, $IDReservaGeneral, $TipoApp)
    {
        $dbo = &SIMDB::get();

        if (!empty($IDClub) && !empty($IDReservaGeneral)) {

            $datos_reserva = $dbo->fetchAll("ReservaGeneralEliminada", " IDReservaGeneral = '" . $IDReservaGeneral . "' ", "array");

            if (!empty($datos_reserva["IDSocioBeneficiario"])) {
                $IDSocioReserva = $datos_reserva["IDSocioBeneficiario"];
            } else {
                $IDSocioReserva = $datos_reserva["IDSocio"];
            }

            $datos_socio_reserva = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocioReserva . "' ", "array");

            //Consulto si el elemento tiene un usuario y clave dentro del app de empleados
            $sql_elemento_usuario = "Select * From UsuarioServicioElemento Where IDServicioElemento = '" . $datos_reserva["IDServicioElemento"] . "'  ";
            $result_elemento_usuario = $dbo->query($sql_elemento_usuario);
            while ($row_elemento_usuario = $dbo->fetchArray($result_elemento_usuario)):
                //Consulto los datos del empleado
                $datos_empleado = $dbo->fetchAll("Usuario", " IDUsuario = '" . $row_elemento_usuario["IDUsuario"] . "' ", "array");

                if (!empty($datos_empleado["IDUsuario"]) && $datos_empleado["Token"] != "2byte" && $datos_empleado["Token"] != "" && $datos_empleado["PushReserva"] != "N"):

                    $users = array(array("id" => $datos_empleado["IDUsuario"],
                        "idclub" => $IDClub,
                        "registration_key" => $datos_empleado["Token"],
                        "deviceType" => $datos_empleado["Dispositivo"]),
                    );
                    $message = "Reserva Eliminada para el dia " . $datos_reserva["Fecha"] . " " . $datos_reserva["Hora"] . " por: " . utf8_encode($datos_socio_reserva["Nombre"] . " " . $datos_socio_reserva["Apellido"]);

                    $custom["tipo"] = "app";
                    $custom["idmodulo"] = (string) "29";
                    $custom["titulo"] = "Eliminacion Reserva Club.";
                    $custom["idseccion"] = "0";
                    $custom["iddetalle"] = "0";

                    if ($datos_empleado["Dispositivo"] == "iOS") {
                        $array_ios[] = $datos_empleado["Token"];
                    } elseif ($datos_empleado["Dispositivo"] == "Android") {
                    $array_android[] = $datos_empleado["Token"];
                }

                //SIMUtil::sendAlerts($users, $message, $custom, $TipoApp);
                //Guardo el log
                $sql_log = $dbo->query("Insert Into LogNotificacion (IDNotificacionesGenerales	, IDUsuario, IDClub, Token, Dispositivo, Fecha,App,Titulo,Mensaje) Values ('', '$datos_empleado[IDUsuario]','" . $IDClub . "','USUARIO: $datos_empleado[Token]','$datos_empleado[Dispositivo]',NOW(),'Empleado','Nueva Reserva','" . utf8_decode($message) . "')");
            endif;
            endwhile;

            if (count($array_android) > 0 || count($array_ios) > 0) {
                $resp = SIMUtil::sendAlerts_V2($users, $message, $custom, $TipoApp, $array_android, $array_ios, $IDClub);
            }

            //Consulto si el auxiliar tiene un usuario y clave dentro del app de empleados
            if (!empty($datos_reserva["IDAuxiliar"])) {
                $array_aux = explode(",", $datos_reserva["IDAuxiliar"]);
                foreach ($array_aux as $id_auxiliar) {
                    if ((int) $id_auxiliar > 0) {
                        $array_ios = array();
                        $array_android = array();
                        $sql_elemento_aux = "Select * From UsuarioAuxiliar Where IDAuxiliar = '" . $id_auxiliar . "'  ";
                        $result_aux = $dbo->query($sql_elemento_aux);
                        while ($row_aux = $dbo->fetchArray($result_aux)):
                            //Consulto los datos del empleado
                            $datos_empleado = $dbo->fetchAll("Usuario", " IDUsuario = '" . $row_aux["IDUsuario"] . "' ", "array");

                            if (!empty($datos_empleado["IDUsuario"]) && $datos_empleado["Token"] != "2byte" && $datos_empleado["Token"] != "" && $datos_empleado["PushReserva"] != "N"):

                                $users = array(array("id" => $datos_empleado["IDUsuario"],
                                    "idclub" => $IDClub,
                                    "registration_key" => $datos_empleado["Token"],
                                    "deviceType" => $datos_empleado["Dispositivo"]),
                                );

                                $Elemento = $dbo->getFields("ServicioElemento", "Nombre", "IDServicioElemento = '" . $datos_reserva["IDServicioElemento"] . "'");
                                $message = "Reserva Eliminada para el dia " . $datos_reserva["Fecha"] . " " . $datos_reserva["Hora"] . " por: " . utf8_encode($datos_socio_reserva["Nombre"] . " " . $datos_socio_reserva["Apellido"]);

                                $custom["tipo"] = "app";
                                $custom["idmodulo"] = (string) "44";
                                $custom["titulo"] = "Nueva Reserva Club.";
                                $custom["idseccion"] = "0";
                                $custom["iddetalle"] = "0";

                                if ($datos_empleado["Dispositivo"] == "iOS") {
                                    $array_ios[] = $datos_empleado["Token"];
                                } elseif ($datos_empleado["Dispositivo"] == "Android") {
                                $array_android[] = $datos_empleado["Token"];
                            }

                            //SIMUtil::sendAlerts($users, $message, $custom, $TipoApp);
                            //SIMUtil::sendAlerts_V2($users, $message, $custom,$TipoApp,$array_android,$array_ios,$IDClub);

                            //SIMUtil::sendAlerts($users, $message, $custom, $TipoApp);

                            //Guardo el log
                            $fecha_hora_r = $datos_reserva[Fecha] . ' ' . $datos_reserva[Hora];
                            $sql_log = $dbo->query("Insert Into LogNotificacion (IDNotificacionesGenerales	, IDUsuario, IDClub, Token, Dispositivo, Fecha,FechaReserva, App,Titulo,Mensaje) Values ('', '$datos_empleado[IDUsuario]','" . $IDClub . "','USUARIO: $datos_empleado[Token]','$datos_empleado[Dispositivo]',NOW(),'$fecha_hora_r','Empleado','Reserva Eliminada','" . $message . "')");
                        endif;
                        endwhile;

                        if (count($array_android) > 0 || count($array_ios) > 0) {
                            $resp = SIMUtil::sendAlerts_V2($users, $message, $custom, $TipoApp, $array_android, $array_ios, $IDClub);
                        }

                    }
                }
            }

        }
    }

    public function push_notifica_reserva_socio($IDClub, $IDSocio, $Mensaje)
    {
        $dbo = &SIMDB::get();

        if (!empty($IDClub) && !empty($IDSocio) && !empty($Mensaje)) {

            $datos_socio_reserva = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");

            if (!empty($datos_socio_reserva["IDSocio"]) && $datos_socio_reserva["Token"] != "2byte" && $datos_socio_reserva["Token"] != ""):

                $users = array(array("id" => $datos_socio_reserva["IDSocio"],
                    "idclub" => $IDClub,
                    "registration_key" => $datos_socio_reserva["Token"],
                    "deviceType" => $datos_socio_reserva["Dispositivo"]),
                );
                $message = $Mensaje;

                $custom["tipo"] = "app";
                $custom["idmodulo"] = (string) "29";
                $custom["titulo"] = "Reservas Club.";
                $custom["idseccion"] = "0";
                $custom["iddetalle"] = "0";

                if ($datos_socio_reserva["Dispositivo"] == "iOS") {
                    $array_ios[] = $datos_socio_reserva["Token"];
                } elseif ($datos_socio_reserva["Dispositivo"] == "Android") {
                $array_android[] = $datos_socio_reserva["Token"];
            }

            SIMUtil::sendAlerts_V2($users, $message, $custom, $TipoApp, $array_android, $array_ios, $IDClub);

            //SIMUtil::sendAlerts($users, $message, $custom, $TipoApp);

            //Guardo el log
            $sql_log = $dbo->query("Insert Into LogNotificacion (IDNotificacionesGenerales	, IDSocio, IDClub, Token, Dispositivo, Fecha) Values ('29', '$row_elemento_usuario[IDUsuario]','29','USUARIO: $datos_empleado[Token]','$datos_empleado[Dispositivo]',NOW())");

            endif;

        }
    }

    public function push_notifica_reserva_elimina_socio($IDClub, $IDReservaGeneral, $Razon)
    {
        $dbo = &SIMDB::get();

        if (!empty($IDClub) && !empty($IDReservaGeneral)) {

            $datos_reserva = $dbo->fetchAll("ReservaGeneralEliminada", " IDReservaGeneral = '" . $IDReservaGeneral . "' ", "array");
            $datos_socio_reserva = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_reserva["IDSocio"] . "' ", "array");

            if (!empty($datos_socio_reserva["IDSocio"]) && $datos_socio_reserva["Token"] != "2byte" && $datos_socio_reserva["Token"] != ""):

                $users = array(array("id" => $datos_socio_reserva["IDSocio"],
                    "idclub" => $IDClub,
                    "registration_key" => $datos_socio_reserva["Token"],
                    "deviceType" => $datos_socio_reserva["Dispositivo"]),
                );
                $message = "La reserva para el dia " . $datos_reserva["Fecha"] . " " . $datos_reserva["Hora"] . " fue eliminada por: " . utf8_encode($Razon);

                $custom["tipo"] = "app";
                $custom["idmodulo"] = (string) "29";
                $custom["titulo"] = "Eliminacion Reserva Club.";
                $custom["idseccion"] = "0";
                $custom["iddetalle"] = "0";

                if ($datos_socio_reserva["Dispositivo"] == "iOS") {
                    $array_ios[] = $datos_socio_reserva["Token"];
                } elseif ($datos_socio_reserva["Dispositivo"] == "Android") {
                $array_android[] = $datos_socio_reserva["Token"];
            }

            SIMUtil::sendAlerts_V2($users, $message, $custom, $TipoApp, $array_android, $array_ios, $IDClub);

            //SIMUtil::sendAlerts($users, $message, $custom, $TipoApp);

            //Guardo el log
            $sql_log = $dbo->query("Insert Into LogNotificacion (IDNotificacionesGenerales	, IDSocio, IDClub, Token, Dispositivo, Fecha) Values ('29', '$row_elemento_usuario[IDUsuario]','29','USUARIO: $datos_empleado[Token]','$datos_empleado[Dispositivo]',NOW())");

            endif;

        }
    }

    public function push_notifica_codigo_pago($IDReservaGeneral)
    {
        $dbo = &SIMDB::get();

        if (!empty($IDReservaGeneral)) {
            $datos_reserva = $dbo->fetchAll("ReservaGeneralEliminada", " IDReservaGeneral = '" . $IDReservaGeneral . "' ", "array");
            $datos_socio_reserva = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_reserva["IDSocio"] . "' ", "array");
            $valor = $dbo->getFields("PagosWeb", "value", "extra1 = " . $IDReservaGeneral);

            //Si fue pagado por payu y fue exitoso o si fue generado por el admin de liga o si fue pagado con un bono
            //if($datos_reserva["PagoPayu"]=="S" && ($datos_reserva["EstadoTransaccion"]==4 || $datos_reserva["EstadoTransaccion"]=="A") || ($datos_reserva["IDClub"]==28 && $datos_reserva["UsuarioTrCr"]=="Starter" && (int)$datos_reserva["IDUsuarioReserva"]>0)):
            if (($datos_reserva["PagoPayu"] == "S" && ($datos_reserva["EstadoTransaccion"] == 4 || $datos_reserva["EstadoTransaccion"] == "A" || $datos_reserva["EstadoTransaccion"] == "Aprobada" ))
                || ($datos_reserva["IDTipoPago"] == 2) || ($datos_reserva["IDClub"] == 28 && $datos_reserva["UsuarioTrCr"] == "Starter" && (int) $datos_reserva["IDUsuarioReserva"] > 0)):

                //generar un codigo valido para redimir
                $codigo_redimir = self::generarPassword("6");
                //Inserto el codigo
                $sql_codigo = "INSERT Into ClubCodigoPago (IDClub, IDSocio, Codigo, Disponible, IDServicio, Valor, UsuarioTrCr, FechaTrCr ) Values ('" . $datos_reserva["IDClub"] . "','" . $datos_reserva["IDSocio"] . "', '" . $codigo_redimir . "','S','" . $datos_reserva["IDServicio"] . "','" . $valor . "','Automatico de eliminacion',NOW())";
                $dbo->query($sql_codigo);
                //Envio push con Codigo
                $users = array(array("id" => $datos_socio_reserva["IDSocio"],
                    "idclub" => $datos_reserva["IDClub"],
                    "registration_key" => $datos_socio_reserva["Token"],
                    "deviceType" => $datos_socio_reserva["Dispositivo"]),
                );
                $message = "Se genero el siguiente codigo para que lo pueda utilizar en su proxima reserva: " . $codigo_redimir;

                $custom["tipo"] = "app";
                $custom["idmodulo"] = (string) "2";
                $custom["titulo"] = "Codigo Reserva";
                $custom["idseccion"] = "0";
                $custom["iddetalle"] = "0";

                if ($datos_socio_reserva["Dispositivo"] == "iOS") {
                    $array_ios[] = $datos_socio_reserva["Token"];
                } elseif ($datos_socio_reserva["Dispositivo"] == "Android") {
                $array_android[] = $datos_socio_reserva["Token"];
            }

            //Guardo el log
            $sql_log = $dbo->query("INSERT INTO LogNotificacion (IDNotificacionesGenerales	, IDSocio, IDClub, Token, Dispositivo, Fecha, App, Tipo, Titulo, Mensaje, Modulo, IDDetalle) Values ('" . $id . "', '" . $datos_socio_reserva["IDSocio"] . "','" . $datos_socio_reserva["IDClub"] . "','" . $datos_socio_reserva["Token"] . "','" . $r_socios["Dispositivo"] . "',NOW(),'Socio','" . $custom["tipo"] . "', '" . $custom["titulo"] . "', '" . $message . "', '" . $custom["idseccion"] . "', '" . $custom["iddetalle"] . "')");



            SIMUtil::sendAlerts_V2($users, $message, $custom, $TipoApp, $array_android, $array_ios, $datos_reserva["IDClub"]);

            //SIMUtil::sendAlerts($users, $message, $custom, $TipoApp);

            //Si el club tiene habilitado sms
            $SMSClub = $dbo->getFields("Club", "PermiteSMS", "IDClub = '" . $datos_reserva["IDClub"] . "'");
            if ($SMSClub == "S" && !empty($row_lista_espera["Celular"]) && strlen($row_lista_espera["Celular"]) == 10):
                //$resultadosms = SIMWebServiceSMS::enviar_sms($row_lista_espera["Celular"],$message);
            endif;



            endif;

        }

        return $codigo_redimir;
    }

    public function push_notifica_libera_reserva($IDClub, $IDReservaGeneral)
    {
        $dbo = &SIMDB::get();

        if (!empty($IDClub) && !empty($IDReservaGeneral)) {

            $datos_reserva = $dbo->fetchAll("ReservaGeneralEliminada", " IDReservaGeneral = '" . $IDReservaGeneral . "' ", "array");

            $datos_servicio = $dbo->fetchAll("Servicio", " IDServicio = '" . $datos_reserva["IDServicio"] . "' ", "array");
            $NombrePersonalizado = $dbo->getFields("ServicioClub", "TituloServicio", "IDServicioMaestro = '" . $datos_servicio["IDServicioMaestro"] . "' and IDClub = '" . $IDClub . "'");
            if (empty($NombrePersonalizado)) {
                $datos_servicio_maestro = $dbo->fetchAll("ServicioMaestro", " IDServicioMaestro = '" . $datos_servicio["IDServicioMaestro"] . "' ", "array");
                $NombrePersonalizado = $datos_servicio_maestro["Nombre"];
            }

            $datos_elemento = $dbo->fetchAll("ServicioElemento", " IDServicioElemento = '" . $datos_reserva["IDServicioElemento"] . "' ", "array");

            //Consulto los socios que se inscribieron a la lista de espera en este periodo
            /*
            $sql_lista_espera = "Select *
            From ListaEspera
            Where IDClub = '".$IDClub."' and FechaInicio >= '".$datos_reserva["Fecha"]."' and FechaFin <= '".$datos_reserva["Fecha"]."' and
            HoraInicio >= '".$datos_reserva["Hora"]."' and HoraFin <= '".$datos_reserva["Hora"]."'
            and IDServicio = '".$datos_reserva["IDServicio"]."' and Tipo = 'Reserva'";
             */
            $sql_lista_espera = "Select *
										 From ListaEspera
										 Where IDClub = '" . $IDClub . "' and FechaInicio >= '" . $datos_reserva["Fecha"] . "' and FechaFin <= '" . $datos_reserva["Fecha"] . "' and
										 HoraInicio >= '" . $datos_reserva["Hora"] . "' and HoraFin <= '" . $datos_reserva["Hora"] . "'
										 and IDServicio = '" . $datos_reserva["IDServicio"] . "' and Tipo = 'Reserva'";
            $result_lista_espera = $dbo->query($sql_lista_espera);
            while ($row_lista_espera = $dbo->fetchArray($result_lista_espera)):
                if ((int) $row_lista_espera["IDServicioElemento"] >= 0):
                    $envia_notif = "S";
                elseif ((int) $row_lista_espera["IDServicioElemento"] == $datos_reserva["IDServicioElemento"]):
                    $envia_notif = "S";
                else:
                    $envia_notif = "N";
                endif;

                if ($envia_notif == "S"):

                    //Consulto los datos del socio
                    $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $row_lista_espera["IDSocio"] . "' ", "array");

                    if (!empty($datos_socio["IDSocio"]) && $datos_socio["Token"] != "2byte" && $datos_socio["Token"] != ""):

                        $users = array(array("id" => $datos_socio["IDSocio"],
                            "idclub" => $IDClub,
                            "registration_key" => $datos_socio["Token"],
                            "deviceType" => $datos_socio["Dispositivo"]),
                        );

                        $message = "Se libero una reserva  para el dia " . $datos_reserva["Fecha"] . " " . $datos_reserva["Hora"] . " Servicio: " . $NombrePersonalizado . " " . $datos_elemento["Nombre"] . "(Lista espera Mi Club)";

                        $custom["tipo"] = "app";
                        $custom["idmodulo"] = (string) "2";
                        $custom["titulo"] = "Reserva " . $NombrePersonalizado . " " . $datos_elemento["Nombre"] . " liberada";
                        $custom["idseccion"] = "0";
                        $custom["iddetalle"] = "0";

                        if ($datos_socio["Dispositivo"] == "iOS") {
                            $array_ios[] = $datos_socio["Token"];
                        } elseif ($datos_socio["Dispositivo"] == "Android") {
                        $array_android[] = $datos_socio["Token"];
                    }

                    //SIMUtil::sendAlerts($users, $message, $custom, $TipoApp);

                    //Si el club tiene habilitado sms
                    $SMSClub = $dbo->getFields("Club", "PermiteSMS", "IDClub = '" . $IDClub . "'");
                    if ($SMSClub == "S" && !empty($row_lista_espera["Celular"]) && strlen($row_lista_espera["Celular"]) == 10):
                        $resultadosms = SIMWebServiceSMS::enviar_sms($row_lista_espera["Celular"], $message);
                    endif;

                    //Guardo el log
                    $sql_log = $dbo->query("INSERT INTO LogNotificacion (IDNotificacionesGenerales	, IDSocio, IDClub, Token, Dispositivo, Fecha, App, Tipo,Titulo, Mensaje, Modulo)
																					Values ('', '$row_lista_espera[IDSocio]','" . $datos_reserva["IDClub"] . "','USUARIO: $datos_socio[Token]','$datos_socio[Dispositivo]',NOW(), 'Socio','General','" . $custom["titulo"] . "','" . $message . "','2')");
                endif;

            endif;

            endwhile;

            if (count($array_android) > 0 || count($array_ios) > 0) {
                $resp = SIMUtil::sendAlerts_V2($users, $message, $custom, $TipoApp, $array_android, $array_ios, $IDClub);
            }

        }
    }

    public function push_notifica_libera_reserva_auxiliar($IDClub, $IDReservaGeneral)
    {
        $dbo = &SIMDB::get();

        if (!empty($IDClub) && !empty($IDReservaGeneral)) {

            $datos_reserva = $dbo->fetchAll("ReservaGeneralEliminada", " IDReservaGeneral = '" . $IDReservaGeneral . "' ", "array");
            //Consulto los socios que se inscribieron a la lista de espera en este periodo
            $sql_lista_espera = "Select *
										 From ListaEspera
										 Where IDClub = '" . $IDClub . "' and FechaInicio >= '" . $datos_reserva["Fecha"] . "' and FechaFin <= '" . $datos_reserva["Fecha"] . "' and
										 HoraInicio >= '" . $datos_reserva["Hora"] . "' and HoraFin <= '" . $datos_reserva["Hora"] . "'
										 and IDServicio = '" . $datos_reserva["IDServicio"] . "' and Tipo = 'Auxiliar'";
            $result_lista_espera = $dbo->query($sql_lista_espera);
            while ($row_lista_espera = $dbo->fetchArray($result_lista_espera)):

                $id_auxiliar_buscar = $datos_reserva["IDAuxiliar"] . ",";

                if ((int) $row_lista_espera["IDAuxiliar"] == 0):
                    $envia_notif = "S";
                elseif ((int) $row_lista_espera["IDAuxiliar"] == $id_auxiliar_buscar):
                    $envia_notif = "S";
                else:
                    $envia_notif = "N";
                endif;

                if ($envia_notif == "S"):

                    //Consulto los datos del socio
                    $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $row_lista_espera["IDSocio"] . "' ", "array");

                    if (!empty($datos_socio["IDSocio"]) && $datos_socio["Token"] != "2byte" && $datos_socio["Token"] != ""):

                        $users = array(array("id" => $datos_socio["IDSocio"],
                            "idclub" => $IDClub,
                            "registration_key" => $datos_socio["Token"],
                            "deviceType" => $datos_socio["Dispositivo"]),
                        );
                        $message = "Se libero un profesor/auxiliar  para el dia " . $datos_reserva["Fecha"] . " " . $datos_reserva["Hora"] . "  (Lista espera Mi Club)";

                        $custom["tipo"] = "app";
                        $custom["idmodulo"] = (string) "2";
                        $custom["titulo"] = "Auxiliar Liberado Club.";
                        $custom["idseccion"] = "0";
                        $custom["iddetalle"] = "0";

                        if ($datos_socio["Dispositivo"] == "iOS") {
                            $array_ios[] = $datos_socio["Token"];
                        } elseif ($datos_socio["Dispositivo"] == "Android") {
                        $array_android[] = $datos_socio["Token"];
                    }

                    //SIMUtil::sendAlerts($users, $message, $custom, $TipoApp);

                    //Si el club tiene habilitado sms
                    $SMSClub = $dbo->getFields("Club", "PermiteSMS", "IDClub = '" . $IDClub . "'");
                    if ($SMSClub == "S" && !empty($row_lista_espera["Celular"]) && strlen($row_lista_espera["Celular"]) == 10):
                        $resultadosms = SIMWebServiceSMS::enviar_sms($row_lista_espera["Celular"], $message);
                    endif;

                    //Guardo el log
                    $sql_log = $dbo->query("Insert Into LogNotificacion (IDNotificacionesGenerales	, IDSocio, IDClub, Token, Dispositivo, Fecha) Values ('29', '$datos_socio[IDUsuario]','29','USUARIO: $datos_socio[Token]','$datos_socio[Dispositivo]',NOW())");
                endif;

            endif;

            endwhile;

            if (count($array_android) > 0 || count($array_ios) > 0) {
                $resp = SIMUtil::sendAlerts_V2($users, $message, $custom, $TipoApp, $array_android, $array_ios, $IDClub);
            }

        }
    }

    public function notificar_lista_espera_hotel($IDClub, $FechaInicio, $FechaFin)
    {
        $dbo = &SIMDB::get();

        if (!empty($IDClub) && !empty($FechaInicio)) {

            //Consulto los socios que se inscribieron a la lista de espera en este periodo
            $sql_lista_espera = "Select *
										 From ListaEspera
										 Where IDClub = '" . $IDClub . "'
										 		and FechaInicio <= '" . $FechaInicio . "'
												and FechaFin >= '" . $FechaFin . "'
										 		and Tipo = 'Hotel'";

            $result_lista_espera = $dbo->query($sql_lista_espera);
            while ($row_lista_espera = $dbo->fetchArray($result_lista_espera)):

                //Consulto los datos del socio
                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $row_lista_espera["IDSocio"] . "' ", "array");

                if (!empty($datos_socio["IDSocio"]) && $datos_socio["Token"] != "2byte" && $datos_socio["Token"] != ""):

                    $users = array(array("id" => $datos_socio["IDSocio"],
                        "idclub" => $IDClub,
                        "registration_key" => $datos_socio["Token"],
                        "deviceType" => $datos_socio["Dispositivo"]),
                    );
                    $message = "Se libero una habitación  para el dia " . $FechaInicio . "  (Lista espera Mi Club)";

                    $custom["tipo"] = "app";
                    $custom["idmodulo"] = (string) "43";
                    $custom["titulo"] = "Habitacion Libre.";
                    $custom["idseccion"] = "";
                    $custom["iddetalle"] = "";

                    if ($datos_socio["Dispositivo"] == "iOS") {
                        $array_ios[] = $datos_socio["Token"];
                    } elseif ($datos_socio["Dispositivo"] == "Android") {
                    $array_android[] = $datos_socio["Token"];
                }

                //SIMUtil::sendAlerts($users, $message, $custom, $TipoApp);

                //Si el club tiene habilitado sms
                $SMSClub = $dbo->getFields("Club", "PermiteSMS", "IDClub = '" . $IDClub . "'");
                if ($SMSClub == "S" && !empty($row_lista_espera["Celular"]) && strlen($row_lista_espera["Celular"]) == 10):
                    $resultadosms = SIMWebServiceSMS::enviar_sms($row_lista_espera["Celular"], $message);
                endif;

                //Guardo el log
                $sql_log = $dbo->query("Insert Into LogNotificacion (IDNotificacionesGenerales	, IDSocio, IDClub, Token, Dispositivo, Fecha) Values ('29', '$datos_socio[IDUsuario]','29','USUARIO: $datos_socio[Token]','$datos_socio[Dispositivo]',NOW())");

            endif;

            endwhile;

            if (count($array_android) > 0 || count($array_ios) > 0) {
                $resp = SIMUtil::sendAlerts_V2($users, $message, $custom, $TipoApp, $array_android, $array_ios, $IDClub);
            }

            $respuesta["message"] = "ok";
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
            return $respuesta;

        } else {
            $respuesta["message"] = "Faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = $response;
            return $respuesta;

        }

    }

    /**
     * @param $vehicles
     * @param $message array message - type
     * @param $dateField
     * @param $date
     */
    public function sendAlerts($users, $message, $custom = array(), $TipoApp = "")
    {

        // Give up if no vehicles
        if (empty($users)) {
            return;
        }

        // Create message for each user
        $messages = array();
        $i = 0;
        foreach ($users as $user) {

            $messages[$i]["user"] = $user;
            $messages[$i]["message"] = $message;

            if (count($custom) > 0) {
                $messages[$i]["tipo"] = $custom["tipo"];
                $messages[$i]["idseccion"] = $custom["idseccion"];
                $messages[$i]["iddetalle"] = $custom["iddetalle"];
                $messages[$i]["idmodulo"] = $custom["idmodulo"];
                $messages[$i]["titulo"] = $custom["titulo"];
                $messages[$i]["link"] = $custom["link"];
                $messages[$i]["urllink"] = $custom["urllink"];
            } //end if

            $i++;
        }

        // Send notifications
        require_once "NotificationService.php";
        $notificationService = new NotificationService();
        $notificationService->sendNotifications($messages, $TipoApp);
    } //end funcion

    public static function sendAlerts_V2($users, $message, $custom = array(), $TipoApp = "", $array_android = array(), $array_ios = array(), $IDClub)
    {
        $dbo = &SIMDB::get();
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        if (count($array_ios) > 0) {
            require "NotificationServiceiOS.php";
        }

        if (count($array_android) > 0) {
            require "NotificationServiceAndroid.php";
        }
        // Send notifications
    } //end funcion

    public function consulta_ocupacion($Valores, $IDClub, $TipoConsulta, $TipoInvitado = "")
    {
        $dbo = &SIMDB::get();

        $fecha_hoy = date("Y-m-d") . " 00:00:00";
        //    $sql_vista="INSERT IGNORE INTO `LogAccesoVista` SELECT * FROM `LogAcceso` WHERE FechaTrCr >= '".$fecha_hoy."'";
        //$dbo->query($sql_vista);

        $tipo_invitado = "SELECT IDTipoInvitado, Nombre FROM TipoInvitado WHERE IDClub = '" . $IDClub . "' ";
        $r_tipo_invitado = $dbo->query($tipo_invitado);
        while ($row_tipo_invitado = $dbo->fetchArray($r_tipo_invitado)) {
            $array_tipo_inv[$row_tipo_invitado["IDTipoInvitado"]] = $row_tipo_invitado["Nombre"];

            $clasif_invitado = "SELECT IDClasificacionInvitado, IDTipoInvitado, Nombre FROM ClasificacionInvitado WHERE  IDTipoInvitado = '" . $row_tipo_invitado["IDTipoInvitado"] . "' ";
            $r_clasif_invitado = $dbo->query($clasif_invitado);
            while ($row_clasif_invitado = $dbo->fetchArray($r_clasif_invitado)) {
                $array_clasif_inv[$row_clasif_invitado["IDTipoInvitado"]] = $row_clasif_invitado["Nombre"];
            }
        }

        $sql_socios = "SELECT IDSocio,NumeroDocumento,Accion FROM Socio WHERE IDClub = '" . $IDClub . "'";
        $r_socios = $dbo->query($sql_socios);
        while ($row_socios = $dbo->fetchArray($r_socios)) {
            $array_socios[$row_socios["IDSocio"]] = $row_socios["NumeroDocumento"];
        }

        if (empty($Valores["FechaInicio"])):
            $Valores["FechaInicio"] = date("Y-m-d");
            $Valores["FechaFin"] = date("Y-m-d");
        endif;

        if (!empty($Valores["IDTipoInvitado"])):
            $condicion_busqueda .= " and IDTipoInvitado = '" . $Valores["IDTipoInvitado"] . "'";
        endif;

        if (!empty($Valores["FechaInicio"])):
            $condicion_fecha_ingreso .= " and FechaIngreso >= '" . $Valores["FechaInicio"] . " 00:00:00'";
            $condicion_fecha_salida .= " and FechaSalida >= '" . $Valores["FechaInicio"] . " 00:00:00'";
            //$condicion_fecha_ingreso_ocupacion  = " and FechaIngreso >= '".$Valores["FechaInicio"]." 00:00:00'";
            $condicion_fecha_ingreso_ocupacion = " and FechaIngreso <= '" . $Valores["FechaInicio"] . " 23:59:59'";
            $condicion_fecha_salida_ocupacion = " and L.FechaTrCr >= '" . $Valores["FechaInicio"] . " 00:00:00'";
            $condicion_fecha_salida_ocupacion2 = " and FechaTrCr >= '" . $Valores["FechaInicio"] . " 00:00:00'";
        endif;

        if (!empty($Valores["FechaFin"])):
            $condicion_fecha_ingreso .= " and FechaIngreso <= '" . $Valores["FechaFin"] . " 23:59:59'";
            $condicion_fecha_salida .= " and FechaSalida <= '" . $Valores["FechaFin"] . " 23:59:59'";
            //$condicion_fecha_ingreso_ocupacion  .= " and FechaIngreso <= '".$Valores["FechaFin"]." 23:59:59'";
            $condicion_fecha_salida_ocupacion .= " and L.FechaTrCr <= '" . $Valores["FechaFin"] . " 23:59:59'";
            $condicion_fecha_salida_ocupacion2 .= " and FechaTrCr <= '" . $Valores["FechaFin"] . " 23:59:59'";
        endif;

        //Consulto por Tipo
        $sql_tipo = "Select  Tipo  From LogAccesoVista Where Tipo <> '' and IDClub = '" . $IDClub . "' and Entrada = 'S' " . $condicion_fecha_ingreso_ocupacion . " " . $condicion_busqueda . " GROUP BY Tipo";
        //$sql_tipo = "Select  Tipo  From LogAccesoVista Where Tipo = 'Socio' and IDClub = '".$IDClub."' and Entrada = 'S' " .  $condicion_fecha_ingreso_ocupacion . " ". $condicion_busqueda . " GROUP BY Tipo";
        $r_tipo = $dbo->query($sql_tipo);
        while ($row_tipo = $dbo->fetchArray($r_tipo)) {

            //Ocupacion Actual
            $sql_ocupacion_actual = "Select  IDLogAcceso,IDInvitacion  From LogAccesoVista Where Tipo = '" . $row_tipo["Tipo"] . "' and IDClub = '" . $IDClub . "' and Entrada = 'S' " . $condicion_fecha_ingreso_ocupacion . " " . $condicion_busqueda . " and FechatrCr >= '2020-03-16' Order By IDLogAcceso Desc Limit 4000";

            //$sql_ocupacion_actual = "Select  IDLogAcceso,IDInvitacion  From LogAcceso Where Tipo <> '' and IDClub = '".$IDClub."' and Entrada = 'S' and IDInvitacion = 913" .  $condicion_fecha_ingreso_ocupacion . " ". $condicion_busqueda . " Order By IDLogAcceso Desc Limit 3000";
            $result_ocupacion_actual = $dbo->query($sql_ocupacion_actual);
            $cont = 0;
            while ($r_ocupacion_actual = $dbo->fetchArray($result_ocupacion_actual)):
                //echo "<br>" .$r_ocupacion_actual["IDLogAcceso"] . " - " . $r_ocupacion_actual["IDInvitacion"];

                //Verifico si el ultimo movimiento fue de salida para saber si ya salio del club
                $sql_salidaOLD = "Select IDLogAcceso, Tipo, IDInvitacion, Entrada, Salida
	            From LogAccesoVista
	              Where Tipo <> '' and IDClub = '" . $IDClub . "' and IDInvitacion = '" . $r_ocupacion_actual["IDInvitacion"] . "'" . $condicion_fecha_ingreso_ocupacion . " " . $condicion_busqueda . " Order by IDLogAcceso Desc Limit 1 ";

                $sql_salida = "Select IDLogAcceso, Tipo, IDInvitacion, Entrada, Salida
	            From LogAccesoVista
	              Where Tipo <> '' and IDClub = '" . $IDClub . "' and IDInvitacion = '" . $r_ocupacion_actual["IDInvitacion"] . "' Order by IDLogAcceso Desc Limit 1 ";

                $sql_salidaOLD2 = "Select L.IDLogAcceso, L.Tipo, L.IDInvitacion, L.Entrada, L.Salida, S.NumeroDocumento
	                    From LogAccesoVista L
	                    LEFT JOIN Socio S ON S.IDSocio = L.IDInvitacion
	                    Where  L.Tipo <> '' and L.IDClub = '" . $IDClub . "' and L.IDInvitacion = '" . $r_ocupacion_actual["IDInvitacion"] . "'" . $condicion_fecha_ingreso_ocupacion . " " . $condicion_busqueda . " Order by IDLogAcceso Desc Limit 1 ";

                $sql_salidaante = "Select L.IDLogAcceso, L.Tipo, L.IDInvitacion, L.Entrada, L.Salida, S.NumeroDocumento
	                    From LogAccesoVista L
	                    LEFT JOIN Socio S ON S.IDSocio = L.IDInvitacion
	                    Where  L.Tipo <> '' and L.IDClub = '" . $IDClub . "' and L.IDInvitacion = '" . $r_ocupacion_actual["IDInvitacion"] . "'" . $condicion_fecha_salida_ocupacion . " " . $condicion_busqueda . " Order by IDLogAcceso Desc Limit 1 ";

                $result_salida = $dbo->query($sql_salida);
                $row_salida = $dbo->fetchArray($result_salida);
                $tipo_salida = $row_salida["Tipo"];
                if ($tipo_salida == "Contratista"):
                    //   echo $cont++;

                    //        $IDInvitado=$dbo->getFields( "SocioAutorizacion" , "IDInvitado" , "IDSocioAutorizacion = '".$row_salida["IDInvitacion"]."'" );

                    //    $sql_datos_inv="SELECT IDTipoInvitado, IDClasificacionInvitado FROM Invitado WHERE IDInvitado = '".$IDInvitado."' LIMIT 1";
                    //        $r_datos_inv=$dbo->query($sql_datos_inv);

                    $r_datos_inv = $dbo->query("Select I.IDTipoInvitado,I.IDClasificacionInvitado FROM Invitado I JOIN  SocioAutorizacion S ON S.IDInvitado = I.IDInvitado
		                                   WHERE S.IDSocioAutorizacion =  '" . $row_salida["IDInvitacion"] . "'");
                    //      $datos_invitado=$dbo->fetchArray($r_invitado);

                    $row_datos_inv = $dbo->FetchArray($r_datos_inv);

                    switch ($TipoInvitado) {

                        case "ClasificacionInvitado":
                            //$IDTipoInvitado=$dbo->getFields( "Invitado" , "IDClasificacionInvitado" , "IDInvitado = '".$IDInvitado."'" );
                            $tipo_invitado = $dbo->getFields("ClasificacionInvitado", "Nombre", "IDClasificacionInvitado = '" . $row_datos_inv["IDClasificacionInvitado"] . "'");
                            if ($tipo_invitado == "") {
                                //selecciono el primer tipo que encuentre de esa clasificacion
                                $tipo_invitado = $array_clasif_inv[$row_datos_inv["IDTipoInvitado"]];
                            }

                            break;
                        default:
                            //$IDTipoInvitado=$dbo->getFields( "Invitado" , "IDTipoInvitado" , "IDInvitado = '".$IDInvitado."'" );
                            $tipo_invitado = $dbo->getFields("TipoInvitado", "Nombre", "IDTipoInvitado = '" . $row_datos_inv["IDTipoInvitado"] . "'");

                    }

                    //$IDTipoInvitado=$dbo->getFields( "Invitado" , "IDTipoInvitado" , "IDInvitado = '".$IDInvitado."'" );
                    //$tipo_invitado=$dbo->getFields( "TipoInvitado" , "Nombre" , "IDTipoInvitado = '".$IDTipoInvitado."'" );
                    if (!empty($tipo_invitado)) {
                        $tipo_salida = $tipo_invitado;
                    }

                endif;

                if ($row_salida["Salida"] != "S"):
                    /*
                    echo "<br>S".$row_salida["IDLogAcceso"]." TIP:".$row_salida["Tipo"];
                    echo "<br>" . $sql_salida;
                    print_r($row_salida);
                     */

                    if ($row_salida["IDLogAcceso"] == 5087850) {
                        //echo $sql_salida;
                        //print_r($row_salida);
                        //echo "SI";exit;
                    }

                    $documento = "";
                    // Guardo el Id del invitado o socio para no tenerlo en cuenta mas de una vez
                    //echo "<br>" . $row_salida["Tipo"];
                    switch ($row_salida["Tipo"]):
                case "Contratista":
                case "InvitadoSocio":

                    $r_invitado = $dbo->query("Select I.NumeroDocumento FROM Invitado I JOIN  SocioAutorizacion S ON S.IDInvitado = I.IDInvitado
		                                   WHERE S.IDSocioAutorizacion =  '" . $row_salida["IDInvitacion"] . "'");
                    $datos_invitado = $dbo->fetchArray($r_invitado);
                    //$total_socio_copropietario=$row_cop["Total"];
                    //    $IDInvitado=$dbo->getFields( "SocioAutorizacion" , "IDInvitado" , "IDSocioAutorizacion = '".$row_salida["IDInvitacion"]."'" );
                    //    $datos_invitado = $dbo->fetchAll( "Invitado", " IDInvitado = '" . $IDInvitado . "' ", "array" );
                    $documento = $datos_invitado["NumeroDocumento"];
                    break;
                case "InvitadoAcceso":
                    //      echo $cont++;
                    //echo "<br>".$row_salida["IDInvitacion"];

                    $r_invitado = $dbo->query("Select I.NumeroDocumento FROM Invitado I JOIN  SocioInvitadoEspecial S ON S.IDInvitado = I.IDInvitado
		                                   WHERE S.IDSocioInvitadoEspecial =  '" . $row_salida["IDInvitacion"] . "'");
                    $datos_invitado = $dbo->fetchArray($r_invitado);

                    //        $IDInvitado=$dbo->getFields( "SocioInvitadoEspecial" , "IDInvitado" , "IDSocioInvitadoEspecial = '".$row_salida["IDInvitacion"]."'" );
                    //        $datos_invitado = $dbo->fetchAll( "Invitado", " IDInvitado = '" . $IDInvitado . "' ", "array" );
                    $documento = $datos_invitado["NumeroDocumento"];
                    break;
                case "Socio":
                    //    $datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $row_salida["IDInvitacion"] . "' ", "array" );
                    //    $documento=$datos_socio["NumeroDocumento"];
                    //$documento = $row_salida["NumeroDocumento"];
                    $documento = $array_socios[$row_salida["IDInvitacion"]];
                    break;
                case "SocioInvitado":
                    $datos_socio_invitado = $dbo->fetchAll("SocioInvitado", " IDSocioInvitado = '" . $row_salida["IDInvitacion"] . "' ", "array");
                    $documento = $datos_socio_invitado["NumeroDocumento"];
                    break;
                    endswitch;

                    if (!in_array($documento, $array_documento)):
                        $array_id_adentro[] = $row_salida["IDLogAcceso"];
                        if ($TipoConsulta == "Totales"):
                            $array_adentro[$tipo_salida]++;
                        elseif ($TipoConsulta == "ID"):
                            $array_adentro[] = $row_salida["IDLogAcceso"];
                        endif;
                    endif;

                    $array_documento[] = $documento;

                endif;

            endwhile;
            //FIN Ocupacion actual

        }

        $id_log_adentro = implode(",", $array_id_adentro);
        $sql_ocp = "INSERT INTO SocioOcupacion (IDClub,Fecha,Socios,Otros,IDLogAcceso,UsuarioTrCr,FechaTrCr)
          VALUES ('" . $_GET["IDClub"] . "',NOW(),'','','" . $id_log_adentro . "','Cron',NOW())";
        $dbo->query($sql_ocp);

        //print_r($array_adentro);
        //exit;

        return $array_adentro;

    } // End function

    public function calcular_tarifa($IDClub, $IDSocio, $IDServicio, $Fecha, $Hora, $IDElemento, $IDReservaGeneral = "", $IDTipoReserva)
    {

            $dbo = &SIMDB::get();
            /*
            $SalitreDiurno = 21000;
            $SalitreNocturno = 24000;
            $SalitreLibreCarne = 21000;
            $SalitreLibreEscuela = 19600;
            $SalitreLibreValle = 19600;

            $CampinPolvoDiurno= 21000;
            $CampinPolvoNocturno = 24000;
            $CampinPolvoCarne = 20000;
            $CampinPolvoAlumno = 21000;
            $CampinSinteticaDiurno = 18000;
            $CampinSinteticaNocturno = 21000;
            $CampinSinteticaCarne = 21000;
            $CampinSinteticaEscuela = 21000;

            $PqNacionalDiurno = 19800;
            $PqNacionalAlumno = 19000;
            $PqNacionalCarne = 19000;
             */

            $SalitreDiurno = $dbo->getFields("PreciosReservas", "Valor", "IDServicio = '2660' AND Nombre = 'Diurno'");
            $SalitreNocturno = $dbo->getFields("PreciosReservas", "Valor", "IDServicio = '2660' AND Nombre = 'Nocturno'");
            $SalitreLibreCarne = $dbo->getFields("PreciosReservas", "Valor", "IDServicio = '2660' AND Nombre = 'Libre Carnet'");
            $SalitreLibreEscuela = $dbo->getFields("PreciosReservas", "Valor", "IDServicio = '2660' AND Nombre = 'Libre Escuela'");
            $SalitreLibreValle = $dbo->getFields("PreciosReservas", "Valor", "IDServicio = '2660' AND Nombre = 'Libre Valle'");

            $CampinPolvoDiurno = $dbo->getFields("PreciosReservas", "Valor", "IDServicio = '2661' AND Nombre = 'Polvo Diurno'");
            $CampinPolvoNocturno = $dbo->getFields("PreciosReservas", "Valor", "IDServicio = '2661' AND Nombre = 'Polvo Nocturno'");
            $CampinPolvoCarne = $dbo->getFields("PreciosReservas", "Valor", "IDServicio = '2661' AND Nombre = 'Polvo Carnet'");
            $CampinPolvoAlumno = $dbo->getFields("PreciosReservas", "Valor", "IDServicio = '2661' AND Nombre = 'Polvo Alumno'");
            $CampinSinteticaDiurno = $dbo->getFields("PreciosReservas", "Valor", "IDServicio = '2661' AND Nombre = 'Sintética Diurno'");
            $CampinSinteticaNocturno = $dbo->getFields("PreciosReservas", "Valor", "IDServicio = '2661' AND Nombre = 'Sintética Nocturno'");
            $CampinSinteticaCarne = $dbo->getFields("PreciosReservas", "Valor", "IDServicio = '2661' AND Nombre = 'Sintética Carnet'");
            $CampinSinteticaEscuela = $dbo->getFields("PreciosReservas", "Valor", "IDServicio = '2661' AND Nombre = 'Sintética Escuelo'");

            $PqNacionalDiurno = $dbo->getFields("PreciosReservas", "Valor", "IDServicio = '2662' AND Nombre = 'Diurno'");
            $PqNacionalAlumno = $dbo->getFields("PreciosReservas", "Valor", "IDServicio = '2662' AND Nombre = 'Alumno'");
            $PqNacionalCarne = $dbo->getFields("PreciosReservas", "Valor", "IDServicio = '2662' AND Nombre = 'Carnet'");

            $HoraReserva = strtotime($Fecha . " " . $Hora);
            //Verifico si la hora es Diurna o nocturna
            $InicioDiurno = strtotime($Fecha . '06:00:00');
            $FinDiurno = strtotime($Fecha . '17:00:00');
            $diurno = ($HoraReserva >= $InicioDiurno && $HoraReserva <= $FinDiurno) ? 'S' : 'N';
            //Fin Verifico si la hora es Diurna o nocturna

            //Verifico si la hora es Valle y esta entre semana
            $dia_semana_reserva = date("w", strtotime($Fecha));
            if ($dia_semana_reserva) {
                $InicioValle = strtotime($Fecha . '09:00:00');
            }

            $FinValle = strtotime($Fecha . '16:00:00');
            $horavalle = ($HoraReserva >= $InicioValle && $HoraReserva <= $FinValle && $dia_semana_reserva != 0 && $dia_semana_reserva != 6) ? 'S' : 'N';

            //verifico los festivos
            if ($Fecha == "2020-12-25" || $Fecha == "2021-01-01" || $Fecha == "2021-01-11" || $Fecha == "2021-03-22" || $Fecha == "2021-04-01" || $Fecha == "2021-04-02" ||
                $Fecha == "2021-05-01" || $Fecha == "2021-05-17" || $Fecha == "2021-06-07" || $Fecha == "2021-06-14" || $Fecha == "2021-07-05" ||
                $Fecha == "2021-07-20" || $Fecha == "2021-08-07" || $Fecha == "2020-08-16" || $Fecha == "2021-10-18" || $Fecha == "2021-11-01" ||
                $Fecha == "2021-11-15" || $Fecha == "2021-12-08" || $Fecha == "2020-12-25") {
                $horavalle = "N";
            }

            //Fin Verifico si la hora es Diurna o nocturna

            if ($IDServicio == 2660 || $IDServicio == 2661 || $IDServicio == 2662): // CANCHA TENIS

                $id_categoria = $dbo->getFields("Socio", "IDCategoria", "IDSocio = '" . $IDSocio . "'");
                //if($id_categoria==44 || $id_categoria==45 || $id_categoria==46|| $id_categoria==47|| $id_categoria==48 || $id_categoria==49  ): //Es alumno o carne
                if ($id_categoria == 44000 || $id_categoria == 45000 || $id_categoria == 46000 || $id_categoria == 47000 || $id_categoria == 48000 || $id_categoria == 49000): //Es alumno o carne
                    switch ($id_categoria):
                case "44": //Alumno Campin
                case "45": //Alumno Salitre
                case "46": //Alumno Pq Nacional
                    if ($IDServicio == 2660): //Salitre
                        $valor = ($diurno == "S") ? $SalitreLibreEscuela : $SalitreLibreEscuela;
                    elseif ($IDServicio == 2661): //Campin
                        if (($IDElemento == "864" || $IDElemento == "865")): //Polvo Ladrillo
                            $valor = ($diurno == "S") ? $CampinPolvoAlumno : $CampinPolvoAlumno;
                        else: //Sinteticas
                            $valor = ($diurno == "S") ? $CampinSinteticaEscuela : $CampinSinteticaEscuela;
                        endif;
                    elseif ($IDServicio == 2662): //Pq Nacional
                        $valor = ($diurno == "S") ? $PqNacionalAlumno : $PqNacionalAlumno;
                    endif;
                    break;

                case "47": //Carné Campin
                case "48": //Carné Pq Nacional
                case "49": //Carné Salitre
                    if ($IDServicio == 2660): //Salitre
                        $valor = ($diurno == "S") ? $SalitreLibreCarne : $SalitreLibreCarne;
                    elseif ($IDServicio == 2661): //Campin
                        if (($IDElemento == "864" || $IDElemento == "865")): //Polvo Ladrillo
                            $valor = ($diurno == "S") ? $CampinPolvoCarne : $CampinPolvoCarne;
                        else: //Sinteticas
                            $valor = ($diurno == "S") ? $CampinSinteticaCarne : $CampinSinteticaCarne;
                        endif;
                    elseif ($IDServicio == 2662): //Pq Nacional
                        $valor = ($diurno == "S") ? $PqNacionalCarne : $PqNacionalCarne;
                    endif;
                    break;
                default:
                    $valor = 70000;
                    endswitch;

                else: // Es usuario normal
                    switch ($IDServicio):
                case "2660": //Tenis Salitre
                    $valor = ($diurno == "S") ? $SalitreDiurno : $SalitreNocturno;
                    $valor = ($horavalle == "S") ? $SalitreLibreValle : $valor;
                    break;
                case "2661": //Tenis Campin
                    if (($IDElemento == "864" || $IDElemento == "865")): //Polvo Ladrillo
                        $valor = ($diurno == "S") ? $CampinPolvoDiurno : $CampinPolvoNocturno;
                    else: //Sinteticas
                        $valor = ($diurno == "S") ? $CampinSinteticaDiurno : $CampinSinteticaNocturno;
                    endif;
                    break;
                case "2662": //Tenis Pq Nacional
                    $valor = ($diurno == "S") ? $PqNacionalDiurno : $PqNacionalDiurno;
                    break;
                default:
                    $valor = 80000;

                    endswitch;
                endif;

            else: //Clases de Tenis
                //Si es 1 es una persona si es 2 son dos personas
                $orden_reserva = (int) $dbo->getFields("ServicioTipoReserva", "Orden", "IDServicioTipoReserva = '" . $IDTipoReserva . "'");
                if ($orden_reserva == 1 || empty($orden_reserva)) {
                        $campo_valor = "Valor";
                } else {
                    $campo_valor = "Valor2";
                }

                $valor_clase = (int) $dbo->getFields("ServicioElemento", $campo_valor, "IDServicioElemento = '" . $IDElemento . "'");
                $IDCanchaReservada = $dbo->getFields("ReservaGeneralAutomatica", "IDServicioElemento", "IDReservaGeneral = '" . $IDReservaGeneral . "' and IDEstadoreserva = 1");
                $IDReservaCancha = $dbo->getFields("ReservaGeneralAutomatica", "IDReservaGeneralAsociada", "IDReservaGeneral = '" . $IDReservaGeneral . "' and IDEstadoreserva = 1");
                $IDServicioCancha = $dbo->getFields("ReservaGeneral", "IDServicio", "IDReservaGeneral = '" . $IDReservaCancha . "' and IDEstadoreserva = 1");
                if (!empty($IDSocio) && !empty($IDServicioCancha) && !empty($IDCanchaReservada)) {
                    $valor = self::calcular_tarifa($IDClub, $IDSocio, $IDServicioCancha, $Fecha, $Hora, $IDCanchaReservada);
                }
                $valor = (int) $valor_clase + (int) $valor;
            endif;

            return $valor;

    }

//Valor especial para reservas de nadesba
    public function calcular_tarifa2($IDSocio, $Invitados, $IDTipoReserva)
    {
        $dbo = &SIMDB::get();

        $Accion = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $IDSocio . "'");
        $Socio1 = explode("-", $Accion);

        $ArrayInvitados = json_decode($Invitados, true);

        $PrecioInd1 = 0;
        $PrecioInd2 = 15000;
        $PrecioInd3 = 20000;
        $PrecioInd4 = 25000;
        $PrecioGrp1 = 0;
        $PrecioGrp2 = 10000;
        $PrecioGrp3 = 15000;
        $PrecioGrp4 = 20000;
        $externo = 20000;

        $valor = 10;

        switch ($Socio1[0]):

    case "1":
        if (empty($ArrayInvitados)) {
                $valor = $PrecioInd1;
        } else {
            $valor = $PrecioGrp1;
            for ($i = 0; $i < count($ArrayInvitados); $i++) {
                if (isset($ArrayInvitados[$i]["IDSocio"])) {
                    $Socio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $ArrayInvitados[$i]["IDSocio"] . "'");
                    $Socio2 = explode("-", $Socio);

                    switch ($Socio2[0]):

                case "1":
                    $valor += $PrecioGrp1;
                    break;

                case "2":
                    $valor += $PrecioGrp2;
                    break;

                case "3":
                    $valor += $PrecioGrp3;
                    break;

                case "4":
                    $valor += $PrecioGrp4;
                    break;

                default:
                    $valor += $externo;
                    break;

                    endswitch;
                } else {
                        $valor += $externo;
                }
            }
        }
        break;

    case "2":
        if (empty($ArrayInvitados)) {
            $valor = $PrecioInd2;
        } else {
            $valor = $PrecioGrp2;

            for ($i = 0; $i < count($ArrayInvitados); $i++) {
                if (isset($ArrayInvitados[$i]["IDSocio"])) {
                    $Socio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $ArrayInvitados[$i]["IDSocio"] . "'");
                    $Socio2 = explode("-", $Socio);

                    switch ($Socio2[0]):

                case "1":
                    $valor += $PrecioGrp1;
                    break;

                case "2":
                    $valor += $PrecioGrp2;
                    break;

                case "3":
                    $valor += $PrecioGrp3;
                    break;

                case "4":
                    $valor += $PrecioGrp4;
                    break;

                default:
                    $valor += $externo;
                    break;

                    endswitch;
                } else {
                        $valor += $externo;
                }
            }
        }
        break;

    case "3":
        if (empty($ArrayInvitados)) {
            $valor = $PrecioInd3;
        } else {
            $valor = $PrecioGrp3;

            for ($i = 0; $i < count($ArrayInvitados); $i++) {
                if (isset($ArrayInvitados[$i]["IDSocio"])) {
                    $Socio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $ArrayInvitados[$i]["IDSocio"] . "'");
                    $Socio2 = explode("-", $Socio);

                    switch ($Socio2[0]):

                case "1":
                    $valor += $PrecioGrp1;
                    break;

                case "2":
                    $valor += $PrecioGrp2;
                    break;

                case "3":
                    $valor += $PrecioGrp3;
                    break;

                case "4":
                    $valor += $PrecioGrp4;
                    break;

                default:
                    $valor += $externo;
                    break;

                    endswitch;
                } else {
                        $valor += $externo;
                }
            }
        }
        break;

    case "4":
        if (empty($ArrayInvitados)) {
            $valor = $PrecioInd4;
        } else {
            $valor = $PrecioGrp4;

            for ($i = 0; $i < count($ArrayInvitados); $i++) {
                if (isset($ArrayInvitados[$i]["IDSocio"])) {
                    $Socio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $ArrayInvitados[$i]["IDSocio"] . "'");
                    $Socio2 = explode("-", $Socio);

                    switch ($Socio2[0]):

                case "1":
                    $valor += $PrecioGrp1;
                    break;

                case "2":
                    $valor += $PrecioGrp2;
                    break;

                case "3":
                    $valor += $PrecioGrp3;
                    break;

                case "4":
                    $valor += $PrecioGrp4;
                    break;

                default:
                    $valor += $externo;
                    break;

                    endswitch;
                } else {
                        $valor += $externo;
                }
            }
        }
        break;

        endswitch;

        if ($IDTipoReserva == "3083" || $IDTipoReserva == "3084") {
            switch ($Socio1[0]):

        case "1":
            if (empty($ArrayInvitados)) {
                    $valor = $PrecioGrp1;
            } else {
                $valor = $PrecioGrp1;
                for ($i = 0; $i < count($ArrayInvitados); $i++) {
                    if (isset($ArrayInvitados[$i]["IDSocio"])) {
                        $Socio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $ArrayInvitados[$i]["IDSocio"] . "'");
                        $Socio2 = explode("-", $Socio);

                        switch ($Socio2[0]):

                    case "1":
                        $valor += $PrecioGrp1;
                        break;

                    case "2":
                        $valor += $PrecioGrp2;
                        break;

                    case "3":
                        $valor += $PrecioGrp3;
                        break;

                    case "4":
                        $valor += $PrecioGrp4;
                        break;

                    default:
                        $valor += $externo;
                        break;

                        endswitch;
                    } else {
                            $valor += $externo;
                    }
                }
            }
            break;

        case "2":
            if (empty($ArrayInvitados)) {
                $valor = $PrecioGrp2;
            } else {
                $valor = $PrecioGrp2;

                for ($i = 0; $i < count($ArrayInvitados); $i++) {
                    if (isset($ArrayInvitados[$i]["IDSocio"])) {
                        $Socio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $ArrayInvitados[$i]["IDSocio"] . "'");
                        $Socio2 = explode("-", $Socio);

                        switch ($Socio2[0]):

                    case "1":
                        $valor += $PrecioGrp1;
                        break;

                    case "2":
                        $valor += $PrecioGrp2;
                        break;

                    case "3":
                        $valor += $PrecioGrp3;
                        break;

                    case "4":
                        $valor += $PrecioGrp4;
                        break;

                    default:
                        $valor += $externo;
                        break;

                        endswitch;
                    } else {
                            $valor += $externo;
                    }
                }
            }
            break;

        case "3":
            if (empty($ArrayInvitados)) {
                $valor = $PrecioGrp3;
            } else {
                $valor = $PrecioGrp3;

                for ($i = 0; $i < count($ArrayInvitados); $i++) {
                    if (isset($ArrayInvitados[$i]["IDSocio"])) {
                        $Socio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $ArrayInvitados[$i]["IDSocio"] . "'");
                        $Socio2 = explode("-", $Socio);

                        switch ($Socio2[0]):

                    case "1":
                        $valor += $PrecioGrp1;
                        break;

                    case "2":
                        $valor += $PrecioGrp2;
                        break;

                    case "3":
                        $valor += $PrecioGrp3;
                        break;

                    case "4":
                        $valor += $PrecioGrp4;
                        break;

                    default:
                        $valor += $externo;
                        break;

                        endswitch;
                    } else {
                            $valor += $externo;
                    }
                }
            }
            break;

        case "4":
            if (empty($ArrayInvitados)) {
                $valor = $PrecioGrp4;
            } else {
                $valor = $PrecioGrp4;

                for ($i = 0; $i < count($ArrayInvitados); $i++) {
                    if (isset($ArrayInvitados[$i]["IDSocio"])) {
                        $Socio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $ArrayInvitados[$i]["IDSocio"] . "'");
                        $Socio2 = explode("-", $Socio);

                        switch ($Socio2[0]):

                    case "1":
                        $valor += $PrecioGrp1;
                        break;

                    case "2":
                        $valor += $PrecioGrp2;
                        break;

                    case "3":
                        $valor += $PrecioGrp3;
                        break;

                    case "4":
                        $valor += $PrecioGrp4;
                        break;

                    default:
                        $valor += $externo;
                        break;

                        endswitch;
                    } else {
                            $valor += $externo;
                    }
                }
            }
            break;

            endswitch;
        }

        return $valor;

    }

//valor especial liga tenis
    public function calcular_tarifa3($IDSocio, $Invitados)
    {
        $dbo = &SIMDB::get();

        $ArrayInvitados = json_decode($Invitados, true);

        $externo = 15000;

        $valor = 0;

        for ($i = 0; $i < count($ArrayInvitados); $i++) {
            if ($ArrayInvitados[$i]["IDSocio"] == 0) {
                $valor += $externo;
            }
        }

        return $valor;

    }

    public static function get_IP()
    {
        if (getenv("HTTP_CLIENT_IP")) {
            $ip = getenv("HTTP_CLIENT_IP");
        } else
        if (getenv("HTTP_X_FORWARDED_FOR")) {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        } else {
            $ip = getenv("REMOTE_ADDR");
        }
        return $ip;
    }

    public function envia_cola_notificacion($datos_persona, $datos_envio)
    {
        $dbo = &SIMDB::get();
        $sql_cola = "INSERT INTO ColaNotificacion (IDClub,IDSocio,IDUsuario,IDSeccion,IDDetalle,IDModulo,IDSubmodulo,Token,Dispositivo,TipoUsuario,TipoNotificacion,Titulo,Mensaje,UsuarioTrCr,FechaTrCr)
		VALUES('" . $datos_envio["IDClub"] . "','" . $datos_persona["IDSocio"] . "','" . $datos_persona["IDUsuario"] . "','" . $datos_envio["IDSeccion"] . "','" . $datos_envio["ID"] . "',
            '" . $datos_envio["IDModulo"] . "','" . $datos_envio["IDSubModulo"] . "','" . $datos_persona["Token"] . "','" . $datos_persona["Dispositivo"] . "','" . $datos_envio["TipoUsuario"] . "','" . $datos_envio["TipoNotificacion"] . "','" . $datos_envio["Titular"] . "','" . $datos_envio["Mensaje"] . "','envio',NOW())";
        $dbo->query($sql_cola);
    }

    public function enviar_ws_lote()
    {
        $dbo = &SIMDB::get();

        $sql_transacciones = "SELECT * FROM PeticionesPlacetoPay Where tipo = 'Deuda' and estado_transaccion = 'APPROVED'  and EnviadaWS <> 'S' and Lote <> '' and Voucher <> '' and IDClub = '51' ";
        $r_transacciones = $dbo->query($sql_transacciones);
        while ($datos_transaccion = $dbo->fetchArray($r_transacciones)):
            $detalle_factura = "";
            $valor_documento_total = "";
            $Observacion_todas = "";
            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_transaccion["IDSocio"] . "' and IDClub = '" . $datos_transaccion["IDClub"] . "'", "array");
            $accion_socio = $datos_socio["Accion"];
            $array_documentos = json_decode($datos_transaccion["Documento"], true);
            //Verifico si la membresia existe
            $endpoint = ENDPOINT_CONDADO;
            $wsdlFile = ENDPOINT_CONDADO;
            //$num_recibo=time().rand(0,100);
            $num_recibo = $datos_transaccion["referencia"];

            if (count($array_documentos) > 0) {

                $response_detalle = array();

                foreach ($array_documentos as $detalle_documento) {
                    $valor_documento_total += $detalle_documento["Valor"];
                    $Observacion_todas .= " Lote: " . $datos_transaccion["Lote"] . " Linea:" . $datos_transaccion["Linea"] . " Establecimiento:2034182";
                    $Observacion_detalle = " Lote: " . $datos_transaccion["Lote"] . " Linea:" . $datos_transaccion["Linea"] . " Establecimiento:2034182";
                    $valor_documento = $detalle_documento["Valor"];
                    $numero_documento = $detalle_documento["NumeroDocumento"];

                    $comentario_transaccion = $datos_transaccion["Lote"] . "_" . $datos_transaccion["Voucher"] . "_2034182";

                    $detalle_factura .= '
						<FacturaDetalle xmlns="http://schemas.datacontract.org/2004/07/WS_APPMOVIL.Clases">
								<Id_Cliente>' . $accion_socio . '</Id_Cliente>
								<Id_Recibo>' . $num_recibo . '</Id_Recibo>
								<Monto_Documento>' . $valor_documento . '</Monto_Documento>
								<Numero_Documento>' . $numero_documento . '</Numero_Documento>
								<Observacion>' . $Observacion_detalle . '</Observacion>
								<Tipo_Documento>1</Tipo_Documento>
						</FacturaDetalle>';
                }

                try {
                    $bodyxml = '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
									<Body>
											<Pago_Factura xmlns="http://tempuri.org/">
													<!-- Optional -->
													<AplicarCabecera>
															<!-- Optional -->
															<FacturaCabecera xmlns="http://schemas.datacontract.org/2004/07/WS_APPMOVIL.Clases">
																	<Comentario>' . $comentario_transaccion . '</Comentario>
																	<Fecha>' . substr($datos_transaccion["fecha_peticion"], 0, 10) . '</Fecha>
																	<Id_Cliente>' . $accion_socio . '</Id_Cliente>
																	<Id_Recibo>' . $num_recibo . '</Id_Recibo>
																	<Monto>' . $valor_documento_total . '</Monto>
																	<Observacion>' . $Observacion_todas . '</Observacion>
																	<Papeleta></Papeleta>
																	<Tipo>TC</Tipo>
															</FacturaCabecera>
													</AplicarCabecera>
													<!-- Optional -->
													<AplicarDetalle>
															<!-- Optional -->
														' . $detalle_factura . '
													</AplicarDetalle>
											</Pago_Factura>
									</Body>
							</Envelope>
							';

                    //print_r($bodyxml);
                    //exit;

                    $client = new nusoap_client(ENDPOINT_CONDADO, true);
                    $err = $client->getError();
                    if ($err) {
                        //echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
                        //exit();
                        $error = 1;
                    }

                    $client->soap_defencoding = 'utf-8';
                    $client->useHTTPPersistentConnection();
                    $bsoapaction = "http://tempuri.org/IService1/Pago_Factura";

                    $result = $client->send($bodyxml, $bsoapaction);

                    // Check for a fault
                    if ($client->fault) {
                        //print_r($result);
                        $error = 2;
                    } else {
                        // Check for errors
                        $err = $client->getError();
                        if ($err) {
                            // Display the error
                            //echo '<h2>Error</h2><pre>' . $err . '</pre>';
                            $error = 3;
                        } else {
                            // Display the result
                            //print_r($result["Pago_FacturaResult"]);
                            //print_r($result);
                            //exit;
                            $update_transacc = "UPDATE PeticionesPlacetoPay
																	SET EnviadaWS = 'S', RespuestaWS = '" . $result["Pago_FacturaResult"] . "', ReciboPago = '" . $num_recibo . "'
																	WHERE IDPeticionesPlacetoPay = '" . $datos_transaccion["IDPeticionesPlacetoPay"] . "'";
                            $dbo->query($update_transacc);
                        }
                    }

                } catch (SoapFault $fault) {
                    trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
                }

            }

        endwhile;
        //echo "Terminado.";
    }

    public function actualiza_secciones_socio($IDClub, $IDSocio)
    {
        $dbo = &SIMDB::get();

        $sql_soc = "SELECT IDSocio FROM Socio WHERE IDSocio = '" . $IDSocio . "' ";
        $result_soc = $dbo->query($sql_soc);
        while ($row_soc = $dbo->fetchArray($result_soc)):

            //Seccion Noticias
            $sql_secc_club = "SELECT * From Seccion Where IDClub = '" . $IDClub . "'";
            $result_secc_club = $dbo->query($sql_secc_club);
            while ($row_secc = $dbo->fetchArray($result_secc_club)) {
                //Verifico si ya el socio la tiene si no se la creo
                $sql_soci_secc = "SELECT * From SocioSeccion Where IDSeccion = '" . $row_secc["IDSeccion"] . "' and IDSocio = '" . $row_soc["IDSocio"] . "'";
                $result_soci_secc = $dbo->query($sql_soci_secc);
                if ($dbo->rows($result_soci_secc) <= 0):
                    $insert_secc = "Insert into SocioSeccion (IDSocio, IDSeccion) Values ('" . $row_soc["IDSocio"] . "','" . $row_secc["IDSeccion"] . "')";
                    $dbo->query($insert_secc);
                    $count_noticia++;
                endif;
            }

            //Fin Seccion Noticias

            //Seccion Galerias
            $sql_secc_club = "SELECT * From SeccionGaleria Where IDClub = '" . $IDClub . "'";
            $result_secc_club = $dbo->query($sql_secc_club);
            while ($row_secc = $dbo->fetchArray($result_secc_club)) {
                //Verifico si ya el socio la tiene si no se la creo
                $sql_soci_secc = "SELECT * From SocioSeccionGaleria Where IDSeccionGaleria = '" . $row_secc["IDSeccionGaleria"] . "' and IDSocio = '" . $row_soc["IDSocio"] . "'";
                $result_soci_secc = $dbo->query($sql_soci_secc);
                if ($dbo->rows($result_soci_secc) <= 0):
                    $insert_secc = "Insert into SocioSeccionGaleria (IDSocio, IDSeccionGaleria) Values ('" . $row_soc["IDSocio"] . "','" . $row_secc["IDSeccionGaleria"] . "')";
                    $dbo->query($insert_secc);

                endif;
            }
            //FIN Seccion Galerias

            //Seccion Eventos
            $sql_secc_club = "SELECT * From SeccionEvento Where IDClub = '" . $IDClub . "'";
            $result_secc_club = $dbo->query($sql_secc_club);
            while ($row_secc = $dbo->fetchArray($result_secc_club)) {
                //Verifico si ya el socio la tiene si no se la creo
                $sql_soci_secc = "SELECT * From SocioSeccionEvento Where IDSeccionEvento = '" . $row_secc["IDSeccionEvento"] . "' and IDSocio = '" . $row_soc["IDSocio"] . "'";
                $result_soci_secc = $dbo->query($sql_soci_secc);
                if ($dbo->rows($result_soci_secc) <= 0):
                    $insert_secc = "Insert into SocioSeccionEvento (IDSocio, IDSeccionEvento) Values ('" . $row_soc["IDSocio"] . "','" . $row_secc["IDSeccionEvento"] . "')";
                    $dbo->query($insert_secc);
                    $count_evento++;
                endif;
            }
            //FIN Seccion Galerias

            //Seccion Eventos
            $sql_secc_club = "SELECT * From SeccionEvento2 Where IDClub = '" . $IDClub . "'";
            $result_secc_club = $dbo->query($sql_secc_club);
            while ($row_secc = $dbo->fetchArray($result_secc_club)) {
                //Verifico si ya el socio la tiene si no se la creo
                $sql_soci_secc = "SELECT * From SocioSeccionEvento2 Where IDSeccionEvento2 = '" . $row_secc["IDSeccionEvento2"] . "' and IDSocio = '" . $row_soc["IDSocio"] . "'";
                $result_soci_secc = $dbo->query($sql_soci_secc);
                if ($dbo->rows($result_soci_secc) <= 0):
                    $insert_secc = "Insert into SocioSeccionEvento2 (IDSocio, IDSeccionEvento2) Values ('" . $row_soc["IDSocio"] . "','" . $row_secc["IDSeccionEvento2"] . "')";
                    $dbo->query($insert_secc);
                endif;
            }
            //FIN Seccion Galerias

            //Seccion Clasificado
            $sql_secc_club = "SELECT * From SeccionClasificados Where IDClub = '" . $IDClub . "'";
            $result_secc_club = $dbo->query($sql_secc_club);
            while ($row_secc = $dbo->fetchArray($result_secc_club)) {
                //Verifico si ya el socio la tiene si no se la creo
                $sql_soci_secc = "SELECT * From SocioSeccionClasificados Where IDSeccionClasificados = '" . $row_secc["IDSeccionClasificados"] . "' and IDSocio = '" . $row_soc["IDSocio"] . "'";
                $result_soci_secc = $dbo->query($sql_soci_secc);
                if ($dbo->rows($result_soci_secc) <= 0):
                    $insert_secc = "Insert into SocioSeccionClasificados (IDSocio, IDSeccionClasificados) Values ('" . $row_soc["IDSocio"] . "','" . $row_secc["IDSeccionClasificados"] . "')";
                    $dbo->query($insert_secc);
                endif;
            }
            //FIN Seccion Galerias

        endwhile;

    }

    public function obtener_accion_pasarela($IDTipoPago, $IDClub)
    {
        $dbo = &SIMDB::get();

        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        $accion = "";
        switch ($IDTipoPago) {
            case "1": //Puede ser Payu Place to pay o zona virtual
                if ($datos_club["PasarelaPAYU"] == "S") {
                    $accion = $datos_club["URL_PAYU"];
                } elseif ($datos_club["PasarelaZonaVirtual"] == "S") {
                    $accion = $datos_club["UrlZona"];
                }
                break;
            case "4": //Exclusiva para payphone
                if ($datos_club["PasarelaPayPhone"] == "S") {
                    $accion = $datos_club["UrlPagoPayPhone"];
                }
                break;
            case "5": //Exclusiva para pay zen
                if ($datos_club["PasarelaPayZen"] == "S") {
                    $accion = $datos_club["UrlPagoPayZen"];
                }
                break;
            case "6": //Exclusiva para credibanco
                if ($datos_club["PasarelaCredibanco"] == "S") {
                    $accion = $datos_club["UrlCredibanco"];
                }
                break;
            case "7":
                $accion = $datos_club["UrlZona"];
                break;
            case "8": //Exclusiva para credibanco
                $accion = $datos_club["UrlZona"];
                break;
            case "11":
                $accion = $datos_club["UrlPlaceToPay"];
                break;
            case "12":
                $accion = $datos_club["UrlPagoApiCredibanco"];
                break;
            default:
                $accion = "";

        }

        return $accion;

    }

    public function sonda_place_to_pay($IDSocio = "")
    {
        $dbo = &SIMDB::get();

        if (!empty($IDSocio)) {
            $condicion_socio = " and IDSocio = '" . $IDSocio . "' ";
        }

        //Actualizar los pagos de place to pay
        $sql_transacciones = "SELECT * FROM PeticionesPlacetoPay Where (estado_transaccion = 'PENDING' or estado_transaccion = '' or estado_transaccion = 'OK') or (estado_transaccion = 'APPROVED' and EnviadaWS <> 'S' and tipo = 'Deuda' ) " . $condicion_socio;
        $r_transacciones = $dbo->query($sql_transacciones);
        while ($row_transaccion = $dbo->fetchArray($r_transacciones)):
            $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $row_transaccion["IDClub"] . "' ", "array");

            $login = trim($datos_club["LoginPlaceToPay"]);
            $secretKey = trim($datos_club["SecretKeyPlaceToPay"]);

            //obtención de nonce
            if (function_exists('random_bytes')) {
                $nonce = bin2hex(random_bytes(16));
            } elseif (function_exists('openssl_random_pseudo_bytes')) {
            $nonce = bin2hex(openssl_random_pseudo_bytes(16));
        } else {
            $nonce = mt_rand();
        }
        $nonceBase64 = base64_encode($nonce);

        $seed = date('c');
        $tranKey = base64_encode(sha1($nonce . $seed . $secretKey, true));

        $auth = array(
            "auth" => array(
                "login" => $login,
                "seed" => $seed,
                "nonce" => $nonceBase64,
                "tranKey" => $tranKey),
        );

        if ($row_transaccion["IDClub"] == 51) {

            if ($datos_club["IsTest"] == 1) {
                $url_place_to_pay = ENDPOINT_PLACE_TO_PAY_TEST;
            } else {
                $url_place_to_pay = ENDPOINT_PLACE_TO_PAY;
            }
        } else {
            if ($datos_club["IsTestPlaceToPay"] == 1) {
                $url_place_to_pay = ENDPOINT_PLACE_TO_PAY_TEST;
            } else {
                $url_place_to_pay = ENDPOINT_PLACE_TO_PAY;
            }
        }

        $ch = curl_init($url_place_to_pay . 'redirection/api/session/' . $row_transaccion["request_id"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $headers = ['Content-Type:application/json; charset=utf-8'];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($auth));
        $response = curl_exec($ch);
        curl_close($ch);
        // do anything you want with your response
        $respuesta = json_decode($response, true);

        /* var_dump($respuesta); */

        //actualizo la transaccion
        $Lote = $respuesta["payment"][0]["processorFields"][5]["value"];
        $Linea = $valor_linea = $respuesta["payment"][0]["processorFields"][6]["value"];
        $Voucher = $valor_linea = $respuesta["payment"][0]["receipt"];
        $update_transacc = "UPDATE PeticionesPlacetoPay
								SET estado_transaccion = '" . $respuesta["status"]["status"] . "',
								Lote = '" . $Lote . "', Linea = '" . $Linea . "', Voucher = '" . $Voucher . "'
								WHERE IDPeticionesPlacetoPay = '" . $row_transaccion["IDPeticionesPlacetoPay"] . "'";
        $dbo->query($update_transacc);

        switch ($respuesta["status"]["status"]) {
            case "OK":
            case "APPROVED":
                $estadoTx = "APROBADA";

                $estadoReserva = 1;
                $pagado = 'S';
                $estado = 'A';
                break;
            case "REJECTED":
                $estadoTx = "RECHAZADA";

                $estadoReserva = 2;
                $pagado = 'N';
                $estado = 'R';

                break;
            case "PENDING":
                $estadoTx = "PENDIENTE";

                $estadoReserva = 1;
                $pagado = 'S';
                $estado = 'A';

                break;
            case "APPROVED_PARTIAL":
                $estadoTx = "APROBADO PARCIAL";

                $estadoReserva = 1;
                $pagado = 'S';
                $estado = 'A';
                break;
            case "PARTIAL_EXPIRED":
                $estadoTx = "PARCIALMENTE EXPIRADO";

                $estadoReserva = 2;
                $pagado = 'N';
                $estado = 'R';
                break;
            case "PENDING_VALIDATION":
                $estadoTx = "PENDIENTE DE VALIDACION";

                $estadoReserva = 1;
                $pagado = 'S';
                $estado = 'A';
                break;
            case "REFUNDED":
                $estadoTx = "REINTEGRADO";

                $estadoReserva = 2;
                $pagado = 'N';
                $estado = 'R';
                break;
            default:
                $estadoTx = $estado_transaccion;
        }

        $actualizaReserva = "	UPDATE ReservaGeneral
										SET IDTipoPago = '1', IDEstadoReserva = '" . $estadoReserva . "', Pagado = '" . $pagado . "', PagoPayu = '" . $pagado . "', MedioPago = 'Place To Pay',
										FechaTransaccion = NOW(), CodigoRespuesta = '" . $respuesta["request"]["payment"]["reference"] . "',
										EstadoTransaccion = '" . $estado . "'
										WHERE IDReservaGeneral ='" . $row_transaccion["IDReserva"] . "' ";
        $dbo->query($actualizaReserva);

        switch ($row_transaccion["tipo"]) {
            case "Factura":
                if ($respuesta["status"]["status"] == "APPROVED") {
                    $update_transacc = "UPDATE FacturaConsumo
															SET Estado = 'Pagada',
															NumeroAprobacion = '" . $Voucher . "', Tarjeta = '" . $respuesta["payment"]["0"]["issuerName"] . "'
															WHERE IDFacturaConsumo = '" . $row_transaccion["IDMaestro"] . "'";
                    $dbo->query($update_transacc);
                }
                break;
        }
        endwhile;
        //echo "Terminado.";
    }

    public function ingreso_votante($IDClub, $IDVotacionEvento, $IDSocio, $Nombre, $NumeroCasa, $Cedula, $Coeficiente, $Consejero, $Moroso, $UsuarioCrea, $Tipo = "")
    {
        $dbo = &SIMDB::get();
        if ((int) $IDSocio > 0) {
            //if(is_numeric($Cedula)){
            if (!empty($Coeficiente) && !empty($Moroso)) {
                //traigo el id del socio
                $IDVotacionVotante = $dbo->getFields("VotacionVotante", "IDVotacionVotante", "Cedula = '" . $Cedula . "' and IDClub = '" . $IDClub . "' and IDVotacionEvento = '" . $IDVotacionEvento . "'");
                if ((int) $IDVotacionVotante > 0) {
                    $sql_update = "UPDATE  VotacionVotante SET IDSocio = '" . $IDSocio . "',Nombre = '" . $Nombre . "', NumeroCasa = '" . $NumeroCasa . "',
																			 Coeficiente = '" . $Coeficiente . "', Consejero = '" . $Consejero . "',Moroso='" . $Moroso . "', FechaTrEd = NOW(), UsuarioTrEd = '" . SIMUser::get("IDUsuario") . "', Tipo = '" . $Tipo . "'
																WHERE IDVotacionVotante = '" . $IDVotacionVotante . "'";
                    $dbo->query($sql_update);
                    $numregok++;
                } else {
                    $sql_insert = "INSERT INTO  VotacionVotante (IDClub, IDSocio,IDVotacionEvento,Nombre,NumeroCasa,Cedula,Coeficiente,Consejero,Moroso,Tipo,FechaTrCr,UsuarioTrCr)
															VALUES ('" . $IDClub . "','" . $IDSocio . "','" . $IDVotacionEvento . "','" . $Nombre . "','" . $NumeroCasa . "','" . $Cedula . "','" . $Coeficiente . "','" . $Consejero . "','" . $Moroso . "','" . $Tipo . "',NOW(),'" . $UsuarioCrea . "')";
                    $dbo->query($sql_insert);
                    $numregok++;
                }
            } else {
                echo $array_reporte_carga[] = "<br>La cedula:  " . $Cedula . " falta el dato de si es moroso o Coeficiente";
            }
            //}
            //else{
            //echo $array_reporte_carga[]="<br>La cedula:  ".$Cedula." no debe tener puntos, comas, etc";
            //}
        } else {
            echo $array_reporte_carga[] = "<br>La persona  " . $Cedula . " no fue encontrado";
        }
        return $numregok;
    }

    public function verifica_coeficiente($IDSocio, $IDEvento = "")
    {
        $dbo = &SIMDB::get();
        $datos_votante = $dbo->fetchAll("VotacionVotante", " IDSocio = '" . $IDSocio . "' and IDVotacionEvento = '" . $IDEvento . "'", "array");
        $sql_sumapoderes = "SELECT Coeficiente FROM VotacionVotante WHERE IDVotacionVotante in (SELECT IDVotacionVotanteDelegaPoder FROM VotacionPoder WHERE IDVotacionVotante = '" . $datos_votante["IDVotacionVotante"] . "') or IDVotacionVotante = '" . $datos_votante["IDVotacionVotante"] . "' ";
        $r_sumapoderes = $dbo->query($sql_sumapoderes);
        while ($row_poderes = $dbo->fetchArray($r_sumapoderes)) {
            $suma_otorgados += $row_poderes["Coeficiente"];
        }
        return $suma_otorgados;
    }

    public function transacciones_pendientes_place_to_pay($IDSocio, $link_continuar, $ReferenciaPago)
    {
        $dbo = &SIMDB::get();

        $array_transaccion = array();
        //Actualizar los pagos de place to pay
        $sql_transacciones = "SELECT * FROM PeticionesPlacetoPay Where IDSocio = '" . $IDSocio . "' and estado_transaccion = 'PENDING' and referencia <> '" . $ReferenciaPago . "'";
        $r_transacciones = $dbo->query($sql_transacciones);
        $filas = $dbo->rows($r_transacciones);
        if ($filas > 0) {
            $mensaje = "<style>table {
		width: 100%;
		border: 1px solid #999;
		text-align: left;
		border-collapse: collapse;
		margin: 0 0 1em 0;
		caption-side: top;
		}
		caption, td, th {
		padding: 0.3em;
		}
		th {
		border-bottom: 1px solid #999;
		width: 25%;
			background: #f4faa9;
		}
		td {
		border-bottom: 1px solid #999;
		width: 25%;
			background: #FFF;
		}
		caption {
		font-weight: bold;
		font-style: italic;
		}
		.enlaceboton {
			PADDING-RIGHT: 4px; PADDING-LEFT: 4px; FONT-WEIGHT: bold; FONT-SIZE: 10pt; PADDING-BOTTOM: 4px; COLOR: #666666; PADDING-TOP: 4px; FONT-FAMILY: verdana, arial, sans-serif; BACKGROUND-COLOR: #ffffcc; TEXT-DECORATION: none
		}
		.enlaceboton:link {
			BORDER-RIGHT: #666666 2px solid; BORDER-TOP: #cccccc 1px solid; BORDER-LEFT: #cccccc 1px solid; BORDER-BOTTOM: #666666 2px solid
		}
		.enlaceboton:visited {
			BORDER-RIGHT: #666666 2px solid; BORDER-TOP: #cccccc 1px solid; BORDER-LEFT: #cccccc 1px solid; BORDER-BOTTOM: #666666 2px solid
		}
		.enlaceboton:hover {
			BORDER-RIGHT: #cccccc 1px solid; BORDER-TOP: #666666 2px solid; BORDER-LEFT: #666666 2px solid; BORDER-BOTTOM: #cccccc 1px solid
		}

		</style>";
            $mensaje .= "<br>Tienes las siguientes transacciones pendientes<br><br>";
            $mensaje .= "<table class='table' border=1><tr><th align='center'>Referencia</th><th align='center'>Fecha</th></tr>";
            while ($row_transaccion = $dbo->fetchArray($r_transacciones)) {
                $fecha_hora = substr($row_transaccion["fecha_peticion"], 0, 19);
                $fecha_hora = str_replace("T", " ", $fecha_hora);
                $mensaje .= "<tr><td>" . $row_transaccion["referencia"] . "</td><td>" . $fecha_hora . "</td></tr>";
            }
            $mensaje .= "</table>";
            if ($ReferenciaPago == 1) {
                $mensaje .= "<br><br><input type='submit' value='Clic aqui si desas continuar de todas formas.'>";
            } else {
                $mensaje .= "<br><br><a class=enlaceboton   href='" . $link_continuar . "' >Clic aqui si desas continuar de todas formas.</a>";
            }

            echo $mensaje;
            exit;
        }

    }

}
