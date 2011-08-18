

var __InputMask = Class.create({

    initialize: function() {
        this.masks = new Hash();
        this.unmaskedElements = {
            '#': /\d/,
            'L': /[A-Za-z]/,
            'A': /[A-Za-z0-9]/,
            '&': /./
        };
        
        this.modifiers = {
            '>': function(ch) { return ch.toUpperCase(); },
            '<': function(ch) { return ch.toLowerCase(); } 
        }
                
    },

   addMask: function(elementId, mask) {
       if($(elementId)) {
           var element = $(elementId);    
           this.masks.set(elementId, mask);
           Event.observe(element, 'keypress',
                         this.execute.bindAsEventListener(this, element), true);
           Event.observe(element, 'keydown',
                         this.interceptNonPrintableKeys.bindAsEventListener(this, element), true);
                         
       }
   },
   
   execute: function(event, element) {
      var elementId = element.id;
      var mask = this.masks.get(elementId);
      if(typeof(mask) != 'undefined') {
         var key  = this.getKey(event);
         //the current value with the pressed key:
         var str     = element.value;
         //the position of the current value with the pressed key:
         var pos     = str.length + 1;

         if ( this.isPrintable(key) ) {
            //the pressed key:
            var ch      = String.fromCharCode(key);
            var doMask = true;
            while(doMask && pos <= mask.length) {
                doMask = false; //by default
                var maskCharacter = mask.charAt(pos - 1);
                var validRegExp   = this.unmaskedElements[maskCharacter];
                //if the element is a mask character, let's add it 
                //to the value and continue iterating:
                if(typeof(validRegExp) == 'undefined') {
                   str = str + maskCharacter;
                   doMask = true;
                }
                //else, if the inserted character correspond to the expected character,
                //add it and stop iterating
                else if(validRegExp.test(ch)) {
                   str = str + ch;
                }
                pos = pos + 1;
            }
            element.value = str;
            Event.stop(event);
         }
      }
   },
   
   interceptNonPrintableKeys: function(event, element) {
       var key  = this.getKey(event);
       if(key == 8) {
	       this.moveCursorToEnd(element);
	   }
	   else if(!this.isPrintable(key)){
	       Event.stop(event);
	   }
   },

   moveCursorToEnd: function(element) {
      if(element.createTextRange) {
          var r = element.createTextRange();
          r.move("character",element.value.length);
      }
      else if (element.setSelectionRange) {
          element.setSelectionRange(element.value.length, element.value.length);
      }
   },

   isPrintable: function(key) {
      return ( key >= 32 && key < 127 );
   },

   getKey: function(e) {
      return window.event ? window.event.keyCode
           : e            ? e.which
           :                0;
   }
   
});

/**
 * Static method to retrieve a __InputMask singleton instance
 *
 */
__InputMask.getInstance = function() {
    if (!__InputMask.hasOwnProperty('instance')) {
        __InputMask.instance = new __InputMask();
    }
    return __InputMask.instance;
};
