<?php
class BDD {

    private static $_pdoList;
		
    const USER_DB = 1;
    const ADMIN_DB = 2;
    const CONFIG_THERM=3;
    const CONFIG_PRODUIT=4;

    private function __construct() {
    }


    public static function getPDO($db)
    {
		if(is_null(self::$_pdoList)) self::$_pdoList = array();

		if(isset(self::$_pdoList[$db])){
			return self::$_pdoList[$db];
		} else {
			switch($db){
				case self::USER_DB :
					$dbName = "db709382224";
					$dbUser = "dbo709382224";
					$dbPassword = "Stang_2017";
					break;
				case self::ADMIN_DB :
					$dbName = "db709382224";
					$dbUser = "dbo709382224";
					$dbPassword = "Stang_2017";
					break;
                case self::CONFIG_THERM :
                    $dbName = "caib_prod";
                    $dbUser = "root";
                    $dbPassword = "";
                    break;
                case self::CONFIG_PRODUIT :
                    $dbName = "caib_config_produit";
                    $dbUser = "root";
                    $dbPassword = "";
                    break;
				default :
					return null;
			}
			
			try
			{
				self::$_pdoList[$db] = new PDO('mysql:host=db709382224.db.1and1.com;dbname='.$dbName, $dbUser, $dbPassword, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
			}
			catch (Exception $e)
			{
				die('Erreur : ' . $e->getMessage());
			}
			
			return self::$_pdoList[$db];
		}
		
		die('Erreur : BDD::getPDO()');
		return null;
    }
	
	public static function sqlExecRequestWithTokens($db, $request, $option=null)
    {
        
        $results = array();
        $reponse = self::getPDO($db)->prepare($request);
        
        if(is_object($reponse)){
		
            $reponse->setFetchMode(PDO::FETCH_OBJ);

            if($option){
                $reponse->execute($option);
            } else {
                $reponse->execute();
            }

            if ($reponse->columnCount() > 0)
            {
                $donnees = $reponse->fetchAll();

                foreach ($donnees as $row){
                    $results[] = $row;
                }

            }

            return $results;

        }
        
        return false;
    }
	
	
}

?>

