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
    
    #billing, #process {
        color: #fff;
        cursor: pointer;
        font-weight: 600;
    }
    
    #billing:hover, #process:hover {
        color: #ff0000;
    }
    
    #dependent_process, #client_process {
        color: #ff0000;
        list-style: none;
        padding-bottom: 5px;
        padding-left: 5px;
    }

     #dependent_process li, #client_process li {
        color: #ff0000;
    }
    
    #loan_list {
        width: 720px;
    }
    
    #menu {
        border: 0;
        position: fixed;
        top: 15px;
        right: 15px;
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
        <form id="client_form" method="post">
            <h3 class="tabHeadings"tag="clientTab">Client Information</h3>
            <div id="clientTab" class="divTabs">
                <div id="client_process">
                    <?= isset($system_message) ? $system_message : '' ?>
                </div>
                <table border="0" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr>
                            <td class="right">
                                <select id="ci_source" name="ci_source" required>
                                    <option value="GSIS">GSIS</option>
                                    <option value="OTHERS">OTHERS</option>
                                    <option value="PVAO">PVAO</option>
                                    <option value="SSS" selected>SSS</option>
                                </select>
                                &nbsp;
                            </td>
                            <td colspan="3"><input id="ci_sssno" type="text" name="ci_sssno" required /></td>
                        </tr>
                        <tr>
                            <td class="right">
                                Client Code
                                &nbsp;
                            </td>
                            <td>
                                <input id="ci_acctno" type="text" name="ci_acctno" required />
                            </td>
                            <td class="right">
                                &nbsp;
                                Status
                                &nbsp;
                                <select id="ci_status" name="ci_status" required>
                                    <option value="A">Active</option>
                                    <option value="I">Inactive</option>
                                </select>
                                &nbsp;
                            </td>
                            <td rowspan="8" style="text-align: center; vertical-align: top;">
                                <img id="perimg" src="<?=base_url()?>system/application/assets/images/no_photo.jpg" style="width: 120px; height: 140px;" />
                                <input id="ci_picture" type="hidden" name="ci_picture" />
                                <br/>
                                <input id="upload_pic-btn" type="button" value="Upload" />
                            </td>
                        </tr>
                        <tr>
                            <td class="right">
                                First Name
                                &nbsp;
                            </td>
                            <td>
                                <input id="ci_fname" type="text" name="ci_fname" required />
                            </td>
                            <td class="right">
                                Group
                                &nbsp;
                                <select id="ci_grp" name="ci_grp" required>
                                    <option value="N">NEW</option>
                                    <option value="O">OLD</option>
                                </select>
                                &nbsp;
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="right">
                                Middle Name
                                &nbsp;
                            </td>
                            <td>
                                <input id="ci_mname" type="text" name="ci_mname" />
                            </td>
                            <td class="right">
                                Type
                                &nbsp;
                                <select id="ci_type" name="ci_type" required>
                                    <option value="EMP">ACCOM-EMP</option>
                                    <option value="AGT">ACCOM-AGENT</option>
                                    <option value="PEN" selected>CLIENT-GSIS/SSS/PNP</option>
                                    <option value="SAL">CLIENT-SALARY</option>
                                </select>
                                &nbsp;
                            </td>
                            <td>
                                &nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td class="right">
                                Last Name
                                &nbsp;
                            </td>
                            <td>
                                <input id="ci_lname" type="text" name="ci_lname" required />
                            </td>
                            <td class="right">
                                Branch
                                &nbsp;
                                <select id="ci_branchcode" name="ci_branchcode">
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
                            <td>
                                &nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td class="right">
                                Birth Date
                                &nbsp;
                            </td>
                            <td>
                                <input class="center" id="ci_bdate" type="date" name="ci_bdate" value="<?= date('Y-m-d') ?>" required />
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
                                <select id="ci_sex" name="ci_sex" required>
                                    <option value="M">Male</option>
                                    <option value="F">Female</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="right">
                                Status
                                &nbsp;
                            </td>
                            <td colspan="3">
                                <select id="ci_civilstatus" name="ci_civilstatus" required>
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
                                <input id="ci_telno" type="text" name="ci_telno" />
                            </td>
                        </tr>
                        <tr>
                            <td class="right">
                                Mobile No
                                &nbsp;
                            </td>
                            <td colspan="3">
                                <input id="ci_mobileno" type="text" name="ci_mobileno" />
                            </td>
                        </tr>
                        <tr>
                            <td class="right top">
                                Present Address
                                &nbsp;
                            </td>
                            <td>
                                <textarea id="ci_add1" name="ci_add1" rows="3" required></textarea>
                            </td>
                            <td class="right top">
                                Permanent Address
                                &nbsp;
                            </td>
                            <td>
                                <textarea id="ci_add2" name="ci_add2" rows="3"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td class="right">
                                Res. Cert. No.
                                &nbsp;
                            </td>
                            <td>
                                <input id="ci_cedulano" type="text" name="ci_cedulano" required />
                            </td>
                            <td class="right" colspan="2">
                                &nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td class="right">
                                Issued At
                                &nbsp;
                            </td>
                            <td>
                                <input id="ci_cedulaplace" type="text" name="ci_cedulaplace" />
                            </td>
                            <td class="right" colspan="2">
                                &nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td class="right">
                                Issued Date
                                &nbsp;
                            </td>
                            <td>
                                <input class="center" id="ci_ceduladate" type="date" name="ci_ceduladate" value="<?= date('Y-m-d') ?>" />
                            </td>
                            <td class="right" colspan="2">
                                &nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td class="right">
                                Agent
                                &nbsp;
                            </td>
                            <td>
                                <input id="agent1_name" type="text" />
                                <input id="ci_agent1" type="hidden" name="ci_agent1" />
                            </td>
                            <td colspan="2">
                                <input class="center" id="ci_agent1_rate" type="text" name="ci_agent1_rate" />
                                <input id="clr_agent1-btn" type="button" value="Clear" />
                            </td>
                        </tr>
                        <tr>
                            <td class="right">
                                Sub-Agent
                                &nbsp;
                            </td>
                            <td>
                                <input id="agent2_name" type="text" />
                                <input id="ci_agent2" type="hidden" name="ci_agent2" />
                            </td>
                            <td colspan="2">
                                <input class="center" id="ci_agent2_rate" type="text" name="ci_agent2_rate" />
                                <input id="clr_agent2-btn" type="button" value="Clear" />
                            </td>
                        </tr>
                        <tr>
                            <td class="right">
                                Co-Maker
                                &nbsp;
                            </td>
                            <td colspan="3">
                                <input id="comaker_name" type="text" />
                                <input id="ci_comaker" type="hidden" name="ci_comaker" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                &nbsp;
                            </td>
                            <td class="right">
                                Problem Account
                                &nbsp;
                                <input id="ci_problemacct" type="checkbox" name="ci_problemacct" value=">PA" />
                            </td>
                            <td>
                                &nbsp;
                            </td>
                            <td>
                                &nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td>
                                &nbsp;
                            </td>
                            <td class="right">
                                Arrears
                                &nbsp;
                                <input id="arrears" type="checkbox" name="arrears" value=">AR" />
                            </td>
                            <td colspan="2">
                                Remarks
                                &nbsp;
                                <input id="ci_remarks" type="text" name="ci_remarks" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <h3 class="tabHeadings"tag="pensionTab">Pension Information</h3>
            <div id="pensionTab" class="divTabs">
                <table border="0" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr>
                            <td class="right">
                                ITF
                                &nbsp;
                            </td>
                            <td>
                                <input id="cp_itf" type="text" name="cp_itf" />
                            </td>
                        </tr>
                        <tr>
                            <td class="right">
                                Pension Type
                                &nbsp;
                            </td>
                            <td colspan="3">
                                <input id="cp_pensiontype" type="text" name="cp_pensiontype" />
                                <input id="pensiontype" type="text" name="pensiontype" />
                            </td>
                        </tr>
                        <tr>
                           <td class="right">
                               Auto-Debit No.
                               &nbsp;
                           </td>
                           <td>
                               <input id="cp_adno" type="text" name="cp_adno" />
                           </td>
                        </tr>
                        <tr>
                            <td class="right">
                                Bank/Branch
                                &nbsp;
                            </td>
                            <td colspan="3">
                                <input id="cp_bankbranch" type="text" name="cp_bankbranch" required />
                            </td>
                        </tr>
                        <tr>
                            <td class="right">
                                Bank Acct. #
                                &nbsp;
                            </td>
                            <td colspan="3">
                                <input id="cp_bankacctno" type="text" name="cp_bankacctno" required />
                            </td>
                        </tr>
                        <tr>
                            <td class="right">
                                Amount
                                &nbsp;
                            </td>
                            <td colspan="3">
                                <input class="center" id="cp_amount" type="text" name="cp_amount" required />
                            </td>
                        </tr>
                        <tr>
                            <td class="right">
                                Withdrawal Day
                                &nbsp;
                            </td>
                            <td colspan="3">
                                <select id="cp_withdrawalday" name="cp_withdrawalday" required>
                                <?php
                                
                                for($i=0 ; $i <= 31; $i++):
                                    echo '<option value="' . $i . '">' . $i . '</option>';
                                endfor;
                                
                                ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="right">
                                Type
                                &nbsp;
                            </td>
                            <td colspan="3">
                                <select id="cp_ptype" name="cp_ptype" required>
                                    <option value="ATM">ATM</option>
                                    <option value="PB">PB</option>
                                    <option value="CASH">CASH</option>
                                    <option value="CHECK">CHECK</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                &nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td class="right">
                                Cause of Death
                                &nbsp;
                            </td>
                            <td>
                                <input id="cp_causeofdeath" type="text" name="cp_causeofdeath" />
                            </td>
                        </tr>
                        <tr>
                            <td class="right">
                                Date of Death
                                &nbsp;
                            </td>
                            <td>
                                <input class="center" id="cp_dateofdeath" type="date" name="cp_dateofdeath" value="<?= date('Y-m-d') ?>" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Cause of Disability
                                &nbsp;
                            </td>
                            <td>
                                <input id="cp_disability" type="text" name="cp_disability" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </form>
        
        <form id="dependent_form" method="post">
            <h3 class="tabHeadings"tag="dependentTab">Dependent Information</h3>
            <div id="dependentTab" class="divTabs">
                <div id="dependent_process">
                </div>
                <table border="0" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr>
                            <td class="right">
                                First Name
                                &nbsp;
                            </td>
                            <td colspan="3">
                                <input id="sysid" type="hidden"  name="sysid" required />
                                <input id="client" type="hidden" name="client" required />
                                <input id="cd_fname" type="text" name="cd_fname" required />
                            </td>
                        </tr>
                        <tr>
                            <td class="right">
                                Last Name
                                &nbsp;
                            </td>
                            <td colspan="3">
                                <input id="cd_lname" type="text" name="cd_lname" required />
                            </td>
                        </tr>
                        <tr>
                            <td class="right">
                                Relation
                                &nbsp;
                            </td>
                            <td colspan="3">
                                <input id="cd_relation" type="text" name="cd_relation" />
                            </td>
                        </tr>
                        <tr>
                            <td class="right">
                                Birth Date
                                &nbsp;
                            </td>
                            <td colspan="3">
                                <input class="center" id="cd_bdate" type="date" name="cd_bdate" value="<?=date('Y-m-d')?>" required />
                            </td>
                        </tr>
                        <tr>
                            <td class="right">
                                Profession
                                &nbsp;
                            </td>
                            <td colspan="3">
                                <input id="cd_profession" type="text" name="cd_profession" />
                            </td>
                        </tr>
                        <tr>
                            <td class="right">
                                SSS No.
                                &nbsp;
                            </td>
                            <td colspan="3">
                                <input id="cd_sssno" type="text" name="cd_sssno" />
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <input id="cd_issurvivingdep" type="checkbox" name="cd_issurvivingdep" value="is_sd">
                                Surviving Dependent
                                &nbsp;
                            </td>
                            <td colspan="3">
                                <input id="add_dep-btn" type="button"  value="Add" />
                                <input id="update_dep-btn" type="button" value="Update" />
                                <input id="delete_dep-btn" type="button" value="Delete" />
                                <input id="cancel_dep-btn" type="button" value="Cancel" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <iframe id="dependent_list"></iframe>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </form>
        
        <h3 class="tabHeadings tabs" tag="loan_listTab">Loan List</h3>
        <div id="loan_listTab"class="divTabs">
            <table cellpadding="0" cellspacing="0">
                <tbody>
                    <tr>
                        <td colspan="4" style="padding-bottom:4px;">
                            <input id="ob_card-btn" type="button" value="OB Card">
                            <input id="reprint_comp-btn" type="button" value="Reprint Comp">
                            <input id="reprint_docs-btn" type="button" value="Reprint Docs">
                            <input id="view_ledger-btn" type="button" value="Client Ledger">
                            <input id="billing-btn" type="button" value="Billing">
                            &nbsp;
                            &nbsp;
                            &nbsp;
                            &nbsp;
                            &nbsp;
                            &nbsp;
                            &nbsp;
                            &nbsp;
                            <span style="display: ;" id="billing">Billing</span>
                            &nbsp;
                            &nbsp;
                            <span style="display: ;" id="process">Process</span>
                        </td>
                    <tr>
                        <td colspan="4">
                            <iframe id="loan_list"></iframe>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <script src="<?= base_url() ?>system/application/assets/js/jquery-2.1.3.min.js" type="text/javascript"></script>
    <script src="<?= base_url() ?>system/application/assets/js/numeral.js" type="text/javascript"></script>
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
        
        function popupWindow(height, width, url)
        {
            var top = (screen.height/2) - (height/2);
            var left = (screen.width/2) - (width/2);
            return window.open(url, 'popupWindow', 'width=' + width + ', height=' + height + ', top=' + top + ', left=' + left + ',scrollbars=yes').focus();
        }
        
        var main = function() {
            var client = $('#clientTab').contents();
            var pension = $('#pensionTab').contents();
            var dependent = $('#dependentTab').contents();
            var loan = $('#loan_listTab').contents();
            
            /* Menu functions */
            $('#cancel-btn').click(function() {
                client.find('input, select, textarea').prop('disabled', true);
                client.find('input[type=hidden], input[type=text], textarea').val('');
                client.find('input[type=checkbox]').prop('checked', false);
                client.find('#perimg').prop('src', '<?= base_url() ?>system/application/assets/images/no_photo.jpg');
                
                pension.find('input, select').prop('disabled', true);
                pension.find('input[type=text]').val('');
                
                dependent.find('input').prop('disabled', true);
                dependent.find('input[type=text]').val('');
                dependent.find('input[type=checkbox]').prop('checked', false);
                dependent.find('#dependent_list').prop('src', '');
                
                loan.find('input[type=button]').prop('disabled', true);
                loan.find('#loan_list').prop('src', '');
                
                $('#cancel-btn, #submit-btn').hide();
                $('#new-btn').show();
                
                var client_list = $('#client_list', parent.document).contents();
                client_list.find('.active').removeClass('active');
            });
            
            $('#edit-btn').click(function() {
                client.find('input, select, textarea').prop('disabled', false);
                
                pension.find('input, select').prop('disabled', false);
                
                dependent.find('input[type=date]').prop('disabled', false);
                dependent.find('input[type=text], input[type=hidden]').prop('disabled', false).val('');
                dependent.find('input[type=checkbox]').prop('disabled', false).prop('checked', false);
                dependent.find('#add_dep-btn').prop('disabled', false);
                dependent.find('#dependent_list').prop('src', 'dependent_list/' + $('#ci_acctno').val());
                
                loan.find('input[type=button]').prop('disabled', true);
                loan.find('#loan_list').prop('src', '');
                
                $('#edit-btn, #new-btn').hide();
                $('#cancel-btn, #submit-btn').show();
                $('#client_form').prop('action', 'update_client');
            });
            
            $('#new-btn').click(function() {
                client.find('input, select, textarea').prop('disabled', false);
                client.find('input[type=hidden], input[type=text], textarea').val('');
                client.find('input[type=checkbox]').prop('checked', false);
                client.find('#perimg').prop('src', '<?= base_url() ?>system/application/assets/images/no_photo.jpg');
                
                pension.find('input, select').prop('disabled', false);
                pension.find('input[type=text]').val('');
                
                dependent.find('input[type=text]').val('');
                dependent.find('input[type=checkbox]').prop('checked', false);
                dependent.find('#dependent_list').prop('src', '');
                
                loan.find('input').prop('disabled', true);
                loan.find('#loan_list').prop('src', '');
                
                $('#edit-btn, #new-btn').hide();
                $('#cancel-btn, #submit-btn').show();
                $('#client_form').prop('action', 'insert_client');
                
                $.post('generate_ci_acctno', function(r)
                {
                    eval(r);
                });
                
                $.post('reset_client_form', function(r)
                {
                    eval(r);
                });
                
                var client_list = $('#client_list', parent.document).contents();
                client_list.find('.active').removeClass('active');
            });
            
            $('#submit-btn').click(function() {
                if(confirm('Submit client form?'))
                {
                        var client_list = $('#client_list', parent.document).contents();
                        client_list.find('.active').removeClass('active');
                        
                        $('#client_form').submit();
                }
            });
            
            /* Main functions */ 
            $('#upload_pic-btn').click(function() {
                height = 360; width = 440;
                url = 'picture_form';
                popupWindow(height, width, url);
            });
            
            $('#ci_bdate').blur(function() {
                var birthdate = getAge($(this).val());
                $('#age').val(birthdate);
            });
            
            $('#cp_amount').blur(function() {
                var value = $(this).val();
                var amount = numeral(value).format('0,0.00');
                $(this).val(amount);
            });
            
            $('#agent1_name, #agent2_name').click(function() {
                var id = $(this).prop('id');
                if(id == 'agent1_name')
                {
                    var uri_seg = 'agent1_list';
                } else if(id=='agent2_name') {
                    var uri_seg = 'agent2_list';
                }
                height = 480; width = 420;
                url = 'agent_list/' + uri_seg;
                popupWindow(height, width, url);
            });
            
            $('#clr_agent1-btn').click(function() {
                $('#agent1_name, #ci_agent1, #ci_agent1_rate').val('');
            });
            
            $('#clr_agent2-btn').click(function() {
                $('#agent2_name, #ci_agent2, #ci_agent2_rate').val('');
            });
            
            $('#comaker_name').click(function() {
                height = 480; width = 420;
                url = 'comaker_list';
                popupWindow(height, width, url)
            });
            
            $('#cp_pensiontype').click(function() {
                height = 280; width = 420;
                url = 'pension_type_list/' + $('#ci_source').val();
                popupWindow(height, width, url);
            });
            
            $('#add_dep-btn').click(function() {
                if(confirm('Add dependent?'))
                {
                        var is_checked = $('#cd_issurvivingdep').is(':checked');
                        if(is_checked == true)
                        {
                                is_sd = 1;
                        } else {
                                is_sd = 0;
                        }
                        $.post('insert_dependent', {ci_acctno: $('#ci_acctno').val(), sysid: dependent.find('#sysid').val(), cd_fname: dependent.find('#cd_fname').val(), cd_lname: dependent.find('#cd_lname').val(), cd_bdate: dependent.find('#cd_bdate').val(), cd_profession: dependent.find('#cd_profession').val(), cd_relation: dependent.find('#cd_relation').val(), cd_sssno: dependent.find('#cd_sssno').val(), cd_issurvivingdep: is_sd}, function(r)
                        {
                                eval(r);
                        });
                }
            });
            
            $('#delete_dep-btn').click(function() {
                if(confirm('Delete dependent?'))
                {
                        $.post('delete_dependent', {ci_acctno: $('#ci_acctno').val(), sysid: dependent.find('#sysid').val(), cd_fname: dependent.find('#cd_fname').val(), cd_lname: dependent.find('#cd_lname').val()}, function(r)
                        {
                                eval(r);
                        });
                }
            });
            
            $('#update_dep-btn').click(function() {
                if(confirm('Update dependent?'))
                {
                        var is_checked = $('#cd_issurvivingdep').is(':checked');
                        if(is_checked == true)
                        {
                                is_sd = 1;
                        } else {
                                is_sd = 0;
                        }
                        $.post('update_dependent', {ci_acctno: $('#ci_acctno').val(), sysid: dependent.find('#sysid').val(), cd_fname: dependent.find('#cd_fname').val(), cd_lname: dependent.find('#cd_lname').val(), cd_bdate: dependent.find('#cd_bdate').val(), cd_profession: dependent.find('#cd_profession').val(), cd_relation: dependent.find('#cd_relation').val(), cd_sssno: dependent.find('#cd_sssno').val(), cd_issurvivingdep: is_sd}, function(r)
                        {
                                eval(r);
                        });
                }
            });
            
            $('#cancel_dep-btn').click(function() {
                $('#add_dep-btn').prop('disabled', false);
                $('#cancel_dep-btn, #delete_dep-btn, #update_dep-btn').prop('disabled', true);
                dependent.find('input[type=text], input[type=hidden]').val('');
                dependent.find('input[type=checkbox]').prop('checked', false);
                
                var dependent_list = dependent.find('#dependent_list').contents();
                dependent_list.find('.active').removeClass('active');
            });
            
            $('#ob_card-btn').click(function() {
                height = 480;width = 960;
                url = 'obcard/' + $('#ci_acctno').val();
                popupWindow(height, width, url);
            });
            
             $('#reprint_docs-btn').click(function(){
                height = 540; width = 840;
                var loan_list = $('#loan_list').contents();
                var lh_pn = loan_list.find('.active').children('td').eq(1).text();
                url = '../sales/documents/' + $('#ci_acctno').val() + '/' + lh_pn;
                if(lh_pn)
                {
                    popupWindow(height, width, url);
                } else {
                    alert('Select PN.');
                }
            });
            
            $('#reprint_comp-btn').click(function(){
                height = 540; width = 840;
                var loan_list = $('#loan_list').contents();
                var lh_pn = loan_list.find('.active').children('td').eq(1).text();
                url = '../sales/loanComputation/0/' + $('#ci_acctno').val() + '/' + lh_pn
                if(lh_pn)
                {
                    popupWindow(height, width, url);
                } else {
                    alert('Select PN.');
                }
            });
            
            $('#view_ledger-btn').click(function() {
                height = 660; width = 880;
                var loan_list = $('#loan_list').contents();
                var lh_pn = loan_list.find('.active').children('td').eq(1).text();
                url = 'clientLedgerList/' + $('#ci_acctno').val() + '/' + lh_pn;
                if(lh_pn)
                {
                    popupWindow(height, width, url);
                } else {
                    alert('Select PN.')
                }
            });
            
            $('#billing-btn').click(function(){
                var height = 640; var width = 880;
                url = 'billingList/'+$('#ci_acctno').val();
                popupWindow(height, width, url);
            });
            
             $('#billing').click(function() {
                height = 600; width = 620;
                var top = (screen.height/2)-(height/2); var left = (screen.width/2)-(width/2);
                
                var ci_acctno = $('#ci_acctno').val();
                var loan_list = $('#loan_list').contents();
                var lh_pn = loan_list.find('.active').children('td').eq(1).text();
                
                url = '../collection/client_billing_list/' + ci_acctno + '/' + lh_pn;
                if(lh_pn)
                {
                    popupWindow(height, width, url);
                } else {
                    alert('Select PN.')
                }
            });
            
             $('#process').click(function() {
                height = 660; width = 880;
                var loan_list = $('#loan_list').contents();
                var lh_pn = loan_list.find('.active').children('td').eq(1).text();
                url = 'process/' + $('#ci_acctno').val() + '/' + lh_pn;
                if(lh_pn)
                {
                    popupWindow(height, width, url);
                } else {
                    alert('Select PN.')
                }     
            });
            
            $('#client_form').validate();
            
            $('input[type=text], textarea').keyup(function() {
                var string = $(this).val().toUpperCase();
                $(this).val(string);
            })
                        
            $('input, select, textarea').click(function() {
               $('#client_process, #dependent_process').hide();
            });
            
            var main = $('#main').contents();
            main.find('input, select, textarea').prop('disabled', true);
            
            var menu = $('#menu').contents();
            menu.find('input[type=button]').hide();
            menu.find('#new-btn').show();
        }
        
        $(document).ready(main);
    </script>
</body>