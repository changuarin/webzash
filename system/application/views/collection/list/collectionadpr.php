<?

$year = $this->uri->segment(3,'');
$month = $this->uri->segment(4,'');
$tmp = str_replace('nhgt_', '', $this->db->database);

switch($tmp):
	case'sanjuan':$branch='San Juan';break;
	case'sanpablo':$branch='San Pablo';break;
	case'latrinidad':$branch='La Trinidad';break;
	case'launion':$branch='La Union';break;
	default: 
		$branch = $tmp;
endswitch;

$this->db->select('branch_code');
$this->db->where('branch_name', $branch);
$qb = $this->db->get('nhgt_master.branch');
$branchcode = '';
if($qb->num_rows()) $branchcode = $qb->row()->branch_code;

if($month.$year!='')
{
	$month = date('m',strtotime("$year-$month-01"));
	$year = date('Y',strtotime("$year-$month-01"));

	$data = $this->db->query("
		SELECT *
		FROM nhgt_bills.header
		WHERE YEAR(billdate) = '$year'
		AND MONTH(billdate) = '$month'
		AND branchcode = '$branchcode'
		AND (status = 'adj' OR status = 'due' OR status = 'pmt' OR status = 'rem')
		AND bankbranch != 'ACCOM'
		ORDER BY billdate ASC, name ASC, CI_AcctNo DESC, paytype DESC, loantrans = 'SPEC' DESC, amtodrawn ASC;
	")->result_array();
}
?>

<link type="text/css" rel="stylesheet" href="<?=base_url()?>system/application/assets/css/forms.css">
<script src="<?=base_url()?>system/application/assets/js/jquery.min.js"></script><?

	$this->load->helper('code');

?><script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.filterTable.js"></script><?
?><script><?

echo trims("
	$(document).ready(function()
	{");

	if($month.$year!=''):

		echo trims("

		$('.sbtn').click(function()
		{
			var w=700,h=450;
			var x=screen.width/2-w/2,y=screen.height/2-h/2;
			var c=$(this).parent().parent();
			var d=c.attr('tag');
			var g=c.attr('tagdate');
			var b='',f='';
			
			var a=$('.'+d).attr('id');
			for(e=0;e<$('.'+d).length;e++)
			{
				b+=$($($('.'+d)[e]).find('td')[4]).html()+'.';
				f+=$($($('.'+d)[e]).find('td')[4]).attr('brn')+'.';
			}
			var w0=window.open('../../../collection/de/'+a+'/'+b+'/'+g+'/'+f,
				'subForm','height='+h+',width='+w+',left='+x+',top='+y);
			w0.focus();

		});



		$('.wdate').click(function()
		{
			var w=300,h=250;
			var x=screen.width/2-w/2,y=screen.height/2-h/2;
			var c=$(this).parent();
			var d=c.attr('tag');
			var g=c.attr('tagdate');
			var b='',f='';

			var a=$('.'+d).attr('id');
			for(e=0;e<$('.'+d).length;e++)
			{
				b+=$($($('.'+d)[e]).find('td')[4]).html()+'.';
				f+=$($($('.'+d)[e]).find('td')[4]).attr('brn')+'.';
			}

			var w0=window.open('../../../collection/cwd/'+a+'/'+g+'/'+f,
				'subForm','height='+h+',width='+w+',left='+x+',top='+y);
			w0.focus();
		});
		


		$('table.cl').filterTable({
			autofocus: 1,
			placeholder: 'Search Client',
			ignoreColumns: [0, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]
		});


		$(\".day-filter\").click(function(){
            var me = $(this).val();
            $(\".day-filter\").removeClass(\"day-filtered\");
            $(\".filter-table input\").val(\"\");
            $(this).addClass(\"day-filtered\");
            $(\".wdate\").parent(\"tr\").css(\"display\",\"table-row\");
            $(\".dayheld\").each(function(){
                var grand = $(this).parent(\"th\").parent(\"tr\");
                
                if(me == $(this).val())
                {
                    return grand.css(\"display\",\"table-row\");
                }
                grand.css(\"display\",\"none\");
            });
        });
        
        
        $(\".filter-table input\").click(function(){
           $(\".day-filter\").removeClass(\"day-filtered\"); 
        });

	
		");

	endif;

		echo trims("$('#ldri', parent.document).hide();
		
	})
");

?></script>
<style type="text/css">
	.disabled{
		background-color: yellow;
	}
	.sel{
		background-color: transparent;
	}
	.sel:hover{
		background-color: #99ffff;
	}
	.wdate{
		cursor: pointer;
		color: black;
		text-decoration: none;
		padding-right: 5px;
		padding-left: 5px;
	}
	.wdate:hover{
		color: blue;
		text-decoration: underline;
	}
	.filter-table{
		margin-top: 10px;
		position: fixed;
		background-color: #FFF;
		width: 100%;
	}
	.fixed{
		position: fixed;
		background-color: #FFF;
		width: 100%;
	}
</style>
<body style="margin:0px;">
	<div class="fixed">Filter by day <?php
    for($i = 1; $i < 32; $i++){
        echo "<input class='day-filter' type='button' value='".($i < 10?"0".$i:$i)."' />";
    }
    ?></div><br><table class="cl"width="100%"border="1"cellpadding="0"cellspacing="0"style="margin-top: 30px;">
	<tr>
		<th align="left">&nbsp;</th>
		<th align="left">Name</th>
		<th align="left">Payment Type</th>
		<th align="left">Bank AcctNo</th>
		<th align="left">Bank Branch</th>
		<th align="left">Loan Reference</th>
		<th align="left">Loan Type</th>
		<th align="left">Duration</th>
		<th align="left">Terms</th>
		<th align="right">Balance</th>
		<th align="right">Loan Amount</th>
		<td>&nbsp;</td>
	</tr><?

	//$this->output->enable_profiler(TRUE);

	$this->load->model('rsm');

	if(isset($data)&&count($data))
	{
		$acctno = ''; $id=0;
		foreach ($data as $d)
		{
			$this->db->select('bill_id');
			$this->db->where('cid', $d['CI_AcctNo']);
			$this->db->where("bill_id LIKE '%{$d['bill_id']}.%'");
			$qq = $this->db->get('collection_entry');
			if( $qq->num_rows() )
			{
				unset($qq, $param);
				$isColl = " style='background-color:#F9F7AD'";
			}else $isColl = '';

			$day = date('d', strtotime($d['billdate']));
			$disabled = $d['collectby']?'disabled':'';
			//$isthesame = $acctno!=$d['CI_AcctNo'];
			//if($isthesame)$id++;
			$id++;
			echo"<tr$isColl id='{$d['CI_AcctNo']}'class='data sel $disabled id$id'tag='id$id'tagdate='{$d['billdate']}'>
			<th class='wdate'title='Change Withdrawal date'>" . $day . "<input class='dayheld' type='hidden' value='$day'/></th>
			<td>{$d['name']}</td>
			<td>{$d['paytype']}</td>
			<td>{$d['bankacctno']}</td>
			<td>{$d['bankbranch']}</td>
			<td brn='{$d['bill_id']}'>{$d['LH_PN']}</td>
			<td>{$d['pentype']}</td>
			<td>{$d['duration']}</td>
			<td>{$d['terms']}</td>
			<td>{$d['balance']}</td>
			<td>{$d['amtodrawn']}</td>
			<td><input class='sbtn'type='button'value='Submit'/></td></tr>";
			$acctno = $d['CI_AcctNo'];

			unset( $disabled, $isColl, $qq );

			flush();

		}
	}

?></table>