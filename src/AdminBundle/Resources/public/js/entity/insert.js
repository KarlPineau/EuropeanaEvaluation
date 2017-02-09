$(document).ready(function() {
    function addPrototype(container, name, index) {
        $(container).append($(container.attr('data-prototype').replace(/__name__label__/g, name+' n°'+(index+1)+' :').replace(/__name__/g, index)));
    }

    function deleteEntityFetch(index)
    {
        $('#adminbundle_entitiesfetch_entitiesFetch_'+index).parent().remove();
    }

    function addEntityFetch(container, index, addProto)
    {
        if(addProto == true) {addPrototype(container, 'EntityFetch ', index);}

        $('#adminbundle_entitiesfetch_entitiesFetch_'+index).addClass('row');

        $('#adminbundle_entitiesfetch_entitiesFetch_'+index+'_uri').parent().addClass('form-group col-md-11');
        $('#adminbundle_entitiesfetch_entitiesFetch_'+index+'_uri').prev().addClass('control-label col-md-3');
        $('#adminbundle_entitiesfetch_entitiesFetch_'+index+'_uri').addClass('form-control').wrap('<div></div>')
        $('#adminbundle_entitiesfetch_entitiesFetch_'+index+'_uri').parent().addClass('col-md-9');


        $('#adminbundle_entitiesfetch_entitiesFetch_'+index).append('' +
            '<div class="col-md-1 text-right">' +
            '   <button type="button" class="btn btn-danger" id="delete_adminbundle_entitiesfetch_entitiesFetch_'+index+'">' +
            '       <span class="glyphicon glyphicon-remove"></span>' +
            '   </button>' +
            '</div>');
        $('#delete_adminbundle_entitiesfetch_entitiesFetch_'+index).on('click', function() {deleteEntityFetch(index);});

    }

    var container_entityFetch = $('#adminbundle_entitiesfetch_entitiesFetch');
        container_entityFetch.prev().hide();

    var oldContent = container_entityFetch.html(); container_entityFetch.html('');

        container_entityFetch.append('' +
            '<div class="text-right">' +
            '   <button type="button" id="buttonAddEntityFetch" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span></button>' +
            '</div>');
        $('#buttonAddEntityFetch').on('click', function() {
            ++index;
            addEntityFetch(container_entityFetch, index, true);
        });

        container_entityFetch.append(oldContent);

        for(var i = 0 ; i < $('div[id^="adminbundle_entitiesfetch_entitiesFetch_"]').length ; i++) {
            addEntityFetch(container_entityFetch, i, false);
        }

        if(i === 'undefined') { var index = 0;} else { var index = i;}

        if(index == 0) {
            addEntityFetch(container_entityFetch, index, true);
        }
});