<?php

namespace Model;

use database\DBConnection;
use PDO;
use InvalidArgumentException;
use Infra\GenericConsts;

class Ref
{
    private object $Conn;
    const TABLE = 'valor_ref';

    /**
     * RefRepository constructor.
     */
    public function __construct()
    {
        $this->Conn = new DBConnection();
        $this->DateTime = new DateTime();
    }

    /**
     * @param $table
     * @return Array
     */
    public function getAllRefs($table)
    {
        $currentDate = $this->DateTime->getNow();
        if ($table) {
            $sql = "SELECT * FROM valor_ref WHERE inicio <= '" .$currentDate."' and  fim >= '". $currentDate."'";
            $stmt = $this->getConn()->getDb()->query($sql);
            if($stmt) {
                $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (is_array($row) && count($row) > 0) {
                    return $row;
                }
            }
            header("HTTP/1.1 406 Not Acceptable");
            throw new InvalidArgumentException(GenericConsts::MSG_ERRO_WITHOUT_RETURN);
        }
        throw new InvalidArgumentException(GenericConsts::MSG_ERRO_WITHOUT_RETURN);
    }

    /**
     * @param $param
     * @return int
     */
    public function getRefsByParams($param)
    {
        if($param[0] == 'id'){
            $sql = "SELECT * FROM " . self::TABLE ." WHERE codigo = ".$param[1];
        } 
        $stmt = $this->getConn()->getDb()->query($sql);

        if($stmt) {
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $row;
        } header("HTTP/1.1 406 Not Acceptable");
        throw new InvalidArgumentException(GenericConsts::MSG_ERRO_WITHOUT_RETURN);        
    }

    /**
     * @param $id
     * @param $data
     * @return int
     */
    public function updateRef($id, $data)
    {
        $sqlUpdate = 'UPDATE ' . self::TABLE . ' SET valor = :price, inicio = :start, fim = :end WHERE codigo = :id';
        $this->Conn->getDb()->beginTransaction();
        $stmt = $this->Conn->getDb()->prepare($sqlUpdate);
        $stmt->bindParam(':id', $id);
        $stmt->bindValue(':price', $data['price']);
        $stmt->bindValue(':start', $data['start']);
        $stmt->bindValue(':end', $data['end']);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * @return Conn|object
     */
    public function getConn()
    {
        return $this->Conn;
    }
}
