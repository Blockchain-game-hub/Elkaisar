class ALWorld{

    static async getEmptyPlace(province)
    {
        return  await Elkaisar.DB.ASelectFrom("*", "world", "ut = 0 AND p = ? ORDER BY RAND() LIMIT 1", [province]);
    }
   

}


module.exports = ALWorld;