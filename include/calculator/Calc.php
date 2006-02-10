<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/

function get_calc($image_path) {
$the_calc = <<<EOQ
<script language="JavaScript" type="text/javascript" src="include/js/general.js"></script>
<script language="JavaScript" type="text/javascript" src="include/calculator/calc.js"></script>
<table border="0" cellspacing="0" cellpadding="0" style="margin-top:0;margin-left:0;" align="center">
  <!--tr>
    <!-- td width="7" height="6"><img src="$image_path/calc_top_left.gif"></td -->
    <!-- td height="6" class="calcBg"><img src="$image_path/blank.gif"></td -->
    <!-- td width="8" height="6"><img src="$image_path/calc_top_right.gif"></td -->
  </tr -->
  <tr>
    <!-- td class="calcBg" width="7"></td -->
    <td class="calcBg">
        <table border="0" cellpadding="2" cellspacing="2">
		<form name="calculator">
          <tr style="height:5">
            <td></td>
          </tr>
          <tr>
            <td colspan=6><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <!-- tr>
                  <!-- td width="3" height="3"><img src="$image_path/calc_res_tl.gif"></td -->
                  <!-- td height="3" background="$image_path/calc_res_tc.gif"><img src="$image_path/blank.gif"></td -->
                  <!--td width="3" height="3"><img src="$image_path/calc_res_tr.gif"></td -->
                </tr -->
                <tr>
                  <!--td width="3"class="calcResult"><img src="$image_path/blank.gif"></td--><!-- background="$image_path/calc_res_ml.gif" -->
                  <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td width="10%"><input type="text" class="calcMem" name="mem" value="M" readonly></td>
                        <td width="90%"><input type="text" class="calcResult" name="answer" maxlength="30" onChange="CheckNumber(this.value)" readonly></td>
                      </tr>
                    </table></td>
                  <!-- td width="3" background="$image_path/calc_res_mr.gif"><img src="$image_path/blank.gif"></td -->
                </tr>
                <!-- tr>
                  <!-- td width="3" height="3"><img src="$image_path/calc_res_bl.gif"></td -->
                  <!-- td height="3" background="$image_path/calc_res_bc.gif"><img src="$image_path/blank.gif"></td -->
                  <!-- td width="3" height="3"><img src="$image_path/calc_res_br.gif"></td -->
                </tr -->
              </table></td>
          </tr>
          <!--<tr style="height:5">
            <td></td>
          </tr>-->
          <tr>
            <td colspan="3"><!-- div align="center"><img src="$image_path/calc_logo.gif" width="80" height="17"></div--></td>
            <td width="25"> <input type="button" name="CE" class="calcCancBtn" value="CE" onClick="CECalc(); return false;">
            </td>
            <td width="25"> <input type="reset" name="C" class="calcCancBtn" value="C" onClick="ClearCalc(); return false;">
            </td>
          </tr>
          <tr>
            <td width="25"> <input type="button" name="backspace" class="calcBackBtn" value="" onClick="Backspace(document.calculator.answer.value); return false;"></td>
            <td width="25"> <input type="button" name="recip" class="calcBlackBtn" value="1/x" onClick="RecipButton(); return false;"></td>
            <td width="25"> <input type="button" name="sqrt" class="calcBlackBtn" value="sqrt" onClick="SqrtButton(); return false;"></td>
            <td width="25"> <input type="button" name="negate" class="calcBlackBtn" value="+/-" onClick="NegateButton(); return false;"></td>
            <td width="25"> <input type="button" name="percent" class="calcBlackBtn" value="%" onClick="PercentButton(); return false;"></td>
          </tr>
          <tr>
            <td width="25"> <input type="button" name="MC" class="calcMemBtn" value="MC" onClick="MemoryClear(); return false;"></td>
            <td width="25"> <input type="button" name="calc7" class="calcGreyBtn" value="7" onClick="CheckNumber('7'); return false;"></td>
            <td width="25"> <input type="button" name="calc8" class="calcGreyBtn" value="8" onClick="CheckNumber('8'); return false;"></td>
            <td width="25"> <input type="button" name="calc9" class="calcGreyBtn" value="9" onClick="CheckNumber('9'); return false;"></td>
            <td width="25"> <input type="button" name="divide" class="calcBlackBtn" value="/" onClick="DivButton(1); return false;"></td>
          </tr>
          <tr>
            <td width="25"> <input type="button" name="MR" class="calcMemBtn" value="MR" onClick="MemoryRecall(Memory); return false;"></td>
            <td width="25"> <input type="button" name="calc4" class="calcGreyBtn" value="4" onClick="CheckNumber('4'); return false;"></td>
            <td width="25"> <input type="button" name="calc5" class="calcGreyBtn" value="5" onClick="CheckNumber('5'); return false;"></td>
            <td width="25"> <input type="button" name="calc6" class="calcGreyBtn" value="6" onClick="CheckNumber('6'); return false;"></td>
            <td width="25"> <input type="button" name="multiply" class="calcBlackBtn" value="x" onClick="MultButton(1); return false;"></td>
          </tr>
          <tr>
            <td width="25"> <input type="button" name="MS" class="calcMemBtn" value="M-" onClick="MemorySubtract(document.calculator.answer.value); return false;"></td>
            <td width="25"> <input type="button" name="calc1" class="calcGreyBtn" value="1" onClick="CheckNumber('1'); return false;"></td>
            <td width="25"> <input type="button" name="calc2" class="calcGreyBtn" value="2" onClick="CheckNumber('2'); return false;"></td>
            <td width="25"> <input type="button" name="calc3" class="calcGreyBtn" value="3" onClick="CheckNumber('3'); return false;"></td>
            <td width="25"> <input type="button" name="minus" class="calcBlackBtn" value="-" onClick="SubButton(1); return false;"></td>
          </tr>
          <tr>
            <td width="25"> <input type="button" name="Mplus" class="calcMemBtn" value="M+" onClick="MemoryAdd(document.calculator.answer.value); return false;"></td>
            <td width="25"> <input type="button" name="calc0" class="calcGreyBtn" value="0" onClick="CheckNumber('0'); return false;"></td>
            <td width="25"> <input type="button" name="dot" class="calcGreyBtn" value="." onClick="CheckNumber('.'); return false;"></td>
            <td width="25"> <input type="button" name="equal" class="calcBlackBtn" value="=" onClick="EqualButton(0); return false;"></td>
            <td width="25"> <input type="button" name="plus" class="calcBlackBtn" value="+" onClick="AddButton(1); return false;"></td>
          </tr>
		   </form>
        </table>
     </td>
    <!-- td width="8" background="$image_path/calc_middle_right.gif"></td -->
  </tr>
  <!-- tr>
    <!-- td width="7" height="8"><img src="$image_path/calc_bottom_left.gif"></td -->
    <!-- td height="8" background="$image_path/calc_bottom_center.gif"><img src="$image_path/blank.gif"></td -->
    <!-- td width="8" height="8"><img src="$image_path/calc_bottom_right.gif"></td -->
  </tr -->
</table>
EOQ;

return $the_calc;
}
?>
