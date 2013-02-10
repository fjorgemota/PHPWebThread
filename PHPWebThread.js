LazyLoad=function(k){function p(b,a){var g=k.createElement(b),c;for(c in a)a.hasOwnProperty(c)&&g.setAttribute(c,a[c]);return g}function l(b){var a=m[b],c,f;if(a)c=a.callback,f=a.urls,f.shift(),h=0,f.length||(c&&c.call(a.context,a.obj),m[b]=null,n[b].length&&j(b))}function w(){var b=navigator.userAgent;c={async:k.createElement("script").async===!0};(c.webkit=/AppleWebKit\//.test(b))||(c.ie=/MSIE/.test(b))||(c.opera=/Opera/.test(b))||(c.gecko=/Gecko\//.test(b))||(c.unknown=!0)}function j(b,a,g,f,h){var j=
function(){l(b)},o=b==="css",q=[],d,i,e,r;c||w();if(a)if(a=typeof a==="string"?[a]:a.concat(),o||c.async||c.gecko||c.opera)n[b].push({urls:a,callback:g,obj:f,context:h});else{d=0;for(i=a.length;d<i;++d)n[b].push({urls:[a[d]],callback:d===i-1?g:null,obj:f,context:h})}if(!m[b]&&(r=m[b]=n[b].shift())){s||(s=k.head||k.getElementsByTagName("head")[0]);a=r.urls;d=0;for(i=a.length;d<i;++d)g=a[d],o?e=c.gecko?p("style"):p("link",{href:g,rel:"stylesheet"}):(e=p("script",{src:g}),e.async=!1),e.className="lazyload",
e.setAttribute("charset","utf-8"),c.ie&&!o?e.onreadystatechange=function(){if(/loaded|complete/.test(e.readyState))e.onreadystatechange=null,j()}:o&&(c.gecko||c.webkit)?c.webkit?(r.urls[d]=e.href,t()):(e.innerHTML='@import "'+g+'";',u(e)):e.onload=e.onerror=j,q.push(e);d=0;for(i=q.length;d<i;++d)s.appendChild(q[d])}}function u(b){var a;try{a=!!b.sheet.cssRules}catch(c){h+=1;h<200?setTimeout(function(){u(b)},50):a&&l("css");return}l("css")}function t(){var b=m.css,a;if(b){for(a=v.length;--a>=0;)if(v[a].href===
b.urls[0]){l("css");break}h+=1;b&&(h<200?setTimeout(t,50):l("css"))}}var c,s,m={},h=0,n={css:[],js:[]},v=k.styleSheets;return{css:function(b,a,c,f){j("css",b,a,c,f)},js:function(b,a,c,f){j("js",b,a,c,f)}}}(this.document); // Just...Lazy Load! :D
(function(){
	var allScripts = document.getElementsByTagName("script");
	var thePath = null;
	var regex = /(.*)PHPWebThread\.js$/;
	var server_supported = true;
	for(var c=0, l=allScripts.length; c<l; ++c){
		if(regex.test(allScripts[c].src)){
			thePath = regex.exec(allScripts[c].src)[1];
			server_supported = allScripts[c].getAttribute("data-server-supported") == "1";
			break; // Just breaks! :D
		}
	}
	if(!server_supported){
		thePath = thePath+"PHPWebThreadDispatch.php/"; // It's don't supported, use PATH INFO instead
	}
	var instances = [];
	window.PHPWebThread = function(name, cached, is_server_supported){
		this.id = instances.length;
		this.name = name;
		this.timeoutId = null;
		this.start = function(){
			var request_uri = window.location.href;
			request_uri = request_uri.split("/");
			request_uri = request_uri.slice(3);
			var last_part = request_uri.pop();
			if(!!last_part){
				request_uri.push(last_part);
			}
			request_uri = request_uri.join("/");
			var asyncScript = document.createElement("script");
			asyncScript.async = true;
			asyncScript.src = thePath+"phpwebthread/"+this.id+"/"+this.name+"."+(cached?"js":"php"); // Set the URL
			asyncScript.charset = "UTF-8"; //It uses UTF-8
			(document.getElementsByTagName("head")[0]||document.head||document.body).appendChild(asyncScript); // And load it!
			if(!!PHPWebThread.timeout){
				this.timeoutId = setTimeout(function(){
					if(window.location.href.indexOf("?") > -1){
						window.location.href = window.location.href+"&phpwebthread_deactivate=1";
					}
					else{
						window.location.href = window.location.href+"?phpwebthread_deactivate=1";
					}
				}, PHPWebThread.timeout*1000); //Issue a timeout! :D
			}
		}
		this.put = function(elements, css_files, js_files){
			/* Put the elements in the DOM */
			var self = this;
			var callback = function(){
					var allScripts = document.getElementsByTagName("script");
					var theScript = null;
					for(var c=0, l=allScripts.length; c<l; ++c){
						if(allScripts[c].getAttribute("data-phpwebthread-id") == self.id.toString()){
							theScript = allScripts[c];
							break; // Just breaks! :D
						}
					}
					if(theScript == null){
						throw("Not encountered :/");
					}
					else{
						var parentElement = theScript.parentNode;
						if(self.timeoutId != null){
							clearTimeout(self.timeoutId); // Stop the timeout! :D
						}
						
						var fallbackMsg = document.getElementById("phpwebthread_fallbackmsg");
						if(!!fallbackMsg){
							fallbackMsg.style.display = "none";
						}
						for(var c=0, l=elements.length;c<l; ++c){
							parentElement.insertBefore(elements[c], theScript);
						}
					}
					if(js_files.length > 0){
						LazyLoad.js(js_files); // Load the JS files after the loading of the DOM:D
					}
			};
			if(css_files.length > 0){
				LazyLoad.css(css_files, callback); // Just load the CSS files and call callback
			}
			else{
				callback(); // Just call the callback
			}
		}
		instances.push(this); // Append the instance
	};
	PHPWebThread.timeout = 10;
	PHPWebThread.setTimeout = function(timeout){
		PHPWebThread.timeout = timeout;
	}
	PHPWebThread.getInstance = function(id){
		return instances[id];
	}
	var tasks = window.php_web_threads||[];
	for(var c=0, l=tasks.length; c<l; ++c){
		var task = tasks[c]; //Just save a pointer
		task(); // Call! :D
	}
})();
