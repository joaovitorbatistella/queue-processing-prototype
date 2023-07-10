<?php

namespace Model;

use database\DBConnection;
use PDO;
use InvalidArgumentException;
use Infra\GenericConsts;

class Subscriber
{
    private object $Conn;
    const TABLE = 'subscribers';

    /**
     * UsuariosRepository constructor.
     */
    public function __construct()
    {
        $this->Conn = new DBConnection();
    }

    /**
     * @param $table
     * @return Array
     */
    public function getAllUsers($table)
    {
        if ($table) {
            $sql = 'SELECT * FROM ' . $table;
            $stmt = $this->getConn()->getDb()->query($sql);
            if($stmt) {
                $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (is_array($row) && count($row) > 0) {
                    return $row;
                }
            }
            header("HTTP/1.1 406 Not Acceptable");
            throw new InvalidArgumentException(GenericConsts::MSG_TABLE_NOT_FOUND);
        }
        throw new InvalidArgumentException(GenericConsts::MSG_ERRO_WITHOUT_RETURN);
    }

    /**
     * @param $login
     * @return int
     */
    public function getRegisterByLogin($login)
    {
        $sql = 'SELECT * FROM ' . self::TABLE . ' WHERE login = :login';
        $stmt = $this->Conn->getDb()->prepare($sql);
        $stmt->bindParam(':login', $login);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * @param $username
     * @return int
     */
    public function getUserByUsername($username)
    {
        $sql = "SELECT codigo, nome, nome_de_usuario FROM " . self::TABLE . " WHERE nome_de_usuario = '". $username."'";
        $stmt = $this->getConn()->getDb()->query($sql);

        if($stmt) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row;
        } header("HTTP/1.1 406 Not Acceptable");
        throw new InvalidArgumentException(GenericConsts::MSG_ERRO_WITHOUT_RETURN);        
    }
    

    /**
     * @param $payload
     * @return int
     */
    public function enQueue($payload)
    {
        try {
            $sqlInsert = 'INSERT INTO jobs (payload, created_at, updated_at) VALUES (:payload, :created_at, :updated_at)';
            // $this->Conn->getDb()->beginTransaction();
            $stmt = $this->Conn->getDb()->prepare($sqlInsert);
            $stmt->bindParam(':payload', $payload);
            $stmt->bindParam(':created_at', (new \DateTime())->format('Y-m-d H:i:s'));
            $stmt->bindParam(':updated_at', (new \DateTime())->format('Y-m-d H:i:s'));
            $stmt->execute();
            return $stmt->rowCount();
        } catch (\PDOException $e) {
            // rollback the transaction
            $this->Conn->getDb()->rollBack();

            // show the error message
            die($e->getMessage());
        }
        
    }

    /**
     * @param $payload
     * @return int
     */
    public function insertFromQueue($id, $payload)
    {
        try {
            $sqlInsert = 'INSERT INTO '. self::TABLE .' (email, name, phone, created_at, updated_at) VALUES (:email, :name, :phone, :created_at, :updated_at)';
            // $this->Conn->getDb()->beginTransaction();

            $stmt = $this->Conn->getDb()->prepare($sqlInsert);
            $stmt->bindParam(':email', $payload->email);
            $stmt->bindParam(':name', $payload->name);
            $stmt->bindParam(':phone', $payload->phone);
            $stmt->bindParam(':created_at', (new \DateTime())->format('Y-m-d H:i:s'));
            $stmt->bindParam(':updated_at', (new \DateTime())->format('Y-m-d H:i:s'));
            $stmt->execute();
            $deleted = $this->Conn->delete('jobs', $id);
            var_dump($deleted);
            return $deleted;
        } catch (\PDOException $e) {
            echo $e->getMessage();
            // rollback the transaction
            $this->Conn->getDb()->rollBack();

            // show the error message
            die($e->getMessage());
        }
        
    }

    /**
     * @param $id
     * @param $data
     * @return int
     */
    public function update($id, $data)
    {
        $id = (int)$id;
        $sqlUpdate = 'UPDATE ' . self::TABLE . ' SET name = :name, email = :email, phone = :phone WHERE id = :id';
        $this->Conn->getDb()->beginTransaction();
        $stmt = $this->Conn->getDb()->prepare($sqlUpdate);
        $stmt->bindParam(':id', $id);
        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':email', $data['email']);
        $stmt->bindValue(':phone', $data['phone']);
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
