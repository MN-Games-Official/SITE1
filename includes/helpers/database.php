<?php
/**
 * EduWrite AI - Database Helper Functions
 * Convenient wrappers around PDO for common database operations.
 */

require_once __DIR__ . '/../../config/database.php';

/**
 * Get the PDO connection instance (shorthand).
 */
function db(): PDO
{
    return Database::getConnection();
}

/**
 * Execute a query with prepared statement parameters.
 *
 * @param string $sql  SQL query with named or positional placeholders
 * @param array  $params Bind parameters
 * @return PDOStatement
 */
function query(string $sql, array $params = []): PDOStatement
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

/**
 * Fetch a single row from a query.
 *
 * @param string $sql
 * @param array  $params
 * @return array|null Associative array or null if not found
 */
function fetch(string $sql, array $params = []): ?array
{
    $stmt = query($sql, $params);
    $row  = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row !== false ? $row : null;
}

/**
 * Fetch all rows from a query.
 *
 * @param string $sql
 * @param array  $params
 * @return array<int, array>
 */
function fetchAll(string $sql, array $params = []): array
{
    return query($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Insert a row into a table and return the last insert ID.
 *
 * @param string $table Table name
 * @param array  $data  Column => value pairs
 * @return string Last insert ID
 */
function insert(string $table, array $data): string
{
    if (empty($data)) {
        throw new \InvalidArgumentException('Insert data cannot be empty.');
    }

    $table   = quoteIdentifier($table);
    $columns = array_keys($data);
    $placeholders = array_map(fn(string $col): string => ':' . $col, $columns);

    $sql = sprintf(
        'INSERT INTO %s (%s) VALUES (%s)',
        $table,
        implode(', ', array_map('quoteIdentifier', $columns)),
        implode(', ', $placeholders)
    );

    $params = [];
    foreach ($data as $key => $value) {
        $params[':' . $key] = $value;
    }

    query($sql, $params);
    return db()->lastInsertId();
}

/**
 * Update rows in a table. Returns the number of affected rows.
 *
 * @param string $table       Table name
 * @param array  $data        Column => value pairs to update
 * @param string $where       WHERE clause (e.g. "id = :id")
 * @param array  $whereParams Parameters for the WHERE clause
 * @return int Affected row count
 */
function update(string $table, array $data, string $where, array $whereParams = []): int
{
    if (empty($data)) {
        throw new \InvalidArgumentException('Update data cannot be empty.');
    }

    $table  = quoteIdentifier($table);
    $setParts = [];
    $params   = [];

    foreach ($data as $column => $value) {
        $paramName = ':set_' . $column;
        $setParts[] = quoteIdentifier($column) . ' = ' . $paramName;
        $params[$paramName] = $value;
    }

    $sql = sprintf('UPDATE %s SET %s WHERE %s', $table, implode(', ', $setParts), $where);

    $params = array_merge($params, $whereParams);
    $stmt   = query($sql, $params);

    return $stmt->rowCount();
}

/**
 * Delete rows from a table. Returns the number of affected rows.
 *
 * @param string $table       Table name
 * @param string $where       WHERE clause
 * @param array  $whereParams Parameters for the WHERE clause
 * @return int Affected row count
 */
function delete(string $table, string $where, array $whereParams = []): int
{
    $table = quoteIdentifier($table);
    $sql   = sprintf('DELETE FROM %s WHERE %s', $table, $where);
    $stmt  = query($sql, $whereParams);

    return $stmt->rowCount();
}

/**
 * Check whether a record exists in a table.
 *
 * @param string $table       Table name
 * @param string $where       WHERE clause
 * @param array  $whereParams Parameters for the WHERE clause
 * @return bool
 */
function exists(string $table, string $where, array $whereParams = []): bool
{
    $table = quoteIdentifier($table);
    $sql   = sprintf('SELECT 1 FROM %s WHERE %s LIMIT 1', $table, $where);
    $stmt  = query($sql, $whereParams);

    return $stmt->fetchColumn() !== false;
}

/**
 * Count records in a table.
 *
 * @param string $table       Table name
 * @param string $where       WHERE clause (optional, omit for all rows)
 * @param array  $whereParams Parameters for the WHERE clause
 * @return int
 */
function count_rows(string $table, string $where = '1=1', array $whereParams = []): int
{
    $table = quoteIdentifier($table);
    $sql   = sprintf('SELECT COUNT(*) FROM %s WHERE %s', $table, $where);
    $stmt  = query($sql, $whereParams);

    return (int)$stmt->fetchColumn();
}

/**
 * Paginate query results.
 *
 * @param string $sql      Base SQL query (without LIMIT/OFFSET)
 * @param array  $params   Bind parameters
 * @param int    $page     Current page (1-based)
 * @param int    $perPage  Items per page
 * @return array{data: array, pagination: array}
 */
function paginate(string $sql, array $params = [], int $page = 1, int $perPage = 20): array
{
    $page    = max(1, $page);
    $perPage = max(1, min($perPage, defined('MAX_PAGE_SIZE') ? MAX_PAGE_SIZE : 100));
    $offset  = ($page - 1) * $perPage;

    // Count total rows using a sub-query wrapper
    $countSql  = 'SELECT COUNT(*) FROM (' . $sql . ') AS _paginate_count';
    $countStmt = query($countSql, $params);
    $total     = (int)$countStmt->fetchColumn();

    $totalPages = $total > 0 ? (int)ceil($total / $perPage) : 1;

    // Fetch current page data
    $pagedSql = $sql . ' LIMIT :_limit OFFSET :_offset';
    $stmt     = db()->prepare($pagedSql);

    foreach ($params as $key => $value) {
        if (is_int($value)) {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        } else {
            $stmt->bindValue($key, $value);
        }
    }

    $stmt->bindValue(':_limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':_offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        'data' => $data,
        'pagination' => [
            'current_page'  => $page,
            'per_page'      => $perPage,
            'total'         => $total,
            'total_pages'   => $totalPages,
            'has_prev'      => $page > 1,
            'has_next'      => $page < $totalPages,
            'from'          => $total > 0 ? $offset + 1 : 0,
            'to'            => min($offset + $perPage, $total),
        ],
    ];
}

/**
 * Begin a database transaction.
 */
function beginTransaction(): bool
{
    return Database::beginTransaction();
}

/**
 * Commit the current transaction.
 */
function commit(): bool
{
    return Database::commit();
}

/**
 * Roll back the current transaction.
 */
function rollback(): bool
{
    return Database::rollback();
}

/**
 * Build a WHERE clause and parameter array from an associative array of conditions.
 * All conditions are combined with AND.
 *
 * @param array $conditions Column => value pairs (simple equality)
 * @return array{clause: string, params: array}
 */
function buildWhereClause(array $conditions): array
{
    if (empty($conditions)) {
        return ['clause' => '1=1', 'params' => []];
    }

    $parts  = [];
    $params = [];

    foreach ($conditions as $column => $value) {
        $paramName = ':where_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $column);

        if ($value === null) {
            $parts[] = quoteIdentifier($column) . ' IS NULL';
        } elseif (is_array($value)) {
            // IN clause
            $inParams = [];
            foreach ($value as $i => $v) {
                $inKey = $paramName . '_' . $i;
                $inParams[]   = $inKey;
                $params[$inKey] = $v;
            }
            $parts[] = quoteIdentifier($column) . ' IN (' . implode(', ', $inParams) . ')';
        } else {
            $parts[] = quoteIdentifier($column) . ' = ' . $paramName;
            $params[$paramName] = $value;
        }
    }

    return [
        'clause' => implode(' AND ', $parts),
        'params' => $params,
    ];
}

/**
 * Quote a database identifier (table or column name) to prevent SQL injection.
 * Only allows alphanumeric characters and underscores.
 *
 * @param string $identifier
 * @return string
 */
function quoteIdentifier(string $identifier): string
{
    if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $identifier)) {
        throw new \InvalidArgumentException(
            'Invalid identifier: ' . $identifier . '. Only alphanumeric characters and underscores are allowed.'
        );
    }
    return '`' . $identifier . '`';
}
