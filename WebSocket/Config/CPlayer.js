class CPlayer
{
    static BattelPlayerEmpty() {
        return {
            "idPlayer": 0,
            "Name": "",
            "Honor": 0,
            "Kills": 0,
            "Killed": 0,
            "heroNum": 1,
            "Troops": {
                "0": 0,
                [Elkaisar.Config.ARMY_A]: 0,
                [Elkaisar.Config.ARMY_B]: 0,
                [Elkaisar.Config.ARMY_C]: 0,
                [Elkaisar.Config.ARMY_D]: 0,
                [Elkaisar.Config.ARMY_E]: 0,
                [Elkaisar.Config.ARMY_F]: 0,
                [Elkaisar.Config.ARMY_WALL_A]: 0,
                [Elkaisar.Config.ARMY_WALL_B]: 0,
                [Elkaisar.Config.ARMY_WALL_C]: 0
            },
            "RemainTroops": {
                "0": 0,
                [Elkaisar.Config.ARMY_A]: 0,
                [Elkaisar.Config.ARMY_B]: 0,
                [Elkaisar.Config.ARMY_C]: 0,
                [Elkaisar.Config.ARMY_D]: 0,
                [Elkaisar.Config.ARMY_E]: 0,
                [Elkaisar.Config.ARMY_F]: 0,
                [Elkaisar.Config.ARMY_WALL_A]: 0,
                [Elkaisar.Config.ARMY_WALL_B]: 0,
                [Elkaisar.Config.ARMY_WALL_C]: 0
            },
            "ItemPrize": [],
            "ResourcePrize": {
                "food": 0,
                "wood": 0,
                "stone": 0,
                "metal": 0,
                "coin": 0
            },
            Study: {},
            State: {},
            GodGate: {}

        };
    }

    static PlayerCityPrizeEmpty() {
        return {
            "idCity": 0,
            "Kills": 0,
            "Killed": 0,
            "armySize": 0,
            "armyRemainSize": 0,
            "ResourceCap": 0,
            "Troops": {
                0: 0,
                [Elkaisar.Config.ARMY_A]: 0,
                [Elkaisar.Config.ARMY_B]: 0,
                [Elkaisar.Config.ARMY_C]: 0,
                [Elkaisar.Config.ARMY_D]: 0,
                [Elkaisar.Config.ARMY_E]: 0,
                [Elkaisar.Config.ARMY_F]: 0,
                [Elkaisar.Config.ARMY_WALL_A]: 0,
                [Elkaisar.Config.ARMY_WALL_B]: 0,
                [Elkaisar.Config.ARMY_WALL_C]: 0
            },
            "RemainTroops": {
                0: 0,
                [Elkaisar.Config.ARMY_A]: 0,
                [Elkaisar.Config.ARMY_B]: 0,
                [Elkaisar.Config.ARMY_C]: 0,
                [Elkaisar.Config.ARMY_D]: 0,
                [Elkaisar.Config.ARMY_E]: 0,
                [Elkaisar.Config.ARMY_F]: 0,
                [Elkaisar.Config.ARMY_WALL_A]: 0,
                [Elkaisar.Config.ARMY_WALL_B]: 0,
                [Elkaisar.Config.ARMY_WALL_C]: 0
            }
        };
    }

    static PrizeEmpty()
    {
        return {
            "ItemPrize": [],
            "ResourcePrize": {
                "food": 0,
                "wood": 0,
                "stone": 0,
                "metal": 0,
                "coin": 0
            }
        };
    }
}




module.exports = CPlayer;