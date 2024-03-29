/**
* DO NOT EDIT THIS FILE.
* See the following change record for more information,
* https://www.drupal.org/node/2815083
* @preserve
**/
"use strict";

function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { _defineProperty(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _defineProperty(obj, key, value) { key = _toPropertyKey(key); if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : String(i); }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter); }
function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }
var _wp = wp,
  blocks = _wp.blocks,
  data = _wp.data,
  element = _wp.element,
  components = _wp.components,
  editor = _wp.editor,
  blockEditor = _wp.blockEditor;
var registerBlockType = blocks.registerBlockType;
var dispatch = data.dispatch,
  select = data.select,
  useSelect = data.useSelect;
var Fragment = element.Fragment;
var Toolbar = components.Toolbar,
  Placeholder = components.Placeholder,
  ToggleControl = components.ToggleControl,
  PanelBody = components.PanelBody,
  TextareaControl = components.TextareaControl;
var RichText = blockEditor.RichText,
  InspectorControls = blockEditor.InspectorControls,
  BlockIcon = blockEditor.BlockIcon,
  TextControl = blockEditor.TextControl,
  InnerBlocks = blockEditor.InnerBlocks;
var __ = Drupal.t;
var alert = {
  title: __('Alert'),
  description: __('Alert with theme variants'),
  icon: 'list-view',
  attributes: {
    alertType: {
      type: "string",
      default: 'info'
    },
    showIcon: {
      type: 'boolean',
      default: true
    },
    showSlim: {
      type: 'boolean',
      default: false
    },
    heading: {
      type: 'string',
      default: ''
    }
  },
  edit: function edit(_ref) {
    var attributes = _ref.attributes,
      className = _ref.className,
      setAttributes = _ref.setAttributes,
      isSelected = _ref.isSelected,
      clientId = _ref.clientId;
    var alertType = attributes.alertType,
      showSlim = attributes.showSlim,
      heading = attributes.heading,
      showIcon = attributes.showIcon;
    var isParentOfSelectedBlock = useSelect(function (select) {
      return select('core/block-editor').hasSelectedInnerBlock(clientId, true);
    });
    var isSelectedOrChild = isSelected || isParentOfSelectedBlock;
    var descriptionClass = ' usa-alert usa-alert--' + alertType;
    var bodyClass = 'usa-alert__body';
    if (showIcon == false) {
      descriptionClass = descriptionClass + ' usa-alert--no-icon';
    }
    if (showSlim == true) {
      descriptionClass = descriptionClass + ' usa-alert--slim';
    }
    return React.createElement(Fragment, null, React.createElement("div", {
      className: className + descriptionClass
    }, React.createElement(Fragment, null, React.createElement("div", {
      className: bodyClass
    }, React.createElement(RichText, {
      tagName: "h4",
      className: "usa-alert__heading",
      value: heading,
      onChange: function onChange(heading) {
        return setAttributes({
          heading: heading
        });
      },
      placeholder: __('Write a heading...'),
      keepPlaceholderOnFocus: true,
      withoutInteractiveFormatting: true,
      allowedFormats: [],
      "aria-expanded": isSelectedOrChild
    }), React.createElement("p", {
      class: "usa-alert__text"
    }, React.createElement(InnerBlocks, null)))), React.createElement(InspectorControls, null, React.createElement(Fragment, null, React.createElement(PanelBody, {
      title: __('Layout')
    }, React.createElement(SelectControl, {
      label: __('Alert Type'),
      value: alertType,
      options: [{
        label: 'Informational',
        value: 'info'
      }, {
        label: 'Error',
        value: 'error'
      }, {
        label: 'Warning',
        value: 'warning'
      }, {
        label: 'Success',
        value: 'success'
      }, {
        label: 'Emergency',
        value: 'emergency'
      }],
      onChange: function onChange(nextLayout) {
        setAttributes({
          alertType: nextLayout
        });
      }
    })))), React.createElement(InspectorControls, null, React.createElement(Fragment, null, React.createElement(PanelBody, {
      title: __('Options')
    }, React.createElement(ToggleControl, {
      label: __('Show Icon'),
      checked: showIcon,
      onChange: function onChange() {
        return setAttributes({
          showIcon: !showIcon
        });
      }
    }), React.createElement(ToggleControl, {
      label: __('Slim'),
      checked: showSlim,
      onChange: function onChange() {
        return setAttributes({
          showSlim: !showSlim
        });
      }
    }))))));
  },
  save: function save(_ref2) {
    var className = _ref2.className,
      attributes = _ref2.attributes;
    var alertType = attributes.alertType,
      showIcon = attributes.showIcon,
      showSlim = attributes.showSlim,
      heading = attributes.heading;
    return React.createElement(InnerBlocks.Content, null);
  }
};
var category = {
  slug: 'uswds',
  title: __('USWDS')
};
var currentCategories = select('core/blocks').getCategories().filter(function (item) {
  return item.slug !== category.slug;
});
dispatch('core/blocks').setCategories([category].concat(_toConsumableArray(currentCategories)));
registerBlockType("".concat(category.slug, "/alert"), _objectSpread({
  category: category.slug
}, alert));