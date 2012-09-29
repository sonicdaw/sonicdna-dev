<?php

$data = array();

foreach ($bands as $band)
{
  $data[] = op_api_band($band);
}

return array(
  'status' => 'success',
  'data' => $data,
);
