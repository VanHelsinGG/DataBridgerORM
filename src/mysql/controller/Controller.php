<?php

namespace Library\DataBridger\mysql\Controller;

require '../../../vendor/autoload.php';

use Library\DataBridger\mysql\config\DatabaseConfig;
use Library\DataBridger\mysql\DAO\ControllerDAO;

/**
 * Manages the interaction with MySQL databases and provides methods for database configuration, insertion, update, deletion, and querying.
 */
class DataBridger
{
    /**
     * Configure the MySQL connection. This method should be used to set up the MySQL connection parameters.
     *
     * @param array $config An array containing configuration settings for the MySQL connection, including the following keys:
     *   - 'hostname' (string): The MySQL server hostname or IP address.
     *   - 'user' (string): The MySQL username.
     *   - 'password' (string): The MySQL password.
     *   - 'database' (string): The name of the MySQL database to connect to.
     *   - 'port' (int): The MySQL server port number.
     *
     * @return void
     */
    public static function config($config = [])
    {
        DatabaseConfig::config($config);
    }

    /**
     * Insert a new record into the specified table.
     *
     * @param string $table The name of the database table to insert the record into.
     * @param array $values An associative array representing the column names and values to insert.
     *
     * @return int|false The number of affected rows or false in case of an error.
     */
    public static function insert($table, $values = [])
    {
        return ControllerDAO::insert($table, $values);
    }

    /**
     * Update records in the specified table based on given conditions.
     *
     * @param string $table The name of the database table to update records in.
     * @param array $values An associative array representing the column names and new values to set.
     * @param array $conditions An array of conditions for updating records.
     *
     * @return int|false The number of affected rows or false in case of an error.
     */
    public static function update($table, $values = [], $conditions = [])
    {
        return ControllerDAO::update($table, $values, $conditions);
    }

    /**
     * Delete records from the specified table based on given conditions.
     *
     * @param string $table The name of the database table to delete records from.
     * @param array $conditions An array of conditions for deleting records.
     *
     * @return int|false The number of affected rows or false in case of an error.
     */
    public static function delete($table, $conditions = [])
    {
        return ControllerDAO::delete($table, $conditions);
    }

    /**
     * Select records from the specified table based on given conditions.
     *
     * @param string $table The name of the database table to select records from.
     * @param array $conditions An array of conditions for selecting records.
     *
     * @return array|false An array of selected records or false in case of an error.
     */
    public static function select($table, $conditions = [])
    {
        return ControllerDAO::select($table, $conditions);
    }

    /**
     * Execute a custom SQL query with optional parameters.
     * 
     * @param string $query The SQL query to execute.
     * @param array $params An array of parameters to bind to the query.
     *
     * @return mixed The query result or false in case of an error.
     */
    public static function query($query, $params = [])
    {
        return ControllerDAO::execute($query, $params);
    }
}

DataBridger::insert('users',[
    'name' => 'victor',
    'password' => 233412
]);