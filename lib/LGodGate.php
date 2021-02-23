<?php

class LGodGate {

    public static $RANK_POINT_PLUSE = [
        "gate_1" => [
//  1     2     3     4     5     6     7     8    9      10
            "attack" => [
                300, 300, 300, 200, 200, 200, 200, 200, 150, 150,
                145, 145, 140, 140, 130, 130, 120, 120, 110, 110,
                100, 100, 100, 100, 100, 75, 75, 75, 75, 75
            ]
        ],
        "gate_2" => [
            "def" => [
                300, 300, 300, 200, 200, 200, 200, 200, 150, 150,
                145, 145, 140, 140, 130, 130, 120, 120, 110, 110,
                100, 100, 100, 100, 100, 75, 75, 75, 75, 75
            ]
        ],
        "gate_3" => [
            "vit" => [
                750, 700, 700, 700, 600, 600, 600, 450, 450, 450,
                300, 300, 300, 300, 300, 250, 250, 250, 250, 250,
                150, 150, 150, 150, 150, 100, 100, 100, 100, 100
            ]
        ],
        "gate_4" => [
            "dam" => [
                200, 200, 200, 150, 150, 150, 150, 150, 140, 140,
                140, 130, 130, 130, 120, 120, 120, 110, 110, 100,
                100, 80, 80, 80, 80, 50, 50, 50, 50, 50
            ]
        ]
    ];

    public static function getPlayerGateEffect($idPlayer, $unit) {



        $pluseEffect = [
            "vit" => "vit",
            "dam" => "damage",
            "attack" => "attack",
            "def" => "defence"
        ];

        $godP = [
            "gate_1" => "attack",
            "gate_2" => "def",
            "gate_3" => "vit",
            "gate_4" => "dam"
        ];

        $effect = [
            "vit" => 0,
            "damage" => 0,
            "attack" => 0,
            "defence" => 0
        ];

        if ($idPlayer < 1) {
            return $effect;
        }
        $playerGate = selectFromTable("*", "god_gate", "id_player = :idp", ["idp" => $idPlayer])[0];
        if (!LWorldUnit::isGodGateEffective($unit["ut"])) {
            return $effect;
        }

        for ($iii = 1; $iii < 4; $iii++) {

            if (is_null($playerGate[("gate_" . $iii)])) {
                continue;
            }

            $gate = selectFromTable("*", "god_gate_" . $iii, "id_player = :idp", ["idp" => $idPlayer])[0];
            $rank = selectFromTable(" FIND_IN_SET( gate_{$iii}, ( SELECT GROUP_CONCAT( gate_{$iii} ORDER BY gate_{$iii} DESC ) FROM god_gate ) ) AS rank ",
                            "god_gate", "id_player = :idp", ["idp" => $idPlayer])[0]["rank"];

            if (!is_numeric($rank) && $rank <= 30) {

                $effect[$pluseEffect[$godP[("gate_" . $iii)]]] += static::$RANK_POINT_PLUSE[("gate_" . $iii)][$godP[("gate_" . $iii)]][$rank];
            }

            for ($jjj = 1; $jjj < 4; $jjj++) {
                $type = $gate[("cell_" . $jjj . "_type")];
                $score = $gate[("cell_" . $jjj . "_score")];

                $effect[$type] += $score;
            }
        }

        return $effect;
    }

    

}
