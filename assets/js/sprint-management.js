import '../css/sprint-management.scss';
import {client} from './rest/routing';

var oGoal = {
    selectors: {
        form: '#add-sub-goal-form',
        addSubGoal: '.add-sub-goal',
        name: '.form-name',
        subGoalsList: '#sub-goals-list',
        deleteSubGoal: '.delete-sub-goal',
        listGroupItem: '.list-group-item',
        deleteUser: '.delete-user',
        addUser: '.add-btn',
        menuEdit: '.profile-menu-edit',
        noSubGoals: '.no-sub-goals',
        goalDesc: '.form-desc'
    },
    data: {},
    init: function () {
        $(oGoal.selectors.form).on('submit', function (e) {
            e.preventDefault();
        });
        $(oGoal.selectors.addSubGoal).on('click', function () {
            $(oGoal.selectors.noSubGoals).attr('style', 'display:none')
            let sprintId = $(this).data("id");
            let name = $(oGoal.selectors.name);
            let goalDesc = $(oGoal.selectors.goalDesc);
            client.goal.create({
                name: name.val(),
                sprintId: sprintId,
                'goalDesc': goalDesc.val()
            }).done(function (data) {
                if (data[0]['success']) {
                    oGoal.addToList(name.val(), data[0]['id']);
                    name.val("");
                    goalDesc.val("")
                } else {
                    oGoal.showAlert();
                }
            });
        });
        $(document).on('click', oGoal.selectors.deleteSubGoal, function () {
            if (confirm('Czy napewno chcesz usunąć?')) {
                let goalId = $(this).data("id");
                let ele = $(this);
                client.goal.destroy(goalId).done(function (data) {
                    if (data[0]['success']) {
                        ele.closest('li').remove();
                    }
                });
            }
        })
    },
    showAlert: function () {
        $("#sub-goal-alert").fadeTo(2000, 500).slideUp(500, function () {
            $("#sub-goal-alert").slideUp(500);
        });
    },
    addToList: function (name, id) {
        const ul = document.querySelector((oGoal.selectors.subGoalsList));
        const li = document.createElement("li");
        const butt = document.createElement("button");
        li.setAttribute('class', "list-group-item");
        let link = document.createElement('a');
        let text = document.createTextNode(name);
        link.appendChild(text);
        link.href = "/goal-management/" + id;
        link.classList.add('name-button');
        li.appendChild(link);
        butt.setAttribute('class', 'fas fa-trash-alt delete-sub-goal')
        butt.setAttribute('data-id', id);
        li.appendChild(butt);
        ul.appendChild(li);

    }
};

$(document).ready(function () {
    oGoal.init();
});
