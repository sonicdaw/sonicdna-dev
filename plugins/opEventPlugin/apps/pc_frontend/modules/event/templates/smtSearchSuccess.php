<?php use_helper('Javascript') ?>
<script id="joinEventListTemplate" type="text/x-jquery-tmpl">
  <div class="span3">
    <div class="row_memberimg row"><div class="span3 center"><a href="${event_url}"><img src="${event_image_url}" class="rad10" width="57" height="57"></a></div></div>
    <div class="row_membername font10 row"><div class="span3 center"><a href="${event_url}">${name}</a> (${member_count})</div></div>
  </div>
</script>
<script type="text/javascript">
$(function(){
  $.getJSON( openpne.apiBase + 'event/search.json?apiKey=' + openpne.apiKey, function(json) {
    if (json.data.length > 0) {
      $('#joinEventListTemplate').tmpl(json.data).appendTo('#memberJoinEventList');
      $('#memberJoinEventList').show();
      $('#joinEventSearch').removeAttr('disabled');
    } else {
      $('#memberJoinEventNotExist').show();
    }
    $('#memberJoinEventListLoading').hide();
  });
  $('#joinEventSearch').keypress(function(){
    $('#memberJoinEventListLoading').show();
    $('#memberJoinEventList, #memberJoinEventNotMatch').hide();
    $('#memberJoinEventList').empty();
  });
  $('#joinEventSearch').blur(function(){
    var keyword = $('#joinEventSearch').val();
    var requestData = { keyword: keyword, apiKey: openpne.apiKey };
    $.getJSON( openpne.apiBase + 'event/search.json', requestData, function(json) {
      if (json.data.length > 0) {
        $result = $('#joinEventListTemplate').tmpl(json.data);
        $('#memberJoinEventList').html($result);
        $('#memberJoinEventList').show();
      } else {
        $('#memberJoinEventNotMatch').show();
      }
      $('#memberJoinEventListLoading').hide();
    });
  });
});
</script>

<hr class="toumei" />
<div class="row">
  <div class="gadget_header span12"><?php echo __('Search %event%', array('%event%' => $op_term['event']->titleize()->pluralize())) ?></div>
</div>
<hr class="toumei" />
<div class="row" id="joinEventSearchBox">
<div class="input-prepend span12">
<span class="add-on"><i class="icon-search"></i></span>
<input type="text" id="joinEventSearch" class="realtime-searchbox" value="" disabled="disabled" />
</div>
</div>
<div class="row hide" id="memberJoinEventList">
</div>
<div class="row hide" id="memberJoinEventNotMatch">
<?php echo __('Your search queries did not match any %event%.') ?>
</div>
<div class="row hide" id="memberJoinEventNotExist">
<?php echo __('%Event% does not exist.') ?>
</div>
<div class="row" id="memberJoinEventListLoading" style="margin-left: 0; text-align: center;">
<?php echo op_image_tag('ajax-loader.gif') ?>
</div>
