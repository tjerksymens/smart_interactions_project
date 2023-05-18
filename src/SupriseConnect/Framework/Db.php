<?php

namespace SupriseConnect\Framework;

class Db
{
    private static $db;

    public static function getInstance()
    {
        if (self::$db !== null) {
            return self::$db;
        } else {
            self::$db = new \PDO('mysql:host=ID393251_SupriseConnect.db.webhosting.be;dbname=ID393251_SupriseConnect', "ID393251_SupriseConnect", "ConnectionProject69!");
            return self::$db;
        }
    }
}
