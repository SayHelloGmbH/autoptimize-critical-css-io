!function(e){var a={};function t(n){if(a[n])return a[n].exports;var r=a[n]={i:n,l:!1,exports:{}};return e[n].call(r.exports,r,r.exports,t),r.l=!0,r.exports}t.m=e,t.c=a,t.d=function(e,a,n){t.o(e,a)||Object.defineProperty(e,a,{enumerable:!0,get:n})},t.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},t.t=function(e,a){if(1&a&&(e=t(e)),8&a)return e;if(4&a&&"object"==typeof e&&e&&e.__esModule)return e;var n=Object.create(null);if(t.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:e}),2&a&&"string"!=typeof e)for(var r in e)t.d(n,r,function(a){return e[a]}.bind(null,r));return n},t.n=function(e){var a=e&&e.__esModule?function(){return e.default}:function(){return e};return t.d(a,"a",a),a},t.o=function(e,a){return Object.prototype.hasOwnProperty.call(e,a)},t.p="",t(t.s=0)}([function(e,a){var t,n;t=jQuery,n=window.AoCriticalCSSVars,t((function(){var e=t(".aoccssio-generate");if(e.length){t("body #wpcontent").append('<div class="aoccssio-loader"></div>');var a=t(".aoccssio-loader"),r=t('<img src="./images/spinner.gif" />'),o=t('<img src="./images/loading.gif" style="margin-right: 5px;" />');e.each((function(){var e=t(this),i=e.find('[name="aoccssio_url"]'),s=e.find(".aoccssio-generate__regenerate"),c=e.find(".aoccssio-generate__delete"),d=e.find(".aoccssio-generate__controls"),u=e.find(".aoccssio-generate__status"),l=function(e){e?(i.attr("disabled",!0),c.add(s).add(u).hide(),d.append(r),o.insertBefore(u)):(i.attr("disabled",!1),c.add(s).add(u).show(),r.add(o).remove())};s.on("click",(function(){if(!function(e){return new RegExp("^(https?:\\/\\/)?((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.?)+[a-z]{2,}|((\\d{1,3}\\.){3}\\d{1,3}))(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*(\\?[;&a-z\\d%_.~+=-]*)?(\\#[-a-z\\d_]*)?$","i").test(e)}(i.val()))return i.addClass("aoccssio-generate__input--error-pop"),setTimeout((function(){i.removeClass("aoccssio-generate__input--error-pop")}),200),!1;l(!0);var r=[];e.find("input, textarea, select").each((function(){r.push(t(this).attr("data-aoccssio-name")+"="+t(this).val())}));var o=r.join("&");a.fadeIn(),t.ajax({url:n.AjaxURL,type:"POST",dataType:"json",data:o}).done((function(a){if(l(!1),null===a.type||"success"!==a.type){var t=a.message;""!==t&&void 0!==t||(t="error"),alert(t)}else u.text(a.add.datetime),e.removeClass("aoccssio-generate--nofile")}))})),c.on("click",(function(){l(!0);var a=[];a.push("action="+e.find("input[name=criticalapi_action_delete]").val()),a.push("critical_key="+e.find("input[name=criticalapi_key]").val());var n=a.join("&");e.removeClass("aoccssio-generate--file"),e.addClass("aoccssio-generate--nofile"),t.ajax({url:vars.AjaxURL,type:"POST",dataType:"json",data:n}).done((function(e){if(l(!1),null===e.type||"success"!==e.type){var a=e.message;""!==a&&void 0!==a||(a="error"),alert(a)}}))}))}))}}))}]);