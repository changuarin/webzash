<style>
    body, form, table {
        font-family: 'Arial';
        font-size: 12px;
        cursor: default;
        margin: 0;
        padding: 0;
    }
    
    span {
        color: #ff0000;
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
    
        margin-top: 50px;
        width: 420px;
    }
    
    table#list {
        border-collapse: collapse;
    }
    
    table#list th {
        border-bottom: 2px solid #999;
        background-color: #eee;
        vertical-align: bottom;
    }
    
    table#list td {
        border-bottom: 1px solid #ccc;
    }
    
    table#list td.alt {
        background-color: #ffc; background-color: rgba(255, 255, 0, 0.2);
    }
    
    .active {
        background-color: #ffff00;
        color: #ff0000;
    }
    
    #menu_form {
        margin-top: 50px
    }
    
    #menu {
        background-color: #fff;
        border: 0;
        position: fixed;
        top: 0;
        width: 420px;
    }
</style>
<body>
    <form id="menu_form" method="post">
        <table id="menu">
            <tbody>
                <tr>
                    <td>
                        Last Name:&nbsp;
                        <input id="cm_lname" type="text" name="cm_lname" value="<?= isset($cm_lname) ? $cm_lname : 'CRUZ' ?>" />
                        <input type="submit" value="Search" />
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
    <table id="list">
        <tbody>
        <?php
        
        $i = 0;
        if(!empty($results)):
            foreach($results as $data):
                echo "
                <tr>
                    <td class='ccid tag$i'>$data->cm_refno $data->name</td>
                </tr>";
                    
                $i = $i ? 0 : 1;
            endforeach;
        endif;
        
        ?>
        </tbody>
    </table>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js" type="text/javascript"></script>
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/jquery.validate.min.js" type="text/javascript"></script>
<script type="text/javascript">
    var main = function()
    {
        $('.ccid').click(function()
        {
            var module = $('#comaker_list', parent.document).prop('class');
            if(module == 'master_comaker')
            {
                $.post('fetch_comaker_details', {data: $(this).html()}, function(r)
                {
                    eval(r);
                });
            } else {
                $.post('fill_comaker_input', {data: $(this).html(), module: $('#comaker_list', window.opener.document).prop('class') }, function(r)
                {
                    eval(r);
                });
            }
            
            $('.ccid').removeClass('active');
            $(this).addClass('active');
        });
        
        $('input[type=text], textarea').keyup(function() {
            var string = $(this).val().toUpperCase();
            $(this).val(string);
        });
        
        $('#ci_type, #ci_status').change(function() {
            $('#client_list').submit();
        });
    }
    
    $(document).ready(main);
</script>