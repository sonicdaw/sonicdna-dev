<?php

$data = array();

foreach ($events as $event)
{
  $data[] = op_api_event($event);
}

return array(
  'status' => 'success',
  'data' => $data,
);
