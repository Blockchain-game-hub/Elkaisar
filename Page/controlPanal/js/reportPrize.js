
var pormotion =[
    {
        ar_title: "مواطن"
    },
    {
        ar_title: "عريف"
    },
    {
        ar_title: "رقيب"
    },
    {
        ar_title: "قسطور"
    },
    {
        ar_title: "قسطور اعلى"
    },
    {
        ar_title: "نائب"
    },
    {
        ar_title: "قاضى"
    },
    {
        ar_title: "موفد"
    },
    {
        ar_title: "ديكتاتور"
    },
    {
        ar_title: "قائد الفيلق الخامس"
    },
    {
        ar_title: "اقئد الفيلق الرابع"
    },
    {
        ar_title: "قائد الفيلق الثالث"
    },
    {
        ar_title: "قائد الفيلق الثانى"
    },
    {
        ar_title: "قائد الفيلق الاول"
    },
    {
        ar_title: "لواء"
    },
    {
        ar_title: "فريق"
    },
    {
        ar_title: "فريق درجة 1"
    },
    {
        ar_title: "فريق درجة 2"
    },
    {
        ar_title: "فريق درجة 3"
    },
    {
        ar_title: "مارشال"
    },
    {
        ar_title: "رقيب درجة 9"
    },
    {
        ar_title: "رقيب درجة 8"
    },
    {
        ar_title: "رقيب درجة 7"
    },
    {
        ar_title: "رقيب درجة 6"
    },
    {
        ar_title: "رقيب درجة 5"
    },
    {
        ar_title: "رقيب درجة 4"
    },
    {
        ar_title: "رقيب درجة 3"
    },
    {
        ar_title: "رقيب درجة 2"
    },
    {
        ar_title: "رقيب درجة 1"
    },
    {
        ar_title: "قيصر"
    },
    {
        ar_title: "قيصر"
    }
];
showAllMatrial();

$(document).on("keyup", "#SearchOnePlayerVal", function (){
  

    var searchVal = $(this).val();

   

    $("#drop-down ul").html("");
    $.ajax({
      url: "api/onePlayer.php",
      data: {

        SEARCHE_FOR_PLAYER: true,
        val: searchVal

      },
      type: 'GET',
      beforeSend: function(xhr) {

      },
      success: function(data, textStatus, jqXHR) {
        $("#drop-down ul").html("");

        var jsonData = JSON.parse(data);
        var list = "";

        for (var iii in jsonData) {

          list += ` <li class="selectUserToShow" data-id-player = "${jsonData[iii].id_player}">${jsonData[iii].name} (${pormotion[jsonData[iii].porm].ar_title})</li>`;

        }


        $("#drop-down ul").html(list);

      },
      error: function(jqXHR, textStatus, errorThrown) {

      }

    });






});



$(document).on("click" , ".selectUserToShow" , function (){
    $("#drop-down ul").html("");
    var id_player = $(this).attr("data-id-player");
    $.ajax({
        url:"api/onePlayer.php",
        data:{
            GET_PLAYER_REPORT: true,
            id_player: id_player,
            startDate: $("#startData").val() || 0,
            endDate  : $("#endData").val() || Math.floor(Date.now()/1000),
            
        },
        type: 'GET',
        beforeSend: function (xhr) {
            
        },
        success: function (data, textStatus, jqXHR) {
             var list = ""
            if(isJson(data)){
                var jsonData = JSON.parse(data);
            }else{
                alert(data);
                return ;
            }
            
            console.log(jsonData)
            for(var iii in jsonData){
                
                if(Elkaisar.BaseData.Items[jsonData[iii].prize]){
                    
                    list += `    
                            <div class="matrial-unit" data-matrial="certain_move" data-matrial-table="matrial_main" data-amount="1" prize-type="matrial" style="width: 10%; height:50px;">
                                <img src="../${Elkaisar.BaseData.Items[jsonData[iii].prize].image}">
                                <div class="amount">${jsonData[iii].amount}</div>
                                <div class="name"><span>${Elkaisar.BaseData.Items[jsonData[iii].prize].name}</span></div>
                            </div>`;
                    
                }else{
                    console.log(jsonData[iii])
                }
                
            }
           $("#report-prize-list").html(list)
        },
        error: function (jqXHR, textStatus, errorThrown) {
            
        }
    })
});
