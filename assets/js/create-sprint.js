import '../css/create-sprint.scss';

const oGoalManage = {
    selectors: {
        input1: '.input1',
        input2: '.input2',

    },
    data: {},
    init: function () {
        $(oGoalManage.selectors.input1).datepicker({
            onSelect: function () {
                console.log($(oGoalManage.selectors.input1).val());
                $(oGoalManage.selectors.input2).datepicker({
                    firstDay: 1,
                    dateFormat: 'dd-mm-yy',
                    defaultDate: $(oGoalManage.selectors.input1).val(),
                });
            },
            firstDay: 1,
            dateFormat: 'dd-mm-yy',

        })

    }
};

$(document).ready(function () {
    oGoalManage.init();
});
