<style>
	body{
		margin: 0px;
		background-color: white;
	}
    .rmbill{
        cursor: pointer;
        color: black;
    }
    .rmbill:hover{
        color: red;   
    }
</style>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.min.js"></script>
<script type="text/javascript">
    $(document).ready(function()
    {
        $('.rmbill').click(function()
        {
            var z=$(this);
            if(confirm('Are you sure you want to Remove this Billing?'))
            {
                $.post('../../z/zy',{a:z.html(),b:z.parent().attr('class'),c:z.parent().index()},function(c){ eval(c) });
            }
        })
    });
</script>
<body>
<table width="100%" style="font:12px Arial;" cellpadding="0" cellspacing="0">
    <tr style="color:darkblue;background:#FFC082;">
        <th class="br bb" width="1%" align='left'nowrap>PN</th>
        <th class="br bb"align='right'width="1%"nowrap>ReferenceNo</th>
        <th class="br bb"align='center'>Date</th>
        <th class="br bb"align='right'>Debit</th>
        <th class="bb"align='right'>Credit</th>
        <th class="bb"align='right'>Balance</th>
    </tr><?
$total = 0;
$a = 0; $tr='';
if(!empty($data)):
    foreach ($data as $d):

        $total += $d['credit'];
        $total -= $d['debit'];
        if(strpos($d['billid'],'TR')>-1)$tr=$d['billid'];
        $b=($a?'#F5F5F5':'#E7F9FF');
        ?><tr style="background-color:<?=$b?>;"class="<?=$tr?>">
            <td class="br bb spadl spadr"title=""nowrap><?=$d['pnno']?></td>
            <td class="br bb<?
            if( $this->session->userdata('user_name')=='admin'||
                $this->session->userdata('user_name')=='cabituin'||
                $this->session->userdata('user_name')=='kcabilar'||
                $this->session->userdata('user_name')=='fmarroyo' ):
                if(strpos($d['billid'], 'BL')>-1)
                echo' rmbill';
            endif;
            ?>" align="right"><?=$d['billid']?></td>
            <td class="br bb" align="center"><?=$d['date']?></td>
            <td class="br bb spadr"align="right"><?=($d['debit']==0?'-':$d['debit'])?></td>
            <td class="br bb spadr"align="right"><?=($d['credit']==0?'-':$d['credit'])?></td>
            <td class="bb spadr"align="right"style="font-weight:bold;"><?=$total?></td>
        </tr><?
        $a=$a?0:1;
        
    endforeach;
endif;

?></table>
</body>