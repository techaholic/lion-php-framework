if(!Lock){var Lock=function(){var b={};var a=function(c){return("c"+c).replace(/[^a-z0-9\-\_]/gi,"")};return{declare:function(){for(var c=0;c<arguments.length;c++){if(!b[a(arguments[c])]){b[a(arguments[c])]=new Array()}}},obtain:function(e){var d=new Object();e=a(e);if(!b[e]){throw"Namespaces must be declared before getting into locks."}b[e].push(d);if(b[e][0]===d){b[e]=[b[e][0]]}var c={isOwner:function(){return(b[e][0]===d)},release:function(){if(b[e][0]===d){b[e]=new Array()}}};return c}}}()}if(!JIT){var JIT=function(){var pending={};var script_ids={};var script_id_counter=0;var lock_owner=null;var script_id_prefix="jit-gen";var loaded_scripts={};var IEVersion=
/*@cc_on function(){ switch(@_jscript_version){ case 1.0:return 3; case 3.0:return 4; case 5.0:return 5; case 5.1:return 5; case 5.5:return 5.5; case 5.6:return 6; case 5.7:return 7; }}()||@*/
0;var document_head=null;var LOCK_WRITING_TO_DOM="JIT_dom_write";var LOCK_DOM_CLEANUP="JIT_dom_clean";var LOCK_GET_SEQUENCE_ID="JIT_sequence_id";Lock.declare(LOCK_GET_SEQUENCE_ID,LOCK_WRITING_TO_DOM,LOCK_DOM_CLEANUP);var generateId=function(){script_id_counter++;return script_id_prefix+script_id_counter};var detectLoadedScripts=function(){var script_nodes=document.getElementsByTagName("script");var css_nodes=document.getElementsByTagName("link");for(var i=0;i<script_nodes.length;i++){var node=script_nodes[i];if(!node.src||node.src.length==0){continue}loaded_scripts[normalizeScriptPath(node.src)]=true}for(var j=0;j<css_nodes.length;j++){var node=css_nodes[j];if(!node.href||node.href.length==0||!node.rel||node.rel.toString().toLowerCase()!="stylesheet"||!node.type||node.type.toString().toLowerCase()!="text/css"){continue}loaded_scripts[normalizeScriptPath(node.href)]=true}};var normalizeScriptPath=function(path){return"s"+escape(path)};var handleLoad=function(urls,verifier,callback,obj,scope,type,once){if(!document.body){window.setTimeout(function(){handleLoad(urls,verifier,callback,obj,scope,type,once)},50);return}var lock=Lock.obtain(LOCK_GET_SEQUENCE_ID);if(!lock.isOwner()){window.setTimeout(function(){handleLoad(urls,verifier,callback,obj,scope,type,once)},10);return}if(!document_head){document_head=document.getElementsByTagName("head")[0]}var sequence_id=generateId();pending[sequence_id]={};script_ids[sequence_id]=[];detectLoadedScripts();lock.release();urls=(urls.constructor===Array)?urls:[urls];if(!verifier||typeof(verifier)!="function"){verifier=function(){return true}}pending[sequence_id]={urls:urls,verifier:verifier,callback:callback,obj:obj,scope:scope,type:type,once:once,lock:lock};if(once){var urls_to_load=[];for(var i=0;i<urls.length;i+=1){var loaded=(loaded_scripts[normalizeScriptPath(urls[i])])?true:false;if(!loaded){urls_to_load.push(urls[i])}}if(urls_to_load.length<=0){loadComplete(sequence_id);return}urls=urls_to_load;pending[sequence_id]={urls:urls,verifier:verifier,callback:callback,obj:obj,scope:scope,type:type,once:once,lock:lock}}if(type=="js"){insertScripts(urls,sequence_id)}else{if(type=="css"||(type.match(/^css/i)&&type=="css"+IEVersion)){insertStyles(urls,sequence_id)}else{lock.release();JIT.scriptsComplete(sequence_id)}}};var insertStyles=function(urls,sequence_id){urls=urls.constructor===Array?urls:[urls];var node;for(var i=0;i<urls.length;i+=1){var sc_id=generateId();node=document.createElement("link");node.id=sc_id;node.href=urls[i];node.rel="stylesheet";node.type="text/css";node.media="screen";writeNode(node)}if(IEVersion){node.onreadystatechange=function(){if(this.readyState=="loaded"||this.readyState=="complete"){JIT.scriptsComplete(sequence_id)}}}else{var sc_id=generateId();script_ids[sequence_id].push(sc_id);var smart_script=document.createElement("script");smart_script.id=sc_id;smart_script.type="text/javascript";smart_script.appendChild(document.createTextNode("JIT.scriptsComplete('"+sequence_id+"');"));writeNode(smart_script)}if(pending[sequence_id]){pending[sequence_id].lock.release()}};var insertScripts=function(urls,sequence_id){urls=urls.constructor===Array?urls:[urls];var script;for(var i=0;i<urls.length;i+=1){var sc_id=generateId();script_ids[sequence_id].push(sc_id);script=document.createElement("script");script.id=sc_id;script.src=urls[i];script.type="text/javascript";writeNode(script)}if(!script){if(pending[sequence_id]){pending[sequence_id].lock.release()}return}if(IEVersion){script.onreadystatechange=function(){if(this.readyState=="loaded"||this.readyState=="complete"){JIT.scriptsComplete(sequence_id)}}}else{var sc_id=generateId();script_ids[sequence_id].push(sc_id);var smart_script=document.createElement("script");smart_script.id=sc_id;smart_script.type="text/javascript";smart_script.appendChild(document.createTextNode("JIT.scriptsComplete('"+sequence_id+"');"));writeNode(smart_script)}if(pending[sequence_id]){pending[sequence_id].lock.release()}};var writeNode=function(node){var timer=null;var retry_in=100;var processWrite=function(){var lock=Lock.obtain(LOCK_WRITING_TO_DOM);if(!lock.isOwner()){timer=window.setTimeout(processWrite,retry_in);return}window.clearTimeout(timer);timer=null;document_head.appendChild(node);lock.release()};timer=window.setTimeout(processWrite,retry_in)};var loadComplete=function(sequence_id,lock){if(!pending[sequence_id]){return}if(!lock){var lock=Lock.obtain(LOCK_DOM_CLEANUP)}if(!lock.isOwner()){window.setTimeout(function(){loadComplete(sequence_id)},10);return}if(pending[sequence_id].verifier&&!pending[sequence_id].verifier.call(window)){window.setTimeout(function(){loadComplete(sequence_id,lock)},100);return}if(pending[sequence_id]){pending[sequence_id].lock.release()}detectLoadedScripts();if(script_ids[sequence_id]){while(script_ids[sequence_id].length>0){var sc_id=script_ids[sequence_id].shift();var script=document.getElementById(sc_id);if(typeof script!="undefined"){script.parentNode.removeChild(script)}}script_ids[sequence_id]=null}if(pending[sequence_id]&&pending[sequence_id].callback){if(pending[sequence_id].obj){if(pending[sequence_id].scope){pending[sequence_id].callback.call(pending[sequence_id].obj)}else{pending[sequence_id].callback.call(window,pending[sequence_id].obj)}}else{pending[sequence_id].callback.call()}}pending[sequence_id]=null;lock.release()};var JIT_Chain=function(){var stack=[];var run_callback=null;var run_object=null;var run_scope=null;return{load:function(urls,verifier){stack.push({type:"js",once:false,urls:urls,verifier:verifier});return this},loadOnce:function(urls,verifier){stack.push({type:"js",once:true,urls:urls,verifier:verifier});return this},addCSS:function(urls,verifier,ie_version){stack.push({type:"css",once:true,urls:urls,verifier:verifier,ie:ie_version});return this},onComplete:function(callback,obj,scope){var that=this;if(!run_callback){if(!callback){callback=function(){}}run_callback=callback;run_object=obj;run_scpe=scope}if(stack.length==0){if(obj){if(scope){run_callback.call(obj)}else{run_callback.call(window,obj)}}else{run_callback.call()}return}var next_call=stack.shift();if(next_call.type=="js"){if(next_call.once){JIT.loadOnce(next_call.urls,next_call.verifier,that.onComplete,that,true)}else{JIT.load(next_call.urls,next_call.verifier,that.onComplete,that,true)}}else{if(next_call.type=="css"){if(next_call.ie){JIT.addCSS(next_call.urls,next_call.verifier,that.onComplete,that,true,next_call.ie)}else{JIT.addCSS(next_call.urls,next_call.verifier,that.onComplete,that,true)}}}}}};return{load:function(urls,verifier,callback,obj,scope){handleLoad(urls,verifier,callback,obj,scope,"js",false)},addCSS:function(urls,verifier,callback,obj,scope,ie_restrict){if(!ie_restrict){ie_restrict=""}handleLoad(urls,verifier,callback,obj,scope,"css"+ie_restrict,true)},loadOnce:function(urls,verifier,callback,obj,scope){handleLoad(urls,verifier,callback,obj,scope,"js",true)},startChain:function(){return JIT_Chain()},scriptsComplete:function(sequence_id){loadComplete(sequence_id)}}}()}if(!__Lion){var __Lion=function(){var f=false;var g=null;var a=document.getElementsByTagName("script");for(var c in a){if(a[c].src&&(a[c].src).match(/lion\.js(\?.*)?$/)){g=(a[c].src).replace(/lion\.js(\?.*)?$/,"");break}}var d=function(h){return g+h};var e={prototype:d("prototype/prototype.js"),componentModel:d("componentmodel/componentmodel.js")};var b=function(h){document.write('<script type="text/javascript" src="'+h+'"><\/script>')};return{load:function(){for(var h in e){b(e[h])}}}}();__Lion.load()};