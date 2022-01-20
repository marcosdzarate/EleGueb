<?php
class Database
{
	private static $dbName = elDB ;
    private static $dbHost = elServer ;
    private static $dbUsername = elUser;
	private static $dbUserPassword = elPassword;
	private static $dbPuertaServer = elPort;

	/*
	private static $dbName = "elefasur" ;
    private static $dbHost = "localhost" ;
    private static $dbUsername = "DBelefasur";
    private static $dbUserPassword = "xxxx";
    */

    private static $cont  = null;

    public function __construct() {
        die('Init function is not allowed');
    }

    public static function connect()
    {
       // One connection through whole application
       if ( null == self::$cont )
       {  
        try
        {
          /*self::$cont =  new PDO( "mysql:host=".self::$dbHost.";port=3306;dbname=".self::$dbName, self::$dbUsername, self::$dbUserPassword); */
		  /* agrego ...UTF8 */
          self::$cont =  new PDO( "mysql:host=".self::$dbHost.";port=".self::$dbPuertaServer.";dbname=".self::$dbName, self::$dbUsername, self::$dbUserPassword,
		  array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES UTF8,lc_messages=es_AR"));
        }
        catch(PDOException $e)
        {
          die($e->getMessage());
        }
       }
       return self::$cont;
    }
    public static function disconnect()
    {
        self::$cont = null;
    }
}
?>