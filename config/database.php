<?php
/**
 * EduWrite AI - Database Connection
 * PDO singleton with connection pooling pattern, secure defaults, and timeout settings.
 */

require_once __DIR__ . '/app.php';

class Database
{
    /** @var PDO|null Singleton PDO instance */
    private static ?PDO $connection = null;

    /** @var int Connection retry attempts */
    private static int $maxRetries = 3;

    /** @var int Milliseconds between retries */
    private static int $retryDelay = 500;

    /** @var array PDO connection options */
    private static array $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_STRINGIFY_FETCHES  => false,
        PDO::ATTR_PERSISTENT         => false,
    ];

    /**
     * Prevent instantiation.
     */
    private function __construct() {}

    /**
     * Prevent cloning.
     */
    private function __clone() {}

    /**
     * Prevent unserialization.
     */
    public function __wakeup()
    {
        throw new \RuntimeException('Cannot unserialize a singleton.');
    }

    /**
     * Build the DSN string from configuration constants.
     */
    private static function buildDsn(): string
    {
        return sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_PORT,
            DB_NAME,
            DB_CHARSET
        );
    }

    /**
     * Get the PDO connection instance.
     * Creates a new connection if one does not exist or the existing one is stale.
     *
     * @return PDO
     * @throws \RuntimeException If connection cannot be established after retries
     */
    public static function getConnection(): PDO
    {
        if (self::$connection !== null) {
            try {
                self::$connection->query('SELECT 1');
                return self::$connection;
            } catch (\PDOException $e) {
                self::$connection = null;
            }
        }

        return self::connect();
    }

    /**
     * Establish a new database connection with retry logic.
     *
     * @return PDO
     * @throws \RuntimeException
     */
    private static function connect(): PDO
    {
        $dsn     = self::buildDsn();
        $options = self::$options;

        if (defined('DB_CONNECT_TIMEOUT')) {
            $options[PDO::ATTR_TIMEOUT] = DB_CONNECT_TIMEOUT;
        }

        $lastException = null;

        for ($attempt = 1; $attempt <= self::$maxRetries; $attempt++) {
            try {
                $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

                $pdo->exec("SET NAMES '" . DB_CHARSET . "' COLLATE '" . DB_COLLATION . "'");
                $pdo->exec('SET SESSION sql_mode = "STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION"');

                if (defined('DB_READ_TIMEOUT')) {
                    $pdo->exec('SET SESSION wait_timeout = ' . (int)DB_READ_TIMEOUT);
                    $pdo->exec('SET SESSION net_read_timeout = ' . (int)DB_READ_TIMEOUT);
                    $pdo->exec('SET SESSION net_write_timeout = ' . (int)DB_READ_TIMEOUT);
                }

                self::$connection = $pdo;
                return self::$connection;
            } catch (\PDOException $e) {
                $lastException = $e;

                if ($attempt < self::$maxRetries) {
                    usleep(self::$retryDelay * 1000 * $attempt);
                }
            }
        }

        self::logConnectionError($lastException);

        throw new \RuntimeException(
            'Database connection failed after ' . self::$maxRetries . ' attempts: ' .
            (APP_DEBUG ? $lastException->getMessage() : 'Please try again later.')
        );
    }

    /**
     * Close the current connection and reset the singleton.
     */
    public static function disconnect(): void
    {
        self::$connection = null;
    }

    /**
     * Reset the connection (close and reopen on next getConnection call).
     */
    public static function reconnect(): PDO
    {
        self::disconnect();
        return self::getConnection();
    }

    /**
     * Check whether a connection is currently active.
     */
    public static function isConnected(): bool
    {
        if (self::$connection === null) {
            return false;
        }

        try {
            self::$connection->query('SELECT 1');
            return true;
        } catch (\PDOException $e) {
            self::$connection = null;
            return false;
        }
    }

    /**
     * Begin a transaction on the current connection.
     */
    public static function beginTransaction(): bool
    {
        return self::getConnection()->beginTransaction();
    }

    /**
     * Commit the current transaction.
     */
    public static function commit(): bool
    {
        return self::getConnection()->commit();
    }

    /**
     * Roll back the current transaction.
     */
    public static function rollback(): bool
    {
        $conn = self::getConnection();
        if ($conn->inTransaction()) {
            return $conn->rollBack();
        }
        return false;
    }

    /**
     * Check whether a transaction is currently active.
     */
    public static function inTransaction(): bool
    {
        return self::getConnection()->inTransaction();
    }

    /**
     * Execute a callback inside a transaction.
     * Automatically commits on success or rolls back on exception.
     *
     * @param callable $callback Receives the PDO instance as its argument
     * @return mixed The return value of the callback
     * @throws \Throwable Re-throws the exception after rollback
     */
    public static function transaction(callable $callback): mixed
    {
        $pdo = self::getConnection();
        $pdo->beginTransaction();

        try {
            $result = $callback($pdo);
            $pdo->commit();
            return $result;
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Get information about the current connection for diagnostics.
     *
     * @return array<string, mixed>
     */
    public static function getConnectionInfo(): array
    {
        if (!self::isConnected()) {
            return ['connected' => false];
        }

        $pdo = self::getConnection();

        return [
            'connected'      => true,
            'driver'         => $pdo->getAttribute(PDO::ATTR_DRIVER_NAME),
            'server_version' => $pdo->getAttribute(PDO::ATTR_SERVER_VERSION),
            'server_info'    => $pdo->getAttribute(PDO::ATTR_SERVER_INFO),
            'host'           => DB_HOST,
            'database'       => DB_NAME,
            'charset'        => DB_CHARSET,
        ];
    }

    /**
     * Log a connection error to the application log file.
     */
    private static function logConnectionError(?\PDOException $e): void
    {
        if ($e === null) {
            return;
        }

        $logDir = defined('LOG_PATH') ? LOG_PATH : dirname(__DIR__) . '/storage/logs';

        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $message = sprintf(
            "[%s] Database connection error: %s (Code: %s) Host: %s, DB: %s\n",
            date('Y-m-d H:i:s'),
            $e->getMessage(),
            $e->getCode(),
            DB_HOST,
            DB_NAME
        );

        error_log($message, 3, $logDir . '/database.log');
    }
}
