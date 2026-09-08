/*! jQuery v1.9.1 | (c) 2005, 2012 jQuery Foundation, Inc. | jquery.org/license
//@ sourceMappingURL=jquery.min.map
*/(function(e,t){var n,r,i=typeof t,o=e.document,a=e.location,s=e.jQuery,u=e.$,l={},c=[],p="1.9.1",f=c.concat,d=c.push,h=c.slice,g=c.indexOf,m=l.toString,y=l.hasOwnProperty,v=p.trim,b=function(e,t){return new b.fn.init(e,t,r)},x=/[+-]?(?:\d*\.|)\d+(?:[eE][+-]?\d+|)/.source,w=/\S+/g,T=/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g,N=/^(?:(<[\w\W]+>)[^>]*|#([\w-]*))$/,C=/^<(\w+)\s*\/?>(?:<\/\1>|)$/,k=/^[\],:{}\s]*$/,E=/(?:^|:|,)(?:\s*\[)+/g,S=/\\(?:["\\\/bfnrt]|u[\da-fA-F]{4})/g,A=/"[^"\\\r\n]*"|true|false|null|-?(?:\d+\.|)\d+(?:[eE][+-]?\d+|)/g,j=/^-ms-/,D=/-([\da-z])/gi,L=function(e,t){return t.toUpperCase()},H=function(e){(o.addEventListener||"load"===e.type||"complete"===o.readyState)&&(q(),b.ready())},q=function(){o.addEventListener?(o.removeEventListener("DOMContentLoaded",H,!1),e.removeEventListener("load",H,!1)):(o.detachEvent("onreadystatechange",H),e.detachEvent("onload",H))};b.fn=b.prototype={jquery:p,constructor:b,init:function(e,n,r){var i,a;if(!e)return this;if("string"==typeof e){if(i="<"===e.charAt(0)&&">"===e.charAt(e.length-1)&&e.length>=3?[null,e,null]:N.exec(e),!i||!i[1]&&n)return!n||n.jquery?(n||r).find(e):this.constructor(n).find(e);if(i[1]){if(n=n instanceof b?n[0]:n,b.merge(this,b.parseHTML(i[1],n&&n.nodeType?n.ownerDocument||n:o,!0)),C.test(i[1])&&b.isPlainObject(n))for(i in n)b.isFunction(this[i])?this[i](n[i]):this.attr(i,n[i]);return this}if(a=o.getElementById(i[2]),a&&a.parentNode){if(a.id!==i[2])return r.find(e);this.length=1,this[0]=a}return this.context=o,this.selector=e,this}return e.nodeType?(this.context=this[0]=e,this.length=1,this):b.isFunction(e)?r.ready(e):(e.selector!==t&&(this.selector=e.selector,this.context=e.context),b.makeArray(e,this))},selector:"",length:0,size:function(){return this.length},toArray:function(){return h.call(this)},get:function(e){return null==e?this.toArray():0>e?this[this.length+e]:this[e]},pushStack:function(e){var t=b.merge(this.constructor(),e);return t.prevObject=this,t.context=this.context,t},each:function(e,t){return b.each(this,e,t)},ready:function(e){return b.ready.promise().done(e),this},slice:function(){return this.pushStack(h.apply(this,arguments))},first:function(){return this.eq(0)},last:function(){return this.eq(-1)},eq:function(e){var t=this.length,n=+e+(0>e?t:0);return this.pushStack(n>=0&&t>n?[this[n]]:[])},map:function(e){return this.pushStack(b.map(this,function(t,n){return e.call(t,n,t)}))},end:function(){return this.prevObject||this.constructor(null)},push:d,sort:[].sort,splice:[].splice},b.fn.init.prototype=b.fn,b.extend=b.fn.extend=function(){var e,n,r,i,o,a,s=arguments[0]||{},u=1,l=arguments.length,c=!1;for("boolean"==typeof s&&(c=s,s=arguments[1]||{},u=2),"object"==typeof s||b.isFunction(s)||(s={}),l===u&&(s=this,--u);l>u;u++)if(null!=(o=arguments[u]))for(i in o)e=s[i],r=o[i],s!==r&&(c&&r&&(b.isPlainObject(r)||(n=b.isArray(r)))?(n?(n=!1,a=e&&b.isArray(e)?e:[]):a=e&&b.isPlainObject(e)?e:{},s[i]=b.extend(c,a,r)):r!==t&&(s[i]=r));return s},b.extend({noConflict:function(t){return e.$===b&&(e.$=u),t&&e.jQuery===b&&(e.jQuery=s),b},isReady:!1,readyWait:1,holdReady:function(e){e?b.readyWait++:b.ready(!0)},ready:function(e){if(e===!0?!--b.readyWait:!b.isReady){if(!o.body)return setTimeout(b.ready);b.isReady=!0,e!==!0&&--b.readyWait>0||(n.resolveWith(o,[b]),b.fn.trigger&&b(o).trigger("ready").off("ready"))}},isFunction:function(e){return"function"===b.type(e)},isArray:Array.isArray||function(e){return"array"===b.type(e)},isWindow:function(e){return null!=e&&e==e.window},isNumeric:function(e){return!isNaN(parseFloat(e))&&isFinite(e)},type:function(e){return null==e?e+"":"object"==typeof e||"function"==typeof e?l[m.call(e)]||"object":typeof e},isPlainObject:function(e){if(!e||"object"!==b.type(e)||e.nodeType||b.isWindow(e))return!1;try{if(e.constructor&&!y.call(e,"constructor")&&!y.call(e.constructor.prototype,"isPrototypeOf"))return!1}catch(n){return!1}var r;for(r in e);return r===t||y.call(e,r)},isEmptyObject:function(e){var t;for(t in e)return!1;return!0},error:function(e){throw Error(e)},parseHTML:function(e,t,n){if(!e||"string"!=typeof e)return null;"boolean"==typeof t&&(n=t,t=!1),t=t||o;var r=C.exec(e),i=!n&&[];return r?[t.createElement(r[1])]:(r=b.buildFragment([e],t,i),i&&b(i).remove(),b.merge([],r.childNodes))},parseJSON:function(n){return e.JSON&&e.JSON.parse?e.JSON.parse(n):null===n?n:"string"==typeof n&&(n=b.trim(n),n&&k.test(n.replace(S,"@").replace(A,"]").replace(E,"")))?Function("return "+n)():(b.error("Invalid JSON: "+n),t)},parseXML:function(n){var r,i;if(!n||"string"!=typeof n)return null;try{e.DOMParser?(i=new DOMParser,r=i.parseFromString(n,"text/xml")):(r=new ActiveXObject("Microsoft.XMLDOM"),r.async="false",r.loadXML(n))}catch(o){r=t}return r&&r.documentElement&&!r.getElementsByTagName("parsererror").length||b.error("Invalid XML: "+n),r},noop:function(){},globalEval:function(t){t&&b.trim(t)&&(e.execScript||function(t){e.eval.call(e,t)})(t)},camelCase:function(e){return e.replace(j,"ms-").replace(D,L)},nodeName:function(e,t){return e.nodeName&&e.nodeName.toLowerCase()===t.toLowerCase()},each:function(e,t,n){var r,i=0,o=e.length,a=M(e);if(n){if(a){for(;o>i;i++)if(r=t.apply(e[i],n),r===!1)break}else for(i in e)if(r=t.apply(e[i],n),r===!1)break}else if(a){for(;o>i;i++)if(r=t.call(e[i],i,e[i]),r===!1)break}else for(i in e)if(r=t.call(e[i],i,e[i]),r===!1)break;return e},trim:v&&!v.call("\ufeff\u00a0")?function(e){return null==e?"":v.call(e)}:function(e){return null==e?"":(e+"").replace(T,"")},makeArray:function(e,t){var n=t||[];return null!=e&&(M(Object(e))?b.merge(n,"string"==typeof e?[e]:e):d.call(n,e)),n},inArray:function(e,t,n){var r;if(t){if(g)return g.call(t,e,n);for(r=t.length,n=n?0>n?Math.max(0,r+n):n:0;r>n;n++)if(n in t&&t[n]===e)return n}return-1},merge:function(e,n){var r=n.length,i=e.length,o=0;if("number"==typeof r)for(;r>o;o++)e[i++]=n[o];else while(n[o]!==t)e[i++]=n[o++];return e.length=i,e},grep:function(e,t,n){var r,i=[],o=0,a=e.length;for(n=!!n;a>o;o++)r=!!t(e[o],o),n!==r&&i.push(e[o]);return i},map:function(e,t,n){var r,i=0,o=e.length,a=M(e),s=[];if(a)for(;o>i;i++)r=t(e[i],i,n),null!=r&&(s[s.length]=r);else for(i in e)r=t(e[i],i,n),null!=r&&(s[s.length]=r);return f.apply([],s)},guid:1,proxy:function(e,n){var r,i,o;return"string"==typeof n&&(o=e[n],n=e,e=o),b.isFunction(e)?(r=h.call(arguments,2),i=function(){return e.apply(n||this,r.concat(h.call(arguments)))},i.guid=e.guid=e.guid||b.guid++,i):t},access:function(e,n,r,i,o,a,s){var u=0,l=e.length,c=null==r;if("object"===b.type(r)){o=!0;for(u in r)b.access(e,n,u,r[u],!0,a,s)}else if(i!==t&&(o=!0,b.isFunction(i)||(s=!0),c&&(s?(n.call(e,i),n=null):(c=n,n=function(e,t,n){return c.call(b(e),n)})),n))for(;l>u;u++)n(e[u],r,s?i:i.call(e[u],u,n(e[u],r)));return o?e:c?n.call(e):l?n(e[0],r):a},now:function(){return(new Date).getTime()}}),b.ready.promise=function(t){if(!n)if(n=b.Deferred(),"complete"===o.readyState)setTimeout(b.ready);else if(o.addEventListener)o.addEventListener("DOMContentLoaded",H,!1),e.addEventListener("load",H,!1);else{o.attachEvent("onreadystatechange",H),e.attachEvent("onload",H);var r=!1;try{r=null==e.frameElement&&o.documentElement}catch(i){}r&&r.doScroll&&function a(){if(!b.isReady){try{r.doScroll("left")}catch(e){return setTimeout(a,50)}q(),b.ready()}}()}return n.promise(t)},b.each("Boolean Number String Function Array Date RegExp Object Error".split(" "),function(e,t){l["[object "+t+"]"]=t.toLowerCase()});function M(e){var t=e.length,n=b.type(e);return b.isWindow(e)?!1:1===e.nodeType&&t?!0:"array"===n||"function"!==n&&(0===t||"number"==typeof t&&t>0&&t-1 in e)}r=b(o);var _={};function F(e){var t=_[e]={};return b.each(e.match(w)||[],function(e,n){t[n]=!0}),t}b.Callbacks=function(e){e="string"==typeof e?_[e]||F(e):b.extend({},e);var n,r,i,o,a,s,u=[],l=!e.once&&[],c=function(t){for(r=e.memory&&t,i=!0,a=s||0,s=0,o=u.length,n=!0;u&&o>a;a++)if(u[a].apply(t[0],t[1])===!1&&e.stopOnFalse){r=!1;break}n=!1,u&&(l?l.length&&c(l.shift()):r?u=[]:p.disable())},p={add:function(){if(u){var t=u.length;(function i(t){b.each(t,function(t,n){var r=b.type(n);"function"===r?e.unique&&p.has(n)||u.push(n):n&&n.length&&"string"!==r&&i(n)})})(arguments),n?o=u.length:r&&(s=t,c(r))}return this},remove:function(){return u&&b.each(arguments,function(e,t){var r;while((r=b.inArray(t,u,r))>-1)u.splice(r,1),n&&(o>=r&&o--,a>=r&&a--)}),this},has:function(e){return e?b.inArray(e,u)>-1:!(!u||!u.length)},empty:function(){return u=[],this},disable:function(){return u=l=r=t,this},disabled:function(){return!u},lock:function(){return l=t,r||p.disable(),this},locked:function(){return!l},fireWith:function(e,t){return t=t||[],t=[e,t.slice?t.slice():t],!u||i&&!l||(n?l.push(t):c(t)),this},fire:function(){return p.fireWith(this,arguments),this},fired:function(){return!!i}};return p},b.extend({Deferred:function(e){var t=[["resolve","done",b.Callbacks("once memory"),"resolved"],["reject","fail",b.Callbacks("once memory"),"rejected"],["notify","progress",b.Callbacks("memory")]],n="pending",r={state:function(){return n},always:function(){return i.done(arguments).fail(arguments),this},then:function(){var e=arguments;return b.Deferred(function(n){b.each(t,function(t,o){var a=o[0],s=b.isFunction(e[t])&&e[t];i[o[1]](function(){var e=s&&s.apply(this,arguments);e&&b.isFunction(e.promise)?e.promise().done(n.resolve).fail(n.reject).progress(n.notify):n[a+"With"](this===r?n.promise():this,s?[e]:arguments)})}),e=null}).promise()},promise:function(e){return null!=e?b.extend(e,r):r}},i={};return r.pipe=r.then,b.each(t,function(e,o){var a=o[2],s=o[3];r[o[1]]=a.add,s&&a.add(function(){n=s},t[1^e][2].disable,t[2][2].lock),i[o[0]]=function(){return i[o[0]+"With"](this===i?r:this,arguments),this},i[o[0]+"With"]=a.fireWith}),r.promise(i),e&&e.call(i,i),i},when:function(e){var t=0,n=h.call(arguments),r=n.length,i=1!==r||e&&b.isFunction(e.promise)?r:0,o=1===i?e:b.Deferred(),a=function(e,t,n){return function(r){t[e]=this,n[e]=arguments.length>1?h.call(arguments):r,n===s?o.notifyWith(t,n):--i||o.resolveWith(t,n)}},s,u,l;if(r>1)for(s=Array(r),u=Array(r),l=Array(r);r>t;t++)n[t]&&b.isFunction(n[t].promise)?n[t].promise().done(a(t,l,n)).fail(o.reject).progress(a(t,u,s)):--i;return i||o.resolveWith(l,n),o.promise()}}),b.support=function(){var t,n,r,a,s,u,l,c,p,f,d=o.createElement("div");if(d.setAttribute("className","t"),d.innerHTML="  <link/><table></table><a href='/a'>a</a><input type='checkbox'/>",n=d.getElementsByTagName("*"),r=d.getElementsByTagName("a")[0],!n||!r||!n.length)return{};s=o.createElement("select"),l=s.appendChild(o.createElement("option")),a=d.getElementsByTagName("input")[0],r.style.cssText="top:1px;float:left;opacity:.5",t={getSetAttribute:"t"!==d.className,leadingWhitespace:3===d.firstChild.nodeType,tbody:!d.getElementsByTagName("tbody").length,htmlSerialize:!!d.getElementsByTagName("link").length,style:/top/.test(r.getAttribute("style")),hrefNormalized:"/a"===r.getAttribute("href"),opacity:/^0.5/.test(r.style.opacity),cssFloat:!!r.style.cssFloat,checkOn:!!a.value,optSelected:l.selected,enctype:!!o.createElement("form").enctype,html5Clone:"<:nav></:nav>"!==o.createElement("nav").cloneNode(!0).outerHTML,boxModel:"CSS1Compat"===o.compatMode,deleteExpando:!0,noCloneEvent:!0,inlineBlockNeedsLayout:!1,shrinkWrapBlocks:!1,reliableMarginRight:!0,boxSizingReliable:!0,pixelPosition:!1},a.checked=!0,t.noCloneChecked=a.cloneNode(!0).checked,s.disabled=!0,t.optDisabled=!l.disabled;try{delete d.test}catch(h){t.deleteExpando=!1}a=o.createElement("input"),a.setAttribute("value",""),t.input=""===a.getAttribute("value"),a.value="t",a.setAttribute("type","radio"),t.radioValue="t"===a.value,a.setAttribute("checked","t"),a.setAttribute("name","t"),u=o.createDocumentFragment(),u.appendChild(a),t.appendChecked=a.checked,t.checkClone=u.cloneNode(!0).cloneNode(!0).lastChild.checked,d.attachEvent&&(d.attachEvent("onclick",function(){t.noCloneEvent=!1}),d.cloneNode(!0).click());for(f in{submit:!0,change:!0,focusin:!0})d.setAttribute(c="on"+f,"t"),t[f+"Bubbles"]=c in e||d.attributes[c].expando===!1;return d.style.backgroundClip="content-box",d.cloneNode(!0).style.backgroundClip="",t.clearCloneStyle="content-box"===d.style.backgroundClip,b(function(){var n,r,a,s="padding:0;margin:0;border:0;display:block;box-sizing:content-box;-moz-box-sizing:content-box;-webkit-box-sizing:content-box;",u=o.getElementsByTagName("body")[0];u&&(n=o.createElement("div"),n.style.cssText="border:0;width:0;height:0;position:absolute;top:0;left:-9999px;margin-top:1px",u.appendChild(n).appendChild(d),d.innerHTML="<table><tr><td></td><td>t</td></tr></table>",a=d.getElementsByTagName("td"),a[0].style.cssText="padding:0;margin:0;border:0;display:none",p=0===a[0].offsetHeight,a[0].style.display="",a[1].style.display="none",t.reliableHiddenOffsets=p&&0===a[0].offsetHeight,d.innerHTML="",d.style.cssText="box-sizing:border-box;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;padding:1px;border:1px;display:block;width:4px;margin-top:1%;position:absolute;top:1%;",t.boxSizing=4===d.offsetWidth,t.doesNotIncludeMarginInBodyOffset=1!==u.offsetTop,e.getComputedStyle&&(t.pixelPosition="1%"!==(e.getComputedStyle(d,null)||{}).top,t.boxSizingReliable="4px"===(e.getComputedStyle(d,null)||{width:"4px"}).width,r=d.appendChild(o.createElement("div")),r.style.cssText=d.style.cssText=s,r.style.marginRight=r.style.width="0",d.style.width="1px",t.reliableMarginRight=!parseFloat((e.getComputedStyle(r,null)||{}).marginRight)),typeof d.style.zoom!==i&&(d.innerHTML="",d.style.cssText=s+"width:1px;padding:1px;display:inline;zoom:1",t.inlineBlockNeedsLayout=3===d.offsetWidth,d.style.display="block",d.innerHTML="<div></div>",d.firstChild.style.width="5px",t.shrinkWrapBlocks=3!==d.offsetWidth,t.inlineBlockNeedsLayout&&(u.style.zoom=1)),u.removeChild(n),n=d=a=r=null)}),n=s=u=l=r=a=null,t}();var O=/(?:\{[\s\S]*\}|\[[\s\S]*\])$/,B=/([A-Z])/g;function P(e,n,r,i){if(b.acceptData(e)){var o,a,s=b.expando,u="string"==typeof n,l=e.nodeType,p=l?b.cache:e,f=l?e[s]:e[s]&&s;if(f&&p[f]&&(i||p[f].data)||!u||r!==t)return f||(l?e[s]=f=c.pop()||b.guid++:f=s),p[f]||(p[f]={},l||(p[f].toJSON=b.noop)),("object"==typeof n||"function"==typeof n)&&(i?p[f]=b.extend(p[f],n):p[f].data=b.extend(p[f].data,n)),o=p[f],i||(o.data||(o.data={}),o=o.data),r!==t&&(o[b.camelCase(n)]=r),u?(a=o[n],null==a&&(a=o[b.camelCase(n)])):a=o,a}}function R(e,t,n){if(b.acceptData(e)){var r,i,o,a=e.nodeType,s=a?b.cache:e,u=a?e[b.expando]:b.expando;if(s[u]){if(t&&(o=n?s[u]:s[u].data)){b.isArray(t)?t=t.concat(b.map(t,b.camelCase)):t in o?t=[t]:(t=b.camelCase(t),t=t in o?[t]:t.split(" "));for(r=0,i=t.length;i>r;r++)delete o[t[r]];if(!(n?$:b.isEmptyObject)(o))return}(n||(delete s[u].data,$(s[u])))&&(a?b.cleanData([e],!0):b.support.deleteExpando||s!=s.window?delete s[u]:s[u]=null)}}}b.extend({cache:{},expando:"jQuery"+(p+Math.random()).replace(/\D/g,""),noData:{embed:!0,object:"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000",applet:!0},hasData:function(e){return e=e.nodeType?b.cache[e[b.expando]]:e[b.expando],!!e&&!$(e)},data:function(e,t,n){return P(e,t,n)},removeData:function(e,t){return R(e,t)},_data:function(e,t,n){return P(e,t,n,!0)},_removeData:function(e,t){return R(e,t,!0)},acceptData:function(e){if(e.nodeType&&1!==e.nodeType&&9!==e.nodeType)return!1;var t=e.nodeName&&b.noData[e.nodeName.toLowerCase()];return!t||t!==!0&&e.getAttribute("classid")===t}}),b.fn.extend({data:function(e,n){var r,i,o=this[0],a=0,s=null;if(e===t){if(this.length&&(s=b.data(o),1===o.nodeType&&!b._data(o,"parsedAttrs"))){for(r=o.attributes;r.length>a;a++)i=r[a].name,i.indexOf("data-")||(i=b.camelCase(i.slice(5)),W(o,i,s[i]));b._data(o,"parsedAttrs",!0)}return s}return"object"==typeof e?this.each(function(){b.data(this,e)}):b.access(this,function(n){return n===t?o?W(o,e,b.data(o,e)):null:(this.each(function(){b.data(this,e,n)}),t)},null,n,arguments.length>1,null,!0)},removeData:function(e){return this.each(function(){b.removeData(this,e)})}});function W(e,n,r){if(r===t&&1===e.nodeType){var i="data-"+n.replace(B,"-$1").toLowerCase();if(r=e.getAttribute(i),"string"==typeof r){try{r="true"===r?!0:"false"===r?!1:"null"===r?null:+r+""===r?+r:O.test(r)?b.parseJSON(r):r}catch(o){}b.data(e,n,r)}else r=t}return r}function $(e){var t;for(t in e)if(("data"!==t||!b.isEmptyObject(e[t]))&&"toJSON"!==t)return!1;return!0}b.extend({queue:function(e,n,r){var i;return e?(n=(n||"fx")+"queue",i=b._data(e,n),r&&(!i||b.isArray(r)?i=b._data(e,n,b.makeArray(r)):i.push(r)),i||[]):t},dequeue:function(e,t){t=t||"fx";var n=b.queue(e,t),r=n.length,i=n.shift(),o=b._queueHooks(e,t),a=function(){b.dequeue(e,t)};"inprogress"===i&&(i=n.shift(),r--),o.cur=i,i&&("fx"===t&&n.unshift("inprogress"),delete o.stop,i.call(e,a,o)),!r&&o&&o.empty.fire()},_queueHooks:function(e,t){var n=t+"queueHooks";return b._data(e,n)||b._data(e,n,{empty:b.Callbacks("once memory").add(function(){b._removeData(e,t+"queue"),b._removeData(e,n)})})}}),b.fn.extend({queue:function(e,n){var r=2;return"string"!=typeof e&&(n=e,e="fx",r--),r>arguments.length?b.queue(this[0],e):n===t?this:this.each(function(){var t=b.queue(this,e,n);b._queueHooks(this,e),"fx"===e&&"inprogress"!==t[0]&&b.dequeue(this,e)})},dequeue:function(e){return this.each(function(){b.dequeue(this,e)})},delay:function(e,t){return e=b.fx?b.fx.speeds[e]||e:e,t=t||"fx",this.queue(t,function(t,n){var r=setTimeout(t,e);n.stop=function(){clearTimeout(r)}})},clearQueue:function(e){return this.queue(e||"fx",[])},promise:function(e,n){var r,i=1,o=b.Deferred(),a=this,s=this.length,u=function(){--i||o.resolveWith(a,[a])};"string"!=typeof e&&(n=e,e=t),e=e||"fx";while(s--)r=b._data(a[s],e+"queueHooks"),r&&r.empty&&(i++,r.empty.add(u));return u(),o.promise(n)}});var I,z,X=/[\t\r\n]/g,U=/\r/g,V=/^(?:input|select|textarea|button|object)$/i,Y=/^(?:a|area)$/i,J=/^(?:checked|selected|autofocus|autoplay|async|controls|defer|disabled|hidden|loop|multiple|open|readonly|required|scoped)$/i,G=/^(?:checked|selected)$/i,Q=b.support.getSetAttribute,K=b.support.input;b.fn.extend({attr:function(e,t){return b.access(this,b.attr,e,t,arguments.length>1)},removeAttr:function(e){return this.each(function(){b.removeAttr(this,e)})},prop:function(e,t){return b.access(this,b.prop,e,t,arguments.length>1)},removeProp:function(e){return e=b.propFix[e]||e,this.each(function(){try{this[e]=t,delete this[e]}catch(n){}})},addClass:function(e){var t,n,r,i,o,a=0,s=this.length,u="string"==typeof e&&e;if(b.isFunction(e))return this.each(function(t){b(this).addClass(e.call(this,t,this.className))});if(u)for(t=(e||"").match(w)||[];s>a;a++)if(n=this[a],r=1===n.nodeType&&(n.className?(" "+n.className+" ").replace(X," "):" ")){o=0;while(i=t[o++])0>r.indexOf(" "+i+" ")&&(r+=i+" ");n.className=b.trim(r)}return this},removeClass:function(e){var t,n,r,i,o,a=0,s=this.length,u=0===arguments.length||"string"==typeof e&&e;if(b.isFunction(e))return this.each(function(t){b(this).removeClass(e.call(this,t,this.className))});if(u)for(t=(e||"").match(w)||[];s>a;a++)if(n=this[a],r=1===n.nodeType&&(n.className?(" "+n.className+" ").replace(X," "):"")){o=0;while(i=t[o++])while(r.indexOf(" "+i+" ")>=0)r=r.replace(" "+i+" "," ");n.className=e?b.trim(r):""}return this},toggleClass:function(e,t){var n=typeof e,r="boolean"==typeof t;return b.isFunction(e)?this.each(function(n){b(this).toggleClass(e.call(this,n,this.className,t),t)}):this.each(function(){if("string"===n){var o,a=0,s=b(this),u=t,l=e.match(w)||[];while(o=l[a++])u=r?u:!s.hasClass(o),s[u?"addClass":"removeClass"](o)}else(n===i||"boolean"===n)&&(this.className&&b._data(this,"__className__",this.className),this.className=this.className||e===!1?"":b._data(this,"__className__")||"")})},hasClass:function(e){var t=" "+e+" ",n=0,r=this.length;for(;r>n;n++)if(1===this[n].nodeType&&(" "+this[n].className+" ").replace(X," ").indexOf(t)>=0)return!0;return!1},val:function(e){var n,r,i,o=this[0];{if(arguments.length)return i=b.isFunction(e),this.each(function(n){var o,a=b(this);1===this.nodeType&&(o=i?e.call(this,n,a.val()):e,null==o?o="":"number"==typeof o?o+="":b.isArray(o)&&(o=b.map(o,function(e){return null==e?"":e+""})),r=b.valHooks[this.type]||b.valHooks[this.nodeName.toLowerCase()],r&&"set"in r&&r.set(this,o,"value")!==t||(this.value=o))});if(o)return r=b.valHooks[o.type]||b.valHooks[o.nodeName.toLowerCase()],r&&"get"in r&&(n=r.get(o,"value"))!==t?n:(n=o.value,"string"==typeof n?n.replace(U,""):null==n?"":n)}}}),b.extend({valHooks:{option:{get:function(e){var t=e.attributes.value;return!t||t.specified?e.value:e.text}},select:{get:function(e){var t,n,r=e.options,i=e.selectedIndex,o="select-one"===e.type||0>i,a=o?null:[],s=o?i+1:r.length,u=0>i?s:o?i:0;for(;s>u;u++)if(n=r[u],!(!n.selected&&u!==i||(b.support.optDisabled?n.disabled:null!==n.getAttribute("disabled"))||n.parentNode.disabled&&b.nodeName(n.parentNode,"optgroup"))){if(t=b(n).val(),o)return t;a.push(t)}return a},set:function(e,t){var n=b.makeArray(t);return b(e).find("option").each(function(){this.selected=b.inArray(b(this).val(),n)>=0}),n.length||(e.selectedIndex=-1),n}}},attr:function(e,n,r){var o,a,s,u=e.nodeType;if(e&&3!==u&&8!==u&&2!==u)return typeof e.getAttribute===i?b.prop(e,n,r):(a=1!==u||!b.isXMLDoc(e),a&&(n=n.toLowerCase(),o=b.attrHooks[n]||(J.test(n)?z:I)),r===t?o&&a&&"get"in o&&null!==(s=o.get(e,n))?s:(typeof e.getAttribute!==i&&(s=e.getAttribute(n)),null==s?t:s):null!==r?o&&a&&"set"in o&&(s=o.set(e,r,n))!==t?s:(e.setAttribute(n,r+""),r):(b.removeAttr(e,n),t))},removeAttr:function(e,t){var n,r,i=0,o=t&&t.match(w);if(o&&1===e.nodeType)while(n=o[i++])r=b.propFix[n]||n,J.test(n)?!Q&&G.test(n)?e[b.camelCase("default-"+n)]=e[r]=!1:e[r]=!1:b.attr(e,n,""),e.removeAttribute(Q?n:r)},attrHooks:{type:{set:function(e,t){if(!b.support.radioValue&&"radio"===t&&b.nodeName(e,"input")){var n=e.value;return e.setAttribute("type",t),n&&(e.value=n),t}}}},propFix:{tabindex:"tabIndex",readonly:"readOnly","for":"htmlFor","class":"className",maxlength:"maxLength",cellspacing:"cellSpacing",cellpadding:"cellPadding",rowspan:"rowSpan",colspan:"colSpan",usemap:"useMap",frameborder:"frameBorder",contenteditable:"contentEditable"},prop:function(e,n,r){var i,o,a,s=e.nodeType;if(e&&3!==s&&8!==s&&2!==s)return a=1!==s||!b.isXMLDoc(e),a&&(n=b.propFix[n]||n,o=b.propHooks[n]),r!==t?o&&"set"in o&&(i=o.set(e,r,n))!==t?i:e[n]=r:o&&"get"in o&&null!==(i=o.get(e,n))?i:e[n]},propHooks:{tabIndex:{get:function(e){var n=e.getAttributeNode("tabindex");return n&&n.specified?parseInt(n.value,10):V.test(e.nodeName)||Y.test(e.nodeName)&&e.href?0:t}}}}),z={get:function(e,n){var r=b.prop(e,n),i="boolean"==typeof r&&e.getAttribute(n),o="boolean"==typeof r?K&&Q?null!=i:G.test(n)?e[b.camelCase("default-"+n)]:!!i:e.getAttributeNode(n);return o&&o.value!==!1?n.toLowerCase():t},set:function(e,t,n){return t===!1?b.removeAttr(e,n):K&&Q||!G.test(n)?e.setAttribute(!Q&&b.propFix[n]||n,n):e[b.camelCase("default-"+n)]=e[n]=!0,n}},K&&Q||(b.attrHooks.value={get:function(e,n){var r=e.getAttributeNode(n);return b.nodeName(e,"input")?e.defaultValue:r&&r.specified?r.value:t},set:function(e,n,r){return b.nodeName(e,"input")?(e.defaultValue=n,t):I&&I.set(e,n,r)}}),Q||(I=b.valHooks.button={get:function(e,n){var r=e.getAttributeNode(n);return r&&("id"===n||"name"===n||"coords"===n?""!==r.value:r.specified)?r.value:t},set:function(e,n,r){var i=e.getAttributeNode(r);return i||e.setAttributeNode(i=e.ownerDocument.createAttribute(r)),i.value=n+="","value"===r||n===e.getAttribute(r)?n:t}},b.attrHooks.contenteditable={get:I.get,set:function(e,t,n){I.set(e,""===t?!1:t,n)}},b.each(["width","height"],function(e,n){b.attrHooks[n]=b.extend(b.attrHooks[n],{set:function(e,r){return""===r?(e.setAttribute(n,"auto"),r):t}})})),b.support.hrefNormalized||(b.each(["href","src","width","height"],function(e,n){b.attrHooks[n]=b.extend(b.attrHooks[n],{get:function(e){var r=e.getAttribute(n,2);return null==r?t:r}})}),b.each(["href","src"],function(e,t){b.propHooks[t]={get:function(e){return e.getAttribute(t,4)}}})),b.support.style||(b.attrHooks.style={get:function(e){return e.style.cssText||t},set:function(e,t){return e.style.cssText=t+""}}),b.support.optSelected||(b.propHooks.selected=b.extend(b.propHooks.selected,{get:function(e){var t=e.parentNode;return t&&(t.selectedIndex,t.parentNode&&t.parentNode.selectedIndex),null}})),b.support.enctype||(b.propFix.enctype="encoding"),b.support.checkOn||b.each(["radio","checkbox"],function(){b.valHooks[this]={get:function(e){return null===e.getAttribute("value")?"on":e.value}}}),b.each(["radio","checkbox"],function(){b.valHooks[this]=b.extend(b.valHooks[this],{set:function(e,n){return b.isArray(n)?e.checked=b.inArray(b(e).val(),n)>=0:t}})});var Z=/^(?:input|select|textarea)$/i,et=/^key/,tt=/^(?:mouse|contextmenu)|click/,nt=/^(?:focusinfocus|focusoutblur)$/,rt=/^([^.]*)(?:\.(.+)|)$/;function it(){return!0}function ot(){return!1}b.event={global:{},add:function(e,n,r,o,a){var s,u,l,c,p,f,d,h,g,m,y,v=b._data(e);if(v){r.handler&&(c=r,r=c.handler,a=c.selector),r.guid||(r.guid=b.guid++),(u=v.events)||(u=v.events={}),(f=v.handle)||(f=v.handle=function(e){return typeof b===i||e&&b.event.triggered===e.type?t:b.event.dispatch.apply(f.elem,arguments)},f.elem=e),n=(n||"").match(w)||[""],l=n.length;while(l--)s=rt.exec(n[l])||[],g=y=s[1],m=(s[2]||"").split(".").sort(),p=b.event.special[g]||{},g=(a?p.delegateType:p.bindType)||g,p=b.event.special[g]||{},d=b.extend({type:g,origType:y,data:o,handler:r,guid:r.guid,selector:a,needsContext:a&&b.expr.match.needsContext.test(a),namespace:m.join(".")},c),(h=u[g])||(h=u[g]=[],h.delegateCount=0,p.setup&&p.setup.call(e,o,m,f)!==!1||(e.addEventListener?e.addEventListener(g,f,!1):e.attachEvent&&e.attachEvent("on"+g,f))),p.add&&(p.add.call(e,d),d.handler.guid||(d.handler.guid=r.guid)),a?h.splice(h.delegateCount++,0,d):h.push(d),b.event.global[g]=!0;e=null}},remove:function(e,t,n,r,i){var o,a,s,u,l,c,p,f,d,h,g,m=b.hasData(e)&&b._data(e);if(m&&(c=m.events)){t=(t||"").match(w)||[""],l=t.length;while(l--)if(s=rt.exec(t[l])||[],d=g=s[1],h=(s[2]||"").split(".").sort(),d){p=b.event.special[d]||{},d=(r?p.delegateType:p.bindType)||d,f=c[d]||[],s=s[2]&&RegExp("(^|\\.)"+h.join("\\.(?:.*\\.|)")+"(\\.|$)"),u=o=f.length;while(o--)a=f[o],!i&&g!==a.origType||n&&n.guid!==a.guid||s&&!s.test(a.namespace)||r&&r!==a.selector&&("**"!==r||!a.selector)||(f.splice(o,1),a.selector&&f.delegateCount--,p.remove&&p.remove.call(e,a));u&&!f.length&&(p.teardown&&p.teardown.call(e,h,m.handle)!==!1||b.removeEvent(e,d,m.handle),delete c[d])}else for(d in c)b.event.remove(e,d+t[l],n,r,!0);b.isEmptyObject(c)&&(delete m.handle,b._removeData(e,"events"))}},trigger:function(n,r,i,a){var s,u,l,c,p,f,d,h=[i||o],g=y.call(n,"type")?n.type:n,m=y.call(n,"namespace")?n.namespace.split("."):[];if(l=f=i=i||o,3!==i.nodeType&&8!==i.nodeType&&!nt.test(g+b.event.triggered)&&(g.indexOf(".")>=0&&(m=g.split("."),g=m.shift(),m.sort()),u=0>g.indexOf(":")&&"on"+g,n=n[b.expando]?n:new b.Event(g,"object"==typeof n&&n),n.isTrigger=!0,n.namespace=m.join("."),n.namespace_re=n.namespace?RegExp("(^|\\.)"+m.join("\\.(?:.*\\.|)")+"(\\.|$)"):null,n.result=t,n.target||(n.target=i),r=null==r?[n]:b.makeArray(r,[n]),p=b.event.special[g]||{},a||!p.trigger||p.trigger.apply(i,r)!==!1)){if(!a&&!p.noBubble&&!b.isWindow(i)){for(c=p.delegateType||g,nt.test(c+g)||(l=l.parentNode);l;l=l.parentNode)h.push(l),f=l;f===(i.ownerDocument||o)&&h.push(f.defaultView||f.parentWindow||e)}d=0;while((l=h[d++])&&!n.isPropagationStopped())n.type=d>1?c:p.bindType||g,s=(b._data(l,"events")||{})[n.type]&&b._data(l,"handle"),s&&s.apply(l,r),s=u&&l[u],s&&b.acceptData(l)&&s.apply&&s.apply(l,r)===!1&&n.preventDefault();if(n.type=g,!(a||n.isDefaultPrevented()||p._default&&p._default.apply(i.ownerDocument,r)!==!1||"click"===g&&b.nodeName(i,"a")||!b.acceptData(i)||!u||!i[g]||b.isWindow(i))){f=i[u],f&&(i[u]=null),b.event.triggered=g;try{i[g]()}catch(v){}b.event.triggered=t,f&&(i[u]=f)}return n.result}},dispatch:function(e){e=b.event.fix(e);var n,r,i,o,a,s=[],u=h.call(arguments),l=(b._data(this,"events")||{})[e.type]||[],c=b.event.special[e.type]||{};if(u[0]=e,e.delegateTarget=this,!c.preDispatch||c.preDispatch.call(this,e)!==!1){s=b.event.handlers.call(this,e,l),n=0;while((o=s[n++])&&!e.isPropagationStopped()){e.currentTarget=o.elem,a=0;while((i=o.handlers[a++])&&!e.isImmediatePropagationStopped())(!e.namespace_re||e.namespace_re.test(i.namespace))&&(e.handleObj=i,e.data=i.data,r=((b.event.special[i.origType]||{}).handle||i.handler).apply(o.elem,u),r!==t&&(e.result=r)===!1&&(e.preventDefault(),e.stopPropagation()))}return c.postDispatch&&c.postDispatch.call(this,e),e.result}},handlers:function(e,n){var r,i,o,a,s=[],u=n.delegateCount,l=e.target;if(u&&l.nodeType&&(!e.button||"click"!==e.type))for(;l!=this;l=l.parentNode||this)if(1===l.nodeType&&(l.disabled!==!0||"click"!==e.type)){for(o=[],a=0;u>a;a++)i=n[a],r=i.selector+" ",o[r]===t&&(o[r]=i.needsContext?b(r,this).index(l)>=0:b.find(r,this,null,[l]).length),o[r]&&o.push(i);o.length&&s.push({elem:l,handlers:o})}return n.length>u&&s.push({elem:this,handlers:n.slice(u)}),s},fix:function(e){if(e[b.expando])return e;var t,n,r,i=e.type,a=e,s=this.fixHooks[i];s||(this.fixHooks[i]=s=tt.test(i)?this.mouseHooks:et.test(i)?this.keyHooks:{}),r=s.props?this.props.concat(s.props):this.props,e=new b.Event(a),t=r.length;while(t--)n=r[t],e[n]=a[n];return e.target||(e.target=a.srcElement||o),3===e.target.nodeType&&(e.target=e.target.parentNode),e.metaKey=!!e.metaKey,s.filter?s.filter(e,a):e},props:"altKey bubbles cancelable ctrlKey currentTarget eventPhase metaKey relatedTarget shiftKey target timeStamp view which".split(" "),fixHooks:{},keyHooks:{props:"char charCode key keyCode".split(" "),filter:function(e,t){return null==e.which&&(e.which=null!=t.charCode?t.charCode:t.keyCode),e}},mouseHooks:{props:"button buttons clientX clientY fromElement offsetX offsetY pageX pageY screenX screenY toElement".split(" "),filter:function(e,n){var r,i,a,s=n.button,u=n.fromElement;return null==e.pageX&&null!=n.clientX&&(i=e.target.ownerDocument||o,a=i.documentElement,r=i.body,e.pageX=n.clientX+(a&&a.scrollLeft||r&&r.scrollLeft||0)-(a&&a.clientLeft||r&&r.clientLeft||0),e.pageY=n.clientY+(a&&a.scrollTop||r&&r.scrollTop||0)-(a&&a.clientTop||r&&r.clientTop||0)),!e.relatedTarget&&u&&(e.relatedTarget=u===e.target?n.toElement:u),e.which||s===t||(e.which=1&s?1:2&s?3:4&s?2:0),e}},special:{load:{noBubble:!0},click:{trigger:function(){return b.nodeName(this,"input")&&"checkbox"===this.type&&this.click?(this.click(),!1):t}},focus:{trigger:function(){if(this!==o.activeElement&&this.focus)try{return this.focus(),!1}catch(e){}},delegateType:"focusin"},blur:{trigger:function(){return this===o.activeElement&&this.blur?(this.blur(),!1):t},delegateType:"focusout"},beforeunload:{postDispatch:function(e){e.result!==t&&(e.originalEvent.returnValue=e.result)}}},simulate:function(e,t,n,r){var i=b.extend(new b.Event,n,{type:e,isSimulated:!0,originalEvent:{}});r?b.event.trigger(i,null,t):b.event.dispatch.call(t,i),i.isDefaultPrevented()&&n.preventDefault()}},b.removeEvent=o.removeEventListener?function(e,t,n){e.removeEventListener&&e.removeEventListener(t,n,!1)}:function(e,t,n){var r="on"+t;e.detachEvent&&(typeof e[r]===i&&(e[r]=null),e.detachEvent(r,n))},b.Event=function(e,n){return this instanceof b.Event?(e&&e.type?(this.originalEvent=e,this.type=e.type,this.isDefaultPrevented=e.defaultPrevented||e.returnValue===!1||e.getPreventDefault&&e.getPreventDefault()?it:ot):this.type=e,n&&b.extend(this,n),this.timeStamp=e&&e.timeStamp||b.now(),this[b.expando]=!0,t):new b.Event(e,n)},b.Event.prototype={isDefaultPrevented:ot,isPropagationStopped:ot,isImmediatePropagationStopped:ot,preventDefault:function(){var e=this.originalEvent;this.isDefaultPrevented=it,e&&(e.preventDefault?e.preventDefault():e.returnValue=!1)},stopPropagation:function(){var e=this.originalEvent;this.isPropagationStopped=it,e&&(e.stopPropagation&&e.stopPropagation(),e.cancelBubble=!0)},stopImmediatePropagation:function(){this.isImmediatePropagationStopped=it,this.stopPropagation()}},b.each({mouseenter:"mouseover",mouseleave:"mouseout"},function(e,t){b.event.special[e]={delegateType:t,bindType:t,handle:function(e){var n,r=this,i=e.relatedTarget,o=e.handleObj;
return(!i||i!==r&&!b.contains(r,i))&&(e.type=o.origType,n=o.handler.apply(this,arguments),e.type=t),n}}}),b.support.submitBubbles||(b.event.special.submit={setup:function(){return b.nodeName(this,"form")?!1:(b.event.add(this,"click._submit keypress._submit",function(e){var n=e.target,r=b.nodeName(n,"input")||b.nodeName(n,"button")?n.form:t;r&&!b._data(r,"submitBubbles")&&(b.event.add(r,"submit._submit",function(e){e._submit_bubble=!0}),b._data(r,"submitBubbles",!0))}),t)},postDispatch:function(e){e._submit_bubble&&(delete e._submit_bubble,this.parentNode&&!e.isTrigger&&b.event.simulate("submit",this.parentNode,e,!0))},teardown:function(){return b.nodeName(this,"form")?!1:(b.event.remove(this,"._submit"),t)}}),b.support.changeBubbles||(b.event.special.change={setup:function(){return Z.test(this.nodeName)?(("checkbox"===this.type||"radio"===this.type)&&(b.event.add(this,"propertychange._change",function(e){"checked"===e.originalEvent.propertyName&&(this._just_changed=!0)}),b.event.add(this,"click._change",function(e){this._just_changed&&!e.isTrigger&&(this._just_changed=!1),b.event.simulate("change",this,e,!0)})),!1):(b.event.add(this,"beforeactivate._change",function(e){var t=e.target;Z.test(t.nodeName)&&!b._data(t,"changeBubbles")&&(b.event.add(t,"change._change",function(e){!this.parentNode||e.isSimulated||e.isTrigger||b.event.simulate("change",this.parentNode,e,!0)}),b._data(t,"changeBubbles",!0))}),t)},handle:function(e){var n=e.target;return this!==n||e.isSimulated||e.isTrigger||"radio"!==n.type&&"checkbox"!==n.type?e.handleObj.handler.apply(this,arguments):t},teardown:function(){return b.event.remove(this,"._change"),!Z.test(this.nodeName)}}),b.support.focusinBubbles||b.each({focus:"focusin",blur:"focusout"},function(e,t){var n=0,r=function(e){b.event.simulate(t,e.target,b.event.fix(e),!0)};b.event.special[t]={setup:function(){0===n++&&o.addEventListener(e,r,!0)},teardown:function(){0===--n&&o.removeEventListener(e,r,!0)}}}),b.fn.extend({on:function(e,n,r,i,o){var a,s;if("object"==typeof e){"string"!=typeof n&&(r=r||n,n=t);for(a in e)this.on(a,n,r,e[a],o);return this}if(null==r&&null==i?(i=n,r=n=t):null==i&&("string"==typeof n?(i=r,r=t):(i=r,r=n,n=t)),i===!1)i=ot;else if(!i)return this;return 1===o&&(s=i,i=function(e){return b().off(e),s.apply(this,arguments)},i.guid=s.guid||(s.guid=b.guid++)),this.each(function(){b.event.add(this,e,i,r,n)})},one:function(e,t,n,r){return this.on(e,t,n,r,1)},off:function(e,n,r){var i,o;if(e&&e.preventDefault&&e.handleObj)return i=e.handleObj,b(e.delegateTarget).off(i.namespace?i.origType+"."+i.namespace:i.origType,i.selector,i.handler),this;if("object"==typeof e){for(o in e)this.off(o,n,e[o]);return this}return(n===!1||"function"==typeof n)&&(r=n,n=t),r===!1&&(r=ot),this.each(function(){b.event.remove(this,e,r,n)})},bind:function(e,t,n){return this.on(e,null,t,n)},unbind:function(e,t){return this.off(e,null,t)},delegate:function(e,t,n,r){return this.on(t,e,n,r)},undelegate:function(e,t,n){return 1===arguments.length?this.off(e,"**"):this.off(t,e||"**",n)},trigger:function(e,t){return this.each(function(){b.event.trigger(e,t,this)})},triggerHandler:function(e,n){var r=this[0];return r?b.event.trigger(e,n,r,!0):t}}),function(e,t){var n,r,i,o,a,s,u,l,c,p,f,d,h,g,m,y,v,x="sizzle"+-new Date,w=e.document,T={},N=0,C=0,k=it(),E=it(),S=it(),A=typeof t,j=1<<31,D=[],L=D.pop,H=D.push,q=D.slice,M=D.indexOf||function(e){var t=0,n=this.length;for(;n>t;t++)if(this[t]===e)return t;return-1},_="[\\x20\\t\\r\\n\\f]",F="(?:\\\\.|[\\w-]|[^\\x00-\\xa0])+",O=F.replace("w","w#"),B="([*^$|!~]?=)",P="\\["+_+"*("+F+")"+_+"*(?:"+B+_+"*(?:(['\"])((?:\\\\.|[^\\\\])*?)\\3|("+O+")|)|)"+_+"*\\]",R=":("+F+")(?:\\(((['\"])((?:\\\\.|[^\\\\])*?)\\3|((?:\\\\.|[^\\\\()[\\]]|"+P.replace(3,8)+")*)|.*)\\)|)",W=RegExp("^"+_+"+|((?:^|[^\\\\])(?:\\\\.)*)"+_+"+$","g"),$=RegExp("^"+_+"*,"+_+"*"),I=RegExp("^"+_+"*([\\x20\\t\\r\\n\\f>+~])"+_+"*"),z=RegExp(R),X=RegExp("^"+O+"$"),U={ID:RegExp("^#("+F+")"),CLASS:RegExp("^\\.("+F+")"),NAME:RegExp("^\\[name=['\"]?("+F+")['\"]?\\]"),TAG:RegExp("^("+F.replace("w","w*")+")"),ATTR:RegExp("^"+P),PSEUDO:RegExp("^"+R),CHILD:RegExp("^:(only|first|last|nth|nth-last)-(child|of-type)(?:\\("+_+"*(even|odd|(([+-]|)(\\d*)n|)"+_+"*(?:([+-]|)"+_+"*(\\d+)|))"+_+"*\\)|)","i"),needsContext:RegExp("^"+_+"*[>+~]|:(even|odd|eq|gt|lt|nth|first|last)(?:\\("+_+"*((?:-\\d)?\\d*)"+_+"*\\)|)(?=[^-]|$)","i")},V=/[\x20\t\r\n\f]*[+~]/,Y=/^[^{]+\{\s*\[native code/,J=/^(?:#([\w-]+)|(\w+)|\.([\w-]+))$/,G=/^(?:input|select|textarea|button)$/i,Q=/^h\d$/i,K=/'|\\/g,Z=/\=[\x20\t\r\n\f]*([^'"\]]*)[\x20\t\r\n\f]*\]/g,et=/\\([\da-fA-F]{1,6}[\x20\t\r\n\f]?|.)/g,tt=function(e,t){var n="0x"+t-65536;return n!==n?t:0>n?String.fromCharCode(n+65536):String.fromCharCode(55296|n>>10,56320|1023&n)};try{q.call(w.documentElement.childNodes,0)[0].nodeType}catch(nt){q=function(e){var t,n=[];while(t=this[e++])n.push(t);return n}}function rt(e){return Y.test(e+"")}function it(){var e,t=[];return e=function(n,r){return t.push(n+=" ")>i.cacheLength&&delete e[t.shift()],e[n]=r}}function ot(e){return e[x]=!0,e}function at(e){var t=p.createElement("div");try{return e(t)}catch(n){return!1}finally{t=null}}function st(e,t,n,r){var i,o,a,s,u,l,f,g,m,v;if((t?t.ownerDocument||t:w)!==p&&c(t),t=t||p,n=n||[],!e||"string"!=typeof e)return n;if(1!==(s=t.nodeType)&&9!==s)return[];if(!d&&!r){if(i=J.exec(e))if(a=i[1]){if(9===s){if(o=t.getElementById(a),!o||!o.parentNode)return n;if(o.id===a)return n.push(o),n}else if(t.ownerDocument&&(o=t.ownerDocument.getElementById(a))&&y(t,o)&&o.id===a)return n.push(o),n}else{if(i[2])return H.apply(n,q.call(t.getElementsByTagName(e),0)),n;if((a=i[3])&&T.getByClassName&&t.getElementsByClassName)return H.apply(n,q.call(t.getElementsByClassName(a),0)),n}if(T.qsa&&!h.test(e)){if(f=!0,g=x,m=t,v=9===s&&e,1===s&&"object"!==t.nodeName.toLowerCase()){l=ft(e),(f=t.getAttribute("id"))?g=f.replace(K,"\\$&"):t.setAttribute("id",g),g="[id='"+g+"'] ",u=l.length;while(u--)l[u]=g+dt(l[u]);m=V.test(e)&&t.parentNode||t,v=l.join(",")}if(v)try{return H.apply(n,q.call(m.querySelectorAll(v),0)),n}catch(b){}finally{f||t.removeAttribute("id")}}}return wt(e.replace(W,"$1"),t,n,r)}a=st.isXML=function(e){var t=e&&(e.ownerDocument||e).documentElement;return t?"HTML"!==t.nodeName:!1},c=st.setDocument=function(e){var n=e?e.ownerDocument||e:w;return n!==p&&9===n.nodeType&&n.documentElement?(p=n,f=n.documentElement,d=a(n),T.tagNameNoComments=at(function(e){return e.appendChild(n.createComment("")),!e.getElementsByTagName("*").length}),T.attributes=at(function(e){e.innerHTML="<select></select>";var t=typeof e.lastChild.getAttribute("multiple");return"boolean"!==t&&"string"!==t}),T.getByClassName=at(function(e){return e.innerHTML="<div class='hidden e'></div><div class='hidden'></div>",e.getElementsByClassName&&e.getElementsByClassName("e").length?(e.lastChild.className="e",2===e.getElementsByClassName("e").length):!1}),T.getByName=at(function(e){e.id=x+0,e.innerHTML="<a name='"+x+"'></a><div name='"+x+"'></div>",f.insertBefore(e,f.firstChild);var t=n.getElementsByName&&n.getElementsByName(x).length===2+n.getElementsByName(x+0).length;return T.getIdNotName=!n.getElementById(x),f.removeChild(e),t}),i.attrHandle=at(function(e){return e.innerHTML="<a href='#'></a>",e.firstChild&&typeof e.firstChild.getAttribute!==A&&"#"===e.firstChild.getAttribute("href")})?{}:{href:function(e){return e.getAttribute("href",2)},type:function(e){return e.getAttribute("type")}},T.getIdNotName?(i.find.ID=function(e,t){if(typeof t.getElementById!==A&&!d){var n=t.getElementById(e);return n&&n.parentNode?[n]:[]}},i.filter.ID=function(e){var t=e.replace(et,tt);return function(e){return e.getAttribute("id")===t}}):(i.find.ID=function(e,n){if(typeof n.getElementById!==A&&!d){var r=n.getElementById(e);return r?r.id===e||typeof r.getAttributeNode!==A&&r.getAttributeNode("id").value===e?[r]:t:[]}},i.filter.ID=function(e){var t=e.replace(et,tt);return function(e){var n=typeof e.getAttributeNode!==A&&e.getAttributeNode("id");return n&&n.value===t}}),i.find.TAG=T.tagNameNoComments?function(e,n){return typeof n.getElementsByTagName!==A?n.getElementsByTagName(e):t}:function(e,t){var n,r=[],i=0,o=t.getElementsByTagName(e);if("*"===e){while(n=o[i++])1===n.nodeType&&r.push(n);return r}return o},i.find.NAME=T.getByName&&function(e,n){return typeof n.getElementsByName!==A?n.getElementsByName(name):t},i.find.CLASS=T.getByClassName&&function(e,n){return typeof n.getElementsByClassName===A||d?t:n.getElementsByClassName(e)},g=[],h=[":focus"],(T.qsa=rt(n.querySelectorAll))&&(at(function(e){e.innerHTML="<select><option selected=''></option></select>",e.querySelectorAll("[selected]").length||h.push("\\["+_+"*(?:checked|disabled|ismap|multiple|readonly|selected|value)"),e.querySelectorAll(":checked").length||h.push(":checked")}),at(function(e){e.innerHTML="<input type='hidden' i=''/>",e.querySelectorAll("[i^='']").length&&h.push("[*^$]="+_+"*(?:\"\"|'')"),e.querySelectorAll(":enabled").length||h.push(":enabled",":disabled"),e.querySelectorAll("*,:x"),h.push(",.*:")})),(T.matchesSelector=rt(m=f.matchesSelector||f.mozMatchesSelector||f.webkitMatchesSelector||f.oMatchesSelector||f.msMatchesSelector))&&at(function(e){T.disconnectedMatch=m.call(e,"div"),m.call(e,"[s!='']:x"),g.push("!=",R)}),h=RegExp(h.join("|")),g=RegExp(g.join("|")),y=rt(f.contains)||f.compareDocumentPosition?function(e,t){var n=9===e.nodeType?e.documentElement:e,r=t&&t.parentNode;return e===r||!(!r||1!==r.nodeType||!(n.contains?n.contains(r):e.compareDocumentPosition&&16&e.compareDocumentPosition(r)))}:function(e,t){if(t)while(t=t.parentNode)if(t===e)return!0;return!1},v=f.compareDocumentPosition?function(e,t){var r;return e===t?(u=!0,0):(r=t.compareDocumentPosition&&e.compareDocumentPosition&&e.compareDocumentPosition(t))?1&r||e.parentNode&&11===e.parentNode.nodeType?e===n||y(w,e)?-1:t===n||y(w,t)?1:0:4&r?-1:1:e.compareDocumentPosition?-1:1}:function(e,t){var r,i=0,o=e.parentNode,a=t.parentNode,s=[e],l=[t];if(e===t)return u=!0,0;if(!o||!a)return e===n?-1:t===n?1:o?-1:a?1:0;if(o===a)return ut(e,t);r=e;while(r=r.parentNode)s.unshift(r);r=t;while(r=r.parentNode)l.unshift(r);while(s[i]===l[i])i++;return i?ut(s[i],l[i]):s[i]===w?-1:l[i]===w?1:0},u=!1,[0,0].sort(v),T.detectDuplicates=u,p):p},st.matches=function(e,t){return st(e,null,null,t)},st.matchesSelector=function(e,t){if((e.ownerDocument||e)!==p&&c(e),t=t.replace(Z,"='$1']"),!(!T.matchesSelector||d||g&&g.test(t)||h.test(t)))try{var n=m.call(e,t);if(n||T.disconnectedMatch||e.document&&11!==e.document.nodeType)return n}catch(r){}return st(t,p,null,[e]).length>0},st.contains=function(e,t){return(e.ownerDocument||e)!==p&&c(e),y(e,t)},st.attr=function(e,t){var n;return(e.ownerDocument||e)!==p&&c(e),d||(t=t.toLowerCase()),(n=i.attrHandle[t])?n(e):d||T.attributes?e.getAttribute(t):((n=e.getAttributeNode(t))||e.getAttribute(t))&&e[t]===!0?t:n&&n.specified?n.value:null},st.error=function(e){throw Error("Syntax error, unrecognized expression: "+e)},st.uniqueSort=function(e){var t,n=[],r=1,i=0;if(u=!T.detectDuplicates,e.sort(v),u){for(;t=e[r];r++)t===e[r-1]&&(i=n.push(r));while(i--)e.splice(n[i],1)}return e};function ut(e,t){var n=t&&e,r=n&&(~t.sourceIndex||j)-(~e.sourceIndex||j);if(r)return r;if(n)while(n=n.nextSibling)if(n===t)return-1;return e?1:-1}function lt(e){return function(t){var n=t.nodeName.toLowerCase();return"input"===n&&t.type===e}}function ct(e){return function(t){var n=t.nodeName.toLowerCase();return("input"===n||"button"===n)&&t.type===e}}function pt(e){return ot(function(t){return t=+t,ot(function(n,r){var i,o=e([],n.length,t),a=o.length;while(a--)n[i=o[a]]&&(n[i]=!(r[i]=n[i]))})})}o=st.getText=function(e){var t,n="",r=0,i=e.nodeType;if(i){if(1===i||9===i||11===i){if("string"==typeof e.textContent)return e.textContent;for(e=e.firstChild;e;e=e.nextSibling)n+=o(e)}else if(3===i||4===i)return e.nodeValue}else for(;t=e[r];r++)n+=o(t);return n},i=st.selectors={cacheLength:50,createPseudo:ot,match:U,find:{},relative:{">":{dir:"parentNode",first:!0}," ":{dir:"parentNode"},"+":{dir:"previousSibling",first:!0},"~":{dir:"previousSibling"}},preFilter:{ATTR:function(e){return e[1]=e[1].replace(et,tt),e[3]=(e[4]||e[5]||"").replace(et,tt),"~="===e[2]&&(e[3]=" "+e[3]+" "),e.slice(0,4)},CHILD:function(e){return e[1]=e[1].toLowerCase(),"nth"===e[1].slice(0,3)?(e[3]||st.error(e[0]),e[4]=+(e[4]?e[5]+(e[6]||1):2*("even"===e[3]||"odd"===e[3])),e[5]=+(e[7]+e[8]||"odd"===e[3])):e[3]&&st.error(e[0]),e},PSEUDO:function(e){var t,n=!e[5]&&e[2];return U.CHILD.test(e[0])?null:(e[4]?e[2]=e[4]:n&&z.test(n)&&(t=ft(n,!0))&&(t=n.indexOf(")",n.length-t)-n.length)&&(e[0]=e[0].slice(0,t),e[2]=n.slice(0,t)),e.slice(0,3))}},filter:{TAG:function(e){return"*"===e?function(){return!0}:(e=e.replace(et,tt).toLowerCase(),function(t){return t.nodeName&&t.nodeName.toLowerCase()===e})},CLASS:function(e){var t=k[e+" "];return t||(t=RegExp("(^|"+_+")"+e+"("+_+"|$)"))&&k(e,function(e){return t.test(e.className||typeof e.getAttribute!==A&&e.getAttribute("class")||"")})},ATTR:function(e,t,n){return function(r){var i=st.attr(r,e);return null==i?"!="===t:t?(i+="","="===t?i===n:"!="===t?i!==n:"^="===t?n&&0===i.indexOf(n):"*="===t?n&&i.indexOf(n)>-1:"$="===t?n&&i.slice(-n.length)===n:"~="===t?(" "+i+" ").indexOf(n)>-1:"|="===t?i===n||i.slice(0,n.length+1)===n+"-":!1):!0}},CHILD:function(e,t,n,r,i){var o="nth"!==e.slice(0,3),a="last"!==e.slice(-4),s="of-type"===t;return 1===r&&0===i?function(e){return!!e.parentNode}:function(t,n,u){var l,c,p,f,d,h,g=o!==a?"nextSibling":"previousSibling",m=t.parentNode,y=s&&t.nodeName.toLowerCase(),v=!u&&!s;if(m){if(o){while(g){p=t;while(p=p[g])if(s?p.nodeName.toLowerCase()===y:1===p.nodeType)return!1;h=g="only"===e&&!h&&"nextSibling"}return!0}if(h=[a?m.firstChild:m.lastChild],a&&v){c=m[x]||(m[x]={}),l=c[e]||[],d=l[0]===N&&l[1],f=l[0]===N&&l[2],p=d&&m.childNodes[d];while(p=++d&&p&&p[g]||(f=d=0)||h.pop())if(1===p.nodeType&&++f&&p===t){c[e]=[N,d,f];break}}else if(v&&(l=(t[x]||(t[x]={}))[e])&&l[0]===N)f=l[1];else while(p=++d&&p&&p[g]||(f=d=0)||h.pop())if((s?p.nodeName.toLowerCase()===y:1===p.nodeType)&&++f&&(v&&((p[x]||(p[x]={}))[e]=[N,f]),p===t))break;return f-=i,f===r||0===f%r&&f/r>=0}}},PSEUDO:function(e,t){var n,r=i.pseudos[e]||i.setFilters[e.toLowerCase()]||st.error("unsupported pseudo: "+e);return r[x]?r(t):r.length>1?(n=[e,e,"",t],i.setFilters.hasOwnProperty(e.toLowerCase())?ot(function(e,n){var i,o=r(e,t),a=o.length;while(a--)i=M.call(e,o[a]),e[i]=!(n[i]=o[a])}):function(e){return r(e,0,n)}):r}},pseudos:{not:ot(function(e){var t=[],n=[],r=s(e.replace(W,"$1"));return r[x]?ot(function(e,t,n,i){var o,a=r(e,null,i,[]),s=e.length;while(s--)(o=a[s])&&(e[s]=!(t[s]=o))}):function(e,i,o){return t[0]=e,r(t,null,o,n),!n.pop()}}),has:ot(function(e){return function(t){return st(e,t).length>0}}),contains:ot(function(e){return function(t){return(t.textContent||t.innerText||o(t)).indexOf(e)>-1}}),lang:ot(function(e){return X.test(e||"")||st.error("unsupported lang: "+e),e=e.replace(et,tt).toLowerCase(),function(t){var n;do if(n=d?t.getAttribute("xml:lang")||t.getAttribute("lang"):t.lang)return n=n.toLowerCase(),n===e||0===n.indexOf(e+"-");while((t=t.parentNode)&&1===t.nodeType);return!1}}),target:function(t){var n=e.location&&e.location.hash;return n&&n.slice(1)===t.id},root:function(e){return e===f},focus:function(e){return e===p.activeElement&&(!p.hasFocus||p.hasFocus())&&!!(e.type||e.href||~e.tabIndex)},enabled:function(e){return e.disabled===!1},disabled:function(e){return e.disabled===!0},checked:function(e){var t=e.nodeName.toLowerCase();return"input"===t&&!!e.checked||"option"===t&&!!e.selected},selected:function(e){return e.parentNode&&e.parentNode.selectedIndex,e.selected===!0},empty:function(e){for(e=e.firstChild;e;e=e.nextSibling)if(e.nodeName>"@"||3===e.nodeType||4===e.nodeType)return!1;return!0},parent:function(e){return!i.pseudos.empty(e)},header:function(e){return Q.test(e.nodeName)},input:function(e){return G.test(e.nodeName)},button:function(e){var t=e.nodeName.toLowerCase();return"input"===t&&"button"===e.type||"button"===t},text:function(e){var t;return"input"===e.nodeName.toLowerCase()&&"text"===e.type&&(null==(t=e.getAttribute("type"))||t.toLowerCase()===e.type)},first:pt(function(){return[0]}),last:pt(function(e,t){return[t-1]}),eq:pt(function(e,t,n){return[0>n?n+t:n]}),even:pt(function(e,t){var n=0;for(;t>n;n+=2)e.push(n);return e}),odd:pt(function(e,t){var n=1;for(;t>n;n+=2)e.push(n);return e}),lt:pt(function(e,t,n){var r=0>n?n+t:n;for(;--r>=0;)e.push(r);return e}),gt:pt(function(e,t,n){var r=0>n?n+t:n;for(;t>++r;)e.push(r);return e})}};for(n in{radio:!0,checkbox:!0,file:!0,password:!0,image:!0})i.pseudos[n]=lt(n);for(n in{submit:!0,reset:!0})i.pseudos[n]=ct(n);function ft(e,t){var n,r,o,a,s,u,l,c=E[e+" "];if(c)return t?0:c.slice(0);s=e,u=[],l=i.preFilter;while(s){(!n||(r=$.exec(s)))&&(r&&(s=s.slice(r[0].length)||s),u.push(o=[])),n=!1,(r=I.exec(s))&&(n=r.shift(),o.push({value:n,type:r[0].replace(W," ")}),s=s.slice(n.length));for(a in i.filter)!(r=U[a].exec(s))||l[a]&&!(r=l[a](r))||(n=r.shift(),o.push({value:n,type:a,matches:r}),s=s.slice(n.length));if(!n)break}return t?s.length:s?st.error(e):E(e,u).slice(0)}function dt(e){var t=0,n=e.length,r="";for(;n>t;t++)r+=e[t].value;return r}function ht(e,t,n){var i=t.dir,o=n&&"parentNode"===i,a=C++;return t.first?function(t,n,r){while(t=t[i])if(1===t.nodeType||o)return e(t,n,r)}:function(t,n,s){var u,l,c,p=N+" "+a;if(s){while(t=t[i])if((1===t.nodeType||o)&&e(t,n,s))return!0}else while(t=t[i])if(1===t.nodeType||o)if(c=t[x]||(t[x]={}),(l=c[i])&&l[0]===p){if((u=l[1])===!0||u===r)return u===!0}else if(l=c[i]=[p],l[1]=e(t,n,s)||r,l[1]===!0)return!0}}function gt(e){return e.length>1?function(t,n,r){var i=e.length;while(i--)if(!e[i](t,n,r))return!1;return!0}:e[0]}function mt(e,t,n,r,i){var o,a=[],s=0,u=e.length,l=null!=t;for(;u>s;s++)(o=e[s])&&(!n||n(o,r,i))&&(a.push(o),l&&t.push(s));return a}function yt(e,t,n,r,i,o){return r&&!r[x]&&(r=yt(r)),i&&!i[x]&&(i=yt(i,o)),ot(function(o,a,s,u){var l,c,p,f=[],d=[],h=a.length,g=o||xt(t||"*",s.nodeType?[s]:s,[]),m=!e||!o&&t?g:mt(g,f,e,s,u),y=n?i||(o?e:h||r)?[]:a:m;if(n&&n(m,y,s,u),r){l=mt(y,d),r(l,[],s,u),c=l.length;while(c--)(p=l[c])&&(y[d[c]]=!(m[d[c]]=p))}if(o){if(i||e){if(i){l=[],c=y.length;while(c--)(p=y[c])&&l.push(m[c]=p);i(null,y=[],l,u)}c=y.length;while(c--)(p=y[c])&&(l=i?M.call(o,p):f[c])>-1&&(o[l]=!(a[l]=p))}}else y=mt(y===a?y.splice(h,y.length):y),i?i(null,a,y,u):H.apply(a,y)})}function vt(e){var t,n,r,o=e.length,a=i.relative[e[0].type],s=a||i.relative[" "],u=a?1:0,c=ht(function(e){return e===t},s,!0),p=ht(function(e){return M.call(t,e)>-1},s,!0),f=[function(e,n,r){return!a&&(r||n!==l)||((t=n).nodeType?c(e,n,r):p(e,n,r))}];for(;o>u;u++)if(n=i.relative[e[u].type])f=[ht(gt(f),n)];else{if(n=i.filter[e[u].type].apply(null,e[u].matches),n[x]){for(r=++u;o>r;r++)if(i.relative[e[r].type])break;return yt(u>1&&gt(f),u>1&&dt(e.slice(0,u-1)).replace(W,"$1"),n,r>u&&vt(e.slice(u,r)),o>r&&vt(e=e.slice(r)),o>r&&dt(e))}f.push(n)}return gt(f)}function bt(e,t){var n=0,o=t.length>0,a=e.length>0,s=function(s,u,c,f,d){var h,g,m,y=[],v=0,b="0",x=s&&[],w=null!=d,T=l,C=s||a&&i.find.TAG("*",d&&u.parentNode||u),k=N+=null==T?1:Math.random()||.1;for(w&&(l=u!==p&&u,r=n);null!=(h=C[b]);b++){if(a&&h){g=0;while(m=e[g++])if(m(h,u,c)){f.push(h);break}w&&(N=k,r=++n)}o&&((h=!m&&h)&&v--,s&&x.push(h))}if(v+=b,o&&b!==v){g=0;while(m=t[g++])m(x,y,u,c);if(s){if(v>0)while(b--)x[b]||y[b]||(y[b]=L.call(f));y=mt(y)}H.apply(f,y),w&&!s&&y.length>0&&v+t.length>1&&st.uniqueSort(f)}return w&&(N=k,l=T),x};return o?ot(s):s}s=st.compile=function(e,t){var n,r=[],i=[],o=S[e+" "];if(!o){t||(t=ft(e)),n=t.length;while(n--)o=vt(t[n]),o[x]?r.push(o):i.push(o);o=S(e,bt(i,r))}return o};function xt(e,t,n){var r=0,i=t.length;for(;i>r;r++)st(e,t[r],n);return n}function wt(e,t,n,r){var o,a,u,l,c,p=ft(e);if(!r&&1===p.length){if(a=p[0]=p[0].slice(0),a.length>2&&"ID"===(u=a[0]).type&&9===t.nodeType&&!d&&i.relative[a[1].type]){if(t=i.find.ID(u.matches[0].replace(et,tt),t)[0],!t)return n;e=e.slice(a.shift().value.length)}o=U.needsContext.test(e)?0:a.length;while(o--){if(u=a[o],i.relative[l=u.type])break;if((c=i.find[l])&&(r=c(u.matches[0].replace(et,tt),V.test(a[0].type)&&t.parentNode||t))){if(a.splice(o,1),e=r.length&&dt(a),!e)return H.apply(n,q.call(r,0)),n;break}}}return s(e,p)(r,t,d,n,V.test(e)),n}i.pseudos.nth=i.pseudos.eq;function Tt(){}i.filters=Tt.prototype=i.pseudos,i.setFilters=new Tt,c(),st.attr=b.attr,b.find=st,b.expr=st.selectors,b.expr[":"]=b.expr.pseudos,b.unique=st.uniqueSort,b.text=st.getText,b.isXMLDoc=st.isXML,b.contains=st.contains}(e);var at=/Until$/,st=/^(?:parents|prev(?:Until|All))/,ut=/^.[^:#\[\.,]*$/,lt=b.expr.match.needsContext,ct={children:!0,contents:!0,next:!0,prev:!0};b.fn.extend({find:function(e){var t,n,r,i=this.length;if("string"!=typeof e)return r=this,this.pushStack(b(e).filter(function(){for(t=0;i>t;t++)if(b.contains(r[t],this))return!0}));for(n=[],t=0;i>t;t++)b.find(e,this[t],n);return n=this.pushStack(i>1?b.unique(n):n),n.selector=(this.selector?this.selector+" ":"")+e,n},has:function(e){var t,n=b(e,this),r=n.length;return this.filter(function(){for(t=0;r>t;t++)if(b.contains(this,n[t]))return!0})},not:function(e){return this.pushStack(ft(this,e,!1))},filter:function(e){return this.pushStack(ft(this,e,!0))},is:function(e){return!!e&&("string"==typeof e?lt.test(e)?b(e,this.context).index(this[0])>=0:b.filter(e,this).length>0:this.filter(e).length>0)},closest:function(e,t){var n,r=0,i=this.length,o=[],a=lt.test(e)||"string"!=typeof e?b(e,t||this.context):0;for(;i>r;r++){n=this[r];while(n&&n.ownerDocument&&n!==t&&11!==n.nodeType){if(a?a.index(n)>-1:b.find.matchesSelector(n,e)){o.push(n);break}n=n.parentNode}}return this.pushStack(o.length>1?b.unique(o):o)},index:function(e){return e?"string"==typeof e?b.inArray(this[0],b(e)):b.inArray(e.jquery?e[0]:e,this):this[0]&&this[0].parentNode?this.first().prevAll().length:-1},add:function(e,t){var n="string"==typeof e?b(e,t):b.makeArray(e&&e.nodeType?[e]:e),r=b.merge(this.get(),n);return this.pushStack(b.unique(r))},addBack:function(e){return this.add(null==e?this.prevObject:this.prevObject.filter(e))}}),b.fn.andSelf=b.fn.addBack;function pt(e,t){do e=e[t];while(e&&1!==e.nodeType);return e}b.each({parent:function(e){var t=e.parentNode;return t&&11!==t.nodeType?t:null},parents:function(e){return b.dir(e,"parentNode")},parentsUntil:function(e,t,n){return b.dir(e,"parentNode",n)},next:function(e){return pt(e,"nextSibling")},prev:function(e){return pt(e,"previousSibling")},nextAll:function(e){return b.dir(e,"nextSibling")},prevAll:function(e){return b.dir(e,"previousSibling")},nextUntil:function(e,t,n){return b.dir(e,"nextSibling",n)},prevUntil:function(e,t,n){return b.dir(e,"previousSibling",n)},siblings:function(e){return b.sibling((e.parentNode||{}).firstChild,e)},children:function(e){return b.sibling(e.firstChild)},contents:function(e){return b.nodeName(e,"iframe")?e.contentDocument||e.contentWindow.document:b.merge([],e.childNodes)}},function(e,t){b.fn[e]=function(n,r){var i=b.map(this,t,n);return at.test(e)||(r=n),r&&"string"==typeof r&&(i=b.filter(r,i)),i=this.length>1&&!ct[e]?b.unique(i):i,this.length>1&&st.test(e)&&(i=i.reverse()),this.pushStack(i)}}),b.extend({filter:function(e,t,n){return n&&(e=":not("+e+")"),1===t.length?b.find.matchesSelector(t[0],e)?[t[0]]:[]:b.find.matches(e,t)},dir:function(e,n,r){var i=[],o=e[n];while(o&&9!==o.nodeType&&(r===t||1!==o.nodeType||!b(o).is(r)))1===o.nodeType&&i.push(o),o=o[n];return i},sibling:function(e,t){var n=[];for(;e;e=e.nextSibling)1===e.nodeType&&e!==t&&n.push(e);return n}});function ft(e,t,n){if(t=t||0,b.isFunction(t))return b.grep(e,function(e,r){var i=!!t.call(e,r,e);return i===n});if(t.nodeType)return b.grep(e,function(e){return e===t===n});if("string"==typeof t){var r=b.grep(e,function(e){return 1===e.nodeType});if(ut.test(t))return b.filter(t,r,!n);t=b.filter(t,r)}return b.grep(e,function(e){return b.inArray(e,t)>=0===n})}function dt(e){var t=ht.split("|"),n=e.createDocumentFragment();if(n.createElement)while(t.length)n.createElement(t.pop());return n}var ht="abbr|article|aside|audio|bdi|canvas|data|datalist|details|figcaption|figure|footer|header|hgroup|mark|meter|nav|output|progress|section|summary|time|video",gt=/ jQuery\d+="(?:null|\d+)"/g,mt=RegExp("<(?:"+ht+")[\\s/>]","i"),yt=/^\s+/,vt=/<(?!area|br|col|embed|hr|img|input|link|meta|param)(([\w:]+)[^>]*)\/>/gi,bt=/<([\w:]+)/,xt=/<tbody/i,wt=/<|&#?\w+;/,Tt=/<(?:script|style|link)/i,Nt=/^(?:checkbox|radio)$/i,Ct=/checked\s*(?:[^=]|=\s*.checked.)/i,kt=/^$|\/(?:java|ecma)script/i,Et=/^true\/(.*)/,St=/^\s*<!(?:\[CDATA\[|--)|(?:\]\]|--)>\s*$/g,At={option:[1,"<select multiple='multiple'>","</select>"],legend:[1,"<fieldset>","</fieldset>"],area:[1,"<map>","</map>"],param:[1,"<object>","</object>"],thead:[1,"<table>","</table>"],tr:[2,"<table><tbody>","</tbody></table>"],col:[2,"<table><tbody></tbody><colgroup>","</colgroup></table>"],td:[3,"<table><tbody><tr>","</tr></tbody></table>"],_default:b.support.htmlSerialize?[0,"",""]:[1,"X<div>","</div>"]},jt=dt(o),Dt=jt.appendChild(o.createElement("div"));At.optgroup=At.option,At.tbody=At.tfoot=At.colgroup=At.caption=At.thead,At.th=At.td,b.fn.extend({text:function(e){return b.access(this,function(e){return e===t?b.text(this):this.empty().append((this[0]&&this[0].ownerDocument||o).createTextNode(e))},null,e,arguments.length)},wrapAll:function(e){if(b.isFunction(e))return this.each(function(t){b(this).wrapAll(e.call(this,t))});if(this[0]){var t=b(e,this[0].ownerDocument).eq(0).clone(!0);this[0].parentNode&&t.insertBefore(this[0]),t.map(function(){var e=this;while(e.firstChild&&1===e.firstChild.nodeType)e=e.firstChild;return e}).append(this)}return this},wrapInner:function(e){return b.isFunction(e)?this.each(function(t){b(this).wrapInner(e.call(this,t))}):this.each(function(){var t=b(this),n=t.contents();n.length?n.wrapAll(e):t.append(e)})},wrap:function(e){var t=b.isFunction(e);return this.each(function(n){b(this).wrapAll(t?e.call(this,n):e)})},unwrap:function(){return this.parent().each(function(){b.nodeName(this,"body")||b(this).replaceWith(this.childNodes)}).end()},append:function(){return this.domManip(arguments,!0,function(e){(1===this.nodeType||11===this.nodeType||9===this.nodeType)&&this.appendChild(e)})},prepend:function(){return this.domManip(arguments,!0,function(e){(1===this.nodeType||11===this.nodeType||9===this.nodeType)&&this.insertBefore(e,this.firstChild)})},before:function(){return this.domManip(arguments,!1,function(e){this.parentNode&&this.parentNode.insertBefore(e,this)})},after:function(){return this.domManip(arguments,!1,function(e){this.parentNode&&this.parentNode.insertBefore(e,this.nextSibling)})},remove:function(e,t){var n,r=0;for(;null!=(n=this[r]);r++)(!e||b.filter(e,[n]).length>0)&&(t||1!==n.nodeType||b.cleanData(Ot(n)),n.parentNode&&(t&&b.contains(n.ownerDocument,n)&&Mt(Ot(n,"script")),n.parentNode.removeChild(n)));return this},empty:function(){var e,t=0;for(;null!=(e=this[t]);t++){1===e.nodeType&&b.cleanData(Ot(e,!1));while(e.firstChild)e.removeChild(e.firstChild);e.options&&b.nodeName(e,"select")&&(e.options.length=0)}return this},clone:function(e,t){return e=null==e?!1:e,t=null==t?e:t,this.map(function(){return b.clone(this,e,t)})},html:function(e){return b.access(this,function(e){var n=this[0]||{},r=0,i=this.length;if(e===t)return 1===n.nodeType?n.innerHTML.replace(gt,""):t;if(!("string"!=typeof e||Tt.test(e)||!b.support.htmlSerialize&&mt.test(e)||!b.support.leadingWhitespace&&yt.test(e)||At[(bt.exec(e)||["",""])[1].toLowerCase()])){e=e.replace(vt,"<$1></$2>");try{for(;i>r;r++)n=this[r]||{},1===n.nodeType&&(b.cleanData(Ot(n,!1)),n.innerHTML=e);n=0}catch(o){}}n&&this.empty().append(e)},null,e,arguments.length)},replaceWith:function(e){var t=b.isFunction(e);return t||"string"==typeof e||(e=b(e).not(this).detach()),this.domManip([e],!0,function(e){var t=this.nextSibling,n=this.parentNode;n&&(b(this).remove(),n.insertBefore(e,t))})},detach:function(e){return this.remove(e,!0)},domManip:function(e,n,r){e=f.apply([],e);var i,o,a,s,u,l,c=0,p=this.length,d=this,h=p-1,g=e[0],m=b.isFunction(g);if(m||!(1>=p||"string"!=typeof g||b.support.checkClone)&&Ct.test(g))return this.each(function(i){var o=d.eq(i);m&&(e[0]=g.call(this,i,n?o.html():t)),o.domManip(e,n,r)});if(p&&(l=b.buildFragment(e,this[0].ownerDocument,!1,this),i=l.firstChild,1===l.childNodes.length&&(l=i),i)){for(n=n&&b.nodeName(i,"tr"),s=b.map(Ot(l,"script"),Ht),a=s.length;p>c;c++)o=l,c!==h&&(o=b.clone(o,!0,!0),a&&b.merge(s,Ot(o,"script"))),r.call(n&&b.nodeName(this[c],"table")?Lt(this[c],"tbody"):this[c],o,c);if(a)for(u=s[s.length-1].ownerDocument,b.map(s,qt),c=0;a>c;c++)o=s[c],kt.test(o.type||"")&&!b._data(o,"globalEval")&&b.contains(u,o)&&(o.src?b.ajax({url:o.src,type:"GET",dataType:"script",async:!1,global:!1,"throws":!0}):b.globalEval((o.text||o.textContent||o.innerHTML||"").replace(St,"")));l=i=null}return this}});function Lt(e,t){return e.getElementsByTagName(t)[0]||e.appendChild(e.ownerDocument.createElement(t))}function Ht(e){var t=e.getAttributeNode("type");return e.type=(t&&t.specified)+"/"+e.type,e}function qt(e){var t=Et.exec(e.type);return t?e.type=t[1]:e.removeAttribute("type"),e}function Mt(e,t){var n,r=0;for(;null!=(n=e[r]);r++)b._data(n,"globalEval",!t||b._data(t[r],"globalEval"))}function _t(e,t){if(1===t.nodeType&&b.hasData(e)){var n,r,i,o=b._data(e),a=b._data(t,o),s=o.events;if(s){delete a.handle,a.events={};for(n in s)for(r=0,i=s[n].length;i>r;r++)b.event.add(t,n,s[n][r])}a.data&&(a.data=b.extend({},a.data))}}function Ft(e,t){var n,r,i;if(1===t.nodeType){if(n=t.nodeName.toLowerCase(),!b.support.noCloneEvent&&t[b.expando]){i=b._data(t);for(r in i.events)b.removeEvent(t,r,i.handle);t.removeAttribute(b.expando)}"script"===n&&t.text!==e.text?(Ht(t).text=e.text,qt(t)):"object"===n?(t.parentNode&&(t.outerHTML=e.outerHTML),b.support.html5Clone&&e.innerHTML&&!b.trim(t.innerHTML)&&(t.innerHTML=e.innerHTML)):"input"===n&&Nt.test(e.type)?(t.defaultChecked=t.checked=e.checked,t.value!==e.value&&(t.value=e.value)):"option"===n?t.defaultSelected=t.selected=e.defaultSelected:("input"===n||"textarea"===n)&&(t.defaultValue=e.defaultValue)}}b.each({appendTo:"append",prependTo:"prepend",insertBefore:"before",insertAfter:"after",replaceAll:"replaceWith"},function(e,t){b.fn[e]=function(e){var n,r=0,i=[],o=b(e),a=o.length-1;for(;a>=r;r++)n=r===a?this:this.clone(!0),b(o[r])[t](n),d.apply(i,n.get());return this.pushStack(i)}});function Ot(e,n){var r,o,a=0,s=typeof e.getElementsByTagName!==i?e.getElementsByTagName(n||"*"):typeof e.querySelectorAll!==i?e.querySelectorAll(n||"*"):t;if(!s)for(s=[],r=e.childNodes||e;null!=(o=r[a]);a++)!n||b.nodeName(o,n)?s.push(o):b.merge(s,Ot(o,n));return n===t||n&&b.nodeName(e,n)?b.merge([e],s):s}function Bt(e){Nt.test(e.type)&&(e.defaultChecked=e.checked)}b.extend({clone:function(e,t,n){var r,i,o,a,s,u=b.contains(e.ownerDocument,e);if(b.support.html5Clone||b.isXMLDoc(e)||!mt.test("<"+e.nodeName+">")?o=e.cloneNode(!0):(Dt.innerHTML=e.outerHTML,Dt.removeChild(o=Dt.firstChild)),!(b.support.noCloneEvent&&b.support.noCloneChecked||1!==e.nodeType&&11!==e.nodeType||b.isXMLDoc(e)))for(r=Ot(o),s=Ot(e),a=0;null!=(i=s[a]);++a)r[a]&&Ft(i,r[a]);if(t)if(n)for(s=s||Ot(e),r=r||Ot(o),a=0;null!=(i=s[a]);a++)_t(i,r[a]);else _t(e,o);return r=Ot(o,"script"),r.length>0&&Mt(r,!u&&Ot(e,"script")),r=s=i=null,o},buildFragment:function(e,t,n,r){var i,o,a,s,u,l,c,p=e.length,f=dt(t),d=[],h=0;for(;p>h;h++)if(o=e[h],o||0===o)if("object"===b.type(o))b.merge(d,o.nodeType?[o]:o);else if(wt.test(o)){s=s||f.appendChild(t.createElement("div")),u=(bt.exec(o)||["",""])[1].toLowerCase(),c=At[u]||At._default,s.innerHTML=c[1]+o.replace(vt,"<$1></$2>")+c[2],i=c[0];while(i--)s=s.lastChild;if(!b.support.leadingWhitespace&&yt.test(o)&&d.push(t.createTextNode(yt.exec(o)[0])),!b.support.tbody){o="table"!==u||xt.test(o)?"<table>"!==c[1]||xt.test(o)?0:s:s.firstChild,i=o&&o.childNodes.length;while(i--)b.nodeName(l=o.childNodes[i],"tbody")&&!l.childNodes.length&&o.removeChild(l)
}b.merge(d,s.childNodes),s.textContent="";while(s.firstChild)s.removeChild(s.firstChild);s=f.lastChild}else d.push(t.createTextNode(o));s&&f.removeChild(s),b.support.appendChecked||b.grep(Ot(d,"input"),Bt),h=0;while(o=d[h++])if((!r||-1===b.inArray(o,r))&&(a=b.contains(o.ownerDocument,o),s=Ot(f.appendChild(o),"script"),a&&Mt(s),n)){i=0;while(o=s[i++])kt.test(o.type||"")&&n.push(o)}return s=null,f},cleanData:function(e,t){var n,r,o,a,s=0,u=b.expando,l=b.cache,p=b.support.deleteExpando,f=b.event.special;for(;null!=(n=e[s]);s++)if((t||b.acceptData(n))&&(o=n[u],a=o&&l[o])){if(a.events)for(r in a.events)f[r]?b.event.remove(n,r):b.removeEvent(n,r,a.handle);l[o]&&(delete l[o],p?delete n[u]:typeof n.removeAttribute!==i?n.removeAttribute(u):n[u]=null,c.push(o))}}});var Pt,Rt,Wt,$t=/alpha\([^)]*\)/i,It=/opacity\s*=\s*([^)]*)/,zt=/^(top|right|bottom|left)$/,Xt=/^(none|table(?!-c[ea]).+)/,Ut=/^margin/,Vt=RegExp("^("+x+")(.*)$","i"),Yt=RegExp("^("+x+")(?!px)[a-z%]+$","i"),Jt=RegExp("^([+-])=("+x+")","i"),Gt={BODY:"block"},Qt={position:"absolute",visibility:"hidden",display:"block"},Kt={letterSpacing:0,fontWeight:400},Zt=["Top","Right","Bottom","Left"],en=["Webkit","O","Moz","ms"];function tn(e,t){if(t in e)return t;var n=t.charAt(0).toUpperCase()+t.slice(1),r=t,i=en.length;while(i--)if(t=en[i]+n,t in e)return t;return r}function nn(e,t){return e=t||e,"none"===b.css(e,"display")||!b.contains(e.ownerDocument,e)}function rn(e,t){var n,r,i,o=[],a=0,s=e.length;for(;s>a;a++)r=e[a],r.style&&(o[a]=b._data(r,"olddisplay"),n=r.style.display,t?(o[a]||"none"!==n||(r.style.display=""),""===r.style.display&&nn(r)&&(o[a]=b._data(r,"olddisplay",un(r.nodeName)))):o[a]||(i=nn(r),(n&&"none"!==n||!i)&&b._data(r,"olddisplay",i?n:b.css(r,"display"))));for(a=0;s>a;a++)r=e[a],r.style&&(t&&"none"!==r.style.display&&""!==r.style.display||(r.style.display=t?o[a]||"":"none"));return e}b.fn.extend({css:function(e,n){return b.access(this,function(e,n,r){var i,o,a={},s=0;if(b.isArray(n)){for(o=Rt(e),i=n.length;i>s;s++)a[n[s]]=b.css(e,n[s],!1,o);return a}return r!==t?b.style(e,n,r):b.css(e,n)},e,n,arguments.length>1)},show:function(){return rn(this,!0)},hide:function(){return rn(this)},toggle:function(e){var t="boolean"==typeof e;return this.each(function(){(t?e:nn(this))?b(this).show():b(this).hide()})}}),b.extend({cssHooks:{opacity:{get:function(e,t){if(t){var n=Wt(e,"opacity");return""===n?"1":n}}}},cssNumber:{columnCount:!0,fillOpacity:!0,fontWeight:!0,lineHeight:!0,opacity:!0,orphans:!0,widows:!0,zIndex:!0,zoom:!0},cssProps:{"float":b.support.cssFloat?"cssFloat":"styleFloat"},style:function(e,n,r,i){if(e&&3!==e.nodeType&&8!==e.nodeType&&e.style){var o,a,s,u=b.camelCase(n),l=e.style;if(n=b.cssProps[u]||(b.cssProps[u]=tn(l,u)),s=b.cssHooks[n]||b.cssHooks[u],r===t)return s&&"get"in s&&(o=s.get(e,!1,i))!==t?o:l[n];if(a=typeof r,"string"===a&&(o=Jt.exec(r))&&(r=(o[1]+1)*o[2]+parseFloat(b.css(e,n)),a="number"),!(null==r||"number"===a&&isNaN(r)||("number"!==a||b.cssNumber[u]||(r+="px"),b.support.clearCloneStyle||""!==r||0!==n.indexOf("background")||(l[n]="inherit"),s&&"set"in s&&(r=s.set(e,r,i))===t)))try{l[n]=r}catch(c){}}},css:function(e,n,r,i){var o,a,s,u=b.camelCase(n);return n=b.cssProps[u]||(b.cssProps[u]=tn(e.style,u)),s=b.cssHooks[n]||b.cssHooks[u],s&&"get"in s&&(a=s.get(e,!0,r)),a===t&&(a=Wt(e,n,i)),"normal"===a&&n in Kt&&(a=Kt[n]),""===r||r?(o=parseFloat(a),r===!0||b.isNumeric(o)?o||0:a):a},swap:function(e,t,n,r){var i,o,a={};for(o in t)a[o]=e.style[o],e.style[o]=t[o];i=n.apply(e,r||[]);for(o in t)e.style[o]=a[o];return i}}),e.getComputedStyle?(Rt=function(t){return e.getComputedStyle(t,null)},Wt=function(e,n,r){var i,o,a,s=r||Rt(e),u=s?s.getPropertyValue(n)||s[n]:t,l=e.style;return s&&(""!==u||b.contains(e.ownerDocument,e)||(u=b.style(e,n)),Yt.test(u)&&Ut.test(n)&&(i=l.width,o=l.minWidth,a=l.maxWidth,l.minWidth=l.maxWidth=l.width=u,u=s.width,l.width=i,l.minWidth=o,l.maxWidth=a)),u}):o.documentElement.currentStyle&&(Rt=function(e){return e.currentStyle},Wt=function(e,n,r){var i,o,a,s=r||Rt(e),u=s?s[n]:t,l=e.style;return null==u&&l&&l[n]&&(u=l[n]),Yt.test(u)&&!zt.test(n)&&(i=l.left,o=e.runtimeStyle,a=o&&o.left,a&&(o.left=e.currentStyle.left),l.left="fontSize"===n?"1em":u,u=l.pixelLeft+"px",l.left=i,a&&(o.left=a)),""===u?"auto":u});function on(e,t,n){var r=Vt.exec(t);return r?Math.max(0,r[1]-(n||0))+(r[2]||"px"):t}function an(e,t,n,r,i){var o=n===(r?"border":"content")?4:"width"===t?1:0,a=0;for(;4>o;o+=2)"margin"===n&&(a+=b.css(e,n+Zt[o],!0,i)),r?("content"===n&&(a-=b.css(e,"padding"+Zt[o],!0,i)),"margin"!==n&&(a-=b.css(e,"border"+Zt[o]+"Width",!0,i))):(a+=b.css(e,"padding"+Zt[o],!0,i),"padding"!==n&&(a+=b.css(e,"border"+Zt[o]+"Width",!0,i)));return a}function sn(e,t,n){var r=!0,i="width"===t?e.offsetWidth:e.offsetHeight,o=Rt(e),a=b.support.boxSizing&&"border-box"===b.css(e,"boxSizing",!1,o);if(0>=i||null==i){if(i=Wt(e,t,o),(0>i||null==i)&&(i=e.style[t]),Yt.test(i))return i;r=a&&(b.support.boxSizingReliable||i===e.style[t]),i=parseFloat(i)||0}return i+an(e,t,n||(a?"border":"content"),r,o)+"px"}function un(e){var t=o,n=Gt[e];return n||(n=ln(e,t),"none"!==n&&n||(Pt=(Pt||b("<iframe frameborder='0' width='0' height='0'/>").css("cssText","display:block !important")).appendTo(t.documentElement),t=(Pt[0].contentWindow||Pt[0].contentDocument).document,t.write("<!doctype html><html><body>"),t.close(),n=ln(e,t),Pt.detach()),Gt[e]=n),n}function ln(e,t){var n=b(t.createElement(e)).appendTo(t.body),r=b.css(n[0],"display");return n.remove(),r}b.each(["height","width"],function(e,n){b.cssHooks[n]={get:function(e,r,i){return r?0===e.offsetWidth&&Xt.test(b.css(e,"display"))?b.swap(e,Qt,function(){return sn(e,n,i)}):sn(e,n,i):t},set:function(e,t,r){var i=r&&Rt(e);return on(e,t,r?an(e,n,r,b.support.boxSizing&&"border-box"===b.css(e,"boxSizing",!1,i),i):0)}}}),b.support.opacity||(b.cssHooks.opacity={get:function(e,t){return It.test((t&&e.currentStyle?e.currentStyle.filter:e.style.filter)||"")?.01*parseFloat(RegExp.$1)+"":t?"1":""},set:function(e,t){var n=e.style,r=e.currentStyle,i=b.isNumeric(t)?"alpha(opacity="+100*t+")":"",o=r&&r.filter||n.filter||"";n.zoom=1,(t>=1||""===t)&&""===b.trim(o.replace($t,""))&&n.removeAttribute&&(n.removeAttribute("filter"),""===t||r&&!r.filter)||(n.filter=$t.test(o)?o.replace($t,i):o+" "+i)}}),b(function(){b.support.reliableMarginRight||(b.cssHooks.marginRight={get:function(e,n){return n?b.swap(e,{display:"inline-block"},Wt,[e,"marginRight"]):t}}),!b.support.pixelPosition&&b.fn.position&&b.each(["top","left"],function(e,n){b.cssHooks[n]={get:function(e,r){return r?(r=Wt(e,n),Yt.test(r)?b(e).position()[n]+"px":r):t}}})}),b.expr&&b.expr.filters&&(b.expr.filters.hidden=function(e){return 0>=e.offsetWidth&&0>=e.offsetHeight||!b.support.reliableHiddenOffsets&&"none"===(e.style&&e.style.display||b.css(e,"display"))},b.expr.filters.visible=function(e){return!b.expr.filters.hidden(e)}),b.each({margin:"",padding:"",border:"Width"},function(e,t){b.cssHooks[e+t]={expand:function(n){var r=0,i={},o="string"==typeof n?n.split(" "):[n];for(;4>r;r++)i[e+Zt[r]+t]=o[r]||o[r-2]||o[0];return i}},Ut.test(e)||(b.cssHooks[e+t].set=on)});var cn=/%20/g,pn=/\[\]$/,fn=/\r?\n/g,dn=/^(?:submit|button|image|reset|file)$/i,hn=/^(?:input|select|textarea|keygen)/i;b.fn.extend({serialize:function(){return b.param(this.serializeArray())},serializeArray:function(){return this.map(function(){var e=b.prop(this,"elements");return e?b.makeArray(e):this}).filter(function(){var e=this.type;return this.name&&!b(this).is(":disabled")&&hn.test(this.nodeName)&&!dn.test(e)&&(this.checked||!Nt.test(e))}).map(function(e,t){var n=b(this).val();return null==n?null:b.isArray(n)?b.map(n,function(e){return{name:t.name,value:e.replace(fn,"\r\n")}}):{name:t.name,value:n.replace(fn,"\r\n")}}).get()}}),b.param=function(e,n){var r,i=[],o=function(e,t){t=b.isFunction(t)?t():null==t?"":t,i[i.length]=encodeURIComponent(e)+"="+encodeURIComponent(t)};if(n===t&&(n=b.ajaxSettings&&b.ajaxSettings.traditional),b.isArray(e)||e.jquery&&!b.isPlainObject(e))b.each(e,function(){o(this.name,this.value)});else for(r in e)gn(r,e[r],n,o);return i.join("&").replace(cn,"+")};function gn(e,t,n,r){var i;if(b.isArray(t))b.each(t,function(t,i){n||pn.test(e)?r(e,i):gn(e+"["+("object"==typeof i?t:"")+"]",i,n,r)});else if(n||"object"!==b.type(t))r(e,t);else for(i in t)gn(e+"["+i+"]",t[i],n,r)}b.each("blur focus focusin focusout load resize scroll unload click dblclick mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave change select submit keydown keypress keyup error contextmenu".split(" "),function(e,t){b.fn[t]=function(e,n){return arguments.length>0?this.on(t,null,e,n):this.trigger(t)}}),b.fn.hover=function(e,t){return this.mouseenter(e).mouseleave(t||e)};var mn,yn,vn=b.now(),bn=/\?/,xn=/#.*$/,wn=/([?&])_=[^&]*/,Tn=/^(.*?):[ \t]*([^\r\n]*)\r?$/gm,Nn=/^(?:about|app|app-storage|.+-extension|file|res|widget):$/,Cn=/^(?:GET|HEAD)$/,kn=/^\/\//,En=/^([\w.+-]+:)(?:\/\/([^\/?#:]*)(?::(\d+)|)|)/,Sn=b.fn.load,An={},jn={},Dn="*/".concat("*");try{yn=a.href}catch(Ln){yn=o.createElement("a"),yn.href="",yn=yn.href}mn=En.exec(yn.toLowerCase())||[];function Hn(e){return function(t,n){"string"!=typeof t&&(n=t,t="*");var r,i=0,o=t.toLowerCase().match(w)||[];if(b.isFunction(n))while(r=o[i++])"+"===r[0]?(r=r.slice(1)||"*",(e[r]=e[r]||[]).unshift(n)):(e[r]=e[r]||[]).push(n)}}function qn(e,n,r,i){var o={},a=e===jn;function s(u){var l;return o[u]=!0,b.each(e[u]||[],function(e,u){var c=u(n,r,i);return"string"!=typeof c||a||o[c]?a?!(l=c):t:(n.dataTypes.unshift(c),s(c),!1)}),l}return s(n.dataTypes[0])||!o["*"]&&s("*")}function Mn(e,n){var r,i,o=b.ajaxSettings.flatOptions||{};for(i in n)n[i]!==t&&((o[i]?e:r||(r={}))[i]=n[i]);return r&&b.extend(!0,e,r),e}b.fn.load=function(e,n,r){if("string"!=typeof e&&Sn)return Sn.apply(this,arguments);var i,o,a,s=this,u=e.indexOf(" ");return u>=0&&(i=e.slice(u,e.length),e=e.slice(0,u)),b.isFunction(n)?(r=n,n=t):n&&"object"==typeof n&&(a="POST"),s.length>0&&b.ajax({url:e,type:a,dataType:"html",data:n}).done(function(e){o=arguments,s.html(i?b("<div>").append(b.parseHTML(e)).find(i):e)}).complete(r&&function(e,t){s.each(r,o||[e.responseText,t,e])}),this},b.each(["ajaxStart","ajaxStop","ajaxComplete","ajaxError","ajaxSuccess","ajaxSend"],function(e,t){b.fn[t]=function(e){return this.on(t,e)}}),b.each(["get","post"],function(e,n){b[n]=function(e,r,i,o){return b.isFunction(r)&&(o=o||i,i=r,r=t),b.ajax({url:e,type:n,dataType:o,data:r,success:i})}}),b.extend({active:0,lastModified:{},etag:{},ajaxSettings:{url:yn,type:"GET",isLocal:Nn.test(mn[1]),global:!0,processData:!0,async:!0,contentType:"application/x-www-form-urlencoded; charset=UTF-8",accepts:{"*":Dn,text:"text/plain",html:"text/html",xml:"application/xml, text/xml",json:"application/json, text/javascript"},contents:{xml:/xml/,html:/html/,json:/json/},responseFields:{xml:"responseXML",text:"responseText"},converters:{"* text":e.String,"text html":!0,"text json":b.parseJSON,"text xml":b.parseXML},flatOptions:{url:!0,context:!0}},ajaxSetup:function(e,t){return t?Mn(Mn(e,b.ajaxSettings),t):Mn(b.ajaxSettings,e)},ajaxPrefilter:Hn(An),ajaxTransport:Hn(jn),ajax:function(e,n){"object"==typeof e&&(n=e,e=t),n=n||{};var r,i,o,a,s,u,l,c,p=b.ajaxSetup({},n),f=p.context||p,d=p.context&&(f.nodeType||f.jquery)?b(f):b.event,h=b.Deferred(),g=b.Callbacks("once memory"),m=p.statusCode||{},y={},v={},x=0,T="canceled",N={readyState:0,getResponseHeader:function(e){var t;if(2===x){if(!c){c={};while(t=Tn.exec(a))c[t[1].toLowerCase()]=t[2]}t=c[e.toLowerCase()]}return null==t?null:t},getAllResponseHeaders:function(){return 2===x?a:null},setRequestHeader:function(e,t){var n=e.toLowerCase();return x||(e=v[n]=v[n]||e,y[e]=t),this},overrideMimeType:function(e){return x||(p.mimeType=e),this},statusCode:function(e){var t;if(e)if(2>x)for(t in e)m[t]=[m[t],e[t]];else N.always(e[N.status]);return this},abort:function(e){var t=e||T;return l&&l.abort(t),k(0,t),this}};if(h.promise(N).complete=g.add,N.success=N.done,N.error=N.fail,p.url=((e||p.url||yn)+"").replace(xn,"").replace(kn,mn[1]+"//"),p.type=n.method||n.type||p.method||p.type,p.dataTypes=b.trim(p.dataType||"*").toLowerCase().match(w)||[""],null==p.crossDomain&&(r=En.exec(p.url.toLowerCase()),p.crossDomain=!(!r||r[1]===mn[1]&&r[2]===mn[2]&&(r[3]||("http:"===r[1]?80:443))==(mn[3]||("http:"===mn[1]?80:443)))),p.data&&p.processData&&"string"!=typeof p.data&&(p.data=b.param(p.data,p.traditional)),qn(An,p,n,N),2===x)return N;u=p.global,u&&0===b.active++&&b.event.trigger("ajaxStart"),p.type=p.type.toUpperCase(),p.hasContent=!Cn.test(p.type),o=p.url,p.hasContent||(p.data&&(o=p.url+=(bn.test(o)?"&":"?")+p.data,delete p.data),p.cache===!1&&(p.url=wn.test(o)?o.replace(wn,"$1_="+vn++):o+(bn.test(o)?"&":"?")+"_="+vn++)),p.ifModified&&(b.lastModified[o]&&N.setRequestHeader("If-Modified-Since",b.lastModified[o]),b.etag[o]&&N.setRequestHeader("If-None-Match",b.etag[o])),(p.data&&p.hasContent&&p.contentType!==!1||n.contentType)&&N.setRequestHeader("Content-Type",p.contentType),N.setRequestHeader("Accept",p.dataTypes[0]&&p.accepts[p.dataTypes[0]]?p.accepts[p.dataTypes[0]]+("*"!==p.dataTypes[0]?", "+Dn+"; q=0.01":""):p.accepts["*"]);for(i in p.headers)N.setRequestHeader(i,p.headers[i]);if(p.beforeSend&&(p.beforeSend.call(f,N,p)===!1||2===x))return N.abort();T="abort";for(i in{success:1,error:1,complete:1})N[i](p[i]);if(l=qn(jn,p,n,N)){N.readyState=1,u&&d.trigger("ajaxSend",[N,p]),p.async&&p.timeout>0&&(s=setTimeout(function(){N.abort("timeout")},p.timeout));try{x=1,l.send(y,k)}catch(C){if(!(2>x))throw C;k(-1,C)}}else k(-1,"No Transport");function k(e,n,r,i){var c,y,v,w,T,C=n;2!==x&&(x=2,s&&clearTimeout(s),l=t,a=i||"",N.readyState=e>0?4:0,r&&(w=_n(p,N,r)),e>=200&&300>e||304===e?(p.ifModified&&(T=N.getResponseHeader("Last-Modified"),T&&(b.lastModified[o]=T),T=N.getResponseHeader("etag"),T&&(b.etag[o]=T)),204===e?(c=!0,C="nocontent"):304===e?(c=!0,C="notmodified"):(c=Fn(p,w),C=c.state,y=c.data,v=c.error,c=!v)):(v=C,(e||!C)&&(C="error",0>e&&(e=0))),N.status=e,N.statusText=(n||C)+"",c?h.resolveWith(f,[y,C,N]):h.rejectWith(f,[N,C,v]),N.statusCode(m),m=t,u&&d.trigger(c?"ajaxSuccess":"ajaxError",[N,p,c?y:v]),g.fireWith(f,[N,C]),u&&(d.trigger("ajaxComplete",[N,p]),--b.active||b.event.trigger("ajaxStop")))}return N},getScript:function(e,n){return b.get(e,t,n,"script")},getJSON:function(e,t,n){return b.get(e,t,n,"json")}});function _n(e,n,r){var i,o,a,s,u=e.contents,l=e.dataTypes,c=e.responseFields;for(s in c)s in r&&(n[c[s]]=r[s]);while("*"===l[0])l.shift(),o===t&&(o=e.mimeType||n.getResponseHeader("Content-Type"));if(o)for(s in u)if(u[s]&&u[s].test(o)){l.unshift(s);break}if(l[0]in r)a=l[0];else{for(s in r){if(!l[0]||e.converters[s+" "+l[0]]){a=s;break}i||(i=s)}a=a||i}return a?(a!==l[0]&&l.unshift(a),r[a]):t}function Fn(e,t){var n,r,i,o,a={},s=0,u=e.dataTypes.slice(),l=u[0];if(e.dataFilter&&(t=e.dataFilter(t,e.dataType)),u[1])for(i in e.converters)a[i.toLowerCase()]=e.converters[i];for(;r=u[++s];)if("*"!==r){if("*"!==l&&l!==r){if(i=a[l+" "+r]||a["* "+r],!i)for(n in a)if(o=n.split(" "),o[1]===r&&(i=a[l+" "+o[0]]||a["* "+o[0]])){i===!0?i=a[n]:a[n]!==!0&&(r=o[0],u.splice(s--,0,r));break}if(i!==!0)if(i&&e["throws"])t=i(t);else try{t=i(t)}catch(c){return{state:"parsererror",error:i?c:"No conversion from "+l+" to "+r}}}l=r}return{state:"success",data:t}}b.ajaxSetup({accepts:{script:"text/javascript, application/javascript, application/ecmascript, application/x-ecmascript"},contents:{script:/(?:java|ecma)script/},converters:{"text script":function(e){return b.globalEval(e),e}}}),b.ajaxPrefilter("script",function(e){e.cache===t&&(e.cache=!1),e.crossDomain&&(e.type="GET",e.global=!1)}),b.ajaxTransport("script",function(e){if(e.crossDomain){var n,r=o.head||b("head")[0]||o.documentElement;return{send:function(t,i){n=o.createElement("script"),n.async=!0,e.scriptCharset&&(n.charset=e.scriptCharset),n.src=e.url,n.onload=n.onreadystatechange=function(e,t){(t||!n.readyState||/loaded|complete/.test(n.readyState))&&(n.onload=n.onreadystatechange=null,n.parentNode&&n.parentNode.removeChild(n),n=null,t||i(200,"success"))},r.insertBefore(n,r.firstChild)},abort:function(){n&&n.onload(t,!0)}}}});var On=[],Bn=/(=)\?(?=&|$)|\?\?/;b.ajaxSetup({jsonp:"callback",jsonpCallback:function(){var e=On.pop()||b.expando+"_"+vn++;return this[e]=!0,e}}),b.ajaxPrefilter("json jsonp",function(n,r,i){var o,a,s,u=n.jsonp!==!1&&(Bn.test(n.url)?"url":"string"==typeof n.data&&!(n.contentType||"").indexOf("application/x-www-form-urlencoded")&&Bn.test(n.data)&&"data");return u||"jsonp"===n.dataTypes[0]?(o=n.jsonpCallback=b.isFunction(n.jsonpCallback)?n.jsonpCallback():n.jsonpCallback,u?n[u]=n[u].replace(Bn,"$1"+o):n.jsonp!==!1&&(n.url+=(bn.test(n.url)?"&":"?")+n.jsonp+"="+o),n.converters["script json"]=function(){return s||b.error(o+" was not called"),s[0]},n.dataTypes[0]="json",a=e[o],e[o]=function(){s=arguments},i.always(function(){e[o]=a,n[o]&&(n.jsonpCallback=r.jsonpCallback,On.push(o)),s&&b.isFunction(a)&&a(s[0]),s=a=t}),"script"):t});var Pn,Rn,Wn=0,$n=e.ActiveXObject&&function(){var e;for(e in Pn)Pn[e](t,!0)};function In(){try{return new e.XMLHttpRequest}catch(t){}}function zn(){try{return new e.ActiveXObject("Microsoft.XMLHTTP")}catch(t){}}b.ajaxSettings.xhr=e.ActiveXObject?function(){return!this.isLocal&&In()||zn()}:In,Rn=b.ajaxSettings.xhr(),b.support.cors=!!Rn&&"withCredentials"in Rn,Rn=b.support.ajax=!!Rn,Rn&&b.ajaxTransport(function(n){if(!n.crossDomain||b.support.cors){var r;return{send:function(i,o){var a,s,u=n.xhr();if(n.username?u.open(n.type,n.url,n.async,n.username,n.password):u.open(n.type,n.url,n.async),n.xhrFields)for(s in n.xhrFields)u[s]=n.xhrFields[s];n.mimeType&&u.overrideMimeType&&u.overrideMimeType(n.mimeType),n.crossDomain||i["X-Requested-With"]||(i["X-Requested-With"]="XMLHttpRequest");try{for(s in i)u.setRequestHeader(s,i[s])}catch(l){}u.send(n.hasContent&&n.data||null),r=function(e,i){var s,l,c,p;try{if(r&&(i||4===u.readyState))if(r=t,a&&(u.onreadystatechange=b.noop,$n&&delete Pn[a]),i)4!==u.readyState&&u.abort();else{p={},s=u.status,l=u.getAllResponseHeaders(),"string"==typeof u.responseText&&(p.text=u.responseText);try{c=u.statusText}catch(f){c=""}s||!n.isLocal||n.crossDomain?1223===s&&(s=204):s=p.text?200:404}}catch(d){i||o(-1,d)}p&&o(s,c,p,l)},n.async?4===u.readyState?setTimeout(r):(a=++Wn,$n&&(Pn||(Pn={},b(e).unload($n)),Pn[a]=r),u.onreadystatechange=r):r()},abort:function(){r&&r(t,!0)}}}});var Xn,Un,Vn=/^(?:toggle|show|hide)$/,Yn=RegExp("^(?:([+-])=|)("+x+")([a-z%]*)$","i"),Jn=/queueHooks$/,Gn=[nr],Qn={"*":[function(e,t){var n,r,i=this.createTween(e,t),o=Yn.exec(t),a=i.cur(),s=+a||0,u=1,l=20;if(o){if(n=+o[2],r=o[3]||(b.cssNumber[e]?"":"px"),"px"!==r&&s){s=b.css(i.elem,e,!0)||n||1;do u=u||".5",s/=u,b.style(i.elem,e,s+r);while(u!==(u=i.cur()/a)&&1!==u&&--l)}i.unit=r,i.start=s,i.end=o[1]?s+(o[1]+1)*n:n}return i}]};function Kn(){return setTimeout(function(){Xn=t}),Xn=b.now()}function Zn(e,t){b.each(t,function(t,n){var r=(Qn[t]||[]).concat(Qn["*"]),i=0,o=r.length;for(;o>i;i++)if(r[i].call(e,t,n))return})}function er(e,t,n){var r,i,o=0,a=Gn.length,s=b.Deferred().always(function(){delete u.elem}),u=function(){if(i)return!1;var t=Xn||Kn(),n=Math.max(0,l.startTime+l.duration-t),r=n/l.duration||0,o=1-r,a=0,u=l.tweens.length;for(;u>a;a++)l.tweens[a].run(o);return s.notifyWith(e,[l,o,n]),1>o&&u?n:(s.resolveWith(e,[l]),!1)},l=s.promise({elem:e,props:b.extend({},t),opts:b.extend(!0,{specialEasing:{}},n),originalProperties:t,originalOptions:n,startTime:Xn||Kn(),duration:n.duration,tweens:[],createTween:function(t,n){var r=b.Tween(e,l.opts,t,n,l.opts.specialEasing[t]||l.opts.easing);return l.tweens.push(r),r},stop:function(t){var n=0,r=t?l.tweens.length:0;if(i)return this;for(i=!0;r>n;n++)l.tweens[n].run(1);return t?s.resolveWith(e,[l,t]):s.rejectWith(e,[l,t]),this}}),c=l.props;for(tr(c,l.opts.specialEasing);a>o;o++)if(r=Gn[o].call(l,e,c,l.opts))return r;return Zn(l,c),b.isFunction(l.opts.start)&&l.opts.start.call(e,l),b.fx.timer(b.extend(u,{elem:e,anim:l,queue:l.opts.queue})),l.progress(l.opts.progress).done(l.opts.done,l.opts.complete).fail(l.opts.fail).always(l.opts.always)}function tr(e,t){var n,r,i,o,a;for(i in e)if(r=b.camelCase(i),o=t[r],n=e[i],b.isArray(n)&&(o=n[1],n=e[i]=n[0]),i!==r&&(e[r]=n,delete e[i]),a=b.cssHooks[r],a&&"expand"in a){n=a.expand(n),delete e[r];for(i in n)i in e||(e[i]=n[i],t[i]=o)}else t[r]=o}b.Animation=b.extend(er,{tweener:function(e,t){b.isFunction(e)?(t=e,e=["*"]):e=e.split(" ");var n,r=0,i=e.length;for(;i>r;r++)n=e[r],Qn[n]=Qn[n]||[],Qn[n].unshift(t)},prefilter:function(e,t){t?Gn.unshift(e):Gn.push(e)}});function nr(e,t,n){var r,i,o,a,s,u,l,c,p,f=this,d=e.style,h={},g=[],m=e.nodeType&&nn(e);n.queue||(c=b._queueHooks(e,"fx"),null==c.unqueued&&(c.unqueued=0,p=c.empty.fire,c.empty.fire=function(){c.unqueued||p()}),c.unqueued++,f.always(function(){f.always(function(){c.unqueued--,b.queue(e,"fx").length||c.empty.fire()})})),1===e.nodeType&&("height"in t||"width"in t)&&(n.overflow=[d.overflow,d.overflowX,d.overflowY],"inline"===b.css(e,"display")&&"none"===b.css(e,"float")&&(b.support.inlineBlockNeedsLayout&&"inline"!==un(e.nodeName)?d.zoom=1:d.display="inline-block")),n.overflow&&(d.overflow="hidden",b.support.shrinkWrapBlocks||f.always(function(){d.overflow=n.overflow[0],d.overflowX=n.overflow[1],d.overflowY=n.overflow[2]}));for(i in t)if(a=t[i],Vn.exec(a)){if(delete t[i],u=u||"toggle"===a,a===(m?"hide":"show"))continue;g.push(i)}if(o=g.length){s=b._data(e,"fxshow")||b._data(e,"fxshow",{}),"hidden"in s&&(m=s.hidden),u&&(s.hidden=!m),m?b(e).show():f.done(function(){b(e).hide()}),f.done(function(){var t;b._removeData(e,"fxshow");for(t in h)b.style(e,t,h[t])});for(i=0;o>i;i++)r=g[i],l=f.createTween(r,m?s[r]:0),h[r]=s[r]||b.style(e,r),r in s||(s[r]=l.start,m&&(l.end=l.start,l.start="width"===r||"height"===r?1:0))}}function rr(e,t,n,r,i){return new rr.prototype.init(e,t,n,r,i)}b.Tween=rr,rr.prototype={constructor:rr,init:function(e,t,n,r,i,o){this.elem=e,this.prop=n,this.easing=i||"swing",this.options=t,this.start=this.now=this.cur(),this.end=r,this.unit=o||(b.cssNumber[n]?"":"px")},cur:function(){var e=rr.propHooks[this.prop];return e&&e.get?e.get(this):rr.propHooks._default.get(this)},run:function(e){var t,n=rr.propHooks[this.prop];return this.pos=t=this.options.duration?b.easing[this.easing](e,this.options.duration*e,0,1,this.options.duration):e,this.now=(this.end-this.start)*t+this.start,this.options.step&&this.options.step.call(this.elem,this.now,this),n&&n.set?n.set(this):rr.propHooks._default.set(this),this}},rr.prototype.init.prototype=rr.prototype,rr.propHooks={_default:{get:function(e){var t;return null==e.elem[e.prop]||e.elem.style&&null!=e.elem.style[e.prop]?(t=b.css(e.elem,e.prop,""),t&&"auto"!==t?t:0):e.elem[e.prop]},set:function(e){b.fx.step[e.prop]?b.fx.step[e.prop](e):e.elem.style&&(null!=e.elem.style[b.cssProps[e.prop]]||b.cssHooks[e.prop])?b.style(e.elem,e.prop,e.now+e.unit):e.elem[e.prop]=e.now}}},rr.propHooks.scrollTop=rr.propHooks.scrollLeft={set:function(e){e.elem.nodeType&&e.elem.parentNode&&(e.elem[e.prop]=e.now)}},b.each(["toggle","show","hide"],function(e,t){var n=b.fn[t];b.fn[t]=function(e,r,i){return null==e||"boolean"==typeof e?n.apply(this,arguments):this.animate(ir(t,!0),e,r,i)}}),b.fn.extend({fadeTo:function(e,t,n,r){return this.filter(nn).css("opacity",0).show().end().animate({opacity:t},e,n,r)},animate:function(e,t,n,r){var i=b.isEmptyObject(e),o=b.speed(t,n,r),a=function(){var t=er(this,b.extend({},e),o);a.finish=function(){t.stop(!0)},(i||b._data(this,"finish"))&&t.stop(!0)};return a.finish=a,i||o.queue===!1?this.each(a):this.queue(o.queue,a)},stop:function(e,n,r){var i=function(e){var t=e.stop;delete e.stop,t(r)};return"string"!=typeof e&&(r=n,n=e,e=t),n&&e!==!1&&this.queue(e||"fx",[]),this.each(function(){var t=!0,n=null!=e&&e+"queueHooks",o=b.timers,a=b._data(this);if(n)a[n]&&a[n].stop&&i(a[n]);else for(n in a)a[n]&&a[n].stop&&Jn.test(n)&&i(a[n]);for(n=o.length;n--;)o[n].elem!==this||null!=e&&o[n].queue!==e||(o[n].anim.stop(r),t=!1,o.splice(n,1));(t||!r)&&b.dequeue(this,e)})},finish:function(e){return e!==!1&&(e=e||"fx"),this.each(function(){var t,n=b._data(this),r=n[e+"queue"],i=n[e+"queueHooks"],o=b.timers,a=r?r.length:0;for(n.finish=!0,b.queue(this,e,[]),i&&i.cur&&i.cur.finish&&i.cur.finish.call(this),t=o.length;t--;)o[t].elem===this&&o[t].queue===e&&(o[t].anim.stop(!0),o.splice(t,1));for(t=0;a>t;t++)r[t]&&r[t].finish&&r[t].finish.call(this);delete n.finish})}});function ir(e,t){var n,r={height:e},i=0;for(t=t?1:0;4>i;i+=2-t)n=Zt[i],r["margin"+n]=r["padding"+n]=e;return t&&(r.opacity=r.width=e),r}b.each({slideDown:ir("show"),slideUp:ir("hide"),slideToggle:ir("toggle"),fadeIn:{opacity:"show"},fadeOut:{opacity:"hide"},fadeToggle:{opacity:"toggle"}},function(e,t){b.fn[e]=function(e,n,r){return this.animate(t,e,n,r)}}),b.speed=function(e,t,n){var r=e&&"object"==typeof e?b.extend({},e):{complete:n||!n&&t||b.isFunction(e)&&e,duration:e,easing:n&&t||t&&!b.isFunction(t)&&t};return r.duration=b.fx.off?0:"number"==typeof r.duration?r.duration:r.duration in b.fx.speeds?b.fx.speeds[r.duration]:b.fx.speeds._default,(null==r.queue||r.queue===!0)&&(r.queue="fx"),r.old=r.complete,r.complete=function(){b.isFunction(r.old)&&r.old.call(this),r.queue&&b.dequeue(this,r.queue)},r},b.easing={linear:function(e){return e},swing:function(e){return.5-Math.cos(e*Math.PI)/2}},b.timers=[],b.fx=rr.prototype.init,b.fx.tick=function(){var e,n=b.timers,r=0;for(Xn=b.now();n.length>r;r++)e=n[r],e()||n[r]!==e||n.splice(r--,1);n.length||b.fx.stop(),Xn=t},b.fx.timer=function(e){e()&&b.timers.push(e)&&b.fx.start()},b.fx.interval=13,b.fx.start=function(){Un||(Un=setInterval(b.fx.tick,b.fx.interval))},b.fx.stop=function(){clearInterval(Un),Un=null},b.fx.speeds={slow:600,fast:200,_default:400},b.fx.step={},b.expr&&b.expr.filters&&(b.expr.filters.animated=function(e){return b.grep(b.timers,function(t){return e===t.elem}).length}),b.fn.offset=function(e){if(arguments.length)return e===t?this:this.each(function(t){b.offset.setOffset(this,e,t)});var n,r,o={top:0,left:0},a=this[0],s=a&&a.ownerDocument;if(s)return n=s.documentElement,b.contains(n,a)?(typeof a.getBoundingClientRect!==i&&(o=a.getBoundingClientRect()),r=or(s),{top:o.top+(r.pageYOffset||n.scrollTop)-(n.clientTop||0),left:o.left+(r.pageXOffset||n.scrollLeft)-(n.clientLeft||0)}):o},b.offset={setOffset:function(e,t,n){var r=b.css(e,"position");"static"===r&&(e.style.position="relative");var i=b(e),o=i.offset(),a=b.css(e,"top"),s=b.css(e,"left"),u=("absolute"===r||"fixed"===r)&&b.inArray("auto",[a,s])>-1,l={},c={},p,f;u?(c=i.position(),p=c.top,f=c.left):(p=parseFloat(a)||0,f=parseFloat(s)||0),b.isFunction(t)&&(t=t.call(e,n,o)),null!=t.top&&(l.top=t.top-o.top+p),null!=t.left&&(l.left=t.left-o.left+f),"using"in t?t.using.call(e,l):i.css(l)}},b.fn.extend({position:function(){if(this[0]){var e,t,n={top:0,left:0},r=this[0];return"fixed"===b.css(r,"position")?t=r.getBoundingClientRect():(e=this.offsetParent(),t=this.offset(),b.nodeName(e[0],"html")||(n=e.offset()),n.top+=b.css(e[0],"borderTopWidth",!0),n.left+=b.css(e[0],"borderLeftWidth",!0)),{top:t.top-n.top-b.css(r,"marginTop",!0),left:t.left-n.left-b.css(r,"marginLeft",!0)}}},offsetParent:function(){return this.map(function(){var e=this.offsetParent||o.documentElement;while(e&&!b.nodeName(e,"html")&&"static"===b.css(e,"position"))e=e.offsetParent;return e||o.documentElement})}}),b.each({scrollLeft:"pageXOffset",scrollTop:"pageYOffset"},function(e,n){var r=/Y/.test(n);b.fn[e]=function(i){return b.access(this,function(e,i,o){var a=or(e);return o===t?a?n in a?a[n]:a.document.documentElement[i]:e[i]:(a?a.scrollTo(r?b(a).scrollLeft():o,r?o:b(a).scrollTop()):e[i]=o,t)},e,i,arguments.length,null)}});function or(e){return b.isWindow(e)?e:9===e.nodeType?e.defaultView||e.parentWindow:!1}b.each({Height:"height",Width:"width"},function(e,n){b.each({padding:"inner"+e,content:n,"":"outer"+e},function(r,i){b.fn[i]=function(i,o){var a=arguments.length&&(r||"boolean"!=typeof i),s=r||(i===!0||o===!0?"margin":"border");return b.access(this,function(n,r,i){var o;return b.isWindow(n)?n.document.documentElement["client"+e]:9===n.nodeType?(o=n.documentElement,Math.max(n.body["scroll"+e],o["scroll"+e],n.body["offset"+e],o["offset"+e],o["client"+e])):i===t?b.css(n,r,s):b.style(n,r,i,s)},n,a?i:t,a,null)}})}),e.jQuery=e.$=b,"function"==typeof define&&define.amd&&define.amd.jQuery&&define("jquery",[],function(){return b})})(window);
/* ========================================================================
 * Bootstrap: affix.js v3.3.7
 * http://getbootstrap.com/javascript/#affix
 * ========================================================================
 * Copyright 2011-2016 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // AFFIX CLASS DEFINITION
  // ======================

  var Affix = function (element, options) {
    this.options = $.extend({}, Affix.DEFAULTS, options)

    this.$target = $(this.options.target)
      .on('scroll.bs.affix.data-api', $.proxy(this.checkPosition, this))
      .on('click.bs.affix.data-api',  $.proxy(this.checkPositionWithEventLoop, this))

    this.$element     = $(element)
    this.affixed      = null
    this.unpin        = null
    this.pinnedOffset = null

    this.checkPosition()
  }

  Affix.VERSION  = '3.3.7'

  Affix.RESET    = 'affix affix-top affix-bottom'

  Affix.DEFAULTS = {
    offset: 0,
    target: window
  }

  Affix.prototype.getState = function (scrollHeight, height, offsetTop, offsetBottom) {
    var scrollTop    = this.$target.scrollTop()
    var position     = this.$element.offset()
    var targetHeight = this.$target.height()

    if (offsetTop != null && this.affixed == 'top') return scrollTop < offsetTop ? 'top' : false

    if (this.affixed == 'bottom') {
      if (offsetTop != null) return (scrollTop + this.unpin <= position.top) ? false : 'bottom'
      return (scrollTop + targetHeight <= scrollHeight - offsetBottom) ? false : 'bottom'
    }

    var initializing   = this.affixed == null
    var colliderTop    = initializing ? scrollTop : position.top
    var colliderHeight = initializing ? targetHeight : height

    if (offsetTop != null && scrollTop <= offsetTop) return 'top'
    if (offsetBottom != null && (colliderTop + colliderHeight >= scrollHeight - offsetBottom)) return 'bottom'

    return false
  }

  Affix.prototype.getPinnedOffset = function () {
    if (this.pinnedOffset) return this.pinnedOffset
    this.$element.removeClass(Affix.RESET).addClass('affix')
    var scrollTop = this.$target.scrollTop()
    var position  = this.$element.offset()
    return (this.pinnedOffset = position.top - scrollTop)
  }

  Affix.prototype.checkPositionWithEventLoop = function () {
    setTimeout($.proxy(this.checkPosition, this), 1)
  }

  Affix.prototype.checkPosition = function () {
    if (!this.$element.is(':visible')) return

    var height       = this.$element.height()
    var offset       = this.options.offset
    var offsetTop    = offset.top
    var offsetBottom = offset.bottom
    var scrollHeight = Math.max($(document).height(), $(document.body).height())

    if (typeof offset != 'object')         offsetBottom = offsetTop = offset
    if (typeof offsetTop == 'function')    offsetTop    = offset.top(this.$element)
    if (typeof offsetBottom == 'function') offsetBottom = offset.bottom(this.$element)

    var affix = this.getState(scrollHeight, height, offsetTop, offsetBottom)

    if (this.affixed != affix) {
      if (this.unpin != null) this.$element.css('top', '')

      var affixType = 'affix' + (affix ? '-' + affix : '')
      var e         = $.Event(affixType + '.bs.affix')

      this.$element.trigger(e)

      if (e.isDefaultPrevented()) return

      this.affixed = affix
      this.unpin = affix == 'bottom' ? this.getPinnedOffset() : null

      this.$element
        .removeClass(Affix.RESET)
        .addClass(affixType)
        .trigger(affixType.replace('affix', 'affixed') + '.bs.affix')
    }

    if (affix == 'bottom') {
      this.$element.offset({
        top: scrollHeight - height - offsetBottom
      })
    }
  }


  // AFFIX PLUGIN DEFINITION
  // =======================

  function Plugin(option) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('bs.affix')
      var options = typeof option == 'object' && option

      if (!data) $this.data('bs.affix', (data = new Affix(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  var old = $.fn.affix

  $.fn.affix             = Plugin
  $.fn.affix.Constructor = Affix


  // AFFIX NO CONFLICT
  // =================

  $.fn.affix.noConflict = function () {
    $.fn.affix = old
    return this
  }


  // AFFIX DATA-API
  // ==============

  $(window).on('load', function () {
    $('[data-spy="affix"]').each(function () {
      var $spy = $(this)
      var data = $spy.data()

      data.offset = data.offset || {}

      if (data.offsetBottom != null) data.offset.bottom = data.offsetBottom
      if (data.offsetTop    != null) data.offset.top    = data.offsetTop

      Plugin.call($spy, data)
    })
  })

}(jQuery);
;
/* ========================================================================
 * Bootstrap: alert.js v3.3.7
 * http://getbootstrap.com/javascript/#alerts
 * ========================================================================
 * Copyright 2011-2016 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // ALERT CLASS DEFINITION
  // ======================

  var dismiss = '[data-dismiss="alert"]'
  var Alert   = function (el) {
    $(el).on('click', dismiss, this.close)
  }

  Alert.VERSION = '3.3.7'

  Alert.TRANSITION_DURATION = 150

  Alert.prototype.close = function (e) {
    var $this    = $(this)
    var selector = $this.attr('data-target')

    if (!selector) {
      selector = $this.attr('href')
      selector = selector && selector.replace(/.*(?=#[^\s]*$)/, '') // strip for ie7
    }

    var $parent = $(selector === '#' ? [] : selector)

    if (e) e.preventDefault()

    if (!$parent.length) {
      $parent = $this.closest('.alert')
    }

    $parent.trigger(e = $.Event('close.bs.alert'))

    if (e.isDefaultPrevented()) return

    $parent.removeClass('in')

    function removeElement() {
      // detach from parent, fire event then clean up data
      $parent.detach().trigger('closed.bs.alert').remove()
    }

    $.support.transition && $parent.hasClass('fade') ?
      $parent
        .one('bsTransitionEnd', removeElement)
        .emulateTransitionEnd(Alert.TRANSITION_DURATION) :
      removeElement()
  }


  // ALERT PLUGIN DEFINITION
  // =======================

  function Plugin(option) {
    return this.each(function () {
      var $this = $(this)
      var data  = $this.data('bs.alert')

      if (!data) $this.data('bs.alert', (data = new Alert(this)))
      if (typeof option == 'string') data[option].call($this)
    })
  }

  var old = $.fn.alert

  $.fn.alert             = Plugin
  $.fn.alert.Constructor = Alert


  // ALERT NO CONFLICT
  // =================

  $.fn.alert.noConflict = function () {
    $.fn.alert = old
    return this
  }


  // ALERT DATA-API
  // ==============

  $(document).on('click.bs.alert.data-api', dismiss, Alert.prototype.close)

}(jQuery);
;
/* ========================================================================
 * Bootstrap: button.js v3.3.7
 * http://getbootstrap.com/javascript/#buttons
 * ========================================================================
 * Copyright 2011-2016 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // BUTTON PUBLIC CLASS DEFINITION
  // ==============================

  var Button = function (element, options) {
    this.$element  = $(element)
    this.options   = $.extend({}, Button.DEFAULTS, options)
    this.isLoading = false
  }

  Button.VERSION  = '3.3.7'

  Button.DEFAULTS = {
    loadingText: 'loading...'
  }

  Button.prototype.setState = function (state) {
    var d    = 'disabled'
    var $el  = this.$element
    var val  = $el.is('input') ? 'val' : 'html'
    var data = $el.data()

    state += 'Text'

    if (data.resetText == null) $el.data('resetText', $el[val]())

    // push to event loop to allow forms to submit
    setTimeout($.proxy(function () {
      $el[val](data[state] == null ? this.options[state] : data[state])

      if (state == 'loadingText') {
        this.isLoading = true
        $el.addClass(d).attr(d, d).prop(d, true)
      } else if (this.isLoading) {
        this.isLoading = false
        $el.removeClass(d).removeAttr(d).prop(d, false)
      }
    }, this), 0)
  }

  Button.prototype.toggle = function () {
    var changed = true
    var $parent = this.$element.closest('[data-toggle="buttons"]')

    if ($parent.length) {
      var $input = this.$element.find('input')
      if ($input.prop('type') == 'radio') {
        if ($input.prop('checked')) changed = false
        $parent.find('.active').removeClass('active')
        this.$element.addClass('active')
      } else if ($input.prop('type') == 'checkbox') {
        if (($input.prop('checked')) !== this.$element.hasClass('active')) changed = false
        this.$element.toggleClass('active')
      }
      $input.prop('checked', this.$element.hasClass('active'))
      if (changed) $input.trigger('change')
    } else {
      this.$element.attr('aria-pressed', !this.$element.hasClass('active'))
      this.$element.toggleClass('active')
    }
  }


  // BUTTON PLUGIN DEFINITION
  // ========================

  function Plugin(option) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('bs.button')
      var options = typeof option == 'object' && option

      if (!data) $this.data('bs.button', (data = new Button(this, options)))

      if (option == 'toggle') data.toggle()
      else if (option) data.setState(option)
    })
  }

  var old = $.fn.button

  $.fn.button             = Plugin
  $.fn.button.Constructor = Button


  // BUTTON NO CONFLICT
  // ==================

  $.fn.button.noConflict = function () {
    $.fn.button = old
    return this
  }


  // BUTTON DATA-API
  // ===============

  $(document)
    .on('click.bs.button.data-api', '[data-toggle^="button"]', function (e) {
      var $btn = $(e.target).closest('.btn')
      Plugin.call($btn, 'toggle')
      if (!($(e.target).is('input[type="radio"], input[type="checkbox"]'))) {
        // Prevent double click on radios, and the double selections (so cancellation) on checkboxes
        e.preventDefault()
        // The target component still receive the focus
        if ($btn.is('input,button')) $btn.trigger('focus')
        else $btn.find('input:visible,button:visible').first().trigger('focus')
      }
    })
    .on('focus.bs.button.data-api blur.bs.button.data-api', '[data-toggle^="button"]', function (e) {
      $(e.target).closest('.btn').toggleClass('focus', /^focus(in)?$/.test(e.type))
    })

}(jQuery);
;
/* ========================================================================
 * Bootstrap: modal.js v3.3.7
 * http://getbootstrap.com/javascript/#modals
 * ========================================================================
 * Copyright 2011-2016 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // MODAL CLASS DEFINITION
  // ======================

  var Modal = function (element, options) {
    this.options             = options
    this.$body               = $(document.body)
    this.$element            = $(element)
    this.$dialog             = this.$element.find('.modal-dialog')
    this.$backdrop           = null
    this.isShown             = null
    this.originalBodyPad     = null
    this.scrollbarWidth      = 0
    this.ignoreBackdropClick = false

    if (this.options.remote) {
      this.$element
        .find('.modal-content')
        .load(this.options.remote, $.proxy(function () {
          this.$element.trigger('loaded.bs.modal')
        }, this))
    }
  }

  Modal.VERSION  = '3.3.7'

  Modal.TRANSITION_DURATION = 300
  Modal.BACKDROP_TRANSITION_DURATION = 150

  Modal.DEFAULTS = {
    backdrop: true,
    keyboard: true,
    show: true
  }

  Modal.prototype.toggle = function (_relatedTarget) {
    return this.isShown ? this.hide() : this.show(_relatedTarget)
  }

  Modal.prototype.show = function (_relatedTarget) {
    var that = this
    var e    = $.Event('show.bs.modal', { relatedTarget: _relatedTarget })

    this.$element.trigger(e)

    if (this.isShown || e.isDefaultPrevented()) return

    this.isShown = true

    this.checkScrollbar()
    this.setScrollbar()
    this.$body.addClass('modal-open')

    this.escape()
    this.resize()

    this.$element.on('click.dismiss.bs.modal', '[data-dismiss="modal"]', $.proxy(this.hide, this))

    this.$dialog.on('mousedown.dismiss.bs.modal', function () {
      that.$element.one('mouseup.dismiss.bs.modal', function (e) {
        if ($(e.target).is(that.$element)) that.ignoreBackdropClick = true
      })
    })

    this.backdrop(function () {
      var transition = $.support.transition && that.$element.hasClass('fade')

      if (!that.$element.parent().length) {
        that.$element.appendTo(that.$body) // don't move modals dom position
      }

      that.$element
        .show()
        .scrollTop(0)

      that.adjustDialog()

      if (transition) {
        that.$element[0].offsetWidth // force reflow
      }

      that.$element.addClass('in')

      that.enforceFocus()

      var e = $.Event('shown.bs.modal', { relatedTarget: _relatedTarget })

      transition ?
        that.$dialog // wait for modal to slide in
          .one('bsTransitionEnd', function () {
            that.$element.trigger('focus').trigger(e)
          })
          .emulateTransitionEnd(Modal.TRANSITION_DURATION) :
        that.$element.trigger('focus').trigger(e)
    })
  }

  Modal.prototype.hide = function (e) {
    if (e) e.preventDefault()

    e = $.Event('hide.bs.modal')

    this.$element.trigger(e)

    if (!this.isShown || e.isDefaultPrevented()) return

    this.isShown = false

    this.escape()
    this.resize()

    $(document).off('focusin.bs.modal')

    this.$element
      .removeClass('in')
      .off('click.dismiss.bs.modal')
      .off('mouseup.dismiss.bs.modal')

    this.$dialog.off('mousedown.dismiss.bs.modal')

    $.support.transition && this.$element.hasClass('fade') ?
      this.$element
        .one('bsTransitionEnd', $.proxy(this.hideModal, this))
        .emulateTransitionEnd(Modal.TRANSITION_DURATION) :
      this.hideModal()
  }

  Modal.prototype.enforceFocus = function () {
    $(document)
      .off('focusin.bs.modal') // guard against infinite focus loop
      .on('focusin.bs.modal', $.proxy(function (e) {
        if (document !== e.target &&
            this.$element[0] !== e.target &&
            !this.$element.has(e.target).length) {
          this.$element.trigger('focus')
        }
      }, this))
  }

  Modal.prototype.escape = function () {
    if (this.isShown && this.options.keyboard) {
      this.$element.on('keydown.dismiss.bs.modal', $.proxy(function (e) {
        e.which == 27 && this.hide()
      }, this))
    } else if (!this.isShown) {
      this.$element.off('keydown.dismiss.bs.modal')
    }
  }

  Modal.prototype.resize = function () {
    if (this.isShown) {
      $(window).on('resize.bs.modal', $.proxy(this.handleUpdate, this))
    } else {
      $(window).off('resize.bs.modal')
    }
  }

  Modal.prototype.hideModal = function () {
    var that = this
    this.$element.hide()
    this.backdrop(function () {
      that.$body.removeClass('modal-open')
      that.resetAdjustments()
      that.resetScrollbar()
      that.$element.trigger('hidden.bs.modal')
    })
  }

  Modal.prototype.removeBackdrop = function () {
    this.$backdrop && this.$backdrop.remove()
    this.$backdrop = null
  }

  Modal.prototype.backdrop = function (callback) {
    var that = this
    var animate = this.$element.hasClass('fade') ? 'fade' : ''

    if (this.isShown && this.options.backdrop) {
      var doAnimate = $.support.transition && animate

      this.$backdrop = $(document.createElement('div'))
        .addClass('modal-backdrop ' + animate)
        .appendTo(this.$body)

      this.$element.on('click.dismiss.bs.modal', $.proxy(function (e) {
        if (this.ignoreBackdropClick) {
          this.ignoreBackdropClick = false
          return
        }
        if (e.target !== e.currentTarget) return
        this.options.backdrop == 'static'
          ? this.$element[0].focus()
          : this.hide()
      }, this))

      if (doAnimate) this.$backdrop[0].offsetWidth // force reflow

      this.$backdrop.addClass('in')

      if (!callback) return

      doAnimate ?
        this.$backdrop
          .one('bsTransitionEnd', callback)
          .emulateTransitionEnd(Modal.BACKDROP_TRANSITION_DURATION) :
        callback()

    } else if (!this.isShown && this.$backdrop) {
      this.$backdrop.removeClass('in')

      var callbackRemove = function () {
        that.removeBackdrop()
        callback && callback()
      }
      $.support.transition && this.$element.hasClass('fade') ?
        this.$backdrop
          .one('bsTransitionEnd', callbackRemove)
          .emulateTransitionEnd(Modal.BACKDROP_TRANSITION_DURATION) :
        callbackRemove()

    } else if (callback) {
      callback()
    }
  }

  // these following methods are used to handle overflowing modals

  Modal.prototype.handleUpdate = function () {
    this.adjustDialog()
  }

  Modal.prototype.adjustDialog = function () {
    var modalIsOverflowing = this.$element[0].scrollHeight > document.documentElement.clientHeight

    this.$element.css({
      paddingLeft:  !this.bodyIsOverflowing && modalIsOverflowing ? this.scrollbarWidth : '',
      paddingRight: this.bodyIsOverflowing && !modalIsOverflowing ? this.scrollbarWidth : ''
    })
  }

  Modal.prototype.resetAdjustments = function () {
    this.$element.css({
      paddingLeft: '',
      paddingRight: ''
    })
  }

  Modal.prototype.checkScrollbar = function () {
    var fullWindowWidth = window.innerWidth
    if (!fullWindowWidth) { // workaround for missing window.innerWidth in IE8
      var documentElementRect = document.documentElement.getBoundingClientRect()
      fullWindowWidth = documentElementRect.right - Math.abs(documentElementRect.left)
    }
    this.bodyIsOverflowing = document.body.clientWidth < fullWindowWidth
    this.scrollbarWidth = this.measureScrollbar()
  }

  Modal.prototype.setScrollbar = function () {
    var bodyPad = parseInt((this.$body.css('padding-right') || 0), 10)
    this.originalBodyPad = document.body.style.paddingRight || ''
    if (this.bodyIsOverflowing) this.$body.css('padding-right', bodyPad + this.scrollbarWidth)
  }

  Modal.prototype.resetScrollbar = function () {
    this.$body.css('padding-right', this.originalBodyPad)
  }

  Modal.prototype.measureScrollbar = function () { // thx walsh
    var scrollDiv = document.createElement('div')
    scrollDiv.className = 'modal-scrollbar-measure'
    this.$body.append(scrollDiv)
    var scrollbarWidth = scrollDiv.offsetWidth - scrollDiv.clientWidth
    this.$body[0].removeChild(scrollDiv)
    return scrollbarWidth
  }


  // MODAL PLUGIN DEFINITION
  // =======================

  function Plugin(option, _relatedTarget) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('bs.modal')
      var options = $.extend({}, Modal.DEFAULTS, $this.data(), typeof option == 'object' && option)

      if (!data) $this.data('bs.modal', (data = new Modal(this, options)))
      if (typeof option == 'string') data[option](_relatedTarget)
      else if (options.show) data.show(_relatedTarget)
    })
  }

  var old = $.fn.modal

  $.fn.modal             = Plugin
  $.fn.modal.Constructor = Modal


  // MODAL NO CONFLICT
  // =================

  $.fn.modal.noConflict = function () {
    $.fn.modal = old
    return this
  }


  // MODAL DATA-API
  // ==============

  $(document).on('click.bs.modal.data-api', '[data-toggle="modal"]', function (e) {
    var $this   = $(this)
    var href    = $this.attr('href')
    var $target = $($this.attr('data-target') || (href && href.replace(/.*(?=#[^\s]+$)/, ''))) // strip for ie7
    var option  = $target.data('bs.modal') ? 'toggle' : $.extend({ remote: !/#/.test(href) && href }, $target.data(), $this.data())

    if ($this.is('a')) e.preventDefault()

    $target.one('show.bs.modal', function (showEvent) {
      if (showEvent.isDefaultPrevented()) return // only register focus restorer if modal will actually get shown
      $target.one('hidden.bs.modal', function () {
        $this.is(':visible') && $this.trigger('focus')
      })
    })
    Plugin.call($target, option, this)
  })

}(jQuery);
;
/* ========================================================================
 * Bootstrap: tooltip.js v3.3.7
 * http://getbootstrap.com/javascript/#tooltip
 * Inspired by the original jQuery.tipsy by Jason Frame
 * ========================================================================
 * Copyright 2011-2016 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // TOOLTIP PUBLIC CLASS DEFINITION
  // ===============================

  var Tooltip = function (element, options) {
    this.type       = null
    this.options    = null
    this.enabled    = null
    this.timeout    = null
    this.hoverState = null
    this.$element   = null
    this.inState    = null

    this.init('tooltip', element, options)
  }

  Tooltip.VERSION  = '3.3.7'

  Tooltip.TRANSITION_DURATION = 150

  Tooltip.DEFAULTS = {
    animation: true,
    placement: 'top',
    selector: false,
    template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
    trigger: 'hover focus',
    title: '',
    delay: 0,
    html: false,
    container: false,
    viewport: {
      selector: 'body',
      padding: 0
    }
  }

  Tooltip.prototype.init = function (type, element, options) {
    this.enabled   = true
    this.type      = type
    this.$element  = $(element)
    this.options   = this.getOptions(options)
    this.$viewport = this.options.viewport && $($.isFunction(this.options.viewport) ? this.options.viewport.call(this, this.$element) : (this.options.viewport.selector || this.options.viewport))
    this.inState   = { click: false, hover: false, focus: false }

    if (this.$element[0] instanceof document.constructor && !this.options.selector) {
      throw new Error('`selector` option must be specified when initializing ' + this.type + ' on the window.document object!')
    }

    var triggers = this.options.trigger.split(' ')

    for (var i = triggers.length; i--;) {
      var trigger = triggers[i]

      if (trigger == 'click') {
        this.$element.on('click.' + this.type, this.options.selector, $.proxy(this.toggle, this))
      } else if (trigger != 'manual') {
        var eventIn  = trigger == 'hover' ? 'mouseenter' : 'focusin'
        var eventOut = trigger == 'hover' ? 'mouseleave' : 'focusout'

        this.$element.on(eventIn  + '.' + this.type, this.options.selector, $.proxy(this.enter, this))
        this.$element.on(eventOut + '.' + this.type, this.options.selector, $.proxy(this.leave, this))
      }
    }

    this.options.selector ?
      (this._options = $.extend({}, this.options, { trigger: 'manual', selector: '' })) :
      this.fixTitle()
  }

  Tooltip.prototype.getDefaults = function () {
    return Tooltip.DEFAULTS
  }

  Tooltip.prototype.getOptions = function (options) {
    options = $.extend({}, this.getDefaults(), this.$element.data(), options)

    if (options.delay && typeof options.delay == 'number') {
      options.delay = {
        show: options.delay,
        hide: options.delay
      }
    }

    return options
  }

  Tooltip.prototype.getDelegateOptions = function () {
    var options  = {}
    var defaults = this.getDefaults()

    this._options && $.each(this._options, function (key, value) {
      if (defaults[key] != value) options[key] = value
    })

    return options
  }

  Tooltip.prototype.enter = function (obj) {
    var self = obj instanceof this.constructor ?
      obj : $(obj.currentTarget).data('bs.' + this.type)

    if (!self) {
      self = new this.constructor(obj.currentTarget, this.getDelegateOptions())
      $(obj.currentTarget).data('bs.' + this.type, self)
    }

    if (obj instanceof $.Event) {
      self.inState[obj.type == 'focusin' ? 'focus' : 'hover'] = true
    }

    if (self.tip().hasClass('in') || self.hoverState == 'in') {
      self.hoverState = 'in'
      return
    }

    clearTimeout(self.timeout)

    self.hoverState = 'in'

    if (!self.options.delay || !self.options.delay.show) return self.show()

    self.timeout = setTimeout(function () {
      if (self.hoverState == 'in') self.show()
    }, self.options.delay.show)
  }

  Tooltip.prototype.isInStateTrue = function () {
    for (var key in this.inState) {
      if (this.inState[key]) return true
    }

    return false
  }

  Tooltip.prototype.leave = function (obj) {
    var self = obj instanceof this.constructor ?
      obj : $(obj.currentTarget).data('bs.' + this.type)

    if (!self) {
      self = new this.constructor(obj.currentTarget, this.getDelegateOptions())
      $(obj.currentTarget).data('bs.' + this.type, self)
    }

    if (obj instanceof $.Event) {
      self.inState[obj.type == 'focusout' ? 'focus' : 'hover'] = false
    }

    if (self.isInStateTrue()) return

    clearTimeout(self.timeout)

    self.hoverState = 'out'

    if (!self.options.delay || !self.options.delay.hide) return self.hide()

    self.timeout = setTimeout(function () {
      if (self.hoverState == 'out') self.hide()
    }, self.options.delay.hide)
  }

  Tooltip.prototype.show = function () {
    var e = $.Event('show.bs.' + this.type)

    if (this.hasContent() && this.enabled) {
      this.$element.trigger(e)

      var inDom = $.contains(this.$element[0].ownerDocument.documentElement, this.$element[0])
      if (e.isDefaultPrevented() || !inDom) return
      var that = this

      var $tip = this.tip()

      var tipId = this.getUID(this.type)

      this.setContent()
      $tip.attr('id', tipId)
      this.$element.attr('aria-describedby', tipId)

      if (this.options.animation) $tip.addClass('fade')

      var placement = typeof this.options.placement == 'function' ?
        this.options.placement.call(this, $tip[0], this.$element[0]) :
        this.options.placement

      var autoToken = /\s?auto?\s?/i
      var autoPlace = autoToken.test(placement)
      if (autoPlace) placement = placement.replace(autoToken, '') || 'top'

      $tip
        .detach()
        .css({ top: 0, left: 0, display: 'block' })
        .addClass(placement)
        .data('bs.' + this.type, this)

      this.options.container ? $tip.appendTo(this.options.container) : $tip.insertAfter(this.$element)
      this.$element.trigger('inserted.bs.' + this.type)

      var pos          = this.getPosition()
      var actualWidth  = $tip[0].offsetWidth
      var actualHeight = $tip[0].offsetHeight

      if (autoPlace) {
        var orgPlacement = placement
        var viewportDim = this.getPosition(this.$viewport)

        placement = placement == 'bottom' && pos.bottom + actualHeight > viewportDim.bottom ? 'top'    :
                    placement == 'top'    && pos.top    - actualHeight < viewportDim.top    ? 'bottom' :
                    placement == 'right'  && pos.right  + actualWidth  > viewportDim.width  ? 'left'   :
                    placement == 'left'   && pos.left   - actualWidth  < viewportDim.left   ? 'right'  :
                    placement

        $tip
          .removeClass(orgPlacement)
          .addClass(placement)
      }

      var calculatedOffset = this.getCalculatedOffset(placement, pos, actualWidth, actualHeight)

      this.applyPlacement(calculatedOffset, placement)

      var complete = function () {
        var prevHoverState = that.hoverState
        that.$element.trigger('shown.bs.' + that.type)
        that.hoverState = null

        if (prevHoverState == 'out') that.leave(that)
      }

      $.support.transition && this.$tip.hasClass('fade') ?
        $tip
          .one('bsTransitionEnd', complete)
          .emulateTransitionEnd(Tooltip.TRANSITION_DURATION) :
        complete()
    }
  }

  Tooltip.prototype.applyPlacement = function (offset, placement) {
    var $tip   = this.tip()
    var width  = $tip[0].offsetWidth
    var height = $tip[0].offsetHeight

    // manually read margins because getBoundingClientRect includes difference
    var marginTop = parseInt($tip.css('margin-top'), 10)
    var marginLeft = parseInt($tip.css('margin-left'), 10)

    // we must check for NaN for ie 8/9
    if (isNaN(marginTop))  marginTop  = 0
    if (isNaN(marginLeft)) marginLeft = 0

    offset.top  += marginTop
    offset.left += marginLeft

    // $.fn.offset doesn't round pixel values
    // so we use setOffset directly with our own function B-0
    $.offset.setOffset($tip[0], $.extend({
      using: function (props) {
        $tip.css({
          top: Math.round(props.top),
          left: Math.round(props.left)
        })
      }
    }, offset), 0)

    $tip.addClass('in')

    // check to see if placing tip in new offset caused the tip to resize itself
    var actualWidth  = $tip[0].offsetWidth
    var actualHeight = $tip[0].offsetHeight

    if (placement == 'top' && actualHeight != height) {
      offset.top = offset.top + height - actualHeight
    }

    var delta = this.getViewportAdjustedDelta(placement, offset, actualWidth, actualHeight)

    if (delta.left) offset.left += delta.left
    else offset.top += delta.top

    var isVertical          = /top|bottom/.test(placement)
    var arrowDelta          = isVertical ? delta.left * 2 - width + actualWidth : delta.top * 2 - height + actualHeight
    var arrowOffsetPosition = isVertical ? 'offsetWidth' : 'offsetHeight'

    $tip.offset(offset)
    this.replaceArrow(arrowDelta, $tip[0][arrowOffsetPosition], isVertical)
  }

  Tooltip.prototype.replaceArrow = function (delta, dimension, isVertical) {
    this.arrow()
      .css(isVertical ? 'left' : 'top', 50 * (1 - delta / dimension) + '%')
      .css(isVertical ? 'top' : 'left', '')
  }

  Tooltip.prototype.setContent = function () {
    var $tip  = this.tip()
    var title = this.getTitle()

    $tip.find('.tooltip-inner')[this.options.html ? 'html' : 'text'](title)
    $tip.removeClass('fade in top bottom left right')
  }

  Tooltip.prototype.hide = function (callback) {
    var that = this
    var $tip = $(this.$tip)
    var e    = $.Event('hide.bs.' + this.type)

    function complete() {
      if (that.hoverState != 'in') $tip.detach()
      if (that.$element) { // TODO: Check whether guarding this code with this `if` is really necessary.
        that.$element
          .removeAttr('aria-describedby')
          .trigger('hidden.bs.' + that.type)
      }
      callback && callback()
    }

    this.$element.trigger(e)

    if (e.isDefaultPrevented()) return

    $tip.removeClass('in')

    $.support.transition && $tip.hasClass('fade') ?
      $tip
        .one('bsTransitionEnd', complete)
        .emulateTransitionEnd(Tooltip.TRANSITION_DURATION) :
      complete()

    this.hoverState = null

    return this
  }

  Tooltip.prototype.fixTitle = function () {
    var $e = this.$element
    if ($e.attr('title') || typeof $e.attr('data-original-title') != 'string') {
      $e.attr('data-original-title', $e.attr('title') || '').attr('title', '')
    }
  }

  Tooltip.prototype.hasContent = function () {
    return this.getTitle()
  }

  Tooltip.prototype.getPosition = function ($element) {
    $element   = $element || this.$element

    var el     = $element[0]
    var isBody = el.tagName == 'BODY'

    var elRect    = el.getBoundingClientRect()
    if (elRect.width == null) {
      // width and height are missing in IE8, so compute them manually; see https://github.com/twbs/bootstrap/issues/14093
      elRect = $.extend({}, elRect, { width: elRect.right - elRect.left, height: elRect.bottom - elRect.top })
    }
    var isSvg = window.SVGElement && el instanceof window.SVGElement
    // Avoid using $.offset() on SVGs since it gives incorrect results in jQuery 3.
    // See https://github.com/twbs/bootstrap/issues/20280
    var elOffset  = isBody ? { top: 0, left: 0 } : (isSvg ? null : $element.offset())
    var scroll    = { scroll: isBody ? document.documentElement.scrollTop || document.body.scrollTop : $element.scrollTop() }
    var outerDims = isBody ? { width: $(window).width(), height: $(window).height() } : null

    return $.extend({}, elRect, scroll, outerDims, elOffset)
  }

  Tooltip.prototype.getCalculatedOffset = function (placement, pos, actualWidth, actualHeight) {
    return placement == 'bottom' ? { top: pos.top + pos.height,   left: pos.left + pos.width / 2 - actualWidth / 2 } :
           placement == 'top'    ? { top: pos.top - actualHeight, left: pos.left + pos.width / 2 - actualWidth / 2 } :
           placement == 'left'   ? { top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left - actualWidth } :
        /* placement == 'right' */ { top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left + pos.width }

  }

  Tooltip.prototype.getViewportAdjustedDelta = function (placement, pos, actualWidth, actualHeight) {
    var delta = { top: 0, left: 0 }
    if (!this.$viewport) return delta

    var viewportPadding = this.options.viewport && this.options.viewport.padding || 0
    var viewportDimensions = this.getPosition(this.$viewport)

    if (/right|left/.test(placement)) {
      var topEdgeOffset    = pos.top - viewportPadding - viewportDimensions.scroll
      var bottomEdgeOffset = pos.top + viewportPadding - viewportDimensions.scroll + actualHeight
      if (topEdgeOffset < viewportDimensions.top) { // top overflow
        delta.top = viewportDimensions.top - topEdgeOffset
      } else if (bottomEdgeOffset > viewportDimensions.top + viewportDimensions.height) { // bottom overflow
        delta.top = viewportDimensions.top + viewportDimensions.height - bottomEdgeOffset
      }
    } else {
      var leftEdgeOffset  = pos.left - viewportPadding
      var rightEdgeOffset = pos.left + viewportPadding + actualWidth
      if (leftEdgeOffset < viewportDimensions.left) { // left overflow
        delta.left = viewportDimensions.left - leftEdgeOffset
      } else if (rightEdgeOffset > viewportDimensions.right) { // right overflow
        delta.left = viewportDimensions.left + viewportDimensions.width - rightEdgeOffset
      }
    }

    return delta
  }

  Tooltip.prototype.getTitle = function () {
    var title
    var $e = this.$element
    var o  = this.options

    title = $e.attr('data-original-title')
      || (typeof o.title == 'function' ? o.title.call($e[0]) :  o.title)

    return title
  }

  Tooltip.prototype.getUID = function (prefix) {
    do prefix += ~~(Math.random() * 1000000)
    while (document.getElementById(prefix))
    return prefix
  }

  Tooltip.prototype.tip = function () {
    if (!this.$tip) {
      this.$tip = $(this.options.template)
      if (this.$tip.length != 1) {
        throw new Error(this.type + ' `template` option must consist of exactly 1 top-level element!')
      }
    }
    return this.$tip
  }

  Tooltip.prototype.arrow = function () {
    return (this.$arrow = this.$arrow || this.tip().find('.tooltip-arrow'))
  }

  Tooltip.prototype.enable = function () {
    this.enabled = true
  }

  Tooltip.prototype.disable = function () {
    this.enabled = false
  }

  Tooltip.prototype.toggleEnabled = function () {
    this.enabled = !this.enabled
  }

  Tooltip.prototype.toggle = function (e) {
    var self = this
    if (e) {
      self = $(e.currentTarget).data('bs.' + this.type)
      if (!self) {
        self = new this.constructor(e.currentTarget, this.getDelegateOptions())
        $(e.currentTarget).data('bs.' + this.type, self)
      }
    }

    if (e) {
      self.inState.click = !self.inState.click
      if (self.isInStateTrue()) self.enter(self)
      else self.leave(self)
    } else {
      self.tip().hasClass('in') ? self.leave(self) : self.enter(self)
    }
  }

  Tooltip.prototype.destroy = function () {
    var that = this
    clearTimeout(this.timeout)
    this.hide(function () {
      that.$element.off('.' + that.type).removeData('bs.' + that.type)
      if (that.$tip) {
        that.$tip.detach()
      }
      that.$tip = null
      that.$arrow = null
      that.$viewport = null
      that.$element = null
    })
  }


  // TOOLTIP PLUGIN DEFINITION
  // =========================

  function Plugin(option) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('bs.tooltip')
      var options = typeof option == 'object' && option

      if (!data && /destroy|hide/.test(option)) return
      if (!data) $this.data('bs.tooltip', (data = new Tooltip(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  var old = $.fn.tooltip

  $.fn.tooltip             = Plugin
  $.fn.tooltip.Constructor = Tooltip


  // TOOLTIP NO CONFLICT
  // ===================

  $.fn.tooltip.noConflict = function () {
    $.fn.tooltip = old
    return this
  }

}(jQuery);
;
/* ========================================================================
 * Bootstrap: transition.js v3.3.7
 * http://getbootstrap.com/javascript/#transitions
 * ========================================================================
 * Copyright 2011-2016 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // CSS TRANSITION SUPPORT (Shoutout: http://www.modernizr.com/)
  // ============================================================

  function transitionEnd() {
    var el = document.createElement('bootstrap')

    var transEndEventNames = {
      WebkitTransition : 'webkitTransitionEnd',
      MozTransition    : 'transitionend',
      OTransition      : 'oTransitionEnd otransitionend',
      transition       : 'transitionend'
    }

    for (var name in transEndEventNames) {
      if (el.style[name] !== undefined) {
        return { end: transEndEventNames[name] }
      }
    }

    return false // explicit for ie8 (  ._.)
  }

  // http://blog.alexmaccaw.com/css-transitions
  $.fn.emulateTransitionEnd = function (duration) {
    var called = false
    var $el = this
    $(this).one('bsTransitionEnd', function () { called = true })
    var callback = function () { if (!called) $($el).trigger($.support.transition.end) }
    setTimeout(callback, duration)
    return this
  }

  $(function () {
    $.support.transition = transitionEnd()

    if (!$.support.transition) return

    $.event.special.bsTransitionEnd = {
      bindType: $.support.transition.end,
      delegateType: $.support.transition.end,
      handle: function (e) {
        if ($(e.target).is(this)) return e.handleObj.handler.apply(this, arguments)
      }
    }
  })

}(jQuery);
;
var jsvat = (function() {

  'use strict'

  function Result(vat, isValid, country) {
    this.value = vat || null
    this.isValid = !!isValid

    if (country) {
      this.country = {
        name: country.name,
        isoCode: {
          short: country.codes[0],
          long: country.codes[1],
          numeric: country.codes[2]
        }
      }
    }
  }

  function removeExtraChars(vat) {
    vat = vat || ''
    return vat.toString().toUpperCase().replace(/(\s|-|\.)+/g, '')
  }

  function isValEqToCode(val, codes) {
    return (val === codes[0] || val === codes[1] || val === codes[2])
  }

  function isInList(list, country) {
    if (!list) return false

    for (var i = 0; i < list.length; i++) {
      var val = list[i].toUpperCase()
      if (val === country.name.toUpperCase()) return true
      if (isValEqToCode(val, country.codes)) return true
    }

    return false
  }

  function isBlocked(country, blocked, allowed) {
    var isBlocked = isInList(blocked, country)
    if (isBlocked) return true
    var isAllowed = isInList(allowed, country)
    return allowed.length > 0 && !isAllowed
  }

  function getCountry(vat, countries) {
    for (var k in countries) {
      if (countries.hasOwnProperty(k)) {
        var regexpValidRes = isVatValidToRegexp(vat, countries[k].rules.regex)
        if (regexpValidRes.isValid) return countries[k]
      }
    }

    return null
  }

  function isVatValidToRegexp(vat, regexArr) {
    for (var i = 0; i < regexArr.length; i++) {
      var regex = regexArr[i]
      var isValid = regex.test(vat)
      if (isValid) return {
        isValid: true,
        regex: regex
      }
    }

    return {
      isValid: false
    }
  }

  function isVatMathValid(vat, country) {
    return country.calcFn(vat)
  }

  function isVatValid(vat, country) {
    var regexpValidRes = isVatValidToRegexp(vat, country.rules.regex)
    if (!regexpValidRes.isValid) return false
    return isVatMathValid(regexpValidRes.regex.exec(vat)[2], country)
  }

  var exports = {
    blocked: [],
    allowed: [],
    countries: {},
    checkVAT: function(vat) {
      if (!vat) throw new Error('VAT should be specified')
      var cleanVAT = removeExtraChars(vat)
      var result = new Result(cleanVAT)

      var country = getCountry(cleanVAT, this.countries)
      if (!country) return result
      if (isBlocked(country, this.blocked, this.allowed)) return new Result(cleanVAT, false, country)

      var isValid = isVatValid(cleanVAT, country)
      if (isValid) return new Result(cleanVAT, isValid, country)

      return result
    }
  }


  // eslint-disable-next-line no-undef
  exports.countries.austria = {
    name: 'Austria',
    codes: ['AT', 'AUT', '040'],
    calcFn: function(vat) {
      var total = 0
      var temp

      for (var i = 0; i < 7; i++) {
        temp = vat.charAt(i) * this.rules.multipliers[i]

        if (temp > 9) {
          total += Math.floor(temp / 10) + temp % 10
        } else {
          total += temp
        }
      }

      total = 10 - (total + 4) % 10
      if (total === 10) total = 0

      return total === +vat.slice(7, 8)
    },
    rules: {
      multipliers: [1, 2, 1, 2, 1, 2, 1],
      regex: [/^(AT)U(\d{8})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.belgium = {
    name: 'Belgium',
    codes: ['BE', 'BEL', '056'],
    calcFn: function(vat) {
      if (vat.length === 9) {
        vat = '0' + vat
      }

      if (+vat.slice(1, 2) === 0) return false

      var check = (97 - +vat.slice(0, 8) % 97)
      return check === +vat.slice(8, 10)
    },
    rules: {
      regex: [/^(BE)(0?\d{9})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.bulgaria = {
    name: 'Bulgaria',
    codes: ['BG', 'BGR', '100'],
    calcFn: function(vat) {
      function _increase(value, vat, from, to, incr) {
        for (var i = from; i < to; i++) {
          value += +vat.charAt(i) * (i + incr)
        }
        return value
      }

      function _increase2(value, vat, from, to, multipliers) {
        for (var i = from; i < to; i++) {
          value += +vat.charAt(i) * multipliers[i]
        }
        return value
      }

      function _checkNineLengthVat(vat) {
        var total
        var temp = 0
        var expect = +vat.slice(8)

        temp = _increase(temp, vat, 0, 8, 1)

        total = temp % 11
        if (total !== 10) {
          return total === expect
        }

        temp = _increase(0, vat, 0, 8, 3)

        total = temp % 11
        if (total === 10) total = 0

        return total === expect
      }

      function _isPhysicalPerson(vat, rules) {
        // 10 digit VAT code - see if it relates to a standard physical person
        if ((/^\d\d[0-5]\d[0-3]\d\d{4}$/).test(vat)) {
          // Check month
          var month = +vat.slice(2, 4)
          if ((month > 0 && month < 13) || (month > 20 && month < 33) || (month > 40 && month < 53)) {
            var total = _increase2(0, vat, 0, 9, rules.multipliers.physical)
            // Establish check digit.
            total = total % 11
            if (total === 10) total = 0
            // Check to see if the check digit given is correct, If not, try next type of person
            if (total === +vat.substr(9, 1)) return true
          }
        }

        return false
      }

      function _isForeigner(vat, rules) {
        // Extract the next digit and multiply by the counter.
        var total = _increase2(0, vat, 0, 9, rules.multipliers.foreigner)

        // Check to see if the check digit given is correct, If not, try next type of person
        if (total % 10 === +vat.substr(9, 1)) {
          return true
        }
      }

      function _miscellaneousVAT(vat, rules) {
        // Finally, if not yet identified, see if it conforms to a miscellaneous VAT number
        var total = _increase2(0, vat, 0, 9, rules.multipliers.miscellaneous)

        // Establish check digit.
        total = 11 - total % 11
        if (total === 10) return false
        if (total === 11) total = 0

        // Check to see if the check digit given is correct, If not, we have an error with the VAT number
        var expect = +vat.substr(9, 1)
        return total === expect
      }

      if (vat.length === 9) {
        return _checkNineLengthVat(vat)
      } else {
        return _isPhysicalPerson(vat, this.rules) || _isForeigner(vat, this.rules) || _miscellaneousVAT(vat, this.rules)
      }
    },
    rules: {
      multipliers: {
        physical: [2, 4, 8, 5, 10, 9, 7, 3, 6],
        foreigner: [21, 19, 17, 13, 11, 9, 7, 3, 1],
        miscellaneous: [4, 3, 2, 7, 6, 5, 4, 3, 2]
      },
      regex: [/^(BG)(\d{9,10})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.croatia = {
    name: 'Croatia',
    codes: ['HR', 'HRV', '191'],
    calcFn: function(vat) {
      var expect

      // Checks the check digits of a Croatian VAT number using ISO 7064, MOD 11-10 for check digit.
      var product = 10
      var sum = 0

      for (var i = 0; i < 10; i++) {
        // Extract the next digit and implement the algorithm
        sum = (+vat.charAt(i) + product) % 10
        if (sum === 0) {
          sum = 10
        }

        product = (2 * sum) % 11
      }

      // Now check that we have the right check digit
      expect = +vat.slice(10, 11)
      return (product + expect) % 10 === 1
    },
    rules: {
      regex: [/^(HR)(\d{11})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.cyprus = {
    name: 'Cyprus',
    codes: ['CY', 'CYP', '196'],
    calcFn: function(vat) {
      var total = 0
      var expect

      // Not allowed to start with '12'
      if (+vat.slice(0, 2) === 12) return false

      // Extract the next digit and multiply by the counter.

      for (var i = 0; i < 8; i++) {
        var temp = +vat.charAt(i)
        if (i % 2 === 0) {
          switch (temp) {
            case 0:
              temp = 1
              break
            case 1:
              temp = 0
              break
            case 2:
              temp = 5
              break
            case 3:
              temp = 7
              break
            case 4:
              temp = 9
              break
            default:
              temp = temp * 2 + 3
          }
        }
        total += temp
      }

      // Establish check digit using modulus 26, and translate to char. equivalent.
      total = total % 26
      total = String.fromCharCode(total + 65)

      // Check to see if the check digit given is correct
      expect = vat.substr(8, 1)
      return total === expect
    },
    rules: {
      regex: [/^(CY)([0-59]\d{7}[A-Z])$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.czech_republic = {
    name: 'Czech Republic',
    codes: ['CZ', 'CZE', '203'],
    calcFn: function(vat) {
      function _isLegalEntities(vat, rules) {
        var total = 0

        if (rules.additional[0].test(vat)) {
          // Extract the next digit and multiply by the counter.
          for (var i = 0; i < 7; i++) {
            total += +vat.charAt(i) * rules.multipliers[i]
          }

          // Establish check digit.
          total = 11 - total % 11
          if (total === 10) total = 0
          if (total === 11) total = 1

          // Compare it with the last character of the VAT number. If it's the same, then it's valid.
          var expect = +vat.slice(7, 8)
          return total === expect
        }

        return false
      }

      function _isIndividualType2(vat, rules) {
        var total = 0

        if (rules.additional[2].test(vat)) {
          // Extract the next digit and multiply by the counter.
          for (var j = 0; j < 7; j++) {
            total += +vat.charAt(j + 1) * rules.multipliers[j]
          }

          // Establish check digit.
          total = 11 - total % 11
          if (total === 10) total = 0
          if (total === 11) total = 1

          // Convert calculated check digit according to a lookup table
          var expect = +vat.slice(8, 9)
          return rules.lookup[total - 1] === expect
        }

        return false
      }

      function _isIndividualType3(vat, rules) {
        if (rules.additional[3].test(vat)) {
          var temp = +vat.slice(0, 2) + vat.slice(2, 4) + vat.slice(4, 6) + vat.slice(6, 8) + vat.slice(8)
          var expect = +vat % 11 === 0
          return !!(temp % 11 === 0 && expect)
        }

        return false
      }

      if (_isLegalEntities(vat, this.rules)) return true
      if (_isIndividualType2(vat, this.rules)) return true
      if (_isIndividualType3(vat, this.rules)) return true

      return false
    },
    rules: {
      multipliers: [8, 7, 6, 5, 4, 3, 2],
      lookup: [8, 7, 6, 5, 4, 3, 2, 1, 0, 9, 10],
      regex: [/^(CZ)(\d{8,10})(\d{3})?$/],
      additional: [
        /^\d{8}$/,
        /^[0-5][0-9][0|1|5|6]\d[0-3]\d\d{3}$/,
        /^6\d{8}$/,
        /^\d{2}[0-3|5-8]\d[0-3]\d\d{4}$/
      ]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.denmark = {
    name: 'Denmark',
    codes: ['DK', 'DNK', '208'],
    calcFn: function(vat) {
      var total = 0

      for (var i = 0; i < 8; i++) {
        total += +vat.charAt(i) * this.rules.multipliers[i]
      }

      return total % 11 === 0
    },
    rules: {
      multipliers: [2, 7, 6, 5, 4, 3, 2, 1],
      regex: [/^(DK)(\d{8})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.estonia = {
    name: 'Estonia',
    codes: ['EE', 'EST', '233'],
    calcFn: function(vat) {
      var total = 0
      var expect

      // Extract the next digit and multiply by the counter.
      for (var i = 0; i < 8; i++) {
        total += +vat.charAt(i) * this.rules.multipliers[i]
      }

      // Establish check digits using modulus 10.
      total = 10 - total % 10
      if (total === 10) total = 0

      // Compare it with the last character of the VAT number. If it's the same, then it's valid.
      expect = +vat.slice(8, 9)
      return total === expect
    },
    rules: {
      multipliers: [3, 7, 1, 3, 7, 1, 3, 7],
      regex: [/^(EE)(10\d{7})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.europe = {
    name: 'Europe',
    codes: ['EU', 'EUR', '000'], // TODO (S.Panfilov) that's not a real codes
    calcFn: function() {
      // We know little about EU numbers apart from the fact that the first 3 digits represent the
      // country, and that there are nine digits in total.
      return true
    },
    rules: {
      regex: [/^(EU)(\d{9})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.finland = {
    name: 'Finland',
    codes: ['FI', 'FIN', '246'],
    calcFn: function(vat) {
      var total = 0
      var expect

      // Extract the next digit and multiply by the counter.
      for (var i = 0; i < 7; i++) total += +vat.charAt(i) * this.rules.multipliers[i]

      // Establish check digit.
      total = 11 - total % 11
      if (total > 9) {
        total = 0
      }

      // Compare it with the last character of the VAT number. If it's the same, then it's valid.
      expect = +vat.slice(7, 8)
      return total === expect
    },
    rules: {
      multipliers: [7, 9, 10, 5, 8, 4, 2],
      regex: [/^(FI)(\d{8})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.france = {
    name: 'France',
    codes: ['FR', 'FRA', '250'],
    calcFn: function(vat) {
      var total
      var expect

      // Checks the check digits of a French VAT number.
      if (!(/^\d{11}$/).test(vat)) {
        return true
      }

      // Extract the last nine digits as an integer.
      total = +vat.substring(2)

      // Establish check digit.
      total = (total * 100 + 12) % 97

      // Compare it with the last character of the VAT number. If it's the same, then it's valid.
      expect = +vat.slice(0, 2)
      return total === expect
    },
    rules: {
      regex: [
        /^(FR)(\d{11})$/,
        /^(FR)([A-HJ-NP-Z]\d{10})$/,
        /^(FR)(\d[A-HJ-NP-Z]\d{9})$/,
        /^(FR)([A-HJ-NP-Z]{2}\d{9})$/
      ]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.germany = {
    name: 'Germany',
    codes: ['DE', 'DEU', '276'],
    calcFn: function(vat) {
      // Checks the check digits of a German VAT number.
      var product = 10
      var sum = 0
      var checkDigit = 0
      var expect

      for (var i = 0; i < 8; i++) {
        // Extract the next digit and implement peculiar algorithm!.
        sum = (+vat.charAt(i) + product) % 10
        if (sum === 0) {
          sum = 10
        }
        product = (2 * sum) % 11
      }

      // Establish check digit.
      if (11 - product === 10) {
        checkDigit = 0
      } else {
        checkDigit = 11 - product
      }

      // Compare it with the last two characters of the VAT number. If the same, then it is a valid
      // check digit.
      expect = +vat.slice(8, 9)
      return checkDigit === expect
    },
    rules: {
      regex: [/^(DE)([1-9]\d{8})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.greece = {
    name: 'Greece',
    codes: ['GR', 'GRC', '300'],
    calcFn: function(vat) {
      var total = 0
      var expect

      // eight character numbers should be prefixed with an 0.
      if (vat.length === 8) {
        vat = '0' + vat
      }

      // Extract the next digit and multiply by the counter.
      for (var i = 0; i < 8; i++) {
        total += +vat.charAt(i) * this.rules.multipliers[i]
      }

      // Establish check digit.
      total = total % 11
      if (total > 9) {
        total = 0
      }

      // Compare it with the last character of the VAT number. If it's the same, then it's valid.
      expect = +vat.slice(8, 9)
      return total === expect
    },
    rules: {
      multipliers: [
        256,
        128,
        64,
        32,
        16,
        8,
        4,
        2
      ],
      regex: [/^(EL)(\d{9})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.hungary = {
    name: 'Hungary',
    codes: ['HU', 'HUN', '348'],
    calcFn: function(vat) {
      var total = 0
      var expect

      // Extract the next digit and multiply by the counter.
      for (var i = 0; i < 7; i++) {
        total += +vat.charAt(i) * this.rules.multipliers[i]
      }

      // Establish check digit.
      total = 10 - total % 10
      if (total === 10) total = 0

      // Compare it with the last character of the VAT number. If it's the same, then it's valid.
      expect = +vat.slice(7, 8)
      return total === expect
    },
    rules: {
      multipliers: [
        9,
        7,
        3,
        1,
        9,
        7,
        3
      ],
      regex: [/^(HU)(\d{8})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.ireland = {
    name: 'Ireland',
    codes: ['IE', 'IRL', '372'],
    calcFn: function(vat) {
      var total = 0
      var expect

      // If the code is type 1 format, we need to convert it to the new before performing the validation.
      if (this.rules.typeFormats.first.test(vat)) {
        vat = '0' + vat.substring(2, 7) + vat.substring(0, 1) + vat.substring(7, 8)
      }

      // Extract the next digit and multiply by the counter.
      for (var i = 0; i < 7; i++) {
        total += +vat.charAt(i) * this.rules.multipliers[i]
      }

      // If the number is type 3 then we need to include the trailing A or H in the calculation
      if (this.rules.typeFormats.third.test(vat)) {
        // Add in a multiplier for the character A (1*9=9) or H (8*9=72)
        if (vat.charAt(8) === 'H') {
          total += 72
        } else {
          total += 9
        }
      }

      // Establish check digit using modulus 23, and translate to char. equivalent.
      total = total % 23
      if (total === 0) {
        total = 'W'
      } else {
        total = String.fromCharCode(total + 64)
      }

      // Compare it with the eighth character of the VAT number. If it's the same, then it's valid.
      expect = vat.slice(7, 8)
      return total === expect
    },
    rules: {
      multipliers: [8, 7, 6, 5, 4, 3, 2],
      typeFormats: {
        first: /^\d[A-Z*+]/,
        third: /^\d{7}[A-Z][AH]$/
      },
      regex: [
        /^(IE)(\d{7}[A-W])$/,
        /^(IE)([7-9][A-Z*+)]\d{5}[A-W])$/,
        /^(IE)(\d{7}[A-W][AH])$/
      ]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.italy = {
    name: 'Italy',
    codes: ['IT', 'ITA', '380'],
    calcFn: function(vat) {
      var total = 0
      var temp
      var expect

      // The last three digits are the issuing office, and cannot exceed more 201, unless 999 or 888
      if (+vat.slice(0, 7) === 0) {
        return false
      }

      temp = +vat.slice(7, 10)
      if ((temp < 1) || (temp > 201) && temp !== 999 && temp !== 888) {
        return false
      }

      // Extract the next digit and multiply by the appropriate
      for (var i = 0; i < 10; i++) {
        temp = +vat.charAt(i) * this.rules.multipliers[i]
        if (temp > 9)
          total += Math.floor(temp / 10) + temp % 10
        else
          total += temp
      }

      // Establish check digit.
      total = 10 - total % 10
      if (total > 9) {
        total = 0
      }

      // Compare it with the last character of the VAT number. If it's the same, then it's valid.
      expect = +vat.slice(10, 11)
      return total === expect
    },
    rules: {
      multipliers: [1, 2, 1, 2, 1, 2, 1, 2, 1, 2],
      regex: [/^(IT)(\d{11})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.latvia = {
    name: 'Latvia',
    codes: ['LV', 'LVA', '428'],
    calcFn: function(vat) {
      var total = 0
      var expect

      // Differentiate between legal entities and natural bodies. For the latter we simply check that
      // the first six digits correspond to valid DDMMYY dates.
      if ((/^[0-3]/).test(vat)) {
        return !!(/^[0-3][0-9][0-1][0-9]/).test(vat)
      } else {
        // Extract the next digit and multiply by the counter.
        for (var i = 0; i < 10; i++) {
          total += +vat.charAt(i) * this.rules.multipliers[i]
        }

        // Establish check digits by getting modulus 11.
        if (total % 11 === 4 && vat[0] === 9) total = total - 45

        if (total % 11 === 4) {
          total = 4 - total % 11
        } else if (total % 11 > 4) {
          total = 14 - total % 11
        } else if (total % 11 < 4) {
          total = 3 - total % 11
        }

        // Compare it with the last character of the VAT number. If it's the same, then it's valid.
        expect = +vat.slice(10, 11)
        return total === expect
      }
    },
    rules: {
      multipliers: [9, 1, 4, 8, 3, 10, 2, 5, 7, 6],
      regex: [/^(LV)(\d{11})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.lithuania = {
    name: 'Lithuania',
    codes: ['LT', 'LTU', '440'],
    calcFn: function(vat) {
      function _extractDigit(vat, multiplier, key) {
        return +vat.charAt(key) * multiplier[key]
      }

      function _doubleCheckCalculation(vat, total, rules) {
        if (total % 11 === 10) {
          total = 0
          for (var i = 0; i < 8; i++) {
            total += _extractDigit(vat, rules.multipliers.short, i)
          }
        }

        return total
      }

      function extractDigit(vat, total) {
        for (var i = 0; i < 8; i++) {
          total += +vat.charAt(i) * (i + 1)
        }
        return total
      }

      function checkDigit(total) {
        total = total % 11
        if (total === 10) {
          total = 0
        }

        return total
      }

      function _check9DigitVat(vat, rules) {
        // 9 character VAT numbers are for legal persons
        var total = 0
        if (vat.length === 9) {
          // 8th character must be one
          if (!(/^\d{7}1/).test(vat)) return false

          // Extract the next digit and multiply by the counter+1.
          total = extractDigit(vat, total)

          // Can have a double check digit calculation!
          total = _doubleCheckCalculation(vat, total, rules)

          // Establish check digit.
          total = checkDigit(total)

          // Compare it with the last character of the VAT number. If it's the same, then it's valid.
          var expect = +vat.slice(8, 9)
          return total === expect
        }
        return false
      }

      function extractDigit12(vat, total, rules) {
        for (var k = 0; k < 11; k++) {
          total += _extractDigit(vat, rules.multipliers.med, k)
        }
        return total
      }

      function _doubleCheckCalculation12(vat, total, rules) {
        if (total % 11 === 10) {
          total = 0
          for (var l = 0; l < 11; l++) {
            total += _extractDigit(vat, rules.multipliers.alt, l)
          }
        }

        return total
      }

      function _check12DigitVat(vat, rules) {
        var total = 0

        // 12 character VAT numbers are for temporarily registered taxpayers
        if (vat.length === 12) {
          // 11th character must be one
          if (!(rules.check).test(vat)) return false

          // Extract the next digit and multiply by the counter+1.
          total = extractDigit12(vat, total, rules)

          // Can have a double check digit calculation!
          total = _doubleCheckCalculation12(vat, total, rules)

          // Establish check digit.
          total = checkDigit(total)

          // Compare it with the last character of the VAT number. If it's the same, then it's valid.
          var expect = +vat.slice(11, 12)
          return total === expect
        }

        return false
      }

      return _check9DigitVat(vat, this.rules) || _check12DigitVat(vat, this.rules)
    },
    rules: {
      multipliers: {
        short: [3, 4, 5, 6, 7, 8, 9, 1],
        med: [1, 2, 3, 4, 5, 6, 7, 8, 9, 1, 2],
        alt: [3, 4, 5, 6, 7, 8, 9, 1, 2, 3, 4]
      },
      check: /^\d{10}1/,
      regex: [/^(LT)(\d{9}|\d{12})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.luxembourg = {
    name: 'Luxembourg',
    codes: ['LU', 'LUX', '442'],
    calcFn: function(vat) {
      var expect = +vat.slice(6, 8)
      var checkDigit = +vat.slice(0, 6) % 89
      // Checks the check digits of a Luxembourg VAT number.

      return checkDigit === expect
    },
    rules: {
      regex: [/^(LU)(\d{8})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.malta = {
    name: 'Malta',
    codes: ['MT', 'MLT', '470'],
    calcFn: function(vat) {
      var total = 0
      var expect

      // Extract the next digit and multiply by the counter.
      for (var i = 0; i < 6; i++) {
        total += +vat.charAt(i) * this.rules.multipliers[i]
      }

      // Establish check digits by getting modulus 37.
      total = 37 - total % 37

      // Compare it with the last character of the VAT number. If it's the same, then it's valid.
      expect = +vat.slice(6, 8)
      return total === expect
    },
    rules: {
      multipliers: [3, 4, 6, 7, 8, 9],
      regex: [/^(MT)([1-9]\d{7})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.netherlands = {
    name: 'Netherlands',
    codes: ['NL', 'NLD', '528'],
    calcFn: function(vat) {
      var total = 0
      var expect

      // Extract the next digit and multiply by the counter.
      for (var i = 0; i < 8; i++) {
        total += +vat.charAt(i) * this.rules.multipliers[i]
      }

      // Establish check digits by getting modulus 11.
      total = total % 11
      if (total > 9) {
        total = 0
      }

      // Compare it with the last character of the VAT number. If it's the same, then it's valid.
      expect = +vat.slice(8, 9)
      return total === expect
    },
    rules: {
      multipliers: [9, 8, 7, 6, 5, 4, 3, 2],
      regex: [/^(NL)(\d{9})B\d{2}$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.norway = {
    name: 'Norway',
    codes: ['NO', 'NOR', '578'],
    calcFn: function(vat) {
      var total = 0
      var expect
      // See http://www.brreg.no/english/coordination/number.html

      // Extract the next digit and multiply by the counter.
      for (var i = 0; i < 8; i++) {
        total += +vat.charAt(i) * this.rules.multipliers[i]
      }

      // Establish check digits by getting modulus 11. Check digits > 9 are invalid
      total = 11 - total % 11

      if (total === 11) {
        total = 0
      }

      if (total < 10) {
        // Compare it with the last character of the VAT number. If it's the same, then it's valid.
        expect = +vat.slice(8, 9)
        return total === expect
      }
    },
    rules: {
      multipliers: [3, 2, 7, 6, 5, 4, 3, 2],
      regex: [/^(NO)(\d{9})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.poland = {
    name: 'Poland',
    codes: ['PL', 'POL', '616'],
    calcFn: function(vat) {
      var total = 0
      var expect

      // Extract the next digit and multiply by the counter.
      for (var i = 0; i < 9; i++) {
        total += +vat.charAt(i) * this.rules.multipliers[i]
      }

      // Establish check digits subtracting modulus 11 from 11.
      total = total % 11
      if (total > 9) {
        total = 0
      }

      // Compare it with the last character of the VAT number. If it's the same, then it's valid.
      expect = +vat.slice(9, 10)
      return total === expect
    },
    rules: {
      multipliers: [6, 5, 7, 2, 3, 4, 5, 6, 7],
      regex: [/^(PL)(\d{10})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.portugal = {
    name: 'Portugal',
    codes: ['PT', 'PRT', '620'],
    calcFn: function(vat) {
      var total = 0
      var expect

      // Extract the next digit and multiply by the counter.
      for (var i = 0; i < 8; i++) {
        total += +vat.charAt(i) * this.rules.multipliers[i]
      }

      // Establish check digits subtracting modulus 11 from 11.
      total = 11 - total % 11
      if (total > 9) {
        total = 0
      }

      // Compare it with the last character of the VAT number. If it's the same, then it's valid.
      expect = +vat.slice(8, 9)
      return total === expect
    },
    rules: {
      multipliers: [9, 8, 7, 6, 5, 4, 3, 2],
      regex: [/^(PT)(\d{9})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.romania = {
    name: 'Romania',
    codes: ['RO', 'ROU', '642'],
    calcFn: function(vat) {
      var total = 0
      var expect

      // Extract the next digit and multiply by the counter.
      var vatLength = vat.length
      var multipliers = this.rules.multipliers.slice(10 - vatLength)

      for (var i = 0; i < vat.length - 1; i++) {
        total += +vat.charAt(i) * multipliers[i]
      }

      // Establish check digits by getting modulus 11.
      total = (10 * total) % 11
      if (total === 10) total = 0

      // Compare it with the last character of the VAT number. If it's the same, then it's valid.
      expect = +vat.slice(vat.length - 1, vat.length)
      return total === expect
    },
    rules: {
      multipliers: [7, 5, 3, 2, 1, 7, 5, 3, 2],
      regex: [/^(RO)([1-9]\d{1,9})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.russia = {
    name: 'Russian Federation',
    codes: ['RU', 'RUS', '643'],
    calcFn: function(vat) {
      function _check10DigitINN(vat, rules) {
        var total = 0

        if (vat.length === 10) {
          for (var i = 0; i < 10; i++) {
            total += +vat.charAt(i) * rules.multipliers.m_1[i]
          }

          total = total % 11
          if (total > 9) {
            total = total % 10
          }

          // Compare it with the last character of the VAT number. If it is the same, then it's valid
          var expect = +vat.slice(9, 10)
          return total === expect
        }

        return false
      }

      function _check12DigitINN(vat, rules) {
        var total1 = 0
        var total2 = 0

        if (vat.length === 12) {
          for (var j = 0; j < 11; j++) {
            total1 += +vat.charAt(j) * rules.multipliers.m_2[j]
          }

          total1 = total1 % 11

          if (total1 > 9) {
            total1 = total1 % 10
          }

          for (var k = 0; k < 11; k++) {
            total2 += +vat.charAt(k) * rules.multipliers.m_3[k]
          }

          total2 = total2 % 11
          if (total2 > 9) {
            total2 = total2 % 10
          }

          // Compare the first check with the 11th character and the second check with the 12th and last
          // character of the VAT number. If they're both the same, then it's valid
          var expect = (total1 === +vat.slice(10, 11))
          var expect2 = (total2 === +vat.slice(11, 12))
          return (expect) && (expect2)
        }

        return false
      }

      // See http://russianpartner.biz/test_inn.html for algorithm
      return _check10DigitINN(vat, this.rules) || _check12DigitINN(vat, this.rules)
    },
    rules: {
      multipliers: {
        m_1: [2, 4, 10, 3, 5, 9, 4, 6, 8, 0],
        m_2: [7, 2, 4, 10, 3, 5, 9, 4, 6, 8, 0],
        m_3: [3, 7, 2, 4, 10, 3, 5, 9, 4, 6, 8, 0]
      },
      regex: [/^(RU)(\d{10}|\d{12})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.serbia = {
    name: 'Serbia',
    codes: ['RS', 'SRB', '688'],
    calcFn: function(vat) {
      // Checks the check digits of a Serbian VAT number using ISO 7064, MOD 11-10 for check digit.

      var product = 10
      var sum = 0
      var checkDigit

      for (var i = 0; i < 8; i++) {
        // Extract the next digit and implement the algorithm
        sum = (+vat.charAt(i) + product) % 10
        if (sum === 0) {
          sum = 10
        }
        product = (2 * sum) % 11
      }

      // Now check that we have the right check digit
      var expect = 1
      checkDigit = (product + (+vat.slice(8, 9))) % 10
      return checkDigit === expect
    },
    rules: {
      regex: [/^(RS)(\d{9})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.slovakia_republic = {
    name: 'Slovakia_',
    codes: ['SK', 'SVK', '703'],
    calcFn: function(vat) {
      var expect = 0
      var checkDigit = (vat % 11)
      return checkDigit === expect
    },
    rules: {
      regex: [/^(SK)([1-9]\d[2346-9]\d{7})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.slovenia = {
    name: 'Slovenia',
    codes: ['SI', 'SVN', '705'],
    calcFn: function(vat) {
      var total = 0
      var expect

      // Extract the next digit and multiply by the counter.
      for (var i = 0; i < 7; i++) {
        total += +vat.charAt(i) * this.rules.multipliers[i]
      }

      // Establish check digits using modulus 11
      total = 11 - total % 11
      if (total === 10) {
        total = 0
      }

      // Compare the number with the last character of the VAT number. If it is the
      // same, then it's a valid check digit.
      expect = +vat.slice(7, 8)
      return !!(total !== 11 && total === expect)
    },
    rules: {
      multipliers: [8, 7, 6, 5, 4, 3, 2],
      regex: [/^(SI)([1-9]\d{7})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.spain = {
    name: 'Spain',
    codes: ['ES', 'ESP', '724'],
    calcFn: function(vat) {
      var i = 0
      var total = 0
      var temp
      var expect

      // National juridical entities
      if (this.rules.additional[0].test(vat)) {
        // Extract the next digit and multiply by the counter.
        for (i = 0; i < 7; i++) {
          temp = vat.charAt(i + 1) * this.rules.multipliers[i]
          if (temp > 9)
            total += Math.floor(temp / 10) + temp % 10
          else
            total += temp
        }
        // Now calculate the check digit itself.
        total = 10 - total % 10
        if (total === 10) {
          total = 0
        }

        // Compare it with the last character of the VAT number. If it's the same, then it's valid.
        expect = +vat.slice(8, 9)
        return total === expect
      }

      // Juridical entities other than national ones
      else if (this.rules.additional[1].test(vat)) {
        // Extract the next digit and multiply by the counter.
        for (i = 0; i < 7; i++) {
          temp = vat.charAt(i + 1) * this.rules.multipliers[i]
          if (temp > 9)
            total += Math.floor(temp / 10) + temp % 10
          else
            total += temp
        }

        // Now calculate the check digit itself.
        total = 10 - total % 10
        total = String.fromCharCode(total + 64)

        // Compare it with the last character of the VAT number. If it's the same, then it's valid.
        expect = vat.slice(8, 9)
        return total === expect
      }

      // Personal number (NIF) (starting with numeric of Y or Z)
      else if (this.rules.additional[2].test(vat)) {
        var tempnumber = vat
        if (tempnumber.substring(0, 1) === 'Y') tempnumber = tempnumber.replace(/Y/, '1')
        if (tempnumber.substring(0, 1) === 'Z') tempnumber = tempnumber.replace(/Z/, '2')
        expect = 'TRWAGMYFPDXBNJZSQVHLCKE'.charAt(+tempnumber.substring(0, 8) % 23)
        return tempnumber.charAt(8) === expect
      }

      // Personal number (NIF) (starting with K, L, M, or X)
      else if (this.rules.additional[3].test(vat)) {
        expect = 'TRWAGMYFPDXBNJZSQVHLCKE'.charAt(+vat.substring(1, 8) % 23)
        return vat.charAt(8) === expect
      } else return false
    },
    rules: {
      multipliers: [2, 1, 2, 1, 2, 1, 2],
      regex: [
        /^(ES)([A-Z]\d{8})$/,
        /^(ES)([A-HN-SW]\d{7}[A-J])$/,
        /^(ES)([0-9YZ]\d{7}[A-Z])$/,
        /^(ES)([KLMX]\d{7}[A-Z])$/
      ],
      additional: [
        /^[A-H|J|U|V]\d{8}$/,
        /^[A-H|N-S|W]\d{7}[A-J]$/,
        /^[0-9|Y|Z]\d{7}[A-Z]$/,
        /^[K|L|M|X]\d{7}[A-Z]$/
      ]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.sweden = {
    name: 'Sweden',
    codes: ['SE', 'SWE', '752'],
    calcFn: function(vat) {
      var expect

      // Calculate R where R = R1 + R3 + R5 + R7 + R9, and Ri = INT(Ci/5) + (Ci*2) modulo 10
      var R = 0
      var digit
      for (var i = 0; i < 9; i = i + 2) {
        digit = +vat.charAt(i)
        R += Math.floor(digit / 5) + ((digit * 2) % 10)
      }

      // Calculate S where S = C2 + C4 + C6 + C8
      var S = 0
      for (var j = 1; j < 9; j = j + 2) {
        S += +vat.charAt(j)
      }

      var checkDigit = (10 - (R + S) % 10) % 10

      // Compare it with the last character of the VAT number. If it's the same, then it's valid.
      expect = +vat.slice(9, 10)

      return checkDigit === expect
    },
    rules: {
      regex: [/^(SE)(\d{10}01)$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.switzerland = {
    name: 'Switzerland',
    codes: ['CH', 'CHE', '756'],
    calcFn: function(vat) {
      var total = 0
      for (var i = 0; i < 8; i++) {
        total += +vat.charAt(i) * this.rules.multipliers[i]
      }

      // Establish check digit.
      total = 11 - total % 11
      if (total === 10) return false
      if (total === 11) total = 0

      // Check to see if the check digit given is correct, If not, we have an error with the VAT number
      var expect = +vat.substr(8, 1)
      return total === expect
    },
    rules: {
      multipliers: [5, 4, 3, 2, 7, 6, 5, 4],
      regex: [/^(CHE)(\d{9})(MWST)?$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.united_kingdom = {
    name: 'United Kingdom',
    codes: ['GB', 'GBR', '826'],
    calcFn: function(vat) {
      var total = 0
      var expect

      // Government departments
      if (vat.substr(0, 2) === 'GD') {
        expect = 500
        return vat.substr(2, 3) < expect
      }

      // Health authorities
      if (vat.substr(0, 2) === 'HA') {
        expect = 499
        return vat.substr(2, 3) > expect
      }

      // Standard and commercial numbers

      // 0 VAT numbers disallowed!
      if (+vat.slice(0) === 0) return false

      // Check range is OK for modulus 97 calculation
      var no = +vat.slice(0, 7)

      // Extract the next digit and multiply by the counter.
      for (var i = 0; i < 7; i++) {
        total += +vat.charAt(i) * this.rules.multipliers[i]
      }

      // Old numbers use a simple 97 modulus, but new numbers use an adaptation of that (less 55). Our
      // VAT number could use either system, so we check it against both.

      // Establish check digits by subtracting 97 from total until negative.
      var checkDigit = total
      while (checkDigit > 0) {
        checkDigit = checkDigit - 97
      }

      // Get the absolute value and compare it with the last two characters of the VAT number. If the
      // same, then it is a valid traditional check digit. However, even then the number must fit within
      // certain specified ranges.
      checkDigit = Math.abs(checkDigit)
      if (checkDigit === +vat.slice(7, 9) && no < 9990001 && (no < 100000 || no > 999999) && (no < 9490001 || no > 9700000)) return true

      // Now try the new method by subtracting 55 from the check digit if we can - else add 42
      if (checkDigit >= 55)
        checkDigit = checkDigit - 55
      else
        checkDigit = checkDigit + 42
      expect = +vat.slice(7, 9)
      return !!(checkDigit === expect && no > 1000000)
    },
    rules: {
      multipliers: [8, 7, 6, 5, 4, 3, 2],
      regex: [
        /^(GB)?(\d{9})$/,
        /^(GB)?(\d{12})$/,
        /^(GB)?(GD\d{3})$/,
        /^(GB)?(HA\d{3})$/
      ]
    }
  }


  //Support of node.js

  if (typeof module === 'object' && module.exports) module.exports = exports

  return exports

})();
/*!
 * hoverIntent v1.8.1 // 2014.08.11 // jQuery v1.9.1+
 * http://cherne.net/brian/resources/jquery.hoverIntent.html
 *
 * You may use hoverIntent under the terms of the MIT license. Basically that
 * means you are free to use hoverIntent as long as this header is left intact.
 * Copyright 2007, 2014 Brian Cherne
 */
 
/* hoverIntent is similar to jQuery's built-in "hover" method except that
 * instead of firing the handlerIn function immediately, hoverIntent checks
 * to see if the user's mouse has slowed down (beneath the sensitivity
 * threshold) before firing the event. The handlerOut function is only
 * called after a matching handlerIn.
 *
 * // basic usage ... just like .hover()
 * .hoverIntent( handlerIn, handlerOut )
 * .hoverIntent( handlerInOut )
 *
 * // basic usage ... with event delegation!
 * .hoverIntent( handlerIn, handlerOut, selector )
 * .hoverIntent( handlerInOut, selector )
 *
 * // using a basic configuration object
 * .hoverIntent( config )
 *
 * @param  handlerIn   function OR configuration object
 * @param  handlerOut  function OR selector for delegation OR undefined
 * @param  selector    selector OR undefined
 * @author Brian Cherne <brian(at)cherne(dot)net>
 */
(function($) {
    $.fn.hoverIntent = function(handlerIn,handlerOut,selector) {

        // default configuration values
        var cfg = {
            interval: 100,
            sensitivity: 6,
            timeout: 0
        };

        if ( typeof handlerIn === "object" ) {
            cfg = $.extend(cfg, handlerIn );
        } else if ($.isFunction(handlerOut)) {
            cfg = $.extend(cfg, { over: handlerIn, out: handlerOut, selector: selector } );
        } else {
            cfg = $.extend(cfg, { over: handlerIn, out: handlerIn, selector: handlerOut } );
        }

        // instantiate variables
        // cX, cY = current X and Y position of mouse, updated by mousemove event
        // pX, pY = previous X and Y position of mouse, set by mouseover and polling interval
        var cX, cY, pX, pY;

        // A private function for getting mouse position
        var track = function(ev) {
            cX = ev.pageX;
            cY = ev.pageY;
        };

        // A private function for comparing current and previous mouse position
        var compare = function(ev,ob) {
            ob.hoverIntent_t = clearTimeout(ob.hoverIntent_t);
            // compare mouse positions to see if they've crossed the threshold
            if ( Math.sqrt( (pX-cX)*(pX-cX) + (pY-cY)*(pY-cY) ) < cfg.sensitivity ) {
                $(ob).off("mousemove.hoverIntent",track);
                // set hoverIntent state to true (so mouseOut can be called)
                ob.hoverIntent_s = true;
                return cfg.over.apply(ob,[ev]);
            } else {
                // set previous coordinates for next time
                pX = cX; pY = cY;
                // use self-calling timeout, guarantees intervals are spaced out properly (avoids JavaScript timer bugs)
                ob.hoverIntent_t = setTimeout( function(){compare(ev, ob);} , cfg.interval );
            }
        };

        // A private function for delaying the mouseOut function
        var delay = function(ev,ob) {
            ob.hoverIntent_t = clearTimeout(ob.hoverIntent_t);
            ob.hoverIntent_s = false;
            return cfg.out.apply(ob,[ev]);
        };

        // A private function for handling mouse 'hovering'
        var handleHover = function(e) {
            // copy objects to be passed into t (required for event object to be passed in IE)
            var ev = $.extend({},e);
            var ob = this;

            // cancel hoverIntent timer if it exists
            if (ob.hoverIntent_t) { ob.hoverIntent_t = clearTimeout(ob.hoverIntent_t); }

            // if e.type === "mouseenter"
            if (e.type === "mouseenter") {
                // set "previous" X and Y position based on initial entry point
                pX = ev.pageX; pY = ev.pageY;
                // update "current" X and Y position based on mousemove
                $(ob).on("mousemove.hoverIntent",track);
                // start polling interval (self-calling timeout) to compare mouse coordinates over time
                if (!ob.hoverIntent_s) { ob.hoverIntent_t = setTimeout( function(){compare(ev,ob);} , cfg.interval );}

                // else e.type == "mouseleave"
            } else {
                // unbind expensive mousemove event
                $(ob).off("mousemove.hoverIntent",track);
                // if hoverIntent state is true, then call the mouseOut function after the specified delay
                if (ob.hoverIntent_s) { ob.hoverIntent_t = setTimeout( function(){delay(ev,ob);} , cfg.timeout );}
            }
        };

        // listen for mouseenter and mouseleave
        return this.on({'mouseenter.hoverIntent':handleHover,'mouseleave.hoverIntent':handleHover}, cfg.selector);
    };
})(jQuery);

/*! =======================================================
                      VERSION  9.2.0              
========================================================= */
"use strict";function _typeof(a){return a&&"undefined"!=typeof Symbol&&a.constructor===Symbol?"symbol":typeof a}/*! =========================================================
 * bootstrap-slider.js
 *
 * Maintainers:
 *		Kyle Kemp
 *			- Twitter: @seiyria
 *			- Github:  seiyria
 *		Rohit Kalkur
 *			- Twitter: @Rovolutionary
 *			- Github:  rovolution
 *
 * =========================================================
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================= */
var windowIsDefined="object"===("undefined"==typeof window?"undefined":_typeof(window));!function(a){if("function"==typeof define&&define.amd)define(["jquery"],a);else if("object"===("undefined"==typeof module?"undefined":_typeof(module))&&module.exports){var b;try{b=require("jquery")}catch(c){b=null}module.exports=a(b)}else window&&(window.Slider=a(window.jQuery))}(function(a){var b="slider",c="bootstrapSlider";windowIsDefined&&!window.console&&(window.console={}),windowIsDefined&&!window.console.log&&(window.console.log=function(){}),windowIsDefined&&!window.console.warn&&(window.console.warn=function(){});var d;return function(a){function b(){}function c(a){function c(b){b.prototype.option||(b.prototype.option=function(b){a.isPlainObject(b)&&(this.options=a.extend(!0,this.options,b))})}function e(b,c){a.fn[b]=function(e){if("string"==typeof e){for(var g=d.call(arguments,1),h=0,i=this.length;i>h;h++){var j=this[h],k=a.data(j,b);if(k)if(a.isFunction(k[e])&&"_"!==e.charAt(0)){var l=k[e].apply(k,g);if(void 0!==l&&l!==k)return l}else f("no such method '"+e+"' for "+b+" instance");else f("cannot call methods on "+b+" prior to initialization; attempted to call '"+e+"'")}return this}var m=this.map(function(){var d=a.data(this,b);return d?(d.option(e),d._init()):(d=new c(this,e),a.data(this,b,d)),a(this)});return!m||m.length>1?m:m[0]}}if(a){var f="undefined"==typeof console?b:function(a){console.error(a)};return a.bridget=function(a,b){c(b),e(a,b)},a.bridget}}var d=Array.prototype.slice;c(a)}(a),function(a){function e(b,c){function d(a,b){var c="data-slider-"+b.replace(/_/g,"-"),d=a.getAttribute(c);try{return JSON.parse(d)}catch(e){return d}}this._state={value:null,enabled:null,offset:null,size:null,percentage:null,inDrag:!1,over:!1},"string"==typeof b?this.element=document.querySelector(b):b instanceof HTMLElement&&(this.element=b),c=c?c:{};for(var e=Object.keys(this.defaultOptions),f=0;f<e.length;f++){var h=e[f],i=c[h];i="undefined"!=typeof i?i:d(this.element,h),i=null!==i?i:this.defaultOptions[h],this.options||(this.options={}),this.options[h]=i}"vertical"!==this.options.orientation||"top"!==this.options.tooltip_position&&"bottom"!==this.options.tooltip_position?"horizontal"!==this.options.orientation||"left"!==this.options.tooltip_position&&"right"!==this.options.tooltip_position||(this.options.tooltip_position="top"):this.options.tooltip_position="right";var j,k,l,m,n,o=this.element.style.width,p=!1,q=this.element.parentNode;if(this.sliderElem)p=!0;else{this.sliderElem=document.createElement("div"),this.sliderElem.className="slider";var r=document.createElement("div");if(r.className="slider-track",k=document.createElement("div"),k.className="slider-track-low",j=document.createElement("div"),j.className="slider-selection",l=document.createElement("div"),l.className="slider-track-high",m=document.createElement("div"),m.className="slider-handle min-slider-handle",m.setAttribute("role","slider"),m.setAttribute("aria-valuemin",this.options.min),m.setAttribute("aria-valuemax",this.options.max),n=document.createElement("div"),n.className="slider-handle max-slider-handle",n.setAttribute("role","slider"),n.setAttribute("aria-valuemin",this.options.min),n.setAttribute("aria-valuemax",this.options.max),r.appendChild(k),r.appendChild(j),r.appendChild(l),this.rangeHighlightElements=[],Array.isArray(this.options.rangeHighlights)&&this.options.rangeHighlights.length>0)for(var s=0;s<this.options.rangeHighlights.length;s++){var t=document.createElement("div");t.className="slider-rangeHighlight slider-selection",this.rangeHighlightElements.push(t),r.appendChild(t)}var u=Array.isArray(this.options.labelledby);if(u&&this.options.labelledby[0]&&m.setAttribute("aria-labelledby",this.options.labelledby[0]),u&&this.options.labelledby[1]&&n.setAttribute("aria-labelledby",this.options.labelledby[1]),!u&&this.options.labelledby&&(m.setAttribute("aria-labelledby",this.options.labelledby),n.setAttribute("aria-labelledby",this.options.labelledby)),this.ticks=[],Array.isArray(this.options.ticks)&&this.options.ticks.length>0){for(this.ticksContainer=document.createElement("div"),this.ticksContainer.className="slider-tick-container",f=0;f<this.options.ticks.length;f++){var v=document.createElement("div");v.className="slider-tick",this.ticks.push(v),this.ticksContainer.appendChild(v)}j.className+=" tick-slider-selection"}if(this.tickLabels=[],Array.isArray(this.options.ticks_labels)&&this.options.ticks_labels.length>0)for(this.tickLabelContainer=document.createElement("div"),this.tickLabelContainer.className="slider-tick-label-container",f=0;f<this.options.ticks_labels.length;f++){var w=document.createElement("div"),x=0===this.options.ticks_positions.length,y=this.options.reversed&&x?this.options.ticks_labels.length-(f+1):f;w.className="slider-tick-label",w.innerHTML=this.options.ticks_labels[y],this.tickLabels.push(w),this.tickLabelContainer.appendChild(w)}var z=function(a){var b=document.createElement("div");b.className="tooltip-arrow";var c=document.createElement("div");c.className="tooltip-inner",a.appendChild(b),a.appendChild(c)},A=document.createElement("div");A.className="tooltip tooltip-main",A.setAttribute("role","presentation"),z(A);var B=document.createElement("div");B.className="tooltip tooltip-min",B.setAttribute("role","presentation"),z(B);var C=document.createElement("div");C.className="tooltip tooltip-max",C.setAttribute("role","presentation"),z(C),this.sliderElem.appendChild(r),this.sliderElem.appendChild(A),this.sliderElem.appendChild(B),this.sliderElem.appendChild(C),this.tickLabelContainer&&this.sliderElem.appendChild(this.tickLabelContainer),this.ticksContainer&&this.sliderElem.appendChild(this.ticksContainer),this.sliderElem.appendChild(m),this.sliderElem.appendChild(n),q.insertBefore(this.sliderElem,this.element),this.element.style.display="none"}if(a&&(this.$element=a(this.element),this.$sliderElem=a(this.sliderElem)),this.eventToCallbackMap={},this.sliderElem.id=this.options.id,this.touchCapable="ontouchstart"in window||window.DocumentTouch&&document instanceof window.DocumentTouch,this.touchX=0,this.touchY=0,this.tooltip=this.sliderElem.querySelector(".tooltip-main"),this.tooltipInner=this.tooltip.querySelector(".tooltip-inner"),this.tooltip_min=this.sliderElem.querySelector(".tooltip-min"),this.tooltipInner_min=this.tooltip_min.querySelector(".tooltip-inner"),this.tooltip_max=this.sliderElem.querySelector(".tooltip-max"),this.tooltipInner_max=this.tooltip_max.querySelector(".tooltip-inner"),g[this.options.scale]&&(this.options.scale=g[this.options.scale]),p===!0&&(this._removeClass(this.sliderElem,"slider-horizontal"),this._removeClass(this.sliderElem,"slider-vertical"),this._removeClass(this.tooltip,"hide"),this._removeClass(this.tooltip_min,"hide"),this._removeClass(this.tooltip_max,"hide"),["left","top","width","height"].forEach(function(a){this._removeProperty(this.trackLow,a),this._removeProperty(this.trackSelection,a),this._removeProperty(this.trackHigh,a)},this),[this.handle1,this.handle2].forEach(function(a){this._removeProperty(a,"left"),this._removeProperty(a,"top")},this),[this.tooltip,this.tooltip_min,this.tooltip_max].forEach(function(a){this._removeProperty(a,"left"),this._removeProperty(a,"top"),this._removeProperty(a,"margin-left"),this._removeProperty(a,"margin-top"),this._removeClass(a,"right"),this._removeClass(a,"top")},this)),"vertical"===this.options.orientation?(this._addClass(this.sliderElem,"slider-vertical"),this.stylePos="top",this.mousePos="pageY",this.sizePos="offsetHeight"):(this._addClass(this.sliderElem,"slider-horizontal"),this.sliderElem.style.width=o,this.options.orientation="horizontal",this.stylePos="left",this.mousePos="pageX",this.sizePos="offsetWidth"),this._setTooltipPosition(),Array.isArray(this.options.ticks)&&this.options.ticks.length>0&&(this.options.max=Math.max.apply(Math,this.options.ticks),this.options.min=Math.min.apply(Math,this.options.ticks)),Array.isArray(this.options.value)?(this.options.range=!0,this._state.value=this.options.value):this.options.range?this._state.value=[this.options.value,this.options.max]:this._state.value=this.options.value,this.trackLow=k||this.trackLow,this.trackSelection=j||this.trackSelection,this.trackHigh=l||this.trackHigh,"none"===this.options.selection&&(this._addClass(this.trackLow,"hide"),this._addClass(this.trackSelection,"hide"),this._addClass(this.trackHigh,"hide")),this.handle1=m||this.handle1,this.handle2=n||this.handle2,p===!0)for(this._removeClass(this.handle1,"round triangle"),this._removeClass(this.handle2,"round triangle hide"),f=0;f<this.ticks.length;f++)this._removeClass(this.ticks[f],"round triangle hide");var D=["round","triangle","custom"],E=-1!==D.indexOf(this.options.handle);if(E)for(this._addClass(this.handle1,this.options.handle),this._addClass(this.handle2,this.options.handle),f=0;f<this.ticks.length;f++)this._addClass(this.ticks[f],this.options.handle);this._state.offset=this._offset(this.sliderElem),this._state.size=this.sliderElem[this.sizePos],this.setValue(this._state.value),this.handle1Keydown=this._keydown.bind(this,0),this.handle1.addEventListener("keydown",this.handle1Keydown,!1),this.handle2Keydown=this._keydown.bind(this,1),this.handle2.addEventListener("keydown",this.handle2Keydown,!1),this.mousedown=this._mousedown.bind(this),this.touchstart=this._touchstart.bind(this),this.touchmove=this._touchmove.bind(this),this.touchCapable&&(this.sliderElem.addEventListener("touchstart",this.touchstart,!1),this.sliderElem.addEventListener("touchmove",this.touchmove,!1)),this.sliderElem.addEventListener("mousedown",this.mousedown,!1),this.resize=this._resize.bind(this),window.addEventListener("resize",this.resize,!1),"hide"===this.options.tooltip?(this._addClass(this.tooltip,"hide"),this._addClass(this.tooltip_min,"hide"),this._addClass(this.tooltip_max,"hide")):"always"===this.options.tooltip?(this._showTooltip(),this._alwaysShowTooltip=!0):(this.showTooltip=this._showTooltip.bind(this),this.hideTooltip=this._hideTooltip.bind(this),this.sliderElem.addEventListener("mouseenter",this.showTooltip,!1),this.sliderElem.addEventListener("mouseleave",this.hideTooltip,!1),this.handle1.addEventListener("focus",this.showTooltip,!1),this.handle1.addEventListener("blur",this.hideTooltip,!1),this.handle2.addEventListener("focus",this.showTooltip,!1),this.handle2.addEventListener("blur",this.hideTooltip,!1)),this.options.enabled?this.enable():this.disable()}var f={formatInvalidInputErrorMsg:function(a){return"Invalid input value '"+a+"' passed in"},callingContextNotSliderInstance:"Calling context element does not have instance of Slider bound to it. Check your code to make sure the JQuery object returned from the call to the slider() initializer is calling the method"},g={linear:{toValue:function(a){var b=a/100*(this.options.max-this.options.min),c=!0;if(this.options.ticks_positions.length>0){for(var d,e,f,g=0,h=1;h<this.options.ticks_positions.length;h++)if(a<=this.options.ticks_positions[h]){d=this.options.ticks[h-1],f=this.options.ticks_positions[h-1],e=this.options.ticks[h],g=this.options.ticks_positions[h];break}var i=(a-f)/(g-f);b=d+i*(e-d),c=!1}var j=c?this.options.min:0,k=j+Math.round(b/this.options.step)*this.options.step;return k<this.options.min?this.options.min:k>this.options.max?this.options.max:k},toPercentage:function(a){if(this.options.max===this.options.min)return 0;if(this.options.ticks_positions.length>0){for(var b,c,d,e=0,f=0;f<this.options.ticks.length;f++)if(a<=this.options.ticks[f]){b=f>0?this.options.ticks[f-1]:0,d=f>0?this.options.ticks_positions[f-1]:0,c=this.options.ticks[f],e=this.options.ticks_positions[f];break}if(f>0){var g=(a-b)/(c-b);return d+g*(e-d)}}return 100*(a-this.options.min)/(this.options.max-this.options.min)}},logarithmic:{toValue:function(a){var b=0===this.options.min?0:Math.log(this.options.min),c=Math.log(this.options.max),d=Math.exp(b+(c-b)*a/100);return d=this.options.min+Math.round((d-this.options.min)/this.options.step)*this.options.step,d<this.options.min?this.options.min:d>this.options.max?this.options.max:d},toPercentage:function(a){if(this.options.max===this.options.min)return 0;var b=Math.log(this.options.max),c=0===this.options.min?0:Math.log(this.options.min),d=0===a?0:Math.log(a);return 100*(d-c)/(b-c)}}};d=function(a,b){return e.call(this,a,b),this},d.prototype={_init:function(){},constructor:d,defaultOptions:{id:"",min:0,max:10,step:1,precision:0,orientation:"horizontal",value:5,range:!1,selection:"before",tooltip:"show",tooltip_split:!1,handle:"round",reversed:!1,enabled:!0,formatter:function(a){return Array.isArray(a)?a[0]+" : "+a[1]:a},natural_arrow_keys:!1,ticks:[],ticks_positions:[],ticks_labels:[],ticks_snap_bounds:0,scale:"linear",focus:!1,tooltip_position:null,labelledby:null,rangeHighlights:[]},getElement:function(){return this.sliderElem},getValue:function(){return this.options.range?this._state.value:this._state.value[0]},setValue:function(a,b,c){a||(a=0);var d=this.getValue();this._state.value=this._validateInputValue(a);var e=this._applyPrecision.bind(this);this.options.range?(this._state.value[0]=e(this._state.value[0]),this._state.value[1]=e(this._state.value[1]),this._state.value[0]=Math.max(this.options.min,Math.min(this.options.max,this._state.value[0])),this._state.value[1]=Math.max(this.options.min,Math.min(this.options.max,this._state.value[1]))):(this._state.value=e(this._state.value),this._state.value=[Math.max(this.options.min,Math.min(this.options.max,this._state.value))],this._addClass(this.handle2,"hide"),"after"===this.options.selection?this._state.value[1]=this.options.max:this._state.value[1]=this.options.min),this.options.max>this.options.min?this._state.percentage=[this._toPercentage(this._state.value[0]),this._toPercentage(this._state.value[1]),100*this.options.step/(this.options.max-this.options.min)]:this._state.percentage=[0,0,100],this._layout();var f=this.options.range?this._state.value:this._state.value[0];return this._setDataVal(f),b===!0&&this._trigger("slide",f),d!==f&&c===!0&&this._trigger("change",{oldValue:d,newValue:f}),this},destroy:function(){this._removeSliderEventHandlers(),this.sliderElem.parentNode.removeChild(this.sliderElem),this.element.style.display="",this._cleanUpEventCallbacksMap(),this.element.removeAttribute("data"),a&&(this._unbindJQueryEventHandlers(),this.$element.removeData("slider"))},disable:function(){return this._state.enabled=!1,this.handle1.removeAttribute("tabindex"),this.handle2.removeAttribute("tabindex"),this._addClass(this.sliderElem,"slider-disabled"),this._trigger("slideDisabled"),this},enable:function(){return this._state.enabled=!0,this.handle1.setAttribute("tabindex",0),this.handle2.setAttribute("tabindex",0),this._removeClass(this.sliderElem,"slider-disabled"),this._trigger("slideEnabled"),this},toggle:function(){return this._state.enabled?this.disable():this.enable(),this},isEnabled:function(){return this._state.enabled},on:function(a,b){return this._bindNonQueryEventHandler(a,b),this},off:function(b,c){a?(this.$element.off(b,c),this.$sliderElem.off(b,c)):this._unbindNonQueryEventHandler(b,c)},getAttribute:function(a){return a?this.options[a]:this.options},setAttribute:function(a,b){return this.options[a]=b,this},refresh:function(){return this._removeSliderEventHandlers(),e.call(this,this.element,this.options),a&&a.data(this.element,"slider",this),this},relayout:function(){return this._resize(),this._layout(),this},_removeSliderEventHandlers:function(){this.handle1.removeEventListener("keydown",this.handle1Keydown,!1),this.handle2.removeEventListener("keydown",this.handle2Keydown,!1),this.showTooltip&&(this.handle1.removeEventListener("focus",this.showTooltip,!1),this.handle2.removeEventListener("focus",this.showTooltip,!1)),this.hideTooltip&&(this.handle1.removeEventListener("blur",this.hideTooltip,!1),this.handle2.removeEventListener("blur",this.hideTooltip,!1)),this.showTooltip&&this.sliderElem.removeEventListener("mouseenter",this.showTooltip,!1),this.hideTooltip&&this.sliderElem.removeEventListener("mouseleave",this.hideTooltip,!1),this.sliderElem.removeEventListener("touchstart",this.touchstart,!1),this.sliderElem.removeEventListener("touchmove",this.touchmove,!1),this.sliderElem.removeEventListener("mousedown",this.mousedown,!1),window.removeEventListener("resize",this.resize,!1)},_bindNonQueryEventHandler:function(a,b){void 0===this.eventToCallbackMap[a]&&(this.eventToCallbackMap[a]=[]),this.eventToCallbackMap[a].push(b)},_unbindNonQueryEventHandler:function(a,b){var c=this.eventToCallbackMap[a];if(void 0!==c)for(var d=0;d<c.length;d++)if(c[d]===b){c.splice(d,1);break}},_cleanUpEventCallbacksMap:function(){for(var a=Object.keys(this.eventToCallbackMap),b=0;b<a.length;b++){var c=a[b];this.eventToCallbackMap[c]=null}},_showTooltip:function(){this.options.tooltip_split===!1?(this._addClass(this.tooltip,"in"),this.tooltip_min.style.display="none",this.tooltip_max.style.display="none"):(this._addClass(this.tooltip_min,"in"),this._addClass(this.tooltip_max,"in"),this.tooltip.style.display="none"),this._state.over=!0},_hideTooltip:function(){this._state.inDrag===!1&&this.alwaysShowTooltip!==!0&&(this._removeClass(this.tooltip,"in"),this._removeClass(this.tooltip_min,"in"),this._removeClass(this.tooltip_max,"in")),this._state.over=!1},_layout:function(){var a;if(a=this.options.reversed?[100-this._state.percentage[0],this.options.range?100-this._state.percentage[1]:this._state.percentage[1]]:[this._state.percentage[0],this._state.percentage[1]],this.handle1.style[this.stylePos]=a[0]+"%",this.handle1.setAttribute("aria-valuenow",this._state.value[0]),this.handle2.style[this.stylePos]=a[1]+"%",this.handle2.setAttribute("aria-valuenow",this._state.value[1]),this.rangeHighlightElements.length>0&&Array.isArray(this.options.rangeHighlights)&&this.options.rangeHighlights.length>0)for(var b=0;b<this.options.rangeHighlights.length;b++){var c=this._toPercentage(this.options.rangeHighlights[b].start),d=this._toPercentage(this.options.rangeHighlights[b].end),e=this._createHighlightRange(c,d);e?"vertical"===this.options.orientation?(this.rangeHighlightElements[b].style.top=e.start+"%",this.rangeHighlightElements[b].style.height=e.size+"%"):(this.rangeHighlightElements[b].style.left=e.start+"%",this.rangeHighlightElements[b].style.width=e.size+"%"):this.rangeHighlightElements[b].style.display="none"}if(Array.isArray(this.options.ticks)&&this.options.ticks.length>0){var f="vertical"===this.options.orientation?"height":"width",g="vertical"===this.options.orientation?"marginTop":"marginLeft",h=this._state.size/(this.options.ticks.length-1);if(this.tickLabelContainer){var i=0;if(0===this.options.ticks_positions.length)"vertical"!==this.options.orientation&&(this.tickLabelContainer.style[g]=-h/2+"px"),i=this.tickLabelContainer.offsetHeight;else for(j=0;j<this.tickLabelContainer.childNodes.length;j++)this.tickLabelContainer.childNodes[j].offsetHeight>i&&(i=this.tickLabelContainer.childNodes[j].offsetHeight);"horizontal"===this.options.orientation&&(this.sliderElem.style.marginBottom=i+"px")}for(var j=0;j<this.options.ticks.length;j++){var k=this.options.ticks_positions[j]||this._toPercentage(this.options.ticks[j]);this.options.reversed&&(k=100-k),this.ticks[j].style[this.stylePos]=k+"%",this._removeClass(this.ticks[j],"in-selection"),this.options.range?k>=a[0]&&k<=a[1]&&this._addClass(this.ticks[j],"in-selection"):"after"===this.options.selection&&k>=a[0]?this._addClass(this.ticks[j],"in-selection"):"before"===this.options.selection&&k<=a[0]&&this._addClass(this.ticks[j],"in-selection"),this.tickLabels[j]&&(this.tickLabels[j].style[f]=h+"px","vertical"!==this.options.orientation&&void 0!==this.options.ticks_positions[j]?(this.tickLabels[j].style.position="absolute",this.tickLabels[j].style[this.stylePos]=k+"%",this.tickLabels[j].style[g]=-h/2+"px"):"vertical"===this.options.orientation&&(this.tickLabels[j].style.marginLeft=this.sliderElem.offsetWidth+"px",this.tickLabelContainer.style.marginTop=this.sliderElem.offsetWidth/2*-1+"px"))}}var l;if(this.options.range){l=this.options.formatter(this._state.value),this._setText(this.tooltipInner,l),this.tooltip.style[this.stylePos]=(a[1]+a[0])/2+"%","vertical"===this.options.orientation?this._css(this.tooltip,"margin-top",-this.tooltip.offsetHeight/2+"px"):this._css(this.tooltip,"margin-left",-this.tooltip.offsetWidth/2+"px"),"vertical"===this.options.orientation?this._css(this.tooltip,"margin-top",-this.tooltip.offsetHeight/2+"px"):this._css(this.tooltip,"margin-left",-this.tooltip.offsetWidth/2+"px");var m=this.options.formatter(this._state.value[0]);this._setText(this.tooltipInner_min,m);var n=this.options.formatter(this._state.value[1]);this._setText(this.tooltipInner_max,n),this.tooltip_min.style[this.stylePos]=a[0]+"%","vertical"===this.options.orientation?this._css(this.tooltip_min,"margin-top",-this.tooltip_min.offsetHeight/2+"px"):this._css(this.tooltip_min,"margin-left",-this.tooltip_min.offsetWidth/2+"px"),this.tooltip_max.style[this.stylePos]=a[1]+"%","vertical"===this.options.orientation?this._css(this.tooltip_max,"margin-top",-this.tooltip_max.offsetHeight/2+"px"):this._css(this.tooltip_max,"margin-left",-this.tooltip_max.offsetWidth/2+"px")}else l=this.options.formatter(this._state.value[0]),this._setText(this.tooltipInner,l),this.tooltip.style[this.stylePos]=a[0]+"%","vertical"===this.options.orientation?this._css(this.tooltip,"margin-top",-this.tooltip.offsetHeight/2+"px"):this._css(this.tooltip,"margin-left",-this.tooltip.offsetWidth/2+"px");if("vertical"===this.options.orientation)this.trackLow.style.top="0",this.trackLow.style.height=Math.min(a[0],a[1])+"%",this.trackSelection.style.top=Math.min(a[0],a[1])+"%",this.trackSelection.style.height=Math.abs(a[0]-a[1])+"%",this.trackHigh.style.bottom="0",this.trackHigh.style.height=100-Math.min(a[0],a[1])-Math.abs(a[0]-a[1])+"%";else{this.trackLow.style.left="0",this.trackLow.style.width=Math.min(a[0],a[1])+"%",this.trackSelection.style.left=Math.min(a[0],a[1])+"%",this.trackSelection.style.width=Math.abs(a[0]-a[1])+"%",this.trackHigh.style.right="0",this.trackHigh.style.width=100-Math.min(a[0],a[1])-Math.abs(a[0]-a[1])+"%";var o=this.tooltip_min.getBoundingClientRect(),p=this.tooltip_max.getBoundingClientRect();"bottom"===this.options.tooltip_position?o.right>p.left?(this._removeClass(this.tooltip_max,"bottom"),this._addClass(this.tooltip_max,"top"),this.tooltip_max.style.top="",this.tooltip_max.style.bottom="22px"):(this._removeClass(this.tooltip_max,"top"),this._addClass(this.tooltip_max,"bottom"),this.tooltip_max.style.top=this.tooltip_min.style.top,this.tooltip_max.style.bottom=""):o.right>p.left?(this._removeClass(this.tooltip_max,"top"),this._addClass(this.tooltip_max,"bottom"),this.tooltip_max.style.top="18px"):(this._removeClass(this.tooltip_max,"bottom"),this._addClass(this.tooltip_max,"top"),this.tooltip_max.style.top=this.tooltip_min.style.top)}},_createHighlightRange:function(a,b){return this._isHighlightRange(a,b)?a>b?{start:b,size:a-b}:{start:a,size:b-a}:null},_isHighlightRange:function(a,b){return a>=0&&100>=a&&b>=0&&100>=b?!0:!1},_resize:function(a){this._state.offset=this._offset(this.sliderElem),this._state.size=this.sliderElem[this.sizePos],this._layout()},_removeProperty:function(a,b){a.style.removeProperty?a.style.removeProperty(b):a.style.removeAttribute(b)},_mousedown:function(a){if(!this._state.enabled)return!1;this._state.offset=this._offset(this.sliderElem),this._state.size=this.sliderElem[this.sizePos];var b=this._getPercentage(a);if(this.options.range){var c=Math.abs(this._state.percentage[0]-b),d=Math.abs(this._state.percentage[1]-b);this._state.dragged=d>c?0:1,this._adjustPercentageForRangeSliders(b)}else this._state.dragged=0;this._state.percentage[this._state.dragged]=b,this._layout(),this.touchCapable&&(document.removeEventListener("touchmove",this.mousemove,!1),document.removeEventListener("touchend",this.mouseup,!1)),this.mousemove&&document.removeEventListener("mousemove",this.mousemove,!1),this.mouseup&&document.removeEventListener("mouseup",this.mouseup,!1),this.mousemove=this._mousemove.bind(this),this.mouseup=this._mouseup.bind(this),this.touchCapable&&(document.addEventListener("touchmove",this.mousemove,!1),document.addEventListener("touchend",this.mouseup,!1)),document.addEventListener("mousemove",this.mousemove,!1),document.addEventListener("mouseup",this.mouseup,!1),this._state.inDrag=!0;var e=this._calculateValue();return this._trigger("slideStart",e),this._setDataVal(e),this.setValue(e,!1,!0),this._pauseEvent(a),this.options.focus&&this._triggerFocusOnHandle(this._state.dragged),!0},_touchstart:function(a){if(void 0===a.changedTouches)return void this._mousedown(a);var b=a.changedTouches[0];this.touchX=b.pageX,this.touchY=b.pageY},_triggerFocusOnHandle:function(a){0===a&&this.handle1.focus(),1===a&&this.handle2.focus()},_keydown:function(a,b){if(!this._state.enabled)return!1;var c;switch(b.keyCode){case 37:case 40:c=-1;break;case 39:case 38:c=1}if(c){if(this.options.natural_arrow_keys){var d="vertical"===this.options.orientation&&!this.options.reversed,e="horizontal"===this.options.orientation&&this.options.reversed;(d||e)&&(c=-c)}var f=this._state.value[a]+c*this.options.step;return this.options.range&&(f=[a?this._state.value[0]:f,a?f:this._state.value[1]]),this._trigger("slideStart",f),this._setDataVal(f),this.setValue(f,!0,!0),this._setDataVal(f),this._trigger("slideStop",f),this._layout(),this._pauseEvent(b),!1}},_pauseEvent:function(a){a.stopPropagation&&a.stopPropagation(),a.preventDefault&&a.preventDefault(),a.cancelBubble=!0,a.returnValue=!1},_mousemove:function(a){if(!this._state.enabled)return!1;var b=this._getPercentage(a);this._adjustPercentageForRangeSliders(b),this._state.percentage[this._state.dragged]=b,this._layout();var c=this._calculateValue(!0);return this.setValue(c,!0,!0),!1},_touchmove:function(a){if(void 0!==a.changedTouches){var b=a.changedTouches[0],c=b.pageX-this.touchX,d=b.pageY-this.touchY;this._state.inDrag||("vertical"===this.options.orientation&&5>=c&&c>=-5&&(d>=15||-15>=d)?this._mousedown(a):5>=d&&d>=-5&&(c>=15||-15>=c)&&this._mousedown(a))}},_adjustPercentageForRangeSliders:function(a){if(this.options.range){var b=this._getNumDigitsAfterDecimalPlace(a);b=b?b-1:0;var c=this._applyToFixedAndParseFloat(a,b);0===this._state.dragged&&this._applyToFixedAndParseFloat(this._state.percentage[1],b)<c?(this._state.percentage[0]=this._state.percentage[1],this._state.dragged=1):1===this._state.dragged&&this._applyToFixedAndParseFloat(this._state.percentage[0],b)>c&&(this._state.percentage[1]=this._state.percentage[0],this._state.dragged=0)}},_mouseup:function(){if(!this._state.enabled)return!1;this.touchCapable&&(document.removeEventListener("touchmove",this.mousemove,!1),document.removeEventListener("touchend",this.mouseup,!1)),document.removeEventListener("mousemove",this.mousemove,!1),document.removeEventListener("mouseup",this.mouseup,!1),this._state.inDrag=!1,this._state.over===!1&&this._hideTooltip();var a=this._calculateValue(!0);return this._layout(),this._setDataVal(a),this._trigger("slideStop",a),!1},_calculateValue:function(a){var b;if(this.options.range?(b=[this.options.min,this.options.max],0!==this._state.percentage[0]&&(b[0]=this._toValue(this._state.percentage[0]),b[0]=this._applyPrecision(b[0])),100!==this._state.percentage[1]&&(b[1]=this._toValue(this._state.percentage[1]),b[1]=this._applyPrecision(b[1]))):(b=this._toValue(this._state.percentage[0]),b=parseFloat(b),b=this._applyPrecision(b)),a){for(var c=[b,1/0],d=0;d<this.options.ticks.length;d++){var e=Math.abs(this.options.ticks[d]-b);e<=c[1]&&(c=[this.options.ticks[d],e])}if(c[1]<=this.options.ticks_snap_bounds)return c[0]}return b},_applyPrecision:function(a){var b=this.options.precision||this._getNumDigitsAfterDecimalPlace(this.options.step);return this._applyToFixedAndParseFloat(a,b)},_getNumDigitsAfterDecimalPlace:function(a){var b=(""+a).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);return b?Math.max(0,(b[1]?b[1].length:0)-(b[2]?+b[2]:0)):0},_applyToFixedAndParseFloat:function(a,b){var c=a.toFixed(b);return parseFloat(c)},_getPercentage:function(a){!this.touchCapable||"touchstart"!==a.type&&"touchmove"!==a.type||(a=a.touches[0]);var b=a[this.mousePos],c=this._state.offset[this.stylePos],d=b-c,e=d/this._state.size*100;return e=Math.round(e/this._state.percentage[2])*this._state.percentage[2],this.options.reversed&&(e=100-e),Math.max(0,Math.min(100,e))},_validateInputValue:function(a){if("number"==typeof a)return a;if(Array.isArray(a))return this._validateArray(a),a;throw new Error(f.formatInvalidInputErrorMsg(a))},_validateArray:function(a){for(var b=0;b<a.length;b++){var c=a[b];if("number"!=typeof c)throw new Error(f.formatInvalidInputErrorMsg(c))}},_setDataVal:function(a){this.element.setAttribute("data-value",a),this.element.setAttribute("value",a),this.element.value=a},_trigger:function(b,c){c=c||0===c?c:void 0;var d=this.eventToCallbackMap[b];if(d&&d.length)for(var e=0;e<d.length;e++){var f=d[e];f(c)}a&&this._triggerJQueryEvent(b,c)},_triggerJQueryEvent:function(a,b){var c={type:a,value:b};this.$element.trigger(c),this.$sliderElem.trigger(c)},_unbindJQueryEventHandlers:function(){this.$element.off(),this.$sliderElem.off()},_setText:function(a,b){"undefined"!=typeof a.textContent?a.textContent=b:"undefined"!=typeof a.innerText&&(a.innerText=b)},_removeClass:function(a,b){for(var c=b.split(" "),d=a.className,e=0;e<c.length;e++){var f=c[e],g=new RegExp("(?:\\s|^)"+f+"(?:\\s|$)");d=d.replace(g," ")}a.className=d.trim()},_addClass:function(a,b){for(var c=b.split(" "),d=a.className,e=0;e<c.length;e++){var f=c[e],g=new RegExp("(?:\\s|^)"+f+"(?:\\s|$)"),h=g.test(d);h||(d+=" "+f)}a.className=d.trim()},_offsetLeft:function(a){return a.getBoundingClientRect().left},_offsetTop:function(a){for(var b=a.offsetTop;(a=a.offsetParent)&&!isNaN(a.offsetTop);)b+=a.offsetTop,"BODY"!==a.tagName&&(b-=a.scrollTop);return b},_offset:function(a){return{left:this._offsetLeft(a),top:this._offsetTop(a)}},_css:function(b,c,d){if(a)a.style(b,c,d);else{var e=c.replace(/^-ms-/,"ms-").replace(/-([\da-z])/gi,function(a,b){return b.toUpperCase()});b.style[e]=d}},_toValue:function(a){return this.options.scale.toValue.apply(this,[a])},_toPercentage:function(a){return this.options.scale.toPercentage.apply(this,[a])},_setTooltipPosition:function(){var a=[this.tooltip,this.tooltip_min,this.tooltip_max];if("vertical"===this.options.orientation){var b=this.options.tooltip_position||"right",c="left"===b?"right":"left";a.forEach(function(a){this._addClass(a,b),a.style[c]="100%"}.bind(this))}else"bottom"===this.options.tooltip_position?a.forEach(function(a){this._addClass(a,"bottom"),a.style.top="22px"}.bind(this)):a.forEach(function(a){this._addClass(a,"top"),a.style.top=-this.tooltip.outerHeight-14+"px"}.bind(this))}},a&&!function(){var e=void 0;a.fn.slider?(windowIsDefined&&window.console.warn("bootstrap-slider.js - WARNING: $.fn.slider namespace is already bound. Use the $.fn.bootstrapSlider namespace instead."),e=c):(a.bridget(b,d),e=b),a.bridget(c,d),a(function(){a("input[data-provide=slider]")[e]()})}()}(a),d});
/*!
 * Datepicker for Bootstrap v1.6.4 (https://github.com/eternicode/bootstrap-datepicker)
 *
 * Copyright 2012 Stefan Petre
 * Improvements by Andrew Rowls
 * Licensed under the Apache License v2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 */
!function(a){"function"==typeof define&&define.amd?define(["jquery"],a):a("object"==typeof exports?require("jquery"):jQuery)}(function(a,b){function c(){return new Date(Date.UTC.apply(Date,arguments))}function d(){var a=new Date;return c(a.getFullYear(),a.getMonth(),a.getDate())}function e(a,b){return a.getUTCFullYear()===b.getUTCFullYear()&&a.getUTCMonth()===b.getUTCMonth()&&a.getUTCDate()===b.getUTCDate()}function f(a){return function(){return this[a].apply(this,arguments)}}function g(a){return a&&!isNaN(a.getTime())}function h(b,c){function d(a,b){return b.toLowerCase()}var e,f=a(b).data(),g={},h=new RegExp("^"+c.toLowerCase()+"([A-Z])");c=new RegExp("^"+c.toLowerCase());for(var i in f)c.test(i)&&(e=i.replace(h,d),g[e]=f[i]);return g}function i(b){var c={};if(q[b]||(b=b.split("-")[0],q[b])){var d=q[b];return a.each(p,function(a,b){b in d&&(c[b]=d[b])}),c}}var j=function(){var b={get:function(a){return this.slice(a)[0]},contains:function(a){for(var b=a&&a.valueOf(),c=0,d=this.length;d>c;c++)if(this[c].valueOf()===b)return c;return-1},remove:function(a){this.splice(a,1)},replace:function(b){b&&(a.isArray(b)||(b=[b]),this.clear(),this.push.apply(this,b))},clear:function(){this.length=0},copy:function(){var a=new j;return a.replace(this),a}};return function(){var c=[];return c.push.apply(c,arguments),a.extend(c,b),c}}(),k=function(b,c){a(b).data("datepicker",this),this._process_options(c),this.dates=new j,this.viewDate=this.o.defaultViewDate,this.focusDate=null,this.element=a(b),this.isInput=this.element.is("input"),this.inputField=this.isInput?this.element:this.element.find("input"),this.component=this.element.hasClass("date")?this.element.find(".add-on, .input-group-addon, .btn"):!1,this.hasInput=this.component&&this.inputField.length,this.component&&0===this.component.length&&(this.component=!1),this.isInline=!this.component&&this.element.is("div"),this.picker=a(r.template),this._check_template(this.o.templates.leftArrow)&&this.picker.find(".prev").html(this.o.templates.leftArrow),this._check_template(this.o.templates.rightArrow)&&this.picker.find(".next").html(this.o.templates.rightArrow),this._buildEvents(),this._attachEvents(),this.isInline?this.picker.addClass("datepicker-inline").appendTo(this.element):this.picker.addClass("datepicker-dropdown dropdown-menu"),this.o.rtl&&this.picker.addClass("datepicker-rtl"),this.viewMode=this.o.startView,this.o.calendarWeeks&&this.picker.find("thead .datepicker-title, tfoot .today, tfoot .clear").attr("colspan",function(a,b){return parseInt(b)+1}),this._allow_update=!1,this.setStartDate(this._o.startDate),this.setEndDate(this._o.endDate),this.setDaysOfWeekDisabled(this.o.daysOfWeekDisabled),this.setDaysOfWeekHighlighted(this.o.daysOfWeekHighlighted),this.setDatesDisabled(this.o.datesDisabled),this.fillDow(),this.fillMonths(),this._allow_update=!0,this.update(),this.showMode(),this.isInline&&this.show()};k.prototype={constructor:k,_resolveViewName:function(a,c){return 0===a||"days"===a||"month"===a?0:1===a||"months"===a||"year"===a?1:2===a||"years"===a||"decade"===a?2:3===a||"decades"===a||"century"===a?3:4===a||"centuries"===a||"millennium"===a?4:c===b?!1:c},_check_template:function(c){try{if(c===b||""===c)return!1;if((c.match(/[<>]/g)||[]).length<=0)return!0;var d=a(c);return d.length>0}catch(e){return!1}},_process_options:function(b){this._o=a.extend({},this._o,b);var e=this.o=a.extend({},this._o),f=e.language;q[f]||(f=f.split("-")[0],q[f]||(f=o.language)),e.language=f,e.startView=this._resolveViewName(e.startView,0),e.minViewMode=this._resolveViewName(e.minViewMode,0),e.maxViewMode=this._resolveViewName(e.maxViewMode,4),e.startView=Math.min(e.startView,e.maxViewMode),e.startView=Math.max(e.startView,e.minViewMode),e.multidate!==!0&&(e.multidate=Number(e.multidate)||!1,e.multidate!==!1&&(e.multidate=Math.max(0,e.multidate))),e.multidateSeparator=String(e.multidateSeparator),e.weekStart%=7,e.weekEnd=(e.weekStart+6)%7;var g=r.parseFormat(e.format);e.startDate!==-(1/0)&&(e.startDate?e.startDate instanceof Date?e.startDate=this._local_to_utc(this._zero_time(e.startDate)):e.startDate=r.parseDate(e.startDate,g,e.language,e.assumeNearbyYear):e.startDate=-(1/0)),e.endDate!==1/0&&(e.endDate?e.endDate instanceof Date?e.endDate=this._local_to_utc(this._zero_time(e.endDate)):e.endDate=r.parseDate(e.endDate,g,e.language,e.assumeNearbyYear):e.endDate=1/0),e.daysOfWeekDisabled=e.daysOfWeekDisabled||[],a.isArray(e.daysOfWeekDisabled)||(e.daysOfWeekDisabled=e.daysOfWeekDisabled.split(/[,\s]*/)),e.daysOfWeekDisabled=a.map(e.daysOfWeekDisabled,function(a){return parseInt(a,10)}),e.daysOfWeekHighlighted=e.daysOfWeekHighlighted||[],a.isArray(e.daysOfWeekHighlighted)||(e.daysOfWeekHighlighted=e.daysOfWeekHighlighted.split(/[,\s]*/)),e.daysOfWeekHighlighted=a.map(e.daysOfWeekHighlighted,function(a){return parseInt(a,10)}),e.datesDisabled=e.datesDisabled||[],a.isArray(e.datesDisabled)||(e.datesDisabled=[e.datesDisabled]),e.datesDisabled=a.map(e.datesDisabled,function(a){return r.parseDate(a,g,e.language,e.assumeNearbyYear)});var h=String(e.orientation).toLowerCase().split(/\s+/g),i=e.orientation.toLowerCase();if(h=a.grep(h,function(a){return/^auto|left|right|top|bottom$/.test(a)}),e.orientation={x:"auto",y:"auto"},i&&"auto"!==i)if(1===h.length)switch(h[0]){case"top":case"bottom":e.orientation.y=h[0];break;case"left":case"right":e.orientation.x=h[0]}else i=a.grep(h,function(a){return/^left|right$/.test(a)}),e.orientation.x=i[0]||"auto",i=a.grep(h,function(a){return/^top|bottom$/.test(a)}),e.orientation.y=i[0]||"auto";else;if(e.defaultViewDate){var j=e.defaultViewDate.year||(new Date).getFullYear(),k=e.defaultViewDate.month||0,l=e.defaultViewDate.day||1;e.defaultViewDate=c(j,k,l)}else e.defaultViewDate=d()},_events:[],_secondaryEvents:[],_applyEvents:function(a){for(var c,d,e,f=0;f<a.length;f++)c=a[f][0],2===a[f].length?(d=b,e=a[f][1]):3===a[f].length&&(d=a[f][1],e=a[f][2]),c.on(e,d)},_unapplyEvents:function(a){for(var c,d,e,f=0;f<a.length;f++)c=a[f][0],2===a[f].length?(e=b,d=a[f][1]):3===a[f].length&&(e=a[f][1],d=a[f][2]),c.off(d,e)},_buildEvents:function(){var b={keyup:a.proxy(function(b){-1===a.inArray(b.keyCode,[27,37,39,38,40,32,13,9])&&this.update()},this),keydown:a.proxy(this.keydown,this),paste:a.proxy(this.paste,this)};this.o.showOnFocus===!0&&(b.focus=a.proxy(this.show,this)),this.isInput?this._events=[[this.element,b]]:this.component&&this.hasInput?this._events=[[this.inputField,b],[this.component,{click:a.proxy(this.show,this)}]]:this._events=[[this.element,{click:a.proxy(this.show,this),keydown:a.proxy(this.keydown,this)}]],this._events.push([this.element,"*",{blur:a.proxy(function(a){this._focused_from=a.target},this)}],[this.element,{blur:a.proxy(function(a){this._focused_from=a.target},this)}]),this.o.immediateUpdates&&this._events.push([this.element,{"changeYear changeMonth":a.proxy(function(a){this.update(a.date)},this)}]),this._secondaryEvents=[[this.picker,{click:a.proxy(this.click,this)}],[a(window),{resize:a.proxy(this.place,this)}],[a(document),{mousedown:a.proxy(function(a){this.element.is(a.target)||this.element.find(a.target).length||this.picker.is(a.target)||this.picker.find(a.target).length||this.isInline||this.hide()},this)}]]},_attachEvents:function(){this._detachEvents(),this._applyEvents(this._events)},_detachEvents:function(){this._unapplyEvents(this._events)},_attachSecondaryEvents:function(){this._detachSecondaryEvents(),this._applyEvents(this._secondaryEvents)},_detachSecondaryEvents:function(){this._unapplyEvents(this._secondaryEvents)},_trigger:function(b,c){var d=c||this.dates.get(-1),e=this._utc_to_local(d);this.element.trigger({type:b,date:e,dates:a.map(this.dates,this._utc_to_local),format:a.proxy(function(a,b){0===arguments.length?(a=this.dates.length-1,b=this.o.format):"string"==typeof a&&(b=a,a=this.dates.length-1),b=b||this.o.format;var c=this.dates.get(a);return r.formatDate(c,b,this.o.language)},this)})},show:function(){return this.inputField.prop("disabled")||this.inputField.prop("readonly")&&this.o.enableOnReadonly===!1?void 0:(this.isInline||this.picker.appendTo(this.o.container),this.place(),this.picker.show(),this._attachSecondaryEvents(),this._trigger("show"),(window.navigator.msMaxTouchPoints||"ontouchstart"in document)&&this.o.disableTouchKeyboard&&a(this.element).blur(),this)},hide:function(){return this.isInline||!this.picker.is(":visible")?this:(this.focusDate=null,this.picker.hide().detach(),this._detachSecondaryEvents(),this.viewMode=this.o.startView,this.showMode(),this.o.forceParse&&this.inputField.val()&&this.setValue(),this._trigger("hide"),this)},destroy:function(){return this.hide(),this._detachEvents(),this._detachSecondaryEvents(),this.picker.remove(),delete this.element.data().datepicker,this.isInput||delete this.element.data().date,this},paste:function(b){var c;if(b.originalEvent.clipboardData&&b.originalEvent.clipboardData.types&&-1!==a.inArray("text/plain",b.originalEvent.clipboardData.types))c=b.originalEvent.clipboardData.getData("text/plain");else{if(!window.clipboardData)return;c=window.clipboardData.getData("Text")}this.setDate(c),this.update(),b.preventDefault()},_utc_to_local:function(a){return a&&new Date(a.getTime()+6e4*a.getTimezoneOffset())},_local_to_utc:function(a){return a&&new Date(a.getTime()-6e4*a.getTimezoneOffset())},_zero_time:function(a){return a&&new Date(a.getFullYear(),a.getMonth(),a.getDate())},_zero_utc_time:function(a){return a&&new Date(Date.UTC(a.getUTCFullYear(),a.getUTCMonth(),a.getUTCDate()))},getDates:function(){return a.map(this.dates,this._utc_to_local)},getUTCDates:function(){return a.map(this.dates,function(a){return new Date(a)})},getDate:function(){return this._utc_to_local(this.getUTCDate())},getUTCDate:function(){var a=this.dates.get(-1);return"undefined"!=typeof a?new Date(a):null},clearDates:function(){this.inputField&&this.inputField.val(""),this.update(),this._trigger("changeDate"),this.o.autoclose&&this.hide()},setDates:function(){var b=a.isArray(arguments[0])?arguments[0]:arguments;return this.update.apply(this,b),this._trigger("changeDate"),this.setValue(),this},setUTCDates:function(){var b=a.isArray(arguments[0])?arguments[0]:arguments;return this.update.apply(this,a.map(b,this._utc_to_local)),this._trigger("changeDate"),this.setValue(),this},setDate:f("setDates"),setUTCDate:f("setUTCDates"),remove:f("destroy"),setValue:function(){var a=this.getFormattedDate();return this.inputField.val(a),this},getFormattedDate:function(c){c===b&&(c=this.o.format);var d=this.o.language;return a.map(this.dates,function(a){return r.formatDate(a,c,d)}).join(this.o.multidateSeparator)},getStartDate:function(){return this.o.startDate},setStartDate:function(a){return this._process_options({startDate:a}),this.update(),this.updateNavArrows(),this},getEndDate:function(){return this.o.endDate},setEndDate:function(a){return this._process_options({endDate:a}),this.update(),this.updateNavArrows(),this},setDaysOfWeekDisabled:function(a){return this._process_options({daysOfWeekDisabled:a}),this.update(),this.updateNavArrows(),this},setDaysOfWeekHighlighted:function(a){return this._process_options({daysOfWeekHighlighted:a}),this.update(),this},setDatesDisabled:function(a){this._process_options({datesDisabled:a}),this.update(),this.updateNavArrows()},place:function(){if(this.isInline)return this;var b=this.picker.outerWidth(),c=this.picker.outerHeight(),d=10,e=a(this.o.container),f=e.width(),g="body"===this.o.container?a(document).scrollTop():e.scrollTop(),h=e.offset(),i=[];this.element.parents().each(function(){var b=a(this).css("z-index");"auto"!==b&&0!==b&&i.push(parseInt(b))});var j=Math.max.apply(Math,i)+this.o.zIndexOffset,k=this.component?this.component.parent().offset():this.element.offset(),l=this.component?this.component.outerHeight(!0):this.element.outerHeight(!1),m=this.component?this.component.outerWidth(!0):this.element.outerWidth(!1),n=k.left-h.left,o=k.top-h.top;"body"!==this.o.container&&(o+=g),this.picker.removeClass("datepicker-orient-top datepicker-orient-bottom datepicker-orient-right datepicker-orient-left"),"auto"!==this.o.orientation.x?(this.picker.addClass("datepicker-orient-"+this.o.orientation.x),"right"===this.o.orientation.x&&(n-=b-m)):k.left<0?(this.picker.addClass("datepicker-orient-left"),n-=k.left-d):n+b>f?(this.picker.addClass("datepicker-orient-right"),n+=m-b):this.picker.addClass("datepicker-orient-left");var p,q=this.o.orientation.y;if("auto"===q&&(p=-g+o-c,q=0>p?"bottom":"top"),this.picker.addClass("datepicker-orient-"+q),"top"===q?o-=c+parseInt(this.picker.css("padding-top")):o+=l,this.o.rtl){var r=f-(n+m);this.picker.css({top:o,right:r,zIndex:j})}else this.picker.css({top:o,left:n,zIndex:j});return this},_allow_update:!0,update:function(){if(!this._allow_update)return this;var b=this.dates.copy(),c=[],d=!1;return arguments.length?(a.each(arguments,a.proxy(function(a,b){b instanceof Date&&(b=this._local_to_utc(b)),c.push(b)},this)),d=!0):(c=this.isInput?this.element.val():this.element.data("date")||this.inputField.val(),c=c&&this.o.multidate?c.split(this.o.multidateSeparator):[c],delete this.element.data().date),c=a.map(c,a.proxy(function(a){return r.parseDate(a,this.o.format,this.o.language,this.o.assumeNearbyYear)},this)),c=a.grep(c,a.proxy(function(a){return!this.dateWithinRange(a)||!a},this),!0),this.dates.replace(c),this.dates.length?this.viewDate=new Date(this.dates.get(-1)):this.viewDate<this.o.startDate?this.viewDate=new Date(this.o.startDate):this.viewDate>this.o.endDate?this.viewDate=new Date(this.o.endDate):this.viewDate=this.o.defaultViewDate,d?this.setValue():c.length&&String(b)!==String(this.dates)&&this._trigger("changeDate"),!this.dates.length&&b.length&&this._trigger("clearDate"),this.fill(),this.element.change(),this},fillDow:function(){var b=this.o.weekStart,c="<tr>";for(this.o.calendarWeeks&&(this.picker.find(".datepicker-days .datepicker-switch").attr("colspan",function(a,b){return parseInt(b)+1}),c+='<th class="cw">&#160;</th>');b<this.o.weekStart+7;)c+='<th class="dow',a.inArray(b,this.o.daysOfWeekDisabled)>-1&&(c+=" disabled"),c+='">'+q[this.o.language].daysMin[b++%7]+"</th>";c+="</tr>",this.picker.find(".datepicker-days thead").append(c)},fillMonths:function(){for(var a=this._utc_to_local(this.viewDate),b="",c=0;12>c;){var d=a&&a.getMonth()===c?" focused":"";b+='<span class="month'+d+'">'+q[this.o.language].monthsShort[c++]+"</span>"}this.picker.find(".datepicker-months td").html(b)},setRange:function(b){b&&b.length?this.range=a.map(b,function(a){return a.valueOf()}):delete this.range,this.fill()},getClassNames:function(b){var c=[],d=this.viewDate.getUTCFullYear(),e=this.viewDate.getUTCMonth(),f=new Date;return b.getUTCFullYear()<d||b.getUTCFullYear()===d&&b.getUTCMonth()<e?c.push("old"):(b.getUTCFullYear()>d||b.getUTCFullYear()===d&&b.getUTCMonth()>e)&&c.push("new"),this.focusDate&&b.valueOf()===this.focusDate.valueOf()&&c.push("focused"),this.o.todayHighlight&&b.getUTCFullYear()===f.getFullYear()&&b.getUTCMonth()===f.getMonth()&&b.getUTCDate()===f.getDate()&&c.push("today"),-1!==this.dates.contains(b)&&c.push("active"),this.dateWithinRange(b)||c.push("disabled"),this.dateIsDisabled(b)&&c.push("disabled","disabled-date"),-1!==a.inArray(b.getUTCDay(),this.o.daysOfWeekHighlighted)&&c.push("highlighted"),this.range&&(b>this.range[0]&&b<this.range[this.range.length-1]&&c.push("range"),-1!==a.inArray(b.valueOf(),this.range)&&c.push("selected"),b.valueOf()===this.range[0]&&c.push("range-start"),b.valueOf()===this.range[this.range.length-1]&&c.push("range-end")),c},_fill_yearsView:function(c,d,e,f,g,h,i,j){var k,l,m,n,o,p,q,r,s,t,u;for(k="",l=this.picker.find(c),m=parseInt(g/e,10)*e,o=parseInt(h/f,10)*f,p=parseInt(i/f,10)*f,n=a.map(this.dates,function(a){return parseInt(a.getUTCFullYear()/f,10)*f}),l.find(".datepicker-switch").text(m+"-"+(m+9*f)),q=m-f,r=-1;11>r;r+=1)s=[d],t=null,-1===r?s.push("old"):10===r&&s.push("new"),-1!==a.inArray(q,n)&&s.push("active"),(o>q||q>p)&&s.push("disabled"),q===this.viewDate.getFullYear()&&s.push("focused"),j!==a.noop&&(u=j(new Date(q,0,1)),u===b?u={}:"boolean"==typeof u?u={enabled:u}:"string"==typeof u&&(u={classes:u}),u.enabled===!1&&s.push("disabled"),u.classes&&(s=s.concat(u.classes.split(/\s+/))),u.tooltip&&(t=u.tooltip)),k+='<span class="'+s.join(" ")+'"'+(t?' title="'+t+'"':"")+">"+q+"</span>",q+=f;l.find("td").html(k)},fill:function(){var d,e,f=new Date(this.viewDate),g=f.getUTCFullYear(),h=f.getUTCMonth(),i=this.o.startDate!==-(1/0)?this.o.startDate.getUTCFullYear():-(1/0),j=this.o.startDate!==-(1/0)?this.o.startDate.getUTCMonth():-(1/0),k=this.o.endDate!==1/0?this.o.endDate.getUTCFullYear():1/0,l=this.o.endDate!==1/0?this.o.endDate.getUTCMonth():1/0,m=q[this.o.language].today||q.en.today||"",n=q[this.o.language].clear||q.en.clear||"",o=q[this.o.language].titleFormat||q.en.titleFormat;if(!isNaN(g)&&!isNaN(h)){this.picker.find(".datepicker-days .datepicker-switch").text(r.formatDate(f,o,this.o.language)),this.picker.find("tfoot .today").text(m).toggle(this.o.todayBtn!==!1),this.picker.find("tfoot .clear").text(n).toggle(this.o.clearBtn!==!1),this.picker.find("thead .datepicker-title").text(this.o.title).toggle(""!==this.o.title),this.updateNavArrows(),this.fillMonths();var p=c(g,h-1,28),s=r.getDaysInMonth(p.getUTCFullYear(),p.getUTCMonth());p.setUTCDate(s),p.setUTCDate(s-(p.getUTCDay()-this.o.weekStart+7)%7);var t=new Date(p);p.getUTCFullYear()<100&&t.setUTCFullYear(p.getUTCFullYear()),t.setUTCDate(t.getUTCDate()+42),t=t.valueOf();for(var u,v=[];p.valueOf()<t;){if(p.getUTCDay()===this.o.weekStart&&(v.push("<tr>"),this.o.calendarWeeks)){var w=new Date(+p+(this.o.weekStart-p.getUTCDay()-7)%7*864e5),x=new Date(Number(w)+(11-w.getUTCDay())%7*864e5),y=new Date(Number(y=c(x.getUTCFullYear(),0,1))+(11-y.getUTCDay())%7*864e5),z=(x-y)/864e5/7+1;v.push('<td class="cw">'+z+"</td>")}u=this.getClassNames(p),u.push("day"),this.o.beforeShowDay!==a.noop&&(e=this.o.beforeShowDay(this._utc_to_local(p)),e===b?e={}:"boolean"==typeof e?e={enabled:e}:"string"==typeof e&&(e={classes:e}),e.enabled===!1&&u.push("disabled"),e.classes&&(u=u.concat(e.classes.split(/\s+/))),e.tooltip&&(d=e.tooltip)),u=a.isFunction(a.uniqueSort)?a.uniqueSort(u):a.unique(u),v.push('<td class="'+u.join(" ")+'"'+(d?' title="'+d+'"':"")+">"+p.getUTCDate()+"</td>"),d=null,p.getUTCDay()===this.o.weekEnd&&v.push("</tr>"),p.setUTCDate(p.getUTCDate()+1)}this.picker.find(".datepicker-days tbody").empty().append(v.join(""));var A=q[this.o.language].monthsTitle||q.en.monthsTitle||"Months",B=this.picker.find(".datepicker-months").find(".datepicker-switch").text(this.o.maxViewMode<2?A:g).end().find("span").removeClass("active");if(a.each(this.dates,function(a,b){b.getUTCFullYear()===g&&B.eq(b.getUTCMonth()).addClass("active")}),(i>g||g>k)&&B.addClass("disabled"),g===i&&B.slice(0,j).addClass("disabled"),g===k&&B.slice(l+1).addClass("disabled"),this.o.beforeShowMonth!==a.noop){var C=this;a.each(B,function(c,d){var e=new Date(g,c,1),f=C.o.beforeShowMonth(e);f===b?f={}:"boolean"==typeof f?f={enabled:f}:"string"==typeof f&&(f={classes:f}),f.enabled!==!1||a(d).hasClass("disabled")||a(d).addClass("disabled"),f.classes&&a(d).addClass(f.classes),f.tooltip&&a(d).prop("title",f.tooltip)})}this._fill_yearsView(".datepicker-years","year",10,1,g,i,k,this.o.beforeShowYear),this._fill_yearsView(".datepicker-decades","decade",100,10,g,i,k,this.o.beforeShowDecade),this._fill_yearsView(".datepicker-centuries","century",1e3,100,g,i,k,this.o.beforeShowCentury)}},updateNavArrows:function(){if(this._allow_update){var a=new Date(this.viewDate),b=a.getUTCFullYear(),c=a.getUTCMonth();switch(this.viewMode){case 0:this.o.startDate!==-(1/0)&&b<=this.o.startDate.getUTCFullYear()&&c<=this.o.startDate.getUTCMonth()?this.picker.find(".prev").css({visibility:"hidden"}):this.picker.find(".prev").css({visibility:"visible"}),this.o.endDate!==1/0&&b>=this.o.endDate.getUTCFullYear()&&c>=this.o.endDate.getUTCMonth()?this.picker.find(".next").css({visibility:"hidden"}):this.picker.find(".next").css({visibility:"visible"});break;case 1:case 2:case 3:case 4:this.o.startDate!==-(1/0)&&b<=this.o.startDate.getUTCFullYear()||this.o.maxViewMode<2?this.picker.find(".prev").css({visibility:"hidden"}):this.picker.find(".prev").css({visibility:"visible"}),this.o.endDate!==1/0&&b>=this.o.endDate.getUTCFullYear()||this.o.maxViewMode<2?this.picker.find(".next").css({visibility:"hidden"}):this.picker.find(".next").css({visibility:"visible"})}}},click:function(b){b.preventDefault(),b.stopPropagation();var e,f,g,h,i,j,k;e=a(b.target),e.hasClass("datepicker-switch")&&this.showMode(1);var l=e.closest(".prev, .next");l.length>0&&(f=r.modes[this.viewMode].navStep*(l.hasClass("prev")?-1:1),0===this.viewMode?(this.viewDate=this.moveMonth(this.viewDate,f),this._trigger("changeMonth",this.viewDate)):(this.viewDate=this.moveYear(this.viewDate,f),1===this.viewMode&&this._trigger("changeYear",this.viewDate)),this.fill()),e.hasClass("today")&&!e.hasClass("day")&&(this.showMode(-2),this._setDate(d(),"linked"===this.o.todayBtn?null:"view")),e.hasClass("clear")&&this.clearDates(),e.hasClass("disabled")||(e.hasClass("day")&&(g=parseInt(e.text(),10)||1,h=this.viewDate.getUTCFullYear(),i=this.viewDate.getUTCMonth(),e.hasClass("old")&&(0===i?(i=11,h-=1,j=!0,k=!0):(i-=1,j=!0)),e.hasClass("new")&&(11===i?(i=0,h+=1,j=!0,k=!0):(i+=1,j=!0)),this._setDate(c(h,i,g)),k&&this._trigger("changeYear",this.viewDate),j&&this._trigger("changeMonth",this.viewDate)),e.hasClass("month")&&(this.viewDate.setUTCDate(1),g=1,i=e.parent().find("span").index(e),h=this.viewDate.getUTCFullYear(),this.viewDate.setUTCMonth(i),this._trigger("changeMonth",this.viewDate),1===this.o.minViewMode?(this._setDate(c(h,i,g)),this.showMode()):this.showMode(-1),this.fill()),(e.hasClass("year")||e.hasClass("decade")||e.hasClass("century"))&&(this.viewDate.setUTCDate(1),g=1,i=0,h=parseInt(e.text(),10)||0,this.viewDate.setUTCFullYear(h),e.hasClass("year")&&(this._trigger("changeYear",this.viewDate),2===this.o.minViewMode&&this._setDate(c(h,i,g))),e.hasClass("decade")&&(this._trigger("changeDecade",this.viewDate),3===this.o.minViewMode&&this._setDate(c(h,i,g))),e.hasClass("century")&&(this._trigger("changeCentury",this.viewDate),4===this.o.minViewMode&&this._setDate(c(h,i,g))),this.showMode(-1),this.fill())),this.picker.is(":visible")&&this._focused_from&&a(this._focused_from).focus(),delete this._focused_from},_toggle_multidate:function(a){var b=this.dates.contains(a);if(a||this.dates.clear(),-1!==b?(this.o.multidate===!0||this.o.multidate>1||this.o.toggleActive)&&this.dates.remove(b):this.o.multidate===!1?(this.dates.clear(),this.dates.push(a)):this.dates.push(a),"number"==typeof this.o.multidate)for(;this.dates.length>this.o.multidate;)this.dates.remove(0)},_setDate:function(a,b){b&&"date"!==b||this._toggle_multidate(a&&new Date(a)),b&&"view"!==b||(this.viewDate=a&&new Date(a)),this.fill(),this.setValue(),b&&"view"===b||this._trigger("changeDate"),this.inputField&&this.inputField.change(),!this.o.autoclose||b&&"date"!==b||this.hide()},moveDay:function(a,b){var c=new Date(a);return c.setUTCDate(a.getUTCDate()+b),c},moveWeek:function(a,b){return this.moveDay(a,7*b)},moveMonth:function(a,b){if(!g(a))return this.o.defaultViewDate;if(!b)return a;var c,d,e=new Date(a.valueOf()),f=e.getUTCDate(),h=e.getUTCMonth(),i=Math.abs(b);if(b=b>0?1:-1,1===i)d=-1===b?function(){return e.getUTCMonth()===h}:function(){return e.getUTCMonth()!==c},c=h+b,e.setUTCMonth(c),(0>c||c>11)&&(c=(c+12)%12);else{for(var j=0;i>j;j++)e=this.moveMonth(e,b);c=e.getUTCMonth(),e.setUTCDate(f),d=function(){return c!==e.getUTCMonth()}}for(;d();)e.setUTCDate(--f),e.setUTCMonth(c);return e},moveYear:function(a,b){return this.moveMonth(a,12*b)},moveAvailableDate:function(a,b,c){do{if(a=this[c](a,b),!this.dateWithinRange(a))return!1;c="moveDay"}while(this.dateIsDisabled(a));return a},weekOfDateIsDisabled:function(b){return-1!==a.inArray(b.getUTCDay(),this.o.daysOfWeekDisabled)},dateIsDisabled:function(b){return this.weekOfDateIsDisabled(b)||a.grep(this.o.datesDisabled,function(a){return e(b,a)}).length>0},dateWithinRange:function(a){return a>=this.o.startDate&&a<=this.o.endDate},keydown:function(a){if(!this.picker.is(":visible"))return void((40===a.keyCode||27===a.keyCode)&&(this.show(),a.stopPropagation()));var b,c,d=!1,e=this.focusDate||this.viewDate;switch(a.keyCode){case 27:this.focusDate?(this.focusDate=null,this.viewDate=this.dates.get(-1)||this.viewDate,this.fill()):this.hide(),a.preventDefault(),a.stopPropagation();break;case 37:case 38:case 39:case 40:if(!this.o.keyboardNavigation||7===this.o.daysOfWeekDisabled.length)break;b=37===a.keyCode||38===a.keyCode?-1:1,0===this.viewMode?a.ctrlKey?(c=this.moveAvailableDate(e,b,"moveYear"),c&&this._trigger("changeYear",this.viewDate)):a.shiftKey?(c=this.moveAvailableDate(e,b,"moveMonth"),c&&this._trigger("changeMonth",this.viewDate)):37===a.keyCode||39===a.keyCode?c=this.moveAvailableDate(e,b,"moveDay"):this.weekOfDateIsDisabled(e)||(c=this.moveAvailableDate(e,b,"moveWeek")):1===this.viewMode?((38===a.keyCode||40===a.keyCode)&&(b=4*b),c=this.moveAvailableDate(e,b,"moveMonth")):2===this.viewMode&&((38===a.keyCode||40===a.keyCode)&&(b=4*b),c=this.moveAvailableDate(e,b,"moveYear")),c&&(this.focusDate=this.viewDate=c,this.setValue(),this.fill(),a.preventDefault());break;case 13:if(!this.o.forceParse)break;e=this.focusDate||this.dates.get(-1)||this.viewDate,this.o.keyboardNavigation&&(this._toggle_multidate(e),d=!0),this.focusDate=null,this.viewDate=this.dates.get(-1)||this.viewDate,this.setValue(),this.fill(),this.picker.is(":visible")&&(a.preventDefault(),a.stopPropagation(),this.o.autoclose&&this.hide());break;case 9:this.focusDate=null,this.viewDate=this.dates.get(-1)||this.viewDate,this.fill(),this.hide()}d&&(this.dates.length?this._trigger("changeDate"):this._trigger("clearDate"),this.inputField&&this.inputField.change())},showMode:function(a){a&&(this.viewMode=Math.max(this.o.minViewMode,Math.min(this.o.maxViewMode,this.viewMode+a))),this.picker.children("div").hide().filter(".datepicker-"+r.modes[this.viewMode].clsName).show(),this.updateNavArrows()}};var l=function(b,c){a(b).data("datepicker",this),this.element=a(b),this.inputs=a.map(c.inputs,function(a){return a.jquery?a[0]:a}),delete c.inputs,n.call(a(this.inputs),c).on("changeDate",a.proxy(this.dateUpdated,this)),this.pickers=a.map(this.inputs,function(b){return a(b).data("datepicker")}),this.updateDates()};l.prototype={updateDates:function(){this.dates=a.map(this.pickers,function(a){return a.getUTCDate()}),this.updateRanges()},updateRanges:function(){var b=a.map(this.dates,function(a){return a.valueOf()});a.each(this.pickers,function(a,c){c.setRange(b)})},dateUpdated:function(b){if(!this.updating){this.updating=!0;var c=a(b.target).data("datepicker");if("undefined"!=typeof c){var d=c.getUTCDate(),e=a.inArray(b.target,this.inputs),f=e-1,g=e+1,h=this.inputs.length;if(-1!==e){if(a.each(this.pickers,function(a,b){b.getUTCDate()||b.setUTCDate(d)}),d<this.dates[f])for(;f>=0&&d<this.dates[f];)this.pickers[f--].setUTCDate(d);else if(d>this.dates[g])for(;h>g&&d>this.dates[g];)this.pickers[g++].setUTCDate(d);this.updateDates(),delete this.updating}}}},remove:function(){a.map(this.pickers,function(a){a.remove()}),delete this.element.data().datepicker}};var m=a.fn.datepicker,n=function(c){var d=Array.apply(null,arguments);d.shift();var e;if(this.each(function(){var b=a(this),f=b.data("datepicker"),g="object"==typeof c&&c;if(!f){var j=h(this,"date"),m=a.extend({},o,j,g),n=i(m.language),p=a.extend({},o,n,j,g);b.hasClass("input-daterange")||p.inputs?(a.extend(p,{inputs:p.inputs||b.find("input").toArray()}),f=new l(this,p)):f=new k(this,p),b.data("datepicker",f)}"string"==typeof c&&"function"==typeof f[c]&&(e=f[c].apply(f,d))}),e===b||e instanceof k||e instanceof l)return this;if(this.length>1)throw new Error("Using only allowed for the collection of a single element ("+c+" function)");return e};a.fn.datepicker=n;var o=a.fn.datepicker.defaults={assumeNearbyYear:!1,autoclose:!1,beforeShowDay:a.noop,beforeShowMonth:a.noop,beforeShowYear:a.noop,beforeShowDecade:a.noop,beforeShowCentury:a.noop,calendarWeeks:!1,clearBtn:!1,toggleActive:!1,daysOfWeekDisabled:[],daysOfWeekHighlighted:[],datesDisabled:[],endDate:1/0,forceParse:!0,format:"mm/dd/yyyy",keyboardNavigation:!0,language:"en",minViewMode:0,maxViewMode:4,multidate:!1,multidateSeparator:",",orientation:"auto",rtl:!1,startDate:-(1/0),startView:0,todayBtn:!1,todayHighlight:!1,weekStart:0,disableTouchKeyboard:!1,enableOnReadonly:!0,showOnFocus:!0,zIndexOffset:10,container:"body",immediateUpdates:!1,title:"",templates:{leftArrow:"&laquo;",rightArrow:"&raquo;"}},p=a.fn.datepicker.locale_opts=["format","rtl","weekStart"];a.fn.datepicker.Constructor=k;var q=a.fn.datepicker.dates={en:{days:["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"],daysShort:["Sun","Mon","Tue","Wed","Thu","Fri","Sat"],daysMin:["Su","Mo","Tu","We","Th","Fr","Sa"],months:["January","February","March","April","May","June","July","August","September","October","November","December"],monthsShort:["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],today:"Today",clear:"Clear",titleFormat:"MM yyyy"}},r={modes:[{clsName:"days",navFnc:"Month",navStep:1},{clsName:"months",navFnc:"FullYear",navStep:1},{clsName:"years",navFnc:"FullYear",navStep:10},{clsName:"decades",navFnc:"FullDecade",navStep:100},{clsName:"centuries",navFnc:"FullCentury",navStep:1e3}],isLeapYear:function(a){return a%4===0&&a%100!==0||a%400===0},getDaysInMonth:function(a,b){return[31,r.isLeapYear(a)?29:28,31,30,31,30,31,31,30,31,30,31][b]},validParts:/dd?|DD?|mm?|MM?|yy(?:yy)?/g,nonpunctuation:/[^ -\/:-@\u5e74\u6708\u65e5\[-`{-~\t\n\r]+/g,parseFormat:function(a){if("function"==typeof a.toValue&&"function"==typeof a.toDisplay)return a;var b=a.replace(this.validParts,"\x00").split("\x00"),c=a.match(this.validParts);if(!b||!b.length||!c||0===c.length)throw new Error("Invalid date format.");return{separators:b,parts:c}},parseDate:function(e,f,g,h){function i(a,b){return b===!0&&(b=10),100>a&&(a+=2e3,a>(new Date).getFullYear()+b&&(a-=100)),a}function j(){var a=this.slice(0,s[n].length),b=s[n].slice(0,a.length);return a.toLowerCase()===b.toLowerCase()}if(!e)return b;if(e instanceof Date)return e;if("string"==typeof f&&(f=r.parseFormat(f)),f.toValue)return f.toValue(e,f,g);var l,m,n,o,p=/([\-+]\d+)([dmwy])/,s=e.match(/([\-+]\d+)([dmwy])/g),t={d:"moveDay",m:"moveMonth",w:"moveWeek",y:"moveYear"},u={yesterday:"-1d",today:"+0d",tomorrow:"+1d"};if(/^[\-+]\d+[dmwy]([\s,]+[\-+]\d+[dmwy])*$/.test(e)){for(e=new Date,n=0;n<s.length;n++)l=p.exec(s[n]),m=parseInt(l[1]),o=t[l[2]],e=k.prototype[o](e,m);return c(e.getUTCFullYear(),e.getUTCMonth(),e.getUTCDate())}if("undefined"!=typeof u[e]&&(e=u[e],s=e.match(/([\-+]\d+)([dmwy])/g),/^[\-+]\d+[dmwy]([\s,]+[\-+]\d+[dmwy])*$/.test(e))){for(e=new Date,n=0;n<s.length;n++)l=p.exec(s[n]),m=parseInt(l[1]),o=t[l[2]],e=k.prototype[o](e,m);return c(e.getUTCFullYear(),e.getUTCMonth(),e.getUTCDate())}s=e&&e.match(this.nonpunctuation)||[],e=new Date;var v,w,x={},y=["yyyy","yy","M","MM","m","mm","d","dd"],z={yyyy:function(a,b){return a.setUTCFullYear(h?i(b,h):b)},yy:function(a,b){return a.setUTCFullYear(h?i(b,h):b)},m:function(a,b){if(isNaN(a))return a;for(b-=1;0>b;)b+=12;for(b%=12,a.setUTCMonth(b);a.getUTCMonth()!==b;)a.setUTCDate(a.getUTCDate()-1);return a},d:function(a,b){return a.setUTCDate(b)}};z.M=z.MM=z.mm=z.m,z.dd=z.d,e=d();var A=f.parts.slice();if(s.length!==A.length&&(A=a(A).filter(function(b,c){return-1!==a.inArray(c,y)}).toArray()),s.length===A.length){var B;for(n=0,B=A.length;B>n;n++){if(v=parseInt(s[n],10),l=A[n],isNaN(v))switch(l){case"MM":w=a(q[g].months).filter(j),v=a.inArray(w[0],q[g].months)+1;break;case"M":w=a(q[g].monthsShort).filter(j),v=a.inArray(w[0],q[g].monthsShort)+1}x[l]=v}var C,D;for(n=0;n<y.length;n++)D=y[n],D in x&&!isNaN(x[D])&&(C=new Date(e),z[D](C,x[D]),isNaN(C)||(e=C))}return e},formatDate:function(b,c,d){if(!b)return"";if("string"==typeof c&&(c=r.parseFormat(c)),
c.toDisplay)return c.toDisplay(b,c,d);var e={d:b.getUTCDate(),D:q[d].daysShort[b.getUTCDay()],DD:q[d].days[b.getUTCDay()],m:b.getUTCMonth()+1,M:q[d].monthsShort[b.getUTCMonth()],MM:q[d].months[b.getUTCMonth()],yy:b.getUTCFullYear().toString().substring(2),yyyy:b.getUTCFullYear()};e.dd=(e.d<10?"0":"")+e.d,e.mm=(e.m<10?"0":"")+e.m,b=[];for(var f=a.extend([],c.separators),g=0,h=c.parts.length;h>=g;g++)f.length&&b.push(f.shift()),b.push(e[c.parts[g]]);return b.join("")},headTemplate:'<thead><tr><th colspan="7" class="datepicker-title"></th></tr><tr><th class="prev">&laquo;</th><th colspan="5" class="datepicker-switch"></th><th class="next">&raquo;</th></tr></thead>',contTemplate:'<tbody><tr><td colspan="7"></td></tr></tbody>',footTemplate:'<tfoot><tr><th colspan="7" class="today"></th></tr><tr><th colspan="7" class="clear"></th></tr></tfoot>'};r.template='<div class="datepicker"><div class="datepicker-days"><table class="table-condensed">'+r.headTemplate+"<tbody></tbody>"+r.footTemplate+'</table></div><div class="datepicker-months"><table class="table-condensed">'+r.headTemplate+r.contTemplate+r.footTemplate+'</table></div><div class="datepicker-years"><table class="table-condensed">'+r.headTemplate+r.contTemplate+r.footTemplate+'</table></div><div class="datepicker-decades"><table class="table-condensed">'+r.headTemplate+r.contTemplate+r.footTemplate+'</table></div><div class="datepicker-centuries"><table class="table-condensed">'+r.headTemplate+r.contTemplate+r.footTemplate+"</table></div></div>",a.fn.datepicker.DPGlobal=r,a.fn.datepicker.noConflict=function(){return a.fn.datepicker=m,this},a.fn.datepicker.version="1.6.4",a(document).on("focus.datepicker.data-api click.datepicker.data-api",'[data-provide="datepicker"]',function(b){var c=a(this);c.data("datepicker")||(b.preventDefault(),n.call(c,"show"))}),a(function(){n.call(a('[data-provide="datepicker-inline"]'))})});
/**
 * Owl Carousel v2.2.1
 * Copyright 2013-2017 David Deutsch
 * Licensed under  ()
 */
!function(a,b,c,d){function e(b,c){this.settings=null,this.options=a.extend({},e.Defaults,c),this.$element=a(b),this._handlers={},this._plugins={},this._supress={},this._current=null,this._speed=null,this._coordinates=[],this._breakpoint=null,this._width=null,this._items=[],this._clones=[],this._mergers=[],this._widths=[],this._invalidated={},this._pipe=[],this._drag={time:null,target:null,pointer:null,stage:{start:null,current:null},direction:null},this._states={current:{},tags:{initializing:["busy"],animating:["busy"],dragging:["interacting"]}},a.each(["onResize","onThrottledResize"],a.proxy(function(b,c){this._handlers[c]=a.proxy(this[c],this)},this)),a.each(e.Plugins,a.proxy(function(a,b){this._plugins[a.charAt(0).toLowerCase()+a.slice(1)]=new b(this)},this)),a.each(e.Workers,a.proxy(function(b,c){this._pipe.push({filter:c.filter,run:a.proxy(c.run,this)})},this)),this.setup(),this.initialize()}e.Defaults={items:3,loop:!1,center:!1,rewind:!1,mouseDrag:!0,touchDrag:!0,pullDrag:!0,freeDrag:!1,margin:0,stagePadding:0,merge:!1,mergeFit:!0,autoWidth:!1,startPosition:0,rtl:!1,smartSpeed:250,fluidSpeed:!1,dragEndSpeed:!1,responsive:{},responsiveRefreshRate:200,responsiveBaseElement:b,fallbackEasing:"swing",info:!1,nestedItemSelector:!1,itemElement:"div",stageElement:"div",refreshClass:"owl-refresh",loadedClass:"owl-loaded",loadingClass:"owl-loading",rtlClass:"owl-rtl",responsiveClass:"owl-responsive",dragClass:"owl-drag",itemClass:"owl-item",stageClass:"owl-stage",stageOuterClass:"owl-stage-outer",grabClass:"owl-grab"},e.Width={Default:"default",Inner:"inner",Outer:"outer"},e.Type={Event:"event",State:"state"},e.Plugins={},e.Workers=[{filter:["width","settings"],run:function(){this._width=this.$element.width()}},{filter:["width","items","settings"],run:function(a){a.current=this._items&&this._items[this.relative(this._current)]}},{filter:["items","settings"],run:function(){this.$stage.children(".cloned").remove()}},{filter:["width","items","settings"],run:function(a){var b=this.settings.margin||"",c=!this.settings.autoWidth,d=this.settings.rtl,e={width:"auto","margin-left":d?b:"","margin-right":d?"":b};!c&&this.$stage.children().css(e),a.css=e}},{filter:["width","items","settings"],run:function(a){var b=(this.width()/this.settings.items).toFixed(3)-this.settings.margin,c=null,d=this._items.length,e=!this.settings.autoWidth,f=[];for(a.items={merge:!1,width:b};d--;)c=this._mergers[d],c=this.settings.mergeFit&&Math.min(c,this.settings.items)||c,a.items.merge=c>1||a.items.merge,f[d]=e?b*c:this._items[d].width();this._widths=f}},{filter:["items","settings"],run:function(){var b=[],c=this._items,d=this.settings,e=Math.max(2*d.items,4),f=2*Math.ceil(c.length/2),g=d.loop&&c.length?d.rewind?e:Math.max(e,f):0,h="",i="";for(g/=2;g--;)b.push(this.normalize(b.length/2,!0)),h+=c[b[b.length-1]][0].outerHTML,b.push(this.normalize(c.length-1-(b.length-1)/2,!0)),i=c[b[b.length-1]][0].outerHTML+i;this._clones=b,a(h).addClass("cloned").appendTo(this.$stage),a(i).addClass("cloned").prependTo(this.$stage)}},{filter:["width","items","settings"],run:function(){for(var a=this.settings.rtl?1:-1,b=this._clones.length+this._items.length,c=-1,d=0,e=0,f=[];++c<b;)d=f[c-1]||0,e=this._widths[this.relative(c)]+this.settings.margin,f.push(d+e*a);this._coordinates=f}},{filter:["width","items","settings"],run:function(){var a=this.settings.stagePadding,b=this._coordinates,c={width:Math.ceil(Math.abs(b[b.length-1]))+2*a,"padding-left":a||"","padding-right":a||""};this.$stage.css(c)}},{filter:["width","items","settings"],run:function(a){var b=this._coordinates.length,c=!this.settings.autoWidth,d=this.$stage.children();if(c&&a.items.merge)for(;b--;)a.css.width=this._widths[this.relative(b)],d.eq(b).css(a.css);else c&&(a.css.width=a.items.width,d.css(a.css))}},{filter:["items"],run:function(){this._coordinates.length<1&&this.$stage.removeAttr("style")}},{filter:["width","items","settings"],run:function(a){a.current=a.current?this.$stage.children().index(a.current):0,a.current=Math.max(this.minimum(),Math.min(this.maximum(),a.current)),this.reset(a.current)}},{filter:["position"],run:function(){this.animate(this.coordinates(this._current))}},{filter:["width","position","items","settings"],run:function(){var a,b,c,d,e=this.settings.rtl?1:-1,f=2*this.settings.stagePadding,g=this.coordinates(this.current())+f,h=g+this.width()*e,i=[];for(c=0,d=this._coordinates.length;c<d;c++)a=this._coordinates[c-1]||0,b=Math.abs(this._coordinates[c])+f*e,(this.op(a,"<=",g)&&this.op(a,">",h)||this.op(b,"<",g)&&this.op(b,">",h))&&i.push(c);this.$stage.children(".active").removeClass("active"),this.$stage.children(":eq("+i.join("), :eq(")+")").addClass("active"),this.settings.center&&(this.$stage.children(".center").removeClass("center"),this.$stage.children().eq(this.current()).addClass("center"))}}],e.prototype.initialize=function(){if(this.enter("initializing"),this.trigger("initialize"),this.$element.toggleClass(this.settings.rtlClass,this.settings.rtl),this.settings.autoWidth&&!this.is("pre-loading")){var b,c,e;b=this.$element.find("img"),c=this.settings.nestedItemSelector?"."+this.settings.nestedItemSelector:d,e=this.$element.children(c).width(),b.length&&e<=0&&this.preloadAutoWidthImages(b)}this.$element.addClass(this.options.loadingClass),this.$stage=a("<"+this.settings.stageElement+' class="'+this.settings.stageClass+'"/>').wrap('<div class="'+this.settings.stageOuterClass+'"/>'),this.$element.append(this.$stage.parent()),this.replace(this.$element.children().not(this.$stage.parent())),this.$element.is(":visible")?this.refresh():this.invalidate("width"),this.$element.removeClass(this.options.loadingClass).addClass(this.options.loadedClass),this.registerEventHandlers(),this.leave("initializing"),this.trigger("initialized")},e.prototype.setup=function(){var b=this.viewport(),c=this.options.responsive,d=-1,e=null;c?(a.each(c,function(a){a<=b&&a>d&&(d=Number(a))}),e=a.extend({},this.options,c[d]),"function"==typeof e.stagePadding&&(e.stagePadding=e.stagePadding()),delete e.responsive,e.responsiveClass&&this.$element.attr("class",this.$element.attr("class").replace(new RegExp("("+this.options.responsiveClass+"-)\\S+\\s","g"),"$1"+d))):e=a.extend({},this.options),this.trigger("change",{property:{name:"settings",value:e}}),this._breakpoint=d,this.settings=e,this.invalidate("settings"),this.trigger("changed",{property:{name:"settings",value:this.settings}})},e.prototype.optionsLogic=function(){this.settings.autoWidth&&(this.settings.stagePadding=!1,this.settings.merge=!1)},e.prototype.prepare=function(b){var c=this.trigger("prepare",{content:b});return c.data||(c.data=a("<"+this.settings.itemElement+"/>").addClass(this.options.itemClass).append(b)),this.trigger("prepared",{content:c.data}),c.data},e.prototype.update=function(){for(var b=0,c=this._pipe.length,d=a.proxy(function(a){return this[a]},this._invalidated),e={};b<c;)(this._invalidated.all||a.grep(this._pipe[b].filter,d).length>0)&&this._pipe[b].run(e),b++;this._invalidated={},!this.is("valid")&&this.enter("valid")},e.prototype.width=function(a){switch(a=a||e.Width.Default){case e.Width.Inner:case e.Width.Outer:return this._width;default:return this._width-2*this.settings.stagePadding+this.settings.margin}},e.prototype.refresh=function(){this.enter("refreshing"),this.trigger("refresh"),this.setup(),this.optionsLogic(),this.$element.addClass(this.options.refreshClass),this.update(),this.$element.removeClass(this.options.refreshClass),this.leave("refreshing"),this.trigger("refreshed")},e.prototype.onThrottledResize=function(){b.clearTimeout(this.resizeTimer),this.resizeTimer=b.setTimeout(this._handlers.onResize,this.settings.responsiveRefreshRate)},e.prototype.onResize=function(){return!!this._items.length&&(this._width!==this.$element.width()&&(!!this.$element.is(":visible")&&(this.enter("resizing"),this.trigger("resize").isDefaultPrevented()?(this.leave("resizing"),!1):(this.invalidate("width"),this.refresh(),this.leave("resizing"),void this.trigger("resized")))))},e.prototype.registerEventHandlers=function(){a.support.transition&&this.$stage.on(a.support.transition.end+".owl.core",a.proxy(this.onTransitionEnd,this)),this.settings.responsive!==!1&&this.on(b,"resize",this._handlers.onThrottledResize),this.settings.mouseDrag&&(this.$element.addClass(this.options.dragClass),this.$stage.on("mousedown.owl.core",a.proxy(this.onDragStart,this)),this.$stage.on("dragstart.owl.core selectstart.owl.core",function(){return!1})),this.settings.touchDrag&&(this.$stage.on("touchstart.owl.core",a.proxy(this.onDragStart,this)),this.$stage.on("touchcancel.owl.core",a.proxy(this.onDragEnd,this)))},e.prototype.onDragStart=function(b){var d=null;3!==b.which&&(a.support.transform?(d=this.$stage.css("transform").replace(/.*\(|\)| /g,"").split(","),d={x:d[16===d.length?12:4],y:d[16===d.length?13:5]}):(d=this.$stage.position(),d={x:this.settings.rtl?d.left+this.$stage.width()-this.width()+this.settings.margin:d.left,y:d.top}),this.is("animating")&&(a.support.transform?this.animate(d.x):this.$stage.stop(),this.invalidate("position")),this.$element.toggleClass(this.options.grabClass,"mousedown"===b.type),this.speed(0),this._drag.time=(new Date).getTime(),this._drag.target=a(b.target),this._drag.stage.start=d,this._drag.stage.current=d,this._drag.pointer=this.pointer(b),a(c).on("mouseup.owl.core touchend.owl.core",a.proxy(this.onDragEnd,this)),a(c).one("mousemove.owl.core touchmove.owl.core",a.proxy(function(b){var d=this.difference(this._drag.pointer,this.pointer(b));a(c).on("mousemove.owl.core touchmove.owl.core",a.proxy(this.onDragMove,this)),Math.abs(d.x)<Math.abs(d.y)&&this.is("valid")||(b.preventDefault(),this.enter("dragging"),this.trigger("drag"))},this)))},e.prototype.onDragMove=function(a){var b=null,c=null,d=null,e=this.difference(this._drag.pointer,this.pointer(a)),f=this.difference(this._drag.stage.start,e);this.is("dragging")&&(a.preventDefault(),this.settings.loop?(b=this.coordinates(this.minimum()),c=this.coordinates(this.maximum()+1)-b,f.x=((f.x-b)%c+c)%c+b):(b=this.settings.rtl?this.coordinates(this.maximum()):this.coordinates(this.minimum()),c=this.settings.rtl?this.coordinates(this.minimum()):this.coordinates(this.maximum()),d=this.settings.pullDrag?-1*e.x/5:0,f.x=Math.max(Math.min(f.x,b+d),c+d)),this._drag.stage.current=f,this.animate(f.x))},e.prototype.onDragEnd=function(b){var d=this.difference(this._drag.pointer,this.pointer(b)),e=this._drag.stage.current,f=d.x>0^this.settings.rtl?"left":"right";a(c).off(".owl.core"),this.$element.removeClass(this.options.grabClass),(0!==d.x&&this.is("dragging")||!this.is("valid"))&&(this.speed(this.settings.dragEndSpeed||this.settings.smartSpeed),this.current(this.closest(e.x,0!==d.x?f:this._drag.direction)),this.invalidate("position"),this.update(),this._drag.direction=f,(Math.abs(d.x)>3||(new Date).getTime()-this._drag.time>300)&&this._drag.target.one("click.owl.core",function(){return!1})),this.is("dragging")&&(this.leave("dragging"),this.trigger("dragged"))},e.prototype.closest=function(b,c){var d=-1,e=30,f=this.width(),g=this.coordinates();return this.settings.freeDrag||a.each(g,a.proxy(function(a,h){return"left"===c&&b>h-e&&b<h+e?d=a:"right"===c&&b>h-f-e&&b<h-f+e?d=a+1:this.op(b,"<",h)&&this.op(b,">",g[a+1]||h-f)&&(d="left"===c?a+1:a),d===-1},this)),this.settings.loop||(this.op(b,">",g[this.minimum()])?d=b=this.minimum():this.op(b,"<",g[this.maximum()])&&(d=b=this.maximum())),d},e.prototype.animate=function(b){var c=this.speed()>0;this.is("animating")&&this.onTransitionEnd(),c&&(this.enter("animating"),this.trigger("translate")),a.support.transform3d&&a.support.transition?this.$stage.css({transform:"translate3d("+b+"px,0px,0px)",transition:this.speed()/1e3+"s"}):c?this.$stage.animate({left:b+"px"},this.speed(),this.settings.fallbackEasing,a.proxy(this.onTransitionEnd,this)):this.$stage.css({left:b+"px"})},e.prototype.is=function(a){return this._states.current[a]&&this._states.current[a]>0},e.prototype.current=function(a){if(a===d)return this._current;if(0===this._items.length)return d;if(a=this.normalize(a),this._current!==a){var b=this.trigger("change",{property:{name:"position",value:a}});b.data!==d&&(a=this.normalize(b.data)),this._current=a,this.invalidate("position"),this.trigger("changed",{property:{name:"position",value:this._current}})}return this._current},e.prototype.invalidate=function(b){return"string"===a.type(b)&&(this._invalidated[b]=!0,this.is("valid")&&this.leave("valid")),a.map(this._invalidated,function(a,b){return b})},e.prototype.reset=function(a){a=this.normalize(a),a!==d&&(this._speed=0,this._current=a,this.suppress(["translate","translated"]),this.animate(this.coordinates(a)),this.release(["translate","translated"]))},e.prototype.normalize=function(a,b){var c=this._items.length,e=b?0:this._clones.length;return!this.isNumeric(a)||c<1?a=d:(a<0||a>=c+e)&&(a=((a-e/2)%c+c)%c+e/2),a},e.prototype.relative=function(a){return a-=this._clones.length/2,this.normalize(a,!0)},e.prototype.maximum=function(a){var b,c,d,e=this.settings,f=this._coordinates.length;if(e.loop)f=this._clones.length/2+this._items.length-1;else if(e.autoWidth||e.merge){for(b=this._items.length,c=this._items[--b].width(),d=this.$element.width();b--&&(c+=this._items[b].width()+this.settings.margin,!(c>d)););f=b+1}else f=e.center?this._items.length-1:this._items.length-e.items;return a&&(f-=this._clones.length/2),Math.max(f,0)},e.prototype.minimum=function(a){return a?0:this._clones.length/2},e.prototype.items=function(a){return a===d?this._items.slice():(a=this.normalize(a,!0),this._items[a])},e.prototype.mergers=function(a){return a===d?this._mergers.slice():(a=this.normalize(a,!0),this._mergers[a])},e.prototype.clones=function(b){var c=this._clones.length/2,e=c+this._items.length,f=function(a){return a%2===0?e+a/2:c-(a+1)/2};return b===d?a.map(this._clones,function(a,b){return f(b)}):a.map(this._clones,function(a,c){return a===b?f(c):null})},e.prototype.speed=function(a){return a!==d&&(this._speed=a),this._speed},e.prototype.coordinates=function(b){var c,e=1,f=b-1;return b===d?a.map(this._coordinates,a.proxy(function(a,b){return this.coordinates(b)},this)):(this.settings.center?(this.settings.rtl&&(e=-1,f=b+1),c=this._coordinates[b],c+=(this.width()-c+(this._coordinates[f]||0))/2*e):c=this._coordinates[f]||0,c=Math.ceil(c))},e.prototype.duration=function(a,b,c){return 0===c?0:Math.min(Math.max(Math.abs(b-a),1),6)*Math.abs(c||this.settings.smartSpeed)},e.prototype.to=function(a,b){var c=this.current(),d=null,e=a-this.relative(c),f=(e>0)-(e<0),g=this._items.length,h=this.minimum(),i=this.maximum();this.settings.loop?(!this.settings.rewind&&Math.abs(e)>g/2&&(e+=f*-1*g),a=c+e,d=((a-h)%g+g)%g+h,d!==a&&d-e<=i&&d-e>0&&(c=d-e,a=d,this.reset(c))):this.settings.rewind?(i+=1,a=(a%i+i)%i):a=Math.max(h,Math.min(i,a)),this.speed(this.duration(c,a,b)),this.current(a),this.$element.is(":visible")&&this.update()},e.prototype.next=function(a){a=a||!1,this.to(this.relative(this.current())+1,a)},e.prototype.prev=function(a){a=a||!1,this.to(this.relative(this.current())-1,a)},e.prototype.onTransitionEnd=function(a){if(a!==d&&(a.stopPropagation(),(a.target||a.srcElement||a.originalTarget)!==this.$stage.get(0)))return!1;this.leave("animating"),this.trigger("translated")},e.prototype.viewport=function(){var d;return this.options.responsiveBaseElement!==b?d=a(this.options.responsiveBaseElement).width():b.innerWidth?d=b.innerWidth:c.documentElement&&c.documentElement.clientWidth?d=c.documentElement.clientWidth:console.warn("Can not detect viewport width."),d},e.prototype.replace=function(b){this.$stage.empty(),this._items=[],b&&(b=b instanceof jQuery?b:a(b)),this.settings.nestedItemSelector&&(b=b.find("."+this.settings.nestedItemSelector)),b.filter(function(){return 1===this.nodeType}).each(a.proxy(function(a,b){b=this.prepare(b),this.$stage.append(b),this._items.push(b),this._mergers.push(1*b.find("[data-merge]").addBack("[data-merge]").attr("data-merge")||1)},this)),this.reset(this.isNumeric(this.settings.startPosition)?this.settings.startPosition:0),this.invalidate("items")},e.prototype.add=function(b,c){var e=this.relative(this._current);c=c===d?this._items.length:this.normalize(c,!0),b=b instanceof jQuery?b:a(b),this.trigger("add",{content:b,position:c}),b=this.prepare(b),0===this._items.length||c===this._items.length?(0===this._items.length&&this.$stage.append(b),0!==this._items.length&&this._items[c-1].after(b),this._items.push(b),this._mergers.push(1*b.find("[data-merge]").addBack("[data-merge]").attr("data-merge")||1)):(this._items[c].before(b),this._items.splice(c,0,b),this._mergers.splice(c,0,1*b.find("[data-merge]").addBack("[data-merge]").attr("data-merge")||1)),this._items[e]&&this.reset(this._items[e].index()),this.invalidate("items"),this.trigger("added",{content:b,position:c})},e.prototype.remove=function(a){a=this.normalize(a,!0),a!==d&&(this.trigger("remove",{content:this._items[a],position:a}),this._items[a].remove(),this._items.splice(a,1),this._mergers.splice(a,1),this.invalidate("items"),this.trigger("removed",{content:null,position:a}))},e.prototype.preloadAutoWidthImages=function(b){b.each(a.proxy(function(b,c){this.enter("pre-loading"),c=a(c),a(new Image).one("load",a.proxy(function(a){c.attr("src",a.target.src),c.css("opacity",1),this.leave("pre-loading"),!this.is("pre-loading")&&!this.is("initializing")&&this.refresh()},this)).attr("src",c.attr("src")||c.attr("data-src")||c.attr("data-src-retina"))},this))},e.prototype.destroy=function(){this.$element.off(".owl.core"),this.$stage.off(".owl.core"),a(c).off(".owl.core"),this.settings.responsive!==!1&&(b.clearTimeout(this.resizeTimer),this.off(b,"resize",this._handlers.onThrottledResize));for(var d in this._plugins)this._plugins[d].destroy();this.$stage.children(".cloned").remove(),this.$stage.unwrap(),this.$stage.children().contents().unwrap(),this.$stage.children().unwrap(),this.$element.removeClass(this.options.refreshClass).removeClass(this.options.loadingClass).removeClass(this.options.loadedClass).removeClass(this.options.rtlClass).removeClass(this.options.dragClass).removeClass(this.options.grabClass).attr("class",this.$element.attr("class").replace(new RegExp(this.options.responsiveClass+"-\\S+\\s","g"),"")).removeData("owl.carousel")},e.prototype.op=function(a,b,c){var d=this.settings.rtl;switch(b){case"<":return d?a>c:a<c;case">":return d?a<c:a>c;case">=":return d?a<=c:a>=c;case"<=":return d?a>=c:a<=c}},e.prototype.on=function(a,b,c,d){a.addEventListener?a.addEventListener(b,c,d):a.attachEvent&&a.attachEvent("on"+b,c)},e.prototype.off=function(a,b,c,d){a.removeEventListener?a.removeEventListener(b,c,d):a.detachEvent&&a.detachEvent("on"+b,c)},e.prototype.trigger=function(b,c,d,f,g){var h={item:{count:this._items.length,index:this.current()}},i=a.camelCase(a.grep(["on",b,d],function(a){return a}).join("-").toLowerCase()),j=a.Event([b,"owl",d||"carousel"].join(".").toLowerCase(),a.extend({relatedTarget:this},h,c));return this._supress[b]||(a.each(this._plugins,function(a,b){b.onTrigger&&b.onTrigger(j)}),this.register({type:e.Type.Event,name:b}),this.$element.trigger(j),this.settings&&"function"==typeof this.settings[i]&&this.settings[i].call(this,j)),j},e.prototype.enter=function(b){a.each([b].concat(this._states.tags[b]||[]),a.proxy(function(a,b){this._states.current[b]===d&&(this._states.current[b]=0),this._states.current[b]++},this))},e.prototype.leave=function(b){a.each([b].concat(this._states.tags[b]||[]),a.proxy(function(a,b){this._states.current[b]--},this))},e.prototype.register=function(b){if(b.type===e.Type.Event){if(a.event.special[b.name]||(a.event.special[b.name]={}),!a.event.special[b.name].owl){var c=a.event.special[b.name]._default;a.event.special[b.name]._default=function(a){return!c||!c.apply||a.namespace&&a.namespace.indexOf("owl")!==-1?a.namespace&&a.namespace.indexOf("owl")>-1:c.apply(this,arguments)},a.event.special[b.name].owl=!0}}else b.type===e.Type.State&&(this._states.tags[b.name]?this._states.tags[b.name]=this._states.tags[b.name].concat(b.tags):this._states.tags[b.name]=b.tags,this._states.tags[b.name]=a.grep(this._states.tags[b.name],a.proxy(function(c,d){return a.inArray(c,this._states.tags[b.name])===d},this)))},e.prototype.suppress=function(b){a.each(b,a.proxy(function(a,b){this._supress[b]=!0},this))},e.prototype.release=function(b){a.each(b,a.proxy(function(a,b){delete this._supress[b]},this))},e.prototype.pointer=function(a){var c={x:null,y:null};return a=a.originalEvent||a||b.event,a=a.touches&&a.touches.length?a.touches[0]:a.changedTouches&&a.changedTouches.length?a.changedTouches[0]:a,a.pageX?(c.x=a.pageX,c.y=a.pageY):(c.x=a.clientX,c.y=a.clientY),c},e.prototype.isNumeric=function(a){return!isNaN(parseFloat(a))},e.prototype.difference=function(a,b){return{x:a.x-b.x,y:a.y-b.y}},a.fn.owlCarousel=function(b){var c=Array.prototype.slice.call(arguments,1);return this.each(function(){var d=a(this),f=d.data("owl.carousel");f||(f=new e(this,"object"==typeof b&&b),d.data("owl.carousel",f),a.each(["next","prev","to","destroy","refresh","replace","add","remove"],function(b,c){f.register({type:e.Type.Event,name:c}),f.$element.on(c+".owl.carousel.core",a.proxy(function(a){a.namespace&&a.relatedTarget!==this&&(this.suppress([c]),f[c].apply(this,[].slice.call(arguments,1)),this.release([c]))},f))})),"string"==typeof b&&"_"!==b.charAt(0)&&f[b].apply(f,c)})},a.fn.owlCarousel.Constructor=e}(window.Zepto||window.jQuery,window,document),function(a,b,c,d){var e=function(b){this._core=b,this._interval=null,this._visible=null,this._handlers={"initialized.owl.carousel":a.proxy(function(a){a.namespace&&this._core.settings.autoRefresh&&this.watch()},this)},this._core.options=a.extend({},e.Defaults,this._core.options),this._core.$element.on(this._handlers)};e.Defaults={autoRefresh:!0,autoRefreshInterval:500},e.prototype.watch=function(){this._interval||(this._visible=this._core.$element.is(":visible"),this._interval=b.setInterval(a.proxy(this.refresh,this),this._core.settings.autoRefreshInterval))},e.prototype.refresh=function(){this._core.$element.is(":visible")!==this._visible&&(this._visible=!this._visible,this._core.$element.toggleClass("owl-hidden",!this._visible),this._visible&&this._core.invalidate("width")&&this._core.refresh())},e.prototype.destroy=function(){var a,c;b.clearInterval(this._interval);for(a in this._handlers)this._core.$element.off(a,this._handlers[a]);for(c in Object.getOwnPropertyNames(this))"function"!=typeof this[c]&&(this[c]=null)},a.fn.owlCarousel.Constructor.Plugins.AutoRefresh=e}(window.Zepto||window.jQuery,window,document),function(a,b,c,d){var e=function(b){this._core=b,this._loaded=[],this._handlers={"initialized.owl.carousel change.owl.carousel resized.owl.carousel":a.proxy(function(b){if(b.namespace&&this._core.settings&&this._core.settings.lazyLoad&&(b.property&&"position"==b.property.name||"initialized"==b.type))for(var c=this._core.settings,e=c.center&&Math.ceil(c.items/2)||c.items,f=c.center&&e*-1||0,g=(b.property&&b.property.value!==d?b.property.value:this._core.current())+f,h=this._core.clones().length,i=a.proxy(function(a,b){this.load(b)},this);f++<e;)this.load(h/2+this._core.relative(g)),h&&a.each(this._core.clones(this._core.relative(g)),i),g++},this)},this._core.options=a.extend({},e.Defaults,this._core.options),this._core.$element.on(this._handlers)};e.Defaults={lazyLoad:!1},e.prototype.load=function(c){var d=this._core.$stage.children().eq(c),e=d&&d.find(".owl-lazy");!e||a.inArray(d.get(0),this._loaded)>-1||(e.each(a.proxy(function(c,d){var e,f=a(d),g=b.devicePixelRatio>1&&f.attr("data-src-retina")||f.attr("data-src");this._core.trigger("load",{element:f,url:g},"lazy"),f.is("img")?f.one("load.owl.lazy",a.proxy(function(){f.css("opacity",1),this._core.trigger("loaded",{element:f,url:g},"lazy")},this)).attr("src",g):(e=new Image,e.onload=a.proxy(function(){f.css({"background-image":'url("'+g+'")',opacity:"1"}),this._core.trigger("loaded",{element:f,url:g},"lazy")},this),e.src=g)},this)),this._loaded.push(d.get(0)))},e.prototype.destroy=function(){var a,b;for(a in this.handlers)this._core.$element.off(a,this.handlers[a]);for(b in Object.getOwnPropertyNames(this))"function"!=typeof this[b]&&(this[b]=null)},a.fn.owlCarousel.Constructor.Plugins.Lazy=e}(window.Zepto||window.jQuery,window,document),function(a,b,c,d){var e=function(b){this._core=b,this._handlers={"initialized.owl.carousel refreshed.owl.carousel":a.proxy(function(a){a.namespace&&this._core.settings.autoHeight&&this.update()},this),"changed.owl.carousel":a.proxy(function(a){a.namespace&&this._core.settings.autoHeight&&"position"==a.property.name&&this.update()},this),"loaded.owl.lazy":a.proxy(function(a){a.namespace&&this._core.settings.autoHeight&&a.element.closest("."+this._core.settings.itemClass).index()===this._core.current()&&this.update()},this)},this._core.options=a.extend({},e.Defaults,this._core.options),this._core.$element.on(this._handlers)};e.Defaults={autoHeight:!1,autoHeightClass:"owl-height"},e.prototype.update=function(){var b=this._core._current,c=b+this._core.settings.items,d=this._core.$stage.children().toArray().slice(b,c),e=[],f=0;a.each(d,function(b,c){e.push(a(c).height())}),f=Math.max.apply(null,e),this._core.$stage.parent().height(f).addClass(this._core.settings.autoHeightClass)},e.prototype.destroy=function(){var a,b;for(a in this._handlers)this._core.$element.off(a,this._handlers[a]);for(b in Object.getOwnPropertyNames(this))"function"!=typeof this[b]&&(this[b]=null)},a.fn.owlCarousel.Constructor.Plugins.AutoHeight=e}(window.Zepto||window.jQuery,window,document),function(a,b,c,d){var e=function(b){this._core=b,this._videos={},this._playing=null,this._handlers={"initialized.owl.carousel":a.proxy(function(a){a.namespace&&this._core.register({type:"state",name:"playing",tags:["interacting"]})},this),"resize.owl.carousel":a.proxy(function(a){a.namespace&&this._core.settings.video&&this.isInFullScreen()&&a.preventDefault()},this),"refreshed.owl.carousel":a.proxy(function(a){a.namespace&&this._core.is("resizing")&&this._core.$stage.find(".cloned .owl-video-frame").remove()},this),"changed.owl.carousel":a.proxy(function(a){a.namespace&&"position"===a.property.name&&this._playing&&this.stop()},this),"prepared.owl.carousel":a.proxy(function(b){if(b.namespace){var c=a(b.content).find(".owl-video");c.length&&(c.css("display","none"),this.fetch(c,a(b.content)))}},this)},this._core.options=a.extend({},e.Defaults,this._core.options),this._core.$element.on(this._handlers),this._core.$element.on("click.owl.video",".owl-video-play-icon",a.proxy(function(a){this.play(a)},this))};e.Defaults={video:!1,videoHeight:!1,videoWidth:!1},e.prototype.fetch=function(a,b){var c=function(){return a.attr("data-vimeo-id")?"vimeo":a.attr("data-vzaar-id")?"vzaar":"youtube"}(),d=a.attr("data-vimeo-id")||a.attr("data-youtube-id")||a.attr("data-vzaar-id"),e=a.attr("data-width")||this._core.settings.videoWidth,f=a.attr("data-height")||this._core.settings.videoHeight,g=a.attr("href");if(!g)throw new Error("Missing video URL.");if(d=g.match(/(http:|https:|)\/\/(player.|www.|app.)?(vimeo\.com|youtu(be\.com|\.be|be\.googleapis\.com)|vzaar\.com)\/(video\/|videos\/|embed\/|channels\/.+\/|groups\/.+\/|watch\?v=|v\/)?([A-Za-z0-9._%-]*)(\&\S+)?/),d[3].indexOf("youtu")>-1)c="youtube";else if(d[3].indexOf("vimeo")>-1)c="vimeo";else{if(!(d[3].indexOf("vzaar")>-1))throw new Error("Video URL not supported.");c="vzaar"}d=d[6],this._videos[g]={type:c,id:d,width:e,height:f},b.attr("data-video",g),this.thumbnail(a,this._videos[g])},e.prototype.thumbnail=function(b,c){var d,e,f,g=c.width&&c.height?'style="width:'+c.width+"px;height:"+c.height+'px;"':"",h=b.find("img"),i="src",j="",k=this._core.settings,l=function(a){e='<div class="owl-video-play-icon"></div>',d=k.lazyLoad?'<div class="owl-video-tn '+j+'" '+i+'="'+a+'"></div>':'<div class="owl-video-tn" style="opacity:1;background-image:url('+a+')"></div>',b.after(d),b.after(e)};if(b.wrap('<div class="owl-video-wrapper"'+g+"></div>"),this._core.settings.lazyLoad&&(i="data-src",j="owl-lazy"),h.length)return l(h.attr(i)),h.remove(),!1;"youtube"===c.type?(f="//img.youtube.com/vi/"+c.id+"/hqdefault.jpg",l(f)):"vimeo"===c.type?a.ajax({type:"GET",url:"//vimeo.com/api/v2/video/"+c.id+".json",jsonp:"callback",dataType:"jsonp",success:function(a){f=a[0].thumbnail_large,l(f)}}):"vzaar"===c.type&&a.ajax({type:"GET",url:"//vzaar.com/api/videos/"+c.id+".json",jsonp:"callback",dataType:"jsonp",success:function(a){f=a.framegrab_url,l(f)}})},e.prototype.stop=function(){this._core.trigger("stop",null,"video"),this._playing.find(".owl-video-frame").remove(),this._playing.removeClass("owl-video-playing"),this._playing=null,this._core.leave("playing"),this._core.trigger("stopped",null,"video")},e.prototype.play=function(b){var c,d=a(b.target),e=d.closest("."+this._core.settings.itemClass),f=this._videos[e.attr("data-video")],g=f.width||"100%",h=f.height||this._core.$stage.height();this._playing||(this._core.enter("playing"),this._core.trigger("play",null,"video"),e=this._core.items(this._core.relative(e.index())),this._core.reset(e.index()),"youtube"===f.type?c='<iframe width="'+g+'" height="'+h+'" src="//www.youtube.com/embed/'+f.id+"?autoplay=1&rel=0&v="+f.id+'" frameborder="0" allowfullscreen></iframe>':"vimeo"===f.type?c='<iframe src="//player.vimeo.com/video/'+f.id+'?autoplay=1" width="'+g+'" height="'+h+'" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>':"vzaar"===f.type&&(c='<iframe frameborder="0"height="'+h+'"width="'+g+'" allowfullscreen mozallowfullscreen webkitAllowFullScreen src="//view.vzaar.com/'+f.id+'/player?autoplay=true"></iframe>'),a('<div class="owl-video-frame">'+c+"</div>").insertAfter(e.find(".owl-video")),this._playing=e.addClass("owl-video-playing"))},e.prototype.isInFullScreen=function(){var b=c.fullscreenElement||c.mozFullScreenElement||c.webkitFullscreenElement;return b&&a(b).parent().hasClass("owl-video-frame")},e.prototype.destroy=function(){var a,b;this._core.$element.off("click.owl.video");for(a in this._handlers)this._core.$element.off(a,this._handlers[a]);for(b in Object.getOwnPropertyNames(this))"function"!=typeof this[b]&&(this[b]=null)},a.fn.owlCarousel.Constructor.Plugins.Video=e}(window.Zepto||window.jQuery,window,document),function(a,b,c,d){var e=function(b){this.core=b,this.core.options=a.extend({},e.Defaults,this.core.options),this.swapping=!0,this.previous=d,this.next=d,this.handlers={"change.owl.carousel":a.proxy(function(a){a.namespace&&"position"==a.property.name&&(this.previous=this.core.current(),this.next=a.property.value)},this),"drag.owl.carousel dragged.owl.carousel translated.owl.carousel":a.proxy(function(a){a.namespace&&(this.swapping="translated"==a.type)},this),"translate.owl.carousel":a.proxy(function(a){a.namespace&&this.swapping&&(this.core.options.animateOut||this.core.options.animateIn)&&this.swap()},this)},this.core.$element.on(this.handlers)};e.Defaults={animateOut:!1,animateIn:!1},e.prototype.swap=function(){if(1===this.core.settings.items&&a.support.animation&&a.support.transition){this.core.speed(0);var b,c=a.proxy(this.clear,this),d=this.core.$stage.children().eq(this.previous),e=this.core.$stage.children().eq(this.next),f=this.core.settings.animateIn,g=this.core.settings.animateOut;this.core.current()!==this.previous&&(g&&(b=this.core.coordinates(this.previous)-this.core.coordinates(this.next),d.one(a.support.animation.end,c).css({left:b+"px"}).addClass("animated owl-animated-out").addClass(g)),f&&e.one(a.support.animation.end,c).addClass("animated owl-animated-in").addClass(f))}},e.prototype.clear=function(b){a(b.target).css({left:""}).removeClass("animated owl-animated-out owl-animated-in").removeClass(this.core.settings.animateIn).removeClass(this.core.settings.animateOut),this.core.onTransitionEnd()},e.prototype.destroy=function(){var a,b;for(a in this.handlers)this.core.$element.off(a,this.handlers[a]);for(b in Object.getOwnPropertyNames(this))"function"!=typeof this[b]&&(this[b]=null)},
a.fn.owlCarousel.Constructor.Plugins.Animate=e}(window.Zepto||window.jQuery,window,document),function(a,b,c,d){var e=function(b){this._core=b,this._timeout=null,this._paused=!1,this._handlers={"changed.owl.carousel":a.proxy(function(a){a.namespace&&"settings"===a.property.name?this._core.settings.autoplay?this.play():this.stop():a.namespace&&"position"===a.property.name&&this._core.settings.autoplay&&this._setAutoPlayInterval()},this),"initialized.owl.carousel":a.proxy(function(a){a.namespace&&this._core.settings.autoplay&&this.play()},this),"play.owl.autoplay":a.proxy(function(a,b,c){a.namespace&&this.play(b,c)},this),"stop.owl.autoplay":a.proxy(function(a){a.namespace&&this.stop()},this),"mouseover.owl.autoplay":a.proxy(function(){this._core.settings.autoplayHoverPause&&this._core.is("rotating")&&this.pause()},this),"mouseleave.owl.autoplay":a.proxy(function(){this._core.settings.autoplayHoverPause&&this._core.is("rotating")&&this.play()},this),"touchstart.owl.core":a.proxy(function(){this._core.settings.autoplayHoverPause&&this._core.is("rotating")&&this.pause()},this),"touchend.owl.core":a.proxy(function(){this._core.settings.autoplayHoverPause&&this.play()},this)},this._core.$element.on(this._handlers),this._core.options=a.extend({},e.Defaults,this._core.options)};e.Defaults={autoplay:!1,autoplayTimeout:5e3,autoplayHoverPause:!1,autoplaySpeed:!1},e.prototype.play=function(a,b){this._paused=!1,this._core.is("rotating")||(this._core.enter("rotating"),this._setAutoPlayInterval())},e.prototype._getNextTimeout=function(d,e){return this._timeout&&b.clearTimeout(this._timeout),b.setTimeout(a.proxy(function(){this._paused||this._core.is("busy")||this._core.is("interacting")||c.hidden||this._core.next(e||this._core.settings.autoplaySpeed)},this),d||this._core.settings.autoplayTimeout)},e.prototype._setAutoPlayInterval=function(){this._timeout=this._getNextTimeout()},e.prototype.stop=function(){this._core.is("rotating")&&(b.clearTimeout(this._timeout),this._core.leave("rotating"))},e.prototype.pause=function(){this._core.is("rotating")&&(this._paused=!0)},e.prototype.destroy=function(){var a,b;this.stop();for(a in this._handlers)this._core.$element.off(a,this._handlers[a]);for(b in Object.getOwnPropertyNames(this))"function"!=typeof this[b]&&(this[b]=null)},a.fn.owlCarousel.Constructor.Plugins.autoplay=e}(window.Zepto||window.jQuery,window,document),function(a,b,c,d){"use strict";var e=function(b){this._core=b,this._initialized=!1,this._pages=[],this._controls={},this._templates=[],this.$element=this._core.$element,this._overrides={next:this._core.next,prev:this._core.prev,to:this._core.to},this._handlers={"prepared.owl.carousel":a.proxy(function(b){b.namespace&&this._core.settings.dotsData&&this._templates.push('<div class="'+this._core.settings.dotClass+'">'+a(b.content).find("[data-dot]").addBack("[data-dot]").attr("data-dot")+"</div>")},this),"added.owl.carousel":a.proxy(function(a){a.namespace&&this._core.settings.dotsData&&this._templates.splice(a.position,0,this._templates.pop())},this),"remove.owl.carousel":a.proxy(function(a){a.namespace&&this._core.settings.dotsData&&this._templates.splice(a.position,1)},this),"changed.owl.carousel":a.proxy(function(a){a.namespace&&"position"==a.property.name&&this.draw()},this),"initialized.owl.carousel":a.proxy(function(a){a.namespace&&!this._initialized&&(this._core.trigger("initialize",null,"navigation"),this.initialize(),this.update(),this.draw(),this._initialized=!0,this._core.trigger("initialized",null,"navigation"))},this),"refreshed.owl.carousel":a.proxy(function(a){a.namespace&&this._initialized&&(this._core.trigger("refresh",null,"navigation"),this.update(),this.draw(),this._core.trigger("refreshed",null,"navigation"))},this)},this._core.options=a.extend({},e.Defaults,this._core.options),this.$element.on(this._handlers)};e.Defaults={nav:!1,navText:["prev","next"],navSpeed:!1,navElement:"div",navContainer:!1,navContainerClass:"owl-nav",navClass:["owl-prev","owl-next"],slideBy:1,dotClass:"owl-dot",dotsClass:"owl-dots",dots:!0,dotsEach:!1,dotsData:!1,dotsSpeed:!1,dotsContainer:!1},e.prototype.initialize=function(){var b,c=this._core.settings;this._controls.$relative=(c.navContainer?a(c.navContainer):a("<div>").addClass(c.navContainerClass).appendTo(this.$element)).addClass("disabled"),this._controls.$previous=a("<"+c.navElement+">").addClass(c.navClass[0]).html(c.navText[0]).prependTo(this._controls.$relative).on("click",a.proxy(function(a){this.prev(c.navSpeed)},this)),this._controls.$next=a("<"+c.navElement+">").addClass(c.navClass[1]).html(c.navText[1]).appendTo(this._controls.$relative).on("click",a.proxy(function(a){this.next(c.navSpeed)},this)),c.dotsData||(this._templates=[a("<div>").addClass(c.dotClass).append(a("<span>")).prop("outerHTML")]),this._controls.$absolute=(c.dotsContainer?a(c.dotsContainer):a("<div>").addClass(c.dotsClass).appendTo(this.$element)).addClass("disabled"),this._controls.$absolute.on("click","div",a.proxy(function(b){var d=a(b.target).parent().is(this._controls.$absolute)?a(b.target).index():a(b.target).parent().index();b.preventDefault(),this.to(d,c.dotsSpeed)},this));for(b in this._overrides)this._core[b]=a.proxy(this[b],this)},e.prototype.destroy=function(){var a,b,c,d;for(a in this._handlers)this.$element.off(a,this._handlers[a]);for(b in this._controls)this._controls[b].remove();for(d in this.overides)this._core[d]=this._overrides[d];for(c in Object.getOwnPropertyNames(this))"function"!=typeof this[c]&&(this[c]=null)},e.prototype.update=function(){var a,b,c,d=this._core.clones().length/2,e=d+this._core.items().length,f=this._core.maximum(!0),g=this._core.settings,h=g.center||g.autoWidth||g.dotsData?1:g.dotsEach||g.items;if("page"!==g.slideBy&&(g.slideBy=Math.min(g.slideBy,g.items)),g.dots||"page"==g.slideBy)for(this._pages=[],a=d,b=0,c=0;a<e;a++){if(b>=h||0===b){if(this._pages.push({start:Math.min(f,a-d),end:a-d+h-1}),Math.min(f,a-d)===f)break;b=0,++c}b+=this._core.mergers(this._core.relative(a))}},e.prototype.draw=function(){var b,c=this._core.settings,d=this._core.items().length<=c.items,e=this._core.relative(this._core.current()),f=c.loop||c.rewind;this._controls.$relative.toggleClass("disabled",!c.nav||d),c.nav&&(this._controls.$previous.toggleClass("disabled",!f&&e<=this._core.minimum(!0)),this._controls.$next.toggleClass("disabled",!f&&e>=this._core.maximum(!0))),this._controls.$absolute.toggleClass("disabled",!c.dots||d),c.dots&&(b=this._pages.length-this._controls.$absolute.children().length,c.dotsData&&0!==b?this._controls.$absolute.html(this._templates.join("")):b>0?this._controls.$absolute.append(new Array(b+1).join(this._templates[0])):b<0&&this._controls.$absolute.children().slice(b).remove(),this._controls.$absolute.find(".active").removeClass("active"),this._controls.$absolute.children().eq(a.inArray(this.current(),this._pages)).addClass("active"))},e.prototype.onTrigger=function(b){var c=this._core.settings;b.page={index:a.inArray(this.current(),this._pages),count:this._pages.length,size:c&&(c.center||c.autoWidth||c.dotsData?1:c.dotsEach||c.items)}},e.prototype.current=function(){var b=this._core.relative(this._core.current());return a.grep(this._pages,a.proxy(function(a,c){return a.start<=b&&a.end>=b},this)).pop()},e.prototype.getPosition=function(b){var c,d,e=this._core.settings;return"page"==e.slideBy?(c=a.inArray(this.current(),this._pages),d=this._pages.length,b?++c:--c,c=this._pages[(c%d+d)%d].start):(c=this._core.relative(this._core.current()),d=this._core.items().length,b?c+=e.slideBy:c-=e.slideBy),c},e.prototype.next=function(b){a.proxy(this._overrides.to,this._core)(this.getPosition(!0),b)},e.prototype.prev=function(b){a.proxy(this._overrides.to,this._core)(this.getPosition(!1),b)},e.prototype.to=function(b,c,d){var e;!d&&this._pages.length?(e=this._pages.length,a.proxy(this._overrides.to,this._core)(this._pages[(b%e+e)%e].start,c)):a.proxy(this._overrides.to,this._core)(b,c)},a.fn.owlCarousel.Constructor.Plugins.Navigation=e}(window.Zepto||window.jQuery,window,document),function(a,b,c,d){"use strict";var e=function(c){this._core=c,this._hashes={},this.$element=this._core.$element,this._handlers={"initialized.owl.carousel":a.proxy(function(c){c.namespace&&"URLHash"===this._core.settings.startPosition&&a(b).trigger("hashchange.owl.navigation")},this),"prepared.owl.carousel":a.proxy(function(b){if(b.namespace){var c=a(b.content).find("[data-hash]").addBack("[data-hash]").attr("data-hash");if(!c)return;this._hashes[c]=b.content}},this),"changed.owl.carousel":a.proxy(function(c){if(c.namespace&&"position"===c.property.name){var d=this._core.items(this._core.relative(this._core.current())),e=a.map(this._hashes,function(a,b){return a===d?b:null}).join();if(!e||b.location.hash.slice(1)===e)return;b.location.hash=e}},this)},this._core.options=a.extend({},e.Defaults,this._core.options),this.$element.on(this._handlers),a(b).on("hashchange.owl.navigation",a.proxy(function(a){var c=b.location.hash.substring(1),e=this._core.$stage.children(),f=this._hashes[c]&&e.index(this._hashes[c]);f!==d&&f!==this._core.current()&&this._core.to(this._core.relative(f),!1,!0)},this))};e.Defaults={URLhashListener:!1},e.prototype.destroy=function(){var c,d;a(b).off("hashchange.owl.navigation");for(c in this._handlers)this._core.$element.off(c,this._handlers[c]);for(d in Object.getOwnPropertyNames(this))"function"!=typeof this[d]&&(this[d]=null)},a.fn.owlCarousel.Constructor.Plugins.Hash=e}(window.Zepto||window.jQuery,window,document),function(a,b,c,d){function e(b,c){var e=!1,f=b.charAt(0).toUpperCase()+b.slice(1);return a.each((b+" "+h.join(f+" ")+f).split(" "),function(a,b){if(g[b]!==d)return e=!c||b,!1}),e}function f(a){return e(a,!0)}var g=a("<support>").get(0).style,h="Webkit Moz O ms".split(" "),i={transition:{end:{WebkitTransition:"webkitTransitionEnd",MozTransition:"transitionend",OTransition:"oTransitionEnd",transition:"transitionend"}},animation:{end:{WebkitAnimation:"webkitAnimationEnd",MozAnimation:"animationend",OAnimation:"oAnimationEnd",animation:"animationend"}}},j={csstransforms:function(){return!!e("transform")},csstransforms3d:function(){return!!e("perspective")},csstransitions:function(){return!!e("transition")},cssanimations:function(){return!!e("animation")}};j.csstransitions()&&(a.support.transition=new String(f("transition")),a.support.transition.end=i.transition.end[a.support.transition]),j.cssanimations()&&(a.support.animation=new String(f("animation")),a.support.animation.end=i.animation.end[a.support.animation]),j.csstransforms()&&(a.support.transform=new String(f("transform")),a.support.transform3d=j.csstransforms3d())}(window.Zepto||window.jQuery,window,document);
/*


   Magic Scroll v2.0.4 
   Copyright 2015 Magic Toolbox
   Buy a license: https://www.magictoolbox.com/magicscroll/
   License agreement: https://www.magictoolbox.com/license/


*/
eval(function(m,a,g,i,c,k){c=function(e){return(e<a?'':c(parseInt(e/a)))+((e=e%a)>35?String.fromCharCode(e+29):e.toString(36))};if(!''.replace(/^/,String)){while(g--){k[c(g)]=i[g]||c(g)}i=[function(e){return k[e]}];c=function(){return'\\w+'};g=1};while(g--){if(i[g]){m=m.replace(new RegExp('\\b'+c(g)+'\\b','g'),i[g])}}return m}('1l.4Q=(17(){1a p,q;p=q=(17(){1a N={5g:"gM.3-b3-8-gL",fM:0,8L:{},$6T:17(R){18(R.$6M||(R.$6M=++H.fM))},9u:17(R){18(H.8L[R]||(H.8L[R]={}))},$F:17(){},$1c:17(){18 1c},$1f:17(){18 1f},g3:"ey-"+1g.5Y(1g.7E()*1w bF().fr()),3c:17(R){18(2U!=R)},bJ:17(S,R){18(2U!=S)?S:R},9B:17(R){18!!(R)},1P:17(R){if(!H.3c(R)){18 1c}if(R.$4W){18 R.$4W}if(!!R.52){if(1==R.52){18"7D"}if(3==R.52){18"fL"}}if(R.1q&&R.2Z){18"gK"}if(R.1q&&R.9F){18"3h"}if((R 4v 1l.fB||R 4v 1l.c4)&&R.4z===H.4h){18"2B"}if(R 4v 1l.5W){18"40"}if(R 4v 1l.c4){18"17"}if(R 4v 1l.ex){18"2e"}if(H.1e.5K){if(H.3c(R.ds)){18"1D"}}1b{if(R===1l.1D||R.4z==1l.1u||R.4z==1l.gJ||R.4z==1l.gN||R.4z==1l.gO||R.4z==1l.gR){18"1D"}}if(R 4v 1l.bF){18"fK"}if(R 4v 1l.gQ){18"gP"}if(R===1l){18"1l"}if(R===1k){18"1k"}18 bc(R)},1T:17(W,V){if(!(W 4v 1l.5W)){W=[W]}if(!V){18 W[0]}1r(1a U=0,S=W.1q;U<S;U++){if(!H.3c(W)){4Y}1r(1a T in V){if(!fB.29.4s.2k(V,T)){4Y}3M{W[U][T]=V[T]}3X(R){}}}18 W[0]},9w:17(V,U){if(!(V 4v 1l.5W)){V=[V]}1r(1a T=0,R=V.1q;T<R;T++){if(!H.3c(V[T])){4Y}if(!V[T].29){4Y}1r(1a S in(U||{})){if(!V[T].29[S]){V[T].29[S]=U[S]}}}18 V[0]},fv:17(T,S){if(!H.3c(T)){18 T}1r(1a R in(S||{})){if(!T[R]){T[R]=S[R]}}18 T},$3M:17(){1r(1a S=0,R=3h.1q;S<R;S++){3M{18 3h[S]()}3X(T){}}18 1i},$A:17(T){if(!H.3c(T)){18 H.$([])}if(T.fJ){18 H.$(T.fJ())}if(T.2Z){1a S=T.1q||0,R=1w 5W(S);4R(S--){R[S]=T[S]}18 H.$(R)}18 H.$(5W.29.b9.2k(T))},5X:17(){18 1w bF().fr()},6o:17(V){1a T;7a(H.1P(V)){1R"8s":T={};1r(1a U in V){T[U]=H.6o(V[U])}1O;1R"40":T=[];1r(1a S=0,R=V.1q;S<R;S++){T[S]=H.6o(V[S])}1O;2E:18 V}18 H.$(T)},$:17(T){1a R=1f;if(!H.3c(T)){18 1i}if(T.$bx){18 T}7a(H.1P(T)){1R"40":T=H.fv(T,H.1T(H.5W,{$bx:H.$F}));T.1A=T.ew;18 T;1O;1R"2e":1a S=1k.e9(T);if(H.3c(S)){18 H.$(S)}18 1i;1O;1R"1l":1R"1k":H.$6T(T);T=H.1T(T,H.3Y);1O;1R"7D":H.$6T(T);T=H.1T(T,H.3C);1O;1R"1D":T=H.1T(T,H.1u);1O;1R"fL":1R"17":1R"40":1R"fK":2E:R=1c;1O}if(R){18 H.1T(T,{$bx:H.$F})}1b{18 T}},$1w:17(R,T,S){18 H.$(H.by.6n(R)).e7(T||{}).2W(S||{})},eV:17(S,U,Y){1a V,T,W,X=[],R=-1;Y||(Y=H.g3);V=H.$(Y)||H.$1w("3e",{id:Y,1H:"9s/d1"}).4e((1k.gI||1k.4j),"1M");T=V.fT||V.eH;if("2e"!=H.1P(U)){1r(1a W in U){X.2c(W+":"+U[W])}U=X.9p(";")}if(T.g7){R=T.g7(S+" {"+U+"}",T.gH.1q)}1b{R=T.gB(S,U)}18 R},d3:17(U,R){1a T,S;T=H.$(U);if("7D"!==H.1P(T)){18}S=T.fT||T.eH;if(S.eG){S.eG(R)}1b{if(S.eF){S.eF(R)}}},gA:17(){18"gz-gy-gC-gD-gG".5F(/[gF]/g,17(T){1a S=1g.7E()*16|0,R=T=="x"?S:(S&3|8);18 R.8Q(16)}).9f()},gE:(17(){1a R;18 17(S){if(!R){R=1k.6n("a")}R.3v("9S",S);18("!!"+R.9S).5F("!!","")}})(),gx:17(T){1a U=0,R=T.1q;1r(1a S=0;S<R;++S){U=31*U+T.h8(S);U%=h7}18 U}};1a H=N;1a I=N.$;if(!1l.cK){1l.cK=N;1l.$ey=N.$}H.5W={$4W:"40",7z:17(U,V){1a R=14.1q;1r(1a S=14.1q,T=(V<0)?1g.6S(0,S+V):V||0;T<S;T++){if(14[T]===U){18 T}}18-1},3f:17(R,S){18 14.7z(R,S)!=-1},ew:17(R,U){1r(1a T=0,S=14.1q;T<S;T++){if(T in 14){R.2k(U,14[T],T,14)}}},4N:17(R,W){1a V=[];1r(1a U=0,S=14.1q;U<S;U++){if(U in 14){1a T=14[U];if(R.2k(W,14[U],U,14)){V.2c(T)}}}18 V},fm:17(R,V){1a U=[];1r(1a T=0,S=14.1q;T<S;T++){if(T in 14){U[T]=R.2k(V,14[T],T,14)}}18 U}};H.9w(ex,{$4W:"2e",48:17(){18 14.5F(/^\\s+|\\s+$/g,"")},eq:17(R,S){18(S||1c)?(14.8Q()===R.8Q()):(14.34().8Q()===R.34().8Q())},5V:17(){18 14.5F(/-\\D/g,17(R){18 R.ay(1).9f()})},aP:17(){18 14.5F(/[A-Z]/g,17(R){18("-"+R.ay(0).34())})},h6:17(R){18 1X(14,R||10)},h5:17(){18 3H(14)},db:17(){18!14.5F(/1f/i,"").48()},9J:17(S,R){R=R||"";18(R+14+R).7z(R+S+R)>-1}});N.9w(c4,{$4W:"17",1d:17(){1a S=H.$A(3h),R=14,T=S.6K();18 17(){18 R.5u(T||1i,S.cn(H.$A(3h)))}},6x:17(){1a S=H.$A(3h),R=14,T=S.6K();18 17(U){18 R.5u(T||1i,H.$([U||(H.1e.1z?1l.1D:1i)]).cn(S))}},3r:17(){1a S=H.$A(3h),R=14,T=S.6K();18 1l.4P(17(){18 R.5u(R,S)},T||0)},dJ:17(){1a S=H.$A(3h),R=14;18 17(){18 R.3r.5u(R,S)}},dg:17(){1a S=H.$A(3h),R=14,T=S.6K();18 1l.h9(17(){18 R.5u(R,S)},T||0)}});1a O={},G=3W.ha.34(),F=G.4G(/(5A|b6|5K|cL)\\/(\\d+\\.?\\d*)/i),K=G.4G(/(hd|bi)\\/(\\d+\\.?\\d*)/i)||G.4G(/(fq|aJ|fl|fb|91|bi)\\/(\\d+\\.?\\d*)/i),M=G.4G(/5g\\/(\\d+\\.?\\d*)/i),B=1k.7B.3e;17 C(S){1a R=S.ay(0).9f()+S.b9(1);18 S in B||("fI"+R)in B||("fp"+R)in B||("4O"+R)in B||("O"+R)in B}H.1e={51:{hc:!!(1k.hb),h4:!!(1l.gW),gV:!!(1k.gU),78:!!(1k.gY||1k.gZ||1k.9N||1k.d7||1k.h2||1k.gn||1k.ge||1k.gf||1k.gw),dk:!!(1l.gr)&&!!(1l.gq)&&(1l.aU&&"gt"in 1w aU),2Y:C("2Y"),ar:C("ar"),fa:C("fa"),2q:C("2q"),5O:1c,fO:1c,bB:1c,9C:(17(){18 1k.gg.gi("gh://gk.gm.gl/gs/ga/gc#g9","1.1")})()},cl:17(){18"gd"in 1l||(1l.fR&&1k 4v fR)}(),8h:G.4G(/(am|bb\\d+|iC).+|iA|iy\\/|iE|iF|iK|iJ|iI|iG|iH|ip(ev|eA|ad)|ix|iw|il |ii|hf|io|8h.+fb|iq|91 m(iv|in)i|iu( it)?|eQ|p(ir|is)\\/|iL|j7|j8|jb(4|6)0|j9|j1|iR\\.(1e|iN)|iZ|hB|hw (ce|eQ)|hx|hC/)?1f:1c,8n:(F&&F[1])?F[1].34():(1l.91)?"cL":!!(1l.hD)?"5K":(2U!==1k.hI||1i!=1l.hH)?"b6":(1i!==1l.hG||!3W.hE)?"5A":"hu",5g:(F&&F[2])?3H(F[2]):0,9m:(K&&K[1])?K[1].34():"",c6:(K&&K[2])?3H(K[2]):0,fz:"",ba:"",56:"",1z:0,7P:G.4G(/ip(?:ad|eA|ev)/)?"dw":(G.4G(/(?:hk|am)/)||3W.7P.4G(/hi|9W|hg/i)||["hm"])[0].34(),fs:1k.a0&&"es"==1k.a0.34(),fW:0,5x:17(){18(1k.a0&&"es"==1k.a0.34())?1k.4j:1k.7B},5O:1l.5O||1l.hq||1l.ho||1l.hp||1l.hJ||2U,aG:1l.aG||1l.fj||1l.fj||1l.hK||1l.i4||1l.i2||2U,5J:1c,8e:17(){if(H.1e.5J){18}1a U,T;H.1e.5J=1f;H.4j=H.$(1k.4j);H.9W=H.$(1l);3M{1a S=H.$1w("38").2W({1B:2z,1J:2z,6E:"2o",2D:"7o",1M:-i1}).4e(1k.4j);H.1e.fW=S.e5-S.dq;S.2J()}3X(R){}3M{U=H.$1w("38");T=U.3e;T.fZ="fP:9v(bI://),9v(bI://),ib 9v(bI://)";H.1e.51.fO=(/(9v\\s*\\(.*?){3}/).3E(T.fP);T=1i;U=1i}3X(R){}if(!H.1e.fX){H.1e.fX=H.aD("ar").aP()}3M{U=H.$1w("38");U.3e.fZ=H.aD("4N").aP()+":g1(ia);";H.1e.51.bB=!!U.3e.1q&&(!H.1e.1z||H.1e.1z>9);U=1i}3X(R){}if(!H.1e.51.bB){H.$(1k.7B).2X("fQ-i7-6g")}if(2U===1l.i8&&2U!==1l.hZ){O.6C="hP"}H.3Y.1s.2k(H.$(1k),"9c")}};(17(){1a V=[],U,T,S;17 R(){18!!(3h.9F.bu)}7a(H.1e.8n){1R"5K":if(!H.1e.5g){H.1e.5g=!!(1l.aU)?3:2}1O;1R"b6":H.1e.5g=(K&&K[2])?3H(K[2]):0;1O}H.1e[H.1e.8n]=1f;if(K&&"fq"===K[1]){H.1e.9m="aJ"}if(!!1l.aJ){H.1e.aJ=1f}if(K&&"bi"===K[1]){H.1e.9m="91";H.1e.91=1f}if("fl"===H.1e.9m&&(M&&M[1])){H.1e.c6=3H(M[1])}if("am"==H.1e.7P&&H.1e.5A&&(M&&M[1])){H.1e.fo=1f}U=({b6:["-fH-","fp","fH"],5A:["-5A-","fI","5A"],5K:["-4O-","4O","4O"],cL:["-o-","O","o"]})[H.1e.8n]||["","",""];H.1e.fz=U[0];H.1e.ba=U[1];H.1e.56=U[2];H.1e.1z=(!H.1e.5K)?2U:(1k.fD)?1k.fD:17(){1a W=0;if(H.1e.fs){18 5}7a(H.1e.5g){1R 2:W=6;1O;1R 3:W=7;1O}18 W}();V.2c(H.1e.7P+"-6g");if(H.1e.8h){V.2c("8h-6g")}if(H.1e.fo){V.2c("am-1e-6g")}if(H.1e.1z){H.1e.9m="ie";H.1e.c6=H.1e.1z;V.2c("ie"+H.1e.1z+"-6g");1r(T=11;T>H.1e.1z;T--){V.2c("lt-ie"+T+"-6g")}}if(H.1e.5A&&H.1e.5g<hX){H.1e.51.78=1c}if(H.1e.5O){H.1e.5O.2k(1l,17(){H.1e.51.5O=1f})}if(H.1e.51.9C){V.2c("9C-6g")}1b{V.2c("fQ-9C-6g")}S=(1k.7B.6j||"").4G(/\\S+/g)||[];1k.7B.6j=H.$(S).cn(V).9p(" ");if(H.1e.1z&&H.1e.1z<9){1k.6n("4t");1k.6n("33")}})();(17(){H.1e.78={cW:H.1e.51.78,f6:17(){18!!(1k.hW||1k[H.1e.56+"hV"]||1k.78||1k.hT||1k[H.1e.56+"hU"])},dn:17(R,S){S||(S={});if(14.cW){H.$(1k).1G(14.cD,14.d8=17(T){if(14.f6()){S.di&&S.di()}1b{H.$(1k).1V(14.cD,14.d8);S.d6&&S.d6()}}.6x(14));H.$(1k).1G(14.b8,14.5c=17(T){S.9t&&S.9t();H.$(1k).1V(14.b8,14.5c)}.6x(14));(R[H.1e.56+"hS"]||R[H.1e.56+"hR"]||R.hM||17(){}).2k(R)}1b{if(S.9t){S.9t()}}},hL:(1k.9N||1k.d7||1k[H.1e.56+"hN"]||1k[H.1e.56+"hO"]||17(){}).1d(1k),cD:1k.dY?"hQ":(1k.9N?"":H.1e.56)+"hY",b8:1k.dY?"i9":(1k.9N?"":H.1e.56)+"i6",i5:H.1e.56,i0:1i}})();1a Q=/\\S+/g,E=/^(5T(dW|dQ|dO|dS)i3)|((5w|6f)(dW|dQ|dO|dS))$/,J={"hs":("2U"===bc(B.e4))?"ht":"e4"},L={hn:1f,hh:1f,3j:1f,bS:1f,bM:1f},D=(1l.eg)?17(T,R){1a S=1l.eg(T,1i);18 S?S.hj(R)||S[R]:1i}:17(U,S){1a T=U.hl,R=1i;R=T?T[S]:1i;if(1i==R&&U.3e&&U.3e[S]){R=U.3e[S]}18 R};17 P(T){1a R,S;S=(H.1e.5A&&"4N"==T)?1c:(T in B);if(!S){R=H.1e.ba+T.ay(0).9f()+T.b9(1);if(R in B){18 R}}18 T}H.aD=P;H.3C={8B:17(R){18!(R||"").9J(" ")&&(14.6j||"").9J(R," ")},2X:17(V){1a S=(14.6j||"").4G(Q)||[],U=(V||"").4G(Q)||[],R=U.1q,T=0;1r(;T<R;T++){if(!H.$(S).3f(U[T])){S.2c(U[T])}}14.6j=S.9p(" ");18 14},4L:17(W){1a S=(14.6j||"").4G(Q)||[],V=(W||"").4G(Q)||[],R=V.1q,U=0,T;1r(;U<R;U++){if((T=H.$(S).7z(V[U]))>-1){S.bA(T,1)}}14.6j=W?S.9p(" "):"";18 14},hv:17(R){18 14.8B(R)?14.4L(R):14.2X(R)},2n:17(S){1a T=S.5V(),R=1i;S=J[T]||(J[T]=P(T));R=D(14,S);if("1Z"===R){R=1i}if(1i!==R){if("3j"==S){18 H.3c(R)?3H(R):1}if(E.3E(S)){R=1X(R,10)?R:"ct"}}18 R},1L:17(S,R){1a U=S.5V();3M{if("3j"==S){14.3V(R);18 14}S=J[U]||(J[U]=P(U));14.3e[S]=R+(("5e"==H.1P(R)&&!L[U])?"2G":"")}3X(T){}18 14},2W:17(S){1r(1a R in S){14.1L(R,S[R])}18 14},hF:17(){1a R={};H.$A(3h).1A(17(S){R[S]=14.2n(S)},14);18 R},3V:17(T,R){1a S;R=R||1c;14.3e.3j=T;T=1X(3H(T)*2z);if(R){if(0===T){if("6H"!=14.3e.6s){14.3e.6s="6H"}}1b{if("72"!=14.3e.6s){14.3e.6s="72"}}}if(H.1e.1z&&H.1e.1z<9){if(!6u(T)){if(!~14.3e.4N.7z("cE")){14.3e.4N+=" ei:ed.ec.cE(ax="+T+")"}1b{14.3e.4N=14.3e.4N.5F(/ax=\\d*/i,"ax="+T)}}1b{14.3e.4N=14.3e.4N.5F(/ei:ed.ec.cE\\(ax=\\d*\\)/i,"").48();if(""===14.3e.4N){14.3e.3U("4N")}}}18 14},e7:17(R){1r(1a S in R){if("2B"===S){14.2X(""+R[S])}1b{14.3v(S,""+R[S])}}18 14},4q:17(){18 14.2W({5r:"5G",6s:"6H"})},2M:17(){18 14.2W({5r:"",6s:"72"})},2j:17(){18{1B:14.e5,1J:14.6e}},hy:17(S){1a R=14.2j();R.1B-=(3H(14.2n("5T-1N-1B")||0)+3H(14.2n("5T-6q-1B")||0));R.1J-=(3H(14.2n("5T-1M-1B")||0)+3H(14.2n("5T-5h-1B")||0));if(!S){R.1B-=(3H(14.2n("5w-1N")||0)+3H(14.2n("5w-6q")||0));R.1J-=(3H(14.2n("5w-1M")||0)+3H(14.2n("5w-5h")||0))}18 R},9A:17(){18{1M:14.b0,1N:14.b5}},hz:17(){1a R=14,S={1M:0,1N:0};do{S.1N+=R.b5||0;S.1M+=R.b0||0;R=R.3q}4R(R);18 S},5j:17(){1a V=14,S=0,U=0;if(H.3c(1k.7B.e8)){1a R=14.e8(),T=H.$(1k).9A(),W=H.1e.5x();18{1M:R.1M+T.y-W.hA,1N:R.1N+T.x-W.ic}}do{S+=V.aV||0;U+=V.cO||0;V=V.ig}4R(V&&!(/^(?:4j|iW)$/i).3E(V.2y));18{1M:U,1N:S}},iV:17(){1a S=14.5j();1a R=14.2j();18{1M:S.1M,5h:S.1M+R.1J,1N:S.1N,6q:S.1N+R.1B}},iX:17(S){3M{14.eR=S}3X(R){14.iY=S}18 14},2J:17(){18(14.3q)?14.3q.9e(14):14},4B:17(){H.$A(14.2g).1A(17(R){if(3==R.52||8==R.52){18}H.$(R).4B()});14.2J();14.cr();if(14.$6M){H.8L[14.$6M]=1i;5t H.8L[14.$6M]}18 1i},2r:17(T,S){S=S||"5h";1a R=14.3n;("1M"==S&&R)?14.7q(T,R):14.8N(T);18 14},4e:17(T,S){1a R=H.$(T).2r(14,S);18 14},iU:17(R){14.2r(R.3q.iT(14,R));18 14},iO:17(R){if("7D"!==H.1P("2e"==H.1P(R)?R=1k.e9(R):R)){18 1c}18(14==R)?1c:(14.3f&&!(H.1e.ea))?(14.3f(R)):(14.eb)?!!(14.eb(R)&16):H.$A(14.7J(R.2y)).3f(R)}};H.3C.iP=H.3C.2n;H.3C.iQ=H.3C.2W;if(!1l.3C){1l.3C=H.$F;if(H.1e.8n.5A){1l.1k.6n("iS")}1l.3C.29=(H.1e.8n.5A)?1l["[[j0.29]]"]:{}}H.9w(1l.3C,{$4W:"7D"});H.3Y={2j:17(){if(H.1e.cl||H.1e.ja||H.1e.ea){18{1B:1l.jc,1J:1l.jd}}18{1B:H.1e.5x().dq,1J:H.1e.5x().j3}},9A:17(){18{x:1l.j2||H.1e.5x().b5,y:1l.j4||H.1e.5x().b0}},j5:17(){1a R=14.2j();18{1B:1g.6S(H.1e.5x().j6,R.1B),1J:1g.6S(H.1e.5x().iM,R.1J)}}};H.1T(1k,{$4W:"1k"});H.1T(1l,{$4W:"1l"});H.1T([H.3C,H.3Y],{2p:17(U,S){1a R=H.9u(14.$6M),T=R[U];if(2U!==S&&2U===T){T=R[U]=S}18(H.3c(T)?T:1i)},3p:17(T,S){1a R=H.9u(14.$6M);R[T]=S;18 14},3D:17(S){1a R=H.9u(14.$6M);5t R[S];18 14}});if(!(1l.d0&&1l.d0.29&&1l.d0.29.d2)){H.1T([H.3C,H.3Y],{d2:17(R){18 H.$A(14.9Q("*")).4N(17(T){3M{18(1==T.52&&T.6j.9J(R," "))}3X(S){}})}})}H.1T([H.3C,H.3Y],{fi:17(){18 14.d2(3h[0])},7J:17(){18 14.9Q(3h[0])}});if(H.1e.78.cW&&!1k.dp){H.3C.dp=17(){H.1e.78.dn(14)}}H.1u={$4W:"1D",74:H.$1c,27:17(){18 14.8q().4l()},8q:17(){if(14.dr){14.dr()}1b{14.ds=1f}18 14},4l:17(){if(14.du){14.du()}1b{14.et=1c}18 14},8p:17(){14.74=H.$1f;18 14},dv:17(){1a S,R;S=((/5R/i).3E(14.1H))?14.3k[0]:14;18(!H.3c(S))?{x:0,y:0}:{x:S.3b,y:S.3a}},6I:17(){1a S,R;S=((/5R/i).3E(14.1H))?14.3k[0]:14;18(!H.3c(S))?{x:0,y:0}:{x:S.6m||S.3b+H.1e.5x().b5,y:S.6r||S.3a+H.1e.5x().b0}},cc:17(){1a R=14.1Y||14.ih;4R(R&&3==R.52){R=R.3q}18 R},az:17(){1a S=1i;7a(14.1H){1R"5o":1R"ij":1R"ik":S=14.aC||14.im;1O;1R"6P":1R"iz":1R"iB":S=14.aC||14.iD;1O;2E:18 S}3M{4R(S&&3==S.52){S=S.3q}}3X(R){S=1i}18 S},7H:17(){if(!14.df&&14.3T!==2U){18(14.3T&1?1:(14.3T&2?3:(14.3T&4?2:0)))}18 14.df},je:17(){18(14.3i&&("5R"===14.3i||14.3i===14.6R))||(/5R/i).3E(14.1H)},gT:17(){18 14.3i?(("5R"===14.3i||14.6R===14.3i)&&14.d5):1===14.3k.1q&&(14.8t.1q?14.8t[0].4x==14.3k[0].4x:1f)}};H.ai="dj";H.9n="gb";H.7s="";if(!1k.dj){H.ai="gj";H.9n="go";H.7s="3N"}H.1u.1v={1H:"",x:1i,y:1i,36:1i,3T:1i,1Y:1i,aC:1i,$4W:"1D.gp",74:H.$1c,6D:H.$([]),4y:17(R){1a S=R;14.6D.2c(S)},27:17(){18 14.8q().4l()},8q:17(){14.6D.1A(17(S){3M{S.8q()}3X(R){}});18 14},4l:17(){14.6D.1A(17(S){3M{S.4l()}3X(R){}});18 14},8p:17(){14.74=H.$1f;18 14},dv:17(){18{x:14.3b,y:14.3a}},6I:17(){18{x:14.x,y:14.y}},cc:17(){18 14.1Y},az:17(){18 14.aC},7H:17(){18 14.3T},gu:17(){18 14.6D.1q>0?14.6D[0].cc():2U}};H.1T([H.3C,H.3Y],{1G:17(T,V,W,Z){1a Y,R,U,X,S;if("2e"==H.1P(T)){S=T.8g(" ");if(S.1q>1){T=S}}if(H.1P(T)=="40"){H.$(T).1A(14.1G.6x(14,V,W,Z));18 14}if(!T||!V||H.1P(T)!="2e"||H.1P(V)!="17"){18 14}if(T=="9c"&&H.1e.5J){V.2k(14);18 14}T=O[T]||T;W=1X(W||50);if(!V.$4d){V.$4d=1g.5Y(1g.7E()*H.5X())}Y=H.3Y.2p.2k(14,"96",{});R=Y[T];if(!R){Y[T]=R=H.$([]);U=14;if(H.1u.1v[T]){H.1u.1v[T].1Q.6B.2k(14,Z)}1b{R.3K=17(aa){aa=H.1T(aa||1l.e,{$4W:"1D"});H.3Y.1s.2k(U,T,H.$(aa))};14[H.ai](H.7s+T,R.3K,1c)}}X={1H:T,fn:V,cd:W,dI:V.$4d};R.2c(X);R.gv(17(ab,aa){18 ab.cd-aa.cd});18 14},1V:17(X){1a V=H.3Y.2p.2k(14,"96",{}),T,R,S,Y,W,U;W=3h.1q>1?3h[1]:-2z;if("2e"==H.1P(X)){U=X.8g(" ");if(U.1q>1){X=U}}if(H.1P(X)=="40"){H.$(X).1A(14.1V.6x(14,W));18 14}X=O[X]||X;if(!X||H.1P(X)!="2e"||!V||!V[X]){18 14}T=V[X]||[];1r(S=0;S<T.1q;S++){R=T[S];if(-2z==W||!!W&&W.$4d===R.dI){Y=T.bA(S--,1)}}if(0===T.1q){if(H.1u.1v[X]){H.1u.1v[X].1Q.2J.2k(14)}1b{14[H.9n](H.7s+X,T.3K,1c)}5t V[X]}18 14},1s:17(V,X){1a U=H.3Y.2p.2k(14,"96",{}),T,R,S;V=O[V]||V;if(!V||H.1P(V)!="2e"||!U||!U[V]){18 14}3M{X=H.1T(X||{},{1H:V})}3X(W){}if(2U===X.36){X.36=H.5X()}T=U[V]||[];1r(S=0;S<T.1q&&!(X.74&&X.74());S++){T[S].fn.2k(14,X)}},he:17(S,R){1a V=("9c"==S)?1c:1f,U=14,T;S=O[S]||S;if(!V){H.3Y.1s.2k(14,S);18 14}if(U===1k&&1k.aW&&!U.dy){U=1k.7B}if(1k.aW){T=1k.aW(S);T.h0(R,1f,1f)}1b{T=1k.h1();T.aK=S}if(1k.aW){U.dy(T)}1b{U.gX("3N"+R,T)}18 T},cr:17(){1a S=H.3Y.2p.2k(14,"96");if(!S){18 14}1r(1a R in S){H.3Y.1V.2k(14,R)}H.3Y.3D.2k(14,"96");18 14}});(17(R){if("4f"===1k.99){18 R.1e.8e.3r(1)}if(R.1e.5A&&R.1e.5g<h3){(17(){(R.$(["4F","4f"]).3f(1k.99))?R.1e.8e():3h.9F.3r(50)})()}1b{if(R.1e.5K&&R.1e.1z<9&&1l==1M){(17(){(R.$3M(17(){R.1e.5x().gS("1N");18 1f}))?R.1e.8e():3h.9F.3r(50)})()}1b{R.3Y.1G.2k(R.$(1k),"fu",R.1e.8e);R.3Y.1G.2k(R.$(1l),"2O",R.1e.8e)}}})(N);H.4h=17(){1a V=1i,S=H.$A(3h);if("2B"==H.1P(S[0])){V=S.6K()}1a R=17(){1r(1a Y in 14){14[Y]=H.6o(14[Y])}if(14.4z.$4a){14.$4a={};1a aa=14.4z.$4a;1r(1a Z in aa){1a X=aa[Z];7a(H.1P(X)){1R"17":14.$4a[Z]=H.4h.dC(14,X);1O;1R"8s":14.$4a[Z]=H.6o(X);1O;1R"40":14.$4a[Z]=H.6o(X);1O}}}1a W=(14.4g)?14.4g.5u(14,3h):14;5t 14.bu;18 W};if(!R.29.4g){R.29.4g=H.$F}if(V){1a U=17(){};U.29=V.29;R.29=1w U;R.$4a={};1r(1a T in V.29){R.$4a[T]=V.29[T]}}1b{R.$4a=1i}R.4z=H.4h;R.29.4z=R;H.1T(R.29,S[0]);H.1T(R,{$4W:"2B"});18 R};N.4h.dC=17(R,S){18 17(){1a U=14.bu;1a T=S.5u(R,3h);18 T}};(17(U){1a T=U.$;1a R=5,S=8u;U.1u.1v.2I=1w U.4h(U.1T(U.1u.1v,{1H:"2I",4g:17(X,W){1a V=W.6I();14.x=V.x;14.y=V.y;14.3b=W.3b;14.3a=W.3a;14.36=W.36;14.3T=W.7H();14.1Y=X;14.4y(W)}}));U.1u.1v.2I.1Q={1t:{8d:S,3T:1},6B:17(V){14.3p("1D:2I:1t",U.1T(U.6o(U.1u.1v.2I.1Q.1t),V||{}));14.1G("7X",U.1u.1v.2I.1Q.3K,1);14.1G("6l",U.1u.1v.2I.1Q.3K,1);14.1G("4k",U.1u.1v.2I.1Q.bt,1);if(U.1e.5K&&U.1e.1z<9){14.1G("8H",U.1u.1v.2I.1Q.3K,1)}},2J:17(){14.1V("7X",U.1u.1v.2I.1Q.3K);14.1V("6l",U.1u.1v.2I.1Q.3K);14.1V("4k",U.1u.1v.2I.1Q.bt);if(U.1e.5K&&U.1e.1z<9){14.1V("8H",U.1u.1v.2I.1Q.3K)}},bt:17(V){V.4l()},3K:17(Y){1a X,V,W;V=14.2p("1D:2I:1t");if(Y.1H!="8H"&&Y.7H()!=V.3T){18}if(14.2p("1D:2I:bk")){14.3D("1D:2I:bk");18}if("7X"==Y.1H){X=1w U.1u.1v.2I(14,Y);14.3p("1D:2I:9I",X)}1b{if("6l"==Y.1H){X=14.2p("1D:2I:9I");if(!X){18}W=Y.6I();14.3D("1D:2I:9I");X.4y(Y);if(Y.36-X.36<=V.8d&&1g.7Q(1g.4K(W.x-X.x,2)+1g.4K(W.y-X.y,2))<=R){14.1s("2I",X)}1k.1s("6l",Y)}1b{if(Y.1H=="8H"){X=1w U.1u.1v.2I(14,Y);14.1s("2I",X)}}}}}})(N);(17(S){1a R=S.$;S.1u.1v.2H=1w S.4h(S.1T(S.1u.1v,{1H:"2H",4T:"4c",6V:1c,4g:17(W,V,U){1a T=V.6I();14.x=T.x;14.y=T.y;14.3b=V.3b;14.3a=V.3a;14.36=V.36;14.3T=V.7H();14.1Y=W;14.4y(V);14.4T=U}}));S.1u.1v.2H.1Q={6B:17(){1a U=S.1u.1v.2H.1Q.dD.6x(14),T=S.1u.1v.2H.1Q.9r.6x(14);14.1G("7X",S.1u.1v.2H.1Q.bp,1);14.1G("6l",S.1u.1v.2H.1Q.9r,1);1k.1G("aF",U,1);1k.1G("6l",T,1);14.3p("1D:2H:5q:1k:7t",U);14.3p("1D:2H:5q:1k:5S",T)},2J:17(){14.1V("7X",S.1u.1v.2H.1Q.bp);14.1V("6l",S.1u.1v.2H.1Q.9r);R(1k).1V("aF",14.2p("1D:2H:5q:1k:7t")||S.$F);R(1k).1V("6l",14.2p("1D:2H:5q:1k:5S")||S.$F);14.3D("1D:2H:5q:1k:7t");14.3D("1D:2H:5q:1k:5S")},bp:17(U){1a T;if(1!=U.7H()){18}U.4l();T=1w S.1u.1v.2H(14,U,"4c");14.3p("1D:2H:4c",T)},9r:17(U){1a T;T=14.2p("1D:2H:4c");if(!T){18}U.4l();T=1w S.1u.1v.2H(14,U,"9a");14.3D("1D:2H:4c");14.1s("2H",T)},dD:17(U){1a T;T=14.2p("1D:2H:4c");if(!T){18}U.4l();if(!T.6V){T.6V=1f;14.1s("2H",T)}T=1w S.1u.1v.2H(14,U,"dB");14.1s("2H",T)}}})(N);(17(S){1a R=S.$;S.1u.1v.4H=1w S.4h(S.1T(S.1u.1v,{1H:"4H",88:1c,8l:1i,4g:17(V,U){1a T=U.6I();14.x=T.x;14.y=T.y;14.3b=U.3b;14.3a=U.3a;14.36=U.36;14.3T=U.7H();14.1Y=V;14.4y(U)}}));S.1u.1v.4H.1Q={1t:{8d:5B},6B:17(T){14.3p("1D:4H:1t",S.1T(S.6o(S.1u.1v.4H.1Q.1t),T||{}));14.1G("2I",S.1u.1v.4H.1Q.3K,1)},2J:17(){14.1V("2I",S.1u.1v.4H.1Q.3K)},3K:17(V){1a U,T;U=14.2p("1D:4H:1D");T=14.2p("1D:4H:1t");if(!U){U=1w S.1u.1v.4H(14,V);U.8l=4P(17(){U.88=1f;V.74=S.$1c;14.1s("2I",V);14.3D("1D:4H:1D")}.1d(14),T.8d+10);14.3p("1D:4H:1D",U);V.8p()}1b{3s(U.8l);14.3D("1D:4H:1D");if(!U.88){U.4y(V);V.8p().27();14.1s("4H",U)}1b{}}}}})(N);(17(X){1a W=X.$;17 R(Y){18 Y.3i?(("5R"===Y.3i||Y.6R===Y.3i)&&Y.d5):1===Y.3k.1q&&(Y.8t.1q?Y.8t[0].4x==Y.3k[0].4x:1f)}17 T(Y){if(Y.3i){18("5R"===Y.3i||Y.6R===Y.3i)?Y.aO:1i}1b{18 Y.3k[0].4x}}17 U(Y){if(Y.3i){18("5R"===Y.3i||Y.6R===Y.3i)?Y:1i}1b{18 Y.3k[0]}}X.1u.1v.3w=1w X.4h(X.1T(X.1u.1v,{1H:"3w",id:1i,4g:17(Z,Y){1a aa=U(Y);14.id=aa.aO||aa.4x;14.x=aa.6m;14.y=aa.6r;14.6m=aa.6m;14.6r=aa.6r;14.3b=aa.3b;14.3a=aa.3a;14.36=Y.36;14.3T=0;14.1Y=Z;14.4y(Y)}}));1a S=10,V=5B;X.1u.1v.3w.1Q={6B:17(Y){14.1G(["7T",1l.3W.4E?"aX":"9T"],X.1u.1v.3w.1Q.8b,1);14.1G(["7V",1l.3W.4E?"8c":"85"],X.1u.1v.3w.1Q.7y,1);14.1G("4k",X.1u.1v.3w.1Q.bn,1)},2J:17(){14.1V(["7T",1l.3W.4E?"aX":"9T"],X.1u.1v.3w.1Q.8b);14.1V(["7V",1l.3W.4E?"8c":"85"],X.1u.1v.3w.1Q.7y);14.1V("4k",X.1u.1v.3w.1Q.bn)},bn:17(Y){Y.4l()},8b:17(Y){if(!R(Y)){14.3D("1D:3w:1D");18}14.3p("1D:3w:1D",1w X.1u.1v.3w(14,Y));14.3p("1D:2I:bk",1f)},7y:17(ab){1a Z=X.5X(),aa=14.2p("1D:3w:1D"),Y=14.2p("1D:3w:1t");if(!aa||!R(ab)){18}14.3D("1D:3w:1D");if(aa.id==T(ab)&&ab.36-aa.36<=V&&1g.7Q(1g.4K(U(ab).6m-aa.x,2)+1g.4K(U(ab).6r-aa.y,2))<=S){14.3D("1D:2I:9I");ab.27();aa.4y(ab);14.1s("3w",aa)}}}})(N);H.1u.1v.4U=1w H.4h(H.1T(H.1u.1v,{1H:"4U",88:1c,8l:1i,4g:17(S,R){14.x=R.x;14.y=R.y;14.3b=R.3b;14.3a=R.3a;14.36=R.36;14.3T=0;14.1Y=S;14.4y(R)}}));H.1u.1v.4U.1Q={1t:{8d:8u},6B:17(R){14.3p("1D:4U:1t",H.1T(H.6o(H.1u.1v.4U.1Q.1t),R||{}));14.1G("3w",H.1u.1v.4U.1Q.3K,1)},2J:17(){14.1V("3w",H.1u.1v.4U.1Q.3K)},3K:17(T){1a S,R;S=14.2p("1D:4U:1D");R=14.2p("1D:4U:1t");if(!S){S=1w H.1u.1v.4U(14,T);S.8l=4P(17(){S.88=1f;T.74=H.$1c;14.1s("3w",T)}.1d(14),R.8d+10);14.3p("1D:4U:1D",S);T.8p()}1b{3s(S.8l);14.3D("1D:4U:1D");if(!S.88){S.4y(T);T.8p().27();14.1s("4U",S)}1b{}}}};(17(W){1a V=W.$;17 R(X){18 X.3i?(("5R"===X.3i||X.6R===X.3i)&&X.d5):1===X.3k.1q&&(X.8t.1q?X.8t[0].4x==X.3k[0].4x:1f)}17 T(X){if(X.3i){18("5R"===X.3i||X.6R===X.3i)?X.aO:1i}1b{18 X.3k[0].4x}}17 U(X){if(X.3i){18("5R"===X.3i||X.6R===X.3i)?X:1i}1b{18 X.3k[0]}}1a S=10;W.1u.1v.2u=1w W.4h(W.1T(W.1u.1v,{1H:"2u",4T:"4c",id:1i,6V:1c,4g:17(Z,Y,X){1a aa=U(Y);14.id=aa.aO||aa.4x;14.3b=aa.3b;14.3a=aa.3a;14.6m=aa.6m;14.6r=aa.6r;14.x=aa.6m;14.y=aa.6r;14.36=Y.36;14.3T=0;14.1Y=Z;14.4y(Y);14.4T=X}}));W.1u.1v.2u.1Q={6B:17(){1a Y=W.1u.1v.2u.1Q.aA.1d(14),X=W.1u.1v.2u.1Q.7y.1d(14);14.1G(["7T",1l.3W.4E?"aX":"9T"],W.1u.1v.2u.1Q.8b,1);14.1G(["7V",1l.3W.4E?"8c":"85"],W.1u.1v.2u.1Q.7y,1);14.1G(["aH",1l.3W.4E?"a1":"a7"],W.1u.1v.2u.1Q.aA,1);14.3p("1D:2u:5q:1k:7t",Y);14.3p("1D:2u:5q:1k:5S",X);V(1k).1G(1l.3W.4E?"a1":"a7",Y,1);V(1k).1G(1l.3W.4E?"8c":"85",X,1)},2J:17(){14.1V(["7T",1l.3W.4E?"aX":"9T"],W.1u.1v.2u.1Q.8b);14.1V(["7V",1l.3W.4E?"8c":"85"],W.1u.1v.2u.1Q.7y);14.1V(["aH",1l.3W.4E?"a1":"a7"],W.1u.1v.2u.1Q.aA);V(1k).1V(1l.3W.4E?"a1":"a7",14.2p("1D:2u:5q:1k:7t")||W.$F,1);V(1k).1V(1l.3W.4E?"8c":"85",14.2p("1D:2u:5q:1k:5S")||W.$F,1);14.3D("1D:2u:5q:1k:7t");14.3D("1D:2u:5q:1k:5S")},8b:17(Y){1a X;if(!R(Y)){18}X=1w W.1u.1v.2u(14,Y,"4c");14.3p("1D:2u:4c",X)},7y:17(Y){1a X;X=14.2p("1D:2u:4c");if(!X||!X.6V||X.id!=T(Y)){18}X=1w W.1u.1v.2u(14,Y,"9a");14.3D("1D:2u:4c");14.1s("2u",X)},aA:17(Y){1a X;X=14.2p("1D:2u:4c");if(!X||!R(Y)){18}if(X.id!=T(Y)){14.3D("1D:2u:4c");18}if(!X.6V&&1g.7Q(1g.4K(U(Y).6m-X.x,2)+1g.4K(U(Y).6r-X.y,2))>S){X.6V=1f;14.1s("2u",X)}if(!X.6V){18}X=1w W.1u.1v.2u(14,Y,"dB");14.1s("2u",X)}}})(N);H.1u.1v.4o=1w H.4h(H.1T(H.1u.1v,{1H:"4o",7m:1,bL:1,dx:1,4T:"jv",4g:17(S,R){14.36=R.36;14.3T=0;14.1Y=S;14.x=R.4S[0].3b+(R.4S[1].3b-R.4S[0].3b)/2;14.y=R.4S[0].3a+(R.4S[1].3a-R.4S[0].3a)/2;14.dA=1g.7Q(1g.4K(R.4S[0].3b-R.4S[1].3b,2)+1g.4K(R.4S[0].3a-R.4S[1].3a,2));14.4y(R)},6L:17(R){1a S;14.4T="m9";if(R.3k[0].4x!=14.6D[0].4S[0].4x||R.3k[1].4x!=14.6D[0].4S[1].4x){18}S=1g.7Q(1g.4K(R.3k[0].3b-R.3k[1].3b,2)+1g.4K(R.3k[0].3a-R.3k[1].3a,2));14.bL=14.7m;14.7m=S/14.dA;14.dx=14.7m/14.bL;14.x=R.3k[0].3b+(R.3k[1].3b-R.3k[0].3b)/2;14.y=R.3k[0].3a+(R.3k[1].3a-R.3k[0].3a)/2;14.4y(R)}}));H.1u.1v.4o.1Q={6B:17(){14.1G("7T",H.1u.1v.4o.1Q.c5,1);14.1G("7V",H.1u.1v.4o.1Q.cp,1);14.1G("aH",H.1u.1v.4o.1Q.co,1)},2J:17(){14.1V("7T",H.1u.1v.4o.1Q.c5);14.1V("7V",H.1u.1v.4o.1Q.cp);14.1V("aH",H.1u.1v.4o.1Q.co)},c5:17(S){1a R;if(S.4S.1q!=2){18}S.4l();R=1w H.1u.1v.4o(14,S);14.3p("1D:4o:1D",R)},cp:17(S){1a R;R=14.2p("1D:4o:1D");if(!R){18}S.4l();14.3D("1D:4o:1D")},co:17(S){1a R;R=14.2p("1D:4o:1D");if(!R){18}S.4l();R.6L(S);14.1s("4o",R)}};(17(W){1a U=W.$;W.1u.1v.4r=1w W.4h(W.1T(W.1u.1v,{1H:"4r",4g:17(ac,ab,ae,Y,X,ad,Z){1a aa=ab.6I();14.x=aa.x;14.y=aa.y;14.36=ab.36;14.1Y=ac;14.m7=ae||0;14.3O=Y||0;14.3J=X||0;14.lF=ad||0;14.lG=Z||0;14.c8=ab.c8||0;14.4M=1c;14.4y(ab)}}));1a V,S;17 R(){V=1i}17 T(X,Y){18(X>50)||(1===Y&&!("9W"==W.1e.7P&&X<1))||(0===X%12)||(0==X%4.kV)}W.1u.1v.4r.1Q={aK:"kU"in 1k||W.1e.1z>8?"kZ":"b1",6B:17(){14.1G(W.1u.1v.4r.1Q.aK,W.1u.1v.4r.1Q.3K,1)},2J:17(){14.1V(W.1u.1v.4r.1Q.aK,W.1u.1v.4r.1Q.3K,1)},3K:17(ac){1a ad=0,aa=0,Y=0,X=0,ab,Z;if(ac.dz){Y=ac.dz*-1}if(ac.dE!==2U){Y=ac.dE}if(ac.dF!==2U){Y=ac.dF}if(ac.dK!==2U){aa=ac.dK*-1}if(ac.3J){Y=-1*ac.3J}if(ac.3O){aa=ac.3O}if(0===Y&&0===aa){18}ad=0===Y?aa:Y;X=1g.6S(1g.1E(Y),1g.1E(aa));if(!V||X<V){V=X}ab=ad>0?"5Y":"8M";ad=1g[ab](ad/V);aa=1g[ab](aa/V);Y=1g[ab](Y/V);if(S){3s(S)}S=4P(R,5B);Z=1w W.1u.1v.4r(14,ac,ad,aa,Y,0,V);Z.4M=T(V,ac.c8||0);14.1s("4r",Z)}}})(N);H.9W=H.$(1l);H.by=H.$(1k);18 N})();(17(D){if(!D){6w"7k 7g 7i"}1a C=D.$;1a B=1l.l3||1l.l2||1i;p.g0=1w D.4h({2b:1i,5J:1c,1t:{aj:D.$F,67:D.$F,9d:D.$F,5c:D.$F,8k:D.$F,dG:D.$F,9Y:1c,dL:1f},1y:1i,9g:1i,ca:0,8j:{aj:17(E){if(E.1Y&&(5B===E.1Y.ap||dH===E.1Y.ap)&&E.l1){14.1t.aj.1d(1i,(E.4F-(14.1t.dL?14.ca:0))/E.l0).3r(1);14.ca=E.4F}},67:17(E){if(E){C(E).27()}14.9j();if(14.5J){18}14.5J=1f;14.9k();!14.1t.9Y&&14.1t.aj.1d(1i,1).3r(1);14.1t.67.1d(1i,14).3r(1);14.1t.8k.1d(1i,14).3r(1)},9d:17(E){if(E){C(E).27()}14.9j();14.5J=1c;14.9k();14.1t.9d.1d(1i,14).3r(1);14.1t.8k.1d(1i,14).3r(1)},5c:17(E){if(E){C(E).27()}14.9j();14.5J=1c;14.9k();14.1t.5c.1d(1i,14).3r(1);14.1t.8k.1d(1i,14).3r(1)}},9U:17(){C(["2O","9h","7p"]).1A(17(E){14.2b.1G(E,14.8j["3N"+E].6x(14).dJ(1))},14)},9j:17(){if(14.9g){3M{3s(14.9g)}3X(E){}14.9g=1i}C(["2O","9h","7p"]).1A(17(F){14.2b.1V(F)},14)},9k:17(){14.2j();if(14.2b.2p("1w")){1a E=14.2b.3q;14.2b.2J().3D("1w").2W({2D:"lw",1M:"1Z"});E.4B()}},dd:17(F){1a G=1w aU(),E;C(["9h","1K"]).1A(17(H){G["3N"+H]=C(17(I){14.8j["3N"+H].2k(14,I)}).1d(14)},14);G.5c=C(17(){14.1t.dG.1d(1i,14).3r(1);14.1t.9Y=1c;14.9U();14.2b.43=F}).1d(14);G.67=C(17(){if(5B!==G.ap&&dH!==G.ap){14.8j.5c.2k(14);18}E=G.lE;14.9U();if(B&&!D.1e.5K&&!("dw"===D.1e.7P&&D.1e.5g<lD)){14.2b.3v("43",B.lC(E))}1b{14.2b.43=F}}).1d(14);G.ls("lj",F);G.ln="lq";G.lp()},4g:17(F,E){14.1t=D.1T(14.1t,E);14.2b=C(F)||D.$1w("2b",{},{"6S-1B":"5G","6S-1J":"5G"}).4e(D.$1w("38").2X("6g-kX-2b").2W({2D:"7o",1M:-lo,1B:10,1J:10,6E:"6H"}).4e(1k.4j)).3p("1w",1f);if(D.1e.51.dk&&14.1t.9Y&&"2e"==D.1P(F)){14.dd(F);18}1a G=17(){if(14.de()){14.8j.67.2k(14)}1b{14.8j.5c.2k(14)}G=1i}.1d(14);14.9U();if("2e"==D.1P(F)){14.2b.43=F}1b{if(D.1e.5K&&5==D.1e.5g&&D.1e.1z<9){14.2b.dh=17(){if(/4F|4f/.3E(14.2b.99)){14.2b.dh=1i;G&&G()}}.1d(14)}14.2b.43=F.3S("43")}14.2b&&14.2b.4f&&G&&(14.9g=G.3r(2z))},g6:17(){14.9j();14.9k();14.5J=1c;18 14},de:17(){1a E=14.2b;18(E.ch)?(E.ch>0):(E.99)?("4f"==E.99):E.1B>0},2j:17(){18 14.1y||(14.1y={1B:14.2b.ch||14.2b.1B,1J:14.2b.lr||14.2b.1J})}})})(p);(17(C){if(!C){6w"7k 7g 7i"}if(C.4n){18}1a B=C.$;C.4n=1w C.4h({4g:17(E,D){1a F;14.el=C.$(E);14.1t=C.1T(14.1t,D);14.6k=1c;14.7Z=14.9H;F=C.4n.8I[14.1t.2Y]||14.1t.2Y;if("17"===C.1P(F)){14.7Z=F}1b{14.5I=14.8V(F)||14.8V("7n")}if("2e"==C.1P(14.1t.8a)){14.1t.8a="3z"===14.1t.8a?lm:1X(14.1t.8a)||1}},1t:{dl:60,3I:be,2Y:"7n",8a:1,1I:"li",8O:C.$F,57:C.$F,aB:C.$F,dt:C.$F,9V:1c,lk:1c},4Z:1i,5I:1i,7Z:1i,ll:17(D){14.1t.2Y=D;D=C.4n.8I[14.1t.2Y]||14.1t.2Y;if("17"===C.1P(D)){14.7Z=D}1b{14.7Z=14.9H;14.5I=14.8V(D)||14.8V("7n")}},2A:17(F){1a D=/\\%$/,E;14.4Z=F;14.bQ=0;14.4T=0;14.lu=0;14.9y={};14.7Y="7Y"===14.1t.1I||"7Y-4C"===14.1t.1I;14.2s="2s"===14.1t.1I||"2s-4C"===14.1t.1I;1r(E in 14.4Z){D.3E(14.4Z[E][0])&&(14.9y[E]=1f);if("4C"===14.1t.1I||"7Y-4C"===14.1t.1I||"2s-4C"===14.1t.1I){14.4Z[E].4C()}}14.ci=C.5X();14.dm=14.ci+14.1t.3I;14.1t.8O.2k();if(0===14.1t.3I){14.7b(1);14.1t.57.2k()}1b{14.9x=14.1n.1d(14);if(!14.1t.9V&&C.1e.51.5O){14.6k=C.1e.5O.2k(1l,14.9x)}1b{14.6k=14.9x.dg(1g.5Q(cS/14.1t.dl))}}18 14},bR:17(){if(14.6k){if(!14.1t.9V&&C.1e.51.5O&&C.1e.aG){C.1e.aG.2k(1l,14.6k)}1b{lB(14.6k)}14.6k=1c}},27:17(D){D=C.3c(D)?D:1c;14.bR();if(D){14.7b(1);14.1t.57.3r(10)}18 14},bO:17(F,E,D){F=3H(F);E=3H(E);18(E-F)*D+F},1n:17(){1a E=C.5X(),D=(E-14.ci)/14.1t.3I,F=1g.5Y(D);if(E>=14.dm&&F>=14.1t.8a){14.bR();14.7b(1);14.1t.57.3r(10);18 14}if(14.7Y&&14.bQ<F){1r(1a G in 14.4Z){14.4Z[G].4C()}}14.bQ=F;if(!14.1t.9V&&C.1e.51.5O){14.6k=C.1e.5O.2k(1l,14.9x)}14.7b((14.2s?F:0)+14.7Z(D%1))},7b:17(D){1a E={},G=D;1r(1a F in 14.4Z){if("3j"===F){E[F]=1g.5Q(14.bO(14.4Z[F][0],14.4Z[F][1],D)*2z)/2z}1b{E[F]=14.bO(14.4Z[F][0],14.4Z[F][1],D);14.9y[F]&&(E[F]+="%")}}14.1t.aB(E,14.el);14.2f(E);14.1t.dt(E,14.el)},2f:17(D){18 14.el.2W(D)},8V:17(D){1a E,F=1i;if("2e"!==C.1P(D)){18 1i}7a(D){1R"93":F=B([0,0,1,1]);1O;1R"7n":F=B([0.25,0.1,0.25,1]);1O;1R"7n-in":F=B([0.42,0,1,1]);1O;1R"7n-au":F=B([0,0,0.58,1]);1O;1R"7n-in-au":F=B([0.42,0,0.58,1]);1O;1R"ee":F=B([0.47,0,0.lA,0.lz]);1O;1R"ef":F=B([0.39,0.lv,0.lx,1]);1O;1R"ly":F=B([0.lg,0.cy,0.55,0.95]);1O;1R"dU":F=B([0.55,0.kY,0.68,0.53]);1O;1R"dR":F=B([0.25,0.46,0.45,0.94]);1O;1R"kW":F=B([0.l4,0.dN,0.l5,0.lc]);1O;1R"dP":F=B([0.55,0.ld,0.le,0.19]);1O;1R"dV":F=B([0.lf,0.61,0.dM,1]);1O;1R"lb":F=B([0.la,0.ej,0.dM,1]);1O;1R"l6":F=B([0.l7,0.dN,0.l8,0.22]);1O;1R"l9":F=B([0.e6,0.84,0.44,1]);1O;1R"lT":F=B([0.77,0,0.eh,1]);1O;1R"m5":F=B([0.m6,0.cy,0.lZ,0.m0]);1O;1R"m8":F=B([0.23,1,0.32,1]);1O;1R"mb":F=B([0.86,0,0.lX,1]);1O;1R"e3":F=B([0.95,0.cy,0.lN,0.lL]);1O;1R"dT":F=B([0.19,1,0.22,1]);1O;1R"lU":F=B([1,0,0,1]);1O;1R"lS":F=B([0.6,0.lM,0.98,0.lR]);1O;1R"lQ":F=B([0.lW,0.82,0.e6,1]);1O;1R"lV":F=B([0.lP,0.lO,0.15,0.86]);1O;1R"e1":F=B([0.6,-0.28,0.lJ,0.ej]);1O;1R"e2":F=B([0.eh,0.lI,0.32,1.lH]);1O;1R"lK":F=B([0.68,-0.55,0.lY,1.55]);1O;2E:D=D.5F(/\\s/g,"");if(D.4G(/^5E-5M\\((?:-?[0-9\\.]{0,}[0-9]{1,},){3}(?:-?[0-9\\.]{0,}[0-9]{1,})\\)$/)){F=D.5F(/^5E-5M\\s*\\(|\\)$/g,"").8g(",");1r(E=F.1q-1;E>=0;E--){F[E]=3H(F[E])}}}18 B(F)},9H:17(P){1a D=0,O=0,L=0,Q=0,N=0,J=0,K=14.1t.3I;17 I(R){18((D*R+O)*R+L)*R}17 H(R){18((Q*R+N)*R+J)*R}17 F(R){18(3*D*R+2*O)*R+L}17 M(R){18 1/(5B*R)}17 E(R,S){18 H(G(R,S))}17 G(Y,Z){1a X,W,V,S,R,U;17 T(aa){if(aa>=0){18 aa}1b{18 0-aa}}1r(V=Y,U=0;U<8;U++){S=I(V)-Y;if(T(S)<Z){18 V}R=F(V);if(T(R)<0.m1){1O}V=V-S/R}X=0;W=1;V=Y;if(V<X){18 X}if(V>W){18 W}4R(X<W){S=I(V);if(T(S-Y)<Z){18 V}if(Y>S){X=V}1b{W=V}V=(W-X)*0.5+X}18 V}L=3*14.5I[0];O=3*(14.5I[2]-14.5I[0])-L;D=1-L-O;J=3*14.5I[1];N=3*(14.5I[3]-14.5I[1])-J;Q=1-J-N;18 E(P,M(K))}});C.4n.8I={93:"93",ma:"ee",m2:"ef",m3:"e3",m4:"dT",lh:"dU",kS:"dR",jN:"dP",jO:"dV",jP:"e1",jQ:"e2",e0:17(E,D){D=D||[];18 1g.4K(2,10*--E)*1g.eB(20*E*1g.3A*(D[0]||1)/3)},jM:17(E,D){18 1-C.4n.8I.e0(1-E,D)},dZ:17(F){1r(1a E=0,D=1;1;E+=D,D/=2){if(F>=(7-4*E)/11){18 D*D-1g.4K((11-6*E-11*F)/4,2)}}},jL:17(D){18 1-C.4n.8I.dZ(1-D)},5G:17(D){18 0}}})(p);(17(C){if(!C){6w"7k 7g 7i"}if(C.dX){18}1a B=C.$;C.dX=1w C.4h(C.4n,{4g:17(D,E){14.bl=D;14.1t=C.1T(14.1t,E);14.6k=1c;14.$4a.4g()},2A:17(H){1a D=/\\%$/,G,F,E=H.1q;14.bH=H;14.a2=1w 5W(E);1r(F=0;F<E;F++){14.a2[F]={};1r(G in H[F]){D.3E(H[F][G][0])&&(14.a2[F][G]=1f);if("4C"===14.1t.1I||"7Y-4C"===14.1t.1I||"2s-4C"===14.1t.1I){14.bH[F][G].4C()}}}14.$4a.2A([]);18 14},7b:17(D){1r(1a E=0;E<14.bl.1q;E++){14.el=C.$(14.bl[E]);14.4Z=14.bH[E];14.9y=14.a2[E];14.$4a.7b(D)}}})})(p);(17(C){if(!C){6w"7k 7g 7i";18}if(C.bD){18}1a B=C.$;C.bD=17(E,F){1a D=14.8f=C.$1w("38",1i,{2D:"7o","z-1U":ek}).2X("jH");C.$(E).1G("5o",17(){D.4e(1k.4j)});C.$(E).1G("6P",17(){D.2J()});C.$(E).1G("aF",17(K){1a M=20,J=C.$(K).6I(),I=D.2j(),H=C.$(1l).2j(),L=C.$(1l).9A();17 G(P,N,O){18(O<(P-N)/2)?O:((O>(P+N)/2)?(O-N):(P-N)/2)}D.2W({1N:L.x+G(H.1B,I.1B+2*M,J.x-L.x)+M,1M:L.y+G(H.1J,I.1J+2*M,J.y-L.y)+M})});14.9s(F)};C.bD.29.9s=17(D){14.8f.3n&&14.8f.9e(14.8f.3n);14.8f.2r(1k.bC(D))}})(p);(17(C){if(!C){6w"7k 7g 7i";18}if(C.jG){18}1a B=C.$;C.al=17(G,F,E,D){14.aI=1i;14.62=C.$1w("an",1i,{2D:"7o","z-1U":ek,6s:"6H",3j:0.8}).2X(D||"").4e(E||1k.4j);14.dc(G);14.2M(F)};C.al.29.2M=17(D){14.62.2M();14.aI=14.4q.1d(14).3r(C.bJ(D,jI))};C.al.29.4q=17(D){3s(14.aI);14.aI=1i;if(14.62&&!14.bE){14.bE=1w p.4n(14.62,{3I:C.bJ(D,9q),57:17(){14.62.4B();5t 14.62;14.bE=1i}.1d(14)}).2A({3j:[14.62.2n("3j"),0]})}};C.al.29.dc=17(D){14.62.3n&&14.8f.9e(14.62.3n);14.62.2r(1k.bC(D))}})(p);(17(C){if(!C){6w"7k 7g 7i"}if(C.c9){18}1a F=C.$,B=1i,J={"65":1,40:2,5e:3,"17":4,2e:2z},D={"65":17(M,L,K){if("65"!=C.1P(L)){if(K||"2e"!=C.1P(L)){18 1c}1b{if(!/^(1f|1c)$/.3E(L)){18 1c}1b{L=L.db()}}}if(M.4s("3B")&&!F(M["3B"]).3f(L)){18 1c}B=L;18 1f},2e:17(M,L,K){if("2e"!==C.1P(L)){18 1c}1b{if(M.4s("3B")&&!F(M["3B"]).3f(L)){18 1c}1b{B=""+L;18 1f}}},5e:17(N,M,L){1a K=1c,P=/%$/,O=(C.1P(M)=="2e"&&P.3E(M));if(L&&!"5e"==bc M){18 1c}M=3H(M);if(6u(M)){18 1c}if(6u(N.7c)){N.7c=da.jJ}if(6u(N.bK)){N.bK=da.jK}if(N.4s("3B")&&!F(N["3B"]).3f(M)){18 1c}if(N.7c>M||M>N.bK){18 1c}B=O?(M+"%"):M;18 1f},40:17(N,L,K){if("2e"===C.1P(L)){3M{L=1l.jR.jS(L)}3X(M){18 1c}}if(C.1P(L)==="40"){B=L;18 1f}1b{18 1c}},"17":17(M,L,K){if(C.1P(L)==="17"){B=L;18 1f}1b{18 1c}}},E=17(P,O,L){1a N;N=P.4s("3G")?P.3G:[P];if("40"!=C.1P(N)){18 1c}1r(1a M=0,K=N.1q-1;M<=K;M++){if(D[N[M].1H](N[M],O,L)){18 1f}}18 1c},H=17(P){1a N,M,O,K,L;if(P.4s("3G")){K=P.3G.1q;1r(N=0;N<K;N++){1r(M=N+1;M<K;M++){if(J[P.3G[N]["1H"]]>J[P.3G[M].1H]){L=P.3G[N];P.3G[N]=P.3G[M];P.3G[M]=L}}}}18 P},I=17(N){1a M;M=N.4s("3G")?N.3G:[N];if("40"!=C.1P(M)){18 1c}1r(1a L=M.1q-1;L>=0;L--){if(!M[L].1H||!J.4s(M[L].1H)){18 1c}if(C.3c(M[L]["3B"])){if("40"!==C.1P(M[L]["3B"])){18 1c}1r(1a K=M[L]["3B"].1q-1;K>=0;K--){if(!D[M[L].1H]({1H:M[L].1H},M[L]["3B"][K],1f)){18 1c}}}}if(N.4s("2E")&&!E(N,N["2E"],1f)){18 1c}18 1f},G=17(K){14.5f={};14.1t={};14.d9(K)};C.1T(G.29,{d9:17(M){1a L,K,N;1r(L in M){if(!M.4s(L)){4Y}K=(L+"").48().5V();if(!14.5f.4s(K)){14.5f[K]=H(M[L]);if(!I(14.5f[K])){6w"jZ k0 k1 k2 \'"+L+"\' jY in "+M}14.1t[K]=2U}}},2f:17(L,K){L=(L+"").48().5V();if(C.1P(K)=="2e"){K=K.48()}if(14.5f.4s(L)){B=K;if(E(14.5f[L],K)){14.1t[L]=B}B=1i}},g8:17(K){K=(K+"").48().5V();if(14.5f.4s(K)){18 C.3c(14.1t[K])?14.1t[K]:14.5f[K]["2E"]}},7G:17(L){1r(1a K in L){14.2f(K,L[K])}},jX:17(){1a L=C.1T({},14.1t);1r(1a K in L){if(2U===L[K]&&2U!==14.5f[K]["2E"]){L[K]=14.5f[K]["2E"]}}18 L},8J:17(K){F(K.8g(";")).1A(F(17(L){L=L.8g(":");14.2f(L.6K().48(),L.9p(":"))}).1d(14))},9B:17(K){K=(K+"").48().5V();18 14.5f.4s(K)},jT:17(K){K=(K+"").48().5V();18 14.9B(K)&&C.3c(14.1t[K])},2J:17(K){K=(K+"").48().5V();if(14.9B(K)){5t 14.1t[K];5t 14.5f[K]}}});C.c9=G}(p));q.$9o=17(B){1a D=[],C;1r(C in B){if(!B.4s(C)||(C+"").jU(0,2)=="$J"){4Y}D.2c(B[C])}18 q.$A(D)};q.9b={4k:2,8H:2,6l:2,7X:2,jV:2,b1:2,jW:2,5o:2,6P:2,aF:2,jF:2,jE:2,8A:2,jn:2,jo:2,jp:2,g1:2,jq:2,bz:2,jm:2,jl:2,2O:1,jh:1,jg:2,5H:1,7t:1,fu:1,ji:1,7p:1,9h:1};q.jj={1k:1f,7D:1f,"2B":1f,8s:1f};q.6O={2K:17(F,E,C){if(q.1P(F)=="40"){j(F).1A(14.2K.6x(14,E,C));18 14}if(!F||!E||q.1P(F)!="2e"||q.1P(E)!="17"){18 14}if(F=="9c"&&q.1e.5J){E.2k(14);18 14}C=1X(C||10);if(!E.$4d){E.$4d=1g.5Y(1g.7E()*q.5X())}1a D=14.2p("8R",{});D[F]||(D[F]={});D[F][C]||(D[F][C]={});D[F]["5s"]||(D[F]["5s"]={});if(D[F][C][E.$4d]){18 14}if(D[F]["5s"][E.$4d]){14.fC(F,E)}1a B=14,G=17(H){18 E.2k(B,j(H))};if(q.9b[F]&&!D[F]["17"]){if(q.9b[F]==2){G=17(H){H=q.1T(H||1l.e,{$4W:"1D"});18 E.2k(B,j(H))}}D[F]["17"]=17(H){B.1s(F,H)};14[q.ai](q.7s+F,D[F]["17"],1c)}D[F][C][E.$4d]=G;D[F]["5s"][E.$4d]=C;18 14},1s:17(C,E){3M{E=q.1T(E||{},{1H:C})}3X(D){}if(!C||q.1P(C)!="2e"){18 14}1a B=14.2p("8R",{});B[C]||(B[C]={});B[C]["5s"]||(B[C]["5s"]={});q.$9o(B[C]).1A(17(F){if(F!=B[C]["5s"]&&F!=B[C]["17"]){q.$9o(F).1A(17(G){G(14)},14)}},E);18 14},fC:17(E,D){if(!E||!D||q.1P(E)!="2e"||q.1P(D)!="17"){18 14}if(!D.$4d){D.$4d=1g.5Y(1g.7E()*q.5X())}1a C=14.2p("8R",{});C[E]||(C[E]={});C[E]["5s"]||(C[E]["5s"]={});4J=C[E]["5s"][D.$4d];C[E][4J]||(C[E][4J]={});if(4J>=0&&C[E][4J][D.$4d]){5t C[E][4J][D.$4d];5t C[E]["5s"][D.$4d];if(q.$9o(C[E][4J]).1q==0){5t C[E][4J];if(q.9b[E]&&q.$9o(C[E]).1q==0){1a B=14;14[q.9n](q.7s+E,C[E]["17"],1c)}}}18 14},eK:17(D){if(!D||q.1P(D)!="2e"){18 14}1a C=14.2p("8R",{});if(q.9b[D]){1a B=14;14[q.9n](q.7s+D,C[D]["17"],1c)}C[D]={};18 14},jk:17(D,C){1a B=14.2p("8R",{});1r(t in B){if(C&&t!=C){4Y}1r(4J in B[t]){if(4J=="5s"||4J=="17"){4Y}1r(f in B[t][4J]){j(D).2K(t,B[t][4J][f],4J)}}}18 14},jr:17(E,D){if(1!==E.52){18 14}1a C=14.2p("6D");if(!C){18 14}1r(1a B in C){if(D&&B!=D){4Y}1r(1a F in C[B]){j(E).2K(B,C[B][F])}}18 14},2p:q.3C.2p,3p:q.3C.3p};(17(B){if(!B){6w"7k 7g 7i";18}B.1T=17(J,I){if(!(J 4v 1l.5W)){J=[J]}if(!(I 4v 1l.5W)){I=[I]}1r(1a G=0,D=J.1q;G<D;G++){if(!B.3c(J[G])){4Y}1r(1a F=0,H=I.1q;F<H;F++){if(!B.3c(I[F])){4Y}1r(1a E in(I[F]||{})){3M{J[G][E]=I[F][E]}3X(C){}}}}18 J[0]};B.c0=17(E,D){17 C(){}C.29=D.29;E.$4a=D.29;E.29=1w C();E.29.4z=E};B.1T([B.3C,1l.cK.3C],{fA:B.3C.2j,2j:17(C){1a D=14.fA();if(C){D.1B+=(1X(14.2n("6f-1N")||0)+1X(14.2n("6f-6q")||0));D.1J+=(1X(14.2n("6f-1M")||0)+((14.2n("5r")!="7C")?1X(14.2n("6f-5h")||0):0))}18 D}})})(p);q.3Q||(q.3Q={});q.3Q.f0=(17(){1a B=["8i","7M"],E;17 F(H,G){18 q.$1w("3T",1i,{5r:"7O-7C"}).2X(E["2B"]).2X(E.1S).2X(E["2B"]+"-fE").2X(E["2B"]+"-fE-"+H).4e(G)}17 C(G,H){H.8q();14.1s(G)}1a D=17(H,G){q.$6T(14);14.1t={"2B":"",9Z:"",aR:"",2D:"fY",1S:"4O-3l",js:"3T"};E=14.o=14.1t;q.1T(14.o,H);14.7M=F("7M",G);14.8i=F("8i",G);14.8i.1G("4k",17(I){I.27()}).1G("2I 3w",C.1d(14,"1F"));14.7M.1G("4k",17(I){I.27()}).1G("2I 3w",C.1d(14,"2w"))};D.29={7u:17(G){i(G&&[G]||B).1A(17(H){14[H].2X(E.aR)},14)},4i:17(G){i(G&&[G]||B).1A(17(H){14[H].4L(E.aR)},14)},4q:17(G){i(G&&[G]||B).1A(17(H){14[H].2X(E.9Z)},14)},2M:17(G){i(G&&[G]||B).1A(17(H){14[H].4L(E.9Z)},14)},2J:17(G){i(G&&[G]||B).1A(17(H){14[H].4B()},14)},eY:17(G){i(B).1A(17(H){14[H].4L("21-"+E.1S);14[H].2X("21-"+G)},14);14.o.1S="21-"+G}};q.1T(D.29,q.6O);18 D})();q.3Q||(q.3Q={});q.3Q.f3=(17(){1a C="jA",B=17(F,E,D){q.$6T(14);14.8r={};14.o=14.8r;q.1T(14.o,F);14.26=q.$([]);14.4A=D;14.5N={};14.a4=1c;14.1j=q.$1w("38",{"2B":"21-26"});14.1j.4e(E)};B.29={2c:17(D){1a E=i(17(G){1a F=14.26.1q;14.26.2c({1U:F,4i:1c,2P:G,1o:q.$1w("38",{"2B":"21-fG 21-fG-"+F})});if(!F){14.5N=14.26[F];14.8W(14.26[F]);14.26[F].4i=1f}14.26[F].1o.1G("4k",i(17(H){H.27();if(14.26[F].1U==14.5N.1U){18}14.a4=14.4A();!14.a4&&14.1s("26-4k",{1I:14.fy(14.26[F]),f2:14.26[F].2P})}).1d(14));14.26[F].1o.4e(14.1j)}).1d(14);14.bz();D.1A(i(17(F){E(F)}).1d(14))},cm:17(D,E){if(14.5N.1U==D[0]){18}14.8W(14.fw(D,E))},2M:17(){14.1j.2X("2M")},6L:17(){if(14.5N.1o){14.bd();14.8W(14.26[0])}},2J:17(){14.26.1A(17(D){D.1o.4B()});14.1j.4B()},bd:17(){14.5N.4i=1c;14.5N.1o.4L(C)},8W:17(D){14.bd();14.5N=D;D.4i=1f;D.1o.2X(C)},fy:17(D){1a E=14.5N.1U>D.1U?"2w":"1F";14.8W(D);18 E},fw:17(D,G){1a H,F=14.26.1q-1,E=14.5N;1r(1a H=F;H>=0;H--){if(14.26[H].2P<=D[0]){E=14.26[H];1O}}if(G){if(14.o.1h-1==D[D.1q-1]){E=14.26[F]}}18 E},bz:17(){14.a4=1c;14.5N={};14.26.1A(17(D){D.1o.4B()});14.26.1q=0}};q.1T(B.29,q.6O);18 B})();q.3Q||(q.3Q={});q.3Q.9i=(17(){1a C=8u,B=17(D,E){14.6J="5G";14.1o=q.$1w("38",{"2B":"21-3x"});if(q.1e.1z&&q.1e.1z<10){14.1o.2r(q.$1w("38",{"2B":"21-3x-9s"}).2r(q.by.bC("jB...")))}1b{if(E){14.1o.2r(q.$1w("38",{"2B":"21-3x-fk"}).2r(q.$1w("38",{"2B":"21-2Z-3x"},{"z-1U":eL})))}1b{14.1o.2r(q.$1w("38",{"2B":"21-3x-fk"}).2r(q.$1w("38",{"2B":"21-3x-2S 21-3x-jC"})).2r(q.$1w("38",{"2B":"21-3x-2S 21-3x-jD"})).2r(q.$1w("38",{"2B":"21-3x-2S 21-3x-jz"})).2r(q.$1w("38",{"2B":"21-3x-2S 21-3x-jy"})).2r(q.$1w("38",{"2B":"21-3x-2S 21-3x-ju"})).2r(q.$1w("38",{"2B":"21-3x-2S 21-3x-jt"})).2r(q.$1w("38",{"2B":"21-3x-2S 21-3x-kT"})).2r(q.$1w("38",{"2B":"21-3x-2S 21-3x-jw"})))}}14.1o.4e(D);14.1o.4q();if(q.1e.1z&&q.1e.1z<10){14.fx=1w q.4n(14.1o,{3I:C,57:14.cN.1d(14)})}1b{14.1o.1G("6C",i(17(F){if(F.1Y==14.1o){14.cN()}}).1d(14));14.1o.1L("2Y","3j "+C+"4O")}};B.29={4f:1f,cN:17(){14.4f=1f;14.1o.4q();14.6J=="cF"&&14.5k()},2M:17(){if(14.6J=="2M"){18}14.4f=1c;14.6J="2M";14.1o.3V(1);14.1o.2M()},4q:17(D){if(14.6J=="4q"){18}14.6J=D?"cF":"4q";if(14.4f){14.5k()}1b{if(14.fx){14.fx.2A({3j:[0.6,0]})}1b{4P(17(){14.1o.3V(0)}.1d(14),1)}}},2J:17(){if(14.6J=="4q"){14.6J="cF";14.4f&&14.1o.4B()}1b{14.4q()}},5k:17(){14.1o.4B()}};18 B})();q.3Q||(q.3Q={});q.3Q.jx=(17(){1a B=17(){1a I=[],D=8u,F=0,G=0,J=1c,H=14;q.$6T(14);17 E(){1a M;if(I.1q==0){H.1s("4f");18}if(!J&&I.1q>0){J=1f;M=I.6K();1a L=i([]);L.2c(M.2Z);if(M.2Z.5l&&M.2Z.5l.1q>0){i(M.2Z.5l).1A(i(17(N){L.2c(N)}).1d(14))}L.1A(17(O,N){G+=1;if(M.72){if(N){M.72=1c}}K(O,!!N,M.72,M.4A,17(){J=1c;E()},M.7S)})}}17 C(M,O,L,N){if(M.1K){M.1K.4q(1f)}F++;if(F==G){G=F=0;L();N()}}17 K(R,Q,N,O,M,L){1a S,T,P=i(R.2m);if(R.2O=="4F"){C(R,Q,O,M);18}if(N){if(q.1e.1z&&q.1e.1z<10){T=i(P).2j();S={3j:[0,1],1M:[T.1J/2,0],1N:[T.1B/2,0],1B:[0,T.1B],1J:[0,T.1J]};14.g2=1w q.4n(P,{3I:D,57:i(17(V,U){P.2W({6E:"",2D:"",1M:"",1N:"",1B:"",1J:""});Q&&(R.2O="4F");C(R,Q,V,U)}).1d(14,O,M),8O:i(17(){P.2W({2D:"fS",6E:"6H"})}).1d(14)});14.g2.2A(S)}1b{P.1L(e,"7m(0.2, 0.2)");P.1L("2Y","5G");P.3V(0);P.6e;P.3q.6e;P.1G("6C",i(17(U){if(U.1Y==P){14.1V(U.1H);14.1L(e,"");14.1L("2Y","")}}).1d(P));if(!Q&&L){L(R)}P.1L("2Y",e+" "+D+"4O 5E-5M(.5,.5,.69,1.9), 3j "+D+"4O 93");P.6e;P.3q.6e;P.1L(e,"7m(1.0, 1.0)");P.3V(1);Q&&(R.2O="4F");C(R,Q,O,M)}}1b{P.3V(1);if(Q){R.2O="4F"}1b{L(R)}C(R,Q,O,M)}}14.2c=17(N,M,L,O){I.2c({2Z:N,72:M,4A:L,7S:O});E()}};q.1T(B.29,q.6O);18 B})();(17(B){B.cf=17(I,D){1a C=0,H=14,G,E;17 L(M){18 17(N){(D[M]||B.$F).2k(H,N,N.g4);C--;K()}}17 K(){1a M;if(!I.1q){}1b{if(C<(D.79||3)){G=I.6K();M=F(G.1o);if(M){E=1w B.g0(M,{67:L("67"),5c:L("5c"),9d:L("9d"),8k:L("8k")});E.g4=G}1b{(D.67||B.$F).2k(H,{1y:i(G.1o).2j(),2b:M},G);C--;K()}C++}}}17 J(M){1a N,O;N=(M&&M 4v jf);if(N){O=M.3S("2i-43")||1i;if(O){M.3v("43",O)}}18(N&&M.3S("43"))?M:1i}17 F(M){18 B.1P(G)=="2e"?M:(B.1P(M)=="8s"?J(M.2b):((M.2y=="A"||M.2y.34()=="4t")?J(i(M).7J("6b")[0]||M.3n):(M.2y=="6b"?J(M):1i)))}14.2c=17(M,N){I[N?"k3":"2c"](M);D.g5||K();18 14};14.9h=17(){E.g6();k4--};14.2O=K;D.g5||I.1q&&K()}})(p);1a l,i=q.$,y=i,j=i;q.4V={};l={1B:{3G:[{1H:"5e",7c:1},{1H:"2e","3B":["1Z"]}],"2E":"1Z"},1J:{3G:[{1H:"5e",7c:1},{1H:"2e","3B":["1Z"]}],"2E":"1Z"},1h:{3G:[{1H:"5e",7c:1},{1H:"40"},{1H:"2e","3B":["1Z","7v"]}],"2E":"1Z"},4D:{3G:[{1H:"65"},{1H:"2e","3B":["1Z"]}],"2E":"1Z"},2h:{3G:[{1H:"65"},{1H:"2e","3B":["fY","c7","9E"]}],"2E":"c7"},4u:{1H:"5e","2E":0},8P:{1H:"5e","2E":be},1n:{3G:[{1H:"2e","3B":["3z","6z","9E"]},{1H:"65","3B":[1c]}],"2E":"3z"},3R:{1H:"65","2E":1c},1S:{1H:"2e","3B":["3l","3t"],"2E":"3l"},3u:{3G:[{1H:"5e",7c:0},{1H:"2e","3B":["1Z"]}],"2E":"1Z"},2t:{1H:"2e","3B":["2o","2q","66","64-6h"],"2E":"2o"},cb:{1H:"65","2E":1c},8o:{1H:"2e","2E":"5E-5M(.8, 0, .5, 1)"},6t:{1H:"65","2E":1c},aE:{1H:"65","2E":1f},c2:{1H:"17","2E":q.$F},bZ:{1H:"17","2E":q.$F},bW:{1H:"17","2E":q.$F},eC:{1H:"17","2E":q.$F},bX:{1H:"17","2E":q.$F},aq:{1H:"17","2E":q.$F}};1k.6n("4t");1k.6n("33");1a m=17(B){18{1B:((1X(B.2n("6f-1N"))||0)+(1X(B.2n("6f-6q"))||0)),1J:((1X(B.2n("6f-1M"))||0)+(1X(B.2n("6f-5h"))||0))}},h=17(B){18{1B:((1X(B.2n("5w-1N"))||0)+(1X(B.2n("5w-6q"))||0)),1J:((1X(B.2n("5w-1M"))||0)+(1X(B.2n("5w-5h"))||0))}},o=17(B){18{1B:((1X(B.2n("5T-1N-1B"))||0)+(1X(B.2n("5T-6q-1B"))||0)),1J:((1X(B.2n("5T-1M-1B"))||0)+(1X(B.2n("5T-5h-1B"))||0))}},A=17(B){18{1B:i(B).2n("1B"),1J:i(B).2n("1J")}},r=q.1e.56,e=q.aD("ar").aP(),b=17(C,D){1a B=1c,E=0;q.$6T(14);14.8r={4I:1f,6p:"5E-5M(.8, 0, .5, 1)",1p:"2o",2s:1c,1K:1c,6W:1c,1S:"3l",3I:9q,1n:1f,3R:1f,3u:"1Z",6t:1c};14.o=14.8r;q.1T(14.o,D);14.1j=i(C).1L("kC-kD","kE");14.1n={2V:1c,2Q:1c};14.as();14.aL=i(17(H){1a G={},F=1f;if(37===H.bq||39===H.bq){G.1I=H.bq==39?"1F":"2w";if(!14.o.1n){if("1F"===G.1I){if(14.1n.2Q){F=1c}}1b{if(14.1n.2V){F=1c}}}F&&14.1s("fg",G)}}).1d(14);14.7e="2o";14.1h=i([]);14.4m=i([]);14.6U=i([]);14.5y=i([]);14.6Q=i([]);14.1m=0;14.3m=0;14.2v=14.o.3u;14.1C=0;14.l=1i;14.5n=1i;14.2l=1i;14.2C=0;14.7j=0;14.24=0;14.1I="1F";14.4A=q.$F;14.3y=0;14.71=1c;14.2R=1i;14.8E=0;14.8m=1i;14.cG=14.1m;14.73=1c;14.bj=1c;14.bs=1c;14.5L=1c;14.ah=1i;14.4b={};14.9R=0;14.8D={1I:"1F",76:1c};14.79=1w q.cf([],{79:1,5c:i(17(G,H){1a F=14.1h[H.1U];F.2O="7p";if(F.1K){F.1K.5k();F.1K=1i}F.1o.2X("21-fN");14.9O(i(17(J,I){if(J.1U==F.1U){J.2r=1f;if(J.1K){J.1K.5k();J.1K=1i}J.1o.2O="7p";J.1o.2X("21-fN")}}).1d(14));E++;if(14.o.3R){if(14.9D()){if(14.o.4I||!14.4b.5v){14.1s("aN");14.1s("bg")}if(!14.2L){14.7F()}!14.4b.5v&&14.1s("4f")}}1b{if(E==14.l&&!14.o.3R){14.5L=1f;!14.4b.5v&&14.1s("4f")}}14.bo()}).1d(14),67:(17(I,J){1a H=[],G=14.1h[J.1U],F;if(!G){18}G.1o.2r(G.2m);if(!14.o.3R&&!14.bs){3M{14.aw(G);14.9l(G)}3X(I){14.bs=1f}}14.bv(G,i(17(){1a K=1f;if(i(["2o","2q"]).3f(14.7e)){if(!14.4b.5v&&!14.o.3R){K=J.1U<14.3y}}14.bw(G,K,14.7S);G.2O="4F";E++;if(14.o.3R){if(14.9D()){if(14.o.4I||!14.4b.5v){14.1s("aN");14.1s("bg")}if(!14.4b.5v){14.1s("4f")}}}1b{if(E==14.l){14.5L=1f;!14.4b.5v&&14.1s("4f")}}14.bo()}).1d(14))}).1d(14)})};b.29={4z:b,7S:q.$F,9l:q.$F,aw:q.$F,bw:17(F,I,H){1a B,E,D,G=9q,C=F.2m;if(I){if(q.1e.1z&&q.1e.1z<10){B=i(C).2j();E={3j:[0,1],1M:[B.1J/2,0],1N:[B.1B/2,0],1B:[0,B.1B],1J:[0,B.1J]};D=1w q.4n(C,{3I:G,57:i(17(K,J){C.2W({6E:"",2D:"",1M:"",1N:"",1B:"",1J:""});if(F.1K){F.1K.5k();F.1K=1i}}).1d(14),8O:i(17(){C.2W({2D:"fS",6E:"6H"})}).1d(14)});D.2A(E)}1b{C.1L("2Y","5G");C.3V(0);C.6e;C.3q.6e;C.1G("6C",i(17(J){if(J.1Y==C){14.1V(J.1H);14.1L(e,"");14.1L("2Y","");if(F.1K){F.1K.5k();F.1K=1i}}}).1d(C));C.1L("2Y",e+" "+G+"4O 5E-5M(.5,.5,.69,1.9), 3j "+G+"4O 93");C.6e;C.3q.6e;C.3V(1);H&&H(F)}}1b{C.3V(1);if(F.1K){F.1K.5k();F.1K=1i}}F.5l.1q>0&&i(F.5l).1A(i(17(J){if(J){i(J.2m).3V(1);J.2O="4F";if(J.1K){J.1K.5k();J.1K=1i}}}).1d(14))},bo:17(){1a B=0;14.1h.1A(i(17(C){if(C.2O=="4F"||C.2O=="7p"){B++}if(14.l==B){14.5L=1f;14.1s("aN")}}).1d(14))},9D:17(){1a B=0,C=0;if(14.5L){18 1f}1r(;B<14.3y;B++){if(14.1h[14.3F(14.1m+B)].2O=="4F"||14.1h[14.3F(14.1m+B)].2O=="7p"){C+=1}}18 C==14.3y},7l:17(){18 14.1j.3q.2j()[14.1x.1y]},as:17(){1a B={3l:{1y:"1B",1W:"1N",fV:"1J"},3t:{1y:"1J",1W:"1M",fV:"1B"}};14.1x=B[14.o.1S];if(14.o.3u==0){14.o.3u="1Z"}if(!14.o.1n||"6z"===14.o.1n){14.1n.2V=1f}if(q.1e.1z&&q.1e.1z<10){14.1j.1L(14.1x.1W,0)}1b{14.1j.1L(e,"4w(0, 0, 0)")}},8S:17(){14.1j.6e},a6:17(){if(14.5L||14.bj){18}14.bj=1f;14.1s("aM");14.1h.1A(i(17(B){if(B.2O=="7K"){if(B.1K){B.1K.5k();B.1K=1i}B.5l.1q>0&&i(B.5l).1A(17(C){if(C.1K){C.1K.5k();C.1K=1i}});14.79.2c({1o:B.2m,1U:B.1U})}}).1d(14));14.5L=1f},83:17(C){1a D,F=14.1m,B=i([]),E,G;if(14.5L){18}if(14.o.3R){C&&(F=(C=="1F")?14.3F(F+14.3y):14.3F(F-14.3y));G=i(17(H){if(H.2O=="7K"){if(14.o.4I){!C&&14.1s("aM")}1b{H.1K&&H.1K.2M()}H.2O="2O";14.79.2c({1o:H.2m,1U:H.1U})}}).1d(14);1r(D=0;D<14.3y;D++){E=14.1h[14.3F(F+D)];G(E);if(!C){G(14.1h[14.3F(E.1U+14.3y)]);G(14.1h[14.3F(E.1U-14.3y)])}}}},cH:17(G){1a H,C,E,D,B=0,F=14.ah.1q;if(G=="2w"){B=F-1;F=-1}if(!14.5L){4R(B!=F){D=14.ah[B];H=D.5j();C=D.3S("2i-2Z");if(H[14.1x.1W]+14.1h[0].1y[14.1x.1y]>14.9R[14.1x.1W]&&H[14.1x.1W]<14.9R[14.1x.1W]+14.24){E=14.1h[C];if(E.2O=="7K"){E.2O="2O";E.1K&&E.1K.2M();i(E.5l).1A(i(17(I){I.1K&&I.1K.2M()}).1d(14));14.79.2c({1o:E.2m,1U:E.1U})}}G=="1F"?B++:B--}}},87:17(F){1a C,B,E,D;if(14.4b.81){18}14.4b.81=1f;B=14.l=14.1h.1q;14.24=14.7l();E=i(14.1j.3q).5j();1r(C=0;C<14.l;C++){D=14.1h[C];D.1y=D.1o.2j(1f);14.2C+=D.1y[14.1x.1y]}14.3P()},aZ:17(C){14.4b.5v=1f;14.a9();if(!q.1e.1z||q.1e.1z&&q.1e.1z>9){14.7f()}14.80();if((!q.1e.1z||q.1e.1z&&q.1e.1z>9)&&"2o"===14.o.1p&&14.o.4D){14.7d()}if(i(["2o","2q"]).3f(14.7e)){1r(1a B=0;B<14.1h.1q;B++){if(B>=14.3y){14.1h[B].1K&&14.1h[B].1K.2M()}}}14.1m=0;14.3m=14.4m.1q;i(1l).1G("5H",14.3P.1d(14));if(14.o.6t){i(1k).1G("8A",14.aL)}14.3P();C&&C()},80:17(){14.1h.1A(i(17(B){B.2m.f5=i(17(){14.1s("2M-14",{1U:B.1U})}).1d(14);B.2m.1G("4k",i(17(C){if(14.2L){C.27()}}).1d(14))}).1d(14))},a9:17(D){1a B,C=0;if(14.71){18}if(14.o.2s){14.2v=14.3y;18}1r(B=0;B<14.l;B++){C+=14.1h[B].1y[14.1x.1y];if(C>=14.24){if(14.2v=="1Z"||14.2v>=B){if(14.o.1p=="2q"&&C-14.1h[B].1y[14.1x.1y]+5<14.24||C==14.24){B+=1}14.2v=B;if(14.o.3u!="1Z"&&14.o.3u<14.2v){14.2v=14.o.3u}}1O}}!14.2v&&(14.2v=1)},cY:17(C){1a B=C.6i();4t=1k.6n("4t"),33=1k.6n("33");q.$A(C.3n.2g).1A(i(17(D){if(D.2y.34()=="33"){q.$A(D.2g).1A(i(17(E){i(33).2r(E.6i(1f))}).1d(14));q.$A(D.fU).1A(i(17(E){4t.3v(E,E.9L)}).1d(14));4t.2r(33)}1b{i(4t).2r(D.6i(1f))}}).1d(14));q.$A(C.3n.fU).1A(i(17(D){4t.3v(D,D.9L)}).1d(14));B.2r(4t);18 B},9O:17(B){if(14.4m.1q>0){i([14.4m,14.6U]).1A(i(17(C){C.1A(i(17(E,D){B(E,D)}).1d(14))}).1d(14))}},bv:17(C,D){if(14.4m.1q>0){1a B=i(17(){1a E;if(q.1e.1z&&q.1e.1z<9&&C.1o.3n.2y.34()=="4t"){E=14.cY(C.2m.6i(1f))}1b{E=C.2m.6i(1f)}E.2g&&q.$A(E.2g).1A(i(17(F){if(i(F).8B&&i(F).8B("4Q-1K-kF")){F.4B()}}).1d(14));18 E}).1d(14);14.9O(i(17(F,E){if(F.1U==C.1U&&!F.2r){F.2m=B();14.1h[C.1U].5l.2c(F);F.2r=1f;F.1o.2r(F.2m)}}).1d(14))}D&&D()},cZ:17(){1a B,C=0,F=0,H=0,E={1N:0,1M:0},G,D;if(14.71){18}1r(B=0;B<14.l;B++){C+=14.1h[B].1y[14.1x.1y];H++;if(14.24<=C){1O}}if(14.l>1&&(H>14.3y||14.4m.1q==0)){F=14.4m.1q;1r(B=F;B<H;B++){G={1o:14.1h[14.l-1-B].1o.6i(),2O:"7K",2r:1c};i(G.1o).3v("2i-2Z",14.l-1-B);G.1U=14.1h[14.l-1-B].1U;if(14.o.3R&&14.o.1K){G.1K=1w q.3Q.9i(G.1o);G.1K.2M()}14.4m.2c(G);D={1o:14.1h[B].1o.6i(),2O:"7K",2r:1c};i(D.1o).3v("2i-2Z",B);D.1U=14.1h[B].1U;if(14.o.3R&&14.o.1K){D.1K=1w q.3Q.9i(D.1o);D.1K.2M()}14.6U.2c(D);i([D.1o,G.1o]).1A(i(17(I){I.1G("4k",i(17(J){if(14.2L){J.27()}}).1d(14))}).1d(14));14.1j.2r(D.1o);14.1j.2r(G.1o,"1M");i([14.1h[14.l-1-B],14.1h[B]]).1A(i(17(I){if(I.2O=="4F"){14.bv(I,i(17(){1a J=1f;if(i(["2o","2q"]).3f(14.7e)){if(!14.4b.5v&&!14.o.3R){J=I.1U<14.3y}}14.bw(I,J);I.5l.1q>0&&i(I.5l).1A(17(K){if(K.1K){K.1K.5k();K.1K=1i}})}).1d(14))}}).1d(14))}if(F){14.3y+=H-F}1b{14.3y=H}}1b{14.3y=H}14.7j=14.1C=0;C=0;1r(B=0;B<14.4m.1q;B++){C+=14.1h[14.l-1-B].1y[14.1x.1y]}14.7j+=C;14.1C-=C;E[14.1x.1W]=14.1C;if(q.1e.1z&&q.1e.1z<10){14.1j.1L(14.1x.1W,E[14.1x.1W])}1b{14.cx()}},2c:17(B){14.l=14.1h.1q;B.1U=14.l;B.2O="7K";B.5l=[];if(14.o.1K&&14.o.3R){B.1K=1w q.3Q.9i(B.1o,1f);if(!14.o.4I){B.1K.2M()}}B.1o.3v("2i-2Z",B.1U);B.1o.1G("5o 6P",i(17(D){1a C=D.az();4R(C&&C!==B.1o){C=C.3q}if(C==B.1o){18}if("5o"===D.1H){14.1s("3N-2Z-f7",{av:B.1U})}1b{14.1s("3N-2Z-au",{av:B.1U})}}).1d(14));14.1h.2c(B)},3F:17(B){B%=14.l;B<0&&(B=B+14.l);18 B},2P:17(C,D){1a B;if(C=="1F"||C=="2w"){14.1I=C}if(14.2L||14.73){18}14.2L=1f;if(q.1P(C)=="8s"){14.1I=C.1I;C.76=1c;C.7W=1c}1b{if(/1F|2w|^\\+|^\\-/.3E(C)){if(/^\\+|^\\-/.3E(C)){B=/^\\+/.3E(C)?"1F":"2w";C={6A:1g.1E(1X(C)),1I:B};C.6A>14.l&&(C.6A=14.l);C.1Y=14.3F(C.1I=="1F"?(14.1m+C.6A):(14.1m-C.6A))}1b{C={1I:C};C.1Y=14.3F(C.1I=="1F"?(14.1m+14.2v):(14.1m-14.2v))}C.76=1c;C.7W=1f}1b{if(q.1P(1X(C))=="5e"){C={1Y:14.3F(C),76:1f,7W:1c}}}}C.4A=D;if(!14.o.1n){if(14.1n.2V||14.1n.2Q){if(14.1n.2V){if("2w"===C.1I){14.2L=1c;D(1i,1f);18}}1b{if("1F"===C.1I){14.2L=1c;D(1i,1f);18}}}}14["kB"+14.7e](C)},5P:17(E,C){1a D={1N:0,1M:0},F=1c,B=C||14.1C;if(E=="1F"){if(B+14.7j-14.2l+14.2C<0){14.1C=B+14.2C;D[14.1x.1W]=14.1C;F=1f}}1b{if(B+14.2l>0){14.1C=B-14.2C;D[14.1x.1W]=14.1C;F=1f}}if(F){if(q.1e.1z&&q.1e.1z<10){14.1j.1L(14.1x.1W,D[14.1x.1W]+"2G")}1b{14.1j.1L(e,"4w("+D.1N+"2G, "+D.1M+"2G, 0)");14.1j.1L("2Y",e+" b7 "+14.o.6p);14.8S();if(14.o.1p=="2q"){14.8X=14.3m=14.6F();if(E=="1F"){14.3m+=14.2v}1b{14.3m-=14.2v}}}}18 F},7U:17(E,D){1a C,B=1f;if(!D){if(14.o.3u=="1Z"){14.2v="1Z";14.a9(E=="2w")}B=1c;D=14.2v}1b{14.o.4I=1c}1r(C=D;C>0;C--){14.1m=14.3F((E=="1F")?(14.1m+1):(14.1m-1));14.3m=(E=="1F")?(14.3m+1):(14.3m-1);14.2l+=14.1h[(E=="1F")?14.3F(14.1m-1):14.1m].1y[14.1x.1y]}if("3z"===14.o.1n){if(!14.o.2s){14.1s("3N-2A-1p",{3Z:14.5b()})}}1b{if("2o"===14.o.1p&&14.1n.2Q&&E=="2w"){if(B){14.1m-=(14.7h-1)}1b{14.1m-=(D-1)}if(14.1m<0){14.1m=0}}14.1s("4i");if(14.1n.2Q&&E=="1F"){14.1n.2Q=1c;14.1n.2V=1f;14.1C=0;14.2l=0;14.1m=0;14.3m=0;14.1s("5d-3g");14.1s("3N-2A-1p",{3Z:14.5b()})}1b{if(14.1n.2V&&E=="2w"){14.1n.2V=1c;14.1n.2Q=1f;14.2l=0;14.1m=14.l-1;if(14.o.1p=="2o"){14.3m=14.l-14.7h;14.1C=(14.2C-14.24)*(-1)}1b{14.3m=14.l-14.l%14.7h;14.1C=(1g.8M(14.l/14.2v)-1)*14.24*(-1)}14.1s("1m-3g");14.1s("3N-2A-1p",{3Z:14.5b(1f)})}1b{14.1n.2Q=1c;14.1n.2V=1c;if(E=="1F"){if(14.1C-14.2l<=14.24-14.2C||14.1C-14.2l+1<=14.24-14.2C){14.1s("1m-3g");if(14.o.1p=="2o"||14.o.1p=="2q"&&"3z"===14.o.1n){14.2l=14.1C-(14.24-14.2C)}1b{14.2l=14.24}14.1n.2Q=1f;14.1m=14.l-1;14.1s("3N-2A-1p",{3Z:14.5b(1f)})}1b{14.1s("3N-2A-1p",{3Z:14.5b()})}}1b{if(14.1C+14.2l>=0||14.1C+14.2l===-1){14.1s("5d-3g");14.2l=1g.1E(14.1C);14.1n.2V=1f;14.3m=0;14.1m=0;14.1s("3N-2A-1p",{3Z:14.5b()})}1b{14.1s("3N-2A-1p",{3Z:14.5b()})}}}}}},eD:17(F){1a B,D,C=0,E;if(!F.1I){C=1g.5Y(14.3y/2);if(14.3y%2==0){C-=1}C<0&&(C=0)}if("3z"===14.o.1n){F.1Y=14.3F(F.1Y-C)}if(14.1m!=F.1Y){14.o.4I=1c;E=i(17(J){1a H=14.1m,I=0,G;do{I++;!J?H++:H--;G=14.3F(H)}4R(G!=F.1Y);18 I}).1d(14);if(!F.1I){if("3z"===14.o.1n){F.1I=E()<=E(1f)?"1F":"2w"}1b{F.1I=F.1Y>14.1m?"1F":"2w"}}14.1s("4i");if("3z"===14.o.1n){4R(14.1m!=F.1Y){14.1m=14.3F(F.1I=="1F"?++14.1m:--14.1m);14.3m=F.1I=="1F"?++14.3m:--14.3m;14.2l+=14.1h[14.1m].1y[14.1x.1y]}}1b{14.1n.2Q=1c;14.1n.2V=1c;14.1m=F.1Y;D=0;1r(B=0;B<F.1Y-C;B++){D+=14.1h[B].1y[14.1x.1y]}14.3m=F.1Y;14.1C=0-14.7j-D;if(14.o.1p=="2o"&&14.1C<=0-(14.2C-14.24)||14.1C<=0-((14.2C+(14.l%14.2v)*14.1h[0].1y[14.1x.1y])-14.24)){if(14.o.1p=="2o"){14.1C=0-(14.2C-14.24)}14.1n.2Q=1f;14.1s("1m-3g");14.1m=14.l-1}if(14.1C>=0){14.1C=0;14.1s("5d-3g");14.1n.2V=1f;14.1m=0}}}1b{14.2L=1c;14.73=1c;14.1s("fh")}},eU:17(E){1a B=14.1C,C=1c,D;14.8X=14.3m;14.2l=0;if((!14.o.1n||"6z"===14.o.1n)&&14.o.1p=="2q"){if(14.1n.2Q&&E.1I=="1F"||14.1n.2V&&E.1I=="2w"){C=1f}}if(E.7W){14.7U(E.1I,E.6A)}1b{14.eD(E)}if(C){E.1I=E.1I=="1F"?"2w":"1F"}if(0!==14.8E){D=14.1h[14.cG].1y[14.1x.1y]-14.8E;if(E.1I=="1F"){14.2l-=D}1b{14.2l+=D}14.8E=0}"3z"===14.o.1n&&14.5P(E.1I);if(E.1I=="1F"){14.1C-=14.2l}1b{14.1C+=14.2l}14.8D.1I=E.1I;14.8D.76=E.76;if(B!=14.1C){14.4A=E.4A;if(14.o.4I&&!14.5L&&!14.9D()){14.1s("aM");14.83();14.2K("bg",i(17(F){14.2L&&14.6N(1i,F.1I,F.76)}).1d(14,14.8D))}1b{if(!14.5L){14.83()}14.6N(1i,E.1I,E.76)}}1b{14.1s("eS")}},6N:17(C,B,E){1a D={1N:0,1M:0};14.2L=1f;if(q.1e.1z&&q.1e.1z<10){D={};D[14.1x.1W]=[1X(14.1j.2n(14.1x.1W)),14.1C];14.fx=1w q.4n(14.1j,{2Y:14.o.6p,3I:C||14.o.3I,57:14.75.1d(14),8O:i(17(){14.f9=1c}).1d(14)}).2A(D)}1b{D[14.1x.1W]=14.1C;if(14.o.1p=="2q"&&!E){14.eE(B,D)}1b{14.1j.1V("6C");14.1j.1G("6C",i(17(F){if(F.1Y==14.1j){14.1j.1V(F.1H);if(E){14.3m=14.6F();14.cJ()}14.75()}}).1d(14));14.1j.1L(e,"4w("+D.1N+"2G, "+D.1M+"2G, 0)");14.1j.1L("2Y",e+" "+(C||14.o.3I)+"4O "+14.o.6p)}}},eE:17(H,G){1a F,C,E,D=14.1j.2g,B=D.1q,I=i(17(J){J%=14.5n;J<0&&(J=J+14.5n);18 J}).1d(14);14.5y.1q=0;14.6Q.1q=0;1r(F=0;F<14.2v;F++){if("3z"===14.o.1n){C=I(14.8X+F)}1b{C=14.8X+F<B?14.8X+F:1i}C!=1i&&14.5y.2c(D[C]);if("3z"===14.o.1n){E=I(14.3m+F)}1b{E=14.3m+F<B?14.3m+F:1i}E!=1i&&14.6Q.2c(D[E])}if(H=="2w"){14.5y.4C();14.6Q.4C()}14.1j.3v("2i-"+H,"");14.5y.1A(i(17(K,J){K.1G(r+"8w 8G",i(17(L,M,N){if(L==14.5y[M]){L.1V(r+"8w 8G").3v("2i-cv","");if(M==14.5y.1q-1){14.5y.1A(i(17(P,O){P.3U("2i-2q-92");P.3U("2i-8U")}).1d(14));14.6Q.1A(i(17(P,O){if(O==14.6Q.1q-1){P.1G(r+"8w 8G",i(17(Q){if(Q.1Y==P){P.1V(r+"8w 8G");14.6Q.1A(i(17(R,S){R.3U("2i-2q-92");R.3U("2i-8U")}).1d(14));14.5y.1A(i(17(R,S){R.3U("2i-cv")}).1d(14));14.1j.3U("2i-"+H);14.8S();14.75()}}).1d(14))}P.3v("2i-cu","");P.1G(r+"8y 8v",i(17(Q){if(Q.1Y==14){14.1V(r+"8y 8v");P.3U("2i-cu")}}).1d(P));P.3v("2i-8U","kA");P.3v("2i-2q-92",(O+1))}).1d(14));14.1j.1L(e,"4w("+G.1N+"2G, "+G.1M+"2G, 0)")}}}).1d(14,K,J))}).1d(14));14.5y.1A(i(17(K,J){K.3v("2i-cB","");K.1G(r+"8y 8v",i(17(L){if(L.1Y==14){K.1V(r+"8y 8v");14.3U("2i-cB")}}).1d(K));K.3v("2i-8U","kw");K.3v("2i-2q-92",(J+1))}).1d(14))},5b:17(E){1a F=0,D=14.2v,B=[],C;if(E){if(14.o.1p=="2o"){F=14.l-14.2v}1b{F=14.l%14.2v?14.l-14.l%14.2v:14.l-14.2v}D=14.l}1r(;F<D;F++){if(!E){C=14.1m+F}1b{C=F}B.2c(14.3F(C))}18 B},75:17(){14.2L=1c;14.8Y=1c;14.4A&&14.4A(14.5b(14.1n.2Q))},cJ:17(){14.1j.1L("2Y",e+" b7")},a8:17(G){1a F={x:0,y:0},D=G.2n(e)||"",E=/3d/.3E(D)?(/kv\\(([^\\)]+)\\)/):(/kx\\(([^\\)]+)\\)/),C=/3d/.3E(D)?12:4,B=/3d/.3E(D)?13:5;(G.2n(e)||"").5F(E,17(J,I){1a H=I.8g(",");F.x+=1X(H[C],10);F.y+=1X(H[B])});18 F},6F:17(){1a E,D,B,C=eL,F=14.1j.3q.5j()[14.1x.1W];1r(E=0;E<14.5n;E++){D=14.1j.2g[E].5j()[14.1x.1W];if(C>1g.1E(F-D)){C=1g.1E(F-D);B=E}1b{1O}}18 B},7F:17(){if(14.4m.1q==0){18}1a C,B,D=i(17(F,G){1a H,E;if(14.1h[G].1o!=F&&14.1h[G].2O=="4F"){1r(E=0;E<14.5n;E++){if(14.1h[G].1o==14.1j.2g[E]){H=E;1O}}if(H<B){14.1j.7q(F,14.1j.2g[H]);if(B+1<=14.5n-1){14.1j.7q(14.1h[G].1o,14.1j.2g[B+1])}1b{14.1j.8N(14.1h[G].1o)}}1b{14.1j.7q(14.1h[G].1o,F);if(H+1<=14.5n-1){14.1j.7q(F,14.1j.2g[H+1])}1b{14.1j.8N(F)}}}}).1d(14);B=14.6F();1r(C=0;C<14.3y;C++){D(14.1j.2g[B],14.3F(14.1m+C));B++}},aT:17(J){1a H,F,G,M=0,C=0,L,I=14.1j.3q.5j()[14.1x.1W]+1,E=14.1j.5j()[14.1x.1W]-I,K=1g.1E(1g.1E(E)-1g.1E(14.1C)),D,B=i(17(N){18 1X(14.1j.2g[N].3S("2i-2Z"))}).1d(14);(K>0&&K<1)&&(K=0);if(J=="1F"){I+=K}1b{I-=K}1r(H=0;H<14.5n;H++){G=14.1j.2g[H].5j()[14.1x.1W];if(G==I){14.1m=B(H);18 0}L=1X(14.1j.2g[H].2j()[14.1x.1y]);if(G<I&&G+L>I){D=H;if(J=="1F"){D=H+1>14.5n-1?14.5n-1:H+1;H++}1r(F=0;F<H;F++){C+=14.1h[B(F)].1y[14.1x.1y]}M=1g.1E(1g.1E(14.1C)-C);14.1m=B(D);1O}}18 M},7f:17(){1a ab,G,Z,R,aa,F,B=(14.1x.1W=="1N")?"x":"y",H={x:0,y:0},P=14.o.1p=="2o",S,U=1f,K={x:0,y:0},D=1c,T=1c,I=1i,N=0,V=1i,O=1c,C=i(17(ae){1a ad,ac=0;if(ae>14.24){ae=14.24}1r(ad=1.5;ad<=90;ad+=1.5){ac+=(ae*1g.eB(ad/1g.3A/2))}18 14.24>ac?ac:14.24}).1d(14),E=i(17(ae){1a af,ac=0,ad,ag;4R(ac>14.1C){ac-=14.24}if(1g.1E(ac-14.1C)>14.24/2){ac+=14.24}ag=ac;1r(af=0;af<14.5n;af++){ad=1X(14.1j.2g[af].3S("2i-2Z"));if(ag==0){14.1m=ad;1O}ag+=14.1h[ad].1y[14.1x.1y]}18 ac}).1d(14),X=i(17(ac){T=1f;i(1k.4j).2X("21-7N");14.o.4I=1c;U=1f;3s(14.2R);if(14.o.1p=="2q"){14.cI()}14.8F&&14.8F();H={x:0,y:0};B=(14.1x.1W=="1N")?"x":"y";14.1s("5m-2A");14.1j.1V("6C");14.1C=14.a8(14.1j)[B];H[B]=14.1C;14.1j.1L(e,"4w("+H.x+"2G, "+H.y+"2G, 0)");14.1j.1L("2Y","5G");14.8S();14.o.1p=="2o"&&(P=1f);14.2L=1f}).1d(14),Q=i(17(ad){i(1k.4j).4L("21-7N");if(T){T=1c;1a ac=14.1C;if(!U){ad.et=1c;M();G=ad.36-ab;if(14.o.1p=="2o"){if(G>5B){F=aa;P=1c}1b{F=C(1g.1E(K[B]-ad[B]))}aa=F;if("3z"===14.o.1n){14.2l=1g.1E(aa);14.5P(Z)}if("3z"===14.o.1n||14.1C<=0){if(1g.1E(14.1C)<aa){aa=1g.1E(14.1C)}14.1C-=aa}Z=="1F"?14.1C-=14.aT(Z):14.1C+=14.aT(Z);if(!14.o.1n||"6z"===14.o.1n){14.1s("4i");14.1n.2V=1c;14.1n.2Q=1c;if(14.1C>0){14.1C=0;14.1m=0;P=1f;14.1s("5d-3g");14.1n.2V=1f}if(14.1C<14.24-14.2C){14.1C=14.24-14.2C;14.1m=14.l-1;P=1f;14.1s("1m-3g");14.1n.2Q=1f}}S=P?be:8u}1b{P=1f;14.2l=0;14.1C=E();"3z"===14.o.1n&&14.5P(Z);if(G<5B){14.2l=14.24;"3z"===14.o.1n&&14.5P(Z);if(Z=="1F"){14.1C-=14.24}1b{14.1C+=14.24}}if(!14.o.1n||"6z"===14.o.1n){14.1s("4i");14.1n.2V=1c;14.1n.2Q=1c;if(14.1C>=0){14.1C=0;14.1m=0;14.1n.2V=1f;14.1s("5d-3g")}if(14.1C<=(1g.8M(14.l/14.2v)-1)*14.24*(-1)){14.1C=(1g.8M(14.l/14.2v)-1)*14.24*(-1);14.1m=14.l-1;14.1n.2Q=1f;14.1s("1m-3g")}}S=9q}H[B]=14.1C;14.1j.1G("6C",i(17(ae){if(ae.1Y==14.1j){if(14.o.1p=="2q"){14.1j.1L("2Y","5G");14.3m=14.6F()}if(14.o.1p=="2q"){14.1m=1X(14.1j.2g[14.6F()].3S("2i-2Z"))}if("3z"===14.o.1n){14.7F()}14.2L=1c;14.73=1c;P=1c;U=1f;14.83();14.1s("5m-5S",{3Z:14.5b(14.1n.2Q)})}}).1d(14));if(ac==14.1C){14.2L=1c;P=1c;U=1f}14.1j.1L("2Y",e+" "+S+"4O 5E-5M(.22,.63,.49,.8)");14.1j.1L(e,"4w("+H.x+"2G, "+H.y+"2G, 0)")}1b{14.2L=1c}}}).1d(14),J=0,M=i(17(){3s(V);V=1i;O=1c;J=0}).1d(14),Y=i(17(){1a ac=J*0.2;if(1g.1E(ac)<0.ky){M();18}J-=ac;14.1C-=ac;H[B]=14.1C;14.1j.1L(e,"4w("+H.x+"2G, "+H.y+"2G, 0)");V=4P(Y,16)}).1d(14),W=i(17(ad){if(T){1a ac=ad[B]-N>0?"2w":"1F";U=1c;if("3z"===14.o.1n){14.2l=1g.1E(aa);14.5P(ac)}if(q.1e.1z){J+=aa;if(!O){O=1f;Y()}}1b{14.1j.1L("2Y",e+" b7");if(14.o.1p=="2q"){}14.1C-=aa;H[B]=14.1C;14.1j.1L(e,"4w("+H.x+"2G, "+H.y+"2G, 0)")}14.cH(ac)}}).1d(14),L=i(17(ac){if(14.71||14.o.1p=="2q"&&P){18}if("4c"==ac.4T){ab=ac.36;K.x=ac.x;K.y=ac.y;N=ac[B]}1b{Z=(aa>0)?"1F":"2w";aa=N-ac[B];14.8D.1I=Z;if("9a"==ac.4T){if(D){D=1c;Q(ac)}}1b{if(14.o.1S=="3t"||1g.1E(ac.x-K.x)>1g.1E(ac.y-K.y)){ac.4l();if(!D){if(14.o.1p=="2q"&&14.2L){18}D=1f;X(ac)}1b{W(ac)}}}}N=ac[B]}).1d(14);if(!q.1e.1z||q.1e.1z&&q.1e.1z>9){14.1j.3q.1G("2H 2u",L)}},7d:17(){1a F,G,C=0,E={x:0,y:0},D=(14.1x.1W=="1N")?"x":"y",B=i(17(I){1a H=C*(I||0.2);F=H>0?"1F":"2w";C-=H;if(1g.1E(H)<0.aY){3s(14.2R);14.1m=1X(14.1j.2g[14.6F()].3S("2i-2Z"));14.7F();14.8E=14.cX();14.cG=14.1m;C=0;14.2l=0;14.2R=1i;14.73=1c;14.2L=1c;14.1s("5m-5S",{3Z:14.5b(14.1n.2Q)});18}14.2l=1g.1E(H);"3z"===14.o.1n&&14.5P(F);14.1C-=H;14.2l=0;14.cH(F);if(!14.o.1n||"6z"===14.o.1n){if(14.1C>0){14.1C=0;C=0.aY;14.1s("5d-3g")}1b{if(14.1C<14.24-14.2C){14.1C=14.24-14.2C;C=0.aY;14.1s("1m-3g")}1b{14.1s("4i")}}}E[D]=14.1C;14.1j.1L(e,"4w("+E.x+"2G, "+E.y+"2G, 0)");14.2R=4P(B.1d(14,I),30)}).1d(14);if(q.1e.1z&&q.1e.1z<10||14.71){18}14.8F=i(17(){if(14.73){3s(14.2R);C=0;14.2l=0;14.2R=1i;14.73=1c;14.2L=1c}}).1d(14);14.1j.1G("4r",i(17(H){1a I=(1g.1E(H.3J)<1g.1E(H.3O)?H.3O:H.3J*(!H.4M?-1:-30));if(14.2L){18}if((1f===14.o.4D&&H.4M)||"3t"===14.o.1S&&1g.1E(H.3J)>1g.1E(H.3O)||"3l"===14.o.1S&&1g.1E(H.3J)<1g.1E(H.3O)){H.27();14.73=1f;if(0===C){14.1j.1L("2Y",e+" b7");E={x:0,y:0};D=(14.1x.1W=="1N")?"x":"y"}14.1s("5m-2A");C+=I;if(!14.2R){B(0.4)}}}).1d(14))},cX:17(){1a C,B,D=14.1C,E=i(["8m","1h","6U"]);14.8m=[];14.4m.1A(i(17(F){14.8m.2c(F)}).1d(14));14.8m.4C();1r(C=0;C<E.1q;C++){1r(B=0;B<14[E[C]].1q;B++){D+=14.1h[14[E[C]][B].1U].1y[14.1x.1y];if(D>0){14.1m=14[E[C]][B].1U;14.8m=1i;18 D}}}},5a:17(){1a B,C;if(!14.o.2s||14.8Y||!14.2L||14.o.1p=="2q"){18}14.8Y=1f;if(q.1e.1z&&q.1e.1z<10){14.fx&&(14.fx.1t.57=q.$F);14.fx&&14.fx.27();14.fx=1i;14.1C=1g.5Q(1X(14.1j.2n(14.1x.1W)))}1b{14.1C=14.a8(14.1j)[(14.1x.1W=="1N")?"x":"y"]}B=14.aT(14.1I);C=14.o.3I/14.2l*B;if(14.1I=="1F"){14.1C-=B}1b{14.1C+=B}14.6N(C)},27:17(){14.f9=1f;14.2L=1c;14.8F&&14.8F();if(14.o.1p=="2q"){14.cI()}if(q.1e.1z&&q.1e.1z<10){14.fx&&14.fx.27(1f);14.fx=1i}1b{14.cJ()}},cI:17(){1a B={x:0,y:0};if(!q.1e.1z||q.1e.1z&&q.1e.1z>10){B[14.1x.1W]=14.1C;14.1j.3U("2i-1F");14.1j.3U("2i-2w");i([14.5y,14.6Q]).1A(i(17(C,D){if(C.1q>0){C.1A(i(17(F,E){F.1V(r+"8y 8v "+r+"8w 8G");F.3U("2i-2q-92");F.3U("2i-8U");if(!D){F.3U("2i-cB");F.3U("2i-cv")}1b{F.3U("2i-cu")}}).1d(14))}}).1d(14));14.1j.1L(e,"4w("+B.1N+"2G, "+B.1M+"2G, 0)");14.2L=1c;14.8S()}},3P:17(){1a B,C;14.27();14.8Y=1c;14.9R=i(14.1j.3q).5j();14.24=14.7l();14.7h=0;14.2C=0;1r(B=0;B<14.l;B++){14.1h[B].1y=14.1h[B].1o.2j(1f);14.2C+=14.1h[B].1y[14.1x.1y];if(14.2C<=14.24){14.7h+=1}}if(q.1e.1z&&q.1e.1z<10){14.1m=0}1b{14.cx()}14.2l=0;14.2v=14.o.3u;if(14.2C<=14.24){14.71=1f;14.1s("eZ");14.1s("7u");14.7j=0;14.1C=0;if(q.1e.1z&&q.1e.1z<10){14.1j.1L(14.1x.1W,0)}1b{14.1j.1L(e,"4w(ct, ct, 0)")}14.9P()}1b{14.71=1c;14.1s("f1");14.1s("4i");if(!14.o.1n||"6z"===14.o.1n){if(14.1n.2V){14.1s("5d-3g")}if(14.1n.2Q){14.1s("1m-3g")}}}if((14.2C>14.24)&&("3z"===14.o.1n||14.o.2s)){14.cZ()}1b{14.3y=C=0;1r(B=0;B<14.l;B++){C+=14.1h[B].1y[14.1x.1y];14.3y++;if(14.24<=C){1O}}}14.5P("1F");14.1j.1V("6C");14.3m=14.6F();14.5n=14.1j.2g.1q;14.a9();14.7F();14.ah=q.$A(14.1j.2g);14.o.3R?14.83():14.a6()},cx:17(){1a D,F,E={1N:0,1M:0},C=14.1h[14.1m].1o.5j()[14.1x.1W],B=14.1j.3q.5j()[14.1x.1W];if(q.1e.1z&&q.1e.1z<10){}1b{if(!14.o.1n&&14.1n.2Q){if("2o"===14.o.1p){E[14.1x.1W]=14.24-14.2C}1b{F=14.7h-14.l%14.7h;E[14.1x.1W]=14.24-(14.2C+14.1h[0].1y[14.1x.1y]*F)}}1b{D=14.a8(14.1j)["1N"===14.1x.1W?"x":"y"];E[14.1x.1W]=D-(C-B)}14.1C=E[14.1x.1W];14.1j.1L(e,"4w("+E.1N+"2G, "+E.1M+"2G, 0)")}},fd:17(){1a H=0,G=1f,C=14.l-1,D=i(["6U","1h","4m"]),F=i(17(L,J){1a I,K=1i;1r(I=0;I<L.1q;I++){if(L[I].1U==J){K=L[I].1o;1O}}18 K}).1d(14),E=i(17(I){18(H==0)?I-1:(H-1)}).1d(14),B=i(17(L,J){1a K,I=L.1q;if(I>0){1r(K=0;K<I;K++){if(G){G=1c;H=I-1;14.1j.8N(L[H].1o)}1b{14.1j.7q(F(L,!H?C:E(I)),F(!H?14[D[J-1]]:L,H));H=!H?C:H-1}}}}).1d(14);D.1A(i(17(I,J){B(14[I],J);H=0}).1d(14));14.1m=0},9P:17(){14.4m.1A(17(B){B.1o.4B()});14.4m=i([]);14.6U.1A(17(B){B.1o.4B()});14.6U=i([])},6L:17(B){1a C={1N:0,1M:0};14.27();14.1C=14.1m=0;if(q.1e.1z&&q.1e.1z<10){14.1j.2W(C)}1b{14.1j.1L(e,"4w("+C.1N+"2G, "+C.1M+"2G, 0)")}14.2v=14.o.3u;if((!14.o.2s&&(!14.o.1n||"6z"===14.o.1n))&&14.4m.1q>0){14.7j=0;14.9P()}14.3P();14.fd();14.2L=1c},eN:17(B){1r(1a C in B){14.o[C]=B[C]}14.as()},7x:17(){14.27();14.9P();i(1l).1V("5H");i(1k).1V("8A");14.1j.1V("2u 2H");14.1h.1A(i(17(B){B.1o.1V("5o 6P");5t B.2m.f5}).1d(14))}};q.1T(b.29,q.6O);q.4V.cM=b;1a u=17(B,C){q.4V.cM.5u(14,3h);14.8r={5U:"1Z",7r:i([0.44,0.59,0.35,0.89]),9G:kz,6p:"5E-5M(.8, 0, .5, 1)"};14.7e="66";14.o=14.8r;q.1T(14.o,C);14.2l=70;14.6v=0;14.2F=0;14.2R=1i;14.b4=1g.4K(10,8);14.2S=2*1g.3A;14.1m=0;14.kG=i([]);14.b2=1i;14.5p=1i;14.6d=1i;14.5U=0;14.l=0;14.6X=1c;14.cU=1i};q.c0(u,q.4V.cM);q.1T(u.29,{4z:u,cZ:q.$F,7F:q.$F,5b:q.$F,eU:q.$F,5a:q.$F,cg:q.$F,9O:q.$F,cY:q.$F,83:q.$F,cX:q.$F,7r:q.1T({},q.4n.29),5P:17(){14.6v%=14.2S;14.2F=14.6v},87:17(D){1a C,B;if(14.4b.81){18}14.4b.81=1f;B=14.l=14.1h.1q;14.24=14.7l();if(q.1e.1z&&q.1e.1z<10&&14.1h[0].2m.1q&&14.1h[0].2m.aS.2y.34()=="33"){14.cU=1X(14.1h[0].2m.aS.2n("kH-1y"))}14.7r.5I=14.o.7r;1r(C=0;C<14.l;C++){14.1h[C].1y=14.1h[C].1o.2j(1f);14.2C+=14.1h[C].1y[14.1x.1y];14.1h[C].1o.1L("2D","7o");14.1h[C].2b=14.bV(14.1h[C])}if("3z"===14.o.1n){14.1s("4i")}if(14.1h[0].33&&!14.6X){if(14.1h[0].2m.2y.34()!="4t"){14.6X=1f}}14.3P();14.a6()},aZ:17(B){14.4b.5v=1f;14.80();14.5p=14.2S/14.l;14.6d=(14.2S-14.5p)*(-1);14.2v=1;14.7f();14.o.4D&&14.7d();i(1l).1G("5H",14.3P.1d(14));if(14.o.6t){i(1k).1G("8A",14.aL)}B&&B();14.3P()},80:17(){u.$4a.80.5u(14);14.1h.1A(i(17(B){B.1o.1G("4k",i(17(C){14.1s("2Z-4k",{1U:B.1U})}).1d(14))}).1d(14))},7S:17(C){1a B=cS;if(q.1e.1z&&q.1e.1z<10||!C.4X){18}C.4X.3V(1);C.4X.1L("2Y","3j "+B+"4O")},9l:17(D){1a B,C,E=i(17(F){if(F.4X||14.6X){B=F.2b.2j();C=F.2b.cO+B.1J;if(F.4X){F.4X.2W({1M:C,1N:F.2b.aV,1B:B.1B})}if(14.6X&&F.33){F.33.2W({1M:C,1N:F.2b.aV,1B:B.1B})}}}).1d(14);D?E(D):14.1h.1A(i(17(F){E(F)}).1d(14))},bV:17(D){1a B,C=D.2m;if(C.2y=="6b"){B=C}1b{if(C.3n.2y=="6b"){B=C.3n}1b{if(C.3n.2y=="cq"&&C.3n.3n.2y=="6b"){B=C.3n.3n}1b{B=1i}}}if(B){i(B).1L("z-1U",2z)}18 B},aw:17(O){if(14.o.1S=="3t"){18}1a D=q.$1w("4X",{},{3j:0}),P=q.$1w("4X"),C,B,G,M,L,N,Q=1,J,K,H,E,I,F;if(q.1e.1z&&q.1e.1z<10){18}if(D.d4){C=D.d4("2d");B=P.d4("2d");if(!O.2b){18}L=i(O.2b).2j();N=L.1J/2z*30;P.1B=L.1B;P.1J=L.1J;B.em();B.7m(1,-1);B.kO(O.2b,0,L.1J*(-1),L.1B,L.1J);G=B.kP(0,0,L.1B,N);B.fc();D.1B=L.1B;D.1J=N;C.em();K=G.2i;F=K.1q;E=F/4/L.1B;H=14.o.9G;J=F/E;1r(I=3;I<F;I+=4){if(I>J){J+=(F/E);Q++;H=1g.5Q(14.o.9G-14.o.9G*14.7r.9H(1/(E/Q)))}K[I]=H}C.kQ(G,0,0);C.fc();O.4X=D;if((!O.2m.2g||O.2m.2g.1q<2)&&O.2m.2y.34()!=="a"){O.1o.8N(D)}1b{O.2m.7q(D,O.2m.2g[1])}D.2X("21-kR")}},c1:17(C){1a D=0,B=14.2l/(14.l/2),E=2z-B;if(C>E){D=(C-E)/B}18 D},5Z:17(I){1a F={1N:0,1M:0},D={1N:0,1M:0},P={1N:0,1M:0},J,O,N=14.l,K=14.2l,B=14.2S/N,M,E,C,H,G,L;F[14.1x.1W]=14.5U;q.3c(I)||(I=0);14.6v=I;1r(G=0;G<N;G++){E=C=G*B+I;C%=14.2S;E%=14.2S;if(C!=0&&C!=1g.3A){if(1g.8M(1g.1E(C)/1g.3A)%2==0){if(1g.1E(C)%1g.3A!=0){E=1g.3A-(1g.1E(C)%1g.3A)}}1b{E=1g.1E(C)}}E=1g.1E(E*2z/1g.3A);if(14.1h[G].33){14.1h[G].33.3V(14.c1(2z-(E*K/2z)))}E=2z-1g.5Q(E*K/2z);!14.b2&&(14.b2=14.1h[G].1y);J=1g.1E(C);if(J>1g.3A/2&&J<1g.3A+1g.3A/2){if(J>1g.3A){J=1g.3A/2-1g.1E(J-1g.3A)}1b{J=J-1g.3A/2}J=(1-1g.6a(J))*0.7}1b{J=1}if(q.1e.1z&&q.1e.1z<10){H={1B:14.cR("1B",E),1J:14.cR("1J",E)};14.1h[G].1o.2W(H);14.1h[G].1o.2W({1M:1g.6a(C)*F.1M+1X(14.8Z.1J)/2-1X(H.1J)/2,1N:1g.6a(C)*F.1N+1X(14.8Z.1B)/2-1X(H.1B)/2});if(14.1h[G].2m.1q&&14.1h[G].2m.aS.2y.34()=="33"){14.1h[G].2m.aS.3e.kN=14.f8(E/2z*E)}if(14.6X){M=14.1h[G].2b.2j();14.1h[G].33.2W({1M:14.1h[G].2b.cO+M.1J,1N:14.1h[G].2b.aV,1B:M.1B})}}1b{P[14.1x.1W]=6c/14.2S*C;14.o.1S=="3t"&&(P[14.1x.1W]*=(-1));L=1g.1E(C);O=1g.7Q(1-1g.6a(L)*1g.6a(L));if(L>1g.3A/2&&L<1g.3A+1g.3A/2){L=14.5U*(O)+14.5U}1b{L=14.5U*(1-O)}L>0&&(L*=(-1));D[14.1x.1W]=(1g.6a(C)*F[14.1x.1W]+1X(14.8Z[14.1x.1y])/2-14.1h[G].1y[14.1x.1y]/2);14.1h[G].1o.1L(e,"kM("+D.1N+"2G)kI("+D.1M+"2G)9X("+L+"2G)eO("+P.1M+"a5)eI("+P.1N+"a5)")}14.1h[G].1o.1L("z-1U",0+E);14.1h[G].1o.3V(J)}},5Q:17(B,C){1a D=1g.4K(10,C||15);18 1g.5Q(B*D)/D},7U:17(E){1a C,D,B=6c/14.l;if(E.7W){if(E.6A){if(E.1I=="1F"&&14.1m>E.1Y){C=14.l-14.1m;C+=E.1Y}1b{if(E.1I=="2w"&&14.1m<E.1Y){C=14.l-E.1Y;C+=14.1m}}!C&&(C=1g.1E(14.1m-E.1Y));14.1m=E.1Y}1b{C=14.2v;14.1m=14.3F(E.1I=="1F"?14.1m+C:14.1m-C)}}1b{D=(6c-14.1m*B+E.1Y*B)%6c;if(D>=0&&D<=bU){!E.1I&&(E.1I="1F")}1b{if(D>=bU&&D<=6c){!E.1I&&(E.1I="2w")}}if(E.1I=="1F"){C=1g.5Q(D/B)}1b{C=1g.5Q((6c-D)/B)}14.1m=E.1Y}18 q.1T(E,{5p:C*14.5p})},eu:17(C){1a B;C=14.7U(C);B=C.5p;if(!14.o.1n){14.1s("4i")}if(C.1I=="1F"){14.2F-=B;if(!14.o.1n){if(14.2F==14.6d){14.1s("1m-3g")}1b{if(14.2F<14.6d){14.1m=0;14.2F=0;14.1s("5d-3g")}}}}1b{14.2F+=B;if(!14.o.1n){if(14.2F==0){14.1s("5d-3g")}1b{if(14.2F>0){14.1m=14.l-1;14.2F=14.6d;14.1s("1m-3g")}}}}14.1s("3N-2A-1p",{3Z:[14.1m]});14.4A=C.4A;14.6N(14.2F)},cR:17(B,C){18 14.b2[B]/2z*C},f8:17(B){18 1g.5Q(14.cU/2z*B)+"2G"},6N:17(B){14.fx=1w q.4n(14.1j,{3I:14.o.3I,2Y:14.o.6p,aB:(17(C){14.5Z(C.5p/14.b4)}).1d(14),57:i(17(){14.75()}).1d(14)}).2A({5p:[14.b4*14.6v,14.b4*B]})},75:17(){14.5P();u.$4a.75.5u(14)},6G:17(C){1a B=1g.1E(14.2F-14.6v)*(C||0.2);if(1g.1E(B)<0.aY){3s(14.2R);14.2R=1i;14.2L=1c;14.1s("5m-5S",{3Z:[14.1m]});18}if(14.2F<14.6v){B*=(-1)}14.5Z(14.6v+B);14.2R=4P(14.6G.1d(14,C),30)},7L:17(){1a D,E=14.2F%14.2S,C=1X(1g.1E(14.2F/14.2S)),G,F,B=i(17(H){4R(C!=0){C--;if(E<=0){H-=14.2S}1b{H+=14.2S}}18 H}).1d(14);1r(D=0;D<14.l;D++){G=(D*14.2S)/14.l;F=((D+1)*14.2S)/14.l;if(E<=0){G*=(-1);F*=(-1)}1b{G=14.2S-G;F=14.2S-F}if(G!=E){if(G>E&&E>F){if(1g.1E(E-G)<=1g.1E(F-E)){14.2F=B(G);14.1m=D}1b{14.2F=B(F);14.1m=14.3F(D+1)}}}1b{14.1m=D}}},7d:17(){1a D,C,B=14.2S/6c*15;14.1j.1G("4r",i(17(E){if(1f===14.o.4D||E.4M||"3t"===14.o.1S&&1g.1E(E.3J)>1g.1E(E.3O)||"3l"===14.o.1S&&1g.1E(E.3J)<1g.1E(E.3O)){14.1s("5m-2A");14.fx&&14.fx.27(1f);14.fx=1i;E.27();if(q.1e.1z&&q.1e.1z<10){E.4M=1f}C=1g.1E(E.3J)<1g.1E(E.3O)?E.3O:-1*E.3J;C=E.4M?(C*B):(C*(8/kJ));!E.4M&&(C=C>0?1g.9M(14.5p/4,C):1g.6S(14.5p/4*(-1),C));14.2F-=C;3s(D);D=4P(i(17(){14.7L()}).1d(14),2z);if(!14.o.1n){if(14.2F>=0){14.1s("5d-3g");14.2F=0;14.1m=0}1b{if(14.2F<=14.6d){14.1s("1m-3g");14.2F=14.6d;14.1m=14.l-1}}}if(!14.2R){14.6G(0.fF)}}}).1d(14))},7f:17(){1a G=(14.1x.1W=="1N")?"x":"y",I={x:0,y:0},H={x:0,y:0},F,C=1c,E="1F",B=1c,D=i(17(J){if("4c"==J.4T){i(1k.4j).2X("21-7N");B=1f;I.x=H.x=J.x;I.y=H.y=J.y}1b{if(B){I.x=J.x;I.y=J.y;if("9a"==J.4T){i(1k.4j).4L("21-7N");B=1c;if(C){C=1c;14.7L()}}1b{if(14.o.1S=="3t"||1g.1E(J.x-H.x)>1g.1E(J.y-H.y)){J.4l();if(!C){C=1f;14.2L=1f;14.fx&&14.fx.27();14.1s("5m-2A");3s(14.2R);14.2R=1i}E=H[G]<I[G]?"2w":"1F";F=1g.1E(H[G]-I[G])/14.5U;if(E=="1F"){14.2F-=F;if(!14.o.1n){if(14.2F<=14.6d){14.1s("1m-3g");14.2F=14.6d;14.1m=14.l-1}}}1b{14.2F+=F;if(!14.o.1n){if(14.2F>=0){14.1s("5d-3g");14.2F=0;14.1m=0}}}!14.2R&&14.6G()}H.x=I.x;H.y=I.y}}}}).1d(14);14.1j.1G("2u 2H",D)},27:17(){14.fx&&14.fx.27(1f);14.fx=1i;3s(14.2R);14.2R=1i;14.2F&&14.5Z(14.2F)},3P:17(){1a B,C;14.27();14.24=14.7l();14.8Z=14.1j.3q.2j();14.2C=0;1r(B=0;B<14.l;B++){14.1h[B].1y=14.1h[B].1o.2j(1f);14.2C+=14.1h[B].1y[14.1x.1y]}14.5p=1*14.2S/14.l;14.6d=(14.2S-14.5p)*(-1);C=14.2C/14.2S;14.5U=14.8Z[14.1x.1y]/2;(14.5U<C)&&(14.5U=C);(q.1e.1z&&q.1e.1z<10)&&(14.5U-=(14.1h[0].1y[14.1x.1y]/2));14.6v=14.2F=14.1m=0;14.5Z();14.9l()},6L:17(B){14.27();14.1m=0;if(14.o.1S=="3t"){14.bY()}1b{14.1h.1A(i(17(C){if(!C.4X){14.aw(C)}}).1d(14))}14.1j.1V("2u 2H 4r");14.7f();14.o.4D&&14.7d();14.cg();14.as();14.3P();if(14.o.1S=="3l"){14.1h.1A(i(17(C){14.7S(C)}).1d(14))}14.2L=1c},bY:17(){14.1h.1A(i(17(B){if(B.4X){B.4X.2J();5t B.4X}}).1d(14))},7x:17(){u.$4a.7x.5u(14);14.1j.1V("4r");14.bY();14.1h.1A(i(17(B){B.1o.1V("4k")}).1d(14))}});q.1T(u.29,q.6O);q.4V.c3=u;1a c=17(B,C){q.4V.c3.5u(14,3h);14.7e="kK";14.2x=1i;14.2l=1i;14.2T=1i;14.5i=1i;14.4p=1i;14.bN=kL;14.2v=1;14.2R=1i;14.3L=1i;14.2N=1i;14.3o=1i};q.c0(c,q.4V.c3);q.1T(c.29,{4z:c,5P:q.$F,eu:q.$F,c1:q.$F,ft:17(){1a B,D,C;14.3o=14.2T;if(14.o.1S=="3t"){C=14.2T+14.2T*0.8;14.3o/=2}1b{C=14.2T*2}1r(B=0;B<14.l;B++){D=(B==1)?C:14.3o;14.1h[B].2D=!B?(14.2x-14.2T):(14.1h[B-1].2D+D)}},bS:17(B){if(14.o.1S=="3l"){18 1g.5Q(14.2C-1g.1E(14.2x-(B.2D+14.2T)))}},87:17(D){1a C,B;if(14.81){18}14.81=1f;B=14.l=14.1h.1q;14.24=14.7l();14.7r.5I=14.o.7r;1r(C=0;C<14.l;C++){14.1h[C].1y=14.1h[C].1o.2j(1f);14.2C+=14.1h[C].1y[14.1x.1y];14.1h[C].1o.1L("2D","7o");14.1h[C].2b=14.bV(14.1h[C]);14.1h[C].33&&i(14.1h[C].33).3V(0)}14.o.1n=1c;if(14.1h[0].33&&!14.6X){if(14.1h[0].2m.2y.34()!="4t"){14.6X=1f}}14.3P();14.a6()},aZ:17(B){14.4b.5v=1f;14.80();14.2v=1;14.7f();14.o.4D&&14.7d();i(1l).1G("5H",14.3P.1d(14));if(14.o.6t){i(1k).1G("8A",14.aL)}B&&B();14.3P()},bM:17(J){1a H,C,I,G,D=1,B,E=J.2D+14.2T,F=J.2D+14.2T<=14.2x;G=F?(14.2x-E):(E-14.2x);G/=((F?(14.2x-14.3L):(14.2N-14.2x))/2z);C=(90/2z*G)*(1g.3A/bU);H=60*1g.6a(C);B=1-1*1g.6a(C);if(14.o.1S=="3l"){!F&&(H*=(-1))}1b{H*=(-1);F&&(D=1-0.7*1g.6a(C))}I=14.bN*1g.6a(C)*(-1);18{8C:H,9X:I,3j:D,bT:B}},ep:17(F,H){1a D,C=1c,G=1c,B=F.2D+14.2T,I,E={8C:60,9X:14.bN*(-1),3j:1};I=B-H;if(B>=14.2N){if(B-H<14.2N){D=B-14.2N;G=1f;H-=D;if(H<=14.2T){H=(14.2N-14.2x)/14.3o*H}1b{if(H<=14.2T*2){H=(14.2N-14.3L)/(14.3o*2)*H}1b{H+=(14.2T*2);G=1c}}F.2D-=D}C=1f;F.2D-=H}1b{if(B<=14.3L){if(14.o.1S=="3t"){H=(14.2N-14.2x)/14.3o*H}1b{if(B-H>14.3L){G=1f;D=14.3L-B;H+=D;if(H>=14.2T*(-1)){H=(14.2N-14.2x)/14.3o*H}1b{if(H>=14.2T*2*(-1)){H=(14.2N-14.3L)/(14.3o*2)*H}1b{H-=(14.2T*2)}}F.2D+=D}}C=1f;F.2D-=H}1b{if(B>14.3L&&B<14.2N){H=(14.2N-14.2x)/14.3o*H;if(B-H>=14.2N){D=14.2N-B;H+=D;H=14.3o/((14.2N-14.2x)/H);F.2D+=D}1b{if(B-H<=14.3L){if(14.o.1S=="3l"){D=B-14.3L;H-=D;H=14.3o/((14.2N-14.2x)/H);F.2D-=D}}1b{G=1f}}F.2D-=H}}}if(14.o.1S=="3l"){F.2D>14.2x&&(E.8C*=(-1))}1b{E.8C=60*(-1);F.2D<14.2x&&(E.3j=0.3)}G&&(E=14.bM(F));C&&(E.bT=0);18 E},5Z:17(E){1a C,D,G,B,F=14.5i-E;E||(E=0);14.5i=E;1r(C=0;C<14.l;C++){G={1N:0,1M:0};B={1N:0,1M:0};D=14.ep(14.1h[C],F);G[14.1x.1W]=14.1h[C].2D;B[14.1x.1W]=D.8C;14.1h[C].1o.1L(e,"4w("+G.1N+"2G, "+G.1M+"2G, "+D.9X+"2G)eO("+B.1M+"a5)eI("+B.1N+"a5)");14.1h[C].33&&14.1h[C].33.3V(D.bT);if(14.o.1S=="3l"){14.1h[C].1o.1L("z-1U",14.bS(14.1h[C]))}1b{14.1h[C].1o.3V(D.3j)}}},7U:17(C){1a B=14.2v;if(C.7W){C.6A&&(B=C.6A);if(C.1I=="1F"){14.1n.2V=1c;if(14.1m+B>14.l-1){if(14.1m!=14.l-1){B=14.l-1-14.1m;14.1m+=B;14.1n.2Q=1f}1b{14.1m=0;B=14.l-1;14.1n.2V=1f;14.1n.2Q=1c;C.1I="2w"}}1b{14.1m+=B;if(14.1m===14.l-1){14.1n.2Q=1f}}}1b{14.1n.2Q=1c;if(14.1m-B<0){if(14.1m!=0){B=14.1m;14.1m-=B;14.1n.2V=1f}1b{14.1m=14.l-1;B=14.l-1;14.1n.2V=1c;14.1n.2Q=1f;C.1I="1F"}}1b{14.1m-=B;if(14.1m===0){14.1n.2V=1f}}}}1b{!C.1I&&(C.1I=C.1Y>=14.1m?"1F":"2w");B=1g.1E(14.1m-C.1Y);14.1m=C.1Y}14.2l=14.3o*B;18 C.1I},ku:17(B){B.1I=14.7U(B);14.4A=B.4A;14.1s("3N-2A-1p",{3Z:[14.1m]});14.6N(B.1I=="1F"?14.5i-14.2l:14.5i+14.2l)},6N:17(B){14.4p=B;14.fx=1w q.4n(14.1j,{3I:9q,2Y:14.o.6p,aB:(17(C){14.5Z(C.1W)}).1d(14),57:i(17(){14.75()}).1d(14)}).2A({1W:[14.5i,B]})},6G:17(C){1a B=1g.1E(14.4p-14.5i)*(C||0.2);if(1g.1E(B)<0.kt){3s(14.2R);14.2R=1i;14.2L=1c;14.1s("5m-5S",{3Z:[14.1m]});18}if(14.4p<14.5i){B*=(-1)}14.5Z(14.5i+B);14.2R=4P(14.6G.1d(14,C),30)},cj:17(F,G){1a D,C=F.2D+14.2T,B=F.2D,E=i(17(H){if(C>14.3L&&C<14.2N||H){G=(14.2N-14.2x)/14.3o*G;if(C-G>=14.2N){D=14.2N-C;G+=D;G=14.3o/((14.2N-14.2x)/G);B+=D}1b{if(C-G<=14.3L){if(14.o.1S=="3l"){D=C-14.3L;G-=D;G=14.3o/((14.2N-14.2x)/G);B-=D}}}B-=G}}).1d(14);if(C>=14.2N){if(C-G<14.2N){D=C-14.2N;G-=D;B-=D;E(1f)}1b{B-=G}}1b{if(C<=14.3L){if(14.o.1S=="3t"){G=(14.2N-14.2x)/14.3o*G}if(C-G>14.3L){D=14.3L-C;G+=D;B+=D;E(1f)}1b{B-=G}}1b{E()}}18 B},7L:17(){1a D,C,B,E=14.5i-14.4p;if(14.o.1S=="3t"){E*=2}1r(D=0;D<14.l;D++){C=!C?14.cj(14.1h[D],E):B;B=(D+1<14.l)?14.cj(14.1h[D+1],E):1i;if(C+14.2T>14.3L||D==14.l-1){if(B&&B+14.2T>=14.2N||!B){B=kc}if(14.2x-(C+14.2T)<(B+14.2T)-14.2x){14.1m=D}1b{14.1m=D+1}if(14.1m===0){14.1n.2V=1f}1b{if(14.1m===14.l-1){14.1n.2Q=1f}}14.4p=14.2x-14.1m*14.3o;1O}}},7d:17(){1a C,B;14.1j.1G("4r",i(17(D){if(1f===14.o.4D||D.4M||"3t"===14.o.1S&&1g.1E(D.3J)>1g.1E(D.3O)||"3l"===14.o.1S&&1g.1E(D.3J)<1g.1E(D.3O)){14.1s("5m-2A");14.fx&&14.fx.27();14.fx=1i;D.27();B=1g.1E(D.3J)<1g.1E(D.3O)?D.3O:-1*D.3J;B=D.4M?(B*14.3o):(B*(8/13));!D.4M&&(B=B>0?1g.9M(14.3o/4,B):1g.6S(14.3o/4*(-1),B));14.4p-=B;3s(C);C=4P(i(17(){14.7L()}).1d(14),2z);if(14.4p>=14.2x){14.4p=14.2x;14.1m=0}1b{if(14.4p<=14.2x-((14.l-1)*14.3o)){14.4p=14.2x-((14.l-1)*14.3o);14.1m=14.l-1}}if(!14.2R){14.6G(0.fF)}}}).1d(14))},7f:17(){1a E=(14.1x.1W=="1N")?"x":"y",G={x:0,y:0},F={x:0,y:0},C=1c,B=1c,D=i(17(H){if("4c"==H.4T){i(1k.4j).2X("21-7N");B=1f;G.x=F.x=H.x;G.y=F.y=H.y;14.1n.2V=1c;14.1n.2Q=1c}1b{if(B){G.x=H.x;G.y=H.y;if("9a"==H.4T){i(1k.4j).4L("21-7N");B=1c;if(C){14.7L();C=1c}}1b{if(14.o.1S=="3t"||1g.1E(H.x-F.x)>1g.1E(H.y-F.y)){H.4l();if(!C){14.fx&&14.fx.27();14.1s("5m-2A");3s(14.2R);14.2L=1f;14.2R=1i;C=1f}14.4p-=(F[E]-G[E]);!14.2R&&14.6G()}1b{14.2L=1c}F.x=G.x;F.y=G.y}}}}).1d(14);14.1j.1G("2u 2H",D)},27:17(){14.fx&&14.fx.27(1f);14.fx=1i;3s(14.2R);14.2R=1i;14.4p&&14.5Z(14.4p)},3P:17(){1a B;14.27();14.2l=0;14.1m=0;14.24=14.7l();14.2C=0;1r(B=0;B<14.l;B++){14.1h[B].1y=14.1h[B].1o.2j(1f);14.2C+=14.1h[B].1y[14.1x.1y]}14.2T=14.1h[0].1y[14.1x.1y]/2;if(14.o.1S=="3l"){14.2x=14.24/2}1b{14.2x=14.2T+(14.2T/50*15)}14.5i=14.4p=14.2x;if(14.o.1S=="3l"){14.3L=14.2x-(14.2T*2);14.2N=14.2x+(14.2T*2)}1b{14.3L=0;14.2N=14.2x+14.2T+14.2T*0.8}14.ft();14.5Z(14.5i);14.9l()},cg:17(){14.1h.1A(i(17(B){if(14.o.1S=="3l"){B.1o.3e.3j=""}1b{B.1o.1L("z-1U","")}}).1d(14))}});q.1T(c.29,q.6O);q.4V.kd=c;1a x=17(E,O){1a I,G,C,K,N,F,J,L,H=0,B,D,M="ke kf 2o 1y.";14.1t=1w q.c9(l);14.o=14.1t.g8.1d(14.1t);14.2f=14.1t.2f.1d(14.1t);14.1t.7G(1l.bh||{});14.1t.7G((1l.bG||{})[E.3S("id")||""]||{});14.1t.8J(E.3S("2i-1t")||"");if(q.1e.8h){14.1t.7G(1l.bm||{});14.1t.7G((1l.bf||{})[E.3S("id")||""]||{});14.1t.8J(E.3S("2i-8h-1t")||"")}if("2e"==q.1P(O)){14.1t.8J(O||"")}1b{14.1t.7G(O||{})}if(!14.o("aE")){18 1c}14.kb=i(E).3p("2o",14);q.$6T(14);14.7R=1c;if(q.1e.1z){q.$A(E.9Q("a")).1A(17(P){P.9S=P.9S});q.$A(E.9Q("2b")).1A(17(P){P.43=P.43})}14.er=i(E).3S("2B")||i(E).3S("6j");14.6y=[];14.2a={4u:14.o("4u"),5a:1f,6W:1c,1K:1f,2s:1c,eo:"2o",4I:1f,f4:"5E-5M(.8, 0, .5, 1)",7w:"1Z"};14.id=E.3S("id")||"4Q-"+1g.5Y(1g.7E()*q.5X());14.1j=E.3p("2o",14);14.41=q.$1w("38",{"2B":"21-41"},{5r:"7O-7C"});14.6Y=q.$1w("38",{"2B":"21-1h-1j"});14.7R=1c;1r(I=14.1j.2g.1q-1;I>=0;I--){C=14.1j.2g[I];if(C.52===3||C.52===8){14.1j.9e(C)}1b{14.6y.2c(C)}}if(14.6y.1q===0){18}F=17(Q){1a P=17(T){1a S=Q.2g[T],R=S.2y.34();if("br"===R||"hr"===R){18 P(++T)}1b{18 S}};18 P(0)};L=F(14.1j);if(L.2y=="cq"){L=i(L).7J("6b")[0]||L.3n}if(L.2y=="A"){L=i(L).7J("6b")[0]||L.3n}14.aQ=1c;if(L.2y=="6b"){14.aQ=L;J=L.3S("2i-43");if(J){J=(J+"").48();if(""!=J){L.3v("43",J)}}}14.ao=1i;B=i(17(P){14.ao=4P(i(17(){14.8z=i(F(14.1j)).2j();if(14.8z.1J==0){if(H<2z){H++;B(P)}}1b{3s(14.ao);P()}}).1d(14),2z)}).1d(14);B(i(17(){14.7I=i([]);K=q.$A(14.1j.2g);14.2V=K[0];i(K[0]).1L("5r","5G");14.fe={1y:A(K[0]),5T:o(K[0]),5w:h(K[0]),6f:m(K[0])};K[0].1L("5r","7O-7C");14.1j.1L("5r","5G");14.bP=A(14.1j);14.1j.1L("5r","7O-7C");14.5z=1i;14.cP();if(14.2a.1K){14.1K=1w q.3Q.9i(14.1j)}14.cz();14.eX();D=i(17(){1a Q,R=1f,P={};14.6Z=q.$1w("38",1i,{2D:"7o",1N:"-en",1M:"-en"}).4e(1k.4j);14.2M();1r(I=0,G=K.1q;I<G;I++){Q=K[I].2y.34();if(R){if("br"===Q||"hr"===Q){4Y}}1b{if("br"===Q||"hr"===Q){4Y}}R=1c;i(K[I]).3V(0).1L("5r","7O-7C");14.2c(K[I],P);P={};if(I==G-1){14.87()}}}).1d(14);1w q.cf([{1o:K[0]}],{79:1,5c:17(P){6w"ez: 4Q: ez ka k6 - "+P.2b.43+". "+M},67:(17(P,Q){14.5z=(P.2b)?P.2b.2j():P.1y;if(Q.1o.2y.34()=="4t"){q.$A(Q.1o.2g).1A(i(17(S){if(S.2y.34()=="33"){1a R=m(i(S));14.97=S.2j();14.97.1B+=R.1B;14.97.1J+=R.1J;14.5z.1J+=14.97.1J}}).1d(14))}D()}).1d(14)})}).1d(14))};q.1T(x.29,{ck:1c,cP:17(){if("2q"==14.o("2t")&&(q.1e.1z||!q.1e.51.2q)){14.2f("2t","2o");14.2f("1h","7v");14.2f("3u","1Z")}if(q.1e.1z&&q.1e.1z<=9&&14.o("2t")=="64-6h"){14.2f("2t","2o")}14.2a.6W=1k.k5.k7.7z("#6g-6W-2t")!=-1;if(q.1P(14.o("1h"))==="40"){14.2a.7w=14.o("1h");i(17(){1a D,F,C,E=14.2a.7w,B=E.1q;1r(D=0;D<B;D++){1r(F=D+1;F<B;F++){if(E[D][0]<E[F][0]){C=E[D];E[D]=E[F];E[F]=C}}}14.2a.7w=E}).1d(14)();14.2f("1h","1Z")}if(14.o("8P")===0){14.2f("8P",10)}if(14.o("4u")<0||14.o("3u")==0){14.2a.2s=1f}if(i(["64-6h","2q"]).3f(14.o("2t"))){14.2a.2s=1c}if("9E"===14.o("1n")||"1c"===14.o("1n")){14.2f("1n",1c)}if(14.o("2t")=="66"||14.2a.2s){14.2f("1n","3z")}if(14.o("2t")=="64-6h"){14.2f("1n",1c)}if("6z"===14.o("1n")&&"2q"===14.o("2t")){14.2f("1n",1c)}if(i(["64-6h","66"]).3f(14.o("2t"))||14.2a.2s){14.2f("cb",1c)}if(i(["64-6h","66"]).3f(14.o("2t"))&&!14.2a.2s){14.2f("3u",1)}if(i(["64-6h","66"]).3f(14.o("2t"))&&!i(["1Z","7v"]).3f(14.o("1h"))){14.2f("1h","1Z")}if(14.o("2t")=="2q"&&14.o("1h")=="1Z"){14.2f("1h","7v")}if(14.o("2t")=="2q"){14.2f("3u","1Z")}if(14.2a.2s){14.2f("8o","5E-5M(0, 0, 1, 1)")}1b{if(14.o("8o")=="5E-5M(0, 0, 1, 1)"){14.2f("8o",14.2a.f4)}}if(i(["64-6h","66"]).3f(14.o("2t"))){14.2f("3R",1c);14.2a.7w="1Z"}14.k8=14.o("1B");14.k9=14.o("1J");if(14.2a.2s){14.2f("4u",0)}if(i(["64-6h","66"]).3f(14.o("2t"))||14.2a.2s){14.2f("2h",1c)}if("1c"===14.o("2h")||"9E"===14.o("2h")){14.2f("2h",1c)}if(14.o("2h")){14.1j.2X("4Q-2h-"+14.o("2h"))}14.1j.2X("4Q-"+14.o("1S"));14.1j.3v("2i-2t",14.o("2t"))},cz:17(){if(!14.o("cb")){if(14.26){14.26.2J();14.26=1i}18}if(!14.26){14.26=1w q.3Q.f3({},14.1j,i(17(){18 14.5C}).1d(14));14.1j.2X("4Q-26");14.26.2K("26-4k",i(17(B){14.2P({1I:B.1I,1Y:B.f2})}).1d(14))}},cA:17(){1a C,B=i([]);if(!14.1p){18}1r(C=0;C<14.1p.l;C++){if(i(["2o","2q"]).3f(14.o("2t"))){if(C%14.1p.2v==0){B.2c(14.1p.1h[C].1U)}}1b{B.2c(14.1p.1h[C].1U)}}14.26.2c(B)},cV:17(){1a B=h(14.1j);if(14.2h){14.2h.2J();14.2h=1i}14.41.2W({1M:"",1N:"",6q:"",5h:""});if(14.o("2h")){if(!14.2h){14.2h=1w q.3Q.f0({1S:"21-"+14.o("1S"),"2B":"21-3T",9Z:"21-6H",aR:"21-kg"},14.1j);14.1p.2K("7u",14.2h.7u.1d(14.2h,2U));14.1p.2K("4i",14.2h.4i.1d(14.2h,2U));14.1p.2K("eZ",14.2h.4q.1d(14.2h,2U));14.1p.2K("f1",14.2h.2M.1d(14.2h,2U));if(!14.o("1n")){14.1p.2K("2o",14.2h.4i.1d(14.2h,2U));14.1p.2K("1m-3g",14.2h.7u.1d(14.2h,"8i"));14.1p.2K("5d-3g",14.2h.7u.1d(14.2h,"7M"))}14.2h.2K("1F",(17(F){14.2P("1F")}).1d(14));14.2h.2K("2w",(17(F){14.2P("2w")}).1d(14))}1b{14.2h.eY(14.o("1S"))}if(14.o("2h")=="c7"){1a E=14.o("1S")=="3l"?i(["1N","6q"]):i(["1M","5h"]),C=14.o("1S")=="3l"?"1B":"1J",D=1X(14.2h.8i.2j()[C]);E.1A(i(17(F){14.41.1L(F,D+(B[C]/2))}).1d(14))}}},8K:17(){if(14.o("1B")!="1Z"){14.1j.1L("1B",14.o("1B"))}if(14.o("1J")!="1Z"){14.1j.1L("1J",14.o("1J"))}18},eX:17(){1a B=i(["2o","2q"]).3f(14.o("2t"))?"1p":14.o("2t");14.1p=1w q.4V[("-"+B).5V()](14.6Y,{1S:14.o("1S"),3I:14.o("8P"),2s:14.2a.2s,6p:14.o("8o"),1n:14.o("1n"),3u:14.o("3u"),1p:14.o("2t"),3R:14.o("3R"),1K:14.2a.1K,4I:14.2a.4I,6W:14.2a.6W,4D:14.o("4D"),6t:14.o("6t")});if(14.o("1h")!="1Z"&&14.o("3u")=="1Z"){14.2f("3u",14.o("1h"))}14.1p.2K("eS",i(17(){14.5C=1c;14.1Z()}).1d(14))},2P:17(B,C){if(14.o("2t")=="2q"&&/^\\+|^\\-/.3E(B)){B=/^\\+/.3E(B)?"1F":"2w"}if(!14.5C&&!14.1p.71){14.5C=1f;3s(14.7A);14.1p.2P(B,i(17(D,E){14.5C=1c;if(E){18}14.1s("cQ-2o");if(!14.2a.2s||14.ck||14.5D){if(14.6Z.2g.1q==0){14.6Z.2J()}if(14.o("1n")){14.1p.7F()}14.o("aq")({id:14.id,1h:D});14.1p.kh=1c;C&&C()}1b{14.2P("1F",C)}}).1d(14))}},ff:17(G){1a C,F,D,B,E;if(G.2y.9f()=="A"){if((B=i(G).7J("6b")[0])){if((E=i(G).7J("an")[0])&&""!==E.eR.48()){F=i(E.6i(1f)).2X("21-eW")}1b{if(((C=B.eT)&&3==C.52&&""!==C.9L.48())||(E&&(C=E.eT)&&3==C.52&&""!==C.9L.48())){F=q.$1w("an",{"2B":"21-eW"}).2r(C.6i(1f))}}1r(D=G.2g.1q-1;D>=0;D--){if(B!==G.2g[D]){G.9e(G.2g[D])}}if(F){G.2r(F)}}}1b{if(G.2y.34()=="4t"){q.$A(G.2g).1A(i(17(H){if(H.2y.34()=="33"){C=H.3S("id")||"33-"+1g.5Y(1g.7E()*q.5X());H.3v("id",C);F=H;14.kp=q.eV("#"+C+":kq",{"5w-1M":(14.97.1J+o(i(H))/2)/1X(14.8z.1B)*2z+"%"})}}).1d(14))}}18{1o:G,33:F}},a3:17(B){if(14.o("1h")!="1Z"){B.1o.1L(14.o("1S")=="3l"?"1B":"1J",2z/14.o("1h")+"%")}},ak:17(C){1a D,B;if(14.o("1h")=="7v"){14.2f("1h",1g.5Y(14.41.2j()[14.1p.1x.1y]/14.5z[14.1p.1x.1y]))}1b{if(14.o("1h")=="1Z"){if(!14.fe.1y[14.1p.1x.1y]){D=14.5z[14.1p.1x.1y]||14.8z[14.1p.1x.1y];B=14.6Y.2j();if("3t"===14.o("1S")){D=1g.9M(D,B[14.1p.1x.1y])}B=(D+m(C.2m)[14.1p.1x.1y]+o(C.2m)[14.1p.1x.1y]+h(C.2m)[14.1p.1x.1y]+h(C.1o)[14.1p.1x.1y])/14.6Y.2j()[14.1p.1x.1y]*2z;if(B>2z){B=2z}C.1o.1L(14.1p.1x.1y,B+"%")}}}},2c:17(C,B){C.2M();C={2m:C};if(B.1M){B.1M.1A(17(E){E.2J()})}if(B.5h){B.5h.1A(17(E){E.2J()})}C.kr=B;1a D=14.ff(C.2m);C.2m=D.1o;C.33=D.33;C.1o=q.$1w("38",{"2B":"21-2Z"});C.1o.4e(14.6Y);14.ak(C);14.a3(C);C.2m.4e(14.6Z);14.1p.2c(C)},2M:17(){if(14.cC){18}14.cC=1f;14.1j.2r(14.41.2r(14.6Y)).2M().3v("id",14.id);14.1j.1L("5r","7O-7C");if(14.o("2h")){14.cV();14.o("1n")&&14.2h.7u("7M");14.2h.4q()}14.9z();14.8K();if(14.aQ){if("3l"===14.o("1S")&&14.1j.2j().1B<14.5z.1B){14.9z(1f);14.8K()}}14.9K();i(1l).1G("5H",14.3P.1d(14))},87:17(B){14.1p.2K("fg",i(17(C){14.2P(C.1I)}).1d(14));14.1p.2K("2M-14",i(17(C){14.2P(C.1U)}).1d(14));14.1p.2K("aM",i(17(){14.1K&&14.1K.2M()}).1d(14));14.1p.2K("aN",i(17(){14.1K&&14.1K.4q()}).1d(14));14.1p.2K("4f",i(17(){14.1p.aZ(i(17(){14.1p.2K("fh",i(17(){14.5C=1c}).1d(14));14.1p.2K("2Z-4k",i(17(E){1a D=1f,C,F;if(14.o("2t")=="66"){C=6c/14.1p.l;F=(6c-14.1p.1m*C+E.1U*C)%6c;if(F>90&&F<ks){D=1c}}D&&14.2P(E.1U)}).1d(14));if(14.26){14.26.o.1h=14.1p.1h.1q;14.cA();14.26.2M()}14.1p.2K("3N-2Z-f7",i(17(C){14.o("c2")({id:14.id,2Z:C.av})}).1d(14));14.1p.2K("3N-2Z-au",i(17(C){14.o("bZ")({id:14.id,2Z:C.av})}).1d(14));14.1p.2K("3N-2A-1p",i(17(C){14.26&&14.26.cm(C.3Z,!14.o("1n"));14.o("bX")({id:14.id,1h:C.3Z})}).1d(14));14.1p.2K("5m-2A",i(17(){14.5C=1f;14.1Z()}).1d(14));14.1p.2K("5m-5S",i(17(C){14.26&&14.26.cm(C.3Z,!14.o("1n"));14.5C=1c;14.o("aq")({id:14.id,1h:C.3Z});if(14.6Z.2g.1q==0){14.6Z.2J()}14.1Z()}).1d(14));14.1j.1L("6E","72");14.7R=1f;14.o("bW").2k(14,14.id);i(1l).1G("5H",i(17(){14.5C=1c;if(14.2a.2s){14.2P.1d(14,"1F").3r(5B)}1b{14.1Z()}}).1d(14));14.cw();if("3t"===14.o("1S")&&/%$/.3E(14.o("1J"))){14.2f("1J",14.1j.2j().1J);14.8K()}if(14.o("4u")!=0){14.1Z()}1b{14.5D=1f}if(14.2a.2s){14.5D=1c;14.2P.1d(14,"1F").3r(5B)}14.7R=1f}).1d(14))}).1d(14));14.1p.87()},cw:17(){14.2K("cQ-2o",i(17(){if(14.2a.4u!=0){!14.2a.2s&&14.1Z()}}).1d(14));if(!q.1e.cl&&(14.2a.5a||14.2a.2s)){14.41.1G("5o 6P",i(17(C){C.27();1a B=C.az();4R(B&&B!==14.41){B=B.3q}if(B==14.41){18}if(14.2a.5a&&!14.5D){14.eJ="5o"==C.1H;14.ck="5o"==C.1H;if(14.2a.2s){if(C.1H=="5o"){14.cT()}1b{14.2P("1F")}}1b{14.1Z()}}}).1d(14))}if(!14.2a.2s&&"2q"===14.o("2t")&&14.o("4D")){14.41.1G("4r",i(17(B){1a C=-1*(1g.1E(B.3J)<1g.1E(B.3O)?B.3O:-1*B.3J);C=B.4M?(C):(C*(8/54));if((1f===14.o("4D")&&B.4M)||"3t"===14.o("1S")&&1g.1E(B.3J)>1g.1E(B.3O)||"3l"===14.o("1S")&&1g.1E(B.3J)<1g.1E(B.3O)){B.27();if(1g.1E(C)<0.6){18}14.2P(C>0?"2w":"1F")}}).1d(14))}},9z:17(J){1a I="1B",K="1J",F=14.o("1S")=="3t",B=14.1j.2j(),E={1B:0,1J:0},G=h(14.1j),N=o(14.41),R=m(14.41),L=h(14.41),M=m(14.2V),H=q.$1w("38",{"2B":"21-2Z"}).4e(14.41.3n),O,P,D,Q,C=h(H);H.2J();if(14.1j.2n("eP-ko")=="5T-eP"){E=o(14.1j)}if(F){I=K;K="1B"}if(14.o(I)=="1Z"&&!1X(14.bP[I])){if(F){if(!6u(14.o("1h"))){14.2f(I,B[I]*14.o("1h"))}1b{14.2f(I,B[I])}}1b{14.2f(I,"2z%")}}if(14.o(K)=="1Z"&&!1X(14.bP[K])||J){D=E[K]+G[K]+N[K]+M[K]+C[K];if(F){O=1g.9M(14.5z[K],B[K])}1b{O=14.5z[K];if(14.aQ){P=14.5z[K]/14.5z[I];if(14.5z[I]>B[I]){O=B[I]*P}}}Q=(O+m(i(14.6y[0]))[K]+h(14.6y[0])[K]+o(14.6y[0])[K])||14.8z[K]||B[K];Q+=D;Q+="";14.2f(K,Q)}},9K:17(){1a D,C,B,F,E=1f;if(14.2a.7w!="1Z"&&i(["2o","2q"]).3f(14.o("2t"))){F=14.2a.7w;B=F.1q;C=14.2a.eo=="2o"?14.1j.2j()[14.o("1S")=="3t"?"1J":"1B"]:i(1l).2j()[14.o("1S")=="3t"?"1J":"1B"];1r(D=B-1;D>=0;D--){if(C<=F[D][0]&&!6u(F[D][1])){14.2f("1h",F[D][1]);E=1c;1O}1b{if(0===D){if(i(["66","64-6h"]).3f(14.o("2t"))){14.2f("1h",1)}1b{if("2q"===14.o("2t")){14.2f("1h","7v")}1b{14.2f("1h","7v")}}}}}q.$A(14.6Y.2g).1A(i(17(H,G){14.ak({1o:H,2m:H.3n});14.a3({1o:H})}).1d(14));if(14.1p.1h.1q>0){14.1p.6L()}}},3P:17(){14.9K();14.26&&14.26.6L()},5H:17(){if(14.7R){14.3P();14.1p.3P()}},cT:17(){14.1p.5a()},27:17(){14.1j.3p("kn-1h-3j",1c);14.1p&&14.1p.27();14.5C=1c;3s(14.7A);14.7A=1c},kj:17(B){18 B==14.o("2t")},cs:17(C,B){if(!i(["c2","bZ","bW","bX","aq"]).3f(C)){18}14.2f(C,B)},7x:17(){1a B,C,D;14.27();3s(14.ao);14.41.1V("5o 6P");14.41.1V("b1");14.1p&&14.1p.7x();if(14.7I){1r(B=0;B<14.7I.1q;B++){q.d3("eM-d1",14.7I[B])}}14.1j.4L("4Q-26");i(14.6y).1A(i(17(E){if(E.3q){i(E).2J()}D=E;if(D.2y=="cq"){D=D.3n}if(D.2y=="A"){D=D.3n}if(D.2y=="6b"){C=D.3S("2i-43");if(C){C=(C+"").48();if(""!=C){D.3U("43")}}}if(E.2g.1q>0&&E.2y.34()=="a"){q.$A(E.2g).1A(i(17(F){if(F.2y&&F.2y.34()=="an"){E.2r(F.2g[0]);F.2J()}}).1d(14))}E.2W({6s:"",3j:"1"})}).1d(14));14.6Z&&14.6Z.2J();q.$A(14.1j.2g).1A(17(E){i(E).4B()});i(14.1j).3U("2i-2t");i(14.1j).cr().4L().2X(14.er);14.1j.2W({1B:"",1J:"",6s:"",5r:"",6E:""});14.1j.3D("2o");1r(B=14.6y.1q-1;B>=0;B--){i(14.6y[B]).2W({3j:""}).4e(14.1j)}14.o("eC").2k(14,14.id);18 1i},8x:17(B){if(1i===B||2U===B){B=14.o("4u")}1b{B||(B=cS);B=1X(B);if(6u(B)){B=14.o("4u")}}if(!14.5D){18}if(!14.7A){14.5D=1c;14.1p.8Y=1c;14.2a.4u=B;14.2P("1F")}},5a:17(){if(14.5D){18}14.5D=1f;if(14.2a.2s){14.cT()}1b{14.27()}14.1Z()},8T:17(B){1a E,D={1J:"",1B:""},C=14.o("2t");14.27();14.1j.4L("4Q-2h-"+14.o("2h"));14.1j.4L("4Q-"+14.o("1S"));14.41.1V("5o 6P b1");14.eK("cQ-2o");14.1K=1i;14.1j.4L("4Q-26");if("2e"==q.1P(B)){14.1t.8J(B||"")}1b{14.1t.7G(B||{})}if(C!=14.o("2t")){18 1c}14.2a.4u=14.o("4u");14.cP();14.1p.1h.1A(i(17(F){F.1o.2W(D)}).1d(14));14.1p.4m.1A(i(17(F){i(F).1o.2W(D)}).1d(14));14.1p.6U.1A(i(17(F){i(F).1o.2W(D)}).1d(14));14.cV();1r(E=0;E<14.7I.1q;E++){14.7I[E]&&q.d3("eM-d1",14.7I[E])}14.1p.eN({1S:14.o("1S"),3I:14.o("8P"),2s:14.2a.2s,6p:14.o("8o"),1n:14.o("1n"),3u:14.o("3u"),1p:14.o("2t"),3R:14.o("3R"),1K:14.2a.1K,4I:14.2a.4I,6W:14.2a.6W,4D:14.o("4D"),6t:14.o("6t")});14.9z();14.8K();14.9K();q.$A(14.6Y.2g).1A(i(17(G,F){14.ak({1o:G,2m:G.3n});14.a3({1o:G})}).1d(14));14.1p.6L();14.cz();if(14.26){14.cA();14.26.2M()}if(14.o("4u")==0){14.5a()}1b{14.5D=1c}14.o("2h")&&14.2h.2M();14.cw();if(14.2a.2s){14.2P.1d(14,"1F").3r(5B);14.5D=1c}1b{14.1Z()}18 1f},1Z:17(){1a B="1F";3s(14.7A);14.7A=1c;if(14.5C||14.5D||14.eJ){18}if(14.2a.4u!=0){14.7A=4P(i(17(){14.2P(B)}).1d(14),1g.1E(14.2a.4u))}}});q.1T(x.29,q.6O);q.4V.at=x;1a z=17(C){1a B=g(C);if(!B){18}18{cs:B.cs.1d(B),5a:B.5a.1d(B),8x:i(17(D){14.8x(D)}).1d(B),1F:i(17(D){D=!D?"1F":a(D,"+");14.2P(D)}).1d(B),2w:i(17(D){D=!D?"2w":a(D,"-");14.2P(D)}).1d(B),2P:i(17(D){if(!D||6u(1g.1E(1X(D)))){D="1F"}14.2P(D)}).1d(B),8T:i(17(D){if(!D||q.1P(D)!="8s"){D={}}14.8T(D)}).1d(B)}},g=17(C){1a B=1i;if(q.1P(C)=="2e"&&i(C)||q.1P(C)=="7D"){B=i(C).2p("2o")}1b{if(q.1P(C)=="17"&&(C 4v q.4V.at)||C&&C.cC){B=C}}18 B},d=17(D,E,C){1a B=g(D);if(B){18 B[C](E)}1b{E=D;D=v}i(D).1A(17(F){F[C](E)})},a=17(C,B){if(q.1P(C)==="2e"){C=1X(C);if(6u(C)){C=C}}if(q.1P(C)==="5e"){C=B+C}18 C},s=17(C){1a B=q.$A((C||1k).fi("4Q")).fm(17(D){18 n.2A(D)});k=1f;18 B},k=1c,w=17(B){18 v=i(v).4N(17(C){18 C.7x()})},v=[],n={5g:"ki.0.4",2A:17(C){1a B=1i;if(3h.1q){C=i(C);if(C&&i(C).8B("4Q")){if(B=i(C).2p("2o")){18 B}1b{B=1w q.4V.at(C,k?{aE:1f}:{});if(!B.o("aE")){B=1i;18 1c}1b{v.2c(B);18 B}}}1b{18 1c}}1b{18 s()}},27:17(B){if(3h.1q){B=(B 4v q.4V.at)?B:(i(B)&&i(B).2p("2o")||1i);if(!B){18}v.bA(i(v).7z(B),1);B.7x()}1b{w();18}},kk:17(B){if(B){n.27(B);18 n.2A(B.id||B)}1b{w();18 s()}},kl:17(D){1a C,B=1c;if(D){C=g(D);if(C){B=C.7R}}18 B},km:17(B){18 z(B)},8T:17(B,C){18 d(B,C,"8T")},5H:17(B){if(B){d(B,1i,"5H")}1b{i(v).1A(17(C){n.5H(C)})}},2P:17(B,C){if(2U!=B&&1i!=B){d(B,C,"2P")}},5a:17(B){d(B,1i,"5a")},8x:17(B,C){d(B,C,"8x")},1F:17(B,C){1a D;C=!C?"1F":a(C,"+");if(!B){B=C}1b{if(!g(B)){B=a(B,"+")}}d(B,C,"2P")},2w:17(B,C){1a D;C=!C?"2w":a(C,"-");if(!B){B=C}1b{if(!g(B)){B=a(B,"-")}}d(B,C,"2P")}};i(1k).1G("9c",17(){q.3c(1l.bh)||(1l.bh={});q.3c(1l.bm)||(1l.bm={});q.3c(1l.bG)||(1l.bG={});q.3c(1l.bf)||(1l.bf={});n.2A.3r(10)});18 n})();',62,1376,'||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||this|||function|return||var|else|false|jBind|jBrowser|true|Math|items|null|container|document|window|last|loop|node|effect|length|for|jCallEvent|options|Event|Custom|new|p_|size|ieMode|jEach|width|containerPosition|event|abs|forward|jAddEvent|type|direction|height|progress|jSetCssProp|top|left|break|jTypeOf|handler|case|orientation|extend|index|jRemoveEvent|pos|parseInt|target|auto||mcs|||containerWidth||bullets|stop||prototype|_insideOptions|img|push||string|set|childNodes|arrows|data|jGetSize|call|distance|content|jGetCss|scroll|jFetch|animation|append|continuous|mode|touchdrag|itemStep|backward|center|tagName|100|start|class|allSize|position|default|nextAngle|px|mousedrag|btnclick|jRemove|bindEvent|move_|show|lastSide|load|jump|lastItem|moveTimer|circle|moiety|undefined|firstItem|jSetCss|jAddClass|transition|item||||figcaption|toLowerCase||timeStamp||div||clientY|clientX|defined||style|contains|frame|arguments|pointerType|opacity|changedTouches|horizontal|globalIndex|firstChild|stepDistance|jStore|parentNode|jDelay|clearTimeout|vertical|step|setAttribute|tap|loader|fullViewedItems|infinite|PI|enum|Element|jDel|test|_getItemIndex|oneOf|parseFloat|duration|deltaY|handle|firstSide|try|on|deltaX|onResize|Modules|lazyLoad|getAttribute|button|removeAttribute|jSetOpacity|navigator|catch|Doc|arr|array|wrapper||src|||||jTrim||parent|doneFlag|dragstart|J_EUID|jAppendTo|complete|init|Class|enable|body|click|stopDefaults|itemsFirstClones|FX|touchpinch|nextPosition|hide|mousescroll|hasOwnProperty|figure|autoplay|instanceof|translate3d|identifier|pushToEvents|constructor|callback|kill|reverse|scrollOnWheel|pointerEnabled|loaded|match|dblbtnclick|stopDownload|order|pow|jRemoveClass|isMouse|filter|ms|setTimeout|MagicScroll|while|touches|state|dbltap|Scroll|J_TYPE|canvas|continue|styles||features|nodeType||||domPrefix|onComplete|||pause|getVisibleIndexes|onerror|first|number|schema|version|bottom|lastPosition|jGetPosition|extraRemove|clone|drag|globalLength|mouseover|angle|listeners|display|orders|delete|apply|two|padding|getDoc|exitItems|sizeFirstImg|webkit|200|hold_|pause_|cubic|replace|none|resize|cubicBezier|ready|trident|loadAll|bezier|activeBullet|requestAnimationFrame|_shiftContainer|round|touch|end|border|radius|jCamelize|Array|now|floor|renderCarousel|||messageBox||cover|boolean|carousel|onload|||sin|IMG|360|endItem|offsetHeight|margin|magic|flow|cloneNode|className|timer|mouseup|pageX|createElement|detach|timingFunction|right|pageY|visibility|keyboard|isNaN|lastAngle|throw|jBindAsEvent|originalNodes|rewind|goTo|add|transitionend|events|overflow|_getGlobalIndex|_move2|hidden|jGetPageXY|flag|shift|update|J_UUID|_move|customEvents|mouseout|enterItems|MSPOINTER_TYPE_TOUCH|max|uuid|itemsLastClones|dragged|debug|captionA|itemsContainer|hashBox||stopScroll|visible|wheel_|isQueueStopped|_onComplete|disableEffect||fullScreen|queue|switch|render|minimum|_initOnWheel|name|_initDragOnScroll|not|itemsVisible|found|correctPosition|MagicJS|_sWidth|scale|ease|absolute|error|insertBefore|gradientBezier|_event_prefix_|move|disable|fit|itemSettings|dispose|onTouchEnd|indexOf|auto_|documentElement|block|element|random|changeClones|fromJSON|getButton|cachedCSS|byTag|notLoaded|searchIndex|prev|dragging|inline|platform|sqrt|scrollReady|showReflection|touchstart|_calcDistance|touchend|defaultMove|mousedown|alternate|easeFn|itemEvent|one||preloadItem||MSPointerUp||done|timedout||cycles|onTouchStart|pointerup|threshold|onready|tooltip|split|mobile|next|_handlers|oncomplete|tm|tempArray|engine|easing|stopQueue|stopDistribution|_options|object|targetTouches|300|animationstart|AnimationEnd|play|AnimationStart|firstItemSize|keydown|jHasClass|rotate|moveSettings|wheelDiff|stopWhell|animationend|dblclick|Transition|fromString|setContainerSize|storage|ceil|appendChild|onStart|speed|toString|_events|_render|updateOptions|action|parseCubicBezier|activate|previous|continuousPause|containerSize||opera|nth|linear|||_EVENTS_|sizefigcaption||readyState|dragend|nativeEvents|domready|onabort|removeChild|toUpperCase|_timer|abort|Progress|_unbind|_cleanup|setCanvasPosition|uaName|_event_del_|AA|join|500|handleMouseUp|text|fallback|getStorage|url|implement|loopBind|pStyles|checkSizes_|jGetScroll|exists|svg|checkLoadingVisibleItems|off|callee|startAlpha|cubicBezierAtTime|btnclickEvent|has|countTheNumberOfItems|nodeValue|min|exitFullscreen|performedOnClones|_removeClones|getElementsByTagName|wrapperPosition|href|MSPointerDown|_bind|forceAnimation|win|translateZ|xhr|classHidden|compatMode|pointermove|pStyles_arr|setPercent|ban|deg|preloadAll|MSPointerMove|getMatrixPosition|setItemStep||||||||allNodes|_event_add_|onprogress|checkWholeItems|Message|android|span|coreTimeout|status|onMoveEnd|transform|_setProperties|Full|out|itemIndex|setReflection|Opacity|charAt|getRelated|onTouchMove|onBeforeRender|relatedTarget|normalizeCSS|autostart|mousemove|cancelAnimationFrame|touchmove|hideTimer|chrome|eventType|keyboardCallback|showProgress|hideProgress|pointerId|dashize|tagImg|classDisabled|lastChild|correctItemPosition|XMLHttpRequest|offsetLeft|createEvent|pointerdown|00001|done2|scrollTop|mousewheel|originSize||fxk|scrollLeft|gecko|0ms|errorEventName|slice|cssDomPrefix||typeof|deactivate|600|MagicScrollMobileExtraOptions|groupLoad|MagicScrollOptions|opr|preloadAllFlag|ignore|el_arr|MagicScrollMobileOptions|onClick|checkLoadedItems|handleMouseDown|keyCode||disableReflection|onclick|caller|addCloneContent|showItem|J_EXT|doc|reset|splice|cssFilters|createTextNode|Tooltip|hideFX|Date|MagicScrollExtraOptions|styles_arr|https|ifndef|maximum|previousScale|zoom|depth|calc|containerCssSize|cycle|stopAnimation|zIndex|captionOpasity|180|getImg|onReady|onMoveStart|removeCanvas|onItemOut|inherit|showCaption|onItemHover|Carousel|Function|handleTouchStart|uaVersion|outside|deltaMode|Options|loadedBytes|pagination|getTarget|priority||QImageLoader|resetZIndex|naturalWidth|startTime|checkPosition|hovered|touchScreen|setActiveBullet|concat|handleTouchMove|handleTouchEnd|FIGURE|jClearEvents|registerCallback|0px|entering|exited|setEvent|correctContainerPosition|05|initBullets|setBullets|exiting|indoc|changeEventName|Alpha|removed|prevIndex|freeTouchPreload|stopEffect|_cleansingStyles|magicJS|presto|Effect|_complete|offsetTop|setupOptions|after|setItemSide|1000|pauseContinuous|originFontSize|setupArrows|capable|_getWheelDiff|cloneFigure|_prepareClones|HTMLElement|css|getElementsByClassName|removeCSS|getContext|isPrimary|onExit|cancelFullScreen|onchange|parseSchema|Number|jToBool|setMessage|loadBlob|isReady|which|interval|onreadystatechange|onEnter|addEventListener|xhr2|fps|finishTime|request||requestFullScreen|clientWidth|stopPropagation|cancelBubble|onAfterRender|preventDefault|getClientXY|ios|curScale|dispatchEvent|detail|_initialDistance|dragmove|wrap|handleMouseMove|wheelDelta|wheelDeltaY|onxhrerror|304|euid|jDefer|wheelDeltaX|progressiveLoad|355|03|Left|easeInCubic|Bottom|easeOutQuad|Right|easeOutExpo|easeInQuad|easeOutCubic|Top|PFX|msExitFullscreen|bounceIn|elasticIn|easeInBack|easeOutBack|easeInExpo|styleFloat|offsetWidth|165|setProps|getBoundingClientRect|getElementById|webkit419|compareDocumentPosition|Microsoft|DXImageTransform|easeInSine|easeOutSine|getComputedStyle|175|progid|045|999||save|10000px|maxSize|calcItemPosition||originalClasses|backcompat|returnValue|_carousel|hone|forEach|String|mjs|Error|od|cos|onStop|jumpToNumber|_moveEffect|removeRule|deleteRule|styleSheet|rotateY|pauseHover_|destroyEvent|100000|magicscroll|setNewOptions|rotateX|box|phone|innerHTML|hold|nextSibling|_scroll|addCSS|caption|initEffect_|setOrientation|hideArrows|ArrowsPair|showArrows|jumpIndex|Bullets|timingFunctionDefault|showThis|enabled|hover|setFontSize|stop_|perspective|firefox|restore|rightQueue|itemCss|parseTag|key_down|disableHold|byClass|mozCancelAnimationFrame|circles|safari|map||androidBrowser|Moz|crios|getTime|backCompat|setItemsPosition|DOMContentLoaded|nativize|getBulletIndex||getDirection|cssPrefix|jGetSize_|Object|unbindEvent|documentMode|arrow|08|bullet|moz|Webkit|toArray|date|textnode|UUID|noimg|multibackground|background|no|DocumentTouch|relative|sheet|attributes|otherSize|scrollbarsWidth|cssTransformProp|inside|cssText|ImageLoader|blur|itemFX|stylesId|origItem|delay|destroy|insertRule|get|Image|SVG11|removeEventListener|feature|ontouchstart|mozCancelFullScreen|oCancelFullScreen|implementation|http|hasFeature|attachEvent|www|org|w3|webkitCancelFullScreen|detachEvent|custom|FormData|ProgressEvent|TR|withCredentials|getOriginalTarget|sort|msCancelFullScreen|getHashCode|xxxx|xxxxxxxx|generateUUID|addRule|4xxx|yxxx|getAbsoluteURL|xy|xxxxxxxxxxxx|cssRules|head|MouseEvent|collection|g4bc9bfe|v3|UIEvent|KeyboardEvent|regexp|RegExp|KeyEvent|doScroll|isPrimaryTouch|querySelector|query|runtime|fireEvent|fullscreenEnabled|msFullscreenEnabled|initEvent|createEventObject|webkitexitFullscreen|420|air|toFloat|jToInt|4294967296|charCodeAt|setInterval|userAgent|evaluate|xpath|edge|jRaiseEvent|midp|linux|lineHeight|mac|getPropertyValue|webos|currentStyle|other|fontWeight|webkitRequestAnimationFrame|oRequestAnimationFrame|mozRequestAnimationFrame||float|cssFloat|unknown|jToggleClass|windows|xda|getInnerSize|jGetFullScroll|clientTop|wap|xiino|ActiveXObject|taintEnabled|jGetStyles|WebKitPoint|mozInnerScreenY|getBoxObjectFor|msRequestAnimationFrame|oCancelAnimationFrame|cancel|requestFullscreen|ExitFullscreen|CancelFullScreen|webkitTransitionEnd|MSFullscreenChange|RequestFullScreen|RequestFullscreen|webkitIsFullScreen|FullScreen|FullscreenElement|fullscreenElement|536|fullscreenchange|WebKitTransitionEvent|activeElement|9999|webkitCancelRequestAnimationFrame|Width|msCancelAnimationFrame|prefix|fullscreenerror|cssfilters|TransitionEvent|MSFullscreenError|2px|red|clientLeft||||offsetParent|srcElement|maemo|pointerover|MSPointerOver|lge|fromElement||mmp||netfront|ixi|re|os|palm|ob|kindle|iris|bada|pointerout|avantgo|MSPointerOut|meego|toElement|blackberry|blazer|hiptop|iemobile|fennec|elaine|compal|plucker|scrollHeight|link|hasChild|jGetStyle|jSetStyle|up|iframe|replaceChild|enclose|jGetRect|html|changeContent|innerText|vodafone|DOMElement|treo|pageXOffset|clientHeight|pageYOffset|jGetFullSize|scrollWidth|pocket|psp|symbian|presto925|series|innerWidth|innerHeight|isTouchEvent|HTMLImageElement|beforeunload|unload|readystatechange|customEventsAllowed|cloneEvents|submit|select|keypress|keyup|focus|change|jCopyEvents|form|circle_06|circle_05|pinchstart|circle_08|ShowItems|circle_04|circle_03|active|Loading|circle_01|circle_02|selectend|selectstart|MessageBox|MagicToolboxTooltip|5000|NEGATIVE_INFINITY|POSITIVE_INFINITY|bounceOut|elasticOut|cubicIn|cubicOut|backIn|backOut|JSON|parse|isset|substring|contextmenu|DOMMouseScroll|getJSON|parameter|Incorrect|definition|of|the|unshift|count|location|image|hash|originwidth|originheight|loading|original|100000000|CoverFlow|Cannot|calculate|disabled|continuousMove|v2|checkEffect|refresh|running|getInstance|swap|sizing|cssId|before|additionalTags|270|01|_coverFlow|matrix3d|exit|matrix|0001|255|enter|_|white|space|nowrap|bar|getVisibleItems|font|translateY|864|coverFlow|350|translateX|fontSize|drawImage|getImageData|putImageData|reflection|quadOut|circle_07|onwheel|000244140625|easeInOutQuad|temporary|085|wheel|total|lengthComputable|webkitURL|URL|455|515|easeInQuart|895|685|easeOutQuart|645|easeInOutCubic|955|055|675|215|445|quadIn|normal|GET|roundCss|setTransition|Infinity|responseType|10000|send|blob|naturalHeight|open||curFrame|575|static|565|easeInOutSine|715|745|clearInterval|createObjectURL|537|response|deltaZ|deltaFactor|275|885|735|easeInOutBack|035|04|795|135|785|easeOutCirc|335|easeInCirc|easeInOutQuart|easeInOutExpo|easeInOutCirc|075|07|265|855|06|000001|sineOut|expoIn|expoOut|easeInQuint|755|delta|easeOutQuint|pinchupdate|sineIn|easeInOutQuint'.split('|'),0,{}))

/*


   Magic Zoom Plus v5.0.8 
   Copyright 2015 Magic Toolbox
   Buy a license: https://www.magictoolbox.com/magiczoomplus/
   License agreement: https://www.magictoolbox.com/license/


*/
eval(function(m,a,g,i,c,k){c=function(e){return(e<a?'':c(parseInt(e/a)))+((e=e%a)>35?String.fromCharCode(e+29):e.toString(36))};if(!''.replace(/^/,String)){while(g--){k[c(g)]=i[g]||c(g)}i=[function(e){return k[e]}];c=function(){return'\\w+'};g=1};while(g--){if(i[g]){m=m.replace(new RegExp('\\b'+c(g)+'\\b','g'),i[g])}}return m}('1j.8P=(17(){1c w,y;w=y=(17(){1c S={4q:"fx.3-b3-8-fy",eC:0,7J:{},$aH:17(W){1a(W.$5U||(W.$5U=++M.eC))},9v:17(W){1a(M.7J[W]||(M.7J[W]={}))},$F:17(){},$1n:17(){1a 1n},$1r:17(){1a 1r},es:"dD-"+1o.6e(1o.6O()*1t a5().eo()),3o:17(W){1a(2D!=W)},aF:17(X,W){1a(2D!=X)?X:W},8X:17(W){1a!!(W)},1P:17(W){if(!M.3o(W)){1a 1n}if(W.$4G){1a W.$4G}if(!!W.5M){if(1==W.5M){1a"5Z"}if(3==W.5M){1a"ek"}}if(W.1I&&W.eK){1a"fb"}if(W.1I&&W.9N){1a"29"}if((W 5g 1j.eM||W 5g 1j.bC)&&W.57===M.3y){1a"3M"}if(W 5g 1j.69){1a"4b"}if(W 5g 1j.bC){1a"17"}if(W 5g 1j.5W){1a"1O"}if(M.1e.4K){if(M.3o(W.cd)){1a"1z"}}1k{if(W===1j.1z||W.57==1j.1u||W.57==1j.eb||W.57==1j.dJ||W.57==1j.fj||W.57==1j.fh){1a"1z"}}if(W 5g 1j.a5){1a"eP"}if(W 5g 1j.dd){1a"fe"}if(W===1j){1a"1j"}if(W===1m){1a"1m"}1a 93(W)},1X:17(ab,aa){if(!(ab 5g 1j.69)){ab=[ab]}if(!aa){1a ab[0]}1S(1c Z=0,X=ab.1I;Z<X;Z++){if(!M.3o(ab)){7G}1S(1c Y in aa){if(!eM.2w.41.2b(aa,Y)){7G}3x{ab[Z][Y]=aa[Y]}3R(W){}}}1a ab[0]},9U:17(aa,Z){if(!(aa 5g 1j.69)){aa=[aa]}1S(1c Y=0,W=aa.1I;Y<W;Y++){if(!M.3o(aa[Y])){7G}if(!aa[Y].2w){7G}1S(1c X in(Z||{})){if(!aa[Y].2w[X]){aa[Y].2w[X]=Z[X]}}}1a aa[0]},em:17(Y,X){if(!M.3o(Y)){1a Y}1S(1c W in(X||{})){if(!Y[W]){Y[W]=X[W]}}1a Y},$3x:17(){1S(1c X=0,W=29.1I;X<W;X++){3x{1a 29[X]()}3R(Y){}}1a 1h},$A:17(Y){if(!M.3o(Y)){1a M.$([])}if(Y.eL){1a M.$(Y.eL())}if(Y.eK){1c X=Y.1I||0,W=1t 69(X);5l(X--){W[X]=Y[X]}1a M.$(W)}1a M.$(69.2w.ao.2b(Y))},6q:17(){1a 1t a5().eo()},4a:17(aa){1c Y;4r(M.1P(aa)){1B"8O":Y={};1S(1c Z in aa){Y[Z]=M.4a(aa[Z])}1G;1B"4b":Y=[];1S(1c X=0,W=aa.1I;X<W;X++){Y[X]=M.4a(aa[X])}1G;1R:1a aa}1a M.$(Y)},$:17(Y){1c W=1r;if(!M.3o(Y)){1a 1h}if(Y.$aL){1a Y}4r(M.1P(Y)){1B"4b":Y=M.em(Y,M.1X(M.69,{$aL:M.$F}));Y.36=Y.ds;1a Y;1G;1B"1O":1c X=1m.cW(Y);if(M.3o(X)){1a M.$(X)}1a 1h;1G;1B"1j":1B"1m":M.$aH(Y);Y=M.1X(Y,M.3v);1G;1B"5Z":M.$aH(Y);Y=M.1X(Y,M.3Y);1G;1B"1z":Y=M.1X(Y,M.1u);1G;1B"ek":1B"17":1B"4b":1B"eP":1R:W=1n;1G}if(W){1a M.1X(Y,{$aL:M.$F})}1k{1a Y}},$1t:17(W,Y,X){1a M.$(M.cv.7L(W)).bK(Y||{}).1x(X||{})},76:17(X,Z,ad){1c aa,Y,ab,ac=[],W=-1;ad||(ad=M.es);aa=M.$(ad)||M.$1t("2l",{id:ad,1y:"9T/6M"}).1Z((1m.fO||1m.3H),"1H");Y=aa.f8||aa.eV;if("1O"!=M.1P(Z)){1S(1c ab in Z){ac.38(ab+":"+Z[ab])}Z=ac.6Y(";")}if(Y.eO){W=Y.eO(X+" {"+Z+"}",Y.fL.1I)}1k{W=Y.fP(X,Z)}1a W},fE:17(Z,W){1c Y,X;Y=M.$(Z);if("5Z"!==M.1P(Y)){1a}X=Y.f8||Y.eV;if(X.f2){X.f2(W)}1k{if(X.eY){X.eY(W)}}},fX:17(){1a"fd-fv-fq-fi-fo".4v(/[g7]/g,17(Y){1c X=1o.6O()*16|0,W=Y=="x"?X:(X&3|8);1a W.8p(16)}).9b()},6b:(17(){1c W;1a 17(X){if(!W){W=1m.7L("a")}W.3T("6K",X);1a("!!"+W.6K).4v("!!","")}})(),fY:17(Y){1c Z=0,W=Y.1I;1S(1c X=0;X<W;++X){Z=31*Z+Y.ex(X);Z%=fV}1a Z}};1c M=S;1c N=S.$;if(!1j.dG){1j.dG=S;1j.$dD=S.$}M.69={$4G:"4b",6a:17(Z,aa){1c W=13.1I;1S(1c X=13.1I,Y=(aa<0)?1o.1V(0,X+aa):aa||0;Y<X;Y++){if(13[Y]===Z){1a Y}}1a-1},5F:17(W,X){1a 13.6a(W,X)!=-1},ds:17(W,Z){1S(1c Y=0,X=13.1I;Y<X;Y++){if(Y in 13){W.2b(Z,13[Y],Y,13)}}},2V:17(W,ab){1c aa=[];1S(1c Z=0,X=13.1I;Z<X;Z++){if(Z in 13){1c Y=13[Z];if(W.2b(ab,13[Z],Z,13)){aa.38(Y)}}}1a aa},fJ:17(W,aa){1c Z=[];1S(1c Y=0,X=13.1I;Y<X;Y++){if(Y in 13){Z[Y]=W.2b(aa,13[Y],Y,13)}}1a Z}};M.9U(5W,{$4G:"1O",4X:17(){1a 13.4v(/^\\s+|\\s+$/g,"")},eq:17(W,X){1a(X||1n)?(13.8p()===W.8p()):(13.5u().8p()===W.5u().8p())},5A:17(){1a 13.4v(/-\\D/g,17(W){1a W.9g(1).9b()})},9Z:17(){1a 13.4v(/[A-Z]/g,17(W){1a("-"+W.9g(0).5u())})},fU:17(W){1a 5D(13,W||10)},fM:17(){1a 2F(13)},cS:17(){1a!13.4v(/1r/i,"").4X()},4F:17(X,W){W=W||"";1a(W+13+W).6a(W+X+W)>-1}});S.9U(bC,{$4G:"17",1E:17(){1c X=M.$A(29),W=13,Y=X.6Q();1a 17(){1a W.6i(Y||1h,X.5E(M.$A(29)))}},2E:17(){1c X=M.$A(29),W=13,Y=X.6Q();1a 17(Z){1a W.6i(Y||1h,M.$([Z||(M.1e.2n?1j.1z:1h)]).5E(X))}},2y:17(){1c X=M.$A(29),W=13,Y=X.6Q();1a 1j.4D(17(){1a W.6i(W,X)},Y||0)},cB:17(){1c X=M.$A(29),W=13;1a 17(){1a W.2y.6i(W,X)}},c7:17(){1c X=M.$A(29),W=13,Y=X.6Q();1a 1j.f6(17(){1a W.6i(W,X)},Y||0)}});1c T={},L=2H.fN.5u(),K=L.3s(/(3W|5K|4K|bF)\\/(\\d+\\.?\\d*)/i),P=L.3s(/(fQ|ap)\\/(\\d+\\.?\\d*)/i)||L.3s(/(eu|4l|8L|dV|6h|ap)\\/(\\d+\\.?\\d*)/i),R=L.3s(/4q\\/(\\d+\\.?\\d*)/i),G=1m.6k.2l;17 H(X){1c W=X.9g(0).9b()+X.ao(1);1a X in G||("er"+W)in G||("ev"+W)in G||("7D"+W)in G||("O"+W)in G}M.1e={2L:{fK:!!(1m.fD),fC:!!(1j.et),bl:!!(1m.ei),4Z:!!(1m.fF||1m.fG||1m.9K||1m.eN||1m.fI||1m.fH||1m.fR||1m.fS||1m.g2),cl:!!(1j.g1)&&!!(1j.g3)&&(1j.8K&&"g6"in 1t 8K),1Y:H("1Y"),2g:H("2g"),8V:H("8V"),ef:H("ef"),4Y:1n,eW:1n,8q:1n,75:(17(){1a 1m.g0.fZ("bU://bV.b1.b8/fB/fT/fW#f9","1.1")})()},cY:17(){1a"fa"in 1j||(1j.dY&&1m 5g dY)}(),3t:L.3s(/(6y|bb\\d+|fu).+|fp|fs\\/|fr|fw|fm|ft|fA|fc|fg|ip(dr|df|ad)|ff|hQ|hj |hi|hh|hf|3t.+dV|hg|6h m(hk|in)i|hp( ho)?|e9|p(hn|hd)\\/|h1|h2|h9|hq(4|6)0|g8|hr|hL\\.(1e|43)|hP|hN|hM (ce|e9)|hw|hs/)?1r:1n,7n:(K&&K[1])?K[1].5u():(1j.6h)?"bF":!!(1j.h0)?"4K":(2D!==1m.gp||1h!=1j.go)?"5K":(1h!==1j.gt||!2H.gx)?"3W":"gw",4q:(K&&K[2])?2F(K[2]):0,3B:(P&&P[1])?P[1].5u():"",7l:(P&&P[2])?2F(P[2]):0,8a:"",aw:"",4W:"",2n:0,4N:L.3s(/ip(?:ad|df|dr)/)?"8R":(L.3s(/(?:gv|6y)/)||2H.4N.3s(/gm|8Y|gl/i)||["gd"])[0].5u(),ej:1m.8U&&"dI"==1m.8U.5u(),f1:0,49:17(){1a(1m.8U&&"dI"==1m.8U.5u())?1m.3H:1m.6k},4Y:1j.4Y||1j.gc||1j.gb||1j.g9||1j.ge||2D,9s:1j.9s||1j.eU||1j.eU||1j.gf||1j.gk||1j.gj||2D,2i:1n,7p:17(){if(M.1e.2i){1a}1c Z,Y;M.1e.2i=1r;M.3H=M.$(1m.3H);M.8Y=M.$(1j);3x{1c X=M.$1t("2Y").1x({1f:2N,1g:2N,5d:"5O",2e:"5C",1H:-gi}).1Z(1m.3H);M.1e.f1=X.d8-X.cZ;X.2P()}3R(W){}3x{Z=M.$1t("2Y");Y=Z.2l;Y.eQ="eR:2d(a8://),2d(a8://),gg 2d(a8://)";M.1e.2L.eW=(/(2d\\s*\\(.*?){3}/).3e(Y.eR);Y=1h;Z=1h}3R(W){}if(!M.1e.7v){M.1e.7v=M.9Q("2g").9Z()}3x{Z=M.$1t("2Y");Z.2l.eQ=M.9Q("2V").9Z()+":5e(gh);";M.1e.2L.8q=!!Z.2l.1I&&(!M.1e.2n||M.1e.2n>9);Z=1h}3R(W){}if(!M.1e.2L.8q){M.$(1m.6k).1A("6E-gz-3p")}if(2D===1j.gS&&2D!==1j.gQ){T.2S="gO"}M.3v.2U.2b(M.$(1m),"9z")}};(17(){1c aa=[],Z,Y,X;17 W(){1a!!(29.9N.bT)}4r(M.1e.7n){1B"4K":if(!M.1e.4q){M.1e.4q=!!(1j.8K)?3:2}1G;1B"5K":M.1e.4q=(P&&P[2])?2F(P[2]):0;1G}M.1e[M.1e.7n]=1r;if(P&&"eu"===P[1]){M.1e.3B="4l"}if(!!1j.4l){M.1e.4l=1r}if(P&&"ap"===P[1]){M.1e.3B="6h";M.1e.6h=1r}if("8L"===M.1e.3B&&(R&&R[1])){M.1e.7l=2F(R[1])}if("6y"==M.1e.4N&&M.1e.3W&&(R&&R[1])){M.1e.6u=1r}Z=({5K:["-ew-","ev","ew"],3W:["-3W-","er","3W"],4K:["-7D-","7D","7D"],bF:["-o-","O","o"]})[M.1e.7n]||["","",""];M.1e.8a=Z[0];M.1e.aw=Z[1];M.1e.4W=Z[2];M.1e.2n=(!M.1e.4K)?2D:(1m.ep)?1m.ep:17(){1c ab=0;if(M.1e.ej){1a 5}4r(M.1e.4q){1B 2:ab=6;1G;1B 3:ab=7;1G}1a ab}();aa.38(M.1e.4N+"-3p");if(M.1e.3t){aa.38("3t-3p")}if(M.1e.6u){aa.38("6y-1e-3p")}if(M.1e.2n){M.1e.3B="ie";M.1e.7l=M.1e.2n;aa.38("ie"+M.1e.2n+"-3p");1S(Y=11;Y>M.1e.2n;Y--){aa.38("gY-ie"+Y+"-3p")}}if(M.1e.3W&&M.1e.4q<gX){M.1e.2L.4Z=1n}if(M.1e.4Y){M.1e.4Y.2b(1j,17(){M.1e.2L.4Y=1r})}if(M.1e.2L.75){aa.38("75-3p")}1k{aa.38("6E-75-3p")}X=(1m.6k.5L||"").3s(/\\S+/g)||[];1m.6k.5L=M.$(X).5E(aa).6Y(" ");if(M.1e.2n&&M.1e.2n<9){1m.7L("8c");1m.7L("dE")}})();(17(){M.1e.4Z={9w:M.1e.2L.4Z,5p:17(){1a!!(1m.gW||1m[M.1e.4W+"gV"]||1m.4Z||1m.gN||1m[M.1e.4W+"gM"])},bH:17(W,X){X||(X={});if(13.9w){M.$(1m).1C(13.aT,13.ez=17(Y){if(13.5p()){X.bE&&X.bE()}1k{M.$(1m).1N(13.aT,13.ez);X.bB&&X.bB()}}.2E(13));M.$(1m).1C(13.b2,13.5S=17(Y){X.8y&&X.8y();M.$(1m).1N(13.b2,13.5S)}.2E(13));(W[M.1e.4W+"gE"]||W[M.1e.4W+"gD"]||W.gC||17(){}).2b(W)}1k{if(X.8y){X.8y()}}},dL:(1m.9K||1m.eN||1m[M.1e.4W+"gB"]||1m[M.1e.4W+"gF"]||17(){}).1E(1m),aT:1m.eE?"gG":(1m.9K?"":M.1e.4W)+"gK",b2:1m.eE?"gH":(1m.9K?"":M.1e.4W)+"gI",gJ:M.1e.4W,gL:1h}})();1c V=/\\S+/g,J=/^(3G(c2|c0|bY|d9)gA)|((78|7X)(c2|c0|bY|d9))$/,O={"gU":("2D"===93(G.cO))?"gT":"cO"},Q={dH:1r,gP:1r,2r:1r,dF:1r,1l:1r},I=(1j.cP)?17(Y,W){1c X=1j.cP(Y,1h);1a X?X.gR(W)||X[W]:1h}:17(Z,X){1c Y=Z.gy,W=1h;W=Y?Y[X]:1h;if(1h==W&&Z.2l&&Z.2l[X]){W=Z.2l[X]}1a W};17 U(Y){1c W,X;X=(M.1e.3W&&"2V"==Y)?1n:(Y in G);if(!X){W=M.1e.aw+Y.9g(0).9b()+Y.ao(1);if(W in G){1a W}}1a Y}M.9Q=U;M.3Y={cG:17(W){1a!(W||"").4F(" ")&&(13.5L||"").4F(W," ")},1A:17(aa){1c X=(13.5L||"").3s(V)||[],Z=(aa||"").3s(V)||[],W=Z.1I,Y=0;1S(;Y<W;Y++){if(!M.$(X).5F(Z[Y])){X.38(Z[Y])}}13.5L=X.6Y(" ");1a 13},1T:17(ab){1c X=(13.5L||"").3s(V)||[],aa=(ab||"").3s(V)||[],W=aa.1I,Z=0,Y;1S(;Z<W;Z++){if((Y=M.$(X).6a(aa[Z]))>-1){X.9X(Y,1)}}13.5L=ab?X.6Y(" "):"";1a 13},ga:17(W){1a 13.cG(W)?13.1T(W):13.1A(W)},3K:17(X){1c Y=X.5A(),W=1h;X=O[Y]||(O[Y]=U(Y));W=I(13,X);if("2t"===W){W=1h}if(1h!==W){if("2r"==X){1a M.3o(W)?2F(W):1}if(J.3e(X)){W=5D(W,10)?W:"5Q"}}1a W},3F:17(X,W){1c Z=X.5A();3x{if("2r"==X){13.cH(W);1a 13}X=O[Z]||(O[Z]=U(Z));13.2l[X]=W+(("63"==M.1P(W)&&!Q[Z])?"2v":"")}3R(Y){}1a 13},1x:17(X){1S(1c W in X){13.3F(W,X[W])}1a 13},gu:17(){1c W={};M.$A(29).36(17(X){W[X]=13.3K(X)},13);1a W},cH:17(Y,W){1c X;W=W||1n;13.2l.2r=Y;Y=5D(2F(Y)*2N);if(W){if(0===Y){if("3f"!=13.2l.5a){13.2l.5a="3f"}}1k{if("6n"!=13.2l.5a){13.2l.5a="6n"}}}if(M.1e.2n&&M.1e.2n<9){if(!9x(Y)){if(!~13.2l.2V.6a("bw")){13.2l.2V+=" cK:cT.d4.bw(8B="+Y+")"}1k{13.2l.2V=13.2l.2V.4v(/8B=\\d*/i,"8B="+Y)}}1k{13.2l.2V=13.2l.2V.4v(/cK:cT.d4.bw\\(8B=\\d*\\)/i,"").4X();if(""===13.2l.2V){13.2l.5B("2V")}}}1a 13},bK:17(W){1S(1c X in W){if("3M"===X){13.1A(""+W[X])}1k{13.3T(X,""+W[X])}}1a 13},4i:17(){1a 13.1x({7A:"3k",5a:"3f"})},5q:17(){1a 13.1x({7A:"",5a:"6n"})},1F:17(){1a{1f:13.d8,1g:13.gs}},8u:17(X){1c W=13.1F();W.1f-=(2F(13.3K("3G-1M-1f")||0)+2F(13.3K("3G-2R-1f")||0));W.1g-=(2F(13.3K("3G-1H-1f")||0)+2F(13.3K("3G-2Q-1f")||0));if(!X){W.1f-=(2F(13.3K("78-1M")||0)+2F(13.3K("78-2R")||0));W.1g-=(2F(13.3K("78-1H")||0)+2F(13.3K("78-2Q")||0))}1a W},6X:17(){1a{1H:13.7W,1M:13.80}},gn:17(){1c W=13,X={1H:0,1M:0};do{X.1M+=W.80||0;X.1H+=W.7W||0;W=W.4H}5l(W);1a X},8b:17(){1c aa=13,X=0,Z=0;if(M.3o(1m.6k.7I)){1c W=13.7I(),Y=M.$(1m).6X(),ab=M.1e.49();1a{1H:W.1H+Y.y-ab.gq,1M:W.1M+Y.x-ab.gr}}do{X+=aa.gZ||0;Z+=aa.hz||0;aa=aa.hA}5l(aa&&!(/^(?:3H|hB)$/i).3e(aa.a9));1a{1H:Z,1M:X}},7S:17(){1c X=13.8b();1c W=13.1F();1a{1H:X.1H,2Q:X.1H+W.1g,1M:X.1M,2R:X.1M+W.1f}},5k:17(X){3x{13.hC=X}3R(W){13.hy=X}1a 13},2P:17(){1a(13.4H)?13.4H.ak(13):13},5i:17(){M.$A(13.hx).36(17(W){if(3==W.5M||8==W.5M){1a}M.$(W).5i()});13.2P();13.bo();if(13.$5U){M.7J[13.$5U]=1h;4P M.7J[13.$5U]}1a 1h},3n:17(Y,X){X=X||"2Q";1c W=13.4h;("1H"==X&&W)?13.ht(Y,W):13.b5(Y);1a 13},1Z:17(Y,X){1c W=M.$(Y).3n(13,X);1a 13},eg:17(W){13.3n(W.4H.b0(13,W));1a 13},9O:17(W){if("5Z"!==M.1P("1O"==M.1P(W)?W=1m.cW(W):W)){1a 1n}1a(13==W)?1n:(13.5F&&!(M.1e.d0))?(13.5F(W)):(13.cX)?!!(13.cX(W)&16):M.$A(13.7T(W.a9)).5F(W)}};M.3Y.hu=M.3Y.3K;M.3Y.hv=M.3Y.1x;if(!1j.3Y){1j.3Y=M.$F;if(M.1e.7n.3W){1j.1m.7L("hD")}1j.3Y.2w=(M.1e.7n.3W)?1j["[[hE.2w]]"]:{}}M.9U(1j.3Y,{$4G:"5Z"});M.3v={1F:17(){if(M.1e.cY||M.1e.hO||M.1e.d0){1a{1f:1j.5f,1g:1j.4J}}1a{1f:M.1e.49().cZ,1g:M.1e.49().hK}},6X:17(){1a{x:1j.hG||M.1e.49().80,y:1j.hF||M.1e.49().7W}},hH:17(){1c W=13.1F();1a{1f:1o.1V(M.1e.49().hI,W.1f),1g:1o.1V(M.1e.49().hJ,W.1g)}}};M.1X(1m,{$4G:"1m"});M.1X(1j,{$4G:"1j"});M.1X([M.3Y,M.3v],{26:17(Z,X){1c W=M.9v(13.$5U),Y=W[Z];if(2D!==X&&2D===Y){Y=W[Z]=X}1a(M.3o(Y)?Y:1h)},34:17(Y,X){1c W=M.9v(13.$5U);W[Y]=X;1a 13},2X:17(X){1c W=M.9v(13.$5U);4P W[X];1a 13}});if(!(1j.aS&&1j.aS.2w&&1j.aS.2w.bj)){M.1X([M.3Y,M.3v],{bj:17(W){1a M.$A(13.9p("*")).2V(17(Y){3x{1a(1==Y.5M&&Y.5L.4F(W," "))}3R(X){}})}})}M.1X([M.3Y,M.3v],{9u:17(){1a 13.bj(29[0])},7T:17(){1a 13.9p(29[0])}});if(M.1e.4Z.9w&&!1m.cD){M.3Y.cD=17(){M.1e.4Z.bH(13)}}M.1u={$4G:"1z",62:M.$1n,2a:17(){1a 13.5c().3z()},5c:17(){if(13.cC){13.cC()}1k{13.cd=1r}1a 13},3z:17(){if(13.ch){13.ch()}1k{13.h8=1n}1a 13},4I:17(){13.62=M.$1r;1a 13},7K:17(){1c X,W;X=((/3A/i).3e(13.1y))?13.2C[0]:13;1a(!M.3o(X))?{x:0,y:0}:{x:X.2O,y:X.2K}},6j:17(){1c X,W;X=((/3A/i).3e(13.1y))?13.2C[0]:13;1a(!M.3o(X))?{x:0,y:0}:{x:X.5n||X.2O+M.1e.49().80,y:X.5o||X.2K+M.1e.49().7W}},bf:17(){1c W=13.4e||13.ha;5l(W&&3==W.5M){W=W.4H}1a W},7Y:17(){1c X=1h;4r(13.1y){1B"7x":1B"hb":1B"hc":X=13.9D||13.h7;1G;1B"8k":1B"ba":1B"ee":X=13.9D||13.h6;1G;1R:1a X}3x{5l(X&&3==X.5M){X=X.4H}}3R(W){X=1h}1a X},6D:17(){if(!13.cj&&13.2o!==2D){1a(13.2o&1?1:(13.2o&2?3:(13.2o&4?2:0)))}1a 13.cj},h3:17(){1a(13.2k&&("3A"===13.2k||13.2k===13.59))||(/3A/i).3e(13.1y)},h4:17(){1a 13.2k?(("3A"===13.2k||13.59===13.2k)&&13.9Y):1===13.2C.1I&&(13.6d.1I?13.6d[0].3D==13.2C[0].3D:1r)}};M.bd="ci";M.bk="h5";M.9q="";if(!1m.ci){M.bd="he";M.bk="hm";M.9q="8f"}M.1u.1w={1y:"",x:1h,y:1h,2I:1h,2o:1h,4e:1h,9D:1h,$4G:"1z.4k",62:M.$1n,5G:M.$([]),4d:17(W){1c X=W;13.5G.38(X)},2a:17(){1a 13.5c().3z()},5c:17(){13.5G.36(17(X){3x{X.5c()}3R(W){}});1a 13},3z:17(){13.5G.36(17(X){3x{X.3z()}3R(W){}});1a 13},4I:17(){13.62=M.$1r;1a 13},7K:17(){1a{x:13.2O,y:13.2K}},6j:17(){1a{x:13.x,y:13.y}},bf:17(){1a 13.4e},7Y:17(){1a 13.9D},6D:17(){1a 13.2o},e3:17(){1a 13.5G.1I>0?13.5G[0].bf():2D}};M.1X([M.3Y,M.3v],{1C:17(Y,aa,ab,ae){1c ad,W,Z,ac,X;if("1O"==M.1P(Y)){X=Y.7H(" ");if(X.1I>1){Y=X}}if(M.1P(Y)=="4b"){M.$(Y).36(13.1C.2E(13,aa,ab,ae));1a 13}if(!Y||!aa||M.1P(Y)!="1O"||M.1P(aa)!="17"){1a 13}if(Y=="9z"&&M.1e.2i){aa.2b(13);1a 13}Y=T[Y]||Y;ab=5D(ab||50);if(!aa.$9C){aa.$9C=1o.6e(1o.6O()*M.6q())}ad=M.3v.26.2b(13,"8l",{});W=ad[Y];if(!W){ad[Y]=W=M.$([]);Z=13;if(M.1u.1w[Y]){M.1u.1w[Y].1K.5m.2b(13,ae)}1k{W.3j=17(af){af=M.1X(af||1j.e,{$4G:"1z"});M.3v.2U.2b(Z,Y,M.$(af))};13[M.bd](M.9q+Y,W.3j,1n)}}ac={1y:Y,fn:aa,bc:ab,cc:aa.$9C};W.38(ac);W.hl(17(ag,af){1a ag.bc-af.bc});1a 13},1N:17(ac){1c aa=M.3v.26.2b(13,"8l",{}),Y,W,X,ad,ab,Z;ab=29.1I>1?29[1]:-2N;if("1O"==M.1P(ac)){Z=ac.7H(" ");if(Z.1I>1){ac=Z}}if(M.1P(ac)=="4b"){M.$(ac).36(13.1N.2E(13,ab));1a 13}ac=T[ac]||ac;if(!ac||M.1P(ac)!="1O"||!aa||!aa[ac]){1a 13}Y=aa[ac]||[];1S(X=0;X<Y.1I;X++){W=Y[X];if(-2N==ab||!!ab&&ab.$9C===W.cc){ad=Y.9X(X--,1)}}if(0===Y.1I){if(M.1u.1w[ac]){M.1u.1w[ac].1K.2P.2b(13)}1k{13[M.bk](M.9q+ac,Y.3j,1n)}4P aa[ac]}1a 13},2U:17(aa,ac){1c Z=M.3v.26.2b(13,"8l",{}),Y,W,X;aa=T[aa]||aa;if(!aa||M.1P(aa)!="1O"||!Z||!Z[aa]){1a 13}3x{ac=M.1X(ac||{},{1y:aa})}3R(ab){}if(2D===ac.2I){ac.2I=M.6q()}Y=Z[aa]||[];1S(X=0;X<Y.1I&&!(ac.62&&ac.62());X++){Y[X].fn.2b(13,ac)}},bt:17(X,W){1c aa=("9z"==X)?1n:1r,Z=13,Y;X=T[X]||X;if(!aa){M.3v.2U.2b(13,X);1a 13}if(Z===1m&&1m.9M&&!Z.aG){Z=1m.6k}if(1m.9M){Y=1m.9M(X);Y.5T(W,1r,1r)}1k{Y=1m.fz();Y.9R=X}if(1m.9M){Z.aG(Y)}1k{Z.fl("8f"+W,Y)}1a Y},bo:17(){1c X=M.3v.26.2b(13,"8l");if(!X){1a 13}1S(1c W in X){M.3v.1N.2b(13,W)}M.3v.2X.2b(13,"8l");1a 13}});(17(W){if("8j"===1m.8m){1a W.1e.7p.2y(1)}if(W.1e.3W&&W.1e.4q<fk){(17(){(W.$(["2f","8j"]).5F(1m.8m))?W.1e.7p():29.9N.2y(50)})()}1k{if(W.1e.4K&&W.1e.2n<9&&1j==1H){(17(){(W.$3x(17(){W.1e.49().g5("1M");1a 1r}))?W.1e.7p():29.9N.2y(50)})()}1k{W.3v.1C.2b(W.$(1m),"g4",W.1e.7p);W.3v.1C.2b(W.$(1j),"5X",W.1e.7p)}}})(S);M.3y=17(){1c aa=1h,X=M.$A(29);if("3M"==M.1P(X[0])){aa=X.6Q()}1c W=17(){1S(1c ad in 13){13[ad]=M.4a(13[ad])}if(13.57.$3I){13.$3I={};1c af=13.57.$3I;1S(1c ae in af){1c ac=af[ae];4r(M.1P(ac)){1B"17":13.$3I[ae]=M.3y.cy(13,ac);1G;1B"8O":13.$3I[ae]=M.4a(ac);1G;1B"4b":13.$3I[ae]=M.4a(ac);1G}}}1c ab=(13.3J)?13.3J.6i(13,29):13;4P 13.bT;1a ab};if(!W.2w.3J){W.2w.3J=M.$F}if(aa){1c Z=17(){};Z.2w=aa.2w;W.2w=1t Z;W.$3I={};1S(1c Y in aa.2w){W.$3I[Y]=aa.2w[Y]}}1k{W.$3I=1h}W.57=M.3y;W.2w.57=W;M.1X(W.2w,X[0]);M.1X(W,{$4G:"3M"});1a W};S.3y.cy=17(W,X){1a 17(){1c Z=13.bT;1c Y=X.6i(W,29);1a Y}};(17(Z){1c Y=Z.$;1c W=5,X=aZ;Z.1u.1w.1Q=1t Z.3y(Z.1X(Z.1u.1w,{1y:"1Q",3J:17(ac,ab){1c aa=ab.6j();13.x=aa.x;13.y=aa.y;13.2O=ab.2O;13.2K=ab.2K;13.2I=ab.2I;13.2o=ab.6D();13.4e=ac;13.4d(ab)}}));Z.1u.1w.1Q.1K={1v:{7e:X,2o:1},5m:17(aa){13.34("1z:1Q:1v",Z.1X(Z.4a(Z.1u.1w.1Q.1K.1v),aa||{}));13.1C("6B",Z.1u.1w.1Q.1K.3j,1);13.1C("5Y",Z.1u.1w.1Q.1K.3j,1);13.1C("2W",Z.1u.1w.1Q.1K.bz,1);if(Z.1e.4K&&Z.1e.2n<9){13.1C("9a",Z.1u.1w.1Q.1K.3j,1)}},2P:17(){13.1N("6B",Z.1u.1w.1Q.1K.3j);13.1N("5Y",Z.1u.1w.1Q.1K.3j);13.1N("2W",Z.1u.1w.1Q.1K.bz);if(Z.1e.4K&&Z.1e.2n<9){13.1N("9a",Z.1u.1w.1Q.1K.3j)}},bz:17(aa){aa.3z()},3j:17(ad){1c ac,aa,ab;aa=13.26("1z:1Q:1v");if(ad.1y!="9a"&&ad.6D()!=aa.2o){1a}if(13.26("1z:1Q:ax")){13.2X("1z:1Q:ax");1a}if("6B"==ad.1y){ac=1t Z.1u.1w.1Q(13,ad);13.34("1z:1Q:9y",ac)}1k{if("5Y"==ad.1y){ac=13.26("1z:1Q:9y");if(!ac){1a}ab=ad.6j();13.2X("1z:1Q:9y");ac.4d(ad);if(ad.2I-ac.2I<=aa.7e&&1o.8z(1o.4z(ab.x-ac.x,2)+1o.4z(ab.y-ac.y,2))<=W){13.2U("1Q",ac)}1m.2U("5Y",ad)}1k{if(ad.1y=="9a"){ac=1t Z.1u.1w.1Q(13,ad);13.2U("1Q",ac)}}}}}})(S);(17(X){1c W=X.$;X.1u.1w.2J=1t X.3y(X.1X(X.1u.1w,{1y:"2J",2m:"3E",6l:1n,3J:17(ab,aa,Z){1c Y=aa.6j();13.x=Y.x;13.y=Y.y;13.2O=aa.2O;13.2K=aa.2K;13.2I=aa.2I;13.2o=aa.6D();13.4e=ab;13.4d(aa);13.2m=Z}}));X.1u.1w.2J.1K={5m:17(){1c Z=X.1u.1w.2J.1K.cu.2E(13),Y=X.1u.1w.2J.1K.92.2E(13);13.1C("6B",X.1u.1w.2J.1K.bG,1);13.1C("5Y",X.1u.1w.2J.1K.92,1);1m.1C("7E",Z,1);1m.1C("5Y",Y,1);13.34("1z:2J:4t:1m:5s",Z);13.34("1z:2J:4t:1m:7j",Y)},2P:17(){13.1N("6B",X.1u.1w.2J.1K.bG);13.1N("5Y",X.1u.1w.2J.1K.92);W(1m).1N("7E",13.26("1z:2J:4t:1m:5s")||X.$F);W(1m).1N("5Y",13.26("1z:2J:4t:1m:7j")||X.$F);13.2X("1z:2J:4t:1m:5s");13.2X("1z:2J:4t:1m:7j")},bG:17(Z){1c Y;if(1!=Z.6D()){1a}Z.3z();Y=1t X.1u.1w.2J(13,Z,"3E");13.34("1z:2J:3E",Y)},92:17(Z){1c Y;Y=13.26("1z:2J:3E");if(!Y){1a}Z.3z();Y=1t X.1u.1w.2J(13,Z,"9I");13.2X("1z:2J:3E");13.2U("2J",Y)},cu:17(Z){1c Y;Y=13.26("1z:2J:3E");if(!Y){1a}Z.3z();if(!Y.6l){Y.6l=1r;13.2U("2J",Y)}Y=1t X.1u.1w.2J(13,Z,"cs");13.2U("2J",Y)}}})(S);(17(X){1c W=X.$;X.1u.1w.4f=1t X.3y(X.1X(X.1u.1w,{1y:"4f",7d:1n,73:1h,3J:17(aa,Z){1c Y=Z.6j();13.x=Y.x;13.y=Y.y;13.2O=Z.2O;13.2K=Z.2K;13.2I=Z.2I;13.2o=Z.6D();13.4e=aa;13.4d(Z)}}));X.1u.1w.4f.1K={1v:{7e:7s},5m:17(Y){13.34("1z:4f:1v",X.1X(X.4a(X.1u.1w.4f.1K.1v),Y||{}));13.1C("1Q",X.1u.1w.4f.1K.3j,1)},2P:17(){13.1N("1Q",X.1u.1w.4f.1K.3j)},3j:17(aa){1c Z,Y;Z=13.26("1z:4f:1z");Y=13.26("1z:4f:1v");if(!Z){Z=1t X.1u.1w.4f(13,aa);Z.73=4D(17(){Z.7d=1r;aa.62=X.$1n;13.2U("1Q",aa);13.2X("1z:4f:1z")}.1E(13),Y.7e+10);13.34("1z:4f:1z",Z);aa.4I()}1k{3P(Z.73);13.2X("1z:4f:1z");if(!Z.7d){Z.4d(aa);aa.4I().2a();13.2U("4f",Z)}1k{}}}}})(S);(17(ac){1c ab=ac.$;17 W(ad){1a ad.2k?(("3A"===ad.2k||ad.59===ad.2k)&&ad.9Y):1===ad.2C.1I&&(ad.6d.1I?ad.6d[0].3D==ad.2C[0].3D:1r)}17 Y(ad){if(ad.2k){1a("3A"===ad.2k||ad.59===ad.2k)?ad.9J:1h}1k{1a ad.2C[0].3D}}17 Z(ad){if(ad.2k){1a("3A"===ad.2k||ad.59===ad.2k)?ad:1h}1k{1a ad.2C[0]}}ac.1u.1w.21=1t ac.3y(ac.1X(ac.1u.1w,{1y:"21",id:1h,3J:17(ae,ad){1c af=Z(ad);13.id=af.9J||af.3D;13.x=af.5n;13.y=af.5o;13.5n=af.5n;13.5o=af.5o;13.2O=af.2O;13.2K=af.2K;13.2I=ad.2I;13.2o=0;13.4e=ae;13.4d(ad)}}));1c X=10,aa=7s;ac.1u.1w.21.1K={5m:17(ad){13.1C(["51",1j.2H.3b?"7Q":"7R"],ac.1u.1w.21.1K.72,1);13.1C(["5R",1j.2H.3b?"6o":"6v"],ac.1u.1w.21.1K.6T,1);13.1C("2W",ac.1u.1w.21.1K.az,1)},2P:17(){13.1N(["51",1j.2H.3b?"7Q":"7R"],ac.1u.1w.21.1K.72);13.1N(["5R",1j.2H.3b?"6o":"6v"],ac.1u.1w.21.1K.6T);13.1N("2W",ac.1u.1w.21.1K.az)},az:17(ad){ad.3z()},72:17(ad){if(!W(ad)){13.2X("1z:21:1z");1a}13.34("1z:21:1z",1t ac.1u.1w.21(13,ad));13.34("1z:1Q:ax",1r)},6T:17(ag){1c ae=ac.6q(),af=13.26("1z:21:1z"),ad=13.26("1z:21:1v");if(!af||!W(ag)){1a}13.2X("1z:21:1z");if(af.id==Y(ag)&&ag.2I-af.2I<=aa&&1o.8z(1o.4z(Z(ag).5n-af.x,2)+1o.4z(Z(ag).5o-af.y,2))<=X){13.2X("1z:1Q:9y");ag.2a();af.4d(ag);13.2U("21",af)}}}})(S);M.1u.1w.3q=1t M.3y(M.1X(M.1u.1w,{1y:"3q",7d:1n,73:1h,3J:17(X,W){13.x=W.x;13.y=W.y;13.2O=W.2O;13.2K=W.2K;13.2I=W.2I;13.2o=0;13.4e=X;13.4d(W)}}));M.1u.1w.3q.1K={1v:{7e:aZ},5m:17(W){13.34("1z:3q:1v",M.1X(M.4a(M.1u.1w.3q.1K.1v),W||{}));13.1C("21",M.1u.1w.3q.1K.3j,1)},2P:17(){13.1N("21",M.1u.1w.3q.1K.3j)},3j:17(Y){1c X,W;X=13.26("1z:3q:1z");W=13.26("1z:3q:1v");if(!X){X=1t M.1u.1w.3q(13,Y);X.73=4D(17(){X.7d=1r;Y.62=M.$1n;13.2U("21",Y)}.1E(13),W.7e+10);13.34("1z:3q:1z",X);Y.4I()}1k{3P(X.73);13.2X("1z:3q:1z");if(!X.7d){X.4d(Y);Y.4I().2a();13.2U("3q",X)}1k{}}}};(17(ab){1c aa=ab.$;17 W(ac){1a ac.2k?(("3A"===ac.2k||ac.59===ac.2k)&&ac.9Y):1===ac.2C.1I&&(ac.6d.1I?ac.6d[0].3D==ac.2C[0].3D:1r)}17 Y(ac){if(ac.2k){1a("3A"===ac.2k||ac.59===ac.2k)?ac.9J:1h}1k{1a ac.2C[0].3D}}17 Z(ac){if(ac.2k){1a("3A"===ac.2k||ac.59===ac.2k)?ac:1h}1k{1a ac.2C[0]}}1c X=10;ab.1u.1w.2p=1t ab.3y(ab.1X(ab.1u.1w,{1y:"2p",2m:"3E",id:1h,6l:1n,3J:17(ae,ad,ac){1c af=Z(ad);13.id=af.9J||af.3D;13.2O=af.2O;13.2K=af.2K;13.5n=af.5n;13.5o=af.5o;13.x=af.5n;13.y=af.5o;13.2I=ad.2I;13.2o=0;13.4e=ae;13.4d(ad);13.2m=ac}}));ab.1u.1w.2p.1K={5m:17(){1c ad=ab.1u.1w.2p.1K.9G.1E(13),ac=ab.1u.1w.2p.1K.6T.1E(13);13.1C(["51",1j.2H.3b?"7Q":"7R"],ab.1u.1w.2p.1K.72,1);13.1C(["5R",1j.2H.3b?"6o":"6v"],ab.1u.1w.2p.1K.6T,1);13.1C(["7N",1j.2H.3b?"6r":"7h"],ab.1u.1w.2p.1K.9G,1);13.34("1z:2p:4t:1m:5s",ad);13.34("1z:2p:4t:1m:7j",ac);aa(1m).1C(1j.2H.3b?"6r":"7h",ad,1);aa(1m).1C(1j.2H.3b?"6o":"6v",ac,1)},2P:17(){13.1N(["51",1j.2H.3b?"7Q":"7R"],ab.1u.1w.2p.1K.72);13.1N(["5R",1j.2H.3b?"6o":"6v"],ab.1u.1w.2p.1K.6T);13.1N(["7N",1j.2H.3b?"6r":"7h"],ab.1u.1w.2p.1K.9G);aa(1m).1N(1j.2H.3b?"6r":"7h",13.26("1z:2p:4t:1m:5s")||ab.$F,1);aa(1m).1N(1j.2H.3b?"6o":"6v",13.26("1z:2p:4t:1m:7j")||ab.$F,1);13.2X("1z:2p:4t:1m:5s");13.2X("1z:2p:4t:1m:7j")},72:17(ad){1c ac;if(!W(ad)){1a}ac=1t ab.1u.1w.2p(13,ad,"3E");13.34("1z:2p:3E",ac)},6T:17(ad){1c ac;ac=13.26("1z:2p:3E");if(!ac||!ac.6l||ac.id!=Y(ad)){1a}ac=1t ab.1u.1w.2p(13,ad,"9I");13.2X("1z:2p:3E");13.2U("2p",ac)},9G:17(ad){1c ac;ac=13.26("1z:2p:3E");if(!ac||!W(ad)){1a}if(ac.id!=Y(ad)){13.2X("1z:2p:3E");1a}if(!ac.6l&&1o.8z(1o.4z(Z(ad).5n-ac.x,2)+1o.4z(Z(ad).5o-ac.y,2))>X){ac.6l=1r;13.2U("2p",ac)}if(!ac.6l){1a}ac=1t ab.1u.1w.2p(13,ad,"cs");13.2U("2p",ac)}}})(S);M.1u.1w.3Z=1t M.3y(M.1X(M.1u.1w,{1y:"3Z",4c:1,a7:1,cq:1,2m:"iH",3J:17(X,W){13.2I=W.2I;13.2o=0;13.4e=X;13.x=W.4o[0].2O+(W.4o[1].2O-W.4o[0].2O)/2;13.y=W.4o[0].2K+(W.4o[1].2K-W.4o[0].2K)/2;13.ct=1o.8z(1o.4z(W.4o[0].2O-W.4o[1].2O,2)+1o.4z(W.4o[0].2K-W.4o[1].2K,2));13.4d(W)},40:17(W){1c X;13.2m="jW";if(W.2C[0].3D!=13.5G[0].4o[0].3D||W.2C[1].3D!=13.5G[0].4o[1].3D){1a}X=1o.8z(1o.4z(W.2C[0].2O-W.2C[1].2O,2)+1o.4z(W.2C[0].2K-W.2C[1].2K,2));13.a7=13.4c;13.4c=X/13.ct;13.cq=13.4c/13.a7;13.x=W.2C[0].2O+(W.2C[1].2O-W.2C[0].2O)/2;13.y=W.2C[0].2K+(W.2C[1].2K-W.2C[0].2K)/2;13.4d(W)}}));M.1u.1w.3Z.1K={5m:17(){13.1C("51",M.1u.1w.3Z.1K.a1,1);13.1C("5R",M.1u.1w.3Z.1K.aJ,1);13.1C("7N",M.1u.1w.3Z.1K.aD,1)},2P:17(){13.1N("51",M.1u.1w.3Z.1K.a1);13.1N("5R",M.1u.1w.3Z.1K.aJ);13.1N("7N",M.1u.1w.3Z.1K.aD)},a1:17(X){1c W;if(X.4o.1I!=2){1a}X.3z();W=1t M.1u.1w.3Z(13,X);13.34("1z:3Z:1z",W)},aJ:17(X){1c W;W=13.26("1z:3Z:1z");if(!W){1a}X.3z();13.2X("1z:3Z:1z")},aD:17(X){1c W;W=13.26("1z:3Z:1z");if(!W){1a}X.3z();W.40(X);13.2U("3Z",W)}};(17(ab){1c Z=ab.$;ab.1u.1w.4x=1t ab.3y(ab.1X(ab.1u.1w,{1y:"4x",3J:17(ah,ag,aj,ad,ac,ai,ae){1c af=ag.6j();13.x=af.x;13.y=af.y;13.2I=ag.2I;13.4e=ah;13.jX=aj||0;13.as=ad||0;13.7V=ac||0;13.jY=ai||0;13.jV=ae||0;13.bD=ag.bD||0;13.aX=1n;13.4d(ag)}}));1c aa,X;17 W(){aa=1h}17 Y(ac,ad){1a(ac>50)||(1===ad&&!("8Y"==ab.1e.4N&&ac<1))||(0===ac%12)||(0==ac%4.jU)}ab.1u.1w.4x.1K={9R:"jQ"in 1m||ab.1e.2n>8?"jR":"jS",5m:17(){13.1C(ab.1u.1w.4x.1K.9R,ab.1u.1w.4x.1K.3j,1)},2P:17(){13.1N(ab.1u.1w.4x.1K.9R,ab.1u.1w.4x.1K.3j,1)},3j:17(ah){1c ai=0,af=0,ad=0,ac=0,ag,ae;if(ah.cp){ad=ah.cp*-1}if(ah.cm!==2D){ad=ah.cm}if(ah.cn!==2D){ad=ah.cn}if(ah.co!==2D){af=ah.co*-1}if(ah.7V){ad=-1*ah.7V}if(ah.as){af=ah.as}if(0===ad&&0===af){1a}ai=0===ad?af:ad;ac=1o.1V(1o.3w(ad),1o.3w(af));if(!aa||ac<aa){aa=ac}ag=ai>0?"6e":"3N";ai=1o[ag](ai/aa);af=1o[ag](af/aa);ad=1o[ag](ad/aa);if(X){3P(X)}X=4D(W,7s);ae=1t ab.1u.1w.4x(13,ah,ai,af,ad,0,aa);ae.aX=Y(aa,ah.bD||0);13.2U("4x",ae)}}})(S);M.8Y=M.$(1j);M.cv=M.$(1m);1a S})();(17(I){if(!I){67"6I 6H 6G"}1c H=I.$;1c G=1j.jT||1j.jZ||1h;w.a6=1t I.3y({24:1h,2i:1n,1v:{9j:I.$F,6c:I.$F,by:I.$F,5S:I.$F,7a:I.$F,cw:I.$F,9P:1n,cA:1r},1D:1h,8i:1h,bu:0,7k:{9j:17(J){if(J.4e&&(7s===J.4e.9W||cx===J.4e.9W)&&J.k0){13.1v.9j.1E(1h,(J.2f-(13.1v.cA?13.bu:0))/J.k7).2y(1);13.bu=J.2f}},6c:17(J){if(J){H(J).2a()}13.8h();if(13.2i){1a}13.2i=1r;13.81();!13.1v.9P&&13.1v.9j.1E(1h,1).2y(1);13.1v.6c.1E(1h,13).2y(1);13.1v.7a.1E(1h,13).2y(1)},by:17(J){if(J){H(J).2a()}13.8h();13.2i=1n;13.81();13.1v.by.1E(1h,13).2y(1);13.1v.7a.1E(1h,13).2y(1)},5S:17(J){if(J){H(J).2a()}13.8h();13.2i=1n;13.81();13.1v.5S.1E(1h,13).2y(1);13.1v.7a.1E(1h,13).2y(1)}},9L:17(){H(["5X","bM","cz"]).36(17(J){13.24.1C(J,13.7k["8f"+J].2E(13).cB(1))},13)},8h:17(){if(13.8i){3x{3P(13.8i)}3R(J){}13.8i=1h}H(["5X","bM","cz"]).36(17(K){13.24.1N(K)},13)},81:17(){13.1F();if(13.24.26("1t")){1c J=13.24.4H;13.24.2P().2X("1t").1x({2e:"k8",1H:"2t"});J.5i()}},ck:17(K){1c L=1t 8K(),J;H(["bM","k9"]).36(17(M){L["8f"+M]=H(17(N){13.7k["8f"+M].2b(13,N)}).1E(13)},13);L.5S=H(17(){13.1v.cw.1E(1h,13).2y(1);13.1v.9P=1n;13.9L();13.24.1U=K}).1E(13);L.6c=H(17(){if(7s!==L.9W&&cx!==L.9W){13.7k.5S.2b(13);1a}J=L.k6;13.9L();if(G&&!I.1e.4K&&!("8R"===I.1e.4N&&I.1e.4q<k5)){13.24.3T("1U",G.k1(J))}1k{13.24.1U=K}}).1E(13);L.a4("k2",K);L.k3="k4";L.jP()},3J:17(K,J){13.1v=I.1X(13.1v,J);13.24=H(K)||I.$1t("24",{},{"1V-1f":"3k","1V-1g":"3k"}).1Z(I.$1t("2Y").1A("3p-at-24").1x({2e:"5C",1H:-dA,1f:10,1g:10,5d:"3f"}).1Z(1m.3H)).34("1t",1r);if(I.1e.2L.cl&&13.1v.9P&&"1O"==I.1P(K)){13.ck(K);1a}1c L=17(){if(13.ca()){13.7k.6c.2b(13)}1k{13.7k.5S.2b(13)}L=1h}.1E(13);13.9L();if("1O"==I.1P(K)){13.24.1U=K}1k{if(I.1e.4K&&5==I.1e.4q&&I.1e.2n<9){13.24.c9=17(){if(/2f|8j/.3e(13.24.8m)){13.24.c9=1h;L&&L()}}.1E(13)}13.24.1U=K.2q("1U")}13.24&&13.24.8j&&L&&(13.8i=L.2y(2N))},jO:17(){13.8h();13.81();13.2i=1n;1a 13},ca:17(){1c J=13.24;1a(J.9r)?(J.9r>0):(J.8m)?("8j"==J.8m):J.1f>0},1F:17(){1a 13.1D||(13.1D={1f:13.24.9r||13.24.1f,1g:13.24.c1||13.24.1g})}})})(w);(17(H){if(!H){67"6I 6H 6G"}if(H.5w){1a}1c G=H.$;H.5w=1t H.3y({3J:17(J,I){1c K;13.el=H.$(J);13.1v=H.1X(13.1v,I);13.5t=1n;13.7f=13.br;K=H.5w.7P[13.1v.1Y]||13.1v.1Y;if("17"===H.1P(K)){13.7f=K}1k{13.5J=13.8w(K)||13.8w("66")}if("1O"==H.1P(13.1v.71)){13.1v.71="jz"===13.1v.71?6m:5D(13.1v.71)||1}},1v:{c3:60,5z:8g,1Y:"66",71:1,4U:"jA",c8:H.$F,7m:H.$F,bR:H.$F,c6:H.$F,9A:1n,jB:1n},4g:1h,5J:1h,7f:1h,jC:17(I){13.1v.1Y=I;I=H.5w.7P[13.1v.1Y]||13.1v.1Y;if("17"===H.1P(I)){13.7f=I}1k{13.7f=13.br;13.5J=13.8w(I)||13.8w("66")}},4V:17(K){1c I=/\\%$/,J;13.4g=K;13.bi=0;13.2m=0;13.jy=0;13.8T={};13.7y="7y"===13.1v.4U||"7y-4p"===13.1v.4U;13.7w="7w"===13.1v.4U||"7w-4p"===13.1v.4U;1S(J in 13.4g){I.3e(13.4g[J][0])&&(13.8T[J]=1r);if("4p"===13.1v.4U||"7y-4p"===13.1v.4U||"7w-4p"===13.1v.4U){13.4g[J].4p()}}13.bm=H.6q();13.c5=13.bm+13.1v.5z;13.1v.c8.2b();if(0===13.1v.5z){13.6p(1);13.1v.7m.2b()}1k{13.9H=13.c4.1E(13);if(!13.1v.9A&&H.1e.2L.4Y){13.5t=H.1e.4Y.2b(1j,13.9H)}1k{13.5t=13.9H.c7(1o.5y(jx/13.1v.c3))}}1a 13},bn:17(){if(13.5t){if(!13.1v.9A&&H.1e.2L.4Y&&H.1e.9s){H.1e.9s.2b(1j,13.5t)}1k{dM(13.5t)}13.5t=1n}},2a:17(I){I=H.3o(I)?I:1n;13.bn();if(I){13.6p(1);13.1v.7m.2y(10)}1a 13},b9:17(K,J,I){K=2F(K);J=2F(J);1a(J-K)*I+K},c4:17(){1c J=H.6q(),I=(J-13.bm)/13.1v.5z,K=1o.6e(I);if(J>=13.c5&&K>=13.1v.71){13.bn();13.6p(1);13.1v.7m.2y(10);1a 13}if(13.7y&&13.bi<K){1S(1c L in 13.4g){13.4g[L].4p()}}13.bi=K;if(!13.1v.9A&&H.1e.2L.4Y){13.5t=H.1e.4Y.2b(1j,13.9H)}13.6p((13.7w?K:0)+13.7f(I%1))},6p:17(I){1c J={},L=I;1S(1c K in 13.4g){if("2r"===K){J[K]=1o.5y(13.b9(13.4g[K][0],13.4g[K][1],I)*2N)/2N}1k{J[K]=13.b9(13.4g[K][0],13.4g[K][1],I);13.8T[K]&&(J[K]+="%")}}13.1v.bR(J,13.el);13.7g(J);13.1v.c6(J,13.el)},7g:17(I){1a 13.el.1x(I)},8w:17(I){1c J,K=1h;if("1O"!==H.1P(I)){1a 1h}4r(I){1B"8D":K=G([0,0,1,1]);1G;1B"66":K=G([0.25,0.1,0.25,1]);1G;1B"66-in":K=G([0.42,0,1,1]);1G;1B"66-cb":K=G([0,0,0.58,1]);1G;1B"66-in-cb":K=G([0.42,0,0.58,1]);1G;1B"d1":K=G([0.47,0,0.jt,0.ju]);1G;1B"d2":K=G([0.39,0.jv,0.kb,1]);1G;1B"jw":K=G([0.jD,0.aQ,0.55,0.95]);1G;1B"d5":K=G([0.55,0.jE,0.68,0.53]);1G;1B"d3":K=G([0.25,0.46,0.45,0.94]);1G;1B"jL":K=G([0.jM,0.cf,0.jN,0.jK]);1G;1B"cU":K=G([0.55,0.jJ,0.jF,0.19]);1G;1B"cJ":K=G([0.jG,0.61,0.cg,1]);1G;1B"jI":K=G([0.ka,0.9V,0.cg,1]);1G;1B"kf":K=G([0.e1,0.cf,0.ed,0.22]);1G;1B"kB":K=G([0.cV,0.84,0.44,1]);1G;1B"kA":K=G([0.77,0,0.88,1]);1G;1B"kz":K=G([0.kC,0.aQ,0.kx,0.ki]);1G;1B"kj":K=G([0.23,1,0.32,1]);1G;1B"kk":K=G([0.86,0,0.kl,1]);1G;1B"d7":K=G([0.95,0.aQ,0.kh,0.kg]);1G;1B"d6":K=G([0.19,1,0.22,1]);1G;1B"kd":K=G([1,0,0,1]);1G;1B"ke":K=G([0.6,0.ky,0.98,0.km]);1G;1B"kn":K=G([0.ku,0.82,0.cV,1]);1G;1B"kw":K=G([0.kt,0.ks,0.15,0.86]);1G;1B"cI":K=G([0.6,-0.28,0.aW,0.9V]);1G;1B"cE":K=G([0.88,0.9c,0.32,1.ay]);1G;1B"kp":K=G([0.68,-0.55,0.kq,1.55]);1G;1R:I=I.4v(/\\s/g,"");if(I.3s(/^4S-4T\\((?:-?[0-9\\.]{0,}[0-9]{1,},){3}(?:-?[0-9\\.]{0,}[0-9]{1,})\\)$/)){K=I.4v(/^4S-4T\\s*\\(|\\)$/g,"").7H(",");1S(J=K.1I-1;J>=0;J--){K[J]=2F(K[J])}}}1a G(K)},br:17(U){1c I=0,T=0,Q=0,V=0,S=0,O=0,P=13.1v.5z;17 N(W){1a((I*W+T)*W+Q)*W}17 M(W){1a((V*W+S)*W+O)*W}17 K(W){1a(3*I*W+2*T)*W+Q}17 R(W){1a 1/(7s*W)}17 J(W,X){1a M(L(W,X))}17 L(ad,ae){1c ac,ab,aa,X,W,Z;17 Y(af){if(af>=0){1a af}1k{1a 0-af}}1S(aa=ad,Z=0;Z<8;Z++){X=N(aa)-ad;if(Y(X)<ae){1a aa}W=K(aa);if(Y(W)<0.be){1G}aa=aa-X/W}ac=0;ab=1;aa=ad;if(aa<ac){1a ac}if(aa>ab){1a ab}5l(ac<ab){X=N(aa);if(Y(X-ad)<ae){1a aa}if(ad>X){ac=aa}1k{ab=aa}aa=(ab-ac)*0.5+ac}1a aa}Q=3*13.5J[0];T=3*(13.5J[2]-13.5J[0])-Q;I=1-Q-T;O=3*13.5J[1];S=3*(13.5J[3]-13.5J[1])-O;V=1-O-S;1a J(U,R(P))}});H.5w.7P={8D:"8D",jr:"d1",ir:"d2",is:"d7",it:"d6",iu:"d5",iq:"d3",io:"cU",ij:"cJ",ik:"cI",il:"cE",cF:17(J,I){I=I||[];1a 1o.4z(2,10*--J)*1o.eB(20*J*1o.eG*(I[0]||1)/3)},im:17(J,I){1a 1-H.5w.7P.cF(1-J,I)},cL:17(K){1S(1c J=0,I=1;1;J+=I,I/=2){if(K>=(7-4*J)/11){1a I*I-1o.4z((11-6*J-11*K)/4,2)}}},iv:17(I){1a 1-H.5w.7P.cL(1-I)},3k:17(I){1a 0}}})(w);(17(H){if(!H){67"6I 6H 6G"}if(H.8E){1a}1c G=H.$;H.8E=1t H.3y(H.5w,{3J:17(I,J){13.an=I;13.1v=H.1X(13.1v,J);13.5t=1n;13.$3I.3J()},4V:17(M){1c I=/\\%$/,L,K,J=M.1I;13.aq=M;13.8W=1t 69(J);1S(K=0;K<J;K++){13.8W[K]={};1S(L in M[K]){I.3e(M[K][L][0])&&(13.8W[K][L]=1r);if("4p"===13.1v.4U||"7y-4p"===13.1v.4U||"7w-4p"===13.1v.4U){13.aq[K][L].4p()}}}13.$3I.4V([]);1a 13},6p:17(I){1S(1c J=0;J<13.an.1I;J++){13.el=H.$(13.an[J]);13.4g=13.aq[J];13.8T=13.8W[J];13.$3I.6p(I)}}})})(w);(17(H){if(!H){67"6I 6H 6G";1a}if(H.ar){1a}1c G=H.$;H.ar=17(J,K){1c I=13.74=H.$1t("2Y",1h,{2e:"5C","z-8J":cM}).1A("iD");H.$(J).1C("7x",17(){I.1Z(1m.3H)});H.$(J).1C("8k",17(){I.2P()});H.$(J).1C("7E",17(P){1c R=20,O=H.$(P).6j(),N=I.1F(),M=H.$(1j).1F(),Q=H.$(1j).6X();17 L(U,S,T){1a(T<(U-S)/2)?T:((T>(U+S)/2)?(T-S):(U-S)/2)}I.1x({1M:Q.x+L(M.1f,N.1f+2*R,O.x-Q.x)+R,1H:Q.y+L(M.1g,N.1g+2*R,O.y-Q.y)+R})});13.9T(K)};H.ar.2w.9T=17(I){13.74.4h&&13.74.ak(13.74.4h);13.74.3n(1m.bx(I))}})(w);(17(H){if(!H){67"6I 6H 6G";1a}if(H.iE){1a}1c G=H.$;H.8M=17(L,K,J,I){13.8G=1h;13.52=H.$1t("bQ",1h,{2e:"5C","z-8J":cM,5a:"3f",2r:0.8}).1A(I||"").1Z(J||1m.3H);13.cR(L);13.5q(K)};H.8M.2w.5q=17(I){13.52.5q();13.8G=13.4i.1E(13).2y(H.aF(I,iF))};H.8M.2w.4i=17(I){3P(13.8G);13.8G=1h;if(13.52&&!13.av){13.av=1t w.5w(13.52,{5z:H.aF(I,eD),7m:17(){13.52.5i();4P 13.52;13.av=1h}.1E(13)}).4V({2r:[13.52.3K("2r"),0]})}};H.8M.2w.cR=17(I){13.52.4h&&13.74.ak(13.52.4h);13.52.3n(1m.bx(I))}})(w);(17(H){if(!H){67"6I 6H 6G"}if(H.7c){1a}1c K=H.$,G=1h,O={"3u":1,4b:2,63:3,"17":4,1O:2N},I={"3u":17(R,Q,P){if("3u"!=H.1P(Q)){if(P||"1O"!=H.1P(Q)){1a 1n}1k{if(!/^(1r|1n)$/.3e(Q)){1a 1n}1k{Q=Q.cS()}}}if(R.41("2u")&&!K(R["2u"]).5F(Q)){1a 1n}G=Q;1a 1r},1O:17(R,Q,P){if("1O"!==H.1P(Q)){1a 1n}1k{if(R.41("2u")&&!K(R["2u"]).5F(Q)){1a 1n}1k{G=""+Q;1a 1r}}},63:17(S,R,Q){1c P=1n,U=/%$/,T=(H.1P(R)=="1O"&&U.3e(R));if(Q&&!"63"==93 R){1a 1n}R=2F(R);if(9x(R)){1a 1n}if(9x(S.7r)){S.7r=cQ.iC}if(9x(S.aC)){S.aC=cQ.js}if(S.41("2u")&&!K(S["2u"]).5F(R)){1a 1n}if(S.7r>R||R>S.aC){1a 1n}G=T?(R+"%"):R;1a 1r},4b:17(S,Q,P){if("1O"===H.1P(Q)){3x{Q=1j.iB.ix(Q)}3R(R){1a 1n}}if(H.1P(Q)==="4b"){G=Q;1a 1r}1k{1a 1n}},"17":17(R,Q,P){if(H.1P(Q)==="17"){G=Q;1a 1r}1k{1a 1n}}},J=17(U,T,Q){1c S;S=U.41("33")?U.33:[U];if("4b"!=H.1P(S)){1a 1n}1S(1c R=0,P=S.1I-1;R<=P;R++){if(I[S[R].1y](S[R],T,Q)){1a 1r}}1a 1n},M=17(U){1c S,R,T,P,Q;if(U.41("33")){P=U.33.1I;1S(S=0;S<P;S++){1S(R=S+1;R<P;R++){if(O[U.33[S]["1y"]]>O[U.33[R].1y]){Q=U.33[S];U.33[S]=U.33[R];U.33[R]=Q}}}}1a U},N=17(S){1c R;R=S.41("33")?S.33:[S];if("4b"!=H.1P(R)){1a 1n}1S(1c Q=R.1I-1;Q>=0;Q--){if(!R[Q].1y||!O.41(R[Q].1y)){1a 1n}if(H.3o(R[Q]["2u"])){if("4b"!==H.1P(R[Q]["2u"])){1a 1n}1S(1c P=R[Q]["2u"].1I-1;P>=0;P--){if(!I[R[Q].1y]({1y:R[Q].1y},R[Q]["2u"][P],1r)){1a 1n}}}}if(S.41("1R")&&!J(S,S["1R"],1r)){1a 1n}1a 1r},L=17(P){13.4B={};13.1v={};13.cN(P)};H.1X(L.2w,{cN:17(R){1c Q,P,S;1S(Q in R){if(!R.41(Q)){7G}P=(Q+"").4X().5A();if(!13.4B.41(P)){13.4B[P]=M(R[Q]);if(!N(13.4B[P])){67"iy iz iA ii \'"+Q+"\' ih in "+R}13.1v[P]=2D}}},7g:17(Q,P){Q=(Q+"").4X().5A();if(H.1P(P)=="1O"){P=P.4X()}if(13.4B.41(Q)){G=P;if(J(13.4B[Q],P)){13.1v[Q]=G}G=1h}},eZ:17(P){P=(P+"").4X().5A();if(13.4B.41(P)){1a H.3o(13.1v[P])?13.1v[P]:13.4B[P]["1R"]}},7M:17(Q){1S(1c P in Q){13.7g(P,Q[P])}},eX:17(){1c Q=H.1X({},13.1v);1S(1c P in Q){if(2D===Q[P]&&2D!==13.4B[P]["1R"]){Q[P]=13.4B[P]["1R"]}}1a Q},8S:17(P){K(P.7H(";")).36(K(17(Q){Q=Q.7H(":");13.7g(Q.6Q().4X(),Q.6Y(":"))}).1E(13))},8X:17(P){P=(P+"").4X().5A();1a 13.4B.41(P)},hY:17(P){P=(P+"").4X().5A();1a 13.8X(P)&&H.3o(13.1v[P])},2P:17(P){P=(P+"").4X().5A();if(13.8X(P)){4P 13.1v[P];4P 13.4B[P]}}});H.7c=L}(w));(17(K){if(!K){67"6I 6H 6G";1a}1c J=K.$;if(K.8Z){1a}1c I="bU://bV.b1.b8/hZ/75",H="bU://bV.b1.b8/i0/i1";1c G=17(L){13.6t={};13.7i=J(L);13.7C=J(1m.9S(I,"75"));13.7C.3T("1f",13.7i.9r||13.7i.1f);13.7C.3T("1g",13.7i.c1||13.7i.1g);13.1i=J(1m.9S(I,"1i"));13.1i.hX(H,"6K",13.7i.2q("1U"));13.1i.3T("1f","2N%");13.1i.3T("1g","2N%");13.1i.1Z(13.7C)};G.2w.6C=17(){1a 13.7C};G.2w.5e=17(L){if(1o.5y(L)<1){1a}if(!13.6t.5e){13.6t.5e=J(1m.9S(I,"2V"));13.6t.5e.3T("id","bX");13.6t.5e.b5(J(1m.9S(I,"hW")).bK({"in":"hS",bZ:L}));13.6t.5e.1Z(13.7C);13.1i.3T("2V","2d(#bX)")}1k{13.6t.5e.4h.3T("bZ",L)}1a 13};K.8Z=G}(w));1c r=(17(I){1c H=I.$;1c G=17(K,J){13.3h={8a:"3p",3r:"7Z",2e:"2Q",1D:{hT:"2v",1f:"2t",1g:"2t"},hU:["1g","1f"]};13.3I=K;13.4u=1h;13.6S=1h;13.2G=1h;13.2M={};13.eF=[];13.5P=1h;13.aK=1h;13.5H=1h;13.3h=I.1X(13.3h,J);13.3g=13.3h.8a+"-aP";13.8n=13.3h.8a+"-6N";13.ec()};G.2w={ec:17(){13.4u=I.$1t("2Y").1A(13.3g).1A(13.3g+"-"+13.3h.3r).1x({5a:"3f"});13.6S=I.$1t("2Y").1A(13.3g+"-6S").1Z(13.4u);13.4u.1Z(13.3I);H(["4C","4y"]).36(17(J){13.2M[J]=I.$1t("2o").1A(13.3g+"-2o").1A(13.3g+"-2o-"+J).1Z(13.4u).1C("1Q 21",(17(L,K){H(L).5G[0].2a().4I();H(L).5c();13.5O(K)}).2E(13,J))}.1E(13));13.2M.4C.1A(13.3g+"-2o-4O");13.2G=I.$1t("hV").1C("1Q 21",17(J){J.2a()})},dS:17(K){1c J=I.$1t("i2").1A(13.8n).3n(K).1Z(13.2G);1t I.a6(K,{7a:13.9B.1E(13)});13.eF.38(J);1a J},dk:17(K){1c J=13.5P||13.2G.9u(13.8n+"-6L")[0];if(J){H(J).1T(13.8n+"-6L")}13.5P=H(K);if(!13.5P){1a}13.5P.1A(13.8n+"-6L");13.5O(13.5P)},bP:17(){if(13.6S!==13.2G.4H){H(13.2G).1Z(13.6S);13.eA();H(1j).1C("7t",13.5H=13.9B.1E(13));13.bP.1E(13).2y(1);1a}1c J=13.3I.1F();if(J.1g>0&&J.1g>J.1f){13.7O("4Q")}1k{13.7O("7Z")}13.9B();13.4u.1x({5a:""})},2a:17(){if(13.5H){H(1j).1N("7t",13.5H)}13.4u.5i()},5O:17(W,M){1c O={x:0,y:0},Z="4Q"==13.3h.3r?"1H":"1M",R="4Q"==13.3h.3r?"1g":"1f",N="4Q"==13.3h.3r?"y":"x",V=13.2G.4H.1F()[R],S=13.2G.4H.8b(),L=13.2G.1F()[R],U,J,Y,P,K,T,Q,X=[];if(13.aK){13.aK.2a()}1k{13.2G.1x("1Y",I.1e.7v+5W.79(32)+"6Z")}if(2D===M){M=8g}U=13.2G.8b();if("1O"==I.1P(W)){O[N]=("4y"==W)?1o.1V(U[Z]-S[Z]-V,V-L):1o.2h(U[Z]-S[Z]+V,0)}1k{if("5Z"==I.1P(W)){J=W.1F();Y=W.8b();O[N]=1o.2h(0,1o.1V(V-L,U[Z]+V/2-Y[Z]-J[R]/2))}1k{1a}}if(I.1e.5K&&"6y"==I.1e.4N||I.1e.2n&&I.1e.2n<10){if("1O"==I.1P(W)&&O[N]==U[Z]-S[Z]){U[Z]+=0===U[Z]-S[Z]?30:-30}O["7X-"+Z]=[((L<=V)?0:(U[Z]-S[Z])),O[N]];4P O.x;4P O.y;if(!13.bA){13.bA=1t I.8E([13.2G],{5z:eD})}X.38(O);13.bA.4V(X);Q=O["7X-"+Z][1]}1k{13.2G.1x({1Y:I.1e.7v+5W.79(32)+M+"7D 66",2g:"4m("+O.x+"2v, "+O.y+"2v, 0)"});Q=O[N]}if(Q>=0){13.2M.4C.1A(13.3g+"-2o-4O")}1k{13.2M.4C.1T(13.3g+"-2o-4O")}if(Q<=V-L){13.2M.4y.1A(13.3g+"-2o-4O")}1k{13.2M.4y.1T(13.3g+"-2o-4O")}Q=1h},eA:17(){1c L,K,M,T,S,V,N,R,Q,U,aa,X,Y,W={x:0,y:0},J,P,O=aZ,Z=17(ad){1c ac,ab=0;1S(ac=1.5;ac<=90;ac+=1.5){ab+=(ad*1o.eB(ac/1o.eG/2))}(T<0)&&(ab*=(-1));1a ab};S=H(17(ab){W={x:0,y:0};J="4Q"==13.3h.3r?"1H":"1M";P="4Q"==13.3h.3r?"1g":"1f";L="4Q"==13.3h.3r?"y":"x";X=13.2G.4H.1F()[P];aa=13.2G.1F()[P];M=X-aa;if(M>=0){1a}if(ab.2m=="3E"){if(2D===Y){Y=0}13.2G.3F("1Y",I.1e.7v+5W.79(32)+"e5");V=ab[L];Q=ab.y;R=ab.x;U=1n}1k{if("9I"==ab.2m){if(U){1a}N=Z(1o.3w(T));Y+=N;(Y<=M)&&(Y=M);(Y>=0)&&(Y=0);W[L]=Y;13.2G.3F("1Y",I.1e.7v+5W.79(32)+O+"7D  4S-4T(.0, .0, .0, 1)");13.2G.3F("2g","4m("+W.x+"2v, "+W.y+"2v, 5Q)");T=0}1k{if(U){1a}if("7Z"==13.3h.3r&&1o.3w(ab.x-R)>1o.3w(ab.y-Q)||"4Q"==13.3h.3r&&1o.3w(ab.x-R)<1o.3w(ab.y-Q)){ab.2a();T=ab[L]-V;Y+=T;W[L]=Y;13.2G.3F("2g","4m("+W.x+"2v, "+W.y+"2v, 5Q)");if(Y>=0){13.2M.4C.1A(13.3g+"-2o-4O")}1k{13.2M.4C.1T(13.3g+"-2o-4O")}if(Y<=M){13.2M.4y.1A(13.3g+"-2o-4O")}1k{13.2M.4y.1T(13.3g+"-2o-4O")}}1k{U=1r}}V=ab[L]}}).1E(13);13.2G.1C("2p",S)},9B:17(){1c M,L,J,K=13.3I.1F();if(K.1g>0&&K.1g>K.1f){13.7O("4Q")}1k{13.7O("7Z")}M="4Q"==13.3h.3r?"1g":"1f";L=13.2G.1F()[M];J=13.4u.1F()[M];if(L<=J){13.4u.1A("6E-2M");13.2G.3F("1Y","").1F();13.2G.3F("2g","4m(0,0,0)");13.2M.4C.1A(13.3g+"-2o-4O");13.2M.4y.1T(13.3g+"-2o-4O")}1k{13.4u.1T("6E-2M")}if(13.5P){13.5O(13.5P,0)}},7O:17(J){if("4Q"!==J&&"7Z"!==J||J==13.3h.3r){1a}13.4u.1T(13.3g+"-"+13.3h.3r);13.3h.3r=J;13.4u.1A(13.3g+"-"+13.3h.3r);13.2G.3F("1Y","3k").1F();13.2G.3F("2g","").3F("7X","")}};1a G})(w);1c h=y.$;if(!y.1e.87){y.1e.87=y.9Q("2g").9Z()}1c o={4A:{1y:"1O","2u":["2W","7z"],"1R":"7z"},3O:{33:[{1y:"1O","2u":["1l","2B","48","3L"],"1R":"1l"},{1y:"3u","2u":[1n]}],"1R":"1l"},eh:{33:[{1y:"1O","2u":["2t"]},{1y:"63",7r:1}],"1R":"2t"},dh:{33:[{1y:"1O","2u":["2t"]},{1y:"63",7r:1}],"1R":"2t"},91:{1y:"1O","1R":"2R"},ib:{1y:"63",7r:0,"1R":15},7F:{33:[{1y:"1O","2u":["2Q","1H","3L"],"1R":"3L"},{1y:"3u","2u":[1n]}],"1R":"3L"},2A:{33:[{1y:"1O","2u":["1j","dq","3L"]},{1y:"3u","2u":[1n]}],"1R":"1j"},65:{33:[{1y:"1O","2u":["1l","2B","3L"],"1R":"1l"},{1y:"3u","2u":[1n]}],"1R":"1l"},3V:{1y:"1O","2u":["2W","3i"],"1R":"2W"},3X:{1y:"3u","1R":1r},e8:{1y:"3u","1R":1r},3a:{33:[{1y:"1O","2u":["aM","3i","3L"]},{1y:"3u","2u":[1n]}],"1R":"aM"},e2:{1y:"3u","1R":1r},e0:{1y:"3u","1R":1n},9f:{1y:"3u","1R":1n},a2:{1y:"3u","1R":1r},dz:{1y:"3u","1R":1n},dp:{1y:"3u","1R":1r},aV:{1y:"1O","2u":["2W","7z"],"1R":"2W"},5x:{1y:"1O"},bO:{1y:"1O","1R":"ic 6F 1l"},8H:{1y:"1O","1R":"eJ 6F 1l"},8Q:{1y:"1O","1R":"eJ 6F 2A"},ig:{1y:"1O","1R":"i9"},i8:{1y:"1O","1R":"i4"},i5:{1y:"1O","1R":"i6"}};1c l={3O:{33:[{1y:"1O","2u":["1l","2B","3L"],"1R":"1l"},{1y:"3u","2u":[1n]}],"1R":"1l"},3V:{1y:"1O","2u":["2W"],"1R":"2W"},8Q:{1y:"1O","1R":"hR 6F 2A"},bO:{1y:"1O","1R":"i7 6F 1l"},8H:{1y:"1O","1R":"iG 21 6F 1l"}};1c n="8P",B="1q",b=20,z=["db","de","dP","dO","e7","dl"];1c t,p={},D=h([]),F,e=1j.jc||1,E,x=1r,f=y.1e.2L.8V?"4m(":"9d(",A=y.1e.2L.8V?",0)":")",m=1h;1c q=(17(){1c H,K,J,I,G;1a G})();17 v(I){1c H,G;H="";1S(G=0;G<I.1I;G++){H+=5W.79(14^I.ex(G))}1a H}17 i(I){1c H=[],G=1h;(I&&(G=h(I)))&&(H=D.2V(17(J){1a J.3U===G}));1a H.1I?H[0]:1h}17 a(I){1c H=h(1j).1F(),G=h(1j).6X();I=I||0;1a{1M:I,2R:H.1f-I,1H:I,2Q:H.1g-I,x:G.x,y:G.y}}17 c(G){1a(G.2k&&("3A"===G.2k||G.2k===G.59))||(/3A/i).3e(G.1y)}17 g(G){1a G.2k?(("3A"===G.2k||G.59===G.2k)&&G.9Y):1===G.2C.1I&&(G.6d.1I?G.6d[0].3D==G.2C[0].3D:1r)}17 s(){1c I=y.$A(29),H=I.6Q(),G=p[H];if(G){1S(1c J=0;J<G.1I;J++){G[J].6i(1h,I)}}}17 C(){1c K=29[0],G,J,H=[];3x{do{J=K.a9;if(/^[A-am-z]*$/.3e(J)){if(G=K.2q("id")){if(/^[A-am-z][-A-am-je-jf]*/.3e(G)){J+="#"+G}}H.38(J)}K=K.4H}5l(K&&K!==1m.6k);H=H.4p();y.76(H.6Y(" ")+"> .1q-8c > 24",{1f:"2N% !2s;"},"1q-et-6M",1r)}3R(I){}}17 u(){1c H=1h,I=1h,G=17(){1j.jb(1m.3H.80,1m.3H.7W);1j.aG(1t 1u("7t"))};I=f6(17(){1c L=1j.3r==90||1j.3r==-90,K=1j.4J,J=(L?f5.ja:f5.j6)*0.85;if((H==1h||H==1n)&&((L&&K<J)||(!L&&K<J))){H=1r;G()}1k{if((H==1h||H==1r)&&((L&&K>J)||(!L&&K>J))){H=1n;G()}}},j7);1a I}17 d(){y.76(".3p-3f-6S, .3p-at-24",{7A:"eS !2s","2h-1g":"0 !2s","2h-1f":"0 !2s","1V-1g":"3k !2s","1V-1f":"3k !2s",1f:"f4 !2s",1g:"f4 !2s",2e:"5C !2s",1H:"-a3 !2s",1M:"0 !2s",5d:"3f !2s","-3W-2g":"3k !2s",2g:"3k !2s","-3W-1Y":"3k !2s",1Y:"3k !2s"},"9o-8C-6M");y.76(".3p-at-24 24",{7A:"dC-eS !2s",3G:"0 !2s",78:"0 !2s","2h-1g":"0 !2s","2h-1f":"0 !2s","1V-1g":"3k !2s","1V-1f":"3k !2s","-3W-2g":"3k !2s",2g:"3k !2s","-3W-1Y":"3k !2s",1Y:"3k !2s"},"9o-8C-6M");if(y.1e.6u){y.76(".3t-3p .1q-2A .1q-2A-bg",{7A:"3k !2s"},"9o-8C-6M")}if(y.1e.6u&&("4l"!==y.1e.3B||44==y.1e.7l)){y.76(".3t-3p .1q-1l-1j.1q-2B, .3t-3p .1q-1l-1j.1q-2B:j8",{"3G-j9":"0 !2s"},"9o-8C-6M")}}1c k=17(J,K,H,I,G){13.1J={1U:1h,2d:1h,64:1,1d:1h,2m:0,1D:{1f:0,1g:0},2f:1n};13.1l={1U:1h,2d:1h,64:1,1d:1h,2m:0,1D:{1f:0,1g:0},2f:1n};if("8O"==y.1P(J)){13.1J=J}1k{if("1O"==y.1P(J)){13.1J.2d=y.6b(J)}}if("8O"==y.1P(K)){13.1l=K}1k{if("1O"==y.1P(K)){13.1l.2d=y.6b(K)}}13.3m=H;13.1v=I;13.4n=G;13.7b=1h;13.43=1h;13.1d=1h};k.2w={9t:17(I,H,G){1c J=I.7T("24")[0];if(G){13.1J.1d=J||y.$1t("24").1Z(I)}if(e>1){13.1J.2d=I.2q("3Q-1i-2x");if(13.1J.2d){13.1J.64=2}13.1l.2d=I.2q("3Q-1l-1i-2x");if(13.1l.2d){13.1l.64=2}}13.1J.1U=I.2q("3Q-1i")||I.2q("jg")||(J?J.2q("1U"):1h);if(13.1J.1U){13.1J.1U=y.6b(13.1J.1U)}13.1J.2d=13.1J.2d||13.1J.1U;if(13.1J.2d){13.1J.2d=y.6b(13.1J.2d)}13.1l.1U=I.2q("3Q-1l-1i")||I.2q("6K");if(13.1l.1U){13.1l.1U=y.6b(13.1l.1U)}13.1l.2d=13.1l.2d||13.1l.1U;if(13.1l.2d){13.1l.2d=y.6b(13.1l.2d)}13.3m=I.2q("3Q-3m")||I.2q("8x")||H;13.43=I.2q("3Q-43");13.4n=I;1a 13},aI:17(G){1c H=1h;if(29.1I>1&&"17"===y.1P(29[1])){H=29[1]}if(0!==13[G].2m){if(13[G].2f){13.6c(H)}1a}if(13[G].2d&&13[G].1d&&!13[G].1d.2q("1U")&&!13[G].1d.2q("jh")){13[G].1d.3T("1U",13[G].2d)}13[G].2m=1;1t y.a6(13[G].1d||13[G].2d,{7a:h(17(I){13[G].2f=1r;13[G].2m=I.2i?2:-1;if(I.2i){13[G].1D=I.1F();if(!13[G].1d){13[G].1d=h(I.24);13[G].1d.2q("2l");13[G].1d.5B("2l");13[G].1D.1f/=13[G].64;13[G].1D.1g/=13[G].64}1k{13[G].1d.1x({"1V-1f":13[G].1D.1f,"1V-1g":13[G].1D.1g});if(13[G].1d.9m&&13[G].1d.9m!=13[G].1d.1U){13[G].2d=13[G].1d.9m}1k{if(y.6b(13[G].1d.2q("1U")||"")!=13[G].2d){13[G].1d.3T("1U",13[G].2d)}}}}13.6c(H)}).1E(13)})},9e:17(){13.aI("1J",29[0])},bJ:17(){13.aI("1l",29[0])},5X:17(){13.7b=1h;if(29.1I>0&&"17"===y.1P(29[0])){13.7b=29[0]}13.9e();13.bJ()},6c:17(G){if(G){G.2b(1h,13)}if(13.7b&&13.1J.2f&&13.1l.2f){13.7b.2b(1h,13);13.7b=1h;1a}},2f:17(){1a(13.1J.2f&&13.1l.2f)},2i:17(){1a(2===13.1J.2m&&2===13.1l.2m)},8v:17(H){1c G="1J"==H?"1l":"1J";if(!13[H].2f||(13[H].2f&&2===13[H].2m)){1a 13[H].2d}1k{if(!13[G].2f||(13[G].2f&&2===13[G].2m)){1a 13[G].2d}1k{1a 1h}}},6C:17(H){1c G="1J"==H?"1l":"1J";if(!13[H].2f||(13[H].2f&&2===13[H].2m)){1a 13[H].1d}1k{if(!13[G].2f||(13[G].2f&&2===13[G].2m)){1a 13[G].1d}1k{1a 1h}}},1F:17(H){1c G="1J"==H?"1l":"1J";if(!13[H].2f||(13[H].2f&&2===13[H].2m)){1a 13[H].1D}1k{if(!13[G].2f||(13[G].2f&&2===13[G].2m)){1a 13[G].1D}1k{1a{1f:0,1g:0}}}},jo:17(H){1c G="1J"==H?"1l":"1J";if(!13[H].2f||(13[H].2f&&2===13[H].2m)){1a 13[H].64}1k{if(!13[G].2f||(13[G].2f&&2===13[G].2m)){1a 13[G].64}1k{1a 1}}},6z:17(G){13.1d=13.6C(G)}};1c j=17(H,G){13.1v=1t y.7c(o);13.1p=h(17(){if(29.1I>1){1a 13.7g(29[0],29[1])}1k{1a 13.eZ(29[0])}}).1E(13.1v);13.f0=1t y.7c(l);13.3C=[];13.1i=1h;13.6P=1h;13.3U=h(H).1C("3E jp 2W",17(I){I.2a()});13.id=1h;13.1d=1h;13.6W=1h;13.9k=1h;13.6x=1h;13.7q={1f:0,1g:0};13.1D={1f:0,1g:0};13.2c={1f:0,1g:0};13.3d={1f:0,1g:0};13.2j={1H:0,1M:0,2Q:0,2R:0};13.2i=1n;13.1L=1n;13.5I=1h;13.aA=1h;13.5H=h(17(){if(13.1L){13.1i.1d.1x({"1V-1g":1o.2h(13.1i.1F("1l").1g,13.6A())});13.1i.1d.1x({"1V-1f":1o.2h(13.1i.1F("1l").1f,13.7B())})}13.bh(29[0])}).1E(13);13.bp=h(17(I){3P(13.aA);13.aA=h(13.5H).2y(10,"5O"===I.1y)}).2E(13);13.1s=1h;13.1b=1h;13.3a=1h;13.bq=1h;13.6U=0;13.bW=1r;13.6g=1h;13.5r=1h;13.6N=1h;13.3c=1h;13.3S=1h;13.3X=1h;13.5h=1h;13.8r=1h;13.4R=1h;13.8t=1h;13.56=1h;13.4j=1h;13.4L=[];13.2M={};13.4V(G)};j.2w={eT:17(G){13.1v.7M(1j[B+"7c"]||{});13.1v.8S(13.3U.2q("3Q-1v")||"");if(y.1e.3t){13.1v.7M(13.f0.eX());13.1v.7M(1j[B+"jq"]||{});13.1v.8S(13.3U.2q("3Q-3t-1v")||"")}if("1O"==y.1P(G)){13.1v.8S(G||"")}1k{13.1v.7M(G||{})}if(13.1p("5x")){13.1p("5x",13.1p("5x").4v(","," "))}if(1n===13.1p("7F")){13.1p("7F","3L")}if(1n===13.1p("3a")){13.1p("3a","3L")}4r(13.1p("3a")){1B"3L":13.6U=0;1G;1B"aM":13.6U=2;1G;1B"3i":13.6U=6m;1G}if("3L"===13.1p("3O")){13.1p("3O",1n)}if("3L"===13.1p("2A")){13.1p("2A",1n)}if("3L"===13.1p("65")){13.1p("65",1n)}if(E){if("1l"==13.1p("3O")){13.1p("91","2z")}}if(y.1e.3t&&"1l"==13.1p("3O")&&"2z"==13.1p("91")){if(13.1p("2A")){13.1p("3O",1n)}1k{13.1p("4A","2W")}}},4V:17(H){1c G;13.eT(H);if(x&&!13.1p("a2")){1a}13.id=13.3U.2q("id")||"1q-"+1o.6e(1o.6O()*y.6q());13.3U.3T("id",13.id);13.1d=y.$1t("8c").1A("1q-8c");C(13.3U);13.6W=13.3U.ei("24");13.9k=13.6W?13.6W.2q("1U"):1h;13.6x=h(13.3U).2q("8x");h(13.3U).5B("8x");13.6P=1t k().9t(13.3U,13.6x,1r);13.1i=13.6P;13.1d.eg(13.1i.1J.1d).1A(13.1p("5x"));if(1r!==13.1p("dz")){13.1d.1C("jn",17(I){I.2a();1a 1n})}13.1d.1A("1q-"+13.1p("4A")+"-1l");if(!13.1p("2A")){13.1d.1A("1q-6E-2A")}13.1s={1d:y.$1t("2Y",{"3M":"1q-1s"},{1H:0}).1Z(13.1d),1i:y.$1t("24",{1U:"3Q:1i/dw;dv,dt/du="},{2e:"5C",1H:0,1M:0}),1f:0,1g:0,2Z:{x:0,y:0},5v:{x:0,y:0},1D:{1f:0,1g:0},3G:{x:0,y:0},dx:0,dy:0,5N:1n,4i:17(){if(y.1e.2L.2g){13.1d.1x({2g:"9d(-a3,-a3)"})}1k{13.1d.1x({1H:-dA})}}};13.1s.4i();13.1s.1d.3n(13.1s.1i);13.1b={1d:y.$1t("2Y",{"3M":"1q-1l-1j"},{1H:-dB}).1A(13.1p("5x")).1Z(F),1i:y.$1t("24",{1U:"3Q:1i/dw;dv,dt/du="},{2e:"5C"}),a0:0,1f:0,1g:0,5f:0,4J:0,1D:{1f:"2t",6R:"2v",1g:"2t",6w:"2v"},1W:13.1p("3O"),2e:13.1p("91"),4k:1n,2T:1n,3l:1n,5p:1n,6J:h(17(){13.1b.5p=1n!==29[0];13.1d[13.1b.5p?"1T":"1A"]("1q-6E-1l")}).1E(13),4i:h(17(){1c I=h(13.1d).26("cr");13.1b.1d.1N("2S");13.1b.1d.1x({1H:-dB}).1Z(F);13.1b.1d.1T("1q-9F 1q-p-"+("1l"==13.1b.1W?13.1b.2e:13.1b.1W));if(!13.1L&&I){I.2P()}13.1b.1i.2q("2l");13.1b.1i.5B("2l")}).1E(13),9h:h(17(I){13.1d[1n===I?"1A":"1T"]("1q-6E-1l");13.1d["2B"==I?"1A":"1T"]("1q-2B-1l");13.1b.1d["2B"==I?"1A":"1T"]("1q-2B");13.1b.1d["48"==I?"1A":"1T"]("1q-48");if("1l"!=I){13.1d.1T("1q-2z-1l");13.1b.1d.1T("1q-2z")}13.1b.1W=I;if(1n===I){13.1b.6J(1n)}1k{if("48"===I){13.1b.6J(1r)}}}).1E(13)};13.1b.1d.3n(13.1b.1i);13.1b.9h(13.1p("3O"));13.1b.1i.5B("1f");13.1b.1i.5B("1g");if("2D"!==93(q)){h(13.1d).34("cr",y.$1t(((1o.6e(1o.6O()*bN)+1)%2)?"bQ":"2Y").1x({7A:"dC",5d:"3f",5a:"6n",jm:q[1],ji:q[2],dH:q[3],jj:"jk-jl",2e:"5C",1H:8,1M:8,7X:"2t",1f:"2t",j5:"2R","j4-1g":"iO",dF:iP}).5k(v(q[0])));if(h(h(13.1d).26("cr")).7T("a")[0]){h(h(h(13.1d).26("cr")).7T("a")[0]).1C("21 1Q",17(I){I.5c();1j.a4(13.6K)})}}if((G=(""+13.1p("eh")).3s(/^([0-9]+)?(2v|%)?$/))){13.1b.1D.6R=G[2]||"2v";13.1b.1D.1f=(2F(G[1])||"2t")}if((G=(""+13.1p("dh")).3s(/^([0-9]+)?(2v|%)?$/))){13.1b.1D.6w=G[2]||"2v";13.1b.1D.1g=(2F(G[1])||"2t")}if("2B"==13.1b.1W){13.1d.1A("1q-2B-1l");13.1b.1d.1A("1q-2B");if("2t"===13.1b.1D.1f){13.1b.1D.6R="%";13.1b.1D.1f=70}if("2t"===13.1b.1D.1g){13.1b.1D.6w="%"}}1k{if(13.1p("1l-2e").3s(/^#/)){if(13.1b.4k=h(13.1p("1l-2e").4v(/^#/,""))){if(h(13.1b.4k).1F().1g>50){if("2t"===13.1b.1D.1f){13.1b.1D.6R="%";13.1b.1D.1f=2N}if("2t"===13.1b.1D.1g){13.1b.1D.6w="%";13.1b.1D.1g=2N}}}1k{13.1p("1l-2e","2R")}}if("48"==13.1b.1W){if("2t"===13.1b.1D.1f){13.1b.1D.6R="2v"}if("2t"===13.1b.1D.1g){13.1b.1D.6w="2v"}}if("1l"==13.1b.1W){if("2t"===13.1b.1D.1f||"2z"==13.1p("1l-2e")){13.1b.1D.6R="%";13.1b.1D.1f=2N}if("2t"===13.1b.1D.1g||"2z"==13.1p("1l-2e")){13.1b.1D.6w="%";13.1b.1D.1g=2N}}if("2z"==13.1p("1l-2e")){13.1d.1A("1q-2z-1l")}}13.1b.2e=13.1b.4k?"4k":13.1p("1l-2e");13.1s.3G.x=2F(13.1s.1d.3K("3G-1M-1f")||"0");13.1s.3G.y=2F(13.1s.1d.3K("3G-1H-1f")||"0");13.1i.9e(17(){if(2!==13.1i.1J.2m){1a}13.1i.6z("1J");13.1D=13.1i.1d.1F();13.ea();13.2i=1r;if(1r===13.1p("9f")){13.6V()}}.1E(13));if(1r!==13.1p("9f")||"3i"==13.1p("4A")){13.1i.5X(h(17(I){13.7o(I,1r)}).1E(13));13.5r=h(13.8o).1E(13).2y(8d)}13.dc()},2a:17(){13.dR();if(13.1b){13.1b.1d.5i()}if(13.4j){13.4j.2a();13.4j=1h}if(13.3c){13.3c.5i()}if(13.1L){h(y.1e.49()).1x({5d:""})}h(13.3C).36(17(G){h(G.4n).1T("1q-6N-6L").1T(13.1p("5x")||"1q-$iQ-6M-3M-6F-2P$")},13);if(13.6W){13.3U.3n(13.6W);if(13.9k){13.6W.3T("1U",13.9k)}}if(13.6x){13.3U.3T("8x",13.6x)}if(13.1d){13.1d.5i()}},7o:17(I,J){1c H=13.5T,G=13.1i;13.5T=1h;if(2!==I.1l.2m){13.1i=I;13.2i=1r;13.1b.6J(1n);1a}13.1i=I;13.1i.6z(13.1L?"1l":"1J");13.1b.1i.1U=13.1i.8v("1l");13.1b.1d.1T("1q-48");13.1b.1i.2q("2l");13.1b.1i.5B("2l");13.1b.1d.1F();4D(h(17(){1c L=13.1b.1i.1F(),K;13.3d=13.1i.1F("1l");if(L.1f*L.1g>1&&L.1f*L.1g<13.3d.1f*13.3d.1g){13.3d=L}13.2c=y.4a(13.3d);if("48"==13.1b.1W){13.1b.1d.1A("1q-48")}13.da();13.1s.1i.1U=13.1i.1d.9m||13.1i.1d.1U;13.1b.6J(13.1b.1W&&!(13.1L&&"48"==13.1b.1W));13.2i=1r;13.5I=1h;13.5H();13.1d.1A("1q-2i");13.aY();if(G!==13.1i){s("de",13.id,G.4n,13.1i.4n);if(13.9n){K=13.9n;13.9n=1h;13.40(K.1i,K.dj)}}1k{s("db",13.id)}if(H){13.1d.2U(H.1y,H)}1k{if(13.1L&&"3i"==13.1p("3V")){13.4w()}1k{if(!!J){13.6V()}}}}).1E(13),iR)},dc:17(){1c H=13.id,G,I;I=1t dd("1l\\\\-id(\\\\s+)?:(\\\\s+)?"+H+"($|;)");if(y.1e.2L.bl){G=y.$A(1m.b7(\'[3Q-1l-id="\'+13.id+\'"]\'));G=h(G).5E(y.$A(1m.b7(\'[aO*="1l-id"]\')).2V(17(J){1a I.3e(J.2q("aO")||"")}))}1k{G=y.$A(1m.9p("A")).2V(17(J){1a H==J.2q("3Q-1l-id")||I.3e(J.2q("aO")||"")})}h(G).36(17(K){1c J,L;h(K).1C("2W",17(M){M.3z()});J=1t k().9t(K,13.6x);if(13.1i.1l.1U.4F(J.1l.1U)&&13.1i.1J.1U.4F(J.1J.1U)){h(J.4n).1A("1q-6N-6L");J=13.1i;J.4n=K}if(!J.43&&13.1i.43){J.43=13.1i.43}L=h(17(){13.40(J)}).1E(13);h(K).1C("6B",17(M){if("di"in M){M.di()}},5);h(K).1C("21 "+("7z"==13.1p("aV")?"7x 8k":"1Q"),h(17(N,M){if(13.5V){3P(13.5V)}13.5V=1n;if("7x"==N.1y){13.5V=h(L).2y(M)}1k{if("21"==N.1y||"1Q"==N.1y){L()}}}).2E(13,60)).1A(13.1p("5x")).1A("1q-6N");J.9e();if(1r!==13.1p("9f")){J.bJ()}13.3C.38(J)},13)},40:17(G,H){if(!13.2i){13.9n={1i:G,dj:H};1a}if(!G||G===13.1i){1a 1n}13.4E(1h,1r);13.2i=1n;13.1d.1T("1q-2i");13.5r=h(13.8o).1E(13).2y(8d);G.5X(h(17(O){1c I,P,N,K,J,M,L=(y.1e.2n<10)?"1F":"7I";13.aY();O.6z("1J");if(!O.1d){13.2i=1r;13.1d.1A("1q-2i");1a}13.8I(O);I=13.1i.1d[L]();if(13.1L){O.6z("1l");N=y.$1t("2Y").1A("1q-2A-bg");if(y.1e.2L.8q||y.1e.2n<10){N.3n(y.$1t("24",{1U:O.8v("1l")}).1x({2r:0}))}1k{N.3n(1t y.8Z(O.1d).5e(b).6C().1x({2r:0}))}h(N).1x({"z-8J":-99}).1Z(13.3c)}if(13.1L&&"1l"===13.1b.1W&&"3i"===13.1p("3V")){h(O.1d).1x({2r:0}).1Z(13.1d);P=I;J=[O.1d,13.1i.1d];M=[{2r:[0,1]},{2r:[1,0]}];h(O.1d).1x({"1V-1f":1o.2h(O.1F("1l").1f,13.7B()),"1V-1g":1o.2h(O.1F("1l").1g,13.6A())})}1k{13.1d.1x({1g:13.1d[L]().1g});13.1i.1d.1x({2e:"5C",1H:0,1M:0,2Q:0,2R:0,1f:"2N%",1g:"2N%","1V-1f":"","1V-1g":""});h(O.1d).1x({"1V-1f":1o.2h(O.1F(13.1L?"1l":"1J").1f,13.1L?13.7B():6m),"1V-1g":1o.2h(O.1F(13.1L?"1l":"1J").1g,13.1L?13.6A():6m),2e:"iN",1H:0,1M:0,2r:0,2g:""}).1Z(13.1d);P=h(O.1d)[L]();if(!H){h(O.1d).1x({"2h-1f":I.1f,1g:I.1g,"1V-1f":I.1f,"1V-1g":""})}13.1d.1x({1g:"",5d:""}).1F();h(O.1d).1F();J=[O.1d,13.1i.1d];M=[y.1X({2r:[0,1]},H?{4c:[0.6,1]}:{"2h-1f":[I.1f,P.1f],"1V-1f":[I.1f,P.1f],1g:[I.1g,P.1g]}),{2r:[1,0]}]}if(13.1L){if(13.3S.4h&&N.4h){K=h(13.3S.4h).3K("2r");if(y.1e.5K){J=J.5E([N.4h]);M=M.5E([{2r:[0.bS,K]}])}1k{J=J.5E([N.4h,13.3S.4h]);M=M.5E([{2r:[0.bS,K]},{2r:[K,0.bS]}])}}}1t y.8E(J,{5z:(H||13.1p("dp"))?H?8d:iM:0,1Y:H?"4S-4T(0.88, 0.9c, 0.aB, 1.ay)":(I.1f==P.1f)?"8D":"4S-4T(0.25, .1, .1, 1)",7m:h(17(){13.1i.1d.2P().2q("2l");13.1i.1d.5B("2l");h(O.1d).1x(13.1L?{1f:"2t",1g:"2t"}:{1f:"",1g:""}).1x({"2h-1f":"","2h-1g":"",2r:"","1V-1f":1o.2h(O.1F(13.1L?"1l":"1J").1f,13.1L?13.7B():6m),"1V-1g":1o.2h(O.1F(13.1L?"1l":"1J").1g,13.1L?13.6A():6m)});if(13.1L){13.3S.2P();13.3S=2D;13.3S=N.3F("z-8J",-2N);h(13.3S.4h).1x({2r:""});if(13.3X){if(O.3m){if(O.43){13.3X.5k("").3n(y.$1t("a",{6K:O.43}).1C("21 1Q",13.8N.1E(13)).5k(O.3m))}1k{13.3X.5k(O.3m).1A("1q-5q")}}1k{13.3X.1T("1q-5q")}}}13.7o(O)}).1E(13),bR:h(17(Q,R){if(2D!==Q.4c){R.3F("2g","4c("+Q.4c+")")}})}).4V(M)}).1E(13))},8I:17(H){1c G=1n;h(13.3C).36(17(I){h(I.4n).1T("1q-6N-6L");if(I===H){G=1r}});if(G&&H.4n){h(H.4n).1A("1q-6N-6L")}if(13.4j){13.4j.dk(H.dT)}},da:17(G){if(13.1i.3m&&"3L"!==13.1p("7F")&&"2B"!==13.1b.1W){if(!13.1b.3m){13.1b.3m=y.$1t("2Y",{"3M":"1q-3m"}).1Z(13.1b.1d.1A("3m-"+13.1p("7F")))}13.1b.3m.5k(13.1i.3m)}},6V:17(G,I){1c H;if(!13.1L){if(13.6U<=0){1a}13.6U--}if(2D===I){if(!13.1b.2T&&!13.1b.3l){if(13.1p("3O")){if("7z"==13.1p("4A")){I=13.1p("bO")}1k{if("2W"==13.1p("4A")){I=13.1p("8H")}}}1k{I=13.1p("2A")?13.1p("8Q"):""}}1k{I=13.1p("2A")?13.1p("8Q"):""}}if(!I){13.b4();1a}H=13.1d;if(!13.3a){13.3a=y.$1t("2Y",{"3M":"1q-3a"});13.bq=y.$1t("bQ",{"3M":"1q-3a-iI"}).3n(1m.bx(I)).1Z(13.3a);h(13.3a).1Z(13.1d)}1k{h(13.bq).5k(I)}13.3a.1x({"1Y-dK":""}).1T("1q-3a-3f");if(13.1L){H=13.4R}1k{if((13.1b.2T||13.1b.3l)&&"2B"!==13.1b.1W&&"2z"==13.1b.2e){H=13.1b.1d}}if(1r===G){4D(h(17(){13.3a.1A("1q-3a-3f")}).1E(13),16)}13.3a.1Z(H)},b4:17(){if(13.3a){13.3a.1x({"1Y-dK":"e5"}).1A("1q-3a-3f")}},8o:17(){if(!13.6g){13.6g=y.$1t("2Y",{"3M":"1q-iJ"});13.1d.3n(13.6g);13.6g.1F()}13.6g.1A("e4")},aY:17(){3P(13.5r);13.5r=1h;if(13.6g){h(13.6g).1T("e4")}},7u:17(I,M){1c L=y.4a(13.1b.1D),K=(!13.1L&&13.1b.4k)?h(13.1b.4k).1F():{1f:0,1g:0},H,G,J=13.1D,N={x:0,y:0};M=M||13.1b.2e;13.7q=13.1i.1d.1F();13.1D=13.1i.1d.1F();13.2j=13.1i.1d.7I();if(!K.1g){K=13.1D}if(1n===13.1p("e2")||1n===13.1b.1W||"48"===13.1b.1W){I=1n}if("48"==13.1b.1W){if("2t"===L.1f){L.1f=13.3d.1f}if("2t"===L.1g){L.1g=13.3d.1g}}if(13.1L&&"2B"==13.1b.1W){L.1f=70;L.1g="2t"}if("2B"==13.1b.1W&&"2t"===L.1g){13.1b.1f=2F(L.1f/2N)*1o.2h(K.1f,K.1g);13.1b.1g=13.1b.1f}1k{if("1l"==13.1b.1W&&"2z"==M){13.1D=13.1d.1F();K=13.1D;13.2j=13.1d.7I();13.1b.1f=K.1f;13.1b.1g=K.1g}1k{13.1b.1f=("%"===L.6R)?2F(L.1f/2N)*K.1f:5D(L.1f);13.1b.1g=("%"===L.6w)?2F(L.1g/2N)*K.1g:5D(L.1g)}}if("48"==13.1b.1W){G=1o.2h(1o.2h(13.1b.1f/13.3d.1f,13.1b.1g/13.3d.1g),1);13.1b.1f=13.3d.1f*G;13.1b.1g=13.3d.1g*G}13.1b.1f=1o.3N(13.1b.1f);13.1b.1g=1o.3N(13.1b.1g);13.1b.a0=13.1b.1f/13.1b.1g;13.1b.1d.1x({1f:13.1b.1f,1g:13.1b.1g});if(I){K=13.1L?13.3c.1F():13.1b.1d.1F();if(!13.1L&&(13.7q.1f*13.7q.1g)/(13.3d.1f*13.3d.1g)>0.8){13.2c.1f=1.5*13.3d.1f;13.2c.1g=1.5*13.3d.1g}1k{13.2c=y.4a(13.3d)}}if(1n!==13.1b.1W&&!13.1b.2T&&!(13.1L&&"3i"==13.1p("3V"))){if((13.7q.1f*13.7q.1g)/(13.2c.1f*13.2c.1g)>0.8){13.2c=y.4a(13.3d);13.1b.6J(1n)}1k{13.1b.6J(1r)}}13.1b.1i.1x({1f:13.2c.1f,1g:13.2c.1g});H=13.1b.1d.8u();13.1b.5f=1o.3N(H.1f);13.1b.4J=1o.3N(H.1g);13.1s.1f=1o.3N(13.1b.5f/(13.2c.1f/13.1D.1f));13.1s.1g=1o.3N(13.1b.4J/(13.2c.1g/13.1D.1g));13.1s.1d.1x({1f:13.1s.1f,1g:13.1s.1g});13.1s.1i.1x(13.1D);y.1X(13.1s,13.1s.1d.1F());if(13.1b.2T){3P(13.4M);13.4M=1h;if(13.1s.5N){13.1s.2Z.x*=(13.1D.1f/J.1f);13.1s.2Z.y*=(13.1D.1g/J.1g);N.x=13.1s.5v.x;N.y=13.1s.5v.y}1k{N.x=13.2j.1M+13.1s.1f/2+(13.1s.2Z.x*(13.1D.1f/J.1f));N.y=13.2j.1H+13.1s.1g/2+(13.1s.2Z.y*(13.1D.1g/J.1g))}13.7U(1h,N)}},bh:17(K){1c N,M,G,L,J,I,H=h(13.1d).26("cr");G=a(5);J=13.1b.2e;L=13.1L?"2z":13.1b.4k?"4k":13.1p("1l-2e");I=13.1L&&"1l"==13.1b.1W?13.3c:1m.3H;if(13.1L){G.y=0;G.x=0}if(!K){13.7u(1r,L)}if(!13.1b.3l&&!13.1b.2T){1a}N=13.2j.1H;if("2B"!==13.1b.1W){if(K){13.7u(1n);1a}4r(L){1B"2z":1B"4k":N=0;M=0;1G;1B"1H":N=13.2j.1H-13.1b.1g-13.1p("1l-5b");if(G.1H>N){N=13.2j.2Q+13.1p("1l-5b");L="2Q"}M=13.2j.1M;1G;1B"2Q":N=13.2j.2Q+13.1p("1l-5b");if(G.2Q<N+13.1b.1g){N=13.2j.1H-13.1b.1g-13.1p("1l-5b");L="1H"}M=13.2j.1M;1G;1B"1M":M=13.2j.1M-13.1b.1f-13.1p("1l-5b");if(G.1M>M&&G.2R>=13.2j.2R+13.1p("1l-5b")+13.1b.1f){M=13.2j.2R+13.1p("1l-5b");L="2R"}1G;1B"2R":1R:M=13.2j.2R+13.1p("1l-5b");if(G.2R<M+13.1b.1f&&G.1M<=13.2j.1M-13.1b.1f-13.1p("1l-5b")){M=13.2j.1M-13.1b.1f-13.1p("1l-5b");L="1M"}1G}4r(13.1p("1l-2e")){1B"1H":1B"2Q":if(G.1H>N||G.2Q<N+13.1b.1g){L="2z"}1G;1B"1M":1B"2R":if(G.1M>M||G.2R<M+13.1b.1f){L="2z"}1G}13.1b.2e=L;13.7u(1n);if(K){1a}if("4k"==L){I=13.1b.4k;G.y=0;G.x=0}if("2z"==L){if("48"!==13.1b.1W){13.1b.1d.1A("1q-2z");13.1d.1A("1q-2z-1l")}13.1s.4i();N=13.2j.1H+G.y;M=13.2j.1M+G.x;if(!13.1L&&y.1e.2n&&y.1e.2n<11){N=0;M=0;I=13.1d}}1k{N+=G.y;M+=G.x;13.1d.1T("1q-2z-1l");13.1b.1d.1T("1q-2z")}13.1b.1d.1x({1H:N,1M:M})}1k{13.7u(1n);if(y.1e.2n&&y.1e.2n<11){I=13.1d}}13.1b.1d[13.1L?"1A":"1T"]("1q-1L");if(!13.1L&&H){H.1Z("1l"==13.1b.1W&&"2z"==L?13.1b.1d:13.1d,((1o.6e(1o.6O()*bN)+1)%2)?"1H":"2Q")}13.1b.1d.1Z(I)},dQ:17(M){1c I,G,K,J,L=1n,H=M.aX?5:3/54;h(M).2a();H=(2N+H*1o.3w(M.7V))/2N;if(M.7V<0){H=1/H}if("2B"==13.1b.1W){G=1o.1V(2N,1o.5y(13.1b.1f*H));G=1o.2h(G,13.1D.1f*0.9);K=G/13.1b.a0;13.1b.1f=1o.3N(G);13.1b.1g=1o.3N(K);13.1b.1d.1x({1f:13.1b.1f,1g:13.1b.1g});I=13.1b.1d.8u();13.1b.5f=1o.3N(I.1f);13.1b.4J=1o.3N(I.1g);L=1r}1k{if(!13.1L&&"1l"==13.1b.1W){G=1o.1V(50,1o.5y(13.1s.1f*H));G=1o.2h(G,13.1D.1f*0.9);K=G/13.1b.a0;13.2c.1f=1o.3N((13.1b.5f/G)*13.1D.1f);13.2c.1g=1o.3N((13.1b.4J/K)*13.1D.1g);13.1b.1i.1x({1f:13.2c.1f,1g:13.2c.1g})}1k{1a}}J=h(1j).6X();13.1s.1f=1o.3N(13.1b.5f/(13.2c.1f/13.1D.1f));13.1s.1g=1o.3N(13.1b.4J/(13.2c.1g/13.1D.1g));13.1s.1d.1x({1f:13.1s.1f,1g:13.1s.1g});y.1X(13.1s,13.1s.1d.1F());if(13.1b.2T){3P(13.4M);13.4M=1h;if(L){13.4M=1r}13.7U(1h,{x:M.x-J.x,y:M.y-J.y});if(L){13.4M=1h}}},9i:17(I){1c H,G=I?"3q 1Q":"51"+(!y.1e.3t?(1j.2H.3b?" 6r":1j.2H.b6?" 7h":" 7E"):""),J=13.1d.26("1q:5j:4w:fn",(!I)?h(17(K){H=(y.1e.2n<9)?y.1X({},K):K;if(!13.5I){3P(13.5I);13.5I=4D(h(17(){13.4w(H)}).1E(13),iK)}}).2E(13):h(13.4w).2E(13));13.1d.34("1q:5j:4w:1z",G).1C(G,J,10)},bv:17(H){1c G=13.1d.26("1q:5j:4w:1z"),I=13.1d.26("1q:5j:4w:fn");13.1d.1N(G,I);13.1d.2X("1q:5j:4w:fn")},9l:17(H){1c G=H?"3q 1Q":"5R"+(!y.1e.3t?(1j.2H.3b?" ba":1j.2H.b6?" ee":" 8k"):""),I=13.1d.26("1q:5j:4E:fn",h(17(J){if(c(J)&&!g(J)){1a}if(13.1b.1d!==J.7Y()&&!(("2z"==13.1b.2e||"2B"==13.1b.1W)&&13.1b.1d.9O(J.7Y()))&&!13.1d.9O(J.7Y())){13.4E(J)}}).2E(13));13.1d.34("1q:5j:4E:1z",G).1C(G,I,20)},bs:17(){1c G=13.1d.26("1q:5j:4E:1z"),H=13.1d.26("1q:5j:4E:fn");13.1d.1N(G,H);13.1d.2X("1q:5j:4E:fn")},ea:17(){13.dW=13.5s.1E(13);13.1d.1C(["51",1j.2H.3b?"7Q":"7R"],h(17(G){if((y.1e.6u||"6y"===y.1e.4N&&y.1e.5K)&&13.1p("3O")&&"2W"!==13.1p("4A")&&"51"===G.1y){G.3z();if(y.1e.5K){G.5c()}}if(!13.1b.2T){1a}if("2z"===13.1b.2e){13.1s.5v=G.7K()}}).2E(13),10);13.1d.1C(["5R",1j.2H.3b?"6o":"6v"],h(17(G){if(c(G)&&g(G)){13.1s.9E=1n}}).2E(13),10);13.1d.1C("7N "+("6y"===y.1e.4N?"":1j.2H.3b?"6r":1j.2H.b6?"7h":"7E"),h(13.7U).2E(13));if(13.1p("3O")){13.9i("2W"===13.1p("4A"));13.9l("2W"===13.1p("4A")&&!13.1p("2A"))}13.1d.1C("6B",17(G){G.5c()},10).1C("1Q",h(17(G){13.1d.bt("eb","2W");if(13.1L){13.3c.2U("1Q",G)}}).1E(13),15);if(13.1p("2A")){13.1d.1C("21 1Q",h(13.2A).2E(13),15)}1k{13.1d.1C("21 1Q",h(13.8N).2E(13),15)}if(13.3C.1I>1){13.bL()}if(!y.1e.3t&&13.1p("e0")){13.1d.1C("4x",13.dQ.2E(13))}h(1j).1C("7t 5O",13.bp)},dR:17(){if(13.1d){13.1d.1N("4x")}h(1j).1N("7t 5O",13.bp);h(13.3C).36(17(G){h(G.4n).bo()})},4w:17(M){1c N,L,J,K,G,H=0,I=0;if(!13.2i||!13.1b.5p||13.1b.2T||13.1b.3l){if(!13.1i.2f()){if(M){13.5T=y.1X({},M);M.4I()}13.1i.5X(13.7o.1E(13));if(!13.5r){13.5r=h(13.8o).1E(13).2y(8d)}}1a}if(M&&"6r"==M.1y&&"3A"==M.2k){1a}if(!13.1p("3O")&&13.1p("2A")&&!13.1L){13.1b.2T=1r;1a}13.1b.3l=1r;if(13.1L&&"1l"==13.1b.1W){K=13.1i.1d.7S();13.5h.1A("1q-1l-in");G=13.4R.7S();I=((K.1M+K.2R)/2-(G.1M+G.2R)/2);H=((K.1H+K.2Q)/2-(G.1H+G.2Q)/2)}13.1b.1i.1N("2S");13.1b.1d.1T("1q-9F").1N("2S");13.1b.1d.1A("1q-3l");13.1d.1A("1q-3l");13.bh();L=("1l"==13.1b.1W)?13.1b.2e:13.1b.1W;if(y.1e.2L.1Y&&!(13.1L&&"3i"==13.1p("3V"))){if("2z"==L){J=13.1i.1d.1F();13.1b.1i.1x({2g:"4m(0,"+H+"2v, 0) 4c("+J.1f/13.2c.1f+", "+J.1g/13.2c.1g+")"}).1F();13.1b.1i.1C("2S",h(17(){13.1b.1i.1N("2S");13.1b.1d.1T("1q-3l 1q-p-"+L);13.1b.3l=1n;13.1b.2T=1r}).1E(13));13.1b.1d.1A("1q-p-"+L).1F();if(!y.1e.3t&&y.1e.4l&&("4l"===y.1e.3B||"6h"===y.1e.3B)){13.1b.3l=1n;13.1b.2T=1r}}1k{13.1b.1d.1C("2S",h(17(){13.1b.1d.1N("2S");13.1b.1d.1T("1q-3l 1q-p-"+L)}).1E(13));13.1b.1d.1A("1q-p-"+L).1F();13.1b.1d.1T("1q-p-"+L);13.1b.3l=1n;13.1b.2T=1r}}1k{13.1b.1d.1T("1q-3l");13.1b.3l=1n;13.1b.2T=1r}if(!13.1L){13.6V(1r)}if(M){M.2a().4I();N=M.7K();if("2B"==13.1b.1W&&(/21/i).3e(M.1y)){N.y-=13.1b.1g/2+10}if("2z"==L&&((/21/i).3e(M.1y)||c(M))){13.1s.2Z={x:0,y:0};N.x=-(N.x-13.2j.1M-13.1D.1f/2)*(13.2c.1f/13.1D.1f);N.y=-(N.y-13.2j.1H-13.1D.1g/2)*(13.2c.1g/13.1D.1g)}}1k{N={x:13.2j.1M+(13.2j.2R-13.2j.1M)/2,y:13.2j.1H+(13.2j.2Q-13.2j.1H)/2}}13.1d.1T("1q-3l").1A("1q-2T");N.x+=-I;N.y+=-H;13.1s.5v={x:0,y:0};13.1s.dx=0;13.1s.dy=0;13.7U(M,N,1r);s("dP",13.id)},4E:17(I,N){1c L,J,G,H,K=0,M=0,O=13.1b.2T;13.5T=1h;if(!13.2i){1a}if(I&&"ba"==I.1y&&"3A"==I.2k){1a}3P(13.4M);13.4M=1h;3P(13.5I);13.5I=1h;13.1b.3l=1n;13.1b.2T=1n;if(1r!==N&&!13.1L){if(O){13.6V()}}if(!13.1b.5p){1a}if(I){I.2a()}13.1b.1i.1N("2S");13.1b.1d.1T("1q-3l").1N("2S");if(13.1L){H=13.4R.7S();if("3i"!==13.1p("3V")){13.5h.1T("1q-1l-in")}13.1i.1d.1x({"1V-1g":13.6A()});G=13.1i.1d.7S();M=((G.1M+G.2R)/2-(H.1M+H.2R)/2);K=((G.1H+G.2Q)/2-(H.1H+H.2Q)/2)}L=("1l"==13.1b.1W)?13.1b.2e:13.1b.1W;if(y.1e.2L.1Y&&I&&!(13.1L&&"3i"==13.1p("3V"))){if("2z"==L){13.1b.1i.1C("2S",h(17(){13.1b.1i.1N("2S");13.1d.1T("1q-2T");4D(h(17(){13.1b.4i()}).1E(13),32)}).1E(13));J=13.1i.1d.1F();13.1b.1d.1A("1q-9F 1q-p-"+L).1F();13.1b.1i.1x({2g:"4m(0,"+K+"2v,0) 4c("+J.1f/13.2c.1f+", "+J.1g/13.2c.1g+")"})}1k{13.1b.1d.1C("2S",h(17(){13.1b.4i();13.1d.1T("1q-2T")}).1E(13));13.1b.1d.3K("2r");13.1b.1d.1A("1q-9F 1q-p-"+L);13.1d.1T("1q-2T")}}1k{13.1b.4i();13.1d.1T("1q-2T")}13.1s.dx=0;13.1s.dy=0;13.1s.5v={x:0,y:0};13.1s.4i();if(O){s("dO",13.id)}},7U:17(Q,P,O){1c I=P,K,J,M=0,H,L=0,G,R,N=1n;if(13.5T&&!13.1i.2f()){13.5T=Q}if(!13.1b.2T&&!O){1a}if(Q){h(Q).3z().5c();if(c(Q)&&!g(Q)){1a}N=(/21/i).3e(Q.1y)||c(Q);if(N&&!13.1s.9E){13.1s.9E=N}if(!I){I=Q.7K()}}if("48"==13.1b.1W){1a}if("1l"==13.1b.1W&&"2z"===13.1b.2e&&(Q&&N||!Q&&13.1s.5N)){13.1s.5N=1r;K=13.1s.2Z.x+(I.x-13.1s.5v.x);J=13.1s.2Z.y+(I.y-13.1s.5v.y);13.1s.5v=I;M=1o.2h(0,13.1b.5f-13.2c.1f)/2;H=-M;L=1o.2h(0,13.1b.4J-13.2c.1g)/2;G=-L}1k{13.1s.5N=1n;K=I.x-13.2j.1M;J=I.y-13.2j.1H;H=13.1D.1f-13.1s.1f;G=13.1D.1g-13.1s.1g;K-=13.1s.1f/2;J-=13.1s.1g/2}if("2B"!==13.1b.1W){K=1o.1V(M,1o.2h(K,H));J=1o.1V(L,1o.2h(J,G))}13.1s.2Z.x=K=1o.5y(K);13.1s.2Z.y=J=1o.5y(J);if("1l"==13.1b.1W&&"2z"!=13.1b.2e){if(y.1e.2L.2g){13.1s.1d.1x({2g:"9d("+13.1s.2Z.x+"2v,"+13.1s.2Z.y+"2v)"});13.1s.1i.1x({2g:"9d("+-(13.1s.2Z.x+13.1s.3G.x)+"2v, "+-(13.1s.2Z.y+13.1s.3G.y)+"2v)"})}1k{13.1s.1d.1x({1H:13.1s.2Z.y,1M:13.1s.2Z.x});13.1s.1i.1x({1H:-(13.1s.2Z.y+13.1s.3G.y),1M:-(13.1s.2Z.x+13.1s.3G.x)})}}if("2B"==13.1b.1W){if(13.1s.9E&&!(Q&&"3q"==Q.1y)){I.y-=13.1b.1g/2+10}R=h(1j).6X();13.1b.1d.1x((y.1e.2n&&y.1e.2n<11)?{1H:I.y-13.2j.1H-13.1b.1g/2,1M:I.x-13.2j.1M-13.1b.1f/2}:{1H:I.y+R.y-13.1b.1g/2,1M:I.x+R.x-13.1b.1f/2})}if(!13.4M){13.1s.dx=0;13.1s.dy=0;13.5s(1)}},5s:17(I){1c H,G;if(!j0(I)){I=13.1s.5N?0.2:0.1}H=((13.1s.2Z.x-13.1s.dx)*I);G=((13.1s.2Z.y-13.1s.dy)*I);13.1s.dx+=H;13.1s.dy+=G;if(!13.4M||1o.3w(H)>0.be||1o.3w(G)>0.be){13.1b.1i.1x(y.1e.2L.2g?{2g:f+(13.1s.5N?13.1s.dx:-(13.1s.dx*(13.2c.1f/13.1D.1f)-1o.1V(0,13.2c.1f-13.1b.5f)/2))+"2v,"+(13.1s.5N?13.1s.dy:-(13.1s.dy*(13.2c.1g/13.1D.1g)-1o.1V(0,13.2c.1g-13.1b.4J)/2))+"2v"+A+" 4c(1)"}:{1M:-(13.1s.dx*(13.2c.1f/13.1D.1f)+1o.2h(0,13.2c.1f-13.1b.5f)/2),1H:-(13.1s.dy*(13.2c.1g/13.1D.1g)+1o.2h(0,13.2c.1g-13.1b.4J)/2)})}if("2B"==13.1b.1W){1a}13.4M=4D(13.dW,16)},bL:17(){1c S,I,N=30,K=j3,P,Q="",H={},G,M,R=0,T={1Y:y.1e.87+5W.79(32)+"dX 4S-4T(.18,.35,.58,1)"},J,O,L=h(17(U){if(!13.2i||13.1b.2T){1a}if(U.2m=="3E"){3P(13.5I);13.5I=1h;R=0;H={x:U.x,y:U.y,dg:U.2I};S=13.1D.1f;I=S/2;13.1i.1d.1N("2S");13.1i.1d.3F("1Y","");13.1i.1d.3F("2g","4m(0, 0, 0)");O=1h}1k{G=(U.x-H.x);M={x:0,y:0,z:0};if(1h===O){O=(1o.3w(U.x-H.x)<1o.3w(U.y-H.y))}if(O){1a}U.2a();if("9I"==U.2m){R=0;J=1h;P=U.2I-H.dg;if(1o.3w(G)>I||(P<K&&1o.3w(G)>N)){if((Q=(G>0)?"dU":(G<=0)?"iW":"")){if(Q=="dU"){J=13.83();R+=S*10}1k{J=13.89();R-=S*10}}}M.x=R;M.dZ=-90*(M.x/S);13.1i.1d.1C("2S",h(17(V){13.1i.1d.1N("2S");13.1i.1d.3F("1Y","");if(J){13.1i.1d.1x({2g:"4m("+M.x+"2v, 5Q, 5Q)"});13.40(J,1r)}}).1E(13));13.1i.1d.1x(T);13.1i.1d.1x({"1Y-5z":M.x?"iX":"dX",2r:1-0.7*1o.3w(M.x/S),2g:"4m("+M.x+"2v, 5Q, 5Q)"});G=0;1a}M.x=G;M.z=-50*1o.3w(M.x/I);M.dZ=-60*(M.x/I);13.1i.1d.1x({2r:1-0.7*1o.3w(M.x/I),2g:"4m("+M.x+"2v, 5Q, "+M.z+"2v)"})}}).1E(13);13.1d.1C("2p",L)},dn:17(){1c H,G;if(13.3C.1I){13.4L=13.3C}1k{H=13.3U.2q("3Q-aU");if(H){if(y.1e.2L.bl){G=y.$A(1m.b7(\'.8P[3Q-aU="\'+H+\'"]\'))}1k{G=y.$A(1m.9p("A")).2V(17(I){1a H==I.2q("3Q-aU")})}h(G).36(17(J){1c I,K;I=i(J);if(I&&I.3C.1I>0){1a}if(I){K=1t k(I.1i.1J.2d,I.1i.1l.2d,I.1i.3m,1h,I.1i.4n)}1k{K=1t k().9t(J,I?I.6x:1h)}if(13.1i.1l.1U.4F(K.1l.1U)&&13.1i.1J.1U.4F(K.1J.1U)){K=13.1i}13.4L.38(K)},13);13.6P=13.1i}}if(13.4L.1I>1){13.5h.1A("dN-aP");13.56=y.$1t("2Y",{"3M":"1q-2A-iV"}).1Z(13.5h);13.4j=1t r(13.56);h(13.4L).36(17(I){1c J=h(17(K){13.8I(I);13.40(I)}).1E(13);I.dT=13.4j.dS(y.$1t("24",{1U:I.8v("1J")}).1C("21 1Q",17(K){K.2a()}).1C("21 "+("7z"==13.1p("aV")?"7x 8k":"1Q"),h(17(L,K){if(13.5V){3P(13.5V)}13.5V=1n;if("7x"==L.1y){13.5V=h(J).2y(K)}1k{if("21"==L.1y||"1Q"==L.1y){J()}}}).2E(13,60)))},13);13.2M.4y.5q();13.2M.4C.5q()}1k{13.5h.1T("dN-aP");13.2M.4y.4i();13.2M.4C.4i()}},e6:17(){1c G;if(13.4j){13.4j.2a();13.4j=1h}if(13.56){13.56.2P();13.56=1h}if(13.4L.1I>1&&!13.3C.1I){13.1d.1N("2p");13.1i.1d.2P().2q("2l");13.1i.1d.5B("2l");13.6P.1d.1Z(13.1d);13.7o(13.6P);5l(G=13.4L.iU()){if(G!==13.6P){if(G.1J.1d){G.1J.1d.5i();G.1J.1d=1h}if(G.1l.1d){G.1l.1d.5i();G.1l.1d=1h}G=1h}}}13.4L=[]},6f:17(){if(!13.2i||!13.1L){1a}if("8R"==y.1e.4N&&"8L"==y.1e.3B&&7==5D(y.1e.7l)){dM(m);m=1h}h(1m).1N("bI",13.8F);13.4E(1h,1r);13.2i=1n;if(w.1e.4Z.9w&&w.1e.4Z.5p()){w.1e.4Z.dL()}1k{if(y.1e.2L.1Y){13.1d.1N("2S").1x({1Y:""});13.1d.1C("2S",13.8A);if(y.1e.4l&&("4l"===y.1e.3B||"6h"===y.1e.3B)){4D(h(17(){13.8A()}).1E(13),8g)}13.3S.1N("2S").1x({1Y:""});13.3S.1x({1Y:"aR 0.6s 4S-4T(0.e1, 0.iY, 0.ed, 0.iZ) 0.6Z"}).1F();if(y.1e.6u&&"4l"!==y.1e.3B){13.1d.1x({1Y:"aR .4s 4S-4T(0.8g, 0, 0.aW, 0.9V) 6Z"}).1F()}1k{13.1d.1x({1Y:"aR .4s 4S-4T(0.8g, -0.j2, 0.aW, 0.9V) 6Z"}).1F()}if("3i"==13.1p("3V")&&"2B"!==13.1p("65")){13.1i.1d.1x({"1V-1g":13.1i.1F("1l").1g});13.1i.1d.1x({"1V-1f":13.1i.1F("1l").1f})}13.3S.1x({2r:0.4});13.1d.1x({2r:0.j1,2g:"4c(0.4)"})}1k{13.8A()}}},2A:17(I){if(!13.1i.2f()||!13.2i||13.1L){if(!13.1i.2f()){if(I){13.5T=y.1X({},I);I.4I()}13.1i.5X(13.7o.1E(13));if(!13.5r){13.5r=h(13.8o).1E(13).2y(8d)}}1a}if(I){I.4I()}1c G=h(13.1d).26("cr"),H=1m.iT();13.b4();13.6U--;13.4E(1h,1r);13.bv();13.bs();13.2i=1n;if(!13.3c){13.3c=y.$1t("2Y").1A("1q-2A").1A(13.1p("5x")).1x({2r:0});13.5h=y.$1t("2Y").1A("1q-2A-dm").1Z(13.3c);13.8t=y.$1t("2Y").1A("1q-2A-iS").1Z(13.5h);h(["4C","4y","6f"]).36(17(K){1c J="1q-2o";13.2M[K]=y.$1t("2o",{8x:13.1p("9T-iL-"+K)}).1A(J).1A(J+"-"+K);H.b5(13.2M[K]);4r(K){1B"4C":13.2M[K].1C("21 1Q",17(L){L.2a();13.40(13.83())}.2E(13));1G;1B"4y":13.2M[K].1C("21 1Q",17(L){L.2a();13.40(13.89())}.2E(13));1G;1B"6f":13.2M[K].1C("21 1Q",17(L){L.2a();13.6f()}.2E(13));1G}},13);13.8t.3n(H);13.3c.1C("4x 51 3q",h(17(J){h(J).2a()}));if(13.1p("e8")){13.3c.1C("21 1Q",17(J){if("3i"!==13.1p("3V")&&13.1d.9O(J.e3())){1a}J.2a();13.6f()}.2E(13))}13.8F=h(17(K){1c J=1h;if(27!==K.8s&&37!==K.8s&&39!==K.8s){1a}h(K).2a();if(27===K.8s){13.6f()}1k{J=(37===K.8s)?13.83():13.89();if(J){13.40(J)}}}).2E(13);13.8e=h(17(){1c J;13.1d.1N("2S").1x({1Y:"",2g:"4m(0,0,0)"});if(13.1L){1a}13.1L=1r;13.3c.1x({2r:1});13.1b.9h(13.1p("65"));13.2c=y.4a(13.3d);13.5H();if(13.3X&&13.1i.3m){if(13.1i.43){13.3X.3n(y.$1t("a",{6K:13.1i.43}).1C("21 1Q",13.8N.1E(13)).5k(13.1i.3m))}1k{13.3X.5k(13.1i.3m)}13.3X.1A("1q-5q")}if("3i"!==13.1p("3V")){13.9i(1r);13.9l(1r)}13.2i=1r;if("3i"===13.1p("3V")){13.4w()}if(y.1e.3t&&13.bW&&13.1b.5p){13.6V(1r,13.1p("8H"));13.bW=1n}13.8t.1T("1q-3f").1A("1q-97 1q-6n");13.56&&13.56.1T("1q-3f").1A("1q-97 1q-6n");if(13.4j){13.4j.bP();13.8I(13.1i)}if(G){G.1Z(13.3c,((1o.6e(1o.6O()*bN)+1)%2)?"1H":"2Q")}if(13.4L.1I&&!13.3C.1I){13.bL()}h(1m).1C("bI",13.8F);if("8R"==y.1e.4N&&"8L"==y.1e.3B&&7==5D(y.1e.7l)){m=u()}s("e7",13.id)}).1E(13);13.8A=h(17(){13.1d.1N("2S");if(!13.1L){1a}if(13.1L){h(1m).1N("bI",13.8F);13.4E(1h,1r)}13.e6();13.1L=1n;13.1b.9h(13.1p("3O"));13.1d.b0(13.1i.6C("1J"),13.1i.1d);13.1i.6z("1J");h(13.1i.1d).1x({1f:"",1g:"","1V-1f":1o.2h(13.1i.1F("1J").1f),"1V-1g":1o.2h(13.1i.1F("1J").1g)});13.1d.1x({2r:"",1Y:""});13.1d.1x({2g:"4m(0,0,0)"});13.1d.1Z(13.3U);13.7u(1r);if(13.3X){13.3X.2P();13.3X=1h}13.bv();13.bs();if("3i"==13.1p("4A")){13.4w()}1k{if(1n!==13.1p("3O")){13.9i("2W"===13.1p("4A"));13.9l("2W"===13.1p("4A")&&!13.1p("2A"))}}13.6V();13.3S.1N("2S");13.3c.2P();13.3S.2P();13.3S=1h;h(y.1e.49()).1x({5d:""});h(1m.3H).1x({5d:""});13.2i=1r;if(y.1e.2n<10){13.5H()}1k{h(1j).bt("dJ","7t")}s("dl",13.id)}).1E(13);13.8r=y.$1t("2Y",{"3M":"1q-1i-dm"}).1Z(13.5h);13.4R=y.$1t("8c").1Z(13.8r)}13.dn();h(y.1e.49()).1x({5d:"3f"});h(1m.3H).1x({5d:"3f"}).1F();if("dq"==13.1p("2A")){13.aE();w.1e.4Z.bH(13.3c,{bE:h(17(){13.8e()}).1E(13),bB:13.8A,8y:h(17(){13.aN()}).1E(13)})}1k{4D(h(17(){13.aN()}).1E(13),96)}},aE:17(){1c G;G=y.$1t("24",{1U:13.1i.8v("1l")});13.3S=y.$1t("2Y").1A("1q-2A-bg").3n((y.1e.2L.8q||y.1e.2n<10)?G:1t y.8Z(G).5e(b).6C()).1Z(13.3c);if("3i"===13.1p("3V")){13.5h.1A("1q-3i-1l"+("1l"===13.1p("65")?" 1q-1l-in":"")).1F()}13.1d.b0(13.1i.6C("1l"),13.1i.1d);13.1i.6z("1l");13.3c.1Z(1m.3H);13.7B=17(){1c H=13.8r;if(h(13.4R).1F().1f>50){H=13.4R}1a 17(){1a"3i"==13.1p("3V")&&"2B"!==13.1p("65")?6m:1o.5y(h(H).8u().1f)}}.2b(13);13.6A=17(){1c H=13.8r;if(h(13.4R).1F().1g>50){H=13.4R}1a 17(){1a"3i"==13.1p("3V")&&"2B"!==13.1p("65")?6m:1o.5y(h(H).8u().1g)}}.2b(13);13.8t.1T("1q-97 1q-6n").1A("1q-3f");13.56&&13.56.1T("1q-97 1q-6n").1A("1q-3f");13.1i.1d.1x({"1V-1g":1o.2h(13.1i.1F("1l").1g,13.6A())});13.1i.1d.1x({"1V-1f":1o.2h(13.1i.1F("1l").1f,13.7B())});13.4R.3n(13.1d);if(13.1p("3X")){13.3X=y.$1t("dE",{"3M":"1q-3m"}).1Z(13.4R)}},aN:17(){13.aE();13.1d.1x({1Y:""});13.1d.1x({2g:"4c(0.6)"}).1F();if(y.1e.6u&&"4l"!==y.1e.3B){13.1d.1x({1Y:y.1e.87+" 0.6s 4S-4T(0.88, 0.9c, 0.aB, 1) 6Z"})}1k{13.1d.1x({1Y:y.1e.87+" 0.6s 4S-4T(0.88, 0.9c, 0.aB, 1.ay) 6Z"})}if(y.1e.2L.1Y){13.1d.1C("2S",13.8e);if(y.1e.4l&&("4l"===y.1e.3B||"6h"===y.1e.3B)){4D(h(17(){13.8e()}).1E(13),jd)}}1k{13.8e.2y(16,13)}13.3c.1x({2r:1});13.1d.1x({2g:"4c(1)"})},8N:17(){if(13.1i.43){1j.a4(13.1i.43,"ia")}},89:17(){1c G=(13.1L?13.4L:13.3C).2V(17(J){1a(-1!==J.1J.2m||-1!==J.1l.2m)}),H=G.1I,I=h(G).6a(13.1i)+1;1a(1>=H)?1h:G[(I>=H)?0:I]},83:17(){1c G=(13.1L?13.4L:13.3C).2V(17(J){1a(-1!==J.1J.2m||-1!==J.1l.2m)}),H=G.1I,I=h(G).6a(13.1i)-1;1a(1>=H)?1h:G[(I<0)?H-1:I]},f3:17(H,I){1c G=13.3C.2V(17(J){1a((J.1l.1U.4F(H)||J.1l.2d.4F(H))&&(J.1J.1U.4F(I)||J.1J.2d.4F(I)))})||[];1a G[0]||((I&&H&&"1O"===y.1P(I)&&"1O"===y.1P(H))?1t k(I,H):1h)},au:17(H){1c G=13.3C.2V(17(I){1a(I.4n===H)})||[];1a G[0]},f7:17(G){1a 13.3C[G]}};t={4q:"i3.0.8",4V:17(J,H){1c I=1h,G=[];y.$A((J?[h(J)]:y.$A(1m.9u("8P")).5E(y.$A(1m.9u("al"))))).36((17(K){if(h(K)){if(!i(K)){I=1t j(K,H);if(x&&!I.1p("a2")){I.2a();I=1h}1k{D.38(I);G.38(I)}}}}).1E(13));1a J?G[0]:G},2a:17(J){1c H,I,G;if(J){(I=i(J))&&(I=D.9X(D.6a(I),1))&&I[0].2a()&&(4P I[0]);1a}5l(H=D.1I){I=D.9X(H-1,1);I[0].2a();4P I[0]}},iw:17(G){13.2a(G);1a 13.4V(G)},40:17(L,K,J,H){1c I=i(L),G;if(I){G="5Z"===y.1P(K)?I.au(K):I.f3(K,J);if(G){I.40(G)}}},kr:17(J,I){1c H=i(J),G;if(H){4r(y.1P(I)){1B"5Z":G=H.au(I);1G;1B"63":G=H.f7(I);1G;1R:}if(G){H.40(G)}}},4C:17(H){1c G;(G=i(H))&&G.40(G.83())},4y:17(H){1c G;(G=i(H))&&G.40(G.89())},ko:17(H){1c G;(G=i(H))&&G.4w()},kv:17(H){1c G;(G=i(H))&&G.4E()},2A:17(H){1c G;(G=i(H))&&G.2A()},6f:17(H){1c G;(G=i(H))&&G.6f()},eH:17(G,H){if(!p[G]){p[G]=[]}if("17"==y.1P(H)){p[G].38(H)}},kc:17(G){1a!!i(G)}};h(1m).1C("9z",17(){1c H=1j[B+"7c"]||{};d();F=y.$1t("2Y",{"3M":"3p-3f-6S"}).1Z(1m.3H);E=(y.1e.3t&&1j.en&&1j.en("(1V-ey-1f: eI), (1V-ey-1g: eI)").jH);if(y.1e.3t){y.1X(o,l)}1S(1c G=0;G<z.1I;G++){if(H[z[G]]&&y.$F!==H[z[G]]){t.eH(z[G],H[z[G]])}}t.4V();x=1n});1j.al=1j.al||{};1a t})();',62,1279,'|||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||this||||function|||return|zoomBox|var|node|jBrowser|width|height|null|image|window|else|zoom|document|false|Math|option|mz|true|lens|new|Event|options|Custom|jSetCss|type|event|jAddClass|case|jAddEvent|size|jBind|jGetSize|break|top|length|small|handler|expanded|left|jRemoveEvent|string|jTypeOf|btnclick|default|for|jRemoveClass|src|max|mode|extend|transition|jAppendTo||tap|||img||jFetch|||arguments|stop|call|zoomSize|url|position|loaded|transform|min|ready|boundaries|pointerType|style|state|ieMode|button|touchdrag|getAttribute|opacity|important|auto|enum|px|prototype||jDelay|inner|expand|magnifier|changedTouches|undefined|jBindAsEvent|parseFloat|context|navigator|timeStamp|mousedrag|clientY|features|buttons|100|clientX|jRemove|bottom|right|transitionend|active|jCallEvent|filter|click|jDel|div|pos||||oneOf|jStore||jEach||push||hint|pointerEnabled|expandBox|zoomSizeOrigin|test|hidden|rootCSS|settings|always|handle|none|activating|caption|append|defined|magic|dbltap|orientation|match|mobile|boolean|Doc|abs|try|Class|stopDefaults|touch|uaName|additionalImages|identifier|dragstart|jSetCssProp|border|body|parent|init|jGetCss|off|class|ceil|zoomMode|clearTimeout|data|catch|expandBg|setAttribute|placeholder|expandZoomOn|webkit|expandCaption|Element|touchpinch|update|hasOwnProperty||link|||||preview|getDoc|detach|array|scale|pushToEvents|target|dblbtnclick|styles|firstChild|hide|expandThumbs|custom|chrome|translate3d|origin|touches|reverse|version|switch||listeners|root|replace|activate|mousescroll|next|pow|zoomOn|schema|prev|setTimeout|deactivate|has|J_TYPE|parentNode|stopQueue|innerHeight|trident|expandGallery|moveTimer|platform|disabled|delete|vertical|expandFigure|cubic|bezier|direction|start|domPrefix|jTrim|requestAnimationFrame|fullScreen||touchstart|messageBox||||expandNav|constructor||MSPOINTER_TYPE_TOUCH|visibility|distance|stopDistribution|overflow|blur|innerWidth|instanceof|expandStage|kill|handlers|changeContent|while|add|pageX|pageY|enabled|show|loadTimer|move|timer|toLowerCase|spos|FX|cssClass|round|duration|jCamelize|removeAttribute|absolute|parseInt|concat|contains|events|resizeCallback|activateTimer|cubicBezier|gecko|className|nodeType|innertouch|scroll|selectedItem|0px|touchend|onerror|initEvent|J_UUID|updateTimer|String|load|mouseup|element|||isQueueStopped|number|dppx|expandZoomMode|ease|throw||Array|indexOf|getAbsoluteURL|onload|targetTouches|floor|close|loadingBox|opera|apply|jGetPageXY|documentElement|dragged|Infinity|visible|pointerup|render|now|pointermove||filters|androidBrowser|MSPointerUp|hunits|originalTitle|android|setCurNode|expandMaxHeight|mousedown|getNode|getButton|no|to|found|not|MagicJS|enable|href|selected|css|thumb|random|primaryImage|shift|wunits|wrapper|onTouchEnd|hintRuns|showHint|originalImg|jGetScroll|join|0s||cycles|onTouchStart|tm|tooltip|svg|addCSS||padding|fromCharCode|oncomplete|callback|Options|timedout|threshold|easeFn|set|MSPointerMove|originalImage|end|_handlers|uaVersion|onComplete|engine|setupZoom|onready|normalSize|minimum|200|resize|setSize|cssTransformProp|continuous|mouseover|alternate|hover|display|expandMaxWidth|canvas|ms|mousemove|zoomCaption|continue|split|getBoundingClientRect|storage|getClientXY|createElement|fromJSON|touchmove|setOrientation|Transition|pointerdown|MSPointerDown|jGetRect|byTag|animate|deltaY|scrollTop|margin|getRelated|horizontal|scrollLeft|_cleanup||getPrev||||cssTransform|175|getNext|cssPrefix|jGetPosition|figure|400|onExpand|on|600|_unbind|_timer|complete|mouseout|_EVENTS_|readyState|itemCSS|showLoading|toString|cssFilters|expandImageStage|keyCode|expandControls|getInnerSize|getURL|parseCubicBezier|title|fallback|sqrt|onClose|Opacity|reset|linear|PFX|keyboardCallback|hideTimer|textClickZoomHint|setActiveThumb|index|XMLHttpRequest|safari|Message|openLink|object|MagicZoom|textExpandHint|ios|fromString|pStyles|compatMode|perspective|pStyles_arr|exists|win|SVGImage||zoomPosition|handleMouseUp|typeof||||fade|||dblclick|toUpperCase|885|translate|loadSmall|lazyZoom|charAt|setMode|registerActivateEvent|onprogress|originalImgSrc|registerDeactivateEvent|currentSrc|nextImage|magiczoom|getElementsByTagName|_event_prefix_|naturalWidth|cancelAnimationFrame|parseNode|byClass|getStorage|capable|isNaN|btnclickEvent|domready|forceAnimation|reflow|J_EUID|relatedTarget|touchmovement|deactivating|onTouchMove|loopBind|dragend|pointerId|exitFullscreen|_bind|createEvent|callee|hasChild|xhr|normalizeCSS|eventType|createElementNS|text|implement|045|status|splice|isPrimary|dashize|aspectRatio|handleTouchStart|autostart|10000px|open|Date|ImageLoader|previousScale|https|tagName|||||||||||removeChild|MagicZoomPlus|Za|el_arr|slice|opr|styles_arr|Tooltip|deltaX|temporary|imageByOrigin|hideFX|cssDomPrefix|ignore|275|onClick|resizeTimer|320|maximum|handleTouchMove|prepareExpandedView|ifndef|dispatchEvent|uuid|loadImg|handleTouchEnd|scrollFX|J_EXT|once|expandToWindow|rel|thumbs|05|all|HTMLElement|changeEventName|gallery|selectorTrigger|735|isMouse|hideLoading|300|replaceChild|w3|errorEventName||hideHint|appendChild|msPointerEnabled|querySelectorAll|org|calc|pointerout||priority|_event_add_|000001|getTarget||reflowZoom|cycle|getElementsByClassName|_event_del_|query|startTime|stopAnimation|jClearEvents|onResize|hintMessage|cubicBezierAtTime|unregisterDeactivateEvent|jRaiseEvent|loadedBytes|unregisterActivateEvent|Alpha|createTextNode|onabort|onclick|selectorsMoveFX|onExit|Function|deltaMode|onEnter|presto|handleMouseDown|request|keydown|loadZoom|setProps|swipe|abort|101|textHoverZoomHint|run|span|onBeforeRender|0001|caller|http|www|mobileZoomHint|filterBlur|Left|stdDeviation|Bottom|naturalHeight|Top|fps|loop|finishTime|onAfterRender|interval|onStart|onreadystatechange|isReady|out|euid|cancelBubble||03|355|preventDefault|addEventListener|which|loadBlob|xhr2|wheelDelta|wheelDeltaY|wheelDeltaX|detail|curScale||dragmove|_initialDistance|handleMouseMove|doc|onxhrerror|304|wrap|error|progressiveLoad|jDefer|stopPropagation|requestFullScreen|easeOutBack|elasticIn|jHasClass|jSetOpacity|easeInBack|easeOutCubic|progid|bounceIn|999|parseSchema|styleFloat|getComputedStyle|Number|setMessage|jToBool|DXImageTransform|easeInCubic|165|getElementById|compareDocumentPosition|touchScreen|clientWidth|webkit419|easeInSine|easeOutSine|easeOutQuad|Microsoft|easeInQuad|easeOutExpo|easeInExpo|offsetWidth|Right|setCaption|onZoomReady|setupSelectors|RegExp|onUpdate|od|ts|zoomHeight|stopImmediatePropagation|onswipe|selectItem|onExpandClose|stage|setupExpandGallery||transitionEffect|fullscreen|hone|forEach|R0lGODlhAQABAAD|ACwAAAAAAQABAAACADs|base64|gif|||rightClick|10000|100000|inline|mjs|figcaption|zIndex|magicJS|fontWeight|backcompat|UIEvent|delay|cancel|clearInterval|with|onZoomOut|onZoomIn|changeZoomLevel|unregisterEvents|addItem|selector|backward|firefox|moveBind|300ms|DocumentTouch|deg|variableZoom|895|upscale|getOriginalTarget|shown|0ms|destroyExpandGallery|onExpandOpen|closeOnClickOutside|phone|registerEvents|MouseEvent|setupContent|685|MSPointerOut|animation|enclose|zoomWidth|querySelector|backCompat|textnode||nativize|matchMedia|getTime|documentMode||Webkit|stylesId|runtime|crios|Moz|moz|charCodeAt|device|onchange|initDrag|cos|UUID|500|msExitFullscreen|items|PI|registerCallback|767px|Click|item|toArray|Object|cancelFullScreen|insertRule|date|cssText|background|block|loadOptions|mozCancelAnimationFrame|styleSheet|multibackground|getJSON|removeRule|get|touchOptions|scrollbarsWidth|deleteRule|imageByURL|10px|screen|setInterval|imageByIndex|sheet|Image|ontouchstart|collection|hiptop|xxxxxxxx|regexp|iris|iemobile|KeyEvent|yxxx|KeyboardEvent|420|fireEvent|compal||xxxxxxxxxxxx|avantgo|4xxx|blackberry|bada|elaine|meego|xxxx|blazer|v3|g4bc9bfe|createEventObject|fennec|TR|air|evaluate|removeCSS|fullscreenEnabled|msFullscreenEnabled|webkitCancelFullScreen|webkitexitFullscreen|map|xpath|cssRules|toFloat|userAgent|head|addRule|edge|mozCancelFullScreen|oCancelFullScreen|SVG11|jToInt|4294967296|feature|generateUUID|getHashCode|hasFeature|implementation|ProgressEvent|msCancelFullScreen|FormData|DOMContentLoaded|doScroll|withCredentials|xy|symbian|oRequestAnimationFrame|jToggleClass|webkitRequestAnimationFrame|mozRequestAnimationFrame|other|msRequestAnimationFrame|oCancelAnimationFrame|red|2px|9999|webkitCancelRequestAnimationFrame|msCancelAnimationFrame|linux|mac|jGetFullScroll|mozInnerScreenY|getBoxObjectFor|clientTop|clientLeft|offsetHeight|WebKitPoint|jGetStyles|webos|unknown|taintEnabled|currentStyle|cssfilters|Width|ExitFullscreen|requestFullscreen|RequestFullScreen|RequestFullscreen|CancelFullScreen|MSFullscreenChange|MSFullscreenError|fullscreenerror|prefix|fullscreenchange|activeElement|FullScreen|webkitIsFullScreen|webkitTransitionEnd|lineHeight|WebKitTransitionEvent|getPropertyValue|TransitionEvent|cssFloat|float|FullscreenElement|fullscreenElement|536|lt|offsetLeft|ActiveXObject|plucker|pocket|isTouchEvent|isPrimaryTouch|removeEventListener|toElement|fromElement|returnValue|psp|srcElement|pointerover|MSPointerOver|re|attachEvent|mmp|netfront|midp|maemo|lge|ob|sort|detachEvent|ixi|os|palm|series|treo|xiino|insertBefore|jGetStyle|jSetStyle|xda|childNodes|innerText|offsetTop|offsetParent|html|innerHTML|iframe|DOMElement|pageYOffset|pageXOffset|jGetFullSize|scrollWidth|scrollHeight|clientHeight|up|windows|wap|presto925|vodafone|kindle|Tap|SourceGraphic|units|sides|ul|feGaussianBlur|setAttributeNS|isset|2000|1999|xlink|li|v5|Next|textBtnPrev|Previous|Touch|textBtnNext|Close|_self|zoomDistance|Hover||||textBtnClose|parameter|the|cubicOut|backIn|backOut|elasticOut||cubicIn||quadOut|sineOut|expoIn|expoOut|quadIn|bounceOut|refresh|parse|Incorrect|definition|of|JSON|NEGATIVE_INFINITY|MagicToolboxTooltip|MessageBox|5000|Double|pinchstart|message|loading|120|btn|350|relative|2em|2147483647|dummy|256|controls|createDocumentFragment|pop|thumbnails|forward|100ms|030|220|isFinite|01|280|201|line|textAlign|availHeight|250|before|radius|availWidth|scrollTo|devicePixelRatio|800|z0|9_|rev|srcset|fontSize|fontFamily|sans|serif|color|contextmenu|getRatio|selectstart|MobileOptions|sineIn|POSITIVE_INFINITY|745|715|575|easeInOutSine|1000|curFrame|infinite|normal|roundCss|setTransition|445|085|675|215|matches|easeInOutCubic|055|955|easeInOutQuad|455|515|destroy|send|onwheel|wheel|mousewheel|URL|000244140625|deltaFactor|pinchupdate|delta|deltaZ|webkitURL|lengthComputable|createObjectURL|GET|responseType|blob|537|response|total|static|progress|645|565|running|easeInOutExpo|easeInCirc|easeInQuart|035|795|06|easeOutQuint|easeInOutQuint|07|335|easeOutCirc|zoomIn|easeInOutBack|265|switchTo|135|785|075|zoomOut|easeInOutCirc|855|04|easeInQuint|easeInOutQuart|easeOutQuart|755'.split('|'),0,{}))

function changeBg(a, img1, img2) {
    if (!a) {
        return true;
    }
    if (a.style.backgroundImage == "url(" + img1 + ")") {
        a.style.backgroundImage = "url(" + img2 + ")";
    } else {
        a.style.backgroundImage = "url(" + img1 + ")";
    }
    return true;
}
function toggleOn(a) {
    var e = document.getElementById(a);
    if (!e) {
        return true;
    }
    e.style.display = "block";
    return true;
}
function toggleOff(a) {
    var e = document.getElementById(a);
    if (!e) {
        return true;
    }
    e.style.display = "none";
    return true;
}
function toggle(a) {
    var e = document.getElementById(a);
    if (e.style.display != "block") {
        if (!e) {
            return true;
        }
        e.style.display = "block";
    } else {
        e.style.display = "none";
    }
}
function toggleOnnb(a) {
    var e = document.getElementById(a);
    if (!e) {
        return true;
    }
    e.style.display = "";
    return true;
}
function toggleOffnb(a) {
    var e = document.getElementById(a);
    if (!e) {
        return true;
    }
    e.style.display = "none";
    return true;
}
function togglenb(a) {
    var e = document.getElementById(a);
    if (e.style.display != "") {
        if (!e) {
            return true;
        }
        e.style.display = "";
    } else {
        e.style.display = "none";
    }
}
function toggleByClass(a) {
    var e = document.getElementByClassName(a);
    if (e.style.display != "block") {
        if (!e) {
            return true;
        }
        e.style.display = "block";
    } else {
        e.style.display = "none";
    }
}
function MM_jumpMenu(targ, selObj, restore) { //v3.0
    eval(targ + ".location='" + selObj.options[selObj.selectedIndex].value + "'");
    if (restore) {
        selObj.selectedIndex = 0;
    }
}
function openPopup(URL, WIDTH, HEIGHT) {
    if (!WIDTH) {
        WIDTH = 600;
    }
    if (!HEIGHT) {
        HEIGHT = 600;
    }
    WIDTH = 600;
    HEIGHT = 600;
    popup = window.open(URL, "popup", "width=" + WIDTH + ", height=" + HEIGHT + ", scrollbars=no");
}
function showLayer(lyr) {
    makeHistory(lyr);
    document.getElementById(currentLayer).className = 'hide';
    document.getElementById(lyr).className = 'show';
    currentLayer = lyr;
}
function showTab(lyr) {
    document.getElementById(currentTab).className = 'taboff';
    document.getElementById(lyr).className = 'tabon';
    currentTab = lyr;
}
function makeHistory(newHash) {
    window.location.hash = "_" + newHash;
    expectedHash = window.location.hash;
    return true;
}
function handleHistory() {
    if (window.location.hash != expectedHash) {
        expectedHash = window.location.hash;
        if (expectedHash.match('tab')) {
            showLayer(expectedHash.substring(2));
        }
    }
    return true;
}
function pollHash() {
    handleHistory();
    window.setInterval("handleHistory()", 200);
    return true;
}
function toggleDiv(id) {
    /*
     var obj=document.getElementsByTagName("div");
     for(i=0;i<obj.length;i++) {
     if(obj[i].collectionEntityClassName =="func") {
     obj[i].style.display="none";
     }
     }
     */
    if (document.getElementById(id).style.display == 'none') {
        document.getElementById(id).style.display = 'block';
        document.getElementById('head_' + id).className = 'function_cat open';
    } else {
        document.getElementById(id).style.display = 'none';
        document.getElementById('head_' + id).className = 'function_cat closed';
    }
}
function MM_swapImgRestore() { //v3.0
    var i, x, a = document.MM_sr;
    for (i = 0; a && i < a.length && (x = a[i]) && x.oSrc; i++) {
        x.src = x.oSrc;
    }
}
function MM_preloadImages() { //v3.0
    var d = document;
    if (d.images) {
        if (!d.MM_p) {
            d.MM_p = new Array();
        }
        var i, j = d.MM_p.length, a = MM_preloadImages.arguments;
        for (i = 0; i < a.length; i++) {
            if (a[i].indexOf("#") != 0) {
                d.MM_p[j] = new Image;
                d.MM_p[j++].src = a[i];
            }
        }
    }
}
function MM_findObj(n, d) { //v4.01
    var p, i, x;
    if (!d) {
        d = document;
    }
    if ((p = n.indexOf("?")) > 0 && parent.frames.length) {
        d = parent.frames[n.substring(p + 1)].document;
        n = n.substring(0, p);
    }
    if (!(x = d[n]) && d.all) {
        x = d.all[n];
    }
    for (i = 0; !x && i < d.forms.length; i++) {
        x = d.forms[i][n];
    }
    for (i = 0; !x && d.layers && i < d.layers.length; i++) {
        x = MM_findObj(n, d.layers[i].document);
    }
    if (!x && d.getElementById) {
        x = d.getElementById(n);
    }
    return x;
}
function MM_swapImage() { //v3.0
    var i, j = 0, x, a = MM_swapImage.arguments;
    document.MM_sr = new Array;
    for (i = 0; i < (a.length - 2); i += 3) {
        if ((x = MM_findObj(a[i])) != null) {
            document.MM_sr[j++] = x;
            if (!x.oSrc) {
                x.oSrc = x.src;
            }
            x.src = a[i + 2];
        }
    }
}
$(document).ready(function () {

    $('.modal-iframe').click(function () {
        e = $(this);
        $('iframe', '#iframeContent').load(e.data("target"));
        $('#iframeContent').modal({show: true})
    });

    $('.share_popup').on("click", function (e) {
        e.preventDefault();
        var t = $(this).data("target"), r = "_blank", i = "600", n = "460", a = "width=" + i + ",height=" + n;
        window.open(t, r, a)
    });

    /* 	$('#itemcard_order_button_form_indiv select').attr('disabled', 'disabled');
     $('#itemcard_order_button_form_std select').removeAttr('disabled');
     */
    $('.itemcard_order_button_radio_wrapper_inner').change('input', function () {
        if ($('#itemcard_order_type_choice_1').is(":checked")) {
            $('#itemcard_order_button_form_indiv_outer').addClass('itemcard_order_button_inactive');
            $('#itemcard_order_button_form_std_outer').removeClass('itemcard_order_button_inactive');
            $('div[u="slides"] > div:nth-child(2) > div > img[cust_img="0"]').click();
            $('#itemcard_order_button_form_indiv select').attr('disabled', 'disabled');
            $('#itemcard_order_button_form_std select').removeAttr('disabled');
        } else {
            $('#itemcard_order_button_form_indiv_outer').removeClass('itemcard_order_button_inactive');
            $('#itemcard_order_button_form_std_outer').addClass('itemcard_order_button_inactive');
            $('div[u="thumbnavigator"] img[cust_img="1"]').click();
            $('#itemcard_order_button_form_std select').attr('disabled', 'disabled');
            $('#itemcard_order_button_form_indiv select').removeAttr('disabled');
        }
    });
    $('.customize_delete_pic').click(function (e) {
        var obj = $(this);
        e.preventDefault();
        var request = $.ajax({
            url: '/module/dcshop/b2c/ajax.php' + $(obj).attr('href'),
            method: "POST"
        });

        request.done(function (msg) {
            var obj2 = $(obj).parents(".cust_img_area")
            $(obj2).html(msg);
            $(obj2).on("change", "input[type='file']", function () {
                $(obj2).find('input[type="text"]').val(this.files[0].name);
            })
        });
    });

    $('.slidecontent_headline').click(function () {
        $(this).toggleClass('active');
        $(this).next('.slidecontent_content_container').slideToggle('fast');
    });

    $('#header_shop_icons .header_icon_search').click(function () {
        $('#header_4').slideToggle('fast');
        $('#header_4 #input_search').focus();
    });
    $('.datepicker').datepicker({
        format: "dd.mm.yyyy",
        language: "de"
    });

    $("#form_search").submit(function (e) {
        if (!$("#form_search").find('#input_search').val()) {
            e.preventDefault(e);
            e.stopPropagation(e);
        }
    });

    var flashMessage = $('.flashMessage');
    var flashSpeed = 'slow';
    var flashDelay = '20000';
    $(window).load(function(){
        flashMessage.fadeIn(flashSpeed);

        $(function() {
            setTimeout(function() {
                flashMessage.fadeOut(flashSpeed);
            },flashDelay);
        });
        flashMessage.click(function(){
            $(this).fadeOut(flashSpeed);
        });
    });

    $('.jumpmark').click(function () {
        scrolling($(this).attr('href'));
    })
});

!function (a) {
    a.fn.datepicker.dates.de = {
        days: ["Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag"],
        daysShort: ["Son", "Mon", "Die", "Mit", "Don", "Fre", "Sam"],
        daysMin: ["So", "Mo", "Di", "Mi", "Do", "Fr", "Sa"],
        months: ["Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"],
        monthsShort: ["Jan", "Feb", "Mär", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dez"],
        today: "Heute",
        monthsTitle: "Monate",
        clear: "Löschen",
        weekStart: 1,
        format: "dd.mm.yyyy"
    }
}(jQuery);



function isTouchDevice() {
    var el = document.createElement('div');
    el.setAttribute('ontouchstart', 'return;');
    if(typeof el.ontouchstart == "function"){
        return true;
    }else {
        return false
    }
}


function scrolling(tabcontent_id) {
    var ziel = $(tabcontent_id);
    var top = ziel.offset().top;
    var heightHeader = $('#header').outerHeight();
    $('html,body').animate({
        scrollTop: top - heightHeader
    }, 800);
}
function set_background( imagelink ) {
    $('#coupon_background_image').css('background-image', 'url(' + imagelink + ')');
}

function set_background_active( background_id ) {
    $('.dc_background').removeClass('active');
    $('#dc_background_' + background_id).addClass('active');
    $('#input_background_image_id').val(background_id);
}

function set_amount_active( amount_id ) {
    $('.dc_amount').removeClass('active');
    $('#dc_amount_' + amount_id).addClass('active');
    $('#input_amount_id').val(amount_id);
    $('#input_coupon_amount').val('');
}

jQuery.fn.borderFlash = function ( color, duration, times ) {
    var current = this.css('border-color'),
        i = 1;
    do {
        this.animate({
                         borderTopColor   : 'rgb(' + color + ')',
                         borderLeftColor  : 'rgb(' + color + ')',
                         borderBottomColor: 'rgb(' + color + ')',
                         borderRightColor : 'rgb(' + color + ')'
                     }, duration / 2);
        this.animate({
                         borderTopColor   : current,
                         borderLeftColor  : current,
                         borderBottomColor: current,
                         borderRightColor : current
                     }, duration / 2);
        i++;
    } while (i <= times)
}

jQuery.fn.Flash = function ( color, duration, times ) {
    var current = this.css('background-color'),
        i = 1;
    do {
        this.animate({backgroundColor: 'rgb(' + color + ')'}, duration / 2);
        this.animate({backgroundColor: current}, duration / 2);
        i++;
    } while (i <= times)
}


$(document).ready(function () {
    $('#dc_shipping_option1').bind('click', function () {
        toggleOn('dc_shipping_option_inputs1');
        toggleOff('dc_shipping_option_inputs0');
    });
    $('#dc_shipping_option0').bind('click', function () {
        toggleOn('dc_shipping_option_inputs0');
        toggleOff('dc_shipping_option_inputs1');
    });

    $('#dc_message #message').on('keyup', function ( e ) {
        if (e.keyCode !== 8 && e.keyCode !== 46) {
            var text = $(this).val()
            textLen = text.length,
                totalLength = 0,
                newText = '';
            textCopy = text.replace(/\n/g, '~');
            ;
            console.log(textCopy);
            for (var i = 0, len = textCopy.length; i < len; i++) {
                if (textCopy[i] == '~') {
                    totalLength = totalLength + 85;
                } else {
                    totalLength++;
                }
                console.log("Total Length" + totalLength);
            }
            if (totalLength > 1000) {
                totalLength = 0;
                for (var i = 0, len = textCopy.length; (i < len) && (totalLength <= 1000); i++) {
                    if (textCopy[i] == '~') {
                        totalLength = totalLength + 85;
                        newText = newText + '\n';
                    } else {
                        totalLength++;
                        newText = newText + textCopy[i];
                    }
                }
                $('#dc_message #message').val(newText);
            }
        }
    });
});
