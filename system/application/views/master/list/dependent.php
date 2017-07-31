<style>
    body,table{
        font-family: 'Arial';
        font-size: 12px;
        cursor: default;
        margin: 0;
        padding: 0;
    }

    table.list {
        border-collapse: collapse;
    }
    
    table.list th {
        border-bottom: 2px solid #999;
        background-color: #eee;
        vertical-align: bottom;
    }
    
    table.list td {
        border-bottom: 1px solid #ccc;
    }
    
    table.list td.alt {
        background-color: #ffc; background-color: rgba(255, 255, 0, 0.2);
    }
    
    .active {
        background-color: #ffff00;
        color: #ff0000;
    }
</style>
<body>
    <table class="list" style="width: 420px;">
        <tbody>
        <?
        
        if(!empty($results)):
            $i = 0;
            
            foreach($results as $data):
                echo "
                <tr class='ccid tag$i'>
                    <td>$data->CD_LName, $data->CD_FName</td>
                    <td style='display: none;'>$data->SysID</td>
                    <td style='display: none;'>$data->CI_AcctNo</td>
                    <td style='display: none;'>$data->CD_FName</td>
                    <td style='display: none;'>$data->CD_LName</td>
                    <td style='display: none;'>$data->CD_Relation</td>
                    <td style='display: none;'>$data->CD_BDate</td>
                    <td style='display: none;'>$data->CD_SSSNo</td>
                    <td style='display: none;'>$data->CD_Profession</td>
                    <td style='display: none;'>$data->CD_IsSurvivingDep</td>
                </tr>";
                
                $i = $i ? 0 : 1;
            endforeach;
        endif;
        
        ?>
        </tbody>
    </table>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">
    var main = function() {
        $('.ccid').click(function() {
            var dependent = $('#dependentTab', parent.document).contents();
            var data = $(this).children('td');
            var is_sd = (data[9].innerHTML == '1' ? true : false);
            
            dependent.find('#sysid').val(data[1].innerHTML);
            dependent.find('#client').val(data[2].innerHTML);
            dependent.find('#cd_fname').val(data[3].innerHTML).select();
            dependent.find('#cd_lname').val(data[4].innerHTML);
            dependent.find('#cd_relation').val(data[5].innerHTML);
            dependent.find('#cd_bdate').val(data[6].innerHTML);
            dependent.find('#cd_sssno').val(data[7].innerHTML);
            dependent.find('#cd_profession').val(data[8].innerHTML);
            dependent.find('#cd_issurvivingdep').prop('checked', is_sd);
            
            var menu = $('#menu', parent.document).contents();
            var is_visible = menu.find('#edit-btn').is(':visible');
            if(is_visible == false)
            {
                dependent.find('#cancel_dep-btn, #delete_dep-btn, #update_dep-btn').prop('disabled', false);
                dependent.find('#add_dep-btn').prop('disabled', true);
            } else {
               dependent.find('#cancel_dep-btn, #update_dep-btn').prop('disabled', true);
            }
            
            $('.ccid').removeClass('active');
            $(this).addClass('active');
        });
    }
    
    $(document).ready(main);
</script>