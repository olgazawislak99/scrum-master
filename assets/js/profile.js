import '../css/profile.scss';
import {client} from './rest/routing';

const oProfileManage = {
    selectors: {
        menuEdit: '.profile-menu-edit',
        form: '#change-pwd-form',
        change: '.change-pwd',
        but: '.but',
        formGroup: '.form-group',
        oldPwd: '#old-pwd',
        newPwd: '#new-pwd',
        confirmPwd: '#confirm-pwd',
        photoInput: '#upload_photo_form_photo',
        addFile: '.addFile',
        photoError: '.photo-error',


    },
    data: {},
    init: function () {
        $(oProfileManage.selectors.form).on('submit', function (e) {
            e.preventDefault();
        });
        $(oProfileManage.selectors.change).on('click', function () {
            let userId = $(this).data('id');
            let prev = oProfileManage.selectors.formGroup;
            let oldPwd = $(this).closest(oProfileManage.selectors.but).prev(prev).prev(prev).prev(prev).find(oProfileManage.selectors.oldPwd).val()
            let newPwd = $(this).closest(oProfileManage.selectors.but).prev(prev).prev(prev).find(oProfileManage.selectors.newPwd).val();
            let confirmPwd = $(this).closest(oProfileManage.selectors.but).prev(oProfileManage.selectors.formGroup).find(oProfileManage.selectors.confirmPwd).val()
            client.user.password.update(userId, {
                'oldPwd': oldPwd,
                'newPwd': newPwd,
                'confirmPwd': confirmPwd
            }).done(function (data) {
                if (!data[0]['success']) {
                    oProfileManage.showAlert();
                } else {
                    oProfileManage.showAlertSuccess();
                }
            });
        });
        $(oProfileManage.selectors.addFile).on('click', function () {
            $(oProfileManage.selectors.photoInput).click();
            return false;
        });
        let errorPhoto = $(oProfileManage.selectors.photoError).text();
        errorPhoto = errorPhoto.trim();
        if(errorPhoto !== ''){
            $(oProfileManage.selectors.menuEdit).tab('show');
        }

    },
    showAlert: function () {
        $(".alert-fail").fadeTo(2000, 500).slideUp(500, function () {
            $(".alert-fail").slideUp(500);
        });
    },
    showAlertSuccess: function () {
        $(".alert-suc").fadeTo(2000, 500).slideUp(500, function () {
            $(".alert-suc").slideUp(500);
        });
    },
};

$(document).ready(function () {
    oProfileManage.init();
});

