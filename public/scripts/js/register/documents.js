$(document).ready(function() {
    $('#sheepItForm').sheepIt({
        allowRemoveLast: true,
        allowRemoveCurrent: true,
        allowRemoveAll: true,
        allowAdd: true,
        allowAddN: true,
        maxFormsCount: 10,
        minFormsCount: 0,
        iniFormsCount: 1
    });
 
});