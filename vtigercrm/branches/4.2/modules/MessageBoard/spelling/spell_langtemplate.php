<?php
  // --------------------------------------------------------------------
  // phpSpell Language Template
  //
  // This is (c)Copyright 2002, Team phpSpell.
  // --------------------------------------------------------------------

  // --------------------------
  // Table Name
  // --------------------------
  $DB_TableName=$table_prefix."spelling_words";

  // ---------------------------------------
  // PSPELL Support - Use English Dictionary
  // ---------------------------------------
  $Spell_Config["PSPELL_LANGUAGE"] = "en";

  // --------------------------------------------------------------------
  // Example translation table:
  //     $Translation_Table = array("�", "�", "�");
  //     $Replacement_Table = array("a", "an", "sth");
  //     $Language_Translation_Character_List = "���";
  // --------------------------------------------------------------------
  // for every "�" it finds in a word it will replace it with a "a"
  // for every "�" it finds it will replace it with a "sth"
  // for every "�" it finds it will replace it with a "�"
  // --------------------------------------------------------------------
  // Put the character(s) to be translated into the Translation_Table
  // Put the replacement character(s) into the replacement table
  // --------------------------------------------------------------------
  // The replacement string should be equivelent to the ENGLISH PHONETIC
  // sound.  So if you were to take a word with "�" in it; how would you
  // phonetically spell the word in english.  If the "�" sounds like a "A"
  // in english then "A" would be the replacement character.
  // If it sounds like "th" then you would use "th" as the characters.
  // always replace Larger groups first.  (i.e. if "��" sounds differently
  // than "�" then in the translation table you would have the "��" listed
  // before the "�".  So that way when it would replaced the "��" before it
  // replaced it twice with "�".
  // --------------------------------------------------------------------
  // Any letters you do not translate will be IGNORED for
  // when it attempts to find spelling matches!!!
  // --------------------------------------------------------------------
  $Translation_Table = array();
  $Replacement_Table = array();

  // --------------------------------------------------------------------
  // Put the list of valid characters in your language in this list
  // --------------------------------------------------------------------
  $Language_Character_List = "abcdefghijklmnopqrstuvwxyz'";

  // --------------------------------------------------------------------
  // Put the list of most common words in this list ",word,word,word,"
  // --------------------------------------------------------------------
  $Language_Common_Words = "";

  // --------------------------------------------------------------------
  // Translation function
  // --------------------------------------------------------------------
  function Translate_Word($Word) {
    global $Translation_Table, $Replacement_Table;

    $New_Word = str_replace($Translation_Table, $Replacement_Table, $Word);
    return ($New_Word);
  }


  // --------------------------------------------------------------------
  // Phonetic work function
  // --------------------------------------------------------------------
  function Word_Sound_Function($Word) {
    return (metaphone($Word));
  }


  // -------------------------------------------------------------------------
  // This function will allow you to convert from one character set to another
  // -------------------------------------------------------------------------
  function Language_Decode($Data)
  {
    // MS Internet Explorer Hack -- IE sends utf8-unicode for upper (ascii 128+) characters
     if (strpos(@$_SERVER['HTTP_USER_AGENT'], 'MSIE') > 0 || strpos(@$_SERVER['ALL_HTTP'], 'MSIE') > 0) {
       if (function_exists('utf8_decode')) $Data = utf8_decode($Data);
     }
     return ($Data);
  }

  // -------------------------------------------------------------------------
  // This allows you to re-encode it back to your character set
  // -------------------------------------------------------------------------
  function Language_Encode($Data)
  {
    return ($Data);
  }



?>