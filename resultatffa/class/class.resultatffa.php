<?php

class resultatffa
{
    public static function getLesCourses(): array
    {
        $db = Database::getInstance();
        $sql = <<<EOD
            Select date, titre
            from resultatffa
order by date
EOD;
        $curseur = $db->query($sql);
        $lesLignes = $curseur->fetchAll(PDO::FETCH_ASSOC);
        $curseur->closeCursor();
        return $lesLignes;
    }
}
