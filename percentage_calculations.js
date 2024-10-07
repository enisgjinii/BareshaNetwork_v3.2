// Funksion për përditësimin e shumës pas përqindjes
const updateTotalAmountAfterPercentage = (totalAmount, percentage) => {
  // Kontrollon nëse totalAmount dhe percentage janë numra të vlefshëm
  if (isNaN(totalAmount) || isNaN(percentage)) {
    console.warn("TotalAmount ose Percentage nuk është një numër i vlefshëm.");
    return;
  }

  // Llogarit shumën pas zbritjes së përqindjes
  const totalAfterPercentage = totalAmount - (totalAmount * (percentage / 100));
  
  // Kthen vlerën e përpunuar me dy shifra decimale
  return totalAfterPercentage.toFixed(2);
};

// Funksion për trajtimin e ndryshimit të klientit
const handleCustomerChange = (event) => {
  const selectedOption = event.target.options[event.target.selectedIndex];
  const percentage = parseFloat(selectedOption.getAttribute("data-percentage")) || 0;

  // Përditëson fushën e përqindjes
  percentageInput.value = percentage;

  // Përditëson shumën pas përqindjes
  const totalAmount = parseFloat(totalAmountInput.value) || 0;
  const updatedTotal = updateTotalAmountAfterPercentage(totalAmount, percentage);
  if (updatedTotal !== undefined) {
    totalAfterPercentageInput.value = updatedTotal;
  }
};

// Funksion për trajtimin e ndryshimit të shumës totale
const handleTotalAmountInput = (event) => {
  const totalAmount = parseFloat(event.target.value) || 0;
  const percentage = parseFloat(percentageInput.value) || 0;

  // Përditëson shumën pas përqindjes
  const updatedTotal = updateTotalAmountAfterPercentage(totalAmount, percentage);
  if (updatedTotal !== undefined) {
    totalAfterPercentageInput.value = updatedTotal;
  }
};

// Cacheimi i elementëve të DOM-it për performancë më të mirë
const customerSelect = document.getElementById("customer_id");
const percentageInput = document.getElementById("percentage");
const totalAmountInput = document.getElementById("total_amount");
const totalAfterPercentageInput = document.getElementById("total_amount_after_percentage");

// Kontrollon nëse të gjithë elementët e nevojshëm ekzistojnë
if (customerSelect && percentageInput && totalAmountInput && totalAfterPercentageInput) {
  
  // Shton event listener për ndryshimin e klientit
  customerSelect.addEventListener("change", handleCustomerChange);
  
  // Shton event listener për inputin në shumën totale
  totalAmountInput.addEventListener("input", handleTotalAmountInput);
  
} else {
  // Njofton nëse disa elementë nuk u gjetën në DOM
  console.error("Një ose më shumë elementë nuk u gjetën në DOM. Kontrolloni ID-të e elementëve.");
}
