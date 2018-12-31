(function($){
    $.getQuery = function( query ) {
        query = query.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
        var expr = "[\\?&]"+query+"=([^&#]*)";
        var regex = new RegExp( expr );
        var results = regex.exec( window.location.href );
        if( results !== null ) {
            return results[1];
            return decodeURIComponent(results[1].replace(/\+/g, " "));
        } else {
            return false;
        }
    };
})(jQuery);

$( document ).ready(function() {
  var prefix = "/ffbad";
  $('.datetimepicker').datetimepicker({
    locale: 'fr',
    format: 'DD/MM/YYYY',
    allowInputToggle: true,
  });

  $('#date-input').val(moment().format('DD/MM/YYYY'));

  var rechercheF = function ($val = false){

    var $p = $('p.message');
    $p.html("");

    if ($val == false) {$val = $('#rechercher-input').val();};
    console.log($val);
    var $date = $('#date-input').val();
    var patLicence = new RegExp('^[0-9]+$','g');
    var patLicencesMultiple = new RegExp('^([0-9]+;)+[0-9]+;?$','g');
    var patNomJoueur = new RegExp('^[A-Za-z-\\u00C0-\\u017F\\s]+$','g');
    if (patLicence.test($val)){
      $url = prefix + '/pages/joueur.php';
    }else if (patNomJoueur.test($val)){
      $url = prefix + '/pages/joueur-nom.php';
    }else if (patLicencesMultiple.test($val)){
      $url = prefix + '/pages/joueur-multiple.php';
    }else {
      $p.append('<b style="color:red">[ERROR]</b> Mauvaise Recherche' );$p.append('<BR/>');
      return;
    }

    var $r = $('p.result');
    $r.html('<div style="text-align:center;margin-top: 20px"><img src="./img/loading.gif" /></div>');
    $.ajax({
      cache : false,
      type: "POST",
      dataType: 'text',
      data: { 'value': $val, 'date': moment($date,'DD/MM/YYYY').format('YYYY-MM-DD')},
      url: $url,
      success: function(result){
        $r.html(result);

        $r.find('#licence-table.datatable').DataTable({
          paging: false,
          dom: '',
          order: [[ 4, 'desc' ]],
          columnDefs: [
            { orderable: false, targets: [3,5,7] }
          ]
        });

        $r.find('#recherche-table.datatable').DataTable({
          paging: false,
          dom: '',
          order: [[ 0, 'asc' ]]
        });

        $r.find('#res-tabs nav-tabs a').click(function (e) {
          e.preventDefault();
          $(this).tab('show');
        });
      },
      error: function(jqXHR, textStatus, errorThrown ) {
        $r.html("");
        $p.append('<b style="color:red">[ERROR]</b> '+errorThrown );$p.append('<BR/>');
        return true;
      },
    });
  };

  $("#rechercher").click(function(){rechercheF();});



  $("#rechercher-input").keyup(function (e) {
      if (e.keyCode == 13) {
         $("#rechercher").click();
      }
  });

  if($.getQuery('value')!==false){
    rechercheF($.getQuery('value'));
  }

});
