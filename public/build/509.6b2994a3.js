(self.webpackChunkkoillection=self.webpackChunkkoillection||[]).push([[509],{71509:(e,t,r)=>{"use strict";r.r(t),r.d(t,{default:()=>v});r(92222),r(24812),r(69070),r(68304),r(30489),r(12419),r(78011),r(82526),r(41817),r(41539),r(32165),r(66992),r(78783),r(33948);var n=r(67931),o=r(35099),i=r.n(o);function u(e){return(u="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}function a(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function c(e,t){for(var r=0;r<t.length;r++){var n=t[r];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}function l(e,t){return(l=Object.setPrototypeOf||function(e,t){return e.__proto__=t,e})(e,t)}function f(e){var t=function(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Boolean.prototype.valueOf.call(Reflect.construct(Boolean,[],(function(){}))),!0}catch(e){return!1}}();return function(){var r,n=h(e);if(t){var o=h(this).constructor;r=Reflect.construct(n,arguments,o)}else r=n.apply(this,arguments);return s(this,r)}}function s(e,t){return!t||"object"!==u(t)&&"function"!=typeof t?p(e):t}function p(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}function h(e){return(h=Object.setPrototypeOf?Object.getPrototypeOf:function(e){return e.__proto__||Object.getPrototypeOf(e)})(e)}function y(e,t,r){return t in e?Object.defineProperty(e,t,{value:r,enumerable:!0,configurable:!0,writable:!0}):e[t]=r,e}var v=function(e){!function(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function");e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,writable:!0,configurable:!0}}),t&&l(e,t)}(u,e);var t,r,n,o=f(u);function u(){var e;a(this,u);for(var t=arguments.length,r=new Array(t),n=0;n<t;n++)r[n]=arguments[n];return y(p(e=o.call.apply(o,[this].concat(r))),"croppie",null),e}return t=u,(r=[{key:"connect",value:function(){var e=this;this.croppie=new(i())(this.areaTarget,{viewport:{width:150,height:150,type:"circle"},boundary:{width:200,height:200},showZoomer:!1,update:function(){e.areaTarget.dispatchEvent(new Event("mouseup"))}}),this.element.querySelector(".cr-image").alt="",this.element.querySelector(".cr-vp-circle").classList.add("fa","fa-plus","fa-fw")}},{key:"loadImage",value:function(e){this.readFile(),this.areaTarget.dispatchEvent(new Event("mouseup"))}},{key:"refreshImage",value:function(e){if(""!==e.target.value){var t=this.element.querySelector(".file-input"),r=this;this.croppie.result({type:"canvas",size:{width:200,height:200}}).then((function(e){t.value=e,r.previewTarget.innerHTML='<img src="'+e+'">'}))}}},{key:"readFile",value:function(){var e=this;if(this.inputTarget.files&&this.inputTarget.files[0]){var t=new FileReader;t.onload=function(t){e.croppie.bind({url:t.target.result})},t.readAsDataURL(this.inputTarget.files[0])}}}])&&c(t.prototype,r),n&&c(t,n),u}(n.Controller);y(v,"targets",["area","input","preview"])}}]);