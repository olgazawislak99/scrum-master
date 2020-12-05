import '../css/goal-management.scss';
import {client} from './rest/routing';

var oGoalManage = {
    selectors: {
        desc: '.form-desc',

    },
    data: {},
    init: function () {
        let wto;
        $(oGoalManage.selectors.desc).on('change', function () {
            let ele = $(this);
            clearTimeout(wto);
            wto = setTimeout(function() {
                let desc = ele.val();
                let goalId = ele.data('id');
                client.goalDesc.update(goalId, {'desc' : desc});
            }, 1000)
            });

    }
};

$(document).ready(function () {
    oGoalManage.init();
});
