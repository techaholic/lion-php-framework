//**************************************************
// Wheel event: ////////////////////////////////////
//**************************************************

Object.extend(Event, {
    wheel:function (event){
        var delta = 0;
        if (!event) event = window.event;
        if (event.wheelDelta) {
            delta = event.wheelDelta/120; 
            if (window.opera) delta = -delta;
        } else if (event.detail) { delta = -event.detail/3; }
        return Math.round(delta); //Safari Round
    }
});


//**************************************************
// Events: /////////////////////////////////////////
//**************************************************

__MouseEventFrame = Class.create({

    initialize: function(mouseX, mouseY, time) {
        this.mouseX = mouseX;
        this.mouseY = mouseY;
        this.time   = time;
        //resolve the screen scroll:
        var scrollOffsets = document.viewport.getScrollOffsets();
        this.scrollX = scrollOffsets.left;
        this.scrollY = scrollOffsets.top;
    },
    
    updateScroll: function() {
        var scrollOffsets = document.viewport.getScrollOffsets();
        if(this.scrollX != scrollOffsets.left || this.scrollY != scrollOffsets.top) {
            window.scrollBy(this.scrollX, this.scrollY);
        }
    }
    
});


__MouseMoveEventFrame = Class.create(__MouseEventFrame, {

    play: function () {
        this.updateScroll();
        new Effect.Move($('mousepointer'), {duration: 0.3, x:this.mouseX, y:this.mouseY, mode: 'absolute'} );
    }

});

__MouseWheelEventFrame = Class.create(__MouseEventFrame, {

    play: function () {
        this.updateScroll();
        new Effect.Move($('mousepointer'), {duration: 0.3, x:this.mouseX, y:this.mouseY, mode: 'absolute'} );
    }

});

__MouseClickEventFrame = Class.create(__MouseEventFrame, {

    play: function () {
        this.updateScroll();
        new Effect.Move($('mousepointer'), {duration: 0.3, x:this.mouseX, y:this.mouseY, mode: 'absolute'} );
        setTimeout(this.showClick.bind(this), 300);
    },
    
    showClick: function() {
        var clickSpot = new Element('img',
                               {  'src': '/forms/recorder/click_spot.png',
                                  'style' : 'width: 9px; height: 9px; position: absolute; left: ' + (this.mouseX - 4) + 'px; top: ' + (this.mouseY - 3) + 'px; z-index: 99999;'
                               });
                 
        document.getElementsByTagName("body")[0].appendChild(clickSpot);
        clickSpot.style.display = '';
        setTimeout(this.hiddenClick.bind(this, clickSpot), 1000);
    },
    
    hiddenClick: function(image) {
        image.style.display = 'none';
        image.remove();
    }

});

__KeypressEventFrame = Class.create({

    initialize: function(keycode, element, value, time) {
        this.keycode = keycode;
        this.element = element;
        this.value  = value;
        this.time   = time;
    },
    
    play: function () {
        if (document.createEventObject){
		    var evt = document.createEventObject( );
            evt.keycode = this.keycode;
            this.element.fireEvent("onkeypress", evt);    
		}
		else {
            // dispatch for firefox + others
            var evt = document.createEvent("HTMLEvents");
            evt.initEvent('keypress', true, true ); // event type,bubbling,cancelable
            evt.keycode = this.keycode;
            this.element.dispatchEvent(evt);
		}
        this.element.value = this.value;
    }    
    
});

//**************************************************
// Observers: //////////////////////////////////////
//**************************************************

__EventObserver = Class.create({
    
    startObserving: function() {
        document.observe(this.eventType, this.saveFrame.bind(this));
    },    

    stopObserving: function() {
        document.stopObserving(this.eventType);
    },
  
    saveFrame: function(event) {
        var recordingSession = __RecordingSession.getInstance();
        if(recordingSession.lockRecord) {
            clearTimeout(recordingSession.lockRecord);
        }
        var eventFrame = this.getEventFrame(event);
        recordingSession.lockRecord = setTimeout(recordingSession.saveFrame.bind(recordingSession, eventFrame), 20);
    }
  
});

__MouseMoveObserver = Class.create(__EventObserver, {

    initialize: function() {
        this.eventType = 'mousemove';
    },

    getEventFrame: function(event) {
        var mouseX = Event.pointerX(event);
        var mouseY = Event.pointerY(event);
        var returnValue = new __MouseMoveEventFrame(mouseX, mouseY, new Date().getTime());
        return returnValue;
    }

});

__MouseClickObserver = Class.create(__EventObserver, {

    initialize: function() {
        this.eventType = 'click';
    },

    getEventFrame: function(event) {
        var mouseX = Event.pointerX(event);
        var mouseY = Event.pointerY(event);
        var returnValue = new __MouseClickEventFrame(mouseX, mouseY, new Date().getTime());
        return returnValue;
    }

});

__MouseWheelObserver = Class.create(__EventObserver, {

    initialize: function() {
        this.eventType = 'wheel';
    },

    getEventFrame: function(event) {
        var mouseX = Event.pointerX(event);
        var mouseY = Event.pointerY(event);
        var scrollX = xxx;
        var scrollY = yyy;
        var returnValue = new __MouseWheelEventFrame(mouseX, mouseY, new Date().getTime());
        return returnValue;
    }

});

__KeypressObserver = Class.create(__EventObserver, {

    initialize: function() {
        this.eventType = 'keyup';
    },

    getEventFrame: function(event) {
        var keycode = event.keycode;
        var element = event.element();
        var value = null;
        if(element.value) {
            value = element.value;
        }
        var returnValue = new __KeypressEventFrame(keycode, element, value, new Date().getTime());
        return returnValue;
    }

});


//**************************************************
// Recorder & player: //////////////////////////////
//**************************************************

__EventRecorder = Class.create({

    initialize: function() {
        this.mouseMoveObserver  = new __MouseMoveObserver();
        this.mouseClickObserver = new __MouseClickObserver();
        this.mouseWheelObserver = new __MouseWheelObserver();
        this.keypressObserver   = new __KeypressObserver();
        this.timer = null;
        //recorder status:
        this.STATUS_STOPPED = 0;
        this.STATUS_PLAYING = 1;
        this.STATUS_RECORDING = 2;
    },
    
    reset: function() {
        this.currentFrame = 0;
        this.status = this.STATUS_STOPPED;
        this.timer = null;
    },
    
    play: function() {
        if(this.status != this.STATUS_STOPPED) {
            this.stop();
        }
        this.status = this.STATUS_PLAYING;
        this.showMousePointer();
        var events = (__RecordingSession.getInstance()).getEvents();
        for (; this.currentFrame < events.length && this.status == this.STATUS_PLAYING; this.currentFrame++) {
            var event = events[this.currentFrame];
            this.timer = setTimeout(event.play.bind(event), event.time);
        }
        this.reset();
    },
    
    stop: function() {
        if(this.status == this.STATUS_RECORDING) {
            this.mouseMoveObserver.stopObserving();
            this.mouseClickObserver.stopObserving();
            this.mouseWheelObserver.stopObserving();
            this.keypressObserver.stopObserving();        
            this.currentFrame = 0;
        }
        this.timer  = null;
        this.status = this.STATUS_STOPPED;
    }, 
    
    record: function() {
        if(this.status != this.STATUS_STOPPED) {
            this.stop();
        }
        this.status = this.STATUS_RECORDING;
        this.mouseMoveObserver.startObserving();
        this.mouseClickObserver.startObserving();
        this.mouseWheelObserver.startObserving();        
        this.keypressObserver.startObserving();        
    },

    rewind: function() {
        this.currentFrame = 0;
    },     

    forward: function() {
        this.currentFrame = this.getTotalFrames();
    },    

    getCurrentFrame: function() {
        return this.currentFrame;
    },
    
    getTotalFrames: function() {
        var events = (__RecordingSession.getInstance()).getEvents();
        var returnValue = events.length;
        return returnValue;  
    },
    
    showMousePointer: function() {
        if(!$('mousepointer')) {
            var mousepointer = new Element('img',
                                   {  'src': '/forms/recorder/mousepointer.gif',
                                      'id'  : 'mousepointer',
                                      'style' : 'width: 12px; height: 21px; position: absolute; z-index: 100000;'
                                      
                                   });
            document.getElementsByTagName("body")[0].appendChild(mousepointer);
        }
        $('mousepointer').style.display = '';
    },
    
    hiddenMousePointer: function() {
        if($('mousepointer')) {
            $('mousepointer').style.display = 'none';
        }
    }
    

});
    
//**************************************************
// Recording Session: //////////////////////////////
//**************************************************

__RecordingSession = Class.create({

    initialize: function() {
        this.events = new Array();
        this.starttime = new Date().getTime();        
        this.lockRecord = null;
    },
    
    clear: function() {
        this.events = new Array();
        this.starttime = new Date().getTime();        
        this.lockRecord = null;
    },
    
    saveFrame: function(event) {
        this.lockRecord = null;
        event.time = event.time - this.starttime;
        this.events[this.events.length] = event;
    },    
    
    getEvents: function() {
        return this.events;
    }
    
});

/**
 * Static method to retrieve a __RecordingSession singleton instance
 *
 */
__RecordingSession.getInstance = function() {
    if (!__RecordingSession.hasOwnProperty('instance')) {
        __RecordingSession.instance = new __RecordingSession();
    }
    return __RecordingSession.instance;
};

