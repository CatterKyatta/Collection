"use strict";(self.webpackChunkkoillection=self.webpackChunkkoillection||[]).push([[746],{76746:(t,e,n)=>{n.r(e),n.d(e,{default:()=>h});n(73210),n(68304),n(30489),n(12419),n(78011),n(69070),n(82526),n(41817),n(41539),n(32165),n(66992),n(78783),n(33948);var o=n(67931),r=n(65179),c=n.n(r),l=n(80873);function u(t){return(u="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t})(t)}function i(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}function a(t,e){for(var n=0;n<e.length;n++){var o=e[n];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(t,o.key,o)}}function s(t,e){return(s=Object.setPrototypeOf||function(t,e){return t.__proto__=e,t})(t,e)}function f(t){var e=function(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Boolean.prototype.valueOf.call(Reflect.construct(Boolean,[],(function(){}))),!0}catch(t){return!1}}();return function(){var n,o=y(t);if(e){var r=y(this).constructor;n=Reflect.construct(o,arguments,r)}else n=o.apply(this,arguments);return p(this,n)}}function p(t,e){if(e&&("object"===u(e)||"function"==typeof e))return e;if(void 0!==e)throw new TypeError("Derived constructors may only return object or undefined");return function(t){if(void 0===t)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return t}(t)}function y(t){return(y=Object.setPrototypeOf?Object.getPrototypeOf:function(t){return t.__proto__||Object.getPrototypeOf(t)})(t)}var h=function(t){!function(t,e){if("function"!=typeof e&&null!==e)throw new TypeError("Super expression must either be null or a function");t.prototype=Object.create(e&&e.prototype,{constructor:{value:t,writable:!0,configurable:!0}}),e&&s(t,e)}(u,t);var e,n,o,r=f(u);function u(){return i(this,u),r.apply(this,arguments)}return e=u,(n=[{key:"connect",value:function(){var t=this;new l.TsSelect2(this.element,{templateSelection:function(e){return e.id?t.htmlToElement('<div><span class="tag-category-select-option tag-category-color" style="background-color: '+e.element.dataset.color+'"></span><span>'+e.text+"</span></div>"):""},templateResult:function(e){return e.id?t.htmlToElement('<div><span class="tag-category-select-option tag-category-color" style="background-color: '+e.element.dataset.color+'"></span><span>'+e.text+"</span></div>"):t.htmlToElement('<span class="select-placeholder">'+c().trans("select2.none")+"</span>")},language:{noResults:function(){return c().trans("select2.no_results")},searching:function(){return c().trans("select2.searching")}}})}},{key:"htmlToElement",value:function(t){var e=document.createElement("template");return t=t.trim(),e.innerHTML=t,e.content.firstChild}}])&&a(e.prototype,n),o&&a(e,o),u}(o.Controller)}}]);