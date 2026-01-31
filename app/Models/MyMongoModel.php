<?php

namespace App\Models;

use App\Libraries\Mongo;

class MyMongoModel
{
  public  $DEBUGMODE = 0;
  public  $strTableSequence = "sequences";
  public  $strTableName;
  public  $strPKAutoIncrement;
  public  $fieldStructure = array();
  public  $mongo_db;

  public function __construct($strTableName, $strPKAutoIncrement = "id")
  {
    $this->strTableName = $strTableName;
    $this->strPKAutoIncrement = $strPKAutoIncrement;
    $this->mongo_db = new Mongo("default");
  }

  /**
   * @param string $collection
   * @return mixed
   */
  public function getIndexes(string $collection)
  {
    return $this->mongo_db->listindexes($collection);
  }

  public function prepareData($data)
  {
    return $this->prepareDataForInsert($data);
  }

  public function prepareDataForInsert($data)
  {
    foreach ($this->fieldStructure as $field => $dataType) {
      $isFound = false;
      foreach ($data as $key => $val) {
        if ($key == $field) {
          $isFound = true;
          break;
        }
      }
      if (!$isFound) {
        if ($dataType == 'int' || $dataType == 'boolean') {
          $data[$field] = 0;
        } else if ($dataType == 'float' || $dataType == 'double') {
          $data[$field] = 0;
        } else if ($dataType == 'array') {
          $data[$field] = array();
        } else if ($dataType == 'geometry') {
          if (!isset($data[$field])) $data[$field] = null;
        } else if ($dataType == 'datetime') {
          $data[$field] = null;
        } else {
          $data[$field] = '';
        }
      }
    }
    return $data;
  }

  public function prepareDataForUpdate($data)
  {
    $record = array();
    foreach ($this->fieldStructure as $field => $dataType) {
      $record[$field] = $data[$field];
      if ($dataType == 'int' || $dataType == 'boolean') {
        $record[$field] = intval($record[$field]);
      } else if ($dataType == 'float' || $dataType == 'double') {
        $record[$field] = floatval($record[$field]);
      }
    }
    return $record;
  }

  public function __call($method, $params)
  {
    return $this->query($method, $params, $this);
  }

  public function query()
  {
    $args     = func_get_args();
    $fields   = null;
    $order    = null;
    $limit    = null;
    $page     = null;
    $auto_indexing = null;
    $page     = null;
    $recursive = null;

    if (count($args) == 1) {
      //single query syntax
      die("Mongo cannot execute query: " . $args[0]);
    } else if (count($args) > 1 && (strpos(strtolower($args[0]), 'findby') === 0 || strpos(strtolower($args[0]), 'findallby') === 0)) {
      $params = $args[1];
      if (strpos(strtolower($args[0]), 'findby') === 0) {
        $all  = false;
        $field = $this->_underscore(preg_replace('/findBy/i', '', $args[0]));
      } else {
        $all  = true;
        $field = $this->_underscore(preg_replace('/findAllBy/i', '', $args[0]));
      }

      $or = (strpos($field, '_or_') !== false);
      if ($or) {
        $field = explode('_or_', $field);
      } else {
        $field = explode('_and_', $field);
      }
      $off = count($field) - 1;

      if (isset($params[1 + $off])) {
        $fields = $params[1 + $off];
      }

      if (isset($params[2 + $off])) {
        $order = $params[2 + $off];
      }

      if (!array_key_exists(0, $params)) {
        return false;
      }

      $c = 0;
      $query = array();
      foreach ($field as $f) {
        if (!is_array($params[$c]) && !empty($params[$c]) && $params[$c] !== true && $params[$c] !== false) {
          $query[/*$args[2]->name . '.' . */$f] = /*'= ' . */ $params[$c];
        } else {
          $query[/*$args[2]->name . '.' . */$f] = $params[$c];
        }
        $c++;
      }
      if ($or) {
        $query = array('OR' => $query);
      }

      if ($all) {
        if (isset($params[3 + $off])) {
          $limit = $params[3 + $off];
        }

        if (isset($params[4 + $off])) {
          $page = $params[4 + $off];
        }

        if (isset($params[5 + $off])) {
          $auto_indexing = $params[5 + $off];
        }
        return $args[2]->findAll($query, $fields, $order, $limit, $page, $auto_indexing /*, $page, $recursive*/);
      } else {
        /*if (isset($params[3 + $off])) {
            $recursive = $params[3 + $off];
          }*/
        return $args[2]->find($query, $fields, $order /*, $recursive*/);
      }
    } else {
      /*if (isset($args[1]) && $args[1] === true) 
        {
          return $this->fetchAll($args[0], true);
        }*/
      return $this->fetchAll($args[0] /*, false*/);
    }
  }

  public function find($varCondition = "", $varField = null, $varOrder = "")
  {
    $cursor = $this->findAll($varCondition, $varField, $varOrder, 1);

    if ($cursor != null) {
      // Convert cursor to array
      $arrResult = iterator_to_array($cursor);

      if (count($arrResult) == 1) {
        return reset($arrResult); // Get first element
      }
    }

    return null;
  }

  public function findAll($varCondition = "", $varField = null, $varOrder = "", $intLimit = null, $page = 1, $autoIndexField = null /*set with field name */)
  {
    $strSQL = "";
    $arrResult = $this->fetchData($this->strTableName, $varCondition, $varField, $varOrder, $intLimit, $page, $autoIndexField, $strSQL);

    if ($this->DEBUGMODE > 0) $this->_debug($strSQL);

    return $arrResult;
  }


  private function fetchData($tableName, $criteria, $fields, $orderBy, $intLimit, $page, $autoIndexField, &$strSQL)
  {
    if ($fields != null) {
      if (is_array($fields)) {
        $hasMongoId = false;
        foreach ($fields as $f) {
          if ($f == '_id') {
            $hasMongoId = true;
          }
        }
        if ($hasMongoId) $hasMongoId = (isset($fields['_id']));
        if ($hasMongoId) {
          $this->mongo_db->select($fields, array('_id'));
        } else {
          $this->mongo_db->select($fields);
        }
      } else {
        $arrFields = array();
        $arrFields = explode(",", $fields);
        $hasMongoId = false;
        foreach ($arrFields as &$rowField) {
          $rowField = trim($rowField);
          if ($rowField == '_id') {
            $hasMongoId = true;
          }
        }

        if (!$hasMongoId) {
          $this->mongo_db->select($arrFields, array('_id'));
        } else {
          $this->mongo_db->select($arrFields);
        }
      }
    } else
      $this->mongo_db->select();

    if (is_array($criteria)) {
      $this->mongo_db->where($criteria);
    } else if ($criteria != null && $criteria != "") {
      // if (stripos($criteria, " OR ") !== false) {
      //   die("Criteria parameter ".$criteria." must be an array");
      // }
      //if (stripos($criteria, "(") !== false) {
      //  die("Criteria parameter ".$criteria." must be an array");
      //}
      $arrCriteria = explode("AND", $criteria);
      foreach ($arrCriteria as $crit) {
        $pattern = "/([^(<=|>=|=|<|>|<>)]*)(<=|>=|=|<|>|<>)(.+)/i";
        preg_match($pattern, $crit, $matches);
        if (count($matches) == 4) {
          $matches[2] = trim($matches[2]);
          $valueField = trim($matches[3]);
          if (strpos($valueField, "'") !== false || strpos($valueField, '"') !== false) {
            $valueField = str_replace(array("'", '"'), '', trim($matches[3]));
          } else {
            if (strpos($valueField, ".") !== false) {
              $valueField = floatval(trim($matches[3]));
            } else {
              $valueField = intval(trim($matches[3]));
            }
          }
          if ($matches[2] == '<=') {
            $this->mongo_db->where_lte(trim($matches[1]), $valueField);
          } else if ($matches[2] == '>=') {
            $this->mongo_db->where_gte(trim($matches[1]), $valueField);
          } else if ($matches[2] == '=') {
            $this->mongo_db->where(trim($matches[1]), $valueField);
          } else if ($matches[2] == '<') {
            $this->mongo_db->where_lt(trim($matches[1]), $valueField);
          } else if ($matches[2] == '>') {
            $this->mongo_db->where_gt(trim($matches[1]), $valueField);
          } else if ($matches[2] == '<>') {
            $this->mongo_db->where_ne(trim($matches[1]), $valueField);
          }
        } else {
          //check where IN
          $crit = str_replace(array(" in ", " In ", " iN "), " IN ", $crit);
          $arrCrit = explode(" IN ", $crit);
          //echo count($arrCrit)."|";die();
          if (count($arrCrit) == 2) {
            $strValues = str_replace(array('(', ')'), '', $arrCrit[1]);
            $strValues = trim($strValues);
            $inValues = explode(',', $strValues);
            $arrInValues = array();
            foreach ($inValues as $val) {
              if (strpos($val, '"') === false && strpos($val, "'") === false) {
                $arrInValues[] = intval($val);
              } else {
                $arrInValues[] = str_replace(array('"', "'"), '', $val);
              }
            }
            //print_r($arrInValues);
            $this->mongo_db->where_in(trim($arrCrit[0]), $arrInValues);
          }
        }
      }
    }
    if ($orderBy != null) {
      $arrOrderByResult = $orderBy;
      if (!is_array($orderBy)) {
        $arrOrderBy = explode(",", $orderBy);
        $arrOrderByResult = array();
        foreach ($arrOrderBy as $rowOrder) {
          $rowOrder = trim($rowOrder);
          $arrOrderBy2 = explode(" ", $rowOrder);
          if (isset($arrOrderBy2[1])) {
            $arrOrderByResult[$arrOrderBy2[0]] = $arrOrderBy2[1];
          } else {
            $arrOrderByResult[$arrOrderBy2[0]] = 1;
          }
        }
      }
      $this->mongo_db->order_by($arrOrderByResult);
    }
    $intOffset = null;
    if ($page > 1 && $intLimit != null) {
      //jump to offset 
      $intLimit = intval($intLimit);
      $intOffset = ((intval($page) - 1) * $intLimit);
    }
    if ($intLimit != null && $intLimit != 0) {
      $intLimit = intval($intLimit);
    }
    if ($intLimit != null || $intOffset != null) {
      if ($intOffset == null)
        $this->mongo_db->limit($intLimit);
      else if ($intLimit != null) {
        $this->mongo_db->limit($intLimit);
        $this->mongo_db->offset($intOffset);
      }
    }

    $query = $this->mongo_db->find($this->strTableName);

    if ($autoIndexField != null) {
      $arrData = $query;
      $arrResult = array();
      foreach ($arrData as $rowResult) {
        if (isset($rowResult[$autoIndexField])) {
          $arrResult[$rowResult[$autoIndexField]] = $rowResult;
        } else {
          $arrResult[0] = $rowResult;
        }
      }
      return $arrResult;
    }

    //$strSQL = $this->mongo_db->last_query();
    //print_r($strSQL);
    return $query;
  }

  private function _debug($message)
  {
    echo "<pre>" . $message . "</pre>";
  }


  private function _underscore($camelCasedWord)
  {
    $replace = strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $camelCasedWord));
    return $replace;
  }

  public function update($criteria = null, $data = null): bool
  {
    if ($criteria === null) {
      return false;
    }

    // Jika $data adalah string, convert menjadi array
    if (is_string($data)) {
      $data = [$data => true];
    }

    // Handle criteria
    $whereClause = [];

    if (is_array($criteria)) {
      // Criteria sudah array, pakai langsung
      $whereClause = $criteria;
    } else {
      // Criteria adalah value primary key
      // Cek apakah primary key pakai ObjectId atau integer
      $pkField = $this->strPKAutoIncrement ?? '_id';

      if ($pkField === '_id') {
        // Primary key adalah _id (ObjectId)
        if (!($criteria instanceof \MongoDB\BSON\ObjectId)) {
          $criteria = new \MongoDB\BSON\ObjectId($criteria);
        }
        $whereClause = ['_id' => $criteria];
      } else {
        // Primary key adalah field lain (integer atau string)
        $whereClause = [$pkField => $criteria];
      }
    }

    // Update langsung tanpa panggil save() untuk avoid infinite loop
    $result = $this->mongo_db->db->selectCollection($this->strTableName)
      ->updateOne(
        $whereClause,
        ['$set' => $data]
      );

    return $result->getModifiedCount() > 0 || $result->getMatchedCount() > 0;
  }

  public function update_all($arrCriteria, $arrRecord)
  {
    $this->mongo_db->set($arrRecord);
    $this->mongo_db->where($arrCriteria);
    $result = $this->mongo_db->updateMany($this->strTableName);

    return $result;
  }

  public function insert($arrRecord)
  {
    if (!isset($arrRecord[$this->strPKAutoIncrement])) {
      $arrRecord[$this->strPKAutoIncrement] = $this->findSequenceByCollectionName($this->strTableName);
    } else if ($arrRecord[$this->strPKAutoIncrement] == 0) {
      $arrRecord[$this->strPKAutoIncrement] = $this->findSequenceByCollectionName($this->strTableName);
    } else {
      $result = 0;
      $this->mongo_db->select(array('sequence_id'));
      $this->mongo_db->where('collection_name', (string)$this->strTableName);
      $query = $this->mongo_db->find($this->strTableSequence);
      $result = 0;
      foreach ($query as $row) {
        $result = intval($row['sequence_id']);
      }
      if ($result == 0) {
        $this->insertSequence($this->strTableName, intval($arrRecord[$this->strPKAutoIncrement]) + 1);
      } else {
        if ((intval($arrRecord[$this->strPKAutoIncrement]) + 1) > $result) {
          $this->mongo_db->set('sequence_id', intval($arrRecord[$this->strPKAutoIncrement]) + 1);
          $this->mongo_db->where('collection_name', (string)$this->strTableName);
          $result = $this->mongo_db->updateMany($this->strTableSequence);
        }
      }
    }

    //check to max int 32 bit
    if ((intval($arrRecord[$this->strPKAutoIncrement]) + 1) >= 2147400000) {
      //set kembali ke 1
      $this->mongo_db->set('sequence_id', 1);
      $this->mongo_db->where('collection_name', (string)$this->strTableName);
      $this->mongo_db->updateMany($this->strTableSequence);
    }

    $result = $this->mongo_db->insertOne($this->strTableName, $arrRecord);
    $result = $result->__toString();
    if ($result) {
      return $arrRecord[$this->strPKAutoIncrement];
    }
    return 0;
  }

  public function insert2(&$arrRecord)
  {
    //print_r($arrRecord);die();
    if (!isset($arrRecord[$this->strPKAutoIncrement])) {
      $arrRecord[$this->strPKAutoIncrement] = $this->findSequenceByCollectionName($this->strTableName);
    } else if ($arrRecord[$this->strPKAutoIncrement] == 0) {
      $arrRecord[$this->strPKAutoIncrement] = $this->findSequenceByCollectionName($this->strTableName);
    } else {
      $result = 0;
      $this->mongo_db->select(array('sequence_id'));
      $this->mongo_db->where('collection_name', (string)$this->strTableName);
      $query = $this->mongo_db->find($this->strTableSequence);
      $result = 0;
      foreach ($query as $row) {
        $result = intval($row['sequence_id']);
      }
      if ($result == 0) {
        $this->insertSequence($this->strTableName, intval($arrRecord[$this->strPKAutoIncrement]) + 1);
      } else {
        if ((intval($arrRecord[$this->strPKAutoIncrement]) + 1) > $result) {
          $this->mongo_db->set('sequence_id', intval($arrRecord[$this->strPKAutoIncrement]) + 1);
          $this->mongo_db->where('collection_name', (string)$this->strTableName);
          $result = $this->mongo_db->updateMany($this->strTableSequence);
        }
      }
    }

    //check to max int 32 bit
    if ((intval($arrRecord[$this->strPKAutoIncrement]) + 1) >= 2147400000) {
      //set kembali ke 1
      $this->mongo_db->set('sequence_id', 1);
      $this->mongo_db->where('collection_name', (string)$this->strTableName);
      $this->mongo_db->updateMany($this->strTableSequence);
    }

    $result = $this->mongo_db->insertOne($this->strTableName, $arrRecord);
    $id = $result->__toString();
    if ($id) {
      return $result;
    }
    return 0;
  }


  public function delete($arrCriteria)
  {
    $this->mongo_db->where($arrCriteria);
    //$this->mongo_db->timeout(-1);
    $result = $this->mongo_db->deleteOne($this->strTableName);

    return $result;
  }



  public function deleteAll()
  {
    $result = $this->mongo_db->deleteMany($this->strTableName);

    return $result;
  }

  public function findCount($varCondition = null)
  {
    if ($varCondition != null)
      $this->mongo_db->where($varCondition);

    return $this->mongo_db->count($this->strTableName);
  }

  //auto increment
  private function findSequenceByCollectionName($collectionName = '')
  {
    $result = 0;
    $this->mongo_db->select(array('sequence_id'));
    $this->mongo_db->where('collection_name', (string)$collectionName);
    $query = $this->mongo_db->find($this->strTableSequence);

    foreach ($query as $row) {
      $result = intval($row['sequence_id']);
      if ($result >= 2147400000) {
        $this->mongo_db->set('sequence_id', 1);
        $this->mongo_db->where('collection_name', (string)$this->strTableName);
        $this->mongo_db->updateMany($this->strTableSequence);
        $result = 1;
      } else {
        $this->incrementSequence($collectionName);
      }
    }
    if ($result == 0) {
      $result = 1;
      $this->insertSequence($collectionName);
    }

    return $result;
  }

  private function insertSequence($collectionName = '', $sequenceId = 2)
  {
    $data = array(
      'collection_name' => (string)$collectionName,
      'sequence_id' => $sequenceId
    );
    $result = $this->mongo_db->insertOne($this->strTableSequence, $data);
    $result = $result->__toString();

    return $result;
  }

  private function incrementSequence($collectionName = '')
  {
    $this->mongo_db->inc('sequence_id', 1);
    $this->mongo_db->where('collection_name', (string)$collectionName);
    $result = $this->mongo_db->updateMany($this->strTableSequence);

    return $result;
  }




  public function generateList($varCondition = null, $varOrder = "", $intLimit = null, $strKeyField, $strValueField, $isAddEmpty = false, $emptyData = null)
  {
    $arrResult = array();
    if (is_array($strValueField)) {
      $strValueField2 = implode(", ", $strValueField);
    } else
      $strValueField2 = $strValueField;

    if ($isAddEmpty) {
      if (is_array($emptyData)) {
        if (isset($emptyData['value'])) $val = $emptyData['value'];
        else $val = "";
        if (isset($emptyData['text'])) $text = $emptyData['text'];
        else $text = "";
        $arrResult[] = array("value" => $val, "text" => $text);
      } else
        $arrResult[] = array("value" => "", "text" => "");
    }
    $arrData = $this->findAll($varCondition, $strKeyField . "," . $strValueField2, $varOrder, $intLimit, null);
    foreach ($arrData as $rowDb) {
      if (is_array($strValueField)) {
        $text = array();
        foreach ($strValueField as $val) {
          $text[] = $rowDb[$val];
        }
        $strText = implode(" - ", $text);
        $arrResult[] = array("value" => $rowDb[$strKeyField], "text" => $strText);
      } else
        $arrResult[] = array("value" => $rowDb[$strKeyField], "text" => $rowDb[$strValueField]);
    }
    return $arrResult;
  }

  function generateListCI($varCondition = null, $varOrder = "", $intLimit = null, $strKeyField, $strValueField, $isAddEmpty = false, $emptyData = null)
  {
    $arrResult = array();
    if (is_array($strValueField)) {
      $strValueField2 = implode(", ", $strValueField);
    } else
      $strValueField2 = $strValueField;

    if ($isAddEmpty) {
      if (is_array($emptyData)) {
        $arrResult = $emptyData;
      } else {
        $arrResult[""] = "";
      }
    }
    $arrData = $this->findAll($varCondition, $strKeyField . "," . $strValueField2, $varOrder, $intLimit, null);
    foreach ($arrData as $rowDb) {
      if (is_array($strValueField)) {
        $text = array();
        foreach ($strValueField as $val) {
          $text[] = $rowDb[$val];
        }
        $strText = implode(" - ", $text);
        $arrResult[$rowDb[$strKeyField]] = $strText;
      } else {
        $arrResult[$rowDb[$strKeyField]] = $rowDb[$strValueField];
      }
    }
    return $arrResult;
  }
}
