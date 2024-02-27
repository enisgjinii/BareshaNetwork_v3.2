$(document).ready(function () {
  $(".krijo-fature-btn").on("click", function () {
    var channelId = $(this).data("channel-id");
    var youtubeAnalyticsValue = $(this).data("revenue");
    $("#channel_display").text(channelId);
    $("#customer_id option").each(function () {
      var option = $(this);
      var optionChannelId = option.data("youtube");
      if (optionChannelId === channelId) {
        option.show();
      } else {
        option.hide();
      }
    });
    $("#total_amount").val(youtubeAnalyticsValue);
    var visibleOptions = $("#customer_id option:visible");
    if (visibleOptions.length > 0) {
      visibleOptions.first().prop("selected", true);
    }
  });
});
