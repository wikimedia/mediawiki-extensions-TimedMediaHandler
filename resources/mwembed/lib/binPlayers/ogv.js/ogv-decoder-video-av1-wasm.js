
var OGVDecoderVideoAV1W = (function() {
  var _scriptDir = typeof document !== 'undefined' && document.currentScript ? document.currentScript.src : undefined;
  return (
function(OGVDecoderVideoAV1W) {
  OGVDecoderVideoAV1W = OGVDecoderVideoAV1W || {};

var a;a||(a=typeof OGVDecoderVideoAV1W !== 'undefined' ? OGVDecoderVideoAV1W : {});var p=a;a.memoryLimit&&(a.TOTAL_MEMORY=p.memoryLimit);var q={},v;for(v in a)a.hasOwnProperty(v)&&(q[v]=a[v]);a.arguments=[];a.thisProgram="./this.program";a.quit=function(b,c){throw c;};a.preRun=[];a.postRun=[];var w=!1,y=!1,z=!1,aa=!1;w="object"===typeof window;y="function"===typeof importScripts;z="object"===typeof process&&"function"===typeof require&&!w&&!y;aa=!w&&!z&&!y;var A="";
if(z){A=__dirname+"/";var B,C;a.read=function(b,c){B||(B=require("fs"));C||(C=require("path"));b=C.normalize(b);b=B.readFileSync(b);return c?b:b.toString()};a.readBinary=function(b){b=a.read(b,!0);b.buffer||(b=new Uint8Array(b));b.buffer||D("Assertion failed: undefined");return b};1<process.argv.length&&(a.thisProgram=process.argv[1].replace(/\\/g,"/"));a.arguments=process.argv.slice(2);process.on("unhandledRejection",D);a.quit=function(b){process.exit(b)};a.inspect=function(){return"[Emscripten Module object]"}}else if(aa)"undefined"!=
typeof read&&(a.read=function(b){return read(b)}),a.readBinary=function(b){if("function"===typeof readbuffer)return new Uint8Array(readbuffer(b));b=read(b,"binary");"object"===typeof b||D("Assertion failed: undefined");return b},"undefined"!=typeof scriptArgs?a.arguments=scriptArgs:"undefined"!=typeof arguments&&(a.arguments=arguments),"function"===typeof quit&&(a.quit=function(b){quit(b)});else if(w||y)y?A=self.location.href:document.currentScript&&(A=document.currentScript.src),_scriptDir&&(A=_scriptDir),
0!==A.indexOf("blob:")?A=A.substr(0,A.lastIndexOf("/")+1):A="",a.read=function(b){var c=new XMLHttpRequest;c.open("GET",b,!1);c.send(null);return c.responseText},y&&(a.readBinary=function(b){var c=new XMLHttpRequest;c.open("GET",b,!1);c.responseType="arraybuffer";c.send(null);return new Uint8Array(c.response)}),a.readAsync=function(b,c,d){var e=new XMLHttpRequest;e.open("GET",b,!0);e.responseType="arraybuffer";e.onload=function(){200==e.status||0==e.status&&e.response?c(e.response):d()};e.onerror=
d;e.send(null)},a.setWindowTitle=function(b){document.title=b};var G=a.print||("undefined"!==typeof console?console.log.bind(console):"undefined"!==typeof print?print:null),I=a.printErr||("undefined"!==typeof printErr?printErr:"undefined"!==typeof console&&console.warn.bind(console)||G);for(v in q)q.hasOwnProperty(v)&&(a[v]=q[v]);q=void 0;var ba={"f64-rem":function(b,c){return b%c},"debugger":function(){debugger}};"object"!==typeof WebAssembly&&I("no native wasm support detected");
var J,ca=!1,da="undefined"!==typeof TextDecoder?new TextDecoder("utf8"):void 0;"undefined"!==typeof TextDecoder&&new TextDecoder("utf-16le");function K(b){0<b%65536&&(b+=65536-b%65536);return b}var buffer,L,Q,ea;function fa(){a.HEAP8=new Int8Array(buffer);a.HEAP16=new Int16Array(buffer);a.HEAP32=Q=new Int32Array(buffer);a.HEAPU8=L=new Uint8Array(buffer);a.HEAPU16=new Uint16Array(buffer);a.HEAPU32=ea=new Uint32Array(buffer);a.HEAPF32=new Float32Array(buffer);a.HEAPF64=new Float64Array(buffer)}
var R=a.TOTAL_MEMORY||16777216;5242880>R&&I("TOTAL_MEMORY should be larger than TOTAL_STACK, was "+R+"! (TOTAL_STACK=5242880)");a.buffer?buffer=a.buffer:("object"===typeof WebAssembly&&"function"===typeof WebAssembly.Memory?(J=new WebAssembly.Memory({initial:R/65536}),buffer=J.buffer):buffer=new ArrayBuffer(R),a.buffer=buffer);fa();Q[97328]=5632448;
function S(b){for(;0<b.length;){var c=b.shift();if("function"==typeof c)c();else{var d=c.I;"number"===typeof d?void 0===c.C?a.dynCall_v(d):a.dynCall_vi(d,c.C):d(void 0===c.C?null:c.C)}}}var ha=[],ia=[],ja=[],ka=[],la=!1;function ma(){var b=a.preRun.shift();ha.unshift(b)}var T=0,na=null,U=null;a.preloadedImages={};a.preloadedAudios={};
function oa(){var b=V;return String.prototype.startsWith?b.startsWith("data:application/octet-stream;base64,"):0===b.indexOf("data:application/octet-stream;base64,")}var V="ogv-decoder-video-av1-wasm.wasm";if(!oa()){var pa=V;V=a.locateFile?a.locateFile(pa,A):A+pa}function qa(){try{if(a.wasmBinary)return new Uint8Array(a.wasmBinary);if(a.readBinary)return a.readBinary(V);throw"both async and sync fetching of the wasm failed";}catch(b){D(b)}}
function ra(){return a.wasmBinary||!w&&!y||"function"!==typeof fetch?new Promise(function(b){b(qa())}):fetch(V,{credentials:"same-origin"}).then(function(b){if(!b.ok)throw"failed to load wasm binary file at '"+V+"'";return b.arrayBuffer()}).catch(function(){return qa()})}
function ua(b){function c(b){a.asm=b.exports;T--;a.monitorRunDependencies&&a.monitorRunDependencies(T);0==T&&(null!==na&&(clearInterval(na),na=null),U&&(b=U,U=null,b()))}function d(b){c(b.instance)}function e(b){ra().then(function(b){return WebAssembly.instantiate(b,g)}).then(b,function(b){I("failed to asynchronously prepare wasm: "+b);D(b)})}var g={env:b,global:{NaN:NaN,Infinity:Infinity},"global.Math":Math,asm2wasm:ba};T++;a.monitorRunDependencies&&a.monitorRunDependencies(T);if(a.instantiateWasm)try{return a.instantiateWasm(g,
c)}catch(n){return I("Module.instantiateWasm callback failed with error: "+n),!1}a.wasmBinary||"function"!==typeof WebAssembly.instantiateStreaming||oa()||"function"!==typeof fetch?e(d):WebAssembly.instantiateStreaming(fetch(V,{credentials:"same-origin"}),g).then(d,function(b){I("wasm streaming compile failed: "+b);I("falling back to ArrayBuffer instantiation");e(d)});return{}}
a.asm=function(b,c){c.memory=J;c.table=new WebAssembly.Table({initial:418,maximum:418,element:"anyfunc"});c.__memory_base=1024;c.__table_base=0;return ua(c)};var va=[null,[],[]],W=0;function X(){W+=4;return Q[W-4>>2]}
var wa={},xa=a.asm({},{b:D,k:function(b){a.___errno_location&&(Q[a.___errno_location()>>2]=b);return b},q:function(b,c){W=c;try{var d=wa.G();X();var e=X(),g=X(),n=X();(void 0).J(d,e,n);Q[g>>2]=d.position;d.H&&0===e&&0===n&&(d.H=null);return 0}catch(f){return D(f),-f.F}},h:function(b,c){W=c;try{var d=X(),e=X(),g=X();for(c=b=0;c<g;c++){for(var n=Q[e+8*c>>2],f=Q[e+(8*c+4)>>2],x=0;x<f;x++){var E=L[n+x],t=va[d];if(0===E||10===E){var H=1===d?G:I;for(var h=t,k=0,r=k+void 0,u=k;h[u]&&!(u>=r);)++u;if(16<u-
k&&h.subarray&&da)var M=da.decode(h.subarray(k,u));else{for(r="";k<u;){var m=h[k++];if(m&128){var N=h[k++]&63;if(192==(m&224))r+=String.fromCharCode((m&31)<<6|N);else{var F=h[k++]&63;m=224==(m&240)?(m&15)<<12|N<<6|F:(m&7)<<18|N<<12|F<<6|h[k++]&63;if(65536>m)r+=String.fromCharCode(m);else{var O=m-65536;r+=String.fromCharCode(55296|O>>10,56320|O&1023)}}}else r+=String.fromCharCode(m)}M=r}H(M);t.length=0}else t.push(E)}b+=f}return b}catch(P){return D(P),-P.F}},p:function(b,c){W=c;return 0},o:function(b,
c){W=c;try{var d=wa.G();(void 0).close(d);return 0}catch(e){return D(e),-e.F}},g:function(){a.abort()},n:function(){return R},m:function(b,c,d){L.set(L.subarray(c,c+d),b)},l:function(b){if(2147418112<b)return!1;for(var c=Math.max(R,16777216);c<b;)536870912>=c?c=K(2*c):c=Math.min(K((3*c+2147483648)/4),2147418112);var d=K(c);var e=a.buffer.byteLength;try{var g=-1!==J.grow((d-e)/65536)?a.buffer=J.buffer:null}catch(n){g=null}if(!g||g.byteLength!=c)return!1;a.buffer=buffer=g;fa();R=c;ea[97328]=b;return!0},
s:function(b,c,d,e,g,n,f,x,E,t,H,h,k,r,u,M){function m(b,c,e,d,g,m,n,k){b=N.subarray(b,b+c*e);var l=b.buffer;"function"===typeof l.slice?(b=l.slice(b.byteOffset,b.byteOffset+b.byteLength),b=new Uint8Array(b)):b=new Uint8Array(b);var h,f;for(h=f=0;h<g;h++,f+=c)for(l=0;l<c;l++)b[f+l]=k;for(;h<g+n;h++,f+=c){for(l=0;l<d;l++)b[f+l]=k;for(l=d+m;l<c;l++)b[f+l]=k}for(;h<e;h++,f+=c)for(l=0;l<c;l++)b[f+l]=k;return b}var N=a.HEAPU8,F=a.videoFormat,O=(k&-2)*E/f,P=(r&-2)*t/x,sa=H*E/f,ta=h*t/x;H===F.cropWidth&&
h===F.cropHeight&&(u=F.displayWidth,M=F.displayHeight);a.frameBuffer={format:{width:f,height:x,chromaWidth:E,chromaHeight:t,cropLeft:k,cropTop:r,cropWidth:H,cropHeight:h,displayWidth:u,displayHeight:M},y:{bytes:m(b,c,x,k,r,H,h,0),stride:c},u:{bytes:m(d,e,t,O,P,sa,ta,128),stride:e},v:{bytes:m(g,n,t,O,P,sa,ta,128),stride:n}}},e:function(){return 0},f:function(){return 0},d:function(){return 0},c:function(){return 0},j:function(){return 11},i:function(){},r:function(){D("OOM")},a:389312},buffer);
a.asm=xa;a._free=function(){return a.asm.t.apply(null,arguments)};a._malloc=function(){return a.asm.u.apply(null,arguments)};a._ogv_video_decoder_async=function(){return a.asm.v.apply(null,arguments)};a._ogv_video_decoder_destroy=function(){return a.asm.w.apply(null,arguments)};a._ogv_video_decoder_init=function(){return a.asm.x.apply(null,arguments)};a._ogv_video_decoder_process_frame=function(){return a.asm.y.apply(null,arguments)};
a._ogv_video_decoder_process_header=function(){return a.asm.z.apply(null,arguments)};a.dynCall_v=function(){return a.asm.A.apply(null,arguments)};a.dynCall_vi=function(){return a.asm.B.apply(null,arguments)};a.asm=xa;a.then=function(b){if(a.calledRun)b(a);else{var c=a.onRuntimeInitialized;a.onRuntimeInitialized=function(){c&&c();b(a)}}return a};function ya(b){this.name="ExitStatus";this.message="Program terminated with exit("+b+")";this.status=b}ya.prototype=Error();ya.prototype.constructor=ya;
U=function za(){a.calledRun||Aa();a.calledRun||(U=za)};
function Aa(){function b(){if(!a.calledRun&&(a.calledRun=!0,!ca)){la||(la=!0,S(ia));S(ja);if(a.onRuntimeInitialized)a.onRuntimeInitialized();if(a.postRun)for("function"==typeof a.postRun&&(a.postRun=[a.postRun]);a.postRun.length;){var b=a.postRun.shift();ka.unshift(b)}S(ka)}}if(!(0<T)){if(a.preRun)for("function"==typeof a.preRun&&(a.preRun=[a.preRun]);a.preRun.length;)ma();S(ha);0<T||a.calledRun||(a.setStatus?(a.setStatus("Running..."),setTimeout(function(){setTimeout(function(){a.setStatus("")},1);
b()},1)):b())}}a.run=Aa;function D(b){if(a.onAbort)a.onAbort(b);void 0!==b?(G(b),I(b),b=JSON.stringify(b)):b="";ca=!0;throw"abort("+b+"). Build with -s ASSERTIONS=1 for more info.";}a.abort=D;if(a.preInit)for("function"==typeof a.preInit&&(a.preInit=[a.preInit]);0<a.preInit.length;)a.preInit.pop()();a.noExitRuntime=!0;Aa();var Y,Ba,Ca;Ca="undefined"===typeof performance||"undefined"===typeof performance.now?Date.now:performance.now.bind(performance);
function Z(b){var c=Ca();b=b();a.cpuTime+=Ca()-c;return b}a.loadedMetadata=!!p.videoFormat;a.videoFormat=p.videoFormat||null;a.frameBuffer=null;a.cpuTime=0;Object.defineProperty(a,"processing",{get:function(){return!1}});a.init=function(b){Z(function(){a._ogv_video_decoder_init()});b()};a.processHeader=function(b,c){var d=Z(function(){var c=b.byteLength;Y&&Ba>=c||(Y&&a._free(Y),Ba=c,Y=a._malloc(Ba));var d=Y;a.HEAPU8.set(new Uint8Array(b),d);return a._ogv_video_decoder_process_header(d,c)});c(d)};
a.D=[];a.processFrame=function(b,c){function d(b){a._free(n);c(b)}var e=a._ogv_video_decoder_async(),g=b.byteLength,n=a._malloc(g);e&&a.D.push(d);var f=Z(function(){a.HEAPU8.set(new Uint8Array(b),n);return a._ogv_video_decoder_process_frame(n,g)});e||d(f)};a.close=function(){};a.sync=function(){a._ogv_video_decoder_async()&&(a.D.push(function(){}),Z(function(){a._ogv_video_decoder_process_frame(0,0)}))};


  return OGVDecoderVideoAV1W
}
);
})();
if (typeof exports === 'object' && typeof module === 'object')
      module.exports = OGVDecoderVideoAV1W;
    else if (typeof define === 'function' && define['amd'])
      define([], function() { return OGVDecoderVideoAV1W; });
    else if (typeof exports === 'object')
      exports["OGVDecoderVideoAV1W"] = OGVDecoderVideoAV1W;
    