

class BattelReplay{
    
    
    static BattelRecord;
    currentRoundIndex = 0;
    constructor(BattelRecord){
        BattelReplay.BattelRecord = BattelRecord;
    }
    
    
    startBattelShow(){
        new Round(0);
    }
    
    
}
