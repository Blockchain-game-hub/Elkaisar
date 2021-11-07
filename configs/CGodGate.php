<?php

class CGodGate {

    static public $GateData = [
        "gate_1" => [
            "points" => 500,
            "porm" => 4
        ],
        "gate_2" => [
            "points" => 1500,
            "porm" => 10
        ],
        "gate_3" => [
            "points" => 2500,
            "porm" => 18
        ],
        "gate_4" => [
            "points" => 4000,
            "porm" => 28
        ]
    ];

    static public function getGateData() {
        $GateData = selectFromTable("*", "god_gate_req", "1");
        return [
            "gate_1" => [
                "points" => $GateData[0]["god_gate_1_points"],
                "porm" => $GateData[0]["god_gate_1_porm"]
            ],
            "gate_2" => [
                "points" => $GateData[0]["god_gate_2_points"],
                "porm" => $GateData[0]["god_gate_2_porm"]
            ],
            "gate_3" => [
                "points" => $GateData[0]["god_gate_3_points"],
                "porm" => $GateData[0]["god_gate_3_porm"]
            ],
            "gate_4" => [
                "points" => $GateData[0]["god_gate_4_points"],
                "porm" => $GateData[0]["god_gate_4_porm"]
            ]
        ];
    }

    static public $MaxVal = [
        "vit" => 100,
        "attack" => 50,
        "damage" => 50,
        "defence" => 50,
        "break" => 15,
        "anti_break" => 15,
        "strike" => 15,
        "immunity" => 15
    ];

    static public function getMaxVal() {
        $GateData = selectFromTable("*", "god_gate_max_val", "1");
        return $GateData[0];
    }

    public static $RankPointPluse = [
        "1" => [
            //  1     2     3     4     5     6     7     8    9      10
            "attack" => [
                300, 300, 300, 200, 200, 200, 200, 200, 150, 150,
                145, 145, 140, 140, 130, 130, 120, 120, 110, 110,
                100, 100, 100, 100, 100, 75, 75, 75, 75, 75
            ]
        ],
        "2" => [
            "def" => [
                300, 300, 300, 200, 200, 200, 200, 200, 150, 150,
                145, 145, 140, 140, 130, 130, 120, 120, 110, 110,
                100, 100, 100, 100, 100, 75, 75, 75, 75, 75
            ]
        ],
        "3" => [
            "vit" => [
                750, 700, 700, 700, 600, 600, 600, 450, 450, 450,
                300, 300, 300, 300, 300, 250, 250, 250, 250, 250,
                150, 150, 150, 150, 150, 100, 100, 100, 100, 100
            ]
        ],
        "4" => [
            "dam" => [
                200, 200, 200, 150, 150, 150, 150, 150, 140, 140,
                140, 130, 130, 130, 120, 120, 120, 110, 110, 100,
                100, 80, 80, 80, 80, 50, 50, 50, 50, 50
            ]
        ]
    ];
    
    static public function getRankPointPluse(){
        $Gate1 = selectFromTable("*", "god_gate_point_plus_1", "1");
        $Gate2 = selectFromTable("*", "god_gate_point_plus_2", "1");
        $Gate3 = selectFromTable("*", "god_gate_point_plus_3", "1");
        $Gate4 = selectFromTable("*", "god_gate_point_plus_4", "1");
        
        $RankPointPluse = [
            "1" => static::getRankPP($Gate1),
            "2" => static::getRankPP($Gate2),
            "3" => static::getRankPP($Gate3),
            "4" => static::getRankPP($Gate4)
        ];
        return $RankPointPluse;
    }
    
    static public function getRankPP($Gate){
        $GatePoints = [];
        foreach ($Gate as $oneRow){
            foreach ($oneRow as $oneKey => $oneVal){
                if($oneKey == "id" || $oneKey == "rank")
                    continue;
                if($oneVal == 0)
                    continue;
                if(!$GatePoints[$oneKey])
                    $GatePoints[$oneKey] = [];
               $GatePoints[$oneKey][] = $oneVal; 
            }
        }
        
        return $GatePoints;
    }

}
