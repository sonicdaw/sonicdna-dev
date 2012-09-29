<?php use_helper('Javascript') ?>
<script id="joinBandListTemplate" type="text/x-jquery-tmpl">
  <div class="span3">
    <div class="row_memberimg row"><div class="span3 center"><a href="${band_url}"><img src="${band_image_url}" class="rad10" width="57" height="57"></a></div></div>
    <div class="row_membername font10 row"><div class="span3 center"><a href="${band_url}">${name}</a> (${member_count})</div></div>
  </div>
</script>
<script type="text/javascript">
$(function(){
  $.getJSON( openpne.apiBase + 'band/search.json?apiKey=' + openpne.apiKey, function(json) {
    if (json.data.length > 0) {
      $('#joinBandListTemplate').tmpl(json.data).appendTo('#memberJoinBandList');
      $('#memberJoinBandList').show();
      $('#joinBandSearch').removeAttr('disabled');
    } else {
      $('#memberJoinBandNotExist').show();
    }
    $('#memberJoinBandListLoading').hide();
  });
  $('#joinBandSearch').keypress(function(){
    $('#memberJoinBandListLoading').show();
    $('#memberJoinBandList, #memberJoinBandNotMatch').hide();
    $('#memberJoinBandList').empty();
  });
  $('#joinBandSearch').blur(function(){
    var keyword = $('#joinBandSearch').val();
    var requestData = { keyword: keyword, apiKey: openpne.apiKey };
    $.getJSON( openpne.apiBase + 'band/search.json', requestData, function(json) {
      if (json.data.length > 0) {
        $result = $('#joinBandListTemplate').tmpl(json.data);
        $('#memberJoinBandList').html($result);
        $('#memberJoinBandList').show();
      } else {
        $('#memberJoinBandNotMatch').show();
      }
      $('#memberJoinBandListLoading').hide();
    });
  });
});
</script>

<hr class="toumei" />
<div class="row">
  <div class="gadget_header span12"><?php echo __('Search %band%', array('%band%' => $op_term['band']->titleize()->pluralize())) ?></div>
</div>
<hr class="toumei" />
<div class="row" id="joinBandSearchBox">
<div class="input-prepend span12">
<span class="add-on"><i class="icon-search"></i></span>
<input type="text" id="joinBandSearch" class="realtime-searchbox" value="" disabled="disabled" />
</div>
</div>
<div class="row hide" id="memberJoinBandList">
</div>
<div class="row hide" id="memberJoinBandNotMatch">
<?php echo __('Your search queries did not match any %band%.') ?>
</div>
<div class="row hide" id="memberJoinBandNotExist">
<?php echo __('%Band% does not exist.') ?>
</div>
<div class="row" id="memberJoinBandListLoading" style="margin-left: 0; text-align: center;">
<?php echo op_image_tag('ajax-loader.gif') ?>
</div>
