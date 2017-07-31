<?php

class PosTag extends Controller {

	function PosTag()
	{
		parent::Controller();
		$this->load->model('Setting_model');

		/* Check access */
		if ( ! check_access('change account settings'))
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('');
			return;
		}

		return;
	}

	function index()
	{
		$this->template->set('page_title', 'Parameter Settings');
		$this->template->set('nav_links', array('setting/postag/add' => 'Add Parameter'));

		$this->db->select('code,group,value,command,status');
		$this->db->from('parameter')->order_by('group, code');
		$data['datas'] = $this->db->get();

		$this->template->load('template', 'setting/pos/index', $data);
		return;
	}

	function add()
	{
		$this->load->helper('code');
		$this->template->set('page_title', 'Edit Parameter');
		$this->template->set('others', array(
			'jscript' => trims("
				<script>
				$(document).ready(function(){
					$('#status').change(function()
					{
						$(this).attr('value', $(this).is(':checked')?'1':'0');
					});
				});
				</script>
			")
		));

		/* Check for account lock */
		if ($this->config->item('account_locked') == 1)
		{
			$this->messages->add('Account is locked.', 'error');
			redirect('setting/postag');
			return;
		}


		/* Form fields */
		
		$data['code'] = array(
			'name' => 'code',
			'id' => 'code',
			'maxlength' => '15',
			'size' => '15',
		);

		$data['oldcode'] = array(
			'name' => 'oldcode',
			'id' => 'oldcode',
			'type' => 'hidden',
		);

		$data['group'] = array(
			'name' => 'group',
			'id' => 'group',
			'maxlength' => '100',
			'size' => '40',
		);

		$data['value'] = array(
			'name' => 'value',
			'id' => 'value',
			'cols' => '47',
			'rows' => '5',
		);

		$data['command'] = array(
			'name' => 'command',
			'id' => 'command',
			'maxlength' => '100',
			'size' => '40',
		);

		$data['status'] = array(
			'name' => 'status',
			'id' => 'status',
			'type' => 'checkbox',
		);


		/* Repopulating form */
		if ($_POST)
		{
			$data['code']['value'] = $this->input->post('code', TRUE);
			$data['group']['value'] = $this->input->post('group', TRUE);
			$data['value']['value'] = $this->input->post('value', TRUE);
			$data['command']['value'] = $this->input->post('command', TRUE);
			$data['status']['value'] = $this->input->post('status', TRUE);
		}

		/* Form validations */
		$this->form_validation->set_rules('code', 'Code', 'trim|required|min_length[2]|max_length[10]');
		$this->form_validation->set_rules('group', 'Group', 'trim|required|min_length[2]|max_length[25]');
		$this->form_validation->set_rules('value', 'Value', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('command', 'Command', 'trim|max_length[100]');
		$this->form_validation->set_rules('status', 'Status', 'trim|max_length[1]');

		/* Validating form */
		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'setting/pos/add', $data);
			return;
		}
		else
		{
			$code = $this->input->post('code', TRUE);
			$group = $this->input->post('group', TRUE);
			$value = $this->input->post('value', TRUE);
			$command = $this->input->post('command', TRUE);
			$status = $this->input->post('status', TRUE);

			$this->db->trans_start();
			$update_data = array(
				'code' => $code,
				'group' => $group,
				'value' => $value,
				'command' => $command,
				'status' => $status,
			);

			if ( ! $this->db->insert('parameter', $update_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error Creating Parameter - ' . $group . '.', 'error');
				$this->logger->write_message("error", "Error Creating Parameter called " . $group . " [id:" . $code . "]");
				$this->template->load('template', 'setting/pos/add', $data);
				return;
			} else {
				$this->db->trans_complete();
				$this->messages->add('Added Parameter - ' . $group . '.', 'success');
				$this->logger->write_message("success", "Added Parameter called " . $group . " [id:" . $code . "]");
				redirect('setting/postag');
				return;
			}
		}
		return;
	}



	function edit($id)
	{
		$this->load->helper('code');
		$this->template->set('page_title', 'Edit Parameter');
		$this->template->set('others', array(
			'jscript' => trims("
				<script>
				$(document).ready(function(){
					$('#status').change(function()
					{
						$(this).attr('value', $(this).is(':checked')?'1':'0');
					});
				});
				</script>
			")
		));

		/* Check for account lock */
		if ($this->config->item('account_locked') == 1)
		{
			$this->messages->add('Account is locked.', 'error');
			redirect('setting/postag');
			return;
		}

		/* Checking for valid data */
		$id = $this->input->xss_clean($id);

		/* Loading current Parameter */
		$this->db->from('parameter')->where('code', $id);
		$param_q = $this->db->get();
		if ($param_q->num_rows() < 1)
		{
			$this->messages->add('Invalid Parameter.', 'error');
			redirect('setting/postag');
			return;
		}
		$param = $param_q->row();

		/* Form fields */
		
		$data['code'] = array(
			'name' => 'code',
			'id' => 'code',
			'maxlength' => '15',
			'size' => '15',
			'value' => $param->code,
		);

		$data['oldcode'] = array(
			'name' => 'oldcode',
			'id' => 'oldcode',
			'type' => 'hidden',
			'value' => $param->code,
		);

		$data['group'] = array(
			'name' => 'group',
			'id' => 'group',
			'maxlength' => '100',
			'size' => '40',
			'value' => $param->group,
		);

		$data['value'] = array(
			'name' => 'value',
			'id' => 'value',
			'cols' => '47',
			'rows' => '5',
			'value' => $param->value,
		);

		$data['command'] = array(
			'name' => 'command',
			'id' => 'command',
			'maxlength' => '100',
			'size' => '40',
			'value' => $param->command,
		);

		$data['status'] = array(
			'name' => 'status',
			'id' => 'status',
			'type' => 'checkbox',
			'value' => $param->status,
		);
		if($param->status) $data['status']['checked'] = '';

		$data['code_id'] = $param->code;

		/* Repopulating form */
		if ($_POST)
		{
			$data['code']['value'] = $this->input->post('code', TRUE);
			$data['group']['value'] = $this->input->post('group', TRUE);
			$data['value']['value'] = $this->input->post('value', TRUE);
			$data['command']['value'] = $this->input->post('command', TRUE);
			$data['status']['value'] = $this->input->post('status', TRUE);
		}

		/* Form validations */
		$this->form_validation->set_rules('code', 'Code', 'trim|required|min_length[2]|max_length[10]');
		$this->form_validation->set_rules('oldcode', 'OldCode', 'trim|required');
		$this->form_validation->set_rules('group', 'Group', 'trim|required|min_length[2]|max_length[25]');
		$this->form_validation->set_rules('value', 'Value', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('command', 'Command', 'trim|max_length[100]');
		$this->form_validation->set_rules('status', 'Status', 'trim|max_length[1]');

		/* Validating form */
		if ($this->form_validation->run() == FALSE)
		{
			$this->messages->add(validation_errors(), 'error');
			$this->template->load('template', 'setting/pos/edit', $data);
			return;
		}
		else
		{
			$oldcode = $this->input->post('oldcode', TRUE);
			$code = $this->input->post('code', TRUE);
			$group = $this->input->post('group', TRUE);
			$value = $this->input->post('value', TRUE);
			$command = $this->input->post('command', TRUE);
			$status = $this->input->post('status', TRUE);

			$this->db->trans_start();
			$update_data = array(
				'code' => $code,
				'group' => $group,
				'value' => $value,
				'command' => $command,
				'status' => $status,
			);

			if ( ! $this->db->where('code', $oldcode)->update('parameter', $update_data))
			{
				$this->db->trans_rollback();
				$this->messages->add('Error Updating Parameter - ' . $group . '.', 'error');
				$this->logger->write_message("error", "Error updating Parameter called " . $group . " [id:" . $code . "]");
				$this->template->load('template', 'setting/pos/edit', $data);
				return;
			} else {
				$this->db->trans_complete();
				$this->messages->add('Updated Parameter - ' . $group . '.', 'success');
				$this->logger->write_message("success", "Updated Parameter called " . $group . " [id:" . $code . "]");
				redirect('setting/postag');
				return;
			}
		}
		return;
	}



	function delete($id)
	{
		/* Check for account lock */
		if ($this->config->item('account_locked') == 1)
		{
			$this->messages->add('Account is locked.', 'error');
			redirect('setting/postag');
			return;
		}

		/* Checking for valid data */
		$id = $this->input->xss_clean($id);

		/* Loading current Parameter */
		$this->db->from('parameter')->where('code', $id);
		$param_q = $this->db->get();
		if ($param_q->num_rows() < 1)
		{
			$this->messages->add('Invalid Parameter.', 'error');
			redirect('setting/postag');
			return;
		} else {
			$param = $param_q->row();
		}


		/* Deleting Parameters */
		$this->db->trans_start();
		if ( ! $this->db->delete('parameter', array('code' => $id)))
		{
			$this->db->trans_rollback();
			$this->messages->add('Error deleting Parameter - ' . $param->group . '.', 'error');
			$this->logger->write_message("error", "Error deleting Parameter called " . $param->group . " [id:" . $id . "]");
			redirect('setting/postag');
			return;
		} else {
			$this->db->trans_complete();
			$this->messages->add('Deleted Parameter - ' . $param->group . '.', 'success');
			$this->logger->write_message("success", "Deleted Parameter called " . $param->group . " [id:" . $id . "]");
			redirect('setting/postag');
			return;
		}
		return;
	}
}

/* End of file postag.php */
/* Location: ./system/application/controllers/setting/entrytypes.php */
