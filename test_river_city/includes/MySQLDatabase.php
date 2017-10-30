<?php

namespace includes;

/**
 * MySQLDatabase - класс для работы с базой данных.
 */
class MySQLDatabase
{
    public $lastQuery;
    private $connection;
    private $magicQuotesActive;
    private $realEscapeStringExists;

    /**
     * Конструктор.
     */
    function __construct()
    {
        $this->openConnection();

        // Установка кодировки для правильного чтения из базы данных:
        $this->query("SET NAMES utf8");

        // Проверка наличия функций экранирования:
        $this->magicQuotesActive = get_magic_quotes_gpc();
        $this->realEscapeStringExists =
            function_exists("mysqli_real_escape_string");
    }

    /**
     * Подключение к базе данных.
     */
    public function openConnection()
    {
        $this->connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS);
        if (!$this->connection) {
            die("Database connection failed: ".
                mysqli_error($this->connection));
        } else {
            $db_select = mysqli_select_db($this->connection, DB_NAME);
            if (!$db_select) {
                die("Database selection failed: ".
                    mysqli_error($this->connection));
            }
        }
    }

    /**
     * Закрытие соединения с базой данных.
     */
    public function closeConnection()
    {
        if (isset($this->connection)) {
            mysqli_close($this->connection);
            unset($this->connection);
        }
    }

    /**
     * Выполнение запроса SQL.
     */
    public function query($sql)
    {
        $this->lastQuery = $sql;
        $result = mysqli_query($this->connection, $sql);
        $this->confirmQuery($result);
        return $result;
    }

    /**
     * Освобождение результата запроса.
     */
    public function freeResult($result)
    {
        mysqli_free_result($result);
    }

    /**
     * Экранирование строк.
     */
    public function escapeValue($value)
    {
        if ($this->realEscapeStringExists) {
            if ($this->magicQuotesActive) {
                $value = stripslashes($value);
            }
            $value = mysqli_real_escape_string($this->connection, $value);
        } else {
            if (!$this->magicQuotesActive) {
                $value = addslashes($value);
            }
        }
        return $value;
    }

    /**
     * Извлечение данных из результата запроса.
     */
    public function fetchArray($result_set)
    {
        return mysqli_fetch_assoc($result_set);
    }

    /**
     * Получение количества строк в результате запроса.
     */
    public function numRows($result_set)
    {
        return mysqli_num_rows($result_set);
    }

    /**
     * Получение последнего добавленного id.
     */
    public function insertId()
    {
        return mysqli_insert_id($this->connection);
    }

    /**
     * Получение количества строк, задействованных в запросе.
     */
    public function affectedRows()
    {
        return mysqli_affected_rows($this->connection);
    }

    /**
     * Подтверждение запроса.
     */
    private function confirmQuery($result)
    {
        if (!$result) {
            $output = "Database query failed: ".
                mysqli_error($this->connection).
                "<br/><br/>";
            die($output);
        }
    }
}
?>
