/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is not neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/js/forms.js":
/*!*************************!*\
  !*** ./src/js/forms.js ***!
  \*************************/
/*! namespace exports */
/*! export Form [provided] [no usage info] [missing usage info prevents renaming] */
/*! export default [provided] [no usage info] [missing usage info prevents renaming] */
/*! other exports [not provided] [no usage info] */
/*! runtime requirements: __webpack_require__.r, __webpack_exports__, __webpack_require__.d, __webpack_require__.* */
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"default\": () => /* binding */ Form,\n/* harmony export */   \"Form\": () => /* binding */ Form\n/* harmony export */ });\nfunction _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError(\"Cannot call a class as a function\"); } }\n\nfunction _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if (\"value\" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }\n\nfunction _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }\n\nvar Form = /*#__PURE__*/function () {\n  function Form(element) {\n    _classCallCheck(this, Form);\n\n    this.el = element;\n    this.init();\n  }\n\n  _createClass(Form, [{\n    key: \"init\",\n    value: function init() {\n      var _this = this;\n\n      this.el.addEventListener(\"submit\", function (e) {\n        return _this.submit(e);\n      });\n    }\n  }, {\n    key: \"submit\",\n    value: function submit(e) {\n      var _this2 = this;\n\n      e.preventDefault();\n\n      if (!this.el.checkValidity()) {\n        alert(\"Veuillez compléter tous les champs requis !\");\n        return false;\n      }\n\n      if (window.recaptcha_v3) {\n        grecaptcha.ready(function () {\n          grecaptcha.execute(window.recaptcha_v3, {\n            action: \"submit\"\n          }).then(function (token) {\n            _this2.send(token);\n          });\n        });\n      } else {\n        this.send();\n      }\n    }\n  }, {\n    key: \"send\",\n    value: function send(token) {\n      var _this3 = this;\n\n      var div = document.createElement(\"div\");\n      div.classList.add(\"js-extra-form-data\");\n      var additions = {\n        action: \"st_post_contact\",\n        \"Page actuelle\": location.href,\n        \"Page précédente\": document.referrer,\n        _subject: this.el.dataset.subject || \"Formulaire de contact\",\n        _title_field: this.el.dataset.title || this.el.querySelectorAll(\"input:first-child\").getAttribute(\"name\"),\n        _category: this.el.dataset.category || \"Contact\",\n        _nobot: \"1\"\n      };\n      if (token) additions[\"_token\"] = token;\n\n      for (var name in additions) {\n        var input = document.createElement(\"input\");\n        input.setAttribute(\"type\", \"hidden\");\n        input.setAttribute(\"name\", name);\n        input.setAttribute(\"value\", additions[name]);\n        div.appendChild(input);\n      }\n\n      var callback = this.el.dataset.callback;\n\n      if (callback && window[callback]) {\n        try {\n          window[callback](this);\n        } catch (error) {// skip\n        }\n      }\n\n      var extras = this.el.querySelectorAll(\".js-extra-form-data\");\n\n      if ($extras) {\n        for (i = 0; i < extras.length; ++i) {\n          extras[i].parentNode.removeChild(e);\n        }\n      }\n\n      this.el.appendChild(div);\n      var formData = new FormData(this.el);\n      fetch(stereo_cf.ajax_url, {\n        method: \"POST\",\n        credentials: \"same-origin\",\n        body: formData\n      }).then(function (response) {\n        _this3.el.reset();\n\n        if (_this3.el.dataset.redirect) {\n          window.location.href = _this3.el.dataset.redirect;\n        } else {\n          _this3.el.classList.remove(\"is-submitting\");\n\n          _this3.el.classList.add(\"is-submitted\");\n\n          _this3.el.nextElementSibling.style.display = \"block\";\n        }\n\n        return response;\n      })[\"catch\"](function (error) {\n        console.log(\"error\", error);\n\n        _this3.el.classList.remove(\"is-submitting\");\n\n        _this3.el.style.display = \"block\";\n        alert(\"Une erreur est survenue, veuillez réessayer!\");\n      });\n      this.el.classList.add(\"is-submitting\");\n\n      if (this.el.getAttribute(\"data-reset-only\")) {\n        this.el.reset();\n      } else {\n        this.el.style.display = \"none\";\n      }\n\n      extras = this.el.querySelectorAll(\".js-extra-form-data\");\n\n      if ($extras) {\n        for (i = 0; i < extras.length; ++i) {\n          extras[i].parentNode.removeChild(e);\n        }\n      }\n    }\n  }]);\n\n  return Form;\n}();\n\n\n\n\n//# sourceURL=webpack://stereo-contactform/./src/js/forms.js?");

/***/ }),

/***/ "./src/js/index.js":
/*!*************************!*\
  !*** ./src/js/index.js ***!
  \*************************/
/*! namespace exports */
/*! exports [not provided] [no usage info] */
/*! runtime requirements: __webpack_require__, __webpack_require__.r, __webpack_exports__, __webpack_require__.* */
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _forms__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./forms */ \"./src/js/forms.js\");\n\n\nwindow.initStereoForm = function () {\n  var forms = document.querySelectorAll('.js-stereo-cf');\n  forms.forEach(function (e) {\n    new _forms__WEBPACK_IMPORTED_MODULE_0__.default(e);\n  });\n};\n\nwindow.initStereoForm();\n\n//# sourceURL=webpack://stereo-contactform/./src/js/index.js?");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		if(__webpack_module_cache__[moduleId]) {
/******/ 			return __webpack_module_cache__[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => Object.prototype.hasOwnProperty.call(obj, prop)
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	// startup
/******/ 	// Load entry module
/******/ 	__webpack_require__("./src/js/index.js");
/******/ 	// This entry module used 'exports' so it can't be inlined
/******/ })()
;