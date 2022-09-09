<?php

class Database
{
    private static $_instance; // stocke l'adresse de l'unique objet instanciable

    /**
     * La méthode statique qui permet d'instancier ou de récupérer l'instance unique
     **/
    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
			$dbHost = 'localhost';
            $dbUser = 'root';
            $dbPassword = '';
            $dbBase =  'vds';
            $dbPort = 3306;
            try {
                $chaine = "mysql:host=$dbHost;dbname=$dbBase;port=$dbPort";
                $db = new PDO($chaine, $dbUser, $dbPassword);
                $db->exec("SET NAMES 'utf8'");
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$_instance = $db;
            } catch (PDOException $e) { // à personnaliser
               echo "Accès à la base de données impossible, vérifiez les paramètres de connexion";
               exit();
            }
        }
        return self::$_instance;
    }
}