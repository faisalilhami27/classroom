/**
 * @license Copyright (c) 2003-2019, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.mathJaxClass = 'math-tex';
	config.extraPlugins = 'mathjax';
	config.filebrowserUploadMethod = 'form';
    // config.toolbar  = [{ name: 'mathjax', items: [ 'mathjax'] }];
    config.mathJaxLib = 'http://cdn.mathjax.org/mathjax/2.6-latest/MathJax.js?config=TeX-AMS_HTML';
};
