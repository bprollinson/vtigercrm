﻿/*
 * FCKeditor - The text editor for internet
 * Copyright (C) 2003-2005 Frederico Caldeira Knabben
 * 
 * Licensed under the terms of the GNU Lesser General Public License:
 * 		http://www.opensource.org/licenses/lgpl-license.php
 * 
 * For further information visit:
 * 		http://www.fckeditor.net/
 * 
 * "Support Open Source software. What about a donation today?"
 * 
 * File Name: ja.js
 * 	Japanese language file.
 * 
 * File Authors:
 * 		Takashi Yamaguchi (jack@omakase.net)
 */

var FCKLang =
{
// Language direction : "ltr" (left to right) or "rtl" (right to left).
Dir					: "ltr",

ToolbarCollapse		: "ツール�?ーを隠�?�",
ToolbarExpand		: "ツール�?ーを表示",

// Toolbar Items and Context Menu
Save				: "�?存",
NewPage				: "新�?��?�ページ",
Preview				: "プレビュー",
Cut					: "切り�?�り",
Copy				: "コピー",
Paste				: "貼り付�?�",
PasteText			: "プレーンテキスト貼り付�?�",
PasteWord			: "ワード文章�?�ら貼り付�?�",
Print				: "�?�刷",
SelectAll			: "�?��?��?��?�択",
RemoveFormat		: "フォーマット削除",
InsertLinkLbl		: "リンク",
InsertLink			: "リンク挿入/編集",
RemoveLink			: "リンク削除",
Anchor				: "アンカー挿入/編集",
InsertImageLbl		: "イメージ",
InsertImage			: "イメージ挿入/編集",
InsertFlashLbl		: "Flash",
InsertFlash			: "Flash挿入/編集",
InsertTableLbl		: "テーブル",
InsertTable			: "テーブル挿入/編集",
InsertLineLbl		: "ライン",
InsertLine			: "横罫線",
InsertSpecialCharLbl: "特殊文字",
InsertSpecialChar	: "特殊文字挿入",
InsertSmileyLbl		: "絵文字",
InsertSmiley		: "絵文字挿入",
About				: "FCKeditorヘルプ",
Bold				: "太字",
Italic				: "斜体",
Underline			: "下線",
StrikeThrough		: "打�?�消�?�線",
Subscript			: "添�?�字",
Superscript			: "上付�??文字",
LeftJustify			: "左�?��?�",
CenterJustify		: "中央�?��?�",
RightJustify		: "�?��?��?�",
BlockJustify		: "両端�?��?�",
DecreaseIndent		: "インデント解除",
IncreaseIndent		: "インデント",
Undo				: "元�?�戻�?�",
Redo				: "やり直�?�",
NumberedListLbl		: "段�?�番�?�",
NumberedList		: "段�?�番�?��?�追加/削除",
BulletedListLbl		: "箇�?�書�??",
BulletedList		: "箇�?�書�??�?�追加/削除",
ShowTableBorders	: "テーブルボーダー表示",
ShowDetails			: "詳細表示",
Style				: "スタイル",
FontFormat			: "フォーマット",
Font				: "フォント",
FontSize			: "サイズ",
TextColor			: "テキスト色",
BGColor				: "背景色",
Source				: "ソース",
Find				: "検索",
Replace				: "置�??�?��?�",
SpellCheck			: "スペル�?ェック",
UniversalKeyboard	: "ユニ�?ーサル・キーボード",
PageBreakLbl		: "改ページ",
PageBreak			: "改ページ挿入",

Form			: "フォーム",
Checkbox		: "�?ェックボックス",
RadioButton		: "ラジオボタン",
TextField		: "１行テキスト",
Textarea		: "テキストエリア",
HiddenField		: "�?�?�視フィールド",
Button			: "ボタン",
SelectionField	: "�?�択フィールド",
ImageButton		: "画�?ボタン",

// Context Menu
EditLink			: "リンク編集",
InsertRow			: "行挿入",
DeleteRows			: "行削除",
InsertColumn		: "列挿入",
DeleteColumns		: "列削除",
InsertCell			: "セル挿入",
DeleteCells			: "セル削除",
MergeCells			: "セル�?�?�",
SplitCell			: "セル分割",
TableDelete			: "テーブル削除",
CellProperties		: "セル プロパティ",
TableProperties		: "テーブル プロパティ",
ImageProperties		: "イメージ プロパティ",
FlashProperties		: "Flash プロパティ",

AnchorProp			: "アンカー プロパティ",
ButtonProp			: "ボタン プロパティ",
CheckboxProp		: "�?ェックボックス プロパティ",
HiddenFieldProp		: "�?�?�視フィールド プロパティ",
RadioButtonProp		: "ラジオボタン プロパティ",
ImageButtonProp		: "画�?ボタン プロパティ",
TextFieldProp		: "１行テキスト プロパティ",
SelectionFieldProp	: "�?�択フィールド プロパティ",
TextareaProp		: "テキストエリア プロパティ",
FormProp			: "フォーム プロパティ",

FontFormats			: "Normal;Formatted;Address;Heading 1;Heading 2;Heading 3;Heading 4;Heading 5;Heading 6;Paragraph (DIV)",

// Alerts and Messages
ProcessingXHTML		: "XHTML処�?�中. �?��?�ら�??�?�待�?��??�?��?��?�...",
Done				: "完了",
PasteWordConfirm	: "貼り付�?�を行�?�テキスト�?��?ワード文章�?�らコピー�?�れよ�?��?��?��?��?��?��?�。貼り付�?�る�?�?�クリーニングを行�?��?��?��?�？",
NotCompatiblePaste	: "�?��?�コマンド�?�インター�?ット・エクスプローラー�?ージョン5.5以上�?�利用�?�能�?��?�。クリーニング�?��?��?��?�貼り付�?�を行�?��?��?��?�？",
UnknownToolbarItem	: "未知�?�ツール�?ー項目 \"%1\"",
UnknownCommand		: "未知�?�コマンド�?? \"%1\"",
NotImplemented		: "コマンド�?�インプリメント�?�れ�?��?�ん�?��?��?�。",
UnknownToolbarSet	: "ツール�?ー設定 \"%1\" 存在�?��?��?�ん。",
NoActiveX			: "エラー�?警告メッセージ�?��?��?�発生�?��?�場�?��?ブラウザー�?�セキュリティ設定�?�よりエディタ�?��?��??�?��?��?�機能�?�制�?�?�れ�?��?�る�?�能性�?��?�り�?��?�。セキュリティ設定�?�オプション�?�\"ActiveXコントロール�?�プラグイン�?�実行\"を有効�?��?�る�?��?��?��??�?��?��?�。",
BrowseServerBlocked : "サー�?ーブラウザーを開�??�?��?��?��?��??�?��?�ん�?��?��?�。�?ップアップ・ブロック機能�?�無効�?��?��?��?��?�る�?�確�?�?��?��??�?��?��?�。",
DialogBlocked		: "ダイアログウィンドウを開�??�?��?��?��?��??�?��?�ん�?��?��?�。�?ップアップ・ブロック機能�?�無効�?��?��?��?��?�る�?�確�?�?��?��??�?��?��?�。",

// Dialogs
DlgBtnOK			: "OK",
DlgBtnCancel		: "キャンセル",
DlgBtnClose			: "閉�?�る",
DlgBtnBrowseServer	: "サー�?ーブラウザー",
DlgAdvancedTag		: "高度�?�設定",
DlgOpOther			: "&lt;�??�?�他&gt;",
DlgInfoTab			: "情報",
DlgAlertUrl			: "URLを挿入�?��?��??�?��?��?�",

// General Dialogs Labels
DlgGenNotSet		: "&lt;�?��?�&gt;",
DlgGenId			: "Id",
DlgGenLangDir		: "文字表記�?�方�?�",
DlgGenLangDirLtr	: "左�?�ら�?� (LTR)",
DlgGenLangDirRtl	: "�?��?�ら左 (RTL)",
DlgGenLangCode		: "言語コード",
DlgGenAccessKey		: "アクセスキー",
DlgGenName			: "Name属性",
DlgGenTabIndex		: "タブインデックス",
DlgGenLongDescr		: "longdesc属性(長文説明)",
DlgGenClass			: "スタイルシートクラス",
DlgGenTitle			: "Title属性",
DlgGenContType		: "Content Type属性",
DlgGenLinkCharset	: "リンクcharset属性",
DlgGenStyle			: "スタイルシート",

// Image Dialog
DlgImgTitle			: "イメージ プロパティ",
DlgImgInfoTab		: "イメージ 情報",
DlgImgBtnUpload		: "サー�?ー�?��?信",
DlgImgURL			: "URL",
DlgImgUpload		: "アップロード",
DlgImgAlt			: "代替テキスト",
DlgImgWidth			: "幅",
DlgImgHeight		: "高�?�",
DlgImgLockRatio		: "ロック比率",
DlgBtnResetSize		: "サイズリセット",
DlgImgBorder		: "ボーダー",
DlgImgHSpace		: "横間隔",
DlgImgVSpace		: "縦間隔",
DlgImgAlign			: "行�?��?�",
DlgImgAlignLeft		: "左",
DlgImgAlignAbsBottom: "下部(絶対的)",
DlgImgAlignAbsMiddle: "中央(絶対的)",
DlgImgAlignBaseline	: "ベースライン",
DlgImgAlignBottom	: "下",
DlgImgAlignMiddle	: "中央",
DlgImgAlignRight	: "�?�",
DlgImgAlignTextTop	: "テキスト上部",
DlgImgAlignTop		: "上",
DlgImgPreview		: "プレビュー",
DlgImgAlertUrl		: "イメージ�?�URLを入力�?��?��??�?��?��?�。",
DlgImgLinkTab		: "リンク",

// Flash Dialog
DlgFlashTitle		: "Flash プロパティ",
DlgFlashChkPlay		: "�?生",
DlgFlashChkLoop		: "ループ�?生",
DlgFlashChkMenu		: "Flashメニュー�?�能",
DlgFlashScale		: "拡大縮�?設定",
DlgFlashScaleAll	: "�?��?��?�表示",
DlgFlashScaleNoBorder	: "外�?�見�?��?��?�様�?�拡大",
DlgFlashScaleFit	: "上下左�?��?�フィット",

// Link Dialog
DlgLnkWindowTitle	: "�?イパーリンク",
DlgLnkInfoTab		: "�?イパーリンク 情報",
DlgLnkTargetTab		: "ターゲット",

DlgLnkType			: "リンクタイプ",
DlgLnkTypeURL		: "URL",
DlgLnkTypeAnchor	: "�?��?�ページ�?�アンカー",
DlgLnkTypeEMail		: "E-Mail",
DlgLnkProto			: "プロトコル",
DlgLnkProtoOther	: "&lt;�??�?�他&gt;",
DlgLnkURL			: "URL",
DlgLnkAnchorSel		: "アンカーを�?�択",
DlgLnkAnchorByName	: "アンカー�??",
DlgLnkAnchorById	: "エレメントID",
DlgLnkNoAnchors		: "&lt;ドキュメント�?��?��?��?�利用�?�能�?�アンカー�?��?�り�?��?�ん。&gt;",
DlgLnkEMail			: "E-Mail アドレス",
DlgLnkEMailSubject	: "件�??",
DlgLnkEMailBody		: "本文",
DlgLnkUpload		: "アップロード",
DlgLnkBtnUpload		: "サー�?ー�?��?信",

DlgLnkTarget		: "ターゲット",
DlgLnkTargetFrame	: "&lt;フレーム&gt;",
DlgLnkTargetPopup	: "&lt;�?ップアップウィンドウ&gt;",
DlgLnkTargetBlank	: "新�?��?�ウィンドウ (_blank)",
DlgLnkTargetParent	: "親ウィンドウ (_parent)",
DlgLnkTargetSelf	: "�?��?�ウィンドウ (_self)",
DlgLnkTargetTop		: "最上�?ウィンドウ (_top)",
DlgLnkTargetFrameName	: "目的�?�フレーム�??",
DlgLnkPopWinName	: "�?ップアップウィンドウ�??",
DlgLnkPopWinFeat	: "�?ップアップウィンドウ特徴",
DlgLnkPopResize		: "リサイズ�?�能",
DlgLnkPopLocation	: "ロケーション�?ー",
DlgLnkPopMenu		: "メニュー�?ー",
DlgLnkPopScroll		: "スクロール�?ー",
DlgLnkPopStatus		: "ステータス�?ー",
DlgLnkPopToolbar	: "ツール�?ー",
DlgLnkPopFullScrn	: "全画�?�モード(IE)",
DlgLnkPopDependent	: "開�?��?�ウィンドウ�?�連動�?��?�閉�?�る (Netscape)",
DlgLnkPopWidth		: "幅",
DlgLnkPopHeight		: "高�?�",
DlgLnkPopLeft		: "左端�?�ら�?�座標�?�指定",
DlgLnkPopTop		: "上端�?�ら�?�座標�?�指定",

DlnLnkMsgNoUrl		: "リンクURLを入力�?��?��??�?��?��?�。",
DlnLnkMsgNoEMail	: "メールアドレスを入力�?��?��??�?��?��?�。",
DlnLnkMsgNoAnchor	: "アンカーを�?�択�?��?��??�?��?��?�。",

// Color Dialog
DlgColorTitle		: "色�?�択",
DlgColorBtnClear	: "クリア",
DlgColorHighlight	: "�?イライト",
DlgColorSelected	: "�?�択色",

// Smiley Dialog
DlgSmileyTitle		: "顔文字挿入",

// Special Character Dialog
DlgSpecialCharTitle	: "特殊文字�?�択",

// Table Dialog
DlgTableTitle		: "テーブル プロパティ",
DlgTableRows		: "行",
DlgTableColumns		: "列",
DlgTableBorder		: "ボーダーサイズ",
DlgTableAlign		: "キャプション�?�整列",
DlgTableAlignNotSet	: "<�?��?�>",
DlgTableAlignLeft	: "左",
DlgTableAlignCenter	: "中央",
DlgTableAlignRight	: "�?�",
DlgTableWidth		: "テーブル幅",
DlgTableWidthPx		: "ピクセル",
DlgTableWidthPc		: "パーセント",
DlgTableHeight		: "テーブル高�?�",
DlgTableCellSpace	: "セル内余白",
DlgTableCellPad		: "セル内間隔",
DlgTableCaption		: "ｷｬﾌﾟｼｮ�?",
DlgTableSummary		: "テーブル目的/構造",

// Table Cell Dialog
DlgCellTitle		: "セル プロパティ",
DlgCellWidth		: "幅",
DlgCellWidthPx		: "ピクセル",
DlgCellWidthPc		: "パーセント",
DlgCellHeight		: "高�?�",
DlgCellWordWrap		: "折り返�?�",
DlgCellWordWrapNotSet	: "&lt;�?��?�&gt;",
DlgCellWordWrapYes	: "Yes",
DlgCellWordWrapNo	: "No",
DlgCellHorAlign		: "セル横�?�整列",
DlgCellHorAlignNotSet	: "&lt;�?��?�&gt;",
DlgCellHorAlignLeft	: "左",
DlgCellHorAlignCenter	: "中央",
DlgCellHorAlignRight: "�?�",
DlgCellVerAlign		: "セル縦�?�整列",
DlgCellVerAlignNotSet	: "&lt;�?��?�&gt;",
DlgCellVerAlignTop	: "上",
DlgCellVerAlignMiddle	: "中央",
DlgCellVerAlignBottom	: "下",
DlgCellVerAlignBaseline	: "ベースライン",
DlgCellRowSpan		: "縦幅(行数)",
DlgCellCollSpan		: "横幅(列数)",
DlgCellBackColor	: "背景色",
DlgCellBorderColor	: "ボーダーカラー",
DlgCellBtnSelect	: "�?�択...",

// Find Dialog
DlgFindTitle		: "検索",
DlgFindFindBtn		: "検索",
DlgFindNotFoundMsg	: "指定�?�れ�?�文字列�?�見�?��?�り�?��?�ん�?��?��?�。",

// Replace Dialog
DlgReplaceTitle			: "置�??�?��?�",
DlgReplaceFindLbl		: "検索�?�る文字列:",
DlgReplaceReplaceLbl	: "置�?��?��?�る文字列:",
DlgReplaceCaseChk		: "部分一致",
DlgReplaceReplaceBtn	: "置�?��?�",
DlgReplaceReplAllBtn	: "�?��?��?�置�?��?�",
DlgReplaceWordChk		: "�?�語�?��?�?�一致",

// Paste Operations / Dialog
PasteErrorPaste	: "ブラウザー�?�セキュリティ設定�?�よりエディタ�?�貼り付�?��?作�?�自動�?�実行�?�る�?��?��?��?��??�?��?�ん。実行�?�る�?��?�手動�?�キーボード�?�(Ctrl+V)を使用�?��?��??�?��?��?�。",
PasteErrorCut	: "ブラウザー�?�セキュリティ設定�?�よりエディタ�?�切り�?�り�?作�?�自動�?�実行�?�る�?��?��?��?��??�?��?�ん。実行�?�る�?��?�手動�?�キーボード�?�(Ctrl+X)を使用�?��?��??�?��?��?�。",
PasteErrorCopy	: "ブラウザー�?�セキュリティ設定�?�よりエディタ�?�コピー�?作�?�自動�?�実行�?�る�?��?��?��?��??�?��?�ん。実行�?�る�?��?�手動�?�キーボード�?�(Ctrl+C)を使用�?��?��??�?��?��?�。",

PasteAsText		: "プレーンテキスト貼り付�?�",
PasteFromWord	: "ワード文章�?�ら貼り付�?�",

DlgPasteMsg2	: "キーボード(<STRONG>Ctrl+V</STRONG>)を使用�?��?��?次�?�入力エリア内�?�貼�?��?��?<STRONG>OK</STRONG>を押�?��?��??�?��?��?�。",
DlgPasteIgnoreFont		: "Fontタグ�?�Face属性を無視�?��?��?�。",
DlgPasteRemoveStyles	: "スタイル定義を削除�?��?��?�。",
DlgPasteCleanBox		: "入力エリアクリア",


// Color Picker
ColorAutomatic	: "自動",
ColorMoreColors	: "�??�?�他�?�色...",

// Document Properties
DocProps		: "文書 プロパティ",

// Anchor Dialog
DlgAnchorTitle		: "アンカー プロパティ",
DlgAnchorName		: "アンカー�??",
DlgAnchorErrorName	: "アンカー�??を必�?�入力�?��?��??�?��?��?�。",

// Speller Pages Dialog
DlgSpellNotInDic		: "辞書�?��?�り�?��?�ん",
DlgSpellChangeTo		: "変更",
DlgSpellBtnIgnore		: "無視",
DlgSpellBtnIgnoreAll	: "�?��?��?�無視",
DlgSpellBtnReplace		: "置�?�",
DlgSpellBtnReplaceAll	: "�?��?��?�置�?�",
DlgSpellBtnUndo			: "やり直�?�",
DlgSpellNoSuggestions	: "- 該当�?��?� -",
DlgSpellProgress		: "スペル�?ェック処�?�中...",
DlgSpellNoMispell		: "スペル�?ェック完了: スペル�?�誤り�?��?�り�?��?�ん�?��?��?�",
DlgSpellNoChanges		: "スペル�?ェック完了: 語�?��?�変更�?�れ�?��?�ん�?��?��?�",
DlgSpellOneChange		: "スペル�?ェック完了: １語�?�変更�?�れ�?��?��?�",
DlgSpellManyChanges		: "スペル�?ェック完了: %1 語�?�変更�?�れ�?��?��?�",

IeSpellDownload			: "スペル�?ェッカー�?�インストール�?�れ�?��?��?��?�ん。今�?��??ダウンロード�?��?��?��?�?",

// Button Dialog
DlgButtonText	: "テキスト (値)",
DlgButtonType	: "タイプ",

// Checkbox and Radio Button Dialogs
DlgCheckboxName		: "�??�?",
DlgCheckboxValue	: "値",
DlgCheckboxSelected	: "�?�択済�?�",

// Form Dialog
DlgFormName		: "フォーム�??",
DlgFormAction	: "アクション",
DlgFormMethod	: "メソッド",

// Select Field Dialog
DlgSelectName		: "�??�?",
DlgSelectValue		: "値",
DlgSelectSize		: "サイズ",
DlgSelectLines		: "行",
DlgSelectChkMulti	: "複数項目�?�択を許�?�",
DlgSelectOpAvail	: "利用�?�能�?�オプション",
DlgSelectOpText		: "�?�択項目�??",
DlgSelectOpValue	: "�?�択項目値",
DlgSelectBtnAdd		: "追加",
DlgSelectBtnModify	: "編集",
DlgSelectBtnUp		: "上�?�",
DlgSelectBtnDown	: "下�?�",
DlgSelectBtnSetValue : "�?�択�?��?�値を設定",
DlgSelectBtnDelete	: "削除",

// Textarea Dialog
DlgTextareaName	: "�??�?",
DlgTextareaCols	: "列",
DlgTextareaRows	: "行",

// Text Field Dialog
DlgTextName			: "�??�?",
DlgTextValue		: "値",
DlgTextCharWidth	: "サイズ",
DlgTextMaxChars		: "最大長",
DlgTextType			: "タイプ",
DlgTextTypeText		: "テキスト",
DlgTextTypePass		: "パスワード入力",

// Hidden Field Dialog
DlgHiddenName	: "�??�?",
DlgHiddenValue	: "値",

// Bulleted List Dialog
BulletedListProp	: "箇�?�書�?? プロパティ",
NumberedListProp	: "段�?�番�?� プロパティ",
DlgLstType			: "タイプ",
DlgLstTypeCircle	: "白丸",
DlgLstTypeDisc		: "黒丸",
DlgLstTypeSquare	: "四角",
DlgLstTypeNumbers	: "アラビア数字 (1, 2, 3)",
DlgLstTypeLCase		: "英字�?文字 (a, b, c)",
DlgLstTypeUCase		: "英字大文字 (A, B, C)",
DlgLstTypeSRoman	: "ローマ数字�?文字 (i, ii, iii)",
DlgLstTypeLRoman	: "ローマ数字大文字 (I, II, III)",

// Document Properties Dialog
DlgDocGeneralTab	: "全般",
DlgDocBackTab		: "背景",
DlgDocColorsTab		: "色�?�マージン",
DlgDocMetaTab		: "メタデータ",

DlgDocPageTitle		: "ページタイトル",
DlgDocLangDir		: "言語文字表記�?�方�?�",
DlgDocLangDirLTR	: "左�?�ら�?��?�文字表記�?��?��?�(LTR)",
DlgDocLangDirRTL	: "�?��?�ら左�?�文字表記�?��?��?�(RTL)",
DlgDocLangCode		: "言語コード",
DlgDocCharSet		: "文字セット符�?�化",
DlgDocCharSetOther	: "他�?�文字セット符�?�化",

DlgDocDocType		: "文書タイプヘッダー",
DlgDocDocTypeOther	: "�??�?�他文書タイプヘッダー",
DlgDocIncXHTML		: "XHTML宣言をインクルード",
DlgDocBgColor		: "背景色",
DlgDocBgImage		: "背景画�? URL",
DlgDocBgNoScroll	: "スクロール�?��?��?�背景",
DlgDocCText			: "テキスト",
DlgDocCLink			: "リンク",
DlgDocCVisited		: "アクセス済�?�リンク",
DlgDocCActive		: "アクセス中リンク",
DlgDocMargins		: "ページ・マージン",
DlgDocMaTop			: "上部",
DlgDocMaLeft		: "左",
DlgDocMaRight		: "�?�",
DlgDocMaBottom		: "下部",
DlgDocMeIndex		: "文書�?�キーワード(カンマ区切り)",
DlgDocMeDescr		: "文書�?�概�?",
DlgDocMeAuthor		: "文書�?�作者",
DlgDocMeCopy		: "文書�?�著作権",
DlgDocPreview		: "プレビュー",

// Templates Dialog
Templates			: "テンプレート(雛形)",
DlgTemplatesTitle	: "テンプレート内容",
DlgTemplatesSelMsg	: "エディター�?�使用�?�るテンプレートを�?�択�?��?��??�?��?��?�。<br>(�?�在�?�エディタ�?�内容�?�失�?れ�?��?�):",
DlgTemplatesLoading	: "テンプレート一覧読�?�込�?�中. �?��?�ら�??�?�待�?��??�?��?��?�...",
DlgTemplatesNoTpl	: "(テンプレート�?�定義�?�れ�?��?��?��?�ん)",

// About Dialog
DlgAboutAboutTab	: "�?ージョン情報",
DlgAboutBrowserInfoTab	: "ブラウザ情報",
DlgAboutVersion		: "�?ージョン",
DlgAboutLicense		: "Licensed under the terms of the GNU Lesser General Public License",
DlgAboutInfo		: "より詳�?��?�情報�?��?��?�ら�?�"
}