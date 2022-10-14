<?php

class Membre
{

    /**
     * Ajout d'un membre avec vérification unicité sur nom, prénom et génération du login
     * @param string $nom
     * @param string $prenom
     * @param string $email
     * @param string $reponse
     * @return bool
     */
    public static function ajouter(string $nom, string $prenom, string $email, string &$reponse): bool
    {
        $db = Database::getInstance();
        $ok = false;
        $sql = <<<EOD
            Select id
            From membre
            Where nom = :nom
            and prenom = :prenom
EOD;
        $db = Database::getInstance();
        $curseur = $db->prepare($sql);
        $curseur->bindParam('nom', $nom);
        $curseur->bindParam('prenom', $prenom);
        $curseur->execute();
        $ligne = $curseur->fetch();
        $curseur->closeCursor();
        if ($ligne)
            $reponse = "Ce membre existe déjà";
        else {
            // génération du login
            $login = $nom;
            $i = 2;
            do {
                $sql = <<<EOD
                      SELECT 1 
                      FROM membre
                       Where login = :login
EOD;
                $curseur = $db->prepare($sql);
                $curseur->bindParam('login', $login);
                $curseur->execute();
                $ligne = $curseur->fetch();
                $curseur->closeCursor();
                if (!$ligne) break;
                $login = $nom . $i;
                $i++;
            } while (true);

            // ajout dans la table membre, le mot de passe par défaut est 0000

            $sql = <<<EOD
        insert into membre(nom, prenom, email, login, password)
        values (:nom, :prenom, :email, :login, sha2('0000', 256));
EOD;

            $curseur = $db->prepare($sql);
            $curseur->bindParam('nom', $nom);
            $curseur->bindParam('prenom', $prenom);
            $curseur->bindParam('email', $email);
            $curseur->bindParam('login', $login);
            try {
                $curseur->execute();
                $reponse = $login;
                $ok = true;
            } catch (Exception $e) {
                $reponse = substr($e->getMessage(), strrpos($e->getMessage(), '#') + 1);
            }
        }
        return $ok;
    }

    /**
     * Retourne la liste des membres
     * @return array
     */
    public static function getLesMembres() : array {
        $db = Database::getInstance();
        $sql = <<<EOD
            Select login, concat(nom, ' ' , prenom) as nomPrenom, email, autMail, photo, ifnull(telephone, 'Non communiqué') as telephone 
            From membre
            Order by nom, prenom;
EOD;
        $curseur = $db->query($sql);
        $lesLignes = $curseur->fetchAll(PDO::FETCH_ASSOC);
        $curseur->closeCursor();
        return $lesLignes;
    }

}

class Base
{
    /**
     * Récupération des catégories à partir d'une requête SQL
     * @return array Tableau des catégories : les enregistrements sont stockés dans un tableau numérique
     */
    public static function getLesCategories(): array
    {
        $sql = <<<EOD
               Select login, concat(nom, ' ' , prenom) as nomPrenom, email, autMail, photo, ifnull(telephone, 'Non communiqué') as telephone
            From membre
            Order by nom, prenom;
EOD;
        $sql = <<<EOD
        insert into membre(nom, prenom, email, login, password)
        values (:nom, :prenom, :email, :login, sha2('0000', 256));
EOD;
        $db = Database::getInstance();
        $curseur = $db->query($sql);
        $lesLignes = $curseur->fetchAll(PDO::FETCH_NUM);
        $curseur->closeCursor();
        return $lesLignes;
    }
}


/**
 * Classe permettant de générer un tableau de données au format HTML
 *
 * @Author : Guy Verghote
 * @Version 1.3
 * @Date : 25/12/2020
 */
class Tableau
{

    private $id; // Valeur de l'attribut id associé au tableau
    private $classe; // Valeur de l'attribut classe associé au tableau
    private $lesTailles; // Tableau contenant les tailles de chaque colonne
    private $lesColonnes; // Tableau contenant le nom de chaque colonne
    private $lesClasses; // Tableau contenant la valeur de l'attribut classe associé à chaque cellule
    private $lesStyles; // Tableau contenant la valeur de l'attribut style associé à chaque cellule

    private $tableau; // contient le code HTML du tableau


    /**
     * Constructeur d'un objet Tableau
     *
     * @param array $lesTailles
     * @param array $lesColonnes
     * @param array $lesClasses
     * @param array $lesStyles
     * @param string $id
     * @param string $classe
     */

    public function __construct($lesColonnes, $lesTailles, $lesStyles, $lesClasses, $id = '', $classe = '')
    {
        $html = <<<EOD
            <div class='table-responsive'>
                <table id='$id' class='table table-condensed table-hover $classe'>
EOD;

        // définition des balises col
        $nb = count($lesTailles);
        foreach ($lesTailles as $taille) {
            if ($taille !== '') {
                $html .= "<col style='width:" . $taille . "px;'>";
            } else {
                $html .= "<col >";
            }
        }
        // définition de l'entête

        $entete = false;
        foreach ($lesColonnes as $colonne) {
            if ($colonne != '') {
                $entete = true;
                break;
            }
        }
        if ($entete) {
            $html .= "<thead><tr>";
            $nb = count($lesColonnes);
            for ($i = 0; $i < $nb; $i++) {
                $html .= <<<EOD
                    <th class=' {$lesClasses[$i]}' style='border-bottom:2px solid red; {$lesStyles[$i]}'>
                        {$lesColonnes[$i]}
                     </th>
EOD;
            }
            $html .= "</tr></thead>";
        }
        $html .= "<tbody>";
        $this->tableau = $html;
    }

    /**
     * Ajouter une ligne
     *
     * @param array $lesCellules Valeur de chaque cellule
     * @param array $lesClasses Classe associée à chaque cellule
     * @param array $lesStyles Style associé à chaque cellule
     * @param $id string Identifiant associé à la ligne
     */

    public function ajouterLigne($lesCellules, $lesStyles, $lesClasses, $id = '')
    {
        $html = "<tr id='$id'>";
        $nb = count($lesCellules);
        for ($i = 0; $i < $nb; $i++) {
            $html .= <<<EOD
                <td class='{$lesClasses[$i]}' style='{$lesStyles[$i]}'>
                    {$lesCellules[$i]}
                 </td>
EOD;
        }
        $html .= "</tr>";
        $this->tableau .= $html;

    }

    /**
     * Ferme les balises
     *
     */

    public function fermer()
    {
        $this->tableau .= "</tbody></table></div>";
    }

    /**
     * retourner le tableau au format HTML
     * @return string Code html du tableau
     *
     */

    public function getTableau()
    {
        return $this->tableau;
    }

}