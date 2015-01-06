if ((strlen($array['user_name'])<=3)) {
$array['error_message'] = $this->error_messages['user_name_too_short'];
$array['error_status'] = true;	
}
  
if ((strlen($array['user_name'])>=20)) {
$array['error_message'] = $this->error_messages['user_name_too_long'];
$array['error_status'] = true;	
}
return $array;  