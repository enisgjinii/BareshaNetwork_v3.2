document.getElementById("customer_id").addEventListener("change", function () {
  var selectedOption = this.options[this.selectedIndex];
  var percentage = selectedOption.getAttribute("data-percentage");
  document.getElementById("percentage").value = percentage;
  var totalAmount = parseFloat(document.getElementById("total_amount").value);
  var totalAmountAfterPercentage =
    totalAmount - totalAmount * (percentage / 100);
  document.getElementById("total_amount_after_percentage").value =
    totalAmountAfterPercentage.toFixed(2);
});
document.getElementById("total_amount").addEventListener("input", function () {
  var totalAmount = parseFloat(this.value);
  var percentage = parseFloat(document.getElementById("percentage").value);
  var totalAmountAfterPercentage =
    totalAmount - totalAmount * (percentage / 100);
  document.getElementById("total_amount_after_percentage").value =
    totalAmountAfterPercentage.toFixed(2);
});
