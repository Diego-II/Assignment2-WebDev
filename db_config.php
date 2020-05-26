<?php
class DbConfig{
	private static $db_name = "cc5002_db"; //Base de datos de la app
	private static $db_user = "cc5002"; //Usuario MySQL
	private static $db_pass = "cc5002"; //Password
	private static $db_host = "localhost";//Servidor donde esta alojado, puede ser 'localhost' o una IP (externa o interna).
	
	public static function getConnection(){
		$mysqli = new mysqli(self::$db_host, self::$db_user, self::$db_pass, self::$db_name);

		$mysqli->set_charset("utf8"); //Muy importante!!
		return $mysqli;
	}
}
?>