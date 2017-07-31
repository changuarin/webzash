<?
date_default_timezone_set('Asia/Manila');

$ref = $_POST;

$this->db->trans_start();

// ASSIGN OR/PR
$this->db->set('orprtype', $ref['b']);
$this->db->set('orprno', $ref['c']);
$this->db->where('uid', $ref['a']);
$this->db->update('collection_entry');

// UPDATE O.R. PARAMETER
if($ref['b']=='OR'):
	$this->db->set('value', $ref['c']+1);
	$this->db->where('group', 'COLLECTION');
	$this->db->where('code', 'ORNO');
	$this->db->update('parameter');
	$orno=$ref['c']+1;
else:
	$orno=$ref['c'];
endif;
// P.R will be increment once the collection POSTED in financial module.

$this->db->trans_complete();

echo json_encode(array(
	'a'=>TRUE,
	'b'=>$_POST['d'],
	'c'=>$orno
));

?>