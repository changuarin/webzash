<style type="text/css">
    body, table, textarea {
        font-family: 'Arial', sans-serif;
        font-size: 12px;
    }
    
    img, input, select, textarea {
        border: 1px solid #0099cc;
    }
    
    input[type=date]:enabled, input[type=text]:enabled, select:enabled, textarea:enabled {
        background-color: #ffff99;
        border: 1px solid #ff0000;
    }
    
    input[type=date]:disabled, input[type=text]:disabled, select:disabled, textarea:disabled {
        background-color: #fff !important;
        border: 1px solid #0099cc;
    }
    
    .center {
        text-align: center;
    }
    
    .right {
        text-align: right;
    }
    
    .top {
        vertical-align: top;
    }
    
    .error {
        background-color: #fc331c !important;
    }
    
    label.error {
        display: none !important;
    }
    
    label.enabled {
        color: #ff0000;
        cursor: pointer;
    }
    
    .tabHeadings {
        background-color: #09c;
        color: #fff;
        font-size: 12px;
        margin-top: 10px;
        margin-bottom: 10px;
        padding: 10px 5px;
    }
    
    #menu {
        border: 0;
        position: fixed;
        top: 15px;
        right: 15px;
    }
    
    #error_list {
        list-style: none;
        padding-left: 5px;
    }

    #error_list li {
        color: #ff0000;
        padding: 5px 10px;
    }

    #system_message {
        color: #ff0000;
    }
</style>
<body>
    <div id="menu">
        <table>
            <tbody>
                <tr>
                    <td>
                        <input id="cancel-btn" type="button" value="Cancel" />
                        <input id="edit-btn" type="button" value="Edit" />
                        <input id="new-btn" type="button" value="New" />
                        <input id="submit-btn" type="button" value="Submit" />
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div id="main">
        <form id="comaker_form" method="post">
            <h3 class="tabHeadings"tag="comakerTab">Co-Maker Information</h3>
            <div id="comakerTab" class="divTabs">
                <div id="system_message">
                    <?= isset($system_message) ? $system_message : '' ?>
                    <ul id="error_list">
                    <?php echo validation_errors() ?>
                    </ul>
                </div>
                <table border="0" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr>
                            <td class="right">
                                Reference No.
                                &nbsp;
                            </td>
                            <td>
                                <input id="cm_refno" type="text" name="cm_refno" required />
                            </td>
                            <td class="right">
                                Branch
                                &nbsp;
                                <select id="cm_branchcode" name="cm_branchcode" required>
                                <?php
                                                                
                                if(!empty($results)):
                                        foreach($results as $data):
                                                echo "<option value='$data->code' " . ($data->code == $branch_code ? 'selected' : '' ) . ">$data->name</option>";
                                        endforeach;
                                endif;
                                                                                                
                                ?>
                                </select>
                                &nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td class="right">
                                First Name
                                &nbsp;
                            </td>
                            <td>
                                <input id="cm_fname" type="text" name="cm_fname" required />
                            </td>
                            <td colspan="2">
                                &nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td class="right">
                                Middle Name
                                &nbsp;
                            </td>
                            <td>
                                <input id="cm_mname" type="text" name="cm_mname" />
                            </td>
                            <td colspan="2">
                                &nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td class="right">
                                Last Name
                                &nbsp;
                            </td>
                            <td>
                                <input id="cm_lname" type="text" name="cm_lname" required />
                            </td>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td class="right">
                                Birth Date
                                &nbsp;
                            </td>
                            <td>
                                <input class="center" id="cm_bdate" type="date" name="cm_bdate" value="<?=date('Y-m-d')?>" required />
                            </td>
                            <td colspan="2">
                                <input class="center" id="age" type="text" name="age" />
                            </td>
                        </tr>
                        <tr>
                            <td class="right">
                                Sex
                                &nbsp;
                            </td>
                            <td colspan="3">
                                <select id="cm_sex" name="cm_sex">
                                    <option value="M">Male</option>
                                    <option value="F">Female</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="right">
                                Civil Status
                                &nbsp;
                            </td>
                            <td colspan="3">
                                <select id="cm_civilstatus" name="cm_civilstatus">
                                    <option value="S">Single</option>
                                    <option value="M">Married</option>
                                    <option value="W">Widow</option>
                                    <option value="SP">Separated</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="right">
                                Tel. No
                                &nbsp;
                            </td>
                            <td colspan="3">
                                <input id="cm_telno" type="text" name="cm_telno" />
                            </td>
                        </tr>
                        <tr>
                            <td class="right">
                                Mobile No
                                &nbsp;
                            </td>
                            <td colspan="3">
                                <input id="cm_mobileno" type="text" name="cm_mobileno" />
                            </td>
                        </tr>
                        <tr>
                            <td class="right top">
                                Present Address
                                &nbsp;
                            </td>
                            <td colspan="3">
                                <textarea id="cm_add1" name="cm_add1" rows="3" required></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td class="right top">
                                Permanent Address
                                &nbsp;
                            </td>
                            <td colspan="3">
                                <textarea id="cm_add2" name="cm_add2" rows="3"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td class="right">
                                Res. Cert. No.
                                &nbsp;
                            </td>
                            <td colspan="3">
                                <input id="cm_cedulano" type="text" name="cm_cedulano" required />
                            </td>
                        </tr>
                        <tr>
                            <td class="right">
                                Issued At
                                &nbsp;
                            </td>
                            <td colspan="3">
                                <input id="cm_cedulaplace" type="text" name="cm_cedulaplace" />
                            </td>
                        </tr>
                        <tr>
                            <td class="right">
                                Issued Date
                                &nbsp;
                            </td>
                            <td colspan="3">
                                <input class="center" id="cm_ceduladate" type="date" name="cm_ceduladate" value="<?=date('Y-m-d')?>" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</body>
<script src="<?= base_url() ?>system/application/assets/js/jquery-2.1.3.min.js" type="text/javascript"></script>
<script src="<?= base_url() ?>system/application/assets/js/jquery.validate.min.js" type="text/javascript"></script>
<script type="text/javascript">
    function getAge(birthdate)
    {
        var today = new Date();
        var birthdate = new Date(birthdate);
        var age = today.getFullYear() - birthdate.getFullYear();
        var month = today.getMonth() - birthdate .getMonth();
        if(month < 0 || (month === 0 && today.getDate() < birthdate.getDate()))
        {
            age--;
        }
        
        return age;
    }
    
    var main = function() {
        var comaker = $('#comakerTab').contents();
        
        /* menu function */
        $('#cancel-btn').click(function() {
            main.find('input, select, textarea').prop('disabled', true);
            main.find('input[type=hidden], input[type=text], textarea').val('');
            
            $('#cancel-btn, #submit-btn').hide();
            $('#new-btn').show();
            
            var comaker_list = $('#comaker_list', parent.document).contents();
            comaker_list.find('.active').removeClass('active');
        });
        
        $('#edit-btn').click(function() {
            comaker.find('input, select, textarea').prop('disabled', false);
            comaker.find('#cm_fname').select();
            
            $('#edit-btn, #new-btn').hide();
            $('#cancel-btn, #submit-btn').show();
            $('#comaker_form').prop('action', 'update_comaker');
        });
        
        $('#new-btn').click(function() {
            comaker.find('input, select, textarea').prop('disabled', false);
            comaker.find('input[type=hidden], input[type=text], textarea').val('');
            comaker.find('#cm_fname').select();
            
            $('#edit-btn, #new-btn, #system_message').hide();
            $('#cancel-btn, #submit-btn').show();
            $('#comaker_form').prop('action', 'insert_comaker');
            
            $.post('generate_cm_refno', function(r)
            {
                eval(r);
            });
            
            var comaker_list = $('#comaker_list', parent.document).contents();
            comaker_list.find('.active').removeClass('active');
        });
        
        $('#submit-btn').click(function() {
            var comaker_list = $('#comaker_list', parent.document).contents();
            comaker_list.find('.active').removeClass('active');
            
            $('#comaker_form').submit();
        });
        
        /* main functions */
        $('#cm_bdate').blur(function() {
            var birthdate = getAge($(this).val());
            $('#age').val(birthdate);
        });
        
        $('input[type=text], textarea').keyup(function() {
            var string = $(this).val().toUpperCase();
            $(this).val(string);
        })
        
        $('#comaker_form').validate();
        
        var main = $('#main').contents();
        main.find('input, select, textarea').prop('disabled', true);
        
        var menu = $('#menu').contents();
        menu.find('input[type=button]').hide();
        menu.find('#new-btn').show();
    }
    
    $(document).ready(main);
</script>