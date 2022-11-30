<?php

namespace Omgalira\TheCodeholicPhpMvc\Db;

use Omgalira\TheCodeholicPhpMvc\Application;
use Omgalira\TheCodeholicPhpMvc\Model;

abstract class DbModel extends Model
{
    abstract public static function tableName(): string;

    abstract public function attributes(): array;

    abstract public static function primaryKey(): string;

    public function save()
    {
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $params = array_map(function($attribute) {
            return ":$attribute";
        }, $attributes);

        $statement = self::prepare(
            "INSERT INTO `$tableName` (".implode(',', $attributes).")
            VALUES (".implode(',', $params).")"
        );

        foreach ($attributes as $attribute) {
            $statement->bindValue(":$attribute", $this->{$attribute});
        }

        $statement->execute();
        return true;
    }

    public static function findOne($where)
    {
        $tableName = static::tableName();
        $attributes = array_keys($where);

        $temp = array_map(function($attr) {
            return "$attr = :$attr";
        }, $attributes);
        $temp = implode(" AND ", $temp);

        $sql = "SELECT * FROM $tableName WHERE $temp";

        $statement = self::prepare($sql);
        foreach ($where as $key => $value) {
            $statement->bindValue(":$key", $value);
        }

        $statement->execute();
        return $statement->fetchObject(static::class);
    }

    public static function prepare($sql)
    {
        return Application::$app->db->pdo->prepare($sql);
    }
}