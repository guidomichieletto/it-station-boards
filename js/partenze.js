var updateTrains = window.setInterval(function(){
  $.get("api/LAP-token/stazione/partenze/treni/2", function(data,status){
    var rows = "";
    var data = JSON.parse(JSON.stringify(data));
    var rowcolor = "grey";
    var imgcolor = "yellow";
    data.forEach(function(dt){
      if(dt.Ritardo == 0) var ritardo = ""; else var ritardo = dt.Ritardo + "'";
      if(dt.InPartenza == 1) var partenza = "<span class=\"dot dot1 " + imgcolor + "dot\" style=\"margin-right:10px\"></span><span class=\"dot dot2 " + imgcolor + "dot\"></span>"; else var partenza = "";
      var trow = "<tr class=\"" + rowcolor + "Row\"><td><img src=\"img/societa/" + dt.SocietaImg + "-" + imgcolor +  ".gif\" width=\"100\"><img src=\"img/tipitreno/" + dt.TipoTrenoImg + "-" + imgcolor +  ".gif\" width=\"50\" height=\"33\" style=\"margin: 0px 7%\">" + dt.NumTreno + "</td>";
      trow += "<td>" + dt.Destinazione + "</td><td>" + dt.Orario + "</td><td>" + ritardo + "</td><td>" + dt.Binario + "</td><td>" + partenza + "</td></tr>";
      if(rowcolor == "grey") rowcolor = "yellow"; else rowcolor = "grey";
      if(imgcolor == "grey") imgcolor = "yellow"; else imgcolor = "grey";
      rows += trow;
    });
    $("#tbody").html(rows);
  });
}, 10000);

var annunci = window.setInterval(function(){
  $.get("api/LAP-token/stazione/partenze/annunci/2", function(data,status){
    var data = JSON.parse(JSON.stringify(data));
    var audio = new Audio(data.Url);
audio.play();
  })

}, 60000);

var orologio = window.setInterval(function(){
  var date = new Date();
  var h = addZero(date.getHours());
  var m = addZero(date.getMinutes());

  $('#orologio').html(h + ':' + m)

}, 10000);

function addZero(x) {
    if (x < 10) {
      return x = '0' + x;
    } else {
      return x;
    }
}
