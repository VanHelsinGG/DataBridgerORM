<?php

namespace Library\DataBridger\mysql\Connector;

require '../../../vendor/autoload.php';

use Exception;
use mysqli;
use mysqli_sql_exception;
use InvalidArgumentException;
use Library\DataBridger\mysql\config\DatabaseConfig;

/**
 * Manages the MySQL database connection and provides methods for executing queries.
 */
class Connector
{
    private $db;
    private $database = '';

    /**
     * Initializes a new database connection using the provided configuration.
     *
     * @throws InvalidArgumentException If the MySQL connection configuration is missing or incorrect.
     * @throws mysqli_sql_exception If there is an issue with the MySQL connection.
     */
    public function __construct()
    {
        try {
            $config = DatabaseConfig::read();

            if ($config === null) {
                throw new InvalidArgumentException('Use DataBridger::config to configure the MySQL connection');
            }

            $this->database = $config['database'];

            $this->db = new mysqli(
                $config['hostname'],
                $config['user'],
                $config['password'],
                '',
                $config['port']
            );

            if ($this->db->connect_error) {
                throw new mysqli_sql_exception('MySQL connection error: ' . $this->db->connect_error);
            }

            $this->ensureDatabase($this->database);

            // Now, reconnect using the selected database
            $this->db = new mysqli(
                $config['hostname'],
                $config['user'],
                $config['password'],
                $this->database,
                $config['port']
            );

            if ($this->db->connect_error) {
                throw new mysqli_sql_exception('MySQL connection error: ' . $this->db->connect_error);
            }
        } catch (mysqli_sql_exception $e) {
            throw $e;
        }
    }

    /**
     * Checks if the MySQL connection is active.
     *
     * @return bool True if the connection is active, false otherwise.
     */
    public function connectionActive()
    {
        return $this->db->ping();
    }

    /**
     * Closes the current MySQL database connection.
     *
     * @return bool True if the connection was successfully closed, false otherwise.
     */
    public function close()
    {
        return $this->db->close();
    }

    /**
     * Ensure that selected database exists. If not, create then.
     *
     * @param string $database Database's name.
     * 
     * @throws mysqli_sql_exception If there is an issue with que database creations.
     * @throws Exception If there is no database selected.
     * 
     * @return bool True if the database exists or was been created, false otherwise.
     */
    private function ensureDatabase($database)
    {

        $result = $this->db->query("SHOW DATABASES LIKE '$database'");

        if ($result->num_rows == 0) {
            $sql = "CREATE DATABASE $database";
            if ($this->db->query($sql) === true) {
                return 1;
            } else {
                throw new mysqli_sql_exception("Error creating database", 1);
            }
        } else {
            return 1;
        }

        return 0;
    }

    public function ensureTable($table)
    {
        $sql = "SHOW TABLES LIKE '$table'";
        $result = $this->db->query($sql);

        if ($result->num_rows > 0) {
            return 1;
        }

        return 0;
    }

    /**
     * Executes a custom SQL query with optional parameters.
     *
     * @param string $query The SQL query to execute.
     * @param array $params An array of parameters to bind to the query:
     *   - 'column' => value
     *
     * @throws InvalidArgumentException If no database is selected.
     * @throws mysqli_sql_exception If there is an issue with the query execution.
     *
     * @return mixed The query result or false in case of an error.
     */
    public function query($query, array $params = [])
    {
        try {
            $stmt = $this->db->prepare($query);


            if (!$stmt) {
                throw new mysqli_sql_exception('Error preparing MYSQL query: ' . $this->db->error);
            }

            if (!empty($params)) {
                $types = "";
                $paramRefs = []; 

                foreach ($params as &$param) {
                    if (is_int($param)) {
                        $types .= "i";
                    } elseif (is_double($param)) {
                        $types .= "d";
                    } else {
                        $types .= "s";
                    }

                    $paramRefs[] = &$param; 
                }

                array_unshift($paramRefs, $types);
                die(var_dump($paramRefs));

                call_user_func_array(array($stmt, 'bind_param'), $paramRefs);
            }

            $result = $stmt->execute();

            if (!$result) {
                throw new mysqli_sql_exception('Error executing the query: ' . $stmt->error);
            }

            // Return rows for SELECT queries
            if ($stmt->field_count > 0) {
                $resultSet = $stmt->get_result();
                $rows = [];

                while ($row = $resultSet->fetch_assoc()) {
                    $rows[] = $row;
                }

                $stmt->close();
                return $rows;
            } else { // Return the number of affected rows for INSERT, UPDATE, DELETE...
                $affectedRows = $stmt->affected_rows;
                $stmt->close();
                return $affectedRows;
            }
        } catch (mysqli_sql_exception $e) {
            throw $e;
        }
    }

    /**
     * Retrieves the error message from the last executed query.
     *
     * @return string The error message, if available, or an empty string.
     */
    public function errorMessage()
    {
        return $this->db->error;
    }
}
