﻿/*
 * FCKeditor - The text editor for internet
 * Copyright (C) 2003-2004 Frederico Caldeira Knabben
 * 
 * Licensed under the terms of the GNU Lesser General Public License:
 * 		http://www.opensource.org/licenses/lgpl-license.php
 * 
 * For further information visit:
 * 		http://www.fckeditor.net/
 * 
 * File Name: fck_1_ie.js
 * 	This is the first part of the "FCK" object creation. This is the main
 * 	object that represents an editor instance.
 * 	(IE specific implementations)
 * 
 * Version:  2.0 RC3
 * Modified: 2005-03-02 08:58:17
 * 
 * File Authors:
 * 		Frederico Caldeira Knabben (fredck@fckeditor.net)
 */

FCK.Description = "FCKeditor for Internet Explorer 5.5+" ;

// The behaviors should be pointed using the FullBasePath to avoid security
// errors when using a differente BaseHref.
FCK._BehaviorsStyle = 
	'<style type="text/css" _fcktemp="true"> \
		TABLE	{ behavior: url(' + FCKConfig.FullBasePath + 'css/behaviors/showtableborders.htc) ; } \
		A		{ behavior: url(' + FCKConfig.FullBasePath + 'css/behaviors/anchors.htc) ; } \
		INPUT	{ behavior: url(' + FCKConfig.FullBasePath + 'css/behaviors/hiddenfield.htc) ; } \
	</style>' ;

FCK.InitializeBehaviors = function( dontReturn )
{
	// Set the focus to the editable area when clicking in the document area.
	// TODO: The cursor must be positioned at the end.
	this.EditorDocument.onmousedown = this.EditorDocument.onmouseup = function()
	{
		FCK.Focus() ;

		FCK.EditorWindow.event.cancelBubble	= true ;
		FCK.EditorWindow.event.returnValue	= false ;
	}

	// Intercept pasting operations
	this.EditorDocument.body.onpaste = function()
	{
		if ( FCK.Status == FCK_STATUS_COMPLETE )
			return FCK.Events.FireEvent( "OnPaste" ) ;
		else
			return false ;
	}

	// Disable Right-Click and shows the context menu.
	this.EditorDocument.oncontextmenu = function()
	{
		var e = this.parentWindow.event ;
		FCK.ShowContextMenu( e.screenX, e.screenY ) ;
		return false ;
	}
	// Check if key strokes must be monitored.
	if ( FCKConfig.UseBROnCarriageReturn || FCKConfig.TabSpaces > 0 )
	{
		// Build the "TAB" key replacement.
		if ( FCKConfig.TabSpaces > 0 )
		{
			window.FCKTabHTML = '' ;
			for ( i = 0 ; i < FCKConfig.TabSpaces ; i++ )
				window.FCKTabHTML += "&nbsp;" ;
		}

		this.EditorDocument.onkeydown = function()
		{
			var e = FCK.EditorWindow.event ;

			if ( e.keyCode == 13 && FCKConfig.UseBROnCarriageReturn )	// ENTER
			{
				if ( (e.ctrlKey || e.altKey || e.shiftKey) )
					return true ;
				else
				{
					// We must ignore it if we are inside a List.
					if ( FCK.EditorDocument.queryCommandState( 'InsertOrderedList' ) || FCK.EditorDocument.queryCommandState( 'InsertUnorderedList' ) )
						return true ;

					// Insert the <BR> (The &nbsp; must be also inserted to make it work)
					FCK.InsertHtml("<br>&nbsp;") ;

					// Remove the &nbsp;
					var oRange = FCK.EditorDocument.selection.createRange() ;
					oRange.moveStart('character',-1) ;
					oRange.select() ;
					FCK.EditorDocument.selection.clear() ;

					return false ;
				}
			}
			else if ( e.keyCode == 9 && FCKConfig.TabSpaces > 0 && !(e.ctrlKey || e.altKey || e.shiftKey) )	// TAB
			{
				FCK.InsertHtml( window.FCKTabHTML ) ;
				return false ;
			}

			return true ;
		}
	}

	this.EditorDocument.ondblclick = function()
	{
		FCK.OnDoubleClick( FCK.EditorWindow.event.srcElement ) ;
		FCK.EditorWindow.event.cancelBubble = true ;
	}

	// Catch cursor movements
	this.EditorDocument.onselectionchange = function()
	{
		FCK.Events.FireEvent( "OnSelectionChange" ) ;
	}

	//Enable editing
//	this.EditorDocument.body.contentEditable = true ;
}

FCK.Focus = function()
{
	try
	{
		if ( FCK.EditMode == FCK_EDITMODE_WYSIWYG )
			FCK.EditorDocument.body.focus() ;
		else
			document.getElementById('eSourceField').focus() ;
	}
	catch(e) {}
}

FCK.SetHTML = function( html, forceWYSIWYG )
{
	if ( forceWYSIWYG || FCK.EditMode == FCK_EDITMODE_WYSIWYG )
	{
		// TODO: Wait stable version and remove the following commented lines.
		// In IE, if you do document.body.innerHTML = '<p><hr></p>' it throws a "Unknow runtime error".
		// To solve it we must add a fake (safe) tag before it, and then remove it.
		// this.EditorDocument.body.innerHTML = '<div id="__fakeFCKRemove__">&nbsp;</div>' + html.replace( FCKRegexLib.AposEntity, '&#39;' ) ;
		// this.EditorDocument.getElementById('__fakeFCKRemove__').removeNode(true) ;

		this.EditorDocument.open() ;

		if ( FCKConfig.FullPage )
		{
			var sExtraHtml = 
				FCK._BehaviorsStyle +
				'<link href="' + FCKConfig.FullBasePath + 'css/fck_internal.css' + '" rel="stylesheet" type="text/css" _fcktemp="true" />' ;

			if ( FCK.TempBaseTag.length > 0 && !FCKRegexLib.HasBaseTag.test( html ) )
				sExtraHtml += FCK.TempBaseTag ;

			html = html.replace( FCKRegexLib.HeadCloser, sExtraHtml + '</head>' ) ;

			this.EditorDocument.write( html ) ;
		}
		else
		{
			var sHtml =
				'<html dir="' + FCKConfig.ContentLangDirection + '">' +
				'<head><title></title>' +
				'<link href="' + FCKConfig.EditorAreaCSS + '" rel="stylesheet" type="text/css" />' +
				'<link href="' + FCKConfig.FullBasePath + 'css/fck_internal.css' + '" rel="stylesheet" type="text/css" _fcktemp="true" />' ;

			sHtml += FCK._BehaviorsStyle ;
			sHtml += FCK.TempBaseTag ;
			sHtml += '</head><body>' + html  + '</body></html>' ;
			
			this.EditorDocument.write( sHtml ) ;
		}

		this.EditorDocument.close() ;
		
		this.InitializeBehaviors() ;
		this.EditorDocument.body.contentEditable = true ;

		this.Events.FireEvent( 'OnAfterSetHTML' ) ;

		// TODO: Wait stable version and remove the following commented lines.
//		this.EditorDocument.body.innerHTML = '' ;
//		if ( html && html.length > 0 )
//			this.EditorDocument.write( html ) ;

//		this.EditorDocument.dir = FCKConfig.ContentLangDirection ;
	}
	else
		document.getElementById('eSourceField').value = html ;
}

// TODO: Wait stable version and remove the following commented lines.
/*
FCK.CheckRelativeLinks = function()
{
	// IE automatically change relative URLs to absolute, so we use a trick
	// to solve this problem (the document base points to "fckeditor:".

	for ( var i = 0 ; i < this.EditorDocument.links.length ; i++ )
	{
		var e = this.EditorDocument.links[i] ;

		if ( e.href.startsWith( FCK.BaseUrl ) )
			e.href = e.href.remove( 0, FCK.BaseUrl.length ) ;
	}

	for ( var i = 0 ; i < this.EditorDocument.images.length ; i++ )
	{
		var e = this.EditorDocument.images[i] ;

		if ( e.src.startsWith( FCK.BaseUrl ) )
			e.src = e.src.remove( 0, FCK.BaseUrl.length ) ;
	}
}
*/

FCK.InsertHtml = function( html )
{
	FCK.Focus() ;

	// Gets the actual selection.
	var oSel = FCK.EditorDocument.selection ;

	// Deletes the actual selection contents.
	if ( oSel.type.toLowerCase() != "none" )
		oSel.clear() ;

	// Inset the HTML.
	oSel.createRange().pasteHTML( html ) ;
}

