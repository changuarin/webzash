<?php
class Test extends Controller
{	
	public function index()
    {
    	$result = array(
    		'user'=>'test',
    		'pw'=>'testing only'
    	);

        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: http://localhost');
        echo json_encode($result);

    }
}