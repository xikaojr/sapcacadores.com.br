/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
    config.skin = 'v2';
    config.fullPage = true;
    config.toolbar = 'Itarget';
    config.extraPlugins = 'charcount';
    config.removePlugins = 'elementspath';
    config.toolbar_Itarget =
    [
        { name: 'clipboard', items : ['Undo','Redo'] },
        { name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike' ] },
        { name: 'insert', items : ['SpecialChar', 'CharCount'] }
    ];
};
