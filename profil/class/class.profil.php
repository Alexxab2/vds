<?php

class Profil
{
    /**
     * @param string $nom Nom du membre
     * @param string $prenom PRénom du membre
     * @return array|bool
     */
    public static function getMembreByNomPrenom(string $nom, string $prenom): array|bool
    {
        $sql = <<<EOD
            SELECT login, email
            FROM membre  
            Where nom = :nom and prenom = :prenom
EOD;
        $db = Database::getInstance();
        $curseur = $db->prepare($sql);
        $curseur->bindParam('nom', $nom);
        $curseur->bindParam('prenom', $prenom);
        try {
            $curseur->execute();
            $ligne = $curseur->fetch(PDO::FETCH_ASSOC);
            $curseur->closeCursor();
            return $ligne;
        } catch (Exception $e) {

            return false;
        }
    }

    /**
     * retourne l'id, le login et le nom et prenom et le mdp de l'utilisateur à partir de son login
     * @param string $login
     * @return array|bool
     */
    public static function getMembreByLogin(string $login): array|bool
    {
        $sql = <<<EOD
            SELECT id, login, nom, prenom, password
            FROM membre  
            WHERE login = :login;
EOD;
        $db = Database::getInstance();
        $curseur = $db->prepare($sql);
        $curseur->bindParam('login', $login);
        try {
            $curseur->execute();
            $ligne = $curseur->fetch(PDO::FETCH_ASSOC);
            $curseur->closeCursor();
            return $ligne;
        } catch (Exception $e) {

            return false;
        }
    }

    /**
     * retourne le nom, le téléphone, la photo et l'autorisation d'affiner l'email
     * @param string $id
     * @return array|bool
     */


    public static function getMembreById(string $id): array|bool
    {
        $db = Database::getInstance();
        $sql = <<<EOD
            SELECT nom, prenom, email, telephone, photo, autMail
            FROM membre  
            WHERE id = :id;
EOD;
        $curseur = $db->prepare($sql);
        $curseur->bindParam('id', $id);
        try {
            $curseur->execute();
            $ligne = $curseur->fetch(PDO::FETCH_ASSOC);
            $curseur->closeCursor();
            return $ligne;

        } catch (Exception $e) {

            return false;
        }
    }

    /**
     * retourne le nom, le prenom, la concaténation nom et prenom,
     * email ou non communiqué si autmail = false
     * téléphone ou non renseigné si téléphone is null
     * photo ou non renseignée si photo is null
     * @return array
     */

    public static function getLesMembres(): array
    {
        $db = Database::getInstance();
        $sql = <<<EOD
            Select nom, prenom, concat(nom, ' ', prenom) as nomPrenom,   
            (CASE autMail WHEN true THEN email else 'Non communiqué' end) as email,  
            ifnull(telephone, 'Non renseigné') as telephone,
            ifnull(photo, 'Non renseignée') as photo 
            From membre 
            order by nom, prenom;
EOD;
        $curseur = $db->query($sql);
        $lesLignes = $curseur->fetchAll(PDO::FETCH_ASSOC);
        $curseur->closeCursor();
        return $lesLignes;
    }

    /**
     * sert à modifier la valeur d'une colonne (telephone, photo) d'un enregistrement de la table
     * @param string $colonne
     * @param string $valeur
     * @param int $id
     * @param string $erreur
     * @return bool
     */
    public static function modifierColonne(string $colonne, string $valeur, int $id, string &$erreur): bool
    {
        $db = Database::getInstance();
        $ok = true;
        $erreur = "";
        $sql = <<<EOD
            Update membre 
            set $colonne = :valeur
            where id = :id;
EOD;
        $curseur = $db->prepare($sql);
        $curseur->bindParam('id', $id);
        $curseur->bindParam('valeur', $valeur);
        try {
            $curseur->execute();
        } catch (Exception $e) {
            $erreur = substr($e->getMessage(), strrpos($e->getMessage(), '#') + 1);
            $ok = false;
        }
        return $ok;
    }

    public static function effacerColonne(string $colonne, int $id, string &$erreur): bool
    {
        $db = Database::getInstance();
        $ok = true;
        $erreur = "";
        $sql = <<<EOD
            Update membre 
            set $colonne = null
            where id = :id;
EOD;
        $curseur = $db->prepare($sql);
        $curseur->bindParam('id', $id);
        try {
            $curseur->execute();
        } catch (Exception $e) {
            $erreur = substr($e->getMessage(), strrpos($e->getMessage(), '#') + 1);
            $ok = false;
        }
        return $ok;
    }

    public static function enregistrerTelephone(int $id, string $telephone): int|string
    {
        $db = Database::getInstance();
        $sql = <<<EOD
            Update membre 
            set telephone = :telephone
            where id = :id;
EOD;
        $curseur = $db->prepare($sql);
        $curseur->bindParam('id', $id);
        $curseur->bindParam('telephone', $telephone);
        try {
            $curseur->execute();
            return 1;
        } catch (Exception $e) {
            return substr($e->getMessage(), strrpos($e->getMessage(), '#') + 1);
        }
    }
}