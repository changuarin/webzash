<style>
    body, form, table {
        font-family: 'Arial';
        font-size: 12px;
        cursor: default;
        margin: 0;
        padding: 0;
    }
    
    .tag1 {
        background-color: #d0d0d0;
    }
    
    .tag1:hover {
        background-color: #99ffff;
    }
    
    .tag0 {
        background-color: #f0f0f0;
    }
    
    .tag0:hover {
        background-color: #99ffff;
    }
    
    .w1 {
        width: 250px;
    }
    
    #list {
        width: 720px;
    }
    
    table#list {
        border-collapse: collapse;
    }
    
    table#list th {
        background-color: #eee;
        text-align: ;
        vertical-align: bottom;
    }
    
    table#list td {
        border: 1px solid #fff;
    }
    
    table#list td.alt {
        background-color: #ffc; background-color: rgba(255, 255, 0, 0.2);
    }
    
    .active {
        background-color: #ffff00;
        color: #ff0000;
    }
    
    .center {
        text-align: center;
    }
    
    .left {
        text-align: left;
    }
    
    .right {
        text-align: right;
    }
</style>
<body>
    <table id="list">
        <tbody>
            <tr>
                <td>&nbsp;</th>
                <th class="left">PN</th>
                <th class="right">Balance</th>
                <th class="right">MA</th>
                <th class="right">Terms</th>
                <th class="left">Duration</th>
                <th class="left">Remarks</th>
            </tr>
            <?php
            
            if(!empty($results))
            {
                $i = 0;
                foreach($results as $data)
                {
                    $day = date('d', strtotime($data->lh_startdate)) . ' / ' . date('d', strtotime($data->lh_enddate));
                    $start_str = date('F', strtotime($data->lh_startdate));
                    $end_str = date('F', strtotime($data->lh_enddate));
                    $start_mo = substr($start_str, 0, 3);
                    $end_mo = substr($end_str, 0, 3);
                    
                    echo "
                    <tr class='ccid tag$i'>
                        <td class='tag center' id='$data->ci_acctno'>$data->lh_istop</td>
                        <td title='" . date('M d, Y',strtotime($data->lh_loandate)) . "'>$data->lh_pn</td>
                        <td class='right'>" . number_format($data->lh_balance, 2) . "</td>
                        <td class='right'>" . number_format($data->lh_monthlyamort, 2) . "</td>
                        <td class='right'>$data->lh_terms</td>
                        <td>" . date('M Y', strtotime($data->lh_startdate)) . " - " . date('M Y', strtotime($data->lh_enddate)) . "</td>
                        <td>$data->lh_loantrans</td>
                    </tr>";
                    
                    $i = $i ? 0 : 1;
                }
            }
            
            ?>
        </tbody>
    </table>
</body>
<script src="<?= base_url() ?>system/application/assets/js/jquery-2.1.3.min.js" type="text/javascript"></script>
<script type="text/javascript">
    var main = function() {
        $('.tag').click(function(){
            $.post('../update_loan_istop', {data: $(this).prop('id') + ';' + $(this).siblings().eq(0).text() + ';' + $(this).html()}, function(r)
            {
                eval(r);
            })
        });
        
        $('.ccid').click(function(){
            $('.ccid').removeClass('active');
            $(this).addClass('active');
        })
    }
    
    $(document).ready(main);
</script>