<?php

/*==============================================
                    Debug
================================================*/
function debug($debug=NULL)
{
  echo "<pre>";
  echo "<h2><strong>".__FILE__."</strong></h2><br>";
  print_r($debug);
  echo "</pre>";
}

define("DB_NAME", "android_api");
define("DB_USER", "root");
define("DB_PASSWORD", "");

if(isset($db)==FALSE){

    try {

       $db = new DAO(DB_NAME, DB_USER, DB_PASSWORD,$table=null);
    
    } catch (PDOException $e) {

      exit("Echec de la connexion".$e->getMessage());
    
    }
}

/////////////////////////////////////////////////////////////////////////////
//                      CLASS DAO herite du CLass PDO
////////////////////////////////////////////////////////////////////////////

class DAO extends PDO{
   public $table;
   private $bdd_a;
   private $lastInsertId_a;
   

/*=====================================================================
                                CONSTRUCTEUR
=======================================================================*/ 
  public function __construct($dsn_p, $username_p, $password_p,$table)
  {
    parent::__construct("mysql:host=localhost;dbname=$dsn_p", $username_p, $password_p);
    $this->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, TRUE);
    $this->lastInsertId_a = 0;
    $this->bdd_a = $dsn_p;
    echo $table;
    if(isset($table)){$this->table = $table;}
    if(empty($this->table)){
      echo "vide";
    }
    else
    {echo "non vide";}


  }
/*=====================================================================
                           METHODE SEND ERROR
======================================================================*/
  public function sendError($query_p)
  { 
    $message = $_SERVER['SCRIPT_FILENAME'].'?'.$_SERVER['QUERY_STRING'] . "\n"
             . 'line = '. __line__ . "\n"
             . date("d/m/Y H:i:s") . "\n";
    $message .= "\n" . $this->error($query_p, "\n") . "\n";
    if (PHP_OS == "WINNT")
    {
      if (($file = fopen("zzz-db-error.log", "at")) !== FALSE)
      {
        fwrite($file, $message);
        fclose($file);
      }
    }
    else // si je suis en ligne j'envoi l'erreur par email avec fichier joint
    {
      // envoyer un email SMTP

    }
  }
  
/*======================================================================
                            METHODE Error
========================================================================*/
  public function error($query_p=null, $crlf_p="<br/>")
  {
    $message = "";
    if (strlen($query_p) > 0)
    {
      $message .= $query_p . $crlf_p;
    }
    $error = $this->errorInfo();
    if (is_array($error) == TRUE)
    {
      for ($i=0; $i<count($error); $i++)
      {
        $message .= $error[$i] . $crlf_p;
      }
    }
    return $message;
  }
/*=====================================================================
                             METHODE QUERY
=======================================================================*/
  public function query($query_p)
  {
    $result = parent::query($query_p);
    if($result === FALSE)
    {
      $this->sendError($query_p);
      $result = 0;

    }
    return $result;
  }
/*======================================================================
                      METHODE SAVE AND UPDATE
=======================================================================*/
  public function save(array $data)
  {
    if(isset($data['table']) && !empty($data['table']) )
    {
      $table = $data['table'];
      unset($data['table']);
      if(isset($data['id']) && !empty($data['id']))
      {

        $sql="UPDATE ".$table." SET ";
        foreach($data as $key=>$val)
        {
          if($key!="id")
          {
            
            if(is_string($val))
            { 
              $sql.="$key='".addslashes(utf8_decode($val))."',";
            }
            else if(is_float($val) || is_int($val))
            {
              $sql.="$key = $val,"; 
            }
          } 
        }
        $sql=substr($sql,0,-1);
        $sql.="WHERE `id`=".$data['id'];  
      }
      else
      { // INSERT DES DONNéES via INSERT INTO
        $sql="INSERT INTO ".$table." (";
        unset($data['id']);
        foreach($data as $key=>$val)
        {
          $sql.="$key,";
        }
        $sql=substr($sql,0,-1);
        $sql.=")VALUES(";
        foreach($data as $val)
        {
          if(is_string($val))
          {
            $sql.="'".addslashes(utf8_decode($val))."',";
          }
          elseif(is_float($val) || is_int($val))

          { $sql.="$val,"; }
          
        }
        $sql=substr($sql,0,-1);
        $sql.=")";
      }
      
      $result = parent::exec($sql);
      $this->lastInsertId_a = intval(parent::lastInsertId());
      if($result === FALSE)
      {
        $this->sendError($sql);
      }
      return $result;
    }
  }
/*===================================================================
                             METHODE DELETE
===================================================================*/
  public function delete(array $data)
  {
    if(isset($data['table']) && !empty($data['table']) )
    {
      if(isset($data['id']) && !empty($data['id']) )
      {
        $table = $data['table']; // affectation nom table
        $id    = $data['id'];    // affectation id

        $sql = "DELETE FROM `".$table."` WHERE `id` = $id ";
        $result = parent::exec($sql);
        if($result === FALSE)
        {
          $this->sendError($sql);
        }
        return $result;
      }
    }
  }
/*===================================================================
                         METHODE LastInsertId
====================================================================*/
  public function getLastId()
  {
    return $this->lastInsertId_a;
  }
/*===================================================================
                         METHODE setTable
====================================================================*/
public function table($table){

  $this->table = $table;
}
  /*===================================================================
                         METHODE getTable
====================================================================*/
  public function getTable()
  {
    return $this->table;
  }
/*=====================================================================
                             METHODE SHOW CHAMPS TABLE 
=======================================================================*/
public function getFieldsName($tableName){

  $res = $this->query("SHOW COLUMNS FROM $tableName");
  $fields = $res->fetchAll(PDO::FETCH_ASSOC);
  foreach ($fields as $field) {
    $fieldNames[] = $field['Field']; 
  }
  $data[$tableName] = array($fieldNames);
  return $data;
}
/*=====================================================================
                             METHODE Find 
=======================================================================*/
  public function find($operator,$params=array()){
    $requette = "SELECT ";
    $limit = NULL;
    $donnees  = array();

    /*===
      operateur 'first' or 'all'
    */
    switch($operator):
      case "all":
      break;
      case "first":
        $limit = " LIMIT 1";
      break;
    endswitch;
    /*===
      param les champs fields
    */ 
      if(empty($params['fields'])) // si le tableau fields est vide, donc je met l'operateur *
          $requette .= " * ";
      else
      {
        foreach ($params['fields'] as $field): // j'ai definit les champs donc je les extrait
          $requette .="`$field`,";
        endforeach;
        $requette=substr($requette,0,-1); // suprime le dernier AND du requette
      }
    /*===
      param Table
    */
      if(!empty($params['table'])){
        $this->table =  $params['table']; // Nom du table
        $requette .=" FROM `".$params['table']."`"; 
      }
      else
      {
        $requette .=" FROM `".$this->table."`";
      }
    /*===
      param alias
    */ 
      if(!empty($params['alias'])){ $requette .=" AS `".$params['alias']."`"; }
    
    /*===
      param jointure de type LEFT joins or RIGTH joins
    */    
      if(!empty($params['joins'])):
      
      endif;
    /*===
      param Conditions WHERE
    */  
      if(!empty($params['conditions'])):
        if(is_array($params['conditions'])){
          $requette .=" WHERE ";
          foreach ($params['conditions'] as $key => $value) {
          
            $requette .="`$key` = '$value' AND ";
          }
          $requette=substr($requette,0,-4); // suprime le dernier AND du requette
        }else{

          $requette .= $params['conditions']." ";
        }
      endif;
      /*===
      param Conditions Order
    */ 
      if(!empty($params['order'])):
        $requette .="ORDER BY ";
        if(is_array($params['order'])){
            foreach ($params['order'] as $key => $value) {
              $requette .="`$key` $value";
            }
        }else{
              $requette .= $params['order']." ";
        }
        endif;
         /*===
        LIMIT
        */
        $requette.= $limit;
        //debug($requette);
       /*===
        EXECUTION DU REQUETE VIA FONCTION QUERY
       */
        $dataKey = array();
        if (($res = $this->query($requette)) !== FALSE)
        {
          if($res=='0'){ // j'ai une erreur en sql
            return "erreur";
          }else{
            $row = $res->fetchAll(PDO::FETCH_ASSOC);
            if(count($row)==0){ // si la requete ne retourn aucun enregistrement

              return 0;
            }
          }
        }

         $donnees[$this->table] = $row ; // RETURN RESULTAT VIA UN TABLEAU ARRAY
         return $donnees;
  }
    
}
?>