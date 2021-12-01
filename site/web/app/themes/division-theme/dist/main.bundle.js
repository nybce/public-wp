/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/index.js":
/*!**********************!*\
  !*** ./src/index.js ***!
  \**********************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
eval("\n\n__webpack_require__(/*! ./sass/style.scss */ \"./src/sass/style.scss\");\n\n__webpack_require__(/*! ./js/navigation */ \"./src/js/navigation.js\");\n\n//# sourceURL=webpack:///./src/index.js?");

/***/ }),

/***/ "./src/js/navigation.js":
/*!******************************!*\
  !*** ./src/js/navigation.js ***!
  \******************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
eval("\n\n/**\n * File navigation.js.\n *\n * Handles toggling the navigation menu for small screens and enables TAB key\n * navigation support for dropdown menus.\n */\n\n(function () {\n\tvar siteNavigation = document.getElementById('site-navigation');\n\n\t// Return early if the navigation don't exist.\n\tif (!siteNavigation) {\n\t\treturn;\n\t}\n\n\tvar button = siteNavigation.getElementsByTagName('button')[0];\n\n\t// Return early if the button don't exist.\n\tif ('undefined' === typeof button) {\n\t\treturn;\n\t}\n\n\tvar menu = siteNavigation.getElementsByTagName('ul')[0];\n\n\t// Hide menu toggle button if menu is empty and return early.\n\tif ('undefined' === typeof menu) {\n\t\tbutton.style.display = 'none';\n\t\treturn;\n\t}\n\n\tif (!menu.classList.contains('nav-menu')) {\n\t\tmenu.classList.add('nav-menu');\n\t}\n\n\t// Toggle the .toggled class and the aria-expanded value each time the button is clicked.\n\tbutton.addEventListener('click', function () {\n\t\tsiteNavigation.classList.toggle('toggled');\n\n\t\tif (button.getAttribute('aria-expanded') === 'true') {\n\t\t\tbutton.setAttribute('aria-expanded', 'false');\n\t\t} else {\n\t\t\tbutton.setAttribute('aria-expanded', 'true');\n\t\t}\n\t});\n\n\t// Remove the .toggled class and set aria-expanded to false when the user clicks outside the navigation.\n\tdocument.addEventListener('click', function (event) {\n\t\tvar isClickInside = siteNavigation.contains(event.target);\n\n\t\tif (!isClickInside) {\n\t\t\tsiteNavigation.classList.remove('toggled');\n\t\t\tbutton.setAttribute('aria-expanded', 'false');\n\t\t}\n\t});\n\n\t// Get all the link elements within the menu.\n\tvar links = menu.getElementsByTagName('a');\n\n\t// Get all the link elements with children within the menu.\n\tvar linksWithChildren = menu.querySelectorAll('.menu-item-has-children > a, .page_item_has_children > a');\n\n\t// Toggle focus each time a menu link is focused or blurred.\n\tvar _iteratorNormalCompletion = true;\n\tvar _didIteratorError = false;\n\tvar _iteratorError = undefined;\n\n\ttry {\n\t\tfor (var _iterator = links[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {\n\t\t\tvar link = _step.value;\n\n\t\t\tlink.addEventListener('focus', toggleFocus, true);\n\t\t\tlink.addEventListener('blur', toggleFocus, true);\n\t\t}\n\n\t\t// Toggle focus each time a menu link with children receive a touch event.\n\t} catch (err) {\n\t\t_didIteratorError = true;\n\t\t_iteratorError = err;\n\t} finally {\n\t\ttry {\n\t\t\tif (!_iteratorNormalCompletion && _iterator.return) {\n\t\t\t\t_iterator.return();\n\t\t\t}\n\t\t} finally {\n\t\t\tif (_didIteratorError) {\n\t\t\t\tthrow _iteratorError;\n\t\t\t}\n\t\t}\n\t}\n\n\tvar _iteratorNormalCompletion2 = true;\n\tvar _didIteratorError2 = false;\n\tvar _iteratorError2 = undefined;\n\n\ttry {\n\t\tfor (var _iterator2 = linksWithChildren[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {\n\t\t\tvar _link = _step2.value;\n\n\t\t\t_link.addEventListener('touchstart', toggleFocus, false);\n\t\t}\n\n\t\t/**\n   * Sets or removes .focus class on an element.\n   */\n\t} catch (err) {\n\t\t_didIteratorError2 = true;\n\t\t_iteratorError2 = err;\n\t} finally {\n\t\ttry {\n\t\t\tif (!_iteratorNormalCompletion2 && _iterator2.return) {\n\t\t\t\t_iterator2.return();\n\t\t\t}\n\t\t} finally {\n\t\t\tif (_didIteratorError2) {\n\t\t\t\tthrow _iteratorError2;\n\t\t\t}\n\t\t}\n\t}\n\n\tfunction toggleFocus() {\n\t\tif (event.type === 'focus' || event.type === 'blur') {\n\t\t\tvar self = this;\n\t\t\t// Move up through the ancestors of the current link until we hit .nav-menu.\n\t\t\twhile (!self.classList.contains('nav-menu')) {\n\t\t\t\t// On li elements toggle the class .focus.\n\t\t\t\tif ('li' === self.tagName.toLowerCase()) {\n\t\t\t\t\tself.classList.toggle('focus');\n\t\t\t\t}\n\t\t\t\tself = self.parentNode;\n\t\t\t}\n\t\t}\n\n\t\tif (event.type === 'touchstart') {\n\t\t\tvar menuItem = this.parentNode;\n\t\t\tevent.preventDefault();\n\t\t\tvar _iteratorNormalCompletion3 = true;\n\t\t\tvar _didIteratorError3 = false;\n\t\t\tvar _iteratorError3 = undefined;\n\n\t\t\ttry {\n\t\t\t\tfor (var _iterator3 = menuItem.parentNode.children[Symbol.iterator](), _step3; !(_iteratorNormalCompletion3 = (_step3 = _iterator3.next()).done); _iteratorNormalCompletion3 = true) {\n\t\t\t\t\tvar _link2 = _step3.value;\n\n\t\t\t\t\tif (menuItem !== _link2) {\n\t\t\t\t\t\t_link2.classList.remove('focus');\n\t\t\t\t\t}\n\t\t\t\t}\n\t\t\t} catch (err) {\n\t\t\t\t_didIteratorError3 = true;\n\t\t\t\t_iteratorError3 = err;\n\t\t\t} finally {\n\t\t\t\ttry {\n\t\t\t\t\tif (!_iteratorNormalCompletion3 && _iterator3.return) {\n\t\t\t\t\t\t_iterator3.return();\n\t\t\t\t\t}\n\t\t\t\t} finally {\n\t\t\t\t\tif (_didIteratorError3) {\n\t\t\t\t\t\tthrow _iteratorError3;\n\t\t\t\t\t}\n\t\t\t\t}\n\t\t\t}\n\n\t\t\tmenuItem.classList.toggle('focus');\n\t\t}\n\t}\n})();\n\n//# sourceURL=webpack:///./src/js/navigation.js?");

/***/ }),

/***/ "./src/sass/style.scss":
/*!*****************************!*\
  !*** ./src/sass/style.scss ***!
  \*****************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("// removed by extract-text-webpack-plugin\n\n//# sourceURL=webpack:///./src/sass/style.scss?");

/***/ }),

/***/ 0:
/*!****************************!*\
  !*** multi ./src/index.js ***!
  \****************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("module.exports = __webpack_require__(/*! /theme/src/index.js */\"./src/index.js\");\n\n\n//# sourceURL=webpack:///multi_./src/index.js?");

/***/ })

/******/ });