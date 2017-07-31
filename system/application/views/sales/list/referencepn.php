<style>
    body {cursor: default;}
    input[type=button] {border:1px solid black;}
    table {width:100%;}
    table tr td, table tr th {font-family:'Arial';font-size:12px;text-align:left;padding:2px 6px 2px 6px;}
    .nomargin {margin:0px;}

    .tag1 {background-color:#99CCCC;}
    .tag1:hover {background-color:#FFFF00;}
    .tag0 {background-color:#99CCFF;}
    .tag0:hover {background-color:#FFFF00;}
    .w1 {width:250px;}
    .hide {display:none;}
    
    .center {text-align:center;}
    .column2 {width:130px;}
    .column3 {width:50px;}
    .column4 {width:70px;}
    .column6 {width:80px;}
    .right {text-align:right;}
    
    .LH_Balance {text-align:center;width:80px;}
    .inc, .dec {color:blue;font-weight:bold;}
    .inc:hover, .dec:hover {color:#FF0000;text-decoration:underline;}
    #tableHead {background-color:#808080;color:#FFF;}
</style>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.min.js"></script>
<script type="text/javascript">
    $(document).ready(function()
    {
        $('.dec').click(function(){
            var i = $(this).attr('id').substr(3,1);
            var monthlyamort = $('#LH_MonthlyAmort'+i).html();
            var balance = $('#LH_Balance'+i).val();
            var obtbdeducted = parseInt(balance) - parseInt(monthlyamort); 
            if(obtbdeducted<=0){ var obtbdeducted = 0;$('#dec'+i).val(''); }
            $('#LH_Balance'+i).val(obtbdeducted);
        });
        
        $('.inc').click(function(){
            var i = $(this).attr('id').substr(3,1);
            var monthlyamort = $('#LH_MonthlyAmort'+i).html();
            var balance = $('#LH_Balance'+i).val();
            var obtbdeducted = parseInt(balance) + parseInt(monthlyamort);
            if(obtbdeducted>0){ $('#dec'+i).val('<<'); } 
            $('#LH_Balance'+i).val(obtbdeducted);
        });
        
        $('#applyButton').click(function(){
            $('#LH_Reference, #LH_Ref_OB', window.opener.document).val('');
            var LH_OBC = 0;
            var i = 0;
            while(i<$('#count').val())
            {
                if($('#checkBox'+i).attr('checked')==true)
                {
                    var refdiv = '';
                    var refval = $('#LH_Reference', window.opener.document).val();
                    if(refval!=''){ var refdiv='|'; }
                    $('#LH_Reference', window.opener.document).val(refval+refdiv+$('#checkBox'+i).val());
                    var refobdiv = '';
                    var refobval = $('#LH_Ref_OB', window.opener.document).val();
                    if(refobval!=''){ var refobdiv=';'; }
                    $('#LH_Ref_OB', window.opener.document).val(refobval+refobdiv+$('#LH_Balance'+i).val());
                    LH_OBC = parseInt(LH_OBC) + parseInt($('#LH_Balance'+i).val());
                }
                i++;
            }
            $('#LH_OBC', window.opener.document).attr('disabled', false).val(LH_OBC);
            window.opener.$('#LH_LoanAmt').blur();
            window.close();
        });
        
    })
</script>
<body class="nomargin">
        
        <table cellpadding="0" cellspacing="1">
            <tr>
                <td colspan="7">
                    <input id="applyButton" type="button" name="applyButton" value="Apply">
                </td>
            </tr>
            <tr id="tableHead">
                <th class="center"><input id="checkAll" type="checkbox" name="checkAll" ></th>
                <th class="column2">PN</th>
                <th>Type</th>
                <th class="column4 right">Mo. Amort</th>
                <th class="column3 right">Terms</th>
                <th class="column6 right">Actual O.B.</th>
                <th class="center">O.B. to be Deducted</th>
            </tr>
        <?php
        
        if($datas)
        {
            $i=0;
            $j=0;
            foreach($datas as $d)
            {
                echo "
                <tr id='pn$i' class='ccid tag$j'>
                    <td class='center'>
                        <input class='pnCheckBox' id='checkBox$i'}' type='checkbox' value='{$d['LH_PN']}' ".($d['LH_PN']==$referencePN?'checked':'').">
                    </td>
                    <td>{$d['LH_PN']}</td>
                    <td>{$d['LH_LoanTrans']}</td>
                    <td id='LH_MonthlyAmort$i' class='right'>{$d['LH_MonthlyAmort']}</td>
                    <td class='right'>{$d['LH_Terms']}</td>
                    <td id='LH_PN$i' class='right'>{$d['LH_Balance']}</td>
                    <td class='center'>
                        <label class='dec' id='dec$i'><<</label>
                        &nbsp;
                        <input id='LH_Balance$i' type='text' class='LH_Balance' value='".($d['LH_Balance']<=0?'0':$d['LH_Balance'])."'>
                        &nbsp;
                        <label class='inc' id='inc$i'>>></label>
                    </td>
                </tr>";
                $i++;
                $j=$j?0:1;
            }
        }
        
        ?>
            <tr>
                <td colspan="7">
                    <input class="hide" id="count" type="text" name="count" value="<?=$i?>">
                </td>
            </tr>
        </table>
        
</body>