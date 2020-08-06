/**
 * @author Roméo Razafindrakoto <romeorazaf@gmail.com>
 */

$(document).ready(function () {
    
    $('[data-toggle="tooltip"]').tooltip()
    /* Initialisation des bouttons ajout */
    btnAdd(".btnAddLangue", "#modalAddLangue", "langue.add");
    btnAdd(".btnAddUser", "#modalAddUser", "user.add");
    btnAdd(".btnAddVideo", "#modalAddVideo", "video.add");

    /* Initialisation des ajouts */
    add("langue.add", "langue.list", "#formAddLangue", "#modalAddLangue", "#langueDatatable");
    add("user.add", "user.list", "#formAddUser", "#modalAddUser", "#userDatatable");
    add("video.add", "video.list", "#formAddVideo", "#modalAddVideo", "#videoDatatable");

    /* Initialisation des bouttons modifié */
    btnEdit('.btnEditLangue', "#modalEditLangue", "#idLangue", "langue.edit");
    btnEdit('.btnEditUser', "#modalEditUser", "#idUser", "user.edit");
    btnEdit(".btnEditProfileUser", "#modalEditProfileUser", "#idUser", "user.edit_profile");
    btnEdit(".btnChangePasswordUser", "#modalChangePasswordUser", "#idUserPassword", "user.change_password");
    btnEdit(".btnEditVideo", "#modalEditVideo", "#idVideo", "video.edit");  

    /* Initialisation des modifications */
    edit("#formEditLangue", "#modalEditLangue", "#idLangue", "langue.edit", "#langueDatatable");
    edit("#formEditUser", "#modalEditUser", "#idUser", "user.edit", "#userDatatable");
    edit("#formEditVideo", "#modalEditVideo", "#idVideo", "video.edit", "#videoDatatable");
    editProfile("#formEditProfileUser", "#modalEditProfileUser", "#idUser", "user.edit_profile");
    editProfile("#formChangePasswordUser", "#modalChangePasswordUser", "#idUserPassword", "user.change_password");

    /* Initialisation des suppressions */
    del("langue.delete", "cette Langue", ".btnDeleteLangue", "#langueDatatable");
    del("category.delete", "cette Categorie", ".btnDeleteCategoryLangue", "#categoryLangueDatatable");
    del("article.delete", "cette Article", ".btnDeleteArticle", "#articleDatatable");
    del("user.delete", "cet Utilisateur", ".btnDeleteUser", "#userDatatable");
    del("page.delete", "cette Page", ".btnDeletePage", "#pageDatatable");
    del("faq.delete", "cet FAQ", ".btnDeleteFaq", "#faqDatatable");
    del("video.delete", "cette Vidéo", ".btnDeleteVideo", "#videoDatatable");

    /* Initialisation du changement de status */
    status_(".categoryStatus", "category.status", "#categoryLangueDatatable")
    status_(".langueStatus", "langue.status", "#langueDatatable")
    status_(".articleStatus", "article.status", "#articleDatatable")
    status_(".pageStatus", "page.status", "#pageDatatable")
    status_(".faqStatus", "faq.status", "#faqDatatable")
    status_(".videoStatus", "video.status", "#videoDatatable")

    /* Initilisation des images */
    loadImage()
    onClickAvatar(".avatar","#user_profile_file")
});

/* Récuperation des données */
function liste(route, table) {
    var Col = [];
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
                    Col.push({
                        "data": key
                    })
                });

                $(table).DataTable({
                    "ajax": getUrl,
                    "sAjaxDataProp": "data",
                    "searching": true,
                    "order": [
                        [0, "desc"]
                    ],
                    "columns": Col,
                    "responsive": true,
                    "autoWidth": false,
                });
            }
        },
        error: function (response) {
        }
    });
}

function getImage() {
    var html = "";
    var url = Routing.generate('get.image', {
        'type': $("#type").val(),
        'id': $("#id").val()
    });
    $.ajax({
        type: $(this).attr('method'),
        url: url,
        data: $(this).serialize(),
        success: function (data) {
            $.each(data, function (key, value) {
                html += value.image
            });
            $(".imagelist").html(html.split("undefined")[0])
        },
        error: function (response) {
        }
    });
}

function onChangeLangue(selectLangue, selectCategory) {
    $(document).on("change", selectLangue, function () {
        getCategory(selectLangue,selectCategory)
    })
}

function getCategory(selectLangue, selectCategory) {
    var html = "";
    var url = Routing.generate('langue.category', {
        'id': $(selectLangue).val(),
        'type': $('#type').val(),
        'typeid': $("#typeid").val()
    });
    $.ajax({
        type: $(this).attr('method'),
        url: url,
        data: $(this).serialize(),
        success: function (data) {
            $.each(data, function (key, value) {
                html += value.option
            });
            $(selectCategory).html(html.split("undefined")[0])
        },
        error: function (response) {
        }
    });
}

/* Suppression */
function removeImage(route, btn) {
    $(document).on("click", btn, function (e) {
        e.preventDefault();
        swal({
                title: "Êtes-vous sûre de supprimer cette image de cette " + $("#type").val() + "?",
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
                        url: getUrl,
                        data: $(this).serialize(),
                        success: function (response) {},
                        error: function (response) {
                        }
                    });
                    getImage()
                    swal("Suppression fait avec succès", {
                        icon: "success",
                    });
                } else {

                }
            });
    });
}

/* Affichage du modal */
function showModal(idModal, route) {
    $(idModal).on('shown.bs.modal', function () {
        var modal = $(this);
        $.ajax(Routing.generate(route, {}), {
            success: function (data) {
                modal.find('.modal-body').html(data);
                $('.select2').select2()
                $('.select2bs4').select2({
                    theme: 'bootstrap4'
                })
                $('input:text:visible:first').focus();
            }
        });
    });
}

/* Action du boutton ajout */
function btnAdd(btnAdd, idModal, route) {
    $(document).on("click", btnAdd, function (e) {
        e.preventDefault()
        showModal(idModal, route);
    })
}

/* Ajout dans la base de donnée */
function add(route, listeRoute, form, modal, dataTable, ) {
    $(document).on('submit', form, function (e) {
        e.preventDefault();
        $form = $(e.target);
        modal = $(modal);
        var $submitButton = $form.find(':submit');
        $submitButton.html('<i class="fas fa-spinner fa-pulse"></i>');
        $submitButton.prop('disabled', true);

        $.ajax({
            type: $(this).attr('method'),
            url: Routing.generate(route),
            data: $(this).serialize(),
            success: function (data) {
                $submitButton.html('<i class="fas fa-check"></i>');
                $submitButton.prop('disabled', true);
                toastr.info('Ajout fais avec succès');
                modal.modal('toggle');
                modal.find('.modal-body').html('');
                liste(listeRoute, dataTable);
            },
            error: function (response) {
                $submitButton.html('Ajouter');
                $submitButton.prop('disabled', false);
                $.each(response,function (key, value) {
                    $(".message").html(value.message);
                });
                $("#error_message").removeClass();
                $("#error_message").addClass("form-group row");
            }
        });
    });
    return false;
}

function onClickAvatar(avatar, input){
    $(document).on("click", avatar,function(){
        $(input).click();
    })
}

/* Action du boutton modifier */
function btnEdit(btn, editModal, idModif, route) {
    $(document).on("click", btn, function (e) {
        e.preventDefault();
        $(editModal).modal('toggle');
        $(idModif).val(this.id);
        showEditModal(editModal, idModif, route);
    })
}

/* Affichage du modal de modification */
function showEditModal(editModal, idModif, route) {
    $(editModal).on('shown.bs.modal', function () {
        var modal = $(this);
        $.ajax(Routing.generate(route, {
            'id': $(idModif).val(),
        }), {
            success: function (data) {
                modal.find('.modal-body').html(data);

                $('.select2').select2()
                $('.select2bs4').select2({
                    theme: 'bootstrap4'
                })
                loadImage();
                $('input:text:visible:first').focus();
            }
        });
    });
}

function loadImage() {
    $(function () {
        var readURL = function (input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('.avatar').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $(".file-upload").on('change', function () {
            readURL(this);
        });
    })
}

/* Mis à jour de la base de donnée */
function edit(form, editModal, idModif, route, dataTable) {
    $(document).on('submit', form, function (e) {
        e.preventDefault();
        $form = $(e.target);
        modal = $(editModal);
        var id = $(idModif).val();
        var getUrl = Routing.generate(route, {
            'id': id
        });

        var $submitButton = $form.find(':submit');
        $submitButton.html('<i class="fas fa-spinner fa-pulse"></i>');
        $submitButton.prop('disabled', true);
        $.ajax({
            url: getUrl,
            type: "POST",
            data: $(this).serialize(),
            success: function (response) {
                $submitButton.html('<i class="fas fa-check"></i>');
                $submitButton.prop('disabled', true);
                toastr.success("Donnée mis à jour!")
                modal.modal('toggle');
                $(dataTable).DataTable().ajax.reload();
            },
            error: function (response) {
                $submitButton.html('Ajouter');
                $submitButton.prop('disabled', false);
                $.each(response,function (key, value) {
                    $(".message").html(value.message);
                });
                $("#error_message").removeClass();
                $("#error_message").addClass("form-group row");
            }
        });
    });
}

/* Mis à jour du profile de la base de donnée */
function editProfile(form, editModal, idModif, route) {
    $(document).on('submit', form, function (e) {
        e.preventDefault();
        $form = $(e.target);
        modal = $(editModal);
        var id = $(idModif).val();
        var getUrl = Routing.generate(route, {
            'id': id
        });

        var $submitButton = $form.find(':submit');
        $submitButton.html('<i class="fas fa-spinner fa-pulse"></i>');
        $submitButton.prop('disabled', true);
        $.ajax({
            url: getUrl,
            type: "POST",
            data: new FormData(this),
            contentType: false,
            processData: false,
            success: function (response) {
                $submitButton.html('<i class="fas fa-check"></i>');
                $submitButton.prop('disabled', true);
                modal.modal('toggle');

                $("#username").html(response[0]);
                $('#userProfileImage').attr('src', '/uploads/profile/' + response[1]);
                $('.user-header').html(response[0] + "<br>" + response[2]);
                toastr.success("Donnée mis à jour!")
            },
            error: function (response) {
                $.each(response,function (key, value) {
                    $(".message").html(value.message);
                });
                $("#error_message").removeClass();
                $submitButton.html('Soumettre');
                $submitButton.prop('disabled', false);
                $("#error_message").addClass("form-group row");
            }
        });
    });
}

/* Activé ou désactivé le status */
function status_(btn, route, dataTable, listRoute) {
    $(document).on('click', btn, function (e) {
        e.preventDefault();
        var id = this.id
        var getUrl = Routing.generate(route, {
            'id': id
        });
        var dt = $(this).serialize();
        $.ajax({
            type: $(this).attr('method'),
            url: getUrl,
            data: dt,
            success: function (result) {
                $(dataTable).DataTable().ajax.reload();
                (result.icon == "success") ? toastr.success(result.title) : toastr.warning(result.title)
            },
            error: function (result) {
                // $(dataTable).DataTable().ajax.reload();
                (result.icon == "success") ? toastr.success(result.title) : toastr.warning(result.title)
                modal.modal('toggle');

            }
        });
    });
}

/* Suppression */
function del(route, titre, btn, dataTable) {
    $(document).on("click", btn, function (e) {
        /*e.preventDefault();*/
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
                            $(dataTable).DataTable().ajax.reload();
                            swal("Suppression fait avec succès", {
                                icon: "success",
                            });
                        },
                        error: function (response) {
                            // $(dataTable).DataTable().ajax.reload();
                            swal(response['statusText'], {
                                icon: "warning",
                            });
                        }
                    });
                }
            });
    });
}