<?php

class AArmyBuild {

    function buildArmy() {
        global $idPlayer;
        $idCity = validateID($_POST["idCity"]);
        $amount = validateID($_POST["amount"]);
        $armyType = validateGameNames($_POST["armyType"]);
        $buildingPlace = validateGameNames($_POST["buildingPlace"]);
        $worshipPlace = validateGameNames($_POST["templePlace"]);
        $divideBy = validateGameNames($_POST["divideBy"]);
        $resNeeded = LArmy::neededResources($armyType);

        $Res = [
            "food" => $resNeeded["food"] * $amount, "wood" => $resNeeded["wood"] * $amount,
            "stone" => $resNeeded["stone"] * $amount, "metal" => $resNeeded["metal"] * $amount,
            "pop" => $resNeeded["pop"] * $amount, "coin" => $resNeeded["coin"] * $amount
        ];

        if (!$idPlayer) return ["state" => "error_0"];
        if (!$idCity) return ["state" => "error_1"];
        if (!LArmy::checkIfArmyType($armyType))
                return ["state" => "error_2", "TryToHack" => TryToHack()];
        if (!LArmy::checkIfDevideBy($divideBy))
                return ["state" => "error_3", "TryToHack" => TryToHack()];
        if (!is_numeric($amount) || $amount <= 0)
                return ["state" => "error_4", "TryToHack" => TryToHack()];
        if (!LCity::isResourceTaken($Res, $idCity))
                return ["state" => "error_5", "TryToHack" => TryToHack()];

        $bBuilding = $this->checkBuilding($resNeeded["condetion"][0], $buildingPlace);
        $bStudy = $this->checkEdu($resNeeded["condetion"][1]);

        if ($bBuilding !== true) return $bBuilding;
        if ($bStudy !== true) return $bStudy;

        if ($divideBy == "none")
                return $this->buildArmyDiviedByNone($armyType, $amount, $idCity, $buildingPlace, $worshipPlace);
        if ($divideBy == "time")
                return $this->buildArmyDiviedByTime($armyType, $amount, $idCity, $worshipPlace);
        if ($divideBy == "amount")
                return $this->buildArmyDiviedByAmount($armyType, $amount, $idCity, $worshipPlace);

        TryToHack();
    }

    private function addArmyBatch($armyType, $amount, $idCity, $buildingTrain, $templeEffect) {
        global $idPlayer;
        $amount = floor($amount);
        $timeStart    = LArmy::getLastbatchArmyBuilding($idCity, $buildingTrain["Place"]);
        $timePerUnit  = LArmy::neededResources($armyType)["time"];
        $timePerUnit -= $timePerUnit * $buildingTrain["Lvl"] * ARMY_TRAIN_BUILDING_T_FAC / 100;
        $timePerUnit -= $timePerUnit * $templeEffect;
        
        $timeEnd      = $timeStart + $amount * $timePerUnit;
        
        $idBatch      = insertIntoTable(
                "id_player = :idp, id_city = :idc, place = :pl, army_type = :at, amount = :am, duration = :du, time_start = :ts, time_end = :te", "build_army",
                ["idp" => $idPlayer, "idc" => $idCity, "pl" => $buildingTrain["Place"], "at" => $armyType, "am" => $amount, "du" => $amount * $timePerUnit, "ts" => $timeStart, "te" => $timeEnd]);

        return [
            "id" => $idBatch,
            "time_end" => $timeEnd,
            "time_start" => $timeStart,
            "amount" => $amount,
            "place" => $buildingTrain["Place"],
            "duration" => $amount * $timePerUnit,
            "army_type" => $armyType,
            "id_city" => $idCity
                ];
    }

    private function buildArmyDiviedByNone($armyType, $amount, $idCity, $buildingPlace, $worshipPlace) {
        global $idPlayer;

        $buildingTrain = LCityBuilding::getBuildingAtPlace($buildingPlace, $idCity);

        if (CArmy::$BuildingTypeForArmy[$armyType] != $buildingTrain["Type"])
                return [
                "state" => "error_6",
                "armyBatches" => [],
                "City" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0]
                    ];

        $workingCount = selectFromTable("COUNT(*) AS c", "build_army", "id_city = :idc AND place = :pl AND id_player = :idp", ["pl" => $buildingTrain["Place"], "idc" => $idCity, "idp" => $idPlayer])[0]["c"];

        if ($workingCount >= min(ARMY_MAX_NUM_BATCH, $buildingTrain["Lvl"]))
                return [
                "state" => "error_7",
                "armyBatches" => [],
                "City" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0]
                    ];

        $templeEffect = LCityBuilding::getTempleEffectRateOnArmy($idCity, $worshipPlace);

        return [
            "state" => "ok",
            "armyBatches" => [$this->addArmyBatch($armyType, $amount, $idCity, $buildingTrain, $templeEffect)],
            "City" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0]
                ];
    }

    private function buildArmyDiviedByTime($armyType, $amount, $idCity, $worshipPlace) {
        
        $BuildingList = LCityBuilding::canBuildArmy($idCity, $armyType);
        $templeEffect = LCityBuilding::getTempleEffectRateOnArmy($idCity, $worshipPlace);
        $batches = [];
        $timePerUnit = LArmy::neededResources($armyType)["time"];


        foreach ($BuildingList as &$oneBuilding) {
            $oneBuilding["TimePerUnit"] = ($timePerUnit - $timePerUnit * $templeEffect - $timePerUnit * $oneBuilding["Lvl"] * ARMY_TRAIN_BUILDING_T_FAC / 100);
        }
        $totalTime = array_sum(array_column($BuildingList, "TimePerUnit"));
        
        foreach ($BuildingList as &$oneBuilding) {
            $oneBuilding["TimePerBuilding"] = max($totalTime - $oneBuilding["TimePerUnit"], 1);
        }
        $totalTimeBuilding = array_sum(array_column($BuildingList, "TimePerBuilding"));



        $amountUnit = $amount / $totalTimeBuilding;
        foreach ($BuildingList as $onB) {
            $batch = $this->addArmyBatch($armyType, floor($amountUnit * $onB["TimePerBuilding"]), $idCity, $onB, $templeEffect);
            if (count($batch)) $batches[] = $batch;
        }
        return [
            "state" => "ok",
            "armyBatches" => $batches,
            "City" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0]
        ];
    }

    private function buildArmyDiviedByAmount($armyType, $amount, $idCity, $worshipPlace) {
        $BuildingList = LCityBuilding::canBuildArmy($idCity, $armyType);
        $templeEffect = LCityBuilding::getTempleEffectRateOnArmy($idCity, $worshipPlace);
        $batches = [];
        $amountFactor = $amount / count($BuildingList);
        foreach ($BuildingList as $oneBuilding) {
            $batch = $this->addArmyBatch($armyType, $amountFactor, $idCity, $oneBuilding, $templeEffect);
            if (count($batch)) $batches[] = $batch;
        }

        return [
            "state" => "ok",
            "armyBatches" => $batches,
            "City" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0]
        ];
    }

    private function checkEdu($Condetion) {
        global $idPlayer;

        if ($Condetion["type"] != "study") return ["state" => "error_6_0"];
        $playerEdu = selectFromTable($Condetion["study"], "player_edu", "id_player = :idp", ["idp" => $idPlayer]);
        if (!count($playerEdu)) return ["state" => "error_6_1"];
        if ($playerEdu[0][$Condetion["study"]] < $Condetion["lvl"])
                return ["state" => "error_6_2"];
        return true;
    }

    private function checkBuilding($Condetion, $buildingPlace) {

        global $idPlayer;

        if ($Condetion["type"] != "building") return ["state" => "error_7_0"];
        $Building = LCityBuilding::getBuildingAtPlace($buildingPlace, validateID($_POST["idCity"]));

        if ($Building["Lvl"] < $Condetion["lvl"])
                return ["state" => "error_7_1"];

        if ($Building["Type"] != $Condetion["buildingType"])
                return ["state" => "error_7_2"];

        return true;
    }

}
