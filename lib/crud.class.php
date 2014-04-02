<?php
define("DB_NAME", "crud_db"); // DATA NAME
define("DB_USER", "root");   // USER
define("DB_PASSWORD", "");  // PASSWORD
if(isset($db)==FALSE){
  try {
  $db = new DAO(DB_NAME, DB_USER, DB_PASSWORD);}
  catch (PDOException $e) {exit("Echec de la connexion".$e->getMessage());}
}
/////////////////////////////////////////////////////////////////////////////
//                      CLASS DAO EXTENDS CLass PDO
////////////////////////////////////////////////////////////////////////////

class DAO extends PDO{
  public $table;
  private $lastInsertId_a; 
  public function __construct($dsn_p, $username_p, $password_p)
  {
    parent::__construct("mysql:host=localhost;dbname=$dsn_p", $username_p, $password_p);
    $this->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, TRUE);
    $this->lastInsertId_a = 0;
  }
/*=====================================================================
                        METHODE SHOW FIELDS TABLE 
=======================================================================*/
public function getFieldsName(){

  $res = $this->query("SHOW COLUMNS FROM $this->table");
  $fields = $res->fetchAll(PDO::FETCH_ASSOC);
  foreach ($fields as $field) {
    $fieldNames[] = $field['Field']; 
  }
  $data[$this->table] = array($fieldNames);
  return $data;
}
/*=====================================================================
                           METHODE SEND ERROR
======================================================================*/
  public function sendError($query_p){ 
    $message = $_SERVER['SCRIPT_FILENAME'].'?'.$_SERVER['QUERY_STRING'] . "\n"
             . 'line = '. __line__ . "\n"
             . date("d/m/Y H:i:s") . "\n";
    $message .= "\n" . $this->error($query_p, "\n") . "\n";
    if (PHP_OS == "WINNT"){
      if (($file = fopen("db-error.log", "at")) !== FALSE){
        fwrite($file, $message);
        fclose($file);
        echo"<br><a target='_blank' href='db-error.log'>db-error.log</a>";
      }
    }else{ // si je suis en ligne j'envoi l'erreur par email avec fichier joint

      // envoyer un email SMTP
    }
  }
  
/*======================================================================
                            METHODE Error
========================================================================*/
  public function error($query_p=null, $crlf_p="<br/>"){
    $message = "";
    if (strlen($query_p) > 0){$message .= $query_p . $crlf_p;}
    $error = $this->errorInfo();
    if (is_array($error) == TRUE){
      for ($i=0; $i<count($error); $i++){$message .= $error[$i] . $crlf_p;}
    }
    return $message;
  }
/*=====================================================================
                             METHODE QUERY
=======================================================================*/
  public function query($query_p){
    $result = parent::query($query_p);
    if($result === FALSE){
      $this->sendError($query_p);
      $result = 0;
    }
    return $result;
  }
/*===================================================================
                      METHODE SAVE AND UPDATE
=====================================================================*/
  public function save(array $data){
    if(!empty($data['table'])){ 
      $this->table = $data['table'];
      unset($data['table']);
    }
    /*===================CHECK ID IN ARRAY=====================*/
    if(isset($data['id']) && !empty($data['id'])){
      // UPDATE DATA
      $sql="UPDATE ".$this->table." SET ";
      foreach($data as $key=>$val):
        if($key!="id"){
          if(is_string($val)) { $sql.="$key='".addslashes(utf8_decode($val))."',"; }
          elseif(is_float($val) || is_int($val)) { $sql.="$key = $val,"; }
        } 
      endforeach;
      $sql=substr($sql,0,-1);
      $sql.=" WHERE `id`=".$data['id'];  
    }
    else{ 
      // ADD DATA
      $sql="INSERT INTO ".$this->table." (";
      unset($data['id']);
      foreach($data as $key=>$val){ $sql.="$key,"; };
      $sql=substr($sql,0,-1);
      $sql.=")VALUES(";
      foreach($data as $val):
        if(is_string($val)){ $sql.="'".addslashes(utf8_decode($val))."',"; }
        elseif(is_float($val) || is_int($val)){ $sql.="$val,"; }
      endforeach;
      $sql=substr($sql,0,-1);
      $sql.=")";
    }
      //EXECUTE QUERY
      $result = parent::exec($sql);
      if($result!==FALSE){$this->lastInsertId_a = intval(parent::lastInsertId());}
      else{echo "Erreur Save";$this->sendError($sql);}
    }
/*=================================================================
                          METHODE DELETE
===================================================================*/
  public function delete(array $data){
    if(!empty($data['table'])){ $this->table = $data['table']; }
    if(isset($data['id']) && !empty($data['id'])){
      $id    = $data['id']; 
      $sql = "DELETE FROM `".$this->table."` WHERE `id` = $id ";
      $result = parent::exec($sql);
      if($result == 0){echo"Erreur Delete";$this->sendError($sql);}
      return $result;
    }
  }
/*=================================================================
                          METHODE DELETEBYID
===================================================================*/
public function deleteById($id){
  if(!empty($id)&& is_int($id)){
    $sql = "DELETE FROM `".$this->table."` WHERE `id` = $id ";
    $result = parent::exec($sql);
    if($result == 0){echo"Erreur DeleteById";$this->sendError($sql);}
    return $result;
  }
}
/*=================================================================
                          METHODE DELETEALL
===================================================================*/
public function deleteAll(){
  $sql = "DELETE FROM `".$this->table."`";
    $result = parent::exec($sql);
    if($result == 0){echo"Erreur DeleteAll";$this->sendError($sql);}
    return $result;
}
/*===================================================================
                         METHODE LastInsertId
====================================================================*/
  public function getLastId(){ return $this->lastInsertId_a;}
/*===================================================================
                         METHODE setTable
====================================================================*/
public function table($table){ $this->table = $table;}
  /*===================================================================
                         METHODE getTable
====================================================================*/
public function getTable(){ return $this->table;}
/*=====================================================================
                             METHODE Find 
=======================================================================*/
public function find($operator,$params=array()){
  $requette = "SELECT ";
  $limit = NULL;
  $donnees  = array();
  //operateur 'first' or 'all'
  switch($operator): 
    case "all":
    break;
    case "first":
    $limit = " LIMIT 1";
    break;
  endswitch;
  //PARAM FIELDS
  if(empty($params['fields'])) // si le tableau fields est vide, donc je met l'operateur *
    $requette .= " * ";
  else{
    foreach ($params['fields'] as $field){$requette .="$field,";}
    $requette=substr($requette,0,-1); // suprime le dernier AND du requette
  }
  //PARAM TABLE
  if(!empty($params['table'])){
    $this->table =  $params['table']; // Nom du table
    $requette .=" FROM `".$params['table']."`"; 
  }
  else{ $requette .=" FROM `".$this->table."`";}
  //PARAM ALIAS
  if(!empty($params['alias'])){ $requette .=" AS `".$params['alias']."` "; }
  // PARAM JOINS(jointure de type LEFT joins or RIGTH joins )  
  if(!empty($params['joins'])){
    $joins = $params['joins'];
      if(is_array($joins['tables']) && is_array($joins['alias']) && is_array($joins['type']) && is_array($joins['condition'])){
        $count = count($joins['tables']);
        for($i=0;$i<$count;$i++){
          $requette .=$joins['type'][$i]." JOIN `".$joins['tables'][$i]."` AS `".$joins['alias'][$i]."` ON ".$joins['condition'][$i]." ";
        }
      }
  }
  //PARAM CONDITIONS
  if(!empty($params['conditions'])){
    if(is_array($params['conditions'])){
      $requette .=" WHERE ";
      foreach ($params['conditions'] as $key => $value){$requette .="$key = '$value' AND ";}
      $requette=substr($requette,0,-4); // suprime le dernier AND du requette
    }else{$requette .= $params['conditions']." ";}
  }
  //PARAM ORDER
  if(!empty($params['order'])){
    $requette .="ORDER BY ";
    if(is_array($params['order'])){
      foreach ($params['order'] as $key => $value){$requette .="`$key` $value";}
    }else{$requette .= $params['order']." ";}
  }
  //LIMIT
  $requette.= $limit;
  //EXECUTION DU REQUETE VIA FONCTION QUERY
  if (($res = $this->query($requette)) !== FALSE){
    if($res=='0'){ return "Erreur Find";} // I HAVE ERROR SQL
    else{ 
      $row = $res->fetchAll(PDO::FETCH_ASSOC);
      if(count($row)==0){return "empty";} // IF QUERY RETURN EMPTY
    }
  }
  return $row; // RETURN VALUES AS ARRAY
}
}
/*==============================================
                    Debug
================================================*/
function debug($debug=NULL){
  echo "<pre>";
  echo "<h2><strong>".__FILE__."</strong></h2><br>";
  print_r($debug);
  echo "</pre>";
}

?>