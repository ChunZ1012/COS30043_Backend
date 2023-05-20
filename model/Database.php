<?php
class Database
{
    protected $connection = null;
    public function __construct()
    {
        try {
            $this->connection = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE_NAME);
    	
            if ( mysqli_connect_errno()) {
                throw new Exception("Could not connect to database.");   
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());   
        }			
    }
    // MSSQL ExecuteScalar
    public function execScalar($sql, $params = [])
    {
        return $this->innerSelect($sql, true, false, true, $params);
    }
    public function select($query = "" , $params = [])
    {
        return $this->innerSelect($query, false, false, false, $params);
    }
    public function selectArray($query = "" , $params = [])
    {
        return $this->innerSelect($query, false, true, false, $params);
    }
    public function selectFirstRow($query = "", $params = [])
    {
        return $this->innerSelect($query, true, false, false, $params);
    }
    private function innerSelect($query = "", $selectOne, $returnArray = false, $execScalar = false, $params = [])
    {
        try 
        {
            $result = false;
            $stmt = $this->executeStatement($query , $params);
            if($selectOne) 
            {
                if ($execScalar) $result = $stmt->get_result()->fetch_array()[0];
                else $result = $stmt->get_result()->fetch_assoc();
            }
            else 
            {
                if($returnArray) $result = $stmt->get_result()->fetch_all(MYSQLI_NUM);
                else $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            }
            $stmt->close();

            return $result;
        } 
        catch(Exception $e) 
        {
            throw New Exception( $e->getMessage() );
        }
        return false;
    }
    public function insert($query = "", $params = [])
    {
        return $this->innerInsertOrUpdate($query, true, $params);
    }
    public function update($query = "", $params = [])
    {
        return $this->innerInsertOrUpdate($query, false, $params);
    }
    public function delete($query = "", $params = [])
    {
        try
        {
            $stmt = $this->executeStatement($query, $params);
            $result = $stmt->affected_rows;
            $stmt->close();
            return $result;
        }
        catch (Exception $e)
        {
            throw New Exception( $e->getMessage() );
        }
    }
    private function innerInsertOrUpdate($query = "", $isInsert = true, $params = [])
    {
        try 
        {
            $stmt = $this->executeStatement($query , $params);
            $result = $isInsert ? $stmt->insert_id : $stmt->affected_rows;
            $stmt->close();
            return $result;
        } 
        catch(Exception $e) 
        {
            throw New Exception( $e->getMessage() );
        }
        return false;
    }
    private function executeStatement($query = "" , $params = [])
    {
        try {
            $stmt = $this->connection->prepare( $query );
            if($stmt === false) {
                throw New Exception("Unable to do prepared statement: " . $query);
            }
            if( $params ) {
                $parameters = array_slice($params, 1);
                $stmt->bind_param($params[0], ...$parameters);
            }
            $stmt->execute();
            return $stmt;
        } catch(Exception $e) {
            throw New Exception( $e->getMessage() );
        }	
    }
}