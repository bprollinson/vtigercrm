{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
-->*}
{strip}
	{assign var="MODULE_NAME" value=$MODULE_MODEL->get('name')}
	<input id="recordId" type="hidden" value="{$RECORD->getId()}" />
	<div class="detailViewContainer">
		<div class="row-fluid detailViewTitle">
			<div class="span10">
				<div class="row-fluid">
					<div class="span5">
						<div class="row-fluid">
							<span class="span0 textOverflowEllipsis" title='{$RECORD->getName()}'>
								{include file="DetailViewHeaderTitle.tpl"|vtemplate_path:$MODULE}
							</span>
							{if $NO_SUMMARY neq true}
								<a class="span0 changeDetailViewMode height20 cursorPointer"><sub>{vtranslate('LBL_COMPLETE_DETAILS',{$MODULE_NAME})}</sub></a>
								{assign var="FULL_MODE_URL" value={$RECORD->getDetailViewUrl()|cat:'&mode=showDetailViewByMode&requestMode=full'} }
								{assign var="SUMMARY_MODE_URL" value={$RECORD->getDetailViewUrl()|cat:'&mode=showDetailViewByMode&requestMode=summary'} }
								<input type="hidden" name="viewMode" value="summary" data-nextviewname="full" data-currentviewlabel="{vtranslate('LBL_SUMMARY_DETAILS',{$MODULE_NAME})}"
									data-summary-url="{$SUMMARY_MODE_URL}" data-full-url="{$FULL_MODE_URL}"  />
							{/if}
						</div>
					</div>

					<div class="span7">
						<div class="pull-right detailViewButtoncontainer">
							<div class="btn-toolbar">
							{foreach item=DEVAIL_VIEW_BASIC_LINK from=$DETAILVIEW_LINKS['DETAILVIEWBASIC']}
							<span class="btn-group">
								<button class="btn"
									{if $DEVAIL_VIEW_BASIC_LINK->isPageLoadLink()}
										onclick="window.location.href='{$DEVAIL_VIEW_BASIC_LINK->getUrl()}'"
									{else}
										onclick={$DEVAIL_VIEW_BASIC_LINK->getUrl()}
									{/if}>
									<strong>{vtranslate($DEVAIL_VIEW_BASIC_LINK->getLabel(), $MODULE_NAME)}</strong>
								</button>
							</span>
							{/foreach}
							{if $DETAILVIEW_LINKS['DETAILVIEW']|@count gt 0}
							<span class="btn-group">
								<button class="btn dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);">
									<strong>{vtranslate('LBL_MORE', $MODULE_NAME)}</strong>&nbsp;&nbsp;<i class="caret"></i>
								</button>
								<ul class="dropdown-menu pull-right">
									{foreach item=DETAIL_VIEW_LINK from=$DETAILVIEW_LINKS['DETAILVIEW']}
									<li>
										<a href={$DETAIL_VIEW_LINK->getUrl()} >{vtranslate($DETAIL_VIEW_LINK->getLabel(), $MODULE_NAME)}</a>
									</li>
									{/foreach}
								</ul>
							</span>
							{/if}
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="span2 detailViewPagingButton">
				<span class="btn-group pull-right">
					<button class="btn" id="detailViewPreviousRecordButton" {if empty($PREVIOUS_RECORD_URL)} disabled="disabled" {else} onclick="window.location.href='{$PREVIOUS_RECORD_URL}'" {/if}><i class="icon-chevron-left"></i></button>
					<button class="btn" id="detailViewNextRecordButton" {if empty($NEXT_RECORD_URL)} disabled="disabled" {else} onclick="window.location.href='{$NEXT_RECORD_URL}'" {/if}><i class="icon-chevron-right"></i></button>
				</span>
			</div>
		</div>
		<div class="detailViewInfo row-fluid">
			<div class="span10 details">
				<form id="detailView" data-name-fields='{ZEND_JSON::encode($MODULE_MODEL->getNameFields())}'>
					<div class="contents">
{/strip}
