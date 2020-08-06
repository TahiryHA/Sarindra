$(document).ready(function(){

    $('[data-toggle="tooltip"]').tooltip();   
    bsCustomFileInput.init();
    setTimeout(() => {
      $("#myAlert").fadeOut("slow");
    }, 3000);

})


/* ==========================================USED========================================== */

$(document).ready(function(){
  //group add limit
  var maxGroup = 10;
  
  //add more fields group
  $(".addMore").click(function(){
      if($('body').find('.fieldGroup').length < maxGroup){
          var fieldHTML = '<div class="form-group fieldGroup">'+$(".fieldGroupCopy").html()+'</div>';
          $('body').find('.fieldGroup:last').after(fieldHTML);
      }else{
          alert('Maximum '+maxGroup+' groups are allowed.');
      }
  });
  
  //remove fields group
  $("body").on("click",".remove",function(){ 
      $(this).parents(".fieldGroup").remove();
  });

});


$(document).on('change','#personnel_departement',function(){
     let $field = $(this)
     let $form = $field.closest('form')
     let data = {}
     data[$field.attr('name')] = $field.val()

     var getUrl = Routing.generate("departement_categorie");

     $.post(getUrl,data).then(function(data){

        //  let $input = $(data).find('#personnel_categorie')
         $('#personnel_categorie').replaceWith(data)
         
     })
});

$(document).on('change','#conge_departement',function(){
    let $field = $(this)
    let $form = $field.closest('form')
    let data = {}
    data[$field.attr('name')] = $field.val()

    var getUrl = Routing.generate("conge_personnel");

    $.post(getUrl,data).then(function(data){

        // console.log(data);
        // let $input = $(data).find('#conge_personnel')
        $('#conge_personnel').replaceWith(data)
        
    })
});

function observationverif() {
    var data = $('form').serialize();

    var getUrl = Routing.generate("sig_verif",{
    'DOSSIER_NUMERO' : $("#verif").attr("attr-id"),
    'OBSERVATION' : data
    });
    
    window.location.href=getUrl;
};

/* Retour à la page précédente */
function goBack() {
  window.history.back()
}

/* Confirm action  */
function del_(route, titre, btn, list, content) {
    $(document).on("click", btn, function (e) {
        swal({
                title: "Êtes-vous sûre de supprimer " + titre + " ?",
                text: "Cette action est irreversible!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    var getUrl = Routing.generate(route, {
                        'id': $(this).attr('id')
                    });
                    $.ajax({
                        type: 'delete',
                        url: getUrl,
                        data: $(this).serialize(),
                        success: function (response) {
                            $(content).DataTable().ajax.reload();
                            swal("Suppression fait avec succès", {
                                icon: "success",
                            });
                        },
                        error: function (response) {
                            // $(content).DataTable().ajax.reload();
                            swal(response['statusText'], {
                                icon: "warning",
                            });
                        }
                    });
                }
            });
    });
}

/* Read  */
function liste_(route, table) {
  var column = [];
  var getUrl = Routing.generate(route, {});
  $.ajax({
      type: $(this).attr('method'),
      url: getUrl,
      data: $(this).serialize(),
      success: function (data) {
          if (jQuery.isEmptyObject(data.data[0])) {
              $(table).DataTable();
          } else {
              $(table).dataTable().fnDestroy();
              $.each(data.data[0], function (key, value) {
                  column.push({
                      "data": key
                  })
              });

              $(table).DataTable({
                  "ajax": getUrl,
                  "sAjaxDataProp": "data",
                  "searching": true,
                  "order": [
                      [0, "ASC"]
                  ],
                  "columns": column,
                  "responsive": true,
                  "autoWidth": false,
              });
          }
      },
      error: function (response) {
          console.log("Error: " + response);
      }
  });
}

/* Use this function for load data in backgroud*/
function load_data(route,content){

  const url = Routing.generate(route);

  $.getJSON(url, function(data){
    var html="";
  
    $.each(data.data, function(index, file){
      html+="<tr>";
      html+="<td>"+file.id+"</td>";
      html+="<td>"+file.name+"</td>";
      html+="<td>";
      (file.category) ? html+=file.category: html+=file.langue;
      html+="</td>";
      html+="<td><span class=";
      (file.status) ? html+="connected" : html+="disconnected";
      html+="></span>";
      (file.status) ? html+="Activé" : html+="Desactivé";
      html+="</td>";
      html+="<td>";
      html+="<a href='javascript:void(0)' class='";
      (file.category) ? html+="setStatus_f" : html+="setStatus_c";
      html+="'id='"+file.id+"'><button type='button'><i class='fas fa-edit'></i></button></a>";
      html+="<button type='button' class='btnDeleteFile' id='"+file.id+"'><i class='fas fa-trash-alt'></i></button>";
      html+="</td>";
      html+="</tr>";

    })
    $(content).html(html);

  });
}

/* Use this function to set Status */
function setStatus(classe,route,list,content){

  $(document).on("click",classe,function(e){
    e.preventDefault();

    const url = Routing.generate(route, {
      'id' : this.id
    });

    $.ajax({
        url: url,
        type : "POST",
        contentType : 'application/json',
        data : null,
        success : function(result) {
          $(content).DataTable().ajax.reload();
          (result.icon == "success") ? toastr.success(result.title) : toastr.warning(result.title)
        },
        error: function(xhr, resp, text) {
            console.log(xhr, resp, text);
        }
    });
  })
}

