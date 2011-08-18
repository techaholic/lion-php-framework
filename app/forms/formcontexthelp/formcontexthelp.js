
function showHelpHint(event) {
    formElement = Event.element(event);
    inputId = formElement.id;
    
    var dimensions = formElement.getDimensions();
    var position   = formElement.positionedOffset();

    if($(inputId + '_contextHelp')) {
        var helpHint = $(inputId + '_contextHelp');
        helpHint.style.display = "inline";
        helpHint.style.left = (position.left + dimensions.width + 30) + 'px';
        helpHint.style.top  = position.top + 'px';
        $(inputId + '_contextHelpPointer').style.display = "inline";
    }
}

function hideHelpHint(event) {
    formElement = Event.element(event);
    inputId = formElement.id;
    if($(inputId + '_contextHelp')) {
        $(inputId + '_contextHelp').style.display = "none";
        $(inputId + '_contextHelpPointer').style.display = "none";
    }
}

function prepareInputsForHints() {

    var inputs = document.getElementsByTagName("input");
    for (var i=0; i<inputs.length; i++){
        if($(inputs[i].id + '_contextHelp')) {
	        inputs[i].onfocus = showHelpHint;
	        inputs[i].onblur = hideHelpHint;
	    }
    }

    var selects = document.getElementsByTagName("select");
    for (var k=0; k<selects.length; k++){
        if($(selects[k].id + '_contextHelp')) {
	        selects[k].onfocus = showHelpHint;
	        selects[k].onblur = hideHelpHint;
	    }
    }

    var textareas = document.getElementsByTagName("textarea");
    for (var m=0; m<textareas.length; m++){
        if($(textareas[m].id + '_contextHelp')) {
	        textareas[m].onfocus = showHelpHint;
	        textareas[m].onblur = hideHelpHint;
	    }
    }
}
