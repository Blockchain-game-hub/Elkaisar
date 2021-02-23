<?php

class APlayerHero {

    public function getAll() {
        $idPlayer = validateID($_GET["idPlayer"]);
        return json_decode( '[]', true);
    }

}
