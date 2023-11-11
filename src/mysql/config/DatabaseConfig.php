<?php

namespace Library\DataBridger\mysql\config;

require '../../../vendor/autoload.php';

use Exception;
use InvalidArgumentException;

/**
 * Manages the configuration settings for the MySQL connection stored in a JSON file.
 */
class DatabaseConfig
{
    /**
     * The path to the configuration file.
     */
    private const CONFIG_FILE = '../config.json';

    /**
     * Configure the MySQL connection settings and save them to the configuration file.
     *
     * @param array $config An array containing configuration settings for the MySQL connection, including the following keys:
     *   - 'hostname' (string): The MySQL server hostname or IP address.
     *   - 'user' (string): The MySQL username.
     *   - 'password' (string): The MySQL password.
     *   - 'database' (string): The name of the MySQL database to connect to.
     *   - 'port' (int): The MySQL server port number.
     *
     * @throws InvalidArgumentException If any of the required configuration keys are missing.
     * @throws Exception If there is an error saving the configuration to the file.
     */
    public static function config($config = [])
    {
        self::validateConfig($config);
        self::save($config);
    }

    /**
     * Validates the MySQL connection configuration to ensure that all required keys are present.
     *
     * @param array $config An array containing configuration settings.
     *
     * @throws InvalidArgumentException If any of the required configuration keys are missing.
     */
    private static function validateConfig($config)
    {
        $requiredKeys = ['hostname', 'user', 'password', 'database', 'port'];

        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $config)) {
                throw new InvalidArgumentException("The key '$key' is required.");
            }
        }
    }

    /**
     * Reads the MySQL connection configuration from the configuration file.
     *
     * @return array|null An array of MySQL connection configuration settings, or null if the configuration file does not exist.
     *
     * @throws Exception If there is an error decoding the configuration file.
     */
    public static function read()
    {
        try {
            if (file_exists(self::CONFIG_FILE)) {
                $json = file_get_contents(self::CONFIG_FILE);
                $config = json_decode($json, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception("Error decoding config file: " . json_last_error_msg());
                }

                return $config;
            }

            return null;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Saves the MySQL connection configuration to the configuration file.
     *
     * @param array $config An array containing MySQL connection configuration settings.
     *
     * @throws Exception If there is an error saving the configuration to the file.
     */
    private static function save($config)
    {
        try {
            $existingConfig = file_exists(self::CONFIG_FILE) ? json_decode(file_get_contents(self::CONFIG_FILE), true) : [];
            $mergedConfig = array_merge($existingConfig, $config);

            $jsonConfig = json_encode($mergedConfig, JSON_PRETTY_PRINT);

            if (file_put_contents(self::CONFIG_FILE, $jsonConfig) === false) {
                throw new Exception("Error saving config to " . self::CONFIG_FILE);
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
}
