var OGVDemuxerOgg = function(Module) {
  Module = Module || {};

var options = Module;
Module = {
 print: (function(str) {
  console.log(str);
 })
};
if (options["memoryLimit"]) {
 Module["TOTAL_MEMORY"] = options["memoryLimit"];
}
var Module;
if (!Module) Module = (typeof OGVDemuxerOgg !== "undefined" ? OGVDemuxerOgg : null) || {};
var moduleOverrides = {};
for (var key in Module) {
 if (Module.hasOwnProperty(key)) {
  moduleOverrides[key] = Module[key];
 }
}
var ENVIRONMENT_IS_WEB = false;
var ENVIRONMENT_IS_WORKER = false;
var ENVIRONMENT_IS_NODE = false;
var ENVIRONMENT_IS_SHELL = false;
if (Module["ENVIRONMENT"]) {
 if (Module["ENVIRONMENT"] === "WEB") {
  ENVIRONMENT_IS_WEB = true;
 } else if (Module["ENVIRONMENT"] === "WORKER") {
  ENVIRONMENT_IS_WORKER = true;
 } else if (Module["ENVIRONMENT"] === "NODE") {
  ENVIRONMENT_IS_NODE = true;
 } else if (Module["ENVIRONMENT"] === "SHELL") {
  ENVIRONMENT_IS_SHELL = true;
 } else {
  throw new Error("The provided Module['ENVIRONMENT'] value is not valid. It must be one of: WEB|WORKER|NODE|SHELL.");
 }
} else {
 ENVIRONMENT_IS_WEB = typeof window === "object";
 ENVIRONMENT_IS_WORKER = typeof importScripts === "function";
 ENVIRONMENT_IS_NODE = typeof process === "object" && typeof require === "function" && !ENVIRONMENT_IS_WEB && !ENVIRONMENT_IS_WORKER;
 ENVIRONMENT_IS_SHELL = !ENVIRONMENT_IS_WEB && !ENVIRONMENT_IS_NODE && !ENVIRONMENT_IS_WORKER;
}
if (ENVIRONMENT_IS_NODE) {
 if (!Module["print"]) Module["print"] = console.log;
 if (!Module["printErr"]) Module["printErr"] = console.warn;
 var nodeFS;
 var nodePath;
 Module["read"] = function read(filename, binary) {
  if (!nodeFS) nodeFS = require("fs");
  if (!nodePath) nodePath = require("path");
  filename = nodePath["normalize"](filename);
  var ret = nodeFS["readFileSync"](filename);
  if (!ret && filename != nodePath["resolve"](filename)) {
   filename = path.join(__dirname, "..", "src", filename);
   ret = nodeFS["readFileSync"](filename);
  }
  if (ret && !binary) ret = ret.toString();
  return ret;
 };
 Module["readBinary"] = function readBinary(filename) {
  var ret = Module["read"](filename, true);
  if (!ret.buffer) {
   ret = new Uint8Array(ret);
  }
  assert(ret.buffer);
  return ret;
 };
 Module["load"] = function load(f) {
  globalEval(read(f));
 };
 if (!Module["thisProgram"]) {
  if (process["argv"].length > 1) {
   Module["thisProgram"] = process["argv"][1].replace(/\\/g, "/");
  } else {
   Module["thisProgram"] = "unknown-program";
  }
 }
 Module["arguments"] = process["argv"].slice(2);
 if (typeof module !== "undefined") {
  module["exports"] = Module;
 }
 process["on"]("uncaughtException", (function(ex) {
  if (!(ex instanceof ExitStatus)) {
   throw ex;
  }
 }));
 Module["inspect"] = (function() {
  return "[Emscripten Module object]";
 });
} else if (ENVIRONMENT_IS_SHELL) {
 if (!Module["print"]) Module["print"] = print;
 if (typeof printErr != "undefined") Module["printErr"] = printErr;
 if (typeof read != "undefined") {
  Module["read"] = read;
 } else {
  Module["read"] = function read() {
   throw "no read() available (jsc?)";
  };
 }
 Module["readBinary"] = function readBinary(f) {
  if (typeof readbuffer === "function") {
   return new Uint8Array(readbuffer(f));
  }
  var data = read(f, "binary");
  assert(typeof data === "object");
  return data;
 };
 if (typeof scriptArgs != "undefined") {
  Module["arguments"] = scriptArgs;
 } else if (typeof arguments != "undefined") {
  Module["arguments"] = arguments;
 }
} else if (ENVIRONMENT_IS_WEB || ENVIRONMENT_IS_WORKER) {
 Module["read"] = function read(url) {
  var xhr = new XMLHttpRequest;
  xhr.open("GET", url, false);
  xhr.send(null);
  return xhr.responseText;
 };
 Module["readAsync"] = function readAsync(url, onload, onerror) {
  var xhr = new XMLHttpRequest;
  xhr.open("GET", url, true);
  xhr.responseType = "arraybuffer";
  xhr.onload = function xhr_onload() {
   if (xhr.status == 200 || xhr.status == 0 && xhr.response) {
    onload(xhr.response);
   } else {
    onerror();
   }
  };
  xhr.onerror = onerror;
  xhr.send(null);
 };
 if (typeof arguments != "undefined") {
  Module["arguments"] = arguments;
 }
 if (typeof console !== "undefined") {
  if (!Module["print"]) Module["print"] = function print(x) {
   console.log(x);
  };
  if (!Module["printErr"]) Module["printErr"] = function printErr(x) {
   console.warn(x);
  };
 } else {
  var TRY_USE_DUMP = false;
  if (!Module["print"]) Module["print"] = TRY_USE_DUMP && typeof dump !== "undefined" ? (function(x) {
   dump(x);
  }) : (function(x) {});
 }
 if (ENVIRONMENT_IS_WORKER) {
  Module["load"] = importScripts;
 }
 if (typeof Module["setWindowTitle"] === "undefined") {
  Module["setWindowTitle"] = (function(title) {
   document.title = title;
  });
 }
} else {
 throw "Unknown runtime environment. Where are we?";
}
function globalEval(x) {
 eval.call(null, x);
}
if (!Module["load"] && Module["read"]) {
 Module["load"] = function load(f) {
  globalEval(Module["read"](f));
 };
}
if (!Module["print"]) {
 Module["print"] = (function() {});
}
if (!Module["printErr"]) {
 Module["printErr"] = Module["print"];
}
if (!Module["arguments"]) {
 Module["arguments"] = [];
}
if (!Module["thisProgram"]) {
 Module["thisProgram"] = "./this.program";
}
Module.print = Module["print"];
Module.printErr = Module["printErr"];
Module["preRun"] = [];
Module["postRun"] = [];
for (var key in moduleOverrides) {
 if (moduleOverrides.hasOwnProperty(key)) {
  Module[key] = moduleOverrides[key];
 }
}
moduleOverrides = undefined;
var Runtime = {
 setTempRet0: (function(value) {
  tempRet0 = value;
 }),
 getTempRet0: (function() {
  return tempRet0;
 }),
 stackSave: (function() {
  return STACKTOP;
 }),
 stackRestore: (function(stackTop) {
  STACKTOP = stackTop;
 }),
 getNativeTypeSize: (function(type) {
  switch (type) {
  case "i1":
  case "i8":
   return 1;
  case "i16":
   return 2;
  case "i32":
   return 4;
  case "i64":
   return 8;
  case "float":
   return 4;
  case "double":
   return 8;
  default:
   {
    if (type[type.length - 1] === "*") {
     return Runtime.QUANTUM_SIZE;
    } else if (type[0] === "i") {
     var bits = parseInt(type.substr(1));
     assert(bits % 8 === 0);
     return bits / 8;
    } else {
     return 0;
    }
   }
  }
 }),
 getNativeFieldSize: (function(type) {
  return Math.max(Runtime.getNativeTypeSize(type), Runtime.QUANTUM_SIZE);
 }),
 STACK_ALIGN: 16,
 prepVararg: (function(ptr, type) {
  if (type === "double" || type === "i64") {
   if (ptr & 7) {
    assert((ptr & 7) === 4);
    ptr += 4;
   }
  } else {
   assert((ptr & 3) === 0);
  }
  return ptr;
 }),
 getAlignSize: (function(type, size, vararg) {
  if (!vararg && (type == "i64" || type == "double")) return 8;
  if (!type) return Math.min(size, 8);
  return Math.min(size || (type ? Runtime.getNativeFieldSize(type) : 0), Runtime.QUANTUM_SIZE);
 }),
 dynCall: (function(sig, ptr, args) {
  if (args && args.length) {
   if (!args.splice) args = Array.prototype.slice.call(args);
   args.splice(0, 0, ptr);
   return Module["dynCall_" + sig].apply(null, args);
  } else {
   return Module["dynCall_" + sig].call(null, ptr);
  }
 }),
 functionPointers: [],
 addFunction: (function(func) {
  for (var i = 0; i < Runtime.functionPointers.length; i++) {
   if (!Runtime.functionPointers[i]) {
    Runtime.functionPointers[i] = func;
    return 2 * (1 + i);
   }
  }
  throw "Finished up all reserved function pointers. Use a higher value for RESERVED_FUNCTION_POINTERS.";
 }),
 removeFunction: (function(index) {
  Runtime.functionPointers[(index - 2) / 2] = null;
 }),
 warnOnce: (function(text) {
  if (!Runtime.warnOnce.shown) Runtime.warnOnce.shown = {};
  if (!Runtime.warnOnce.shown[text]) {
   Runtime.warnOnce.shown[text] = 1;
   Module.printErr(text);
  }
 }),
 funcWrappers: {},
 getFuncWrapper: (function(func, sig) {
  assert(sig);
  if (!Runtime.funcWrappers[sig]) {
   Runtime.funcWrappers[sig] = {};
  }
  var sigCache = Runtime.funcWrappers[sig];
  if (!sigCache[func]) {
   sigCache[func] = function dynCall_wrapper() {
    return Runtime.dynCall(sig, func, arguments);
   };
  }
  return sigCache[func];
 }),
 getCompilerSetting: (function(name) {
  throw "You must build with -s RETAIN_COMPILER_SETTINGS=1 for Runtime.getCompilerSetting or emscripten_get_compiler_setting to work";
 }),
 stackAlloc: (function(size) {
  var ret = STACKTOP;
  STACKTOP = STACKTOP + size | 0;
  STACKTOP = STACKTOP + 15 & -16;
  return ret;
 }),
 staticAlloc: (function(size) {
  var ret = STATICTOP;
  STATICTOP = STATICTOP + size | 0;
  STATICTOP = STATICTOP + 15 & -16;
  return ret;
 }),
 dynamicAlloc: (function(size) {
  var ret = DYNAMICTOP;
  DYNAMICTOP = DYNAMICTOP + size | 0;
  DYNAMICTOP = DYNAMICTOP + 15 & -16;
  if (DYNAMICTOP >= TOTAL_MEMORY) {
   var success = enlargeMemory();
   if (!success) {
    DYNAMICTOP = ret;
    return 0;
   }
  }
  return ret;
 }),
 alignMemory: (function(size, quantum) {
  var ret = size = Math.ceil(size / (quantum ? quantum : 16)) * (quantum ? quantum : 16);
  return ret;
 }),
 makeBigInt: (function(low, high, unsigned) {
  var ret = unsigned ? +(low >>> 0) + +(high >>> 0) * +4294967296 : +(low >>> 0) + +(high | 0) * +4294967296;
  return ret;
 }),
 GLOBAL_BASE: 8,
 QUANTUM_SIZE: 4,
 __dummy__: 0
};
Module["Runtime"] = Runtime;
var ABORT = false;
var EXITSTATUS = 0;
function assert(condition, text) {
 if (!condition) {
  abort("Assertion failed: " + text);
 }
}
function getCFunc(ident) {
 var func = Module["_" + ident];
 if (!func) {
  try {
   func = eval("_" + ident);
  } catch (e) {}
 }
 assert(func, "Cannot call unknown function " + ident + " (perhaps LLVM optimizations or closure removed it?)");
 return func;
}
var cwrap, ccall;
((function() {
 var JSfuncs = {
  "stackSave": (function() {
   Runtime.stackSave();
  }),
  "stackRestore": (function() {
   Runtime.stackRestore();
  }),
  "arrayToC": (function(arr) {
   var ret = Runtime.stackAlloc(arr.length);
   writeArrayToMemory(arr, ret);
   return ret;
  }),
  "stringToC": (function(str) {
   var ret = 0;
   if (str !== null && str !== undefined && str !== 0) {
    ret = Runtime.stackAlloc((str.length << 2) + 1);
    writeStringToMemory(str, ret);
   }
   return ret;
  })
 };
 var toC = {
  "string": JSfuncs["stringToC"],
  "array": JSfuncs["arrayToC"]
 };
 ccall = function ccallFunc(ident, returnType, argTypes, args, opts) {
  var func = getCFunc(ident);
  var cArgs = [];
  var stack = 0;
  if (args) {
   for (var i = 0; i < args.length; i++) {
    var converter = toC[argTypes[i]];
    if (converter) {
     if (stack === 0) stack = Runtime.stackSave();
     cArgs[i] = converter(args[i]);
    } else {
     cArgs[i] = args[i];
    }
   }
  }
  var ret = func.apply(null, cArgs);
  if (returnType === "string") ret = Pointer_stringify(ret);
  if (stack !== 0) {
   if (opts && opts.async) {
    EmterpreterAsync.asyncFinalizers.push((function() {
     Runtime.stackRestore(stack);
    }));
    return;
   }
   Runtime.stackRestore(stack);
  }
  return ret;
 };
 var sourceRegex = /^function\s*[a-zA-Z$_0-9]*\s*\(([^)]*)\)\s*{\s*([^*]*?)[\s;]*(?:return\s*(.*?)[;\s]*)?}$/;
 function parseJSFunc(jsfunc) {
  var parsed = jsfunc.toString().match(sourceRegex).slice(1);
  return {
   arguments: parsed[0],
   body: parsed[1],
   returnValue: parsed[2]
  };
 }
 var JSsource = null;
 function ensureJSsource() {
  if (!JSsource) {
   JSsource = {};
   for (var fun in JSfuncs) {
    if (JSfuncs.hasOwnProperty(fun)) {
     JSsource[fun] = parseJSFunc(JSfuncs[fun]);
    }
   }
  }
 }
 cwrap = function cwrap(ident, returnType, argTypes) {
  argTypes = argTypes || [];
  var cfunc = getCFunc(ident);
  var numericArgs = argTypes.every((function(type) {
   return type === "number";
  }));
  var numericRet = returnType !== "string";
  if (numericRet && numericArgs) {
   return cfunc;
  }
  var argNames = argTypes.map((function(x, i) {
   return "$" + i;
  }));
  var funcstr = "(function(" + argNames.join(",") + ") {";
  var nargs = argTypes.length;
  if (!numericArgs) {
   ensureJSsource();
   funcstr += "var stack = " + JSsource["stackSave"].body + ";";
   for (var i = 0; i < nargs; i++) {
    var arg = argNames[i], type = argTypes[i];
    if (type === "number") continue;
    var convertCode = JSsource[type + "ToC"];
    funcstr += "var " + convertCode.arguments + " = " + arg + ";";
    funcstr += convertCode.body + ";";
    funcstr += arg + "=(" + convertCode.returnValue + ");";
   }
  }
  var cfuncname = parseJSFunc((function() {
   return cfunc;
  })).returnValue;
  funcstr += "var ret = " + cfuncname + "(" + argNames.join(",") + ");";
  if (!numericRet) {
   var strgfy = parseJSFunc((function() {
    return Pointer_stringify;
   })).returnValue;
   funcstr += "ret = " + strgfy + "(ret);";
  }
  if (!numericArgs) {
   ensureJSsource();
   funcstr += JSsource["stackRestore"].body.replace("()", "(stack)") + ";";
  }
  funcstr += "return ret})";
  return eval(funcstr);
 };
}))();
Module["ccall"] = ccall;
Module["cwrap"] = cwrap;
function setValue(ptr, value, type, noSafe) {
 type = type || "i8";
 if (type.charAt(type.length - 1) === "*") type = "i32";
 switch (type) {
 case "i1":
  HEAP8[ptr >> 0] = value;
  break;
 case "i8":
  HEAP8[ptr >> 0] = value;
  break;
 case "i16":
  HEAP16[ptr >> 1] = value;
  break;
 case "i32":
  HEAP32[ptr >> 2] = value;
  break;
 case "i64":
  tempI64 = [ value >>> 0, (tempDouble = value, +Math_abs(tempDouble) >= +1 ? tempDouble > +0 ? (Math_min(+Math_floor(tempDouble / +4294967296), +4294967295) | 0) >>> 0 : ~~+Math_ceil((tempDouble - +(~~tempDouble >>> 0)) / +4294967296) >>> 0 : 0) ], HEAP32[ptr >> 2] = tempI64[0], HEAP32[ptr + 4 >> 2] = tempI64[1];
  break;
 case "float":
  HEAPF32[ptr >> 2] = value;
  break;
 case "double":
  HEAPF64[ptr >> 3] = value;
  break;
 default:
  abort("invalid type for setValue: " + type);
 }
}
Module["setValue"] = setValue;
function getValue(ptr, type, noSafe) {
 type = type || "i8";
 if (type.charAt(type.length - 1) === "*") type = "i32";
 switch (type) {
 case "i1":
  return HEAP8[ptr >> 0];
 case "i8":
  return HEAP8[ptr >> 0];
 case "i16":
  return HEAP16[ptr >> 1];
 case "i32":
  return HEAP32[ptr >> 2];
 case "i64":
  return HEAP32[ptr >> 2];
 case "float":
  return HEAPF32[ptr >> 2];
 case "double":
  return HEAPF64[ptr >> 3];
 default:
  abort("invalid type for setValue: " + type);
 }
 return null;
}
Module["getValue"] = getValue;
var ALLOC_NORMAL = 0;
var ALLOC_STACK = 1;
var ALLOC_STATIC = 2;
var ALLOC_DYNAMIC = 3;
var ALLOC_NONE = 4;
Module["ALLOC_NORMAL"] = ALLOC_NORMAL;
Module["ALLOC_STACK"] = ALLOC_STACK;
Module["ALLOC_STATIC"] = ALLOC_STATIC;
Module["ALLOC_DYNAMIC"] = ALLOC_DYNAMIC;
Module["ALLOC_NONE"] = ALLOC_NONE;
function allocate(slab, types, allocator, ptr) {
 var zeroinit, size;
 if (typeof slab === "number") {
  zeroinit = true;
  size = slab;
 } else {
  zeroinit = false;
  size = slab.length;
 }
 var singleType = typeof types === "string" ? types : null;
 var ret;
 if (allocator == ALLOC_NONE) {
  ret = ptr;
 } else {
  ret = [ typeof _malloc === "function" ? _malloc : Runtime.staticAlloc, Runtime.stackAlloc, Runtime.staticAlloc, Runtime.dynamicAlloc ][allocator === undefined ? ALLOC_STATIC : allocator](Math.max(size, singleType ? 1 : types.length));
 }
 if (zeroinit) {
  var ptr = ret, stop;
  assert((ret & 3) == 0);
  stop = ret + (size & ~3);
  for (; ptr < stop; ptr += 4) {
   HEAP32[ptr >> 2] = 0;
  }
  stop = ret + size;
  while (ptr < stop) {
   HEAP8[ptr++ >> 0] = 0;
  }
  return ret;
 }
 if (singleType === "i8") {
  if (slab.subarray || slab.slice) {
   HEAPU8.set(slab, ret);
  } else {
   HEAPU8.set(new Uint8Array(slab), ret);
  }
  return ret;
 }
 var i = 0, type, typeSize, previousType;
 while (i < size) {
  var curr = slab[i];
  if (typeof curr === "function") {
   curr = Runtime.getFunctionIndex(curr);
  }
  type = singleType || types[i];
  if (type === 0) {
   i++;
   continue;
  }
  if (type == "i64") type = "i32";
  setValue(ret + i, curr, type);
  if (previousType !== type) {
   typeSize = Runtime.getNativeTypeSize(type);
   previousType = type;
  }
  i += typeSize;
 }
 return ret;
}
Module["allocate"] = allocate;
function getMemory(size) {
 if (!staticSealed) return Runtime.staticAlloc(size);
 if (typeof _sbrk !== "undefined" && !_sbrk.called || !runtimeInitialized) return Runtime.dynamicAlloc(size);
 return _malloc(size);
}
Module["getMemory"] = getMemory;
function Pointer_stringify(ptr, length) {
 if (length === 0 || !ptr) return "";
 var hasUtf = 0;
 var t;
 var i = 0;
 while (1) {
  t = HEAPU8[ptr + i >> 0];
  hasUtf |= t;
  if (t == 0 && !length) break;
  i++;
  if (length && i == length) break;
 }
 if (!length) length = i;
 var ret = "";
 if (hasUtf < 128) {
  var MAX_CHUNK = 1024;
  var curr;
  while (length > 0) {
   curr = String.fromCharCode.apply(String, HEAPU8.subarray(ptr, ptr + Math.min(length, MAX_CHUNK)));
   ret = ret ? ret + curr : curr;
   ptr += MAX_CHUNK;
   length -= MAX_CHUNK;
  }
  return ret;
 }
 return Module["UTF8ToString"](ptr);
}
Module["Pointer_stringify"] = Pointer_stringify;
function AsciiToString(ptr) {
 var str = "";
 while (1) {
  var ch = HEAP8[ptr++ >> 0];
  if (!ch) return str;
  str += String.fromCharCode(ch);
 }
}
Module["AsciiToString"] = AsciiToString;
function stringToAscii(str, outPtr) {
 return writeAsciiToMemory(str, outPtr, false);
}
Module["stringToAscii"] = stringToAscii;
function UTF8ArrayToString(u8Array, idx) {
 var u0, u1, u2, u3, u4, u5;
 var str = "";
 while (1) {
  u0 = u8Array[idx++];
  if (!u0) return str;
  if (!(u0 & 128)) {
   str += String.fromCharCode(u0);
   continue;
  }
  u1 = u8Array[idx++] & 63;
  if ((u0 & 224) == 192) {
   str += String.fromCharCode((u0 & 31) << 6 | u1);
   continue;
  }
  u2 = u8Array[idx++] & 63;
  if ((u0 & 240) == 224) {
   u0 = (u0 & 15) << 12 | u1 << 6 | u2;
  } else {
   u3 = u8Array[idx++] & 63;
   if ((u0 & 248) == 240) {
    u0 = (u0 & 7) << 18 | u1 << 12 | u2 << 6 | u3;
   } else {
    u4 = u8Array[idx++] & 63;
    if ((u0 & 252) == 248) {
     u0 = (u0 & 3) << 24 | u1 << 18 | u2 << 12 | u3 << 6 | u4;
    } else {
     u5 = u8Array[idx++] & 63;
     u0 = (u0 & 1) << 30 | u1 << 24 | u2 << 18 | u3 << 12 | u4 << 6 | u5;
    }
   }
  }
  if (u0 < 65536) {
   str += String.fromCharCode(u0);
  } else {
   var ch = u0 - 65536;
   str += String.fromCharCode(55296 | ch >> 10, 56320 | ch & 1023);
  }
 }
}
Module["UTF8ArrayToString"] = UTF8ArrayToString;
function UTF8ToString(ptr) {
 return UTF8ArrayToString(HEAPU8, ptr);
}
Module["UTF8ToString"] = UTF8ToString;
function stringToUTF8Array(str, outU8Array, outIdx, maxBytesToWrite) {
 if (!(maxBytesToWrite > 0)) return 0;
 var startIdx = outIdx;
 var endIdx = outIdx + maxBytesToWrite - 1;
 for (var i = 0; i < str.length; ++i) {
  var u = str.charCodeAt(i);
  if (u >= 55296 && u <= 57343) u = 65536 + ((u & 1023) << 10) | str.charCodeAt(++i) & 1023;
  if (u <= 127) {
   if (outIdx >= endIdx) break;
   outU8Array[outIdx++] = u;
  } else if (u <= 2047) {
   if (outIdx + 1 >= endIdx) break;
   outU8Array[outIdx++] = 192 | u >> 6;
   outU8Array[outIdx++] = 128 | u & 63;
  } else if (u <= 65535) {
   if (outIdx + 2 >= endIdx) break;
   outU8Array[outIdx++] = 224 | u >> 12;
   outU8Array[outIdx++] = 128 | u >> 6 & 63;
   outU8Array[outIdx++] = 128 | u & 63;
  } else if (u <= 2097151) {
   if (outIdx + 3 >= endIdx) break;
   outU8Array[outIdx++] = 240 | u >> 18;
   outU8Array[outIdx++] = 128 | u >> 12 & 63;
   outU8Array[outIdx++] = 128 | u >> 6 & 63;
   outU8Array[outIdx++] = 128 | u & 63;
  } else if (u <= 67108863) {
   if (outIdx + 4 >= endIdx) break;
   outU8Array[outIdx++] = 248 | u >> 24;
   outU8Array[outIdx++] = 128 | u >> 18 & 63;
   outU8Array[outIdx++] = 128 | u >> 12 & 63;
   outU8Array[outIdx++] = 128 | u >> 6 & 63;
   outU8Array[outIdx++] = 128 | u & 63;
  } else {
   if (outIdx + 5 >= endIdx) break;
   outU8Array[outIdx++] = 252 | u >> 30;
   outU8Array[outIdx++] = 128 | u >> 24 & 63;
   outU8Array[outIdx++] = 128 | u >> 18 & 63;
   outU8Array[outIdx++] = 128 | u >> 12 & 63;
   outU8Array[outIdx++] = 128 | u >> 6 & 63;
   outU8Array[outIdx++] = 128 | u & 63;
  }
 }
 outU8Array[outIdx] = 0;
 return outIdx - startIdx;
}
Module["stringToUTF8Array"] = stringToUTF8Array;
function stringToUTF8(str, outPtr, maxBytesToWrite) {
 return stringToUTF8Array(str, HEAPU8, outPtr, maxBytesToWrite);
}
Module["stringToUTF8"] = stringToUTF8;
function lengthBytesUTF8(str) {
 var len = 0;
 for (var i = 0; i < str.length; ++i) {
  var u = str.charCodeAt(i);
  if (u >= 55296 && u <= 57343) u = 65536 + ((u & 1023) << 10) | str.charCodeAt(++i) & 1023;
  if (u <= 127) {
   ++len;
  } else if (u <= 2047) {
   len += 2;
  } else if (u <= 65535) {
   len += 3;
  } else if (u <= 2097151) {
   len += 4;
  } else if (u <= 67108863) {
   len += 5;
  } else {
   len += 6;
  }
 }
 return len;
}
Module["lengthBytesUTF8"] = lengthBytesUTF8;
function demangle(func) {
 var hasLibcxxabi = !!Module["___cxa_demangle"];
 if (hasLibcxxabi) {
  try {
   var buf = _malloc(func.length);
   writeStringToMemory(func.substr(1), buf);
   var status = _malloc(4);
   var ret = Module["___cxa_demangle"](buf, 0, 0, status);
   if (getValue(status, "i32") === 0 && ret) {
    return Pointer_stringify(ret);
   }
  } catch (e) {
   return func;
  } finally {
   if (buf) _free(buf);
   if (status) _free(status);
   if (ret) _free(ret);
  }
 }
 Runtime.warnOnce("warning: build with  -s DEMANGLE_SUPPORT=1  to link in libcxxabi demangling");
 return func;
}
function demangleAll(text) {
 return text.replace(/__Z[\w\d_]+/g, (function(x) {
  var y = demangle(x);
  return x === y ? x : x + " [" + y + "]";
 }));
}
function jsStackTrace() {
 var err = new Error;
 if (!err.stack) {
  try {
   throw new Error(0);
  } catch (e) {
   err = e;
  }
  if (!err.stack) {
   return "(no stack trace available)";
  }
 }
 return err.stack.toString();
}
function stackTrace() {
 return demangleAll(jsStackTrace());
}
Module["stackTrace"] = stackTrace;
var PAGE_SIZE = 4096;
function alignMemoryPage(x) {
 if (x % 4096 > 0) {
  x += 4096 - x % 4096;
 }
 return x;
}
var HEAP;
var buffer;
var HEAP8, HEAPU8, HEAP16, HEAPU16, HEAP32, HEAPU32, HEAPF32, HEAPF64;
function updateGlobalBufferViews() {
 Module["HEAP8"] = HEAP8 = new Int8Array(buffer);
 Module["HEAP16"] = HEAP16 = new Int16Array(buffer);
 Module["HEAP32"] = HEAP32 = new Int32Array(buffer);
 Module["HEAPU8"] = HEAPU8 = new Uint8Array(buffer);
 Module["HEAPU16"] = HEAPU16 = new Uint16Array(buffer);
 Module["HEAPU32"] = HEAPU32 = new Uint32Array(buffer);
 Module["HEAPF32"] = HEAPF32 = new Float32Array(buffer);
 Module["HEAPF64"] = HEAPF64 = new Float64Array(buffer);
}
var STATIC_BASE = 0, STATICTOP = 0, staticSealed = false;
var STACK_BASE = 0, STACKTOP = 0, STACK_MAX = 0;
var DYNAMIC_BASE = 0, DYNAMICTOP = 0;
function abortOnCannotGrowMemory() {
 abort("Cannot enlarge memory arrays. Either (1) compile with  -s TOTAL_MEMORY=X  with X higher than the current value " + TOTAL_MEMORY + ", (2) compile with  -s ALLOW_MEMORY_GROWTH=1  which adjusts the size at runtime but prevents some optimizations, (3) set Module.TOTAL_MEMORY to a higher value before the program runs, or if you want malloc to return NULL (0) instead of this abort, compile with  -s ABORTING_MALLOC=0 ");
}
function enlargeMemory() {
 abortOnCannotGrowMemory();
}
var TOTAL_STACK = Module["TOTAL_STACK"] || 5242880;
var TOTAL_MEMORY = Module["TOTAL_MEMORY"] || 16777216;
var totalMemory = 64 * 1024;
while (totalMemory < TOTAL_MEMORY || totalMemory < 2 * TOTAL_STACK) {
 if (totalMemory < 16 * 1024 * 1024) {
  totalMemory *= 2;
 } else {
  totalMemory += 16 * 1024 * 1024;
 }
}
if (totalMemory !== TOTAL_MEMORY) {
 TOTAL_MEMORY = totalMemory;
}
if (Module["buffer"]) {
 buffer = Module["buffer"];
} else {
 buffer = new ArrayBuffer(TOTAL_MEMORY);
}
updateGlobalBufferViews();
HEAP32[0] = 255;
if (HEAPU8[0] !== 255 || HEAPU8[3] !== 0) throw "Typed arrays 2 must be run on a little-endian system";
Module["HEAP"] = HEAP;
Module["buffer"] = buffer;
Module["HEAP8"] = HEAP8;
Module["HEAP16"] = HEAP16;
Module["HEAP32"] = HEAP32;
Module["HEAPU8"] = HEAPU8;
Module["HEAPU16"] = HEAPU16;
Module["HEAPU32"] = HEAPU32;
Module["HEAPF32"] = HEAPF32;
Module["HEAPF64"] = HEAPF64;
function callRuntimeCallbacks(callbacks) {
 while (callbacks.length > 0) {
  var callback = callbacks.shift();
  if (typeof callback == "function") {
   callback();
   continue;
  }
  var func = callback.func;
  if (typeof func === "number") {
   if (callback.arg === undefined) {
    Runtime.dynCall("v", func);
   } else {
    Runtime.dynCall("vi", func, [ callback.arg ]);
   }
  } else {
   func(callback.arg === undefined ? null : callback.arg);
  }
 }
}
var __ATPRERUN__ = [];
var __ATINIT__ = [];
var __ATMAIN__ = [];
var __ATEXIT__ = [];
var __ATPOSTRUN__ = [];
var runtimeInitialized = false;
var runtimeExited = false;
function preRun() {
 if (Module["preRun"]) {
  if (typeof Module["preRun"] == "function") Module["preRun"] = [ Module["preRun"] ];
  while (Module["preRun"].length) {
   addOnPreRun(Module["preRun"].shift());
  }
 }
 callRuntimeCallbacks(__ATPRERUN__);
}
function ensureInitRuntime() {
 if (runtimeInitialized) return;
 runtimeInitialized = true;
 callRuntimeCallbacks(__ATINIT__);
}
function preMain() {
 callRuntimeCallbacks(__ATMAIN__);
}
function exitRuntime() {
 callRuntimeCallbacks(__ATEXIT__);
 runtimeExited = true;
}
function postRun() {
 if (Module["postRun"]) {
  if (typeof Module["postRun"] == "function") Module["postRun"] = [ Module["postRun"] ];
  while (Module["postRun"].length) {
   addOnPostRun(Module["postRun"].shift());
  }
 }
 callRuntimeCallbacks(__ATPOSTRUN__);
}
function addOnPreRun(cb) {
 __ATPRERUN__.unshift(cb);
}
Module["addOnPreRun"] = addOnPreRun;
function addOnInit(cb) {
 __ATINIT__.unshift(cb);
}
Module["addOnInit"] = addOnInit;
function addOnPreMain(cb) {
 __ATMAIN__.unshift(cb);
}
Module["addOnPreMain"] = addOnPreMain;
function addOnExit(cb) {
 __ATEXIT__.unshift(cb);
}
Module["addOnExit"] = addOnExit;
function addOnPostRun(cb) {
 __ATPOSTRUN__.unshift(cb);
}
Module["addOnPostRun"] = addOnPostRun;
function intArrayFromString(stringy, dontAddNull, length) {
 var len = length > 0 ? length : lengthBytesUTF8(stringy) + 1;
 var u8array = new Array(len);
 var numBytesWritten = stringToUTF8Array(stringy, u8array, 0, u8array.length);
 if (dontAddNull) u8array.length = numBytesWritten;
 return u8array;
}
Module["intArrayFromString"] = intArrayFromString;
function intArrayToString(array) {
 var ret = [];
 for (var i = 0; i < array.length; i++) {
  var chr = array[i];
  if (chr > 255) {
   chr &= 255;
  }
  ret.push(String.fromCharCode(chr));
 }
 return ret.join("");
}
Module["intArrayToString"] = intArrayToString;
function writeStringToMemory(string, buffer, dontAddNull) {
 var array = intArrayFromString(string, dontAddNull);
 var i = 0;
 while (i < array.length) {
  var chr = array[i];
  HEAP8[buffer + i >> 0] = chr;
  i = i + 1;
 }
}
Module["writeStringToMemory"] = writeStringToMemory;
function writeArrayToMemory(array, buffer) {
 for (var i = 0; i < array.length; i++) {
  HEAP8[buffer++ >> 0] = array[i];
 }
}
Module["writeArrayToMemory"] = writeArrayToMemory;
function writeAsciiToMemory(str, buffer, dontAddNull) {
 for (var i = 0; i < str.length; ++i) {
  HEAP8[buffer++ >> 0] = str.charCodeAt(i);
 }
 if (!dontAddNull) HEAP8[buffer >> 0] = 0;
}
Module["writeAsciiToMemory"] = writeAsciiToMemory;
if (!Math["imul"] || Math["imul"](4294967295, 5) !== -5) Math["imul"] = function imul(a, b) {
 var ah = a >>> 16;
 var al = a & 65535;
 var bh = b >>> 16;
 var bl = b & 65535;
 return al * bl + (ah * bl + al * bh << 16) | 0;
};
Math.imul = Math["imul"];
if (!Math["clz32"]) Math["clz32"] = (function(x) {
 x = x >>> 0;
 for (var i = 0; i < 32; i++) {
  if (x & 1 << 31 - i) return i;
 }
 return 32;
});
Math.clz32 = Math["clz32"];
var Math_abs = Math.abs;
var Math_cos = Math.cos;
var Math_sin = Math.sin;
var Math_tan = Math.tan;
var Math_acos = Math.acos;
var Math_asin = Math.asin;
var Math_atan = Math.atan;
var Math_atan2 = Math.atan2;
var Math_exp = Math.exp;
var Math_log = Math.log;
var Math_sqrt = Math.sqrt;
var Math_ceil = Math.ceil;
var Math_floor = Math.floor;
var Math_pow = Math.pow;
var Math_imul = Math.imul;
var Math_fround = Math.fround;
var Math_min = Math.min;
var Math_clz32 = Math.clz32;
var runDependencies = 0;
var runDependencyWatcher = null;
var dependenciesFulfilled = null;
function addRunDependency(id) {
 runDependencies++;
 if (Module["monitorRunDependencies"]) {
  Module["monitorRunDependencies"](runDependencies);
 }
}
Module["addRunDependency"] = addRunDependency;
function removeRunDependency(id) {
 runDependencies--;
 if (Module["monitorRunDependencies"]) {
  Module["monitorRunDependencies"](runDependencies);
 }
 if (runDependencies == 0) {
  if (runDependencyWatcher !== null) {
   clearInterval(runDependencyWatcher);
   runDependencyWatcher = null;
  }
  if (dependenciesFulfilled) {
   var callback = dependenciesFulfilled;
   dependenciesFulfilled = null;
   callback();
  }
 }
}
Module["removeRunDependency"] = removeRunDependency;
Module["preloadedImages"] = {};
Module["preloadedAudios"] = {};
var ASM_CONSTS = [];
STATIC_BASE = 8;
STATICTOP = STATIC_BASE + 6928;
__ATINIT__.push();
allocate([ 0, 0, 0, 0, 183, 29, 193, 4, 110, 59, 130, 9, 217, 38, 67, 13, 220, 118, 4, 19, 107, 107, 197, 23, 178, 77, 134, 26, 5, 80, 71, 30, 184, 237, 8, 38, 15, 240, 201, 34, 214, 214, 138, 47, 97, 203, 75, 43, 100, 155, 12, 53, 211, 134, 205, 49, 10, 160, 142, 60, 189, 189, 79, 56, 112, 219, 17, 76, 199, 198, 208, 72, 30, 224, 147, 69, 169, 253, 82, 65, 172, 173, 21, 95, 27, 176, 212, 91, 194, 150, 151, 86, 117, 139, 86, 82, 200, 54, 25, 106, 127, 43, 216, 110, 166, 13, 155, 99, 17, 16, 90, 103, 20, 64, 29, 121, 163, 93, 220, 125, 122, 123, 159, 112, 205, 102, 94, 116, 224, 182, 35, 152, 87, 171, 226, 156, 142, 141, 161, 145, 57, 144, 96, 149, 60, 192, 39, 139, 139, 221, 230, 143, 82, 251, 165, 130, 229, 230, 100, 134, 88, 91, 43, 190, 239, 70, 234, 186, 54, 96, 169, 183, 129, 125, 104, 179, 132, 45, 47, 173, 51, 48, 238, 169, 234, 22, 173, 164, 93, 11, 108, 160, 144, 109, 50, 212, 39, 112, 243, 208, 254, 86, 176, 221, 73, 75, 113, 217, 76, 27, 54, 199, 251, 6, 247, 195, 34, 32, 180, 206, 149, 61, 117, 202, 40, 128, 58, 242, 159, 157, 251, 246, 70, 187, 184, 251, 241, 166, 121, 255, 244, 246, 62, 225, 67, 235, 255, 229, 154, 205, 188, 232, 45, 208, 125, 236, 119, 112, 134, 52, 192, 109, 71, 48, 25, 75, 4, 61, 174, 86, 197, 57, 171, 6, 130, 39, 28, 27, 67, 35, 197, 61, 0, 46, 114, 32, 193, 42, 207, 157, 142, 18, 120, 128, 79, 22, 161, 166, 12, 27, 22, 187, 205, 31, 19, 235, 138, 1, 164, 246, 75, 5, 125, 208, 8, 8, 202, 205, 201, 12, 7, 171, 151, 120, 176, 182, 86, 124, 105, 144, 21, 113, 222, 141, 212, 117, 219, 221, 147, 107, 108, 192, 82, 111, 181, 230, 17, 98, 2, 251, 208, 102, 191, 70, 159, 94, 8, 91, 94, 90, 209, 125, 29, 87, 102, 96, 220, 83, 99, 48, 155, 77, 212, 45, 90, 73, 13, 11, 25, 68, 186, 22, 216, 64, 151, 198, 165, 172, 32, 219, 100, 168, 249, 253, 39, 165, 78, 224, 230, 161, 75, 176, 161, 191, 252, 173, 96, 187, 37, 139, 35, 182, 146, 150, 226, 178, 47, 43, 173, 138, 152, 54, 108, 142, 65, 16, 47, 131, 246, 13, 238, 135, 243, 93, 169, 153, 68, 64, 104, 157, 157, 102, 43, 144, 42, 123, 234, 148, 231, 29, 180, 224, 80, 0, 117, 228, 137, 38, 54, 233, 62, 59, 247, 237, 59, 107, 176, 243, 140, 118, 113, 247, 85, 80, 50, 250, 226, 77, 243, 254, 95, 240, 188, 198, 232, 237, 125, 194, 49, 203, 62, 207, 134, 214, 255, 203, 131, 134, 184, 213, 52, 155, 121, 209, 237, 189, 58, 220, 90, 160, 251, 216, 238, 224, 12, 105, 89, 253, 205, 109, 128, 219, 142, 96, 55, 198, 79, 100, 50, 150, 8, 122, 133, 139, 201, 126, 92, 173, 138, 115, 235, 176, 75, 119, 86, 13, 4, 79, 225, 16, 197, 75, 56, 54, 134, 70, 143, 43, 71, 66, 138, 123, 0, 92, 61, 102, 193, 88, 228, 64, 130, 85, 83, 93, 67, 81, 158, 59, 29, 37, 41, 38, 220, 33, 240, 0, 159, 44, 71, 29, 94, 40, 66, 77, 25, 54, 245, 80, 216, 50, 44, 118, 155, 63, 155, 107, 90, 59, 38, 214, 21, 3, 145, 203, 212, 7, 72, 237, 151, 10, 255, 240, 86, 14, 250, 160, 17, 16, 77, 189, 208, 20, 148, 155, 147, 25, 35, 134, 82, 29, 14, 86, 47, 241, 185, 75, 238, 245, 96, 109, 173, 248, 215, 112, 108, 252, 210, 32, 43, 226, 101, 61, 234, 230, 188, 27, 169, 235, 11, 6, 104, 239, 182, 187, 39, 215, 1, 166, 230, 211, 216, 128, 165, 222, 111, 157, 100, 218, 106, 205, 35, 196, 221, 208, 226, 192, 4, 246, 161, 205, 179, 235, 96, 201, 126, 141, 62, 189, 201, 144, 255, 185, 16, 182, 188, 180, 167, 171, 125, 176, 162, 251, 58, 174, 21, 230, 251, 170, 204, 192, 184, 167, 123, 221, 121, 163, 198, 96, 54, 155, 113, 125, 247, 159, 168, 91, 180, 146, 31, 70, 117, 150, 26, 22, 50, 136, 173, 11, 243, 140, 116, 45, 176, 129, 195, 48, 113, 133, 153, 144, 138, 93, 46, 141, 75, 89, 247, 171, 8, 84, 64, 182, 201, 80, 69, 230, 142, 78, 242, 251, 79, 74, 43, 221, 12, 71, 156, 192, 205, 67, 33, 125, 130, 123, 150, 96, 67, 127, 79, 70, 0, 114, 248, 91, 193, 118, 253, 11, 134, 104, 74, 22, 71, 108, 147, 48, 4, 97, 36, 45, 197, 101, 233, 75, 155, 17, 94, 86, 90, 21, 135, 112, 25, 24, 48, 109, 216, 28, 53, 61, 159, 2, 130, 32, 94, 6, 91, 6, 29, 11, 236, 27, 220, 15, 81, 166, 147, 55, 230, 187, 82, 51, 63, 157, 17, 62, 136, 128, 208, 58, 141, 208, 151, 36, 58, 205, 86, 32, 227, 235, 21, 45, 84, 246, 212, 41, 121, 38, 169, 197, 206, 59, 104, 193, 23, 29, 43, 204, 160, 0, 234, 200, 165, 80, 173, 214, 18, 77, 108, 210, 203, 107, 47, 223, 124, 118, 238, 219, 193, 203, 161, 227, 118, 214, 96, 231, 175, 240, 35, 234, 24, 237, 226, 238, 29, 189, 165, 240, 170, 160, 100, 244, 115, 134, 39, 249, 196, 155, 230, 253, 9, 253, 184, 137, 190, 224, 121, 141, 103, 198, 58, 128, 208, 219, 251, 132, 213, 139, 188, 154, 98, 150, 125, 158, 187, 176, 62, 147, 12, 173, 255, 151, 177, 16, 176, 175, 6, 13, 113, 171, 223, 43, 50, 166, 104, 54, 243, 162, 109, 102, 180, 188, 218, 123, 117, 184, 3, 93, 54, 181, 180, 64, 247, 177, 229, 8, 0, 0, 7, 0, 0, 0, 237, 8, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0, 2, 0, 0, 0, 244, 8, 0, 0, 7, 0, 0, 0, 252, 8, 0, 0, 3, 0, 0, 0, 2, 0, 0, 0, 4, 0, 0, 0, 3, 9, 0, 0, 5, 0, 0, 0, 3, 9, 0, 0, 5, 0, 0, 0, 3, 0, 0, 0, 0, 0, 0, 0, 9, 9, 0, 0, 8, 0, 0, 0, 18, 9, 0, 0, 6, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 22, 9, 0, 0, 8, 0, 0, 0, 31, 9, 0, 0, 7, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 36, 9, 0, 0, 7, 0, 0, 0, 36, 9, 0, 0, 8, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 44, 9, 0, 0, 7, 0, 0, 0, 52, 9, 0, 0, 9, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 61, 9, 0, 0, 4, 0, 0, 0, 66, 9, 0, 0, 10, 0, 0, 0, 4, 0, 0, 0, 0, 0, 0, 0, 72, 9, 0, 0, 5, 0, 0, 0, 78, 9, 0, 0, 11, 0, 0, 0, 4, 0, 0, 0, 0, 0, 0, 0, 83, 9, 0, 0, 7, 0, 0, 0, 83, 9, 0, 0, 12, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 91, 9, 0, 0, 8, 0, 0, 0, 100, 9, 0, 0, 13, 0, 0, 0, 5, 0, 0, 0, 0, 0, 0, 0, 105, 9, 0, 0, 8, 0, 0, 0, 114, 9, 0, 0, 14, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 119, 9, 0, 0, 5, 0, 0, 0, 125, 9, 0, 0, 15, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 131, 9, 0, 0, 8, 0, 0, 0, 140, 9, 0, 0, 16, 0, 0, 0, 6, 0, 0, 0, 17, 0, 0, 0, 145, 9, 0, 0, 5, 0, 0, 0, 151, 9, 0, 0, 18, 0, 0, 0, 7, 0, 0, 0, 0, 0, 0, 0, 252, 22, 0, 0, 0, 0, 0, 0, 160, 9, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 3, 0, 0, 0, 7, 0, 0, 0, 15, 0, 0, 0, 31, 0, 0, 0, 63, 0, 0, 0, 127, 0, 0, 0, 255, 0, 0, 0, 255, 1, 0, 0, 255, 3, 0, 0, 255, 7, 0, 0, 255, 15, 0, 0, 255, 31, 0, 0, 255, 63, 0, 0, 255, 127, 0, 0, 255, 255, 0, 0, 255, 255, 1, 0, 255, 255, 3, 0, 255, 255, 7, 0, 255, 255, 15, 0, 255, 255, 31, 0, 255, 255, 63, 0, 255, 255, 127, 0, 255, 255, 255, 0, 255, 255, 255, 1, 255, 255, 255, 3, 255, 255, 255, 7, 255, 255, 255, 15, 255, 255, 255, 31, 255, 255, 255, 63, 255, 255, 255, 127, 255, 255, 255, 255, 128, 2, 0, 0, 204, 1, 0, 0, 24, 0, 0, 0, 1, 0, 0, 0, 176, 0, 0, 0, 120, 0, 0, 0, 96, 1, 0, 0, 240, 0, 0, 0, 96, 1, 0, 0, 32, 1, 0, 0, 192, 2, 0, 0, 224, 1, 0, 0, 192, 2, 0, 0, 64, 2, 0, 0, 208, 2, 0, 0, 224, 1, 0, 0, 208, 2, 0, 0, 64, 2, 0, 0, 0, 5, 0, 0, 208, 2, 0, 0, 0, 5, 0, 0, 208, 2, 0, 0, 128, 7, 0, 0, 56, 4, 0, 0, 128, 7, 0, 0, 56, 4, 0, 0, 128, 7, 0, 0, 56, 4, 0, 0, 128, 7, 0, 0, 56, 4, 0, 0, 0, 8, 0, 0, 56, 4, 0, 0, 0, 16, 0, 0, 112, 8, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0, 9, 0, 0, 0, 10, 0, 0, 0, 9, 0, 0, 0, 10, 0, 0, 0, 9, 0, 0, 0, 10, 0, 0, 0, 4, 0, 0, 0, 3, 0, 0, 0, 7, 0, 0, 0, 6, 0, 0, 0, 4, 0, 0, 0, 3, 0, 0, 0, 7, 0, 0, 0, 6, 0, 0, 0, 2, 0, 0, 0, 2, 0, 0, 0, 7, 0, 0, 0, 6, 0, 0, 0, 7, 0, 0, 0, 6, 0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0, 192, 93, 0, 0, 233, 3, 0, 0, 24, 0, 0, 0, 1, 0, 0, 0, 25, 0, 0, 0, 1, 0, 0, 0, 48, 117, 0, 0, 233, 3, 0, 0, 30, 0, 0, 0, 1, 0, 0, 0, 50, 0, 0, 0, 1, 0, 0, 0, 96, 234, 0, 0, 233, 3, 0, 0, 60, 0, 0, 0, 1, 0, 0, 0, 152, 58, 0, 0, 233, 3, 0, 0, 25, 0, 0, 0, 2, 0, 0, 0, 224, 1, 0, 0, 192, 3, 0, 0, 128, 7, 0, 0, 64, 11, 0, 0, 224, 1, 0, 0, 192, 3, 0, 0, 128, 7, 0, 0, 64, 11, 0, 0, 224, 1, 0, 0, 192, 3, 0, 0, 128, 7, 0, 0, 64, 11, 0, 0, 224, 1, 0, 0, 192, 3, 0, 0, 224, 1, 0, 0, 192, 3, 0, 0, 120, 0, 0, 0, 240, 0, 0, 0, 224, 1, 0, 0, 192, 3, 0, 0, 120, 0, 0, 0, 240, 0, 0, 0, 224, 1, 0, 0, 192, 3, 0, 0, 120, 0, 0, 0, 240, 0, 0, 0, 224, 1, 0, 0, 192, 3, 0, 0, 120, 0, 0, 0, 240, 0, 0, 0, 224, 1, 0, 0, 192, 3, 0, 0, 108, 8, 0, 0, 5, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 2, 0, 0, 0, 5, 23, 0, 0, 0, 4, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 10, 255, 255, 255, 255, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 108, 8, 0, 0, 79, 103, 103, 83, 0, 128, 116, 104, 101, 111, 114, 97, 0, 84, 104, 101, 111, 114, 97, 0, 1, 118, 111, 114, 98, 105, 115, 0, 86, 111, 114, 98, 105, 115, 0, 83, 112, 101, 101, 120, 0, 80, 67, 77, 32, 32, 32, 32, 32, 0, 80, 67, 77, 0, 67, 77, 77, 76, 0, 0, 0, 0, 0, 67, 77, 77, 76, 0, 65, 110, 110, 111, 100, 101, 120, 0, 102, 105, 115, 104, 101, 97, 100, 0, 83, 107, 101, 108, 101, 116, 111, 110, 0, 102, 76, 97, 67, 0, 70, 108, 97, 99, 48, 0, 127, 70, 76, 65, 67, 0, 70, 108, 97, 99, 0, 65, 110, 120, 68, 97, 116, 97, 0, 67, 69, 76, 84, 32, 32, 32, 32, 0, 67, 69, 76, 84, 0, 128, 107, 97, 116, 101, 0, 0, 0, 0, 75, 97, 116, 101, 0, 66, 66, 67, 68, 0, 0, 68, 105, 114, 97, 99, 0, 79, 112, 117, 115, 72, 101, 97, 100, 0, 79, 112, 117, 115, 0, 79, 86, 80, 56, 48, 0, 86, 80, 56, 0, 86, 80, 56, 48, 0, 85, 110, 107, 110, 111, 119, 110, 0, 3, 118, 111, 114, 98, 105, 115, 0, 129, 116, 104, 101, 111, 114, 97, 0, 129, 107, 97, 116, 101, 0, 0, 0, 0, 79, 112, 117, 115, 84, 97, 103, 115, 0, 79, 86, 80, 56, 48, 2, 32, 0, 102, 105, 115, 104, 101, 97, 100, 0, 0, 102, 105, 115, 98, 111, 110, 101, 0, 0, 105, 110, 100, 101, 120, 0, 0, 69, 114, 114, 111, 114, 32, 112, 114, 111, 99, 101, 115, 115, 105, 110, 103, 32, 115, 107, 101, 108, 101, 116, 111, 110, 32, 112, 97, 99, 107, 101, 116, 58, 32, 37, 100, 10, 0, 116, 104, 101, 111, 114, 97, 0, 118, 111, 114, 98, 105, 115, 0, 111, 112, 117, 115, 0, 73, 110, 118, 97, 108, 105, 100, 32, 111, 103, 103, 32, 115, 107, 101, 108, 101, 116, 111, 110, 32, 116, 114, 97, 99, 107, 32, 100, 97, 116, 97, 63, 32, 37, 100, 10, 0, 73, 110, 118, 97, 108, 105, 100, 32, 115, 116, 97, 116, 101, 32, 105, 110, 32, 79, 103, 103, 32, 114, 101, 97, 100, 80, 97, 99, 107, 101, 116, 67, 97, 108, 108, 98, 97, 99, 107, 0, 66, 117, 102, 102, 101, 114, 32, 115, 101, 101, 107, 32, 102, 97, 105, 108, 117, 114, 101, 32, 105, 110, 32, 111, 103, 103, 32, 100, 101, 109, 117, 120, 101, 114, 59, 32, 37, 108, 108, 100, 32, 40, 37, 108, 100, 32, 37, 100, 41, 10, 0, 69, 114, 114, 111, 114, 32, 37, 100, 32, 102, 114, 111, 109, 32, 111, 103, 103, 122, 95, 114, 101, 97, 100, 10, 0, 70, 97, 105, 108, 101, 100, 32, 116, 111, 32, 39, 115, 101, 101, 107, 39, 32, 111, 103, 103, 122, 32, 37, 100, 10, 0, 116, 114, 121, 105, 110, 103, 32, 116, 111, 32, 115, 101, 101, 107, 32, 116, 111, 32, 37, 108, 108, 100, 10, 0, 102, 97, 105, 108, 101, 100, 32, 98, 113, 95, 114, 101, 97, 100, 32, 108, 101, 110, 32, 37, 100, 32, 97, 116, 32, 112, 111, 115, 32, 37, 108, 108, 100, 10, 0, 102, 97, 105, 108, 101, 100, 32, 97, 32, 98, 113, 95, 114, 101, 97, 100, 32, 108, 101, 110, 32, 37, 100, 32, 97, 116, 32, 112, 111, 115, 32, 37, 108, 108, 100, 10, 0, 17, 0, 10, 0, 17, 17, 17, 0, 0, 0, 0, 5, 0, 0, 0, 0, 0, 0, 9, 0, 0, 0, 0, 11, 0, 0, 0, 0, 0, 0, 0, 0, 17, 0, 15, 10, 17, 17, 17, 3, 10, 7, 0, 1, 19, 9, 11, 11, 0, 0, 9, 6, 11, 0, 0, 11, 0, 6, 17, 0, 0, 0, 17, 17, 17, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 11, 0, 0, 0, 0, 0, 0, 0, 0, 17, 0, 10, 10, 17, 17, 17, 0, 10, 0, 0, 2, 0, 9, 11, 0, 0, 0, 9, 0, 11, 0, 0, 11, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 12, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 12, 0, 0, 0, 0, 12, 0, 0, 0, 0, 9, 12, 0, 0, 0, 0, 0, 12, 0, 0, 12, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 14, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 13, 0, 0, 0, 4, 13, 0, 0, 0, 0, 9, 14, 0, 0, 0, 0, 0, 14, 0, 0, 14, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 16, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 15, 0, 0, 0, 0, 15, 0, 0, 0, 0, 9, 16, 0, 0, 0, 0, 0, 16, 0, 0, 16, 0, 0, 18, 0, 0, 0, 18, 18, 18, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 18, 0, 0, 0, 18, 18, 18, 0, 0, 0, 0, 0, 0, 9, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 11, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 10, 0, 0, 0, 0, 10, 0, 0, 0, 0, 9, 11, 0, 0, 0, 0, 0, 11, 0, 0, 11, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 12, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 12, 0, 0, 0, 0, 12, 0, 0, 0, 0, 9, 12, 0, 0, 0, 0, 0, 12, 0, 0, 12, 0, 0, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 65, 66, 67, 68, 69, 70, 45, 43, 32, 32, 32, 48, 88, 48, 120, 0, 84, 33, 34, 25, 13, 1, 2, 3, 17, 75, 28, 12, 16, 4, 11, 29, 18, 30, 39, 104, 110, 111, 112, 113, 98, 32, 5, 6, 15, 19, 20, 21, 26, 8, 22, 7, 40, 36, 23, 24, 9, 10, 14, 27, 31, 37, 35, 131, 130, 125, 38, 42, 43, 60, 61, 62, 63, 67, 71, 74, 77, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 99, 100, 101, 102, 103, 105, 106, 107, 108, 114, 115, 116, 121, 122, 123, 124, 0, 73, 108, 108, 101, 103, 97, 108, 32, 98, 121, 116, 101, 32, 115, 101, 113, 117, 101, 110, 99, 101, 0, 68, 111, 109, 97, 105, 110, 32, 101, 114, 114, 111, 114, 0, 82, 101, 115, 117, 108, 116, 32, 110, 111, 116, 32, 114, 101, 112, 114, 101, 115, 101, 110, 116, 97, 98, 108, 101, 0, 78, 111, 116, 32, 97, 32, 116, 116, 121, 0, 80, 101, 114, 109, 105, 115, 115, 105, 111, 110, 32, 100, 101, 110, 105, 101, 100, 0, 79, 112, 101, 114, 97, 116, 105, 111, 110, 32, 110, 111, 116, 32, 112, 101, 114, 109, 105, 116, 116, 101, 100, 0, 78, 111, 32, 115, 117, 99, 104, 32, 102, 105, 108, 101, 32, 111, 114, 32, 100, 105, 114, 101, 99, 116, 111, 114, 121, 0, 78, 111, 32, 115, 117, 99, 104, 32, 112, 114, 111, 99, 101, 115, 115, 0, 70, 105, 108, 101, 32, 101, 120, 105, 115, 116, 115, 0, 86, 97, 108, 117, 101, 32, 116, 111, 111, 32, 108, 97, 114, 103, 101, 32, 102, 111, 114, 32, 100, 97, 116, 97, 32, 116, 121, 112, 101, 0, 78, 111, 32, 115, 112, 97, 99, 101, 32, 108, 101, 102, 116, 32, 111, 110, 32, 100, 101, 118, 105, 99, 101, 0, 79, 117, 116, 32, 111, 102, 32, 109, 101, 109, 111, 114, 121, 0, 82, 101, 115, 111, 117, 114, 99, 101, 32, 98, 117, 115, 121, 0, 73, 110, 116, 101, 114, 114, 117, 112, 116, 101, 100, 32, 115, 121, 115, 116, 101, 109, 32, 99, 97, 108, 108, 0, 82, 101, 115, 111, 117, 114, 99, 101, 32, 116, 101, 109, 112, 111, 114, 97, 114, 105, 108, 121, 32, 117, 110, 97, 118, 97, 105, 108, 97, 98, 108, 101, 0, 73, 110, 118, 97, 108, 105, 100, 32, 115, 101, 101, 107, 0, 67, 114, 111, 115, 115, 45, 100, 101, 118, 105, 99, 101, 32, 108, 105, 110, 107, 0, 82, 101, 97, 100, 45, 111, 110, 108, 121, 32, 102, 105, 108, 101, 32, 115, 121, 115, 116, 101, 109, 0, 68, 105, 114, 101, 99, 116, 111, 114, 121, 32, 110, 111, 116, 32, 101, 109, 112, 116, 121, 0, 67, 111, 110, 110, 101, 99, 116, 105, 111, 110, 32, 114, 101, 115, 101, 116, 32, 98, 121, 32, 112, 101, 101, 114, 0, 79, 112, 101, 114, 97, 116, 105, 111, 110, 32, 116, 105, 109, 101, 100, 32, 111, 117, 116, 0, 67, 111, 110, 110, 101, 99, 116, 105, 111, 110, 32, 114, 101, 102, 117, 115, 101, 100, 0, 72, 111, 115, 116, 32, 105, 115, 32, 100, 111, 119, 110, 0, 72, 111, 115, 116, 32, 105, 115, 32, 117, 110, 114, 101, 97, 99, 104, 97, 98, 108, 101, 0, 65, 100, 100, 114, 101, 115, 115, 32, 105, 110, 32, 117, 115, 101, 0, 66, 114, 111, 107, 101, 110, 32, 112, 105, 112, 101, 0, 73, 47, 79, 32, 101, 114, 114, 111, 114, 0, 78, 111, 32, 115, 117, 99, 104, 32, 100, 101, 118, 105, 99, 101, 32, 111, 114, 32, 97, 100, 100, 114, 101, 115, 115, 0, 66, 108, 111, 99, 107, 32, 100, 101, 118, 105, 99, 101, 32, 114, 101, 113, 117, 105, 114, 101, 100, 0, 78, 111, 32, 115, 117, 99, 104, 32, 100, 101, 118, 105, 99, 101, 0, 78, 111, 116, 32, 97, 32, 100, 105, 114, 101, 99, 116, 111, 114, 121, 0, 73, 115, 32, 97, 32, 100, 105, 114, 101, 99, 116, 111, 114, 121, 0, 84, 101, 120, 116, 32, 102, 105, 108, 101, 32, 98, 117, 115, 121, 0, 69, 120, 101, 99, 32, 102, 111, 114, 109, 97, 116, 32, 101, 114, 114, 111, 114, 0, 73, 110, 118, 97, 108, 105, 100, 32, 97, 114, 103, 117, 109, 101, 110, 116, 0, 65, 114, 103, 117, 109, 101, 110, 116, 32, 108, 105, 115, 116, 32, 116, 111, 111, 32, 108, 111, 110, 103, 0, 83, 121, 109, 98, 111, 108, 105, 99, 32, 108, 105, 110, 107, 32, 108, 111, 111, 112, 0, 70, 105, 108, 101, 110, 97, 109, 101, 32, 116, 111, 111, 32, 108, 111, 110, 103, 0, 84, 111, 111, 32, 109, 97, 110, 121, 32, 111, 112, 101, 110, 32, 102, 105, 108, 101, 115, 32, 105, 110, 32, 115, 121, 115, 116, 101, 109, 0, 78, 111, 32, 102, 105, 108, 101, 32, 100, 101, 115, 99, 114, 105, 112, 116, 111, 114, 115, 32, 97, 118, 97, 105, 108, 97, 98, 108, 101, 0, 66, 97, 100, 32, 102, 105, 108, 101, 32, 100, 101, 115, 99, 114, 105, 112, 116, 111, 114, 0, 78, 111, 32, 99, 104, 105, 108, 100, 32, 112, 114, 111, 99, 101, 115, 115, 0, 66, 97, 100, 32, 97, 100, 100, 114, 101, 115, 115, 0, 70, 105, 108, 101, 32, 116, 111, 111, 32, 108, 97, 114, 103, 101, 0, 84, 111, 111, 32, 109, 97, 110, 121, 32, 108, 105, 110, 107, 115, 0, 78, 111, 32, 108, 111, 99, 107, 115, 32, 97, 118, 97, 105, 108, 97, 98, 108, 101, 0, 82, 101, 115, 111, 117, 114, 99, 101, 32, 100, 101, 97, 100, 108, 111, 99, 107, 32, 119, 111, 117, 108, 100, 32, 111, 99, 99, 117, 114, 0, 83, 116, 97, 116, 101, 32, 110, 111, 116, 32, 114, 101, 99, 111, 118, 101, 114, 97, 98, 108, 101, 0, 80, 114, 101, 118, 105, 111, 117, 115, 32, 111, 119, 110, 101, 114, 32, 100, 105, 101, 100, 0, 79, 112, 101, 114, 97, 116, 105, 111, 110, 32, 99, 97, 110, 99, 101, 108, 101, 100, 0, 70, 117, 110, 99, 116, 105, 111, 110, 32, 110, 111, 116, 32, 105, 109, 112, 108, 101, 109, 101, 110, 116, 101, 100, 0, 78, 111, 32, 109, 101, 115, 115, 97, 103, 101, 32, 111, 102, 32, 100, 101, 115, 105, 114, 101, 100, 32, 116, 121, 112, 101, 0, 73, 100, 101, 110, 116, 105, 102, 105, 101, 114, 32, 114, 101, 109, 111, 118, 101, 100, 0, 68, 101, 118, 105, 99, 101, 32, 110, 111, 116, 32, 97, 32, 115, 116, 114, 101, 97, 109, 0, 78, 111, 32, 100, 97, 116, 97, 32, 97, 118, 97, 105, 108, 97, 98, 108, 101, 0, 68, 101, 118, 105, 99, 101, 32, 116, 105, 109, 101, 111, 117, 116, 0, 79, 117, 116, 32, 111, 102, 32, 115, 116, 114, 101, 97, 109, 115, 32, 114, 101, 115, 111, 117, 114, 99, 101, 115, 0, 76, 105, 110, 107, 32, 104, 97, 115, 32, 98, 101, 101, 110, 32, 115, 101, 118, 101, 114, 101, 100, 0, 80, 114, 111, 116, 111, 99, 111, 108, 32, 101, 114, 114, 111, 114, 0, 66, 97, 100, 32, 109, 101, 115, 115, 97, 103, 101, 0, 70, 105, 108, 101, 32, 100, 101, 115, 99, 114, 105, 112, 116, 111, 114, 32, 105, 110, 32, 98, 97, 100, 32, 115, 116, 97, 116, 101, 0, 78, 111, 116, 32, 97, 32, 115, 111, 99, 107, 101, 116, 0, 68, 101, 115, 116, 105, 110, 97, 116, 105, 111, 110, 32, 97, 100, 100, 114, 101, 115, 115, 32, 114, 101, 113, 117, 105, 114, 101, 100, 0, 77, 101, 115, 115, 97, 103, 101, 32, 116, 111, 111, 32, 108, 97, 114, 103, 101, 0, 80, 114, 111, 116, 111, 99, 111, 108, 32, 119, 114, 111, 110, 103, 32, 116, 121, 112, 101, 32, 102, 111, 114, 32, 115, 111, 99, 107, 101, 116, 0, 80, 114, 111, 116, 111, 99, 111, 108, 32, 110, 111, 116, 32, 97, 118, 97, 105, 108, 97, 98, 108, 101, 0, 80, 114, 111, 116, 111, 99, 111, 108, 32, 110, 111, 116, 32, 115, 117, 112, 112, 111, 114, 116, 101, 100, 0, 83, 111, 99, 107, 101, 116, 32, 116, 121, 112, 101, 32, 110, 111, 116, 32, 115, 117, 112, 112, 111, 114, 116, 101, 100, 0, 78, 111, 116, 32, 115, 117, 112, 112, 111, 114, 116, 101, 100, 0, 80, 114, 111, 116, 111, 99, 111, 108, 32, 102, 97, 109, 105, 108, 121, 32, 110, 111, 116, 32, 115, 117, 112, 112, 111, 114, 116, 101, 100, 0, 65, 100, 100, 114, 101, 115, 115, 32, 102, 97, 109, 105, 108, 121, 32, 110, 111, 116, 32, 115, 117, 112, 112, 111, 114, 116, 101, 100, 32, 98, 121, 32, 112, 114, 111, 116, 111, 99, 111, 108, 0, 65, 100, 100, 114, 101, 115, 115, 32, 110, 111, 116, 32, 97, 118, 97, 105, 108, 97, 98, 108, 101, 0, 78, 101, 116, 119, 111, 114, 107, 32, 105, 115, 32, 100, 111, 119, 110, 0, 78, 101, 116, 119, 111, 114, 107, 32, 117, 110, 114, 101, 97, 99, 104, 97, 98, 108, 101, 0, 67, 111, 110, 110, 101, 99, 116, 105, 111, 110, 32, 114, 101, 115, 101, 116, 32, 98, 121, 32, 110, 101, 116, 119, 111, 114, 107, 0, 67, 111, 110, 110, 101, 99, 116, 105, 111, 110, 32, 97, 98, 111, 114, 116, 101, 100, 0, 78, 111, 32, 98, 117, 102, 102, 101, 114, 32, 115, 112, 97, 99, 101, 32, 97, 118, 97, 105, 108, 97, 98, 108, 101, 0, 83, 111, 99, 107, 101, 116, 32, 105, 115, 32, 99, 111, 110, 110, 101, 99, 116, 101, 100, 0, 83, 111, 99, 107, 101, 116, 32, 110, 111, 116, 32, 99, 111, 110, 110, 101, 99, 116, 101, 100, 0, 67, 97, 110, 110, 111, 116, 32, 115, 101, 110, 100, 32, 97, 102, 116, 101, 114, 32, 115, 111, 99, 107, 101, 116, 32, 115, 104, 117, 116, 100, 111, 119, 110, 0, 79, 112, 101, 114, 97, 116, 105, 111, 110, 32, 97, 108, 114, 101, 97, 100, 121, 32, 105, 110, 32, 112, 114, 111, 103, 114, 101, 115, 115, 0, 79, 112, 101, 114, 97, 116, 105, 111, 110, 32, 105, 110, 32, 112, 114, 111, 103, 114, 101, 115, 115, 0, 83, 116, 97, 108, 101, 32, 102, 105, 108, 101, 32, 104, 97, 110, 100, 108, 101, 0, 82, 101, 109, 111, 116, 101, 32, 73, 47, 79, 32, 101, 114, 114, 111, 114, 0, 81, 117, 111, 116, 97, 32, 101, 120, 99, 101, 101, 100, 101, 100, 0, 78, 111, 32, 109, 101, 100, 105, 117, 109, 32, 102, 111, 117, 110, 100, 0, 87, 114, 111, 110, 103, 32, 109, 101, 100, 105, 117, 109, 32, 116, 121, 112, 101, 0, 78, 111, 32, 101, 114, 114, 111, 114, 32, 105, 110, 102, 111, 114, 109, 97, 116, 105, 111, 110, 0, 0, 40, 110, 117, 108, 108, 41, 0, 45, 48, 88, 43, 48, 88, 32, 48, 88, 45, 48, 120, 43, 48, 120, 32, 48, 120, 0, 105, 110, 102, 0, 73, 78, 70, 0, 110, 97, 110, 0, 78, 65, 78, 0, 46, 0 ], "i8", ALLOC_NONE, Runtime.GLOBAL_BASE);
var tempDoublePtr = STATICTOP;
STATICTOP += 16;
Module["_bitshift64Ashr"] = _bitshift64Ashr;
function _ogvjs_callback_loaded_metadata(videoCodecStr, audioCodecStr) {
 if (videoCodecStr) {
  Module.videoCodec = Module.Pointer_stringify(videoCodecStr);
 }
 if (audioCodecStr) {
  Module.audioCodec = Module.Pointer_stringify(audioCodecStr);
 }
 var len = Module._ogv_demuxer_media_duration();
 if (len >= 0) {
  Module.duration = len;
 } else {
  Module.duration = NaN;
 }
 Module.loadedMetadata = true;
}
Module["_i64Subtract"] = _i64Subtract;
function ___setErrNo(value) {
 if (Module["___errno_location"]) HEAP32[Module["___errno_location"]() >> 2] = value;
 return value;
}
var ERRNO_CODES = {
 EPERM: 1,
 ENOENT: 2,
 ESRCH: 3,
 EINTR: 4,
 EIO: 5,
 ENXIO: 6,
 E2BIG: 7,
 ENOEXEC: 8,
 EBADF: 9,
 ECHILD: 10,
 EAGAIN: 11,
 EWOULDBLOCK: 11,
 ENOMEM: 12,
 EACCES: 13,
 EFAULT: 14,
 ENOTBLK: 15,
 EBUSY: 16,
 EEXIST: 17,
 EXDEV: 18,
 ENODEV: 19,
 ENOTDIR: 20,
 EISDIR: 21,
 EINVAL: 22,
 ENFILE: 23,
 EMFILE: 24,
 ENOTTY: 25,
 ETXTBSY: 26,
 EFBIG: 27,
 ENOSPC: 28,
 ESPIPE: 29,
 EROFS: 30,
 EMLINK: 31,
 EPIPE: 32,
 EDOM: 33,
 ERANGE: 34,
 ENOMSG: 42,
 EIDRM: 43,
 ECHRNG: 44,
 EL2NSYNC: 45,
 EL3HLT: 46,
 EL3RST: 47,
 ELNRNG: 48,
 EUNATCH: 49,
 ENOCSI: 50,
 EL2HLT: 51,
 EDEADLK: 35,
 ENOLCK: 37,
 EBADE: 52,
 EBADR: 53,
 EXFULL: 54,
 ENOANO: 55,
 EBADRQC: 56,
 EBADSLT: 57,
 EDEADLOCK: 35,
 EBFONT: 59,
 ENOSTR: 60,
 ENODATA: 61,
 ETIME: 62,
 ENOSR: 63,
 ENONET: 64,
 ENOPKG: 65,
 EREMOTE: 66,
 ENOLINK: 67,
 EADV: 68,
 ESRMNT: 69,
 ECOMM: 70,
 EPROTO: 71,
 EMULTIHOP: 72,
 EDOTDOT: 73,
 EBADMSG: 74,
 ENOTUNIQ: 76,
 EBADFD: 77,
 EREMCHG: 78,
 ELIBACC: 79,
 ELIBBAD: 80,
 ELIBSCN: 81,
 ELIBMAX: 82,
 ELIBEXEC: 83,
 ENOSYS: 38,
 ENOTEMPTY: 39,
 ENAMETOOLONG: 36,
 ELOOP: 40,
 EOPNOTSUPP: 95,
 EPFNOSUPPORT: 96,
 ECONNRESET: 104,
 ENOBUFS: 105,
 EAFNOSUPPORT: 97,
 EPROTOTYPE: 91,
 ENOTSOCK: 88,
 ENOPROTOOPT: 92,
 ESHUTDOWN: 108,
 ECONNREFUSED: 111,
 EADDRINUSE: 98,
 ECONNABORTED: 103,
 ENETUNREACH: 101,
 ENETDOWN: 100,
 ETIMEDOUT: 110,
 EHOSTDOWN: 112,
 EHOSTUNREACH: 113,
 EINPROGRESS: 115,
 EALREADY: 114,
 EDESTADDRREQ: 89,
 EMSGSIZE: 90,
 EPROTONOSUPPORT: 93,
 ESOCKTNOSUPPORT: 94,
 EADDRNOTAVAIL: 99,
 ENETRESET: 102,
 EISCONN: 106,
 ENOTCONN: 107,
 ETOOMANYREFS: 109,
 EUSERS: 87,
 EDQUOT: 122,
 ESTALE: 116,
 ENOTSUP: 95,
 ENOMEDIUM: 123,
 EILSEQ: 84,
 EOVERFLOW: 75,
 ECANCELED: 125,
 ENOTRECOVERABLE: 131,
 EOWNERDEAD: 130,
 ESTRPIPE: 86
};
function _sysconf(name) {
 switch (name) {
 case 30:
  return PAGE_SIZE;
 case 85:
  return totalMemory / PAGE_SIZE;
 case 132:
 case 133:
 case 12:
 case 137:
 case 138:
 case 15:
 case 235:
 case 16:
 case 17:
 case 18:
 case 19:
 case 20:
 case 149:
 case 13:
 case 10:
 case 236:
 case 153:
 case 9:
 case 21:
 case 22:
 case 159:
 case 154:
 case 14:
 case 77:
 case 78:
 case 139:
 case 80:
 case 81:
 case 82:
 case 68:
 case 67:
 case 164:
 case 11:
 case 29:
 case 47:
 case 48:
 case 95:
 case 52:
 case 51:
 case 46:
  return 200809;
 case 79:
  return 0;
 case 27:
 case 246:
 case 127:
 case 128:
 case 23:
 case 24:
 case 160:
 case 161:
 case 181:
 case 182:
 case 242:
 case 183:
 case 184:
 case 243:
 case 244:
 case 245:
 case 165:
 case 178:
 case 179:
 case 49:
 case 50:
 case 168:
 case 169:
 case 175:
 case 170:
 case 171:
 case 172:
 case 97:
 case 76:
 case 32:
 case 173:
 case 35:
  return -1;
 case 176:
 case 177:
 case 7:
 case 155:
 case 8:
 case 157:
 case 125:
 case 126:
 case 92:
 case 93:
 case 129:
 case 130:
 case 131:
 case 94:
 case 91:
  return 1;
 case 74:
 case 60:
 case 69:
 case 70:
 case 4:
  return 1024;
 case 31:
 case 42:
 case 72:
  return 32;
 case 87:
 case 26:
 case 33:
  return 2147483647;
 case 34:
 case 1:
  return 47839;
 case 38:
 case 36:
  return 99;
 case 43:
 case 37:
  return 2048;
 case 0:
  return 2097152;
 case 3:
  return 65536;
 case 28:
  return 32768;
 case 44:
  return 32767;
 case 75:
  return 16384;
 case 39:
  return 1e3;
 case 89:
  return 700;
 case 71:
  return 256;
 case 40:
  return 255;
 case 2:
  return 100;
 case 180:
  return 64;
 case 25:
  return 20;
 case 5:
  return 16;
 case 6:
  return 6;
 case 73:
  return 4;
 case 84:
  {
   if (typeof navigator === "object") return navigator["hardwareConcurrency"] || 1;
   return 1;
  }
 }
 ___setErrNo(ERRNO_CODES.EINVAL);
 return -1;
}
Module["_memset"] = _memset;
function _pthread_cleanup_push(routine, arg) {
 __ATEXIT__.push((function() {
  Runtime.dynCall("vi", routine, [ arg ]);
 }));
 _pthread_cleanup_push.level = __ATEXIT__.length;
}
Module["_bitshift64Lshr"] = _bitshift64Lshr;
Module["_bitshift64Shl"] = _bitshift64Shl;
function _pthread_cleanup_pop() {
 assert(_pthread_cleanup_push.level == __ATEXIT__.length, "cannot pop if something else added meanwhile!");
 __ATEXIT__.pop();
 _pthread_cleanup_push.level = __ATEXIT__.length;
}
function _abort() {
 Module["abort"]();
}
var SYSCALLS = {
 varargs: 0,
 get: (function(varargs) {
  SYSCALLS.varargs += 4;
  var ret = HEAP32[SYSCALLS.varargs - 4 >> 2];
  return ret;
 }),
 getStr: (function() {
  var ret = Pointer_stringify(SYSCALLS.get());
  return ret;
 }),
 get64: (function() {
  var low = SYSCALLS.get(), high = SYSCALLS.get();
  if (low >= 0) assert(high === 0); else assert(high === -1);
  return low;
 }),
 getZero: (function() {
  assert(SYSCALLS.get() === 0);
 })
};
function ___syscall3(which, varargs) {
 SYSCALLS.varargs = varargs;
 try {
  var stream = SYSCALLS.getStreamFromFD(), buf = SYSCALLS.get(), count = SYSCALLS.get();
  return FS.read(stream, HEAP8, buf, count);
 } catch (e) {
  if (typeof FS === "undefined" || !(e instanceof FS.ErrnoError)) abort(e);
  return -e.errno;
 }
}
function ___lock() {}
function ___unlock() {}
function ___syscall6(which, varargs) {
 SYSCALLS.varargs = varargs;
 try {
  var stream = SYSCALLS.getStreamFromFD();
  FS.close(stream);
  return 0;
 } catch (e) {
  if (typeof FS === "undefined" || !(e instanceof FS.ErrnoError)) abort(e);
  return -e.errno;
 }
}
Module["_i64Add"] = _i64Add;
function _ogvjs_callback_audio_packet(buffer, len, audioTimestamp) {
 Module.audioPackets.push({
  data: Module.HEAPU8.buffer.slice ? Module.HEAPU8.buffer.slice(buffer, buffer + len) : (new Uint8Array(new Uint8Array(Module.HEAPU8.buffer, buffer, len))).buffer,
  timestamp: audioTimestamp
 });
}
function _emscripten_memcpy_big(dest, src, num) {
 HEAPU8.set(HEAPU8.subarray(src, src + num), dest);
 return dest;
}
Module["_memcpy"] = _memcpy;
Module["_memmove"] = _memmove;
function _ogvjs_callback_video_packet(buffer, len, frameTimestamp, keyframeTimestamp) {
 Module.videoPackets.push({
  data: Module.HEAPU8.buffer.slice ? Module.HEAPU8.buffer.slice(buffer, buffer + len) : (new Uint8Array(new Uint8Array(Module.HEAPU8.buffer, buffer, len))).buffer,
  timestamp: frameTimestamp,
  keyframeTimestamp: keyframeTimestamp
 });
}
function _sbrk(bytes) {
 var self = _sbrk;
 if (!self.called) {
  DYNAMICTOP = alignMemoryPage(DYNAMICTOP);
  self.called = true;
  assert(Runtime.dynamicAlloc);
  self.alloc = Runtime.dynamicAlloc;
  Runtime.dynamicAlloc = (function() {
   abort("cannot dynamically allocate, sbrk now has control");
  });
 }
 var ret = DYNAMICTOP;
 if (bytes != 0) {
  var success = self.alloc(bytes);
  if (!success) return -1 >>> 0;
 }
 return ret;
}
function _time(ptr) {
 var ret = Date.now() / 1e3 | 0;
 if (ptr) {
  HEAP32[ptr >> 2] = ret;
 }
 return ret;
}
function _pthread_self() {
 return 0;
}
function ___syscall140(which, varargs) {
 SYSCALLS.varargs = varargs;
 try {
  var stream = SYSCALLS.getStreamFromFD(), offset_high = SYSCALLS.get(), offset_low = SYSCALLS.get(), result = SYSCALLS.get(), whence = SYSCALLS.get();
  var offset = offset_low;
  assert(offset_high === 0);
  FS.llseek(stream, offset, whence);
  HEAP32[result >> 2] = stream.position;
  if (stream.getdents && offset === 0 && whence === 0) stream.getdents = null;
  return 0;
 } catch (e) {
  if (typeof FS === "undefined" || !(e instanceof FS.ErrnoError)) abort(e);
  return -e.errno;
 }
}
function ___syscall146(which, varargs) {
 SYSCALLS.varargs = varargs;
 try {
  var stream = SYSCALLS.get(), iov = SYSCALLS.get(), iovcnt = SYSCALLS.get();
  var ret = 0;
  if (!___syscall146.buffer) {
   ___syscall146.buffers = [ null, [], [] ];
   ___syscall146.printChar = (function(stream, curr) {
    var buffer = ___syscall146.buffers[stream];
    assert(buffer);
    if (curr === 0 || curr === 10) {
     (stream === 1 ? Module["print"] : Module["printErr"])(UTF8ArrayToString(buffer, 0));
     buffer.length = 0;
    } else {
     buffer.push(curr);
    }
   });
  }
  for (var i = 0; i < iovcnt; i++) {
   var ptr = HEAP32[iov + i * 8 >> 2];
   var len = HEAP32[iov + (i * 8 + 4) >> 2];
   for (var j = 0; j < len; j++) {
    ___syscall146.printChar(stream, HEAPU8[ptr + j]);
   }
   ret += len;
  }
  return ret;
 } catch (e) {
  if (typeof FS === "undefined" || !(e instanceof FS.ErrnoError)) abort(e);
  return -e.errno;
 }
}
function ___syscall54(which, varargs) {
 SYSCALLS.varargs = varargs;
 try {
  return 0;
 } catch (e) {
  if (typeof FS === "undefined" || !(e instanceof FS.ErrnoError)) abort(e);
  return -e.errno;
 }
}
__ATEXIT__.push((function() {
 var fflush = Module["_fflush"];
 if (fflush) fflush(0);
 var printChar = ___syscall146.printChar;
 if (!printChar) return;
 var buffers = ___syscall146.buffers;
 if (buffers[1].length) printChar(1, 10);
 if (buffers[2].length) printChar(2, 10);
}));
STACK_BASE = STACKTOP = Runtime.alignMemory(STATICTOP);
staticSealed = true;
STACK_MAX = STACK_BASE + TOTAL_STACK;
DYNAMIC_BASE = DYNAMICTOP = Runtime.alignMemory(STACK_MAX);
var cttz_i8 = allocate([ 8, 0, 1, 0, 2, 0, 1, 0, 3, 0, 1, 0, 2, 0, 1, 0, 4, 0, 1, 0, 2, 0, 1, 0, 3, 0, 1, 0, 2, 0, 1, 0, 5, 0, 1, 0, 2, 0, 1, 0, 3, 0, 1, 0, 2, 0, 1, 0, 4, 0, 1, 0, 2, 0, 1, 0, 3, 0, 1, 0, 2, 0, 1, 0, 6, 0, 1, 0, 2, 0, 1, 0, 3, 0, 1, 0, 2, 0, 1, 0, 4, 0, 1, 0, 2, 0, 1, 0, 3, 0, 1, 0, 2, 0, 1, 0, 5, 0, 1, 0, 2, 0, 1, 0, 3, 0, 1, 0, 2, 0, 1, 0, 4, 0, 1, 0, 2, 0, 1, 0, 3, 0, 1, 0, 2, 0, 1, 0, 7, 0, 1, 0, 2, 0, 1, 0, 3, 0, 1, 0, 2, 0, 1, 0, 4, 0, 1, 0, 2, 0, 1, 0, 3, 0, 1, 0, 2, 0, 1, 0, 5, 0, 1, 0, 2, 0, 1, 0, 3, 0, 1, 0, 2, 0, 1, 0, 4, 0, 1, 0, 2, 0, 1, 0, 3, 0, 1, 0, 2, 0, 1, 0, 6, 0, 1, 0, 2, 0, 1, 0, 3, 0, 1, 0, 2, 0, 1, 0, 4, 0, 1, 0, 2, 0, 1, 0, 3, 0, 1, 0, 2, 0, 1, 0, 5, 0, 1, 0, 2, 0, 1, 0, 3, 0, 1, 0, 2, 0, 1, 0, 4, 0, 1, 0, 2, 0, 1, 0, 3, 0, 1, 0, 2, 0, 1, 0 ], "i8", ALLOC_DYNAMIC);
function invoke_iiii(index, a1, a2, a3) {
 try {
  return Module["dynCall_iiii"](index, a1, a2, a3);
 } catch (e) {
  if (typeof e !== "number" && e !== "longjmp") throw e;
  asm["setThrew"](1, 0);
 }
}
function invoke_vi(index, a1) {
 try {
  Module["dynCall_vi"](index, a1);
 } catch (e) {
  if (typeof e !== "number" && e !== "longjmp") throw e;
  asm["setThrew"](1, 0);
 }
}
function invoke_ii(index, a1) {
 try {
  return Module["dynCall_ii"](index, a1);
 } catch (e) {
  if (typeof e !== "number" && e !== "longjmp") throw e;
  asm["setThrew"](1, 0);
 }
}
function invoke_iiiii(index, a1, a2, a3, a4) {
 try {
  return Module["dynCall_iiiii"](index, a1, a2, a3, a4);
 } catch (e) {
  if (typeof e !== "number" && e !== "longjmp") throw e;
  asm["setThrew"](1, 0);
 }
}
function invoke_iii(index, a1, a2) {
 try {
  return Module["dynCall_iii"](index, a1, a2);
 } catch (e) {
  if (typeof e !== "number" && e !== "longjmp") throw e;
  asm["setThrew"](1, 0);
 }
}
function invoke_iiiiii(index, a1, a2, a3, a4, a5) {
 try {
  return Module["dynCall_iiiiii"](index, a1, a2, a3, a4, a5);
 } catch (e) {
  if (typeof e !== "number" && e !== "longjmp") throw e;
  asm["setThrew"](1, 0);
 }
}
Module.asmGlobalArg = {
 "Math": Math,
 "Int8Array": Int8Array,
 "Int16Array": Int16Array,
 "Int32Array": Int32Array,
 "Uint8Array": Uint8Array,
 "Uint16Array": Uint16Array,
 "Uint32Array": Uint32Array,
 "Float32Array": Float32Array,
 "Float64Array": Float64Array,
 "NaN": NaN,
 "Infinity": Infinity
};
Module.asmLibraryArg = {
 "abort": abort,
 "assert": assert,
 "invoke_iiii": invoke_iiii,
 "invoke_vi": invoke_vi,
 "invoke_ii": invoke_ii,
 "invoke_iiiii": invoke_iiiii,
 "invoke_iii": invoke_iii,
 "invoke_iiiiii": invoke_iiiiii,
 "_pthread_cleanup_pop": _pthread_cleanup_pop,
 "___lock": ___lock,
 "___unlock": ___unlock,
 "_ogvjs_callback_video_packet": _ogvjs_callback_video_packet,
 "___syscall3": ___syscall3,
 "_pthread_self": _pthread_self,
 "___syscall6": ___syscall6,
 "___setErrNo": ___setErrNo,
 "_abort": _abort,
 "_sbrk": _sbrk,
 "_time": _time,
 "_pthread_cleanup_push": _pthread_cleanup_push,
 "_emscripten_memcpy_big": _emscripten_memcpy_big,
 "___syscall54": ___syscall54,
 "_ogvjs_callback_audio_packet": _ogvjs_callback_audio_packet,
 "___syscall140": ___syscall140,
 "_sysconf": _sysconf,
 "___syscall146": ___syscall146,
 "_ogvjs_callback_loaded_metadata": _ogvjs_callback_loaded_metadata,
 "STACKTOP": STACKTOP,
 "STACK_MAX": STACK_MAX,
 "tempDoublePtr": tempDoublePtr,
 "ABORT": ABORT,
 "cttz_i8": cttz_i8
};
// EMSCRIPTEN_START_ASM

var asm = (function(global,env,buffer) {

  'use asm';
  
  
  var HEAP8 = new global.Int8Array(buffer);
  var HEAP16 = new global.Int16Array(buffer);
  var HEAP32 = new global.Int32Array(buffer);
  var HEAPU8 = new global.Uint8Array(buffer);
  var HEAPU16 = new global.Uint16Array(buffer);
  var HEAPU32 = new global.Uint32Array(buffer);
  var HEAPF32 = new global.Float32Array(buffer);
  var HEAPF64 = new global.Float64Array(buffer);


  var STACKTOP=env.STACKTOP|0;
  var STACK_MAX=env.STACK_MAX|0;
  var tempDoublePtr=env.tempDoublePtr|0;
  var ABORT=env.ABORT|0;
  var cttz_i8=env.cttz_i8|0;

  var __THREW__ = 0;
  var threwValue = 0;
  var setjmpId = 0;
  var undef = 0;
  var nan = global.NaN, inf = global.Infinity;
  var tempInt = 0, tempBigInt = 0, tempBigIntP = 0, tempBigIntS = 0, tempBigIntR = 0.0, tempBigIntI = 0, tempBigIntD = 0, tempValue = 0, tempDouble = 0.0;

  var tempRet0 = 0;
  var tempRet1 = 0;
  var tempRet2 = 0;
  var tempRet3 = 0;
  var tempRet4 = 0;
  var tempRet5 = 0;
  var tempRet6 = 0;
  var tempRet7 = 0;
  var tempRet8 = 0;
  var tempRet9 = 0;
  var Math_floor=global.Math.floor;
  var Math_abs=global.Math.abs;
  var Math_sqrt=global.Math.sqrt;
  var Math_pow=global.Math.pow;
  var Math_cos=global.Math.cos;
  var Math_sin=global.Math.sin;
  var Math_tan=global.Math.tan;
  var Math_acos=global.Math.acos;
  var Math_asin=global.Math.asin;
  var Math_atan=global.Math.atan;
  var Math_atan2=global.Math.atan2;
  var Math_exp=global.Math.exp;
  var Math_log=global.Math.log;
  var Math_ceil=global.Math.ceil;
  var Math_imul=global.Math.imul;
  var Math_min=global.Math.min;
  var Math_clz32=global.Math.clz32;
  var abort=env.abort;
  var assert=env.assert;
  var invoke_iiii=env.invoke_iiii;
  var invoke_vi=env.invoke_vi;
  var invoke_ii=env.invoke_ii;
  var invoke_iiiii=env.invoke_iiiii;
  var invoke_iii=env.invoke_iii;
  var invoke_iiiiii=env.invoke_iiiiii;
  var _pthread_cleanup_pop=env._pthread_cleanup_pop;
  var ___lock=env.___lock;
  var ___unlock=env.___unlock;
  var _ogvjs_callback_video_packet=env._ogvjs_callback_video_packet;
  var ___syscall3=env.___syscall3;
  var _pthread_self=env._pthread_self;
  var ___syscall6=env.___syscall6;
  var ___setErrNo=env.___setErrNo;
  var _abort=env._abort;
  var _sbrk=env._sbrk;
  var _time=env._time;
  var _pthread_cleanup_push=env._pthread_cleanup_push;
  var _emscripten_memcpy_big=env._emscripten_memcpy_big;
  var ___syscall54=env.___syscall54;
  var _ogvjs_callback_audio_packet=env._ogvjs_callback_audio_packet;
  var ___syscall140=env.___syscall140;
  var _sysconf=env._sysconf;
  var ___syscall146=env.___syscall146;
  var _ogvjs_callback_loaded_metadata=env._ogvjs_callback_loaded_metadata;
  var tempFloat = 0.0;

// EMSCRIPTEN_START_FUNCS
function _printf_core(i1, i2, i3, i4, i5) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 i5 = i5 | 0;
 var i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0, i17 = 0, i18 = 0, i19 = 0, i20 = 0, i21 = 0, i22 = 0, i23 = 0, i24 = 0, i25 = 0, i26 = 0, i27 = 0, i28 = 0, i29 = 0, i30 = 0, i31 = 0, i32 = 0, i33 = 0, i34 = 0, i35 = 0, i36 = 0, i37 = 0, i38 = 0, i39 = 0, i40 = 0, i41 = 0, i42 = 0, i43 = 0, i44 = 0, i45 = 0, i46 = 0, i47 = 0, i48 = 0, i49 = 0, i50 = 0, i51 = 0, i52 = 0, i53 = 0, i54 = 0, i55 = 0, i56 = 0, i57 = 0, i58 = 0, i59 = 0, i60 = 0, i61 = 0, i62 = 0, i63 = 0, i64 = 0, i65 = 0, i66 = 0, i67 = 0, i68 = 0, i69 = 0, i70 = 0, i71 = 0, i72 = 0, i73 = 0, i74 = 0, i75 = 0, i76 = 0, i77 = 0, i78 = 0, i79 = 0, i80 = 0, i81 = 0, i82 = 0, i83 = 0, i84 = 0, i85 = 0, i86 = 0, i87 = 0, i88 = 0, i89 = 0, i90 = 0, i91 = 0, i92 = 0, i93 = 0, i94 = 0, i95 = 0, i96 = 0, i97 = 0, i98 = 0, i99 = 0, i100 = 0, i101 = 0, d102 = 0.0, d103 = 0.0, i104 = 0, i105 = 0, i106 = 0, i107 = 0, i108 = 0, d109 = 0.0, d110 = 0.0, d111 = 0.0, d112 = 0.0, i113 = 0, i114 = 0, i115 = 0, i116 = 0, i117 = 0, i118 = 0, i119 = 0, i120 = 0, d121 = 0.0, i122 = 0, i123 = 0, i124 = 0, i125 = 0, i126 = 0, i127 = 0, i128 = 0, i129 = 0, i130 = 0, i131 = 0, i132 = 0, i133 = 0, i134 = 0, i135 = 0, i136 = 0, i137 = 0, i138 = 0, i139 = 0, i140 = 0, i141 = 0, i142 = 0, i143 = 0, i144 = 0, i145 = 0, i146 = 0, i147 = 0, i148 = 0, d149 = 0.0, d150 = 0.0, d151 = 0.0, i152 = 0, i153 = 0, i154 = 0, i155 = 0, i156 = 0, i157 = 0, i158 = 0, i159 = 0, i160 = 0, i161 = 0, i162 = 0, i163 = 0, i164 = 0, i165 = 0, i166 = 0, i167 = 0, i168 = 0, i169 = 0, i170 = 0, i171 = 0, i172 = 0, i173 = 0, i174 = 0, i175 = 0, i176 = 0, i177 = 0, i178 = 0, i179 = 0, i180 = 0, i181 = 0, i182 = 0, i183 = 0, i184 = 0;
 i6 = STACKTOP;
 STACKTOP = STACKTOP + 624 | 0;
 i7 = i6 + 24 | 0;
 i8 = i6 + 16 | 0;
 i9 = i6 + 588 | 0;
 i10 = i6 + 576 | 0;
 i11 = i6;
 i12 = i6 + 536 | 0;
 i13 = i6 + 8 | 0;
 i14 = i6 + 528 | 0;
 i15 = (i1 | 0) != 0;
 i16 = i12 + 40 | 0;
 i17 = i16;
 i18 = i12 + 39 | 0;
 i12 = i13 + 4 | 0;
 i19 = i9;
 i20 = 0 - i19 | 0;
 i21 = i10 + 12 | 0;
 i22 = i10 + 11 | 0;
 i10 = i21;
 i23 = i10 - i19 | 0;
 i24 = -2 - i19 | 0;
 i25 = i10 + 2 | 0;
 i26 = i7 + 288 | 0;
 i27 = i9 + 9 | 0;
 i28 = i27;
 i29 = i9 + 8 | 0;
 i30 = 0;
 i31 = 0;
 i32 = 0;
 i33 = i2;
 L1 : while (1) {
  do if ((i30 | 0) > -1) if ((i31 | 0) > (2147483647 - i30 | 0)) {
   HEAP32[(___errno_location() | 0) >> 2] = 75;
   i34 = -1;
   break;
  } else {
   i34 = i31 + i30 | 0;
   break;
  } else i34 = i30; while (0);
  i2 = HEAP8[i33 >> 0] | 0;
  if (!(i2 << 24 >> 24)) {
   i35 = i34;
   i36 = i32;
   i37 = 244;
   break;
  } else {
   i38 = i2;
   i39 = i33;
  }
  L9 : while (1) {
   switch (i38 << 24 >> 24) {
   case 37:
    {
     i40 = i39;
     i41 = i39;
     i37 = 9;
     break L9;
     break;
    }
   case 0:
    {
     i42 = i39;
     i43 = i39;
     break L9;
     break;
    }
   default:
    {}
   }
   i2 = i39 + 1 | 0;
   i38 = HEAP8[i2 >> 0] | 0;
   i39 = i2;
  }
  L12 : do if ((i37 | 0) == 9) while (1) {
   i37 = 0;
   if ((HEAP8[i40 + 1 >> 0] | 0) != 37) {
    i42 = i40;
    i43 = i41;
    break L12;
   }
   i2 = i41 + 1 | 0;
   i44 = i40 + 2 | 0;
   if ((HEAP8[i44 >> 0] | 0) == 37) {
    i40 = i44;
    i41 = i2;
    i37 = 9;
   } else {
    i42 = i44;
    i43 = i2;
    break;
   }
  } while (0);
  i2 = i43 - i33 | 0;
  if (i15 ? (HEAP32[i1 >> 2] & 32 | 0) == 0 : 0) ___fwritex(i33, i2, i1) | 0;
  if ((i43 | 0) != (i33 | 0)) {
   i30 = i34;
   i31 = i2;
   i33 = i42;
   continue;
  }
  i44 = i42 + 1 | 0;
  i45 = HEAP8[i44 >> 0] | 0;
  i46 = (i45 << 24 >> 24) + -48 | 0;
  if (i46 >>> 0 < 10) {
   i47 = (HEAP8[i42 + 2 >> 0] | 0) == 36;
   i48 = i47 ? i42 + 3 | 0 : i44;
   i49 = HEAP8[i48 >> 0] | 0;
   i50 = i47 ? i46 : -1;
   i51 = i47 ? 1 : i32;
   i52 = i48;
  } else {
   i49 = i45;
   i50 = -1;
   i51 = i32;
   i52 = i44;
  }
  i44 = i49 << 24 >> 24;
  L25 : do if ((i44 & -32 | 0) == 32) {
   i45 = i44;
   i48 = i49;
   i47 = 0;
   i46 = i52;
   while (1) {
    if (!(1 << i45 + -32 & 75913)) {
     i53 = i48;
     i54 = i47;
     i55 = i46;
     break L25;
    }
    i56 = 1 << (i48 << 24 >> 24) + -32 | i47;
    i57 = i46 + 1 | 0;
    i58 = HEAP8[i57 >> 0] | 0;
    i45 = i58 << 24 >> 24;
    if ((i45 & -32 | 0) != 32) {
     i53 = i58;
     i54 = i56;
     i55 = i57;
     break;
    } else {
     i48 = i58;
     i47 = i56;
     i46 = i57;
    }
   }
  } else {
   i53 = i49;
   i54 = 0;
   i55 = i52;
  } while (0);
  do if (i53 << 24 >> 24 == 42) {
   i44 = i55 + 1 | 0;
   i46 = (HEAP8[i44 >> 0] | 0) + -48 | 0;
   if (i46 >>> 0 < 10 ? (HEAP8[i55 + 2 >> 0] | 0) == 36 : 0) {
    HEAP32[i5 + (i46 << 2) >> 2] = 10;
    i59 = 1;
    i60 = i55 + 3 | 0;
    i61 = HEAP32[i4 + ((HEAP8[i44 >> 0] | 0) + -48 << 3) >> 2] | 0;
   } else {
    if (i51 | 0) {
     i62 = -1;
     break L1;
    }
    if (!i15) {
     i63 = i54;
     i64 = 0;
     i65 = i44;
     i66 = 0;
     break;
    }
    i46 = (HEAP32[i3 >> 2] | 0) + (4 - 1) & ~(4 - 1);
    i47 = HEAP32[i46 >> 2] | 0;
    HEAP32[i3 >> 2] = i46 + 4;
    i59 = 0;
    i60 = i44;
    i61 = i47;
   }
   if ((i61 | 0) < 0) {
    i63 = i54 | 8192;
    i64 = i59;
    i65 = i60;
    i66 = 0 - i61 | 0;
   } else {
    i63 = i54;
    i64 = i59;
    i65 = i60;
    i66 = i61;
   }
  } else {
   i47 = (i53 << 24 >> 24) + -48 | 0;
   if (i47 >>> 0 < 10) {
    i44 = i55;
    i46 = 0;
    i48 = i47;
    while (1) {
     i47 = (i46 * 10 | 0) + i48 | 0;
     i45 = i44 + 1 | 0;
     i48 = (HEAP8[i45 >> 0] | 0) + -48 | 0;
     if (i48 >>> 0 >= 10) {
      i67 = i47;
      i68 = i45;
      break;
     } else {
      i44 = i45;
      i46 = i47;
     }
    }
    if ((i67 | 0) < 0) {
     i62 = -1;
     break L1;
    } else {
     i63 = i54;
     i64 = i51;
     i65 = i68;
     i66 = i67;
    }
   } else {
    i63 = i54;
    i64 = i51;
    i65 = i55;
    i66 = 0;
   }
  } while (0);
  L46 : do if ((HEAP8[i65 >> 0] | 0) == 46) {
   i46 = i65 + 1 | 0;
   i44 = HEAP8[i46 >> 0] | 0;
   if (i44 << 24 >> 24 != 42) {
    i48 = (i44 << 24 >> 24) + -48 | 0;
    if (i48 >>> 0 < 10) {
     i69 = i46;
     i70 = 0;
     i71 = i48;
    } else {
     i72 = 0;
     i73 = i46;
     break;
    }
    while (1) {
     i46 = (i70 * 10 | 0) + i71 | 0;
     i48 = i69 + 1 | 0;
     i71 = (HEAP8[i48 >> 0] | 0) + -48 | 0;
     if (i71 >>> 0 >= 10) {
      i72 = i46;
      i73 = i48;
      break L46;
     } else {
      i69 = i48;
      i70 = i46;
     }
    }
   }
   i46 = i65 + 2 | 0;
   i48 = (HEAP8[i46 >> 0] | 0) + -48 | 0;
   if (i48 >>> 0 < 10 ? (HEAP8[i65 + 3 >> 0] | 0) == 36 : 0) {
    HEAP32[i5 + (i48 << 2) >> 2] = 10;
    i72 = HEAP32[i4 + ((HEAP8[i46 >> 0] | 0) + -48 << 3) >> 2] | 0;
    i73 = i65 + 4 | 0;
    break;
   }
   if (i64 | 0) {
    i62 = -1;
    break L1;
   }
   if (i15) {
    i48 = (HEAP32[i3 >> 2] | 0) + (4 - 1) & ~(4 - 1);
    i44 = HEAP32[i48 >> 2] | 0;
    HEAP32[i3 >> 2] = i48 + 4;
    i72 = i44;
    i73 = i46;
   } else {
    i72 = 0;
    i73 = i46;
   }
  } else {
   i72 = -1;
   i73 = i65;
  } while (0);
  i46 = i73;
  i44 = 0;
  while (1) {
   i48 = (HEAP8[i46 >> 0] | 0) + -65 | 0;
   if (i48 >>> 0 > 57) {
    i62 = -1;
    break L1;
   }
   i47 = i46 + 1 | 0;
   i45 = HEAP8[2871 + (i44 * 58 | 0) + i48 >> 0] | 0;
   i48 = i45 & 255;
   if ((i48 + -1 | 0) >>> 0 < 8) {
    i46 = i47;
    i44 = i48;
   } else {
    i74 = i47;
    i75 = i45;
    i76 = i48;
    i77 = i46;
    i78 = i44;
    break;
   }
  }
  if (!(i75 << 24 >> 24)) {
   i62 = -1;
   break;
  }
  i44 = (i50 | 0) > -1;
  do if (i75 << 24 >> 24 == 19) if (i44) {
   i62 = -1;
   break L1;
  } else i37 = 52; else {
   if (i44) {
    HEAP32[i5 + (i50 << 2) >> 2] = i76;
    i46 = i4 + (i50 << 3) | 0;
    i48 = HEAP32[i46 + 4 >> 2] | 0;
    i45 = i11;
    HEAP32[i45 >> 2] = HEAP32[i46 >> 2];
    HEAP32[i45 + 4 >> 2] = i48;
    i37 = 52;
    break;
   }
   if (!i15) {
    i62 = 0;
    break L1;
   }
   _pop_arg(i11, i76, i3);
  } while (0);
  if ((i37 | 0) == 52 ? (i37 = 0, !i15) : 0) {
   i30 = i34;
   i31 = i2;
   i32 = i64;
   i33 = i74;
   continue;
  }
  i44 = HEAP8[i77 >> 0] | 0;
  i48 = (i78 | 0) != 0 & (i44 & 15 | 0) == 3 ? i44 & -33 : i44;
  i44 = i63 & -65537;
  i45 = (i63 & 8192 | 0) == 0 ? i63 : i44;
  L75 : do switch (i48 | 0) {
  case 110:
   {
    switch (i78 | 0) {
    case 0:
     {
      HEAP32[HEAP32[i11 >> 2] >> 2] = i34;
      i30 = i34;
      i31 = i2;
      i32 = i64;
      i33 = i74;
      continue L1;
      break;
     }
    case 1:
     {
      HEAP32[HEAP32[i11 >> 2] >> 2] = i34;
      i30 = i34;
      i31 = i2;
      i32 = i64;
      i33 = i74;
      continue L1;
      break;
     }
    case 2:
     {
      i46 = HEAP32[i11 >> 2] | 0;
      HEAP32[i46 >> 2] = i34;
      HEAP32[i46 + 4 >> 2] = ((i34 | 0) < 0) << 31 >> 31;
      i30 = i34;
      i31 = i2;
      i32 = i64;
      i33 = i74;
      continue L1;
      break;
     }
    case 3:
     {
      HEAP16[HEAP32[i11 >> 2] >> 1] = i34;
      i30 = i34;
      i31 = i2;
      i32 = i64;
      i33 = i74;
      continue L1;
      break;
     }
    case 4:
     {
      HEAP8[HEAP32[i11 >> 2] >> 0] = i34;
      i30 = i34;
      i31 = i2;
      i32 = i64;
      i33 = i74;
      continue L1;
      break;
     }
    case 6:
     {
      HEAP32[HEAP32[i11 >> 2] >> 2] = i34;
      i30 = i34;
      i31 = i2;
      i32 = i64;
      i33 = i74;
      continue L1;
      break;
     }
    case 7:
     {
      i46 = HEAP32[i11 >> 2] | 0;
      HEAP32[i46 >> 2] = i34;
      HEAP32[i46 + 4 >> 2] = ((i34 | 0) < 0) << 31 >> 31;
      i30 = i34;
      i31 = i2;
      i32 = i64;
      i33 = i74;
      continue L1;
      break;
     }
    default:
     {
      i30 = i34;
      i31 = i2;
      i32 = i64;
      i33 = i74;
      continue L1;
     }
    }
    break;
   }
  case 112:
   {
    i79 = i45 | 8;
    i80 = i72 >>> 0 > 8 ? i72 : 8;
    i81 = 120;
    i37 = 64;
    break;
   }
  case 88:
  case 120:
   {
    i79 = i45;
    i80 = i72;
    i81 = i48;
    i37 = 64;
    break;
   }
  case 111:
   {
    i46 = i11;
    i47 = HEAP32[i46 >> 2] | 0;
    i57 = HEAP32[i46 + 4 >> 2] | 0;
    if ((i47 | 0) == 0 & (i57 | 0) == 0) i82 = i16; else {
     i46 = i16;
     i56 = i47;
     i47 = i57;
     while (1) {
      i57 = i46 + -1 | 0;
      HEAP8[i57 >> 0] = i56 & 7 | 48;
      i56 = _bitshift64Lshr(i56 | 0, i47 | 0, 3) | 0;
      i47 = tempRet0;
      if ((i56 | 0) == 0 & (i47 | 0) == 0) {
       i82 = i57;
       break;
      } else i46 = i57;
     }
    }
    if (!(i45 & 8)) {
     i83 = i82;
     i84 = i45;
     i85 = i72;
     i86 = 0;
     i87 = 3351;
     i37 = 77;
    } else {
     i46 = i17 - i82 | 0;
     i83 = i82;
     i84 = i45;
     i85 = (i72 | 0) > (i46 | 0) ? i72 : i46 + 1 | 0;
     i86 = 0;
     i87 = 3351;
     i37 = 77;
    }
    break;
   }
  case 105:
  case 100:
   {
    i46 = i11;
    i47 = HEAP32[i46 >> 2] | 0;
    i56 = HEAP32[i46 + 4 >> 2] | 0;
    if ((i56 | 0) < 0) {
     i46 = _i64Subtract(0, 0, i47 | 0, i56 | 0) | 0;
     i57 = tempRet0;
     i58 = i11;
     HEAP32[i58 >> 2] = i46;
     HEAP32[i58 + 4 >> 2] = i57;
     i88 = i46;
     i89 = i57;
     i90 = 1;
     i91 = 3351;
     i37 = 76;
     break L75;
    }
    if (!(i45 & 2048)) {
     i57 = i45 & 1;
     i88 = i47;
     i89 = i56;
     i90 = i57;
     i91 = (i57 | 0) == 0 ? 3351 : 3353;
     i37 = 76;
    } else {
     i88 = i47;
     i89 = i56;
     i90 = 1;
     i91 = 3352;
     i37 = 76;
    }
    break;
   }
  case 117:
   {
    i56 = i11;
    i88 = HEAP32[i56 >> 2] | 0;
    i89 = HEAP32[i56 + 4 >> 2] | 0;
    i90 = 0;
    i91 = 3351;
    i37 = 76;
    break;
   }
  case 99:
   {
    HEAP8[i18 >> 0] = HEAP32[i11 >> 2];
    i92 = i18;
    i93 = i44;
    i94 = 1;
    i95 = 0;
    i96 = 3351;
    i97 = i16;
    break;
   }
  case 109:
   {
    i98 = _strerror(HEAP32[(___errno_location() | 0) >> 2] | 0) | 0;
    i37 = 82;
    break;
   }
  case 115:
   {
    i56 = HEAP32[i11 >> 2] | 0;
    i98 = i56 | 0 ? i56 : 5253;
    i37 = 82;
    break;
   }
  case 67:
   {
    HEAP32[i13 >> 2] = HEAP32[i11 >> 2];
    HEAP32[i12 >> 2] = 0;
    HEAP32[i11 >> 2] = i13;
    i99 = i13;
    i100 = -1;
    i37 = 86;
    break;
   }
  case 83:
   {
    i56 = HEAP32[i11 >> 2] | 0;
    if (!i72) {
     _pad(i1, 32, i66, 0, i45);
     i101 = 0;
     i37 = 97;
    } else {
     i99 = i56;
     i100 = i72;
     i37 = 86;
    }
    break;
   }
  case 65:
  case 71:
  case 70:
  case 69:
  case 97:
  case 103:
  case 102:
  case 101:
   {
    d102 = +HEAPF64[i11 >> 3];
    HEAP32[i8 >> 2] = 0;
    HEAPF64[tempDoublePtr >> 3] = d102;
    if ((HEAP32[tempDoublePtr + 4 >> 2] | 0) >= 0) if (!(i45 & 2048)) {
     i56 = i45 & 1;
     d103 = d102;
     i104 = i56;
     i105 = (i56 | 0) == 0 ? 5261 : 5266;
    } else {
     d103 = d102;
     i104 = 1;
     i105 = 5263;
    } else {
     d103 = -d102;
     i104 = 1;
     i105 = 5260;
    }
    HEAPF64[tempDoublePtr >> 3] = d103;
    i56 = HEAP32[tempDoublePtr + 4 >> 2] & 2146435072;
    do if (i56 >>> 0 < 2146435072 | (i56 | 0) == 2146435072 & 0 < 0) {
     d102 = +_frexpl(d103, i8) * 2.0;
     i47 = d102 != 0.0;
     if (i47) HEAP32[i8 >> 2] = (HEAP32[i8 >> 2] | 0) + -1;
     i57 = i48 | 32;
     if ((i57 | 0) == 97) {
      i46 = i48 & 32;
      i58 = (i46 | 0) == 0 ? i105 : i105 + 9 | 0;
      i106 = i104 | 2;
      i107 = 12 - i72 | 0;
      do if (!(i72 >>> 0 > 11 | (i107 | 0) == 0)) {
       i108 = i107;
       d109 = 8.0;
       while (1) {
        i108 = i108 + -1 | 0;
        d110 = d109 * 16.0;
        if (!i108) {
         d111 = d110;
         break;
        } else d109 = d110;
       }
       if ((HEAP8[i58 >> 0] | 0) == 45) {
        d112 = -(d111 + (-d102 - d111));
        break;
       } else {
        d112 = d102 + d111 - d111;
        break;
       }
      } else d112 = d102; while (0);
      i107 = HEAP32[i8 >> 2] | 0;
      i108 = (i107 | 0) < 0 ? 0 - i107 | 0 : i107;
      i113 = _fmt_u(i108, ((i108 | 0) < 0) << 31 >> 31, i21) | 0;
      if ((i113 | 0) == (i21 | 0)) {
       HEAP8[i22 >> 0] = 48;
       i114 = i22;
      } else i114 = i113;
      HEAP8[i114 + -1 >> 0] = (i107 >> 31 & 2) + 43;
      i107 = i114 + -2 | 0;
      HEAP8[i107 >> 0] = i48 + 15;
      i113 = (i72 | 0) < 1;
      i108 = (i45 & 8 | 0) == 0;
      d109 = d112;
      i115 = i9;
      while (1) {
       i116 = ~~d109;
       i117 = i115 + 1 | 0;
       HEAP8[i115 >> 0] = HEAPU8[3335 + i116 >> 0] | i46;
       d109 = (d109 - +(i116 | 0)) * 16.0;
       do if ((i117 - i19 | 0) == 1) {
        if (i108 & (i113 & d109 == 0.0)) {
         i118 = i117;
         break;
        }
        HEAP8[i117 >> 0] = 46;
        i118 = i115 + 2 | 0;
       } else i118 = i117; while (0);
       if (!(d109 != 0.0)) {
        i119 = i118;
        break;
       } else i115 = i118;
      }
      i115 = i119;
      i113 = i107;
      i108 = (i72 | 0) != 0 & (i24 + i115 | 0) < (i72 | 0) ? i25 + i72 - i113 | 0 : i23 - i113 + i115 | 0;
      i46 = i108 + i106 | 0;
      _pad(i1, 32, i66, i46, i45);
      if (!(HEAP32[i1 >> 2] & 32)) ___fwritex(i58, i106, i1) | 0;
      _pad(i1, 48, i66, i46, i45 ^ 65536);
      i117 = i115 - i19 | 0;
      if (!(HEAP32[i1 >> 2] & 32)) ___fwritex(i9, i117, i1) | 0;
      i115 = i10 - i113 | 0;
      _pad(i1, 48, i108 - (i117 + i115) | 0, 0, 0);
      if (!(HEAP32[i1 >> 2] & 32)) ___fwritex(i107, i115, i1) | 0;
      _pad(i1, 32, i66, i46, i45 ^ 8192);
      i120 = (i46 | 0) < (i66 | 0) ? i66 : i46;
      break;
     }
     i46 = (i72 | 0) < 0 ? 6 : i72;
     if (i47) {
      i115 = (HEAP32[i8 >> 2] | 0) + -28 | 0;
      HEAP32[i8 >> 2] = i115;
      d121 = d102 * 268435456.0;
      i122 = i115;
     } else {
      d121 = d102;
      i122 = HEAP32[i8 >> 2] | 0;
     }
     i115 = (i122 | 0) < 0 ? i7 : i26;
     i117 = i115;
     d109 = d121;
     i108 = i115;
     while (1) {
      i113 = ~~d109 >>> 0;
      HEAP32[i108 >> 2] = i113;
      i116 = i108 + 4 | 0;
      d109 = (d109 - +(i113 >>> 0)) * 1.0e9;
      if (!(d109 != 0.0)) {
       i123 = i116;
       break;
      } else i108 = i116;
     }
     i108 = HEAP32[i8 >> 2] | 0;
     if ((i108 | 0) > 0) {
      i47 = i108;
      i107 = i115;
      i106 = i123;
      while (1) {
       i58 = (i47 | 0) > 29 ? 29 : i47;
       i116 = i106 + -4 | 0;
       do if (i116 >>> 0 < i107 >>> 0) i124 = i107; else {
        i113 = 0;
        i125 = i116;
        while (1) {
         i126 = _bitshift64Shl(HEAP32[i125 >> 2] | 0, 0, i58 | 0) | 0;
         i127 = _i64Add(i126 | 0, tempRet0 | 0, i113 | 0, 0) | 0;
         i126 = tempRet0;
         i128 = ___uremdi3(i127 | 0, i126 | 0, 1e9, 0) | 0;
         HEAP32[i125 >> 2] = i128;
         i128 = ___udivdi3(i127 | 0, i126 | 0, 1e9, 0) | 0;
         i125 = i125 + -4 | 0;
         if (i125 >>> 0 < i107 >>> 0) {
          i129 = i128;
          break;
         } else i113 = i128;
        }
        if (!i129) {
         i124 = i107;
         break;
        }
        i113 = i107 + -4 | 0;
        HEAP32[i113 >> 2] = i129;
        i124 = i113;
       } while (0);
       i116 = i106;
       while (1) {
        if (i116 >>> 0 <= i124 >>> 0) {
         i130 = i116;
         break;
        }
        i113 = i116 + -4 | 0;
        if (!(HEAP32[i113 >> 2] | 0)) i116 = i113; else {
         i130 = i116;
         break;
        }
       }
       i116 = (HEAP32[i8 >> 2] | 0) - i58 | 0;
       HEAP32[i8 >> 2] = i116;
       if ((i116 | 0) > 0) {
        i47 = i116;
        i107 = i124;
        i106 = i130;
       } else {
        i131 = i116;
        i132 = i124;
        i133 = i130;
        break;
       }
      }
     } else {
      i131 = i108;
      i132 = i115;
      i133 = i123;
     }
     if ((i131 | 0) < 0) {
      i106 = ((i46 + 25 | 0) / 9 | 0) + 1 | 0;
      i107 = (i57 | 0) == 102;
      i47 = i131;
      i116 = i132;
      i113 = i133;
      while (1) {
       i125 = 0 - i47 | 0;
       i128 = (i125 | 0) > 9 ? 9 : i125;
       do if (i116 >>> 0 < i113 >>> 0) {
        i125 = (1 << i128) + -1 | 0;
        i126 = 1e9 >>> i128;
        i127 = 0;
        i134 = i116;
        while (1) {
         i135 = HEAP32[i134 >> 2] | 0;
         HEAP32[i134 >> 2] = (i135 >>> i128) + i127;
         i136 = Math_imul(i135 & i125, i126) | 0;
         i134 = i134 + 4 | 0;
         if (i134 >>> 0 >= i113 >>> 0) {
          i137 = i136;
          break;
         } else i127 = i136;
        }
        i127 = (HEAP32[i116 >> 2] | 0) == 0 ? i116 + 4 | 0 : i116;
        if (!i137) {
         i138 = i127;
         i139 = i113;
         break;
        }
        HEAP32[i113 >> 2] = i137;
        i138 = i127;
        i139 = i113 + 4 | 0;
       } else {
        i138 = (HEAP32[i116 >> 2] | 0) == 0 ? i116 + 4 | 0 : i116;
        i139 = i113;
       } while (0);
       i58 = i107 ? i115 : i138;
       i127 = (i139 - i58 >> 2 | 0) > (i106 | 0) ? i58 + (i106 << 2) | 0 : i139;
       i47 = (HEAP32[i8 >> 2] | 0) + i128 | 0;
       HEAP32[i8 >> 2] = i47;
       if ((i47 | 0) >= 0) {
        i140 = i138;
        i141 = i127;
        break;
       } else {
        i116 = i138;
        i113 = i127;
       }
      }
     } else {
      i140 = i132;
      i141 = i133;
     }
     do if (i140 >>> 0 < i141 >>> 0) {
      i113 = (i117 - i140 >> 2) * 9 | 0;
      i116 = HEAP32[i140 >> 2] | 0;
      if (i116 >>> 0 < 10) {
       i142 = i113;
       break;
      } else {
       i143 = i113;
       i144 = 10;
      }
      while (1) {
       i144 = i144 * 10 | 0;
       i113 = i143 + 1 | 0;
       if (i116 >>> 0 < i144 >>> 0) {
        i142 = i113;
        break;
       } else i143 = i113;
      }
     } else i142 = 0; while (0);
     i116 = (i57 | 0) == 103;
     i128 = (i46 | 0) != 0;
     i113 = i46 - ((i57 | 0) != 102 ? i142 : 0) + ((i128 & i116) << 31 >> 31) | 0;
     if ((i113 | 0) < (((i141 - i117 >> 2) * 9 | 0) + -9 | 0)) {
      i47 = i113 + 9216 | 0;
      i113 = i115 + 4 + (((i47 | 0) / 9 | 0) + -1024 << 2) | 0;
      i106 = ((i47 | 0) % 9 | 0) + 1 | 0;
      if ((i106 | 0) < 9) {
       i47 = 10;
       i107 = i106;
       while (1) {
        i106 = i47 * 10 | 0;
        i107 = i107 + 1 | 0;
        if ((i107 | 0) == 9) {
         i145 = i106;
         break;
        } else i47 = i106;
       }
      } else i145 = 10;
      i47 = HEAP32[i113 >> 2] | 0;
      i107 = (i47 >>> 0) % (i145 >>> 0) | 0;
      i57 = (i113 + 4 | 0) == (i141 | 0);
      do if (i57 & (i107 | 0) == 0) {
       i146 = i140;
       i147 = i113;
       i148 = i142;
      } else {
       d109 = (((i47 >>> 0) / (i145 >>> 0) | 0) & 1 | 0) == 0 ? 9007199254740992.0 : 9007199254740994.0;
       i106 = (i145 | 0) / 2 | 0;
       if (i107 >>> 0 < i106 >>> 0) d149 = .5; else d149 = i57 & (i107 | 0) == (i106 | 0) ? 1.0 : 1.5;
       do if (!i104) {
        d150 = d109;
        d151 = d149;
       } else {
        if ((HEAP8[i105 >> 0] | 0) != 45) {
         d150 = d109;
         d151 = d149;
         break;
        }
        d150 = -d109;
        d151 = -d149;
       } while (0);
       i106 = i47 - i107 | 0;
       HEAP32[i113 >> 2] = i106;
       if (!(d150 + d151 != d150)) {
        i146 = i140;
        i147 = i113;
        i148 = i142;
        break;
       }
       i108 = i106 + i145 | 0;
       HEAP32[i113 >> 2] = i108;
       if (i108 >>> 0 > 999999999) {
        i108 = i140;
        i106 = i113;
        while (1) {
         i127 = i106 + -4 | 0;
         HEAP32[i106 >> 2] = 0;
         if (i127 >>> 0 < i108 >>> 0) {
          i58 = i108 + -4 | 0;
          HEAP32[i58 >> 2] = 0;
          i152 = i58;
         } else i152 = i108;
         i58 = (HEAP32[i127 >> 2] | 0) + 1 | 0;
         HEAP32[i127 >> 2] = i58;
         if (i58 >>> 0 > 999999999) {
          i108 = i152;
          i106 = i127;
         } else {
          i153 = i152;
          i154 = i127;
          break;
         }
        }
       } else {
        i153 = i140;
        i154 = i113;
       }
       i106 = (i117 - i153 >> 2) * 9 | 0;
       i108 = HEAP32[i153 >> 2] | 0;
       if (i108 >>> 0 < 10) {
        i146 = i153;
        i147 = i154;
        i148 = i106;
        break;
       } else {
        i155 = i106;
        i156 = 10;
       }
       while (1) {
        i156 = i156 * 10 | 0;
        i106 = i155 + 1 | 0;
        if (i108 >>> 0 < i156 >>> 0) {
         i146 = i153;
         i147 = i154;
         i148 = i106;
         break;
        } else i155 = i106;
       }
      } while (0);
      i113 = i147 + 4 | 0;
      i157 = i146;
      i158 = i148;
      i159 = i141 >>> 0 > i113 >>> 0 ? i113 : i141;
     } else {
      i157 = i140;
      i158 = i142;
      i159 = i141;
     }
     i113 = 0 - i158 | 0;
     i107 = i159;
     while (1) {
      if (i107 >>> 0 <= i157 >>> 0) {
       i160 = 0;
       i161 = i107;
       break;
      }
      i47 = i107 + -4 | 0;
      if (!(HEAP32[i47 >> 2] | 0)) i107 = i47; else {
       i160 = 1;
       i161 = i107;
       break;
      }
     }
     do if (i116) {
      i107 = (i128 & 1 ^ 1) + i46 | 0;
      if ((i107 | 0) > (i158 | 0) & (i158 | 0) > -5) {
       i162 = i48 + -1 | 0;
       i163 = i107 + -1 - i158 | 0;
      } else {
       i162 = i48 + -2 | 0;
       i163 = i107 + -1 | 0;
      }
      i107 = i45 & 8;
      if (i107 | 0) {
       i164 = i162;
       i165 = i163;
       i166 = i107;
       break;
      }
      do if (i160) {
       i107 = HEAP32[i161 + -4 >> 2] | 0;
       if (!i107) {
        i167 = 9;
        break;
       }
       if (!((i107 >>> 0) % 10 | 0)) {
        i168 = 10;
        i169 = 0;
       } else {
        i167 = 0;
        break;
       }
       while (1) {
        i168 = i168 * 10 | 0;
        i47 = i169 + 1 | 0;
        if ((i107 >>> 0) % (i168 >>> 0) | 0 | 0) {
         i167 = i47;
         break;
        } else i169 = i47;
       }
      } else i167 = 9; while (0);
      i107 = ((i161 - i117 >> 2) * 9 | 0) + -9 | 0;
      if ((i162 | 32 | 0) == 102) {
       i47 = i107 - i167 | 0;
       i57 = (i47 | 0) < 0 ? 0 : i47;
       i164 = i162;
       i165 = (i163 | 0) < (i57 | 0) ? i163 : i57;
       i166 = 0;
       break;
      } else {
       i57 = i107 + i158 - i167 | 0;
       i107 = (i57 | 0) < 0 ? 0 : i57;
       i164 = i162;
       i165 = (i163 | 0) < (i107 | 0) ? i163 : i107;
       i166 = 0;
       break;
      }
     } else {
      i164 = i48;
      i165 = i46;
      i166 = i45 & 8;
     } while (0);
     i46 = i165 | i166;
     i117 = (i46 | 0) != 0 & 1;
     i128 = (i164 | 32 | 0) == 102;
     if (i128) {
      i170 = (i158 | 0) > 0 ? i158 : 0;
      i171 = 0;
     } else {
      i116 = (i158 | 0) < 0 ? i113 : i158;
      i107 = _fmt_u(i116, ((i116 | 0) < 0) << 31 >> 31, i21) | 0;
      if ((i10 - i107 | 0) < 2) {
       i116 = i107;
       while (1) {
        i57 = i116 + -1 | 0;
        HEAP8[i57 >> 0] = 48;
        if ((i10 - i57 | 0) < 2) i116 = i57; else {
         i172 = i57;
         break;
        }
       }
      } else i172 = i107;
      HEAP8[i172 + -1 >> 0] = (i158 >> 31 & 2) + 43;
      i116 = i172 + -2 | 0;
      HEAP8[i116 >> 0] = i164;
      i170 = i10 - i116 | 0;
      i171 = i116;
     }
     i116 = i104 + 1 + i165 + i117 + i170 | 0;
     _pad(i1, 32, i66, i116, i45);
     if (!(HEAP32[i1 >> 2] & 32)) ___fwritex(i105, i104, i1) | 0;
     _pad(i1, 48, i66, i116, i45 ^ 65536);
     do if (i128) {
      i113 = i157 >>> 0 > i115 >>> 0 ? i115 : i157;
      i57 = i113;
      while (1) {
       i47 = _fmt_u(HEAP32[i57 >> 2] | 0, 0, i27) | 0;
       do if ((i57 | 0) == (i113 | 0)) {
        if ((i47 | 0) != (i27 | 0)) {
         i173 = i47;
         break;
        }
        HEAP8[i29 >> 0] = 48;
        i173 = i29;
       } else {
        if (i47 >>> 0 <= i9 >>> 0) {
         i173 = i47;
         break;
        }
        _memset(i9 | 0, 48, i47 - i19 | 0) | 0;
        i108 = i47;
        while (1) {
         i106 = i108 + -1 | 0;
         if (i106 >>> 0 > i9 >>> 0) i108 = i106; else {
          i173 = i106;
          break;
         }
        }
       } while (0);
       if (!(HEAP32[i1 >> 2] & 32)) ___fwritex(i173, i28 - i173 | 0, i1) | 0;
       i47 = i57 + 4 | 0;
       if (i47 >>> 0 > i115 >>> 0) {
        i174 = i47;
        break;
       } else i57 = i47;
      }
      do if (i46 | 0) {
       if (HEAP32[i1 >> 2] & 32 | 0) break;
       ___fwritex(5295, 1, i1) | 0;
      } while (0);
      if ((i165 | 0) > 0 & i174 >>> 0 < i161 >>> 0) {
       i57 = i165;
       i113 = i174;
       while (1) {
        i47 = _fmt_u(HEAP32[i113 >> 2] | 0, 0, i27) | 0;
        if (i47 >>> 0 > i9 >>> 0) {
         _memset(i9 | 0, 48, i47 - i19 | 0) | 0;
         i108 = i47;
         while (1) {
          i106 = i108 + -1 | 0;
          if (i106 >>> 0 > i9 >>> 0) i108 = i106; else {
           i175 = i106;
           break;
          }
         }
        } else i175 = i47;
        if (!(HEAP32[i1 >> 2] & 32)) ___fwritex(i175, (i57 | 0) > 9 ? 9 : i57, i1) | 0;
        i113 = i113 + 4 | 0;
        i108 = i57 + -9 | 0;
        if (!((i57 | 0) > 9 & i113 >>> 0 < i161 >>> 0)) {
         i176 = i108;
         break;
        } else i57 = i108;
       }
      } else i176 = i165;
      _pad(i1, 48, i176 + 9 | 0, 9, 0);
     } else {
      i57 = i160 ? i161 : i157 + 4 | 0;
      if ((i165 | 0) > -1) {
       i113 = (i166 | 0) == 0;
       i108 = i165;
       i106 = i157;
       while (1) {
        i127 = _fmt_u(HEAP32[i106 >> 2] | 0, 0, i27) | 0;
        if ((i127 | 0) == (i27 | 0)) {
         HEAP8[i29 >> 0] = 48;
         i177 = i29;
        } else i177 = i127;
        do if ((i106 | 0) == (i157 | 0)) {
         i127 = i177 + 1 | 0;
         if (!(HEAP32[i1 >> 2] & 32)) ___fwritex(i177, 1, i1) | 0;
         if (i113 & (i108 | 0) < 1) {
          i178 = i127;
          break;
         }
         if (HEAP32[i1 >> 2] & 32 | 0) {
          i178 = i127;
          break;
         }
         ___fwritex(5295, 1, i1) | 0;
         i178 = i127;
        } else {
         if (i177 >>> 0 <= i9 >>> 0) {
          i178 = i177;
          break;
         }
         _memset(i9 | 0, 48, i177 + i20 | 0) | 0;
         i127 = i177;
         while (1) {
          i58 = i127 + -1 | 0;
          if (i58 >>> 0 > i9 >>> 0) i127 = i58; else {
           i178 = i58;
           break;
          }
         }
        } while (0);
        i47 = i28 - i178 | 0;
        if (!(HEAP32[i1 >> 2] & 32)) ___fwritex(i178, (i108 | 0) > (i47 | 0) ? i47 : i108, i1) | 0;
        i127 = i108 - i47 | 0;
        i106 = i106 + 4 | 0;
        if (!(i106 >>> 0 < i57 >>> 0 & (i127 | 0) > -1)) {
         i179 = i127;
         break;
        } else i108 = i127;
       }
      } else i179 = i165;
      _pad(i1, 48, i179 + 18 | 0, 18, 0);
      if (HEAP32[i1 >> 2] & 32 | 0) break;
      ___fwritex(i171, i10 - i171 | 0, i1) | 0;
     } while (0);
     _pad(i1, 32, i66, i116, i45 ^ 8192);
     i120 = (i116 | 0) < (i66 | 0) ? i66 : i116;
    } else {
     i46 = (i48 & 32 | 0) != 0;
     i115 = d103 != d103 | 0.0 != 0.0;
     i128 = i115 ? 0 : i104;
     i117 = i128 + 3 | 0;
     _pad(i1, 32, i66, i117, i44);
     i107 = HEAP32[i1 >> 2] | 0;
     if (!(i107 & 32)) {
      ___fwritex(i105, i128, i1) | 0;
      i180 = HEAP32[i1 >> 2] | 0;
     } else i180 = i107;
     if (!(i180 & 32)) ___fwritex(i115 ? (i46 ? 5287 : 5291) : i46 ? 5279 : 5283, 3, i1) | 0;
     _pad(i1, 32, i66, i117, i45 ^ 8192);
     i120 = (i117 | 0) < (i66 | 0) ? i66 : i117;
    } while (0);
    i30 = i34;
    i31 = i120;
    i32 = i64;
    i33 = i74;
    continue L1;
    break;
   }
  default:
   {
    i92 = i33;
    i93 = i45;
    i94 = i72;
    i95 = 0;
    i96 = 3351;
    i97 = i16;
   }
  } while (0);
  L311 : do if ((i37 | 0) == 64) {
   i37 = 0;
   i48 = i11;
   i2 = HEAP32[i48 >> 2] | 0;
   i56 = HEAP32[i48 + 4 >> 2] | 0;
   i48 = i81 & 32;
   if (!((i2 | 0) == 0 & (i56 | 0) == 0)) {
    i117 = i16;
    i46 = i2;
    i2 = i56;
    while (1) {
     i56 = i117 + -1 | 0;
     HEAP8[i56 >> 0] = HEAPU8[3335 + (i46 & 15) >> 0] | i48;
     i46 = _bitshift64Lshr(i46 | 0, i2 | 0, 4) | 0;
     i2 = tempRet0;
     if ((i46 | 0) == 0 & (i2 | 0) == 0) {
      i181 = i56;
      break;
     } else i117 = i56;
    }
    i117 = i11;
    if ((i79 & 8 | 0) == 0 | (HEAP32[i117 >> 2] | 0) == 0 & (HEAP32[i117 + 4 >> 2] | 0) == 0) {
     i83 = i181;
     i84 = i79;
     i85 = i80;
     i86 = 0;
     i87 = 3351;
     i37 = 77;
    } else {
     i83 = i181;
     i84 = i79;
     i85 = i80;
     i86 = 2;
     i87 = 3351 + (i81 >> 4) | 0;
     i37 = 77;
    }
   } else {
    i83 = i16;
    i84 = i79;
    i85 = i80;
    i86 = 0;
    i87 = 3351;
    i37 = 77;
   }
  } else if ((i37 | 0) == 76) {
   i37 = 0;
   i83 = _fmt_u(i88, i89, i16) | 0;
   i84 = i45;
   i85 = i72;
   i86 = i90;
   i87 = i91;
   i37 = 77;
  } else if ((i37 | 0) == 82) {
   i37 = 0;
   i117 = _memchr(i98, 0, i72) | 0;
   i2 = (i117 | 0) == 0;
   i92 = i98;
   i93 = i44;
   i94 = i2 ? i72 : i117 - i98 | 0;
   i95 = 0;
   i96 = 3351;
   i97 = i2 ? i98 + i72 | 0 : i117;
  } else if ((i37 | 0) == 86) {
   i37 = 0;
   i117 = 0;
   i2 = 0;
   i46 = i99;
   while (1) {
    i48 = HEAP32[i46 >> 2] | 0;
    if (!i48) {
     i182 = i117;
     i183 = i2;
     break;
    }
    i56 = _wctomb(i14, i48) | 0;
    if ((i56 | 0) < 0 | i56 >>> 0 > (i100 - i117 | 0) >>> 0) {
     i182 = i117;
     i183 = i56;
     break;
    }
    i48 = i56 + i117 | 0;
    if (i100 >>> 0 > i48 >>> 0) {
     i117 = i48;
     i2 = i56;
     i46 = i46 + 4 | 0;
    } else {
     i182 = i48;
     i183 = i56;
     break;
    }
   }
   if ((i183 | 0) < 0) {
    i62 = -1;
    break L1;
   }
   _pad(i1, 32, i66, i182, i45);
   if (!i182) {
    i101 = 0;
    i37 = 97;
   } else {
    i46 = 0;
    i2 = i99;
    while (1) {
     i117 = HEAP32[i2 >> 2] | 0;
     if (!i117) {
      i101 = i182;
      i37 = 97;
      break L311;
     }
     i56 = _wctomb(i14, i117) | 0;
     i46 = i56 + i46 | 0;
     if ((i46 | 0) > (i182 | 0)) {
      i101 = i182;
      i37 = 97;
      break L311;
     }
     if (!(HEAP32[i1 >> 2] & 32)) ___fwritex(i14, i56, i1) | 0;
     if (i46 >>> 0 >= i182 >>> 0) {
      i101 = i182;
      i37 = 97;
      break;
     } else i2 = i2 + 4 | 0;
    }
   }
  } while (0);
  if ((i37 | 0) == 97) {
   i37 = 0;
   _pad(i1, 32, i66, i101, i45 ^ 8192);
   i30 = i34;
   i31 = (i66 | 0) > (i101 | 0) ? i66 : i101;
   i32 = i64;
   i33 = i74;
   continue;
  }
  if ((i37 | 0) == 77) {
   i37 = 0;
   i44 = (i85 | 0) > -1 ? i84 & -65537 : i84;
   i2 = i11;
   i46 = (HEAP32[i2 >> 2] | 0) != 0 | (HEAP32[i2 + 4 >> 2] | 0) != 0;
   if ((i85 | 0) != 0 | i46) {
    i2 = (i46 & 1 ^ 1) + (i17 - i83) | 0;
    i92 = i83;
    i93 = i44;
    i94 = (i85 | 0) > (i2 | 0) ? i85 : i2;
    i95 = i86;
    i96 = i87;
    i97 = i16;
   } else {
    i92 = i16;
    i93 = i44;
    i94 = 0;
    i95 = i86;
    i96 = i87;
    i97 = i16;
   }
  }
  i44 = i97 - i92 | 0;
  i2 = (i94 | 0) < (i44 | 0) ? i44 : i94;
  i46 = i95 + i2 | 0;
  i56 = (i66 | 0) < (i46 | 0) ? i46 : i66;
  _pad(i1, 32, i56, i46, i93);
  if (!(HEAP32[i1 >> 2] & 32)) ___fwritex(i96, i95, i1) | 0;
  _pad(i1, 48, i56, i46, i93 ^ 65536);
  _pad(i1, 48, i2, i44, 0);
  if (!(HEAP32[i1 >> 2] & 32)) ___fwritex(i92, i44, i1) | 0;
  _pad(i1, 32, i56, i46, i93 ^ 8192);
  i30 = i34;
  i31 = i56;
  i32 = i64;
  i33 = i74;
 }
 L345 : do if ((i37 | 0) == 244) if (!i1) if (i36) {
  i74 = 1;
  while (1) {
   i33 = HEAP32[i5 + (i74 << 2) >> 2] | 0;
   if (!i33) {
    i184 = i74;
    break;
   }
   _pop_arg(i4 + (i74 << 3) | 0, i33, i3);
   i74 = i74 + 1 | 0;
   if ((i74 | 0) >= 10) {
    i62 = 1;
    break L345;
   }
  }
  if ((i184 | 0) < 10) {
   i74 = i184;
   while (1) {
    if (HEAP32[i5 + (i74 << 2) >> 2] | 0) {
     i62 = -1;
     break L345;
    }
    i74 = i74 + 1 | 0;
    if ((i74 | 0) >= 10) {
     i62 = 1;
     break;
    }
   }
  } else i62 = 1;
 } else i62 = 0; else i62 = i35; while (0);
 STACKTOP = i6;
 return i62 | 0;
}

function _malloc(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0, i17 = 0, i18 = 0, i19 = 0, i20 = 0, i21 = 0, i22 = 0, i23 = 0, i24 = 0, i25 = 0, i26 = 0, i27 = 0, i28 = 0, i29 = 0, i30 = 0, i31 = 0, i32 = 0, i33 = 0, i34 = 0, i35 = 0, i36 = 0, i37 = 0, i38 = 0, i39 = 0, i40 = 0, i41 = 0, i42 = 0, i43 = 0, i44 = 0, i45 = 0, i46 = 0, i47 = 0, i48 = 0, i49 = 0, i50 = 0, i51 = 0, i52 = 0, i53 = 0, i54 = 0, i55 = 0, i56 = 0, i57 = 0, i58 = 0, i59 = 0, i60 = 0, i61 = 0, i62 = 0, i63 = 0, i64 = 0, i65 = 0, i66 = 0, i67 = 0, i68 = 0, i69 = 0, i70 = 0, i71 = 0, i72 = 0, i73 = 0, i74 = 0, i75 = 0, i76 = 0, i77 = 0, i78 = 0, i79 = 0, i80 = 0, i81 = 0, i82 = 0, i83 = 0, i84 = 0, i85 = 0, i86 = 0, i87 = 0, i88 = 0, i89 = 0, i90 = 0, i91 = 0, i92 = 0;
 do if (i1 >>> 0 < 245) {
  i2 = i1 >>> 0 < 11 ? 16 : i1 + 11 & -8;
  i3 = i2 >>> 3;
  i4 = HEAP32[1347] | 0;
  i5 = i4 >>> i3;
  if (i5 & 3 | 0) {
   i6 = (i5 & 1 ^ 1) + i3 | 0;
   i7 = 5428 + (i6 << 1 << 2) | 0;
   i8 = i7 + 8 | 0;
   i9 = HEAP32[i8 >> 2] | 0;
   i10 = i9 + 8 | 0;
   i11 = HEAP32[i10 >> 2] | 0;
   do if ((i7 | 0) != (i11 | 0)) {
    if (i11 >>> 0 < (HEAP32[1351] | 0) >>> 0) _abort();
    i12 = i11 + 12 | 0;
    if ((HEAP32[i12 >> 2] | 0) == (i9 | 0)) {
     HEAP32[i12 >> 2] = i7;
     HEAP32[i8 >> 2] = i11;
     break;
    } else _abort();
   } else HEAP32[1347] = i4 & ~(1 << i6); while (0);
   i11 = i6 << 3;
   HEAP32[i9 + 4 >> 2] = i11 | 3;
   i8 = i9 + i11 + 4 | 0;
   HEAP32[i8 >> 2] = HEAP32[i8 >> 2] | 1;
   i13 = i10;
   return i13 | 0;
  }
  i8 = HEAP32[1349] | 0;
  if (i2 >>> 0 > i8 >>> 0) {
   if (i5 | 0) {
    i11 = 2 << i3;
    i7 = i5 << i3 & (i11 | 0 - i11);
    i11 = (i7 & 0 - i7) + -1 | 0;
    i7 = i11 >>> 12 & 16;
    i12 = i11 >>> i7;
    i11 = i12 >>> 5 & 8;
    i14 = i12 >>> i11;
    i12 = i14 >>> 2 & 4;
    i15 = i14 >>> i12;
    i14 = i15 >>> 1 & 2;
    i16 = i15 >>> i14;
    i15 = i16 >>> 1 & 1;
    i17 = (i11 | i7 | i12 | i14 | i15) + (i16 >>> i15) | 0;
    i15 = 5428 + (i17 << 1 << 2) | 0;
    i16 = i15 + 8 | 0;
    i14 = HEAP32[i16 >> 2] | 0;
    i12 = i14 + 8 | 0;
    i7 = HEAP32[i12 >> 2] | 0;
    do if ((i15 | 0) != (i7 | 0)) {
     if (i7 >>> 0 < (HEAP32[1351] | 0) >>> 0) _abort();
     i11 = i7 + 12 | 0;
     if ((HEAP32[i11 >> 2] | 0) == (i14 | 0)) {
      HEAP32[i11 >> 2] = i15;
      HEAP32[i16 >> 2] = i7;
      i18 = HEAP32[1349] | 0;
      break;
     } else _abort();
    } else {
     HEAP32[1347] = i4 & ~(1 << i17);
     i18 = i8;
    } while (0);
    i8 = (i17 << 3) - i2 | 0;
    HEAP32[i14 + 4 >> 2] = i2 | 3;
    i4 = i14 + i2 | 0;
    HEAP32[i4 + 4 >> 2] = i8 | 1;
    HEAP32[i4 + i8 >> 2] = i8;
    if (i18 | 0) {
     i7 = HEAP32[1352] | 0;
     i16 = i18 >>> 3;
     i15 = 5428 + (i16 << 1 << 2) | 0;
     i3 = HEAP32[1347] | 0;
     i5 = 1 << i16;
     if (i3 & i5) {
      i16 = i15 + 8 | 0;
      i10 = HEAP32[i16 >> 2] | 0;
      if (i10 >>> 0 < (HEAP32[1351] | 0) >>> 0) _abort(); else {
       i19 = i16;
       i20 = i10;
      }
     } else {
      HEAP32[1347] = i3 | i5;
      i19 = i15 + 8 | 0;
      i20 = i15;
     }
     HEAP32[i19 >> 2] = i7;
     HEAP32[i20 + 12 >> 2] = i7;
     HEAP32[i7 + 8 >> 2] = i20;
     HEAP32[i7 + 12 >> 2] = i15;
    }
    HEAP32[1349] = i8;
    HEAP32[1352] = i4;
    i13 = i12;
    return i13 | 0;
   }
   i4 = HEAP32[1348] | 0;
   if (i4) {
    i8 = (i4 & 0 - i4) + -1 | 0;
    i4 = i8 >>> 12 & 16;
    i15 = i8 >>> i4;
    i8 = i15 >>> 5 & 8;
    i7 = i15 >>> i8;
    i15 = i7 >>> 2 & 4;
    i5 = i7 >>> i15;
    i7 = i5 >>> 1 & 2;
    i3 = i5 >>> i7;
    i5 = i3 >>> 1 & 1;
    i10 = HEAP32[5692 + ((i8 | i4 | i15 | i7 | i5) + (i3 >>> i5) << 2) >> 2] | 0;
    i5 = (HEAP32[i10 + 4 >> 2] & -8) - i2 | 0;
    i3 = i10;
    i7 = i10;
    while (1) {
     i10 = HEAP32[i3 + 16 >> 2] | 0;
     if (!i10) {
      i15 = HEAP32[i3 + 20 >> 2] | 0;
      if (!i15) {
       i21 = i5;
       i22 = i7;
       break;
      } else i23 = i15;
     } else i23 = i10;
     i10 = (HEAP32[i23 + 4 >> 2] & -8) - i2 | 0;
     i15 = i10 >>> 0 < i5 >>> 0;
     i5 = i15 ? i10 : i5;
     i3 = i23;
     i7 = i15 ? i23 : i7;
    }
    i7 = HEAP32[1351] | 0;
    if (i22 >>> 0 < i7 >>> 0) _abort();
    i3 = i22 + i2 | 0;
    if (i22 >>> 0 >= i3 >>> 0) _abort();
    i5 = HEAP32[i22 + 24 >> 2] | 0;
    i12 = HEAP32[i22 + 12 >> 2] | 0;
    do if ((i12 | 0) == (i22 | 0)) {
     i14 = i22 + 20 | 0;
     i17 = HEAP32[i14 >> 2] | 0;
     if (!i17) {
      i15 = i22 + 16 | 0;
      i10 = HEAP32[i15 >> 2] | 0;
      if (!i10) {
       i24 = 0;
       break;
      } else {
       i25 = i10;
       i26 = i15;
      }
     } else {
      i25 = i17;
      i26 = i14;
     }
     while (1) {
      i14 = i25 + 20 | 0;
      i17 = HEAP32[i14 >> 2] | 0;
      if (i17 | 0) {
       i25 = i17;
       i26 = i14;
       continue;
      }
      i14 = i25 + 16 | 0;
      i17 = HEAP32[i14 >> 2] | 0;
      if (!i17) {
       i27 = i25;
       i28 = i26;
       break;
      } else {
       i25 = i17;
       i26 = i14;
      }
     }
     if (i28 >>> 0 < i7 >>> 0) _abort(); else {
      HEAP32[i28 >> 2] = 0;
      i24 = i27;
      break;
     }
    } else {
     i14 = HEAP32[i22 + 8 >> 2] | 0;
     if (i14 >>> 0 < i7 >>> 0) _abort();
     i17 = i14 + 12 | 0;
     if ((HEAP32[i17 >> 2] | 0) != (i22 | 0)) _abort();
     i15 = i12 + 8 | 0;
     if ((HEAP32[i15 >> 2] | 0) == (i22 | 0)) {
      HEAP32[i17 >> 2] = i12;
      HEAP32[i15 >> 2] = i14;
      i24 = i12;
      break;
     } else _abort();
    } while (0);
    do if (i5 | 0) {
     i12 = HEAP32[i22 + 28 >> 2] | 0;
     i7 = 5692 + (i12 << 2) | 0;
     if ((i22 | 0) == (HEAP32[i7 >> 2] | 0)) {
      HEAP32[i7 >> 2] = i24;
      if (!i24) {
       HEAP32[1348] = HEAP32[1348] & ~(1 << i12);
       break;
      }
     } else {
      if (i5 >>> 0 < (HEAP32[1351] | 0) >>> 0) _abort();
      i12 = i5 + 16 | 0;
      if ((HEAP32[i12 >> 2] | 0) == (i22 | 0)) HEAP32[i12 >> 2] = i24; else HEAP32[i5 + 20 >> 2] = i24;
      if (!i24) break;
     }
     i12 = HEAP32[1351] | 0;
     if (i24 >>> 0 < i12 >>> 0) _abort();
     HEAP32[i24 + 24 >> 2] = i5;
     i7 = HEAP32[i22 + 16 >> 2] | 0;
     do if (i7 | 0) if (i7 >>> 0 < i12 >>> 0) _abort(); else {
      HEAP32[i24 + 16 >> 2] = i7;
      HEAP32[i7 + 24 >> 2] = i24;
      break;
     } while (0);
     i7 = HEAP32[i22 + 20 >> 2] | 0;
     if (i7 | 0) if (i7 >>> 0 < (HEAP32[1351] | 0) >>> 0) _abort(); else {
      HEAP32[i24 + 20 >> 2] = i7;
      HEAP32[i7 + 24 >> 2] = i24;
      break;
     }
    } while (0);
    if (i21 >>> 0 < 16) {
     i5 = i21 + i2 | 0;
     HEAP32[i22 + 4 >> 2] = i5 | 3;
     i7 = i22 + i5 + 4 | 0;
     HEAP32[i7 >> 2] = HEAP32[i7 >> 2] | 1;
    } else {
     HEAP32[i22 + 4 >> 2] = i2 | 3;
     HEAP32[i3 + 4 >> 2] = i21 | 1;
     HEAP32[i3 + i21 >> 2] = i21;
     i7 = HEAP32[1349] | 0;
     if (i7 | 0) {
      i5 = HEAP32[1352] | 0;
      i12 = i7 >>> 3;
      i7 = 5428 + (i12 << 1 << 2) | 0;
      i14 = HEAP32[1347] | 0;
      i15 = 1 << i12;
      if (i14 & i15) {
       i12 = i7 + 8 | 0;
       i17 = HEAP32[i12 >> 2] | 0;
       if (i17 >>> 0 < (HEAP32[1351] | 0) >>> 0) _abort(); else {
        i29 = i12;
        i30 = i17;
       }
      } else {
       HEAP32[1347] = i14 | i15;
       i29 = i7 + 8 | 0;
       i30 = i7;
      }
      HEAP32[i29 >> 2] = i5;
      HEAP32[i30 + 12 >> 2] = i5;
      HEAP32[i5 + 8 >> 2] = i30;
      HEAP32[i5 + 12 >> 2] = i7;
     }
     HEAP32[1349] = i21;
     HEAP32[1352] = i3;
    }
    i13 = i22 + 8 | 0;
    return i13 | 0;
   } else i31 = i2;
  } else i31 = i2;
 } else if (i1 >>> 0 <= 4294967231) {
  i7 = i1 + 11 | 0;
  i5 = i7 & -8;
  i15 = HEAP32[1348] | 0;
  if (i15) {
   i14 = 0 - i5 | 0;
   i17 = i7 >>> 8;
   if (i17) if (i5 >>> 0 > 16777215) i32 = 31; else {
    i7 = (i17 + 1048320 | 0) >>> 16 & 8;
    i12 = i17 << i7;
    i17 = (i12 + 520192 | 0) >>> 16 & 4;
    i10 = i12 << i17;
    i12 = (i10 + 245760 | 0) >>> 16 & 2;
    i4 = 14 - (i17 | i7 | i12) + (i10 << i12 >>> 15) | 0;
    i32 = i5 >>> (i4 + 7 | 0) & 1 | i4 << 1;
   } else i32 = 0;
   i4 = HEAP32[5692 + (i32 << 2) >> 2] | 0;
   L123 : do if (!i4) {
    i33 = i14;
    i34 = 0;
    i35 = 0;
    i36 = 86;
   } else {
    i12 = i14;
    i10 = 0;
    i7 = i5 << ((i32 | 0) == 31 ? 0 : 25 - (i32 >>> 1) | 0);
    i17 = i4;
    i8 = 0;
    while (1) {
     i16 = HEAP32[i17 + 4 >> 2] & -8;
     i9 = i16 - i5 | 0;
     if (i9 >>> 0 < i12 >>> 0) if ((i16 | 0) == (i5 | 0)) {
      i37 = i9;
      i38 = i17;
      i39 = i17;
      i36 = 90;
      break L123;
     } else {
      i40 = i9;
      i41 = i17;
     } else {
      i40 = i12;
      i41 = i8;
     }
     i9 = HEAP32[i17 + 20 >> 2] | 0;
     i17 = HEAP32[i17 + 16 + (i7 >>> 31 << 2) >> 2] | 0;
     i16 = (i9 | 0) == 0 | (i9 | 0) == (i17 | 0) ? i10 : i9;
     i9 = (i17 | 0) == 0;
     if (i9) {
      i33 = i40;
      i34 = i16;
      i35 = i41;
      i36 = 86;
      break;
     } else {
      i12 = i40;
      i10 = i16;
      i7 = i7 << (i9 & 1 ^ 1);
      i8 = i41;
     }
    }
   } while (0);
   if ((i36 | 0) == 86) {
    if ((i34 | 0) == 0 & (i35 | 0) == 0) {
     i4 = 2 << i32;
     i14 = i15 & (i4 | 0 - i4);
     if (!i14) {
      i31 = i5;
      break;
     }
     i4 = (i14 & 0 - i14) + -1 | 0;
     i14 = i4 >>> 12 & 16;
     i2 = i4 >>> i14;
     i4 = i2 >>> 5 & 8;
     i3 = i2 >>> i4;
     i2 = i3 >>> 2 & 4;
     i8 = i3 >>> i2;
     i3 = i8 >>> 1 & 2;
     i7 = i8 >>> i3;
     i8 = i7 >>> 1 & 1;
     i42 = HEAP32[5692 + ((i4 | i14 | i2 | i3 | i8) + (i7 >>> i8) << 2) >> 2] | 0;
    } else i42 = i34;
    if (!i42) {
     i43 = i33;
     i44 = i35;
    } else {
     i37 = i33;
     i38 = i42;
     i39 = i35;
     i36 = 90;
    }
   }
   if ((i36 | 0) == 90) while (1) {
    i36 = 0;
    i8 = (HEAP32[i38 + 4 >> 2] & -8) - i5 | 0;
    i7 = i8 >>> 0 < i37 >>> 0;
    i3 = i7 ? i8 : i37;
    i8 = i7 ? i38 : i39;
    i7 = HEAP32[i38 + 16 >> 2] | 0;
    if (i7 | 0) {
     i37 = i3;
     i38 = i7;
     i39 = i8;
     i36 = 90;
     continue;
    }
    i38 = HEAP32[i38 + 20 >> 2] | 0;
    if (!i38) {
     i43 = i3;
     i44 = i8;
     break;
    } else {
     i37 = i3;
     i39 = i8;
     i36 = 90;
    }
   }
   if ((i44 | 0) != 0 ? i43 >>> 0 < ((HEAP32[1349] | 0) - i5 | 0) >>> 0 : 0) {
    i15 = HEAP32[1351] | 0;
    if (i44 >>> 0 < i15 >>> 0) _abort();
    i8 = i44 + i5 | 0;
    if (i44 >>> 0 >= i8 >>> 0) _abort();
    i3 = HEAP32[i44 + 24 >> 2] | 0;
    i7 = HEAP32[i44 + 12 >> 2] | 0;
    do if ((i7 | 0) == (i44 | 0)) {
     i2 = i44 + 20 | 0;
     i14 = HEAP32[i2 >> 2] | 0;
     if (!i14) {
      i4 = i44 + 16 | 0;
      i10 = HEAP32[i4 >> 2] | 0;
      if (!i10) {
       i45 = 0;
       break;
      } else {
       i46 = i10;
       i47 = i4;
      }
     } else {
      i46 = i14;
      i47 = i2;
     }
     while (1) {
      i2 = i46 + 20 | 0;
      i14 = HEAP32[i2 >> 2] | 0;
      if (i14 | 0) {
       i46 = i14;
       i47 = i2;
       continue;
      }
      i2 = i46 + 16 | 0;
      i14 = HEAP32[i2 >> 2] | 0;
      if (!i14) {
       i48 = i46;
       i49 = i47;
       break;
      } else {
       i46 = i14;
       i47 = i2;
      }
     }
     if (i49 >>> 0 < i15 >>> 0) _abort(); else {
      HEAP32[i49 >> 2] = 0;
      i45 = i48;
      break;
     }
    } else {
     i2 = HEAP32[i44 + 8 >> 2] | 0;
     if (i2 >>> 0 < i15 >>> 0) _abort();
     i14 = i2 + 12 | 0;
     if ((HEAP32[i14 >> 2] | 0) != (i44 | 0)) _abort();
     i4 = i7 + 8 | 0;
     if ((HEAP32[i4 >> 2] | 0) == (i44 | 0)) {
      HEAP32[i14 >> 2] = i7;
      HEAP32[i4 >> 2] = i2;
      i45 = i7;
      break;
     } else _abort();
    } while (0);
    do if (i3 | 0) {
     i7 = HEAP32[i44 + 28 >> 2] | 0;
     i15 = 5692 + (i7 << 2) | 0;
     if ((i44 | 0) == (HEAP32[i15 >> 2] | 0)) {
      HEAP32[i15 >> 2] = i45;
      if (!i45) {
       HEAP32[1348] = HEAP32[1348] & ~(1 << i7);
       break;
      }
     } else {
      if (i3 >>> 0 < (HEAP32[1351] | 0) >>> 0) _abort();
      i7 = i3 + 16 | 0;
      if ((HEAP32[i7 >> 2] | 0) == (i44 | 0)) HEAP32[i7 >> 2] = i45; else HEAP32[i3 + 20 >> 2] = i45;
      if (!i45) break;
     }
     i7 = HEAP32[1351] | 0;
     if (i45 >>> 0 < i7 >>> 0) _abort();
     HEAP32[i45 + 24 >> 2] = i3;
     i15 = HEAP32[i44 + 16 >> 2] | 0;
     do if (i15 | 0) if (i15 >>> 0 < i7 >>> 0) _abort(); else {
      HEAP32[i45 + 16 >> 2] = i15;
      HEAP32[i15 + 24 >> 2] = i45;
      break;
     } while (0);
     i15 = HEAP32[i44 + 20 >> 2] | 0;
     if (i15 | 0) if (i15 >>> 0 < (HEAP32[1351] | 0) >>> 0) _abort(); else {
      HEAP32[i45 + 20 >> 2] = i15;
      HEAP32[i15 + 24 >> 2] = i45;
      break;
     }
    } while (0);
    do if (i43 >>> 0 >= 16) {
     HEAP32[i44 + 4 >> 2] = i5 | 3;
     HEAP32[i8 + 4 >> 2] = i43 | 1;
     HEAP32[i8 + i43 >> 2] = i43;
     i3 = i43 >>> 3;
     if (i43 >>> 0 < 256) {
      i15 = 5428 + (i3 << 1 << 2) | 0;
      i7 = HEAP32[1347] | 0;
      i2 = 1 << i3;
      if (i7 & i2) {
       i3 = i15 + 8 | 0;
       i4 = HEAP32[i3 >> 2] | 0;
       if (i4 >>> 0 < (HEAP32[1351] | 0) >>> 0) _abort(); else {
        i50 = i3;
        i51 = i4;
       }
      } else {
       HEAP32[1347] = i7 | i2;
       i50 = i15 + 8 | 0;
       i51 = i15;
      }
      HEAP32[i50 >> 2] = i8;
      HEAP32[i51 + 12 >> 2] = i8;
      HEAP32[i8 + 8 >> 2] = i51;
      HEAP32[i8 + 12 >> 2] = i15;
      break;
     }
     i15 = i43 >>> 8;
     if (i15) if (i43 >>> 0 > 16777215) i52 = 31; else {
      i2 = (i15 + 1048320 | 0) >>> 16 & 8;
      i7 = i15 << i2;
      i15 = (i7 + 520192 | 0) >>> 16 & 4;
      i4 = i7 << i15;
      i7 = (i4 + 245760 | 0) >>> 16 & 2;
      i3 = 14 - (i15 | i2 | i7) + (i4 << i7 >>> 15) | 0;
      i52 = i43 >>> (i3 + 7 | 0) & 1 | i3 << 1;
     } else i52 = 0;
     i3 = 5692 + (i52 << 2) | 0;
     HEAP32[i8 + 28 >> 2] = i52;
     i7 = i8 + 16 | 0;
     HEAP32[i7 + 4 >> 2] = 0;
     HEAP32[i7 >> 2] = 0;
     i7 = HEAP32[1348] | 0;
     i4 = 1 << i52;
     if (!(i7 & i4)) {
      HEAP32[1348] = i7 | i4;
      HEAP32[i3 >> 2] = i8;
      HEAP32[i8 + 24 >> 2] = i3;
      HEAP32[i8 + 12 >> 2] = i8;
      HEAP32[i8 + 8 >> 2] = i8;
      break;
     }
     i4 = i43 << ((i52 | 0) == 31 ? 0 : 25 - (i52 >>> 1) | 0);
     i7 = HEAP32[i3 >> 2] | 0;
     while (1) {
      if ((HEAP32[i7 + 4 >> 2] & -8 | 0) == (i43 | 0)) {
       i53 = i7;
       i36 = 148;
       break;
      }
      i3 = i7 + 16 + (i4 >>> 31 << 2) | 0;
      i2 = HEAP32[i3 >> 2] | 0;
      if (!i2) {
       i54 = i3;
       i55 = i7;
       i36 = 145;
       break;
      } else {
       i4 = i4 << 1;
       i7 = i2;
      }
     }
     if ((i36 | 0) == 145) if (i54 >>> 0 < (HEAP32[1351] | 0) >>> 0) _abort(); else {
      HEAP32[i54 >> 2] = i8;
      HEAP32[i8 + 24 >> 2] = i55;
      HEAP32[i8 + 12 >> 2] = i8;
      HEAP32[i8 + 8 >> 2] = i8;
      break;
     } else if ((i36 | 0) == 148) {
      i7 = i53 + 8 | 0;
      i4 = HEAP32[i7 >> 2] | 0;
      i2 = HEAP32[1351] | 0;
      if (i4 >>> 0 >= i2 >>> 0 & i53 >>> 0 >= i2 >>> 0) {
       HEAP32[i4 + 12 >> 2] = i8;
       HEAP32[i7 >> 2] = i8;
       HEAP32[i8 + 8 >> 2] = i4;
       HEAP32[i8 + 12 >> 2] = i53;
       HEAP32[i8 + 24 >> 2] = 0;
       break;
      } else _abort();
     }
    } else {
     i4 = i43 + i5 | 0;
     HEAP32[i44 + 4 >> 2] = i4 | 3;
     i7 = i44 + i4 + 4 | 0;
     HEAP32[i7 >> 2] = HEAP32[i7 >> 2] | 1;
    } while (0);
    i13 = i44 + 8 | 0;
    return i13 | 0;
   } else i31 = i5;
  } else i31 = i5;
 } else i31 = -1; while (0);
 i44 = HEAP32[1349] | 0;
 if (i44 >>> 0 >= i31 >>> 0) {
  i43 = i44 - i31 | 0;
  i53 = HEAP32[1352] | 0;
  if (i43 >>> 0 > 15) {
   i55 = i53 + i31 | 0;
   HEAP32[1352] = i55;
   HEAP32[1349] = i43;
   HEAP32[i55 + 4 >> 2] = i43 | 1;
   HEAP32[i55 + i43 >> 2] = i43;
   HEAP32[i53 + 4 >> 2] = i31 | 3;
  } else {
   HEAP32[1349] = 0;
   HEAP32[1352] = 0;
   HEAP32[i53 + 4 >> 2] = i44 | 3;
   i43 = i53 + i44 + 4 | 0;
   HEAP32[i43 >> 2] = HEAP32[i43 >> 2] | 1;
  }
  i13 = i53 + 8 | 0;
  return i13 | 0;
 }
 i53 = HEAP32[1350] | 0;
 if (i53 >>> 0 > i31 >>> 0) {
  i43 = i53 - i31 | 0;
  HEAP32[1350] = i43;
  i53 = HEAP32[1353] | 0;
  i44 = i53 + i31 | 0;
  HEAP32[1353] = i44;
  HEAP32[i44 + 4 >> 2] = i43 | 1;
  HEAP32[i53 + 4 >> 2] = i31 | 3;
  i13 = i53 + 8 | 0;
  return i13 | 0;
 }
 do if (!(HEAP32[1465] | 0)) {
  i53 = _sysconf(30) | 0;
  if (!(i53 + -1 & i53)) {
   HEAP32[1467] = i53;
   HEAP32[1466] = i53;
   HEAP32[1468] = -1;
   HEAP32[1469] = -1;
   HEAP32[1470] = 0;
   HEAP32[1458] = 0;
   HEAP32[1465] = (_time(0) | 0) & -16 ^ 1431655768;
   break;
  } else _abort();
 } while (0);
 i53 = i31 + 48 | 0;
 i43 = HEAP32[1467] | 0;
 i44 = i31 + 47 | 0;
 i55 = i43 + i44 | 0;
 i54 = 0 - i43 | 0;
 i43 = i55 & i54;
 if (i43 >>> 0 <= i31 >>> 0) {
  i13 = 0;
  return i13 | 0;
 }
 i52 = HEAP32[1457] | 0;
 if (i52 | 0 ? (i51 = HEAP32[1455] | 0, i50 = i51 + i43 | 0, i50 >>> 0 <= i51 >>> 0 | i50 >>> 0 > i52 >>> 0) : 0) {
  i13 = 0;
  return i13 | 0;
 }
 L257 : do if (!(HEAP32[1458] & 4)) {
  i52 = HEAP32[1353] | 0;
  L259 : do if (i52) {
   i50 = 5836;
   while (1) {
    i51 = HEAP32[i50 >> 2] | 0;
    if (i51 >>> 0 <= i52 >>> 0 ? (i45 = i50 + 4 | 0, (i51 + (HEAP32[i45 >> 2] | 0) | 0) >>> 0 > i52 >>> 0) : 0) {
     i56 = i50;
     i57 = i45;
     break;
    }
    i50 = HEAP32[i50 + 8 >> 2] | 0;
    if (!i50) {
     i36 = 173;
     break L259;
    }
   }
   i50 = i55 - (HEAP32[1350] | 0) & i54;
   if (i50 >>> 0 < 2147483647) {
    i45 = _sbrk(i50 | 0) | 0;
    if ((i45 | 0) == ((HEAP32[i56 >> 2] | 0) + (HEAP32[i57 >> 2] | 0) | 0)) {
     if ((i45 | 0) != (-1 | 0)) {
      i58 = i45;
      i59 = i50;
      i36 = 193;
      break L257;
     }
    } else {
     i60 = i45;
     i61 = i50;
     i36 = 183;
    }
   }
  } else i36 = 173; while (0);
  do if ((i36 | 0) == 173 ? (i52 = _sbrk(0) | 0, (i52 | 0) != (-1 | 0)) : 0) {
   i5 = i52;
   i50 = HEAP32[1466] | 0;
   i45 = i50 + -1 | 0;
   if (!(i45 & i5)) i62 = i43; else i62 = i43 - i5 + (i45 + i5 & 0 - i50) | 0;
   i50 = HEAP32[1455] | 0;
   i5 = i50 + i62 | 0;
   if (i62 >>> 0 > i31 >>> 0 & i62 >>> 0 < 2147483647) {
    i45 = HEAP32[1457] | 0;
    if (i45 | 0 ? i5 >>> 0 <= i50 >>> 0 | i5 >>> 0 > i45 >>> 0 : 0) break;
    i45 = _sbrk(i62 | 0) | 0;
    if ((i45 | 0) == (i52 | 0)) {
     i58 = i52;
     i59 = i62;
     i36 = 193;
     break L257;
    } else {
     i60 = i45;
     i61 = i62;
     i36 = 183;
    }
   }
  } while (0);
  L279 : do if ((i36 | 0) == 183) {
   i45 = 0 - i61 | 0;
   do if (i53 >>> 0 > i61 >>> 0 & (i61 >>> 0 < 2147483647 & (i60 | 0) != (-1 | 0)) ? (i52 = HEAP32[1467] | 0, i5 = i44 - i61 + i52 & 0 - i52, i5 >>> 0 < 2147483647) : 0) if ((_sbrk(i5 | 0) | 0) == (-1 | 0)) {
    _sbrk(i45 | 0) | 0;
    break L279;
   } else {
    i63 = i5 + i61 | 0;
    break;
   } else i63 = i61; while (0);
   if ((i60 | 0) != (-1 | 0)) {
    i58 = i60;
    i59 = i63;
    i36 = 193;
    break L257;
   }
  } while (0);
  HEAP32[1458] = HEAP32[1458] | 4;
  i36 = 190;
 } else i36 = 190; while (0);
 if ((((i36 | 0) == 190 ? i43 >>> 0 < 2147483647 : 0) ? (i63 = _sbrk(i43 | 0) | 0, i43 = _sbrk(0) | 0, i63 >>> 0 < i43 >>> 0 & ((i63 | 0) != (-1 | 0) & (i43 | 0) != (-1 | 0))) : 0) ? (i60 = i43 - i63 | 0, i60 >>> 0 > (i31 + 40 | 0) >>> 0) : 0) {
  i58 = i63;
  i59 = i60;
  i36 = 193;
 }
 if ((i36 | 0) == 193) {
  i60 = (HEAP32[1455] | 0) + i59 | 0;
  HEAP32[1455] = i60;
  if (i60 >>> 0 > (HEAP32[1456] | 0) >>> 0) HEAP32[1456] = i60;
  i60 = HEAP32[1353] | 0;
  do if (i60) {
   i63 = 5836;
   do {
    i43 = HEAP32[i63 >> 2] | 0;
    i61 = i63 + 4 | 0;
    i44 = HEAP32[i61 >> 2] | 0;
    if ((i58 | 0) == (i43 + i44 | 0)) {
     i64 = i43;
     i65 = i61;
     i66 = i44;
     i67 = i63;
     i36 = 203;
     break;
    }
    i63 = HEAP32[i63 + 8 >> 2] | 0;
   } while ((i63 | 0) != 0);
   if (((i36 | 0) == 203 ? (HEAP32[i67 + 12 >> 2] & 8 | 0) == 0 : 0) ? i60 >>> 0 < i58 >>> 0 & i60 >>> 0 >= i64 >>> 0 : 0) {
    HEAP32[i65 >> 2] = i66 + i59;
    i63 = i60 + 8 | 0;
    i44 = (i63 & 7 | 0) == 0 ? 0 : 0 - i63 & 7;
    i63 = i60 + i44 | 0;
    i61 = i59 - i44 + (HEAP32[1350] | 0) | 0;
    HEAP32[1353] = i63;
    HEAP32[1350] = i61;
    HEAP32[i63 + 4 >> 2] = i61 | 1;
    HEAP32[i63 + i61 + 4 >> 2] = 40;
    HEAP32[1354] = HEAP32[1469];
    break;
   }
   i61 = HEAP32[1351] | 0;
   if (i58 >>> 0 < i61 >>> 0) {
    HEAP32[1351] = i58;
    i68 = i58;
   } else i68 = i61;
   i61 = i58 + i59 | 0;
   i63 = 5836;
   while (1) {
    if ((HEAP32[i63 >> 2] | 0) == (i61 | 0)) {
     i69 = i63;
     i70 = i63;
     i36 = 211;
     break;
    }
    i63 = HEAP32[i63 + 8 >> 2] | 0;
    if (!i63) {
     i71 = 5836;
     break;
    }
   }
   if ((i36 | 0) == 211) if (!(HEAP32[i70 + 12 >> 2] & 8)) {
    HEAP32[i69 >> 2] = i58;
    i63 = i70 + 4 | 0;
    HEAP32[i63 >> 2] = (HEAP32[i63 >> 2] | 0) + i59;
    i63 = i58 + 8 | 0;
    i44 = i58 + ((i63 & 7 | 0) == 0 ? 0 : 0 - i63 & 7) | 0;
    i63 = i61 + 8 | 0;
    i43 = i61 + ((i63 & 7 | 0) == 0 ? 0 : 0 - i63 & 7) | 0;
    i63 = i44 + i31 | 0;
    i53 = i43 - i44 - i31 | 0;
    HEAP32[i44 + 4 >> 2] = i31 | 3;
    do if ((i43 | 0) != (i60 | 0)) {
     if ((i43 | 0) == (HEAP32[1352] | 0)) {
      i62 = (HEAP32[1349] | 0) + i53 | 0;
      HEAP32[1349] = i62;
      HEAP32[1352] = i63;
      HEAP32[i63 + 4 >> 2] = i62 | 1;
      HEAP32[i63 + i62 >> 2] = i62;
      break;
     }
     i62 = HEAP32[i43 + 4 >> 2] | 0;
     if ((i62 & 3 | 0) == 1) {
      i57 = i62 & -8;
      i56 = i62 >>> 3;
      L331 : do if (i62 >>> 0 >= 256) {
       i54 = HEAP32[i43 + 24 >> 2] | 0;
       i55 = HEAP32[i43 + 12 >> 2] | 0;
       do if ((i55 | 0) == (i43 | 0)) {
        i45 = i43 + 16 | 0;
        i5 = i45 + 4 | 0;
        i52 = HEAP32[i5 >> 2] | 0;
        if (!i52) {
         i50 = HEAP32[i45 >> 2] | 0;
         if (!i50) {
          i72 = 0;
          break;
         } else {
          i73 = i50;
          i74 = i45;
         }
        } else {
         i73 = i52;
         i74 = i5;
        }
        while (1) {
         i5 = i73 + 20 | 0;
         i52 = HEAP32[i5 >> 2] | 0;
         if (i52 | 0) {
          i73 = i52;
          i74 = i5;
          continue;
         }
         i5 = i73 + 16 | 0;
         i52 = HEAP32[i5 >> 2] | 0;
         if (!i52) {
          i75 = i73;
          i76 = i74;
          break;
         } else {
          i73 = i52;
          i74 = i5;
         }
        }
        if (i76 >>> 0 < i68 >>> 0) _abort(); else {
         HEAP32[i76 >> 2] = 0;
         i72 = i75;
         break;
        }
       } else {
        i5 = HEAP32[i43 + 8 >> 2] | 0;
        if (i5 >>> 0 < i68 >>> 0) _abort();
        i52 = i5 + 12 | 0;
        if ((HEAP32[i52 >> 2] | 0) != (i43 | 0)) _abort();
        i45 = i55 + 8 | 0;
        if ((HEAP32[i45 >> 2] | 0) == (i43 | 0)) {
         HEAP32[i52 >> 2] = i55;
         HEAP32[i45 >> 2] = i5;
         i72 = i55;
         break;
        } else _abort();
       } while (0);
       if (!i54) break;
       i55 = HEAP32[i43 + 28 >> 2] | 0;
       i5 = 5692 + (i55 << 2) | 0;
       do if ((i43 | 0) != (HEAP32[i5 >> 2] | 0)) {
        if (i54 >>> 0 < (HEAP32[1351] | 0) >>> 0) _abort();
        i45 = i54 + 16 | 0;
        if ((HEAP32[i45 >> 2] | 0) == (i43 | 0)) HEAP32[i45 >> 2] = i72; else HEAP32[i54 + 20 >> 2] = i72;
        if (!i72) break L331;
       } else {
        HEAP32[i5 >> 2] = i72;
        if (i72 | 0) break;
        HEAP32[1348] = HEAP32[1348] & ~(1 << i55);
        break L331;
       } while (0);
       i55 = HEAP32[1351] | 0;
       if (i72 >>> 0 < i55 >>> 0) _abort();
       HEAP32[i72 + 24 >> 2] = i54;
       i5 = i43 + 16 | 0;
       i45 = HEAP32[i5 >> 2] | 0;
       do if (i45 | 0) if (i45 >>> 0 < i55 >>> 0) _abort(); else {
        HEAP32[i72 + 16 >> 2] = i45;
        HEAP32[i45 + 24 >> 2] = i72;
        break;
       } while (0);
       i45 = HEAP32[i5 + 4 >> 2] | 0;
       if (!i45) break;
       if (i45 >>> 0 < (HEAP32[1351] | 0) >>> 0) _abort(); else {
        HEAP32[i72 + 20 >> 2] = i45;
        HEAP32[i45 + 24 >> 2] = i72;
        break;
       }
      } else {
       i45 = HEAP32[i43 + 8 >> 2] | 0;
       i55 = HEAP32[i43 + 12 >> 2] | 0;
       i54 = 5428 + (i56 << 1 << 2) | 0;
       do if ((i45 | 0) != (i54 | 0)) {
        if (i45 >>> 0 < i68 >>> 0) _abort();
        if ((HEAP32[i45 + 12 >> 2] | 0) == (i43 | 0)) break;
        _abort();
       } while (0);
       if ((i55 | 0) == (i45 | 0)) {
        HEAP32[1347] = HEAP32[1347] & ~(1 << i56);
        break;
       }
       do if ((i55 | 0) == (i54 | 0)) i77 = i55 + 8 | 0; else {
        if (i55 >>> 0 < i68 >>> 0) _abort();
        i5 = i55 + 8 | 0;
        if ((HEAP32[i5 >> 2] | 0) == (i43 | 0)) {
         i77 = i5;
         break;
        }
        _abort();
       } while (0);
       HEAP32[i45 + 12 >> 2] = i55;
       HEAP32[i77 >> 2] = i45;
      } while (0);
      i78 = i43 + i57 | 0;
      i79 = i57 + i53 | 0;
     } else {
      i78 = i43;
      i79 = i53;
     }
     i56 = i78 + 4 | 0;
     HEAP32[i56 >> 2] = HEAP32[i56 >> 2] & -2;
     HEAP32[i63 + 4 >> 2] = i79 | 1;
     HEAP32[i63 + i79 >> 2] = i79;
     i56 = i79 >>> 3;
     if (i79 >>> 0 < 256) {
      i62 = 5428 + (i56 << 1 << 2) | 0;
      i54 = HEAP32[1347] | 0;
      i5 = 1 << i56;
      do if (!(i54 & i5)) {
       HEAP32[1347] = i54 | i5;
       i80 = i62 + 8 | 0;
       i81 = i62;
      } else {
       i56 = i62 + 8 | 0;
       i52 = HEAP32[i56 >> 2] | 0;
       if (i52 >>> 0 >= (HEAP32[1351] | 0) >>> 0) {
        i80 = i56;
        i81 = i52;
        break;
       }
       _abort();
      } while (0);
      HEAP32[i80 >> 2] = i63;
      HEAP32[i81 + 12 >> 2] = i63;
      HEAP32[i63 + 8 >> 2] = i81;
      HEAP32[i63 + 12 >> 2] = i62;
      break;
     }
     i5 = i79 >>> 8;
     do if (!i5) i82 = 0; else {
      if (i79 >>> 0 > 16777215) {
       i82 = 31;
       break;
      }
      i54 = (i5 + 1048320 | 0) >>> 16 & 8;
      i57 = i5 << i54;
      i52 = (i57 + 520192 | 0) >>> 16 & 4;
      i56 = i57 << i52;
      i57 = (i56 + 245760 | 0) >>> 16 & 2;
      i50 = 14 - (i52 | i54 | i57) + (i56 << i57 >>> 15) | 0;
      i82 = i79 >>> (i50 + 7 | 0) & 1 | i50 << 1;
     } while (0);
     i5 = 5692 + (i82 << 2) | 0;
     HEAP32[i63 + 28 >> 2] = i82;
     i62 = i63 + 16 | 0;
     HEAP32[i62 + 4 >> 2] = 0;
     HEAP32[i62 >> 2] = 0;
     i62 = HEAP32[1348] | 0;
     i50 = 1 << i82;
     if (!(i62 & i50)) {
      HEAP32[1348] = i62 | i50;
      HEAP32[i5 >> 2] = i63;
      HEAP32[i63 + 24 >> 2] = i5;
      HEAP32[i63 + 12 >> 2] = i63;
      HEAP32[i63 + 8 >> 2] = i63;
      break;
     }
     i50 = i79 << ((i82 | 0) == 31 ? 0 : 25 - (i82 >>> 1) | 0);
     i62 = HEAP32[i5 >> 2] | 0;
     while (1) {
      if ((HEAP32[i62 + 4 >> 2] & -8 | 0) == (i79 | 0)) {
       i83 = i62;
       i36 = 281;
       break;
      }
      i5 = i62 + 16 + (i50 >>> 31 << 2) | 0;
      i57 = HEAP32[i5 >> 2] | 0;
      if (!i57) {
       i84 = i5;
       i85 = i62;
       i36 = 278;
       break;
      } else {
       i50 = i50 << 1;
       i62 = i57;
      }
     }
     if ((i36 | 0) == 278) if (i84 >>> 0 < (HEAP32[1351] | 0) >>> 0) _abort(); else {
      HEAP32[i84 >> 2] = i63;
      HEAP32[i63 + 24 >> 2] = i85;
      HEAP32[i63 + 12 >> 2] = i63;
      HEAP32[i63 + 8 >> 2] = i63;
      break;
     } else if ((i36 | 0) == 281) {
      i62 = i83 + 8 | 0;
      i50 = HEAP32[i62 >> 2] | 0;
      i57 = HEAP32[1351] | 0;
      if (i50 >>> 0 >= i57 >>> 0 & i83 >>> 0 >= i57 >>> 0) {
       HEAP32[i50 + 12 >> 2] = i63;
       HEAP32[i62 >> 2] = i63;
       HEAP32[i63 + 8 >> 2] = i50;
       HEAP32[i63 + 12 >> 2] = i83;
       HEAP32[i63 + 24 >> 2] = 0;
       break;
      } else _abort();
     }
    } else {
     i50 = (HEAP32[1350] | 0) + i53 | 0;
     HEAP32[1350] = i50;
     HEAP32[1353] = i63;
     HEAP32[i63 + 4 >> 2] = i50 | 1;
    } while (0);
    i13 = i44 + 8 | 0;
    return i13 | 0;
   } else i71 = 5836;
   while (1) {
    i63 = HEAP32[i71 >> 2] | 0;
    if (i63 >>> 0 <= i60 >>> 0 ? (i53 = i63 + (HEAP32[i71 + 4 >> 2] | 0) | 0, i53 >>> 0 > i60 >>> 0) : 0) {
     i86 = i53;
     break;
    }
    i71 = HEAP32[i71 + 8 >> 2] | 0;
   }
   i44 = i86 + -47 | 0;
   i53 = i44 + 8 | 0;
   i63 = i44 + ((i53 & 7 | 0) == 0 ? 0 : 0 - i53 & 7) | 0;
   i53 = i60 + 16 | 0;
   i44 = i63 >>> 0 < i53 >>> 0 ? i60 : i63;
   i63 = i44 + 8 | 0;
   i43 = i58 + 8 | 0;
   i61 = (i43 & 7 | 0) == 0 ? 0 : 0 - i43 & 7;
   i43 = i58 + i61 | 0;
   i50 = i59 + -40 - i61 | 0;
   HEAP32[1353] = i43;
   HEAP32[1350] = i50;
   HEAP32[i43 + 4 >> 2] = i50 | 1;
   HEAP32[i43 + i50 + 4 >> 2] = 40;
   HEAP32[1354] = HEAP32[1469];
   i50 = i44 + 4 | 0;
   HEAP32[i50 >> 2] = 27;
   HEAP32[i63 >> 2] = HEAP32[1459];
   HEAP32[i63 + 4 >> 2] = HEAP32[1460];
   HEAP32[i63 + 8 >> 2] = HEAP32[1461];
   HEAP32[i63 + 12 >> 2] = HEAP32[1462];
   HEAP32[1459] = i58;
   HEAP32[1460] = i59;
   HEAP32[1462] = 0;
   HEAP32[1461] = i63;
   i63 = i44 + 24 | 0;
   do {
    i63 = i63 + 4 | 0;
    HEAP32[i63 >> 2] = 7;
   } while ((i63 + 4 | 0) >>> 0 < i86 >>> 0);
   if ((i44 | 0) != (i60 | 0)) {
    i63 = i44 - i60 | 0;
    HEAP32[i50 >> 2] = HEAP32[i50 >> 2] & -2;
    HEAP32[i60 + 4 >> 2] = i63 | 1;
    HEAP32[i44 >> 2] = i63;
    i43 = i63 >>> 3;
    if (i63 >>> 0 < 256) {
     i61 = 5428 + (i43 << 1 << 2) | 0;
     i62 = HEAP32[1347] | 0;
     i57 = 1 << i43;
     if (i62 & i57) {
      i43 = i61 + 8 | 0;
      i5 = HEAP32[i43 >> 2] | 0;
      if (i5 >>> 0 < (HEAP32[1351] | 0) >>> 0) _abort(); else {
       i87 = i43;
       i88 = i5;
      }
     } else {
      HEAP32[1347] = i62 | i57;
      i87 = i61 + 8 | 0;
      i88 = i61;
     }
     HEAP32[i87 >> 2] = i60;
     HEAP32[i88 + 12 >> 2] = i60;
     HEAP32[i60 + 8 >> 2] = i88;
     HEAP32[i60 + 12 >> 2] = i61;
     break;
    }
    i61 = i63 >>> 8;
    if (i61) if (i63 >>> 0 > 16777215) i89 = 31; else {
     i57 = (i61 + 1048320 | 0) >>> 16 & 8;
     i62 = i61 << i57;
     i61 = (i62 + 520192 | 0) >>> 16 & 4;
     i5 = i62 << i61;
     i62 = (i5 + 245760 | 0) >>> 16 & 2;
     i43 = 14 - (i61 | i57 | i62) + (i5 << i62 >>> 15) | 0;
     i89 = i63 >>> (i43 + 7 | 0) & 1 | i43 << 1;
    } else i89 = 0;
    i43 = 5692 + (i89 << 2) | 0;
    HEAP32[i60 + 28 >> 2] = i89;
    HEAP32[i60 + 20 >> 2] = 0;
    HEAP32[i53 >> 2] = 0;
    i62 = HEAP32[1348] | 0;
    i5 = 1 << i89;
    if (!(i62 & i5)) {
     HEAP32[1348] = i62 | i5;
     HEAP32[i43 >> 2] = i60;
     HEAP32[i60 + 24 >> 2] = i43;
     HEAP32[i60 + 12 >> 2] = i60;
     HEAP32[i60 + 8 >> 2] = i60;
     break;
    }
    i5 = i63 << ((i89 | 0) == 31 ? 0 : 25 - (i89 >>> 1) | 0);
    i62 = HEAP32[i43 >> 2] | 0;
    while (1) {
     if ((HEAP32[i62 + 4 >> 2] & -8 | 0) == (i63 | 0)) {
      i90 = i62;
      i36 = 307;
      break;
     }
     i43 = i62 + 16 + (i5 >>> 31 << 2) | 0;
     i57 = HEAP32[i43 >> 2] | 0;
     if (!i57) {
      i91 = i43;
      i92 = i62;
      i36 = 304;
      break;
     } else {
      i5 = i5 << 1;
      i62 = i57;
     }
    }
    if ((i36 | 0) == 304) if (i91 >>> 0 < (HEAP32[1351] | 0) >>> 0) _abort(); else {
     HEAP32[i91 >> 2] = i60;
     HEAP32[i60 + 24 >> 2] = i92;
     HEAP32[i60 + 12 >> 2] = i60;
     HEAP32[i60 + 8 >> 2] = i60;
     break;
    } else if ((i36 | 0) == 307) {
     i62 = i90 + 8 | 0;
     i5 = HEAP32[i62 >> 2] | 0;
     i63 = HEAP32[1351] | 0;
     if (i5 >>> 0 >= i63 >>> 0 & i90 >>> 0 >= i63 >>> 0) {
      HEAP32[i5 + 12 >> 2] = i60;
      HEAP32[i62 >> 2] = i60;
      HEAP32[i60 + 8 >> 2] = i5;
      HEAP32[i60 + 12 >> 2] = i90;
      HEAP32[i60 + 24 >> 2] = 0;
      break;
     } else _abort();
    }
   }
  } else {
   i5 = HEAP32[1351] | 0;
   if ((i5 | 0) == 0 | i58 >>> 0 < i5 >>> 0) HEAP32[1351] = i58;
   HEAP32[1459] = i58;
   HEAP32[1460] = i59;
   HEAP32[1462] = 0;
   HEAP32[1356] = HEAP32[1465];
   HEAP32[1355] = -1;
   i5 = 0;
   do {
    i62 = 5428 + (i5 << 1 << 2) | 0;
    HEAP32[i62 + 12 >> 2] = i62;
    HEAP32[i62 + 8 >> 2] = i62;
    i5 = i5 + 1 | 0;
   } while ((i5 | 0) != 32);
   i5 = i58 + 8 | 0;
   i62 = (i5 & 7 | 0) == 0 ? 0 : 0 - i5 & 7;
   i5 = i58 + i62 | 0;
   i63 = i59 + -40 - i62 | 0;
   HEAP32[1353] = i5;
   HEAP32[1350] = i63;
   HEAP32[i5 + 4 >> 2] = i63 | 1;
   HEAP32[i5 + i63 + 4 >> 2] = 40;
   HEAP32[1354] = HEAP32[1469];
  } while (0);
  i59 = HEAP32[1350] | 0;
  if (i59 >>> 0 > i31 >>> 0) {
   i58 = i59 - i31 | 0;
   HEAP32[1350] = i58;
   i59 = HEAP32[1353] | 0;
   i60 = i59 + i31 | 0;
   HEAP32[1353] = i60;
   HEAP32[i60 + 4 >> 2] = i58 | 1;
   HEAP32[i59 + 4 >> 2] = i31 | 3;
   i13 = i59 + 8 | 0;
   return i13 | 0;
  }
 }
 HEAP32[(___errno_location() | 0) >> 2] = 12;
 i13 = 0;
 return i13 | 0;
}

function _oggz_read_sync(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0, i17 = 0, i18 = 0, i19 = 0, i20 = 0, i21 = 0, i22 = 0, i23 = 0, i24 = 0, i25 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 160 | 0;
 i3 = i2 + 148 | 0;
 i4 = i2 + 144 | 0;
 i5 = i2 + 140 | 0;
 i6 = i2 + 136 | 0;
 i7 = i2 + 132 | 0;
 i8 = i2 + 128 | 0;
 i9 = i2 + 124 | 0;
 i10 = i2 + 120 | 0;
 i11 = i2 + 24 | 0;
 i12 = i2 + 104 | 0;
 i13 = i2 + 96 | 0;
 i14 = i2 + 16 | 0;
 i15 = i2 + 92 | 0;
 i16 = i2 + 88 | 0;
 i17 = i2 + 84 | 0;
 i18 = i2 + 8 | 0;
 i19 = i2 + 80 | 0;
 i20 = i2;
 HEAP32[i4 >> 2] = i1;
 HEAP32[i5 >> 2] = (HEAP32[i4 >> 2] | 0) + 112;
 HEAP32[i13 >> 2] = 0;
 HEAP32[i8 >> 2] = i11;
 HEAP32[i9 >> 2] = i11 + 32;
 L1 : while (1) {
  if (HEAP32[i13 >> 2] | 0) {
   i21 = 75;
   break;
  }
  L4 : do if ((HEAP32[i13 >> 2] | 0) == 0 ? (HEAP32[(HEAP32[i5 >> 2] | 0) + 392 >> 2] | 0) != -1 : 0) do {
   HEAP32[i10 >> 2] = HEAP32[(HEAP32[i5 >> 2] | 0) + 392 >> 2];
   HEAP32[i6 >> 2] = _oggz_get_stream(HEAP32[i4 >> 2] | 0, HEAP32[i10 >> 2] | 0) | 0;
   if ((HEAP32[i6 >> 2] | 0) == 0 ? (i1 = _oggz_add_stream(HEAP32[i4 >> 2] | 0, HEAP32[i10 >> 2] | 0) | 0, HEAP32[i6 >> 2] = i1, (i1 | 0) == 0) : 0) {
    i21 = 7;
    break L1;
   }
   HEAP32[i7 >> 2] = HEAP32[i6 >> 2];
   HEAP32[i15 >> 2] = _ogg_stream_packetout(HEAP32[i7 >> 2] | 0, HEAP32[i8 >> 2] | 0) | 0;
   if ((HEAP32[i15 >> 2] | 0) == -1) {
    i1 = (HEAP32[i6 >> 2] | 0) + 440 | 0;
    i22 = HEAP32[i1 + 4 >> 2] | 0;
    i23 = (HEAP32[(HEAP32[i6 >> 2] | 0) + 364 >> 2] | 0) - 1 | 0;
    i24 = ((i23 | 0) < 0) << 31 >> 31;
    if ((i22 | 0) < (i24 | 0) | ((i22 | 0) == (i24 | 0) ? (HEAP32[i1 >> 2] | 0) >>> 0 < i23 >>> 0 : 0)) {
     i21 = 10;
     break L1;
    }
    HEAP32[i15 >> 2] = _ogg_stream_packetout(HEAP32[i7 >> 2] | 0, HEAP32[i8 >> 2] | 0) | 0;
    if ((HEAP32[i15 >> 2] | 0) == -1) {
     i21 = 12;
     break L1;
    }
    HEAP32[(HEAP32[i5 >> 2] | 0) + 440 >> 2] = 1;
    HEAP32[(HEAP32[i5 >> 2] | 0) + 436 >> 2] = HEAP32[(HEAP32[i4 >> 2] | 0) + 64 >> 2];
    HEAP32[(HEAP32[i5 >> 2] | 0) + 444 >> 2] = 1;
   }
   if ((HEAP32[i15 >> 2] | 0) <= 0) break L4;
   i23 = (HEAP32[i6 >> 2] | 0) + 440 | 0;
   i1 = i23;
   i24 = _i64Add(HEAP32[i1 >> 2] | 0, HEAP32[i1 + 4 >> 2] | 0, 1, 0) | 0;
   i1 = i23;
   HEAP32[i1 >> 2] = i24;
   HEAP32[i1 + 4 >> 2] = tempRet0;
   i1 = (HEAP32[i8 >> 2] | 0) + 16 | 0;
   i24 = HEAP32[i1 + 4 >> 2] | 0;
   i23 = i14;
   HEAP32[i23 >> 2] = HEAP32[i1 >> 2];
   HEAP32[i23 + 4 >> 2] = i24;
   HEAP32[i16 >> 2] = _oggz_stream_get_content(HEAP32[i4 >> 2] | 0, HEAP32[i10 >> 2] | 0) | 0;
   do if (!((HEAP32[i16 >> 2] | 0) < 0 | (HEAP32[i16 >> 2] | 0) >= 15)) {
    if (((HEAP32[i16 >> 2] | 0) == 6 ? 1 : (HEAP32[(HEAP32[i6 >> 2] | 0) + 448 >> 2] | 0) == 0) ? HEAP32[HEAP32[i4 >> 2] >> 2] & 32 | 0 : 0) _oggz_auto_read_bos_packet(HEAP32[i4 >> 2] | 0, HEAP32[i8 >> 2] | 0, HEAP32[i10 >> 2] | 0, 0) | 0;
    if (!(HEAP32[HEAP32[i4 >> 2] >> 2] & 32)) {
     i24 = i14;
     i23 = HEAP32[i24 + 4 >> 2] | 0;
     i1 = (HEAP32[i5 >> 2] | 0) + 424 | 0;
     HEAP32[i1 >> 2] = HEAP32[i24 >> 2];
     HEAP32[i1 + 4 >> 2] = i23;
     break;
    }
    i23 = i14;
    i1 = _oggz_auto_calculate_granulepos(HEAP32[i16 >> 2] | 0, HEAP32[i23 >> 2] | 0, HEAP32[i23 + 4 >> 2] | 0, HEAP32[i6 >> 2] | 0, HEAP32[i8 >> 2] | 0) | 0;
    i23 = (HEAP32[i5 >> 2] | 0) + 424 | 0;
    HEAP32[i23 >> 2] = i1;
    HEAP32[i23 + 4 >> 2] = tempRet0;
    i23 = i14;
    if (((HEAP32[i23 >> 2] | 0) != -1 ? 1 : (HEAP32[i23 + 4 >> 2] | 0) != -1) ? (i23 = (HEAP32[i5 >> 2] | 0) + 424 | 0, i1 = HEAP32[i23 + 4 >> 2] | 0, i24 = i14, i22 = HEAP32[i24 + 4 >> 2] | 0, (i1 | 0) < (i22 | 0) | ((i1 | 0) == (i22 | 0) ? (HEAP32[i23 >> 2] | 0) >>> 0 < (HEAP32[i24 >> 2] | 0) >>> 0 : 0)) : 0) {
     i24 = i14;
     i23 = HEAP32[i24 + 4 >> 2] | 0;
     i22 = (HEAP32[i5 >> 2] | 0) + 424 | 0;
     HEAP32[i22 >> 2] = HEAP32[i24 >> 2];
     HEAP32[i22 + 4 >> 2] = i23;
    }
   } else {
    i23 = i14;
    i22 = HEAP32[i23 + 4 >> 2] | 0;
    i24 = (HEAP32[i5 >> 2] | 0) + 424 | 0;
    HEAP32[i24 >> 2] = HEAP32[i23 >> 2];
    HEAP32[i24 + 4 >> 2] = i22;
   } while (0);
   i22 = (HEAP32[i5 >> 2] | 0) + 424 | 0;
   i24 = HEAP32[i22 + 4 >> 2] | 0;
   i23 = (HEAP32[i6 >> 2] | 0) + 488 | 0;
   HEAP32[i23 >> 2] = HEAP32[i22 >> 2];
   HEAP32[i23 + 4 >> 2] = i24;
   if (!(!(HEAP32[(HEAP32[i4 >> 2] | 0) + 88 >> 2] | 0) ? !(HEAP32[(HEAP32[i6 >> 2] | 0) + 448 >> 2] | 0) : 0)) i21 = 27;
   if ((i21 | 0) == 27 ? (i21 = 0, i24 = (HEAP32[i5 >> 2] | 0) + 424 | 0, (HEAP32[i24 >> 2] | 0) != -1 ? 1 : (HEAP32[i24 + 4 >> 2] | 0) != -1) : 0) {
    i24 = (HEAP32[i5 >> 2] | 0) + 424 | 0;
    i23 = _oggz_get_unit(HEAP32[i4 >> 2] | 0, HEAP32[i10 >> 2] | 0, HEAP32[i24 >> 2] | 0, HEAP32[i24 + 4 >> 2] | 0) | 0;
    i24 = (HEAP32[i5 >> 2] | 0) + 416 | 0;
    HEAP32[i24 >> 2] = i23;
    HEAP32[i24 + 4 >> 2] = tempRet0;
   }
   i24 = (HEAP32[i6 >> 2] | 0) + 440 | 0;
   if ((HEAP32[i24 >> 2] | 0) == 1 & (HEAP32[i24 + 4 >> 2] | 0) == 0) _oggz_auto_read_comments(HEAP32[i4 >> 2] | 0, HEAP32[i6 >> 2] | 0, HEAP32[i10 >> 2] | 0, HEAP32[i8 >> 2] | 0) | 0;
   i24 = (HEAP32[i5 >> 2] | 0) + 424 | 0;
   i23 = HEAP32[i24 + 4 >> 2] | 0;
   i22 = HEAP32[i9 >> 2] | 0;
   HEAP32[i22 >> 2] = HEAP32[i24 >> 2];
   HEAP32[i22 + 4 >> 2] = i23;
   HEAP32[(HEAP32[i9 >> 2] | 0) + 8 >> 2] = HEAP32[(HEAP32[i5 >> 2] | 0) + 436 >> 2];
   HEAP32[(HEAP32[i9 >> 2] | 0) + 12 >> 2] = HEAP32[(HEAP32[i4 >> 2] | 0) + 64 >> 2];
   HEAP32[(HEAP32[i9 >> 2] | 0) + 16 >> 2] = HEAP32[(HEAP32[i5 >> 2] | 0) + 440 >> 2];
   HEAP32[(HEAP32[i9 >> 2] | 0) + 20 >> 2] = HEAP32[(HEAP32[i5 >> 2] | 0) + 444 >> 2];
   do if (HEAP32[HEAP32[i4 >> 2] >> 2] & 32 | 0) {
    i23 = (HEAP32[i5 >> 2] | 0) + 424 | 0;
    i22 = HEAP32[i4 >> 2] | 0;
    if ((HEAP32[i23 >> 2] | 0) == -1 ? (HEAP32[i23 + 4 >> 2] | 0) == -1 : 0) {
     HEAP32[i17 >> 2] = _oggz_read_new_pbuffer_entry(i22, i11, HEAP32[i10 >> 2] | 0, HEAP32[i6 >> 2] | 0, HEAP32[i5 >> 2] | 0) | 0;
     _oggz_dlist_append(HEAP32[(HEAP32[i4 >> 2] | 0) + 560 >> 2] | 0, HEAP32[i17 >> 2] | 0) | 0;
     i21 = 45;
     break;
    }
    if (!(_oggz_dlist_is_empty(HEAP32[i22 + 560 >> 2] | 0) | 0)) {
     i22 = (HEAP32[i6 >> 2] | 0) + 488 | 0;
     i23 = HEAP32[i22 + 4 >> 2] | 0;
     i24 = i18;
     HEAP32[i24 >> 2] = HEAP32[i22 >> 2];
     HEAP32[i24 + 4 >> 2] = i23;
     HEAP32[(HEAP32[i6 >> 2] | 0) + 508 >> 2] = HEAP32[i8 >> 2];
     _oggz_dlist_reverse_iter(HEAP32[(HEAP32[i4 >> 2] | 0) + 560 >> 2] | 0, 4);
     HEAP32[(HEAP32[i4 >> 2] | 0) + 76 >> 2] = 0;
     if ((_oggz_dlist_deliter(HEAP32[(HEAP32[i4 >> 2] | 0) + 560 >> 2] | 0, 5) | 0) == -1) {
      i21 = 36;
      break L1;
     }
     i23 = (HEAP32[i4 >> 2] | 0) + 76 | 0;
     if ((HEAP32[(HEAP32[i4 >> 2] | 0) + 76 >> 2] | 0) > 0) {
      HEAP32[i13 >> 2] = HEAP32[i23 >> 2];
      HEAP32[(HEAP32[i4 >> 2] | 0) + 76 >> 2] = 0;
      break;
     }
     HEAP32[i23 >> 2] = 0;
     i23 = i18;
     i24 = HEAP32[i23 + 4 >> 2] | 0;
     i22 = (HEAP32[i6 >> 2] | 0) + 488 | 0;
     HEAP32[i22 >> 2] = HEAP32[i23 >> 2];
     HEAP32[i22 + 4 >> 2] = i24;
     if (!(_oggz_dlist_is_empty(HEAP32[(HEAP32[i4 >> 2] | 0) + 560 >> 2] | 0) | 0)) {
      HEAP32[i19 >> 2] = _oggz_read_new_pbuffer_entry(HEAP32[i4 >> 2] | 0, i11, HEAP32[i10 >> 2] | 0, HEAP32[i6 >> 2] | 0, HEAP32[i5 >> 2] | 0) | 0;
      _oggz_dlist_append(HEAP32[(HEAP32[i4 >> 2] | 0) + 560 >> 2] | 0, HEAP32[i19 >> 2] | 0) | 0;
      i21 = 45;
     } else i21 = 41;
    } else i21 = 41;
   } else i21 = 41; while (0);
   do if ((i21 | 0) == 41) {
    i21 = 0;
    if (HEAP32[(HEAP32[i6 >> 2] | 0) + 468 >> 2] | 0) {
     HEAP32[i13 >> 2] = FUNCTION_TABLE_iiiii[HEAP32[(HEAP32[i6 >> 2] | 0) + 468 >> 2] & 15](HEAP32[i4 >> 2] | 0, i11, HEAP32[i10 >> 2] | 0, HEAP32[(HEAP32[i6 >> 2] | 0) + 472 >> 2] | 0) | 0;
     i21 = 45;
     break;
    }
    if (HEAP32[(HEAP32[i5 >> 2] | 0) + 396 >> 2] | 0) {
     HEAP32[i13 >> 2] = FUNCTION_TABLE_iiiii[HEAP32[(HEAP32[i5 >> 2] | 0) + 396 >> 2] & 15](HEAP32[i4 >> 2] | 0, i11, HEAP32[i10 >> 2] | 0, HEAP32[(HEAP32[i5 >> 2] | 0) + 400 >> 2] | 0) | 0;
     i21 = 45;
    } else i21 = 45;
   } while (0);
   if ((i21 | 0) == 45) {
    i21 = 0;
    if ((HEAP32[(HEAP32[i5 >> 2] | 0) + 436 >> 2] | 0) == (HEAP32[(HEAP32[i4 >> 2] | 0) + 64 >> 2] | 0)) {
     i24 = (HEAP32[i5 >> 2] | 0) + 444 | 0;
     HEAP32[i24 >> 2] = (HEAP32[i24 >> 2] | 0) + 1;
    } else {
     HEAP32[(HEAP32[i5 >> 2] | 0) + 436 >> 2] = HEAP32[(HEAP32[i4 >> 2] | 0) + 64 >> 2];
     HEAP32[(HEAP32[i5 >> 2] | 0) + 444 >> 2] = 1;
    }
    HEAP32[(HEAP32[i5 >> 2] | 0) + 440 >> 2] = 1;
    if (!(HEAP32[(HEAP32[i8 >> 2] | 0) + 8 >> 2] | 0)) HEAP32[(HEAP32[i6 >> 2] | 0) + 420 >> 2] = 1;
   }
  } while (!(HEAP32[i13 >> 2] | 0)); while (0);
  if ((HEAP32[i13 >> 2] | 0) == 1 | (HEAP32[i13 >> 2] | 0) == -1 | (HEAP32[i13 >> 2] | 0) == -17) {
   i21 = 51;
   break;
  }
  if ((_oggz_read_get_next_page(HEAP32[i4 >> 2] | 0, i12) | 0) < 0) {
   i21 = 53;
   break;
  }
  HEAP32[i10 >> 2] = _ogg_page_serialno(i12) | 0;
  HEAP32[(HEAP32[i5 >> 2] | 0) + 392 >> 2] = HEAP32[i10 >> 2];
  HEAP32[i6 >> 2] = _oggz_get_stream(HEAP32[i4 >> 2] | 0, HEAP32[i10 >> 2] | 0) | 0;
  i24 = HEAP32[i4 >> 2] | 0;
  i22 = HEAP32[i10 >> 2] | 0;
  if (!(HEAP32[i6 >> 2] | 0)) {
   i23 = _oggz_add_stream(i24, i22) | 0;
   HEAP32[i6 >> 2] = i23;
   if (!i23) {
    i21 = 56;
    break;
   }
   _oggz_auto_identify_page(HEAP32[i4 >> 2] | 0, i12, HEAP32[i10 >> 2] | 0) | 0;
   if (HEAP32[HEAP32[i4 >> 2] >> 2] & 32 | 0) _oggz_auto_read_bos_page(HEAP32[i4 >> 2] | 0, i12, HEAP32[i10 >> 2] | 0, 0) | 0;
  } else if ((_oggz_stream_get_content(i24, i22) | 0) == 9) _oggz_auto_identify_page(HEAP32[i4 >> 2] | 0, i12, HEAP32[i10 >> 2] | 0) | 0;
  HEAP32[i7 >> 2] = HEAP32[i6 >> 2];
  i22 = _ogg_page_granulepos(i12) | 0;
  i24 = i20;
  HEAP32[i24 >> 2] = i22;
  HEAP32[i24 + 4 >> 2] = tempRet0;
  i24 = i20;
  i22 = HEAP32[i24 + 4 >> 2] | 0;
  i23 = (HEAP32[i6 >> 2] | 0) + 496 | 0;
  HEAP32[i23 >> 2] = HEAP32[i24 >> 2];
  HEAP32[i23 + 4 >> 2] = i22;
  if (HEAP32[(HEAP32[i4 >> 2] | 0) + 88 >> 2] | 0) {
   i22 = i20;
   if ((HEAP32[i22 >> 2] | 0) != -1 ? 1 : (HEAP32[i22 + 4 >> 2] | 0) != -1) i21 = 64; else i21 = 65;
  } else {
   i22 = i20;
   if ((HEAP32[(HEAP32[i6 >> 2] | 0) + 448 >> 2] | 0) != 0 & ((HEAP32[i22 >> 2] | 0) != -1 ? 1 : (HEAP32[i22 + 4 >> 2] | 0) != -1)) i21 = 64; else i21 = 65;
  }
  if ((i21 | 0) == 64) {
   i21 = 0;
   i22 = i20;
   i23 = _oggz_get_unit(HEAP32[i4 >> 2] | 0, HEAP32[i10 >> 2] | 0, HEAP32[i22 >> 2] | 0, HEAP32[i22 + 4 >> 2] | 0) | 0;
   i22 = (HEAP32[i5 >> 2] | 0) + 416 | 0;
   HEAP32[i22 >> 2] = i23;
   HEAP32[i22 + 4 >> 2] = tempRet0;
  } else if ((i21 | 0) == 65 ? (i21 = 0, i22 = i20, (HEAP32[i22 >> 2] | 0) == 0 & (HEAP32[i22 + 4 >> 2] | 0) == 0) : 0) {
   i22 = (HEAP32[i5 >> 2] | 0) + 416 | 0;
   HEAP32[i22 >> 2] = 0;
   HEAP32[i22 + 4 >> 2] = 0;
  }
  if (!(HEAP32[(HEAP32[i6 >> 2] | 0) + 476 >> 2] | 0)) {
   if (HEAP32[(HEAP32[i5 >> 2] | 0) + 404 >> 2] | 0) HEAP32[i13 >> 2] = FUNCTION_TABLE_iiiii[HEAP32[(HEAP32[i5 >> 2] | 0) + 404 >> 2] & 15](HEAP32[i4 >> 2] | 0, i12, HEAP32[i10 >> 2] | 0, HEAP32[(HEAP32[i5 >> 2] | 0) + 408 >> 2] | 0) | 0;
  } else HEAP32[i13 >> 2] = FUNCTION_TABLE_iiiii[HEAP32[(HEAP32[i6 >> 2] | 0) + 476 >> 2] & 15](HEAP32[i4 >> 2] | 0, i12, HEAP32[i10 >> 2] | 0, HEAP32[(HEAP32[i6 >> 2] | 0) + 480 >> 2] | 0) | 0;
  _ogg_stream_pagein(HEAP32[i7 >> 2] | 0, i12) | 0;
  i22 = (_ogg_page_continued(i12) | 0) != 0;
  i23 = (HEAP32[i5 >> 2] | 0) + 440 | 0;
  if (!i22) {
   HEAP32[i23 >> 2] = 1;
   HEAP32[(HEAP32[i5 >> 2] | 0) + 436 >> 2] = HEAP32[(HEAP32[i4 >> 2] | 0) + 64 >> 2];
   HEAP32[(HEAP32[i5 >> 2] | 0) + 444 >> 2] = 0;
   continue;
  }
  if ((HEAP32[i23 >> 2] | 0) == -1) continue;
  i23 = (HEAP32[i5 >> 2] | 0) + 440 | 0;
  HEAP32[i23 >> 2] = (HEAP32[i23 >> 2] | 0) + 1;
 }
 if ((i21 | 0) == 7) {
  HEAP32[i3 >> 2] = -18;
  i25 = HEAP32[i3 >> 2] | 0;
  STACKTOP = i2;
  return i25 | 0;
 } else if ((i21 | 0) == 10) {
  HEAP32[i3 >> 2] = -17;
  i25 = HEAP32[i3 >> 2] | 0;
  STACKTOP = i2;
  return i25 | 0;
 } else if ((i21 | 0) == 12) {
  HEAP32[i3 >> 2] = -17;
  i25 = HEAP32[i3 >> 2] | 0;
  STACKTOP = i2;
  return i25 | 0;
 } else if ((i21 | 0) == 36) {
  HEAP32[i3 >> 2] = -17;
  i25 = HEAP32[i3 >> 2] | 0;
  STACKTOP = i2;
  return i25 | 0;
 } else if ((i21 | 0) == 51) {
  HEAP32[i3 >> 2] = HEAP32[i13 >> 2];
  i25 = HEAP32[i3 >> 2] | 0;
  STACKTOP = i2;
  return i25 | 0;
 } else if ((i21 | 0) == 53) {
  HEAP32[i3 >> 2] = -404;
  i25 = HEAP32[i3 >> 2] | 0;
  STACKTOP = i2;
  return i25 | 0;
 } else if ((i21 | 0) == 56) {
  HEAP32[i3 >> 2] = -18;
  i25 = HEAP32[i3 >> 2] | 0;
  STACKTOP = i2;
  return i25 | 0;
 } else if ((i21 | 0) == 75) {
  HEAP32[i3 >> 2] = HEAP32[i13 >> 2];
  i25 = HEAP32[i3 >> 2] | 0;
  STACKTOP = i2;
  return i25 | 0;
 }
 return 0;
}

function _free(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0, i17 = 0, i18 = 0, i19 = 0, i20 = 0, i21 = 0, i22 = 0, i23 = 0, i24 = 0, i25 = 0, i26 = 0, i27 = 0, i28 = 0, i29 = 0, i30 = 0, i31 = 0, i32 = 0, i33 = 0, i34 = 0, i35 = 0, i36 = 0, i37 = 0;
 if (!i1) return;
 i2 = i1 + -8 | 0;
 i3 = HEAP32[1351] | 0;
 if (i2 >>> 0 < i3 >>> 0) _abort();
 i4 = HEAP32[i1 + -4 >> 2] | 0;
 i1 = i4 & 3;
 if ((i1 | 0) == 1) _abort();
 i5 = i4 & -8;
 i6 = i2 + i5 | 0;
 do if (!(i4 & 1)) {
  i7 = HEAP32[i2 >> 2] | 0;
  if (!i1) return;
  i8 = i2 + (0 - i7) | 0;
  i9 = i7 + i5 | 0;
  if (i8 >>> 0 < i3 >>> 0) _abort();
  if ((i8 | 0) == (HEAP32[1352] | 0)) {
   i10 = i6 + 4 | 0;
   i11 = HEAP32[i10 >> 2] | 0;
   if ((i11 & 3 | 0) != 3) {
    i12 = i8;
    i13 = i9;
    break;
   }
   HEAP32[1349] = i9;
   HEAP32[i10 >> 2] = i11 & -2;
   HEAP32[i8 + 4 >> 2] = i9 | 1;
   HEAP32[i8 + i9 >> 2] = i9;
   return;
  }
  i11 = i7 >>> 3;
  if (i7 >>> 0 < 256) {
   i7 = HEAP32[i8 + 8 >> 2] | 0;
   i10 = HEAP32[i8 + 12 >> 2] | 0;
   i14 = 5428 + (i11 << 1 << 2) | 0;
   if ((i7 | 0) != (i14 | 0)) {
    if (i7 >>> 0 < i3 >>> 0) _abort();
    if ((HEAP32[i7 + 12 >> 2] | 0) != (i8 | 0)) _abort();
   }
   if ((i10 | 0) == (i7 | 0)) {
    HEAP32[1347] = HEAP32[1347] & ~(1 << i11);
    i12 = i8;
    i13 = i9;
    break;
   }
   if ((i10 | 0) != (i14 | 0)) {
    if (i10 >>> 0 < i3 >>> 0) _abort();
    i14 = i10 + 8 | 0;
    if ((HEAP32[i14 >> 2] | 0) == (i8 | 0)) i15 = i14; else _abort();
   } else i15 = i10 + 8 | 0;
   HEAP32[i7 + 12 >> 2] = i10;
   HEAP32[i15 >> 2] = i7;
   i12 = i8;
   i13 = i9;
   break;
  }
  i7 = HEAP32[i8 + 24 >> 2] | 0;
  i10 = HEAP32[i8 + 12 >> 2] | 0;
  do if ((i10 | 0) == (i8 | 0)) {
   i14 = i8 + 16 | 0;
   i11 = i14 + 4 | 0;
   i16 = HEAP32[i11 >> 2] | 0;
   if (!i16) {
    i17 = HEAP32[i14 >> 2] | 0;
    if (!i17) {
     i18 = 0;
     break;
    } else {
     i19 = i17;
     i20 = i14;
    }
   } else {
    i19 = i16;
    i20 = i11;
   }
   while (1) {
    i11 = i19 + 20 | 0;
    i16 = HEAP32[i11 >> 2] | 0;
    if (i16 | 0) {
     i19 = i16;
     i20 = i11;
     continue;
    }
    i11 = i19 + 16 | 0;
    i16 = HEAP32[i11 >> 2] | 0;
    if (!i16) {
     i21 = i19;
     i22 = i20;
     break;
    } else {
     i19 = i16;
     i20 = i11;
    }
   }
   if (i22 >>> 0 < i3 >>> 0) _abort(); else {
    HEAP32[i22 >> 2] = 0;
    i18 = i21;
    break;
   }
  } else {
   i11 = HEAP32[i8 + 8 >> 2] | 0;
   if (i11 >>> 0 < i3 >>> 0) _abort();
   i16 = i11 + 12 | 0;
   if ((HEAP32[i16 >> 2] | 0) != (i8 | 0)) _abort();
   i14 = i10 + 8 | 0;
   if ((HEAP32[i14 >> 2] | 0) == (i8 | 0)) {
    HEAP32[i16 >> 2] = i10;
    HEAP32[i14 >> 2] = i11;
    i18 = i10;
    break;
   } else _abort();
  } while (0);
  if (i7) {
   i10 = HEAP32[i8 + 28 >> 2] | 0;
   i11 = 5692 + (i10 << 2) | 0;
   if ((i8 | 0) == (HEAP32[i11 >> 2] | 0)) {
    HEAP32[i11 >> 2] = i18;
    if (!i18) {
     HEAP32[1348] = HEAP32[1348] & ~(1 << i10);
     i12 = i8;
     i13 = i9;
     break;
    }
   } else {
    if (i7 >>> 0 < (HEAP32[1351] | 0) >>> 0) _abort();
    i10 = i7 + 16 | 0;
    if ((HEAP32[i10 >> 2] | 0) == (i8 | 0)) HEAP32[i10 >> 2] = i18; else HEAP32[i7 + 20 >> 2] = i18;
    if (!i18) {
     i12 = i8;
     i13 = i9;
     break;
    }
   }
   i10 = HEAP32[1351] | 0;
   if (i18 >>> 0 < i10 >>> 0) _abort();
   HEAP32[i18 + 24 >> 2] = i7;
   i11 = i8 + 16 | 0;
   i14 = HEAP32[i11 >> 2] | 0;
   do if (i14 | 0) if (i14 >>> 0 < i10 >>> 0) _abort(); else {
    HEAP32[i18 + 16 >> 2] = i14;
    HEAP32[i14 + 24 >> 2] = i18;
    break;
   } while (0);
   i14 = HEAP32[i11 + 4 >> 2] | 0;
   if (i14) if (i14 >>> 0 < (HEAP32[1351] | 0) >>> 0) _abort(); else {
    HEAP32[i18 + 20 >> 2] = i14;
    HEAP32[i14 + 24 >> 2] = i18;
    i12 = i8;
    i13 = i9;
    break;
   } else {
    i12 = i8;
    i13 = i9;
   }
  } else {
   i12 = i8;
   i13 = i9;
  }
 } else {
  i12 = i2;
  i13 = i5;
 } while (0);
 if (i12 >>> 0 >= i6 >>> 0) _abort();
 i5 = i6 + 4 | 0;
 i2 = HEAP32[i5 >> 2] | 0;
 if (!(i2 & 1)) _abort();
 if (!(i2 & 2)) {
  if ((i6 | 0) == (HEAP32[1353] | 0)) {
   i18 = (HEAP32[1350] | 0) + i13 | 0;
   HEAP32[1350] = i18;
   HEAP32[1353] = i12;
   HEAP32[i12 + 4 >> 2] = i18 | 1;
   if ((i12 | 0) != (HEAP32[1352] | 0)) return;
   HEAP32[1352] = 0;
   HEAP32[1349] = 0;
   return;
  }
  if ((i6 | 0) == (HEAP32[1352] | 0)) {
   i18 = (HEAP32[1349] | 0) + i13 | 0;
   HEAP32[1349] = i18;
   HEAP32[1352] = i12;
   HEAP32[i12 + 4 >> 2] = i18 | 1;
   HEAP32[i12 + i18 >> 2] = i18;
   return;
  }
  i18 = (i2 & -8) + i13 | 0;
  i3 = i2 >>> 3;
  do if (i2 >>> 0 >= 256) {
   i21 = HEAP32[i6 + 24 >> 2] | 0;
   i22 = HEAP32[i6 + 12 >> 2] | 0;
   do if ((i22 | 0) == (i6 | 0)) {
    i20 = i6 + 16 | 0;
    i19 = i20 + 4 | 0;
    i15 = HEAP32[i19 >> 2] | 0;
    if (!i15) {
     i1 = HEAP32[i20 >> 2] | 0;
     if (!i1) {
      i23 = 0;
      break;
     } else {
      i24 = i1;
      i25 = i20;
     }
    } else {
     i24 = i15;
     i25 = i19;
    }
    while (1) {
     i19 = i24 + 20 | 0;
     i15 = HEAP32[i19 >> 2] | 0;
     if (i15 | 0) {
      i24 = i15;
      i25 = i19;
      continue;
     }
     i19 = i24 + 16 | 0;
     i15 = HEAP32[i19 >> 2] | 0;
     if (!i15) {
      i26 = i24;
      i27 = i25;
      break;
     } else {
      i24 = i15;
      i25 = i19;
     }
    }
    if (i27 >>> 0 < (HEAP32[1351] | 0) >>> 0) _abort(); else {
     HEAP32[i27 >> 2] = 0;
     i23 = i26;
     break;
    }
   } else {
    i19 = HEAP32[i6 + 8 >> 2] | 0;
    if (i19 >>> 0 < (HEAP32[1351] | 0) >>> 0) _abort();
    i15 = i19 + 12 | 0;
    if ((HEAP32[i15 >> 2] | 0) != (i6 | 0)) _abort();
    i20 = i22 + 8 | 0;
    if ((HEAP32[i20 >> 2] | 0) == (i6 | 0)) {
     HEAP32[i15 >> 2] = i22;
     HEAP32[i20 >> 2] = i19;
     i23 = i22;
     break;
    } else _abort();
   } while (0);
   if (i21 | 0) {
    i22 = HEAP32[i6 + 28 >> 2] | 0;
    i9 = 5692 + (i22 << 2) | 0;
    if ((i6 | 0) == (HEAP32[i9 >> 2] | 0)) {
     HEAP32[i9 >> 2] = i23;
     if (!i23) {
      HEAP32[1348] = HEAP32[1348] & ~(1 << i22);
      break;
     }
    } else {
     if (i21 >>> 0 < (HEAP32[1351] | 0) >>> 0) _abort();
     i22 = i21 + 16 | 0;
     if ((HEAP32[i22 >> 2] | 0) == (i6 | 0)) HEAP32[i22 >> 2] = i23; else HEAP32[i21 + 20 >> 2] = i23;
     if (!i23) break;
    }
    i22 = HEAP32[1351] | 0;
    if (i23 >>> 0 < i22 >>> 0) _abort();
    HEAP32[i23 + 24 >> 2] = i21;
    i9 = i6 + 16 | 0;
    i8 = HEAP32[i9 >> 2] | 0;
    do if (i8 | 0) if (i8 >>> 0 < i22 >>> 0) _abort(); else {
     HEAP32[i23 + 16 >> 2] = i8;
     HEAP32[i8 + 24 >> 2] = i23;
     break;
    } while (0);
    i8 = HEAP32[i9 + 4 >> 2] | 0;
    if (i8 | 0) if (i8 >>> 0 < (HEAP32[1351] | 0) >>> 0) _abort(); else {
     HEAP32[i23 + 20 >> 2] = i8;
     HEAP32[i8 + 24 >> 2] = i23;
     break;
    }
   }
  } else {
   i8 = HEAP32[i6 + 8 >> 2] | 0;
   i22 = HEAP32[i6 + 12 >> 2] | 0;
   i21 = 5428 + (i3 << 1 << 2) | 0;
   if ((i8 | 0) != (i21 | 0)) {
    if (i8 >>> 0 < (HEAP32[1351] | 0) >>> 0) _abort();
    if ((HEAP32[i8 + 12 >> 2] | 0) != (i6 | 0)) _abort();
   }
   if ((i22 | 0) == (i8 | 0)) {
    HEAP32[1347] = HEAP32[1347] & ~(1 << i3);
    break;
   }
   if ((i22 | 0) != (i21 | 0)) {
    if (i22 >>> 0 < (HEAP32[1351] | 0) >>> 0) _abort();
    i21 = i22 + 8 | 0;
    if ((HEAP32[i21 >> 2] | 0) == (i6 | 0)) i28 = i21; else _abort();
   } else i28 = i22 + 8 | 0;
   HEAP32[i8 + 12 >> 2] = i22;
   HEAP32[i28 >> 2] = i8;
  } while (0);
  HEAP32[i12 + 4 >> 2] = i18 | 1;
  HEAP32[i12 + i18 >> 2] = i18;
  if ((i12 | 0) == (HEAP32[1352] | 0)) {
   HEAP32[1349] = i18;
   return;
  } else i29 = i18;
 } else {
  HEAP32[i5 >> 2] = i2 & -2;
  HEAP32[i12 + 4 >> 2] = i13 | 1;
  HEAP32[i12 + i13 >> 2] = i13;
  i29 = i13;
 }
 i13 = i29 >>> 3;
 if (i29 >>> 0 < 256) {
  i2 = 5428 + (i13 << 1 << 2) | 0;
  i5 = HEAP32[1347] | 0;
  i18 = 1 << i13;
  if (i5 & i18) {
   i13 = i2 + 8 | 0;
   i28 = HEAP32[i13 >> 2] | 0;
   if (i28 >>> 0 < (HEAP32[1351] | 0) >>> 0) _abort(); else {
    i30 = i13;
    i31 = i28;
   }
  } else {
   HEAP32[1347] = i5 | i18;
   i30 = i2 + 8 | 0;
   i31 = i2;
  }
  HEAP32[i30 >> 2] = i12;
  HEAP32[i31 + 12 >> 2] = i12;
  HEAP32[i12 + 8 >> 2] = i31;
  HEAP32[i12 + 12 >> 2] = i2;
  return;
 }
 i2 = i29 >>> 8;
 if (i2) if (i29 >>> 0 > 16777215) i32 = 31; else {
  i31 = (i2 + 1048320 | 0) >>> 16 & 8;
  i30 = i2 << i31;
  i2 = (i30 + 520192 | 0) >>> 16 & 4;
  i18 = i30 << i2;
  i30 = (i18 + 245760 | 0) >>> 16 & 2;
  i5 = 14 - (i2 | i31 | i30) + (i18 << i30 >>> 15) | 0;
  i32 = i29 >>> (i5 + 7 | 0) & 1 | i5 << 1;
 } else i32 = 0;
 i5 = 5692 + (i32 << 2) | 0;
 HEAP32[i12 + 28 >> 2] = i32;
 HEAP32[i12 + 20 >> 2] = 0;
 HEAP32[i12 + 16 >> 2] = 0;
 i30 = HEAP32[1348] | 0;
 i18 = 1 << i32;
 do if (i30 & i18) {
  i31 = i29 << ((i32 | 0) == 31 ? 0 : 25 - (i32 >>> 1) | 0);
  i2 = HEAP32[i5 >> 2] | 0;
  while (1) {
   if ((HEAP32[i2 + 4 >> 2] & -8 | 0) == (i29 | 0)) {
    i33 = i2;
    i34 = 130;
    break;
   }
   i28 = i2 + 16 + (i31 >>> 31 << 2) | 0;
   i13 = HEAP32[i28 >> 2] | 0;
   if (!i13) {
    i35 = i28;
    i36 = i2;
    i34 = 127;
    break;
   } else {
    i31 = i31 << 1;
    i2 = i13;
   }
  }
  if ((i34 | 0) == 127) if (i35 >>> 0 < (HEAP32[1351] | 0) >>> 0) _abort(); else {
   HEAP32[i35 >> 2] = i12;
   HEAP32[i12 + 24 >> 2] = i36;
   HEAP32[i12 + 12 >> 2] = i12;
   HEAP32[i12 + 8 >> 2] = i12;
   break;
  } else if ((i34 | 0) == 130) {
   i2 = i33 + 8 | 0;
   i31 = HEAP32[i2 >> 2] | 0;
   i9 = HEAP32[1351] | 0;
   if (i31 >>> 0 >= i9 >>> 0 & i33 >>> 0 >= i9 >>> 0) {
    HEAP32[i31 + 12 >> 2] = i12;
    HEAP32[i2 >> 2] = i12;
    HEAP32[i12 + 8 >> 2] = i31;
    HEAP32[i12 + 12 >> 2] = i33;
    HEAP32[i12 + 24 >> 2] = 0;
    break;
   } else _abort();
  }
 } else {
  HEAP32[1348] = i30 | i18;
  HEAP32[i5 >> 2] = i12;
  HEAP32[i12 + 24 >> 2] = i5;
  HEAP32[i12 + 12 >> 2] = i12;
  HEAP32[i12 + 8 >> 2] = i12;
 } while (0);
 i12 = (HEAP32[1355] | 0) + -1 | 0;
 HEAP32[1355] = i12;
 if (!i12) i37 = 5844; else return;
 while (1) {
  i12 = HEAP32[i37 >> 2] | 0;
  if (!i12) break; else i37 = i12 + 8 | 0;
 }
 HEAP32[1355] = -1;
 return;
}

function _dispose_chunk(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0, i17 = 0, i18 = 0, i19 = 0, i20 = 0, i21 = 0, i22 = 0, i23 = 0, i24 = 0, i25 = 0, i26 = 0, i27 = 0, i28 = 0, i29 = 0, i30 = 0, i31 = 0, i32 = 0, i33 = 0, i34 = 0, i35 = 0;
 i3 = i1 + i2 | 0;
 i4 = HEAP32[i1 + 4 >> 2] | 0;
 do if (!(i4 & 1)) {
  i5 = HEAP32[i1 >> 2] | 0;
  if (!(i4 & 3)) return;
  i6 = i1 + (0 - i5) | 0;
  i7 = i5 + i2 | 0;
  i8 = HEAP32[1351] | 0;
  if (i6 >>> 0 < i8 >>> 0) _abort();
  if ((i6 | 0) == (HEAP32[1352] | 0)) {
   i9 = i3 + 4 | 0;
   i10 = HEAP32[i9 >> 2] | 0;
   if ((i10 & 3 | 0) != 3) {
    i11 = i6;
    i12 = i7;
    break;
   }
   HEAP32[1349] = i7;
   HEAP32[i9 >> 2] = i10 & -2;
   HEAP32[i6 + 4 >> 2] = i7 | 1;
   HEAP32[i6 + i7 >> 2] = i7;
   return;
  }
  i10 = i5 >>> 3;
  if (i5 >>> 0 < 256) {
   i5 = HEAP32[i6 + 8 >> 2] | 0;
   i9 = HEAP32[i6 + 12 >> 2] | 0;
   i13 = 5428 + (i10 << 1 << 2) | 0;
   if ((i5 | 0) != (i13 | 0)) {
    if (i5 >>> 0 < i8 >>> 0) _abort();
    if ((HEAP32[i5 + 12 >> 2] | 0) != (i6 | 0)) _abort();
   }
   if ((i9 | 0) == (i5 | 0)) {
    HEAP32[1347] = HEAP32[1347] & ~(1 << i10);
    i11 = i6;
    i12 = i7;
    break;
   }
   if ((i9 | 0) != (i13 | 0)) {
    if (i9 >>> 0 < i8 >>> 0) _abort();
    i13 = i9 + 8 | 0;
    if ((HEAP32[i13 >> 2] | 0) == (i6 | 0)) i14 = i13; else _abort();
   } else i14 = i9 + 8 | 0;
   HEAP32[i5 + 12 >> 2] = i9;
   HEAP32[i14 >> 2] = i5;
   i11 = i6;
   i12 = i7;
   break;
  }
  i5 = HEAP32[i6 + 24 >> 2] | 0;
  i9 = HEAP32[i6 + 12 >> 2] | 0;
  do if ((i9 | 0) == (i6 | 0)) {
   i13 = i6 + 16 | 0;
   i10 = i13 + 4 | 0;
   i15 = HEAP32[i10 >> 2] | 0;
   if (!i15) {
    i16 = HEAP32[i13 >> 2] | 0;
    if (!i16) {
     i17 = 0;
     break;
    } else {
     i18 = i16;
     i19 = i13;
    }
   } else {
    i18 = i15;
    i19 = i10;
   }
   while (1) {
    i10 = i18 + 20 | 0;
    i15 = HEAP32[i10 >> 2] | 0;
    if (i15 | 0) {
     i18 = i15;
     i19 = i10;
     continue;
    }
    i10 = i18 + 16 | 0;
    i15 = HEAP32[i10 >> 2] | 0;
    if (!i15) {
     i20 = i18;
     i21 = i19;
     break;
    } else {
     i18 = i15;
     i19 = i10;
    }
   }
   if (i21 >>> 0 < i8 >>> 0) _abort(); else {
    HEAP32[i21 >> 2] = 0;
    i17 = i20;
    break;
   }
  } else {
   i10 = HEAP32[i6 + 8 >> 2] | 0;
   if (i10 >>> 0 < i8 >>> 0) _abort();
   i15 = i10 + 12 | 0;
   if ((HEAP32[i15 >> 2] | 0) != (i6 | 0)) _abort();
   i13 = i9 + 8 | 0;
   if ((HEAP32[i13 >> 2] | 0) == (i6 | 0)) {
    HEAP32[i15 >> 2] = i9;
    HEAP32[i13 >> 2] = i10;
    i17 = i9;
    break;
   } else _abort();
  } while (0);
  if (i5) {
   i9 = HEAP32[i6 + 28 >> 2] | 0;
   i8 = 5692 + (i9 << 2) | 0;
   if ((i6 | 0) == (HEAP32[i8 >> 2] | 0)) {
    HEAP32[i8 >> 2] = i17;
    if (!i17) {
     HEAP32[1348] = HEAP32[1348] & ~(1 << i9);
     i11 = i6;
     i12 = i7;
     break;
    }
   } else {
    if (i5 >>> 0 < (HEAP32[1351] | 0) >>> 0) _abort();
    i9 = i5 + 16 | 0;
    if ((HEAP32[i9 >> 2] | 0) == (i6 | 0)) HEAP32[i9 >> 2] = i17; else HEAP32[i5 + 20 >> 2] = i17;
    if (!i17) {
     i11 = i6;
     i12 = i7;
     break;
    }
   }
   i9 = HEAP32[1351] | 0;
   if (i17 >>> 0 < i9 >>> 0) _abort();
   HEAP32[i17 + 24 >> 2] = i5;
   i8 = i6 + 16 | 0;
   i10 = HEAP32[i8 >> 2] | 0;
   do if (i10 | 0) if (i10 >>> 0 < i9 >>> 0) _abort(); else {
    HEAP32[i17 + 16 >> 2] = i10;
    HEAP32[i10 + 24 >> 2] = i17;
    break;
   } while (0);
   i10 = HEAP32[i8 + 4 >> 2] | 0;
   if (i10) if (i10 >>> 0 < (HEAP32[1351] | 0) >>> 0) _abort(); else {
    HEAP32[i17 + 20 >> 2] = i10;
    HEAP32[i10 + 24 >> 2] = i17;
    i11 = i6;
    i12 = i7;
    break;
   } else {
    i11 = i6;
    i12 = i7;
   }
  } else {
   i11 = i6;
   i12 = i7;
  }
 } else {
  i11 = i1;
  i12 = i2;
 } while (0);
 i2 = HEAP32[1351] | 0;
 if (i3 >>> 0 < i2 >>> 0) _abort();
 i1 = i3 + 4 | 0;
 i17 = HEAP32[i1 >> 2] | 0;
 if (!(i17 & 2)) {
  if ((i3 | 0) == (HEAP32[1353] | 0)) {
   i20 = (HEAP32[1350] | 0) + i12 | 0;
   HEAP32[1350] = i20;
   HEAP32[1353] = i11;
   HEAP32[i11 + 4 >> 2] = i20 | 1;
   if ((i11 | 0) != (HEAP32[1352] | 0)) return;
   HEAP32[1352] = 0;
   HEAP32[1349] = 0;
   return;
  }
  if ((i3 | 0) == (HEAP32[1352] | 0)) {
   i20 = (HEAP32[1349] | 0) + i12 | 0;
   HEAP32[1349] = i20;
   HEAP32[1352] = i11;
   HEAP32[i11 + 4 >> 2] = i20 | 1;
   HEAP32[i11 + i20 >> 2] = i20;
   return;
  }
  i20 = (i17 & -8) + i12 | 0;
  i21 = i17 >>> 3;
  do if (i17 >>> 0 >= 256) {
   i19 = HEAP32[i3 + 24 >> 2] | 0;
   i18 = HEAP32[i3 + 12 >> 2] | 0;
   do if ((i18 | 0) == (i3 | 0)) {
    i14 = i3 + 16 | 0;
    i4 = i14 + 4 | 0;
    i10 = HEAP32[i4 >> 2] | 0;
    if (!i10) {
     i9 = HEAP32[i14 >> 2] | 0;
     if (!i9) {
      i22 = 0;
      break;
     } else {
      i23 = i9;
      i24 = i14;
     }
    } else {
     i23 = i10;
     i24 = i4;
    }
    while (1) {
     i4 = i23 + 20 | 0;
     i10 = HEAP32[i4 >> 2] | 0;
     if (i10 | 0) {
      i23 = i10;
      i24 = i4;
      continue;
     }
     i4 = i23 + 16 | 0;
     i10 = HEAP32[i4 >> 2] | 0;
     if (!i10) {
      i25 = i23;
      i26 = i24;
      break;
     } else {
      i23 = i10;
      i24 = i4;
     }
    }
    if (i26 >>> 0 < i2 >>> 0) _abort(); else {
     HEAP32[i26 >> 2] = 0;
     i22 = i25;
     break;
    }
   } else {
    i4 = HEAP32[i3 + 8 >> 2] | 0;
    if (i4 >>> 0 < i2 >>> 0) _abort();
    i10 = i4 + 12 | 0;
    if ((HEAP32[i10 >> 2] | 0) != (i3 | 0)) _abort();
    i14 = i18 + 8 | 0;
    if ((HEAP32[i14 >> 2] | 0) == (i3 | 0)) {
     HEAP32[i10 >> 2] = i18;
     HEAP32[i14 >> 2] = i4;
     i22 = i18;
     break;
    } else _abort();
   } while (0);
   if (i19 | 0) {
    i18 = HEAP32[i3 + 28 >> 2] | 0;
    i7 = 5692 + (i18 << 2) | 0;
    if ((i3 | 0) == (HEAP32[i7 >> 2] | 0)) {
     HEAP32[i7 >> 2] = i22;
     if (!i22) {
      HEAP32[1348] = HEAP32[1348] & ~(1 << i18);
      break;
     }
    } else {
     if (i19 >>> 0 < (HEAP32[1351] | 0) >>> 0) _abort();
     i18 = i19 + 16 | 0;
     if ((HEAP32[i18 >> 2] | 0) == (i3 | 0)) HEAP32[i18 >> 2] = i22; else HEAP32[i19 + 20 >> 2] = i22;
     if (!i22) break;
    }
    i18 = HEAP32[1351] | 0;
    if (i22 >>> 0 < i18 >>> 0) _abort();
    HEAP32[i22 + 24 >> 2] = i19;
    i7 = i3 + 16 | 0;
    i6 = HEAP32[i7 >> 2] | 0;
    do if (i6 | 0) if (i6 >>> 0 < i18 >>> 0) _abort(); else {
     HEAP32[i22 + 16 >> 2] = i6;
     HEAP32[i6 + 24 >> 2] = i22;
     break;
    } while (0);
    i6 = HEAP32[i7 + 4 >> 2] | 0;
    if (i6 | 0) if (i6 >>> 0 < (HEAP32[1351] | 0) >>> 0) _abort(); else {
     HEAP32[i22 + 20 >> 2] = i6;
     HEAP32[i6 + 24 >> 2] = i22;
     break;
    }
   }
  } else {
   i6 = HEAP32[i3 + 8 >> 2] | 0;
   i18 = HEAP32[i3 + 12 >> 2] | 0;
   i19 = 5428 + (i21 << 1 << 2) | 0;
   if ((i6 | 0) != (i19 | 0)) {
    if (i6 >>> 0 < i2 >>> 0) _abort();
    if ((HEAP32[i6 + 12 >> 2] | 0) != (i3 | 0)) _abort();
   }
   if ((i18 | 0) == (i6 | 0)) {
    HEAP32[1347] = HEAP32[1347] & ~(1 << i21);
    break;
   }
   if ((i18 | 0) != (i19 | 0)) {
    if (i18 >>> 0 < i2 >>> 0) _abort();
    i19 = i18 + 8 | 0;
    if ((HEAP32[i19 >> 2] | 0) == (i3 | 0)) i27 = i19; else _abort();
   } else i27 = i18 + 8 | 0;
   HEAP32[i6 + 12 >> 2] = i18;
   HEAP32[i27 >> 2] = i6;
  } while (0);
  HEAP32[i11 + 4 >> 2] = i20 | 1;
  HEAP32[i11 + i20 >> 2] = i20;
  if ((i11 | 0) == (HEAP32[1352] | 0)) {
   HEAP32[1349] = i20;
   return;
  } else i28 = i20;
 } else {
  HEAP32[i1 >> 2] = i17 & -2;
  HEAP32[i11 + 4 >> 2] = i12 | 1;
  HEAP32[i11 + i12 >> 2] = i12;
  i28 = i12;
 }
 i12 = i28 >>> 3;
 if (i28 >>> 0 < 256) {
  i17 = 5428 + (i12 << 1 << 2) | 0;
  i1 = HEAP32[1347] | 0;
  i20 = 1 << i12;
  if (i1 & i20) {
   i12 = i17 + 8 | 0;
   i27 = HEAP32[i12 >> 2] | 0;
   if (i27 >>> 0 < (HEAP32[1351] | 0) >>> 0) _abort(); else {
    i29 = i12;
    i30 = i27;
   }
  } else {
   HEAP32[1347] = i1 | i20;
   i29 = i17 + 8 | 0;
   i30 = i17;
  }
  HEAP32[i29 >> 2] = i11;
  HEAP32[i30 + 12 >> 2] = i11;
  HEAP32[i11 + 8 >> 2] = i30;
  HEAP32[i11 + 12 >> 2] = i17;
  return;
 }
 i17 = i28 >>> 8;
 if (i17) if (i28 >>> 0 > 16777215) i31 = 31; else {
  i30 = (i17 + 1048320 | 0) >>> 16 & 8;
  i29 = i17 << i30;
  i17 = (i29 + 520192 | 0) >>> 16 & 4;
  i20 = i29 << i17;
  i29 = (i20 + 245760 | 0) >>> 16 & 2;
  i1 = 14 - (i17 | i30 | i29) + (i20 << i29 >>> 15) | 0;
  i31 = i28 >>> (i1 + 7 | 0) & 1 | i1 << 1;
 } else i31 = 0;
 i1 = 5692 + (i31 << 2) | 0;
 HEAP32[i11 + 28 >> 2] = i31;
 HEAP32[i11 + 20 >> 2] = 0;
 HEAP32[i11 + 16 >> 2] = 0;
 i29 = HEAP32[1348] | 0;
 i20 = 1 << i31;
 if (!(i29 & i20)) {
  HEAP32[1348] = i29 | i20;
  HEAP32[i1 >> 2] = i11;
  HEAP32[i11 + 24 >> 2] = i1;
  HEAP32[i11 + 12 >> 2] = i11;
  HEAP32[i11 + 8 >> 2] = i11;
  return;
 }
 i20 = i28 << ((i31 | 0) == 31 ? 0 : 25 - (i31 >>> 1) | 0);
 i31 = HEAP32[i1 >> 2] | 0;
 while (1) {
  if ((HEAP32[i31 + 4 >> 2] & -8 | 0) == (i28 | 0)) {
   i32 = i31;
   i33 = 127;
   break;
  }
  i1 = i31 + 16 + (i20 >>> 31 << 2) | 0;
  i29 = HEAP32[i1 >> 2] | 0;
  if (!i29) {
   i34 = i1;
   i35 = i31;
   i33 = 124;
   break;
  } else {
   i20 = i20 << 1;
   i31 = i29;
  }
 }
 if ((i33 | 0) == 124) {
  if (i34 >>> 0 < (HEAP32[1351] | 0) >>> 0) _abort();
  HEAP32[i34 >> 2] = i11;
  HEAP32[i11 + 24 >> 2] = i35;
  HEAP32[i11 + 12 >> 2] = i11;
  HEAP32[i11 + 8 >> 2] = i11;
  return;
 } else if ((i33 | 0) == 127) {
  i33 = i32 + 8 | 0;
  i35 = HEAP32[i33 >> 2] | 0;
  i34 = HEAP32[1351] | 0;
  if (!(i35 >>> 0 >= i34 >>> 0 & i32 >>> 0 >= i34 >>> 0)) _abort();
  HEAP32[i35 + 12 >> 2] = i11;
  HEAP32[i33 >> 2] = i11;
  HEAP32[i11 + 8 >> 2] = i35;
  HEAP32[i11 + 12 >> 2] = i32;
  HEAP32[i11 + 24 >> 2] = 0;
  return;
 }
}

function _auto_calc_vorbis(i1, i2, i3, i4) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 var i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0, i17 = 0, i18 = 0, i19 = 0, i20 = 0, i21 = 0, i22 = 0, i23 = 0, i24 = 0, i25 = 0, i26 = 0, i27 = 0, i28 = 0, i29 = 0, i30 = 0, i31 = 0, i32 = 0;
 i5 = STACKTOP;
 STACKTOP = STACKTOP + 96 | 0;
 i6 = i5 + 16 | 0;
 i7 = i5 + 8 | 0;
 i8 = i5 + 80 | 0;
 i9 = i5 + 76 | 0;
 i10 = i5 + 72 | 0;
 i11 = i5 + 68 | 0;
 i12 = i5 + 64 | 0;
 i13 = i5 + 60 | 0;
 i14 = i5 + 56 | 0;
 i15 = i5 + 52 | 0;
 i16 = i5 + 48 | 0;
 i17 = i5 + 44 | 0;
 i18 = i5 + 40 | 0;
 i19 = i5 + 36 | 0;
 i20 = i5 + 32 | 0;
 i21 = i5 + 28 | 0;
 i22 = i5 + 24 | 0;
 i23 = i5;
 i24 = i7;
 HEAP32[i24 >> 2] = i1;
 HEAP32[i24 + 4 >> 2] = i2;
 HEAP32[i8 >> 2] = i3;
 HEAP32[i9 >> 2] = i4;
 i4 = HEAP32[HEAP32[i9 >> 2] >> 2] | 0;
 if (!(HEAP32[(HEAP32[i8 >> 2] | 0) + 504 >> 2] | 0)) {
  HEAP32[i13 >> 2] = 1 << ((HEAPU8[i4 + 28 >> 0] | 0) >> 4);
  HEAP32[i12 >> 2] = 1 << ((HEAPU8[(HEAP32[HEAP32[i9 >> 2] >> 2] | 0) + 28 >> 0] | 0) & 15);
  i3 = _malloc(44) | 0;
  HEAP32[(HEAP32[i8 >> 2] | 0) + 504 >> 2] = i3;
  if (!(HEAP32[(HEAP32[i8 >> 2] | 0) + 504 >> 2] | 0)) {
   i3 = i6;
   HEAP32[i3 >> 2] = -1;
   HEAP32[i3 + 4 >> 2] = -1;
   i25 = i6;
   i26 = i25;
   i27 = HEAP32[i26 >> 2] | 0;
   i28 = i25 + 4 | 0;
   i29 = i28;
   i30 = HEAP32[i29 >> 2] | 0;
   tempRet0 = i30;
   STACKTOP = i5;
   return i27 | 0;
  } else {
   HEAP32[i10 >> 2] = HEAP32[(HEAP32[i8 >> 2] | 0) + 504 >> 2];
   HEAP32[(HEAP32[i10 >> 2] | 0) + 12 >> 2] = HEAP32[i13 >> 2] >> 1;
   HEAP32[(HEAP32[i10 >> 2] | 0) + 8 >> 2] = ((HEAP32[i13 >> 2] >> 2) * 3 | 0) - (HEAP32[i12 >> 2] >> 2);
   HEAP32[(HEAP32[i10 >> 2] | 0) + 4 >> 2] = (HEAP32[i13 >> 2] >> 2) + (HEAP32[i12 >> 2] >> 2);
   HEAP32[HEAP32[i10 >> 2] >> 2] = HEAP32[(HEAP32[i10 >> 2] | 0) + 12 >> 2];
   HEAP32[(HEAP32[i10 >> 2] | 0) + 20 >> 2] = HEAP32[i12 >> 2];
   HEAP32[(HEAP32[i10 >> 2] | 0) + 24 >> 2] = HEAP32[i13 >> 2];
   HEAP32[(HEAP32[i10 >> 2] | 0) + 16 >> 2] = HEAP32[i12 >> 2] >> 1;
   HEAP32[(HEAP32[i10 >> 2] | 0) + 28 >> 2] = 0;
   i12 = i6;
   HEAP32[i12 >> 2] = 0;
   HEAP32[i12 + 4 >> 2] = 0;
   i25 = i6;
   i26 = i25;
   i27 = HEAP32[i26 >> 2] | 0;
   i28 = i25 + 4 | 0;
   i29 = i28;
   i30 = HEAP32[i29 >> 2] | 0;
   tempRet0 = i30;
   STACKTOP = i5;
   return i27 | 0;
  }
 }
 if (!((HEAPU8[i4 >> 0] | 0) & 1)) {
  HEAP32[i10 >> 2] = HEAP32[(HEAP32[i8 >> 2] | 0) + 504 >> 2];
  HEAP32[i21 >> 2] = (HEAPU8[HEAP32[HEAP32[i9 >> 2] >> 2] >> 0] | 0) >> 1 & (1 << HEAP32[(HEAP32[i10 >> 2] | 0) + 36 >> 2]) - 1;
  HEAP32[i22 >> 2] = HEAP32[(HEAP32[i10 >> 2] | 0) + 40 + (HEAP32[i21 >> 2] << 2) >> 2];
  i21 = i7;
  i4 = HEAP32[i21 + 4 >> 2] | 0;
  if ((i4 | 0) > -1 | (i4 | 0) == -1 & (HEAP32[i21 >> 2] | 0) >>> 0 > 4294967295 ? (i21 = (HEAP32[i8 >> 2] | 0) + 488 | 0, (HEAP32[i21 >> 2] | 0) == -1 ? (HEAP32[i21 + 4 >> 2] | 0) == -1 : 0) : 0) {
   HEAP32[(HEAP32[i10 >> 2] | 0) + 28 >> 2] = 1;
   HEAP32[(HEAP32[i10 >> 2] | 0) + 32 >> 2] = HEAP32[i22 >> 2];
   i21 = i7;
   i7 = HEAP32[i21 + 4 >> 2] | 0;
   i4 = i6;
   HEAP32[i4 >> 2] = HEAP32[i21 >> 2];
   HEAP32[i4 + 4 >> 2] = i7;
   i25 = i6;
   i26 = i25;
   i27 = HEAP32[i26 >> 2] | 0;
   i28 = i25 + 4 | 0;
   i29 = i28;
   i30 = HEAP32[i29 >> 2] | 0;
   tempRet0 = i30;
   STACKTOP = i5;
   return i27 | 0;
  }
  if (!(HEAP32[(HEAP32[i10 >> 2] | 0) + 28 >> 2] | 0)) {
   HEAP32[(HEAP32[i10 >> 2] | 0) + 28 >> 2] = 1;
   HEAP32[(HEAP32[i10 >> 2] | 0) + 32 >> 2] = HEAP32[i22 >> 2];
   i7 = i6;
   HEAP32[i7 >> 2] = -1;
   HEAP32[i7 + 4 >> 2] = -1;
   i25 = i6;
   i26 = i25;
   i27 = HEAP32[i26 >> 2] | 0;
   i28 = i25 + 4 | 0;
   i29 = i28;
   i30 = HEAP32[i29 >> 2] | 0;
   tempRet0 = i30;
   STACKTOP = i5;
   return i27 | 0;
  }
  i7 = (HEAP32[i8 >> 2] | 0) + 488 | 0;
  if ((HEAP32[i7 >> 2] | 0) == -1 ? (HEAP32[i7 + 4 >> 2] | 0) == -1 : 0) {
   HEAP32[(HEAP32[i10 >> 2] | 0) + 32 >> 2] = HEAP32[i22 >> 2];
   i7 = i6;
   HEAP32[i7 >> 2] = -1;
   HEAP32[i7 + 4 >> 2] = -1;
   i25 = i6;
   i26 = i25;
   i27 = HEAP32[i26 >> 2] | 0;
   i28 = i25 + 4 | 0;
   i29 = i28;
   i30 = HEAP32[i29 >> 2] | 0;
   tempRet0 = i30;
   STACKTOP = i5;
   return i27 | 0;
  }
  i7 = (HEAP32[i8 >> 2] | 0) + 488 | 0;
  i4 = HEAP32[i10 >> 2] | 0;
  if (HEAP32[(HEAP32[i10 >> 2] | 0) + 32 >> 2] | 0) i31 = HEAP32[i4 + 24 >> 2] | 0; else i31 = HEAP32[i4 + 20 >> 2] | 0;
  i4 = HEAP32[i10 >> 2] | 0;
  if (HEAP32[i22 >> 2] | 0) i32 = HEAP32[i4 + 24 >> 2] | 0; else i32 = HEAP32[i4 + 20 >> 2] | 0;
  i4 = (i31 + i32 | 0) / 4 | 0;
  i32 = _i64Add(HEAP32[i7 >> 2] | 0, HEAP32[i7 + 4 >> 2] | 0, i4 | 0, ((i4 | 0) < 0) << 31 >> 31 | 0) | 0;
  i4 = i23;
  HEAP32[i4 >> 2] = i32;
  HEAP32[i4 + 4 >> 2] = tempRet0;
  HEAP32[(HEAP32[i10 >> 2] | 0) + 32 >> 2] = HEAP32[i22 >> 2];
  i22 = i23;
  i23 = HEAP32[i22 + 4 >> 2] | 0;
  i4 = i6;
  HEAP32[i4 >> 2] = HEAP32[i22 >> 2];
  HEAP32[i4 + 4 >> 2] = i23;
  i25 = i6;
  i26 = i25;
  i27 = HEAP32[i26 >> 2] | 0;
  i28 = i25 + 4 | 0;
  i29 = i28;
  i30 = HEAP32[i29 >> 2] | 0;
  tempRet0 = i30;
  STACKTOP = i5;
  return i27 | 0;
 }
 L34 : do if ((HEAPU8[HEAP32[HEAP32[i9 >> 2] >> 2] >> 0] | 0 | 0) == 5) {
  HEAP32[i14 >> 2] = (HEAP32[HEAP32[i9 >> 2] >> 2] | 0) + ((HEAP32[(HEAP32[i9 >> 2] | 0) + 4 >> 2] | 0) - 1);
  HEAP32[i16 >> 2] = 0;
  HEAP32[i15 >> 2] = 8;
  while (1) {
   i23 = (HEAP32[i15 >> 2] | 0) + -1 | 0;
   HEAP32[i15 >> 2] = i23;
   if (!((1 << i23 & (HEAPU8[HEAP32[i14 >> 2] >> 0] | 0) | 0) != 0 ^ 1)) break;
   if (HEAP32[i15 >> 2] | 0) continue;
   HEAP32[i15 >> 2] = 8;
   HEAP32[i14 >> 2] = (HEAP32[i14 >> 2] | 0) + -1;
  }
  while (1) {
   HEAP32[i15 >> 2] = ((HEAP32[i15 >> 2] | 0) + 7 | 0) % 8 | 0;
   if ((HEAP32[i15 >> 2] | 0) == 7) HEAP32[i14 >> 2] = (HEAP32[i14 >> 2] | 0) + -1;
   if ((HEAPU8[(HEAP32[i14 >> 2] | 0) + -5 >> 0] | 0) & ~((1 << (HEAP32[i15 >> 2] | 0) + 1) - 1) | 0) break;
   if (HEAPU8[(HEAP32[i14 >> 2] | 0) + -4 >> 0] | 0 | 0) break;
   if (HEAPU8[(HEAP32[i14 >> 2] | 0) + -3 >> 0] | 0 | 0) break;
   if (HEAPU8[(HEAP32[i14 >> 2] | 0) + -2 >> 0] | 0 | 0) break;
   if ((HEAPU8[(HEAP32[i14 >> 2] | 0) + -1 >> 0] | 0) & (1 << (HEAP32[i15 >> 2] | 0) + 1) - 1 | 0) break;
   HEAP32[i16 >> 2] = (HEAP32[i16 >> 2] | 0) + 1;
   HEAP32[i14 >> 2] = (HEAP32[i14 >> 2] | 0) + -5;
  }
  HEAP32[i11 >> 2] = 0;
  while (1) {
   if ((HEAP32[i11 >> 2] | 0) >= 2) break;
   i23 = HEAPU8[HEAP32[i14 >> 2] >> 0] | 0;
   i4 = HEAP32[i15 >> 2] | 0;
   if ((HEAP32[i15 >> 2] | 0) > 4) HEAP32[i17 >> 2] = i23 >> i4 - 5 & 63; else {
    HEAP32[i17 >> 2] = i23 & (1 << i4 + 1) - 1;
    HEAP32[i17 >> 2] = HEAP32[i17 >> 2] << 5 - (HEAP32[i15 >> 2] | 0);
    HEAP32[i17 >> 2] = HEAP32[i17 >> 2] | ((HEAPU8[(HEAP32[i14 >> 2] | 0) + -1 >> 0] | 0) & ~((1 << (HEAP32[i15 >> 2] | 0) + 3) - 1)) >> (HEAP32[i15 >> 2] | 0) + 3;
   }
   HEAP32[i17 >> 2] = (HEAP32[i17 >> 2] | 0) + 1;
   if ((HEAP32[i17 >> 2] | 0) == (HEAP32[i16 >> 2] | 0)) break;
   HEAP32[i15 >> 2] = ((HEAP32[i15 >> 2] | 0) + 1 | 0) % 8 | 0;
   if (!(HEAP32[i15 >> 2] | 0)) HEAP32[i14 >> 2] = (HEAP32[i14 >> 2] | 0) + 1;
   HEAP32[i14 >> 2] = (HEAP32[i14 >> 2] | 0) + 5;
   HEAP32[i16 >> 2] = (HEAP32[i16 >> 2] | 0) - 1;
   HEAP32[i11 >> 2] = (HEAP32[i11 >> 2] | 0) + 1;
  }
  HEAP32[i20 >> 2] = 44 + ((HEAP32[i16 >> 2] | 0) - 1 << 2);
  if ((HEAP32[i20 >> 2] | 0) >>> 0 < 44) {
   i4 = i6;
   HEAP32[i4 >> 2] = -1;
   HEAP32[i4 + 4 >> 2] = -1;
   i25 = i6;
   i26 = i25;
   i27 = HEAP32[i26 >> 2] | 0;
   i28 = i25 + 4 | 0;
   i29 = i28;
   i30 = HEAP32[i29 >> 2] | 0;
   tempRet0 = i30;
   STACKTOP = i5;
   return i27 | 0;
  }
  HEAP32[i10 >> 2] = _realloc(HEAP32[(HEAP32[i8 >> 2] | 0) + 504 >> 2] | 0, HEAP32[i20 >> 2] | 0) | 0;
  if (!(HEAP32[i10 >> 2] | 0)) {
   i4 = i6;
   HEAP32[i4 >> 2] = -1;
   HEAP32[i4 + 4 >> 2] = -1;
   i25 = i6;
   i26 = i25;
   i27 = HEAP32[i26 >> 2] | 0;
   i28 = i25 + 4 | 0;
   i29 = i28;
   i30 = HEAP32[i29 >> 2] | 0;
   tempRet0 = i30;
   STACKTOP = i5;
   return i27 | 0;
  }
  HEAP32[(HEAP32[i8 >> 2] | 0) + 504 >> 2] = HEAP32[i10 >> 2];
  HEAP32[i19 >> 2] = -1;
  do {
   i4 = (HEAP32[i19 >> 2] | 0) + 1 | 0;
   HEAP32[i19 >> 2] = i4;
  } while ((1 << i4 | 0) < (HEAP32[i16 >> 2] | 0));
  HEAP32[(HEAP32[i10 >> 2] | 0) + 36 >> 2] = HEAP32[i19 >> 2];
  HEAP32[i18 >> 2] = (HEAP32[i10 >> 2] | 0) + 40;
  HEAP32[i19 >> 2] = 0;
  while (1) {
   if ((HEAP32[i19 >> 2] | 0) >= (HEAP32[i16 >> 2] | 0)) break L34;
   HEAP32[i15 >> 2] = ((HEAP32[i15 >> 2] | 0) + 1 | 0) % 8 | 0;
   if (!(HEAP32[i15 >> 2] | 0)) HEAP32[i14 >> 2] = (HEAP32[i14 >> 2] | 0) + 1;
   i4 = (HEAPU8[HEAP32[i14 >> 2] >> 0] | 0) >> HEAP32[i15 >> 2] & 1;
   i23 = HEAP32[i18 >> 2] | 0;
   HEAP32[i18 >> 2] = i23 + 4;
   HEAP32[i23 >> 2] = i4;
   HEAP32[i14 >> 2] = (HEAP32[i14 >> 2] | 0) + 5;
   HEAP32[i19 >> 2] = (HEAP32[i19 >> 2] | 0) + 1;
  }
 } while (0);
 i19 = i6;
 HEAP32[i19 >> 2] = 0;
 HEAP32[i19 + 4 >> 2] = 0;
 i25 = i6;
 i26 = i25;
 i27 = HEAP32[i26 >> 2] | 0;
 i28 = i25 + 4 | 0;
 i29 = i28;
 i30 = HEAP32[i29 >> 2] | 0;
 tempRet0 = i30;
 STACKTOP = i5;
 return i27 | 0;
}

function _ogg_stream_pagein(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0, i17 = 0, i18 = 0, i19 = 0, i20 = 0, i21 = 0, i22 = 0, i23 = 0, i24 = 0, i25 = 0, i26 = 0, i27 = 0, i28 = 0, i29 = 0, i30 = 0, i31 = 0, i32 = 0, i33 = 0, i34 = 0, i35 = 0;
 i3 = HEAP32[i2 >> 2] | 0;
 i4 = HEAP32[i2 + 8 >> 2] | 0;
 i5 = HEAP32[i2 + 12 >> 2] | 0;
 i2 = HEAP8[i3 + 4 >> 0] | 0;
 i6 = HEAPU8[i3 + 5 >> 0] | 0;
 i7 = i6 & 1;
 i8 = i6 & 2;
 i9 = i6 & 4;
 i6 = _bitshift64Shl(HEAPU8[i3 + 13 >> 0] | 0 | 0, 0, 8) | 0;
 i10 = _bitshift64Shl(i6 | (HEAPU8[i3 + 12 >> 0] | 0) | 0, tempRet0 | 0, 8) | 0;
 i6 = _bitshift64Shl(i10 | (HEAPU8[i3 + 11 >> 0] | 0) | 0, tempRet0 | 0, 8) | 0;
 i10 = _bitshift64Shl(i6 | (HEAPU8[i3 + 10 >> 0] | 0) | 0, tempRet0 | 0, 8) | 0;
 i6 = _bitshift64Shl(i10 | (HEAPU8[i3 + 9 >> 0] | 0) | 0, tempRet0 | 0, 8) | 0;
 i10 = _bitshift64Shl(i6 | (HEAPU8[i3 + 8 >> 0] | 0) | 0, tempRet0 | 0, 8) | 0;
 i6 = _bitshift64Shl(i10 | (HEAPU8[i3 + 7 >> 0] | 0) | 0, tempRet0 | 0, 8) | 0;
 i10 = tempRet0;
 i11 = i6 | (HEAPU8[i3 + 6 >> 0] | 0);
 i6 = (HEAPU8[i3 + 15 >> 0] | 0) << 8 | (HEAPU8[i3 + 14 >> 0] | 0) | (HEAPU8[i3 + 16 >> 0] | 0) << 16 | (HEAPU8[i3 + 17 >> 0] | 0) << 24;
 i12 = (HEAPU8[i3 + 19 >> 0] | 0) << 8 | (HEAPU8[i3 + 18 >> 0] | 0) | (HEAPU8[i3 + 20 >> 0] | 0) << 16 | (HEAPU8[i3 + 21 >> 0] | 0) << 24;
 i13 = HEAP8[i3 + 26 >> 0] | 0;
 i14 = i13 & 255;
 if (!i1) {
  i15 = -1;
  return i15 | 0;
 }
 i16 = HEAP32[i1 >> 2] | 0;
 if (!i16) {
  i15 = -1;
  return i15 | 0;
 }
 i17 = i1 + 36 | 0;
 i18 = HEAP32[i17 >> 2] | 0;
 i19 = i1 + 12 | 0;
 i20 = HEAP32[i19 >> 2] | 0;
 if (i20 | 0) {
  i21 = i1 + 8 | 0;
  i22 = HEAP32[i21 >> 2] | 0;
  i23 = i22 - i20 | 0;
  HEAP32[i21 >> 2] = i23;
  if ((i22 | 0) != (i20 | 0)) _memmove(i16 | 0, i16 + i20 | 0, i23 | 0) | 0;
  HEAP32[i19 >> 2] = 0;
 }
 if (i18 | 0) {
  i19 = i1 + 28 | 0;
  i23 = HEAP32[i19 >> 2] | 0;
  if ((i23 | 0) == (i18 | 0)) i24 = i18; else {
   i20 = HEAP32[i1 + 16 >> 2] | 0;
   _memmove(i20 | 0, i20 + (i18 << 2) | 0, i23 - i18 << 2 | 0) | 0;
   i23 = HEAP32[i1 + 20 >> 2] | 0;
   _memmove(i23 | 0, i23 + (i18 << 3) | 0, (HEAP32[i19 >> 2] | 0) - i18 << 3 | 0) | 0;
   i24 = HEAP32[i19 >> 2] | 0;
  }
  HEAP32[i19 >> 2] = i24 - i18;
  i24 = i1 + 32 | 0;
  HEAP32[i24 >> 2] = (HEAP32[i24 >> 2] | 0) - i18;
  HEAP32[i17 >> 2] = 0;
 }
 if (i2 << 24 >> 24 ? 1 : (i6 | 0) != (HEAP32[i1 + 336 >> 2] | 0)) {
  i15 = -1;
  return i15 | 0;
 }
 if (__os_lacing_expand(i1, i14 + 1 | 0) | 0) {
  i15 = -1;
  return i15 | 0;
 }
 i6 = i1 + 340 | 0;
 i2 = HEAP32[i6 >> 2] | 0;
 if ((i12 | 0) != (i2 | 0)) {
  i17 = i1 + 32 | 0;
  i18 = HEAP32[i17 >> 2] | 0;
  i24 = i1 + 28 | 0;
  i19 = HEAP32[i24 >> 2] | 0;
  if ((i18 | 0) < (i19 | 0)) {
   i23 = HEAP32[i1 + 16 >> 2] | 0;
   i20 = i1 + 8 | 0;
   i16 = HEAP32[i20 >> 2] | 0;
   i22 = i18;
   while (1) {
    i21 = i16 - (HEAP32[i23 + (i22 << 2) >> 2] & 255) | 0;
    i22 = i22 + 1 | 0;
    if ((i22 | 0) >= (i19 | 0)) {
     i25 = i21;
     break;
    } else i16 = i21;
   }
   HEAP32[i20 >> 2] = i25;
  }
  HEAP32[i24 >> 2] = i18;
  if ((i2 | 0) != -1) {
   i2 = i18 + 1 | 0;
   HEAP32[i24 >> 2] = i2;
   HEAP32[(HEAP32[i1 + 16 >> 2] | 0) + (i18 << 2) >> 2] = 1024;
   HEAP32[i17 >> 2] = i2;
  }
 }
 L35 : do if (i7) {
  i2 = HEAP32[i1 + 28 >> 2] | 0;
  if ((i2 | 0) >= 1 ? (HEAP32[(HEAP32[i1 + 16 >> 2] | 0) + (i2 + -1 << 2) >> 2] | 0) != 1024 : 0) {
   i26 = i4;
   i27 = i5;
   i28 = i8;
   i29 = 0;
   break;
  }
  if (!(i13 << 24 >> 24)) {
   i26 = i4;
   i27 = i5;
   i28 = 0;
   i29 = 0;
  } else {
   i2 = i4;
   i17 = i5;
   i18 = 0;
   while (1) {
    i24 = HEAP8[i3 + (i18 + 27) >> 0] | 0;
    i25 = i24 & 255;
    i20 = i2 + i25 | 0;
    i16 = i17 - i25 | 0;
    i25 = i24 << 24 >> 24 == -1;
    i24 = (i25 & 1 ^ 1) + i18 | 0;
    if (!i25) {
     i26 = i20;
     i27 = i16;
     i28 = 0;
     i29 = i24;
     break L35;
    }
    i25 = i24 + 1 | 0;
    if ((i25 | 0) < (i14 | 0)) {
     i2 = i20;
     i17 = i16;
     i18 = i25;
    } else {
     i26 = i20;
     i27 = i16;
     i28 = 0;
     i29 = i25;
     break;
    }
   }
  }
 } else {
  i26 = i4;
  i27 = i5;
  i28 = i8;
  i29 = 0;
 } while (0);
 if (i27 | 0) {
  i8 = i1 + 4 | 0;
  i5 = HEAP32[i8 >> 2] | 0;
  i4 = i1 + 8 | 0;
  i13 = HEAP32[i4 >> 2] | 0;
  do if ((i5 - i27 | 0) > (i13 | 0)) {
   i30 = HEAP32[i1 >> 2] | 0;
   i31 = i13;
  } else {
   if ((i5 | 0) > (2147483647 - i27 | 0)) {
    i7 = HEAP32[i1 >> 2] | 0;
    if (i7 | 0) _free(i7);
    i7 = HEAP32[i1 + 16 >> 2] | 0;
    if (i7 | 0) _free(i7);
    i7 = HEAP32[i1 + 20 >> 2] | 0;
    if (i7 | 0) _free(i7);
    _memset(i1 | 0, 0, 360) | 0;
    i15 = -1;
    return i15 | 0;
   }
   i7 = i5 + i27 | 0;
   i18 = (i7 | 0) < 2147482623 ? i7 + 1024 | 0 : i7;
   i7 = _realloc(HEAP32[i1 >> 2] | 0, i18) | 0;
   if (i7 | 0) {
    HEAP32[i8 >> 2] = i18;
    HEAP32[i1 >> 2] = i7;
    i30 = i7;
    i31 = HEAP32[i4 >> 2] | 0;
    break;
   }
   i7 = HEAP32[i1 >> 2] | 0;
   if (i7 | 0) _free(i7);
   i7 = HEAP32[i1 + 16 >> 2] | 0;
   if (i7 | 0) _free(i7);
   i7 = HEAP32[i1 + 20 >> 2] | 0;
   if (i7 | 0) _free(i7);
   _memset(i1 | 0, 0, 360) | 0;
   i15 = -1;
   return i15 | 0;
  } while (0);
  _memcpy(i30 + i31 | 0, i26 | 0, i27 | 0) | 0;
  HEAP32[i4 >> 2] = (HEAP32[i4 >> 2] | 0) + i27;
 }
 if ((i29 | 0) < (i14 | 0)) {
  i27 = i1 + 28 | 0;
  i4 = i1 + 32 | 0;
  i26 = HEAP32[i1 + 16 >> 2] | 0;
  i31 = HEAP32[i1 + 20 >> 2] | 0;
  i30 = HEAP32[i27 >> 2] | 0;
  i8 = i28;
  i28 = -1;
  i5 = i29;
  L79 : while (1) {
   i29 = i30;
   i13 = i8;
   i7 = i5;
   while (1) {
    i18 = HEAP8[i3 + (i7 + 27) >> 0] | 0;
    i17 = i18 & 255;
    i2 = i26 + (i29 << 2) | 0;
    HEAP32[i2 >> 2] = i17;
    i25 = i31 + (i29 << 3) | 0;
    HEAP32[i25 >> 2] = -1;
    HEAP32[i25 + 4 >> 2] = -1;
    if (i13 | 0) HEAP32[i2 >> 2] = i17 | 256;
    i17 = i29;
    i29 = i29 + 1 | 0;
    HEAP32[i27 >> 2] = i29;
    i7 = i7 + 1 | 0;
    if (i18 << 24 >> 24 != -1) {
     i32 = i17;
     i33 = i29;
     i34 = i7;
     break;
    }
    if ((i7 | 0) >= (i14 | 0)) {
     i35 = i28;
     break L79;
    } else i13 = 0;
   }
   HEAP32[i4 >> 2] = i33;
   if ((i34 | 0) < (i14 | 0)) {
    i30 = i33;
    i8 = 0;
    i28 = i32;
    i5 = i34;
   } else {
    i35 = i32;
    break;
   }
  }
  if ((i35 | 0) != -1) {
   i32 = (HEAP32[i1 + 20 >> 2] | 0) + (i35 << 3) | 0;
   HEAP32[i32 >> 2] = i11;
   HEAP32[i32 + 4 >> 2] = i10;
  }
 }
 if (i9 | 0 ? (HEAP32[i1 + 328 >> 2] = 1, i9 = HEAP32[i1 + 28 >> 2] | 0, (i9 | 0) > 0) : 0) {
  i10 = (HEAP32[i1 + 16 >> 2] | 0) + (i9 + -1 << 2) | 0;
  HEAP32[i10 >> 2] = HEAP32[i10 >> 2] | 512;
 }
 HEAP32[i6 >> 2] = i12 + 1;
 i15 = 0;
 return i15 | 0;
}

function _auto_calc_vp8(i1, i2, i3, i4) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 var i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0, i17 = 0, i18 = 0, i19 = 0, i20 = 0, i21 = 0, i22 = 0, i23 = 0, i24 = 0, i25 = 0, i26 = 0, i27 = 0;
 i5 = STACKTOP;
 STACKTOP = STACKTOP + 80 | 0;
 i6 = i5 + 40 | 0;
 i7 = i5 + 32 | 0;
 i8 = i5 + 68 | 0;
 i9 = i5 + 64 | 0;
 i10 = i5 + 60 | 0;
 i11 = i5 + 56 | 0;
 i12 = i5 + 52 | 0;
 i13 = i5 + 48 | 0;
 i14 = i5 + 24 | 0;
 i15 = i5 + 16 | 0;
 i16 = i5 + 8 | 0;
 i17 = i5;
 i18 = i7;
 HEAP32[i18 >> 2] = i1;
 HEAP32[i18 + 4 >> 2] = i2;
 HEAP32[i8 >> 2] = i3;
 HEAP32[i9 >> 2] = i4;
 HEAP32[i13 >> 2] = HEAP32[(HEAP32[i8 >> 2] | 0) + 504 >> 2];
 if (!(HEAP32[(HEAP32[i8 >> 2] | 0) + 504 >> 2] | 0)) {
  i4 = _malloc(8) | 0;
  HEAP32[(HEAP32[i8 >> 2] | 0) + 504 >> 2] = i4;
  if (!(HEAP32[(HEAP32[i8 >> 2] | 0) + 504 >> 2] | 0)) {
   i4 = i6;
   HEAP32[i4 >> 2] = -1;
   HEAP32[i4 + 4 >> 2] = -1;
   i19 = i6;
   i20 = i19;
   i21 = HEAP32[i20 >> 2] | 0;
   i22 = i19 + 4 | 0;
   i23 = i22;
   i24 = HEAP32[i23 >> 2] | 0;
   tempRet0 = i24;
   STACKTOP = i5;
   return i21 | 0;
  } else {
   HEAP32[i13 >> 2] = HEAP32[(HEAP32[i8 >> 2] | 0) + 504 >> 2];
   HEAP32[(HEAP32[i13 >> 2] | 0) + 4 >> 2] = 0;
   HEAP32[HEAP32[i13 >> 2] >> 2] = 1;
   i4 = i6;
   HEAP32[i4 >> 2] = 0;
   HEAP32[i4 + 4 >> 2] = 0;
   i19 = i6;
   i20 = i19;
   i21 = HEAP32[i20 >> 2] | 0;
   i22 = i19 + 4 | 0;
   i23 = i22;
   i24 = HEAP32[i23 >> 2] | 0;
   tempRet0 = i24;
   STACKTOP = i5;
   return i21 | 0;
  }
 }
 if (!(HEAP32[(HEAP32[i9 >> 2] | 0) + 4 >> 2] | 0)) i25 = 1; else i25 = (HEAPU8[HEAP32[HEAP32[i9 >> 2] >> 2] >> 0] | 0 | 0) == 79;
 HEAP32[i10 >> 2] = i25 & 1;
 if (!(HEAP32[i10 >> 2] | 0) ? (HEAP32[(HEAP32[i9 >> 2] | 0) + 4 >> 2] | 0) > 0 : 0) i26 = ((HEAPU8[HEAP32[HEAP32[i9 >> 2] >> 2] >> 0] | 0) & 1 | 0) == 0; else i26 = 0;
 HEAP32[i11 >> 2] = i26 & 1;
 if (!(HEAP32[i10 >> 2] | 0) ? (HEAP32[(HEAP32[i9 >> 2] | 0) + 4 >> 2] | 0) > 0 : 0) i27 = ((HEAPU8[HEAP32[HEAP32[i9 >> 2] >> 2] >> 0] | 0) >> 4 & 1 | 0) != 0; else i27 = 0;
 HEAP32[i12 >> 2] = i27 & 1;
 i27 = HEAP32[i13 >> 2] | 0;
 if (HEAP32[i10 >> 2] | 0) HEAP32[i27 >> 2] = (HEAP32[i27 >> 2] | 0) + 1; else HEAP32[i27 + 4 >> 2] = 1;
 i27 = i7;
 i9 = HEAP32[i27 + 4 >> 2] | 0;
 if ((i9 | 0) > -1 | (i9 | 0) == -1 & (HEAP32[i27 >> 2] | 0) >>> 0 > 4294967295) {
  i27 = i7;
  i7 = HEAP32[i27 + 4 >> 2] | 0;
  i9 = i6;
  HEAP32[i9 >> 2] = HEAP32[i27 >> 2];
  HEAP32[i9 + 4 >> 2] = i7;
  i19 = i6;
  i20 = i19;
  i21 = HEAP32[i20 >> 2] | 0;
  i22 = i19 + 4 | 0;
  i23 = i22;
  i24 = HEAP32[i23 >> 2] | 0;
  tempRet0 = i24;
  STACKTOP = i5;
  return i21 | 0;
 }
 if (!(HEAP32[(HEAP32[i13 >> 2] | 0) + 4 >> 2] | 0)) {
  i13 = i6;
  HEAP32[i13 >> 2] = 0;
  HEAP32[i13 + 4 >> 2] = 0;
  i19 = i6;
  i20 = i19;
  i21 = HEAP32[i20 >> 2] | 0;
  i22 = i19 + 4 | 0;
  i23 = i22;
  i24 = HEAP32[i23 >> 2] | 0;
  tempRet0 = i24;
  STACKTOP = i5;
  return i21 | 0;
 }
 i13 = (HEAP32[i8 >> 2] | 0) + 488 | 0;
 i7 = HEAP32[i13 + 4 >> 2] | 0;
 if (!((i7 | 0) > 0 | (i7 | 0) == 0 & (HEAP32[i13 >> 2] | 0) >>> 0 > 0)) {
  i13 = i6;
  HEAP32[i13 >> 2] = -1;
  HEAP32[i13 + 4 >> 2] = -1;
  i19 = i6;
  i20 = i19;
  i21 = HEAP32[i20 >> 2] | 0;
  i22 = i19 + 4 | 0;
  i23 = i22;
  i24 = HEAP32[i23 >> 2] | 0;
  tempRet0 = i24;
  STACKTOP = i5;
  return i21 | 0;
 }
 i13 = (HEAP32[i8 >> 2] | 0) + 488 | 0;
 i7 = HEAP32[i13 >> 2] | 0;
 i9 = HEAP32[i13 + 4 >> 2] | 0;
 if (HEAP32[i10 >> 2] | 0) {
  i10 = i6;
  HEAP32[i10 >> 2] = i7;
  HEAP32[i10 + 4 >> 2] = i9;
  i19 = i6;
  i20 = i19;
  i21 = HEAP32[i20 >> 2] | 0;
  i22 = i19 + 4 | 0;
  i23 = i22;
  i24 = HEAP32[i23 >> 2] | 0;
  tempRet0 = i24;
  STACKTOP = i5;
  return i21 | 0;
 }
 i10 = _bitshift64Ashr(i7 | 0, i9 | 0, 32) | 0;
 i9 = i14;
 HEAP32[i9 >> 2] = i10;
 HEAP32[i9 + 4 >> 2] = tempRet0;
 i9 = (HEAP32[i8 >> 2] | 0) + 488 | 0;
 i10 = _bitshift64Ashr(HEAP32[i9 >> 2] | 0, HEAP32[i9 + 4 >> 2] | 0, 30) | 0;
 i9 = i15;
 HEAP32[i9 >> 2] = i10 & 3;
 HEAP32[i9 + 4 >> 2] = 0;
 i9 = (HEAP32[i8 >> 2] | 0) + 488 | 0;
 i8 = _bitshift64Ashr(HEAP32[i9 >> 2] | 0, HEAP32[i9 + 4 >> 2] | 0, 3) | 0;
 i9 = i16;
 HEAP32[i9 >> 2] = i8 & 134217727;
 HEAP32[i9 + 4 >> 2] = 0;
 if (HEAP32[i11 >> 2] | 0) {
  i11 = i16;
  HEAP32[i11 >> 2] = 0;
  HEAP32[i11 + 4 >> 2] = 0;
 } else {
  i11 = i16;
  i9 = _i64Add(HEAP32[i11 >> 2] | 0, HEAP32[i11 + 4 >> 2] | 0, 1, 0) | 0;
  i11 = i16;
  HEAP32[i11 >> 2] = i9;
  HEAP32[i11 + 4 >> 2] = tempRet0;
 }
 do if (!(HEAP32[i12 >> 2] | 0)) {
  i11 = i15;
  if ((HEAP32[i11 >> 2] | 0) == 3 & (HEAP32[i11 + 4 >> 2] | 0) == 0) {
   i11 = i15;
   HEAP32[i11 >> 2] = 0;
   HEAP32[i11 + 4 >> 2] = 0;
   break;
  } else {
   i11 = i15;
   i9 = _i64Add(HEAP32[i11 >> 2] | 0, HEAP32[i11 + 4 >> 2] | 0, 1, 0) | 0;
   i11 = i15;
   HEAP32[i11 >> 2] = i9;
   HEAP32[i11 + 4 >> 2] = tempRet0;
   break;
  }
 } else {
  i11 = i14;
  i9 = _i64Add(HEAP32[i11 >> 2] | 0, HEAP32[i11 + 4 >> 2] | 0, 1, 0) | 0;
  i11 = i14;
  HEAP32[i11 >> 2] = i9;
  HEAP32[i11 + 4 >> 2] = tempRet0;
  i11 = i15;
  HEAP32[i11 >> 2] = 3;
  HEAP32[i11 + 4 >> 2] = 0;
 } while (0);
 i12 = HEAP32[i14 >> 2] | 0;
 i14 = i15;
 i15 = _bitshift64Shl(HEAP32[i14 >> 2] | 0, HEAP32[i14 + 4 >> 2] | 0, 30) | 0;
 i14 = i12 | tempRet0;
 i12 = i16;
 i16 = _bitshift64Shl(HEAP32[i12 >> 2] | 0, HEAP32[i12 + 4 >> 2] | 0, 3) | 0;
 i12 = i17;
 HEAP32[i12 >> 2] = i15 | i16;
 HEAP32[i12 + 4 >> 2] = i14 | tempRet0;
 i14 = i17;
 i17 = HEAP32[i14 + 4 >> 2] | 0;
 i12 = i6;
 HEAP32[i12 >> 2] = HEAP32[i14 >> 2];
 HEAP32[i12 + 4 >> 2] = i17;
 i19 = i6;
 i20 = i19;
 i21 = HEAP32[i20 >> 2] | 0;
 i22 = i19 + 4 | 0;
 i23 = i22;
 i24 = HEAP32[i23 >> 2] | 0;
 tempRet0 = i24;
 STACKTOP = i5;
 return i21 | 0;
}

function _oggz_comments_decode(i1, i2, i3, i4) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 var i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0, i17 = 0, i18 = 0, i19 = 0, i20 = 0, i21 = 0, i22 = 0;
 i5 = STACKTOP;
 STACKTOP = STACKTOP + 64 | 0;
 i6 = i5 + 56 | 0;
 i7 = i5 + 52 | 0;
 i8 = i5 + 48 | 0;
 i9 = i5 + 44 | 0;
 i10 = i5 + 40 | 0;
 i11 = i5 + 36 | 0;
 i12 = i5 + 32 | 0;
 i13 = i5 + 28 | 0;
 i14 = i5 + 24 | 0;
 i15 = i5 + 20 | 0;
 i16 = i5 + 16 | 0;
 i17 = i5 + 12 | 0;
 i18 = i5 + 8 | 0;
 i19 = i5 + 4 | 0;
 i20 = i5;
 HEAP32[i7 >> 2] = i1;
 HEAP32[i8 >> 2] = i2;
 HEAP32[i9 >> 2] = i3;
 HEAP32[i10 >> 2] = i4;
 HEAP32[i12 >> 2] = HEAP32[i9 >> 2];
 HEAP32[i20 >> 2] = 0;
 if ((HEAP32[i10 >> 2] | 0) < 8) {
  HEAP32[i6 >> 2] = -1;
  i21 = HEAP32[i6 >> 2] | 0;
  STACKTOP = i5;
  return i21 | 0;
 }
 HEAP32[i17 >> 2] = (HEAP32[i12 >> 2] | 0) + (HEAP32[i10 >> 2] | 0);
 HEAP32[i16 >> 2] = HEAP8[(HEAP32[i12 >> 2] | 0) + 3 >> 0] << 24 & -16777216 | HEAP8[(HEAP32[i12 >> 2] | 0) + 2 >> 0] << 16 & 16711680 | HEAP8[(HEAP32[i12 >> 2] | 0) + 1 >> 0] << 8 & 65280 | HEAP8[HEAP32[i12 >> 2] >> 0] & 255;
 HEAP32[i12 >> 2] = (HEAP32[i12 >> 2] | 0) + 4;
 if ((HEAP32[i16 >> 2] | 0) >>> 0 > ((HEAP32[i17 >> 2] | 0) - (HEAP32[i12 >> 2] | 0) | 0) >>> 0) {
  HEAP32[i6 >> 2] = -1;
  i21 = HEAP32[i6 >> 2] | 0;
  STACKTOP = i5;
  return i21 | 0;
 }
 HEAP32[i11 >> 2] = _oggz_get_stream(HEAP32[i7 >> 2] | 0, HEAP32[i8 >> 2] | 0) | 0;
 if (!(HEAP32[i11 >> 2] | 0)) {
  HEAP32[i6 >> 2] = -20;
  i21 = HEAP32[i6 >> 2] | 0;
  STACKTOP = i5;
  return i21 | 0;
 }
 if ((HEAP32[i16 >> 2] | 0) >>> 0 > 0) {
  i10 = _oggz_strdup_len(HEAP32[i12 >> 2] | 0, HEAP32[i16 >> 2] | 0) | 0;
  HEAP32[i20 >> 2] = i10;
  if (!i10) {
   HEAP32[i6 >> 2] = -18;
   i21 = HEAP32[i6 >> 2] | 0;
   STACKTOP = i5;
   return i21 | 0;
  }
  i10 = (__oggz_comment_set_vendor(HEAP32[i7 >> 2] | 0, HEAP32[i8 >> 2] | 0, HEAP32[i20 >> 2] | 0) | 0) == -18;
  _free(HEAP32[i20 >> 2] | 0);
  if (i10) {
   HEAP32[i6 >> 2] = -18;
   i21 = HEAP32[i6 >> 2] | 0;
   STACKTOP = i5;
   return i21 | 0;
  }
 }
 HEAP32[i12 >> 2] = (HEAP32[i12 >> 2] | 0) + (HEAP32[i16 >> 2] | 0);
 if (((HEAP32[i12 >> 2] | 0) + 4 | 0) >>> 0 > (HEAP32[i17 >> 2] | 0) >>> 0) {
  HEAP32[i6 >> 2] = -1;
  i21 = HEAP32[i6 >> 2] | 0;
  STACKTOP = i5;
  return i21 | 0;
 }
 HEAP32[i14 >> 2] = HEAP8[(HEAP32[i12 >> 2] | 0) + 3 >> 0] << 24 & -16777216 | HEAP8[(HEAP32[i12 >> 2] | 0) + 2 >> 0] << 16 & 16711680 | HEAP8[(HEAP32[i12 >> 2] | 0) + 1 >> 0] << 8 & 65280 | HEAP8[HEAP32[i12 >> 2] >> 0] & 255;
 HEAP32[i12 >> 2] = (HEAP32[i12 >> 2] | 0) + 4;
 HEAP32[i13 >> 2] = 0;
 while (1) {
  if ((HEAP32[i13 >> 2] | 0) >= (HEAP32[i14 >> 2] | 0)) {
   i22 = 34;
   break;
  }
  if (((HEAP32[i12 >> 2] | 0) + 4 | 0) >>> 0 > (HEAP32[i17 >> 2] | 0) >>> 0) {
   i22 = 17;
   break;
  }
  HEAP32[i16 >> 2] = HEAP8[(HEAP32[i12 >> 2] | 0) + 3 >> 0] << 24 & -16777216 | HEAP8[(HEAP32[i12 >> 2] | 0) + 2 >> 0] << 16 & 16711680 | HEAP8[(HEAP32[i12 >> 2] | 0) + 1 >> 0] << 8 & 65280 | HEAP8[HEAP32[i12 >> 2] >> 0] & 255;
  HEAP32[i12 >> 2] = (HEAP32[i12 >> 2] | 0) + 4;
  if ((HEAP32[i16 >> 2] | 0) >>> 0 > ((HEAP32[i17 >> 2] | 0) - (HEAP32[i12 >> 2] | 0) | 0) >>> 0) {
   i22 = 19;
   break;
  }
  HEAP32[i15 >> 2] = 0;
  HEAP32[i18 >> 2] = HEAP32[i12 >> 2];
  HEAP32[i19 >> 2] = _oggz_index_len(HEAP32[i12 >> 2] | 0, 61, HEAP32[i16 >> 2] | 0) | 0;
  if (HEAP32[i19 >> 2] | 0) {
   HEAP8[HEAP32[i19 >> 2] >> 0] = 0;
   HEAP32[i19 >> 2] = (HEAP32[i19 >> 2] | 0) + 1;
   HEAP32[i15 >> 2] = (HEAP32[i12 >> 2] | 0) + (HEAP32[i16 >> 2] | 0) - (HEAP32[i19 >> 2] | 0);
  }
  if (HEAP32[i15 >> 2] | 0) {
   i10 = _oggz_strdup_len(HEAP32[i19 >> 2] | 0, HEAP32[i15 >> 2] | 0) | 0;
   HEAP32[i20 >> 2] = i10;
   if (!i10) {
    i22 = 24;
    break;
   }
   i10 = (__oggz_comment_add_byname(HEAP32[i11 >> 2] | 0, HEAP32[i18 >> 2] | 0, HEAP32[i20 >> 2] | 0) | 0) == 0;
   _free(HEAP32[i20 >> 2] | 0);
   if (i10) {
    i22 = 26;
    break;
   }
  } else {
   i10 = _oggz_strdup_len(HEAP32[i18 >> 2] | 0, HEAP32[i16 >> 2] | 0) | 0;
   HEAP32[i20 >> 2] = i10;
   if (!i10) {
    i22 = 28;
    break;
   }
   i10 = (__oggz_comment_add_byname(HEAP32[i11 >> 2] | 0, HEAP32[i20 >> 2] | 0, 0) | 0) == 0;
   _free(HEAP32[i20 >> 2] | 0);
   if (i10) {
    i22 = 30;
    break;
   }
  }
  if (HEAP32[i19 >> 2] | 0) HEAP8[(HEAP32[i19 >> 2] | 0) + -1 >> 0] = 61;
  HEAP32[i12 >> 2] = (HEAP32[i12 >> 2] | 0) + (HEAP32[i16 >> 2] | 0);
  HEAP32[i13 >> 2] = (HEAP32[i13 >> 2] | 0) + 1;
 }
 if ((i22 | 0) == 17) {
  HEAP32[i6 >> 2] = -1;
  i21 = HEAP32[i6 >> 2] | 0;
  STACKTOP = i5;
  return i21 | 0;
 } else if ((i22 | 0) == 19) {
  HEAP32[i6 >> 2] = -1;
  i21 = HEAP32[i6 >> 2] | 0;
  STACKTOP = i5;
  return i21 | 0;
 } else if ((i22 | 0) == 24) {
  HEAP32[i6 >> 2] = -18;
  i21 = HEAP32[i6 >> 2] | 0;
  STACKTOP = i5;
  return i21 | 0;
 } else if ((i22 | 0) == 26) {
  HEAP32[i6 >> 2] = -18;
  i21 = HEAP32[i6 >> 2] | 0;
  STACKTOP = i5;
  return i21 | 0;
 } else if ((i22 | 0) == 28) {
  HEAP32[i6 >> 2] = -18;
  i21 = HEAP32[i6 >> 2] | 0;
  STACKTOP = i5;
  return i21 | 0;
 } else if ((i22 | 0) == 30) {
  HEAP32[i6 >> 2] = -18;
  i21 = HEAP32[i6 >> 2] | 0;
  STACKTOP = i5;
  return i21 | 0;
 } else if ((i22 | 0) == 34) {
  HEAP32[i6 >> 2] = 0;
  i21 = HEAP32[i6 >> 2] | 0;
  STACKTOP = i5;
  return i21 | 0;
 }
 return 0;
}

function _try_realloc_chunk(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0, i17 = 0, i18 = 0, i19 = 0, i20 = 0, i21 = 0, i22 = 0, i23 = 0;
 i3 = i1 + 4 | 0;
 i4 = HEAP32[i3 >> 2] | 0;
 i5 = i4 & -8;
 i6 = i1 + i5 | 0;
 i7 = HEAP32[1351] | 0;
 i8 = i4 & 3;
 if (!((i8 | 0) != 1 & i1 >>> 0 >= i7 >>> 0 & i1 >>> 0 < i6 >>> 0)) _abort();
 i9 = HEAP32[i6 + 4 >> 2] | 0;
 if (!(i9 & 1)) _abort();
 if (!i8) {
  if (i2 >>> 0 < 256) {
   i10 = 0;
   return i10 | 0;
  }
  if (i5 >>> 0 >= (i2 + 4 | 0) >>> 0 ? (i5 - i2 | 0) >>> 0 <= HEAP32[1467] << 1 >>> 0 : 0) {
   i10 = i1;
   return i10 | 0;
  }
  i10 = 0;
  return i10 | 0;
 }
 if (i5 >>> 0 >= i2 >>> 0) {
  i8 = i5 - i2 | 0;
  if (i8 >>> 0 <= 15) {
   i10 = i1;
   return i10 | 0;
  }
  i11 = i1 + i2 | 0;
  HEAP32[i3 >> 2] = i4 & 1 | i2 | 2;
  HEAP32[i11 + 4 >> 2] = i8 | 3;
  i12 = i11 + i8 + 4 | 0;
  HEAP32[i12 >> 2] = HEAP32[i12 >> 2] | 1;
  _dispose_chunk(i11, i8);
  i10 = i1;
  return i10 | 0;
 }
 if ((i6 | 0) == (HEAP32[1353] | 0)) {
  i8 = (HEAP32[1350] | 0) + i5 | 0;
  if (i8 >>> 0 <= i2 >>> 0) {
   i10 = 0;
   return i10 | 0;
  }
  i11 = i8 - i2 | 0;
  i8 = i1 + i2 | 0;
  HEAP32[i3 >> 2] = i4 & 1 | i2 | 2;
  HEAP32[i8 + 4 >> 2] = i11 | 1;
  HEAP32[1353] = i8;
  HEAP32[1350] = i11;
  i10 = i1;
  return i10 | 0;
 }
 if ((i6 | 0) == (HEAP32[1352] | 0)) {
  i11 = (HEAP32[1349] | 0) + i5 | 0;
  if (i11 >>> 0 < i2 >>> 0) {
   i10 = 0;
   return i10 | 0;
  }
  i8 = i11 - i2 | 0;
  if (i8 >>> 0 > 15) {
   i12 = i1 + i2 | 0;
   i13 = i12 + i8 | 0;
   HEAP32[i3 >> 2] = i4 & 1 | i2 | 2;
   HEAP32[i12 + 4 >> 2] = i8 | 1;
   HEAP32[i13 >> 2] = i8;
   i14 = i13 + 4 | 0;
   HEAP32[i14 >> 2] = HEAP32[i14 >> 2] & -2;
   i15 = i12;
   i16 = i8;
  } else {
   HEAP32[i3 >> 2] = i4 & 1 | i11 | 2;
   i8 = i1 + i11 + 4 | 0;
   HEAP32[i8 >> 2] = HEAP32[i8 >> 2] | 1;
   i15 = 0;
   i16 = 0;
  }
  HEAP32[1349] = i16;
  HEAP32[1352] = i15;
  i10 = i1;
  return i10 | 0;
 }
 if (i9 & 2 | 0) {
  i10 = 0;
  return i10 | 0;
 }
 i15 = (i9 & -8) + i5 | 0;
 if (i15 >>> 0 < i2 >>> 0) {
  i10 = 0;
  return i10 | 0;
 }
 i5 = i15 - i2 | 0;
 i16 = i9 >>> 3;
 do if (i9 >>> 0 >= 256) {
  i8 = HEAP32[i6 + 24 >> 2] | 0;
  i11 = HEAP32[i6 + 12 >> 2] | 0;
  do if ((i11 | 0) == (i6 | 0)) {
   i12 = i6 + 16 | 0;
   i14 = i12 + 4 | 0;
   i13 = HEAP32[i14 >> 2] | 0;
   if (!i13) {
    i17 = HEAP32[i12 >> 2] | 0;
    if (!i17) {
     i18 = 0;
     break;
    } else {
     i19 = i17;
     i20 = i12;
    }
   } else {
    i19 = i13;
    i20 = i14;
   }
   while (1) {
    i14 = i19 + 20 | 0;
    i13 = HEAP32[i14 >> 2] | 0;
    if (i13 | 0) {
     i19 = i13;
     i20 = i14;
     continue;
    }
    i14 = i19 + 16 | 0;
    i13 = HEAP32[i14 >> 2] | 0;
    if (!i13) {
     i21 = i19;
     i22 = i20;
     break;
    } else {
     i19 = i13;
     i20 = i14;
    }
   }
   if (i22 >>> 0 < i7 >>> 0) _abort(); else {
    HEAP32[i22 >> 2] = 0;
    i18 = i21;
    break;
   }
  } else {
   i14 = HEAP32[i6 + 8 >> 2] | 0;
   if (i14 >>> 0 < i7 >>> 0) _abort();
   i13 = i14 + 12 | 0;
   if ((HEAP32[i13 >> 2] | 0) != (i6 | 0)) _abort();
   i12 = i11 + 8 | 0;
   if ((HEAP32[i12 >> 2] | 0) == (i6 | 0)) {
    HEAP32[i13 >> 2] = i11;
    HEAP32[i12 >> 2] = i14;
    i18 = i11;
    break;
   } else _abort();
  } while (0);
  if (i8 | 0) {
   i11 = HEAP32[i6 + 28 >> 2] | 0;
   i14 = 5692 + (i11 << 2) | 0;
   if ((i6 | 0) == (HEAP32[i14 >> 2] | 0)) {
    HEAP32[i14 >> 2] = i18;
    if (!i18) {
     HEAP32[1348] = HEAP32[1348] & ~(1 << i11);
     break;
    }
   } else {
    if (i8 >>> 0 < (HEAP32[1351] | 0) >>> 0) _abort();
    i11 = i8 + 16 | 0;
    if ((HEAP32[i11 >> 2] | 0) == (i6 | 0)) HEAP32[i11 >> 2] = i18; else HEAP32[i8 + 20 >> 2] = i18;
    if (!i18) break;
   }
   i11 = HEAP32[1351] | 0;
   if (i18 >>> 0 < i11 >>> 0) _abort();
   HEAP32[i18 + 24 >> 2] = i8;
   i14 = i6 + 16 | 0;
   i12 = HEAP32[i14 >> 2] | 0;
   do if (i12 | 0) if (i12 >>> 0 < i11 >>> 0) _abort(); else {
    HEAP32[i18 + 16 >> 2] = i12;
    HEAP32[i12 + 24 >> 2] = i18;
    break;
   } while (0);
   i12 = HEAP32[i14 + 4 >> 2] | 0;
   if (i12 | 0) if (i12 >>> 0 < (HEAP32[1351] | 0) >>> 0) _abort(); else {
    HEAP32[i18 + 20 >> 2] = i12;
    HEAP32[i12 + 24 >> 2] = i18;
    break;
   }
  }
 } else {
  i12 = HEAP32[i6 + 8 >> 2] | 0;
  i11 = HEAP32[i6 + 12 >> 2] | 0;
  i8 = 5428 + (i16 << 1 << 2) | 0;
  if ((i12 | 0) != (i8 | 0)) {
   if (i12 >>> 0 < i7 >>> 0) _abort();
   if ((HEAP32[i12 + 12 >> 2] | 0) != (i6 | 0)) _abort();
  }
  if ((i11 | 0) == (i12 | 0)) {
   HEAP32[1347] = HEAP32[1347] & ~(1 << i16);
   break;
  }
  if ((i11 | 0) != (i8 | 0)) {
   if (i11 >>> 0 < i7 >>> 0) _abort();
   i8 = i11 + 8 | 0;
   if ((HEAP32[i8 >> 2] | 0) == (i6 | 0)) i23 = i8; else _abort();
  } else i23 = i11 + 8 | 0;
  HEAP32[i12 + 12 >> 2] = i11;
  HEAP32[i23 >> 2] = i12;
 } while (0);
 if (i5 >>> 0 < 16) {
  HEAP32[i3 >> 2] = i15 | i4 & 1 | 2;
  i23 = i1 + i15 + 4 | 0;
  HEAP32[i23 >> 2] = HEAP32[i23 >> 2] | 1;
  i10 = i1;
  return i10 | 0;
 } else {
  i23 = i1 + i2 | 0;
  HEAP32[i3 >> 2] = i4 & 1 | i2 | 2;
  HEAP32[i23 + 4 >> 2] = i5 | 3;
  i2 = i23 + i5 + 4 | 0;
  HEAP32[i2 >> 2] = HEAP32[i2 >> 2] | 1;
  _dispose_chunk(i23, i5);
  i10 = i1;
  return i10 | 0;
 }
 return 0;
}

function _decode_index(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0, i17 = 0, i18 = 0, i19 = 0, i20 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 80 | 0;
 i4 = i3 + 76 | 0;
 i5 = i3 + 72 | 0;
 i6 = i3 + 68 | 0;
 i7 = i3 + 48 | 0;
 i8 = i3 + 40 | 0;
 i9 = i3 + 32 | 0;
 i10 = i3 + 24 | 0;
 i11 = i3 + 64 | 0;
 i12 = i3 + 16 | 0;
 i13 = i3 + 60 | 0;
 i14 = i3 + 56 | 0;
 i15 = i3 + 8 | 0;
 i16 = i3;
 HEAP32[i5 >> 2] = i1;
 HEAP32[i6 >> 2] = i2;
 i2 = i7;
 HEAP32[i2 >> 2] = 0;
 HEAP32[i2 + 4 >> 2] = 0;
 i2 = i8;
 HEAP32[i2 >> 2] = 0;
 HEAP32[i2 + 4 >> 2] = 0;
 i2 = i9;
 HEAP32[i2 >> 2] = 0;
 HEAP32[i2 + 4 >> 2] = 0;
 i2 = i10;
 HEAP32[i2 >> 2] = 1e3;
 HEAP32[i2 + 4 >> 2] = 0;
 HEAP32[i11 >> 2] = 0;
 i2 = i12;
 HEAP32[i2 >> 2] = -1;
 HEAP32[i2 + 4 >> 2] = -1;
 HEAP32[i13 >> 2] = 0;
 HEAP32[i14 >> 2] = -1;
 if (!(HEAP32[i5 >> 2] | 0)) {
  HEAP32[i4 >> 2] = -2;
  i17 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i17 | 0;
 }
 if (HEAP32[i6 >> 2] | 0 ? HEAP32[HEAP32[i6 >> 2] >> 2] | 0 : 0) {
  HEAP32[i11 >> 2] = _calloc(1, 48) | 0;
  if (!(HEAP32[i11 >> 2] | 0)) {
   HEAP32[i4 >> 2] = -4;
   i17 = HEAP32[i4 >> 2] | 0;
   STACKTOP = i3;
   return i17 | 0;
  }
  HEAP32[i13 >> 2] = (HEAP32[HEAP32[i6 >> 2] >> 2] | 0) + 6;
  HEAP32[i13 >> 2] = _extract_int32(HEAP32[i13 >> 2] | 0, HEAP32[i11 >> 2] | 0) | 0;
  HEAP32[i13 >> 2] = _extract_int64(HEAP32[i13 >> 2] | 0, (HEAP32[i11 >> 2] | 0) + 8 | 0) | 0;
  HEAP32[i13 >> 2] = _extract_int64(HEAP32[i13 >> 2] | 0, (HEAP32[i11 >> 2] | 0) + 16 | 0) | 0;
  if (((HEAPU16[HEAP32[i5 >> 2] >> 1] | 0) << 16 | (HEAPU16[(HEAP32[i5 >> 2] | 0) + 2 >> 1] | 0) | 0) == 262144) {
   HEAP32[i13 >> 2] = _extract_int64(HEAP32[i13 >> 2] | 0, (HEAP32[i11 >> 2] | 0) + 24 | 0) | 0;
   HEAP32[i13 >> 2] = _extract_int64(HEAP32[i13 >> 2] | 0, (HEAP32[i11 >> 2] | 0) + 32 | 0) | 0;
  }
  i2 = (HEAP32[i11 >> 2] | 0) + 16 | 0;
  do if ((HEAP32[i2 >> 2] | 0) == 0 & (HEAP32[i2 + 4 >> 2] | 0) == 0) HEAP32[i14 >> 2] = -9; else {
   i1 = (HEAP32[i11 >> 2] | 0) + 8 | 0;
   i18 = ___muldi3(HEAP32[i1 >> 2] | 0, HEAP32[i1 + 4 >> 2] | 0, 2, 0) | 0;
   i1 = _i64Add(26, 0, i18 | 0, tempRet0 | 0) | 0;
   i18 = i12;
   HEAP32[i18 >> 2] = i1;
   HEAP32[i18 + 4 >> 2] = tempRet0;
   i18 = i12;
   i1 = HEAP32[i18 + 4 >> 2] | 0;
   i19 = HEAP32[(HEAP32[i6 >> 2] | 0) + 4 >> 2] | 0;
   i20 = ((i19 | 0) < 0) << 31 >> 31;
   if ((i1 | 0) > (i20 | 0) | ((i1 | 0) == (i20 | 0) ? (HEAP32[i18 >> 2] | 0) >>> 0 > i19 >>> 0 : 0)) {
    HEAP32[i14 >> 2] = -10;
    break;
   }
   i19 = _calloc(HEAP32[(HEAP32[i11 >> 2] | 0) + 8 >> 2] | 0, 16) | 0;
   HEAP32[(HEAP32[i11 >> 2] | 0) + 40 >> 2] = i19;
   if (!(HEAP32[(HEAP32[i11 >> 2] | 0) + 40 >> 2] | 0)) {
    HEAP32[i14 >> 2] = -4;
    break;
   }
   i19 = i7;
   HEAP32[i19 >> 2] = 0;
   HEAP32[i19 + 4 >> 2] = 0;
   while (1) {
    i19 = i7;
    i18 = HEAP32[i19 + 4 >> 2] | 0;
    i20 = (HEAP32[i11 >> 2] | 0) + 8 | 0;
    i1 = HEAP32[i20 + 4 >> 2] | 0;
    if (!((i18 | 0) < (i1 | 0) | ((i18 | 0) == (i1 | 0) ? (HEAP32[i19 >> 2] | 0) >>> 0 < (HEAP32[i20 >> 2] | 0) >>> 0 : 0))) break;
    i20 = i15;
    HEAP32[i20 >> 2] = 0;
    HEAP32[i20 + 4 >> 2] = 0;
    i20 = i16;
    HEAP32[i20 >> 2] = 0;
    HEAP32[i20 + 4 >> 2] = 0;
    HEAP32[i13 >> 2] = _read_var_length(HEAP32[i13 >> 2] | 0, i15) | 0;
    i20 = i15;
    i19 = i8;
    i1 = _i64Add(HEAP32[i19 >> 2] | 0, HEAP32[i19 + 4 >> 2] | 0, HEAP32[i20 >> 2] | 0, HEAP32[i20 + 4 >> 2] | 0) | 0;
    i20 = i8;
    HEAP32[i20 >> 2] = i1;
    HEAP32[i20 + 4 >> 2] = tempRet0;
    HEAP32[i13 >> 2] = _read_var_length(HEAP32[i13 >> 2] | 0, i16) | 0;
    i20 = i16;
    i1 = i9;
    i19 = _i64Add(HEAP32[i1 >> 2] | 0, HEAP32[i1 + 4 >> 2] | 0, HEAP32[i20 >> 2] | 0, HEAP32[i20 + 4 >> 2] | 0) | 0;
    i20 = i9;
    HEAP32[i20 >> 2] = i19;
    HEAP32[i20 + 4 >> 2] = tempRet0;
    i20 = i8;
    i19 = HEAP32[i20 + 4 >> 2] | 0;
    i1 = (HEAP32[(HEAP32[i11 >> 2] | 0) + 40 >> 2] | 0) + (HEAP32[i7 >> 2] << 4) | 0;
    HEAP32[i1 >> 2] = HEAP32[i20 >> 2];
    HEAP32[i1 + 4 >> 2] = i19;
    i19 = i9;
    i1 = i10;
    i20 = ___muldi3(HEAP32[i19 >> 2] | 0, HEAP32[i19 + 4 >> 2] | 0, HEAP32[i1 >> 2] | 0, HEAP32[i1 + 4 >> 2] | 0) | 0;
    i1 = (HEAP32[i11 >> 2] | 0) + 16 | 0;
    i19 = ___divdi3(i20 | 0, tempRet0 | 0, HEAP32[i1 >> 2] | 0, HEAP32[i1 + 4 >> 2] | 0) | 0;
    i1 = (HEAP32[(HEAP32[i11 >> 2] | 0) + 40 >> 2] | 0) + (HEAP32[i7 >> 2] << 4) + 8 | 0;
    HEAP32[i1 >> 2] = i19;
    HEAP32[i1 + 4 >> 2] = tempRet0;
    i1 = i7;
    i19 = _i64Add(HEAP32[i1 >> 2] | 0, HEAP32[i1 + 4 >> 2] | 0, 1, 0) | 0;
    i1 = i7;
    HEAP32[i1 >> 2] = i19;
    HEAP32[i1 + 4 >> 2] = tempRet0;
   }
   HEAP32[i14 >> 2] = _oggskel_vect_add_index(HEAP32[(HEAP32[i5 >> 2] | 0) + 112 >> 2] | 0, HEAP32[i11 >> 2] | 0, HEAP32[HEAP32[i11 >> 2] >> 2] | 0) | 0;
  } while (0);
  if ((HEAP32[i14 >> 2] | 0) < 0) _free(HEAP32[i11 >> 2] | 0);
  HEAP32[i4 >> 2] = HEAP32[i14 >> 2];
  i17 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i17 | 0;
 }
 HEAP32[i4 >> 2] = -13;
 i17 = HEAP32[i4 >> 2] | 0;
 STACKTOP = i3;
 return i17 | 0;
}

function ___udivmoddi4(i1, i2, i3, i4, i5) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 i5 = i5 | 0;
 var i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0, i17 = 0, i18 = 0, i19 = 0, i20 = 0, i21 = 0, i22 = 0, i23 = 0, i24 = 0, i25 = 0, i26 = 0, i27 = 0, i28 = 0, i29 = 0, i30 = 0, i31 = 0, i32 = 0;
 i6 = i1;
 i7 = i2;
 i8 = i7;
 i9 = i3;
 i10 = i4;
 i11 = i10;
 if (!i8) {
  i12 = (i5 | 0) != 0;
  if (!i11) {
   if (i12) {
    HEAP32[i5 >> 2] = (i6 >>> 0) % (i9 >>> 0);
    HEAP32[i5 + 4 >> 2] = 0;
   }
   i13 = 0;
   i14 = (i6 >>> 0) / (i9 >>> 0) >>> 0;
   return (tempRet0 = i13, i14) | 0;
  } else {
   if (!i12) {
    i13 = 0;
    i14 = 0;
    return (tempRet0 = i13, i14) | 0;
   }
   HEAP32[i5 >> 2] = i1 | 0;
   HEAP32[i5 + 4 >> 2] = i2 & 0;
   i13 = 0;
   i14 = 0;
   return (tempRet0 = i13, i14) | 0;
  }
 }
 i12 = (i11 | 0) == 0;
 do if (i9) {
  if (!i12) {
   i15 = (Math_clz32(i11 | 0) | 0) - (Math_clz32(i8 | 0) | 0) | 0;
   if (i15 >>> 0 <= 31) {
    i16 = i15 + 1 | 0;
    i17 = 31 - i15 | 0;
    i18 = i15 - 31 >> 31;
    i19 = i16;
    i20 = i6 >>> (i16 >>> 0) & i18 | i8 << i17;
    i21 = i8 >>> (i16 >>> 0) & i18;
    i22 = 0;
    i23 = i6 << i17;
    break;
   }
   if (!i5) {
    i13 = 0;
    i14 = 0;
    return (tempRet0 = i13, i14) | 0;
   }
   HEAP32[i5 >> 2] = i1 | 0;
   HEAP32[i5 + 4 >> 2] = i7 | i2 & 0;
   i13 = 0;
   i14 = 0;
   return (tempRet0 = i13, i14) | 0;
  }
  i17 = i9 - 1 | 0;
  if (i17 & i9 | 0) {
   i18 = (Math_clz32(i9 | 0) | 0) + 33 - (Math_clz32(i8 | 0) | 0) | 0;
   i16 = 64 - i18 | 0;
   i15 = 32 - i18 | 0;
   i24 = i15 >> 31;
   i25 = i18 - 32 | 0;
   i26 = i25 >> 31;
   i19 = i18;
   i20 = i15 - 1 >> 31 & i8 >>> (i25 >>> 0) | (i8 << i15 | i6 >>> (i18 >>> 0)) & i26;
   i21 = i26 & i8 >>> (i18 >>> 0);
   i22 = i6 << i16 & i24;
   i23 = (i8 << i16 | i6 >>> (i25 >>> 0)) & i24 | i6 << i15 & i18 - 33 >> 31;
   break;
  }
  if (i5 | 0) {
   HEAP32[i5 >> 2] = i17 & i6;
   HEAP32[i5 + 4 >> 2] = 0;
  }
  if ((i9 | 0) == 1) {
   i13 = i7 | i2 & 0;
   i14 = i1 | 0 | 0;
   return (tempRet0 = i13, i14) | 0;
  } else {
   i17 = _llvm_cttz_i32(i9 | 0) | 0;
   i13 = i8 >>> (i17 >>> 0) | 0;
   i14 = i8 << 32 - i17 | i6 >>> (i17 >>> 0) | 0;
   return (tempRet0 = i13, i14) | 0;
  }
 } else {
  if (i12) {
   if (i5 | 0) {
    HEAP32[i5 >> 2] = (i8 >>> 0) % (i9 >>> 0);
    HEAP32[i5 + 4 >> 2] = 0;
   }
   i13 = 0;
   i14 = (i8 >>> 0) / (i9 >>> 0) >>> 0;
   return (tempRet0 = i13, i14) | 0;
  }
  if (!i6) {
   if (i5 | 0) {
    HEAP32[i5 >> 2] = 0;
    HEAP32[i5 + 4 >> 2] = (i8 >>> 0) % (i11 >>> 0);
   }
   i13 = 0;
   i14 = (i8 >>> 0) / (i11 >>> 0) >>> 0;
   return (tempRet0 = i13, i14) | 0;
  }
  i17 = i11 - 1 | 0;
  if (!(i17 & i11)) {
   if (i5 | 0) {
    HEAP32[i5 >> 2] = i1 | 0;
    HEAP32[i5 + 4 >> 2] = i17 & i8 | i2 & 0;
   }
   i13 = 0;
   i14 = i8 >>> ((_llvm_cttz_i32(i11 | 0) | 0) >>> 0);
   return (tempRet0 = i13, i14) | 0;
  }
  i17 = (Math_clz32(i11 | 0) | 0) - (Math_clz32(i8 | 0) | 0) | 0;
  if (i17 >>> 0 <= 30) {
   i18 = i17 + 1 | 0;
   i15 = 31 - i17 | 0;
   i19 = i18;
   i20 = i8 << i15 | i6 >>> (i18 >>> 0);
   i21 = i8 >>> (i18 >>> 0);
   i22 = 0;
   i23 = i6 << i15;
   break;
  }
  if (!i5) {
   i13 = 0;
   i14 = 0;
   return (tempRet0 = i13, i14) | 0;
  }
  HEAP32[i5 >> 2] = i1 | 0;
  HEAP32[i5 + 4 >> 2] = i7 | i2 & 0;
  i13 = 0;
  i14 = 0;
  return (tempRet0 = i13, i14) | 0;
 } while (0);
 if (!i19) {
  i27 = i23;
  i28 = i22;
  i29 = i21;
  i30 = i20;
  i31 = 0;
  i32 = 0;
 } else {
  i2 = i3 | 0 | 0;
  i3 = i10 | i4 & 0;
  i4 = _i64Add(i2 | 0, i3 | 0, -1, -1) | 0;
  i10 = tempRet0;
  i7 = i23;
  i23 = i22;
  i22 = i21;
  i21 = i20;
  i20 = i19;
  i19 = 0;
  do {
   i1 = i7;
   i7 = i23 >>> 31 | i7 << 1;
   i23 = i19 | i23 << 1;
   i6 = i21 << 1 | i1 >>> 31 | 0;
   i1 = i21 >>> 31 | i22 << 1 | 0;
   _i64Subtract(i4 | 0, i10 | 0, i6 | 0, i1 | 0) | 0;
   i8 = tempRet0;
   i11 = i8 >> 31 | ((i8 | 0) < 0 ? -1 : 0) << 1;
   i19 = i11 & 1;
   i21 = _i64Subtract(i6 | 0, i1 | 0, i11 & i2 | 0, (((i8 | 0) < 0 ? -1 : 0) >> 31 | ((i8 | 0) < 0 ? -1 : 0) << 1) & i3 | 0) | 0;
   i22 = tempRet0;
   i20 = i20 - 1 | 0;
  } while ((i20 | 0) != 0);
  i27 = i7;
  i28 = i23;
  i29 = i22;
  i30 = i21;
  i31 = 0;
  i32 = i19;
 }
 i19 = i28;
 i28 = 0;
 if (i5 | 0) {
  HEAP32[i5 >> 2] = i30;
  HEAP32[i5 + 4 >> 2] = i29;
 }
 i13 = (i19 | 0) >>> 31 | (i27 | i28) << 1 | (i28 << 1 | i19 >>> 31) & 0 | i31;
 i14 = (i19 << 1 | 0 >>> 31) & -2 | i32;
 return (tempRet0 = i13, i14) | 0;
}

function _auto_calc_opus(i1, i2, i3, i4) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 var i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0, i17 = 0, i18 = 0, i19 = 0, i20 = 0, i21 = 0;
 i5 = STACKTOP;
 STACKTOP = STACKTOP + 48 | 0;
 i6 = i5 + 24 | 0;
 i7 = i5 + 16 | 0;
 i8 = i5 + 40 | 0;
 i9 = i5 + 36 | 0;
 i10 = i5 + 32 | 0;
 i11 = i5 + 8 | 0;
 i12 = i5;
 i13 = i7;
 HEAP32[i13 >> 2] = i1;
 HEAP32[i13 + 4 >> 2] = i2;
 HEAP32[i8 >> 2] = i3;
 HEAP32[i9 >> 2] = i4;
 HEAP32[i10 >> 2] = HEAP32[(HEAP32[i8 >> 2] | 0) + 504 >> 2];
 if (!(HEAP32[(HEAP32[i8 >> 2] | 0) + 504 >> 2] | 0)) {
  i4 = _malloc(16) | 0;
  HEAP32[(HEAP32[i8 >> 2] | 0) + 504 >> 2] = i4;
  if (!(HEAP32[(HEAP32[i8 >> 2] | 0) + 504 >> 2] | 0)) {
   i4 = i6;
   HEAP32[i4 >> 2] = -1;
   HEAP32[i4 + 4 >> 2] = -1;
   i14 = i6;
   i15 = i14;
   i16 = HEAP32[i15 >> 2] | 0;
   i17 = i14 + 4 | 0;
   i18 = i17;
   i19 = HEAP32[i18 >> 2] | 0;
   tempRet0 = i19;
   STACKTOP = i5;
   return i16 | 0;
  } else {
   HEAP32[i10 >> 2] = HEAP32[(HEAP32[i8 >> 2] | 0) + 504 >> 2];
   HEAP32[(HEAP32[i10 >> 2] | 0) + 4 >> 2] = 0;
   HEAP32[HEAP32[i10 >> 2] >> 2] = 1;
   i4 = (HEAP32[i10 >> 2] | 0) + 8 | 0;
   HEAP32[i4 >> 2] = 0;
   HEAP32[i4 + 4 >> 2] = 0;
   i4 = i6;
   HEAP32[i4 >> 2] = 0;
   HEAP32[i4 + 4 >> 2] = 0;
   i14 = i6;
   i15 = i14;
   i16 = HEAP32[i15 >> 2] | 0;
   i17 = i14 + 4 | 0;
   i18 = i17;
   i19 = HEAP32[i18 >> 2] | 0;
   tempRet0 = i19;
   STACKTOP = i5;
   return i16 | 0;
  }
 }
 i4 = HEAP32[i10 >> 2] | 0;
 if ((HEAP32[HEAP32[i10 >> 2] >> 2] | 0) < 2) HEAP32[i4 >> 2] = (HEAP32[i4 >> 2] | 0) + 1; else HEAP32[i4 + 4 >> 2] = 1;
 i4 = i7;
 i3 = HEAP32[i4 + 4 >> 2] | 0;
 if ((i3 | 0) > -1 | (i3 | 0) == -1 & (HEAP32[i4 >> 2] | 0) >>> 0 > 4294967295) {
  i4 = i7;
  i7 = HEAP32[i4 + 4 >> 2] | 0;
  i3 = i6;
  HEAP32[i3 >> 2] = HEAP32[i4 >> 2];
  HEAP32[i3 + 4 >> 2] = i7;
  i14 = i6;
  i15 = i14;
  i16 = HEAP32[i15 >> 2] | 0;
  i17 = i14 + 4 | 0;
  i18 = i17;
  i19 = HEAP32[i18 >> 2] | 0;
  tempRet0 = i19;
  STACKTOP = i5;
  return i16 | 0;
 }
 if (!(HEAP32[(HEAP32[i10 >> 2] | 0) + 4 >> 2] | 0)) {
  i7 = i6;
  HEAP32[i7 >> 2] = 0;
  HEAP32[i7 + 4 >> 2] = 0;
  i14 = i6;
  i15 = i14;
  i16 = HEAP32[i15 >> 2] | 0;
  i17 = i14 + 4 | 0;
  i18 = i17;
  i19 = HEAP32[i18 >> 2] | 0;
  tempRet0 = i19;
  STACKTOP = i5;
  return i16 | 0;
 }
 i7 = _opus_packet_duration(HEAP32[i9 >> 2] | 0) | 0;
 i9 = i11;
 HEAP32[i9 >> 2] = i7;
 HEAP32[i9 + 4 >> 2] = tempRet0;
 i9 = (HEAP32[i8 >> 2] | 0) + 488 | 0;
 i7 = HEAP32[i9 + 4 >> 2] | 0;
 if (!((i7 | 0) > 0 | (i7 | 0) == 0 & (HEAP32[i9 >> 2] | 0) >>> 0 > 0)) {
  i9 = i11;
  i7 = (HEAP32[i10 >> 2] | 0) + 8 | 0;
  i10 = i7;
  i3 = _i64Add(HEAP32[i10 >> 2] | 0, HEAP32[i10 + 4 >> 2] | 0, HEAP32[i9 >> 2] | 0, HEAP32[i9 + 4 >> 2] | 0) | 0;
  i9 = i7;
  HEAP32[i9 >> 2] = i3;
  HEAP32[i9 + 4 >> 2] = tempRet0;
  i9 = i6;
  HEAP32[i9 >> 2] = -1;
  HEAP32[i9 + 4 >> 2] = -1;
  i14 = i6;
  i15 = i14;
  i16 = HEAP32[i15 >> 2] | 0;
  i17 = i14 + 4 | 0;
  i18 = i17;
  i19 = HEAP32[i18 >> 2] | 0;
  tempRet0 = i19;
  STACKTOP = i5;
  return i16 | 0;
 }
 i9 = (HEAP32[i8 >> 2] | 0) + 488 | 0;
 i3 = i11;
 i11 = _i64Add(HEAP32[i9 >> 2] | 0, HEAP32[i9 + 4 >> 2] | 0, HEAP32[i3 >> 2] | 0, HEAP32[i3 + 4 >> 2] | 0) | 0;
 i3 = i12;
 HEAP32[i3 >> 2] = i11;
 HEAP32[i3 + 4 >> 2] = tempRet0;
 i3 = i12;
 i11 = HEAP32[i3 + 4 >> 2] | 0;
 i9 = (HEAP32[i8 >> 2] | 0) + 496 | 0;
 i7 = HEAP32[i9 + 4 >> 2] | 0;
 if ((i11 | 0) > (i7 | 0) | ((i11 | 0) == (i7 | 0) ? (HEAP32[i3 >> 2] | 0) >>> 0 > (HEAP32[i9 >> 2] | 0) >>> 0 : 0) ? (i9 = (HEAP32[i8 >> 2] | 0) + 496 | 0, i3 = HEAP32[i9 + 4 >> 2] | 0, i7 = (HEAP32[i8 >> 2] | 0) + 488 | 0, i11 = HEAP32[i7 + 4 >> 2] | 0, (i3 | 0) > (i11 | 0) | ((i3 | 0) == (i11 | 0) ? (HEAP32[i9 >> 2] | 0) >>> 0 >= (HEAP32[i7 >> 2] | 0) >>> 0 : 0)) : 0) {
  i7 = (HEAP32[i8 >> 2] | 0) + 496 | 0;
  i20 = HEAP32[i7 >> 2] | 0;
  i21 = HEAP32[i7 + 4 >> 2] | 0;
 } else {
  i7 = i12;
  i20 = HEAP32[i7 >> 2] | 0;
  i21 = HEAP32[i7 + 4 >> 2] | 0;
 }
 i7 = i6;
 HEAP32[i7 >> 2] = i20;
 HEAP32[i7 + 4 >> 2] = i21;
 i14 = i6;
 i15 = i14;
 i16 = HEAP32[i15 >> 2] | 0;
 i17 = i14 + 4 | 0;
 i18 = i17;
 i19 = HEAP32[i18 >> 2] | 0;
 tempRet0 = i19;
 STACKTOP = i5;
 return i16 | 0;
}

function _oggskel_get_keypoint_offset(i1, i2, i3, i4, i5, i6) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 i5 = i5 | 0;
 i6 = i6 | 0;
 var i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0, i17 = 0, i18 = 0, i19 = 0, i20 = 0;
 i7 = STACKTOP;
 STACKTOP = STACKTOP + 64 | 0;
 i8 = i7 + 48 | 0;
 i9 = i7 + 44 | 0;
 i10 = i7 + 40 | 0;
 i11 = i7 + 36 | 0;
 i12 = i7 + 8 | 0;
 i13 = i7 + 32 | 0;
 i14 = i7 + 28 | 0;
 i15 = i7 + 24 | 0;
 i16 = i7 + 20 | 0;
 i17 = i7;
 i18 = i7 + 16 | 0;
 HEAP32[i9 >> 2] = i1;
 HEAP32[i10 >> 2] = i2;
 HEAP32[i11 >> 2] = i3;
 i3 = i12;
 HEAP32[i3 >> 2] = i4;
 HEAP32[i3 + 4 >> 2] = i5;
 HEAP32[i13 >> 2] = i6;
 HEAP32[i14 >> 2] = 0;
 HEAP32[i15 >> 2] = -1;
 HEAP32[i16 >> 2] = 0;
 i6 = i17;
 HEAP32[i6 >> 2] = -1;
 HEAP32[i6 + 4 >> 2] = 2147483647;
 i6 = _getter_error_check(HEAP32[i9 >> 2] | 0, HEAP32[i13 >> 2] | 0) | 0;
 HEAP32[i15 >> 2] = i6;
 if ((i6 | 0) < 0) {
  HEAP32[i8 >> 2] = HEAP32[i15 >> 2];
  i19 = HEAP32[i8 >> 2] | 0;
  STACKTOP = i7;
  return i19 | 0;
 }
 if (!(HEAP16[(HEAP32[i9 >> 2] | 0) + 116 >> 1] | 0)) {
  HEAP32[i8 >> 2] = -19;
  i19 = HEAP32[i8 >> 2] | 0;
  STACKTOP = i7;
  return i19 | 0;
 }
 if ((HEAP32[i10 >> 2] | 0) == 0 | (HEAP32[i11 >> 2] | 0) == 0) {
  HEAP32[i8 >> 2] = -1;
  i19 = HEAP32[i8 >> 2] | 0;
  STACKTOP = i7;
  return i19 | 0;
 }
 do if ((HEAPU16[HEAP32[i9 >> 2] >> 1] | 0) == 3) {
  i15 = (HEAP32[i9 >> 2] | 0) + 80 | 0;
  i6 = HEAP32[i15 + 4 >> 2] | 0;
  i5 = i12;
  i3 = HEAP32[i5 + 4 >> 2] | 0;
  if (!((i6 | 0) < (i3 | 0) | ((i6 | 0) == (i3 | 0) ? (HEAP32[i15 >> 2] | 0) >>> 0 < (HEAP32[i5 >> 2] | 0) >>> 0 : 0)) ? (i5 = i12, i15 = HEAP32[i5 + 4 >> 2] | 0, i3 = (HEAP32[i9 >> 2] | 0) + 64 | 0, i6 = HEAP32[i3 + 4 >> 2] | 0, !((i15 | 0) < (i6 | 0) | ((i15 | 0) == (i6 | 0) ? (HEAP32[i5 >> 2] | 0) >>> 0 < (HEAP32[i3 >> 2] | 0) >>> 0 : 0))) : 0) break;
  HEAP32[i8 >> 2] = -17;
  i19 = HEAP32[i8 >> 2] | 0;
  STACKTOP = i7;
  return i19 | 0;
 } while (0);
 HEAP32[i16 >> 2] = 0;
 while (1) {
  if ((HEAP32[i16 >> 2] | 0) >>> 0 >= (HEAP32[i11 >> 2] | 0) >>> 0) {
   i20 = 23;
   break;
  }
  HEAP32[i18 >> 2] = 0;
  i3 = _oggskel_vect_get_index(HEAP32[(HEAP32[i9 >> 2] | 0) + 112 >> 2] | 0, HEAP32[(HEAP32[i10 >> 2] | 0) + (HEAP32[i16 >> 2] << 2) >> 2] | 0) | 0;
  HEAP32[i14 >> 2] = i3;
  if (!i3) {
   i20 = 14;
   break;
  }
  if ((HEAPU16[HEAP32[i9 >> 2] >> 1] | 0) == 4) {
   i3 = (HEAP32[i14 >> 2] | 0) + 32 | 0;
   i5 = HEAP32[i3 + 4 >> 2] | 0;
   i6 = i12;
   i15 = HEAP32[i6 + 4 >> 2] | 0;
   if ((i5 | 0) < (i15 | 0) | ((i5 | 0) == (i15 | 0) ? (HEAP32[i3 >> 2] | 0) >>> 0 < (HEAP32[i6 >> 2] | 0) >>> 0 : 0)) {
    i20 = 18;
    break;
   }
   i6 = i12;
   i3 = HEAP32[i6 + 4 >> 2] | 0;
   i15 = (HEAP32[i14 >> 2] | 0) + 24 | 0;
   i5 = HEAP32[i15 + 4 >> 2] | 0;
   if ((i3 | 0) < (i5 | 0) | ((i3 | 0) == (i5 | 0) ? (HEAP32[i6 >> 2] | 0) >>> 0 < (HEAP32[i15 >> 2] | 0) >>> 0 : 0)) {
    i20 = 18;
    break;
   }
  }
  i15 = i12;
  HEAP32[i18 >> 2] = _get_seek_keypoint(HEAP32[i14 >> 2] | 0, HEAP32[i15 >> 2] | 0, HEAP32[i15 + 4 >> 2] | 0) | 0;
  if (HEAP32[i18 >> 2] | 0 ? (i15 = HEAP32[i18 >> 2] | 0, i6 = HEAP32[i15 + 4 >> 2] | 0, i5 = i17, i3 = HEAP32[i5 + 4 >> 2] | 0, !((i6 | 0) > (i3 | 0) | ((i6 | 0) == (i3 | 0) ? (HEAP32[i15 >> 2] | 0) >>> 0 > (HEAP32[i5 >> 2] | 0) >>> 0 : 0))) : 0) {
   i5 = HEAP32[i18 >> 2] | 0;
   i15 = HEAP32[i5 + 4 >> 2] | 0;
   i3 = i17;
   HEAP32[i3 >> 2] = HEAP32[i5 >> 2];
   HEAP32[i3 + 4 >> 2] = i15;
  }
  HEAP32[i16 >> 2] = (HEAP32[i16 >> 2] | 0) + 1;
 }
 if ((i20 | 0) == 14) {
  HEAP32[i8 >> 2] = -8;
  i19 = HEAP32[i8 >> 2] | 0;
  STACKTOP = i7;
  return i19 | 0;
 } else if ((i20 | 0) == 18) {
  HEAP32[i8 >> 2] = -17;
  i19 = HEAP32[i8 >> 2] | 0;
  STACKTOP = i7;
  return i19 | 0;
 } else if ((i20 | 0) == 23) {
  i20 = i17;
  if ((HEAP32[i20 >> 2] | 0) == -1 ? (HEAP32[i20 + 4 >> 2] | 0) == 2147483647 : 0) {
   HEAP32[i8 >> 2] = -1;
   i19 = HEAP32[i8 >> 2] | 0;
   STACKTOP = i7;
   return i19 | 0;
  } else {
   i20 = i17;
   i17 = HEAP32[i20 + 4 >> 2] | 0;
   i16 = HEAP32[i13 >> 2] | 0;
   HEAP32[i16 >> 2] = HEAP32[i20 >> 2];
   HEAP32[i16 + 4 >> 2] = i17;
   HEAP32[i8 >> 2] = 0;
   i19 = HEAP32[i8 >> 2] | 0;
   STACKTOP = i7;
   return i19 | 0;
  }
 }
 return 0;
}

function _ogg_sync_pageseek(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0, i17 = 0, i18 = 0, i19 = 0, i20 = 0, i21 = 0, i22 = 0, i23 = 0, i24 = 0, i25 = 0, i26 = 0, i27 = 0, i28 = 0, i29 = 0, i30 = 0, i31 = 0, i32 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i4 = i3;
 i5 = i1 + 12 | 0;
 i6 = HEAP32[i5 >> 2] | 0;
 i7 = (HEAP32[i1 >> 2] | 0) + i6 | 0;
 i8 = i1 + 8 | 0;
 i9 = (HEAP32[i8 >> 2] | 0) - i6 | 0;
 if ((HEAP32[i1 + 4 >> 2] | 0) <= -1) {
  i10 = 0;
  STACKTOP = i3;
  return i10 | 0;
 }
 i6 = i1 + 20 | 0;
 i11 = HEAP32[i6 >> 2] | 0;
 do if (!i11) {
  if ((i9 | 0) < 27) {
   i10 = 0;
   STACKTOP = i3;
   return i10 | 0;
  }
  if (_memcmp(i7, 2272, 4) | 0) {
   i12 = i1 + 24 | 0;
   break;
  }
  i13 = i7 + 26 | 0;
  i14 = HEAP8[i13 >> 0] | 0;
  i15 = (i14 & 255) + 27 | 0;
  if ((i9 | 0) < (i15 | 0)) {
   i10 = 0;
   STACKTOP = i3;
   return i10 | 0;
  }
  if (i14 << 24 >> 24) {
   i14 = i1 + 24 | 0;
   i16 = HEAP32[i14 >> 2] | 0;
   i17 = 0;
   do {
    i16 = i16 + (HEAPU8[i7 + (i17 + 27) >> 0] | 0) | 0;
    HEAP32[i14 >> 2] = i16;
    i17 = i17 + 1 | 0;
   } while ((i17 | 0) < (HEAPU8[i13 >> 0] | 0 | 0));
  }
  HEAP32[i6 >> 2] = i15;
  i18 = i15;
  i19 = 11;
 } else {
  i18 = i11;
  i19 = 11;
 } while (0);
 do if ((i19 | 0) == 11) {
  i11 = i1 + 24 | 0;
  if ((i18 + (HEAP32[i11 >> 2] | 0) | 0) > (i9 | 0)) {
   i10 = 0;
   STACKTOP = i3;
   return i10 | 0;
  }
  i13 = i7 + 22 | 0;
  i17 = HEAPU8[i13 >> 0] | HEAPU8[i13 + 1 >> 0] << 8 | HEAPU8[i13 + 2 >> 0] << 16 | HEAPU8[i13 + 3 >> 0] << 24;
  HEAP32[i4 >> 2] = i17;
  HEAP8[i13 >> 0] = 0;
  HEAP8[i13 + 1 >> 0] = 0;
  HEAP8[i13 + 2 >> 0] = 0;
  HEAP8[i13 + 3 >> 0] = 0;
  i16 = HEAP32[i6 >> 2] | 0;
  i14 = i7 + i16 | 0;
  i20 = HEAP32[i11 >> 2] | 0;
  i21 = i7 + 23 | 0;
  i22 = i7 + 24 | 0;
  i23 = i7 + 25 | 0;
  HEAP8[i13 >> 0] = 0;
  HEAP8[i13 + 1 >> 0] = 0;
  HEAP8[i13 + 2 >> 0] = 0;
  HEAP8[i13 + 3 >> 0] = 0;
  if ((i16 | 0) > 0) {
   i24 = 0;
   i25 = 0;
   while (1) {
    i26 = HEAP32[8 + (((HEAPU8[i7 + i25 >> 0] | 0) ^ i24 >>> 24) << 2) >> 2] ^ i24 << 8;
    i25 = i25 + 1 | 0;
    if ((i25 | 0) == (i16 | 0)) {
     i27 = i26;
     break;
    } else i24 = i26;
   }
  } else i27 = 0;
  if ((i20 | 0) > 0) {
   i24 = i27;
   i16 = 0;
   while (1) {
    i25 = HEAP32[8 + (((HEAPU8[i14 + i16 >> 0] | 0) ^ i24 >>> 24) << 2) >> 2] ^ i24 << 8;
    i16 = i16 + 1 | 0;
    if ((i16 | 0) == (i20 | 0)) {
     i28 = i25;
     break;
    } else i24 = i25;
   }
  } else i28 = i27;
  HEAP8[i13 >> 0] = i28;
  HEAP8[i21 >> 0] = i28 >>> 8;
  HEAP8[i22 >> 0] = i28 >>> 16;
  HEAP8[i23 >> 0] = i28 >>> 24;
  if (_memcmp(i4, i13, 4) | 0) {
   HEAP8[i13 >> 0] = i17;
   HEAP8[i13 + 1 >> 0] = i17 >> 8;
   HEAP8[i13 + 2 >> 0] = i17 >> 16;
   HEAP8[i13 + 3 >> 0] = i17 >> 24;
   i12 = i11;
   break;
  }
  i24 = HEAP32[i5 >> 2] | 0;
  i20 = (HEAP32[i1 >> 2] | 0) + i24 | 0;
  if (!i2) {
   i29 = HEAP32[i11 >> 2] | 0;
   i30 = HEAP32[i6 >> 2] | 0;
  } else {
   HEAP32[i2 >> 2] = i20;
   i16 = HEAP32[i6 >> 2] | 0;
   HEAP32[i2 + 4 >> 2] = i16;
   HEAP32[i2 + 8 >> 2] = i20 + i16;
   i20 = HEAP32[i11 >> 2] | 0;
   HEAP32[i2 + 12 >> 2] = i20;
   i29 = i20;
   i30 = i16;
  }
  HEAP32[i1 + 16 >> 2] = 0;
  i16 = i29 + i30 | 0;
  HEAP32[i5 >> 2] = i24 + i16;
  HEAP32[i6 >> 2] = 0;
  HEAP32[i11 >> 2] = 0;
  i10 = i16;
  STACKTOP = i3;
  return i10 | 0;
 } while (0);
 HEAP32[i6 >> 2] = 0;
 HEAP32[i12 >> 2] = 0;
 i12 = _memchr(i7 + 1 | 0, 79, i9 + -1 | 0) | 0;
 if (!i12) {
  i9 = HEAP32[i1 >> 2] | 0;
  i31 = i9;
  i32 = i9 + (HEAP32[i8 >> 2] | 0) | 0;
 } else {
  i31 = HEAP32[i1 >> 2] | 0;
  i32 = i12;
 }
 i12 = i32;
 HEAP32[i5 >> 2] = i12 - i31;
 i10 = i7 - i12 | 0;
 STACKTOP = i3;
 return i10 | 0;
}

function _auto_calc_flac(i1, i2, i3, i4) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 var i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0, i17 = 0, i18 = 0, i19 = 0;
 i5 = STACKTOP;
 STACKTOP = STACKTOP + 48 | 0;
 i6 = i5 + 8 | 0;
 i7 = i5;
 i8 = i5 + 28 | 0;
 i9 = i5 + 24 | 0;
 i10 = i5 + 20 | 0;
 i11 = i5 + 32 | 0;
 i12 = i5 + 16 | 0;
 i13 = i7;
 HEAP32[i13 >> 2] = i1;
 HEAP32[i13 + 4 >> 2] = i2;
 HEAP32[i8 >> 2] = i3;
 HEAP32[i9 >> 2] = i4;
 do if (HEAP32[(HEAP32[i8 >> 2] | 0) + 504 >> 2] | 0) {
  HEAP32[i10 >> 2] = HEAP32[(HEAP32[i8 >> 2] | 0) + 504 >> 2];
  if ((HEAPU8[HEAP32[HEAP32[i9 >> 2] >> 2] >> 0] | 0 | 0) == 255) HEAP32[(HEAP32[i10 >> 2] | 0) + 8 >> 2] = 1;
  i4 = i7;
  if ((((HEAP32[i4 >> 2] | 0) == -1 ? (HEAP32[i4 + 4 >> 2] | 0) == -1 : 0) ? (HEAPU8[HEAP32[HEAP32[i9 >> 2] >> 2] >> 0] | 0 | 0) == 255 : 0) ? (HEAP32[(HEAP32[i9 >> 2] | 0) + 4 >> 2] | 0) > 2 : 0) {
   HEAP8[i11 >> 0] = ((HEAPU8[(HEAP32[HEAP32[i9 >> 2] >> 2] | 0) + 2 >> 0] | 0) & 240) >> 4;
   do switch (HEAPU8[i11 >> 0] | 0 | 0) {
   case 0:
    {
     HEAP32[i12 >> 2] = -1;
     break;
    }
   case 1:
    {
     HEAP32[i12 >> 2] = 192;
     break;
    }
   case 2:
    {
     HEAP32[i12 >> 2] = 576;
     break;
    }
   case 3:
    {
     HEAP32[i12 >> 2] = 1152;
     break;
    }
   case 4:
    {
     HEAP32[i12 >> 2] = 2304;
     break;
    }
   case 5:
    {
     HEAP32[i12 >> 2] = 4608;
     break;
    }
   case 6:
    {
     HEAP32[i12 >> 2] = -1;
     break;
    }
   case 7:
    {
     HEAP32[i12 >> 2] = -1;
     break;
    }
   case 8:
    {
     HEAP32[i12 >> 2] = 256;
     break;
    }
   case 9:
    {
     HEAP32[i12 >> 2] = 512;
     break;
    }
   case 10:
    {
     HEAP32[i12 >> 2] = 1024;
     break;
    }
   case 11:
    {
     HEAP32[i12 >> 2] = 2048;
     break;
    }
   case 12:
    {
     HEAP32[i12 >> 2] = 4096;
     break;
    }
   case 13:
    {
     HEAP32[i12 >> 2] = 8192;
     break;
    }
   case 14:
    {
     HEAP32[i12 >> 2] = 16384;
     break;
    }
   case 15:
    {
     HEAP32[i12 >> 2] = 32768;
     break;
    }
   default:
    HEAP32[i12 >> 2] = -1;
   } while (0);
   if ((HEAP32[i12 >> 2] | 0) == -1) break;
   i4 = HEAP32[i10 >> 2] | 0;
   i3 = HEAP32[i12 >> 2] | 0;
   i2 = _i64Add(HEAP32[i4 >> 2] | 0, HEAP32[i4 + 4 >> 2] | 0, i3 | 0, ((i3 | 0) < 0) << 31 >> 31 | 0) | 0;
   i3 = i7;
   HEAP32[i3 >> 2] = i2;
   HEAP32[i3 + 4 >> 2] = tempRet0;
   break;
  }
  i3 = i7;
  if (((HEAP32[i3 >> 2] | 0) == -1 ? (HEAP32[i3 + 4 >> 2] | 0) == -1 : 0) ? (HEAP32[(HEAP32[i10 >> 2] | 0) + 8 >> 2] | 0) == 0 : 0) {
   i3 = i7;
   HEAP32[i3 >> 2] = 0;
   HEAP32[i3 + 4 >> 2] = 0;
  }
 } else {
  i3 = _malloc(16) | 0;
  HEAP32[(HEAP32[i8 >> 2] | 0) + 504 >> 2] = i3;
  if (HEAP32[(HEAP32[i8 >> 2] | 0) + 504 >> 2] | 0) {
   HEAP32[i10 >> 2] = HEAP32[(HEAP32[i8 >> 2] | 0) + 504 >> 2];
   i3 = HEAP32[i10 >> 2] | 0;
   HEAP32[i3 >> 2] = 0;
   HEAP32[i3 + 4 >> 2] = 0;
   HEAP32[(HEAP32[i10 >> 2] | 0) + 8 >> 2] = 0;
   break;
  }
  i3 = i6;
  HEAP32[i3 >> 2] = -1;
  HEAP32[i3 + 4 >> 2] = -1;
  i14 = i6;
  i15 = i14;
  i16 = HEAP32[i15 >> 2] | 0;
  i17 = i14 + 4 | 0;
  i18 = i17;
  i19 = HEAP32[i18 >> 2] | 0;
  tempRet0 = i19;
  STACKTOP = i5;
  return i16 | 0;
 } while (0);
 i8 = i7;
 i12 = HEAP32[i8 + 4 >> 2] | 0;
 i11 = HEAP32[i10 >> 2] | 0;
 HEAP32[i11 >> 2] = HEAP32[i8 >> 2];
 HEAP32[i11 + 4 >> 2] = i12;
 i12 = i7;
 i7 = HEAP32[i12 + 4 >> 2] | 0;
 i11 = i6;
 HEAP32[i11 >> 2] = HEAP32[i12 >> 2];
 HEAP32[i11 + 4 >> 2] = i7;
 i14 = i6;
 i15 = i14;
 i16 = HEAP32[i15 >> 2] | 0;
 i17 = i14 + 4 | 0;
 i18 = i17;
 i19 = HEAP32[i18 >> 2] | 0;
 tempRet0 = i19;
 STACKTOP = i5;
 return i16 | 0;
}

function _oggz_read(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 48 | 0;
 i4 = i3 + 36 | 0;
 i5 = i3 + 32 | 0;
 i6 = i3 + 28 | 0;
 i7 = i3 + 24 | 0;
 i8 = i3 + 20 | 0;
 i9 = i3 + 16 | 0;
 i10 = i3 + 12 | 0;
 i11 = i3 + 8 | 0;
 i12 = i3 + 4 | 0;
 i13 = i3;
 HEAP32[i5 >> 2] = i1;
 HEAP32[i6 >> 2] = i2;
 HEAP32[i10 >> 2] = 1;
 HEAP32[i11 >> 2] = HEAP32[i6 >> 2];
 HEAP32[i12 >> 2] = 0;
 HEAP32[i13 >> 2] = 0;
 if (!(HEAP32[i5 >> 2] | 0)) {
  HEAP32[i4 >> 2] = -2;
  i14 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i14 | 0;
 }
 if (HEAP32[HEAP32[i5 >> 2] >> 2] & 1 | 0) {
  HEAP32[i4 >> 2] = -3;
  i14 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i14 | 0;
 }
 i6 = HEAP32[(HEAP32[i5 >> 2] | 0) + 76 >> 2] | 0;
 HEAP32[i13 >> 2] = i6;
 i2 = HEAP32[i5 >> 2] | 0;
 if (i6 | 0) {
  HEAP32[i2 + 76 >> 2] = 0;
  HEAP32[i4 >> 2] = _oggz_map_return_value_to_error(HEAP32[i13 >> 2] | 0) | 0;
  i14 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i14 | 0;
 }
 HEAP32[i7 >> 2] = i2 + 112;
 HEAP32[i13 >> 2] = _oggz_read_sync(HEAP32[i5 >> 2] | 0) | 0;
 if ((HEAP32[i13 >> 2] | 0) == -18) {
  HEAP32[i4 >> 2] = HEAP32[i13 >> 2];
  i14 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i14 | 0;
 }
 while (1) {
  if (!((HEAP32[i13 >> 2] | 0) != -1 & (HEAP32[i13 >> 2] | 0) != 1 & (HEAP32[i10 >> 2] | 0) > 0 & (HEAP32[i11 >> 2] | 0) > 0)) {
   i15 = 15;
   break;
  }
  HEAP32[i9 >> 2] = (HEAP32[i11 >> 2] | 0) < 65536 ? HEAP32[i11 >> 2] | 0 : 65536;
  HEAP32[i8 >> 2] = _ogg_sync_buffer(HEAP32[i7 >> 2] | 0, HEAP32[i9 >> 2] | 0) | 0;
  HEAP32[i10 >> 2] = _oggz_io_read(HEAP32[i5 >> 2] | 0, HEAP32[i8 >> 2] | 0, HEAP32[i9 >> 2] | 0) | 0;
  if ((HEAP32[i10 >> 2] | 0) == -10) {
   i15 = 11;
   break;
  }
  if ((HEAP32[i10 >> 2] | 0) <= 0) continue;
  _ogg_sync_wrote(HEAP32[i7 >> 2] | 0, HEAP32[i10 >> 2] | 0) | 0;
  HEAP32[i11 >> 2] = (HEAP32[i11 >> 2] | 0) - (HEAP32[i10 >> 2] | 0);
  HEAP32[i12 >> 2] = (HEAP32[i12 >> 2] | 0) + (HEAP32[i10 >> 2] | 0);
  HEAP32[i13 >> 2] = _oggz_read_sync(HEAP32[i5 >> 2] | 0) | 0;
  if ((HEAP32[i13 >> 2] | 0) == -18 | (HEAP32[i13 >> 2] | 0) == -17) {
   i15 = 14;
   break;
  }
 }
 if ((i15 | 0) == 11) {
  HEAP32[i4 >> 2] = -10;
  i14 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i14 | 0;
 } else if ((i15 | 0) == 14) {
  HEAP32[i4 >> 2] = HEAP32[i13 >> 2];
  i14 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i14 | 0;
 } else if ((i15 | 0) == 15) {
  if ((HEAP32[i13 >> 2] | 0) == -1) _oggz_purge(HEAP32[i5 >> 2] | 0) | 0;
  if (HEAP32[i12 >> 2] | 0) {
   if ((HEAP32[i13 >> 2] | 0) == -404) HEAP32[i13 >> 2] = 0;
   HEAP32[(HEAP32[i5 >> 2] | 0) + 76 >> 2] = HEAP32[i13 >> 2];
   HEAP32[i4 >> 2] = HEAP32[i12 >> 2];
   i14 = HEAP32[i4 >> 2] | 0;
   STACKTOP = i3;
   return i14 | 0;
  }
  switch (HEAP32[i10 >> 2] | 0) {
  case -10:
  case -16:
   {
    HEAP32[i4 >> 2] = HEAP32[i10 >> 2];
    i14 = HEAP32[i4 >> 2] | 0;
    STACKTOP = i3;
    return i14 | 0;
   }
  default:
   {}
  }
  if ((HEAP32[i13 >> 2] | 0) == -404) {
   HEAP32[i4 >> 2] = 0;
   i14 = HEAP32[i4 >> 2] | 0;
   STACKTOP = i3;
   return i14 | 0;
  } else {
   HEAP32[i4 >> 2] = _oggz_map_return_value_to_error(HEAP32[i13 >> 2] | 0) | 0;
   i14 = HEAP32[i4 >> 2] | 0;
   STACKTOP = i3;
   return i14 | 0;
  }
 }
 return 0;
}

function _decode_fishead(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i4 = i3 + 16 | 0;
 i5 = i3 + 12 | 0;
 i6 = i3 + 8 | 0;
 i7 = i3 + 4 | 0;
 i8 = i3;
 HEAP32[i5 >> 2] = i1;
 HEAP32[i6 >> 2] = i2;
 HEAP32[i8 >> 2] = 0;
 if (!(HEAP32[i5 >> 2] | 0)) {
  HEAP32[i4 >> 2] = -2;
  i9 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i9 | 0;
 }
 if (HEAP32[i6 >> 2] | 0 ? HEAP32[HEAP32[i6 >> 2] >> 2] | 0 : 0) {
  HEAP32[i8 >> 2] = (HEAP32[HEAP32[i6 >> 2] >> 2] | 0) + 8;
  HEAP32[i8 >> 2] = _extract_uint16(HEAP32[i8 >> 2] | 0, HEAP32[i5 >> 2] | 0) | 0;
  HEAP32[i8 >> 2] = _extract_uint16(HEAP32[i8 >> 2] | 0, (HEAP32[i5 >> 2] | 0) + 2 | 0) | 0;
  HEAP32[i7 >> 2] = (HEAPU16[HEAP32[i5 >> 2] >> 1] | 0) << 16 | (HEAPU16[(HEAP32[i5 >> 2] | 0) + 2 >> 1] | 0);
  if ((HEAP32[i7 >> 2] | 0) >>> 0 > 262144) {
   HEAP32[i4 >> 2] = -11;
   i9 = HEAP32[i4 >> 2] | 0;
   STACKTOP = i3;
   return i9 | 0;
  }
  if ((HEAP32[i7 >> 2] | 0) == 262144 ? (HEAP32[(HEAP32[i6 >> 2] | 0) + 4 >> 2] | 0) != 80 : 0) {
   HEAP32[i4 >> 2] = -18;
   i9 = HEAP32[i4 >> 2] | 0;
   STACKTOP = i3;
   return i9 | 0;
  }
  if ((HEAPU16[HEAP32[i5 >> 2] >> 1] | 0 | 0) == 3) {
   if ((HEAPU16[(HEAP32[i5 >> 2] | 0) + 2 >> 1] | 0 | 0) >= 2 ? (HEAP32[(HEAP32[i6 >> 2] | 0) + 4 >> 2] | 0) != 112 : 0) {
    HEAP32[i4 >> 2] = -18;
    i9 = HEAP32[i4 >> 2] | 0;
    STACKTOP = i3;
    return i9 | 0;
   }
   if ((HEAPU16[(HEAP32[i5 >> 2] | 0) + 2 >> 1] | 0 | 0) == 0 ? (HEAP32[(HEAP32[i6 >> 2] | 0) + 4 >> 2] | 0) != 64 : 0) {
    HEAP32[i4 >> 2] = -18;
    i9 = HEAP32[i4 >> 2] | 0;
    STACKTOP = i3;
    return i9 | 0;
   }
  }
  HEAP32[i8 >> 2] = _extract_int64(HEAP32[i8 >> 2] | 0, (HEAP32[i5 >> 2] | 0) + 8 | 0) | 0;
  HEAP32[i8 >> 2] = _extract_int64(HEAP32[i8 >> 2] | 0, (HEAP32[i5 >> 2] | 0) + 16 | 0) | 0;
  HEAP32[i8 >> 2] = _extract_int64(HEAP32[i8 >> 2] | 0, (HEAP32[i5 >> 2] | 0) + 24 | 0) | 0;
  HEAP32[i8 >> 2] = _extract_int64(HEAP32[i8 >> 2] | 0, (HEAP32[i5 >> 2] | 0) + 32 | 0) | 0;
  i6 = (HEAP32[i5 >> 2] | 0) + 40 | 0;
  i2 = HEAP32[i8 >> 2] | 0;
  i1 = i6 + 20 | 0;
  do {
   HEAP8[i6 >> 0] = HEAP8[i2 >> 0] | 0;
   i6 = i6 + 1 | 0;
   i2 = i2 + 1 | 0;
  } while ((i6 | 0) < (i1 | 0));
  if ((HEAP32[i7 >> 2] | 0) >>> 0 >= 196610) {
   HEAP32[i8 >> 2] = (HEAP32[i8 >> 2] | 0) + 20;
   if ((HEAPU16[HEAP32[i5 >> 2] >> 1] | 0 | 0) == 3) {
    HEAP32[i8 >> 2] = _extract_int64(HEAP32[i8 >> 2] | 0, (HEAP32[i5 >> 2] | 0) + 64 | 0) | 0;
    HEAP32[i8 >> 2] = _extract_int64(HEAP32[i8 >> 2] | 0, (HEAP32[i5 >> 2] | 0) + 72 | 0) | 0;
    HEAP32[i8 >> 2] = _extract_int64(HEAP32[i8 >> 2] | 0, (HEAP32[i5 >> 2] | 0) + 80 | 0) | 0;
    HEAP32[i8 >> 2] = _extract_int64(HEAP32[i8 >> 2] | 0, (HEAP32[i5 >> 2] | 0) + 88 | 0) | 0;
   }
   HEAP32[i8 >> 2] = _extract_int64(HEAP32[i8 >> 2] | 0, (HEAP32[i5 >> 2] | 0) + 96 | 0) | 0;
   _extract_int64(HEAP32[i8 >> 2] | 0, (HEAP32[i5 >> 2] | 0) + 104 | 0) | 0;
  }
  HEAP32[i4 >> 2] = 1;
  i9 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i9 | 0;
 }
 HEAP32[i4 >> 2] = -13;
 i9 = HEAP32[i4 >> 2] | 0;
 STACKTOP = i3;
 return i9 | 0;
}

function _oggz_read_deliver_packet(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i3 = i2 + 28 | 0;
 i4 = i2 + 24 | 0;
 i5 = i2 + 20 | 0;
 i6 = i2 + 8 | 0;
 i7 = i2;
 i8 = i2 + 16 | 0;
 HEAP32[i4 >> 2] = i1;
 HEAP32[i5 >> 2] = HEAP32[i4 >> 2];
 i4 = (HEAP32[i5 >> 2] | 0) + 32 | 0;
 if ((HEAP32[i4 >> 2] | 0) == -1 ? (HEAP32[i4 + 4 >> 2] | 0) == -1 : 0) {
  HEAP32[i3 >> 2] = 0;
  i9 = HEAP32[i3 >> 2] | 0;
  STACKTOP = i2;
  return i9 | 0;
 }
 i4 = (HEAP32[(HEAP32[i5 >> 2] | 0) + 60 >> 2] | 0) + 424 | 0;
 i1 = HEAP32[i4 + 4 >> 2] | 0;
 i10 = i6;
 HEAP32[i10 >> 2] = HEAP32[i4 >> 2];
 HEAP32[i10 + 4 >> 2] = i1;
 i1 = (HEAP32[(HEAP32[i5 >> 2] | 0) + 60 >> 2] | 0) + 416 | 0;
 i10 = HEAP32[i1 + 4 >> 2] | 0;
 i4 = i7;
 HEAP32[i4 >> 2] = HEAP32[i1 >> 2];
 HEAP32[i4 + 4 >> 2] = i10;
 i10 = (HEAP32[i5 >> 2] | 0) + 32 | 0;
 i4 = HEAP32[i10 + 4 >> 2] | 0;
 i1 = (HEAP32[(HEAP32[i5 >> 2] | 0) + 60 >> 2] | 0) + 424 | 0;
 HEAP32[i1 >> 2] = HEAP32[i10 >> 2];
 HEAP32[i1 + 4 >> 2] = i4;
 i4 = (HEAP32[i5 >> 2] | 0) + 32 | 0;
 i1 = _oggz_get_unit(HEAP32[(HEAP32[i5 >> 2] | 0) + 64 >> 2] | 0, HEAP32[(HEAP32[i5 >> 2] | 0) + 68 >> 2] | 0, HEAP32[i4 >> 2] | 0, HEAP32[i4 + 4 >> 2] | 0) | 0;
 i4 = (HEAP32[(HEAP32[i5 >> 2] | 0) + 60 >> 2] | 0) + 416 | 0;
 HEAP32[i4 >> 2] = i1;
 HEAP32[i4 + 4 >> 2] = tempRet0;
 i4 = HEAP32[i5 >> 2] | 0;
 if (HEAP32[(HEAP32[(HEAP32[i5 >> 2] | 0) + 56 >> 2] | 0) + 468 >> 2] | 0) {
  i1 = FUNCTION_TABLE_iiiii[HEAP32[(HEAP32[i4 + 56 >> 2] | 0) + 468 >> 2] & 15](HEAP32[(HEAP32[i5 >> 2] | 0) + 64 >> 2] | 0, HEAP32[i5 >> 2] | 0, HEAP32[(HEAP32[i5 >> 2] | 0) + 68 >> 2] | 0, HEAP32[(HEAP32[(HEAP32[i5 >> 2] | 0) + 56 >> 2] | 0) + 472 >> 2] | 0) | 0;
  HEAP32[i8 >> 2] = i1;
  if ((i1 | 0) < 0 ? (HEAP32[(HEAP32[(HEAP32[i5 >> 2] | 0) + 64 >> 2] | 0) + 76 >> 2] = HEAP32[i8 >> 2], (HEAP32[i8 >> 2] | 0) == -1) : 0) {
   HEAP32[i3 >> 2] = -1;
   i9 = HEAP32[i3 >> 2] | 0;
   STACKTOP = i2;
   return i9 | 0;
  }
 } else if ((HEAP32[(HEAP32[i4 + 60 >> 2] | 0) + 396 >> 2] | 0 ? (i4 = FUNCTION_TABLE_iiiii[HEAP32[(HEAP32[(HEAP32[i5 >> 2] | 0) + 60 >> 2] | 0) + 396 >> 2] & 15](HEAP32[(HEAP32[i5 >> 2] | 0) + 64 >> 2] | 0, HEAP32[i5 >> 2] | 0, HEAP32[(HEAP32[i5 >> 2] | 0) + 68 >> 2] | 0, HEAP32[(HEAP32[(HEAP32[i5 >> 2] | 0) + 60 >> 2] | 0) + 400 >> 2] | 0) | 0, HEAP32[i8 >> 2] = i4, (i4 | 0) < 0) : 0) ? (HEAP32[(HEAP32[(HEAP32[i5 >> 2] | 0) + 64 >> 2] | 0) + 76 >> 2] = HEAP32[i8 >> 2], (HEAP32[i8 >> 2] | 0) == -1) : 0) {
  HEAP32[i3 >> 2] = -1;
  i9 = HEAP32[i3 >> 2] | 0;
  STACKTOP = i2;
  return i9 | 0;
 }
 i8 = i6;
 i6 = HEAP32[i8 + 4 >> 2] | 0;
 i4 = (HEAP32[(HEAP32[i5 >> 2] | 0) + 60 >> 2] | 0) + 424 | 0;
 HEAP32[i4 >> 2] = HEAP32[i8 >> 2];
 HEAP32[i4 + 4 >> 2] = i6;
 i6 = i7;
 i7 = HEAP32[i6 + 4 >> 2] | 0;
 i4 = (HEAP32[(HEAP32[i5 >> 2] | 0) + 60 >> 2] | 0) + 416 | 0;
 HEAP32[i4 >> 2] = HEAP32[i6 >> 2];
 HEAP32[i4 + 4 >> 2] = i7;
 _oggz_read_free_pbuffer_entry(HEAP32[i5 >> 2] | 0);
 HEAP32[i3 >> 2] = 1;
 i9 = HEAP32[i3 >> 2] | 0;
 STACKTOP = i2;
 return i9 | 0;
}

function _auto_calc_theora(i1, i2, i3, i4) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 var i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0;
 i5 = STACKTOP;
 STACKTOP = STACKTOP + 48 | 0;
 i6 = i5 + 8 | 0;
 i7 = i5;
 i8 = i5 + 32 | 0;
 i9 = i5 + 28 | 0;
 i10 = i5 + 24 | 0;
 i11 = i5 + 20 | 0;
 i12 = i5 + 36 | 0;
 i13 = i5 + 16 | 0;
 i14 = i7;
 HEAP32[i14 >> 2] = i1;
 HEAP32[i14 + 4 >> 2] = i2;
 HEAP32[i8 >> 2] = i3;
 HEAP32[i9 >> 2] = i4;
 if (!(HEAP32[(HEAP32[i9 >> 2] | 0) + 4 >> 2] | 0)) i15 = 64; else i15 = HEAPU8[HEAP32[HEAP32[i9 >> 2] >> 2] >> 0] | 0;
 HEAP8[i12 >> 0] = i15;
 HEAP32[i13 >> 2] = HEAP32[(HEAP32[i8 >> 2] | 0) + 504 >> 2];
 L4 : do if (!((HEAPU8[i12 >> 0] | 0) & 128 | 0)) {
  i15 = i7;
  i9 = HEAP32[i15 + 4 >> 2] | 0;
  if ((i9 | 0) > -1 | (i9 | 0) == -1 & (HEAP32[i15 >> 2] | 0) >>> 0 > 4294967295) {
   HEAP32[HEAP32[i13 >> 2] >> 2] = 1;
   i15 = i7;
   i9 = HEAP32[i15 + 4 >> 2] | 0;
   i4 = i6;
   HEAP32[i4 >> 2] = HEAP32[i15 >> 2];
   HEAP32[i4 + 4 >> 2] = i9;
   break;
  }
  i9 = (HEAP32[i8 >> 2] | 0) + 488 | 0;
  i4 = HEAP32[i13 >> 2] | 0;
  if ((HEAP32[i9 >> 2] | 0) == -1 ? (HEAP32[i9 + 4 >> 2] | 0) == -1 : 0) {
   HEAP32[i4 >> 2] = 1;
   i9 = i6;
   HEAP32[i9 >> 2] = -1;
   HEAP32[i9 + 4 >> 2] = -1;
   break;
  }
  if (!(HEAP32[i4 >> 2] | 0)) {
   HEAP32[HEAP32[i13 >> 2] >> 2] = 1;
   i4 = i6;
   HEAP32[i4 >> 2] = -1;
   HEAP32[i4 + 4 >> 2] = -1;
   break;
  }
  i4 = HEAP32[i8 >> 2] | 0;
  if ((HEAPU8[i12 >> 0] | 0) & 64 | 0) {
   i9 = i4 + 488 | 0;
   i15 = _i64Add(HEAP32[i9 >> 2] | 0, HEAP32[i9 + 4 >> 2] | 0, 1, 0) | 0;
   i9 = i6;
   HEAP32[i9 >> 2] = i15;
   HEAP32[i9 + 4 >> 2] = tempRet0;
   break;
  } else {
   HEAP32[i11 >> 2] = HEAP32[i4 + 408 >> 2];
   i4 = (HEAP32[i8 >> 2] | 0) + 488 | 0;
   i9 = _bitshift64Ashr(HEAP32[i4 >> 2] | 0, HEAP32[i4 + 4 >> 2] | 0, HEAP32[i11 >> 2] | 0) | 0;
   HEAP32[i10 >> 2] = i9;
   i9 = (HEAP32[i8 >> 2] | 0) + 488 | 0;
   i4 = (1 << HEAP32[i11 >> 2]) - 1 | 0;
   i15 = _i64Add(HEAP32[i9 >> 2] & i4 | 0, HEAP32[i9 + 4 >> 2] & ((i4 | 0) < 0) << 31 >> 31 | 0, 1, 0) | 0;
   i4 = HEAP32[i10 >> 2] | 0;
   i9 = _i64Add(i4 | 0, ((i4 | 0) < 0) << 31 >> 31 | 0, i15 | 0, tempRet0 | 0) | 0;
   HEAP32[i10 >> 2] = i9;
   i9 = HEAP32[i10 >> 2] | 0;
   i15 = _bitshift64Shl(i9 | 0, ((i9 | 0) < 0) << 31 >> 31 | 0, HEAP32[i11 >> 2] | 0) | 0;
   i9 = i6;
   HEAP32[i9 >> 2] = i15;
   HEAP32[i9 + 4 >> 2] = tempRet0;
   break;
  }
 } else {
  do if (!(HEAP32[i13 >> 2] | 0)) {
   i9 = _malloc(4) | 0;
   HEAP32[(HEAP32[i8 >> 2] | 0) + 504 >> 2] = i9;
   if (!(HEAP32[(HEAP32[i8 >> 2] | 0) + 504 >> 2] | 0)) {
    i9 = i6;
    HEAP32[i9 >> 2] = -1;
    HEAP32[i9 + 4 >> 2] = -1;
    break L4;
   } else {
    HEAP32[i13 >> 2] = HEAP32[(HEAP32[i8 >> 2] | 0) + 504 >> 2];
    break;
   }
  } while (0);
  HEAP32[HEAP32[i13 >> 2] >> 2] = 0;
  i9 = i6;
  HEAP32[i9 >> 2] = 0;
  HEAP32[i9 + 4 >> 2] = 0;
 } while (0);
 i13 = i6;
 tempRet0 = HEAP32[i13 + 4 >> 2] | 0;
 STACKTOP = i5;
 return HEAP32[i13 >> 2] | 0;
}

function _bq_append(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0, i17 = 0, i18 = 0, i19 = 0, i20 = 0, i21 = 0, i22 = 0, i23 = 0, i24 = 0, i25 = 0, i26 = 0, i27 = 0, i28 = 0, i29 = 0, i30 = 0, i31 = 0, i32 = 0, i33 = 0, i34 = 0, i35 = 0, i36 = 0;
 i4 = i1 + 4 | 0;
 i5 = HEAP32[i4 >> 2] | 0;
 i6 = i1 + 8 | 0;
 do if ((i5 | 0) == (HEAP32[i6 >> 2] | 0)) if (!i5) i7 = 0; else {
  i8 = i1 + 16 | 0;
  i9 = HEAP32[i1 >> 2] | 0;
  i10 = i5;
  i11 = 0;
  while (1) {
   i12 = i9 + (i11 * 24 | 0) + 8 | 0;
   i13 = _i64Add(HEAP32[i9 + (i11 * 24 | 0) + 16 >> 2] | 0, 0, HEAP32[i12 >> 2] | 0, HEAP32[i12 + 4 >> 2] | 0) | 0;
   i12 = tempRet0;
   i14 = i8;
   i15 = HEAP32[i14 + 4 >> 2] | 0;
   if (!((i12 | 0) < (i15 | 0) | ((i12 | 0) == (i15 | 0) ? i13 >>> 0 < (HEAP32[i14 >> 2] | 0) >>> 0 : 0))) {
    i16 = i10;
    i17 = i9;
    i18 = i11;
    i19 = 6;
    break;
   }
   _free(HEAP32[i9 + (i11 * 24 | 0) >> 2] | 0);
   i14 = HEAP32[i1 >> 2] | 0;
   HEAP32[i14 + (i11 * 24 | 0) >> 2] = 0;
   i13 = i11 + 1 | 0;
   i15 = HEAP32[i4 >> 2] | 0;
   if (i13 >>> 0 < i15 >>> 0) {
    i9 = i14;
    i10 = i15;
    i11 = i13;
   } else {
    i20 = i15;
    i21 = i14;
    i22 = i13;
    break;
   }
  }
  if ((i19 | 0) == 6) if (!i18) {
   i7 = i16;
   break;
  } else {
   i20 = i16;
   i21 = i17;
   i22 = i18;
  }
  i11 = i20 - i22 | 0;
  HEAP32[i4 >> 2] = i11;
  _memmove(i21 | 0, i21 + (i22 * 24 | 0) | 0, i11 * 24 | 0) | 0;
  i7 = HEAP32[i4 >> 2] | 0;
 } else i7 = i5; while (0);
 if ((i7 | 0) == (HEAP32[i6 >> 2] | 0)) {
  i5 = i7 + 8 | 0;
  HEAP32[i6 >> 2] = i5;
  HEAP32[i1 >> 2] = _realloc(HEAP32[i1 >> 2] | 0, i5 * 24 | 0) | 0;
  i23 = HEAP32[i4 >> 2] | 0;
 } else i23 = i7;
 if (!i23) {
  i7 = i1 + 16 | 0;
  i24 = HEAP32[i1 >> 2] | 0;
  i25 = HEAP32[i7 >> 2] | 0;
  i26 = HEAP32[i7 + 4 >> 2] | 0;
  i27 = i24 + (i23 * 24 | 0) + 8 | 0;
  i28 = i27;
  i29 = i28;
  HEAP32[i29 >> 2] = i25;
  i30 = i28 + 4 | 0;
  i31 = i30;
  HEAP32[i31 >> 2] = i26;
  i32 = i24 + (i23 * 24 | 0) + 16 | 0;
  HEAP32[i32 >> 2] = i3;
  i33 = _malloc(i3) | 0;
  i34 = i24 + (i23 * 24 | 0) | 0;
  HEAP32[i34 >> 2] = i33;
  _memcpy(i33 | 0, i2 | 0, i3 | 0) | 0;
  i35 = HEAP32[i4 >> 2] | 0;
  i36 = i35 + 1 | 0;
  HEAP32[i4 >> 2] = i36;
  return;
 } else {
  i7 = i23 + -1 | 0;
  i5 = HEAP32[i1 >> 2] | 0;
  i1 = i5 + (i7 * 24 | 0) + 8 | 0;
  i6 = _i64Add(HEAP32[i5 + (i7 * 24 | 0) + 16 >> 2] | 0, 0, HEAP32[i1 >> 2] | 0, HEAP32[i1 + 4 >> 2] | 0) | 0;
  i24 = i5;
  i25 = i6;
  i26 = tempRet0;
  i27 = i24 + (i23 * 24 | 0) + 8 | 0;
  i28 = i27;
  i29 = i28;
  HEAP32[i29 >> 2] = i25;
  i30 = i28 + 4 | 0;
  i31 = i30;
  HEAP32[i31 >> 2] = i26;
  i32 = i24 + (i23 * 24 | 0) + 16 | 0;
  HEAP32[i32 >> 2] = i3;
  i33 = _malloc(i3) | 0;
  i34 = i24 + (i23 * 24 | 0) | 0;
  HEAP32[i34 >> 2] = i33;
  _memcpy(i33 | 0, i2 | 0, i3 | 0) | 0;
  i35 = HEAP32[i4 >> 2] | 0;
  i36 = i35 + 1 | 0;
  HEAP32[i4 >> 2] = i36;
  return;
 }
}

function _oggz_metric_dirac(i1, i2, i3, i4, i5) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 i5 = i5 | 0;
 var i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0, i17 = 0, i18 = 0, i19 = 0, i20 = 0, i21 = 0, i22 = 0, i23 = 0;
 i6 = STACKTOP;
 STACKTOP = STACKTOP + 80 | 0;
 i7 = i6 + 40 | 0;
 i8 = i6 + 64 | 0;
 i9 = i6 + 60 | 0;
 i10 = i6 + 32 | 0;
 i11 = i6 + 52 | 0;
 i12 = i6 + 24 | 0;
 i13 = i6 + 16 | 0;
 i14 = i6 + 48 | 0;
 i15 = i6 + 68 | 0;
 i16 = i6 + 8 | 0;
 i17 = i6;
 HEAP32[i8 >> 2] = i1;
 HEAP32[i9 >> 2] = i2;
 i2 = i10;
 HEAP32[i2 >> 2] = i3;
 HEAP32[i2 + 4 >> 2] = i4;
 HEAP32[i6 + 56 >> 2] = i5;
 HEAP32[i11 >> 2] = _oggz_get_stream(HEAP32[i8 >> 2] | 0, HEAP32[i9 >> 2] | 0) | 0;
 if (!(HEAP32[i11 >> 2] | 0)) {
  i9 = i7;
  HEAP32[i9 >> 2] = -1;
  HEAP32[i9 + 4 >> 2] = -1;
  i18 = i7;
  i19 = i18;
  i20 = HEAP32[i19 >> 2] | 0;
  i21 = i18 + 4 | 0;
  i22 = i21;
  i23 = HEAP32[i22 >> 2] | 0;
  tempRet0 = i23;
  STACKTOP = i6;
  return i20 | 0;
 } else {
  i9 = i10;
  i8 = _bitshift64Ashr(HEAP32[i9 >> 2] | 0, HEAP32[i9 + 4 >> 2] | 0, HEAP32[(HEAP32[i11 >> 2] | 0) + 408 >> 2] | 0) | 0;
  i9 = i12;
  HEAP32[i9 >> 2] = i8;
  HEAP32[i9 + 4 >> 2] = tempRet0;
  i9 = i10;
  i10 = HEAP32[i9 >> 2] | 0;
  i8 = HEAP32[i9 + 4 >> 2] | 0;
  i9 = i12;
  i5 = _bitshift64Shl(HEAP32[i9 >> 2] | 0, HEAP32[i9 + 4 >> 2] | 0, HEAP32[(HEAP32[i11 >> 2] | 0) + 408 >> 2] | 0) | 0;
  i9 = _i64Subtract(i10 | 0, i8 | 0, i5 | 0, tempRet0 | 0) | 0;
  i5 = i13;
  HEAP32[i5 >> 2] = i9;
  HEAP32[i5 + 4 >> 2] = tempRet0;
  i5 = i12;
  i12 = i13;
  i9 = _i64Add(HEAP32[i5 >> 2] | 0, HEAP32[i5 + 4 >> 2] | 0, HEAP32[i12 >> 2] | 0, HEAP32[i12 + 4 >> 2] | 0) | 0;
  i12 = _bitshift64Ashr(i9 | 0, tempRet0 | 0, 9) | 0;
  HEAP32[i14 >> 2] = i12;
  i12 = i13;
  i13 = _bitshift64Ashr(HEAP32[i12 >> 2] | 0, HEAP32[i12 + 4 >> 2] | 0, 9) | 0;
  HEAP16[i15 >> 1] = i13;
  i13 = _i64Subtract(HEAP32[i14 >> 2] | 0, 0, HEAPU16[i15 >> 1] | 0 | 0, 0) | 0;
  i15 = i16;
  HEAP32[i15 >> 2] = i13;
  HEAP32[i15 + 4 >> 2] = tempRet0;
  i15 = i16;
  i16 = (HEAP32[i11 >> 2] | 0) + 384 | 0;
  i13 = ___muldi3(HEAP32[i15 >> 2] | 0, HEAP32[i15 + 4 >> 2] | 0, HEAP32[i16 >> 2] | 0, HEAP32[i16 + 4 >> 2] | 0) | 0;
  i16 = (HEAP32[i11 >> 2] | 0) + 376 | 0;
  i11 = ___divdi3(i13 | 0, tempRet0 | 0, HEAP32[i16 >> 2] | 0, HEAP32[i16 + 4 >> 2] | 0) | 0;
  i16 = i17;
  HEAP32[i16 >> 2] = i11;
  HEAP32[i16 + 4 >> 2] = tempRet0;
  i16 = i17;
  i17 = HEAP32[i16 + 4 >> 2] | 0;
  i11 = i7;
  HEAP32[i11 >> 2] = HEAP32[i16 >> 2];
  HEAP32[i11 + 4 >> 2] = i17;
  i18 = i7;
  i19 = i18;
  i20 = HEAP32[i19 >> 2] | 0;
  i21 = i18 + 4 | 0;
  i22 = i21;
  i23 = HEAP32[i22 >> 2] | 0;
  tempRet0 = i23;
  STACKTOP = i6;
  return i20 | 0;
 }
 return 0;
}

function _bq_read(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0, i17 = 0, i18 = 0, i19 = 0, i20 = 0, i21 = 0, i22 = 0, i23 = 0, i24 = 0, i25 = 0, i26 = 0, i27 = 0, i28 = 0, i29 = 0, i30 = 0, i31 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i5 = i4 + 16 | 0;
 i6 = i4;
 i7 = i1 + 4 | 0;
 i8 = HEAP32[i7 >> 2] | 0;
 i9 = (i8 | 0) == 0;
 i10 = i1 + 16 | 0;
 i11 = i10;
 i12 = HEAP32[i11 >> 2] | 0;
 i13 = HEAP32[i11 + 4 >> 2] | 0;
 if (i9) {
  i14 = i12;
  i15 = i13;
 } else {
  i11 = i8 + -1 | 0;
  i16 = HEAP32[i1 >> 2] | 0;
  i17 = i16 + (i11 * 24 | 0) + 8 | 0;
  i18 = _i64Add(HEAP32[i16 + (i11 * 24 | 0) + 16 >> 2] | 0, 0, HEAP32[i17 >> 2] | 0, HEAP32[i17 + 4 >> 2] | 0) | 0;
  i14 = i18;
  i15 = tempRet0;
 }
 i18 = _i64Subtract(i14 | 0, i15 | 0, i12 | 0, i13 | 0) | 0;
 i15 = tempRet0;
 if ((i15 | 0) < 0 | (i15 | 0) == 0 & i18 >>> 0 < i3 >>> 0) {
  HEAP32[i6 >> 2] = i3;
  i18 = i6 + 8 | 0;
  HEAP32[i18 >> 2] = i12;
  HEAP32[i18 + 4 >> 2] = i13;
  _printf(2799, i6) | 0;
  i19 = -1;
  STACKTOP = i4;
  return i19 | 0;
 }
 L8 : do if (i9) {
  i20 = i12;
  i21 = i13;
 } else {
  i6 = i13;
  i18 = i12;
  i15 = i8;
  i14 = 0;
  i17 = 0;
  i11 = i3;
  while (1) {
   i16 = HEAP32[i1 >> 2] | 0;
   i22 = i16 + (i14 * 24 | 0) + 8 | 0;
   i23 = HEAP32[i22 >> 2] | 0;
   i24 = HEAP32[i22 + 4 >> 2] | 0;
   i22 = HEAP32[i16 + (i14 * 24 | 0) + 16 >> 2] | 0;
   i25 = _i64Add(i22 | 0, 0, i23 | 0, i24 | 0) | 0;
   i26 = tempRet0;
   if ((i26 | 0) < (i6 | 0) | (i26 | 0) == (i6 | 0) & i25 >>> 0 < i18 >>> 0) {
    i27 = i15;
    i28 = i18;
    i29 = i6;
    i30 = i17;
    i31 = i11;
   } else {
    i25 = _i64Subtract(i18 | 0, i6 | 0, i23 | 0, i24 | 0) | 0;
    i24 = i22 - i25 | 0;
    i22 = i24 >>> 0 > i11 >>> 0 ? i11 : i24;
    _memcpy(i2 + i17 | 0, (HEAP32[i16 + (i14 * 24 | 0) >> 2] | 0) + i25 | 0, i22 | 0) | 0;
    i25 = i10;
    i16 = _i64Add(HEAP32[i25 >> 2] | 0, HEAP32[i25 + 4 >> 2] | 0, i22 | 0, 0) | 0;
    i25 = tempRet0;
    i24 = i10;
    HEAP32[i24 >> 2] = i16;
    HEAP32[i24 + 4 >> 2] = i25;
    if ((i11 | 0) == (i22 | 0)) {
     i19 = 0;
     break;
    }
    i27 = HEAP32[i7 >> 2] | 0;
    i28 = i16;
    i29 = i25;
    i30 = i22 + i17 | 0;
    i31 = i11 - i22 | 0;
   }
   i14 = i14 + 1 | 0;
   if (i14 >>> 0 >= i27 >>> 0) {
    i20 = i28;
    i21 = i29;
    break L8;
   } else {
    i6 = i29;
    i18 = i28;
    i15 = i27;
    i17 = i30;
    i11 = i31;
   }
  }
  STACKTOP = i4;
  return i19 | 0;
 } while (0);
 HEAP32[i5 >> 2] = i3;
 i3 = i5 + 8 | 0;
 HEAP32[i3 >> 2] = i20;
 HEAP32[i3 + 4 >> 2] = i21;
 _printf(2834, i5) | 0;
 i19 = -1;
 STACKTOP = i4;
 return i19 | 0;
}

function _oggz_metric_default_granuleshift(i1, i2, i3, i4, i5) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 i5 = i5 | 0;
 var i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0, i17 = 0, i18 = 0, i19 = 0, i20 = 0;
 i6 = STACKTOP;
 STACKTOP = STACKTOP + 64 | 0;
 i7 = i6 + 32 | 0;
 i8 = i6 + 52 | 0;
 i9 = i6 + 48 | 0;
 i10 = i6 + 24 | 0;
 i11 = i6 + 40 | 0;
 i12 = i6 + 16 | 0;
 i13 = i6 + 8 | 0;
 i14 = i6;
 HEAP32[i8 >> 2] = i1;
 HEAP32[i9 >> 2] = i2;
 i2 = i10;
 HEAP32[i2 >> 2] = i3;
 HEAP32[i2 + 4 >> 2] = i4;
 HEAP32[i6 + 44 >> 2] = i5;
 HEAP32[i11 >> 2] = _oggz_get_stream(HEAP32[i8 >> 2] | 0, HEAP32[i9 >> 2] | 0) | 0;
 if (!(HEAP32[i11 >> 2] | 0)) {
  i9 = i7;
  HEAP32[i9 >> 2] = -1;
  HEAP32[i9 + 4 >> 2] = -1;
  i15 = i7;
  i16 = i15;
  i17 = HEAP32[i16 >> 2] | 0;
  i18 = i15 + 4 | 0;
  i19 = i18;
  i20 = HEAP32[i19 >> 2] | 0;
  tempRet0 = i20;
  STACKTOP = i6;
  return i17 | 0;
 }
 i9 = i10;
 i8 = _bitshift64Ashr(HEAP32[i9 >> 2] | 0, HEAP32[i9 + 4 >> 2] | 0, HEAP32[(HEAP32[i11 >> 2] | 0) + 408 >> 2] | 0) | 0;
 i9 = i12;
 HEAP32[i9 >> 2] = i8;
 HEAP32[i9 + 4 >> 2] = tempRet0;
 i9 = i10;
 i8 = HEAP32[i9 >> 2] | 0;
 i5 = HEAP32[i9 + 4 >> 2] | 0;
 i9 = i12;
 i4 = _bitshift64Shl(HEAP32[i9 >> 2] | 0, HEAP32[i9 + 4 >> 2] | 0, HEAP32[(HEAP32[i11 >> 2] | 0) + 408 >> 2] | 0) | 0;
 i9 = _i64Subtract(i8 | 0, i5 | 0, i4 | 0, tempRet0 | 0) | 0;
 i4 = i13;
 HEAP32[i4 >> 2] = i9;
 HEAP32[i4 + 4 >> 2] = tempRet0;
 i4 = i12;
 i12 = i13;
 i13 = _i64Add(HEAP32[i4 >> 2] | 0, HEAP32[i4 + 4 >> 2] | 0, HEAP32[i12 >> 2] | 0, HEAP32[i12 + 4 >> 2] | 0) | 0;
 i12 = i10;
 HEAP32[i12 >> 2] = i13;
 HEAP32[i12 + 4 >> 2] = tempRet0;
 i12 = i10;
 i13 = HEAP32[i12 + 4 >> 2] | 0;
 if ((i13 | 0) > 0 | (i13 | 0) == 0 & (HEAP32[i12 >> 2] | 0) >>> 0 > 0) {
  i12 = (HEAP32[i11 >> 2] | 0) + 392 | 0;
  i13 = i10;
  i4 = _i64Subtract(HEAP32[i13 >> 2] | 0, HEAP32[i13 + 4 >> 2] | 0, HEAP32[i12 >> 2] | 0, HEAP32[i12 + 4 >> 2] | 0) | 0;
  i12 = i10;
  HEAP32[i12 >> 2] = i4;
  HEAP32[i12 + 4 >> 2] = tempRet0;
 }
 i12 = i10;
 i10 = (HEAP32[i11 >> 2] | 0) + 384 | 0;
 i4 = ___muldi3(HEAP32[i12 >> 2] | 0, HEAP32[i12 + 4 >> 2] | 0, HEAP32[i10 >> 2] | 0, HEAP32[i10 + 4 >> 2] | 0) | 0;
 i10 = (HEAP32[i11 >> 2] | 0) + 376 | 0;
 i11 = ___divdi3(i4 | 0, tempRet0 | 0, HEAP32[i10 >> 2] | 0, HEAP32[i10 + 4 >> 2] | 0) | 0;
 i10 = i14;
 HEAP32[i10 >> 2] = i11;
 HEAP32[i10 + 4 >> 2] = tempRet0;
 i10 = i14;
 i14 = HEAP32[i10 + 4 >> 2] | 0;
 i11 = i7;
 HEAP32[i11 >> 2] = HEAP32[i10 >> 2];
 HEAP32[i11 + 4 >> 2] = i14;
 i15 = i7;
 i16 = i15;
 i17 = HEAP32[i16 >> 2] | 0;
 i18 = i15 + 4 | 0;
 i19 = i18;
 i20 = HEAP32[i19 >> 2] | 0;
 tempRet0 = i20;
 STACKTOP = i6;
 return i17 | 0;
}

function _dirac_parse_info(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 48 | 0;
 i5 = i4 + 44 | 0;
 i6 = i4 + 40 | 0;
 i7 = i4 + 36 | 0;
 i8 = i4 + 32 | 0;
 i9 = i4 + 16 | 0;
 i10 = i4 + 8 | 0;
 i11 = i4 + 4 | 0;
 i12 = i4;
 HEAP32[i6 >> 2] = i1;
 HEAP32[i7 >> 2] = i2;
 HEAP32[i8 >> 2] = i3;
 _dirac_bs_init(i9, HEAP32[i7 >> 2] | 0, HEAP32[i8 >> 2] | 0);
 _dirac_bs_skip(i9, 104);
 i8 = _dirac_uint(i9) | 0;
 HEAP32[HEAP32[i6 >> 2] >> 2] = i8;
 i8 = _dirac_uint(i9) | 0;
 HEAP32[(HEAP32[i6 >> 2] | 0) + 4 >> 2] = i8;
 i8 = _dirac_uint(i9) | 0;
 HEAP32[(HEAP32[i6 >> 2] | 0) + 8 >> 2] = i8;
 i8 = _dirac_uint(i9) | 0;
 HEAP32[(HEAP32[i6 >> 2] | 0) + 12 >> 2] = i8;
 i8 = _dirac_uint(i9) | 0;
 HEAP32[i10 >> 2] = i8;
 HEAP32[(HEAP32[i6 >> 2] | 0) + 20 >> 2] = i8;
 if ((HEAP32[i10 >> 2] | 0) >>> 0 >= 17) {
  HEAP32[i5 >> 2] = -1;
  i13 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i13 | 0;
 }
 HEAP32[(HEAP32[i6 >> 2] | 0) + 24 >> 2] = HEAP32[1548 + (HEAP32[i10 >> 2] << 3) >> 2];
 HEAP32[(HEAP32[i6 >> 2] | 0) + 28 >> 2] = HEAP32[1548 + (HEAP32[i10 >> 2] << 3) + 4 >> 2];
 if (_dirac_bool(i9) | 0) {
  i8 = _dirac_uint(i9) | 0;
  HEAP32[(HEAP32[i6 >> 2] | 0) + 24 >> 2] = i8;
  i8 = _dirac_uint(i9) | 0;
  HEAP32[(HEAP32[i6 >> 2] | 0) + 28 >> 2] = i8;
 }
 if (_dirac_bool(i9) | 0) {
  i8 = _dirac_uint(i9) | 0;
  HEAP32[(HEAP32[i6 >> 2] | 0) + 16 >> 2] = i8;
 }
 do if (_dirac_bool(i9) | 0) {
  HEAP32[i11 >> 2] = _dirac_uint(i9) | 0;
  if ((HEAP32[i11 >> 2] | 0) < 2) {
   HEAP32[(HEAP32[i6 >> 2] | 0) + 40 >> 2] = HEAP32[i11 >> 2];
   break;
  } else {
   HEAP32[(HEAP32[i6 >> 2] | 0) + 40 >> 2] = 0;
   break;
  }
 } else HEAP32[(HEAP32[i6 >> 2] | 0) + 40 >> 2] = HEAP32[1684 + (HEAP32[i10 >> 2] << 2) >> 2]; while (0);
 HEAP32[(HEAP32[i6 >> 2] | 0) + 44 >> 2] = HEAP32[1768 + (HEAP32[i10 >> 2] << 2) >> 2];
 HEAP32[(HEAP32[i6 >> 2] | 0) + 32 >> 2] = HEAP32[1936 + (HEAP32[1852 + (HEAP32[i10 >> 2] << 2) >> 2] << 3) >> 2];
 HEAP32[(HEAP32[i6 >> 2] | 0) + 36 >> 2] = HEAP32[1936 + (HEAP32[1852 + (HEAP32[i10 >> 2] << 2) >> 2] << 3) + 4 >> 2];
 if (_dirac_bool(i9) | 0 ? (HEAP32[i12 >> 2] = _dirac_uint(i9) | 0, HEAP32[(HEAP32[i6 >> 2] | 0) + 32 >> 2] = HEAP32[1936 + (HEAP32[i12 >> 2] << 3) >> 2], HEAP32[(HEAP32[i6 >> 2] | 0) + 36 >> 2] = HEAP32[1936 + (HEAP32[i12 >> 2] << 3) + 4 >> 2], (HEAP32[i12 >> 2] | 0) == 0) : 0) {
  i12 = _dirac_uint(i9) | 0;
  HEAP32[(HEAP32[i6 >> 2] | 0) + 32 >> 2] = i12;
  i12 = _dirac_uint(i9) | 0;
  HEAP32[(HEAP32[i6 >> 2] | 0) + 36 >> 2] = i12;
 }
 HEAP32[i5 >> 2] = 0;
 i13 = HEAP32[i5 >> 2] | 0;
 STACKTOP = i4;
 return i13 | 0;
}

function _get_seek_keypoint(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0, i17 = 0, i18 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 48 | 0;
 i5 = i4 + 36 | 0;
 i6 = i4 + 32 | 0;
 i7 = i4 + 24 | 0;
 i8 = i4 + 16 | 0;
 i9 = i4 + 8 | 0;
 i10 = i4;
 HEAP32[i6 >> 2] = i1;
 i1 = i7;
 HEAP32[i1 >> 2] = i2;
 HEAP32[i1 + 4 >> 2] = i3;
 i3 = i8;
 HEAP32[i3 >> 2] = 0;
 HEAP32[i3 + 4 >> 2] = 0;
 i3 = i9;
 HEAP32[i3 >> 2] = 0;
 HEAP32[i3 + 4 >> 2] = 0;
 if (!(HEAP32[i6 >> 2] | 0)) {
  HEAP32[i5 >> 2] = 0;
  i11 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i11 | 0;
 }
 i3 = (HEAP32[i6 >> 2] | 0) + 8 | 0;
 i1 = _i64Subtract(HEAP32[i3 >> 2] | 0, HEAP32[i3 + 4 >> 2] | 0, 1, 0) | 0;
 i3 = i9;
 HEAP32[i3 >> 2] = i1;
 HEAP32[i3 + 4 >> 2] = tempRet0;
 while (1) {
  i3 = i9;
  i1 = HEAP32[i3 + 4 >> 2] | 0;
  i2 = i8;
  i12 = HEAP32[i2 + 4 >> 2] | 0;
  if (!((i1 | 0) > (i12 | 0) | ((i1 | 0) == (i12 | 0) ? (HEAP32[i3 >> 2] | 0) >>> 0 > (HEAP32[i2 >> 2] | 0) >>> 0 : 0))) break;
  i2 = i8;
  i3 = i9;
  i12 = _i64Add(HEAP32[i2 >> 2] | 0, HEAP32[i2 + 4 >> 2] | 0, HEAP32[i3 >> 2] | 0, HEAP32[i3 + 4 >> 2] | 0) | 0;
  i3 = _i64Add(i12 | 0, tempRet0 | 0, 1, 0) | 0;
  i12 = _bitshift64Ashr(i3 | 0, tempRet0 | 0, 1) | 0;
  i3 = i10;
  HEAP32[i3 >> 2] = i12;
  HEAP32[i3 + 4 >> 2] = tempRet0;
  i3 = (HEAP32[(HEAP32[i6 >> 2] | 0) + 40 >> 2] | 0) + (HEAP32[i10 >> 2] << 4) + 8 | 0;
  i12 = i7;
  i2 = i10;
  i13 = HEAP32[i2 >> 2] | 0;
  i14 = HEAP32[i2 + 4 >> 2] | 0;
  if ((HEAP32[i3 >> 2] | 0) == (HEAP32[i12 >> 2] | 0) ? (HEAP32[i3 + 4 >> 2] | 0) == (HEAP32[i12 + 4 >> 2] | 0) : 0) {
   i15 = 6;
   break;
  }
  i12 = (HEAP32[(HEAP32[i6 >> 2] | 0) + 40 >> 2] | 0) + (i13 << 4) + 8 | 0;
  i3 = HEAP32[i12 + 4 >> 2] | 0;
  i2 = i7;
  i1 = HEAP32[i2 + 4 >> 2] | 0;
  i16 = i10;
  i17 = HEAP32[i16 >> 2] | 0;
  i18 = HEAP32[i16 + 4 >> 2] | 0;
  if ((i3 | 0) < (i1 | 0) | ((i3 | 0) == (i1 | 0) ? (HEAP32[i12 >> 2] | 0) >>> 0 < (HEAP32[i2 >> 2] | 0) >>> 0 : 0)) {
   i2 = i8;
   HEAP32[i2 >> 2] = i17;
   HEAP32[i2 + 4 >> 2] = i18;
   continue;
  } else {
   i2 = _i64Subtract(i17 | 0, i18 | 0, 1, 0) | 0;
   i18 = i9;
   HEAP32[i18 >> 2] = i2;
   HEAP32[i18 + 4 >> 2] = tempRet0;
   continue;
  }
 }
 if ((i15 | 0) == 6) {
  i15 = i8;
  HEAP32[i15 >> 2] = i13;
  HEAP32[i15 + 4 >> 2] = i14;
 }
 HEAP32[i5 >> 2] = (HEAP32[(HEAP32[i6 >> 2] | 0) + 40 >> 2] | 0) + (HEAP32[i8 >> 2] << 4);
 i11 = HEAP32[i5 >> 2] | 0;
 STACKTOP = i4;
 return i11 | 0;
}

function _decode_fisbone(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i4 = i3 + 20 | 0;
 i5 = i3 + 16 | 0;
 i6 = i3 + 12 | 0;
 i7 = i3 + 8 | 0;
 i8 = i3 + 4 | 0;
 i9 = i3;
 HEAP32[i5 >> 2] = i1;
 HEAP32[i6 >> 2] = i2;
 HEAP32[i7 >> 2] = 0;
 HEAP32[i8 >> 2] = -1;
 HEAP32[i9 >> 2] = 0;
 if (!(HEAP32[i5 >> 2] | 0)) {
  HEAP32[i4 >> 2] = -2;
  i10 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i10 | 0;
 }
 if (HEAP32[i6 >> 2] | 0 ? HEAP32[HEAP32[i6 >> 2] >> 2] | 0 : 0) {
  if ((HEAP32[(HEAP32[i6 >> 2] | 0) + 4 >> 2] | 0) < 52) {
   HEAP32[i4 >> 2] = -7;
   i10 = HEAP32[i4 >> 2] | 0;
   STACKTOP = i3;
   return i10 | 0;
  }
  HEAP32[i7 >> 2] = _calloc(1, 56) | 0;
  if (!(HEAP32[i7 >> 2] | 0)) {
   HEAP32[i4 >> 2] = -4;
   i10 = HEAP32[i4 >> 2] | 0;
   STACKTOP = i3;
   return i10 | 0;
  }
  HEAP32[i9 >> 2] = (HEAP32[HEAP32[i6 >> 2] >> 2] | 0) + 8;
  HEAP32[i9 >> 2] = _extract_uint32(HEAP32[i9 >> 2] | 0, HEAP32[i7 >> 2] | 0) | 0;
  HEAP32[i9 >> 2] = _extract_int32(HEAP32[i9 >> 2] | 0, (HEAP32[i7 >> 2] | 0) + 4 | 0) | 0;
  HEAP32[i9 >> 2] = _extract_uint32(HEAP32[i9 >> 2] | 0, (HEAP32[i7 >> 2] | 0) + 8 | 0) | 0;
  HEAP32[i9 >> 2] = _extract_int64(HEAP32[i9 >> 2] | 0, (HEAP32[i7 >> 2] | 0) + 16 | 0) | 0;
  HEAP32[i9 >> 2] = _extract_int64(HEAP32[i9 >> 2] | 0, (HEAP32[i7 >> 2] | 0) + 24 | 0) | 0;
  HEAP32[i9 >> 2] = _extract_int64(HEAP32[i9 >> 2] | 0, (HEAP32[i7 >> 2] | 0) + 32 | 0) | 0;
  HEAP32[i9 >> 2] = _extract_uint32(HEAP32[i9 >> 2] | 0, (HEAP32[i7 >> 2] | 0) + 40 | 0) | 0;
  HEAP8[(HEAP32[i7 >> 2] | 0) + 44 >> 0] = HEAP8[HEAP32[i9 >> 2] >> 0] | 0;
  i9 = _calloc((HEAP32[(HEAP32[i6 >> 2] | 0) + 4 >> 2] | 0) - 52 | 0, 1) | 0;
  HEAP32[(HEAP32[i7 >> 2] | 0) + 48 >> 2] = i9;
  i9 = HEAP32[i7 >> 2] | 0;
  if (!(HEAP32[(HEAP32[i7 >> 2] | 0) + 48 >> 2] | 0)) {
   _free(i9);
   HEAP32[i4 >> 2] = -4;
   i10 = HEAP32[i4 >> 2] | 0;
   STACKTOP = i3;
   return i10 | 0;
  }
  _memcpy(HEAP32[i9 + 48 >> 2] | 0, (HEAP32[HEAP32[i6 >> 2] >> 2] | 0) + 52 | 0, (HEAP32[(HEAP32[i6 >> 2] | 0) + 4 >> 2] | 0) - 52 | 0) | 0;
  HEAP32[i8 >> 2] = _oggskel_vect_add_bone(HEAP32[(HEAP32[i5 >> 2] | 0) + 112 >> 2] | 0, HEAP32[i7 >> 2] | 0, HEAP32[(HEAP32[i7 >> 2] | 0) + 4 >> 2] | 0) | 0;
  if ((HEAP32[i8 >> 2] | 0) < 0) _free(HEAP32[i7 >> 2] | 0);
  HEAP32[i4 >> 2] = HEAP32[i8 >> 2];
  i10 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i10 | 0;
 }
 HEAP32[i4 >> 2] = -13;
 i10 = HEAP32[i4 >> 2] | 0;
 STACKTOP = i3;
 return i10 | 0;
}

function _pop_arg(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0, d8 = 0.0;
 L1 : do if (i2 >>> 0 <= 20) do switch (i2 | 0) {
 case 9:
  {
   i4 = (HEAP32[i3 >> 2] | 0) + (4 - 1) & ~(4 - 1);
   i5 = HEAP32[i4 >> 2] | 0;
   HEAP32[i3 >> 2] = i4 + 4;
   HEAP32[i1 >> 2] = i5;
   break L1;
   break;
  }
 case 10:
  {
   i5 = (HEAP32[i3 >> 2] | 0) + (4 - 1) & ~(4 - 1);
   i4 = HEAP32[i5 >> 2] | 0;
   HEAP32[i3 >> 2] = i5 + 4;
   i5 = i1;
   HEAP32[i5 >> 2] = i4;
   HEAP32[i5 + 4 >> 2] = ((i4 | 0) < 0) << 31 >> 31;
   break L1;
   break;
  }
 case 11:
  {
   i4 = (HEAP32[i3 >> 2] | 0) + (4 - 1) & ~(4 - 1);
   i5 = HEAP32[i4 >> 2] | 0;
   HEAP32[i3 >> 2] = i4 + 4;
   i4 = i1;
   HEAP32[i4 >> 2] = i5;
   HEAP32[i4 + 4 >> 2] = 0;
   break L1;
   break;
  }
 case 12:
  {
   i4 = (HEAP32[i3 >> 2] | 0) + (8 - 1) & ~(8 - 1);
   i5 = i4;
   i6 = HEAP32[i5 >> 2] | 0;
   i7 = HEAP32[i5 + 4 >> 2] | 0;
   HEAP32[i3 >> 2] = i4 + 8;
   i4 = i1;
   HEAP32[i4 >> 2] = i6;
   HEAP32[i4 + 4 >> 2] = i7;
   break L1;
   break;
  }
 case 13:
  {
   i7 = (HEAP32[i3 >> 2] | 0) + (4 - 1) & ~(4 - 1);
   i4 = HEAP32[i7 >> 2] | 0;
   HEAP32[i3 >> 2] = i7 + 4;
   i7 = (i4 & 65535) << 16 >> 16;
   i4 = i1;
   HEAP32[i4 >> 2] = i7;
   HEAP32[i4 + 4 >> 2] = ((i7 | 0) < 0) << 31 >> 31;
   break L1;
   break;
  }
 case 14:
  {
   i7 = (HEAP32[i3 >> 2] | 0) + (4 - 1) & ~(4 - 1);
   i4 = HEAP32[i7 >> 2] | 0;
   HEAP32[i3 >> 2] = i7 + 4;
   i7 = i1;
   HEAP32[i7 >> 2] = i4 & 65535;
   HEAP32[i7 + 4 >> 2] = 0;
   break L1;
   break;
  }
 case 15:
  {
   i7 = (HEAP32[i3 >> 2] | 0) + (4 - 1) & ~(4 - 1);
   i4 = HEAP32[i7 >> 2] | 0;
   HEAP32[i3 >> 2] = i7 + 4;
   i7 = (i4 & 255) << 24 >> 24;
   i4 = i1;
   HEAP32[i4 >> 2] = i7;
   HEAP32[i4 + 4 >> 2] = ((i7 | 0) < 0) << 31 >> 31;
   break L1;
   break;
  }
 case 16:
  {
   i7 = (HEAP32[i3 >> 2] | 0) + (4 - 1) & ~(4 - 1);
   i4 = HEAP32[i7 >> 2] | 0;
   HEAP32[i3 >> 2] = i7 + 4;
   i7 = i1;
   HEAP32[i7 >> 2] = i4 & 255;
   HEAP32[i7 + 4 >> 2] = 0;
   break L1;
   break;
  }
 case 17:
  {
   i7 = (HEAP32[i3 >> 2] | 0) + (8 - 1) & ~(8 - 1);
   d8 = +HEAPF64[i7 >> 3];
   HEAP32[i3 >> 2] = i7 + 8;
   HEAPF64[i1 >> 3] = d8;
   break L1;
   break;
  }
 case 18:
  {
   i7 = (HEAP32[i3 >> 2] | 0) + (8 - 1) & ~(8 - 1);
   d8 = +HEAPF64[i7 >> 3];
   HEAP32[i3 >> 2] = i7 + 8;
   HEAPF64[i1 >> 3] = d8;
   break L1;
   break;
  }
 default:
  break L1;
 } while (0); while (0);
 return;
}

function ___stpncpy(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0, i17 = 0, i18 = 0, i19 = 0, i20 = 0, i21 = 0, i22 = 0, i23 = 0, i24 = 0, i25 = 0, i26 = 0, i27 = 0, i28 = 0;
 i4 = i2;
 do if (!((i4 ^ i1) & 3)) {
  i5 = (i3 | 0) != 0;
  L3 : do if (i5 & (i4 & 3 | 0) != 0) {
   i6 = i3;
   i7 = i2;
   i8 = i1;
   while (1) {
    i9 = HEAP8[i7 >> 0] | 0;
    HEAP8[i8 >> 0] = i9;
    if (!(i9 << 24 >> 24)) {
     i10 = i6;
     i11 = i7;
     i12 = i8;
     break L3;
    }
    i9 = i6 + -1 | 0;
    i13 = i7 + 1 | 0;
    i14 = i8 + 1 | 0;
    i15 = (i9 | 0) != 0;
    if (i15 & (i13 & 3 | 0) != 0) {
     i6 = i9;
     i7 = i13;
     i8 = i14;
    } else {
     i16 = i9;
     i17 = i13;
     i18 = i14;
     i19 = i15;
     i20 = 5;
     break;
    }
   }
  } else {
   i16 = i3;
   i17 = i2;
   i18 = i1;
   i19 = i5;
   i20 = 5;
  } while (0);
  if ((i20 | 0) == 5) if (i19) {
   i10 = i16;
   i11 = i17;
   i12 = i18;
  } else {
   i21 = i18;
   i22 = 0;
   break;
  }
  if (!(HEAP8[i11 >> 0] | 0)) {
   i21 = i12;
   i22 = i10;
  } else {
   L11 : do if (i10 >>> 0 > 3) {
    i5 = i10;
    i8 = i12;
    i7 = i11;
    while (1) {
     i6 = HEAP32[i7 >> 2] | 0;
     if ((i6 & -2139062144 ^ -2139062144) & i6 + -16843009 | 0) {
      i23 = i5;
      i24 = i8;
      i25 = i7;
      break L11;
     }
     HEAP32[i8 >> 2] = i6;
     i6 = i5 + -4 | 0;
     i15 = i7 + 4 | 0;
     i14 = i8 + 4 | 0;
     if (i6 >>> 0 > 3) {
      i5 = i6;
      i8 = i14;
      i7 = i15;
     } else {
      i23 = i6;
      i24 = i14;
      i25 = i15;
      break;
     }
    }
   } else {
    i23 = i10;
    i24 = i12;
    i25 = i11;
   } while (0);
   i26 = i25;
   i27 = i24;
   i28 = i23;
   i20 = 11;
  }
 } else {
  i26 = i2;
  i27 = i1;
  i28 = i3;
  i20 = 11;
 } while (0);
 L16 : do if ((i20 | 0) == 11) if (!i28) {
  i21 = i27;
  i22 = 0;
 } else {
  i3 = i26;
  i1 = i27;
  i2 = i28;
  while (1) {
   i23 = HEAP8[i3 >> 0] | 0;
   HEAP8[i1 >> 0] = i23;
   if (!(i23 << 24 >> 24)) {
    i21 = i1;
    i22 = i2;
    break L16;
   }
   i2 = i2 + -1 | 0;
   i23 = i1 + 1 | 0;
   if (!i2) {
    i21 = i23;
    i22 = 0;
    break;
   } else {
    i3 = i3 + 1 | 0;
    i1 = i23;
   }
  }
 } while (0);
 _memset(i21 | 0, 0, i22 | 0) | 0;
 return i21 | 0;
}

function _auto_calc_speex(i1, i2, i3, i4) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 var i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0;
 i5 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i6 = i5 + 8 | 0;
 i7 = i5;
 i8 = i5 + 24 | 0;
 i9 = i5 + 20 | 0;
 i10 = i5 + 16 | 0;
 i11 = i7;
 HEAP32[i11 >> 2] = i1;
 HEAP32[i11 + 4 >> 2] = i2;
 HEAP32[i8 >> 2] = i3;
 HEAP32[i9 >> 2] = i4;
 HEAP32[i10 >> 2] = HEAP32[(HEAP32[i8 >> 2] | 0) + 504 >> 2];
 do if (!(HEAP32[(HEAP32[i8 >> 2] | 0) + 504 >> 2] | 0)) {
  i4 = _malloc(12) | 0;
  HEAP32[(HEAP32[i8 >> 2] | 0) + 504 >> 2] = i4;
  if (!(HEAP32[(HEAP32[i8 >> 2] | 0) + 504 >> 2] | 0)) {
   i4 = i6;
   HEAP32[i4 >> 2] = -1;
   HEAP32[i4 + 4 >> 2] = -1;
   break;
  } else {
   HEAP32[i10 >> 2] = HEAP32[(HEAP32[i8 >> 2] | 0) + 504 >> 2];
   HEAP32[(HEAP32[i10 >> 2] | 0) + 8 >> 2] = 0;
   i4 = Math_imul(HEAP32[(HEAP32[HEAP32[i9 >> 2] >> 2] | 0) + 64 >> 2] | 0, HEAP32[(HEAP32[HEAP32[i9 >> 2] >> 2] | 0) + 56 >> 2] | 0) | 0;
   HEAP32[(HEAP32[i10 >> 2] | 0) + 4 >> 2] = i4;
   HEAP32[HEAP32[i10 >> 2] >> 2] = 1;
   i4 = i6;
   HEAP32[i4 >> 2] = 0;
   HEAP32[i4 + 4 >> 2] = 0;
   break;
  }
 } else {
  i4 = HEAP32[i10 >> 2] | 0;
  if ((HEAP32[HEAP32[i10 >> 2] >> 2] | 0) < 2) HEAP32[i4 >> 2] = (HEAP32[i4 >> 2] | 0) + 1; else HEAP32[i4 + 8 >> 2] = 1;
  i4 = i7;
  i3 = HEAP32[i4 + 4 >> 2] | 0;
  if ((i3 | 0) > -1 | (i3 | 0) == -1 & (HEAP32[i4 >> 2] | 0) >>> 0 > 4294967295) {
   i4 = i7;
   i3 = HEAP32[i4 + 4 >> 2] | 0;
   i2 = i6;
   HEAP32[i2 >> 2] = HEAP32[i4 >> 2];
   HEAP32[i2 + 4 >> 2] = i3;
   break;
  }
  if (!(HEAP32[(HEAP32[i10 >> 2] | 0) + 8 >> 2] | 0)) {
   i3 = i6;
   HEAP32[i3 >> 2] = 0;
   HEAP32[i3 + 4 >> 2] = 0;
   break;
  }
  i3 = (HEAP32[i8 >> 2] | 0) + 488 | 0;
  i2 = HEAP32[i3 + 4 >> 2] | 0;
  if ((i2 | 0) > 0 | (i2 | 0) == 0 & (HEAP32[i3 >> 2] | 0) >>> 0 > 0) {
   i3 = (HEAP32[i8 >> 2] | 0) + 488 | 0;
   i2 = HEAP32[(HEAP32[i10 >> 2] | 0) + 4 >> 2] | 0;
   i4 = _i64Add(HEAP32[i3 >> 2] | 0, HEAP32[i3 + 4 >> 2] | 0, i2 | 0, ((i2 | 0) < 0) << 31 >> 31 | 0) | 0;
   i2 = i6;
   HEAP32[i2 >> 2] = i4;
   HEAP32[i2 + 4 >> 2] = tempRet0;
   break;
  } else {
   i2 = i6;
   HEAP32[i2 >> 2] = -1;
   HEAP32[i2 + 4 >> 2] = -1;
   break;
  }
 } while (0);
 i10 = i6;
 tempRet0 = HEAP32[i10 + 4 >> 2] | 0;
 STACKTOP = i5;
 return HEAP32[i10 >> 2] | 0;
}

function _ogg_stream_packetout(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0, i17 = 0, i18 = 0, i19 = 0, i20 = 0, i21 = 0, i22 = 0, i23 = 0, i24 = 0, i25 = 0, i26 = 0;
 if (!i1) {
  i3 = 0;
  return i3 | 0;
 }
 i4 = HEAP32[i1 >> 2] | 0;
 if (!i4) {
  i3 = 0;
  return i3 | 0;
 }
 i5 = i1 + 36 | 0;
 i6 = HEAP32[i5 >> 2] | 0;
 if ((HEAP32[i1 + 32 >> 2] | 0) <= (i6 | 0)) {
  i3 = 0;
  return i3 | 0;
 }
 i7 = HEAP32[i1 + 16 >> 2] | 0;
 i8 = HEAP32[i7 + (i6 << 2) >> 2] | 0;
 if (i8 & 1024 | 0) {
  HEAP32[i5 >> 2] = i6 + 1;
  i9 = i1 + 344 | 0;
  i10 = i9;
  i11 = _i64Add(HEAP32[i10 >> 2] | 0, HEAP32[i10 + 4 >> 2] | 0, 1, 0) | 0;
  i10 = i9;
  HEAP32[i10 >> 2] = i11;
  HEAP32[i10 + 4 >> 2] = tempRet0;
  i3 = -1;
  return i3 | 0;
 }
 i10 = (i2 | 0) == 0;
 i11 = i8 & 255;
 i9 = i8 & 512;
 i12 = i8 & 256;
 if ((i11 | 0) == 255) {
  i8 = 255;
  i13 = i9;
  i14 = i6;
  while (1) {
   i15 = i14 + 1 | 0;
   i16 = HEAP32[i7 + (i15 << 2) >> 2] | 0;
   i17 = i16 & 255;
   i18 = (i16 & 512 | 0) == 0 ? i13 : 512;
   i16 = i17 + i8 | 0;
   if ((i17 | 0) == 255) {
    i8 = i16;
    i13 = i18;
    i14 = i15;
   } else {
    i19 = i16;
    i20 = i18;
    i21 = i15;
    break;
   }
  }
 } else {
  i19 = i11;
  i20 = i9;
  i21 = i6;
 }
 if (i10) {
  i10 = i1 + 12 | 0;
  i6 = i1 + 344 | 0;
  i9 = i6;
  i22 = i6;
  i23 = i10;
  i24 = HEAP32[i10 >> 2] | 0;
  i25 = HEAP32[i9 >> 2] | 0;
  i26 = HEAP32[i9 + 4 >> 2] | 0;
 } else {
  HEAP32[i2 + 12 >> 2] = i20;
  HEAP32[i2 + 8 >> 2] = i12;
  i12 = i1 + 12 | 0;
  i20 = HEAP32[i12 >> 2] | 0;
  HEAP32[i2 >> 2] = i4 + i20;
  i4 = i1 + 344 | 0;
  i9 = i4;
  i10 = HEAP32[i9 >> 2] | 0;
  i6 = HEAP32[i9 + 4 >> 2] | 0;
  i9 = i2 + 24 | 0;
  HEAP32[i9 >> 2] = i10;
  HEAP32[i9 + 4 >> 2] = i6;
  i9 = (HEAP32[i1 + 20 >> 2] | 0) + (i21 << 3) | 0;
  i1 = HEAP32[i9 + 4 >> 2] | 0;
  i11 = i2 + 16 | 0;
  HEAP32[i11 >> 2] = HEAP32[i9 >> 2];
  HEAP32[i11 + 4 >> 2] = i1;
  HEAP32[i2 + 4 >> 2] = i19;
  i22 = i4;
  i23 = i12;
  i24 = i20;
  i25 = i10;
  i26 = i6;
 }
 HEAP32[i23 >> 2] = i24 + i19;
 HEAP32[i5 >> 2] = i21 + 1;
 i21 = _i64Add(i25 | 0, i26 | 0, 1, 0) | 0;
 i26 = i22;
 HEAP32[i26 >> 2] = i21;
 HEAP32[i26 + 4 >> 2] = tempRet0;
 i3 = 1;
 return i3 | 0;
}

function ___stdio_write(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0, i17 = 0, i18 = 0, i19 = 0, i20 = 0, i21 = 0, i22 = 0, i23 = 0, i24 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 48 | 0;
 i5 = i4 + 16 | 0;
 i6 = i4;
 i7 = i4 + 32 | 0;
 i8 = i1 + 28 | 0;
 i9 = HEAP32[i8 >> 2] | 0;
 HEAP32[i7 >> 2] = i9;
 i10 = i1 + 20 | 0;
 i11 = (HEAP32[i10 >> 2] | 0) - i9 | 0;
 HEAP32[i7 + 4 >> 2] = i11;
 HEAP32[i7 + 8 >> 2] = i2;
 HEAP32[i7 + 12 >> 2] = i3;
 i2 = i1 + 60 | 0;
 i9 = i1 + 44 | 0;
 i12 = i7;
 i7 = 2;
 i13 = i11 + i3 | 0;
 while (1) {
  if (!(HEAP32[1335] | 0)) {
   HEAP32[i5 >> 2] = HEAP32[i2 >> 2];
   HEAP32[i5 + 4 >> 2] = i12;
   HEAP32[i5 + 8 >> 2] = i7;
   i14 = ___syscall_ret(___syscall146(146, i5 | 0) | 0) | 0;
  } else {
   _pthread_cleanup_push(2, i1 | 0);
   HEAP32[i6 >> 2] = HEAP32[i2 >> 2];
   HEAP32[i6 + 4 >> 2] = i12;
   HEAP32[i6 + 8 >> 2] = i7;
   i11 = ___syscall_ret(___syscall146(146, i6 | 0) | 0) | 0;
   _pthread_cleanup_pop(0);
   i14 = i11;
  }
  if ((i13 | 0) == (i14 | 0)) {
   i15 = 6;
   break;
  }
  if ((i14 | 0) < 0) {
   i16 = i12;
   i17 = i7;
   i15 = 8;
   break;
  }
  i11 = i13 - i14 | 0;
  i18 = HEAP32[i12 + 4 >> 2] | 0;
  if (i14 >>> 0 <= i18 >>> 0) if ((i7 | 0) == 2) {
   HEAP32[i8 >> 2] = (HEAP32[i8 >> 2] | 0) + i14;
   i19 = i18;
   i20 = i14;
   i21 = i12;
   i22 = 2;
  } else {
   i19 = i18;
   i20 = i14;
   i21 = i12;
   i22 = i7;
  } else {
   i23 = HEAP32[i9 >> 2] | 0;
   HEAP32[i8 >> 2] = i23;
   HEAP32[i10 >> 2] = i23;
   i19 = HEAP32[i12 + 12 >> 2] | 0;
   i20 = i14 - i18 | 0;
   i21 = i12 + 8 | 0;
   i22 = i7 + -1 | 0;
  }
  HEAP32[i21 >> 2] = (HEAP32[i21 >> 2] | 0) + i20;
  HEAP32[i21 + 4 >> 2] = i19 - i20;
  i12 = i21;
  i7 = i22;
  i13 = i11;
 }
 if ((i15 | 0) == 6) {
  i13 = HEAP32[i9 >> 2] | 0;
  HEAP32[i1 + 16 >> 2] = i13 + (HEAP32[i1 + 48 >> 2] | 0);
  i9 = i13;
  HEAP32[i8 >> 2] = i9;
  HEAP32[i10 >> 2] = i9;
  i24 = i3;
 } else if ((i15 | 0) == 8) {
  HEAP32[i1 + 16 >> 2] = 0;
  HEAP32[i8 >> 2] = 0;
  HEAP32[i10 >> 2] = 0;
  HEAP32[i1 >> 2] = HEAP32[i1 >> 2] | 32;
  if ((i17 | 0) == 2) i24 = 0; else i24 = i3 - (HEAP32[i16 + 4 >> 2] | 0) | 0;
 }
 STACKTOP = i4;
 return i24 | 0;
}

function _oggz_auto_read_comments(i1, i2, i3, i4) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 var i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0;
 i5 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i6 = i5 + 20 | 0;
 i7 = i5 + 16 | 0;
 i8 = i5 + 12 | 0;
 i9 = i5 + 8 | 0;
 i10 = i5 + 4 | 0;
 i11 = i5;
 HEAP32[i6 >> 2] = i1;
 HEAP32[i7 >> 2] = i2;
 HEAP32[i8 >> 2] = i3;
 HEAP32[i9 >> 2] = i4;
 HEAP32[i10 >> 2] = -1;
 HEAP32[i11 >> 2] = -1;
 switch (HEAP32[(HEAP32[i7 >> 2] | 0) + 360 >> 2] | 0) {
 case 1:
  {
   if ((HEAP32[(HEAP32[i9 >> 2] | 0) + 4 >> 2] | 0) > 7 ? (_memcmp(HEAP32[HEAP32[i9 >> 2] >> 2] | 0, 2472, 7) | 0) == 0 : 0) HEAP32[i10 >> 2] = 7;
   break;
  }
 case 3:
 case 2:
  {
   HEAP32[i10 >> 2] = 0;
   break;
  }
 case 0:
  {
   if ((HEAP32[(HEAP32[i9 >> 2] | 0) + 4 >> 2] | 0) > 7 ? (_memcmp(HEAP32[HEAP32[i9 >> 2] >> 2] | 0, 2480, 7) | 0) == 0 : 0) HEAP32[i10 >> 2] = 7;
   break;
  }
 case 11:
  {
   if ((HEAP32[(HEAP32[i9 >> 2] | 0) + 4 >> 2] | 0) > 9 ? (_memcmp(HEAP32[HEAP32[i9 >> 2] >> 2] | 0, 2488, 8) | 0) == 0 : 0) HEAP32[i10 >> 2] = 9;
   break;
  }
 case 8:
  {
   if ((HEAP32[(HEAP32[i9 >> 2] | 0) + 4 >> 2] | 0) > 4 ? ((HEAPU8[HEAP32[HEAP32[i9 >> 2] >> 2] >> 0] | 0) & 7 | 0) == 4 : 0) {
    HEAP32[i11 >> 2] = ((HEAPU8[(HEAP32[HEAP32[i9 >> 2] >> 2] | 0) + 1 >> 0] | 0) << 16) + ((HEAPU8[(HEAP32[HEAP32[i9 >> 2] >> 2] | 0) + 2 >> 0] | 0) << 8) + (HEAPU8[(HEAP32[HEAP32[i9 >> 2] >> 2] | 0) + 3 >> 0] | 0);
    HEAP32[i10 >> 2] = 4;
   }
   break;
  }
 case 13:
  {
   if ((HEAP32[(HEAP32[i9 >> 2] | 0) + 4 >> 2] | 0) > 8 ? (_memcmp(HEAP32[HEAP32[i9 >> 2] >> 2] | 0, 2497, 8) | 0) == 0 : 0) HEAP32[i10 >> 2] = 8;
   break;
  }
 case 14:
  {
   if ((HEAP32[(HEAP32[i9 >> 2] | 0) + 4 >> 2] | 0) > 7 ? (_memcmp(HEAP32[HEAP32[i9 >> 2] >> 2] | 0, 2506, 7) | 0) == 0 : 0) HEAP32[i10 >> 2] = 7;
   break;
  }
 default:
  {}
 }
 if ((HEAP32[i11 >> 2] | 0) == -1) HEAP32[i11 >> 2] = (HEAP32[(HEAP32[i9 >> 2] | 0) + 4 >> 2] | 0) - (HEAP32[i10 >> 2] | 0);
 if ((HEAP32[i10 >> 2] | 0) < 0) {
  STACKTOP = i5;
  return 0;
 }
 _oggz_comments_decode(HEAP32[i6 >> 2] | 0, HEAP32[i8 >> 2] | 0, (HEAP32[HEAP32[i9 >> 2] >> 2] | 0) + (HEAP32[i10 >> 2] | 0) | 0, HEAP32[i11 >> 2] | 0) | 0;
 STACKTOP = i5;
 return 0;
}

function _oggz_add_stream(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i4 = i3 + 12 | 0;
 i5 = i3 + 8 | 0;
 i6 = i3 + 4 | 0;
 i7 = i3;
 HEAP32[i5 >> 2] = i1;
 HEAP32[i6 >> 2] = i2;
 HEAP32[i7 >> 2] = _malloc(512) | 0;
 if (!(HEAP32[i7 >> 2] | 0)) {
  HEAP32[i4 >> 2] = 0;
  i8 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i8 | 0;
 }
 _ogg_stream_init(HEAP32[i7 >> 2] | 0, HEAP32[i6 >> 2] | 0) | 0;
 i6 = (_oggz_comments_init(HEAP32[i7 >> 2] | 0) | 0) == -1;
 i2 = HEAP32[i7 >> 2] | 0;
 if (i6) {
  _free(i2);
  HEAP32[i4 >> 2] = 0;
  i8 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i8 | 0;
 } else {
  HEAP32[i2 + 360 >> 2] = 15;
  HEAP32[(HEAP32[i7 >> 2] | 0) + 364 >> 2] = 3;
  HEAP32[(HEAP32[i7 >> 2] | 0) + 368 >> 2] = 0;
  i2 = (HEAP32[i7 >> 2] | 0) + 376 | 0;
  HEAP32[i2 >> 2] = 1;
  HEAP32[i2 + 4 >> 2] = 0;
  i2 = (HEAP32[i7 >> 2] | 0) + 384 | 0;
  HEAP32[i2 >> 2] = 1;
  HEAP32[i2 + 4 >> 2] = 0;
  i2 = (HEAP32[i7 >> 2] | 0) + 392 | 0;
  HEAP32[i2 >> 2] = 0;
  HEAP32[i2 + 4 >> 2] = 0;
  i2 = (HEAP32[i7 >> 2] | 0) + 400 | 0;
  HEAP32[i2 >> 2] = 0;
  HEAP32[i2 + 4 >> 2] = 0;
  HEAP32[(HEAP32[i7 >> 2] | 0) + 408 >> 2] = 0;
  HEAP32[(HEAP32[i7 >> 2] | 0) + 420 >> 2] = 0;
  HEAP32[(HEAP32[i7 >> 2] | 0) + 424 >> 2] = 1;
  HEAP32[(HEAP32[i7 >> 2] | 0) + 428 >> 2] = 0;
  i2 = (HEAP32[i7 >> 2] | 0) + 432 | 0;
  HEAP32[i2 >> 2] = 0;
  HEAP32[i2 + 4 >> 2] = 0;
  i2 = (HEAP32[i7 >> 2] | 0) + 440 | 0;
  HEAP32[i2 >> 2] = -1;
  HEAP32[i2 + 4 >> 2] = -1;
  HEAP32[(HEAP32[i7 >> 2] | 0) + 448 >> 2] = 0;
  HEAP32[(HEAP32[i7 >> 2] | 0) + 452 >> 2] = 0;
  HEAP32[(HEAP32[i7 >> 2] | 0) + 456 >> 2] = 0;
  HEAP32[(HEAP32[i7 >> 2] | 0) + 460 >> 2] = 0;
  HEAP32[(HEAP32[i7 >> 2] | 0) + 464 >> 2] = 0;
  HEAP32[(HEAP32[i7 >> 2] | 0) + 468 >> 2] = 0;
  HEAP32[(HEAP32[i7 >> 2] | 0) + 472 >> 2] = 0;
  HEAP32[(HEAP32[i7 >> 2] | 0) + 476 >> 2] = 0;
  HEAP32[(HEAP32[i7 >> 2] | 0) + 480 >> 2] = 0;
  HEAP32[(HEAP32[i7 >> 2] | 0) + 504 >> 2] = 0;
  _oggz_vector_insert_p(HEAP32[(HEAP32[i5 >> 2] | 0) + 80 >> 2] | 0, HEAP32[i7 >> 2] | 0) | 0;
  HEAP32[i4 >> 2] = HEAP32[i7 >> 2];
  i8 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i8 | 0;
 }
 return 0;
}

function _oggskel_decode_header(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i4 = i3 + 12 | 0;
 i5 = i3 + 8 | 0;
 i6 = i3 + 4 | 0;
 i7 = i3;
 HEAP32[i5 >> 2] = i1;
 HEAP32[i6 >> 2] = i2;
 HEAP32[i7 >> 2] = -1;
 if (!(HEAP32[i5 >> 2] | 0)) {
  HEAP32[i4 >> 2] = -2;
  i8 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i8 | 0;
 }
 if (!(HEAP32[i6 >> 2] | 0)) {
  HEAP32[i4 >> 2] = -13;
  i8 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i8 | 0;
 }
 if (HEAP32[(HEAP32[i6 >> 2] | 0) + 12 >> 2] | 0) {
  if ((HEAP32[(HEAP32[i5 >> 2] | 0) + 120 >> 2] | 0) != 1) {
   HEAP32[i4 >> 2] = -1;
   i8 = HEAP32[i4 >> 2] | 0;
   STACKTOP = i3;
   return i8 | 0;
  }
  HEAP16[(HEAP32[i5 >> 2] | 0) + 118 >> 1] = 1;
  HEAP32[(HEAP32[i5 >> 2] | 0) + 120 >> 2] = 2;
  if (HEAP32[(HEAP32[i6 >> 2] | 0) + 4 >> 2] | 0) {
   HEAP32[i4 >> 2] = -6;
   i8 = HEAP32[i4 >> 2] | 0;
   STACKTOP = i3;
   return i8 | 0;
  } else {
   HEAP32[i4 >> 2] = 0;
   i8 = HEAP32[i4 >> 2] | 0;
   STACKTOP = i3;
   return i8 | 0;
  }
 }
 do if (!(_memcmp(HEAP32[HEAP32[i6 >> 2] >> 2] | 0, 2514, 8) | 0)) {
  HEAP32[i7 >> 2] = _decode_fishead(HEAP32[i5 >> 2] | 0, HEAP32[i6 >> 2] | 0) | 0;
  if (HEAP32[i7 >> 2] | 0) {
   if (HEAP32[(HEAP32[i6 >> 2] | 0) + 8 >> 2] | 0) {
    if (HEAP32[(HEAP32[i5 >> 2] | 0) + 120 >> 2] | 0) HEAP32[i7 >> 2] = -14;
   } else HEAP32[i7 >> 2] = -12;
   HEAP32[(HEAP32[i5 >> 2] | 0) + 120 >> 2] = 1;
  }
 } else {
  if (!(_memcmp(HEAP32[HEAP32[i6 >> 2] >> 2] | 0, 2523, 8) | 0)) {
   HEAP32[i7 >> 2] = _decode_fisbone(HEAP32[i5 >> 2] | 0, HEAP32[i6 >> 2] | 0) | 0;
   if (!(HEAP32[i7 >> 2] | 0)) break;
   if ((HEAP32[(HEAP32[i5 >> 2] | 0) + 120 >> 2] | 0) == 1) break;
   HEAP32[i7 >> 2] = -14;
   break;
  }
  if (_memcmp(HEAP32[HEAP32[i6 >> 2] >> 2] | 0, 2532, 6) | 0) {
   HEAP32[i7 >> 2] = -1;
   break;
  }
  HEAP16[(HEAP32[i5 >> 2] | 0) + 116 >> 1] = 1;
  HEAP32[i7 >> 2] = _decode_index(HEAP32[i5 >> 2] | 0, HEAP32[i6 >> 2] | 0) | 0;
  if (HEAP32[i7 >> 2] | 0 ? (HEAP32[(HEAP32[i5 >> 2] | 0) + 120 >> 2] | 0) != 1 : 0) HEAP32[i7 >> 2] = -14;
 } while (0);
 HEAP32[i4 >> 2] = HEAP32[i7 >> 2];
 i8 = HEAP32[i4 >> 2] | 0;
 STACKTOP = i3;
 return i8 | 0;
}

function _auto_rcalc_vorbis(i1, i2, i3, i4, i5) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 i5 = i5 | 0;
 var i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0, i17 = 0, i18 = 0, i19 = 0, i20 = 0, i21 = 0, i22 = 0, i23 = 0, i24 = 0, i25 = 0;
 i6 = STACKTOP;
 STACKTOP = STACKTOP + 64 | 0;
 i7 = i6 + 16 | 0;
 i8 = i6 + 8 | 0;
 i9 = i6 + 48 | 0;
 i10 = i6 + 44 | 0;
 i11 = i6 + 40 | 0;
 i12 = i6 + 36 | 0;
 i13 = i6 + 32 | 0;
 i14 = i6 + 28 | 0;
 i15 = i6 + 24 | 0;
 i16 = i6;
 i17 = i8;
 HEAP32[i17 >> 2] = i1;
 HEAP32[i17 + 4 >> 2] = i2;
 HEAP32[i9 >> 2] = i3;
 HEAP32[i10 >> 2] = i4;
 HEAP32[i11 >> 2] = i5;
 HEAP32[i12 >> 2] = HEAP32[(HEAP32[i9 >> 2] | 0) + 504 >> 2];
 HEAP32[i13 >> 2] = (HEAPU8[HEAP32[HEAP32[i10 >> 2] >> 2] >> 0] | 0) >> 1 & (1 << HEAP32[(HEAP32[i12 >> 2] | 0) + 36 >> 2]) - 1;
 i10 = HEAP32[i12 >> 2] | 0;
 if (HEAP32[(HEAP32[i12 >> 2] | 0) + 40 + (HEAP32[i13 >> 2] << 2) >> 2] | 0) i18 = HEAP32[i10 + 24 >> 2] | 0; else i18 = HEAP32[i10 + 20 >> 2] | 0;
 HEAP32[i14 >> 2] = i18;
 HEAP32[i13 >> 2] = (HEAPU8[HEAP32[HEAP32[i11 >> 2] >> 2] >> 0] | 0) >> 1 & (1 << HEAP32[(HEAP32[i12 >> 2] | 0) + 36 >> 2]) - 1;
 i11 = HEAP32[i12 >> 2] | 0;
 if (HEAP32[(HEAP32[i12 >> 2] | 0) + 40 + (HEAP32[i13 >> 2] << 2) >> 2] | 0) i19 = HEAP32[i11 + 24 >> 2] | 0; else i19 = HEAP32[i11 + 20 >> 2] | 0;
 HEAP32[i15 >> 2] = i19;
 i19 = i8;
 i8 = ((HEAP32[i14 >> 2] | 0) + (HEAP32[i15 >> 2] | 0) | 0) / 4 | 0;
 i15 = _i64Subtract(HEAP32[i19 >> 2] | 0, HEAP32[i19 + 4 >> 2] | 0, i8 | 0, ((i8 | 0) < 0) << 31 >> 31 | 0) | 0;
 i8 = i16;
 HEAP32[i8 >> 2] = i15;
 HEAP32[i8 + 4 >> 2] = tempRet0;
 if ((HEAP32[i16 + 4 >> 2] | 0) < 0) {
  i8 = i7;
  HEAP32[i8 >> 2] = 0;
  HEAP32[i8 + 4 >> 2] = 0;
  i20 = i7;
  i21 = i20;
  i22 = HEAP32[i21 >> 2] | 0;
  i23 = i20 + 4 | 0;
  i24 = i23;
  i25 = HEAP32[i24 >> 2] | 0;
  tempRet0 = i25;
  STACKTOP = i6;
  return i22 | 0;
 } else {
  i8 = i16;
  i16 = HEAP32[i8 + 4 >> 2] | 0;
  i15 = i7;
  HEAP32[i15 >> 2] = HEAP32[i8 >> 2];
  HEAP32[i15 + 4 >> 2] = i16;
  i20 = i7;
  i21 = i20;
  i22 = HEAP32[i21 >> 2] | 0;
  i23 = i20 + 4 | 0;
  i24 = i23;
  i25 = HEAP32[i24 >> 2] | 0;
  tempRet0 = i25;
  STACKTOP = i6;
  return i22 | 0;
 }
 return 0;
}

function _auto_rcalc_opus(i1, i2, i3, i4, i5) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 i5 = i5 | 0;
 var i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0, i17 = 0, i18 = 0, i19 = 0;
 i6 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i7 = i6 + 8 | 0;
 i8 = i6 + 28 | 0;
 i9 = i6 + 24 | 0;
 i10 = i6 + 20 | 0;
 i11 = i6 + 16 | 0;
 i12 = i6;
 i13 = i7;
 HEAP32[i13 >> 2] = i1;
 HEAP32[i13 + 4 >> 2] = i2;
 HEAP32[i8 >> 2] = i3;
 HEAP32[i9 >> 2] = i4;
 HEAP32[i10 >> 2] = i5;
 HEAP32[i11 >> 2] = HEAP32[(HEAP32[i8 >> 2] | 0) + 504 >> 2];
 i8 = i7;
 i5 = HEAP32[i8 + 4 >> 2] | 0;
 i4 = (HEAP32[i11 >> 2] | 0) + 8 | 0;
 i3 = HEAP32[i4 + 4 >> 2] | 0;
 i2 = i7;
 i7 = HEAP32[i2 >> 2] | 0;
 i13 = HEAP32[i2 + 4 >> 2] | 0;
 if (!((i5 | 0) > (i3 | 0) | ((i5 | 0) == (i3 | 0) ? (HEAP32[i8 >> 2] | 0) >>> 0 >= (HEAP32[i4 >> 2] | 0) >>> 0 : 0))) {
  i4 = i12;
  HEAP32[i4 >> 2] = i7;
  HEAP32[i4 + 4 >> 2] = i13;
  i4 = _opus_packet_duration(HEAP32[i9 >> 2] | 0) | 0;
  i9 = (HEAP32[i11 >> 2] | 0) + 8 | 0;
  i8 = i9;
  i3 = _i64Subtract(HEAP32[i8 >> 2] | 0, HEAP32[i8 + 4 >> 2] | 0, i4 | 0, tempRet0 | 0) | 0;
  i4 = i9;
  HEAP32[i4 >> 2] = i3;
  HEAP32[i4 + 4 >> 2] = tempRet0;
  i14 = i12;
  i15 = i14;
  i16 = HEAP32[i15 >> 2] | 0;
  i17 = i14 + 4 | 0;
  i18 = i17;
  i19 = HEAP32[i18 >> 2] | 0;
  tempRet0 = i19;
  STACKTOP = i6;
  return i16 | 0;
 }
 i4 = _opus_packet_duration(HEAP32[i10 >> 2] | 0) | 0;
 i10 = _i64Subtract(i7 | 0, i13 | 0, i4 | 0, tempRet0 | 0) | 0;
 i4 = i12;
 HEAP32[i4 >> 2] = i10;
 HEAP32[i4 + 4 >> 2] = tempRet0;
 i4 = i12;
 i10 = HEAP32[i4 + 4 >> 2] | 0;
 i13 = (HEAP32[i11 >> 2] | 0) + 8 | 0;
 i7 = HEAP32[i13 + 4 >> 2] | 0;
 if ((i10 | 0) < (i7 | 0) | ((i10 | 0) == (i7 | 0) ? (HEAP32[i4 >> 2] | 0) >>> 0 < (HEAP32[i13 >> 2] | 0) >>> 0 : 0)) {
  i13 = (HEAP32[i11 >> 2] | 0) + 8 | 0;
  i4 = HEAP32[i13 + 4 >> 2] | 0;
  i7 = i12;
  HEAP32[i7 >> 2] = HEAP32[i13 >> 2];
  HEAP32[i7 + 4 >> 2] = i4;
 }
 i4 = (HEAP32[i11 >> 2] | 0) + 8 | 0;
 HEAP32[i4 >> 2] = 0;
 HEAP32[i4 + 4 >> 2] = 0;
 i14 = i12;
 i15 = i14;
 i16 = HEAP32[i15 >> 2] | 0;
 i17 = i14 + 4 | 0;
 i18 = i17;
 i19 = HEAP32[i18 >> 2] | 0;
 tempRet0 = i19;
 STACKTOP = i6;
 return i16 | 0;
}

function _ogv_demuxer_media_duration() {
 var i1 = 0, i2 = 0, i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, d9 = 0.0, i10 = 0, d11 = 0.0, d12 = 0.0, i13 = 0, i14 = 0, d15 = 0.0, d16 = 0.0, d17 = 0.0, d18 = 0.0, d19 = 0.0;
 i1 = STACKTOP;
 STACKTOP = STACKTOP + 48 | 0;
 i2 = i1 + 42 | 0;
 i3 = i1 + 40 | 0;
 i4 = i1 + 32 | 0;
 i5 = i1 + 24 | 0;
 i6 = i1 + 16 | 0;
 i7 = i1 + 8 | 0;
 i8 = i1;
 if (!(HEAP32[1330] | 0)) {
  d9 = -1.0;
  STACKTOP = i1;
  return +d9;
 }
 HEAP16[i2 >> 1] = -1;
 HEAP16[i3 >> 1] = -1;
 _oggskel_get_ver_maj(HEAP32[1329] | 0, i2) | 0;
 _oggskel_get_ver_min(HEAP32[1329] | 0, i3) | 0;
 i3 = HEAP32[1331] | 0;
 if (!i3) i10 = 0; else {
  HEAP32[i4 >> 2] = i3;
  i10 = 1;
 }
 i3 = HEAP32[1332] | 0;
 if (!i3) if (!i10) {
  d11 = -1.0;
  d12 = -1.0;
 } else {
  i13 = 1;
  i14 = 7;
 } else {
  HEAP32[i4 + (i10 << 2) >> 2] = i3;
  i13 = i10 + 1 | 0;
  i14 = 7;
 }
 if ((i14 | 0) == 7) {
  d15 = -1.0;
  i14 = 0;
  d16 = -1.0;
  while (1) {
   i10 = i5;
   HEAP32[i10 >> 2] = -1;
   HEAP32[i10 + 4 >> 2] = -1;
   i10 = i6;
   HEAP32[i10 >> 2] = -1;
   HEAP32[i10 + 4 >> 2] = -1;
   i10 = i7;
   HEAP32[i10 >> 2] = -1;
   HEAP32[i10 + 4 >> 2] = -1;
   i10 = i8;
   HEAP32[i10 >> 2] = -1;
   HEAP32[i10 + 4 >> 2] = -1;
   i10 = HEAP32[i4 + (i14 << 2) >> 2] | 0;
   _oggskel_get_first_sample_num(HEAP32[1329] | 0, i10, i5) | 0;
   _oggskel_get_first_sample_denum(HEAP32[1329] | 0, i10, i6) | 0;
   _oggskel_get_last_sample_num(HEAP32[1329] | 0, i10, i7) | 0;
   _oggskel_get_last_sample_denum(HEAP32[1329] | 0, i10, i8) | 0;
   i10 = i5;
   i3 = i6;
   d17 = (+((HEAP32[i10 >> 2] | 0) >>> 0) + 4294967296.0 * +(HEAP32[i10 + 4 >> 2] | 0)) / (+((HEAP32[i3 >> 2] | 0) >>> 0) + 4294967296.0 * +(HEAP32[i3 + 4 >> 2] | 0));
   d18 = d15 == -1.0 | d17 < d15 ? d17 : d15;
   i3 = i7;
   i10 = i8;
   d17 = (+((HEAP32[i3 >> 2] | 0) >>> 0) + 4294967296.0 * +(HEAP32[i3 + 4 >> 2] | 0)) / (+((HEAP32[i10 >> 2] | 0) >>> 0) + 4294967296.0 * +(HEAP32[i10 + 4 >> 2] | 0));
   d19 = d16 == -1.0 | d17 > d16 ? d17 : d16;
   i14 = i14 + 1 | 0;
   if ((i14 | 0) == (i13 | 0)) {
    d11 = d18;
    d12 = d19;
    break;
   } else {
    d15 = d18;
    d16 = d19;
   }
  }
 }
 d9 = d12 - d11;
 STACKTOP = i1;
 return +d9;
}

function _oggz_get_unit(i1, i2, i3, i4) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 var i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0;
 i5 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i6 = i5 + 8 | 0;
 i7 = i5 + 24 | 0;
 i8 = i5 + 20 | 0;
 i9 = i5;
 i10 = i5 + 16 | 0;
 HEAP32[i7 >> 2] = i1;
 HEAP32[i8 >> 2] = i2;
 i2 = i9;
 HEAP32[i2 >> 2] = i3;
 HEAP32[i2 + 4 >> 2] = i4;
 do if (!(HEAP32[i7 >> 2] | 0)) {
  i4 = i6;
  HEAP32[i4 >> 2] = -2;
  HEAP32[i4 + 4 >> 2] = -1;
 } else {
  i4 = i9;
  if ((HEAP32[i4 >> 2] | 0) == -1 ? (HEAP32[i4 + 4 >> 2] | 0) == -1 : 0) {
   i4 = i6;
   HEAP32[i4 >> 2] = -1;
   HEAP32[i4 + 4 >> 2] = -1;
   break;
  }
  i4 = HEAP32[i7 >> 2] | 0;
  if ((HEAP32[i8 >> 2] | 0) == -1) {
   if (HEAP32[i4 + 88 >> 2] | 0) {
    i2 = i9;
    i3 = FUNCTION_TABLE_iiiiii[HEAP32[(HEAP32[i7 >> 2] | 0) + 88 >> 2] & 31](HEAP32[i7 >> 2] | 0, HEAP32[i8 >> 2] | 0, HEAP32[i2 >> 2] | 0, HEAP32[i2 + 4 >> 2] | 0, HEAP32[(HEAP32[i7 >> 2] | 0) + 92 >> 2] | 0) | 0;
    i2 = i6;
    HEAP32[i2 >> 2] = i3;
    HEAP32[i2 + 4 >> 2] = tempRet0;
    break;
   }
  } else {
   HEAP32[i10 >> 2] = _oggz_get_stream(i4, HEAP32[i8 >> 2] | 0) | 0;
   if (!(HEAP32[i10 >> 2] | 0)) {
    i4 = i6;
    HEAP32[i4 >> 2] = -1;
    HEAP32[i4 + 4 >> 2] = -1;
    break;
   }
   if (HEAP32[(HEAP32[i10 >> 2] | 0) + 448 >> 2] | 0) {
    i4 = i9;
    i2 = FUNCTION_TABLE_iiiiii[HEAP32[(HEAP32[i10 >> 2] | 0) + 448 >> 2] & 31](HEAP32[i7 >> 2] | 0, HEAP32[i8 >> 2] | 0, HEAP32[i4 >> 2] | 0, HEAP32[i4 + 4 >> 2] | 0, HEAP32[(HEAP32[i10 >> 2] | 0) + 452 >> 2] | 0) | 0;
    i4 = i6;
    HEAP32[i4 >> 2] = i2;
    HEAP32[i4 + 4 >> 2] = tempRet0;
    break;
   }
   if (HEAP32[(HEAP32[i7 >> 2] | 0) + 88 >> 2] | 0) {
    i4 = i9;
    i2 = FUNCTION_TABLE_iiiiii[HEAP32[(HEAP32[i7 >> 2] | 0) + 88 >> 2] & 31](HEAP32[i7 >> 2] | 0, HEAP32[i8 >> 2] | 0, HEAP32[i4 >> 2] | 0, HEAP32[i4 + 4 >> 2] | 0, HEAP32[(HEAP32[i7 >> 2] | 0) + 92 >> 2] | 0) | 0;
    i4 = i6;
    HEAP32[i4 >> 2] = i2;
    HEAP32[i4 + 4 >> 2] = tempRet0;
    break;
   }
  }
  i4 = i6;
  HEAP32[i4 >> 2] = -1;
  HEAP32[i4 + 4 >> 2] = -1;
 } while (0);
 i7 = i6;
 tempRet0 = HEAP32[i7 + 4 >> 2] | 0;
 STACKTOP = i5;
 return HEAP32[i7 >> 2] | 0;
}

function _auto_calc_celt(i1, i2, i3, i4) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 var i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0;
 i5 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i6 = i5 + 8 | 0;
 i7 = i5;
 i8 = i5 + 24 | 0;
 i9 = i5 + 16 | 0;
 i10 = i7;
 HEAP32[i10 >> 2] = i1;
 HEAP32[i10 + 4 >> 2] = i2;
 HEAP32[i8 >> 2] = i3;
 HEAP32[i5 + 20 >> 2] = i4;
 HEAP32[i9 >> 2] = HEAP32[(HEAP32[i8 >> 2] | 0) + 504 >> 2];
 do if (!(HEAP32[(HEAP32[i8 >> 2] | 0) + 504 >> 2] | 0)) {
  i4 = _malloc(12) | 0;
  HEAP32[(HEAP32[i8 >> 2] | 0) + 504 >> 2] = i4;
  if (!(HEAP32[(HEAP32[i8 >> 2] | 0) + 504 >> 2] | 0)) {
   i4 = i6;
   HEAP32[i4 >> 2] = -1;
   HEAP32[i4 + 4 >> 2] = -1;
   break;
  } else {
   HEAP32[i9 >> 2] = HEAP32[(HEAP32[i8 >> 2] | 0) + 504 >> 2];
   HEAP32[(HEAP32[i9 >> 2] | 0) + 8 >> 2] = 0;
   HEAP32[(HEAP32[i9 >> 2] | 0) + 4 >> 2] = 256;
   HEAP32[HEAP32[i9 >> 2] >> 2] = 1;
   i4 = i6;
   HEAP32[i4 >> 2] = 0;
   HEAP32[i4 + 4 >> 2] = 0;
   break;
  }
 } else {
  i4 = HEAP32[i9 >> 2] | 0;
  if ((HEAP32[HEAP32[i9 >> 2] >> 2] | 0) < 2) HEAP32[i4 >> 2] = (HEAP32[i4 >> 2] | 0) + 1; else HEAP32[i4 + 8 >> 2] = 1;
  i4 = i7;
  i3 = HEAP32[i4 + 4 >> 2] | 0;
  if ((i3 | 0) > -1 | (i3 | 0) == -1 & (HEAP32[i4 >> 2] | 0) >>> 0 > 4294967295) {
   i4 = i7;
   i3 = HEAP32[i4 + 4 >> 2] | 0;
   i2 = i6;
   HEAP32[i2 >> 2] = HEAP32[i4 >> 2];
   HEAP32[i2 + 4 >> 2] = i3;
   break;
  }
  if (!(HEAP32[(HEAP32[i9 >> 2] | 0) + 8 >> 2] | 0)) {
   i3 = i6;
   HEAP32[i3 >> 2] = 0;
   HEAP32[i3 + 4 >> 2] = 0;
   break;
  }
  i3 = (HEAP32[i8 >> 2] | 0) + 488 | 0;
  i2 = HEAP32[i3 + 4 >> 2] | 0;
  if ((i2 | 0) > 0 | (i2 | 0) == 0 & (HEAP32[i3 >> 2] | 0) >>> 0 > 0) {
   i3 = (HEAP32[i8 >> 2] | 0) + 488 | 0;
   i2 = HEAP32[(HEAP32[i9 >> 2] | 0) + 4 >> 2] | 0;
   i4 = _i64Add(HEAP32[i3 >> 2] | 0, HEAP32[i3 + 4 >> 2] | 0, i2 | 0, ((i2 | 0) < 0) << 31 >> 31 | 0) | 0;
   i2 = i6;
   HEAP32[i2 >> 2] = i4;
   HEAP32[i2 + 4 >> 2] = tempRet0;
   break;
  } else {
   i2 = i6;
   HEAP32[i2 >> 2] = -1;
   HEAP32[i2 + 4 >> 2] = -1;
   break;
  }
 } while (0);
 i9 = i6;
 tempRet0 = HEAP32[i9 + 4 >> 2] | 0;
 STACKTOP = i5;
 return HEAP32[i9 >> 2] | 0;
}

function _auto_fisbone(i1, i2, i3, i4, i5) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 i5 = i5 | 0;
 var i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0, i17 = 0, i18 = 0;
 i6 = STACKTOP;
 STACKTOP = STACKTOP + 64 | 0;
 i7 = i6 + 52 | 0;
 i8 = i6 + 48 | 0;
 i9 = i6 + 44 | 0;
 i10 = i6 + 40 | 0;
 i11 = i6 + 36 | 0;
 i12 = i6 + 28 | 0;
 i13 = i6 + 24 | 0;
 i14 = i6 + 8 | 0;
 i15 = i6;
 i16 = i6 + 20 | 0;
 i17 = i6 + 16 | 0;
 HEAP32[i8 >> 2] = i1;
 HEAP32[i9 >> 2] = i2;
 HEAP32[i10 >> 2] = i3;
 HEAP32[i11 >> 2] = i4;
 HEAP32[i6 + 32 >> 2] = i5;
 HEAP32[i12 >> 2] = HEAP32[i10 >> 2];
 i10 = i14;
 HEAP32[i10 >> 2] = 0;
 HEAP32[i10 + 4 >> 2] = 0;
 i10 = i15;
 HEAP32[i10 >> 2] = 0;
 HEAP32[i10 + 4 >> 2] = 0;
 if ((HEAP32[i11 >> 2] | 0) < 48) {
  HEAP32[i7 >> 2] = 0;
  i18 = HEAP32[i7 >> 2] | 0;
  STACKTOP = i6;
  return i18 | 0;
 }
 HEAP32[i13 >> 2] = _int32_le_at((HEAP32[i12 >> 2] | 0) + 12 | 0) | 0;
 if (_oggz_stream_has_metric(HEAP32[i8 >> 2] | 0, HEAP32[i13 >> 2] | 0) | 0) {
  HEAP32[i7 >> 2] = 1;
  i18 = HEAP32[i7 >> 2] | 0;
  STACKTOP = i6;
  return i18 | 0;
 } else {
  i11 = _int64_le_at((HEAP32[i12 >> 2] | 0) + 20 | 0) | 0;
  i10 = i14;
  HEAP32[i10 >> 2] = i11;
  HEAP32[i10 + 4 >> 2] = tempRet0;
  i10 = _int64_le_at((HEAP32[i12 >> 2] | 0) + 28 | 0) | 0;
  i11 = i15;
  HEAP32[i11 >> 2] = i10;
  HEAP32[i11 + 4 >> 2] = tempRet0;
  HEAP32[i16 >> 2] = HEAPU8[(HEAP32[i12 >> 2] | 0) + 48 >> 0];
  i12 = HEAP32[i8 >> 2] | 0;
  i11 = HEAP32[i13 >> 2] | 0;
  i10 = i14;
  i14 = HEAP32[i10 >> 2] | 0;
  i5 = HEAP32[i10 + 4 >> 2] | 0;
  i10 = i15;
  i15 = ___muldi3(1e3, 0, HEAP32[i10 >> 2] | 0, HEAP32[i10 + 4 >> 2] | 0) | 0;
  _oggz_set_granulerate(i12, i11, i14, i5, i15, tempRet0) | 0;
  _oggz_set_granuleshift(HEAP32[i8 >> 2] | 0, HEAP32[i13 >> 2] | 0, HEAP32[i16 >> 2] | 0) | 0;
  HEAP32[i17 >> 2] = _oggz_stream_get_numheaders(HEAP32[i8 >> 2] | 0, HEAP32[i9 >> 2] | 0) | 0;
  _oggz_stream_set_numheaders(HEAP32[i8 >> 2] | 0, HEAP32[i9 >> 2] | 0, (HEAP32[i17 >> 2] | 0) + 1 | 0) | 0;
  HEAP32[i7 >> 2] = 1;
  i18 = HEAP32[i7 >> 2] | 0;
  STACKTOP = i6;
  return i18 | 0;
 }
 return 0;
}

function _readPacketCallback(i1, i2, i3, i4) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 var i5 = 0, i6 = 0, i7 = 0, i8 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i1 = i4 + 8 | 0;
 i5 = i4;
 switch (HEAP32[1327] | 0) {
 case 0:
  {
   if (!(HEAP32[i2 + 8 >> 2] | 0)) if (!(HEAP32[1333] | 0)) {
    HEAP32[1327] = 2;
    _ogvjs_callback_loaded_metadata(HEAP32[1325] | 0, HEAP32[1326] | 0);
    i6 = _processDecoding(i2, i3) | 0;
    STACKTOP = i4;
    return i6 | 0;
   } else {
    HEAP32[1327] = 1;
    i6 = _processSkeleton(i2, i3) | 0;
    STACKTOP = i4;
    return i6 | 0;
   }
   i7 = _oggz_stream_get_content(HEAP32[1328] | 0, i3) | 0;
   if (!(HEAP32[1331] | i7)) {
    HEAP32[1325] = 2577;
    HEAP32[1331] = i3;
    _ogvjs_callback_video_packet(HEAP32[i2 >> 2] | 0, HEAP32[i2 + 4 >> 2] | 0, -1.0, -1.0);
    i6 = 0;
    STACKTOP = i4;
    return i6 | 0;
   }
   i8 = (HEAP32[1332] | 0) == 0;
   if ((i7 | 0) == 1 & i8) {
    HEAP32[1326] = 2584;
    HEAP32[1332] = i3;
    _ogvjs_callback_audio_packet(HEAP32[i2 >> 2] | 0, HEAP32[i2 + 4 >> 2] | 0, -1.0);
    i6 = 0;
    STACKTOP = i4;
    return i6 | 0;
   }
   if ((i7 | 0) == 13 & i8) {
    HEAP32[1326] = 2591;
    HEAP32[1332] = i3;
    _ogvjs_callback_audio_packet(HEAP32[i2 >> 2] | 0, HEAP32[i2 + 4 >> 2] | 0, -1.0);
    i6 = 0;
    STACKTOP = i4;
    return i6 | 0;
   }
   if (!((i7 | 0) == 6 & (HEAP32[1333] | 0) == 0)) {
    i6 = 0;
    STACKTOP = i4;
    return i6 | 0;
   }
   HEAP32[1333] = i3;
   i7 = _oggskel_decode_header(HEAP32[1329] | 0, i2) | 0;
   if (!i7) {
    HEAP32[1330] = 1;
    i6 = 0;
    STACKTOP = i4;
    return i6 | 0;
   }
   if ((i7 | 0) > 0) {
    i6 = 0;
    STACKTOP = i4;
    return i6 | 0;
   }
   HEAP32[i5 >> 2] = i7;
   _printf(2596, i5) | 0;
   i6 = -1;
   STACKTOP = i4;
   return i6 | 0;
  }
 case 1:
  {
   i6 = _processSkeleton(i2, i3) | 0;
   STACKTOP = i4;
   return i6 | 0;
  }
 case 2:
  {
   i6 = _processDecoding(i2, i3) | 0;
   STACKTOP = i4;
   return i6 | 0;
  }
 default:
  {
   _printf(2633, i1) | 0;
   i6 = -1;
   STACKTOP = i4;
   return i6 | 0;
  }
 }
 return 0;
}

function _memchr(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0, i17 = 0, i18 = 0, i19 = 0, i20 = 0, i21 = 0, i22 = 0, i23 = 0, i24 = 0, i25 = 0;
 i4 = i2 & 255;
 i5 = (i3 | 0) != 0;
 L1 : do if (i5 & (i1 & 3 | 0) != 0) {
  i6 = i2 & 255;
  i7 = i3;
  i8 = i1;
  while (1) {
   if ((HEAP8[i8 >> 0] | 0) == i6 << 24 >> 24) {
    i9 = i7;
    i10 = i8;
    i11 = 6;
    break L1;
   }
   i12 = i8 + 1 | 0;
   i13 = i7 + -1 | 0;
   i14 = (i13 | 0) != 0;
   if (i14 & (i12 & 3 | 0) != 0) {
    i7 = i13;
    i8 = i12;
   } else {
    i15 = i13;
    i16 = i14;
    i17 = i12;
    i11 = 5;
    break;
   }
  }
 } else {
  i15 = i3;
  i16 = i5;
  i17 = i1;
  i11 = 5;
 } while (0);
 if ((i11 | 0) == 5) if (i16) {
  i9 = i15;
  i10 = i17;
  i11 = 6;
 } else {
  i18 = 0;
  i19 = i17;
 }
 L8 : do if ((i11 | 0) == 6) {
  i17 = i2 & 255;
  if ((HEAP8[i10 >> 0] | 0) == i17 << 24 >> 24) {
   i18 = i9;
   i19 = i10;
  } else {
   i15 = Math_imul(i4, 16843009) | 0;
   L11 : do if (i9 >>> 0 > 3) {
    i16 = i9;
    i1 = i10;
    while (1) {
     i5 = HEAP32[i1 >> 2] ^ i15;
     if ((i5 & -2139062144 ^ -2139062144) & i5 + -16843009 | 0) {
      i20 = i16;
      i21 = i1;
      break;
     }
     i5 = i1 + 4 | 0;
     i3 = i16 + -4 | 0;
     if (i3 >>> 0 > 3) {
      i16 = i3;
      i1 = i5;
     } else {
      i22 = i3;
      i23 = i5;
      i11 = 11;
      break L11;
     }
    }
    i24 = i20;
    i25 = i21;
   } else {
    i22 = i9;
    i23 = i10;
    i11 = 11;
   } while (0);
   if ((i11 | 0) == 11) if (!i22) {
    i18 = 0;
    i19 = i23;
    break;
   } else {
    i24 = i22;
    i25 = i23;
   }
   while (1) {
    if ((HEAP8[i25 >> 0] | 0) == i17 << 24 >> 24) {
     i18 = i24;
     i19 = i25;
     break L8;
    }
    i15 = i25 + 1 | 0;
    i24 = i24 + -1 | 0;
    if (!i24) {
     i18 = 0;
     i19 = i15;
     break;
    } else i25 = i15;
   }
  }
 } while (0);
 return (i18 | 0 ? i19 : 0) | 0;
}

function __oggz_comment_add_byname(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i5 = i4 + 24 | 0;
 i6 = i4 + 20 | 0;
 i7 = i4 + 16 | 0;
 i8 = i4 + 12 | 0;
 i9 = i4 + 8 | 0;
 i10 = i4 + 4 | 0;
 i11 = i4;
 HEAP32[i6 >> 2] = i1;
 HEAP32[i7 >> 2] = i2;
 HEAP32[i8 >> 2] = i3;
 HEAP32[i11 >> 2] = 0;
 L1 : while (1) {
  i3 = HEAP32[i11 >> 2] | 0;
  if ((i3 | 0) >= (_oggz_vector_size(HEAP32[(HEAP32[i6 >> 2] | 0) + 416 >> 2] | 0) | 0)) {
   i12 = 14;
   break;
  }
  HEAP32[i9 >> 2] = _oggz_vector_nth_p(HEAP32[(HEAP32[i6 >> 2] | 0) + 416 >> 2] | 0, HEAP32[i11 >> 2] | 0) | 0;
  do if (HEAP32[HEAP32[i9 >> 2] >> 2] | 0 ? (_strcasecmp(HEAP32[i7 >> 2] | 0, HEAP32[HEAP32[i9 >> 2] >> 2] | 0) | 0) == 0 : 0) {
   i3 = HEAP32[i8 >> 2] | 0;
   if (!(HEAP32[(HEAP32[i9 >> 2] | 0) + 4 >> 2] | 0)) if (!i3) {
    i12 = 7;
    break L1;
   } else break;
   if (i3 | 0 ? (_strcmp(HEAP32[i8 >> 2] | 0, HEAP32[(HEAP32[i9 >> 2] | 0) + 4 >> 2] | 0) | 0) == 0 : 0) {
    i12 = 12;
    break L1;
   }
   if ((HEAP32[i8 >> 2] | 0) == 0 ? (HEAP32[(HEAP32[i9 >> 2] | 0) + 4 >> 2] | 0) == 0 : 0) {
    i12 = 12;
    break L1;
   }
  } while (0);
  HEAP32[i11 >> 2] = (HEAP32[i11 >> 2] | 0) + 1;
 }
 if ((i12 | 0) == 7) {
  HEAP32[i5 >> 2] = HEAP32[i9 >> 2];
  i13 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i13 | 0;
 } else if ((i12 | 0) == 12) {
  HEAP32[i5 >> 2] = HEAP32[i9 >> 2];
  i13 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i13 | 0;
 } else if ((i12 | 0) == 14) {
  i12 = _oggz_comment_new(HEAP32[i7 >> 2] | 0, HEAP32[i8 >> 2] | 0) | 0;
  HEAP32[i10 >> 2] = i12;
  if (!i12) {
   HEAP32[i5 >> 2] = 0;
   i13 = HEAP32[i5 >> 2] | 0;
   STACKTOP = i4;
   return i13 | 0;
  } else {
   HEAP32[i5 >> 2] = _oggz_vector_insert_p(HEAP32[(HEAP32[i6 >> 2] | 0) + 416 >> 2] | 0, HEAP32[i10 >> 2] | 0) | 0;
   i13 = HEAP32[i5 >> 2] | 0;
   STACKTOP = i4;
   return i13 | 0;
  }
 }
 return 0;
}

function _auto_theora(i1, i2, i3, i4, i5) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 i5 = i5 | 0;
 var i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0, i17 = 0, i18 = 0;
 i6 = STACKTOP;
 STACKTOP = STACKTOP + 48 | 0;
 i7 = i6 + 40 | 0;
 i8 = i6 + 36 | 0;
 i9 = i6 + 32 | 0;
 i10 = i6 + 28 | 0;
 i11 = i6 + 24 | 0;
 i12 = i6 + 16 | 0;
 i13 = i6 + 12 | 0;
 i14 = i6 + 8 | 0;
 i15 = i6 + 4 | 0;
 i16 = i6 + 44 | 0;
 i17 = i6;
 HEAP32[i8 >> 2] = i1;
 HEAP32[i9 >> 2] = i2;
 HEAP32[i10 >> 2] = i3;
 HEAP32[i11 >> 2] = i4;
 HEAP32[i6 + 20 >> 2] = i5;
 HEAP32[i12 >> 2] = HEAP32[i10 >> 2];
 HEAP8[i16 >> 0] = 0;
 if ((HEAP32[i11 >> 2] | 0) < 41) {
  HEAP32[i7 >> 2] = 0;
  i18 = HEAP32[i7 >> 2] | 0;
  STACKTOP = i6;
  return i18 | 0;
 }
 HEAP32[i13 >> 2] = (HEAPU8[(HEAP32[i12 >> 2] | 0) + 7 >> 0] << 16) + (HEAPU8[(HEAP32[i12 >> 2] | 0) + 8 >> 0] << 8) + (HEAPU8[(HEAP32[i12 >> 2] | 0) + 9 >> 0] | 0);
 HEAP32[i14 >> 2] = _int32_be_at((HEAP32[i12 >> 2] | 0) + 22 | 0) | 0;
 HEAP32[i15 >> 2] = _int32_be_at((HEAP32[i12 >> 2] | 0) + 26 | 0) | 0;
 if (!(HEAP32[i14 >> 2] | 0)) HEAP32[i14 >> 2] = 1;
 HEAP8[i16 >> 0] = (HEAPU8[(HEAP32[i12 >> 2] | 0) + 40 >> 0] & 3) << 3;
 HEAP8[i16 >> 0] = HEAP8[i16 >> 0] | (HEAPU8[(HEAP32[i12 >> 2] | 0) + 41 >> 0] & 224) >> 5;
 HEAP32[i17 >> 2] = HEAP8[i16 >> 0];
 i16 = HEAP32[i8 >> 2] | 0;
 i12 = HEAP32[i9 >> 2] | 0;
 i11 = HEAP32[i14 >> 2] | 0;
 i14 = HEAP32[i15 >> 2] | 0;
 i15 = ___muldi3(1e3, 0, i14 | 0, ((i14 | 0) < 0) << 31 >> 31 | 0) | 0;
 _oggz_set_granulerate(i16, i12, i11, ((i11 | 0) < 0) << 31 >> 31, i15, tempRet0) | 0;
 _oggz_set_granuleshift(HEAP32[i8 >> 2] | 0, HEAP32[i9 >> 2] | 0, HEAP32[i17 >> 2] | 0) | 0;
 if ((HEAP32[i13 >> 2] | 0) < 197120) _oggz_set_first_granule(HEAP32[i8 >> 2] | 0, HEAP32[i9 >> 2] | 0, -1, -1) | 0;
 _oggz_stream_set_numheaders(HEAP32[i8 >> 2] | 0, HEAP32[i9 >> 2] | 0, 3) | 0;
 HEAP32[i7 >> 2] = 1;
 i18 = HEAP32[i7 >> 2] | 0;
 STACKTOP = i6;
 return i18 | 0;
}

function _auto_vp8(i1, i2, i3, i4, i5) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 i5 = i5 | 0;
 var i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0;
 i6 = STACKTOP;
 STACKTOP = STACKTOP + 48 | 0;
 i7 = i6 + 32 | 0;
 i8 = i6 + 28 | 0;
 i9 = i6 + 24 | 0;
 i10 = i6 + 20 | 0;
 i11 = i6 + 16 | 0;
 i12 = i6 + 8 | 0;
 i13 = i6 + 4 | 0;
 i14 = i6;
 HEAP32[i8 >> 2] = i1;
 HEAP32[i9 >> 2] = i2;
 HEAP32[i10 >> 2] = i3;
 HEAP32[i11 >> 2] = i4;
 HEAP32[i6 + 12 >> 2] = i5;
 HEAP32[i12 >> 2] = HEAP32[i10 >> 2];
 if ((HEAP32[i11 >> 2] | 0) < 26) {
  HEAP32[i7 >> 2] = 0;
  i15 = HEAP32[i7 >> 2] | 0;
  STACKTOP = i6;
  return i15 | 0;
 }
 if ((HEAPU8[HEAP32[i10 >> 2] >> 0] | 0 | 0) != 79) {
  HEAP32[i7 >> 2] = 0;
  i15 = HEAP32[i7 >> 2] | 0;
  STACKTOP = i6;
  return i15 | 0;
 }
 if (_memcmp((HEAP32[i10 >> 2] | 0) + 1 | 0, 2459, 4) | 0) {
  HEAP32[i7 >> 2] = 0;
  i15 = HEAP32[i7 >> 2] | 0;
  STACKTOP = i6;
  return i15 | 0;
 }
 if ((HEAPU8[(HEAP32[i10 >> 2] | 0) + 5 >> 0] | 0 | 0) != 1) {
  HEAP32[i7 >> 2] = 0;
  i15 = HEAP32[i7 >> 2] | 0;
  STACKTOP = i6;
  return i15 | 0;
 }
 if ((HEAPU8[(HEAP32[i10 >> 2] | 0) + 6 >> 0] | 0 | 0) != 1) {
  HEAP32[i7 >> 2] = 0;
  i15 = HEAP32[i7 >> 2] | 0;
  STACKTOP = i6;
  return i15 | 0;
 } else {
  HEAP32[i13 >> 2] = _int32_be_at((HEAP32[i12 >> 2] | 0) + 18 | 0) | 0;
  HEAP32[i14 >> 2] = _int32_be_at((HEAP32[i12 >> 2] | 0) + 22 | 0) | 0;
  i12 = HEAP32[i8 >> 2] | 0;
  i10 = HEAP32[i9 >> 2] | 0;
  i11 = HEAP32[i13 >> 2] | 0;
  i13 = HEAP32[i14 >> 2] | 0;
  i14 = ___muldi3(1e3, 0, i13 | 0, ((i13 | 0) < 0) << 31 >> 31 | 0) | 0;
  _oggz_set_granulerate(i12, i10, i11, ((i11 | 0) < 0) << 31 >> 31, i14, tempRet0) | 0;
  _oggz_set_granuleshift(HEAP32[i8 >> 2] | 0, HEAP32[i9 >> 2] | 0, 32) | 0;
  _oggz_stream_set_numheaders(HEAP32[i8 >> 2] | 0, HEAP32[i9 >> 2] | 0, 1) | 0;
  HEAP32[i7 >> 2] = 1;
  i15 = HEAP32[i7 >> 2] | 0;
  STACKTOP = i6;
  return i15 | 0;
 }
 return 0;
}

function _oggz_metric_default_linear(i1, i2, i3, i4, i5) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 i5 = i5 | 0;
 var i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0, i17 = 0, i18 = 0, i19 = 0;
 i6 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i7 = i6 + 8 | 0;
 i8 = i6 + 28 | 0;
 i9 = i6 + 24 | 0;
 i10 = i6;
 i11 = i6 + 16 | 0;
 HEAP32[i8 >> 2] = i1;
 HEAP32[i9 >> 2] = i2;
 i2 = i10;
 HEAP32[i2 >> 2] = i3;
 HEAP32[i2 + 4 >> 2] = i4;
 HEAP32[i6 + 20 >> 2] = i5;
 HEAP32[i11 >> 2] = _oggz_get_stream(HEAP32[i8 >> 2] | 0, HEAP32[i9 >> 2] | 0) | 0;
 if (!(HEAP32[i11 >> 2] | 0)) {
  i9 = i7;
  HEAP32[i9 >> 2] = -1;
  HEAP32[i9 + 4 >> 2] = -1;
  i12 = i7;
  i13 = i12;
  i14 = HEAP32[i13 >> 2] | 0;
  i15 = i12 + 4 | 0;
  i16 = i15;
  i17 = HEAP32[i16 >> 2] | 0;
  tempRet0 = i17;
  STACKTOP = i6;
  return i14 | 0;
 }
 i9 = i10;
 i8 = HEAP32[i9 + 4 >> 2] | 0;
 i5 = (HEAP32[i11 >> 2] | 0) + 392 | 0;
 i4 = HEAP32[i5 + 4 >> 2] | 0;
 if ((i8 | 0) < (i4 | 0) | ((i8 | 0) == (i4 | 0) ? (HEAP32[i9 >> 2] | 0) >>> 0 <= (HEAP32[i5 >> 2] | 0) >>> 0 : 0)) {
  i18 = 0;
  i19 = 0;
 } else {
  i5 = i10;
  i9 = (HEAP32[i11 >> 2] | 0) + 392 | 0;
  i4 = _i64Subtract(HEAP32[i5 >> 2] | 0, HEAP32[i5 + 4 >> 2] | 0, HEAP32[i9 >> 2] | 0, HEAP32[i9 + 4 >> 2] | 0) | 0;
  i18 = i4;
  i19 = tempRet0;
 }
 i4 = i10;
 HEAP32[i4 >> 2] = i18;
 HEAP32[i4 + 4 >> 2] = i19;
 i19 = (HEAP32[i11 >> 2] | 0) + 384 | 0;
 i4 = i10;
 i10 = ___muldi3(HEAP32[i19 >> 2] | 0, HEAP32[i19 + 4 >> 2] | 0, HEAP32[i4 >> 2] | 0, HEAP32[i4 + 4 >> 2] | 0) | 0;
 i4 = (HEAP32[i11 >> 2] | 0) + 376 | 0;
 i11 = ___divdi3(i10 | 0, tempRet0 | 0, HEAP32[i4 >> 2] | 0, HEAP32[i4 + 4 >> 2] | 0) | 0;
 i4 = i7;
 HEAP32[i4 >> 2] = i11;
 HEAP32[i4 + 4 >> 2] = tempRet0;
 i12 = i7;
 i13 = i12;
 i14 = HEAP32[i13 >> 2] | 0;
 i15 = i12 + 4 | 0;
 i16 = i15;
 i17 = HEAP32[i16 >> 2] | 0;
 tempRet0 = i17;
 STACKTOP = i6;
 return i14 | 0;
}

function _oggz_metric_vp8(i1, i2, i3, i4, i5) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 i5 = i5 | 0;
 var i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0, i17 = 0, i18 = 0, i19 = 0;
 i6 = STACKTOP;
 STACKTOP = STACKTOP + 48 | 0;
 i7 = i6 + 24 | 0;
 i8 = i6 + 44 | 0;
 i9 = i6 + 40 | 0;
 i10 = i6 + 16 | 0;
 i11 = i6 + 32 | 0;
 i12 = i6 + 8 | 0;
 i13 = i6;
 HEAP32[i8 >> 2] = i1;
 HEAP32[i9 >> 2] = i2;
 i2 = i10;
 HEAP32[i2 >> 2] = i3;
 HEAP32[i2 + 4 >> 2] = i4;
 HEAP32[i6 + 36 >> 2] = i5;
 HEAP32[i11 >> 2] = _oggz_get_stream(HEAP32[i8 >> 2] | 0, HEAP32[i9 >> 2] | 0) | 0;
 if (!(HEAP32[i11 >> 2] | 0)) {
  i9 = i7;
  HEAP32[i9 >> 2] = -1;
  HEAP32[i9 + 4 >> 2] = -1;
  i14 = i7;
  i15 = i14;
  i16 = HEAP32[i15 >> 2] | 0;
  i17 = i14 + 4 | 0;
  i18 = i17;
  i19 = HEAP32[i18 >> 2] | 0;
  tempRet0 = i19;
  STACKTOP = i6;
  return i16 | 0;
 } else {
  i9 = i10;
  i10 = _bitshift64Ashr(HEAP32[i9 >> 2] | 0, HEAP32[i9 + 4 >> 2] | 0, HEAP32[(HEAP32[i11 >> 2] | 0) + 408 >> 2] | 0) | 0;
  i9 = i12;
  HEAP32[i9 >> 2] = i10;
  HEAP32[i9 + 4 >> 2] = tempRet0;
  i9 = i12;
  i12 = (HEAP32[i11 >> 2] | 0) + 384 | 0;
  i10 = ___muldi3(HEAP32[i9 >> 2] | 0, HEAP32[i9 + 4 >> 2] | 0, HEAP32[i12 >> 2] | 0, HEAP32[i12 + 4 >> 2] | 0) | 0;
  i12 = (HEAP32[i11 >> 2] | 0) + 376 | 0;
  i11 = ___divdi3(i10 | 0, tempRet0 | 0, HEAP32[i12 >> 2] | 0, HEAP32[i12 + 4 >> 2] | 0) | 0;
  i12 = i13;
  HEAP32[i12 >> 2] = i11;
  HEAP32[i12 + 4 >> 2] = tempRet0;
  i12 = i13;
  i13 = HEAP32[i12 + 4 >> 2] | 0;
  i11 = i7;
  HEAP32[i11 >> 2] = HEAP32[i12 >> 2];
  HEAP32[i11 + 4 >> 2] = i13;
  i14 = i7;
  i15 = i14;
  i16 = HEAP32[i15 >> 2] | 0;
  i17 = i14 + 4 | 0;
  i18 = i17;
  i19 = HEAP32[i18 >> 2] | 0;
  tempRet0 = i19;
  STACKTOP = i6;
  return i16 | 0;
 }
 return 0;
}

function _oggz_metric_update(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i4 = i3 + 12 | 0;
 i5 = i3 + 8 | 0;
 i6 = i3 + 4 | 0;
 i7 = i3;
 HEAP32[i5 >> 2] = i1;
 HEAP32[i6 >> 2] = i2;
 if (!(HEAP32[i5 >> 2] | 0)) {
  HEAP32[i4 >> 2] = -2;
  i8 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i8 | 0;
 }
 HEAP32[i7 >> 2] = _oggz_get_stream(HEAP32[i5 >> 2] | 0, HEAP32[i6 >> 2] | 0) | 0;
 if (!(HEAP32[i7 >> 2] | 0)) {
  HEAP32[i4 >> 2] = -20;
  i8 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i8 | 0;
 }
 i2 = (HEAP32[i7 >> 2] | 0) + 376 | 0;
 if ((HEAP32[i2 >> 2] | 0) == 0 & (HEAP32[i2 + 4 >> 2] | 0) == 0) {
  i2 = (HEAP32[i7 >> 2] | 0) + 376 | 0;
  HEAP32[i2 >> 2] = 1;
  HEAP32[i2 + 4 >> 2] = 0;
  i2 = (HEAP32[i7 >> 2] | 0) + 384 | 0;
  HEAP32[i2 >> 2] = 0;
  HEAP32[i2 + 4 >> 2] = 0;
 }
 i2 = HEAP32[i5 >> 2] | 0;
 i1 = HEAP32[i6 >> 2] | 0;
 if (!(HEAP32[(HEAP32[i7 >> 2] | 0) + 408 >> 2] | 0)) {
  HEAP32[i4 >> 2] = _oggz_set_metric_internal(i2, i1, 19, 0, 1) | 0;
  i8 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i8 | 0;
 }
 i7 = (_oggz_stream_get_content(i2, i1) | 0) == 12;
 i1 = HEAP32[i5 >> 2] | 0;
 i2 = HEAP32[i6 >> 2] | 0;
 if (i7) {
  HEAP32[i4 >> 2] = _oggz_set_metric_internal(i1, i2, 20, 0, 1) | 0;
  i8 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i8 | 0;
 }
 i7 = (_oggz_stream_get_content(i1, i2) | 0) == 14;
 i2 = HEAP32[i5 >> 2] | 0;
 i5 = HEAP32[i6 >> 2] | 0;
 if (i7) {
  HEAP32[i4 >> 2] = _oggz_set_metric_internal(i2, i5, 21, 0, 1) | 0;
  i8 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i8 | 0;
 } else {
  HEAP32[i4 >> 2] = _oggz_set_metric_internal(i2, i5, 22, 0, 1) | 0;
  i8 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i8 | 0;
 }
 return 0;
}

function _auto_rcalc_theora(i1, i2, i3, i4, i5) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 i5 = i5 | 0;
 var i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0, i17 = 0, i18 = 0;
 i6 = STACKTOP;
 STACKTOP = STACKTOP + 48 | 0;
 i7 = i6 + 8 | 0;
 i8 = i6;
 i9 = i6 + 32 | 0;
 i10 = i6 + 20 | 0;
 i11 = i6 + 16 | 0;
 i12 = i8;
 HEAP32[i12 >> 2] = i1;
 HEAP32[i12 + 4 >> 2] = i2;
 HEAP32[i9 >> 2] = i3;
 HEAP32[i6 + 28 >> 2] = i4;
 HEAP32[i6 + 24 >> 2] = i5;
 i5 = i8;
 i4 = _bitshift64Ashr(HEAP32[i5 >> 2] | 0, HEAP32[i5 + 4 >> 2] | 0, HEAP32[(HEAP32[i9 >> 2] | 0) + 408 >> 2] | 0) | 0;
 HEAP32[i10 >> 2] = i4;
 i4 = i8;
 i8 = HEAP32[i10 >> 2] << HEAP32[(HEAP32[i9 >> 2] | 0) + 408 >> 2];
 i5 = _i64Subtract(HEAP32[i4 >> 2] | 0, HEAP32[i4 + 4 >> 2] | 0, i8 | 0, ((i8 | 0) < 0) << 31 >> 31 | 0) | 0;
 HEAP32[i11 >> 2] = i5;
 i5 = HEAP32[i10 >> 2] | 0;
 if (!(HEAP32[i11 >> 2] | 0)) {
  i10 = (i5 - 60 << HEAP32[(HEAP32[i9 >> 2] | 0) + 408 >> 2]) + 59 | 0;
  i8 = i7;
  HEAP32[i8 >> 2] = i10;
  HEAP32[i8 + 4 >> 2] = ((i10 | 0) < 0) << 31 >> 31;
  i13 = i7;
  i14 = i13;
  i15 = HEAP32[i14 >> 2] | 0;
  i16 = i13 + 4 | 0;
  i17 = i16;
  i18 = HEAP32[i17 >> 2] | 0;
  tempRet0 = i18;
  STACKTOP = i6;
  return i15 | 0;
 } else {
  i10 = _bitshift64Shl(i5 | 0, ((i5 | 0) < 0) << 31 >> 31 | 0, HEAP32[(HEAP32[i9 >> 2] | 0) + 408 >> 2] | 0) | 0;
  i9 = (HEAP32[i11 >> 2] | 0) - 1 | 0;
  i11 = _i64Add(i10 | 0, tempRet0 | 0, i9 | 0, ((i9 | 0) < 0) << 31 >> 31 | 0) | 0;
  i9 = i7;
  HEAP32[i9 >> 2] = i11;
  HEAP32[i9 + 4 >> 2] = tempRet0;
  i13 = i7;
  i14 = i13;
  i15 = HEAP32[i14 >> 2] | 0;
  i16 = i13 + 4 | 0;
  i17 = i16;
  i18 = HEAP32[i17 >> 2] | 0;
  tempRet0 = i18;
  STACKTOP = i6;
  return i15 | 0;
 }
 return 0;
}

function _auto_cmml(i1, i2, i3, i4, i5) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 i5 = i5 | 0;
 var i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0;
 i6 = STACKTOP;
 STACKTOP = STACKTOP + 48 | 0;
 i7 = i6 + 44 | 0;
 i8 = i6 + 40 | 0;
 i9 = i6 + 36 | 0;
 i10 = i6 + 32 | 0;
 i11 = i6 + 28 | 0;
 i12 = i6 + 20 | 0;
 i13 = i6 + 8 | 0;
 i14 = i6;
 i15 = i6 + 16 | 0;
 HEAP32[i8 >> 2] = i1;
 HEAP32[i9 >> 2] = i2;
 HEAP32[i10 >> 2] = i3;
 HEAP32[i11 >> 2] = i4;
 HEAP32[i6 + 24 >> 2] = i5;
 HEAP32[i12 >> 2] = HEAP32[i10 >> 2];
 i10 = i13;
 HEAP32[i10 >> 2] = 0;
 HEAP32[i10 + 4 >> 2] = 0;
 i10 = i14;
 HEAP32[i10 >> 2] = 0;
 HEAP32[i10 + 4 >> 2] = 0;
 if ((HEAP32[i11 >> 2] | 0) < 28) {
  HEAP32[i7 >> 2] = 0;
  i16 = HEAP32[i7 >> 2] | 0;
  STACKTOP = i6;
  return i16 | 0;
 }
 i10 = _int64_le_at((HEAP32[i12 >> 2] | 0) + 12 | 0) | 0;
 i5 = i13;
 HEAP32[i5 >> 2] = i10;
 HEAP32[i5 + 4 >> 2] = tempRet0;
 i5 = _int64_le_at((HEAP32[i12 >> 2] | 0) + 20 | 0) | 0;
 i10 = i14;
 HEAP32[i10 >> 2] = i5;
 HEAP32[i10 + 4 >> 2] = tempRet0;
 if ((HEAP32[i11 >> 2] | 0) > 28) HEAP32[i15 >> 2] = HEAPU8[(HEAP32[i12 >> 2] | 0) + 28 >> 0]; else HEAP32[i15 >> 2] = 0;
 i12 = HEAP32[i8 >> 2] | 0;
 i11 = HEAP32[i9 >> 2] | 0;
 i10 = i13;
 i13 = HEAP32[i10 >> 2] | 0;
 i5 = HEAP32[i10 + 4 >> 2] | 0;
 i10 = i14;
 i14 = ___muldi3(1e3, 0, HEAP32[i10 >> 2] | 0, HEAP32[i10 + 4 >> 2] | 0) | 0;
 _oggz_set_granulerate(i12, i11, i13, i5, i14, tempRet0) | 0;
 _oggz_set_granuleshift(HEAP32[i8 >> 2] | 0, HEAP32[i9 >> 2] | 0, HEAP32[i15 >> 2] | 0) | 0;
 _oggz_stream_set_numheaders(HEAP32[i8 >> 2] | 0, HEAP32[i9 >> 2] | 0, 3) | 0;
 HEAP32[i7 >> 2] = 1;
 i16 = HEAP32[i7 >> 2] | 0;
 STACKTOP = i6;
 return i16 | 0;
}

function _dirac_bs_read(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i4 = i3 + 16 | 0;
 i5 = i3 + 12 | 0;
 i6 = i3 + 8 | 0;
 i7 = i3 + 4 | 0;
 i8 = i3;
 HEAP32[i5 >> 2] = i1;
 HEAP32[i6 >> 2] = i2;
 HEAP32[i8 >> 2] = 0;
 while (1) {
  if ((HEAP32[i6 >> 2] | 0) <= 0) {
   i9 = 9;
   break;
  }
  if ((HEAP32[(HEAP32[i5 >> 2] | 0) + 4 >> 2] | 0) >>> 0 >= (HEAP32[(HEAP32[i5 >> 2] | 0) + 8 >> 2] | 0) >>> 0) {
   i9 = 9;
   break;
  }
  i2 = (HEAP32[(HEAP32[i5 >> 2] | 0) + 12 >> 2] | 0) - (HEAP32[i6 >> 2] | 0) | 0;
  HEAP32[i7 >> 2] = i2;
  i10 = HEAPU8[HEAP32[(HEAP32[i5 >> 2] | 0) + 4 >> 2] >> 0] | 0;
  if ((i2 | 0) >= 0) break;
  HEAP32[i8 >> 2] = HEAP32[i8 >> 2] | (i10 & HEAP32[1416 + (HEAP32[(HEAP32[i5 >> 2] | 0) + 12 >> 2] << 2) >> 2]) << 0 - (HEAP32[i7 >> 2] | 0);
  HEAP32[i6 >> 2] = (HEAP32[i6 >> 2] | 0) - (HEAP32[(HEAP32[i5 >> 2] | 0) + 12 >> 2] | 0);
  i2 = (HEAP32[i5 >> 2] | 0) + 4 | 0;
  HEAP32[i2 >> 2] = (HEAP32[i2 >> 2] | 0) + 1;
  HEAP32[(HEAP32[i5 >> 2] | 0) + 12 >> 2] = 8;
 }
 if ((i9 | 0) == 9) {
  HEAP32[i4 >> 2] = HEAP32[i8 >> 2];
  i11 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i11 | 0;
 }
 HEAP32[i8 >> 2] = HEAP32[i8 >> 2] | i10 >> HEAP32[i7 >> 2] & HEAP32[1416 + (HEAP32[i6 >> 2] << 2) >> 2];
 i7 = (HEAP32[i5 >> 2] | 0) + 12 | 0;
 HEAP32[i7 >> 2] = (HEAP32[i7 >> 2] | 0) - (HEAP32[i6 >> 2] | 0);
 if (!(HEAP32[(HEAP32[i5 >> 2] | 0) + 12 >> 2] | 0)) {
  i6 = (HEAP32[i5 >> 2] | 0) + 4 | 0;
  HEAP32[i6 >> 2] = (HEAP32[i6 >> 2] | 0) + 1;
  HEAP32[(HEAP32[i5 >> 2] | 0) + 12 >> 2] = 8;
 }
 HEAP32[i4 >> 2] = HEAP32[i8 >> 2];
 i11 = HEAP32[i4 >> 2] | 0;
 STACKTOP = i3;
 return i11 | 0;
}

function _processSkeleton(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, d8 = 0.0, i9 = 0, i10 = 0, i11 = 0, d12 = 0.0, i13 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i4 = i3 + 16 | 0;
 i5 = i3 + 8 | 0;
 i6 = i3;
 i7 = _oggz_tell_units(HEAP32[1328] | 0) | 0;
 d8 = (+(i7 >>> 0) + 4294967296.0 * +(tempRet0 | 0)) / 1.0e3;
 i7 = _oggz_tell_granulepos(HEAP32[1328] | 0) | 0;
 i9 = tempRet0;
 i10 = _oggz_get_granuleshift(HEAP32[1328] | 0, i2) | 0;
 i11 = i5;
 HEAP32[i11 >> 2] = 0;
 HEAP32[i11 + 4 >> 2] = 0;
 i11 = i6;
 HEAP32[i11 >> 2] = 0;
 HEAP32[i11 + 4 >> 2] = 0;
 _oggz_get_granulerate(HEAP32[1328] | 0, i2, i5, i6) | 0;
 i11 = _bitshift64Ashr(i7 | 0, i9 | 0, i10 | 0) | 0;
 i10 = i6;
 i6 = i5;
 d12 = (+(i11 >>> 0) + 4294967296.0 * +(tempRet0 | 0)) * (+((HEAP32[i10 >> 2] | 0) >>> 0) + 4294967296.0 * +(HEAP32[i10 + 4 >> 2] | 0)) / (+((HEAP32[i6 >> 2] | 0) >>> 0) + 4294967296.0 * +(HEAP32[i6 + 4 >> 2] | 0));
 do if ((HEAP32[1333] | 0) == (i2 | 0)) {
  i6 = _oggskel_decode_header(HEAP32[1329] | 0, i1) | 0;
  if ((i6 | 0) < 0) {
   HEAP32[i4 >> 2] = i6;
   _printf(2539, i4) | 0;
   i13 = -1;
   STACKTOP = i3;
   return i13 | 0;
  } else {
   if (!(HEAP32[i1 + 12 >> 2] | 0)) break;
   HEAP32[1330] = 1;
   HEAP32[1327] = 2;
   _ogvjs_callback_loaded_metadata(HEAP32[1325] | 0, HEAP32[1326] | 0);
   break;
  }
 } while (0);
 if ((HEAP32[1331] | 0) == (i2 | 0)) _ogvjs_callback_video_packet(HEAP32[i1 >> 2] | 0, HEAP32[i1 + 4 >> 2] | 0, +d8, +d12);
 if ((HEAP32[1332] | 0) != (i2 | 0)) {
  i13 = 0;
  STACKTOP = i3;
  return i13 | 0;
 }
 _ogvjs_callback_audio_packet(HEAP32[i1 >> 2] | 0, HEAP32[i1 + 4 >> 2] | 0, +d8);
 i13 = 0;
 STACKTOP = i3;
 return i13 | 0;
}

function _oggz_new(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0, i5 = 0, i6 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2 + 8 | 0;
 i4 = i2 + 4 | 0;
 i5 = i2;
 HEAP32[i4 >> 2] = i1;
 if (_oggz_flags_disabled(HEAP32[i4 >> 2] | 0) | 0) {
  HEAP32[i3 >> 2] = 0;
  i6 = HEAP32[i3 >> 2] | 0;
  STACKTOP = i2;
  return i6 | 0;
 }
 HEAP32[i5 >> 2] = _malloc(568) | 0;
 if (!(HEAP32[i5 >> 2] | 0)) {
  HEAP32[i3 >> 2] = 0;
  i6 = HEAP32[i3 >> 2] | 0;
  STACKTOP = i2;
  return i6 | 0;
 }
 HEAP32[HEAP32[i5 >> 2] >> 2] = HEAP32[i4 >> 2];
 HEAP32[(HEAP32[i5 >> 2] | 0) + 4 >> 2] = 0;
 HEAP32[(HEAP32[i5 >> 2] | 0) + 8 >> 2] = 0;
 HEAP32[(HEAP32[i5 >> 2] | 0) + 64 >> 2] = 0;
 HEAP32[(HEAP32[i5 >> 2] | 0) + 68 >> 2] = 0;
 HEAP32[(HEAP32[i5 >> 2] | 0) + 72 >> 2] = 1024;
 HEAP32[(HEAP32[i5 >> 2] | 0) + 76 >> 2] = 0;
 i4 = _oggz_vector_new() | 0;
 HEAP32[(HEAP32[i5 >> 2] | 0) + 80 >> 2] = i4;
 do if (HEAP32[(HEAP32[i5 >> 2] | 0) + 80 >> 2] | 0) {
  HEAP32[(HEAP32[i5 >> 2] | 0) + 84 >> 2] = 0;
  HEAP32[(HEAP32[i5 >> 2] | 0) + 88 >> 2] = 0;
  HEAP32[(HEAP32[i5 >> 2] | 0) + 92 >> 2] = 0;
  HEAP32[(HEAP32[i5 >> 2] | 0) + 96 >> 2] = 0;
  HEAP32[(HEAP32[i5 >> 2] | 0) + 100 >> 2] = 0;
  HEAP32[(HEAP32[i5 >> 2] | 0) + 104 >> 2] = 0;
  i4 = _oggz_dlist_new() | 0;
  HEAP32[(HEAP32[i5 >> 2] | 0) + 560 >> 2] = i4;
  i4 = HEAP32[i5 >> 2] | 0;
  if (!(HEAP32[(HEAP32[i5 >> 2] | 0) + 560 >> 2] | 0)) {
   _free(HEAP32[i4 + 80 >> 2] | 0);
   break;
  }
  _oggz_read_init(i4) | 0;
  HEAP32[i3 >> 2] = HEAP32[i5 >> 2];
  i6 = HEAP32[i3 >> 2] | 0;
  STACKTOP = i2;
  return i6 | 0;
 } while (0);
 _free(HEAP32[i5 >> 2] | 0);
 HEAP32[i3 >> 2] = 0;
 i6 = HEAP32[i3 >> 2] | 0;
 STACKTOP = i2;
 return i6 | 0;
}

function _oggz_read_update_gp(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2 + 12 | 0;
 i4 = i2 + 8 | 0;
 i5 = i2 + 4 | 0;
 i6 = i2;
 HEAP32[i4 >> 2] = i1;
 HEAP32[i5 >> 2] = HEAP32[i4 >> 2];
 i4 = (HEAP32[i5 >> 2] | 0) + 32 | 0;
 do if (((HEAP32[i4 >> 2] | 0) == -1 ? (HEAP32[i4 + 4 >> 2] | 0) == -1 : 0) ? (i1 = (HEAP32[(HEAP32[i5 >> 2] | 0) + 56 >> 2] | 0) + 488 | 0, (HEAP32[i1 >> 2] | 0) != -1 ? 1 : (HEAP32[i1 + 4 >> 2] | 0) != -1) : 0) {
  HEAP32[i6 >> 2] = _oggz_stream_get_content(HEAP32[(HEAP32[i5 >> 2] | 0) + 64 >> 2] | 0, HEAP32[(HEAP32[i5 >> 2] | 0) + 68 >> 2] | 0) | 0;
  if (!((HEAP32[i6 >> 2] | 0) < 0 | (HEAP32[i6 >> 2] | 0) >= 15)) {
   i1 = (HEAP32[(HEAP32[i5 >> 2] | 0) + 56 >> 2] | 0) + 488 | 0;
   i7 = _oggz_auto_calculate_gp_backwards(HEAP32[i6 >> 2] | 0, HEAP32[i1 >> 2] | 0, HEAP32[i1 + 4 >> 2] | 0, HEAP32[(HEAP32[i5 >> 2] | 0) + 56 >> 2] | 0, HEAP32[i5 >> 2] | 0, HEAP32[(HEAP32[(HEAP32[i5 >> 2] | 0) + 56 >> 2] | 0) + 508 >> 2] | 0) | 0;
   i1 = (HEAP32[i5 >> 2] | 0) + 32 | 0;
   HEAP32[i1 >> 2] = i7;
   HEAP32[i1 + 4 >> 2] = tempRet0;
   i1 = (HEAP32[i5 >> 2] | 0) + 32 | 0;
   i7 = HEAP32[i1 + 4 >> 2] | 0;
   i8 = (HEAP32[(HEAP32[i5 >> 2] | 0) + 56 >> 2] | 0) + 488 | 0;
   HEAP32[i8 >> 2] = HEAP32[i1 >> 2];
   HEAP32[i8 + 4 >> 2] = i7;
   HEAP32[(HEAP32[(HEAP32[i5 >> 2] | 0) + 56 >> 2] | 0) + 508 >> 2] = HEAP32[i5 >> 2];
   break;
  }
  HEAP32[i3 >> 2] = 0;
  i9 = HEAP32[i3 >> 2] | 0;
  STACKTOP = i2;
  return i9 | 0;
 } while (0);
 HEAP32[i3 >> 2] = 1;
 i9 = HEAP32[i3 >> 2] | 0;
 STACKTOP = i2;
 return i9 | 0;
}

function _oggz_set_metric_internal(i1, i2, i3, i4, i5) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 i5 = i5 | 0;
 var i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0;
 i6 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i7 = i6 + 24 | 0;
 i8 = i6 + 20 | 0;
 i9 = i6 + 16 | 0;
 i10 = i6 + 12 | 0;
 i11 = i6 + 8 | 0;
 i12 = i6 + 4 | 0;
 i13 = i6;
 HEAP32[i8 >> 2] = i1;
 HEAP32[i9 >> 2] = i2;
 HEAP32[i10 >> 2] = i3;
 HEAP32[i11 >> 2] = i4;
 HEAP32[i12 >> 2] = i5;
 if (!(HEAP32[i8 >> 2] | 0)) {
  HEAP32[i7 >> 2] = -2;
  i14 = HEAP32[i7 >> 2] | 0;
  STACKTOP = i6;
  return i14 | 0;
 }
 i5 = HEAP32[i8 >> 2] | 0;
 if ((HEAP32[i9 >> 2] | 0) == -1) {
  if (HEAP32[i5 + 96 >> 2] | 0 ? HEAP32[(HEAP32[i8 >> 2] | 0) + 92 >> 2] | 0 : 0) _free(HEAP32[(HEAP32[i8 >> 2] | 0) + 92 >> 2] | 0);
  HEAP32[(HEAP32[i8 >> 2] | 0) + 88 >> 2] = HEAP32[i10 >> 2];
  HEAP32[(HEAP32[i8 >> 2] | 0) + 92 >> 2] = HEAP32[i11 >> 2];
  HEAP32[(HEAP32[i8 >> 2] | 0) + 96 >> 2] = HEAP32[i12 >> 2];
 } else {
  HEAP32[i13 >> 2] = _oggz_get_stream(i5, HEAP32[i9 >> 2] | 0) | 0;
  if (!(HEAP32[i13 >> 2] | 0)) {
   HEAP32[i7 >> 2] = -20;
   i14 = HEAP32[i7 >> 2] | 0;
   STACKTOP = i6;
   return i14 | 0;
  }
  if (HEAP32[(HEAP32[i13 >> 2] | 0) + 456 >> 2] | 0 ? HEAP32[(HEAP32[i13 >> 2] | 0) + 452 >> 2] | 0 : 0) _free(HEAP32[(HEAP32[i13 >> 2] | 0) + 452 >> 2] | 0);
  HEAP32[(HEAP32[i13 >> 2] | 0) + 448 >> 2] = HEAP32[i10 >> 2];
  HEAP32[(HEAP32[i13 >> 2] | 0) + 452 >> 2] = HEAP32[i11 >> 2];
  HEAP32[(HEAP32[i13 >> 2] | 0) + 456 >> 2] = HEAP32[i12 >> 2];
 }
 HEAP32[i7 >> 2] = 0;
 i14 = HEAP32[i7 >> 2] | 0;
 STACKTOP = i6;
 return i14 | 0;
}

function _oggz_read_new_pbuffer_entry(i1, i2, i3, i4, i5) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 i5 = i5 | 0;
 var i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0;
 i6 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i7 = i6 + 28 | 0;
 i8 = i6 + 24 | 0;
 i9 = i6 + 20 | 0;
 i10 = i6 + 16 | 0;
 i11 = i6 + 12 | 0;
 i12 = i6 + 8 | 0;
 i13 = i6 + 4 | 0;
 i14 = i6;
 HEAP32[i8 >> 2] = i1;
 HEAP32[i9 >> 2] = i2;
 HEAP32[i10 >> 2] = i3;
 HEAP32[i11 >> 2] = i4;
 HEAP32[i12 >> 2] = i5;
 HEAP32[i14 >> 2] = HEAP32[i9 >> 2];
 i5 = _malloc(72) | 0;
 HEAP32[i13 >> 2] = i5;
 if (!i5) {
  HEAP32[i7 >> 2] = 0;
  i15 = HEAP32[i7 >> 2] | 0;
  STACKTOP = i6;
  return i15 | 0;
 }
 i5 = HEAP32[i13 >> 2] | 0;
 i4 = HEAP32[i9 >> 2] | 0;
 i9 = i5 + 56 | 0;
 do {
  HEAP32[i5 >> 2] = HEAP32[i4 >> 2];
  i5 = i5 + 4 | 0;
  i4 = i4 + 4 | 0;
 } while ((i5 | 0) < (i9 | 0));
 i4 = _malloc(HEAP32[(HEAP32[i14 >> 2] | 0) + 4 >> 2] | 0) | 0;
 HEAP32[HEAP32[i13 >> 2] >> 2] = i4;
 i5 = HEAP32[i13 >> 2] | 0;
 if (!i4) {
  _free(i5);
  HEAP32[i7 >> 2] = 0;
  i15 = HEAP32[i7 >> 2] | 0;
  STACKTOP = i6;
  return i15 | 0;
 } else {
  _memcpy(HEAP32[i5 >> 2] | 0, HEAP32[HEAP32[i14 >> 2] >> 2] | 0, HEAP32[(HEAP32[i14 >> 2] | 0) + 4 >> 2] | 0) | 0;
  HEAP32[(HEAP32[i13 >> 2] | 0) + 56 >> 2] = HEAP32[i11 >> 2];
  HEAP32[(HEAP32[i13 >> 2] | 0) + 68 >> 2] = HEAP32[i10 >> 2];
  HEAP32[(HEAP32[i13 >> 2] | 0) + 60 >> 2] = HEAP32[i12 >> 2];
  HEAP32[(HEAP32[i13 >> 2] | 0) + 64 >> 2] = HEAP32[i8 >> 2];
  HEAP32[i7 >> 2] = HEAP32[i13 >> 2];
  i15 = HEAP32[i7 >> 2] | 0;
  STACKTOP = i6;
  return i15 | 0;
 }
 return 0;
}

function _auto_kate(i1, i2, i3, i4, i5) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 i5 = i5 | 0;
 var i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0, i17 = 0;
 i6 = STACKTOP;
 STACKTOP = STACKTOP + 48 | 0;
 i7 = i6 + 36 | 0;
 i8 = i6 + 32 | 0;
 i9 = i6 + 28 | 0;
 i10 = i6 + 24 | 0;
 i11 = i6 + 20 | 0;
 i12 = i6 + 12 | 0;
 i13 = i6 + 8 | 0;
 i14 = i6 + 4 | 0;
 i15 = i6 + 40 | 0;
 i16 = i6;
 HEAP32[i8 >> 2] = i1;
 HEAP32[i9 >> 2] = i2;
 HEAP32[i10 >> 2] = i3;
 HEAP32[i11 >> 2] = i4;
 HEAP32[i6 + 16 >> 2] = i5;
 HEAP32[i12 >> 2] = HEAP32[i10 >> 2];
 HEAP8[i15 >> 0] = 0;
 if ((HEAP32[i11 >> 2] | 0) < 64) {
  HEAP32[i7 >> 2] = 0;
  i17 = HEAP32[i7 >> 2] | 0;
  STACKTOP = i6;
  return i17 | 0;
 } else {
  HEAP32[i13 >> 2] = _int32_le_at((HEAP32[i12 >> 2] | 0) + 24 | 0) | 0;
  HEAP32[i14 >> 2] = _int32_le_at((HEAP32[i12 >> 2] | 0) + 28 | 0) | 0;
  HEAP8[i15 >> 0] = HEAP8[(HEAP32[i12 >> 2] | 0) + 15 >> 0] | 0;
  HEAP32[i16 >> 2] = HEAPU8[(HEAP32[i12 >> 2] | 0) + 11 >> 0];
  i12 = HEAP32[i8 >> 2] | 0;
  i11 = HEAP32[i9 >> 2] | 0;
  i10 = HEAP32[i13 >> 2] | 0;
  i13 = HEAP32[i14 >> 2] | 0;
  i14 = ___muldi3(1e3, 0, i13 | 0, ((i13 | 0) < 0) << 31 >> 31 | 0) | 0;
  _oggz_set_granulerate(i12, i11, i10, ((i10 | 0) < 0) << 31 >> 31, i14, tempRet0) | 0;
  _oggz_set_granuleshift(HEAP32[i8 >> 2] | 0, HEAP32[i9 >> 2] | 0, HEAPU8[i15 >> 0] | 0) | 0;
  _oggz_stream_set_numheaders(HEAP32[i8 >> 2] | 0, HEAP32[i9 >> 2] | 0, HEAP32[i16 >> 2] | 0) | 0;
  HEAP32[i7 >> 2] = 1;
  i17 = HEAP32[i7 >> 2] | 0;
  STACKTOP = i6;
  return i17 | 0;
 }
 return 0;
}

function _vfprintf(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0, i17 = 0, i18 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 224 | 0;
 i5 = i4 + 120 | 0;
 i6 = i4 + 80 | 0;
 i7 = i4;
 i8 = i4 + 136 | 0;
 i9 = i6;
 i10 = i9 + 40 | 0;
 do {
  HEAP32[i9 >> 2] = 0;
  i9 = i9 + 4 | 0;
 } while ((i9 | 0) < (i10 | 0));
 HEAP32[i5 >> 2] = HEAP32[i3 >> 2];
 if ((_printf_core(0, i2, i5, i7, i6) | 0) < 0) i11 = -1; else {
  if ((HEAP32[i1 + 76 >> 2] | 0) > -1) i12 = ___lockfile(i1) | 0; else i12 = 0;
  i3 = HEAP32[i1 >> 2] | 0;
  i9 = i3 & 32;
  if ((HEAP8[i1 + 74 >> 0] | 0) < 1) HEAP32[i1 >> 2] = i3 & -33;
  i3 = i1 + 48 | 0;
  if (!(HEAP32[i3 >> 2] | 0)) {
   i10 = i1 + 44 | 0;
   i13 = HEAP32[i10 >> 2] | 0;
   HEAP32[i10 >> 2] = i8;
   i14 = i1 + 28 | 0;
   HEAP32[i14 >> 2] = i8;
   i15 = i1 + 20 | 0;
   HEAP32[i15 >> 2] = i8;
   HEAP32[i3 >> 2] = 80;
   i16 = i1 + 16 | 0;
   HEAP32[i16 >> 2] = i8 + 80;
   i8 = _printf_core(i1, i2, i5, i7, i6) | 0;
   if (!i13) i17 = i8; else {
    FUNCTION_TABLE_iiii[HEAP32[i1 + 36 >> 2] & 7](i1, 0, 0) | 0;
    i18 = (HEAP32[i15 >> 2] | 0) == 0 ? -1 : i8;
    HEAP32[i10 >> 2] = i13;
    HEAP32[i3 >> 2] = 0;
    HEAP32[i16 >> 2] = 0;
    HEAP32[i14 >> 2] = 0;
    HEAP32[i15 >> 2] = 0;
    i17 = i18;
   }
  } else i17 = _printf_core(i1, i2, i5, i7, i6) | 0;
  i6 = HEAP32[i1 >> 2] | 0;
  HEAP32[i1 >> 2] = i6 | i9;
  if (i12 | 0) ___unlockfile(i1);
  i11 = (i6 & 32 | 0) == 0 ? i17 : -1;
 }
 STACKTOP = i4;
 return i11 | 0;
}

function _oggskel_vect_destroy(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2 + 4 | 0;
 i4 = i2;
 HEAP32[i3 >> 2] = i1;
 HEAP32[i4 >> 2] = 0;
 if (!(HEAP32[i3 >> 2] | 0)) {
  STACKTOP = i2;
  return;
 }
 if (HEAP32[(HEAP32[i3 >> 2] | 0) + 4 >> 2] | 0) {
  HEAP32[i4 >> 2] = 0;
  while (1) {
   if ((HEAP32[i4 >> 2] | 0) >>> 0 >= (HEAP32[HEAP32[i3 >> 2] >> 2] | 0) >>> 0) break;
   if (HEAP32[(HEAP32[(HEAP32[i3 >> 2] | 0) + 4 >> 2] | 0) + ((HEAP32[i4 >> 2] | 0) * 12 | 0) + 4 >> 2] | 0) {
    if (HEAP32[(HEAP32[(HEAP32[(HEAP32[i3 >> 2] | 0) + 4 >> 2] | 0) + ((HEAP32[i4 >> 2] | 0) * 12 | 0) + 4 >> 2] | 0) + 48 >> 2] | 0) _free(HEAP32[(HEAP32[(HEAP32[(HEAP32[i3 >> 2] | 0) + 4 >> 2] | 0) + ((HEAP32[i4 >> 2] | 0) * 12 | 0) + 4 >> 2] | 0) + 48 >> 2] | 0);
    _free(HEAP32[(HEAP32[(HEAP32[i3 >> 2] | 0) + 4 >> 2] | 0) + ((HEAP32[i4 >> 2] | 0) * 12 | 0) + 4 >> 2] | 0);
   }
   if (HEAP32[(HEAP32[(HEAP32[i3 >> 2] | 0) + 4 >> 2] | 0) + ((HEAP32[i4 >> 2] | 0) * 12 | 0) + 8 >> 2] | 0) {
    if (HEAP32[(HEAP32[(HEAP32[(HEAP32[i3 >> 2] | 0) + 4 >> 2] | 0) + ((HEAP32[i4 >> 2] | 0) * 12 | 0) + 8 >> 2] | 0) + 40 >> 2] | 0) _free(HEAP32[(HEAP32[(HEAP32[(HEAP32[i3 >> 2] | 0) + 4 >> 2] | 0) + ((HEAP32[i4 >> 2] | 0) * 12 | 0) + 8 >> 2] | 0) + 40 >> 2] | 0);
    _free(HEAP32[(HEAP32[(HEAP32[i3 >> 2] | 0) + 4 >> 2] | 0) + ((HEAP32[i4 >> 2] | 0) * 12 | 0) + 8 >> 2] | 0);
   }
   HEAP32[i4 >> 2] = (HEAP32[i4 >> 2] | 0) + 1;
  }
  _free(HEAP32[(HEAP32[i3 >> 2] | 0) + 4 >> 2] | 0);
 }
 _free(HEAP32[i3 >> 2] | 0);
 STACKTOP = i2;
 return;
}

function _opus_packet_duration(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i3 = i2;
 i4 = i2 + 16 | 0;
 i5 = i2 + 22 | 0;
 i6 = i2 + 21 | 0;
 i7 = i2 + 20 | 0;
 i8 = i2 + 12 | 0;
 i9 = i2 + 8 | 0;
 HEAP32[i4 >> 2] = i1;
 do if ((HEAP32[(HEAP32[i4 >> 2] | 0) + 4 >> 2] | 0) >= 1) {
  HEAP8[i5 >> 0] = HEAP8[HEAP32[HEAP32[i4 >> 2] >> 2] >> 0] | 0;
  HEAP8[i6 >> 0] = (HEAPU8[i5 >> 0] | 0) & 3;
  HEAP32[i8 >> 2] = HEAP32[2024 + ((HEAPU8[i5 >> 0] | 0) >> 3 << 2) >> 2];
  if ((HEAPU8[i6 >> 0] | 0 | 0) == 3 ? (HEAP32[(HEAP32[i4 >> 2] | 0) + 4 >> 2] | 0) < 2 : 0) {
   i1 = i3;
   HEAP32[i1 >> 2] = 0;
   HEAP32[i1 + 4 >> 2] = 0;
   break;
  }
  switch (HEAPU8[i6 >> 0] | 0 | 0) {
  case 0:
   {
    HEAP8[i7 >> 0] = 1;
    break;
   }
  case 2:
  case 1:
   {
    HEAP8[i7 >> 0] = 2;
    break;
   }
  case 3:
   {
    HEAP8[i7 >> 0] = (HEAPU8[(HEAP32[HEAP32[i4 >> 2] >> 2] | 0) + 1 >> 0] | 0) & 63;
    break;
   }
  default:
   {}
  }
  HEAP32[i9 >> 2] = Math_imul(HEAP32[i8 >> 2] | 0, HEAPU8[i7 >> 0] | 0) | 0;
  if ((HEAP32[i9 >> 2] | 0) > 5760) {
   i1 = i3;
   HEAP32[i1 >> 2] = 0;
   HEAP32[i1 + 4 >> 2] = 0;
   break;
  } else {
   i1 = HEAP32[i9 >> 2] | 0;
   i10 = i3;
   HEAP32[i10 >> 2] = i1;
   HEAP32[i10 + 4 >> 2] = ((i1 | 0) < 0) << 31 >> 31;
   break;
  }
 } else {
  i1 = i3;
  HEAP32[i1 >> 2] = 0;
  HEAP32[i1 + 4 >> 2] = 0;
 } while (0);
 i9 = i3;
 tempRet0 = HEAP32[i9 + 4 >> 2] | 0;
 STACKTOP = i2;
 return HEAP32[i9 >> 2] | 0;
}

function _oggz_vector_qsort(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i5 = i4 + 20 | 0;
 i6 = i4 + 16 | 0;
 i7 = i4 + 12 | 0;
 i8 = i4 + 8 | 0;
 i9 = i4 + 4 | 0;
 i10 = i4;
 HEAP32[i5 >> 2] = i1;
 HEAP32[i6 >> 2] = i2;
 HEAP32[i7 >> 2] = i3;
 HEAP32[i10 >> 2] = HEAP32[(HEAP32[i5 >> 2] | 0) + 8 >> 2];
 if ((HEAP32[i6 >> 2] | 0) >= (HEAP32[i7 >> 2] | 0)) {
  STACKTOP = i4;
  return;
 }
 __array_swap(HEAP32[i10 >> 2] | 0, HEAP32[i6 >> 2] | 0, ((HEAP32[i6 >> 2] | 0) + (HEAP32[i7 >> 2] | 0) | 0) / 2 | 0);
 HEAP32[i9 >> 2] = HEAP32[i6 >> 2];
 HEAP32[i8 >> 2] = (HEAP32[i6 >> 2] | 0) + 1;
 while (1) {
  if ((HEAP32[i8 >> 2] | 0) > (HEAP32[i7 >> 2] | 0)) break;
  if ((FUNCTION_TABLE_iiii[HEAP32[(HEAP32[i5 >> 2] | 0) + 12 >> 2] & 7](HEAP32[(HEAP32[i10 >> 2] | 0) + (HEAP32[i8 >> 2] << 2) >> 2] | 0, HEAP32[(HEAP32[i10 >> 2] | 0) + (HEAP32[i6 >> 2] << 2) >> 2] | 0, HEAP32[(HEAP32[i5 >> 2] | 0) + 16 >> 2] | 0) | 0) < 0) {
   i3 = HEAP32[i10 >> 2] | 0;
   i2 = (HEAP32[i9 >> 2] | 0) + 1 | 0;
   HEAP32[i9 >> 2] = i2;
   __array_swap(i3, i2, HEAP32[i8 >> 2] | 0);
  }
  HEAP32[i8 >> 2] = (HEAP32[i8 >> 2] | 0) + 1;
 }
 __array_swap(HEAP32[i10 >> 2] | 0, HEAP32[i6 >> 2] | 0, HEAP32[i9 >> 2] | 0);
 _oggz_vector_qsort(HEAP32[i5 >> 2] | 0, HEAP32[i6 >> 2] | 0, (HEAP32[i9 >> 2] | 0) - 1 | 0);
 _oggz_vector_qsort(HEAP32[i5 >> 2] | 0, (HEAP32[i9 >> 2] | 0) + 1 | 0, HEAP32[i7 >> 2] | 0);
 STACKTOP = i4;
 return;
}

function _auto_flac(i1, i2, i3, i4, i5) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 i5 = i5 | 0;
 var i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0;
 i6 = STACKTOP;
 STACKTOP = STACKTOP + 48 | 0;
 i7 = i6 + 36 | 0;
 i8 = i6 + 32 | 0;
 i9 = i6 + 28 | 0;
 i10 = i6 + 24 | 0;
 i11 = i6 + 20 | 0;
 i12 = i6 + 12 | 0;
 i13 = i6;
 i14 = i6 + 8 | 0;
 HEAP32[i8 >> 2] = i1;
 HEAP32[i9 >> 2] = i2;
 HEAP32[i10 >> 2] = i3;
 HEAP32[i11 >> 2] = i4;
 HEAP32[i6 + 16 >> 2] = i5;
 HEAP32[i12 >> 2] = HEAP32[i10 >> 2];
 i10 = i13;
 HEAP32[i10 >> 2] = 0;
 HEAP32[i10 + 4 >> 2] = 0;
 if ((HEAP32[i11 >> 2] | 0) < 51) {
  HEAP32[i7 >> 2] = 0;
  i15 = HEAP32[i7 >> 2] | 0;
  STACKTOP = i6;
  return i15 | 0;
 } else {
  i11 = (HEAPU8[(HEAP32[i12 >> 2] | 0) + 27 >> 0] | 0) << 12;
  i10 = (HEAPU8[(HEAP32[i12 >> 2] | 0) + 28 >> 0] | 0) << 4;
  i5 = (HEAPU8[(HEAP32[i12 >> 2] | 0) + 29 >> 0] | 0) >> 4 & 15;
  i4 = i13;
  HEAP32[i4 >> 2] = i11 | i10 | i5;
  HEAP32[i4 + 4 >> 2] = ((i11 | 0) < 0) << 31 >> 31 | ((i10 | 0) < 0) << 31 >> 31 | ((i5 | 0) < 0) << 31 >> 31;
  i5 = i13;
  _oggz_set_granulerate(HEAP32[i8 >> 2] | 0, HEAP32[i9 >> 2] | 0, HEAP32[i5 >> 2] | 0, HEAP32[i5 + 4 >> 2] | 0, 1e3, 0) | 0;
  HEAP32[i14 >> 2] = (_int16_be_at((HEAP32[i12 >> 2] | 0) + 7 | 0) | 0) & 65535;
  _oggz_stream_set_numheaders(HEAP32[i8 >> 2] | 0, HEAP32[i9 >> 2] | 0, HEAP32[i14 >> 2] | 0) | 0;
  HEAP32[i7 >> 2] = 1;
  i15 = HEAP32[i7 >> 2] | 0;
  STACKTOP = i6;
  return i15 | 0;
 }
 return 0;
}

function ___stpcpy(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0;
 i3 = i2;
 L1 : do if (!((i3 ^ i1) & 3)) {
  if (!(i3 & 3)) {
   i4 = i2;
   i5 = i1;
  } else {
   i6 = i1;
   i7 = i2;
   while (1) {
    i8 = HEAP8[i7 >> 0] | 0;
    HEAP8[i6 >> 0] = i8;
    if (!(i8 << 24 >> 24)) {
     i9 = i6;
     break L1;
    }
    i8 = i7 + 1 | 0;
    i10 = i6 + 1 | 0;
    if (!(i8 & 3)) {
     i4 = i8;
     i5 = i10;
     break;
    } else {
     i6 = i10;
     i7 = i8;
    }
   }
  }
  i7 = HEAP32[i4 >> 2] | 0;
  if (!((i7 & -2139062144 ^ -2139062144) & i7 + -16843009)) {
   i6 = i7;
   i7 = i5;
   i8 = i4;
   while (1) {
    i10 = i8 + 4 | 0;
    i11 = i7 + 4 | 0;
    HEAP32[i7 >> 2] = i6;
    i6 = HEAP32[i10 >> 2] | 0;
    if ((i6 & -2139062144 ^ -2139062144) & i6 + -16843009 | 0) {
     i12 = i11;
     i13 = i10;
     break;
    } else {
     i7 = i11;
     i8 = i10;
    }
   }
  } else {
   i12 = i5;
   i13 = i4;
  }
  i14 = i13;
  i15 = i12;
  i16 = 8;
 } else {
  i14 = i2;
  i15 = i1;
  i16 = 8;
 } while (0);
 if ((i16 | 0) == 8) {
  i16 = HEAP8[i14 >> 0] | 0;
  HEAP8[i15 >> 0] = i16;
  if (!(i16 << 24 >> 24)) i9 = i15; else {
   i16 = i15;
   i15 = i14;
   while (1) {
    i15 = i15 + 1 | 0;
    i14 = i16 + 1 | 0;
    i1 = HEAP8[i15 >> 0] | 0;
    HEAP8[i14 >> 0] = i1;
    if (!(i1 << 24 >> 24)) {
     i9 = i14;
     break;
    } else i16 = i14;
   }
  }
 }
 return i9 | 0;
}

function _auto_dirac(i1, i2, i3, i4, i5) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 i5 = i5 | 0;
 var i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0;
 i6 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i7 = i6 + 28 | 0;
 i8 = i6 + 24 | 0;
 i9 = i6 + 20 | 0;
 i10 = i6 + 16 | 0;
 i11 = i6 + 12 | 0;
 i12 = i6 + 4 | 0;
 i13 = i6;
 HEAP32[i8 >> 2] = i1;
 HEAP32[i9 >> 2] = i2;
 HEAP32[i10 >> 2] = i3;
 HEAP32[i11 >> 2] = i4;
 HEAP32[i6 + 8 >> 2] = i5;
 HEAP32[i12 >> 2] = 22;
 HEAP32[i13 >> 2] = _malloc(48) | 0;
 if (!(HEAP32[i13 >> 2] | 0)) {
  HEAP32[i7 >> 2] = -1;
  i14 = HEAP32[i7 >> 2] | 0;
  STACKTOP = i6;
  return i14 | 0;
 }
 if ((_dirac_parse_info(HEAP32[i13 >> 2] | 0, HEAP32[i10 >> 2] | 0, HEAP32[i11 >> 2] | 0) | 0) == -1) {
  _free(HEAP32[i13 >> 2] | 0);
  HEAP32[i7 >> 2] = -1;
  i14 = HEAP32[i7 >> 2] | 0;
  STACKTOP = i6;
  return i14 | 0;
 } else {
  i11 = HEAP32[i8 >> 2] | 0;
  i10 = HEAP32[i9 >> 2] | 0;
  i5 = ___muldi3(2, 0, HEAP32[(HEAP32[i13 >> 2] | 0) + 32 >> 2] | 0, 0) | 0;
  i4 = tempRet0;
  i3 = ___muldi3(1e3, 0, HEAP32[(HEAP32[i13 >> 2] | 0) + 36 >> 2] | 0, 0) | 0;
  _oggz_set_granulerate(i11, i10, i5, i4, i3, tempRet0) | 0;
  _oggz_set_granuleshift(HEAP32[i8 >> 2] | 0, HEAP32[i9 >> 2] | 0, HEAP32[i12 >> 2] | 0) | 0;
  _oggz_stream_set_numheaders(HEAP32[i8 >> 2] | 0, HEAP32[i9 >> 2] | 0, 0) | 0;
  _free(HEAP32[i13 >> 2] | 0);
  HEAP32[i7 >> 2] = 1;
  i14 = HEAP32[i7 >> 2] | 0;
  STACKTOP = i6;
  return i14 | 0;
 }
 return 0;
}

function _oggz_set_read_callback(i1, i2, i3, i4) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 var i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0;
 i5 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i6 = i5 + 24 | 0;
 i7 = i5 + 20 | 0;
 i8 = i5 + 16 | 0;
 i9 = i5 + 12 | 0;
 i10 = i5 + 8 | 0;
 i11 = i5 + 4 | 0;
 i12 = i5;
 HEAP32[i7 >> 2] = i1;
 HEAP32[i8 >> 2] = i2;
 HEAP32[i9 >> 2] = i3;
 HEAP32[i10 >> 2] = i4;
 if (!(HEAP32[i7 >> 2] | 0)) {
  HEAP32[i6 >> 2] = -2;
  i13 = HEAP32[i6 >> 2] | 0;
  STACKTOP = i5;
  return i13 | 0;
 }
 HEAP32[i11 >> 2] = (HEAP32[i7 >> 2] | 0) + 112;
 if (HEAP32[HEAP32[i7 >> 2] >> 2] & 1 | 0) {
  HEAP32[i6 >> 2] = -3;
  i13 = HEAP32[i6 >> 2] | 0;
  STACKTOP = i5;
  return i13 | 0;
 }
 do if ((HEAP32[i8 >> 2] | 0) == -1) {
  HEAP32[(HEAP32[i11 >> 2] | 0) + 396 >> 2] = HEAP32[i9 >> 2];
  HEAP32[(HEAP32[i11 >> 2] | 0) + 400 >> 2] = HEAP32[i10 >> 2];
 } else {
  HEAP32[i12 >> 2] = _oggz_get_stream(HEAP32[i7 >> 2] | 0, HEAP32[i8 >> 2] | 0) | 0;
  if (!(HEAP32[i12 >> 2] | 0)) HEAP32[i12 >> 2] = _oggz_add_stream(HEAP32[i7 >> 2] | 0, HEAP32[i8 >> 2] | 0) | 0;
  if (HEAP32[i12 >> 2] | 0) {
   HEAP32[(HEAP32[i12 >> 2] | 0) + 468 >> 2] = HEAP32[i9 >> 2];
   HEAP32[(HEAP32[i12 >> 2] | 0) + 472 >> 2] = HEAP32[i10 >> 2];
   break;
  }
  HEAP32[i6 >> 2] = -18;
  i13 = HEAP32[i6 >> 2] | 0;
  STACKTOP = i5;
  return i13 | 0;
 } while (0);
 HEAP32[i6 >> 2] = 0;
 i13 = HEAP32[i6 >> 2] | 0;
 STACKTOP = i5;
 return i13 | 0;
}

function _oggz_auto_calculate_granulepos(i1, i2, i3, i4, i5) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 i5 = i5 | 0;
 var i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0, i17 = 0, i18 = 0;
 i6 = STACKTOP;
 STACKTOP = STACKTOP + 48 | 0;
 i7 = i6 + 16 | 0;
 i8 = i6 + 32 | 0;
 i9 = i6 + 8 | 0;
 i10 = i6 + 28 | 0;
 i11 = i6 + 24 | 0;
 i12 = i6;
 HEAP32[i8 >> 2] = i1;
 i1 = i9;
 HEAP32[i1 >> 2] = i2;
 HEAP32[i1 + 4 >> 2] = i3;
 HEAP32[i10 >> 2] = i4;
 HEAP32[i11 >> 2] = i5;
 if (HEAP32[1032 + ((HEAP32[i8 >> 2] | 0) * 24 | 0) + 16 >> 2] | 0) {
  i5 = i9;
  i4 = FUNCTION_TABLE_iiiii[HEAP32[1032 + ((HEAP32[i8 >> 2] | 0) * 24 | 0) + 16 >> 2] & 15](HEAP32[i5 >> 2] | 0, HEAP32[i5 + 4 >> 2] | 0, HEAP32[i10 >> 2] | 0, HEAP32[i11 >> 2] | 0) | 0;
  i11 = i12;
  HEAP32[i11 >> 2] = i4;
  HEAP32[i11 + 4 >> 2] = tempRet0;
  i11 = i12;
  i12 = HEAP32[i11 + 4 >> 2] | 0;
  i4 = i7;
  HEAP32[i4 >> 2] = HEAP32[i11 >> 2];
  HEAP32[i4 + 4 >> 2] = i12;
  i13 = i7;
  i14 = i13;
  i15 = HEAP32[i14 >> 2] | 0;
  i16 = i13 + 4 | 0;
  i17 = i16;
  i18 = HEAP32[i17 >> 2] | 0;
  tempRet0 = i18;
  STACKTOP = i6;
  return i15 | 0;
 } else {
  i12 = i9;
  i9 = HEAP32[i12 + 4 >> 2] | 0;
  i4 = i7;
  HEAP32[i4 >> 2] = HEAP32[i12 >> 2];
  HEAP32[i4 + 4 >> 2] = i9;
  i13 = i7;
  i14 = i13;
  i15 = HEAP32[i14 >> 2] | 0;
  i16 = i13 + 4 | 0;
  i17 = i16;
  i18 = HEAP32[i17 >> 2] | 0;
  tempRet0 = i18;
  STACKTOP = i6;
  return i15 | 0;
 }
 return 0;
}

function _bq_seek(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i5 = i4 + 8 | 0;
 i6 = i4;
 i7 = HEAP32[i1 + 4 >> 2] | 0;
 i8 = (i7 | 0) == 0;
 if (i8) i9 = i1 + 16 | 0; else i9 = (HEAP32[i1 >> 2] | 0) + 8 | 0;
 i10 = i9;
 i9 = HEAP32[i10 + 4 >> 2] | 0;
 if ((i9 | 0) > (i3 | 0) | ((i9 | 0) == (i3 | 0) ? (HEAP32[i10 >> 2] | 0) >>> 0 > i2 >>> 0 : 0)) {
  i10 = i6;
  HEAP32[i10 >> 2] = i2;
  HEAP32[i10 + 4 >> 2] = i3;
  _printf(2775, i6) | 0;
  i6 = i1 + 24 | 0;
  HEAP32[i6 >> 2] = i2;
  HEAP32[i6 + 4 >> 2] = i3;
  i11 = -1;
  STACKTOP = i4;
  return i11 | 0;
 }
 if (i8) {
  i8 = i1 + 16 | 0;
  i12 = HEAP32[i8 + 4 >> 2] | 0;
  i13 = HEAP32[i8 >> 2] | 0;
 } else {
  i8 = i7 + -1 | 0;
  i7 = HEAP32[i1 >> 2] | 0;
  i6 = i7 + (i8 * 24 | 0) + 8 | 0;
  i10 = _i64Add(HEAP32[i7 + (i8 * 24 | 0) + 16 >> 2] | 0, 0, HEAP32[i6 >> 2] | 0, HEAP32[i6 + 4 >> 2] | 0) | 0;
  i12 = tempRet0;
  i13 = i10;
 }
 if ((i12 | 0) < (i3 | 0) | (i12 | 0) == (i3 | 0) & i13 >>> 0 < i2 >>> 0) {
  i13 = i5;
  HEAP32[i13 >> 2] = i2;
  HEAP32[i13 + 4 >> 2] = i3;
  _printf(2775, i5) | 0;
  i5 = i1 + 24 | 0;
  HEAP32[i5 >> 2] = i2;
  HEAP32[i5 + 4 >> 2] = i3;
  i11 = -1;
  STACKTOP = i4;
  return i11 | 0;
 } else {
  i5 = i1 + 16 | 0;
  HEAP32[i5 >> 2] = i2;
  HEAP32[i5 + 4 >> 2] = i3;
  i11 = 0;
  STACKTOP = i4;
  return i11 | 0;
 }
 return 0;
}

function _auto_speex(i1, i2, i3, i4, i5) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 i5 = i5 | 0;
 var i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0;
 i6 = STACKTOP;
 STACKTOP = STACKTOP + 48 | 0;
 i7 = i6 + 36 | 0;
 i8 = i6 + 32 | 0;
 i9 = i6 + 28 | 0;
 i10 = i6 + 24 | 0;
 i11 = i6 + 20 | 0;
 i12 = i6 + 12 | 0;
 i13 = i6;
 i14 = i6 + 8 | 0;
 HEAP32[i8 >> 2] = i1;
 HEAP32[i9 >> 2] = i2;
 HEAP32[i10 >> 2] = i3;
 HEAP32[i11 >> 2] = i4;
 HEAP32[i6 + 16 >> 2] = i5;
 HEAP32[i12 >> 2] = HEAP32[i10 >> 2];
 i10 = i13;
 HEAP32[i10 >> 2] = 0;
 HEAP32[i10 + 4 >> 2] = 0;
 if ((HEAP32[i11 >> 2] | 0) < 68) {
  HEAP32[i7 >> 2] = 0;
  i15 = HEAP32[i7 >> 2] | 0;
  STACKTOP = i6;
  return i15 | 0;
 } else {
  i11 = _int32_le_at((HEAP32[i12 >> 2] | 0) + 36 | 0) | 0;
  i10 = i13;
  HEAP32[i10 >> 2] = i11;
  HEAP32[i10 + 4 >> 2] = ((i11 | 0) < 0) << 31 >> 31;
  i11 = i13;
  _oggz_set_granulerate(HEAP32[i8 >> 2] | 0, HEAP32[i9 >> 2] | 0, HEAP32[i11 >> 2] | 0, HEAP32[i11 + 4 >> 2] | 0, 1e3, 0) | 0;
  _oggz_set_preroll(HEAP32[i8 >> 2] | 0, HEAP32[i9 >> 2] | 0, 3) | 0;
  i11 = _int32_le_at((HEAP32[i12 >> 2] | 0) + 68 | 0) | 0;
  i12 = _i64Add(i11 | 0, ((i11 | 0) < 0) << 31 >> 31 | 0, 2, 0) | 0;
  HEAP32[i14 >> 2] = i12;
  _oggz_stream_set_numheaders(HEAP32[i8 >> 2] | 0, HEAP32[i9 >> 2] | 0, HEAP32[i14 >> 2] | 0) | 0;
  HEAP32[i7 >> 2] = 1;
  i15 = HEAP32[i7 >> 2] | 0;
  STACKTOP = i6;
  return i15 | 0;
 }
 return 0;
}

function _auto_anxdata(i1, i2, i3, i4, i5) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 i5 = i5 | 0;
 var i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0;
 i6 = STACKTOP;
 STACKTOP = STACKTOP + 48 | 0;
 i7 = i6 + 40 | 0;
 i8 = i6 + 36 | 0;
 i9 = i6 + 32 | 0;
 i10 = i6 + 28 | 0;
 i11 = i6 + 24 | 0;
 i12 = i6 + 16 | 0;
 i13 = i6 + 8 | 0;
 i14 = i6;
 HEAP32[i8 >> 2] = i1;
 HEAP32[i9 >> 2] = i2;
 HEAP32[i10 >> 2] = i3;
 HEAP32[i11 >> 2] = i4;
 HEAP32[i6 + 20 >> 2] = i5;
 HEAP32[i12 >> 2] = HEAP32[i10 >> 2];
 i10 = i13;
 HEAP32[i10 >> 2] = 0;
 HEAP32[i10 + 4 >> 2] = 0;
 i10 = i14;
 HEAP32[i10 >> 2] = 0;
 HEAP32[i10 + 4 >> 2] = 0;
 if ((HEAP32[i11 >> 2] | 0) < 28) {
  HEAP32[i7 >> 2] = 0;
  i15 = HEAP32[i7 >> 2] | 0;
  STACKTOP = i6;
  return i15 | 0;
 } else {
  i11 = _int64_le_at((HEAP32[i12 >> 2] | 0) + 8 | 0) | 0;
  i10 = i13;
  HEAP32[i10 >> 2] = i11;
  HEAP32[i10 + 4 >> 2] = tempRet0;
  i10 = _int64_le_at((HEAP32[i12 >> 2] | 0) + 16 | 0) | 0;
  i12 = i14;
  HEAP32[i12 >> 2] = i10;
  HEAP32[i12 + 4 >> 2] = tempRet0;
  i12 = HEAP32[i8 >> 2] | 0;
  i8 = HEAP32[i9 >> 2] | 0;
  i9 = i13;
  i13 = HEAP32[i9 >> 2] | 0;
  i10 = HEAP32[i9 + 4 >> 2] | 0;
  i9 = i14;
  i14 = ___muldi3(1e3, 0, HEAP32[i9 >> 2] | 0, HEAP32[i9 + 4 >> 2] | 0) | 0;
  _oggz_set_granulerate(i12, i8, i13, i10, i14, tempRet0) | 0;
  HEAP32[i7 >> 2] = 1;
  i15 = HEAP32[i7 >> 2] | 0;
  STACKTOP = i6;
  return i15 | 0;
 }
 return 0;
}

function _oggskel_get_first_sample_denum(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i5 = i4 + 20 | 0;
 i6 = i4 + 16 | 0;
 i7 = i4 + 12 | 0;
 i8 = i4 + 8 | 0;
 i9 = i4 + 4 | 0;
 i10 = i4;
 HEAP32[i6 >> 2] = i1;
 HEAP32[i7 >> 2] = i2;
 HEAP32[i8 >> 2] = i3;
 HEAP32[i9 >> 2] = -1;
 i3 = _getter_error_check(HEAP32[i6 >> 2] | 0, HEAP32[i8 >> 2] | 0) | 0;
 HEAP32[i9 >> 2] = i3;
 if ((i3 | 0) < 0) {
  HEAP32[i5 >> 2] = HEAP32[i9 >> 2];
  i11 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i11 | 0;
 }
 do if ((HEAPU16[HEAP32[i6 >> 2] >> 1] | 0 | 0) < 4) {
  i9 = (HEAP32[i6 >> 2] | 0) + 72 | 0;
  i3 = HEAP32[i9 + 4 >> 2] | 0;
  i2 = HEAP32[i8 >> 2] | 0;
  HEAP32[i2 >> 2] = HEAP32[i9 >> 2];
  HEAP32[i2 + 4 >> 2] = i3;
 } else {
  HEAP32[i10 >> 2] = 0;
  i3 = _oggskel_vect_get_index(HEAP32[(HEAP32[i6 >> 2] | 0) + 112 >> 2] | 0, HEAP32[i7 >> 2] | 0) | 0;
  HEAP32[i10 >> 2] = i3;
  if (i3 | 0) {
   i3 = (HEAP32[i10 >> 2] | 0) + 16 | 0;
   i2 = HEAP32[i3 + 4 >> 2] | 0;
   i9 = HEAP32[i8 >> 2] | 0;
   HEAP32[i9 >> 2] = HEAP32[i3 >> 2];
   HEAP32[i9 + 4 >> 2] = i2;
   break;
  }
  HEAP32[i5 >> 2] = -8;
  i11 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i11 | 0;
 } while (0);
 HEAP32[i5 >> 2] = 0;
 i11 = HEAP32[i5 >> 2] | 0;
 STACKTOP = i4;
 return i11 | 0;
}

function _auto_celt(i1, i2, i3, i4, i5) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 i5 = i5 | 0;
 var i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0;
 i6 = STACKTOP;
 STACKTOP = STACKTOP + 48 | 0;
 i7 = i6 + 36 | 0;
 i8 = i6 + 32 | 0;
 i9 = i6 + 28 | 0;
 i10 = i6 + 24 | 0;
 i11 = i6 + 20 | 0;
 i12 = i6 + 12 | 0;
 i13 = i6;
 i14 = i6 + 8 | 0;
 HEAP32[i8 >> 2] = i1;
 HEAP32[i9 >> 2] = i2;
 HEAP32[i10 >> 2] = i3;
 HEAP32[i11 >> 2] = i4;
 HEAP32[i6 + 16 >> 2] = i5;
 HEAP32[i12 >> 2] = HEAP32[i10 >> 2];
 i10 = i13;
 HEAP32[i10 >> 2] = 0;
 HEAP32[i10 + 4 >> 2] = 0;
 if ((HEAP32[i11 >> 2] | 0) < 56) {
  HEAP32[i7 >> 2] = 0;
  i15 = HEAP32[i7 >> 2] | 0;
  STACKTOP = i6;
  return i15 | 0;
 } else {
  i11 = _int32_le_at((HEAP32[i12 >> 2] | 0) + 40 | 0) | 0;
  i10 = i13;
  HEAP32[i10 >> 2] = i11;
  HEAP32[i10 + 4 >> 2] = ((i11 | 0) < 0) << 31 >> 31;
  i11 = i13;
  _oggz_set_granulerate(HEAP32[i8 >> 2] | 0, HEAP32[i9 >> 2] | 0, HEAP32[i11 >> 2] | 0, HEAP32[i11 + 4 >> 2] | 0, 1e3, 0) | 0;
  i11 = _int32_le_at((HEAP32[i12 >> 2] | 0) + 52 | 0) | 0;
  i12 = _i64Add(i11 | 0, ((i11 | 0) < 0) << 31 >> 31 | 0, 2, 0) | 0;
  HEAP32[i14 >> 2] = i12;
  _oggz_stream_set_numheaders(HEAP32[i8 >> 2] | 0, HEAP32[i9 >> 2] | 0, HEAP32[i14 >> 2] | 0) | 0;
  HEAP32[i7 >> 2] = 1;
  i15 = HEAP32[i7 >> 2] | 0;
  STACKTOP = i6;
  return i15 | 0;
 }
 return 0;
}

function _oggskel_get_last_sample_denum(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i5 = i4 + 20 | 0;
 i6 = i4 + 16 | 0;
 i7 = i4 + 12 | 0;
 i8 = i4 + 8 | 0;
 i9 = i4 + 4 | 0;
 i10 = i4;
 HEAP32[i6 >> 2] = i1;
 HEAP32[i7 >> 2] = i2;
 HEAP32[i8 >> 2] = i3;
 HEAP32[i9 >> 2] = -1;
 i3 = _getter_error_check(HEAP32[i6 >> 2] | 0, HEAP32[i8 >> 2] | 0) | 0;
 HEAP32[i9 >> 2] = i3;
 if ((i3 | 0) < 0) {
  HEAP32[i5 >> 2] = HEAP32[i9 >> 2];
  i11 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i11 | 0;
 }
 do if ((HEAPU16[HEAP32[i6 >> 2] >> 1] | 0 | 0) < 4) {
  i9 = (HEAP32[i6 >> 2] | 0) + 88 | 0;
  i3 = HEAP32[i9 + 4 >> 2] | 0;
  i2 = HEAP32[i8 >> 2] | 0;
  HEAP32[i2 >> 2] = HEAP32[i9 >> 2];
  HEAP32[i2 + 4 >> 2] = i3;
 } else {
  HEAP32[i10 >> 2] = 0;
  i3 = _oggskel_vect_get_index(HEAP32[(HEAP32[i6 >> 2] | 0) + 112 >> 2] | 0, HEAP32[i7 >> 2] | 0) | 0;
  HEAP32[i10 >> 2] = i3;
  if (i3 | 0) {
   i3 = (HEAP32[i10 >> 2] | 0) + 16 | 0;
   i2 = HEAP32[i3 + 4 >> 2] | 0;
   i9 = HEAP32[i8 >> 2] | 0;
   HEAP32[i9 >> 2] = HEAP32[i3 >> 2];
   HEAP32[i9 + 4 >> 2] = i2;
   break;
  }
  HEAP32[i5 >> 2] = -8;
  i11 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i11 | 0;
 } while (0);
 HEAP32[i5 >> 2] = 0;
 i11 = HEAP32[i5 >> 2] | 0;
 STACKTOP = i4;
 return i11 | 0;
}

function _oggskel_get_first_sample_num(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i5 = i4 + 20 | 0;
 i6 = i4 + 16 | 0;
 i7 = i4 + 12 | 0;
 i8 = i4 + 8 | 0;
 i9 = i4 + 4 | 0;
 i10 = i4;
 HEAP32[i6 >> 2] = i1;
 HEAP32[i7 >> 2] = i2;
 HEAP32[i8 >> 2] = i3;
 HEAP32[i9 >> 2] = -1;
 i3 = _getter_error_check(HEAP32[i6 >> 2] | 0, HEAP32[i8 >> 2] | 0) | 0;
 HEAP32[i9 >> 2] = i3;
 if ((i3 | 0) < 0) {
  HEAP32[i5 >> 2] = HEAP32[i9 >> 2];
  i11 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i11 | 0;
 }
 do if ((HEAPU16[HEAP32[i6 >> 2] >> 1] | 0 | 0) < 4) {
  i9 = (HEAP32[i6 >> 2] | 0) + 64 | 0;
  i3 = HEAP32[i9 + 4 >> 2] | 0;
  i2 = HEAP32[i8 >> 2] | 0;
  HEAP32[i2 >> 2] = HEAP32[i9 >> 2];
  HEAP32[i2 + 4 >> 2] = i3;
 } else {
  HEAP32[i10 >> 2] = 0;
  i3 = _oggskel_vect_get_index(HEAP32[(HEAP32[i6 >> 2] | 0) + 112 >> 2] | 0, HEAP32[i7 >> 2] | 0) | 0;
  HEAP32[i10 >> 2] = i3;
  if (i3 | 0) {
   i3 = (HEAP32[i10 >> 2] | 0) + 24 | 0;
   i2 = HEAP32[i3 + 4 >> 2] | 0;
   i9 = HEAP32[i8 >> 2] | 0;
   HEAP32[i9 >> 2] = HEAP32[i3 >> 2];
   HEAP32[i9 + 4 >> 2] = i2;
   break;
  }
  HEAP32[i5 >> 2] = -8;
  i11 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i11 | 0;
 } while (0);
 HEAP32[i5 >> 2] = 0;
 i11 = HEAP32[i5 >> 2] | 0;
 STACKTOP = i4;
 return i11 | 0;
}

function _oggskel_get_last_sample_num(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i5 = i4 + 20 | 0;
 i6 = i4 + 16 | 0;
 i7 = i4 + 12 | 0;
 i8 = i4 + 8 | 0;
 i9 = i4 + 4 | 0;
 i10 = i4;
 HEAP32[i6 >> 2] = i1;
 HEAP32[i7 >> 2] = i2;
 HEAP32[i8 >> 2] = i3;
 HEAP32[i9 >> 2] = -1;
 i3 = _getter_error_check(HEAP32[i6 >> 2] | 0, HEAP32[i8 >> 2] | 0) | 0;
 HEAP32[i9 >> 2] = i3;
 if ((i3 | 0) < 0) {
  HEAP32[i5 >> 2] = HEAP32[i9 >> 2];
  i11 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i11 | 0;
 }
 do if ((HEAPU16[HEAP32[i6 >> 2] >> 1] | 0 | 0) < 4) {
  i9 = (HEAP32[i6 >> 2] | 0) + 80 | 0;
  i3 = HEAP32[i9 + 4 >> 2] | 0;
  i2 = HEAP32[i8 >> 2] | 0;
  HEAP32[i2 >> 2] = HEAP32[i9 >> 2];
  HEAP32[i2 + 4 >> 2] = i3;
 } else {
  HEAP32[i10 >> 2] = 0;
  i3 = _oggskel_vect_get_index(HEAP32[(HEAP32[i6 >> 2] | 0) + 112 >> 2] | 0, HEAP32[i7 >> 2] | 0) | 0;
  HEAP32[i10 >> 2] = i3;
  if (i3 | 0) {
   i3 = (HEAP32[i10 >> 2] | 0) + 32 | 0;
   i2 = HEAP32[i3 + 4 >> 2] | 0;
   i9 = HEAP32[i8 >> 2] | 0;
   HEAP32[i9 >> 2] = HEAP32[i3 >> 2];
   HEAP32[i9 + 4 >> 2] = i2;
   break;
  }
  HEAP32[i5 >> 2] = -8;
  i11 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i11 | 0;
 } while (0);
 HEAP32[i5 >> 2] = 0;
 i11 = HEAP32[i5 >> 2] | 0;
 STACKTOP = i4;
 return i11 | 0;
}

function _oggz_auto_calculate_gp_backwards(i1, i2, i3, i4, i5, i6) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 i5 = i5 | 0;
 i6 = i6 | 0;
 var i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0, i16 = 0, i17 = 0, i18 = 0, i19 = 0;
 i7 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i8 = i7 + 8 | 0;
 i9 = i7 + 28 | 0;
 i10 = i7;
 i11 = i7 + 24 | 0;
 i12 = i7 + 20 | 0;
 i13 = i7 + 16 | 0;
 HEAP32[i9 >> 2] = i1;
 i1 = i10;
 HEAP32[i1 >> 2] = i2;
 HEAP32[i1 + 4 >> 2] = i3;
 HEAP32[i11 >> 2] = i4;
 HEAP32[i12 >> 2] = i5;
 HEAP32[i13 >> 2] = i6;
 if (HEAP32[1032 + ((HEAP32[i9 >> 2] | 0) * 24 | 0) + 20 >> 2] | 0) {
  i6 = i10;
  i10 = FUNCTION_TABLE_iiiiii[HEAP32[1032 + ((HEAP32[i9 >> 2] | 0) * 24 | 0) + 20 >> 2] & 31](HEAP32[i6 >> 2] | 0, HEAP32[i6 + 4 >> 2] | 0, HEAP32[i11 >> 2] | 0, HEAP32[i12 >> 2] | 0, HEAP32[i13 >> 2] | 0) | 0;
  i13 = i8;
  HEAP32[i13 >> 2] = i10;
  HEAP32[i13 + 4 >> 2] = tempRet0;
  i14 = i8;
  i15 = i14;
  i16 = HEAP32[i15 >> 2] | 0;
  i17 = i14 + 4 | 0;
  i18 = i17;
  i19 = HEAP32[i18 >> 2] | 0;
  tempRet0 = i19;
  STACKTOP = i7;
  return i16 | 0;
 } else {
  i13 = i8;
  HEAP32[i13 >> 2] = 0;
  HEAP32[i13 + 4 >> 2] = 0;
  i14 = i8;
  i15 = i14;
  i16 = HEAP32[i15 >> 2] | 0;
  i17 = i14 + 4 | 0;
  i18 = i17;
  i19 = HEAP32[i18 >> 2] | 0;
  tempRet0 = i19;
  STACKTOP = i7;
  return i16 | 0;
 }
 return 0;
}

function _oggz_auto_read_bos_page(i1, i2, i3, i4) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 var i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0;
 i5 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i6 = i5 + 20 | 0;
 i7 = i5 + 16 | 0;
 i8 = i5 + 12 | 0;
 i9 = i5 + 8 | 0;
 i10 = i5 + 4 | 0;
 i11 = i5;
 HEAP32[i7 >> 2] = i1;
 HEAP32[i8 >> 2] = i2;
 HEAP32[i9 >> 2] = i3;
 HEAP32[i10 >> 2] = i4;
 HEAP32[i11 >> 2] = 0;
 HEAP32[i11 >> 2] = _oggz_stream_get_content(HEAP32[i7 >> 2] | 0, HEAP32[i9 >> 2] | 0) | 0;
 if ((HEAP32[i11 >> 2] | 0) < 0 | (HEAP32[i11 >> 2] | 0) >= 15) {
  HEAP32[i6 >> 2] = 0;
  i12 = HEAP32[i6 >> 2] | 0;
  STACKTOP = i5;
  return i12 | 0;
 }
 if ((HEAP32[i11 >> 2] | 0) == 6 ? (_ogg_page_bos(HEAP32[i8 >> 2] | 0) | 0) == 0 : 0) {
  HEAP32[i6 >> 2] = _auto_fisbone(HEAP32[i7 >> 2] | 0, HEAP32[i9 >> 2] | 0, HEAP32[(HEAP32[i8 >> 2] | 0) + 8 >> 2] | 0, HEAP32[(HEAP32[i8 >> 2] | 0) + 12 >> 2] | 0, HEAP32[i10 >> 2] | 0) | 0;
  i12 = HEAP32[i6 >> 2] | 0;
  STACKTOP = i5;
  return i12 | 0;
 }
 HEAP32[i6 >> 2] = FUNCTION_TABLE_iiiiii[HEAP32[1032 + ((HEAP32[i11 >> 2] | 0) * 24 | 0) + 12 >> 2] & 31](HEAP32[i7 >> 2] | 0, HEAP32[i9 >> 2] | 0, HEAP32[(HEAP32[i8 >> 2] | 0) + 8 >> 2] | 0, HEAP32[(HEAP32[i8 >> 2] | 0) + 12 >> 2] | 0, HEAP32[i10 >> 2] | 0) | 0;
 i12 = HEAP32[i6 >> 2] | 0;
 STACKTOP = i5;
 return i12 | 0;
}

function _oggz_io_read(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i5 = i4 + 20 | 0;
 i6 = i4 + 16 | 0;
 i7 = i4 + 12 | 0;
 i8 = i4 + 8 | 0;
 i9 = i4 + 4 | 0;
 i10 = i4;
 HEAP32[i6 >> 2] = i1;
 HEAP32[i7 >> 2] = i2;
 HEAP32[i8 >> 2] = i3;
 i3 = HEAP32[i6 >> 2] | 0;
 do if (HEAP32[(HEAP32[i6 >> 2] | 0) + 4 >> 2] | 0) {
  i2 = _fileno(HEAP32[i3 + 4 >> 2] | 0) | 0;
  i1 = _read(i2, HEAP32[i7 >> 2] | 0, HEAP32[i8 >> 2] | 0) | 0;
  HEAP32[i10 >> 2] = i1;
  if ((i1 | 0) == 0 ? _ferror(HEAP32[(HEAP32[i6 >> 2] | 0) + 4 >> 2] | 0) | 0 : 0) {
   HEAP32[i5 >> 2] = -10;
   i11 = HEAP32[i5 >> 2] | 0;
   STACKTOP = i4;
   return i11 | 0;
  }
 } else {
  i1 = HEAP32[i3 + 8 >> 2] | 0;
  HEAP32[i9 >> 2] = i1;
  if (!i1) {
   HEAP32[i5 >> 2] = -3;
   i11 = HEAP32[i5 >> 2] | 0;
   STACKTOP = i4;
   return i11 | 0;
  }
  if (HEAP32[HEAP32[i9 >> 2] >> 2] | 0) {
   HEAP32[i10 >> 2] = FUNCTION_TABLE_iiii[HEAP32[HEAP32[i9 >> 2] >> 2] & 7](HEAP32[(HEAP32[i9 >> 2] | 0) + 4 >> 2] | 0, HEAP32[i7 >> 2] | 0, HEAP32[i8 >> 2] | 0) | 0;
   break;
  }
  HEAP32[i5 >> 2] = -1;
  i11 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i11 | 0;
 } while (0);
 HEAP32[i5 >> 2] = HEAP32[i10 >> 2];
 i11 = HEAP32[i5 >> 2] | 0;
 STACKTOP = i4;
 return i11 | 0;
}

function _processDecoding(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, d7 = 0.0, i8 = 0, i9 = 0, i10 = 0, d11 = 0.0, i12 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i4 = i3 + 8 | 0;
 i5 = i3;
 i6 = _oggz_tell_units(HEAP32[1328] | 0) | 0;
 d7 = (+(i6 >>> 0) + 4294967296.0 * +(tempRet0 | 0)) / 1.0e3;
 i6 = _oggz_tell_granulepos(HEAP32[1328] | 0) | 0;
 i8 = tempRet0;
 i9 = _oggz_get_granuleshift(HEAP32[1328] | 0, i2) | 0;
 i10 = i4;
 HEAP32[i10 >> 2] = 0;
 HEAP32[i10 + 4 >> 2] = 0;
 i10 = i5;
 HEAP32[i10 >> 2] = 0;
 HEAP32[i10 + 4 >> 2] = 0;
 _oggz_get_granulerate(HEAP32[1328] | 0, i2, i4, i5) | 0;
 i10 = _bitshift64Ashr(i6 | 0, i8 | 0, i9 | 0) | 0;
 i9 = i5;
 i5 = i4;
 d11 = (+(i10 >>> 0) + 4294967296.0 * +(tempRet0 | 0)) * (+((HEAP32[i9 >> 2] | 0) >>> 0) + 4294967296.0 * +(HEAP32[i9 + 4 >> 2] | 0)) / (+((HEAP32[i5 >> 2] | 0) >>> 0) + 4294967296.0 * +(HEAP32[i5 + 4 >> 2] | 0));
 if ((HEAP32[1331] | 0) == (i2 | 0) ? (i5 = HEAP32[i1 + 4 >> 2] | 0, (i5 | 0) > 0) : 0) {
  _ogvjs_callback_video_packet(HEAP32[i1 >> 2] | 0, i5 | 0, +d7, +d11);
  i12 = 1;
  STACKTOP = i3;
  return i12 | 0;
 }
 if ((HEAP32[1332] | 0) != (i2 | 0)) {
  i12 = 0;
  STACKTOP = i3;
  return i12 | 0;
 }
 _ogvjs_callback_audio_packet(HEAP32[i1 >> 2] | 0, HEAP32[i1 + 4 >> 2] | 0, +d7);
 i12 = 1;
 STACKTOP = i3;
 return i12 | 0;
}

function _oggz_auto_read_bos_packet(i1, i2, i3, i4) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 var i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0;
 i5 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i6 = i5 + 20 | 0;
 i7 = i5 + 16 | 0;
 i8 = i5 + 12 | 0;
 i9 = i5 + 8 | 0;
 i10 = i5 + 4 | 0;
 i11 = i5;
 HEAP32[i7 >> 2] = i1;
 HEAP32[i8 >> 2] = i2;
 HEAP32[i9 >> 2] = i3;
 HEAP32[i10 >> 2] = i4;
 HEAP32[i11 >> 2] = 0;
 HEAP32[i11 >> 2] = _oggz_stream_get_content(HEAP32[i7 >> 2] | 0, HEAP32[i9 >> 2] | 0) | 0;
 if ((HEAP32[i11 >> 2] | 0) < 0 | (HEAP32[i11 >> 2] | 0) >= 15) {
  HEAP32[i6 >> 2] = 0;
  i12 = HEAP32[i6 >> 2] | 0;
  STACKTOP = i5;
  return i12 | 0;
 }
 if ((HEAP32[i11 >> 2] | 0) == 6 ? (HEAP32[(HEAP32[i8 >> 2] | 0) + 8 >> 2] | 0) == 0 : 0) {
  HEAP32[i6 >> 2] = _auto_fisbone(HEAP32[i7 >> 2] | 0, HEAP32[i9 >> 2] | 0, HEAP32[HEAP32[i8 >> 2] >> 2] | 0, HEAP32[(HEAP32[i8 >> 2] | 0) + 4 >> 2] | 0, HEAP32[i10 >> 2] | 0) | 0;
  i12 = HEAP32[i6 >> 2] | 0;
  STACKTOP = i5;
  return i12 | 0;
 }
 HEAP32[i6 >> 2] = FUNCTION_TABLE_iiiiii[HEAP32[1032 + ((HEAP32[i11 >> 2] | 0) * 24 | 0) + 12 >> 2] & 31](HEAP32[i7 >> 2] | 0, HEAP32[i9 >> 2] | 0, HEAP32[HEAP32[i8 >> 2] >> 2] | 0, HEAP32[(HEAP32[i8 >> 2] | 0) + 4 >> 2] | 0, HEAP32[i10 >> 2] | 0) | 0;
 i12 = HEAP32[i6 >> 2] | 0;
 STACKTOP = i5;
 return i12 | 0;
}

function _oggz_set_granulerate(i1, i2, i3, i4, i5, i6) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 i5 = i5 | 0;
 i6 = i6 | 0;
 var i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0;
 i7 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i8 = i7 + 28 | 0;
 i9 = i7 + 24 | 0;
 i10 = i7 + 20 | 0;
 i11 = i7 + 8 | 0;
 i12 = i7;
 i13 = i7 + 16 | 0;
 HEAP32[i9 >> 2] = i1;
 HEAP32[i10 >> 2] = i2;
 i2 = i11;
 HEAP32[i2 >> 2] = i3;
 HEAP32[i2 + 4 >> 2] = i4;
 i4 = i12;
 HEAP32[i4 >> 2] = i5;
 HEAP32[i4 + 4 >> 2] = i6;
 if (!(HEAP32[i9 >> 2] | 0)) {
  HEAP32[i8 >> 2] = -2;
  i14 = HEAP32[i8 >> 2] | 0;
  STACKTOP = i7;
  return i14 | 0;
 }
 HEAP32[i13 >> 2] = _oggz_get_stream(HEAP32[i9 >> 2] | 0, HEAP32[i10 >> 2] | 0) | 0;
 if (!(HEAP32[i13 >> 2] | 0)) {
  HEAP32[i8 >> 2] = -20;
  i14 = HEAP32[i8 >> 2] | 0;
  STACKTOP = i7;
  return i14 | 0;
 } else {
  i6 = i11;
  i11 = HEAP32[i6 + 4 >> 2] | 0;
  i4 = (HEAP32[i13 >> 2] | 0) + 376 | 0;
  HEAP32[i4 >> 2] = HEAP32[i6 >> 2];
  HEAP32[i4 + 4 >> 2] = i11;
  i11 = i12;
  i12 = HEAP32[i11 + 4 >> 2] | 0;
  i4 = (HEAP32[i13 >> 2] | 0) + 384 | 0;
  HEAP32[i4 >> 2] = HEAP32[i11 >> 2];
  HEAP32[i4 + 4 >> 2] = i12;
  HEAP32[i8 >> 2] = _oggz_metric_update(HEAP32[i9 >> 2] | 0, HEAP32[i10 >> 2] | 0) | 0;
  i14 = HEAP32[i8 >> 2] | 0;
  STACKTOP = i7;
  return i14 | 0;
 }
 return 0;
}

function _auto_opus(i1, i2, i3, i4, i5) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 i5 = i5 | 0;
 var i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0;
 i6 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i7 = i6 + 24 | 0;
 i8 = i6 + 20 | 0;
 i9 = i6 + 16 | 0;
 i10 = i6 + 12 | 0;
 i11 = i6 + 8 | 0;
 i12 = i6 + 28 | 0;
 HEAP32[i8 >> 2] = i1;
 HEAP32[i9 >> 2] = i2;
 HEAP32[i10 >> 2] = i3;
 HEAP32[i11 >> 2] = i4;
 HEAP32[i6 + 4 >> 2] = i5;
 HEAP32[i6 >> 2] = HEAP32[i10 >> 2];
 if ((HEAP32[i11 >> 2] | 0) < 19) {
  HEAP32[i7 >> 2] = 0;
  i13 = HEAP32[i7 >> 2] | 0;
  STACKTOP = i6;
  return i13 | 0;
 }
 HEAP8[i12 >> 0] = HEAP8[(HEAP32[i10 >> 2] | 0) + 9 >> 0] | 0;
 if ((HEAPU8[i12 >> 0] | 0 | 0) < 1) {
  HEAP32[i7 >> 2] = 0;
  i13 = HEAP32[i7 >> 2] | 0;
  STACKTOP = i6;
  return i13 | 0;
 } else {
  _oggz_set_granulerate(HEAP32[i8 >> 2] | 0, HEAP32[i9 >> 2] | 0, 48e3, 0, 1e3, 0) | 0;
  _oggz_set_granuleshift(HEAP32[i8 >> 2] | 0, HEAP32[i9 >> 2] | 0, 0) | 0;
  i12 = HEAP32[i8 >> 2] | 0;
  i11 = HEAP32[i9 >> 2] | 0;
  _oggz_set_first_granule(i12, i11, (_int16_le_at((HEAP32[i10 >> 2] | 0) + 10 | 0) | 0) & 65535, 0) | 0;
  _oggz_stream_set_numheaders(HEAP32[i8 >> 2] | 0, HEAP32[i9 >> 2] | 0, 2) | 0;
  HEAP32[i7 >> 2] = 1;
  i13 = HEAP32[i7 >> 2] | 0;
  STACKTOP = i6;
  return i13 | 0;
 }
 return 0;
}

function _oggz_auto_identify(i1, i2, i3, i4) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 var i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0;
 i5 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i6 = i5 + 24 | 0;
 i7 = i5 + 20 | 0;
 i8 = i5 + 16 | 0;
 i9 = i5 + 12 | 0;
 i10 = i5 + 8 | 0;
 i11 = i5 + 4 | 0;
 i12 = i5;
 HEAP32[i7 >> 2] = i1;
 HEAP32[i8 >> 2] = i2;
 HEAP32[i9 >> 2] = i3;
 HEAP32[i10 >> 2] = i4;
 HEAP32[i11 >> 2] = 0;
 while (1) {
  if ((HEAP32[i11 >> 2] | 0) >= 15) {
   i13 = 7;
   break;
  }
  HEAP32[i12 >> 2] = 1032 + ((HEAP32[i11 >> 2] | 0) * 24 | 0);
  if ((HEAP32[i10 >> 2] | 0) >= (HEAP32[(HEAP32[i12 >> 2] | 0) + 4 >> 2] | 0) ? (_memcmp(HEAP32[i9 >> 2] | 0, HEAP32[HEAP32[i12 >> 2] >> 2] | 0, HEAP32[(HEAP32[i12 >> 2] | 0) + 4 >> 2] | 0) | 0) == 0 : 0) {
   i13 = 5;
   break;
  }
  HEAP32[i11 >> 2] = (HEAP32[i11 >> 2] | 0) + 1;
 }
 if ((i13 | 0) == 5) {
  _oggz_stream_set_content(HEAP32[i7 >> 2] | 0, HEAP32[i8 >> 2] | 0, HEAP32[i11 >> 2] | 0) | 0;
  HEAP32[i6 >> 2] = 1;
  i14 = HEAP32[i6 >> 2] | 0;
  STACKTOP = i5;
  return i14 | 0;
 } else if ((i13 | 0) == 7) {
  _oggz_stream_set_content(HEAP32[i7 >> 2] | 0, HEAP32[i8 >> 2] | 0, 15) | 0;
  HEAP32[i6 >> 2] = 0;
  i14 = HEAP32[i6 >> 2] | 0;
  STACKTOP = i5;
  return i14 | 0;
 }
 return 0;
}

function _oggz_comment_new(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i4 = i3 + 12 | 0;
 i5 = i3 + 8 | 0;
 i6 = i3 + 4 | 0;
 i7 = i3;
 HEAP32[i5 >> 2] = i1;
 HEAP32[i6 >> 2] = i2;
 if (!(_oggz_comment_validate_byname(HEAP32[i5 >> 2] | 0) | 0)) {
  HEAP32[i4 >> 2] = 0;
  i8 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i8 | 0;
 }
 HEAP32[i7 >> 2] = _malloc(8) | 0;
 if (!(HEAP32[i7 >> 2] | 0)) {
  HEAP32[i4 >> 2] = 0;
  i8 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i8 | 0;
 }
 i2 = _oggz_strdup(HEAP32[i5 >> 2] | 0) | 0;
 HEAP32[HEAP32[i7 >> 2] >> 2] = i2;
 if (!(HEAP32[HEAP32[i7 >> 2] >> 2] | 0)) {
  _free(HEAP32[i7 >> 2] | 0);
  HEAP32[i4 >> 2] = 0;
  i8 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i8 | 0;
 }
 if (HEAP32[i6 >> 2] | 0) {
  i2 = _oggz_strdup(HEAP32[i6 >> 2] | 0) | 0;
  HEAP32[(HEAP32[i7 >> 2] | 0) + 4 >> 2] = i2;
  if (!(HEAP32[(HEAP32[i7 >> 2] | 0) + 4 >> 2] | 0)) {
   _free(HEAP32[HEAP32[i7 >> 2] >> 2] | 0);
   _free(HEAP32[i7 >> 2] | 0);
   HEAP32[i4 >> 2] = 0;
   i8 = HEAP32[i4 >> 2] | 0;
   STACKTOP = i3;
   return i8 | 0;
  }
 } else HEAP32[(HEAP32[i7 >> 2] | 0) + 4 >> 2] = 0;
 HEAP32[i4 >> 2] = HEAP32[i7 >> 2];
 i8 = HEAP32[i4 >> 2] | 0;
 STACKTOP = i3;
 return i8 | 0;
}

function _ogg_sync_buffer(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0;
 i3 = i1 + 4 | 0;
 i4 = HEAP32[i3 >> 2] | 0;
 if ((i4 | 0) <= -1) {
  i5 = 0;
  return i5 | 0;
 }
 i6 = i1 + 12 | 0;
 i7 = HEAP32[i6 >> 2] | 0;
 i8 = i1 + 8 | 0;
 if (!i7) i9 = i4; else {
  i10 = (HEAP32[i8 >> 2] | 0) - i7 | 0;
  HEAP32[i8 >> 2] = i10;
  if ((i10 | 0) > 0) {
   i11 = HEAP32[i1 >> 2] | 0;
   _memmove(i11 | 0, i11 + i7 | 0, i10 | 0) | 0;
   i12 = HEAP32[i3 >> 2] | 0;
  } else i12 = i4;
  HEAP32[i6 >> 2] = 0;
  i9 = i12;
 }
 i12 = HEAP32[i8 >> 2] | 0;
 do if ((i9 - i12 | 0) < (i2 | 0)) {
  i6 = i2 + 4096 + i12 | 0;
  i4 = HEAP32[i1 >> 2] | 0;
  if (!i4) i13 = _malloc(i6) | 0; else i13 = _realloc(i4, i6) | 0;
  if (i13 | 0) {
   HEAP32[i1 >> 2] = i13;
   HEAP32[i3 >> 2] = i6;
   i14 = i13;
   i15 = HEAP32[i8 >> 2] | 0;
   break;
  }
  i6 = HEAP32[i1 >> 2] | 0;
  if (i6 | 0) _free(i6);
  HEAP32[i1 >> 2] = 0;
  HEAP32[i1 + 4 >> 2] = 0;
  HEAP32[i1 + 8 >> 2] = 0;
  HEAP32[i1 + 12 >> 2] = 0;
  HEAP32[i1 + 16 >> 2] = 0;
  HEAP32[i1 + 20 >> 2] = 0;
  HEAP32[i1 + 24 >> 2] = 0;
  i5 = 0;
  return i5 | 0;
 } else {
  i14 = HEAP32[i1 >> 2] | 0;
  i15 = i12;
 } while (0);
 i5 = i14 + i15 | 0;
 return i5 | 0;
}

function _oggz_read_get_next_page(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i4 = i3 + 20 | 0;
 i5 = i3 + 16 | 0;
 i6 = i3 + 12 | 0;
 i7 = i3 + 8 | 0;
 i8 = i3 + 4 | 0;
 i9 = i3;
 HEAP32[i5 >> 2] = i1;
 HEAP32[i6 >> 2] = i2;
 HEAP32[i7 >> 2] = (HEAP32[i5 >> 2] | 0) + 112;
 HEAP32[i9 >> 2] = 0;
 i2 = (HEAP32[i5 >> 2] | 0) + 64 | 0;
 HEAP32[i2 >> 2] = (HEAP32[i2 >> 2] | 0) + (HEAP32[(HEAP32[i7 >> 2] | 0) + 432 >> 2] | 0);
 while (1) {
  HEAP32[i8 >> 2] = _ogg_sync_pageseek(HEAP32[i7 >> 2] | 0, HEAP32[i6 >> 2] | 0) | 0;
  if (!(HEAP32[i8 >> 2] | 0)) {
   i10 = 3;
   break;
  }
  i2 = HEAP32[i8 >> 2] | 0;
  if ((HEAP32[i8 >> 2] | 0) < 0) {
   i1 = (HEAP32[i5 >> 2] | 0) + 64 | 0;
   HEAP32[i1 >> 2] = (HEAP32[i1 >> 2] | 0) + (0 - i2);
  } else {
   HEAP32[(HEAP32[i7 >> 2] | 0) + 432 >> 2] = i2;
   HEAP32[i9 >> 2] = 1;
  }
  if (!((HEAP32[i9 >> 2] | 0) != 0 ^ 1)) {
   i10 = 8;
   break;
  }
 }
 if ((i10 | 0) == 3) {
  HEAP32[i4 >> 2] = -2;
  i11 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i11 | 0;
 } else if ((i10 | 0) == 8) {
  HEAP32[i4 >> 2] = HEAP32[(HEAP32[i5 >> 2] | 0) + 64 >> 2];
  i11 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i11 | 0;
 }
 return 0;
}

function ___fwritex(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0;
 i4 = i3 + 16 | 0;
 i5 = HEAP32[i4 >> 2] | 0;
 if (!i5) if (!(___towrite(i3) | 0)) {
  i6 = HEAP32[i4 >> 2] | 0;
  i7 = 5;
 } else i8 = 0; else {
  i6 = i5;
  i7 = 5;
 }
 L5 : do if ((i7 | 0) == 5) {
  i5 = i3 + 20 | 0;
  i4 = HEAP32[i5 >> 2] | 0;
  i9 = i4;
  if ((i6 - i4 | 0) >>> 0 < i2 >>> 0) {
   i8 = FUNCTION_TABLE_iiii[HEAP32[i3 + 36 >> 2] & 7](i3, i1, i2) | 0;
   break;
  }
  L10 : do if ((HEAP8[i3 + 75 >> 0] | 0) > -1) {
   i4 = i2;
   while (1) {
    if (!i4) {
     i10 = i2;
     i11 = i1;
     i12 = i9;
     i13 = 0;
     break L10;
    }
    i14 = i4 + -1 | 0;
    if ((HEAP8[i1 + i14 >> 0] | 0) == 10) {
     i15 = i4;
     break;
    } else i4 = i14;
   }
   if ((FUNCTION_TABLE_iiii[HEAP32[i3 + 36 >> 2] & 7](i3, i1, i15) | 0) >>> 0 < i15 >>> 0) {
    i8 = i15;
    break L5;
   }
   i10 = i2 - i15 | 0;
   i11 = i1 + i15 | 0;
   i12 = HEAP32[i5 >> 2] | 0;
   i13 = i15;
  } else {
   i10 = i2;
   i11 = i1;
   i12 = i9;
   i13 = 0;
  } while (0);
  _memcpy(i12 | 0, i11 | 0, i10 | 0) | 0;
  HEAP32[i5 >> 2] = (HEAP32[i5 >> 2] | 0) + i10;
  i8 = i13 + i10 | 0;
 } while (0);
 return i8 | 0;
}

function __os_lacing_expand(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0;
 i3 = i1 + 24 | 0;
 i4 = HEAP32[i3 >> 2] | 0;
 if ((i4 - i2 | 0) > (HEAP32[i1 + 28 >> 2] | 0)) {
  i5 = 0;
  return i5 | 0;
 }
 if ((i4 | 0) > (2147483647 - i2 | 0)) {
  i6 = HEAP32[i1 >> 2] | 0;
  if (i6 | 0) _free(i6);
  i6 = HEAP32[i1 + 16 >> 2] | 0;
  if (i6 | 0) _free(i6);
  i6 = HEAP32[i1 + 20 >> 2] | 0;
  if (i6 | 0) _free(i6);
  _memset(i1 | 0, 0, 360) | 0;
  i5 = -1;
  return i5 | 0;
 }
 i6 = i4 + i2 | 0;
 i2 = (i6 | 0) < 2147483615 ? i6 + 32 | 0 : i6;
 i6 = i1 + 16 | 0;
 i4 = _realloc(HEAP32[i6 >> 2] | 0, i2 << 2) | 0;
 if (!i4) {
  i7 = HEAP32[i1 >> 2] | 0;
  if (i7 | 0) _free(i7);
  i7 = HEAP32[i6 >> 2] | 0;
  if (i7 | 0) _free(i7);
  i7 = HEAP32[i1 + 20 >> 2] | 0;
  if (i7 | 0) _free(i7);
  _memset(i1 | 0, 0, 360) | 0;
  i5 = -1;
  return i5 | 0;
 }
 HEAP32[i6 >> 2] = i4;
 i4 = i1 + 20 | 0;
 i7 = _realloc(HEAP32[i4 >> 2] | 0, i2 << 3) | 0;
 if (i7 | 0) {
  HEAP32[i4 >> 2] = i7;
  HEAP32[i3 >> 2] = i2;
  i5 = 0;
  return i5 | 0;
 }
 i2 = HEAP32[i1 >> 2] | 0;
 if (i2 | 0) _free(i2);
 i2 = HEAP32[i6 >> 2] | 0;
 if (i2 | 0) _free(i2);
 i2 = HEAP32[i4 >> 2] | 0;
 if (i2 | 0) _free(i2);
 _memset(i1 | 0, 0, 360) | 0;
 i5 = -1;
 return i5 | 0;
}

function _auto_vorbis(i1, i2, i3, i4, i5) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 i5 = i5 | 0;
 var i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0;
 i6 = STACKTOP;
 STACKTOP = STACKTOP + 48 | 0;
 i7 = i6 + 32 | 0;
 i8 = i6 + 28 | 0;
 i9 = i6 + 24 | 0;
 i10 = i6 + 20 | 0;
 i11 = i6 + 16 | 0;
 i12 = i6 + 8 | 0;
 i13 = i6;
 HEAP32[i8 >> 2] = i1;
 HEAP32[i9 >> 2] = i2;
 HEAP32[i10 >> 2] = i3;
 HEAP32[i11 >> 2] = i4;
 HEAP32[i6 + 12 >> 2] = i5;
 HEAP32[i12 >> 2] = HEAP32[i10 >> 2];
 i10 = i13;
 HEAP32[i10 >> 2] = 0;
 HEAP32[i10 + 4 >> 2] = 0;
 if ((HEAP32[i11 >> 2] | 0) < 30) {
  HEAP32[i7 >> 2] = 0;
  i14 = HEAP32[i7 >> 2] | 0;
  STACKTOP = i6;
  return i14 | 0;
 } else {
  i11 = _int32_le_at((HEAP32[i12 >> 2] | 0) + 12 | 0) | 0;
  i12 = i13;
  HEAP32[i12 >> 2] = i11;
  HEAP32[i12 + 4 >> 2] = ((i11 | 0) < 0) << 31 >> 31;
  i11 = i13;
  _oggz_set_granulerate(HEAP32[i8 >> 2] | 0, HEAP32[i9 >> 2] | 0, HEAP32[i11 >> 2] | 0, HEAP32[i11 + 4 >> 2] | 0, 1e3, 0) | 0;
  _oggz_set_preroll(HEAP32[i8 >> 2] | 0, HEAP32[i9 >> 2] | 0, 2) | 0;
  _oggz_stream_set_numheaders(HEAP32[i8 >> 2] | 0, HEAP32[i9 >> 2] | 0, 3) | 0;
  HEAP32[i7 >> 2] = 1;
  i14 = HEAP32[i7 >> 2] | 0;
  STACKTOP = i6;
  return i14 | 0;
 }
 return 0;
}

function _oggz_seek(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i5 = i4 + 24 | 0;
 i6 = i4 + 20 | 0;
 i7 = i4 + 16 | 0;
 i8 = i4 + 12 | 0;
 i9 = i4 + 8 | 0;
 i10 = i4;
 HEAP32[i6 >> 2] = i1;
 HEAP32[i7 >> 2] = i2;
 HEAP32[i8 >> 2] = i3;
 i3 = i10;
 HEAP32[i3 >> 2] = -1;
 HEAP32[i3 + 4 >> 2] = -1;
 if (!(HEAP32[i6 >> 2] | 0)) {
  HEAP32[i5 >> 2] = -1;
  i11 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i11 | 0;
 }
 if (HEAP32[HEAP32[i6 >> 2] >> 2] & 1 | 0) {
  HEAP32[i5 >> 2] = -1;
  i11 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i11 | 0;
 }
 if ((HEAP32[i7 >> 2] | 0) == 0 & (HEAP32[i8 >> 2] | 0) == 0) {
  i3 = i10;
  HEAP32[i3 >> 2] = 0;
  HEAP32[i3 + 4 >> 2] = 0;
 }
 HEAP32[i9 >> 2] = (HEAP32[i6 >> 2] | 0) + 112;
 if (!((HEAP32[i7 >> 2] | 0) == 0 & (HEAP32[i8 >> 2] | 0) == 1)) {
  i3 = (HEAP32[i9 >> 2] | 0) + 416 | 0;
  HEAP32[i3 >> 2] = -1;
  HEAP32[i3 + 4 >> 2] = -1;
 }
 i3 = i10;
 HEAP32[i5 >> 2] = _oggz_reset(HEAP32[i6 >> 2] | 0, HEAP32[i7 >> 2] | 0, HEAP32[i3 >> 2] | 0, HEAP32[i3 + 4 >> 2] | 0, HEAP32[i8 >> 2] | 0) | 0;
 i11 = HEAP32[i5 >> 2] | 0;
 STACKTOP = i4;
 return i11 | 0;
}

function _oggz_get_granulerate(i1, i2, i3, i4) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 var i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0;
 i5 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i6 = i5 + 20 | 0;
 i7 = i5 + 16 | 0;
 i8 = i5 + 12 | 0;
 i9 = i5 + 8 | 0;
 i10 = i5 + 4 | 0;
 i11 = i5;
 HEAP32[i7 >> 2] = i1;
 HEAP32[i8 >> 2] = i2;
 HEAP32[i9 >> 2] = i3;
 HEAP32[i10 >> 2] = i4;
 if (!(HEAP32[i7 >> 2] | 0)) {
  HEAP32[i6 >> 2] = -2;
  i12 = HEAP32[i6 >> 2] | 0;
  STACKTOP = i5;
  return i12 | 0;
 }
 HEAP32[i11 >> 2] = _oggz_get_stream(HEAP32[i7 >> 2] | 0, HEAP32[i8 >> 2] | 0) | 0;
 if (!(HEAP32[i11 >> 2] | 0)) {
  HEAP32[i6 >> 2] = -20;
  i12 = HEAP32[i6 >> 2] | 0;
  STACKTOP = i5;
  return i12 | 0;
 } else {
  i8 = (HEAP32[i11 >> 2] | 0) + 376 | 0;
  i7 = HEAP32[i8 + 4 >> 2] | 0;
  i4 = HEAP32[i9 >> 2] | 0;
  HEAP32[i4 >> 2] = HEAP32[i8 >> 2];
  HEAP32[i4 + 4 >> 2] = i7;
  i7 = (HEAP32[i11 >> 2] | 0) + 384 | 0;
  i11 = ___udivdi3(HEAP32[i7 >> 2] | 0, HEAP32[i7 + 4 >> 2] | 0, 1e3, 0) | 0;
  i7 = HEAP32[i10 >> 2] | 0;
  HEAP32[i7 >> 2] = i11;
  HEAP32[i7 + 4 >> 2] = tempRet0;
  HEAP32[i6 >> 2] = 0;
  i12 = HEAP32[i6 >> 2] | 0;
  STACKTOP = i5;
  return i12 | 0;
 }
 return 0;
}

function _oggz_close(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0, i5 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2 + 4 | 0;
 i4 = i2;
 HEAP32[i4 >> 2] = i1;
 if (!(HEAP32[i4 >> 2] | 0)) {
  HEAP32[i3 >> 2] = -2;
  i5 = HEAP32[i3 >> 2] | 0;
  STACKTOP = i2;
  return i5 | 0;
 }
 _oggz_read_close(HEAP32[i4 >> 2] | 0) | 0;
 _oggz_vector_foreach(HEAP32[(HEAP32[i4 >> 2] | 0) + 80 >> 2] | 0, 2) | 0;
 _oggz_vector_delete(HEAP32[(HEAP32[i4 >> 2] | 0) + 80 >> 2] | 0);
 _oggz_dlist_deliter(HEAP32[(HEAP32[i4 >> 2] | 0) + 560 >> 2] | 0, 3) | 0;
 _oggz_dlist_delete(HEAP32[(HEAP32[i4 >> 2] | 0) + 560 >> 2] | 0);
 if (HEAP32[(HEAP32[i4 >> 2] | 0) + 96 >> 2] | 0) _free(HEAP32[(HEAP32[i4 >> 2] | 0) + 92 >> 2] | 0);
 if (HEAP32[(HEAP32[i4 >> 2] | 0) + 4 >> 2] | 0 ? (_fclose(HEAP32[(HEAP32[i4 >> 2] | 0) + 4 >> 2] | 0) | 0) == -1 : 0) {
  HEAP32[i3 >> 2] = -10;
  i5 = HEAP32[i3 >> 2] | 0;
  STACKTOP = i2;
  return i5 | 0;
 }
 if (HEAP32[(HEAP32[i4 >> 2] | 0) + 8 >> 2] | 0) {
  _oggz_io_flush(HEAP32[i4 >> 2] | 0) | 0;
  _free(HEAP32[(HEAP32[i4 >> 2] | 0) + 8 >> 2] | 0);
 }
 _free(HEAP32[i4 >> 2] | 0);
 HEAP32[i3 >> 2] = 0;
 i5 = HEAP32[i3 >> 2] | 0;
 STACKTOP = i2;
 return i5 | 0;
}

function _oggz_reset_seek(i1, i2, i3, i4, i5) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 i5 = i5 | 0;
 var i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0;
 i6 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i7 = i6 + 28 | 0;
 i8 = i6 + 24 | 0;
 i9 = i6 + 20 | 0;
 i10 = i6;
 i11 = i6 + 16 | 0;
 i12 = i6 + 12 | 0;
 i13 = i6 + 8 | 0;
 HEAP32[i8 >> 2] = i1;
 HEAP32[i9 >> 2] = i2;
 i2 = i10;
 HEAP32[i2 >> 2] = i3;
 HEAP32[i2 + 4 >> 2] = i4;
 HEAP32[i11 >> 2] = i5;
 HEAP32[i12 >> 2] = (HEAP32[i8 >> 2] | 0) + 112;
 HEAP32[i13 >> 2] = _oggz_seek_raw(HEAP32[i8 >> 2] | 0, HEAP32[i9 >> 2] | 0, HEAP32[i11 >> 2] | 0) | 0;
 if ((HEAP32[i13 >> 2] | 0) == -1) {
  HEAP32[i7 >> 2] = -1;
  i14 = HEAP32[i7 >> 2] | 0;
  STACKTOP = i6;
  return i14 | 0;
 }
 HEAP32[(HEAP32[i8 >> 2] | 0) + 64 >> 2] = HEAP32[i13 >> 2];
 i8 = i10;
 if ((HEAP32[i8 >> 2] | 0) != -1 ? 1 : (HEAP32[i8 + 4 >> 2] | 0) != -1) {
  i8 = i10;
  i10 = HEAP32[i8 + 4 >> 2] | 0;
  i11 = (HEAP32[i12 >> 2] | 0) + 416 | 0;
  HEAP32[i11 >> 2] = HEAP32[i8 >> 2];
  HEAP32[i11 + 4 >> 2] = i10;
 }
 HEAP32[i7 >> 2] = HEAP32[i13 >> 2];
 i14 = HEAP32[i7 >> 2] | 0;
 STACKTOP = i6;
 return i14 | 0;
}

function _get_track_nfo(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i4 = i3 + 12 | 0;
 i5 = i3 + 8 | 0;
 i6 = i3 + 4 | 0;
 i7 = i3;
 HEAP32[i5 >> 2] = i1;
 HEAP32[i6 >> 2] = i2;
 HEAP32[i7 >> 2] = 0;
 i2 = _find_track_info(HEAP32[i5 >> 2] | 0, HEAP32[i6 >> 2] | 0) | 0;
 HEAP32[i7 >> 2] = i2;
 do if (!i2) {
  HEAP32[i7 >> 2] = _realloc(HEAP32[(HEAP32[i5 >> 2] | 0) + 4 >> 2] | 0, ((HEAP32[HEAP32[i5 >> 2] >> 2] | 0) + 1 | 0) * 12 | 0) | 0;
  if (HEAP32[i7 >> 2] | 0) {
   HEAP32[(HEAP32[i5 >> 2] | 0) + 4 >> 2] = HEAP32[i7 >> 2];
   i1 = HEAP32[i5 >> 2] | 0;
   HEAP32[i1 >> 2] = (HEAP32[i1 >> 2] | 0) + 1;
   HEAP32[i7 >> 2] = (HEAP32[(HEAP32[i5 >> 2] | 0) + 4 >> 2] | 0) + (((HEAP32[HEAP32[i5 >> 2] >> 2] | 0) - 1 | 0) * 12 | 0);
   HEAP32[HEAP32[i7 >> 2] >> 2] = HEAP32[i6 >> 2];
   HEAP32[(HEAP32[i7 >> 2] | 0) + 4 >> 2] = 0;
   HEAP32[(HEAP32[i7 >> 2] | 0) + 8 >> 2] = 0;
   break;
  }
  HEAP32[i4 >> 2] = 0;
  i8 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i8 | 0;
 } while (0);
 HEAP32[i4 >> 2] = HEAP32[i7 >> 2];
 i8 = HEAP32[i4 >> 2] | 0;
 STACKTOP = i3;
 return i8 | 0;
}

function _oggz_dlist_new() {
 var i1 = 0, i2 = 0, i3 = 0, i4 = 0, i5 = 0, i6 = 0;
 i1 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i2 = i1 + 12 | 0;
 i3 = i1 + 8 | 0;
 i4 = i1 + 4 | 0;
 i5 = i1;
 HEAP32[i3 >> 2] = _malloc(8) | 0;
 if (!(HEAP32[i3 >> 2] | 0)) {
  HEAP32[i2 >> 2] = 0;
  i6 = HEAP32[i2 >> 2] | 0;
  STACKTOP = i1;
  return i6 | 0;
 }
 HEAP32[i4 >> 2] = _malloc(12) | 0;
 if (!(HEAP32[i4 >> 2] | 0)) {
  _free(HEAP32[i3 >> 2] | 0);
  HEAP32[i2 >> 2] = 0;
  i6 = HEAP32[i2 >> 2] | 0;
  STACKTOP = i1;
  return i6 | 0;
 }
 HEAP32[i5 >> 2] = _malloc(12) | 0;
 if (!(HEAP32[i5 >> 2] | 0)) {
  _free(HEAP32[i4 >> 2] | 0);
  _free(HEAP32[i3 >> 2] | 0);
  HEAP32[i2 >> 2] = 0;
  i6 = HEAP32[i2 >> 2] | 0;
  STACKTOP = i1;
  return i6 | 0;
 } else {
  HEAP32[HEAP32[i4 >> 2] >> 2] = HEAP32[i5 >> 2];
  HEAP32[(HEAP32[i4 >> 2] | 0) + 4 >> 2] = 0;
  HEAP32[(HEAP32[i5 >> 2] | 0) + 4 >> 2] = HEAP32[i4 >> 2];
  HEAP32[HEAP32[i5 >> 2] >> 2] = 0;
  HEAP32[HEAP32[i3 >> 2] >> 2] = HEAP32[i4 >> 2];
  HEAP32[(HEAP32[i3 >> 2] | 0) + 4 >> 2] = HEAP32[i5 >> 2];
  HEAP32[i2 >> 2] = HEAP32[i3 >> 2];
  i6 = HEAP32[i2 >> 2] | 0;
  STACKTOP = i1;
  return i6 | 0;
 }
 return 0;
}

function _oggz_dlist_deliter(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i4 = i3 + 20 | 0;
 i5 = i3 + 16 | 0;
 i6 = i3 + 12 | 0;
 i7 = i3 + 8 | 0;
 i8 = i3 + 4 | 0;
 i9 = i3;
 HEAP32[i4 >> 2] = i1;
 HEAP32[i5 >> 2] = i2;
 HEAP32[i8 >> 2] = 0;
 HEAP32[i6 >> 2] = HEAP32[HEAP32[HEAP32[i4 >> 2] >> 2] >> 2];
 while (1) {
  if ((HEAP32[i6 >> 2] | 0) == (HEAP32[(HEAP32[i4 >> 2] | 0) + 4 >> 2] | 0)) {
   i10 = 7;
   break;
  }
  HEAP32[i9 >> 2] = FUNCTION_TABLE_ii[HEAP32[i5 >> 2] & 15](HEAP32[(HEAP32[i6 >> 2] | 0) + 8 >> 2] | 0) | 0;
  if ((HEAP32[i9 >> 2] | 0) == -1) HEAP32[i8 >> 2] = -1;
  if (!(HEAP32[i9 >> 2] | 0)) {
   i10 = 7;
   break;
  }
  HEAP32[i7 >> 2] = HEAP32[HEAP32[i6 >> 2] >> 2];
  HEAP32[HEAP32[(HEAP32[i6 >> 2] | 0) + 4 >> 2] >> 2] = HEAP32[HEAP32[i6 >> 2] >> 2];
  HEAP32[(HEAP32[HEAP32[i6 >> 2] >> 2] | 0) + 4 >> 2] = HEAP32[(HEAP32[i6 >> 2] | 0) + 4 >> 2];
  _free(HEAP32[i6 >> 2] | 0);
  HEAP32[i6 >> 2] = HEAP32[i7 >> 2];
 }
 if ((i10 | 0) == 7) {
  STACKTOP = i3;
  return HEAP32[i8 >> 2] | 0;
 }
 return 0;
}

function _auto_oggpcm2(i1, i2, i3, i4, i5) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 i5 = i5 | 0;
 var i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0;
 i6 = STACKTOP;
 STACKTOP = STACKTOP + 48 | 0;
 i7 = i6 + 32 | 0;
 i8 = i6 + 28 | 0;
 i9 = i6 + 24 | 0;
 i10 = i6 + 20 | 0;
 i11 = i6 + 16 | 0;
 i12 = i6 + 8 | 0;
 i13 = i6;
 HEAP32[i8 >> 2] = i1;
 HEAP32[i9 >> 2] = i2;
 HEAP32[i10 >> 2] = i3;
 HEAP32[i11 >> 2] = i4;
 HEAP32[i6 + 12 >> 2] = i5;
 HEAP32[i12 >> 2] = HEAP32[i10 >> 2];
 if ((HEAP32[i11 >> 2] | 0) < 28) {
  HEAP32[i7 >> 2] = 0;
  i14 = HEAP32[i7 >> 2] | 0;
  STACKTOP = i6;
  return i14 | 0;
 } else {
  i11 = _int32_be_at((HEAP32[i12 >> 2] | 0) + 16 | 0) | 0;
  i12 = i13;
  HEAP32[i12 >> 2] = i11;
  HEAP32[i12 + 4 >> 2] = ((i11 | 0) < 0) << 31 >> 31;
  i11 = i13;
  _oggz_set_granulerate(HEAP32[i8 >> 2] | 0, HEAP32[i9 >> 2] | 0, HEAP32[i11 >> 2] | 0, HEAP32[i11 + 4 >> 2] | 0, 1e3, 0) | 0;
  _oggz_stream_set_numheaders(HEAP32[i8 >> 2] | 0, HEAP32[i9 >> 2] | 0, 3) | 0;
  HEAP32[i7 >> 2] = 1;
  i14 = HEAP32[i7 >> 2] | 0;
  STACKTOP = i6;
  return i14 | 0;
 }
 return 0;
}

function _auto_flac0(i1, i2, i3, i4, i5) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 i5 = i5 | 0;
 var i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0;
 i6 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i7 = i6 + 28 | 0;
 i8 = i6 + 24 | 0;
 i9 = i6 + 20 | 0;
 i10 = i6 + 8 | 0;
 i11 = i6;
 HEAP32[i7 >> 2] = i1;
 HEAP32[i8 >> 2] = i2;
 HEAP32[i9 >> 2] = i3;
 HEAP32[i6 + 16 >> 2] = i4;
 HEAP32[i6 + 12 >> 2] = i5;
 HEAP32[i10 >> 2] = HEAP32[i9 >> 2];
 i9 = i11;
 HEAP32[i9 >> 2] = 0;
 HEAP32[i9 + 4 >> 2] = 0;
 i9 = (HEAPU8[(HEAP32[i10 >> 2] | 0) + 14 >> 0] | 0) << 12;
 i5 = (HEAPU8[(HEAP32[i10 >> 2] | 0) + 15 >> 0] | 0) << 4;
 i4 = (HEAPU8[(HEAP32[i10 >> 2] | 0) + 16 >> 0] | 0) >> 4 & 15;
 i10 = i11;
 HEAP32[i10 >> 2] = i9 | i5 | i4;
 HEAP32[i10 + 4 >> 2] = ((i9 | 0) < 0) << 31 >> 31 | ((i5 | 0) < 0) << 31 >> 31 | ((i4 | 0) < 0) << 31 >> 31;
 i4 = i11;
 _oggz_set_granulerate(HEAP32[i7 >> 2] | 0, HEAP32[i8 >> 2] | 0, HEAP32[i4 >> 2] | 0, HEAP32[i4 + 4 >> 2] | 0, 1e3, 0) | 0;
 _oggz_stream_set_numheaders(HEAP32[i7 >> 2] | 0, HEAP32[i8 >> 2] | 0, 3) | 0;
 STACKTOP = i6;
 return 1;
}

function __oggz_comment_set_vendor(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i5 = i4 + 16 | 0;
 i6 = i4 + 12 | 0;
 i7 = i4 + 8 | 0;
 i8 = i4 + 4 | 0;
 i9 = i4;
 HEAP32[i6 >> 2] = i1;
 HEAP32[i7 >> 2] = i2;
 HEAP32[i8 >> 2] = i3;
 if (!(HEAP32[i6 >> 2] | 0)) {
  HEAP32[i5 >> 2] = -2;
  i10 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i10 | 0;
 }
 HEAP32[i9 >> 2] = _oggz_get_stream(HEAP32[i6 >> 2] | 0, HEAP32[i7 >> 2] | 0) | 0;
 if (!(HEAP32[i9 >> 2] | 0)) {
  HEAP32[i5 >> 2] = -20;
  i10 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i10 | 0;
 }
 if (HEAP32[(HEAP32[i9 >> 2] | 0) + 412 >> 2] | 0) _free(HEAP32[(HEAP32[i9 >> 2] | 0) + 412 >> 2] | 0);
 i7 = _oggz_strdup(HEAP32[i8 >> 2] | 0) | 0;
 HEAP32[(HEAP32[i9 >> 2] | 0) + 412 >> 2] = i7;
 if (!i7) {
  HEAP32[i5 >> 2] = -18;
  i10 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i10 | 0;
 } else {
  HEAP32[i5 >> 2] = 0;
  i10 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i10 | 0;
 }
 return 0;
}

function _oggskel_get_segment_len(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i4 = i3 + 16 | 0;
 i5 = i3 + 12 | 0;
 i6 = i3 + 8 | 0;
 i7 = i3 + 4 | 0;
 i8 = i3;
 HEAP32[i5 >> 2] = i1;
 HEAP32[i6 >> 2] = i2;
 HEAP32[i7 >> 2] = -1;
 HEAP32[i8 >> 2] = 0;
 i2 = _getter_error_check(HEAP32[i5 >> 2] | 0, HEAP32[i6 >> 2] | 0) | 0;
 HEAP32[i7 >> 2] = i2;
 if ((i2 | 0) < 0) {
  HEAP32[i4 >> 2] = HEAP32[i7 >> 2];
  i9 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i9 | 0;
 }
 HEAP32[i8 >> 2] = (HEAPU16[HEAP32[i5 >> 2] >> 1] | 0) << 16 | (HEAPU16[(HEAP32[i5 >> 2] | 0) + 2 >> 1] | 0);
 if ((HEAP32[i8 >> 2] | 0) >>> 0 < 196610) {
  HEAP32[i4 >> 2] = -1;
  i9 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i9 | 0;
 } else {
  i8 = (HEAP32[i5 >> 2] | 0) + 96 | 0;
  i5 = HEAP32[i8 + 4 >> 2] | 0;
  i7 = HEAP32[i6 >> 2] | 0;
  HEAP32[i7 >> 2] = HEAP32[i8 >> 2];
  HEAP32[i7 + 4 >> 2] = i5;
  HEAP32[i4 >> 2] = 0;
  i9 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i9 | 0;
 }
 return 0;
}

function _oggz_io_seek(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i5 = i4 + 16 | 0;
 i6 = i4 + 12 | 0;
 i7 = i4 + 8 | 0;
 i8 = i4 + 4 | 0;
 i9 = i4;
 HEAP32[i6 >> 2] = i1;
 HEAP32[i7 >> 2] = i2;
 HEAP32[i8 >> 2] = i3;
 i3 = HEAP32[i6 >> 2] | 0;
 do if (HEAP32[(HEAP32[i6 >> 2] | 0) + 4 >> 2] | 0) if ((_fseek(HEAP32[i3 + 4 >> 2] | 0, HEAP32[i7 >> 2] | 0, HEAP32[i8 >> 2] | 0) | 0) == -1) HEAP32[i5 >> 2] = -10; else i10 = 10; else {
  i2 = HEAP32[i3 + 8 >> 2] | 0;
  HEAP32[i9 >> 2] = i2;
  if (!i2) {
   HEAP32[i5 >> 2] = -3;
   break;
  }
  if (!(HEAP32[(HEAP32[i9 >> 2] | 0) + 16 >> 2] | 0)) {
   HEAP32[i5 >> 2] = -1;
   break;
  }
  if ((FUNCTION_TABLE_iiii[HEAP32[(HEAP32[i9 >> 2] | 0) + 16 >> 2] & 7](HEAP32[(HEAP32[i9 >> 2] | 0) + 20 >> 2] | 0, HEAP32[i7 >> 2] | 0, HEAP32[i8 >> 2] | 0) | 0) == -1) HEAP32[i5 >> 2] = -1; else i10 = 10;
 } while (0);
 if ((i10 | 0) == 10) HEAP32[i5 >> 2] = 0;
 STACKTOP = i4;
 return HEAP32[i5 >> 2] | 0;
}

function _oggz_vector_grow(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2 + 12 | 0;
 i4 = i2 + 8 | 0;
 i5 = i2 + 4 | 0;
 i6 = i2;
 HEAP32[i4 >> 2] = i1;
 i1 = (HEAP32[i4 >> 2] | 0) + 4 | 0;
 HEAP32[i1 >> 2] = (HEAP32[i1 >> 2] | 0) + 1;
 do if ((HEAP32[(HEAP32[i4 >> 2] | 0) + 4 >> 2] | 0) > (HEAP32[HEAP32[i4 >> 2] >> 2] | 0)) {
  if (!(HEAP32[HEAP32[i4 >> 2] >> 2] | 0)) HEAP32[i6 >> 2] = 1; else HEAP32[i6 >> 2] = HEAP32[HEAP32[i4 >> 2] >> 2] << 1;
  HEAP32[i5 >> 2] = _realloc(HEAP32[(HEAP32[i4 >> 2] | 0) + 8 >> 2] | 0, HEAP32[i6 >> 2] << 2) | 0;
  if (HEAP32[i5 >> 2] | 0) {
   HEAP32[HEAP32[i4 >> 2] >> 2] = HEAP32[i6 >> 2];
   HEAP32[(HEAP32[i4 >> 2] | 0) + 8 >> 2] = HEAP32[i5 >> 2];
   break;
  }
  i1 = (HEAP32[i4 >> 2] | 0) + 4 | 0;
  HEAP32[i1 >> 2] = (HEAP32[i1 >> 2] | 0) + -1;
  HEAP32[i3 >> 2] = 0;
  i7 = HEAP32[i3 >> 2] | 0;
  STACKTOP = i2;
  return i7 | 0;
 } while (0);
 HEAP32[i3 >> 2] = HEAP32[i4 >> 2];
 i7 = HEAP32[i3 >> 2] | 0;
 STACKTOP = i2;
 return i7 | 0;
}

function _oggz_vector_find_with(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i5 = i4 + 20 | 0;
 i6 = i4 + 16 | 0;
 i7 = i4 + 12 | 0;
 i8 = i4 + 8 | 0;
 i9 = i4 + 4 | 0;
 i10 = i4;
 HEAP32[i6 >> 2] = i1;
 HEAP32[i7 >> 2] = i2;
 HEAP32[i8 >> 2] = i3;
 HEAP32[i10 >> 2] = 0;
 while (1) {
  if ((HEAP32[i10 >> 2] | 0) >= (HEAP32[(HEAP32[i6 >> 2] | 0) + 4 >> 2] | 0)) {
   i11 = 6;
   break;
  }
  HEAP32[i9 >> 2] = HEAP32[(HEAP32[(HEAP32[i6 >> 2] | 0) + 8 >> 2] | 0) + (HEAP32[i10 >> 2] << 2) >> 2];
  if (FUNCTION_TABLE_iii[HEAP32[i7 >> 2] & 1](HEAP32[i9 >> 2] | 0, HEAP32[i8 >> 2] | 0) | 0) {
   i11 = 4;
   break;
  }
  HEAP32[i10 >> 2] = (HEAP32[i10 >> 2] | 0) + 1;
 }
 if ((i11 | 0) == 4) {
  HEAP32[i5 >> 2] = HEAP32[i9 >> 2];
  i12 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i12 | 0;
 } else if ((i11 | 0) == 6) {
  HEAP32[i5 >> 2] = 0;
  i12 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i12 | 0;
 }
 return 0;
}

function _oggz_set_first_granule(i1, i2, i3, i4) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 var i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0;
 i5 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i6 = i5 + 20 | 0;
 i7 = i5 + 16 | 0;
 i8 = i5 + 12 | 0;
 i9 = i5;
 i10 = i5 + 8 | 0;
 HEAP32[i7 >> 2] = i1;
 HEAP32[i8 >> 2] = i2;
 i2 = i9;
 HEAP32[i2 >> 2] = i3;
 HEAP32[i2 + 4 >> 2] = i4;
 if (!(HEAP32[i7 >> 2] | 0)) {
  HEAP32[i6 >> 2] = -2;
  i11 = HEAP32[i6 >> 2] | 0;
  STACKTOP = i5;
  return i11 | 0;
 }
 HEAP32[i10 >> 2] = _oggz_get_stream(HEAP32[i7 >> 2] | 0, HEAP32[i8 >> 2] | 0) | 0;
 if (!(HEAP32[i10 >> 2] | 0)) {
  HEAP32[i6 >> 2] = -20;
  i11 = HEAP32[i6 >> 2] | 0;
  STACKTOP = i5;
  return i11 | 0;
 } else {
  i4 = i9;
  i9 = HEAP32[i4 + 4 >> 2] | 0;
  i2 = (HEAP32[i10 >> 2] | 0) + 392 | 0;
  HEAP32[i2 >> 2] = HEAP32[i4 >> 2];
  HEAP32[i2 + 4 >> 2] = i9;
  HEAP32[i6 >> 2] = _oggz_metric_update(HEAP32[i7 >> 2] | 0, HEAP32[i8 >> 2] | 0) | 0;
  i11 = HEAP32[i6 >> 2] | 0;
  STACKTOP = i5;
  return i11 | 0;
 }
 return 0;
}

function _oggz_dlist_append(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i4 = i3 + 12 | 0;
 i5 = i3 + 8 | 0;
 i6 = i3 + 4 | 0;
 i7 = i3;
 HEAP32[i5 >> 2] = i1;
 HEAP32[i6 >> 2] = i2;
 if (!(HEAP32[i5 >> 2] | 0)) {
  HEAP32[i4 >> 2] = -1;
  i8 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i8 | 0;
 }
 HEAP32[i7 >> 2] = _malloc(12) | 0;
 if (!(HEAP32[i7 >> 2] | 0)) {
  HEAP32[i4 >> 2] = -1;
  i8 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i8 | 0;
 } else {
  HEAP32[(HEAP32[i7 >> 2] | 0) + 8 >> 2] = HEAP32[i6 >> 2];
  HEAP32[HEAP32[i7 >> 2] >> 2] = HEAP32[(HEAP32[i5 >> 2] | 0) + 4 >> 2];
  HEAP32[(HEAP32[i7 >> 2] | 0) + 4 >> 2] = HEAP32[(HEAP32[(HEAP32[i5 >> 2] | 0) + 4 >> 2] | 0) + 4 >> 2];
  HEAP32[HEAP32[(HEAP32[i7 >> 2] | 0) + 4 >> 2] >> 2] = HEAP32[i7 >> 2];
  HEAP32[(HEAP32[HEAP32[i7 >> 2] >> 2] | 0) + 4 >> 2] = HEAP32[i7 >> 2];
  HEAP32[i4 >> 2] = 0;
  i8 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i8 | 0;
 }
 return 0;
}

function _oggz_io_set_tell(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i5 = i4 + 12 | 0;
 i6 = i4 + 8 | 0;
 i7 = i4 + 4 | 0;
 i8 = i4;
 HEAP32[i6 >> 2] = i1;
 HEAP32[i7 >> 2] = i2;
 HEAP32[i8 >> 2] = i3;
 if (!(HEAP32[i6 >> 2] | 0)) {
  HEAP32[i5 >> 2] = -2;
  i9 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i9 | 0;
 }
 if (HEAP32[(HEAP32[i6 >> 2] | 0) + 4 >> 2] | 0) {
  HEAP32[i5 >> 2] = -3;
  i9 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i9 | 0;
 }
 if ((HEAP32[(HEAP32[i6 >> 2] | 0) + 8 >> 2] | 0) == 0 ? (_oggz_io_init(HEAP32[i6 >> 2] | 0) | 0) == -1 : 0) {
  HEAP32[i5 >> 2] = -18;
  i9 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i9 | 0;
 }
 HEAP32[(HEAP32[(HEAP32[i6 >> 2] | 0) + 8 >> 2] | 0) + 24 >> 2] = HEAP32[i7 >> 2];
 HEAP32[(HEAP32[(HEAP32[i6 >> 2] | 0) + 8 >> 2] | 0) + 28 >> 2] = HEAP32[i8 >> 2];
 HEAP32[i5 >> 2] = 0;
 i9 = HEAP32[i5 >> 2] | 0;
 STACKTOP = i4;
 return i9 | 0;
}

function _oggz_io_set_seek(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i5 = i4 + 12 | 0;
 i6 = i4 + 8 | 0;
 i7 = i4 + 4 | 0;
 i8 = i4;
 HEAP32[i6 >> 2] = i1;
 HEAP32[i7 >> 2] = i2;
 HEAP32[i8 >> 2] = i3;
 if (!(HEAP32[i6 >> 2] | 0)) {
  HEAP32[i5 >> 2] = -2;
  i9 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i9 | 0;
 }
 if (HEAP32[(HEAP32[i6 >> 2] | 0) + 4 >> 2] | 0) {
  HEAP32[i5 >> 2] = -3;
  i9 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i9 | 0;
 }
 if ((HEAP32[(HEAP32[i6 >> 2] | 0) + 8 >> 2] | 0) == 0 ? (_oggz_io_init(HEAP32[i6 >> 2] | 0) | 0) == -1 : 0) {
  HEAP32[i5 >> 2] = -18;
  i9 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i9 | 0;
 }
 HEAP32[(HEAP32[(HEAP32[i6 >> 2] | 0) + 8 >> 2] | 0) + 16 >> 2] = HEAP32[i7 >> 2];
 HEAP32[(HEAP32[(HEAP32[i6 >> 2] | 0) + 8 >> 2] | 0) + 20 >> 2] = HEAP32[i8 >> 2];
 HEAP32[i5 >> 2] = 0;
 i9 = HEAP32[i5 >> 2] | 0;
 STACKTOP = i4;
 return i9 | 0;
}

function _oggz_io_set_read(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i5 = i4 + 12 | 0;
 i6 = i4 + 8 | 0;
 i7 = i4 + 4 | 0;
 i8 = i4;
 HEAP32[i6 >> 2] = i1;
 HEAP32[i7 >> 2] = i2;
 HEAP32[i8 >> 2] = i3;
 if (!(HEAP32[i6 >> 2] | 0)) {
  HEAP32[i5 >> 2] = -2;
  i9 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i9 | 0;
 }
 if (HEAP32[(HEAP32[i6 >> 2] | 0) + 4 >> 2] | 0) {
  HEAP32[i5 >> 2] = -3;
  i9 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i9 | 0;
 }
 if ((HEAP32[(HEAP32[i6 >> 2] | 0) + 8 >> 2] | 0) == 0 ? (_oggz_io_init(HEAP32[i6 >> 2] | 0) | 0) == -1 : 0) {
  HEAP32[i5 >> 2] = -18;
  i9 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i9 | 0;
 }
 HEAP32[HEAP32[(HEAP32[i6 >> 2] | 0) + 8 >> 2] >> 2] = HEAP32[i7 >> 2];
 HEAP32[(HEAP32[(HEAP32[i6 >> 2] | 0) + 8 >> 2] | 0) + 4 >> 2] = HEAP32[i8 >> 2];
 HEAP32[i5 >> 2] = 0;
 i9 = HEAP32[i5 >> 2] | 0;
 STACKTOP = i4;
 return i9 | 0;
}

function _oggz_index_len(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i5 = i4 + 12 | 0;
 i6 = i4 + 8 | 0;
 i7 = i4 + 16 | 0;
 i8 = i4 + 4 | 0;
 i9 = i4;
 HEAP32[i6 >> 2] = i1;
 HEAP8[i7 >> 0] = i2;
 HEAP32[i8 >> 2] = i3;
 HEAP32[i9 >> 2] = 0;
 while (1) {
  if (!(HEAP8[HEAP32[i6 >> 2] >> 0] | 0)) {
   i10 = 7;
   break;
  }
  if ((HEAP32[i9 >> 2] | 0) >= (HEAP32[i8 >> 2] | 0)) {
   i10 = 7;
   break;
  }
  if ((HEAP8[HEAP32[i6 >> 2] >> 0] | 0) == (HEAP8[i7 >> 0] | 0)) {
   i10 = 5;
   break;
  }
  HEAP32[i9 >> 2] = (HEAP32[i9 >> 2] | 0) + 1;
  HEAP32[i6 >> 2] = (HEAP32[i6 >> 2] | 0) + 1;
 }
 if ((i10 | 0) == 5) {
  HEAP32[i5 >> 2] = HEAP32[i6 >> 2];
  i11 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i11 | 0;
 } else if ((i10 | 0) == 7) {
  HEAP32[i5 >> 2] = 0;
  i11 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i11 | 0;
 }
 return 0;
}

function _oggz_vector_tail_insertion_sort(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0, i5 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2 + 4 | 0;
 i4 = i2;
 HEAP32[i3 >> 2] = i1;
 if (!(HEAP32[(HEAP32[i3 >> 2] | 0) + 12 >> 2] | 0)) {
  STACKTOP = i2;
  return;
 }
 HEAP32[i4 >> 2] = (HEAP32[(HEAP32[i3 >> 2] | 0) + 4 >> 2] | 0) - 1;
 while (1) {
  if ((HEAP32[i4 >> 2] | 0) <= 0) {
   i5 = 6;
   break;
  }
  if ((FUNCTION_TABLE_iiii[HEAP32[(HEAP32[i3 >> 2] | 0) + 12 >> 2] & 7](HEAP32[(HEAP32[(HEAP32[i3 >> 2] | 0) + 8 >> 2] | 0) + ((HEAP32[i4 >> 2] | 0) - 1 << 2) >> 2] | 0, HEAP32[(HEAP32[(HEAP32[i3 >> 2] | 0) + 8 >> 2] | 0) + (HEAP32[i4 >> 2] << 2) >> 2] | 0, HEAP32[(HEAP32[i3 >> 2] | 0) + 16 >> 2] | 0) | 0) <= 0) {
   i5 = 6;
   break;
  }
  __array_swap(HEAP32[(HEAP32[i3 >> 2] | 0) + 8 >> 2] | 0, HEAP32[i4 >> 2] | 0, (HEAP32[i4 >> 2] | 0) - 1 | 0);
  HEAP32[i4 >> 2] = (HEAP32[i4 >> 2] | 0) + -1;
 }
 if ((i5 | 0) == 6) {
  STACKTOP = i2;
  return;
 }
}

function _oggz_io_tell(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2 + 12 | 0;
 i4 = i2 + 8 | 0;
 i5 = i2 + 4 | 0;
 i6 = i2;
 HEAP32[i4 >> 2] = i1;
 i1 = HEAP32[i4 >> 2] | 0;
 do if (HEAP32[(HEAP32[i4 >> 2] | 0) + 4 >> 2] | 0) {
  i7 = _ftell(HEAP32[i1 + 4 >> 2] | 0) | 0;
  HEAP32[i6 >> 2] = i7;
  if ((i7 | 0) == -1) HEAP32[i3 >> 2] = -1; else i8 = 10;
 } else {
  i7 = HEAP32[i1 + 8 >> 2] | 0;
  HEAP32[i5 >> 2] = i7;
  if (!i7) {
   HEAP32[i3 >> 2] = -3;
   break;
  }
  if (!(HEAP32[(HEAP32[i5 >> 2] | 0) + 24 >> 2] | 0)) {
   HEAP32[i3 >> 2] = -1;
   break;
  }
  i7 = FUNCTION_TABLE_ii[HEAP32[(HEAP32[i5 >> 2] | 0) + 24 >> 2] & 15](HEAP32[(HEAP32[i5 >> 2] | 0) + 28 >> 2] | 0) | 0;
  HEAP32[i6 >> 2] = i7;
  if ((i7 | 0) == -1) HEAP32[i3 >> 2] = -1; else i8 = 10;
 } while (0);
 if ((i8 | 0) == 10) HEAP32[i3 >> 2] = HEAP32[i6 >> 2];
 STACKTOP = i2;
 return HEAP32[i3 >> 2] | 0;
}

function _read_var_length(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i4 = i3 + 16 | 0;
 i5 = i3 + 12 | 0;
 i6 = i3 + 8 | 0;
 i7 = i3;
 HEAP32[i4 >> 2] = i1;
 HEAP32[i5 >> 2] = i2;
 HEAP32[i6 >> 2] = 0;
 i2 = i7;
 HEAP32[i2 >> 2] = 0;
 HEAP32[i2 + 4 >> 2] = 0;
 i2 = HEAP32[i5 >> 2] | 0;
 HEAP32[i2 >> 2] = 0;
 HEAP32[i2 + 4 >> 2] = 0;
 do {
  i2 = i7;
  HEAP32[i2 >> 2] = HEAPU8[HEAP32[i4 >> 2] >> 0];
  HEAP32[i2 + 4 >> 2] = 0;
  i2 = _bitshift64Shl(HEAP32[i7 >> 2] & 127 | 0, 0, HEAP32[i6 >> 2] | 0) | 0;
  i1 = HEAP32[i5 >> 2] | 0;
  i8 = i1;
  i9 = HEAP32[i8 + 4 >> 2] | tempRet0;
  i10 = i1;
  HEAP32[i10 >> 2] = HEAP32[i8 >> 2] | i2;
  HEAP32[i10 + 4 >> 2] = i9;
  HEAP32[i6 >> 2] = (HEAP32[i6 >> 2] | 0) + 7;
  HEAP32[i4 >> 2] = (HEAP32[i4 >> 2] | 0) + 1;
 } while (0 ? 1 : (HEAP32[i7 >> 2] & 128 | 0) != 128);
 STACKTOP = i3;
 return HEAP32[i4 >> 2] | 0;
}

function _oggz_seek_raw(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i5 = i4 + 20 | 0;
 i6 = i4 + 16 | 0;
 i7 = i4 + 12 | 0;
 i8 = i4 + 8 | 0;
 i9 = i4 + 4 | 0;
 i10 = i4;
 HEAP32[i6 >> 2] = i1;
 HEAP32[i7 >> 2] = i2;
 HEAP32[i8 >> 2] = i3;
 HEAP32[i9 >> 2] = (HEAP32[i6 >> 2] | 0) + 112;
 if ((_oggz_io_seek(HEAP32[i6 >> 2] | 0, HEAP32[i7 >> 2] | 0, HEAP32[i8 >> 2] | 0) | 0) == -1) {
  HEAP32[i5 >> 2] = -1;
  i11 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i11 | 0;
 } else {
  HEAP32[i10 >> 2] = _oggz_io_tell(HEAP32[i6 >> 2] | 0) | 0;
  HEAP32[(HEAP32[i6 >> 2] | 0) + 64 >> 2] = HEAP32[i10 >> 2];
  _ogg_sync_reset(HEAP32[i9 >> 2] | 0) | 0;
  _oggz_vector_foreach(HEAP32[(HEAP32[i6 >> 2] | 0) + 80 >> 2] | 0, 7) | 0;
  HEAP32[i5 >> 2] = HEAP32[i10 >> 2];
  i11 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i11 | 0;
 }
 return 0;
}

function _oggz_comment_validate_byname(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2 + 8 | 0;
 i4 = i2 + 4 | 0;
 i5 = i2;
 HEAP32[i4 >> 2] = i1;
 if (!(HEAP32[i4 >> 2] | 0)) {
  HEAP32[i3 >> 2] = 0;
  i6 = HEAP32[i3 >> 2] | 0;
  STACKTOP = i2;
  return i6 | 0;
 }
 HEAP32[i5 >> 2] = HEAP32[i4 >> 2];
 while (1) {
  if (!(HEAP8[HEAP32[i5 >> 2] >> 0] | 0)) {
   i7 = 10;
   break;
  }
  if ((HEAP8[HEAP32[i5 >> 2] >> 0] | 0) < 32) {
   i7 = 8;
   break;
  }
  if ((HEAP8[HEAP32[i5 >> 2] >> 0] | 0) > 125) {
   i7 = 8;
   break;
  }
  if ((HEAP8[HEAP32[i5 >> 2] >> 0] | 0) == 61) {
   i7 = 8;
   break;
  }
  HEAP32[i5 >> 2] = (HEAP32[i5 >> 2] | 0) + 1;
 }
 if ((i7 | 0) == 8) {
  HEAP32[i3 >> 2] = 0;
  i6 = HEAP32[i3 >> 2] | 0;
  STACKTOP = i2;
  return i6 | 0;
 } else if ((i7 | 0) == 10) {
  HEAP32[i3 >> 2] = 1;
  i6 = HEAP32[i3 >> 2] | 0;
  STACKTOP = i2;
  return i6 | 0;
 }
 return 0;
}

function _find_track_info(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i4 = i3 + 12 | 0;
 i5 = i3 + 8 | 0;
 i6 = i3 + 4 | 0;
 i7 = i3;
 HEAP32[i5 >> 2] = i1;
 HEAP32[i6 >> 2] = i2;
 HEAP32[i7 >> 2] = 0;
 HEAP32[i7 >> 2] = 0;
 while (1) {
  if ((HEAP32[i7 >> 2] | 0) >>> 0 >= (HEAP32[HEAP32[i5 >> 2] >> 2] | 0) >>> 0) {
   i8 = 6;
   break;
  }
  i9 = HEAP32[i7 >> 2] | 0;
  if ((HEAP32[(HEAP32[(HEAP32[i5 >> 2] | 0) + 4 >> 2] | 0) + ((HEAP32[i7 >> 2] | 0) * 12 | 0) >> 2] | 0) == (HEAP32[i6 >> 2] | 0)) {
   i8 = 4;
   break;
  }
  HEAP32[i7 >> 2] = i9 + 1;
 }
 if ((i8 | 0) == 4) {
  HEAP32[i4 >> 2] = (HEAP32[(HEAP32[i5 >> 2] | 0) + 4 >> 2] | 0) + (i9 * 12 | 0);
  i10 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i10 | 0;
 } else if ((i8 | 0) == 6) {
  HEAP32[i4 >> 2] = 0;
  i10 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i10 | 0;
 }
 return 0;
}

function _oggz_strdup_len(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i4 = i3 + 12 | 0;
 i5 = i3 + 8 | 0;
 i6 = i3 + 4 | 0;
 i7 = i3;
 HEAP32[i5 >> 2] = i1;
 HEAP32[i6 >> 2] = i2;
 do if (HEAP32[i5 >> 2] | 0) {
  if (!(HEAP32[i6 >> 2] | 0)) {
   HEAP32[i4 >> 2] = 0;
   break;
  }
  HEAP32[i6 >> 2] = (HEAP32[i6 >> 2] | 0) >>> 0 < 4294967294 ? HEAP32[i6 >> 2] | 0 : -2;
  HEAP32[i7 >> 2] = _malloc((HEAP32[i6 >> 2] | 0) + 1 | 0) | 0;
  if (!(HEAP32[i7 >> 2] | 0)) {
   HEAP32[i4 >> 2] = 0;
   break;
  }
  if (!(_strncpy(HEAP32[i7 >> 2] | 0, HEAP32[i5 >> 2] | 0, HEAP32[i6 >> 2] | 0) | 0)) {
   _free(HEAP32[i7 >> 2] | 0);
   HEAP32[i4 >> 2] = 0;
   break;
  } else {
   HEAP8[(HEAP32[i7 >> 2] | 0) + (HEAP32[i6 >> 2] | 0) >> 0] = 0;
   HEAP32[i4 >> 2] = HEAP32[i7 >> 2];
   break;
  }
 } else HEAP32[i4 >> 2] = 0; while (0);
 STACKTOP = i3;
 return HEAP32[i4 >> 2] | 0;
}

function _fflush(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0;
 do if (i1) {
  if ((HEAP32[i1 + 76 >> 2] | 0) <= -1) {
   i2 = ___fflush_unlocked(i1) | 0;
   break;
  }
  i3 = (___lockfile(i1) | 0) == 0;
  i4 = ___fflush_unlocked(i1) | 0;
  if (i3) i2 = i4; else {
   ___unlockfile(i1);
   i2 = i4;
  }
 } else {
  if (!(HEAP32[567] | 0)) i5 = 0; else i5 = _fflush(HEAP32[567] | 0) | 0;
  ___lock(5368);
  i4 = HEAP32[1341] | 0;
  if (!i4) i6 = i5; else {
   i3 = i4;
   i4 = i5;
   while (1) {
    if ((HEAP32[i3 + 76 >> 2] | 0) > -1) i7 = ___lockfile(i3) | 0; else i7 = 0;
    if ((HEAP32[i3 + 20 >> 2] | 0) >>> 0 > (HEAP32[i3 + 28 >> 2] | 0) >>> 0) i8 = ___fflush_unlocked(i3) | 0 | i4; else i8 = i4;
    if (i7 | 0) ___unlockfile(i3);
    i3 = HEAP32[i3 + 56 >> 2] | 0;
    if (!i3) {
     i6 = i8;
     break;
    } else i4 = i8;
   }
  }
  ___unlock(5368);
  i2 = i6;
 } while (0);
 return i2 | 0;
}

function _strcasecmp(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0;
 i3 = HEAP8[i1 >> 0] | 0;
 L1 : do if (!(i3 << 24 >> 24)) {
  i4 = 0;
  i5 = i2;
 } else {
  i6 = i3;
  i7 = i3 & 255;
  i8 = i1;
  i9 = i2;
  while (1) {
   i10 = HEAP8[i9 >> 0] | 0;
   if (!(i10 << 24 >> 24)) {
    i4 = i6;
    i5 = i9;
    break L1;
   }
   if (i6 << 24 >> 24 != i10 << 24 >> 24 ? (i10 = _tolower(i7) | 0, (i10 | 0) != (_tolower(HEAPU8[i9 >> 0] | 0) | 0)) : 0) {
    i11 = i8;
    i12 = i9;
    break;
   }
   i8 = i8 + 1 | 0;
   i10 = i9 + 1 | 0;
   i13 = HEAP8[i8 >> 0] | 0;
   if (!(i13 << 24 >> 24)) {
    i4 = 0;
    i5 = i10;
    break L1;
   } else {
    i6 = i13;
    i7 = i13 & 255;
    i9 = i10;
   }
  }
  i4 = HEAP8[i11 >> 0] | 0;
  i5 = i12;
 } while (0);
 i12 = _tolower(i4 & 255) | 0;
 return i12 - (_tolower(HEAPU8[i5 >> 0] | 0) | 0) | 0;
}

function _oggskel_new() {
 var i1 = 0, i2 = 0, i3 = 0, i4 = 0, i5 = 0;
 i1 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i2 = i1 + 4 | 0;
 i3 = i1;
 HEAP32[i3 >> 2] = 0;
 HEAP32[i3 >> 2] = _calloc(1, 136) | 0;
 if (!(HEAP32[i3 >> 2] | 0)) {
  HEAP32[i2 >> 2] = 0;
  i4 = HEAP32[i2 >> 2] | 0;
  STACKTOP = i1;
  return i4 | 0;
 }
 i5 = _oggskel_vect_new() | 0;
 HEAP32[(HEAP32[i3 >> 2] | 0) + 112 >> 2] = i5;
 i5 = HEAP32[i3 >> 2] | 0;
 if (!(HEAP32[(HEAP32[i3 >> 2] | 0) + 112 >> 2] | 0)) {
  _free(i5);
  HEAP32[i2 >> 2] = 0;
  i4 = HEAP32[i2 >> 2] | 0;
  STACKTOP = i1;
  return i4 | 0;
 } else {
  HEAP16[i5 + 116 >> 1] = 0;
  HEAP16[(HEAP32[i3 >> 2] | 0) + 118 >> 1] = 0;
  HEAP32[(HEAP32[i3 >> 2] | 0) + 120 >> 2] = 0;
  i5 = (HEAP32[i3 >> 2] | 0) + 128 | 0;
  HEAP32[i5 >> 2] = 0;
  HEAP32[i5 + 4 >> 2] = 0;
  HEAP32[i2 >> 2] = HEAP32[i3 >> 2];
  i4 = HEAP32[i2 >> 2] | 0;
  STACKTOP = i1;
  return i4 | 0;
 }
 return 0;
}

function _extract_int64(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i4 = i3 + 16 | 0;
 i5 = i3 + 12 | 0;
 i6 = i3 + 8 | 0;
 i7 = i3 + 4 | 0;
 i8 = i3;
 HEAP32[i5 >> 2] = i1;
 HEAP32[i6 >> 2] = i2;
 HEAP32[i7 >> 2] = -1;
 HEAP32[i8 >> 2] = -1;
 if ((HEAP32[i5 >> 2] | 0) == 0 | (HEAP32[i6 >> 2] | 0) == 0) {
  HEAP32[i4 >> 2] = 0;
  i9 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i9 | 0;
 } else {
  HEAP32[i5 >> 2] = _extract_uint32(HEAP32[i5 >> 2] | 0, i7) | 0;
  HEAP32[i5 >> 2] = _extract_int32(HEAP32[i5 >> 2] | 0, i8) | 0;
  i2 = HEAP32[i8 >> 2] | 0;
  i8 = _i64Add(HEAP32[i7 >> 2] | 0, 0, 0, i2 | 0) | 0;
  i2 = HEAP32[i6 >> 2] | 0;
  HEAP32[i2 >> 2] = i8;
  HEAP32[i2 + 4 >> 2] = tempRet0;
  HEAP32[i4 >> 2] = HEAP32[i5 >> 2];
  i9 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i9 | 0;
 }
 return 0;
}

function _pad(i1, i2, i3, i4, i5) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 i5 = i5 | 0;
 var i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0, i13 = 0, i14 = 0, i15 = 0;
 i6 = STACKTOP;
 STACKTOP = STACKTOP + 256 | 0;
 i7 = i6;
 do if ((i3 | 0) > (i4 | 0) & (i5 & 73728 | 0) == 0) {
  i8 = i3 - i4 | 0;
  _memset(i7 | 0, i2 | 0, (i8 >>> 0 > 256 ? 256 : i8) | 0) | 0;
  i9 = HEAP32[i1 >> 2] | 0;
  i10 = (i9 & 32 | 0) == 0;
  if (i8 >>> 0 > 255) {
   i11 = i3 - i4 | 0;
   i12 = i8;
   i13 = i9;
   i9 = i10;
   while (1) {
    if (i9) {
     ___fwritex(i7, 256, i1) | 0;
     i14 = HEAP32[i1 >> 2] | 0;
    } else i14 = i13;
    i12 = i12 + -256 | 0;
    i9 = (i14 & 32 | 0) == 0;
    if (i12 >>> 0 <= 255) break; else i13 = i14;
   }
   if (i9) i15 = i11 & 255; else break;
  } else if (i10) i15 = i8; else break;
  ___fwritex(i7, i15, i1) | 0;
 } while (0);
 STACKTOP = i6;
 return;
}

function _oggz_set_granuleshift(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i5 = i4 + 16 | 0;
 i6 = i4 + 12 | 0;
 i7 = i4 + 8 | 0;
 i8 = i4 + 4 | 0;
 i9 = i4;
 HEAP32[i6 >> 2] = i1;
 HEAP32[i7 >> 2] = i2;
 HEAP32[i8 >> 2] = i3;
 if (!(HEAP32[i6 >> 2] | 0)) {
  HEAP32[i5 >> 2] = -2;
  i10 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i10 | 0;
 }
 HEAP32[i9 >> 2] = _oggz_get_stream(HEAP32[i6 >> 2] | 0, HEAP32[i7 >> 2] | 0) | 0;
 if (!(HEAP32[i9 >> 2] | 0)) {
  HEAP32[i5 >> 2] = -20;
  i10 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i10 | 0;
 } else {
  HEAP32[(HEAP32[i9 >> 2] | 0) + 408 >> 2] = HEAP32[i8 >> 2];
  HEAP32[i5 >> 2] = _oggz_metric_update(HEAP32[i6 >> 2] | 0, HEAP32[i7 >> 2] | 0) | 0;
  i10 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i10 | 0;
 }
 return 0;
}

function _fmt_u(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0, i12 = 0;
 if (i2 >>> 0 > 0 | (i2 | 0) == 0 & i1 >>> 0 > 4294967295) {
  i4 = i3;
  i5 = i1;
  i6 = i2;
  while (1) {
   i2 = ___uremdi3(i5 | 0, i6 | 0, 10, 0) | 0;
   i7 = i4 + -1 | 0;
   HEAP8[i7 >> 0] = i2 | 48;
   i2 = ___udivdi3(i5 | 0, i6 | 0, 10, 0) | 0;
   if (i6 >>> 0 > 9 | (i6 | 0) == 9 & i5 >>> 0 > 4294967295) {
    i4 = i7;
    i5 = i2;
    i6 = tempRet0;
   } else {
    i8 = i7;
    i9 = i2;
    break;
   }
  }
  i10 = i8;
  i11 = i9;
 } else {
  i10 = i3;
  i11 = i1;
 }
 if (!i11) i12 = i10; else {
  i1 = i10;
  i10 = i11;
  while (1) {
   i11 = i1 + -1 | 0;
   HEAP8[i11 >> 0] = (i10 >>> 0) % 10 | 0 | 48;
   if (i10 >>> 0 < 10) {
    i12 = i11;
    break;
   } else {
    i1 = i11;
    i10 = (i10 >>> 0) / 10 | 0;
   }
  }
 }
 return i12 | 0;
}

function _oggskel_vect_add_index(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i5 = i4 + 16 | 0;
 i6 = i4 + 12 | 0;
 i7 = i4 + 8 | 0;
 i8 = i4 + 4 | 0;
 i9 = i4;
 HEAP32[i6 >> 2] = i1;
 HEAP32[i7 >> 2] = i2;
 HEAP32[i8 >> 2] = i3;
 HEAP32[i9 >> 2] = 0;
 do if (HEAP32[i6 >> 2] | 0) {
  if (!(HEAP32[i7 >> 2] | 0)) {
   HEAP32[i5 >> 2] = -1;
   break;
  }
  HEAP32[i9 >> 2] = _get_track_nfo(HEAP32[i6 >> 2] | 0, HEAP32[i8 >> 2] | 0) | 0;
  if (!(HEAP32[i9 >> 2] | 0)) {
   HEAP32[i5 >> 2] = -4;
   break;
  }
  if (HEAP32[(HEAP32[i9 >> 2] | 0) + 8 >> 2] | 0) {
   HEAP32[i5 >> 2] = -1;
   break;
  } else {
   HEAP32[(HEAP32[i9 >> 2] | 0) + 8 >> 2] = HEAP32[i7 >> 2];
   HEAP32[i5 >> 2] = 1;
   break;
  }
 } else HEAP32[i5 >> 2] = -1; while (0);
 STACKTOP = i4;
 return HEAP32[i5 >> 2] | 0;
}

function _oggskel_vect_add_bone(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i5 = i4 + 16 | 0;
 i6 = i4 + 12 | 0;
 i7 = i4 + 8 | 0;
 i8 = i4 + 4 | 0;
 i9 = i4;
 HEAP32[i6 >> 2] = i1;
 HEAP32[i7 >> 2] = i2;
 HEAP32[i8 >> 2] = i3;
 HEAP32[i9 >> 2] = 0;
 do if (HEAP32[i6 >> 2] | 0) {
  if (!(HEAP32[i7 >> 2] | 0)) {
   HEAP32[i5 >> 2] = -1;
   break;
  }
  HEAP32[i9 >> 2] = _get_track_nfo(HEAP32[i6 >> 2] | 0, HEAP32[i8 >> 2] | 0) | 0;
  if (!(HEAP32[i9 >> 2] | 0)) {
   HEAP32[i5 >> 2] = -4;
   break;
  }
  if (HEAP32[(HEAP32[i9 >> 2] | 0) + 4 >> 2] | 0) {
   HEAP32[i5 >> 2] = -1;
   break;
  } else {
   HEAP32[(HEAP32[i9 >> 2] | 0) + 4 >> 2] = HEAP32[i7 >> 2];
   HEAP32[i5 >> 2] = 1;
   break;
  }
 } else HEAP32[i5 >> 2] = -1; while (0);
 STACKTOP = i4;
 return HEAP32[i5 >> 2] | 0;
}

function _oggz_comment_cmp(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i5 = i4 + 12 | 0;
 i6 = i4 + 8 | 0;
 i7 = i4 + 4 | 0;
 HEAP32[i6 >> 2] = i1;
 HEAP32[i7 >> 2] = i2;
 HEAP32[i4 >> 2] = i3;
 do if ((HEAP32[i6 >> 2] | 0) != (HEAP32[i7 >> 2] | 0)) {
  if (!((HEAP32[i6 >> 2] | 0) != 0 & (HEAP32[i7 >> 2] | 0) != 0)) {
   HEAP32[i5 >> 2] = 0;
   break;
  }
  if (_strcasecmp(HEAP32[HEAP32[i6 >> 2] >> 2] | 0, HEAP32[HEAP32[i7 >> 2] >> 2] | 0) | 0) {
   HEAP32[i5 >> 2] = 0;
   break;
  }
  if (_strcmp(HEAP32[(HEAP32[i6 >> 2] | 0) + 4 >> 2] | 0, HEAP32[(HEAP32[i7 >> 2] | 0) + 4 >> 2] | 0) | 0) {
   HEAP32[i5 >> 2] = 0;
   break;
  } else {
   HEAP32[i5 >> 2] = 1;
   break;
  }
 } else HEAP32[i5 >> 2] = 1; while (0);
 STACKTOP = i4;
 return HEAP32[i5 >> 2] | 0;
}

function _wcrtomb(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0;
 do if (i1) {
  if (i2 >>> 0 < 128) {
   HEAP8[i1 >> 0] = i2;
   i4 = 1;
   break;
  }
  if (i2 >>> 0 < 2048) {
   HEAP8[i1 >> 0] = i2 >>> 6 | 192;
   HEAP8[i1 + 1 >> 0] = i2 & 63 | 128;
   i4 = 2;
   break;
  }
  if (i2 >>> 0 < 55296 | (i2 & -8192 | 0) == 57344) {
   HEAP8[i1 >> 0] = i2 >>> 12 | 224;
   HEAP8[i1 + 1 >> 0] = i2 >>> 6 & 63 | 128;
   HEAP8[i1 + 2 >> 0] = i2 & 63 | 128;
   i4 = 3;
   break;
  }
  if ((i2 + -65536 | 0) >>> 0 < 1048576) {
   HEAP8[i1 >> 0] = i2 >>> 18 | 240;
   HEAP8[i1 + 1 >> 0] = i2 >>> 12 & 63 | 128;
   HEAP8[i1 + 2 >> 0] = i2 >>> 6 & 63 | 128;
   HEAP8[i1 + 3 >> 0] = i2 & 63 | 128;
   i4 = 4;
   break;
  } else {
   HEAP32[(___errno_location() | 0) >> 2] = 84;
   i4 = -1;
   break;
  }
 } else i4 = 1; while (0);
 return i4 | 0;
}

function _oggz_io_flush(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2 + 8 | 0;
 i4 = i2 + 4 | 0;
 i5 = i2;
 HEAP32[i4 >> 2] = i1;
 i1 = HEAP32[i4 >> 2] | 0;
 do if (HEAP32[(HEAP32[i4 >> 2] | 0) + 4 >> 2] | 0) if ((_fflush(HEAP32[i1 + 4 >> 2] | 0) | 0) == -1) HEAP32[i3 >> 2] = -10; else i6 = 10; else {
  i7 = HEAP32[i1 + 8 >> 2] | 0;
  HEAP32[i5 >> 2] = i7;
  if (!i7) {
   HEAP32[i3 >> 2] = -3;
   break;
  }
  if (!(HEAP32[(HEAP32[i5 >> 2] | 0) + 32 >> 2] | 0)) {
   HEAP32[i3 >> 2] = -3;
   break;
  }
  if ((FUNCTION_TABLE_ii[HEAP32[(HEAP32[i5 >> 2] | 0) + 32 >> 2] & 15](HEAP32[(HEAP32[i5 >> 2] | 0) + 36 >> 2] | 0) | 0) == -1) HEAP32[i3 >> 2] = -1; else i6 = 10;
 } while (0);
 if ((i6 | 0) == 10) HEAP32[i3 >> 2] = 0;
 STACKTOP = i2;
 return HEAP32[i3 >> 2] | 0;
}

function _strlen(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0;
 i2 = i1;
 L1 : do if (!(i2 & 3)) {
  i3 = i1;
  i4 = 4;
 } else {
  i5 = i1;
  i6 = i2;
  while (1) {
   if (!(HEAP8[i5 >> 0] | 0)) {
    i7 = i6;
    break L1;
   }
   i8 = i5 + 1 | 0;
   i6 = i8;
   if (!(i6 & 3)) {
    i3 = i8;
    i4 = 4;
    break;
   } else i5 = i8;
  }
 } while (0);
 if ((i4 | 0) == 4) {
  i4 = i3;
  while (1) {
   i3 = HEAP32[i4 >> 2] | 0;
   if (!((i3 & -2139062144 ^ -2139062144) & i3 + -16843009)) i4 = i4 + 4 | 0; else {
    i9 = i3;
    i10 = i4;
    break;
   }
  }
  if (!((i9 & 255) << 24 >> 24)) i11 = i10; else {
   i9 = i10;
   while (1) {
    i10 = i9 + 1 | 0;
    if (!(HEAP8[i10 >> 0] | 0)) {
     i11 = i10;
     break;
    } else i9 = i10;
   }
  }
  i7 = i11;
 }
 return i7 - i2 | 0;
}

function _oggz_stream_set_numheaders(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i5 = i4 + 16 | 0;
 i6 = i4 + 12 | 0;
 i7 = i4 + 8 | 0;
 i8 = i4 + 4 | 0;
 i9 = i4;
 HEAP32[i6 >> 2] = i1;
 HEAP32[i7 >> 2] = i2;
 HEAP32[i8 >> 2] = i3;
 if (!(HEAP32[i6 >> 2] | 0)) {
  HEAP32[i5 >> 2] = -2;
  i10 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i10 | 0;
 }
 HEAP32[i9 >> 2] = _oggz_get_stream(HEAP32[i6 >> 2] | 0, HEAP32[i7 >> 2] | 0) | 0;
 if (!(HEAP32[i9 >> 2] | 0)) {
  HEAP32[i5 >> 2] = -20;
  i10 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i10 | 0;
 } else {
  HEAP32[(HEAP32[i9 >> 2] | 0) + 364 >> 2] = HEAP32[i8 >> 2];
  HEAP32[i5 >> 2] = 0;
  i10 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i10 | 0;
 }
 return 0;
}

function _bq_headroom(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0;
 i2 = HEAP32[i1 + 4 >> 2] | 0;
 if (!i2) {
  i3 = i1 + 16 | 0;
  i4 = HEAP32[i3 >> 2] | 0;
  i5 = HEAP32[i3 + 4 >> 2] | 0;
  i6 = i4;
  i7 = i5;
  i8 = i4;
  i9 = i5;
  i10 = _i64Subtract(i6 | 0, i7 | 0, i8 | 0, i9 | 0) | 0;
  i11 = tempRet0;
  tempRet0 = i11;
  return i10 | 0;
 } else {
  i5 = i2 + -1 | 0;
  i2 = HEAP32[i1 >> 2] | 0;
  i4 = i2 + (i5 * 24 | 0) + 8 | 0;
  i3 = _i64Add(HEAP32[i2 + (i5 * 24 | 0) + 16 >> 2] | 0, 0, HEAP32[i4 >> 2] | 0, HEAP32[i4 + 4 >> 2] | 0) | 0;
  i4 = i1 + 16 | 0;
  i6 = i3;
  i7 = tempRet0;
  i8 = HEAP32[i4 >> 2] | 0;
  i9 = HEAP32[i4 + 4 >> 2] | 0;
  i10 = _i64Subtract(i6 | 0, i7 | 0, i8 | 0, i9 | 0) | 0;
  i11 = tempRet0;
  tempRet0 = i11;
  return i10 | 0;
 }
 return 0;
}

function _oggz_set_preroll(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i5 = i4 + 16 | 0;
 i6 = i4 + 12 | 0;
 i7 = i4 + 8 | 0;
 i8 = i4 + 4 | 0;
 i9 = i4;
 HEAP32[i6 >> 2] = i1;
 HEAP32[i7 >> 2] = i2;
 HEAP32[i8 >> 2] = i3;
 if (!(HEAP32[i6 >> 2] | 0)) {
  HEAP32[i5 >> 2] = -2;
  i10 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i10 | 0;
 }
 HEAP32[i9 >> 2] = _oggz_get_stream(HEAP32[i6 >> 2] | 0, HEAP32[i7 >> 2] | 0) | 0;
 if (!(HEAP32[i9 >> 2] | 0)) {
  HEAP32[i5 >> 2] = -20;
  i10 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i10 | 0;
 } else {
  HEAP32[(HEAP32[i9 >> 2] | 0) + 368 >> 2] = HEAP32[i8 >> 2];
  HEAP32[i5 >> 2] = 0;
  i10 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i10 | 0;
 }
 return 0;
}

function _oggz_read_init(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2 + 4 | 0;
 i4 = i2;
 HEAP32[i3 >> 2] = i1;
 HEAP32[i4 >> 2] = (HEAP32[i3 >> 2] | 0) + 112;
 _ogg_sync_init(HEAP32[i4 >> 2] | 0) | 0;
 _ogg_stream_init((HEAP32[i4 >> 2] | 0) + 32 | 0, -1) | 0;
 HEAP32[(HEAP32[i4 >> 2] | 0) + 392 >> 2] = -1;
 HEAP32[(HEAP32[i4 >> 2] | 0) + 396 >> 2] = 0;
 HEAP32[(HEAP32[i4 >> 2] | 0) + 400 >> 2] = 0;
 HEAP32[(HEAP32[i4 >> 2] | 0) + 404 >> 2] = 0;
 HEAP32[(HEAP32[i4 >> 2] | 0) + 408 >> 2] = 0;
 i1 = (HEAP32[i4 >> 2] | 0) + 416 | 0;
 HEAP32[i1 >> 2] = 0;
 HEAP32[i1 + 4 >> 2] = 0;
 HEAP32[(HEAP32[i4 >> 2] | 0) + 432 >> 2] = 0;
 HEAP32[(HEAP32[i4 >> 2] | 0) + 436 >> 2] = 0;
 HEAP32[(HEAP32[i4 >> 2] | 0) + 440 >> 2] = 0;
 STACKTOP = i2;
 return HEAP32[i3 >> 2] | 0;
}

function _frexp(d1, i2) {
 d1 = +d1;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, d7 = 0.0, d8 = 0.0, i9 = 0, d10 = 0.0;
 HEAPF64[tempDoublePtr >> 3] = d1;
 i3 = HEAP32[tempDoublePtr >> 2] | 0;
 i4 = HEAP32[tempDoublePtr + 4 >> 2] | 0;
 i5 = _bitshift64Lshr(i3 | 0, i4 | 0, 52) | 0;
 i6 = i5 & 2047;
 switch (i6 | 0) {
 case 0:
  {
   if (d1 != 0.0) {
    d7 = +_frexp(d1 * 18446744073709551616.0, i2);
    d8 = d7;
    i9 = (HEAP32[i2 >> 2] | 0) + -64 | 0;
   } else {
    d8 = d1;
    i9 = 0;
   }
   HEAP32[i2 >> 2] = i9;
   d10 = d8;
   break;
  }
 case 2047:
  {
   d10 = d1;
   break;
  }
 default:
  {
   HEAP32[i2 >> 2] = i6 + -1022;
   HEAP32[tempDoublePtr >> 2] = i3;
   HEAP32[tempDoublePtr + 4 >> 2] = i4 & -2146435073 | 1071644672;
   d10 = +HEAPF64[tempDoublePtr >> 3];
  }
 }
 return +d10;
}

function ___remdi3(i1, i2, i3, i4) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 var i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0;
 i5 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i6 = i5 | 0;
 i7 = i2 >> 31 | ((i2 | 0) < 0 ? -1 : 0) << 1;
 i8 = ((i2 | 0) < 0 ? -1 : 0) >> 31 | ((i2 | 0) < 0 ? -1 : 0) << 1;
 i9 = i4 >> 31 | ((i4 | 0) < 0 ? -1 : 0) << 1;
 i10 = ((i4 | 0) < 0 ? -1 : 0) >> 31 | ((i4 | 0) < 0 ? -1 : 0) << 1;
 i11 = _i64Subtract(i7 ^ i1 | 0, i8 ^ i2 | 0, i7 | 0, i8 | 0) | 0;
 i2 = tempRet0;
 ___udivmoddi4(i11, i2, _i64Subtract(i9 ^ i3 | 0, i10 ^ i4 | 0, i9 | 0, i10 | 0) | 0, tempRet0, i6) | 0;
 i10 = _i64Subtract(HEAP32[i6 >> 2] ^ i7 | 0, HEAP32[i6 + 4 >> 2] ^ i8 | 0, i7 | 0, i8 | 0) | 0;
 i8 = tempRet0;
 STACKTOP = i5;
 return (tempRet0 = i8, i10) | 0;
}

function _seekCallback(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i5 = i4;
 switch (i3 | 0) {
 case 0:
  {
   i6 = i2;
   i7 = ((i2 | 0) < 0) << 31 >> 31;
   break;
  }
 case 1:
  {
   i6 = _i64Add(_bq_tell(i1) | 0, tempRet0 | 0, i2 | 0, ((i2 | 0) < 0) << 31 >> 31 | 0) | 0;
   i7 = tempRet0;
   break;
  }
 default:
  {
   i8 = -1;
   STACKTOP = i4;
   return i8 | 0;
  }
 }
 if (!(_bq_seek(i1, i6, i7) | 0)) {
  i8 = i6;
  STACKTOP = i4;
  return i8 | 0;
 } else {
  i1 = i5;
  HEAP32[i1 >> 2] = i6;
  HEAP32[i1 + 4 >> 2] = i7;
  HEAP32[i5 + 8 >> 2] = i2;
  HEAP32[i5 + 12 >> 2] = i3;
  _printf(2673, i5) | 0;
  i8 = -1;
  STACKTOP = i4;
  return i8 | 0;
 }
 return 0;
}

function _ogg_stream_init(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0;
 if (!i1) {
  i3 = -1;
  return i3 | 0;
 }
 _memset(i1 | 0, 0, 360) | 0;
 HEAP32[i1 + 4 >> 2] = 16384;
 HEAP32[i1 + 24 >> 2] = 1024;
 i4 = _malloc(16384) | 0;
 HEAP32[i1 >> 2] = i4;
 i5 = _malloc(4096) | 0;
 i6 = i1 + 16 | 0;
 HEAP32[i6 >> 2] = i5;
 i7 = _malloc(8192) | 0;
 i8 = i1 + 20 | 0;
 HEAP32[i8 >> 2] = i7;
 do if (!i4) i9 = i5; else {
  if ((i7 | 0) == 0 | (i5 | 0) == 0) {
   _free(i4);
   i9 = HEAP32[i6 >> 2] | 0;
   break;
  }
  HEAP32[i1 + 336 >> 2] = i2;
  i3 = 0;
  return i3 | 0;
 } while (0);
 if (i9 | 0) _free(i9);
 i9 = HEAP32[i8 >> 2] | 0;
 if (i9 | 0) _free(i9);
 _memset(i1 | 0, 0, 360) | 0;
 i3 = -1;
 return i3 | 0;
}

function _oggz_tell_units(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2;
 i4 = i2 + 12 | 0;
 i5 = i2 + 8 | 0;
 HEAP32[i4 >> 2] = i1;
 do if (HEAP32[i4 >> 2] | 0) if (HEAP32[HEAP32[i4 >> 2] >> 2] & 1 | 0) {
  i1 = i3;
  HEAP32[i1 >> 2] = -3;
  HEAP32[i1 + 4 >> 2] = -1;
  break;
 } else {
  HEAP32[i5 >> 2] = (HEAP32[i4 >> 2] | 0) + 112;
  i1 = (HEAP32[i5 >> 2] | 0) + 416 | 0;
  i6 = HEAP32[i1 + 4 >> 2] | 0;
  i7 = i3;
  HEAP32[i7 >> 2] = HEAP32[i1 >> 2];
  HEAP32[i7 + 4 >> 2] = i6;
  break;
 } else {
  i6 = i3;
  HEAP32[i6 >> 2] = -2;
  HEAP32[i6 + 4 >> 2] = -1;
 } while (0);
 i5 = i3;
 tempRet0 = HEAP32[i5 + 4 >> 2] | 0;
 STACKTOP = i2;
 return HEAP32[i5 >> 2] | 0;
}

function _extract_uint32(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i4 = i3 + 8 | 0;
 i5 = i3 + 4 | 0;
 i6 = i3;
 HEAP32[i5 >> 2] = i1;
 HEAP32[i6 >> 2] = i2;
 if ((HEAP32[i5 >> 2] | 0) == 0 | (HEAP32[i6 >> 2] | 0) == 0) {
  HEAP32[i4 >> 2] = 0;
  i7 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i7 | 0;
 } else {
  HEAP32[HEAP32[i6 >> 2] >> 2] = HEAPU8[HEAP32[i5 >> 2] >> 0] | 0 | (HEAPU8[(HEAP32[i5 >> 2] | 0) + 1 >> 0] | 0) << 8 | (HEAPU8[(HEAP32[i5 >> 2] | 0) + 2 >> 0] | 0) << 16 | (HEAPU8[(HEAP32[i5 >> 2] | 0) + 3 >> 0] | 0) << 24;
  HEAP32[i4 >> 2] = (HEAP32[i5 >> 2] | 0) + 4;
  i7 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i7 | 0;
 }
 return 0;
}

function _extract_int32(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i4 = i3 + 8 | 0;
 i5 = i3 + 4 | 0;
 i6 = i3;
 HEAP32[i5 >> 2] = i1;
 HEAP32[i6 >> 2] = i2;
 if ((HEAP32[i5 >> 2] | 0) == 0 | (HEAP32[i6 >> 2] | 0) == 0) {
  HEAP32[i4 >> 2] = 0;
  i7 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i7 | 0;
 } else {
  HEAP32[HEAP32[i6 >> 2] >> 2] = HEAPU8[HEAP32[i5 >> 2] >> 0] | 0 | (HEAPU8[(HEAP32[i5 >> 2] | 0) + 1 >> 0] | 0) << 8 | (HEAPU8[(HEAP32[i5 >> 2] | 0) + 2 >> 0] | 0) << 16 | (HEAPU8[(HEAP32[i5 >> 2] | 0) + 3 >> 0] | 0) << 24;
  HEAP32[i4 >> 2] = (HEAP32[i5 >> 2] | 0) + 4;
  i7 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i7 | 0;
 }
 return 0;
}

function ___fseeko_unlocked(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0;
 if ((i3 | 0) == 1) i4 = i2 - (HEAP32[i1 + 8 >> 2] | 0) + (HEAP32[i1 + 4 >> 2] | 0) | 0; else i4 = i2;
 i2 = i1 + 20 | 0;
 i5 = i1 + 28 | 0;
 if ((HEAP32[i2 >> 2] | 0) >>> 0 > (HEAP32[i5 >> 2] | 0) >>> 0 ? (FUNCTION_TABLE_iiii[HEAP32[i1 + 36 >> 2] & 7](i1, 0, 0) | 0, (HEAP32[i2 >> 2] | 0) == 0) : 0) i6 = -1; else {
  HEAP32[i1 + 16 >> 2] = 0;
  HEAP32[i5 >> 2] = 0;
  HEAP32[i2 >> 2] = 0;
  if ((FUNCTION_TABLE_iiii[HEAP32[i1 + 40 >> 2] & 7](i1, i4, i3) | 0) < 0) i6 = -1; else {
   HEAP32[i1 + 8 >> 2] = 0;
   HEAP32[i1 + 4 >> 2] = 0;
   HEAP32[i1 >> 2] = HEAP32[i1 >> 2] & -17;
   i6 = 0;
  }
 }
 return i6 | 0;
}

function _oggz_stream_set_content(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i5 = i4 + 16 | 0;
 i6 = i4 + 12 | 0;
 i7 = i4 + 8 | 0;
 i8 = i4 + 4 | 0;
 i9 = i4;
 HEAP32[i6 >> 2] = i1;
 HEAP32[i7 >> 2] = i2;
 HEAP32[i8 >> 2] = i3;
 HEAP32[i9 >> 2] = _oggz_get_stream(HEAP32[i6 >> 2] | 0, HEAP32[i7 >> 2] | 0) | 0;
 if (!(HEAP32[i9 >> 2] | 0)) {
  HEAP32[i5 >> 2] = -20;
  i10 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i10 | 0;
 } else {
  HEAP32[(HEAP32[i9 >> 2] | 0) + 360 >> 2] = HEAP32[i8 >> 2];
  HEAP32[i5 >> 2] = 0;
  i10 = HEAP32[i5 >> 2] | 0;
  STACKTOP = i4;
  return i10 | 0;
 }
 return 0;
}

function _oggz_stream_clear(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0, i5 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2 + 4 | 0;
 i4 = i2;
 HEAP32[i3 >> 2] = i1;
 HEAP32[i4 >> 2] = HEAP32[i3 >> 2];
 _oggz_comments_free(HEAP32[i4 >> 2] | 0) | 0;
 if ((HEAP32[(HEAP32[i4 >> 2] | 0) + 336 >> 2] | 0) != -1) _ogg_stream_clear(HEAP32[i4 >> 2] | 0) | 0;
 if (HEAP32[(HEAP32[i4 >> 2] | 0) + 456 >> 2] | 0) _free(HEAP32[(HEAP32[i4 >> 2] | 0) + 452 >> 2] | 0);
 if (!(HEAP32[(HEAP32[i4 >> 2] | 0) + 504 >> 2] | 0)) {
  i5 = HEAP32[i4 >> 2] | 0;
  _free(i5);
  STACKTOP = i2;
  return 0;
 }
 _free(HEAP32[(HEAP32[i4 >> 2] | 0) + 504 >> 2] | 0);
 i5 = HEAP32[i4 >> 2] | 0;
 _free(i5);
 STACKTOP = i2;
 return 0;
}

function _ogv_demuxer_keypoint_offset(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2;
 i4 = i2 + 8 | 0;
 i5 = i3;
 HEAP32[i5 >> 2] = -1;
 HEAP32[i5 + 4 >> 2] = -1;
 if (!(HEAP32[1330] | 0)) {
  i6 = -1;
  STACKTOP = i2;
  return i6 | 0;
 }
 i5 = HEAP32[1331] | 0;
 if (!i5) {
  i7 = HEAP32[1332] | 0;
  if (!i7) {
   i8 = i4;
   i9 = 0;
  } else {
   HEAP32[i4 >> 2] = i7;
   i8 = i4;
   i9 = 1;
  }
 } else {
  HEAP32[i4 >> 2] = i5;
  i8 = i4;
  i9 = 1;
 }
 _oggskel_get_keypoint_offset(HEAP32[1329] | 0, i8, i9, i1, ((i1 | 0) < 0) << 31 >> 31, i3) | 0;
 i6 = HEAP32[i3 >> 2] | 0;
 STACKTOP = i2;
 return i6 | 0;
}

function _oggz_stream_has_metric(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i4 = i3 + 12 | 0;
 i5 = i3 + 8 | 0;
 i6 = i3 + 4 | 0;
 i7 = i3;
 HEAP32[i5 >> 2] = i1;
 HEAP32[i6 >> 2] = i2;
 do if (!(HEAP32[(HEAP32[i5 >> 2] | 0) + 88 >> 2] | 0)) {
  HEAP32[i7 >> 2] = _oggz_get_stream(HEAP32[i5 >> 2] | 0, HEAP32[i6 >> 2] | 0) | 0;
  if (!(HEAP32[i7 >> 2] | 0)) {
   HEAP32[i4 >> 2] = -20;
   break;
  }
  if (HEAP32[(HEAP32[i7 >> 2] | 0) + 448 >> 2] | 0) {
   HEAP32[i4 >> 2] = 1;
   break;
  } else {
   HEAP32[i4 >> 2] = 0;
   break;
  }
 } else HEAP32[i4 >> 2] = 1; while (0);
 STACKTOP = i3;
 return HEAP32[i4 >> 2] | 0;
}

function _oggz_vector_insert_p(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i4 = i3 + 8 | 0;
 i5 = i3 + 4 | 0;
 i6 = i3;
 HEAP32[i5 >> 2] = i1;
 HEAP32[i6 >> 2] = i2;
 if (!(_oggz_vector_grow(HEAP32[i5 >> 2] | 0) | 0)) {
  HEAP32[i4 >> 2] = 0;
  i7 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i7 | 0;
 } else {
  HEAP32[(HEAP32[(HEAP32[i5 >> 2] | 0) + 8 >> 2] | 0) + ((HEAP32[(HEAP32[i5 >> 2] | 0) + 4 >> 2] | 0) - 1 << 2) >> 2] = HEAP32[i6 >> 2];
  _oggz_vector_tail_insertion_sort(HEAP32[i5 >> 2] | 0);
  HEAP32[i4 >> 2] = HEAP32[i6 >> 2];
  i7 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i7 | 0;
 }
 return 0;
}

function _int64_le_at(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0, i5 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2 + 8 | 0;
 i4 = i2 + 4 | 0;
 i5 = i2;
 HEAP32[i3 >> 2] = i1;
 HEAP32[i4 >> 2] = HEAPU8[HEAP32[i3 >> 2] >> 0] | 0 | (HEAPU8[(HEAP32[i3 >> 2] | 0) + 1 >> 0] | 0) << 8 | (HEAPU8[(HEAP32[i3 >> 2] | 0) + 2 >> 0] | 0) << 16 | (HEAPU8[(HEAP32[i3 >> 2] | 0) + 3 >> 0] | 0) << 24;
 HEAP32[i5 >> 2] = HEAPU8[(HEAP32[i3 >> 2] | 0) + 4 >> 0] | 0 | (HEAPU8[(HEAP32[i3 >> 2] | 0) + 5 >> 0] | 0) << 8 | (HEAPU8[(HEAP32[i3 >> 2] | 0) + 6 >> 0] | 0) << 16 | (HEAPU8[(HEAP32[i3 >> 2] | 0) + 7 >> 0] | 0) << 24;
 tempRet0 = HEAP32[i5 >> 2] | 0;
 STACKTOP = i2;
 return HEAP32[i4 >> 2] | 0;
}

function _oggskel_get_ver_min(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i4 = i3 + 12 | 0;
 i5 = i3 + 8 | 0;
 i6 = i3 + 4 | 0;
 i7 = i3;
 HEAP32[i5 >> 2] = i1;
 HEAP32[i6 >> 2] = i2;
 HEAP32[i7 >> 2] = -1;
 i2 = _getter_error_check(HEAP32[i5 >> 2] | 0, HEAP32[i6 >> 2] | 0) | 0;
 HEAP32[i7 >> 2] = i2;
 if ((i2 | 0) < 0) {
  HEAP32[i4 >> 2] = HEAP32[i7 >> 2];
  i8 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i8 | 0;
 } else {
  HEAP16[HEAP32[i6 >> 2] >> 1] = HEAP16[(HEAP32[i5 >> 2] | 0) + 2 >> 1] | 0;
  HEAP32[i4 >> 2] = 0;
  i8 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i8 | 0;
 }
 return 0;
}

function _oggskel_get_ver_maj(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i4 = i3 + 12 | 0;
 i5 = i3 + 8 | 0;
 i6 = i3 + 4 | 0;
 i7 = i3;
 HEAP32[i5 >> 2] = i1;
 HEAP32[i6 >> 2] = i2;
 HEAP32[i7 >> 2] = -1;
 i2 = _getter_error_check(HEAP32[i5 >> 2] | 0, HEAP32[i6 >> 2] | 0) | 0;
 HEAP32[i7 >> 2] = i2;
 if ((i2 | 0) < 0) {
  HEAP32[i4 >> 2] = HEAP32[i7 >> 2];
  i8 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i8 | 0;
 } else {
  HEAP16[HEAP32[i6 >> 2] >> 1] = HEAP16[HEAP32[i5 >> 2] >> 1] | 0;
  HEAP32[i4 >> 2] = 0;
  i8 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i8 | 0;
 }
 return 0;
}

function _oggz_tell_granulepos(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0, i5 = 0, i6 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2;
 i4 = i2 + 8 | 0;
 HEAP32[i4 >> 2] = i1;
 do if (HEAP32[i4 >> 2] | 0) if (HEAP32[HEAP32[i4 >> 2] >> 2] & 1 | 0) {
  i1 = i3;
  HEAP32[i1 >> 2] = -3;
  HEAP32[i1 + 4 >> 2] = -1;
  break;
 } else {
  i1 = (HEAP32[i4 >> 2] | 0) + 112 + 424 | 0;
  i5 = HEAP32[i1 + 4 >> 2] | 0;
  i6 = i3;
  HEAP32[i6 >> 2] = HEAP32[i1 >> 2];
  HEAP32[i6 + 4 >> 2] = i5;
  break;
 } else {
  i5 = i3;
  HEAP32[i5 >> 2] = -2;
  HEAP32[i5 + 4 >> 2] = -1;
 } while (0);
 i4 = i3;
 tempRet0 = HEAP32[i4 + 4 >> 2] | 0;
 STACKTOP = i2;
 return HEAP32[i4 >> 2] | 0;
}

function _oggskel_vect_get_index(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i4 = i3 + 12 | 0;
 i5 = i3 + 8 | 0;
 i6 = i3 + 4 | 0;
 i7 = i3;
 HEAP32[i5 >> 2] = i1;
 HEAP32[i6 >> 2] = i2;
 HEAP32[i7 >> 2] = 0;
 if (!(HEAP32[i5 >> 2] | 0)) {
  HEAP32[i4 >> 2] = 0;
  i8 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i8 | 0;
 }
 HEAP32[i7 >> 2] = _find_track_info(HEAP32[i5 >> 2] | 0, HEAP32[i6 >> 2] | 0) | 0;
 if (!(HEAP32[i7 >> 2] | 0)) i9 = 0; else i9 = HEAP32[(HEAP32[i7 >> 2] | 0) + 8 >> 2] | 0;
 HEAP32[i4 >> 2] = i9;
 i8 = HEAP32[i4 >> 2] | 0;
 STACKTOP = i3;
 return i8 | 0;
}

function _oggz_dlist_reverse_iter(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i4 = i3 + 8 | 0;
 i5 = i3 + 4 | 0;
 i6 = i3;
 HEAP32[i4 >> 2] = i1;
 HEAP32[i5 >> 2] = i2;
 HEAP32[i6 >> 2] = HEAP32[(HEAP32[(HEAP32[i4 >> 2] | 0) + 4 >> 2] | 0) + 4 >> 2];
 while (1) {
  if ((HEAP32[i6 >> 2] | 0) == (HEAP32[HEAP32[i4 >> 2] >> 2] | 0)) {
   i7 = 5;
   break;
  }
  if (!(FUNCTION_TABLE_ii[HEAP32[i5 >> 2] & 15](HEAP32[(HEAP32[i6 >> 2] | 0) + 8 >> 2] | 0) | 0)) {
   i7 = 5;
   break;
  }
  HEAP32[i6 >> 2] = HEAP32[(HEAP32[i6 >> 2] | 0) + 4 >> 2];
 }
 if ((i7 | 0) == 5) {
  STACKTOP = i3;
  return;
 }
}

function _ogg_page_granulepos(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0;
 i2 = HEAP32[i1 >> 2] | 0;
 i1 = _bitshift64Shl(HEAPU8[i2 + 13 >> 0] | 0 | 0, 0, 8) | 0;
 i3 = _bitshift64Shl(i1 | (HEAPU8[i2 + 12 >> 0] | 0) | 0, tempRet0 | 0, 8) | 0;
 i1 = _bitshift64Shl(i3 | (HEAPU8[i2 + 11 >> 0] | 0) | 0, tempRet0 | 0, 8) | 0;
 i3 = _bitshift64Shl(i1 | (HEAPU8[i2 + 10 >> 0] | 0) | 0, tempRet0 | 0, 8) | 0;
 i1 = _bitshift64Shl(i3 | (HEAPU8[i2 + 9 >> 0] | 0) | 0, tempRet0 | 0, 8) | 0;
 i3 = _bitshift64Shl(i1 | (HEAPU8[i2 + 8 >> 0] | 0) | 0, tempRet0 | 0, 8) | 0;
 i1 = _bitshift64Shl(i3 | (HEAPU8[i2 + 7 >> 0] | 0) | 0, tempRet0 | 0, 8) | 0;
 return i1 | (HEAPU8[i2 + 6 >> 0] | 0) | 0;
}

function _ogv_demuxer_process() {
 var i1 = 0, i2 = 0, i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0;
 i1 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i2 = i1;
 while (1) {
  i3 = _bq_headroom(HEAP32[1334] | 0) | 0;
  i4 = tempRet0;
  i5 = (i4 | 0) < 0 | (i4 | 0) == 0 & i3 >>> 0 < 65536;
  i4 = i5 ? i3 : 65536;
  i3 = _oggz_read(HEAP32[1328] | 0, i4) | 0;
  if ((i3 | 0) == -14) {
   i6 = 1;
   i7 = 6;
   break;
  }
  if ((i3 | 0) <= 0) {
   i8 = i3;
   break;
  }
 }
 if ((i7 | 0) == 6) {
  STACKTOP = i1;
  return i6 | 0;
 }
 if (!i8) {
  i6 = 0;
  STACKTOP = i1;
  return i6 | 0;
 }
 HEAP32[i2 >> 2] = i8;
 _printf(2724, i2) | 0;
 i6 = 0;
 STACKTOP = i1;
 return i6 | 0;
}

function _strerror(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0;
 i2 = 0;
 while (1) {
  if ((HEAPU8[3361 + i2 >> 0] | 0) == (i1 | 0)) {
   i3 = i2;
   i4 = 2;
   break;
  }
  i2 = i2 + 1 | 0;
  if ((i2 | 0) == 87) {
   i5 = 87;
   i6 = 3449;
   i4 = 5;
   break;
  }
 }
 if ((i4 | 0) == 2) if (!i3) i7 = 3449; else {
  i5 = i3;
  i6 = 3449;
  i4 = 5;
 }
 if ((i4 | 0) == 5) while (1) {
  i4 = 0;
  i3 = i6;
  while (1) {
   i2 = i3 + 1 | 0;
   if (!(HEAP8[i3 >> 0] | 0)) {
    i8 = i2;
    break;
   } else i3 = i2;
  }
  i5 = i5 + -1 | 0;
  if (!i5) {
   i7 = i8;
   break;
  } else {
   i6 = i8;
   i4 = 5;
  }
 }
 return i7 | 0;
}

function _oggz_comments_free(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0, i5 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2;
 HEAP32[i3 >> 2] = i1;
 _oggz_vector_foreach(HEAP32[(HEAP32[i3 >> 2] | 0) + 416 >> 2] | 0, 1) | 0;
 _oggz_vector_delete(HEAP32[(HEAP32[i3 >> 2] | 0) + 416 >> 2] | 0);
 HEAP32[(HEAP32[i3 >> 2] | 0) + 416 >> 2] = 0;
 if (!(HEAP32[(HEAP32[i3 >> 2] | 0) + 412 >> 2] | 0)) {
  i4 = HEAP32[i3 >> 2] | 0;
  i5 = i4 + 412 | 0;
  HEAP32[i5 >> 2] = 0;
  STACKTOP = i2;
  return 0;
 }
 _free(HEAP32[(HEAP32[i3 >> 2] | 0) + 412 >> 2] | 0);
 i4 = HEAP32[i3 >> 2] | 0;
 i5 = i4 + 412 | 0;
 HEAP32[i5 >> 2] = 0;
 STACKTOP = i2;
 return 0;
}

function ___fflush_unlocked(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0;
 i2 = i1 + 20 | 0;
 i3 = i1 + 28 | 0;
 if ((HEAP32[i2 >> 2] | 0) >>> 0 > (HEAP32[i3 >> 2] | 0) >>> 0 ? (FUNCTION_TABLE_iiii[HEAP32[i1 + 36 >> 2] & 7](i1, 0, 0) | 0, (HEAP32[i2 >> 2] | 0) == 0) : 0) i4 = -1; else {
  i5 = i1 + 4 | 0;
  i6 = HEAP32[i5 >> 2] | 0;
  i7 = i1 + 8 | 0;
  i8 = HEAP32[i7 >> 2] | 0;
  if (i6 >>> 0 < i8 >>> 0) FUNCTION_TABLE_iiii[HEAP32[i1 + 40 >> 2] & 7](i1, i6 - i8 | 0, 1) | 0;
  HEAP32[i1 + 16 >> 2] = 0;
  HEAP32[i3 >> 2] = 0;
  HEAP32[i2 >> 2] = 0;
  HEAP32[i7 >> 2] = 0;
  HEAP32[i5 >> 2] = 0;
  i4 = 0;
 }
 return i4 | 0;
}

function ___divdi3(i1, i2, i3, i4) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 var i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0;
 i5 = i2 >> 31 | ((i2 | 0) < 0 ? -1 : 0) << 1;
 i6 = ((i2 | 0) < 0 ? -1 : 0) >> 31 | ((i2 | 0) < 0 ? -1 : 0) << 1;
 i7 = i4 >> 31 | ((i4 | 0) < 0 ? -1 : 0) << 1;
 i8 = ((i4 | 0) < 0 ? -1 : 0) >> 31 | ((i4 | 0) < 0 ? -1 : 0) << 1;
 i9 = _i64Subtract(i5 ^ i1 | 0, i6 ^ i2 | 0, i5 | 0, i6 | 0) | 0;
 i2 = tempRet0;
 i1 = i7 ^ i5;
 i5 = i8 ^ i6;
 return _i64Subtract((___udivmoddi4(i9, i2, _i64Subtract(i7 ^ i3 | 0, i8 ^ i4 | 0, i7 | 0, i8 | 0) | 0, tempRet0, 0) | 0) ^ i1 | 0, tempRet0 ^ i5 | 0, i1 | 0, i5 | 0) | 0;
}

function _oggz_io_init(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0, i5 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2 + 4 | 0;
 i4 = i2;
 HEAP32[i4 >> 2] = i1;
 i1 = _malloc(40) | 0;
 HEAP32[(HEAP32[i4 >> 2] | 0) + 8 >> 2] = i1;
 if (!(HEAP32[(HEAP32[i4 >> 2] | 0) + 8 >> 2] | 0)) {
  HEAP32[i3 >> 2] = -1;
  i5 = HEAP32[i3 >> 2] | 0;
  STACKTOP = i2;
  return i5 | 0;
 } else {
  i1 = HEAP32[(HEAP32[i4 >> 2] | 0) + 8 >> 2] | 0;
  i4 = i1 + 40 | 0;
  do {
   HEAP32[i1 >> 2] = 0;
   i1 = i1 + 4 | 0;
  } while ((i1 | 0) < (i4 | 0));
  HEAP32[i3 >> 2] = 0;
  i5 = HEAP32[i3 >> 2] | 0;
  STACKTOP = i2;
  return i5 | 0;
 }
 return 0;
}

function _extract_uint16(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i4 = i3 + 8 | 0;
 i5 = i3 + 4 | 0;
 i6 = i3;
 HEAP32[i5 >> 2] = i1;
 HEAP32[i6 >> 2] = i2;
 if ((HEAP32[i5 >> 2] | 0) == 0 | (HEAP32[i6 >> 2] | 0) == 0) {
  HEAP32[i4 >> 2] = 0;
  i7 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i7 | 0;
 } else {
  HEAP16[HEAP32[i6 >> 2] >> 1] = HEAPU8[HEAP32[i5 >> 2] >> 0] | 0 | (HEAPU8[(HEAP32[i5 >> 2] | 0) + 1 >> 0] | 0) << 8;
  HEAP32[i4 >> 2] = (HEAP32[i5 >> 2] | 0) + 2;
  i7 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i7 | 0;
 }
 return 0;
}

function _realloc(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0;
 if (!i1) {
  i3 = _malloc(i2) | 0;
  return i3 | 0;
 }
 if (i2 >>> 0 > 4294967231) {
  HEAP32[(___errno_location() | 0) >> 2] = 12;
  i3 = 0;
  return i3 | 0;
 }
 i4 = _try_realloc_chunk(i1 + -8 | 0, i2 >>> 0 < 11 ? 16 : i2 + 11 & -8) | 0;
 if (i4 | 0) {
  i3 = i4 + 8 | 0;
  return i3 | 0;
 }
 i4 = _malloc(i2) | 0;
 if (!i4) {
  i3 = 0;
  return i3 | 0;
 }
 i5 = HEAP32[i1 + -4 >> 2] | 0;
 i6 = (i5 & -8) - ((i5 & 3 | 0) == 0 ? 8 : 4) | 0;
 _memcpy(i4 | 0, i1 | 0, (i6 >>> 0 < i2 >>> 0 ? i6 : i2) | 0) | 0;
 _free(i1);
 i3 = i4;
 return i3 | 0;
}

function _oggz_comments_init(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0, i5 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2 + 4 | 0;
 i4 = i2;
 HEAP32[i4 >> 2] = i1;
 HEAP32[(HEAP32[i4 >> 2] | 0) + 412 >> 2] = 0;
 i1 = _oggz_vector_new() | 0;
 HEAP32[(HEAP32[i4 >> 2] | 0) + 416 >> 2] = i1;
 if (!(HEAP32[(HEAP32[i4 >> 2] | 0) + 416 >> 2] | 0)) {
  HEAP32[i3 >> 2] = -1;
  i5 = HEAP32[i3 >> 2] | 0;
  STACKTOP = i2;
  return i5 | 0;
 } else {
  _oggz_vector_set_cmp(HEAP32[(HEAP32[i4 >> 2] | 0) + 416 >> 2] | 0, 3, 0) | 0;
  HEAP32[i3 >> 2] = 0;
  i5 = HEAP32[i3 >> 2] | 0;
  STACKTOP = i2;
  return i5 | 0;
 }
 return 0;
}

function _oggz_vector_new() {
 var i1 = 0, i2 = 0, i3 = 0, i4 = 0;
 i1 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i2 = i1 + 4 | 0;
 i3 = i1;
 HEAP32[i3 >> 2] = _malloc(20) | 0;
 if (!(HEAP32[i3 >> 2] | 0)) {
  HEAP32[i2 >> 2] = 0;
  i4 = HEAP32[i2 >> 2] | 0;
  STACKTOP = i1;
  return i4 | 0;
 } else {
  HEAP32[HEAP32[i3 >> 2] >> 2] = 0;
  HEAP32[(HEAP32[i3 >> 2] | 0) + 4 >> 2] = 0;
  HEAP32[(HEAP32[i3 >> 2] | 0) + 8 >> 2] = 0;
  HEAP32[(HEAP32[i3 >> 2] | 0) + 12 >> 2] = 0;
  HEAP32[(HEAP32[i3 >> 2] | 0) + 16 >> 2] = 0;
  HEAP32[i2 >> 2] = HEAP32[i3 >> 2];
  i4 = HEAP32[i2 >> 2] | 0;
  STACKTOP = i1;
  return i4 | 0;
 }
 return 0;
}

function _oggz_purge(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2 + 4 | 0;
 i4 = i2;
 HEAP32[i4 >> 2] = i1;
 do if (!(HEAP32[i4 >> 2] | 0)) HEAP32[i3 >> 2] = -2; else {
  if (HEAP32[HEAP32[i4 >> 2] >> 2] & 1 | 0) {
   HEAP32[i3 >> 2] = -3;
   break;
  }
  _oggz_reset_streams(HEAP32[i4 >> 2] | 0);
  if (HEAP32[(HEAP32[i4 >> 2] | 0) + 4 >> 2] | 0 ? (_oggz_reset(HEAP32[i4 >> 2] | 0, HEAP32[(HEAP32[i4 >> 2] | 0) + 64 >> 2] | 0, -1, -1, 0) | 0) < 0 : 0) {
   HEAP32[i3 >> 2] = -10;
   break;
  }
  HEAP32[i3 >> 2] = 0;
 } while (0);
 STACKTOP = i2;
 return HEAP32[i3 >> 2] | 0;
}

function _oggz_stream_get_numheaders(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i4 = i3 + 12 | 0;
 i5 = i3 + 8 | 0;
 i6 = i3 + 4 | 0;
 i7 = i3;
 HEAP32[i5 >> 2] = i1;
 HEAP32[i6 >> 2] = i2;
 do if (HEAP32[i5 >> 2] | 0) {
  HEAP32[i7 >> 2] = _oggz_get_stream(HEAP32[i5 >> 2] | 0, HEAP32[i6 >> 2] | 0) | 0;
  if (!(HEAP32[i7 >> 2] | 0)) {
   HEAP32[i4 >> 2] = -20;
   break;
  } else {
   HEAP32[i4 >> 2] = HEAP32[(HEAP32[i7 >> 2] | 0) + 364 >> 2];
   break;
  }
 } else HEAP32[i4 >> 2] = -2; while (0);
 STACKTOP = i3;
 return HEAP32[i4 >> 2] | 0;
}

function _oggz_stream_get_content(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i4 = i3 + 12 | 0;
 i5 = i3 + 8 | 0;
 i6 = i3 + 4 | 0;
 i7 = i3;
 HEAP32[i5 >> 2] = i1;
 HEAP32[i6 >> 2] = i2;
 do if (HEAP32[i5 >> 2] | 0) {
  HEAP32[i7 >> 2] = _oggz_get_stream(HEAP32[i5 >> 2] | 0, HEAP32[i6 >> 2] | 0) | 0;
  if (!(HEAP32[i7 >> 2] | 0)) {
   HEAP32[i4 >> 2] = -20;
   break;
  } else {
   HEAP32[i4 >> 2] = HEAP32[(HEAP32[i7 >> 2] | 0) + 360 >> 2];
   break;
  }
 } else HEAP32[i4 >> 2] = -2; while (0);
 STACKTOP = i3;
 return HEAP32[i4 >> 2] | 0;
}

function _memcpy(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0;
 if ((i3 | 0) >= 4096) return _emscripten_memcpy_big(i1 | 0, i2 | 0, i3 | 0) | 0;
 i4 = i1 | 0;
 if ((i1 & 3) == (i2 & 3)) {
  while (i1 & 3) {
   if (!i3) return i4 | 0;
   HEAP8[i1 >> 0] = HEAP8[i2 >> 0] | 0;
   i1 = i1 + 1 | 0;
   i2 = i2 + 1 | 0;
   i3 = i3 - 1 | 0;
  }
  while ((i3 | 0) >= 4) {
   HEAP32[i1 >> 2] = HEAP32[i2 >> 2];
   i1 = i1 + 4 | 0;
   i2 = i2 + 4 | 0;
   i3 = i3 - 4 | 0;
  }
 }
 while ((i3 | 0) > 0) {
  HEAP8[i1 >> 0] = HEAP8[i2 >> 0] | 0;
  i1 = i1 + 1 | 0;
  i2 = i2 + 1 | 0;
  i3 = i3 - 1 | 0;
 }
 return i4 | 0;
}

function _oggz_get_granuleshift(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i4 = i3 + 12 | 0;
 i5 = i3 + 8 | 0;
 i6 = i3 + 4 | 0;
 i7 = i3;
 HEAP32[i5 >> 2] = i1;
 HEAP32[i6 >> 2] = i2;
 do if (HEAP32[i5 >> 2] | 0) {
  HEAP32[i7 >> 2] = _oggz_get_stream(HEAP32[i5 >> 2] | 0, HEAP32[i6 >> 2] | 0) | 0;
  if (!(HEAP32[i7 >> 2] | 0)) {
   HEAP32[i4 >> 2] = -20;
   break;
  } else {
   HEAP32[i4 >> 2] = HEAP32[(HEAP32[i7 >> 2] | 0) + 408 >> 2];
   break;
  }
 } else HEAP32[i4 >> 2] = -2; while (0);
 STACKTOP = i3;
 return HEAP32[i4 >> 2] | 0;
}

function _oggz_reset(i1, i2, i3, i4, i5) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 i5 = i5 | 0;
 var i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0;
 i6 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i7 = i6 + 16 | 0;
 i8 = i6 + 12 | 0;
 i9 = i6;
 i10 = i6 + 8 | 0;
 HEAP32[i7 >> 2] = i1;
 HEAP32[i8 >> 2] = i2;
 i2 = i9;
 HEAP32[i2 >> 2] = i3;
 HEAP32[i2 + 4 >> 2] = i4;
 HEAP32[i10 >> 2] = i5;
 _oggz_reset_streams(HEAP32[i7 >> 2] | 0);
 i5 = i9;
 i9 = _oggz_reset_seek(HEAP32[i7 >> 2] | 0, HEAP32[i8 >> 2] | 0, HEAP32[i5 >> 2] | 0, HEAP32[i5 + 4 >> 2] | 0, HEAP32[i10 >> 2] | 0) | 0;
 STACKTOP = i6;
 return i9 | 0;
}

function __array_swap(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i5 = i4 + 12 | 0;
 i6 = i4 + 8 | 0;
 i7 = i4 + 4 | 0;
 i8 = i4;
 HEAP32[i5 >> 2] = i1;
 HEAP32[i6 >> 2] = i2;
 HEAP32[i7 >> 2] = i3;
 HEAP32[i8 >> 2] = HEAP32[(HEAP32[i5 >> 2] | 0) + (HEAP32[i6 >> 2] << 2) >> 2];
 HEAP32[(HEAP32[i5 >> 2] | 0) + (HEAP32[i6 >> 2] << 2) >> 2] = HEAP32[(HEAP32[i5 >> 2] | 0) + (HEAP32[i7 >> 2] << 2) >> 2];
 HEAP32[(HEAP32[i5 >> 2] | 0) + (HEAP32[i7 >> 2] << 2) >> 2] = HEAP32[i8 >> 2];
 STACKTOP = i4;
 return;
}

function _dirac_uint(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0, i5 = 0, i6 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2 + 8 | 0;
 i4 = i2 + 4 | 0;
 i5 = i2;
 HEAP32[i3 >> 2] = i1;
 HEAP32[i4 >> 2] = 0;
 HEAP32[i5 >> 2] = 0;
 while (1) {
  i1 = (_dirac_bs_read(HEAP32[i3 >> 2] | 0, 1) | 0) != 0 ^ 1;
  i6 = HEAP32[i4 >> 2] | 0;
  if (!i1) break;
  HEAP32[i4 >> 2] = i6 + 1;
  HEAP32[i5 >> 2] = HEAP32[i5 >> 2] << 1;
  i1 = _dirac_bs_read(HEAP32[i3 >> 2] | 0, 1) | 0;
  HEAP32[i5 >> 2] = HEAP32[i5 >> 2] | i1;
 }
 STACKTOP = i2;
 return (1 << i6) - 1 + (HEAP32[i5 >> 2] | 0) | 0;
}

function _oggz_vector_set_cmp(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i5 = i4 + 8 | 0;
 i6 = i4 + 4 | 0;
 i7 = i4;
 HEAP32[i5 >> 2] = i1;
 HEAP32[i6 >> 2] = i2;
 HEAP32[i7 >> 2] = i3;
 HEAP32[(HEAP32[i5 >> 2] | 0) + 12 >> 2] = HEAP32[i6 >> 2];
 HEAP32[(HEAP32[i5 >> 2] | 0) + 16 >> 2] = HEAP32[i7 >> 2];
 if (!(HEAP32[i6 >> 2] | 0)) {
  STACKTOP = i4;
  return 0;
 }
 _oggz_vector_qsort(HEAP32[i5 >> 2] | 0, 0, (HEAP32[(HEAP32[i5 >> 2] | 0) + 4 >> 2] | 0) - 1 | 0);
 STACKTOP = i4;
 return 0;
}

function _dirac_bs_skip(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i4 = i3 + 4 | 0;
 i5 = i3;
 HEAP32[i4 >> 2] = i1;
 HEAP32[i5 >> 2] = i2;
 i2 = (HEAP32[i4 >> 2] | 0) + 12 | 0;
 HEAP32[i2 >> 2] = (HEAP32[i2 >> 2] | 0) - (HEAP32[i5 >> 2] | 0);
 while (1) {
  if ((HEAP32[(HEAP32[i4 >> 2] | 0) + 12 >> 2] | 0) > 0) break;
  i5 = (HEAP32[i4 >> 2] | 0) + 4 | 0;
  HEAP32[i5 >> 2] = (HEAP32[i5 >> 2] | 0) + 1;
  i5 = (HEAP32[i4 >> 2] | 0) + 12 | 0;
  HEAP32[i5 >> 2] = (HEAP32[i5 >> 2] | 0) + 8;
 }
 STACKTOP = i3;
 return;
}

function _oggz_vector_nth_p(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i4 = i3 + 8 | 0;
 i5 = i3 + 4 | 0;
 i6 = i3;
 HEAP32[i5 >> 2] = i1;
 HEAP32[i6 >> 2] = i2;
 do if (HEAP32[i5 >> 2] | 0) if ((HEAP32[i6 >> 2] | 0) >= (HEAP32[(HEAP32[i5 >> 2] | 0) + 4 >> 2] | 0)) {
  HEAP32[i4 >> 2] = 0;
  break;
 } else {
  HEAP32[i4 >> 2] = HEAP32[(HEAP32[(HEAP32[i5 >> 2] | 0) + 8 >> 2] | 0) + (HEAP32[i6 >> 2] << 2) >> 2];
  break;
 } else HEAP32[i4 >> 2] = 0; while (0);
 STACKTOP = i3;
 return HEAP32[i4 >> 2] | 0;
}

function _fclose(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0, i5 = 0;
 i2 = (HEAP32[i1 >> 2] & 1 | 0) != 0;
 if (!i2) {
  ___lock(5368);
  i3 = HEAP32[i1 + 52 >> 2] | 0;
  i4 = i1 + 56 | 0;
  if (i3 | 0) HEAP32[i3 + 56 >> 2] = HEAP32[i4 >> 2];
  i5 = HEAP32[i4 >> 2] | 0;
  if (i5 | 0) HEAP32[i5 + 52 >> 2] = i3;
  if ((HEAP32[1341] | 0) == (i1 | 0)) HEAP32[1341] = i5;
  ___unlock(5368);
 }
 i5 = _fflush(i1) | 0;
 i3 = FUNCTION_TABLE_ii[HEAP32[i1 + 12 >> 2] & 15](i1) | 0 | i5;
 i5 = HEAP32[i1 + 92 >> 2] | 0;
 if (i5 | 0) _free(i5);
 if (!i2) _free(i1);
 return i3 | 0;
}

function _oggz_vector_foreach(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i4 = i3 + 8 | 0;
 i5 = i3 + 4 | 0;
 i6 = i3;
 HEAP32[i4 >> 2] = i1;
 HEAP32[i5 >> 2] = i2;
 HEAP32[i6 >> 2] = 0;
 while (1) {
  if ((HEAP32[i6 >> 2] | 0) >= (HEAP32[(HEAP32[i4 >> 2] | 0) + 4 >> 2] | 0)) break;
  FUNCTION_TABLE_ii[HEAP32[i5 >> 2] & 15](HEAP32[(HEAP32[(HEAP32[i4 >> 2] | 0) + 8 >> 2] | 0) + (HEAP32[i6 >> 2] << 2) >> 2] | 0) | 0;
  HEAP32[i6 >> 2] = (HEAP32[i6 >> 2] | 0) + 1;
 }
 STACKTOP = i3;
 return 0;
}

function _getter_error_check(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i4 = i3 + 8 | 0;
 i5 = i3 + 4 | 0;
 i6 = i3;
 HEAP32[i5 >> 2] = i1;
 HEAP32[i6 >> 2] = i2;
 do if (HEAP32[i5 >> 2] | 0) {
  if (!(HEAP16[(HEAP32[i5 >> 2] | 0) + 118 >> 1] | 0)) {
   HEAP32[i4 >> 2] = -5;
   break;
  }
  if (!(HEAP32[i6 >> 2] | 0)) {
   HEAP32[i4 >> 2] = -16;
   break;
  } else {
   HEAP32[i4 >> 2] = 0;
   break;
  }
 } else HEAP32[i4 >> 2] = -2; while (0);
 STACKTOP = i3;
 return HEAP32[i4 >> 2] | 0;
}

function _oggz_get_stream(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i4 = i3 + 8 | 0;
 i5 = i3 + 4 | 0;
 i6 = i3;
 HEAP32[i5 >> 2] = i1;
 HEAP32[i6 >> 2] = i2;
 if ((HEAP32[i6 >> 2] | 0) == -1) {
  HEAP32[i4 >> 2] = 0;
  i7 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i7 | 0;
 } else {
  HEAP32[i4 >> 2] = _oggz_vector_find_with(HEAP32[(HEAP32[i5 >> 2] | 0) + 80 >> 2] | 0, 1, HEAP32[i6 >> 2] | 0) | 0;
  i7 = HEAP32[i4 >> 2] | 0;
  STACKTOP = i3;
  return i7 | 0;
 }
 return 0;
}

function _dirac_bs_init(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i5 = i4 + 8 | 0;
 i6 = i4 + 4 | 0;
 i7 = i4;
 HEAP32[i5 >> 2] = i1;
 HEAP32[i6 >> 2] = i2;
 HEAP32[i7 >> 2] = i3;
 HEAP32[HEAP32[i5 >> 2] >> 2] = HEAP32[i6 >> 2];
 HEAP32[(HEAP32[i5 >> 2] | 0) + 4 >> 2] = HEAP32[i6 >> 2];
 HEAP32[(HEAP32[i5 >> 2] | 0) + 8 >> 2] = (HEAP32[(HEAP32[i5 >> 2] | 0) + 4 >> 2] | 0) + (HEAP32[i7 >> 2] | 0);
 HEAP32[(HEAP32[i5 >> 2] | 0) + 12 >> 2] = 8;
 STACKTOP = i4;
 return;
}

function _bq_free(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0, i5 = 0, i6 = 0, i7 = 0;
 i2 = i1 + 4 | 0;
 if (!(HEAP32[i2 >> 2] | 0)) {
  i3 = HEAP32[i1 >> 2] | 0;
  HEAP32[i2 >> 2] = 0;
  _free(i3);
  _free(i1);
  return;
 }
 i4 = HEAP32[i1 >> 2] | 0;
 i5 = 0;
 while (1) {
  _free(HEAP32[i4 + (i5 * 24 | 0) >> 2] | 0);
  i6 = HEAP32[i1 >> 2] | 0;
  HEAP32[i6 + (i5 * 24 | 0) >> 2] = 0;
  i5 = i5 + 1 | 0;
  if (i5 >>> 0 >= (HEAP32[i2 >> 2] | 0) >>> 0) {
   i7 = i6;
   break;
  } else i4 = i6;
 }
 i3 = i7;
 HEAP32[i2 >> 2] = 0;
 _free(i3);
 _free(i1);
 return;
}

function _ogg_stream_reset(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0;
 if (!i1) {
  i2 = -1;
  return i2 | 0;
 }
 if (!(HEAP32[i1 >> 2] | 0)) {
  i2 = -1;
  return i2 | 0;
 }
 HEAP32[i1 + 8 >> 2] = 0;
 HEAP32[i1 + 12 >> 2] = 0;
 HEAP32[i1 + 28 >> 2] = 0;
 HEAP32[i1 + 32 >> 2] = 0;
 HEAP32[i1 + 36 >> 2] = 0;
 HEAP32[i1 + 324 >> 2] = 0;
 HEAP32[i1 + 328 >> 2] = 0;
 HEAP32[i1 + 332 >> 2] = 0;
 HEAP32[i1 + 340 >> 2] = -1;
 i3 = i1 + 344 | 0;
 HEAP32[i3 >> 2] = 0;
 HEAP32[i3 + 4 >> 2] = 0;
 HEAP32[i3 + 8 >> 2] = 0;
 HEAP32[i3 + 12 >> 2] = 0;
 i2 = 0;
 return i2 | 0;
}

function _oggz_strdup(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0, i5 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2 + 8 | 0;
 i4 = i2 + 4 | 0;
 i5 = i2;
 HEAP32[i4 >> 2] = i1;
 do if (HEAP32[i4 >> 2] | 0) {
  HEAP32[i5 >> 2] = _malloc((_oggz_comment_len(HEAP32[i4 >> 2] | 0) | 0) + 1 | 0) | 0;
  if (!(HEAP32[i5 >> 2] | 0)) {
   HEAP32[i3 >> 2] = 0;
   break;
  } else {
   HEAP32[i3 >> 2] = _strcpy(HEAP32[i5 >> 2] | 0, HEAP32[i4 >> 2] | 0) | 0;
   break;
  }
 } else HEAP32[i3 >> 2] = 0; while (0);
 STACKTOP = i2;
 return HEAP32[i3 >> 2] | 0;
}

function _memcmp(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0, i8 = 0, i9 = 0, i10 = 0, i11 = 0;
 L1 : do if (!i3) i4 = 0; else {
  i5 = i3;
  i6 = i1;
  i7 = i2;
  while (1) {
   i8 = HEAP8[i6 >> 0] | 0;
   i9 = HEAP8[i7 >> 0] | 0;
   if (i8 << 24 >> 24 != i9 << 24 >> 24) {
    i10 = i8;
    i11 = i9;
    break;
   }
   i5 = i5 + -1 | 0;
   if (!i5) {
    i4 = 0;
    break L1;
   } else {
    i6 = i6 + 1 | 0;
    i7 = i7 + 1 | 0;
   }
  }
  i4 = (i10 & 255) - (i11 & 255) | 0;
 } while (0);
 return i4 | 0;
}

function _oggz_comment_len(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0, i5 = 0, i6 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2 + 8 | 0;
 i4 = i2 + 4 | 0;
 i5 = i2;
 HEAP32[i4 >> 2] = i1;
 if (!(HEAP32[i4 >> 2] | 0)) {
  HEAP32[i3 >> 2] = 0;
  i6 = HEAP32[i3 >> 2] | 0;
  STACKTOP = i2;
  return i6 | 0;
 } else {
  HEAP32[i5 >> 2] = _strlen(HEAP32[i4 >> 2] | 0) | 0;
  HEAP32[i3 >> 2] = (HEAP32[i5 >> 2] | 0) >>> 0 < 4294967294 ? HEAP32[i5 >> 2] | 0 : -2;
  i6 = HEAP32[i3 >> 2] | 0;
  STACKTOP = i2;
  return i6 | 0;
 }
 return 0;
}

function _memset(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0;
 i4 = i1 + i3 | 0;
 if ((i3 | 0) >= 20) {
  i2 = i2 & 255;
  i5 = i1 & 3;
  i6 = i2 | i2 << 8 | i2 << 16 | i2 << 24;
  i7 = i4 & ~3;
  if (i5) {
   i5 = i1 + 4 - i5 | 0;
   while ((i1 | 0) < (i5 | 0)) {
    HEAP8[i1 >> 0] = i2;
    i1 = i1 + 1 | 0;
   }
  }
  while ((i1 | 0) < (i7 | 0)) {
   HEAP32[i1 >> 2] = i6;
   i1 = i1 + 4 | 0;
  }
 }
 while ((i1 | 0) < (i4 | 0)) {
  HEAP8[i1 >> 0] = i2;
  i1 = i1 + 1 | 0;
 }
 return i1 - i3 | 0;
}

function _auto_fishead(i1, i2, i3, i4, i5) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 i5 = i5 | 0;
 var i6 = 0, i7 = 0, i8 = 0;
 i6 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i7 = i6 + 16 | 0;
 i8 = i6 + 12 | 0;
 HEAP32[i7 >> 2] = i1;
 HEAP32[i8 >> 2] = i2;
 HEAP32[i6 + 8 >> 2] = i3;
 HEAP32[i6 + 4 >> 2] = i4;
 HEAP32[i6 >> 2] = i5;
 _oggz_set_granulerate(HEAP32[i7 >> 2] | 0, HEAP32[i8 >> 2] | 0, 0, 0, 1, 0) | 0;
 _oggz_stream_set_numheaders(HEAP32[i7 >> 2] | 0, HEAP32[i8 >> 2] | 0, 1) | 0;
 STACKTOP = i6;
 return 1;
}

function _oggskel_destroy(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0, i5 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2 + 4 | 0;
 i4 = i2;
 HEAP32[i4 >> 2] = i1;
 if (!(HEAP32[i4 >> 2] | 0)) {
  HEAP32[i3 >> 2] = -2;
  i5 = HEAP32[i3 >> 2] | 0;
  STACKTOP = i2;
  return i5 | 0;
 } else {
  _oggskel_vect_destroy(HEAP32[(HEAP32[i4 >> 2] | 0) + 112 >> 2] | 0);
  _free(HEAP32[i4 >> 2] | 0);
  HEAP32[i3 >> 2] = 0;
  i5 = HEAP32[i3 >> 2] | 0;
  STACKTOP = i2;
  return i5 | 0;
 }
 return 0;
}

function ___stdio_seek(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i5 = i4;
 i6 = i4 + 20 | 0;
 HEAP32[i5 >> 2] = HEAP32[i1 + 60 >> 2];
 HEAP32[i5 + 4 >> 2] = 0;
 HEAP32[i5 + 8 >> 2] = i2;
 HEAP32[i5 + 12 >> 2] = i6;
 HEAP32[i5 + 16 >> 2] = i3;
 if ((___syscall_ret(___syscall140(140, i5 | 0) | 0) | 0) < 0) {
  HEAP32[i6 >> 2] = -1;
  i7 = -1;
 } else i7 = HEAP32[i6 >> 2] | 0;
 STACKTOP = i4;
 return i7 | 0;
}

function _strcmp(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0;
 i3 = HEAP8[i1 >> 0] | 0;
 i4 = HEAP8[i2 >> 0] | 0;
 if (i3 << 24 >> 24 == 0 ? 1 : i3 << 24 >> 24 != i4 << 24 >> 24) {
  i5 = i3;
  i6 = i4;
 } else {
  i4 = i1;
  i1 = i2;
  do {
   i4 = i4 + 1 | 0;
   i1 = i1 + 1 | 0;
   i2 = HEAP8[i4 >> 0] | 0;
   i3 = HEAP8[i1 >> 0] | 0;
  } while (!(i2 << 24 >> 24 == 0 ? 1 : i2 << 24 >> 24 != i3 << 24 >> 24));
  i5 = i2;
  i6 = i3;
 }
 return (i5 & 255) - (i6 & 255) | 0;
}

function _oggz_dlist_delete(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2 + 4 | 0;
 i4 = i2;
 HEAP32[i3 >> 2] = i1;
 HEAP32[i4 >> 2] = HEAP32[HEAP32[HEAP32[i3 >> 2] >> 2] >> 2];
 while (1) {
  if (!(HEAP32[i4 >> 2] | 0)) break;
  _free(HEAP32[(HEAP32[i4 >> 2] | 0) + 4 >> 2] | 0);
  HEAP32[i4 >> 2] = HEAP32[HEAP32[i4 >> 2] >> 2];
 }
 _free(HEAP32[(HEAP32[i3 >> 2] | 0) + 4 >> 2] | 0);
 _free(HEAP32[i3 >> 2] | 0);
 STACKTOP = i2;
 return;
}

function _oggz_auto_identify_page(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0, i7 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i5 = i4 + 8 | 0;
 i6 = i4 + 4 | 0;
 i7 = i4;
 HEAP32[i5 >> 2] = i1;
 HEAP32[i6 >> 2] = i2;
 HEAP32[i7 >> 2] = i3;
 i3 = _oggz_auto_identify(HEAP32[i5 >> 2] | 0, HEAP32[i7 >> 2] | 0, HEAP32[(HEAP32[i6 >> 2] | 0) + 8 >> 2] | 0, HEAP32[(HEAP32[i6 >> 2] | 0) + 12 >> 2] | 0) | 0;
 STACKTOP = i4;
 return i3 | 0;
}

function ___towrite(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0;
 i2 = i1 + 74 | 0;
 i3 = HEAP8[i2 >> 0] | 0;
 HEAP8[i2 >> 0] = i3 + 255 | i3;
 i3 = HEAP32[i1 >> 2] | 0;
 if (!(i3 & 8)) {
  HEAP32[i1 + 8 >> 2] = 0;
  HEAP32[i1 + 4 >> 2] = 0;
  i2 = HEAP32[i1 + 44 >> 2] | 0;
  HEAP32[i1 + 28 >> 2] = i2;
  HEAP32[i1 + 20 >> 2] = i2;
  HEAP32[i1 + 16 >> 2] = i2 + (HEAP32[i1 + 48 >> 2] | 0);
  i4 = 0;
 } else {
  HEAP32[i1 >> 2] = i3 | 32;
  i4 = -1;
 }
 return i4 | 0;
}

function _oggz_map_return_value_to_error(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2 + 4 | 0;
 i4 = i2;
 HEAP32[i4 >> 2] = i1;
 switch (HEAP32[i4 >> 2] | 0) {
 case 0:
  {
   HEAP32[i3 >> 2] = 0;
   break;
  }
 case 1:
  {
   HEAP32[i3 >> 2] = -14;
   break;
  }
 case -1:
  {
   HEAP32[i3 >> 2] = -15;
   break;
  }
 default:
  HEAP32[i3 >> 2] = HEAP32[i4 >> 2];
 }
 STACKTOP = i2;
 return HEAP32[i3 >> 2] | 0;
}

function _auto_annodex(i1, i2, i3, i4, i5) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 i5 = i5 | 0;
 var i6 = 0, i7 = 0, i8 = 0;
 i6 = STACKTOP;
 STACKTOP = STACKTOP + 32 | 0;
 i7 = i6 + 16 | 0;
 i8 = i6 + 12 | 0;
 HEAP32[i7 >> 2] = i1;
 HEAP32[i8 >> 2] = i2;
 HEAP32[i6 + 8 >> 2] = i3;
 HEAP32[i6 + 4 >> 2] = i4;
 HEAP32[i6 >> 2] = i5;
 _oggz_set_granulerate(HEAP32[i7 >> 2] | 0, HEAP32[i8 >> 2] | 0, 0, 0, 1, 0) | 0;
 STACKTOP = i6;
 return 1;
}

function copyTempDouble(i1) {
 i1 = i1 | 0;
 HEAP8[tempDoublePtr >> 0] = HEAP8[i1 >> 0];
 HEAP8[tempDoublePtr + 1 >> 0] = HEAP8[i1 + 1 >> 0];
 HEAP8[tempDoublePtr + 2 >> 0] = HEAP8[i1 + 2 >> 0];
 HEAP8[tempDoublePtr + 3 >> 0] = HEAP8[i1 + 3 >> 0];
 HEAP8[tempDoublePtr + 4 >> 0] = HEAP8[i1 + 4 >> 0];
 HEAP8[tempDoublePtr + 5 >> 0] = HEAP8[i1 + 5 >> 0];
 HEAP8[tempDoublePtr + 6 >> 0] = HEAP8[i1 + 6 >> 0];
 HEAP8[tempDoublePtr + 7 >> 0] = HEAP8[i1 + 7 >> 0];
}

function ___stdout_write(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 80 | 0;
 i5 = i4;
 HEAP32[i1 + 36 >> 2] = 6;
 if ((HEAP32[i1 >> 2] & 64 | 0) == 0 ? (HEAP32[i5 >> 2] = HEAP32[i1 + 60 >> 2], HEAP32[i5 + 4 >> 2] = 21505, HEAP32[i5 + 8 >> 2] = i4 + 12, ___syscall54(54, i5 | 0) | 0) : 0) HEAP8[i1 + 75 >> 0] = -1;
 i5 = ___stdio_write(i1, i2, i3) | 0;
 STACKTOP = i4;
 return i5 | 0;
}

function ___ftello_unlocked(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0;
 if (!(HEAP32[i1 >> 2] & 128)) i2 = 1; else i2 = (HEAP32[i1 + 20 >> 2] | 0) >>> 0 > (HEAP32[i1 + 28 >> 2] | 0) >>> 0 ? 2 : 1;
 i3 = FUNCTION_TABLE_iiii[HEAP32[i1 + 40 >> 2] & 7](i1, 0, i2) | 0;
 if ((i3 | 0) < 0) i4 = i3; else i4 = i3 - (HEAP32[i1 + 8 >> 2] | 0) + (HEAP32[i1 + 4 >> 2] | 0) + (HEAP32[i1 + 20 >> 2] | 0) - (HEAP32[i1 + 28 >> 2] | 0) | 0;
 return i4 | 0;
}

function _oggz_comment_free(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2;
 HEAP32[i3 >> 2] = i1;
 if (!(HEAP32[i3 >> 2] | 0)) {
  STACKTOP = i2;
  return;
 }
 if (HEAP32[HEAP32[i3 >> 2] >> 2] | 0) _free(HEAP32[HEAP32[i3 >> 2] >> 2] | 0);
 if (HEAP32[(HEAP32[i3 >> 2] | 0) + 4 >> 2] | 0) _free(HEAP32[(HEAP32[i3 >> 2] | 0) + 4 >> 2] | 0);
 _free(HEAP32[i3 >> 2] | 0);
 STACKTOP = i2;
 return;
}

function _ogv_demuxer_init() {
 var i1 = 0;
 HEAP32[1327] = 0;
 HEAP32[1334] = _bq_init() | 0;
 i1 = _oggz_new(32) | 0;
 HEAP32[1328] = i1;
 _oggz_set_read_callback(i1, -1, 8, 0) | 0;
 _oggz_io_set_read(HEAP32[1328] | 0, 4, HEAP32[1334] | 0) | 0;
 _oggz_io_set_seek(HEAP32[1328] | 0, 5, HEAP32[1334] | 0) | 0;
 _oggz_io_set_tell(HEAP32[1328] | 0, 8, HEAP32[1334] | 0) | 0;
 HEAP32[1329] = _oggskel_new() | 0;
 return;
}

function _bq_flush(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0;
 i2 = i1 + 4 | 0;
 if (!(HEAP32[i2 >> 2] | 0)) {
  HEAP32[i2 >> 2] = 0;
  return;
 }
 i3 = HEAP32[i1 >> 2] | 0;
 i4 = 0;
 do {
  _free(HEAP32[i3 + (i4 * 24 | 0) >> 2] | 0);
  i3 = HEAP32[i1 >> 2] | 0;
  HEAP32[i3 + (i4 * 24 | 0) >> 2] = 0;
  i4 = i4 + 1 | 0;
 } while (i4 >>> 0 < (HEAP32[i2 >> 2] | 0) >>> 0);
 HEAP32[i2 >> 2] = 0;
 return;
}

function _oggz_vector_clear(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2;
 HEAP32[i3 >> 2] = i1;
 if (HEAP32[(HEAP32[i3 >> 2] | 0) + 8 >> 2] | 0) {
  _free(HEAP32[(HEAP32[i3 >> 2] | 0) + 8 >> 2] | 0);
  HEAP32[(HEAP32[i3 >> 2] | 0) + 8 >> 2] = 0;
 }
 HEAP32[(HEAP32[i3 >> 2] | 0) + 4 >> 2] = 0;
 HEAP32[HEAP32[i3 >> 2] >> 2] = 0;
 STACKTOP = i2;
 return;
}

function ___muldsi3(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0;
 i3 = i1 & 65535;
 i4 = i2 & 65535;
 i5 = Math_imul(i4, i3) | 0;
 i6 = i1 >>> 16;
 i1 = (i5 >>> 16) + (Math_imul(i4, i6) | 0) | 0;
 i4 = i2 >>> 16;
 i2 = Math_imul(i4, i3) | 0;
 return (tempRet0 = (i1 >>> 16) + (Math_imul(i4, i6) | 0) + (((i1 & 65535) + i2 | 0) >>> 16) | 0, i1 + i2 << 16 | i5 & 65535 | 0) | 0;
}

function _calloc(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0;
 if (i1) {
  i3 = Math_imul(i2, i1) | 0;
  if ((i2 | i1) >>> 0 > 65535) i4 = ((i3 >>> 0) / (i1 >>> 0) | 0 | 0) == (i2 | 0) ? i3 : -1; else i4 = i3;
 } else i4 = 0;
 i3 = _malloc(i4) | 0;
 if (!i3) return i3 | 0;
 if (!(HEAP32[i3 + -4 >> 2] & 3)) return i3 | 0;
 _memset(i3 | 0, 0, i4 | 0) | 0;
 return i3 | 0;
}

function _memmove(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0;
 if ((i2 | 0) < (i1 | 0) & (i1 | 0) < (i2 + i3 | 0)) {
  i4 = i1;
  i2 = i2 + i3 | 0;
  i1 = i1 + i3 | 0;
  while ((i3 | 0) > 0) {
   i1 = i1 - 1 | 0;
   i2 = i2 - 1 | 0;
   i3 = i3 - 1 | 0;
   HEAP8[i1 >> 0] = HEAP8[i2 >> 0] | 0;
  }
  i1 = i4;
 } else _memcpy(i1, i2, i3) | 0;
 return i1 | 0;
}

function _ogv_demuxer_media_length() {
 var i1 = 0, i2 = 0, i3 = 0, i4 = 0;
 i1 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i2 = i1;
 i3 = i2;
 HEAP32[i3 >> 2] = -1;
 HEAP32[i3 + 4 >> 2] = -1;
 if (!(HEAP32[1330] | 0)) {
  i4 = -1;
  STACKTOP = i1;
  return i4 | 0;
 }
 _oggskel_get_segment_len(HEAP32[1329] | 0, i2) | 0;
 i4 = HEAP32[i2 >> 2] | 0;
 STACKTOP = i1;
 return i4 | 0;
}

function _oggz_find_stream(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0, i6 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i4 = i3 + 8 | 0;
 i5 = i3 + 4 | 0;
 i6 = i3;
 HEAP32[i4 >> 2] = i1;
 HEAP32[i5 >> 2] = i2;
 HEAP32[i6 >> 2] = HEAP32[i4 >> 2];
 STACKTOP = i3;
 return (HEAP32[(HEAP32[i6 >> 2] | 0) + 336 >> 2] | 0) == (HEAP32[i5 >> 2] | 0) | 0;
}

function _oggz_stream_reset(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2 + 4 | 0;
 i4 = i2;
 HEAP32[i3 >> 2] = i1;
 HEAP32[i4 >> 2] = HEAP32[i3 >> 2];
 if ((HEAP32[(HEAP32[i4 >> 2] | 0) + 336 >> 2] | 0) == -1) {
  STACKTOP = i2;
  return 0;
 }
 _ogg_stream_reset(HEAP32[i4 >> 2] | 0) | 0;
 STACKTOP = i2;
 return 0;
}

function _llvm_cttz_i32(i1) {
 i1 = i1 | 0;
 var i2 = 0;
 i2 = HEAP8[cttz_i8 + (i1 & 255) >> 0] | 0;
 if ((i2 | 0) < 8) return i2 | 0;
 i2 = HEAP8[cttz_i8 + (i1 >> 8 & 255) >> 0] | 0;
 if ((i2 | 0) < 8) return i2 + 8 | 0;
 i2 = HEAP8[cttz_i8 + (i1 >> 16 & 255) >> 0] | 0;
 if ((i2 | 0) < 8) return i2 + 16 | 0;
 return (HEAP8[cttz_i8 + (i1 >>> 24) >> 0] | 0) + 24 | 0;
}

function _oggz_read_close(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2 + 4 | 0;
 i4 = i2;
 HEAP32[i3 >> 2] = i1;
 HEAP32[i4 >> 2] = (HEAP32[i3 >> 2] | 0) + 112;
 _ogg_stream_clear((HEAP32[i4 >> 2] | 0) + 32 | 0) | 0;
 _ogg_sync_clear(HEAP32[i4 >> 2] | 0) | 0;
 STACKTOP = i2;
 return HEAP32[i3 >> 2] | 0;
}

function _int32_be_at(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2;
 HEAP32[i3 >> 2] = i1;
 STACKTOP = i2;
 return (HEAPU8[HEAP32[i3 >> 2] >> 0] | 0) << 24 | (HEAPU8[(HEAP32[i3 >> 2] | 0) + 1 >> 0] | 0) << 16 | (HEAPU8[(HEAP32[i3 >> 2] | 0) + 2 >> 0] | 0) << 8 | (HEAPU8[(HEAP32[i3 >> 2] | 0) + 3 >> 0] | 0) | 0;
}

function _int32_le_at(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2;
 HEAP32[i3 >> 2] = i1;
 STACKTOP = i2;
 return HEAPU8[HEAP32[i3 >> 2] >> 0] | 0 | (HEAPU8[(HEAP32[i3 >> 2] | 0) + 1 >> 0] | 0) << 8 | (HEAPU8[(HEAP32[i3 >> 2] | 0) + 2 >> 0] | 0) << 16 | (HEAPU8[(HEAP32[i3 >> 2] | 0) + 3 >> 0] | 0) << 24 | 0;
}

function ___fseeko(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0, i6 = 0;
 if ((HEAP32[i1 + 76 >> 2] | 0) > -1) {
  i4 = (___lockfile(i1) | 0) == 0;
  i5 = ___fseeko_unlocked(i1, i2, i3) | 0;
  if (i4) i6 = i5; else {
   ___unlockfile(i1);
   i6 = i5;
  }
 } else i6 = ___fseeko_unlocked(i1, i2, i3) | 0;
 return i6 | 0;
}

function _ogg_sync_wrote(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0, i5 = 0;
 i3 = HEAP32[i1 + 4 >> 2] | 0;
 if ((i3 | 0) <= -1) {
  i4 = -1;
  return i4 | 0;
 }
 i5 = i1 + 8 | 0;
 i1 = (HEAP32[i5 >> 2] | 0) + i2 | 0;
 if ((i1 | 0) > (i3 | 0)) {
  i4 = -1;
  return i4 | 0;
 }
 HEAP32[i5 >> 2] = i1;
 i4 = 0;
 return i4 | 0;
}

function _oggz_vector_size(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2 + 4 | 0;
 i4 = i2;
 HEAP32[i4 >> 2] = i1;
 if (!(HEAP32[i4 >> 2] | 0)) HEAP32[i3 >> 2] = 0; else HEAP32[i3 >> 2] = HEAP32[(HEAP32[i4 >> 2] | 0) + 4 >> 2];
 STACKTOP = i2;
 return HEAP32[i3 >> 2] | 0;
}

function _ogv_demuxer_flush() {
 var i1 = 0, i2 = 0, i3 = 0;
 i1 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i2 = i1;
 _oggz_purge(HEAP32[1328] | 0) | 0;
 i3 = _oggz_seek(HEAP32[1328] | 0, 0, 1) | 0;
 if ((i3 | 0) < 0) {
  HEAP32[i2 >> 2] = i3;
  _printf(2749, i2) | 0;
 }
 _bq_flush(HEAP32[1334] | 0);
 STACKTOP = i1;
 return;
}

function _ogg_sync_clear(i1) {
 i1 = i1 | 0;
 var i2 = 0;
 if (!i1) return 0;
 i2 = HEAP32[i1 >> 2] | 0;
 if (i2 | 0) _free(i2);
 HEAP32[i1 >> 2] = 0;
 HEAP32[i1 + 4 >> 2] = 0;
 HEAP32[i1 + 8 >> 2] = 0;
 HEAP32[i1 + 12 >> 2] = 0;
 HEAP32[i1 + 16 >> 2] = 0;
 HEAP32[i1 + 20 >> 2] = 0;
 HEAP32[i1 + 24 >> 2] = 0;
 return 0;
}

function _read(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0;
 i4 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i5 = i4;
 HEAP32[i5 >> 2] = i1;
 HEAP32[i5 + 4 >> 2] = i2;
 HEAP32[i5 + 8 >> 2] = i3;
 i3 = ___syscall_ret(___syscall3(3, i5 | 0) | 0) | 0;
 STACKTOP = i4;
 return i3 | 0;
}

function _oggskel_vect_new() {
 var i1 = 0, i2 = 0, i3 = 0;
 i1 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i2 = i1 + 4 | 0;
 i3 = i1;
 HEAP32[i3 >> 2] = _calloc(1, 8) | 0;
 if (!(HEAP32[i3 >> 2] | 0)) HEAP32[i2 >> 2] = 0; else HEAP32[i2 >> 2] = HEAP32[i3 >> 2];
 STACKTOP = i1;
 return HEAP32[i2 >> 2] | 0;
}

function _ogg_sync_reset(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0;
 if ((HEAP32[i1 + 4 >> 2] | 0) <= -1) {
  i2 = -1;
  return i2 | 0;
 }
 i3 = i1 + 8 | 0;
 HEAP32[i3 >> 2] = 0;
 HEAP32[i3 + 4 >> 2] = 0;
 HEAP32[i3 + 8 >> 2] = 0;
 HEAP32[i3 + 12 >> 2] = 0;
 HEAP32[i3 + 16 >> 2] = 0;
 i2 = 0;
 return i2 | 0;
}

function ___uremdi3(i1, i2, i3, i4) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 var i5 = 0, i6 = 0;
 i5 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i6 = i5 | 0;
 ___udivmoddi4(i1, i2, i3, i4, i6) | 0;
 STACKTOP = i5;
 return (tempRet0 = HEAP32[i6 + 4 >> 2] | 0, HEAP32[i6 >> 2] | 0) | 0;
}

function _oggz_flags_disabled(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2 + 4 | 0;
 i4 = i2;
 HEAP32[i4 >> 2] = i1;
 if (HEAP32[i4 >> 2] & 1 | 0) HEAP32[i3 >> 2] = -11; else HEAP32[i3 >> 2] = 0;
 STACKTOP = i2;
 return HEAP32[i3 >> 2] | 0;
}

function _oggz_read_free_pbuffers(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2 + 4 | 0;
 i4 = i2;
 HEAP32[i3 >> 2] = i1;
 HEAP32[i4 >> 2] = HEAP32[i3 >> 2];
 _oggz_read_free_pbuffer_entry(HEAP32[i4 >> 2] | 0);
 STACKTOP = i2;
 return 1;
}

function _ogg_stream_clear(i1) {
 i1 = i1 | 0;
 var i2 = 0;
 if (!i1) return 0;
 i2 = HEAP32[i1 >> 2] | 0;
 if (i2 | 0) _free(i2);
 i2 = HEAP32[i1 + 16 >> 2] | 0;
 if (i2 | 0) _free(i2);
 i2 = HEAP32[i1 + 20 >> 2] | 0;
 if (i2 | 0) _free(i2);
 _memset(i1 | 0, 0, 360) | 0;
 return 0;
}

function ___muldi3(i1, i2, i3, i4) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 var i5 = 0, i6 = 0;
 i5 = i1;
 i1 = i3;
 i3 = ___muldsi3(i5, i1) | 0;
 i6 = tempRet0;
 return (tempRet0 = (Math_imul(i2, i1) | 0) + (Math_imul(i4, i5) | 0) + i6 | i6 & 0, i3 | 0 | 0) | 0;
}

function _oggz_dlist_is_empty(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2;
 HEAP32[i3 >> 2] = i1;
 STACKTOP = i2;
 return (HEAP32[HEAP32[HEAP32[i3 >> 2] >> 2] >> 2] | 0) == (HEAP32[(HEAP32[i3 >> 2] | 0) + 4 >> 2] | 0) | 0;
}

function _ferror(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0;
 if ((HEAP32[i1 + 76 >> 2] | 0) > -1) {
  i2 = (___lockfile(i1) | 0) == 0;
  i3 = (HEAP32[i1 >> 2] | 0) >>> 5 & 1;
  if (i2) i4 = i3; else i4 = i3;
 } else i4 = (HEAP32[i1 >> 2] | 0) >>> 5 & 1;
 return i4 | 0;
}

function runPostSets() {}
function _bitshift64Ashr(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 if ((i3 | 0) < 32) {
  tempRet0 = i2 >> i3;
  return i1 >>> i3 | (i2 & (1 << i3) - 1) << 32 - i3;
 }
 tempRet0 = (i2 | 0) < 0 ? -1 : 0;
 return i2 >> i3 - 32 | 0;
}

function _oggz_seek_reset_stream(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2;
 HEAP32[i3 >> 2] = i1;
 i1 = (HEAP32[i3 >> 2] | 0) + 488 | 0;
 HEAP32[i1 >> 2] = -1;
 HEAP32[i1 + 4 >> 2] = -1;
 STACKTOP = i2;
 return 0;
}

function _int16_be_at(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2;
 HEAP32[i3 >> 2] = i1;
 STACKTOP = i2;
 return ((HEAPU8[HEAP32[i3 >> 2] >> 0] | 0) << 8 | (HEAPU8[(HEAP32[i3 >> 2] | 0) + 1 >> 0] | 0)) & 65535 | 0;
}

function ___ftello(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0, i4 = 0;
 if ((HEAP32[i1 + 76 >> 2] | 0) > -1) {
  i2 = (___lockfile(i1) | 0) == 0;
  i3 = ___ftello_unlocked(i1) | 0;
  if (i2) i4 = i3; else i4 = i3;
 } else i4 = ___ftello_unlocked(i1) | 0;
 return i4 | 0;
}

function _int16_le_at(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2;
 HEAP32[i3 >> 2] = i1;
 STACKTOP = i2;
 return (HEAPU8[HEAP32[i3 >> 2] >> 0] | 0 | (HEAPU8[(HEAP32[i3 >> 2] | 0) + 1 >> 0] | 0) << 8) & 65535 | 0;
}

function _ogg_sync_init(i1) {
 i1 = i1 | 0;
 if (!i1) return 0;
 HEAP32[i1 >> 2] = 0;
 HEAP32[i1 + 4 >> 2] = 0;
 HEAP32[i1 + 8 >> 2] = 0;
 HEAP32[i1 + 12 >> 2] = 0;
 HEAP32[i1 + 16 >> 2] = 0;
 HEAP32[i1 + 20 >> 2] = 0;
 HEAP32[i1 + 24 >> 2] = 0;
 return 0;
}

function _oggz_read_free_pbuffer_entry(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2;
 HEAP32[i3 >> 2] = i1;
 _free(HEAP32[HEAP32[i3 >> 2] >> 2] | 0);
 _free(HEAP32[i3 >> 2] | 0);
 STACKTOP = i2;
 return;
}

function _oggz_reset_streams(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2;
 HEAP32[i3 >> 2] = i1;
 _oggz_vector_foreach(HEAP32[(HEAP32[i3 >> 2] | 0) + 80 >> 2] | 0, 6) | 0;
 STACKTOP = i2;
 return;
}

function ___stdio_close(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2;
 HEAP32[i3 >> 2] = HEAP32[i1 + 60 >> 2];
 i1 = ___syscall_ret(___syscall6(6, i3 | 0) | 0) | 0;
 STACKTOP = i2;
 return i1 | 0;
}

function copyTempFloat(i1) {
 i1 = i1 | 0;
 HEAP8[tempDoublePtr >> 0] = HEAP8[i1 >> 0];
 HEAP8[tempDoublePtr + 1 >> 0] = HEAP8[i1 + 1 >> 0];
 HEAP8[tempDoublePtr + 2 >> 0] = HEAP8[i1 + 2 >> 0];
 HEAP8[tempDoublePtr + 3 >> 0] = HEAP8[i1 + 3 >> 0];
}

function _oggz_vector_delete(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2;
 HEAP32[i3 >> 2] = i1;
 _oggz_vector_clear(HEAP32[i3 >> 2] | 0);
 _free(HEAP32[i3 >> 2] | 0);
 STACKTOP = i2;
 return;
}

function _bq_init() {
 var i1 = 0, i2 = 0;
 i1 = _malloc(32) | 0;
 i2 = i1 + 16 | 0;
 HEAP32[i2 >> 2] = 0;
 HEAP32[i2 + 4 >> 2] = 0;
 HEAP32[i1 + 4 >> 2] = 0;
 HEAP32[i1 + 8 >> 2] = 8;
 HEAP32[i1 >> 2] = _malloc(192) | 0;
 return i1 | 0;
}

function _printf(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0, i4 = 0;
 i3 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i4 = i3;
 HEAP32[i4 >> 2] = i2;
 i2 = _vfprintf(HEAP32[538] | 0, i1, i4) | 0;
 STACKTOP = i3;
 return i2 | 0;
}

function _ogg_page_serialno(i1) {
 i1 = i1 | 0;
 var i2 = 0;
 i2 = HEAP32[i1 >> 2] | 0;
 return (HEAPU8[i2 + 15 >> 0] | 0) << 8 | (HEAPU8[i2 + 14 >> 0] | 0) | (HEAPU8[i2 + 16 >> 0] | 0) << 16 | (HEAPU8[i2 + 17 >> 0] | 0) << 24 | 0;
}

function _readCallback(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 var i4 = 0, i5 = 0;
 i4 = _bq_headroom(i1) | 0;
 i5 = i4 >>> 0 > i3 >>> 0 ? i3 : i4;
 i4 = (_bq_read(i1, i2, i5) | 0) < 0;
 return (i4 ? -1 : i5) | 0;
}

function _bitshift64Shl(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 if ((i3 | 0) < 32) {
  tempRet0 = i2 << i3 | (i1 & (1 << i3) - 1 << 32 - i3) >>> 32 - i3;
  return i1 << i3;
 }
 tempRet0 = i1 << i3 - 32;
 return 0;
}

function _i64Subtract(i1, i2, i3, i4) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 var i5 = 0;
 i5 = i2 - i4 >>> 0;
 i5 = i2 - i4 - (i3 >>> 0 > i1 >>> 0 | 0) >>> 0;
 return (tempRet0 = i5, i1 - i3 >>> 0 | 0) | 0;
}

function _bitshift64Lshr(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 if ((i3 | 0) < 32) {
  tempRet0 = i2 >>> i3;
  return i1 >>> i3 | (i2 & (1 << i3) - 1) << 32 - i3;
 }
 tempRet0 = 0;
 return i2 >>> i3 - 32 | 0;
}

function _dirac_bool(i1) {
 i1 = i1 | 0;
 var i2 = 0, i3 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + 16 | 0;
 i3 = i2;
 HEAP32[i3 >> 2] = i1;
 i1 = _dirac_bs_read(HEAP32[i3 >> 2] | 0, 1) | 0;
 STACKTOP = i2;
 return i1 | 0;
}

function dynCall_iiiiii(i1, i2, i3, i4, i5, i6) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 i5 = i5 | 0;
 i6 = i6 | 0;
 return FUNCTION_TABLE_iiiiii[i1 & 31](i2 | 0, i3 | 0, i4 | 0, i5 | 0, i6 | 0) | 0;
}

function _i64Add(i1, i2, i3, i4) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 var i5 = 0;
 i5 = i1 + i3 >>> 0;
 return (tempRet0 = i2 + i4 + (i5 >>> 0 < i1 >>> 0 | 0) >>> 0, i5 | 0) | 0;
}

function dynCall_iiiii(i1, i2, i3, i4, i5) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 i5 = i5 | 0;
 return FUNCTION_TABLE_iiiii[i1 & 15](i2 | 0, i3 | 0, i4 | 0, i5 | 0) | 0;
}

function ___syscall_ret(i1) {
 i1 = i1 | 0;
 var i2 = 0;
 if (i1 >>> 0 > 4294963200) {
  HEAP32[(___errno_location() | 0) >> 2] = 0 - i1;
  i2 = -1;
 } else i2 = i1;
 return i2 | 0;
}

function _ogv_demuxer_destroy() {
 _oggskel_destroy(HEAP32[1329] | 0) | 0;
 _oggz_close(HEAP32[1328] | 0) | 0;
 _bq_free(HEAP32[1334] | 0);
 HEAP32[1334] = 0;
 return;
}

function dynCall_iiii(i1, i2, i3, i4) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 return FUNCTION_TABLE_iiii[i1 & 7](i2 | 0, i3 | 0, i4 | 0) | 0;
}

function ___errno_location() {
 var i1 = 0;
 if (!(HEAP32[1335] | 0)) i1 = 5384; else i1 = HEAP32[(_pthread_self() | 0) + 64 >> 2] | 0;
 return i1 | 0;
}
function stackAlloc(i1) {
 i1 = i1 | 0;
 var i2 = 0;
 i2 = STACKTOP;
 STACKTOP = STACKTOP + i1 | 0;
 STACKTOP = STACKTOP + 15 & -16;
 return i2 | 0;
}

function ___udivdi3(i1, i2, i3, i4) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 return ___udivmoddi4(i1, i2, i3, i4, 0) | 0;
}

function _wctomb(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 var i3 = 0;
 if (!i1) i3 = 0; else i3 = _wcrtomb(i1, i2, 0) | 0;
 return i3 | 0;
}

function _bq_tell(i1) {
 i1 = i1 | 0;
 var i2 = 0;
 i2 = i1 + 16 | 0;
 tempRet0 = HEAP32[i2 + 4 >> 2] | 0;
 return HEAP32[i2 >> 2] | 0;
}

function dynCall_iii(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 return FUNCTION_TABLE_iii[i1 & 1](i2 | 0, i3 | 0) | 0;
}

function _fileno(i1) {
 i1 = i1 | 0;
 (HEAP32[i1 + 76 >> 2] | 0) > -1 ? ___lockfile(i1) | 0 : 0;
 return HEAP32[i1 + 60 >> 2] | 0;
}

function b5(i1, i2, i3, i4, i5) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 i5 = i5 | 0;
 abort(5);
 return 0;
}

function _ogv_demuxer_receive_input(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 _bq_append(HEAP32[1334] | 0, i1, i2);
 return;
}

function _strncpy(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 ___stpncpy(i1, i2, i3) | 0;
 return i1 | 0;
}

function setThrew(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 if (!__THREW__) {
  __THREW__ = i1;
  threwValue = i2;
 }
}

function _tolower(i1) {
 i1 = i1 | 0;
 var i2 = 0;
 i2 = (_isupper(i1) | 0) == 0;
 return (i2 ? i1 : i1 | 32) | 0;
}

function _ogg_page_continued(i1) {
 i1 = i1 | 0;
 return (HEAPU8[(HEAP32[i1 >> 2] | 0) + 5 >> 0] | 0) & 1 | 0;
}

function b3(i1, i2, i3, i4) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 i4 = i4 | 0;
 abort(3);
 return 0;
}

function _fseek(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 return ___fseeko(i1, i2, i3) | 0;
}

function dynCall_ii(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 return FUNCTION_TABLE_ii[i1 & 15](i2 | 0) | 0;
}

function _ogg_page_bos(i1) {
 i1 = i1 | 0;
 return (HEAPU8[(HEAP32[i1 >> 2] | 0) + 5 >> 0] | 0) & 2 | 0;
}

function _cleanup_387(i1) {
 i1 = i1 | 0;
 if (!(HEAP32[i1 + 68 >> 2] | 0)) ___unlockfile(i1);
 return;
}

function establishStackSpace(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 STACKTOP = i1;
 STACK_MAX = i2;
}

function _tellCallback(i1) {
 i1 = i1 | 0;
 var i2 = 0;
 i2 = _bq_tell(i1) | 0;
 return i2 | 0;
}

function _strcpy(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 ___stpcpy(i1, i2) | 0;
 return i1 | 0;
}

function dynCall_vi(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 FUNCTION_TABLE_vi[i1 & 3](i2 | 0);
}

function b0(i1, i2, i3) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 i3 = i3 | 0;
 abort(0);
 return 0;
}

function _frexpl(d1, i2) {
 d1 = +d1;
 i2 = i2 | 0;
 return +(+_frexp(d1, i2));
}

function _isupper(i1) {
 i1 = i1 | 0;
 return (i1 + -65 | 0) >>> 0 < 26 | 0;
}

function b4(i1, i2) {
 i1 = i1 | 0;
 i2 = i2 | 0;
 abort(4);
 return 0;
}

function _ogv_demuxer_seek_to_keypoint(i1) {
 i1 = i1 | 0;
 return 0;
}

function _ftell(i1) {
 i1 = i1 | 0;
 return ___ftello(i1) | 0;
}

function stackRestore(i1) {
 i1 = i1 | 0;
 STACKTOP = i1;
}

function setTempRet0(i1) {
 i1 = i1 | 0;
 tempRet0 = i1;
}

function b2(i1) {
 i1 = i1 | 0;
 abort(2);
 return 0;
}

function ___unlockfile(i1) {
 i1 = i1 | 0;
 return;
}

function ___lockfile(i1) {
 i1 = i1 | 0;
 return 0;
}

function getTempRet0() {
 return tempRet0 | 0;
}

function _ogv_demuxer_seekable() {
 return 1;
}

function stackSave() {
 return STACKTOP | 0;
}

function b1(i1) {
 i1 = i1 | 0;
 abort(1);
}

// EMSCRIPTEN_END_FUNCS
var FUNCTION_TABLE_iiii = [b0,___stdout_write,___stdio_seek,_oggz_comment_cmp,_readCallback,_seekCallback,___stdio_write,b0];
var FUNCTION_TABLE_vi = [b1,_oggz_comment_free,_cleanup_387,b1];
var FUNCTION_TABLE_ii = [b2,___stdio_close,_oggz_stream_clear,_oggz_read_free_pbuffers,_oggz_read_update_gp,_oggz_read_deliver_packet,_oggz_stream_reset,_oggz_seek_reset_stream,_tellCallback,b2,b2,b2,b2,b2,b2,b2];
var FUNCTION_TABLE_iiiii = [b3,_auto_calc_theora,_auto_calc_vorbis,_auto_calc_speex,_auto_calc_flac,_auto_calc_celt,_auto_calc_opus,_auto_calc_vp8,_readPacketCallback,b3,b3,b3,b3,b3,b3,b3];
var FUNCTION_TABLE_iii = [b4,_oggz_find_stream];
var FUNCTION_TABLE_iiiiii = [b5,_auto_theora,_auto_rcalc_theora,_auto_vorbis,_auto_rcalc_vorbis,_auto_speex,_auto_oggpcm2,_auto_cmml,_auto_annodex,_auto_fishead,_auto_flac0,_auto_flac,_auto_anxdata,_auto_celt,_auto_kate,_auto_dirac,_auto_opus,_auto_rcalc_opus,_auto_vp8,_oggz_metric_default_linear,_oggz_metric_dirac,_oggz_metric_vp8,_oggz_metric_default_granuleshift,b5,b5,b5,b5,b5,b5
,b5,b5,b5];

  return { _ogv_demuxer_receive_input: _ogv_demuxer_receive_input, _i64Subtract: _i64Subtract, _ogv_demuxer_keypoint_offset: _ogv_demuxer_keypoint_offset, _memcpy: _memcpy, _ogv_demuxer_process: _ogv_demuxer_process, _i64Add: _i64Add, _ogv_demuxer_flush: _ogv_demuxer_flush, _bitshift64Ashr: _bitshift64Ashr, _ogv_demuxer_init: _ogv_demuxer_init, _memset: _memset, _ogv_demuxer_destroy: _ogv_demuxer_destroy, _malloc: _malloc, _free: _free, _ogv_demuxer_seekable: _ogv_demuxer_seekable, _ogv_demuxer_media_duration: _ogv_demuxer_media_duration, _ogv_demuxer_media_length: _ogv_demuxer_media_length, _memmove: _memmove, _bitshift64Lshr: _bitshift64Lshr, _ogv_demuxer_seek_to_keypoint: _ogv_demuxer_seek_to_keypoint, _bitshift64Shl: _bitshift64Shl, runPostSets: runPostSets, stackAlloc: stackAlloc, stackSave: stackSave, stackRestore: stackRestore, establishStackSpace: establishStackSpace, setThrew: setThrew, setTempRet0: setTempRet0, getTempRet0: getTempRet0, dynCall_iiii: dynCall_iiii, dynCall_vi: dynCall_vi, dynCall_ii: dynCall_ii, dynCall_iiiii: dynCall_iiiii, dynCall_iii: dynCall_iii, dynCall_iiiiii: dynCall_iiiiii };
})
// EMSCRIPTEN_END_ASM
(Module.asmGlobalArg, Module.asmLibraryArg, buffer);
var _ogv_demuxer_receive_input = Module["_ogv_demuxer_receive_input"] = asm["_ogv_demuxer_receive_input"];
var _i64Subtract = Module["_i64Subtract"] = asm["_i64Subtract"];
var _ogv_demuxer_keypoint_offset = Module["_ogv_demuxer_keypoint_offset"] = asm["_ogv_demuxer_keypoint_offset"];
var runPostSets = Module["runPostSets"] = asm["runPostSets"];
var _ogv_demuxer_process = Module["_ogv_demuxer_process"] = asm["_ogv_demuxer_process"];
var _i64Add = Module["_i64Add"] = asm["_i64Add"];
var _ogv_demuxer_flush = Module["_ogv_demuxer_flush"] = asm["_ogv_demuxer_flush"];
var _ogv_demuxer_seek_to_keypoint = Module["_ogv_demuxer_seek_to_keypoint"] = asm["_ogv_demuxer_seek_to_keypoint"];
var _bitshift64Ashr = Module["_bitshift64Ashr"] = asm["_bitshift64Ashr"];
var _ogv_demuxer_init = Module["_ogv_demuxer_init"] = asm["_ogv_demuxer_init"];
var _memset = Module["_memset"] = asm["_memset"];
var _ogv_demuxer_destroy = Module["_ogv_demuxer_destroy"] = asm["_ogv_demuxer_destroy"];
var _malloc = Module["_malloc"] = asm["_malloc"];
var _ogv_demuxer_seekable = Module["_ogv_demuxer_seekable"] = asm["_ogv_demuxer_seekable"];
var _memcpy = Module["_memcpy"] = asm["_memcpy"];
var _ogv_demuxer_media_duration = Module["_ogv_demuxer_media_duration"] = asm["_ogv_demuxer_media_duration"];
var _ogv_demuxer_media_length = Module["_ogv_demuxer_media_length"] = asm["_ogv_demuxer_media_length"];
var _memmove = Module["_memmove"] = asm["_memmove"];
var _bitshift64Lshr = Module["_bitshift64Lshr"] = asm["_bitshift64Lshr"];
var _free = Module["_free"] = asm["_free"];
var _bitshift64Shl = Module["_bitshift64Shl"] = asm["_bitshift64Shl"];
var dynCall_iiii = Module["dynCall_iiii"] = asm["dynCall_iiii"];
var dynCall_vi = Module["dynCall_vi"] = asm["dynCall_vi"];
var dynCall_ii = Module["dynCall_ii"] = asm["dynCall_ii"];
var dynCall_iiiii = Module["dynCall_iiiii"] = asm["dynCall_iiiii"];
var dynCall_iii = Module["dynCall_iii"] = asm["dynCall_iii"];
var dynCall_iiiiii = Module["dynCall_iiiiii"] = asm["dynCall_iiiiii"];
Runtime.stackAlloc = asm["stackAlloc"];
Runtime.stackSave = asm["stackSave"];
Runtime.stackRestore = asm["stackRestore"];
Runtime.establishStackSpace = asm["establishStackSpace"];
Runtime.setTempRet0 = asm["setTempRet0"];
Runtime.getTempRet0 = asm["getTempRet0"];
function ExitStatus(status) {
 this.name = "ExitStatus";
 this.message = "Program terminated with exit(" + status + ")";
 this.status = status;
}
ExitStatus.prototype = new Error;
ExitStatus.prototype.constructor = ExitStatus;
var initialStackTop;
var preloadStartTime = null;
var calledMain = false;
dependenciesFulfilled = function runCaller() {
 if (!Module["calledRun"]) run();
 if (!Module["calledRun"]) dependenciesFulfilled = runCaller;
};
Module["callMain"] = Module.callMain = function callMain(args) {
 args = args || [];
 ensureInitRuntime();
 var argc = args.length + 1;
 function pad() {
  for (var i = 0; i < 4 - 1; i++) {
   argv.push(0);
  }
 }
 var argv = [ allocate(intArrayFromString(Module["thisProgram"]), "i8", ALLOC_NORMAL) ];
 pad();
 for (var i = 0; i < argc - 1; i = i + 1) {
  argv.push(allocate(intArrayFromString(args[i]), "i8", ALLOC_NORMAL));
  pad();
 }
 argv.push(0);
 argv = allocate(argv, "i32", ALLOC_NORMAL);
 try {
  var ret = Module["_main"](argc, argv, 0);
  exit(ret, true);
 } catch (e) {
  if (e instanceof ExitStatus) {
   return;
  } else if (e == "SimulateInfiniteLoop") {
   Module["noExitRuntime"] = true;
   return;
  } else {
   if (e && typeof e === "object" && e.stack) Module.printErr("exception thrown: " + [ e, e.stack ]);
   throw e;
  }
 } finally {
  calledMain = true;
 }
};
function run(args) {
 args = args || Module["arguments"];
 if (preloadStartTime === null) preloadStartTime = Date.now();
 if (runDependencies > 0) {
  return;
 }
 preRun();
 if (runDependencies > 0) return;
 if (Module["calledRun"]) return;
 function doRun() {
  if (Module["calledRun"]) return;
  Module["calledRun"] = true;
  if (ABORT) return;
  ensureInitRuntime();
  preMain();
  if (Module["onRuntimeInitialized"]) Module["onRuntimeInitialized"]();
  if (Module["_main"] && shouldRunNow) Module["callMain"](args);
  postRun();
 }
 if (Module["setStatus"]) {
  Module["setStatus"]("Running...");
  setTimeout((function() {
   setTimeout((function() {
    Module["setStatus"]("");
   }), 1);
   doRun();
  }), 1);
 } else {
  doRun();
 }
}
Module["run"] = Module.run = run;
function exit(status, implicit) {
 if (implicit && Module["noExitRuntime"]) {
  return;
 }
 if (Module["noExitRuntime"]) {} else {
  ABORT = true;
  EXITSTATUS = status;
  STACKTOP = initialStackTop;
  exitRuntime();
  if (Module["onExit"]) Module["onExit"](status);
 }
 if (ENVIRONMENT_IS_NODE) {
  process["exit"](status);
 } else if (ENVIRONMENT_IS_SHELL && typeof quit === "function") {
  quit(status);
 }
 throw new ExitStatus(status);
}
Module["exit"] = Module.exit = exit;
var abortDecorators = [];
function abort(what) {
 if (what !== undefined) {
  Module.print(what);
  Module.printErr(what);
  what = JSON.stringify(what);
 } else {
  what = "";
 }
 ABORT = true;
 EXITSTATUS = 1;
 var extra = "\nIf this abort() is unexpected, build with -s ASSERTIONS=1 which can give more information.";
 var output = "abort(" + what + ") at " + stackTrace() + extra;
 if (abortDecorators) {
  abortDecorators.forEach((function(decorator) {
   output = decorator(output, what);
  }));
 }
 throw output;
}
Module["abort"] = Module.abort = abort;
if (Module["preInit"]) {
 if (typeof Module["preInit"] == "function") Module["preInit"] = [ Module["preInit"] ];
 while (Module["preInit"].length > 0) {
  Module["preInit"].pop()();
 }
}
var shouldRunNow = false;
if (Module["noInitialRun"]) {
 shouldRunNow = false;
}
Module["noExitRuntime"] = true;
run();
var inputBuffer, inputBufferSize;
function reallocInputBuffer(size) {
 if (inputBuffer && inputBufferSize >= size) {
  return inputBuffer;
 }
 if (inputBuffer) {
  Module._free(inputBuffer);
 }
 inputBufferSize = size;
 inputBuffer = Module._malloc(inputBufferSize);
 return inputBuffer;
}
var getTimestamp;
if (typeof performance === "undefined" || typeof performance.now === "undefined") {
 getTimestamp = Date.now;
} else {
 getTimestamp = performance.now.bind(performance);
}
function time(func) {
 var start = getTimestamp(), ret;
 ret = func();
 var delta = getTimestamp() - start;
 Module.cpuTime += delta;
 return ret;
}
Module.loadedMetadata = false;
Module.videoCodec = null;
Module.audioCodec = null;
Module.duration = NaN;
Module.onseek = null;
Module.cpuTime = 0;
Module.audioPackets = [];
Object.defineProperty(Module, "hasAudio", {
 get: (function() {
  return Module.loadedMetadata && Module.audioCodec;
 })
});
Object.defineProperty(Module, "audioReady", {
 get: (function() {
  return Module.audioPackets.length > 0;
 })
});
Object.defineProperty(Module, "audioTimestamp", {
 get: (function() {
  if (Module.audioPackets.length > 0) {
   return Module.audioPackets[0].timestamp;
  } else {
   return -1;
  }
 })
});
Module.videoPackets = [];
Object.defineProperty(Module, "hasVideo", {
 get: (function() {
  return Module.loadedMetadata && Module.videoCodec;
 })
});
Object.defineProperty(Module, "frameReady", {
 get: (function() {
  return Module.videoPackets.length > 0;
 })
});
Object.defineProperty(Module, "frameTimestamp", {
 get: (function() {
  if (Module.videoPackets.length > 0) {
   return Module.videoPackets[0].timestamp;
  } else {
   return -1;
  }
 })
});
Object.defineProperty(Module, "keyframeTimestamp", {
 get: (function() {
  if (Module.videoPackets.length > 0) {
   return Module.videoPackets[0].keyframeTimestamp;
  } else {
   return -1;
  }
 })
});
Object.defineProperty(Module, "processing", {
 get: function getProcessing() {
  return false;
 }
});
Object.defineProperty(Module, "seekable", {
 get: (function() {
  return !!Module._ogv_demuxer_seekable();
 })
});
Module.init = (function(callback) {
 time((function() {
  Module._ogv_demuxer_init();
 }));
 callback();
});
Module.receiveInput = (function(data, callback) {
 var ret = time((function() {
  var len = data.byteLength;
  var buffer = reallocInputBuffer(len);
  Module.HEAPU8.set(new Uint8Array(data), buffer);
  Module._ogv_demuxer_receive_input(buffer, len);
 }));
 callback();
});
Module.process = (function(callback) {
 var ret = time((function() {
  return Module._ogv_demuxer_process();
 }));
 callback(!!ret);
});
Module.dequeueVideoPacket = (function(callback) {
 if (Module.videoPackets.length) {
  var packet = Module.videoPackets.shift().data;
  callback(packet);
 } else {
  callback(null);
 }
});
Module.dequeueAudioPacket = (function(callback) {
 if (Module.audioPackets.length) {
  var packet = Module.audioPackets.shift().data;
  callback(packet);
 } else {
  callback(null);
 }
});
Module.getKeypointOffset = (function(timeSeconds, callback) {
 var offset = time((function() {
  return Module._ogv_demuxer_keypoint_offset(timeSeconds * 1e3);
 }));
 callback(offset);
});
Module.seekToKeypoint = (function(timeSeconds, callback) {
 var ret = time((function() {
  return Module._ogv_demuxer_seek_to_keypoint(timeSeconds * 1e3);
 }));
 if (ret) {
  Module.audioPackets.splice(0, Module.audioPackets.length);
  Module.videoPackets.splice(0, Module.videoPackets.length);
 }
 callback(!!ret);
});
Module.flush = (function(callback) {
 time((function() {
  Module.audioPackets.splice(0, Module.audioPackets.length);
  Module.videoPackets.splice(0, Module.videoPackets.length);
  Module._ogv_demuxer_flush();
 }));
 callback();
});
Module.close = (function() {});





  return Module;
};
