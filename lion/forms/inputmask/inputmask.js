var __InputMask=Class.create({initialize:function(){this.masks=new Hash();this.unmaskedElements={"#":/\d/,L:/[A-Za-z]/,A:/[A-Za-z0-9]/,"&":/./};this.modifiers={">":function(a){return a.toUpperCase()},"<":function(a){return a.toLowerCase()}}},addMask:function(b,a){if($(b)){var c=$(b);this.masks.set(b,a);Event.observe(c,"keypress",this.execute.bindAsEventListener(this,c),true);Event.observe(c,"keydown",this.interceptNonPrintableKeys.bindAsEventListener(this,c),true)}},execute:function(c,e){var f=e.id;var k=this.masks.get(f);if(typeof(k)!="undefined"){var j=this.getKey(c);var g=e.value;var i=g.length+1;if(this.isPrintable(j)){var b=String.fromCharCode(j);var d=true;while(d&&i<=k.length){d=false;var h=k.charAt(i-1);var a=this.unmaskedElements[h];if(typeof(a)=="undefined"){g=g+h;d=true}else{if(a.test(b)){g=g+b}}i=i+1}e.value=g;Event.stop(c)}}},interceptNonPrintableKeys:function(c,b){var a=this.getKey(c);if(a==8){this.moveCursorToEnd(b)}else{if(!this.isPrintable(a)){Event.stop(c)}}},moveCursorToEnd:function(a){if(a.createTextRange){var b=a.createTextRange();b.move("character",a.value.length)}else{if(a.setSelectionRange){a.setSelectionRange(a.value.length,a.value.length)}}},isPrintable:function(a){return(a>=32&&a<127)},getKey:function(a){return window.event?window.event.keyCode:a?a.which:0}});__InputMask.getInstance=function(){if(!__InputMask.hasOwnProperty("instance")){__InputMask.instance=new __InputMask()}return __InputMask.instance};