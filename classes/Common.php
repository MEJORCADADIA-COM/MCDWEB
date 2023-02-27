<?php

$path = realpath(dirname(__FILE__));
include_once($path . "/../lib/Session.php");
Session::init();
include_once($path . "/../lib/Database.php");
include_once($path . "/../helper/Format.php");

class Common
{
    public readonly Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function insert(string $table, array $data)
    {
        $query = "INSERT INTO {$table} {$this->prepareInsertData($data)}";
        return $this->db->query($query, array_values($data));
    }

    public function bulkInsert(string $table, array $columns, array $data)
    {
        $placeholders = [];
        foreach ($data as $row) {
            $placeholders[] = "(" . implode(",", array_fill(0, count($columns), "?")) . ")";
        }
        $query = "INSERT INTO {$table} (".implode(',', $columns).") VALUES " . implode(", ", $placeholders);

        $values = [];
        foreach ($data as $row) {
            $values = array_merge($values, $row);
        }
        return $this->db->query($query, $values);
    }

    public function update(string $table, array $data, string $cond, array $params, $hasModifiedColumn = true, $modifiedColumnName = 'modified')
    {
        if ($hasModifiedColumn) {
            $data = [...$data, $modifiedColumnName => date('Y-m-d h:i:s')];
        }

        $dataString = $this->prepareUpdateData($data);

        $query = "UPDATE {$table} SET {$dataString} WHERE $cond";
        $result = $this->db->query($query, [...$data, ...$params]);

        return $result;
    }

    public function get(string $table, ?string $cond = null, array $params = [], array $columns = [], ?string $orderBy = null, ?string $order = 'asc')
    {
        $query = "SELECT " . $this->formatColumns($columns) . " FROM $table" . $this->getConditionString($cond) . ($orderBy ? " ORDER BY {$orderBy} {$order}" : "");

        return $this->db->query($query, $params)->fetchAll();
    }

    public function paginate(string $table, ?string $cond = null, array $params = [], array $columns = [], int $limit = 10, ?string $orderBy = null, ?string $order = 'asc')
    {
        $offset = !empty($_GET['page']) && is_numeric($_GET['page']) ? ((int)$_GET['page'] - 1) * $limit : 0;

        $query = "SELECT " . $this->formatColumns($columns) . " FROM $table" . $this->getConditionString($cond) . ($orderBy ? " ORDER BY {$orderBy} {$order}" : "") . " LIMIT {$offset}, {$limit}";

        return $this->db->query($query, $params)->fetchAll();
    }

    public function first(string $table, string $cond = null, array $params = [], array $columns = [], ?string $orderBy = null, ?string $order = 'asc')
    {
        $query = "SELECT " . $this->formatColumns($columns) . " FROM $table" . $this->getConditionString($cond) . ($orderBy ? " ORDER BY {$orderBy} {$order}" : "") . " Limit 0,1";

        return $this->db->query($query, $params)->fetch();
    }

    public function leftJoin(string $table1, string $table2, string $join_condition, string $cond = null, array $params = [], array $columns = [], ?string $orderBy = null, ?string $order = 'asc', ?string $groupBy = null)
    {
        $query = "SELECT " . $this->formatColumns($columns) . " FROM $table1 LEFT JOIN $table2 ON $join_condition" . $this->getConditionString($cond) . ($groupBy ? " GROUP BY {$groupBy}" : "") . ($orderBy ? " ORDER BY {$orderBy} {$order}" : "");

        return $this->db->query($query, $params)->fetchAll();
    }

    public function leftJoinPaginate(string $table1, string $table2, string $join_condition, string $cond = null, array $params = [], array $columns = [], ?string $orderBy = null, ?string $order = 'asc', ?int $limit = null, ?string $groupBy = null)
    {
        $query = "SELECT " . $this->formatColumns($columns) . " FROM $table1 LEFT JOIN $table2 ON $join_condition" . $this->getConditionString($cond) . ($groupBy ? " GROUP BY {$groupBy}" : "") . ($orderBy ? " ORDER BY {$orderBy} {$order}" : "");

        $countQuery = "SELECT count(" . ($columns[0] ?? $table1 .'.id') . ") FROM $table1 LEFT JOIN $table2 ON $join_condition" . $this->getConditionString($cond) . ($groupBy ? " GROUP BY {$groupBy}" : "") . ($orderBy ? " ORDER BY {$orderBy} {$order}" : "");
        $count = $this->db->query($countQuery, $params)->fetchColumn();
        $totalPage = (int)ceil($count / $limit);

        $offset = !empty($_GET['page']) && is_numeric($_GET['page']) ? ((int)$_GET['page'] - 1) * $limit : 0;

        $query .= " LIMIT {$offset}, {$limit}";

        return ['data' => $this->db->query($query, $params)->fetchAll(), 'total_page' => $totalPage];
    }

    public function delete(string $table, string $cond, array $params, $file = NULL)
    {
        $query = "DELETE FROM {$table} WHERE {$cond}";
        $result = $this->db->query($query, $params);

        if ($file != NULL) {
            unlink($file);
        }
        return $result;
    }

    public function count(string $table, string $cond = null, array $params = [], array $columns = ['count(id)'])
    {
        $query = "SELECT " . $this->formatColumns($columns) . " FROM $table" . $this->getConditionString($cond);

        return $this->db->query($query, $params)->fetchColumn();
    }

    public function pageCount(string $table, string $cond = null, array $params = [], array $columns = ['count(id)'], $limit = 10)
    {
        $count = $this->count($table, $cond, $params, $columns);

        return (int)ceil($count / $limit);
    }

    public function insertId()
    {
        return $this->db->insertId();
    }

    private function prepareInsertData(array $data): string
    {
        $columns = implode(',', array_keys($data));
        $placeholders = '';

        for ($i = 0; $i < count($data); $i++) {
            $placeholders .= '?' . ($i < count($data) - 1 ? ',' : '');
        }

        return "({$columns}) VALUES ({$placeholders})";
    }

    private function prepareUpdateData(array $data): string
    {
        $dataString = '';
        $lastColumn = array_key_last($data);

        foreach ($data as $column => $value) {
            $dataString .= "{$column}=:{$column}" . ($column !== $lastColumn ? ',' : '');
        }

        return $dataString;
    }

    private function formatColumns(array $columns): string
    {
        return count($columns) > 0 ? implode(',', $columns) : "*";
    }

    private function getConditionString(?string $cond): string
    {
        return $cond !== null ? " WHERE $cond" : "";
    }
}
