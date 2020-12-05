import '../css/actual-sprint.scss';

var oActualManage = {
    selectors: {
        hidden: 'hidden',
        goalsList: '#goals-list',
        progress: '.progress-bar',
        check: '.check',
        down: '.sub-goals-down',
        goal: '.goal',

    },
    data: {},
    init: function () {
        oActualManage.getProgress();
        $(oActualManage.selectors.down).on('click', function () {
            $(this).find('i').toggleClass('fas fa-chevron-down');
            $(this).find('i').toggleClass('fas fa-chevron-up');
            $(this).next('p').toggleClass('hidden');
        })
    },
    getProgress: function () {
        $(oActualManage.selectors.goalsList).each(function () {
            let goals = $(oActualManage.selectors.goal);
            let goalProgress = 100 / (goals.length);
            let progressBar = $(oActualManage.selectors.progress);
            let actualProgress = 0;
            goals.each(function () {
                if ($(this).find(oActualManage.selectors.check).hasClass('green')) {
                    progressBar.attr('style', "width:" + (actualProgress + goalProgress) + "%;");
                    actualProgress += goalProgress;
                }
            });
            if (actualProgress <= 20) {
                progressBar.addClass('one');
                progressBar.removeClass('two three four five');
            } else if (actualProgress <= 40) {
                progressBar.addClass('two');
                progressBar.removeClass('one three four five');
            } else if (actualProgress <= 60) {
                progressBar.addClass('three');
                progressBar.removeClass('two one four five');
            } else if (actualProgress <= 80) {
                progressBar.addClass('four');
                progressBar.removeClass('two three one five');
            } else {
                progressBar.addClass('five');
                progressBar.removeClass('two three four one');
            }
        })

    }
};

$(document).ready(function () {
    oActualManage.init();
});
