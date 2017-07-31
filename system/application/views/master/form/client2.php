<style type="text/css">
	body,
	table,
	textarea {
		font-family: 'Arial', sans-serif;
		font-size: 12px;
	}
	
	img,
	input,
	select,
	textarea {
		border: 1px solid #0099cc;
	}
	
	table {
		border-spacing: 2px;
		border-
	}
	
	table th, table td {
		padding: 0;
	}
	
	input[type=text]:enabled,
	select:enabled,
	textarea:enabled {
		background-color: #ffff99;
		border: 1px solid #ff0000;
	}
	
	input[type=text]:disabled,
	select:disabled,
	textarea:disabled {
		background-color: #fff !important;
		border: 1px solid #0099cc;
	}
	
	.hidden {
		display: none;
	}
	
	.text-center {
		text-align: center;
	}
	
	.text-left {
		text-align: left;
	}
	
	.text-right {
		text-align: right;
	}
	
	.vertical-top {
		vertical-align: top;
	}
	
	.text-success {
		color: green;
	}
	
	.text-warning {
		color: #ff0000;
	}
	
	.menu {
		border: 0;
		position: fixed;
		top: 15px;
		right: 15px;
	}
	
	.enabled {
		color: #ff0000;
		cursor: pointer;
	}
	
	.tab-heading {
		background-color: #09c;
		color: #fff;
		font-size: 12px;
		margin-top: 10px;
		margin-bottom: 10px;
		padding: 10px 5px;
	}
	
	.error-list {
		list-style: none;
		padding-left: 5px;
	}
	
	.error-list li {
		padding: 5px 10px;
	}
	
	.thumbnail {
      height: 140px;
      width: 120px;
  }
  
  #loanList {
  	width: 95%;
  }
</style>
<body>
	<h3 class="tab-heading">Client Verification</h3>
	<div class="main" style="padding-left: 10px;">
		<table>
			<tbody>
				<tr>
					<th class="text-left" colspan="4">
						<h3>Client Information</h3>
					</th>
				</tr>
				<tr>
					<td class="text-right">
						<label for="ciacctno">Client Code</label>
					</td>
					<td colspan="2">
						<input id="ciAcctno" type="text" name="ciacctno">
					</td>
				</tr>
				<tr>
          <td class="text-right">
            <select id="ciSource">
              <?php if( ! empty($pmt_sources)) : ?>
							<?php foreach($pmt_sources as $pmt_source) : ?>
							<option value="<?php echo $pmt_source['code']; ?>"><?php echo $pmt_source['name']; ?></option>
							<?php endforeach; ?>
							<?php endif; ?>
            </select>
          </td>
          <td>
            <input id="ciSssno" type="text" name="cisssno">
          </td>
          <td class="text-right">
              <label for="">Status</label>
              <select id="ciStatus" name="cistatus">
                  <option value="">-SELECT-</option>
                  <option value="A">Active</option>
                  <option value="I">Inactive</option>
              </select>
          </td>
          <td rowspan="8" class="text-center vertical-top">
              <img class="thumbnail" id="perImg" src="<?=base_url()?>system/application/assets/images/no_photo.jpg">
              <input class="hidden" id="ciimage" type="text" name="ciimage">
          </td>
	      </tr>
	      <tr>
          <td class="text-right">
              <label for="cifname">First Name</label>
          </td>
          <td>
              <input id="ciFname" type="text" name="cifname">
          </td>
          <td class="text-right">
              <label for="cigrp">Group</label>
              <select id="ciGrp" name="cigrp">
                  <option value="">-SELECT-</option>
                  <option value="N">New</option>
                  <option value="O">Old</option>
              </select>
          </td>
          <td></td>
	      </tr>
	      <tr>
          <td class="text-right">
              <label for="cimname">Middle Name</label>
          </td>
          <td>
              <input id="ciMname" type="text" name="cimname">
          </td>
          <td class="text-right">
              <label for="citype">Type</label>
              <select id="ciType" name="citype">
              	<?php if( ! empty($client_types)) : ?>
								<?php foreach($client_types as $client_type) : ?>
								<option value="<?php echo $client_type['code']; ?>"><?php echo $client_type['name']; ?></option>
								<?php endforeach; ?>
								<?php endif; ?>
	              </select>
          </td>
          <td></td>
	      </tr>
	      <tr>
          <td class="text-right">
            <label for="cilname">Last Name</label>
          </td>
          <td>
              <input id="ciLname" type="text" name="cilname">
          </td>
          <td class="text-right">
              <label for="cibranchcode">Branch</label>
              <select id="ciBranchcode" name="cibranchcode">
	              <option value="">-SELECT-</option>
								<?php if( ! empty($branches)) : ?>
								<?php foreach($branches as $branch) : ?>
								<option value="<?php echo $branch->Branch_Code; ?>"><?php echo $branch->Branch_Name; ?></option>
								<?php endforeach; ?>
								<?php endif; ?>
              </select>
          </td>
        </tr>
      	<!--./-->
				<tr>
					<td class="text-right">
						<label for="cibdate">Birth Date</label>
					</td>
					<td>
						<input class="text-center" id="ciBdate" type="text" name="cibdate" placeholder="YYYY-MM-DD">
					</td>
					<td>
						<input class="text-center" id="age" type="text" name="age">
					</td>
				</tr>
				<tr>
					<td class="text-right">
						<label for="cisex">Gender</label>
					</td>
					<td colspan="3">
						<select id="ciSex" name="cisex">
							<option value="">-SELECT-</option>
							<option value="M">Male</option>
							<option value="F">Female</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="text-right">
						<label for="cicivilstatus">Civil Status</label>
					</td>
					<td colspan="3">
						<select id="ciCivilstatus" name="cicivilstatus">
							<option value="">-SELECT-</option>
							<option value="S">Single</option>
							<option value="M">Married</option>
							<option value="SP">Separated</option>
							<option value="W">Widow</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="text-right">
						<label for="citelno">Tel. No</label>
					</td>
					<td colspan="3">
						<input id="ciTelno" type="text" name="citelno">
					</td>
				</tr>
				<tr>
					<td class="text-right">
						<label for="cimobileno">Mobile No</label>
					</td>
					<td colspan="3">
						<input id="ciMobileno" type="text" name="cimobileno">
					</td>
				</tr>
				<tr>
					<td class="text-right vertical-top">
						<label for="ciadd1">Present Address</label>
					</td>
					<td colspan="3">
						<textarea class="vertical-top" id="ciAdd1" name="ciadd1" rows="3"></textarea>
					</td>
				</tr>
				<tr>
					<td class="text-right vertical-top">
						<label for="ciadd2">Permanent Address</label>
					</td>
					<td colspan="3">
						<textarea id="ciAdd2" name="ciadd2" rows="3"></textarea>
					</td>
				</tr>
				<tr>
					<th class="text-left" colspan="4" style="padding-top: 10px;">
						<h3>Pension Information</h3>
					</th>
				</tr>
				<tr>
          <td class="text-right">
            <label for="cpitf">ITF</label>
          </td>
          <td>
              <input id="cpItf" type="text" name="cpitf">
          </td>
	      </tr>
	      <tr>
	          <td class="text-right">
            	<label for="cppensiontype">Pension Type</label>
	          </td>
	          <td colspan="3">
              <input id="cpPensiontype" type="text" name="cppensiontype">
              <input id="pensionType" type="text" name="pensiontype">
	          </td>
	      </tr>
	      <tr>
          <td class="text-right">
            <label for="cpbankbranch">Bank/Branch</label>
          </td>
          <td colspan="3">
            <input id="cpBankbranch" type="text" name="cpbankbranch">
          </td>
	      </tr>
	      <tr>
          <td class="text-right">
            <label for="cpbankacctno">Bank Acct. #</label>
          </td>
          <td colspan="3">
            <input id="cpBankacctno" type="text" name="cpbankacctno">
          </td>
	      </tr>
	      <tr>
          <td class="text-right">
            <label for="cpamount">Amount</label>
          </td>
          <td colspan="3">
            <input class="text-center" id="cpAmount" type="text" name="cpamount">
          </td>
	      </tr>
	      <tr>
          <td class="text-right">
          	<label for="cpwithdrawalday">Withdrawal Day</label>
          </td>
          <td colspan="3">
            <select id="cpWithdrawalday" name="cpwithdrawalday">
            	<option value="">-SELECT-</option>
            	<?php for($i=0 ; $i <= 31; $i++) : ?>
            	<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
            	<?php endfor; ?>
            </select>
          </td>
	      </tr>
	      <tr>
          <td class="text-right">
            <label for="cpptype">Type</label>
          </td>
          <td colspan="3">
            <select id="cpPtype" name="cpptype">
              <?php if( ! empty($pmt_types)) : ?>
							<?php foreach($pmt_types as $pmt_type) : ?>
							<option value="<?php echo $pmt_type['code']; ?>"><?php echo $pmt_type['name']; ?></option>
							<?php endforeach; ?>
							<?php endif; ?>
            </select>
          </td>
	      </tr>
	      <tr>
          <td></td>
	      </tr>
	      <tr>
          <td class="text-right">
            <label for="cpcauseofdeath">Cause of Death</label>
          </td>
          <td>
            <input id="cpCauseofdeath" type="text" name="cpcauseofdeath">
          </td>
	      </tr>
	      <tr>
          <td class="text-right">
            <label for="cpdateofdeath">Date of Death</label>
          </td>
          <td>
            <input class="text-center" id="cpDateofdeath" type="text" name="cpdateofdeath" placeholder="YYYY-MM-DD">
          </td>
	      </tr>
	      <tr>
          <td>
            <label for="cpdisability">Cause of Disability</label>
          </td>
          <td>
            <input id="cpDisability" type="text" name="cpdisability">
          </td>
      	</tr>
      	<tr>
					<th class="text-left" colspan="4" style="padding-top: 10px;">
						<h3>Dependent Information</h3>
					</th>
				</tr>
				<tr>
          <td class="text-right">
            <label for="cdFname">First Name</label>
          </td>
          <td colspan="3">
          	<input id="cdFname" type="text" name="cdfname">
            <input id="cdSysid" type="text"  name="cdsysid">
          </td>
	      </tr>
	      <tr>
          <td class="text-right">
            <label for="cdlaname">Last Name</label>
          </td>
          <td colspan="3">
            <input id="cdLname" type="text" name="cdlname">
          </td>
	      </tr>
	      <tr>
          <td class="text-right">
            <label for="cdrelation">Relation</label>
          </td>
          <td colspan="3">
            <input id="cdRelation" type="text" name="cdrelation">
          </td>
	      </tr>
	      <tr>
          <td class="text-right">
            <label for="cdbdate">Birth Date</label>
          </td>
          <td colspan="3">
            <input class="text-center" id="cdBdate" type="text" name="cdbdate" placeholder="YYYY-MM-DD">
          </td>
	      </tr>
	      <tr>
          <td class="text-right">
            <label for="cdprofession">Profession</label>
          </td>
          <td colspan="3">
              <input id="cdProfession" type="text" name="cdprofession">
          </td>
	      </tr>
	      <tr>
          <td class="text-right">
            <label for="cdsssno">SSS No.</label>
          </td>
          <td colspan="3">
            <input id="cdsssNo" type="text" name="cdsssno">
          </td>
	      </tr>
	      <tr>
          <td>
          	<label for="issurviving">Surviving Dependent</label>
          </td>
          <td>
            <input id="isSurviving" type="checkbox" name="issurviving" value="issd">
          </td>
	      </tr>
	      <tr>
          <td colspan="4">
            <iframe id="dependentList"></iframe>
          </td>
      	</tr>
      	
			</tbody>
		</table>
		<h3>Loan List</h3>
		<table>
			<tbody>
				<tr>
					<td>
						<input id="database" type="text" name="database">
						<input id="obcardBtn" type="button" value="OBCard">
					</td>
				</tr>
			</tbody>
		</table>
    <iframe id="loanList"></iframe>
	</div><!--./main-->
</body>

<script src="<?= base_url() ?>system/application/assets/js/jquery-2.1.3.min.js" type="text/javascript"></script>
<script type="text/javascript">
	function popupWindow(height, width, link)
  {
    var top = (screen.height/2) - (height/2);
    var left = (screen.width/2) - (width/2);
    
    return window.open(link, 'popupWindow', 'width=' + width + ', height=' + height + ', top=' + top + ',left=' + left + 'scrollbars=yes').focus();
  }
  
	$(document).ready(function() {
		$('input, select, textarea').prop('disabled', true);
		$('#obcardBtn').prop('disabled', false);
		
		$('#obcardBtn').click(function() {
      var height = 480;
      var width = 960;
      var link = 'obcard/' + $('#database').val() + '|' + $('#ciAcctno').val();
      
      popupWindow(height, width, link);
    });
	})
</script>