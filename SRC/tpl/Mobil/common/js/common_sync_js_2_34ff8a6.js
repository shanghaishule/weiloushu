define("common:static/js/searchdata.js",function(e,t,a){var c,n;c||(c=e("common:static/js/util.js")),n||(n=e("common:widget/geolocation/location.js"));var i={_cache:{length:0,index:0,data:{}},fetch:function(e,t,a){if(e)if(e=this._processUrl(e),this._getCacheData(e))t&&t(this._getCacheData(e));else{var c=this;$.ajax({url:"http://map.baidu.com"+e,dataType:"jsonp",success:function(n){try{var i=n;c._saveToCache(e,i),t&&t(i)}catch(o){a&&a()}},error:function(){a&&a()}})}},_processUrl:function(e){e=e.replace(/%3C/gi,encodeURIComponent(" ")).replace(/%3E/gi,encodeURIComponent(" "));var t="&from=maponline",a="&tn=m01",c="&ie=utf-8",n="&data_version=11252019",i="";return 0!==e.indexOf("/")&&(i="/mobile/?"),e=i+e+t+a+c+n},_getCacheData:function(e){return this._cache.data[e]&&this._cache.data[e].response||null},_saveToCache:function(e,t){this._cache.length>=i.MAX_CACHE&&this._removeOldData();var a=this._cache.index;this._cache.data[e]={index:a,response:t},this._cache.length++,this._cache.index++},_removeOldData:function(){var e=5,t=[];for(var a in this._cache.data)t.push({url:a,index:this._cache.data[a].index});t.sort(function(e,t){return e.index-t.index}),e=e>t.length?t.length:e;for(var c=0;e>c;c++)delete this._cache.data[t[c].url];this._cache.length-=e}};i.MAX_CACHE=10,a.exports=i});
;define("common:widget/imageloader/imageloader.js",function(n,i,e){"use strict";var o=parseInt(window._WISE_INFO.netype,10),a=window.devicePixelRatio,t=t||{};t={init:function(n,i){var e=this;o&&a&&function(){2!==o&&a>1&&0!==o&&e.loadRetinaBackground(n,i)}()},loadRetinaBackground:function(n,i){var e=new Image;e.src=i,e.onload=function(){$(n).css({"background-image":"url("+i+");"})},e=null}},e.exports=t});