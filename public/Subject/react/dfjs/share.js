!function(C){function e(e){for(var n,t,o=e[0],r=e[1],a=e[2],i=0,s=[];i<o.length;i++)t=o[i],B[t]&&s.push(B[t][0]),B[t]=0;for(n in r)Object.prototype.hasOwnProperty.call(r,n)&&(C[n]=r[n]);for(v&&v(e);s.length;)s.shift()();return b.push.apply(b,a||[]),l()}function l(){for(var e,n=0;n<b.length;n++){for(var t=b[n],o=!0,r=1;r<t.length;r++){var a=t[r];0!==B[a]&&(o=!1)}o&&(b.splice(n--,1),e=U(U.s=t[0]))}return e}var t=window.webpackHotUpdate;window.webpackHotUpdate=function(e,n){!function(e,n){if(!N[e]||!p[e])return;for(var t in p[e]=!1,n)Object.prototype.hasOwnProperty.call(n,t)&&(M[t]=n[t]);0==--c&&0===d&&m()}(e,n),t&&t(e,n)};var a,o=!0,D="31d7eb9dc91b59fca188",n=1e4,R={},L=[],r=[];var i=[],S="idle";function T(e){S=e;for(var n=0;n<i.length;n++)i[n].call(null,e)}var s,M,I,c=0,d=0,u={},p={},N={};function A(e){return+e+""===e?+e:e}function f(e){if("idle"!==S)throw new Error("check() is only allowed in idle status");return o=e,T("check"),(a=n,a=a||1e4,new Promise(function(n,t){if("undefined"==typeof XMLHttpRequest)return t(new Error("No browser support"));try{var o=new XMLHttpRequest,r=U.p+""+D+".hot-update.json";o.open("GET",r,!0),o.timeout=a,o.send(null)}catch(e){return t(e)}o.onreadystatechange=function(){if(4===o.readyState)if(0===o.status)t(new Error("Manifest request to "+r+" timed out."));else if(404===o.status)n();else if(200!==o.status&&304!==o.status)t(new Error("Manifest request to "+r+" failed."));else{try{var e=JSON.parse(o.responseText)}catch(e){return void t(e)}n(e)}}})).then(function(e){if(!e)return T("idle"),null;p={},u={},N=e.c,I=e.h,T("prepare");var n=new Promise(function(e,n){s={resolve:e,reject:n}});for(var t in M={},B)_(t);return"prepare"===S&&0===d&&0===c&&m(),n});var a}function _(e){var n,t,o;N[e]?(p[e]=!0,c++,n=e,t=document.getElementsByTagName("head")[0],(o=document.createElement("script")).charset="utf-8",o.src=U.p+""+n+"."+D+".hot-update.js",t.appendChild(o)):u[e]=!0}function m(){T("ready");var n=s;if(s=null,n)if(o)Promise.resolve().then(function(){return h(o)}).then(function(e){n.resolve(e)},function(e){n.reject(e)});else{var e=[];for(var t in M)Object.prototype.hasOwnProperty.call(M,t)&&e.push(A(t));n.resolve(e)}}function h(t){if("ready"!==S)throw new Error("apply() is only allowed in ready status");var e,n,o,d,r;function a(e){for(var n=[e],t={},o=n.slice().map(function(e){return{chain:[e],id:e}});0<o.length;){var r=o.pop(),a=r.id,i=r.chain;if((d=H[a])&&!d.hot._selfAccepted){if(d.hot._selfDeclined)return{type:"self-declined",chain:i,moduleId:a};if(d.hot._main)return{type:"unaccepted",chain:i,moduleId:a};for(var s=0;s<d.parents.length;s++){var l=d.parents[s],c=H[l];if(c){if(c.hot._declinedDependencies[a])return{type:"declined",chain:i.concat([l]),moduleId:a,parentId:l};-1===n.indexOf(l)&&(c.hot._acceptedDependencies[a]?(t[l]||(t[l]=[]),u(t[l],[a])):(delete t[l],n.push(l),o.push({chain:i.concat([l]),id:l})))}}}}return{type:"accepted",moduleId:e,outdatedModules:n,outdatedDependencies:t}}function u(e,n){for(var t=0;t<n.length;t++){var o=n[t];-1===e.indexOf(o)&&e.push(o)}}t=t||{};var i={},s=[],l={},c=function(){};for(var p in M)if(Object.prototype.hasOwnProperty.call(M,p)){var f;r=A(p);var _=!1,m=!1,h=!1,b="";switch((f=M[p]?a(r):{type:"disposed",moduleId:p}).chain&&(b="\nUpdate propagation: "+f.chain.join(" -> ")),f.type){case"self-declined":t.onDeclined&&t.onDeclined(f),t.ignoreDeclined||(_=new Error("Aborted because of self decline: "+f.moduleId+b));break;case"declined":t.onDeclined&&t.onDeclined(f),t.ignoreDeclined||(_=new Error("Aborted because of declined dependency: "+f.moduleId+" in "+f.parentId+b));break;case"unaccepted":t.onUnaccepted&&t.onUnaccepted(f),t.ignoreUnaccepted||(_=new Error("Aborted because "+r+" is not accepted"+b));break;case"accepted":t.onAccepted&&t.onAccepted(f),m=!0;break;case"disposed":t.onDisposed&&t.onDisposed(f),h=!0;break;default:throw new Error("Unexception type "+f.type)}if(_)return T("abort"),Promise.reject(_);if(m)for(r in l[r]=M[r],u(s,f.outdatedModules),f.outdatedDependencies)Object.prototype.hasOwnProperty.call(f.outdatedDependencies,r)&&(i[r]||(i[r]=[]),u(i[r],f.outdatedDependencies[r]));h&&(u(s,[f.moduleId]),l[r]=c)}var g,y=[];for(n=0;n<s.length;n++)r=s[n],H[r]&&H[r].hot._selfAccepted&&y.push({module:r,errorHandler:H[r].hot._selfAccepted});T("dispose"),Object.keys(N).forEach(function(e){!1===N[e]&&delete B[e]});for(var x,v,w=s.slice();0<w.length;)if(r=w.pop(),d=H[r]){var k={},j=d.hot._disposeHandlers;for(o=0;o<j.length;o++)(e=j[o])(k);for(R[r]=k,d.hot.active=!1,delete H[r],delete i[r],o=0;o<d.children.length;o++){var E=H[d.children[o]];E&&(0<=(g=E.parents.indexOf(r))&&E.parents.splice(g,1))}}for(r in i)if(Object.prototype.hasOwnProperty.call(i,r)&&(d=H[r]))for(v=i[r],o=0;o<v.length;o++)x=v[o],0<=(g=d.children.indexOf(x))&&d.children.splice(g,1);for(r in T("apply"),D=I,l)Object.prototype.hasOwnProperty.call(l,r)&&(C[r]=l[r]);var O=null;for(r in i)if(Object.prototype.hasOwnProperty.call(i,r)&&(d=H[r])){v=i[r];var q=[];for(n=0;n<v.length;n++)if(x=v[n],e=d.hot._acceptedDependencies[x]){if(-1!==q.indexOf(e))continue;q.push(e)}for(n=0;n<q.length;n++){e=q[n];try{e(v)}catch(e){t.onErrored&&t.onErrored({type:"accept-errored",moduleId:r,dependencyId:v[n],error:e}),t.ignoreErrored||O||(O=e)}}}for(n=0;n<y.length;n++){var P=y[n];r=P.module,L=[r];try{U(r)}catch(n){if("function"==typeof P.errorHandler)try{P.errorHandler(n)}catch(e){t.onErrored&&t.onErrored({type:"self-accept-error-handler-errored",moduleId:r,error:e,originalError:n}),t.ignoreErrored||O||(O=e),O||(O=n)}else t.onErrored&&t.onErrored({type:"self-accept-errored",moduleId:r,error:n}),t.ignoreErrored||O||(O=n)}}return O?(T("fail"),Promise.reject(O)):(T("idle"),new Promise(function(e){e(s)}))}var H={},B={share:0},b=[];function U(e){if(H[e])return H[e].exports;var n,o,t=H[e]={i:e,l:!1,exports:{},hot:(n=e,o={_acceptedDependencies:{},_declinedDependencies:{},_selfAccepted:!1,_selfDeclined:!1,_disposeHandlers:[],_main:a!==n,active:!0,accept:function(e,n){if(void 0===e)o._selfAccepted=!0;else if("function"==typeof e)o._selfAccepted=e;else if("object"==typeof e)for(var t=0;t<e.length;t++)o._acceptedDependencies[e[t]]=n||function(){};else o._acceptedDependencies[e]=n||function(){}},decline:function(e){if(void 0===e)o._selfDeclined=!0;else if("object"==typeof e)for(var n=0;n<e.length;n++)o._declinedDependencies[e[n]]=!0;else o._declinedDependencies[e]=!0},dispose:function(e){o._disposeHandlers.push(e)},addDisposeHandler:function(e){o._disposeHandlers.push(e)},removeDisposeHandler:function(e){var n=o._disposeHandlers.indexOf(e);0<=n&&o._disposeHandlers.splice(n,1)},check:f,apply:h,status:function(e){if(!e)return S;i.push(e)},addStatusHandler:function(e){i.push(e)},removeStatusHandler:function(e){var n=i.indexOf(e);0<=n&&i.splice(n,1)},data:R[n]},a=void 0,o),parents:(r=L,L=[],r),children:[]};return C[e].call(t.exports,t,t.exports,function(n){var t=H[n];if(!t)return U;var o=function(e){return t.hot.active?(H[e]?-1===H[e].parents.indexOf(n)&&H[e].parents.push(n):(L=[n],a=e),-1===t.children.indexOf(e)&&t.children.push(e)):L=[],U(e)},e=function(n){return{configurable:!0,enumerable:!0,get:function(){return U[n]},set:function(e){U[n]=e}}};for(var r in U)Object.prototype.hasOwnProperty.call(U,r)&&"e"!==r&&"t"!==r&&Object.defineProperty(o,r,e(r));return o.e=function(e){return"ready"===S&&T("prepare"),d++,U.e(e).then(n,function(e){throw n(),e});function n(){d--,"prepare"===S&&(u[e]||_(e),0===d&&0===c&&m())}},o.t=function(e,n){return 1&n&&(e=o(e)),U.t(e,-2&n)},o}(e)),t.l=!0,t.exports}U.m=C,U.c=H,U.d=function(e,n,t){U.o(e,n)||Object.defineProperty(e,n,{enumerable:!0,get:t})},U.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},U.t=function(n,e){if(1&e&&(n=U(n)),8&e)return n;if(4&e&&"object"==typeof n&&n&&n.__esModule)return n;var t=Object.create(null);if(U.r(t),Object.defineProperty(t,"default",{enumerable:!0,value:n}),2&e&&"string"!=typeof n)for(var o in n)U.d(t,o,function(e){return n[e]}.bind(null,o));return t},U.n=function(e){var n=e&&e.__esModule?function(){return e.default}:function(){return e};return U.d(n,"a",n),n},U.o=function(e,n){return Object.prototype.hasOwnProperty.call(e,n)},U.p="",U.h=function(){return D};var g=window.webpackJsonp=window.webpackJsonp||[],y=g.push.bind(g);g.push=e,g=g.slice();for(var x=0;x<g.length;x++)e(g[x]);var v=y;b.push(["./src/src/share.js","commons"]),l()}({"./node_modules/_css-loader@2.0.0@css-loader/dist/cjs.js!./node_modules/_less-loader@4.1.0@less-loader/dist/cjs.js!./src/src/share/style/index.less":function(module,exports,__webpack_require__){eval('exports = module.exports = __webpack_require__(/*! ../../../../node_modules/_css-loader@2.0.0@css-loader/dist/runtime/api.js */ "./node_modules/_css-loader@2.0.0@css-loader/dist/runtime/api.js")(false);\n// Module\nexports.push([module.i, ".cataglory {\\n  padding-top: 50px;\\n  padding-bottom: 10px;\\n  background-color: white;\\n}\\n.cataglory .am-flexbox {\\n  line-height: 40px;\\n}\\n.cataglory .am-flexbox .cata-type {\\n  width: 50px;\\n  text-align: center;\\n  font-weight: 1000;\\n  flex-shrink: 0;\\n  font-size: 15px;\\n}\\n.cataglory .am-flexbox .tags .my-tag {\\n  margin-left: 5px;\\n  margin-top: 5px;\\n  border: solid 1px #ccc;\\n}\\n.cataglory .am-flexbox .tags .my-tag:before {\\n  display: none;\\n}\\n.cataglory .am-flexbox .tags .my-tag .am-tag-text {\\n  font-size: 13px;\\n}\\n.stelen_cataglory {\\n  padding-top: 50px;\\n  padding-bottom: 10px;\\n  background-color: white;\\n}\\n.stelen_cataglory .am-flexbox {\\n  line-height: 40px;\\n}\\n.stelen_cataglory .am-flexbox .cata-type {\\n  height: 30px;\\n  width: 50px;\\n  background: #ece0e0;\\n  margin-left: 10px;\\n  margin-right: 10px;\\n  text-align: center;\\n}\\n.stelen_cataglory .am-flexbox .my-tag {\\n  margin-left: 5px;\\n  margin-top: 5px;\\n  background: #ece0e0;\\n  height: 10px;\\n  padding-left: 40px;\\n  width: 50px;\\n}\\n.stelen_cataglory .am-flexbox .my-tag .am-tag-text {\\n  font-size: 13px;\\n}\\n.catatips {\\n  display: inline-block;\\n  line-height: 30px;\\n  text-align: center;\\n  background-color: #5243438a;\\n  color: white;\\n  width: 100%;\\n  position: fixed;\\n  top: 45px;\\n  z-index: 20000;\\n}\\n.booklist {\\n  text-align: center;\\n  padding-bottom: 10px;\\n}\\n.booklist .bookname {\\n  padding-top: 5px;\\n  overflow: hidden;\\n  text-overflow: ellipsis;\\n  white-space: nowrap;\\n}\\n.booklist .bookrecomm {\\n  padding-top: 5px;\\n}\\n.booklist .bookimage {\\n  position: relative;\\n}\\n.booklist .bookimage .img {\\n  width: 98%;\\n  height: 150px;\\n}\\n.booklist .bookimage .bookremark {\\n  position: absolute;\\n  top: 83%;\\n  line-height: 25px;\\n  width: 100%;\\n  background: #151414a3;\\n  text-align: center;\\n  font-size: 1px;\\n  color: white;\\n  overflow: hidden;\\n  text-overflow: ellipsis;\\n  white-space: nowrap;\\n}\\n.stelen_booklist {\\n  text-align: center;\\n  padding-bottom: 10px;\\n}\\n.stelen_booklist .book {\\n  padding-left: 5px;\\n  padding-right: 5px;\\n}\\n.stelen_booklist .book .bookname {\\n  margin-top: 5px;\\n  overflow: hidden;\\n  text-overflow: ellipsis;\\n  white-space: nowrap;\\n  height: 10px;\\n  background-color: #ece0e0;\\n}\\n.stelen_booklist .book .bookrecomm {\\n  padding-top: 5px;\\n}\\n.stelen_booklist .book .bookimage {\\n  position: relative;\\n  height: 80px;\\n  background-color: #ece0e0;\\n}\\n.stelen_booklist .book .bookimage .img {\\n  width: 98%;\\n}\\n.stelen_booklist .book .bookimage .bookremark {\\n  position: absolute;\\n  top: 83%;\\n  line-height: 25px;\\n  width: 100%;\\n  background: #151414a3;\\n  text-align: center;\\n  font-size: 1px;\\n  color: white;\\n  overflow: hidden;\\n  text-overflow: ellipsis;\\n  white-space: nowrap;\\n}\\n.am-flexbox .am-flexbox-item {\\n  margin-left: 0px;\\n}\\n.bookcover {\\n  position: absolute;\\n  top: 65px;\\n  display: inline-block;\\n  text-align: center;\\n  padding-left: 10px;\\n  padding-right: 10px;\\n}\\n.bookcover .bookcover-image {\\n  width: 100%;\\n}\\n.bookcover .playaudio {\\n  width: 80px;\\n  height: 80px;\\n  position: absolute;\\n  display: inline;\\n  left: 40%;\\n  top: 40%;\\n}\\n.bookcontent {\\n  position: absolute;\\n  width: 100%;\\n  top: 75%;\\n}\\n.am-drawer-sidebar {\\n  background-color: white;\\n}\\n.my-drawer .am-drawer-content .am-drawer-draghandle {\\n  background-color: white;\\n}\\n.active.am-list-item .am-list-line-multiple .am-list-content {\\n  color: #ff6c00e0;\\n}\\n.book-translate {\\n  position: absolute;\\n  right: 0px;\\n  bottom: 20px;\\n  background: #0000003d;\\n}\\n.swiper-pagination {\\n  display: none;\\n}\\n.swiper-pagination.active {\\n  display: block;\\n}\\n.book-word {\\n  width: 95%;\\n  position: absolute;\\n  top: 50px;\\n}\\n.question .tncontent img {\\n  display: block;\\n}\\n.question .items {\\n  width: 100%;\\n  text-align: left;\\n}\\n.page {\\n  display: inline-block;\\n  position: absolute;\\n  top: 50px;\\n  right: 10px;\\n}\\n.book-cover {\\n  margin-top: 50px;\\n}\\n.book-cover .book-download {\\n  display: inline-block;\\n  width: 100%;\\n  text-align: center;\\n  bottom: 30px;\\n  margin-left: 0px;\\n}\\n.book-cover .book-download .book-listen .am-flexbox .am-flexbox-item:before {\\n  display: inline;\\n}\\n.book-cover .book-download .book-listen .am-flexbox .am-flexbox-item .am-grid-item-content {\\n  position: relative;\\n}\\n.book-cover .book-download .book-listen .am-flexbox .am-flexbox-item .am-grid-item-content .icon {\\n  width: 46px;\\n  height: 46px;\\n}\\n.book-cover .book-cover-img {\\n  height: 80%;\\n  display: flex;\\n  align-items: center;\\n  justify-content: center;\\n}\\n.book-cover .book-cover-img .bookcover-image {\\n  width: 90%;\\n}\\n.title .am-list-line .am-list-content {\\n  text-align: center;\\n}\\n.am-button.book-button {\\n  background: #7e9490;\\n  text-align: center;\\n  width: 100%;\\n}\\n.record .am-grid .am-flexbox .am-flexbox-item .am-grid-item-content .am-grid-item-inner-content {\\n  display: flex;\\n}\\n.record .am-grid.am-grid-square .am-grid-item .am-grid-item-content {\\n  top: 0;\\n}\\n.am-grid.am-grid-square .am-grid-item .am-grid-item-content {\\n  position: absolute;\\n  top: 0;\\n  transform: translateY(0%);\\n}\\n.am-grid .am-flexbox .am-flexbox-item .am-grid-item-content {\\n  padding: 0 0;\\n}\\n.book-cover-img.user {\\n  position: relative;\\n}\\n.book-cover-img.user .userplay {\\n  position: absolute;\\n  display: inline-block;\\n  top: 35%;\\n  text-align: center;\\n  width: 100%;\\n}\\n.book-cover-img.user .userplay .am-icon-md {\\n  width: 65px;\\n  height: 65px;\\n}\\n.playicons {\\n  position: absolute;\\n  top: 50%;\\n  transform: translateY(-70%);\\n  left: 0px;\\n  width: 100%;\\n  text-align: center;\\n}\\n.playicons.am-icon-md {\\n  height: 20%;\\n}\\n.stopicons {\\n  position: absolute;\\n  top: 50%;\\n  transform: translateY(-70%);\\n  left: 0px;\\n  width: 100%;\\n  text-align: center;\\n}\\n.stopicons.am-icon-md {\\n  height: 20%;\\n}\\n", ""]);\n\n\n\n//# sourceURL=webpack:///./src/src/share/style/index.less?./node_modules/_css-loader@2.0.0@css-loader/dist/cjs.js!./node_modules/_less-loader@4.1.0@less-loader/dist/cjs.js')},"./node_modules/_react-audio-player@0.11.0@react-audio-player/dist/bundle.js":function(module,exports,__webpack_require__){eval('module.exports=function(e){var t={};function n(o){if(t[o])return t[o].exports;var r=t[o]={i:o,l:!1,exports:{}};return e[o].call(r.exports,r,r.exports,n),r.l=!0,r.exports}return n.m=e,n.c=t,n.d=function(e,t,o){n.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:o})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(e,t){if(1&t&&(e=n(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var o=Object.create(null);if(n.r(o),Object.defineProperty(o,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var r in e)n.d(o,r,function(t){return e[t]}.bind(null,r));return o},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="",n(n.s=0)}([function(e,t,n){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var o=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var n=arguments[t];for(var o in n)Object.prototype.hasOwnProperty.call(n,o)&&(e[o]=n[o])}return e},r=function(){function e(e,t){for(var n=0;n<t.length;n++){var o=t[n];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(e,o.key,o)}}return function(t,n,o){return n&&e(t.prototype,n),o&&e(t,o),t}}(),a=n(1),u=l(a),i=l(n(2));function l(e){return e&&e.__esModule?e:{default:e}}var s=function(e){function t(){return function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,t),function(e,t){if(!e)throw new ReferenceError("this hasn\'t been initialised - super() hasn\'t been called");return!t||"object"!=typeof t&&"function"!=typeof t?e:t}(this,(t.__proto__||Object.getPrototypeOf(t)).apply(this,arguments))}return function(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function, not "+typeof t);e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,enumerable:!1,writable:!0,configurable:!0}}),t&&(Object.setPrototypeOf?Object.setPrototypeOf(e,t):e.__proto__=t)}(t,a.Component),r(t,[{key:"componentDidMount",value:function(){var e=this,t=this.audioEl;this.updateVolume(this.props.volume),t.addEventListener("error",function(t){e.props.onError(t)}),t.addEventListener("canplay",function(t){e.props.onCanPlay(t)}),t.addEventListener("canplaythrough",function(t){e.props.onCanPlayThrough(t)}),t.addEventListener("play",function(t){e.setListenTrack(),e.props.onPlay(t)}),t.addEventListener("abort",function(t){e.clearListenTrack(),e.props.onAbort(t)}),t.addEventListener("ended",function(t){e.clearListenTrack(),e.props.onEnded(t)}),t.addEventListener("pause",function(t){e.clearListenTrack(),e.props.onPause(t)}),t.addEventListener("seeked",function(t){e.props.onSeeked(t)}),t.addEventListener("loadedmetadata",function(t){e.props.onLoadedMetadata(t)}),t.addEventListener("volumechange",function(t){e.props.onVolumeChanged(t)})}},{key:"componentWillReceiveProps",value:function(e){this.updateVolume(e.volume)}},{key:"setListenTrack",value:function(){var e=this;if(!this.listenTracker){var t=this.props.listenInterval;this.listenTracker=setInterval(function(){e.props.onListen(e.audioEl.currentTime)},t)}}},{key:"updateVolume",value:function(e){"number"==typeof e&&e!==this.audioEl.volume&&(this.audioEl.volume=e)}},{key:"clearListenTrack",value:function(){this.listenTracker&&(clearInterval(this.listenTracker),this.listenTracker=null)}},{key:"render",value:function(){var e=this,t=this.props.children||u.default.createElement("p",null,"Your browser does not support the ",u.default.createElement("code",null,"audio")," element."),n=!(!1===this.props.controls),r=this.props.title?this.props.title:this.props.src,a={};return this.props.controlsList&&(a.controlsList=this.props.controlsList),u.default.createElement("audio",o({autoPlay:this.props.autoPlay,className:"react-audio-player "+this.props.className,controls:n,crossOrigin:this.props.crossOrigin,id:this.props.id,loop:this.props.loop,muted:this.props.muted,onPlay:this.onPlay,preload:this.props.preload,ref:function(t){e.audioEl=t},src:this.props.src,style:this.props.style,title:r},a),t)}}]),t}();s.defaultProps={autoPlay:!1,children:null,className:"",controls:!1,controlsList:"",crossOrigin:null,id:"",listenInterval:1e4,loop:!1,muted:!1,onAbort:function(){},onCanPlay:function(){},onCanPlayThrough:function(){},onEnded:function(){},onError:function(){},onListen:function(){},onPause:function(){},onPlay:function(){},onSeeked:function(){},onVolumeChanged:function(){},onLoadedMetadata:function(){},preload:"metadata",src:null,style:{},title:"",volume:1},s.propTypes={autoPlay:i.default.bool,children:i.default.element,className:i.default.string,controls:i.default.bool,controlsList:i.default.string,crossOrigin:i.default.string,id:i.default.string,listenInterval:i.default.number,loop:i.default.bool,muted:i.default.bool,onAbort:i.default.func,onCanPlay:i.default.func,onCanPlayThrough:i.default.func,onEnded:i.default.func,onError:i.default.func,onListen:i.default.func,onLoadedMetadata:i.default.func,onPause:i.default.func,onPlay:i.default.func,onSeeked:i.default.func,onVolumeChanged:i.default.func,preload:i.default.oneOf(["","none","metadata","auto"]),src:i.default.string,style:i.default.objectOf(i.default.string),title:i.default.string,volume:i.default.number};var c=s;t.default=c;"undefined"!=typeof __REACT_HOT_LOADER__&&(__REACT_HOT_LOADER__.register(s,"ReactAudioPlayer","/Users/justin/Projects/react-audio-player/src/index.jsx"),__REACT_HOT_LOADER__.register(c,"default","/Users/justin/Projects/react-audio-player/src/index.jsx"))},function(e,t){e.exports=__webpack_require__(/*! react */ "./node_modules/_react@15.6.2@react/react.js")},function(e,t){e.exports=__webpack_require__(/*! prop-types */ "./node_modules/_prop-types@15.6.2@prop-types/index.js")}]);\n\n//# sourceURL=webpack:///./node_modules/_react-audio-player@0.11.0@react-audio-player/dist/bundle.js?')},"./src/src/share.js":function(module,exports,__webpack_require__){"use strict";eval('\n\nvar _react = __webpack_require__(/*! react */ "./node_modules/_react@15.6.2@react/react.js");\n\nvar _react2 = _interopRequireDefault(_react);\n\nvar _reactDom = __webpack_require__(/*! react-dom */ "./node_modules/_react-dom@15.6.2@react-dom/index.js");\n\nvar _reactDom2 = _interopRequireDefault(_reactDom);\n\nvar _index = __webpack_require__(/*! ./share/index */ "./src/src/share/index.js");\n\nvar _index2 = _interopRequireDefault(_index);\n\nfunction _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }\n\n_reactDom2.default.render(_react2.default.createElement(_index2.default, null), document.getElementById(\'example\'));\n\n//# sourceURL=webpack:///./src/src/share.js?')},"./src/src/share/index.js":function(module,exports,__webpack_require__){"use strict";eval("\n\nObject.defineProperty(exports, \"__esModule\", {\n    value: true\n});\n\nvar _defineProperty2 = __webpack_require__(/*! babel-runtime/helpers/defineProperty */ \"./node_modules/_babel-runtime@6.26.0@babel-runtime/helpers/defineProperty.js\");\n\nvar _defineProperty3 = _interopRequireDefault(_defineProperty2);\n\nvar _getPrototypeOf = __webpack_require__(/*! babel-runtime/core-js/object/get-prototype-of */ \"./node_modules/_babel-runtime@6.26.0@babel-runtime/core-js/object/get-prototype-of.js\");\n\nvar _getPrototypeOf2 = _interopRequireDefault(_getPrototypeOf);\n\nvar _classCallCheck2 = __webpack_require__(/*! babel-runtime/helpers/classCallCheck */ \"./node_modules/_babel-runtime@6.26.0@babel-runtime/helpers/classCallCheck.js\");\n\nvar _classCallCheck3 = _interopRequireDefault(_classCallCheck2);\n\nvar _createClass2 = __webpack_require__(/*! babel-runtime/helpers/createClass */ \"./node_modules/_babel-runtime@6.26.0@babel-runtime/helpers/createClass.js\");\n\nvar _createClass3 = _interopRequireDefault(_createClass2);\n\nvar _possibleConstructorReturn2 = __webpack_require__(/*! babel-runtime/helpers/possibleConstructorReturn */ \"./node_modules/_babel-runtime@6.26.0@babel-runtime/helpers/possibleConstructorReturn.js\");\n\nvar _possibleConstructorReturn3 = _interopRequireDefault(_possibleConstructorReturn2);\n\nvar _inherits2 = __webpack_require__(/*! babel-runtime/helpers/inherits */ \"./node_modules/_babel-runtime@6.26.0@babel-runtime/helpers/inherits.js\");\n\nvar _inherits3 = _interopRequireDefault(_inherits2);\n\nvar _react = __webpack_require__(/*! react */ \"./node_modules/_react@15.6.2@react/react.js\");\n\nvar _react2 = _interopRequireDefault(_react);\n\nvar _reactDom = __webpack_require__(/*! react-dom */ \"./node_modules/_react-dom@15.6.2@react-dom/index.js\");\n\nvar _reactDom2 = _interopRequireDefault(_reactDom);\n\nvar _swiper = __webpack_require__(/*! swiper/dist/js/swiper.js */ \"./node_modules/_swiper@4.4.6@swiper/dist/js/swiper.js\");\n\nvar _swiper2 = _interopRequireDefault(_swiper);\n\n__webpack_require__(/*! swiper/dist/css/swiper.min.css */ \"./node_modules/_swiper@4.4.6@swiper/dist/css/swiper.min.css\");\n\n__webpack_require__(/*! ../config/index.js */ \"./src/src/config/index.js\");\n\nvar _axios = __webpack_require__(/*! axios */ \"./node_modules/_axios@0.18.0@axios/index.js\");\n\nvar _axios2 = _interopRequireDefault(_axios);\n\nvar _requests = __webpack_require__(/*! ../../js/components/util/requests */ \"./src/js/components/util/requests.js\");\n\n__webpack_require__(/*! ./style/index.less */ \"./src/src/share/style/index.less\");\n\nvar _index = __webpack_require__(/*! ../../js/components/icon/index */ \"./src/js/components/icon/index.js\");\n\nvar _index2 = _interopRequireDefault(_index);\n\nvar _index3 = __webpack_require__(/*! ../../js/components/nav-bar/index */ \"./src/js/components/nav-bar/index.js\");\n\nvar _index4 = _interopRequireDefault(_index3);\n\nvar _index5 = __webpack_require__(/*! ../../js/components/view/index */ \"./src/js/components/view/index.js\");\n\nvar _index6 = _interopRequireDefault(_index5);\n\nvar _index7 = __webpack_require__(/*! ./../../js/components/flex/index */ \"./src/js/components/flex/index.js\");\n\nvar _index8 = _interopRequireDefault(_index7);\n\nvar _index9 = __webpack_require__(/*! ./../../js/components/list/index */ \"./src/js/components/list/index.js\");\n\nvar _index10 = _interopRequireDefault(_index9);\n\nvar _index11 = __webpack_require__(/*! ./../../js/components/button/index */ \"./src/js/components/button/index.js\");\n\nvar _index12 = _interopRequireDefault(_index11);\n\nvar _reactAudioPlayer = __webpack_require__(/*! react-audio-player */ \"./node_modules/_react-audio-player@0.11.0@react-audio-player/dist/bundle.js\");\n\nvar _reactAudioPlayer2 = _interopRequireDefault(_reactAudioPlayer);\n\nfunction _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }\n\nvar Share = function (_React$Component) {\n    (0, _inherits3.default)(Share, _React$Component);\n\n    //定义state\n    function Share(props) {\n        (0, _classCallCheck3.default)(this, Share);\n\n        var _this = (0, _possibleConstructorReturn3.default)(this, (Share.__proto__ || (0, _getPrototypeOf2.default)(Share)).call(this, props));\n\n        _this.state = {\n            data: []\n        };\n        return _this;\n    }\n\n    (0, _createClass3.default)(Share, [{\n        key: 'componentDidMount',\n        value: function componentDidMount() {\n            var _self = this;\n            var requests = (0, _requests.getJson)();\n            var bookid = requests[\"id\"];\n            var token = requests[\"token\"];\n            //获取单元数据\n            _axios2.default.get('../../Subject/Api/getUserBookRecord', {\n                params: {\n                    bookid: bookid,\n                    token: token\n                },\n                headers: {\n                    'Content-Type': 'application/json;charset=UTF-8'\n                }\n            }).then(function (result) {\n                var book = result.data;\n                _self.setState({\n                    data: book\n                });\n\n                mySwiper = new _swiper2.default('.swiper-container', {\n                    effect: 'coverflow',\n                    spaceBetween: 20,\n                    pagination: {\n                        el: '.swiper-pagination',\n                        type: 'fraction'\n                    },\n                    on: {\n                        transitionStart: function transitionStart() {},\n                        transitionEnd: function transitionEnd() {\n                            var contents = _self.state.data[this.activeIndex].contents;\n                            if (contents.length > 0 && contents[0].mp3 != null && contents[0].mp3 != 'null' && contents[0].mp3 != undefined) {\n                                document.getElementsByClassName(\"page\")[0].innerHTML = this.activeIndex + 1 + \"/\" + _self.state.data.length;\n                                document.getElementsByClassName(\"react-audio-player\")[0].pause();\n                                document.getElementsByClassName(\"react-audio-player\")[0].src = _self.state.data[this.activeIndex].contents[0].mp3;\n                                document.getElementsByClassName(\"react-audio-player\")[0].play();\n                            }\n                        },\n                        click: function click(event) {\n                            document.getElementsByClassName(\"react-audio-player\")[0].pause();\n                            var contents = _self.state.data[this.activeIndex].contents;\n                            if (contents.length > 0 && contents[0].mp3 != null && contents[0].mp3 != 'null' && contents[0].mp3 != undefined) {\n                                document.getElementsByClassName(\"react-audio-player\")[0].play();\n                            }\n                        },\n                        init: function init() {\n                            var contents = _self.state.data[0].contents;\n                            if (contents.length > 0 && contents[0].mp3 != null && contents[0].mp3 != 'null' && contents[0].mp3 != undefined) {\n                                document.getElementsByClassName(\"page\")[0].innerHTML = \"1/\" + _self.state.data.length;\n                                document.getElementsByClassName(\"react-audio-player\")[0].src = _self.state.data[0].contents[0].mp3;\n                            }\n                        }\n                    }\n                });\n                //获取用户使用的数据\n            }).catch(function (error) {\n                console.log(\"获取基础列表数据失败\" + error);\n            });\n        }\n    }, {\n        key: 'renderHeader',\n        value: function renderHeader() {\n            var _React$createElement;\n\n            var _self = this;\n            var _props = this.props,\n                rightNavContent = _props.rightNavContent,\n                rightNavEvent = _props.rightNavEvent;\n\n            return _react2.default.createElement(\n                _index4.default,\n                (_React$createElement = {\n                    style: { \"color\": \"white\" },\n                    mode: 'light',\n                    className: 'title'\n                }, (0, _defineProperty3.default)(_React$createElement, 'style', { 'background': '#00bdc7', \"position\": \"fixed\", \"width\": \"100%\", \"zIndex\": \"1500\" }), (0, _defineProperty3.default)(_React$createElement, 'leftContent', [_react2.default.createElement(_index2.default, { type: 'left', size: 'lg', color: '#fff' }), _react2.default.createElement(\n                    'h1',\n                    { className: 'header_left', style: { \"fontSize\": \"18px\", \"color\": \"white\" } },\n                    '\\u8FD4\\u56DE'\n                )]), (0, _defineProperty3.default)(_React$createElement, 'onLeftClick', function onLeftClick() {\n                    window.close();\n                    WeixinJSBridge.call('closeWindow');\n                }), _React$createElement),\n                '\\u5206\\u7EA7\\u9605\\u8BFB'\n            );\n        }\n    }, {\n        key: 'renderBookContent',\n        value: function renderBookContent() {\n            var _self = this;\n            var data = this.state.data;\n            console.log(data);\n            var words = this.state.words;\n            var height = window.innerHeight;\n            return _react2.default.createElement(\n                _index6.default,\n                { className: 'swiper-container' },\n                _react2.default.createElement(\n                    _index6.default,\n                    { className: 'swiper-wrapper' },\n                    data.map(function (item, index) {\n                        return _react2.default.createElement(\n                            _index8.default,\n                            { className: 'swiper-slide', style: { 'minHeight': height, \"overflow\": \"auto\" } },\n                            _react2.default.createElement(\n                                _index8.default.Item,\n                                { className: 'bookcover' },\n                                _react2.default.createElement('img', { className: 'bookcover-image', src: item.pic })\n                            ),\n                            _react2.default.createElement(\n                                _index8.default.Item,\n                                { className: 'bookcontent' },\n                                _react2.default.createElement(\n                                    _index10.default,\n                                    null,\n                                    item.contents.map(function (citem, cindex) {\n                                        if (citem.encontent != \"\" && citem.encontent != \"null\" && citem.encontent != null) {\n                                            return _react2.default.createElement(\n                                                _index10.default.Item,\n                                                { wrap: 'true' },\n                                                citem.encontent,\n                                                _react2.default.createElement(\n                                                    _index10.default.Item.Brief,\n                                                    null,\n                                                    citem.cncontent\n                                                )\n                                            );\n                                        }\n                                    })\n                                )\n                            )\n                        );\n                    })\n                )\n            );\n        }\n    }, {\n        key: 'render',\n        value: function render() {\n            var _self = this;\n            var status = this.state.status;\n            var isover = this.state.isover;\n            return _react2.default.createElement(\n                _index6.default,\n                null,\n                _react2.default.createElement(\n                    _index6.default,\n                    { className: 'wrapper', style: { \"paddingTop\": \"0px\" }, ref: 'booklist' },\n                    _react2.default.createElement('div', { className: 'page', ref: 'page' }),\n                    _self.renderBookContent()\n                ),\n                _react2.default.createElement(\n                    _index6.default,\n                    null,\n                    _react2.default.createElement(\n                        _index12.default,\n                        {\n                            icon: _react2.default.createElement('img', { src: '/public/Subject/images/logo.png', alt: '' }),\n                            style: { 'position': 'absolute', 'bottom': '0', 'width': '100%', 'zIndex': '10000' },\n                            onClick: function onClick() {\n                                window.location.href = \"https://a.app.qq.com/o/simple.jsp?pkgname=com.meijiale.macyandlarry\";\n                            }\n                        },\n                        '\\u4E0B\\u8F7D\\u4F18\\u6559\\u4FE1\\u4F7F'\n                    )\n                ),\n                _react2.default.createElement(_reactAudioPlayer2.default, {\n                    autoplay: 'false',\n                    preload: 'true',\n                    controls: 'false',\n                    style: { \"display\": \"none\" },\n                    onEnded: function onEnded() {\n                        mySwiper.slideNext();\n                    }\n                })\n            );\n        }\n    }]);\n    return Share;\n}(_react2.default.Component);\n\nexports.default = Share;\n\n//# sourceURL=webpack:///./src/src/share/index.js?")},"./src/src/share/style/index.less":function(module,exports,__webpack_require__){eval("// style-loader: Adds some css to the DOM by adding a <style> tag\n\n// load the styles\nvar content = __webpack_require__(/*! !../../../../node_modules/_css-loader@2.0.0@css-loader/dist/cjs.js!../../../../node_modules/_less-loader@4.1.0@less-loader/dist/cjs.js!./index.less */ \"./node_modules/_css-loader@2.0.0@css-loader/dist/cjs.js!./node_modules/_less-loader@4.1.0@less-loader/dist/cjs.js!./src/src/share/style/index.less\");\nif(typeof content === 'string') content = [[module.i, content, '']];\n// add the styles to the DOM\nvar update = __webpack_require__(/*! ../../../../node_modules/_style-loader@0.13.2@style-loader/addStyles.js */ \"./node_modules/_style-loader@0.13.2@style-loader/addStyles.js\")(content, {});\nif(content.locals) module.exports = content.locals;\n// Hot Module Replacement\nif(true) {\n\t// When the styles change, update the <style> tags\n\tif(!content.locals) {\n\t\tmodule.hot.accept(/*! !../../../../node_modules/_css-loader@2.0.0@css-loader/dist/cjs.js!../../../../node_modules/_less-loader@4.1.0@less-loader/dist/cjs.js!./index.less */ \"./node_modules/_css-loader@2.0.0@css-loader/dist/cjs.js!./node_modules/_less-loader@4.1.0@less-loader/dist/cjs.js!./src/src/share/style/index.less\", function() {\n\t\t\tvar newContent = __webpack_require__(/*! !../../../../node_modules/_css-loader@2.0.0@css-loader/dist/cjs.js!../../../../node_modules/_less-loader@4.1.0@less-loader/dist/cjs.js!./index.less */ \"./node_modules/_css-loader@2.0.0@css-loader/dist/cjs.js!./node_modules/_less-loader@4.1.0@less-loader/dist/cjs.js!./src/src/share/style/index.less\");\n\t\t\tif(typeof newContent === 'string') newContent = [[module.i, newContent, '']];\n\t\t\tupdate(newContent);\n\t\t});\n\t}\n\t// When the module is disposed, remove the <style> tags\n\tmodule.hot.dispose(function() { update(); });\n}\n\n//# sourceURL=webpack:///./src/src/share/style/index.less?")}});