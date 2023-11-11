<?php

namespace Library\DataBridger\mysql\DAO;

require '../../../vendor/autoload.php';

use Exception;
use mysqli_sql_exception;
use InvalidArgumentException;
use Library\DataBridger\mysql\Connector\Connector;

/**
 * Manages database operations, including inserting, selecting, updating, and deleting records.
 */
class ControllerDAO
{
    /**
     * Inserts a new record into the specified table.
     *
     * @param string $table The name of the database table to insert the record into.
     * @param array $values An associative array representing the column names and values to insert:
     *  - 'column' => 'value'
     *
     * @throws InvalidArgumentException If the values array is empty.
     * @throws mysqli_sql_exception If there is an issue with the INSERT query execution.
     *
     * @return int|false The number of affected rows or false in case of an error.
     */
    public static function insert($table, $values = [])
    {
        $db = new Connector();

        if (!$db->ensureTable($table)) {
            throw new Exception("The selected table don't exists.", 1);
        }

        if (empty($values)) {
            throw new InvalidArgumentException("Values array is empty. You must provide values to insert.");
        }

        $keys = implode(', ', array_keys($values));
        $placeholders = implode(', ', array_fill(0, count($values), '?'));

        $query = "INSERT INTO $table ($keys) VALUES ($placeholders)";

        try {
            $result = $db->query($query, array_values($values));

            if ($result === false) {
                throw new mysqli_sql_exception('Error executing the INSERT query: ' . $db->errorMessage());
            }

            $db->close();
            return $result;
        } catch (mysqli_sql_exception $e) {
            $db->close();
            throw $e;
        }
    }

    /**
     * Selects records from the specified table based on given conditions.
     *
     * @param string $table The name of the database table to select records from.
     * @param array $conditions An array of conditions for selecting records:
     * - 'column' => value
     *
     * @throws mysqli_sql_exception If there is an issue with the SELECT query execution.
     *
     * @return array|false An array of selected records or false in case of an error.
     */
    public static function select($table, $conditions = [])
    {
        $db = new Connector();

        if (!$db->ensureTable($table)) {
            throw new Exception("The selected table don't exists.", 1);
        }

        $query = "SELECT * FROM $table";

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(' AND ', $conditions);
        }

        try {
            $result = $db->query($query);

            if ($result === false) {
                throw new mysqli_sql_exception('Error executing the SELECT query: ' . $db->errorMessage());
            }

            $db->close();
            return $result;
        } catch (mysqli_sql_exception $e) {
            $db->close();
            throw $e;
        }
    }

    /**
     * Updates records in the specified table based on given conditions.
     *
     * @param string $table The name of the database table to update records in.
     * @param array $values An associative array representing the column names and new values to set:
     *  - 'column' => 'name'
     * @param array $conditions An array of conditions for updating records:
     * - 'column' => value
     *
     * @throws InvalidArgumentException If the values array is empty.
     * @throws mysqli_sql_exception If there is an issue with the UPDATE query execution.
     *
     * @return int|false The number of affected rows or false in case of an error.
     */
    public static function update($table, $values = [], $conditions = [])
    {
        $db = new Connector();

        if (!$db->ensureTable($table)) {
            throw new Exception("The selected table don't exists.", 1);
        }

        if (empty($values)) {
            throw new InvalidArgumentException("Values array is empty. You must provide values to update.");
        }

        $setClause = [];
        foreach ($values as $key => $value) {
            $setClause[] = "$key = ?";
        }
        $setClause = implode(', ', $setClause);

        $query = "UPDATE $table SET $setClause";

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(' AND ', $conditions);
        }

        try {
            $result = $db->query($query, array_values($values));

            if ($result === false) {
                throw new mysqli_sql_exception('Error executing the UPDATE query: ' . $db->errorMessage());
            }

            $db->close();
            return $result;
        } catch (mysqli_sql_exception $e) {
            $db->close();
            throw $e;
        }
    }

    /**
     * Deletes records from the specified table based on given conditions.
     *
     * @param string $table The name of the database table to delete records from.
     * @param array $conditions An array of conditions for deleting records:
     * - 'column' => value
     *
     * @throws mysqli_sql_exception If there is an issue with the DELETE query execution.
     *
     * @return int|false The number of affected rows or false in case of an error.
     */
    public static function delete($table, $conditions = [])
    {
        $db = new Connector();

        if (!$db->ensureTable($table)) {
            throw new Exception("The selected table don't exists.", 1);
        }

        $query = "DELETE FROM $table";

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(' AND ', $conditions);
        }

        try {
            $result = $db->query($query);

            if ($result === false) {
                throw new mysqli_sql_exception('Error executing the DELETE query: ' . $db->errorMessage());
            }

            $db->close();
            return $result;
        } catch (mysqli_sql_exception $e) {
            $db->close();
            throw $e;
        }
    }

    /**
     * Executes a custom SQL query with optional parameters.
     *
     * @param string $query The SQL query to execute.
     * @param array $params An array of parameters to bind to the query:
     * - 'column' => value
     *
     * @throws mysqli_sql_exception If there is an issue with the query execution.
     *
     * @return mixed The query result or false in case of an error.
     */
    public static function execute($query, $params = [])
    {
        $db = new Connector();
        return $db->query($query, $params);
    }
}
