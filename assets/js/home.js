import '../css/home.scss';
import {client} from './rest/routing';

var oHomeManage = {
    selectors: {
        deleteGoal: '.delete-goal',
        goalName: '.goal-name',
        goalDescription: '.goal-desc',
        hidden: 'hidden',
        listItem: '.list-group-item',
        check: '.check',
        progress: '.progress-bar',
        done: '.green',
        listGroupSubGoal: '.sub-goals-list',
        goal: '.goal',
        goalsList: '.timeline'
    },
    data: {},
    init: function () {
        $(oHomeManage.selectors.deleteGoal).on('click', function () {
            if (confirm('Czy napewno chcesz usunąć?')) {
                let goalId = $(this).data("id");
                let ele = $(this);
                client.goal.destroy(goalId).done(function (data) {
                    if (data[0]['success']) {
                        ele.closest('li').next(oHomeManage.selectors.goalDescription).remove();
                        ele.closest('li').remove();
                    }
                });
            }
        });
        $(oHomeManage.selectors.goalName).on('click', function () {
            $(this).closest(oHomeManage.selectors.listItem).next(oHomeManage.selectors.goalDescription).toggleClass(oHomeManage.selectors.hidden);
        });
        $(oHomeManage.selectors.check).on('click', function () {
            $(this).toggleClass('green');
            let goalId = $(this).data("goal-id");
            client.goal.update(goalId, {isDone: $(this).hasClass('green')});

        });
        $(oHomeManage.selectors.goal).odd().addClass('timeline-inverted');
        if (document.getElementsByClassName('no-goals').length !== 0) {
            $(oHomeManage.selectors.goalsList).attr('style', 'display:none;');
        }
        oHomeManage.getProgress();
    },
    getProgress: function () {
        $(oHomeManage.selectors.listGroupSubGoal).each(function () {
            let subGoals = $(this).children();
            let goalProgress = 100 / subGoals.length;
            let progressBar = $(this).prev('div').find(oHomeManage.selectors.progress);
            let actualProgress = 0;
            subGoals.each(function () {
                if ($(this).find(oHomeManage.selectors.check).hasClass('green')) {
                    progressBar.attr('style', "width:" + (actualProgress + goalProgress) + "%;");
                    actualProgress += goalProgress;
                }
            })
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
    oHomeManage.init();
});


