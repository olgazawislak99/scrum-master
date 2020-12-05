import '../css/navbar.scss';
import {client} from './rest/routing';

$(document).ready(function (){
    $('.project').on('click', function (){
        let projectId = $(this).data('project-id');
        $(this).addClass('actual');
        client.project.update(projectId).done(function (){
            window.location.href = '/'
        });
    })
})