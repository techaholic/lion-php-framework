/**
 * This class handles the synchronization between the client and the server,
 * by sending all client values + events to the server and update the client
 * with the server responses.
 *
 */
var __ClientEventHandler = Class.create({

    initialize: function() {
        this.code = null;
        this.url  = "index.ajax";
        this.debug = false;
        this.valueHolders = new Hash();
        this._latestInteractiveResponse = null;
        this.allowEvents = true;
    },

    setCode: function(code) {
        this.code = code;
    },
    
    getCode: function() {
        return this.code;
    },
    
    setUrl: function(url) {
        this.url = url;
    },

    getUrl: function() {
        return this.url;
    },    
    
    setDebug: function(debug) {
        this.debug = debug;
    },
    
    getDebug: function() {
        return this.debug;
    },
    
    registerValueHolder: function(valueHolder) {
        this.valueHolders.set(valueHolder.getCode(), valueHolder);  
    },
    
    updateValueHolder: function(code, value) {
        var valueHolder = this.valueHolders.get(code);
        if(typeof(valueHolder) != 'undefined') {
            if(value == null) {
                value = "";
            }
            valueHolder.setValue(value);
        }
    },
    
    getClientValues: function() {
        var returnValue = new Hash();
        this.valueHolders.each(function(pair) {
            if(pair.value.isUnsynchronized()) {
                var value = pair.value.getValue();
                if(value == null) {
                    value = "";
                }
                returnValue.set(pair.key, value);
            }
        });
        return returnValue;
    },    
    
    startWaiting: function(componentId, showProgressBar) {
        var progressHandler = (__ProgressBroadcaster.getInstance()).getProgressHandler(componentId);
        if(typeof(progressHandler) != 'undefined') {
            progressHandler.startWaiting();
        }
        if(window[componentId + '_spinner']) {
            window[componentId + '_spinner']['startWaiting']();
            if(showProgressBar == true) {
                window[componentId + '_spinner']['startProgressBar']();
            }
            return window[componentId + '_spinner'];
        }
    },

    stopWaiting: function(componentId) {
        if(window[componentId + '_spinner']) {
            window[componentId + '_spinner']['stopWaiting']();
        }
        var progressHandler = (__ProgressBroadcaster.getInstance()).getProgressHandler(componentId);
        if(typeof(progressHandler) != 'undefined') {
            progressHandler.stopWaiting();
        }        
    },

    _addClientEndPointValuesToForm: function(form) {
        var clientEndpointValues = document.createElement("input");
        clientEndpointValues.setAttribute("type", "hidden");
        clientEndpointValues.setAttribute("name", "clientEndpointValues");
        clientEndpointValues.setAttribute("value", this.getClientValues().toJSON());
        form.appendChild(clientEndpointValues);
    },

    _waitToSubmitForm: function(form) {
        var progressHandler = (__ProgressBroadcaster.getInstance()).getProgressHandler(form.id);
        if(typeof(progressHandler) != 'undefined' && progressHandler.isWaiting()) {
            window.setTimeout((this._waitToSubmitForm).bind(this, form), 500);
        }
        else {
            this._addClientEndPointValuesToForm(form);
            form.submit();
        }
    },

    _handleFormWaiting: function(form) {
        var progressHandler = (__ProgressBroadcaster.getInstance()).getProgressHandler(form.id);
        if(typeof(progressHandler) != 'undefined' && progressHandler.isWaiting()) {
            //put an spinner to each submit button, in order to disable and show the progress bar (if applicable)
            var submitButtons = form.getInputs('submit');
            for(var i = 0, totalSubmitButtons = submitButtons.length; i < totalSubmitButtons; i++) {
                var submitButton = submitButtons[i];
                var spinner = this.startWaiting(submitButton.id);
                if(typeof(spinner) != 'undefined') {
                    progressHandler.registerUiProgressIndicator(spinner);
                }
            }
            //wait to submit until progress completed:
            this._waitToSubmitForm(form);
        }
    },

    handleSubmit: function(form) {
        //do not allow other events while submitting the form
        this.allowEvents = false;
        var progressHandler = (__ProgressBroadcaster.getInstance()).getProgressHandler(form.id);
        if(typeof(progressHandler) != 'undefined' && progressHandler.isWaiting()) {
            this._handleFormWaiting(form);
            return false;
        }        
        else {
            this._addClientEndPointValuesToForm(form);
            return true;
        }
    },

    handleEvent: function (event) {
        var componentId = Event.element(event).id;
        var eventName = event.type;
        var extraInfo = null;
        this.sendEvent(eventName, extraInfo, componentId);
    },
    
    sendEvent: function(eventName, extraInfo, componentId, silentMode) {
        if (!silentMode) {
            this.startWaiting(componentId);
        }
        this._doSendEvent(eventName, extraInfo, componentId);
    },

    _doSendEvent: function(eventName, extraInfo, componentId) {
        if(this.allowEvents) {
            var messageProcessor = new __MessageProcessor();
            new __LionAjaxRequest(this.url, {
                asynchronous: true,
                evalJS: false,
                evalJSON: false,
                method: 'post',
                parameters: {pageId               : this.getCode(), 
                             clientEndpointValues : this.getClientValues().toJSON(), 
                             event                : $H({componentId : componentId, 
                                                        eventName   : eventName, 
                                                        extraInfo   : extraInfo}).toJSON()},
                requestHeaders: {Accept: 'text/javascript, text/html, application/xml, text/xml, application/json, */*'},
                onInteractive: this.handleInteractiveServerResponse.bind(this, componentId, messageProcessor), 
                onSuccess: this.handleServerResponse.bind(this, componentId, messageProcessor),
                onFailure: this.stopWaiting.bind(this, componentId)
           });
	    }
    },

    handleInteractiveServerResponse: function(componentId, messageProcessor, serverResponse) {
        if(serverResponse.responseText) {
            (__JSONHelper.extractJSON(serverResponse.responseText)).each((function(messageProcessor, message) {
                messageProcessor.process(Object.extend(new __Message(), message));
            }).bind(this, messageProcessor));
        }
    },

    handleServerResponse: function(componentId, messageProcessor, serverResponse) {
        this.stopWaiting(componentId);
        if(serverResponse.responseText) {
            var JSONParts = __JSONHelper.extractJSON(serverResponse.responseText);
            JSONParts.each((function(messageProcessor, message) {
                messageProcessor.process(Object.extend(new __Message(), message));
            }).bind(this, messageProcessor));
        }
        if(window.hasOwnProperty('showAllExamples')) {
        	(window['showAllExamples'])();
        }        
    }

});

__LionAjaxRequest = Class.create(Ajax.Request, {
  
    request: function($super, url) {
        //multipart not supported by IE
        if (/MSIE/.test(navigator.userAgent) == false){
            this.transport.multipart = true;
        }
        $super(url);
    }

});

/**
 * Static method to retrieve a __ClientEventHandler singleton instance
 *
 */
__ClientEventHandler.getInstance = function() {
    if (!__ClientEventHandler.hasOwnProperty('instance')) {
        __ClientEventHandler.instance = new __ClientEventHandler();
    }
    return __ClientEventHandler.instance;
};

//**************************************************
// Progress classes: ///////////////////////////////
//**************************************************

__ProgressHandler = Class.create({

    initialize: function(element) {
        this._element  = element;
        this._code     = element.id;
        this._progressHandlers = new Hash();
        this._dependantProgressHandlers = new Hash();
        this._progressIndicator = null;
        this._uiProgressIndicators = new Hash();
        this._waiting = 0;
    },

    getCode: function() {
        return this._code;
    },
    
    startWaiting: function() {
        this._waiting += 1;
        this._dependantProgressHandlers.each(function(pair) {
            (pair.value).startWaiting();
        });
    },

    stopWaiting: function() {
        if(this.isWaiting()) {
            this._waiting -= 1;
            this._dependantProgressHandlers.each(function(pair) {
                (pair.value).stopWaiting();
            });
        }
    },
    
    isWaiting: function() {
        var returnValue = false;
        if(this._waiting > 0) {
            returnValue = true;
        }
        return returnValue;
    },
    
    registerUiProgressIndicator: function(uiProgressIndicator) {
        this._uiProgressIndicators.set(uiProgressIndicator.getCode(), uiProgressIndicator);  
    },
    
    registerProgressHandler: function(progressHandler) {
        this._progressHandlers.set(progressHandler.getCode(), progressHandler); 
        progressHandler.registerDependantProgressHandler(this);
    },
    
    registerDependantProgressHandler: function(dependantProgressHandler) {
        this._dependantProgressHandlers.set(dependantProgressHandler.getCode(), dependantProgressHandler);  
    },
    
    setProgress: function(progress) {
        if(this._progressIndicator == null) {
            this._progressIndicator = new __ProgressIndicator(this._code);
        }
        (this._progressIndicator).setProgress(progress);
        this.updateUIProgressIndicators();
        this._dependantProgressHandlers.each(function(pair) {
            (pair.value).updateUIProgressIndicators();
        });
    },
    
    getProgress: function() {
        var returnValue = 0;
        var totalProgressIndicators = 0;
        if(this._progressIndicator != null) {
            totalProgressIndicators += 1;
            returnValue = this._progressIndicator.getProgress();
        }
        this._progressHandlers.each(function(pair) {
            if((pair.value).isWaiting() || (pair.value).getProgress() == 100) {
                returnValue += (pair.value).getProgress();
                totalProgressIndicators += 1;
            }
        });
        if(totalProgressIndicators > 0) {
            returnValue = Math.round(returnValue / totalProgressIndicators);
        }
        return returnValue;
    },
    
    updateUIProgressIndicators: function() {
        var progress = this.getProgress();
        this._uiProgressIndicators.each(function(pair) {
            (pair.value).setProgress(progress);
        });
    }

});

__ProgressIndicator = Class.create({

    initialize: function(code) {
        this._code = code;
        this._progress = 0;
    },
    
    getCode: function() {
        return this._code;
    },
    
    setProgress: function(progress) {
        if(progress < 0) {
            progress = 0;
        }
        else if(progress > 100) {
            progress = 100;
        }
        this._progress = progress;
    },
    
    getProgress: function() {
        return this._progress;
    }
    
});

__ProgressBroadcaster = Class.create({

    initialize: function() {
        this._progressHandlers = new Hash();
    },

    registerProgressHandler: function(progressHandler) {
        this._progressHandlers.set(progressHandler.getCode(), progressHandler);  
    },
    
    getProgressHandler: function(code) {
        var progressHandler = this._progressHandlers.get(code);
        if(typeof(progressHandler) == 'undefined') {
            if($(code)) {
                var element = $(code);
                progressHandler = new __ProgressHandler(element);
                this.registerProgressHandler(progressHandler);
            }
        }
        return progressHandler;      
    },
    
    setWaitingDependence: function(dependantProgressHandlerCode, progressHandlerCode) {
        var dependantProgressHandler = this.getProgressHandler(dependantProgressHandlerCode);
        var progressHandler = this.getProgressHandler(progressHandlerCode);
        if(typeof(dependantProgressHandler) != 'undefined' && typeof(progressHandler) != 'undefined') {
            dependantProgressHandler.registerProgressHandler(progressHandler);
        }
    },
    
    updateProgress: function(code, progress) {
        var progressHandler = this.getProgressHandler(code);
        if(typeof(progressHandler) != 'undefined') {
            progressHandler.setProgress(progress);
        }
    }     

});

/**
 * Static method to retrieve a __ProgressBroadcaster singleton instance
 *
 */
__ProgressBroadcaster.getInstance = function() {
    if (!__ProgressBroadcaster.hasOwnProperty('instance')) {
        __ProgressBroadcaster.instance = new __ProgressBroadcaster();
    }
    return __ProgressBroadcaster.instance;
};


//**************************************************
// Messaging classes: //////////////////////////////
//**************************************************

var AJAX_MESSAGE_STATUS_OK    = 1;
var AJAX_MESSAGE_STATUS_ERROR = -1;
var AJAX_MESSAGE_STATUS_REDIRECT = 302;

/**
 * This class represents a message.
 */
__Message = Class.create({

    initialize: function() {
        this.header   = null;
        this.commands = new Array();
        this._resolved = false;
    },

    getHeader: function() {
        this._resolve();
        return this.header;
    },
        
    getCommands: function() {
        this._resolve();
        return this.commands;
    },
    
    _resolve: function() {
        if(this._resolved == false) {
            this.header = Object.extend(new __MessageHeader(), this.header);
            var commandInstances = new Array();
            for(var i = 0, totalCommands = this.commands.length; i < totalCommands; i++) {
                if(this.commands[i].hasOwnProperty('class') && window[this.commands[i]['class']] && this.commands[i].hasOwnProperty('data')) {
                    commandInstances[commandInstances.length] = Object.extend(new window[this.commands[i]['class']](), this.commands[i]['data']);
                }
            }
            this.commands = commandInstances;
            this._resolved = true;
        }
    }     

});

/**
 * This class represents the header of a message
 */
__MessageHeader = Class.create({

    initialize: function() {
        this.id     = null;
        this.status = 0;
        this.location = null;
        this.message  = null;
        this.pageId = null;
    },
    
    getId: function() {
        return this.id;
    },
    
    getStatus: function() {
        return this.status;
    },
    
    getLocation: function() {
        return this.location;
    },
    
    getPageId: function() {
        return this.pageId;
    }
        
});


/**
 * This class exposes an static method to process a server incoming message
 */
var __MessageProcessor = Class.create({

    initialize: function() {
        this.processedMessageIds = new Hash();
    },
    
    process: function(message) {
        var messageId = (message.getHeader()).getId();
        var processed = this.processedMessageIds.get(messageId);
        if(typeof(processed) == 'undefined') {
            this.processedMessageIds.set(messageId, true);  
            __MessageProcessor.process(message);
        }
    }

});

__MessageProcessor.process = function(message) {
    var header = message.getHeader();
    var status = header.getStatus();
    //if status is OK:
    if(status == AJAX_MESSAGE_STATUS_OK) {
        var commands = message.getCommands();
        for(var i=0, totalCommands = commands.length; i < totalCommands; i++) {
            //execute the command
            commands[i].execute();
        };
    }
    else {
        switch(status) {
            case AJAX_MESSAGE_STATUS_REDIRECT:
                document.location.href = header.getLocation();
                break;
            case AJAX_MESSAGE_STATUS_ERROR:
            default:
                if (header.message != null && header.message != '') {
                    alert(header.message);
                }
                break;
        }
    }
};

//**************************************************
// Command classes: ////////////////////////////////
//**************************************************

var __RegisterValueHolderCommand = Class.create({
   
    initialize: function() {
        this.valueHolderData = new Hash();
    },
    
    execute: function() {
        var valueHolder = __ValueHolderFactory.createValueHolder(this.valueHolderData);
        if(valueHolder) {
            (__ClientEventHandler.getInstance()).registerValueHolder(valueHolder);
        }
    }
    
});

var __RegisterEventListenerCommand = Class.create({
    
    initialize: function() {
        this.element = null;
        this.event   = null;  
    },
    
    execute: function() {
        if($(this.element)) {   
            if(this.event.toUpperCase() == 'LOAD') {
                (__ClientEventHandler.getInstance()).sendEvent(this.event, null, this.element);
            }
            else {
                var eventHandler = __ClientEventHandler.getInstance();
                $(this.element).observe(this.event, eventHandler.handleEvent.bind(eventHandler));
            }
        }
    }
        
});

var __UpdateValueHolderCommand = Class.create({
   
    initialize: function() {
        this.code  = null;
        this.value = null;
    },
    
    execute: function() {
        (__ClientEventHandler.getInstance()).updateValueHolder(this.code, this.value);
    }
    
});

var __ExecuteCallbackCommand = Class.create({

    initialize: function() {
        this.receiver  = null;
        this.method    = null;
        this.parameter = null;
    },

    execute: function() {
        //to implement in subclasses
    }
    
});


var __ExecuteObjectCallbackCommand = Class.create(__ExecuteCallbackCommand, {
    
    execute: function() {
        if(this.receiver != null && this.method != null) {
            if(window[this.receiver] && window[this.receiver][this.method]) {
                window[this.receiver][this.method](this.parameter);
            }
        }
    }    

});


var __ExecuteElementCallbackCommand = Class.create(__ExecuteCallbackCommand, {

    execute: function() {
        if(this.receiver != null && this.method != null) {
            if($(this.receiver) && $(this.receiver)[this.method]) {
                $(this.receiver)[this.method](this.parameter);
            }
        }
    }
    
});


var __ExecuteJavascriptOnDemandCommand = Class.create({

    initialize: function() {
        this.code = null;
    },

    execute: function() {
        eval(this.code);
    }
    
});

var __UpdateProgressIndicatorCommand = Class.create({

    initialize: function() {
        this.code  = null;
        this.progress = null;
    },
    
    execute: function() {
        (__ProgressBroadcaster.getInstance()).updateProgress(this.code, this.progress);
    }
    
});    
    

var __ShowValidationErrorCommand = Class.create({

    initialize: function(message, receiver) {
        this.message  = message;
        this.receiver = receiver;
    },

    execute: function() {
        if(window[this.receiver] && window[this.receiver]['onInvalid']) {
            window[this.receiver]['message'] = this.message;
            if(this.message != null && this.message != '') {
                window[this.receiver]['validationFailed'] = true;
                window[this.receiver]['displayMessageWhenEmpty'] = true;
                window[this.receiver]['onInvalid']();
            }
            else {
                window[this.receiver]['removeMessageAndFieldClass']();
            }
        }
    }
    
});

//**************************************************
// Value holder classes: ///////////////////////////
//**************************************************

var __ValueHolderFactory = Class.create();
__ValueHolderFactory.createValueHolder = function(valueHolder) {
    var returnValue = null;
    if(valueHolder.hasOwnProperty('valueHolderClass')) {
        var valueHolderClass = valueHolder['valueHolderClass'];
        if(valueHolderClass == '__ElementProperty') {
            var element = valueHolder.receiver;
            if($(element)) {
                var elementType = $(element).type;
                if(elementType == 'select-one') {
                    returnValue = Object.extend(new __SelectElementProperty(), valueHolder);
                }
                else if(elementType == 'checkbox' || elementType == 'radio') {
                    returnValue = Object.extend(new __CheckableElementProperty(), valueHolder);
                }
                else {
                    returnValue = Object.extend(new __ElementProperty(), valueHolder);
                }
            }
        }
        else if(window[valueHolderClass]) {
            returnValue = Object.extend(new window[valueHolderClass](), valueHolder);
        }
    }
    return returnValue;
};


var __ValueHolder = Class.create({
   
    initialize: function() {
        this.code      = null;
        this.receiver  = null;
        this.property  = null;
        this.value     = null;
        this.syncServer   = true;
    }, 
    
    isUnsynchronized: function() {
        var returnValue = false;
        if(this.allowSyncToServer()) {
            returnValue = true;
        }
        return returnValue;
    },
        
    //to be overwrited by children classes:
    allowSyncToServer: function() {
        return true;
    },
        
    getCode: function() {
        return this.code;
    },
    
    getReceiver: function() {
        return this.receiver;
    },
    
    getProperty: function() {
        return this.property;
    },    

    setValue: function(value) {
        this.value = value;
    },    
    
    getValue: function() {
        return null;
    }
    
});

var __ElementProperty = Class.create(__ValueHolder, {

    setValue: function(value) {
        if( this.receiver != null && value != this.getValue() ) {
            if(document.getElementById(this.receiver)) {
                var receiver = $(this.receiver);
                if(this.property.toUpperCase() == 'INNERHTML') {
                    receiver.innerHTML = value;
                }
                else if(this.property.toUpperCase() == 'VALUE') {
                    receiver.value = value;
                }                
                else if(receiver[this.property]) {
                    receiver[this.property] = value;
                }
                else {
                    receiver.writeAttribute(this.property, value);
                }
            }
        }
    },
        
    getValue: function() {
        var returnValue = null;
        if( this.receiver != null ) {
            if(document.getElementById(this.receiver)) {
                var receiver = $(this.receiver);
                if(this.property.toUpperCase() == 'INNERHTML') {
                    returnValue = receiver.innerHTML;
                }
                else if(this.property.toUpperCase() == 'VALUE') {
                	if(receiver['exampleValue'] && receiver['exampleValue'] == receiver.value) {
                		returnValue = null;
                	}
                	else {
                        returnValue = receiver.value;
                	}
                }
                else if(receiver[this.property]) {
                    returnValue = receiver[this.property];
                }
                else {
                    returnValue = receiver.readAttribute(this.property);
                }
            }
        }
        return returnValue;
    },
    
    allowSyncToServer: function() {
        var returnValue = false;
        if( this.receiver != null && document.getElementById(this.receiver)) {
            returnValue = true;
        }
        return returnValue;
    }
    
});

var __ElementAccessor = Class.create(__ValueHolder, {

    initialize: function() {
        this.code      = null;
        this.receiver  = null;
        this.setter    = null;
        this.getter    = null;
        this.value     = null;
    }, 

    setValue: function(value) {
        if( this.receiver != null && this.setter != null ) {
            if( this.getter == null || (this.getter != null && value != this.getValue()) ) {
                if(document.getElementById(this.receiver)) {
                    var receiver = $(this.receiver);
                    if(typeof(receiver[this.setter]) == 'function') {
                        receiver[this.setter](value);
                    }
                }
            }
        }
    },
        
    getValue: function() {
        var returnValue = null;
        if( this.receiver != null && this.getter != null) {
            if(document.getElementById(this.receiver)) {
                var receiver = $(this.receiver);
                if(typeof(receiver[this.getter]) == 'function') {
                    returnValue = receiver[this.getter]();
                }
            }
        }
        return returnValue;
    },
    
    allowSyncToServer: function() {
        var returnValue = false;
        if( this.receiver != null && this.getter != null) {
            if(document.getElementById(this.receiver)) {
                var receiver = $(this.receiver);
                if(typeof(receiver[this.getter]) == 'function') {
                    returnValue = true;
                }
            }
        }
        return returnValue;
    }    
    
});


var __CheckableElementProperty = Class.create(__ValueHolder, {

    setValue: function(value) {
        if( this.receiver != null && value != this.getValue()) {
            if(document.getElementById(this.receiver)) {
                if(this.property.toUpperCase() == 'VALUE') {
                    if(value == true) {
                        $(this.receiver).checked = true;
                    }
                    else {
                        $(this.receiver).checked = false;
                    }
                }
            }
        }
    },
        
    getValue: function() {
        var returnValue = false;
        if( this.receiver != null ) {
            if(document.getElementById(this.receiver)) {
                if(this.property.toUpperCase() == 'VALUE') {
                    returnValue = $(this.receiver).checked;
                }
            }
        }
        return returnValue;
    },
    
    allowSyncToServer: function() {
        var returnValue = false;
        if( this.receiver != null && document.getElementById(this.receiver) && this.property.toUpperCase() == 'VALUE') {
            returnValue = true;
        }
        return returnValue;
    }    
    
});

var __SelectElementProperty = Class.create(__ValueHolder, {

    setValue: function(value) {
        if( this.receiver != null ) {
            if(document.getElementById(this.receiver)) {
                if(this.property.toUpperCase() == 'SELECTEDINDEX') {
                    $(this.receiver).selectedIndex = value;
                }
                else if(this.property.toUpperCase() == 'ITEMS') {
                    var receiver = document.getElementById(this.receiver);
                    var previousSelectedIndex = receiver.options.selectedIndex;
                    receiver.options.length = 0;
                    if( !this.isEmpty(value) ) {
                        for (var i = 0, totalItems = value.size(); i < totalItems; i++) {
                            var item = value[i];
                            var selected = false;
                            if(receiver.options.length == previousSelectedIndex) {
                                selected = true;
                            }
                            var itemValue = item[1];
                            if(itemValue == null) {
                                itemValue = "";
                            }
                            receiver.options[receiver.options.length] = new Option(item[0], itemValue, false, selected);
                        }
                    }
                }
            }
        }
    },    
    
    getValue: function() {
        var returnValue = null;
        if( this.receiver != null ) {
            if(document.getElementById(this.receiver)) {
                if(this.property.toUpperCase() == 'SELECTEDINDEX') {
                    returnValue = $(this.receiver).selectedIndex;
                }
                else if(this.property.toUpperCase() == 'ITEMS') {
                    returnValue = new Array();
                    for(var i=0, totalItems = $(this.receiver).options.length; i < totalItems;i++) {
                        var value = $(this.receiver).options[i].value;
                        if(value == null) {
                            value = "";
                        }
                        returnValue[returnValue.length] = [$(this.receiver).options[i].text, value];
                    }
                }
            }
        }
        return returnValue;
    },
    
    allowSyncToServer: function() {
        var returnValue = false;
        if( this.receiver != null && document.getElementById(this.receiver) ) {
            returnValue = true;
        }
        return returnValue;
    },    

    /**
    * Auxiliar methods:
    */
    typeOf: function (value) {
        var s = typeof value;
        if (s === 'object') {
            if (value) {
                if (typeof value.length === 'number' &&
                !(value.propertyIsEnumerable('length')) &&
                typeof value.splice === 'function') {
                    s = 'array';
                }
            } else {
                s = 'null';
            }
        }
        return s;
    },


    isEmpty: function(o) {
        var i, v;
        if (this.typeOf(o) === 'array') {
            for (i in o) {
                v = o[i];
                if (v !== undefined && this.typeOf(v) !== 'function') {
                    return false;
                }
            }
        }
        return true;
    }    
    
    
    
    
});

var __ObjectProperty = Class.create(__ValueHolder, {

    getPropertyAccessor: function(accessor_prefix) {
        var returnValue = null;
        if(this.receiver != null && this.property != null) {
            if(window[this.receiver]) {
                if(window[this.receiver][accessor_prefix + this.property.capitalize()]) {
                    returnValue = accessor_prefix + this.property.capitalize();
                }
                else if(window[this.receiver][accessor_prefix + this.property.charAt(0).toUpperCase() + this.property.substr(1)]) {
                    returnValue = accessor_prefix + this.property.charAt(0).toUpperCase() + this.property.substr(1);
                }
            }
        }
        return returnValue;
    },

    setValue: function(value) {
        if(this.receiver != null && this.property != null && value != this.getValue()) {
            if(window[this.receiver]) {
                var accessor = this.getPropertyAccessor('set');
                if(accessor != null) {
                    window[this.receiver][accessor](value);
                }
                else if(window[this.receiver][this.property]) {
                    window[this.receiver][this.property] = value;
                }
            }
        }
    },    
    
    getValue: function() {
        var returnValue = null;
        if(this.receiver != null && this.property != null) {
            if(window[this.receiver]) {
                var accessor = this.getPropertyAccessor('get');
                if(accessor != null) {
                    returnValue = window[this.receiver][accessor]();
                }
                else if(window[this.receiver][this.property]) {
                    returnValue = window[this.receiver][this.property];
                }
            }
        }
        return returnValue;
    },
    
    allowSyncToServer: function() {
        var returnValue = false;
        if( this.receiver != null && this.property != null && window[this.receiver]) {
            returnValue = true;
        }
        return returnValue;
    }    

});

var __Object = Class.create(__ValueHolder, {

    setValue: function(value) {
        if(this.receiver != null && value != this.getValue()) {
            if(window[this.receiver]) {
                window[this.receiver] = value;
            }
        }
    },    
    
    getValue: function() {
        var returnValue = null;
        if(this.receiver != null) {
            returnValue = window[this.receiver];
        }
        return returnValue;
    },
    
    allowSyncToServer: function() {
        var returnValue = false;
        if( this.receiver != null && window[this.receiver]) {
            returnValue = true;
        }
        return returnValue;
    }    

});


//**************************************************
// Utility classes: ////////////////////////////////
//**************************************************

var DomLoaded =
{
    onload: [],
    loadComplete: false,
    listeningEvent: false,

    isLoaded: function() {
        return DomLoaded.loadComplete;
    },

    loaded: function()
    {
        DomLoaded.loadComplete = true;
        if (arguments.callee.done) return;
        arguments.callee.done = true;
        for (var i = 0; i < DomLoaded.onload.length;i++) {
            DomLoaded.onload[i]();
        }
        DomLoaded.onload = [];
    },
    load: function(fireThis)
    {
        //if the DomLoaded event is already raised, execute the function directly:
        if(DomLoaded.isLoaded()) {
            return fireThis();
        }
        //otherwise, append it to the onload stack
        DomLoaded.onload.push(fireThis);
        if (DomLoaded.listeningEvent == true) return;
        if (document.addEventListener)
        document.addEventListener("DOMContentLoaded", DomLoaded.loaded, null);
        if (/KHTML|WebKit/i.test(navigator.userAgent))
        {
            var _timer = setInterval(function()
            {
                if (/loaded|complete/.test(document.readyState))
                {
                    clearInterval(_timer);
                    delete _timer;
                    DomLoaded.loaded();
                }
            }, 10);
        }
        /*@cc_on @*/
        /*@if (@_win32)
        var proto = "src='javascript:void(0)'";
        if (location.protocol == "https:") proto = "src=//0";
        document.write("<scr"+"ipt id=__ie_onload defer " + proto + "><\/scr"+"ipt>");
        var script = document.getElementById("__ie_onload");
        script.onreadystatechange = function() {
        if (this.readyState == "complete") {
        DomLoaded.loaded();
        }
        };
        /*@end @*/
        window.onload = DomLoaded.loaded;
        DomLoaded.listeningEvent = true;
    }
};

/**
* This class show or hidden an spinner icon attached to an HTML element.
* It's based on the Protoload library by Andreas Kalsch
*
*/
var __Spinner = Class.create({

    // the script to wait this amount of msecs until it shows the loading element
    timeUntilShow: 250,

    // opacity of loading element
    opacity: 0.8,

    initialize: function(elementName) {
        this.elementName = elementName;
        this.className = null;
        this.code = 'spinner_' + elementName;
        this.waitingMessage = null;
    },
    
    setWaitingMessage: function(waitingMessage) {
        this.waitingMessage = waitingMessage;
    },
    
    getWaitingMessage: function() {
        return this.waitingMessage;
    },
    
    getCode: function() {
        return this.code;
    },

    startProgressBar: function() {
        var element = $(this.elementName);
        if(!element) {
            return;
        }
    
        if (!this._loadingProgressBar) {
            var divProgressBar = document.createElement('div');
            (element.offsetParent || document.body).appendChild(this._loadingProgressBar = divProgressBar);
            var divProgressBarInsideArea = document.createElement('div');
            (element.offsetParent || document.body).appendChild(this._loadingProgressBarInsideArea = divProgressBarInsideArea);
            var divCompletedArea = document.createElement('div');
            (element.offsetParent || document.body).appendChild(this._loadingProgressBarCompletedArea = divCompletedArea);
            
            var dimensions = element.getDimensions();
            var position   = element.positionedOffset();
            var divProgressBar = this._loadingProgressBar;
            
            var progressBarTop = position.top + dimensions.height + 2;            
            var progressBarHeight = 3;
            
            divProgressBar.style.position = 'absolute';
            divProgressBar.style.left    = position.left     + 'px';
            divProgressBar.style.top     = progressBarTop + 'px';
            divProgressBar.style.width   = dimensions.width  + 'px';
            divProgressBar.style.height  = progressBarHeight + 'px';
            divProgressBar.style.display = 'inline';
            divProgressBar.style.backgroundColor = 'black';
            
            divProgressBarInsideArea.style.position = 'absolute';
            divProgressBarInsideArea.style.left    = (position.left + 1)    + 'px';
            divProgressBarInsideArea.style.top     = (progressBarTop + 1) + 'px';
            divProgressBarInsideArea.style.width   = (dimensions.width - 2) + 'px';
            divProgressBarInsideArea.style.height  = (progressBarHeight - 2) + 'px';
            divProgressBarInsideArea.style.display = 'inline';
            divProgressBarInsideArea.style.backgroundColor = 'white';
            
            divCompletedArea.style.position = 'absolute';
            divCompletedArea.style.left    = (position.left + 1)    + 'px';
            divCompletedArea.style.top     = (progressBarTop + 1) + 'px';
            divCompletedArea.style.width   = 1 + 'px';
            divCompletedArea.style.height  = (progressBarHeight - 2) + 'px';
            divCompletedArea.hide();
            divCompletedArea.style.backgroundColor = 'blue';                        
        }
    
    },
    
    setProgress: function(progress) {
        var element = $(this.elementName);
        if(!element) {
            return;
        }
        if(!this._loadingProgressBar) {
            this.startProgressBar();
        }
        if(progress <= 0) { progress = 0; }
        if(progress >= 100) { progress = 100; }        
        var divCompletedArea = this._loadingProgressBarCompletedArea;
        divCompletedArea.style.display = 'inline';
        var dimensions = element.getDimensions();
        var totalWidth = dimensions.width - 2;
        var width      = progress * totalWidth / 100;
        divCompletedArea.style.width = Math.round(width);
    },

    // Start waiting status - show loading element
    startWaiting: function(timeUntilShow) {
        var element = $(this.elementName);
        if(!element) {
            return;
        }
        if (timeUntilShow == undefined) {
            timeUntilShow = this.timeUntilShow;
        }
        this._spinner = true;
        if (!this._loading) {
            if(this.waitingMessage != null) {
                var divWaitingMessage = document.createElement('div');
                divWaitingMessage.innerHTML = this.waitingMessage;
                (element.offsetParent || document.body).appendChild(this._waitingMessage = divWaitingMessage);
            }
            var divSpinner = document.createElement('div');
            (element.offsetParent || document.body).appendChild(this._loading = divSpinner);
            divSpinner.style.position = 'absolute';
            try {divSpinner.style.opacity = this.opacity;} catch(e) {}
            try {divSpinner.style.MozOpacity = this.opacity;} catch(e) {}
            try {divSpinner.style.filter = 'alpha(opacity='+Math.round(this.opacity * 100)+')';} catch(e) {}
            try {divSpinner.style.KhtmlOpacity = this.opacity;} catch(e) {}
        }
        if (this.className == undefined) {
            this._loading.style.backgroundImage  = 'url("resources/images/misc/spinner.gif")';
            this._loading.style.backgroundRepeat = 'no-repeat';
            this._loading.style.backgroundPosition = "center center";
            this._loading.style.backgroundColor = "white";
        }
        else {
            this._loading.className = this.className;
        }
        window.setTimeout((function(element) {
            var dimensions = element.getDimensions();
            var position   = element.positionedOffset();
            if(this._loading) {
                var divSpinner = this._loading;
                divSpinner.style.width   = dimensions.width  + 'px';
                divSpinner.style.height  = dimensions.height + 'px';
                divSpinner.style.left    = position.left     + 'px';
                divSpinner.style.top     = position.top      + 'px';
                divSpinner.style.display = 'inline';
            }
            this._relocateSpinner(element);
        }).bind(this, element), timeUntilShow);
    },
    
    _relocateSpinner: function(element) {
        var dimensions = element.getDimensions();
        var position   = element.positionedOffset();
        if (this._spinner) {
            var divSpinner = this._loading;
            if(divSpinner.style.left != position.left + 'px' ||
               divSpinner.style.top  != position.top  + 'px') {
                divSpinner.style.left    = position.left     + 'px';
                divSpinner.style.top     = position.top      + 'px';
                if (this._loadingProgressBar) {
                    var divProgressBar = this._loadingProgressBar;
                    var divProgressBarInsideArea = this._loadingProgressBarInsideArea;
                    var divCompletedArea = this._loadingProgressBarCompletedArea;
        
                    var progressBarTop = position.top + dimensions.height + 2;            
                    
                    divProgressBar.style.left    = position.left     + 'px';
                    divProgressBar.style.top     = progressBarTop + 'px';
                    
                    divProgressBarInsideArea.style.left    = (position.left + 1)    + 'px';
                    divProgressBarInsideArea.style.top     = (progressBarTop + 1) + 'px';
                    
                    divCompletedArea.style.left    = (position.left + 1)    + 'px';
                    divCompletedArea.style.top     = (progressBarTop + 1) + 'px';
                }
            }
            window.setTimeout( (this._relocateSpinner).bind(this, element), 100 );
        }           
        
    },

    // Stop waiting status - hide loading element
    stopWaiting: function() {
        var element = $(this.elementName);
        if(!element) {
            return;
        }
        //remove spinner:
        if (this._spinner) {
            this._spinner = false;
            this._loading.parentNode.removeChild(this._loading);
            this._loading = null;
        }
        //remove waiting message:
        if(this._waitingMessage) {
            this._waitingMessage.parentNode.removeChild(this._waitingMessage);
            this._waitingMessage = null;
        }        
        //remove progress bar:
        if (this._loadingProgressBar) {
            this._loadingProgressBar.parentNode.removeChild(this._loadingProgressBar);
            this._loadingProgressBarInsideArea.parentNode.removeChild(this._loadingProgressBarInsideArea);
            this._loadingProgressBarCompletedArea.parentNode.removeChild(this._loadingProgressBarCompletedArea);
            this._loadingProgressBar              = null;
            this._loadingProgressBarInsideArea    = null;
            this._loadingProgressBarCompletedArea = null;
        }
    }

});

/**
* This function is based on the Ajax.pull javascript library by "four"
*
*/
var __JSONHelper =
{
    extractJSON: function(JSONString) {
        var insideString = false;
        var sBrackets = cBrackets = parens = 0;
        var statements = new Array();
        var start = 0;
        for(var i = 0; i < JSONString.length; i++) {
            if( cBrackets < 0 || sBrackets < 0 || parens < 0 ) {
                return null;
            }
            if(insideString == true) {
                switch(JSONString.slice(i, i+1)) {
                    case '\\': i++; break;
                    case '"': insideString = false; break;
                }
            } else {
                switch(JSONString.slice(i, i+1)) {
                    case '"': insideString = true; break;
                    case '{': cBrackets++; break;
                    case '}': cBrackets--; break;
                    case '[': sBrackets++; break;
                    case ']': sBrackets--; break;
                    case '(': parens++; break;
                    case ')': parens++; break;
                }
            }
            if(cBrackets == 0 && sBrackets == 0 && parens == 0) {
                var statement = JSONString.slice(start, i + 1);
                if(statement.isJSON()) {
                    var JSONStatement = statement.evalJSON(true);
                    statements.push(JSONStatement);
                }
                start = i+1;
            }
        }
        return statements;
    }
  
};

var __FileUploader = Class.create({

    initialize: function(input) {
        this.input = input;
        this.inputDiv = new Element('div', { 'id': 'input_div_'  + (this.input).id, 'style': 'display: inline;'});
        (this.input).parentNode.appendChild(this.inputDiv);
        (this.inputDiv).appendChild(this.input);
        this._addEventHandlers(this.input);
        
        this.cancelled = false;
        this.uploadMode = 1;
        this.executeBeforeUploadEvent = false;
        this.cancelLink = new Element('a',
                                   {  'href': 'javascript: void(0);',
                                      'id'  : 'cancel_' + input.id 
                                   }).update("Cancel");
        this.cancelLink.observe('click', function(event){
            this.cancel();
        }.bind(this));
        this.fileCheckbox = new Element('input',
                                    { 'type': 'checkbox',
                                      'id'  : 'checkbox_' + input.id,
                                      'checked' : true });
                                      
        this.fileCheckbox.observe('click', function(event){
            this.reset();
        }.bind(this));
                                      
        this.filenameSpan = new Element('span', { 'id' : 'checkboxcaption_' + this.input.id });
        this.cancellingSpan = new Element('span', { 'id' : 'checkboxcaption_' + this.input.id }).update('Cancelling...');
        //---------------------------------------------
        //render the background upload form: //////////
        //---------------------------------------------
        var iframe = new Element('iframe', 
                                { 'src': '', 
                                  'id': 'iframe_'  + (this.input).id,
                                  'name': 'iframe_'  + (this.input).id,
                                  'style': 'display: none; width: 0px; height: 0; border: 0px;' });   
        var div = new Element('div', { 'id': 'div_'  + (this.input).id, 'style': 'display: none;' });    
        this.asyncForm = new Element('form', 
                              { 'id': 'form_'  + (this.input).id, 
                                'name': 'form_'  + (this.input).id,
                                'enctype': 'multipart/form-data',
                                'target': 'iframe_'  + (this.input).id,
                                'method': 'POST',
                                'action': 'upload.async' });
        div.appendChild(this.asyncForm);                            
        this.apc_upload_progress = new Element('input', 
                              { 'type': 'hidden', 
                                'name': 'APC_UPLOAD_PROGRESS',
                                'value': this.input.id });    
        this.asyncForm.appendChild(this.apc_upload_progress);

        document.body.appendChild(div);
        document.body.appendChild(iframe);
        
    },

    setExecuteBeforeUploadEvent: function(executeBeforeUploadEvent) {
        this.executeBeforeUploadEvent = executeBeforeUploadEvent;
    },
    
    reset: function() {
        if(!this.fileCheckbox.checked) {
            (__ClientEventHandler.getInstance()).sendEvent('cancelUpload', {}, (this.input).id, true);
            if($(this.fileCheckbox.id)) {
               $(this.fileCheckbox.id).remove();
            }
            if($(this.filenameSpan.id)) {
               $(this.filenameSpan.id).remove();
            }
            (this.inputDiv).style.display = 'inline';
        }
        if($('async_' + this.input.id)) {
            this.asyncForm.reset();
            var id = this.input.id;
            this.apc_upload_progress.value = id;
            var inputTmp = $('async_' + this.input.id);
            this.input.parentNode.insertBefore(inputTmp, this.input);
            this.input.remove();
            this.input = inputTmp;
            this.input.id = id;
        }       
        
    },

    cancel: function() {
        this.cancelled = true;
        if($('iframe_' + this.input.id)) {
            var iframe = $('iframe_' + this.input.id);
            iframe.remove();
            document.body.appendChild(iframe);
        }
        (this.cancelLink).remove();
        ((this.inputDiv).parentNode).appendChild(this.cancellingSpan);
        this._handleCancel();      
    },
    
    _handleCancel: function() {
        var progressHandler = (__ProgressBroadcaster.getInstance()).getProgressHandler(this.input.id);
        
        if(progressHandler.isWaiting()) {
            window.setTimeout((this._handleCancel).bind(this), 500);
        }
        else {
            (this.cancellingSpan).remove();
            this.reset();
            (__ClientEventHandler.getInstance()).sendEvent('cancelUpload', {}, (this.input).id, true);
        }
    },

    upload: function() {
        this.cancelled = false;
        filename = (this.input).value;
        if(filename != null && filename != "") {
            var fullName = filename;
            var shortName = fullName.match(/[^\/\\]+$/);

            var inputClone = (this.input).cloneNode(true);
            if($('async_' + this.input.id)) {
                $('async_' + this.input.id).remove();
            }
            this.input.id = 'async_' + this.input.id;
            this.input.parentNode.insertBefore(inputClone, this.input);
            this.asyncForm.appendChild(this.input);
            this.input = inputClone;
            
            var encType = this.asyncForm.getAttributeNode("enctype");
            encType.value = "multipart/form-data";
            var formMethod = this.asyncForm.getAttributeNode("method");
            formMethod.value = "POST";    
              
            this.asyncForm.submit();

            (this.input).value = null;
            (this.inputDiv).appendChild(this.cancelLink);
            
            (__ClientEventHandler.getInstance()).sendEvent('uploading', {}, this.input.id);
            
            this._handleUploadingProcess(shortName);            
        }
    },
    
    renderAsUploaded: function(filename) {
        (this.fileCheckbox).checked = true;
        (this.fileCheckbox).defaultChecked = true;
        (this.inputDiv).parentNode.appendChild(this.fileCheckbox);
        this.filenameSpan.update(filename);
        (this.fileCheckbox).parentNode.appendChild(this.filenameSpan);  
        (this.inputDiv).style.display = 'none';
    },
    
    _handleUploadingProcess: function(filename) {
        var progressHandler = (__ProgressBroadcaster.getInstance()).getProgressHandler(this.input.id);
        
        if(progressHandler.isWaiting()) {
            window.setTimeout((this._handleUploadingProcess).bind(this, filename), 500);
        }
        else if(this.cancelled == false) {
            if($(this.cancelLink.id)) {
                $(this.cancelLink.id).remove();
            }        
            this.renderAsUploaded(filename);
            (__ClientEventHandler.getInstance()).sendEvent('uploaded', {}, (this.input).id, true);
        }
    },
    
    _addEventHandlers: function(inputelement) {
        inputelement.observe('change', function(event){
            var event = this.fire('lion:validate');
            if (typeof(event.validationResult) == 'undefined' 
            || (typeof(event.validationResult) != 'undefined' && event.validationResult)) { this.fileUploader.upload(); }
        }.bind(inputelement));
        inputelement.fileUploader = this;
    }
    
});

var __ResponseCallbackHandler = Class.create({

    initialize: function(callback) {
        this.callback = callback;
    },
    
    execute: function(parameter) {
        if(this.callback) {
            callback_function = eval(this.callback);
            if(typeof(callback_function) == 'function') {
                callback_function(parameter);
            }
        }
    }
        
});