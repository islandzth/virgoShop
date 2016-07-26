/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var listCity = null;
function updateOrderStatus(id) {
    var shopId = $('#shopId' + id).val();
    var orderId = $('#orderId' + id).val();
    var orderStatusNew = $('#statusNew' + id).val();
    var orderStatusOld = $('#statusOld' + id).val();
    $.post(SITE_URL + "manage/updateOdersStatus", {status_old: orderStatusOld, status_new: orderStatusNew, sId: shopId, oId: orderId}, function (data) {
        if (data == 1) {
            if (orderStatusNew == 0)
                $('#formStatus' + id).html("<span class='label label-info'>Chưa xử lý</span>");
            else if (orderStatusNew == 1)
                $('#formStatus' + id).html("<span class='label label-success'>Thành công</span>");
            else if (orderStatusNew == 2)
                $('#formStatus' + id).html("<span class='label label-danger' >Hủy</span>");
        } else {
            alert("ko thanh cong");
        }
    });

}
function loadDistrict() {
    var cityId = $('#listCity').val();

    $.post(SITE_URL + "ajax/loadListDistrict", {cityId: cityId}, function (data) {
        $('#listDistrict').html(data);
    });

}
function loadDistrictBranch(stt) {
    var cityId = $('#listCityBranch' + stt).val();

    $.post(SITE_URL + "ajax/loadListDistrict", {cityId: cityId}, function (data) {
        $('#listDistrictBranch' + stt).html(data);
    });

}
function insertFormBranch(stt) {
    $('#Branch').append("<div class='form-group' id = 'sttBranch" + stt + "'><label for='inputAddress' class='col-lg-3 control-label'>Chi nhánh " + stt + "</label><div class='col-lg-7'><input type='text' class='form-control' id='inputAddress' name='arrBranchAddress[]' placeholder='Hồ Chí Minh'></div></div><div class='form-group'><label for='inputAddress' class='col-lg-3 control-label'></label><div class='col-lg-7'><select rel='ttttttt' id='listCityBranch" + stt + "' name='selectCityBranchId[]' onChange='loadDistrictBranch(" + stt + ");'><option class='form-control' value='0'>Chọn tỉnh/thành phố</option></select><select id ='listDistrictBranch" + stt + "' name='selectDistrictBranchId[]'><option class='form-control' value ='0'>Chọn quận/huyện</option></select></div></div><div class='form-group' id = 'sttBranch" + stt + "'><label for='inputAddress' class='col-lg-3 control-label'>Số điện thoại</label><div class='col-lg-7'><input type='text' class='form-control' id='inputAddress' name='arrBranchPhone[]' placeholder='Hồ Chí Minh'></div></div>\n");
    if (listCity === null) {
        $.get(SITE_URL + "ajax/loadListCity", {cityId: 1}, function (data) {
            $("select[rel=ttttttt]").html(data);
            $("select[rel=ttttttt]").removeAttr("rel");
            listCity = data;
        });
    } else {
        $("select[rel=ttttttt]").html(listCity);
        $("select[rel=ttttttt]").removeAttr("rel");
    }
    stt = stt + 1;
    $('#BtnBranch').html("<a onclick=insertFormBranch(" + stt + ")>Thêm chi nhánh</a>");

}
function deleteFormBranch(stt) {
    $('#sttBranch' + stt).remove();
    $('#sttBranchPhone' + stt).remove();
}
function insertFormDetailImage(stt) {
    $('#DetailImage').append("<div class='form-group'><label for='fileDealImages' class='col-lg-3 control-label'>Hình chi tiết " + stt + "</label><div class='col-lg-7'><input type='file' class='form-control' name='arrFileDetailImages[]' id='fileDealImages'></div></div><div class='form-group'><label for='fileDealImages' class='col-lg-3 control-label'>Chú thích hình ảnh:</label><div class='col-lg-7'><input type='text' class='form-control' name='arrNameDetailImages[]' id='fileDealImages'></div></div>");
    stt = stt + 1;
    $('#BtnDetailImage').html("<a onclick =insertFormDetailImage(" + stt + ")>Thêm hình ảnh chi tiết</a>");
}
function insertFormImageLogoProduct() {
    $('#formImageLogoProduct').html("<label for='fileDealImages' class='col-lg-3 control-label'>Hình đại diện mới</label><div class='col-lg-7'><input type='file' class='form-control' name='fileDealImages' id='fileDealImages'></div>");
}
function deleteFormImageProduct(stt) {
    $('#listDetailImage-' + stt).remove();
    $('#listDetailNote-' + stt).remove();
    $('#listDetailBtn-' + stt).remove();
}
function deleteFromAttribute(stt) {
    $('#titleAttribute-' + stt).remove();
    $('#contentAttribute-' + stt).remove();
}
function loadCategoryProduct(parentId) {
    $.post(SITE_URL + "ajax/getCategoryChild", {parentId: parentId}, function (data) {
        $('#CategoryChildProduct').html(data);
    });
}
function loadCategoryMutilSelect(parentId) {
    if (parentId == 1) {
        $('#businessCategory1').prop('checked', true);
        $('#businessCategory2').prop('checked', false);
        $('#businessCategory3').prop('checked', false);
        $('#businessCategorySelect1').show();
        $('#businessCategorySelect2').hide();
        $('#businessCategorySelect3').hide();
    } else if (parentId == 2) {
        $('#businessCategory1').prop('checked', false);
        $('#businessCategory2').prop('checked', true);
        $('#businessCategory3').prop('checked', false);
        $('#businessCategorySelect1').hide();
        $('#businessCategorySelect2').show();
        $('#businessCategorySelect3').hide();
    } else if (parentId == 3) {
        $('#businessCategory1').prop('checked', false);
        $('#businessCategory2').prop('checked', false);
        $('#businessCategory3').prop('checked', true);
        $('#businessCategorySelect1').hide();
        $('#businessCategorySelect2').hide();
        $('#businessCategorySelect3').show();
    }
    //$.post("http://dev.shop.wili.vn/ajax/getCategoryChildMutilSelect", {parentId: parentId}, function(data) {
    //    $('#CategoryMutilSelect').html(data);
    //});
}
function loadCategoryFollowMutilSelect1(parentId) {
    if (parentId == 1) {
        $('#followCategory1').prop('checked', true);
        $('#followCategory2').prop('checked', false);
        $('#followCategory3').prop('checked', false);
        $('#followCategorySelect1').show();
        $('#followCategorySelect2').hide();
        $('#followCategorySelect3').hide();
    } else if (parentId == 2) {
        $('#followCategory1').prop('checked', false);
        $('#followCategory2').prop('checked', true);
        $('#followCategory3').prop('checked', false);
        $('#followCategorySelect1').hide();
        $('#followCategorySelect2').show();
        $('#followCategorySelect3').hide();
    } else if (parentId == 3) {
        $('#followCategory1').prop('checked', false);
        $('#followCategory2').prop('checked', false);
        $('#followCategory3').prop('checked', true);
        $('#followCategorySelect1').hide();
        $('#followCategorySelect2').hide();
        $('#followCategorySelect3').show();
    }
    //$.post("http://dev.shop.wili.vn/ajax/getCategoryChildMutilSelect", {parentId: parentId}, function(data) {
    //    $('#CategoryMutilSelect').html(data);
    //});
}
function loadCategoryProductMutilSelect(parentId) {
    if (parentId === 1) {
        $('#productCategory1').prop('checked', true);
        $('#productCategory2').prop('checked', false);
        $('#productCategory3').prop('checked', false);
        $('#productCategory4').prop('checked', false);
        $("div[id='productCategorySelect1']").show();
        $("div[id='productCategorySelect2']").hide();
        $("div[id='productCategorySelect3']").hide();
        $("div[id='productCategorySelect4']").hide();
    } else if (parentId === 2) {
        $('#productCategory1').prop('checked', false);
        $('#productCategory2').prop('checked', true);
        $('#productCategory3').prop('checked', false);
        $('#productCategory4').prop('checked', false);
        $("div[id='productCategorySelect1']").hide();
        $("div[id='productCategorySelect2']").show();
        $("div[id='productCategorySelect3']").hide();
        $("div[id='productCategorySelect4']").hide();

    } else if (parentId === 3) {
        $('#productCategory1').prop('checked', false);
        $('#productCategory2').prop('checked', false);
        $('#productCategory3').prop('checked', true);
        $('#productCategory4').prop('checked', false);
        $("div[id='productCategorySelect1']").hide();
        $("div[id='productCategorySelect2']").hide();
        $("div[id='productCategorySelect3']").show();
        $("div[id='productCategorySelect4']").hide();

    } else if (parentId === 4) {
        $('#productCategory1').prop('checked', false);
        $('#productCategory2').prop('checked', false);
        $('#productCategory3').prop('checked', false);
        $('#productCategory4').prop('checked', true);
        $("div[id='productCategorySelect1']").hide();
        $("div[id='productCategorySelect2']").hide();
        $("div[id='productCategorySelect3']").hide();
        $("div[id='productCategorySelect4']").show();

    }
}
function loadCategoryGenderMutilSelect(parentId) {
    if (parentId == 1) {
        $('#genderProductCategory1').prop('checked', true);
        $('#genderProductCategory2').prop('checked', false);
        $('#categoryMale1').show();
        $('#categoryMale2').show();
        $('#categoryMale3').show();
        $('#categoryFemale1').hide();
        $('#categoryFemale2').hide();
        $('#categoryFemale3').hide();
    } else if (parentId == 2) {
        $('#genderProductCategory1').prop('checked', false);
        $('#genderProductCategory2').prop('checked', true);
        $('#categoryMale1').hide();
        $('#categoryMale2').hide();
        $('#categoryMale3').hide();
        $('#categoryFemale1').show();
        $('#categoryFemale2').show();
        $('#categoryFemale3').show();
    }
}
function loadCategoryBusinessMutilSelect(parentId) {
    var gender1 = $('#genderBusinessCategory1').is(':checked');
    var gender0 = $('#genderBusinessCategory2').is(':checked');

    if (parentId == 1) {
        $('#businessCategory1').prop('checked', true);
        $('#businessCategory2').prop('checked', false);
        $('#businessCategory3').prop('checked', false);
        $('#businessCategorySelect1').show();
        $('#businessCategorySelect2').hide();
        $('#businessCategorySelect3').hide();
        if (gender0 === true) {
            $('#categoryBusinessFemale1').show();
            $('#categoryBusinessMale1').hide();
        } else if (gender1 === true) {
            $('#categoryBusinessMale1').show();
            $('#categoryBusinessFemale1').hide();
        }
    } else if (parentId == 2) {
        $('#businessCategory1').prop('checked', false);
        $('#businessCategory2').prop('checked', true);
        $('#businessCategory3').prop('checked', false);
        $('#businessCategorySelect1').hide();
        $('#businessCategorySelect2').show();
        $('#businessCategorySelect3').hide();
        if (gender0 === true) {
            $('#categoryBusinessFemale2').show();
            $('#categoryBusinessMale2').hide();
        } else if (gender1 === true) {
            $('#categoryBusinessMale2').show();
            $('#categoryBusinessFemale2').hide();
        }
    } else if (parentId == 3) {
        $('#businessCategory1').prop('checked', false);
        $('#businessCategory2').prop('checked', false);
        $('#businessCategory3').prop('checked', true);
        $('#businessCategorySelect1').hide();
        $('#businessCategorySelect2').hide();
        $('#businessCategorySelect3').show();
        if (gender0 === true) {
            $('#categoryBusinessFemale3').show();
            $('#categoryBusinessMale3').hide();
        } else if (gender1 === true) {
            $('#categoryBusinessMale3').show();
            $('#categoryBusinessFemale3').hide();
        }
    }
}
function loadCategoryBusinessGenderMutilSelect(parentId) {
    if (parentId == 1) {
        $('#genderBusinessCategory1').prop('checked', true);
        $('#genderBusinessCategory2').prop('checked', false);
        $('#categoryBusinessMale1').show();
        $('#categoryBusinessMale2').show();
        $('#categoryBusinessMale3').show();
        $('#categoryBusinessFemale1').hide();
        $('#categoryBusinessFemale2').hide();
        $('#categoryBusinessFemale3').hide();
    } else if (parentId == 2) {
        $('#genderBusinessCategory1').prop('checked', false);
        $('#genderBusinessCategory2').prop('checked', true);
        $('#categoryBusinessMale1').hide();
        $('#categoryBusinessMale2').hide();
        $('#categoryBusinessMale3').hide();
        $('#categoryBusinessFemale1').show();
        $('#categoryBusinessFemale2').show();
        $('#categoryBusinessFemale3').show();
    }
}
function loadCategoryFollowMutilSelect(parentId) {
    var gender1 = $('#genderFollowCategory1').is(':checked');
    var gender0 = $('#genderFollowCategory2').is(':checked');

    if (parentId == 1) {
        $('#followCategory1').prop('checked', true);
        $('#followCategory2').prop('checked', false);
        $('#followCategory3').prop('checked', false);
        $('#followCategorySelect1').show();
        $('#followCategorySelect2').hide();
        $('#followCategorySelect3').hide();
        if (gender0 === true) {
            $('#categoryFollowFemale1').show();
            $('#categoryFollowMale1').hide();
        } else if (gender1 === true) {
            $('#categoryFollowMale1').show();
            $('#categoryFollowFemale1').hide();
        }
    } else if (parentId == 2) {
        $('#followCategory1').prop('checked', false);
        $('#followCategory2').prop('checked', true);
        $('#followCategory3').prop('checked', false);
        $('#followCategorySelect1').hide();
        $('#followCategorySelect2').show();
        $('#followCategorySelect3').hide();
        if (gender0 === true) {
            $('#categoryFollowFemale2').show();
            $('#categoryFollowMale2').hide();
        } else if (gender1 === true) {
            $('#categoryFollowMale2').show();
            $('#categoryFollowFemale2').hide();
        }
    } else if (parentId == 3) {
        $('#followCategory1').prop('checked', false);
        $('#followCategory2').prop('checked', false);
        $('#followCategory3').prop('checked', true);
        $('#followCategorySelect1').hide();
        $('#followCategorySelect2').hide();
        $('#followCategorySelect3').show();
        if (gender0 === true) {
            $('#categoryFollowFemale3').show();
            $('#categoryFollowMale3').hide();
        } else if (gender1 === true) {
            $('#categoryFollowMale3').show();
            $('#categoryFollowFemale3').hide();
        }
    }
}
function loadCategoryFollowGenderMutilSelect(parentId) {
    if (parentId == 1) {
        $('#genderFollowCategory1').prop('checked', true);
        $('#genderFollowCategory2').prop('checked', false);
        $('#categoryFollowMale1').show();
        $('#categoryFollowMale2').show();
        $('#categoryFollowMale3').show();
        $('#categoryFollowFemale1').hide();
        $('#categoryFollowFemale2').hide();
        $('#categoryFollowFemale3').hide();
    } else if (parentId == 2) {
        $('#genderFollowCategory1').prop('checked', false);
        $('#genderFollowCategory2').prop('checked', true);
        $('#categoryFollowMale1').hide();
        $('#categoryFollowMale2').hide();
        $('#categoryFollowMale3').hide();
        $('#categoryFollowFemale1').show();
        $('#categoryFollowFemale2').show();
        $('#categoryFollowFemale3').show();
    }
}
function insertFormLogoShop() {
    $('#insertFormLogoShop').html("<div class='form-group'><label for='fileDealImages' class='col-lg-3 control-label'>Logo Shop Mới</label><div class='col-lg-7'><input type='file' class='form-control' name='fileLogoImages' id='fileDealImages'></div></div>");

}
function insertFormDescribed() {
    $('#addAttribute').append("<div class='form-group'><label for='inputPrice' class='col-lg-3 control-label'>Mô tả:</label><div class='col-lg-7'><input type='text' class='form-control' id='titleDescribed' name='titleAttribute[]' placeholder='Màu Sắc' style='width:150px !important' /></div></div><div class='form-group'><label for='inputPrice' class='col-lg-3 control-label'>Nội dung mô tả:</label><div class='col-lg-7'><input type='text' class='form-control' id='contentAttribute' name='contentAttribute[]' placeholder='Xanh, Cam'  /></div></div>");
}
function outPrice() {
    var inputData = $('#inputPrice').val()
    if (isNaN(inputData) === false) {
        var arrPrice = inputData.split("");
        var countArr = 1;
        var strKq = '';
        for (var i = arrPrice.length - 1; i >= 0; i--) {
            if (countArr % 3 == 1 && countArr > 1)
                strKq = arrPrice[i] + '.' + strKq;
            else
                strKq = arrPrice[i] + strKq;
            countArr++;
        }
        $('#outPrice').html(strKq);
    } else {
        var lenStr = inputData.length;
        var strKq = inputData.substr(0, lenStr - 1);
        $('#inputPrice').val(strKq);

    }
}
function showDivUpload() {
    $('#uploadFile').modal('show');
}
$(document).ready(function () {
    document.getElementById('files').addEventListener('change', handleFileSelect, false);
});
function handleFileSelect(evt) {
    var files = evt.target.files; // FileList object

    // files is a FileList of File objects. List some properties.
    console.log(files);

    //var countImage = files.length;
    //for (var i = 0; i < countImage; i++) {
    //   $.post(SITE_URL+"ajax/handleFileImage",{filename: files[i]['name'], filetype: files[i]['type'], filetmp: files[i]['tmp_name']},function(data){
    //      alert(data);
    // });
    //}

}
function handleUrlChange() {
    var url = $('#inputUrl').val();
    $('#inputUrl').attr('disabled', true);
    $('#btnUrl').attr('disabled', true);
    $.post(SITE_URL + "ajax/handleUrlImage", {url: url}, function (data) {
        pathImage = UPLOAD_URL + 'temp/' + data + '.jpg';
        $('#showImageUrl').prepend("<div style='width:300px; float:left'><div><img src=" + pathImage + " style='width:290px'><input type='hidden' name='arrFileDetailImages[]' value=" + data + " /></div><div><input type='text' class='form-control'  name='arrNameDetailImages[]' placeholder='Mô tả hình ảnh' style='width:290px !important' /></div><div><input type='radio' checked name='image_represent' value=" + data + "> Chọn làm ảnh đại diện</div></div>");
        $('#inputUrl').removeAttr('disabled');
        $('#btnUrl').removeAttr('disabled');
        $('#inputUrl').val('');
    });
}
function hiddenUploadUrl() {
    $('#uploadComputer').css("display", "none");
}
function hiddenUploadComputer() {
    $('#uploadUrl').css("display", "none");
}


