var commandHistory;var historyIndex;function showExtendedDebug(){if(debugWindow!=null){hideDebug()}if(debugWindow==null){commandHistory=new Array();historyIndex=0;debugWindow=new Window("debug_window",{className:"dialog",width:250,height:100,right:4,minWidth:250,bottom:42,zIndex:1000,opacity:1,showEffect:Element.show,resizable:true,title:"Debug"});debugWindow.getContent().innerHTML="<style>#debug_window .dialog_content {background:#000;}</style> <div font='monaco' id='debug' style='padding:3px;color:#0F0;font-family:monaco'></div>";var a='<div id="debug_window_inspect" style="width: 15px; height: 15px; background: transparent url(themes/default/inspect.gif) no-repeat 0 0; position:absolute; top:5px; left:70px; cursor:pointer; z-index:3000;"></div>';new Insertion.After("debug_window_maximize",a);Event.observe("debug_window_inspect","click",enterInspectionMode,false);a='Eval:<input id="debug_window_command" type="textbox" style="width:150px; height: 12px; color: black;">';debugWindow.setStatusBar(a);Event.observe("debug_window_command","mousedown",donothing);Event.observe("debug_window_command","keypress",evalJS,false)}debugWindow.show()}function donothing(a){Field.activate("debug_window_command");return false}function evalJS(evt){if(evt.keyCode==Event.KEY_RETURN){var js=$F("debug_window_command");try{var ret=eval(js);if(ret!=null){debug(ret)}}catch(e){debug(e)}$("debug_window_command").value="";Field.activate("debug_window_command");commandHistory.push(js);historyIndex=0}if(evt.keyCode==Event.KEY_UP){if(commandHistory.length>historyIndex){historyIndex++;var js=commandHistory[commandHistory.length-historyIndex];$("debug_window_command").value=js;Event.stop(evt);Field.activate("debug_window_command")}}if(evt.keyCode==Event.KEY_DOWN){if(commandHistory.length>=historyIndex&&historyIndex>1){historyIndex--;var js=commandHistory[commandHistory.length-historyIndex];$("debug_window_command").value=js;Event.stop(evt);Field.activate("debug_window_command")}}}function enterInspectionMode(a){Event.stopObserving("debug_window_inspect","click",enterInspectionMode,false);document.body.style.cursor="help";Event.observe(window,"click",inspectItem,false)}function inspectItem(a){var b=Event.element(a);if(b.id!="debug_window_inspect"){clearDebug();document.body.style.cursor="default";debug(b.id);inspect(b);Event.stopObserving(window,"click",inspectItem,false);Event.observe("debug_window_inspect","click",enterInspectionMode,false)}}function clearDebug(){var b=$("debug");if(b==null){return}b.innerHTML=" ";var a=document.getElementsByClassName("inspector");a.each(function(c){Element.remove(c)})};