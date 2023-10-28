function addFormValues(keyList) {
    let allFormkeyList = {};
    var createEventForm = document.forms['create-event-form'];

    if (!createEventForm) {
        return false;
    }

    for (var key of keyList) {
        var formField = createEventForm.elements[key];
        if (formField) {
            var value = formField.value;
            allFormkeyList[key] = value;        
        }
       
    }
    return allFormkeyList;
}


