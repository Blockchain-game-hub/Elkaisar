Elkaisar.DB.SelectFrom = function (Query, Table, Where, Parmter, ComFunc) {
    Elkaisar.Mysql.getConnection(function (err, connection) {
        if (err)
            throw err;
        connection.query("SELECT " + Query + " FROM " + Table + " WHERE " + Where, Parmter, function (err, result, fields) {
            if (err)
                throw err;
            if(ComFunc)
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
            if(ComFunc)
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
            if(ComFunc)
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
            if(ComFunc)
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
            if(ComFunc)
            {
                if(!result)
                    ComFunc(false);
                if(!result[0])
                    ComFunc(false);
                if(result[0].val === 0)
                    ComFunc(false);
                if(result[0].val === 1)
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
            if(ComFunc){
                ComFunc(result);  
            }
                
            connection.release();
        });
    });
    
};

