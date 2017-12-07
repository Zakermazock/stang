<?php
/**
 * Created by PhpStorm.
 * User: Velen
 * Date: 26/11/17
 * Time: 17:53
 */

class contenuCv {

    public $id=0;
    public $idStructure=0;
    public $contenu="";
    public $ordre=0;

    public static function getContenuCV($id=0,$idStructure=0)
    {
        $error =false;
        $arrayContenu = array();

        $request = "SELECT * FROM contenucv";
        $params = array();
        $whereFilters = array();

        if($id > 0){
            $params[':id'] = $id;
            $whereFilters[] = "id=:id";
        }
        if($idStructure > 0){
            $params[':idStructure'] = $idStructure;
            $whereFilters[] = "idStructure=:idStructure";
        }



        $request .= (count($whereFilters)>0) ? " WHERE ".implode(" AND ", $whereFilters) : "";
        $request .= " ORDER BY ordre asc";

        $userSqlRows = BDD::sqlExecRequestWithTokens(BDD::USER_DB, $request, $params);
        foreach($userSqlRows as $row)
        {

            $contenu = new contenuCv();

            $contenu->populateByDbRow($row);
            $arrayContenu[] = $contenu;

        }

        return $arrayContenu;

    }

    public function populateByDbRow($dbRow)
    {

        foreach($this as $key => $value)
        {
            $this->$key = $dbRow->$key;
        }

    }

    public function alreadyExistInDB()
    {
        $bdd = BDD::getPDO(BDD::USER_DB);
        $req = $bdd->prepare("SELECT * FROM contenucv WHERE id = :id");
        $req->execute(array(':id' => $this->id));
        $resultat = $req->fetch();

        if(!$resultat)
        {
            return false;
        }

        return true;
    }

    public function save()
    {
        $bdd = BDD::getPDO(BDD::USER_DB);
        $allReadyExist = $this->alreadyExistInDB();
        $error =false;
        $fields="(";
        $values="(";
        $fields2 = array();
        $params = array();
        $update = "";

        foreach($this as $key => $value) //parsing object attributes and set them with xml values
        {
            switch($key)
            {
                case 'id':
                    break;

                default :
                    if($value!="")
                    {
                        $fields2[] = $key;
                        $params[":".$key] = $value;
                        $fields .=$key.",";
                        $values .=":".$key.",";
                        //if($key!="CliGenRef")
                        $update .= $key."=:".$key.",";
                    }
                    break;
            }

        }

        $fields = substr($fields,0,-1).")";
        $values = substr($values,0,-1).")";

        $update = substr($update,0,-1);

        if(!$allReadyExist) //create
        {
            $requete = $bdd->prepare("insert into contenucv ".$fields." VALUES ".$values);
        }
        else
        {
            $requete = $bdd->prepare("UPDATE contenucv SET ".$update." WHERE id=:id2");
            $params[":id2"] = $this->id;
        }

        if($requete->execute($params))
        {
            if(!$allReadyExist)
                $this->id =$bdd->lastInsertId();
        }
        else
        {
            $error = true;
            //LOG::LogDB("Echec add / maj travaux - error info : ".$bdd->errorInfo()[2]);
        }

        //add user Right

        if($error === true)
        {
            return false;
        }
        else
        {
            return true;
        }

    }

    public static function delete($id)
    {
        $bdd = BDD::getPDO(BDD::USER_DB);
        $requete = $bdd->prepare("delete from contenucv where id=:id");
        $params[":id"] = $id;
        if($requete->execute($params))
        {

            return true;
        }
        else
        {
            $error = true;
            //LOG::LogDB("Echec suppression de l'élémentt - error info : ".$bdd->errorInfo()[2]);
            return false;
        }
    }
} 