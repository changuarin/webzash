<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/rsm.custom.js"></script>
<script>
    $(document).ready(function()
    {
        var a=parseFloat($('#i',window.opener.document).val());
        var b=$('#g',window.opener.document).val()?parseFloat($('#g',window.opener.document).val()):0;
        var url='../../../submit/a';
        $('#b').val( Format(a+b) );


        $('#k').val( $('#e',window.opener.document).val() );
        $('label.remarks').html( $('#e',window.opener.document).val() );
        $('#l').val( $('#r',window.opener.document).val() );
        $('label.nam').html( $('#r',window.opener.document).val() );
        $('label.nam').html( $('#r',window.opener.document).val() );
        $('label.banbra').html( $('#o',window.opener.document).val() );
        $('#a').val( $('#j',window.opener.document).val() );
        $('label.dat').html( $('#j',window.opener.document).val() );

        $(".bcas").click(function(){
            if($(".trfp").val()==""){
                alert("RFP No. Required.");
                $(".trfp").focus();
            }else{
                if($(".tcas").val()==""||N($(".tcas").val())==0){
                    alert("RFP Amount Required.");
                    $(".tcas").select();
                }else{
                    $("#toper").css("display","none");
                    $("#preview").css("display","");
                    self.print();
                    $("#toper").css("display","");
                }
            }
        });
        
        $(".bche").click(function(){
            if($(".tche").val()==""||N($(".tche").val())==0){

                alert("Input Amount Greater than one thousand.");
                $(".tche").focus();

            }else{
    
                if(N($(".tche").val())>1000){

                    $(".cche").attr("disabled",true);
                    $(".tche").attr("disabled",true);
                    $(".bcas").attr("disabled",true);
                    $(this).attr("disabled",true);
                    if(confirm("Are you sure you want to Process this Refund?")){
                        $('#n',window.opener.document).val('CV');
                        $('#e',window.opener.document).val( $('#k').val() );
                        $('#t',window.opener.document).val( $('#b').val() );
                        $('#k',window.opener.document).attr('action',url).submit();
                        self.close();
                    }else{
                        $(".cche").attr("disabled",false);
                        $(".tche").attr("disabled",false);
                        $(".bche").attr("disabled",false);
                    }
            
                }else{
                    alert("Must be greater than one thousand.");
                    $(".tche").select();
                }

            }
        });
        
        $(".bsub").click(function(){
            if($(".trfp").val()==""){
                alert("RFP No. Required.");
                $(".trfp").focus();
            }else{
                if($(".tcas").val()==""||N($(".tcas").val())==0){
                    alert("RFP Amount Required.");
                    $(".tcas").select();
                }else{
                    $(".ccas").attr("disabled",true);
                    $(".tcas").attr("disabled",true);
                    $(".trfp").attr("disabled",true);
                    $(this).attr("disabled",true);
                    if(confirm("Are you sure you want to Process this Refund?")){
                        $('#n',window.opener.document).val('RF');
                        $('#e',window.opener.document).val( $('#k').val() );
                        $('#t',window.opener.document).val( $('#i').val() );
                        $('#u',window.opener.document).val( $('#f').val() );
                        $('#k',window.opener.document).attr('action',url).submit();
                        self.close();
                    }else{
                        $(".ccas").attr("disabled",false);
                        $(".tcas").attr("disabled",false);
                        $(".trfp").attr("disabled",false);
                        $(this).attr("disabled",false);
                    }
                }
            }
        });
        
        $(".cche").click(function(){
            if($(this).attr("checked")){
                var tn=N($(".ttot").val());
                var am1=($(this).attr("checked")?N($(".tche").val()):0);
                var am2=($(".ccas").attr("checked")?N($(".tcas").val()):0);
                if(am1+am2>tn){
                    alert("Cannot Proceed due to Amount?");
                    $(".bche").attr("disabled",true);
                }else $(".bche").attr("disabled",false);
                $(".tche").css("background","#FFFFC0")
                    .select();
                $('#f').val('').css("background","");
                $('#i').val('').css("background","");
                $('#h').attr('checked',false);
                $('#g').attr('disabled',true);
                $('#j').attr('disabled',true);
            }else{
                $(".bche").attr("disabled",true);
                $(".tche").css("background","")
                .select();
            }
        });
        
        $(".ccas").click(function(){
            if($(this).attr("checked")){
                var tn=N($(".ttot").val());
                var am1=($(this).attr("checked")?N($(".tcas").val()):0);
                var am2=($(".cche").attr("checked")?N($(".tche").val()):0);
                if(am1+am2>tn){
                    alert("Cannot Proceed due to Amount?");
                    $(".bsub").attr("disabled",true);
                    $(".bcas").attr("disabled",true);
                }else{
                    $(".bsub").attr("disabled",false);
                    $(".bcas").attr("disabled",false);
                }
                $(".tcas").css("background","#FFFFC0");
                $(".trfp").css("background","#FFFFC0").
                select();
                $('#d').val('').css("background","");
                $('#c').attr('checked',false);
                $('#e').attr('disabled',true);
            }else{
                $(".bcas").attr("disabled",true);
                $(".bsub").attr("disabled",true);
                $(".tcas").css("background","");
                $(".trfp").css("background","");
            }
        });
        
        $(".tche").blur(function(){
            var tn=N($(".ttot").val());
            var am1=($(".cche").attr("checked")?N($(".tche").val()):0);
            var am2=($(".ccas").attr("checked")?N($(".tcas").val()):0);
            $(this).val(Format($(this).val()));
            if(am1+am2>tn){
                alert("Cannot Proceed due to Amount?");
                $(this).val('0.00').select();
            }
            
        });
        
        function isChanges(){
            $(".totamt").html("..."+$(".tcas").val()+"...");
            $(".detpay").html(toWords(N($(".tcas").val())));
            $(".remarks").html($(".trem").val());
            $(".payrecby").html($(".tpayby").val());
            $(".rfp").html(revstr($(".trfp").val()));
        }
        
        $(".tcas").change(function(){
            var tn=N($(".ttot").val());
            var am1=($(".cche").attr("checked")?N($(".tche").val()):0);
            var am2=($(".ccas").attr("checked")?N($(".tcas").val()):0);
            $(this).val(Format($(this).val()));
            isChanges();
            if(am1+am2>tn){
                alert("Cannot Proceed due to Amount?");
                $(".totamt").html("");
                $(".detpay").html("");
                $(".rem").html("");
                $(".tcas").val('0.00');
                $(".tcas").select();
            }
        });
        
        $(".tpayby").blur(function(){
            $(this).val(String($(this).val()).toUpperCase());
            isChanges();
        });
        
        $(".trem").blur(function(){
            $(this).val(String($(this).val()).toUpperCase());
            isChanges();
        });
        
        $(".trfp").blur(function(){
            $(this).val(String($(this).val()).toUpperCase());
            isChanges();
        }).change(function(){
            isChanges();
            $(".tcas").select();
        });
    });
</script>
<style>
    .abpos{
        position:relative;
    }
    .trfp{
        width:129px;
    }
    .totamt,.nam{font-size:14px;}
</style>
<body class="nomargin">
<br>
<table id="toper"style="background:#E0E0E0;"align="center"width="60%">
<tr>
    <th class="padl"width="80">
    Date:
    </th>
    <td colspan="2">
    <input id="a"class="text center wid12"type="date"/>
    </td>
</tr>

<tr>
    <th class="padl"width="80">
    Total Amount:
    </th>
    <td colspan="2">
    <input name="ttot"id="b"class="text center wid12 ttot"type="text"readonly/>    </td>
</tr>
<tr>
    <th align="left"class="padl"colspan="3">
    Refund Type
    </th>
</tr>
<tr>
    <th align="right"style="padding-right:10px;">
        Check :
    </th>
    <td width="1%"nowrap>
        <input id="c"class="cche"type="checkbox">
        <input id="d"class="text center tche"type="text"placeholder="Amount"/>
    </td>
    <td>
        <input id="e"class="bche"type="button"value="Submit"disabled/>
    </td>
</tr>
<tr>
    <th align="right"style="padding-right:10px;">
        RFP No. :
    </th>
    <td colspan="2"style="padding-left:22px;">
        <input id="f"class="text center trfp"type="text"placeholder="RFP Number"/>
        <input id="g"class="bcas"type="button"value="Print RFP"disabled/>
    </td>
</tr>
<tr>
    <th align="right"style="padding-right:10px;">
        PCF :
    </th>
    <td>
        <input id="h"class="ccas"type="checkbox"/>
        <input id="i"class="text center tcas"type="text"placeholder="Amount"/>
    </td>
    <td nowrap>
        <input id="j"class="bsub"type="button"value="Submit"disabled/>
    </td>
</tr>

<tr>
    <th align="right"style="padding-right:10px;">
        Remarks :
    </th>
    <td colspan="2">
        <input id="k"class="text trem wid3"type="text"value="TESTING ONLY"/>
    </td>
</tr>
<tr>
    <th align="right"style="padding-left:10px;padding-right:10px;"nowrap>
        Received By :
    </th>
    <td colspan="2">
        <input id="l"class="text tpayby wid3"type="text"value="ABAC, BENEDICTA  "/>
    </td>
</tr>
</table>
<table id="previewrfp" width="100%"border="0"style="padding-top:70px;font:12px Century Gothic;">
<tr>
    <th style="padding-right:70px;padding-bottom:40px;"colspan="2"align="right">
        <label class="abpos rfp"></label>
    </th>
</tr>
<tr>
    <th width="60%"style="padding-left:255px;"align="left">
        <label class="abpos nam"></label>
    </th>
    <th width="40%"style="padding-left:255px;padding-top:5px;"align="left">
        <label class="abpos dat"></label>
    </th>
</tr>
<tr>
    <th colspan="2"style="padding-left:255px;"align="left">
        <label class="abpos banbra"></label>
    </th>
</tr>
<tr>
    <td>
        &nbsp;
    </td>
    <td>
        &nbsp;
    </td>
</tr>
<tr valign="top">
    <th rowspan="10"style="padding-top:155px;padding-left:255px;"align="left">
        <label class="abpos remarks"></label>
    </th>
    <th style="padding-left:60px;padding-top:30px;"rowspan="10"align="left">
        <label class="abpos detpay"></label>
    </th>
</tr>
<tr></tr>
<tr></tr>
<tr></tr>
<tr></tr>
<tr></tr>
<tr></tr>
<tr></tr>
<tr></tr>
<tr></tr>
<tr>
    <td colspan="2"style="padding-top:110px;">
        <table id="previewrfp" width="100%"border="0"style="font:12px Century Gothic;">
        <tr>
            <th style="padding-left:110px;"width="16%"align="left"><label class="abpos preby"><?=$this->session->userdata('user_name')?></label></th>
            <th style="padding-left:110px;"width="16%"align="left"><label class="abpos appby">ACR</label></th>
            <th style="padding-left:120px;"width="50%"align="left"><label class="abpos payrecby"></label></th>
            <th><label class="abpos totamt"></label></th>
        </tr>
        </table>
    </td>
</tr>
<tr>
    <td>
        &nbsp;
    </td>
    <td>
        &nbsp;
    </td>
</tr>
</table>
</body>
