<?php use_helper('Javascript') ?>
<script id="bandMemberJoinListTemplate" type="text/x-jquery-tmpl">
  <div class="span3">
    <div class="row_memberimg row"><div class="span3 center"><a href="${profile_url}"><img src="${profile_image}" class="rad10" width="57" height="57"></a></div></div>
    <div class="row_membername font10 row"><div class="span3 center"><a href="${profile_url}">${name}</a> (${friends_count})</div></div>
  </div>
</script>
<script type="text/javascript">
$(function(){
  $.getJSON( openpne.apiBase + 'member/search.json?target=band&target_id=<?php echo $band->getId() ?>&apiKey=' + openpne.apiKey, function(json) {
    $('#bandMemberJoinListTemplate').tmpl(json.data).appendTo('#bandMemberJoinList');
    $('#bandMemberJoinList').show();
    $('#bandMemberJoinListLoading').hide();
  });
  $('#bandMemberJoinListSearch').keypress(function(){
    $('#bandMemberJoinListLoading').show();
    $('#bandMemberJoinList').hide();
    $('#bandMemberJoinList').empty();
  });
  $('#bandMemberJoinListSearch').blur(function(){
    var keyword = $('#bandMemberJoinListSearch').val();
    var requestData = { target: 'band', target_id: <?php echo $band->getId(); ?>, keyword: keyword, apiKey: openpne.apiKey };
    $.getJSON( openpne.apiBase + 'member/search.json', requestData, function(json) {
      $result = $('#bandMemberJoinListTemplate').tmpl(json.data);
      $('#bandMemberJoinList').html($result);
      $('#bandMemberJoinList').show();
      $('#bandMemberJoinListLoading').hide();
    });
  });
});
</script>

<hr class="toumei" />
<div class="row">
  <div class="gadget_header span12"><?php echo __('%band% Members', array('%band%' => $op_term['band'])) ?></div>
</div>
<hr class="toumei" />
<div class="row" id="bandMemberJoinListSearchBox">
<div class="input-prepend span12">
<span class="add-on"><i class="icon-search"></i></span>
<input type="text" id="bandMemberJoinListSearch" class="realtime-searchbox" value="" />
</div>
</div>
<div class="row hide" id="bandMemberJoinList">
</div>
<div class="row center" id="bandMemberJoinListLoading" style="margin-left: 0;">
<?php echo op_image_tag('ajax-loader.gif') ?>
</div>
