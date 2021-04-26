class CArmy {

    static  ArmyCap = {
        0 : 0,
        [Elkaisar.Config.ARMY_A] : 1,
        [Elkaisar.Config.ARMY_B] : 3,
        [Elkaisar.Config.ARMY_C] : 6,
        [Elkaisar.Config.ARMY_D] : 1,
        [Elkaisar.Config.ARMY_E] : 4,
        [Elkaisar.Config.ARMY_F] : 8,
        [Elkaisar.Config.ARMY_WALL_A] : 0,
        [Elkaisar.Config.ARMY_WALL_B] : 0,
        [Elkaisar.Config.ARMY_WALL_C] : 0
    };
    static  ArmyCityToArmyHero = {
        "army_a" : 1,
        "army_b" : 2,
        "army_c" : 3,
        "army_d" : 4,
        "army_e" : 5,
        "army_f" : 6
    };
    static  $ArmyCapCity = {
        "army_a" : 1,
        "army_b" : 3,
        "army_c" : 6,
        "army_d" : 1,
        "army_e" : 4,
        "army_f" : 8
    };
    static  $ArmyCityPlace = {
        0 : 0,
        1 : "army_a",
        2 : "army_b",
        3 : "army_c",
        4 : "army_d",
        5 : "army_e",
        6 : "army_f"
    };
    static  AmySpeed = [
        100, //البطل  فاضى
        300, //مشاة
        900, //فرسان
        600, //مدرعين
        250, //رماة
        150, //مقاليع
        100 //منجنيق
    ];
    static  ArmyPower = {
        0 :                             {"attack" : 0,  "def" : 0, "vit" : 0, "dam" : 0, "break" : 0, "anti_break" : 0, "strike" : 0, "immunity" : 0, "res_cap" : 0},
        [Elkaisar.Config.ARMY_A ]     : {"attack" : 8,  "def" : 8, "vit" : 60, "dam" : 3, "break" : 5, "anti_break" : 4, "strike" : 3, "immunity" : 1, "res_cap" : 100},
        [Elkaisar.Config.ARMY_B ]     : {"attack" : 30, "def" : 20, "vit" : 250, "dam" : 35, "break" : 10, "anti_break" : 4, "strike" : 1, "immunity" : 2, "res_cap" : 200},
        [Elkaisar.Config.ARMY_C ]     : {"attack" : 25, "def" : 30, "vit" : 400, "dam" : 40, "break" : 1, "anti_break" : 5, "strike" : 10, "immunity" : 10, "res_cap" : 220},
        [Elkaisar.Config.ARMY_D ]     : {"attack" : 9,  "def" : 5, "vit" : 45, "dam" : 3, "break" : 6, "anti_break" : 2, "strike" : 2, "immunity" : 2, "res_cap" : 75},
        [Elkaisar.Config.ARMY_E ]     : {"attack" : 19, "def" : 25, "vit" : 100, "dam" : 19, "break" : 2, "anti_break" : 2, "strike" : 12, "immunity" : 2, "res_cap" : 35},
        [Elkaisar.Config.ARMY_F ]     : {"attack" : 40, "def" : 20, "vit" : 600, "dam" : 70, "break" : 5, "anti_break" : 4, "strike" : 15, "immunity" : 5, "res_cap" : 75},
        [Elkaisar.Config.ARMY_WALL_A] : {"attack" : 20, "def" : 10, "vit" : 300, "dam" : 10, "break" : 5, "anti_break" : 4, "strike" : 15, "immunity" : 5, "res_cap" : 75},
        [Elkaisar.Config.ARMY_WALL_B] : {"attack" : 19, "def" : 25, "vit" : 400, "dam" : 35, "break" : 5, "anti_break" : 4, "strike" : 15, "immunity" : 5, "res_cap" : 75},
        [Elkaisar.Config.ARMY_WALL_C] : {"attack" : 40, "def" : 20, "vit" : 600, "dam" : 70, "break" : 5, "anti_break" : 4, "strike" : 15, "immunity" : 5, "res_cap" : 75}
    };
    
    static  FoodEat = [0, 4, 18, 36, 5, 20, 150];

}


module.exports = CArmy;