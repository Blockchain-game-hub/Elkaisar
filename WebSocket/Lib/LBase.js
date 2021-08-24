Elkaisar.DB.SelectFrom = function (Query, Table, Where, Parmter, ComFunc) {
    Elkaisar.Mysql.getConnection(function (err, connection) {
        if (err)
            throw err;
        connection.query("SELECT " + Query + " FROM " + Table + " WHERE " + Where, Parmter, function (err, result, fields) {
            if (err)
                throw err;
            if (ComFunc)
                ComFunc(result);
            connection.release();
        });
    });

};





Elkaisar.DB.Update = function (Query, Table, Where, Parmter, ComFunc) {
    Elkaisar.Mysql.getConnection(function (err, connection) {
        if (err)
            throw err;
        connection.query(`UPDATE ${Table} SET ${Query} WHERE ${Where}`, Parmter, function (err, result) {
            if (err)
                throw err;
            if (ComFunc)
                ComFunc(result);
            connection.release();
        });
    });

};



Elkaisar.DB.Insert = function (Query, Table, Parmter, ComFunc) {
    Elkaisar.Mysql.getConnection(function (err, connection) {
        if (err)
            throw err;
        connection.query(`INSERT IGNORE INTO ${Table} SET ${Query}`, Parmter, function (err, result) {
            if (err)
                throw err;
            if (ComFunc)
                ComFunc(result);
            connection.release();
        });
    });

};

Elkaisar.DB.Delete = function (Table, Where, Parmter, ComFunc) {
    Elkaisar.Mysql.getConnection(function (err, connection) {
        if (err)
            throw err;
        connection.query(`DELETE FROM ${Table} WHERE ${Where}`, Parmter, function (err, result) {
            if (err)
                throw err;
            if (ComFunc)
                ComFunc(result);
            connection.release();
        });
    });

};

Elkaisar.DB.Exist = function (Table, Where, Parmter, ComFunc) {
    Elkaisar.Mysql.getConnection(function (err, connection) {
        if (err)
            throw err;
        connection.query(`SELECT EXISTS(SELECT * FROM ${Table} WHERE ${Where}) AS val`, Parmter, function (err, result) {
            if (err)
                throw err;
            if (ComFunc) {
                if (!result)
                    ComFunc(false);
                if (!result[0])
                    ComFunc(false);
                if (result[0].val === 0)
                    ComFunc(false);
                if (result[0].val === 1)
                    ComFunc(true);

                ComFunc(false);
            }

            connection.release();
        });
    });

};


Elkaisar.DB.QueryExc = function (Quary, Parmter, ComFunc) {


    Elkaisar.Mysql.getConnection(function (err, connection) {
        if (err)
            throw err;
        connection.query(Quary, Parmter, function (err, result) {
            if (err)
                throw err;
            if (ComFunc) {
                ComFunc(result);
            }

            connection.release();
        });
    });

};

Elkaisar.DB.ASelectFrom = function (Query, Table, Where, Parmter, ComFunc) {
    return new Promise((resolve, reject) => {
        Elkaisar.Mysql.query("SELECT " + Query + " FROM " + Table + " WHERE " + Where, Parmter, (error, results) => {
            if (error) {
                return reject(error);
            }
            return resolve(results);
        });
    });
};


Elkaisar.DB.AUpdate = function (Query, Table, Where, Parmter, ComFunc) {


    return new Promise((resolve, reject) => {
        Elkaisar.Mysql.query(`UPDATE ${Table} SET ${Query} WHERE ${Where}`, Parmter, (error, results) => {
            if (error) {
                return reject(error);
            }
            return resolve(results);
        });
    });
};
Elkaisar.DB.ADelete = function (Table, Where, Parmter, ComFunc) {


    return new Promise((resolve, reject) => {
        Elkaisar.Mysql.query(`DELETE FROM ${Table} WHERE ${Where}`, Parmter, (error, results) => {
            if (error) {
                return reject(error);
            }
            return resolve(results);
        });
    });
};

Elkaisar.DB.AInsert = function (Query, Table, Parmter, ComFunc) {
 
    return new Promise((resolve, reject) => {
        Elkaisar.Mysql.query(`INSERT IGNORE INTO ${Table} SET ${Query}`, Parmter, (error, Result) => {
            if (error) {
                return reject(error);
            }
            return resolve(Result);
        });
    });
};


Elkaisar.DB.AQueryExc = function (Query, Parmter) {
 
    return new Promise((resolve, reject) => {
        Elkaisar.Mysql.query(Query, Parmter, (error, Result) => {
            if (error) {
                return reject(error);
            }
            return resolve(Result);
        });
    });
};



Elkaisar.DB.AExist = function (Table, Where, Parmter, ComFunc) {

    return new Promise((resolve, reject) => {

        Elkaisar.Mysql.query(`SELECT EXISTS(SELECT * FROM ${Table} WHERE ${Where}) AS val`, Parmter, (error, results) => {
            if (error) {
                return reject(error);
            }
            if (!results)
                resolve(false);
            if (!results[0])
                resolve(false);
            if (results[0].val === 0)
                resolve(false);
            if (results[0].val === 1)
                resolve(true);
        });
    });
};


module.exports.MakeStringId = function (Len) {
    var result = '';
    var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var charactersLength = characters.length;
    for (var i = 0; i < Len; i++) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
};



Elkaisar.Base.validateId = function (id) {
    return Number(id);
}
Elkaisar.Base.validateGameNames = function (id) {
    return id;
}
Elkaisar.Base.validateJson = function (id) {
    return id;
}
Elkaisar.Base.validatePlayerWord = function (id) {
    return id;
}

Elkaisar.Base.sendMsgToPlayer = function(idPlayer, StrMsg){

    const Player = Elkaisar.Base.getPlayer(idPlayer);
    if(Player && Player.connection)
    Player.connection.sendUTF(StrMsg);

}

Elkaisar.Base.tryToHack = function(){};


Elkaisar.Base.rand = function (min, max){
  
    return Math.floor((Math.random() * (max - min)) + min);
    
};


Elkaisar.Base.playerWinPrize = function (idPlayer, Title, PrizeList){
    var List = "", ii;
    var Item;
    for(ii =0 ; ii < PrizeList.length; ii++ ){
        Item = Elkaisar.Lib.LItem.ItemList[PrizeList[ii].Item];
        List += `<li style="width: 20%;">
                <div class="image"><img src="${Item.image}"></div>
                <div class="amount stroke">${PrizeList[ii].amount} X</div>
            </li>`;
    }
     
    Elkaisar.DB.Insert(
            `id_to = ${idPlayer}, head = '${Title}', body=?, time_stamp = ${Math.floor(Date.now() / 1000)}`, "msg_diff",
            [`<div id="matrial-box-gift" style="border: none; background: none"><ul class="matrial-list">${List}</ul></div>`]);
    Elkaisar.Base.sendMsgToPlayer(idPlayer, JSON.stringify({
        classPath: "Base.PrizeSent"
    }));
};


Elkaisar.Base.TryToHack = function (){
    
};