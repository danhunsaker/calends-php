<?php



class Schema
{
    protected static $schema = null;

    public static function __callStatic($method, $args)
    {
        if (is_null(static::$schema)) static::$schema = DB::schema();

        return call_user_func_array([static::$schema, $method], $args);
    }
}
