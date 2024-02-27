const tabs = document.querySelectorAll('.nav-link[data-bs-toggle="pill"]');
const tabContent = document.querySelectorAll(".tab-pane");
const activeTab = localStorage.getItem("activeTab");
if (activeTab) {
  tabs.forEach((tab) => {
    if (tab.getAttribute("id") === activeTab) {
      tab.classList.add("active");
      tab.setAttribute("aria-selected", "true");
    } else {
      tab.classList.remove("active");
      tab.setAttribute("aria-selected", "false");
    }
  });
  tabContent.forEach((content) => {
    if (content.getAttribute("id") === activeTab.replace("-tab", "")) {
      content.classList.add("show", "active");
    } else {
      content.classList.remove("show", "active");
    }
  });
}
tabs.forEach((tab) => {
  tab.addEventListener("click", () => {
    const tabId = tab.getAttribute("id");
    localStorage.setItem("activeTab", tabId);
  });
});
$(document).ready(function () {
  var myModal = new bootstrap.Modal(document.getElementById("newInvoice"));
  myModal.addEventListener("show.bs.modal", function (event) {
    var button = event.relatedTarget;
    var channelId = button.getAttribute("data-channel-id");
    var youtubeAnalyticsValue = "Replace with actual value";
    $("#channel_display").text(channelId);
    $("#customer_id option:eq(2)").prop("selected", true);
    $("#customer_id option").each(function () {
      var option = $(this);
      var optionChannelId = option.data("youtube");
      if (optionChannelId === channelId) {
        option.prop("selected", true);
      }
    });
    $("#total_amount").val(youtubeAnalyticsValue);
  });
  document
    .querySelector(".krijo-fature-btn")
    .addEventListener("click", function () {
      myModal.show();
    });
});
