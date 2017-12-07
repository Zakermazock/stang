<?php
/**
 * Created by PhpStorm.
 * User: jonat
 * Date: 05/11/17
 * Time: 13:40
 */




class visuelTravaux {

        public $id=0;
        public $idTravaux=0;
        public $nom="";
        public $visuel="";
        public $ordre="";


    public static function getVisuel($id=0,$idTravaux=0,$nom="")
    {
        $error =false;
        $arrayTravaux = array();

        $request = "SELECT * FROM visuel_travaux";
        $params = array();
        $whereFilters = array();

        if($id > 0){
            $params[':id'] = $id;
            $whereFilters[] = "id=:id";
        }
        if($idTravaux >0)
        {
            $params[':idTravaux'] = $idTravaux;
            $whereFilters[] = "idTravaux=:idTravaux";
        }


        $request .= (count($whereFilters)>0) ? " WHERE ".implode(" AND ", $whereFilters) : "";
        $request .= " ORDER BY ordre asc";

        $userSqlRows = BDD::sqlExecRequestWithTokens(BDD::USER_DB, $request, $params);
        foreach($userSqlRows as $row)
        {

            $visuelTravaux = new visuelTravaux();

            $visuelTravaux->populateByDbRow($row);
            $arrayTravaux[] = $visuelTravaux;

        }

        return $arrayTravaux;

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
        $req = $bdd->prepare("SELECT * FROM visuel_travaux WHERE id = :id");
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
            $requete = $bdd->prepare("insert into visuel_travaux ".$fields." VALUES ".$values);
        }
        else
        {
            $requete = $bdd->prepare("UPDATE visuel_travaux SET ".$update." WHERE id=:id2");
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
            //LOG::LogDB("Echec add / maj visuel_travaux - error info : ".$bdd->errorInfo()[2]);
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
} 