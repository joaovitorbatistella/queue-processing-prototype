<?php

namespace database;

use InvalidArgumentException;
use PDO;
use PDOException;
use Infra\GenericConsts;

class DBConnection
{
    private object $db;

    /**
     * Postgres constructor.
     */
    public function __construct()
    {
        $this->db = $this->setDB();
    }

    /**
     * @return PDO
     */
    public function setDB()
    {
        try {
            return new PDO(
              'mysql:host=' . HOST . '; port=' . PORT . '; dbname=' . DATABASE, USERNAME, PASSWORD
            );
        } catch (PDOException $exception) {
            throw new PDOException($exception->getMessage());
        }
    }

    /**
     * @param $table
     * @param $id
     * @return string
     */
    public function delete($table, $id)
    {
        $sqlDelete = 'DELETE FROM ' . $table . ' WHERE codigo = :id';
        if ($table && $id) {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare($sqlDelete);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $this->db->commit();
                return GenericConsts::MSG_DELETADO_SUCESSO;
            }
            $this->db->rollBack();
            throw new InvalidArgumentException(GenericConsts::MSG_ERRO_WITHOUT_RETURN);
        }
        throw new InvalidArgumentException(GenericConsts::MSG_ERRO_GENERICO);
    }

    /**
     * @param $table
     * @return array
     */
    public function getAll($table)
    {
        if ($table) {
            $sql = 'SELECT * FROM ' . $table;
            $stmt = $this->db->query($sql);
            if($stmt) {
                $row = $stmt->fetchAll($this->db::FETCH_ASSOC);
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
     * @param $table
     * @param $id
     * @return mixed
     */
    public function getOneByKey($table, $id)
    {
        if ($table && $id) {
            $sql = 'SELECT * FROM ' . $table . ' WHERE codigo = :id';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $row = $stmt->rowCount();
            if ($row === 1) {
                return $stmt->fetch($this->db::FETCH_ASSOC);
            }
            throw new InvalidArgumentException(GenericConsts::MSG_ERRO_WITHOUT_RETURN);
        }

        throw new InvalidArgumentException(GenericConsts::MSG_ERRO_ID_OBRIGATORIO);
    }

    /**
     * @return object|PDO
     */
    public function getDb()
    {
        return $this->db;
    }
}