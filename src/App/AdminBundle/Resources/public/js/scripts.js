
$('.multi-select').multiSelect({
    selectableHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='rechercher...'>",
    selectionHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='rechercher...'>",
    selectableFooter: "<div style='width: 100%;text-align: center;'>Ajouter <i class='fa fa-arrow-circle-right'></i></div>",
    selectionFooter: "<div style='width: 100%;text-align: center;'><i class='fa fa-arrow-circle-left'></i> Retirer</div>",
    afterInit: function(ms){
        var that = this,
            $selectableSearch = that.$selectableUl.prev(),
            $selectionSearch = that.$selectionUl.prev(),
            selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
            selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';

        that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
            .on('keydown', function(e){
                if (e.which === 40){
                    that.$selectableUl.focus();
                    return false;
                }
            });

        that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
            .on('keydown', function(e){
                if (e.which == 40){
                    that.$selectionUl.focus();
                    return false;
                }
            });
    },
    afterSelect: function(){
        this.qs1.cache();
        this.qs2.cache();
    },
    afterDeselect: function(){
        this.qs1.cache();
        this.qs2.cache();
    }

});

$('.spinner4').spinner({value:0, step: 1, min: 0, max: 200000});

tinymce.init({
    force_br_newlines : true,
    force_p_newlines : false,
    forced_root_block : false,
    selector: "textarea",
    theme: "modern",
    language : 'fr_FR',
    plugins: [
        "link",
    ],
    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link"
});

$('#nav-accordion').dcAccordion({
    eventType: 'click',
    autoClose: true,
    saveState: true,
    disableLink: true,
    speed: 'fast',
    showCount: false,
    autoExpand: true,
//        cookie: 'dcjq-accordion-1',
    classExpand: 'dcjq-current-parent'
});


$('.dpYears').datepicker({
    format: "dd/mm/yyyy",
    language: 'fr'
});

function deleteModal(location, content){
    $("#deleteModal").modal();
    $("#deleteModal .modal-body").html(content);
    $("#deleteModal .modal-footer .btn-danger").on('click', function(){
        document.location.href= location;
    });
}